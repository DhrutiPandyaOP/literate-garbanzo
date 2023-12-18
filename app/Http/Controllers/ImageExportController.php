<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteDesignCancelDownloadJob;
use App\Jobs\FailJobOlderThenDefinedTime;
use App\Jobs\ImageExportJob;
use App\Jobs\SendMailJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ImageExportController extends Controller
{

  private $job_download_id;

  public function generateDesign(Request $request_body)
  {
    try {

      $token = JWTAuth::getToken();
      $user_detail = JWTAuth::toUser($token);
      $user_id = $user_detail->id;

      $request = json_decode($request_body->input('request_data'));
      $params = json_decode($request_body->input('params'));


      if ($params->type == 'jpg') {
        $par_type = 1;
      } elseif ($params->type == 'png') {
        $par_type = 2;
      } else {
        $par_type = 3;
      }

      $type = $par_type;
      $size = $params->size;
      $userRoleId = $params->userRoleId;

      if (Config::get('constant.IS_RENDER_SERVER_WORKING') == 0) {
        $response = Response::json(array('code' => 205, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'export ' . $size . 'x sized file. Please Try Again.', 'cause' => '', 'data' => json_decode("{}")));
        return $response;
      }

      $queue_record = DB::select('SELECT COUNT(id) as total FROM design_template_jobs WHERE status = 0 AND user_id = ?', [$user_id]);
      $queue_limit = Config::get('constant.QUEUE_IMAGE_LIMIT');

      if ($queue_record[0]->total >= $queue_limit) {
        return Response::json(array('code' => 201, 'message' => 'You can\'t add more than ' . $queue_limit . ' designs for download at a time. Please try after some time.', 'cause' => '', 'data' => json_decode("{}")));
      }

      $job = new FailJobOlderThenDefinedTime();
      $this->dispatch($job);


      $user_uuid = $user_detail->uuid;


      if (isset($request->my_design_id) && $request->my_design_id != '') {

        if (($response = (new VerificationController())->validateRequiredParameter(array('json_data', 'my_design_id', 'user_template_name', 'is_stock_photos_deleted'), $request)) != '') {
          return $response;
        }

        if (($response = (new VerificationController())->validateRequiredArrayParameter(array('deleted_pages', 'deleted_object_images', 'deleted_transparent_images'), $request)) != '') {
          return $response;
        }

        if (($response = (new VerificationController())->validateRequiredParameterIsArray(array('pages_sequence'), $request)) != '') {
          return $response;
        }

        //verify all files here which comes in request
        if (($response = (new ImageController())->validateAllFilesToCreateDesign()) != '') {
          Log::error('GenerateDesign : File did not verified successfully. ', [$response]);
          return $response;
        }

        $user_template_name = (new ImageController())->removeEmoji($request->user_template_name);
        if (trim($user_template_name) == "") {
          return Response::json(array('code' => 201, 'message' => 'Please enter valid design name.', 'cause' => '', 'data' => ''));
        }


        $my_design_id = $request->my_design_id;

        $edited_json_content = $request->json_data;
        $is_stock_photos_deleted = $request->is_stock_photos_deleted;
        $pages_sequence = implode(',', $request->pages_sequence);
        $stock_photos_id_list = $request->stock_photos_id_list;
        $deleted_pages = $request->deleted_pages;
        $deleted_object_images = $request->deleted_object_images;
        $deleted_transparent_images = $request->deleted_transparent_images;
        $user_template_name = substr($request->user_template_name, 0, 100);
        $color_value = "";
        $sub_category_uuid = isset($request->sub_category_id) ? $request->sub_category_id : '';
        $create_time = date('Y-m-d H:i:s');
        $is_active = isset($request->is_active) ? $request->is_active : 1;
        $deleted_file_list = array(); //stores file_name & file_path which we have upload in s3 if any exception error occurs then get all file_list & delete one by one
        $des_folder_id = isset($request->des_folder_id) ? $request->des_folder_id : '';
        $source_folder_id = isset($request->source_folder_id) ? $request->source_folder_id : '';

        //get all design detail from my_design_id & assign this detail to variable. If design does not exist then return error message & print log
        $my_design_detail = DB::SELECT('SELECT id, json_data, json_file_name, is_multipage, image,sub_category_id from my_design_master WHERE uuid = ? AND is_active = ? ', [$my_design_id, $is_active]);
        if (count($my_design_detail) <= 0) {
          Log::error('GenerateDesign : Design does not exist.', [$my_design_id]);
          return Response::json(array('code' => 201, 'message' => 'Design does not exist.', 'cause' => '', 'data' => json_decode("{}")));
        }

        $my_design_id_int = $my_design_detail[0]->id;

        $my_design_id_for_job = $my_design_id_int;

        $json_data = json_decode($my_design_detail[0]->json_data);
        $json_file_name = $my_design_detail[0]->json_file_name;
        $is_multipage = $my_design_detail[0]->is_multipage;
        $sample_image = $my_design_detail[0]->image;
        $card_image = $my_design_detail[0]->image;


        //check sample image is arrived or not in request
        //if user changed his first page then only sample_image arrived in request otherwise not arrived
        if ($request_body->hasFile('file')) {
          $image_array = Input::file('file');
          //generate random color for this image & if not generated then put default value
          $color_value = (new ImageController())->getRandomColor($image_array);
          if (!$color_value) {
            $color_value = Config::get('constant.DEFAULT_RANDOM_COLOR_VALUE');
          }
          //generate new file_name upload this file in local & then move file to s3
          $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
          (new ImageController())->saveMyDesign($card_image);

          if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
            (new ImageController())->saveMyDesignInToS3($card_image);
          }
          DB::delete('DELETE FROM image_details WHERE name = ? ', [$sample_image]);
          //delete unused sample images
          (new ImageController())->deleteMyDesign($sample_image);
          //if any exception occurs then delete all files which we have upload previously that's why we stored name & path to this variable
          $image_detail['name'] = $card_image;
          $image_detail['path'] = "my_design";
          array_push($deleted_file_list, $image_detail);
        }

        //check this design is old or new
        if ($is_multipage) {

          //check json_data is moved to s3, if yes then get json_data from s3
          if ($json_data == NULL) {
            $file_name = Config::get('constant.JSON_FILE_DIRECTORY_OF_S3') . $json_file_name;
            $json_data = json_decode(file_get_contents($file_name));
          }

          //delete data in json
          foreach ($deleted_pages as $i => $page) {
            unset($json_data->{$page});
          }

          //add or edit data in json
          foreach ($edited_json_content as $i => $file_content) {
            $json_data->{$i} = $file_content;
          }

        } else {
          $json_data = $edited_json_content;
          $is_multipage = 1;
        }

        $content_type = isset($request->content_type) ? $request->content_type : 1;

        DB::beginTransaction();

        DB::update('UPDATE
                        my_design_master SET
                        user_template_name = ?,
                        json_data = ?,
                        json_pages_sequence = ?,
                        is_multipage = ?,
                        image = ?,
                        color_value=IF(? != "",?,color_value)
                    WHERE id = ? AND user_id = ?', [$user_template_name, json_encode($json_data), $pages_sequence, $is_multipage, $card_image, $color_value, $color_value, $my_design_id_int, $user_id]);


        if ($request_body->hasFile('object_images')) {
          $object_images = Input::file('object_images');
          foreach ($object_images as $object_image) {
            $object_image_name = $object_image->getClientOriginalName();
            $response = (new UserController)->add3DObjectImagesV2($object_image, $object_image_name, $my_design_id_int, $create_time, $deleted_file_list);
            $deleted_file_list = $response['data'];
            if ($response['code'] != 200) {
              (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
              return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => '', 'data' => json_decode("{}")));
            }
          }
        }

        //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
        if ($request_body->hasFile('transparent_images')) {
          $transparent_images = Input::file('transparent_images');
          foreach ($transparent_images as $transparent_image) {
            $transparent_image_name = $transparent_image->getClientOriginalName();
            $response = (new UserController)->addTransparentImagesV2($transparent_image, $transparent_image_name, $my_design_id_int, $create_time, $deleted_file_list);
            $deleted_file_list = $response['data'];
            if ($response['code'] != 200) {
              (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
              return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => '', 'data' => json_decode("{}")));
            }
          }
        }

        if ($request_body->hasFile('stock_photos')) {
          $stock_images = Input::file('stock_photos');
          $response = (new UserController)->addStockPhotosV2($stock_images, $my_design_id_int, $create_time, $deleted_file_list);
          $deleted_file_list = $response['data'];
          if ($response['code'] != 200) {
            (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => '', 'data' => json_decode("{}")));
          }
        }

        $uuid = $my_design_id;
        $folder_id = $des_folder_id;

        $sub_category_id = $my_design_detail[0]->sub_category_id;


        if (count($stock_photos_id_list) >= 0 && $is_stock_photos_deleted) {
          (new UserController)->removeMyDesignIdFromTheList($stock_photos_id_list, $my_design_id_int);
        }

        if (count($deleted_object_images) > 0) {
          (new UserController)->removeMyDesignIdFromThe3dImageList($deleted_object_images, $my_design_id_int);
        }

        if (count($deleted_transparent_images) > 0) {
          (new UserController)->removeMyDesignIdFromTheTransparentImageList($deleted_transparent_images, $my_design_id_int);
        }

        (new UserController)->moveToFolder($user_id, $des_folder_id, $my_design_id_int, $source_folder_id);

        DB::commit();
      } else {

        if (($response = (new VerificationController())->validateRequiredParameter(array('json_data', 'sub_category_id'), $request)) != '') {
          return $response;
        }

        if (($response = (new VerificationController())->validateRequiredParameterIsArray(array('pages_sequence'), $request)) != '') {
          return $response;
        }

        //verify all files here which comes in request
        if (($response = (new ImageController())->validateAllFilesToCreateDesign()) != '') {
          Log::error('generateDesign : File did not verified successfully. ', [$response]);
          return $response;
        }

        $json_data = json_encode($request->json_data);

        $pages_sequence = implode(',', $request->pages_sequence); //pages_sequence that manages or sequence or sorts multi-page index wise
        $sub_category_uuid = $request->sub_category_id;
        $content_type = isset($request->content_type) ? $request->content_type : 1;
        $user_template_name = substr($request->user_template_name, 0, 100);
        $create_time = date('Y-m-d H:i:s');
        $deleted_file_list = array(); //stores file_name & file_path which we have upload in s3 if any exception error occurs then get all file_list & delete one by one
        $is_active = 1;
        $folder_id = isset($request->folder_id) ? $request->folder_id : '';

          if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {

            $is_active = 0;

            $is_exist = DB::select('SELECT id,image FROM my_design_master WHERE user_id = ? AND is_active = ?', [$user_id, $is_active]);
            if (count($is_exist) > 0) {
              $old_design_id = $is_exist[0]->id;
              $old_image_name = $is_exist[0]->image;

              (new UserController)->deleteMyDesignIdFromTheList($old_design_id);
              //delete unused sample images
              (new ImageController())->deleteMyDesign($old_image_name);
              DB::delete('DELETE FROM my_design_master WHERE user_id = ? AND is_active = ?', [$user_id, $is_active]);
            }
            return $response;
          }

        //get id from uuid & check this id is exist in our database if not then take default id & print error log
        $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_uuid]);
        if (count($sub_category_detail) <= 0) {
          Log::error('generateDesign : Sub category does not exist.  ', [$sub_category_uuid]);
          $sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES');
        } else {
          $sub_category_id = $sub_category_detail[0]->id;
        }

        //check sample image is arrived or not in request, if not then print error log & return error message with 201 code
        if (!$request_body->hasFile('file')) {
          Log::error('generateDesign : Required field file is missing or empty. ');
          return Response::json(array('code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
        }

        $image_array = Input::file('file');

        //generate random color for this image & if not generated then put default value
        $color_value = (new ImageController())->getRandomColor($image_array);
        if (!$color_value) {
          $color_value = Config::get('constant.DEFAULT_RANDOM_COLOR_VALUE');
        }

        //generate new file_name upload this file in local & then move file to s3
        $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);

        (new ImageController())->saveMyDesign($card_image);

        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
          (new ImageController())->saveMyDesignInToS3($card_image);
        }

        //if any exception occurs then delete all files which we have upload previously that's why we stored name & path to this variable
        $image_detail['name'] = $card_image;
        $image_detail['path'] = "my_design";
        array_push($deleted_file_list, $image_detail);

        $uuid = (new ImageController())->generateUUID();

        DB::beginTransaction();
        $data = array(
          'user_id' => $user_id,
          'uuid' => $uuid,
          'sub_category_id' => $sub_category_id,
          'json_data' => $json_data,
          'json_pages_sequence' => $pages_sequence,
          'image' => $card_image,
          'color_value' => $color_value,
          'content_type' => $content_type,
          'is_active' => $is_active,
          'is_multipage' => 1,
          'create_time' => $create_time,
          'user_template_name' => $user_template_name,
        );

        $my_design_id = DB::table('my_design_master')->insertGetId($data);

        $my_design_id_for_job = $my_design_id;

        if ($request_body->hasFile('object_images')) {
          $object_images = Input::file('object_images');
          foreach ($object_images as $object_image) {
            $object_image_name = $object_image->getClientOriginalName();
            $response = (new UserController)->add3DObjectImagesV2($object_image, $object_image_name, $my_design_id, $create_time, $deleted_file_list);
            $deleted_file_list = $response['data'];
            if ($response['code'] != 200) {
              (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
              return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => '', 'data' => json_decode("{}")));
            }
          }
        }
        //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
        if ($request_body->hasFile('transparent_images')) {
          $transparent_images = Input::file('transparent_images');
          foreach ($transparent_images as $transparent_image) {
            $transparent_image_name = $transparent_image->getClientOriginalName();
            $response = (new UserController)->addTransparentImagesV2($transparent_image, $transparent_image_name, $my_design_id, $create_time, $deleted_file_list);
            $deleted_file_list = $response['data'];
            if ($response['code'] != 200) {
              (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
              return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => '', 'data' => json_decode("{}")));
            }
          }
        }

        if ($request_body->hasFile('stock_photos')) {
          $stock_images = Input::file('stock_photos');
          $response = (new UserController)->addStockPhotosV2($stock_images, $my_design_id, $create_time, $deleted_file_list);
          $deleted_file_list = $response['data'];
          if ($response['code'] != 200) {
            (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => '', 'data' => json_decode("{}")));
          }
        }

        if ($folder_id) {
          (new UserController)->moveToFolder($user_id, $folder_id, $my_design_id);
        }

        if ($is_active) {
          (new UserController())->increaseMyDesignCount($user_id, $create_time, $content_type);
        }



        DB::commit();
      }

      $sample_image = !empty($request->user_template_name) ? $request->user_template_name : "Untitled Design";
      $create_at = date('Y-m-d H:i:s');
      $this->job_download_id = base64_encode($sample_image . uniqid());
      DB::beginTransaction();
      $design_template_jobs = array(
        'download_id' => $this->job_download_id,
        'user_id' => $user_id,
        'request_body' => json_encode($request),
        'quality' => $size,
        'content_type' => $type,
        'my_design_id' => $my_design_id_for_job,
        'status' => 0,
        'is_active' => 1,
        'create_time' => $create_at,
      );


      DB::table('design_template_jobs')->insert($design_template_jobs);

      DB::commit();

      $job = new ImageExportJob($request, $type, $size, $userRoleId, $this->job_download_id);
      $data = $this->dispatch($job);

      DB::update('UPDATE design_template_jobs SET job_id=? WHERE download_id =?', [$data, $this->job_download_id]);



      $image_list = array('my_design_id' => $uuid, 'folder_id' => $folder_id, 'user_id' => $user_uuid, 'sub_category_id' => $sub_category_uuid, 'sample_image' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $card_image, 'download_id' => $this->job_download_id);

      $result_array = array('my_design_id' => $my_design_id, 'download_id' => $this->job_download_id, 'is_limit_exceeded' => 0, 'result' => $image_list);
      $response = Response::json(array('code' => 200, 'message' => 'Design generated successfully', 'cause' => '', 'data' => $result_array));

      (new UserController())->deleteAllRedisKeys("getMyDesignFolder$user_id");
      (new UserController())->deleteAllRedisKeys("getDesignFolderForAdmin$user_id");
      (new UserController())->deleteAllRedisKeys("getRecentMyDesign:$user_id");

    } catch (Exception $e) {
      $failed = 2;
      $fail_reason = json_encode(array("download_id" => $this->job_download_id, "\nerror_msg" => $e->getMessage()));
      DB::beginTransaction();
      DB::update('UPDATE design_template_jobs SET status=? , fail_reason=? , user_end_time = ? WHERE download_id =?', [$failed, $fail_reason, date('Y-m-d H:i:s'), $this->job_download_id]);
      DB::commit();

      Log::error("generateDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $deleted_file_list = isset($deleted_file_list) ? $deleted_file_list : array();
      (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;
  }

  public function checkReadyToDownloadDesign(Request $request_body)
  {
    try {
      // Get the JWT token and extract user information
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      // Decode the JSON request body
      $request = json_decode($request_body->getContent());

      // Validate required parameters using a verification controller
      if (($response = (new VerificationController())->validateRequiredParameter(array('download_id', 'filename'), $request)) != '') {
        return $response;
      }

      // Check if the cache for readiness status exists, if not create it
      if (!Cache::has("checkReadyToDownloadDesign:" . $request->download_id)) {

        Cache::put("checkReadyToDownloadDesign:" . $request->download_id, 1, Config::get('constant.CACHE_TIME_1_HOUR'));
      }

      // Retrieve the counter from the cache
      $counter = Cache::get("checkReadyToDownloadDesign:" . $request->download_id);


      if ($counter <= 10) {
        // Get download_id and filename from the request
        $download_id = $request->download_id;

        // Check if is_dismiss is set in the request
        $is_dismiss = isset($request->is_dismiss) ? $request->is_dismiss : '';

        // Retrieve design information from the database
        $job_result = DB::select('SELECT id, output_design, status,fail_reason FROM design_template_jobs WHERE download_id = ?', [$download_id]);

        if (count($job_result) > 0) {
          // Extract relevant design information
          $output_design = $job_result[0]->output_design;

          if ($is_dismiss) {
            // Dispatch a job to delete the design and cancel download
            $job = new DeleteDesignCancelDownloadJob($download_id);
            $this->dispatch($job);

            $status = 2; // Update status to indicate cancellation
            $est_time_sec = "";
          } else {
            $CheckOldSql = DB::select('SELECT status FROM design_template_jobs WHERE (download_id = ? AND TIMESTAMPDIFF(MINUTE, create_time, NOW()) < ?)', [$download_id, Config::get('constant.QUEUE_AGE_LIMIT')]);
            if (count($CheckOldSql) > 0) {
              // Update status and calculate estimated time if in queue
              $status = $job_result[0]->status;
              $id = $job_result[0]->id;

              $queue_record = DB::select('SELECT COUNT(id) total FROM design_template_jobs WHERE status = 0 AND id <= ?', [$id]);

              if ($queue_record[0]->total != 0) {
                $est_time_sec = $queue_record[0]->total * 1; // Estimated time calculation

                if ($est_time_sec == "") {
                  $est_time_sec = 2;
                } elseif ($est_time_sec > 10) {
                  $est_time_sec = 10;
                } elseif ($est_time_sec >= 2 && $est_time_sec <= 10) {
                  $est_time_sec = $est_time_sec;
                } else {
                  $est_time_sec = 2;
                }

              } else {
                $est_time_sec = "";
              }
            } else {
              $fail_reason = "Queue is older than " . Config::get('constant.QUEUE_AGE_LIMIT') . " minutes";
              DB::update('UPDATE design_template_jobs SET status = ?, is_active = ?, fail_reason = ?, attribute1 = ? ,user_end_time = ? WHERE (status = ? AND download_id = ?)', [2, 0, $fail_reason, 1001, date('Y-m-d H:i:s'), 0, $download_id]);
              (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);
              $http_code = 201;
              $msg = "Sorry, we couldn't generate the design. Please try again.";
              $result = ['status' => 2, 'output_design' => "", 'est_time_sec' => '', "download_id" => $request->download_id, "fail_reason" => $fail_reason];
              return Response::json(array('code' => $http_code, 'message' => $msg, 'cause' => '', 'data' => $result));
            }
          }
        } else {
          $status = 2;
          $est_time_sec = "";
        }

        $msg = "";
        $http_code = 200;

        if ($status == 0) {
          // Update counter and cache, indicate design not ready
          $new_counter = $counter + 1;

          Cache::put("checkReadyToDownloadDesign:" . $request->download_id, $new_counter, Config::get('constant.CACHE_TIME_1_HOUR'));

          $msg = "Design is not ready to download";
          $result = ['status' => $status, 'output_design' => "", 'est_time_sec' => $est_time_sec, "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
        }

        if ($status == 1) {
          // Delete related Redis keys, update status, and generate download URL if applicable
          (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

          //update time user will get success message
          $user_end_time = date('Y-m-d H:i:s');
          DB::update('UPDATE design_template_jobs SET user_end_time = ? WHERE download_id =?', [$user_end_time, $request->download_id]);

          $msg = "Design is ready to download";

          $result = ['status' => $status, 'output_design' => $output_design, 'est_time_sec' => "", "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
        }

        if ($status == 2) {
          // Delete related Redis keys, update status, and set HTTP code for failure
          (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

          //update time user will get success message
          $user_end_time = date('Y-m-d H:i:s');
          DB::update('UPDATE design_template_jobs SET user_end_time = ? WHERE download_id =?', [$user_end_time, $request->download_id]);

          $http_code = 201;
          $msg = "Sorry, we couldn't generate the design. Please try again.";
          $result = ['status' => $status, 'output_design' => "", 'est_time_sec' => $est_time_sec, "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
        }

        if ($status == 3) {
          // Delete related Redis keys, update status, and set HTTP code for failure
          (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

          //update time user will get success message
          $user_end_time = date('Y-m-d H:i:s');
          DB::update('UPDATE design_template_jobs SET user_end_time = ? WHERE download_id =?', [$user_end_time, $request->download_id]);

          $http_code = 203;
          $msg = "";
          $result = ['status' => $status, 'output_design' => "", 'est_time_sec' => $est_time_sec, "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
        }

        // Prepare response JSON with appropriate data
        $response = Response::json(array('code' => $http_code, 'message' => $msg, 'cause' => '', 'data' => $result));

      } else {
        $noderesponse = $this->testNodeServer($request->download_id);

        if ($noderesponse != 200) {
          // Delete related Redis keys, update status, and set HTTP code for failure
          (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

          // Handle Node server error
          $http_code = 204;
          $msg = Config::get('constant.EXCEPTION_ERROR') . ' check if design is ready to download.';
          $result = ['status' => 201, 'output_design' => "", 'est_time_sec' => '', "download_id" => $request->download_id];
          $response = Response::json(array('code' => $http_code, 'message' => $msg, 'cause' => 'Node server has been stopped.', 'data' => $result));
        } else {
          // Reset counter again, retrieve design information, and prepare response
          $new_counter_reset = 1;

          Cache::put("checkReadyToDownloadDesign:" . $request->download_id, $new_counter_reset, Config::get('constant.CACHE_TIME_1_HOUR'));

          $download_id = $request->download_id;
          $filename = $request->filename;
          $is_dismiss = isset($request->is_dismiss) ? $request->is_dismiss : '';
          $job_result = DB::select('SELECT id, output_design, status ,fail_reason FROM design_template_jobs WHERE download_id = ?', [$download_id]);

          if (count($job_result) > 0) {
            $output_design = $job_result[0]->output_design;

            if ($is_dismiss) {
              $job = new DeleteDesignCancelDownloadJob($download_id);
              $this->dispatch($job);

              $status = 2;
              $est_time_sec = "";
            } else {
              $CheckOldSql = DB::select('SELECT status FROM design_template_jobs WHERE (download_id = ? AND TIMESTAMPDIFF(MINUTE, create_time, NOW()) < ?)', [$download_id, Config::get('constant.QUEUE_AGE_LIMIT')]);
              if (count($CheckOldSql) > 0) {
                // Update status and calculate estimated time if in queue
                $status = $job_result[0]->status;
                $id = $job_result[0]->id;

                $queue_record = DB::select('SELECT COUNT(id) total FROM design_template_jobs WHERE status = 0 AND id <= ?', [$id]);

                if ($queue_record[0]->total != 0) {
                  $est_time_sec = $queue_record[0]->total * 1; // Estimated time calculation

                  if ($est_time_sec == "") {
                    $est_time_sec = 2;
                  } elseif ($est_time_sec > 10) {
                    $est_time_sec = 10;
                  } elseif ($est_time_sec >= 2 && $est_time_sec <= 10) {
                    $est_time_sec = $est_time_sec;
                  } else {
                    $est_time_sec = 2;
                  }

                } else {
                  $est_time_sec = "";
                }
              } else {
                $fail_reason = "Queue is older than " . Config::get('constant.QUEUE_AGE_LIMIT') . " minutes";
                DB::update('UPDATE design_template_jobs SET status = ?, is_active = ?, fail_reason = ?, attribute1 = ? ,user_end_time = ? WHERE (status = ? AND download_id = ?)', [2, 0, $fail_reason, 1001, date('Y-m-d H:i:s'), 0, $download_id]);
                (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);
                $http_code = 201;
                $msg = "Sorry, we couldn't generate the design. Please try again.";
                $result = ['status' => 2, 'output_design' => "", 'est_time_sec' => '', "download_id" => $request->download_id, "fail_reason" => $fail_reason];
                return Response::json(array('code' => $http_code, 'message' => $msg, 'cause' => '', 'data' => $result));
              }

            }
          } else {
            $status = 2;
            $est_time_sec = "";
          }


          $msg = "";
          $http_code = 200;

          if ($status == 0) {
            // Update counter and cache, indicate design not ready
            $new_counter = $new_counter_reset + 1;
            Cache::put("checkReadyToDownloadDesign:" . $request->download_id, $new_counter, Config::get('constant.CACHE_TIME_1_HOUR'));

            $msg = "Design is not ready to download";
            $result = ['status' => $status, 'output_design' => "", 'est_time_sec' => $est_time_sec, "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
          }

          if ($status == 1) {
            // Delete related Redis keys, update status, and generate download URL if applicable
            (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

            $msg = "Design is ready to download";

            //update time user will get success message
            $user_end_time = date('Y-m-d H:i:s');
            DB::update('UPDATE design_template_jobs SET user_end_time = ? WHERE download_id =?', [$user_end_time, $request->download_id]);

            $result = ['status' => $status, 'output_design' => $output_design, 'est_time_sec' => "", "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
          }

          if ($status == 2) {
            // Delete related Redis keys, update status, and set HTTP code for failure
            (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

            //update time user will get success message
            $user_end_time = date('Y-m-d H:i:s');
            DB::update('UPDATE design_template_jobs SET user_end_time = ? WHERE download_id =?', [$user_end_time, $request->download_id]);

            $http_code = 201;
            $msg = "Sorry, we couldn't generate the design. Please try again.";
            $result = ['status' => $status, 'output_design' => "", 'est_time_sec' => $est_time_sec, "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
          }

          if ($status == 3) {

            // Delete related Redis keys, update status, and set HTTP code for failure
            (new UserController())->deleteAllRedisKeys("checkReadyToDownloadDesign:" . $request->download_id);

            //update time user will get success message
            $user_end_time = date('Y-m-d H:i:s');
            DB::update('UPDATE design_template_jobs SET user_end_time = ? WHERE download_id =?', [$user_end_time, $request->download_id]);

            $http_code = 203;
            $msg = "Sorry, we couldn't generate the design. Please try again.";
            $result = ['status' => $status, 'output_design' => "", 'est_time_sec' => $est_time_sec, "download_id" => $request->download_id, "fail_reason" => $job_result[0]->fail_reason];
          }

          // Prepare response JSON with appropriate data
          $response = Response::json(array('code' => $http_code, 'message' => $msg, 'cause' => '', 'data' => $result));
        }
      }

    } catch (Exception $e) {
      // Log the exception and prepare error response
      (new ImageController())->logs("checkReadyToDownloadDesign", $e);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . ' Check ready to download.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  //this function will work as webhook for node sp when output url for the design is ready node will call this api and here output_url that is recived by node and status of job will be updated

  public function testNodeServer($download_id)
  {
    $apiUrl = Config::get('constant.NODE_API_URL_IP') . '/testServer';

    $ch = curl_init($apiUrl);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);

    if ($response === false) {
      DB::update("UPDATE design_template_jobs SET status = ? , fail_reason = ? , is_active = ? , user_end_time = ? WHERE download_id = ?", [2, curl_error($ch), 0, date('Y-m-d H:i:s'), $download_id]);
      DB::update("UPDATE design_template_jobs SET status = ? , fail_reason = ? , is_active = ? ,user_end_time = ? WHERE status = ?", [2, curl_error($ch), 0, date('Y-m-d H:i:s'), 0]);
      $email_array = array("annu.optimumbrew@gmail.com", "dhrutip.optimumbrew@gmail.com", "alagiyanirav@gmail.com");
      foreach ($email_array as $val) {
        $template = 'send_elert_mail_for_node_failure';
        $subject = 'PhotoADKing: Node.js Server ('.Config::get('constant.NODE_API_URL_IP').') Stopped Suddenly for PhotoAdKing Project';
        $message_body = array(
          'message' => '<p>The Node.js server ('.Config::get('constant.NODE_API_URL_IP').') for PhotoAdKing has failed unexpectedly, causing service disruption. Please investigate and address the issue urgently to restore normal operations.</p>',
        );
        $api_name = 'generateDesign';
        $api_description = 'image export design';
        $this->dispatch(new SendMailJob(null, $val, $subject, $message_body, $template, $api_name, $api_description));
      }
      curl_close($ch);
      return 201;
    } else {
      $responseData = json_decode($response, true);
      if (isset($responseData['code']) && $responseData['code'] === 200) {
        curl_close($ch);
        return 200;
      } else {
        DB::update("UPDATE design_template_jobs SET status = ? , fail_reason = ? , is_active = ? , user_end_time = ? WHERE download_id = ?", [2, curl_error($ch), 0, date('Y-m-d H:i:s'), $download_id]);
        DB::update("UPDATE design_template_jobs SET status = ? , fail_reason = ? , is_active = ? ,user_end_time = ? WHERE status = ?", [2, curl_error($ch), 0, date('Y-m-d H:i:s'), 0]);
        $email_array = array("annu.optimumbrew@gmail.com", "dhrutip.optimumbrew@gmail.com", "alagiyanirav@gmail.com");
        foreach ($email_array as $val) {
          $template = 'send_elert_mail_for_node_failure';

          $subject = 'PhotoADKing: Node.js Server ('.Config::get('constant.NODE_API_URL_IP').') Stopped Suddenly for PhotoAdKing Project';
          $message_body = array(
            'message' => '<p>The Node.js server ('.Config::get('constant.NODE_API_URL_IP').') for PhotoAdKing has failed unexpectedly, causing service disruption. Please investigate and address the issue urgently to restore normal operations.</p>',
          );
          $api_name = 'generateDesign';
          $api_description = 'image export design';
          $this->dispatch(new SendMailJob(null, $val, $subject, $message_body, $template, $api_name, $api_description));
        }
        curl_close($ch);
        return 201;
      }
    }
  }

  public function getUrlFromNodeRoute(Request $request_body)
  {
    try {
      $requested_ip = $request_body->ip();


//            if (Config::get('constant.WEBHOOK_IP_ADDRESS') != $requested_ip) {
//                $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'save design.', 'cause' => 'IP Address Does not match.', 'data' => json_decode("{}")));
//            }

      $request = json_decode($request_body->getContent());
      $download_id = $request->download_id;
      $url = $request->url;

      DB::update('UPDATE design_template_jobs SET output_design  = ? , status = ? , is_active = ? , render_end_time = ? WHERE (download_id = ? AND status = ?)', [$url, 1, 0, date('Y-m-d H:i:s'), $download_id, 0]);

      $response = Response::json(array('code' => 200, 'message' => 'Success', 'cause' => ''));
    } catch (Exception $e) {
      (new ImageController())->logs("ImageExportController.php/getUrlFromNodeRoute", $e);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check ready to download.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  public function getGenerateDesignReportForAdmin(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count', 'start_date', 'end_date'), $request)) != '')
        return $response;

      $this->page = $request->page;
      $this->item_count = $request->item_count;
      $this->offset = ($this->page - 1) * $this->item_count;
      $this->start_date = $request->start_date;
      $this->end_date = $request->end_date;

      $total_row = DB::select('SELECT
                                        COUNT(dtj.id) AS total
                                      FROM
                                          design_template_jobs AS dtj
                                      WHERE DATE(dtj.create_time) BETWEEN ? AND ?', [$this->start_date, $this->end_date]);
      $total_record = $total_row[0]->total;
      if ($total_record) {
        $result = DB::select('SELECT
                    dtj.id AS id,
                    um.email_id AS email_id,
                    dtj.content_type,
                    dtj.status,
                    dtj.quality,
                    mdm.uuid as my_design_uuid,
                    IF(dtj.my_design_id != "", CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", cm.image), NULL) AS my_design_request_img,
                    IF(dtj.my_design_id != "", CONCAT("' . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '", mdm.image), NULL) AS sample_image_user,
                    dtj.output_design as my_design_exported_img,
                    dtj.file_size as download_file_size,
                    TIMEDIFF(dtj.user_end_time, dtj.create_time) AS response_time,
                    TIMEDIFF(dtj.render_end_time, dtj.render_start_time) AS render_time,
                    dtj.create_time as S1,
                    dtj.user_end_time as S2,
                    dtj.render_start_time as R1,
                    dtj.render_end_time as R2,
                    dtj.fail_reason as fail_reason,
                    dtj.attribute1 as error_code
                FROM design_template_jobs AS dtj
                LEFT JOIN my_design_master AS mdm ON mdm.id = dtj.my_design_id
                LEFT JOIN content_master AS cm ON cm.id = mdm.content_id
                LEFT JOIN user_master AS um ON dtj.user_id = um.id
                WHERE
                    DATE(dtj.create_time) BETWEEN ? AND ?
                ORDER BY dtj.update_time DESC
                LIMIT ?, ?', [$this->start_date, $this->end_date, $this->offset, $this->item_count]);

      } else {
        $result = 0;
      }

      $is_next_page = ($total_record > ($this->offset + $this->item_count)) ? true : false;
      $redis_result = array('total_record' => $total_record, 'is_next_page' => $is_next_page, 'result' => $result);


      $response = Response::json(array('code' => 200, 'message' => 'Design report successfully received.', 'cause' => '', 'data' => $redis_result));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
    } catch (Exception $e) {
      (new ImageController())->logs("getGenerateDesignReportForAdmin", $e);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check ready to download.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  public function cancleLiveQueueJobForDesign(Request $request_body)
  {
    try {
      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameterIsArray(array('cd_ids'), $request)) != '') {
        return $response;
      }

      $cd_ids = $request->cd_ids;
      foreach ($cd_ids as $vl) {
        $result = DB::select('SELECT output_design,job_id FROM design_template_jobs WHERE download_id = ?', [$vl]);
        if (count($result) > 0) {
          DB::beginTransaction();
          $fail_reason = 'User canceled the job execution or stopped the download process.';
          DB::delete('DELETE FROM jobs WHERE id = ?', [$result[0]->job_id]);
          DB::update('UPDATE design_template_jobs SET status = ? , is_active = ? ,fail_reason = ? , attribute1 = ? ,user_end_time = ? WHERE download_id = ? AND status = ?', [3, 0, $fail_reason, 1002, date('Y-m-d H:i:s'), $vl, 0]);
          DB::commit();
        }
      }
      $response = Response::json(array('code' => 200, 'message' => 'Jobs cancelled successfully', 'cause' => '', 'data' => json_decode("{}")));

    } catch (Exception $e) {
      (new ImageController())->logs("cancleLiveQueueJobForDesign", $e);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check ready to download.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

}
