<?php

namespace App\Http\Controllers;

use Response;
use Config;
use DB;
use Log;
use DateTime;
use Illuminate\Http\Request;
use Cache;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\EmailJob;
use Mail;

/**
 * Class stripePaymentController
 *
 * @package api\app\Http\Controllers\api
 */
class stripePaymentController extends Controller{

  /*=============================== Product ====================================*/

  public function getAllProducts(Request $request_body){

    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);


      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

      $product = \Stripe\Product::all(['limit' => 3]);

      DB::beginTransaction();
      DB::update('UPDATE stripe_product_master SET status = 0 WHERE id = ?');
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Products fetched successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-DeleteProduct",$e);
//      Log::error("Stripe-DeleteProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  /*================================ Plan ======================================*/

  public function getAllPlansFromStripe(Request $request_body){

    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

//      $existing_plan_details = DB::select('SELECT plan_id AS id FROM stripe_plan_details');
//      dd($existing_plan_details);
//      $plan_id_list = $existing_plan_details[0]->id;

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
      $plan_details = \Stripe\Plan::all();
      $plan_data = $plan_details->data;
      //if (in_array("$user_id", array($admin,$sub_admin), true )){}
      foreach ($plan_data AS $plan){
        $id = $plan->id;
        $object = $plan->object;
        $nickname = $plan->nickname;
        $amount = $plan->amount / 100;
        $interval = $plan->interval;
        $active = ($plan->active == "true") ? 1 : 0 ;
        $created = $plan->created;
        $datetimeFormat = 'Y-m-d H:i:s';
        $date = new \DateTime();
        $date->setTimestamp($created);
        $create_time = $date->format($datetimeFormat);
        $plan_json_response = json_encode(json_decode(json_encode($plan)));


        DB::beginTransaction();
        DB::insert('INSERT INTO stripe_plan_details(plan_id, object, name, amount, plan_interval, plan_json_response, is_active, create_time) 
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?)',[$id,
                                                    $object,
                                                    $nickname,
                                                    $amount,
                                                    $interval,
                                                    $plan_json_response,
                                                    $active,
                                                    $create_time]);
        DB::commit();
      }
      $response = Response::json(array('code' => 200, 'message' => 'Plan fetched successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-getAllPlansFromStripe",$e);
//      Log::error("Stripe-getAllPlansFromStripe : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch plans.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/getAllPlansForUser",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="getAllPlansForUser",
   *        summary="get All Plans For User",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *     @SWG\Schema(),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"All plans fetched successfully.","cause":"","data":{"result":{{"plan_id": "1","name": "monthly starter","amount": "1200","plan_interval": "day"},{"plan_id": "3","name": "monthly pro","amount": "2200","plan_interval": "day"}}}}),),
   *        ),
   *     @SWG\Response(
   *            response=201,
   *            description="Error",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is Unable to fetch plans. Please try again..","cause":"Exception message","data":"{}"}),),
   *        ),
   *      )
   *
   */
  public function getAllPlansForUser(Request $request_body){

    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      if (!Cache::has("Config::get('constant.REDIS_KEY'):getAllPlansForUser")) {
        $result = Cache::rememberforever("getAllPlansForUser", function () {
          return DB::select('SELECT  plan_id , name, amount, plan_interval FROM  stripe_plan_details ORDER BY create_time');
        });
      }

      $redis_result = Cache::get("getAllPlansForUser");

      if (!$redis_result) {
        $redis_result = [];
      }

      $response = Response::json(array('code' => 200, 'message' => 'All plans fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-getAllPlansFromStripe",$e);
//      Log::error("Stripe-getAllPlansFromStripe : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch plans.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  /*========================== Create Subscription ==============================*/

  //Not used in live
  public function CreateStripePaymentMethod(Request $request_body){
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;

      $request = $request_body;
      if (($response = (new VerificationController())->validateRequiredParameter(array('number','exp_month','exp_year','cvc','type'), $request)) != '')
        return $response;

      $number = $request->number;
      $exp_month = $request->exp_month;
      $exp_year = $request->exp_year;
      $cvc = $request->cvc;
      $type = $request->type;

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
      \Stripe\Stripe::setApiVersion(Config::get('constant.STRIPE_API_VERSION'));

      $payment_method = \Stripe\PaymentMethod::create([
        'type' => $type,
        'card' => [
          'number' => $number,
          'exp_month' => $exp_month,
          'exp_year' => $exp_year,
          'cvc' => $cvc,
        ],
      ]);
//      Log::info('create payment method : ',[$payment_method]);

      $plan_id = 1;

      $request_body_data = ['payment_method_response'=>$payment_method,'plan_id'=>$plan_id,'user_id'=>$user_id];
      $data = $this->stripeUserPayment(json_encode($request_body_data));
//      Log::error('response : ',[$data]);


      $response = Response::json(array('code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-DeleteProduct",$e);
//      Log::error("Stripe-DeleteProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Create Stripe Payment Method.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/stripeUserPayment",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="stripeUserPayment",
   *        summary="Stripe User Payment",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *   	  @SWG\Schema(
   *          required={"payment_method","plan_id"},
   *          @SWG\Property(property="payment_method_response",  type="object", example={"tx": "9LJ58889NK990510S","st": "Completed","amt": "22%2e00","cc": "USD","cm": 2,"item_number": 3,"sig": "lpkICK9e1MSthZYoqGIF7T4UUvz%2bbYxK%2bxympLh3%2fX5W6l1tJYz2hJZloDIc%2fnulwbu59cOFJowHjFoKjxhqVmE%2fByJGJA45rt55IOLodzgfRWB0FpQXrHHAw1sQWJfgDh%2fAq0xtlXUFywwOiEQaUVkBb10IDwglDZlJsLn0yRs%3d"}, description=""),
   *          @SWG\Property(property="plan_id",  type="object", example="1", description=""),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Thank you, your payment was successful.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4Njk4NzkwLCJleHAiOjE1NjkzMDM1OTAsIm5iZiI6MTU2ODY5ODc5MCwianRpIjoiVGlSVVY3VmMzQ0dCWWNmUCJ9.bq6uVaByVeLCxQNsLd3_RonXklSFK9sfe9qNx0PX7Ms","user_detail":{"user_id":31,"user_name":"steave@gmail.com","first_name":"Steave","email_id":"steave@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":1,"mailchimp_subscr_id":"b149f77a27357223db2104142cf13a6f","role_id":5,"create_time":"2019-02-22 05:14:22","update_time":"2019-02-22 05:14:24","subscr_expiration_time":"2019-10-17 05:40:11","next_billing_date":"2019-10-17 05:40:11","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
   *        ),
   *      @SWG\Response(
   *            response=419,
   *            description="Running on multiple subscription",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":419,"message":"You are running on multiple subscriptions, please unsubscribe any one of them from the Paypal using below button, else you will be charged for all active subscriptions.","cause":"","data":"{}"}),),
   *        ),
   *     @SWG\Response(
   *            response=201,
   *            description="Error",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to create subscription.","cause":"Exception message","data":"{}"}),),
   *        ),
   *      )
   *
   */
  Public function stripeUserPayment(Request $request_body)
{

  try {
    $token = JWTAuth::getToken();
    JWTAuth::toUser($token);
    $user_id = JWTAuth::toUser($token)->id;

    $request = json_decode($request_body->getContent());
    if (($response = (new VerificationController())->validateRequiredParameter(array('payment_method_response', 'plan_id'), $request)) != '') {
//      Log::error('log error : ', [$response]);
      return $response;
    }

    $user_details = DB::select('SELECT email_id FROM user_master WHERE id = ?', [$user_id]);
    $email_id = $user_details[0]->email_id;

    if (($response = (new VerificationController())->verifyBillingInfo($user_id)) != '') {
      return $response;
    }
    $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
    $this->country_code = $billing_info->country_code;

    $result = DB::select('SELECT * FROM subscriptions
                                    WHERE user_id = ? AND
                                      cancellation_date IS NULL
                                    ORDER BY create_time DESC limit 1', [$user_id]);
    if (count($result) > 0) {
      return Response::json(array('code' => 419, 'message' => 'You are running one of the subscriptions, please unsubscribe or update your subscription with other plan.', 'cause' => '', 'data' => json_decode("{}")));
    }

    $actual_plan_id = $request->plan_id;

//    Log::info('User Payment method response : ', [$request->payment_method_response]);
    $payment_method = $request->payment_method_response;
    $payment_method_json_response = json_encode(json_decode(json_encode($payment_method)));
    $payment_method_id = $payment_method->id;
    $object = $payment_method->object;
    $card = $payment_method->card;
    $card_brand = $card->brand;
    $card_exp_month = $card->exp_month;
    $card_exp_year = $card->exp_year;
    $card_funding = $card->funding;
    $card_last4 = $card->last4;
    $created = $payment_method->created;
    $type = $payment_method->type;

    DB::beginTransaction();
    $stripe_payment_method_details = array(
      'user_id' => $user_id,
      'payment_method_id' => $payment_method_id,
      'object' => $object,
      'email_id' => $email_id,
      'card_brand' => $card_brand,
      'card_type' => $type,
      'exp_month' => $card_exp_month,
      'exp_year' => $card_exp_year,
      'funding' => $card_funding,
      'last_4_digit' => $card_last4,
      'created' => $created,
      'payment_method_json_response' => $payment_method_json_response,
      'is_active' => 1
    );
    $db_payment_method_id = DB::table('stripe_payment_method_details')->insertGetId($stripe_payment_method_details);
    DB::commit();

    $customer_array = $this->CreateCustomer($payment_method_id, $email_id, $user_id, $billing_info);
    If (!isset($customer_array['db_customer_id'])) {
      return Response::json(array('code' => 201, 'message' => $customer_array['error_code'], 'cause' => '', 'data' => json_decode("{}")));
    }
    $db_customer_id = $customer_array['db_customer_id'];
    $stripe_customer_id = $customer_array['stripe_customer_id'];

    /*Now we create 8 plan to resolve issue of indian currency*/
    /*check country and also check with supported plans*/
    $plan_id = (new VerificationController())->getPlanForIndia($actual_plan_id,$user_id);


    $subscription_array = $this->CreateSubscription($stripe_customer_id, $plan_id, $user_id, $actual_plan_id);
//    Log::info('$subscription_array : ', [$subscription_array]);
    if (!isset($subscription_array['db_subscription_id'])) {
        return Response::json(array('code' => 201, 'message' => $subscription_array['error_code'], 'cause' => '', 'data' => json_decode("{}")));
    }
    $db_subscription_id = $subscription_array['db_subscription_id'];
    $subscription_id = $subscription_array['stripe_subscription_id'];
    $sub_id = $subscription_array['subscription_id'];
    $current_period_start = $subscription_array['current_period_start'];
    $current_period_end = $subscription_array['current_period_end'];
    $expiration_time = $subscription_array['expiration_time'];
    $is_sub_verified = $subscription_array['is_sub_verified'];
    $client_secret = isset($subscription_array['client_secret']) ? $subscription_array['client_secret'] : '';
    $error_message = isset($subscription_array['error_message']) ? $subscription_array['error_message'] : '';
    //stripe_payment_status
    DB::beginTransaction();
    DB::insert('INSERT INTO stripe_payment_status(user_id,
                                    payment_method_id,
                                    customer_id,
                                    stripe_subscription_id,
                                    subscription_id,
                                    plan_id,
                                    require_3D_secure,
                                    error_message,
                                    current_period_start,
                                    current_period_end,
                                    expiration_time,
                                    is_active,
                                    is_verified_by_webhook)
                                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)',
      [$user_id,
        $db_payment_method_id,
        $db_customer_id,
        $db_subscription_id,
        $sub_id,
        $plan_id,
        $client_secret,
        $error_message,
        $current_period_start,
        $current_period_end,
        $expiration_time,
        $is_sub_verified]);
    DB::commit();
    if (isset($subscription_array['error_status']) == 1) {
      return Response::json(array('code' => 444, 'message' => 'Subscriptions require 3D secure authentication.', 'cause' => '', 'data' => array("client_secret"=>$subscription_array['client_secret'])));
    }



    $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
    if ($subscription_array['status'] == "active") {

      $user_name = $user_detail->first_name;
      $template = "payment_successful";
      $subject = 'PhotoADKing: Subscribe The New Plan';
      $message = 'Thank you for purchasing subscription for the ' . $subscription_array['plan_nickname'] . '.';
      $subscription_name = $subscription_array['plan_nickname'];
      $subscr_id = $subscription_id;
      $first_name = $user_detail->first_name;
      $payment_received_from = $user_detail->email_id;
      $txn_type = 'Subscription[S]';
      $mc_currency = $subscription_array['currency'];
      $total_amount = $subscription_array['plan_amount'];
      $payment_status = $subscription_array['status'];
      $invoice_url = $subscription_array['invoice_url'];
      $email = $user_detail->email_id;
      $activation_date = $current_period_start;
      $next_billing_date = $current_period_end;
      $api_name = 'stripeUserPayment';
      $api_description = 'subscribe a new subscription.';
      //send mail of subscription successful
      $response = $this->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $total_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id = '',$this->country_code);
      if (!isset($response['success'])) {
        Log::error('unable to send mail to user');
      }

      $activation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($current_period_start,$this->country_code);
      $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($current_period_end,$this->country_code);

      $template = 'payment_successful';
      $subject = 'PhotoADKing: Payment Received';
      $message_body = array(
        'message' => 'Your payment received successfully. Following are the transaction details.',
        'subscription_name' => $subscription_name,
        'txn_id' => $subscription_id,
        'txn_type' => 'Subscription[S]',
        'subscr_id' => $subscription_id,
        'first_name' => $user_detail->first_name,
        'payment_received_from' => $user_detail->first_name . '(' . $user_detail->email_id . ')',
        'total_amount' => $total_amount,
        'mc_currency' => $mc_currency,
        'payer_email' => $user_detail->email_id,
        'payment_status' => $payment_status,
        'activation_date' => $current_period_start,
        'next_billing_date' => ($current_period_end != NULL) ? $current_period_end : 'NA',
        'activation_date_local' => $activation_date_local,
        'next_billing_date_local' => ($next_billing_date_local != NULL) ? $next_billing_date_local : ''
      );
      $api_name = 'stripeUserPayment';
      $api_description = 'subscribe a new subscription.';

      $this->dispatch(new EmailJob($user_id, $email, $subject, $message_body, $template, $api_name, $api_description));

      /*$requestHeaders = apache_request_headers();
      $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/
      $response = Response::json(array('code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));
    } else {

      $template = 'subscr_in_wait_mode';
      $subject = 'PhotoADKing: Payment information';
      $message_body = array(
        'user_name' => $user_detail->first_name,
        'sub_type' => $subscription_array['plan_nickname'],
        'activate_date' => $current_period_start.' UTC'
      );
      $api_name = 'stripeUserPayment';
      $api_description = 'subscribe a new subscription.';

      $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));

      /*$requestHeaders = apache_request_headers();
      $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/
      $response = Response::json(array('code' => 436, 'message' => 'Thank you, We have received your payment request and will respond within 24 hours.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));
    }

  } catch (\Stripe\Exception\CardException $e) {

    // Since it's a decline, \Stripe\Exception\CardException will be caught
    Log::error('stripeUserPayment -> CardException : ', ["Status" => $e->getHttpStatus(), "Type" => $e->getError()->type, "Code" => $e->getError()->code, "Param" => $e->getError()->param, "Message" => $e->getError()->message]);
    $response = Response::json(array('code' => 201, 'message' => 'PAK : it a decline,card exception will be caught.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

  } catch (\Stripe\Exception\RateLimitException $e) {

    // Too many requests made to the API too quickly
    Log::error('stripeUserPayment -> RateLimitException : ', ["Status" => $e->getHttpStatus(), "Type" => $e->getError()->type, "Code" => $e->getError()->code, "Param" => $e->getError()->param, "Message" => $e->getError()->message]);
    $response = Response::json(array('code' => 201, 'message' => 'PAK : Too many requests made to the API too quickly.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

  } catch (\Stripe\Exception\InvalidRequestException $e) {

    // Invalid parameters were supplied to Stripe's API
    Log::error('stripeUserPayment -> InvalidRequestException : ', ["Status" => $e->getHttpStatus(), "Type" => $e->getError()->type, "Code" => $e->getError()->code, "Param" => $e->getError()->param, "Message" => $e->getError()->message]);
    $response = Response::json(array('code' => 201, 'message' => 'PAK : Invalid parameters were supplied to Stripe API.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

  } catch (\Stripe\Exception\AuthenticationException $e) {

    // Authentication with Stripe's API failed
    // (maybe you changed API keys recently)
    Log::error('stripeUserPayment -> AuthenticationException : ', ["Status" => $e->getHttpStatus(), "Type" => $e->getError()->type, "Code" => $e->getError()->code, "Param" => $e->getError()->param, "Message" => $e->getError()->message]);
    $response = Response::json(array('code' => 201, 'message' => 'PAK : Authentication with Stripe API failed.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

  } catch (\Stripe\Exception\ApiConnectionException $e) {

    // Network communication with Stripe failed
    Log::error('stripeUserPayment -> ApiConnectionException : ', ["Status" => $e->getHttpStatus(), "Type" => $e->getError()->type, "Code" => $e->getError()->code, "Param" => $e->getError()->param, "Message" => $e->getError()->message]);
    $response = Response::json(array('code' => 201, 'message' => 'PAK : Network communication with Stripe failed.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

  } catch (\Stripe\Exception\ApiErrorException $e) {

    // Display a very generic error to the user, and maybe send
    // yourself an email
    Log::error('stripeUserPayment -> ApiErrorException : ', ["Status" => $e->getHttpStatus(), "Type" => $e->getError()->type, "Code" => $e->getError()->code, "Param" => $e->getError()->param, "Message" => $e->getError()->message]);
    $response = Response::json(array('code' => 201, 'message' => 'PAK : Display a very generic error to the user, and maybe send.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));

  } catch (Exception $e) {
    (new ImageController())->logs("Stripe-stripeUserPayment",$e);
//    Log::error("Stripe-stripeUserPayment : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
      [$user_id,
        "stripeUserPayment",
        "occur from exception block",
        "unable to pay payment with stripe",
        $e->getMessage()]);
    $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'pay payment.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    DB::rollBack();
  }
  return $response;
}

  //Create customer
  function CreateCustomer($payment_method_id,$email_id,$user_id,$billing_info){

    try {

      //create customer
      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
      \Stripe\Stripe::setApiVersion(Config::get('constant.STRIPE_API_VERSION'));

      $full_name = $billing_info->full_name;
      $address = $billing_info->address;
      $country = $billing_info->country_code;
      $state = $billing_info->state;
      $city = $billing_info->city;
      $zip_code = $billing_info->zip_code;

      $customer_exist = DB::select('SELECT id,is_active,customer_id FROM stripe_customer_details WHERE user_id = ? ORDER BY update_time DESC ',[$user_id]);
      if(count($customer_exist) > 0){

          $db_customer_id = $customer_exist[0]->id;
          DB::beginTransaction();
          DB::update('UPDATE stripe_customer_details SET is_active = 1 WHERE  id = ?',[$db_customer_id]);
          DB::commit();

          $customer_id = $customer_exist[0]->customer_id;
          $stripe = new \Stripe\StripeClient(
            Config::get('constant.STRIPE_API_KEY')
          );

          $stripe->paymentMethods->attach(
            $payment_method_id,
            ['customer' => $customer_id,
              ]
          );

        $stripe->customers->update(
          $customer_id,
          ["invoice_settings" => [
            "default_payment_method" => $payment_method_id
          ]]
        );

//        $payment_method = \Stripe\PaymentMethod::attach($payment_method_id,[
//          'customer' => $customer_id,
//        ]);

//          Log::error('new customer with updated payment method : ',[$customer]);
          $result = array('db_customer_id'=>$db_customer_id,
            'stripe_customer_id'=>$customer_id
          );

//        Log::error('Customer details [existing]: ',['user'=>$email_id,'customer'=>$customer_id]);
          return $result;
      }else{
        # This creates a new Customer and attaches the default PaymentMethod in one API call.
        $customer = \Stripe\Customer::create([
          'name' => $full_name,
          'address' => [
            'line1' => $address,
            'postal_code' => $zip_code,
            'city' => $city,
            'state' => $state,
            'country' => $country,
          ],
          "payment_method" => $payment_method_id,
          "email" => $email_id,
          "invoice_settings" => [
            "default_payment_method" => $payment_method_id
          ]
        ]);

        //Log::info('User Customer Created : ',[$customer]);
        $customer_id = $customer->id;
        $customer_object = $customer->object;
        $balance = $customer->balance / 100;
        $currency = $customer->currency;
        $customer_balance = $balance.' '.$currency;

        $datetimeFormat = 'Y-m-d H:i:s';
        $customer_created = $customer->created;
        $date = new \DateTime();
        $date->setTimestamp($customer_created);
        $customer_created = $date->format($datetimeFormat);

        $customer_email = $customer->email;
        $customer_json_response = json_encode(json_decode(json_encode($customer)));

        DB::beginTransaction();
        $stripe_customer_details = array(
          'user_id'=>$user_id,
          'customer_id'=>$customer_id,
          'object'=>$customer_object,
          'balance'=>$customer_balance,
          'created'=>$customer_created,
          'email_id'=>$customer_email,
          'customer_json_response'=>$customer_json_response,
          'is_active' => 1
        );
        $db_customer_id = DB::table('stripe_customer_details')->insertGetId($stripe_customer_details);
        DB::commit();

        $result = array('db_customer_id'=>$db_customer_id,
          'stripe_customer_id'=>$customer_id
        );
//        Log::error('Customer details [new]: ',['user'=>$email_id,'customer'=>$customer_id]);
        return $result;
      }

    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-CreateCustomer",$e);
//      Log::error("Stripe-CreateCustomer : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "Fun-CreateCustomer",
          "occur from exception block",
          "unable to create customer with stripe",
          $e->getMessage()]);
      DB::rollBack();
      return array('error_code'=>$e->getMessage());
    }
  }

  //Create subscription
  function CreateSubscription($customer_id,$plan_id,$user_id,$actual_plan_id){
    try{

      $is_discount_applied = Config::get('constant.PHOTOADKING_APPLIED_DISCOUNT');
//      Log::info('CreateSubscription : is_discount_applied ',[$is_discount_applied]);
      if($is_discount_applied != 0){
        $subscription = \Stripe\Subscription::create([
          "customer" => $customer_id,
          "items" => [
            [
              "plan" => $plan_id,
            ],
          ],
          'coupon' => $is_discount_applied,
          "expand" => ['latest_invoice.payment_intent']
        ]);
      }else{
        $subscription = \Stripe\Subscription::create([
          "customer" => $customer_id,
          "items" => [
            [
              "plan" => $plan_id,
            ],
          ],
          "expand" => ['latest_invoice.payment_intent']
        ]);
      }

//      Log::info('create new sub : ');

      if(!isset($subscription->id)){
        Log::error('CreateSubscription : Unable to get subscription id from subscription array');
        return array('error_code'=>'Unable to get subscription id from subscription array with 4##');
      }
      $subscription_id = $subscription->id;
      $subscription_object = $subscription->object;
      $status = $subscription->status;

      $billing_cycle_anchor = $subscription->billing_cycle_anchor;
      $datetimeFormat = 'Y-m-d H:i:s';
      $date = new \DateTime();
      $date->setTimestamp($billing_cycle_anchor);
      $billing_cycle_anchor = $date->format($datetimeFormat);

      $current_period_start = $subscription->current_period_start;
      $date = new \DateTime();
      $date->setTimestamp($current_period_start);
      $current_period_start = $date->format($datetimeFormat);

      $current_period_end = $subscription->current_period_end;
      $date = new \DateTime();
      $date->setTimestamp($current_period_end);
      $current_period_end = $date->format($datetimeFormat);

      $cancel_at = $subscription->cancel_at;
      If($cancel_at != ''){
        $date = new \DateTime();
        $date->setTimestamp($cancel_at);
        $cancel_at = $date->format($datetimeFormat);
      }

      $latest_invoice = $subscription->latest_invoice;
      $invoice_id = $latest_invoice->id;
      $discount = $latest_invoice->discount;

      $items = $subscription->items;
      $data = $items->data;
      $data_plan = $data[0]->plan;
      $plan_id = $data_plan->id;

      $plan_amount = $data_plan->amount / 100;
      if(isset($discount->coupon)){
        $coupon = $discount->coupon;
        $percent_off = $coupon->percent_off;
        $plan_amount = $plan_amount * $percent_off / 100 ;
      }


      $plan_interval = $data_plan->interval;
      $plan_nickname = $data_plan->nickname;
      $plan_product = $data_plan->product;
      $currency = $data_plan->currency;

      $account_name = $latest_invoice->account_name;
      $collection_method = $subscription->collection_method;
      $hosted_invoice_url = $latest_invoice->hosted_invoice_url;

      isset($latest_invoice->payment_intent) ? $payment_intent =  $latest_invoice->payment_intent : '';
      $payment_error = $payment_intent->last_payment_error;
      if(isset($payment_error)){
//        Log::error('CreateSubscription :',["error_code"=>$payment_error->message,"error_status"=>1,"client_secret"=>"No","last_payment_error"=>$payment_error,"type"=>"no type"]);
        return array("error_code"=>$payment_error->message,"error_status"=>1,"client_secret"=>"No","last_payment_error"=>$payment_error,"type"=>"no type");
      }

      $created = $subscription->created;
      $date = new \DateTime();
      $date->setTimestamp($created);
      $created = $date->format($datetimeFormat);

      $create_time = date('Y-m-d H:i:s');

      $subscription_json_response = json_encode(json_decode(json_encode($subscription)));

      if ($status == "active") {
        $paypal_status = 1;
        $is_active = 1;
        $subscr_type = $actual_plan_id;

        $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
        $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
        $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
        $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

        (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);
        $days_to_add = 0;
        if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {
          $expiration_time = $response = (new VerificationController())->addDaysIntoDate($create_time, 30);
        } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

          $expiration_time = $response = (new VerificationController())->addDaysIntoDate($create_time, 365);
        } else {
          $expiration_time = $create_time;
        }
      } elseif($status ==  "incomplete") {

        if(isset($payment_intent)){
            $payment_intent_status = $payment_intent->status;
            if ($payment_intent_status == "requires_action"){
              $client_secret = $payment_intent->client_secret;
              $last_payment_error = $payment_intent->last_payment_error;
              $error_type = $payment_intent->type;

              $requires_action_array = array("error_status"=>1,"client_secret"=>$client_secret,"last_payment_error"=>$last_payment_error,"type"=>$error_type);

            }elseif ($payment_intent_status == "requires_payment_method"){
              $last_payment_error = isset($payment_intent->last_payment_error->message) ? $payment_intent->last_payment_error->message : '';
            }else{
              $last_payment_error = isset($payment_intent->last_payment_error->message) ? $payment_intent->last_payment_error->message : '';
            }
        }else{
          Log::error('Payment intent is not settled with INCOMPLETE status of subscription');
        }
        $days_to_add = 0;
        $paypal_status = 0;
        $is_active = 0;
        $expiration_time = $current_period_end;
      } else {
        $days_to_add = 0;
        $paypal_status = 0;
        $is_active = 0;
        $expiration_time = $current_period_end;
      }

      $is_verified = DB::update('UPDATE stripe_webhook_details SET is_verify = 1,is_active = 0 Where subscription_id = ? AND invoice_id = ? AND is_active = 1',[$subscription_id,$invoice_id]);
      If($is_verified == 1 && $is_active == 1){
        $is_verify = 1;
      }else {
        $is_verify = 0;
      }

      DB::beginTransaction();

      //stripe_subscription_master
      $stripe_subscription_master = array(
        'user_id'=>$user_id,
        'subscription_id'=>$subscription_id,
        'invoice_id'=>$invoice_id,
        'plan_id'=>$plan_id,
        'nick_name'=>$plan_nickname,
        'product'=>$plan_product,
        'amount'=>$plan_amount,
        'plan_interval'=>$plan_interval,
        'cancel_at'=>$cancel_at,
        'hosted_invoice_url'=>$hosted_invoice_url,
        'account_name'=>$account_name,
        'collection_method'=>$collection_method,
        'subscription_json_response'=>$subscription_json_response,
        'is_active' => $is_active,
        'is_verify' => $is_verify,
        'created'=>$created
      );
      $db_subscription_id = DB::table('stripe_subscription_master')->insertGetId($stripe_subscription_master);

      //payment_status_master
      DB::insert('INSERT INTO payment_status_master (
                          user_id,
                          txn_id,
                          paypal_status,
                          paypal_payment_status,
                          paypal_response,
                          expiration_time,
                          is_active,
                          create_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?)',
        [
          $user_id,
          $subscription_id,
          $paypal_status,
          $status,
          $subscription_json_response,
          $current_period_end,
          $is_active,
          $create_time
        ]);
      $product_details = DB::select('SELECT id AS product_id,
              name AS product_name,
              discount_percentage
              FROM subscription_product_details
              WHERE is_applied = 1');

      $product_id = $product_details[0]->product_id;

      if($status == "active"){
        $subscriptions = array(
          'user_id'=>$user_id,
          'transaction_id'=>$subscription_id,
          'paypal_id'=>$subscription_id,
          'payment_mode'=>"STRIPE",
          'subscr_type'=>$plan_id,
          'product_id'=>$product_id,
          'txn_type'=>$subscription_object,
          'payment_status'=>$status,
          'total_amount'=>$plan_amount,
          'paypal_response'=>$subscription_json_response,
          'payment_date'=>$billing_cycle_anchor,
          'activation_time'=>$current_period_start,
          'expiration_time'=>$current_period_end,
          'days_to_add'=>$days_to_add,
          'cancellation_date'=>$cancel_at,
          'payment_type'=>2,
          'create_time'=>$create_time,
        );
      }else{
        $subscriptions = array(
          'user_id'=>$user_id,
          'transaction_id'=>$subscription_id,
          'paypal_id'=>$subscription_id,
          'payment_mode'=>"STRIPE",
          'subscr_type'=>$plan_id,
          'product_id'=>$product_id,
          'txn_type'=>$subscription_object,
          'payment_status'=>$status,
          'total_amount'=>$plan_amount,
          'paypal_response'=>$subscription_json_response,
          'payment_date'=>$billing_cycle_anchor,
          'activation_time'=>$current_period_start,
          'expiration_time'=>$current_period_end,
          'days_to_add'=>$days_to_add,
          'cancellation_date'=>$cancel_at,
          'payment_type'=>2,
          'is_active'=>0,
          'create_time'=>$create_time,
        );
      }
      //payment_type : "1 = paypal","2 = stripe"
      //subscriptions "Stripe_Payment"

      $sub_id = DB::table('subscriptions')->insertGetId($subscriptions);
      DB::commit();

      $subscription_return = array('db_subscription_id'=>$db_subscription_id,
        'stripe_subscription_id'=>$subscription_id,
        'subscription_id'=>$sub_id,
        'current_period_start'=>$current_period_start,
        'current_period_end'=>$current_period_end,
        'expiration_time'=>$expiration_time,
        'plan_nickname'=>$plan_nickname,
        'currency'=>$currency,
        'plan_amount'=>$plan_amount,
        'status'=>$status,
        'invoice_url'=>$hosted_invoice_url,
        'is_sub_verified'=>$is_verify,
        'error_message'=>isset($last_payment_error) ? $last_payment_error : NULL,
      );

      //if required 3D secure
      if(isset($requires_action_array)){
        $requires_action_array = array_merge($requires_action_array,$subscription_return);
        return $requires_action_array;
      }

      //return values
      return $subscription_return;


    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-CreateSubscription",$e);
//      Log::error("Stripe-CreateSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "Fun-CreateSubscription",
          "occur from exception block",
          "unable to create subscription with stripe",
          $e->getMessage()]);
      DB::rollBack();
      return array('error_code'=> $e->getMessage());
    }
  }

  /*=========================== Cancel Subscription =============================*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/cancelStripeSubscription",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="cancelStripeSubscription",
   *        summary="cancel Stripe Subscription",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *   	  @SWG\Schema(
   *          required={"cancellation_type"},
   *          @SWG\Property(property="cancellation_type",  type="object", example="1", description="1:Cancel subscription now ,2:Cancel subscription at the end of the current subscription period"),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Subscription cancelled successfully.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4Njk4NzkwLCJleHAiOjE1NjkzMDM1OTAsIm5iZiI6MTU2ODY5ODc5MCwianRpIjoiVGlSVVY3VmMzQ0dCWWNmUCJ9.bq6uVaByVeLCxQNsLd3_RonXklSFK9sfe9qNx0PX7Ms","user_detail":{"user_id":31,"user_name":"steave@gmail.com","first_name":"Steave","email_id":"steave@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":1,"mailchimp_subscr_id":"b149f77a27357223db2104142cf13a6f","role_id":5,"create_time":"2019-02-22 05:14:22","update_time":"2019-02-22 05:14:24","subscr_expiration_time":"2019-10-17 05:40:11","next_billing_date":"2019-10-17 05:40:11","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
   *        ),
   *     @SWG\Response(
   *            response=201,
   *            description="Error",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is Unable to cancel subscription. Please try again..","cause":"Exception message","data":"{}"}),),
   *        ),
   *      )
   *
   */
  Public function cancelStripeSubscription(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;
      $payer_email = JWTAuth::toUser($token)->email_id;

      $request = json_decode($request_body->getContent());

      //cancellation_type = (1 : Cancel subscription now ,2 : Cancel subscription at the end of the current subscription period)
      /* if (($response = (new VerificationController())->validateRequiredParameter(array('cancellation_type'), $request)) != '')
        return $response;
      $cancellation_type = $request->cancellation_type;*/
      $datetimeFormat = 'Y-m-d H:i:s';

      $sub_result = DB::select('SELECT
                      subscr_type,
                      expiration_time,
                      total_amount,
                      paypal_id,
                      payment_type,
                      payment_status,
                      is_active
                      FROM subscriptions
                      WHERE user_id = ?
                      ORDER BY id DESC', [$user_id]);

      if (count($sub_result) <= 0) {

        return $response = Response::json(array('code' => 201, 'message' => 'You are not subscriber.', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->payment_type != 2) {

        return $response = Response::json(array('code' => 201, 'message' => 'You are not a Stripe Subscriber, please cancel a subscription using PayPal.', 'cause' => '', 'data' => json_decode("{}")));

      } else {
        $result_status = $sub_result[0]->payment_status;
        if ($result_status == "incomplete") {
//          Log::info('is_subscription in incomplete mode');
          $subscription_id = $sub_result[0]->paypal_id;


          \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

          $retrieve_subscription = \Stripe\Subscription::retrieve(
            $subscription_id
          );
//          Log::info('cancelSubscription : retrieve subscription : ',[$retrieve_subscription]);
          $subscription = $retrieve_subscription->cancel();
//          Log::info('cancelSubscription : Cancel subscription now : ',[$subscription]);

          $status = $subscription->status;
//          if ($status != "incomplete_expired") {
//            return $response = Response::json(array('code' => 201, 'message' => 'Unable to cancel subscription. Please try again.', 'cause' => '', 'data' => json_decode("{}")));
//          }
          $canceled_date = $subscription->canceled_at;
          $date = new \DateTime();
          $date->setTimestamp($canceled_date);
          $canceled_at = $date->format($datetimeFormat);

          //$current_period_end = $subscription->canceled_at; //right for live [current_period_end]
          $current_period_end = $subscription->current_period_end; //only use for subscrption is deletre now [canceled_at]
          $date = new \DateTime();
          $date->setTimestamp($current_period_end);
          $current_period_end_date = $date->format($datetimeFormat);


          DB::beginTransaction();

          DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $canceled_at, $user_id]);

          DB::update('UPDATE subscriptions SET        
                              final_expiration_time = ?, is_expired = 1 WHERE 
                    user_id = ? AND transaction_id = ?', [$canceled_at, $user_id, $subscription_id]);

          DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$user_id]);

          DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

          DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

          DB::commit();

          (new UserController())->cancelSubscriptionByPaypalID($subscription_id, $payer_email);

          $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
          /*$requestHeaders = apache_request_headers();
          $authorization = isset($requestHeaders['Authorization']) ? $requestHeaders['Authorization'] : $requestHeaders['authorization'];
          $jwt_token = str_ireplace('Bearer ', '', $authorization);*/

          $result = array(
            'user_detail' => $user_detail,
          );
          $response = Response::json(array('code' => 200, 'message' => 'Subscription cancelled successfully.', 'cause' => '', 'data' => $result));

        } else {
          Log::info('is_subscription in incomplete mode else ');
          $is_active = $sub_result[0]->is_active;

          if ($is_active == 0) {
            return $response = Response::json(array('code' => 201, 'message' => 'Subscription already cancelled.', 'cause' => '', 'data' => json_decode("{}")));
          }

          $total_amount = $sub_result[0]->total_amount;
          $subscription_id = $sub_result[0]->paypal_id;

          \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

          $retrieve_subscription = \Stripe\Subscription::retrieve(
            $subscription_id
          );

          $canceled_date = $retrieve_subscription->canceled_at;
          $date = new \DateTime();
          $date->setTimestamp($canceled_date);
          $canceled_at = $date->format($datetimeFormat);

          $current_period_end = $retrieve_subscription->current_period_end; //only use for subscription is delete now [canceled_at]
          $date = new \DateTime();
          $date->setTimestamp($current_period_end);
          $current_period_end_date = $date->format($datetimeFormat);
          Log::info("retrieve_subscription :", [$retrieve_subscription]);

          //Check is subscription is already canceled in stripe and we didn't update in our server due to any issue then we do canceled process.
          if ($retrieve_subscription->status == "canceled" && $current_period_end_date >= $sub_result[0]->expiration_time) {
            $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');

            DB::beginTransaction();

            DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $canceled_at, $user_id]);

            DB::update('UPDATE subscriptions SET        
                              final_expiration_time = ?, is_expired = 1 WHERE 
                    user_id = ? AND transaction_id = ?', [$canceled_at, $user_id, $subscription_id]);

            DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);

            DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);

            DB::commit();

            (new UserController())->cancelSubscriptionByPaypalID($subscription_id, $payer_email);

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
            $result = array(
              'user_detail' => $user_detail,
            );

            $response = Response::json(array('code' => 200, 'message' => 'Subscription cancelled successfully.', 'cause' => '', 'data' => $result));
          } else {

//        If ($cancellation_type = 1) {
//          $subscription = $retrieve_subscription->cancel();
//          Log::info('cancelSubscription : Cancel subscription now : ',[$subscription]);
//        } else {
            $subscription = \Stripe\Subscription::update(
              $subscription_id,
              [
                'cancel_at_period_end' => true,
              ]
            );

            $status = $subscription->status;
            if ($status != "active") {
              return $response = Response::json(array('code' => 201, 'message' => 'Unable to cancel subscription. Please try again.', 'cause' => '', 'data' => json_decode("{}")));
            }
            $canceled_date = $subscription->canceled_at;
            $date = new \DateTime();
            $date->setTimestamp($canceled_date);
            $canceled_at = $date->format($datetimeFormat);

            //$current_period_end = $subscription->canceled_at; //right for live [current_period_end]
            $current_period_end = $subscription->current_period_end; //only use for subscrption is deletre now [canceled_at]
            $date = new \DateTime();
            $date->setTimestamp($current_period_end);
            $current_period_end_date = $date->format($datetimeFormat);

            $plan = $subscription->plan;
            $subscription_name = $plan->nickname;
            $currency = $plan->currency;

            $invoice_id = $subscription->latest_invoice;

            DB::beginTransaction();

            DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $current_period_end_date, $user_id]);

            DB::update('UPDATE subscriptions SET        
                              final_expiration_time = ? WHERE 
                    user_id = ? AND transaction_id = ?', [$current_period_end_date, $user_id, $subscription_id]);

            DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::commit();

            (new UserController())->cancelSubscriptionByPaypalID($subscription_id, $payer_email);

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

            /* get country code for set local time in email */
            $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
            $this->country_code = $billing_info->country_code;

            $cancellation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($canceled_at,$this->country_code);
            $expiration_date_local = (New ImageController())->convertUTCDateTimeInToLocal($current_period_end_date,$this->country_code);

            $template = 'cancel_subscription';
            $subject = 'PhotoADKing: Subscription Cancelled';
            $message_body = array(
              'message' => 'Your subscription cancelled successfully. Following are the subscription details.',
              'subscription_name' => $subscription_name,
              'txn_id' => $subscription_id,
              'txn_type' => 'Subscription[S]',
              'subscr_id' => $subscription_id,
              'total_amount' => $total_amount,
              'first_name' => $user_detail->first_name,
              'payment_received_from' => $user_detail->first_name . ' (' . $user_detail->email_id . ')',
              'payment_status' => 'Subscription cancelled',
              'payer_email' => $user_detail->email_id,
              'mc_currency' => $currency,
              'cancellation_date' => $canceled_at,
              'expiration_date' => $current_period_end_date,
              'cancellation_date_local' => $cancellation_date_local,
              'expiration_date_local' => $expiration_date_local
            );
            $api_name = 'paypalIpn';
            $api_description = 'subscription cancelled .';

//            Log::info('cancel_subscription mail data : ',['user_id'=>$user_id,'email_id'=>$user_detail->email_id,'subject'=>$subject,'message_body'=>$message_body]);
            $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));

            /*$requestHeaders = apache_request_headers();
            $authorization = isset($requestHeaders['Authorization']) ? $requestHeaders['Authorization'] : $requestHeaders['authorization'];
            $jwt_token = str_ireplace('Bearer ', '', $authorization);*/

            $result = array(
              'user_detail' => $user_detail,
            );
            $response = Response::json(array('code' => 200, 'message' => 'Subscription cancelled successfully.', 'cause' => '', 'data' => $result));
          }
        }
      }


    } catch (Exception $e) {
      (new ImageController())->logs("cancelStripeSubscription",$e);
//      //$this->dispatch(new EmailJob("NA", Config::get('constant.ADMIN_EMAIL_ID'), "Cancel subscription failed", $e->getMessage() . "---" , "NA", "cancelSubscription", "cancelSubscription"));
      Log::error("cancelStripeSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "cancelStripeSubscription",
          "occur from exception block",
          "unable to cancel subscription with stripe",
          $e->getMessage()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'cancel subscription.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  /*============== Create UpComing Invoice For change-Subscription ==============*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/createUpcomingInvoice",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="createUpcomingInvoice",
   *        summary="create Upcoming Invoice",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *   	  @SWG\Schema(
   *          required={"plan_id"},
   *          @SWG\Property(property="plan_id",  type="integer", example="1", description=""),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"The details of your next invoice have been successfully obtained.","cause":"","data":{"amount" = 17020,"unused_amount" = -2180,"remaining_amount" = 19200,"old_plan_id" = "3","new_plan_id" = "4","proration_date" = "2020-02-03 05:49:28"}}),),
   *        ),
   *      @SWG\Response(
   *            response=201,
   *            description="Running on multiple subscription",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"You are not subscriber.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 1",  type="object", example={"code":201,"message":"You are not a Stripe Subscriber, please update a subscription using PayPal.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 2",  type="object", example={"code":201,"message":"You are running on this same plan,please use a different plan to create a new invoice for the update subscription type.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 4",  type="object", example={"code":201,"message":"Subscription already cancelled.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 5",  type="object", example={"code":201,"message":"PhotoADKing is unable to get invoice details.","cause":"","data":"{}"}),),
   *        ),
   *      )
   *
   */
  Public function createUpcomingInvoice(Request $request_body){
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;
      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('plan_id'), $request)) != '')
        return $response;

      $datetimeFormat = 'Y-m-d H:i:s';
      $change_plan_id = $request->plan_id;
      $plan_id = (new VerificationController())->getPlanForIndia($change_plan_id,$user_id);
      $sub_result = DB::select('SELECT 
                                  sub.paypal_id, 
                                  sub.payment_type, 
                                  sub.subscr_type, 
                                  sub.final_expiration_time,  
                                  sub.is_active,  
                                  scd.customer_id, 
                                  scd.id AS db_customer_id 
                                  FROM subscriptions AS sub, 
                                  stripe_customer_details AS scd, 
                                  stripe_payment_status AS sps 
                                  WHERE sub.user_id = ? AND 
                                  sub.id = sps.subscription_id AND 
                                  sps.customer_id = scd.id
                                  ORDER BY sub.id DESC ', [$user_id]);
      if (count($sub_result) <= 0) {

        $response =  $response = Response::json(array('code' => 201, 'message' => 'You cannot preview the upcoming invoice for a canceled subscription..', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->payment_type != 2) {

        $response =  $response = Response::json(array('code' => 201, 'message' => 'You are not a Stripe Subscriber, please update a subscription using PayPal.', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->subscr_type == $plan_id) {

        $response =  $response = Response::json(array('code' => 201, 'message' => 'You are running on this same plan,please use a different plan to create a new invoice for the update subscription type.', 'cause' => '', 'data' => json_decode("{}")));

      } else {
        $is_active = $sub_result[0]->is_active;

//        if ($is_active == 0) {
//          return $response = Response::json(array('code' => 201, 'message' => 'Subscription already cancelled.', 'cause' => '', 'data' => json_decode("{}")));
//        }
        $subscription_id = $sub_result[0]->paypal_id;
        $proration_date = time();
        $customer_id = $sub_result[0]->customer_id;

        \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
        $subscription = \Stripe\Subscription::retrieve(
          $subscription_id
        );

        // See what the next invoice would look like with a plan switch
        // and proration set:
        $items = [
          [
            'id' => $subscription->items->data[0]->id,
            'plan' => $plan_id, # Switch to new plan
          ],
        ];

        $invoice = \Stripe\Invoice::upcoming([
          'customer' => $customer_id,
          'subscription' => $subscription_id,
          'subscription_items' => $items,
          'subscription_proration_date' => $proration_date,
          'subscription_billing_cycle_anchor' => 'now',
        ]);

//        Log::info('CreateUpcomingInvoice :', [$invoice]);
        // Calculate the proration cost:
        $cost = 0;
        $current_prorations = [];
        foreach ($invoice->lines->data as $line) {
          if ($line->period->start - $proration_date <= 1) {
            array_push($current_prorations, $line);
            $cost += $line->amount;
          }
        }
        $db_customer_id = $sub_result[0]->db_customer_id;
        $amount = $invoice->amount_due;
        $lines = $invoice->lines;
        $unused_amount_details = $lines->data[0];
        $remaining_amount_details = $lines->data[1];
        $unused_amount = $unused_amount_details->amount;
        $old_plan_id = $unused_amount_details->plan->id;
        $old_plan = $unused_amount_details->plan->nickname;
        $remaining_amount = $remaining_amount_details->amount;
        $new_plan_id = $remaining_amount_details->plan->id;
        $new_plan = $remaining_amount_details->plan->nickname;
        $total = $invoice->total / 100;

        $unused_amount= $unused_amount / 100;
        $remaining_amount= $remaining_amount / 100;
        $amount= $amount / 100;

        if(isset($invoice->discount)){
          $discount = $invoice->discount;
          $coupon = $discount->coupon;
          $coupon_id = $coupon->id;
          $percent_off = $coupon->percent_off;
          $remaining_amount = $remaining_amount * $percent_off / 100 ;
        }

        $sub_proration_date = $invoice->subscription_proration_date;
        if($proration_date == $sub_proration_date){
          $date = new \DateTime();
          $date->setTimestamp($sub_proration_date);
          $formated_proration_date = $date->format($datetimeFormat);
        }else{
          $date = new \DateTime();
          $date->setTimestamp($sub_proration_date);
          $formated_proration_date = $date->format($datetimeFormat);
        }

        $currency = $invoice->currency;
        $inv_created = $invoice->created;
        $date = new \DateTime();
        $date->setTimestamp($inv_created);
        $invoice_created = $date->format($datetimeFormat);

        $invoice_json_response = json_encode(json_decode(json_encode($invoice)));

        $invoice_array = array('customer_id' => $db_customer_id,
          'amount'=>$amount,
          'unused_amount'=>$unused_amount,
          'remaining_amount'=>$remaining_amount,
          'old_plan_id'=>$old_plan_id,
          'new_plan_id'=>$new_plan_id,
          'proration_date'=>$proration_date,
          'total'=>$total,
          'invoice_created'=>$invoice_created,
          'invoice_json_response'=>$invoice_json_response,
          'is_active'=>1);

        DB::beginTransaction();
        $upcoming_invoice_id = DB::table('stripe_proration_master')->insertGetId($invoice_array);
        DB::update('UPDATE stripe_subscription_master SET upcoming_invoice_id = ? WHERE subscription_id = ?',[$upcoming_invoice_id,$subscription_id]);
        DB::commit();

        $invoice_details = array(
          'amount' => $amount,
          'currency' => $currency,
          'unused_amount' => $unused_amount,
          'remaining_amount' => $remaining_amount,
          'old_plan' => $old_plan,
          'new_plan' => $new_plan,
          'proration_date' => $formated_proration_date
        );
        $response = Response::json(array('code' => 200, 'message' => 'The details of your next invoice have been successfully obtained.', 'cause' => '', 'data' => $invoice_details));
      }
    }catch (Exception $e) {
      (new ImageController())->logs("CreateUpcomingInvoice",$e);
      //$this->dispatch(new EmailJob("NA", Config::get('constant.ADMIN_EMAIL_ID'), "Cancel subscription failed", $e->getMessage() . "---" , "NA", "cancelSubscription", "cancelSubscription"));
//      Log::error("CreateUpcomingInvoice : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::rollBack();
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "createUpcomingInvoice",
          "occur from exception block",
          "unable to get invoice details with stripe",
          $e->getMessage()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get invoice details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  /*=================== Active or Resubscribe existing Subscription ========================*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/resubscribeStripeSubscription",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="resubscribeStripeSubscription",
   *        summary="resubscribe Stripe Subscription",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *   	  @SWG\Schema(
   *          required={"plan_id"},
   *          @SWG\Property(property="plan_id",  type="integer", example="1", description=""),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Your subscription is successfully activated.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4Njk4NzkwLCJleHAiOjE1NjkzMDM1OTAsIm5iZiI6MTU2ODY5ODc5MCwianRpIjoiVGlSVVY3VmMzQ0dCWWNmUCJ9.bq6uVaByVeLCxQNsLd3_RonXklSFK9sfe9qNx0PX7Ms","user_detail":{"user_id":31,"user_name":"steave@gmail.com","first_name":"Steave","email_id":"steave@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":1,"mailchimp_subscr_id":"b149f77a27357223db2104142cf13a6f","role_id":5,"create_time":"2019-02-22 05:14:22","update_time":"2019-02-22 05:14:24","subscr_expiration_time":"2019-10-17 05:40:11","next_billing_date":"2019-10-17 05:40:11","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
   *        ),
   *      @SWG\Response(
   *            response=201,
   *            description="Running on multiple subscription",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"You are not subscriber.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 1",  type="object", example={"code":201,"message":"You are not a Stripe Subscriber, please update a subscription using PayPal.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 2",  type="object", example={"code":201,"message":"You are not able to activate your subscription with a different plan, please use the same plan to activate your existing subscription.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 4",  type="object", example={"code":201,"message":"Subscription is already running with an active subscription plan.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 6",  type="object", example={"code":201,"message":"PhotoADKing is unable to get invoice details.","cause":"","data":"{}"}),),
   *        ),
   *      )
   *
   */
  Public function resubscribeStripeSubscription(Request $request_body)
  {
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;
      $payer_email = JWTAuth::toUser($token)->email_id;

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('plan_id'), $request)) != '')
        return $response;

      $actual_plan_id = $request->plan_id;

      $plan_id = (new VerificationController())->getPlanForIndia($actual_plan_id,$user_id);

      /* get country code for set local time in email */
      $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
      $this->country_code = $billing_info->country_code;

      $sub_result = DB::select('SELECT sub.id, 
                                        sub.paypal_id, 
                                        sub.payment_type, 
                                        sub.subscr_type, 
                                        sub.expiration_time, 
                                        sub.is_active, 
                                        sub.days_to_add, 
                                        sub.total_amount, 
                                        scd.customer_id, 
                                        scd.id AS db_customer_id 
                                        FROM subscriptions AS sub, 
                                          stripe_customer_details AS scd, 
                                          stripe_payment_status AS sps WHERE 
                                          sub.user_id = ? AND 
                                            sub.id = sps.subscription_id AND 
                                            sps.customer_id = scd.id AND 
                                            sub.is_active = 0 
                                          ORDER BY sub.id DESC ', [$user_id]);

      if (count($sub_result) <= 0) {

        $response = $response = Response::json(array('code' => 201, 'message' => 'You are not subscriber.', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->payment_type != 2) {

        $response = $response = Response::json(array('code' => 201, 'message' => 'You are not a Stripe Subscriber, please update a subscription using PayPal.', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->subscr_type != $plan_id) {

        $response = $response = Response::json(array('code' => 201, 'message' => 'You are not able to activate your subscription with a different plan, please use the same plan to activate your existing subscription.', 'cause' => '', 'data' => json_decode("{}")));

      } else {
        $is_active = $sub_result[0]->is_active;

        if ($is_active == 1) {
          return $response = Response::json(array('code' => 201, 'message' => 'Subscription is already running with an active subscription plan.', 'cause' => '', 'data' => json_decode("{}")));
        }
        $subscription_id = $sub_result[0]->paypal_id;
        $total_amount = $sub_result[0]->total_amount;
        $db_sub_id = $sub_result[0]->id;

        $subscription_array = $this->reActivatePlan($user_id, $subscription_id, $db_sub_id);

        $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
        if($subscription_array['status'] == "active"){
          $user_name = $user_detail->first_name;
          $template = "payment_successful";
          $subject = 'PhotoADKing: Subscription Plan Activated';
          $message = 'Your subscription plan activated successfully. Following are the transaction details.';
          $subscription_name = $subscription_array['plan_nickname'];
          $txn_id = $subscription_id;
          $subscr_id = $subscription_id;
          $first_name = $user_detail->first_name;
          $payment_received_from = $user_detail->email_id;
          $txn_type = 'Subscription[S]';
          $mc_currency = $subscription_array['currency'];
          $payment_status = $subscription_array['status'];
          $invoice_url = $subscription_array['invoice_url'];
          $email = $user_detail->email_id;
          $activation_date = $subscription_array['current_period_start'];
          $next_billing_date = $subscription_array['current_period_end'];
          $api_name = 'resubscribeStripeSubscription';
          $api_description = 'resubscribe a subscription.';
          //send mail of subscription successful
          $response = $this->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $total_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id,$this->country_code);
//          Log::info('response of mail : ',[$response]);
          if (!isset($response['success'])) {
            Log::error('unable to send mail to user');
          }

          /*$requestHeaders = apache_request_headers();
          $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/
          $response = Response::json(array('code' => 200, 'message' => 'Your subscription is successfully activated.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));

        }else{
          /*$requestHeaders = apache_request_headers();
          $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/
          $response = Response::json(array('code' => 436, 'message' => 'Thank you, We have received your payment request and will respond within 24 hours.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));
        }

      }
    } catch (Exception $e) {
      (new ImageController())->logs("resubscribeStripeSubscription",$e);
      //$this->dispatch(new EmailJob("NA", Config::get('constant.ADMIN_EMAIL_ID'), "Cancel subscription failed", $e->getMessage() . "---" , "NA", "cancelSubscription", "cancelSubscription"));
//      Log::error("resubscribeStripeSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "changeStripeSubscription",
          "occur from exception block",
          "unable to change subscription with stripe",
          $e->getMessage()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'active subscription.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  function reActivatePlan($user_id, $subscription_id, $db_sub_id)
  {
    try {

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

      $subscription = \Stripe\Subscription::retrieve($subscription_id);
//      Log::info("reActivatePlan :", [$subscription]);

      $datetimeFormat = 'Y-m-d H:i:s';
      $canceled_date = $subscription->canceled_at;
      $date = new \DateTime();
      $date->setTimestamp($canceled_date);
      $canceled_at = $date->format($datetimeFormat);

      $current_period_end = $subscription->current_period_end; //only use for subscription is delete now [canceled_at]
      $date = new \DateTime();
      $date->setTimestamp($current_period_end);
      $current_period_end_date = $date->format($datetimeFormat);

      $sub_result = DB::select('SELECT final_expiration_time,expiration_time FROM subscriptions WHERE transaction_id= ?',[$subscription_id]);

      //Check is subscription is already canceled in stripe and we didn't update in our server due to any issue then we do canceled process.
      /*******************************
       * This type of case not happen (it happen in rare case)
       * We set is_active = 0 is_expired = 1 and change user role to free and expire user session (In short Canceled subscription)
       * This happen in live one time (jan-2021) then we apply solution when user cancel subscription and when stripe call subscription delete webhook so ,From now this case not raise
       *********************************/
      if ($subscription->status == "canceled" &&  $current_period_end_date >= $sub_result[0]->expiration_time) {
        $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');

        DB::beginTransaction();

        DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $canceled_at, $user_id]);

        DB::update('UPDATE subscriptions SET        
                              final_expiration_time = ?, is_expired = 1 WHERE 
                    user_id = ? AND transaction_id = ?', [$canceled_at, $user_id, $subscription_id]);

        DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$user_id]);

        DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

        DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

        DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);

        DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);

        DB::commit();

        (new UserController())->cancelSubscriptionByPaypalID($subscription_id);

        $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
        $result = array(
          'user_detail' => $user_detail,
        );

        return Response::json(array('code' => 200, 'message' => 'Subscription already canceled.', 'cause' => '', 'data' => $result));
      }else{
        $update_subscription = \Stripe\Subscription::update($subscription_id, [
          'cancel_at_period_end' => false,
        ]);
//      Log::info('subscription successfully activate : ',[$update_subscription]);
        $subscription_id = $update_subscription->id;
        $status = $update_subscription->status;
        $plan = $update_subscription->plan;
        $plan_nickname = $plan->nickname;
        $currency = $plan->currency;
        $amount = $plan->amount / 100;

        if (isset($subscription->discount)) {
          $discount = $subscription->discount;
          if (isset($discount->coupon)) {
            $coupon = $discount->coupon;
            $percent_off = $coupon->percent_off;
            $amount = $amount * $percent_off / 100;
          }
        }

        $customer_id = $update_subscription->customer;
        $invoice_id = $update_subscription->latest_invoice;
        $invoice = \Stripe\Invoice::retrieve(
          $invoice_id
        );
        $hosted_invoice_url = $invoice->hosted_invoice_url;

        $datetimeFormat = 'Y-m-d H:i:s';
        $current_period_start = $subscription->current_period_start;
        $date = new \DateTime();
        $date->setTimestamp($current_period_start);
        $current_period_start = $date->format($datetimeFormat);

        $current_period_end = $subscription->current_period_end;
        $date = new \DateTime();
        $date->setTimestamp($current_period_end);
        $current_period_end = $date->format($datetimeFormat);

        if ($status == "active") {

          $payment_method_id = DB::select('SELECT sps.payment_method_id FROM stripe_payment_status as sps , stripe_subscription_master AS ssm 
                                          WHERE ssm.user_id = ? AND ssm.subscription_id = ? AND ssm.id = sps.stripe_subscription_id', [$user_id, $subscription_id]);

          DB::beginTransaction();

          $is_stripe_payment_status = DB::update('UPDATE stripe_payment_status SET is_active = ? WHERE user_id = ? AND subscription_id = ?', [1, $user_id, $db_sub_id]);

          $is_subscriptions = DB::update('UPDATE subscriptions SET
                              final_expiration_time = NULL,
                              cancellation_date = NULL,
                              remaining_days= NULL,
                              response_message= NULL,
                              is_active= 1
                              WHERE
                    user_id = ? AND 
                    is_active = 0 AND 
                    transaction_id = ? ', [$user_id, $subscription_id]);

          $is_stripe_payment_method_details = DB::update('UPDATE stripe_payment_method_details SET is_active = 1 WHERE user_id = ? AND id = ?', [$user_id, $payment_method_id[0]->payment_method_id]);

          $is_stripe_subscription_master = DB::update('UPDATE stripe_subscription_master SET is_active = 1 WHERE user_id = ? AND subscription_id = ? ', [$user_id, $subscription_id]);

          $is_stripe_customer_details = DB::update('UPDATE stripe_customer_details SET is_active = 1 WHERE user_id = ? AND customer_id = ?', [$user_id, $customer_id]);

          $payment_status_master = DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [1, $subscription_id]);

          DB::commit();

//        Log::info('data : ',['stripe_payment_status'=> $is_stripe_payment_status,'subscriptions'=>$is_subscriptions,'stripe_subscription_master'=>$is_stripe_subscription_master,'stripe_customer_details'=>$is_stripe_customer_details,'payment_status_master'=>$payment_status_master,'stripe_payment_method_details'=>$is_stripe_payment_method_details]);

          return array('plan_nickname' => $plan_nickname,
            'plan_amount' => $amount,
            'currency' => $currency,
            'current_period_start' => $current_period_start,
            'current_period_end' => $current_period_end,
            'invoice_url' => $hosted_invoice_url,
            'status' => $status);

        } else {
          return array('status' => $status);
        }
      }
    } catch (Exception $e) {
      (new ImageController())->logs("reActivatePlan",$e);
      //$this->dispatch(new EmailJob("NA", Config::get('constant.ADMIN_EMAIL_ID'), "Cancel subscription failed", $e->getMessage() . "---" , "NA", "cancelSubscription", "cancelSubscription"));
//      Log::error("reActivatePlan : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "reActivatePlan",
          "occur from exception block",
          "unable to reactivate subscription with stripe",
          $e->getMessage()]);
      //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'active subscription.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
  }

  /*=================== Upgrade & Downgrade Subscription ========================*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/changeStripeSubscription",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="changeStripeSubscription",
   *        summary="change Stripe Subscription",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *   	  @SWG\Schema(
   *          required={"plan_id"},
   *          @SWG\Property(property="plan_id",  type="integer", example="1", description=""),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Thank you, your payment was successful.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4Njk4NzkwLCJleHAiOjE1NjkzMDM1OTAsIm5iZiI6MTU2ODY5ODc5MCwianRpIjoiVGlSVVY3VmMzQ0dCWWNmUCJ9.bq6uVaByVeLCxQNsLd3_RonXklSFK9sfe9qNx0PX7Ms","user_detail":{"user_id":31,"user_name":"steave@gmail.com","first_name":"Steave","email_id":"steave@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":1,"mailchimp_subscr_id":"b149f77a27357223db2104142cf13a6f","role_id":5,"create_time":"2019-02-22 05:14:22","update_time":"2019-02-22 05:14:24","subscr_expiration_time":"2019-10-17 05:40:11","next_billing_date":"2019-10-17 05:40:11","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
   *        ),
   *      @SWG\Response(
   *            response=201,
   *            description="Running on multiple subscription",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"You are not subscriber.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 1",  type="object", example={"code":201,"message":"You are not a Stripe Subscriber, please update a subscription using PayPal.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 2",  type="object", example={"code":201,"message":"You are running on this same plan,please use a different plan to create a new invoice for the update subscription type.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 4",  type="object", example={"code":201,"message":"Subscription already cancelled.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 5",  type="object", example={"code":201,"message":"Please first find out the details of the proration amount, then update your subscription plan.","cause":"","data":"{}"}),
   *          @SWG\Property(property="Sample Response 6",  type="object", example={"code":201,"message":"PhotoADKing is unable to get invoice details.","cause":"","data":"{}"}),),
   *        ),
   *      )
   *
   */
  Public function changeStripeSubscription(Request $request_body)
  {
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('plan_id'), $request)) != '')
        return $response;

      $actual_plan_id = $request->plan_id;

      $plan_id = (new VerificationController())->getPlanForIndia($actual_plan_id,$user_id);

      /* get country code for set local time in email */
      $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
      $this->country_code = $billing_info->country_code;

      $sub_result = DB::select('SELECT sub.id, 
                                        sub.paypal_id, 
                                        sub.payment_type, 
                                        sub.subscr_type, 
                                        sub.expiration_time, 
                                        sub.is_active, 
                                        sub.days_to_add, 
                                        sub.total_amount, 
                                        scd.customer_id, 
                                        sps.require_3D_secure, 
                                        scd.id AS db_customer_id
                                        FROM subscriptions AS sub, 
                                          stripe_customer_details AS scd, 
                                          stripe_payment_status AS sps WHERE 
                                          sub.user_id = ? AND 
                                            sub.id = sps.subscription_id AND 
                                            sps.customer_id = scd.id 
                                          ORDER BY sub.id DESC ', [$user_id]);

      if (count($sub_result) <= 0) {

        $response =  $response = Response::json(array('code' => 201, 'message' => 'You are not subscriber.', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->payment_type != 2) {

        $response =  $response = Response::json(array('code' => 201, 'message' => 'You are not a Stripe Subscriber, please update a subscription using PayPal.', 'cause' => '', 'data' => json_decode("{}")));

      } elseif ($sub_result[0]->subscr_type == $plan_id) {

        $response =  $response = Response::json(array('code' => 201, 'message' => 'You are running on this same plan,please use a different plan to create a new invoice for the update subscription type.', 'cause' => '', 'data' => json_decode("{}")));

      }  else {
        $is_active = $sub_result[0]->is_active;


        $subscription_id = $sub_result[0]->paypal_id;
        $db_row_id = $sub_result[0]->id;
        $subscr_type = $sub_result[0]->subscr_type;
        $expiration_time = $sub_result[0]->expiration_time;
        $old_amount = $sub_result[0]->total_amount;
        $customer_id = $sub_result[0]->customer_id;
        $db_sub_id = $sub_result[0]->id;
        $db_customer_id = $sub_result[0]->db_customer_id;
        $client_secret = $sub_result[0]->require_3D_secure;

        if ($is_active == 1) {

          $proration_details = DB::select('SELECT proration_date
                             FROM stripe_proration_master AS spm, stripe_subscription_master AS ssp WHERE 
                             spm.customer_id = ? AND spm.is_active = 1 AND spm.id = ssp.upcoming_invoice_id AND ssp.subscription_id = ? AND ssp.is_active = 1',[$db_customer_id,$subscription_id]);

//          Log::info('proration of active user: ',['proration'=>$proration_details,'db_customer_id'=>$db_customer_id]);
          if(count($proration_details) <= 0){
            return Response::json(array('code' => 201, 'message' => 'Please first find out the details of the proration amount, then update your subscription plan.', 'cause' => '', 'data' => json_decode("{}")));
          }
          $is_active_user = 1;
          $proration_date = $proration_details[0]->proration_date;

          $subscription_array = $this->upgradeOrDowngradePlan($plan_id,$proration_date,$subscription_id,$user_id,$db_sub_id,$db_customer_id,$is_active_user,$client_secret);


        }else{

          $proration_details = DB::select('SELECT proration_date
                             FROM stripe_proration_master AS spm, stripe_subscription_master AS ssp WHERE 
                             spm.customer_id = ? AND spm.is_active = 1 AND spm.id = ssp.upcoming_invoice_id AND ssp.subscription_id = ? AND ssp.is_active = 0',[$db_customer_id,$subscription_id]);

//          Log::info('proration of deactive user : ',['proration'=>$proration_details,'db_customer_id'=>$db_customer_id]);
          if(count($proration_details) <= 0){
            return Response::json(array('code' => 201, 'message' => 'Please first find out the details of the proration amount, then update your subscription plan.', 'cause' => '', 'data' => json_decode("{}")));
          }
          $is_active_user = 0;
          $proration_date = $proration_details[0]->proration_date;

          $subscription_array = $this->upgradeOrDowngradePlan($plan_id,$proration_date,$subscription_id,$user_id,$db_sub_id,$db_customer_id,$is_active_user,$client_secret);

        }

        DB::beginTransaction();
        DB::update('UPDATE stripe_proration_master SET is_active = 0 WHERE customer_id = ?',[$db_customer_id]);
        DB::commit();

        if (isset($subscription_array['error_status']) == 2) {
          return Response::json(array('code' => 444, 'message' => 'Subscriptions require 3D secure authentication.', 'cause' => '', 'data' => array("client_secret"=>$subscription_array['client_secret'],"invoice_url"=>$subscription_array['hosted_invoice_url'])));
        }

        $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
        if ($subscription_array['status'] == "active") {

          $user_name = $user_detail->first_name;
          $template = "payment_successful";
          $subject = 'PhotoADKing: Subscription Plan Changed';
          $message = 'Your subscription plan changed successfully. Remaining amount are lefter into your new subscription. Following are the transaction details.';
          $subscription_name = $subscription_array['plan_nickname'];
          $txn_id = $subscription_array['txn_id'];
          $subscr_id = $subscription_id;
          $first_name = $user_detail->first_name;
          $payment_received_from = $user_detail->email_id;
          $txn_type = 'Subscription[S]';
          $mc_currency = $subscription_array['currency'];
          $total_amount = $subscription_array['plan_amount'];
          $payment_status = $subscription_array['status'];
          $invoice_url = $subscription_array['invoice_url'];
          $email = $user_detail->email_id;
          $activation_date = $subscription_array['current_period_start'];
          $next_billing_date = $subscription_array['current_period_end'];
          $api_name = 'stripeUserPayment';
          $api_description = 'subscribe a new subscription.';
          //send mail of subscription successful
          $response = $this->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $total_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id,$this->country_code);
          if (!isset($response['success'])) {
            Log::error('unable to send mail to user');
          }

          $activation_date_local = (New ImageController())->convertUTCDateTimeInToLocal( $subscription_array['current_period_start'],$this->country_code);
          $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($subscription_array['current_period_end'],$this->country_code);

          $template = 'payment_successful';
          $subject = 'PhotoADKing: Payment Received';
          $message_body = array(
            'message' => 'Your payment received successfully. Following are the transaction details.',
            'subscription_name' => $subscription_name,
            'txn_id' => $subscription_id,
            'txn_type' => 'Subscription[S]',
            'subscr_id' => $subscription_id,
            'first_name' => $user_detail->first_name,
            'payment_received_from' => $user_detail->first_name . '(' . $user_detail->email_id . ')',
            'total_amount' => $total_amount,
            'mc_currency' => $mc_currency,
            'payer_email' => $user_detail->email_id,
            'payment_status' => $payment_status,
            'activation_date' => $subscription_array['current_period_start'],
            'next_billing_date' => ($subscription_array['current_period_end'] != NULL) ? $subscription_array['current_period_end'] : 'NA',
            'activation_date_local' => $activation_date_local,
            'next_billing_date_local' => ($next_billing_date_local != NULL) ? $next_billing_date_local : ''
          );
          $api_name = 'stripeUserPayment';
          $api_description = 'subscribe a new subscription.';
          $this->dispatch(new EmailJob($user_id, $email, $subject, $message_body, $template, $api_name, $api_description));

          /*$requestHeaders = apache_request_headers();
          $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/

          $response = Response::json(array('code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));
        }else{

          /*$requestHeaders = apache_request_headers();
          $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/
          $response = Response::json(array('code' => 436, 'message' => 'Thank you, We have received your payment request and will respond within 24 hours.', 'cause' => '', 'data' => ['user_detail' => $user_detail]));

        }
      }
    } catch (Exception $e) {
      (new ImageController())->logs("changeStripeSubscription",$e);
      //$this->dispatch(new EmailJob("NA", Config::get('constant.ADMIN_EMAIL_ID'), "Cancel subscription failed", $e->getMessage() . "---" , "NA", "cancelSubscription", "cancelSubscription"));
//      Log::error("changeStripeSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::insert('INSERT INTO stripe_error_master(user_id,api_name,error_type,description,error_json_response) VALUES(?, ?, ?, ?, ?)',
        [$user_id,
          "changeStripeSubscription",
          "occur from exception block",
          "unable to change subscription with stripe",
          $e->getMessage()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change subscription.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  public function upgradeOrDowngradePlan($plan_id,$proration_date,$update_subscription_id,$user_id,$db_sub_id,$db_customer_id,$is_active_user,$client_secret)
  {

    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
    $retrieve_subscription = \Stripe\Subscription::retrieve(
      $update_subscription_id
    );

    $subscription = \Stripe\Subscription::update($update_subscription_id, [
      'cancel_at_period_end' => false,
      'billing_cycle_anchor' => 'now',
      'items' => [
        [
          'id' => $retrieve_subscription->items->data[0]->id,
          'plan' => $plan_id,
        ],
      ],
      'proration_date' => $proration_date,
    ]);
//    Log::info('subscription_update',[$subscription]);

    $subscription_id = $subscription->id;
    $invoice_id = $subscription->latest_invoice;
    $invoice = \Stripe\Invoice::retrieve(
      $invoice_id
    );

    $hosted_invoice_url = $invoice->hosted_invoice_url;
    $status = $subscription->status;
    if($client_secret != '' && $status != "active"){
      DB::beginTransaction();
      DB::update('UPDATE stripe_payment_status SET is_secure_user_active = ? ,authenticate_url = ? WHERE require_3D_secure = ?',[$is_active_user,$hosted_invoice_url,$client_secret]);
      DB::update('UPDATE stripe_subscription_master set invoice_id = ? WHERE subscription_id = ?',[$invoice_id,$subscription_id]);
      DB::commit();
      return $requires_action_array = array("error_status"=>2,"client_secret"=>$client_secret,"hosted_invoice_url"=>$hosted_invoice_url);
    }


    if($update_subscription_id != $subscription_id){
      Log::error('upgradeOrDowngradePlan : Subscription does not match',['old_sub_id'=>$update_subscription_id ,'new_sub_id'=>$subscription_id]);
      return Response::json(array('code' => 201, 'message' => 'Subscription does not match.', 'cause' => '', 'data' => json_decode("{}")));
    }



    $billing_cycle_anchor = $subscription->billing_cycle_anchor;
    $datetimeFormat = 'Y-m-d H:i:s';
    $date = new \DateTime();
    $date->setTimestamp($billing_cycle_anchor);
    $billing_cycle_anchor = $date->format($datetimeFormat);

    $current_period_start = $subscription->current_period_start;
    $date = new \DateTime();
    $date->setTimestamp($current_period_start);
    $current_period_start = $date->format($datetimeFormat);

    $current_period_end = $subscription->current_period_end;
    $date = new \DateTime();
    $date->setTimestamp($current_period_end);
    $current_period_end = $date->format($datetimeFormat);


    $cancel_at = $subscription->cancel_at;
    If($cancel_at != ''){
      $date = new \DateTime();
      $date->setTimestamp($cancel_at);
      $cancel_at = $date->format($datetimeFormat);
    }

    $items = $subscription->items;
    $data = $items->data;
    $txn_id = $data[0]->id;
    $data_plan = $data[0]->plan;
    $plan_id = $data_plan->id;

    $plan_amount = $data_plan->amount / 100;
    if(isset($subscription->discount)){
      $discount = $subscription->discount;
      if(isset($discount->coupon)){
        $coupon = $discount->coupon;
        $percent_off = $coupon->percent_off;
        $plan_amount = $plan_amount * $percent_off / 100 ;
      }
    }


    $plan_interval = $data_plan->interval;
    $plan_nickname = $data_plan->nickname;
    $plan_product = $data_plan->product;
    $currency = $data_plan->currency;

    $created = $subscription->created;
    $date = new \DateTime();
    $date->setTimestamp($created);
    $created = $date->format($datetimeFormat);

    $subscription_json_response = json_encode(json_decode(json_encode($subscription)));

    if ($status == "active") {
      $paypal_status = 1;
      $is_active = 1;
      $subscr_type = $plan_id;

      (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);
      $expiration_time = $current_period_end;
    } elseif($status ==  "incomplete") {

      if(isset($payment_intent)){
        $status = $payment_intent->status;
        if ($status == "requires_action"){
          $client_secret = $payment_intent->client_secret;
          $requires_action_array = array("error_status"=>1,"client_secret"=>$client_secret);
        }
      }else{
        Log::error('Payment intent is not settled with INCOMPLETE status of subscription');
      }
    }  else {
      Log::error('upgradeOrDowngradePlan : subscription status un-define ',[$status]);
    }



    DB::beginTransaction();

    if($is_active_user == 1){

//      Log::error('update subscription of active user');

      //stripe_subscription_master
      DB::update('UPDATE stripe_subscription_master
                                                            set invoice_id = ?, 
                                                            plan_id = ?,
                                                            nick_name = ?,
                                                            product = ?,
                                                            amount = ?,
                                                            plan_interval = ?,
                                                            cancel_at = ?,
                                                            hosted_invoice_url = ?, 
                                                            subscription_json_response = ?,
                                                            created = ?
                                                            WHERE user_id = ? AND 
                                                            subscription_id = ?',[$invoice_id,
        $plan_id,
        $plan_nickname,
        $plan_product,
        $plan_amount,
        $plan_interval,
        $cancel_at,
        $hosted_invoice_url,
        $subscription_json_response,
        $created,
        $user_id,
        $subscription_id]);

      //subscriptions "Stripe_Payment"
      DB::update('UPDATE subscriptions SET subscr_type = ?,
                                                payment_status = ?,
                                                total_amount = ?,
                                                paypal_response = ?,
                                                payment_date = ?,
                                                activation_time = ?,
                                                expiration_time = ?,
                                                cancellation_date = ?
                                                WHERE user_id = ?
                                                 AND transaction_id = ?
                                                 AND is_active = 1',
        [$plan_id,
          $status,
          $plan_amount,
          $subscription_json_response,
          $billing_cycle_anchor,
          $current_period_start,
          $current_period_end,
          $cancel_at,
          $user_id,
          $subscription_id]);

      if ($status == "active") {

        //payment_status_master
        DB::update('UPDATE payment_status_master SET
                          paypal_status = ?,
                          paypal_payment_status = ?,
                          paypal_response = ?,
                          expiration_time = ?,
                          is_active = ?
                          WHERE user_id = ? AND txn_id = ? AND is_active = 1',
          [
            $paypal_status,
            $status,
            $subscription_json_response,
            $expiration_time,
            $is_active,
            $user_id,
            $subscription_id
          ]);
      }else{
        //payment_status_master
        DB::update('UPDATE payment_status_master SET
                          paypal_payment_status = ?,
                          paypal_response = ?,
                          is_active = ?
                          WHERE user_id = ? AND txn_id = ? AND is_active = 1',
          [
            $status,
            $subscription_json_response,
            0,
            $user_id,
            $subscription_id
          ]);
      }

    }else{

//      Log::error('reactive subscription of deactive user');
      //stripe_subscription_master
      DB::update('UPDATE stripe_subscription_master
                                                            set invoice_id = ?, 
                                                            plan_id = ?,
                                                            nick_name = ?,
                                                            product = ?,
                                                            amount = ?,
                                                            plan_interval = ?,
                                                            cancel_at = ?,
                                                            hosted_invoice_url = ?, 
                                                            created = ?,
                                                            is_active = 1 
                                                            WHERE user_id = ? AND 
                                                            subscription_id = ?',[$invoice_id,
        $plan_id,
        $plan_nickname,
        $plan_product,
        $plan_amount,
        $plan_interval,
        $cancel_at,
        $hosted_invoice_url,
        $subscription_json_response,
        $created,
        $user_id,
        $subscription_id]);

      //subscriptions "Stripe_Payment"
      DB::update('UPDATE subscriptions SET subscr_type = ?,
                                                payment_status = ?,
                                                total_amount = ?,
                                                paypal_response = ?,
                                                payment_date = ?,
                                                activation_time = ?,
                                                expiration_time = ?,
                                                cancellation_date = ?,
                                                is_active = 1
                                                WHERE user_id = ?
                                                 AND transaction_id = ?',
        [$plan_id,
          $status,
          $plan_amount,
          $subscription_json_response,
          $billing_cycle_anchor,
          $current_period_start,
          $current_period_end,
          $cancel_at,
          $user_id,
          $subscription_id]);


      if ($status == "active") {
        //payment_status_master
        DB::update('UPDATE payment_status_master SET
                          paypal_status = ?,
                          paypal_payment_status = ?,
                          paypal_response = ?,
                          expiration_time = ?,
                          is_active = ?
                          WHERE user_id = ? AND txn_id = ?',
          [
            $paypal_status,
            $status,
            $subscription_json_response,
            $expiration_time,
            $is_active,
            $user_id,
            $subscription_id
          ]);
      }else{
        //payment_status_master
        DB::update('UPDATE payment_status_master SET
                          paypal_payment_status = ?,
                          paypal_response = ?,
                          is_active = ?
                          WHERE user_id = ? AND txn_id = ? AND is_active = 1',
          [
            $status,
            $subscription_json_response,
            0,
            $user_id,
            $subscription_id
          ]);
      }


      $payment_method_id = DB::select('SELECT sps.payment_method_id FROM stripe_payment_status as sps , 
                                                                        stripe_subscription_master AS ssm 
                                                        WHERE ssm.user_id = ? 
                                                        AND ssm.subscription_id = ? 
                                                        AND ssm.id = sps.stripe_subscription_id',
        [$user_id,$subscription_id]);

      DB::update('UPDATE stripe_payment_method_details SET is_active = 1 WHERE user_id = ? AND id = ?',[$user_id,$payment_method_id[0]->payment_method_id]);

      DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [1, $subscription_id]);

      DB::update('UPDATE stripe_customer_details SET is_active = 1 WHERE user_id = ? AND id = ?',[$user_id,$db_customer_id]);

      DB::update('UPDATE stripe_payment_status SET is_active = ? WHERE user_id = ? AND subscription_id = ?', [1, $user_id, $db_sub_id]);

    }


    DB::commit();

      $subscription_return = array('plan_nickname' => $plan_nickname,
      'plan_amount' => $plan_amount,
      'txn_id' => $txn_id,
      'currency' => $currency,
      'current_period_start' => $current_period_start,
      'current_period_end' => $current_period_end,
      'invoice_url' => $hosted_invoice_url,
      'status' => $status);

    if(isset($requires_action_array)){
      $requires_action_array = array_merge($requires_action_array,$subscription_return);
      return $requires_action_array;
    }

    return $subscription_return;

  }


  /*============================== Functions =================================*/

  //Use for send stripe mails
  public function SubscriptionMail($user_id,$user_name,$template,$subject,$message,$subscription_name,$txn_type,$subscr_id,$first_name,$payment_received_from,$total_amount,$mc_currency,$email,$payment_status,$activation_date,$next_billing_date,$api_name,$api_description,$invoice_url,$txn_id,$country_code =""){
    try{

      if($country_code) {
        $activation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($activation_date,$country_code);
        $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($next_billing_date,$country_code);
      }else{
        $activation_date_local="";
        $next_billing_date_local="";
      }

      $message_body = array(
        'message' => $message,
        'subscription_name' => $subscription_name,
        isset($txn_id) && ($txn_id != "") ? 'txn_id => '.$txn_id : '',
        'txn_type' => $txn_type,
        'subscr_id' => $subscr_id,
        'first_name' => $first_name,
        'payment_received_from' => $user_name . ' (' . $payment_received_from . ')',
        'total_amount' => $total_amount,
        'mc_currency' => $mc_currency,
        'payer_email' => $email,
        'payment_status' => $payment_status,
        'activation_date' => $activation_date,
        'next_billing_date' => ($next_billing_date != NULL) ? $next_billing_date : 'NA',
        'activation_date_local' => $activation_date_local,
        'next_billing_date_local' => ($next_billing_date_local != NULL) ? $next_billing_date_local : '',
        'invoice_url' => ($invoice_url != NULL) ? $invoice_url : 'NA'
      );
//      Log::info("SubscriptionMail message_body : ",[$message_body]);
      $this->dispatch(new EmailJob($user_id, $email, $subject, $message_body, $template, $api_name, $api_description));

      return array('success'=>1);
    }catch (Exception $e) {
      (new ImageController())->logs("CreateUpcomingInvoice",$e);
      //$this->dispatch(new EmailJob("NA", Config::get('constant.ADMIN_EMAIL_ID'), "Cancel subscription failed", $e->getMessage() . "---" , "NA", "cancelSubscription", "cancelSubscription"));
      Log::error("CreateUpcomingInvoice : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      return array('error code'=>'unable to send mail with 4##');
    }
  }

  /*=============================== Change status of user ====================================*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/getPaymentStatusForUser",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="getPaymentStatusForUser",
   *        summary="get Payment Status For User",
   *        produces={"application/json"},
   *     @SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *       in="body",
   *        name="request_body",
   *   	  @SWG\Schema(
   *          required={"payment_response"},
   *          @SWG\Property(property="payment_response",  type="object", example="{}", description=""),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="Success",
   *        @SWG\Schema(
   *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Profile fetched successfully.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4Njk4NzkwLCJleHAiOjE1NjkzMDM1OTAsIm5iZiI6MTU2ODY5ODc5MCwianRpIjoiVGlSVVY3VmMzQ0dCWWNmUCJ9.bq6uVaByVeLCxQNsLd3_RonXklSFK9sfe9qNx0PX7Ms","user_detail":{"user_id":31,"user_name":"steave@gmail.com","first_name":"Steave","email_id":"steave@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":1,"mailchimp_subscr_id":"b149f77a27357223db2104142cf13a6f","role_id":5,"create_time":"2019-02-22 05:14:22","update_time":"2019-02-22 05:14:24","subscr_expiration_time":"2019-10-17 05:40:11","next_billing_date":"2019-10-17 05:40:11","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
   *        ),
   *      @SWG\Response(
   *            response=201,
   *            description="Running on multiple subscription",
   *        @SWG\Schema(
   *     @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get user profile.","cause":"","data":"{}"}),),
   *        ),
   *      )
   *
   */
  Public function getPaymentStatusForUser(Request $request_body)
  {
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('payment_response'), $request)) != '')
        return $response;

      $payment_response = $request->payment_response;
//      Log::info('getPaymentStatusByUser :3D secure -> font-end response of payment methods :',[$payment_response]);
      $status = $payment_response->status;
//      Log::info('data : ',[$status]);
      if($status == 'succeeded'){

        $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
        $role_id = $user_detail->role_id;
        if($role_id == 2){
          $response = Response::json(array('code' => 436, 'message' => 'Thank you, We have received your payment request and will respond within 24 hours.', 'cause' => '', 'data' => $user_detail));
        }else{
          $response = Response::json(array('code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => $user_detail));
        }
      }elseif($status == 'requires_payment_method'){

        $last_payment_error = $payment_response->last_payment_error;
        $message = $last_payment_error->message;
//        Log::info('requires_payment_method',[$message]);

        ////////////////////////////////////////////////////////
        $sub_result =  DB::select('SELECT
                           id,
                      transaction_id,
                      total_amount,
                      paypal_id,
                      payment_type,
                      payment_status,
                      is_active
                      FROM subscriptions
                      WHERE user_id = ? AND is_active = 0
                      ORDER BY id DESC', [$user_id]);
//        Log::info('$subscription_id',[$sub_result]);
        $subscription_id = $sub_result[0]->transaction_id;
        $id = $sub_result[0]->id;


        \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

        $retrieve_subscription = \Stripe\Subscription::retrieve(
          $subscription_id
        );
//        Log::info('cancelSubscription : retrieve subscription : ',[$retrieve_subscription]);

        $subscription = $retrieve_subscription->cancel();
//        Log::info('cancelSubscription : cancel subscription : ',[$subscription]);

        DB::beginTransaction();

        DB::update('UPDATE stripe_payment_status SET error_message = ? WHERE subscription_id = ?',[$message,$id]);

        DB::delete('DELETE FROM stripe_payment_status WHERE is_active = 0 AND user_id = ?', [$user_id]);

        DB::delete('DELETE FROM subscriptions WHERE
                    user_id = ? AND transaction_id = ?', [$user_id,$subscription_id]);

        DB::delete('DELETE FROM stripe_subscription_master WHERE user_id = ? AND is_active = 0',[$user_id]);

        DB::delete('DELETE FROM stripe_payment_method_details WHERE user_id = ? AND is_active = 0',[$user_id]);

        DB::delete('DELETE FROM stripe_customer_details WHERE user_id = ? AND is_active = 0',[$user_id]);

        DB::delete('DELETE FROM payment_status_master WHERE user_id = ? AND txn_id = ?',[$user_id,$subscription_id]);

        DB::commit();

        /// ///////////////////////////////////////////////////
        $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
        $response = Response::json(array('code' => 436, 'message' => $message, 'cause' => '', 'data' => $user_detail));
      }else{
        $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
        $last_payment_error = $payment_response->last_payment_error;
        $message = $last_payment_error->message;
        Log::info('unknown : ',['status' => $status,'msg' => $message]);
        $response = Response::json(array('code' => 436, 'message' => 'Thank you, We have received your payment request and will respond within 24 hours.', 'cause' => '', 'data' => $user_detail));
      }

//      Log::info('getPaymentStatusByUser :3D secure -> user details :',[$user_detail]);
    } catch (Exception $e) {
      (new ImageController())->logs("getUserProfile",$e);
//      Log::error('getUserProfile  : ', ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get user profile.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }


  /*================================= Extra ==================================*/
  /*
  public function runScheduler()
  {
    try {
      $this->dispatch(new SubscriptionExpireSchedule());
    } catch (Exception $e) {
      Log::error("AndroidReviewCommand.php() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
    return 1;
  }

  public function DbBackupJob()
  {
    try {
      $this->dispatch(new DbBackupJob());
    } catch (Exception $e) {
      Log::error("AndroidReviewCommand.php() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
    return 1;
  }

  */
  public function CreateProduct(Request $request_body){

    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      //Log::info("request data :", [$request]);
      if (($response = (new VerificationController())->validateRequiredParameter(array('name','type'), $request)) != '')
        return $response;

      $name = trim($request->name);
      $type = trim($request->type);

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
      \Stripe\Product::create([
        'name' => $name,
        'type' => $type,
      ]);

      DB::beginTransaction();
      DB::insert('INSERT INTO stripe_product_master (name,type) VALUES(?,?)', [$name, $type]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Stripe product created successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-CreateProduct",$e);
//      Log::error("Stripe-CreateProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'create product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  public function UpdateProduct(Request $request_body){

    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      //Log::info("request data :", [$request]);
      if (($response = (new VerificationController())->validateRequiredParameter(array('id','name'), $request)) != '')
        return $response;

      $id = trim($request->id);
      $name = trim($request->name);

      $product = DB::select('SELECT product_id FROM stripe_product_master WHERE id = ?', [$id]);
      $product_id = $product->product_id;

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
      \Stripe\Product::update(
        $product_id,
        ['name' => $name]
      );

      DB::beginTransaction();
      DB::update('UPDATE stripe_product_master SET name = ? WHERE id = ?', [$name,$id]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Stripe product updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-UpdateProduct",$e);
//      Log::error("Stripe-UpdateProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  public function DeleteProduct(Request $request_body){

    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      //Log::info("request data :", [$request]);
      if (($response = (new VerificationController())->validateRequiredParameter(array('id'), $request)) != '')
        return $response;

      $id = trim($request->id);

      $product = DB::select('SELECT product_id FROM stripe_product_master WHERE id = ?', [$id]);
      $product_id = $product->product_id;

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

      $product = \Stripe\Product::retrieve(
        $product_id
      );
      $product->delete();

      DB::beginTransaction();
      DB::update('UPDATE stripe_product_master SET status = 0 WHERE id = ?', [$id]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Stripe product deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("Stripe-DeleteProduct",$e);
//      Log::error("Stripe-DeleteProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  public function sendSubscriptionExpiredMailToUser($subscription_detail,$user_detail){

    try {
      $subscr_type = $subscription_detail[0]->subscr_type;
      $db_final_expiration_time = $subscription_detail[0]->final_expiration_time;

      $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
      $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
      $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
      $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');
      $subscr_type_of_indian_monthly_starter = Config::get('constant.INDIAN_MONTHLY_STARTER');
      $subscr_type_of_indian_yearly_starter = Config::get('constant.INDIAN_YEARLY_STARTER');
      $subscr_type_of_indian_monthly_pro = Config::get('constant.INDIAN_MONTHLY_PRO');
      $subscr_type_of_indian_yearly_pro = Config::get('constant.INDIAN_YEARLY_PRO');

      if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_indian_monthly_starter) {
        $subscription_name = 'Monthly Starter';

      } elseif ($subscr_type == $subscr_type_of_monthly_pro or $subscr_type == $subscr_type_of_indian_monthly_pro) {
        $subscription_name = 'Monthly Pro';

      } elseif ($subscr_type == $subscr_type_of_yearly_pro or $subscr_type == $subscr_type_of_indian_yearly_pro) {

        $subscription_name = 'Yearly Pro';
      } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_indian_yearly_starter) {

        $subscription_name = 'Yearly Starter';
      } else {
        $subscription_name = "None";
      }

      $date = new DateTime($db_final_expiration_time);
      $final_expiration_time = $date->format('M d, Y H:i:s T');

      $subject = 'PhotoADKing: Subscription Expired';
      $template = 'simple';
      $message_body = array(
        'message' => '<p style="text-align: left">Thanks for signing up for <b>PhotoADKing</b>. We hope you have been enjoying the <b>
                            ' . $subscription_name . '</b>. <br><br><span style="color: #484747;">Unfortunately, your <b>' . $subscription_name . '</b> is ending on <b>'
          . $final_expiration_time . '</b>.</span><br><br>We\'d love to keep you as a customer,
                                and there is still time to subscribe to a new plan! Simply visit your account dashboard to subscribe.
                                <br><br>As a reminder, when your purchase expires you will be automatically placed on the free plan.</p>',
        'user_name' => $user_detail->first_name
      );

      $data = array('template' => $template, 'email' => $user_detail->email_id, 'subject' => $subject, 'message_body' => $message_body);

      Mail::send($data['template'], $data, function ($message) use ($data) {
        $message->to($data['email'])->subject($data['subject']);
        $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
        $message->bcc(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);
        $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
      });
      return 1;
    } catch (Exception $e) {
      (new ImageController())->logs("sendSubscriptionExpiredMailToUser",$e);
//      Log::error("sendSubscriptionExpiredMailToUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      return 0;
    }


  }

}
