<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use Config;
use DateTime;
use Exception;
use File;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Log;

class PreviewVideoJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $job_id;

    protected $content_id;

    protected $result_status; // All process status : 1=Success, 0=Fail

    protected $old_preview_video_name;

    public function __construct($content_id, $job_id, $old_preview_video_name = '')
    {
        try {
            //      Log::info('----------------------------------------------------------------------------');
            //      Log::info('__construct()');
            //All file
            $this->job_id = $job_id;
            $this->content_id = $content_id;
            $this->old_preview_video_name = $old_preview_video_name;
            $this->result_status = 1;
        } catch (Exception $e) {
            Log::error('PreviewVideoJob.php construct() : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
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
        //-f concat =   tells ffmpeg to use the concat filter
        //-safe 0   =   has something to do with letting ffmpeg read relative paths in the text file
        //-map 0:v  =   select video from 1st input file
        //-map 2:a  =   select audio from 3rd input position
        //-filter_complex = filtering using ffmpeg
        //-shortest =   take smallest duration from Audio & Video
        //-ss       =   trim video
        //overlay   =   overlay one video on top of another
        //-an       =   disables audio stream
        //-af       =`  create the filtergraph specified by filtergraph and use it to filter the stream.
        //-apad     =   pad the end of an audio stream with silence.
        //-shortest =   to extend audio streams to the same length as the video stream.
        //-y        =   force file overwrite
        $ready = 1;
        $failed = 2;
        try {

            //      Log::info('handle()');

            //Get video detail for preview generate
            $video_detail = DB::select('SELECT * FROM preview_video_jobs WHERE id = ?', [$this->job_id]);

            if (count($video_detail) == 0) {
                return '';
            }
            $content_detail = DB::select('SELECT * FROM content_master WHERE id = ?', [$this->content_id]);
            if (count($content_detail) == 0) {
                return '';
            }

            $json_data = $content_detail[0]->json_data;
            $json_data = json_decode($json_data);
            $bg_video_name = $json_data->background_json->background_image;
            $video_name = $content_detail[0]->content_file; //video name
            $transparent_img = $video_detail[0]->transparent_img; //transparent image
            $output_height = $video_detail[0]->output_height; //output file's height
            $output_width = $video_detail[0]->output_width; //output file's weight
            $output_file_name = uniqid().'_'.'preview_video'.'_'.time().'.mp4'; // Generate preview name

            //      //Get file path
            //      $input_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . $video_name;
            //      $transparent_img =  Config::get('constant.TEMP_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING') . $transparent_img;
            //      $output_file = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . $output_file_name;
            //      $output_folder_name = 'video';

            //Get file path
            $input_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$video_name;
            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $input_video = '"'.$input_video.'"';
            }

            $transparent_img = './..'.Config::get('constant.TEMP_DIRECTORY').$transparent_img;
            $output_file = './..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$output_file_name;
            $output_folder_name = 'video';

            //            $dimension = (new ImageController())->getVideoDimension($input_video);
            //            $output_width = $dimension->getWidth()/2;
            //            $output_height = $dimension->getHeight()/2;
            //      Log::info(['output_width' => $output_width, 'output_height' => $output_height]);

            $ffmpeg = Config::get('constant.FFMPEG_PATH');
            //      Log::info("Request cycle Queues started");

            //==============| Generate preview video|==============//

            /* crf = As others have pointed out (Thanks all), the values will depend on which encoder you're using.
                     For x264 your valid range is 0-51:
                            Where 0 is lossless, 23 is default, and 51 is worst possible. A lower value is a higher quality.
            */

            /*
             * ffmpeg -i INPUT.mp4 -i overlay.png
             * -filter_complex "[0:v]scale=trunc(270/2)*2:trunc(480/2)*2[video];[1:v]scale=trunc(270/2)*2:trunc(480/2)*2[bg];[bg][video]overlay=0:0[bv];[1:v]scale=trunc(270/2)*2:trunc(480/2)*2[wf];[bv][wf]overlay=0:0"
             * -crf 40
             * -y
             * -preset ultrafast
             * OUTPUT.mp4
             * 2>&1
             */

            $cmd = $ffmpeg.' -i '.$input_video.' -i '.$transparent_img.' -filter_complex'.
              ' "[0:v]scale=trunc('.$output_width.'/2)*2:trunc('.$output_height.'/2)*2[video];'.''
              ."[1:v]scale=trunc($output_width/2)*2:trunc($output_height/2)*2[bg];".''
              .'[video][bg]overlay=0:0"'.' -t 10 -an -crf 35 -vcodec mpeg4 -pix_fmt yuv420p -c:v libx264  -y -preset ultrafast '.$output_file.' 2>&1';
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
                //        if($response == ""){
                //          $format_name = NULL;
                //          $duration = NULL;
                //          $size = NULL;
                //          $bit_rate = NULL;
                //          $title = NULL;
                //          $genre = NULL;
                //          $artist = NULL;
                //          $width = NULL;
                //          $height = NULL;
                //        }else{
                //          //Log::info('PreviewVideoJob', [$response]);
                //          $format_name = $response['format_name'];
                //          $duration = $response['duration'];
                //          $size = $response['size'];
                //          $bit_rate = $response['bit_rate'];
                //          $title = $response['title'];
                //          $genre = $response['genre'];
                //          $artist = $response['artist'];
                //          $width = $response['width'];
                //          $height = $response['height'];
                //        }
                //
                //        $pvd = DB::select('SELECT * FROM video_details WHERE content_id = ?', [$this->content_id]);
                //
                //        DB::beginTransaction();
                //
                //        DB::update('UPDATE preview_video_jobs
                //                            SET status=?, cmd_execute_time = ?, preview_video=?,content_id=?,output_height=?,output_width=?
                //                             WHERE id =?',
                //          [$ready, $cmd_execute_time, $output_file_name, $this->content_id, $output_height, $output_width, $this->job_id]);
                //        DB::update('UPDATE content_master
                //                            SET content_file = ?
                //                             WHERE id = ?',
                //          [$output_file_name, $this->content_id]);
                //
                //        if (count($pvd) > 0) {
                //          DB::update('UPDATE video_details
                //                                SET format_name = ?,
                //                                    file_name = ?,
                //                                    file_path = ?,
                //                                    duration = ?,
                //                                    width=?,
                //                                    height = ?,
                //                                    size = ?,
                //                                    bit_rate = ?,
                //                                    genre = ?,
                //                                    title = ?,
                //                                    artist = ?
                //                                WHERE content_id = ?',
                //            [
                //              $format_name,
                //              $output_file_name,
                //              $output_folder_name,
                //              $duration,
                //              $width,
                //              $height,
                //              $size,
                //              $bit_rate,
                //              $genre,
                //              $title,
                //              $artist,
                //              $this->content_id
                //            ]);
                //        } else {
                //          $create_time = date('Y-m-d H:i:s');
                //          DB::insert('INSERT INTO video_details
                //                        (content_id,format_name, file_name, file_path, duration, width, height, size, bit_rate, genre, title,artist, is_active, create_time)
                //                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ',
                //            [$this->content_id, $format_name, $output_file_name, $output_folder_name, $duration, $width, $height, $size, $bit_rate, $genre, $title, $artist, 1, $create_time]);
                //        }

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

                if (! empty($this->old_preview_video_name) && $bg_video_name != $this->old_preview_video_name) {
                    (new ImageController())->deleteVideoFromJob($this->old_preview_video_name);
                }

                (new ImageController())->unlinkLocalStorageFileFromFilePath($transparent_img);

                DB::commit();

                //        Log::info('set status=1');
                //        Log::info($output_file);
                //        Log::info('----------------------------------------------------------------------------');

                $this->result_status = 1;
            } else {
                Log::error('PreviewVideoJob.php handle() : ', ['preview_id' => $this->job_id]);
                Log::error('PreviewVideoJob.php handle()_output : ', [$output]);
                Log::error('PreviewVideoJob.php handle()_result : ', [$result]);
                DB::beginTransaction();
                DB::update('UPDATE preview_video_jobs SET status=? WHERE id =? ', [$failed, $this->job_id]);
                DB::update('UPDATE content_master SET is_active = ? WHERE id = ?', [0, $this->content_id]);
                DB::commit();
                $this->result_status = 0;
            }

        } catch (Exception $e) {
            (new ImageController())->logs('PreviewVideoJob.php handle catch()', $e);
            Log::error('PreviewVideoJob.php handle catch() : ', ['preview_id' => $this->job_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
            DB::beginTransaction();
            DB::update('UPDATE preview_video_jobs SET status=? WHERE id =? ', [$failed, $this->job_id]);
            DB::commit();
            $this->result_status = 0;
        }
    }

    public function failed(Exception $e)
    {
        $failed = 2;
        Log::error('PreviewVideoJob.php failed() : ', ['preview_id' => $this->job_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
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
