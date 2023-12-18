<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use Config;
use DateTime;
use Exception;
use File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Log;

class IntrosVideoTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**Intro maker  */
    protected $text_json; //text json

    protected $logo_json; //image data

    protected $audio_json; // audio data

    protected $video_json; //video data

    protected $video_width;

    protected $video_height;

    protected $user_quality; //user's selected quality

    protected $quality; // Quality of out_video  1 =normal, 2=SD, 3=HD, 4=FHD;

    protected $is_image_user_uploaded; // 0=collection,1= user upload,2=resource,3=pixabay

    protected $is_audio_user_uploaded; // 0=collection,1= user upload

    protected $output_file; //Output video file

    protected $is_audio_trim; // For audio trim

    protected $is_audio_repeat; //Is audio repeat

    protected $audio_start_time; // Audio's trimming start time

    protected $audio_end_time; // Audio's trimming end time

    protected $audio_duration; // Audio's trimming duration time

    protected $download_id; // Download_id for download output video

    protected $status; // Video generate status

    protected $result_status; // All process status : 1=Success, 0=Fail

    protected $video_name; //Video name for background

    protected $audio_name; // Audio file name for set audio

    protected $img_size; //Transparent image size in bytes

    protected $sample_video_name;

    protected $is_single_image; //1=single image,0=Multiple images

    protected $role; // user role

    protected $design_id; //user_design_id

    protected $content_type; //content_type

    protected $content_id; //content_id

    protected $user_id; //

    protected $get_download_id; //

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request_body)
    {
        try {
            //             Log::info('----------------------------------------------------------------------------');
            //             Log::info('__construct()');
            $request = json_decode($request_body);
            $json_data = $request->json_data;
            $this->video_height = $json_data->video_height;
            $this->sample_video_name = $json_data->sample_video_url;
            $this->video_width = $json_data->video_width;
            $this->text_json = $json_data->text_json;
            $this->logo_json = isset($json_data->logo_json_list) ? $json_data->logo_json_list : [];
            $this->audio_json = isset($json_data->audio_json) ? $json_data->audio_json : [];
            $this->video_json = $json_data->video_json;
            $this->user_id = $request->user_id;
            $this->is_audio_repeat = isset($request->download_json->is_audio_repeat) ? $request->download_json->is_audio_repeat : '';
            $this->is_audio_user_uploaded = isset($request->download_json->is_audio_user_uploaded) ? $request->download_json->is_audio_user_uploaded : 1;
            $this->audio_duration = isset($request->download_json->audio_duration) ? $request->download_json->audio_duration : ''; //Audio's duration
            $this->is_audio_trim = isset($request->download_json->is_audio_trim) ? $request->download_json->is_audio_trim : ''; // is_audio_trim

            $this->design_id = isset($request->get_my_design_id) ? $request->get_my_design_id : null;
            $this->content_type = isset($request->content_type) ? $request->content_type : null;
            $this->content_id = isset($request->get_content_id) ? $request->get_content_id : null;

            if (count($this->logo_json) > 1) {
                $this->is_single_image = 0;
            } else {
                $this->is_single_image = 1;
            }
            $this->user_quality = isset($request->download_json->quality) ? $request->download_json->quality : 1;
            $this->quality = $this->user_quality;
            /* set video quality according to user role */
            $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$this->user_id]);
            if (count($get_user_role) > 0) {
                $this->role = $get_user_role[0]->role_id;
            } else {
                $this->role = Config::get('constant.ROLE_ID_FOR_FREE_USER');
            }

            if ($this->quality == Config::get('constant.FULL_HD_VIDEO')) {
                if ($this->role == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                    $this->quality = Config::get('constant.NORMAL_VIDEO');
                }
            }

            $create_at = date('Y-m-d H:i:s');
            $this->status = 0;
            $this->download_id = base64_encode($this->sample_video_name.uniqid());

            $video_template_jobs = [
                'download_id' => $this->download_id,
                'user_id' => $this->user_id,
                'request_body' => $request_body,
                'bg_file' => $this->video_name,
                'bg_file_type' => 1, //Bg file type 1 = video
                'content_type' => $this->content_type,
                'my_design_id' => $this->design_id,
                'status' => $this->status,
                'quality' => $this->quality,
                'create_time' => $create_at,
            ];

            $this->get_download_id = DB::table('video_template_jobs')->insertGetId($video_template_jobs);
            DB::update('UPDATE video_generate_history_master SET download_id=? WHERE download_id IS NULL AND content_id=? AND user_id=?', [$this->get_download_id, $this->content_id, $this->user_id]);
            (new UserController())->addVideoGenerateHistory('queue', $this->user_id, $this->get_download_id, null, $this->content_id, 3, $this->status);

            /*DB::beginTransaction();
            DB::insert('INSERT INTO video_template_jobs(download_id,user_id,request_body, bg_file, bg_file_type, content_type, my_design_id, status,quality,create_time)
                          VALUES(?,?,?,?,?,?,?,?,?,?)', [
              $this->download_id,
              $this->user_id,
              $request_body,
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
            Log::error('IntrosVideoTemplateJob.php construct() : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
            $this->result_status = 0;
            $fail_reason = json_encode(['Exception' => $e->getMessage()]);
            DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [2, $this->download_id]);
            (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, null, $this->content_id, 3, 2);
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
            $ffmpeg = Config::get('constant.FFMPEG_PATH');
            $shortest = '';
            /** Audio */
            $this->audio_start_time = isset($this->audio_json[0]->audio_start_pos) ? $this->audio_json[0]->audio_start_pos : ''; //Audio's end time
            $this->audio_end_time = isset($this->audio_json[0]->audio_end_pos) ? $this->audio_json[0]->audio_end_pos : ''; //Audio's start time
            $audio_name = isset($this->audio_json[0]->audio_name) ? $this->audio_json[0]->audio_name : '';

            /** Video  */
            $input_video_duration = $this->video_json->video_duration;
            $input_video_height = $this->video_json->video_height;
            $input_video_width = $this->video_json->video_width;
            $input_video_url = $this->video_json->input_video_url;
            $txt_file_array = [];

            $duration_time_formate = $this->formatMilliseconds($input_video_duration);

            $output_file_name = uniqid().'_'.'outputvideo'.'_'.time().'.mp4';
            $output_file = './..'.Config::get('constant.TEMP_DIRECTORY').$output_file_name;

            $video_db = null;
            $audio_db = null;
            $video_id = null;

            //      if($this->user_quality == Config::get('constant.NORMAL_VIDEO')){
            //        $input_video = Config::get('constant.THUMBNAIL_VIDEO_DIRECTORY_OF_S3') . $input_video_url;
            //      }else {
            $input_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_S3').$input_video_url;
            //      }

            //      if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
            $input_video = '"'.$input_video.'"';
            //      }

            $get_video_id = DB::select('SELECT id FROM video_details WHERE file_name = ?', [$input_video_url]);

            if (count($get_video_id) > 0) {
                $video_id = $get_video_id[0]->id;
            }
            /* Add video detail in db **/
            $video_id = isset($video_id) ? "video_file_id = $video_id ," : '';
            //Here we set 0 because video from collection
            $video_db = "is_video_user_uploaded = 0 , $video_id";

            //Filter of audio trim
            if (empty($audio_name)) {
                $audio = '';
            } else {
                $audio = '';
                $audioFile = '';
                $audio_id = null;
                //Get audio detail by audio type
                if ($this->is_audio_user_uploaded == 1) {
                    $audio_original_path = Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY_OF_S3').$audio_name;
                    $audio_path_trim = 'trim'.$audio_name;
                    $get_aud_id = DB::select('SELECT id FROM user_uploaded_audio WHERE file_name = ?', [$audio_name]);
                    if (count($get_aud_id) > 0) {
                        $audio_id = $get_aud_id[0]->id;
                    }
                } else {
                    $audio_original_path = Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_S3').$audio_name;
                    $audio_path_trim = 'trim'.$audio_name;
                    $get_aud_id = DB::select('SELECT id FROM audio_master WHERE file_name = ?', [$audio_name]);
                    if (count($get_aud_id) > 0) {
                        $audio_id = $get_aud_id[0]->id;
                    }
                }

                //Add audio detail in db
                $audio_id = isset($audio_id) ? "audio_file_id = $audio_id ," : '';
                $audio_db = "is_audio_user_uploaded = $this->is_audio_user_uploaded ,$audio_id ";
                if ($this->is_audio_trim == 1) {
                    $audio_path = (new ImageController())->trimAudioByJob($audio_original_path, $audio_path_trim, $this->audio_start_time, $this->audio_duration, $this->get_download_id);
                    if ($audio_path == '') {
                        Log::error('IntroVideosTemplateJob.php trimAudioByJob() : ', ['download_id' => $this->download_id]);
                        DB::beginTransaction();
                        DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [2, $this->download_id]);
                        DB::commit();
                        $this->result_status = 0;

                        return '';
                        $response = Response::json(['code' => 201, 'message' => 'Audio is unable to trimming.', 'cause' => '', 'data' => json_decode('{}')]);
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
                        $video_durationInSec = $input_video_duration / 1000; // video duration in sec
                        $input_audio_dir = './..'.Config::get('constant.INPUT_AUDIO_DIRECTORY'); //Directory of temp audio store

                        //Video duration longer than audio duration
                        if ($audio_durationInSec <= $video_durationInSec && $this->is_audio_repeat == 1) {
                            for ($d = 0; $video_durationInSec > ($audio_durationInSec * $d); $d++) {
                                $text = $text."file '$audio_path'".PHP_EOL; //php end of line
                            }
                            $audioFile = $input_audio_dir.$audio_name.'_'.time().'.txt'; //create text file with audio list
                            file_put_contents($audioFile, $text);
                        } else {
                            $text = $text."file '$audio_path'";
                            $audioFile = $input_audio_dir.$audio_name.'_'.time().'.txt'; //create text file with audio list
                            file_put_contents($audioFile, $text);
                        }

                        if ($audio_durationInSec >= $video_durationInSec || $this->is_audio_repeat == 1) {
                            $shortest = '-shortest';
                        }
                    }
                } catch (Exception $e) {
                    Log::error('IntrosVideoTemplateJob.php : ', ['download_id' => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
                    $fail_reason = json_encode(['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage()]);
                    DB::beginTransaction();
                    DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
                    DB::commit();
                    (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, null, $this->content_id, 3, $failed);
                    $this->result_status = 0;

                    return '';
                }
                $audio = " -protocol_whitelist file,http,https,tcp,tls,crypto -f concat -safe 0 -t $duration_time_formate -i $audioFile";
            }

            if ($this->quality == Config::get('constant.FULL_HD_VIDEO')) {
                $quality = '4M';
                $out_width = 1920;
            } else {
                $quality = '500k';
                $out_width = 640;
            }

            /**  Output video height and width */
            if ($this->video_width == $this->video_height) {
                $out_height = $out_width;
            } elseif ($this->video_width > $this->video_height) {
                $out_height = (int) (($this->video_height / $this->video_width) * $out_width);
                if ($out_height % 2 != 0) {
                    $out_height = (int) $out_height + 1;
                }
            } elseif ($this->video_height > $this->video_width) {
                $out_height = (int) (($this->video_width / $this->video_height) * $out_width);
                if ($out_height % 2 != 0) {
                    $out_height = (int) $out_height + 1;
                }
            } else {
                $out_height = $out_width;
            }

            $image_option = '';
            $input_image = '';

            if ($this->is_single_image) {
                /** Single Image  */
                $this->is_image_user_uploaded = isset($this->logo_json[0]->is_image_user_uploaded) ? $this->logo_json[0]->is_image_user_uploaded : 1;
                $is_image_user_uploaded = $this->is_image_user_uploaded;

                if (isset($this->logo_json[0]->company_logo) && ! empty($this->logo_json[0]->company_logo)) {

                    $image_name = $this->logo_json[0]->company_logo;
                    $is_cropped = isset($this->logo_json[0]->is_croped) ? $this->logo_json[0]->is_croped : 0;
                    $img_center_x = $this->logo_json[0]->img_center_x;
                    $img_center_y = $this->logo_json[0]->img_center_y;
                    $img_height = $this->logo_json[0]->img_height;
                    $img_width = $this->logo_json[0]->img_width;
                    $img_in_anim_1_name = $this->logo_json[0]->img_in_anim_1_name;
                    $img_in_anim_1_time = $this->logo_json[0]->img_in_anim_1_time;
                    $img_in_anim_2_name = $this->logo_json[0]->img_in_anim_2_name;
                    $img_in_anim_2_time = $this->logo_json[0]->img_in_anim_2_time;
                    $img_in_time = $this->logo_json[0]->img_in_time;
                    $img_out_anim_1_name = $this->logo_json[0]->img_out_anim_1_name;
                    $img_out_anim_1_time = $this->logo_json[0]->img_out_anim_1_time;
                    $img_out_time = $this->logo_json[0]->img_out_time;
                    $img_x = (int) $this->logo_json[0]->img_x;
                    $img_y = (int) $this->logo_json[0]->img_y;
                    $fade_out_time = $img_out_time - $img_out_anim_1_time;

                    /** original path of  image and output video path **/
                    if ($is_image_user_uploaded == 1) {
                        if ($is_cropped == 1) {
                            $input_image_path = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_S3').$image_name;
                            copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                            $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                            array_push($txt_file_array, $image_name);
                        } else {
                            $input_image_path = Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_S3').$image_name;
                            copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                            $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                            array_push($txt_file_array, $image_name);
                        }
                        $input_image = ' -loop 1 -i '.'"'.$input_image_path.'"';
                    } elseif ($is_image_user_uploaded == 2) {
                        $input_image_path = Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_S3').$image_name;
                        copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                        $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                        array_push($txt_file_array, $image_name);
                        $input_image = ' -loop 1 -i '.'"'.$input_image_path.'"';
                    } elseif ($is_image_user_uploaded == 3) {
                        $input_image = ' -loop 1 -i '.'"'.$image_name.'"';
                    } else {
                        $input_image_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_S3').$image_name;
                        copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                        $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                        array_push($txt_file_array, $image_name);
                        $input_image = ' -loop 1 -i '.'"'.$input_image_path.'"';
                    }

                    /** New Image height width for(LTR,RTL,TTB,BTT,Bling) */
                    //          if ($this->video_width == $this->video_height) {
                    //            $part = $this->video_width / 6;
                    //            $new_image_height = (int)($part * 2);
                    //            $new_image_width = (int)($part * 2);
                    //          } else if ($this->video_width > $this->video_height) {
                    //            if ($img_width > $img_height) {
                    //              $part = $this->video_width / 6;
                    //              $new_image_width = (int)($part * 2);
                    //              $new_image_height = (int)(($img_height / $img_width) * $new_image_width);
                    //            } else {
                    //              $part = $this->video_height / 6;
                    //              $new_image_height = (int)($part * 2);
                    //              $new_image_width = (int)(($img_width / $img_height) * $new_image_height);
                    //            }
                    //          } else if ($this->video_width < $this->video_height) {
                    //            if ($img_width > $img_height) {
                    //              $part = $this->video_width / 6;
                    //              $new_image_width = (int)($part * 2);
                    //              $new_image_height = (int)(($img_height / $img_width) * $new_image_width);
                    //            } else {
                    //              $part = $this->video_height / 6;
                    //              $new_image_height = (int)($part * 2);
                    //              $new_image_width = (int)(($img_width / $img_height) * $new_image_height);
                    //            }
                    //          } else {
                    //            $part = $this->video_width / 6;
                    //            $new_image_height = ($part * 2);
                    //            $new_image_width = ($part * 2);
                    //          }
                    $new_image_height = (int) $img_height;
                    $new_image_width = (int) $img_width;
                    /**set Image animation */
                    if ($img_in_anim_2_name == Config::get('constant.ANIM_ZOOM_IN')) {
                        /** Zoom In animation  */
                        $EndTime = $img_in_time + $img_in_anim_2_time;
                        //            if ($this->video_width == $this->video_height) {
                        //              $part = $this->video_width / 6;
                        //              $new_image_height =(int)  ($part * 3);
                        //              $new_image_width =(int)  ($part * 3);
                        //            } else if ($this->video_width > $this->video_height) {
                        //              $part = $this->video_height / 6;
                        //              $new_image_height =(int)  ($part * 3);
                        //              $new_image_width =(int)  (($img_width / $img_height) * $new_image_height);
                        //            } else if ($this->video_width < $this->video_height) {
                        //              $part = $this->video_width / 6;
                        //              $new_image_height =(int)  (($img_height / $img_width) * $new_image_width);
                        //              $new_image_width =(int)  ($part * 3);
                        //            } else {
                        //              $part = $this->video_width / 6;
                        //              $new_image_height = (int) ($part * 3);
                        //              $new_image_width =(int)  ($part * 3);
                        //            }

                        $zoom1 = $img_in_time * 25;
                        $zoom2 = ($img_in_time + $img_in_anim_2_time) * 25;
                        $zoom3 = $zoom2 - $zoom1;
                        $zoomValue = 1 / $zoom3;

                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,pad=7/2*iw:7/2*ih:(ow-iw)/2:(oh-ih)/2:0x00000000,zoompan=z='if(gt(in,($img_in_time*25)),if(lt(in,($EndTime*25)),min(pzoom+$zoomValue,2),2))':d=1:fps=25:x=iw/2-(iw/zoom/2):y=ih/2-(ih/zoom/2):s=$new_image_width".'x'."$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=x=$img_x:shortest=1:y=$img_y:enable='between(t,$img_in_time,$img_out_time)'";

                        /** Zoom Out */
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_ZOOM_OUT')) {

                        //            if ($this->video_width == $this->video_height) {
                        //              $part = $this->video_width / 6;
                        //              $new_image_height =(int)  ($part * 2.5);
                        //              $new_image_width =(int)  ($part * 2.5);
                        //            } elseif ($this->video_width > $this->video_height) {
                        //              $part = $this->video_height / 6;
                        //              $new_image_height =(int)  ($part * 2.5);
                        //              $new_image_width =(int)  (($img_width / $img_height) * $new_image_height);
                        //            } elseif ($this->video_width < $this->video_height) {
                        //              $part = $this->video_width / 6;
                        //              $new_image_height = (int) (($img_height / $img_width) * $new_image_width);
                        //              $new_image_width = (int) ($part * 2.5);
                        //            } else {
                        //              $part = $this->video_width / 6;
                        //              $new_image_height = (int) ($part * 2.5);
                        //              $new_image_width =(int)  ($part * 2.5);
                        //            }

                        $zoom1 = $img_in_time * 24;
                        $zoom2 = ($img_in_time + $img_in_anim_2_time) * 24;
                        $zoom3 = $zoom2 - $zoom1;
                        $zoomValue = 0.5 / $zoom3;

                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,pad=3/2*iw:3/2*ih:(ow-iw)/2:(oh-ih)/2:0x00000000,zoompan=z='if(gt(in,($img_in_time*24)),if(lt(in,(($img_in_time+$img_in_anim_2_time)*24)),1.5-((in-($img_in_time*24))*$zoomValue)))':d=0:x=iw/2-(iw/zoom/2):y=ih/2-(ih/zoom/2):fps=24:s=$new_image_width".'x'."$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=x=$img_x:shortest=1:y=$img_y:enable='between(t,$img_in_time,($img_out_time))'";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_RIGHT_TO_LEFT')) {
                        /** ANIM_RIGHT_TO_LEFT: **/
                        $totalAnimationTime = $img_in_time + $img_in_anim_2_time;
                        $right_to_left = 0;
                        if ($img_x > $this->video_width) {
                            $right_to_left = (int) ($img_x - $this->video_width);
                        } else {
                            $right_to_left = (int) ($this->video_width - $img_x);
                        }
                        $XString = "x='if(lt(t,$totalAnimationTime),main_w-(($right_to_left/$totalAnimationTime)*t),$img_x)'";

                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=$XString:shortest=1:y=$img_y:enable='between(t,$img_in_time,($img_out_time))'";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_LEFT_TO_RIGHT')) {
                        /** ANIM_LEFT_TO_RIGHT: **/
                        $totalAnimationTime = $img_in_time + $img_in_anim_2_time;
                        $XString = "x='if(lt(t,$totalAnimationTime),($img_x/$img_in_anim_2_time)*(t-$img_in_time),$img_x)'";

                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=$XString:shortest=1:y=$img_y:enable='between(t,$img_in_time,($img_out_time))'";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_TOP_TO_BOTTOM')) {
                        /** ANIM_TOP_TO_BOTTOM: **/
                        $totalAnimationTime = $img_in_time + $img_in_anim_2_time;

                        $YString = "y='if(lt(t,$totalAnimationTime),($img_y/$img_in_anim_2_time)*(t-$img_in_time),$img_y)'";
                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=x=$img_x:shortest=1:$YString:enable='between(t,$img_in_time,($img_out_time))'";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_BOTTOM_TO_TOP')) {
                        /** ANIM_BOTTOM_TO_TOP: **/
                        $totalAnimationTime = $img_in_time + $img_in_anim_2_time;
                        $BTT = 0;
                        if ($img_y > $this->video_height) {
                            $BTT = (int) ($img_y - $this->video_height);
                        } else {
                            $BTT = (int) ($this->video_height - $img_y);
                        }

                        $YString = "y='if(lt(t,$totalAnimationTime),main_h-(($BTT/$totalAnimationTime)*t),$img_y)'";
                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=x=$img_x:shortest=1:$YString:enable='between(t,$img_in_time,($img_out_time))'";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_BLINK')) {
                        /** ANIM_BLINK: **/
                        $totalAnimationTime = $img_in_time + $img_in_anim_2_time;
                        $enableString = ":enable='if(gt(t,$img_in_time),if(lt(t,$totalAnimationTime),lt(mod(t,0.5),(0.5/$img_in_anim_2_time)),0.5))'";
                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=x=$img_x:shortest=1:y=$img_y"."$enableString";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_NONE')) {
                        /** ANIM_NONE: **/
                        $enableString = ":enable='between(t,$img_in_time,$img_out_time)'";
                        $image_option = "[1]format=rgba,scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [over1];[0:v][over1]overlay=x=$img_x:shortest=1:y=$img_y"."$enableString";
                    }
                }

            } else {

                /** Multiple Images  */
                $index = 1;
                $XString = '';
                $YString = '';
                $enableString = '';
                $overlayCmd1 = '';
                //        $overlayCmd2 = "";
                foreach ($this->logo_json as $row) {

                    $image_name = $row->company_logo;
                    $is_cropped = isset($row->is_croped) ? $row->is_croped : 0;
                    $img_height = $row->img_height;
                    $img_width = $row->img_width;
                    $img_in_anim_1_time = $row->img_in_anim_1_time;
                    $img_in_anim_2_name = $row->img_in_anim_2_name;
                    $img_in_anim_2_time = $row->img_in_anim_2_time;
                    $img_in_time = $row->img_in_time;
                    $img_out_anim_1_time = $row->img_out_anim_1_time;
                    $img_out_time = $row->img_out_time;
                    $img_x = (int) $row->img_x;
                    $img_y = (int) $row->img_y;
                    $is_image_user_uploaded = $row->is_image_user_uploaded;
                    $fade_out_time = $img_out_time - $img_out_anim_1_time;
                    $new_image_height = (int) $img_height;
                    $new_image_width = (int) $img_width;
                    $over = 'over'.$index;

                    if ($is_image_user_uploaded == 1) {
                        if ($is_cropped == 1) {
                            $input_image_path = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_S3').$image_name;
                            copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                            $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                            array_push($txt_file_array, $image_name);
                        } else {
                            $input_image_path = Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_S3').$image_name;
                            copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                            $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                            array_push($txt_file_array, $image_name);
                        }
                        $input_image .= ' -loop 1 -i '.'"'.$input_image_path.'"';
                    } elseif ($is_image_user_uploaded == 2) {
                        $input_image_path = Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_S3').$image_name;
                        copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                        $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                        array_push($txt_file_array, $image_name);
                        $input_image .= ' -loop 1 -i '.'"'.$input_image_path.'"';
                    } elseif ($is_image_user_uploaded == 3) {
                        $input_image .= ' -loop 1 -i '.'"'.$image_name.'"';
                    } else {
                        $input_image_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_S3').$image_name;
                        copy($input_image_path, './..'.Config::get('constant.TEMP_DIRECTORY').$image_name);
                        $input_image_path = './..'.Config::get('constant.TEMP_DIRECTORY').$image_name;
                        array_push($txt_file_array, $image_name);
                        $input_image .= ' -loop 1 -i '.'"'.$input_image_path.'"';
                    }

                    /** New Image height width for(LTR,RTL,TTB,BTT,Bling) */
                    if ($img_in_anim_2_name == Config::get('constant.ANIM_RIGHT_TO_LEFT')) {

                        $totalAnimationTime1 = $img_in_time + $img_in_anim_2_time;
                        if ($img_x > $this->video_width) {
                            $RTL = (int) ($img_x - $this->video_width);
                        } else {
                            $RTL = (int) ($this->video_width - $img_x);
                        }

                        $XString = "x='if(lt(t,$totalAnimationTime1),main_w-(($RTL / $totalAnimationTime1)*t),$img_x)'";
                        $YString = 'y='.$img_y;
                        $enableString = ":enable='between(t,$img_in_time,($img_out_time))'";

                        $image_option .= "[$index]scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";

                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_LEFT_TO_RIGHT')) {
                        $totalAnimationTime1 = $img_in_time + $img_in_anim_2_time;
                        $XString = "x='if(lt(t,$totalAnimationTime1),($img_x / $img_in_anim_2_time)*(t-$img_in_time),$img_x)'";
                        $YString = 'y='.$img_y;
                        $enableString = ":enable='between(t,$img_in_time,($img_out_time))'";

                        $image_option .= "[$index]scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";

                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_TOP_TO_BOTTOM')) {
                        $totalAnimationTime1 = $img_in_time + $img_in_anim_2_time;
                        $XString = 'x='.$img_x;
                        $YString = "y='if(lt(t,$totalAnimationTime1),($img_y / $img_in_anim_2_time)*(t-$img_in_time),$img_y)'";
                        $enableString = ":enable='between(t,$img_in_time,($img_out_time))'";

                        $image_option .= "[$index]scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";

                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_BOTTOM_TO_TOP')) {
                        $totalAnimationTime1 = $img_in_time + $img_in_anim_2_time;
                        $BTT = 0;
                        if ($img_y > $this->video_height) {
                            $BTT = (int) ($img_y - $this->video_height);
                        } else {
                            $BTT = (int) ($this->video_height - $img_y);
                        }

                        $XString = 'x='.$img_x;
                        $YString = "y='if(lt(t,$totalAnimationTime1),main_h-(($BTT / $totalAnimationTime1)*t),$img_y)'";
                        $enableString = ":enable='between(t,$img_in_time,($img_out_time))'";

                        $image_option .= "[$index]scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";

                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_BLINK')) {
                        $XString = 'x='.$img_x;
                        $YString = 'y='.$img_y;

                        $totalAnimationTime1 = $img_in_time + $img_in_anim_2_time;
                        $enableString = ":enable='if(gt(t,$img_in_time),if(lt(t,$totalAnimationTime1),lt(mod(t,0.5),(0.5 / $img_in_anim_2_time)),0.5))'";

                        $image_option .= "[$index]scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_NONE')) {
                        $XString = 'x='.$img_x;
                        $YString = 'y='.$img_y;
                        $enableString = ":enable='between(t,$img_in_time,$img_out_time)'";
                        $image_option .= "[$index]scale=$new_image_width:$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_ZOOM_IN')) {

                        $XString = 'x='.$img_x;
                        $YString = 'y='.$img_y;
                        $enableString = ":enable='between(t,$img_in_time,($img_out_time))'";

                        $zoom1 = $img_in_time * 25;
                        $zoom2 = ($img_in_time + $img_in_anim_2_time) * 25;
                        $zoom3 = $zoom2 - $zoom1;
                        $zoomValue = 1 / $zoom3;

                        $EndTime = $img_in_time + $img_in_anim_2_time;

                        $image_option .= "[$index]format=rgba,pad=7/2*iw:7/2*ih:(ow-iw)/2:(oh-ih)/2:0x00000000,zoompan=z='if(gt(in,($img_in_time*25)),if(lt(in,($EndTime*25)),min(pzoom+$zoomValue,2),2))':d=1:fps=25:x=iw/2-(iw/zoom/2):y=ih/2-(ih/zoom/2):s=$new_image_width".'x'."$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";
                    } elseif ($img_in_anim_2_name == Config::get('constant.ANIM_ZOOM_OUT')) {

                        $XString = 'x='.$img_x;
                        $YString = 'y='.$img_y;
                        $enableString = ":enable='between(t,$img_in_time,($img_out_time))'";

                        $zoom_1 = $img_in_time * 24;
                        $zoom_2 = ($img_in_time + $img_in_anim_2_time) * 24;
                        $zoom_3 = $zoom_2 - $zoom_1;
                        $zoomValue = 0.5 / $zoom_3;

                        $image_option .= "[$index]format=rgba,pad=3/2*iw:3/2*ih:(ow-iw)/2:(oh-ih)/2:0x00000000,zoompan=z='if(gt(in,($img_in_time*24)),if(lt(in,(($img_in_time+$img_in_anim_2_time)*24)),1.5-((in-($img_in_time*24))*$zoomValue)))':d=0:x=iw/2-(iw/zoom/2):y=ih/2-(ih/zoom/2):fps=24:s=$new_image_width".'x'."$new_image_height,format=rgba,fade=t=in:st=$img_in_time:d=$img_in_anim_1_time,format=rgba,fade=t=out:st=$fade_out_time:d=$img_out_anim_1_time [$over];";
                    }
                    if ($index == 1) {
                        $overlayCmd1 = "overlay=$XString:shortest=1:".$YString.$enableString.'[base1];';
                    } else {
                        $indexPrefix = $index - 1;

                        if (count($this->logo_json) == $index) {
                            $overlayCmd1 .= "[base$indexPrefix][over$index]overlay=$XString:shortest=1:".$YString.$enableString;
                        } else {
                            $overlayCmd1 .= "[base$indexPrefix][over$index]overlay=$XString:shortest=1:".$YString.$enableString."[base$index];";
                        }
                    }

                    /* if ($index == 1) {
                       $overlayCmd1 = "overlay=$XString:shortest=1:" . $YString . $enableString . "[base1];";
                     } else {
                       $overlayCmd2 = "[base1][over2]overlay=$XString:shortest=1:" . $YString . $enableString;
                     }*/
                    $index++;
                }
            }

            /** Draw text  */
            $drawtext = '';
            $i = 1;
            foreach ($this->text_json as $row) {

                $font_file = $row->font_file;
                $font_name = str_replace('fonts/', '', $font_file);
                $tag_text = $row->tag_text;
                $text_alignment = $row->text_alignment;
                $text_bkg_color = $row->text_bkg_color;
                //        $text_border_color = $row->text_border_color;
                $text_center_x = $row->text_center_x;
                $text_color = $row->text_color;
                $text_in_anim_1_name = $row->text_in_anim_1_name;
                $text_in_anim_1_time = $row->text_in_anim_1_time;
                $text_in_anim_2_name = $row->text_in_anim_2_name;
                $text_in_anim_2_time = $row->text_in_anim_2_time;
                $text_in_time = $row->text_in_time;
                $text_line_spacing = $row->text_line_spacing;
                $text_out_anim_1_name = $row->text_out_anim_1_name;
                $text_out_anim_1_time = $row->text_out_anim_1_time;
                $text_out_time = $row->text_out_time;
                $text_shadow_color = $row->text_shadow_color;
                $text_shadow_x = $row->text_shadow_x;
                $text_shadow_y = $row->text_shadow_y;
                $text_size = $row->text_size;
                $text_width = $row->text_width;
                $text_x = $row->text_x;
                $text_y = $row->text_y;
                $user_text = $row->user_text;
                $is_font_user_uploaded = isset($row->is_font_user_uploaded) ? $row->is_font_user_uploaded : 0;

                /** Create text file */
                $temp_dir = './..'.Config::get('constant.TEMP_DIRECTORY'); //Directory of temp audio store
                $text_file_name = uniqid().'_'.time().'.txt';
                array_push($txt_file_array, $text_file_name);
                $txtFile = $temp_dir.$text_file_name; //create text file with text
                file_put_contents($txtFile, $user_text);

                $fontFile = './..'.Config::get('constant.FONT_FILE_DIRECTORY').$font_name;

                if (! file_exists($fontFile)) {
                    (new ImageController())->downloadFont($font_name, $is_font_user_uploaded);
                }
                $fontFile = './..'.Config::get('constant.FONT_FILE_DIRECTORY').$font_name;
                $strBGColor = '';
                $strShadow = '';
                $strLineSpacing = '';
                if ($text_bkg_color != '') {
                    $strBGColor = ":box=1:boxborderw=20:boxcolor=$text_bkg_color";
                }
                if ($text_shadow_color != '' && $text_shadow_x != '' && $text_shadow_y != '') {
                    $strShadow = ":shadowcolor=$text_shadow_color:shadowx=$text_shadow_x:shadowy=$text_shadow_y";
                }
                if ($text_line_spacing) {
                    $strLineSpacing = ":line_spacing=$text_line_spacing";
                }

                /**Default:**/
                $XString = ":x='($text_x+(($text_width/2)-(tw/2)))'";
                $YString = ":y=$text_y";

                /**Alpha */
                $alpha = ":alpha='if(lt(t,$text_in_time),0,if(lt(t,($text_in_time+$text_in_anim_1_time)),(t-$text_in_time)/$text_in_anim_1_time,if(lt(t,($text_out_time-$text_out_anim_1_time)),1,if(gte(t,($text_out_time-$text_out_anim_1_time)),(1-(t-($text_out_time-$text_out_anim_1_time))/$text_out_anim_1_time),0))))'";

                /** all animation except blink animation :    **/
                $enableString = ":enable='between(t,$text_in_time,$text_out_time)'";

                /** ANIM_RIGHT_TO_LEFT: **/
                if ($text_in_anim_2_name == Config::get('constant.ANIM_RIGHT_TO_LEFT')) {

                    $XTempString = "($text_x+(($text_width/2)-(tw/2)))";
                    $totalAnimationTime = $text_in_time + $text_in_anim_2_time;

                    $rtl = '';
                    if ($text_alignment == 1) {
                        $rtl = "abs(($text_x-w))";
                        $XTempString = $text_x;
                    } elseif ($text_alignment == 2) {
                        $rtl = "abs(($XTempString-w))";
                        $XTempString = "($text_x+(($text_width/2)-(tw/2)))";
                    } else {
                        $rtl = "abs(($XTempString-w))";
                        $XTempString = "($text_x+(($text_width/2)-(tw/2)))";
                    }
                    $XString = ":x='if(lt(t,$totalAnimationTime),w-(($rtl/$totalAnimationTime)*t),$XTempString)'";

                    /** ANIM_LEFT_TO_RIGHT: **/
                } elseif ($text_in_anim_2_name == Config::get('constant.ANIM_LEFT_TO_RIGHT')) {

                    $XTempStringLTR = '';
                    if ($text_alignment == 1) {
                        $XTempStringLTR = "$text_x";
                    } elseif ($text_alignment == 2) {
                        $XTempStringLTR = "($text_x+(($text_width/2)-(tw/2)))";
                    }
                    $totalAnimationTime = $text_in_time + $text_in_anim_2_time;
                    $XString = ":x='if(lt(t,$totalAnimationTime),($XTempStringLTR/$text_in_anim_2_time)*(t-$text_in_time),$XTempStringLTR)'";

                    /** ANIM_TOP_TO_BOTTOM: **/
                } elseif ($text_in_anim_2_name == Config::get('constant.ANIM_TOP_TO_BOTTOM')) {

                    $totalAnimationTime = $text_in_time + $text_in_anim_2_time;
                    $YString = ":y='if(lt(t,$totalAnimationTime),($text_y/$text_in_anim_2_time)*(t-$text_in_time),$text_y)'";

                    /** ANIM_BOTTOM_TO_TOP: **/
                } elseif ($text_in_anim_2_name == Config::get('constant.ANIM_BOTTOM_TO_TOP')) {

                    $totalAnimationTime = $text_in_time + $text_in_anim_2_time;
                    $BTT = 0;
                    if ($text_y > $this->video_height) {
                        $BTT = ($text_y - $this->video_height);
                    } else {
                        $BTT = ($this->video_height - $text_y);
                    }
                    $YString = ":y='if(lt(t,$totalAnimationTime),h-(($BTT/$totalAnimationTime)*t),$text_y)'";

                    /** ANIM_BLINK:**/
                } elseif ($text_in_anim_2_name == Config::get('constant.ANIM_BLINK')) {

                    $totalAnimationTime = $text_in_time + $text_in_anim_2_time;
                    $enableString = ":enable='if(gt(t,$text_in_time),if(lt(t,$totalAnimationTime),lt(mod(t,0.5),(0.5/$text_in_anim_2_time)),0.5))'";
                } elseif ($text_in_anim_2_name == Config::get('constant.ANIM_NONE')) {

                    $enableString = ":enable='between(t,$text_in_time,$text_out_time)'";
                }
                if ($i != 1) {
                    $drawtext .= ',';
                }
                $drawtext .= "drawtext=textfile=$txtFile:fontsize=$text_size:expansion=none$strBGColor"."$strShadow"."$strLineSpacing:fontfile=$fontFile:fontcolor=$text_color"."$alpha"."$XString"."$YString"."$enableString";

                $i++;
            }

            $input_watermark = '';
            $watermark = '';
            if ($this->is_single_image) {

                if ($drawtext != '' && $image_option != '') {
                    $drawtext = ','.$drawtext;
                }

                if ($this->quality == Config::get('constant.NORMAL_VIDEO') && $this->role == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                    $input_watermark = ' -i '.Config::get('constant.WATERMARK_LOGO');
                    if ($input_image != '') {
                        $watermark = "[base];[base]scale=trunc($out_width/2)*2:trunc($out_height/2)*2[bg];[2]scale=trunc($out_width/2)*2:trunc($out_height/2)*2[wf];[bg][wf]overlay=0:0[out]";
                    } else {
                        $watermark = "[base];[base]scale=trunc($out_width/2)*2:trunc($out_height/2)*2[bg];[1]scale=trunc($out_width/2)*2:trunc($out_height/2)*2[wf];[bg][wf]overlay=0:0[out]";
                    }
                } else {
                    $drawtext .= '[out]';
                }
                if ($audio == '') {
                    $map = ' -map "[out]" ';
                } else {
                    if ($input_image != '' && $this->quality == Config::get('constant.NORMAL_VIDEO') && $this->role == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                        $map = ' -map "[out]" -map 3:a';
                    } elseif ($input_image != '' && $this->quality == Config::get('constant.NORMAL_VIDEO') && $this->role != Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                        $map = ' -map "[out]" -map 2:a';
                    } elseif ($input_image == '' && $this->quality == Config::get('constant.NORMAL_VIDEO') && $this->role == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                        $map = ' -map "[out]" -map 2:a';
                    } elseif ($input_image == '' && $this->quality == Config::get('constant.NORMAL_VIDEO') && $this->role != Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                        $map = ' -map "[out]" -map 1:a';
                    } elseif ($input_image != '' && $this->quality == Config::get('constant.FULL_HD_VIDEO')) {
                        $map = ' -map "[out]" -map 2:a';
                    } elseif ($input_image == '' && $this->quality == Config::get('constant.FULL_HD_VIDEO')) {
                        $map = ' -map "[out]" -map 1:a';
                    }
                }

                /** Command for single image */
                $cmd = $ffmpeg.' -i '.$input_video.$input_image.$input_watermark.$audio.' -filter_complex '.
                  " \"$image_option"."$drawtext"."$watermark\" $map -r 25 -s $out_width".'x'."$out_height -strict experimental -b:v $quality -c:v libx264 -y -preset ultrafast -pix_fmt yuv420p -strict 2 $shortest -t $duration_time_formate ".$output_file.' 2>&1';

            } else {

                $logo_count = count($this->logo_json);
                $tag1 = '';
                if ($drawtext == '') {
                    $tag1 = '[0:v]';
                } else {
                    $tag1 = '[text]';

                    $drawtext = '[0:v]'.$drawtext.'[text];';
                }

                if ($this->quality == Config::get('constant.NORMAL_VIDEO') && $this->role == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                    $watermark_index = $logo_count + 1;
                    $input_watermark = ' -i '.Config::get('constant.WATERMARK_LOGO');
                    /* set watermark using overlay image */
                    $watermark = "[base];[base]scale=trunc($out_width/2)*2:trunc($out_height/2)*2[bg];[$watermark_index]scale=trunc($out_width/2)*2:trunc($out_height/2)*2[wf];[bg][wf]overlay=0:0[out]";
                    /* set watermark using  image */
                } else {
                    $overlayCmd1 .= '[out]';
                }
                if ($audio == '') {
                    $map = ' -map "[out]" ';
                } else {
                    if ($this->quality == Config::get('constant.NORMAL_VIDEO')) {
                        if ($this->role == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                            $audio_index = $logo_count + 2;
                        } else {
                            $audio_index = $logo_count + 1;
                        }
                        $map = " -map \"[out]\" -map $audio_index:a";
                    } elseif ($this->quality == Config::get('constant.FULL_HD_VIDEO')) {
                        $audio_index = $logo_count + 1;
                        $map = " -map \"[out]\" -map $audio_index:a";
                    }
                }
                /** Command for multiple image */
                $cmd = $ffmpeg.' -i '.$input_video.$input_image.$input_watermark.$audio.' -filter_complex '.
                  " \"$drawtext"."$image_option $tag1 [over1]$overlayCmd1 $watermark\" $map -r 25 -s $out_width".'x'."$out_height -strict experimental -b:v $quality -c:v libx264 -y -preset ultrafast -pix_fmt yuv420p -strict 2 $shortest -t $duration_time_formate ".$output_file.' 2>&1';
            }

            //      Log::info('Intro cmd is : ',[$cmd]);
            $start = date('Y-m-d H:i:s');
            exec('timeout 120s '.$cmd, $output, $result);
            $end = date('Y-m-d H:i:s');
            //      Log::info('Intro cmd end : ');

            $datetime1 = new DateTime($start);
            $datetime2 = new DateTime($end);
            $interval = $datetime1->diff($datetime2);
            $cmd_execute_time = $interval->format('%H:%I:%S');

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
                (new userController())->addVideoGenerateHistory('success', $this->user_id, $this->get_download_id, null, $this->content_id, 3, 1);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveOutputVideoInToS3($output_file_name, 1);
                }

                /** Delete audio list txt file */
                if (! empty($audio_name)) {
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
                }

                /** Delete text txt file */
                if (! empty($txt_file_array)) {
                    foreach ($txt_file_array as $row) {
                        $file_dir = './..'.Config::get('constant.TEMP_DIRECTORY').$row;
                        if (file_exists($file_dir)) {
                            unlink($file_dir);
                        }
                    }
                }

                $this->result_status = 1;
            } else {
                Log::error('IntrosVideoTemplateJob.php handle() : ', ['download_id' => $this->download_id]);
                Log::error('IntrosVideoTemplateJob.php handle()_output : ', [$output]);
                Log::error('IntrosVideoTemplateJob.php handle()_result : ', [$result]);
                Log::error('IntrosVideoTemplateJob.php handle()_cmd : ', [$cmd]);

                /** Delete audio list txt file */
                if (isset($audio_name) && ! empty($audio_name)) {
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
                }

                /** Delete text txt file */
                if (isset($txt_file_array) && ! empty($txt_file_array)) {
                    foreach ($txt_file_array as $row) {
                        $file_dir = './..'.Config::get('constant.TEMP_DIRECTORY').$row;
                        if (file_exists($file_dir)) {
                            unlink($file_dir);
                        }
                    }
                }

                if ($result == 124) {
                    $output = 'Command terminated after 90 seconds.';
                }

                $fail_reason = json_encode(['download_id' => $this->download_id, 'output' => $output, 'result' => $result]);
                DB::beginTransaction();
                DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
                DB::commit();
                (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, null, $this->content_id, 3, $failed);
                $this->result_status = 0;
            }
        } catch (\Exception $e) {
            (new ImageController())->logs('IntrosVideoTemplateJob.php handle catch()', $e);
            //      Log::error("IntrosVideoTemplateJob.php handle catch() : ", ["download_id" => $this->download_id, "\nerror_msg" => $e->getMessage(), "\ngetTraceAsString" => $e->getTraceAsString()]);
            $fail_reason = json_encode(['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage()]);
            DB::beginTransaction();
            DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
            DB::commit();
            (new userController())->addVideoGenerateHistory($fail_reason, $this->user_id, $this->get_download_id, null, $this->content_id, 3, $failed);
            $this->result_status = 0;
            /** Delete audio list txt file */
            if (isset($audio_name) && ! empty($audio_name)) {
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
            }

            /** Delete text txt file */
            if (isset($txt_file_array) && ! empty($txt_file_array)) {
                foreach ($txt_file_array as $row) {
                    $file_dir = './..'.Config::get('constant.TEMP_DIRECTORY').$row;
                    if (file_exists($file_dir)) {
                        unlink($file_dir);
                    }
                }
            }
        }
    }

    public function failed(Exception $e)
    {
        $failed = 2;
        Log::error('IntrosVideoTemplateJob.php failed() : ', ['download_id' => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
        $fail_reason = json_encode(['download_id' => $this->download_id, "\nerror_msg" => $e->getMessage()]);
        DB::beginTransaction();
        DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
        DB::commit();
        (new UserController())->addVideoGenerateHistory($e->getMessage(), $this->user_id, $this->get_download_id, null, $this->content_id, 3, null);
        //        Log::info('set status=2 failed()');
        $this->result_status = 0;
    }

    public function getResponse()
    {
        return ['download_id' => $this->download_id,
            'result_status' => $this->result_status,
        ];
    }

    public function formatMilliseconds($milliseconds)
    {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $milliseconds = $milliseconds % 1000;
        $seconds = $seconds % 60;
        $minutes = $minutes % 60;

        $format = '%u:%02u:%02u.%03u';
        $time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);

        return rtrim($time, '0');
    }
}
