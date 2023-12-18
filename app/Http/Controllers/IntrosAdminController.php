<?php

namespace App\Http\Controllers;

use App\Jobs\IntrosPreviewVideoJob;
use Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use Log;
use Response;

class IntrosAdminController extends Controller
{
    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addIntrosVideoByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addIntrosVideoByAdmin",
     *        summary="Add intros Video By Admin",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="request_data",
     *          required=true,
     *          type="string",
     *          description="Give catalog_id,is_featured,is_free,search_category in json object",
     *
     *         @SWG\Schema(
     *              required={"video_name","catalog_id","is_featured","is_free","search_category"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1),
     *              @SWG\Property(property="is_featured",type="integer", example=1),
     *              @SWG\Property(property="is_free",type="integer", example=1),
     *              @SWG\Property(property="search_category",type="string", example=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="Sample image uploading",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="sample_video",
     *         in="formData",
     *         description="Sample video uploading",
     *         required=true,
     *         type="file"
     *     ),
     *    *     @SWG\Parameter(
     *         name="audio",
     *         in="formData",
     *         description="audio uploading",
     *         required=true,
     *         type="file"
     *     ),
     *    *     @SWG\Parameter(
     *         name="zip",
     *         in="formData",
     *         description="zip uploading(which contain video,json and image)",
     *         required=true,
     *         type="file"
     *     ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Template added successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=434,
     *            description="Error",
     *
     *         @SWG\Schema(
     *
     *            @SWG\Property(property="Sample_Response", type="string", example={"code":434,"message":"File does not exist. File name : health_care_video_mdj8_19.mp4","cause":"","data":{}}, description=""),
     *        ),
     *      ),
     *
     *    @SWG\Response(
     *            response=201,
     *            description="Error",
     *     ),
     *    )
     */
    /**
     * @api {post} addIntrosVideoByAdmin addIntrosVideoByAdmin
     *
     * @apiName addIntrosVideoByAdmin
     *
     * @apiGroup Admin_video
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "catalog_id":1, //compulsory
     * "is_featured":1, //compulsory
     * "is_free":1, //compulsory
     * "search_ctegory":"" //compulsory
     * }
     * file:sample_image.png //compulsory
     * sample_video:sample_video.mp4 //compulsory
     * audio:123.mp3 //compulsory
     * zip:123.zip //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template added successfully.",
     * "cause": "",
     * "data": {
     * "preview_id": 43,
     * "content_id": 66
     * }
     * }
     */
    public function addIntrosVideoByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (! $request_body->hasFile('sample_video')) {
                return Response::json(['code' => 201, 'message' => 'Required field sample_video is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (! $request_body->hasFile('zip')) {
                return Response::json(['code' => 201, 'message' => 'Required field zip is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id', 'is_featured', 'is_free', 'search_category', 'template_name'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : null;
            $search_category = strtolower($request->search_category);
            $template_name = $request->template_name;
            $created_at = date('Y-m-d H:i:s');

            $sample_image = Input::file('file');
            $sample_video = Input::file('sample_video');
            $zip_file = Input::file('zip');

            /** verify   **/
            if (($response = (new ImageController())->verifySampleImageForVideo($sample_image)) != '') {
                return $response;
            }

            if (($response = (new ImageController())->verifyVideo($sample_video)) != '') {
                return $response;
            }

            if (($response = (new ImageController())->verifyZipFile($zip_file)) != '') {
                return $response;
            }

            $resource_video_name = '';
            $resource_image_name = '';
            $json_data = '';
            $json_file_name = '';
            $video_file_array = [];
            $json_file_array = [];
            $resource_image_array = [];
            $error_msg = '';

            $zip = new \ZipArchive;

            if ($zip->open($zip_file) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $filesize = $zip->statIndex($i)['size'];
                    if ($filesize > 0) {
                        $fileinfo = pathinfo($filename);
                        $basename = $fileinfo['basename'];
                        $extension = $fileinfo['extension'];

                        if ($extension == 'mp4' && $error_msg == '') {
                            $resource_video_name = $basename;
                            array_push($video_file_array, $resource_video_name);
                            $MAXIMUM_FILESIZE = 10 * 1024 * 1024;
                            if ($filesize <= $MAXIMUM_FILESIZE) {
                                //                (new ImageController())->deleteVideo($resource_video_name);
                                copy('zip://'.$zip_file.'#'.$filename, '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$resource_video_name);
                            } else {
                                $video_size_mb = $filesize / 1024 / 1024;
                                $video_size_mb = round($video_size_mb, 2);
                                $error_msg = "Background video file size is greater than 10MB.The size of Background video is $video_size_mb MB.";
                            }
                        } elseif ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' && $error_msg == '') {
                            $resource_image_name = $basename;
                            array_push($resource_image_array, $resource_image_name);
                            $MAXIMUM_FILESIZE = 150 * 1024;
                            if ($filesize <= $MAXIMUM_FILESIZE) {
                                //                (new ImageController())->unlinkFileFromLocalStorage($resource_image_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
                                copy('zip://'.$zip_file.'#'.$filename, '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$resource_image_name);
                            } else {
                                $error_msg = 'Resource image file size is greater than 150KB.';
                            }
                        } elseif ($extension == 'json' && $error_msg == '') {
                            array_push($json_file_array, $basename);
                            $json_file_name = uniqid().'_json_file_'.time().'.'.$extension;
                            copy('zip://'.$zip_file.'#'.$filename, '../..'.Config::get('constant.TEMP_DIRECTORY').$json_file_name);
                            $json_data = json_decode(file_get_contents('../..'.Config::get('constant.TEMP_DIRECTORY').$json_file_name));
                        }
                    }
                }
                $zip->close();
            }

            $resource_video_path = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$resource_video_name;
            $json_file_path = '../..'.Config::get('constant.TEMP_DIRECTORY').$json_file_name;

            /** Delete file if any error in zip data */
            if (empty($video_file_array) || empty($json_file_array) || $error_msg != '') {
                if ($resource_video_name != '') {
                    (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
                }
                if (count($resource_image_array) > 0) {
                    foreach ($resource_image_array as $image_name) {
                        $resource_image_path = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$image_name;
                        (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
                    }
                }
                if ($json_file_name != '') {
                    (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
                }
            }
            if ($error_msg != '') {
                return Response::json(['code' => 201, 'message' => $error_msg, 'cause' => '', 'data' => json_decode('{}')]);
            }
            if (empty($video_file_array)) {
                return Response::json(['code' => 201, 'message' => 'Required file video  is missing in zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            if (empty($json_file_array)) {
                return Response::json(['code' => 201, 'message' => 'Required file json is missing in zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /* check resource image/video exist */
            if (count($resource_image_array) > 0) {
                $exist_files_array = [];
                foreach ($resource_image_array as $image_name) {
                    if (($response = (new ImageController())->checkIsResourceImageExist($image_name)) == '') {
                        array_push($exist_files_array, $image_name);
                    }
                }
                /* Delete file*/
                if (count($exist_files_array) > 0) {
                    if ($resource_video_name != '') {
                        (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
                    }
                    if (count($resource_image_array) > 0) {
                        foreach ($resource_image_array as $image_name) {
                            $resource_image_path = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$image_name;
                            (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
                        }
                    }
                    if ($json_file_name != '') {
                        (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
                    }
                    $array = ['existing_files' => $exist_files_array];
                    $result = json_decode(json_encode($array), true);

                    return $response = Response::json(['code' => 420, 'message' => 'Resource image already exists.', 'cause' => '', 'data' => $result]);
                }
            }

            if (($response = (new ImageController())->checkIsVideoExist($resource_video_name)) == '') {
                if ($resource_video_name != '') {
                    (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
                }
                if (count($resource_image_array) > 0) {
                    foreach ($resource_image_array as $image_name) {
                        $resource_image_path = '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$image_name;
                        (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
                    }
                }
                if ($json_file_name != '') {
                    (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
                }

                return $response = Response::json(['code' => 420, 'message' => 'Resource video already exist. File name : '.$resource_video_name, 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateIntrosFonts($json_data)) != '') {
                return $response;
            }

            $uuid = (new ImageController())->generateUUID();
            if ($uuid == '') {
                return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            DB::beginTransaction();

            $color_value = (new ImageController())->getRandomColor($sample_image);

            $sample_video_name = $sample_video->getClientOriginalName();
            if ($sample_video_name != $json_data->sample_video_url) {
                return Response::json(['code' => 201, 'message' => 'Sample video does match with json file video.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /** Sample image  */
            $catalog_image = (new ImageController())->generateNewFileName('sample_image', $sample_image);
            (new ImageController())->saveMultipleOriginalImage($catalog_image, 'file');
            (new ImageController())->saveMultipleCompressedImage($catalog_image, 'file');
            (new ImageController())->saveMultipleThumbnailImage($catalog_image, 'file');
            $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
            $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

            /** Sample video */
            $original_path = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
            (new ImageController())->saveVideo($sample_video_name, $original_path, $sample_video);

            /** Save resource video information */
            $video_info = (new ImageController())->getVideoInformation($original_path.$resource_video_name);
            if ($video_info == '') {
                return Response::json(['code' => 201, 'message' => 'We couldn\'t get video information.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            (new ImageController())->saveVideoInformation($video_info, $resource_video_name, 'video', null);

            /** Audio */
            $audio_file_name = $json_data->audio_json[0]->audio_name;
            $fileData = pathinfo(basename($audio_file_name));
            $audio_name = uniqid().'_audio_file_'.time().'.'.strtolower($fileData['extension']);

            (new ImageController())->downloadAudio($audio_file_name, $audio_name);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                /**sample image */
                (new ImageController())->saveImageInToS3($catalog_image);
                (new ImageController())->saveWebpImageInToS3($file_name);

                /** Resource image and video */
                (new ImageController())->saveVideoInToS3($resource_video_name);

                if (count($resource_image_array) > 0) {
                    foreach ($resource_image_array as $image_name) {
                        (new ImageController())->saveResourceImageInToS3($image_name);
                    }
                }

                /** Sample video */
                (new ImageController())->saveVideoInToS3($sample_video_name);

                /** Audio */
                (new ImageController())->saveAudioInToS3Bucket($audio_name);
            }
            $content_type = Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');

            if (($response = (new ImageController())->checkIsVideoExist($resource_video_name)) != '') {
                return $response;
            }

            if (($response = (new ImageController())->checkIsAudioExist($audio_name)) != '') {
                return $response;
            }

            if (count($resource_image_array) > 0) {
                foreach ($resource_image_array as $image_name) {
                    if (($response = (new ImageController())->checkIsResourceImageExist($image_name)) != '') {
                        return $response;
                    }
                }
            }

            /** Update audio name in json */
            $json_data->audio_json[0]->audio_name = $audio_name;
            $json_data->audio_json[0]->audio_end_pos = $json_data->audio_json[0]->audio_end_pos / 1000;
            $json_data->audio_json[0]->audio_start_pos = $json_data->audio_json[0]->audio_start_pos / 1000;

            $content_detail = [
                'catalog_id' => $catalog_id,
                'uuid' => $uuid,
                'image' => $catalog_image,
                'content_type' => $content_type,
                'json_data' => json_encode($json_data),
                'content_file' => $sample_video_name,
                'is_free' => $is_free,
                'is_featured' => $is_featured,
                'is_portrait' => $is_portrait,
                'search_category' => $search_category,
                'template_name' => $template_name,
                'height' => $dimension['height'],
                'width' => $dimension['width'],
                'color_value' => $color_value,
                'create_time' => $created_at,
                'webp_image' => $file_name,
            ];
            $content_id = DB::table('content_master')->insertGetId($content_detail);

            $height = $json_data->video_height;
            $width = $json_data->video_width;
            $dimension = (new ImageController())->generatePreviewVideoHeightWidth($width, $height);

            $preview_detail = [
                'catalog_id' => $catalog_id,
                'content_id' => $content_id,
                'template_video' => $sample_video_name,
                'output_height' => $dimension['height'],
                'output_width' => $dimension['width'],
                'create_time' => $created_at,
            ];

            $prv_id = DB::table('preview_video_jobs')->insertGetId($preview_detail);

            DB::commit();

            $job = new IntrosPreviewVideoJob($content_id, $prv_id, '', $resource_video_name);
            $data = $this->dispatch($job);
            $result = $job->getResponse();

            $template_details = DB::select('SELECT
                                    cm.id AS content_id,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                    coalesce(cm.content_file,"") AS content_file,
                                    coalesce(cm.image,"") AS svg_file,
                                    cm.content_type,
                                    coalesce(cm.json_data,"") AS json_data,
                                    coalesce(cm.is_featured,0) AS is_featured,
                                    coalesce(cm.is_free,0) AS is_free,
                                    coalesce(cm.is_portrait,0) AS is_portrait,
                                    coalesce(cm.search_category,"") AS search_category,
                                    cm.template_name AS template_name,
                                    coalesce(am.format_name,"") as format_name,
                                    coalesce(am.duration,"") as duration,
                                    coalesce(am.size,0) as size,
                                    coalesce(am.bit_rate,0) as bit_rate,
                                    coalesce(am.genre,"") as genre,
                                    coalesce(am.tag,"") as tag,
                                    coalesce(am.title,"") as title
                                  FROM
                                     content_master as cm
                                     LEFT JOIN audio_master AS am ON cm.id = am.content_id
                                  WHERE
                                    cm.is_active = 1 AND
                                    cm.id = ? AND
                                    isnull(cm.original_img) AND
                                    isnull(cm.display_img)
                                  ORDER BY cm.update_time DESC', [$content_id]);
            if (count($template_details) > 0) {
                foreach ($template_details as $key) {
                    if ($key->json_data != '') {
                        $key->json_data = json_decode($key->json_data);
                    }
                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_AUDIO')) {
                        $key->content_file = Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }

                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_3D_SHAPE')) {
                        $key->content_file = Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }
                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO')) {
                        $key->content_file = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }

                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON') || $key->content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                        $key->content_file = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }

                    if ($key->content_type == Config::get('constant.CONTENT_TYPE_OF_SVG')) {
                        $key->thumbnail_img = '';
                        $key->compressed_img = '';
                        $key->original_img = '';
                        $key->svg_file = Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$key->svg_file;
                    } else {
                        $key->svg_file = '';
                    }
                }
                $template_details = $template_details[0];
            } else {
                $template_details = '{}';
            }
            /** Delete json file from temp folder */
            (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);

            if ($result['result_status'] == 0) {
                $response = Response::json(['code' => 201, 'message' => 'Json is unable to preview video', 'cause' => '', ['preview_id' => $prv_id, 'content_id' => $content_id, 'template_detail' => $template_details]]);
            } else {
                $preview_id = $result['preview_id'];
                $response = Response::json(['code' => 200, 'message' => 'Template added successfully.', 'cause' => '', 'data' => ['preview_id' => $preview_id, 'content_id' => $content_id, 'template_detail' => $template_details]]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('addIntrosVideoByAdmin', $e);
            //      Log::error("addIntrosVideoByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' add intros video by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            /** Delete json file from temp folder */
            if (isset($json_file_path) && $json_file_path != '') {
                (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
            }
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editIntrosVideoByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="editIntrosVideoByAdmin",
     *        summary="Edit Json Video By Admin",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *     @SWG\Parameter(
     *          in="formData",
     *          name="request_data",
     *          required=true,
     *          type="string",
     *          description="Give content_id,catalog_id,is_featured,is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","catalog_id","is_featured","is_free","is_image_update", "is_video_update", "is_zip_update"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1),
     *              @SWG\Property(property="is_image_update",  type="integer", example=1),
     *              @SWG\Property(property="is_video_update",  type="integer", example=1),
     *              @SWG\Property(property="is_zip_update",  type="integer", example=1),
     *              @SWG\Property(property="catalog_id",  type="integer", example=1),
     *              @SWG\Property(property="is_featured",type="integer", example=1),
     *              @SWG\Property(property="is_free",type="integer", example=1),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="Sample file uploading",
     *         required=false,
     *         type="file"
     *     ),
     *          @SWG\Parameter(
     *         name="video_file",
     *         in="formData",
     *         description="Sample video uploading",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="zip",
     *         in="formData",
     *         description="Zip file uploading (It is compulsory if any of this('is_image_update','is_video_update','is_zip_update') is 1. )",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="audio",
     *         in="formData",
     *         description="Audio file uploading",
     *         required=false,
     *         type="file"
     *     ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Json added successfully.","cause":"","data":{"preview_id":1,"content_id":2845}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=434,
     *            description="Error",
     *
     *         @SWG\Schema(
     *
     *            @SWG\Property(property="Sample_Response", type="string", example={"code":434,"message":"File does not exist. File name : health_care_video_mdj8_19.mp4","cause":"","data":{}}, description=""),
     *        ),
     *      ),
     *
     *    @SWG\Response(
     *            response=201,
     *            description="Error",
     *     ),
     *    )
     */
    /**
     * @api {post} editIntrosVideoByAdmin editIntrosVideoByAdmin
     *
     * @apiName editIntrosVideoByAdmin
     *
     * @apiGroup Admin_video
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * request_data:{
     * "content_id":66,//compulsory
     * "catalog_id":1, //compulsory
     * "is_featured":1, //compulsory
     * "is_free":1, //compulsory
     * "is_image_update":0,//compulsory
     * "is_video_update":0,//compulsory
     * "is_zip_update":0,//compulsory
     * }
     * }
     * file:sample_image.jpg //optional
     * sample_video:video.mp4//optional
     * zip:file.zip//It is compulsory if any of this("is_image_update", "is_video_update", "is_zip_update") is 1.
     * audio:audio.mp3//optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template updated successfully.",
     * "cause": "",
     * "data": {
     * "content_id": 66
     * }
     * }
     */
    public function editIntrosVideoByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            //      if (($response = (new VerificationController())->validateRequiredParameter(array('content_id', 'is_image_update','is_zip_update', 'is_video_update', 'catalog_id', 'is_featured', 'is_free'), $request)) != '')
            //        return $response;
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'is_featured', 'is_free', 'search_category', 'template_name'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            //      $catalog_id = $request->catalog_id;
            $is_free = $request->is_free;
            //      $is_video_update = $request->is_video_update; //0=no,1=yes
            //      $is_image_update = $request->is_image_update; //0=no,1=yes
            //      $is_zip_update = $request->is_zip_update;//0=no,1=yes
            $is_featured = $request->is_featured;
            //      $is_portrait = isset($request->is_portrait) ? $request->is_portrait : NULL;
            $search_category = strtolower($request->search_category);
            $template_name = $request->template_name;
            //      $content_type = Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');
            //      $created_at = date('Y-m-d H:i:s');
            $dimension['height'] = null;
            $dimension['width'] = null;
            $color_value = '';
            $file_name = '';
            $catalog_image = '';
            //      $sample_video_name = "";
            //      $resource_video_name = "";
            //      $resource_image_name = "";
            //      $json_file_name = "";
            //      $json_data = "";
            //      $video_file_array = array();
            //      $resource_image_array = array();
            //      $json_file_array = array();
            //      $error_msg = "";

            //      $zip_file = Input::file('zip');
            //
            //      if ($is_image_update == 1 || $is_video_update == 1 || $is_zip_update ==1) {
            //
            //        if (!$request_body->hasFile('zip'))
            //          return Response::json(array('code' => 201, 'message' => 'Required field zip is missing or empty.', 'cause' => '', 'data' => json_decode("{}")));
            //
            //        if (($response = (new ImageController())->verifyZipFile($zip_file)) != '')
            //          return $response;
            //
            //        $zip = new \ZipArchive;
            //        if ($zip->open($zip_file) === true) {
            //          for ($i = 0; $i < $zip->numFiles; $i++) {
            //            $filename = $zip->getNameIndex($i);
            //            $filesize = $zip->statIndex($i)['size'];
            //            if ($filesize > 0) {
            //            $fileinfo = pathinfo($filename);
            //            $basename = $fileinfo['basename'];
            //            $extension = $fileinfo['extension'];
            //
            //            if ($extension == "mp4" && $error_msg == "" && $is_video_update == 1) {
            //              $resource_video_name = $basename;
            //              array_push($video_file_array, $resource_video_name);
            //              $MAXIMUM_FILESIZE = 10 * 1024 * 1024;
            //              if ($filesize <= $MAXIMUM_FILESIZE) {
            ////                (new ImageController())->deleteVideo($resource_video_name);
            //                copy("zip://" . $zip_file . "#" . $filename, '../..' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY') . $resource_video_name);
            //              } else {
            //                $video_size_mb = $filesize / 1024 / 1024 ;
            //                $video_size_mb = round($video_size_mb,2);
            //                $error_msg = "Background video file size is greater than 10MB.The size of Background video is $video_size_mb MB.";
            //              }
            //            } elseif ($extension == "jpg" || $extension == "png" || $extension == "jpeg" && $error_msg == "" && $is_image_update == 1) {
            //              $filesize = $zip->statIndex($i)['size'];
            //              $resource_image_name = $basename;
            //              array_push($resource_image_array, $resource_image_name);
            //              $MAXIMUM_FILESIZE = 150 * 1024;
            //              if ($filesize <= $MAXIMUM_FILESIZE) {
            ////                (new ImageController())->unlinkFileFromLocalStorage($resource_image_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
            //                copy("zip://" . $zip_file . "#" . $filename, '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $resource_image_name);
            //              } else {
            //                $error_msg = "Resource image file size is greater than 150KB.";
            //              }
            //            } elseif ($extension == "json" && $error_msg == "") {
            //              array_push($json_file_array, $basename);
            //              $json_file_name = uniqid() . "_json_file_" . time() . "." . $extension;
            //              copy("zip://" . $zip_file . "#" . $filename, '../..' . Config::get('constant.TEMP_DIRECTORY') . $json_file_name);
            //              $json_data = json_decode(file_get_contents('../..' . Config::get('constant.TEMP_DIRECTORY') . $json_file_name));
            //            }
            //          }
            //          }
            //          $zip->close();
            //        }
            //
            //        if ($resource_video_name != '') {
            //          $resource_video_path = '../..' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY') . $resource_video_name;
            //        }
            //        if ($json_file_name != '') {
            //          $json_file_path = '../..' . Config::get('constant.TEMP_DIRECTORY') . $json_file_name;
            //        }
            //
            //        if ($error_msg != '') {
            //
            //          /** Delete file if any error in zip data */
            //          if (isset($resource_video_path) && $resource_video_path != '') {
            //            (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
            //          }
            //          if (count($resource_image_array) > 0 ) {
            //            foreach ($resource_image_array as $image_name) {
            //              $resource_image_path = '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $image_name;
            //              (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
            //            }
            //          }
            //          if (isset($json_file_path) && $json_file_path != '') {
            //            (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
            //          }
            //          return Response::json(array('code' => 201, 'message' => $error_msg, 'cause' => '', 'data' => json_decode("{}")));
            //        }
            //        if (empty($video_file_array) && $is_video_update == 1) {
            //          /** Delete file if any error in zip data */
            //          if (isset($resource_video_path) && $resource_video_path != '') {
            //            (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
            //          }
            //          if (count($resource_image_array) > 0 ) {
            //            foreach ($resource_image_array as $image_name) {
            //              $resource_image_path = '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $image_name;
            //              (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
            //            }
            //          }
            //          if (isset($json_file_path) && $json_file_path != '') {
            //            (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
            //          }
            //          return Response::json(array('code' => 201, 'message' => 'Required file video  is missing in zip file.', 'cause' => '', 'data' => json_decode("{}")));
            //        }

            /* check resource image/video exist */
            //        if (count($resource_image_array) > 0) {
            //          $exist_files_array = array();
            //          foreach ($resource_image_array as $image_name) {
            //            if (($response = (new ImageController())->checkIsResourceImageExist($image_name)) == '') {
            //              array_push($exist_files_array, $image_name);
            //            }
            //          }
            //          /* Delete file*/
            //          if (count($exist_files_array) > 0) {
            //            if ($resource_video_name != '') {
            //              (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
            //            }
            //            if (count($resource_image_array) > 0) {
            //              foreach ($resource_image_array as $image_name) {
            //                $resource_image_path = '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $image_name;
            //                (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
            //              }
            //            }
            //            if ($json_file_name != '') {
            //              (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
            //            }
            //
            //            $array = array('existing_files' => $exist_files_array);
            //            $result = json_decode(json_encode($array), true);
            //            return $response = Response::json(array('code' => 420, 'message' => 'Resource image already exists.', 'cause' => '', 'data' => $result));
            //          }
            //        }

            //        if (($response = (new ImageController())->checkIsVideoExist($resource_video_name)) == ''){
            //          if($resource_video_name !='') {
            //            (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_video_path);
            //          }
            //          if (count($resource_image_array) > 0 ) {
            //            foreach ($resource_image_array as $image_name) {
            //              $resource_image_path = '../..' . Config::get('constant.RESOURCE_IMAGES_DIRECTORY') . $image_name;
            //              (new ImageController())->unlinkLocalStorageFileFromFilePath($resource_image_path);
            //            }
            //          }
            //          if($json_file_name !='') {
            //            (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
            //          }
            //
            //          return $response = Response::json(array('code' => 420, 'message' => 'Resource video already exist. File name : ' . $resource_video_name, 'cause' => '', 'data' => json_decode("{}")));
            //        }
            //      }
            //      if ($json_data != '') {
            //        if (($response = (new VerificationController())->validateIntrosFonts($json_data)) != '')
            //          return $response;
            //      }
            //
            //      if ($is_video_update == 1 && $resource_video_name != '') {
            //        /** Save resource video information */
            //        $original_path = '../..' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
            //        $video_info = (new ImageController())->getVideoInformation($original_path . $resource_video_name);
            //        if ($video_info == "")
            //          return Response::json(array('code' => 201, 'message' => 'We couldn\'t get video information.', 'cause' => '', 'data' => json_decode("{}")));
            //
            //        (new ImageController())->saveVideoInformation($video_info, $resource_video_name, 'video', NULL);
            //      }
            //
            $content_detail = DB::select('SELECT image,webp_image FROM content_master WHERE id = ?', [$content_id]);
            if (count($content_detail) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Template does not exist,Invalid content_id', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $image = $content_detail[0]->image;
            $webp_image = $content_detail[0]->webp_image;

            //      /** Sample image  */
            if ($request_body->hasFile('file')) {
                $sample_image = Input::file('file');

                if (($response = (new ImageController())->verifySampleImageForVideo($sample_image)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($sample_image);

                $catalog_image = (new ImageController())->generateNewFileName('sample_image', $sample_image);
                (new ImageController())->saveMultipleOriginalImage($catalog_image, 'file');
                (new ImageController())->saveMultipleCompressedImage($catalog_image, 'file');
                (new ImageController())->saveMultipleThumbnailImage($catalog_image, 'file');
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                //Delete old image
                if ($image) {
                    (new ImageController())->deleteImage($image);
                }

                //Webp image delete
                if ($webp_image) {
                    (new ImageController())->deleteWebpImage($webp_image);
                }
            }
            //
            //      /** Sample video */
            //      if ($request_body->hasFile('sample_video')) {
            //        $sample_video = Input::file('sample_video');
            //
            //        if (($response = (new ImageController())->verifyVideo($sample_video)) != '')
            //          return $response;
            //
            //        $sample_video_name = $sample_video->getClientOriginalName();
            //        $original_path = '../..' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
            //        (new ImageController())->saveVideo($sample_video_name, $original_path, $sample_video);
            //
            //        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
            //          (new ImageController())->saveVideoInToS3($sample_video_name);
            //        }
            //      }
            //
            //      /**Audio */
            //      $json_data->audio_json[0]->audio_name = $old_audio_name;
            //      $json_data->audio_json[0]->audio_end_pos = $old_audio_end_pos;
            //      $json_data->audio_json[0]->audio_start_pos = $old_audio_start_pos;
            //
            //      /** store Resource image and video into S3*/
            //      if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
            //        if ($resource_video_name != "") {
            //          (new ImageController())->saveVideoInToS3($resource_video_name);
            //        }
            //        if (count($resource_image_array) > 0 ) {
            //          foreach ($resource_image_array as $image_name) {
            //            (new ImageController())->saveResourceImageInToS3($image_name);
            //          }
            //        }
            //      }
            //
            //      if ($resource_video_name != '') {
            //        if (($response = (new ImageController())->checkIsVideoExist($resource_video_name)) != '')
            //          return $response;
            //      }
            //      if (count($resource_image_array) > 0 ) {
            //        foreach ($resource_image_array as $image_name) {
            //          if (($response = (new ImageController())->checkIsResourceImageExist($image_name)) != '')
            //            return $response;
            //        }
            //      }
            //
            //      if ($sample_video_name != '') {
            //       if($sample_video_name !=  $json_data->sample_video_url){
            //         return Response::json(array('code' => 201, 'message' => 'Sample video does match with json file video.', 'cause' => '', 'data' => json_decode("{}")));
            //       }
            //      }

            DB::beginTransaction();
            DB::update('UPDATE content_master
                        SET
                            is_free = ?,
                            is_featured = ?,
                            search_category = ?,
                            template_name = ?,
                            color_value = IF(? != "",?,color_value),
                            height = IF(? != NULL,?,height),
                            width = IF(? != NULL,?,width),
                            image = IF(? != "",?,image),
                            webp_image = IF(? != "",?,webp_image)
                            WHERE id = ?', [
                $is_free,
                $is_featured,
                $search_category,
                $template_name,
                $color_value,
                $color_value,
                $dimension['height'],
                $dimension['height'],
                $dimension['width'],
                $dimension['width'],
                $catalog_image,
                $catalog_image,
                $file_name,
                $file_name,
                $content_id,
            ]);

            //      DB::beginTransaction();
            //      DB::update('UPDATE content_master
            //                        SET
            //                            catalog_id = IF(? != "",?,catalog_id),
            //                            image = IF(? != "",?,image),
            //                            content_type = ?,
            //                            json_data = ?,
            //                            content_file = IF(? != "",?,content_file),
            //                            is_free = IF(? != is_free,?,is_free),
            //                            is_featured = IF(? != is_featured,?,is_featured),
            //                            is_portrait = IF(? != is_portrait,?,is_portrait),
            //                            search_category = IF(? != "",?,search_category),
            //                            height = IF(? != NULL,?,height),
            //                            width = IF(? != NULL,?,width),
            //                            color_value = IF(? != "",?,color_value),
            //                            webp_image = IF(? != "",?,webp_image)
            //                            WHERE id = ?', [
            //        $catalog_id,
            //        $catalog_id,
            //        $catalog_image,
            //        $catalog_image,
            //        $content_type,
            //        json_encode($json_data),
            //        $sample_video_name,
            //        $sample_video_name,
            //        $is_free,
            //        $is_free,
            //        $is_featured,
            //        $is_featured,
            //        $is_portrait,
            //        $is_portrait,
            //        $search_category,
            //        $search_category,
            //        $dimension['height'],
            //        $dimension['height'],
            //        $dimension['width'],
            //        $dimension['width'],
            //        $color_value,
            //        $color_value,
            //        $file_name,
            //        $file_name,
            //        $content_id
            //      ]);

            //      if ($sample_video_name != '') {
            //
            //        $height = $json_data->video_height;
            //        $width = $json_data->video_width;
            //        $dimension = (new ImageController())->generatePreviewVideoHeightWidth($width, $height);
            //
            //        $preview_detail = [
            //          'catalog_id' => $catalog_id,
            //          'content_id' => $content_id,
            //          'template_video' => $sample_video_name,
            //          'output_height' => $dimension['height'],
            //          'output_width' => $dimension['width'],
            //          'create_time' => $created_at
            //        ];
            //
            //        $prv_id = DB::table('preview_video_jobs')->insertGetId($preview_detail);
            //      }
            //
            DB::commit();
            //      if ($sample_video_name != '' && $prv_id != '') {
            //        $job = new IntrosPreviewVideoJob($content_id, $prv_id, $old_video_name,$resource_video_name);
            //        $data = $this->dispatch($job);
            //        $result = $job->getResponse();
            //      }

            $template_details = DB::select('SELECT
                                    cm.id AS content_id,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                    coalesce(cm.content_file,"") AS content_file,
                                    coalesce(cm.image,"") AS svg_file,
                                    cm.content_type,
                                    coalesce(cm.json_data,"") AS json_data,
                                    coalesce(cm.is_featured,0) AS is_featured,
                                    coalesce(cm.is_free,0) AS is_free,
                                    coalesce(cm.is_portrait,0) AS is_portrait,
                                    coalesce(cm.search_category,"") AS search_category,
                                    cm.template_name AS template_name,
                                    coalesce(am.format_name,"") as format_name,
                                    coalesce(am.duration,"") as duration,
                                    coalesce(am.size,0) as size,
                                    coalesce(am.bit_rate,0) as bit_rate,
                                    coalesce(am.genre,"") as genre,
                                    coalesce(am.tag,"") as tag,
                                    coalesce(am.title,"") as title
                                  FROM
                                     content_master as cm
                                     LEFT JOIN audio_master AS am ON cm.id = am.content_id
                                  WHERE
                                    cm.is_active = 1 AND
                                    cm.id = ? AND
                                    isnull(cm.original_img) AND
                                    isnull(cm.display_img)
                                  ORDER BY cm.update_time DESC', [$content_id]);
            if (count($template_details) > 0) {
                foreach ($template_details as $key) {
                    if ($key->json_data != '') {
                        $key->json_data = json_decode($key->json_data);
                    }
                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_AUDIO')) {
                        $key->content_file = Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }

                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_3D_SHAPE')) {
                        $key->content_file = Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }
                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO')) {
                        $key->content_file = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }

                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON') || $key->content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                        $key->content_file = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$key->content_file;
                    }

                    if ($key->content_type == Config::get('constant.CONTENT_TYPE_OF_SVG')) {
                        $key->thumbnail_img = '';
                        $key->compressed_img = '';
                        $key->original_img = '';
                        $key->svg_file = Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$key->svg_file;
                    } else {
                        $key->svg_file = '';
                    }
                }
                $template_details = $template_details[0];
            } else {
                $template_details = '{}';
            }

            $response = Response::json(['code' => 200, 'message' => 'Template updated successfully.', 'cause' => '', 'data' => ['content_id' => $content_id, 'template_detail' => $template_details]]);
            //      if (isset($result) && !empty($result)) {
            //        if ($result['result_status'] == 0) {
            //          $response = Response::json(array('code' => 201, 'message' => 'Template is unable to preview video', 'cause' => '', 'data' => ['preview_id' => $prv_id, 'content_id' => $content_id, 'template_detail' => $template_details]));
            //        } else {
            //          $preview_id = $result['preview_id'];
            //          $response = Response::json(array('code' => 200, 'message' => 'Template updated successfully. ', 'cause' => '', 'data' => ['preview_id' => $preview_id, 'content_id' => $content_id, 'template_detail' => $template_details]));
            //        }
            //      } else {
            //        $response = Response::json(array('code' => 200, 'message' => 'Template updated successfully.', 'cause' => '', 'data' => ['content_id' => $content_id, 'template_detail' => $template_details]));
            //      }
        } catch (Exception $e) {
            (new ImageController())->logs('editIntrosVideoByAdmin', $e);
            //      Log::error("editIntrosVideoByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit json by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            /** Delete json file from temp folder */
            //      if (isset($json_file_path) && $json_file_path != '') {
            //        (new ImageController())->unlinkLocalStorageFileFromFilePath($json_file_path);
            //      }
            //      DB::rollBack();
        }

        return $response;
    }

    public function generateRowVideo(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $ffmpeg = Config::get('constant.FFMPEG_PATH');
            $catalog_id = $request->catalog_id;

            $item_count = $request->item_count;
            $page = $request->page;
            $offset = ($page - 1) * $item_count;

            $total_row_result = DB::select('SELECT
                                              COUNT(id) as total
                                            FROM
                                              content_master
                                            WHERE
                                               catalog_id =?
                                            ORDER BY update_time ASC', [$catalog_id]);
            $total_row = $total_row_result[0]->total;

            $template_details = DB::select('SELECT
                                               id,
                                               json_data
                                         FROM
                                              content_master
                                            WHERE
                                               catalog_id =?
                                            ORDER BY update_time ASC  LIMIT ?, ?', [$catalog_id, $offset, $item_count]);

            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;
            $preview_count = 0;
            $fail_id_array = [];
            if (count($template_details) > 0) {

                foreach ($template_details as $row) {

                    $json_data = json_decode($row->json_data);
                    $content_id = $row->id;
                    $video_name = $json_data->video_json->input_video_url;
                    $row_width = $json_data->video_json->video_width;
                    $row_height = $json_data->video_json->video_height;
                    $output_dimension = (new ImageController())->generateRowVideoHeightWidth($row_width, $row_height);
                    $row_output_height = $output_dimension['height'];
                    $row_output_width = $output_dimension['width'];
                    //Get file path
                    $input_row_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$video_name;
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        $input_row_video = '"'.$input_row_video.'"';
                    }

                    $output_row_file = '../..'.Config::get('constant.THUMBNAIL_VIDEO_DIRECTORY').$video_name;

                    $row_cmd = $ffmpeg.' -i '.$input_row_video." -crf 27 -an -s $row_output_width".'x'."$row_output_height  -vcodec mpeg4 -pix_fmt yuv420p -c:v libx264 -y -preset ultrafast ".$output_row_file.' 2>&1';
                    exec($row_cmd, $row_output, $row_result);

                    if (file_exists($output_row_file) && $row_result == 0) {
                        $preview_count++;
                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveThumbnailVideoInToS3($video_name);
                        }
                    } else {
                        array_push($fail_id_array, $content_id);
                        Log::info('cmd : ', [$row_cmd]);
                        Log::error('generateRowVideo output : ', [$row_output]);
                        Log::error('generateRowVideo result : ', [$row_result]);
                    }
                }
            }
            $response = Response::json(['code' => 200, 'message' => 'Row videos successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'total_generated_video' => $preview_count, 'fail_ids' => $fail_id_array]]);

        } catch (Exception $e) {
            (new ImageController())->logs('generateRowVideo', $e);
            //      Log::error("generateRowVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' compresse row video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }
}
