<?php

namespace App\Http\Controllers;

use App\Console\Commands\SubscriptionExpireSchedule;
use PhpParser\Node\Stmt\Foreach_;
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

/**
 * Class stripeWebhookEventController
 *
 * @package api\app\Http\Controllers\api
 */
class stripeWebhookEventController extends Controller
{

  /*================================ Create new subscription ======================================*/

  public function stripePaymentEvents()
  {
    Log::info('Stripe webhook call stripePaymentEvents');
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

    // If you are testing your webhook locally with the Stripe CLI you
    // can find the endpoint's secret by running `stripe listen`
    // Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
    $endpoint_secret = Config::get('constant.PAYMENT_WEBHOOK_SECRET_KEY');


    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch (\UnexpectedValueException $e) {
      // Invalid payload
      Log::error('UnexpectedValueException : 400');
      http_response_code(400);
      exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      Log::error('SignatureVerificationException : 400');
      http_response_code(400);
      exit();
    }

    // Handle the event
    switch ($event->type) {
      case 'payment_method.attached':
        $paymentMethod = $event->data->object; // contains a StripePaymentMethod
        $payment_method_object = $this->handlePaymentMethodAttached($paymentMethod);
        break;
      case 'payment_intent.succeeded':
        $paymentMethod = $event->data->object;
        $payment_method_object = $this->handlePaymentIntentSucceeded($paymentMethod);
        break;
      case 'customer.subscription.created':
        $customer_subscription = $event->data->object; // contains a StripeCustomerSubscription
        $subscription_object = $this->handleCustomerSubscriptionCreated($customer_subscription);
        break;
      // ... handle other event types
      default:
        // Unexpected event type
        http_response_code(400);
        exit();
    }

    http_response_code(200);

  }

  /*=============== Functions ================*/

