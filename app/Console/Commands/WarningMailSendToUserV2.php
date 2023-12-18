<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Config;
use DB;
use Exception;
use Illuminate\Console\Command;
use Log;
use Mail;

class WarningMailSendToUserV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WarningMailSendToUserV2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending mail to user if user has not been active for 30 days.';

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
            $file_name = 'user_record_csv.csv';
            $dir = Config::get('constant.TEMP_DIRECTORY');
            $original_csv_path = './..'.$dir.$file_name;
            $users_data = [];
            $user_id_lists = [];
            $deactivation_date = date('d M Y', strtotime('+15 days'));

            //select user which are not logged in from last 30 days.
            $users = DB::select('SELECT
                                um.id,
                                um.uuid,
                                ud.first_name,
                                IF(um.signup_type = 1,"Email",IF(um.signup_type = 2,"Facebook","Google")) AS signup_type,
                                um.email_id,
                                COALESCE(DATEDIFF(DATE(NOW()),(SELECT DATE(update_time) FROM user_session WHERE user_id = um.id ORDER BY update_time DESC LIMIT 1)),DATEDIFF(DATE(NOW()),(SELECT DATE(update_time) FROM user_master WHERE id = um.id ORDER BY update_time DESC LIMIT 1))) AS inactivity_days
                            FROM
                                user_master AS um
                                LEFT JOIN role_user AS ru ON um.id=ru.user_id
                                LEFT JOIN user_detail AS ud ON um.id=ud.user_id
                            WHERE
                                ru.role_id = '.Config::get('constant.ROLE_ID_FOR_FREE_USER').' AND
                                um.is_active = 1 AND
                                um.attribute1 IS NULL AND
                                um.id NOT IN (SELECT DISTINCT(user_id) FROM device_master WHERE DATE(create_time) BETWEEN DATE_SUB(DATE(NOW()), INTERVAL '.Config::get('constant.INACTIVITY_DAYS_FOR_WARNING_MAIL').' DAY) AND DATE(NOW()) )
                            ORDER BY um.update_time ASC LIMIT 100');

            if (count($users) > 0) {

                // send email to admin
                $template = 'user_account_deactivation_warning';
                $subject = 'Your PhotoADKing account closing soon.';
                $start_time = date('Y-m-d H:i:s');
                $j = 0;
                foreach ($users as $user) {
                    array_push($user_id_lists, $user->id);
                    $message_body = [
                        'email' => $user->email_id,
                        'user_name' => $user->first_name,
                        'deactivation_date' => $deactivation_date,
                    ];

                    $data = ['template' => $template, 'email' => $user->email_id, 'subject' => $subject, 'message_body' => $message_body];
                    Mail::send($data['template'], $data, function ($message) use ($data) {
                        $message->to($data['email'])->subject($data['subject']);
                        $message->bcc('janviborad.optimumbrew@gmail.com')->subject($data['subject']);
                    });
                    $j++;
                }
                $end_time = date('Y-m-d H:i:s');
                Log::info('WarningMailSendToUser : ', ['starting_time: ' => $start_time, 'ending_time: ' => $end_time, 'user_details: ' => $users, 'counter' => $j]);

                $user_id = implode(',', $user_id_lists);
                DB::beginTransaction();
                DB::update('UPDATE user_master SET attribute1=1, update_time = update_time WHERE id IN ('.$user_id.')');
                DB::commit();

                $users_detail = DB::select('SELECT
                                      COALESCE(ud.user_id, 0) AS user_id,
                                      COALESCE(ud.first_name, "") AS first_name,
                                      COALESCE(ud.email_id, "") AS email_id,
                                      COALESCE(ud.my_design_count, 0) AS my_design_count,
                                      COALESCE(ud.my_video_design_count, 0) AS my_video_design_count,
                                      COALESCE(ud.uploaded_img_count, 0) AS uploaded_img_count,
                                      COALESCE((SELECT create_time FROM device_master WHERE user_id = ud.user_id ORDER BY create_time DESC LIMIT 1), (SELECT create_time FROM user_master WHERE id = ud.user_id ORDER BY create_time DESC LIMIT 1)) AS last_login_time
                                    FROM
                                      user_detail AS ud
                                    WHERE ud.user_id IN ('.$user_id.')');

                //make csv file in temp folder
                foreach ($users_detail as $i => $user_detail) {
                    $users_data[$i] = [$user_detail->user_id, $user_detail->first_name, $user_detail->email_id, $user_detail->my_design_count, $user_detail->my_video_design_count, $user_detail->uploaded_img_count, $user_detail->last_login_time];
                }

                if (($is_exist = ((new ImageController())->checkFileExist($original_csv_path)) != 1)) {
                    $file = fopen($original_csv_path, 'w');
                    $heading = ['User Id', 'First Name', 'Email Id', 'Design Count', 'Video Design Count', 'Upload Image Count', 'Last Login Time'];
                    fputcsv($file, $heading);
                    foreach ($users_data as $record) {
                        fputcsv($file, $record);
                    }
                } else {
                    $file = fopen($original_csv_path, 'a');
                    foreach ($users_data as $record) {
                        fputcsv($file, $record);
                    }
                }
                fclose($file);

                $subject = 'PhotoADKing: MonthlyReport: WarningMailSendToUser: ('.count($user_id_lists).') ';
                $template = 'deactive_user_report_to_sub_admin';
                $data = ['template' => $template, 'subject' => $subject, 'users_details' => $users_detail, 'title' => 'Warning Mail Send To User'];

                //send report to super admin attach with zip file
                Mail::send($data['template'], $data, function ($message) use ($data) {
                    $message->to(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                });

            } else {
                Log::error('WarningMailSendToUser : No user found to send Warning Mail.');
            }

        } catch (Exception $e) {
            Log::error('WarningMailSendToUser : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
