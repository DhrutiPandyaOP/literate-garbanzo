<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use Config;
use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Log;

class IntrosPreviewVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $job_id;

    protected $content_id;

    protected $result_status; // All process status : 1=Success, 0=Fail

    protected $old_preview_video_name;

    protected $resource_video_name;

    protected $is_rename;

    public function __construct($content_id, $job_id, $old_preview_video_name = '', $resource_video_name = '')
    {
        try {
            //      Log::info('----------------------------------------------------------------------------');
            //      Log::info('__construct()');
            //All file
            $this->job_id = $job_id;
            $this->content_id = $content_id;
            $this->old_preview_video_name = $old_preview_video_name;
            $this->resource_video_name = $resource_video_name;
            $this->result_status = 1;
            $this->is_rename = 0;
        } catch (Exception $e) {
            Log::error('IntrosPreviewVideoJob.php construct() : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            $this->result_status = 0;
        }
    }

    public function handle()
    {
        /*-->2 is the console standard error.
              -->1 is the console standard output.
              -->"2>&1" : the standard error is redirected to standard output, so you can see both outputs together
                    -$ cmd 2>&1
             *--> you can see all the output, including errors, in the debug.log.
                    -$ cmd 1>out.log 2>err.log */

        //-y        =   force file overwrite
        $ready = 1;
        $failed = 2;
        try {

            //      Log::info('handle()');
            //Get video detail for preview generate
            $video_detail = DB::select('SELECT id,output_height,output_width FROM preview_video_jobs WHERE id = ?', [$this->job_id]);

            if (count($video_detail) == 0) {
                return '';
            }
            $content_detail = DB::select('SELECT id,content_file,json_data FROM content_master WHERE id = ?', [$this->content_id]);
            if (count($content_detail) == 0) {
                return '';
            }

            $video_name = $content_detail[0]->content_file; //video name
            $output_height = $video_detail[0]->output_height; //output file's height
            $output_width = $video_detail[0]->output_width; //output file's weight
            $output_file_name = uniqid().'_'.'preview_video'.'_'.time().'.mp4'; // Generate preview name

            //Get file path
            $input_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$video_name;
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $input_video = '"'.$input_video.'"';
            }

            $output_file = './..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$output_file_name;
            $output_folder_name = 'video';

            $ffmpeg = Config::get('constant.FFMPEG_PATH');
            //      Log::info("Request cycle Queues started");

            //==============| Generate preview video|==============//

            /* crf = As others have pointed out (Thanks all), the values will depend on which encoder you're using.
                     For x264 your valid range is 0-51:
                            Where 0 is lossless, 23 is default, and 51 is worst possible. A lower value is a higher quality.
            */

            $cmd = $ffmpeg.' -i '.$input_video." -t 10 -crf 40 -s $output_width".'x'."$output_height -vcodec mpeg4 -pix_fmt yuv420p -c:v libx264  -y -preset ultrafast ".$output_file.' 2>&1';
            //      Log::info($cmd);

            $start = date('Y-m-d H:i:s');
            exec($cmd, $output, $result);
            $end = date('Y-m-d H:i:s');

            //      Log::info("Request cycle Queues finished");

            $datetime1 = new DateTime($start);
            $datetime2 = new DateTime($end);
            $interval = $datetime1->diff($datetime2);
            $cmd_execute_time = $interval->format('%H:%I:%S');
            //      Log::info($cmd_execute_time);

            if (file_exists($output_file) && $result == 0) {

                $video_info = (new ImageController())->getVideoInformation($output_file);

                (new ImageController())->saveVideoInformation($video_info, $output_file_name, $output_folder_name, $this->content_id);

                DB::beginTransaction();

                DB::update('UPDATE preview_video_jobs
                              SET status=?, cmd_execute_time = ?, preview_video=?,content_id=?,output_height=?,output_width=?
                               WHERE id =?',
                    [$ready, $cmd_execute_time, $output_file_name, $this->content_id, $output_height, $output_width, $this->job_id]);
                DB::update('UPDATE content_master
                              SET content_file = ?
                               WHERE id = ?',
                    [$output_file_name, $this->content_id]);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveJsonPreviewVideoInToS3($output_file_name, 1);
                }

                if (! empty($this->old_preview_video_name)) {
                    (new ImageController())->deleteVideoFromJob($this->old_preview_video_name);
                }

                if (! empty($video_name)) {
                    (new ImageController())->deleteVideoFromJob($video_name);
                }

                DB::commit();

                $this->result_status = 1;
            } else {
                Log::error('IntrosPreviewVideoJob.php handle() : ', ['preview_id' => $this->job_id]);
                Log::error('IntrosPreviewVideoJob.php handle()_output : ', [$output]);
                Log::error('IntrosPreviewVideoJob.php handle()_result : ', [$result]);
                DB::beginTransaction();
                DB::update('UPDATE preview_video_jobs SET status=? WHERE id =? ', [$failed, $this->job_id]);
                DB::update('UPDATE content_master SET is_active = ? WHERE id = ?', [0, $this->content_id]);
                DB::commit();
                $this->result_status = 0;
            }

            /* Compressed row video */
            if ($this->resource_video_name != '' && $result == 0) {
                $json_data = json_decode($content_detail[0]->json_data);

                $row_width = $json_data->video_json->video_width;
                $row_height = $json_data->video_json->video_height;
                $output_dimension = (new ImageController())->generateRowVideoHeightWidth($row_width, $row_height);
                $row_output_height = $output_dimension['height'];
                $row_output_width = $output_dimension['width'];
                //Get file path
                $input_row_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$this->resource_video_name;
                //        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $input_row_video = '"'.$input_row_video.'"';
                //        }
                $output_file_name = $this->resource_video_name;
                if (preg_match('/[\'^£$%&*()}{@#~?><>,|=+-]/', $this->resource_video_name)) {
                    $output_file_name = uniqid().'_'.'row_video'.'_'.time().'.mp4';
                    $this->is_rename = 1;
                }
                $output_row_file = './..'.Config::get('constant.THUMBNAIL_VIDEO_DIRECTORY').$output_file_name;

                $row_cmd = $ffmpeg.' -i '.$input_row_video." -crf 27 -an -s $row_output_width".'x'."$row_output_height  -vcodec mpeg4 -pix_fmt yuv420p -c:v libx264 -y -preset ultrafast ".$output_row_file.' 2>&1';
                exec($row_cmd, $row_output, $row_result);

                if (file_exists($output_row_file) && $row_result == 0) {
                    if ($this->is_rename) {
                        $thumbnail_path = './..'.Config::get('constant.THUMBNAIL_VIDEO_DIRECTORY');
                        $old_name = $thumbnail_path.$output_file_name;
                        $new_name = $thumbnail_path.$this->resource_video_name;
                        rename($old_name, $new_name);
                    }
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveThumbnailVideoInToS3($this->resource_video_name, 1);
                    }
                    $this->result_status = 1;
                } else {
                    Log::error('IntrosPreviewVideoJob.php Row cmd: ', [$row_cmd]);
                    Log::error('IntrosPreviewVideoJob.php Row handle()_output : ', [$row_output]);
                    Log::error('IntrosPreviewVideoJob.php Row handle()_result : ', [$row_result]);
                    DB::beginTransaction();
                    DB::update('UPDATE preview_video_jobs SET status=? WHERE id =? ', [$failed, $this->job_id]);
                    DB::update('UPDATE content_master SET is_active = ? WHERE id = ?', [0, $this->content_id]);
                    DB::commit();
                    $this->result_status = 0;
                }
            }
        } catch (Exception $e) {
            (new ImageController())->logs('IntrosPreviewVideoJob.php handle catch()', $e);
            //      Log::error("IntrosPreviewVideoJob.php handle catch() : ", ["preview_id" => $this->job_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
            DB::beginTransaction();
            DB::update('UPDATE preview_video_jobs SET status=? WHERE id =? ', [$failed, $this->job_id]);
            DB::commit();
            $this->result_status = 0;
        }
    }

    public function failed(Exception $e)
    {
        $failed = 2;
        Log::error('IntrosPreviewVideoJob.php failed() : ', ['preview_id' => $this->job_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
        DB::beginTransaction();
        DB::update('UPDATE preview_video_jobs SET status=? WHERE id =? ', [$failed, $this->job_id]);
        DB::commit();
        //    Log::info('set status=2 failed()');
        $this->result_status = 0;
    }

    public function getResponse()
    {
        return ['preview_id' => $this->job_id,
            'result_status' => $this->result_status,
        ];
    }
}
