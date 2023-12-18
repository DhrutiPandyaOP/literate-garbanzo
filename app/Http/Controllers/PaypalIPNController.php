<?php
//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.
//
// Title:            PhotoADKing
// File:             PaypalIPNController.php
// Since:            29-10-2017
//
// Author:           Pinal Patel
// Email:            rushita.optimumbrew@gmail.com
//
//////////////////////////////////////////////////////////////////////////////
namespace App\Http\Controllers;

use App\Jobs\EmailJob;
use DateTime;
use Exception;
use Log;
use App\Libraries\IPNListener;
use DB;
use App\Http\Controllers\RegisterController;
use Config;

class PaypalIPNController extends Controller
{

    private $country_code;

    public function paypalIpn()
    {

        try{
            $paypal_response_data = json_encode($_POST);
            if($paypal_response_data == NULL){
                Log::error('paypalIpn received null response from PayPal');
            }
        } catch (Exception $e) {
            (new ImageController())->logs("paypalIpn received null response from PayPal",$e);
//            Log::error("paypalIpn received null response from PayPal: ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

        Log::info('paypalipn_request_post_data : ',['response_array'=>$_POST]);
        try{
          $paypal_response_data = json_encode($_POST);
          DB::beginTransaction();
          DB::insert('INSERT INTO paypalipn_response(paypal_response) VALUES(?)',[$paypal_response_data]);
          DB::commit();
        } catch (Exception $e) {
            (new ImageController())->logs("paypalIpn unable to add ipn response into db",$e);
//          Log::error("paypalIpn unable to add ipn response into db: ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
          exit(0);
        }


        $listener = new IPNListener();
        $listener->use_sandbox = Config::get('constant.USE_SANDBOX');//true;
        //Log::error('paypalIpn',["Exception"]);


        try {
            $verified = $listener->processIpn();

            $report = $listener->getTextReport();

//            Log::info('-----New IPN Call-----', ['Report' => $report]);

        } catch (Exception $e) {
            (new ImageController())->logs("paypalIpn",$e);
//            Log::error("paypalIpn : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            exit(0);
        }

        if ($verified) {
            $data = $_POST;
            Log::info('-----new payment post data (verified)-----', ['Data' => $data]);

            /** Convert user id (numeric) from uuid */
            if (isset($data['custom'])) {
              $custom_data = $data['custom'];
              $user_id = $custom_data;//->user_id;
              if (!is_numeric($user_id)) {
                $user_detail = DB::select('SELECT id FROM user_master WHERE uuid=?', [$user_id]);
                if (count($user_detail) > 0) {
                  $data['custom'] = $user_detail[0]->id;
                }
              }
            }

            /** Convert payment date in to UTC format ex: 20:28:48 Mar 02, 2021 PST to 2021-03-03 20:28:48 */
            if (isset($data['payment_date'])) {
              $data['payment_date'] = date("Y-m-d H:i:s", strtotime($data['payment_date']));
            }
            if (isset($data['subscr_date'])) {
              $data['subscr_date'] = date("Y-m-d H:i:s", strtotime($data['subscr_date']));
            }

            if (isset($data['residence_country'])) {
              $this->country_code = $data['residence_country'];
            }

            if (isset($_POST['txn_type'])) {
                $txn_type = $data['txn_type'];
                if (strcmp($txn_type, 'subscr_signup') == 0) {
                    if (isset($data['custom'])) {
                        $custom_data = json_decode($data['custom']);//get custom parameters passed with button code
                        //Log::info('paypalIpn : ',['custom_data' => $custom_data]);
                        $user_id = $custom_data;//->user_id;

                        $subscription_date = isset($data['subscr_date']) ? $data['subscr_date'] : date('Y-m-d H:i:s');


                        $subscr_type = $data['item_number'];
                        $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                        $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                        $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                        $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                        if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {
                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Month");
                            $expires = $date->format('Y-m-d H:i:s');

                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
                        } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Year");
                            $expires = $date->format('Y-m-d H:i:s');
                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
                        } else {
                            $expires = NULL;
                        }

                        $subscr_date_local = (New ImageController())->convertUTCDateTimeInToLocal($data['subscr_date'],$this->country_code);
                        $expires_time_local = (New ImageController())->convertUTCDateTimeInToLocal($expires,$this->country_code);

                        $txn = array(
                            'txn_id' => (isset($data['txn_id']) ? $data['txn_id'] : 'NA'),
                            'user_id' => $user_id,
                            'txn_type' => $txn_type,
                            'paypal_id' => $data['subscr_id'],
                            'subscr_date' => $data['subscr_date'],
                            'subscr_date_local' => $subscr_date_local,
                            'mc_amount3' => $data['mc_amount3'],
                            'payer_email' => $data['payer_email'],
                            'mc_currency' => $data['mc_currency'],
                            'period1' => (isset($data['period1']) ? $data['period1'] : 'NA'),
                            'expires' => $expires,//date('Y-m-d H:i:s', strtotime('+14 day')),
                            'expires_local' => $expires_time_local,
                            'payment_status' => (isset($data['payment_status']) ? $data['payment_status'] : 'NA'),
                            'subscr_type' => $data['item_number'],
                            'paypal_response' => json_encode($data),
                            'create_time' => date('Y-m-d H:i:s'),
                            'item_name' => (isset($data['item_name']) ? $data['item_name'] : 'NA'),
                            'first_name' => (isset($data['first_name']) ? $data['first_name'] : 'NA')
                        );

                        $this->updatePaymentDetailForSignUp($user_id, $txn);
                        $this->logPaypalIPN($user_id, $txn);

                        Log::debug('paypalIpn-Verified 1: ', ['txn_type' => "subscr_signup", "txt" => $txn]);
                    } else {
                        Log::error('paypalIpn-Verified 2: ', ['txn_type' => "subscr_signup", "txt" => "Custom Data Not found"]);
                    }

                } else if (strcmp($txn_type, 'subscr_payment') == 0) {

                    if (isset($data['custom'])) {
                        $custom_data = json_decode($data['custom']);
                        $user_id = isset($custom_data) ? $custom_data : 'NA';
                        $payment_status = $data['payment_status'];
                        $subscription_date = isset($data['payment_date']) ? $data['payment_date'] : date('Y-m-d H:i:s');


                        $subscr_type = $data['item_number'];
                        $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                        $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                        $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                        $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                        if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {
                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Month");
                            $expires = $date->format('Y-m-d H:i:s');

                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
                        } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Year");
                            $expires = $date->format('Y-m-d H:i:s');

                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
                        } else {
                            $expires = NULL;
                        }

                        $subscr_date_local = (New ImageController())->convertUTCDateTimeInToLocal($data['payment_date'],$this->country_code);
                        $expires_time_local = (New ImageController())->convertUTCDateTimeInToLocal($expires,$this->country_code);

                        $txn = array(
                            'txn_id' => $data['txn_id'],
                            'user_id' => $user_id,
                            'txn_type' => $txn_type,
                            'payment_status' => $payment_status,
                            'paypal_id' => $data['subscr_id'],
                            'subscr_date' => $data['payment_date'],
                            'subscr_date_local' => $subscr_date_local,
                            'mc_amount3' => $data['payment_gross'],
                            'payer_email' => $data['payer_email'],
                            'mc_currency' => $data['mc_currency'],
                            'period1' => (isset($data['period1']) ? $data['period1'] : 'NA'),
                            'expires' => $expires,//date('Y-m-d H:i:s', strtotime('+14 day')),
                            'expires_local' => $expires_time_local,
                            'subscr_type' => $data['item_number'],
                            'item_name' => (isset($data['item_name']) ? $data['item_name'] : 'NA'),
                            'first_name' => (isset($data['first_name']) ? $data['first_name'] : 'NA'),
                            'paypal_response' => json_encode($data),
                            'create_time' => date('Y-m-d H:i:s')
                        );

                        if (strcmp($payment_status, 'Completed') == 0) {
                            Log::debug('paypalIpn-Verified (Payment status : completed): ', ['payment_status' => $payment_status,'txn_type' => "subscr_payment", "txt" => $txn, "original_object_by_paypal" => $data]);
                            $this->updatePaymentDetailByUserID($user_id, $txn);
                        } else {
                            Log::debug('paypalIpn-Verified 3: ', ['txn_type' => "subscr_payment", "txt" => $txn]);
                        }
                        $this->logPaypalIPN($user_id, $txn);
                        Log::info('paypalIpn-Verified 4: ', ['txn_type' => "subscr_payment", "txt" => $txn]);
                    } else {
                        Log::error('paypalIpn-Verified 5: ', ['txn_type' => "subscr_payment", "txt" => "Custom Data Not found"]);
                    }

                } else if (strcmp($txn_type, 'subscr_cancel') == 0) {
                    if (isset($data['subscr_id'])) {
                        $paypal_id = $data['subscr_id'];
                        $user_id = "NA";
                        $payment_status = (isset($data['payment_status']) ? $data['payment_status'] : 'NA');
                        $subscription_date = isset($data['subscr_date']) ? $data['subscr_date'] : date('Y-m-d H:i:s');

                        $subscr_type = $data['item_number'];
                        $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                        $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                        $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                        $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                        if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {

                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Month");
                            $expires = $date->format('Y-m-d H:i:s');

                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
                        } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Year");
                            $expires = $date->format('Y-m-d H:i:s');

                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
                        } else {
                            $expires = NULL;
                        }

                        $txn = array(
                            'txn_id' => (isset($data['txn_id']) ? $data['txn_id'] : 'NA'),
                            'user_id' => $user_id,
                            'txn_type' => $txn_type,
                            'payment_status' => $payment_status,
                            'paypal_id' => (isset($data['subscr_id']) ? $data['subscr_id'] : 'NA'),
                            'subscr_date' => (isset($data['subscr_date']) ? $data['subscr_date'] : 'NA'),
                            'mc_amount3' => (isset($data['mc_amount3']) ? $data['mc_amount3'] : NULL),
                            'payer_email' => (isset($data['payer_email']) ? $data['payer_email'] : 'NA'),
                            'mc_currency' => (isset($data['mc_currency']) ? $data['mc_currency'] : 'NA'),
                            'period1' => (isset($data['period1']) ? $data['period1'] : 'NA'),
                            'expires' => $expires,//date('Y-m-d H:i:s', strtotime('+14 day')),
                            'subscr_type' => $data['item_number'],
                            'paypal_response' => json_encode($data),
                            'create_time' => date('Y-m-d H:i:s'),
                            'item_name' => (isset($data['item_name']) ? $data['item_name'] : 'NA'),
                            'first_name' => (isset($data['first_name']) ? $data['first_name'] : 'NA')
                        );

                        $this->cancelPaymentDetailByPaypalID($paypal_id, $txn);
                        $this->logPaypalIPN($user_id, $txn);
                        Log::info('paypalIpn-Verified 6: ', ['txn_type' => "subscr_cancel", "txt" => $txn]);
                    }
                } else if (strcmp($txn_type, 'subscr_failed') == 0) {
                    if (isset($data['subscr_id'])) {
                        $paypal_id = $data['subscr_id'];
                        $user_id = isset($data['custom']) ? $data['custom'] : 'NA';
                        $subscription_date = isset($data['subscr_date']) ? $data['subscr_date'] : date('Y-m-d H:i:s');

                        $subscr_type = $data['item_number'];
                        $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                        $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                        $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                        $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                        if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {

                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Month");
                            $expires = $date->format('Y-m-d H:i:s');


                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
                        } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

                            $date = new DateTime($subscription_date);
                            $date->modify("+1 Year");
                            $expires = $date->format('Y-m-d H:i:s');

                            //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
                        } else {
                            $expires = NULL;
                        }

                        $txn = array(
                            'txn_id' => (isset($data['txn_id']) ? $data['txn_id'] : 'NA'),
                            'user_id' => $user_id,
                            'txn_type' => $txn_type,
                            'paypal_id' => (isset($data['subscr_id']) ? $data['subscr_id'] : 'NA'),
                            'subscr_date' => (isset($data['subscr_date']) ? $data['subscr_date'] : 'NA'),
                            'mc_amount3' => (isset($data['mc_amount3']) ? $data['mc_amount3'] : NULL),
                            'payer_email' => (isset($data['payer_email']) ? $data['payer_email'] : 'NA'),
                            'mc_currency' => (isset($data['mc_currency']) ? $data['mc_currency'] : 'NA'),
                            'period1' => (isset($data['period1']) ? $data['period1'] : 'NA'),
                            'expires' => $expires,//date('Y-m-d H:i:s', strtotime('+14 day')),
                            'payment_status' => (isset($data['payment_status']) ? $data['payment_status'] : 'NA'),
                            'subscr_type' => $data['item_number'],
                            'paypal_response' => json_encode($data),
                            'create_time' => date('Y-m-d H:i:s'),
                            'item_name' => (isset($data['item_name']) ? $data['item_name'] : 'NA'),
                            'first_name' => (isset($data['first_name']) ? $data['first_name'] : 'NA'),
                            'payment_gross' => (isset($data['payment_gross']) ? $data['payment_gross'] : 0)
                        );

                        $this->logPaypalIPN($user_id, $txn);
                        Log::debug('paypalIpn-subscr_failed : ', ['txn_type' => 'subscr_failed' ,'paypal_id' => $paypal_id]);

                        if($user_id != 'NA' or $user_id != '')
                        {
                            $user_profile = (new LoginController())->getUserInfoByUserId($user_id);

                            $email_id = $user_profile->email_id;
                            $first_name = $user_profile->first_name;

                            /** When paypal payment failed due to some paypal reason after successfully completed transaction and user's plan activated.In this type of case we send one mail to admin so admin can change user role from admin panel*/
                            $is_subscription_active = DB::select('SELECT id FROM subscriptions WHERE is_active =1 AND user_id =? ',[$user_id]);
                            if(count($is_subscription_active) > 0 ){
                              $template = 'payment_failed';
                              $subject = 'PhotoADKing: Payment Failed After activation subscription';
                              $message_body = array(
                                'message' => 'User payment failed by paypal after successfully activated subscription.So please check and take any action on this user.',
                                'subscription_name' => $txn['item_name'],
                                'txn_id' => $txn['txn_id'],
                                'txn_type' => 'Subscription[P]',
                                'subscr_id' => $txn['paypal_id'],
                                'first_name' => $first_name,
                                'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                                'total_amount' => $txn['payment_gross'],
                                'mc_currency' => $txn['mc_currency'],
                                'payer_email' => $email_id,
                                'payment_status' => $txn['payment_status']
                              );
                              $api_name = 'paypalIpn';
                              $api_description = 'Subscription failed.';

                              $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');

                              $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                            }

                            $template = 'payment_failed';
                            $subject = 'PhotoADKing: Payment Failed';
                            $message_body = array(
                                'message' => 'Sorry, your payment has been failed. No charges were made. Following are the transaction details.',
                                'subscription_name' => $txn['item_name'],
                                'txn_id' => $txn['txn_id'],
                                'txn_type' => 'Subscription[P]',
                                'subscr_id' => $txn['paypal_id'],
                                'first_name' => $first_name,
                                'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                                'total_amount' => $txn['payment_gross'],
                                'mc_currency' => $txn['mc_currency'],
                                'payer_email' => $email_id,
                                'payment_status' => $txn['payment_status']
                            );
                            $api_name = 'paypalIpn';
                            $api_description = 'Subscription failed.';

                            $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                        }
                        else{
                            Log::debug('paypalIpn-subscr_failed did not get user_id : ', ['data' => $data]);

                            $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
                            $template = 'simple';
                            $subject = 'PhotoADKing: PayPal IPN Failed';
                            $message_body = array(
                                'message' => '<p>API "paypalIpn" could not fetch user_id from IPN response in case of transaction type is subscr_failed. Please check the logs.</p>',
                                'user_name' => 'Admin'
                            );
                            $api_name = 'paypalIpn';
                            $api_description = 'Get INVALID from IPN.';
                            $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                        }



                    } else {
                        Log::error('paypalIpn-subscr_failed did not get any subscription data from IPN: ', ['data' => $data]);

                        $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
                        $template = 'simple';
                        $subject = 'PhotoADKing: PayPal IPN Failed';
                        $message_body = array(
                            'message' => '<p>API "paypalIpn" could not fetch subscription details from IPN response in case of transaction type is subscr_failed. Please check the logs.</p>',
                            'user_name' => 'Admin'
                        );
                        $api_name = 'paypalIpn';
                        $api_description = 'Get INVALID from IPN.';
                        $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                    }

                }
            }
            // IPN response was "VERIFIED"
            // Log::error('paypalIpn',['IsVerified'=>"VERIFIED"]);
        } else {
            // IPN response was "INVALID"
            Log::error('paypalIpn', ['IsVerified' => "INVALID"]);
            $data = $_POST;
            Log::info('-----new payment post data (INVALID)-----', ['Report' => $data]);
            $txn_type = $data['txn_type'];
            $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
            if (isset($_POST['txn_type'])) {

                if (isset($data['subscr_id'])) {
                    $paypal_id = $data['subscr_id'];
                    $user_id = isset($data['custom']) ? $data['custom'] : 'NA';
                    $subscription_date = isset($data['subscr_date']) ? $data['subscr_date'] : date('Y-m-d H:i:s');


                    $subscr_type = $data['item_number'];
                    $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                    $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                    $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                    $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                    if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {

                        $date = new DateTime($subscription_date);
                        $date->modify("+1 Month");
                        $expires = $date->format('Y-m-d H:i:s');

                        //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
                    } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

                        $date = new DateTime($subscription_date);
                        $date->modify("+1 Year");
                        $expires = $date->format('Y-m-d H:i:s');

                        //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
                    } else {
                        $expires = NULL;
                    }

                    $txn = array(
                        'txn_id' => (isset($data['txn_id']) ? $data['txn_id'] : 'NA'),
                        'user_id' => $user_id,
                        'txn_type' => $txn_type,
                        'paypal_id' => (isset($data['subscr_id']) ? $data['subscr_id'] : 'NA'),
                        'subscr_date' => (isset($data['subscr_date']) ? $data['subscr_date'] : 'NA'),
                        'mc_amount3' => (isset($data['mc_amount3']) ? $data['mc_amount3'] : NULL),
                        'payer_email' => (isset($data['payer_email']) ? $data['payer_email'] : 'NA'),
                        'mc_currency' => (isset($data['mc_currency']) ? $data['mc_currency'] : 'NA'),
                        'period1' => (isset($data['period1']) ? $data['period1'] : 'NA'),
                        'expires' => $expires,//date('Y-m-d H:i:s', strtotime('+14 day')),
                        'payment_status' => (isset($data['payment_status']) ? $data['payment_status'] : 'NA'),
                        'subscr_type' => $data['item_number'],
                        'paypal_response' => json_encode($data),
                        'create_time' => date('Y-m-d H:i:s'),
                        'item_name' => (isset($data['item_name']) ? $data['item_name'] : 'NA'),
                        'first_name' => (isset($data['first_name']) ? $data['first_name'] : 'NA'),
                        'payment_gross' => (isset($data['payment_gross']) ? $data['payment_gross'] : 0)
                    );

                    $this->logPaypalIPN($user_id, $txn);
                    Log::debug('paypalIpn-Invalid subscr_failed : ', ['txn_type' => $data['txn_type'], 'paypal_id' => $paypal_id]);

                    if($user_id != 'NA' or $user_id != '')
                    {
                        $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
                        $email_id =$user_profile->email_id;
                        $first_name = $user_profile->first_name;

                        $template = 'payment_failed';
                        $subject = 'PhotoADKing: Payment Failed';
                        $message_body = array(
                            'message' => 'Transaction detail is not stored in the system by IPN API (INVALID). Please verify the transaction from admin portal. Following are the transaction details.',
                            'subscription_name' => $txn['item_name'],
                            'txn_id' => $txn['txn_id'],
                            'txn_type' => 'Subscription[P]',
                            'subscr_id' => $txn['paypal_id'],
                            'first_name' => $first_name,
                            'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                            'total_amount' => $txn['payment_gross'],
                            'mc_currency' => $txn['mc_currency'],
                            'payer_email' => $email_id,
                            'payment_status' => $txn['payment_status']
                        );
                        $api_name = 'paypalIpn';
                        $api_description = 'Subscription failed in case of getting INVALID response from IPN.';

                        $this->dispatch(new EmailJob($user_id, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                    }
                    else{
                        Log::error('paypalIpn-subscr_failed did not get any user data from IPN: ', ['data' => $data]);

                        $template = 'simple';
                        $subject = 'PhotoADKing: PayPal IPN Failed';
                        $message_body = array(
                            'message' => '<p>API "paypalIpn" could not fetch user_id from IPN response in case of getting an INVALID response. Please check the logs.</p>',
                            'user_name' => 'Admin'
                        );
                        $api_name = 'paypalIpn';
                        $api_description = 'Get INVALID from IPN.';
                        $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                    }

                } else {
                    Log::error('paypalIpn-subscr_failed did not get subscr_id from IPN: ', ['data' => $data]);

                    $template = 'simple';
                    $subject = 'PhotoADKing: PayPal IPN Failed';
                    $message_body = array(
                        'message' => '<p>API "paypalIpn" could not fetch subscription details from IPN response in case of getting an INVALID response. Please check the logs.</p>',
                        'user_name' => 'Admin'
                    );
                    $api_name = 'paypalIpn';
                    $api_description = 'Get INVALID from IPN.';
                    $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                }

            }else
            {
                Log::error('paypalIpn-subscr_failed did not get any txn_type from IPN: ', ['data' => $data]);

                $template = 'simple';
                $subject = 'PhotoADKing: PayPal IPN Failed';
                $message_body = array(
                    'message' => '<p>IPN received INVALID response. Please check the logs.</p>',
                    'user_name' => 'Admin'
                );
                $api_name = 'paypalIpn';
                $api_description = 'Get INVALID from IPN.';
                $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));


            }


        }
    }

    public function updatePaymentDetailForSignUp($user_id, array $txn)
    {
        try {

            $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
            $email_id = $user_profile->email_id;
            $first_name = $user_profile->first_name;

            Log::info('paypalIpn-updatePaymentDetailForSignUp', ["user_id"=>$user_id,'txn' => $txn]);
            //$result = DB::table('subscriptions')->select(array('id', 'user_id', 'transaction_id', 'subscr_type', 'total_amount', 'expiration_time'))->where('user_id', $user_id)->get();

            $result = DB::select('SELECT
                      id,
                      user_id,
                      transaction_id,
                      paypal_id,
                      subscr_type,
                      total_amount,
                      payment_date,
                      expiration_time,
                      remaining_days,
                      days_to_add,
                      final_expiration_time,
                      cancellation_date
                      FROM subscriptions
                      WHERE user_id = ?
                      ORDER BY id DESC', [$user_id]);

            //DB::select('SELECT * FROM subscriptions WHERE ');
            $db_activation_time = date("Y-m-d H:i:s");
            if (count($result) == 0) {

                // Add new SignUp entry
                $days_to_add = 0;
                $final_expiration_time = $txn['expires'];
                //identify payment Mode
                $payment_mode = "PAYPAL";
                $product_details = DB::select('SELECT id AS product_id,
                name AS product_name,
                discount_percentage
                FROM subscription_product_details
                WHERE is_applied = 1');

                $product_id = $product_details[0]->product_id;

                DB::beginTransaction();

                DB::insert('INSERT INTO subscriptions
                            (user_id,
                             transaction_id,
                             paypal_id,
                             payment_mode,
                             subscr_type,
                             product_id,
                             txn_type,
                             payment_status,
                             total_amount,
                             paypal_response,
                             payment_date,
                             activation_time,
                             expiration_time,
                             days_to_add,
                             final_expiration_time,
                             create_time)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    [$user_id,
                        $txn['txn_id'],
                        $txn['paypal_id'],
                        $payment_mode,
                        $txn['subscr_type'],
                        $product_id,
                        $txn['txn_type'],
                        $txn['payment_status'],
                        $txn['mc_amount3'],
                        $txn['paypal_response'],
                        $txn['subscr_date'],
                        $db_activation_time,
                        $txn['expires'],
                        $days_to_add,
                        $final_expiration_time,
                        $txn['create_time']
                    ]);

                DB::commit();
                $this->updateUserRole($txn['subscr_type'], $user_id);

                if ($txn['payment_status'] == "Completed") {
                    $this->updatePaymentStatus($txn['txn_id'], 1, 1);
                } else {
                    $this->updatePaymentStatus($txn['txn_id'], 0, 0);
                }

                $template = 'payment_successful';
                $subject = 'PhotoADKing: Subscribe The New Plan';
                $message_body = array(
                    'message' => 'Thank you for purchasing subscription for the ' . $txn['item_name'] . '.',
                    'subscription_name' => $txn['item_name'],
                    'txn_id' => $txn['txn_id'],
                    'txn_type' => 'Subscription[P]',
                    'subscr_id' => $txn['paypal_id'],
                    'first_name' => $first_name,
                    'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                    'total_amount' => $txn['mc_amount3'],
                    'mc_currency' => $txn['mc_currency'],
                    'payer_email' => $email_id,
                    'payment_status' => $txn['payment_status'],
                    'activation_date' => $txn['subscr_date'],
                    'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                    'activation_date_local' => $txn['subscr_date_local'],
                    'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                );
                $api_name = 'paypalIpn';
                $api_description = 'subscribe a new subscription.';

                $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));


            } else {

                //Update Subscription
                $subscr_type = $result[0]->subscr_type;
                $remaining_days = $result[0]->remaining_days;
                $db_expiration_time = $result[0]->expiration_time;
                $db_cancellation_date = $result[0]->cancellation_date;
                $old_amount = $result[0]->total_amount;
                $days_to_add = $result[0]->days_to_add;
                $db_transaction_id = $result[0]->transaction_id;
                $db_paypal_id = $result[0]->paypal_id;

                if ($subscr_type == $txn['subscr_type'] and $db_paypal_id == $txn['paypal_id']) {
                    Log::info('Subscription type : ', ['subscr_type' => $txn['subscr_type']]);
                    $db_row_id = $result[0]->id;

                    //$final_expiration_time = $this->getFinalExpirationTime($db_expiration_time, $days_to_add);
                    $final_expiration_time = $this->getFinalExpirationTime($txn['expires'], $days_to_add); //get expiration time with adding remaining days

                    if (is_null($db_transaction_id) || strcmp($db_transaction_id, 'NA') == 0) {

                        // Update Form signup to first payment
                        DB::beginTransaction();
                        DB::update('UPDATE subscriptions SET
                                                            paypal_id = ?,
                                                            txn_type = ?,
                                                            activation_time = ?,
                                                            expiration_time = ?,
                                                            final_expiration_time = ?,
                                                            response_message= ?
                                                            WHERE id = ? ',
                            [$txn['paypal_id'],
                                $txn['txn_type'],
                                $db_activation_time,
                                $txn['expires'],
                                $final_expiration_time,
                                'Second SignUp: Error',
                                $db_row_id]);

                        DB::commit();
                    } else {

                        // Update Form signup to first payment
                        DB::beginTransaction();
                        DB::update('UPDATE subscriptions SET
                                                            paypal_id = ?,
                                                            txn_type = ?,
                                                            activation_time = ?,
                                                            expiration_time = ?,
                                                            final_expiration_time = ?,
                                                            response_message= ?
                                                            WHERE id = ? ',
                            [$txn['paypal_id'],
                                $txn['txn_type'],
                                $db_activation_time,
                                $txn['expires'],
                                $final_expiration_time,
                                'Signup After Payment OR Re-subscribe',
                                $db_row_id]);

                        DB::commit();
                    }
                    $this->updateUserRole($txn['subscr_type'], $user_id);

                    if ($txn['payment_status'] == "Completed") {
                        $this->updatePaymentStatus($txn['txn_id'], 1, 1);
                    } else {
                        $this->updatePaymentStatus($txn['txn_id'], 0, 0);
                    }

                    //if($result[0]->payment_date == $txn['subscr_date']){
                    if (date("Y-m-d", strtotime($result[0]->payment_date)) == date("Y-m-d", strtotime($txn['subscr_date']))) {


                        $template = 'payment_successful';
                        $subject = 'PhotoADKing: Payment Received';
                        $message_body = array(
                            'message' => 'Your payment received successfully. Following are the transaction details.',
                            'subscription_name' => $txn['item_name'],
                            'txn_id' => $txn['txn_id'],
                            'txn_type' => 'Subscription[P]',
                            'subscr_id' => $txn['paypal_id'],
                            'first_name' => $first_name,
                            'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                            'total_amount' => $txn['mc_amount3'],
                            'mc_currency' => $txn['mc_currency'],
                            'payer_email' => $email_id,
                            'payment_status' => $txn['payment_status'],
                            'activation_date' => $txn['subscr_date'],
                            'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                            'activation_date_local' => $txn['subscr_date_local'],
                            'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                        );
                        $api_name = 'paypalIpn';
                        $api_description = 'subscribe a new subscription.';

                        $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    } else {

                        $subscr_date = new DateTime($txn['subscr_date']);
                        $subscr_date_format = $subscr_date->format('M d, Y H:i:s T');

                        $template = 'payment_successful';
                        $subject = 'PhotoADKing: Payment Received For Subscription Renewal';
                        $message_body = array(
                            'message' => 'Your subscription for <b>' . $txn['item_name'] . '</b> has been renewed on <b>' . $subscr_date_format . '</b>.
Thanks for renewing the subscription for <b>' . $txn['item_name'] . '</b>. We hope you are enjoying the PhotoADKing.',
                            'subscription_name' => $txn['item_name'],
                            'txn_id' => $txn['txn_id'],
                            'txn_type' => 'Subscription[P]',
                            'subscr_id' => $txn['paypal_id'],
                            'first_name' => $first_name,
                            'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                            'total_amount' => $txn['mc_amount3'],
                            'mc_currency' => $txn['mc_currency'],
                            'payer_email' => $email_id,
                            'payment_status' => $txn['payment_status'],
                            'activation_date' => $txn['subscr_date'],
                            'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                            'activation_date_local' => $txn['subscr_date_local'],
                            'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                        );
                        $api_name = 'paypalIpn';
                        $api_description = 'subscribe a new subscription.';

                        $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));
                      }

                } else {


                    $expiry_detail = $this->getRemainingDays($db_cancellation_date, $db_expiration_time, $remaining_days, $old_amount, $txn['mc_amount3'], $subscr_type, $txn['subscr_type'], $txn['expires']);
                    $final_expiration_time = $expiry_detail['final_expiration_time'];
                    $days_to_add = $expiry_detail['days_to_add'];
                    Log::info('updatePaymentDetailForSignUp Expiration : ', ['final_expiration_time' => $final_expiration_time, 'days_to_add' => $days_to_add]);
                    // Add new SignUp entry

                    //identify payment Mode
                    $payment_mode = "PAYPAL";
                    $product_details = DB::select('SELECT id AS product_id,
                  name AS product_name,
                  discount_percentage
                  FROM subscription_product_details
                  WHERE is_applied = 1');

                    $product_id = $product_details[0]->product_id;


                    DB::beginTransaction();

                    DB::insert('INSERT INTO subscriptions
                            (user_id,
                             transaction_id,
                             paypal_id,
                             payment_mode,
                             subscr_type,
                             product_id,
                             txn_type,
                             payment_status,
                             total_amount,
                             paypal_response,
                             payment_date,
                             activation_time,
                             expiration_time,
                             days_to_add,
                             final_expiration_time,
                             create_time)
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                        [$user_id,
                            $txn['txn_id'],
                            $txn['paypal_id'],
                            $payment_mode,
                            $txn['subscr_type'],
                            $product_id,
                            $txn['txn_type'],
                            $txn['payment_status'],
                            $txn['mc_amount3'],
                            $txn['paypal_response'],
                            $txn['subscr_date'],
                            $db_activation_time,
                            $txn['expires'],
                            $days_to_add,
                            $final_expiration_time,
                            $txn['create_time']
                        ]);

                    DB::commit();
                    $this->updateUserRole($txn['subscr_type'], $user_id);
                    if ($txn['payment_status'] == "Completed") {
                        $this->updatePaymentStatus($txn['txn_id'], 1, 1);
                    } else {
                        $this->updatePaymentStatus($txn['txn_id'], 0, 0);
                    }

                    $template = 'payment_successful';
                    $subject = 'PhotoADKing: Subscription Plan Changed';
                    $message_body = array(
                        'message' => 'Your subscription plan changed successfully. Remaining days are added into your new subscription. Following are the transaction details.',
                        'subscription_name' => $txn['item_name'],
                        'txn_id' => $txn['txn_id'],
                        'txn_type' => 'Subscription[P]',
                        'subscr_id' => $txn['paypal_id'],
                        'first_name' => $first_name,
                        'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                        'total_amount' => $txn['mc_amount3'],
                        'mc_currency' => $txn['mc_currency'],
                        'payer_email' => $email_id,
                        'payment_status' => $txn['payment_status'],
                        'activation_date' => $txn['subscr_date'],
                        'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                        'activation_date_local' => $txn['subscr_date_local'],
                        'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                    );
                    $api_name = 'paypalIpn';
                    $api_description = 'Subscription plan changed.';
                    $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                }

            }
        } catch (Exception $e) {
            (new ImageController())->logs("updatePaymentDetailForSignUp",$e);
//            Log::error("updatePaymentDetailForSignUp : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

    }

    public function updatePaymentDetailByUserID($user_id, array $txn)
    {
        Log::info('updatePaymentDetailByUserID', ["user_id"=>$user_id, 'txn' => $txn]);
        $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
        $email_id = $user_profile->email_id;
        $first_name = $user_profile->first_name;

        $result = DB::select('SELECT
                      id,
                      user_id,
                      transaction_id,
                      paypal_id,
                      subscr_type,
                      total_amount,
                      payment_date,
                      expiration_time,
                      remaining_days,
                      days_to_add,
                      final_expiration_time,
                      cancellation_date
                      FROM subscriptions
                      WHERE user_id = ?
                      ORDER BY id DESC', [$user_id]);

        $db_activation_time = date("Y-m-d H:i:s");
        if (count($result) == 0) {

            // Add new SignUp entry
            //identify payment Mode
            $payment_mode = "PAYPAL";
            $days_to_add = 0;
            $final_expiration_time = $txn['expires'];

            $product_details = DB::select('SELECT id AS product_id,
                  name AS product_name,
                  discount_percentage
                  FROM subscription_product_details
                  WHERE is_applied = 1');

            $product_id = $product_details[0]->product_id;


          DB::beginTransaction();

            DB::insert('INSERT INTO subscriptions
                            (user_id,
                             transaction_id,
                             paypal_id,
                             payment_mode,
                             subscr_type,
                             product_id,
                             txn_type,
                             payment_status,
                             total_amount,
                             paypal_response,
                             payment_date,
                             activation_time,
                             expiration_time,
                             days_to_add,
                             final_expiration_time,
                             create_time
                             )
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                [$user_id,
                    $txn['txn_id'],
                    $txn['paypal_id'],
                    $payment_mode,
                    $txn['subscr_type'],
                    $product_id,
                    $txn['txn_type'],
                    $txn['payment_status'],
                    $txn['mc_amount3'],
                    $txn['paypal_response'],
                    $txn['subscr_date'],
                    $db_activation_time,
                    $txn['expires'],
                    $days_to_add,
                    $final_expiration_time,
                    $txn['create_time']]);

            DB::commit();
            $this->updateUserRole($txn['subscr_type'], $user_id);
            if ($txn['payment_status'] == "Completed") {
                $this->updatePaymentStatus($txn['txn_id'], 1, 1);
            } else {
                $this->updatePaymentStatus($txn['txn_id'], 0, 0);
            }

            $template = 'payment_successful';
            $subject = 'PhotoADKing: Subscribe The New Plan';
            $message_body = array(
                'message' => 'Your payment received successfully. Following are the transaction details.',
                'subscription_name' => $txn['item_name'],
                'txn_id' => $txn['txn_id'],
                'txn_type' => 'Subscription[P]',
                'subscr_id' => $txn['paypal_id'],
                'first_name' => $first_name,
                'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                'total_amount' => $txn['mc_amount3'],
                'mc_currency' => $txn['mc_currency'],
                'payer_email' => $email_id,
                'payment_status' => $txn['payment_status'],
                'activation_date' => $txn['subscr_date'],
                'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                'activation_date_local' => $txn['subscr_date_local'],
                'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
            );
            $api_name = 'paypalIpn';
            $api_description = 'subscribe a new subscription.';
            $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

        } else {

            //Update Subscription
            $subscr_type = $result[0]->subscr_type;
            $remaining_days = $result[0]->remaining_days;
            $db_expiration_time = $result[0]->expiration_time;
            $db_cancellation_date = $result[0]->cancellation_date;
            $days_to_add = $result[0]->days_to_add;
            $db_transaction_id = $result[0]->transaction_id;
            $db_paypal_id = $result[0]->paypal_id;

            if ($subscr_type == $txn['subscr_type'] and $db_paypal_id == $txn['paypal_id']) {
                $db_row_id = $result[0]->id;
                //$final_expiration_time = $this->getFinalExpirationTime($db_expiration_time, $days_to_add);
                $final_expiration_time = $this->getFinalExpirationTime($txn['expires'], $days_to_add); //get expiration time with adding remaining days
                if (is_null($db_transaction_id) || strcmp($db_transaction_id, 'NA') == 0) {

                    // Update Form signup to first payment
                    DB::beginTransaction();
                    DB::update('UPDATE subscriptions SET
                                                            transaction_id=?,
                                                            paypal_id = ?,
                                                            txn_type = ?,
                                                            activation_time = ?,
                                                            expiration_time = ?,
                                                            final_expiration_time = ?,
                                                            response_message= ?
                                                            WHERE id = ? ',
                        [$txn['txn_id'],
                            $txn['paypal_id'],
                            $txn['txn_type'],
                            $db_activation_time,
                            $txn['expires'],
                            $final_expiration_time,
                            'Payment After Signup',
                            $db_row_id]);

                    DB::commit();


                } else {

                    // Update Form signup to first payment
                    DB::beginTransaction();
                    DB::update('UPDATE subscriptions SET
                                                            transaction_id=?,
                                                            paypal_id = ?,
                                                            txn_type = ?,
                                                            activation_time = ?,
                                                            expiration_time = ?,
                                                            final_expiration_time = ?,
                                                            response_message= ?
                                                            WHERE id = ? ',
                        [$txn['txn_id'],
                            $txn['paypal_id'],
                            $txn['txn_type'],
                            $db_activation_time,
                            $txn['expires'],
                            $final_expiration_time,
                            'Recursive Payment',
                            $db_row_id]);

                    DB::commit();
                }
                $this->updateUserRole($txn['subscr_type'], $user_id);
                if ($txn['payment_status'] == "Completed") {
                    $this->updatePaymentStatus($txn['txn_id'], 1, 1);
                } else {
                    $this->updatePaymentStatus($txn['txn_id'], 0, 0);
                }


                if (date("Y-m-d", strtotime($result[0]->payment_date)) == date("Y-m-d", strtotime($txn['subscr_date']))) {

                    $template = 'payment_successful';
                    $subject = 'PhotoADKing: Payment Received';
                    $message_body = array(
                        'message' => 'Your payment received successfully. Following are the transaction details.',
                        'subscription_name' => $txn['item_name'],
                        'txn_id' => $txn['txn_id'],
                        'txn_type' => 'Subscription[P]',
                        'subscr_id' => $txn['paypal_id'],
                        'first_name' => $first_name,
                        'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                        'total_amount' => $txn['mc_amount3'],
                        'mc_currency' => $txn['mc_currency'],
                        'payer_email' => $email_id,
                        'payment_status' => $txn['payment_status'],
                        'activation_date' => $txn['subscr_date'],
                        'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                        'activation_date_local' => $txn['subscr_date_local'],
                        'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                    );
                    $api_name = 'paypalIpn';
                    $api_description = 'subscribe a new subscription.';
                    $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                } else {

                    $subscr_date = new DateTime($txn['subscr_date']);
                    $subscr_date_format = $subscr_date->format('M d, Y H:i:s T');

                    $template = 'payment_successful';
                    $subject = 'PhotoADKing: Payment Received For Subscription Renewal';
                    $message_body = array(
                        'message' => 'Your subscription for <b>' . $txn['item_name'] . '</b> has been renewed on <b>' . $subscr_date_format . '</b>.
Thanks for renewing the subscription for <b>' . $txn['item_name'] . '</b>. We hope you are enjoying the PhotoADKing.',
                        'subscription_name' => $txn['item_name'],
                        'txn_id' => $txn['txn_id'],
                        'txn_type' => 'Subscription[P]',
                        'subscr_id' => $txn['paypal_id'],
                        'first_name' => $first_name,
                        'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                        'total_amount' => $txn['mc_amount3'],
                        'mc_currency' => $txn['mc_currency'],
                        'payer_email' => $email_id,
                        'payment_status' => $txn['payment_status'],
                        'activation_date' => $txn['subscr_date'],
                        'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                        'activation_date_local' => $txn['subscr_date_local'],
                        'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                    );
                    $api_name = 'paypalIpn';
                    $api_description = 'auto-renew subscription.';

                    Log::debug('updatePaymentDetailByUserID (Payment Received For Subscription Renewal) : ',['message_body' => $message_body]);
                    $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                }


            } else {

                $old_amount = $result[0]->total_amount;

                $expiry_detail = $this->getRemainingDays($db_cancellation_date, $db_expiration_time, $remaining_days, $old_amount, $txn['mc_amount3'], $subscr_type, $txn['subscr_type'], $txn['expires']);
                $final_expiration_time = $expiry_detail['final_expiration_time'];
                $days_to_add = $expiry_detail['days_to_add'];
                Log::info('updatePaymentDetailByUserID Expiration : ', ['final_expiration_time' => $final_expiration_time, 'days_to_add' => $days_to_add]);

                $payment_mode = "PAYPAL";

                $product_details = DB::select('SELECT id AS product_id,
                    name AS product_name,
                    discount_percentage
                    FROM subscription_product_details
                    WHERE is_applied = 1');

                $product_id = $product_details[0]->product_id;

                DB::beginTransaction();

                DB::insert('INSERT INTO subscriptions
                            (user_id,
                             transaction_id,
                             paypal_id,
                             payment_mode,
                             subscr_type,
                             product_id,
                             txn_type,
                             payment_status,
                             total_amount,
                             paypal_response,
                             payment_date,
                             activation_time,
                             expiration_time,
                             days_to_add,
                             final_expiration_time,
                             create_time
                             )
                             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                    [$user_id,
                        $txn['txn_id'],
                        $txn['paypal_id'],
                        $payment_mode,
                        $txn['subscr_type'],
                        $product_id,
                        $txn['txn_type'],
                        $txn['payment_status'],
                        $txn['mc_amount3'],
                        $txn['paypal_response'],
                        $txn['subscr_date'],
                        $db_activation_time,
                        $txn['expires'],
                        $days_to_add,
                        $final_expiration_time,
                        $txn['create_time']]);

                DB::commit();
                $this->updateUserRole($txn['subscr_type'], $user_id);
                if ($txn['payment_status'] == "Completed") {
                    $this->updatePaymentStatus($txn['txn_id'], 1, 1);
                } else {
                    $this->updatePaymentStatus($txn['txn_id'], 0, 0);
                }

                $template = 'payment_successful';
                $subject = 'PhotoADKing: Subscription Plan Changed';
                $message_body = array(
                    'message' => 'Your subscription plan changed successfully. Remaining days are added into your new subscription. Following are the transaction details.',
                    'subscription_name' => $txn['item_name'],
                    'txn_id' => $txn['txn_id'],
                    'txn_type' => 'Subscription[P]',
                    'subscr_id' => $txn['paypal_id'],
                    'first_name' => $first_name,
                    'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                    'total_amount' => $txn['mc_amount3'],
                    'mc_currency' => $txn['mc_currency'],
                    'payer_email' => $email_id,
                    'payment_status' => $txn['payment_status'],
                    'activation_date' => $txn['subscr_date'],
                    'next_billing_date' => ($txn['expires'] != NULL) ? $txn['expires'] : 'NA',
                    'activation_date_local' => $txn['subscr_date_local'],
                    'next_billing_date_local' => ($txn['expires_local'] != NULL) ? $txn['expires_local'] : ''
                );
                $api_name = 'paypalIpn';
                $api_description = 'Subscription plan changed.';
                $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));
              }
        }
    }

    public function cancelPaymentDetailByPaypalID($paypal_id, $txn)
    {
        Log::info('cancelPaymentDetailByPaypalID: ', ["txt" => $txn]);
        $result = DB::select('SELECT
                      id,
                      user_id,
                      transaction_id,
                      subscr_type,
                      days_to_add,
                      total_amount,
                      expiration_time,
                      final_expiration_time,
                      remaining_days,
                      cancellation_date
                      FROM subscriptions
                      WHERE paypal_id = ?
                      ORDER BY id DESC', [$paypal_id]);

        if (count($result) == 0) {

            // Cancel Payment without sub
            //Major Error

        } else {
            $db_expiration_time = date("Y-m-d", strtotime($result[0]->expiration_time));
            $current_date = date("Y-m-d");

            if ($db_expiration_time > $current_date) {
                $datetime1 = new DateTime($db_expiration_time);
                $datetime2 = new DateTime($current_date);
                $interval = $datetime1->diff($datetime2);
                $remaining_days = $interval->format('%a');
            } else {
                $remaining_days = 0;
            }
            $remaining_days = $remaining_days + $result[0]->days_to_add;


            //$remaining_days = (new VerificationController())->differenceBetweenTwoDate($current_date, $db_expiration_time);


            Log::info('cancelPaymentDetailByPaypalID (remaining_days) : ', ['remaining_days' => $remaining_days]);

            //Update Subscription
            $db_row_id = $result[0]->id;
            $subscr_type = $result[0]->subscr_type;
            $db_txn_id = $result[0]->transaction_id;
            $cancellation_date = date("Y-m-d H:i:s");
            DB::beginTransaction();
            DB::update('UPDATE subscriptions SET
                            cancellation_date = ?,
                            txn_type = ?,
                            remaining_days= ?,
                            response_message= ?,
                            is_active= ?
                            WHERE id = ? ',
                [$cancellation_date,
                    $txn['txn_type'],
                    $remaining_days,
                    'Subscription Cancelled',
                    0,
                    $db_row_id]);
            DB::commit();

            DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [0, $db_txn_id]);
            DB::commit();

            $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
            $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
            $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
            $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

            if ($subscr_type == $subscr_type_of_monthly_starter) {
                $subscription_name = 'Monthly Starter';

            } elseif ($subscr_type == $subscr_type_of_monthly_pro) {
                $subscription_name = 'Monthly Pro';

            } elseif ($subscr_type == $subscr_type_of_yearly_pro) {

                $subscription_name = 'Yearly Pro';
            } elseif ($subscr_type == $subscr_type_of_yearly_starter) {

                $subscription_name = 'Yearly Starter';
            } else {
                $subscription_name = "None";
            }


            $txn_id = $result[0]->transaction_id;
            $total_amount = $result[0]->total_amount;
            $user_id = $result[0]->user_id;
            $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
            if(!isset($user_profile->email_id)){
                Log::info('cancel_subscription by payapal : The user has already deleted the account');
                return;
            }
            $email_id = $user_profile->email_id;
            $first_name = $user_profile->first_name;

            $cancellation_date_local = (New ImageController())->convertUTCDateTimeInToLocal($cancellation_date,$this->country_code);
            $expiration_date_local = (New ImageController())->convertUTCDateTimeInToLocal($result[0]->final_expiration_time,$this->country_code);

            $template = 'cancel_subscription';
            $subject = 'PhotoADKing: Subscription Cancelled';
            $message_body = array(
                'message' => 'Your subscription cancelled successfully. Following are the subscription details.',
                'subscription_name' => $subscription_name,
                'txn_id' => $txn_id,
                'txn_type' => 'Subscription[P]',
                'subscr_id' => $paypal_id,
                'total_amount' => $total_amount,
                'first_name' => $first_name,
                'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                'payment_status' => 'Subscription cancelled',
                'payer_email' => $email_id,
                'mc_currency' => $txn['mc_currency'],
                'cancellation_date' => $cancellation_date,
                'expiration_date' => $result[0]->final_expiration_time,
                'cancellation_date_local' => $cancellation_date_local,
                'expiration_date_local' => $expiration_date_local
            );
            $api_name = 'paypalIpn';
            $api_description = 'subscription cancelled .';

          Log::info('cancel_subscription mail data : ',['user_id'=>$result[0]->user_id,'email_id'=>$email_id,'subject'=>$subject,'message_body'=>$message_body]);
          $this->dispatch(new EmailJob($result[0]->user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));


        }
    }

    public function logPaypalIPN($user_id, array $txn)
    {

        try {
          Log::debug('logPaypalIPN', ["user_id"=>$user_id,'txn' => $txn]);
            DB::beginTransaction();

            DB::insert('INSERT INTO subscriptions_logs
                                (txn_id,
                                 user_id,
                                 txn_type,
                                 paypal_id,
                                 mc_amount3,
                                 subscr_date,
                                 payer_email,
                                 mc_currency,
                                 period1,
                                 paypal_response,
                                 expires)
                                 VALUES (?,?,?,?,?,?,?,?,?,?,?)',
                [$txn['txn_id'],
                    $user_id,
                    $txn['txn_type'],
                    $txn['paypal_id'],
                    $txn['mc_amount3'],
                    $txn['subscr_date'],
                    $txn['payer_email'],
                    $txn['mc_currency'],
                    $txn['period1'],
                    $txn['paypal_response'],
                    $txn['expires']]);

            DB::commit();
        } catch (Exception $e) {
            (new ImageController())->logs("logPaypalIPN",$e);
//            Log::error("logPaypalIPN : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function getRemainingDays($db_cancellation_date, $db_expiration_time, $remaining_days, $old_amount, $new_amount, $old_subscr_type, $new_subscr_type, $expires)
    {

        try {

            //Log::info('getRemainingDays');
            if ($remaining_days == "" or $remaining_days == NULL) {
                $remaining_days = 0;
            }

            $date = new DateTime($db_cancellation_date);
            $date->modify("+$remaining_days day");
            $final_expiration_date = $date->format('Y-m-d');
            $db_expiration_time = date("Y-m-d", strtotime($db_expiration_time));
            $current_date = date("Y-m-d");
            if ($final_expiration_date > $current_date) {
                //$expires = date("Y-m-d", strtotime($expires));

                Log::info('getRemainingDays Expiration with calculation detail : ',
                    [
                        'final_expiration_date' => $final_expiration_date,
                        'expires' => $expires,
                        'current_date' => $current_date,
                        'db_expiration_time' => $db_expiration_time,
                        'db_remaining_days' => $remaining_days
                    ]);

                //$remaining_days = (new VerificationController())->differenceBetweenTwoDate($db_expiration_time, $current_date);
                $db_expiration_time = date("Y-m-d", strtotime($final_expiration_date));
                $current_date = date("Y-m-d");

                if ($db_expiration_time > $current_date) {
                    $datetime1 = new DateTime($db_expiration_time);
                    $datetime2 = new DateTime($current_date);
                    $interval = $datetime1->diff($datetime2);
                    $remaining_days = $interval->format('%a');
                } else {
                    $remaining_days = 0;
                }

                $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                if ($new_subscr_type == $subscr_type_of_monthly_starter or $new_subscr_type == $subscr_type_of_monthly_pro) {
                    $total_new_days = 30;
                } elseif ($new_subscr_type == $subscr_type_of_yearly_starter or $new_subscr_type == $subscr_type_of_yearly_pro) {
                    $total_new_days = 365;
                } else {
                    $total_new_days = 0;
                }

                if ($old_subscr_type == $subscr_type_of_monthly_starter or $old_subscr_type == $subscr_type_of_monthly_pro) {
                    $total_old_days = 30;
                } elseif ($old_subscr_type == $subscr_type_of_yearly_starter or $subscr_type_of_yearly_pro == 4) {
                    $total_old_days = 365;
                } else {
                    $total_old_days = 0;
                }

                $old_subscr_amount_per_day = $old_amount / $total_old_days;
                $new_subscr_amount_per_day = $new_amount / $total_new_days;

                $remaining_price = $remaining_days * round($old_subscr_amount_per_day,2);
                $days_to_add = intval(($remaining_price * 1) / round($new_subscr_amount_per_day,2));

                Log::info('getRemainingDays Expiration detail before calculation : ', ['expiry' => $expires]);

                $date = new DateTime($expires);
                $date->modify("+$days_to_add day");
                $expires = $date->format('Y-m-d H:i:s');

                Log::info('getRemainingDays Expiration detail after calculation : ',
                    [
                        'expiry' => $expires,
                        'old_amount' => $old_amount,
                        'old_subscr_type' => $old_subscr_type,
                        'new_subscr_type' => $new_subscr_type,
                        'current_date' => $current_date,
                        'db_expiration_time' => $db_expiration_time,
                        'total_old_days' => $total_old_days,
                        'new_amount' => $new_amount,
                        'total_new_days' => $total_new_days,
                        'old_subscr_amount_per_day' => $old_subscr_amount_per_day,
                        'new_subscr_amount_per_day' => $new_subscr_amount_per_day,
                        'remaining_price' => $remaining_price,
                        'days_to_add' => $days_to_add,
                        'remaining_days' => $remaining_days]);

                return array('final_expiration_time' => $expires, 'days_to_add' => $days_to_add);
            } else {
                //$remaining_days = 0;
                return array('final_expiration_time' => $expires, 'days_to_add' => 0);
            }

        } catch (Exception $e) {
            (new ImageController())->logs("getRemainingDays",$e);
//            Log::error("getRemainingDays : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function getFinalExpirationTime($db_expiration_time, $days_to_add)
    {

        try {

            $date = new DateTime($db_expiration_time);
            $date->modify("+$days_to_add day");
            $final_expiration_time = $date->format('Y-m-d H:i:s');

            return $final_expiration_time;

        } catch (Exception $e) {
            (new ImageController())->logs("getFinalExpirationTime",$e);
//            Log::error("getFinalExpirationTime : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function updatePaymentStatus($txn_id, $ipn_status, $is_active)
    {

        try {

            DB::beginTransaction();

            if ($ipn_status == 1) {
                DB::update('UPDATE subscriptions SET payment_status = ? WHERE transaction_id = ?', ['Completed', $txn_id]);
                DB::update('UPDATE payment_status_master SET ipn_status = ?, is_active = ?, verify_status = ? WHERE txn_id = ?', [$ipn_status, $is_active, $ipn_status, $txn_id]);
            }

            DB::commit();


        } catch (Exception $e) {
            (new ImageController())->logs("updatePaymentStatus",$e);
//            Log::error("updatePaymentStatus : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function updateUserRole($subscr_type, $user_id)
    {

        try {
            DB::beginTransaction();

            if ($subscr_type == 1) {
                $role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'monthly_starter');

            } elseif ($subscr_type == 2) {
                $role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'yearly_starter');

            } elseif ($subscr_type == 3) {
                $role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'monthly_pro');

            } elseif ($subscr_type == 4) {
                $role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_PRO');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'yearly_pro');

            } elseif ($subscr_type == 5) {
                $role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'monthly_starter');

            } elseif ($subscr_type == 6) {
                $role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'yearly_starter');

            } elseif ($subscr_type == 7) {
                $role_id = Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'monthly_pro');

            } elseif ($subscr_type == 8) {
                $role_id = Config::get('constant.ROLE_ID_FOR_YEARLY_PRO');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'yearly_pro');

            } elseif ($subscr_type == 9) {
                $role_id = Config::get('constant.ROLE_ID_FOR_LIFETIME_PRO');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'lifetime_pro');

            } else {
                $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $user_id]);
                DB::commit();

                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'free_user');

            }

        } catch (Exception $e) {
            (new ImageController())->logs("updateUserRole",$e);
//            Log::error("updateUserRole : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

}
