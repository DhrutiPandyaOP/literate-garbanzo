<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Illuminate\Console\Command;
use Mail;
use Config;
use Log;
use Exception;
use Illuminate\Support\Facades\DB;

class GetTotalSignUpAndDesignOfPreviousDay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getTotalSignUpAndDesignOfPreviousDay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a report of total sign up users and total designs from the previous day ';

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

            $total_image_design = 0;
            $total_video_design = 0;
            $total_intro_design = 0;

             $total_row_design = DB::select('SELECT content_type,count(*) AS total FROM my_design_master
                                          WHERE
                                            is_active = 1 AND
                                            DATE(create_time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))
                                            GROUP BY content_type ');

            foreach ($total_row_design as $row){
                if($row->content_type == 1){
                  $total_image_design = $row->total;
                }
                if($row->content_type == 2){
                  $total_video_design = $row->total;
                }
                if($row->content_type == 3){
                  $total_intro_design = $row->total;
                }
            }


            $active_users = DB::select('SELECT count(*) AS total FROM user_master
                                              WHERE
                                                is_active = ? AND
                                                is_verify = ? AND
                                                DATE(create_time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) ', [1, 1 ]);

            $total_user = isset($active_users[0]->total) ? $active_users[0]->total : 0;

//          $total_report = array(
//              "total_user"=>$total_user,
//              "total_image_design"=>$total_image_design,
//              "total_video_design"=>$total_video_design,
//              "total_intro_design"=>$total_intro_design
//          );
//
//          $subject = 'PhotoADKing: DailyReport: NewUser('.$total_user.'), Design('.$total_image_design.'),Video('.$total_video_design.'), Intro('.$total_intro_design.')';
//          $template = "total_user_design_report";
//          $data = array('template' => $template,'subject' =>$subject, 'message_body' => $total_report );

            $total_verification_mail = DB::select('SELECT
                                                        COUNT(IF(find_in_set("send", status), id, NULL )) AS total_send,
                                                        COUNT(IF(find_in_set("bounce", status), id, NULL )) AS total_bounce,
                                                        COUNT(IF(find_in_set("delivery", status), id, NULL )) AS total_deliver,
                                                        COUNT(IF(find_in_set("deliverydelay", status), id, NULL )) AS total_deliver_delay,
                                                        COUNT(IF(find_in_set("open", status), id, NULL )) AS total_open
                                                    FROM
                                                        email_monitor_master
                                                    WHERE
                                                        is_active = ? AND
                                                        subject = "' . Config::get('constant.VERIFICATION_MAIL_SUBJECT') . '" AND
                                                        DATE(create_time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) ', [1]);

            $total_send = isset($total_verification_mail[0]->total_send) ? $total_verification_mail[0]->total_send : 0;
            $total_bounce = isset($total_verification_mail[0]->total_bounce) ? $total_verification_mail[0]->total_bounce : 0;
            $total_deliver = isset($total_verification_mail[0]->total_deliver) ? $total_verification_mail[0]->total_deliver : 0;
            $total_deliver_delay = isset($total_verification_mail[0]->total_deliver_delay) ? $total_verification_mail[0]->total_deliver_delay : 0;
            $total_open = isset($total_verification_mail[0]->total_open) ? $total_verification_mail[0]->total_open : 0;

            $total_welcome_mail = DB::select('SELECT
                                                    COUNT(IF(find_in_set("send", status), id, NULL )) AS total_send,
                                                    COUNT(IF(find_in_set("bounce", status), id, NULL )) AS total_bounce,
                                                    COUNT(IF(find_in_set("delivery", status), id, NULL )) AS total_deliver,
                                                    COUNT(IF(find_in_set("deliverydelay", status), id, NULL )) AS total_deliver_delay,
                                                    COUNT(IF(find_in_set("open", status), id, NULL )) AS total_open
                                                FROM
                                                    email_monitor_master
                                                WHERE
                                                    is_active = ? AND
                                                    subject = "' . Config::get('constant.ACCOUNT_ACTIVATION_MAIL_SUBJECT') . '" AND
                                                    DATE(create_time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) ', [1]);

            $total_welcome_send = isset($total_welcome_mail[0]->total_send) ? $total_welcome_mail[0]->total_send : 0;
            $total_welcome_bounce = isset($total_welcome_mail[0]->total_bounce) ? $total_welcome_mail[0]->total_bounce : 0;
            $total_welcome_deliver = isset($total_welcome_mail[0]->total_deliver) ? $total_welcome_mail[0]->total_deliver : 0;
            $total_welcome_deliver_delay = isset($total_welcome_mail[0]->total_deliver_delay) ? $total_welcome_mail[0]->total_deliver_delay : 0;
            $total_welcome_open = isset($total_welcome_mail[0]->total_open) ? $total_welcome_mail[0]->total_open : 0;


      $total_exports = DB::select('SELECT COUNT(id) as count FROM design_template_jobs WHERE TIMESTAMPDIFF(DAY, update_time, NOW()) = 1;')[0]->count;
      $failed_exports = DB::select('SELECT COUNT(id) as count FROM design_template_jobs WHERE TIMESTAMPDIFF(DAY, update_time, NOW()) = 1 AND status != 0 AND status != 1')[0]->count;

      $total_sub = DB::select('SELECT COUNT(um.id) as count FROM user_master AS um LEFT JOIN role_user AS ru ON um.id = ru.user_id LEFT JOIN subscriptions AS sb ON um.id = sb.user_id WHERE um.id != 1 AND ru.role_id != 2 AND DATE(sb.activation_time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) AND payment_status = "Active"')[0]->count;

        $new_subs = DB::select('SELECT um.id AS user_id,
                                 IF(ud.last_name != "", CONCAT(ud.first_name, " ", ud.last_name), ud.first_name) AS full_name,
                                 ud.user_keyword,
                                 ud.attribute1 AS utm_campaign
                                 FROM
                                      user_master AS um
                                    LEFT JOIN role_user AS ru ON um.id = ru.user_id
                                    LEFT JOIN user_detail AS ud ON um.id = ud.user_id
                                    LEFT JOIN subscriptions AS sb ON um.id = sb.user_id
                                WHERE
                                      um.id != 1 AND ru.role_id != 2 AND DATE(sb.activation_time) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) AND sb.payment_status = "Active" ');


        $blade_data = array(
                  "date"=>date("d-m-Y",strtotime("yesterday")),
                  "blade_description"=>"Below is the daily report of new signups & total Design, Video, Intro created yesterday.",
                  "table_heading"=>"<tr style=\"font-size: 16px;\">
                                        <th>Total User </th>
                                        <th>Total Image Design</th>
                                        <th>Total Video Design</th>
                                        <th>Total Intro & Outro  Design</th>
                                        <th>Total Design Exported</th>
                                        <th width=\"20%\">Total new subscription purchase</th>
                                    </tr>",
                  "table_description"=>"<tr style=\"text-align: center;font-size: 20px;\">
                                            <td> $total_user </td>
                                            <td> $total_image_design </td>
                                            <td> $total_video_design </td>
                                            <td> $total_intro_design </td>
                                            <td> $total_exports ($failed_exports)</td>
                                            <td> $total_sub </td>
                                          </tr>",
                  "verification_report_description"=>"Below is the daily report of verification mail sent yesterday.",
                  "verification_report_count"=>$total_send,
                  "verification_report_table_heading"=>"<tr style=\"font-size: 16px;\">
                                                            <th>Total Send Mail </th>
                                                            <th>Total Bounce Mail</th>
                                                            <th>Total Deliver Mail</th>
                                                            <th>Total Deliver Delay Mail</th>
                                                            <th>Total Open Mail</th>
                                                        </tr>",
                  "verification_table_description"=>"<tr style=\"text-align: center;font-size: 20px;\">
                                                        <td> $total_send </td>
                                                        <td> $total_bounce </td>
                                                        <td> $total_deliver </td>
                                                        <td> $total_deliver_delay </td>
                                                        <td> $total_open </td>
                                                      </tr>",
                  "welcome_report_description"=>"Below is the daily report of welcome mail sent yesterday.",
                  "welcome_report_count"=>$total_welcome_send,
                  "welcome_report_table_heading"=>"<tr style=\"font-size: 16px;\">
                                                        <th>Total Send Mail </th>
                                                        <th>Total Bounce Mail</th>
                                                        <th>Total Deliver Mail</th>
                                                        <th>Total Deliver Delay Mail</th>
                                                        <th>Total Open Mail</th>
                                                    </tr>",
                  "welcome_report_table_description"=>"<tr style=\"text-align: center;font-size: 20px;\">
                                                          <td> $total_welcome_send </td>
                                                          <td> $total_welcome_bounce </td>
                                                          <td> $total_welcome_deliver </td>
                                                          <td> $total_welcome_deliver_delay </td>
                                                          <td> $total_welcome_open </td>
                                                        </tr>",
          "total_subs_purchase_today_description"=>"Below is the daily report of How many New Subscribers have purchase memberships.",
          "total_subs_purchase_today_heading"=>"<tr style=\"font-size: 16px;\">
                                                        <th>Sr.No.</th>
                                                        <th>Subscriber Name</th>
                                                        <th>UTM Campaign</th>
                                                        <th>User Keywords</th>
                                                    </tr>",
          "total_subs_purchase_today"=>$new_subs
            );

            $subject = 'PhotoADKing: DailyReport: NewUser('.$total_user.'), Design('.$total_image_design.'),Video('.$total_video_design.'), Intro('.$total_intro_design.') , Export('.$total_exports.'), Subscription('.$total_sub.')';
            $template = "send_total_report_dynamically";
            $data = array('template' => $template,'subject' =>$subject, 'message_body' => $blade_data );

            Mail::send($data['template'], $data, function($message) use ($data) {
              $message->to(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject($data['subject']);
              $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
              $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });

      } catch (Exception $e) {
        (new ImageController())->logs("GetTotalSignUpAndDesignOfPreviousDay",$e);
//        Log::error("GetTotalSignUpAndDesignOfPreviousDay : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      }

    }
}
