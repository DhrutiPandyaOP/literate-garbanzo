<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  MoveMultiPageJsonDataToS3.php

File Created    :  Monday, 10th May 2021 05:22:26 pm
Author          :  Optimumbrew
Auther Email    :  info@optimumbrew.com
Last Modified   :  Monday, 10th May 2021 05:22:26 pm
-----
Purpose          :  This scheduler get multipage json data from database & move this data to s3 bucket (make .txt file & put this json data to this file).

-----
Copyright 2018 - 2021 Optimumbrew Technology

*/

namespace App\Console\Commands;

use App\Jobs\DeleteFileFromBucket;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class MoveMultiPageJsonDataToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MoveMultiPageJsonDataToS3';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move MultiPage Json Data To S3';

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

            $count_json_data_moved = 0;
            $time_start = microtime(true);
            $total_designs = DB::select('SELECT
                                            id,
                                            uuid,
                                            json_data,
                                            json_file_name,
                                            update_time
                                          FROM my_design_master
                                            WHERE
                                                is_active = 1 AND
                                                TIMESTAMPDIFF(DAY,update_time,NOW()) >= 1 AND
                                                #TIMESTAMPDIFF(HOUR,update_time,NOW()) >= 1 AND
                                                json_data IS NOT NULL AND
                                                is_multipage = 1 AND
                                                content_type = '.Config::get('constant.IMAGE').'
                                             ORDER BY id ');

            foreach ($total_designs as $design) {

                $update_time = DB::select('SELECT update_time FROM my_design_master WHERE id=?', [$design->id]);
                if ($design->update_time != $update_time[0]->update_time) {
                    Log::info('MoveMultiPageJsonDataToS3 : design is updated after schedule starts', ['design_id' => $design->id]);

                    continue;
                }

                $json_file_name = $design->json_file_name;
                if ($json_file_name == null) {
                    $json_file_name = $design->uuid.'_json_data_'.time().'.txt';
                }

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    $aws_bucket = Config::get('constant.AWS_BUCKET');
                    $disk = Storage::disk('s3');
                    $resource_targetFile = "$aws_bucket/json/".$json_file_name;
                    $disk->put($resource_targetFile, $design->json_data, 'public');
                } else {

                    $json_file_path = './..'.Config::get('constant.JSON_FILE_DIRECTORY').$json_file_name;
                    file_put_contents($json_file_path, $design->json_data);
                }

                DB::beginTransaction();
                $update_status = DB::update('UPDATE
                        my_design_master SET
                          json_data = NULL,
                          json_file_name = ?,
                          update_time = update_time
                        WHERE id=? ', [$json_file_name, $design->id]);
                DB::commit();

                $count_json_data_moved++;

            }

            $time_end = microtime(true);
            $execution_time = intval($time_end - $time_start).' Sec';

            $blade_data = [
                'date' => date('d-m-Y'),
                'blade_description' => "Below is the daily report of user's json_data moved in s3.",
                'table_heading' => '<tr style="font-size: 16px;">
                                    <th>Total Json Moved </th>
                                    <th>Total Processing Time</th>
                                  </tr>',
                'table_description' => "<tr style=\"text-align: center;font-size: 20px;\">
                                        <td> $count_json_data_moved </td>
                                        <td> $execution_time </td>
                                      </tr>",

            ];

            $subject = 'PhotoADKing: DailyReport: TotalJsonDataMoveToS3('.$count_json_data_moved.')';
            $template = 'send_total_report_dynamically';
            $data = ['template' => $template, 'subject' => $subject, 'message_body' => $blade_data];

            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });

            $all_stock_videos = DB::select('SELECT id, video FROM stock_videos_master');
            $stock_video_full_path = Config::get('constant.STOCK_VIDEOS_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');
            $stock_video_full_s3_path = Config::get('constant.STOCK_VIDEOS_IMAGES_DIRECTORY_OF_S3');
            $stock_video_dir = Config::get('constant.STOCK_VIDEOS_IMAGES_DIRECTORY');
            $deleted_stock_video_id = [];
            $video_content_type = Config::get('constant.VIDEO');

            DB::beginTransaction();

            foreach ($all_stock_videos as $stock_video) {

                $video_designs = DB::select('SELECT
                                                        id
                                                    FROM
                                                          my_design_master
                                                    WHERE
                                                        (JSON_EXTRACT(json_data, "$.background_json.background_image") = ? OR
                                                         JSON_EXTRACT(json_data, "$.background_json.background_image") = ?) AND
                                                        content_type = ? ', [$stock_video_full_path.$stock_video->video, $stock_video_full_s3_path.$stock_video->video, $video_content_type]);

                if ($video_designs) {
                    $design_ids = implode(',', array_column($video_designs, 'id'));
                    DB::update('UPDATE stock_videos_master SET design_ids = ? WHERE id = ?', [$design_ids, $stock_video->id]);
                } else {
                    DeleteFileFromBucket::dispatch($stock_video_dir, $stock_video->video, 'stock_videos');
                    $deleted_stock_video_id[] = $stock_video->id;
                }
            }

            if ($deleted_stock_video_id) {
                DB::delete('DELETE FROM stock_videos_master WHERE id IN ('.implode(',', $deleted_stock_video_id).')');
            }

            DB::commit();

            //shell_exec('find '.Config::get('constant.IMAGE_BUCKET_CHUNKS_PATH').'* -mmin +59 -exec rm -r {} \;');
            shell_exec('find /var/www/html/image_bucket/chunks/* -mmin +59 -exec rm -r {} \;');

        } catch (Exception $e) {
            Log::error('MoveMultiPageJsonDataToS3 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }

    }
}
