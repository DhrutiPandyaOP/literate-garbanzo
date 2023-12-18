<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use DateTime;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class VideoTemplateJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    protected $video_name; //Video name for background

    protected $transparent_img; // Transparent image for Overlay

    protected $audio_name; // Audio file name for set audio

    protected $img_size; //Transparent image size in bytes

    protected $output_file; //Output video file name

    protected $out_width; // output video width

    protected $out_height; // output video height

    protected $quality; // Quality of out_video 1 = web quality, 2 = HD quality

    protected $is_trim; // video triming : 1=video trim

    protected $start_time; //video triming start time

    protected $trim_duration; //video triming offset time

    protected $end_time; //video triming end time

    protected $is_audio_mute; //audio mute of video : 1=audio mute

    protected $is_audio_trim; // For audio trim

    protected $audio_start_time; // Audio's trimming start time

    protected $audio_end_time; // Audio's trimming end time

    protected $audio_duration; // Audio's trimming duration time

    protected $download_id; // Download_id for download output video

    protected $status; // Video generate status

    protected $result_status; // All process status : 1=Success, 0=Fail

    protected $is_video_user_uploaded; // Check is user uploaded video

    protected $is_audio_user_uploaded; // Check is user uploaded audio

    protected $watermark_pos; //

    protected $content_type; //

    protected $design_id; //

    protected $user_id; //

    protected $get_download_id; //

    public function __construct($image, $img_size, $request_body)
    {
        try {
            //      Log::info('----------------------------------------------------------------------------');
            //      Log::info('__construct()');
            $request = json_decode($request_body);

            //All file
            $this->video_name = $request->video_name;
            $this->user_id = $request->user_id;
            $this->is_video_user_uploaded = isset($request->is_video_user_uploaded) ? $request->is_video_user_uploaded : ''; //0=collection video,1=user uploaded video ,2=pixabay video
            $this->transparent_img = $image;
            $this->audio_name = isset($request->audio_name) ? $request->audio_name : '';
            $this->is_audio_user_uploaded = isset($request->is_audio_user_uploaded) ? $request->is_audio_user_uploaded : '';

            //Output filter
            $this->out_width = $request->out_width; //output video's width
            $this->out_height = $request->out_height; //output video's height
            $this->quality = $request->quality;
            $this->watermark_pos = isset($request->watermark_pos) ? $request->watermark_pos : 1;

            //Input video's filter
            $this->is_trim = $request->is_trim; //Is it video trim?
            $this->start_time = isset($request->start_time) ? $request->start_time : ''; //Video's start time
            $this->end_time = isset($request->end_time) ? $request->end_time : ''; //Video's end time
            $this->trim_duration = isset($request->trim_duration) ? $request->trim_duration : ''; //video's duration

            //Check output video is mute or unmute
            $this->is_audio_mute = isset($request->is_audio_mute) ? $request->is_audio_mute : ''; // Video's audio mute

            //Input Audio's filter
            $this->is_audio_trim = isset($request->is_audio_trim) ? $request->is_audio_trim : ''; // is_audio_trim
            $this->audio_start_time = isset($request->audio_start_time) ? $request->audio_start_time : ''; //Audio's start time
            $this->audio_end_time = isset($request->audio_end_time) ? $request->audio_end_time : ''; //Audio's end time
            $this->audio_duration = isset($request->audio_duration) ? $request->audio_duration : ''; //Audio's duration

            $this->design_id = isset($request->get_my_design_id) ? $request->get_my_design_id : null;
            $this->content_type = isset($request->content_type) ? $request->content_type : null;

            $create_at = date('Y-m-d H:i:s');
            $this->status = 0;
            $this->download_id = base64_encode($image);

            $video_template_jobs = [
                'download_id' => $this->download_id,
                'user_id' => $this->user_id,
                'request_body' => $request_body,
                'image' => $this->transparent_img,
                'image_size' => $img_size,
                'bg_file' => $this->video_name,
                'bg_file_type' => 1, //Bg file type 1 = video
                'content_type' => $this->content_type,
                'my_design_id' => $this->design_id,
                'status' => $this->status,
                'quality' => $this->quality,
                'create_time' => $create_at,
            ];

            $this->get_download_id = DB::table('video_template_jobs')->insertGetId($video_template_jobs);
            DB::update('UPDATE video_generate_history_master SET download_id=? WHERE download_id IS NULL AND design_id=?', [$this->get_download_id, $this->design_id]);
            (new userController())->addVideoGenerateHistory('queue', $this->user_id, $this->get_download_id, $this->design_id, null, 2, $this->status);

            /*DB::beginTransaction();
            DB::insert('INSERT INTO video_template_jobs(download_id,user_id,request_body, image, image_size, bg_file, bg_file_type, content_type, my_design_id, status,quality,create_time)
                          VALUES(?,?,?,?,?,?,?,?,?,?,?,?)', [
              $this->download_id,
              $this->user_id,
              $request_body,
              $this->transparent_img,
              $img_size,
              $this->video_name,
              1, //Bg file type 1 = video
              $this->content_type,
              $this->design_id,
              $this->status,
              $this->quality,
              $create_at]);
            DB::commit();*/
            $this->result_status = 1;
        } catch (Exception $e) {
            DB::rollBack();
            (new ImageController())->logs('VideoTemplateJob.php construct()', $e);
            //Log::error("VideoTemplateJob.php construct() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $this->result_status = 0;
            $fail_reason = json_encode(['Exception' => $e->getMessage()]);
            DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [2, $this->download_id]);
            (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, $this->design_id, null, 2, 2);

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
            $video = $this->video_name;
            $transparent_img = $this->transparent_img;
            $output_file_name = uniqid().'_output_video_'.time().'.mp4';
            $is_video_user_uploaded = $this->is_video_user_uploaded;
            $video_db = null;
            $audio_db = null;
            $video_id = null;
            $relative_temp_dir = './..'.Config::get('constant.TEMP_DIRECTORY');

            //Check is video is user uploaded and get video's detail by id
            if ($is_video_user_uploaded == 1) {
                $input_video = Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY_OF_DIGITAL_OCEAN').$video;
                $get_video_id = DB::select('SELECT id FROM user_uploaded_video WHERE file_name = ?', [$video]);
                if ($get_video_id) {
                    $video_id = $get_video_id[0]->id;
                }
            } elseif ($is_video_user_uploaded == 2) {
                $input_video = $video;
                $video = uniqid().'_stock_videos_'.time().'.'.strtok(pathinfo($video, PATHINFO_EXTENSION), '?');
            } else {
                $input_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$video;
                $get_video_id = DB::select('SELECT id FROM video_details WHERE file_name = ?', [$video]);
                if ($get_video_id) {
                    $video_id = $get_video_id[0]->id;
                }
            }

            $local_input_video = $relative_temp_dir.$video;
            copy($input_video, $local_input_video);
            $input_video = '"'.$local_input_video.'"';

            //Add video detail in db
            $video_id = isset($video_id) ? "video_file_id = $video_id ," : '';
            //      $video_db = "is_video_user_uploaded = $is_video_user_uploaded , video_file_id = $video_id ,";
            $video_db = "is_video_user_uploaded = $is_video_user_uploaded , $video_id";

            //original path of transparent image and output video path
            $transparent_img = $relative_temp_dir.$transparent_img;
            $output_file = $relative_temp_dir.$output_file_name;

            //Filter of video trim
            $this->is_trim == 1 ? $trim = " -ss $this->start_time -t $this->trim_duration" : $trim = '';
            $this->is_audio_mute == 1 ? $mute = ' -an' : $mute = ''; //disable audio
            if (empty($this->audio_name)) {
                $audio = '';
            } else {
                $audio = '';
                $audioFile = '';
                $audio_id = null;
                if (! $this->is_audio_mute) {
                    //          Log::info('audio_name :', [$this->audio_name]);
                    //Get audio detail by audio type
                    if ($this->is_audio_user_uploaded == 1) {
                        $audio_original_path = Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY_OF_DIGITAL_OCEAN').$this->audio_name;
                        $audio_path_trim = 'trim'.$this->audio_name;
                        //            $get_aud_id = DB::select('SELECT id FROM user_uploaded_video WHERE file_name = ?', [$this->audio_name]);
                        $get_aud_id = DB::select('SELECT id FROM user_uploaded_audio WHERE file_name = ?', [$this->audio_name]);
                        if (count($get_aud_id) > 0) {
                            $audio_id = $get_aud_id[0]->id;
                        }
                    } else {
                        $audio_original_path = Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').$this->audio_name;
                        $audio_path_trim = 'trim'.$this->audio_name;
                        $get_aud_id = DB::select('SELECT id FROM audio_master WHERE file_name = ?', [$this->audio_name]);
                        if (count($get_aud_id) > 0) {
                            $audio_id = $get_aud_id[0]->id;
                        }
                    }
                    //          Log::info('audio_original_path :', [$audio_original_path]);
                    //          Log::info('audio_id :', [$audio_id]);
                    //Add audio detail in db
                    $audio_id = isset($audio_id) ? "audio_file_id = $audio_id ," : '';
                    $audio_db = "is_audio_user_uploaded = $this->is_audio_user_uploaded ,$audio_id ";
                    if ($this->is_audio_trim == 1) {
                        $audio_path = (new ImageController())->trimAudioByJob($audio_original_path, $audio_path_trim, $this->audio_start_time, $this->audio_duration, $this->get_download_id);
                        if ($audio_path == '') {
                            Log::error('VideoTemplateJob.php trimAudioByJob() : ', ['download_id' => $this->download_id]);
                            DB::beginTransaction();
                            DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [2, $this->download_id]);
                            DB::commit();
                            $this->result_status = 0;

                            return '';
                            //$response = Response::json(array('code' => 201, 'message' => "Audio is unable to trimming.", 'cause' => '', 'data' => json_decode("{}")));
                        }
                    } else {
                        $audio_path = $audio_original_path;
                    }

                    try {

                        if (file_get_contents($audio_path)) {

                            $file_data = pathinfo(basename($audio_path));
                            $audio_name = $file_data['filename'];

                            $text = '';
                            $audio_durationInSec = $this->audio_duration; // audio duration in sec
                            $trim_durationInSec = $this->trim_duration; // video duration in sec
                            $input_audio_dir = './..'.Config::get('constant.INPUT_AUDIO_DIRECTORY'); //Directory of temp audio store

                            //Video duration longer than audio duration
                            if ($audio_durationInSec <= $trim_durationInSec) {

                                for ($d = 0; $trim_durationInSec > ($audio_durationInSec * $d); $d++) {
                                    $text = $text."file '$audio_path'".PHP_EOL; //php end of line
                                }
                                $audioFile = $input_audio_dir.$audio_name.'_'.time().'.txt'; //create text file with audio list
                                file_put_contents($audioFile, $text);
                            } else {
                                $text = $text."file '$audio_path'";
                                $audioFile = $input_audio_dir.$audio_name.'_'.time().'.txt'; //create text file with audio list
                                file_put_contents($audioFile, $text);
                            }
                        }
                    } catch (Exception $e) {
                        Log::error('VideoTemplateJob.php : ', ['download_id' => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
                        $fail_reason = json_encode(['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage()]);
                        DB::beginTransaction();
                        DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
                        DB::commit();
                        (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, $this->design_id, null, 2, $failed);
                        $this->result_status = 0;

                        return '';
                    }
                    //" -f concat -safe 0 -i $this->audio_path -af apad -map 0:v -map 2:a"
                    $audio = " -protocol_whitelist file,http,https,tcp,tls,crypto -f concat -safe 0 -i $audioFile -map 0:v -map 2:a";
                }
            }
            //      Log::info("audio :", [$audio]);

            $ffmpeg = Config::get('constant.FFMPEG_PATH');
            if ($is_video_user_uploaded == 2) {
                $this->quality == Config::get('constant.NORMAL_VIDEO') ? $quality = '-crf 35 ' : $quality = '';
            } else {
                $this->quality == Config::get('constant.NORMAL_VIDEO') ? $quality = '-crf 40 ' : $quality = '';
            }
            //      Log::info("Request cycle Queues started");

            //==============Video trimming in filter_complex==============//
            /*
             * ffmpeg
             * -ss 0 -t 2.87 //Video trimming
             * -i INPUT_v //Background video
             * -i INPUT_img  //Transparent image
             * -f concat -safe 0 -i Audio_txt //Add audio on video
             * -map 0:v -map 2:a //mapping of video and audio
             * -filter_complex "[0:v]scale=trunc(1280/2)*2:trunc(720/2)*2[video]; //Set bg video H/W
             * [1:v]scale=trunc(1280/2)*2:trunc(720/2)*2[bg]; //Set transparent image H/W
             * [bg][video]overlay=0:0" //set transparent image on bg video
             * -map "[v]" -map 2:a -shortest -y -preset ultrafast
             * OUTPUT 2>&1
             *
             */

            //      $cmd = $ffmpeg . "" . $trim . " -i " . $input_video . " -i " . $transparent_img . $audio . " -filter_complex" .
            //        " \"[0:v]scale=trunc(" . $this->out_width . "/2)*2:trunc(" . $this->out_height . "/2)*2[video];" . ""
            //        . "[1:v]scale=trunc($this->out_width/2)*2:trunc($this->out_height/2)*2[bg];" . ""
            //        . "[video][bg]overlay=0:0\"" . $mute . " -vcodec mpeg4 -pix_fmt yuv420p -c:v libx264  -shortest -y -preset ultrafast  $quality " . $output_file . " 2>&1";

            //==============Video trimming in filter_complex==============//
            /*ffmpeg -i INPUT_v -i INPUT_img  -f concat -safe 0 -i Audio_txt
            -filter_complex "[0:v]scale=trunc(1280/2)*2:trunc(720/2)*2[video];
                [1:v]scale=trunc(1280/2)*2:trunc(720/2)*2[bg];
                [bg][video]overlay=0:0[bv];
                [1:v]scale=trunc(1280/2)*2:trunc(720/2)*2[wf];
                [bv][wf]overlay=0:0"
            -filter_complex "[0:v]trim=start=00.00:end=05.10,setpts=PTS-STARTPTS[v]"
             -map "[v]" -map 2:a -shortest -y -preset ultrafast
            OUTPUT 2>&1*/

            $cmd = $ffmpeg.''.$trim.' -i '.$input_video.' -i '.$transparent_img.$audio.' -filter_complex'.
              ' "[0:v]scale=trunc('.$this->out_width.'/2)*2:trunc('.$this->out_height.'/2)*2[video];'.''
              ."[1:v]scale=trunc($this->out_width/2)*2:trunc($this->out_height/2)*2[bg];".''
              .'[bg][video]overlay=0:0[bv];'.''
              ."[1:v]scale=trunc($this->out_width/2)*2:trunc($this->out_height/2)*2[wf];".''
              .'[bv][wf]overlay=0:0"'.$mute." -shortest -y -preset ultrafast $quality ".$output_file.' 2>&1';

            //      Log::info('Video cmd is : ',[$cmd]);
            $start = date('Y-m-d H:i:s');
            exec($cmd, $output, $result);
            $end = date('Y-m-d H:i:s');
            //      Log::info('Video cmd end : ');

            //      Log::info("Request cycle Queues finished");
            //      Log::info('output', [$output]);
            //      Log::info('result', [$result]);

            $datetime1 = new DateTime($start);
            $datetime2 = new DateTime($end);
            $interval = $datetime1->diff($datetime2);
            $cmd_execute_time = $interval->format('%H:%I:%S');
            //      Log::info('cmd_execute_time:',[$cmd_execute_time]);

            if (file_exists($output_file) && $result == 0) {
                $video_info = (new ImageController())->getVideoInformation($output_file);
                $size = $video_info['size'];
                DB::beginTransaction();
                DB::update('UPDATE video_template_jobs
                            SET status=?,
                             cmd_execute_time = ?,
                             '.$video_db.$audio_db.'
                             output_video=?,
                             download_video_size=?
                             WHERE download_id =?',
                    [$ready, $cmd_execute_time, $output_file_name, $size, $this->download_id]);
                DB::commit();
                (new userController())->addVideoGenerateHistory('success', $this->user_id, $this->get_download_id, $this->design_id, null, 2, 1);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveOutputVideoInToS3($output_file_name, 1);
                }

                $this->result_status = 1;

            } else {
                Log::error('VideoTemplateJob.php handle() : ', ['download_id' => $this->download_id]);
                Log::error('VideoTemplateJob.php handle()_output : ', [$output]);
                Log::error('VideoTemplateJob.php handle()_result : ', [$result]);
                Log::error('VideoTemplateJob.php handle()_cmd : ', [$cmd]);
                $fail_reason = json_encode(['download_id' => $this->download_id, 'output' => $output, 'result' => $result]);
                DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
                (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, $this->design_id, null, 2, $failed);
                $this->result_status = 0;
            }

            (new ImageController())->unlinkLocalStorageFileFromFilePath($transparent_img);
            unlink($local_input_video);

            if (! empty($this->audio_name)) {
                if (isset($audio_path_trim) && $audio_path_trim != '') {
                    $audio_file_path = './..'.Config::get('constant.INPUT_AUDIO_DIRECTORY').$audio_path_trim;
                    if (file_exists($audio_file_path)) {
                        unlink($audio_file_path);
                    }
                }
                if (isset($audioFile) && $audioFile != '') {
                    if (file_exists($audioFile)) {
                        unlink($audioFile);
                    }
                }
                //          $audio_path_for_unlink = $input_audio_dir;
                //          foreach (glob($audio_path_for_unlink . '*') as $filename) {
                //            Log::info('all file name :',[$filename]);
                //            if (file_exists($audioFile)) {
                //              unlink($filename);
                //            }
                //          }
            }

        } catch (Exception $e) {
            (new ImageController())->logs('VideoTemplateJob.php handle catch()', $e);
            Log::error('VideoTemplateJob.php handle catch() : ', ['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage(), "\ngetTraceAsString" => $e->getTraceAsString()]);
            $fail_reason = json_encode(['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage()]);
            DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
            (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, $this->design_id, null, 2, $failed);
            $this->result_status = 0;
        }
    }

    public function failed(Exception $e)
    {
        $failed = 2;
        Log::error('VideoTemplateJob.php failed() : ', ['download_id' => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
        $fail_reason = json_encode(['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage()]);
        //    DB::beginTransaction();
        //    DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
        //    DB::commit();
        //    (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, $this->design_id, NULL, 2, $failed);
        //    Log::info('set status=2 failed()');
        $this->result_status = 0;
    }

    public function getResponse()
    {
        return [
            'download_id' => $this->download_id,
            'result_status' => $this->result_status,
        ];
    }
}
