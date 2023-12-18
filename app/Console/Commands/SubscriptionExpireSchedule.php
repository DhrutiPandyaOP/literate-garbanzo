<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MailchimpController;
use App\Http\Controllers\UserController;
use Config;
use DateTime;
use DB;
use Exception;
use Illuminate\Console\Command;
use Log;
use Mail;

class SubscriptionExpireSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SubscriptionExpire:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage subscription expiry & mail of help us improve PhotoADKing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            //$result = DB::select('SELECT * FROM subscriptions WHERE DATEDIFF(final_expiration_time, NOW()) <= 0 AND DATEDIFF(final_expiration_time, NOW()) >= -1');
            $result = DB::select('SELECT
                                          DISTINCT user_id,
                                          subscr_type,
                                          final_expiration_time
                                        FROM subscriptions
                                        WHERE
                                          final_expiration_time >= NOW() - INTERVAL 24 HOUR AND final_expiration_time <= NOW() AND
                                          is_active = 0 AND is_expired = 0
                                        ORDER BY final_expiration_time DESC');

            //          Log::info('result of SubscriptionExpireSchedule : ',[$result]);

            if ($result != null) {

                $updated_user_list = [];
                foreach ($result as $r) {
                    //              Log::info('SubscriptionExpireSchedule user detail : ',[$r]);

                    if (! in_array($r->user_id, $updated_user_list)) {

                        $user_detail = (new LoginController())->getUserInfoByUserId($r->user_id);
                        if (isset($user_detail->is_subscribe) && $user_detail->role_id != Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                            if ($user_detail->is_subscribe != 1 && $user_detail->subscr_expiration_time <= $r->final_expiration_time) {
                                //                    Log::info('SubscriptionExpireSchedule role change : ', ["is_subscription" => $user_detail->is_subscribe, "subscr_expiration_time" => $user_detail->subscr_expiration_time, "final_expiration_time" => $r->final_expiration_time]);
                                $role_id = Config::get('constant.ROLE_ID_FOR_FREE_USER');
                                DB::beginTransaction();
                                DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ?', [$role_id, $r->user_id]);
                                DB::commit();

                                (new UserController())->deleteAllRedisKeys("getDashBoardDetails:payment_status_details:$user_detail->user_id");
                                //(new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'free_user');

                                $subscr_type = $r->subscr_type;
                                $db_final_expiration_time = $r->final_expiration_time;

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
                                    $subscription_name = 'None';
                                }

                                DB::beginTransaction();
                                DB::delete('DELETE FROM user_session WHERE user_id = ?', [$r->user_id]);
                                DB::commit();

                                $date = new DateTime($db_final_expiration_time);
                                $final_expiration_time = $date->format('M d, Y H:i:s T');

                                $subject = 'PhotoADKing: Subscription Expired';
                                $template = 'simple';
                                $message_body = [
                                    'message' => '<p style="text-align: left">Thanks for signing up for <b>PhotoADKing</b>. We hope you have been enjoying the <b>
                            '.$subscription_name.'</b>. <br><br><span style="color: #484747;">Unfortunately, your <b>'.$subscription_name.'</b> is ending on <b>'
                                      .$final_expiration_time.'</b>.</span><br><br>We\'d love to keep you as a customer,
                                and there is still time to subscribe to a new plan! Simply visit your account dashboard to subscribe.
                                <br><br>As a reminder, when your purchase expires you will be automatically placed on the free plan.</p>',
                                    'user_name' => $user_detail->first_name,
                                ];

                                $data = ['template' => $template, 'email' => $user_detail->email_id, 'subject' => $subject, 'message_body' => $message_body];

                                Mail::send($data['template'], $data, function ($message) use ($data) {
                                    $message->to($data['email'])->subject($data['subject']);
                                    $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                                    $message->bcc(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);

                                });

                                $updated_user_list[] = $r->user_id;
                            }
                        }
                        /*
                        else
                        {
                          //Log::info('Is expiry exist : ',['count' => count($result)]);

                          $subject = 'PhotoADKing: Subscription Expiry';
                          $template = 'simple';
                          $message_body = array(
                            'message' => 'Today, subscription expiry count is 0.',
                            'user_name' => 'Admin'
                          );

                          $data = array('template' => $template, 'email' => Config::get('constant.SUB_ADMIN_EMAIL_ID'), 'subject' => $subject, 'message_body' => $message_body);

                          Mail::send($data['template'], $data, function ($message) use ($data) {
                            //$message->to($data['email'])->subject($data['subject']);
                            //$message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                            //$message->bcc(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);
                            $message->to(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);

                          });
                        }
                        */
                    }
                }
                /*//Log::info('updated_user_list :',[$updated_user_list]);
                if(count($updated_user_list) >= 0 ){

                  //Log::info('Is expiry total count  : ',['expiry_count' => count($updated_user_list)]);
                  $subject = 'PhotoADKing: Subscription Expiry count of PhotoAdKing';
                  $template = 'simple';
                  $message_body = array(
                    'message' => 'Today, subscription expiry count is '.count($updated_user_list).' .',
                    'user_name' => 'Admin'
                  );

                  $data = array('template' => $template, 'email' => Config::get('constant.ADMIN_EMAIL_ID'), 'subject' => $subject, 'message_body' => $message_body);

                  Mail::send($data['template'], $data, function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['subject']);
                    $message->bcc(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);

                  });
                }*/
            }
            /*else {
              //Log::info('Is expiry exist : ',['count' => count($result)]);

              $subject = 'PhotoADKing: Subscription Expiry';
              $template = 'simple';
              $message_body = array(
                'message' => 'Today, subscription expiry count is 0.',
                'user_name' => 'Admin'
              );

              $data = array('template' => $template, 'email' => Config::get('constant.ADMIN_EMAIL_ID'), 'subject' => $subject, 'message_body' => $message_body);

              Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);

              });
            }*/
        } catch (Exception $e) {
            (new ImageController())->logs('SubscriptionExpireSchedule', $e);
            //          Log::error("SubscriptionExpireSchedule : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

       /* Send mail to users which are sign up before 24 hour to get feedback. */
//        $user_list = DB::select('SELECT * FROM user_master WHERE create_time >= NOW() - INTERVAL 24 HOUR AND create_time <= NOW()');
//
//        foreach ($user_list as $key) {
//
//            $user_profile = (new LoginController())->getUserInfoByUserId($key->id);
//
//            $subject = 'PhotoADKing: Help us improve PhotoADKing';
//            $template = 'help_us';
//
//            $message_body = array(
//                'message' => '<p style="text-align: left">Thanks for trying out PhotoADKing, we hope you had the time to check out most of the tool options.<br><br>
//                                We\'re completely aware that we still need to do a lot of work to make PhotoADKing very useful for you.<br><br>
//                                In order to get there as fast as possible we need your brutally honest opinion, so I wanted to ask you:<br><br>
//                                What are the biggest barriers to start using <b>PhotoADKing right away for your projects? What is missing?</b><br><br>
//                                </p>',
//                'user_name' => $user_profile->first_name
//            );
//
//            $data = array('template' => $template, 'email' => $key->email_id, 'subject' => $subject, 'message_body' => $message_body);
//            Mail::send($data['template'], $data, function ($message) use ($data) {
//                $message->to($data['email'])->subject($data['subject']);
//                 //   ->replyTo('help.photoadking@gmail.com', 'PhotoADKing');;
//                //$message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
//                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
//            });
//        }

    }
}
