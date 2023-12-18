<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Config;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;
use Mail;

class DeleteUnusedRecordFromDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DeleteUnusedRecordFromDB';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remove 1 day or 1 month old or extra record from DB';

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

            $otp_codes_count = DB::select('SELECT COUNT(*) AS total FROM otp_codes WHERE TIMESTAMPDIFF(DAY,update_time,NOW()) > 1 ');
            $otp_codes_count = $otp_codes_count[0]->total;

            DB::beginTransaction();
            $user_registration_temp_count = DB::delete('DELETE FROM user_registration_temp WHERE TIMESTAMPDIFF(DAY,update_time,NOW()) > 1  ');
            $total_otp_record = DB::delete('DELETE FROM otp_codes WHERE TIMESTAMPDIFF(DAY,update_time,NOW()) > 1 ');
            $user_pwd_reset_token_master_count = DB::delete('DELETE FROM user_pwd_reset_token_master WHERE TIMESTAMPDIFF(DAY,update_time,NOW()) > 1 ');
            $failed_jobs_count = DB::delete('DELETE FROM failed_jobs WHERE TIMESTAMPDIFF(MONTH,failed_at,NOW()) > 1 ');
            $failed_jobs_detail_count = DB::delete('DELETE FROM failed_jobs_detail WHERE TIMESTAMPDIFF(MONTH,update_time,NOW()) > 1 ');
            //We delete record only de-active session. That's why we have to calculate session time from config/jwt.php
            $user_session_time = (config('jwt.ttl') + config('jwt.refresh_ttl')) * 2;
            $user_session_count = DB::delete('DELETE FROM user_session WHERE TIMESTAMPDIFF(MINUTE, update_time, NOW()) > ?', [$user_session_time]);
            $device_master_count = DB::delete('DELETE FROM device_master WHERE TIMESTAMPDIFF(MINUTE, update_time, NOW()) > ?', [$user_session_time]);
            $tag_master_count = DB::delete('DELETE FROM tag_analysis_master WHERE TIMESTAMPDIFF(MONTH, update_time, NOW()) > 1');
            DB::commit();

            $total_report = [
                'user_registration_temp' => $user_registration_temp_count,
                'otp_codes' => $total_otp_record,
                'user_pwd_reset_token_master' => $user_pwd_reset_token_master_count,
                'failed_jobs' => $failed_jobs_count,
                'failed_jobs_detail' => $failed_jobs_detail_count,
                'user_session' => $user_session_count,
                'device_master' => $device_master_count,
                'tag_analysis_master' => $tag_master_count,
            ];

            Log::info('DeleteUnusedRecordFromDB : Total deleted search tag record.', ['total_report' => $total_report]);

            return 1;

            //            Log::info('Deleted records from DB',["user_registration_temp_count"=>$user_registration_temp_count,"otp_codes_count"=>$otp_codes_count,
            //                "total_otp_record"=>$total_otp_record,"user_pwd_reset_token_master"=>$user_pwd_reset_token_master_count,"failed_jobs_count"=>$failed_jobs_count,
            //                "failed_jobs_detail_count"=>$failed_jobs_detail_count]);

            //            $total_report = array(
            //                "user_registration_temp"=>$user_registration_temp_count,
            //                "otp_codes"=>$otp_codes_count,
            //                "user_pwd_reset_token_master"=>$user_pwd_reset_token_master_count,
            //                "failed_jobs"=>$failed_jobs_count,
            //                "failed_jobs_detail"=>$failed_jobs_detail_count
            //            );
            //
            //            $subject = 'PhotoADKing: Daily report of deleted extra record from table';
            //            $template = "total_deleted_table_record_report";
            //            $data = array('template' => $template,'subject' =>$subject, 'message_body' => $total_report );

            $blade_data = [
                'date' => date('d-m-Y'),
                'blade_description' => 'Below is the daily report of delete extra record from table.',
                'table_heading' => '<tr style="font-size: 16px;">
                                      <th colspan="5">Table Name</th>
                                  </tr>
                                  <tr style="font-size: 16px;word-break: break-all">
                                    <th>user_registration_temp </th>
                                    <th>otp_codes</th>
                                    <th>user_pwd_reset_token_master</th>
                                    <th>failed_jobs</th>
                                    <th>failed_jobs_detail</th>
                                    <th>user_session_count</th>
                                    <th>device_master_count</th>
                                  </tr>',
                'table_description' => "<tr style=\"text-align: center;font-size: 20px;\">
                                        <td> $user_registration_temp_count </td>
                                        <td> $otp_codes_count </td>
                                        <td> $user_pwd_reset_token_master_count </td>
                                        <td> $failed_jobs_count </td>
                                        <td> $failed_jobs_detail_count </td>
                                        <td> $user_session_count </td>
                                        <td> $device_master_count </td>
                                      </tr>",

            ];

            $subject = 'PhotoADKing: Daily report of deleted extra record from table';
            $template = 'send_total_report_dynamically';
            $data = ['template' => $template, 'subject' => $subject, 'message_body' => $blade_data];

            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });

        } catch (Exception $e) {
            (new ImageController())->logs('DeleteUnusedRecordFromDB', $e);
            //Log::error("DeleteUnusedRecordFromDB : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

    }
}
