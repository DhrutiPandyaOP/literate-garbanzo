<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  SendNormalImageTagReport.php

File Created    :  Monday, 14th February 2022 10:41:03 pm
Author          :  Optimumbrew
Author Email    :  info@optimumbrew.com
Last Modified   :  Tuesday, 15th February 2022 10:56:00 pm
-----
Purpose          :  This file send mail of daily search tag report of normal images to admin, sub_admin and super_admin.
-----
Copyright 2018 - 2022 Optimumbrew Technology

*/

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Config;
use DB;
use Illuminate\Console\Command;
use Log;
use Mail;

class SendNormalImageTagReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendNormalImageReportMail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly normal image search tag report mail to admin.';

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
            //set start date(monday) and end date(sunday) of last week
            $start_date = date('Y-m-d', strtotime('last week monday'));
            $end_date = date('Y-m-d', strtotime('last week sunday'));

            $where = "DATE_FORMAT(tam.week_start_date, '%Y-%m-%d') >= '$start_date' AND
                     DATE_FORMAT(tam.week_end_date, '%Y-%m-%d') <= '$end_date'";

            $sub_category_array = [];
            $tags = DB::select('(SELECT
                                   tam.id,
                                   tam.tag,
                                   tam.is_success,
                                   tam.content_count,
                                   tam.sub_category_id,
                                   ct.name as category_name,
                                   scm.sub_category_name,
                                   CASE WHEN tam.content_type = "1,8" THEN "Image,SVG"
                                        WHEN tam.content_type = 3 THEN "Audio"
                                         END AS content_type,
                                   tam.search_count,
                                   tam.update_time
                                FROM
                                    tag_analysis_master as tam
                                LEFT JOIN
                                    sub_category_master as scm ON scm.id = tam.sub_category_id
                                LEFT JOIN
                                    category AS ct ON tam.category_id = ct.id
                                WHERE
                                    tam.is_success=1 AND
                                    (tam.content_type = "1,8"
                                    OR tam.content_type = 3) AND
                                    '.$where.'
                                    LIMIT 20)
                                UNION (SELECT
                                           tam.id,
                                           tam.tag,
                                           tam.is_success,
                                           tam.content_count,
                                           tam.sub_category_id,
                                           ct.name as category_name,
                                           scm.sub_category_name,
                                           CASE WHEN tam.content_type = "1,8" THEN "Image,SVG"
                                                WHEN tam.content_type = 3 THEN "Audio"
                                                 END AS content_type,
                                           tam.search_count,
                                           tam.update_time
                                        FROM
                                            tag_analysis_master as tam
                                        LEFT JOIN
                                            sub_category_master as scm ON scm.id=tam.sub_category_id
                                        LEFT JOIN category AS ct ON
                                            tam.category_id = ct.id
                                        WHERE
                                            tam.is_success=2 AND
                                            (tam.content_type = "1,8"
                                            OR tam.content_type = 3) AND
                                            '.$where.'
                                            LIMIT 20)
                                        ORDER BY is_success,search_count DESC');

            foreach ($tags as $i => $tag_detail) {
                if ($tag_detail->sub_category_id != '') {
                    if (! isset($sub_category_array[$tag_detail->sub_category_name])) {
                        $sub_category = DB::select('SELECT GROUP_CONCAT(scm.sub_category_name SEPARATOR " + ") AS sub_category_name FROM sub_category_master AS scm WHERE scm.id IN('.$tag_detail->sub_category_id.')');
                        $sub_category_array[$tag_detail->sub_category_name] = $sub_category[0]->sub_category_name;
                    }
                    $tag_detail->sub_category_name = $sub_category_array[$tag_detail->sub_category_name];
                }
            }

            if (count($tags) > 0) {
                $app_name = Config::get('constant.APP_HOST_NAME');
                $data = ['data' => ['app_name' => $app_name, 'start_date' => $start_date, 'end_date' => $end_date, 'tags' => $tags]];

                Mail::send('normal_image_tag_report', $data, function ($message) {
                    $message->to(Config::get('constant.SUPER_ADMIN_EMAIL_ID'))->subject('PhotoADKing: Search tag analysis report');
                    $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject('PhotoADKing: Search tag analysis report');
                    $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject('PhotoADKing: Search tag analysis report');
                });
            } else {
                Log::info('SendNormalImageTagReport : No search tag found for last week.');
            }

        } catch (Exception $e) {
            (new ImageController())->logs('SendNormalImageTagReport command handle()', $e);
            //      Log::error("SendNormalImageTagReport command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config::get('constants.EXCEPTION_ERROR').' send normal image search tag report', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }
}
