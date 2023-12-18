<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  UserController.php

File Created    :  Friday, 28th September 2018 05:22:26 pm
Author          :  Optimumbrew
Author Email    :  info@optimumbrew.com
Last Modified   :  Saturday, 29th January  2022 10:56:00 pm
-----
Purpose          :  This file provide data and handle data of user side.
-----
Copyright 2018 - 2022 Optimumbrew Technology

*/

namespace App\Http\Controllers;

use App\Jobs\activatePayPalSubscriptionAfter10MinJob;
use App\Jobs\DeleteCancelDownloadDataJob;
use App\Jobs\EmailJob;
use App\Jobs\FeedbackEmailJob;
use App\Jobs\SaveNormalImagesSearchTagJob;
use App\Jobs\SaveSearchTagJob;
use App\Jobs\VideoTemplateJob;
use App\Role;
use DateTime;
use Exception;
use Google\Cloud\Translate\V2\TranslateClient;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Swagger\Annotations as SWG;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class UserController
 */
class UserController extends Controller
{
    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
        $this->base_url = (new ImageController())->getBaseUrl();

    }

    /* =================================| User Fonts |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/uploadFontByUser",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="uploadFontByUser",
     *        summary="Upload font by user",
     *        consumes={"multipart/form-data" },
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
     *         name="font_file",
     *         in="formData",
     *         description="upload font file here",
     *         required=true,
     *         type="file"
     *     ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Font uploaded successfully.","cause":"","data":{"total_record":7,"result":{{"uploaded_font_id":83,"font_name":"Mf Wedding Bells","font_file":"5d3ec596cafce_user_uploaded_fonts_1564394902.ttf","font_url":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_fonts/5d3ec596cafce_user_uploaded_fonts_1564394902.ttf","preview_img":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_original/5d3ec596cd2f7_font_preview_image_1564394902.png"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=430,
     *            description="This feature allow for only paid user",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":430,"message":"You must upgrade your plan with any paid plan to enable this feature.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=432,
     *            description="Limit exceeded",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":432,"message":"Create design limit exceeded for Free Plan.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function uploadFontByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $create_time = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->validateUserToUploadFont($user_id)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('font_file')) {
                return Response::json(['code' => 201, 'message' => 'Required field font_file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $font_file_array = Input::file('font_file');
                if (($response = (new UserVerificationController())->verifyFontFile($font_file_array)) != '') {
                    return $response;
                }

                $font_info = (new VerificationController())->checkIsFontExist($font_file_array);

                if ($font_info['is_exist'] != 1) {
                    $font_file = $font_info['file_name'];
                    $font_name = (new ImageController())->saveUserUploadedFont($font_file);

                    $preview_img_name = (new ImageController())->generateNewFileNameForPNG('font_preview_image');
                    (new ImageController())->generatePreviewImage($font_file, $preview_img_name, $font_name, 0);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveUserUploadedFontInToS3($font_file, $preview_img_name);
                    }
                    $uuid = (new ImageController())->generateUUID();
                    DB::beginTransaction();
                    DB::insert('insert into user_uploaded_fonts(font_name,uuid, font_file, preview_img, user_ids, is_active, create_time)  VALUES(?,?,?,?,?,?,?)',
                        [$font_name, $uuid, $font_file, $preview_img_name, $user_id, 1, $create_time]);
                    DB::commit();
                } else {
                    $font_name = $font_info['font_name'];
                    $is_exist = DB::select('SELECT 1 FROM user_uploaded_fonts
                                                  WHERE
                                                  font_name = ? AND
                                                  FIND_IN_SET("'.$user_id.'", user_ids)', [$font_name]);

                    $result = DB::select('SELECT user_ids FROM user_uploaded_fonts
                                                  WHERE
                                                  font_name = ?', [$font_name]);
                    $user_ids = $result[0]->user_ids.','.$user_id;

                    if (count($is_exist) == 0) {
                        DB::beginTransaction();
                        DB::update('UPDATE
                                  user_uploaded_fonts SET
                                  user_ids = ? WHERE font_name = ? ', [$user_ids, $font_name]);
                        DB::commit();
                    } else {
                        return $response = Response::json(['code' => 201, 'message' => 'Font already exist.', 'cause' => '', 'data' => json_decode('{}')]);

                    }
                }
            }

            $uploaded_fonts = DB::select('SELECT
                                              uuf.uuid AS uploaded_font_id,
                                              uuf.font_name,
                                              uuf.font_file,
                                              IF(uuf.font_file != "",CONCAT("'.Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN').'",font_file),"") AS font_url,
                                              IF(uuf.preview_img != "",CONCAT("'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",preview_img),"") AS preview_img
                                            FROM
                                              user_uploaded_fonts as uuf
                                            WHERE find_in_set("'.$user_id.'", uuf.user_ids) AND is_active=1
                                            ORDER BY update_time DESC');

            $this->increaseFontUploadCount($user_id);

            $response = Response::json(['code' => 200, 'message' => 'Font uploaded successfully.', 'cause' => '', 'data' => ['total_record' => count($uploaded_fonts), 'result' => $uploaded_fonts]]);

        } catch (Exception $e) {
            (new ImageController())->logs('uploadFontByUser', $e);
            //      Log::error("uploadFontByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload font.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function increaseFontUploadCount($user_id)
    {
        try {

            DB::beginTransaction();
            DB::update('UPDATE user_detail SET
                          uploaded_font_count = uploaded_font_count + 1
                          WHERE  user_id = ?', [$user_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Font uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('increaseFontUploadCount', $e);
            //      Log::error("increaseFontUploadCount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'increase font upload count.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyUploadedFonts",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyUploadedFonts",
     *        summary="Get my uploaded fonts",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Fonts fetched successfully.","cause":"","data":{"total_record":5,"result":{{"uploaded_font_id":60,"font_name":"Lao UI","font_file":"5c7de8d4007aa_user_uploaded_fonts_1551755476.ttf","font_url":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_fonts/5c7de8d4007aa_user_uploaded_fonts_1551755476.ttf","preview_img":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_original/5c7de8d4051e3_font_preview_image_1551755476.png"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getMyUploadedFonts()
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getMyUploadedFonts$this->user_id")) {
                $result = Cache::rememberforever("getMyUploadedFonts$this->user_id", function () {

                    $uploaded_fonts = DB::select('SELECT
                                              uuf.uuid AS uploaded_font_id,
                                              uuf.font_name,
                                              uuf.font_file,
                                              IF(uuf.font_file != "",CONCAT("'.Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN').'",uuf.font_file),"") AS font_url,
                                              IF(uuf.preview_img != "",CONCAT("'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",uuf.preview_img),"") AS preview_img
                                            FROM
                                              user_uploaded_fonts as uuf
                                            WHERE find_in_set("'.$this->user_id.'", uuf.user_ids) AND is_active=1
                                            ORDER BY update_time DESC');

                    return ['total_record' => count($uploaded_fonts), 'result' => $uploaded_fonts];

                });
            }

            $redis_result = Cache::get("getMyUploadedFonts$this->user_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyUploadedFonts', $e);
            //      Log::error("getMyUploadedFonts : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get my uploaded fonts.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteMyUploadedFontById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteMyUploadedFontById",
     *        summary="Delete uploaded font by id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"uploaded_font_id"},
     *
     *          @SWG\Property(property="uploaded_font_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Font deleted successfully.","cause":"","data":{"total_record":1,"result":{{"uploaded_font_id":1,"font_name":"Open Sans Bold","font_file":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_fonts/5c6a4be8c7812_user_uploaded_fonts_1550470120.ttf","preview_img":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_video_thumbnail/5c6a4be8cb693_font_preview_image_1550470120.png"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function deleteMyUploadedFontById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['uploaded_font_id'], $request)) != '') {
                return $response;
            }

            $uploaded_font_id = $request->uploaded_font_id;

            DB::beginTransaction();
            DB::update('UPDATE user_uploaded_fonts
                                    SET
                                      user_ids = TRIM(BOTH "," FROM REPLACE(CONCAT(",", user_ids, ","), ",'.$user_id.',", ","))
                                    WHERE
                                      uuid = ? AND
                                      FIND_IN_SET("'.$user_id.'", user_ids)', [$uploaded_font_id]);
            DB::commit();

            $unused_fonts = DB::select('SELECT id,font_name, font_file, preview_img FROM user_uploaded_fonts WHERE uuid = ? AND (user_ids = "" OR user_ids IS NULL)', [$uploaded_font_id]);

            if (count($unused_fonts) > 0) {
                (new ImageController())->deleteUploadedFonts($unused_fonts[0]->font_file, $unused_fonts[0]->preview_img);

                DB::beginTransaction();
                DB::delete('DELETE FROM user_uploaded_fonts WHERE uuid = ?', [$uploaded_font_id]);
                DB::commit();

            }

            $uploaded_fonts = DB::select('SELECT
                                              uuf.uuid AS uploaded_font_id,
                                              uuf.font_name,
                                              uuf.font_file,
                                              IF(uuf.font_file != "",CONCAT("'.Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN').'",uuf.font_file),"") AS font_url,
                                              IF(uuf.preview_img != "",CONCAT("'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",uuf.preview_img),"") AS preview_img
                                            FROM
                                              user_uploaded_fonts as uuf
                                            WHERE find_in_set("'.$user_id.'", uuf.user_ids) AND uuf.is_active=1
                                            ORDER BY uuf.update_time DESC');

            $response = Response::json(['code' => 200, 'message' => 'Font deleted successfully.', 'cause' => '', 'data' => ['total_record' => count($uploaded_fonts), 'result' => $uploaded_fonts]]);

        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyUploadedFontById', $e);
            //      Log::error("deleteMyUploadedFontById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete font.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getAllFonts",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getAllFonts",
     *        summary="Get all fonts by sub_category_id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id","page","item_count"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=105, description="sub_category_id of font"),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Fonts fetched successfully.","cause":"","data":{"total_record":115,"is_next_page":true,"result":{{"font_id":77,"catalog_id":76,"font_name":"3d","font_file":"3d.ttf","font_url":"http://192.168.0.113/photoadking_testing/image_bucket/fonts/3d.ttf","font_json_file": "http://192.168.0.113/photoadking_testing/font_json/fonts/3d.json","preview_image":"","ios_font_name":"3d","android_font_name":"fonts/3d.ttf"},{"font_id":78,"catalog_id":76,"font_name":"AdineKirnberg-Script","font_file":"font22.ttf","font_url":"http://192.168.0.113/photoadking_testing/image_bucket/fonts/font22.ttf","preview_image":"","ios_font_name":"AdineKirnberg-Script","android_font_name":"fonts/font22.ttf"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get fonts.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getAllFonts(Request $request)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::rememberforever("getAllFonts:$this->sub_category_id:$this->page:$this->item_count", function () {

                $this->offline_catalogs = Config::get('constant.OFFLINE_CATALOG_IDS_OF_FONT');

                $total_row = Cache::rememberforever("getAllFonts:$this->sub_category_id:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT
                                                COUNT(id) as total
                                              FROM
                                                font_master
                                              WHERE
                                                is_active = ? AND
                                                catalog_id NOT IN('.$this->offline_catalogs.')', [1]);

                    return $total_row = $total_row_result[0]->total;
                });

                $fonts_list = DB::select('SELECT
                                              fm.uuid AS font_id,
                                              ctm.uuid as catalog_id,
                                              fm.font_name,
                                              fm.font_file,
                                              IF(fm.font_file != "",CONCAT("'.Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",fm.font_file),"") AS font_url,
                                              IF(fm.font_json_file != "",CONCAT("'.Config::get('constant.FONT_JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",fm.font_json_file),"") as font_json_file,
                                              IF(fm.preview_image != "",CONCAT("'.Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",fm.preview_image),"") AS preview_image,
                                              fm.ios_font_name,
                                              fm.android_font_name,
                                              COALESCE(fm.issue_code, "") AS issue_code
                                            FROM
                                              font_master as fm,
                                              catalog_master as ctm
                                            WHERE
                                              fm.catalog_id=ctm.id AND
                                              fm.is_active = ? AND
                                              fm.catalog_id NOT IN('.$this->offline_catalogs.')
                                            ORDER BY fm.font_name ASC LIMIT ?,?', [1, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $fonts_list];

            });

            $response = Response::json(['code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllFonts', $e);
            //      Log::error("getAllFonts : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| User |=============================*/

    public function uploadImage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            if (($response = (new VerificationController())->validateRequiredParameter(['unique_id', 'chunks', 'chunk'], $request_body)) != '') {
                return $response;
            }

            $response = $this->uploadChunkFile($request_body);
            if (is_object($response['data'])) {

                $file = $response['data'];
                $file_size = $file->getSize();
                if (($response = (new UserVerificationController())->verifyImageForUser($file)) != '') {
                    Log::error('UploadedFile : verifyImageForUser : ', ['response' => $response]);

                    return $response;
                }

                if (($response = (new VerificationController())->validateUserToUploadImage($user_id)) != '') {
                    Log::error('UploadedFile : validateUserToUploadImage : ', ['response' => $response]);

                    return $response;
                }

                $image = (new ImageController())->generateName($file, 'user_upload');
                $file_info = (new ImageController())->saveUserUploadedImage($file, $image)['msg'];
                $webp_file_name = $file_info['file_name'];
                $height = $file_info['height'];
                $width = $file_info['width'];

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveUserUploadedImageInToS3($image, $webp_file_name);
                }

                $uuid = (new ImageController())->generateUUID();

                DB::insert('INSERT INTO upload_master
                            (user_id, uuid, file_name, webp_file_name, height, width, is_active, create_time)
                  VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)',
                    [$user_id, $uuid, $image, $webp_file_name, $height, $width, 1]);

                $this->increaseFileSize($user_id, $file_size);

                $image_detail[0]['upload_id'] = $uuid;
                $image_detail[0]['update_time'] = date('Y-m-d H:i:s');
                $image_detail[0]['original_img'] = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                $image_detail[0]['compressed_img'] = Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                $image_detail[0]['thumbnail_img'] = Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                $upload_limit = (new VerificationController())->getTotalRemainCountToUploadImageOfUSer($user_id);
                $response = Response::json(['code' => 200, 'message' => 'File uploaded successfully.', 'cause' => '', 'data' => ['result' => $image_detail, 'upload_limit' => $upload_limit]]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('uploadImage', $e);
            //Log::error("uploadImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'upload image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/uploadImageByUser",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="uploadImageByUser",
     *        summary="Upload image by user",
     *        consumes={"multipart/form-data" },
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
     *         name="file",
     *         in="formData",
     *         description="upload image",
     *         required=true,
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} uploadImageByUser uploadImageByUser
     *
     * @apiName uploadImageByUser
     *
     * @apiGroup User
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
     * "file":"[{},{}]" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Image uploaded successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function uploadImageByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $user_id = $user_detail->id;

            $create_time = date('Y-m-d H:i:s');
            $exception = [];
            $exception_response = [];
            $success_response = $image_list = [];

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $total_image_array = Input::file('file');
            if (! is_array($total_image_array)) {
                return Response::json(['code' => 201, 'message' => 'Required field file is accept only array.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $total_file_size = array_sum($_FILES['file']['size']) / 1048576;  //convert bytes to mb :- size_in_bytes/(1024*1024)
            if (($response = (new VerificationController())->validateUserToUploadMultipleImage($user_id, count($total_image_array), round($total_file_size, 2))) != '') {
                return $response;
            }

            foreach ($total_image_array as $i => $image_array) {
                try {

                    if (($response = (new UserVerificationController())->verifyImageForUser($image_array)) != '') {
                        $exception['file'] = $image_array->getClientOriginalName();
                        $exception['reason'] = json_decode(json_encode($response))->original->message;
                        array_push($exception_response, $exception);

                        continue;
                    }
                    //
                    //          if (($response = (new VerificationController())->validateUserToUploadImage($user_id)) != ''){
                    //            $exception_response['file'] = $image_array->getClientOriginalName();
                    //            $exception_response['reason'] = json_decode(json_encode($response))->original->message;
                    //            array_push($exception_response_array, $exception_response);
                    //            continue;
                    //          }

                    //      $image = (new ImageController())->generateNewFileNameForUser('user_upload', $image_array);
                    $image_size = $image_array->getSize();
                    $image = (new ImageController())->generateNewFileNameForUser('user_upload', $image_array);
                    if ($image['code'] == 201) {
                        $exception['file'] = $image_array->getClientOriginalName();
                        $exception['reason'] = $image['msg'];
                        array_push($exception_response, $exception);

                        continue;
                    } else {
                        $image = $image['msg'];
                    }

                    //      $file_info = (new ImageController())->saveUserUploadedImage($image_array,$image);
                    $file_info = (new ImageController())->saveUserUploadedImage($image_array, $image);
                    if ($file_info['code'] == 201) {
                        $exception['file'] = $image_array->getClientOriginalName();
                        $exception['reason'] = $file_info['msg'];
                        array_push($exception_response, $exception);

                        continue;
                    } else {
                        $file_info = $file_info['msg'];
                    }

                    $webp_file_name = $file_info['file_name'];
                    $height = $file_info['height'];
                    $width = $file_info['width'];

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        //          (new ImageController())->saveUserUploadedImageInToS3($image, $webp_file_name);
                        if (($file_info = (new ImageController())->saveUserUploadedImageInToS3($image, $webp_file_name)) != '') {
                            $exception['file'] = $image_array->getClientOriginalName();
                            $exception['reason'] = $file_info;
                            array_push($exception_response, $exception);

                            continue;
                        }
                    }

                    $uuid = (new ImageController())->generateUUID();
                    if ($uuid == '') {
                        $exception['file'] = $image_array->getClientOriginalName();
                        $exception['reason'] = 'Something went wrong.Please try again.';
                        array_push($exception_response, $exception);

                        continue;
                        //            return Response::json(array('code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode("{}")));
                        //            $exception_response .= Response::json(array('code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => $image_array ));
                    }

                    DB::beginTransaction();
                    DB::insert('insert into upload_master
                            (user_id,uuid,file_name, webp_file_name, height, width, is_active, create_time) VALUES(?,?,?,?,?,?,?,?)',
                        [$user_id, $uuid, $image, $webp_file_name, $height, $width, 1, $create_time]);
                    DB::commit();

                    $this->increaseFileSize($user_id, $image_size);
                    array_push($success_response, $image_array->getClientOriginalName());

                    $image_detail['upload_id'] = $uuid;
                    $image_detail['update_time'] = $create_time;
                    $image_detail['original_img'] = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                    $image_detail['compressed_img'] = Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                    $image_detail['thumbnail_img'] = Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                    array_push($image_list, $image_detail);

                } catch (Exception $e) {
                    (new ImageController())->logs('uploadImageByUser', $e);
                    Log::error('uploadImageByUser foreach loop : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                    $exception['file'] = $image_array->getClientOriginalName();
                    $exception['reason'] = $e->getMessage();
                    array_push($exception_response, $exception);

                    continue;
                }
            }

            $upload_limit = (new VerificationController())->getTotalRemainCountToUploadImageOfUSer($user_id);

            if ($exception_response != null) {
                $response = Response::json(['code' => 417, 'message' => 'This image can not uploaded.', 'cause' => '', 'data' => $exception_response]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Image uploaded successfully.', 'cause' => '', 'data' => ['success_response' => $success_response, 'upload_limit' => $upload_limit, 'result' => $image_list]]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('uploadImageByUser', $e);
            //      Log::error("uploadImageByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function increaseFileSize($user_id, $image_size)
    {
        try {

            $size = number_format($image_size / 1048576, 2);
            //Log::info('increaseFileSize : ', ['user_id' => $user_id, 'image_size' => $image_size, 'size' => $size]);
            DB::beginTransaction();
            DB::update('UPDATE user_detail SET
                          uploaded_img_count = uploaded_img_count + 1,
                          uploaded_img_total_size = round(uploaded_img_total_size + "'.$size.'",2)
                          WHERE  user_id = ?', [$user_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Image uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('increaseFileSize', $e);
            //      Log::error("increaseFileSize : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'increase file size.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyUploadedImages",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyUploadedImages",
     *        summary="Get my uploaded images",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"total_record":5,"is_next_page":false,"result":{{"upload_id":81,"user_id":23,"original_img":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_original/5d19ea1000d64_user_upload_1561979408.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_compressed/5d19ea1000d64_user_upload_1561979408.png","thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_thumbnail/5d19ea1000d64_user_upload_1561979408.png","update_time":"2019-07-01 11:10:08"}}}}),),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getMyUploadedImages(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            //Log::info('request_data', ['request_data' => $request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getMyUploadedImages$this->user_id:$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getMyUploadedImages$this->user_id:$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(id) as total FROM upload_master WHERE user_id = ? AND is_active = ?', [$this->user_id, 1]);
                    $total_row = $total_row_result[0]->total;

                    $image_list = DB::select('SELECT
                                        upm.uuid as upload_id,
                                        IF(upm.file_name != "",CONCAT("'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",upm.file_name),"") as original_img,
                                        IF(upm.file_name != "",CONCAT("'.Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",upm.file_name),"") as compressed_img,
                                        IF(upm.file_name != "",CONCAT("'.Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",upm.file_name),"") as thumbnail_img,
                                        upm.update_time
                                      FROM
                                        upload_master as upm
                                      WHERE
                                        upm.user_id = ? AND
                                        upm.is_active = ?
                                      ORDER BY upm.update_time DESC
                                      LIMIT ?,?', [$this->user_id, 1, $this->offset, $this->item_count]);

                    $upload_limit = (new VerificationController())->getTotalRemainCountToUploadImageOfUSer($this->user_id);
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['upload_limit' => $upload_limit, 'total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $image_list];

                });
            }

            $redis_result = Cache::get("getMyUploadedImages$this->user_id:$this->page:$this->item_count");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyUploadedImages', $e);
            //      Log::error("getMyUploadedImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get my uploaded images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteMyUploadedImageById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteMyUploadedImageById",
     *        summary="Delete uploaded image by id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"upload_id"},
     *
     *          @SWG\Property(property="upload_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function deleteMyUploadedImageById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['upload_id'], $request)) != '') {
                return $response;
            }

            $upload_id = $request->upload_id;

            DB::beginTransaction();
            DB::delete('delete from upload_master where uuid = ? AND user_id = ?', [$upload_id, $user_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Image deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyUploadedImageById', $e);
            //      Log::error("deleteMyUploadedImageById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ============================| My Design Folders |==========================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/createMyDesignFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="createMyDesignFolder",
     *        summary="create My Design Folder",
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
     *          in="body",
     *        name="request_body",
     *        description="",
     *
     *   	  @SWG\Schema(
     *          required={"content_type","folder_name"},
     *
     *          @SWG\Property(property="content_type",  type="integer", example="2", description="1=image folder,2=video folder"),
     *          @SWG\Property(property="folder_name",  type="text", example="Festivals", description="compulsory when you want to create folder"),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Folder created successfully.","cause":"","data":{"folder_id": 7,"user_id": 3,"folder_name": "fourth_folder","my_design_ids": null,"update_time": "2019-11-15 09:26:04"}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=432,
     *            description="Limit exceeded",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":432,"message":"Free users are not authorized to create and store folders for your design.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to create folder.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function createMyDesignFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $user_uuid = $user_detail->uuid;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_type', 'folder_name'], $request)) != '') {
                return $response;
            }

            $folder_content_type = $request->content_type;
            $folder_name = mb_substr(trim($request->folder_name), 0, 100);

            if (($response = (new VerificationController())->validateUserToCreateFolder($user_id)) != '') {
                return $response;
            }

            $is_exist = DB::select('SELECT 1 FROM design_folder_master WHERE folder_name = ? AND user_id = ? AND folder_content_type = ? ', [$folder_name, $user_id, $folder_content_type]);
            if (count($is_exist) > 0) {
                return Response::json(['code' => 201, 'message' => 'A folder with the same name already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $uuid = (new ImageController())->generateUUID();
            $data = [
                'user_id' => $user_id,
                'uuid' => $uuid,
                'folder_name' => $folder_name,
                'folder_content_type' => $folder_content_type,
                'is_active' => 1,
            ];
            DB::table('design_folder_master')->insert($data);

            $result = [[
                'folder_id' => $uuid,
                'user_id' => $user_uuid,
                'folder_name' => $folder_name,
                'my_design_ids' => null,
                'folder_content_type' => $folder_content_type,
                'update_time' => date('Y-m-d H:i:s'),
            ]];

            $response = Response::json(['code' => 200, 'message' => 'Folder created successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('createMyDesignFolder', $e);
            //Log::error("createMyDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'create folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editMyDesignFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="editMyDesignFolder",
     *        summary="edit MyDesign Folder",
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
     *          in="body",
     *        name="request_body",
     *        description="",
     *
     *   	  @SWG\Schema(
     *          required={"folder_id","folder_name"},
     *
     *          @SWG\Property(property="folder_id",  type="integer", example=1, description="compulsory when you want to rename folder"),
     *          @SWG\Property(property="folder_name",  type="text", example="Festivals", description="compulsory when you want to rename folder"),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Folder name updated successfully.","cause":"","data":{"folder_id": 7,"user_id": 3,"folder_name": "fourth_folder","my_design_ids": null,"update_time": "2019-11-15 09:26:04"}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"The length of your folder-name is too long.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=202,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"A folder with this name already exists, please use a different name for the folder to create a new folder.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=203,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to update folder.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function editMyDesignFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['folder_id', 'folder_name'], $request)) != '') {
                return $response;
            }

            $folder_id = $request->folder_id;
            $folder_name = mb_substr(trim($request->folder_name), 0, 100);

            $is_exist = DB::select('SELECT 1 FROM design_folder_master WHERE folder_name LIKE ? AND uuid != ?  AND user_id = ?', [$folder_name, $folder_id, $user_id]);
            if (count($is_exist) > 0) {
                return Response::json(['code' => 201, 'message' => 'A folder with the same name already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $status = DB::update('UPDATE design_folder_master SET folder_name = ? WHERE uuid = ?', [$folder_name, $folder_id]);
            if (! $status) {
                return Response::json(['code' => 201, 'message' => 'Folder does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $result = DB::select('SELECT
                                          dfm.uuid as folder_id,
                                          um.uuid as user_id,
                                          dfm.folder_name,
                                          IF(dfm.my_design_ids != "",length(dfm.my_design_ids) - length(replace(dfm.my_design_ids, ",", ""))+1,0) as TotalDesign,
                                          dfm.update_time
                                        FROM
                                          design_folder_master as dfm,
                                          user_master as um
                                        WHERE
                                          dfm.user_id=um.id AND
                                          dfm.uuid = ? ', [$folder_id]);

            $response = Response::json(['code' => 200, 'message' => 'Folder name updated successfully.', 'cause' => '', 'data' => json_decode(json_encode($result))]);

        } catch (Exception $e) {
            (new ImageController())->logs('editMyDesignFolder', $e);
            //Log::error("editMyDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    //Unused API
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/removeMyDesignFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="removeMyDesignFolder",
     *        summary="remove MyDesign Folder",
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
     *          in="body",
     *        name="request_body",
     *        description="",
     *
     *   	  @SWG\Schema(
     *          required={"folder_id"},
     *
     *          @SWG\Property(property="folder_id",  type="integer", example=1, description="compulsory when you want to rename folder"),
     *          @SWG\Property(property="design_be_moved",  type="integer", example=0, description="0=delete folder with all designs,1=delete folder and design will be move to out site the folder"),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Folder deleted successfully.","cause":"","data":{"my_design_id":217,"is_limit_exceeded":0}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"A folder with this name does not exist.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=202,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to delete folder.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function removeMyDesignFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['folder_id'], $request)) != '') {
                return $response;
            }

            $folder_id = $request->folder_id;
            $design_be_moved = isset($request->design_be_moved) ? $request->design_be_moved : 0;

            $design_list = DB::select('SELECT my_design_ids FROM design_folder_master WHERE uuid = ?', [$folder_id]);
            if (count($design_list) <= 0) {
                return Response::json(['code' => 201, 'message' => 'A folder with this name does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($design_list[0]->my_design_ids == null) {
                DB::beginTransaction();
                DB::delete('DELETE FROM design_folder_master WHERE uuid = ?', [$folder_id]);
                DB::commit();

            } else {
                $my_design_ids = explode(',', $design_list[0]->my_design_ids);
                if ($design_be_moved == 1) {
                    foreach ($my_design_ids as $design_id) {

                        DB::beginTransaction();
                        DB::update('UPDATE my_design_master SET folder_id = NULL WHERE id = ?', [$design_id]);
                        DB::commit();

                    }

                    DB::beginTransaction();
                    DB::delete('DELETE FROM design_folder_master WHERE uuid = ?', [$folder_id]);
                    DB::commit();

                } else {
                    foreach ($my_design_ids as $design_id) {

                        $old_sample_image = DB::select('SELECT image FROM my_design_master WHERE id = ?', [$design_id]);

                        DB::beginTransaction();
                        DB::update('UPDATE design_folder_master
                                    SET
                                      my_design_ids =
                                      TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$design_id.',", ","))
                                    WHERE
                                      FIND_IN_SET("'.$design_id.'", my_design_ids)');
                        DB::delete('delete from my_design_master where id = ? AND user_id = ?', [$design_id, $user_id]);
                        DB::commit();

                        //delete all resource images(3D, transparent, stock photos)
                        $this->deleteMyDesignIdFromTheList($design_id);
                        //delete sample images
                        (new ImageController())->deleteMyDesign($old_sample_image[0]->image);

                    }

                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Folder deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('removeMyDesignFolder', $e);
            //      Log::error("removeMyDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/removeDesignLists",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="removeDesignLists",
     *        summary="remove MyDesign Folder",
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
     *          in="body",
     *        name="request_body",
     *        description="",
     *
     *   	  @SWG\Schema(
     *          required={""},
     *
     *          @SWG\Property(property="folder_ids",  type="stirng", example="1,2,3", description="compulsory when you want to remove folder"),
     *          @SWG\Property(property="design_ids",  type="stirng", example="1,2,3", description="compulsory when you want to remove design"),
     *          @SWG\Property(property="design_be_moved",  type="integer", example=0, description="0=delete folder with all designs,1=delete folder and design will be move to out site the folder"),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"designs deleted successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"A folder with this name does not exist.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=202,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to delete folder.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function removeDesignLists(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());

            $folder_ids = isset($request->folder_ids) ? $request->folder_ids : '';
            $design_ids = isset($request->design_ids) ? $request->design_ids : '';
            $design_be_moved = isset($request->design_be_moved) ? $request->design_be_moved : 0;

            if ($folder_ids != '') {
                $folder_ids = explode(',', $folder_ids);
                foreach ($folder_ids as $folder_id) {
                    $design_list = DB::select('SELECT my_design_ids FROM design_folder_master WHERE uuid = ?', [$folder_id]);
                    if (count($design_list) <= 0) {
                        return Response::json(['code' => 201, 'message' => 'A folder with this name does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    if ($design_list[0]->my_design_ids == null) {
                        DB::beginTransaction();
                        DB::delete('DELETE FROM design_folder_master WHERE uuid = ?', [$folder_id]);
                        DB::commit();

                    } else {
                        $my_design_ids = explode(',', $design_list[0]->my_design_ids);
                        if ($design_be_moved == 1) {
                            foreach ($my_design_ids as $design_id) {

                                DB::beginTransaction();
                                DB::update('UPDATE my_design_master SET folder_id = NULL WHERE id = ?', [$design_id]);
                                DB::commit();

                            }
                            $image_list = DB::select('SELECT
                                          mdm.uuid as my_design_id,
                                          um.uuid as user_id,
                                          scm.uuid as sub_category_id,
                                          mdm.content_type,
                                          IF(mdm.user_template_name != "",user_template_name,"Untitled Design") as user_template_name,
                                          IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") as sample_image,
                                          coalesce(mdm.color_value,"") AS color_value,
                                          COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          mdm.update_time
                                        FROM
                                          my_design_master as mdm,
                                          sub_category_master as scm,
                                          user_master as um
                                        WHERE
                                           um.id= mdm.user_id AND
                                           mdm.sub_category_id= scm.id AND
                                           mdm.id IN ('.$design_list[0]->my_design_ids.') AND
                                           mdm.folder_id IS NULL AND
                                           mdm.is_active = ?
                                        ORDER BY mdm.update_time DESC', [1]);

                        } else {
                            foreach ($my_design_ids as $design_id) {

                                $old_sample_image = DB::select('SELECT image,json_data,json_file_name FROM my_design_master WHERE id = ?', [$design_id]);

                                DB::beginTransaction();
                                DB::update('UPDATE design_folder_master
                                      SET
                                        my_design_ids =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$design_id.',", ","))
                                      WHERE
                                        FIND_IN_SET("'.$design_id.'", my_design_ids)');
                                DB::delete('delete from my_design_master where id = ? AND user_id = ?', [$design_id, $user_id]);
                                DB::commit();

                                //delete all resource images(3D, transparent, stock photos)
                                $this->deleteMyDesignIdFromTheList($design_id);
                                //delete sample images
                                (new ImageController())->deleteMyDesign($old_sample_image[0]->image);
                                if ($old_sample_image[0]->json_data == null) {
                                    (new ImageController())->deleteJsonData($old_sample_image[0]->json_file_name);
                                }

                            }
                        }
                        DB::beginTransaction();
                        DB::delete('DELETE FROM design_folder_master WHERE uuid = ?', [$folder_id]);
                        DB::commit();
                    }
                }
                if (isset($image_list)) {
                    $response = Response::json(['code' => 200, 'message' => 'Folder deleted successfully.', 'cause' => '', 'data' => ['image_list' => $image_list]]);
                } else {
                    $response = Response::json(['code' => 200, 'message' => 'Folder deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }

            if ($design_ids != '') {
                $design_ids = explode(',', $design_ids);
                foreach ($design_ids as $design_id) {

                    $old_sample_image = DB::select('SELECT image,id,json_data,json_file_name FROM my_design_master WHERE uuid = ?', [$design_id]);
                    if (! $old_sample_image) {
                        Log::error('removeDesignLists : Design does not exist.', ['design_id' => $design_id, 'user_id' => $user_id]);

                        return Response::json(['code' => 201, 'message' => 'Design does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                    $design_int_id = $old_sample_image[0]->id;
                    DB::beginTransaction();
                    DB::update('UPDATE design_folder_master
                                      SET
                                        my_design_ids =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$design_int_id.',", ","))
                                      WHERE
                                        FIND_IN_SET("'.$design_int_id.'", my_design_ids)');
                    DB::delete('delete from my_design_master where uuid = ? AND user_id = ?', [$design_id, $user_id]);
                    DB::commit();

                    //delete all resource images(3D, transparent, stock photos)
                    $this->deleteMyDesignIdFromTheList($old_sample_image[0]->id);
                    //delete sample images
                    (new ImageController())->deleteMyDesign($old_sample_image[0]->image);
                    if ($old_sample_image[0]->json_data == null) {
                        (new ImageController())->deleteJsonData($old_sample_image[0]->json_file_name);
                    }

                }
                $response = Response::json(['code' => 200, 'message' => 'Design deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");

        } catch (Exception $e) {
            (new ImageController())->logs('removeMyDesignFolder', $e);
            //      Log::error("removeMyDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/moveMyDesignInToFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="moveMyDesignInToFolder",
     *        summary="move MyDesign InTo A Folder",
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
     *          in="body",
     *        name="request_body",
     *        description="",
     *
     *   	  @SWG\Schema(
     *          required={"my_design_ids","source_folder_id","des_folder_id"},
     *
     *          @SWG\Property(property="my_design_ids",  type="string", example="1,2,3,4,5", description="compulsory when you want to move the designs"),
     *          @SWG\Property(property="source_folder_id",  type="integer", example=1, description="compulsory when you want to move into folder"),
     *          @SWG\Property(property="des_folder_id",  type="integer", example=2, description="compulsory when you want to move into folder"),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Moved design to the folder Successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"A design already exists in this folder.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=202,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to move design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function moveMyDesignInToFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['my_design_ids'], $request)) != '') {
                return $response;
            }

            $my_design_id = $request->my_design_ids;
            isset($request->des_folder_id) ? $des_folder_id = $request->des_folder_id : '';
            isset($request->source_folder_id) ? $source_folder_id = $request->source_folder_id : '';

            if (isset($my_design_id) && isset($des_folder_id) && isset($source_folder_id)) {
                $existing_design = DB::select('SELECT my_design_ids FROM design_folder_master WHERE uuid = ? AND user_id = ? ', [$des_folder_id, $user_id]);
                $existing_design_ids = $existing_design[0]->my_design_ids;
                $old_id_list = explode(',', $existing_design_ids);
                $new_id_list = explode(',', $my_design_id);

                $already_exist = array_intersect($old_id_list, $new_id_list);
                if (count($already_exist) > 0) {
                    return Response::json(['code' => 201, 'message' => 'Any '.count($already_exist).' design from the design list you provided already exists in this folder.', 'cause' => '', 'data' => json_decode('{}')]);
                }
                foreach ($new_id_list as $id) {
                    $get_design_id = DB::select('SELECT id FROM my_design_master WHERE uuid = ? AND user_id = ? ', [$id, $user_id]);
                    $get_folder_id = DB::select('SELECT id FROM design_folder_master WHERE uuid = ?', [$des_folder_id]);
                    $integer_design_id = $get_design_id[0]->id;
                    $integer_folder_id = $get_folder_id[0]->id;
                    DB::update('UPDATE design_folder_master
                                    SET
                                      my_design_ids =
                                      TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$integer_design_id.',", ","))
                                    WHERE
                                      FIND_IN_SET("'.$integer_design_id.'", my_design_ids) AND uuid = ?', [$source_folder_id]);

                    DB::update('UPDATE my_design_master SET folder_id = ? WHERE id = ?', [$integer_folder_id, $integer_design_id]);

                }
                $id_list_array = [];
                foreach ($new_id_list as $row) {
                    $get_int_id = DB::SELECT('select id from my_design_master where uuid= ?', [$row]);
                    array_push($id_list_array, $get_int_id[0]->id);
                }
                $my_design_id_str = implode(',', $id_list_array);
                $update_time = gmdate('Y-m-d H:i:s');
                $increase_time = gmdate('Y-m-d H:i:s', strtotime('+1 seconds', strtotime($update_time)));

                ($existing_design_ids == null) ? $my_design_ids = $my_design_id_str : $my_design_ids = $existing_design_ids.','.$my_design_id_str;
                DB::beginTransaction();
                DB::update('UPDATE design_folder_master SET my_design_ids = ?, update_time = ? WHERE uuid = ?', [$my_design_ids, $increase_time, $des_folder_id]);
                DB::commit();

                $response = Response::json(['code' => 200, 'message' => 'Design moved successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            } elseif (isset($my_design_id) && isset($des_folder_id)) {

                $existing_design = DB::select('SELECT my_design_ids FROM design_folder_master WHERE uuid = ? AND user_id = ? ', [$des_folder_id, $user_id]);
                $existing_design_ids = $existing_design[0]->my_design_ids;
                $old_id_list = explode(',', $existing_design_ids);
                $new_id_list = explode(',', $my_design_id);

                $already_exist = array_intersect($old_id_list, $new_id_list);
                if (count($already_exist) > 0) {
                    return Response::json(['code' => 201, 'message' => 'Any '.count($already_exist).' design from the design list you provided already exists in this folder.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                foreach ($new_id_list as $id) {
                    $get_folder_id = DB::select('SELECT id FROM design_folder_master WHERE uuid = ?', [$des_folder_id]);
                    $integer_folder_id = $get_folder_id[0]->id;

                    DB::update('UPDATE my_design_master SET folder_id = ? WHERE uuid = ?', [$integer_folder_id, $id]);
                }
                $id_list_array = [];
                foreach ($new_id_list as $row) {
                    $get_int_id = DB::SELECT('select id from my_design_master where uuid= ?', [$row]);
                    array_push($id_list_array, $get_int_id[0]->id);
                }
                $my_design_id_str = implode(',', $id_list_array);
                ($existing_design_ids == null) ? $my_design_ids = $my_design_id_str : $my_design_ids = $existing_design_ids.','.$my_design_id_str;
                DB::beginTransaction();
                DB::update('UPDATE design_folder_master SET my_design_ids = ? WHERE uuid = ?', [$my_design_ids, $des_folder_id]);
                DB::commit();

                $response = Response::json(['code' => 200, 'message' => 'The design has been moved to folder.', 'cause' => '', 'data' => json_decode('{}')]);

            } elseif (isset($my_design_id) && isset($source_folder_id)) {
                $my_design_ids = explode(',', $my_design_id);
                $id_list_array = [];

                foreach ($my_design_ids as $id) {
                    $get_design_id = DB::select('SELECT id FROM my_design_master WHERE uuid = ? AND user_id = ? ', [$id, $user_id]);
                    $integer_design_id = $get_design_id[0]->id;
                    array_push($id_list_array, $get_design_id[0]->id);
                    DB::update('UPDATE design_folder_master
                                    SET
                                      my_design_ids =
                                      TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$integer_design_id.',", ","))
                                    WHERE
                                      FIND_IN_SET("'.$integer_design_id.'", my_design_ids) AND uuid = ?', [$source_folder_id]);
                    DB::update('UPDATE my_design_master SET folder_id = NULL WHERE id = ?', [$integer_design_id]);
                }

                $my_design_id_str = implode(',', $id_list_array);
                $image_list = DB::select('SELECT
                                          mdm.uuid as my_design_id,
                                          um.uuid as user_id,
                                          scm.uuid as sub_category_id,
                                          IF(mdm.user_template_name != "",user_template_name,"Untitled Design") as user_template_name,
                                          IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") as sample_image,
                                          coalesce(mdm.color_value,"") AS color_value,
                                          mdm.update_time
                                        FROM
                                          my_design_master as mdm,
                                          user_master as um,
                                          sub_category_master as scm
                                        WHERE
                                            mdm.user_id = um.id AND
                                            mdm.sub_category_id=scm.id AND
                                            mdm.id IN ('.$my_design_id_str.') AND
                                            mdm.folder_id IS NULL AND
                                            mdm.is_active = ?
                                        ORDER BY mdm.update_time DESC', [1]);

                $response = Response::json(['code' => 200, 'message' => 'Design moved successfully.', 'cause' => '', 'data' => ['image_list' => $image_list]]);
            }

            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");

        } catch (Exception $e) {
            (new ImageController())->logs('moveMyDesignInToFolder', $e);
            //      Log::error("moveMyDesignInToFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'moved design to the folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/copyMyDesignsById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="copyMyDesignsById",
     *        summary="Copy my design by id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 			@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"my_design_id","content_type"},
     *
     *          @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="content_type",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *
     *     @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data": {"my_design_id": 550, "is_limit_exceeded": 0}}),),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function copyMyDesignsById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['my_design_id', 'content_type'], $request)) != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;
            $content_type = $request->content_type;

            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {
                return $response;
            }

            $my_design = DB::select('SELECT
                                      id,
                                      content_id,
                                      sub_category_id,
                                      folder_id,
                                      CONCAT("Copy of ", user_template_name) AS user_template_name,
                                      json_data,
                                      json_file_name,
                                      json_pages_sequence,
                                      is_multipage,
                                      download_json,
                                      image,
                                      color_value
                                  FROM my_design_master
                                      WHERE uuid = ? AND user_id = ?', [$my_design_id, $user_id]);

            $my_design_id = $my_design[0]->id;
            $content_id = $my_design[0]->content_id;
            $sub_category_id = $my_design[0]->sub_category_id;
            $folder_id = $my_design[0]->folder_id;
            $user_template_name = $my_design[0]->user_template_name;
            $download_json = $my_design[0]->download_json;
            $color_value = $my_design[0]->color_value;
            $create_time = date('Y-m-d H:i:s');
            $json_data = $my_design[0]->json_data;
            $json_file_name = $my_design[0]->json_file_name;
            $new_json_file_name = null;
            $pages_sequence = $my_design[0]->json_pages_sequence;
            $is_multipage = $my_design[0]->is_multipage;

            if ($json_data == null) {
                $extension = pathinfo($json_file_name, PATHINFO_EXTENSION);
                $new_json_file_name = uniqid().'_json_data_'.time().'.'.$extension;
                $file_content = file_get_contents(Config::get('constant.JSON_FILE_DIRECTORY_OF_S3').$json_file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    $aws_bucket = Config::get('constant.AWS_BUCKET');
                    $destination_path = "$aws_bucket/json/".$new_json_file_name;
                    $disk = Storage::disk('s3');
                    $value = "$aws_bucket/json/".$json_file_name;
                    if ($disk->exists($value)) {
                        $disk->put($destination_path, $file_content, 'public');
                    } else {
                        Log::info('copyMyDesignsById : Old json file does not exist', [$value]);
                    }

                } else {
                    $destination_path = '../..'.Config::get('constant.JSON_FILE_DIRECTORY').$new_json_file_name;
                    file_put_contents($destination_path, $file_content);
                }

            }

            $image = $my_design[0]->image;
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $new_file_name = uniqid().'_my_design_'.time().'.'.$extension;
            $destination_path = '../..'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY').$new_file_name;

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                $source_path = Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_S3').$image;
                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $destination_path = "$aws_bucket/my_design/".$new_file_name;
                $disk = Storage::disk('s3');
                $value = "$aws_bucket/my_design/".$image;
                if ($disk->exists($value)) {
                    $disk->put($destination_path, file_get_contents($source_path), 'public');
                }

            } else {
                $source_path = '../..'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY').$image;
                copy($source_path, $destination_path);
            }
            $uuid = (new ImageController())->generateUUID();
            DB::beginTransaction();
            $data = ['user_id' => $user_id,
                'uuid' => $uuid,
                'sub_category_id' => $sub_category_id,
                'folder_id' => $folder_id,
                'user_template_name' => ($user_template_name == '') ? 'Untitled Design' : $user_template_name,
                'json_data' => $json_data,
                'json_file_name' => $new_json_file_name,
                'json_pages_sequence' => $pages_sequence,
                'is_multipage' => $is_multipage,
                'download_json' => $download_json,
                'image' => (isset($new_file_name)) ? $new_file_name : '',
                'color_value' => $color_value,
                'content_type' => $content_type,
                'is_active' => 1,
                'create_time' => $create_time,
                'content_id' => $content_id,
            ];

            $new_my_design_id = DB::table('my_design_master')->insertGetId($data);
            $sub_category_uuid = DB::select('SELECT uuid FROM sub_category_master WHERE id = ?', [$sub_category_id]);
            if ($folder_id != '') {
                $existing_designs = DB::select('SELECT my_design_ids FROM design_folder_master WHERE id = ?', [$folder_id]);
                $design_ids = $existing_designs[0]->my_design_ids;
                $my_design_ids = $design_ids.','.$new_my_design_id;
                DB::update('UPDATE design_folder_master SET my_design_ids = ? WHERE id = ?', [$my_design_ids, $folder_id]);
            }

            DB::update("UPDATE
                              my_design_3d_image_master
                              set `my_design_id` = IF(`my_design_id` = '','$new_my_design_id',CONCAT(`my_design_id`, ',', '$new_my_design_id'))
                              WHERE find_in_set('$my_design_id',`my_design_id`)");

            DB::update("UPDATE my_design_transparent_image_master
                              set `my_design_id` = IF(`my_design_id` = '','$new_my_design_id',CONCAT(`my_design_id`, ',', '$new_my_design_id'))
                              WHERE find_in_set('$my_design_id',`my_design_id`)");

            DB::update("UPDATE stock_photos_master
                    set `my_design_ids` = IF(`my_design_ids` = '','$new_my_design_id',CONCAT(`my_design_ids`, ',', '$new_my_design_id'))
                    WHERE find_in_set('$my_design_id',`my_design_ids`)");

            DB::commit();

            $this->increaseMyDesignCount($user_id, $create_time, $content_type);
            $total_pages = strlen($pages_sequence) - strlen(str_replace(',', '', $pages_sequence)) + 1;
            $copied_design_detail = ['image_result' => [
                'content_type' => $content_type,
                'download_json' => $download_json,
                'is_video_user_uploaded' => '',
                'my_design_id' => $uuid,
                'overlay_image' => '',
                'sample_image' => (isset($new_file_name)) ? Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$new_file_name : '',
                'sub_category_id' => $sub_category_uuid[0]->uuid,
                'total_pages' => is_null($total_pages) ? 1 : $total_pages,
                'update_time' => $create_time,
                'user_template_name' => ($user_template_name == '') ? 'Untitled Design' : $user_template_name, ]];

            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");
            $response = Response::json(['code' => 200, 'message' => 'Design copied successfully.', 'cause' => '', 'data' => $copied_design_detail]);
        } catch (Exception $e) {
            (new ImageController())->logs('copyMyDesignsById', $e);
            //      Log::error("copyMyDesignsById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'copy my designs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyDesignFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyDesignFolder",
     *        summary="get My Design Folder list",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
    @SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * @SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
     *        ),
     *
     * @SWG\Response(
     *            response=201,
     *            description="error",
     *
     *     @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getMyDesignFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = Config::get('constant.IMAGE');

            $redis_result = Cache::rememberforever("getMyDesignFolder$this->user_id:$this->page:$this->item_count", function () {

                $get_my_design = $this->getDesign($this->user_id, $this->content_type, $this->offset, $this->item_count);

                return $get_my_design;

            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyDesignFolder', $e);
            //      Log::error("getMyDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyVideoDesignFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyVideoDesignFolder",
     *        summary="get My Video Design Folder list",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
    @SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * @SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
     *        ),
     *
     * @SWG\Response(
     *            response=201,
     *            description="error",
     *
     *     @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getMyVideoDesignFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = Config::get('constant.VIDEO');

            $redis_result = Cache::rememberforever("getMyVideoDesignFolder$this->user_id:$this->page:$this->item_count", function () {

                $get_my_video_design = $this->getDesign($this->user_id, $this->content_type, $this->offset, $this->item_count);

                return $get_my_video_design;

            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'video design fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyVideoDesignFolder', $e);
            //      Log::error("getMyVideoDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyIntroDesignFolder",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyIntroDesignFolder",
     *        summary="get My intro Design Folder list",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
    @SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * @SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
     *        ),
     *
     * @SWG\Response(
     *            response=201,
     *            description="error",
     *
     *     @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getMyIntroDesignFolder(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = Config::get('constant.INTRO_VIDEO');

            $redis_result = Cache::rememberforever("getMyIntroDesignFolder$this->user_id:$this->page:$this->item_count", function () {

                $get_my_intro_design = $this->getDesign($this->user_id, $this->content_type, $this->offset, $this->item_count);

                return $get_my_intro_design;

            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'video design fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyIntroDesignFolder', $e);
            //      Log::error("getMyIntroDesignFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyDesignByFolderId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyDesignByFolderId",
     *        summary="get My Design By Folder_Id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
    @SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * @SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count","folder_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *          @SWG\Property(property="folder_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Designs fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}}}}, description=""),),
     *        ),
     *
     * @SWG\Response(
     *            response=201,
     *            description="error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get design from folder.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getMyDesignByFolderId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'folder_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->folder_id = $request->folder_id;

            $redis_result = Cache::rememberforever("getMyDesignByFolderId$this->user_id:$this->page:$this->item_count:$this->folder_id", function () {

                $get_my_design_folder = $this->getDesignFolder($this->user_id, $this->folder_id, $this->offset, $this->item_count);

                return $get_my_design_folder;

            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Designs fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyDesignByFolderId', $e);
            //      Log::error("getMyDesignByFolderId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design from folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getFolders(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->content_type = $request->content_type;
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::rememberforever("getFolders:$this->user_id:$this->content_type:$this->page:$this->item_count", function () {

                return DB::select('SELECT
                              dfm.uuid AS folder_id,
                              dfm.folder_name,
                              dfm.folder_content_type,
                              dfm.update_time
                            FROM
                              design_folder_master AS dfm
                            WHERE
                              dfm.user_id = ? AND
                              dfm.is_active = 1 AND
                              dfm.folder_content_type = ?
                            ORDER BY dfm.update_time DESC', [$this->user_id, $this->content_type]);
            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Folders fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyDesignByFolderId', $e);
            //      Log::error("getMyDesignByFolderId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get folders.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| My Design |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/saveMyDesign",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="saveMyDesign",
     *        summary="Save my design",
     *        consumes={"multipart/form-data" },
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
     *          description="Give json_data & sub_category_id in json object",
     *
     *         @SWG\Schema(
     *              required={"json_data","sub_category_id"},
     *
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="image of card",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="object_images[]",
     *         in="formData",
     *         description="array of 3D object images",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="stock_photos[]",
     *         in="formData",
     *         description="array of stock photos",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="transparent_images[]",
     *         in="formData",
     *         description="array of transparent images",
     *         required=true,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":{"my_design_id":217,"is_limit_exceeded":0}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=432,
     *            description="Limit exceeded",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":432,"message":"Create design limit exceeded for free plan.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    //Unused API
    public function saveMyDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $json_data = json_encode($request->json_data);
            $sub_category_id = $request->sub_category_id;
            $create_time = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id)) != '') {
                return $response;
            }

            DB::beginTransaction();
            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $image_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);
                $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
                (new ImageController())->saveMyDesign($card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveMyDesignInToS3($card_image);
                }

                $data = ['user_id' => $user_id,
                    'sub_category_id' => $sub_category_id,
                    'json_data' => $json_data,
                    'image' => $card_image,
                    'color_value' => $color_value,
                    'is_active' => 1,
                    'create_time' => $create_time,
                ];

                $my_design_id = DB::table('my_design_master')->insertGetId($data);

                DB::commit();

                if ($request_body->hasFile('object_images')) {
                    $object_images = Input::file('object_images');

                    if (($response = $this->add3DObjectImages($object_images, $my_design_id, $create_time)) != '') {
                        $this->deleteMyDesignImage($card_image, $my_design_id);

                        return $response;
                    }
                }

                //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
                if ($request_body->hasFile('transparent_images')) {
                    $transparent_images = Input::file('transparent_images');

                    if (($response = $this->addTransparentImages($transparent_images, $my_design_id, $create_time)) != '') {
                        $this->deleteMyDesignImage($card_image, $my_design_id);

                        return $response;
                    }
                }

                if ($request_body->hasFile('stock_photos')) {
                    $stock_images = Input::file('stock_photos');

                    if (($response = $this->addStockPhotos($stock_images, $my_design_id, $create_time)) != '') {
                        $this->deleteMyDesignImage($card_image, $my_design_id);

                        return $response;
                    }
                }
            }

            $result_array = ['my_design_id' => $my_design_id, 'is_limit_exceeded' => 0];

            $result = json_decode(json_encode($result_array), true);

            $this->increaseMyDesignCount($user_id, $create_time);

            $response = Response::json(['code' => 200, 'message' => 'Design saved successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('saveMyDesign', $e);
            //      Log::error("saveMyDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/checkLimitExceededToSaveMyDesign",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="checkLimitExceededToSaveMyDesign",
     *        summary="Check limit is exceeded to save my design.",
     *        consumes={"multipart/form-data" },
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="0=limit not exceeded, 1=limit exceeded",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Details fetched successfully.","cause":"","data":{"is_limit_exceeded":0}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function checkLimitExceededToSaveMyDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_type'], $request)) != '') {
                return $response;
            }

            $content_type = $request->content_type;
            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {
                $data = (json_decode(json_encode($response), true));
                $result_array = ['is_limit_exceeded' => 1];
                $message = $data['original']['message'];
            } else {
                $result_array = ['is_limit_exceeded' => 0];
                $message = '';
            }

            $result = json_decode(json_encode($result_array), true);
            $response = Response::json(['code' => 200, 'message' => $message, 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('checkLimitExceededToSaveMyDesign', $e);
            //      Log::error("checkLimitExceededToSaveMyDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'check limit exceeded or not.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateMyDesign",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateMyDesign",
     *        summary="Update my design",
     *        consumes={"multipart/form-data" },
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
     *          description="Give my_design_id json_data, sample_image, deleted_object_images & deleted_transparent_images in json object",
     *
     *         @SWG\Schema(
     *              required={"my_design_id","sub_category_id","json_data"},
     *
     *              @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sample_image",  type="string", example={}, description="sampel image name"),
     *              @SWG\Property(property="deleted_object_images",  type="string", example={}, description="deleted 3d-object images"),
     *              @SWG\Property(property="deleted_transparent_images",  type="string", example={}, description="deleted transparent images"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of card",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="object_images[]",
     *         in="formData",
     *         description="array of 3D object images",
     *         required=false,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="transparent_images[]",
     *         in="formData",
     *         description="array of transparent images",
     *         required=false,
     *         type="file",
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    //Unused API
    public function updateMyDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'my_design_id', 'sub_category_id', 'sample_image', 'user_template_name'], $request)) != '') {
                return $response;
            }

            $response = (new VerificationController())->validateRequiredArrayParameter(['deleted_object_images', 'deleted_transparent_images'], $request);
            if ($response != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;
            $sub_category_id = $request->sub_category_id;
            $json_data = json_encode($request->json_data);
            $stock_photos_id_list = $request->stock_photos_id_list;
            $sample_image = $request->sample_image;
            $deleted_object_images = $request->deleted_object_images;
            $deleted_transparent_images = $request->deleted_transparent_images;
            $user_template_name = $request->user_template_name;

            if (count($stock_photos_id_list) > 0) {
                $this->removeMyDesignIdFromTheList($stock_photos_id_list, $my_design_id);
            }

            if (count($deleted_object_images) > 0) {
                $this->removeMyDesignIdFromThe3dImageList($deleted_object_images, $my_design_id);
            }

            if (count($deleted_transparent_images) > 0) {
                $this->removeMyDesignIdFromTheTransparentImageList($deleted_transparent_images, $my_design_id);
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $image_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);

                $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
                (new ImageController())->saveMyDesign($card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveMyDesignInToS3($card_image);
                }

                DB::beginTransaction();
                DB::update('UPDATE
                          my_design_master SET
                          sub_category_id = ?,
                          json_data = ?,
                          image = ?,
                          user_template_name=?,
                          color_value = ? WHERE id = ? AND user_id = ?', [$sub_category_id, $json_data, $card_image, $user_template_name, $color_value, $my_design_id, $user_id]);

                DB::commit();

                if ($request_body->hasFile('object_images')) {
                    $object_images = Input::file('object_images');

                    if (($response = $this->edit3DObjectImages($object_images, $my_design_id)) != '') {
                        return $response;
                    }
                }

                //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
                if ($request_body->hasFile('transparent_images')) {
                    $transparent_images = Input::file('transparent_images');

                    if (($response = $this->editTransparentImages($transparent_images, $my_design_id)) != '') {
                        return $response;
                    }
                }

                if ($request_body->hasFile('stock_photos')) {
                    $stock_images = Input::file('stock_photos');

                    $create_time = date('Y-m-d H:i:s');
                    if (($response = $this->addStockPhotos($stock_images, $my_design_id, $create_time)) != '') {
                        return $response;
                    }
                }

                //delete unused sample images
                (new ImageController())->deleteMyDesign($sample_image);
                //$this->deleteUnusedImages($sample_image, $deleted_object_images, $deleted_transparent_images);
            }

            $response = Response::json(['code' => 200, 'message' => 'Design saved successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateMyDesign', $e);
            //      Log::error("updateMyDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyDesigns",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyDesigns",
     *        summary="Get my designs",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    //Unused API
    public function getMyDesigns(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getMyDesigns$this->user_id:$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getMyDesigns$this->user_id:$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT count(id) as total FROM my_design_master WHERE user_id = ? AND is_active = ?', [$this->user_id, 1]);
                    $total_row = $total_row_result[0]->total;
                    $default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES');

                    $image_list = DB::select('SELECT
                                          id as my_design_id,
                                          user_id,
                                          sub_category_id,
                                          coalesce(download_json,"") AS download_json,
                                          IF(user_template_name != "",user_template_name,"Untitled Design") as user_template_name,
                                          IF(image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",image),"") as sample_image,
                                          IF(overlay_image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",overlay_image),"") as overlay_image,
                                          coalesce(video_name,"") AS video_name,
                                          coalesce(is_video_user_uploaded,"") AS is_video_user_uploaded,
                                          coalesce(color_value,"") AS color_value,
                                          content_type,
                                          update_time
                                        FROM
                                          my_design_master
                                        WHERE
                                          user_id = ? AND
                                          is_active = ?
                                        ORDER BY update_time DESC
                                        LIMIT ?,?', [$this->user_id, 1, $this->offset, $this->item_count]);

                    /*foreach($image_list as $key)
                      {
                          $object_image_list = DB::select('SELECT
                                          id as my_design_3d_image_id,
                                          IF(image != "",CONCAT("' . Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as object_image
                                        FROM
                                          my_design_3d_image_master
                                        WHERE
                                          my_design_id = ? AND
                                          is_active = ?
                                        ORDER BY update_time DESC', [$key->my_design_id, 1]);

                                  $key->object_image_list = $object_image_list;
                              }*/
                    if (count($image_list) > 0) {
                        $recommended_templates = [];
                        $rec_sub_category_ids = DB::select('SELECT
                                                                id as my_design_id,
                                                                sub_category_id
                                                              FROM
                                                                my_design_master
                                                              WHERE
                                                                user_id = ? AND
                                                                is_active = ?
                                                              ORDER BY update_time DESC', [$this->user_id, 1]);

                        foreach ($rec_sub_category_ids as $key) {

                            $recommended_templates = DB::select('SELECT
                                                          cm.id as content_id,
                                                          sct.sub_category_id,
                                                          cm.catalog_id,
                                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                          coalesce(cm.is_featured,"") as is_featured,
                                                          cm.content_type,
                                                          coalesce(cm.is_free,0) as is_free,
                                                          coalesce(cm.is_portrait,0) as is_portrait,
                                                          coalesce(cm.height,0) as height,
                                                          coalesce(cm.width,0) as width,
                                                          coalesce(cm.color_value,"") AS color_value,
                                                          cm.update_time
                                                        FROM
                                                          content_master as cm,
                                                          sub_category_catalog as sct,
                                                          catalog_master as ct
                                                        WHERE
                                                          sct.sub_category_id = ? AND
                                                          sct.catalog_id=cm.catalog_id AND
                                                          sct.catalog_id=ct.id AND
                                                          sct.is_active=1 AND
                                                          ct.is_featured = 1 AND
                                                          cm.content_type = ?
                                                          ORDER BY cm.update_time DESC LIMIT ?, ?', [$key->sub_category_id, 4, 0, 10]);
                            if (count($recommended_templates) > 0) {
                                break;
                            }

                        }

                        if (count($recommended_templates) <= 0) {
                            $recommended_templates = DB::select('SELECT
                                                          cm.id as content_id,
                                                          sct.sub_category_id,
                                                          cm.catalog_id,
                                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                          coalesce(cm.is_featured,"") as is_featured,
                                                          cm.content_type,
                                                          coalesce(cm.is_free,0) as is_free,
                                                          coalesce(cm.is_portrait,0) as is_portrait,
                                                          coalesce(cm.height,0) as height,
                                                          coalesce(cm.width,0) as width,
                                                          coalesce(cm.color_value,"") AS color_value,
                                                          cm.update_time
                                                        FROM
                                                          content_master as cm,
                                                          sub_category_catalog as sct,
                                                          catalog_master as ct
                                                        WHERE
                                                          sct.sub_category_id = ? AND
                                                          sct.catalog_id=cm.catalog_id AND
                                                          sct.catalog_id=ct.id AND
                                                          sct.is_active=1 AND
                                                          ct.is_featured = 1 AND
                                                          cm.content_type = ?
                                                          ORDER BY cm.update_time DESC LIMIT ?, ?', [$default_sub_category_id, 4, 0, 10]);
                        }

                    } else {
                        $recommended_templates = DB::select('SELECT
                                                          cm.id as content_id,
                                                          sct.sub_category_id,
                                                          cm.catalog_id,
                                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                          coalesce(cm.is_featured,"") as is_featured,
                                                          cm.content_type,
                                                          coalesce(cm.is_free,0) as is_free,
                                                          coalesce(cm.is_portrait,0) as is_portrait,
                                                          coalesce(cm.height,0) as height,
                                                          coalesce(cm.width,0) as width,
                                                          coalesce(cm.color_value,"") AS color_value,
                                                          cm.update_time
                                                        FROM
                                                          content_master as cm,
                                                          sub_category_catalog as sct,
                                                          catalog_master as ct
                                                        WHERE
                                                          sct.sub_category_id = ? AND
                                                          sct.catalog_id=cm.catalog_id AND
                                                          sct.catalog_id=ct.id AND
                                                          sct.is_active=1 AND
                                                          ct.is_featured = 1 AND
                                                          cm.content_type = ?
                                                          ORDER BY cm.update_time DESC LIMIT ?, ?', [$default_sub_category_id, 4, 0, 10]);

                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $image_list, 'recommended_templates' => $recommended_templates];

                });
            }

            $redis_result = Cache::get("getMyDesigns$this->user_id:$this->page:$this->item_count");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyDesigns', $e);
            //      Log::error("getMyDesigns : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get designs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getContentDetailOfMyDesignById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getContentDetailOfMyDesignById",
     *        summary="Get content detail of my design by id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"my_design_id"},
     *
     *          @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getContentDetailOfMyDesignById(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['my_design_id'], $request)) != '') {
                return $response;
            }

            $this->my_design_id = $request->my_design_id;

            $redis_result = Cache::rememberforever("getContentDetailOfMyDesignById:$this->my_design_id:$this->user_id", function () {

                //Log::info($this->content_id.' 7 '. $this->content_type);
                $result = DB::select('SELECT
                                            IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") AS sample_image,
                                            COALESCE(mdm.json_data,"") AS json_data,
                                            COALESCE(mdm.json_file_name,"") AS json_file_name,
                                            COALESCE(mdm.json_pages_sequence,"") AS pages_sequence,
                                            IF(mdm.content_type = 3,mdm.download_json,"") AS download_json,
                                            COALESCE(mdm.user_template_name,"Untitled Design") AS user_template_name,
                                            mdm.color_value,
                                            COALESCE(dfm.uuid,"") AS folder_id
                                        FROM
                                            my_design_master AS mdm
                                            LEFT JOIN design_folder_master AS dfm ON dfm.id = mdm.folder_id
                                        WHERE
                                            mdm.uuid = ? AND
                                            mdm.user_id = ?
                                        ORDER BY mdm.update_time DESC', [$this->my_design_id, $this->user_id]);

                if (count($result) > 0) {

                    if ($result[0]->json_data == null) {
                        if ($result[0]->json_file_name != null) {
                            $result[0]->json_data = json_decode(file_get_contents(Config::get('constant.JSON_FILE_DIRECTORY_OF_S3').$result[0]->json_file_name));
                        } else {
                            Log::error('getContentDetailOfMyDesignById : json_data is missing');

                            return [];
                        }
                    } else {
                        $result[0]->json_data = json_decode($result[0]->json_data);
                    }
                    unset($result[0]->json_file_name);

                    if ($result[0]->pages_sequence != '') {
                        $pages_sequence = $result[0]->pages_sequence;
                        $result[0]->pages_sequence = explode(',', $pages_sequence);
                    }

                    if ($result[0]->download_json != '') {
                        $result[0]->download_json = json_decode($result[0]->download_json);
                    }

                    return $result[0];
                } else {
                    Log::error('getContentDetailOfMyDesignById : Requested design not found.', ['my_design_id' => $this->my_design_id, 'user_id' => $this->user_id]);

                    return [];
                }

            });

            if (! $redis_result) {
                $response = Response::json(['code' => 201, 'message' => 'Requested design not found, Please try another template.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getContentDetailOfMyDesignById', $e);
            //Log::error("getContentDetailOfMyDesignById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteMyDesignsById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteMyDesignsById",
     *        summary="Delete my design by id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"my_design_id"},
     *
     *          @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function deleteMyDesignsById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['my_design_id'], $request)) != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;
            //info('my_design_id',[$my_design_id]);

            $old_sample_image = DB::select('SELECT image, id, json_data, json_file_name FROM my_design_master WHERE uuid = ?', [$my_design_id]);
            if (! $old_sample_image) {
                Log::error('deleteMyDesignsById : Design does not exist.', ['design_id' => $my_design_id, 'user_id' => $user_id]);

                return Response::json(['code' => 201, 'message' => 'Design does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $my_design_int_id = $old_sample_image[0]->id;

            DB::beginTransaction();
            DB::delete('delete from my_design_master where uuid = ? AND user_id = ?', [$my_design_id, $user_id]);
            DB::update('UPDATE design_folder_master
                                      SET
                                        my_design_ids =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$my_design_int_id.',", ","))
                                      WHERE
                                        FIND_IN_SET("'.$my_design_int_id.'", my_design_ids)');
            DB::commit();

            $this->deleteMyDesignIdFromTheList($old_sample_image[0]->id);
            //delete unused sample images
            (new ImageController())->deleteMyDesign($old_sample_image[0]->image);
            if ($old_sample_image[0]->json_data == null) {
                (new ImageController())->deleteJsonData($old_sample_image[0]->json_file_name);
            }
            //$this->deleteMyDesignImages($my_design_id);
            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");

            $response = Response::json(['code' => 200, 'message' => 'Design deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyDesignsById', $e);
            //      Log::error("deleteMyDesignsById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete my designs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //    public function copyMyDesignsById(Request $request_body)
    //    {
    //        try {
    //
    //            $token = JWTAuth::getToken();
    //            JWTAuth::toUser($token);
    //            $user_id = JWTAuth::toUser($token)->id;
    //
    //            $request = json_decode($request_body->getContent());
    //            if (($response = (new VerificationController())->validateRequiredParameter(array('my_design_id'), $request)) != '')
    //                return $response;
    //
    //            $my_design_id = $request->my_design_id;
    //
    //            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id)) != '')
    //            return $response;
    //
    //
    //          $my_design = DB::select('SELECT
    //                                      sub_category_id,
    //                                      user_template_name,
    //                                      json_data,
    //                                      image,
    //                                      color_value
    //                                      FROM my_design_master
    //                                      WHERE id = ? AND user_id = ?',
    //                                      [$my_design_id, $user_id]);
    //
    //
    //            $sub_category_id = $my_design[0]->sub_category_id;
    //            $user_template_name = $my_design[0]->user_template_name;
    //            $json_data = $my_design[0]->json_data;
    //            $color_value = $my_design[0]->color_value;
    //            $create_time = date('Y-m-d H:i:s');
    //
    //            $image = $my_design[0]->image;
    //
    //          $extension = pathinfo($image, PATHINFO_EXTENSION);
    //          $new_file_name = uniqid() . '_my_design_' . time() . '.' . $extension;
    //          $destination_path = '../..' . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY') . $new_file_name;
    //
    //          if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
    //
    //            $source_path = Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image;
    //            $aws_bucket = Config::get('constant.AWS_BUCKET');
    //            $destination_path = "$aws_bucket/my_design/" . $new_file_name;
    //            $disk = Storage::disk('s3');
    //            $value = "$aws_bucket/my_design/" . $image;
    //            if ($disk->exists($value)) {
    //              $disk->put($destination_path, file_get_contents($source_path), 'public');
    //            }
    //
    //          }else {
    //            $source_path = '../..' . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY') . $image;
    //            copy($source_path, $destination_path);
    //          }
    //
    //            DB::beginTransaction();
    //            $data = array('user_id' => $user_id,
    //              'sub_category_id' => $sub_category_id,
    //              'user_template_name' => $user_template_name,
    //              'json_data' => $json_data,
    //              'image' => (isset($new_file_name))?$new_file_name:'' ,
    //              'color_value' => $color_value,
    //              'is_active' => 1,
    //              'create_time' => $create_time
    //            );
    //            $new_my_design_id = DB::table('my_design_master')->insertGetId($data);
    //
    //            DB::update("UPDATE
    //                              my_design_3d_image_master
    //                              set `my_design_id` = IF(`my_design_id` = '','$new_my_design_id',CONCAT(`my_design_id`, ',', '$new_my_design_id'))
    //                              WHERE find_in_set('$my_design_id',`my_design_id`)");
    //
    //            DB::update("UPDATE my_design_transparent_image_master
    //                              set `my_design_id` = IF(`my_design_id` = '','$new_my_design_id',CONCAT(`my_design_id`, ',', '$new_my_design_id'))
    //                              WHERE find_in_set('$my_design_id',`my_design_id`)");
    //
    //            DB::update("UPDATE stock_photos_master
    //                    set `my_design_ids` = IF(`my_design_ids` = '','$new_my_design_id',CONCAT(`my_design_ids`, ',', '$new_my_design_id'))
    //                    WHERE find_in_set('$my_design_id',`my_design_ids`)");
    //
    //            DB::commit();
    //
    //            $image_list = DB::select('SELECT
    //                                        id as my_design_id,
    //                                        user_id,
    //                                        sub_category_id,
    //                                        user_template_name,
    //                                        IF(image != "",CONCAT("' . Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as sample_image,
    //                                        coalesce(color_value,"") AS color_value,
    //                                        update_time FROM my_design_master WHERE id = ?',[$new_my_design_id]);
    //
    //            $result_array = array('result' => $image_list, 'is_limit_exceeded' => 0);
    //
    //            $result = json_decode(json_encode($result_array), true);
    //
    //            $this->increaseMyDesignCount($user_id, $create_time);
    //
    //            $response = Response::json(array('code' => 200, 'message' => 'Design copied successfully.', 'cause' => '', 'data' => $result));
    //        } catch (Exception $e) {
    //            Log::error("copyMyDesignsById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    //            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'copy my designs.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    //            DB::rollBack();
    //        }
    //        return $response;
    //     }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/renameMyDesignNameById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="renameMyDesignNameById",
     *        summary="rename My Design template Name By Id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 			@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"my_design_id","user_template_name"},
     *
     *          @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="user_template_name",  type="string", example="my family", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *
     *     @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design name updated successfully.","cause":"","data": {"my_design_id": 550, "is_limit_exceeded": 0}}),),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function renameMyDesignNameById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['my_design_id', 'user_template_name'], $request)) != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;
            $user_template_name = (new ImageController())->removeEmoji($request->user_template_name);
            if (trim($user_template_name) == '') {
                return Response::json(['code' => 201, 'message' => 'Please enter valid design name.', 'cause' => '', 'data' => '']);
            }
            if (strlen($user_template_name) > 100) {
                return Response::json(['code' => 201, 'message' => 'The length of your design-name is too long.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            DB::update('UPDATE my_design_master SET user_template_name = ? WHERE uuid = ?', [$user_template_name, $my_design_id]);
            DB::commit();

            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");
            $response = Response::json(['code' => 200, 'message' => 'Design name updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('renameMyDesignNameById', $e);
            //      Log::error("renameMyDesignNameById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update design name.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Save module |=============================*/
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/saveMyTemplate",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="saveMyTemplate",
     *        summary="Save my design",
     *        consumes={"multipart/form-data" },
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
     *          description="Give json_data & sub_category_id in json object",
     *
     *         @SWG\Schema(
     *              required={"json_data","sub_category_id"},
     *
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="user_template_name",  type="string", example="my family", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="image of card",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="object_images[]",
     *         in="formData",
     *         description="array of 3D object images",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="stock_photos[]",
     *         in="formData",
     *         description="array of stock photos",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="transparent_images[]",
     *         in="formData",
     *         description="array of transparent images",
     *         required=true,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":{"my_design_id":217,"template_img_name":"5bc1d0c76bb81_my_design_1539428551.jpg","is_limit_exceeded":0}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=432,
     *            description="Limit exceeded",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":432,"message":"Create design limit exceeded for free plan.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function saveMyTemplate(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $json_data = json_encode($request->json_data);
            $sub_category_id = $request->sub_category_id;
            $content_type = isset($request->content_type) ? $request->content_type : 1;
            $user_template_name = isset($request->user_template_name) ? (new ImageController())->removeEmoji($request->user_template_name) : 'Untitled Design';
            if (trim($user_template_name) == '') {
                $user_template_name = 'Untitled Design';
            }
            $create_time = date('Y-m-d H:i:s');
            $video_name = null;
            $is_video_user_uploaded = null;
            $download_json = null;
            $overlay_image = null;

            if (isset($request->content_id)) {
                $content_id = DB::select('SELECT id FROM content_master WHERE uuid=?', [$request->content_id]);
                if (count($content_id) > 0) {
                    $content_id = $content_id[0]->id;
                }
            } else {
                $content_id = null;
            }

            if (is_numeric($sub_category_id)) {
                $sub_category_detail = DB::select('SELECT
                                             uuid
                                           FROM
                                             sub_category_master
                                           WHERE
                                            id =?', [$sub_category_id]);
                $sub_category_id = $sub_category_detail[0]->uuid;
            }

            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {
                return $response;
            }

            if (strlen($user_template_name) > 100) {
                return Response::json(['code' => 201, 'message' => 'The length of your design-name is too long.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $image_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                //        $image_array = Input::file('file');
                //        if (($response = (new ImageController())->verifyImage($image_array)) != '')
                //          return $response;

                if ($content_type == Config::get('constant.VIDEO')) {
                    if (($response = (new VerificationController())->validateRequiredParameter(['video_name', 'download_json'], $request)) != '') {
                        return $response;
                    }

                    $video_name = $request->video_name;
                    $download_json = json_encode($request->download_json);
                    $is_video_user_uploaded = $request->download_json->is_video_user_uploaded;

                    if (! $request_body->hasFile('overlay_image')) {
                        return Response::json(['code' => 201, 'message' => 'Required field overlay_image file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                    } else {

                        $overlay_image_array = Input::file('overlay_image');
                        //            if (($response = (new ImageController())->verifyImage($overlay_image_array)) != '')
                        //              return $response;

                        if (($response = (new UserVerificationController())->verifyImage($overlay_image_array)) != '') {
                            return $response;
                        }

                        $overlay_image = (new ImageController())->generateNewFileName('overlay_image', $overlay_image_array);
                        (new ImageController())->saveMyDesignWithFileArray($overlay_image, $overlay_image_array);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveMyDesignInToS3($overlay_image);
                        }

                    }

                }

                if ($content_type == Config::get('constant.INTRO_VIDEO')) {
                    $json_data = $request->json_data;
                    $logo_json_list = isset($json_data->logo_json_list) ? $json_data->logo_json_list : [];
                    $audio_array = $json_data->audio_json;
                    if (($response = (new VerificationController())->validateRequiredParameter(['download_json'], $request)) != '') {
                        return $response;
                    }

                    $download_json = $request->download_json;
                    if (($response = (new VerificationController())->validateRequiredParameter(['quality'], $download_json)) != '') {
                        return $response;
                    }

                    if (count($audio_array) > 0) {
                        if (($response = (new VerificationController())->validateRequiredParameter(['is_audio_user_uploaded', 'is_audio_repeat', 'is_audio_trim', 'audio_duration'], $download_json)) != '') {
                            return $response;
                        }
                    }

                    if ($request_body->hasFile('crop_image1')) {

                        $is_image_user_uploaded = $logo_json_list[0]->is_image_user_uploaded;
                        $crop_image_array = Input::file('crop_image1');
                        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
                            return $response;
                        }

                        $crop_image_name = $crop_image_array->getClientOriginalName();
                        $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image1');
                    }
                    if ($request_body->hasFile('crop_image2')) {

                        $is_image_user_uploaded = $logo_json_list[1]->is_image_user_uploaded;
                        $crop_image_array = Input::file('crop_image2');
                        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
                            return $response;
                        }

                        $crop_image_name = $crop_image_array->getClientOriginalName();
                        $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image2');
                    }
                    if ($request_body->hasFile('crop_image3')) {

                        $is_image_user_uploaded = $logo_json_list[2]->is_image_user_uploaded;
                        $crop_image_array = Input::file('crop_image3');
                        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
                            return $response;
                        }

                        $crop_image_name = $crop_image_array->getClientOriginalName();
                        $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image3');
                    }

                    $download_json = json_encode($request->download_json);
                    $json_data = json_encode($request->json_data);
                }

                $color_value = (new ImageController())->getRandomColor($image_array);
                $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
                (new ImageController())->saveMyDesign($card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveMyDesignInToS3($card_image);
                }

                $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
                if (count($sub_category_detail) <= 0) {
                    return Response::json(['code' => 201, 'message' => 'Sub category does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $sub_category_id = $sub_category_detail[0]->id;
                $uuid = (new ImageController())->generateUUID();

                $data = ['user_id' => $user_id,
                    'uuid' => $uuid,
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
                    'content_id' => $content_id,
                ];

                $my_design_id = DB::table('my_design_master')->insertGetId($data);
                DB::commit();

                if ($request_body->hasFile('object_images')) {
                    $object_images = Input::file('object_images');

                    if (($response = $this->add3DObjectImages($object_images, $my_design_id, $create_time)) != '') {
                        $this->deleteMyDesignImage($card_image, $my_design_id);

                        return $response;
                    }
                }

                //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
                if ($request_body->hasFile('transparent_images')) {
                    $transparent_images = Input::file('transparent_images');

                    if (($response = $this->addTransparentImages($transparent_images, $my_design_id, $create_time)) != '') {
                        $this->deleteMyDesignImage($card_image, $my_design_id);

                        return $response;
                    }
                }

                if ($request_body->hasFile('stock_photos')) {
                    $stock_images = Input::file('stock_photos');

                    if (($response = $this->addStockPhotos($stock_images, $my_design_id, $create_time)) != '') {
                        $this->deleteMyDesignImage($card_image, $my_design_id);

                        return $response;
                    }
                }
            }

            $image_list = DB::select('SELECT
                                        mdm.uuid as my_design_id,
                                        um.uuid as user_id,
                                        scm.uuid as sub_category_id,
                                        mdm.user_template_name,
                                        IF(mdm.overlay_image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.overlay_image),"") as overlay_image,
                                        IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") as sample_image,
                                        coalesce(mdm.color_value,"") AS color_value,
                                        mdm.update_time
                                   FROM
                                       my_design_master as mdm,
                                       user_master as um,
                                       sub_category_master as scm
                                   WHERE
                                    mdm.user_id = um.id AND
                                    mdm.sub_category_id=scm.id AND
                                    mdm.id = ?', [$my_design_id]);

            $result_array = ['result' => $image_list, 'is_limit_exceeded' => 0];

            //     $result_array = array('my_design_id' => $my_design_id, 'is_limit_exceeded' => 0);

            $result = json_decode(json_encode($result_array), true);

            $this->increaseMyDesignCount($user_id, $create_time, $content_type);

            $response = Response::json(['code' => 200, 'message' => 'Design saved successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('saveMyTemplate', $e);
            //      Log::error("saveMyTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function saveTemplateWithLimitCheck(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $user_uuid = $user_detail->uuid;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            //verify all files here which comes in request
            if (($response = (new ImageController())->validateAllFilesToCreateDesign()) != '') {
                Log::error('saveTemplateWithLimitCheck : File did not verified successfully. ', [$response]);

                return $response;
            }

            $json_data = json_encode($request->json_data);
            $sub_category_uuid = $sub_category_id = $request->sub_category_id;
            $content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.IMAGE');
            $user_template_name = isset($request->user_template_name) ? (new ImageController())->removeEmoji($request->user_template_name) : 'Untitled Design';
            if (trim($user_template_name) == '') {
                $user_template_name = 'Untitled Design';
            }
            $create_time = date('Y-m-d H:i:s');
            $video_name = null;
            $is_video_user_uploaded = null;
            $download_json = null;
            $overlay_image = null;
            $content_id = null;
            $error_msg = null;
            $error_code = null;
            $is_active = 1;
            $deleted_file_list = [];       //stores file_name & file_path which we have upload in s3 if any exception error occurs then get all file_list & delete one by one
            $folder_id = isset($request->folder_id) ? $request->folder_id : '';

            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {

                $error_msg = json_decode(json_encode($response))->original->message;
                $error_code = json_decode(json_encode($response))->original->code;
                $is_active = 0;

                $is_exist = DB::select('SELECT id,image FROM my_design_master WHERE user_id = ? AND is_active = ?', [$user_id, $is_active]);
                if (count($is_exist) > 0) {
                    $old_design_id = $is_exist[0]->id;
                    $old_image_name = $is_exist[0]->image;

                    $this->deleteMyDesignIdFromTheList($old_design_id);
                    //delete unused sample images
                    (new ImageController())->deleteMyDesign($old_image_name);
                    DB::delete('DELETE FROM my_design_master WHERE user_id = ? AND is_active = ?', [$user_id, $is_active]);
                }

            }

            if (strlen($user_template_name) > 100) {
                $user_template_name = substr($user_template_name, 0, 100);
            }

            //check sample image is arrived or not in request, if not then print error log & return error message with 201 code
            if (! $request_body->hasFile('file')) {
                Log::error('saveTemplateWithLimitCheck : Required field file is missing or empty. ');

                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //get id from uuid & check this id is exist in our database if not then take default id & print error log
            $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_uuid]);
            if (count($sub_category_detail) <= 0) {
                Log::error('saveTemplateWithLimitCheck : Sub category does not exist.', [$sub_category_uuid]);
                $sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES');
            } else {
                $sub_category_id = $sub_category_detail[0]->id;
            }

            if (isset($request->content_id)) {
                $content_id = DB::select('SELECT id FROM content_master WHERE uuid=?', [$request->content_id]);
                if (count($content_id) > 0) {
                    $content_id = $content_id[0]->id;
                }
            }

            DB::beginTransaction();
            if ($content_type == Config::get('constant.VIDEO')) {
                if (($response = (new VerificationController())->validateRequiredParameter(['video_name', 'download_json'], $request)) != '') {
                    return $response;
                }

                $video_name = $request->video_name;
                $download_json = json_encode($request->download_json);
                $is_video_user_uploaded = $request->download_json->is_video_user_uploaded;

                if (! $request_body->hasFile('overlay_image')) {
                    Log::error('saveTemplateWithLimitCheck : Required field overlay_image file is missing or empty.');

                    return Response::json(['code' => 201, 'message' => 'Required field overlay_image file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $overlay_image_array = Input::file('overlay_image');

                $overlay_image = (new ImageController())->generateNewFileName('overlay_image', $overlay_image_array);
                (new ImageController())->saveMyDesignWithFileArray($overlay_image, $overlay_image_array);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveMyDesignInToS3($overlay_image);
                }

                //if any exception occurs then delete all files which we have upload previously that's why we stored name & path to this variable
                $image_detail['name'] = $overlay_image;
                $image_detail['path'] = 'my_design';
                array_push($deleted_file_list, $image_detail);

                $this->deleteAllRedisKeys("getMyVideoDesignFolder$user_id");
                $this->deleteAllRedisKeys("getVideoDesignFolderForAdmin$user_id");
            }

            if ($content_type == Config::get('constant.INTRO_VIDEO')) {
                $json_data = $request->json_data;
                $logo_json_list = isset($json_data->logo_json_list) ? $json_data->logo_json_list : [];
                $audio_array = $json_data->audio_json;

                if (($response = (new VerificationController())->validateRequiredParameter(['download_json'], $request)) != '') {
                    return $response;
                }

                $download_json = $request->download_json;
                if (($response = (new VerificationController())->validateRequiredParameter(['quality'], $download_json)) != '') {
                    return $response;
                }

                if (count($audio_array) > 0) {
                    if (($response = (new VerificationController())->validateRequiredParameter(['is_audio_user_uploaded', 'is_audio_repeat', 'is_audio_trim', 'audio_duration'], $download_json)) != '') {
                        return $response;
                    }
                }

                if ($request_body->hasFile('crop_image1')) {

                    $is_image_user_uploaded = $logo_json_list[0]->is_image_user_uploaded;
                    $crop_image_array = Input::file('crop_image1');

                    $crop_image_name = $crop_image_array->getClientOriginalName();
                    $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image1');
                }
                if ($request_body->hasFile('crop_image2')) {

                    $is_image_user_uploaded = $logo_json_list[1]->is_image_user_uploaded;
                    $crop_image_array = Input::file('crop_image2');

                    $crop_image_name = $crop_image_array->getClientOriginalName();
                    $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image2');
                }
                if ($request_body->hasFile('crop_image3')) {

                    $is_image_user_uploaded = $logo_json_list[2]->is_image_user_uploaded;
                    $crop_image_array = Input::file('crop_image3');

                    $crop_image_name = $crop_image_array->getClientOriginalName();
                    $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image3');
                }

                $download_json = json_encode($request->download_json);
                $json_data = json_encode($request->json_data);

                $this->deleteAllRedisKeys("getMyIntroDesignFolder$user_id");
                $this->deleteAllRedisKeys("getIntroDesignFolderForAdmin$user_id");
            }

            $image_array = Input::file('file');

            //generate random color for this image & if not generated then put default value
            $color_value = (new ImageController())->getRandomColor($image_array);
            if (! $color_value) {
                Log::error('saveTemplateWithLimitCheck : Color value is empty.', [$color_value]);
                $color_value = Config::get('constant.DEFAULT_RANDOM_COLOR_VALUE');
            }

            $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
            (new ImageController())->saveMyDesign($card_image);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveMyDesignInToS3($card_image);
            }

            //if any exception occurs then delete all files which we have upload previously that's why we stored name & path to this variable
            $image_detail['name'] = $card_image;
            $image_detail['path'] = 'my_design';
            array_push($deleted_file_list, $image_detail);

            $uuid = (new ImageController())->generateUUID();

            $data = ['user_id' => $user_id,
                'uuid' => $uuid,
                'sub_category_id' => $sub_category_id,
                'json_data' => $json_data,
                'download_json' => $download_json,
                'image' => $card_image,
                'overlay_image' => $overlay_image,
                'video_name' => $video_name,
                'is_video_user_uploaded' => $is_video_user_uploaded,
                'color_value' => $color_value,
                'content_type' => $content_type,
                'is_active' => $is_active,
                'create_time' => $create_time,
                'user_template_name' => $user_template_name,
                'content_id' => $content_id,
            ];

            $my_design_id = DB::table('my_design_master')->insertGetId($data);

            if ($request_body->hasFile('object_images')) {
                $object_images = Input::file('object_images');

                foreach ($object_images as $object_image) {

                    $object_image_name = $object_image->getClientOriginalName();
                    $response = $this->add3DObjectImagesV2($object_image, $object_image_name, $my_design_id, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
            if ($request_body->hasFile('transparent_images')) {
                $transparent_images = Input::file('transparent_images');

                foreach ($transparent_images as $transparent_image) {

                    $transparent_image_name = $transparent_image->getClientOriginalName();
                    $response = $this->addTransparentImagesV2($transparent_image, $transparent_image_name, $my_design_id, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            if ($request_body->hasFile('stock_photos')) {
                $stock_images = Input::file('stock_photos');

                $response = $this->addStockPhotosV2($stock_images, $my_design_id, $create_time, $deleted_file_list);

                $deleted_file_list = $response['data'];
                if ($response['code'] != 200) {
                    (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                    return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                }

            }

            if ($folder_id) {
                $move_status_msg = $this->moveToFolder($user_id, $folder_id, $my_design_id);
            }
            DB::commit();

            $image_list = [['my_design_id' => $uuid, 'folder_id' => $folder_id, 'user_id' => $user_uuid, 'sub_category_id' => $sub_category_uuid, 'sample_image' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$card_image, 'overlay_image' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$overlay_image]];
            $result_array = ['result' => $image_list, 'is_limit_exceeded' => 0];
            $result = json_decode(json_encode($result_array), true);

            if ($is_active) {
                $this->increaseMyDesignCount($user_id, $create_time, $content_type);
            }

            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");
            if ($content_type == Config::get('constant.IMAGE')) {
                $this->deleteAllRedisKeys("getMyDesignFolder$user_id");
            }

            if ($error_msg && $error_code) {
                $response = Response::json(['code' => $error_code, 'message' => $error_msg, 'cause' => '', 'data' => $result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => isset($move_status_msg) ? $move_status_msg : 'Design saved successfully.', 'cause' => '', 'data' => $result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('saveTemplateWithLimitCheck', $e);
            $deleted_file_list = isset($deleted_file_list) ? $deleted_file_list : [];
            (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
            //Log::error("saveTemplateWithLimitCheck : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateTemplate",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateTemplate",
     *        summary="Update my template",
     *        consumes={"multipart/form-data" },
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
     *          description="Give my_design_id, sub_category_id, user_template_name, json_data, sample_image, deleted_object_images & deleted_transparent_images in json object",
     *
     *         @SWG\Schema(
     *              required={"my_design_id","sub_category_id","json_data"},
     *
     *              @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="user_template_name",  type="string", example="my family", description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sample_image",  type="string", example={}, description="sampel image name"),
     *              @SWG\Property(property="deleted_object_images",  type="string", example={}, description="deleted 3d-object images"),
     *              @SWG\Property(property="deleted_transparent_images",  type="string", example={}, description="deleted transparent images"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of card",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="object_images[]",
     *         in="formData",
     *         description="array of 3D object images",
     *         required=false,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="transparent_images[]",
     *         in="formData",
     *         description="array of transparent images",
     *         required=false,
     *         type="file",
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":{"my_design_id":217,"template_img_name":"5bc1d0c76bb81_my_design_1539428551.jpg"}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function updateTemplate(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'my_design_id', 'sub_category_id', 'sample_image', 'user_template_name'], $request)) != '') {
                return $response;
            }

            $response = (new VerificationController())->validateRequiredArrayParameter(['deleted_object_images', 'deleted_transparent_images'], $request);
            if ($response != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;
            $sub_category_id = $request->sub_category_id;
            $content_type = isset($request->content_type) ? $request->content_type : 1;
            $user_template_name = (new ImageController())->removeEmoji($request->user_template_name);
            if (trim($user_template_name) == '') {
                return Response::json(['code' => 201, 'message' => 'Please enter valid design name.', 'cause' => '', 'data' => '']);
            }
            $json_data = json_encode($request->json_data);
            $stock_photos_id_list = $request->stock_photos_id_list;
            $sample_image = $request->sample_image;
            $deleted_overlay_image = isset($request->deleted_overlay_image) ? $request->deleted_overlay_image : '';
            $deleted_object_images = $request->deleted_object_images;
            $deleted_transparent_images = $request->deleted_transparent_images;
            $download_json = '';
            $is_video_user_uploaded = '';
            $video_name = '';
            $overlay_image = null;
            $is_active = isset($request->is_active) ? $request->is_active : 1;
            $des_folder_id = isset($request->des_folder_id) ? $request->des_folder_id : '';
            $source_folder_id = isset($request->source_folder_id) ? $request->source_folder_id : '';

            if (is_numeric($sub_category_id)) {
                $sub_category_detail = DB::select('SELECT
                                             uuid
                                           FROM
                                             sub_category_master
                                           WHERE
                                            id =?', [$sub_category_id]);
                $sub_category_id = $sub_category_detail[0]->uuid;
            }

            $my_design_detail = DB::SELECT('SELECT id from my_design_master WHERE uuid = ? AND is_active = ? ', [$my_design_id, $is_active]);
            if (count($my_design_detail) <= 0) {
                Log::error('updateTemplate : Design does not exist.', [$my_design_id]);

                return Response::json(['code' => 201, 'message' => 'Design does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $my_design_id_int = $my_design_detail[0]->id;

            if (strlen($user_template_name) > 100) {
                return Response::json(['code' => 201, 'message' => 'The length of your design-name is too long.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($stock_photos_id_list) >= 0) {
                $this->removeMyDesignIdFromTheList($stock_photos_id_list, $my_design_id_int);
            }

            if (count($deleted_object_images) > 0) {
                $this->removeMyDesignIdFromThe3dImageList($deleted_object_images, $my_design_id_int);
            }

            if (count($deleted_transparent_images) > 0) {
                $this->removeMyDesignIdFromTheTransparentImageList($deleted_transparent_images, $my_design_id_int);
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $image_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);

                $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
                (new ImageController())->saveMyDesign($card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveMyDesignInToS3($card_image);
                }

                if ($content_type == Config::get('constant.VIDEO')) {
                    if (! $request_body->hasFile('overlay_image')) {
                        return Response::json(['code' => 201, 'message' => 'Required field overlay_image file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                    } else {

                        $overlay_image_array = Input::file('overlay_image');

                        if (($response = (new UserVerificationController())->verifyImage($overlay_image_array)) != '') {
                            return $response;
                        }

                        $overlay_image = (new ImageController())->generateNewFileName('overlay_image', $overlay_image_array);
                        (new ImageController())->saveMyDesignWithFileArray($overlay_image, $overlay_image_array);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveMyDesignInToS3($overlay_image);
                        }

                    }

                    if (($response = (new VerificationController())->validateRequiredParameter(['video_name', 'download_json'], $request)) != '') {
                        return $response;
                    }
                    $download_json = json_encode($request->download_json);
                    $is_video_user_uploaded = $request->download_json->is_video_user_uploaded;
                    $video_name = $request->video_name;
                }

                $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
                if (count($sub_category_detail) <= 0) {
                    return Response::json(['code' => 201, 'message' => 'Sub category does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $sub_category_id = $sub_category_detail[0]->id;

                DB::beginTransaction();
                DB::update('UPDATE
                                my_design_master SET
                                sub_category_id = ?,
                                user_template_name = ?,
                                json_data = ?,
                                image = ?,
                                overlay_image =?,
                                content_type=?,
                                video_name=IF(? !="",?,video_name),
                                download_json=IF(? !="",?,download_json),
                                is_video_user_uploaded=IF(? !="",?,is_video_user_uploaded),
                                color_value = ? WHERE uuid = ? AND user_id = ?', [$sub_category_id, $user_template_name, $json_data, $card_image, $overlay_image, $content_type, $video_name, $video_name, $download_json, $download_json, $is_video_user_uploaded, $is_video_user_uploaded, $color_value, $my_design_id, $user_id]);

                DB::delete('DELETE FROM image_details WHERE name = ? ', [$sample_image]);
                DB::commit();

                if ($request_body->hasFile('object_images')) {
                    $object_images = Input::file('object_images');

                    if (($response = $this->edit3DObjectImages($object_images, $my_design_id_int)) != '') {
                        return $response;
                    }
                }

                //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
                if ($request_body->hasFile('transparent_images')) {
                    $transparent_images = Input::file('transparent_images');

                    if (($response = $this->editTransparentImages($transparent_images, $my_design_id_int)) != '') {
                        return $response;
                    }
                }

                if ($request_body->hasFile('stock_photos')) {
                    $stock_images = Input::file('stock_photos');

                    $create_time = date('Y-m-d H:i:s');
                    if (($response = $this->addStockPhotos($stock_images, $my_design_id_int, $create_time)) != '') {
                        return $response;
                    }
                }

                //delete unused sample images
                (new ImageController())->deleteMyDesign($sample_image);

                //delete old overlay image
                if ($deleted_overlay_image) {
                    (new ImageController())->deleteMyDesign($deleted_overlay_image);
                }

                //$this->deleteUnusedImages($sample_image, $deleted_object_images, $deleted_transparent_images);
            }
            $move_status_msg = $this->moveToFolder($user_id, $des_folder_id, $my_design_id_int, $source_folder_id);
            $overlay_image_url = Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$overlay_image;
            $result_array = ['my_design_id' => $my_design_id, 'folder_id' => $des_folder_id, 'template_img_name' => $card_image, 'overlay_image' => $overlay_image_url];
            $response = Response::json(['code' => 200, 'message' => isset($move_status_msg) ? $move_status_msg : 'Design saved successfully.', 'cause' => '', 'data' => $result_array]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateTemplate', $e);
            //      Log::error("updateTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateIntrosTemplate",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateIntrosTemplate",
     *        summary="Update intors template",
     *        consumes={"multipart/form-data" },
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
     *          description="Give my_design_id, sub_category_id, user_template_name, json_data, sample_image in json object",
     *
     *         @SWG\Schema(
     *              required={"my_design_id","sub_category_id","json_data","is_image_user_uploaded","user_template_name","sample_image"},
     *
     *              @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="user_template_name",  type="string", example="my family", description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sample_image",  type="string", example={}, description="sampel image name"),
     *              @SWG\Property(property="is_image_user_uploaded",  type="string", example={}, description="0=collection,1user_upload,2=resource"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of card",
     *         required=true,
     *         type="file"
     *     ),

     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":{"my_design_id":217,"template_img_name":"5bc1d0c76bb81_my_design_1539428551.jpg"}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function updateIntrosTemplate(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'my_design_id', 'sub_category_id', 'sample_image', 'user_template_name'], $request)) != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;
            $sub_category_id = $request->sub_category_id;
            $content_type = isset($request->content_type) ? $request->content_type : 1;
            $user_template_name = (new ImageController())->removeEmoji($request->user_template_name);
            if (trim($user_template_name) == '') {
                return Response::json(['code' => 201, 'message' => 'Please enter valid design name.', 'cause' => '', 'data' => '']);
            }
            $json_data = json_encode($request->json_data);
            $sample_image = $request->sample_image;
            $deleted_crop_image = isset($request->deleted_crop_image) ? $request->deleted_crop_image : [];
            $download_json = '';
            $is_video_user_uploaded = '';
            $video_name = '';
            $overlay_image = null;
            $is_active = isset($request->is_active) ? $request->is_active : 1;

            if (strlen($user_template_name) > 100) {
                return Response::json(['code' => 201, 'message' => 'The length of your design-name is too long.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($deleted_crop_image) > 0) {
                $this->removeUnUsedCropImages($deleted_crop_image);
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $my_design_detail = DB::SELECT('SELECT id from my_design_master WHERE uuid = ? AND is_active = ? ', [$my_design_id, $is_active]);
                if (count($my_design_detail) <= 0) {
                    Log::error('updateIntrosTemplate : Design does not exist.', ['design_id' => $my_design_id, 'user_id' => $user_id]);

                    return Response::json(['code' => 201, 'message' => 'Design does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
                }
                $my_design_id_int = $my_design_detail[0]->id;

                if (is_numeric($sub_category_id)) {
                    $sub_category_detail = DB::select('SELECT
                                             uuid
                                           FROM
                                             sub_category_master
                                           WHERE
                                            id =?', [$sub_category_id]);
                    $sub_category_id = $sub_category_detail[0]->uuid;
                }
                $image_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);

                $card_image = (new ImageController())->generateNewFileName('my_design', $image_array);
                (new ImageController())->saveMyDesign($card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveMyDesignInToS3($card_image);
                }

                if ($content_type == Config::get('constant.INTRO_VIDEO')) {
                    $json_data = $request->json_data;
                    $logo_json_list = isset($json_data->logo_json_list) ? $json_data->logo_json_list : [];
                    $audio_array = $json_data->audio_json;

                    if (($response = (new VerificationController())->validateRequiredParameter(['download_json'], $request)) != '') {
                        return $response;
                    }

                    $download_json = $request->download_json;
                    if (($response = (new VerificationController())->validateRequiredParameter(['quality'], $download_json)) != '') {
                        return $response;
                    }

                    if (count($audio_array) > 0) {
                        if (($response = (new VerificationController())->validateRequiredParameter(['is_audio_user_uploaded', 'is_audio_repeat', 'is_audio_trim', 'audio_duration'], $download_json)) != '') {
                            return $response;
                        }
                    }

                    if ($request_body->hasFile('crop_image1')) {

                        $is_image_user_uploaded = $logo_json_list[0]->is_image_user_uploaded;
                        $crop_image_array = Input::file('crop_image1');
                        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
                            return $response;
                        }

                        $crop_image_name = $crop_image_array->getClientOriginalName();
                        $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image1');
                    }
                    if ($request_body->hasFile('crop_image2')) {

                        $is_image_user_uploaded = $logo_json_list[1]->is_image_user_uploaded;
                        $crop_image_array = Input::file('crop_image2');
                        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
                            return $response;
                        }

                        $crop_image_name = $crop_image_array->getClientOriginalName();
                        $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image2');
                    }
                    if ($request_body->hasFile('crop_image3')) {

                        $is_image_user_uploaded = $logo_json_list[2]->is_image_user_uploaded;
                        $crop_image_array = Input::file('crop_image3');
                        if (($response = (new UserVerificationController())->verifyImage($crop_image_array)) != '') {
                            return $response;
                        }

                        $crop_image_name = $crop_image_array->getClientOriginalName();
                        $this->storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, 'crop_image3');
                    }

                    $download_json = json_encode($request->download_json);
                    $json_data = json_encode($request->json_data);
                }

                $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
                if (count($sub_category_detail) <= 0) {
                    return Response::json(['code' => 201, 'message' => 'Sub category does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $sub_category_id = $sub_category_detail[0]->id;

                DB::beginTransaction();
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

            }
            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");

            $overlay_image_url = Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$card_image;
            $result_array = ['my_design_id' => $my_design_id, 'template_img_name' => $card_image, 'overlay_image' => $overlay_image_url];
            $response = Response::json(['code' => 200, 'message' => 'Design saved successfully.', 'cause' => '', 'data' => $result_array]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateIntrosTemplate', $e);
            //      Log::error("updateIntrosTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| MultiPage Save module for Image Card |=============================*/
    /*
    Purpose : for save user's multi-page design
    Description : This method compulsory take 3 argument as parameter.(if any argument is optional then define it here)
    Return : return user_design_id, sample_image_name, sub_category_id & user_id if success otherwise error with specific status code
    */
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/saveMyTemplateV2",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="saveMyTemplateV2",
     *        summary="Save my design",
     *        consumes={"multipart/form-data" },
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
     *          description="Give json_data & sub_category_id in json object",
     *
     *         @SWG\Schema(
     *              required={"json_data","sub_category_id"},
     *
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="user_template_name",  type="string", example="my family", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="image of card",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="object_images[]",
     *         in="formData",
     *         description="array of 3D object images",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="stock_photos[]",
     *         in="formData",
     *         description="array of stock photos",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="transparent_images[]",
     *         in="formData",
     *         description="array of transparent images",
     *         required=true,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":{"my_design_id":217,"template_img_name":"5bc1d0c76bb81_my_design_1539428551.jpg","is_limit_exceeded":0}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=432,
     *            description="Limit exceeded",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":432,"message":"Create design limit exceeded for free plan.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function saveMyTemplateV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $user_uuid = $user_detail->uuid;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['pages_sequence'], $request)) != '') {
                return $response;
            }

            //verify all files here which comes in request
            if (($response = (new ImageController())->validateAllFilesToCreateDesign()) != '') {
                Log::error('saveMyTemplateV2 : File did not verified successfully. ', [$response]);

                return $response;
            }

            $json_data = json_encode($request->json_data);
            $pages_sequence = implode(',', $request->pages_sequence);        //pages_sequence that manages or sequence or sorts multi-page index wise
            $sub_category_uuid = $request->sub_category_id;
            $content_type = isset($request->content_type) ? $request->content_type : 1;
            $user_template_name = isset($request->user_template_name) ? (new ImageController())->removeEmoji($request->user_template_name) : 'Untitled Design';
            if (trim($user_template_name) == '') {
                $user_template_name = 'Untitled Design';
            }
            $create_time = date('Y-m-d H:i:s');
            $deleted_file_list = [];       //stores file_name & file_path which we have upload in s3 if any exception error occurs then get all file_list & delete one by one

            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {
                return $response;
            }

            if (strlen($user_template_name) > 100) {
                $user_template_name = substr($user_template_name, 0, 100);
            }

            //get id from uuid & check this id is exist in our database if not then take default id & print error log
            $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_uuid]);
            if (count($sub_category_detail) <= 0) {
                Log::error('saveMyTemplateV2 : Sub category does not exist.  ', [$sub_category_uuid]);
                $sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES');
            } else {
                $sub_category_id = $sub_category_detail[0]->id;
            }

            //check sample image is arrived or not in request, if not then print error log & return error message with 201 code
            if (! $request_body->hasFile('file')) {
                Log::error('saveMyTemplateV2 : Required field file is missing or empty. ');

                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $image_array = Input::file('file');

            //generate random color for this image & if not generated then put default value
            $color_value = (new ImageController())->getRandomColor($image_array);
            if (! $color_value) {
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
            $image_detail['path'] = 'my_design';
            array_push($deleted_file_list, $image_detail);

            $uuid = (new ImageController())->generateUUID();

            DB::beginTransaction();
            $data = ['user_id' => $user_id,
                'uuid' => $uuid,
                'sub_category_id' => $sub_category_id,
                'json_data' => $json_data,
                'json_pages_sequence' => $pages_sequence,
                'image' => $card_image,
                'color_value' => $color_value,
                'content_type' => $content_type,
                'is_active' => 1,
                'is_multipage' => 1,
                'create_time' => $create_time,
                'user_template_name' => $user_template_name,
            ];

            $my_design_id = DB::table('my_design_master')->insertGetId($data);

            if ($request_body->hasFile('object_images')) {
                $object_images = Input::file('object_images');

                foreach ($object_images as $object_image) {

                    $object_image_name = $object_image->getClientOriginalName();
                    $response = $this->add3DObjectImagesV2($object_image, $object_image_name, $my_design_id, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
            if ($request_body->hasFile('transparent_images')) {
                $transparent_images = Input::file('transparent_images');

                foreach ($transparent_images as $transparent_image) {

                    $transparent_image_name = $transparent_image->getClientOriginalName();
                    $response = $this->addTransparentImagesV2($transparent_image, $transparent_image_name, $my_design_id, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            if ($request_body->hasFile('stock_photos')) {
                $stock_images = Input::file('stock_photos');

                $response = $this->addStockPhotosV2($stock_images, $my_design_id, $create_time, $deleted_file_list);

                $deleted_file_list = $response['data'];
                if ($response['code'] != 200) {
                    (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                    return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                }

            }

            DB::commit();

            $image_list = [['my_design_id' => $uuid, 'user_id' => $user_uuid, 'sub_category_id' => $sub_category_uuid, 'sample_image' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$card_image]];

            $result_array = ['result' => $image_list, 'is_limit_exceeded' => 0];

            $result = json_decode(json_encode($result_array), true);

            $this->increaseMyDesignCount($user_id, $create_time, $content_type);

            $response = Response::json(['code' => 200, 'message' => 'Design saved successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            Log::error('saveMyTemplateV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $deleted_file_list = isset($deleted_file_list) ? $deleted_file_list : [];
            (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function saveMultiPageTemplateWithLimitCheck(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $user_uuid = $user_detail->uuid;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['pages_sequence'], $request)) != '') {
                return $response;
            }

            //verify all files here which comes in request
            if (($response = (new ImageController())->validateAllFilesToCreateDesign()) != '') {
                Log::error('saveMultiPageTemplateWithLimitCheck : File did not verified successfully. ', [$response]);

                return $response;
            }

            $json_data = json_encode($request->json_data);
            $pages_sequence = implode(',', $request->pages_sequence);        //pages_sequence that manages or sequence or sorts multi-page index wise
            $sub_category_uuid = $request->sub_category_id;
            $content_type = isset($request->content_type) ? $request->content_type : 1;
            $user_template_name = substr($request->user_template_name, 0, 100);
            $create_time = date('Y-m-d H:i:s');
            $deleted_file_list = [];       //stores file_name & file_path which we have upload in s3 if any exception error occurs then get all file_list & delete one by one
            $is_active = 1;
            $error_code = null;
            $error_msg = null;
            $folder_id = isset($request->folder_id) ? $request->folder_id : '';

            if (($response = (new VerificationController())->validateUserToCreateDesign($user_id, $content_type)) != '') {

                $error_msg = json_decode(json_encode($response))->original->message;
                $error_code = json_decode(json_encode($response))->original->code;
                $is_active = 0;

                $is_exist = DB::select('SELECT id,image FROM my_design_master WHERE user_id = ? AND is_active = ?', [$user_id, $is_active]);
                if (count($is_exist) > 0) {
                    $old_design_id = $is_exist[0]->id;
                    $old_image_name = $is_exist[0]->image;

                    $this->deleteMyDesignIdFromTheList($old_design_id);
                    //delete unused sample images
                    (new ImageController())->deleteMyDesign($old_image_name);
                    DB::delete('DELETE FROM my_design_master WHERE user_id = ? AND is_active = ?', [$user_id, $is_active]);
                }

            }

            //get id from uuid & check this id is exist in our database if not then take default id & print error log
            $sub_category_detail = DB::SELECT('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_uuid]);
            if (count($sub_category_detail) <= 0) {
                Log::error('saveMultiPageTemplateWithLimitCheck : Sub category does not exist.  ', [$sub_category_uuid]);
                $sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES');
            } else {
                $sub_category_id = $sub_category_detail[0]->id;
            }

            //check sample image is arrived or not in request, if not then print error log & return error message with 201 code
            if (! $request_body->hasFile('file')) {
                Log::error('saveMultiPageTemplateWithLimitCheck : Required field file is missing or empty. ');

                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $image_array = Input::file('file');

            //generate random color for this image & if not generated then put default value
            $color_value = (new ImageController())->getRandomColor($image_array);
            if (! $color_value) {
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
            $image_detail['path'] = 'my_design';
            array_push($deleted_file_list, $image_detail);

            $uuid = (new ImageController())->generateUUID();

            DB::beginTransaction();
            $data = ['user_id' => $user_id,
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
            ];

            $my_design_id = DB::table('my_design_master')->insertGetId($data);

            if ($request_body->hasFile('object_images')) {
                $object_images = Input::file('object_images');

                foreach ($object_images as $object_image) {

                    $object_image_name = $object_image->getClientOriginalName();
                    $response = $this->add3DObjectImagesV2($object_image, $object_image_name, $my_design_id, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
            if ($request_body->hasFile('transparent_images')) {
                $transparent_images = Input::file('transparent_images');

                foreach ($transparent_images as $transparent_image) {

                    $transparent_image_name = $transparent_image->getClientOriginalName();
                    $response = $this->addTransparentImagesV2($transparent_image, $transparent_image_name, $my_design_id, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            if ($request_body->hasFile('stock_photos')) {
                $stock_images = Input::file('stock_photos');

                $response = $this->addStockPhotosV2($stock_images, $my_design_id, $create_time, $deleted_file_list);

                $deleted_file_list = $response['data'];
                if ($response['code'] != 200) {
                    (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                    return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }

            if ($folder_id) {
                $move_status_msg = $this->moveToFolder($user_id, $folder_id, $my_design_id);
            }
            DB::commit();

            $image_list = [['my_design_id' => $uuid, 'folder_id' => $folder_id, 'user_id' => $user_uuid, 'sub_category_id' => $sub_category_uuid, 'sample_image' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$card_image]];
            $result_array = ['result' => $image_list, 'is_limit_exceeded' => 0];
            $result = json_decode(json_encode($result_array), true);

            if ($is_active) {
                $this->increaseMyDesignCount($user_id, $create_time, $content_type);
            }

            if ($error_msg && $error_code) {
                $response = Response::json(['code' => $error_code, 'message' => $error_msg, 'cause' => '', 'data' => $result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => isset($move_status_msg) ? $move_status_msg : 'Design saved successfully.', 'cause' => '', 'data' => $result]);
            }

            $this->deleteAllRedisKeys("getMyDesignFolder$user_id");
            $this->deleteAllRedisKeys("getDesignFolderForAdmin$user_id");
            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");

        } catch (Exception $e) {
            Log::error('saveMultiPageTemplateWithLimitCheck : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $deleted_file_list = isset($deleted_file_list) ? $deleted_file_list : [];
            (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /*
    Purpose : for update user's multi-page design
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here)
    Return : return user_design_id & sample_image_name if success otherwise error with specific status code
    */
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateTemplateV2",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateTemplateV2",
     *        summary="Update my template",
     *        consumes={"multipart/form-data" },
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
     *          description="Give my_design_id, sub_category_id, user_template_name, json_data, sample_image, deleted_object_images & deleted_transparent_images in json object",
     *
     *         @SWG\Schema(
     *              required={"my_design_id","sub_category_id","json_data"},
     *
     *              @SWG\Property(property="my_design_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="user_template_name",  type="string", example="my family", description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="sample_image",  type="string", example={}, description="sampel image name"),
     *              @SWG\Property(property="deleted_object_images",  type="string", example={}, description="deleted 3d-object images"),
     *              @SWG\Property(property="deleted_transparent_images",  type="string", example={}, description="deleted transparent images"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of card",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="object_images[]",
     *         in="formData",
     *         description="array of 3D object images",
     *         required=false,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="transparent_images[]",
     *         in="formData",
     *         description="array of transparent images",
     *         required=false,
     *         type="file",
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Design saved successfully.","cause":"","data":{"my_design_id":217,"template_img_name":"5bc1d0c76bb81_my_design_1539428551.jpg"}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function updateTemplateV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'my_design_id', 'user_template_name', 'is_stock_photos_deleted'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredArrayParameter(['deleted_pages', 'deleted_object_images', 'deleted_transparent_images'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['pages_sequence'], $request)) != '') {
                return $response;
            }

            //verify all files here which comes in request
            if (($response = (new ImageController())->validateAllFilesToCreateDesign()) != '') {
                Log::error('updateTemplateV2 : File did not verified successfully. ', [$response]);

                return $response;
            }

            $my_design_id = $request->my_design_id;
            $user_template_name = (new ImageController())->removeEmoji($request->user_template_name);
            if (trim($user_template_name) == '') {
                return Response::json(['code' => 201, 'message' => 'Please enter valid design name.', 'cause' => '', 'data' => '']);
            }
            $edited_json_content = $request->json_data;
            $is_stock_photos_deleted = $request->is_stock_photos_deleted;
            $pages_sequence = implode(',', $request->pages_sequence);
            $stock_photos_id_list = $request->stock_photos_id_list;
            $deleted_pages = $request->deleted_pages;
            $deleted_object_images = $request->deleted_object_images;
            $deleted_transparent_images = $request->deleted_transparent_images;
            $user_template_name = substr($request->user_template_name, 0, 100);
            $color_value = '';
            $create_time = date('Y-m-d H:i:s');
            $is_active = isset($request->is_active) ? $request->is_active : 1;
            $deleted_file_list = [];       //stores file_name & file_path which we have upload in s3 if any exception error occurs then get all file_list & delete one by one
            $des_folder_id = isset($request->des_folder_id) ? $request->des_folder_id : '';
            $source_folder_id = isset($request->source_folder_id) ? $request->source_folder_id : '';

            //get all design detail from my_design_id & assign this detail to variable. If design does not exist then return error message & print log
            $my_design_detail = DB::SELECT('SELECT id, json_data, json_file_name, is_multipage, image from my_design_master WHERE uuid = ? AND is_active = ? ', [$my_design_id, $is_active]);
            if (count($my_design_detail) <= 0) {
                Log::error('updateTemplateV2 : Design does not exist.', [$my_design_id]);

                return Response::json(['code' => 201, 'message' => 'Design does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $my_design_id_int = $my_design_detail[0]->id;
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
                if (! $color_value) {
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
                $image_detail['path'] = 'my_design';
                array_push($deleted_file_list, $image_detail);

            }

            //check this design is old or new
            if ($is_multipage) {

                //check json_data is moved to s3, if yes then get json_data from s3
                if ($json_data == null) {
                    $file_name = Config::get('constant.JSON_FILE_DIRECTORY_OF_S3').$json_file_name;
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

            DB::beginTransaction();
            DB::update('UPDATE
                        my_design_master SET
                        user_template_name = ?,
                        json_data = ?,
                        json_pages_sequence = ?,
                        is_multipage = ?,
                        image = ?,
                        color_value=IF(? != "",?,color_value)
                    WHERE uuid = ? AND user_id = ?', [$user_template_name, json_encode($json_data), $pages_sequence, $is_multipage, $card_image, $color_value, $color_value, $my_design_id, $user_id]);

            if ($request_body->hasFile('object_images')) {
                $object_images = Input::file('object_images');

                foreach ($object_images as $object_image) {

                    $object_image_name = $object_image->getClientOriginalName();
                    $response = $this->add3DObjectImagesV2($object_image, $object_image_name, $my_design_id_int, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            //transparent_images is an images which comes from stickers, user uploads & stock photos when user removes backgrounds from images
            if ($request_body->hasFile('transparent_images')) {
                $transparent_images = Input::file('transparent_images');

                foreach ($transparent_images as $transparent_image) {

                    $transparent_image_name = $transparent_image->getClientOriginalName();
                    $response = $this->addTransparentImagesV2($transparent_image, $transparent_image_name, $my_design_id_int, $create_time, $deleted_file_list);

                    $deleted_file_list = $response['data'];
                    if ($response['code'] != 200) {
                        (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                        return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                }
            }

            if ($request_body->hasFile('stock_photos')) {
                $stock_images = Input::file('stock_photos');

                $response = $this->addStockPhotosV2($stock_images, $my_design_id_int, $create_time, $deleted_file_list);

                $deleted_file_list = $response['data'];
                if ($response['code'] != 200) {
                    (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);

                    return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save design.', 'cause' => '', 'data' => json_decode('{}')]);
                }

            }

            if (count($stock_photos_id_list) >= 0 && $is_stock_photos_deleted) {
                $this->removeMyDesignIdFromTheList($stock_photos_id_list, $my_design_id_int);
            }

            if (count($deleted_object_images) > 0) {
                $this->removeMyDesignIdFromThe3dImageList($deleted_object_images, $my_design_id_int);
            }

            if (count($deleted_transparent_images) > 0) {
                $this->removeMyDesignIdFromTheTransparentImageList($deleted_transparent_images, $my_design_id_int);
            }

            $move_status_msg = $this->moveToFolder($user_id, $des_folder_id, $my_design_id_int, $source_folder_id);
            DB::commit();

            $this->deleteAllRedisKeys("getRecentMyDesign:$user_id");

            $result_array = ['my_design_id' => $my_design_id, 'folder_id' => $des_folder_id, 'template_img_name' => $card_image];
            $response = Response::json(['code' => 200, 'message' => isset($move_status_msg) ? $move_status_msg : 'Design saved successfully.', 'cause' => '', 'data' => $result_array]);

        } catch (Exception $e) {
            Log::error('updateTemplateV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $deleted_file_list = isset($deleted_file_list) ? $deleted_file_list : [];
            (new ImageController())->deleteAllFilesUsedInDesign($deleted_file_list);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Feedback |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/setBillingAddress",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="setBillingAddress",
     *        summary="Set Billing Address",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *        description="Billing Info",
     *
     *   	  @SWG\Schema(
     *
     *          @SWG\Property(property="full_name",  type="string", example="Will Turner", description="optional"),
     *          @SWG\Property(property="address",  type="string", example="135/B,Test,Test.", description="optional"),
     *          @SWG\Property(property="country",  type="string", example="India", description="optional"),
     *          @SWG\Property(property="state",  type="string", example="Gujarat", description="optional"),
     *          @SWG\Property(property="city",  type="string", example="Surat", description="optional"),
     *          @SWG\Property(property="zip_code",  type="string", example="123456", description="optional"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function setBillingAddress(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request->getContent());
            /*if (($response = (new VerificationController())->validateRequiredParameter(array(), $request)) != '')
                      return $response;*/

            $full_name = isset($request->full_name) ? (new ImageController())->removeEmoji($request->full_name) : null;
            if (trim($full_name) == '') {
                $full_name = null;
            }
            $address = isset($request->address) ? (new ImageController())->removeEmoji($request->address) : null;
            if (trim($address) == '') {
                $address = null;
            }
            $country = isset($request->country) ? $request->country : null;
            $state = isset($request->state) ? (new ImageController())->removeEmoji($request->state) : null;
            if (trim($state) == '') {
                $state = null;
            }
            $city = isset($request->city) ? (new ImageController())->removeEmoji($request->city) : null;
            if (trim($city) == '') {
                $city = null;
            }
            $zip_code = isset($request->zip_code) ? $request->zip_code : null;

            $create_time = date('Y-m-d H:i:s');

            if ($country != null) {
                if (($country_code = (new UserVerificationController())->getCountryCode($country)) == '') {
                    return Response::json(['code' => 201, 'message' => 'Unable to set billing address.', 'cause' => '', 'data' => json_decode('{}')]);
                }
                //        Log::info('$country_code :',[$country_code]);
            }
            if (isset($country_code)) {

                DB::beginTransaction();

                if (($response = (new VerificationController())->checkIsBillingInfoAdded($user_id)) != 0) {

                    DB::update('UPDATE billing_master SET
                              full_name = ?,
                              address = ?,
                              country = ?,
                              attribute1 = ?,
                              state = ?,
                              city = ?,
                              zip_code = ?
                              WHERE
                              user_id = ?', [$full_name, $address, $country, $country_code, $state, $city, $zip_code, $user_id]);
                } else {
                    $uuid = (new ImageController())->generateUUID();

                    DB::insert('insert into billing_master (
                              user_id,
                              uuid,
                              full_name,
                              address,
                              country,
                              attribute1,
                              state,
                              city,
                              zip_code,
                              is_active,
                              create_time)
                              VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)', [$user_id, $uuid, $full_name, $address, $country, $country_code, $state, $city, $zip_code, 1, $create_time]);
                }

                DB::commit();
            } else {

                DB::beginTransaction();

                if (($response = (new VerificationController())->checkIsBillingInfoAdded($user_id)) != 0) {

                    DB::update('UPDATE billing_master SET
                              full_name = ?,
                              address = ?,
                              country = ?,
                              state = ?,
                              city = ?,
                              zip_code = ?
                              WHERE
                              user_id = ?', [$full_name, $address, $country, $state, $city, $zip_code, $user_id]);
                } else {
                    $uuid = (new ImageController())->generateUUID();

                    DB::insert('insert into billing_master (
                              user_id,
                              uuid,
                              full_name,
                              address,
                              country,
                              state,
                              city,
                              zip_code,
                              is_active,
                              create_time)
                              VALUES(?, ?, ?, ?, ?, ?, ?, ?,?, ?)', [$user_id, $uuid, $full_name, $address, $country, $state, $city, $zip_code, 1, $create_time]);
                }

                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Billing address saved successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('setBillingAddress', $e);
            //      Log::error("setBillingAddress : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set billing address.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getBillingInfo",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getBillingInfo",
     *        summary="Get Billing Information",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Billing Information fetched successfully.","cause":"","data":{"user_detail":{"user_id":23,"user_name":"rushita.optimumbrew@gmail.com","first_name":"rushita","email_id":"rushita.optimumbrew@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":0,"mailchimp_subscr_id":"3d7d82c763761fa3edb6b175ed254330","role_id":7,"create_time":"2019-01-12 00:55:39","update_time":"2019-01-12 00:55:46","subscr_expiration_time":"2020-09-17 05:27:34","next_billing_date":"2020-09-17 05:27:34","is_subscribe":1},"billing_info":{"billing_id":3,"user_id":23,"full_name":"Test","address":"Test","country":"","state":"","city":"","zip_code":"","update_time":"2019-01-30 04:12:43"}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get billing information.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function getBillingInfo()
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getBillingInfo$this->user_id")) {
                $result = Cache::rememberforever("getBillingInfo$this->user_id", function () {

                    $billing_info = DB::select('SELECT
                                                  bm.uuid  AS billing_id,
                                                  um.uuid AS user_id,
                                                  COALESCE (bm.full_name, "") AS full_name,
                                                  COALESCE (bm.address, "") AS address,
                                                  COALESCE (bm.country, "") AS country,
                                                  COALESCE (bm.state, "") AS state,
                                                  COALESCE (bm.city, "") AS city,
                                                  COALESCE (bm.zip_code, "") AS zip_code,
                                                  COALESCE (bm.attribute1, "") AS country_code,
                                                  bm.update_time
                                                FROM
                                                  billing_master as bm,
                                                  user_master as um
                                                WHERE
                                                  bm.user_id = um.id AND
                                                  bm.user_id = ? AND
                                                  bm.is_active = ?
                                                ORDER BY bm.update_time DESC', [$this->user_id, 1]);

                    $user_detail = (new LoginController())->getUserInfoByUserId($this->user_id);

                    if (count($billing_info) > 0) {
                        return $result = [
                            'user_detail' => $user_detail,
                            'billing_info' => $billing_info[0],
                        ];
                    } else {
                        return $result = [
                            'user_detail' => $user_detail,
                            'billing_info' => json_decode('{}'),
                        ];
                    }

                });
            }

            $redis_result = Cache::get("getBillingInfo$this->user_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Billing Information fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getBillingInfo', $e);
            //      Log::error("getBillingInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get billing information.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Feedback |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/giveFeedback",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="giveFeedback",
     *        summary="Give feedback by user",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *        description="any one parameter is required in json object",
     *
     *   	  @SWG\Schema(
     *          required={"feedback_text","rate","feedback_url"},
     *
     *          @SWG\Property(property="feedback_id",  type="integer", example=1, description="compulsory when you want to update feedback"),
     *          @SWG\Property(property="feedback_text",  type="string", example="Frame", description="optional when rate given by the user"),
     *          @SWG\Property(property="rate",  type="integer", example=3, description="optional when feedback_text given by the user"),
     *          @SWG\Property(property="feedback_url",  type="string", example="https://photoadking.com/app/#/video-editor/ajfvyv0cbeffda/fnuyaa55a26661/dzikz7b9eefe0d", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function giveFeedback(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['rate', 'feedback_url'], $request)) != '') {
                return $response;
            }

            $feedback_text = isset($request->feedback_text) ? (new ImageController())->removeEmoji($request->feedback_text) : null;
            if (trim($feedback_text) == '') {
                $feedback_text = null;
            }
            $feedback_url = $request->feedback_url;
            $rate = isset($request->rate) ? $request->rate : 0;
            $device_info = isset($request->device_info) ? json_encode($request->device_info) : null;
            $create_time = date('Y-m-d H:i:s');

            if (($feedback_text == null or $feedback_text == '') and ($rate == 0 or $rate == null)) {
                return $response = Response::json(['code' => 201, 'message' => 'Required field(s) feedback_text and rate both are missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $uuid = (new ImageController())->generateUUID();
            DB::beginTransaction();
            DB::insert('INSERT INTO feedback_master (user_id,uuid, rate, feedback_text, feedback_url, device_info, is_active, create_time)  VALUES(?, ?, ?, ?, ?, ?, ?, ?)', [$user_id, $uuid, $rate, $feedback_text, $feedback_url, $device_info, 1, $create_time]);

            /* update feedback if user gives feedback 2nd time */
            /*if (($response = (new VerificationController())->checkIsFeedbackGiven($user_id)) != '') {

                      if (($response = (new VerificationController())->validateRequiredParameter(array('feedback_id'), $request)) != '')
                          return $response;

                      $feedback_id = $request->feedback_id;

                      DB::update('UPDATE feedback_master SET rate = ?, feedback_text = ? WHERE user_id = ? AND id = ?', [$rate, $feedback_text, $user_id, $feedback_id]);
                  } else {
                      DB::insert('INSERT INTO feedback_master (user_id, rate, feedback_text, is_active, create_time)  VALUES(?, ?, ?, ?, ?)', [$user_id, $rate, $feedback_text, 1, $create_time]);
                  }*/

            DB::commit();

            //send email

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
            $email_id = $user_detail->email_id;
            $first_name = $user_detail->first_name;

            //$template = 'simple';
            $template = 'feedback';
            $subject = "PhotoADKing: Feedback ($rate star)";
            $message_body = [
                'send_by' => $first_name,
                'message' => $feedback_text,
                'rate' => $rate,
                'email_id' => $email_id,
                'user_name' => 'Admin'];
            $api_name = 'giveFeedback';
            $api_description = 'Give feedback.';
            if ($rate >= 4) {
                $admin_email_id = 'contact2rushita@gmail.com';
                $super_admin_email_id = '';
            } else {
                $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
                $super_admin_email_id = Config::get('constant.SUPER_ADMIN_EMAIL_ID');
            }
            $this->dispatch(new FeedbackEmailJob(1, $email_id, $admin_email_id, $super_admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

            $response = Response::json(['code' => 200, 'message' => 'Thank you for your feedback.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('giveFeedback', $e);
            //      Log::error("giveFeedback : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'give feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateFeedback",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateFeedback",
     *        summary="Update feedback by user",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *        description="any one parameter is required in json object",
     *
     *   	  @SWG\Schema(
     *          required={"feedback_id","feedback_text","rate"},
     *
     *          @SWG\Property(property="feedback_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="feedback_text",  type="string", example="Frame", description="optional when rate given by the user"),
     *          @SWG\Property(property="rate",  type="integer", example=3, description="optional when feedback_text given by the user"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function updateFeedback(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            $feedback_text = isset($request->feedback_text) ? $request->feedback_text : null;
            $rate = isset($request->rate) ? $request->rate : 0;
            $create_time = date('Y-m-d H:i:s');

            if (($feedback_text == null or $feedback_text == '') and ($rate == 0 or $rate == null)) {
                return $response = Response::json(['code' => 201, 'message' => 'Required field(s) feedback_text and rate both are missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->checkIsFeedbackGiven($user_id)) != '') {
                return $response;
            }

            DB::beginTransaction();

            DB::update('UPDATE feedback_master SET rate = ?, feedback_text = ? WHERE user_id = ?)', [$rate, $feedback_text, $user_id]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Thank you for your feedback.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('updateFeedback', $e);
            //      Log::error("updateFeedback : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getFeedback",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getFeedback",
     *        summary="Get feedback.",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"feedback_id"},
     *
     *          @SWG\Property(property="feedback_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getFeedback()
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $this->user_id = $user_detail->id;

            $redis_result = Cache::rememberforever("getFeedback$this->user_id", function () {

                return DB::select('SELECT
                                        fm.uuid as feedback_id,
                                        um.uuid as user_id,
                                        COALESCE (fm.rate,0) AS rate,
                                        COALESCE (fm.feedback_text,"") AS feedback_text,
                                        fm.update_time
                                      FROM
                                        feedback_master as fm,
                                        user_master as um
                                      WHERE
                                        um.id = fm.user_id AND
                                        fm.user_id = ? AND
                                        fm.is_active = ?
                                      ORDER BY fm.update_time DESC', [$this->user_id, 1]);

            });

            if (! $redis_result) {
                $redis_result = json_decode('{}');
                $response = Response::json(['code' => 200, 'message' => 'Feedback fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Feedback fetched successfully.', 'cause' => '', 'data' => $redis_result[0]]);
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getFeedback', $e);
            //      Log::error("getFeedback : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Report a problem |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/submitReport",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addCatalog",
     *        summary="Submit a report by user",
     *        consumes={"multipart/form-data" },
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *      @SWG\Parameter(
     *        in="formData",
     *        name="request_data",
     *        type="string",
     *        description="pass report_text in json object",
     *
     *        @SWG\Schema(
     *
     *           @SWG\Property(property="report_text",  type="string", example="I've problem like this..", description="required when file is not attached by the user"),
     *              ),
     *      ),
     *
     *      @SWG\Parameter(
     *        name="file",
     *        in="formData",
     *        description="screen shot attached by user",
     *        type="file"
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function submitReport(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));

            //$request = json_decode($request_body->getContent());
            $report_text = isset($request->report_text) ? $request->report_text : null;
            $create_time = date('Y-m-d H:i:s');

            if (($report_text == null or $report_text == '') and (! $request_body->hasFile('file'))) {
                return $response = Response::json(['code' => 201, 'message' => 'Required field(s) report_text and file both are missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                $attachment = (new ImageController())->generateNewFileName('attachment', $image_array);
                (new ImageController())->saveReportAttachment($attachment);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($attachment);
                }
            } else {
                $attachment = null;
            }

            $uuid = (new ImageController())->generateUUID();
            DB::beginTransaction();

            DB::insert('insert into report_master (user_id,uuid, report_text, attachment, is_active, create_time)   VALUES(?, ?, ?, ?,?,?)', [$user_id, $uuid, $report_text, $attachment, 1, $create_time]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Report submitted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('submitReport', $e);
            //      Log::error("submitReport : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'submit report.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Dashboard |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getDashboardData",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getDashboardData",
     *        summary="Get featured cards & sub categories list by list sub category id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *           required={"sub_category_id_list"},
     *
     *           @SWG\Property(property="sub_category_id_list",type="array",description="comma seperated sub_category_id",
     *
     *                  @SWG\Items(type="integer",example=1
     *                  ),
     *              ),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    //Unused API
    public function getDashboardData(Request $request)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->sub_category_id_list = $request->sub_category_id_list;

            //convert array into string with asc order of id to use in redis-cache key
            sort($this->sub_category_id_list);
            $this->sub_category_ids = implode(':', $this->sub_category_id_list);

            $this->item_count = Config::get('constant.ITEM_COUNT_OF_SAMPLE_JSON');
            $this->offset = 0;

            //Log::info('request_data', ['request_data' => $request]);

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getDashboardData$this->item_count:$this->sub_category_ids")) {
                $result = Cache::rememberforever("getDashboardData$this->item_count:$this->sub_category_ids", function () {

                    $result_array = [];
                    $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
                    $category_id = Config::get('constant.CATEGORY_ID_OF_TEMPLATES');
                    foreach ($this->sub_category_id_list as $key) {

                        $sub_category = DB::select('SELECT
                                        sct.id as sub_category_id,
                                        sct.category_id,
                                        sct.sub_category_name
                                      FROM
                                        sub_category_master as sct
                                      WHERE
                                        sct.id = ? AND
                                        sct.is_active=1 AND
                                        sct.category_id = ?', [$key, $category_id]);

                        if (count($sub_category) > 0) {
                            $sub_category_name = $sub_category[0]->sub_category_name;
                            $sub_category_id = $sub_category[0]->sub_category_id;

                            $sample_cards = DB::select('SELECT
                                                          cm.id as content_id,
                                                          cm.catalog_id,
                                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                          coalesce(cm.is_featured,"") as is_featured,
                                                          cm.content_type,
                                                          coalesce(cm.is_free,0) as is_free,
                                                          coalesce(cm.is_portrait,0) as is_portrait,
                                                          coalesce(cm.height,0) as height,
                                                          coalesce(cm.width,0) as width,
                                                          coalesce(cm.color_value,"") AS color_value,
                                                          cm.update_time
                                                        FROM
                                                          content_master as cm,
                                                          sub_category_catalog as sct,
                                                          catalog_master as ct
                                                        WHERE
                                                          sct.sub_category_id = ? AND
                                                          sct.catalog_id=cm.catalog_id AND
                                                          sct.catalog_id=ct.id AND
                                                          sct.is_active=1 AND
                                                          ct.is_featured = 1 AND
                                                          cm.content_type = ?
                                                          ORDER BY cm.update_time DESC LIMIT ?, ?', [$key, $content_type, $this->offset, $this->item_count]);

                            $result_array[] = ['sub_category_id' => $sub_category_id, 'sub_category_name' => $sub_category_name, 'sample_cards' => $sample_cards];
                        }

                    }

                    return $result_array;
                });
            }

            $redis_result = Cache::get("getDashboardData$this->item_count:$this->sub_category_ids");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All sample cards & categories are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getDashboardData', $e);
            //      Log::error("getDashboardData : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get sample cards and sub categories.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getDashBoardDetails()
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_uuid = $user_detail->uuid;

            $tag_details = Cache::rememberforever('getDashBoardDetails:tag_details', function () {

                return DB::select('SELECT
                              tag_name
                          FROM
                              tag_master
                          WHERE
                              is_active = ?
                          ORDER BY update_time DESC', [1]);
            });

            $payment_status_details = $cache_data = Cache::get("getDashBoardDetails:payment_status_details:$user_uuid");
            if (isset($cache_data['is_new_event_occurs']) && $cache_data['is_new_event_occurs']) {
                $cache_data['is_new_event_occurs'] = 0;
                Cache::forever("getDashBoardDetails:payment_status_details:$user_uuid", $cache_data);
            }

            $response = Response::json(['code' => 200, 'message' => 'All tags fetched successfully.', 'cause' => '', 'data' => ['tag_details' => $tag_details, 'payment_status_details' => $payment_status_details]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getDashBoardDetails', $e);
            //Log::error("getDashBoardDetails : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get dashboard details.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| Catalog & Content |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getFeaturedCatalogsBySubCategoryId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getFeaturedCatalogsBySubCategoryId",
     *        summary="Get featured catalogs by sub category id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getFeaturedCatalogsBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;

            //            if (($response = (new VerificationController())->validateUserToGet3DObject($user_id, $this->sub_category_id)) != '')
            //                return $response;

            $this->catalog_id = isset($request->catalog_id) ? $request->catalog_id : 0;
            $this->page = isset($request->page) ? $request->page : 1;
            //      $this->item_count = isset($request->item_count) ? $request->item_count : Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."getFeaturedCatalogsBySubCategoryId:$this->sub_category_id:$this->catalog_id:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    if ($this->catalog_id === 0) {

                        $catalog_list = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=1
                                      order by ctm.update_time DESC', [$this->sub_category_id]);

                        foreach ($catalog_list as $key) {
                            if ($catalog_list[0]->catalog_id == $key->catalog_id) {

                                $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."getFeaturedCatalogsBySubCategoryId:$this->sub_category_id:$this->catalog_id:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () use ($catalog_list) {

                                    $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id=ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$catalog_list[0]->catalog_id]);

                                    return $total_row = $total_row_result[0]->total;

                                });

                                $catalog_content = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        WHERE
                                          cm.catalog_id=ctm.id AND
                                          cm.is_active = 1 AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                        order by cm.update_time DESC limit ?, ?', [$catalog_list[0]->catalog_id, $this->offset, $this->item_count]);

                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $catalog_list[0]->content_list = $catalog_content;
                            } else {
                                $total_row = 0;
                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $key->content_list = [];

                            }
                        }
                    } else {
                        $catalog_list = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=1
                                      order by ctm.update_time DESC', [$this->sub_category_id]);

                        foreach ($catalog_list as $key) {
                            if ($this->catalog_id == $key->catalog_id) {

                                $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."getFeaturedCatalogsBySubCategoryId:$this->sub_category_id:$this->catalog_id:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                                    $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id=ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$this->catalog_id]);

                                    return $total_row = $total_row_result[0]->total;

                                });

                                $catalog_content = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        WHERE
                                          cm.is_active = 1 AND
                                          cm.catalog_id=ctm.id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                        order by cm.update_time DESC limit ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $key->content_list = $catalog_content;
                            } else {
                                $total_row = 0;
                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $key->content_list = [];

                            }

                        }
                    }

                    return $catalog_list;

                });
            } else {

                if ($this->catalog_id === 0) {

                    $redis_result = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=1
                                      order by ctm.update_time DESC', [$this->sub_category_id]);

                    foreach ($redis_result as $key) {
                        if ($redis_result[0]->catalog_id == $key->catalog_id) {

                            $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id=ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$redis_result[0]->catalog_id]);

                            $total_row = $total_row_result[0]->total;

                            $catalog_content = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        WHERE
                                          cm.catalog_id=ctm.id AND
                                          cm.is_active = 1 AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                        order by cm.update_time DESC limit ?, ?', [$redis_result[0]->catalog_id, $this->offset, $this->item_count]);

                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $redis_result[0]->content_list = $catalog_content;
                        } else {
                            $total_row = 0;
                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $key->content_list = [];

                        }
                    }
                } else {
                    $redis_result = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=1
                                      order by ctm.update_time DESC', [$this->sub_category_id]);

                    foreach ($redis_result as $key) {
                        if ($this->catalog_id == $key->catalog_id) {

                            $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id=ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$this->catalog_id]);

                            $total_row = $total_row_result[0]->total;

                            $catalog_content = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        WHERE
                                          cm.is_active = 1 AND
                                          cm.catalog_id=ctm.id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                        order by cm.update_time DESC limit ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $key->content_list = $catalog_content;
                        } else {
                            $total_row = 0;
                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $key->content_list = [];

                        }

                    }
                }

            }

            $response = Response::json(['code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getFeaturedCatalogsBySubCategoryId', $e);
            //      Log::error("getFeaturedCatalogsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get featured catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getFeaturedCatalogsWithTemplateBySubCategoryId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getFeaturedCatalogsWithTemplateBySubCategoryId",
     *        summary="Get featured catalogs by sub category id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getFeaturedCatalogsWithTemplateBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->page = isset($request->page) ? $request->page : 1;
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_FEATURED_TEMPLATES');
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."getFeaturedCatalogsWithTemplateBySubCategoryId:$this->sub_category_id:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $catalog_list = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=0
                                      order by ctm.update_time DESC', [$this->sub_category_id]);

                    foreach ($catalog_list as $key) {

                        $catalog_content = DB::select('SELECT
                                                cm.uuid as content_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                                cm.content_type,
                                                coalesce(cm.height,0) as height,
                                                coalesce(cm.width,0) as width,
                                                coalesce(cm.color_value,"") AS color_value,
                                                cm.content_type,
                                                coalesce(cm.is_featured,0) AS is_featured,
                                                coalesce(cm.is_free,0) as is_free,
                                                coalesce(cm.is_portrait,0) AS is_portrait,
                                                coalesce(cm.search_category,"") as search_category,
                                                cm.update_time
                                              FROM
                                                content_master as cm,
                                                catalog_master as ctm
                                              WHERE
                                                cm.catalog_id=ctm.id AND
                                                cm.is_active = 1 AND
                                                ctm.uuid = ? AND
                                                isnull(cm.original_img) AND
                                                isnull(cm.display_img)
                                              order by cm.update_time DESC limit ?, ?', [$key->catalog_id, $this->offset, $this->item_count]);

                        $key->content_list = $catalog_content;

                    }

                    return $catalog_list;

                });
            } else {
                $redis_result = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=0
                                      order by ctm.update_time DESC', [$this->sub_category_id]);

                foreach ($redis_result as $key) {

                    $catalog_content = DB::select('SELECT
                                                cm.uuid as content_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                                cm.content_type,
                                                coalesce(cm.height,0) as height,
                                                coalesce(cm.width,0) as width,
                                                coalesce(cm.color_value,"") AS color_value,
                                                cm.content_type,
                                                coalesce(cm.is_featured,0) AS is_featured,
                                                coalesce(cm.is_free,0) as is_free,
                                                coalesce(cm.is_portrait,0) AS is_portrait,
                                                coalesce(cm.search_category,"") as search_category,
                                                cm.update_time
                                              FROM
                                                content_master as cm,
                                                catalog_master as ctm
                                              WHERE
                                                cm.catalog_id=ctm.id AND
                                                cm.is_active = 1 AND
                                                ctm.uuid = ? AND
                                                isnull(cm.original_img) AND
                                                isnull(cm.display_img)
                                              order by cm.update_time DESC limit ?, ?', [$key->catalog_id, $this->offset, $this->item_count]);

                    $key->content_list = $catalog_content;

                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getFeaturedCatalogsWithTemplateBySubCategoryId', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get featured catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getContentDetailById",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getContentDetailById",
     *        summary="Get content detail by id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"content_id","content_type"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="content_type",  type="integer", example=4, description="1=image(sticker, frame, background),2=video,3=audio,4=template,5=text_json,6=3D_text_json,7=3D_shape"),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Detail fetched successfully.","cause":"","data":{"catalog_id":93,"content_file":"","json_data":{"text_json":{{"xPos":289,"yPos":530,"color":"#219cdd","text":"CHARITY","size":100,"fontName":"AgencyFB-Bold","fontPath":"fonts/AGENCYB.ttf","alignment":2,"bg_image":"","texture_image":"","opacity":100,"angle":0,"shadowColor":"#000000","shadowRadius":0,"shadowDistance":0}},"sticker_json":{{"xPos":0,"yPos":0,"width":1024,"height":1024,"sticker_image":"umesh_5cee2000a95fb_sticker_image_15591096320.png","angle":0,"is_round":0}},"image_sticker_json":{},"frame_json":{"frame_image":"","frame_color":""},"background_json":{"background_image":"","background_color":""},"sample_image":"umesh_5cee2000a4faa_sample_image_1559109632.jpg","height":1024,"width":1024,"is_featured":0,"is_portrait":1},"is_free":1}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get content detail.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function getContentDetailById(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $this->user_detail = JWTAuth::toUser($token);

            $this->user_id = $this->user_detail->id;
            if ($this->user_detail->roles) {
                $this->user_role = $this->user_detail->roles->first()->name;
            } else {
                Log::error('getContentDetailById : Role did not fetched.', ['token' => $token, 'user_id' => $this->user_id]);

                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content detail.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->content_id = $request->content_id;
            $this->content_type = $request->content_type;
            $this->page_id = isset($request->page_id) ? $request->page_id : null;

            //Log::info('request_data', ['request_data' => $request]);

            $redis_result = Cache::rememberforever("getContentDetailById:$this->content_id:$this->content_type:$this->page_id", function () {

                $result = DB::select('SELECT
                                            ctm.uuid as catalog_id,
                                            ctm.name as catalog_name,
                                            COALESCE(cm.multiple_images,"") AS multiple_images,
                                            IF(cm.content_file != "",CONCAT("'.Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                            template_name,
                                            COALESCE(cm.json_data,"") AS json_data,
                                            COALESCE(json_pages_sequence,"") AS pages_sequence,
                                            COALESCE(cm.is_free,0) AS is_free
                                        FROM
                                            content_master as cm,
                                            catalog_master as ctm
                                        WHERE
                                            cm.catalog_id = ctm.id AND
                                            cm.uuid= ?
                                        ORDER BY cm.update_time DESC', [$this->content_id]);

                if (count($result) > 0 && $result[0]->json_data) {

                    if ($result[0]->pages_sequence) {
                        $result[0]->pages_sequence = explode(',', $result[0]->pages_sequence);
                    }

                    $result[0]->json_data = json_decode($result[0]->json_data);

                    if ($this->page_id) {
                        $json_data = $result[0]->json_data->{$this->page_id};
                        $result[0]->json_data = json_decode('{}');
                        $result[0]->json_data->{$this->page_id} = $json_data;
                        $result[0]->pages_sequence = [$this->page_id];
                    }

                    return $result[0];

                } else {
                    Log::error('getContentDetailById : can not fetch content_detail', ['content_id' => $this->content_id]);

                    return [];
                }

            });

            if (! $redis_result) {
                $redis_result = [];

            } elseif ($this->content_type != Config::get('constant.CONTENT_TYPE_OF_3D_TEXT_JSON') && $this->content_type != Config::get('constant.CONTENT_TYPE_OF_3D_SHAPE') && $redis_result->is_free == 0 && $this->user_role == Config::get('constant.ROLE_FOR_USER')) {

                //Log::error('getContentDetailById : Unauthorized user.', ['user_role' => $this->user_role, 'content_id' => $this->content_id, 'user_id' => $this->user_id]);
                return Response::json(['code' => 432, 'message' => 'Free users are not authorized to create pro designs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $response = Response::json(['code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getContentDetailById', $e);
            //Log::error("getContentDetailById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getContentDetailByIdV3(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->content_id = $request->content_id;
            $this->content_type = $request->content_type;
            $this->page_id = isset($request->page_id) ? $request->page_id : null;

            $redis_result = Cache::rememberforever("getContentDetailById:$this->content_id:$this->content_type:$this->page_id", function () {

                $result = DB::select('SELECT
                                          ctm.uuid AS catalog_id,
                                          ctm.name AS catalog_name,
                                          COALESCE(cm.multiple_images, "") AS multiple_images,
                                          IF(cm.content_file != "", CONCAT("'.Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.content_file), "") AS content_file,
                                          IF(cm.image != "", CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.image), "") as sample_image,
                                          COALESCE(cm.json_data, "") AS json_data,
                                          COALESCE(json_pages_sequence, "") AS pages_sequence,
                                          COALESCE(cm.is_free, 0) AS is_free
                                      FROM
                                          content_master AS cm,
                                          catalog_master AS ctm
                                      WHERE
                                          cm.catalog_id = ctm.id AND
                                          cm.uuid= ?
                                      ORDER BY cm.update_time DESC', [$this->content_id]);

                if (count($result) > 0 && $result[0]->json_data) {

                    if ($result[0]->pages_sequence) {
                        $result[0]->pages_sequence = explode(',', $result[0]->pages_sequence);
                    }

                    $result[0]->json_data = json_decode($result[0]->json_data);

                    if ($this->page_id) {
                        $json_data = $result[0]->json_data->{$this->page_id};
                        $result[0]->json_data = json_decode('{}');
                        $result[0]->json_data->{$this->page_id} = $json_data;
                        $result[0]->pages_sequence = [$this->page_id];
                    }

                    return $result[0];

                } else {
                    Log::error('getContentDetailById : can not fetch content_detail', ['content_id' => $this->content_id]);

                    return [];
                }
            });

            $response = Response::json(['code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getContentDetailByIdV3', $e);
            //Log::error("getContentDetailByIdV3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    //This API is only used for designer because of font issue
    public function getContentDetailByIdV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $this->user_detail = JWTAuth::toUser($token);

            $this->user_id = $this->user_detail->id;
            if ($this->user_detail->roles) {
                $this->user_role = $this->user_detail->roles->first()->name;
            } else {
                Log::error('getContentDetailById : Role did not fetched.', ['token' => $token, 'user_id' => $this->user_id]);

                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content detail.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->content_id = $request->content_id;
            $this->content_type = $request->content_type;
            $this->page_id = isset($request->page_id) ? $request->page_id : null;

            //Log::info('request_data', ['request_data' => $request]);

            $redis_result = Cache::rememberforever("getContentDetailByIdV2:$this->content_id:$this->content_type:$this->page_id", function () {

                $result = DB::select('SELECT
                                            ctm.uuid as catalog_id,
                                            ctm.name as catalog_name,
                                            COALESCE(cm.multiple_images,"") AS multiple_images,
                                            IF(cm.content_file != "",CONCAT("'.Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                            COALESCE(cm.json_data,"") AS json_data,
                                            COALESCE(json_pages_sequence,"") AS pages_sequence,
                                            COALESCE(cm.is_free,0) AS is_free
                                        FROM
                                            content_master as cm,
                                            catalog_master as ctm
                                        WHERE
                                            cm.catalog_id = ctm.id AND
                                            cm.uuid= ?
                                        ORDER BY cm.update_time DESC', [$this->content_id]);

                if (count($result) > 0 && $result[0]->json_data) {

                    $json_data = $result[0]->json_data = json_decode($result[0]->json_data);

                    if ($result[0]->pages_sequence) {
                        $pages_sequence_array = $result[0]->pages_sequence = explode(',', $result[0]->pages_sequence);

                        $text_json = $curved_text_json = [];
                        foreach ($pages_sequence_array as $i => $page_sequence) {
                            $text_json = array_merge($text_json, isset($json_data->{$page_sequence}->text_json) ? $json_data->{$page_sequence}->text_json : []);
                            $curved_text_json = array_merge($curved_text_json, isset($json_data->{$page_sequence}->curved_text_json) ? $json_data->{$page_sequence}->curved_text_json : []);
                        }

                    } else {
                        $text_json = isset($json_data->text_json) ? $json_data->text_json : [];
                        $curved_text_json = isset($json_data->curved_text_json) ? $json_data->curved_text_json : [];
                    }

                    if ($this->page_id) {
                        $json_data = $result[0]->json_data->{$this->page_id};
                        $result[0]->json_data = json_decode('{}');
                        $result[0]->json_data->{$this->page_id} = $json_data;
                        $result[0]->pages_sequence = [$this->page_id];
                    }

                    $font_name = array_column(json_decode(json_encode($text_json), 1), 'fontName');
                    $font_name = array_merge($font_name, array_column(json_decode(json_encode($curved_text_json), 1), 'fontName'));
                    $unique_font_name = '"'.implode('", "', array_unique(array_filter($font_name))).'"';

                    $font_issue = DB::select('SELECT ios_font_name, issue_code FROM font_master WHERE ios_font_name IN ('.$unique_font_name.') AND issue_code IS NOT NULL AND issue_code != \'\' ');

                    $result[0]->font_issue = $font_issue;

                    return $result[0];

                } else {
                    Log::error('getContentDetailByIdV2 : can not fetch content_detail', ['content_id' => $this->content_id]);

                    return [];
                }

            });

            if (! $redis_result) {
                $redis_result = [];

            } elseif ($this->content_type != Config::get('constant.CONTENT_TYPE_OF_3D_TEXT_JSON') && $this->content_type != Config::get('constant.CONTENT_TYPE_OF_3D_SHAPE') && $redis_result->is_free == 0 && $this->user_role == Config::get('constant.ROLE_FOR_USER')) {

                //Log::error('getContentDetailById : Unauthorized user.', ['user_role' => $this->user_role, 'content_id' => $this->content_id, 'user_id' => $this->user_id]);
                return Response::json(['code' => 432, 'message' => 'Free users are not authorized to create pro designs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $response = Response::json(['code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getContentDetailByIdV2', $e);
            //Log::error("getContentDetailByIdV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*
    public function getContentDetailById(Request $request_body)
    {

      try {

        $token = JWTAuth::getToken();
        JWTAuth::toUser($token);

        $request = json_decode($request_body->getContent());

        if (($response = (new VerificationController())->validateRequiredParameter(array('content_id', 'content_type'), $request)) != '')
          return $response;

        $this->content_id = $request->content_id;
        $this->content_type = $request->content_type;

        //Log::info('request_data', ['request_data' => $request]);

        if (!Cache::has("Config::get('constant.REDIS_KEY'):getContentDetailById$this->content_id:$this->content_type")) {
          $result = Cache::rememberforever("getContentDetailById$this->content_id:$this->content_type", function () {

            if ($this->content_type == Config::get('constant.CONTENT_TYPE_OF_CARD_JSON') or $this->content_type == Config::get('constant.CONTENT_TYPE_OF_TEXT_JSON') or $this->content_type == Config::get('constant.CONTENT_TYPE_OF_3D_TEXT_JSON')) {
              //Log::info($this->content_id.' 4,5,6 '. $this->content_type);
              $result = DB::select('SELECT
                                                  ctm.uuid as catalog_id,
                                                  coalesce(cm.content_file,"") AS content_file,
                                                  coalesce(cm.json_data,"") AS json_data,
                                                  coalesce(cm.is_free,0) AS is_free
                                                  FROM
                                                  content_master as cm,
                                                  catalog_master as ctm
                                                  WHERE
                                                  cm.catalog_id = ctm.id AND
                                                  cm.uuid= ?
                                                  order by cm.update_time DESC', [$this->content_id]);
              if (count($result) > 0) {
                //return json_decode($result[0]->json_data);
                if ($result[0]->json_data != "") {
                  $result[0]->json_data = json_decode($result[0]->json_data);
                }
                return $result[0];
              } else {
                return json_decode("{}");
              }
            } elseif ($this->content_type == Config::get('constant.CONTENT_TYPE_OF_3D_SHAPE')) {
              //Log::info($this->content_id.' 7 '. $this->content_type);
              $result = DB::select('SELECT
                                                  ctm.uuid as catalog_id,
                                                  IF(cm.content_file != "",CONCAT("' . Config::get('constant.3D_OBJECTS_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.content_file),"") AS content_file,
                                                  coalesce(cm.json_data,"") AS json_data,
                                                  coalesce(cm.is_free,0) AS is_free
                                                  FROM
                                                  content_master as cm,
                                                  catalog_master as ctm
                                                  WHERE
                                                  cm.catalog_id = ctm.id AND
                                                  cm.uuid= ?
                                                  ORDER BY cm.update_time DESC', [$this->content_id]);
              if (count($result) > 0) {
                if ($result[0]->json_data != "") {
                  $result[0]->json_data = json_decode($result[0]->json_data);
                }
                return $result[0];
              } else {
                return json_decode("{}");
              }
            } else {
              $result = DB::select('SELECT
                                                  ctm.uuid as catalog_id,
                                                  IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as sample_image,
                                                  coalesce(cm.content_file,"") AS content_file,
                                                  coalesce(cm.json_data,"") AS json_data,
                                                  coalesce(cm.is_free,0) AS is_free
                                                  FROM
                                                  content_master as cm,
                                                  catalog_master as ctm
                                                  WHERE
                                                  ctm.id =cm.catalog_id AND
                                                  cm.uuid= ?
                                                  order by cm.update_time DESC', [$this->content_id]);
              if (count($result) > 0) {
                if ($result[0]->json_data != "") {
                  $result[0]->json_data = json_decode($result[0]->json_data);
                }
                return $result[0];
              } else {
                return json_decode("{}");
              }
            }
          });
        }

        $redis_result = Cache::get("getContentDetailById$this->content_id:$this->content_type");

        if (!$redis_result) {
          $redis_result = [];
        }

        $response = Response::json(array('code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => $redis_result));
        $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

      } catch (Exception $e) {
        (new ImageController())->logs("getContentDetailById",$e);
//      Log::error("getContentDetailById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get content detail.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        DB::rollBack();
      }
      return $response;
    }
    */

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getSampleContentBySubCategoryId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getSampleContentBySubCategoryId",
     *        summary="Get sample content by sub category id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    //Unused API In PAK
    public function getSampleContentBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getSampleImagesForMobile Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSampleContentBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getSampleContentBySubCategoryId$this->sub_category_id", function () {
                    return DB::select('SELECT
                                          DISTINCT cm.id AS content_id,
                                          IF(cm.original_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.original_img),"") AS original_thumbnail_img,
                                          IF(cm.original_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.original_img),"") AS original_compressed_img,
                                          IF(cm.original_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.original_img),"") AS original_original_img,
                                          IF(cm.display_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.display_img),"") AS display_thumbnail_img,
                                          IF(cm.display_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.display_img),"") AS display_compressed_img,
                                          IF(cm.display_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.display_img),"") AS display_original_img,
                                          cm.image_type,
                                          cm.update_time
                                        FROM
                                          catalog_master AS ctm,
                                          content_master AS cm
                                          LEFT JOIN sub_category_catalog AS scc ON scc.catalog_id = cm.catalog_id AND sub_category_id = ?
                                        WHERE
                                          cm.is_active = 1 AND
                                          isnull(cm.image) AND
                                          ctm.is_featured = 1
                                        ORDER BY cm.update_time DESC', [$this->sub_category_id]);
                });
            }
            $redis_result = Cache::get("getSampleContentBySubCategoryId$this->sub_category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sample content fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getSampleContentBySubCategoryId', $e);
            //      Log::error("getSampleContentBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get sample content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getContentByCatalogId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getContentByCatalogId",
     *        summary="Get content by catalog id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"catalog_id","page","item_count"},
     *
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=1, description=""),
     *          @SWG\Property(property="is_free",  type="integer", example=1, description="0=paid & 1=free"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getContentByCatalogId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getContentByCatalogIdForAdmin Request :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;
            $this->is_free = isset($request->is_free) ? $request->is_free : 'coalesce(cm.is_free,0)';
            $this->content_type = isset($request->content_type) ? $request->content_type : '';
            $this->cache_content_type = isset($request->content_type) ? "AND cm.content_type = $request->content_type" : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {

                $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."getContentByCatalogId:$this->catalog_id:$this->page:$this->item_count:$this->is_free:$this->content_type", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."getContentByCatalogId:$this->catalog_id:$this->item_count:$this->is_free:$this->content_type", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                        $total_row_result = DB::select('SELECT
                                          COUNT(cm.id) AS total
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        WHERE
                                          cm.is_active = 1 AND
                                          cm.catalog_id = ctm.id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                          '.$this->cache_content_type.'
                                        order by cm.update_time DESC', [$this->catalog_id]);

                        return $total_row = $total_row_result[0]->total;

                    });

                    /*$result = DB::select('SELECT
                                                    cm.id as content_id,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                                    cm.content_type,
                                                    coalesce(cm.is_featured,0) as is_featured,
                                                    coalesce(cm.is_free,0) as is_free,
                                                    coalesce(cm.is_portrait,0) as is_portrait,
                                                    cm.update_time
                                                  FROM
                                                    content_master as cm
                                                  WHERE
                                                    cm.is_active = 1 AND
                                                    cm.catalog_id = ? AND
                                                    isnull(cm.original_img) AND
                                                    isnull(cm.display_img)
                                                  order by cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);*/

                    $result = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                          IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                          IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                          cm.content_type,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          cm.content_type,
                                          coalesce(cm.is_featured,0) AS is_featured,
                                          '.$this->is_free.' AS is_free,
                                          coalesce(cm.is_portrait,0) AS is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          cm.update_time,
                                          coalesce(am.credit_note,"") as credit_note,
                                          coalesce(am.tag,"") as tag
                                        FROM
                                            content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                            catalog_master as ctm
                                        WHERE
                                          cm.is_active = 1 AND
                                          ctm.id = cm.catalog_id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                          '.$this->cache_content_type.'
                                        ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                    foreach ($result as $key) {
                        if ($key->credit_note != '') {
                            $key->credit_note = json_decode($key->credit_note);
                        }
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];

                    //return $result;
                });
            } else {
                $total_row_result = DB::select('SELECT
                                          COUNT(cm.id) AS total
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        WHERE
                                          cm.is_active = 1 AND
                                          cm.catalog_id = ctm.id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                          '.$this->cache_content_type.'
                                        order by cm.update_time DESC', [$this->catalog_id]);
                $total_row = $total_row_result[0]->total;

                /*$result = DB::select('SELECT
                                                cm.id as content_id,
                                                IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                                IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                                IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                                cm.content_type,
                                                coalesce(cm.is_featured,0) as is_featured,
                                                coalesce(cm.is_free,0) as is_free,
                                                coalesce(cm.is_portrait,0) as is_portrait,
                                                cm.update_time
                                              FROM
                                                content_master as cm
                                              WHERE
                                                cm.is_active = 1 AND
                                                cm.catalog_id = ? AND
                                                isnull(cm.original_img) AND
                                                isnull(cm.display_img)
                                              order by cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);*/

                $result = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                          IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                          IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                          cm.content_type,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          cm.content_type,
                                          coalesce(cm.is_featured,0) AS is_featured,
                                          '.$this->is_free.' AS is_free,
                                          coalesce(cm.is_portrait,0) AS is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          cm.update_time,
                                          coalesce(am.credit_note,"") as credit_note,
                                          coalesce(am.tag,"") as tag
                                        FROM
                                            content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                            catalog_master as ctm
                                        WHERE
                                          cm.is_active = 1 AND
                                          ctm.id = cm.catalog_id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                          '.$this->cache_content_type.'
                                        ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                foreach ($result as $key) {
                    if ($key->credit_note != '') {
                        $key->credit_note = json_decode($key->credit_note);
                    }
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];

            }

            $response = Response::json(['code' => 200, 'message' => 'Content fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getContentByCatalogId', $e);
            //      Log::error("getContentByCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getStickerCatalogsBySubCategoryId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getStickerCatalogsBySubCategoryId",
     *        summary="Get sticker catalogs by sub category id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Catalogs fetched successfully.","cause":"","data":{"result":{{"catalog_id":57,"name":"png stickers","thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c273994e1fac_catalog_img_1546074516.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c273994e1fac_catalog_img_1546074516.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5c273994e1fac_catalog_img_1546074516.png","is_free":1,"is_featured":0,"total_record":0,"is_next_page":false,"content_list":{}},{"catalog_id":56,"name":"New Stickers","thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c134fe1c48c0_catalog_img_1544769505.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c134fe1c48c0_catalog_img_1544769505.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5c134fe1c48c0_catalog_img_1544769505.png","is_free":0,"is_featured":0,"total_record":274,"is_next_page":true,"content_list":{{"content_id":2693,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5cc13b4a99ab9_normal_image_1556167498.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5cc13b4a99ab9_normal_image_1556167498.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5cc13b4a99ab9_normal_image_1556167498.png","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5cc13b4a99ab9_normal_image_1556167498.png","content_type":1,"height":0,"width":0,"color_value":"","is_free":0,"update_time":"2019-04-24 23:14:59"}}}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get catalogs.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function getStickerCatalogsBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->page = isset($request->page) ? $request->page : 1;
            $this->item_count = isset($request->item_count) ? $request->item_count : Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = Config::get('constant.CONTENT_TYPE_OF_IMAGE');

            $redis_result = Cache::rememberforever("getStickerCatalogsBySubCategoryId:$this->sub_category_id:$this->page:$this->item_count", function () {

                $catalog_list = DB::select('SELECT
                                        DISTINCT ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured,
                                        ctm.update_time
                                      FROM
                                        catalog_master as ctm,
                                        content_master as cm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        scm.uuid = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=ctm.id AND
                                        cm.catalog_id IN (ctm.id) AND
                                        cm.content_type = ? AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured = 0
                                       ORDER BY ctm.update_time DESC', [$this->sub_category_id, $this->content_type]);
                foreach ($catalog_list as $key) {
                    if ($catalog_list[0]->catalog_id == $key->catalog_id) {

                        $total_row = Cache::rememberforever("getStickerCatalogsBySubCategoryId:$this->sub_category_id:$this->item_count", function () use ($catalog_list) {

                            $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.content_type = ? AND
                                                                  cm.catalog_id = ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$this->content_type, $catalog_list[0]->catalog_id]);

                            return $total_row = $total_row_result[0]->total;

                        });

                        $catalog_content = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                          IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                          IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                          cm.content_type,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          cm.content_type,
                                          coalesce(cm.is_featured,0) AS is_featured,
                                          '.$key->is_free.' AS is_free,
                                          coalesce(cm.is_portrait,0) AS is_portrait,
                                          cm.update_time,
                                          coalesce(am.credit_note,"") as credit_note,
                                          coalesce(am.tag,"") as tag
                                        FROM
                                            content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                            catalog_master as ctm
                                        WHERE
                                          cm.content_type = ? AND
                                          cm.is_active = 1 AND
                                          ctm.id = cm.catalog_id AND
                                          ctm.uuid = ? AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)
                                        ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->content_type, $catalog_list[0]->catalog_id, $this->offset, $this->item_count]);

                        foreach ($catalog_content as $content_key) {
                            if ($content_key->credit_note != '') {
                                $content_key->credit_note = json_decode($content_key->credit_note);
                            }
                        }
                        $key->total_record = $total_row;
                        $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        $catalog_list[0]->content_list = $catalog_content;
                    } else {
                        $total_row = 0;
                        $key->total_record = $total_row;
                        $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        $key->content_list = [];

                    }
                }

                return $catalog_list;
            });

            $response = Response::json(['code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getStickerCatalogsBySubCategoryId', $e);
            //      Log::error("getStickerCatalogsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get normal catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| Template |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchTemplateBySubCategoryId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchTemplateBySubCategoryId",
     *        summary="Search template by sub category_id or without category_id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id","page"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description="0=All"),
     *          @SWG\Property(property="search_category",  type="string", example="Art & Design", description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"All templates are fetched successfully.","cause":"","data":{"total_record":170,"is_next_page":true,"result":{{"content_id":855,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c04fea203329_template_image_1543831202.jpg","is_featured":"1","catalog_id":54,"content_type":4,"is_free":1,"is_portrait":1,"height":450,"width":300,"color_value":"#1f4fa3","update_time":"2018-12-13 06:10:04","search_text":1.4999457597733},{"content_id":1375,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c08976b269ad_template_image_1544066923.jpg","is_featured":"1","catalog_id":37,"content_type":4,"is_free":1,"is_portrait":0,"height":540,"width":540,"color_value":"#4121d","update_time":"2018-12-06 03:28:43","search_text":1.4999457597733}}}}),),
     *        ),
     *
     *       @SWG\Response(
     *            response=433,
     *            description="Couldn't find any templates for 'search_text'",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":433,"message":"Sorry, we couldn't find any templates for 'Flyer', but we found some other templates you might like:","cause":"","data":{"total_record":48,"is_next_page":true,"result":{{"content_id":1486,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c08e12adddab_template_image_1544085802.jpg","is_featured":"1","catalog_id":37,"content_type":4,"sub_category_id":65,"is_free":1,"is_portrait":0,"height":540,"width":540,"color_value":"#fef8d6","update_time":"2018-12-06 08:49:30"},{"content_id":1485,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c08e0f1baa1e_template_image_1544085745.jpg","is_featured":"1","catalog_id":37,"content_type":4,"sub_category_id":65,"is_free":1,"is_portrait":0,"height":540,"width":540,"color_value":"#4abcee","update_time":"2018-12-06 08:42:26"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function searchTemplateBySubCategoryIdBackUp(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'page', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            //Remove '[@()<>+*%"]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_category = isset($request->search_category) ? mb_substr(preg_replace('/[@()<>+*%"]/', '', mb_strtolower(trim($request->search_category))), 0, 100) : '';
            $this->page = $request->page;
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT'); //$request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type_for_cache = isset($request->content_type) ? $request->content_type : 0;
            $this->content_type = isset($request->content_type) ? $request->content_type : 0;
            if ($this->content_type) {
                $this->content_type = 'AND content_type = '.$this->content_type;
            } else {
                $this->content_type = '';
            }

            if ($this->sub_category_id != 0 && is_numeric($this->sub_category_id)) {
                $sub_category_detail = DB::select('SELECT
                                             uuid
                                           FROM
                                             sub_category_master
                                           WHERE
                                            id =?', [$this->sub_category_id]);
                $this->sub_category_id = $sub_category_detail[0]->uuid;
            }

            $search_text = trim($this->search_category, '%');
            $this->is_cache = 1;

            //caching time of redis key to get default featured templates
            $this->time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_FEATURED_TEMPLATES');

            if ($this->sub_category_id === 0 && $this->search_category == '') {
                /*
                  * create 2 redis keys to manage 24 hour caching when  sub_category_id = 0 & search_category = ""
                  * this key is expired in every 24 hours
                 */
                if (! Cache::has("Config::get('constant.REDIS_KEY'):searchTemplateFromAllCategory$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count")) {
                    $result = Cache::remember("searchTemplateFromAllCategory$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count", $this->time_of_expired_redis_key, function () {
                        /*
                          * If user searched from all sub_categories then we provide following output
                          * output = featured templates from all default featured sub_categories (order by random records & limit is 150)
                         */
                        //get comma separated list of featured sub_categories
                        $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                        //get random templates from featured categories
                        if (! Cache::has("Config::get('constant.REDIS_KEY'):defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache")) {
                            $result = Cache::remember("defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache", $this->time_of_expired_redis_key, function () {
                                //get random templates from featured categories
                                DB::statement("SET sql_mode = '' ");
                                $search_result = DB::select('SELECT
                                                    cm.uuid as content_id,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                    coalesce(cm.is_featured,"") as is_featured,
                                                    ctm.uuid as catalog_id,
                                                    ctm.name as catalog_name,
                                                    scm.uuid as sub_category_id,
                                                    cm.content_type,
                                                    coalesce(cm.is_free,0) as is_free,
                                                    coalesce(cm.is_portrait,0) as is_portrait,
                                                    coalesce(cm.search_category,"") as search_category,
                                                    coalesce(cm.height,0) as height,
                                                    coalesce(cm.width,0) as width,
                                                    coalesce(cm.color_value,"") AS color_value,
                                                    COALESCE(cm.multiple_images,"") AS multiple_images,
                                                    COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                                    cm.update_time
                                                  FROM
                                                    content_master as cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND find_in_set(scc.sub_category_id,"'.$this->default_sub_category_id.'")
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                    sub_category_master AS scm
                                                  WHERE
                                                    scc.sub_category_id = scm.id AND
                                                    cm.is_featured = 1 AND
                                                    cm.is_active = 1 AND
                                                    isnull(cm.original_img) AND
                                                    isnull(cm.display_img)
                                                    '.$this->content_type.'
                                                    GROUP BY content_id
                                                  ORDER BY RAND() LIMIT 150');

                                $total_row = count($search_result);
                                $result = ['total_row' => $total_row, 'search_result' => $search_result];

                                return $result;

                            });
                        }

                        $featured_templates = Cache::get("defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache");
                        Redis::expire("defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache", 1);

                        if (! $featured_templates) {
                            $featured_templates = [];
                        }

                        $total_row = $featured_templates['total_row'];

                        //get elements from array with start & end position
                        $search_result = array_slice($featured_templates['search_result'], $this->offset, $this->item_count);

                        $code = 200;
                        $search_text = trim($this->search_category, '%');
                        $message = "Sorry, we couldn't find any templates for '$search_text', but we found some other templates you might like:";

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                        $result = ['code' => $code, 'message' => $message, 'result' => $search_result];

                        return $result;

                    });
                }

                $redis_result = Cache::get("searchTemplateFromAllCategory$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count");
                Redis::expire("searchTemplateFromAllCategory$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count", 1);

            } else {
                /*
                  * create 2 redis keys to manage 24 hour caching when  sub_category_id = 0 & search_category = ""
                  * this key is deleted only when any changes are made in database related to template
                 */
                if (! Cache::has("Config::get('constant.REDIS_KEY'):searchTemplateBySubCategoryId$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count")) {
                    $result = Cache::rememberforever("searchTemplateBySubCategoryId$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count", function () {
                        $this->is_cache = 0;
                        if ($this->sub_category_id !== 0 && $this->search_category != '') {
                            //fetch templates using search tag from specific sub_category
                            $total_row_result = DB::select('SELECT
                                                  count(*) AS total
                                                FROM
                                                  content_master as cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                  sub_category_master AS scm
                                                where
                                                  scc.sub_category_id = scm.id AND
                                                  scm.uuid = ? AND
                                                  cm.is_active = 1 AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                    '.$this->content_type.'
                                                    ', [$this->sub_category_id]);

                            $total_row = $total_row_result[0]->total;
                            if ($this->page == 1) {
                                SaveSearchTagJob::dispatch($total_row, $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);
                            }
                            $search_result = DB::select('SELECT
                                                  cm.uuid as content_id,
                                                  IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                  IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                                  coalesce(cm.is_featured,"") as is_featured,
                                                  ctm.uuid as catalog_id,
                                                  ctm.name as catalog_name,
                                                  cm.content_type,
                                                  scm.uuid as sub_category_id,
                                                  coalesce(cm.is_free,0) as is_free,
                                                  coalesce(cm.is_portrait,0) as is_portrait,
                                                  coalesce(cm.search_category,"") as search_category,
                                                  coalesce(cm.height,0) as height,
                                                  coalesce(cm.width,0) as width,
                                                  coalesce(cm.color_value,"") AS color_value,
                                                  COALESCE(cm.multiple_images,"") AS multiple_images,
                                                  COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                                  cm.update_time,
                                                  MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                FROM
                                                  content_master as cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                  sub_category_master AS scm
                                                WHERE
                                                  scc.sub_category_id = scm.id AND
                                                  scm.uuid =? AND
                                                  cm.is_active = 1 AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                '.$this->content_type.'
                                                ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                        } elseif ($this->sub_category_id === 0 && $this->search_category != '') {
                            //fetch templates using search tag from all sub_categories
                            $total_row_result = DB::select('SELECT
                                                count(*) AS total
                                              FROM
                                                content_master as cm
                                                JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                              WHERE
                                                cm.is_active = 1 AND
                                                isnull(cm.original_img) AND
                                                isnull(cm.display_img) AND
                                                (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                  '.$this->content_type.'
                                                  ');
                            $total_row = $total_row_result[0]->total;
                            if ($this->page == 1) {
                                SaveSearchTagJob::dispatch($total_row, $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);
                            }
                            DB::statement("SET sql_mode = '' ");
                            $search_result = DB::select('SELECT
                                                        cm.uuid as content_id,
                                                        IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                        IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                                        coalesce(cm.is_featured,"") as is_featured,
                                                        ctm.uuid as catalog_id,
                                                        ctm.name as catalog_name,
                                                        cm.content_type,
                                                        scm.uuid as sub_category_id,
                                                        coalesce(cm.is_free,0) as is_free,
                                                        coalesce(cm.is_portrait,0) as is_portrait,
                                                        coalesce(cm.search_category,"") as search_category,
                                                        coalesce(cm.height,0) as height,
                                                        coalesce(cm.width,0) as width,
                                                        coalesce(cm.color_value,"") AS color_value,
                                                        COALESCE(cm.multiple_images,"") AS multiple_images,
                                                        COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                        COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                                        cm.update_time,
                                                        MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                                        MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                      FROM
                                                        content_master as cm
                                                        JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                        sub_category_master AS scm
                                                      WHERE
                                                        scc.sub_category_id = scm.id AND
                                                        cm.is_active = 1 AND
                                                        isnull(cm.original_img) AND
                                                        isnull(cm.display_img) AND
                                                        (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                          MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                        '.$this->content_type.'
                                                        GROUP BY content_id
                                                      ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                        } else {
                            $search_result = [];
                        }
                        //check return result is empty because we've to provide default templates if get empty result in above 2 cases
                        if (count($search_result) <= 0) {
                            //check user searched from all category or specific category
                            if ($this->sub_category_id !== 0) {
                                if ($this->search_category != '') {
                                    $total_row_result = DB::select('SELECT
                                                     count(DISTINCT cm.id) AS total
                                                  FROM
                                                      content_master as cm
                                                      JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                      sub_category_master AS scm
                                                  WHERE
                                                      scc.sub_category_id = scm.id AND
                                                      scm.uuid != ? AND
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img) AND
                                                      (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                        MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                        '.$this->content_type.'
                                                        ', [$this->sub_category_id]);

                                    $total_row = $total_row_result[0]->total;
                                    if ($total_row > 0) {
                                        if ($this->page == 1) {
                                            SaveSearchTagJob::dispatch($total_row, $this->search_category, 4, 0, '', $this->content_type_for_cache);
                                        }
                                        DB::statement("SET sql_mode = '' ");
                                        $search_result = DB::select('SELECT
                                                  cm.uuid as content_id,
                                                  IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                  IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                                  coalesce(cm.is_featured,"") as is_featured,
                                                  ctm.uuid as catalog_id,
                                                  ctm.name as catalog_name,
                                                  cm.content_type,
                                                  scm.uuid as sub_category_id,
                                                  coalesce(cm.is_free,0) as is_free,
                                                  coalesce(cm.is_portrait,0) as is_portrait,
                                                  coalesce(cm.search_category,"") as search_category,
                                                  coalesce(cm.height,0) as height,
                                                  coalesce(cm.width,0) as width,
                                                  coalesce(cm.color_value,"") AS color_value,
                                                  COALESCE(cm.multiple_images,"") AS multiple_images,
                                                  COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                  COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                                  cm.update_time,
                                                  MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                FROM
                                                  content_master as cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                  sub_category_master AS scm
                                                WHERE
                                                  scc.sub_category_id = scm.id AND
                                                  scm.uuid !=? AND
                                                  cm.is_active = 1 AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                '.$this->content_type.'
                                                GROUP BY content_id
                                                ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                                        $search_text = trim($this->search_category, '%');
                                        $message = "Sorry, we couldn't find any templates for '$search_text' in this category, but we found similar templates that you might like:";
                                    } else {
                                        $search_result = [];
                                    }
                                }
                                if (count($search_result) <= 0) {
                                    /*
                                     * If no data found of search category in all sub category
                                     * If user searched from specific sub_category then we provide following output
                                     * output = featured templates + normal templates (order by featured & update_time desc)
                                    */
                                    $total_row_result = DB::select('SELECT
                                                  count(*) AS total
                                                FROM
                                                  content_master as cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                  sub_category_master AS scm
                                                WHERE
                                                  scc.sub_category_id = scm.id AND
                                                  scm.uuid = ? AND
                                                  cm.is_active = 1 AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img)
                                                  '.$this->content_type.'
                                                  ', [$this->sub_category_id]);

                                    $total_row = $total_row_result[0]->total;
                                    $search_result = DB::select('SELECT
                                                    cm.uuid as content_id,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                                    coalesce(cm.is_featured,"") as is_featured,
                                                    ctm.uuid as catalog_id,
                                                    ctm.name as catalog_name,
                                                    cm.content_type,
                                                    scm.uuid as sub_category_id,
                                                    coalesce(cm.is_free,0) as is_free,
                                                    coalesce(cm.is_portrait,0) as is_portrait,
                                                    coalesce(cm.search_category,"") as search_category,
                                                    coalesce(cm.height,0) as height,
                                                    coalesce(cm.width,0) as width,
                                                    coalesce(cm.color_value,"") AS color_value,
                                                    COALESCE(cm.multiple_images,"") AS multiple_images,
                                                    COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                                    cm.update_time
                                                   FROM
                                                    content_master as cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                    sub_category_master as scm
                                                   WHERE
                                                    scc.sub_category_id = scm.id AND
                                                    scm.uuid =  ? AND
                                                    cm.is_active = 1 AND
                                                    isnull(cm.original_img) AND
                                                    isnull(cm.display_img)
                                                    '.$this->content_type.'
                                                   ORDER BY FIELD(cm.is_featured, 1) DESC, cm.update_time DESC LIMIT ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                                    $search_text = trim($this->search_category, '%');
                                    $message = "Sorry, we couldn't find any templates for '$search_text', but we found some other templates you might like:";
                                }

                            } else {
                                /*
                                  * If user searched from all sub_categories then we provide following output
                                  * output = featured templates from all default featured sub_categories (order by random records & limit is 150)
                                 */
                                //get comma separated list of featured sub_categories
                                $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                                //caching time of redis key to get default featured templates
                                $time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_FEATURED_TEMPLATES');
                                //get random templates from featured categories
                                if (! Cache::has("Config::get('constant.REDIS_KEY'):defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache")) {
                                    $result = Cache::remember("defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache", $time_of_expired_redis_key, function () {

                                        //get random templates from featured categories
                                        DB::statement("SET sql_mode = '' ");
                                        $search_result = DB::select('SELECT
                                                        DISTINCT cm.uuid as content_id,
                                                        IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                        IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                                        coalesce(cm.is_featured,"") as is_featured,
                                                        ctm.uuid as catalog_id,
                                                        ctm.name as catalog_name,
                                                        scm.uuid as sub_category_id,
                                                        cm.content_type,
                                                        coalesce(cm.is_free,0) as is_free,
                                                        coalesce(cm.is_portrait,0) as is_portrait,
                                                        coalesce(cm.search_category,"") as search_category,
                                                        coalesce(cm.height,0) as height,
                                                        coalesce(cm.width,0) as width,
                                                        coalesce(cm.color_value,"") AS color_value,
                                                        COALESCE(cm.multiple_images,"") AS multiple_images,
                                                        COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                        COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                                        cm.update_time
                                                      FROM
                                                        content_master as cm
                                                        JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND find_in_set(scc.sub_category_id,"'.$this->default_sub_category_id.'")
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                        sub_category_master AS scm
                                                      WHERE
                                                        scc.sub_category_id = scm.id AND
                                                        cm.is_featured = 1 AND
                                                        cm.is_active = 1 AND
                                                        isnull(cm.original_img) AND
                                                        isnull(cm.display_img)
                                                        '.$this->content_type.'
                                                        GROUP BY content_id
                                                      ORDER BY RAND() LIMIT 150');

                                        $total_row = count($search_result);
                                        $result = ['total_row' => $total_row, 'search_result' => $search_result];

                                        return $result;

                                    });
                                }

                                $featured_templates = Cache::get("defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache");
                                Redis::expire("defaultFeaturedTemplates$this->default_sub_category_id:$this->content_type_for_cache", 1);

                                if (! $featured_templates) {
                                    $featured_templates = [];
                                }

                                $total_row = $featured_templates['total_row'];

                                //get elements from array with start & end position
                                $search_result = array_slice($featured_templates['search_result'], $this->offset, $this->item_count);

                                $search_text = trim($this->search_category, '%');
                                $message = "Sorry, we couldn't find any templates for '$search_text', but we found some other templates you might like:";

                            }

                            if (trim($this->search_category) == '') {
                                $code = 200;
                            } else {
                                $code = 433;
                            }

                        } else {
                            $code = 200;
                            $message = 'Templates fetched successfully.';
                        }

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                        $result = ['code' => $code, 'message' => $message, 'result' => $search_result];

                        return $result;
                    });
                }
                $redis_result = Cache::get("searchTemplateBySubCategoryId$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count");
            }
            if (! $redis_result) {
                $redis_result = [];
                $message = "Sorry, we couldn't find any templates for '$search_text'.";
                $response = Response::json(['code' => '200', 'message' => $message, 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                if ($this->search_category != '') {
                    if ($this->is_cache == 1 && $this->page == 1) {
                        if ($redis_result['code'] == 200) {
                            //when data come from cache and status 200 means is success tag  so increment success count
                            SaveSearchTagJob::dispatch($redis_result['result']['total_record'], $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);

                        } else {
                            //when data come from cache and status not 200 means is fail tag so increment fail count
                            SaveSearchTagJob::dispatch(0, $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);
                        }
                    }
                }
                $response = Response::json(['code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']]);
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
        } catch (Exception $e) {
            (new ImageController())->logs('searchTemplateBySubCategoryId', $e);
            //      Log::error("searchTemplateBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function searchTemplateBySubCategoryIdBackUp2(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->default_sub_category_id = $this->sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : '';
            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->db_search_category = $this->search_category = isset($request->search_category) ? trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_category)), 0, 100)) : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type_for_cache = $this->content_type = isset($request->content_type) ? $request->content_type : 0;
            $this->select_column = $this->table_condition = $this->default_table_condition = $this->where_condition = $this->default_where_condition = $this->order_by_clause = $this->default_order_by_clause = $this->or_condition = null;
            $this->success_code = 200;
            $this->success_message = 'Templates fetched successfully.';
            $this->default_code = 433;
            $this->default_message = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
            $this->is_search_category_changed = 0;

            if ($this->content_type) {
                $this->content_type = ' AND content_type IN ('.$this->content_type.') ';
            } else {
                $this->content_type = ' AND content_type IN (4,9,10)';
            }

            run_same_query:
            //when user search from all sub_category and search tag is NULL
            if ($this->sub_category_id === 0 && $this->search_category == '') {

                //get comma separated list of featured sub_categories
                $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                $this->table_condition = ' AND FIND_IN_SET(scc.sub_category_id,"'.$this->default_sub_category_id.'")';

                $this->where_condition = ' AND cm.is_featured = 1 '.$this->content_type;

                //when user search from specific sub_category and search tag is NOT NULL
            } elseif ($this->sub_category_id !== 0 && $this->search_category != '') {

                $this->select_column = ' ,MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text';

                $this->where_condition = ' AND (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) '.$this->content_type;

                //below variable will use if data not found in specific sub_category for search tag
                $this->default_where_condition = $this->content_type;

                $this->order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, search_text DESC, ';

                //below variable will use if data not found in specific sub_category for search tag
                $this->default_order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, FIELD(cm.is_featured, 1) DESC, ';

                //when user search from all sub_category and search tag is NOT NULL
            } elseif ($this->sub_category_id === 0 && $this->search_category != '') {

                //get comma separated list of featured sub_categories
                $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                $this->select_column = ' ,MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text';

                //below variable will use if data not found in all sub_category for search tag
                $this->default_table_condition = ' AND FIND_IN_SET(scc.sub_category_id,"'.$this->default_sub_category_id.'")';

                $this->where_condition = ' AND (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) '.$this->content_type;

                //below variable will use if data not found in all sub_category for search tag
                $this->default_where_condition = ' AND cm.is_featured = 1 '.$this->content_type;

                $this->order_by_clause = ' search_text DESC, ';

                $is_exist = DB::select('SELECT content_ids FROM tag_master WHERE tag_name = ?', [$this->search_category]);
                if (count($is_exist) > 0) {
                    $this->content_ids = $is_exist[0]->content_ids;
                    if ($this->content_ids) {
                        $this->order_by_clause = ' FIELD (cm.id, '.$this->content_ids.') DESC, search_text DESC, ';
                        $this->or_condition = ' OR cm.id IN ('.$this->content_ids.')';
                    }
                }

                //when user search from specific sub_category and search tag is NOT NULL
            } elseif ($this->sub_category_id !== 0 && $this->search_category == '') {

                $this->order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, FIELD(cm.is_featured, 1) DESC, ';

                $this->where_condition = $this->content_type;
            }

//      dd($this->select_column, $this->table_condition, $this->default_table_condition, $this->where_condition, $this->default_where_condition, $this->order_by_clause, $this->default_order_by_clause);

            /*
              * create redis keys to manage 24 hour caching when user search from specific sub_category or all sub_category
              * this key is deleted only when any changes are made in database related to template
             */
            $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $total_row_result = DB::select('SELECT
                                            COUNT(DISTINCT cm.id) AS total
                                        FROM
                                            content_master AS cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->table_condition.'
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                            sub_category_master AS scm
                                        WHERE
                                            scc.sub_category_id = scm.id AND
                                            cm.is_active = 1 AND
                                            ISNULL(cm.original_img) AND
                                            ISNULL(cm.display_img)
                                            '.$this->where_condition.$this->or_condition.' ');

                    return $total_row = $total_row_result[0]->total;
                });

                if ($total_row) {
                    DB::statement("SET sql_mode = '' ");
                    $search_result = DB::select('SELECT
                                          cm.uuid AS content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                          coalesce(cm.is_featured,"") AS is_featured,
                                          ctm.uuid AS catalog_id,
                                          ctm.name AS catalog_name,
                                          cm.content_type,
                                          scm.uuid AS sub_category_id,
                                          COALESCE(cm.is_free,0) AS is_free,
                                          COALESCE(cm.is_portrait,0) AS is_portrait,
                                          COALESCE(cm.search_category,"") AS search_category,
                                          COALESCE(cm.height,0) AS height,
                                          COALESCE(cm.width,0) AS width,
                                          COALESCE(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          cm.update_time
                                          '.$this->select_column.'
                                       FROM
                                          content_master AS cm
                                          JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->table_condition.'
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                          sub_category_master AS scm
                                       WHERE
                                          scc.sub_category_id = scm.id AND
                                          cm.is_active = 1 AND
                                          ISNULL(cm.original_img) AND
                                          ISNULL(cm.display_img)
                                          '.$this->where_condition.$this->or_condition.'
                                       GROUP BY content_id
                                       ORDER BY '.$this->order_by_clause.' cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                    return ['code' => $this->success_code, 'message' => $this->success_message, 'result' => $search_result];

                } else {

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => []];

                    return ['code' => $this->default_code, 'message' => $this->default_message, 'result' => $search_result];
                }

            });

            if (! $redis_result['result']['total_record'] && ! $this->is_search_category_changed) {

                Redis::del(Config::get('constant.REDIS_KEY').':'.Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type:$this->page:$this->item_count");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_category, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_category) {
                    $this->search_category = trim($translate_data['data']['text']);
                    goto run_same_query;
                }
            }

            if ($redis_result['code'] == 433) {
                //caching time of redis key to get default featured templates
                $this->time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_FEATURED_TEMPLATES');

                /*
                 * If no data found of search category in all sub category or specific sub_category
                 * If user searched from specific sub_category then we provide following output from all sub_category(on priority of specific searched sub_category)
                 * If user searched from all sub_category then we provide following output from default featured sub_categories
                 * output = featured templates + normal templates (order by featured & update_time desc)
                */
                $redis_result = Cache::remember("defaultFeaturedTemplates:$this->default_sub_category_id:$this->content_type_for_cache:$this->page:$this->item_count", $this->time_of_expired_redis_key, function () {
                    DB::statement("SET sql_mode = '' ");
                    $total_row = Cache::remember("defaultFeaturedTemplates:$this->default_sub_category_id:$this->content_type_for_cache:$this->item_count", $this->time_of_expired_redis_key, function () {

                        $total_row_result = DB::select('SELECT
                                            count(DISTINCT cm.id) AS total
                                          FROM
                                            content_master AS cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->default_table_condition.'
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                            sub_category_master AS scm
                                          WHERE
                                            scc.sub_category_id = scm.id AND
                                            cm.is_active = 1 AND
                                            ISNULL(cm.original_img) AND
                                            ISNULL(cm.display_img)
                                            '.$this->default_where_condition.'
                                            ');

                        return $total_row = $total_row_result[0]->total;
                    });

                    $search_result = DB::select('SELECT
                                          cm.uuid AS content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                          coalesce(cm.is_featured,"") AS is_featured,
                                          ctm.uuid AS catalog_id,
                                          ctm.name AS catalog_name,
                                          cm.content_type,
                                          scm.uuid AS sub_category_id,
                                          COALESCE(cm.is_free,0) AS is_free,
                                          COALESCE(cm.is_portrait,0) AS is_portrait,
                                          COALESCE(cm.search_category,"") AS search_category,
                                          COALESCE(cm.height,0) AS height,
                                          COALESCE(cm.width,0) AS width,
                                          COALESCE(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          cm.update_time
                                       FROM
                                          content_master AS cm
                                          JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->default_table_condition.'
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                          sub_category_master AS scm
                                       WHERE
                                          scc.sub_category_id = scm.id AND
                                          cm.is_active = 1 AND
                                          ISNULL(cm.original_img) AND
                                          ISNULL(cm.display_img)
                                          '.$this->default_where_condition.'
                                       GROUP BY content_id
                                       ORDER BY '.$this->default_order_by_clause.' cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                    return ['code' => $this->default_code, 'message' => $this->default_message, 'result' => $search_result];

                });
                $redis_result['message'] = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
            }

            if (! $redis_result) {
                $redis_result = [];
                $response = Response::json(['code' => $this->default_code, 'message' => $this->default_message, 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                if ($this->search_category != '') {
                    if ($this->page == 1) {
                        if ($redis_result['code'] == 200) {
                            //when data come from cache and status 200 means is success tag  so increment success count
                            SaveSearchTagJob::dispatch($redis_result['result']['total_record'], $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);

                        } else {
                            //when data come from cache and status not 200 means is fail tag so increment fail count
                            SaveSearchTagJob::dispatch(0, $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);
                        }
                    }
                }
                $response = Response::json(['code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']]);
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error('searchTemplateBySubCategoryId : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function searchTemplateBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->default_sub_category_id = $this->sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : '';
            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->db_search_category = $this->search_category = isset($request->search_category) ? trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_category)), 0, 100)) : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type_for_cache = $this->content_type = isset($request->content_type) ? $request->content_type : 0;
            $this->is_free_for_cache = $this->is_free = isset($request->is_free) ? $request->is_free : null;
            $this->is_portrait_for_cache = $this->is_portrait = isset($request->is_portrait) ? $request->is_portrait : null;
            $this->select_column = $this->table_condition = $this->default_table_condition = $this->where_condition = $this->default_where_condition = $this->order_by_clause = $this->default_order_by_clause = $this->or_condition = null;
            $this->success_code = 200;
            $this->success_message = 'Templates fetched successfully.';
            $this->default_code = 433;
            $this->default_message = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
            $this->is_search_category_changed = 0;

            if ($this->content_type) {
                $this->content_type = ' AND cm.content_type IN ('.$this->content_type.') ';
            } else {
                $this->content_type = ' AND cm.content_type IN (4,9,10)';
            }

            if (! is_null($this->is_free)) {
                $this->is_free = ' AND cm.is_free = '.$this->is_free;
            }

            if (! is_null($this->is_portrait)) {
                $this->is_portrait = ' AND cm.is_portrait = '.$this->is_portrait;
            }

            run_same_query:
            //when user search from all sub_category and search tag is NULL
            if ($this->sub_category_id === 0 && $this->search_category == '') {

                //get comma separated list of featured sub_categories
                $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                $this->table_condition = ' AND FIND_IN_SET(scc.sub_category_id,"'.$this->default_sub_category_id.'")';

                $this->where_condition = ' AND cm.is_featured = 1 '.$this->content_type;

                //when user search from specific sub_category and search tag is NOT NULL
            } elseif ($this->sub_category_id !== 0 && $this->search_category != '') {

                $this->select_column = ' ,MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text';

                $this->where_condition = ' AND (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) '.$this->content_type;

                //below variable will use if data not found in specific sub_category for search tag
                $this->default_where_condition = $this->content_type;

                $this->order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, search_text DESC, ';

                //below variable will use if data not found in specific sub_category for search tag
                $this->default_order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, FIELD(cm.is_featured, 1) DESC, ';

                //when user search from all sub_category and search tag is NOT NULL
            } elseif ($this->sub_category_id === 0 && $this->search_category != '') {

                //get comma separated list of featured sub_categories
                $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                $this->select_column = ' ,MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text';

                //below variable will use if data not found in all sub_category for search tag
                $this->default_table_condition = ' AND FIND_IN_SET(scc.sub_category_id,"'.$this->default_sub_category_id.'")';

                $this->where_condition = ' AND (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) '.$this->content_type;

                //below variable will use if data not found in all sub_category for search tag
                $this->default_where_condition = ' AND cm.is_featured = 1 '.$this->content_type;

                $this->order_by_clause = ' search_text DESC, ';

                $is_exist = DB::select('SELECT content_ids FROM tag_master WHERE tag_name = ?', [$this->search_category]);
                if (count($is_exist) > 0) {
                    $this->content_ids = $is_exist[0]->content_ids;
                    if ($this->content_ids) {
                        $this->order_by_clause = ' FIELD (cm.id, '.$this->content_ids.') DESC, search_text DESC, ';
                        $this->or_condition = ' OR cm.id IN ('.$this->content_ids.')';
                    }
                }

                //when user search from specific sub_category and search tag is NOT NULL
            } elseif ($this->sub_category_id !== 0 && $this->search_category == '') {

                $this->order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, FIELD(cm.is_featured, 1) DESC, ';

                $this->where_condition = $this->content_type;
            }

//      dd($this->select_column, $this->table_condition, $this->default_table_condition, $this->where_condition, $this->default_where_condition, $this->order_by_clause, $this->default_order_by_clause);

            /*
              * create redis keys to manage 24 hour caching when user search from specific sub_category or all sub_category
              * this key is deleted only when any changes are made in database related to template
             */
            $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $total_row_result = DB::select('SELECT
                                            COUNT(DISTINCT cm.id) AS total
                                        FROM
                                            content_master AS cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->table_condition.'
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                            sub_category_master AS scm
                                        WHERE
                                            scc.sub_category_id = scm.id AND
                                            cm.is_active = 1 AND
                                            ISNULL(cm.original_img) AND
                                            ISNULL(cm.display_img)
                                            '.$this->where_condition.$this->or_condition.'
                                            '.$this->is_free.'
                                            '.$this->is_portrait.' ');

                    return $total_row = $total_row_result[0]->total;
                });

                if ($total_row) {
                    DB::statement("SET sql_mode = '' ");
                    $search_result = DB::select('SELECT
                                          cm.uuid AS content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                          coalesce(cm.is_featured,"") AS is_featured,
                                          ctm.uuid AS catalog_id,
                                          ctm.name AS catalog_name,
                                          cm.content_type,
                                          scm.uuid AS sub_category_id,
                                          COALESCE(cm.is_free,0) AS is_free,
                                          COALESCE(cm.is_portrait,0) AS is_portrait,
                                          COALESCE(cm.search_category,"") AS search_category,
                                          COALESCE(cm.height,0) AS height,
                                          COALESCE(cm.width,0) AS width,
                                          COALESCE(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          cm.update_time
                                          '.$this->select_column.'
                                       FROM
                                          content_master AS cm
                                          JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->table_condition.'
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                          sub_category_master AS scm
                                       WHERE
                                          scc.sub_category_id = scm.id AND
                                          cm.is_active = 1 AND
                                          ISNULL(cm.original_img) AND
                                          ISNULL(cm.display_img)
                                          '.$this->where_condition.$this->or_condition.'
                                          '.$this->is_free.'
                                          '.$this->is_portrait.'
                                       GROUP BY content_id
                                       ORDER BY '.$this->order_by_clause.' cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                    return ['code' => $this->success_code, 'message' => $this->success_message, 'result' => $search_result];

                } else {

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => []];

                    return ['code' => $this->default_code, 'message' => $this->default_message, 'result' => $search_result];
                }

            });

            if (! $redis_result['result']['total_record'] && ! $this->is_search_category_changed) {

                Redis::del(Config::get('constant.REDIS_KEY').':'.Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type:$this->page:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_category, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_category) {
                    $this->search_category = trim($translate_data['data']['text']);
                    goto run_same_query;
                }
            }

            if ($redis_result['code'] == 433) {
                //caching time of redis key to get default featured templates
                $this->time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_FEATURED_TEMPLATES');

                /*
                 * If no data found of search category in all sub category or specific sub_category
                 * If user searched from specific sub_category then we provide following output from all sub_category(on priority of specific searched sub_category)
                 * If user searched from all sub_category then we provide following output from default featured sub_categories
                 * output = featured templates + normal templates (order by featured & update_time desc)
                */
                $redis_result = Cache::remember("defaultFeaturedTemplates:$this->default_sub_category_id:$this->content_type_for_cache:$this->page:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache", $this->time_of_expired_redis_key, function () {
                    DB::statement("SET sql_mode = '' ");
                    $total_row = Cache::remember("defaultFeaturedTemplates:$this->default_sub_category_id:$this->content_type_for_cache:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache", $this->time_of_expired_redis_key, function () {

                        $total_row_result = DB::select('SELECT
                                            count(DISTINCT cm.id) AS total
                                          FROM
                                            content_master AS cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->default_table_condition.'
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                            sub_category_master AS scm
                                          WHERE
                                            scc.sub_category_id = scm.id AND
                                            cm.is_active = 1 AND
                                            ISNULL(cm.original_img) AND
                                            ISNULL(cm.display_img)
                                            '.$this->default_where_condition.'
                                            '.$this->is_free.'
                                            '.$this->is_portrait.'
                                            ');

                        return $total_row = $total_row_result[0]->total;
                    });

                    $search_result = DB::select('SELECT
                                          cm.uuid AS content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                          coalesce(cm.is_featured,"") AS is_featured,
                                          ctm.uuid AS catalog_id,
                                          ctm.name AS catalog_name,
                                          cm.content_type,
                                          scm.uuid AS sub_category_id,
                                          COALESCE(cm.is_free,0) AS is_free,
                                          COALESCE(cm.is_portrait,0) AS is_portrait,
                                          COALESCE(cm.search_category,"") AS search_category,
                                          COALESCE(cm.height,0) AS height,
                                          COALESCE(cm.width,0) AS width,
                                          COALESCE(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          cm.update_time
                                       FROM
                                          content_master AS cm
                                          JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$this->default_table_condition.'
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                          sub_category_master AS scm
                                       WHERE
                                          scc.sub_category_id = scm.id AND
                                          cm.is_active = 1 AND
                                          ISNULL(cm.original_img) AND
                                          ISNULL(cm.display_img)
                                          '.$this->default_where_condition.'
                                          '.$this->is_free.'
                                          '.$this->is_portrait.'
                                       GROUP BY content_id
                                       ORDER BY '.$this->default_order_by_clause.' cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                    return ['code' => $this->default_code, 'message' => $this->default_message, 'result' => $search_result];

                });
                $redis_result['message'] = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
            }

            if (! $redis_result) {
                $redis_result = [];
                $response = Response::json(['code' => $this->default_code, 'message' => $this->default_message, 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                if ($this->search_category != '') {
                    if ($this->page == 1) {
                        if ($redis_result['code'] == 200) {
                            //when data come from cache and status 200 means is success tag  so increment success count
                            SaveSearchTagJob::dispatch($redis_result['result']['total_record'], $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);

                        } else {
                            //when data come from cache and status not 200 means is fail tag so increment fail count
                            SaveSearchTagJob::dispatch(0, $this->search_category, 4, $this->sub_category_id, '', $this->content_type_for_cache);
                        }
                    }
                }
                $response = Response::json(['code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']]);
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error('searchTemplateBySubCategoryId : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getSuggestionTextsForTemplate",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getSuggestionTextsForTemplate",
     *        summary="Get suggestion texts for search template",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"category_id"},
     *
     *          @SWG\Property(property="category_id",  type="integer", example=1, description="1=Frame, 2=Sticker, 3=Background, 4=Templates, 5=Text, 6=3D Object"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"All templates are fetched successfully.","cause":"","data":{"total_record":170,"is_next_page":true,"result":{{"content_id":855,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c04fea203329_template_image_1543831202.jpg","is_featured":"1","catalog_id":54,"content_type":4,"is_free":1,"is_portrait":1,"height":450,"width":300,"color_value":"#1f4fa3","update_time":"2018-12-13 06:10:04","search_text":1.4999457597733},{"content_id":1375,"sample_image":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c08976b269ad_template_image_1544066923.jpg","is_featured":"1","catalog_id":37,"content_type":4,"is_free":1,"is_portrait":0,"height":540,"width":540,"color_value":"#4121d","update_time":"2018-12-06 03:28:43","search_text":1.4999457597733}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to save design.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function getSuggestionTextsForTemplate(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id'], $request)) != '') {
                return $response;
            }

            $this->category_id = $request->category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSuggestionTextsForTemplate$this->category_id")) {
                $result = Cache::rememberforever("getSuggestionTextsForTemplate$this->category_id", function () {

                    $category_id = Config::get('constant.CATEGORY_ID_OF_TEMPLATES');

                    $result = DB::select('SELECT
                                            TRIM(TRAILING "," FROM REPLACE(GROUP_CONCAT(DISTINCT cm.search_category, ","),",,",",")) search_tags
                                            FROM
                                              content_master AS cm JOIN
                                              sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1 INNER JOIN
                                              sub_category_master AS scm ON scm.id = scc.sub_category_id AND scc.is_active = 1 AND scm.is_active = 1 LEFT JOIN
                                              category as cat ON scm.category_id = cat.id AND cat.uuid =? ', [$category_id]);

                    return explode(',', implode(',', array_unique(explode(',', $result[0]->search_tags))));

                });
            }

            $redis_result = Cache::get("getSuggestionTextsForTemplate$this->category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Suggestion texts fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getSuggestionTextsForTemplate', $e);
            //      Log::error("getSuggestionTextsForTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get suggestion texts.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getTemplateByCatalogId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getTemplateByCatalogId",
     *        summary="Get templates by catalog id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id","catalog_id","page","item_count"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getTemplateByCatalogId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'catalog_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            //$this->item_count = $request->item_count;
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->page = $request->page;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {
                $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."getTemplateByCatalogId:$this->page:$this->item_count:$this->catalog_id:$this->sub_category_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    if ($this->catalog_id === 0) {

                        $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."getTemplateByCatalogId:$this->item_count:$this->catalog_id:$this->sub_category_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                            $total_row_result = DB::select('SELECT COUNT(id) AS total
                                                    FROM content_master
                                                    WHERE catalog_id IN (SELECT sct.catalog_id
                                                                     FROM sub_category_catalog as sct,sub_category_master as scm
                                                                     WHERE scm.uuid = ? AND sct.sub_category_id = scm.id) AND is_featured = 1', [$this->sub_category_id]);

                            return $total_row = $total_row_result[0]->total;

                        });

                        $result = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        where
                                          cm.catalog_id=ctm.id AND
                                          cm.is_active = 1 AND
                                          cm.is_featured = 1 AND
                                          cm.catalog_id in(select catalog_id FROM sub_category_catalog as sct,sub_category_master as scm WHERE scm.uuid = ? AND sct.sub_category_id = scm.id)
                                        ORDER BY cm.update_time DESC limit ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    } else {

                        $total_row = Cache::remember(Config::get('constant.REDIS_PREFIX')."getTemplateByCatalogId:$this->item_count:$this->catalog_id:$this->sub_category_id", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                            $total_row_result = DB::select('SELECT COUNT(cm.id) as total FROM content_master as cm,catalog_master as ctm WHERE ctm.id=cm.catalog_id AND ctm.uuid = ?', [$this->catalog_id]);

                            return $total_row = $total_row_result[0]->total;

                        });

                        $result = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        where
                                          ctm.id=cm.catalog_id AND
                                          cm.is_active = 1 AND
                                          ctm.uuid = ?
                                        order by cm.update_time DESC limit ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $result];
                });

            } else {

                if ($this->catalog_id === 0) {

                    $total_row_result = DB::select('SELECT COUNT(id) AS total
                                                    FROM content_master
                                                    WHERE catalog_id IN (SELECT sct.catalog_id
                                                                     FROM sub_category_catalog as sct,sub_category_master as scm
                                                                     WHERE scm.uuid = ? AND sct.sub_category_id = scm.id) AND is_featured = 1', [$this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        where
                                          cm.catalog_id=ctm.id AND
                                          cm.is_active = 1 AND
                                          cm.is_featured = 1 AND
                                          cm.catalog_id in(select catalog_id FROM sub_category_catalog as sct,sub_category_master as scm WHERE scm.uuid = ? AND sct.sub_category_id = scm.id)
                                        ORDER BY cm.update_time DESC limit ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                } else {

                    $total_row_result = DB::select('SELECT COUNT(cm.id) as total FROM content_master as cm,catalog_master as ctm WHERE ctm.id=cm.catalog_id AND ctm.uuid = ?', [$this->catalog_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          ctm.uuid as catalog_id,
                                          ctm.name as catalog_name,
                                          cm.content_type,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.search_category,"") as search_category,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          cm.update_time
                                        FROM
                                          content_master as cm,
                                          catalog_master as ctm
                                        where
                                          ctm.id=cm.catalog_id AND
                                          cm.is_active = 1 AND
                                          ctm.uuid = ?
                                        order by cm.update_time DESC limit ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $result];

            }

            $response = Response::json(['code' => 200, 'message' => 'All templates are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplateByCatalogId', $e);
            //      Log::error("getTemplateByCatalogId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getTemplatesByCatalogIdForDesignPage(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'catalog_id'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->brandkit_sub_category_id = Config::get('constant.SUB_CATEGORY_UUID_OF_BRANDKIT');

            if ($this->sub_category_id != $this->brandkit_sub_category_id) {
                Log::error('getTemplateByCatalogIdForDesignPage : Unable to match sub_category_id.', ['sub_category_id' => $this->sub_category_id]);

                return Response::json(['code' => 201, 'message' => 'Unable to match sub category.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $redis_result = Cache::remember("getTemplatesByCatalogIdForDesignPage$this->catalog_id:$this->sub_category_id", 1440, function () {

                $result = DB::select('SELECT
                                              cm.uuid AS content_id,
                                              ctm.uuid AS catalog_id,
                                              ctm.name AS catalog_name,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                              cm.content_type,
                                              coalesce(cm.height,0) AS height,
                                              coalesce(cm.width,0) AS width
                                            FROM
                                              content_master AS cm,
                                              catalog_master AS ctm
                                            WHERE
                                              ctm.id = cm.catalog_id AND
                                              cm.is_active = 1 AND
                                              ctm.uuid = ?
                                            ORDER BY cm.update_time DESC', [$this->catalog_id]);

                return $result;
            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All templates are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplatesByCatalogIdForDesignPage', $e);
            //Log::error("getTemplatesByCatalogIdForDesignPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getTemplateByCatalogNameAsTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'catalog_id', 'catalog_name', 'height', 'width', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;
            $this->sub_category_id = $request->sub_category_id;
            $this->content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            $this->search_category = trim(strtolower($request->catalog_name));
            $this->height = $request->height;
            $this->width = $request->width;
            $this->min_height = $this->height - 100;
            $this->min_width = $this->width - 100;
            $this->max_height = $this->height + 100;
            $this->max_width = $this->width + 100;
            //$this->item_count = $request->item_count;
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->page = $request->page;
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {

                $redis_result = Cache::remember("getTemplateByCatalogNameAsTag:$this->catalog_id:$this->sub_category_id:$this->content_type:$this->search_category:$this->height:$this->width:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    $total_row_result = DB::select('SELECT
                                                  cm.id
                                                FROM
                                                  content_master AS cm,
                                                  catalog_master AS ctm
                                                WHERE
                                                  cm.catalog_id = ctm.id AND
                                                  cm.is_active = 1 AND
                                                  ctm.is_featured = 1 AND
                                                  ctm.uuid != ? AND
                                                  cm.content_type = ? AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                GROUP BY cm.id', [$this->catalog_id, $this->content_type]);

                    $total_row = count($total_row_result);

                    DB::statement("SET sql_mode = '' ");
                    $search_result = DB::select('SELECT
                                                    cm.uuid AS content_id,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                                    ctm.uuid AS catalog_id,
                                                    ctm.name AS catalog_name,
                                                    cm.content_type,
                                                    COALESCE(cm.is_featured,"") AS is_featured,
                                                    COALESCE(cm.is_free,0) AS is_free,
                                                    COALESCE(cm.is_portrait,0) AS is_portrait,
                                                    COALESCE(cm.search_category,"") AS search_category,
                                                    COALESCE(cm.height,0) AS height,
                                                    COALESCE(cm.width,0) AS width,
                                                    COALESCE(cm.color_value,"") AS color_value,
                                                    COALESCE(cm.multiple_images,"") AS multiple_images,
                                                    COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                    cm.update_time
                                                FROM
                                                  content_master AS cm,
                                                  catalog_master AS ctm
                                                WHERE
                                                    cm.catalog_id = ctm.id AND
                                                    cm.is_active = 1 AND
                                                    ctm.is_featured = 1 AND
                                                    ctm.uuid != ? AND
                                                    cm.content_type = ? AND
                                                    ISNULL(cm.original_img) AND
                                                    ISNULL(cm.display_img) AND
                                                    (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                GROUP BY cm.id
                                                ORDER BY
                                                        CASE WHEN cm.height BETWEEN ? AND ? then 1 else 2 end,
                                                        CASE WHEN cm.width BETWEEN ? AND ? then 1 else 2 end,
                                                        cm.update_time DESC
                                                LIMIT ?,?', [$this->catalog_id, $this->content_type, $this->min_height, $this->max_height, $this->min_width, $this->max_width, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                    return $result;
                });

            } else {
                $total_row_result = DB::select('SELECT
                                                  cm.id
                                                FROM
                                                  content_master AS cm,
                                                  catalog_master AS ctm
                                                WHERE
                                                  cm.catalog_id = ctm.id AND
                                                  cm.is_active = 1 AND
                                                  ctm.is_featured = 1 AND
                                                  ctm.uuid != ? AND
                                                  cm.content_type = ? AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                GROUP BY cm.id', [$this->catalog_id, $this->content_type]);

                $total_row = count($total_row_result);

                DB::statement("SET sql_mode = '' ");
                $search_result = DB::select('SELECT
                                                    cm.uuid AS content_id,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                                    ctm.uuid AS catalog_id,
                                                    ctm.name AS catalog_name,
                                                    cm.content_type,
                                                    COALESCE(cm.is_featured,"") AS is_featured,
                                                    COALESCE(cm.is_free,0) AS is_free,
                                                    COALESCE(cm.is_portrait,0) AS is_portrait,
                                                    COALESCE(cm.search_category,"") AS search_category,
                                                    COALESCE(cm.height,0) AS height,
                                                    COALESCE(cm.width,0) AS width,
                                                    COALESCE(cm.color_value,"") AS color_value,
                                                    COALESCE(cm.multiple_images,"") AS multiple_images,
                                                    COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                    COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                                    cm.update_time
                                                FROM
                                                  content_master AS cm,
                                                  catalog_master AS ctm
                                                WHERE
                                                    cm.catalog_id = ctm.id AND
                                                    cm.is_active = 1 AND
                                                    ctm.is_featured = 1 AND
                                                    ctm.uuid != ? AND
                                                    cm.content_type = ? AND
                                                    ISNULL(cm.original_img) AND
                                                    ISNULL(cm.display_img) AND
                                                    (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                GROUP BY cm.id
                                                ORDER BY
                                                        CASE WHEN cm.height BETWEEN ? AND ? then 1 else 2 end,
                                                        CASE WHEN cm.width BETWEEN ? AND ? then 1 else 2 end,
                                                        cm.update_time DESC
                                                LIMIT ?,?', [$this->catalog_id, $this->content_type, $this->min_height, $this->max_height, $this->min_width, $this->max_width, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $redis_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];
            }
            $response = Response::json(['code' => 200, 'message' => 'All templates are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplateByCatalogNameAsTag', $e);
            //Log::error("getTemplateByCatalogNameAsTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Sticker |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchSticker",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchSticker",
     *        summary="Search stickers by catalog name",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"search_query","page","item_count","search_query"},
     *
     *          @SWG\Property(property="search_query",  type="string", example="Art & Design", description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Stickers fetched successfully.","cause":"","data":{"total_record":90,"is_next_page":true,"content_list":{{"content_id":3058,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce51b83d1edb_normal_image_1558518659.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce51b83d1edb_normal_image_1558518659.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce51b83d1edb_normal_image_1558518659.png","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce51b83d1edb_normal_image_1558518659.png","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-22 09:54:08"},{"content_id":3055,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce51b7edae47_normal_image_1558518654.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce51b7edae47_normal_image_1558518654.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce51b7edae47_normal_image_1558518654.png","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce51b7edae47_normal_image_1558518654.png","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-22 09:54:06"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search stickers.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function searchSticker(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'search_query'], $request)) != '') {
                return $response;
            }

            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_query = $search_text = trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_query)), 0, 100));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_search_category_changed = 0;

            //store result data into cache forever, this key is deleted only when any changes are made in database related to stickers.
            run_same_query_sticker:
            $redis_result = Cache::rememberforever("searchSticker$this->search_query:$this->page:$this->item_count", function () {

                $sub_category_list = DB::select('SELECT
                                             GROUP_CONCAT(scm.id) AS sub_category_ids
                                           FROM
                                             sub_category_master AS scm
                                           WHERE
                                             scm.category_id = 2 AND
                                             scm.is_active = 1');

                if (count($sub_category_list) > 0) {
                    DB::statement("SET sql_mode = '' ");
                    $total_row = Cache::remember("searchSticker:$this->search_query:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () use ($sub_category_list) {

                        $total_row_result = DB::select('SELECT
                                                COUNT(cm.id) AS total
                                            FROM
                                               content_master as cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active=1 AND ctm.is_featured = 0,
                                            sub_category_master AS scm
                                            WHERE
                                              scc.sub_category_id = scm.id AND
                                              cm.is_active = 1 AND
                                              isnull(cm.original_img) AND
                                              isnull(cm.display_img) AND
                                              (cm.content_type = ? OR cm.content_type = ?) AND
                                              find_in_set(scm.id,"'.$sub_category_list[0]->sub_category_ids.'") AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))',
                            [Config::get('constant.CONTENT_TYPE_OF_IMAGE'), Config::get('constant.CONTENT_TYPE_OF_SVG')]);

                        return $total_row = $total_row_result[0]->total;
                        //Log::info('total_row',['total_row' => $total_row]);
                    });

                    DB::statement("SET sql_mode = '' ");
                    $stickers = DB::select('SELECT
                                      cm.uuid as content_id,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                      cm.content_type,
                                      coalesce(cm.height,0) as height,
                                      coalesce(cm.width,0) as width,
                                      coalesce(cm.color_value,"") AS color_value,
                                      (select ctm.is_free from catalog_master as ctm where ctm.id=cm.catalog_id) as is_free,
                                      coalesce(cm.search_category,"") AS search_category,
                                      cm.update_time,
                                      MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                    FROM
                                      content_master as cm
                                      JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active=1 AND ctm.is_featured = 0,
                                      sub_category_master AS scm
                                    WHERE
                                      scc.sub_category_id = scm.id AND
                                      cm.is_active = 1 AND
                                      isnull(cm.original_img) AND
                                      isnull(cm.display_img) AND
                                      (cm.content_type = ? OR cm.content_type = ?) AND
                                      find_in_set(scm.id,"'.$sub_category_list[0]->sub_category_ids.'") AND
                                      (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                    GROUP BY content_id ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [Config::get('constant.CONTENT_TYPE_OF_IMAGE'), Config::get('constant.CONTENT_TYPE_OF_SVG'), $this->offset, $this->item_count]);

                    //            $stickers = DB::select('SELECT
                    //                                                      cm.uuid as content_id,
                    //                                                      IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as thumbnail_img,
                    //                                                      IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as compressed_img,
                    //                                                      IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as original_img,
                    //                                                      IF(cm.image != "",CONCAT("' . Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") as svg_image,
                    //                                                      cm.content_type,
                    //                                                      coalesce(cm.height,0) as height,
                    //                                                      coalesce(cm.width,0) as width,
                    //                                                      coalesce(cm.color_value,"") AS color_value,
                    //                                                      (select ctm.is_free from catalog_master as ctm where ctm.id=cm.catalog_id) as is_free,
                    //                                                      cm.update_time,
                    //                                                      MATCH(cm.search_category) AGAINST("' . $this->search_query . '") +
                    //                                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("' . $this->search_query . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                    //                                                    FROM
                    //                                                      content_master as cm
                    //                                                    WHERE
                    //                                                      cm.is_active = 1 AND
                    //                                                      isnull(cm.original_img) AND
                    //                                                      isnull(cm.display_img)AND
                    //                                                      (cm.content_type = ? OR cm.content_type = ?) AND
                    //                                                      find_in_set(cm.catalog_id,"' . $catalog_list[0]->catalog_ids . '") AND
                    //                                                      (MATCH(cm.search_category) AGAINST("' . $this->search_query . '") OR
                    //                                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("' . $this->search_query . '"," ")," ","* ")  IN BOOLEAN MODE))
                    //                                                    ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [Config::get('constant.CONTENT_TYPE_OF_IMAGE'), Config::get('constant.CONTENT_TYPE_OF_SVG'), $this->offset, $this->item_count]);

                } else {
                    $stickers = [];
                    $total_row = 0;
                    //Log::info('total_row 0',['total_row' => $total_row]);
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $stickers, 'sub_category_ids' => $sub_category_list[0]->sub_category_ids];

            });

            if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                Redis::del("Config::get('constant.REDIS_KEY'):searchSticker$this->search_query:$this->page:$this->item_count");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_query, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                    $this->search_query = trim($translate_data['data']['text']);
                    goto run_same_query_sticker;
                }
            }

            if ($redis_result['total_record'] <= 0) {
                $code = 201;
                $message = "Sorry, we couldn't find any Stickers for '$search_text'.";
            } else {
                $code = 200;
                $message = 'Stickers fetched successfully.';
            }

            //dispatch job for insert search tag detail into database.
            if ($this->page == 1) {
                SaveNormalImagesSearchTagJob::dispatch($redis_result['total_record'], $this->search_query, '', $redis_result['sub_category_ids'], 2, Config::get('constant.CONTENT_TYPE_OF_IMAGE').','.Config::get('constant.CONTENT_TYPE_OF_SVG'));
            }

            //unset sub_category_ids key from redis_result, so it cannot display in user side.
            unset($redis_result['sub_category_ids']);
            $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('searchSticker', $e);
            //      Log::error("searchSticker : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search sticker.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchBackground",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchBackground",
     *        summary="Search background by catalog name",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"search_query","page","item_count","search_query"},
     *
     *          @SWG\Property(property="search_query",  type="string", example="Art & Design", description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Backgrounds fetched successfully.","cause":"","data":{"total_record":233,"is_next_page":true,"content_list":{{"content_id":3002,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce237acab5e7_normal_image_1558329260.jpg","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce237acab5e7_normal_image_1558329260.jpg","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce237acab5e7_normal_image_1558329260.jpg","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce237acab5e7_normal_image_1558329260.jpg","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-20 05:14:20"},{"content_id":3001,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce237aa1a8eb_normal_image_1558329258.jpg","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce237aa1a8eb_normal_image_1558329258.jpg","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce237aa1a8eb_normal_image_1558329258.jpg","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce237aa1a8eb_normal_image_1558329258.jpg","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-20 05:14:18"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search backgrounds.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function searchBackground(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'search_query'], $request)) != '') {
                return $response;
            }

            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_query = $search_text = trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_query)), 0, 100));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_search_category_changed = 0;

            //store result data into cache forever, this key is deleted only when any changes are made in database related to background.
            run_same_query_background:
            $redis_result = Cache::rememberforever("searchBackground$this->search_query:$this->page:$this->item_count", function () {

                $catalog_list = DB::select('SELECT
                                                      GROUP_CONCAT(ctm.id) AS catalog_ids,
                                                      GROUP_CONCAT(DISTINCT(scm.id)) AS sub_category_ids
                                                    FROM
                                                      catalog_master AS ctm JOIN sub_category_catalog AS scc ON scc.catalog_id=ctm.id
                                                      JOIN sub_category_master AS scm ON scm.id = scc.sub_category_id AND scm.category_id = 3
                                                    WHERE
                                                      ctm.is_active = 1 AND
                                                      ctm.is_featured = 0');

                if (count($catalog_list) > 0) {

                    $total_row = Cache::remember("searchBackground:$this->search_query:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () use ($catalog_list) {

                        $total_row_result = DB::select('SELECT
                                                      COUNT(cm.id) AS total
                                                    FROM
                                                      content_master as cm
                                                    WHERE
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img)AND
                                                      (cm.content_type = ? OR cm.content_type = ?) AND
                                                      find_in_set(cm.catalog_id,"'.$catalog_list[0]->catalog_ids.'") AND
                                                      (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))', [Config::get('constant.CONTENT_TYPE_OF_IMAGE'), Config::get('constant.CONTENT_TYPE_OF_SVG')]);

                        return $total_row = $total_row_result[0]->total;
                        //Log::info('total_row',['total_row' => $total_row]);

                    });

                    $stickers = DB::select('SELECT
                                                      cm.uuid as content_id,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                      cm.content_type,
                                                      coalesce(cm.height,0) as height,
                                                      coalesce(cm.width,0) as width,
                                                      coalesce(cm.color_value,"") AS color_value,
                                                      (select ctm.is_free from catalog_master as ctm where ctm.id=cm.catalog_id) as is_free,
                                                      coalesce(cm.search_category,"") as search_category,
                                                      cm.update_time,
                                                      MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                    FROM
                                                      content_master as cm
                                                    WHERE
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img)AND
                                                      (cm.content_type = ? OR cm.content_type = ?) AND
                                                      find_in_set(cm.catalog_id,"'.$catalog_list[0]->catalog_ids.'") AND
                                                      (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                    ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?',
                        [
                            Config::get('constant.CONTENT_TYPE_OF_IMAGE'),
                            Config::get('constant.CONTENT_TYPE_OF_SVG'),
                            $this->offset,
                            $this->item_count]);

                } else {
                    $stickers = [];
                    $total_row = 0;
                    //Log::info('total_row 0',['total_row' => $total_row]);
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $stickers, 'catalog_ids' => $catalog_list[0]->catalog_ids, 'sub_category_ids' => $catalog_list[0]->sub_category_ids];

            });

            if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                Redis::del("Config::get('constant.REDIS_KEY'):searchBackground$this->search_query:$this->page:$this->item_count");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_query, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                    $this->search_query = trim($translate_data['data']['text']);
                    goto run_same_query_background;
                }
            }

            if ($redis_result['total_record'] <= 0) {
                $code = 201;
                $message = "Sorry, we couldn't find any Backgrounds for '$search_text'.";
            } else {
                $code = 200;
                $message = 'Backgrounds fetched successfully.';
            }

            //dispatch job for insert search tag detail into database.
            if ($this->page == 1) {
                SaveNormalImagesSearchTagJob::dispatch($redis_result['total_record'], $this->search_query, $redis_result['catalog_ids'], $redis_result['sub_category_ids'], 3, Config::get('constant.CONTENT_TYPE_OF_IMAGE').','.Config::get('constant.CONTENT_TYPE_OF_SVG'));
            }

            //unset catalog_ids and sub_category_ids keys from redis_result, so it cannot display in user side.
            unset($redis_result['catalog_ids']);
            unset($redis_result['sub_category_ids']);
            $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('searchBackground', $e);
            //      Log::error("searchBackground : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search background.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function searchStickerMCM(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'search_query'], $request)) != '') {
                return $response;
            }

            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_query = $search_text = trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_query)), 0, 100));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_search_category_changed = 0;

            //store result data into cache forever, this key is deleted only when any changes are made in database related to stickers.
            run_same_query_sticker:
            $redis_result = Cache::rememberforever("searchStickerMCM$this->search_query:$this->page:$this->item_count", function () {

                $sub_category_list = DB::select('SELECT
                                           GROUP_CONCAT(scm.id) AS sub_category_ids
                                         FROM
                                           sub_category_master AS scm
                                         WHERE
                                           scm.category_id = 11 AND
                                           scm.is_active = 1');

                if (count($sub_category_list) > 0) {
                    DB::statement("SET sql_mode = '' ");
                    $total_row = Cache::remember("searchStickerMCM:$this->search_query:$this->item_count", config('constant.CACHE_TIME_6_HOUR'), function () use ($sub_category_list) {

                        $total_row_result = DB::select('SELECT
                                              COUNT(cm.id) AS total
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active = 1 AND ctm.is_featured = 0,
                                              sub_category_master AS scm
                                            WHERE
                                              scc.sub_category_id = scm.id AND
                                              cm.is_active = 1 AND
                                              ISNULL(cm.original_img) AND
                                              ISNULL(cm.display_img) AND
                                              (cm.content_type = ? OR cm.content_type = ?) AND
                                              FIND_IN_SET(scm.id,"'.$sub_category_list[0]->sub_category_ids.'") AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))',
                            [config('constant.CONTENT_TYPE_OF_IMAGE'), config('constant.CONTENT_TYPE_OF_SVG')]);

                        return $total_row = $total_row_result[0]->total;
                    });

                    DB::statement("SET sql_mode = '' ");
                    $stickers = DB::select('SELECT
                                    cm.uuid AS content_id,
                                    IF(cm.image != "",CONCAT("'.config('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                    IF(cm.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                    IF(cm.image != "",CONCAT("'.config('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                    IF(cm.image != "",CONCAT("'.config('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS svg_image,
                                    COALESCE(cm.height,0) AS height,
                                    COALESCE(cm.width,0) AS width,
                                    COALESCE(cm.color_value,"") AS color_value,
                                    COALESCE(cm.search_category,"") AS search_category,
                                    (SELECT ctm.is_free FROM catalog_master AS ctm WHERE ctm.id = cm.catalog_id) AS is_free,
                                    cm.content_type,
                                    cm.update_time,
                                    MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                  FROM
                                    content_master AS cm
                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active = 1 AND ctm.is_featured = 0,
                                    sub_category_master AS scm
                                  WHERE
                                    scc.sub_category_id = scm.id AND
                                    cm.is_active = 1 AND
                                    ISNULL(cm.original_img) AND
                                    ISNULL(cm.display_img) AND
                                    (cm.content_type = ? OR cm.content_type = ?) AND
                                    FIND_IN_SET(scm.id,"'.$sub_category_list[0]->sub_category_ids.'") AND
                                    (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                  GROUP BY content_id ORDER BY search_text DESC, cm.update_time DESC LIMIT ?, ?', [config('constant.CONTENT_TYPE_OF_IMAGE'), config('constant.CONTENT_TYPE_OF_SVG'), $this->offset, $this->item_count]);
                } else {
                    $stickers = [];
                    $total_row = 0;
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $stickers, 'sub_category_ids' => $sub_category_list[0]->sub_category_ids];

            });

            if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                Redis::del("Config::get('constant.REDIS_KEY'):searchStickerMCM$this->search_query:$this->page:$this->item_count");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_query, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                    $this->search_query = trim($translate_data['data']['text']);
                    goto run_same_query_sticker;
                }
            }

            if ($redis_result['total_record'] <= 0) {
                $code = 201;
                $message = "Sorry, we couldn't find any Stickers for '$search_text'.";
            } else {
                $code = 200;
                $message = 'Stickers fetched successfully.';
            }

            //dispatch job for insert search tag detail into database.
            if ($this->page == 1) {
                //SaveNormalImagesSearchTagJob::dispatch($redis_result['total_record'], $this->search_query, '', $redis_result['sub_category_ids'], 2, config('constant.CONTENT_TYPE_OF_IMAGE') . "," . config('constant.CONTENT_TYPE_OF_SVG'));
            }

            //unset sub_category_ids key from redis_result, so it cannot display in user side.
            unset($redis_result['sub_category_ids']);
            $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', config('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('searchStickerMCM', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'search sticker.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function searchBackgroundMCM(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'search_query'], $request)) != '') {
                return $response;
            }

            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_query = $search_text = trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_query)), 0, 100));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_search_category_changed = 0;

            //store result data into cache forever, this key is deleted only when any changes are made in database related to background.
            run_same_query_background:
            $redis_result = Cache::rememberforever("searchBackgroundMCM$this->search_query:$this->page:$this->item_count", function () {
                $catalog_list = DB::select('SELECT
                                      GROUP_CONCAT(ctm.id) AS catalog_ids,
                                      GROUP_CONCAT(DISTINCT(scm.id)) AS sub_category_ids
                                    FROM
                                      catalog_master AS ctm JOIN sub_category_catalog AS scc ON scc.catalog_id = ctm.id
                                      JOIN sub_category_master AS scm ON scm.id = scc.sub_category_id AND scm.category_id = 12
                                    WHERE
                                      ctm.is_active = 1 AND
                                      ctm.is_featured = 0');

                if (count($catalog_list) > 0) {
                    $total_row = Cache::remember("searchBackgroundMCM:$this->search_query:$this->item_count", config('constant.CACHE_TIME_6_HOUR'), function () use ($catalog_list) {

                        $total_row_result = DB::select('SELECT
                                              COUNT(cm.id) AS total
                                            FROM
                                              content_master AS cm
                                            WHERE
                                              cm.is_active = 1 AND
                                              ISNULL(cm.original_img) AND
                                              ISNULL(cm.display_img) AND
                                              (cm.content_type = ? OR cm.content_type = ?) AND
                                              FIND_IN_SET(cm.catalog_id,"'.$catalog_list[0]->catalog_ids.'") AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))', [config('constant.CONTENT_TYPE_OF_IMAGE'), config('constant.CONTENT_TYPE_OF_SVG')]);

                        return $total_row = $total_row_result[0]->total;
                    });

                    $stickers = DB::select('SELECT
                                    cm.uuid AS content_id,
                                    IF(cm.image != "",CONCAT("'.config('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                    IF(cm.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                    IF(cm.image != "",CONCAT("'.config('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                    IF(cm.image != "",CONCAT("'.config('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS svg_image,
                                    COALESCE(cm.height,0) AS height,
                                    COALESCE(cm.width,0) AS width,
                                    COALESCE(cm.color_value,"") AS color_value,
                                    COALESCE(cm.search_category,"") AS search_category,
                                    (SELECT ctm.is_free FROM catalog_master AS ctm WHERE ctm.id = cm.catalog_id) AS is_free,
                                    cm.content_type,
                                    cm.update_time,
                                    MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                  FROM
                                    content_master as cm
                                  WHERE
                                    cm.is_active = 1 AND
                                    ISNULL(cm.original_img) AND
                                    ISNULL(cm.display_img) AND
                                    (cm.content_type = ? OR cm.content_type = ?) AND
                                    FIND_IN_SET(cm.catalog_id,"'.$catalog_list[0]->catalog_ids.'") AND
                                    (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                    MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                  ORDER BY search_text DESC, cm.update_time DESC LIMIT ?, ?',
                        [
                            config('constant.CONTENT_TYPE_OF_IMAGE'),
                            config('constant.CONTENT_TYPE_OF_SVG'),
                            $this->offset,
                            $this->item_count]);

                } else {
                    $stickers = [];
                    $total_row = 0;
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $stickers, 'catalog_ids' => $catalog_list[0]->catalog_ids, 'sub_category_ids' => $catalog_list[0]->sub_category_ids];

            });

            if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                Redis::del("Config::get('constant.REDIS_KEY'):searchBackgroundMCM$this->search_query:$this->page:$this->item_count");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_query, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                    $this->search_query = trim($translate_data['data']['text']);
                    goto run_same_query_background;
                }
            }

            if ($redis_result['total_record'] <= 0) {
                $code = 201;
                $message = "Sorry, we couldn't find any Backgrounds for '$search_text'.";
            } else {
                $code = 200;
                $message = 'Backgrounds fetched successfully.';
            }

            //dispatch job for insert search tag detail into database.
            if ($this->page == 1) {
                //SaveNormalImagesSearchTagJob::dispatch($redis_result['total_record'], $this->search_query, $redis_result['catalog_ids'], $redis_result['sub_category_ids'], 3, config('constant.CONTENT_TYPE_OF_IMAGE') . "," . config('constant.CONTENT_TYPE_OF_SVG'));
            }

            //unset catalog_ids and sub_category_ids keys from redis_result, so it cannot display in user side.
            unset($redis_result['catalog_ids']);
            unset($redis_result['sub_category_ids']);
            $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', config('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('searchBackgroundMCM', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'search background.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchTextArt",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchTextArt",
     *        summary="Search stickers by catalog name",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id","page","item_count","search_query"},
     *
     *          @SWG\Property(property="sub_category_id",  type="string", example="dsfser", description=""),
     *          @SWG\Property(property="search_query",  type="string", example="Art & Design", description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Stickers fetched successfully.","cause":"","data":{"total_record":90,"is_next_page":true,"content_list":{{"content_id":3058,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce51b83d1edb_normal_image_1558518659.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce51b83d1edb_normal_image_1558518659.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce51b83d1edb_normal_image_1558518659.png","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce51b83d1edb_normal_image_1558518659.png","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-22 09:54:08"},{"content_id":3055,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce51b7edae47_normal_image_1558518654.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce51b7edae47_normal_image_1558518654.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce51b7edae47_normal_image_1558518654.png","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce51b7edae47_normal_image_1558518654.png","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-22 09:54:06"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search stickers.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function searchTextArt(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'page', 'item_count', 'search_query'], $request)) != '') {
                return $response;
            }

            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_query = $search_text = trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_query)), 0, 100));
            $this->sub_category_id = $request->sub_category_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_search_category_changed = 0;

            //convert sub_category_id string to integer
            $sub_category_detail = DB::select('SELECT
                                                   id
                                               FROM
                                                   sub_category_master AS scm
                                               WHERE
                                                  scm.uuid = ?', [$this->sub_category_id]);
            $sub_category_int_id = $sub_category_detail[0]->id;

            //store result data into cache forever, this key is deleted only when any changes are made in database related to TextArt.
            run_same_query_textart:
            $redis_result = Cache::rememberforever("searchTextArt:$this->sub_category_id:$this->search_query:$this->page:$this->item_count", function () {

                DB::statement("SET sql_mode = '' ");
                $total_row_result = Cache::remember("searchTextArt:$this->sub_category_id:$this->search_query:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    return $total_row_result = DB::select('SELECT
                                                COUNT(cm.id) AS total
                                            FROM
                                               content_master as cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active=1 AND ctm.is_featured = 0,
                                            sub_category_master AS scm
                                            WHERE
                                              scc.sub_category_id = scm.id AND
                                              cm.is_active = 1 AND
                                              isnull(cm.original_img) AND
                                              isnull(cm.display_img)AND
                                              (cm.content_type = ? OR cm.content_type = ?) AND
                                              scm.uuid = ? AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))',
                        [Config::get('constant.CONTENT_TYPE_OF_IMAGE'), Config::get('constant.CONTENT_TYPE_OF_SVG'), $this->sub_category_id]);

                });

                $total_row = $total_row_result[0]->total;
                if (count($total_row_result) > 0) {
                    DB::statement("SET sql_mode = '' ");
                    $stickers = DB::select('SELECT
                                      cm.uuid as content_id,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                      cm.content_type,
                                      coalesce(cm.height,0) as height,
                                      coalesce(cm.width,0) as width,
                                      coalesce(cm.color_value,"") AS color_value,
                                      (select ctm.is_free from catalog_master as ctm where ctm.id=cm.catalog_id) as is_free,
                                      coalesce(cm.search_category,"") as search_category,
                                      cm.update_time,
                                      MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                    FROM
                                      content_master as cm
                                      JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_active=1 AND ctm.is_featured = 0,
                                      sub_category_master AS scm
                                    WHERE
                                      scc.sub_category_id = scm.id AND
                                      cm.is_active = 1 AND
                                      isnull(cm.original_img) AND
                                      isnull(cm.display_img) AND
                                      (cm.content_type = ? OR cm.content_type = ?) AND
                                      scm.uuid =? AND
                                      (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                      MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                    GROUP BY content_id ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [Config::get('constant.CONTENT_TYPE_OF_IMAGE'), Config::get('constant.CONTENT_TYPE_OF_SVG'), $this->sub_category_id, $this->offset, $this->item_count]);

                } else {
                    $stickers = [];
                    $total_row = 0;
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $stickers];

            });

            if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                Redis::del("Config::get('constant.REDIS_KEY'):searchTextArt:$this->sub_category_id:$this->search_query:$this->page:$this->item_count");
                $this->is_search_category_changed = 1;
                $translate_data = $this->translateLanguage($this->search_query, 'en');

                if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                    $this->search_query = trim($translate_data['data']['text']);
                    goto run_same_query_textart;
                }
            }

            if ($redis_result['total_record'] <= 0) {
                $code = 201;
                $message = "Sorry, we couldn't find any TextArt for '$search_text'.";
            } else {
                $code = 200;
                $message = 'TextArt fetched successfully.';
            }

            //dispatch job for insert search tag detail into database.
            if ($this->page == 1) {
                SaveNormalImagesSearchTagJob::dispatch($redis_result['total_record'], $this->search_query, '', $sub_category_int_id, 2, Config::get('constant.CONTENT_TYPE_OF_IMAGE').','.Config::get('constant.CONTENT_TYPE_OF_SVG'));
            }

            $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('searchTextArt', $e);
            //      Log::error("searchTextArt : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search textart.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchAudioOrVideo",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchAudioOrVideo",
     *        summary="Search audio or video by catalog name",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"search_query","page","item_count","content_type"},
     *
     *          @SWG\Property(property="search_query",  type="string", example="Art & Design", description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *          @SWG\Property(property="content_type",  type="integer", example=2, description="2=video,3=audio"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Audio/Video fetched successfully.","cause":"","data":{"total_record":233,"is_next_page":true,"content_list":{{"content_id":3002,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce237acab5e7_normal_image_1558329260.jpg","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce237acab5e7_normal_image_1558329260.jpg","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce237acab5e7_normal_image_1558329260.jpg","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce237acab5e7_normal_image_1558329260.jpg","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-20 05:14:20"},{"content_id":3001,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5ce237aa1a8eb_normal_image_1558329258.jpg","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5ce237aa1a8eb_normal_image_1558329258.jpg","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5ce237aa1a8eb_normal_image_1558329258.jpg","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5ce237aa1a8eb_normal_image_1558329258.jpg","content_type":1,"height":0,"width":0,"color_value":"","is_free":1,"update_time":"2019-05-20 05:14:18"}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search Audio/Video.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function searchAudioOrVideo(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'search_query', 'content_type'], $request)) != '') {
                return $response;
            }

            //Remove '[\\\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->search_query = $search_text = trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_query)), 0, 100));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->content_type = $request->content_type;
            $content_type = $request->content_type;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->is_search_category_changed = 0;

            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO')) {
                //store result data into cache forever, this key is deleted only when any changes are made in database related to video.
                run_same_query_video:
                $redis_result = Cache::rememberforever("searchAudioOrVideo:$this->search_query:$this->page:$this->item_count:$this->content_type", function () {
                    DB::statement("SET sql_mode = '' ");
                    $total_row_result = Cache::rememberforever("searchAudioOrVideo:$this->search_query:$this->item_count:$this->content_type", function () {

                        DB::statement("SET sql_mode = '' ");

                        return $total_row_result = DB::select('SELECT
                                              COUNT(cm.id) AS total,
                                              scc.sub_category_id
                                            FROM
                                              content_master as cm
                                              LEFT JOIN sub_category_catalog AS scc ON scc.catalog_id = cm.catalog_id
                                            WHERE
                                              cm.is_active = 1 AND
                                              isnull(cm.original_img) AND
                                              isnull(cm.display_img)AND
                                              cm.content_type = ? AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))', [Config::get('constant.CONTENT_TYPE_OF_VIDEO')]);

                    });

                    $total_row = $total_row_result[0]->total;
                    $sub_category_id = $total_row_result[0]->sub_category_id;
                    //Log::info('total_row',['total_row' => $total_row]);

                    $videos = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                          cm.content_file as video_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                          cm.content_type,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          (select ctm.is_free from catalog_master as ctm where ctm.id=cm.catalog_id) as is_free,
                                          cm.update_time,
                                          MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                          MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                        FROM
                                          content_master as cm
                                        WHERE
                                          cm.is_active = 1 AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)AND
                                          cm.content_type = ? AND
                                          (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                          MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                        ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?',
                        [
                            Config::get('constant.CONTENT_TYPE_OF_VIDEO'),
                            $this->offset,
                            $this->item_count]);
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $videos, 'sub_category_id' => $sub_category_id];

                });

                if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                    Redis::del("Config::get('constant.REDIS_KEY'):searchAudioOrVideo:$this->search_query:$this->page:$this->item_count:$this->content_type");
                    $this->is_search_category_changed = 1;
                    $translate_data = $this->translateLanguage($this->search_query, 'en');

                    if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                        $this->search_query = trim($translate_data['data']['text']);
                        goto run_same_query_video;
                    }
                }

            } elseif ($content_type == Config::get('constant.CONTENT_TYPE_OF_AUDIO')) {
                //store result data into cache forever, this key is deleted only when any changes are made in database related to audio.
                run_same_query_audio:
                $redis_result = Cache::rememberforever("searchAudioOrVideo:$this->search_query:$this->page:$this->item_count:$this->content_type", function () {
                    DB::statement("SET sql_mode = '' ");

                    $total_row_result = Cache::rememberforever("searchAudioOrVideo:$this->search_query:$this->item_count:$this->content_type", function () {

                        return $total_row_result = DB::select('SELECT
                                              COUNT(cm.id) AS total,
                                              scc.sub_category_id
                                            FROM
                                              content_master as cm
                                              LEFT JOIN sub_category_catalog AS scc ON scc.catalog_id = cm.catalog_id
                                            WHERE
                                              cm.is_active = 1 AND
                                              isnull(cm.original_img) AND
                                              isnull(cm.display_img) AND
                                              cm.content_type = ? AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))', [Config::get('constant.CONTENT_TYPE_OF_AUDIO')]);
                    });
                    $total_row = $total_row_result[0]->total;
                    $sub_category_id = $total_row_result[0]->sub_category_id;

                    $audio = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                          cm.content_file as video_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                          IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                          cm.content_type,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          (select ctm.is_free from catalog_master as ctm where ctm.id=cm.catalog_id) as is_free,
                                          cm.update_time,
                                          coalesce(am.credit_note,"") as credit_note,
                                          coalesce(am.tag,"") as tag,
                                          MATCH(cm.search_category) AGAINST("'.$this->search_query.'") +
                                          MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                        FROM
                                          content_master as cm
                                          LEFT JOIN audio_master am ON am.content_id = cm.id
                                        WHERE
                                          cm.is_active = 1 AND
                                          isnull(cm.original_img) AND
                                          isnull(cm.display_img)AND
                                          cm.content_type = ? AND
                                          (MATCH(cm.search_category) AGAINST("'.$this->search_query.'") OR
                                          MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE))
                                        ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?',
                        [
                            Config::get('constant.CONTENT_TYPE_OF_AUDIO'),
                            $this->offset,
                            $this->item_count]);

                    foreach ($audio as $key) {
                        if ($key->credit_note != '') {
                            $key->credit_note = json_decode($key->credit_note);
                        }
                    }
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $audio, 'sub_category_id' => $sub_category_id];
                });

                if (! $redis_result['total_record'] && ! $this->is_search_category_changed) {

                    Redis::del("Config::get('constant.REDIS_KEY'):searchAudioOrVideo:$this->search_query:$this->page:$this->item_count:$this->content_type");
                    $this->is_search_category_changed = 1;
                    $translate_data = $this->translateLanguage($this->search_query, 'en');

                    if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_query) {
                        $this->search_query = trim($translate_data['data']['text']);
                        goto run_same_query_audio;
                    }
                }

            }

            if ($redis_result['total_record'] <= 0) {
                $code = 201;
                $message = "Sorry, we couldn't find any Audio/Video for '$search_text'.";
            } else {
                $code = 200;
                $message = 'Audio/Video fetched successfully.';
            }

            //dispatch job for insert search tag detail into database.
            if ($this->page == 1) {
                SaveNormalImagesSearchTagJob::dispatch($redis_result['total_record'], $this->search_query, '', $redis_result['sub_category_id'], 9, $content_type);
            }

            //unset sub_category_id key from redis_result, so it cannot display in user side.
            unset($redis_result['sub_category_id']);

            $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('searchAudioOrVideo', $e);
            //      Log::error("searchAudioOrVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search audio/video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getNormalCatalogsBySubCategoryId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getNormalCatalogsBySubCategoryId",
     *        summary="Get normal catalogs by sub category id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"sub_category_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=10, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Catalogs fetched successfully.","cause":"","data":{"result":{{"catalog_id":57,"name":"png stickers","thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c273994e1fac_catalog_img_1546074516.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c273994e1fac_catalog_img_1546074516.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5c273994e1fac_catalog_img_1546074516.png","is_free":1,"is_featured":0,"total_record":0,"is_next_page":false,"content_list":{}},{"catalog_id":56,"name":"New Stickers","thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c134fe1c48c0_catalog_img_1544769505.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c134fe1c48c0_catalog_img_1544769505.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5c134fe1c48c0_catalog_img_1544769505.png","is_free":0,"is_featured":0,"total_record":274,"is_next_page":true,"content_list":{{"content_id":2693,"thumbnail_img":"http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5cc13b4a99ab9_normal_image_1556167498.png","compressed_img":"http://192.168.0.113/photoadking_testing/image_bucket/compressed/5cc13b4a99ab9_normal_image_1556167498.png","original_img":"http://192.168.0.113/photoadking_testing/image_bucket/original/5cc13b4a99ab9_normal_image_1556167498.png","svg_image":"http://192.168.0.113/photoadking_testing/image_bucket/svg/5cc13b4a99ab9_normal_image_1556167498.png","content_type":1,"height":0,"width":0,"color_value":"","is_free":0,"update_time":"2019-04-24 23:14:59"}}}}}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to get catalogs.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function getNormalCatalogsBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->catalog_id = isset($request->catalog_id) ? $request->catalog_id : 0;
            $this->page = isset($request->page) ? $request->page : 1;
            $this->item_count = isset($request->item_count) ? $request->item_count : Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->offset = ($this->page - 1) * $this->item_count;
            $is_cache_enable = isset($request->is_cache_enable) ? $request->is_cache_enable : 1;

            if ($is_cache_enable) {

                $redis_result = Cache::remember(Config::get('constant.REDIS_PREFIX')."getNormalCatalogsBySubCategoryId:$this->sub_category_id:$this->catalog_id:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_6_HOUR'), function () {

                    if ($this->catalog_id === 0) {
                        $catalog_list = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        sct.sub_category_id = scm.id AND
                                        scm.uuid= ? AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=0
                                      ORDER BY FIELD(ctm.id, 27) DESC, ctm.update_time DESC', [$this->sub_category_id]);

                        foreach ($catalog_list as $key) {
                            if ($catalog_list[0]->catalog_id == $key->catalog_id) {

                                $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id = ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$catalog_list[0]->catalog_id]);

                                $total_row = $total_row_result[0]->total;

                                $catalog_content = DB::select('SELECT
                                                      cm.uuid as content_id,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                                      cm.content_type,
                                                      coalesce(cm.height,0) as height,
                                                      coalesce(cm.width,0) as width,
                                                      coalesce(cm.color_value,"") AS color_value,
                                                      '.$key->is_free.' AS is_free,
                                                      coalesce(cm.search_category,"") as search_category,
                                                      cm.update_time,
                                                      coalesce(am.credit_note,"") as credit_note,
                                                      coalesce(am.tag,"") as tag
                                                    FROM
                                                      content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                                      catalog_master as ctm
                                                    WHERE
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img)AND
                                                      cm.catalog_id = ctm.id AND
                                                      ctm.uuid = ?
                                                    ORDER BY cm.update_time DESC LIMIT ?, ?', [$catalog_list[0]->catalog_id, $this->offset, $this->item_count]);
                                foreach ($catalog_content as $content_key) {
                                    if ($content_key->credit_note != '') {
                                        $content_key->credit_note = json_decode($content_key->credit_note);
                                    }
                                }

                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $catalog_list[0]->content_list = $catalog_content;
                            } else {
                                $total_row = 0;
                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $key->content_list = [];

                            }
                        }
                    } else {
                        $catalog_list = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        sct.sub_category_id = scm.id  AND
                                        scm.uuid = ? AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=0
                                      ORDER BY FIELD(ctm.id, 27) DESC, ctm.update_time DESC', [$this->sub_category_id]);

                        foreach ($catalog_list as $key) {
                            if ($this->catalog_id == $key->catalog_id) {

                                $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id = ctm.id AND
                                                                  ctm.uuid =? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$this->catalog_id]);

                                $total_row = $total_row_result[0]->total;

                                $catalog_content = DB::select('SELECT
                                                      cm.uuid as content_id,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                                      cm.content_type,
                                                      coalesce(cm.height,0) as height,
                                                      coalesce(cm.width,0) as width,
                                                      coalesce(cm.color_value,"") AS color_value,
                                                      '.$key->is_free.' AS is_free,
                                                      coalesce(cm.search_category,"") as search_category,
                                                      cm.update_time,
                                                      coalesce(am.credit_note,"") as credit_note,
                                                      coalesce(am.tag,"") as tag
                                                    FROM
                                                      content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                                      catalog_master as ctm
                                                    WHERE
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img) AND
                                                      ctm.id =cm.catalog_id AND
                                                      ctm.uuid = ?
                                                    ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $key->content_list = $catalog_content;

                                foreach ($catalog_content as $content_key) {
                                    if ($content_key->credit_note != '') {
                                        $content_key->credit_note = json_decode($content_key->credit_note);
                                    }
                                }
                            } else {
                                $total_row = 0;
                                $key->total_record = $total_row;
                                $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                                $key->content_list = [];

                            }
                        }
                    }

                    return $catalog_list;

                });
            } else {
                if ($this->catalog_id === 0) {
                    $redis_result = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        sct.sub_category_id = scm.id AND
                                        scm.uuid= ? AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=0
                                      ORDER BY FIELD(ctm.id, 27) DESC, ctm.update_time DESC', [$this->sub_category_id]);

                    foreach ($redis_result as $key) {
                        if ($redis_result[0]->catalog_id == $key->catalog_id) {

                            $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id = ctm.id AND
                                                                  ctm.uuid = ? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$redis_result[0]->catalog_id]);

                            $total_row = $total_row_result[0]->total;

                            $catalog_content = DB::select('SELECT
                                                      cm.uuid as content_id,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                                      cm.content_type,
                                                      coalesce(cm.height,0) as height,
                                                      coalesce(cm.width,0) as width,
                                                      coalesce(cm.color_value,"") AS color_value,
                                                      '.$key->is_free.' AS is_free,
                                                      coalesce(cm.search_category,"") as search_category,
                                                      cm.update_time,
                                                      coalesce(am.credit_note,"") as credit_note,
                                                      coalesce(am.tag,"") as tag
                                                    FROM
                                                      content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                                      catalog_master as ctm
                                                    WHERE
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img)AND
                                                      cm.catalog_id = ctm.id AND
                                                      ctm.uuid = ?
                                                    ORDER BY cm.update_time DESC LIMIT ?, ?', [$redis_result[0]->catalog_id, $this->offset, $this->item_count]);
                            foreach ($catalog_content as $content_key) {
                                if ($content_key->credit_note != '') {
                                    $content_key->credit_note = json_decode($content_key->credit_note);
                                }
                            }

                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $redis_result[0]->content_list = $catalog_content;
                        } else {
                            $total_row = 0;
                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $key->content_list = [];

                        }
                    }
                } else {
                    $redis_result = DB::select('SELECT
                                        ctm.uuid as catalog_id,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") as original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                      FROM
                                        catalog_master as ctm,
                                        sub_category_catalog as sct,
                                        sub_category_master as scm
                                      WHERE
                                        sct.sub_category_id = scm.id  AND
                                        scm.uuid = ? AND
                                        sct.catalog_id=ctm.id AND
                                        ctm.is_active=1 AND
                                        ctm.is_featured=0
                                      ORDER BY FIELD(ctm.id, 27) DESC, ctm.update_time DESC', [$this->sub_category_id]);

                    foreach ($redis_result as $key) {
                        if ($this->catalog_id == $key->catalog_id) {

                            $total_row_result = DB::select('SELECT
                                                                  COUNT(cm.id) AS total
                                                                FROM
                                                                  content_master as cm,
                                                                  catalog_master as ctm
                                                                where
                                                                  cm.is_active = 1 AND
                                                                  cm.catalog_id = ctm.id AND
                                                                  ctm.uuid =? AND
                                                                  isnull(cm.original_img) AND
                                                                  isnull(cm.display_img)
                                                                order by cm.update_time DESC', [$this->catalog_id]);

                            $total_row = $total_row_result[0]->total;

                            $catalog_content = DB::select('SELECT
                                                      cm.uuid as content_id,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as compressed_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_original_img,
                                                      IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail_img,
                                                      IF(cm.image != "",CONCAT("'.Config::get('constant.SVG_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as svg_image,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as video_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 2,CONCAT(cm.content_file),"") AS video_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT("'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as audio_file,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(cm.content_file),"") AS audio_name,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.title),"") AS title,
                                                      IF(cm.content_file != "" AND cm.content_type = 3,CONCAT(am.duration),"") AS duration,
                                                      cm.content_type,
                                                      coalesce(cm.height,0) as height,
                                                      coalesce(cm.width,0) as width,
                                                      coalesce(cm.color_value,"") AS color_value,
                                                      '.$key->is_free.' AS is_free,
                                                      coalesce(cm.search_category,"") as search_category,
                                                      cm.update_time,
                                                      coalesce(am.credit_note,"") as credit_note,
                                                      coalesce(am.tag,"") as tag
                                                    FROM
                                                      content_master as cm LEFT JOIN audio_master am ON am.content_id = cm.id,
                                                      catalog_master as ctm
                                                    WHERE
                                                      cm.is_active = 1 AND
                                                      isnull(cm.original_img) AND
                                                      isnull(cm.display_img) AND
                                                      ctm.id =cm.catalog_id AND
                                                      ctm.uuid = ?
                                                    ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->offset, $this->item_count]);

                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $key->content_list = $catalog_content;

                            foreach ($catalog_content as $content_key) {
                                if ($content_key->credit_note != '') {
                                    $content_key->credit_note = json_decode($content_key->credit_note);
                                }
                            }
                        } else {
                            $total_row = 0;
                            $key->total_record = $total_row;
                            $key->is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $key->content_list = [];

                        }
                    }
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getNormalCatalogsBySubCategoryId', $e);
            //      Log::error("getNormalCatalogsBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get normal catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/setPaymentStatus",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="setPaymentStatus",
     *        summary="Set payment status",
     *        produces={"application/json"},
     *
     *     @SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *       in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"txn_id"},
     *
     *          @SWG\Property(property="txn_id",  type="string", example="9LJ58889NK990510S", description=""),
     *          @SWG\Property(property="paypal_response",  type="object", example={"tx": "9LJ58889NK990510S","st": "Completed","amt": "22%2e00","cc": "USD","cm": 2,"item_number": 3,"sig": "lpkICK9e1MSthZYoqGIF7T4UUvz%2bbYxK%2bxympLh3%2fX5W6l1tJYz2hJZloDIc%2fnulwbu59cOFJowHjFoKjxhqVmE%2fByJGJA45rt55IOLodzgfRWB0FpQXrHHAw1sQWJfgDh%2fAq0xtlXUFywwOiEQaUVkBb10IDwglDZlJsLn0yRs%3d"}, description=""),
     *          @SWG\Property(property="paypal_payment_status",  type="string", example="Completed", description="Value of 'st' return from paypal_url"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Thank you, your payment was successful.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjMxLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4Njk4NzkwLCJleHAiOjE1NjkzMDM1OTAsIm5iZiI6MTU2ODY5ODc5MCwianRpIjoiVGlSVVY3VmMzQ0dCWWNmUCJ9.bq6uVaByVeLCxQNsLd3_RonXklSFK9sfe9qNx0PX7Ms","user_detail":{"user_id":31,"user_name":"steave@gmail.com","first_name":"Steave","email_id":"steave@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":1,"mailchimp_subscr_id":"b149f77a27357223db2104142cf13a6f","role_id":5,"create_time":"2019-02-22 05:14:22","update_time":"2019-02-22 05:14:24","subscr_expiration_time":"2019-10-17 05:40:11","next_billing_date":"2019-10-17 05:40:11","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
     *        ),
     *
     *      @SWG\Response(
     *            response=419,
     *            description="Running on multiple subscription",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":419,"message":"You are running on multiple subscriptions, please unsubscribe any one of them from the Paypal using below button, else you will be charged for all active subscriptions.","cause":"","data":"{}"}),),
     *        ),
     *
     *     @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to set payment status.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function setPaymentStatus(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['txn_id', 'paypal_response', 'paypal_payment_status'], $request)) != '') {
                return $response;
            }

            $txn_id = $request->txn_id;

            if (($response = (new VerificationController())->checkIsSubscriptionAlreadyExist($user_id)) != '') {
                return $response;
            }

            $paypal_response = $request->paypal_response;
            $paypal_payment_status = $request->paypal_payment_status;
            $create_time = date('Y-m-d H:i:s');
            if ($paypal_payment_status == 'Completed') {
                $paypal_status = 1;
                $is_active = 1;
                $subscr_type = $paypal_response->item_number;

                $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                (new PaypalIPNController())->updateUserRole($subscr_type, $user_id);

                if ($subscr_type == $subscr_type_of_monthly_starter or $subscr_type == $subscr_type_of_monthly_pro) {
                    $expiration_time = $response = (new VerificationController())->addDaysIntoDate($create_time, 30);
                } elseif ($subscr_type == $subscr_type_of_yearly_starter or $subscr_type == $subscr_type_of_yearly_pro) {

                    $expiration_time = $response = (new VerificationController())->addDaysIntoDate($create_time, 365);
                } else {
                    $expiration_time = $create_time;
                }
            } else {
                $paypal_status = 0;
                $is_active = 0;
                $expiration_time = null;

                /** Set job which are run after 10 minute for check payment status */
                $job = new activatePayPalSubscriptionAfter10MinJob($user_id);
                $jobId = $this->dispatch($job);
                $schedule_time = strtotime('+10 minutes', strtotime(date('Y-m-d H:i:s')));
                DB::table('jobs')->where('id', $jobId)->update(['available_at' => $schedule_time]);
                //        $result = $job->getResponse();
                //        Log::info("job result : ",[$result]);
            }

            DB::beginTransaction();

            DB::insert('INSERT INTO payment_status_master (
                          user_id,
                          txn_id,
                          paypal_status,
                          paypal_payment_status,
                          paypal_response,
                          expiration_time,
                          is_active,
                          create_time) VALUES (?,?,?,?,?,?,?,?)',
                [
                    $user_id,
                    $txn_id,
                    $paypal_status,
                    $paypal_payment_status,
                    json_encode($paypal_response),
                    $expiration_time,
                    $is_active,
                    $create_time,
                ]);

            DB::commit();

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

            //Log::info($token);
            //return $result = json_decode(json_encode($token), true);
            /*$requestHeaders = apache_request_headers();
            $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/
            $response = Response::json(['code' => 200, 'message' => 'Thank you, your payment was successful.', 'cause' => '', 'data' => ['user_detail' => $user_detail]]);

        } catch (Exception $e) {
            (new ImageController())->logs('setPaymentStatus', $e);
            //      Log::error("setPaymentStatus : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set payment status.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| Add 3D shape by designer |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/edit3DShape",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="edit3DShape",
     *        summary="Edit 3D shape",
     *        consumes={"multipart/form-data" },
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
     *          description="Give content_id, json_data (optional), content_type (6=3D text,7=3D shape), is_featured & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","is_featured","is_free","content_type"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="json object"),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="content_type",type="integer", example=1, description="1=free, 0=paid"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of 3D object",
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         name="content_file",
     *         in="formData",
     *         description="stl file of 3D shape. Required when content_type = 7",
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} edit3DShape edit3DShape
     *
     * @apiName edit3DShape
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "request_data":{
     * "content_id:1, //compulsory
     * "json_data":{} //compulsory
     * },
     * "file":1.png //optional
     * "content_file":logo_image.stl //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "3D object updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function edit3DShape(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter([
                'content_id',
                'json_data',
            ], $request)) != ''
            ) {
                return $response;
            }

            $content_id = $request->content_id;
            $json_data = json_encode($request->json_data);

            //Log::info('request_data', ['request_data' => $request]);

            DB::beginTransaction();
            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                //return $images_array;
                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);

                $object_image = (new ImageController())->generateNewFileName('3D_object_image', $image_array);
                (new ImageController())->saveOriginalImage($object_image);
                (new ImageController())->saveCompressedImage($object_image);
                (new ImageController())->saveThumbnailImage($object_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($object_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($object_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($object_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                DB::update('UPDATE
                                content_master SET
                                  image = ?,
                                  webp_image = ?,
                                  json_data = ?,
                                  height = ?,
                                  width = ?,
                                  color_value = ?
                                WHERE uuid = ?', [
                    $object_image,
                    $file_name,
                    $json_data,
                    $dimension['height'],
                    $dimension['width'],
                    $color_value,
                    $content_id]);
                DB::commit();

                if (strstr($file_name, '.webp')) {

                    $response = Response::json(['code' => 200, 'message' => '3D shape updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

                } else {
                    $response = Response::json(['code' => 200, 'message' => '3D shape updated successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);

                }

            } else {

                DB::update('UPDATE
                                content_master SET
                                  json_data = ?
                                WHERE uuid = ?', [
                    $json_data,
                    $content_id]);

                DB::commit();

                $response = Response::json(['code' => 200, 'message' => '3D shape updated successfully!.', 'cause' => '', 'data' => json_decode('{}')]);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('edit3DShape', $e);
            //      Log::error("edit3DShape : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit 3D shape.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| Delete Account |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteMyAccount",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteMyAccount",
     *        summary="Delete my account",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} deleteMyAccount deleteMyAccount
     *
     * @apiName deleteMyAccount
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Account deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteMyAccount()
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            if ($user_id != 1) {
                //Log::info('request_data', ['request_data' => $request]);
                $create_time = date('Y-m-d H:i:s');

                $record_of_user_master = DB::select('SELECT * FROM user_master WHERE id = ?', [$user_id]);
                $record_of_user_detail = DB::select('SELECT * FROM user_detail WHERE user_id = ?', [$user_id]);
                $record_of_my_design_tracking = DB::select('SELECT * FROM my_design_tracking_master WHERE user_id = ? ORDER BY create_time DESC', [$user_id]);
                $record_of_subscriptions = DB::select('SELECT * FROM subscriptions WHERE user_id = ? ORDER BY update_time DESC', [$user_id]);
                $record_of_payment_status = DB::select('SELECT * FROM payment_status_master WHERE user_id = ? ORDER BY update_time DESC', [$user_id]);
                $record_of_stripe_subscription = DB::select('SELECT * FROM stripe_subscription_master WHERE user_id = ? ORDER BY update_time DESC', [$user_id]);

                $uuid = (new ImageController())->generateUUID();
                DB::beginTransaction();
                DB::insert('INSERT INTO deleted_user_bkp_master (
                            user_id,
                            uuid,
                            record_of_user_master,
                            record_of_user_detail,
                            record_of_my_design_tracking,
                            record_of_subscriptions,
                            record_of_stripe_subscription,
                            record_of_payment_status,
                            is_deleted,
                            is_active,
                            create_time) VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?)', [
                    $user_id,
                    $uuid,
                    json_encode($record_of_user_master),
                    json_encode($record_of_user_detail),
                    json_encode($record_of_my_design_tracking),
                    json_encode($record_of_subscriptions),
                    json_encode($record_of_stripe_subscription),
                    json_encode($record_of_payment_status),
                    1,
                    1,
                    $create_time,
                ]);
                DB::commit();

                $this->deleteProfile($user_id);
                $this->deleteMyDesigns($user_id);
                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                DB::delete('DELETE FROM user_master WHERE id = ?', [$user_id]);
                //DB::delete('DELETE FROM subscriptions WHERE user_id = ?', [$user_id]);
                DB::update('UPDATE subscriptions SET first_name = ? WHERE user_id = ?', [$record_of_user_detail[0]->first_name, $user_id]);
                DB::commit();

                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'account_deleted');

                $response = Response::json(['code' => 200, 'message' => 'Account deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            } else {
                $response = Response::json(['code' => 201, 'message' => 'Invalid user.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyAccount', $e);
            //      Log::error("deleteMyAccount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete account.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ================================| Generate SVG |================================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/generateSVGWithXML",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="generateSVGWithXML",
     *        summary="Generate SVG With XML tag",
     *        consumes={"multipart/form-data" },
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
     *         name="file[]",
     *         in="formData",
     *         description="upload image",
     *         required=true,
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} generateSVGWithXML generateSVGWithXML
     *
     * @apiName generateSVGWithXML
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *   Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * file[]:"1.svg",
     * file[]:"2.svg",
     * file[]:"3.svg",
     * file[]:"4.svg"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "SVG generated successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * "http://192.168.0.113/photoadking/image_bucket/extra/5bf8ccf8a2df4_svg_1543032056.svg",
     * "http://192.168.0.113/photoadking/image_bucket/extra/5bf8ccf8a35c4_svg_1543032056.svg",
     * "http://192.168.0.113/photoadking/image_bucket/extra/5bf8ccf8a39ac_svg_1543032056.svg",
     * "http://192.168.0.113/photoadking/image_bucket/extra/5bf8ccf8a417c_svg_1543032056.svg"
     * ]
     * }
     * }
     */
    public function generateSVGWithXML(Request $request_body)
    {
        try {
            $exist_files_array = [];

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $images_array = Input::file('file');

                //return $images_array;
                foreach ($images_array as $image_array) {

                    $image = (new ImageController())->generateNewFileName('svg', $image_array);

                    $string = file_get_contents($image_array);

                    if (false == strpos($string, '<?xml')) {
                        /*$string = "<?xml version=1.0 encoding=UTF-8?>" . $string;*/
                        $string = '<?xml version="1.0" encoding="iso-8859-1"?>'.$string;

                        $path = '../..'.Config::get('constant.EXTRA_IMAGES_DIRECTORY').$image;
                        file_put_contents($path, $string);

                        $url = Config::get('constant.EXTRA_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                        $exist_files_array[] = $url;
                    } else {

                        /*$string = '<?xml version="1.0" encoding="iso-8859-1"?>' . $string;*/

                        $path = '../..'.Config::get('constant.EXTRA_IMAGES_DIRECTORY').$image;
                        file_put_contents($path, $string);

                        $url = Config::get('constant.EXTRA_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                        $exist_files_array[] = $url;
                    }

                }
                $result_array = ['result' => $exist_files_array];

                $result = json_decode(json_encode($result_array), true);
                $response = Response::json(['code' => 200, 'message' => 'SVG generated successfully.', 'cause' => '', 'data' => $result]);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('generateSVGWithXML', $e);
            //      Log::error("generateSVGWithXML : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate svg.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| UnSubscribe Membership |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/cancelSubscription",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="cancelSubscription",
     *        summary="Cancel Subscription",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} cancelSubscription cancelSubscription
     *
     * @apiName cancelSubscription
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Subscription canceled successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function cancelSubscription()
    {
        //$curl_response = "NA";
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $payer_email = $user_detail->email_id;

            $result = DB::select('SELECT
                                    id,
                                    user_id,
                                    transaction_id,
                                    expiration_time,
                                    paypal_id,
                                    payment_mode,
                                    is_active
                                    FROM subscriptions
                                    WHERE user_id = ? ORDER BY id DESC ', [$user_id]);

            if (count($result) <= 0) {

                return $response = Response::json(['code' => 201, 'message' => 'You are not subscriber.', 'cause' => '', 'data' => json_decode('{}')]);

            } else {
                $is_active = $result[0]->is_active;

                if ($is_active == 0) {
                    return $response = Response::json(['code' => 201, 'message' => 'Subscription already cancelled.', 'cause' => '', 'data' => json_decode('{}')]);

                } else {
                    $paypal_id = $result[0]->paypal_id;
                    $req = [
                        'user' => Config::get('constant.PAYPAL_API_USER'),
                        'pwd' => Config::get('constant.PAYPAL_API_PASSWORD'),
                        'signature' => Config::get('constant.PAYPAL_API_SIGNATURE'),
                        'version' => '70.0',
                        'METHOD' => 'ManageRecurringPaymentsProfileStatus',
                        'PROFILEID' => urlencode($paypal_id),
                        'ACTION' => 'Cancel',
                        'NOTE' => 'User cancelled on website',
                    ];

                    $ch = curl_init();

                    // Swap these if you're testing with the sandbox
                    curl_setopt($ch, CURLOPT_URL, Config::get('constant.PAYPAL_API_URL'));
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req));
                    $curl_response = curl_exec($ch);
                    $curl_response = urldecode($curl_response);
                    $curl_response_status = strval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

                    if ($curl_response === false || $curl_response_status == '0') {
                        $errno = curl_errno($ch);
                        $errstr = curl_error($ch);
                        curl_close($ch);
                        //throw new Exception("cURL error: [$errno] $errstr");
                        return $response = Response::json(['code' => 201, 'message' => 'Unable to cancel subscription. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);

                    } else {
                        curl_close($ch);
                    }

                    if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $curl_response, $matches)) {
                        foreach ($matches['name'] as $offset => $name) {
                            $nvp[$name] = urldecode($matches['value'][$offset]);
                        }
                    }

                    $paypal_ACK = (isset($nvp['ACK'])) ? $nvp['ACK'] : 'Error';

                    if (strcmp($paypal_ACK, 'Error') == 0 || strcmp($paypal_ACK, 'Failure') == 0) {
                        //throw new Exception("Paypal error:" . $nvp['L_SHORTMESSAGE0']);
                        return $response = Response::json(['code' => 201, 'message' => 'Unable to cancel subscription. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    $this->cancelSubscriptionByPaypalID($paypal_id, $payer_email);

                    $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                    /* $requestHeaders = apache_request_headers();
                     $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);*/

                    $result = [
                        'user_detail' => $user_detail,
                    ];

                    $response = Response::json(['code' => 200, 'message' => 'Subscription cancelled successfully.', 'cause' => '', 'data' => $result]);

                }

            }

        } catch (Exception $e) {
            (new ImageController())->logs('cancelSubscription', $e);
            $this->dispatch(new EmailJob('NA', Config::get('constant.ADMIN_EMAIL_ID'), 'Cancel subscription failed', $e->getMessage().'---'.$curl_response, 'NA', 'cancelSubscription', 'cancelSubscription'));
            //      Log::error("cancelSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'cancel subscription.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| link Share module |=============================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getShareLinkContentForUser",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getShareLinkContentForUser",
     *        summary="get Share Link Content By User",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *
     *      @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Detail fetched successfully.","cause":"","data":{"is_enable":"1","result":{"social_id":1,"social_media_name":"twitter.com","link_id":1,"link":"https://photoadking.com/design/template/"}}}),
     *      ),
     *   ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function getShareLinkContentForUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            $this->is_enable = DB::select('SELECT is_enable FROM share_module_master');
            if ($this->is_enable[0]->is_enable == 0) {
                $result = ['is_enable' => $this->is_enable[0]->is_enable, 'result' => []];
            } else {

                if (! Cache::has("Config::get('constant.REDIS_KEY'):getShareLinkContentForUser$this->user_id")) {
                    $result = Cache::remember("getShareLinkContentForUser$this->user_id", 5, function () {

                        $media_list = DB::select('SELECT smm.id AS social_id,
                                              smm.name AS social_media_name,
                                              slm.id AS link_id,
                                              slm.link from social_media_master AS smm ,share_link_master AS slm WHERE smm.id NOT IN
                                                (SELECT
                                                  um.social_id
                                                  FROM user_shared_link_master AS um ,share_link_master AS sm
                                                  WHERE um.user_id = ? AND
                                                  sm.id = um.link_id AND
                                                  sm.is_share_enable = 1 AND
                                                  now() < um.share_it_after)
                                              AND slm.is_share_enable = 1',
                            [$this->user_id]);

                        return ['is_enable' => $this->is_enable[0]->is_enable, 'result' => $media_list];
                    });
                }

                $redis_result = Cache::get("getShareLinkContentForUser$this->user_id");

                if (! $redis_result) {
                    $result = [];
                } else {
                    $result = $redis_result;
                }

            }

            $response = Response::json(['code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => $result]);
        } catch (Exception $e) {
            (new ImageController())->logs('getShareLinkContentForUser', $e);
            //        Log::error("getShareLinkContentForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get details.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

          return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addSharedLinkDetailsByUser",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addSharedLinkDetailsByUser",
     *        summary="add Shared Link Details By User",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"name"},
     *
     *          @SWG\Property(property="link_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="social_id",  type="integer", example=1, description=""),
     *        ),
     *
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    public function addSharedLinkDetailsByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['link_id', 'social_id'], $request)) != '') {
                return $response;
            }

            $link_id = $request->link_id;
            $social_id = $request->social_id;
            if (count(DB::select('SELECT 1 FROM user_shared_link_master WHERE link_id = ? AND user_id = ? AND social_id = ? AND now() < share_it_after', [$link_id, $user_id, $social_id])) > 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Sorry, you are not eligible to share at this time.', 'cause' => '', 'data' => '']);
            }
            $last_shared_at = date('Y-m-d H:i:s');
            $frequency = DB::select('SELECT frequency FROM share_link_master WHERE id = ?', [$link_id]);
            //        $share_it_after = date('Y-m-d H:i:s' ,strtotime($last_shared_at. '+ '. $frequency[0]->frequency. ' day'));
            $share_it_after = date('Y-m-d H:i:s', strtotime($last_shared_at.'+ '.$frequency[0]->frequency.' minutes'));

            $already_shared = DB::select('SELECT id,total_share FROM user_shared_link_master WHERE link_id = ? AND user_id = ? AND social_id = ?', [$link_id, $user_id, $social_id]);
            if (count($already_shared) > 0) {
                $share_id = $already_shared[0]->id;
                $count = $already_shared[0]->total_share + 1;
                DB::beginTransaction();
                DB::update('UPDATE user_shared_link_master set last_shared_at = ? ,share_it_after = ? ,total_share = ? WHERE id = ?',
                    [$last_shared_at, $share_it_after, $count, $share_id]);
                DB::commit();

            } else {
                DB::beginTransaction();
                DB::insert('INSERT INTO user_shared_link_master(link_id,user_id,social_id,last_shared_at,share_it_after,total_share) VALUES(?,?,?,?,?,1)',
                    [$link_id, $user_id, $social_id, $last_shared_at, $share_it_after]);
                DB::commit();
            }
            $response = Response::json(['code' => 200, 'message' => 'User shared link details added successfully.', 'cause' => '', 'data' => '']);
        } catch (Exception $e) {
            (new ImageController())->logs('addSharedLinkDetailsByUser', $e);
            //        Log::error("addSharedLinkDetailsByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add details.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

            DB::rollback();
        }

        return $response;
    }

    /* =================================| Sub Functions |=============================*/
    public function deleteAllRedisKeys($key_name)
    {
        try {
            $is_success = Redis::del(array_merge(Redis::keys(Config::get('constant.REDIS_KEY').":$key_name*"), ['']));

            return $is_success;

        } catch (Exception $e) {
            Log::error('deleteAllRedisKeys : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return 0;
        }
    }

    public function deleteMultipleRedisKeys($key_name)
    {
        try {
            foreach ($key_name as $i => $name) {
                Redis::del(array_merge(Redis::keys(Config::get('constant.REDIS_KEY').":$name*"), ['']));
            }

            return 1;

        } catch (Exception $e) {
            Log::error('deleteMultipleRedisKeys : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return 0;
        }
    }

    public function getAllRedisKeys($key_name)
    {
        try {
            $redis_keys = Redis::keys(Config::get('constant.REDIS_KEY').":$key_name*");

            return $redis_keys;

        } catch (Exception $e) {
            Log::error('getAllRedisKeys : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return json_decode('{}');
        }
    }

    public function getRedisKeyValue($key_name)
    {
        try {
            $redis_value = Cache::get($key_name);

            return $redis_value;

        } catch (Exception $e) {
            Log::error('getRedisKeyValue : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return json_decode('{}');
        }
    }

    public function uploadChunkFile($request_body)
    {
        try {
            if (! $request_body->hasFile('file')) {
                Log::error('uploadChunkFile : Required field file is missing or empty.');

                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $chunk_file = Input::file('file');
            $unique_id = $request_body->unique_id;
            $chunks = $request_body->chunks;
            $chunk = $request_body->chunk;
            $folder_path = '../..'.Config::get('constant.CHUNKS_DIRECTORY').$unique_id;

            if (! is_dir($folder_path)) {
                @mkdir($folder_path, 0777, true);
            }

            $extension = strtolower($chunk_file->getClientOriginalExtension());
            $img = $unique_id.'.'.$chunk.'.'.$extension;
            $final_img = $unique_id.'.'.$extension;
            $chunk_file->move($folder_path, $img);
            $files = glob("$folder_path/*");

            if ($chunks == count($files)) {
                natcasesort($files);
                $command = 'cat '.implode(' ', $files)." > $folder_path/$final_img";
                shell_exec($command);
                if (! file_exists("$folder_path/$final_img")) {
                    Log::error('uploadChunkFile : File does not exist.', ['command' => $command, 'file_path' => "$folder_path/$final_img", 'unique_id' => $unique_id, 'chunk' => $chunks, 'chunks' => $chunks, 'request_body' => $request_body]);
                }
                $result = $this->pathToUploadedFile("$folder_path/$final_img");

                return ['code' => 200, 'message' => 'Image uploaded successfully.', 'cause' => '', 'data' => $result];
            } else {
                return ['code' => 200, 'message' => 'File in progress.', 'cause' => '', 'data' => (count($files) * 100) / $chunks];
            }

        } catch (Exception $e) {
            Log::error('uploadChunkFile : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return ['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'upload image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')];
        }
    }

    public function pathToUploadedFile($path, $public = true)
    {
        try {
            $name = File::name($path);
            $extension = File::extension($path);
            $originalName = $name.'.'.$extension;
            $mimeType = File::mimeType($path);
            $size = File::size($path);
            $error = null;
            $test = $public;
            $object = new UploadedFile($path, $originalName, $mimeType, $size, $error, $test);

            return $object;
        } catch (Exception $e) {
            (new ImageController())->logs('pathToUploadedFile', $e);
            //Log::error("pathToUploadedFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function searchTemplatesBySearchCategory($search_category, $sub_category_id, $offset, $item_count)
    {
        try {
            $this->sub_category_id = $sub_category_id;
            $this->db_search_category = $this->search_category = $search_category;
            $this->offset = $offset;
            $this->item_count = $item_count;

            $redis_result = Cache::rememberforever("searchCardsBySubCategoryId:$this->sub_category_id:$this->search_category:$this->offset:$this->item_count", function () {

                $code = 200;
                $message = 'Templates fetched successfully.';
                $is_spell_corrected = 0;

                $total_row_result = DB::select('SELECT
                                            COUNT(DISTINCT(ctm.id)) AS total
                                        FROM
                                            content_master AS ctm,
                                            catalog_master AS cm,
                                            sub_category_catalog AS scc
                                        WHERE
                                            ctm.is_active = 1 AND
                                            ctm.catalog_id = scc.catalog_id AND
                                            cm.is_featured = 1 AND
                                            cm.id = scc.catalog_id AND
                                            scc.sub_category_id IN ('.$this->sub_category_id.') AND
                                            ISNULL(ctm.original_img) AND
                                            ISNULL(ctm.display_img) AND
                                            (MATCH(ctm.search_category) AGAINST("'.$this->search_category.'") OR
                                            MATCH(ctm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ") IN BOOLEAN MODE)) ');
                $total_row = $total_row_result[0]->total;

                $search_result = DB::select('SELECT
                                          DISTINCT ctm.id AS json_id,
                                          IF(ctm.attribute1 != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.attribute1),"") AS sample_image,
                                          ctm.is_free,
                                          ctm.is_featured,
                                          ctm.is_portrait,
                                          COALESCE(ctm.height,0) AS height,
                                          COALESCE(ctm.width,0) AS width,
                                          COALESCE(ctm.multiple_images,"") AS multiple_images,
                                          COALESCE(ctm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(ctm.json_pages_sequence) - LENGTH(REPLACE(ctm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          ctm.update_time,
                                          MATCH(ctm.search_category) AGAINST("'.$this->search_category.'") +
                                          MATCH(ctm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ") IN BOOLEAN MODE) AS search_text
                                      FROM
                                          content_master AS ctm,
                                          catalog_master AS cm,
                                          sub_category_catalog AS scc
                                      WHERE
                                          ctm.is_active = 1 AND
                                          ctm.catalog_id = scc.catalog_id AND
                                          cm.id = scc.catalog_id AND
                                          cm.is_featured = 1 AND
                                          scc.sub_category_id IN ('.$this->sub_category_id.') AND
                                          ISNULL(ctm.original_img) AND
                                          ISNULL(ctm.display_img) AND
                                          (MATCH(ctm.search_category) AGAINST("'.$this->search_category.'") OR
                                          MATCH(ctm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ") IN BOOLEAN MODE))
                                      ORDER BY search_text DESC,ctm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                if (count($search_result) <= 0) {

                    $total_row_result = DB::select('SELECT
                                              COUNT(DISTINCT(ctm.id)) AS total
                                          FROM
                                              content_master AS ctm,
                                              catalog_master AS cm,
                                              sub_category_catalog AS scc
                                          WHERE
                                              ctm.is_active = 1 AND
                                              ctm.catalog_id = scc.catalog_id AND
                                              cm.id = scc.catalog_id AND
                                              scc.sub_category_id IN ('.$this->sub_category_id.') AND
                                              cm.is_featured = 1 AND
                                              ISNULL(ctm.original_img) AND
                                              ISNULL(ctm.display_img) ');
                    $total_row = $total_row_result[0]->total;

                    $search_result = DB::select('SELECT
                                            DISTINCT ctm.id AS json_id,
                                            IF(ctm.attribute1 != "",CONCAT("'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.attribute1),"") AS sample_image,
                                            ctm.is_free,
                                            ctm.is_featured,
                                            ctm.is_portrait,
                                            COALESCE(ctm.height,0) AS height,
                                            COALESCE(ctm.width,0) AS width,
                                            COALESCE(ctm.multiple_images,"") AS multiple_images,
                                            COALESCE(ctm.json_pages_sequence,"") AS pages_sequence,
                                            COALESCE(LENGTH(ctm.json_pages_sequence) - LENGTH(REPLACE(ctm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                            ctm.update_time
                                        FROM
                                            content_master AS ctm,
                                            catalog_master AS cm,
                                            sub_category_catalog AS scc
                                        WHERE
                                            ctm.is_active = 1 AND
                                            ctm.catalog_id = scc.catalog_id AND
                                            cm.id = scc.catalog_id AND
                                            cm.is_featured = 1 AND
                                            scc.sub_category_id IN ('.$this->sub_category_id.') AND
                                            ISNULL(ctm.original_img) AND
                                            ISNULL(ctm.display_img)
                                        ORDER BY ctm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);
                    $code = 427;
                    $message = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                return ['code' => $code, 'message' => $message, 'cause' => '', 'data' => $search_result];

            });

            return $redis_result;

        } catch (Exception $e) {
            Log::error('searchTemplatesBySearchCategory : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return ['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')];
        }
    }

    /*
    Purpose : To translate language from any to english with google API.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return translated language detected detail if success otherwise error with specific status code
    */
    public function translateLanguage($text, $language = 'en')
    {
        try {
            $this->text = $text;
            $this->language = $language;

            $redis_result = Cache::rememberforever("translateLanguage:$this->text", function () {

                $translate = new TranslateClient([
                    'key' => Config::get('constant.GOOGLE_API_KEY'),
                ]);

                // Translate text from Any to English.
                $result = $translate->translate($this->text, [
                    'target' => $this->language,
                ]);

                //Log::info('translateLanguage : ', ['user_tag' => $this->text, 'result' => $result, 'target_language' => $this->language]);
                return $result;
            });

            $response = ['code' => 200, 'message' => 'Language translation successfully.', 'cause' => '', 'data' => $redis_result];

        } catch (Exception $e) {
            Log::error('translateLanguage : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = ['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get template detail.', 'cause' => $e->getMessage(), 'data' => ['source' => '', 'input' => $text, 'text' => '', 'model' => '']];
        }

        return $response;
    }

    /*
    Purpose : To correct spelling of text with p-spell php extension.
    Description : This method compulsory take 2 argument as parameter.(if any argument is optional then define it here).
    Return : return spell corrected word in array if success otherwise error with specific status code
    */
    public function spellCorrection($language_dictionary, $text)
    {
        try {
            $suggestions = [];
            //$spellLink = pspell_new($language_dictionary);
            $spellLink = pspell_new('en');
            if (! pspell_check($spellLink, $text)) {
                $suggestions = pspell_suggest($spellLink, $text);
                Log::info('spellCorrection : Spell suggestion.', ['user_tag' => $text, 'suggestion' => $suggestions, 'language_dictionary' => $language_dictionary]);
            }

            $response = ['code' => 200, 'message' => 'Spell correction successfully.', 'cause' => '', 'data' => $suggestions];

        } catch (Exception $e) {
            Log::error('spellCorrection : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = ['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get template detail.', 'cause' => $e->getMessage(), 'data' => []];
        }

        return $response;
    }

    public function addVideoGenerateHistory($message, $user_id, $download_id, $get_my_design_id, $content_id, $content_type, $status)
    {
        DB::beginTransaction();
        DB::insert('INSERT INTO video_generate_history_master (message,user_id,download_id,design_id,content_id,content_type,status) VALUES (?,?,?,?,?,?,?)', [$message, $user_id, $download_id, $get_my_design_id, $content_id, $content_type, $status]);
        DB::commit();
    }

    //get image design,video,intro For admin & user
    public function getDesign($user_id, $content_type, $offset, $item_count)
    {

        $total_row_result = DB::select('SELECT
                                          COUNT(id) AS total
                                        FROM
                                          my_design_master
                                        WHERE
                                          user_id = ? AND
                                          folder_id IS NULL AND
                                          is_active = ? AND
                                          content_type = ?
                                        ORDER BY update_time DESC', [$user_id, 1, $content_type]);
        $total_row = $total_row_result[0]->total;
        //          $default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_RECOMMENDED_TEMPLATES');

        $folder_list = DB::select('SELECT
                                          dfm.uuid as folder_id,
                                          dfm.folder_name,
                                          dfm.folder_content_type,
                                          IF(dfm.my_design_ids != "",length(dfm.my_design_ids) - length(replace(dfm.my_design_ids, ",", ""))+1,0) as TotalDesign,
                                          dfm.update_time
                                        FROM
                                          design_folder_master as dfm
                                        WHERE
                                          dfm.user_id = ? AND
                                          dfm.is_active = 1 AND
                                          dfm.folder_content_type = ?
                                        ORDER BY dfm.create_time', [$user_id, $content_type]);

        $image_list = DB::select('SELECT
                                          mdm.uuid as my_design_id,
                                          scm.uuid as sub_category_id,
                                          coalesce(mdm.download_json,"") AS download_json,
                                          IF(mdm.user_template_name != "",user_template_name,"Untitled Design") as user_template_name,
                                          IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") as sample_image,
                                          IF(mdm.overlay_image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.overlay_image),"") as overlay_image,
                                          coalesce(mdm.is_video_user_uploaded,"") AS is_video_user_uploaded,
                                          mdm.content_type,
                                          COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) as total_pages,
                                          mdm.update_time
                                        FROM
                                          my_design_master as mdm,
                                          sub_category_master as scm
                                        WHERE
                                          mdm.sub_category_id=scm.id AND
                                          mdm.user_id = ? AND
                                          mdm.folder_id IS NULL AND
                                          mdm.is_active = ? AND
                                          mdm.content_type = ?
                                        ORDER BY mdm.update_time DESC
                                        LIMIT ?,?', [$user_id, 1, $content_type, $offset, $item_count]);

        $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

        return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'folder_result' => $folder_list, 'image_result' => $image_list];

    }

    //get Design Folder for admin & user
    public function getDesignFolder($user_id, $folder_id, $offset, $item_count)
    {

        $my_design_ids = DB::select('SELECT my_design_ids FROM design_folder_master WHERE user_id = ? AND is_active = ? AND uuid = ?', [$user_id, 1, $folder_id]);
        $id_list = $my_design_ids[0]->my_design_ids;
        $image_list = [];
        if ($id_list == null) {
            $total_row = 0;
        } else {
            $ids_list = explode(',', $id_list);
            $ids = array_reverse($ids_list);
            $total_row = count($ids);
            foreach ($ids as $id) {
                $image = DB::select('SELECT
                                            mdm.uuid as my_design_id,
                                            scm.uuid as sub_category_id,
                                            mdm.content_type,
                                            IF(mdm.user_template_name != "",user_template_name,"Untitled Design") as user_template_name,
                                            IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") as sample_image,
                                            coalesce(mdm.color_value,"") AS color_value,
                                            COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) as total_pages,
                                            mdm.update_time
                                          FROM
                                             my_design_master as mdm,
                                             sub_category_master as scm
                                          WHERE
                                            mdm.sub_category_id=scm.id AND
                                            mdm.id = ? AND
                                            mdm.is_active = ?', [$id, 1]);
                $image_list = array_merge($image_list, $image);
            }
        }

        //get elements from array with start & end position
        $result = array_slice($image_list, $offset, $item_count);

        $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

        return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'data' => $result];

    }

    //for single-page operation (video-intro)
    public function add3DObjectImages($images_array, $my_design_id, $create_time)
    {
        try {

            if (($response = (new VerificationController())->checkIsObjectImageExist($images_array)) != '') {
                return $response;
            }

            //return $images_array;
            foreach ($images_array as $image_array) {

                //(new ImageController())->unlink3DObjectImage($image_array);

                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                (new ImageController())->save3DObjectImage($image_array);

                $image = $image_array->getClientOriginalName();

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->save3DObjectImageInToS3($image);
                }

                DB::beginTransaction();
                DB::insert('INSERT
                                INTO
                                  my_design_3d_image_master(
                                my_design_id,
                                image,
                                is_active,
                                create_time)
                                VALUES(?, ?, ?, ?)', [
                    $my_design_id,
                    $image,
                    1,
                    $create_time]);
                DB::commit();
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('add3DObjectImages', $e);
            //      Log::error("add3DObjectImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add 3D object images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function addTransparentImages($images_array, $my_design_id, $create_time)
    {
        try {

            if (($response = (new VerificationController())->checkIsTransparentImageExist($images_array)) != '') {
                return $response;
            }

            foreach ($images_array as $image_array) {

                if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                    return $response;
                }

                (new ImageController())->saveTransparentImage($image_array);

                $image = $image_array->getClientOriginalName();

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveTransparentImageInToS3($image);
                }

                DB::beginTransaction();
                DB::insert('INSERT
                                INTO
                                  my_design_transparent_image_master(
                                my_design_id,
                                image,
                                is_active,
                                create_time)
                                VALUES(?, ?, ?, ?)', [
                    $my_design_id,
                    $image,
                    1,
                    $create_time]);
                DB::commit();
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('addTransparentImages', $e);
            //      Log::error("addTransparentImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add transparent images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function addStockPhotos($images_array, $my_design_id, $create_time)
    {
        try {

            foreach ($images_array as $image_array) {
                $pixabay_image_id = (new VerificationController())->getPixabayImageId($image_array);

                if (($response = (new VerificationController())->checkIsStockPhotosExist($pixabay_image_id)) != 1) {

                    if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                        return $response;
                    }

                    (new ImageController())->saveStockPhotos($image_array);

                    $image = $image_array->getClientOriginalName();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveStockPhotosInToS3($image);
                    }

                    DB::beginTransaction();
                    DB::insert('INSERT INTO
                                  stock_photos_master(
                                  image,
                                  pixabay_image_id,
                                  my_design_ids,
                                  is_active,
                                  create_time)
                                VALUES(?, ?, ?, ?, ?)', [
                        $image,
                        $pixabay_image_id,
                        $my_design_id,
                        1,
                        $create_time]);
                    DB::commit();
                } else {
                    $is_exist = DB::select('SELECT 1 FROM stock_photos_master
                                                  WHERE
                                                  pixabay_image_id = ? AND
                                                  FIND_IN_SET("'.$my_design_id.'", my_design_ids)', [$pixabay_image_id]);

                    $result = DB::select('SELECT my_design_ids FROM stock_photos_master
                                                  WHERE
                                                  pixabay_image_id = ?', [$pixabay_image_id]);
                    $my_design_ids = $result[0]->my_design_ids.','.$my_design_id;

                    if (count($is_exist) == 0) {
                        DB::beginTransaction();
                        DB::update('UPDATE
                                  stock_photos_master SET
                                  my_design_ids = ? WHERE pixabay_image_id = ? ', [$my_design_ids, $pixabay_image_id]);
                        DB::commit();
                    }

                }

            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('addStockPhotos', $e);
            //      Log::error("addStockPhotos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add stock photos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //V2 is for multi-page operation (image)
    public function add3DObjectImagesV2($image_array, $image, $my_design_id, $create_time, $deleted_file_list)
    {
        try {

            DB::insert('INSERT IGNORE
                                INTO
                                  my_design_3d_image_master(
                                my_design_id,
                                image,
                                is_active,
                                create_time)
                                VALUES(?, ?, ?, ?)', [
                $my_design_id,
                $image,
                1,
                $create_time]);

            (new ImageController())->save3DObjectImage($image_array);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->save3DObjectImageInToS3($image);
            }

            $image_detail['name'] = $image;
            $image_detail['path'] = 'object_images';
            array_push($deleted_file_list, $image_detail);

            $response = ['code' => 200, 'message' => 'object image saved successfully.', 'cause' => '', 'data' => $deleted_file_list];

        } catch (Exception $e) {
            Log::error('add3DObjectImagesV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            //Observe first how many times the requested image name is duplicated. Remove below comment after observation.
            /*if($e->getMessage() == "1062 Duplicate entry '$image' for key 'image'"){
                $object_image_name = (new ImageController())->generateNewFileName('3D_object_image', $image_array);
                Log::info("add3DObjectImagesV2 new image name : ", [$object_image_name]);
                $this->add3DObjectImagesV2($image_array, $object_image_name, $my_design_id, $create_time, $deleted_file_list);
            }*/

            $response = ['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add 3D object images.', 'cause' => $e->getMessage(), 'data' => $deleted_file_list];
            DB::rollBack();
        }

        return $response;
    }

    public function addTransparentImagesV2($image_array, $image, $my_design_id, $create_time, $deleted_file_list)
    {
        try {
            DB::insert('INSERT IGNORE
                                INTO
                                  my_design_transparent_image_master(
                                my_design_id,
                                image,
                                is_active,
                                create_time)
                                VALUES(?, ?, ?, ?)', [
                $my_design_id,
                $image,
                1,
                $create_time]);

            (new ImageController())->saveTransparentImage($image_array);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveTransparentImageInToS3($image);
            }

            $image_detail['name'] = $image;
            $image_detail['path'] = 'my_design';
            array_push($deleted_file_list, $image_detail);

            $response = ['code' => 200, 'message' => 'transparent image saved successfully.', 'cause' => '', 'data' => $deleted_file_list];

        } catch (Exception $e) {
            Log::error('addTransparentImagesV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            //Observe first how many times the requested image name is duplicated. Remove below comment after observation.
            /*if($e->getMessage() == "1062 Duplicate entry '$image' for key 'image'"){
                $object_image_name = (new ImageController())->generateNewFileName('3D_object_image', $image_array);
                Log::info("addTransparentImagesV2 new image name : ", [$object_image_name]);
                $this->addTransparentImagesV2($image_array, $object_image_name, $my_design_id, $create_time, $deleted_file_list);
            }*/

            $response = ['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add transparent images.', 'cause' => $e->getMessage(), 'data' => $deleted_file_list];
            DB::rollBack();
        }

        return $response;
    }

    public function addStockPhotosV2($images_array, $my_design_id, $create_time, $deleted_file_list)
    {
        try {

            foreach ($images_array as $image_array) {
                $pixabay_image_id = (new VerificationController())->getPixabayImageId($image_array);

                if (($response = (new VerificationController())->checkIsStockPhotosExist($pixabay_image_id)) != 1) {

                    (new ImageController())->saveStockPhotos($image_array);

                    $image = $image_array->getClientOriginalName();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveStockPhotosInToS3($image);
                    }

                    DB::insert('INSERT INTO
                                  stock_photos_master(
                                  image,
                                  pixabay_image_id,
                                  my_design_ids,
                                  is_active,
                                  create_time)
                                VALUES(?, ?, ?, ?, ?)', [
                        $image,
                        $pixabay_image_id,
                        $my_design_id,
                        1,
                        $create_time]);

                    $image_detail['name'] = $image;
                    $image_detail['path'] = 'stock_photos';
                    array_push($deleted_file_list, $image_detail);

                } else {

                    $is_exist = DB::select('SELECT 1 FROM stock_photos_master
                                                  WHERE
                                                  pixabay_image_id = ? AND
                                                  FIND_IN_SET("'.$my_design_id.'", my_design_ids)', [$pixabay_image_id]);

                    $result = DB::select('SELECT my_design_ids FROM stock_photos_master
                                                  WHERE
                                                  pixabay_image_id = ?', [$pixabay_image_id]);
                    $my_design_ids = $result[0]->my_design_ids.','.$my_design_id;

                    if (count($is_exist) == 0) {
                        DB::update('UPDATE
                                  stock_photos_master SET
                                  my_design_ids = ? WHERE pixabay_image_id = ? ', [$my_design_ids, $pixabay_image_id]);
                    }

                }

            }

            $response = ['code' => 200, 'message' => 'stock image saved successfully.', 'cause' => '', 'data' => $deleted_file_list];

        } catch (Exception $e) {
            Log::error('addStockPhotosV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = ['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add stock photos.', 'cause' => $e->getMessage(), 'data' => $deleted_file_list];
            DB::rollBack();
        }

        return $response;
    }

    public function increaseMyDesignCount($user_id, $create_time, $content_type)
    {
        try {

            //content_type 1 = image template , 2=video template
            $year_month = date('Y-m');
            $month_name = date('F');
            $my_design_count = 0;
            $my_video_design_count = 0;

            if ($content_type == Config::get('constant.IMAGE')) {
                $increment_design_count = 'my_design_count = my_design_count + 1';
                $my_design_count = 1;
            } else {
                $increment_design_count = 'my_video_design_count = my_video_design_count + 1';
                $my_video_design_count = 1;
            }

            DB::beginTransaction();
            DB::update('UPDATE user_detail SET '.$increment_design_count.' WHERE user_id = ?', [$user_id]);
            DB::commit();

            $is_exist = DB::select('SELECT id FROM my_design_tracking_master WHERE user_id = ? AND DATE_FORMAT(create_time, "%Y-%m") = ?', [$user_id, $year_month]);

            if (count($is_exist) > 0) {
                DB::beginTransaction();
                DB::update('UPDATE my_design_tracking_master SET  '.$increment_design_count.'  WHERE id = ?', [$is_exist[0]->id]);
                DB::commit();
            } else {
                DB::beginTransaction();
                DB::insert('INSERT INTO
                          my_design_tracking_master(user_id, month_name, my_design_count,my_video_design_count,is_active, create_time)
                                VALUES(?, ?, ?, ?, ?,?)', [
                    $user_id,
                    $month_name,
                    $my_design_count,
                    $my_video_design_count,
                    1,
                    $create_time]);
                DB::commit();
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('increaseMyDesignCount', $e);
            //      Log::error("increaseMyDesignCount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'increase my design count.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //remove stock images
    public function removeMyDesignIdFromTheList($stock_photos_id_list, $my_design_id)
    {
        try {
            $existed_data = DB::select('SELECT GROUP_CONCAT(pixabay_image_id) AS pixabay_image_ids
                                          FROM
                                            stock_photos_master
                                          WHERE
                                            find_in_set("'.$my_design_id.'",my_design_ids)');

            $pixabay_image_ids = (explode(',', $existed_data[0]->pixabay_image_ids));

            $not_existed_id = array_merge(array_diff($stock_photos_id_list, $pixabay_image_ids), array_diff($pixabay_image_ids, $stock_photos_id_list));

            foreach ($not_existed_id as $key) {

                DB::beginTransaction();
                DB::update('UPDATE stock_photos_master
                                      SET
                                        my_design_ids =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$my_design_id.',", ","))
                                      WHERE
                                        pixabay_image_id = ? AND
                                        FIND_IN_SET("'.$my_design_id.'", my_design_ids)', [$key]);
                DB::commit();

                $this->deleteUnusedStockPhotos($key);
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('removeMyDesignIdFromTheList', $e);
            //      Log::error("removeMyDesignIdFromTheList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete stock photos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //remove 3D images
    public function removeMyDesignIdFromThe3dImageList($deleted_object_images, $my_design_id)
    {
        try {
            foreach ($deleted_object_images as $deleted_object_image) {
                $existed_data = DB::select('SELECT id AS object_image_id
                                                  FROM
                                                    my_design_3d_image_master
                                                  WHERE
                                                    find_in_set("'.$my_design_id.'",my_design_id) AND image = ?', [$deleted_object_image]);
                //Log::debug('removeMyDesignIdFromThe3dImageList my_design_3d_image_master existed_data : ',['object_image_id'=>$existed_data]);
                if (count($existed_data) > 0) {

                    DB::beginTransaction();
                    DB::update('UPDATE my_design_3d_image_master
                                              SET
                                                my_design_id =
                                                TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_id, ","), ",'.$my_design_id.',", ","))
                                              WHERE
                                                 id = ? AND
                                                FIND_IN_SET("'.$my_design_id.'", my_design_id)', [$existed_data[0]->object_image_id]);
                    DB::commit();

                    $unused_3d_object = DB::select('SELECT id AS object_image_id,image
                                                      FROM my_design_3d_image_master WHERE id = ?
                                                      AND (my_design_id = "" OR my_design_id IS NULL)', [$existed_data[0]->object_image_id]);
                    if (count($unused_3d_object) > 0) {
                        //delete unused object images
                        (new ImageController())->delete3DObjectImage($unused_3d_object[0]->image);

                        DB::beginTransaction();
                        DB::delete('DELETE FROM my_design_3d_image_master WHERE id = ?', [$unused_3d_object[0]->object_image_id]);
                        DB::commit();
                    }
                }
            }
            $response = '';
        } catch (Exception $e) {
            (new ImageController())->logs('removeMyDesignIdFromThe3dImageList', $e);
            //      Log::error("removeMyDesignIdFromThe3dImageList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete 3D photos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //remove transparent images
    public function removeMyDesignIdFromTheTransparentImageList($deleted_transparent_images, $my_design_id)
    {
        try {
            foreach ($deleted_transparent_images as $deleted_object_image) {

                $existed_data = DB::select('SELECT id AS transparent_image_id
                                                FROM
                                                  my_design_transparent_image_master
                                                WHERE
                                                  find_in_set("'.$my_design_id.'",my_design_id) AND image = ?', [$deleted_object_image]);
                if (count($existed_data) > 0) {

                    DB::beginTransaction();
                    DB::update('UPDATE my_design_transparent_image_master
                                            SET
                                              my_design_id =
                                              TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_id, ","), ",'.$my_design_id.',", ","))
                                            WHERE
                                              id = ? AND
                                              FIND_IN_SET("'.$my_design_id.'", my_design_id)', [$existed_data[0]->transparent_image_id]);
                    DB::commit();

                    $unused_transparent_image = DB::select('SELECT id AS transparent_image_id,image
                                                          FROM my_design_transparent_image_master WHERE id = ?
                                                          AND (my_design_id = "" OR my_design_id IS NULL)', [$existed_data[0]->transparent_image_id]);
                    if (count($unused_transparent_image) > 0) {
                        //delete unused object images
                        (new ImageController())->deleteTransparentImage($unused_transparent_image[0]->image);

                        DB::beginTransaction();
                        DB::delete('DELETE FROM my_design_transparent_image_master WHERE id = ?', [$unused_transparent_image[0]->transparent_image_id]);
                        DB::commit();
                    }
                }
            }
            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('removeMyDesignIdFromTheTransparentImageList', $e);
            //      Log::error("removeMyDesignIdFromTheTransparentImageList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete transparent photos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function edit3DObjectImages($images_array, $my_design_id)
    {
        try {

            $create_time = date('Y-m-d H:i:s');
            $response = (new VerificationController())->checkIsObjectImageExistToEditDesign($images_array, $my_design_id);
            if ($response != '' && $response != 1) {
                return $response;
            } elseif ($response == '') {
                foreach ($images_array as $image_array) {

                    //(new ImageController())->unlink3DObjectImage($image_array);
                    if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                        return $response;
                    }

                    (new ImageController())->save3DObjectImage($image_array);

                    $image = $image_array->getClientOriginalName();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->save3DObjectImageInToS3($image);
                    }

                    $is_exist = DB::select('SELECT 1 FROM my_design_3d_image_master WHERE image = ? AND find_in_set(?,my_design_id)', [$image, $my_design_id]);

                    if (count($is_exist) > 0) {
                        //Any database operation is not required in this case
                    } else {
                        DB::beginTransaction();
                        DB::insert('INSERT
                                          INTO
                                            my_design_3d_image_master(
                                          my_design_id,
                                          image,
                                          is_active,
                                          create_time)
                                          VALUES(?, ?, ?, ?)', [
                            $my_design_id,
                            $image,
                            1,
                            $create_time]);
                        DB::commit();
                    }
                }
            } elseif ($response == 1) {
                $response = '';
            }

            return $response;
        } catch (Exception $e) {
            (new ImageController())->logs('edit3DObjectImages', $e);
            //      Log::error("edit3DObjectImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit 3D object images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function editTransparentImages($images_array, $my_design_id)
    {
        try {

            $create_time = date('Y-m-d H:i:s');
            $response = (new VerificationController())->checkIsTransparentImageExistToEditDesign($images_array, $my_design_id);
            if ($response != '' && $response != 1) {
                return $response;
            } elseif ($response == '') {

                foreach ($images_array as $image_array) {

                    if (($response = (new UserVerificationController())->verifyImage($image_array)) != '') {
                        return $response;
                    }

                    (new ImageController())->saveTransparentImage($image_array);

                    $image = $image_array->getClientOriginalName();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveTransparentImageInToS3($image);
                    }

                    $is_exist = DB::select('SELECT 1 FROM my_design_transparent_image_master WHERE image = ? AND find_in_set(?,my_design_id)', [$image, $my_design_id]);

                    if (count($is_exist) > 0) {
                        //Any database operation is not required in this case
                    } else {
                        DB::beginTransaction();
                        DB::insert('INSERT
                                INTO
                                  my_design_transparent_image_master(
                                my_design_id,
                                image,
                                is_active,
                                create_time)
                                VALUES(?, ?, ?, ?)', [
                            $my_design_id,
                            $image,
                            1,
                            $create_time]);
                        DB::commit();
                    }

                }

            } elseif ($response == 1) {
                $response = '';
            }

            return $response;
        } catch (Exception $e) {
            (new ImageController())->logs('editTransparentImages', $e);
            //      Log::error("editTransparentImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit transparent images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function deleteMyDesignImage($card_image, $my_design_id)
    {
        try {

            DB::delete('DELETE FROM my_design_master WHERE id = ?', [$my_design_id]);
            DB::commit();

            if (($response = (new ImageController())->deleteMyDesign($card_image)) != '') {
                return $response;
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyDesignImage', $e);
            //      Log::error("deleteMyDesignImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete my design image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function cancelSubscriptionByPaypalID($paypal_id, $payer_email = '')
    {
        try {
            $result = DB::select('SELECT
                      id,
                      user_id,
                      transaction_id,
                      subscr_type,
                      total_amount,
                      expiration_time,
                      days_to_add,
                      remaining_days,
                      cancellation_date
                      FROM subscriptions
                      WHERE paypal_id = ?
                      ORDER BY id DESC', [$paypal_id]);

            if (count($result) == 0) {

                // Cancel Payment without sub
                //Major Error

            } else {
                $db_expiration_time = date('Y-m-d', strtotime($result[0]->expiration_time));
                $current_date = date('Y-m-d');

                if ($db_expiration_time > $current_date) {
                    $datetime1 = new DateTime($db_expiration_time);
                    $datetime2 = new DateTime($current_date);
                    $interval = $datetime1->diff($datetime2);
                    $remaining_days = $interval->format('%a');
                } else {
                    $remaining_days = 0;
                }
                $remaining_days = $remaining_days + $result[0]->days_to_add;

                //$remaining_days = (new VerificationController())->differenceBetweenTwoDate($current_date, $db_expiration_time);

                //        Log::info('cancelPaymentDetailByPaypalID (remaining_days) : ', ['remaining_days' => $remaining_days,'id' => $paypal_id]);

                //Update Subscription
                $db_row_id = $result[0]->id;
                $subscr_type = $result[0]->subscr_type;
                $db_txn_id = $result[0]->transaction_id;
                $cancellation_date = date('Y-m-d H:i:s');
                DB::beginTransaction();
                DB::update('UPDATE subscriptions SET cancellation_date = ?,
                                                            remaining_days= ?,
                                                            response_message= ?,
                                                            is_active= ?
                                                            WHERE id = ? ',
                    [$cancellation_date,
                        $remaining_days,
                        'Subscription Cancelled',
                        0,
                        $db_row_id]);
                //DB::commit();

                DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [0, $db_txn_id]);
                DB::commit();

                /*$subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
                        $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
                        $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
                        $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

                        if ($subscr_type == $subscr_type_of_monthly_starter) {
                            $subscription_name = 'Monthly Starter';

                        } elseif ($subscr_type == $subscr_type_of_monthly_pro) {
                            $subscription_name = 'Monthly Pro';

                        } elseif ($subscr_type == $subscr_type_of_yearly_pro) {

                            $subscription_name = 'Yearly Starter';
                        } elseif ($subscr_type == $subscr_type_of_yearly_starter) {

                            $subscription_name = 'Yearly Pro';
                        } else {
                            $subscription_name = "None";
                        }

                        $txn_id = $result[0]->transaction_id;
                        $total_amount = $result[0]->total_amount;

                        $template = 'cancel_subscription';
                        $subject = 'PhotoADKing: Subscription Cancelled';
                        $message_body = array(
                            'message' => 'Your subscription cancelled successfully. Following are the subscription details.',
                            'subscription_name' => $subscription_name,
                            'txn_id' => $txn_id,
                            'txn_type' => 'Subscription[P]',
                            'subscr_id' => $paypal_id,
                            'total_amount' => $total_amount,
                            'first_name' => $user_profile->first_name,
                            'payment_status' => 'Subscription cancelled',
                            'payer_email' => $payer_email,
                            'mc_currency' => $txn['mc_currency'],
                            'cancellation_date' => $cancellation_date,
                            'expiration_date' => $db_expiration_time
                        );

                        $api_name = 'cancelSubscriptionByPaypalID';
                        $api_description = 'Subscription Cancelled';

                        $this->dispatch(new EmailJob($result[0]->user_id, $payer_email, $subject, $message_body, $template, $api_name, $api_description));*/
            }
        } catch (Exception $e) {
            (new ImageController())->logs('cancelSubscriptionByPaypalID', $e);
            //        Log::error("cancelSubscriptionByPaypalID : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'cancel subscription.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

    }

    public function delete3DObjectImages($my_design_id)
    {
        try {

            $object_list = DB::select('SELECT image FROM my_design_3d_image_master WHERE my_design_id = ?', [$my_design_id]);

            foreach ($object_list as $key) {
                if (($response = (new ImageController())->delete3DObjectImage($key->image)) != '') {
                    return $response;
                }

            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('delete3DObjectImages', $e);
            //      Log::error("delete3DObjectImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete 3D objects.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    //delete 3D image from the list and storage
    public function delete3DImageObject($object_3d_image_id)
    {
        try {
            $object_list = DB::select('SELECT image
                                        FROM my_design_3d_image_master
                                        WHERE
                                        id = ? AND
                                        (my_design_id = "" OR my_design_id IS NULL)', [$object_3d_image_id]
            );

            foreach ($object_list as $key) {
                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    (new ImageController())->deleteObjectFromS3($key->image, 'object_images');

                } else {
                    if (($response = (new ImageController())->delete3DObjectImage($key->image)) != '') {
                        return $response;
                    }
                }
            }
            DB::beginTransaction();
            DB::delete('DELETE FROM my_design_3d_image_master WHERE id = ? AND (my_design_id = "" OR my_design_id IS NULL) ', [$object_3d_image_id]);
            DB::commit();

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('delete3DImageObject', $e);
            //      Log::error("delete3DImageObject : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete 3D objects.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    //delete transparent image from storage
    public function deleteTransparentImageObject($transparent_img_id)
    {
        try {
            $object_list = DB::select('SELECT image
                                        FROM my_design_transparent_image_master
                                        WHERE
                                        id = ? AND
                                        (my_design_id = "" OR my_design_id IS NULL)', [$transparent_img_id]);

            foreach ($object_list as $key) {
                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->deleteObjectFromS3($key->image, 'my_design');
                } else {
                    (new ImageController())->unlinkFileFromLocalStorage($key->image, Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY'));
                }

                DB::beginTransaction();
                DB::delete('DELETE FROM my_design_transparent_image_master WHERE id = ? AND (my_design_id = "" OR my_design_id IS NULL) ', [$transparent_img_id]);
                DB::commit();
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteTransparentImageObject', $e);
            //      Log::error("deleteTransparentImageObject : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
    }

    public function deleteMyDesigns($user_id)
    {
        try {

            $used_stock_photos_ids = DB::select('SELECT id, image, json_data, json_file_name
                                                    FROM
                                                      my_design_master
                                                    WHERE
                                                      user_id = ?', [$user_id]);

            foreach ($used_stock_photos_ids as $key) {

                DB::beginTransaction();
                DB::delete('delete from my_design_master where id = ? AND user_id = ?', [$key->id, $user_id]);
                DB::commit();

                (new ImageController())->deleteMyDesign($key->image);
                if ($key->json_data == null) {
                    (new ImageController())->deleteJsonData($key->json_file_name);
                }
                $this->deleteMyDesignIdFromTheList($key->id);
                $this->delete3DObjectImages($key->id);
            }

            $response = Response::json(['code' => 200, 'message' => 'Design deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyDesigns', $e);
            //        Log::error("deleteMyDesigns : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete my designs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function deleteProfile($user_id)
    {
        try {

            $user_profile = DB::select('SELECT profile_img
                                                    FROM
                                                      user_detail
                                                    WHERE
                                                      user_id = ?', [$user_id]);

            if ($user_profile[0]->profile_img != '' or $user_profile[0]->profile_img != null) {
                (new ImageController())->deleteUserProfile($user_profile[0]->profile_img);
            }

            $response = Response::json(['code' => 200, 'message' => 'Design deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteProfile', $e);
            //        Log::error("deleteProfile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete profile.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function deleteMyDesignIdFromTheList($my_design_id)
    {
        try {

            //delete stock images
            $existed_data = DB::select('SELECT pixabay_image_id
                                            FROM
                                              stock_photos_master
                                            WHERE
                                              find_in_set("'.$my_design_id.'",my_design_ids)');

            foreach ($existed_data as $key) {

                DB::beginTransaction();
                DB::update('UPDATE stock_photos_master
                                      SET
                                        my_design_ids =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$my_design_id.',", ","))
                                      WHERE
                                        pixabay_image_id = ? AND
                                        FIND_IN_SET("'.$my_design_id.'", my_design_ids)', [$key->pixabay_image_id]);
                DB::commit();

                $this->deleteUnusedStockPhotos($key->pixabay_image_id);
            }

            //delete 3D images
            $existed_3d_data = DB::select('SELECT id,
                                              my_design_id,
                                              image
                                            FROM
                                              my_design_3d_image_master
                                            WHERE
                                              find_in_set("'.$my_design_id.'",my_design_id)');
            foreach ($existed_3d_data as $key) {
                DB::beginTransaction();
                $data_3d_image = DB::update('UPDATE my_design_3d_image_master
                                      SET
                                        my_design_id =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_id, ","),  ",'.$my_design_id.',",  ","))
                                      WHERE
                                        FIND_IN_SET("'.$my_design_id.'", my_design_id)
                                         AND id  = '.$key->id);
                DB::commit();
                $this->delete3DImageObject($key->id);
            }

            //delete transparent images
            $existed_transparent_data = DB::select('SELECT id,
                                              my_design_id,
                                              image
                                            FROM
                                              my_design_transparent_image_master
                                            WHERE
                                              find_in_set("'.$my_design_id.'",my_design_id)');
            foreach ($existed_transparent_data as $key) {

                DB::beginTransaction();
                $data_transparent_image = DB::update('UPDATE my_design_transparent_image_master
                                      SET
                                        my_design_id =
                                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_id, ","),  ",'.$my_design_id.',",  ","))
                                      WHERE
                                        FIND_IN_SET("'.$my_design_id.'", my_design_id) AND id  = '.$key->id);

                DB::commit();

                $this->deleteTransparentImageObject($key->id);
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyDesignIdFromTheList', $e);
            //        Log::error("deleteMyDesignIdFromTheList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete stock photos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    // Delete unused images
    public function deleteUnusedImages($sample_image, $objec_images, $transparent_images)
    {
        try {

            if (($response = (new ImageController())->deleteMyDesign($sample_image)) != '') {
                return $response;
            }

            foreach ($objec_images as $key) {
                if (($response = (new ImageController())->delete3DObjectImage($key)) != '') {
                    return $response;
                }
            }

            foreach ($transparent_images as $key) {
                if (($response = (new ImageController())->deleteTransparentImage($key)) != '') {
                    return $response;
                }
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('deleteUnusedImages', $e);
            //        Log::error("deleteUnusedImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

        return $response;
    }

    // Delete unused stock photos
    public function deleteUnusedStockPhotos($pixabay_image_id)
    {
        try {

            $unused_images = DB::select('SELECT image,pixabay_image_id
                                                    FROM
                                                      stock_photos_master
                                                    WHERE
                                                      pixabay_image_id = ? AND
                                                      (my_design_ids = "" OR my_design_ids IS NULL)', [$pixabay_image_id]);
            foreach ($unused_images as $key) {
                (new ImageController())->deleteStockPhotos($key->image);
            }

            DB::beginTransaction();
            DB::delete('DELETE FROM stock_photos_master WHERE pixabay_image_id = ? AND (my_design_ids = "" OR my_design_ids IS NULL)', [$pixabay_image_id]);
            DB::commit();

            return '';

        } catch (Exception $e) {
            (new ImageController())->logs('deleteUnusedStockPhotos', $e);
            //        Log::error("deleteUnusedStockPhotos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    public function updateStockPhotosList($stock_photos_id_list, $my_design_id)
    {
        try {

            //return $images_array;
            foreach ($stock_photos_id_list as $key) {

                $is_exist = DB::select('SELECT 1 FROM stock_photos_master
                                                    WHERE
                                                    pixabay_image_id = ? AND
                                                    my_design_ids IN("'.$my_design_id.'")', [$key]);

                if (count($is_exist) == 0) {
                    $get_my_design_ids = DB::select('SELECT my_design_ids FROM stock_photos_master
                                                    WHERE
                                                    pixabay_image_id = ?', [$key]);

                    $my_design_ids = $get_my_design_ids[0]->my_design_ids;

                    DB::beginTransaction();
                    DB::update('UPDATE
                                    stock_photos_master SET
                                    my_design_ids = ? WHERE pixabay_image_id = ? ', [$my_design_ids, $key]);
                    DB::commit();
                }
            }

            $response = '';

        } catch (Exception $e) {
            (new ImageController())->logs('updateStockPhotosList', $e);
            //        Log::error("updateStockPhotosList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add stock photos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getExpiry()
    {
        try {

            $array = [
                'from_env_AWS_BUCKET' => env('AWS_BUCKET'),
                'from_env_STORAGE' => env('STORAGE'),
                'from_env_APP_ENV' => env('APP_ENV'),
                'from_config_AWS_BUCKET' => Config::get('constant.AWS_BUCKET'),
                'from_config_STORAGE' => Config::get('constant.STORAGE'),
                'from_config_APP_ENV' => Config::get('constant.APP_ENV'),
            ];

            return $array;
            //return Config::get('constant.IMAGE_BUCKET_ORIGINAL_IMG_PATH');
            $days_to_add = intval(9.125);
            $expires = '2019-10-25 11:05:40';
            $date = new DateTime($expires);
            $date->modify("+$days_to_add day");

            return $date->format('Y-m-d H:i:s');

        } catch (Exception $e) {
            (new ImageController())->logs('getExpiry', $e);
            //        Log::error("getExpiry : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get expiry.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function deleteAccount(Request $request)
    {

        try {

            $request = json_decode($request->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter([
                'user_id',
            ], $request)) != ''
            ) {
                return $response;
            }

            $user_id = $request->user_id;

            if ($user_id != 1) {
                //Log::info('request_data', ['request_data' => $request]);
                $create_time = date('Y-m-d H:i:s');

                $record_of_user_master = DB::select('SELECT * FROM user_master WHERE id = ?', [$user_id]);
                $record_of_user_detail = DB::select('SELECT * FROM user_detail WHERE user_id = ?', [$user_id]);
                $record_of_my_design_tracking = DB::select('SELECT * FROM my_design_tracking_master WHERE user_id = ? ORDER BY create_time DESC', [$user_id]);
                $record_of_subscriptions = DB::select('SELECT * FROM subscriptions WHERE user_id = ? ORDER BY update_time DESC', [$user_id]);
                $record_of_payment_status = DB::select('SELECT * FROM payment_status_master WHERE user_id = ? ORDER BY update_time DESC', [$user_id]);

                DB::beginTransaction();
                DB::insert('INSERT INTO deleted_user_bkp_master (
                              user_id,
                              record_of_user_master,
                              record_of_user_detail,
                              record_of_my_design_tracking,
                              record_of_subscriptions,
                              record_of_payment_status,
                              is_deleted,
                              is_active,
                              create_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $user_id,
                    json_encode($record_of_user_master),
                    json_encode($record_of_user_detail),
                    json_encode($record_of_my_design_tracking),
                    json_encode($record_of_subscriptions),
                    json_encode($record_of_payment_status),
                    1,
                    1,
                    $create_time,
                ]);
                DB::commit();

                $this->deleteProfile($user_id);
                $this->deleteMyDesigns($user_id);
                $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

                DB::delete('DELETE FROM user_master WHERE id = ?', [$user_id]);
                DB::delete('DELETE FROM subscriptions WHERE user_id = ?', [$user_id]);
                DB::commit();

                (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'account_deleted');

                $response = Response::json(['code' => 200, 'message' => 'Account deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            } else {
                $response = Response::json(['code' => 201, 'message' => 'Invalid user.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteAccount', $e);
            //        Log::error("deleteAccount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete account.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function storeCropImage($is_image_user_uploaded, $crop_image_name, $crop_image_array, $fle_name)
    {
        if ($is_image_user_uploaded == 1) {
            $des_folder_name = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY');
            (new ImageController())->saveSingleFileInToLocal($fle_name, $crop_image_name, $des_folder_name);
        } elseif ($is_image_user_uploaded == 2) {
            (new ImageController())->saveResourceImage($crop_image_array);
        } else {
            $des_folder_name = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
            (new ImageController())->saveSingleFileInToLocal($fle_name, $crop_image_name, $des_folder_name);
        }

        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

            if ($is_image_user_uploaded == 1) {
                $des_folder_path = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY');
                (new ImageController())->saveSingleImageInToS3($crop_image_name, $des_folder_path, 'user_uploaded_original');
            } elseif ($is_image_user_uploaded == 2) {
                (new ImageController())->saveResourceImageInToS3($crop_image_name);
            } else {
                $des_folder_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY');
                (new ImageController())->saveSingleImageInToS3($crop_image_name, $des_folder_path, 'original');
            }
        }
    }

    public function removeUnUsedCropImages($deleted_crop_image)
    {
        foreach ($deleted_crop_image as $row) {
            $is_image_user_uploaded = $row->is_image_user_uploaded;

            if ($is_image_user_uploaded == 1) {
                (new ImageController())->unlinkFileFromLocalStorage($row->crop_image, Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY'));
            } elseif ($is_image_user_uploaded == 2) {
                (new ImageController())->unlinkFileFromLocalStorage($row->crop_image, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
            } else {
                (new ImageController())->unlinkFileFromLocalStorage($row->crop_image, Config::get('constant.ORIGINAL_IMAGES_DIRECTORY'));
            }

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                if ($is_image_user_uploaded == 1) {
                    (new ImageController())->deleteObjectFromS3($row->crop_image, 'user_uploaded_original');
                } elseif ($is_image_user_uploaded == 2) {
                    (new ImageController())->deleteObjectFromS3($row->crop_image, 'resource');
                } else {
                    (new ImageController())->deleteObjectFromS3($row->crop_image, 'original');
                }
            }
        }
    }

    public function moveToFolder($user_id, $des_folder_id, $my_design_id_int, $source_folder_id = '')
    {
        try {
            /* Move from existing folder to new folder */
            if ($des_folder_id != '' && $source_folder_id != '') {

                /* Check if design already exist in destination folder */
                $existing_design = DB::select('SELECT id, my_design_ids FROM design_folder_master WHERE uuid = ? AND user_id = ?', [$des_folder_id, $user_id]);
                $existing_design_ids = $existing_design[0]->my_design_ids;
                $old_id_list = explode(',', $existing_design_ids);
                $already_exist = in_array($my_design_id_int, $old_id_list);

                if (! $already_exist) {
                    $existing_design_ids = $existing_design[0]->my_design_ids;
                    $integer_folder_id = $existing_design[0]->id;

                    /* Remove design from existing folder */
                    DB::update('UPDATE design_folder_master
                      SET
                        my_design_ids =
                        TRIM(BOTH "," FROM REPLACE(CONCAT(",", my_design_ids, ","), ",'.$my_design_id_int.',", ","))
                      WHERE
                        FIND_IN_SET("'.$my_design_id_int.'", my_design_ids) AND uuid = ?', [$source_folder_id]);

                    /* Update new folder id in design data */
                    DB::update('UPDATE my_design_master SET folder_id = ? WHERE id = ?', [$integer_folder_id, $my_design_id_int]);

                    $update_time = gmdate('Y-m-d H:i:s');
                    $increase_time = gmdate('Y-m-d H:i:s', strtotime('+1 seconds', strtotime($update_time)));

                    /* Check if destination folder has designs, If yes then append design_id otherwise add design_id directly */
                    ($existing_design_ids == null) ? $my_design_ids = $my_design_id_int : $my_design_ids = $existing_design_ids.','.$my_design_id_int;
                    DB::update('UPDATE design_folder_master SET my_design_ids = ?, update_time = ? WHERE uuid = ?', [$my_design_ids, $increase_time, $des_folder_id]);

                    $this->deleteAllRedisKeys("getFolders:$user_id");
                    $this->deleteAllRedisKeys("getMyDesignFolder$user_id");
                    $this->deleteAllRedisKeys("getMyVideoDesignFolder$user_id");
                    $this->deleteAllRedisKeys("getMyIntroDesignFolder$user_id");
                }

                return 'Design moved to folder successfully.';

                /* Move design to folder */
            } elseif ($des_folder_id != '') {

                /* Check if design already exist in destination folder */
                $existing_design = DB::select('SELECT id, my_design_ids FROM design_folder_master WHERE uuid = ? AND user_id = ?', [$des_folder_id, $user_id]);
                $existing_design_ids = $existing_design[0]->my_design_ids;
                $old_id_list = explode(',', $existing_design_ids);
                $already_exist = in_array($my_design_id_int, $old_id_list);

                if (! $already_exist) {
                    $existing_design_ids = $existing_design[0]->my_design_ids;
                    $integer_folder_id = $existing_design[0]->id;

                    /* Add folder id in design data */
                    DB::update('UPDATE my_design_master SET folder_id = ? WHERE id = ?', [$integer_folder_id, $my_design_id_int]);

                    /* Check if destination folder has designs, If yes then append design_id otherwise add design_id directly */
                    ($existing_design_ids == null) ? $my_design_ids = $my_design_id_int : $my_design_ids = $existing_design_ids.','.$my_design_id_int;
                    DB::update('UPDATE design_folder_master SET my_design_ids = ? WHERE uuid = ?', [$my_design_ids, $des_folder_id]);

                    $this->deleteAllRedisKeys("getFolders:$user_id");
                    $this->deleteAllRedisKeys("getMyDesignFolder$user_id");
                    $this->deleteAllRedisKeys("getMyVideoDesignFolder$user_id");
                    $this->deleteAllRedisKeys("getMyIntroDesignFolder$user_id");
                }

                return 'Design added to folder successfully.';
            } else {
                return false;
            }

        } catch (Exception $e) {
            (new ImageController())->logs('moveToFolder', $e);

            return false;
        }
    }

    /* =================================| Video Module |============================= */
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/generateVideo",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="generateVideo",
     *        summary="Generate video",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *   	@SWG\Parameter(
     *         name="transparent_img",
     *         in="formData",
     *         description="Transparent image",
     *         required=true,
     *         type="file"
     *     ),
     * 		@SWG\Parameter(
     *          in="formData",
     *          name="request_data",
     *          required=true,
     *          type="string",
     *          description="Give video_name, is_video_user_uploaded, is_trim, quality in json object",
     *
     *         @SWG\Schema(
     *              required={"video_name","is_video_user_uploaded","out_width","out_height","is_trim","quality", "start_time", "end_time","trim_duration","is_audio_mute", "is_audio_trim", "audio_name","is_audio_user_uploaded","audio_duration", "audio_start_time","audio_end_time"},
     *
     *              @SWG\Property(property="video_name",  type="text", example="1.mp4", description=""),
     *              @SWG\Property(property="is_video_user_uploaded",  type="integer", example=1, description=""),
     *              @SWG\Property(property="out_width",  type="integer", example=1280, description=""),
     *              @SWG\Property(property="out_height",type="integer", example=720, description=""),
     *              @SWG\Property(property="is_trim",type="integer", example=0, description=""),
     *              @SWG\Property(property="quality",type="integer", example=1, description="1 = Free(Web quality), 2 = HD quality"),
     *              @SWG\Property(property="start_time",type="integer", example="00:00", description="Video's trimming start time ,if is_trim = 1 compulsory"),
     *              @SWG\Property(property="end_time",type="integer", example="09:00", description="Video's trimming end time ,if is_trim = 1 compulsory"),
     *              @SWG\Property(property="trim_duration",type="integer", example="05:00", description="Video's trimming duration time ,if is_trim = 1 compulsory"),
     *              @SWG\Property(property="is_audio_mute",type="integer", example=0, description="1=free, 0=paid"),
     *              @SWG\Property(property="is_audio_trim",type="integer", example=0, description="1=free, 0=paid"),
     *              @SWG\Property(property="audio_name",type="test", example="5_sec.mp3", description=""),
     *              @SWG\Property(property="is_audio_user_uploaded",type="integer", example=0, description=""),
     *              @SWG\Property(property="audio_duration",type="integer", example="0.01", description="1=free, 0=paid"),
     *              @SWG\Property(property="audio_start_time",type="integer", example="04:00", description="1=free, 0=paid"),
     *              @SWG\Property(property="audio_end_time",type="integer", example="05:00", description="1=free, 0=paid"),
     *              @SWG\Property(property="content_type",type="integer", example="9", description="1=free, 0=paid"),
     *              @SWG\Property(property="content_id",type="integer", example="laghh7d929914d", description=""),
     *              @SWG\Property(property="my_design_id",type="integer", example="88rxg827b23627", description=""),
     *
     *              ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video is ready to download.","cause":"","data":{ "status": 1,"output_video": "http://192.168.0.116/photoadking_testing/image_bucket/temp/5c57bf211ece8_video_file_1549254433.mp4","est_time_sec":""}}, description="0=Queue,1=ready,2=failed"),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} generateVideo   generateVideo
     *
     * @apiName generateVideo
     *
     * @apiGroup FFmpeg
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "video_name": "1.mp4", //compulsory
     * "is_video_user_uploaded": 1,//compulsory
     * "out_width": 1280, //compulsory
     * "out_height": 720, //compulsory
     * "is_trim": 0, //compulsory
     * "quality": 1, //compulsory, 1 = Free(Web quality), 2 = HD quality
     * "start_time": "00.00", //Video's trimming start time ,if is_trim = 1 compulsory
     * "end_time": "09.00", //Video's trimming end time ,if is_trim = 1 compulsory
     * "trim_duration": "5.00", //Video's trimming duration time ,if is_trim = 1 compulsory
     * "is_audio_mute":0, // 1=Audio mute
     * "is_audio_trim": 0, // 1=audio trim
     * "audio_name": "5_sec.mp3",
     * "is_audio_user_uploaded": 0, //if audio_name, compulsory
     * "audio_duration": "0.01", //if audio_name, compulsory
     * "audio_start_time": "05.00", //if audio_name, compulsory
     * "audio_end_time": "04.00" //if audio_name, compulsory
     * "content_type": "2"
     * "content_id": "laghh7d929914d"
     * "my_design_id": "88rxg827b23627"
     * }
     * transparent_img:image.png //compulsory
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
    public function generateVideo(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            if ($user_detail->roles) {
                $role_id = $user_detail->roles->first()->id;
            } else {
                Log::error('generateVideo : Role did not fetched.', ['token' => $token, 'user_id' => $user_id]);

                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate video.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (! $request_body->has('request_data')) {
                Log::error('generateVideo : Required field request_data is missing or empty.');

                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));

            $get_my_design_id = null;
            if (isset($request->my_design_id)) {
                $my_design_uuid = $request->my_design_id;
                $my_design_id = DB::select('SELECT id FROM my_design_master WHERE uuid=?', [$my_design_uuid]);
                if (count($my_design_id) > 0) {
                    $get_my_design_id = $my_design_id[0]->id;
                }
            }

            if (! $request_body->hasFile('transparent_img')) {
                Log::error('generateVideo : Required field transparent_img is missing or empty.');

                return Response::json(['code' => 201, 'message' => 'Required field transparent_img is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['video_name', 'is_video_user_uploaded', 'out_width', 'out_height', 'is_trim', 'quality'], $request)) != '') {
                Log::error('generateVideo : Required field some request\'s data is missing or empty.', ['response' => $response]);

                return $response;
            }

            if ($request->quality == Config::get('constant.FULL_HD_VIDEO') && $role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
                Log::error('generateVideo : Free users are not authorized to download design of Pro qualities.', ['token' => $token, 'user_id' => $user_id, 'role_id' => $role_id, 'quality' => $request->quality]);

                return Response::json(['code' => 432, 'message' => 'Free users are not authorized to download design of Pro qualities.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($request->is_trim) {
                if (($response = (new VerificationController())->validateRequiredParameter(['start_time', 'end_time', 'trim_duration'], $request)) != '') {
                    Log::error('generateVideo : Required field some is_trim\'s data is missing or empty.', ['is_trim' => $request->is_trim, 'response' => $response]);

                    return $response;
                }
            }

            if (isset($request->audio_name)) {
                if (($response = (new VerificationController())->validateRequiredParameter(['is_audio_user_uploaded', 'audio_start_time', 'audio_end_time', 'audio_duration', 'is_audio_trim'], $request)) != '') {
                    Log::error('generateVideo : Required field some audio_name\'s data is missing or empty.', ['audio_name' => $request->audio_name]);

                    return $response;
                }
            }

            $request->user_id = $user_id;
            $request->get_my_design_id = $get_my_design_id;
            $json_request_data = json_encode($request);

            // Check video generate limit
            $queue_record = DB::select('SELECT COUNT(id) total
                    FROM video_template_jobs
                    WHERE status = 0 AND user_id = ?', [$user_id]);
            $queue_limit = Config::get('constant.QUEUE_VIDEO_LIMIT');

            if ($queue_record && $queue_record[0]->total >= $queue_limit) {
                Log::error('generateVideo : You can\'t add more than '.$queue_limit.' videos for download at a time. Please try after some time.', ['user_id' => $user_id, 'get_my_design_id' => $get_my_design_id]);

                return Response::json(['code' => 201, 'message' => 'You can\'t add more than '.$queue_limit.' videos for download at a time. Please try after some time.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $transparent_img = Input::file('transparent_img');
            if (($response = (new ImageController())->verifyTransparentImage($transparent_img)) != '') {
                Log::error('generateVideo : Transparent Image File did not verified successfully.', ['response' => $response]);

                return $response;
            }
            $img_size = $transparent_img->getSize();

            //generate unique file name and save in directory
            $transparent_image = (new ImageController())->generateNewFileName($user_id.'transparent_image', $transparent_img);
            (new ImageController())->saveMultipartTempFile($transparent_image, $transparent_img);

            //Send all data for generate video
            $job = new VideoTemplateJob($transparent_image, $img_size, $json_request_data);
            $data = $this->dispatch($job);
            $result = $job->getResponse();

            if ($result['result_status'] == 0) {
                Log::error('generateVideo : '.Config::get('constant.EXCEPTION_ERROR').' generate video.', ['result' => $result]);
                $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate video.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Video generated successfully', 'cause' => '', 'data' => ['download_id' => $result['download_id']]]);
            }

        } catch (Exception $e) {
            //Log::error("generateVideo : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            (new ImageController())->logs('generateVideo', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            $this->addVideoGenerateHistory($e->getMessage(), $user_id, null, $get_my_design_id, null, 2, null);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/checkReadyToDownload",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="checkReadyToDownload",
     *        summary="Check video ready to download",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"download_id"},
     *
     *          @SWG\Property(property="download_id",  type="text", example="NWM1N2JkZTI1YTVmZV92aWRlb19maWxlXzE1NDkyNTQxMTQubXA0", description=""),
     *          @SWG\Property(property="is_dismiss",  type="integer", example="0", description="1=cancel download"),
     *        ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video is ready to download.","cause":"","data":{ "status": 1,"output_video": "http://192.168.0.116/photoadking_testing/image_bucket/temp/5c57bf211ece8_video_file_1549254433.mp4","est_time_sec":""}}, description="0=Queue,1=ready,2=failed"),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} checkReadyToDownload   checkReadyToDownload
     *
     * @apiName checkReadyToDownload
     *
     * @apiGroup FFmpeg
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "download_id":"NWM1N2JkZTI1YTVmZV92aWRlb19maWxlXzE1NDkyNTQxMTQubXA0" //compulsory
     * "is_dismiss":0 //optional 1=cancel download
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video is ready to download.",
     * "cause": "",
     * "data": {
     * "status": 1, //0=Queue,1=ready,2=failed
     * "output_video": "http://192.168.0.116/photoadking_testing/image_bucket/temp/5c57bf211ece8_video_file_1549254433.mp4",
     * "est_time_sec":""
     * }
     * }
     */
    public function checkReadyToDownload(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['download_id', 'filename'], $request)) != '') {
                return $response;
            }

            $download_id = $request->download_id;
            $filename = 'Testing_'.$request->filename;
            $is_dismiss = isset($request->is_dismiss) ? $request->is_dismiss : '';
            $output_video = '';
            $result = DB::select('SELECT id, output_video, status FROM video_template_jobs WHERE download_id = ?', [$download_id]);

            if (count($result) > 0) {
                //Log::error('error :', [count($result)]);
                $output_video = $result[0]->output_video;

                if ($is_dismiss) {
                    $job = new DeleteCancelDownloadDataJob($download_id);
                    $data = $this->dispatch($job);

                    $status = 2;
                    $est_time_sec = '';

                } else {
                    $status = $result[0]->status;
                    $id = $result[0]->id;

                    $queue_record = DB::select('SELECT COUNT(id) total
                      FROM video_template_jobs
                      WHERE status = 0 AND id <= ?', [$id]);

                    if ($queue_record[0]->total != 0) {
                        $est_time_sec = $queue_record[0]->total * 6;
                    } else {
                        $est_time_sec = '';
                    }
                }
            } else {
                $status = 2;
                $est_time_sec = '';
            }
            $msg = '';
            $http_code = 200;
            if ($status == 0) {
                $msg = 'Video is not ready to download';
                $result = ['status' => $status, 'output_video' => '', 'est_time_sec' => $est_time_sec];
            }
            if ($status == 1) {
                $msg = 'Video is ready to download';
                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    $output_video_file = (new ImageController())->generateDownloadURL($output_video, 'temp', $filename);
                } else {
                    $output_video_file = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.TEMP_DIRECTORY').$output_video;
                }

                $result = ['status' => $status, 'output_video' => $output_video_file, 'est_time_sec' => ''];
            }
            if ($status == 2) {
                $http_code = 201;
                $msg = "Sorry, we couldn't generate video. Please, try again.";
                $result = ['status' => $status, 'output_video' => '', 'est_time_sec' => $est_time_sec];
            }

            $response = Response::json(['code' => $http_code, 'message' => $msg, 'cause' => '', 'data' => $result]);
        } catch (Exception $e) {
            //Log::error("checkReadyToDownload : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            (new ImageController())->logs('checkReadyToDownload', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'check ready to download.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| User's video |============================= */
    public function uploadVideo(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            if (($response = (new VerificationController())->validateRequiredParameter(['unique_id', 'chunks', 'chunk'], $request_body)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->checkIsUserPro($user_id)) != '') {
                return $response;
            }

            $create_time = date('Y-m-d H:i:s');

            $response = $this->uploadChunkFile($request_body);
            if (is_object($response['data'])) {

                $video_array = $response['data'];
                $video_size = $video_array->getSize();
                if (($response = (new UserVerificationController())->verifyVideo($video_array)) != '') {
                    return $response;
                }

                if (($response = (new VerificationController())->validateUserToUploadImage($user_id)) != '') {
                    return $response;
                }

                $video = (new ImageController())->generateNewFileName('user_uploaded_video', $video_array);
                (new ImageController())->saveUserUploadedVideo($video, $video_array);

                $thum_video_file_name = (new ImageController())->generateThumbnailFileName('user_uploaded_video', $video_array);

                $original_video_path = '../..'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY').$video;
                $thumbnailFilePath = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY').$thum_video_file_name;
                $thumbnail_file_path = 'user_uploaded_video';
                if (($response = (new ImageController())->getAndSaveOriginalImageFromVideo($original_video_path, $thumbnailFilePath)) != '') {
                    return $response;
                }

                $video_info = (new ImageController())->getVideoInformation($original_video_path);

                $format_name = $video_info['format_name'];
                $duration = $video_info['duration'];
                $width = $video_info['width'];
                $height = $video_info['height'];
                $size = $video_info['size'];
                $bit_rate = $video_info['bit_rate'];
                $title = isset($video_info['title']) && $video_info['title'] != '' ? $video_info['title'] : ' ';
                $genre = isset($video_info['genre']) && $video_info['genre'] != '' ? $video_info['genre'] : ' ';
                $artist = isset($video_info['artist']) && $video_info['artist'] != '' ? $video_info['artist'] : ' ';
                //generate & save webp images
                $file_name = (new ImageController())->saveUserUploadedWebpOriginalImage($thum_video_file_name);
                $dimension = (new ImageController())->saveUserUploadedWebpThumbnailImage($thum_video_file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveUserUploadedVideoInToS3($video, $thum_video_file_name);
                    (new ImageController())->saveUserUploadedImageInToS3($thum_video_file_name, $file_name);
                    //(new ImageController())->saveUserUploadedWebpImageInToS3($thum_video_file_name);
                }

                $uuid = (new ImageController())->generateUUID();
                DB::insert('INSERT INTO user_uploaded_video
                        (user_id, uuid, file_name, video_thumbnail, web_image, format_name, file_path, duration, width, height, size, bit_rate, genre, title, artist, is_active, create_time)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$user_id, $uuid, $video, $thum_video_file_name, $file_name, $format_name, $thumbnail_file_path, $duration, $width, $height, $size, $bit_rate, $genre, $title, $artist, 1, $create_time]);

                $this->increaseFileSize($user_id, $video_size);

                $response = Response::json(['code' => 200, 'message' => 'Video uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('uploadVideoByUser', $e);
            //Log::error("uploadVideoByUser : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'user upload video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/uploadVideoByUser",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="uploadVideoByUser",
     *        summary="Upload Video By User",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="file uploading",
     *         required=true,
     *         type="file"
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video uploaded successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} uploadVideoByUser   uploadVideoByUser
     *
     * @apiName uploadVideoByUser
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *   Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * file : 1.mp4
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video uploaded successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function uploadVideoByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $user_id = $user_detail->id;

            if (($response = (new VerificationController())->checkIsUserPro($user_id)) != '') {
                return $response;
            }

            $create_time = date('Y-m-d H:i:s');

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $video_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyVideo($video_array)) != '') {
                    return $response;
                }

                $video_size = $video_array->getSize();
                if (($response = (new VerificationController())->validateUserToUploadImage($user_id)) != '') {
                    return $response;
                }

                $video = (new ImageController())->generateNewFileName('user_uploaded_video', $video_array);
                (new ImageController())->saveUserUploadedVideo($video, $video_array);

                $thum_video_file_name = (new ImageController())->generateThumbnailFileName('user_uploaded_video', $video_array);

                $original_video_path = '../..'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY').$video;
                $thumbnailFilePath = '../..'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY').$thum_video_file_name;
                $thumbnail_file_path = 'user_uploaded_video';
                //        dd($original_video_path,$thumbnailFilePath);
                if (($response = (new ImageController())->getAndSaveOriginalImageFromVideo($original_video_path, $thumbnailFilePath)) != '') {
                    return $response;
                }

                $video_info = (new ImageController())->getVideoInformation($original_video_path);

                $format_name = $video_info['format_name'];
                $duration = $video_info['duration'];
                $width = $video_info['width'];
                $height = $video_info['height'];
                $size = $video_info['size'];
                $bit_rate = $video_info['bit_rate'];
                $title = isset($video_info['title']) && $video_info['title'] != '' ? $video_info['title'] : ' ';
                $genre = isset($video_info['genre']) && $video_info['genre'] != '' ? $video_info['genre'] : ' ';
                $artist = isset($video_info['artist']) && $video_info['artist'] != '' ? $video_info['artist'] : ' ';
                //generate & save webp images
                $file_name = (new ImageController())->saveUserUploadedWebpOriginalImage($thum_video_file_name);
                $dimension = (new ImageController())->saveUserUploadedWebpThumbnailImage($thum_video_file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveUserUploadedVideoInToS3($video, $thum_video_file_name);
                    (new ImageController())->saveUserUploadedImageInToS3($thum_video_file_name, $file_name);
                    //          (new ImageController())->saveUserUploadedWebpImageInToS3($thum_video_file_name);
                }

            }
            $uuid = (new ImageController())->generateUUID();
            DB::beginTransaction();
            DB::insert('INSERT INTO user_uploaded_video
                        (user_id,uuid,file_name,video_thumbnail,web_image,format_name,file_path,duration,width,height,size,bit_rate,genre,title,artist,is_active,create_time)
                  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',
                [$user_id, $uuid, $video, $thum_video_file_name, $file_name, $format_name, $thumbnail_file_path, $duration, $width, $height, $size, $bit_rate, $genre, $title, $artist, 1, $create_time]);
            DB::commit();

            $this->increaseFileSize($user_id, $video_size);
            $image_detail['duration'] = $duration;
            $image_detail['update_time'] = $create_time;
            $image_detail['user_uploaded_video_id'] = $uuid;
            $image_detail['video_file'] = Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY_OF_DIGITAL_OCEAN').$video;
            $image_detail['video_name'] = $video;
            $image_detail['video_thumbnail'] = Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$thum_video_file_name;
            $image_detail['web_image_original'] = Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$file_name;
            $image_detail['web_image_thumbnail'] = Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$file_name;

            $response = Response::json(['code' => 200, 'message' => 'Video uploaded successfully.', 'cause' => '', 'data' => ['result' => $image_detail]]);
        } catch (Exception $e) {
            (new ImageController())->logs('uploadVideoByUser', $e);
            //      Log::error("uploadVideoByUser : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'user upload video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyUploadedVideos",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyUploadedVideos",
     *        summary="Get My Uploaded Videos",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Videos fetched successfully.","cause":"","data":{"total_record":5,"is_next_page":false,"result":{{"user_uploaded_video_id":7,"user_id":3,"video_file":"http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_video/5d036a7017cf6_user_uploaded_video_1560504944.mp4","video_name":"5d036a7017cf6_user_uploaded_video_1560504944.mp4","video_thumbnail":"http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_video_thumbnail/5d036a718ef8c_user_uploaded_video_1560504945.png","web_image_original":"http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_webp_original/5d036a718ef8c_user_uploaded_video_1560504945.webp","web_image_thumbnail":"http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_webp_thumbnail/5d036a718ef8c_user_uploaded_video_1560504945.webp","duration":"00:00:03","update_time":"2019-06-14 09:35:47"}}}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} getMyUploadedVideos   getMyUploadedVideos
     *
     * @apiName getMyUploadedVideos
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1,
     * "item_count":50
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Videos fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 7,
     * "is_next_page": false,
     * "result": [
     * {
     * "user_uploaded_video_id": 4,
     * "user_id": 3,
     * "video_file": "http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_video/5d03501180080_user_uploaded_video_1560498193.mp4",
     * "video_name": "5d03501180080_user_uploaded_video_1560498193.mp4",
     * "video_thumbnail": "http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_video_thumbnail/5d03501523cbf_user_uploaded_video_1560498197.png",
     * "duration": "00:00:03",
     * "update_time": "2019-06-14 07:43:21"
     * },
     * {
     * "user_uploaded_video_id": 3,
     * "user_id": 3,
     * "video_file": "http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_video/5d022e1c27282_user_uploaded_video_1560423964.mp4",
     * "video_name": "5d022e1c27282_user_uploaded_video_1560423964.mp4",
     * "video_thumbnail": "http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_video_thumbnail/5d022e1d08684_user_uploaded_video_1560423965.png",
     * "duration": "00:00:03",
     * "update_time": "2019-06-13 11:06:06"
     * }
     * ]
     * }
     * }
     */
    public function getMyUploadedVideos(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            //Log::info('request_data', ['request_data' => $request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getMyUploadedVideos:$this->page:$this->item_count:$this->user_id")) {
                $result = Cache::rememberforever("getMyUploadedVideos:$this->page:$this->item_count:$this->user_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(id) as total FROM user_uploaded_video WHERE user_id = ? AND is_active = ?', [$this->user_id, 1]);
                    $total_row = $total_row_result[0]->total;

                    $image_list = DB::select('SELECT
                                        uuv.uuid as user_uploaded_video_id,
                                        IF(uuv.file_name != "",CONCAT("'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY_OF_DIGITAL_OCEAN').'",file_name),"") as video_file,
                                        coalesce(uuv.file_name,"") AS video_name,
                                        IF(uuv.video_thumbnail != "",CONCAT("'.Config::get('constant.USER_UPLOAD_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",video_thumbnail),"") as video_thumbnail,
                                        IF(uuv.web_image != "",CONCAT("'.Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",web_image),"") as web_image_original,
                                        IF(uuv.web_image != "",CONCAT("'.Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",web_image),"") as web_image_thumbnail,
                                        uuv.duration,
                                        uuv.update_time
                                      FROM
                                        user_uploaded_video as uuv
                                      WHERE
                                        uuv.user_id = ? AND
                                        uuv.is_active = ?
                                      ORDER BY uuv.update_time DESC
                                      LIMIT ?,?', [$this->user_id, 1, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $image_list];
                });
            }

            $redis_result = Cache::get("getMyUploadedVideos:$this->page:$this->item_count:$this->user_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Videos fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyUploadedVideos', $e);
            //      Log::error("getMyUploadedVideos : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get my uploaded videos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteMyUploadedVideoById",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteMyUploadedVideoById",
     *        summary="Delete My Uploaded Video By Id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"user_uploaded_video_id"},
     *
     *          @SWG\Property(property="user_uploaded_video_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video deleted successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} deleteMyUploadedVideoById   deleteMyUploadedVideoById
     *
     * @apiName deleteMyUploadedVideoById
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "user_uploaded_video_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteMyUploadedVideoById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['user_uploaded_video_id'], $request)) != '') {
                return $response;
            }

            $user_uploaded_video_id = $request->user_uploaded_video_id;

            $delete_detail = DB::select('SELECT * FROM user_uploaded_video WHERE uuid = ? AND user_id = ?', [$user_uploaded_video_id, $user_id]);

            if (count($delete_detail) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Video does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $video_name = $delete_detail[0]->file_name;
            $video_thumbnail = $delete_detail[0]->video_thumbnail;
            $web_image = $delete_detail[0]->web_image;

            $my_design_detail = DB::select('SELECT *
                                      FROM my_design_master
                                      WHERE is_video_user_uploaded = ? AND user_id = ? AND video_name = ?',
                [1, $user_id, $video_name]);

            DB::beginTransaction();
            DB::delete('DELETE FROM user_uploaded_video WHERE uuid = ? AND user_id = ?', [$user_uploaded_video_id, $user_id]);
            DB::commit();

            if (count($my_design_detail) == 0) {
                (new ImageController())->deleteUserUploadedVideo($video_name, $video_thumbnail, $web_image);
            }

            $response = Response::json(['code' => 200, 'message' => 'Video deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyUploadedVideoById', $e);
            //      Log::error("deleteMyUploadedVideoById : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =================================| User's Audio |============================= */

    public function uploadAudio(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            if (($response = (new VerificationController())->validateRequiredParameter(['unique_id', 'chunks', 'chunk'], $request_body)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->checkIsUserPro($user_id)) != '') {
                return $response;
            }

            $create_time = date('Y-m-d H:i:s');

            $response = $this->uploadChunkFile($request_body);
            if (is_object($response['data'])) {

                $audio_array = $response['data'];
                $audio_size = $audio_array->getSize();

                if (($response = (new UserVerificationController())->verifyAudio($audio_array)) != '') {
                    return $response;
                }

                if (($response = (new VerificationController())->validateUserToUploadImage($user_id)) != '') {
                    return $response;
                }

                $request = json_decode($request_body->input('request_data'));
                $title = isset($request->title) ? $request->title : 'sample_audio';
                $tag = isset($request->tag) ? $request->tag : null;
                $audio = (new ImageController())->generateNewFileName('user_uploaded_audio', $audio_array);
                (new ImageController())->saveUserUploadedAudio($audio, $audio_array);

                $audio_detail = (new ImageController())->getAudioInformation('../..'.Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY').$audio);

                $result = json_decode(json_encode($audio_detail->all()), true);
                $format_name = isset($result['format_name']) ? $result['format_name'] : null;
                $duration = isset($result['duration']) ? date('H:i:s', intval($result['duration'])) : null;
                $size = isset($result['size']) ? number_format($result['size'] / 1048576, 2) : null;
                $bit_rate = isset($result['bit_rate']) ? $result['bit_rate'] : null;
                //$title = isset($result['tags']['title']) ? $result['tags']['title'] : NULL;
                $genre = isset($result['tags']['genre']) ? $result['tags']['genre'] : null;
                $artist = isset($result['tags']['artist']) ? $result['tags']['artist'] : null;

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveUserUploadedAudioInToS3($audio);
                }

                $uuid = (new ImageController())->generateUUID();
                DB::insert('INSERT INTO user_uploaded_audio
                        (user_id, uuid, file_name, format_name, duration, size, bit_rate, genre, tag, title,artist, is_active, create_time)
                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ',
                    [$user_id, $uuid, $audio, $format_name, $duration, $size, $bit_rate, $genre, $tag, $title, $artist, 1, $create_time]);

                $this->increaseFileSize($user_id, $audio_size);

                $response = Response::json(['code' => 200, 'message' => 'Audio uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
                $folder_path = '../..'.Config::get('constant.TEMP_DIRECTORY').$request_body->unique_id;
                shell_exec("rm -R $folder_path");
            }

        } catch (Exception $e) {
            (new ImageController())->logs('uploadAudio', $e);
            //Log::error("uploadAudio : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload audio by audio.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/uploadAudioByUser",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="uploadAudioByUser",
     *        summary="Upload Audio By User",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="file uploading",
     *         required=true,
     *         type="file"
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Audio uploaded successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} uploadAudioByUser uploadAudioByUser
     *
     * @apiName uploadAudioByUser
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "title":"test" // Audio's name
     * }
     * file:"music.mp3"
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Audio uploaded successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function uploadAudioByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $user_id = $user_detail->id;

            if (($response = (new VerificationController())->checkIsUserPro($user_id)) != '') {
                return $response;
            }

            $create_time = date('Y-m-d H:i:s');

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $audio_array = Input::file('file');
                if (($response = (new UserVerificationController())->verifyAudio($audio_array)) != '') {
                    return $response;
                }

                $audio_size = $audio_array->getSize();
                if (($response = (new VerificationController())->validateUserToUploadImage($user_id)) != '') {
                    return $response;
                }

                $request = json_decode($request_body->input('request_data'));
                $title = isset($request->title) ? $request->title : 'sample_audio';
                $tag = isset($request->tag) ? $request->tag : null;
                $audio = (new ImageController())->generateNewFileName('user_uploaded_audio', $audio_array);
                (new ImageController())->saveUserUploadedAudio($audio, $audio_array);

                $audio_detail = (new ImageController())->getAudioInformation('../..'.Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY').$audio);

                //dd($audio_detail->all()); //get ffprobe response into array

                $result = json_decode(json_encode($audio_detail->all()), true);
                $format_name = isset($result['format_name']) ? $result['format_name'] : null;
                $duration = isset($result['duration']) ? date('H:i:s', intval($result['duration'])) : null;
                $size = isset($result['size']) ? number_format($result['size'] / 1048576, 2) : null;
                $bit_rate = isset($result['bit_rate']) ? $result['bit_rate'] : null;
                //$title = isset($result['tags']['title']) ? $result['tags']['title'] : NULL;
                $genre = isset($result['tags']['genre']) ? $result['tags']['genre'] : null;
                $artist = isset($result['tags']['artist']) ? $result['tags']['artist'] : null;

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                    (new ImageController())->saveUserUploadedAudioInToS3($audio);

                }
            }

            $uuid = (new ImageController())->generateUUID();

            DB::beginTransaction();
            DB::insert('INSERT INTO user_uploaded_audio
                        (user_id,uuid,file_name,format_name, duration, size, bit_rate, genre, tag, title,artist, is_active, create_time)
                                VALUES(?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ',
                [$user_id, $uuid, $audio, $format_name, $duration, $size, $bit_rate, $genre, $tag, $title, $artist, 1, $create_time]);
            DB::commit();

            $this->increaseFileSize($user_id, $audio_size);
            $image_detail['artist'] = $artist;
            $image_detail['audio_file'] = Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY_OF_DIGITAL_OCEAN').$audio;
            $image_detail['audio_format'] = $format_name;
            $image_detail['audio_name'] = $audio;
            $image_detail['bit_rate'] = $bit_rate;
            $image_detail['duration'] = $duration;
            $image_detail['genre'] = $genre;
            $image_detail['size'] = $size;
            $image_detail['tag'] = $tag;
            $image_detail['title'] = $title;
            $image_detail['update_time'] = $create_time;
            $image_detail['user_uploaded_audio_id'] = $uuid;

            $response = Response::json(['code' => 200, 'message' => 'Audio uploaded successfully.', 'cause' => '', 'data' => ['result' => $image_detail]]);
        } catch (Exception $e) {
            (new ImageController())->logs('uploadAudioByUser', $e);
            //      Log::error("uploadAudioByUser : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload audio by audio.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getMyUploadedAudio",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getMyUploadedAudio",
     *        summary="Get My Uploaded Audio",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Audios fetched successfully.","cause":"","data":{"total_record":2,"is_next_page":false,"result":{{"user_uploaded_audio_id":2,"user_id":3,"audio_file":"http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_audio/5d035f49bebd5_user_uploaded_audio_1560502089.mp3","audio_name":"5d035f49bebd5_user_uploaded_audio_1560502089.mp3","audio_format":"mp3","duration":"00:04:58","size":4.56,"bit_rate":"128109","genre":"","tag":"test","title":"34_sec","artist":"","update_time":"2019-06-14 08:48:10"}}}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} getMyUploadedAudio   getMyUploadedAudio
     *
     * @apiName getMyUploadedAudio
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1,
     * "item_count":50
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Audios fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "is_next_page": false,
     * "result": [
     * {
     * "user_uploaded_audio_id": 1,
     * "user_id": 2,
     * "audio_file": "http://192.168.0.116/photoadking_testing/image_bucket/user_uploaded_audio/5c74c1b19cd9d_user_uploaded_audio_1551155633.mp3",
     * "audio_format": "mp3",
     * "duration": "00:02:03",
     * "size": 0.94,
     * "bit_rate": "64024",
     * "genre": "Children's",
     * "tag": null,
     * "title": "test",
     * "update_time": "2019-02-26 04:33:54"
     * }
     * ]
     * }
     * }
     */
    public function getMyUploadedAudio(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            //Log::info('request_data', ['request_data' => $request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getMyUploadedAudio:$this->page:$this->item_count:$this->user_id")) {
                $result = Cache::rememberforever("getMyUploadedAudio:$this->page:$this->item_count:$this->user_id", function () {

                    $total_row_result = DB::select('SELECT COUNT(id) AS total FROM user_uploaded_audio WHERE user_id = ? AND is_active = ?', [$this->user_id, 1]);
                    $total_row = $total_row_result[0]->total;

                    $image_list = DB::select('SELECT
                                        uua.uuid as user_uploaded_audio_id,
                                        IF(uua.file_name != "",CONCAT("'.Config::get('constant.USER_UPLOAD_AUDIOS_DIRECTORY_OF_DIGITAL_OCEAN').'",file_name),"") AS audio_file,
                                        coalesce(uua.file_name,"") AS audio_name,
                                        uua.format_name AS audio_format,
                                        coalesce(uua.duration,"") AS duration,
                                        coalesce(uua.size,"") AS size,
                                        coalesce(uua.bit_rate,"") AS bit_rate,
                                        coalesce(uua.genre,"") AS genre,
                                        coalesce(uua.tag,"") AS tag,
                                        coalesce(uua.title,"") AS title,
                                        coalesce(uua.artist,"") AS artist,
                                        uua.update_time
                                      FROM
                                        user_uploaded_audio as uua
                                      WHERE
                                        uua.user_id = ? AND
                                        uua.is_active = ?
                                      ORDER BY uua.update_time DESC
                                      LIMIT ?,?', [$this->user_id, 1, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $image_list];

                });
            }

            $redis_result = Cache::get("getMyUploadedAudio:$this->page:$this->item_count:$this->user_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Audios fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getMyUploadedAudio', $e);
            //      Log::error("getMyUploadedAudio : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get my uploaded audio.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteMyUploadedAudioById",
     *        tags={"Users_video_module"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteMyUploadedAudioById",
     *        summary="Delete My Uploaded Audio By Id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     * 		@SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="file uploading",
     *         required=true,
     *         type="file"
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Audio uploaded successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="error",
     *        ),
     *    )
     */
    /**
     * @api {post} deleteMyUploadedAudioById   deleteMyUploadedAudioById
     *
     * @apiName deleteMyUploadedAudioById
     *
     * @apiGroup User
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "user_uploaded_audio_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Audio deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteMyUploadedAudioById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['user_uploaded_audio_id'], $request)) != '') {
                return $response;
            }

            $audio_id = $request->user_uploaded_audio_id;

            $result = DB::select('SELECT file_name FROM user_uploaded_audio WHERE uuid = ? AND user_id = ?', [$audio_id, $user_id]);
            if (count($result) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Audio does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $audio_file_name = $result[0]->file_name;

            DB::beginTransaction();
            DB::delete('DELETE FROM user_uploaded_audio WHERE uuid = ? AND user_id = ?', [$audio_id, $user_id]);
            DB::commit();

            //Delete user uploaded Audio from image_bucket
            (new ImageController())->deleteUserUploadedAudio($audio_file_name);
            $response = Response::json(['code' => 200, 'message' => 'Audio deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteMyUploadedAudioById', $e);
            //      Log::error("deleteMyUploadedAudioById : ", ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete audio.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //Search font by user
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchFontByUser",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchFontByUser",
     *        summary="search font by user",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"search_query"},
     *
     *          @SWG\Property(property="search_query",  type="text", example="test", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Fonts search successfully.","cause":"","data":"{}"}),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search font by user.","cause":"Exception message","data":"{}"}),
     *        ),
     *   )
     * )
     * )
     */
    /**
     * @api {post} searchFontByUser searchFontByUser
     *
     * @apiName searchFontByUser
     *
     * @apiGroup User
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
     * "search_query":"test" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Fonts search successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function searchFontByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_query'], $request)) != '') {
                return $response;
            }

            $this->search_query = trim(strtolower($request->search_query));
            $this->search_query = trim(preg_replace('/[^A-Za-z_ ]/', '', $this->search_query));
            if (strlen($this->search_query) >= 100 || strlen($this->search_query) == 0) {
                return Response::json(['code' => 201, 'message' => 'Please enter valid search text.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($this->search_query != '' && strlen($this->search_query) > 0) {
                if (! Cache::has("Config::get('constant.REDIS_KEY'):searchFontByUser:$this->search_query")) {
                    $result = Cache::rememberforever("searchFontByUser:$this->search_query", function () {
                        $offline_catalogs = Config::get('constant.OFFLINE_CATALOG_IDS_OF_FONT');
                        $fonts_list = DB::select('SELECT
                                              fm.uuid AS font_id,
                                              ctm.uuid as catalog_id,
                                              fm.font_name,
                                              fm.font_file,
                                              IF(fm.font_file != "",CONCAT("'.Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",font_file),"") AS font_url,
                                              IF(fm.font_json_file != "",CONCAT("'.Config::get('constant.FONT_JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",font_json_file),"") as font_json_file,
                                              IF(fm.preview_image != "",CONCAT("'.Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",preview_image),"") AS preview_image,
                                              fm.ios_font_name,
                                              fm.android_font_name,
                                              COALESCE(fm.issue_code, "") AS issue_code
                                            FROM
                                              font_master as fm,
                                              catalog_master as ctm
                                            WHERE
                                             fm.catalog_id=ctm.id AND
                                             fm.is_active = ? AND
                                             fm.catalog_id NOT IN('.$offline_catalogs.') AND
                                             (MATCH(fm.font_name) AGAINST("'.$this->search_query.'") OR
                                             MATCH(fm.font_name) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ") IN BOOLEAN MODE))', [1]);
                        $total_row = count($fonts_list);

                        return ['total_record' => $total_row, 'result' => $fonts_list];

                    });
                }
                $redis_result = Cache::get("searchFontByUser:$this->search_query");

                if (! $redis_result) {
                    $redis_result = [];
                }
                $response = Response::json(['code' => 200, 'message' => 'Fonts search successfully.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Sorry, we couldn\'t find any font for '.$request->search_query.'.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('searchFontByUser', $e);
            //      Log::error("searchFontByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' search font by user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function searchFontByUserV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_query'], $request)) != '') {
                return $response;
            }

            $this->search_query = trim(preg_replace('/[^A-Za-z_ ]/', '', strtolower($request->search_query)));
            if (strlen($this->search_query) >= 100 || strlen($this->search_query) == 0) {
                return Response::json(['code' => 201, 'message' => 'Please enter valid search text.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $redis_result = Cache::rememberforever("searchFontByUserV2:$this->search_query", function () {
                $offline_catalogs = config('constant.OFFLINE_CATALOG_IDS_OF_FONT');
                $fonts_list = DB::select('SELECT
                                      fm.uuid AS font_id,
                                      ctm.uuid AS catalog_id,
                                      fm.font_name,
                                      fm.font_file,
                                      COALESCE(fm.search_tags, "") AS search_tags,
                                      IF(fm.font_file != "",CONCAT("'.config('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",font_file),"") AS font_url,
                                      IF(fm.font_json_file != "",CONCAT("'.config('constant.FONT_JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",font_json_file),"") AS font_json_file,
                                      IF(fm.preview_image != "",CONCAT("'.config('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",preview_image),"") AS preview_image,
                                      fm.ios_font_name,
                                      fm.android_font_name,
                                      COALESCE(fm.issue_code, "") AS issue_code,
                                      MATCH(fm.search_tags) AGAINST("'.$this->search_query.'") +
                                      MATCH(fm.search_tags) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                  FROM
                                      font_master AS fm,
                                      catalog_master AS ctm
                                  WHERE
                                     fm.catalog_id = ctm.id AND
                                     fm.is_active = ? AND
                                     fm.catalog_id NOT IN('.$offline_catalogs.') AND
                                     (MATCH(fm.search_tags) AGAINST("'.$this->search_query.'") OR
                                     MATCH(fm.search_tags) AGAINST(REPLACE(CONCAT("'.$this->search_query.'"," ")," ","* ") IN BOOLEAN MODE))
                                  ORDER BY search_text DESC', [1]);
                $total_row = count($fonts_list);

                return ['total_record' => $total_row, 'result' => $fonts_list];
            });

            if (! $redis_result) {
                $redis_result = [];
            }
            $response = Response::json(['code' => 200, 'message' => 'Fonts search successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchFontByUserV2', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').' search font by user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*
    Purpose : for searching user uploaded font
    Description : This method compulsory take 1 argument as parameter.(no argument is optional )
    Return : return message 'Fonts search successfully.' if success otherwise error with specific status code
    */
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchUploadedFontByUser",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchUploadedFontByUser",
     *        summary="search font by user",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"search_query"},
     *
     *          @SWG\Property(property="search_query",  type="text", example="Raleway Heavy", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Fonts search successfully.","cause":"","data":{"code":200,"message":"Fonts search successfully.","cause":"","data":{"total_record":3,"result":{{"uploaded_font_id":"0c422ob407b6be","font_name":"Raleway ExtraLight","font_file":"61e80645473a8_user_uploaded_fonts_1642595909.ttf","font_url":"http:\/\/192.168.0.108\/photoadking\/image_bucket\/user_uploaded_fonts\/61e80645473a8_user_uploaded_fonts_1642595909.ttf","preview_img":"http:\/\/192.168.0.108\/photoadking\/image_bucket\/user_uploaded_original\/61e8064549ab8_font_preview_image_1642595909.png"},{"uploaded_font_id":"z7bbml5d32b461","font_name":"Raleway Bold","font_file":"61e8eb8bd28b2_user_uploaded_fonts_1642654603.ttf","font_url":"http:\/\/192.168.0.108\/photoadking\/image_bucket\/user_uploaded_fonts\/61e8eb8bd28b2_user_uploaded_fonts_1642654603.ttf","preview_img":"http:\/\/192.168.0.108\/photoadking\/image_bucket\/user_uploaded_original\/61e8eb8be2e6e_font_preview_image_1642654603.png"},{"uploaded_font_id":"fc69se62473c61","font_name":"Raleway Heavy","font_file":"61e8eeefb7d83_user_uploaded_fonts_1642655471.ttf","font_url":"http:\/\/192.168.0.108\/photoadking\/image_bucket\/user_uploaded_fonts\/61e8eeefb7d83_user_uploaded_fonts_1642655471.ttf","preview_img":"http:\/\/192.168.0.108\/photoadking\/image_bucket\/user_uploaded_original\/61e8eeefba494_font_preview_image_1642655471.png"}}}}}),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search font by user.","cause":"Exception message","data":"{}"}),
     *        ),
     *   )
     * )
     * )
     */
    public function searchUploadedFontByUser(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $this->user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_query'], $request)) != '') {
                return $response;
            }

            //convert search query into lowercase and remove character except alphabets
            $this->search_query = trim(strtolower($request->search_query));
            $this->search_query = trim(preg_replace('/[^A-Za-z_ ]/', '', $this->search_query));
            if (strlen($this->search_query) >= 100 || strlen($this->search_query) == 0) {
                return Response::json(['code' => 201, 'message' => 'Please enter valid search text.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //check if search query is empty and check if cache has the result for search query and if not then get data from database
            if ($this->search_query != '' && strlen($this->search_query) > 0) {
                if (! Cache::has("Config::get('constant.REDIS_KEY'):searchUploadedFontByUser:$this->search_query")) {
                    $result = Cache::rememberforever("searchUploadedFontByUser:$this->search_query", function () {
                        $offline_catalogs = Config::get('constant.OFFLINE_CATALOG_IDS_OF_FONT');
                        $fonts_list = DB::select('SELECT
                                          uuf.uuid AS uploaded_font_id,
                                              uuf.font_name,
                                              uuf.font_file,
                                              IF(uuf.font_file != "",CONCAT("'.Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY_OF_DIGITAL_OCEAN').'",uuf.font_file),"") AS font_url,
                                              IF(uuf.preview_img != "",CONCAT("'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",uuf.preview_img),"") AS preview_img
                                            FROM
                                              user_uploaded_fonts as uuf
                                            WHERE find_in_set("'.$this->user_id.'", uuf.user_ids) AND
                                             uuf.is_active = ? AND
                                             (MATCH(uuf.font_name) AGAINST("'.$this->search_query.'") OR
                                             MATCH(uuf.font_name) AGAINST(REPLACE(concat("'.$this->search_query.'"," ")," ","* ") IN BOOLEAN MODE))', [1]);
                        $total_row = count($fonts_list);

                        return ['total_record' => $total_row, 'result' => $fonts_list];

                    });
                }
                //get data for search query from cache
                $redis_result = Cache::get("searchUploadedFontByUser:$this->search_query");

                if (! $redis_result) {
                    $redis_result = [];
                }
                $response = Response::json(['code' => 200, 'message' => 'Fonts search successfully.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Sorry, we couldn\'t find any font for '.$request->search_query.'.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('searchUploadedFontByUser', $e);
            //      Log::error("searchUploadedFontByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' search uploaded font by user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    //*   UserController   *//

    /* =================================| User publish template module |=============================*/
    /**
     * @api {post} publishDesignByUser publishDesignByUser
     *
     * @apiName publishDesignByUser
     *
     * @apiGroup Admin
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
     * "sub_category_id":12,
     * "design_id":1,
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Design saved successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function publishDesignByUser(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['design_id', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $design_id = $request->design_id;
            $sub_category_id = $request->sub_category_id;
            $create_time = date('Y-m-d H:i:s');

            /* Store design detail in to unpublish table*/
            $design_detail = DB::select('SELECT
                                        sub_category_id,
                                        json_data,
                                        download_json,
                                        image,
                                        overlay_image,
                                        video_name,
                                        is_video_user_uploaded,
                                        user_template_name,
                                        color_value,
                                        content_type,
                                        user_template_name
                                     FROM my_design_master WHERE id = ?', [$design_id]);
            if (count($design_detail) > 0) {
                //        $sub_category_id = $design_detail[0]->sub_category_id;
                $json_data = $design_detail[0]->json_data;
                $download_json = $design_detail[0]->download_json;
                $sample_image = $design_detail[0]->image;
                $overlay_image = $design_detail[0]->overlay_image;
                $video_name = $design_detail[0]->video_name;
                $is_video_user_uploaded = $design_detail[0]->is_video_user_uploaded;
                $color_value = $design_detail[0]->color_value;
                $content_type = $design_detail[0]->content_type;
                $user_template_name = $design_detail[0]->user_template_name;

                //        if($is_video_user_uploaded == 0 ){
                DB::beginTransaction();

                $unpublish_design = DB::select('SELECT id FROM unpublish_design_master WHERE design_id = ?', [$design_id]);
                if (count($unpublish_design) > 0) {
                    DB::update('update unpublish_design_master
                         set user_id =?,
                           design_id =?,
                           sub_category_id =?,
                           is_publish =?,
                           json_data=?,
                           download_json=?,
                           image=?,
                           overlay_image=?,
                           video_name=?,
                           is_video_user_uploaded=?,
                           color_value=?,
                           content_type=?,
                           user_template_name=?', [$user_id, $design_id, $sub_category_id, 0, $json_data, $download_json, $sample_image, $overlay_image, $video_name, $is_video_user_uploaded, $color_value, $content_type, $user_template_name]);
                } else {
                    DB::insert('insert into unpublish_design_master
                        (user_id,design_id,sub_category_id,is_publish,json_data,download_json,image,overlay_image,video_name,is_video_user_uploaded,color_value,content_type,user_template_name,create_time)
                        values (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', [$user_id, $design_id, $sub_category_id, 0, $json_data, $download_json, $sample_image, $overlay_image, $video_name, $is_video_user_uploaded, $color_value, $content_type, $user_template_name, $create_time]);
                }
                DB::update('update my_design_master
                         set
                           is_publish =?
                           Where id=?', [0, $design_id]);
                DB::commit();
                //        } else{
                //          return Response::json(array('code' => 201, 'message' => 'Unable to publish design.', 'cause' => '', 'data' => json_decode('{}')));
                //        }
            } else {
                return Response::json(['code' => 201, 'message' => 'Invalid design id.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $response = Response::json(['code' => 200, 'message' => 'Design add to publish,We will inform.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('publishDesignByUser', $e);
            //      Log::error("publishDesignByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'publish design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* For get encrypt id for design template page */
    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getEncryptIdForDesignTemplate",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getEncryptIdForDesignTemplate",
     *        summary="get encrypt id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"template_array"},
     *
     *          @SWG\Property(property="template_array",  type="array", example="[{},{}]", description="",
     *
     *              @SWG\Items(type="integer",example=1
     *          ),
     *        ),
     *      ),
     *     ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Id fetch successfully.","cause":"","data":"{}"}),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search font by user.","cause":"Exception message","data":"{}"}),
     *        ),
     *   )
     * )
     * )
     */
    /**
     * @api {post} getEncryptIdForDesignTemplate getEncryptIdForDesignTemplate
     *
     * @apiName getEncryptIdForDesignTemplate
     *
     * @apiGroup User
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
     * "template_array":"[{},{}]" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Id fetch successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getEncryptIdForDesignTemplate(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $template_array = isset($request->template_array) ? $request->template_array : [];
            if (count($template_array) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Required field template_array is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $content_id = [];
            foreach ($template_array as $row) {
                $row->data = $row->sub_category_id.'/'.$row->catalog_id.'/'.$row->template_id;
                $sub_category = DB::select('SELECT uuid FROM sub_category_master WHERE id = ?', [$row->sub_category_id]);
                if (count($sub_category) > 0) {
                    $sub_category_id = $sub_category[0]->uuid;
                } else {
                    $sub_category_id = '';
                }
                $catalog_detail = DB::select('SELECT uuid FROM catalog_master WHERE id = ?', [$row->catalog_id]);
                if (count($catalog_detail) > 0) {
                    $catalog_uuid = $catalog_detail[0]->uuid;
                } else {
                    $catalog_uuid = '';
                }
                $row->catalog_id = $catalog_uuid;
                $row->sub_category_id = $sub_category_id;
                array_push($content_id, $row->template_id);
            }
            $content_id = implode(',', $content_id);

            $content_list = DB::select('SELECT
                                      DISTINCT cm.id as template_id,
                                      cm.uuid as content_id
                                  FROM
                                      content_master as cm
                                   WHERE
                                      cm.id IN('.$content_id.')
                                  ');
            foreach ($template_array as $row) {
                foreach ($content_list as $content_row) {
                    if ($row->template_id == $content_row->template_id) {
                        $row->template_id = $content_row->content_id;
                    }
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Id fetch successfully.', 'cause' => '', 'data' => $template_array]);

        } catch (Exception $e) {
            (new ImageController())->logs('getEncryptIdForDesignTemplate', $e);
            //      Log::error("getEncryptIdForDesignTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get encrypt id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getEncryptId",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getEncryptId",
     *        summary="get encrypt id",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="header",
     *        name="Authorization",
     *        description="access token",
     *        required=true,
     *        type="string",
     *      ),
     *		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example="12", description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example="1", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Id fetch successfully.","cause":"","data":"{}"}),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to search font by user.","cause":"Exception message","data":"{}"}),
     *        ),
     *   )
     * )
     * )
     */
    /**
     * @api {post} getEncryptId getEncryptId
     *
     * @apiName getEncryptId
     *
     * @apiGroup User
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
     * "sub_category_id":12,
     * "catalog_id":1,
     * "content_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Id fetch successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getEncryptId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $sub_category_id = isset($request->sub_category_id) && ($request->sub_category_id) ? $request->sub_category_id : 0;
            $catalog_id = isset($request->catalog_id) && ($request->catalog_id) ? $request->catalog_id : 0;
            $content_id = isset($request->content_id) && ($request->content_id) ? $request->content_id : 0;
            if ($sub_category_id) {
                $sub_category = DB::select('SELECT
                                        uuid
                                    FROM
                                        sub_category_master
                                    WHERE
                                         id = ?', [$sub_category_id]);
                $sub_category_id = $sub_category[0]->uuid;
            }
            if ($catalog_id) {
                $catalog = DB::select('SELECT
                                    uuid
                               FROM
                                    catalog_master
                               WHERE
                                    id = ?', [$catalog_id]);
                $catalog_id = $catalog[0]->uuid;
            }
            if ($content_id) {
                $content = DB::select('SELECT
                                    uuid
                               FROM
                                    content_master
                               WHERE
                                    id = ?', [$content_id]);
                $content_id = $content[0]->uuid;
            }
            $data = ['sub_category_id' => $sub_category_id, 'catalog_id' => $catalog_id, 'content_id' => $content_id];

            $response = Response::json(['code' => 200, 'message' => 'Id fetch successfully.', 'cause' => '', 'data' => $data]);

        } catch (Exception $e) {
            (new ImageController())->logs('getEncryptIdForDesignTemplate', $e);
            //      Log::error("getEncryptIdForDesignTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get encrypt id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function encryptStaticPageCTALink(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $pages = DB::select('SELECT
                                 id,
                                 header_detail
                              FROM
                                 static_page_master
                              WHERE
                                  is_active = 1
                              ORDER BY update_time ASC');

            foreach ($pages as $row) {

                $header_detail = json_decode($row->header_detail);
                $header_cta_link = $header_detail->cta_link;
                $cta_link_array = explode('/', $header_cta_link);
                if (count($cta_link_array) == 1) {
                    $integer_sub_category_id = $cta_link_array[0];
                    $integer_catalog = '';
                } else {
                    $integer_sub_category_id = $cta_link_array[0];
                    $integer_catalog = $cta_link_array[1];
                }
                if ($integer_sub_category_id) {
                    if ($integer_sub_category_id == 16 || $integer_sub_category_id == 10) {
                        $integer_sub_category_id = 12;
                    } elseif ($integer_sub_category_id == 1 || $integer_sub_category_id == 2 || $integer_sub_category_id == 65) {
                        $integer_sub_category_id = 3;
                    } elseif ($integer_sub_category_id == 55) {
                        $integer_sub_category_id = 13;
                    } elseif ($integer_sub_category_id == 50) {
                        $integer_sub_category_id = 6;
                    } elseif ($integer_sub_category_id == 35) {
                        $integer_sub_category_id = 34;
                    } elseif ($integer_sub_category_id == 46) {
                        $integer_sub_category_id = 44;
                    }
                }

                if (is_numeric($integer_catalog)) {
                    $catalog = DB::select('SELECT uuid FROM catalog_master WHERE id = ?', [$integer_catalog]);
                    if (count($catalog) <= 0) {
                        $catalog = DB::select('SELECT
                                        scc.catalog_id,
                                        cm.uuid
                                   FROM
                                        sub_category_catalog as scc
                                   LEFT JOIN
                                        catalog_master as cm
                                   ON
                                        scc.catalog_id = cm.id
                                   WHERE
                                        sub_category_id = ?
                                   ORDER BY scc.update_time DESC LIMIT 1', [$integer_sub_category_id]);
                    }
                    $integer_catalog = $catalog[0]->uuid;
                }
                if (is_numeric($integer_sub_category_id)) {
                    $sub_category = DB::select('SELECT uuid FROM sub_category_master WHERE  id = ?', [$integer_sub_category_id]);
                    $integer_sub_category_id = $sub_category[0]->uuid;
                }

                if (count($cta_link_array) == 1) {
                    $header_cta_link = "$integer_sub_category_id";
                } else {
                    $header_cta_link = "$integer_sub_category_id/$integer_catalog";
                }
                $header_detail->cta_link = $header_cta_link;
                DB::beginTransaction();
                DB::update('UPDATE
                     static_page_master
                  SET
                     header_detail =?
                  WHERE id=?', [json_encode($header_detail), $row->id]);
                DB::commit();
            }
            $response = Response::json(['code' => 200, 'message' => 'CTA link encrypted successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('encryptStaticPageCTALink', $e);
            DB::rollBack();
            //      Log::error("encryptStaticPageCTALink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' encrypt CTA link.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;

    }

    public function removeDesignerNameFromResource(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredArrayParameter(['limit'], $request)) != '') {
                return $response;
            }

            $limit = $request->limit;
            $image_counter = 0;
            $images = [];
            DB::statement("SET sql_mode = '' ");
            $content_details = DB::select('SELECT
                                          cm.id AS my_design_id,
                                          cm.json_pages_sequence,
                                          cm.json_data AS json_data,
                                          CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'", "/app/#/editor/", scm.uuid, "/", ctm.uuid, "/", cm.uuid, "/") AS url
                                      FROM
                                          content_master AS cm
                                          JOIN catalog_master AS ctm ON ctm.id = cm.catalog_id
                                          JOIN sub_category_catalog AS scc ON scc.catalog_id = cm.catalog_id
                                          JOIN sub_category_master AS scm ON scm.id = scc.sub_category_id
                                      WHERE
                                          cm.json_data LIKE "%prarthana.optimumbrew@gmail.com_%"
                                      GROUP BY
                                          cm.id
                                      ORDER BY
                                          cm.update_time DESC LIMIT ?', [$limit]);

            $all_url = array_column($content_details, 'url');
            $all_id = implode(',', array_column($content_details, 'my_design_id'));

            foreach ($content_details as $i => $content_detail) {

                $content_id = $content_detail->my_design_id;
                $old_json_data = json_decode($content_detail->json_data);
                //dd($old_json_data);

                if ($content_detail->json_pages_sequence) {

                    $json_pages_sequence = explode(',', $content_detail->json_pages_sequence);
                    //dd($json_pages_sequence);

                    foreach ($json_pages_sequence as $o => $json_page) {

                        $single_page_json_data = $old_json_data->{$json_page};
                        //dd($single_page_json_data);

                        $frame_image_sticker_jsons = isset($single_page_json_data->frame_image_sticker_json) ? $single_page_json_data->frame_image_sticker_json : [];
                        $frame_jsons = isset($single_page_json_data->frame_json) ? $single_page_json_data->frame_json : [];
                        $background_jsons = isset($single_page_json_data->background_json) ? $single_page_json_data->background_json : [];
                        $image_sticker_jsons = isset($single_page_json_data->image_sticker_json) ? $single_page_json_data->image_sticker_json : [];
                        $sticker_jsons = isset($single_page_json_data->sticker_json) ? $single_page_json_data->sticker_json : [];
                        $sample_image = isset($single_page_json_data->sample_image) ? $single_page_json_data->sample_image : [];
                        //dd($frame_image_sticker_jsons, $frame_jsons, $background_jsons, $image_sticker_jsons, $sticker_jsons, $sample_image);

                        foreach ($frame_image_sticker_jsons as $j => $frame_image_sticker_json) {
                            $images[] = $frame_image_sticker_json->image_sticker_image;
                        }

                        if ($frame_jsons && isset($frame_jsons->frame_image) && $frame_jsons->frame_image != '') {
                            $images[] = $frame_jsons->frame_image;
                        }

                        if ($background_jsons && isset($background_jsons->background_image) && $background_jsons->background_image != '') {
                            $images[] = $background_jsons->background_image;
                        }

                        foreach ($image_sticker_jsons as $k => $image_sticker_json) {
                            $images[] = $image_sticker_json->image_sticker_image;
                        }

                        foreach ($sticker_jsons as $l => $sticker_json) {
                            $images[] = $sticker_json->sticker_image;
                        }

                        if ($sample_image && $sample_image != '') {
                            $images[] = $sample_image;
                        }
                    }

                } else {
                    $frame_image_sticker_jsons = isset($old_json_data->frame_image_sticker_json) ? $old_json_data->frame_image_sticker_json : [];
                    $frame_jsons = isset($old_json_data->frame_json) ? $old_json_data->frame_json : [];
                    $background_jsons = isset($old_json_data->background_json) ? $old_json_data->background_json : [];
                    $image_sticker_jsons = isset($old_json_data->image_sticker_json) ? $old_json_data->image_sticker_json : [];
                    $sticker_jsons = isset($old_json_data->sticker_json) ? $old_json_data->sticker_json : [];
                    $sample_image = isset($old_json_data->sample_image) ? $old_json_data->sample_image : [];
                    //dd($frame_image_sticker_jsons, $frame_jsons, $background_jsons, $image_sticker_jsons, $sticker_jsons, $sample_image);

                    foreach ($frame_image_sticker_jsons as $j => $frame_image_sticker_json) {
                        $images[] = $frame_image_sticker_json->image_sticker_image;
                    }

                    if ($frame_jsons && isset($frame_jsons->frame_image) && $frame_jsons->frame_image != '') {
                        $images[] = $frame_jsons->frame_image;
                    }

                    if ($background_jsons && isset($background_jsons->background_image) && $background_jsons->background_image != '') {
                        $images[] = $background_jsons->background_image;
                    }

                    foreach ($image_sticker_jsons as $k => $image_sticker_json) {
                        $images[] = $image_sticker_json->image_sticker_image;
                    }

                    foreach ($sticker_jsons as $l => $sticker_json) {
                        $images[] = $sticker_json->sticker_image;
                    }

                    if ($sample_image && $sample_image != '') {
                        $images[] = $sample_image;
                    }
                }
            }

            $counter = substr_count(json_encode($content_details), 'prarthana.optimumbrew@gmail.com_');
            $disk = Storage::disk('s3');

            foreach ($images as $i => $image) {
                $fullname = Config::get('constant.RESOURCE_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$image;
                $new_name = Config::get('constant.AWS_BUCKET').'/resource/'.str_replace('prarthana.optimumbrew@gmail.com_', '', $image);
                //$disk->move($image, $new_name);
                $disk->put($new_name, file_get_contents($fullname), 'public');
                $image_counter++;
            }

            if ($all_id) {
                DB::beginTransaction();
                DB::update('UPDATE content_master
                  SET
                    json_data = REPLACE(json_data, "prarthana.optimumbrew@gmail.com_", ""),
                    update_time = update_time
                  WHERE id IN ('.$all_id.')');
                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Designer name removed successfully.', 'cause' => '', 'data' => ['content_details_count' => count($content_details), 'total_word_counter' => $counter, 'counter' => $image_counter, 'all_url' => $all_url]]);

        } catch (Exception $e) {
            Log::error('removeDesignerNameFromResource : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'remove designer name.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} addSubCategoryASTag addSubCategoryASTag
     *
     * @apiName addSubCategoryASTag
     *
     * @apiGroup Admin
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
     * "sub_category_id":12,
     * "page":1,
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addSubCategoryASTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $request->sub_category_id;
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;

            $total_row_result = DB::select('SELECT
                                         COUNT(cm.id) as total
                                      FROM
                                           content_master as cm,
                                           sub_category_catalog as sct,
                                           sub_category_master as scm
                                      WHERE
                                        scm.id = ? AND
                                        sct.sub_category_id = scm.id AND
                                        sct.catalog_id=cm.catalog_id ', [$sub_category_id]);

            $total_row = $total_row_result[0]->total;
            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $templates = DB::select('SELECT
                                      cm.id,
                                      scm.sub_category_name,
                                      cm.search_category,
                                      cm.update_time
                                  FROM
                                       content_master as cm,
                                       sub_category_catalog as sct,
                                       sub_category_master as scm
                                  WHERE
                                    scm.id = ? AND
                                    sct.sub_category_id = scm.id AND
                                    sct.catalog_id=cm.catalog_id
                                  ORDER BY cm.update_time ASC LIMIT ?,? ', [$sub_category_id, $offset, $item_count]);
            if (count($templates) > 0) {
                $tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $templates[0]->sub_category_name)));

                foreach ($templates as $row) {
                    if ($row->search_category != null || $row->search_category != '') {
                        $row->search_category .= ','.$tag;
                    } else {
                        $row->search_category = $tag;
                    }
                    $row->search_category = implode(',', array_unique(array_filter(explode(',', $row->search_category))));
                    DB::beginTransaction();
                    DB::update('UPDATE content_master
                        SET
                           search_category =?,
                           update_time =?
                        WHERE id=?', [$row->search_category, $row->update_time, $row->id]);
                    DB::commit();
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page]]);
        } catch (Exception $e) {
            (new ImageController())->logs('addSubCategoryASTag', $e);
            //      Log::error("addSubCategoryASTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function addCategoryASTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $page = $request->page;
            $item_count = $request->item_count;
            $offset = ($page - 1) * $item_count;

            $total_row_result = DB::select('SELECT
                                         COUNT(cm.id) as total
                                      FROM
                                           content_master as cm
                                      WHERE
                                        cm.catalog_id = ?', [$catalog_id]);

            $total_row = $total_row_result[0]->total;
            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $templates = DB::select('SELECT
                                      cm.id,
                                      cm.search_category,
                                      cm.update_time
                                  FROM
                                      content_master as cm
                                  WHERE
                                    cm.catalog_id= ?
                                  ORDER BY cm.update_time ASC LIMIT ?,? ', [$catalog_id, $offset, $item_count]);

            if (count($templates) > 0) {
                $catalog_detail = DB::select('SELECT name FROM catalog_master WHERE id = ?', [$catalog_id]);
                $tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $catalog_detail[0]->name)));

                foreach ($templates as $row) {
                    if ($row->search_category != null || $row->search_category != '') {
                        $row->search_category .= ','.$tag;
                    } else {
                        $row->search_category = $tag;
                    }
                    $row->search_category = implode(',', array_unique(array_filter(explode(',', $row->search_category))));
                    DB::beginTransaction();
                    DB::update('UPDATE content_master
                        SET
                           search_category =?,
                           update_time =?
                        WHERE id=?', [$row->search_category, $row->update_time, $row->id]);
                    DB::commit();
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page]]);
        } catch (Exception $e) {
            (new ImageController())->logs('addCategoryASTag', $e);
            //      Log::error("addCategoryASTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* Cancel stripe subscription which are not cancel by user (Due to some stripe problem we disable stripe for some time)*/
    public function cancelAllStripeSubscription(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['item_count'], $request)) != '') {
                return $response;
            }

            $item_count = $request->item_count;
            $offset = 0;

            $total_row_result = DB::select('SELECT COUNT(id) AS total FROM subscriptions WHERE payment_type = 2 AND is_active = 1 AND final_expiration_time IS NULL');

            $total_row = $total_row_result[0]->total;
            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $subscriptions = DB::select('SELECT id,user_id,expiration_time,transaction_id,update_time
                                   FROM subscriptions
                                   WHERE payment_type = 2 AND is_active = 1 AND final_expiration_time IS NULL
                                   ORDER BY update_time ASC LIMIT ?,?', [$offset, $item_count]);
            if (count($subscriptions) > 0) {
                foreach ($subscriptions as $row) {

                    DB::beginTransaction();

                    DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $row->expiration_time, $row->user_id]);

                    DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$row->user_id]);

                    DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$row->user_id]);

                    DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$row->user_id]);

                    $response_message = 'Subscription Cancelled By Admin';
                    DB::update('UPDATE subscriptions set final_expiration_time=? ,cancellation_date=?,is_active=?,response_message =? WHERE id = ? ', [$row->expiration_time, $row->expiration_time, 0, $response_message, $row->id]);

                    DB::update('UPDATE payment_status_master SET is_active= ? WHERE txn_id = ? ', [0, $row->transaction_id]);

                    DB::commit();
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Subscriptions cancel successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page]]);
        } catch (Exception $e) {
            (new ImageController())->logs('cancelAllStripeSubscription', $e);
            //      Log::error("cancelAllStripeSubscription : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'cancel stripe subscription.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function addIndexInImageJson(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredArrayParameter(['sub_category_ids'], $request)) != '') {
                return $response;
            }

            $sub_category_ids = $request->sub_category_ids;
            $all_query = null;
            $i = 0;

            DB::statement('SET SESSION group_concat_max_len = 1000000');
            $catalog_details = DB::select('SELECT GROUP_CONCAT(catalog_id) AS catalog_ids FROM sub_category_catalog WHERE sub_category_id IN ('.$sub_category_ids.') AND is_active = 1 ');
            $catalog_ids = $catalog_details[0]->catalog_ids;

            //dd($catalog_ids);

            $content_details = DB::select('SELECT * FROM content_master WHERE content_type = ? AND catalog_id IN ('.$catalog_ids.') ORDER BY update_time DESC', [Config::get('constant.CONTENT_TYPE_OF_CARD_JSON')]);

            //dd($content_details);

            DB::beginTransaction();

            foreach ($content_details as $i => $content_detail) {

                $old_json_data = json_decode($content_detail->json_data);

                //dd($old_json_data);

                if ($content_detail->json_pages_sequence) {

                    $json_pages_sequence = explode(',', $content_detail->json_pages_sequence);

                    //dd($json_pages_sequence);

                    foreach ($json_pages_sequence as $o => $json_page) {

                        $single_page_json_data = $old_json_data->{$json_page};

                        //dd($single_page_json_data);

                        $index = 0;
                        $total_object_count = 0;
                        $frame_image_sticker_jsons = isset($single_page_json_data->frame_image_sticker_json) ? $single_page_json_data->frame_image_sticker_json : [];
                        $frame_jsons = isset($single_page_json_data->frame_json) ? $single_page_json_data->frame_json : [];
                        $image_sticker_jsons = isset($single_page_json_data->image_sticker_json) ? $single_page_json_data->image_sticker_json : [];
                        $sticker_jsons = isset($single_page_json_data->sticker_json) ? $single_page_json_data->sticker_json : [];
                        $text_jsons = isset($single_page_json_data->text_json) ? $single_page_json_data->text_json : [];
                        $curved_text_jsons = isset($single_page_json_data->curved_text_json) ? $single_page_json_data->curved_text_json : [];
                        $tool_jsons = isset($single_page_json_data->tool_json) ? $single_page_json_data->tool_json : [];
                        $tool_jsons_index_array = array_column($tool_jsons, 'index');
                        $total_object_count += count($tool_jsons);

                        foreach ($frame_image_sticker_jsons as $j => $frame_image_sticker_json) {
                            frame_image_sticker_json:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto frame_image_sticker_json;
                            }
                            $frame_image_sticker_json->index = $index;
                            $index++;
                        }
                        $total_object_count += count($frame_image_sticker_jsons);

                        if ($frame_jsons) {
                            if (isset($frame_jsons->frame_color) && $frame_jsons->frame_color != '') {
                                frame_color:
                                if (in_array($index, $tool_jsons_index_array)) {
                                    $index++;
                                    goto frame_color;
                                }
                                $frame_jsons->index = $index;
                                $index++;
                                $total_object_count += 1;

                            } elseif (isset($frame_jsons->frame_image) && $frame_jsons->frame_image != '') {
                                frame_image:
                                if (in_array($index, $tool_jsons_index_array)) {
                                    $index++;
                                    goto frame_image;
                                }
                                $frame_jsons->index = $index;
                                $index++;
                                $total_object_count += 1;
                            }
                        }

                        foreach ($image_sticker_jsons as $k => $image_sticker_json) {
                            image_sticker_json:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto image_sticker_json;
                            }
                            $image_sticker_json->index = $index;
                            $index++;
                        }
                        $total_object_count += count($image_sticker_jsons);

                        foreach ($sticker_jsons as $l => $sticker_json) {
                            sticker_json:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto sticker_json;
                            }
                            $sticker_json->index = $index;
                            $index++;
                        }
                        $total_object_count += count($sticker_jsons);

                        foreach ($text_jsons as $m => $text_json) {
                            text_json:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto text_json;
                            }
                            $text_json->index = $index;
                            $index++;
                        }
                        $total_object_count += count($text_jsons);

                        foreach ($curved_text_jsons as $n => $curved_text_json) {
                            curved_text_json:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto curved_text_json;
                            }
                            $curved_text_json->index = $index;
                            $index++;
                        }
                        $total_object_count += count($curved_text_jsons);

                        $single_page_json_data->total_objects = $total_object_count;
                    }

                } else {

                    $index = 0;
                    $total_object_count = 0;
                    $frame_image_sticker_jsons = isset($old_json_data->frame_image_sticker_json) ? $old_json_data->frame_image_sticker_json : [];
                    $frame_jsons = isset($old_json_data->frame_json) ? $old_json_data->frame_json : [];
                    $image_sticker_jsons = isset($old_json_data->image_sticker_json) ? $old_json_data->image_sticker_json : [];
                    $sticker_jsons = isset($old_json_data->sticker_json) ? $old_json_data->sticker_json : [];
                    $text_jsons = isset($old_json_data->text_json) ? $old_json_data->text_json : [];
                    $curved_text_jsons = isset($old_json_data->curved_text_json) ? $old_json_data->curved_text_json : [];
                    $tool_jsons = isset($old_json_data->tool_json) ? $old_json_data->tool_json : [];
                    $tool_jsons_index_array = array_column($tool_jsons, 'index');
                    $total_object_count += count($tool_jsons);

                    foreach ($frame_image_sticker_jsons as $j => $frame_image_sticker_json) {
                        frame_image_sticker_json_condition:
                        if (in_array($index, $tool_jsons_index_array)) {
                            $index++;
                            goto frame_image_sticker_json_condition;
                        }
                        $frame_image_sticker_json->index = $index;
                        $index++;
                    }
                    $total_object_count += count($frame_image_sticker_jsons);

                    if ($frame_jsons) {
                        if (isset($frame_jsons->frame_color) && $frame_jsons->frame_color != '') {
                            frame_color_condition:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto frame_color_condition;
                            }
                            $frame_jsons->index = $index;
                            $index++;
                            $total_object_count += 1;

                        } elseif (isset($frame_jsons->frame_image) && $frame_jsons->frame_image != '') {
                            frame_image_condition:
                            if (in_array($index, $tool_jsons_index_array)) {
                                $index++;
                                goto frame_image_condition;
                            }
                            $frame_jsons->index = $index;
                            $index++;
                            $total_object_count += 1;
                        }
                    }

                    foreach ($image_sticker_jsons as $k => $image_sticker_json) {
                        image_sticker_json_condition:
                        if (in_array($index, $tool_jsons_index_array)) {
                            $index++;
                            goto image_sticker_json_condition;
                        }
                        $image_sticker_json->index = $index;
                        $index++;
                    }
                    $total_object_count += count($image_sticker_jsons);

                    foreach ($sticker_jsons as $l => $sticker_json) {
                        sticker_json_condition:
                        if (in_array($index, $tool_jsons_index_array)) {
                            $index++;
                            goto sticker_json_condition;
                        }
                        $sticker_json->index = $index;
                        $index++;
                    }
                    $total_object_count += count($sticker_jsons);

                    foreach ($text_jsons as $m => $text_json) {
                        text_json_condition:
                        if (in_array($index, $tool_jsons_index_array)) {
                            $index++;
                            goto text_json_condition;
                        }
                        $text_json->index = $index;
                        $index++;
                    }
                    $total_object_count += count($text_jsons);

                    foreach ($curved_text_jsons as $n => $curved_text_json) {
                        curved_text_json_condition:
                        if (in_array($index, $tool_jsons_index_array)) {
                            $index++;
                            goto curved_text_json_condition;
                        }
                        $curved_text_json->index = $index;
                        $index++;
                    }
                    $total_object_count += count($curved_text_jsons);

                    $old_json_data->total_objects = $total_object_count;
                }

                //dd($old_json_data);

                $all_query = DB::update('UPDATE content_master SET json_data = ?, update_time = update_time, attribute2 = 1 WHERE id = ?', [json_encode($old_json_data), $content_detail->id]);
                if (! $all_query) {
                    Log::error('addIndexInImageJson : query fails :', ['content_id' => $content_detail->id]);
                }

            }

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Index added successfully.', 'cause' => '', 'data' => $i]);

        } catch (Exception $e) {
            Log::error('addIndexInImageJson : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add index in json.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function addIndexInImageJsonWhileCardUploading($single_page_json_data)
    {
        try {

            $index = 0;
            $total_object_count = 0;
            $frame_image_sticker_jsons = isset($single_page_json_data->frame_image_sticker_json) ? $single_page_json_data->frame_image_sticker_json : [];
            $frame_jsons = isset($single_page_json_data->frame_json) ? $single_page_json_data->frame_json : [];
            $image_sticker_jsons = isset($single_page_json_data->image_sticker_json) ? $single_page_json_data->image_sticker_json : [];
            $sticker_jsons = isset($single_page_json_data->sticker_json) ? $single_page_json_data->sticker_json : [];
            $text_jsons = isset($single_page_json_data->text_json) ? $single_page_json_data->text_json : [];
            $curved_text_jsons = isset($single_page_json_data->curved_text_json) ? $single_page_json_data->curved_text_json : [];
            $tool_jsons = isset($single_page_json_data->tool_json) ? $single_page_json_data->tool_json : [];
            $tool_jsons_index_array = array_column($tool_jsons, 'index');
            $total_object_count += count($tool_jsons);

            foreach ($frame_image_sticker_jsons as $j => $frame_image_sticker_json) {
                frame_image_sticker_json:
                if (in_array($index, $tool_jsons_index_array)) {
                    $index++;
                    goto frame_image_sticker_json;
                }
                $frame_image_sticker_json->index = $index;
                $index++;
            }
            $total_object_count += count($frame_image_sticker_jsons);

            if ($frame_jsons) {
                if (isset($frame_jsons->frame_color) && $frame_jsons->frame_color != '') {
                    frame_color:
                    if (in_array($index, $tool_jsons_index_array)) {
                        $index++;
                        goto frame_color;
                    }
                    $frame_jsons->index = $index;
                    $index++;
                    $total_object_count += 1;

                } elseif (isset($frame_jsons->frame_image) && $frame_jsons->frame_image != '') {
                    frame_image:
                    if (in_array($index, $tool_jsons_index_array)) {
                        $index++;
                        goto frame_image;
                    }
                    $frame_jsons->index = $index;
                    $index++;
                    $total_object_count += 1;
                }
            }

            foreach ($image_sticker_jsons as $k => $image_sticker_json) {
                image_sticker_json:
                if (in_array($index, $tool_jsons_index_array)) {
                    $index++;
                    goto image_sticker_json;
                }
                $image_sticker_json->index = $index;
                $index++;
            }
            $total_object_count += count($image_sticker_jsons);

            foreach ($sticker_jsons as $l => $sticker_json) {
                sticker_json:
                if (in_array($index, $tool_jsons_index_array)) {
                    $index++;
                    goto sticker_json;
                }
                $sticker_json->index = $index;
                $index++;
            }
            $total_object_count += count($sticker_jsons);

            foreach ($text_jsons as $m => $text_json) {
                text_json:
                if (in_array($index, $tool_jsons_index_array)) {
                    $index++;
                    goto text_json;
                }
                $text_json->index = $index;
                $index++;
            }
            $total_object_count += count($text_jsons);

            foreach ($curved_text_jsons as $n => $curved_text_json) {
                curved_text_json:
                if (in_array($index, $tool_jsons_index_array)) {
                    $index++;
                    goto curved_text_json;
                }
                $curved_text_json->index = $index;
                $index++;
            }
            $total_object_count += count($curved_text_jsons);

            $single_page_json_data->total_objects = $total_object_count;

            $response = Response::json(['code' => 200, 'message' => 'Index added successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('addIndexInImageJsonWhileCardUploading : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add index in json.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function addAnimatedStickerZip(Request $request_body)
    {
        try {

            if (! $request_body->hasFile('zip_file')) {
                return Response::json(['code' => 201, 'message' => 'Required field zip_file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $request = json_decode($request_body->input('request_data'));
            $zip_file = Input::file('zip_file');
            $frame = isset($request->frame) ? $request->frame : 25;
            $zip_file_name = $zip_file->getClientOriginalName();
            $zip_file_info = pathinfo($zip_file->getClientOriginalName());
            $folder_name = $zip_file_info['filename'];
            $extracted_dir = '../..'.Config::get('constant.TEMP_DIRECTORY').$folder_name;

            $IMAGE_MAXIMUM_FILESIZE = 100 * 1024; //100kb
            $zip = new \ZipArchive;
            $error_msg = $width = $height = '';
            if ($zip->open($zip_file) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $filesize = $zip->statIndex($i)['size'];
                    if ($filesize > 0) {
                        $fileinfo = pathinfo($filename);
                        $extension = $fileinfo['extension'];
                        //                        if ($width == "" || $height == "") {
                        //                            list($width, $height) = getimagesize("zip://" . $zip_file . "#" . $filename);
                        //                        }
                        if ($extension == 'png' && $error_msg == '') {
                            if ($filesize >= $IMAGE_MAXIMUM_FILESIZE) {
                                $error_msg = 'Sticker image file size is greater than 100 KB.';
                            }
                        }
                    }
                }
                $zip->extractTo('../..'.Config::get('constant.TEMP_DIRECTORY'));
                $zip->close();
            }

            if ($error_msg != '') {
                return Response::json(['code' => 201, 'message' => $error_msg, 'cause' => '', 'data' => json_decode('{}')]);
            }
            [$width, $height] = getimagesize($extracted_dir.'/1.png');
            log::info('h/w', [$height, $width]);

            $output_height = 256;
            $output_width = 256;

            $ffmpeg = Config::get('constant.FFMPEG_PATH');

            $webp_file_name = uniqid().'_webp_'.time().'.webp';
            $output_file = '../..'.Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY').$webp_file_name;
            $input_images = $extracted_dir.'/%d.png';
            $cmd = $ffmpeg.' -framerate '.$frame.' -i '.$input_images." -filter_complex select=\"not(mod(n-1\,2))\"  -s ".$output_width.'x'.$output_height.'  -loop 0 -y -preset default '.$output_file.' 2>&1';
            exec($cmd, $output, $result);
            log::info('1 :', [$cmd]);

            $output_file = '../..'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY').'1Animated.webp';
            $cmd = $ffmpeg.' -y -framerate '.$frame.' -i '.$input_images.' -s '.$output_width.'x'.$output_height.' -loop 0 -y -preset default '.$output_file.' 2>&1';
            exec($cmd, $output, $result);

            if (! file_exists($output_file) && $result != 0) {
                Log::info('cmd : ', [$cmd]);
                Log::error('addAnimatedStickerZip output : ', [$output]);
                Log::error('addAnimatedStickerZip result : ', [$result]);

                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' generate original animated webp.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $output_height = 60;
            $output_width = 60;

            $output_file = '../..'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY').$webp_file_name;
            $cmd = $ffmpeg.' -framerate '.$frame.' -i '.$input_images." -filter_complex select=\"not(mod(n-1\,2))\" -s ".$output_width.'x'.$output_height.' -loop 0 -y -preset default '.$output_file.' 2>&1';
            exec($cmd, $output, $result);
            log::info('2 :', [$cmd]);
            Log::info('addAnimatedStickerZip output : ', [$output]);

            if (! file_exists($output_file) && $result != 0) {
                Log::info('cmd : ', [$cmd]);
                Log::error('addAnimatedStickerZip output : ', [$output]);
                Log::error('addAnimatedStickerZip result : ', [$result]);

                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' generate compressed animated webp.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $response = Response::json(['code' => 200, 'message' => 'Animated sticker added successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('addAnimatedStickerZip : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'animated sticker.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function analysisMail(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['email', 'template', 'subject'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['message_body'], $request)) != '') {
                return $response;
            }

            $email_id = $request->email;
            $template = $request->template;
            $subject = $request->subject;
            $app_name = Config::get('constant.APP_HOST_NAME');
            $start_date = date('Y-m-d', strtotime('last week monday'));
            $end_date = date('Y-m-d', strtotime('last week sunday'));
            $message_body = json_decode(json_encode($request->message_body[0]), true);

            $data = ['data' => ['app_name' => $app_name, 'start_date' => $start_date, 'end_date' => $end_date, 'tags' => []], 'template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body];

            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                $message->to(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });

            $response = Response::json(['code' => 200, 'message' => 'Mail sent successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            Log::error('analysisMail : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'sent mail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function uploadFile(Request $request_body)
    {
        try {
            //$token = JWTAuth::getToken();
            //JWTAuth::toUser($token);

            if (! $request_body->hasFile('file')) {
                Log::error('uploadFile : Required field file is missing or empty. ');

                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $image_array = Input::file('file');

            //generate new file_name upload this file in local & then move file to s3
            $card_image = (new ImageController())->generateNewFileName('temp_design', $image_array);

            $original_path = '../..'.Config::get('constant.TEMP_DIRECTORY');
            $image_array->move($original_path, $card_image);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                $original_sourceFile = '../..'.Config::get('constant.TEMP_DIRECTORY').$card_image;

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');

                if (($is_exist = (new ImageController())->checkFileExist($original_sourceFile)) != 0) {
                    $original_targetFile = "$aws_bucket/temp/".$card_image;
                    //$disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                    $disk->put($original_targetFile, fopen($original_sourceFile, 'r+'), 'public');
                }

                (new ImageController())->unlinkFileFromLocalStorage($card_image, Config::get('constant.TEMP_DIRECTORY'));
            }
            $file_path = Config::get('constant.TEMP_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING').$card_image;

            $response = Response::json(['code' => 200, 'message' => 'Image upload successfully.', 'cause' => '', 'data' => $file_path]);
        } catch (Exception $e) {
            Log::error('uploadFile : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'sent mail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function uploadFileInChunkMode(Request $request_body)
    {
        try {
            //$token = JWTAuth::getToken();
            //JWTAuth::toUser($token);

            if (($response = (new VerificationController())->validateRequiredParameter(['unique_id', 'chunks', 'chunk'], $request_body)) != '') {
                return $response;
            }

            $response = $this->uploadChunkFile($request_body);
            if (is_object($response['data'])) {
                $image_array = $response['data'];
                $card_image = (new ImageController())->generateNewFileName('temp_design', $image_array);
                $original_path = '../..'.Config::get('constant.TEMP_DIRECTORY');
                $image_array->move($original_path, $card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    $original_sourceFile = '../..'.Config::get('constant.TEMP_DIRECTORY').$card_image;

                    $aws_bucket = Config::get('constant.AWS_BUCKET');
                    $disk = Storage::disk('s3');

                    if (($is_exist = (new ImageController())->checkFileExist($original_sourceFile)) != 0) {
                        $original_targetFile = "$aws_bucket/temp/".$card_image;
                        //$disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');
                        $disk->put($original_targetFile, fopen($original_sourceFile, 'r+'), 'public');
                    }

                    (new ImageController())->unlinkFileFromLocalStorage($card_image, Config::get('constant.TEMP_DIRECTORY'));
                }
                $file_path = Config::get('constant.TEMP_DIRECTORY_OF_DIGITAL_OCEAN_PHOTOADKING').$card_image;

                $response = Response::json(['code' => 200, 'message' => 'Image upload successfully.', 'cause' => '', 'data' => $file_path]);
            }

        } catch (Exception $e) {
            Log::error('uploadFileInChunkMode : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'sent mail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function setUserSessionOnSignup(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            $signup_session_details = isset($request->signup_session_details) ? json_encode($request->signup_session_details) : null;
            $dashboard_session_details = isset($request->dashboard_session_details) ? json_encode($request->dashboard_session_details) : null;
            $editor_session_details = isset($request->editor_session_details) ? json_encode($request->editor_session_details) : null;

            if (isset($request->signup_session_details)) {
                /* Save user entry point data in user_detail table in database */
                $this->setUserKeywordOnSignup($request->signup_session_details, $user_id);
            }

            if (! config('constant.IS_USER_SESSION_ANALYTICS_ENABLE')) {
                return Response::json(['code' => 200, 'message' => 'Information added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $is_exist = DB::select('SELECT 1 FROM user_session_signup_details WHERE user_id = ?', [$user_id]);

            if ($is_exist) {
                DB::update('UPDATE
            user_session_signup_details
          SET
            signup_session_details = IF(? != "", ?, signup_session_details),
            dashboard_session_details = IF(? != "", ?, dashboard_session_details),
            editor_session_details = IF(? !="", ?, editor_session_details)
          WHERE user_id = ?',
                    [$signup_session_details, $signup_session_details, $dashboard_session_details, $dashboard_session_details, $editor_session_details, $editor_session_details, $user_id]);
            } else {
                $data = [
                    'user_id' => $user_id,
                    'signup_session_details' => $signup_session_details,
                    'dashboard_session_details' => $dashboard_session_details,
                    'editor_session_details' => $editor_session_details,
                ];
                DB::table('user_session_signup_details')->insert($data);
            }

            $response = Response::json(['code' => 200, 'message' => 'Information added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            Log::error('setUserSessionOnSignup : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'sent mail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function setUserKeywordOnSignup($signup_session_details, $user_id)
    {
        try {
            $user_keyword = $this->validateUserKeyword($signup_session_details->signup_entry_point);

            $usr_ke = explode(',', $user_keyword);
            $utm_para = (in_array('utm_campaign', $usr_ke, true)) ? 1 : 0;

            $filteredArray = $user_keyword;

            if ($utm_para) {
                $all_key = explode(',', $user_keyword);
                $stringToRemove = 'utm_campaign';
                $filteredArray = array_filter($all_key, function ($element) use ($stringToRemove) {
                    return $element !== $stringToRemove;
                });
                $filteredArray = array_values($filteredArray);

                $filteredArray = implode(',', $filteredArray);
            }

            if ($filteredArray) {
                DB::update('UPDATE
                      user_detail
                    SET
                      user_keyword = IF(ISNULL(user_keyword),?,user_keyword),
                      update_time = update_time,
                      attribute1 = ?
                    WHERE
                      user_id = ?',
                    [$filteredArray, $utm_para, $user_id]);
            }
        } catch (Exception $e) {
            Log::error('setUserKeywordOnSignup : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    public function validateUserKeyword($user_keyword)
    {
        try {
            /* Replace '-' with ',' in user keywords */
            $user_keyword = str_replace('-', ',', $user_keyword);

            /* Remove first word for design pages if it's match with specified words */
            $first_word = strtok($user_keyword, ',');
            $specified_words = ['design', 'search', 'tools', 'calendar', 'create', 'features'];
            if (in_array($first_word, $specified_words)) {
                $user_keyword = str_replace($first_word.',', '', $user_keyword);
            }
            $user_keyword_array = explode(',', $user_keyword);

            /* Remove duplicate words if words count is greater than 5 */
            if (count($user_keyword_array) > 6) {
                $user_keyword_array = array_unique($user_keyword_array);
            }

            /* Remove preposition from the user keywords */
            $remove_word = ['and', 'of', 'to', 'the', 'on', 'for', 'templates', 'unknown', 'not specified'];
            $user_keyword_array = array_udiff($user_keyword_array, $remove_word, 'strcasecmp');

            /* Remove words which have length of 2 characters and less than 2 characters from the user keywords except specified words */
            $this->except_words = ['uk', 'ad', '3d', 'dj', 'qr', 'hr', 'al', 'ul', 'up'];
            $user_keyword_array = array_filter($user_keyword_array, function ($val) {
                if (! in_array($val, $this->except_words)) {
                    return strlen($val) > 2;
                } else {
                    return true;
                }
            });

            return implode(',', $user_keyword_array);

        } catch (Exception $e) {
            Log::error('validateUserKeyword : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    /* =================================| Debug API |=============================*/
    public function monitorTransferStartApi()
    {
        try {
            $response = Response::json(['code' => 200, 'message' => 'Test Successful.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('monitorTransferStartApi : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => config('constants.EXCEPTION_ERROR').'monitor transfer start api.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| For You page API |============================= */

    /**
     * @api {post} getRecentMyDesign getRecentMyDesign
     *
     * @apiName getRecentMyDesign
     *
     * @apiGroup User
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
     * "page": 1, //compulsory
     * "item_count": 10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data": {
     * "image_result": [
     * {
     * "my_design_id": "gsxvc4b18a2de6",
     * "sub_category_id": "7e6xbmd929914d",
     * "download_json": "",
     * "user_template_name": "Coral Red and White testing Flyer",
     * "sample_image": "http://192.168.0.108/photoadking/image_bucket/my_design/6433fb247dd85_my_design_1681128228.jpg",
     * "overlay_image": "",
     * "is_video_user_uploaded": "",
     * "total_pages": 1,
     * "content_type": 1,
     * "update_time": "2023-04-10 12:03:48"
     * }
     * ]
     * }
     * }
     */
    public function getRecentMyDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $this->user_id = $user_detail->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::rememberforever("getRecentMyDesign:$this->user_id:$this->page:$this->item_count", function () {

                $image_list = DB::select('SELECT
                                      mdm.uuid AS my_design_id,
                                      scm.uuid AS sub_category_id,
                                      COALESCE(mdm.download_json,"") AS download_json,
                                      IF(mdm.user_template_name != "",user_template_name,"Untitled Design") AS user_template_name,
                                      IF(mdm.image != "",CONCAT("'.config('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") AS sample_image,
                                      IF(mdm.overlay_image != "",CONCAT("'.config('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.overlay_image),"") AS overlay_image,
                                      COALESCE(mdm.is_video_user_uploaded,"") AS is_video_user_uploaded,
                                      COALESCE(LENGTH(json_pages_sequence) - LENGTH(REPLACE(json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                      mdm.content_type,
                                      mdm.update_time
                                    FROM
                                      my_design_master AS mdm,
                                      sub_category_master AS scm
                                    WHERE
                                      mdm.sub_category_id = scm.id AND
                                      mdm.user_id = ? AND
                                      mdm.is_active = ?
                                    ORDER BY mdm.update_time DESC LIMIT ?, ?', [$this->user_id, 1, $this->offset, $this->item_count]);

                return ['image_result' => isset($image_list) ? $image_list : []];
            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Designs fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', config('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getRecentMyDesign', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get recent designs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getUpcomingEvents getUpcomingEvents
     *
     * @apiName getUpcomingEvents
     *
     * @apiGroup User
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
     * "event_date": 2023-04-10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Event fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "post_schedule_id": "2tjapu70d09e2b",
     * "post_date": "2023-04-10",
     * "events": [
     * {
     * "event_id": "3fxd9p5c3c1e4d",
     * "content_type": 9,
     * "height": 960,
     * "width": 540,
     * "thumbnail_img":  * "http://192.168.0.108/photoadking/image_bucket/thumbnail/5e01b51dabe41_sample_image_1577170205.jpg",
     * "compressed_img": "http://192.168.0.108/photoadking/image_bucket/compressed/5e01b51dabe41_sample_image_1577170205.jpg",
     * "original_img": "http://192.168.0.108/photoadking/image_bucket/original/5e01b51dabe41_sample_image_1577170205.jpg",
     * "webp_original_img": "http://192.168.0.108/photoadking/image_bucket/webp_original/5e01b51dabe41_sample_image_1577170205.webp",
     * "webp_thumbnail_img": "http://192.168.0.108/photoadking/image_bucket/webp_thumbnail/5e01b51dabe41_sample_image_1577170205.webp",
     * "content_file": "http://192.168.0.108/photoadking/image_bucket/video/5e05da132fe4c_preview_video_1577441811.mp4",
     * "template_url": "http://192.168.0.108/photoadking/app/#/video-editor/hkupvrd929914d/0yapuvc8da391f/nm1pyee63831c9",
     * "event_name": "friend-ship-day",
     * "title": "Happy Friendship Day",
     * "tag": "friend",
     * "related_tag": null,
     * "short_description": "short happy",
     * "long_description": "<p>long happy</p>"
     * }
     * ]
     * }
     * ]
     *
     * }
     * }
     */
    public function getUpcomingEvents(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            /* Required request data parameter */
            if (($response = (new VerificationController())->validateRequiredParameter(['event_date'], $request)) != '') {
                return $response;
            }

            $this->event_date = $request->event_date;

            /* Check is date is valid or proper format, If not then return with proper error message */
            $date_arr = explode('-', $request->event_date);
            if (count($date_arr) != 3) {
                return Response::json(['code' => 201, 'message' => 'Invalid date format. ', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $year = $date_arr[0];
            $month = $date_arr[1];
            $day = $date_arr[2];

            /* Check request date is "YYYY-MM-DD" format & all date numbers are numeric */
            if ((strlen($year) != 4) || (strlen($month) != 2) || (strlen($day) != 2) || (! is_numeric($year)) || (! is_numeric($month)) || (! is_numeric($day))) {
                return Response::json(['code' => 201, 'message' => 'Please enter the date in the format "YYYY-MM-DD". ', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /* Check date is valid or not with checkdate() php function */
            if (! checkdate($month, $day, $year)) {
                return Response::json(['code' => 201, 'message' => 'Invalid date.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /* Check if date verify our proper requirement (if date between today to +7week) if not then return with proper error message */
            $this->next_date_to_allow = date('Y-m-d', strtotime('+7 week'));
            if (! ($this->event_date < $this->next_date_to_allow)) {
                return Response::json(['code' => 201, 'message' => "Sorry, we couldn't find any events.", 'cause' => '', 'data' => json_decode('{}')]);
            }

            $redis_result = Cache::rememberforever("getUpcomingEvents:$this->event_date", function () {

                //check only date is arrived in request that means get data for home page that is https://photoadking.com/social-media-content-calendar/
                //else if both name & date are arrived in request that means get data for detail page that is https://photoadking.com/social-media-content-calendar/templates/?date=2021-06-01&event=world-milk-day
                if (($this->event_date)) {

                    //Just get a schedule for 7 days
                    $post_schedules = DB::select('SELECT
                                          uuid AS post_schedule_id,
                                          sch.post_date,
                                          sch.post_ids
                                        FROM post_schedule_master AS sch
                                        WHERE sch.post_date BETWEEN "'.$this->event_date.'" AND "'.$this->next_date_to_allow.'"
                                        ORDER BY sch.post_date ASC');

                    //get all event with this particular schedule. Means the loop runs 7 times
                    foreach ($post_schedules as $i => $post_schedule) {
                        $events = DB::select('SELECT
                                    sug.uuid AS event_id,
                                    cnt.content_type AS content_type,
                                    COALESCE(cnt.height,0) AS height,
                                    COALESCE(cnt.width,0) AS width,
                                    IF(cnt.image != "",CONCAT("'.config('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cnt.image),"") AS thumbnail_img,
                                    IF(cnt.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cnt.image),"") AS compressed_img,
                                    IF(cnt.image != "",CONCAT("'.config('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cnt.image),"") AS original_img,
                                    IF(cnt.webp_image != "",CONCAT("'.config('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cnt.webp_image),"") as webp_original_img,
                                    IF(cnt.webp_image != "",CONCAT("'.config('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cnt.webp_image),"") as webp_thumbnail_img,
                                    IF(cnt.content_file != "",CONCAT("'.config('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cnt.content_file),"") AS content_file,
                                    IF(sug.preview_content_id != "",CONCAT("'.config('constant.ACTIVATION_LINK_PATH').'","/app/#/",IF(cnt.content_type = 4,"editor",IF(cnt.content_type = 9,"video-editor","intro-editor")),"/",scm.uuid,"/",cat.uuid,"/",cnt.uuid),NULL) AS template_url,
                                    sug.event_name AS event_name,
                                    sug.title AS title,
                                    sug.tag AS tag,
                                    sug.related_tag AS related_tag,
                                    sug.short_description AS short_description,
                                    sug.long_description AS long_description
                                  FROM
                                    post_suggestion_master AS sug
                                    LEFT JOIN content_master AS cnt ON cnt.id = sug.preview_content_id
                                    LEFT JOIN catalog_master AS cat ON cnt.catalog_id = cat.id
                                    LEFT JOIN sub_category_catalog AS scc ON scc.catalog_id = cat.id
                                    LEFT JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id
                                  WHERE
                                    sug.id IN ('.$post_schedule->post_ids.')
                                  ORDER BY FIELD(sug.id, '.$post_schedule->post_ids.' )');

                        //$post_schedules[$i]->events = $events;
                        foreach ($events as $j => $event) {
                            $data[] = array_merge(get_object_vars($post_schedules[$i]), get_object_vars($events[$j]));
                            unset($data[$j]['post_ids']);
                        }
                    }
                    //return array('result' => $post_schedules);
                    return ['result' => isset($data) ? $data : []];
                }
            });

            if (! $redis_result) {
                $code = 201;
                $message = "Sorry, we couldn't find any events.";
                $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $code = 200;
                $message = 'Event fetched successfully.';
                $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getUpcomingEvents', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get post schedule.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getContentBySearchTag getContentBySearchTag
     *
     * @apiName getContentBySearchTag
     *
     * @apiGroup User
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content fetched successfully.",
     * "cause": "",
     * "data": {
     * "user_keyword": "instagram,post,personal,ideas",
     * "search_result": [
     * {
     * "content_id": "w9tlv3477e1a67",
     * "sample_image": "https://d3jmn01ri1fzgl.cloudfront.net/photoadking/compressed/605c591ba7864_json_image_1616664859.jpg",
     * "preview_file": "",
     * "is_featured": "0",
     * "catalog_id": "yrn68ec7d461c9",
     * "catalog_name": "Interior & Home Decor",
     * "content_type": 4,
     * "sub_category_id": "dzx3220cbeffda",
     * "is_free": 0,
     * "is_portrait": 2,
     * "search_category": "instagram,post,interior,home,decor,house,green,design,decorating,tips,tricks,vectors,business,designing,analytics,square,ideas,product,ad",
     * "height": 540,
     * "width": 540,
     * "color_value": "#36954",
     * "multiple_images": "",
     * "pages_sequence": "",
     * "total_pages": 1,
     * "update_time": "2022-07-26 12:03:41",
     * "search_text": 25.672298431396484
     * }
     * ]
     * }
     * }
     */
    public function getContentBySearchTag()
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;
            $this->page = 1;
            $this->item_count = 10;
            $this->offset = ($this->page - 1) * $this->item_count;

            $user_details = DB::select('SELECT user_keyword FROM user_detail WHERE user_id = ?', [$user_id]);
            $this->user_keyword = $user_details[0]->user_keyword;

            $redis_result = Cache::rememberforever("getContentBySearchTag:$this->user_keyword:$this->page:$this->item_count", function () {

                if ($this->user_keyword) {
                    $search_result = DB::select('SELECT
                                          DISTINCT cm.uuid AS content_id,
                                          IF(cm.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                          IF(cm.content_file != "",CONCAT("'.config('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                          COALESCE(cm.is_featured,"") AS is_featured,
                                          ctm.uuid AS catalog_id,
                                          ctm.name AS catalog_name,
                                          cm.content_type,
                                          cm.template_name,
                                          scm.uuid AS sub_category_id,
                                          COALESCE(cm.is_free,0) AS is_free,
                                          COALESCE(cm.is_portrait,0) AS is_portrait,
                                          COALESCE(cm.search_category,"") AS search_category,
                                          COALESCE(cm.height,0) AS height,
                                          COALESCE(cm.width,0) AS width,
                                          COALESCE(cm.color_value,"") AS color_value,
                                          COALESCE(cm.multiple_images,"") AS multiple_images,
                                          COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                          COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                          cm.update_time,
                                          MATCH(cm.search_category) AGAINST("'.$this->user_keyword.'") +
                                          MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->user_keyword.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                        FROM
                                          content_master AS cm
                                          JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1 AND ctm.is_active = 1
                                          JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                        WHERE
                                          cm.is_active = 1 AND
                                          ISNULL(cm.original_img) AND
                                          ISNULL(cm.display_img) AND
                                          (MATCH(cm.search_category) AGAINST("'.$this->user_keyword.'") OR
                                            MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->user_keyword.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                          content_type IN(4,9,10)
                                        ORDER BY search_text DESC,cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);
                }

                return ['user_keyword' => isset($this->user_keyword) ? $this->user_keyword : '', 'search_result' => isset($search_result) ? $search_result : []];
            });

            if (! $redis_result) {
                $redis_result = [];
            }
            $response = Response::json(['code' => 200, 'message' => 'Content fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getContentBySearchTag', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getContentBySearchTagWithCategory getContentBySearchTagWithCategory
     *
     * @apiName getContentBySearchTagWithCategory
     *
     * @apiGroup User
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content fetched successfully.",
     * "cause": "",
     * "data": {
     * "search_result": [
     * {
     * "Facebook Canvas": [
     * {
     * "content_id": "0hxory0b6b738b",
     * "sample_image": "http://192.168.0.108/photoadking/image_bucket/compressed/60f7cc474e0d9_json_image_1626852423.jpg",
     * "preview_file": "",
     * "is_featured": "0",
     * "catalog_id": "bpfpyze690689a",
     * "sub_category_name": "Facebook Canvas",
     * "catalog_name": "Instagram Post 15",
     * "content_type": 4,
     * "sub_category_id": "patii4d929914d",
     * "is_free": 1,
     * "is_portrait": 1,
     * "search_category": "black,friday,sale,car,offere,white,maroon,instagram story,all,template,flyer,services,event,facebook,canvas,instagram,post",
     * "height": 960,
     * "width": 540,
     * "color_value": "#702522",
     * "multiple_images": "{\"328433\":{\"name\":\"60f7cc474e0d9_json_image_1626852423.jpg\",\"webp_name\":\"60f7cc474e0d9_json_image_1626852423.webp\",\"width\":540,\"height\":960}}",
     * "pages_sequence": "328433",
     * "total_pages": 1,
     * "update_time": "2022-02-18 06:15:16",
     * "search_text": 25.797700881958008
     * },
     * {
     * "content_id": "1jp9dse63831c9",
     * "sample_image": "http://192.168.0.108/photoadking/image_bucket/compressed/5c04f0aa142eb_template_image_1543827626.jpg",
     * "preview_file": "",
     * "is_featured": "1",
     * "catalog_id": "bpfpyze690689a",
     * "sub_category_name": "Facebook Canvas",
     * "catalog_name": "Instagram Post 15",
     * "content_type": 4,
     * "sub_category_id": "patii4d929914d",
     * "is_free": 1,
     * "is_portrait": 1,
     * "search_category": "illustration,business,desktop,text,symbol,graphic,facts,technology,designing,image,internet,abstract,vector,sign,design,signalise,card,World Wide Web,service,element,flyer,services,event,facebook,canvas,instagram,post,snapchat,geo,filter,all",
     * "height": 960,
     * "width": 540,
     * "color_value": "#5d26c9",
     * "multiple_images": "",
     * "pages_sequence": "",
     * "total_pages": 1,
     * "update_time": "2022-07-06 08:55:04",
     * "search_text": 17.198467254638672
     * }
     * ]
     * },
     * {
     * "Food & Drink Menu": [
     * {
     * "content_id": "5ptn65e63831c9",
     * "sample_image": "http://192.168.0.108/photoadking/image_bucket/compressed/5d6e3d5ca77d0_template_image_1567505756.jpg",
     * "preview_file": "",
     * "is_featured": "0",
     * "catalog_id": "84urssc8da391f",
     * "sub_category_name": "Food & Drink Menu",
     * "catalog_name": "Menu",
     * "content_type": 4,
     * "sub_category_id": "e7vzpad929914d",
     * "is_free": 1,
     * "is_portrait": 1,
     * "search_category": "navigation,dinner,no person,menu,lunch,set,stripe,template,cooking,layout,flat,presentation,health,food,kind,delicious,ingredients,site,menu (food),nutrition,grill",
     * "height": 492,
     * "width": 400,
     * "color_value": "#442f2e",
     * "multiple_images": "",
     * "pages_sequence": "",
     * "total_pages": 1,
     * "update_time": "2020-06-17 10:47:12",
     * "search_text": 8.864234924316406
     * },
     * {
     * "content_id": "3ilgzre63831c9",
     * "sample_image": "http://192.168.0.108/photoadking/image_bucket/compressed/5d70adc00d94b_template_image_1567665600.png",
     * "preview_file": "",
     * "is_featured": "0",
     * "catalog_id": "zpye73c8da391f",
     * "sub_category_name": "Food & Drink Menu",
     * "catalog_name": "Recipe Cards",
     * "content_type": 4,
     * "sub_category_id": "e7vzpad929914d",
     * "is_free": 1,
     * "is_portrait": 1,
     * "search_category": "chicken,tikka,food,food drink menu,recipes,illustration,dinner,business,isolated,cooking,lunch,template,menu,vectors,set,recipe card",
     * "height": 600,
     * "width": 400,
     * "color_value": "#7",
     * "multiple_images": "",
     * "pages_sequence": "",
     * "total_pages": 1,
     * "update_time": "2020-06-17 10:47:12",
     * "search_text": 8.864234924316406
     * }
     * ]
     * }
     * ]
     * }
     * }
     */
    public function getContentBySearchTagWithCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            /* Required request data parameter */
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->result = $array = [];

            $user_details = DB::select('SELECT user_keyword FROM user_detail WHERE user_id = ?', [$user_id]);
            $this->user_keyword = $user_details[0]->user_keyword;

            $redis_result = Cache::rememberforever("getContentBySearchTagWithCategory:$this->user_keyword:$this->page:$this->item_count", function () {

                if ($this->user_keyword) {
                    $sub_category_list = DB::select('SELECT
                                              scm.id AS sub_category_id,
                                              scm.uuid AS sub_category_uuid,
                                              scm.sub_category_name,
                                              COUNT(cm.id) AS search_count,
                                              scm.is_active
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1 AND ctm.is_active = 1
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                              scc.sub_category_id = scm.id AND
                                              cm.is_active = 1 AND
                                              ISNULL(cm.original_img) AND
                                              ISNULL(cm.display_img) AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->user_keyword.'") OR
                                                MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->user_keyword.'"," ")," ","* ")  IN BOOLEAN MODE))
                                              AND content_type IN(4,9,10)
                                            GROUP BY scm.id
                                            ORDER BY search_count DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    if ($sub_category_list) {
                        foreach ($sub_category_list as $sub_category) {

                            $sub_category_id = $sub_category->sub_category_id;
                            $search_result = DB::select('SELECT
                                              DISTINCT cm.uuid AS content_id,
                                              IF(cm.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.content_file != "",CONCAT("'.config('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                              COALESCE(cm.is_featured,"") AS is_featured,
                                              ctm.uuid AS catalog_id,
                                              scm.sub_category_name,
                                              ctm.name AS catalog_name,
                                              cm.content_type,
                                              cm.template_name,
                                              scm.uuid AS sub_category_id,
                                              COALESCE(cm.is_free,0) AS is_free,
                                              COALESCE(cm.is_portrait,0) AS is_portrait,
                                              COALESCE(cm.search_category,"") AS search_category,
                                              COALESCE(cm.height,0) AS height,
                                              COALESCE(cm.width,0) AS width,
                                              COALESCE(cm.color_value,"") AS color_value,
                                              COALESCE(cm.multiple_images,"") AS multiple_images,
                                              COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                              COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) AS total_pages,
                                              cm.update_time,
                                              MATCH(cm.search_category) AGAINST("'.$this->user_keyword.'") +
                                              MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->user_keyword.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1 AND ctm.is_active = 1
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                              scm.id = ? AND
                                              cm.is_active = 1 AND
                                              ISNULL(cm.original_img) AND
                                              ISNULL(cm.display_img) AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->user_keyword.'") OR
                                                MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->user_keyword.'"," ")," ","* ")  IN BOOLEAN MODE))
                                              AND content_type IN(4,9,10)
                                            ORDER BY search_text DESC, cm.update_time DESC LIMIT ?, ?', [$sub_category_id, 0, 10]);

                            $sub_category_name = $search_result[0]->sub_category_name;
                            array_push($this->result, [$sub_category_name => $search_result]);
                        }
                    }
                }

                return ['user_keyword' => isset($this->user_keyword) ? $this->user_keyword : '', 'search_result' => isset($this->result) ? $this->result : []];
            });

            if (! $redis_result) {
                $redis_result = [];
            }
            $response = Response::json(['code' => 200, 'message' => 'Content fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getContentBySearchTagWithCategory', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getUserKeyword getUserKeyword
     *
     * @apiName getUserKeyword
     *
     * @apiGroup User
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Keyword get successfully.",
     * "cause": "",
     * "data": {
     * "user_keyword": "facebook-canvas,facebook-canvas"
     * }
     * }
     */
    public function getUserKeyword()
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $user_details = DB::select('SELECT user_keyword FROM user_detail WHERE user_id = ?', [$user_id]);
            if ($user_details) {
                $this->user_keyword = $user_details[0]->user_keyword;
            }

            $result = ['user_keyword' => isset($this->user_keyword) ? $this->user_keyword : ''];
            $response = Response::json(['code' => 200, 'message' => 'Keyword get successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getUserKeyword', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} editUserKeyword editUserKeyword
     *
     * @apiName editUserKeyword
     *
     * @apiGroup User
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
     * "keywords": "food,flyer,video" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Keyword updated successfully.",
     * "cause": "",
     * "data": {
     * }
     * }
     */
    public function editUserKeyword(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            $request = json_decode($request_body->getContent());
            $user_keyword = isset($request->keywords) ? $request->keywords : null;

            DB::beginTransaction();
            DB::update('UPDATE
                    user_detail
                  SET
                    user_keyword = IF(? = "", NULL, ?),
                    update_time = update_time
                  WHERE
                    user_id = ?',
                [$user_keyword, $user_keyword, $user_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Keyword updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('getUserKeyword', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function insertMyDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['folder_id'], $request)) != '') {
                return $response;
            }

            $folder_id = $request->folder_id;
            $user_id = $request->user_id;
            $user_template_name = $request->user_template_name;
            $json_data = $request->json_data;
            $sample_image_name = $request->image;
            $overlay_image = $request->overlay_image;
            $color_value = $request->color_value;
            $content_type = $request->content_type;
            $json_pages_sequence = $request->json_pages_sequence;

            $uuid = (new ImageController())->generateUUID();
            $data = ['user_id' => $user_id,
                'uuid' => $uuid,
                'sub_category_id' => 37,
                'folder_id' => $folder_id,
                'user_template_name' => $user_template_name,
                'json_data' => $json_data,
                'is_multipage' => 1,
                'image' => $sample_image_name,
                'overlay_image' => $overlay_image,
                'color_value' => $color_value,
                'content_type' => $content_type,
                'json_pages_sequence' => $json_pages_sequence,
                'is_active' => 1,
                'create_time' => date('Y-m-d H:i:s'),
            ];
            $my_design_id = DB::table('my_design_master')->insertGetId($data);
            $existing_design = DB::select('SELECT my_design_ids FROM design_folder_master WHERE id = ? AND user_id = ? ', [$folder_id, $user_id]);

            $existing_design_ids = $existing_design[0]->my_design_ids;

            ($existing_design_ids == null) ? $my_design_ids = $my_design_id : $my_design_ids = $existing_design_ids.','.$my_design_id;
            DB::update('UPDATE design_folder_master SET my_design_ids = ? WHERE id = ?', [$my_design_ids, $folder_id]);

            $response = Response::json(['code' => 200, 'message' => 'Template added successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('insertMyDesign', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'add template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }
}