  //Occurs whenever a new payment method is attached to a customer.
  function handlePaymentMethodAttached($paymentMethod)
  {
//    Log::info('handlePaymentMethodAttached : ',[$paymentMethod]);
    try {
      if (isset($paymentMethod['object']) == 'payment_method') {
        $payment_method_id = $paymentMethod->id;
        $payment_method_verify_data = DB::select('SELECT id FROM stripe_payment_method_details WHERE payment_method_id = ? ', [$payment_method_id]);
        if (count($payment_method_verify_data) <= 0) {
          Log::error('handlePaymentMethodAttached : payment method not exist');
        } elseif (count($payment_method_verify_data) > 1) {
          Log::error('handlePaymentMethodAttached : payment method is more than one with same method id');
        } else {
          DB::beginTransaction();
          DB::update('UPDATE stripe_payment_method_details SET is_verify = 1 WHERE payment_method_id = ? ', [$payment_method_id]);
          DB::commit();
//          Log::error('handlePaymentMethodAttached : payment method successfully verified');
        }
      } else {
        Log::error('paymentMethodEvents -> handlePaymentMethodAttached : This is not a payment_method object.', [$paymentMethod]);
      }
    } catch (Exception $e) {
      DB::rollBack();
      (new ImageController())->logs("handlePaymentMethodAttached",$e);
//      Log::error("handlePaymentMethodAttached : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  //Occurs when a PaymentIntent has successfully completed payment.
  function handlePaymentIntentSucceeded($paymentIntent)
  {
//    Log::info('handlePaymentIntentSucceeded : ',[$paymentIntent]);
    try {
      if (isset($paymentIntent ['object']) == 'payment_intent') {
//          Log::error('handlePaymentIntentSucceeded : payment intent successfully verified');
        }
       else {
        Log::error('paymentMethodEvents -> handlePaymentIntentSucceeded : This is not a payment_intent object.', [$paymentIntent]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handlePaymentIntentSucceeded",$e);
//      Log::error("handlePaymentIntentSucceeded : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  //Occurs whenever a customer is signed up for a new plan.
  function handleCustomerSubscriptionCreated($subscription)
  {
//    Log::info('handleCustomerSubscriptionCreated : ',[$subscription]);
    try {
      if (isset($subscription ['object']) == 'subscription') {

        $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
        $subscription_id = $subscription->id;
        $datetimeFormat = 'Y-m-d H:i:s';
        $subscription_verify_data = DB::select('SELECT
                                                ssm.id AS subscription_id,
                                                plan_id,
                                                scd.id as  customer_id,
                                                spd.id AS payment_method_id,
                                                ssm.user_id FROM
                                                stripe_subscription_master AS ssm
                                                INNER JOIN stripe_customer_details AS scd ON ssm.user_id = scd.user_id
                                                INNER JOIN stripe_payment_method_details AS spd ON ssm.user_id = spd.user_id
                                                WHERE
                                                ssm.subscription_id = ? AND 
                                                scd.is_active = 1 AND
                                                spd.is_active = 1 AND
                                                ssm.is_active = 1', [$subscription_id]);
        if (count($subscription_verify_data) <= 0) {
//          Log::error('handleCustomerSubscriptionCreated : subscription not exist');
          $invoice_id = $subscription->latest_invoice;
          $items = $subscription->items;
          $data = $items->data;
          $data_plan = $data[0]->plan;
          $plan_id = $data_plan->id;
          $subscription_json_response = json_encode(json_decode(json_encode($subscription)));
          DB::beginTransaction();
          DB::insert('INSERT INTO stripe_webhook_details(subscription_id,
                                                                                    invoice_id,
                                                                                    plan_id,
                                                                                    json_response,
                                                                                    is_active,
                                                                                    is_verify) VALUES(?, ?, ?, ? , 1, 0)',
                                                          [$subscription_id,
                                                            $invoice_id,
                                                            $plan_id,
                                                            $subscription_json_response]);
          DB::commit();

        } elseif (count($subscription_verify_data) > 1) {
          Log::error('handleCustomerSubscriptionCreated : subscription is more than one with same subscription id',['count :' => count($subscription_verify_data)]);
        } else {

          $items = $subscription->items;
          $data = $items->data;
          $data_plan = $data[0]->plan;
          $plan_amount = $data_plan->amount / 100;
          if(isset($subscription->discount)){
            $discount = $subscription->discount;
            if(isset($discount->coupon)){
              $coupon = $discount->coupon;
              $percent_off = $coupon->percent_off;
              $plan_amount = $plan_amount * $percent_off / 100 ;
            }
          }
          $plan_nickname = $data_plan->nickname;
          $currency = $data_plan->currency;
          $status = $subscription->status;
          $invoice_id = $subscription->latest_invoice;
          $invoice = \Stripe\Invoice::retrieve(
            $invoice_id
          );

          $hosted_invoice_url = $invoice->hosted_invoice_url;


          $current_period_start = $subscription->current_period_start;
          $date = new \DateTime();
          $date->setTimestamp($current_period_start);
          $current_period_start = $date->format($datetimeFormat);

          $current_period_end = $subscription->current_period_end;
          $date = new \DateTime();
          $date->setTimestamp($current_period_end);
          $current_period_end = $date->format($datetimeFormat);

          $db_subscription_id = $subscription_verify_data[0]->subscription_id;
          $plan_id = $subscription_verify_data[0]->plan_id;
          $payment_method_id = $subscription_verify_data[0]->payment_method_id;
          $customer_id = $subscription_verify_data[0]->customer_id;
          $user_id = $subscription_verify_data[0]->user_id;
//          Log::error('handleCustomerSubscriptionCreated : subscription successfully verified');

          $is_verified = DB::select('SELECT 1 FROM stripe_payment_status WHERE is_verified_by_webhook = 1 AND user_id = ?',[$user_id]);

          If(count($is_verified) >  0){
            DB::beginTransaction();
            DB::update('UPDATE stripe_subscription_master SET is_verify = 1 WHERE subscription_id = ? ', [$subscription_id]);
            DB::commit();
//            Log::info('subscription already verified');

          }else{

          DB::beginTransaction();
          DB::update('UPDATE stripe_subscription_master SET is_verify = 1 WHERE subscription_id = ? ', [$subscription_id]);
          DB::update('UPDATE stripe_payment_status
                                            SET is_verified_by_webhook = 1
                                            WHERE
                                            user_id = ? AND
                                            plan_id = ? AND
                                            payment_method_id = ? AND
                                            customer_id = ? AND
                                            stripe_subscription_id = ?',
            [$user_id,
              $plan_id,
              $payment_method_id,
              $customer_id,
              $db_subscription_id]);
          DB::commit();
          /*if($status == "active"){
            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

            $user_name = $user_detail->first_name;
            $template = "payment_successful";
            $subject = 'PhotoADKing: Subscribe The New Plan';
            $message = 'Thank you for purchasing subscription for the ' .$plan_nickname. '.';
            $subscription_name = $plan_nickname;
            $subscr_id = $subscription_id;
            $first_name = $user_detail->first_name;
            $payment_received_from = $user_detail->email_id;
            $txn_type = 'Subscription[S]';
            $mc_currency = $currency;
            $total_amount = $plan_amount;
            $payment_status = $status;
            $invoice_url = $hosted_invoice_url;
            $email = $user_detail->email_id;
            $activation_date = $current_period_start;
            $next_billing_date = $current_period_end;
            $api_name = 'stripeUserPayment';
            $api_description = 'subscribe a new subscription.';
            //send mail of subscription successful
            $response = (new stripePaymentController())->SubscriptionMail($user_id,$user_name,$template,$subject,$message,$subscription_name,$txn_type,$subscr_id,$first_name,$payment_received_from,$total_amount,$mc_currency,$email,$payment_status,$activation_date,$next_billing_date,$api_name,$api_description,$invoice_url,$txn_id='');
            if(!isset($response['success'])){
              Log::error('unable to send mail to user');
            }

            $template = 'payment_successful';
            $subject = 'PhotoADKing: Payment Received';
            $message_body = array(
              'message' => 'Your payment received successfully. Following are the transaction details.',
              'subscription_name' => $plan_nickname,
              'txn_id' => $subscription_id,
              'txn_type' => 'Subscription',
              'subscr_id' => $subscription_id,
              'first_name' => $user_detail->first_name,
              'payment_received_from' => $user_detail->first_name. '('.$user_detail->email_id.')',
              'total_amount' => $plan_amount,
              'mc_currency' => $currency,
              'payer_email' => $user_detail->email_id,
              'payment_status' => $status,
              'activation_date' => $current_period_start,
              'next_billing_date' => ($current_period_end != NULL) ? $current_period_end : 'NA',
              'invoice_url' => ($hosted_invoice_url != NULL) ? $hosted_invoice_url : 'NA'
            );
            $api_name = 'stripeUserPayment';
            $api_description = 'subscribe a new subscription.';

            $this->dispatch(new EmailJob($user_id, $email, $subject, $message_body, $template, $api_name, $api_description));
          }*/
          }
        }
      } else {
        Log::error('customerEvents -> handleCustomerSubscriptionCreated : this object is not subscription object.', [$subscription]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handleCustomerSubscriptionCreated",$e);
//      Log::error("handleCustomerSubscriptionCreated : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::rollBack();
    }
  }

  /*================================ Cancel subscription ======================================*/

  public function stripeSubscriptionUpdateEvents()
  {

    Log::info('Stripe webhook call stripeSubscriptionUpdateEvents');
    \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
    $endpoint_secret = Config::get('constant.UPDATE_PAYMENT_WEBHOOK_SECRET_KEY');

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch (\UnexpectedValueException $e) {
      // Invalid payload
      Log::error('UnexpectedValueException : 400');
      http_response_code(400);
      exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      Log::error('SignatureVerificationException : 400');
      http_response_code(400);
      exit();
    }

    // Handle the event
    switch ($event->type) {
      case 'customer.subscription.updated':
        $update_subscription = $event->data->object; // contains a StripePaymentMethod
        $payment_method_object = $this->handleCustomerSubscriptionUpdated($update_subscription);
        break;
      // ... handle other event types
      case 'customer.subscription.deleted':
        $delete_subscription = $event->data->object; // contains a StripePaymentMethod
        $payment_method_object = $this->handleCustomerSubscriptionDeleted($delete_subscription);
        break;
      // ... handle other event types
      default:
        // Unexpected event type
        http_response_code(400);
        exit();
    }

    http_response_code(200);

  }

  /*=============== Functions ================*/

  //Occurs whenever a subscription changes (e.g., switching from one plan to another, or changing the status from trial to active).
  function handleCustomerSubscriptionUpdated($subscription)
  {
//    Log::info('handleCustomerSubscriptionUpdated : ');
    try {
      if (isset($subscription ['object']) == 'subscription') {

        $subscription_id = $subscription->id;
        $cancel_at_period_end = $subscription->cancel_at_period_end;
        $status = $subscription->status;
        $datetimeFormat = 'Y-m-d H:i:s';

        $plan_id = $subscription->plan->id;
        $latest_invoice = $subscription->latest_invoice;

        if ($status == "active") {
//          Log::info('handleCustomerSubscriptionUpdated in active status : ',[$status,$subscription_id]);
          $is_user_active = DB::select('SELECT is_active,id FROM payment_status_master WHERE txn_id = ?', [$subscription_id]);
          if(count($is_user_active) > 0){
           if ($is_user_active[0]->is_active == 0) {
            if ($cancel_at_period_end == false) {
//              Log::info('handleCustomerSubscriptionUpdated in active status cancel_at_period_end == false: ');
              //handle 3D secure subscription

              $manage_subscription = DB::select('SELECT user_id,final_expiration_time FROM subscriptions WHERE is_active = 0 AND transaction_id = ? AND subscr_type = ?',[$subscription_id,$plan_id]);
              if($manage_subscription[0]->final_expiration_time == ""){
                $user_id = $manage_subscription[0]->user_id;

                  /* get country code for set local time in email */
                  $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
                  $this->country_code = $billing_info->country_code;

                $user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?',[$manage_subscription[0]->user_id]);
                if($user_role[0]->role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')){

                  Log::info('handle 3D secure subscription successfully');
                  DB::beginTransaction();

                  DB::update('UPDATE subscriptions SET
                              final_expiration_time = NULL,
                              cancellation_date = NULL,
                              remaining_days= NULL,
                              response_message= NULL,
                              payment_status = ?,
                              is_active= 1
                              WHERE
                    is_active = 0 AND 
                    transaction_id = ? ', [$status,$subscription_id]);

                  DB::update('UPDATE stripe_subscription_master SET is_active = 1,is_verify = 1 WHERE is_active = 0 AND is_verify = 0 AND subscription_id = ? ',[$subscription_id]);

                  DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [1, $subscription_id]);

                  DB::commit();

                  //update user role
                  $subscr_type = $plan_id;
                  (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);

                  $invoice = \Stripe\Invoice::retrieve(
                    $latest_invoice
                  );
                  $hosted_invoice_url = $invoice->hosted_invoice_url;

                  $current_period_end = $subscription->current_period_end;
                  $datetimeFormat = 'Y-m-d H:i:s';
                  $date = new \DateTime();
                  $date->setTimestamp($current_period_end);
                  $current_period_end = $date->format($datetimeFormat);

                  $current_period_start = $subscription->current_period_start;
                  $date = new \DateTime();
                  $date->setTimestamp($current_period_start);
                  $current_period_start = $date->format($datetimeFormat);

                  $cancel_at = $subscription->cancel_at;
                  If ($cancel_at != '') {
                    $date = new \DateTime();
                    $date->setTimestamp($cancel_at);
                    $cancel_at = $date->format($datetimeFormat);
                  }

                  $created = $subscription->created;

                  $items = $subscription->items;
                  $data = $items->data;
                  $data_plan = $data[0]->plan;
                  $plan_id = $data_plan->id;
                  $plan_nickname = $data_plan->nickname;
                  $currency = $data_plan->currency;

                  $plan_amount = $data_plan->amount / 100;
                  if(isset($subscription->discount)){
                    $discount = $subscription->discount;
                    if(isset($discount->coupon)){
                      $coupon = $discount->coupon;
                      $percent_off = $coupon->percent_off;
                      $plan_amount = $plan_amount * $percent_off / 100 ;
                    }
                  }

                  $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                  $user_name = $user_detail->first_name;
                  $template = "payment_successful";
                  $subject = 'PhotoADKing: Subscribe The New Plan';
                  $message = 'Thank you for purchasing subscription for the ' . $plan_nickname . '.';
                  $subscription_name = $plan_nickname;
                  $subscr_id = $subscription_id;
                  $first_name = $user_detail->first_name;
                  $payment_received_from = $user_detail->email_id;
                  $txn_type = 'Subscription[S]';
                  $mc_currency = $currency;
                  $total_amount = $plan_amount;
                  $payment_status = $status;
                  $invoice_url = $hosted_invoice_url;
                  $email = $user_detail->email_id;
                  $activation_date = $current_period_start;
                  $next_billing_date = $current_period_end;
                  $api_name = 'stripeUserPayment';
                  $api_description = 'subscribe a new subscription.';
                  //send mail of subscription successful
                  $response = (new stripePaymentController())->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $total_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id = '',$this->country_code);
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
                }else{

//                  Log::error('user already working on subscribe any one plan');
//                  Log::info('handle 3D secure subscription update with upgrade or downgrade');
                  DB::beginTransaction();

                  DB::update('UPDATE subscriptions SET
                              final_expiration_time = NULL,
                              cancellation_date = NULL,
                              remaining_days= NULL,
                              response_message= NULL,
                              is_active= 1
                              WHERE
                    transaction_id = ? ', [$subscription_id]);

                  DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [1, $subscription_id]);

                  DB::commit();

                  //update user role
                  $subscr_type = $plan_id;
                  (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);

                  $invoice = \Stripe\Invoice::retrieve(
                    $latest_invoice
                  );
                  $hosted_invoice_url = $invoice->hosted_invoice_url;

                  $current_period_end = $subscription->current_period_end;
                  $datetimeFormat = 'Y-m-d H:i:s';
                  $date = new \DateTime();
                  $date->setTimestamp($current_period_end);
                  $current_period_end = $date->format($datetimeFormat);

                  $current_period_start = $subscription->current_period_start;
                  $date = new \DateTime();
                  $date->setTimestamp($current_period_start);
                  $current_period_start = $date->format($datetimeFormat);

                  $cancel_at = $subscription->cancel_at;
                  If ($cancel_at != '') {
                    $date = new \DateTime();
                    $date->setTimestamp($cancel_at);
                    $cancel_at = $date->format($datetimeFormat);
                  }

                  $created = $subscription->created;

                  $items = $subscription->items;
                  $data = $items->data;
                  $data_plan = $data[0]->plan;
                  $plan_id = $data_plan->id;
                  $plan_nickname = $data_plan->nickname;
                  $currency = $data_plan->currency;


                  $plan_amount = $data_plan->amount / 100;
                  if(isset($subscription->discount)){
                    $discount = $subscription->discount;
                    if(isset($discount->coupon)){
                      $coupon = $discount->coupon;
                      $percent_off = $coupon->percent_off;
                      $plan_amount = $plan_amount * $percent_off / 100 ;
                    }
                  }


                  $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                  $user_name = $user_detail->first_name;
                  $template = "payment_successful";
                  $subject = 'PhotoADKing: Subscription Plan Changed';
                  $message = 'Your subscription plan changed successfully. Remaining amount are lefter into your new subscription. Following are the transaction details.';
                  $subscription_name = $plan_nickname;
                  $subscr_id = $subscription_id;
                  $first_name = $user_detail->first_name;
                  $payment_received_from = $user_detail->email_id;
                  $txn_type = 'Subscription[S]';
                  $mc_currency = $currency;
                  $total_amount = $plan_amount;
                  $payment_status = $status;
                  $invoice_url = $hosted_invoice_url;
                  $email = $user_detail->email_id;
                  $activation_date = $current_period_start;
                  $next_billing_date = $current_period_end;
                  $api_name = 'stripeUserPayment';
                  $api_description = 'subscribe a new subscription.';
                  //send mail of subscription successful
                  $response = (new stripePaymentController())->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $total_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id = '',$this->country_code);
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
                    'activation_date' => $activation_date_local,
                    'next_billing_date' => ($next_billing_date_local != NULL) ? $next_billing_date_local : ''
                  );
                  $api_name = 'stripeUserPayment';
                  $api_description = 'subscribe a new subscription.';
                  $this->dispatch(new EmailJob($user_id, $email, $subject, $message_body, $template, $api_name, $api_description));
                }

              }else{
                //reactivate subscription
                Log::info('handleCustomerSubscriptionUpdated : subscription reactivate process -pending');
              }
            }else{
//              Log::info('handleCustomerSubscriptionUpdated in active status cancel_at_period_end == true: ',[$subscription_id]);
              Log::info('handleCustomerSubscriptionUpdated : subscription already cancel by api');
            }
          } else {
            if ($cancel_at_period_end == true) {
//              Log::info('handleCustomerSubscriptionUpdated in active status else cancel_at_period_end == true: ',[$subscription_id]);
//              Log::info('handleSubscriptionUpdated : Subscription cancel : ', [$subscription]);
              $current_period_end = $subscription->current_period_end;
              $date = new \DateTime();
              $date->setTimestamp($current_period_end);
              $current_period_end_date = $date->format($datetimeFormat);

              $is_verified = DB::select('SELECT id FROM subscriptions WHERE transaction_id = ? AND is_active = 0 AND final_expiration_time = ?', [$subscription_id, $current_period_end_date]);
              if (!isset($is_verified[0]->id)) {

                $user_detail = DB::select('SELECT sps.user_id FROM stripe_payment_status AS sps, subscriptions AS sub WHERE sps.plan_id = ? 
                                                                                                                          AND sps.is_active = 1 
                                                                                                                          AND sps.subscription_id = sub.transaction_id 
                                                                                                                          AND sub.transaction_id = ?',
                  [$plan_id, $subscription_id]);
                $user_id = $user_detail[0]->user_id;

                  /* get country code for set local time in email */
                  $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
                  $this->country_code = $billing_info->country_code;

                $canceled_date = $subscription->canceled_at;
                $date = new \DateTime();
                $date->setTimestamp($canceled_date);
                $canceled_at = $date->format($datetimeFormat);

                $invoice_id = $subscription->latest_invoice;
                $items = $subscription->items;
                $data = $items->data;
                $plan = $data[0]->plan;
                $subscription_name = $plan->nickname;
                $total_amount = $plan->amount / 100;


                DB::beginTransaction();

                DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $current_period_end_date, $user_id]);

                DB::update('UPDATE subscriptions SET        
                              final_expiration_time = ? WHERE 
                    user_id = ? AND transaction_id = ?', [$current_period_end_date, $user_id, $subscription_id]);

                DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$user_id]);

                DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

                DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

                DB::commit();

                (new UserController())->cancelSubscriptionByPaypalID($subscription_id, $payer_email = '');

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
                  'mc_currency' => '',
                  'cancellation_date' => $canceled_at,
                  'expiration_date' => $current_period_end_date,
                  'cancellation_date_local' => $cancellation_date_local,
                  'expiration_date_local' => $expiration_date_local
                );
                $api_name = 'paypalIpn';
                $api_description = 'subscription cancelled .';

//                Log::info('cancel_subscription mail data : ', ['user_id' => $user_id, 'email_id' => $user_detail->email_id, 'subject' => $subject, 'message_body' => $message_body]);
                $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));
              } else {
                Log::info('handleSubscriptionUpdated : cancel subscription successfully verified.[is_verified]');
              }

            } elseif ($cancel_at_period_end == false) {
//              Log::info('handleCustomerSubscriptionUpdated in active status else cancel_at_period_end == false: ',[$subscription_id]);
//              Log::info('handleCustomerSubscriptionUpdated : Subscription updated : ', [$subscription]);

              $current_period_end = $subscription->current_period_end;
              $date = new \DateTime();
              $date->setTimestamp($current_period_end);
              $current_period_end = $date->format($datetimeFormat);

              $is_sub_verified = DB::select('SELECT id,subscr_type,expiration_time 
                                                                FROM subscriptions
                                                                WHERE transaction_id = ? AND
                                                                payment_type = 2 AND is_active = 1', [$subscription_id]);
              if ($is_sub_verified[0]->subscr_type == $plan_id && $is_sub_verified[0]->expiration_time == $current_period_end) {
                Log::info('handleSubscriptionUpdated : Stripe Subscription already upgraded : ');
              }else if ($is_sub_verified[0]->subscr_type != $plan_id) {
                Log::info('handleCustomerSubscriptionUpdated : Stripe Subscription upgrade or downgrade : ');

                $user_detail = DB::select('SELECT user_id FROM subscriptions WHERE is_active = 1 AND transaction_id = ?',
                  [$subscription_id]);
                $user_id = $user_detail[0]->user_id;

                  /* get country code for set local time in email */
                  $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
                  $this->country_code = $billing_info->country_code;

                $user_details = DB::select('SELECT is_secure_user_active FROM stripe_payment_status 
                                                  WHERE user_id = ? AND require_3D_secure IS NOT NULL',[$user_id]);

                If(count($user_details) >= 0 ){
                  $is_secure_user_active = $user_details[0]->is_secure_user_active;
                  $invoice_id = $subscription->latest_invoice;
                  $invoice = \Stripe\Invoice::retrieve(
                    $invoice_id
                  );
                  $hosted_invoice_url = $invoice->hosted_invoice_url;

                  $status = $subscription->status;
                  $customer_id = $subscription->customer;


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

                  $paypal_status = 1;
                  $is_active = 1;
                  $subscr_type = $plan_id;

                  (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);
                  $expiration_time = $current_period_end;

                  $user_detail = (new LoginController())->getUserInfoByUserId($user_id);


                  if($is_secure_user_active == 1){
                    DB::beginTransaction();
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
                    DB::commit();

                    $user_name = $user_detail->first_name;
                    $template = "payment_successful";
                    $subject = 'PhotoADKing: Subscription Plan Changed';
                    $message = 'Your subscription plan changed successfully. Remaining amount are lefter into your new subscription. Following are the transaction details.';
                    $subscription_name = $plan_nickname;
                    $txn_id = $subscription_id;
                    $subscr_id = $subscription_id;
                    $first_name = $user_detail->first_name;
                    $payment_received_from = $user_detail->email_id;
                    $txn_type = 'Subscription[S]';
                    $mc_currency = $currency;
                    $total_amount = $plan_amount;
                    $payment_status = $status;
                    $invoice_url = $hosted_invoice_url;
                    $email = $user_detail->email_id;
                    $activation_date = $current_period_start;
                    $next_billing_date = $current_period_end;
                    $api_name = 'stripeUserPayment';
                    $api_description = 'subscribe a new subscription.';
                    //send mail of subscription successful
                    $response = (new stripePaymentController())->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $total_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id,$this->country_code);
                    if (!isset($response['success'])) {
                      Log::error('unable to send mail to user');
                    }

                    $activation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($current_period_start,$this->country_code);
                    $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($current_period_end,$this->country_code);
                    $template = 'payment_successful';
                    $subject = 'PhotoADKing: Payment Received';
                    $message_body = array(
                      'message' => 'Your payment received successfully. Following are the transaction details.',
                      'subscription_name' => $plan_nickname,
                      'txn_id' => $subscription_id,
                      'txn_type' => 'Subscription[S]',
                      'subscr_id' => $subscription_id,
                      'first_name' => $user_detail->first_name,
                      'payment_received_from' => $user_detail->first_name . '(' . $user_detail->email_id . ')',
                      'total_amount' => $plan_amount,
                      'mc_currency' => $currency,
                      'payer_email' => $user_detail->email_id,
                      'payment_status' => $status,
                      'activation_date' => $current_period_start,
                      'next_billing_date' => ($current_period_end != NULL) ? $current_period_end : 'NA',
                      'activation_date_local' => $activation_date_local,
                      'next_billing_date_local' => ($next_billing_date_local != NULL) ? $next_billing_date_local : ''
                    );
                    $api_name = 'stripeUserPayment';
                    $api_description = 'subscribe a new subscription.';
                    $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));

                  }else{

                    DB::beginTransaction();
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

                    $payment_method_id = DB::select('SELECT sps.payment_method_id FROM stripe_payment_status as sps , 
                                                                        stripe_subscription_master AS ssm 
                                                        WHERE ssm.user_id = ? 
                                                        AND ssm.subscription_id = ? 
                                                        AND ssm.id = sps.stripe_subscription_id',
                      [$user_id,$subscription_id]);

                    DB::update('UPDATE stripe_payment_method_details SET is_active = 1 WHERE user_id = ? AND id = ?',[$user_id,$payment_method_id[0]->payment_method_id]);

                    DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [1, $subscription_id]);

                    DB::update('UPDATE stripe_customer_details SET is_active = 1 WHERE user_id = ? AND customer_id = ?',[$user_id,$customer_id]);

                    DB::update('UPDATE stripe_payment_status SET is_active = ? WHERE user_id = ? ', [1, $user_id]);
                    DB::commit();

                    $user_name = $user_detail->first_name;
                    $template = "payment_successful";
                    $subject = 'PhotoADKing: Subscription Plan Activated';
                    $message = 'Your subscription plan activated successfully. Following are the transaction details.';
                    $subscription_name = $plan_nickname;
                    $txn_id = $subscription_id;
                    $subscr_id = $subscription_id;
                    $first_name = $user_detail->first_name;
                    $payment_received_from = $user_detail->email_id;
                    $txn_type = 'Subscription[S]';
                    $mc_currency = $currency;
                    $payment_status = $status;
                    $invoice_url = $hosted_invoice_url;
                    $email = $user_detail->email_id;
                    $activation_date = $current_period_start;
                    $next_billing_date = $current_period_end;
                    $api_name = 'resubscribeStripeSubscription';
                    $api_description = 'resubscribe a subscription.';
                    //send mail of subscription successful
                    (new stripePaymentController())->SubscriptionMail($user_id, $user_name, $template, $subject, $message, $subscription_name, $txn_type, $subscr_id, $first_name, $payment_received_from, $plan_amount, $mc_currency, $email, $payment_status, $activation_date, $next_billing_date, $api_name, $api_description, $invoice_url, $txn_id,$this->country_code);
                  }


                }else{
                  $billing_cycle_anchor = $subscription->billing_cycle_anchor;
                  $datetimeFormat = 'Y-m-d H:i:s';
                  $date = new \DateTime();
                  $date->setTimestamp($billing_cycle_anchor);
                  $billing_cycle_anchor = $date->format($datetimeFormat);

                  $current_period_start = $subscription->current_period_start;
                  $date = new \DateTime();
                  $date->setTimestamp($current_period_start);
                  $current_period_start = $date->format($datetimeFormat);

                  $cancel_at = $subscription->cancel_at;

                  $items = $subscription->items;
                  $data = $items->data;
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
                  $subscription_json_response = json_encode(json_decode(json_encode($subscription)));

                  //update user role
                  (new PaypalIPNController())->updateUserRole($plan_id, $user_id);

                  DB::beginTransaction();
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

                  DB::update('UPDATE payment_status_master SET
                          paypal_status = ?,
                          paypal_payment_status = ?,
                          paypal_response = ?,
                          expiration_time = ?,
                          is_active = ?
                          WHERE user_id = ? AND txn_id = ?',
                    [
                      1,
                      $status,
                      $subscription_json_response,
                      $current_period_end,
                      1,
                      $user_id,
                      $subscription_id,
                    ]);
                  DB::commit();

                }

              }else if ($is_sub_verified[0]->subscr_type == $plan_id) {
                Log::info('handleCustomerSubscriptionUpdated Active: Stripe Subscription renew : ');

                $user_detail = DB::select('SELECT user_id FROM subscriptions WHERE transaction_id = ? AND subscr_type = ?',
                  [$subscription_id,$plan_id]);
                $user_id = $user_detail[0]->user_id;

                $invoice_id = $subscription->latest_invoice;
                $invoice = \Stripe\Invoice::retrieve(
                  $invoice_id
                );

                $hosted_invoice_url = $invoice->hosted_invoice_url;
//                Log::info('handleCustomerSubscriptionUpdated : Stripe Subscription renew invoice_url: ',["hosted_invoice_url"=>$hosted_invoice_url,"latest_invoice"=>$invoice_id]);
                $billing_cycle_anchor = $subscription->billing_cycle_anchor;
                $datetimeFormat = 'Y-m-d H:i:s';
                $date = new \DateTime();
                $date->setTimestamp($billing_cycle_anchor);
                $billing_cycle_anchor = $date->format($datetimeFormat);

                $current_period_start = $subscription->current_period_start;
                $date = new \DateTime();
                $date->setTimestamp($current_period_start);
                $current_period_start = $date->format($datetimeFormat);

                $cancel_at = $subscription->cancel_at;
                If ($cancel_at != '') {
                  $date = new \DateTime();
                  $date->setTimestamp($cancel_at);
                  $cancel_at = $date->format($datetimeFormat);
                }

                $created = $subscription->created;
                $date = new \DateTime();
                $date->setTimestamp($created);
                $created = $date->format($datetimeFormat);

                $items = $subscription->items;
                $data = $items->data;
                $data_plan = $data[0]->plan;
                $plan_id = $data_plan->id;
                $plan_nickname = $data_plan->nickname;
                $currency = $data_plan->currency;

                $plan_amount = $data_plan->amount / 100;
                if(isset($subscription->discount)){
                  $discount = $subscription->discount;
                  if(isset($discount->coupon)){
                    $coupon = $discount->coupon;
                    $percent_off = $coupon->percent_off;
                    $plan_amount = $plan_amount * $percent_off / 100 ;
                  }
                }

                $subscription_json_response = json_encode(json_decode(json_encode($subscription)));
                DB::beginTransaction();
                //stripe_subscription_master
                //is_verify = 2 [renew process is pending to verify]
                DB::update('UPDATE stripe_subscription_master
                                                            set
                                                            invoice_id = ?,
                                                            cancel_at = ?,
                                                            hosted_invoice_url = ?,
                                                            subscription_json_response = ?,
                                                            created = ?,
                                                            is_verify = 2
                                                            WHERE user_id = ? AND
                                                            subscription_id = ?', [$invoice_id,
                  $cancel_at,
                  $hosted_invoice_url,
                  $subscription_json_response,
                  $created,
                  $user_id,
                  $subscription_id]);

               /* //subscriptions "Stripe_Payment"
                DB::update('UPDATE subscriptions SET
                                                paypal_response = ?,
                                                payment_date = ?,
                                                activation_time = ?,
                                                expiration_time = ?,
                                                cancellation_date = ?
                                                 WHERE user_id = ?
                                                 AND transaction_id = ?
                                                 AND is_active = 1',
                  [$subscription_json_response,
                    $billing_cycle_anchor,
                    $current_period_start,
                    $current_period_end,
                    $cancel_at,
                    $user_id,
                    $subscription_id]);

                //payment_status_master
                DB::update('UPDATE payment_status_master SET
                          paypal_response = ?,
                          expiration_time = ?
                          WHERE user_id = ? AND txn_id = ? AND is_active = 1',
                  [$subscription_json_response,
                    $current_period_end,
                    $user_id,
                    $subscription_id,
                  ]);*/

                DB::commit();
                /*$user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                $template = 'payment_successful';
                $subject = 'PhotoADKing: Payment Received For Subscription Renewal';
                $message_body = array(
                  'message' => 'Your subscription for <b>' . $plan_nickname . '</b> has been renewed on <b>' . $billing_cycle_anchor . '</b>.
Thanks for renewing the subscription for <b>' . $plan_nickname . '</b>. We hope you are enjoying the PhotoADKing.',
                  'subscription_name' => $plan_nickname,
                  'txn_type' => 'Subscription',
                  'subscr_id' => $subscription_id,
                  'first_name' => $user_detail->first_name,
                  'payment_received_from' => $user_detail->first_name . ' (' . $user_detail->email_id . ')',
                  'total_amount' => $plan_amount,
                  'mc_currency' => $currency,
                  'payer_email' => $user_detail->email_id,
                  'payment_status' => $status,
                  'activation_date' => $current_period_start,
                  'next_billing_date' => ($current_period_end != NULL) ? $current_period_end : 'NA',
                  'invoice_url' => ($hosted_invoice_url != NULL) ? $hosted_invoice_url : 'NA'
                );
                $api_name = 'stripeSubscriptionUpdateEvents';
                $api_description = 'auto-renew subscription.';

//                Log::debug('handleCustomerSubscriptionUpdated (Payment Received For Subscription Renewal) : ', ['message_body' => $message_body]);
                $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));
                */
              }
            } else {
              Log::error('handleCustomerSubscriptionUpdated : un-define subscription webhook call : ', [$subscription]);
            }
          }
          }else{

          }

