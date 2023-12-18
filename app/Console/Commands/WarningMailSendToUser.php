<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Config;
use DB;
use Exception;
use Illuminate\Console\Command;
use Log;
use Mail;
use ZipArchive;

class WarningMailSendToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WarningMailSendToUser:command {limit} {start_date} {end_date} {days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending mail to user if user has not been active for 60 days.';

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

            $limit = $this->argument('limit');
            $start_date = $this->argument('start_date');
            $end_date = $this->argument('end_date');
            $days = $this->argument('days');

            $zip = new ZipArchive();
            $milliseconds = intval(microtime(true) * 1000);
            $user_id_lists = [];
            $users_data = [];
            $file_name = $milliseconds.'.csv';
            $zip_name = 'user_record_zip.zip';
            $dir = Config::get('constant.TEMP_DIRECTORY');
            $original_csv_path = './..'.$dir.$file_name;
            $original_zip_path = './..'.$dir.$zip_name;
            $destination_array = [];

            $users = DB::select('SELECT
                                  um.id,
                                  um.uuid,
                                  ud.first_name,
                                  IF(um.signup_type = 1,"Email",IF(um.signup_type = 2,"Facebook","Google")) AS signup_type,
                                  um.email_id,
                                  COALESCE(DATEDIFF(DATE(NOW()),(SELECT DATE(create_time) FROM user_session WHERE user_id = um.id ORDER BY update_time DESC LIMIT 1)),DATEDIFF(DATE(NOW()),(SELECT DATE(create_time) FROM user_master WHERE id = um.id ORDER BY create_time DESC LIMIT 1))) AS inactivity_days
                              FROM
                                  user_master AS um LEFT JOIN role_user AS ru ON um.id=ru.user_id
                                                    LEFT JOIN user_detail AS ud ON um.id=ud.user_id
                              WHERE
                                  ru.role_id = '.Config::get('constant.ROLE_ID_FOR_FREE_USER').' AND
                                  um.is_active = 1 AND
                                  um.attribute1 IS NULL AND
                                  DATE(um.create_time) BETWEEN ? AND ? AND
                                  um.id NOT IN (SELECT DISTINCT(user_id) FROM device_master WHERE DATE(create_time) BETWEEN DATE_SUB(DATE(NOW()), INTERVAL '.$days.' DAY) AND DATE(NOW()) )
                                  ORDER BY um.create_time ASC LIMIT ?', [$start_date, $end_date, $limit]);

            Log::info('user master', [count($users)]);
            Log::info('parameter', ['limit' => $limit, 'start_date' => $start_date, 'end_date' => $end_date, 'days' => $days]);

            if (count($users) > 0) {

                $recipient = ['Destination' => [
                    'ToAddresses' => ['bhargav.optimumbrew@gmail.com'],
                ],
                    'ReplacementTemplateData' => '{ "name" : "bhargav", "email_id" : "bhargav.optimumbrew@gmail.com", "signup_type" : "Google", "uuid" : "abcd1234"}', ];
                array_push($destination_array, $recipient);

                foreach ($users as $i => $user) {
                    array_push($user_id_lists, $user->id);

                    $recipient = ['Destination' => [
                        'ToAddresses' => [$user->email_id],
                    ],
                        'ReplacementTemplateData' => "{ \"name\" : \"$user->first_name\", \"email_id\" : \"$user->email_id\", \"signup_type\" : \"$user->signup_type\", \"uuid\" : \"$user->uuid\"}", ];
                    array_push($destination_array, $recipient);
                }

                $recipient = ['Destination' => [
                    'ToAddresses' => ['moxesh.optimumbrew@gmail.com'],
                ],
                    'ReplacementTemplateData' => '{ "name" : "moxesh", "email_id" : "moxesh.optimumbrew@gmail.com", "signup_type" : "Emaill", "uuid" : "a1b2c3d4"}', ];
                array_push($destination_array, $recipient);

                $client = SesClient::factory([
                    'version' => 'latest',
                    'region' => 'us-east-1',
                    'credentials' => [
                        'key' => Config::get('constant.SES_KEY'),
                        'secret' => Config::get('constant.SES_SECRET'),
                    ],
                ]);

                $destinations = array_chunk($destination_array, 25);
                foreach ($destinations as $i => $recipient_array) {
                    try {
                        $result = $client->sendBulkTemplatedEmail([
                            'DefaultTemplateData' => '{ "name":"Friends","email_id":"email@gmail.com","signup_type":"Email","uuid":"abcd1234"}',
                            'Destinations' => $recipient_array,
                            'Source' => 'PhotoAdKing <no-reply@photoadking.com>',
                            'Template' => 'WarningMailSendToUser',
                        ]);
                        sleep(1);
                        Log::info('WarningMailSendToUser SES : ', ['result' => $result, 'recipient' => $recipient_array]);

                    } catch (AwsException $e) {
                        Log::error('WarningMailSendToUser SES Error : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString(), 'recipient' => $recipient_array]);
                    }
                }

                $user_id = implode(',', $user_id_lists);
                DB::beginTransaction();
                DB::update('UPDATE user_master AS um SET um.attribute1=1 WHERE id IN ('.$user_id.')');
                DB::commit();

                $users_detail = DB::select('SELECT
                          COALESCE(ud.user_id,0) AS user_id ,
                          COALESCE(ud.first_name,"") AS first_name,
                          COALESCE(ud.email_id,"") AS email_id,
                          COALESCE(ud.my_design_count,0) AS my_design_count,
                          COALESCE(ud.my_video_design_count,0) AS my_video_design_count,
                          COALESCE(ud.uploaded_img_count,0) AS uploaded_img_count,
                          COALESCE((SELECT create_time FROM device_master WHERE user_id = ud.user_id ORDER BY create_time DESC LIMIT 1),(SELECT create_time FROM user_master WHERE id = ud.user_id ORDER BY create_time DESC LIMIT 1)) AS last_login_time
                        FROM user_detail AS ud
                          WHERE ud.user_id IN ('.$user_id.')');

                //make csv file in temp folder
                foreach ($users_detail as $i => $user_detail) {
                    $users_data[$i] = [$user_detail->user_id, $user_detail->first_name, $user_detail->email_id, $user_detail->my_design_count, $user_detail->my_video_design_count, $user_detail->uploaded_img_count, $user_detail->last_login_time];
                }
                $heading = ['User Id', 'First Name', 'Email Id', 'Design Count', 'Video Design Count', 'Upload Image Count', 'Last Login Time'];
                $file = fopen($original_csv_path, 'w');
                fputcsv($file, $heading);
                foreach ($users_data as $record) {
                    fputcsv($file, $record);
                }
                fclose($file);

                //make zip file from csv file in temp folder
                if ($zip->open($original_zip_path, ZipArchive::CREATE) === true) {
                    foreach (glob($original_csv_path) as $key => $value) {
                        $relative_name_in_zip_file = basename($value);
                        $zip->addFile($value, $relative_name_in_zip_file);
                    }
                    $zip->close();
                }
            }

            $total_report = [
                'count_user' => count($user_id_lists),
                'user' => 'Warning Mail Send To User',
            ];

            $subject = 'PhotoADKing: MonthlyReport: WarningMailSendToUser: ('.count($user_id_lists).') ';
            $template = 'total_deactive_user_report';
            $data = ['template' => $template, 'subject' => $subject, 'message_body' => $total_report, 'original_zip_path' => $original_zip_path, 'user_id_lists' => $user_id_lists];

            //send report to super admin attech with zip file
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                if (count($data['user_id_lists']) > 0) {
                    $message->attach($data['original_zip_path'], ['as' => 'monthly_report.zip', 'mime' => 'application/pdf']);
                }
            });

            //remove zip & csv file in templ folder
            if (($is_exist = ((new ImageController())->checkFileExist($original_csv_path)) != 0)) {
                unlink($original_csv_path);
            }
            if (($is_exist = ((new ImageController())->checkFileExist($original_zip_path)) != 0)) {
                unlink($original_zip_path);
            }

        } catch (Exception $e) {
            Log::error('WarningMailSendToUser : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            if (($is_exist = ((new ImageController())->checkFileExist($original_csv_path)) != 0)) {
                unlink($original_csv_path);
            }
            if (($is_exist = ((new ImageController())->checkFileExist($original_zip_path)) != 0)) {
                unlink($original_zip_path);
            }
        }
    }
}
