<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Log;
use DB;
use Config;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PaypalIPNController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Redis;
use Response;
use App\Jobs\EmailJob;
use DateTime;

class ToActivatePayPalSubscriptionJob extends Job implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }
    public function handle()
    {
        try {
            $subscription_list = array();
            $unverified_subscriptions = DB::select('SELECT 
                                                        psm.txn_id,
                                                        psm.user_id,
                                                        psm.create_time,
                                                        ud.email_id,
                                                        CONCAT(IFNULL(ud.first_name,"")," ",IFNULL(ud.last_name,"")) as user_name
                                                    FROM 
                                                        payment_status_master AS psm  
                                                    LEFT JOIN 
                                                        user_detail AS ud
                                                    ON 
                                                        ud.user_id = psm.user_id
                                                    WHERE 
                                                        psm.paypal_payment_status = "Pending" AND
                                                        psm.ipn_status = 0 AND
                                                        psm.is_active= 0');
            if(count($unverified_subscriptions) < 0){
//                Log::info('No user found with unverified subscription');
                return;
            }
//            Log::info('Those are unverified user:',[$unverified_subscriptions]);
            $total_verified_subscriptions = 0;
            foreach ($unverified_subscriptions AS $sub){
                try {
                    $txn_id = $sub->txn_id;
                    $db_user_id = $sub->user_id;
                    $create_time = $sub->create_time;
                    $user_email_id = $sub->email_id;
                    $user_name = $sub->user_name;
                    $is_already_subscriber = DB::select('SELECT 1 FROM subscriptions AS sub WHERE sub.is_active = 1 AND sub.user_id=?', [$db_user_id]);
                    if (count($is_already_subscriber) < 0) {
//                        Log::info('User already registered with any active subscription', ['user_id' => $db_user_id]);
                        continue;
                    }
                    $req = array(
                        'user' => Config::get('constant.PAYPAL_API_USER'),
                        'pwd' => Config::get('constant.PAYPAL_API_PASSWORD'),
                        'signature' => Config::get('constant.PAYPAL_API_SIGNATURE'),
                        'version' => '70.0',
                        'METHOD' => 'GetTransactionDetails',
                        'TRANSACTIONID' => urlencode($txn_id),
                        'NOTE' => 'Fetch transaction detail',
                    );
                    $ch = curl_init();

                    // Swap these if you're testing with the sandbox
                    curl_setopt($ch, CURLOPT_URL, Config::get('constant.PAYPAL_API_URL'));
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
                    $curl_response = curl_exec($ch);
                    $curl_response = urldecode($curl_response);
                    $curl_response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

                    if ($curl_response === false || $curl_response_status == '0') {
                        $errno = curl_errno($ch);
                        $errstr = curl_error($ch);
                        curl_close($ch);
                        //throw new Exception("cURL error: [$errno] $errstr");
                        //return $response = Response::json(array('code' => 201, 'message' => 'Unable to fetch transaction detail. Please try again.', 'cause' => '', 'data' => json_decode("{}")));
                        Log::error("ToActivatePayPalSubscriptionJob : ", ['code' => 201, 'message' => 'Unable to fetch transaction detail.', 'transaction_id' => $txn_id, 'errno' => $errno, 'errstr' => $errstr]);
                        exit(0);
                    } else {
                        curl_close($ch);
                        parse_str($curl_response, $result); //convert encoded url to string
                        //$result = json_encode($result);
                    }
                    if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $curl_response, $matches)) {
                        foreach ($matches['name'] as $offset => $name) {
                            $nvp[$name] = urldecode($matches['value'][$offset]);
                        }
                    }
                    $paypal_ACK = (isset($nvp['ACK'])) ? $nvp['ACK'] : 'Error';
                    if (strcmp($paypal_ACK, 'Error') == 0 || strcmp($paypal_ACK, 'Failure') == 0) {
                        Log::error('verifyTransaction : ', ['error' => $nvp['L_SHORTMESSAGE0']]);
                        //throw new Exception("Paypal error:" . $nvp['L_SHORTMESSAGE0']);
                        return $response = Response::json(array('code' => 201, 'message' => 'Unable to fetch transaction detail. Please try again.', 'cause' => '', 'data' => json_decode("{}")));
                    }

//                    Log::info('result: ',[$result]);
                    $txn_type = $result['TRANSACTIONTYPE'];
                    if(!isset($result['CUSTOM'])){
                        Log::error('User details missing(tag : CUSTOM) ', [$result]);
                        continue;
                    }
                    $user_id = $result['CUSTOM'];
//                    Log::info('paypal returns user_id : ',[$user_id]);
                    if (!is_numeric($user_id)) {
                        $user_detail = DB::select('SELECT id FROM user_master WHERE uuid=?', [$user_id]);
                        if (count($user_detail) > 0) {
                            $user_id = $user_detail[0]->id;
                        } else {
                            return Response::json(array('code' => 201, 'message' => 'User not found.', 'cause' => '', 'data' => json_decode("{}")));
                        }
                    }
//                    Log::info('converted user_ids: ',['paypal_user_id'=>$user_id,'db_user_id'=>$db_user_id]);

                    if ($db_user_id != $user_id) {
                        Log::error('User Id mismatch', ['db_user_id' => $db_user_id, 'Paypal_user_id' => $user_id]);
                        continue;
                    }
                    $txn_id = $result['TRANSACTIONID'];
                    $subscr_id = $result['SUBSCRIPTIONID'];
                    $subscr_date = date("Y-m-d H:i:s", strtotime($result['ORDERTIME']));
                    $mc_amount3 = $result['AMT'];
                    $payer_email = $result['EMAIL'];
                    $mc_currency = $result['CURRENCYCODE'];
                    $payment_status = $result['PAYMENTSTATUS'];
                    $item_number = $result['L_NUMBER0'];
                    $item_name = $result['L_NAME0'];
                    $first_name = $result['FIRSTNAME'];
                    $country_code = $result['COUNTRYCODE'];

                    if (($response = (new VerificationController())->checkIsSubscriptionIdExist($subscr_id)) != '') {
//                        Log::info('Is subscription already exist with same PayPal_Id', ['PayPal_Id' => $subscr_id]);
                        continue;
                    }

                    $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                    $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                    $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                    $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                    if ($item_number == $subscr_type_of_monthly_starter or $item_number == $subscr_type_of_monthly_pro) {

                        $date = new DateTime($subscr_date);
                        $date->modify("+1 Month");
                        $expires = $date->format('Y-m-d H:i:s');

                        //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
                    } elseif ($item_number == $subscr_type_of_yearly_starter or $item_number == $subscr_type_of_yearly_pro) {

                        $date = new DateTime($subscr_date);
                        $date->modify("+1 Year");
                        $expires = $date->format('Y-m-d H:i:s');
                        //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
                    } else {
                        $expires = NULL;
                    }
                    $subscr_date_local = (New ImageController())->convertUTCDateTimeInToLocal($subscr_date,$country_code);
                    $expires_time_local = (New ImageController())->convertUTCDateTimeInToLocal($expires,$country_code);
                    $txn = array(
                        'txn_id' => $txn_id,
                        'user_id' => $user_id,
                        'txn_type' => $txn_type,
                        'paypal_id' => $subscr_id,
                        'subscr_date' => $subscr_date,
                        'subscr_date_local' => $subscr_date_local,
                        'mc_amount3' => $mc_amount3,
                        'payer_email' => $payer_email,
                        'mc_currency' => $mc_currency,
                        'period1' => 'NULL',
                        'expires' => $expires,
                        'expires_local' => $expires_time_local,
                        'payment_status' => $payment_status,
                        'subscr_type' => $item_number,
                        'paypal_response' => json_encode($result),
                        'create_time' => date('Y-m-d H:i:s'),
                        'item_name' => $item_name,
                        'first_name' => $first_name
                    );
                    //return $txn;
                    Log::info('ToActivatePayPalSubscriptionJob (New Payment)', ['Report' => $txn]);

                    if ($txn) {

                        if (strcmp($txn_type, 'subscrpayment') == 0) {
                            $txn['txn_type'] = 'subscr_payment';

                            if (strcmp($payment_status, 'Completed') == 0) {
                                (new PaypalIPNController())->updatePaymentDetailByUserID($user_id, $txn);
                            } else {
//                                Log::debug('paypalIpn-Verified : ', ['txn_type' => "subscr_payment", "txt" => $txn]);
                            }
                            (new PaypalIPNController())->logPaypalIPN($user_id, $txn);
//                            Log::debug('paypalIpn-Verified : ', ['txn_type' => "subscr_payment", "txt" => $txn]);

                            $subscription_detail =array(
                              'purchase_date' =>$create_time,
                              'txn_id' => $txn_id,
                              'user_id' => $db_user_id,
                              'email_id' => $user_email_id,
                              'user_name' => $user_name
                            );
                            array_push($subscription_list,$subscription_detail);
                        } else if (strcmp($txn_type, 'subscrfailed') == 0) {
                            $txn['txn_type'] = 'subscr_failed';

                            (new PaypalIPNController())->logPaypalIPN($user_id, $txn);
//                            Log::debug('paypalIpn-subscr_failed : ', ['paypal_id' => $subscr_id]);

                            if ($user_id != 'NA' or $user_id != '') {
                                $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
                                $email_id = $user_profile->email_id;
                                $first_name = $user_profile->first_name;

                                $template = 'payment_failed';
                                $subject = 'PhotoADKing: Payment Failed';
                                $message_body = array(
                                    'message' => 'Sorry, your payment failed. No charges were made. Following are the transaction details.',
                                    'subscription_name' => $txn['item_name'],
                                    'txn_id' => $txn['txn_id'],
                                    'txn_type' => 'Subscription',
                                    'subscr_id' => $txn['paypal_id'],
                                    'first_name' => $first_name,
                                    'payment_received_from' => $txn['first_name'] . ' (' . $txn['payer_email'] . ')',
                                    'total_amount' => $txn['mc_amount3'],
                                    'mc_currency' => $txn['mc_currency'],
                                    'payer_email' => $txn['payer_email'],
                                    'payment_status' => $txn['payment_status']
                                );
                                $api_name = 'confirmPaymentByAdmin';
                                $api_description = 'Subscription failed.';

                                $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                            } else {
//                                Log::debug('paypalIpn-subscr_failed did not get user_id : ', ['data' => $txn]);
                                $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
                                $template = 'simple';
                                $subject = 'PhotoADKing: PayPal IPN Failed';
                                $message_body = array(
                                    'message' => '<p>API "paypalIpn" could not fetch user_id from IPN response in case of transaction type is subscr_failed. Please check the logs.</p>',
                                    'user_name' => 'Admin'
                                );
                                $api_name = 'confirmPaymentByAdmin';
                                $api_description = 'Get INVALID from IPN.';
                                $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                            }
                        } else {
                            Log::error('You can only confirm payment if transaction type is subscr_payment.', ['tansaction_id' => $txn_id, 'user_id' => $user_id]);
                            //return $response = Response::json(array('code' => 201, 'message' => 'You can only confirm payment if transaction type is subscr_payment.', 'cause' => '', 'data' => json_decode("{}")));

                        }
                    } else {
                        //return $response = Response::json(array('code' => 201, 'message' => 'Transaction details is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
                        Log::error('Transaction details is missing or empty.', ['transaction_id' => $txn_id, 'user_id' => $user_id]);
                        continue;
                    }
                    //$response = Response::json(array('code' => 200, 'message' => 'Payment confirmed successfully.', 'cause' => '', 'data' => json_decode("{}")));
                    $total_verified_subscriptions++;
                }catch (Exception $e) {

                    Log::error("ToActivatePayPalSubscriptionJob : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                    // send email to admin
                    $template = 'simple';
                    $description = "Error while I'm activate the user subscription.";
                    $error_msg = $e->getMessage();
                    $email_id = Config::get('constant.ADMIN_EMAIL_ID');//add admin email address
                    $subject = 'ToActivatePayPalSubscriptionJob : logic Exception Call';
                    $message_body = array(
                        'message' => 'Exception Reason = ' . $error_msg . '<br>' . 'API Name = ToActivatePayPalSubscriptionJob' . '<br>' . 'API Description = ' . $description,
                        'user_name' => 'Admin'
                    );
                    $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
                    Mail::send($data['template'], $data, function ($message) use ($data) {
                        $message->to($data['email'])->subject($data['subject']);
                        $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                    });
                    continue;
                }
            }

            /*$template = 'job_or_scheduler_report';
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');
            $subject = 'PhotoADKing : Activate User Subscription Using Scheduler';
            $message ="User subscription successfully activated using scheduler which are created in last 1 hour";
            $message_body = array(
              'message' =>$message,
              'subscription_list' =>$subscription_list
            );
            $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
            Mail::send($data['template'], $data, function ($message) use ($data) {
              $message->to($data['email'])->subject($data['subject']);
              $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });*/

            //remove caching
            //getBillingInfo
            $keys = Redis::keys(Config::get("constant.REDIS_KEY").':getBillingInfo*');
            //Log::info("Config::get("constant.REDIS_KEY"):getBillingInfo Key Deleted",['key' =>$keys]);
            foreach ($keys as $key) {
                Redis::del($key);
            }
            //getAllTransactionsForAdmin
            $keys = Redis::keys(Config::get("constant.REDIS_KEY").':getAllTransactionsForAdmin*');
            foreach ($keys as $key) {
                Redis::del($key);
            }
            //getUserProfile
            $keys = Redis::keys(Config::get("constant.REDIS_KEY").':getUserProfile*');
            foreach ($keys as $key) {
                Redis::del($key);
            }
            //getAllUsersByAdmin
            $keys = Redis::keys(Config::get("constant.REDIS_KEY").':getAllUsersByAdmin*');
            foreach ($keys as $key) {
                Redis::del($key);
            }


        } catch (Exception $e) {
            (new ImageController())->logs("ToActivatePayPalSubscriptionJob",$e);
//            Log::error("ToActivatePayPalSubscriptionJob : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            // send email to admin
            $template = 'simple';
            $description = "Error while I'm activate the user subscription.";
            $error_msg = $e->getMessage();
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');//add admin email address
            $subject = 'ToActivatePayPalSubscriptionJob : Job Exception Call';
            $message_body = array(
                'message' => 'Exception Reason = ' . $error_msg . '<br>' . 'API Name = ToActivatePayPalSubscriptionJob' . '<br>' . 'API Description = ' . $description,
                'user_name' => 'Admin'
            );
            $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });
            exit(0);
        }

    }

    public function failed()
    {
        $user_id = 1;
        $api_name = 'ToActivatePayPalSubscriptionJob';
        $api_description = 'Use job to activate subscriptions of PAK-Users';
        $job_name = 'ToActivatePayPalSubscriptionJob';

        // get failed job max id
        $failed_job_id_result = DB::select('SELECT max(id) as max_id FROM failed_jobs');
        if (count($failed_job_id_result) > 0) {

            $failed_job_id = $failed_job_id_result[0]->max_id;
            if($failed_job_id == NULL)
            {
                $failed_job_id = 1;
            }

            // add failed job detail
            DB::beginTransaction();
            DB::insert('INSERT INTO failed_jobs_detail
                        (failed_job_id, user_id, api_name, api_description, job_name)
                        VALUES (?,?,?,?,?)',
                [$failed_job_id, $user_id, $api_name, $api_description, $job_name]);
            DB::commit();

            // send email to admin
            $template = 'simple';
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');//add admin email address
            $subject = 'ToActivatePayPalSubscriptionJob : Job failed';
            $message_body = array(
                'message' => 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description,
                'user_name' => 'Admin'
            );
            $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
//                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });
            // log failed job
            Log::error('ToActivatePayPalSubscriptionJob.php failed()',['failed_job_id'=>$failed_job_id,'user_id'=>$user_id,'api_name'=>$api_name,'api_description'=> $api_description]);
        }
    }
}
