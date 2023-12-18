<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Response;
use JWTAuth;
use Exception;
use Cache;
use Log;
use Artisan;
use Config;
use App\Jobs\IntrosVideoTemplateJob;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;


class IntrosUserController extends Controller
{
  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/generateIntrosVideo",
   *        tags={"Users_video_module"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="generateIntrosVideo",
   *        summary="Generate video",
   *        produces={"application/json"},
   * 		@SWG\Parameter(
   *        in="header",
   *        name="Authorization",
   *        description="access token",
   *        required=true,
   *        type="string",
   *      ),
   * 		@SWG\Parameter(
   *          in="formData",
   *          name="request_data",
   *          required=true,
   *          type="string",
   *          description="Give json_data,quality,is_audio_user_uploaded,is_image_user_uploaded,is_audio_trim,audio_duration",
   *         @SWG\Schema(
   *              required={"json_data","quality","is_audio_user_uploaded","is_image_user_uploaded","is_audio_trim","audio_duration"},
   *              @SWG\Property(property="json_data",type="object", example={}, description="{}"),
      *           @SWG\Property(property="quality",type="integer", example=1, description="1 = normal, 2 = SD,3=HD,4=Full hd"),
   *              @SWG\Property(property="is_audio_trim",type="integer", example=0, description="1=free, 0=paid"),
   *              @SWG\Property(property="is_audio_user_uploaded",type="integer", example=0, description="0=collection,1=user upload"),
   *              @SWG\Property(property="is_image_user_uploaded",type="integer", example=0, description="0=collection,1=user upload,2=resource,4=pixabay"),
   *              @SWG\Property(property="audio_duration",type="integer", example="0.01", description="1=free, 0=paid"),
   *              ),
   *      ),
   *     @SWG\Response(
   *            response=200,
   *            description="Success",
   *      @SWG\Schema(
   *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video is ready to download.","cause":"","data":{ "status": 1,"output_video": "http://192.168.0.116/photoadking_testing/image_bucket/temp/5c57bf211ece8_video_file_1549254433.mp4","est_time_sec":""}}, description="0=Queue,1=ready,2=failed"),
   *      ),
   *    ),
   * 		@SWG\Response(
   *            response=201,
   *            description="error",
   *        ),
   *    )
   *
   */
  /**
   * @api {post} generateIntrosVideo   generateIntrosVideo
   * @apiName generateIntrosVideo
   * @apiGroup FFmpeg
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * request_data:{
   * json_data:{},//compulsory
   *"is_audio_user_uploaded": 1,// 0=collection,1 = user upload
   *"is_image_user_uploaded":1,// 0=collection,1= user upload,2=resource,4=pixabay
   * "quality": 1, //compulsory,  1 =normal, 2=SD, 3=HD, 4=FHD
   * "is_audio_trim": 0, // 1=audio trim
   * "audio_duration": "0.01", //if audio_name, compulsory
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Video generate successfully.",
   * "cause": "",
   * "data": {
   * "download_id": "NWM1N2JlM2YwZDQ0OV92aWRlb19maWxlXzE1NDkyNTQyMDcubXA0"
   * }
   * }
   */
//  public function generateIntrosVideo(Request $request_body)
//  {
//    try {
//
//      $token = JWTAuth::getToken();
//      JWTAuth::toUser($token);
//      $user_id = JWTAuth::toUser($token)->id;
//
//      if (!$request_body->has('request_data'))
//        return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
//
//      $request = json_decode($request_body->input('request_data'));
//      if (($response = (new VerificationController())->validateRequiredParameter(array('json_data','download_json'), $request)) != '')
//        return $response;
//
//      $download_json = $request->download_json;
//      $json_data = $request->json_data;
//      $audio_array = $json_data->audio_json;
//
//      if (($response = (new VerificationController())->validateRequiredParameter(array('quality'), $download_json)) != '')
//        return $response;
//
//      if(count($audio_array) > 0){
//        if (($response = (new VerificationController())->validateRequiredParameter(array('is_audio_user_uploaded','is_audio_repeat','is_audio_trim','audio_duration'), $download_json)) != '')
//          return $response;
//      }
//
//      $quality = $request->download_json->quality;
//
//      if ($quality != Config::get('constant.NORMAL_VIDEO')) {
//        if (($response = (new VerificationController())->checkIsUserPro($user_id)) != '')
//          return $response;
//      }
//
//      $request->user_id = $user_id;
//      $json_request_data = json_encode($request);
//
//      // Check video generate limit
//      $queue_record = DB::select('SELECT count(*) total
//                    FROM video_template_jobs
//                    WHERE status = 0 AND user_id = ?', [$user_id]);
//      $queue_limit = Config::get('constant.QUEUE_VIDEO_LIMIT');
//
//      if (count($queue_record) > 0) {
//        if($queue_record[0]->total >= $queue_limit) {
//          //generate random string of user id
//
//          /*
//           * Identify user id from random string
//           *
//           * after first dot(.)(position:19) leave 1 character(position:20) after that (position 21) is start index of user_id
//           *
//           */
//
//          $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%&?';
//
//          // Shuffle the $str and returns substring
//          // of specified length
//          $first_string = substr(str_shuffle($str),0, 18);
//          $second_string = substr(str_shuffle($str),0, 1);
//          $third_string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%&?'),0, 5);
//          $str_len = 50 - strlen($first_string.'.'.$second_string.$user_id.$third_string.'.');
//          $four_string = substr(str_shuffle($str),0, $str_len);
//          $random_string = $first_string.'.'.$second_string.$user_id.$third_string.'.'.$four_string;
//
//          Log::info($random_string);
//          return Response::json(array('code' => 201, 'message' => 'You can\'t add more than ' . $queue_limit . ' videos for download at a time. Please try after some time.', 'cause' => '', 'data' => json_decode("{}")));
//        }
//      }
//
//      //Send all data for generate video
//      $job = new IntrosVideoTemplateJob($json_request_data);
//      $data = $this->dispatch($job);
//      $result = $job->getResponse();
//      if ($result['result_status'] == 0) {
//        $response = Response::json(array('code' => 201, 'message' => 'Video is unable to generate video', 'cause' => '', 'data' => json_decode("{}")));
//      } else {
//        $download_id = $result['download_id'];
//        $response = Response::json(array('code' => 200, 'message' => 'Video generated successfully', 'cause' => '', 'data' => ['download_id' => $download_id]));
//      }
//
//    } catch (Exception $e) {
//      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'generate video.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
//      Log::error("generateIntrosVideo : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
//      DB::rollBack();
//    }
//    return $response;
//  }
  public function generateIntrosVideo(Request $request_body)
  {
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $user_id = JWTAuth::toUser($token)->id;

      $request = json_decode($request_body->input('request_data'));

      $content_id = NULL;
      if(isset($request->content_id)){
        $content_id = DB::select('SELECT id FROM content_master WHERE uuid=?',[$request->content_id]);
        if(count($content_id) > 0){
          $content_id =  $content_id[0]->id;
        }
      }elseif(isset($request->my_design_id) && $request->my_design_id){
        $content_id = DB::select('SELECT content_id FROM my_design_master WHERE uuid=?',[$request->my_design_id]);

        if(count($content_id) > 0){
          $content_id =  $content_id[0]->content_id;
        }
      }

      if (!$request_body->has('request_data')) {
        (new UserController())->addVideoGenerateHistory('Required field request_data is missing or empty.',$user_id,NULL,NULL,$content_id,3,NULL);
        return Response::json(array('code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode("{}")));
      }

      if (!$request_body->hasFile('file')) {
        (new UserController())->addVideoGenerateHistory('Required field file is missing or empty.',$user_id,NULL,NULL,$content_id,3,NULL);
        return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
      }

      if (($response = (new VerificationController())->validateRequiredParameter(array('json_data', 'download_json', 'my_design_id', 'sub_category_id'), $request)) != '') {
        (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
        return $response;
      }

      $my_design_id = $request->my_design_id;
      $sub_category_id = $request->sub_category_id;
      $content_type = isset($request->content_type) ? $request->content_type : 1;
      $download_json = $request->download_json;
      $json_data = $request->json_data;
      $audio_array = $json_data->audio_json;
      $video_name = NULL;
      $is_video_user_uploaded = NULL;
      $overlay_image = NULL;

      /** For generate  */
      if (($response = (new VerificationController())->validateRequiredParameter(array('quality'), $download_json)) != '') {
        (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
        return $response;
      }

      if (count($audio_array) > 0) {
        if (($response = (new VerificationController())->validateRequiredParameter(array('is_audio_user_uploaded', 'is_audio_repeat', 'is_audio_trim', 'audio_duration'), $download_json)) != '') {
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }
      }

      $quality = $request->download_json->quality;

      if ($quality != Config::get('constant.NORMAL_VIDEO')) {
        if (($response = (new VerificationController())->checkIsUserPro($user_id)) != '') {
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }
      }


      if ($my_design_id) {
        if (($response = (new VerificationController())->validateRequiredParameter(array('sample_image', 'user_template_name'), $request)) != ''){
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }

        $user_template_name = $request->user_template_name;
      } else {
        if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != ''){
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }

        $user_template_name = isset($request->user_template_name) ? $request->user_template_name : 'Untitled Design';
      }

      if (strlen($user_template_name) > 100) {
        (new UserController())->addVideoGenerateHistory('The length of your design-name is too long.',$user_id,NULL,NULL,$content_id,3,NULL);
        return Response::json(array('code' => 201, 'message' => 'The length of your design-name is too long.', 'cause' => '', 'data' => json_decode("{}")));
      }


      /** store sample image  */
      $image_array = Input::file('file');
      if (($response = (new UserVerificationController())->verifyImage($image_array)) != ''){
        (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
        return $response;
      }

      $color_value = (new ImageController())->getRandomColor($image_array);

      $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
      (new ImageController())->saveMyDesign($card_image);

      if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
        (new ImageController())->saveMyDesignInToS3($card_image);
      }

      /** Store ceop image  */
      $json_data = $request->json_data;
      $logo_json_list = isset($json_data->logo_json_list) ? $json_data->logo_json_list : array();
      if ($request_body->hasFile('crop_image1')) {

        $is_image_user_uploaded = $logo_json_list[0]->is_image_user_uploaded;
        $crop_image_array = Input::file('crop_image1');
        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }

        $crop_image_name = $crop_image_array->getClientOriginalName();
        (new UserController())->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image1');
      }

      if ($request_body->hasFile('crop_image2')) {

        $is_image_user_uploaded = $logo_json_list[1]->is_image_user_uploaded;
        $crop_image_array = Input::file('crop_image2');
        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != ''){
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }

        $crop_image_name = $crop_image_array->getClientOriginalName();
        (new UserController())->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image2');
      }

      if ($request_body->hasFile('crop_image3')) {

        $is_image_user_uploaded = $logo_json_list[2]->is_image_user_uploaded;
        $crop_image_array = Input::file('crop_image3');
        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
          (new UserController())->addVideoGenerateHistory(json_decode(json_encode($response))->original->message,$user_id,NULL,NULL,$content_id,3,NULL);
          return $response;
        }

        $crop_image_name = $crop_image_array->getClientOriginalName();
        (new UserController())->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image3');
      }

      if(is_numeric($sub_category_id)){
        $sub_category_detail = DB::select('SELECT
                                             uuid
                                           FROM
                                             sub_category_master
                                           WHERE
                                            id =?',[$sub_category_id]);
        $sub_category_id = $sub_category_detail[0]->uuid;
      }

      $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?',[$sub_category_id]);
      if(count($sub_category_detail) <=0){
        (new UserController())->addVideoGenerateHistory('Sub category does not exist.',$user_id,NULL,NULL,$content_id,3,NULL);
        return Response::json(array('code' => 201, 'message' => 'Sub category does not exist.', 'cause' => '', 'data' => json_decode("{}")));
      }
      $sub_category_id = $sub_category_detail[0]->id;

      /** Update  */
      if ($my_design_id) {

        $json_data = json_encode($request->json_data);
        $download_json = json_encode($request->download_json);
        $sample_image = $request->sample_image;
        $deleted_crop_image = isset($request->deleted_crop_image) ? $request->deleted_crop_image : array();

        if (count($deleted_crop_image) > 0) {
          (new UserController())->removeUnUsedCropImages($deleted_crop_image);
        }

        DB::beginTransaction();
        $get_my_design_id = DB::select('SELECT id FROM my_design_master WHERE uuid=?',[$my_design_id]);
        if(count($get_my_design_id) > 0){
          $get_my_design_id =  $get_my_design_id[0]->id;
        }
        DB::update('UPDATE
                                my_design_master SET
                                sub_category_id = ?,
                                user_template_name = ?,
                                json_data = ?,
                                image = ?,
                                content_type=?,
                                video_name=IF(? !="",?,video_name),
                                download_json=IF(? !="",?,download_json),
                                is_video_user_uploaded=IF(? !="",?,is_video_user_uploaded),
                                color_value = ? WHERE uuid = ? AND user_id = ?', [$sub_category_id, $user_template_name, $json_data, $card_image, $content_type, $video_name, $video_name, $download_json, $download_json, $is_video_user_uploaded, $is_video_user_uploaded, $color_value, $my_design_id, $user_id]);

        DB::delete('DELETE FROM image_details WHERE name = ? ', [$sample_image]);
        DB::commit();

        //delete unused sample images
        (new ImageController())->deleteMyDesign($sample_image);
      } else {
        /** save  */
        $json_data = json_encode($request->json_data);
        $download_json = json_encode($request->download_json);
        $create_time = date('Y-m-d H:i:s');

        $uuid = (new ImageController())->generateUUID();
        if($uuid == ""){
          (new UserController())->addVideoGenerateHistory('Something went wrong.Please try again.',$user_id,NULL,NULL,$content_id,3,NULL);
          return Response::json(array('code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode("{}")));
        }

        DB::beginTransaction();

        $data = array(
          'user_id' => $user_id,
          'uuid'=>$uuid,
          'sub_category_id' => $sub_category_id,
          'json_data' => $json_data,
          'download_json' => $download_json,
          'image' => $card_image,
          'overlay_image' => $overlay_image,
          'video_name' => $video_name,
          'is_video_user_uploaded' => $is_video_user_uploaded,
          'color_value' => $color_value,
          'content_type' => $content_type,
          'is_active' => 1,
          'create_time' => $create_time,
          'user_template_name' => $user_template_name,
          'content_id' => $content_id
        );

        $get_my_design_id = DB::table('my_design_master')->insertGetId($data);
        $my_design_id = $uuid;

        DB::commit();

        (new UserController())->increaseMyDesignCount($user_id, $create_time, $content_type);
      }

      $request->user_id = $user_id;
      $request->get_my_design_id = $get_my_design_id;
      $request->get_content_id = $content_id;
      $json_request_data = json_encode($request);

      // Check video generate limit
      $queue_record = DB::select('SELECT count(*) total
                    FROM video_template_jobs
                    WHERE status = 0 AND user_id = ?', [$user_id]);
      $queue_limit = Config::get('constant.QUEUE_VIDEO_LIMIT');

      if (count($queue_record) > 0) {
        if ($queue_record[0]->total >= $queue_limit) {
          //generate random string of user id

          /*
           * Identify user id from random string
           *
           * after first dot(.)(position:19) leave 1 character(position:20) after that (position 21) is start index of user_id
           *
           */

          $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%&?';

          // Shuffle the $str and returns substring
          // of specified length
          $first_string = substr(str_shuffle($str), 0, 18);
          $second_string = substr(str_shuffle($str), 0, 1);
          $third_string = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%&?'), 0, 5);
          $str_len = 50 - strlen($first_string . '.' . $second_string . $user_id . $third_string . '.');
          $four_string = substr(str_shuffle($str), 0, $str_len);
          $random_string = $first_string . '.' . $second_string . $user_id . $third_string . '.' . $four_string;

          Log::info($random_string);
          (new UserController())->addVideoGenerateHistory('You can\'t add more than ' . $queue_limit . ' videos for download at a time. Please try after some time.',$user_id,NULL,NULL,$content_id,3,NULL);
          return Response::json(array('code' => 201, 'message' => 'You can\'t add more than ' . $queue_limit . ' videos for download at a time. Please try after some time.', 'cause' => '', 'data' => json_decode("{}")));
        }
      }

      //Send all data for generate video
      $job = new IntrosVideoTemplateJob($json_request_data);
      $data = $this->dispatch($job);
      $result = $job->getResponse();

      $image_list = DB::select('SELECT
                                        mdm.uuid as my_design_id,
                                        um.uuid as user_id,
                                        scm.uuid as sub_category_id,
                                        mdm.user_template_name,
                                        IF(mdm.overlay_image != "",CONCAT("' . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",mdm.overlay_image),"") as overlay_image,
                                        IF(mdm.image != "",CONCAT("' . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",mdm.image),"") as sample_image,
                                        coalesce(mdm.color_value,"") AS color_value,
                                        mdm.update_time
                                   FROM
                                       my_design_master as mdm,
                                       user_master as um,
                                       sub_category_master as scm
                                   WHERE
                                    mdm.user_id = um.id AND
                                    mdm.sub_category_id=scm.id AND
                                    mdm.uuid = ?', [$my_design_id]);


      if ($result['result_status'] == 0) {
        $response = Response::json(array('code' => 201, 'message' => 'Video is unable to generate video', 'cause' => '', 'data' => json_decode("{}")));
      } else {
        $download_id = $result['download_id'];
        $result_array = array('my_design_id'=>$my_design_id,'download_id' => $download_id, 'is_limit_exceeded' => 0,'result' => $image_list);
        $response = Response::json(array('code' => 200, 'message' => 'Video generated successfully', 'cause' => '', 'data' =>$result_array));
      }

      (new UserController())->deleteAllRedisKeys("getRecentMyDesign:$user_id");

    } catch (Exception $e) {
      (new ImageController())->logs("generateIntrosVideo",$e);
//      Log::error("generateIntrosVideo : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'generate video.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      (new UserController())->addVideoGenerateHistory($e->getMessage(),$user_id,NULL,NULL,$content_id,3,NULL);
      DB::rollBack();
    }
    return $response;
  }
}
