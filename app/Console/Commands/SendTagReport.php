<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  SendTagReport.php

File Created    :  Monday, 27th April 2020 10:41:03 pm
Author          :  Optimumbrew
Author Email    :  info@optimumbrew.com
Last Modified   :  Tuesday, 15th February 2022 10:56:00 pm
-----
Purpose          :  This file send mail of daily search tag report of templates to admin, sub_admin and super_admin.
-----
Copyright 2018 - 2022 Optimumbrew Technology

*/

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Illuminate\Console\Command;
use App\Mail\TagReportMail;
use Response;
use JWTAuth;
use Exception;
use Log;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendTagReport extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'sendreportmail';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send weekly search tag report mail to admin.';

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
    try{
      $start_date = date("Y-m-d", strtotime("last week monday"));
      $end_date = date("Y-m-d", strtotime("last week sunday"));
//      $end_date = date("Y-m-d");

      $where ="DATE_FORMAT(tam.week_start_date, '%Y-%m-%d') >= '$start_date' AND
                     DATE_FORMAT(tam.week_end_date, '%Y-%m-%d') <= '$end_date'";


      $tags = DB::select('(SELECT
                                    tam.id,
                                    tam.tag,
                                    tam.is_success,
                                    tam.content_count,
                                    scm.sub_category_name,
                                    tam.content_type,
                                    CASE WHEN tam.content_type = 4 THEN "Image"
                                         WHEN tam.content_type = 9 THEN "Video"
                                         WHEN tam.content_type = 10 THEN "Intro Video"
                                         ELSE "All"
                                         END AS content_type,
                                    tam.search_count,
                                    tam.update_time
                                FROM
                                    tag_analysis_master as tam
                                LEFT JOIN
                                    sub_category_master as scm
                                ON
                                    scm.id=tam.sub_category_id
                                WHERE
                                    tam.is_success=1 AND
                                    (tam.content_type = 0
                                    OR tam.content_type = 4
                                    OR tam.content_type = 9
                                    OR tam.content_type = 10) AND
                                    '.$where.'
                                    LIMIT 20)
                                UNION
                                 (SELECT
                                     tam.id,
                                     tam.tag,
                                     tam.is_success,
                                     tam.content_count,
                                     scm.sub_category_name,
                                     tam.content_type,
                                     CASE WHEN tam.content_type = 4 THEN "Image"
                                          WHEN tam.content_type = 9 THEN "Video"
                                          WHEN tam.content_type = 10 THEN "Intro Video"
                                          ELSE "All"
                                          END AS content_type,
                                     tam.search_count,
                                     tam.update_time
                                FROM
                                    tag_analysis_master as tam
                                LEFT JOIN
                                    sub_category_master as scm
                                ON
                                    scm.id=tam.sub_category_id
                                WHERE
                                    tam.is_success=2 AND
                                    (tam.content_type = 0
                                    OR tam.content_type = 4
                                    OR tam.content_type = 9
                                    OR tam.content_type = 10) AND
                                    '.$where.'
                                    LIMIT 20)
                                ORDER BY is_success,search_count DESC');

      $admin_email= Config::get('constant.ADMIN_EMAIL_ID');
      $sub_admin_email= Config::get('constant.SUB_ADMIN_EMAIL_ID');
      $app_name = Config::get('constant.APP_HOST_NAME');
      $data  = array("app_name" => $app_name,'start_date'=>$start_date,'end_date'=>$end_date,'tags'=>$tags);

//      Log::info('mail report : ',['data'=>$data,'admin_email'=>$admin_email,'sub_admin_email'=>$sub_admin_email]);
      // Mail::to("bhargav.optimumbrew@gmail.com")->send(new TagReportMail($data));
      Mail::to($admin_email)
        ->bcc($sub_admin_email)
        ->send(new TagReportMail($data));
    } catch (Exception $e) {
      (new ImageController())->logs("SendTagReport command handle()",$e);
//      Log::error("SendTagReport command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      return Response::json(array('code' => 201, 'message' =>Config::get('constants.EXCEPTION_ERROR'). ' send search tag report', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
  }
}
