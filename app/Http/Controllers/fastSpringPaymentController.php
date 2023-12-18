<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  fastSpringPaymentController.php

File Created    :  Monday, 12th July 2021 05:22:26 pm
Author          :  Optimumbrew
Auther Email    :  info@optimumbrew.com
Last Modified   :  Monday, 25th July 2021 05:22:26 pm
-----
Purpose          :  This file has been processed fast-spring payment gateway.
-----
Copyright 2018 - 2021 Optimumbrew Technology

*/

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use App\Jobs\EmailJob;
use Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use File;
use Illuminate\Support\Facades\Cache;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class fastSpringPaymentController extends Controller
{
    /* =================================| FastSpring API in PAK |=============================*/
    /*
    Purpose : To change user role & perform db_process
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here).
    Return : return user detail if success otherwise error with specific status code
    */
    public function setFastSpringPaymentMethod(Request $request_body){

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $user_uuid = $user_detail->uuid;
            $client = new Client();

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('order_id', 'product_id', 'total_amount', 'tax_amount'), $request)) != '')
                return $response;

            $order_id = $request->order_id;
            $product_id = $request->product_id;
            $subscription_id = isset($request->subscription_id) ? $request->subscription_id : "";
            $total_amount = $request->total_amount;
            $tax_amount = $request->tax_amount;
            $create_time = date('Y-m-d H:i:s');

            //check this order_id is already exist in fast-spring. If not then print log & return response
            $order_url = Config::get('constant.FASTSPRING_API_URL') . Config::get('constant.FASTSPRING_ORDERS_API_NAME') . $order_id;
            $username = Config::get('constant.FASTSPRING_API_USER_NAME');
            $password = Config::get('constant.FASTSPRING_API_PASSWORD');

            try {
                $response = $client->get($order_url, ['auth' => [$username, $password]]);
                $api_response = json_decode($response->getBody()->getContents());

            }catch (RequestException  $e){
                $error_msg = json_decode($e->getResponse()->getBody()->getContents());
                Log::error("setFastSpringPaymentMethod : unable to get order detail. ", ["Exception" => $error_msg]);
                if(count($error_msg->orders) > 1){
                    Log::error("setFastSpringPaymentMethod : multiple order details arrives in exception : ",[$error_msg]);
                }
                return Response::json(array('code' => 201, 'message' => $error_msg->orders[0]->error->order, 'cause' => '', 'data' => json_decode("{}")));
            }

            //get total days according to it's plan.
            $response = $this->getDaysAndTypeOfPayment($product_id);
            $add_days = $response['add_days'];
            $add_month = $response['add_month'];
            $subscr_type = $response['subscr_type'];

            //calculate expire time or next billing date based on this days.
            if(!$add_month){
                $final_expiration_time = $next_billing_date = (new VerificationController())->addDaysIntoDate($create_time, $add_days);
            }else{
                $final_expiration_time = $next_billing_date = date('Y-m-d H:i:s', strtotime($create_time . "+$add_month months"));
            }

            //get user subscription by user id
            $subscription_detail = DB::select('SELECT
                                                    user_id,
                                                    transaction_id AS order_id,
                                                    paypal_id AS subscription_id,
                                                    subscr_type,
                                                    total_amount,
                                                    COALESCE(tax_amount,0) AS tax_amount,
                                                    cancellation_date,
                                                    final_expiration_time,
                                                    is_active
                                                FROM
                                                    subscriptions
                                                WHERE
                                                    user_id = ? AND
                                                    final_expiration_time >= ?
                                                ORDER BY id DESC', [$user_id, $create_time]);

            //check user subscription is exist if not then insert into subscription table
            if (count($subscription_detail) <= 0) {
                Log::info('setFastSpringPaymentMethod : Subscriptions does not exist (New subscription).', ["user_id" => $user_id, "subscription_id" => $subscription_id]);

                //check user subscription is active if active then print log & return response
            } elseif ($subscription_detail[0]->is_active == 1 || $subscription_detail[0]->cancellation_date == NULL) {
                Log::info('setFastSpringPaymentMethod : Subscriptions is already active', ["db_subscription_id" => $subscription_detail[0]->subscription_id, "subscription_id" => $subscription_id]);
                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                return Response::json(array('code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));

            } else {
                //in this case means : user purchased plan previously, then canceled it, then purchase a new plan before previous plan expires.
                //so we want to add on it's expire time. So user can use PAK feature till below date.
                //calculate final expire time date based on above days.
                $subscription_final_expiration_time = $subscription_detail[0]->final_expiration_time;
                if(!$add_month){
                    $final_expiration_time = (new VerificationController())->addDaysIntoDate($subscription_final_expiration_time, $add_days);
                }else{
                    $final_expiration_time = date('Y-m-d H:i:s', strtotime($subscription_final_expiration_time . "+$add_month months"));
                }
                Log::info('setFastSpringPaymentMethod : Subscriptions already exist. carry-on expire time.', ["updated_fep" => $final_expiration_time, "db_fep" => $subscription_final_expiration_time]);
            }

            $payment_mode = "FASTSPRING";
            $product_details = DB::select('SELECT id AS product_id,
                                                    name AS product_name,
                                                    discount_percentage
                                                    FROM subscription_product_details
                                                    WHERE is_applied = 1');

            if (count($product_details) > 0) {
                $product_id = $product_details[0]->product_id;
            } else {
                $product_id = 2;
            }

            //Insert payment details in subscriptions & payment_status_master table
            DB::beginTransaction();
            DB::insert('INSERT IGNORE INTO subscriptions
                            (user_id,
                             product_id,
                             order_id,
                             transaction_id,
                             paypal_id,
                             payment_mode,
                             subscr_type,
                             txn_type,
                             payment_status,
                             total_amount,
                             tax_amount,
                             paypal_response,
                             payment_date,
                             activation_time,
                             expiration_time,
                             final_expiration_time,
                             payment_type,
                             create_time)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                [$user_id,
                    $product_id,
                    $order_id,
                    $order_id,
                    $subscription_id,
                    $payment_mode,
                    $subscr_type,
                    "",
                    "Active",
                    $total_amount,
                    $tax_amount,
                    NULL,
                    $create_time,
                    $create_time,
                    $next_billing_date,
                    $final_expiration_time,
                    Config::get('constant.PAYMENT_TYPE_OF_FASTSPRING'),
                    $create_time
                ]);

            $is_exist = DB::select('SELECT 1 FROM payment_status_master WHERE txn_id=?', [$order_id]);
            if (!$is_exist) {
                DB::insert('INSERT INTO payment_status_master
                                            (user_id, txn_id, paypal_status, paypal_payment_status, ipn_status, verify_status, expiration_time, is_active, create_time)
                                        VALUES (?,?,?,?,?,?,?,?,?)',
                    [$user_id, $order_id, 1, "Active", 1, 1, $next_billing_date, 1, $create_time]);
            }

            $design_detail = DB::select('SELECT id, content_type FROM my_design_master WHERE user_id = ? AND is_active = 0 ORDER BY update_time DESC', [$user_id]);
            if(count($design_detail) > 0) {
                DB::update('UPDATE my_design_master SET is_active = 1 WHERE user_id = ? AND is_active = 0 LIMIT 1', [$user_id]);
                (new UserController())->increaseMyDesignCount($user_id, $create_time, $design_detail[0]->content_type);
                (new UserController())->deleteMultipleRedisKeys(["getMyDesignFolder$user_id", "getDesignFolderForAdmin$user_id", "getMyVideoDesignFolder$user_id", "getVideoDesignFolderForAdmin$user_id", "getMyIntroDesignFolder$user_id", "getIntroDesignFolderForAdmin$user_id"]);
            }
            DB::commit();

            //Change user role according to it's plan.
            (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

            (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_uuid");
            $response = Response::json(array('code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));

        } catch (Exception $e) {
            (new ImageController())->logs("setFastSpringPaymentMethod",$e);
            //Log::error("setFastSpringPaymentMethod : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set payment method.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*
    Purpose : To cancel user subscription & de-active it in our db.
    Description : This method not take any argument as parameter.
    Return : return user detail if success otherwise error with specific status code
    */
    public function cancelFastSpringSubscription(Request $request_body){

        try{

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $user_uuid = $user_detail->uuid;
            $client = new Client();
            $payment_type = Config::get('constant.PAYMENT_TYPE_OF_FASTSPRING');

            //get subscription detail by user_id
            $subscription_detail = DB::select('SELECT
                                                      id,
                                                      user_id,
                                                      transaction_id AS order_id,
                                                      paypal_id AS subscription_id,
                                                      is_active,
                                                      cancellation_date
                                                FROM subscriptions
                                                  WHERE
                                                      user_id=? AND
                                                      payment_type=?
                                                  ORDER BY id DESC', [$user_id, $payment_type]);

            if(count($subscription_detail) <= 0){
                Log::error('cancelFastSpringSubscription : Subscriptions does not exist',["user_id"=>$user_id]);
                return Response::json(array('code' => 201, 'message' => 'Subscriptions does not exist.', 'cause' => '', 'data' => json_decode("{}")));

            //check if subscription is already cancel or not in our db, if yes then print log
            }elseif ($subscription_detail[0]->is_active == 0 || $subscription_detail[0]->cancellation_date != NULL){
                Log::error('cancelFastSpringSubscription : Subscriptions already cancel.',["user_id"=>$user_id]);
            }

            //check if subscription is already cancel or not in fastspring, if yes then print log & return error message
            $subscription_id = $subscription_detail[0]->subscription_id;
            $cancel_subscription_url = Config::get('constant.FASTSPRING_API_URL') . Config::get('constant.FASTSPRING_SUBSCRIPTIONS_API_NAME') . $subscription_id;
            $username = Config::get('constant.FASTSPRING_API_USER_NAME');
            $password = Config::get('constant.FASTSPRING_API_PASSWORD');

            try {
                $response = $client->delete($cancel_subscription_url, ['auth' => [$username, $password]]);
                $api_response = json_decode($response->getBody()->getContents());

            }catch (RequestException  $e){
                $error_msg = json_decode($e->getResponse()->getBody()->getContents());
                Log::error("cancelFastSpringSubscription : unable to cancel ", ["Exception" => $error_msg]);
                if(isset($error_msg->subscriptions) && count($error_msg->subscriptions) > 1){
                    Log::error("cancelFastSpringSubscription : multiple subscription arrives in exception : ",[$error_msg]);
                }
                $message = isset($error_msg->subscriptions[0]->error->subscription) ? $error_msg->subscriptions[0]->error->subscription : Config::get('constant.EXCEPTION_ERROR') . 'cancel fast spring subscription.';
                return Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();
            //run db query to cancel user's subscription in our db.
            $query = DB::update('UPDATE subscriptions AS sub
                                    LEFT JOIN payment_status_master AS psm ON psm.txn_id=sub.transaction_id
                                SET
                                    sub.is_active = 0,
                                    psm.is_active = 0,
                                    sub.cancellation_date = CURRENT_TIMESTAMP,
                                    sub.response_message = "Subscription Cancelled",
                                    sub.payment_status = "Cancelled"
                                WHERE sub.paypal_id = ?',[$subscription_id]);
            DB::commit();

            if(!$query){
                Log::error('cancelFastSpringSubscription : Subscriptions not updated.', ["user_id" => $user_id, "subscription_id" => $subscription_id]);
            }

            //get users detail & return response
            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
            $result = array('user_detail' => $user_detail);

            (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_uuid");
            $response = Response::json(array('code' => 200, 'message' => 'Subscription cancelled successfully.', 'cause' => '', 'data' => $result));

        }catch (Exception $e){
            (new ImageController())->logs("cancelFastSpringSubscription",$e);
            //Log::error("cancelFastSpringSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'cancel fast spring subscription.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : To get user authenticate URL
    Description : This method has not take any argument as parameter.
    Return : return user order url if success otherwise error with specific status code
    */
    public function getFastSpringUserOrdersURL(Request $request_body){

        try{

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_id = JWTAuth::toUser($token)->id;
            $user_uuid = JWTAuth::toUser($token)->uuid;
            $client = new Client();

            //get user fs-account id from our DB. Use this id & make request to fast-spring for user auth URL.
            $subscription_detail = DB::select('SELECT
                                                      paypal_response
                                                FROM paypalipn_response
                                                WHERE
                                                      response_from = 2 AND
                                                      paypal_response LIKE "%'.$user_uuid.'%" AND
                                                      paypal_response LIKE "%order.completed%"
                                                ORDER BY update_time DESC LIMIT 1');

            if(count($subscription_detail) <= 0){
                Log::error('getFastSpringUserAuthenticateURL : Subscriptions does not exist',["user_id"=>$user_id]);
                return Response::json(array('code' => 201, 'message' => 'Subscriptions does not exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

            $user_order_detail = json_decode($subscription_detail[0]->paypal_response);
            $account_id = $user_order_detail->data->account->id;

            $get_user_auth_url = Config::get('constant.FASTSPRING_API_URL') . Config::get('constant.FASTSPRING_ACCOUNTS_API_NAME') . $account_id . "/authenticate";
            $username = Config::get('constant.FASTSPRING_API_USER_NAME');
            $password = Config::get('constant.FASTSPRING_API_PASSWORD');

            $response = $client->get($get_user_auth_url, ['auth' => [$username, $password]]);
            $api_response = json_decode($response->getBody()->getContents());
            $result = $api_response->accounts[0]->url;

            $response = Response::json(array('code' => 200, 'message' => 'User order url fetch successfully.', 'cause' => '', 'data' => $result));

        }catch (Exception $e){
            (new ImageController())->logs("getFastSpringUserOrdersURL",$e);
            //Log::error("getFastSpringUserOrdersURL : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get order url.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| FastSpring Webhook Events |=============================*/
    /*
    Purpose : To send an email to users and let you know that its payment has under process & subscription is active
    Description : This method has not take any argument as parameter.
    Return : return send an email to users if success otherwise error with specific status code
    */
    public function subscriptionActivatedEvent(Request $request_body){

        try{

            //check if request is coming from a fast-spring, if not then print error log & return error message.
            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('subscriptionActivatedEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if($i >= 1){
                    Log::info("subscriptionActivatedEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);
                $create_time = date('Y-m-d H:i:s');
                $country_code = $events->data->account->country;
                $product_id = $events->data->product->product;

                //get total days according to it's plan.
                $response = $this->getDaysAndTypeOfPayment($product_id);
                $add_days = $response['add_days'];
                $add_month = $response['add_month'];
                $subscr_type = $response['subscr_type'];

                if(!$add_month){
                    $next_billing_date = (new VerificationController())->addDaysIntoDate($create_time, $add_days);
                }else{
                    $next_billing_date = date('Y-m-d H:i:s', strtotime($create_time . "+$add_month months"));
                }

                $payment_date_local = (New ImageController())->convertUTCDateTimeInToLocal($create_time, $country_code);
                $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($next_billing_date, $country_code);

                //Check if user purchased a plan previously. Change subject of email according to that.
                $is_exist = DB::select('SELECT
                                              paypal_response
                                        FROM paypalipn_response
                                        WHERE
                                              response_from = 2 AND
                                              paypal_response LIKE "%'.$events->data->tags->user_id.'%" AND
                                              paypal_response LIKE "%subscription.canceled%"
                                        ORDER BY update_time DESC LIMIT 1');

                if(count($is_exist) > 0){
                    $subject = 'PhotoADKing: Subscription Plan Changed';
                }else{
                    $subject = 'PhotoADKing: Subscription Plan Activated';
                }

                $template = 'payment_successful';
                $message_body = array(
                    'message' => 'Thank you for purchasing subscription for the ' . $events->data->display . '.',
                    'subscription_name' => $events->data->display,
                    'txn_id' => 'N/A',
                    'txn_type' => 'Subscription[F]',
                    'subscr_id' => $events->data->subscription,
                    'first_name' => $events->data->tags->first_name,
                    'payment_received_from' => $events->data->account->contact->first . ' (' . $events->data->account->contact->email . ')',
                    'total_amount' => $events->data->subtotal,
                    'mc_currency' => $events->data->currency,
                    'payer_email' => $events->data->tags->user_email,
                    'payment_status' => $events->type,
                    'activation_date' => $create_time,
                    'next_billing_date' => $next_billing_date,
                    'activation_date_local' => $payment_date_local,
                    'next_billing_date_local' => $next_billing_date_local
                );
                $api_name = 'subscriptionActivatedEvent';
                $api_description = 'subscribe a new subscription.';

                //send an email to users and let you know that its payment has under process
                $this->dispatch(new EmailJob($events->data->tags->user_id, $events->data->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));
            }

            $response = Response::json(array('code' => 200, 'message' => 'Subscription activated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionActivatedEvent",$e);
            //Log::error("subscriptionActivatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription activated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : payment DB processing and to send an email to users and let you know that its payment has been success
    Description : This method has not take any argument as parameter.
    Return : return send an email to users if success otherwise error with specific status code
    */
    public function orderCompletedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('orderCompletedEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if($i >= 1){
                    Log::error("orderCompletedEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);
                $user_uuid = $events->data->tags->user_id;
                $create_time = date('Y-m-d H:i:s');

                $user_profile = (new LoginController())->getUserDetailsByUserId($user_uuid);
                if (isset($user_profile[0]->user_id)) {
                    $user_id = $user_profile[0]->user_id;
                } else {
                    Log::error('orderCompletedEvent : user does not exist.', ['user_id' => $user_uuid]);
                    return Response::json(array('code' => 201, 'message' => 'User does not exist.', 'cause' => '', 'data' => json_decode("{}")));
                }
                $country_code = $events->data->address->country;
                $subscription_id = isset($events->data->items[0]->subscription->id) ? $events->data->items[0]->subscription->id : "";
                $subscription_name = $events->data->items[0]->display;
                $product_id = $events->data->items[0]->product;
                $db_process = true;

                $response = $this->getDaysAndTypeOfPayment($product_id);
                $add_days = $response['add_days'];
                $add_month = $response['add_month'];
                $subscr_type = $response['subscr_type'];

                if(!$add_month){
                    $final_expiration_time = $next_billing_date = (new VerificationController())->addDaysIntoDate($create_time, $add_days);
                }else{
                    $final_expiration_time = $next_billing_date = date('Y-m-d H:i:s', strtotime($create_time . "+$add_month months"));
                }

                //get user subscription by user id
                $subscription_detail = DB::select('SELECT
                                                    id,
                                                    user_id,
                                                    transaction_id AS order_id,
                                                    paypal_id AS subscription_id,
                                                    subscr_type,
                                                    total_amount,
                                                    COALESCE(tax_amount,0) AS tax_amount,
                                                    cancellation_date,
                                                    final_expiration_time,
                                                    is_active
                                                FROM
                                                    subscriptions
                                                WHERE
                                                    user_id = ? AND
                                                    final_expiration_time >= ?
                                                ORDER BY id DESC', [$user_id, $create_time]);

                //check user subscription is exist if not then insert into subscription table
                if (count($subscription_detail) <= 0) {
                    Log::info('orderCompletedEvent : Subscriptions does not exist (New user).', ["user_id" => $user_id, "subscription_id" => $subscription_id]);

                    //check user subscription is active if active then print log & return response
                } elseif ($subscription_detail[0]->is_active == 1 || $subscription_detail[0]->cancellation_date == NULL) {
                    Log::info('orderCompletedEvent : Subscriptions is already active', ["db_subscription_id" => $subscription_detail[0]->subscription_id, "subscription_id" => $subscription_id]);
                    $db_process = false;

                } else {
                    $subscription_final_expiration_time = $subscription_detail[0]->final_expiration_time;
                    if(!$add_month){
                        $final_expiration_time = (new VerificationController())->addDaysIntoDate($subscription_final_expiration_time, $add_days);
                    }else{
                        $final_expiration_time = date('Y-m-d H:i:s', strtotime($subscription_final_expiration_time . "+$add_month months") );
                    }
                    Log::info('orderCompletedEvent : Subscriptions already exist. carry-on expire time.', ["updated_fep" => $final_expiration_time, "db_fep" => $subscription_final_expiration_time]);
                }

                if($db_process) {

                    $payment_mode = "FASTSPRING";
                    $product_details = DB::select('SELECT id AS product_id,
                                                    name AS product_name,
                                                    discount_percentage
                                                    FROM subscription_product_details
                                                    WHERE is_applied = 1');

                    if (count($product_details) > 0) {
                        $product_id = $product_details[0]->product_id;
                    } else {
                        $product_id = 2;
                    }

                    DB::beginTransaction();
                    DB::insert('INSERT IGNORE INTO subscriptions
                            (user_id,
                             product_id,
                             order_id,
                             transaction_id,
                             paypal_id,
                             payment_mode,
                             subscr_type,
                             txn_type,
                             payment_status,
                             total_amount,
                             tax_amount,
                             paypal_response,
                             payment_date,
                             activation_time,
                             expiration_time,
                             final_expiration_time,
                             payment_type,
                             create_time)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        [$user_id,
                            $product_id,
                            $events->data->order,
                            $events->data->order,
                            $subscription_id,
                            $payment_mode,
                            $subscr_type,
                            $events->type,
                            "Active",
                            $events->data->total,
                            $events->data->tax,
                            json_encode($events),
                            $create_time,
                            $create_time,
                            $next_billing_date,
                            $final_expiration_time,
                            Config::get('constant.PAYMENT_TYPE_OF_FASTSPRING'),
                            $create_time
                        ]);

                    $design_detail = DB::select('SELECT id, content_type FROM my_design_master WHERE user_id = ? AND is_active = 0 ORDER BY update_time DESC', [$user_id]);
                    if(count($design_detail) > 0) {
                        DB::update('UPDATE my_design_master SET is_active = 1 WHERE user_id = ? AND is_active = 0 LIMIT 1', [$user_id]);
                        (new UserController())->increaseMyDesignCount($user_id, $create_time, $design_detail[0]->content_type);
                        (new UserController())->deleteMultipleRedisKeys(["getMyDesignFolder$user_id", "getDesignFolderForAdmin$user_id", "getMyVideoDesignFolder$user_id", "getVideoDesignFolderForAdmin$user_id", "getMyIntroDesignFolder$user_id", "getIntroDesignFolderForAdmin$user_id"]);
                    }
                    DB::commit();

                    (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_uuid");
                    (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);

                }else{

                    DB::beginTransaction();
                    DB::update('UPDATE subscriptions
                            SET
                                txn_type = ?,
                                paypal_response = ?,
                                payment_date = ?,
                                activation_time = ?,
                                expiration_time = ?
                            WHERE id = ?',
                        [$events->type, json_encode($events), $create_time, $create_time, $next_billing_date, $subscription_detail[0]->id]);
                    DB::commit();

                }

                DB::beginTransaction();
                $is_exist = DB::select('SELECT 1 FROM payment_status_master WHERE txn_id=?', [$events->data->order]);
                if ($is_exist) {
                    DB::update('UPDATE payment_status_master SET paypal_payment_status = ?, paypal_response = ?, ipn_status = ?, is_active = ?, verify_status = ?, expiration_time = ? WHERE txn_id = ?', ["Active", json_encode($events), 1, 1, 1, $next_billing_date, $events->data->order]);
                } else {
                    DB::insert('INSERT INTO payment_status_master
                                            (user_id, txn_id, paypal_status, paypal_payment_status, paypal_response, ipn_status, verify_status, expiration_time, is_active, create_time)
                                        VALUES (?,?,?,?,?,?,?,?,?,?)',
                        [$user_id, $events->data->order, 1, "Active", json_encode($events), 1, 1, $next_billing_date, 1, $create_time]);
                }
                DB::commit();

//                $tax_amount = "  (Subtotal = " . ($events->data->subtotal) . " + tax = " . $events->data->tax . ")";
                $tax_amount = "  (Subtotal = " . ($events->data->subtotal) . " + " . $events->data->tax . "(Tax))";
                $payment_date_local = (New ImageController())->convertUTCDateTimeInToLocal($create_time, $country_code);
                $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($next_billing_date, $country_code);

                $template = 'payment_successful';
                $subject = 'PhotoADKing: Payment Received';
                $message_body = array(
                    'message' => 'Your payment received successfully. Following are the transaction details.',
                    'subscription_name' => $subscription_name,
                    'txn_id' => $events->data->order,
                    'txn_type' => 'Subscription[F]',
                    'first_name' => $events->data->tags->first_name,
                    'payment_received_from' => $events->data->customer->first . '(' . $events->data->customer->email . ')',
                    'total_amount' => $events->data->total,
                    'mc_currency' => $events->data->currency,
                    'payer_email' => $events->data->tags->user_email,
                    'payment_status' => $events->type,
                    'tax_amount' => $tax_amount,
                    'invoice_url' => $events->data->invoiceUrl,
                    'subscr_id' => $subscription_id,
                    'activation_date' => $create_time,
                    'next_billing_date' => $next_billing_date,
                    'activation_date_local' => $payment_date_local,
                    'next_billing_date_local' => $next_billing_date_local,
                );

                if(!$subscription_id){
                    array_splice($message_body, -5);
                }

                $api_name = 'orderCompletedEvent';
                $api_description = 'subscribe a new subscription.';

                //send an email to users and let you know that its payment has successful.
                $this->dispatch(new EmailJob($user_id, $events->data->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));
            }

            $response = Response::json(array('code' => 200, 'message' => 'Order completed event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));;

        }catch (Exception $e){
            (new ImageController())->logs("orderCompletedEvent",$e);
            //Log::error("orderCompletedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired order completed event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : To send an email to users and let you know that its payment has failed
    Description : This method has not take any argument as parameter.
    Return : return send an email to users if success otherwise error with specific status code
    */
    public function orderFailedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('orderFailedEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if($i >= 1){
                    Log::info("orderFailedEvent : multiple data arrives in request : $i");
                }

                $user_uuid = $events->data->tags->user_id;
                $user_account_url = $this->getUserAccountURL($events->data->account->id, "orderFailedEvent");
                Cache::forever("getDashBoardDetails:payment_status_details:$user_uuid", ['api_name' => 'orderFailedEvent', 'message' => Config::get('constant.PAYMENT_METHOD_MESSAGE'), 'reason' => $events->data->reason, 'user_account_url' => $user_account_url, 'is_new_event_occurs' => 1]);

                $this->addPaymentResponse($events, 2);
//                $tax_amount = "  (Subtotal = " . ($events->data->subtotal) . " + tax = " . $events->data->tax . ")";
                $tax_amount = "  (Subtotal = " . ($events->data->subtotal) . " + " . $events->data->tax . "(Tax))";

                $template = 'payment_failed';
                $subject = 'PhotoADKing: Payment Failed';
                $message_body = array(
                    'message' => 'Sorry, your payment has been failed. No charges were made. Following are the transaction details.',
                    'subscription_name' => $events->data->items[0]->display,
                    'txn_id' => $events->data->order,
                    'txn_type' => 'Subscription[F]',
                    'subscr_id' => 'N/A',
                    'first_name' => $events->data->tags->first_name,
                    'payment_received_from' => $events->data->customer->first . '(' . $events->data->customer->email . ')',
                    'phone' => $events->data->customer->phone,
                    'address' => $events->data->address->display,
                    'total_amount' => $events->data->total,
                    'mc_currency' => $events->data->currency,
                    'payer_email' => $events->data->tags->user_email,
                    'payment_status' => $events->type,
                    'tax_amount' => $tax_amount
                );
                $api_name = 'orderFailedEvent';
                $api_description = 'Subscription failed.';

                //send an email to users and let you know that its payment has failed
                $this->dispatch(new EmailJob($events->data->tags->user_id, $events->data->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));
            }

            $response = Response::json(array('code' => 200, 'message' => 'Order failed event process successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("orderFailedEvent",$e);
            //Log::error("orderFailedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired order failed event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : To send an email to users and let you know that its subscription has cancelled
    Description : This method has not take any argument as parameter.
    Return : return send an email to users if success otherwise error with specific status code
    */
    public function subscriptionCanceledEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('subscriptionCanceledEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if($i >= 1){
                    Log::info("subscriptionCanceledEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);
                $subscription_id = $events->data->subscription;
                $user_uuid = $events->data->tags->user_id;
                $create_time = date('Y-m-d H:i:s');

                //get user subscription by subscription id
                $subscription_detail = DB::select('SELECT
                                                    user_id,
                                                    transaction_id AS order_id,
                                                    paypal_id AS subscription_id,
                                                    subscr_type,
                                                    total_amount,
                                                    COALESCE(tax_amount,0) AS tax_amount,
                                                    cancellation_date,
                                                    final_expiration_time,
                                                    is_active
                                                    FROM subscriptions WHERE paypal_id = ? ORDER BY id DESC', [$subscription_id]);

                //check user subscription is exist if not then print log & return response
                if (count($subscription_detail) <= 0) {
                    Log::error('subscriptionCanceledEvent : Subscriptions does not exist', ["subscription_id" => $subscription_id]);
                    return Response::json(array('code' => 201, 'message' => 'Subscriptions does not exist.', 'cause' => '', 'data' => json_decode("{}")));

                    //check user subscription is active if active then print log & perform db process
                } elseif ($subscription_detail[0]->is_active == 1 || $subscription_detail[0]->cancellation_date == NULL) {
                    Log::error('subscriptionCanceledEvent : Subscriptions is active', ["db_subscription_id" => $subscription_detail[0]->subscription_id, "subscription_id" => $subscription_id]);

                    DB::beginTransaction();
                    $query = DB::update('UPDATE subscriptions AS sub
                                            LEFT JOIN payment_status_master AS psm ON psm.txn_id=sub.transaction_id
                                        SET
                                            sub.is_active = 0,
                                            psm.is_active = 0,
                                            sub.cancellation_date = CURRENT_TIMESTAMP,
                                            sub.response_message = "Subscription Cancelled from active subscription",
                                            sub.payment_status = "Cancelled"
                                        WHERE
                                            sub.paypal_id = ? AND
                                            sub.cancellation_date IS NULL AND
                                            psm.is_active = 1 AND
                                            sub.is_active = 1',[$subscription_id]);
                    DB::commit();

                    if (!$query) {
                        Log::info('subscriptionCanceledEvent : Subscriptions not updated.', ["user_id" => $events->data->tags->user_id, "subscription_id" => $events->data->subscription]);
                    }

                    $subscription_detail[0]->cancellation_date = $create_time;
                    (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_uuid");

                }

                $order_id = $subscription_detail[0]->order_id;
                $subscription_cancellation_date = $subscription_detail[0]->cancellation_date;
                $subscription_final_expiration_time = $subscription_detail[0]->final_expiration_time;
                $total_amount = isset($events->data->subtotal) ? $events->data->subtotal : $subscription_detail[0]->total_amount;
                $currency = $events->data->currency;
                $tax = $subscription_detail[0]->tax_amount;
//                $tax_amount = "  (Subtotal = " . ($total_amount - $tax) . " + tax = " . $tax . ")";
                $tax_amount = "  (Subtotal = " . ($total_amount - $tax) . " + " . $tax . "(Tax))";
                $country_code = $events->data->account->country;
                $cancellation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($subscription_cancellation_date, $country_code);
                $expiration_date_local = (New ImageController())->convertUTCDateTimeInToLocal($subscription_final_expiration_time, $country_code);

                $subject = 'PhotoADKing: Subscription Cancelled';
                $template = 'cancel_subscription';
                $message_body = array(
                    'message' => 'Your subscription cancelled successfully. Following are the subscription details.',
                    'subscription_name' => $events->data->display,
                    'txn_id' => $order_id,
                    'txn_type' => 'Subscription[F]',
                    'subscr_id' => $subscription_id,
                    'total_amount' => $total_amount,
                    'first_name' => $events->data->tags->first_name,
                    'payment_received_from' => $events->data->account->contact->first . '(' . $events->data->account->contact->email . ')',
                    'payment_status' => $events->type,
                    'payer_email' => $events->data->tags->user_email,
                    'mc_currency' => $currency,
                    'cancellation_date' => $subscription_cancellation_date,
                    'expiration_date' => $subscription_final_expiration_time,
                    'cancellation_date_local' => $cancellation_date_local,
                    'expiration_date_local' => $expiration_date_local
                );
                $api_name = 'subscriptionCanceledEvent';
                $api_description = 'subscription cancelled.';

                //send an email to users and let you know that its subscription has cancelled
                $this->dispatch(new EmailJob($subscription_detail[0]->user_id, $events->data->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));
            }

            $response = Response::json(array('code' => 200, 'message' => 'subscription canceled event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));;

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionCanceledEvent",$e);
            //Log::error("subscriptionCanceledEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription canceled event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : DB Un-canceled process
    Description : This method has not take any argument as parameter.
    Return : return perform db process if success otherwise error with specific status code
    */
    public function subscriptionUncanceledEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('subscriptionUncanceledEvent fired : ',[$events]);


            foreach ($events AS $i => $events) {

                if ($i >= 1) {
                    Log::info("subscriptionUncanceledEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);

                //get user subscription by subscription id
                $query = DB::update('UPDATE subscriptions AS sub
                                        LEFT JOIN payment_status_master AS psm ON psm.txn_id=sub.transaction_id
                                    SET
                                        sub.is_active = 1,
                                        psm.is_active = 1,
                                        sub.cancellation_date = NULL,
                                        sub.response_message = "Subscription Un-Cancelled",
                                        sub.payment_status = "UnCancelled"
                                    WHERE
                                        sub.paypal_id = ? AND
                                        sub.final_expiration_time > CURRENT_TIMESTAMP AND
                                        sub.cancellation_date IS NOT NULL AND
                                        psm.is_active = 0 AND
                                        sub.is_active = 0',[$events->data->subscription]);

                if (!$query) {
                    Log::info('subscriptionUncanceledEvent : Subscriptions not updated.', ["user_id" => $events->data->tags->user_id, "subscription_id" => $events->data->subscription]);
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'subscription uncanceled event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));;

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionUncanceledEvent",$e);
            //Log::error("subscriptionUncanceledEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription uncanceled event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : This webhook is only for QA to test 1. cancel subscription & 2. free user scheduler. This will not perform in live server.
    Description : This method has not take any argument as parameter.
    Return : return perform db process if success otherwise error with specific status code
    */
    public function subscriptionDeactivatedEvent(Request $request_body)
    {
        try {
            if (($response = $this->validateWebHookEvents($request_body)) != '')
              return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('subscriptionDeactivatedEvent fired : ', [$events]);

            if (Config::get('constant.ACTIVATION_LINK_PATH') != 'https://test.photoadking.com') {
                return Response::json(array('code' => 201, 'message' => 'Live user are not allowed to use this webhook.', 'cause' => '', 'data' => json_decode("{}")));
            }

            foreach ($events as $i => $events) {

                if ($i >= 1) {
                  Log::info("subscriptionDeactivatedEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);
                $subscription_id = $events->data->subscription;
                $user_uuid = $events->data->tags->user_id;
                $create_time = date('Y-m-d H:i:s');

                //get user subscription by subscription id
                $subscription_detail = DB::select('SELECT
                                                        user_id,
                                                        transaction_id AS order_id,
                                                        paypal_id AS subscription_id,
                                                        subscr_type,
                                                        total_amount,
                                                        COALESCE(tax_amount,0) AS tax_amount,
                                                        cancellation_date,
                                                        final_expiration_time,
                                                        is_active
                                                    FROM
                                                        subscriptions
                                                    WHERE paypal_id = ? ORDER BY id DESC', [$subscription_id]);

                //check user subscription is exist if not then print log & return response
                if (count($subscription_detail) <= 0) {
                    Log::error('subscriptionDeactivatedEvent : Subscriptions does not exist', ["subscription_id" => $subscription_id]);
                    return Response::json(array('code' => 201, 'message' => 'Subscriptions does not exist.', 'cause' => '', 'data' => json_decode("{}")));

                    //check user subscription is active if active then print log & perform db process
                } elseif ($subscription_detail[0]->is_active == 1 || $subscription_detail[0]->cancellation_date == NULL) {
                    Log::error('subscriptionDeactivatedEvent : Subscriptions is active', ["db_subscription_id" => $subscription_detail[0]->subscription_id, "subscription_id" => $subscription_id]);
                    $subscription_detail[0]->cancellation_date = $create_time;
                }

                DB::beginTransaction();
                $query = DB::update('UPDATE subscriptions AS sub
                                              LEFT JOIN payment_status_master AS psm ON psm.txn_id=sub.transaction_id
                                          SET
                                              sub.is_active = 0,
                                              psm.is_active = 0,
                                              sub.cancellation_date = CURRENT_TIMESTAMP,
                                              sub.final_expiration_time = CURRENT_TIMESTAMP,
                                              psm.expiration_time = CURRENT_TIMESTAMP,
                                              sub.response_message = "Subscription Deactivated",
                                              sub.payment_status = "Deactivated"
                                          WHERE
                                              sub.paypal_id = ?',[$subscription_id]);

                DB::delete('DELETE FROM user_session WHERE user_id = ?', [$subscription_detail[0]->user_id]);
                DB::commit();

                $this->changeUserRoleToFree($subscription_detail[0]->user_id);
                (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_uuid");

                if (!$query) {
                    Log::info('subscriptionDeactivatedEvent : Subscriptions not updated.', ["user_id" => $events->data->tags->user_id, "subscription_id" => $events->data->subscription]);
                }

                $order_id = $subscription_detail[0]->order_id;
                $subscription_cancellation_date = $subscription_detail[0]->cancellation_date;
                $subscription_final_expiration_time = $subscription_detail[0]->final_expiration_time;
                $total_amount = isset($events->data->subtotal) ? $events->data->subtotal : $subscription_detail[0]->total_amount;
                $currency = $events->data->currency;
                $tax = $subscription_detail[0]->tax_amount;
                //$tax_amount = "  (Subtotal = " . ($total_amount - $tax) . " + tax = " . $tax . ")";
                $tax_amount = "  (Subtotal = " . ($total_amount - $tax) . " + " . $tax . "(Tax))";
                $country_code = $events->data->account->country;
                $cancellation_date_local = (new ImageController())->convertUTCDateTimeInToLocal($subscription_cancellation_date, $country_code);
                $expiration_date_local = (new ImageController())->convertUTCDateTimeInToLocal($subscription_final_expiration_time, $country_code);
                $date = new DateTime($subscription_final_expiration_time);
                $final_expiration_time = $date->format('M d, Y H:i:s T');

                $subject = 'PhotoADKing: Subscription Cancelled';
                $template = 'cancel_subscription';
                $message_body = array(
                    'message' => 'Your subscription cancelled successfully. Following are the subscription details.',
                    'subscription_name' => $events->data->display,
                    'txn_id' => $order_id,
                    'txn_type' => 'Subscription[F]',
                    'subscr_id' => $subscription_id,
                    'total_amount' => $total_amount,
                    'first_name' => $events->data->tags->first_name,
                    'payment_received_from' => $events->data->account->contact->first . '(' . $events->data->account->contact->email . ')',
                    'payment_status' => $events->type,
                    'payer_email' => $events->data->tags->user_email,
                    'mc_currency' => $currency,
                    'tax_amount' => $tax_amount,
                    'cancellation_date' => $subscription_cancellation_date,
                    'expiration_date' => $subscription_final_expiration_time,
                    'cancellation_date_local' => $cancellation_date_local,
                    'expiration_date_local' => $expiration_date_local
                );
                $api_name = 'subscriptionCanceledEvent';
                $api_description = 'subscription cancelled.';

                //send an email to users and let you know that its subscription has cancelled
                $this->dispatch(new EmailJob($subscription_detail[0]->user_id, $events->data->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));

                $subject = 'PhotoADKing: Subscription Expired';
                $template = 'simple';
                $message_body = array(
                    'message' => '<p style="text-align: left">Thanks for signing up for <b>PhotoADKing</b>. We hope you have been enjoying the <b>
                                ' . $events->data->display . '</b>. <br><br><span style="color: #484747;">Unfortunately, your <b>' . $events->data->display . '</b> is ending on <b>'
                      . $final_expiration_time . '</b>.</span><br><br>We\'d love to keep you as a customer,
                                    and there is still time to subscribe to a new plan! Simply visit your account dashboard to subscribe.
                                    <br><br>As a reminder, when your purchase expires you will be automatically placed on the free plan.</p>',
                    'user_name' => $events->data->tags->first_name
                );

                //send an email to users and let you know that its now a free user
                $this->dispatch(new EmailJob($subscription_detail[0]->user_id, $events->data->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));

            }

            $response = Response::json(array('code' => 200, 'message' => 'Subscription deactivated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));;

        } catch (Exception $e) {
          (new ImageController())->logs("subscriptionDeactivatedEvent", $e);
          //Log::error("subscriptionDeactivatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
          $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription deactivated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
          DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : To send an email to users and let you know that its subscription has renewed & process renew in our system
    Description : This method has not take any argument as parameter.
    Return : return send an email to users & db operation if success otherwise error with specific status code
    */
    public function subscriptionChargeCompletedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::info('subscriptionChargeCompletedEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if ($i >= 1) {
                    Log::info("subscriptionChargeCompletedEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);

                $payment_type = Config::get('constant.PAYMENT_TYPE_OF_FASTSPRING');
                $free_user_role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
                $create_time = date('Y-m-d H:i:s');
                $subscr_date_format = date('M d, Y H:i:s T');

                $user_uuid = $events->data->subscription->tags->user_id;
                $subscription_id = $events->data->subscription->id;
                $order_id = $events->data->order->id;
                $transaction_type = $events->type;
                $subscription_name = $events->data->subscription->display;
                $sub_total_amount = $events->data->order->subtotal;
                $tax_amount = $events->data->order->tax;
                $total_amount = $events->data->order->total;

                $prouct_id = $events->data->subscription->product;
                $response = $this->getDaysAndTypeOfPayment($prouct_id);
                $add_days = $response['add_days'];
                $add_month = $response['add_month'];
                $subscr_type = $response['subscr_type'];

                if(!$add_month){
                    $final_expiration_time = $next_billing_date = (new VerificationController())->addDaysIntoDate($create_time, $add_days);
                } else {
                    $final_expiration_time = $next_billing_date = date('Y-m-d H:i:s', strtotime($create_time . "+$add_month months"));
                }

                $subscription_detail = DB::select('SELECT
                                                      sub.id,
                                                      sub.user_id,
                                                      sub.transaction_id,
                                                      sub.is_active,
                                                      sub.product_id,
                                                      sub.cancellation_date,
                                                      sub.final_expiration_time
                                                  FROM subscriptions AS sub
                                                  WHERE
                                                      sub.paypal_id = ? AND
                                                      sub.payment_type = ?
                                                  ORDER BY id DESC', [$subscription_id, $payment_type]);

                if (count($subscription_detail) <= 0) {
                    Log::error('subscriptionChargeCompletedEvent : Subscriptions does not exist', ["subscription_id" => $subscription_id]);
                    return Response::json(array('code' => 201, 'message' => 'Subscriptions does not exist.', 'cause' => '', 'data' => json_decode("{}")));
                }elseif($subscription_detail[0]->is_active == 0 || $subscription_detail[0]->cancellation_date != NULL){
                    Log::error('subscriptionChargeCompletedEvent : Subscriptions is in-active', ["subscription_id" => $subscription_id]);
                }elseif ($subscription_detail[0]->final_expiration_time >= $create_time){

                    $subscription_final_expiration_time = $subscription_detail[0]->final_expiration_time;
                    if(!$add_month){
                        $final_expiration_time = (new VerificationController())->addDaysIntoDate($subscription_final_expiration_time, $add_days);
                    }else {
                        $final_expiration_time = date('Y-m-d H:i:s', strtotime($subscription_final_expiration_time . "+$add_month months"));
                    }

                }

                $id = $subscription_detail[0]->id;
                $user_id = $subscription_detail[0]->user_id;
                $old_order_id = $subscription_detail[0]->transaction_id;

                DB::beginTransaction();
                DB::update('UPDATE subscriptions
                            SET
                                transaction_id=?,
                                txn_type = ?,
                                payment_status = "RenewActive",
                                total_amount = ?,
                                tax_amount = ?,
                                paypal_response = ?,
                                activation_time = ?,
                                expiration_time = ?,
                                final_expiration_time = ?,
                                cancellation_date = NULL,
                                is_active = 1,
                                response_message= ?
                            WHERE id = ? ',
                    [$order_id, $transaction_type, $total_amount, $tax_amount, json_encode($events), $create_time, $next_billing_date, $final_expiration_time, 'Recursive Payment', $id]);

                DB::update('UPDATE payment_status_master
                            SET
                                txn_id = ?,
                                paypal_response = ?,
                                paypal_status = ?,
                                ipn_status = ?,
                                is_active = ?,
                                verify_status = ?,
                                expiration_time = ?
                            WHERE
                                txn_id = ?', [$order_id, json_encode($events), 1, 1, 1, 1, $next_billing_date, $old_order_id]);
                DB::commit();

                (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_uuid");

                $check_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id=?',[$user_id]);
                if(count($check_user_role) > 0 && $check_user_role[0]->role_id == $free_user_role_id) {
                    (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);
                    Log::info("subscriptionChargeCompletedEvent : user role has been changed : $i",["role_id"=>$check_user_role]);
                }

                $country_code = $events->data->account->country;
                $tax_amount = "  (Subtotal = " . $sub_total_amount . " + " . $tax_amount . "(Tax))";
                $payment_date_local = (New ImageController())->convertUTCDateTimeInToLocal($create_time, $country_code);
                $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($next_billing_date, $country_code);

                $template = 'payment_successful';
                $subject = 'PhotoADKing: Payment Received For Subscription Renewal';
                $message_body = array(
                    'message' => 'Your subscription for <b>' . $subscription_name . '</b> has been renewed on <b>' . $subscr_date_format . '</b>.
Thanks for renewing the subscription for <b>' . $subscription_name . '</b>. We hope you are enjoying the PhotoADKing.',
                    'subscription_name' => $subscription_name,
                    'txn_id' => $order_id,
                    'txn_type' => 'Subscription[F]',
                    'subscr_id' => $subscription_id,
                    'first_name' => $events->data->subscription->tags->first_name,
                    'payment_received_from' => $events->data->account->contact->first . '(' . $events->data->account->contact->email . ')',
                    'total_amount' => $total_amount,
                    'mc_currency' => $events->data->currency,
                    'payer_email' => $events->data->subscription->tags->user_email,
                    'payment_status' => $events->type,
                    'activation_date' => $create_time,
                    'next_billing_date' => $next_billing_date,
                    'activation_date_local' => $payment_date_local,
                    'next_billing_date_local' => $next_billing_date_local,
                    'tax_amount' => $tax_amount,
                    'invoice_url' => $events->data->order->invoiceUrl
                );
                $api_name = 'subscriptionChargeCompletedEvent';
                $api_description = 'auto-renew subscription.';

                $this->dispatch(new EmailJob($user_id, $events->data->subscription->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));
            }

            $response = Response::json(array('code' => 200, 'message' => 'subscription charge completed event process successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionChargeCompletedEvent",$e);
            //Log::error("subscriptionChargeCompletedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription charge completed event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : To send an email to users and let you know that its subscription renewed has failed & process failed in our system
    Description : This method has not take any argument as parameter.
    Return : return send an email to users & db operation if success otherwise error with specific status code
    */
    public function subscriptionChargeFailedEvent(Request $request_body){

        try{
            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $client = new Client();
            $events = $request->events;
            //Log::info('subscriptionChargeFailedEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if ($i >= 1) {
                    Log::info("subscriptionChargeFailedEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);

                $payment_type = Config::get('constant.PAYMENT_TYPE_OF_FASTSPRING');
                $subscription_id = $events->data->subscription->id;
                $user_uuid = $events->data->subscription->tags->user_id;
                $reason = isset($events->data->reason) ? $events->data->reason : "N/A";
                $create_time = date('Y-m-d H:i:s');

                $subscription_detail = DB::select('SELECT
                                                      sub.id,
                                                      sub.user_id,
                                                      sub.is_active,
                                                      sub.expiration_time,
                                                      sub.final_expiration_time,
                                                      sub.cancellation_date
                                                  FROM subscriptions AS sub
                                                  WHERE
                                                      sub.paypal_id = ? AND
                                                      sub.payment_type = ?
                                                  ORDER BY sub.id DESC', [$subscription_id, $payment_type]);

                if (count($subscription_detail) <= 0) {
                    Log::error('subscriptionChargeFailedEvent : Subscriptions does not exist', ["subscription_id" => $subscription_id]);
                    return Response::json(array('code' => 201, 'message' => 'Subscriptions does not exist.', 'cause' => '', 'data' => json_decode("{}")));
                } elseif ($subscription_detail[0]->is_active == 0 || $subscription_detail[0]->cancellation_date != NULL) {
                    Log::error('subscriptionChargeFailedEvent : Subscriptions is already in-active.', ["subscription_id" => $subscription_id]);
                } else {
                    if ($subscription_detail[0]->final_expiration_time <= $create_time) {
                        $this->changeUserRoleToFree($subscription_detail[0]->user_id);
                    }

                    DB::beginTransaction();
                    $query = DB::update('UPDATE subscriptions AS sub
                                            LEFT JOIN payment_status_master AS psm ON psm.txn_id=sub.transaction_id
                                        SET
                                            sub.is_active = 0,
                                            psm.is_active = 0,
                                            sub.cancellation_date = CURRENT_TIMESTAMP,
                                            sub.response_message = "Subscription canceled because renewal failed",
                                            sub.payment_status = "RenewFailed"
                                        WHERE
                                            sub.paypal_id = ? AND
                                            sub.cancellation_date IS NULL AND
                                            psm.is_active = 1 AND
                                            sub.is_active = 1',[$subscription_id]);
                    DB::commit();

                    if(!$query){
                        Log::error('subscriptionChargeFailedEvent : Subscriptions not updated.', ["user_id" => $subscription_detail[0]->user_id, "subscription_id" => $subscription_id]);
                    }

                }

                $user_account_url = $this->getUserAccountURL($events->data->account->id, "subscriptionChargeFailedEvent");
                Cache::forever("getDashBoardDetails:payment_status_details:$user_uuid", ['api_name' => 'subscriptionChargeFailedEvent', 'message' => Config::get('constant.PAYMENT_METHOD_MESSAGE'), 'reason' => $reason, 'user_account_url' => $user_account_url, 'is_new_event_occurs' => 1]);

                $template = 'payment_failed';
                $subject = 'PhotoADKing: Payment Failed';
                $message_body = array(
                    'message' => 'Sorry, your payment has been failed. No charges were made. Following are the transaction details.',
                    'subscription_name' => $events->data->subscription->display,
                    'txn_id' => "N/A",
                    'txn_type' => 'Subscription[F]',
                    'subscr_id' => $subscription_id,
                    'first_name' => $events->data->subscription->tags->first_name,
                    'payment_received_from' => $events->data->account->contact->first . '(' . $events->data->account->contact->email . ')',
                    /* it will show full amount in subscription failed mail
                    'total_amount' => $events->data->subscription->price,*/
                    'total_amount' => $events->data->subscription->subtotal,
                    'mc_currency' => $events->data->subscription->currency,
                    'payer_email' => $events->data->subscription->tags->user_email,
                    'payment_status' => $events->type,
                    'user_account_url' => $user_account_url,
                    'how_to_update_payment_method' => Config::get('constant.HOW_TO_UPDATE_PAYMENT_METHOD_URL'),
                );
                $api_name = 'subscriptionChargeFailedEvent';
                $api_description = 'Subscription failed.';

                //send an email to users and let you know that its payment has failed
                $this->dispatch(new EmailJob($events->data->subscription->tags->user_id, $events->data->subscription->tags->user_email, $subject, $message_body, $template, $api_name, $api_description));
            }

            $response = Response::json(array('code' => 200, 'message' => 'subscription charge failed event process successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionChargeFailedEvent",$e);
            //Log::error("subscriptionChargeFailedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription charge failed event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : To send an email to users and let you know that its subscription renewed has failed & process failed in our system
    Description : This method has not take any argument as parameter.
    Return : return send an email to users & db operation if success otherwise error with specific status code
    */
    public function returnCreatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            Log::info('returnCreatedEvent fired : ',[$events]);

            foreach ($events AS $i => $events) {

                if ($i >= 1) {
                    Log::info("returnCreatedEvent : multiple data arrives in request : $i");
                }

                $this->addPaymentResponse($events, 2);

                //$user_profile = (new LoginController())->getUserDetailsByUserId($events->data->original->tags->user_id);
                $user_profile = (new LoginController())->getUserDetailsByUserId(isset($events->data->original->tags->user_id) ? $events->data->original->tags->user_id : $events->data->items[0]->subscription->tags->user_id);
                if (isset($user_profile[0]->user_id)) {
                    $user_id = $user_profile[0]->user_id;
                    $subscriptions_id = isset($events->data->original->subscriptions) ? $events->data->original->subscriptions : ["N/A"];
                    $order_id = isset($events->data->original->order) ? $events->data->original->order : "";
                    //$this->changeUserRoleToFree($user_id);

                    if(count($subscriptions_id) > 1) {
                        Log::info("returnCreatedEvent : multiple subscription_id arrives in request : ",[$subscriptions_id]);
                    }

                    DB::beginTransaction();
                    $query = DB::update('UPDATE subscriptions AS sub
                                            LEFT JOIN payment_status_master AS psm ON psm.txn_id=sub.transaction_id
                                        SET
                                            sub.is_active = 0,
                                            psm.is_active = 0,
                                            sub.cancellation_date = CURRENT_TIMESTAMP,
                                            sub.final_expiration_time = CURRENT_TIMESTAMP,
                                            psm.expiration_time = CURRENT_TIMESTAMP,
                                            sub.response_message = "Subscription canceled because amount return to user",
                                            sub.payment_status = "Refunded"
                                        WHERE
                                            sub.paypal_id = ? OR sub.transaction_id = ?',[$subscriptions_id[0], $order_id]);

                    DB::delete('DELETE FROM user_session WHERE user_id=?',[$user_id]);
                    DB::commit();

                    if(!$query){
                        Log::error('returnCreatedEvent : Subscriptions not updated.', ["user_id" => $user_id, "subscription_id" => $subscriptions_id[0]]);
                    }

                    $query2 = DB::update('UPDATE
                                              subscriptions AS sub
                                          SET
                                              sub.final_expiration_time = sub.expiration_time
                                          WHERE
                                              sub.user_id = ? AND
                                              sub.is_active = 1',[$user_id]);

                    if(!$query2){
                      Log::error('returnCreatedEvent : Final expiration time is not changed in active subscriptions.', ["user_id" => $user_id, "subscription_id" => $subscriptions_id[0]]);
                    }

                    $subscription_detail = DB::select('SELECT
                                                          sub.subscr_type
                                                       FROM
                                                          subscriptions AS sub
                                                       WHERE
                                                          sub.user_id = ? AND sub.final_expiration_time > NOW()
                                                       ORDER BY sub.final_expiration_time DESC',[$user_id]);

                    if(count($subscription_detail) > 0) {
                      $subscr_type = $subscription_detail[0]->subscr_type;
                      (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);
                    }else{
                      $this->changeUserRoleToFree($user_id);
                    }

                } else {
                    Log::error('returnCreatedEvent : user does not exist.', ['user_id' => $events->data->original->tags->user_id]);
                }
            }

            $response = Response::json(array('code' => 200, 'message' => 'return created event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("returnCreatedEvent",$e);
            //Log::error("returnCreatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired return created event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| Sub Functions |=============================*/
    public function getUserAccountURL($account_id, $api_name)
    {
        try{

            $get_user_auth_url = Config::get('constant.FASTSPRING_API_URL') . Config::get('constant.FASTSPRING_ACCOUNTS_API_NAME') . $account_id . "/authenticate";
            $username = Config::get('constant.FASTSPRING_API_USER_NAME');
            $password = Config::get('constant.FASTSPRING_API_PASSWORD');
            $client = new Client();

            try {
                $response = $client->get($get_user_auth_url, ['auth' => [$username, $password]]);
                $api_response = json_decode($response->getBody()->getContents());
                $user_account_url = $api_response->accounts[0]->url;

            }catch (RequestException  $e){
                $error_msg = json_decode($e->getResponse()->getBody()->getContents());
                Log::error("getUserAccountURL : $api_name : unable to generate URL", ["Exception" => $error_msg]);
                $user_account_url = Config::get('constant.FASTSPRING_USER_ACCOUNT_URL');
            }

        }catch (Exception $e){
            (new ImageController())->logs("getUserAccountURL : $api_name",$e);
            //Log::error("getUserAccountURL : $api_name :", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $user_account_url = Config::get('constant.FASTSPRING_USER_ACCOUNT_URL');
        }
        return $user_account_url;

    }

    public function addPaymentResponse($payment_response, $payment_from){
        try{

            DB::beginTransaction();
            DB::insert('INSERT INTO paypalipn_response(paypal_response,response_from) VALUES(?,?)',[json_encode($payment_response), $payment_from]);
            DB::commit();

            $response = True;

        }catch (Exception $e){
            (new ImageController())->logs("addPaymentResponse",$e);
            //Log::error("addPaymentResponse : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = False;
            DB::rollBack();
        }
        return $response;

    }

    public function getDaysAndTypeOfPayment($product_id){
        try{

            if ($product_id == Config::get('constant.PRODUCT_ID_OF_MONTHLY_STARTER') || $product_id == Config::get('constant.PRODUCT_ID_OF_MONTHLY_STARTER_DISCOUNTED')) {
                $add_days = 30;
                $subscr_type = Config::get('constant.MONTHLY_STARTER');
                $add_month = 1;
                //$role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER');

            } elseif ($product_id == Config::get('constant.PRODUCT_ID_OF_MONTHLY_STARTER_DISCOUNTED_STAGING')) {
                $add_days = 7;
                $subscr_type = Config::get('constant.MONTHLY_STARTER');
                $add_month = 0;
                //$role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER');

            } elseif ($product_id == Config::get('constant.PRODUCT_ID_OF_MONTHLY_PRO') || $product_id == Config::get('constant.PRODUCT_ID_OF_MONTHLY_PRO_DISCOUNTED') || $product_id == Config::get('constant.PRODUCT_ID_OF_MONTHLY_PRO_DISCOUNTED_STAGING')) {
                $add_days = 30;
                $subscr_type = Config::get('constant.MONTHLY_PRO');
                $add_month = 1;
                //$role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO');

            } elseif ($product_id == Config::get('constant.PRODUCT_ID_OF_YEARLY_STARTER') || $product_id == Config::get('constant.PRODUCT_ID_OF_YEARLY_STARTER_DISCOUNTED') || $product_id == Config::get('constant.PRODUCT_ID_OF_YEARLY_STARTER_DISCOUNTED_STAGING')) {
                $add_days = 365;
                $subscr_type = Config::get('constant.YEARLY_STARTER');
                $add_month = 12;
                //$role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER');

            } elseif ($product_id == Config::get('constant.PRODUCT_ID_OF_YEARLY_PRO') || $product_id == Config::get('constant.PRODUCT_ID_OF_YEARLY_PRO_DISCOUNTED') || $product_id == Config::get('constant.PRODUCT_ID_OF_YEARLY_PRO_DISCOUNTED_STAGING')) {
                $add_days = 365;
                $subscr_type = Config::get('constant.YEARLY_PRO');
                $add_month = 12;
                //$role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_PRO');

            } elseif ($product_id == Config::get('constant.PRODUCT_ID_OF_LIFETIME_PRO') || $product_id == Config::get('constant.PRODUCT_ID_OF_LIFETIME_PRO_DISCOUNTED')) {
                $add_days = 1825;
                $subscr_type = Config::get('constant.LIFETIME_PRO');
                $add_month = 60;
                //$role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_PRO');

            } else {
                $add_days = 0;
                $subscr_type = 0;
                $add_month = 0;
                //$role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
                Log::error('getDaysAndTypeOfPayment : Subscriptions id does not match', ["subscription_id" => $product_id]);
            }

            $response = array('add_days'=>$add_days, 'subscr_type'=>$subscr_type, 'add_month'=>$add_month);

        }catch (Exception $e){
            (new ImageController())->logs("getDaysAndTypeOfPayment",$e);
            //Log::error("getDaysAndTypeOfPayment : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = array('add_days'=>0, 'subscr_type'=>0, 'add_month'=>0);
        }
        return $response;

    }

    public function changeUserRoleToFree($user_id){

        try{

            $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
            DB::beginTransaction();
            $response = DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
            DB::commit();

            if(!$response){
                Log::error('changeUserRoleToFree : unable to perform query', ['user_id' => $user_id]);
            }

        }catch (Exception $e){
            (new ImageController())->logs("changeUserRoleToFree",$e);
            //Log::error("changeUserRoleToFree : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = 0;
            DB::rollBack();
        }
        return $response;
    }

    public function validateWebHookEvents($request_body){

        try{

            if (!$request_body->hasHeader('X-Fs-Signature')) {
                Log::error($request_body->path() . " : Required field token is missing or empty.");
                return Response::json(array('code' => 201, 'message' => 'Required field token is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            }

            $secret_key = Config::get('constant.FASTSPRING_WEBHOOK_AUTH_KEY');
            $hash_key = base64_encode( hash_hmac( 'sha256', file_get_contents('php://input') , $secret_key, true ) );

            if ($hash_key != $request_body->header('X-Fs-Signature')) {
                Log::error($request_body->path() . " : Sorry, Your token is invalid.");
                return Response::json(array('code' => 201, 'message' => 'Sorry, Your token is invalid.', 'cause' => '', 'data' => json_decode("{}")));
            }

            $request = json_decode($request_body->getContent());
            if(($response = (new VerificationController())->validateRequiredParameterIsArray(array('events'), $request)) != '') {
                Log::error($request_body->path() . " : Required field events is missing or empty.");
                return $response;
            }

            $response = '';

        }catch (Exception $e){
            (new ImageController())->logs("validateWebHookEvents",$e);
            //Log::error("validateWebHookEvents : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'validate webhook event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /* =================================| Unnecessary Webhook Event |=============================*/
    //All below api is Only for debugging purpose that when below webhook called. We did not used it.
    public function orderCanceledEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('orderCanceledEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'order canceled event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("orderCanceledEvent",$e);
            //Log::error("orderPaymentPendingEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired order canceled event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function orderPaymentPendingEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('orderPaymentPendingEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'order payment pending event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("orderPaymentPendingEvent",$e);
            //Log::error("orderPaymentPendingEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired order payment pending event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function orderApprovalPendingEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('orderApprovalPendingEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'order approval pending event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("orderApprovalPendingEvent",$e);
            //Log::error("orderApprovalPendingEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired order approval pending event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function fulfillmentFailedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('fulfillmentFailedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'fulfillment failed event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("fulfillmentFailedEvent",$e);
            //Log::error("fulfillmentFailedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired fulfillment failed event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*public function subscriptionDeactivatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('subscriptionDeactivatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'subscription deactivated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionDeactivatedEvent",$e);
            //Log::error("subscriptionDeactivatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription deactivated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }*/

    public function subscriptionUpdatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('subscriptionUpdatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'subscription updated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionUpdatedEvent",$e);
            //Log::error("subscriptionUpdatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription updated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function subscriptionTrialReminderEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('subscriptionTrialReminderEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'subscription trial reminder event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionTrialReminderEvent",$e);
            //Log::error("subscriptionTrialReminderEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription trial reminder event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function subscriptionPaymentReminderEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('subscriptionPaymentReminderEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'subscription payment reminder event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionPaymentReminderEvent",$e);
            //Log::error("subscriptionPaymentReminderEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription payment reminder event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function subscriptionPaymentOverdueEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('subscriptionPaymentOverdueEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'subscription payment overdue event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("subscriptionPaymentOverdueEvent",$e);
            //Log::error("subscriptionPaymentOverdueEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired subscription payment overdue event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function invoiceReminderEmailEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('invoiceReminderEmailEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'invoice reminder email event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("invoiceReminderEmailEvent",$e);
            //Log::error("invoiceReminderEmailEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired invoice reminder email event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function mailingListEntryUpdatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('mailingListEntryUpdatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'mailing list entry updated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("mailingListEntryUpdatedEvent",$e);
            //Log::error("mailingListEntryUpdatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired mailing list entry updated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function mailingListEntryAbandonedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('mailingListEntryAbandonedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'mailing list entry abandoned event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("mailingListEntryAbandonedEvent",$e);
            //Log::error("mailingListEntryAbandonedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired mailing list entry abandoned event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function mailingListEntryRemovedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('mailingListEntryRemovedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'mailing list entry removed event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("mailingListEntryRemovedEvent",$e);
            //Log::error("mailingListEntryRemovedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired mailing list entry removed event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function accountCreatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('accountCreatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'account created event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("accountCreatedEvent",$e);
            //Log::error("accountCreatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired account Created event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function accountUpdatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('accountUpdatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'account updated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("accountUpdatedEvent",$e);
            //Log::error("accountUpdatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired account updated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function payoutEntryCreatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('payoutEntryCreatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'payout entry created event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("payoutEntryCreatedEvent",$e);
            //Log::error("payoutEntryCreatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired payout entry created event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function quoteCreatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('quoteCreatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'quote created event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("quoteCreatedEvent",$e);
            //Log::error("quoteCreatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired quote created event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function quoteUpdatedEvent(Request $request_body){

        try{

            if (($response = $this->validateWebHookEvents($request_body)) != '')
                return $response;

            $request = json_decode($request_body->getContent());
            $events = $request->events;
            //Log::debug('quoteUpdatedEvent fired : ',[$events]);

            $response = Response::json(array('code' => 200, 'message' => 'quote updated event processed successfully.', 'cause' => '', 'data' => json_decode("{}")));

        }catch (Exception $e){
            (new ImageController())->logs("quoteUpdatedEvent",$e);
            //Log::error("quoteUpdatedEvent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fired quote updated event.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

}