          /*

                    $is_user_active = DB::select('SELECT id FROM stripe_subscription_master WHERE subscription_id = ? AND is_active = 1 AND plan_id = ? AND invoice_id = ?',
                                              [$subscription_id,$plan_id,$latest_invoice]);
                    if(isset($is_user_active[0]->id)){
                      Log::info('handleSubscriptionUpdated : is user active ');


                    }else{
                      Log::info('handleSubscriptionUpdated : is user not active ');
                      $is_existed = DB::select('SELECT id FROM stripe_subscription_master WHERE subscription_id = ? AND plan_id = ? AND invoice_id = ?',[$subscription_id,$plan_id,$latest_invoice]);
                      if(isset($is_existed[0]->id)){
                        $paypal_status = 1;
                        $is_active = 1;
                        $subscr_type = $plan_id;

                        $user_detail = DB::select('SELECT sps.id AS id,sps.user_id FROM stripe_payment_status AS sps, subscriptions AS sub WHERE sps.plan_id = ?
                                                                                                                                    AND sps.is_active = 1
                                                                                                                                    AND sps.subscription_id = sub.id
                                                                                                                                    AND sub.transaction_id = ?',
                          [$plan_id,$subscription_id]);
                        $sps_id = $user_detail[0]->id;
                        $user_id = $user_detail[0]->user_id;

                        $items = $subscription->items;
                        $data = $items->data;
                        $data_plan = $data[0]->plan;
                        $plan_amount = $data_plan->amount / 100;
                        $plan_nickname = $data_plan->nickname;
                        $currency = $data_plan->currency;
                        $status = $subscription->status;
                        $current_period_start = $subscription->current_period_start;
                        $date = new \DateTime();
                        $date->setTimestamp($current_period_start);
                        $current_period_start = $date->format($datetimeFormat);

                        $current_period_end = $subscription->current_period_end;
                        $date = new \DateTime();
                        $date->setTimestamp($current_period_end);
                        $current_period_end = $date->format($datetimeFormat);

                        (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);
                        $days_to_add = 0;
                        $expiration_time = $current_period_end;

                        DB::beginTransaction();
                        DB::update('UPDATE payment_status_master SET expiration_time = ?,is_active = ?,paypal_status = ? WHERE user_id = ? AND txn_id =?',
                          [$expiration_time,$is_active,$paypal_status,$user_id,$subscription_id]);

                        DB::update('UPDATE stripe_payment_status SET is_active = ?,is_verified_by_webhook = 1,id = ?',[$is_active,$sps_id]);
                        DB::commit();
                          $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                          $user_name = $user_detail->first_name;
                          $template = "payment_successful";
                          $subject = 'PhotoADKing: Subscribe The New Plan';
                          $message = 'Thank you for purchasing subscription for the ' .$plan_nickname. '.';
                          $subscription_name = $plan_nickname;
                          $subscr_id = $subscription_id;
                          $first_name = $user_detail->first_name;
                          $payment_received_from = $user_detail->first_name. '('.$user_detail->email_id.')';
                          $txn_type = 'Subscription[S]';
                          $mc_currency = $currency;
                          $total_amount = $plan_amount;
                          $payment_status = $status;
                          $invoice_url = '';
                          $email = $user_detail->email_id;
                          $activation_date = $current_period_start;
                          $next_billing_date = $current_period_end;
                          $api_name = 'stripeUserPayment';
                          $api_description = 'subscribe a new subscription.';
                          //send mail of subscription successful
                          $response = (new stripePaymentController())->SubscriptionMail($user_id,$user_name,$template,$subject,$message,$subscription_name,$txn_type,$subscr_id,$first_name,$payment_received_from,$total_amount,$mc_currency,$email,$payment_status,$activation_date,$next_billing_date,$api_name,$api_description,$invoice_url,$txn_id='');
                          if(!isset($response['success'])){
                            Log::error('unable to send mail to user');
                          }

                          $template = 'payment_successful';
                          $subject = 'PhotoADKing: Payment Received';
                          $message_body = array(
                            'message' => 'Your payment received successfully. Following are the transaction details.',
                            'subscription_name' => $plan_nickname,
                            'txn_id' => $subscription_id,
                            'txn_type' => 'Subscription',
                            'subscr_id' => $subscription_id,
                            'first_name' => $user_detail->first_name,
                            'payment_received_from' => $user_detail->first_name. '('.$user_detail->email_id.')',
                            'total_amount' => $plan_amount,
                            'mc_currency' => $currency,
                            'payer_email' => $user_detail->email_id,
                            'payment_status' => $status,
                            'activation_date' => $current_period_start,
                            'next_billing_date' => ($current_period_end != NULL) ? $current_period_end : 'NA'
                          );
                          $api_name = 'stripeUserPayment';
                          $api_description = 'subscribe a new subscription.';

                          $this->dispatch(new EmailJob($user_id, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                        DB::beginTransaction();
                        DB::delete('DELETE user_session WHERE user_id = ?', [$user_id]);
                        DB::commit();
                      }
                    }


          */
        }elseif ($status == "past_due"){
//          Log::info('handleCustomerSubscriptionUpdated in past_due status : ',[$status]);

          $is_user_subscription_exist = DB::select('SELECT user_id,
                                                            subscr_type,
                                                            expiration_time
                                                            FROM subscriptions
                                                                WHERE transaction_id = ? AND
                                                                payment_type = 2 AND is_active = 1', [$subscription_id]);

          if(count($is_user_subscription_exist) > 0){
            $current_period_end = $subscription->current_period_end;
            $date = new \DateTime();
            $date->setTimestamp($current_period_end);
            $current_period_end = $date->format($datetimeFormat);
//            if ($is_user_subscription_exist[0]->subscr_type == $plan_id && $is_user_subscription_exist[0]->expiration_time == $current_period_end) {
//              Log::info('handleSubscriptionUpdated : Stripe Subscription already updated with past_due status : ');
//            }
//            else if($is_user_subscription_exist[0]->subscr_type == $plan_id && $is_user_subscription_exist[0]->expiration_time != $current_period_end){
            if($is_user_subscription_exist[0]->subscr_type == $plan_id){

//              Log::info('handleCustomerSubscriptionUpdated : Stripe Subscription renew : with past_due status[3D authentication] ',[$subscription_id]);

              $user_id = $is_user_subscription_exist[0]->user_id;

              $invoice_id = $subscription->latest_invoice;
              $invoice = \Stripe\Invoice::retrieve(
                $invoice_id
              );

              $hosted_invoice_url = $invoice->hosted_invoice_url;
//              Log::info(':( handleCustomerSubscriptionUpdated : Stripe 3D-Subscription renew invoice_url: ',["hosted_invoice_url"=>$hosted_invoice_url,"latest_invoice"=>$invoice_id]);

              $current_period_start = $subscription->current_period_start;
              $date = new \DateTime();
              $date->setTimestamp($current_period_start);
              $current_period_start = $date->format($datetimeFormat);

              $created = $subscription->created;
              $date = new \DateTime();
              $date->setTimestamp($created);
              $created = $date->format($datetimeFormat);

              $cancel_at = $subscription->cancel_at;
              If ($cancel_at != '') {
                $date = new \DateTime();
                $date->setTimestamp($cancel_at);
                $cancel_at = $date->format($datetimeFormat);
              }

              $items = $subscription->items;
              $data = $items->data;
              $data_plan = $data[0]->plan;
              $plan_nickname = $data_plan->nickname;

              $subscription_json_response = json_encode(json_decode(json_encode($subscription)));

              /*
              DB::beginTransaction();

              DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $cancel_at, $user_id]);

              DB::update('UPDATE subscriptions SET
                              cancellation_date = ?,
                              final_expiration_time = ?,
                              response_message = ?,
                              is_active = ?,
                              paypal_response = ?
                              WHERE
                    user_id = ? AND transaction_id = ?', [$created,$created,'User confirmation pending',0,$subscription_json_response,$user_id,$subscription_id]);

              DB::update('UPDATE payment_status_master SET is_active = ? WHERE txn_id = ? ', [0, $subscription_id]);

              DB::update('UPDATE stripe_subscription_master SET is_active = 0,subscription_json_response = ? WHERE user_id = ?',[$user_id,$subscription_json_response]);

              DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?',[$user_id]);

              DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?',[$user_id]);

              DB::commit();
              */

              $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
              DB::beginTransaction();
              DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
              DB::commit();

              $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

              $template = 'renew_subscription';
              $subject = 'PhotoADKing: Subscription Renewal';
              $message_body = array(
                'subscription_name' => $plan_nickname,
                'subscr_id' => $subscription_id,
                'user_name' => $user_detail->first_name,
                'email_id' => $user_detail->email_id,
                'activation_date' => $created,
                'expiration_date' => $current_period_start,
                'invoice_url' => $hosted_invoice_url
              );
              $api_name = 'stripeSubscriptionUpdateEvents';
              $api_description = 'auto-renew subscription.';

//              Log::debug('handleCustomerSubscriptionUpdated (Payment Received For 3D authentication Subscription Renewal) : ', ['message_body' => $message_body]);
              $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));
            }else{
              Log::error('Un-define type of subscription with past_due');
            }
          }
        } elseif ($status = "incomplete_expired"){
//          Log::info('handleCustomerSubscriptionUpdated in incomplete_expired status : ',[$status,$subscription_id]);

          $user_subscription = DB::select('SELECT id,user_id,is_active,
                                                  final_expiration_time,
                                                  payment_status
                                                  FROM subscriptions 
                                                  WHERE transaction_id = ?',[$subscription_id]);
          Log::info('subscription details which is failed : ',[$user_subscription]);
          if(count($user_subscription) > 0){
            $subscr_id = $user_subscription[0]->id;
            $user_id = $user_subscription[0]->user_id;

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

            if(isset($user_detail->role_id)){
//              Log::info('handleCustomerSubscriptionUpdated in incomplete_expired status user_details: ',[$subscription_id]);
              $user_subscription_status = DB::select('SELECT error_message FROM stripe_payment_status WHERE subscription_id = ?',[$subscr_id]);
              $cancel_at = $subscription->cancel_at;
              If ($cancel_at != '') {
                $date = new \DateTime();
                $date->setTimestamp($cancel_at);
                $cancel_at = $date->format($datetimeFormat);
              }

              $created = $subscription->created;
              $date = new \DateTime();
              $date->setTimestamp($created);
              $created = $date->format($datetimeFormat);


              $items = $subscription->items;
              $data = $items->data;
              $data_plan = $data[0]->plan;
              $plan_id = $data_plan->id;
              $plan_nickname = $data_plan->nickname;
              $currency = $data_plan->currency;

              $plan_amount = $data_plan->amount / 100;

              $subscription_json_response = json_encode(json_decode(json_encode($subscription)));

              DB::beginTransaction();

              DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $cancel_at, $user_id]);

              DB::update('UPDATE subscriptions SET  
                              cancellation_date = ?,      
                              final_expiration_time = ?,
                              response_message = ?,
                              is_active = ?,
                              paypal_response = ?,
                              is_expired = 1
                              WHERE 
                    user_id = ? AND transaction_id = ?', [$created,$created,'Subscription Failed',0,$subscription_json_response,$user_id,$subscription_id]);

              DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [0, $subscription_id]);

              DB::update('UPDATE stripe_subscription_master SET is_active = 0,subscription_json_response = ? WHERE user_id = ?',[$user_id,$subscription_json_response]);

              DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?',[$user_id]);

              DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?',[$user_id]);

              DB::commit();

              $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
              DB::beginTransaction();
              DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
              DB::commit();

              $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
              $email_id = $user_detail->email_id;
              $first_name = $user_detail->first_name;

              DB::beginTransaction();
              DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);
              DB::commit();
              $decline_reason = '';
              $template = 'update_account_info';
              $subject = 'PhotoADKing: Update your account info';
              $message_body = array(
                'user_name' => $first_name,
                'activation_date' => $created.' UTC',
                'decline_reason' => $user_subscription_status[0]->error_message != '' ? $user_subscription_status[0]->error_message : 'Billing information does not match with card details',
                'email_id' => $email_id,
                'subscr_id' => $subscription_id
              );
              $api_name = 'handleCustomerSubscriptionUpdated';
              $api_description = 'Subscription failed due to cross country.';

              $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));
            }else{
              Log::info('handleCustomerSubscriptionUpdated user transaction failed : user account deleted : ',[$subscription]);
            }
          }
        } else{
          Log::info('handleCustomerSubscriptionUpdated subscription status not handle : ',[$subscription]);
        }
      } else {
        Log::error('stripeSubscriptionUpdateEvents -> handleCustomerSubscriptionUpdated : this object is not subscription object.', [$subscription]);
      }

    } catch (Exception $e) {
      (new ImageController())->logs("handleCustomerSubscriptionUpdated",$e);
//      Log::error("handleCustomerSubscriptionUpdated : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::rollBack();
    }
  }


  function handleCustomerSubscriptionDeleted($subscription){
//    Log::info('handleCustomerSubscriptionDeleted : ', [$subscription]);
    try {
      if (isset($subscription ['object']) == 'subscription') {
        $datetimeFormat = 'Y-m-d H:i:s';
        $status = $subscription->status;
        if ($status == "canceled") {
//          Log::info('handleCustomerSubscriptionDeleted  status : canceled ',[$subscription]);
          $subscription_id = $subscription->id;
          $subscription_detail = DB::select('SELECT user_id,subscr_type,final_expiration_time FROM subscriptions WHERE transaction_id= ?',[$subscription_id]);
          if(isset($subscription_detail[0]->user_id)){
            $user_id = $subscription_detail[0]->user_id;
            $is_user_exist = DB::select('SELECT id FROM user_master WHERE id = ?', [$user_id]);
            if(count($is_user_exist) <= 0){
              return;
            }

            /* get country code for set local time in email */
            $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
            $this->country_code = $billing_info->country_code;

            $is_user_exist = DB::select('SELECT is_active FROM user_master WHERE id = ?',[$user_id]);
            if(count($is_user_exist) > 0 && $is_user_exist[0]->is_active == 1){
              Log::info('handleCustomerSubscriptionDeleted  user_exist  : ',[$user_id]);
              $user_role = DB::select('SELECT ru.role_id
                                  FROM role_user ru, user_master um
                                  WHERE
                                    um.id = ru.user_id AND
                                    um.id = ?',[$user_id]);
              if($user_role[0]->role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')){

                $canceled_date = $subscription->canceled_at;
                $date = new \DateTime();
                $date->setTimestamp($canceled_date);
                $canceled_at = $date->format($datetimeFormat);

                $plan =  $subscription->plan;
                $subscription_name =  $plan->nickname;
                $currency =  $plan->currency;
                $total_amount =  $plan->amount / 100;

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                $template = 'payment_failed';
                $subject = 'PhotoADKing: Payment Failed';
                $message_body = array(
                  'message' => 'Your transaction is Failed . Following are the transaction details.',
                  'subscription_name' => $subscription_name,
                  'txn_id' => $subscription_id,
                  'txn_type' => 'Subscription[S]',
                  'subscr_id' => $subscription_id,
                  'first_name' => $user_detail->first_name,
                  'payment_received_from' => $user_detail->first_name.' ('.$user_detail->email_id.')',
                  'total_amount' => $total_amount,
                  'mc_currency' => $currency,
                  'payer_email' => $user_detail->email_id,
                  'payment_status' => $status
                );
                $api_name = 'handleCustomerSubscriptionDeleted';
                $api_description = 'Subscription failed in case of cross card issue.';

                $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));

              }else{
                //$current_period_end = $subscription->canceled_at; //right for live [current_period_end]
                $current_period_end = $subscription->current_period_end; //only use for subscrption is deletre now [canceled_at]
                $date = new \DateTime();
                $date->setTimestamp($current_period_end);
                $current_period_end_date = $date->format($datetimeFormat);

                $already_canceled = DB::select('SELECT final_expiration_time FROM subscriptions        
                               WHERE final_expiration_time = ? AND
                    user_id = ? AND transaction_id = ? AND is_active = 0', [$current_period_end_date,$user_id,$subscription_id]);

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                if(count($already_canceled) < 0){
                  $canceled_date = $subscription->canceled_at;
                  $date = new \DateTime();
                  $date->setTimestamp($canceled_date);
                  $canceled_at = $date->format($datetimeFormat);

                  $plan =  $subscription->plan;
                  $subscription_name =  $plan->nickname;
                  $total_amount =  $plan->amount / 100;

                  $invoice_id = $subscription->latest_invoice;
                  $payer_email = '';

                  DB::beginTransaction();


                  DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?',
                    [0, $current_period_end_date, $user_id]);

                  DB::update('UPDATE subscriptions SET        
                              final_expiration_time = ? WHERE 
                    user_id = ? AND transaction_id = ?', [$current_period_end_date,$user_id,$subscription_id]);

                  DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?',[$user_id]);

                  DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?',[$user_id]);

                  DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?',[$user_id]);

                  DB::commit();

                  (new UserController())->cancelSubscriptionByPaypalID($subscription_id, $payer_email);

                  // Change user role to free if his subscription is canceled (Not cancels) and subscription time is over
                  $subscription_expire_detail = DB::select('SELECT final_expiration_time FROM subscriptions WHERE transaction_id= ?',[$subscription_id]);

                  if($subscription->cancel_at_period_end && $subscription_expire_detail[0]->final_expiration_time == $current_period_end_date ){
                      $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
                      DB::beginTransaction();
                      DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                      DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);
                      DB::commit();


                    $mail_send = (New stripePaymentController())->sendSubscriptionExpiredMailToUser($subscription_detail,$user_detail);

                  }else {
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
                      'mc_currency' => '',
                      'cancellation_date' => $canceled_at,
                      'expiration_date' => $current_period_end_date,
                      'cancellation_date_local' => $cancellation_date_local,
                      'expiration_date_local' => $expiration_date_local
                    );
                    $api_name = 'paypalIpn';
                    $api_description = 'subscription cancelled .';

//                    Log::info('cancel_subscription mail data : ', ['user_id' => $user_id, 'email_id' => $user_detail->email_id, 'subject' => $subject, 'message_body' => $message_body]);
                    $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));
                  }

                }else{
                  // Changes user role in subscription is canceled and send mail.
//                  Log::info('handleCustomerSubscriptionDeleted already_canceled',[$already_canceled]);
                  if($subscription->cancel_at_period_end && $subscription_detail[0]->final_expiration_time == $current_period_end_date) {
                    $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');

                    DB::beginTransaction();
                    DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $subscription_detail[0]->user_id]);
                    DB::delete('DELETE FROM user_session WHERE user_id = ?', [$subscription_detail[0]->user_id]);
                    DB::commit();

                    $mail_send = (New stripePaymentController())->sendSubscriptionExpiredMailToUser($subscription_detail, $user_detail);
                  }
                }
              }
            }
            /*else{
              Log::info('handleCustomerSubscriptionDeleted user not exist  : ',[$is_user_exist]);
            }*/
          }else{
            Log::info('handleCustomerSubscriptionDeleted subscription not exist  : ',[$subscription_detail]);
          }
        }else{
          Log::info('handleCustomerSubscriptionDeleted subscription canceled but status is not canceled : ',["status"=>$status]);
        }
      }else {
        Log::error('stripeSubscriptionUpdateEvents -> handleCustomerSubscriptionDeleted : this object is not subscription object.', [$subscription]);
      }
    }catch (Exception $e) {
      (new ImageController())->logs("handleCustomerSubscriptionDeleted",$e);
//      Log::error("handleCustomerSubscriptionDeleted : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::rollBack();
    }
  }


  /*================================ Customer events  ======================================*/

  public function stripeCustomerEvents()
  {

    Log::info('Stripe webhook call stripeCustomerEvents');
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

    // If you are testing your webhook locally with the Stripe CLI you
    // can find the endpoint's secret by running `stripe listen`
    // Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
    $endpoint_secret = Config::get('constant.CUSTOMER_WEBHOOK_SECRET_KEY');


    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch (\UnexpectedValueException $e) {
      // Invalid payload
      Log::error('UnexpectedValueException : 400');
      http_response_code(400);
      exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      Log::error('SignatureVerificationException : 400');
      http_response_code(400);
      exit();
    }

    // Handle the event
    switch ($event->type) {
      case 'customer.created':
        $customer = $event->data->object; // contains a StripeCustomer
        $customer_object = $this->handleCustomerCreated($customer);
        break;
      case 'customer.updated':
        $customer = $event->data->object; // contains a StripeCustomer
        $customer_object = $this->handleCustomerUpdated($customer);
        break;
      case 'customer.deleted':
        $customer = $event->data->object; // contains a StripeCustomer
        $customer_object = $this->handleCustomerDeleted($customer);
        break;
      // ... handle other event types
      default:
        // Unexpected event type
        http_response_code(400);
        exit();
    }
    http_response_code(200);

  }

  /*=============== Functions ================*/

  //Occurs whenever a new customer is created.
  function handleCustomerCreated($customer)
  {
//    Log::info('handleCustomerCreated : ',[$customer]);
    try {
      if (isset($customer ['object']) == 'customer') {
        $customer_id = $customer->id;
        $customer_verify_data = DB::select('SELECT 1 FROM stripe_customer_details WHERE customer_id = ? AND is_active = 1', [$customer_id]);
        if (count($customer_verify_data) <= 0) {
          $is_inserted = DB::insert('INSERT into stripe_webhook_details(customer_id,is_active) VALUES(?, 1)',[$customer_id]);
          if($is_inserted == 1){
            Log::info('handleCustomerCreated : customer successfully verified.[is_inserted]');
          }
        } elseif (count($customer_verify_data) > 1) {
          Log::error('handleCustomerCreated :Customer is more than one with same customer id');
        } else {
          $is_updated = DB::update('UPDATE stripe_customer_details SET is_verify = 1 WHERE customer_id = ? ', [$customer_id]);
          if ($is_updated != 1) {
            Log::error('Stripe-Webhook: did not verify customer details by webhook : ', ['is_updated' => $is_updated]);
          }
          /*else{
            Log::error('handleCustomerCreated : Ccustomer successfully verified.[is_updated]');
          }*/
        }
      } else {
        Log::error('stripeCustomerEvents -> handleCustomerCreated : This is not a customer object.', [$customer]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handleCustomerCreated",$e);
//      Log::error("handleCustomerCreated : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  //Occurs whenever a new customer is updated.
  function handleCustomerUpdated($customer)
  {
//    Log::info('handleCustomerUpdated : ',[$customer]);
    try {
      if (isset($customer ['object']) == 'customer') {
        $customer_id = $customer->id;
        $balance = $customer->balance / 100;
        $currency = $customer->currency;
        $customer_balance = $balance.' '.$currency;

        $customer_verify_data = DB::select('SELECT 1 FROM stripe_customer_details WHERE customer_id = ? AND is_active = 1', [$customer_id]);
        if (count($customer_verify_data) <= 0) {
          Log::info('handleCustomerUpdated : customer not found.');
        } elseif (count($customer_verify_data) > 1) {
          Log::error('handleCustomerUpdated :Customer is more than one with same customer id');
        } else {
          $is_updated = DB::update('UPDATE stripe_customer_details SET balance = ? WHERE customer_id = ? AND is_active = 1 AND is_verify = 1',[$customer_balance,$customer_id]);
          /*if($is_updated != 1){
            Log::error('Stripe-Webhook: did not verify customer update details by webhook : ', ['is_updated' => $is_updated]);
          }else{
            Log::error('handleCustomerUpdated : Updated customer successfully verified.');
          }*/
        }
      } else {
        Log::error('stripeCustomerEvents -> handleCustomerUpdated : This is not a customer object.', [$customer]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handleCustomerUpdated",$e);
//      Log::error("handleCustomerUpdated : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  //Occurs whenever a new customer is deleted.
  function handleCustomerDeleted($customer)
  {
//    Log::info('handleCustomerDeleted : ',[$customer]);
    try {
      if (isset($customer ['object']) == 'customer') {
        $customer_id = $customer->id;
        $balance = $customer->balance / 100;
        $currency = $customer->currency;
        $customer_balance = $balance.' '.$currency;
        $customer_verify_data = DB::select('SELECT is_active FROM stripe_customer_details WHERE customer_id = ?', [$customer_id]);
        if (count($customer_verify_data) <= 0) {
          Log::info('handleCustomerDeleted : customer not found.');
        } else {
          if($customer_verify_data[0]->is_active == 1){
            $is_updated = DB::update('UPDATE stripe_customer_details SET balance = ? ,is_active = 0 WHERE customer_id = ? AND is_active = 1 AND is_verify = 1',[$customer_balance,$customer_id]);
            /*if($is_updated != 1){
              Log::error('Stripe-Webhook - handleCustomerDeleted: did not verify customer update details by webhook : ', ['is_updated' => $is_updated]);
            }else{
              Log::error('handleCustomerDeleted : Deleted customer successfully verified.');
            }*/
          }
        }
      } else {
        Log::error('stripeCustomerEvents -> handleCustomerDeleted : This is not a customer object.', [$customer]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handleCustomerDeleted",$e);
//      Log::error("handleCustomerDeleted : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  /*================================ Invoice events  ======================================*/
  public function stripeInvoiceEvents()
  {
    Log::info('Stripe webhook call stripeInvoiceEvents');
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

    // If you are testing your webhook locally with the Stripe CLI you
    // can find the endpoint's secret by running `stripe listen`
    // Otherwise, find your endpoint's secret in your webhook settings in the Developer Dashboard
    $endpoint_secret = Config::get('constant.INVOICE_WEBHOOK_SECRET_KEY');


    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $event = null;

    try {
      $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
      );
    } catch (\UnexpectedValueException $e) {
      // Invalid payload
      Log::error('UnexpectedValueException : 400');
      http_response_code(400);
      exit();
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
      // Invalid signature
      Log::error('SignatureVerificationException : 400');
      http_response_code(400);
      exit();
    }
    // Handle the event
    switch ($event->type) {
      case 'invoice.payment_failed':
        $invoice = $event->data->object; // contains a StripeCustomer
        $invoice_object = $this->handleInvoiceFailed($invoice);
        break;
      case 'invoice.payment_succeeded':
        $invoice = $event->data->object; // contains a StripeCustomer
        $invoice_object = $this->handleInvoiceSucceeded($invoice);
        break;
      case 'invoice.payment_action_required':
        $invoice = $event->data->object; // contains a StripeCustomer
//      Log::info('handlePaymentActionRequired = invoice : ',[$invoice]);
        $invoice_object = $this->handlePaymentActionRequired($invoice);
        break;
      // ... handle other event types
      default:
        // Unexpected event type
        http_response_code(400);
        exit();
    }
    http_response_code(200);
  }

  /*=============== Functions ================*/

  //Occurs whenever an invoice payment attempt fails, due either to a declined payment or to the lack of a stored payment method.
  function handleInvoiceFailed($invoice)
  {
//    Log::info('handleInvoiceFailed : ',[$invoice]);
    try {
      if (isset($invoice ['object']) == 'invoice') {
        $invoice_id = $invoice->id;
        $verify_invoice = DB::select('SELECT is_active FROM stripe_subscription_master WHERE invoice_id = ?', [$invoice_id]);
        if(count($verify_invoice) > 0){
          if ($verify_invoice[0]->is_active == 1) {
            Log::info('handleInvoiceFailed : invoice fail --pending.');
          }
        }
      } else {
        Log::error('stripeInvoiceEvents -> handleInvoiceFailed : This is not a invoice object.', [$invoice]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handleInvoiceFailed",$e);
//      Log::error("handleInvoiceFailed : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  //Occurs whenever an invoice payment attempt succeeds.
  function handleInvoiceSucceeded($invoice)
  {
//    Log::info('handleInvoiceSucceeded : ',[$invoice]);
    try {
      if (isset($invoice ['object']) == 'invoice') {
        $invoice_id = $invoice->id;
        $verify_invoice = DB::select('SELECT is_active,is_verify,user_id FROM stripe_subscription_master WHERE invoice_id = ?', [$invoice_id]);
        if(count($verify_invoice) > 0){
          $user_id = $verify_invoice[0]->user_id;
          $is_user_exist = DB::select('SELECT id FROM user_master WHERE id = ?', [$user_id]);
          if(count($is_user_exist) <= 0){
            return;
          }

          if($verify_invoice[0]->is_verify == 2)
          {
//           Log::info('handleInvoiceSucceeded : renew success.');
            $status = $invoice->status;
            if($status == 'paid'){
              $datetimeFormat = 'Y-m-d H:i:s';
              $subscription = $invoice->subscription;

              /* get country code for set local time in email */
              $billing_info = (new VerificationController())->getBillingInfoByUser($user_id);
              $this->country_code = $billing_info->country_code;

              $subscriptions_arr = \Stripe\Subscription::retrieve(
                $subscription
              );
//              Log::info('subscription response',[$subscriptions_arr]);

              $current_period_start = $subscriptions_arr->current_period_start;
              $date = new \DateTime();
              $date->setTimestamp($current_period_start);
              $period_start = $date->format($datetimeFormat);

              $current_period_end = $subscriptions_arr->current_period_end;
              $date = new \DateTime();
              $date->setTimestamp($current_period_end);
              $period_end = $date->format($datetimeFormat);

              /*$period_end = $invoice->period_end;
              $date = new \DateTime();
              $date->setTimestamp($period_end);
              $period_end = $date->format($datetimeFormat);

              $period_start = $invoice->period_start;
              $date = new \DateTime();
              $date->setTimestamp($period_start);
              $period_start = $date->format($datetimeFormat);*/

              $hosted_invoice_url = $invoice->hosted_invoice_url;
              $customer = $invoice->customer;

              $lines = $invoice->lines;
              $data = $lines->data;
              $data_plan = $data[0]->plan;
              $plan_id = $data_plan->id;
              $plan_nickname = $data_plan->nickname;
              $plan_amount = $data_plan->amount / 100;
              if(isset($subscriptions_arr->discount)){
                $discount = $subscriptions_arr->discount;
                if(isset($discount->coupon)){
                  $coupon = $discount->coupon;
                  $percent_off = $coupon->percent_off;
                  $plan_amount = $plan_amount * $percent_off / 100 ;
                }
              }
              $currency = $data_plan->currency;
              (new PaypalIPNController())->updateUserRole($plan_id, $user_id);

              DB::beginTransaction();
              DB::update('UPDATE stripe_subscription_master
                                                            set
                                                            hosted_invoice_url = ?,
                                                            is_verify = 1
                                                            WHERE is_verify = 2 AND
                                                            subscription_id = ? AND 
                                                            invoice_id = ?',
                [$hosted_invoice_url,
                $subscription,
                $invoice_id]);

               //subscriptions "Stripe_Payment"
               DB::update('UPDATE subscriptions SET
                                               payment_date = ?,
                                               activation_time = ?,
                                               expiration_time = ?
                                                WHERE user_id = ?
                                                AND transaction_id = ?
                                                AND is_active = 1',
                 [
                   $period_start,
                   $period_start,
                   $period_end,
                   $user_id,
                   $subscription]);

               //payment_status_master
               DB::update('UPDATE payment_status_master SET
                         expiration_time = ?
                         WHERE user_id = ? AND txn_id = ? AND is_active = 1',
                 [
                   $period_end,
                   $user_id,
                   $subscription,
                 ]);

              DB::commit();
             $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

              $activation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($period_start,$this->country_code);
              $next_billing_date_local = (New ImageController())->convertUTCDateTimeInToLocal($period_end,$this->country_code);

              $period_start_date = new DateTime($period_start);
              $period_start_format = $period_start_date->format('M d, Y H:i:s T');

              $template = 'payment_successful';
              $subject = 'PhotoADKing: Payment Received For Subscription Renewal';
              $message_body = array(
                'message' => 'Your subscription for <b>' . $plan_nickname . '</b> has been renewed on <b>' . $period_start_format . '</b>.
Thanks for renewing the subscription for <b>' . $plan_nickname . '</b>. We hope you are enjoying the PhotoADKing.',
                'subscription_name' => $plan_nickname,
                'txn_type' => 'Subscription[S]',
                'subscr_id' => $subscription,
                'first_name' => $user_detail->first_name,
                'payment_received_from' => $user_detail->first_name . ' (' . $user_detail->email_id . ')',
                'total_amount' => $plan_amount,
                'mc_currency' => $currency,
                'payer_email' => $user_detail->email_id,
                'payment_status' => $status,
                'activation_date' => $period_start,
                'next_billing_date' => ($period_end != NULL) ? $period_end : 'NA',
                'activation_date_local' => $activation_date_local,
                'next_billing_date_local' => ($next_billing_date_local != NULL) ? $next_billing_date_local : '',
                'invoice_url' => ($hosted_invoice_url != NULL) ? $hosted_invoice_url : 'NA'
              );
              $api_name = 'stripeSubscriptionUpdateEvents';
              $api_description = 'auto-renew subscription.';

//                Log::debug('handleCustomerSubscriptionUpdated (Payment Received For Subscription Renewal) : ', ['message_body' => $message_body]);
              $this->dispatch(new EmailJob($user_id, $user_detail->email_id, $subject, $message_body, $template, $api_name, $api_description));


            }
         }
        }
      } else {
        Log::error('stripeInvoiceEvents -> handleInvoiceSucceeded : This is not a invoice object.', [$invoice]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handleInvoiceSucceeded",$e);
//      Log::error("handleInvoiceSucceeded : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

  function handlePaymentActionRequired($invoice)
  {
//    Log::info('handlePaymentActionRequired : ',[$invoice]);
    try {
      if (isset($invoice ['object']) == 'invoice') {
        $invoice_id = $invoice->id;
        $verify_invoice = DB::select('SELECT id,is_active FROM stripe_subscription_master WHERE invoice_id = ?', [$invoice_id]);

           $stripe_sub_id = $verify_invoice[0]->id;
           $hosted_invoice_url = $invoice->hosted_invoice_url;

           DB::beginTransaction();
//           $data = DB::update('UPDATE stripe_payment_status AS sps
//                      SET sps.authenticate_url = ?
//                      WHERE sps.stripe_subscription_id = ? ',[$hosted_invoice_url,$stripe_sub_id]);
           DB::commit();
//           Log::info('$hosted_invoice_url : ',[$hosted_invoice_url]);

      } else {
        Log::error('stripeInvoiceEvents -> handlePaymentActionRequired : This is not a invoice object.', [$invoice]);
      }
    } catch (Exception $e) {
      (new ImageController())->logs("handlePaymentActionRequired",$e);
//      Log::error("handlePaymentActionRequired : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }

}
