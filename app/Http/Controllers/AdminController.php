<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  AdminController.php

File Created    :  Monday, 15th March 2019 05:22:26 pm
Author          :  Optimumbrew
Auther Email    :  info@optimumbrew.com
Last Modified   :  Thursday, 27th January  2022 12:18:38 pm
-----
Purpose          :  This file handle the data from admin panel in user side.
-----
Copyright 2018 - 2022 Optimumbrew Technology

*/

namespace App\Http\Controllers;

use App\Jobs\EmailJob;
use App\Jobs\PreviewVideoJob;
use App\Jobs\RunCommandAsRoot;
use App\Jobs\RunPhpFunctionAsRoot\FilePutContents;
use App\Jobs\RunPhpFunctionAsRoot\Mkdir;
use App\Jobs\SendMailJob;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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
use Intervention\Image\Facades\Image;
use Swagger\Annotations as SWG;
use Swift_TransportException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AdminController
 */
class AdminController extends Controller
{
    public $item_count;

    public function __construct()
    {
        $this->item_count = Config::get('constant.PAGINATION_ITEM_LIMIT');
        $this->base_url = (new ImageController())->getBaseUrl();
    }

    /* =========================================| Category |=========================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addCategory",
     *        summary="Add category",
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
     *          @SWG\Property(property="name",  type="string", example="Frame", description=""),
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
    /**
     * @api {post} addCategory   addCategory
     *
     * @apiName addCategory
     *
     * @apiGroup Admin
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
     * "name":"Nature"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['name'], $request)) != '') {
                return $response;
            }

            $name = trim($request->name);
            $create_time = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfCategoryExist($name)) != '') {
                return $response;
            }
            $uuid = (new ImageController())->generateUUID();
            DB::beginTransaction();
            DB::insert('INSERT INTO category (uuid,name,create_time) VALUES(?,?,?)', [$uuid, $name, $create_time]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Category added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addCategory', $e);
            //      Log::error("addCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateCategory",
     *        summary="Update category",
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
     *          required={"category_id","name"},
     *
     *          @SWG\Property(property="category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="name",  type="string", example="Frame", description=""),
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
    /**
     * @api {post} updateCategory   updateCategory
     *
     * @apiName updateCategory
     *
     * @apiGroup Admin
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
     * "category_id":1, //compulsory
     * "name":"Nature" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Category updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'name'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $name = trim($request->name);

            $result = DB::select('SELECT 1 FROM category WHERE name = ? AND id != ?', [$name, $category_id]);

            if (count($result) > 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Category already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                DB::beginTransaction();
                DB::update('UPDATE
                        category
                      SET
                        name = ?
                      WHERE
                    id = ? ',
                    [$name, $category_id]);
                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Category updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateCategory', $e);
            //      Log::error("updateCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update a category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteCategory",
     *        summary="Delete category",
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
     *          required={"category_id","name"},
     *
     *          @SWG\Property(property="category_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} deleteCategory   deleteCategory
     *
     * @apiName deleteCategory
     *
     * @apiGroup Admin
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
     * "category_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Category deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteCategory(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $is_active = 0;

            DB::beginTransaction();
            DB::update('UPDATE category SET is_active=? WHERE id = ? ', [$is_active, $category_id]);
            DB::update('UPDATE sub_category_master SET is_active = ? WHERE category_id = ?', [0, $category_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Category deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteCategory', $e);
            //      Log::error("deleteCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getAllCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getAllCategory",
     *        summary="Get all categories",
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
     * @api {post} getAllCategory   getAllCategory
     *
     * @apiName getAllCategory
     *
     * @apiGroup Admin
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
     * "message": "All categories fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "category_id": 1,
     * "name": "Frame"
     * },
     * {
     * "category_id": 2,
     * "name": "Sticker"
     * },
     * {
     * "category_id": 3,
     * "name": "Background"
     * }
     * ]
     * }
     * }
     */
    public function getAllCategory()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllCategory")) {
                $result = Cache::rememberforever('getAllCategory', function () {
                    return DB::select('SELECT
                                ct.id AS category_id,
                                ct.name
                              FROM
                              category AS ct
                              WHERE is_active = ?', [1]);
                });
            }

            $redis_result = Cache::get('getAllCategory');

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All categories fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllCategory', $e);
            //      Log::error("getAllCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =========================================| Sub Category |=========================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addSubCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addSubCategory",
     *        summary="Add sub category",
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
     *          description="Give sub_category_name & category_id in json object",
     *
     *         @SWG\Schema(
     *              required={"category_id","sub_category_name"},
     *
     *              @SWG\Property(property="category_id",  type="integer", example=1),
     *              @SWG\Property(property="sub_category_name",type="string", example="Business Card Frames"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="file uploading",
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
     * @api {post} addSubCategory   addSubCategory
     *
     * @apiName addSubCategory
     *
     * @apiGroup Admin
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
     * "category_id":1, //compulsory
     * "sub_category_name":"Nature", //compulsory
     * "is_featured": 1 //compulsory 1=featured (for templates), 0=normal (shapes, textArt,etc...)
     * }
     * file:image.jpeg //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'sub_category_name', 'is_featured'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $sub_category_name = trim($request->sub_category_name);
            $is_featured = $request->is_featured;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog
            $create_at = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfSubCategoryExist($sub_category_name, 0)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $image_array = Input::file('file');

                /* Here we passes category_id=0 bcz we want to use common image validation for the sub_category_image */
                if (($response = (new ImageController())->verifyImage($image_array, 0, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $category_img = (new ImageController())->generateNewFileName('sub_category_img', $image_array);
                (new ImageController())->saveOriginalImage($category_img);
                (new ImageController())->saveCompressedImage($category_img);
                (new ImageController())->saveThumbnailImage($category_img);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($category_img);
                }

            }

            $uuid = (new ImageController())->generateUUID();
            DB::beginTransaction();
            DB::insert('INSERT INTO sub_category_master
                          (sub_category_name,uuid,category_id,image,is_featured,create_time) VALUES(?,?,?,?,?,?)',
                [$sub_category_name, $uuid, $category_id, $category_img, $is_featured, $create_at]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Sub category added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addSubCategory', $e);
            //      Log::error("addSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateSubCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateSubCategory",
     *        summary="Update sub category",
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
     *          description="Give sub_category_name & category_id in json object",
     *
     *         @SWG\Schema(
     *              required={"sub_category_id","sub_category_name"},
     *
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1),
     *              @SWG\Property(property="sub_category_name",type="string", example="Business Card Frames"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="file uploading",
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
     * @api {post} updateSubCategory   updateSubCategory
     *
     * @apiName updateSubCategory
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
     * "request_data":{
     * "sub_category_id":2, //compulsory
     * "name":"Love-Category", //compulsory
     * "is_featured": 1 //compulsory 1=featured (for templates), 0=normal (shapes, textArt,etc...)
     * },
     * "file":image.png //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'sub_category_name', 'is_featured'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $request->sub_category_id;
            $sub_category_name = trim($request->sub_category_name);
            $is_featured = $request->is_featured;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog

            if (($response = (new VerificationController())->checkIfSubCategoryExist($sub_category_name, $sub_category_id)) != '') {
                return $response;
            }

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                /* Here we passes category_id=0 bcz we want to use common image validation for the sub_category_image */
                if (($response = (new ImageController())->verifyImage($image_array, 0, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $sub_category_img = (new ImageController())->generateNewFileName('sub_category_img', $image_array);
                (new ImageController())->saveOriginalImage($sub_category_img);
                (new ImageController())->saveCompressedImage($sub_category_img);
                (new ImageController())->saveThumbnailImage($sub_category_img);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($sub_category_img);
                }

                $result = DB::select('SELECT image FROM sub_category_master WHERE id = ?', [$sub_category_id]);
                $image_name = $result[0]->image;

                if ($image_name) {
                    //Delete image from image_bucket
                    (new ImageController())->deleteImage($image_name);
                }
            } else {
                $sub_category_img = '';
            }

            DB::beginTransaction();
            DB::update('UPDATE sub_category_master SET
                                sub_category_name = IF(? != "",?,sub_category_name),
                                image = IF(? != "",?,image),
                                is_featured = IF(? != is_featured,?,is_featured)
                              WHERE id = ?', [$sub_category_name, $sub_category_name, $sub_category_img, $sub_category_img, $is_featured, $is_featured, $sub_category_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Sub category updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateSubCategory', $e);
            //      Log::error("updateSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteSubCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteSubCategory",
     *        summary="Delete sub category",
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
    /**
     * @api {post} deleteSubCategory   deleteSubCategory
     *
     * @apiName deleteSubCategory
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
     * "sub_category_id":3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteSubCategory(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $request->sub_category_id;
            $is_active = 0;

            DB::beginTransaction();
            DB::update('UPDATE sub_category_master SET is_active = ? WHERE id = ? ', [$is_active, $sub_category_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Sub category deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteSubCategory', $e);
            //      Log::error("deleteSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getSubCategoryByCategoryIdForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getSubCategoryByCategoryIdForAdmin",
     *        summary="Get sub category by category id",
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
     *          required={"page","item_count","category_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=15, description=""),
     *          @SWG\Property(property="category_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} getSubCategoryByCategoryIdForAdmin   getSubCategoryByCategoryIdForAdmin
     *
     * @apiName getSubCategoryByCategoryIdForAdmin
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
     * "page":3, //compulsory
     * "item_count":20, //compulsory
     * "category_id":3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 64,
     * "is_next_page": true,
     * "category_name": "Templates",
     * "result": [
     * {
     * "sub_category_id": 1,
     * "category_id": 4,
     * "sub_category_name": "Snapchat Geo Filter",
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c0240cb817ca_category_img_1543651531.png",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c0240cb817ca_category_img_1543651531.png",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5c0240cb817ca_category_img_1543651531.png",
     * "is_featured": 0
     * }
     * ]
     * }
     * }
     */
    public function getSubCategoryByCategoryIdForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'category_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->category_id = $request->category_id;
            $this->item_count_sub_category = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count_sub_category;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSubCategoryByCategoryIdForAdmin$this->category_id:$this->page:$this->item_count_sub_category")) {
                $result = Cache::rememberforever("getSubCategoryByCategoryIdForAdmin$this->category_id:$this->page:$this->item_count_sub_category", function () {

                    $is_active = 1;

                    //get category name
                    $name = DB::select('SELECT sc.name FROM  category AS sc WHERE id = ? AND is_active = ?', [$this->category_id, $is_active]);
                    $category_name = $name[0]->name;

                    $total_row_result = DB::select('SELECT COUNT(*) AS total FROM sub_category_master WHERE is_active = ? AND category_id = ?', [$is_active, $this->category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                          scm.id as sub_category_id,
                                          scm.category_id,
                                          scm.sub_category_name,
                                          IF(scm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",scm.image),"") AS thumbnail_img,
                                          IF(scm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",scm.image),"") AS compressed_img,
                                          IF(scm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",scm.image),"") AS original_img,
                                          scm.is_featured
                                        FROM
                                          sub_category_master AS scm
                                        WHERE
                                          scm.category_id = ? AND
                                          scm.is_active=?
                                        ORDER BY scm.create_time ASC
                                        LIMIT ?,?', [$this->category_id, $is_active, $this->offset, $this->item_count_sub_category]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'category_name' => $category_name, 'result' => $result];

                });
            }

            $redis_result = Cache::get("getSubCategoryByCategoryIdForAdmin$this->category_id:$this->page:$this->item_count_sub_category");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sub category fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getSubCategoryByCategoryIdForAdmin', $e);
            //      Log::error("getSubCategoryByCategoryIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get sub categories.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchSubCategoryByName",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchSubCategoryByName",
     *        summary="Search sub category by name",
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
     *          required={"category_id","sub_category_name"},
     *
     *          @SWG\Property(property="category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="sub_category_name",  type="string", example="Business Card", description=""),
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
    /**
     * @api {post} searchSubCategoryByName   searchSubCategoryByName
     *
     * @apiName searchSubCategoryByName
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
     * "category_id":3, //compulsory
     * "sub_category_name":"Flyer" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub category search successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "sub_category_id": 37,
     * "sub_category_name": "Flyer",
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c02486b17a8b_category_img_1543653483.png",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c02486b17a8b_category_img_1543653483.png",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5c02486b17a8b_category_img_1543653483.png",
     * "is_featured": 1
     * }
     * ]
     * }
     * }
     */
    public function searchSubCategoryByName(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'sub_category_name'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $sub_category_name = '%'.trim($request->sub_category_name).'%';

            $result = DB::select('SELECT
                                      scm.id AS sub_category_id,
                                      scm.sub_category_name,
                                      IF(scm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",scm.image),"") AS thumbnail_img,
                                      IF(scm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",scm.image),"") AS compressed_img,
                                      IF(scm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",scm.image),"") AS original_img,
                                      scm.is_featured
                                     FROM
                                      sub_category_master AS scm
                                     WHERE
                                      scm.category_id = ? AND
                                      scm.is_active = ? AND
                                      scm.sub_category_name LIKE ? ', [$category_id, 1, $sub_category_name]);

            $response = Response::json(['code' => 200, 'message' => 'Sub category search successfully.', 'cause' => '', 'data' => ['result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchSubCategoryByName', $e);
            //      Log::error("searchSubCategoryByName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllSubCategoryByCategoryIdForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id'], $request)) != '') {
                return $response;
            }

            $this->category_id = $request->category_id;

            $redis_result = Cache::remember("getAllSubCategoryByCategoryIdForAdmin:$this->category_id", Config::get('constant.CACHE_TIME_7_DAYS'), function () {

                return DB::select('SELECT
                                              scm.id AS id,
                                              scm.uuid AS uuid,
                                              scm.category_id,
                                              scm.sub_category_name,
                                              IF(scm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", scm.image),"") AS thumbnail_img,
                                              IF(scm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", scm.image),"") AS compressed_img,
                                              IF(scm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", scm.image),"") AS original_img,
                                              scm.is_featured
                                        FROM
                                              sub_category_master AS scm
                                        WHERE
                                              scm.category_id = ? AND
                                              scm.is_active = 1
                                        ORDER BY scm.sub_category_name ASC', [$this->category_id]);

            });

            $response = Response::json(['code' => 200, 'message' => 'Sub category fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllSubCategoryByCategoryIdForAdmin', $e);
            //Log::error("getAllSubCategoryByCategoryIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get sub categories.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* ========================================= Catalog Category =========================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addCatalog",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addCatalog",
     *        summary="Add catalog",
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
     *          description="Give sub_category_id, name, is_featured & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"sub_category_id","name","is_featured","is_free"},
     *
     *              @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="name",type="string", example="Business Card Frames", description=""),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="catalog image",
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
     * @api {post} addCatalog   addCatalog
     *
     * @apiName addCatalog
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "sub_category_id":1,
     * "name":1,
     * "is_featured":1, //0=normal 1=featured
     * "is_free":1 //0=paid 1=free
     * }
     * file:image1.jpeg //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCatalog(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'sub_category_id', 'name', 'is_featured', 'is_free'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $sub_category_id = $request->sub_category_id;
            $name = trim($request->name);
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $create_time = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfCatalogExist($sub_category_id, $name, '')) != '') {
                return $response;
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $file_array = Input::file('file');

                /* Here we passes is_catalog=1 bcz this is a catalog image */
                if (($response = (new ImageController())->verifyImage($file_array, $category_id, $is_featured, 1)) != '') {
                    return $response;
                }

                $file_name = (new ImageController())->generateNewFileName('catalog_img', $file_array);
                (new ImageController())->saveOriginalImage($file_name);
                (new ImageController())->saveCompressedImage($file_name);
                (new ImageController())->saveThumbnailImage($file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($file_name);
                }

            }
            $uuid = (new ImageController())->generateUUID();
            $data = ['name' => $name,
                'uuid' => $uuid,
                'image' => $file_name,
                'is_free' => $is_free,
                'is_featured' => $is_featured,
                'create_time' => $create_time,
            ];

            DB::beginTransaction();
            $catalog_id = DB::table('catalog_master')->insertGetId($data);
            $uuid = (new ImageController())->generateUUID();
            DB::insert('INSERT INTO sub_category_catalog(uuid,sub_category_id,catalog_id,create_time) VALUES (?, ?,?, ?)', [$uuid, $sub_category_id, $catalog_id, $create_time]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Catalog added successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('addCatalog', $e);
            //      Log::error("addCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateCatalog",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateCatalog",
     *        summary="Update catalog",
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
     *          description="Give catalog_id, name, is_featured & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","name","is_featured","is_free"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="name",type="string", example="Business Card Frames", description=""),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="catalog image",
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
     * @api {post} updateCatalog   updateCatalog
     *
     * @apiName updateCatalog
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "sub_category_id":1,
     * "catalog_id":1,
     * "name":1,
     * is_featured":1, //0=normal 1=featured
     * "is_free":1 //0=paid 1=free
     * }
     * file:image1.jpeg //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateCatalog(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'sub_category_id', 'catalog_id', 'name', 'is_free', 'is_featured'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $sub_category_id = $request->sub_category_id;
            $catalog_id = $request->catalog_id;
            $name = $request->name;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;

            if (($response = (new VerificationController())->checkIfCatalogExist($sub_category_id, $name, $catalog_id)) != '') {
                return $response;
            }

            if ($request_body->hasFile('file')) {
                $file_array = Input::file('file');

                /* Here we passes is_catalog=1 bcz this is a catalog image */
                if (($response = (new ImageController())->verifyImage($file_array, $category_id, $is_featured, 1)) != '') {
                    return $response;
                }

                $catalog_img_name = (new ImageController())->generateNewFileName('catalog_img', $file_array);
                (new ImageController())->saveOriginalImage($catalog_img_name);
                (new ImageController())->saveCompressedImage($catalog_img_name);
                (new ImageController())->saveThumbnailImage($catalog_img_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_img_name);
                }

                $result = DB::select('SELECT image FROM catalog_master WHERE id = ?', [$catalog_id]);
                $image_name = $result[0]->image;
                if ($image_name) {
                    //Delete image from storage
                    (new ImageController())->deleteImage($image_name);
                }

            } else {
                $catalog_img_name = '';
            }

            DB::beginTransaction();
            DB::update('UPDATE catalog_master SET
                                  name = IF(? != "",?,name),
                                  image = IF(? != "",?,image),
                                  is_free = IF(? != is_free,?,is_free),
                                  is_featured = IF(? != is_featured,?,is_featured)
                                WHERE id = ?', [$name, $name, $catalog_img_name, $catalog_img_name, $is_free, $is_free, $is_featured, $is_featured, $catalog_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Catalog updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateCatalog', $e);
            //      Log::error("updateCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteCatalog",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteCatalog",
     *        summary="Delete catalog",
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
     *          required={"catalog_id"},
     *
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} deleteCatalog   deleteCatalog
     *
     * @apiName deleteCatalog
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
     * "catalog_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteCatalog(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $is_active = 0;

            DB::statement('SET SESSION group_concat_max_len = 1000000');
            $content_ids = DB::select('SELECT
                                    GROUP_CONCAT(content_ids) AS content_ids
                                  FROM
                                    static_page_master
                                  WHERE content_type != 0');

            $details = DB::select('SELECT
                                id AS content_id
                              FROM
                                content_master
                              WHERE
                                is_active = 1 AND
                                catalog_id = ? AND
                                ISNULL(original_img) AND
                                ISNULL(display_img) AND
                                id IN ('.$content_ids[0]->content_ids.')', [$catalog_id]);

            if (count($details) > 0) {
                $response = Response::json(['code' => 201, 'message' => 'You are trying to delete catalog which have templates those are used in static page.', 'cause' => '', 'data' => json_decode('{}')]);

                return $response;
            }

            DB::beginTransaction();
            DB::update('UPDATE catalog_master SET is_active = ?, is_featured = ?  WHERE id = ? ', [$is_active, 0, $catalog_id]);
            DB::update('UPDATE sub_category_catalog SET is_active = ? WHERE catalog_id = ? ', [$is_active, $catalog_id]);

            DB::delete('DELETE FROM content_master WHERE catalog_id = ?', [$catalog_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Catalog deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteCatalog', $e);
            //      Log::error("deleteCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getCatalogBySubCategoryId",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getCatalogBySubCategoryId",
     *        summary="Get all catalog by sub category id",
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
    /**
     * @api {post} getCatalogBySubCategoryId   getCatalogBySubCategoryId
     *
     * @apiName getCatalogBySubCategoryId
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
     * "sub_category_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All catalogs fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "category_name": "Sample 3D Object Category",
     * "result": [
     * {
     * "catalog_id": 16,
     * "name": "3D Text",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bb4a19857941_catalog_img_1538564504.JPG",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bb4a19857941_catalog_img_1538564504.JPG",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bb4a19857941_catalog_img_1538564504.JPG",
     * "is_free": 1,
     * "is_featured": 1
     * },
     * {
     * "catalog_id": 5,
     * "name": "3D Shape",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bab6f17ce451_catalog_img_1537961751.JPG",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bab6f17ce451_catalog_img_1537961751.JPG",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bab6f17ce451_catalog_img_1537961751.JPG",
     * "is_free": 1,
     * "is_featured": 1
     * }
     * ]
     * }
     * }
     */
    public function getCatalogBySubCategoryId(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getCatalogBySubCategoryId$this->sub_category_id")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryId$this->sub_category_id", function () {

                    //sub category name
                    $name = DB::select('SELECT scm.sub_category_name FROM  sub_category_master as scm WHERE scm.id = ? AND scm.is_active = ?', [$this->sub_category_id, 1]);
                    $category_name = $name[0]->sub_category_name;

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM  sub_category_catalog WHERE sub_category_id = ? AND is_active = ?', [$this->sub_category_id, 1]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                    ctm.id AS catalog_id,
                                    ctm.name,
                                    IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") AS thumbnail_img,
                                    IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") AS compressed_img,
                                    IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") AS original_img,
                                    ctm.is_free,
                                    ctm.is_featured
                                  FROM
                                    catalog_master AS ctm,
                                    sub_category_catalog AS sct
                                  WHERE
                                    sct.sub_category_id = ? AND
                                    sct.catalog_id = ctm.id AND
                                    sct.is_active = 1
                                  ORDER BY ctm.update_time DESC', [$this->sub_category_id]);

                    return ['total_record' => $total_row, 'category_name' => $category_name, 'result' => $result];
                });

            }

            $redis_result = Cache::get("getCatalogBySubCategoryId$this->sub_category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All catalogs fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getCatalogBySubCategoryId', $e);
            //      Log::error("getCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchCatalogByName",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchCatalogByName",
     *        summary="Get all catalog by sub category id",
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
     *          required={"sub_category_id","name"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="name",  type="string", example="Business Card", description=""),
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
    /**
     * @api {post} searchCatalogByName   searchCatalogByName
     *
     * @apiName searchCatalogByName
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
     * "sub_category_id":1,
     * "name":"black"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog search successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "catalog_id": 8,
     * "name": "Classic",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5badffa535eab_catalog_img_1538129829.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5badffa535eab_catalog_img_1538129829.jpg",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5badffa535eab_catalog_img_1538129829.jpg",
     * "is_free": 1,
     * "is_featured": 1
     * }
     * ]
     * }
     * }
     */
    public function searchCatalogByName(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'name'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $request->sub_category_id;
            $name = '%'.trim($request->name).'%';

            $result = DB::select('SELECT
                                ctm.id AS catalog_id,
                                ctm.name,
                                IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") AS thumbnail_img,
                                IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") AS compressed_img,
                                IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ctm.image),"") AS original_img,
                                ctm.is_free,
                                ctm.is_featured
                               FROM
                                  catalog_master AS ctm,
                                  sub_category_catalog AS sct
                                WHERE
                                  sct.sub_category_id = ? AND
                                  sct.catalog_id = ctm.id AND
                                  sct.is_active = 1 AND
                                  ctm.name LIKE ? ', [$sub_category_id, $name]);

            $response = Response::json(['code' => 200, 'message' => 'Catalog search successfully.', 'cause' => '', 'data' => ['result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchCatalogByName', $e);
            //      Log::error("searchCatalogByName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllCatalogBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;

            $redis_result = Cache::remember("getAllCatalogBySubCategoryId:$this->sub_category_id", Config::get('constant.CACHE_TIME_7_DAYS'), function () {

                return DB::select('SELECT
                                        ctm.id AS id,
                                        ctm.uuid AS uuid,
                                        ctm.name,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ctm.image),"") AS thumbnail_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'" ,ctm.image),"") AS compressed_img,
                                        IF(ctm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ctm.image),"") AS original_img,
                                        ctm.is_free,
                                        ctm.is_featured
                                  FROM
                                        catalog_master AS ctm,
                                        sub_category_catalog AS sct,
                                        sub_category_master AS scm
                                  WHERE
                                        sct.catalog_id = ctm.id AND
                                        sct.sub_category_id = scm.id AND
                                        (scm.id = ? OR scm.uuid = ?) AND
                                        sct.is_active = 1
                                  ORDER BY ctm.create_time ASC', [$this->sub_category_id, $this->sub_category_id]);
            });

            $response = Response::json(['code' => 200, 'message' => 'All catalogs fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllCatalogBySubCategoryId', $e);
            //Log::error("getAllCatalogBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =========================================| Normal Images |========================================= */

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addNormalImages",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addNormalImages",
     *        summary="Add normal images",
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
     *          description="Give catalog_id in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","search_category"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="search_category",  type="string", example="landscape,portrait", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file[]",
     *         in="formData",
     *         description="array of normal image",
     *         required=true,
     *         type="file",
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
     * @api {post} addNormalImages   addNormalImages
     *
     * @apiName addNormalImages
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "catalog_id":1,
     * "is_featured":1 //1=featured catalog, 0=normal catalog
     * }
     * file[]:image.jpeg //compulsory
     * file[]:image12.jpeg
     * file[]:image.png
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal images added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addNormalImages(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'catalog_id', 'is_featured'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $catalog_id = $request->catalog_id;
            $is_featured = $request->is_featured;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is normal images
            $create_at = date('Y-m-d H:i:s');

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $images_array = Input::file('file');

                //To verify all sticker/bkg.. images array
                if (($response = (new ImageController())->verifyImagesArray($images_array, 2, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                foreach ($images_array as $key) {

                    /*if (($response = (new ImageController())->verifyStickerImage($image_array)) != '')
                      return $response;*/

                    $content_type = (new ImageController())->getImageType($key);

                    if ($content_type == 8) {
                        $normal_image = (new ImageController())->generateNewFileName('normal_image', $key);

                        (new ImageController())->saveSvgImage($key, $normal_image);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveSvgImageInToS3($normal_image);
                        }
                        $tag_list = null;

                    } else {

                        $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($key));

                        /*if ($tag_list == "" or $tag_list == NULL) {
                          return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarify.com.', 'cause' => '', 'data' => json_decode("{}")));
                        }*/

                        $normal_image = (new ImageController())->generateNewFileName('normal_image', $key);
                        (new ImageController())->saveOriginalImageFromArray($key, $normal_image);
                        (new ImageController())->saveCompressedImage($normal_image);
                        (new ImageController())->saveThumbnailImage($normal_image);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveImageInToS3($normal_image);
                        }
                    }
                    $uuid = (new ImageController())->generateUUID();

                    $catalog_detail = DB::select('SELECT name FROM catalog_master WHERE id = ?', [$catalog_id]);
                    $tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $catalog_detail[0]->name)));
                    if ($tag_list != '') {
                        $tag_list .= ','.$tag;
                    } else {
                        $tag_list .= $tag;
                    }
                    $tag_list = implode(',', array_unique(array_filter(explode(',', $tag_list))));

                    DB::beginTransaction();
                    DB::insert('INSERT
                        INTO
                          content_master(catalog_id,uuid, image, content_type, search_category, create_time)
                        VALUES(?, ?, ?, ?,?, ?) ', [$catalog_id, $uuid, $normal_image, $content_type, $tag_list, $create_at]);
                    DB::commit();
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Normal images added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addNormalImages', $e);
            //      Log::error("addNormalImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add normal images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateNormalImage",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateNormalImage",
     *        summary="Update normal images",
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
     *          description="Give content_id in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="normal image",
     *         required=true,
     *         type="file",
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
     * @api {post} updateNormalImage   updateNormalImage
     *
     * @apiName updateNormalImage
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "content_id":1,
     * "is_featured":1, //1=featured catalog, 0=normal catalog
     * "search_category":"test,abc" //optional
     * }
     * file:1.jpg //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Image updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateNormalImage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'content_id', 'search_category', 'is_featured'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $content_id = $request->content_id;
            $search_category = strtolower($request->search_category);
            $is_featured = $request->is_featured;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is normal images

            if ($search_category != null or $search_category != '') {
                if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '') {
                    return $response;
                }
            }

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifyStickerImage($image_array, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $content_type = (new ImageController())->getImageType($image_array);
                if ($content_type == 8) {
                    $normal_image = (new ImageController())->generateNewFileName('normal_image', $image_array);

                    (new ImageController())->saveSvgImage($image_array, $normal_image);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveSvgImageInToS3($normal_image);
                    }
                    $tag_list = null;
                } else {

                    $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($image_array));

                    $normal_image = (new ImageController())->generateNewFileName('normal_image', $image_array);

                    (new ImageController())->saveOriginalImage($normal_image);
                    (new ImageController())->saveCompressedImage($normal_image);
                    (new ImageController())->saveThumbnailImage($normal_image);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($normal_image);
                    }
                }
            } else {
                $normal_image = '';
                $tag_list = $search_category;
            }
            $content_type = isset($content_type) ? "content_type =IF($content_type != content_type,$content_type,content_type) ," : '';
            DB::beginTransaction();
            DB::update('UPDATE
                      content_master
                    SET
                      image = IF(? != "",?,image),
                      '.$content_type.'
                      search_category = ?
                    WHERE
                      id = ? ',
                [$normal_image, $normal_image, $tag_list, $content_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Image updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateNormalImage', $e);
            //      Log::error("updateNormalImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ==============================| Featured images for background |================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addSampleImages",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addSampleImages",
     *        summary="Add featured sample images",
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
     *          description="Give catalog_id in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","image_type"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="image_type",  type="integer", example=1, description="1=Background , 2=Frame"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="original_img",
     *         in="formData",
     *         description="array of normal image",
     *         required=true,
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="display_img",
     *         in="formData",
     *         description="array of normal image",
     *         required=true,
     *         type="file",
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
     * @api {post} addSampleImages   addSampleImages
     *
     * @apiName addSampleImages
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "catalog_id":1,
     * "image_type":1, //1=Background , 2=Frame
     * "is_featured":1 //1=featured catalog, 0=normal catalog
     * }
     * original_img:image1.jpeg //compulsory
     * display_img:image12.jpeg //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sample images added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addSampleImages(Request $request_body)
    {

        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'catalog_id', 'image_type', 'is_featured'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $catalog_id = $request->catalog_id;
            $image_type = $request->image_type;
            $is_featured = $request->is_featured;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is normal images
            $create_time = date('Y-m-d H:i:s');

            if (! $request_body->hasFile('original_img') and ! $request_body->hasFile('display_img')) {
                return Response::json(['code' => 201, 'message' => 'Required field original_img or display_img is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } elseif (! $request_body->hasFile('original_img')) {

                return Response::json(['code' => 201, 'message' => 'Required field original_img is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);

            } elseif (! $request_body->hasFile('display_img')) {
                return Response::json(['code' => 201, 'message' => 'Required field display_img is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);

            } else {
                if ($request_body->hasFile('original_img')) {
                    $image_array = Input::file('original_img');

                    if (($response = (new ImageController())->verifyImage($image_array, $category_id, $is_featured, $is_catalog)) != '') {
                        return $response;
                    }

                    $original_img = (new ImageController())->generateNewFileName('original_img', $image_array);
                    $file_name = 'original_img';
                    (new ImageController())->saveMultipleOriginalImage($original_img, $file_name);
                    (new ImageController())->saveMultipleCompressedImage($original_img, $file_name);
                    (new ImageController())->saveMultipleThumbnailImage($original_img, $file_name);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($original_img);
                    }

                }
                if ($request_body->hasFile('display_img')) {

                    $image_array = Input::file('display_img');

                    if (($response = (new ImageController())->verifyImage($image_array, $category_id, $is_featured, $is_catalog)) != '') {
                        return $response;
                    }

                    $display_img = (new ImageController())->generateNewFileName('display_img', $image_array);

                    $file_name = 'display_img';
                    (new ImageController())->saveMultipleOriginalImage($display_img, $file_name);
                    (new ImageController())->saveMultipleCompressedImage($display_img, $file_name);
                    (new ImageController())->saveMultipleThumbnailImage($display_img, $file_name);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($display_img);
                    }

                }
                $uuid = (new ImageController())->generateUUID();
                DB::beginTransaction();
                DB::insert('INSERT INTO content_master(
                        catalog_id,
                        uuid,
                        original_img,
                        display_img,
                        image_type,
                        is_active,
                        create_time)
                        VALUES (?, ?, ?, ?, ?, ?,?)',
                    [
                        $uuid,
                        $catalog_id,
                        $original_img,
                        $display_img,
                        $image_type,
                        1,
                        $create_time]);
                DB::commit();

                $response = Response::json(['code' => 200, 'message' => 'Sample images added successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('addSampleImages', $e);
            //      Log::error("addSampleImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add sample images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateSampleImages",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateSampleImages",
     *        summary="Update featured sample images into catalogs",
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
     *          description="Give content_id & image_type(1=Background , 2=Frame) in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","image_type"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="image_type",  type="integer", example=1, description="1=Background , 2=Frame"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="original_img",
     *         in="formData",
     *         description="array of normal image",
     *         type="file",
     *     ),
     *     @SWG\Parameter(
     *         name="display_img",
     *         in="formData",
     *         description="array of normal image",
     *         type="file",
     *     ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="success",
     *     ),
     *     @SWG\Response(
     *            response=201,
     *            description="error",
     *     ),
     * )
     */
    /**
     * @api {post} updateSampleImages   updateSampleImages
     *
     * @apiName updateSampleImages
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "content_id":1,
     * "image_type":1, //1=Background , 2=Frame
     * "is_featured":1 //1=featured catalog, 0=normal catalog
     * }
     * original_img:image1.jpeg //optional
     * display_img:image12.jpeg //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sample images updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateSampleImages(Request $request_body)
    {

        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'content_id', 'image_type', 'is_featured'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $content_id = $request->content_id;
            $image_type = $request->image_type;
            $is_featured = $request->is_featured;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is featured background images

            /* original_img : background image without content */
            /* display_img : sample image show to the user */

            DB::beginTransaction();
            if ($request_body->hasFile('original_img')) {
                $image_array = Input::file('original_img');

                if (($response = (new ImageController())->verifyImage($image_array, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $original_img = (new ImageController())->generateNewFileName('original_img', $image_array);
                $file_name = 'original_img';
                (new ImageController())->saveMultipleOriginalImage($original_img, $file_name);
                (new ImageController())->saveMultipleCompressedImage($original_img, $file_name);
                (new ImageController())->saveMultipleThumbnailImage($original_img, $file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($original_img);
                }

                DB::update('UPDATE content_master SET original_img = ?,image_type = ? WHERE id = ?', [$original_img, $image_type, $content_id]);

            } else {
                $original_img = '';
            }
            if ($request_body->hasFile('display_img')) {

                $image_array = Input::file('display_img');

                if (($response = (new ImageController())->verifyImage($image_array, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $display_img = (new ImageController())->generateNewFileName('display_img', $image_array);
                $file_name = 'display_img';
                (new ImageController())->saveMultipleOriginalImage($display_img, $file_name);
                (new ImageController())->saveMultipleCompressedImage($display_img, $file_name);
                (new ImageController())->saveMultipleThumbnailImage($display_img, $file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($display_img);
                }

                DB::update('UPDATE content_master SET display_img = ?,image_type = ? WHERE id = ?', [$display_img, $image_type, $content_id]);

            } else {
                $display_img = '';
            }

            if ($original_img == '' && $display_img == '') {
                DB::update('UPDATE content_master SET image_type = ? WHERE id = ?', [$image_type, $content_id]);
            }

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Sample images updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('updateSampleImages', $e);
            //      Log::error("updateSampleImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update sample images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getSampleImagesForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getSampleImagesForAdmin",
     *        summary="Get sample images",
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
     *          required={"catalog_id"},
     *
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} getSampleImagesForAdmin   getSampleImagesForAdmin
     *
     * @apiName getSampleImagesForAdmin
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
     * "catalog_id":4 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "content_id": 240,
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdd5bfeb5633_3D_object_image_1541233662.JPG",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdd5bfeb5633_3D_object_image_1541233662.JPG",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdd5bfeb5633_3D_object_image_1541233662.JPG",
     * "content_file": "http://192.168.0.113/photoadking/image_bucket/3d_object/5bdd5bfee6766_stl_object_1541233662.stl",
     * "content_type": 7,
     * "json_data": "",
     * "is_featured": 1,
     * "is_free": 1,
     * "is_portrait": 0,
     * "search_category": ""
     * },
     * {
     * "content_id": 239,
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdd5bf7be915_3D_object_image_1541233655.JPG",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdd5bf7be915_3D_object_image_1541233655.JPG",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdd5bf7be915_3D_object_image_1541233655.JPG",
     * "content_file": "http://192.168.0.113/photoadking/image_bucket/3d_object/5bdd5bf813ac6_stl_object_1541233656.stl",
     * "content_type": 7,
     * "json_data": "",
     * "is_featured": 1,
     * "is_free": 1,
     * "is_portrait": 0,
     * "search_category": ""
     * }
     * ]
     * }
     * }
     */
    public function getSampleImagesForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSampleImagesForAdmin$this->catalog_id")) {
                $result = Cache::rememberforever("getSampleImagesForAdmin$this->catalog_id", function () {
                    return DB::select('SELECT
                                cm.id AS img_id,
                                IF(cm.original_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.original_img),"") AS original_thumbnail_img,
                                IF(cm.original_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.original_img),"") AS original_compressed_img,
                                IF(cm.original_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.original_img),"") AS original_original_img,
                                IF(cm.display_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.display_img),"") AS display_thumbnail_img,
                                IF(cm.display_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.display_img),"") AS display_compressed_img,
                                IF(cm.display_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.display_img),"") AS display_original_img,
                                cm.image_type
                              FROM
                                content_master AS cm,
                                catalog_master AS ctm
                              WHERE
                                cm.is_active = 1 AND
                                cm.catalog_id = ? AND
                                ctm.id=cm.catalog_id AND
                                isnull(cm.image) AND
                                ctm.is_featured = 1
                              ORDER BY cm.update_time DESC', [$this->catalog_id]);
                });
            }
            $redis_result = Cache::get("getSampleImagesForAdmin$this->catalog_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sample images fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getSampleImagesForAdmin', $e);
            //      Log::error("getSampleImagesForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get sample image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* ==========================| Common for all content within a catalog |===========================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteContentById",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteContentById",
     *        summary="Delete content of catalog by id",
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
     *          required={"content_id"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} deleteContentById   deleteContentById
     *
     * @apiName deleteContentById
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
     * "content_id":4 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteContentById(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;

            $detail = DB::select('SELECT 1 FROM static_page_master WHERE FIND_IN_SET('.$content_id.', content_ids)');
            if (count($detail) > 0) {
                $response = Response::json(['code' => 201, 'message' => 'You are trying to delete template which is exist in static page.', 'cause' => '', 'data' => json_decode('{}')]);

                return $response;
            }

            DB::beginTransaction();
            DB::delete('DELETE FROM content_master WHERE id = ? ', [$content_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Content deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteContentById', $e);
            //      Log::error("deleteContentById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getContentByCatalogIdForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getContentByCatalogIdForAdmin",
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
     *          required={"catalog_id"},
     *
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} getContentByCatalogIdForAdmin   getContentByCatalogIdForAdmin
     *
     * @apiName getContentByCatalogIdForAdmin
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
     * "catalog_id":4 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Content fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "content_id": 207,
     * "thumbnail_img": "",
     * "compressed_img": "",
     * "original_img": "",
     * "content_file": "",
     * "svg_file": "http://192.168.0.113/photoadking/image_bucket/svg/5bd2f0309ca3e_normal_image_1540550704.svg",
     * "content_type": 8,
     * "json_data": "",
     * "is_featured": 0,
     * "is_free": 0,
     * "is_portrait": 0,
     * "search_category": ""
     * },
     * {
     * "content_id": 21,
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5badede53bce0_normal_image_1538125285.png",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5badede53bce0_normal_image_1538125285.png",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5badede53bce0_normal_image_1538125285.png",
     * "content_file": "",
     * "svg_file": "",
     * "content_type": 1,
     * "json_data": "",
     * "is_featured": 0,
     * "is_free": 0,
     * "is_portrait": 0,
     * "search_category": ""
     * }
     * ]
     * }
     * }
     */
    public function getContentByCatalogIdForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getContentByCatalogIdForAdmin$this->catalog_id")) {
                $result = Cache::rememberforever("getContentByCatalogIdForAdmin$this->catalog_id", function () {
                    $result = DB::select('SELECT
                                    cm.id AS content_id,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS original_img,
                                    coalesce(cm.content_file,"") AS content_file,
                                    coalesce(cm.image,"") AS svg_file,
                                    cm.content_type,
                                    cm.is_active,
                                    coalesce(cm.json_data,"") AS json_data,
                                    cm.template_name,
                                    coalesce(cm.is_featured,0) AS is_featured,
                                    coalesce(cm.is_free,0) AS is_free,
                                    coalesce(cm.is_portrait,0) AS is_portrait,
                                    coalesce(cm.search_category,"") AS search_category,
                                    coalesce(am.format_name,"") as format_name,
                                    coalesce(am.duration,"") as duration,
                                    coalesce(am.size,0) as size,
                                    coalesce(am.bit_rate,0) as bit_rate,
                                    coalesce(am.genre,"") as genre,
                                    coalesce(am.tag,"") as tag,
                                    coalesce(am.title,"") as title,
                                    coalesce(am.credit_note,"") as credit_note
                                  FROM
                                     content_master as cm
                                     LEFT JOIN audio_master AS am ON cm.id = am.content_id
                                  WHERE
                                    cm.catalog_id = ? AND
                                    isnull(cm.original_img) AND
                                    isnull(cm.display_img)
                                  ORDER BY cm.update_time DESC', [$this->catalog_id]);

                    foreach ($result as $key) {
                        if ($key->json_data != '') {
                            $key->json_data = json_decode($key->json_data);
                        }
                        if ($key->credit_note != '') {
                            $key->credit_note = json_decode($key->credit_note);
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

                    return $result;
                });
            }
            $redis_result = Cache::get("getContentByCatalogIdForAdmin$this->catalog_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Content fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getContentByCatalogIdForAdmin', $e);
            //      Log::error("getContentByCatalogIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* ========================================| Link Catalog |===================================== */

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getAllSubCategoryToLinkCatalog",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getAllSubCategoryToLinkCatalog",
     *        summary="Get all sub categories to link catalog",
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
     *          required={"catalog_id","category_id"},
     *
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="category_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} getAllSubCategoryToLinkCatalog   getAllSubCategoryToLinkCatalog
     *
     * @apiName getAllSubCategoryToLinkCatalog
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
     * "catalog_id":16 //compulsory
     * "category_id":4 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub categories are fetched successfully.",
     * "cause": "",
     * "data": {
     * "category_list": [
     * {
     * "sub_category_id": 8,
     * "sub_category_name": "Announcement Maker",
     * "is_linked": 0
     * },
     * {
     * "sub_category_id": 2,
     * "sub_category_name": "Business Card",
     * "is_linked": 0
     * },
     * {
     * "sub_category_id": 6,
     * "sub_category_name": "Invitation Card Maker",
     * "is_linked": 0
     * },
     * {
     * "sub_category_id": 7,
     * "sub_category_name": "Resume Maker",
     * "is_linked": 0
     * }
     * ]
     * }
     * }
     */
    public function getAllSubCategoryToLinkCatalog(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id', 'category_id'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;
            $this->category_id = $request->category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllSubCategoryToLinkCatalog$this->catalog_id:$this->category_id")) {
                $result = Cache::rememberforever("getAllSubCategoryToLinkCatalog$this->catalog_id:$this->category_id", function () {

                    return DB::select('SELECT
                                sc.id AS sub_category_id,
                                sc.sub_category_name,
                                coalesce(scc.is_active,0) AS is_linked
                              FROM sub_category_master sc
                                LEFT JOIN sub_category_catalog AS scc ON scc.catalog_id = ? AND sc.id = scc.sub_category_id AND scc.is_active = 1
                              WHERE
                                sc.is_active = 1 AND
                                sc.category_id = ?
                              ORDER BY sub_category_name', [$this->catalog_id, $this->category_id]);
                });

            }

            $redis_result = Cache::get("getAllSubCategoryToLinkCatalog$this->catalog_id:$this->category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sub categories are fetched successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllSubCategoryToLinkCatalog', $e);
            //      Log::error("getAllSubCategoryToLinkCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get all sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/linkCatalogToSubCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="linkCatalogToSubCategory",
     *        summary="Link catalog with another sub category",
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
     *          required={"sub_category_id","catalog_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} linkCatalogToSubCategory linkCatalogToSubCategory
     *
     * @apiName linkCatalogToSubCategory
     *
     * @apiGroup Admin
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
     * "sub_category_id":2,
     * "catalog_id":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog linked successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function linkCatalogToSubCategory(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'catalog_id'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $request->sub_category_id;
            $catalog_id = $request->catalog_id;
            $create_time = date('Y-m-d H:i:s');

            $catalog_name = DB::select('SELECT name FROM catalog_master WHERE id = ?', [$catalog_id]);

            if (($response = (new VerificationController())->checkIfCatalogExist($sub_category_id, $catalog_name[0]->name, $catalog_id)) != '') {
                $sub_category_name = DB::select('SELECT sub_category_name FROM sub_category_master WHERE id = ?', [$sub_category_id]);

                return $response = Response::json(['code' => 201, 'message' => '"'.$catalog_name[0]->name.'" already exist in "'.$sub_category_name[0]->sub_category_name.'" category.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $uuid = (new ImageController())->generateUUID();

            DB::beginTransaction();
            DB::insert('INSERT INTO sub_category_catalog(sub_category_id,uuid,catalog_id,create_time) VALUES (?, ?,?, ?)', [$sub_category_id, $uuid, $catalog_id, $create_time]);
            DB::commit();

            $sub_category_name = DB::select('SELECT sub_category_name FROM sub_category_master WHERE id = ?', [$sub_category_id]);
            $tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $sub_category_name[0]->sub_category_name)));
            $templates = DB::select('SELECT
                                      id,
                                      search_category,
                                      update_time
                               FROM
                                   content_master
                               WHERE
                                   catalog_id=?', [$catalog_id]);
            if (count($templates) > 0) {
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

            $response = Response::json(['code' => 200, 'message' => 'Catalog linked successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('linkCatalogToSubCategory', $e);
            //      Log::error("linkCatalogToSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'link catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/unlinkLinkedCatalogFromSubCategory",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="unlinkLinkedCatalogFromSubCategory",
     *        summary="De-link catalog from the sub category",
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
     *          required={"sub_category_id","catalog_id"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} unlinkLinkedCatalogFromSubCategory unlinkLinkedCatalogFromSubCategory
     *
     * @apiName unlinkLinkedCatalogFromSubCategory
     *
     * @apiGroup Admin
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
     * "sub_category_id":2,
     * "catalog_id":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog unlinked successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function unlinkLinkedCatalogFromSubCategory(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'catalog_id'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $sub_category_id = $request->sub_category_id;

            $result = DB::select('SELECT count(*) AS count_catalog FROM sub_category_catalog WHERE catalog_id = ? AND is_active = 1', [$catalog_id]);
            if ($result[0]->count_catalog > 1) {
                DB::beginTransaction();
                DB::delete('DELETE FROM sub_category_catalog WHERE sub_category_id = ? AND catalog_id = ? ', [$sub_category_id, $catalog_id]);
                DB::commit();

                $sub_category_name = DB::select('SELECT sub_category_name FROM sub_category_master WHERE id = ?', [$sub_category_id]);
                $comma_before_tag = ','.implode(',', array_unique(array_filter(explode(',', str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $sub_category_name[0]->sub_category_name)))))));
                $comma_after_tag = implode(',', array_unique(array_filter(explode(',', str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $sub_category_name[0]->sub_category_name))))))).',';
                $templates = DB::select('SELECT
                                      id,
                                      search_category,
                                      update_time
                                 FROM
                                   content_master
                                 WHERE
                                   catalog_id=?', [$catalog_id]);
                if (count($templates) > 0) {
                    foreach ($templates as $row) {
                        $row->search_category = str_replace($comma_before_tag, '', $row->search_category);
                        $row->search_category = str_replace($comma_after_tag, '', $row->search_category);
                        DB::beginTransaction();
                        DB::update('UPDATE content_master
                        SET
                           search_category =?,
                           update_time =?
                        WHERE id=?', [$row->search_category, $row->update_time, $row->id]);
                        DB::commit();
                    }
                }

                $response = Response::json(['code' => 200, 'message' => 'Catalog unlinked successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Unable to de-link this catalog, it is not linked with any other application.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('deleteLinkedCatalog', $e);
            //      Log::error("deleteLinkedCatalog : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete Linked catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ========================================| Move Template |===================================== */

    /**
     * @api {post} moveTemplate moveTemplate
     *
     * @apiName moveTemplate
     *
     * @apiGroup Admin
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
     * "catalog_id":201, //compulsory
     * "template_list":[3386] //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template moved successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function moveTemplate(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id'], $request)) != '') {
                return $response;
            }

            $response = (new VerificationController())->validateRequiredArrayParameter(['template_list'], $request);
            if ($response != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $template_list = $request->template_list;

            $sub_category_detail = DB::select('SELECT
                                              scm.sub_category_name,
                                              cm.name,
                                              cm.is_featured
                                         FROM
                                               sub_category_catalog as sct,
                                               sub_category_master as scm,
                                               catalog_master as cm
                                          WHERE
                                            sct.sub_category_id = scm.id AND
                                            sct.catalog_id = cm.id AND
                                            sct.catalog_id=? ', [$catalog_id]);

            if ($sub_category_detail[0]->is_featured) {
                $tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $sub_category_detail[0]->sub_category_name)));
                $tag .= ','.str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $sub_category_detail[0]->name)));
            } else {
                $tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $sub_category_detail[0]->name)));
            }

            foreach ($template_list as $index => $key) {
                $template_detail = DB::select('SELECT search_category FROM content_master WHERE id=?', [$key]);
                $search_category = $template_detail[0]->search_category;
                if ($search_category != null || $search_category != '') {
                    $search_category .= ','.$tag;
                } else {
                    $search_category = $tag;
                }
                $search_category = implode(',', array_unique(array_filter(explode(',', $search_category))));

                $update_time = date('Y-m-d H:i:s');

                DB::beginTransaction();
                DB::update('UPDATE content_master SET catalog_id = ?,search_category =?,update_time = ? WHERE id = ?', [$catalog_id, $search_category, $update_time, $key]);
                DB::commit();
                sleep(1);
            }

            $response = Response::json(['code' => 200, 'message' => 'Template moved successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('moveTemplate', $e);
            //      Log::error("moveTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'move template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllSubCategoryToMoveTemplate   getAllSubCategoryToMoveTemplate
     *
     * @apiName getAllSubCategoryToMoveTemplate
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
     * "content_id":2696, //compulsory
     * "category_id":2, //optional (If this arg is not pass then it will return sub_categories from all categories)
     * "is_featured":1 //1=featured catalog, 0=normal catalog
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub categories are fetched successfully.",
     * "cause": "",
     * "data": {
     * "sub_category_list": [
     * {
     * "sub_category_id": 37,
     * "sub_category_name": "Flyer",
     * "update_time": "2019-06-08 11:42:17",
     * "catalog_list": [
     * {
     * "catalog_id": 93,
     * "catalog_name": "testing",
     * "is_linked": 0,
     * "update_time": "2019-05-25 12:06:25"
     * },
     * {
     * "catalog_id": 62,
     * "catalog_name": "Valentine",
     * "is_linked": 0,
     * "update_time": "2019-04-29 05:54:21"
     * },
     * {
     * "catalog_id": 3,
     * "catalog_name": "All",
     * "is_linked": 1,
     * "update_time": "2019-04-29 05:54:20"
     * }
     * ]
     * }
     * ]
     * }
     * }
     */
    public function getAllSubCategoryToMoveTemplate(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $this->content_id = $request->content_id;
            $this->category_id = isset($request->category_id) ? $request->category_id : 0;
            $this->is_featured = isset($request->is_featured) ? $request->is_featured : 1; //to identify catalog is_featured or not

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllSubCategoryToMoveTemplate$this->content_id:$this->category_id:$this->is_featured")) {
                $result = Cache::rememberforever("getAllSubCategoryToMoveTemplate$this->content_id:$this->category_id:$this->is_featured", function () {

                    if ($this->category_id != 0) {
                        $sub_categories = DB::select('SELECT
                                              DISTINCT scm.id AS sub_category_id,
                                              scm.sub_category_name,
                                              scm.update_time
                                            FROM sub_category_master scm
                                              LEFT JOIN sub_category_catalog AS scc ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                            WHERE
                                              scm.is_active = 1 AND
                                              scm.is_featured = 1 AND
                                              scm.category_id = ?
                                            ORDER BY scm.id ASC', [$this->category_id]);

                        foreach ($sub_categories as $key) {
                            $catalogs = DB::select('SELECT
                                          DISTINCT scc.catalog_id,
                                          cm.name AS catalog_name,
                                          ifnull ((SELECT 1 FROM content_master AS cm WHERE cm.id = ? AND scc.catalog_id = cm.catalog_id),0) AS is_linked,
                                          cm.update_time
                                        FROM sub_category_catalog AS scc
                                          JOIN catalog_master AS cm
                                            ON cm.id = scc.catalog_id AND
                                               cm.is_active = 1 AND
                                               cm.is_featured = ?
                                        WHERE
                                          scc.is_active = 1 AND
                                          scc.sub_category_id = ?
                                        ORDER BY cm.update_time DESC', [$this->content_id, $this->is_featured, $key->sub_category_id]);

                            $key->catalog_list = $catalogs;

                        }
                    } else {
                        $sub_categories = DB::select('SELECT
                                              DISTINCT scm.id AS sub_category_id,
                                              scm.sub_category_name,
                                              scm.update_time
                                            FROM sub_category_master scm
                                              LEFT JOIN sub_category_catalog AS scc ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                            WHERE
                                              scm.is_active = 1 AND
                                              scm.is_featured = 1
                                            ORDER BY scm.id ASC');

                        foreach ($sub_categories as $key) {
                            $catalogs = DB::select('SELECT
                                          DISTINCT scc.catalog_id,
                                          cm.name AS catalog_name,
                                          ifnull ((SELECT 1 FROM content_master AS cm WHERE cm.id = ? AND scc.catalog_id = cm.catalog_id),0) AS is_linked,
                                          cm.update_time
                                        FROM sub_category_catalog AS scc
                                          JOIN catalog_master AS cm
                                            ON cm.id = scc.catalog_id AND
                                               cm.is_active = 1 AND
                                               cm.is_featured = ?
                                        WHERE
                                          scc.is_active = 1 AND
                                          scc.sub_category_id = ?
                                        ORDER BY cm.update_time DESC', [$this->content_id, $this->is_featured, $key->sub_category_id]);

                            $key->catalog_list = $catalogs;

                        }
                    }

                    return $sub_categories;
                });
            }

            $redis_result = Cache::get("getAllSubCategoryToMoveTemplate$this->content_id:$this->category_id:$this->is_featured");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sub categories are fetched successfully.', 'cause' => '', 'data' => ['sub_category_list' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllSubCategoryToLinkTemplate', $e);
            //      Log::error("getAllSubCategoryToLinkTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* ========================================= Other ==============================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getImageDetails",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getImageDetails",
     *        summary="Get Image Details",
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
     *          @SWG\Property(property="item_count",  type="integer", example=2, description=""),
     *          @SWG\Property(property="order_by",  type="string", example="type", description="optional"),
     *          @SWG\Property(property="order_type",  type="string", example="asc", description="optional"),
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
    /**
     * @api {post} getImageDetails   getImageDetails
     *
     * @apiName getImageDetails
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
     * "page":1,
     * "item_count":2,
     * "order_by":"size",
     * "order_type":"ASC"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Image details fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1324,
     * "image_details": [
     * {
     * "name": "5bbdb718bf24a_template_image_1539159832.jpg",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bbdb718bf24a_template_image_1539159832.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bbdb718bf24a_template_image_1539159832.jpg",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bbdb718bf24a_template_image_1539159832.jpg",
     * "directory_name": "compress",
     * "type": "jpg",
     * "size": 87109,
     * "height": 1050,
     * "width": 600,
     * "create_time": "2018-10-10 08:23:52"
     * },
     * {
     * "name": "5bc4835422b79_my_design_1539605332.jpg",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bc4835422b79_my_design_1539605332.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bc4835422b79_my_design_1539605332.jpg",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bc4835422b79_my_design_1539605332.jpg",
     * "directory_name": "my_design",
     * "type": "jpg",
     * "size": 397820,
     * "height": 1140,
     * "width": 1995,
     * "create_time": "2018-10-15 12:08:52"
     * }
     * ]
     * }
     * }
     */
    public function getImageDetails(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'size';
            $this->order_type = isset($request->order_type) ? $request->order_type : 'DESC';
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM image_details');
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                          id.name,
                                          IF(id.name != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",id.name),"") as thumbnail_img,
                                          IF(id.name != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",id.name),"") as compressed_img,
                                          IF(id.name != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",id.name),"") as original_img,
                                          id.directory_name,
                                          id.type,
                                          id.size,
                                          id.height,
                                          id.width,
                                          id.create_time
                                        FROM
                                          image_details AS id
                                        ORDER BY id.'.$this->order_by.' '.$this->order_type.'
                                        LIMIT ?,?', [$this->offset, $this->item_count]);

                    return ['total_record' => $total_row, 'image_details' => $result];
                });
            }

            $redis_result = Cache::get("getImageDetails$this->page:$this->item_count:$this->order_by:$this->order_type");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Image details fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getImageDetails', $e);
            //      Log::error("getImageDetails : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get image details.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* ====================================| Template |==================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addTemplateImages",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addTemplateImages",
     *        summary="Add normal images",
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
     *          description="Give is_replace in json object",
     *
     *         @SWG\Schema(
     *              required={"is_replace"},
     *
     *              @SWG\Property(property="is_replace",  type="integer", example=1, description="0=do not replace the existing file, 2=replace the existing file"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file[]",
     *         in="formData",
     *         description="array of template images",
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
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Template images added successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to add template images.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    /**
     * @api {post} addTemplateImages addTemplateImages
     *
     * @apiName addTemplateImages
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
     * request_data:{//all parameters are compulsory
     * "category_id":2,
     * "is_replace":0 //0=do not replace the existing file, 2=replace the existing file
     * }
     * file[]:1.jpg //compulsory
     * file[]:2.jpg
     * file[]:3.jpg
     * file[]:4.jpg
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template images added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addTemplateImages(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['is_replace', 'category_id'], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $is_featured = 1; //Here we are passed 1 bcz resource images always uploaded from featured catalogs
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is resource images
            $is_replace = $request->is_replace;
            $invalidations_path = [];
            $is_cdn_error = '';

            if ($request_body->hasFile('file')) {
                $images_array = Input::file('file');

                //To verify all template resource images array
                if (($response = (new ImageController())->verifyImagesArray($images_array, 1, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                if ($is_replace == 0) {
                    if (($response = (new ImageController())->checkIsImageExist($images_array)) != '') {
                        return $response;
                    }
                } else {
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                        foreach ($images_array as $image_array) {
                            $image = $image_array->getClientOriginalName();
                            $image_directory = 'resource';
                            $bucket_name = Config::get('constant.AWS_BUCKET');
                            array_push($invalidations_path, "/$bucket_name/$image_directory/$image");
                        }

                        if ($invalidations_path) {
                            if (($response = (new ImageController())->deleteCDNCache($invalidations_path)) == '') {
                                $is_cdn_error = 'true';
                            }
                        }
                    }
                }

                foreach ($images_array as $image_array) {

                    (new ImageController())->saveResourceImage($image_array);
                    $image = $image_array->getClientOriginalName();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveResourceImageInToS3($image);
                    }
                }
            }

            if ($is_cdn_error) {
                $response = Response::json(['code' => 200, 'message' => 'Template images added successfully. Note: CDN cache not removed.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Template images added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('addTemplateImages', $e);
            //      Log::error("addTemplateImages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add template images.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addTemplate",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addTemplate",
     *        summary="Add template",
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
     *          description="Give catalog_id, json_data, is_portrait, is_featured, is_free & search_category in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","json_data","is_featured","is_free","is_portrait","search_category"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="is_portrait",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="search_category",type="string", example="business", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of template",
     *         required=true,
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Template added successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to add template.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=435,
     *            description="Font does not exist",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":435,"message":"Fonts used by json does not exist in the server.","cause":"","data":{"mismatch_fonts":{},"incorrect_fonts":{{"font_name":"HaginCapsMedium","font_path":"fonts/Hagin Caps Medium.ttf","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0},{"font_name":"ScriptMTBold","font_path":"fonts/SCRIPTBL.TTF","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0}}}}),),
     *        ),
     *      )
     */
    /**
     * @api {post} addTemplate addTemplate
     *
     * @apiName addTemplate
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
     * request_data:{
     * "category_id":1, //compulsory
     * "catalog_id":1, //compulsory
     * "is_featured_catalog":1, //compulsory, 1=featured catalog, 0=normal catalog
     * "is_featured":1, //compulsory
     * "is_free":1, //compulsory
     * "is_portrait":1, //compulsory, 1=portrait, 0=landscape
     * "json_data":{}, //compulsory
     * "search_category":1 //optional
     * }
     * file:1.jpg //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addTemplate(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'catalog_id',
                'is_featured_catalog',
                'is_featured',
                'is_free',
                'is_portrait',
                'is_active',
                'json_data',
                'template_name',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $catalog_id = $request->catalog_id;
            $is_featured_catalog = $request->is_featured_catalog;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template images
            $json_data = $request->json_data;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = $request->is_portrait;
            $is_active = $request->is_active;
            $search_category = isset($request->search_category) ? strtolower($request->search_category) : null;
            $template_name = $request->template_name;
            $create_time = date('Y-m-d H:i:s');
            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');

            if ($search_category != null or $search_category != '') {
                $search_category = $search_category.',';
            }

            if (($response = (new VerificationController())->validateFonts($json_data)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifySampleImage($image_array, $category_id, $is_featured_catalog, $is_catalog)) != '') {
                    return $response;
                }

                if (($response = (new VerificationController())->validateHeightWidthOfSampleImage($image_array, $json_data)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);
                $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($image_array));
                /* if ($tag_list == "" or $tag_list == NULL) {
                   return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarify.com.', 'cause' => '', 'data' => json_decode("{}")));
                 }*/

                if (($response = (new VerificationController())->verifySearchCategory("$search_category$tag_list")) != '') {
                    $response_details = (json_decode(json_encode($response), true));
                    $data = $response_details['original']['data'];
                    $tag_list = $data['search_tags'];
                } else {
                    $tag_list = "$search_category$tag_list";
                }

                $card_image = (new ImageController())->generateNewFileName('template_image', $image_array);
                (new ImageController())->saveOriginalImage($card_image);
                (new ImageController())->saveCompressedImage($card_image);
                (new ImageController())->saveThumbnailImage($card_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($card_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($card_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($card_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                $uuid = (new ImageController())->generateUUID();

                DB::beginTransaction();
                DB::insert('INSERT
                        INTO
                          content_master(
                          uuid,
                          catalog_id,
                          image,
                          webp_image,
                          content_type,
                          json_data,
                          is_free,
                          is_featured,
                          is_portrait,
                          search_category,
                          template_name,
                          height,
                          width,
                          color_value,
                          is_active,
                          create_time)
                        VALUES(?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $uuid,
                    $catalog_id,
                    $card_image,
                    $file_name,
                    $content_type,
                    json_encode($json_data),
                    $is_free,
                    $is_featured,
                    $is_portrait,
                    $tag_list,
                    $template_name,
                    $dimension['height'],
                    $dimension['width'],
                    $color_value,
                    $is_active,
                    $create_time]);
                DB::commit();
            }

            if (strstr($file_name, '.webp')) {
                $response = Response::json(['code' => 200, 'message' => 'Template added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Template added successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('addTemplate', $e);
            //      Log::error("addTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editTemplate",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="editTemplate",
     *        summary="Edit template",
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
     *          description="Give content_id, json_data, is_portrait, is_featured, is_free & search_category in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","json_data","is_featured","is_free","is_portrait","search_category"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="is_portrait",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="search_category",type="string", example="business", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of template",
     *         required=true,
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Template updated successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to edit template.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=435,
     *            description="Font does not exist",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":435,"message":"Fonts used by json does not exist in the server.","cause":"","data":{"mismatch_fonts":{},"incorrect_fonts":{{"font_name":"HaginCapsMedium","font_path":"fonts/Hagin Caps Medium.ttf","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0},{"font_name":"ScriptMTBold","font_path":"fonts/SCRIPTBL.TTF","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0}}}}),),
     *        ),
     *      )
     */
    /**
     * @api {post} editTemplate editTemplate
     *
     * @apiName editTemplate
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
     * request_data:{
     * "category_id":1, //compulsory,
     * "is_featured_catalog":1, //compulsory, 1=featured catalog, 0=normal catalog
     * "content_id":1, //compulsory,
     * "is_featured":1, //compulsory,
     * "is_free":1, //compulsory,
     * "is_portrait":1, //compulsory,
     * "json_data":{}, //compulsory,
     * "search_category":1 //optional
     * }
     * file:1.jpg //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editTemplate(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'is_featured_catalog',
                'content_id',
                'is_featured',
                'is_free',
                'is_portrait',
                'is_active',
                'template_name',
                'json_data',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $is_featured_catalog = $request->is_featured_catalog;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template image
            $content_id = $request->content_id;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = $request->is_portrait;
            $is_active = $request->is_active;
            $search_category = isset($request->search_category) ? strtolower($request->search_category) : null;
            $template_name = $request->template_name;
            $json_data = json_encode($request->json_data);
            $all_json_datas = $request->json_data;

            if ($search_category != null or $search_category != '') {
                if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '') {
                    return $response;
                }
            }

            //check this json is multi-page or single-page
            $is_multipage_json = DB::select('SELECT 1 FROM content_master WHERE id=? AND json_pages_sequence IS NOT NULL', [$content_id]);

            if ($is_multipage_json) {
                foreach ($all_json_datas as $all_json_data) {
                    if (($response = (new VerificationController())->validateFonts($all_json_data)) != '') {
                        return $response;
                    }
                }
            } else {
                if (($response = (new VerificationController())->validateFonts($request->json_data)) != '') {
                    return $response;
                }
            }

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                if (($response = (new ImageController())->verifySampleImage($image_array, $category_id, $is_featured_catalog, $is_catalog)) != '') {
                    return $response;
                }

                if ($is_multipage_json) {
                    foreach ($all_json_datas as $all_json_data) {
                        if (($response = (new VerificationController())->validateHeightWidthOfSampleImage($image_array, $all_json_data)) != '') {
                            return $response;
                        }
                    }
                } else {
                    if (($response = (new VerificationController())->validateHeightWidthOfSampleImage($image_array, $request->json_data)) != '') {
                        return $response;
                    }
                }

                $color_value = (new ImageController())->getRandomColor($image_array);
                $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($image_array));
                /*if ($tag_list == "" or $tag_list == NULL) {
                  return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarify.com.', 'cause' => '', 'data' => json_decode("{}")));
                }*/

                $template_image = (new ImageController())->generateNewFileName('template_image', $image_array);
                (new ImageController())->saveOriginalImage($template_image);
                (new ImageController())->saveCompressedImage($template_image);
                (new ImageController())->saveThumbnailImage($template_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($template_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($template_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($template_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                if (strstr($file_name, '.webp')) {
                    $response = Response::json(['code' => 200, 'message' => 'Template updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
                } else {
                    $response = Response::json(['code' => 200, 'message' => 'Template updated successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            } else {

                $template_image = '';
                $file_name = '';
                $dimension['height'] = '';
                $dimension['width'] = '';
                $color_value = '';
                $tag_list = $search_category;

                $response = Response::json(['code' => 200, 'message' => 'Template updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            DB::beginTransaction();
            DB::update('UPDATE
                      content_master
                    SET
                      image = IF(? != "",?,image),
                      webp_image = IF(? != "",?,webp_image),
                      json_data = IF(? != "",?,json_data),
                      is_free = IF(? != is_free,?,is_free),
                      is_featured = IF(? != is_featured,?,is_featured),
                      is_portrait = IF(? != is_portrait,?,is_portrait),
                      search_category = IF(? != "",?,search_category),
                      template_name = ?,
                      height = IF(? != "",?,height),
                      width = IF(? != "",?,width),
                      color_value = IF(? != "",?,color_value),
                      is_active = ?
                    WHERE
                      id = ? ',
                [$template_image, $template_image,
                    $file_name, $file_name,
                    $json_data, $json_data,
                    $is_free, $is_free,
                    $is_featured, $is_featured,
                    $is_portrait, $is_portrait,
                    $tag_list, $tag_list,
                    $template_name,
                    $dimension['height'], $dimension['height'],
                    $dimension['width'], $dimension['width'],
                    $color_value, $color_value,
                    $is_active,
                    $content_id]);
            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('editTemplate', $e);
            //      Log::error("editTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ====================================| Text |==================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addText",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addText",
     *        summary="Add text josn",
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
     *          description="Give catalog_id, json_data, is_featured & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","json_data","is_featured","is_free"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of text",
     *         required=true,
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Text json added successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to add text.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=435,
     *            description="Font does not exist",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":435,"message":"Fonts used by json does not exist in the server.","cause":"","data":{"mismatch_fonts":{},"incorrect_fonts":{{"font_name":"HaginCapsMedium","font_path":"fonts/Hagin Caps Medium.ttf","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0},{"font_name":"ScriptMTBold","font_path":"fonts/SCRIPTBL.TTF","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0}}}}),),
     *        ),
     *      )
     */
    /**
     * @api {post} addText addText
     *
     * @apiName addText
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "catalog_id":1,
     * "is_featured_catalog":1, //1=featured catalog, 0=normal catalog
     * "is_featured":1,
     * "is_free":1,
     * "json_data":{}
     * }
     * file:1.jpg //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Text json added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addText(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'catalog_id',
                'is_featured_catalog',
                'is_featured',
                'is_free',
                'json_data',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $catalog_id = $request->catalog_id;
            $is_featured_catalog = $request->is_featured_catalog;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template image
            $json_data = json_encode($request->json_data);
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $create_time = date('Y-m-d H:i:s');
            $content_type = Config::get('constant.CONTENT_TYPE_OF_TEXT_JSON');

            if (($response = (new VerificationController())->validateFonts($request->json_data)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifySampleImage($image_array, $category_id, $is_featured_catalog, $is_catalog)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);
                $text_image = (new ImageController())->generateNewFileName('text_image', $image_array);
                (new ImageController())->saveOriginalImage($text_image);
                (new ImageController())->saveCompressedImage($text_image);
                (new ImageController())->saveThumbnailImage($text_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($text_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($text_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($text_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                $uuid = (new ImageController())->generateUUID();
                DB::beginTransaction();
                DB::insert('INSERT
                      INTO
                        content_master(
                        uuid,
                        catalog_id,
                        image,
                        webp_image,
                        content_type,
                        json_data,
                        is_free,
                        is_featured,
                        height,
                        width,
                        color_value,
                        create_time)
                      VALUES(?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?)', [
                    $uuid,
                    $catalog_id,
                    $text_image,
                    $file_name,
                    $content_type,
                    $json_data,
                    $is_free,
                    $is_featured,
                    $dimension['height'],
                    $dimension['width'],
                    $color_value,
                    $create_time]);
                DB::commit();
            }

            if (strstr($file_name, '.webp')) {
                $response = Response::json(['code' => 200, 'message' => 'Text json added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Text json added successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('addText', $e);
            //      Log::error("addText : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add text.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editText",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="editText",
     *        summary="Edit text",
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
     *          description="Give content_id, json_data, is_featured & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","json_data","is_featured","is_free"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="sample image of text",
     *         required=true,
     *         type="file"
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Text json updated successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to edit text.","cause":"Exception message","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=435,
     *            description="Font does not exist",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":435,"message":"Fonts used by json does not exist in the server.","cause":"","data":{"mismatch_fonts":{},"incorrect_fonts":{{"font_name":"HaginCapsMedium","font_path":"fonts/Hagin Caps Medium.ttf","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0},{"font_name":"ScriptMTBold","font_path":"fonts/SCRIPTBL.TTF","correct_font_path":"Font not available","correct_font_name":"Font not available","is_correct_path":0,"is_correct_name":0}}}}),),
     *        ),
     *      )
     */
    /**
     * @api {post} editText editText
     *
     * @apiName editText
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
     * request_data:{//all parameters are compulsory
     * "category_id":1,
     * "is_featured_catalog":1, //1=featured catalog, 0=normal catalog
     * "content_id":1,
     * "is_featured":1,
     * "is_free":1,
     * "json_data":{}
     * }
     * file:1.jpg //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Text json updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editText(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'is_featured_catalog',
                'content_id',
                'is_featured',
                'is_free',
                'json_data',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $is_featured_catalog = $request->is_featured_catalog;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template image
            $content_id = $request->content_id;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $json_data = json_encode($request->json_data);

            if (($response = (new VerificationController())->validateFonts($request->json_data)) != '') {
                return $response;
            }

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifySampleImage($image_array, $category_id, $is_featured_catalog, $is_catalog)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($image_array);
                $text_image = (new ImageController())->generateNewFileName('text_image', $image_array);
                (new ImageController())->saveOriginalImage($text_image);
                (new ImageController())->saveCompressedImage($text_image);
                (new ImageController())->saveThumbnailImage($text_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($text_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($text_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($text_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                if (strstr($file_name, '.webp')) {
                    $response = Response::json(['code' => 200, 'message' => 'Text updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
                } else {
                    $response = Response::json(['code' => 200, 'message' => 'Text updated successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            } else {

                $text_image = '';
                $file_name = '';
                $dimension['height'] = '';
                $dimension['width'] = '';
                $color_value = '';

                $response = Response::json(['code' => 200, 'message' => 'Text updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            DB::beginTransaction();
            DB::update('UPDATE
                      content_master
                    SET
                      image = IF(? != "",?,image),
                      webp_image = IF(? != "",?,webp_image),
                      json_data = IF(? != "",?,json_data),
                      is_free = IF(? != is_free,?,is_free),
                      is_featured = IF(? != is_featured,?,is_featured),
                      height = IF(? != "",?,height),
                      width = IF(? != "",?,width),
                      color_value = IF(? != "",?,color_value)
                    WHERE
                      id = ? ',
                [$text_image, $text_image,
                    $file_name, $file_name,
                    $json_data, $json_data,
                    $is_free, $is_free,
                    $is_featured, $is_featured,
                    $dimension['height'], $dimension['height'],
                    $dimension['width'], $dimension['width'],
                    $color_value, $color_value,
                    $content_id]);
            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('editText', $e);
            //      Log::error("editText : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit text.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ====================================| 3D Object |==================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/add3DObject",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="add3DObject",
     *        summary="Add 3D Object",
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
     *          description="Give catalog_id, json_data (optional), content_type (6=3D text,7=3D shape), is_featured & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","is_featured","is_free","content_type"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="card json"),
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
     *         required=true,
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
     * @api {post} add3DObject add3DObject
     *
     * @apiName add3DObject
     *
     * @apiGroup Admin
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{//all parameters are compulsory
     * "category_id:1,
     * "catalog_id:1,
     * "is_featured_catalog:1, //1=featured catalog, 0=normal catalog
     * "is_featured":1,
     * "is_free":1,
     * "content_type":6 //6=3D text,7=3D shape
     * }
     * file:1.png //compulsory
     * content_file:logo_image.stl //optional when content_type=6
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "3D object added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function add3DObject(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'catalog_id',
                'is_featured_catalog',
                'is_featured',
                'is_free',
                'content_type',
                'is_replace',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $catalog_id = $request->catalog_id;
            $is_featured_catalog = $request->is_featured_catalog;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template image
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $content_type = $request->content_type;
            $is_replace = $request->is_replace;
            $create_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifySampleImage($image_array, $category_id, $is_featured_catalog, $is_catalog)) != '') {
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

                if ($content_type == Config::get('constant.CONTENT_TYPE_OF_3D_TEXT_JSON')) {

                    if (($response = (new VerificationController())->validateRequiredParameter(['json_data'], $request)) != '') {
                        return $response;
                    }

                    $json_data = json_encode($request->json_data);
                    $uuid = (new ImageController())->generateUUID();
                    DB::insert('INSERT
                        INTO
                          content_master(
                          uuid,
                          catalog_id,
                          image,
                          webp_image,
                          content_type,
                          json_data,
                          is_free,
                          is_featured,
                          height,
                          width,
                          color_value,
                          create_time)
                        VALUES(?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?)', [$uuid,
                        $catalog_id,
                        $object_image,
                        $file_name,
                        $content_type,
                        $json_data,
                        $is_free,
                        $is_featured,
                        $dimension['height'],
                        $dimension['width'],
                        $color_value,
                        $create_time]);
                    DB::commit();
                } else {
                    if (! $request_body->hasFile('content_file')) {
                        return Response::json(['code' => 201, 'message' => 'Required field content_file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                    } else {

                        $stl_file_content = Input::file('content_file');
                        if ($is_replace == 0) {
                            if (($response = (new ImageController())->checkStlIsExist($stl_file_content)) != '') {
                                return $response;
                            }
                        }

                        (new ImageController())->unlinkStlFile($stl_file_content);
                        if (($response = (new ImageController())->verifyStlFile($stl_file_content)) != '') {
                            return $response;
                        }

                        $stl_file = $stl_file_content->getClientOriginalName(); //(new ImageController())->generateNewFileName('stl_object', $stl_file_content);
                        $parameter_name = 'content_file';
                        (new ImageController())->saveStlFile($parameter_name, $stl_file);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveStlFileInToS3($stl_file);
                        }
                    }

                    $json_data = isset($request->json_data) ? json_encode($request->json_data) : null;
                    $uuid = (new ImageController())->generateUUID();
                    DB::insert('INSERT
                        INTO
                          content_master(
                          catalog_id,
                          uuid,
                          image,
                          webp_image,
                          content_type,
                          content_file,
                          json_data,
                          is_free,
                          is_featured,
                          height,
                          width,
                          color_value,
                          create_time)
                        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?)', [
                        $catalog_id,
                        $uuid,
                        $object_image,
                        $file_name,
                        $content_type,
                        $stl_file,
                        $json_data,
                        $is_free,
                        $is_featured,
                        $dimension['height'],
                        $dimension['width'],
                        $color_value,
                        $create_time]);
                    DB::commit();
                }
            }

            if (strstr($file_name, '.webp')) {
                $response = Response::json(['code' => 200, 'message' => '3D object added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => '3D object added successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('add3DObject', $e);
            //      Log::error("add3DObject : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add 3D object.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/edit3DObject",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="edit3DObject",
     *        summary="Edit 3D Object",
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
     *          description="Give content_id, json_data (optional), content_type (6=3D text,7=3D shape), is_featured, is_replace & is_free in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","is_featured","is_free","content_type"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="json_data",  type="object", example={}, description="text object json"),
     *              @SWG\Property(property="is_featured",type="integer", example=1, description="1=featured, 0=normal"),
     *              @SWG\Property(property="is_free",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="content_type",type="integer", example=1, description="1=free, 0=paid"),
     *              @SWG\Property(property="is_replace",type="integer", example=0, description="1=replace with existing, 0=don't replace"),
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
     * @api {post} edit3DObject edit3DObject
     *
     * @apiName edit3DObject
     *
     * @apiGroup Admin
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{//all parameters are compulsory
     * "category_id:1,
     * "is_featured_catalog:1, //1=featured catalog, 0=normal catalog
     * "content_id:1,
     * "is_featured":1,
     * "is_free":1,
     * "content_type":6, //6=3D text,7=3D shape
     * "is_replace":0 //1=replace with existing, 0=don't replace"
     * }
     * file:1.png //optional
     * content_file:logo_image.stl //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "3D object updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function edit3DObject(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'is_featured_catalog',
                'content_id',
                'is_featured',
                'is_free',
                'content_type',
                'is_replace',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $is_featured_catalog = $request->is_featured_catalog;
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is tempalte image
            $content_id = $request->content_id;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $content_type = $request->content_type;
            $is_replace = $request->is_replace;

            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_3D_TEXT_JSON')) {
                if (($response = (new VerificationController())->validateRequiredParameter(['json_data'], $request)) != '') {
                    return $response;
                }
            }

            $json_data = json_encode(isset($request->json_data) ? $request->json_data : null);

            //update 3D_object_image
            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');
                if (($response = (new ImageController())->verifySampleImage($image_array, $category_id, $is_featured_catalog, $is_catalog)) != '') {
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

                if (strstr($file_name, '.webp')) {
                    $response = Response::json(['code' => 200, 'message' => '3D object updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
                } else {
                    $response = Response::json(['code' => 200, 'message' => '3D object updated successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            } else {
                $object_image = '';
                $file_name = '';
                $dimension['height'] = '';
                $dimension['width'] = '';
                $color_value = '';
                $response = Response::json(['code' => 200, 'message' => '3D object updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //update .stl file
            if ($request_body->hasFile('content_file')) {
                $stl_file_content = Input::file('content_file');
                if ($is_replace == 0) {
                    if (($response = (new ImageController())->checkStlIsExist($stl_file_content)) != '') {
                        return $response;
                    }
                }

                (new ImageController())->unlinkStlFile($stl_file_content);
                if (($response = (new ImageController())->verifyStlFile($stl_file_content)) != '') {
                    return $response;
                }

                $stl_file = $stl_file_content->getClientOriginalName(); //(new ImageController())->generateNewFileName('stl_object', $stl_file_content);
                $parameter_name = 'content_file';
                (new ImageController())->saveStlFile($parameter_name, $stl_file);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveStlFileInToS3($stl_file);
                }
            } else {
                $stl_file = '';
            }

            DB::beginTransaction();
            DB::update('UPDATE
                      content_master
                    SET
                      image = IF(? != "",?,image),
                      webp_image = IF(? != "",?,webp_image),
                      content_type = IF(? != content_type,?,content_type),
                      content_file = IF(? != "",?,content_file),
                      json_data = IF(? != "",?,json_data),
                      is_free = IF(? != is_free,?,is_free),
                      is_featured = IF(? != is_featured,?,is_featured),
                      height = IF(? != "",?,height),
                      width = IF(? != "",?,width),
                      color_value = IF(? != "",?,color_value)
                    WHERE
                      id = ? ',
                [$object_image, $object_image,
                    $file_name, $file_name,
                    $content_type, $content_type,
                    $stl_file, $stl_file,
                    $json_data, $json_data,
                    $is_free, $is_free,
                    $is_featured, $is_featured,
                    $dimension['height'], $dimension['height'],
                    $dimension['width'], $dimension['width'],
                    $color_value, $color_value,
                    $content_id]);
            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('edit3DObject', $e);
            //      Log::error("edit3DObject : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit 3D object.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =====================================| Advertisement |===================================== */

    /**
     * @api {post} addLink addLink
     *
     * @apiName addLink
     *
     * @apiGroup Admin
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{//all parameters are compulsory
     * "name":"Announcement Card Maker",
     * "url":"https://play.google.com/store/apps/details?id=com.nra.announcementmaker",
     * "platform":"Android",
     * "app_description":"This is test description."
     * }
     * file:ob.png //compulsory
     * logo_file:logo_image.png //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement link added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addLink(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['name', 'url', 'platform', 'app_description'], $request)) != '') {
                return $response;
            }

            $name = $request->name;
            $url = $request->url;
            $platform = $request->platform;
            $app_description = $request->app_description;
            $create_at = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->checkIfAdvertisementExist($url, $platform)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $image_array = Input::file('file');

                /* Here we passed value following parameters as 0 bcs we use common validation for advertise images
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($image_array, 0, 0, 0)) != '') {
                    return $response;
                }

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                (new ImageController())->saveOriginalImage($app_image);
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);

                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($app_image);
                }
            }

            //Application logo image
            if (! $request_body->hasFile('logo_file')) {
                return Response::json(['code' => 201, 'message' => 'Required field logo_file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $logo_image_array = Input::file('logo_file');

                /* Here we passed value following parameters as 0 bcs we use common validation for advertise images
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($logo_image_array, 0, 0, 0)) != '') {
                    return $response;
                }

                $app_logo = (new ImageController())->generateNewFileName('app_logo_image', $logo_image_array);
                (new ImageController())->saveMultipleOriginalImage($app_logo, 'logo_file');
                (new ImageController())->saveCompressedImage($app_logo);
                (new ImageController())->saveThumbnailImage($app_logo);

                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($app_logo);
                }
            }

            $data = ['name' => $name,
                'image' => $app_image, //Application banner
                'app_logo_img' => $app_logo, //app_logo,
                'url' => $url,
                'platform' => $platform,
                'app_description' => $app_description,
                'create_time' => $create_at];

            DB::beginTransaction();
            DB::table('advertise_links')->insertGetId($data);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Advertisement link added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addLink', $e);
            //      Log::error("addLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add link.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} updateLink updateLink
     *
     * @apiName updateLink
     *
     * @apiGroup Admin
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
     * "advertise_link_id": 51,
     * "name": "QR Scanner",
     * "url": "https://play.google.com/store/apps/details?id=com.optimumbrewlab.dqnentrepreneur&hl=en",
     * "platform": "Android",
     * "app_description": "This is test description"
     * }
     * file:ob.png //optional
     * logo_file:ob.png //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement link updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateLink(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['advertise_link_id', 'name', 'platform', 'url', 'app_description'], $request)) != '') {
                return $response;
            }

            $advertise_link_id = $request->advertise_link_id;
            $name = $request->name;
            $url = $request->url;
            $platform = $request->platform;
            $app_description = $request->app_description;
            $create_at = date('Y-m-d H:i:s');
            $image_name = '';
            $logo_image_name = '';

            DB::beginTransaction();
            if ((! $request_body->hasFile('file')) && (! $request_body->hasFile('logo_file'))) {

                DB::update('UPDATE advertise_links
                      SET name = ?,
                          url = ?,
                          platform = ?,
                          app_description = ?
                      WHERE
                          id = ?', [$name, $url, $platform, $app_description, $advertise_link_id]);
            } elseif ((! $request_body->hasFile('logo_file')) && $request_body->hasFile('file')) {
                $image_array = Input::file('file');

                /* Here we passed value following parameters as 0 bcs we use common validation for advertise images
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($image_array, 0, 0, 0)) != '') {
                    return $response;
                }

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                (new ImageController())->saveMultipleOriginalImage($app_image, 'file');
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);

                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($app_image);
                }

                $result = DB::select('SELECT image FROM advertise_links WHERE id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;

                DB::update('UPDATE advertise_links
                      SET name = ?,
                          image = ?,
                          url = ?,
                          platform = ?,
                          app_description = ?
                      WHERE
                          id = ?', [$name, $app_image, $url, $platform, $app_description, $advertise_link_id]);

            } elseif ($request_body->hasFile('logo_file') && (! $request_body->hasFile('file'))) {
                $image_array = Input::file('logo_file');

                /* Here we passed value following parameters as 0 bcs we use common validation for advertise images
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($image_array, 0, 0, 0)) != '') {
                    return $response;
                }

                $logo_image = (new ImageController())->generateNewFileName('app_logo_image', $image_array);
                (new ImageController())->saveMultipleOriginalImage($logo_image, 'logo_file');
                (new ImageController())->saveCompressedImage($logo_image);
                (new ImageController())->saveThumbnailImage($logo_image);

                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($logo_image);
                }

                $result = DB::select('SELECT app_logo_img FROM advertise_links WHERE id = ?', [$advertise_link_id]);
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('UPDATE advertise_links
                      SET name = ?,
                          app_logo_img = ?,
                          url = ?,
                          platform = ?,
                          app_description = ?,
                          create_time = ?
                      WHERE
                          id = ?', [$name, $logo_image, $url, $platform, $app_description, $create_at, $advertise_link_id]);

            } else {
                $image_array = Input::file('file');
                $logo_image_array = Input::file('logo_file');

                /* Here we passed value following parameters as 0 bcs we use common validation for advertise images
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($image_array, 0, 0, 0)) != '') {
                    return $response;
                }

                if (($response = (new ImageController())->verifyImage($logo_image_array, 0, 0, 0)) != '') {
                    return $response;
                }

                $app_image = (new ImageController())->generateNewFileName('banner_image', $image_array);
                $logo_image = (new ImageController())->generateNewFileName('app_logo_image', $logo_image_array);
                (new ImageController())->saveOriginalImage($app_image);
                (new ImageController())->saveCompressedImage($app_image);
                (new ImageController())->saveThumbnailImage($app_image);
                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($app_image);
                }
                (new ImageController())->saveMultipleOriginalImage($logo_image, 'logo_file');
                (new ImageController())->saveCompressedImage($logo_image);
                (new ImageController())->saveThumbnailImage($logo_image);

                if (Config::get('constant.APP_ENV') != 'local') {
                    (new ImageController())->saveImageInToS3($logo_image);
                }

                $result = DB::select('SELECT image,app_logo_img FROM advertise_links WHERE id = ?', [$advertise_link_id]);
                $image_name = $result[0]->image;
                $logo_image_name = $result[0]->app_logo_img;

                DB::update('UPDATE advertise_links
                      SET name = ?,
                          image = ?,
                          app_logo_img = ?,
                          url = ?,
                          platform = ?,
                          app_description = ?,
                          create_time = ?
                      WHERE
                          id = ?', [$name, $app_image, $logo_image, $url, $platform, $app_description, $create_at, $advertise_link_id]);
            }
            DB::commit();

            //Delete image from storage
            if ($image_name) {
                (new ImageController())->deleteImage($image_name);
            }
            if ($logo_image_name) {
                (new ImageController())->deleteImage($logo_image_name);
            }

            $response = Response::json(['code' => 200, 'message' => 'Advertisement link updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('updateLink', $e);
            //      Log::error("updateLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update link.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteLink deleteLink
     *
     * @apiName deleteLink
     *
     * @apiGroup Admin
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
     *  "advertise_link_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement link deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteLink(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['advertise_link_id'], $request)) != '') {
                return $response;
            }

            $advertise_link_id = $request->advertise_link_id;

            DB::beginTransaction();
            //DB::delete('delete FROM sub_category_advertise_links WHERE advertise_link_id = ?', [$advertise_link_id]);
            DB::delete('DELETE FROM advertise_links WHERE id = ? ', [$advertise_link_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Advertisement link deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteLink', $e);
            //      Log::error("deleteLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete link.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllAdvertisements   getAllAdvertisements
     *
     * @apiName getAllAdvertisements
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
     * "page":1,
     * "item_count":2
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All advertisements fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "is_next_page": false,
     * "result": [
     * {
     * "advertise_link_id": 1,
     * "name": "Announcement Card Maker",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdea6ca70155_banner_image_1541318346.png",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdea6ca70155_banner_image_1541318346.png",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdea6ca70155_banner_image_1541318346.png",
     * "app_logo_thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "app_logo_compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "app_logo_original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "url": "https://play.google.com/store/apps/details?id=com.nra.announcementmaker",
     * "platform": "Android",
     * "app_description": "This is test description."
     * }
     * ]
     * }
     * }
     */
    public function getAllAdvertisements(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllAdvertisements$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getAllAdvertisements$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) AS total FROM  advertise_links WHERE is_active = ?', [1]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                    adl.id AS advertise_link_id,
                                    adl.name,
                                    IF(adl.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS thumbnail_img,
                                    IF(adl.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS compressed_img,
                                    IF(adl.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS original_img,
                                    IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_thumbnail_img,
                                    IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_compressed_img,
                                    IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_original_img,
                                    adl.url,
                                    adl.platform,
                                    if(adl.app_description!="",adl.app_description,"") AS app_description
                                  FROM
                                    advertise_links AS adl
                                  WHERE
                                    is_active = 1
                                  ORDER BY adl.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];

                });
            }

            $redis_result = Cache::get("getAllAdvertisements$this->page:$this->item_count");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All advertisements fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllAdvertisements', $e);
            //      Log::error("getAllAdvertisements : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all advertisements.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getAllLink   getAllLink
     *
     * @apiName getAllLink
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
     * "sub_category_id":1, //compulsory
     * "page":1, //compulsory
     * "item_count":10 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All linked advertisements fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 1,
     * "is_next_page": false,
     * "link_list": [
     * {
     * "advertise_link_id": 1,
     * "name": "Announcement Card Maker",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdea6ca70155_banner_image_1541318346.png",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdea6ca70155_banner_image_1541318346.png",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdea6ca70155_banner_image_1541318346.png",
     * "app_logo_thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "app_logo_compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "app_logo_original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "url": "https://play.google.com/store/apps/details?id=com.nra.announcementmaker",
     * "platform": "Android",
     * "app_description": "This is test description."
     * }
     * ]
     * }
     * }
     */
    public function getAllLink(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllLink$this->sub_category_id:$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getAllLink$this->sub_category_id:$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) AS total FROM  sub_category_advertise_links WHERE is_active = ? AND sub_category_id = ?', [1, $this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                    adl.id AS advertise_link_id,
                                    adl.name,
                                    IF(adl.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS thumbnail_img,
                                    IF(adl.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS compressed_img,
                                    IF(adl.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS original_img,
                                    IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_thumbnail_img,
                                    IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_compressed_img,
                                    IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_original_img,
                                    adl.url,
                                    adl.platform,
                                    if(adl.app_description!="",adl.app_description,"") AS app_description
                                  FROM
                                    advertise_links AS adl,
                                    sub_category_advertise_links AS sadl
                                  WHERE
                                    sadl.sub_category_id = ? AND
                                    sadl.advertise_link_id = adl.id AND
                                    sadl.is_active = 1
                                  ORDER BY adl.update_time DESC
                                  LIMIT ?,?', [$this->sub_category_id, $this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'link_list' => $result];

                });
            }
            $redis_result = Cache::get("getAllLink$this->sub_category_id:$this->page:$this->item_count");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All linked advertisements fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllLink', $e);
            //      Log::error("getAllLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all links.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getAllAdvertisementToLinkAdvertisement   getAllAdvertisementToLinkAdvertisement
     *
     * @apiName getAllAdvertisementToLinkAdvertisement
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
     * "sub_category_id":4 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Advertisements fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "advertise_link_id": 1,
     * "name": "Announcement Card Maker",
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdea6ca70155_banner_image_1541318346.png",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdea6ca70155_banner_image_1541318346.png",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdea6ca70155_banner_image_1541318346.png",
     * "app_logo_thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "app_logo_compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "app_logo_original_img": "http://192.168.0.113/photoadking/image_bucket/original/5bdea7ad0e6ae_app_logo_image_1541318573.png",
     * "url": "https://play.google.com/store/apps/details?id=com.nra.announcementmaker",
     * "platform": "Android",
     * "app_description": "This is test description.",
     * "linked": 0
     * }
     * ]
     * }
     * }
     */
    public function getAllAdvertisementToLinkAdvertisement(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllAdvertisementToLinkAdvertisement$this->sub_category_id")) {
                $result = Cache::rememberforever("getAllAdvertisementToLinkAdvertisement$this->sub_category_id", function () {

                    return DB::select('SELECT
                                adl.id AS advertise_link_id,
                                adl.name,
                                IF(adl.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS thumbnail_img,
                                IF(adl.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS compressed_img,
                                IF(adl.image != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.image),"") AS original_img,
                                IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_thumbnail_img,
                                IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_compressed_img,
                                IF(adl.app_logo_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",adl.app_logo_img),"") AS app_logo_original_img,
                                adl.url,
                                adl.platform,
                                if(adl.app_description!="",adl.app_description,"") AS app_description,
                                IF((SELECT sub_category_id
                                    FROM sub_category_advertise_links scc
                                    WHERE sub_category_id = ? AND adl.id = scc.advertise_link_id AND scc.is_active = 1 LIMIT 1) ,1,0) AS linked
                              FROM
                                advertise_links AS adl
                              WHERE
                                adl.is_active = 1
                              ORDER BY adl.update_time DESC', [$this->sub_category_id]);
                });
            }

            $redis_result = Cache::get("getAllAdvertisementToLinkAdvertisement$this->sub_category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Advertisements fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllAdvertisementToLinkAdvertisement', $e);
            //      Log::error("getAllAdvertisementToLinkAdvertisement : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all advertisements.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} linkAdvertisementWithSubCategory linkAdvertisementWithSubCategory
     *
     * @apiName linkAdvertisementWithSubCategory
     *
     * @apiGroup Admin
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
     * "advertise_link_id":57,
     * "sub_category_id":47
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement linked successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function linkAdvertisementWithSubCategory(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['advertise_link_id', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;
            $create_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::insert('INSERT INTO sub_category_advertise_links(sub_category_id,advertise_link_id,create_time) VALUES (?, ?, ?)', [$sub_category_id, $advertise_link_id, $create_time]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Advertisement linked successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('linkAdvertisementWithSubCategory', $e);
            //      Log::error("linkAdvertisementWithSubCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'link advertisement', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteLinkedAdvertisement deleteLinkedAdvertisement
     *
     * @apiName deleteLinkedAdvertisement
     *
     * @apiGroup Admin
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
     * "advertise_link_id":57, //compulsory
     * "sub_category_id":47 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Advertisement unlinked successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteLinkedAdvertisement(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['advertise_link_id', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $advertise_link_id = $request->advertise_link_id;
            $sub_category_id = $request->sub_category_id;

            DB::beginTransaction();
            DB::delete('DELETE FROM sub_category_advertise_links WHERE sub_category_id = ? AND advertise_link_id = ? ', [$sub_category_id, $advertise_link_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Advertisement unlinked successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('deleteLinkedAdvertisement', $e);
            //      Log::error("deleteLinkedAdvertisement : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete linked advertisement.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* ========================================= Tags =========================================*/

    /**
     * @api {post} addTag   addTag
     *
     * @apiName addTag
     *
     * @apiGroup Admin
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
     * "tag_name":"Nature" //compulsory
     * "sub_category_id":12
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['tag_name'], $request)) != '') {
                return $response;
            }

            $tag_name = trim($request->tag_name);
            $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
            $create_at = date('Y-m-d H:i:s');
            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');

            $tag_name = trim($tag_name, '%');
            $tag_name = trim(preg_replace('/[^A-Za-z ]/', '', $tag_name));
            if (strlen($tag_name) >= 100) {
                return Response::json(['code' => 201, 'message' => 'Invalid tag please enter valid tag.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $result = DB::select('SELECT * FROM tag_master WHERE tag_name = ?', [$tag_name]);
            if (count($result) > 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Tag already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $total_row_result = DB::select('SELECT
                                        count(*) AS total
                                      FROM
                                        content_master as cm
                                      WHERE
                                        cm.is_active = 1 AND
                                        isnull(cm.original_img) AND
                                        isnull(cm.display_img) AND
                                        cm.content_type IN('.$content_type.') AND
                                        (MATCH(cm.search_category) AGAINST("'.$tag_name.'") OR
                                         MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$tag_name.'"," ")," ","* ")  IN BOOLEAN MODE))');
            $total_row = $total_row_result[0]->total;

            if ($total_row == 0) {
                return Response::json(['code' => 201, 'message' => 'Templates do not exist having this tag.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            DB::beginTransaction();

            DB::insert('insert into tag_master (tag_name,is_active,sub_category_id,create_time) VALUES(?,?,?, ?)', [$tag_name, 1, $sub_category_id, $create_at]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Tag added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addTag', $e);
            //      Log::error("addTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} updateTag   updateTag
     *
     * @apiName updateTag
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
     * "tag_id":1, //compulsory
     * "tag_name":"Featured" //compulsory
     * "sub_category_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateTag(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['tag_id', 'tag_name'], $request)) != '') {
                return $response;
            }

            $tag_id = $request->tag_id;
            $sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
            $tag_name = trim($request->tag_name);

            $result = DB::select('SELECT * FROM tag_master WHERE tag_name = ? AND id != ?', [$tag_name, $tag_id]);
            if (count($result) > 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Tag already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');

            $total_row_result = DB::select('SELECT
                                        count(*) AS total
                                      FROM
                                        content_master as cm
                                      WHERE
                                        cm.is_active = 1 AND
                                        isnull(cm.original_img) AND
                                        isnull(cm.display_img) AND
                                        cm.content_type IN('.$content_type.') AND
                                        (MATCH(cm.search_category) AGAINST("'.$tag_name.'") OR
                                         MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$tag_name.'"," ")," ","* ")  IN BOOLEAN MODE))');
            $total_row = $total_row_result[0]->total;

            if ($total_row == 0) {
                return Response::json(['code' => 201, 'message' => 'Templates do not exist having this tag.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $tag_detail = DB::select('SELECT tag_name FROM tag_master WHERE id = ?', [$tag_id]);
            $tag = $tag_detail[0]->tag_name;

            if ($tag !== $tag_name) {
                DB::beginTransaction();
                DB::update('UPDATE
                      tag_master
                    SET
                      tag_name = ?,
                      content_ids = NULL,
                      sub_category_id = IF(? != NULL,?,sub_category_id)
                    WHERE
                      id = ? ',
                    [$tag_name, $sub_category_id, $sub_category_id, $tag_id]);
                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Tag updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateTag', $e);
            //      Log::error("updateTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteTag   deleteTag
     *
     * @apiName deleteTag
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
     * "tag_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteTag(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['tag_id'], $request)) != '') {
                return $response;
            }

            $tag_id = $request->tag_id;

            DB::beginTransaction();

            DB::delete('DELETE FROM tag_master where id = ? ', [$tag_id]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Tag deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteTag', $e);
            //      Log::error("deleteTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllTags   getAllTags
     *
     * @apiName getAllTags
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
     * "sub_catgeory_id" : 12 //Optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All tags fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 4,
     * "result": [
     * {
     * "tag_id": 1,
     * "tag_name": "test"
     * },
     * {
     * "tag_id": 2,
     * "tag_name": "Offer & Sales"
     * },
     * {
     * "tag_id": 3,
     * "tag_name": "Mobile Apps"
     * },
     * {
     * "tag_id": 4,
     * "tag_name": "Photography"
     * }
     * ]
     * }
     * }
     */
    public function getAllTags(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $this->sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
            if ($this->sub_category_id) {
                $this->where = 'AND sub_category_id = '.$this->sub_category_id;
            } else {
                $this->where = '';
            }

            $this->content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');

            $redis_result = Cache::rememberforever("getAllTags:$this->sub_category_id", function () {

                $tag_list = DB::select('SELECT
                                          id AS tag_id,
                                          tag_name,
                                          content_ids
                                          FROM
                                          tag_master
                                          WHERE is_active = ? '.$this->where.' ORDER BY update_time DESC', [1]);
                foreach ($tag_list as $key) {
                    $total_row_result = DB::select('SELECT
                                        count(*) AS total
                                      FROM
                                        content_master as cm
                                      WHERE
                                        cm.is_active = 1 AND
                                        isnull(cm.original_img) AND
                                        isnull(cm.display_img) AND
                                        cm.content_type IN('.$this->content_type.') AND
                                        (MATCH(cm.search_category) AGAINST("'.$key->tag_name.'") OR
                                         MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$key->tag_name.'"," ")," ","* ")  IN BOOLEAN MODE))');

                    $key->total_template = $total_row_result[0]->total;
                }

                return $tag_list;

            });

            $response = Response::json(['code' => 200, 'message' => 'All tags fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllTags', $e);
            //Log::error("getAllTags : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get all tags.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} setTagRankOnTheTopByAdmin setTagRankOnTheTopByAdmin
     *
     * @apiName setTagRankOnTheTopByAdmin
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
     * "tag_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setTagRankOnTheTopByAdmin(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['tag_id'], $request)) != '') {
                return $response;
            }

            $tag_id = $request->tag_id;
            $current_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::update('UPDATE
                    tag_master
                  SET update_time = ?
                  WHERE
                    id = ?', [$current_time, $tag_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setTagRankOnTheTopByAdmin', $e);
            //      Log::error("setTagRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set rank.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*=============================================| Users |=================================================*/

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getDesignFolderForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getDesignFolderForAdmin",
     *        summary="get User Design Folder list",
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
     *          required={"page","item_count","user_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *          @SWG\Property(property="user_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
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
    /**
     * @api {post} getDesignFolderForAdmin   getDesignFolderForAdmin
     *
     * @apiName getDesignFolderForAdmin
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
     * "page":1,
     * "item_count":10
     * "user_id":215
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Images fetched successfully.",
     * "cause": "",
     * "data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}},
     * }
     */
    public function getDesignFolderForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'user_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = 1;
            $this->user_id = $request->user_id;

            $redis_result = Cache::rememberforever("getDesignFolderForAdmin:$this->user_id:$this->page:$this->item_count", function () {

                $get_user_image_design = (new UserController())->getDesign($this->user_id, $this->content_type, $this->offset, $this->item_count);

                return $get_user_image_design;

            });

            $response = Response::json(['code' => 200, 'message' => 'User images successfully retrieved.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getDesignFolderForAdmin', $e);
            //Log::error("getDesignFolderForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getVideoDesignFolderForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getVideoDesignFolderForAdmin",
     *        summary="get User Video Design Folder list",
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
     *          required={"page","item_count","user_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *          @SWG\Property(property="user_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Video fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
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
    /**
     * @api {post} getVideoDesignFolderForAdmin   getVideoDesignFolderForAdmin
     *
     * @apiName getVideoDesignFolderForAdmin
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
     * "page":1,
     * "item_count":10
     * "user_id":215
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code":200,"message":"Video fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}
     * }
     */
    public function getVideoDesignFolderForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'user_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = 2;
            $this->user_id = $request->user_id;

            $redis_result = Cache::rememberforever("getVideoDesignFolderForAdmin:$this->user_id:$this->page:$this->item_count", function () {

                $get_user_video_design = (new UserController())->getDesign($this->user_id, $this->content_type, $this->offset, $this->item_count);

                return $get_user_video_design;

            });

            $response = Response::json(['code' => 200, 'message' => 'User video design successfully obtained.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getVideoDesignFolderForAdmin', $e);
            //Log::error("getVideoDesignFolderForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getIntroDesignFolderForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getIntroDesignFolderForAdmin",
     *        summary="get User intro Design Folder list",
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
     *          required={"page","item_count","user_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *          @SWG\Property(property="user_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Intro fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}}, description=""),),
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
    /**
     * @api {post} getIntroDesignFolderForAdmin   getIntroDesignFolderForAdmin
     *
     * @apiName getIntroDesignFolderForAdmin
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
     * "page":1,
     * "item_count":10
     * "user_id":215
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code":200,"message":"Intro fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"folder_result":{{"folder_id":4,"user_id":3,"folder_name":"third_folder","my_design_ids":"1,2,3,4","update_time":"2019-04-23 09:45:09"}},"image_result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}},"recommended_templates":{{"content_id":2445,"sub_category_id":37,"catalog_id":3,"sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/thumbnail/5c8b470a80e2b_template_image_1552631562.jpg","is_featured":"1","content_type":4,"is_free":1,"is_portrait":0,"height":300,"width":525,"color_value":"#fffffd","update_time":"2019-03-16 05:14:51"}}}
     * }
     */
    public function getIntroDesignFolderForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'user_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = 3;
            $this->user_id = $request->user_id;

            $redis_result = Cache::rememberforever("getIntroDesignFolderForAdmin:$this->user_id:$this->page:$this->item_count", function () {

                $get_user_intro_design = (new UserController())->getDesign($this->user_id, $this->content_type, $this->offset, $this->item_count);

                return $get_user_intro_design;

            });

            $response = Response::json(['code' => 200, 'message' => 'User intro / video design successfully received.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getIntroDesignFolderForAdmin', $e);
            //Log::error("getIntroDesignFolderForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getDesignByFolderIdForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getDesignByFolderIdForAdmin",
     *        summary="get User Design By Folder_Id",
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
     *          required={"page","item_count","folder_id","user_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=4, description=""),
     *          @SWG\Property(property="folder_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="user_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Designs fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}}}}, description=""),),
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
    /**
     * @api {post} getDesignByFolderIdForAdmin   getDesignByFolderIdForAdmin
     *
     * @apiName getDesignByFolderIdForAdmin
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
     * "page":1,
     * "item_count":10
     * "user_id":215
     * "folder_id":"2e98g04bea3ee9"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code":200,"message":"Designs fetched successfully.","cause":"","data":{"total_record":43,"is_next_page":true,"result":{{"my_design_id":470,"user_id":23,"sub_category_id":37,"user_template_name":"my family","sample_image":"http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5cbedea50e8c9_my_design_1556012709.jpg","color_value":"#8547e8","update_time":"2019-04-23 09:45:09"}}}
     * }
     */
    public function getDesignByFolderIdForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'folder_id', 'user_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $this->folder_id = $request->folder_id;
            $this->user_id = $request->user_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getDesignByFolderIdForAdmin$this->user_id:$this->page:$this->item_count:$this->folder_id")) {
                $result = Cache::rememberforever("getDesignByFolderIdForAdmin$this->user_id:$this->page:$this->item_count:$this->folder_id", function () {

                    $get_user_design_folder = (new UserController())->getDesignFolder($this->user_id, $this->folder_id, $this->offset, $this->item_count);

                    return $get_user_design_folder;

                });
            }

            $redis_result = Cache::get("getDesignByFolderIdForAdmin$this->user_id:$this->page:$this->item_count:$this->folder_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Designs fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getDesignByFolderIdForAdmin', $e);
            //      Log::error("getDesignByFolderIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get design from folder.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllUsersByAdmin getAllUsersByAdmin
     *
     * @apiName getAllUsersByAdmin
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
     * "page":1,
     * "item_count":1,
     * "order_by":"first_name", //optional
     * "order_type":"asc" //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Users fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 9,
     * "is_next_page": true,
     * "result": [
     * {
     * "user_id": 9,
     * "user_name": "alagiyanirav@gmail.com",
     * "first_name": "Alagiya Nirav",
     * "email_id": "alagiyanirav@gmail.com",
     * "thumbnail_img": "",
     * "compressed_img": "",
     * "original_img": "",
     * "social_uid": "",
     * "signup_type": 0,
     * "profile_setup": 1,
     * "is_active": 1,
     * "is_verify": 1,
     * "role_id": 2,
     * "device_model_name": "Firefox",
     * "device_os_version": "windows-7",
     * "device_platform": "Windows",
     * "create_time": "2018-12-05 09:18:04",
     * "update_time": "2018-12-05 09:18:05"
     * }
     * ],
     * "role_list": [
     * {
     * "role_id": 1,
     * "role_name": "admin"
     * },
     * {
     * "role_id": 2,
     * "role_name": "free_user"
     * },
     * {
     * "role_id": 3,
     * "role_name": "monthly_starter"
     * },
     * {
     * "role_id": 4,
     * "role_name": "yearly_starter"
     * },
     * {
     * "role_id": 5,
     * "role_name": "monthly_pro"
     * },
     * {
     * "role_id": 6,
     * "role_name": "yearly_pro"
     * },
     * {
     * "role_id": 7,
     * "role_name": "premium_user"
     * }
     * ]
     * }
     * }
     */
    public function getAllUsersByAdminBackup(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $this->order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->device_info = 'COALESCE(dm.device_platform	,"") as device_platform,
                            COALESCE(dm.device_model_name	,"") as device_model_name,
                            COALESCE(dm.device_os_version	,"") as device_os_version,
                            COALESCE(dm.device_type	,"") as device_type,
                            dm.device_registered_from AS ip_address_type,
                            COALESCE(dm.ip_address	,"0.0.0.0") AS ip_address,
                            dm.create_time AS update_time';

            if ($this->order_by == 'first_name' or $this->order_by == 'profile_img') {
                $this->table_prefix = 'ud';
            } elseif ($this->order_by == 'role_id') {
                $this->table_prefix = 'ru';
            } else {
                if ($this->order_by == 'user_id') {
                    $this->order_by = 'id';
                }
                $this->table_prefix = 'um';
            }

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllUsersByAdmin$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getAllUsersByAdmin$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM user_master as um,
                                                user_detail as ud
                                                where um.id = ud.user_id AND
                                                NOT um.id= 1');
                    $total_row = $total_row_result[0]->total;

                    if ($this->order_by == '' && $this->order_type == '') {

                        $result = DB::select('select
                                                    um.id as user_id,
                                                    COALESCE(um.user_name,"")as user_name,
                                                    COALESCE(ud.first_name,"")as first_name,
                                                    COALESCE(um.email_id,"")as email_id,
                                                    IF(ud.profile_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as thumbnail_img,
                                                    IF(ud.profile_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as compressed_img,
                                                    IF(ud.profile_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as original_img,
                                                    COALESCE(um.social_uid,"") as social_uid,
                                                    COALESCE(um.signup_type,0) as signup_type,
                                                    COALESCE(um.profile_setup,0) as profile_setup,
                                                    COALESCE(um.is_active,0) as is_active,
                                                    COALESCE(um.is_verify,0) as is_verify,
                                                    ru.role_id,
                                                    um.create_time,
                                                    um.update_time
                                                   FROM
                                                    user_master um LEFT JOIN role_user AS ru ON um.id = ru.user_id , user_detail AS ud
                                                   WHERE
                                                    um.id != 1 AND
                                                    um.id=ud.user_id ORDER BY ru.role_id != '.Config::get('constant.ROLE_ID_FOR_FREE_USER').' DESC, um.create_time DESC LIMIT ?,?', [$this->offset, $this->item_count]);

                        foreach ($result as $i => $data) {
                            $result[$i]->address_detail = DB::select('SELECT
                                                            COALESCE(full_name,"") AS full_name,
                                                            COALESCE(address,"") AS address,
                                                            COALESCE(country,"") AS country,
                                                            COALESCE(state,"") AS state,
                                                            COALESCE(city,"") AS city,
                                                            COALESCE(zip_code,"") AS zip_code,
                                                            COALESCE(is_active,0)  AS is_active
                                                          FROM billing_master WHERE user_id=?', [$data->user_id]);

                            $signup_device_detail = DB::select('SELECT
                                              '.$this->device_info.'
                                        FROM device_master AS dm
                                               WHERE dm.user_id=?
                                               AND dm.device_registered_from=0
                                        ORDER BY dm.create_time DESC LIMIT 1', [$data->user_id]);

                            $login_device_detail = DB::select('SELECT
                                              '.$this->device_info.'
                                        FROM device_master AS dm RIGHT JOIN user_session AS us ON us.id=dm.user_session_id AND us.is_active=1
                                               WHERE dm.user_id=?
                                               AND dm.device_registered_from=1
                                        ORDER BY dm.create_time DESC', [$data->user_id]);

                            foreach ($login_device_detail as $j => $detail) {
                                array_push($signup_device_detail, $detail);
                            }
                            $result[$i]->device_detail = $signup_device_detail;
                        }

                    } else {

                        $result = DB::select('select
                                                    um.id as user_id,
                                                    COALESCE(um.user_name,"")as user_name,
                                                    COALESCE(ud.first_name,"")as first_name,
                                                    COALESCE(um.email_id,"")as email_id,
                                                    IF(ud.profile_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as thumbnail_img,
                                                    IF(ud.profile_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as compressed_img,
                                                    IF(ud.profile_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as original_img,
                                                    COALESCE(um.social_uid,"") as social_uid,
                                                    COALESCE(um.signup_type,0) as signup_type,
                                                    COALESCE(um.profile_setup,0) as profile_setup,
                                                    COALESCE(um.is_active,0) as is_active,
                                                    COALESCE(um.is_verify,0) as is_verify,
                                                    ru.role_id,
                                                    um.create_time,
                                                    um.update_time
                                                   FROM
                                                    user_master um LEFT JOIN role_user AS ru ON um.id = ru.user_id
                                                    LEFT JOIN device_master AS dm ON dm.user_id = um.id AND dm.device_registered_from=0 , user_detail AS ud
                                                   WHERE
                                                    um.id != 1 AND
                                                    um.id=ud.user_id
                                                    ORDER BY '.$this->table_prefix.'.'.$this->order_by.' '.$this->order_type.' LIMIT ?,?', [$this->offset, $this->item_count]);

                        foreach ($result as $i => $data) {
                            $result[$i]->address_detail = DB::select('SELECT
                                                            COALESCE(full_name,"") AS full_name,
                                                            COALESCE(address,"") AS address,
                                                            COALESCE(country,"") AS country,
                                                            COALESCE(state,"") AS state,
                                                            COALESCE(city,"") AS city,
                                                            COALESCE(zip_code,"") AS zip_code,
                                                            COALESCE(is_active,0)  AS is_active
                                                          FROM billing_master WHERE user_id=?', [$data->user_id]);

                            $signup_device_detail = DB::select('SELECT
                                              '.$this->device_info.'
                                        FROM device_master AS dm
                                               WHERE dm.user_id=?
                                               AND dm.device_registered_from=0
                                        ORDER BY dm.create_time DESC LIMIT 1', [$data->user_id]);

                            $login_device_detail = DB::select('SELECT
                                              '.$this->device_info.'
                                        FROM device_master AS dm RIGHT JOIN user_session AS us ON us.id=dm.user_session_id AND us.is_active=1
                                               WHERE dm.user_id=?
                                               AND dm.device_registered_from=1
                                        ORDER BY dm.create_time DESC', [$data->user_id]);

                            foreach ($login_device_detail as $j => $detail) {
                                array_push($signup_device_detail, $detail);
                            }
                            $result[$i]->device_detail = $signup_device_detail;
                        }
                    }

                    $role_list = $this->getAllRole();

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result, 'role_list' => $role_list];

                });
            }

            $redis_result = Cache::get("getAllUsersByAdmin$this->page:$this->item_count:$this->order_by:$this->order_type");

            if (! $redis_result) {
                $redis_result = [];
            }
            $response = Response::json(['code' => 200, 'message' => 'Users fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getAllUsersByAdmin', $e);
            //      Log::error("getAllUsersByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getAllUsersByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (
                ($response = (new VerificationController())->validateRequiredParameter(
                    ['page', 'item_count'],
                    $request
                )) != ''
            ) {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $this->order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::rememberforever(
                "getAllUsersByAdmin:$this->page:$this->item_count:$this->order_by:$this->order_type",
                function () {
                    $total_row_result = DB::select('SELECT
                                                COUNT(um.id) AS total
                                          FROM
                                                user_master AS um,
                                                user_detail as ud,
                                                role_user AS ru
                                          WHERE
                                                um.id = ud.user_id AND um.id = ru.user_id AND ru.role_id != 2 AND
                                                um.id != 1');
                    $total_row = $total_row_result[0]->total;
                    $result = [];

                    if ($total_row) {
                        $this->order_by_clause = null;
                        if ($this->order_by == 'first_name') {
                            $this->order_by_clause .= " ud.$this->order_by $this->order_type ";
                        } elseif ($this->order_by == 'role_id') {
                            $this->order_by_clause .= " ru.$this->order_by $this->order_type ";
                        } elseif ($this->order_by) {
                            $this->order_by_clause .= " um.$this->order_by $this->order_type ";
                        } else {
                            $this->order_by_clause .= 'sb.create_time DESC ';
                        }

                        $result = DB::select(
                            'SELECT
                               um.id AS user_id,
                               COALESCE(um.user_name, "") AS user_name,
                               COALESCE(ud.first_name, "") AS first_name,
                               COALESCE(um.email_id, "") AS email_id,
                               IF(ud.profile_img != "", CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ud.profile_img), "") AS thumbnail_img,
                               IF(ud.profile_img != "", CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ud.profile_img), "") AS compressed_img,
                               IF(ud.profile_img != "", CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ud.profile_img), "") AS original_img,
                               COALESCE(um.social_uid, "") AS social_uid,
                               COALESCE(um.signup_type, 0) AS signup_type,
                               COALESCE(um.profile_setup, 0) AS profile_setup,
                               COALESCE(um.is_active, 0) AS is_active,
                               COALESCE(um.is_verify, 0) AS is_verify,
                               ud.user_keyword,
                               ru.role_id,
                               um.create_time,
                               um.update_time,
                               COALESCE(bm.full_name, "") AS full_name,
                               COALESCE(bm.address, "") AS address,
                               COALESCE(bm.country, "") AS country,
                               COALESCE(bm.state, "") AS state,
                               COALESCE(bm.city, "") AS city,
                               COALESCE(bm.zip_code, "") AS zip_code,
                               sb.create_time as sub_create_time
                            FROM
                               user_master AS um
                                LEFT JOIN role_user AS ru ON um.id = ru.user_id
                                LEFT JOIN billing_master AS bm ON um.id = bm.user_id
                                LEFT JOIN user_detail AS ud ON um.id = ud.user_id
                                LEFT JOIN subscriptions AS sb ON um.id = sb.user_id
                            WHERE
                             um.id != 1 AND ru.role_id != 2
                       ORDER BY '.$this->order_by_clause.' LIMIT ?,?', [$this->offset, $this->item_count]);
                    }

                    $role_list = $this->getAllRole();

                    $is_next_page = $total_row > ($this->offset + $this->item_count);

                    return [
                        'total_record' => $total_row,
                        'is_next_page' => $is_next_page,
                        'result' => $result,
                        'role_list' => $role_list,
                    ];
                }
            );

            $response = Response::json([
                'code' => 200,
                'message' => 'Users fetched successfully.',
                'cause' => '',
                'data' => $redis_result,
            ]);
        } catch (Exception $e) {
            (new ImageController())->logs('getAllUsersByAdmin', $e);
            //Log::error("getAllUsersByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json([
                'code' => 201,
                'message' => Config::get('constant.EXCEPTION_ERROR').'get all user.',
                'cause' => $e->getMessage(),
                'data' => json_decode('{}'),
            ]);
        }

        return $response;
    }

    /**
     * @api {post} searchUserForAdmin searchUserForAdmin
     *
     * @apiName searchUserForAdmin
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
     * "search_type":"first_name",
     * "search_query":"rushita"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Users fetched successfully.",
     * "cause": "",
     * "data": {
     * "user_detail": [
     * {
     * "user_id": 2,
     * "user_name": "rushita.optimumbrew@gmail.com",
     * "first_name": "rushita",
     * "email_id": "rushita.optimumbrew@gmail.com",
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5c026264d03b3_profile_img_1543660132.png",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5c026264d03b3_profile_img_1543660132.png",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5c026264d03b3_profile_img_1543660132.png",
     * "social_uid": "",
     * "signup_type": 0,
     * "profile_setup": 1,
     * "is_active": 1,
     * "is_verify": 1,
     * "role_id": 3,
     * "create_time": "2018-12-01 06:14:59",
     * "update_time": "2018-12-03 08:11:36"
     * }
     * ],
     * "role_list": [
     * {
     * "role_id": 1,
     * "role_name": "admin"
     * },
     * {
     * "role_id": 2,
     * "role_name": "free_user"
     * },
     * {
     * "role_id": 3,
     * "role_name": "monthly_starter"
     * },
     * {
     * "role_id": 4,
     * "role_name": "yearly_starter"
     * },
     * {
     * "role_id": 5,
     * "role_name": "monthly_pro"
     * },
     * {
     * "role_id": 6,
     * "role_name": "yearly_pro"
     * },
     * {
     * "role_id": 7,
     * "role_name": "premium_user"
     * }
     * ]
     * }
     * }
     */
    public function searchUserForAdminBackup(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_type', 'search_query'], $request)) != '') {
                return $response;
            }

            $search_type = $request->search_type;
            $search_query = '%'.$request->search_query.'%';
            $this->device_info = 'COALESCE(dm.device_platform	,"") as device_platform,
                            COALESCE(dm.device_model_name	,"") as device_model_name,
                            COALESCE(dm.device_os_version	,"") as device_os_version,
                            COALESCE(dm.device_type	,"") as device_type,
                            dm.device_registered_from AS ip_address_type,
                            COALESCE(dm.ip_address	,"0.0.0.0") AS ip_address,
                            dm.create_time AS update_time';

            if ($search_type == 'first_name' or $search_type == 'profile_img') {
                $table_prefix = 'ud';
            } elseif ($search_type == 'role_id') {
                $table_prefix = 'ru';
            } else {
                if ($search_type == 'user_id') {
                    $search_type = 'id';
                }
                $table_prefix = 'um';
            }

            $result = DB::select('select
                                      um.id as user_id,
                                      COALESCE(um.user_name,"")as user_name,
                                      COALESCE(ud.first_name,"")as first_name,
                                      COALESCE(um.email_id,"")as email_id,
                                      IF(ud.profile_img != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as thumbnail_img,
                                      IF(ud.profile_img != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as compressed_img,
                                      IF(ud.profile_img != "",CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",ud.profile_img),"") as original_img,
                                      COALESCE(um.social_uid,"") as social_uid,
                                      COALESCE(um.signup_type,0) as signup_type,
                                      COALESCE(um.profile_setup,0) as profile_setup,
                                      COALESCE(um.is_active,0) as is_active,
                                      COALESCE(um.is_verify,0) as is_verify,
                                      ru.role_id,
                                      um.create_time,
                                      um.update_time
                                      FROM
                                      user_master um LEFT JOIN role_user AS ru ON um.id = ru.user_id, user_detail ud
                                      WHERE
                                      um.id != 1 AND
                                      um.id=ud.user_id AND
                                      '.$table_prefix.'.'.$search_type.' LIKE ?', [$search_query]);

            foreach ($result as $i => $data) {
                $result[$i]->address_detail = DB::select('SELECT
                                                      COALESCE(full_name,"") AS full_name,
                                                      COALESCE(address,"") AS address,
                                                      COALESCE(country,"") AS country,
                                                      COALESCE(state,"") AS state,
                                                      COALESCE(city,"") AS city,
                                                      COALESCE(zip_code,"") AS zip_code,
                                                      COALESCE(is_active,0)  AS is_active
                                                    FROM billing_master WHERE user_id=?', [$data->user_id]);

                $signup_device_detail = DB::select('SELECT
                                              '.$this->device_info.'
                                        FROM device_master AS dm
                                               WHERE dm.user_id=?
                                               AND dm.device_registered_from=0
                                        ORDER BY dm.create_time DESC LIMIT 1', [$data->user_id]);

                $login_device_detail = DB::select('SELECT
                                              '.$this->device_info.'
                                        FROM device_master AS dm RIGHT JOIN user_session AS us ON us.id=dm.user_session_id AND us.is_active=1
                                               WHERE dm.user_id=?
                                               AND dm.device_registered_from=1
                                        ORDER BY dm.create_time DESC', [$data->user_id]);

                foreach ($login_device_detail as $j => $detail) {
                    array_push($signup_device_detail, $detail);
                }
                $result[$i]->device_detail = $signup_device_detail;
            }

            $role_list = $this->getAllRole();

            $response = Response::json(['code' => 200, 'message' => 'Users fetched successfully.', 'cause' => '', 'data' => ['user_detail' => $result, 'role_list' => $role_list]]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchUserForAdmin', $e);
            //      Log::error("searchUserForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' search user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function searchUserForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (
                ($response = (new VerificationController())->validateRequiredParameter(
                    ['search_type', 'search_query'],
                    $request
                )) != ''
            ) {
                return $response;
            }

            $search_type = $request->search_type;
            $search_query = '%'.$request->search_query.'%';
            $where_condition = null;

            if ($search_type == 'first_name') {
                $where_condition .=
                  " AND ud.$search_type LIKE '".$search_query."' ";
            } elseif ($search_type) {
                $where_condition .=
                  " AND um.$search_type LIKE '".$search_query."' ";
            }

            $result = DB::select(
                'SELECT
                              um.id AS user_id,
                              COALESCE(um.user_name, "") AS user_name,
                              COALESCE(ud.first_name, "") AS first_name,
                              COALESCE(um.email_id, "") AS email_id,
                              IF(ud.profile_img != "", CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ud.profile_img), "") AS thumbnail_img,
                              IF(ud.profile_img != "", CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ud.profile_img), "") AS compressed_img,
                              IF(ud.profile_img != "", CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", ud.profile_img), "") AS original_img,
                              COALESCE(um.social_uid, "") AS social_uid,
                              COALESCE(um.signup_type, 0) AS signup_type,
                              COALESCE(um.profile_setup, 0) AS profile_setup,
                              COALESCE(um.is_active, 0) AS is_active,
                              COALESCE(um.is_verify, 0) AS is_verify,
                              ud.user_keyword,
                              ru.role_id,
                              um.create_time,
                              um.update_time,
                              COALESCE(bm.full_name, "") AS full_name,
                              COALESCE(bm.address, "") AS address,
                              COALESCE(bm.country, "") AS country,
                              COALESCE(bm.state, "") AS state,
                              COALESCE(bm.city, "") AS city,
                              COALESCE(bm.zip_code, "") AS zip_code
                           FROM
                              user_master AS um
                              LEFT JOIN role_user AS ru ON um.id = ru.user_id
                              LEFT JOIN billing_master AS bm ON um.id = bm.user_id,
                              user_detail AS ud
                           WHERE
                              um.id != 1 AND
                              um.id = ud.user_id
                              '.$where_condition.' ');

            $role_list = $this->getAllRole();

            $response = Response::json([
                'code' => 200,
                'message' => 'Users fetched successfully.',
                'cause' => '',
                'data' => ['user_detail' => $result, 'role_list' => $role_list],
            ]);
        } catch (Exception $e) {
            (new ImageController())->logs('searchUserForAdmin', $e);
            //Log::error("searchUserForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json([
                'code' => 201,
                'message' => Config::get('constant.EXCEPTION_ERROR').' search user.',
                'cause' => $e->getMessage(),
                'data' => json_decode('{}'),
            ]);
        }

        return $response;
    }

    /**
     * @api {post} getUserSessionInfo   getUserSessionInfo
     *
     * @apiName getUserSessionInfo
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
     * "user_id":4 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Session information fetched successfully.",
     * "cause": "",
     * "data": {
     * "user_id": 4,
     * "create_time": "2018-11-17 06:22:24",
     * "update_time": "2018-11-17 06:22:24",
     * "session_count": 23
     * }
     * }
     */
    public function getUserSessionInfo(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (
                ($response = (new VerificationController())->validateRequiredParameter(
                    ['user_id'],
                    $request
                )) != ''
            ) {
                return $response;
            }

            $this->user_id = $request->user_id;

            $session_count = DB::select('SELECT count(user_id) AS session_count FROM user_session WHERE user_id = ? AND is_active=1', [$this->user_id]);
            $result = DB::select('SELECT user_id,create_time,update_time FROM user_session WHERE user_id = ? AND is_active = 1 ORDER BY update_time DESC LIMIT 1', [$this->user_id]);

            $signup_device_detail = DB::select('SELECT
                              TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_model_name")) AS device_model_name,
                              TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_os_version")) AS device_os_version,
                              TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_platform")) AS device_platform,
                              TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_type")) AS device_type,
                              TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.ip_address")) AS ip_address,
                              0 AS ip_address_type,
                              update_time
                          FROM
                              user_detail
                          WHERE user_id = ?', [$this->user_id]);

            $device_detail = DB::select(
                'SELECT TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_model_name")) AS device_model_name,
                TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_os_version")) AS device_os_version,
                TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_platform")) AS device_platform,
                TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.device_type")) AS device_type,
                TRIM(BOTH \'"\' FROM JSON_EXTRACT(device_json, "$.ip_address")) AS ip_address,
                1 AS ip_address_type,
                update_time
            FROM
                user_session
            WHERE user_id = ? ORDER BY id DESC LIMIT 0,10', [$this->user_id]);

            $final_device_info =
              array_merge(
                  $signup_device_detail,
                  $device_detail
              );

            if (count($result) > 0) {
                $result[0]->session_count = $session_count[0]->session_count;
                $result = $result[0];
            } else {
                $result['user_id'] = $this->user_id;
                $result['session_count'] = 0;
                $result['create_time'] = '';
                $result['update_time'] = '';
            }

            $final_result['result'] = $result;

            $final_result['device_detail'] = $final_device_info;

            $response = Response::json([
                'code' => 200,
                'message' => 'Session information fetched successfully.',
                'cause' => '',
                'data' => $final_result,
            ]);
            $response->headers->set(
                'Cache-Control',
                Config::get('constant.RESPONSE_HEADER_CACHE')
            );
        } catch (Exception $e) {
            (new ImageController())->logs('getUserSessionInfo', $e);
            //      Log::error("getUserSessionInfo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json([
                'code' => 201,
                'message' => Config::get('constant.EXCEPTION_ERROR').
                  ' get session information.',
                'cause' => $e->getMessage(),
                'data' => json_decode('{}'),
            ]);
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/changeUserRole",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="changeUserRole",
     *        summary="Change the role of user.",
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
     *          required={"role_id","user_id"},
     *
     *          @SWG\Property(property="role_id",  type="integer", example=7, description=""),
     *          @SWG\Property(property="user_id",  type="string", example=2, description=""),
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
    /**
     * @api {post} changeUserRole   changeUserRole
     *
     * @apiName changeUserRole
     *
     * @apiGroup Admin
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     *{
     * "role_id":7, //compulsory
     * "user_id":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Role of the user changed successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function changeUserRole(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['role_id', 'user_id'], $request)) != '') {
                return $response;
            }

            $role_id = $request->role_id;
            $user_id = $request->user_id;
            $date = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->validateRole($role_id)) != '') {
                return $response;
            }

            if ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER') || $role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO')) {
                $final_expiration_time = date('Y-m-d H:i:s', strtotime('+1 months'));
            } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER') || $role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_PRO')) {
                $final_expiration_time = date('Y-m-d H:i:s', strtotime('+1 years'));
            } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_PREMIUM_USER')) {
                $final_expiration_time = date('Y-m-d H:i:s');
            } else {
                $final_expiration_time = date('Y-m-d H:i:s', strtotime('-1 minutes'));
            }

            DB::beginTransaction();

            DB::update('UPDATE stripe_payment_status SET is_active = ?,expiration_time = ? WHERE user_id = ?', [0, $final_expiration_time, $user_id]);

            DB::update('UPDATE subscriptions SET
                      final_expiration_time = ?,
                      expiration_time = ?,
                      cancellation_date = ?,
                      remaining_days= ?,
                      response_message= ?,
                      is_active= ?,
                      is_expired = ?
                 WHERE
                      user_id = ?', [$final_expiration_time, $final_expiration_time, $date, 0, 'Change role by Admin', 0, 1, $user_id]);

            DB::update('UPDATE stripe_subscription_master SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE stripe_payment_method_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE stripe_customer_details SET is_active = 0 WHERE user_id = ?', [$user_id]);

            DB::update('UPDATE payment_status_master SET is_active= ? WHERE user_id = ? ', [0, $user_id]);

            DB::update('UPDATE role_user SET role_id = ? WHERE user_id = ? ', [$role_id, $user_id]);

            DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_id]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Role of the user changed successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('changeUserRole', $e);
            //      Log::error("changeUserRole : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'change the role of a user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getAllRole()
    {
        try {

            return $result = DB::select('SELECT
                                        id AS role_id,
                                        display_name AS role_name
                                        FROM roles
                                        WHERE id != 1
                                      ORDER BY id');

        } catch (Exception $e) {
            (new ImageController())->logs('getAllRole', $e);
            //      Log::error("getAllRole : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all role.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| Feedback |===================================== */

    public function getFeedbackForObArm(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->user_id = JWTAuth::toUser($token)->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['last_sync_time'], $request)) != '') {
                return $response;
            }

            Log::info('request_data', ['request_data' => $request]);

            $this->last_sync_time = $request->last_sync_time;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getFeedbackForObArm$this->last_sync_time")) {
                $result = Cache::rememberforever("getFeedbackForObArm$this->last_sync_time", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM feedback_master where is_active = 1 AND create_time >= ?', [$this->last_sync_time]);
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                              fm.id as feedback_id,
                                              fm.user_id,
                                              ud.first_name,
                                              ud.email_id,
                                              COALESCE (fm.rate,0) AS rate,
                                              COALESCE (fm.feedback_text,"") AS feedback_text,
                                              COALESCE (fm.feedback_url,"") AS feedback_url,
                                              COALESCE (fm.device_info,"") AS device_info,
                                              fm.update_time
                                            FROM
                                              feedback_master AS fm LEFT JOIN user_detail AS ud ON ud.user_id=fm.user_id
                                              WHERE
                                              fm.is_active = 1 AND
                                              fm.create_time >= ?', [$this->last_sync_time]);

                    return ['total_record' => $total_row, 'result' => $result];
                });

            }

            $redis_result = Cache::get("getFeedbackForObArm$this->last_sync_time");

            if (! $redis_result) {
                $redis_result = [];

            }

            $response = Response::json(['code' => 200, 'message' => 'Feedback fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getFeedbackForObArm', $e);
            //      Log::error("getFeedbackForObArm : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getAllFeedbackForObArm(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->user_id = JWTAuth::toUser($token)->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            //Log::info('request_data', ['request_data' => $request]);

            $this->page = isset($request->page) ? $request->page : '';
            $this->item_count = isset($request->item_count) ? $request->item_count : '';
            $this->offset = ($this->page - 1) * $this->item_count;

            $this->order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $this->order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $this->search_type = isset($request->search_type) ? $request->search_type : '';
            //$this->search_query = isset($request->search_query) ?  '%' . $request->search_query . '%'  : '';
            $this->search_query = isset($request->search_query) ? $request->search_query : '';
            $this->table_prefix = 'fm';

            if ($this->order_by == 'first_name' || $this->search_type == 'first_name') {
                $this->table_prefix = 'ud';
            }

            if ($this->search_type && $this->search_query) {
                $this->where_condition = $this->table_prefix.'.'.$this->search_type.' LIKE "'.$this->search_query.'" ';
            } else {
                $this->where_condition = 'fm.id IS NOT NULL';
            }

            if ($this->order_by && $this->order_type) {
                $this->order_by = $this->table_prefix.'.'.$this->order_by.' '.$this->order_type;
            } else {
                $this->order_by = 'fm.update_time DESC';
            }

            $redis_result = Cache::remember("getAllFeedbackForObArm:$this->user_id:$this->order_by:$this->order_type:$this->search_type:$this->search_query", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                $feedback_result = DB::select('SELECT
                                    fm.id as feedback_id,
                                    JSON_EXTRACT(device_info, "$.device_model_name") AS device_model_name,
                                    JSON_EXTRACT(device_info, "$.device_os_version") AS device_os_version,
                                    JSON_EXTRACT(device_info, "$.device_platform") AS device_platform,
                                    JSON_EXTRACT(device_info, "$.device_type") AS device_type,
                                    fm.feedback_url,
                                    fm.user_id,
                                    ru.role_id,
                                    ud.first_name,
                                    ud.email_id,
                                    COALESCE (fm.rate,0) AS rate,
                                    COALESCE (fm.feedback_text,"") AS feedback_text,
                                    fm.update_time
                                  FROM
                                    feedback_master AS fm LEFT JOIN user_detail AS ud ON ud.user_id=fm.user_id
                                                          LEFT JOIN role_user AS ru ON fm.user_id=ru.user_id
                                    WHERE
                                    fm.is_active = ? AND
                                    '.$this->where_condition.'
                            ORDER BY '.$this->order_by.' LIMIT 1000', [1]);

                $total_row = count($feedback_result);

                foreach ($feedback_result as $feedback) {
                    if (strpos($feedback->feedback_url, '?edit=') !== false) {
                        $my_design_id = substr($feedback->feedback_url, (strpos($feedback->feedback_url, '=') ?: -1) + 1);
                        $sample_image_url = DB::select('SELECT
                                                      IF(mdm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),"") AS sample_image
                                                    FROM
                                                      my_design_master AS mdm
                                                    WHERE
                                                      mdm.uuid = ?', [$my_design_id]);
                        if ($sample_image_url) {
                            $feedback->feedback_url = $sample_image_url[0]->sample_image;
                        }
                    }
                }
                $result = ['total_row' => $total_row, 'feedback_result' => $feedback_result];

                return $result;

            });

            $result = array_slice($redis_result['feedback_result'], $this->offset, $this->item_count);
            $is_next_page = ($redis_result['total_row'] > ($this->offset + $this->item_count)) ? true : false;
            $result = ['total_record' => $redis_result['total_row'], 'is_next_page' => $is_next_page, 'result' => $result];

            $response = Response::json(['code' => 200, 'message' => 'Feedback fetched successfully.', 'cause' => '', 'data' => $result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllFeedbackForObArm', $e);
            //Log::error("getAllFeedbackForObArm : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getAllFeedback",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getAllFeedback",
     *        summary="Get all feedback.",
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
     * @api {post} getAllFeedback getAllFeedback
     *
     * @apiName getAllFeedback
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
     * "page":1, //compulsory
     * "item_count":1, //compulsory
     * "order_by":"first_name", //optional
     * "order_type":"ASC" //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Feedback fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "feedback_id": 2,
     * "feedback_url": "http://localhost:4200/#/editor/7e6xbmd929914d",
     * "user_id": 2,
     * "first_name": "rushita",
     * "rate": 3,
     * "feedback_text": "hi hi hi",
     * "update_time": "2018-11-17 05:00:17"
     * },
     * {
     * "feedback_id": 1,
     * "feedback_url": NULL,
     * "user_id": 4,
     * "first_name": "Umesh Patadiya",
     * "rate": 4,
     * "feedback_text": "tg4t4\n",
     * "update_time": "2018-11-15 11:52:54"
     * }
     * ]
     * }
     * }
     */
    public function getAllFeedback(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->user_id = JWTAuth::toUser($token)->id;
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            //Log::info('request_data', ['request_data' => $request]);

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $this->order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->order_by == 'first_name') {
                $this->table_prefix = 'ud';
            } else {
                $this->table_prefix = 'fm';

            }

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllFeedback$this->user_id:$this->order_by:$this->order_type:$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getAllFeedback$this->user_id:$this->order_by:$this->order_type:$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total FROM feedback_master where is_active = 1');
                    $total_row = $total_row_result[0]->total;

                    if ($this->order_by == '' && $this->order_type == '') {

                        $result = DB::select('SELECT
                                                fm.id as feedback_id,
                                                JSON_EXTRACT(device_info, "$.device_model_name") AS device_model_name,
                                                JSON_EXTRACT(device_info, "$.device_os_version") AS device_os_version,
                                                JSON_EXTRACT(device_info, "$.device_platform") AS device_platform,
                                                JSON_EXTRACT(device_info, "$.device_type") AS device_type,
                                                fm.feedback_url,
                                                fm.user_id,
                                                ru.role_id ,
                                                ud.first_name,
                                                ud.email_id,
                                                COALESCE (fm.rate,0) AS rate,
                                                COALESCE (fm.feedback_text,"") AS feedback_text,
                                                fm.update_time
                                              FROM
                                                feedback_master AS fm LEFT JOIN user_detail AS ud ON ud.user_id=fm.user_id
                                                                      LEFT JOIN role_user AS ru ON fm.user_id=ru.user_id
                                                WHERE
                                                fm.is_active = ?
                                              ORDER BY fm.update_time DESC LIMIT ?,?', [1, $this->offset, $this->item_count]);

                    } else {
                        $result = DB::select('SELECT
                                                fm.id as feedback_id,
                                                JSON_EXTRACT(device_info, "$.device_model_name") AS device_model_name,
                                                JSON_EXTRACT(device_info, "$.device_os_version") AS device_os_version,
                                                JSON_EXTRACT(device_info, "$.device_platform") AS device_platform,
                                                JSON_EXTRACT(device_info, "$.device_type") AS device_type,
                                                fm.feedback_url,
                                                fm.user_id,
                                                ru.role_id,
                                                ud.first_name,
                                                ud.email_id,
                                                COALESCE (fm.rate,0) AS rate,
                                                COALESCE (fm.feedback_text,"") AS feedback_text,
                                                fm.update_time
                                              FROM
                                                feedback_master AS fm LEFT JOIN user_detail AS ud ON ud.user_id=fm.user_id
                                                                      LEFT JOIN role_user AS ru ON fm.user_id=ru.user_id
                                                WHERE
                                                fm.is_active = ?
                                        ORDER BY '.$this->table_prefix.'.'.$this->order_by.' '.$this->order_type.' LIMIT ?,?', [1, $this->offset, $this->item_count]);

                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];
                });

            }

            $redis_result = Cache::get("getAllFeedback$this->user_id:$this->order_by:$this->order_type:$this->page:$this->item_count");

            if (! $redis_result) {
                $redis_result = [];

            }

            $response = Response::json(['code' => 200, 'message' => 'Feedback fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllFeedback', $e);
            //      Log::error("getAllFeedback : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} searchFeedback searchFeedback
     *
     * @apiName searchFeedback
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
     * "search_type":"user_id", //compulsory
     * "search_query":"4" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Feedback fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "feedback_id": 1,
     * "user_id": 4,
     * "first_name": "Umesh Patadiya",
     * "rate": 4,
     * "feedback_text": "tg4t4\n",
     * "update_time": "2018-11-15 11:52:54"
     * }
     * ]
     * }
     * }
     */
    public function searchFeedback(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_type', 'search_query'], $request)) != '') {
                return $response;
            }

            $search_type = $request->search_type;
            $search_query = '%'.$request->search_query.'%';

            if ($search_type == 'first_name') {

                $table_prefix = 'ud';
            } else {
                $table_prefix = 'fm';
            }

            $result = DB::select('SELECT
                                                fm.id as feedback_id,
                                                JSON_EXTRACT(device_info, "$.device_model_name") AS device_model_name,
                                                JSON_EXTRACT(device_info, "$.device_os_version") AS device_os_version,
                                                JSON_EXTRACT(device_info, "$.device_platform") AS device_platform,
                                                JSON_EXTRACT(device_info, "$.device_type") AS device_type,
                                                fm.feedback_url,
                                                fm.user_id,
                                                ru.role_id,
                                                ud.first_name,
                                                COALESCE (fm.rate,0) AS rate,
                                                COALESCE (fm.feedback_text,"") AS feedback_text,
                                                fm.update_time
                                              FROM
                                                feedback_master AS fm LEFT JOIN user_detail AS ud ON ud.user_id=fm.user_id
                                                                      LEFT JOIN role_user AS ru ON fm.user_id=ru.user_id
                                                WHERE
                                                fm.is_active = ? AND
                                              '.$table_prefix.'.'.$search_type.' LIKE ?
                                              ORDER BY fm.update_time DESC', [1, $search_query]);

            $response = Response::json(['code' => 200, 'message' => 'Feedback fetched successfully.', 'cause' => '', 'data' => ['result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchFeedback', $e);
            //      Log::error("searchFeedback : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' search feedback.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| Download Summary |===================================== */

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getGenerateVideoReportForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getGenerateVideoReportForAdmin",
     *        summary="Get download report ",
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
     *          @SWG\Property(property="start_date",  type="date", example="2020-10-01", description=""),
     *          @SWG\Property(property="end_date",  type="date", example="2020-10-05", description=""),
     *
     *        ),
     *      ),
     *
     * @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Generate video report fetched successfully..","cause":"","data":{
     *     "template_url": "http://192.168.0.134/photoadking_testing_latest/app/#/video-editor/u1j6j7d929914d/ge3cagc8da391f/vm26zre63831c9",
     *  "sample_image_template": "http://192.168.0.134/photoadking_testing_latest/image_bucket/compressed/5e019feb59868_sample_image_1577164779.jpg",
     *  "sample_image_user": "http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5f81488969aa9_my_design_1602308233.jpg",
     *  "bg_file": "http://192.168.0.134/photoadking_testing_latest/image_bucket/video/facebookcanvas_video_jrj17_11.mp4",
     *  "content_type": 2,
     *  "user_id": 215,
     *  "status": 1,
     *  "failed_reason": null,
     *  "quality": 2,
     *  "downloaded_time": "00:00:06",
     *  "cmd_execute_time": "00:00:03",
     *  "is_audio_user_uploaded": 1,
     *  "audio_file_id": 82}}, description=""),),
     *        ),
     *
     * @SWG\Response(
     *            response=201,
     *            description="error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to Generate video report.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} getGenerateVideoReportForAdmin   getGenerateVideoReportForAdmin
     *
     * @apiName getGenerateVideoReportForAdmin
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
     * "page":1,
     * "item_count":2,
     * "start_date":"2020-09-25",
     * "end_date":"2020-09-29",
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     *   "message": "Download Video report successfully received.",
     *   "cause": "",
     *   "data": {
     *   "total_record": 3,
     *   "is_next_page": false,
     *   "total_download_list": [
     *   {
     *   "template_url": "https://www.photoadking.com//app/#/video-editor/laghh7d929914d/d5h6w9c8da391f/xhn39ge63831c9",
     *   "sample_image_template": "http://192.168.0.134/photoadking_testing_latest/image_bucket/compressed/5e0191c30577b_sample_image_1577161155.jpg",
     *   "sample_image_user": "http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5f755beeb2e5b_my_design_1601526766.jpg",
     *   "bg_file": "https://player.vimeo.com/external/459802291.hd.mp4?s=241d1fb766fff425fef8903063696e4254689f06&profile_id=174",
     *   "content_type": 2,        //2=video,3=intro
     *   "download_video_size": 40855,
     *   "user_id": 23,
     *   "status": 1,                   //0=queued,1=success,2=fail
     *   "failed_reason": "{\"download_id\":\"NWY4MTMyNWMzOTA2MV8yMTV0cmFuc3BhcmVudF9pbWFnZV8xNjAyMzAyNTU2LnBuZw==\",\"output\":[\"'-i' is not recognized as an internal or external command,\",\"operable program or batch file.\"],\"result\":1}",
     *   "quality": 2,                   //1=Web quality,2=HD quality
     *   "downloaded_time": "00:00:43",
     *   "cmd_execute_time": "00:00:13",
     *   "is_audio_user_uploaded": 0,
     *   "audio_file_id": 0
     *   },
     *   {
     *   "template_url": "https://www.photoadking.com//app/#/video-editor/laghh7d929914d/d5h6w9c8da391f/xhn39ge63831c9",
     *   "sample_image_template": "http://192.168.0.134/photoadking_testing_latest/image_bucket/compressed/5e0191c30577b_sample_image_1577161155.jpg",
     *   "sample_image_user": "http://192.168.0.134/photoadking_testing_latest/image_bucket/my_design/5f755beeb2e5b_my_design_1601526766.jpg",
     *   "bg_file": "http://192.168.0.134/photoadking_testing_latest/image_bucket/video/harshilvastarpara_5f5f0db751fda_bg_image_1600064951.mp4",
     *   "content_type": 2,     //2=video,3=intro
     *   "download_video_size": 126,
     *   "user_id": 23,
     *   "status": 1,           //0=queued,1=success,2=fail
     *   "failed_reason": "{\"download_id\":\"NWY4MTMyNWMzOTA2MV8yMTV0cmFuc3BhcmVudF9pbWFnZV8xNjAyMzAyNTU2LnBuZw==\",\"output\":[\"'-i' is not recognized as an internal or external command,\",\"operable program or batch file.\"],\"result\":1}",
     *   "quality": 1,          //1=Web quality,2=HD quality
     *   "downloaded_time": "00:28:27",
     *   "cmd_execute_time": "00:00:04",
     *   "is_audio_user_uploaded": 0,
     *   "audio_file_id": 0
     *   },
     *   {
     *   "template_url": null,
     *   "sample_image_template": "",
     *   "sample_image_user": "",
     *   "bg_file": "http://192.168.0.134/photoadking_testing_latest/image_bucket/video/harshilvastarpara_5f5f0db751fda_bg_image_1600064951.mp4",
     *   "content_type": 2,          //2=video,3=intro
     *   "download_video_size": 871,
     *   "user_id": 23,
     *   "status": 1,       //0=queued,1=success,2=fail
     *   "failed_reason": "{\"download_id\":\"NWY4MTMyNWMzOTA2MV8yMTV0cmFuc3BhcmVudF9pbWFnZV8xNjAyMzAyNTU2LnBuZw==\",\"output\":[\"'-i' is not recognized as an internal or external command,\",\"operable program or batch file.\"],\"result\":1}",
     *   "quality": 2,      //1=Web quality,2=HD quality
     *   "downloaded_time": "00:00:03",
     *   "cmd_execute_time": "00:00:01",
     *   "is_audio_user_uploaded": 0,
     *   "audio_file_id": 0
     *   }
     *   ]
     *   }
     * }
     */
    public function getGenerateVideoReportForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'start_date', 'end_date'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->start_date = $request->start_date;
            $this->end_date = $request->end_date;

            $redis_result = Cache::rememberforever("getGenerateVideoReportForAdmin:$this->start_date:$this->end_date:$this->page:$this->item_count", function () {

                $total_row = DB::select('SELECT
                                    COUNT(vtj.id) AS total
                                  FROM
                                      video_template_jobs AS vtj
                                  WHERE DATE(vtj.create_time) BETWEEN ? AND ?', [$this->start_date, $this->end_date]);
                $total_record = $total_row[0]->total;

                if ($total_record) {

                    DB::statement("SET sql_mode = '' ");
                    $result = DB::select('SELECT
                                  vtj.id AS id,
                                  IF(vtj.my_design_id != "",CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'","/app/#/",IF(vtj.content_type = 2,"video-editor","intro-editor"),"/",scm.uuid,"/",cat.uuid,"/",cm.uuid),NULL) AS template_url,
                                  IF(vtj.my_design_id != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),NULL) AS sample_image_template,
                                  IF(vtj.my_design_id != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",mdm.image),NULL) AS sample_image_user,
                                  IF(SUBSTR(vtj.bg_file,1,4)="http",vtj.bg_file,IF(vtj.is_video_user_uploaded=1,CONCAT("'.Config::get('constant.USER_UPLOAD_VIDEOS_DIRECTORY_OF_DIGITAL_OCEAN').'",vtj.bg_file),CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",vtj.bg_file))) AS bg_file,
                                  vtj.content_type,
                                  um.email_id AS email_id,
                                  vtj.status,
                                  vtj.quality,
                                  IF(vtj.download_video_size < 1024,CONCAT(vtj.download_video_size," ","KB"),CONCAT(ROUND(vtj.download_video_size/1024,2)," ","MB")) AS download_video_size,
                                  TIMEDIFF(vtj.update_time,vtj.create_time) AS downloaded_time,
                                  vtj.cmd_execute_time,
                                  coalesce(vtj.is_audio_user_uploaded,0) as is_audio_user_uploaded,
                                  coalesce(vtj.audio_file_id,0) as audio_file_id
                              FROM video_template_jobs AS vtj
                                      LEFT JOIN my_design_master AS mdm ON mdm.id = vtj.my_design_id
                                      LEFT JOIN content_master AS cm ON cm.id = mdm.content_id
                                      LEFT JOIN catalog_master AS cat ON cm.catalog_id = cat.id
                                      LEFT JOIN sub_category_catalog AS scc ON cat.id = scc.catalog_id
                                      LEFT JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id
                                      LEFT JOIN user_master AS um ON vtj.user_id = um.id
                              WHERE
                                  DATE(vtj.create_time) BETWEEN ? AND ?
                              GROUP BY vtj.id
                                ORDER BY vtj.update_time DESC
                              LIMIT ?, ?', [$this->start_date, $this->end_date, $this->offset, $this->item_count]);

                    foreach ($result as $i => $data) {
                        $history = DB::select('SELECT message,create_time FROM video_generate_history_master WHERE download_id=?', [$data->id]);
                        if (count($history) > 0) {
                            $data->history = $history;
                        } else {
                            $data->history = null;
                        }
                    }

                } else {
                    $result = 0;
                }
                $is_next_page = ($total_record > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_record, 'is_next_page' => $is_next_page, 'result' => $result];

            });

            $response = Response::json(['code' => 200, 'message' => 'Download Video report successfully received.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getGenerateVideoReportForAdmin', $e);
            //Log::error("getGenerateVideoReportForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'received video report .', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| Content Summary |===================================== */

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getSummaryByAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getSummaryByAdmin",
     *        summary="Get summary by admin",
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
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="page",  type="integer", example=1, description=""),
     *          @SWG\Property(property="item_count",  type="integer", example=2, description=""),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Summary fetched successfully.","cause":"","data":{"total_record":68,"is_next_page":false,"result":{{"sub_category_id":15,"category_id":4,"category_name":"Templates","sub_category_name":"Twitter Post","no_of_catalogs":1,"content_count":26,"free_content":26,"paid_content":0,"is_active":1},{"sub_category_id":1,"category_id":4,"category_name":"Templates","sub_category_name":"Snapchat Geo Filter","no_of_catalogs":1,"content_count":30,"free_content":19,"paid_content":11,"is_active":1}}}}),),
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
    public function getSummaryByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $page = $request->page;
            $item_count = $request->item_count;
            $order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $offset = ($page - 1) * $item_count;

            if ($order_by == 'no_of_catalogs') {
                $table_prefix = '';
            } elseif ($order_by == 'content_count' or $order_by == 'free_content' or $order_by == 'paid_content') {
                $table_prefix = '';
            } elseif ($order_by == 'category_name') {
                $order_by = 'name';
                $table_prefix = 'c.';
            } else {
                if ($order_by == 'sub_category_id') {
                    $order_by = 'id';
                }
                $table_prefix = 'scm.';
            }

            $total_row_result = DB::select('SELECT
                                                        count(scm.id) AS total
                                                      FROM
                                                        sub_category_master AS scm WHERE scm.is_active = 1');

            $total_row = $total_row_result[0]->total;

            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            if ($order_by == '' && $order_type == '') {

                $result = DB::select('SELECT
                                            DISTINCT scm.id AS sub_category_id,
                                            scm.category_id,
                                            c.name AS category_name,
                                            scm.sub_category_name,
                                            count(DISTINCT scc.catalog_id) AS no_of_catalogs,
                                            count(cm.catalog_id) AS content_count,
                                            count(IF(cm.is_free=1,1, NULL)) AS free_content,
                                            count(IF(cm.is_free=0,1, NULL)) AS paid_content,
                                            scm.is_active
                                          FROM
                                            sub_category_master AS scm LEFT JOIN sub_category_catalog AS scc
                                            LEFT JOIN content_master AS cm
                                              ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1
                                              ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                            LEFT JOIN category AS c ON c.id = scm.category_id
                                          GROUP BY scm.id HAVING scm.is_active = 1 ORDER BY FIELD(scm.category_id, ?) DESC LIMIT ?,?', [$content_type, $offset, $item_count]);

            } else {

                $result = DB::select('SELECT
                                            DISTINCT scm.id AS sub_category_id,
                                            scm.category_id,
                                            c.name AS category_name,
                                            scm.sub_category_name,
                                            count(DISTINCT scc.catalog_id) AS no_of_catalogs,
                                            count(cm.catalog_id) AS content_count,
                                            count(IF(cm.is_free=1,1, NULL)) AS free_content,
                                            count(IF(cm.is_free=0,1, NULL)) AS paid_content,
                                            scm.is_active
                                          FROM
                                            sub_category_master AS scm LEFT JOIN sub_category_catalog AS scc
                                            LEFT JOIN content_master AS cm
                                              ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1
                                              ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                            LEFT JOIN category AS c ON c.id = scm.category_id
                                              GROUP BY scm.id HAVING scm.is_active = 1
                                            ORDER BY '.$table_prefix.$order_by.' '.$order_type.' LIMIT ?,?', [$offset, $item_count]);

            }

            $is_next_page = ($total_row > ($offset + $item_count)) ? true : false;

            $response = Response::json(['code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('getSummaryByAdmin', $e);
            //      Log::error("getSummaryByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get summary.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/searchSummaryByAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="searchSummaryByAdmin",
     *        summary="Search summary by admin",
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
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count"},
     *
     *          @SWG\Property(property="search_type",  type="string", example="sub_category_name", description=""),
     *          @SWG\Property(property="search_query",  type="string", example="LinkedIn", description=""),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Summary fetched successfully.","cause":"","data":{"result":{{"sub_category_id":18,"category_id":4,"category_name":"Templates","sub_category_name":"LinkedIn Banner","no_of_catalogs":1,"content_count":10,"free_content":10,"paid_content":0,"is_active":1},{"sub_category_id":14,"category_id":4,"category_name":"Templates","sub_category_name":"LinkedIn Post Header","no_of_catalogs":0,"content_count":0,"free_content":0,"paid_content":0,"is_active":1}}}}),),
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
    public function searchSummaryByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_type', 'search_query'], $request)) != '') {
                return $response;
            }

            $search_type = $request->search_type;
            $search_query = '%'.$request->search_query.'%';

            if ($search_type == 'no_of_catalogs') {
                $table_prefix = '';
            } elseif ($search_type == 'content_count' or $search_type == 'free_content' or $search_type == 'paid_content') {
                $table_prefix = '';
            } elseif ($search_type == 'category_name') {

                $search_type = 'name';
                $table_prefix = 'c.';
            } else {
                if ($search_type == 'sub_category_id') {
                    $search_type = 'id';
                }
                $table_prefix = 'scm.';

            }
            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');

            $result = DB::select('SELECT
                                            DISTINCT scm.id AS sub_category_id,
                                            scm.category_id,
                                            c.name AS category_name,
                                            scm.sub_category_name,
                                            count(DISTINCT scc.catalog_id) AS no_of_catalogs,
                                            count(cm.catalog_id) AS content_count,
                                            count(IF(cm.is_free=1,1, NULL)) AS free_content,
                                            count(IF(cm.is_free=0,1, NULL)) AS paid_content,
                                            scm.is_active
                                          FROM
                                            sub_category_master AS scm LEFT JOIN sub_category_catalog AS scc
                                            LEFT JOIN content_master AS cm
                                              ON cm.catalog_id = scc.catalog_id AND cm.is_active = 1
                                              ON scm.id = scc.sub_category_id AND scc.is_active = 1
                                            LEFT JOIN category AS c ON c.id = scm.category_id
                                          GROUP BY scm.id HAVING scm.is_active = 1 AND
                                      '.$table_prefix.$search_type.' LIKE ? ORDER BY FIELD(scm.category_id, ?)', [$search_query, $content_type]);

            $response = Response::json(['code' => 200, 'message' => 'Summary fetched successfully.', 'cause' => '', 'data' => ['result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchSummaryByAdmin', $e);
            //      Log::error("searchSummaryByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search summary.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getCatalogWithDetailBySubCategoryId",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="get Catalog WithDetail BySubCategoryId ForAdmin",
     *        summary="getTotalCatalog based on free or paid",
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
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"page","item_count","sub_category_id"},
     *
     *          @SWG\Property(property="page",  type="integer", example="1", description=""),
     *          @SWG\Property(property="item_count",  type="integer", example="1", description=""),
     *          @SWG\Property(property="sub_category_id",  type="integer", example="1", description=""),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="string", example={"code":200,"message":"Catalog Fetch successfully.","cause":"","data":{"total_record":5,"is_next_page":false,"result":{{"catalog_name": "Snapchat Geo Filter 1","is_active": 1,"free_content": 22, "paid_content": 1,"total_content": 23}}}}, description=""),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to fetch catalog name.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    /**
     * @api {post} getCatalogWithDetailBySubCategoryId getCatalogWithDetailBySubCategoryId
     *
     * @apiName getCatalogWithDetailBySubCategoryId
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
     * "page":1, //compulsory
     * "item_count":1, //compulsory
     * "sub_category_id":1 //compulsory
     *
     * }
     * @apiSuccessExample Success-Response:
     * {
          "code": 200,
          "message": "Catalog Fetch successfully .",
          "cause": "",
          "data": {
          "total_record": 5,
          "is_next_page": false,
          "catalog_result": [
          {
          "catalog_name": "Snapchat Geo Filter 1",
          "is_active": 1,
          "free_content": 22,
          "paid_content": 1,
          "total_content": 23
          },
          {
          "catalog_name": "All",
          "is_active": 1,
          "free_content": 22,
          "paid_content": 13,
          "total_content": 35
          },
          {
          "catalog_name": "Snapchat Geo Filter",
          "is_active": 1,
          "free_content": 3,
          "paid_content": 0,
          "total_content": 3
          },
          {
          "catalog_name": "Jay",
          "is_active": 1,
          "free_content": 1,
          "paid_content": 0,
          "total_content": 1
          },
          {
          "catalog_name": "Business",
          "is_active": 0,
          "free_content": 17,
          "paid_content": 0,
          "total_content": 17
          }
          ]
          }
          }
     */
    public function getCatalogWithDetailBySubCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = 1;
            $this->sub_category_id = $request->sub_category_id;
            $this->order_by = isset($request->order_by) ? $request->order_by : ''; //field name
            $this->order_type = isset($request->order_type) ? $request->order_type : ''; //asc or desc
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if ($this->order_by == 'no_of_catalogs') {
                $this->table_prefix = '';
            } elseif ($this->order_by == 'content_count' or $this->order_by == 'free_content' or $this->order_by == 'paid_content') {
                $this->table_prefix = '';
            } elseif ($this->order_by == 'name') {
                $this->order_by = 'name';
                $this->table_prefix = 'cat.';
            } elseif ($this->order_by == 'is_active') {
                $this->order_by = 'is_active';
                $this->table_prefix = 'cat.';
            }

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getCatalogWithDetailBySubCategoryId:$this->sub_category_id:$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getCatalogWithDetailBySubCategoryId:$this->sub_category_id:$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    if ($this->order_by == '' && $this->order_type == '') {

                        $total_catalog = DB::select('SELECT cat.name,
                                                (SELECT CONCAT("'.$this->static_page_dir.'",spscm.sub_category_path,"/",spm.catalog_path)
                                                    FROM static_page_master AS spm
                                                  INNER JOIN static_page_sub_category_master AS spscm ON spm.static_page_sub_category_id = spscm.id
                                                    WHERE spm.catalog_id = cat.id AND spm.is_active = 1)  AS page_url,
                                                cat.is_active,
                                                COUNT(IF(cm.is_free=1,1,NUll)) AS free_content,
                                                COUNT(IF(cm.is_free=0,1,NUll)) AS paid_content,
                                                COUNT(cm.catalog_id) AS content_count FROM `sub_category_catalog` AS scc
                                        INNER JOIN catalog_master AS cat ON cat.id = scc.catalog_id
                                        LEFT JOIN content_master AS cm ON cm.catalog_id = scc.catalog_id
                                          WHERE scc.sub_category_id=?
                                        GROUP BY scc.catalog_id', [$this->sub_category_id]);
                    } else {

                        $total_catalog = DB::select('SELECT cat.name,
                                                (SELECT CONCAT("'.$this->static_page_dir.'",spscm.sub_category_path,"/",spm.catalog_path)
                                                    FROM static_page_master AS spm
                                                  INNER JOIN static_page_sub_category_master AS spscm ON spm.static_page_sub_category_id = spscm.id
                                                    WHERE spm.catalog_id = cat.id AND spm.is_active = 1)  AS page_url,
                                                cat.is_active,
                                                COUNT(IF(cm.is_free=1,1,NUll)) AS free_content,
                                                COUNT(IF(cm.is_free=0,1,NUll)) AS paid_content,
                                                COUNT(cm.catalog_id) AS content_count FROM `sub_category_catalog` AS scc
                                        INNER JOIN catalog_master AS cat ON cat.id = scc.catalog_id
                                        LEFT JOIN content_master AS cm ON cm.catalog_id = scc.catalog_id
                                          WHERE scc.sub_category_id=?
                                        GROUP BY scc.catalog_id
                                          ORDER BY '.$this->table_prefix.$this->order_by.' '.$this->order_type.' ', [$this->sub_category_id]);
                    }

                    $total_row = count($total_catalog);
                    $result = array_slice($total_catalog, $this->offset, $this->item_count);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    //Log::info('total record',[$total_catalog]);

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'catalog_result' => $result];

                });
            }

            $redis_result = Cache::get("getCatalogWithDetailBySubCategoryId:$this->sub_category_id:$this->page:$this->item_count:$this->order_by:$this->order_type");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Catalog Fetch successfully .', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getCatalogWithDetailBySubCategoryId', $e);
            //      Log::error("getCatalogWithDetailBySubCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get catalog name.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /* =====================================| Transactions |===================================== */

    /**
     * @api {post} getAllTransactionsForAdmin getAllTransactionsForAdmin
     *
     * @apiName getAllTransactionsForAdmin
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
     * "page":1,
     * "item_count":1,
     * "order_by":"payment_status", //optional
     * "order_type":"ASC" //optional
     * }
     * @apiSuccessExample Success-Response:
     *{
     * "code": 200,
     * "message": "Transactions fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": false,
     * "result": [
     * {
     * "subscription_id": 7,
     * "user_id": "4",
     * "first_name": "Umesh Patadiya",
     * "transaction_id": "4NG448163N753943P",
     * "paypal_id": "I-LN4PM76D8Y5B",
     * "subscr_type": 2,
     * "txn_type": "subscr_payment",
     * "payment_status": "Completed",
     * "total_amount": 108,
     * "activation_time": "2018-11-16 10:13:06",
     * "expiration_time": "2019-11-16 10:13:06",
     * "final_expiration_time": "2019-11-16 10:13:06",
     * "cancellation_date": "",
     * "is_active": "1",
     * "create_time": "2018-11-16 10:13:06",
     * "ipn_status": 1,
     * "paypal_status": 1,
     * "paypal_payment_status": "Completed",
     * "verify_status": 1,
     * "update_time": "2018-11-16 10:14:23"
     * },
     * {
     * "subscription_id": 2,
     * "user_id": "4",
     * "first_name": "Umesh Patadiya",
     * "transaction_id": "6K5621162H181180J",
     * "paypal_id": "I-B0AG3RBMS7N3",
     * "subscr_type": 1,
     * "txn_type": "subscr_signup",
     * "payment_status": "NULL",
     * "total_amount": 12,
     * "activation_time": "2018-11-16 08:47:04",
     * "expiration_time": "2018-12-16 08:47:04",
     * "final_expiration_time": "2018-12-16 08:46:03",
     * "cancellation_date": "2018-11-16 09:19:29",
     * "is_active": "0",
     * "create_time": "2018-11-16 08:46:03",
     * "ipn_status": 0,
     * "paypal_status": 1,
     * "paypal_payment_status": "Completed",
     * "verify_status": 1,
     * "update_time": "2018-11-16 09:19:29"
     * }
     * ]
     * }
     * }
     */
    public function getAllTransactionsForAdmin(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time'; //field name
            $this->order_type = isset($request->order_type) ? strtolower($request->order_type) : 'desc'; //asc or desc
            $this->offset = ($this->page - 1) * $this->item_count;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllTransactionsForAdmin$this->page:$this->item_count:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getAllTransactionsForAdmin$this->page:$this->item_count:$this->order_by:$this->order_type", function () {

                    $total_row_result = DB::select('SELECT
                                              COALESCE(psm.txn_id,"") AS transaction_id
                                            FROM
                                              payment_status_master AS psm LEFT JOIN
                                              subscriptions AS scm ON scm.transaction_id = psm.txn_id LEFT JOIN
                                              user_detail AS ud ON psm.user_id=ud.user_id UNION
                                            SELECT
                                              COALESCE(scm.transaction_id,"") AS transaction_id
                                            FROM
                                              subscriptions AS scm LEFT JOIN
                                              payment_status_master AS psm ON scm.transaction_id = psm.txn_id LEFT JOIN
                                              user_detail AS ud ON scm.user_id=ud.user_id');

                    $total_row = count($total_row_result);

                    $result = DB::select('SELECT
                                  scm.id as subscription_id,
                                  psm.user_id,
                                  IF(ud.first_name != "", ud.first_name , scm.first_name) AS first_name,
                                  IF(ud.email_id != "", ud.email_id , "Not Found") AS email_id,
                                  COALESCE(psm.txn_id,"") AS transaction_id,
                                  COALESCE(scm.paypal_id,"") AS paypal_id,
                                  COALESCE(scm.subscr_type,0) AS subscr_type,
                                  COALESCE(scm.txn_type,"") AS txn_type,
                                  COALESCE(scm.payment_status,"") AS payment_status,
                                  COALESCE(scm.total_amount,0) AS total_amount,
                                  IF(scm.subscr_type = "1" || scm.subscr_type = "2" || scm.subscr_type = "3" || scm.subscr_type = "4", "USD" , "INR")  AS currency,
                                  COALESCE(scm.activation_time,"") AS activation_time,
                                  COALESCE(scm.expiration_time,"") AS expiration_time,
                                  COALESCE(scm.payment_date,"") AS payment_date,
                                  COALESCE(scm.final_expiration_time,"") AS final_expiration_time,
                                  COALESCE(scm.cancellation_date,"") AS cancellation_date,
                                  COALESCE(scm.is_active,"") AS is_active,
                                  COALESCE(scm.create_time,"") AS create_time,
                                  COALESCE(psm.ipn_status,0) AS ipn_status,
                                  COALESCE(psm.paypal_status,0) AS paypal_status,
                                  COALESCE(psm.paypal_payment_status,"") AS paypal_payment_status,
                                  COALESCE(psm.verify_status,0) AS verify_status,
                                  IF(scm.update_time != "",IF(psm.update_time != "",IF(scm.update_time > psm.update_time, scm.update_time , psm.update_time),scm.update_time),IF(psm.update_time != "",psm.update_time,NULL)) AS update_time
                                FROM
                                  payment_status_master AS psm LEFT JOIN
                                  subscriptions AS scm ON scm.transaction_id = psm.txn_id LEFT JOIN
                                  user_detail AS ud ON psm.user_id=ud.user_id
                              UNION
                                SELECT
                                  scm.id as subscription_id,
                                  scm.user_id,
                                  IF(ud.first_name != "", ud.first_name , scm.first_name) AS first_name,
                                  IF(ud.email_id != "", ud.email_id , "Not Found") AS email_id,
                                  COALESCE(scm.transaction_id,"") AS transaction_id,
                                  COALESCE(scm.paypal_id,"") AS paypal_id,
                                  COALESCE(scm.subscr_type,0) AS subscr_type,
                                  COALESCE(scm.txn_type,"") AS txn_type,
                                  COALESCE(scm.payment_status,"") AS payment_status,
                                  COALESCE(scm.total_amount,0) AS total_amount,
                                  IF(scm.subscr_type = "1" || scm.subscr_type = "2" || scm.subscr_type = "3" || scm.subscr_type = "4", "USD" , "INR")  AS currency,
                                  COALESCE(scm.activation_time,"") AS activation_time,
                                  COALESCE(scm.expiration_time,"") AS expiration_time,
                                  COALESCE(scm.payment_date,"") AS payment_date,
                                  COALESCE(scm.final_expiration_time,"") AS final_expiration_time,
                                  COALESCE(scm.cancellation_date,"") AS cancellation_date,
                                  COALESCE(scm.is_active,"") AS is_active,
                                  COALESCE(scm.create_time,"") AS create_time,
                                  COALESCE(psm.ipn_status,0) AS ipn_status,
                                  COALESCE(psm.paypal_status,0) AS paypal_status,
                                  COALESCE(psm.paypal_payment_status,"") AS paypal_payment_status,
                                  COALESCE(psm.verify_status,0) AS verify_status,
                                  IF(scm.update_time != "",IF(psm.update_time != "",IF(scm.update_time > psm.update_time, scm.update_time , psm.update_time),scm.update_time),IF(psm.update_time != "",psm.update_time,NULL)) AS update_time
                                FROM
                                  subscriptions AS scm LEFT JOIN
                                  payment_status_master AS psm ON scm.transaction_id = psm.txn_id LEFT JOIN
                                  user_detail AS ud ON scm.user_id=ud.user_id
                              ORDER BY '.$this->order_by.' '.$this->order_type.' LIMIT ?,?', [$this->offset, $this->item_count]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];

                });
            }

            $redis_result = Cache::get("getAllTransactionsForAdmin$this->page:$this->item_count:$this->order_by:$this->order_type");

            if (! $redis_result) {
                $redis_result = [];
            }
            $response = Response::json(['code' => 200, 'message' => 'Transactions fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getAllTransactionsForAdmin', $e);
            //      Log::error("getAllTransactionsForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all transactions.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} searchTransaction searchTransaction
     *
     * @apiName searchTransaction
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
     * "search_type":"transaction_id", //compulsory
     * "search_query":"30540646MJ8745730" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Transactions fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "subscription_id": 4,
     * "user_id": "4",
     * "first_name": "Umesh Patadiya",
     * "transaction_id": "30540646MJ8745730",
     * "paypal_id": "I-LMYV1CTCJS01",
     * "subscr_type": 1,
     * "txn_type": "subscr_payment",
     * "payment_status": "Completed",
     * "total_amount": 12,
     * "activation_time": "2018-11-03 04:26:05",
     * "expiration_time": "2018-12-03 04:26:05",
     * "final_expiration_time": "2019-01-03 04:25:36",
     * "cancellation_date": "2018-11-03 04:34:14",
     * "is_active": "0",
     * "create_time": "2018-11-03 04:25:36",
     * "ipn_status": 1,
     * "paypal_status": 1,
     * "paypal_payment_status": "Completed",
     * "verify_status": 1,
     * "update_time": "2018-11-03 04:34:14"
     * }
     * ]
     * }
     * }
     */
    public function searchTransaction(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_type', 'search_query'], $request)) != '') {
                return $response;
            }

            $search_type = $request->search_type;
            if ($search_type == 'unverified') {
                $search_query = 'result.paypal_payment_status = "Pending" AND result.ipn_status = 0 AND result.is_active = 0';
            } else {
                $search_query = 'result.'.$search_type.' LIKE "'.$request->search_query.'"';
            }

            $result = DB::select('SELECT * FROM (SELECT
                                        scm.id as subscription_id,
                                        psm.user_id,
                                        COALESCE(ud.first_name,"") AS first_name,
                                        COALESCE(psm.txn_id,"") AS transaction_id,
                                        COALESCE(scm.paypal_id,"") AS paypal_id,
                                        COALESCE(scm.subscr_type,0) AS subscr_type,
                                        COALESCE(scm.txn_type,"") AS txn_type,
                                        COALESCE(scm.payment_status,"") AS payment_status,
                                        COALESCE(scm.total_amount,0) AS total_amount,
                                        IF(scm.subscr_type = "1" || scm.subscr_type = "2" || scm.subscr_type = "3" || scm.subscr_type = "4", "USD" , "INR")  AS currency,
                                        COALESCE(scm.activation_time,"") AS activation_time,
                                        COALESCE(scm.expiration_time,"") AS expiration_time,
                                        COALESCE(scm.payment_date,"") AS payment_date,
                                        COALESCE(scm.final_expiration_time,"") AS final_expiration_time,
                                        COALESCE(scm.cancellation_date,"") AS cancellation_date,
                                        COALESCE(scm.is_active,"") AS is_active,
                                        COALESCE(scm.create_time,"") AS create_time,
                                        COALESCE(psm.ipn_status,0) AS ipn_status,
                                        COALESCE(psm.is_active,0) AS psm_is_active,
                                        COALESCE(psm.paypal_status,0) AS paypal_status,
                                        COALESCE(psm.paypal_payment_status,"") AS paypal_payment_status,
                                        COALESCE(psm.verify_status,0) AS verify_status,
                                        COALESCE(scm.update_time,"") AS update_time
                                      FROM
                                        payment_status_master AS psm LEFT JOIN
                                        subscriptions AS scm ON scm.transaction_id = psm.txn_id LEFT JOIN
                                        user_detail AS ud ON psm.user_id=ud.user_id UNION
                                      SELECT
                                        scm.id as subscription_id,
                                        scm.user_id,
                                        COALESCE(ud.first_name,"") AS first_name,
                                        COALESCE(scm.transaction_id,"") AS transaction_id,
                                        COALESCE(scm.paypal_id,"") AS paypal_id,
                                        COALESCE(scm.subscr_type,0) AS subscr_type,
                                        COALESCE(scm.txn_type,"") AS txn_type,
                                        COALESCE(scm.payment_status,"") AS payment_status,
                                        COALESCE(scm.total_amount,0) AS total_amount,
                                        IF(scm.subscr_type = "1" || scm.subscr_type = "2" || scm.subscr_type = "3" || scm.subscr_type = "4", "USD" , "INR")  AS currency,
                                        COALESCE(scm.activation_time,"") AS activation_time,
                                        COALESCE(scm.expiration_time,"") AS expiration_time,
                                        COALESCE(scm.payment_date,"") AS payment_date,
                                        COALESCE(scm.final_expiration_time,"") AS final_expiration_time,
                                        COALESCE(scm.cancellation_date,"") AS cancellation_date,
                                        COALESCE(scm.is_active,"") AS is_active,
                                        COALESCE(scm.create_time,"") AS create_time,
                                        COALESCE(psm.ipn_status,0) AS ipn_status,
                                        COALESCE(psm.is_active,0) AS psm_is_active,
                                        COALESCE(psm.paypal_status,0) AS paypal_status,
                                        COALESCE(psm.paypal_payment_status,"") AS paypal_payment_status,
                                        COALESCE(psm.verify_status,0) AS verify_status,
                                        COALESCE(scm.update_time,"") AS update_time
                                      FROM
                                        subscriptions AS scm LEFT JOIN
                                        payment_status_master AS psm ON scm.transaction_id = psm.txn_id LEFT JOIN
                                        user_detail AS ud ON scm.user_id=ud.user_id) AS result
                                      WHERE '.$search_query);
            //                                      WHERE result.' . $search_type . ' LIKE ?', [$search_query]);

            $response = Response::json(['code' => 200, 'message' => 'Transactions fetched successfully.', 'cause' => '', 'data' => ['result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('searchTransaction', $e);
            //      Log::error("searchTransaction : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search transaction.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} verifyTransaction verifyTransaction
     *
     * @apiName verifyTransaction
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
     * "txn_id":"3VM06099G18139837" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Transaction details fetched successfully.",
     * "cause": "",
     * "data": {
     * "RECEIVERBUSINESS": "pmpatel1415160@gmail.com",
     * "RECEIVEREMAIL": "pmpatel1415160@gmail.com",
     * "RECEIVERID": "RN5562U7KC3GQ",
     * "EMAIL": "bella@grr.la",
     * "PAYERID": "MQ2WNUZJRETME",
     * "PAYERSTATUS": "unverified",
     * "COUNTRYCODE": "IN",
     * "ADDRESSOWNER": "PayPal",
     * "ADDRESSSTATUS": "None",
     * "CUSTOM": "7",
     * "SUBJECT": "Yearly Starter",
     * "GIFTRECEIPT": "0",
     * "TIMESTAMP": "2018-11-17T03:47:45Z",
     * "CORRELATIONID": "ba583b45cf3f5",
     * "ACK": "Success",
     * "VERSION": "70.0",
     * "BUILD": "46457558",
     * "FIRSTNAME": "Bella",
     * "LASTNAME": "Swan",
     * "TRANSACTIONID": "3VM06099G18139837",
     * "TRANSACTIONTYPE": "subscrpayment",
     * "PAYMENTTYPE": "instant",
     * "ORDERTIME": "2018-11-16T11:16:11Z",
     * "AMT": "108.00",
     * "FEEAMT": "4.51",
     * "CURRENCYCODE": "USD",
     * "PAYMENTSTATUS": "Completed",
     * "PENDINGREASON": "None",
     * "REASONCODE": "None",
     * "PROTECTIONELIGIBILITY": "Eligible",
     * "PROTECTIONELIGIBILITYTYPE": "ItemNotReceivedEligible,UnauthorizedPaymentEligible",
     * "L_NAME0": "Yearly Starter",
     * "L_NUMBER0": "2",
     * "L_CURRENCYCODE0": "USD",
     * "SUBSCRIPTIONID": "I-1PEC0G9K4W4J",
     * "RECURRENCES": "0",
     * "INSURANCEOPTIONSELECTED": "0",
     * "SHIPPINGOPTIONISDEFAULT": "0"
     * }
     * }
     */
    public function verifyTransaction(Request $request)
    {
        //$curl_response = "NA";
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_id = JWTAuth::toUser($token)->id;
            $client = new Client();

            $request = json_decode($request->getContent());
            //Log::info("getFeaturedCatalogBySubCategoryId Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['txn_id'], $request)) != '') {
                return $response;
            }

            $txn_id = $request->txn_id;
            $is_confirmed = 0;
            $user_sub_type = DB::select('SELECT payment_type,payment_status,subscr_type FROM subscriptions WHERE transaction_id = ?', [$txn_id]);

            if (count($user_sub_type) > 0) {
                $sub_type = $user_sub_type[0]->payment_type;
                $is_confirmed = 1;
                //1 = Paypal, 2 = Stripe, 3 = Fastspring
                if ($sub_type == 1) {

                    $req = [
                        'user' => Config::get('constant.PAYPAL_API_USER'),
                        'pwd' => Config::get('constant.PAYPAL_API_PASSWORD'),
                        'signature' => Config::get('constant.PAYPAL_API_SIGNATURE'),
                        'version' => '70.0',
                        'METHOD' => 'GetTransactionDetails',
                        'TRANSACTIONID' => urlencode($txn_id),
                        'NOTE' => 'Fetch transaction detail',
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
                        return $response = Response::json(['code' => 201, 'message' => 'Unable to fetch transaction detail. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);

                    } else {
                        curl_close($ch);
                        parse_str($curl_response, $result); //convert encoded url to string
                        //$result = json_encode($result);

                    }

                    if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $curl_response, $matches)) {
                        foreach ($matches['name'] as $offset => $name) {
                            $nvp[$name] = urldecode($matches['value'][$offset]);
                        }
                    }

                    $paypal_ACK = (isset($nvp['ACK'])) ? $nvp['ACK'] : 'Error';

                    if (strcmp($paypal_ACK, 'Error') == 0 || strcmp($paypal_ACK, 'Failure') == 0) {
                        Log::error('verifyTransaction : ', ['error' => $nvp['L_SHORTMESSAGE0']]);
                        //throw new Exception("Paypal error:" . $nvp['L_SHORTMESSAGE0']);
                        return $response = Response::json(['code' => 201, 'message' => 'Unable to fetch transaction detail. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                } elseif ($sub_type == 2) {
                    //stripe_subscription

                    \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));
                    $stripe_subscription = \Stripe\Subscription::retrieve(
                        $txn_id
                    );
                    $sub_object = $stripe_subscription->object;
                    $status = $stripe_subscription->status;
                    $collection_method = $stripe_subscription->collection_method;
                    $customer_id = $stripe_subscription->customer;

                    $plan = $stripe_subscription->plan;
                    $plan_id = $plan->id;
                    $subscription_name = $plan->nickname;
                    $currency = $plan->currency;
                    $amount = $plan->amount / 100;

                    if (isset($subscription->discount)) {
                        $discount = $stripe_subscription->discount;
                        if (isset($discount->coupon)) {
                            $coupon = $discount->coupon;
                            $percent_off = $coupon->percent_off;
                            $feeamount = $amount * $percent_off / 100;
                        }
                    }

                    //              Log::info('Transaction verification  : ',[$stripe_subscription]);

                    $user_sub_result = DB::select('SELECT um.email_id, ud.first_name, ud.last_name
                                                          FROM user_master AS um,
                                                          subscriptions AS sub,
                                                          user_detail AS ud
                                                          WHERE sub.user_id = um.id AND
                                                          ud.user_id = um.id AND sub.transaction_id = ?
                                                          ORDER BY sub.update_time DESC',
                        [$txn_id]);
                    if (count($user_sub_result) > 0) {
                        $email_id = $user_sub_result[0]->email_id;
                        $first_name = $user_sub_result[0]->first_name;
                        $last_name = $user_sub_result[0]->last_name;
                    } else {
                        $user_sub_details = DB::select('SELECT sub.first_name
                                                          FROM
                                                          subscriptions AS sub
                                                          WHERE sub.transaction_id = ?
                                                          ORDER BY sub.update_time DESC',
                            [$txn_id]);

                        $email_id = 'Not applicable';
                        $first_name = $user_sub_details[0]->first_name;
                        $last_name = 'Not applicable';
                    }

                    $result = ['RECEIVERBUSINESS' => 'Not applicable',
                        'RECEIVEREMAIL' => 'Not applicable',
                        'RECEIVERID' => 'Not applicable',
                        'EMAIL' => $email_id,
                        'PAYERID' => $customer_id, //customer_id
                        'PAYERSTATUS' => $status,
                        'COUNTRYCODE' => 'Not applicable',
                        'ADDRESSOWNER' => 'Stripe',
                        'ADDRESSSTATUS' => 'None',
                        'CUSTOM' => 'Not applicable',
                        'SUBJECT' => $subscription_name,
                        'GIFTRECEIPT' => '0',
                        'TIMESTAMP' => 'Not applicable',
                        'CORRELATIONID' => 'Not applicable',
                        'ACK' => $status,
                        'VERSION' => '0',
                        'BUILD' => '0',
                        'FIRSTNAME' => $first_name,
                        'LASTNAME' => $last_name,
                        'TRANSACTIONID' => $txn_id,
                        'TRANSACTIONTYPE' => $sub_object,
                        'PAYMENTTYPE' => 'recurring',
                        'ORDERTIME' => 'None',
                        'AMT' => $amount,
                        'FEEAMT' => (isset($feeamount)) ? $feeamount : '0',
                        'CURRENCYCODE' => $currency,
                        'PAYMENTSTATUS' => $status,
                        'PENDINGREASON' => 'None',
                        'REASONCODE' => 'None',
                        'PROTECTIONELIGIBILITY' => 'None',
                        'PROTECTIONELIGIBILITYTYPE' => 'None',
                        'L_NAME0' => $subscription_name,
                        'L_NUMBER0' => $plan_id,
                        'L_CURRENCYCODE0' => $currency,
                        'SUBSCRIPTIONID' => $txn_id,
                        'RECURRENCES' => $collection_method,
                        'INSURANCEOPTIONSELECTED' => '0',
                        'SHIPPINGOPTIONISDEFAULT' => '0',
                        'is_confirmed' => '0'];

                } elseif ($sub_type == 3) {

                    $get_order_detail_url = Config::get('constant.FASTSPRING_API_URL').Config::get('constant.FASTSPRING_ORDERS_API_NAME').$txn_id;
                    $username = Config::get('constant.FASTSPRING_API_USER_NAME');
                    $password = Config::get('constant.FASTSPRING_API_PASSWORD');

                    $response = $client->get($get_order_detail_url, ['auth' => [$username, $password]]);
                    $api_response = json_decode($response->getBody()->getContents());

                    $result = [
                        'ACK' => $user_sub_type[0]->payment_status,
                        'ADDRESSOWNER' => 'FastSpring',
                        'ADDRESSSTATUS' => 'None',
                        'AMT' => $api_response->subtotal,
                        'BUILD' => '0',
                        'CORRELATIONID' => 'Not applicable',
                        'COUNTRYCODE' => $api_response->address->country,
                        'CURRENCYCODE' => $api_response->currency,
                        'CUSTOM' => 'Not applicable',
                        'EMAIL' => $api_response->customer->email,
                        'FEEAMT' => $api_response->tax,
                        'FIRSTNAME' => $api_response->customer->first,
                        'GIFTRECEIPT' => '0',
                        'INSURANCEOPTIONSELECTED' => '0',
                        'LASTNAME' => $api_response->customer->last,
                        'L_CURRENCYCODE0' => $api_response->currency,
                        'L_NAME0' => $api_response->items[0]->display,
                        'L_NUMBER0' => $user_sub_type[0]->subscr_type,
                        'ORDERTIME' => $api_response->changedDisplay,
                        'PAYERID' => $api_response->account,
                        'PAYERSTATUS' => $user_sub_type[0]->payment_status,
                        'PAYMENTSTATUS' => $user_sub_type[0]->payment_status,
                        'PAYMENTTYPE' => 'recurring',
                        'PENDINGREASON' => 'None',
                        'PROTECTIONELIGIBILITY' => 'None',
                        'PROTECTIONELIGIBILITYTYPE' => 'None',
                        'REASONCODE' => 'None',
                        'RECEIVERBUSINESS' => 'Not applicable',
                        'RECEIVEREMAIL' => 'Not applicable',
                        'RECEIVERID' => 'Not applicable',
                        'RECURRENCES' => 'charge_automatically',
                        'RETURNAMT' => isset($api_response->returns) ? $api_response->returns[0]->amount : 0,
                        'SHIPPINGOPTIONISDEFAULT' => '0',
                        'SUBJECT' => $api_response->items[0]->display,
                        'SUBSCRIPTIONID' => isset($api_response->items[0]->subscription) ? $api_response->items[0]->subscription : 'Not applicable',
                        'TIMESTAMP' => $api_response->changed,
                        'TRANSACTIONID' => $txn_id,
                        'TRANSACTIONTYPE' => isset($api_response->items[0]->subscription) ? 'subscription' : 'subscription-false',
                        'VERSION' => '0',
                    ];

                } else {
                    return $response = Response::json(['code' => 201, 'message' => 'Unable to fetch transaction source.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            } else {

                $req = [
                    'user' => Config::get('constant.PAYPAL_API_USER'),
                    'pwd' => Config::get('constant.PAYPAL_API_PASSWORD'),
                    'signature' => Config::get('constant.PAYPAL_API_SIGNATURE'),
                    'version' => '70.0',
                    'METHOD' => 'GetTransactionDetails',
                    'TRANSACTIONID' => urlencode($txn_id),
                    'NOTE' => 'Fetch transaction detail',
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
                    return $response = Response::json(['code' => 201, 'message' => 'Unable to fetch transaction detail. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);

                } else {
                    curl_close($ch);
                    parse_str($curl_response, $result); //convert encoded url to string
                    //$result = json_encode($result);

                }

                if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $curl_response, $matches)) {
                    foreach ($matches['name'] as $offset => $name) {
                        $nvp[$name] = urldecode($matches['value'][$offset]);
                    }
                }

                $paypal_ACK = (isset($nvp['ACK'])) ? $nvp['ACK'] : 'Error';

                if (strcmp($paypal_ACK, 'Error') == 0 || strcmp($paypal_ACK, 'Failure') == 0) {
                    Log::error('verifyTransaction : ', ['error' => $nvp['L_SHORTMESSAGE0']]);
                    //throw new Exception("Paypal error:" . $nvp['L_SHORTMESSAGE0']);
                    return $response = Response::json(['code' => 201, 'message' => 'Unable to fetch transaction detail. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                }

            }

            $result['is_confirmed'] = $is_confirmed;

            $response = Response::json(['code' => 200, 'message' => 'Transaction details fetched successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('verifyTransaction', $e);
            //      Log::error("verifyTransaction : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'verify transaction.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} confirmPaymentByAdmin confirmPaymentByAdmin
     *
     * @apiName confirmPaymentByAdmin
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
     * "RECEIVERBUSINESS": "help.photoadking@gmail.com",
     * "RECEIVEREMAIL": "help.photoadking@gmail.com",
     * "RECEIVERID": "4BAPTVSDVF6Q8",
     * "EMAIL": "buyer3.photoadking@gmail.com", //compulsory
     * "PAYERID": "BL5HLLB5L5A5L",
     * "PAYERSTATUS": "verified",
     * "COUNTRYCODE": "US",
     * "ADDRESSOWNER": "PayPal",
     * "ADDRESSSTATUS": "None",
     * "CUSTOM": "2", //compulsory
     * "SUBJECT": "Monthly Starter",
     * "GIFTRECEIPT": "0",
     * "TIMESTAMP": "2018-12-24T04:10:48Z",
     * "CORRELATIONID": "cc34cd18eb321",
     * "ACK": "Success",
     * "VERSION": "70.0",
     * "BUILD": "46457558",
     * "FIRSTNAME": "BuyerC", //compulsory
     * "LASTNAME": "BuyerC",
     * "TRANSACTIONID": "83425351BS097704U", //compulsory
     * "TRANSACTIONTYPE": "subscrpayment", //compulsory
     * "PAYMENTTYPE": "instant",
     * "ORDERTIME": "2018-12-21T09:53:12Z", //compulsory
     * "AMT": "12.00", //compulsory
     * "FEEAMT": "0.91",
     * "CURRENCYCODE": "USD", //compulsory
     * "PAYMENTSTATUS": "Completed", //compulsory
     * "PENDINGREASON": "None",
     * "REASONCODE": "None",
     * "PROTECTIONELIGIBILITY": "Eligible",
     * "PROTECTIONELIGIBILITYTYPE": "ItemNotReceivedEligible,UnauthorizedPaymentEligible",
     * "L_NAME0": "Monthly Starter", //compulsory
     * "L_NUMBER0": "1", //compulsory
     * "L_CURRENCYCODE0": "USD",
     * "SUBSCRIPTIONID": "I-72C67HB40LW0", //compulsory
     * "RECURRENCES": "0",
     * "INSURANCEOPTIONSELECTED": "0",
     * "SHIPPINGOPTIONISDEFAULT": "0"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Payment confirmed successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function confirmPaymentByAdmin(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter([
                'TRANSACTIONTYPE',
                'CUSTOM',
                'TRANSACTIONID',
                'SUBSCRIPTIONID',
                'ORDERTIME',
                'AMT',
                'EMAIL',
                'CURRENCYCODE',
                'PAYMENTSTATUS',
                'L_NUMBER0',
                'L_NAME0',
                'FIRSTNAME',
                'COUNTRYCODE',
            ], $request)) != ''
            ) {
                return $response;
            }

            $txn_type = $request->TRANSACTIONTYPE;
            $user_id = $request->CUSTOM;
            if (! is_numeric($user_id)) {
                $user_detail = DB::select('SELECT id FROM user_master WHERE uuid=?', [$user_id]);
                if (count($user_detail) > 0) {
                    $user_id = $user_detail[0]->id;
                } else {
                    return Response::json(['code' => 201, 'message' => 'User not found.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }
            $txn_id = $request->TRANSACTIONID;
            $subscr_id = $request->SUBSCRIPTIONID;
            $subscr_date = date('Y-m-d H:i:s', strtotime($request->ORDERTIME));
            $mc_amount3 = $request->AMT;
            $payer_email = $request->EMAIL;
            $mc_currency = $request->CURRENCYCODE;
            $payment_status = $request->PAYMENTSTATUS;
            $item_number = $request->L_NUMBER0;
            $item_name = $request->L_NAME0;
            $first_name = $request->FIRSTNAME;
            $country_code = $request->COUNTRYCODE;

            if (($response = (new VerificationController())->checkIsSubscriptionIdExist($subscr_id)) != '') {
                return $response;
            }

            $subscr_type_of_monthly_starter = Config::get('constant.MONTHLY_STARTER');
            $subscr_type_of_yearly_starter = Config::get('constant.YEARLY_STARTER');
            $subscr_type_of_monthly_pro = Config::get('constant.MONTHLY_PRO');
            $subscr_type_of_yearly_pro = Config::get('constant.YEARLY_PRO');

            if ($item_number == $subscr_type_of_monthly_starter or $item_number == $subscr_type_of_monthly_pro) {

                $date = new DateTime($subscr_date);
                $date->modify('+1 Month');
                $expires = $date->format('Y-m-d H:i:s');

                //$expires = date('Y-m-d H:i:s', strtotime('+1 Month'));
            } elseif ($item_number == $subscr_type_of_yearly_starter or $item_number == $subscr_type_of_yearly_pro) {

                $date = new DateTime($subscr_date);
                $date->modify('+1 Year');
                $expires = $date->format('Y-m-d H:i:s');
                //$expires = date('Y-m-d H:i:s', strtotime('+1 Year'));
            } else {
                $expires = null;
            }
            $subscr_date_local = (new ImageController())->convertUTCDateTimeInToLocal($subscr_date, $country_code);
            $expires_time_local = (new ImageController())->convertUTCDateTimeInToLocal($expires, $country_code);

            $txn = [
                'txn_id' => $txn_id,
                'user_id' => $user_id,
                'txn_type' => $txn_type,
                'paypal_id' => $subscr_id,
                'subscr_date' => $subscr_date,
                'subscr_date_local' => $subscr_date_local,
                'mc_amount3' => $mc_amount3,
                'payer_email' => $payer_email,
                'mc_currency' => $mc_currency,
                'period1' => 'NULL',
                'expires' => $expires,
                'expires_local' => $expires_time_local,
                'payment_status' => $payment_status,
                'subscr_type' => $item_number,
                'paypal_response' => json_encode($request),
                'create_time' => date('Y-m-d H:i:s'),
                'item_name' => $item_name,
                'first_name' => $first_name,
            ];
            //return $txn;

            Log::info('-----new payment-----', ['Report' => $txn]);

            if ($txn) {

                if (strcmp($txn_type, 'subscrpayment') == 0) {
                    $txn['txn_type'] = 'subscr_payment';

                    if (strcmp($payment_status, 'Completed') == 0) {
                        (new PaypalIPNController())->updatePaymentDetailByUserID($user_id, $txn);
                    } else {
                        Log::debug('paypalIpn-Verified : ', ['txn_type' => 'subscr_payment', 'txt' => $txn]);
                    }
                    (new PaypalIPNController())->logPaypalIPN($user_id, $txn);
                    Log::debug('logPaypalIPN paypalIpn-Verified : ', ['txn_type' => 'subscr_payment', 'txt' => $txn]);

                } elseif (strcmp($txn_type, 'subscrfailed') == 0) {
                    $txn['txn_type'] = 'subscr_failed';

                    (new PaypalIPNController())->logPaypalIPN($user_id, $txn);
                    Log::debug('paypalIpn-subscr_failed : ', ['paypal_id' => $subscr_id]);

                    if ($user_id != 'NA' or $user_id != '') {
                        $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
                        $email_id = $user_profile->email_id;
                        $first_name = $user_profile->first_name;

                        $template = 'payment_failed';
                        $subject = 'PhotoADKing: Payment Failed';
                        $message_body = [
                            'message' => 'Sorry, your payment failed. No charges were made. Following are the transaction details.',
                            'subscription_name' => $txn['item_name'],
                            'txn_id' => $txn['txn_id'],
                            'txn_type' => 'Subscription',
                            'subscr_id' => $txn['paypal_id'],
                            'first_name' => $first_name,
                            'payment_received_from' => $txn['first_name'].' ('.$txn['payer_email'].')',
                            'total_amount' => $txn['mc_amount3'],
                            'mc_currency' => $txn['mc_currency'],
                            'payer_email' => $txn['payer_email'],
                            'payment_status' => $txn['payment_status'],
                        ];
                        $api_name = 'confirmPaymentByAdmin';
                        $api_description = 'Subscription failed.';

                        $this->dispatch(new EmailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    } else {
                        Log::debug('paypalIpn-subscr_failed did not get user_id : ', ['data' => $txn]);

                        $admin_email_id = Config::get('constant.ADMIN_EMAIL_ID');
                        $template = 'simple';
                        $subject = 'PhotoADKing: PayPal IPN Failed';
                        $message_body = [
                            'message' => '<p>API "paypalIpn" could not fetch user_id from IPN response in case of transaction type is subscr_failed. Please check the logs.</p>',
                            'user_name' => 'Admin',
                        ];
                        $api_name = 'confirmPaymentByAdmin';
                        $api_description = 'Get INVALID from IPN.';
                        $this->dispatch(new EmailJob(1, $admin_email_id, $subject, $message_body, $template, $api_name, $api_description));

                    }
                } else {
                    return $response = Response::json(['code' => 201, 'message' => 'You can only confirm payment if transaction type is subscr_payment.', 'cause' => '', 'data' => json_decode('{}')]);

                }
            } else {
                return $response = Response::json(['code' => 201, 'message' => 'Transaction details is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            $response = Response::json(['code' => 200, 'message' => 'Payment confirmed successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('confirmPaymentByAdmin', $e);
            //      Log::error("confirmPaymentByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'confirm payment.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;

    }

    /* =========================================| Get Old Font (Non-commercial) |=========================================*/

    /**
     * @api {post} getSamplesOfNonCommercialFont   getSamplesOfNonCommercialFont
     *
     * @apiName getSamplesOfNonCommercialFont
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
     *  "catalog_id":1, //compulsory
     *  "order_by":1,
     *  "order_type":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Catalog Images Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "image_list": [
     * {
     * "img_id": 360,
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5a169952c71b0_catalog_image_1511430482.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5a169952c71b0_catalog_image_1511430482.jpg",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5a169952c71b0_catalog_image_1511430482.jpg",
     * "is_json_data": 0,
     * "json_data": "",
     * "is_featured": "",
     * "is_free": 0
     * },
     * {
     * "img_id": 359,
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5a1697482f0a2_json_image_1511429960.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5a1697482f0a2_json_image_1511429960.jpg",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5a1697482f0a2_json_image_1511429960.jpg",
     * "is_json_data": 1,
     * "json_data": "test",
     * "is_featured": "0",
     * "is_free": 0
     * },
     * {
     * "img_id": 352,
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5a0d7f290a6df_catalog_image_1510833961.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5a0d7f290a6df_catalog_image_1510833961.jpg",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5a0d7f290a6df_catalog_image_1510833961.jpg",
     * "is_json_data": 1,
     * "json_data": {
     * "text_json": [],
     * "sticker_json": [],
     * "image_sticker_json": [
     * {
     * "xPos": 440,
     * "yPos": 0,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 210,
     * "width": 210
     * },
     * {
     * "xPos": 0,
     * "yPos": 211,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 270,
     * "width": 430
     * },
     * {
     * "xPos": 353,
     * "yPos": 439,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 320,
     * "width": 297
     * }
     * ],
     * "frame_json": {
     * "frame_image": "frame_1.6.png"
     * },
     * "background_json": {},
     * "sample_image": "sample_1.6.jpg",
     * "height": 800,
     * "width": 650,
     * "is_featured": 0
     * },
     * "is_featured": "0",
     * "is_free": 1
     * },
     * {
     * "img_id": 355,
     * "thumbnail_img": "http://192.168.0.113/photoadking_testing/image_bucket/thumbnail/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "compressed_img": "http://192.168.0.113/photoadking_testing/image_bucket/compressed/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "original_img": "http://192.168.0.113/photoadking_testing/image_bucket/original/5a0d7faa3b1bc_catalog_image_1510834090.jpg",
     * "is_json_data": 1,
     * "json_data": {
     * "text_json": [],
     * "sticker_json": [],
     * "image_sticker_json": [
     * {
     * "xPos": 0,
     * "yPos": 0,
     * "image_sticker_image": "",
     * "angle": 0,
     * "is_round": 0,
     * "height": 800,
     * "width": 500
     * }
     * ],
     * "frame_json": {
     * "frame_image": "frame_15.7.png"
     * },
     * "background_json": {},
     * "sample_image": "sample_15.7.jpg",
     * "is_featured": 0,
     * "height": 800,
     * "width": 800
     * },
     * "is_featured": "1",
     * "is_free": 1
     * }
     * ]
     * }
     * }
     */
    public function getSamplesOfNonCommercialFont()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            /*define follwoing variables into constant before use this API

                    'NON_COMMERCIAL_FONT_PATH' => "fonts/American Typewriter Condensed.ttf,fonts/style10.ttf,fonts/Blanch Condensed Inline.ttf,fonts/CoronetLTStd-Bold.ttf,fonts/daunpenh.ttf,fonts/Filxgirl.TTF,fonts/LFAX.TTF,fonts/LFAXI.TTF,fonts/ufonts.com_lydian-cursive-bt.ttf,fonts/Medusa Gothic.otf,fonts/PrestigeEliteStd-Bd.otf,fonts/VAGRoundedStd-Bold.ttf,fonts/VAGRoundedStd-Light.ttf",
            'NON_COMMERCIAL_FONT_NAME' => "AmericanTypewriter-Condensed,BacktoBlackDemo,Blanch-CondensedInline,CoronetLTStd-Bold,DaunPenh,FiolexGirls-Regular,LucidaFax,LucidaFax-Italic,LydianCursiveBT-Regular,MedusaGothic,PrestigeEliteStd-Bd,VAGRoundedStd-Bold,VAGRoundedStd-Light"

                    */

            $non_commercial_fonts_android = explode(',', Config::get('constant.NON_COMMERCIAL_FONT_PATH')); //get non-commercial fonts using fontPath
            $non_commercial_fonts_ios = explode(',', Config::get('constant.NON_COMMERCIAL_FONT_NAME')); //get non-commercial fonts using fontName

            $json_list_of_android = []; //array of json which contains non-commercial fonts (android)
            $json_list_of_ios = []; //array of json which contains non-commercial fonts (ios)
            $json_list_of_android_new = [];
            foreach ($non_commercial_fonts_android as $key) {

                $list_of_android_match = DB::select('SELECT
                                      DISTINCT id
                                    FROM images
                                    WHERE
                                      JSON_SEARCH(json_data,"all",?, NULL , "$.text_json[*].fontPath") IS NOT NULL', [$key]);

                foreach ($list_of_android_match as $id) {
                    //$json_list_of_android[] = $id;
                    $json_list_of_android[] = $id;

                }

            }
            //return $json_list_of_android;

            foreach ($non_commercial_fonts_ios as $key) {

                $list_of_ios_match = DB::select('SELECT
                                      DISTINCT id
                                    FROM images
                                    WHERE
                                      JSON_SEARCH(json_data,"all",?, NULL , "$.text_json[*].fontName") IS NOT NULL', [$key]);

                /*$list_of_ios_match = DB::select('SELECT
                                              DISTINCT id,
                                              IF(image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",image),"") as original_img
                                            FROM images
                                            WHERE
                                              JSON_SEARCH(json_data,"all",?, NULL , "$.text_json[*].fontName") IS NOT NULL', [$key]);*/

                foreach ($list_of_ios_match as $id) {
                    //$json_list_of_ios[] = $id->img_ids;
                    if (! in_array($id, $json_list_of_android)) {
                        $json_list_of_ios[] = $id;
                    }

                }

            }

            //return $json_list_of_ios;

            //return array_merge($json_list_of_android, $json_list_of_ios);

            $result = array_merge($json_list_of_android, $json_list_of_ios);
            $not_existed_id = array_unique($result);

            $response = Response::json(['code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => ['result' => $not_existed_id]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getSamplesOfNonCommercialFont', $e);
            //      Log::error("getSamplesOfNonCommercialFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =========================================| Font Module |=========================================*/

    /**
     * @api {post} addFont   addFont
     *
     * @apiName addFont
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
     * request_data:{//all parameters are compulsory
     * "category_id":6,
     * "catalog_id":280,
     * "ios_font_name":"3d", //optional
     * "is_replace":1, //1=replace font file, 0=don't replace font file
     * "is_featured":1 //1=featured catalog, 0=normal catalog
     * }
     * file:3d.ttf //compulsory
     * font_json_file:3d.json //compulsory
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addFont(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'catalog_id',
                'search_tags',
                'is_replace',
                'is_featured',
            ], $request)) != ''
            ) {
                return $response;
            }

            $category_id = $request->category_id;
            $catalog_id = $request->catalog_id;
            $search_tags = strtolower($request->search_tags);
            $font_type = isset($request->font_type) ? trim($request->font_type) : null;
            $is_replace = $request->is_replace;
            $is_featured = $request->is_featured;
            $issue_code = isset($request->issue_code) ? $request->issue_code : null;
            $is_catalog = 0; //Here we are passed 1 bcz this is not image of catalog, this is font file
            $create_at = date('Y-m-d H:i:s');

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $file_array = Input::file('file');
            if (($response = (new ImageController())->verifyFontFile($file_array, $category_id, $is_featured, $is_catalog)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('font_json_file')) {
                return Response::json(['code' => 201, 'message' => 'Required field font_json_file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $font_json_file = Input::file('font_json_file');
            if (($response = (new ImageController())->verifyFontJsonFile($font_json_file)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('font_preview_image')) {
                return Response::json(['code' => 201, 'message' => 'Required field font_preview_image is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($is_replace == 0) {
                if (($response = (new VerificationController())->checkIsFontExistForAdmin($file_array)) != '') {
                    return $response;
                }
                $file_name = str_replace(' ', '', strtolower($file_array->getClientOriginalName()));
            } else {
                $file_name = $file_array->getClientOriginalName();
            }
            $font_name = (new ImageController())->saveFontFile($file_name, $is_replace);

            $preview_img_name = (new ImageController())->generateNewFileNameForPNG('font_preview_image');
            (new ImageController())->saveFontPreviewImageFile($preview_img_name);

            $json_file_name = (new ImageController())->generateFontJsonFileName($file_name);
            (new ImageController())->saveFontJsonFile($json_file_name);

            if (config('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveFontInToS3($file_name, $preview_img_name);
                (new ImageController())->saveFontJsonFileInToS3($json_file_name);
            }

            $android_font_name = "fonts/$file_name";
            $ios_font_name = isset($request->ios_font_name) ? $request->ios_font_name : $file_name;
            $uuid = (new ImageController())->generateUUID();

            DB::beginTransaction();
            DB::insert('INSERT INTO
                      font_master(
                        catalog_id,
                        uuid,
                        font_name,
                        font_file,
                        font_json_file,
                        search_tags,
                        font_type,
                        preview_image,
                        ios_font_name,
                        android_font_name,
                        issue_code,
                        is_active,
                        create_time) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ',
                [
                    $catalog_id,
                    $uuid,
                    $font_name,
                    $file_name,
                    $json_file_name,
                    $search_tags,
                    $font_type,
                    $preview_img_name,
                    $ios_font_name,
                    $android_font_name,
                    $issue_code,
                    1,
                    $create_at,
                ]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Font added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addFont', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'add font.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} editFont   editFont
     *
     * @apiName editFont
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
     * request_data:{
     * "font_id":1, //compulsory
     * "ios_font_name":"3d", //optional
     * "android_font_name":"3d.ttf" //optional
     * }
     * font_json_file:3d.json //optional
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font edited successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    /*
    => Android and iOS have some problems when using some fonts so we have to warn the our designers not to use that font, as any one of the following problems will occur while using these fonts.
    1. "Large Size: Single Line: Normal Padding",
    2. "Large Size: Single Line: Extra Padding",
    3. "Large Size: Multi Line: Normal Padding",
    4. "Small Size: Single Line: Normal Padding",
    5. "Small Size: Multi Line: Normal Padding",
    6. "In Between Space",
    7. "Alphabets Lowercase",
    8. "Alphabets Uppercase",
    9. "All Special Characters",
    10. "All Numbers(0 to 9)",
    11."Font cut ios",
    */
    public function editFont(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['font_id'], $request)) != '') {
                return $response;
            }

            $font_id = $request->font_id;
            $json_file_name = '';
            $issue_code_where_condition = '';
            $search_tags = isset($request->search_tags) ? strtolower($request->search_tags) : '';
            $font_type = isset($request->font_type) ? trim($request->font_type) : 1;
            $preview_img_name = '';

            if ($font_type != 1) {
                if (! $request_body->hasFile('font_preview_image')) {
                    return Response::json(['code' => 201, 'message' => 'Required field font_preview_image is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $preview_img_name = (new ImageController())->generateNewFileNameForPNG('font_preview_image');
                (new ImageController())->saveFontPreviewImageFile($preview_img_name);
            }

            if (isset($request->issue_code)) {

                if (($response = (new VerificationController())->validateRequiredParameter(['is_update_all', 'catalog_id'], $request)) != '') {
                    return $response;
                }

                $is_update_all = $request->is_update_all;
                $issue_code = isset($request->issue_code) ? $request->issue_code : '';
                $catalog_id = $request->catalog_id;
                $issue_code_where_condition = " , fm.issue_code = '$issue_code'";

                if ($is_update_all) {
                    DB::update('UPDATE font_master AS fm SET fm.issue_code = ? WHERE fm.catalog_id = ?', [$issue_code, $catalog_id]);
                }
            }

            $font_detail = DB::select('SELECT font_file,font_json_file,preview_image FROM font_master WHERE id = ?', [$font_id]);
            if (count($font_detail) > 0) {

                if ($preview_img_name) {
                    /* Delete old font preview image from S3 */
                    $old_preview_image = $font_detail[0]->preview_image;
                    (new ImageController())->deleteObjectFromS3($old_preview_image, 'font_preview_image');

                    /* Save new font preview image in S3 */
                    if (config('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveFontPreviewImageInToS3($preview_img_name);
                    }
                }

                $font_json_file = Input::file('font_json_file');
                if (! empty($font_json_file)) {
                    if (($response = (new ImageController())->verifyFontJsonFile($font_json_file)) != '') {
                        return $response;
                    }

                    $file_name = $font_detail[0]->font_file;
                    $old_json_file_name = $font_detail[0]->font_json_file;

                    $json_file_name = (new ImageController())->generateFontJsonFileName($file_name);
                    if (isset($old_json_file_name) && $old_json_file_name != '') {
                        (new ImageController())->unlinkFileFromLocalStorage($old_json_file_name, Config::get('constant.FONT_JSON_FILE_DIRECTORY'));
                    }
                    (new ImageController())->saveFontJsonFile($json_file_name);
                    if (config('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveFontJsonFileInToS3($json_file_name);
                    }
                }
                $ios_font_name = isset($request->ios_font_name) ? $request->ios_font_name : '';
                $android_font_name = isset($request->android_font_name) ? $request->android_font_name : '';

                DB::beginTransaction();
                DB::update('UPDATE font_master AS fm
                    SET
                      fm.ios_font_name = IF(? != "",?,fm.ios_font_name),
                      fm.android_font_name = IF(? != "",?,fm.android_font_name),
                      fm.font_json_file = IF(? != "",?,fm.font_json_file),
                      fm.search_tags = IF(? != "",?,fm.search_tags),
                      fm.font_type = IF(? != 1,?,fm.font_type),
                      fm.preview_image = IF(? != "",?,fm.preview_image)
                      '.$issue_code_where_condition.'
                    WHERE fm.id = ?', [$ios_font_name, $ios_font_name, $android_font_name, $android_font_name, $json_file_name, $json_file_name, $search_tags, $search_tags, $font_type, $font_type, $preview_img_name, $preview_img_name, $font_id]);
                DB::commit();

                $response = Response::json(['code' => 200, 'message' => 'Font edited successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Font does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('editFont', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'edit font.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteFont   deleteFont
     *
     * @apiName deleteFont
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
     * "font_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteFont(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['font_id'], $request)) != '') {
                return $response;
            }

            $font_id = $request->font_id;

            DB::beginTransaction();

            DB::delete('DELETE FROM font_master where id = ? ', [$font_id]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Font deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteFont', $e);
            //      Log::error("deleteFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete font.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllFontsByCatalogIdForAdmin   getAllFontsByCatalogIdForAdmin
     *
     * @apiName getAllFontsByCatalogIdForAdmin
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
     *  "catalog_id":1, //compulsory
     *  "order_by":"update_time", //optional
     *  "order_type":"DESC" //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Fonts fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_count": 1,
     * "result": [
     * {
     * "font_id": 1,
     * "font_name": "Felix Titling",
     * "font_file": "http://192.168.0.113/photoadking_testing/image_bucket/fonts/FELIXTI.TTF",
     * "font_json_file": "http://192.168.0.113/photoadking_testing/font_json/fonts/FELIXTI.json",
     * "ios_font_name": "FELIXTI.TTF",
     * "android_font_name": "fonts/FELIXTI.TTF",
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getAllFontsByCatalogIdForAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id'], $request)) != '') {
                return $response;
            }

            $this->catalog_id = $request->catalog_id;
            $this->order_by = isset($request->order_by) ? $request->order_by : 'update_time'; //field name
            $this->order_type = strtolower(isset($request->order_type) ? $request->order_type : 'desc'); //asc or desc

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllFontsByCatalogIdForAdmin$this->catalog_id:$this->order_by:$this->order_type")) {
                $result = Cache::rememberforever("getAllFontsByCatalogIdForAdmin$this->catalog_id:$this->order_by:$this->order_type", function () {

                    $result = DB::select('SELECT
                                  fm.id AS font_id,
                                  fm.font_name,
                                  fm.font_file AS font_file_name,
                                  IF(fm.font_file != "",CONCAT("'.Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",fm.font_file),"") AS font_file,
                                  IF(fm.font_json_file != "",CONCAT("'.Config::get('constant.FONT_JSON_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",fm.font_json_file),"") AS font_json_file,
                                  IF(fm.preview_image != "",CONCAT("'.Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN').'",fm.preview_image),"") AS preview_image,
                                  COALESCE(fm.search_tags,"") AS search_tags,
                                  COALESCE(fm.font_type,"") AS font_type,
                                  COALESCE(fm.ios_font_name,"") AS ios_font_name,
                                  COALESCE(fm.android_font_name,"") AS android_font_name,
                                  COALESCE(fm.issue_code, "") AS issue_code,
                                  fm.is_active
                                FROM
                                  font_master AS fm
                                WHERE
                                  fm.is_active = 1 AND
                                  fm.catalog_id = ?
                                ORDER BY fm.'.$this->order_by.' '.$this->order_type, [$this->catalog_id]);

                    return $result;
                });
            }
            $redis_result = Cache::get("getAllFontsByCatalogIdForAdmin$this->catalog_id:$this->order_by:$this->order_type");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Fonts fetched successfully.', 'cause' => '', 'data' => ['total_count' => count($redis_result), 'result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllFontsByCatalogIdForAdmin', $e);
            //      Log::error("getAllFontsByCatalogIdForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get fonts.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function generateSearchTagByAI(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['font_file_name'], $request)) != '') {
                return $response;
            }

            $font_file_name = $request->font_file_name;

            $prompt = "Role: You are a font database curator and tagging expert. You have wide range of knowledge about font style classification and shown on details of fonts.\r\n\r\nUser provided font file name : $font_file_name\r\n\r\nContext: The user provides a widely known font file name with extension .ttf or .otf, and you need to generate search tag related to that font so i can add that font in my database with that search tags. So user can search that font from database easily by searching that search tags.\r\n\r\nPrompt: Based on the provided font file name, Please provide a comma-separated list of search tags based on the following key points:\r\n\r\nClassification: (e.g., Serif, Sans-serif, Script, Display, Handwriting, etc.)\r\nStyle: (e.g., Regular, Italic, Bold, etc.)\r\nDesign Features: (e.g., Decorative, Handwritten, Geometric, etc.)\r\nMood: (e.g., Elegant, Serious, Playful, Fun, etc.)\r\nUsage: (e.g., Body text, Headings, Display, Logo, etc.)\r\nTheme: (e.g., Vintage, Fashion, Art, Retro, etc.)\r\nPopularity: (e.g., Trendy, Classic, Underrated, Timeless, etc.)\r\nHolidays: (e.g., Birthday, Tattoo, Party, Halloween, etc.)\r\n\r\nFor example if user have given Dekko-Regular.ttf as font file then results should include following tags:\r\n\r\nClassification: Handwriting, Script\r\nStyle: Regular\r\nDesign Features: Calligraphy, Casual, Informal, Clean\r\nMood: Friendly, Fun\r\nUsage: Logo, Text, Branding, Web\r\nTheme: Art\r\nPopularity: Classic\r\nHolidays: Party\r\n\r\nDekko, Regular, Font, Typeface, Handwritten, Handwriting, Script, Calligraphy, Casual, Friendly, Fun, Stylish, Informal, Clean, Text, Design, Logo, Display, Web, Print, Branding.\r\n\r\nPlease note that above search tags are for example don't use it as an options, use your wide expertise for final relevant result.\r\n\r\nGuidelines: \r\n1. Don't give response based on your assumptions, if you couldn't find any data then you can give failed response.\r\n2. Please ensure that all tags you provide are unique and directly relevant to the font style. Response should be different for each f\r\n3. Your tags should not be limited to the given examples; feel free to use your knowledge to include additional tags.give me maximum of 10 tags that are strictly relevant.\r\n\r\nPlease note that in search tags you should return wide range if tag that are related to given font. Don't limit your knowledge to the one in example\r\n\r\nProvide search tag in following success JSON format : \r\n\r\n{\r\n  \"code\": 200,\r\n  \"data\": {\r\n    \"classification\": \"\",\r\n    \"style\": \"\",\r\n    \"design\": \"\",\r\n    \"mood\": \"\",\r\n    \"usage\": \"\",\r\n    \"theme\": \"\",\r\n    \"popularity\": \"\",\r\n    \"holidays\": \"\"\r\n  }\r\n}\r\n\r\nIf the result cannot be found, please provide the following failed JSON format :\r\n\r\n{\r\n    \"code\": 201,\r\n    \"message\": \"The possible error message.\"\r\n}\r\n\r\n\r\n";

            $chatGpt_request = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $prompt,
                    ],
                ],
            ];

            $client = new Client();
            $response_client = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer '.config('constant.OPENAI_API_KEY'),
                ],
                'json' => $chatGpt_request,
            ]);

            $response_content = json_decode($response_client->getBody()->getContents(), true);
            $result = json_decode($response_content['choices'][0]['message']['content']);
            $code = $result->code;

            if ($code === 200) {
                $response = Response::json(['code' => 200, 'message' => 'Search tag generated successfully.', 'cause' => '', 'data' => $result->data]);
            } else {
                $response = Response::json(['code' => $code, 'message' => 'Unable to generate search tag.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('generateSearchTagByAI', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'generate search tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} generatePreviewImageForUploadedFonts   generatePreviewImageForUploadedFonts
     *
     * @apiName generatePreviewImageForUploadedFonts
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
     * "catalog_ids":"67,76", //compulsory
     * "font_name":"Text", //optional
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Font added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function generatePreviewImageForUploadedFonts(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter([
                'catalog_ids',
                'page',
                'item_count',
            ], $request)) != ''
            ) {
                return $response;
            }

            $catalog_ids = $request->catalog_ids;
            $font_name = isset($request->font_name) ? $request->font_name : '';
            $item_count = $request->item_count;
            $page = $request->page;
            $offset = ($page - 1) * $item_count;
            $remaining_images = [];
            $generated_images = [];
            $original_file_path = Config::get('constant.FONT_PREVIEW_IMAGE_FILE_DIRECTORY_OF_DIGITAL_OCEAN');

            if ($font_name == '') {

                $total_fonts = DB::select('SELECT count(*) AS total FROM
                                    font_master
                                  WHERE
                                    catalog_id NOT IN('.$catalog_ids.')
                                  ORDER BY update_time ASC');

                $font_list = DB::select('SELECT * FROM
                                    font_master
                                  WHERE
                                    catalog_id NOT IN('.$catalog_ids.')
                                  ORDER BY update_time ASC
                                  LIMIT ?,?', [$offset, $item_count]);

                foreach ($font_list as $key) {

                    $file_name = $key->font_file;
                    $font_name = $key->font_name;

                    try {
                        $source_font_file_dir = Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN');
                        $destination_font_file_dir = Config::get('constant.FONT_FILE_DIRECTORY');
                        (new ImageController())->saveSingleFileInToLocalFromS3($file_name, $source_font_file_dir, $destination_font_file_dir);

                        $preview_img_name = (new ImageController())->generateNewFileNameForPNG('font_preview_image');
                        (new ImageController())->generatePreviewImage($file_name, $preview_img_name, $font_name, 1);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveFontInToS3($file_name, $preview_img_name);
                        }

                        DB::beginTransaction();
                        DB::update('UPDATE
                        font_master
                      SET
                        preview_image = ?
                      WHERE id = ?', [$preview_img_name, $key->id]);
                        DB::commit();
                        $original_path = $original_file_path.$preview_img_name;
                        $generated_images[] = $original_path;

                    } catch (Exception $e) {
                        (new ImageController())->logs('generatePreviewImageForUploadedFonts', $e);
                        //            Log::error("generatePreviewImageForUploadedFonts unable to generate images : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                        $remaining_images[] = $key->file_name;
                    }

                }

            } else {
                $total_fonts = DB::select('SELECT count(*) AS total FROM
                                    font_master
                                  WHERE
                                    catalog_id IN('.$catalog_ids.')
                                  ORDER BY update_time ASC');

                $font_list = DB::select('SELECT * FROM
                                    font_master
                                  WHERE
                                    catalog_id IN('.$catalog_ids.')
                                  ORDER BY update_time ASC
                                  LIMIT ?,?', [$offset, $item_count]);

                foreach ($font_list as $key) {

                    $file_name = $key->font_file;
                    try {

                        $source_font_file_dir = Config::get('constant.FONT_FILE_DIRECTORY_OF_DIGITAL_OCEAN');
                        $destination_font_file_dir = Config::get('constant.FONT_FILE_DIRECTORY');
                        (new ImageController())->saveSingleFileInToLocalFromS3($file_name, $source_font_file_dir, $destination_font_file_dir);

                        $preview_img_name = (new ImageController())->generateNewFileNameForPNG('font_preview_image');
                        (new ImageController())->generatePreviewImage($file_name, $preview_img_name, $font_name, 1);

                        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                            (new ImageController())->saveFontInToS3($file_name, $preview_img_name);
                        }

                        DB::beginTransaction();
                        DB::update('UPDATE
                        font_master
                      SET
                        preview_image = ?
                      WHERE id = ?', [$preview_img_name, $key->id]);
                        DB::commit();
                        $original_path = $original_file_path.$preview_img_name;
                        $generated_images[] = $original_path;

                    } catch (Exception $e) {
                        (new ImageController())->logs('generatePreviewImageForUploadedFonts', $e);
                        //            Log::error("generatePreviewImageForUploadedFonts unable to generate images : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                        $remaining_images[] = $key->file_name;
                    }

                }

            }

            $result_array = ['total_records_to_update' => $total_fonts[0]->total, 'generated_images' => $generated_images, 'remaining_images' => $remaining_images];
            $result = json_decode(json_encode($result_array), true);

            $response = Response::json(['code' => 200, 'message' => 'Preview image generated successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('generatePreviewImageForUploadedFonts', $e);
            //      Log::error("generatePreviewImageForUploadedFonts : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate preview image.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function editStaticFontPathInToJson(Request $request)
    {
        try {

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['my_design_id'], $request)) != '') {
                return $response;
            }

            $my_design_id = $request->my_design_id;

            $result = DB::select('SELECT json_data FROM my_design_master WHERE id = ?', [$my_design_id]);
            $json = json_decode($result[0]->json_data);
            $updated_json = (new VerificationController())->getJsonWithUpdatedFontPath($json);
            $json = json_encode($updated_json);

            DB::beginTransaction();
            DB::update('UPDATE my_design_master SET json_data = ? WHERE id = ?', [$json, $my_design_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Json edited successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('editStaticFontPathInToJson', $e);
            //      Log::error("editStaticFontPathInToJson : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit static fontPath into json.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =================================| Set Rank of Catalogs & Templates |===================================*/

    /**
     * @api {post} setCatalogRankOnTheTopByAdmin setCatalogRankOnTheTopByAdmin
     *
     * @apiName setCatalogRankOnTheTopByAdmin
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
     *{
     * "catalog_id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    /*public function setCatalogRankOnTheTopByAdmin(Request $request_body)
    {
      try {

        $token = JWTAuth::getToken();
        JWTAuth::toUser($token);

        $request = json_decode($request_body->getContent());
        if (($response = (new VerificationController())->validateRequiredParameter(array('catalog_id'), $request)) != '')
          return $response;

        $catalog_id = $request->catalog_id;
        $create_time = date('Y-m-d H:i:s');
        DB::beginTransaction();
        DB::update('UPDATE
                                catalog_master
                                SET update_time = ?
                                WHERE
                                id = ?', [$create_time, $catalog_id]);
        DB::commit();

        $response = Response::json(array('code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode("{}")));

      } catch (Exception $e) {
         (new ImageController())->logs("setCatalogRankOnTheTopByAdmin",$e);
//      Log::error("setCatalogRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set catalog rank.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      }
      return $response;
    }*/

    public function setCatalogRankOnTheTopByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['catalog_ids'], $request)) != '') {
                return $response;
            }

            $catalog_ids = $request->catalog_ids;
            $create_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            foreach ($catalog_ids as $key => $id) {
                $increase_time = date('Y-m-d H:i:s', strtotime("+$key seconds", strtotime($create_time)));

                DB::update('UPDATE
                        catalog_master
                    SET
                        update_time = ?
                    WHERE
                        id = ?', [$increase_time, $id]);
            }
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setCatalogRankOnTheTopByAdmin', $e);
            //Log::error("setCatalogRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set catalog rank.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} setContentRankOnTheTopByAdmin setContentRankOnTheTopByAdmin
     *
     * @apiName setContentRankOnTheTopByAdmin
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
     * "content_id":1963 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setContentRankOnTheTopByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $create_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::update('UPDATE
                              content_master
                              SET update_time = ?
                              WHERE
                              id = ?', [$create_time, $content_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setContentRankOnTheTopByAdmin', $e);
            //      Log::error("setContentRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set content rank.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} setMultipleContentRankByAdmin setMultipleContentRankByAdmin
     *
     * @apiName setMultipleContentRankByAdmin
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
     * "content_ids":[1963,1964] //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setMultipleContentRankByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredArrayParameter(['content_ids'], $request)) != '') {
                return $response;
            }

            $content_ids = $request->content_ids;
            $created_at = date('Y-m-d H:i:s');
            DB::beginTransaction();

            foreach ($content_ids as $key => $id) {
                $increase_time = date('Y-m-d H:i:s', strtotime("+$key seconds", strtotime($created_at)));

                DB::update('UPDATE
                                content_master
                            SET
                                update_time = ?
                            WHERE
                                id = ?', [$increase_time, $id]);
            }

            DB::commit();
            $response = Response::json(['code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setMultipleContentRankByAdmin', $e);
            //Log::error("setMultipleContentRankByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set content rank.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*==================================| Generate all images for background video|==================================*/

    /**
     * @api {post} generateAllImagesForUserUploads   generateAllImagesForUserUploads
     *
     * @apiName generateAllImagesForUserUploads
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
     * "page":1, //compulsory
     * "item_count":1, //compulsory
     * "no_of_times_update":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Image generated successfully.",
     * "cause": "",
     * "data": {
     * "total_records_to_update": 57,
     * "uploaded_images": [
     * "http://192.168.0.113/photoadking_testing/image_bucket/user_uploaded_original/5d19e4af7f5e1_user_upload_1561978031.png"
     * ],
     * "remaining_images": []
     * }
     * }
     */
    public function generateAllImagesForUserUploads(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['item_count', 'page', 'no_of_times_update'], $request)) != '') {
                return $response;
            }

            $item_count = $request->item_count;
            $page = $request->page;
            $no_of_times_update = $request->no_of_times_update;
            $offset = ($page - 1) * $item_count;

            $total_row_result = DB::select('SELECT count(*) as total FROM upload_master');
            $image_list = DB::select('SELECT * FROM upload_master ORDER BY update_time ASC LIMIT ?,?', [$offset, $item_count]);

            $count = 0;
            $remaining_images = [];
            $updated_images = [];
            $original_file_path = Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');

            foreach ($image_list as $key) {

                $file_name = $key->file_name;
                $original_path = $original_file_path.$file_name;

                if (Config::get('constant.APP_ENV') != 'local') {

                    $aws_bucket = Config::get('constant.AWS_BUCKET');
                    $disk = Storage::disk('s3');
                    $value = "$aws_bucket/user_uploaded_original/".$file_name;
                    if ($disk->exists($value) == true) {

                        try {

                            $file_info = (new ImageController())->generateAllImagesFromOriginalImage($file_name);

                            $webp_file_name = $file_info['file_name'];
                            $height = $file_info['height'];
                            $width = $file_info['width'];

                            if (Config::get('constant.APP_ENV') != 'local') {

                                $compressed_dir = Config::get('constant.USER_UPLOADED_COMPRESSED_IMAGES_DIRECTORY');
                                $thumbnail_dir = Config::get('constant.USER_UPLOADED_THUMBNAIL_IMAGES_DIRECTORY');
                                $webp_original_dir = Config::get('constant.USER_UPLOADED_WEBP_ORIGINAL_IMAGES_DIRECTORY');
                                $webp_thumbnail_dir = Config::get('constant.USER_UPLOADED_WEBP_THUMBNAIL_IMAGES_DIRECTORY');

                                (new ImageController())->saveSingleImageInToS3($file_name, $compressed_dir, 'user_uploaded_compressed');
                                (new ImageController())->saveSingleImageInToS3($file_name, $thumbnail_dir, 'user_uploaded_thumbnail');
                                (new ImageController())->saveSingleImageInToS3($webp_file_name, $webp_original_dir, 'user_uploaded_webp_original');
                                (new ImageController())->saveSingleImageInToS3($webp_file_name, $webp_thumbnail_dir, 'user_uploaded_webp_thumbnail');

                            }

                            sleep(1);
                            DB::beginTransaction();
                            DB::update('UPDATE
                                upload_master SET height = ?, width = ?, webp_file_name = ?, attribute1 = ?
                                WHERE id = ?', [$height, $width, $webp_file_name, $no_of_times_update, $key->id]);
                            DB::commit();
                            $count = $count + 1;
                            $updated_images[] = $original_path;
                        } catch (Exception $e) {
                            (new ImageController())->logs('generateAllImagesForBkgVideos', $e);
                            //              Log::error("generateAllImagesForBkgVideos unable to generate images : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                            $remaining_images[] = $key->file_name;
                        }
                    } else {
                        $remaining_images[] = "Already exist $key->file_name";
                    }
                } else {
                    $org_path = '../..'.Config::get('constant.USER_UPLOADED_ORIGINAL_IMAGES_DIRECTORY');
                    if (($is_exist = (new ImageController())->checkFileExist($org_path.$file_name) == 1)) {
                        try {

                            $file_info = (new ImageController())->generateAllImagesFromOriginalImage($file_name);

                            $webp_file_name = $file_info['file_name'];
                            $height = $file_info['height'];
                            $width = $file_info['width'];

                            sleep(1);
                            DB::beginTransaction();
                            DB::update('UPDATE
                                upload_master SET height = ?, width = ?, webp_file_name = ?, attribute1 = ?
                                WHERE id = ?', [$height, $width, $webp_file_name, $no_of_times_update, $key->id]);
                            DB::commit();
                            $count = $count + 1;
                            $updated_images[] = $original_path;
                        } catch (Exception $e) {
                            (new ImageController())->logs('generateAllImagesForBkgVideos', $e);
                            //              Log::error("generateAllImagesForBkgVideos unable to generate images : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                            $remaining_images[] = $key->file_name;
                        }

                    } else {
                        $remaining_images[] = "Already exist $key->file_name";
                    }
                }

            }

            $result_array = ['total_records_to_update' => $total_row_result[0]->total, 'uploaded_images' => $updated_images, 'remaining_images' => $remaining_images];
            $result = json_decode(json_encode($result_array), true);

            $response = Response::json(['code' => 200, 'message' => 'Image generated successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('generateAllImagesForUserUploads', $e);
            //      Log::error("generateAllImagesForUserUploads : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate all images for user uploads.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getUsersMaxDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['user_limit'], $request)) != '') {
                return $response;
            }

            $user_limit = $request->user_limit;

            DB::statement("SET sql_mode = '' ");
            $result = DB::select('SELECT
                                mdm.user_id,
                                ud.email_id,
                                ud.first_name,
                                COUNT(mdm.id) AS total
                            FROM
                                my_design_master AS mdm
                                INNER JOIN user_detail AS ud ON ud.user_id = mdm.user_id AND mdm.is_active = 1
                            GROUP BY
                                mdm.user_id
                            ORDER BY
                                total DESC
                            LIMIT ?', [$user_limit]);

            $response = Response::json(['code' => 200, 'message' => 'Users design fetch Successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            Log::error('getUsersMaxDesign : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => config('constants.EXCEPTION_ERROR').'fetch user design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getUsersDailyDesign(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['days'], $request)) != '') {
                return $response;
            }

            $response_array = [];
            $final_response_array = [];
            $days = $request->days;
            /*$days = $request->days - 1;
            $current_time = date('Y-m-d');
            $past_date = date('Y-m-d', strtotime("-$days day", strtotime($current_time)));
            $mysql_where_date = " WHERE mdm.is_active = 1 AND ";

            for ($x = strtotime($past_date); $x <= strtotime($current_time); $x += 86400) {
                $mysql_where_date .= " DATE(mdm.create_time) = '".date('Y-m-d', $x)."' OR ";
            }

            DB::statement("SET sql_mode = '' ");
            $result = DB::select('SELECT
                                      mdm.user_id,
                                      ud.email_id,
                                      ud.first_name,
                                      COUNT(mdm.id) AS total
                                  FROM
                                      my_design_master AS mdm
                                      INNER JOIN user_detail AS ud ON ud.user_id = mdm.user_id
                                  ' . trim($mysql_where_date, "OR ") . '
                                  GROUP BY mdm.user_id');*/

            DB::statement("SET sql_mode = '' ");
            $results = DB::select('SELECT
                                  mdm.user_id,
                                  ud.email_id,
                                  ud.first_name,
                                  DATE(mdm.create_time) AS create_time,
                                  DATE(mdm.update_time) AS update_time,
                                  COUNT(mdm.id) AS total
                              FROM
                                  my_design_master AS mdm
                                  INNER JOIN user_detail AS ud ON ud.user_id = mdm.user_id
                              WHERE
                                  mdm.is_active = 1 AND
                                  DATE(mdm.create_time) > DATE(NOW()) - INTERVAL ? DAY
                              GROUP BY
                                  mdm.user_id, DATE(mdm.create_time)
                              ORDER BY mdm.user_id', [$days]);

            //dd($results);

            foreach ($results as $i => $result) {
                if (! isset($response_array[$result->user_id][$result->create_time])) {
                    $response_array[$result->user_id][$result->create_time] = $result;
                }
            }
            //dd($response_array);

            foreach ($response_array as $i => $response) {
                if (count($response) == $days) {
                    $final_response_array[] = $response;
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Users daily design fetch Successfully.', 'cause' => '', 'data' => $final_response_array]);

        } catch (Exception $e) {
            Log::error('getUsersDailyDesign : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => config('constants.EXCEPTION_ERROR').'fetch user daily design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getUserSessionDetails(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['start_date', 'end_date'], $request)) != '') {
                return $response;
            }

            ini_set('memory_limit', '-1');
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $final_results = $design_result = [];
            $signup_sub_category_id = $signup_catalog_id = $signup_content_id =
            $dashboard_sub_category_id = $dashboard_catalog_id = $dashboard_content_id =
            $editor_sub_category_id = $editor_catalog_id = $editor_content_id = null;

            $total_row_design = DB::select('SELECT
                user_id,
                content_type,
                count(*) AS total
          FROM
                my_design_master
          WHERE
                is_active = 1 AND
                DATE(create_time) BETWEEN ? AND ?
            GROUP BY content_type, user_id', [$start_date, $end_date]);

            foreach ($total_row_design as $i => $design) {
                $design_result[$design->user_id][$design->content_type] = $design->total;
            }
            //dd($design_result);

            $results = DB::select('SELECT
                                ussd.create_time AS session_create_time,
                                ussd.user_id,
                                ud.first_name,
                                um.email_id,
                                um.signup_type,
                                ru.role_id,
                                ussd.signup_session_details,
                                ussd.dashboard_session_details,
                                ussd.editor_session_details
                            FROM
                                user_session_signup_details AS ussd
                                INNER JOIN user_detail AS ud ON ud.user_id = ussd.user_id AND ussd.is_active = 1
                                INNER JOIN user_master AS um ON um.id = ussd.user_id AND um.is_active = 1
                                INNER JOIN role_user AS ru ON ru.user_id = ussd.user_id
                            WHERE
                                DATE(ussd.update_time) BETWEEN ? AND ?
                            ORDER BY ussd.create_time DESC', [$start_date, $end_date]);

            foreach ($results as $i => $result) {

                $signup_session_details = json_decode($result->signup_session_details, 1);
                $dashboard_session_details = json_decode($result->dashboard_session_details, 1);
                $editor_session_details = json_decode($result->editor_session_details, 1);

                //dd($signup_session_details, $dashboard_session_details, $editor_session_details);
                //dd($result);
                //dump($final_results, $result->user_id, isset($design_result[$result->user_id][2]));

                $final_results[$i] = [
                    'session_create_time' => $result->session_create_time,
                    'user_id' => $result->user_id,
                    'first_name' => $result->first_name,
                    'email_id' => $result->email_id,
                    'signup_type' => $result->signup_type,
                    'role_id' => $result->role_id,
                    'image_design_count' => isset($design_result[$result->user_id][1]) ? $design_result[$result->user_id][1] : 0,
                    'video_design_count' => isset($design_result[$result->user_id][2]) ? $design_result[$result->user_id][2] : 0,
                    'intro_design_count' => isset($design_result[$result->user_id][3]) ? $design_result[$result->user_id][3] : 0,

                    'signup_entry_point' => null,
                    'signup_sub_category_name' => null,
                    'signup_catalog_name' => null,
                    'signup_template_url' => null,
                    'signup_sample_image' => null,
                    'signup_preview_file' => null,
                    'signup_content_type' => null,
                    'signup_is_featured' => null,
                    'signup_is_free' => null,
                    'signup_is_portrait' => null,
                    'signup_total_pages' => null,

                    'dashboard_entry_point' => null,
                    'dashboard_other_actions' => null,
                    'dashboard_sub_category_name' => null,
                    'dashboard_catalog_name' => null,
                    'dashboard_template_url' => null,
                    'dashboard_sample_image' => null,
                    'dashboard_preview_file' => null,
                    'dashboard_content_type' => null,
                    'dashboard_is_featured' => null,
                    'dashboard_is_free' => null,
                    'dashboard_is_portrait' => null,
                    'dashboard_total_pages' => null,

                    'editor_entry_point' => null,
                    'editor_other_actions' => null,
                    'editor_sub_category_name' => null,
                    'editor_catalog_name' => null,
                    'editor_template_url' => null,
                    'editor_sample_image' => null,
                    'editor_preview_file' => null,
                    'editor_content_type' => null,
                    'editor_is_featured' => null,
                    'editor_is_free' => null,
                    'editor_is_portrait' => null,
                    'editor_total_pages' => null,
                ];

                if ($signup_session_details) {
                    $final_results[$i]['signup_entry_point'] = $signup_session_details['signup_entry_point'];
                    $signup_sub_category_id = ($signup_session_details['signup_sub_category_id']) ? $signup_session_details['signup_sub_category_id'] : null;
                    $signup_catalog_id = ($signup_session_details['signup_catalog_id']) ? $signup_session_details['signup_catalog_id'] : null;
                    $signup_content_id = ($signup_session_details['signup_content_id']) ? $signup_session_details['signup_content_id'] : null;

                    if ($signup_sub_category_id) {
                        $final_results[$i]['signup_sub_category_name'] = DB::select('SELECT
                  scm.sub_category_name AS signup_sub_category_name
                FROM
                  sub_category_master AS scm
                WHERE
                  scm.uuid = ?
                ORDER BY scm.update_time DESC', [$signup_sub_category_id])[0]->signup_sub_category_name;
                    }

                    if ($signup_catalog_id) {
                        $final_results[$i]['signup_catalog_name'] = DB::select('SELECT
                ctm.name AS signup_catalog_name
              FROM
                catalog_master AS ctm
              WHERE
                ctm.uuid = ?
              ORDER BY ctm.update_time DESC', [$signup_catalog_id])[0]->signup_catalog_name;
                    }

                    if ($signup_content_id) {
                        DB::statement("SET sql_mode = '' ");
                        $template_detail = DB::select('SELECT
              CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'", "/app/#/",
                IF(cm.content_type = 4, "editor", IF(cm.content_type = 9, "video-editor", "intro-editor")), "/", scm.uuid, "/", ctm.uuid, "/", cm.uuid) AS template_url,
              IF(cm.image != "", CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.image),"") AS sample_image,
              IF(cm.content_file != "", CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.content_file),"") AS preview_file,
              cm.content_type,
              COALESCE(cm.is_featured,"") AS is_featured,
              COALESCE(cm.is_free,0) AS is_free,
              COALESCE(cm.is_portrait,0) AS is_portrait,
              COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages
            FROM
              content_master AS cm
              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id,
              sub_category_master AS scm
            WHERE
              scc.sub_category_id = scm.id AND
              cm.uuid = ? AND
              ISNULL(cm.original_img) AND
              ISNULL(cm.display_img)
            GROUP BY
              cm.uuid
            ORDER BY cm.update_time DESC', [$signup_content_id])[0];

                        $final_results[$i]['signup_template_url'] = $template_detail->template_url;
                        $final_results[$i]['signup_sample_image'] = $template_detail->sample_image;
                        $final_results[$i]['signup_preview_file'] = $template_detail->preview_file;
                        $final_results[$i]['signup_content_type'] = $template_detail->content_type;
                        $final_results[$i]['signup_is_featured'] = $template_detail->is_featured;
                        $final_results[$i]['signup_is_free'] = $template_detail->is_free;
                        $final_results[$i]['signup_is_portrait'] = $template_detail->is_portrait;
                        $final_results[$i]['signup_total_pages'] = $template_detail->total_pages;
                    }
                }
                //dd($result);

                if ($dashboard_session_details) {
                    $final_results[$i]['dashboard_entry_point'] = $dashboard_session_details['dashboard_entry_point'];
                    $final_results[$i]['dashboard_other_actions'] = $dashboard_session_details['dashboard_other_actions'];
                    $dashboard_sub_category_id = ($dashboard_session_details['dashboard_sub_category_id']) ? $dashboard_session_details['dashboard_sub_category_id'] : null;
                    $dashboard_catalog_id = ($dashboard_session_details['dashboard_catalog_id']) ? $dashboard_session_details['dashboard_catalog_id'] : null;
                    $dashboard_content_id = ($dashboard_session_details['dashboard_content_id']) ? $dashboard_session_details['dashboard_content_id'] : null;

                    if ($dashboard_sub_category_id) {
                        $final_results[$i]['dashboard_sub_category_name'] = DB::select('SELECT
                  scm.sub_category_name AS dashboard_sub_category_name
                FROM
                  sub_category_master AS scm
                WHERE
                  scm.uuid = ?
                ORDER BY scm.update_time DESC', [$dashboard_sub_category_id])[0]->dashboard_sub_category_name;
                    }

                    if ($dashboard_catalog_id) {
                        $final_results[$i]['dashboard_catalog_name'] = DB::select('SELECT
                ctm.name AS dashboard_catalog_name
              FROM
                catalog_master AS ctm
              WHERE
                ctm.uuid = ?
              ORDER BY ctm.update_time DESC', [$dashboard_catalog_id])[0]->dashboard_catalog_name;
                    }

                    if ($dashboard_content_id) {
                        DB::statement("SET sql_mode = '' ");
                        $template_detail = DB::select('SELECT
              CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'", "/app/#/",
                IF(cm.content_type = 4, "editor", IF(cm.content_type = 9, "video-editor", "intro-editor")), "/", scm.uuid, "/", ctm.uuid, "/", cm.uuid) AS template_url,
              IF(cm.image != "", CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.image),"") AS sample_image,
              IF(cm.content_file != "", CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.content_file),"") AS preview_file,
              cm.content_type,
              COALESCE(cm.is_featured,"") AS is_featured,
              COALESCE(cm.is_free,0) AS is_free,
              COALESCE(cm.is_portrait,0) AS is_portrait,
              COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages
            FROM
              content_master AS cm
              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id,
              sub_category_master AS scm
            WHERE
              scc.sub_category_id = scm.id AND
              cm.uuid = ? AND
              ISNULL(cm.original_img) AND
              ISNULL(cm.display_img)
            GROUP BY
              cm.uuid
            ORDER BY cm.update_time DESC', [$dashboard_content_id])[0];

                        $final_results[$i]['dashboard_template_url'] = $template_detail->template_url;
                        $final_results[$i]['dashboard_sample_image'] = $template_detail->sample_image;
                        $final_results[$i]['dashboard_preview_file'] = $template_detail->preview_file;
                        $final_results[$i]['dashboard_content_type'] = $template_detail->content_type;
                        $final_results[$i]['dashboard_is_featured'] = $template_detail->is_featured;
                        $final_results[$i]['dashboard_is_free'] = $template_detail->is_free;
                        $final_results[$i]['dashboard_is_portrait'] = $template_detail->is_portrait;
                        $final_results[$i]['dashboard_total_pages'] = $template_detail->total_pages;
                    }
                }
                //dd($result);

                if ($editor_session_details) {
                    $final_results[$i]['editor_entry_point'] = $editor_session_details['editor_entry_point'];
                    $final_results[$i]['editor_other_actions'] = $editor_session_details['editor_other_actions'];
                    $editor_sub_category_id = ($editor_session_details['editor_sub_category_id']) ? $editor_session_details['editor_sub_category_id'] : null;
                    $editor_catalog_id = ($editor_session_details['editor_catalog_id']) ? $editor_session_details['editor_catalog_id'] : null;
                    $editor_content_id = ($editor_session_details['editor_content_id']) ? $editor_session_details['editor_content_id'] : null;

                    if ($editor_sub_category_id) {
                        $final_results[$i]['editor_sub_category_name'] = DB::select('SELECT
                  scm.sub_category_name AS editor_sub_category_name
                FROM
                  sub_category_master AS scm
                WHERE
                  scm.uuid = ?
                ORDER BY scm.update_time DESC', [$editor_sub_category_id])[0]->editor_sub_category_name;
                    }

                    if ($editor_catalog_id) {
                        $final_results[$i]['editor_catalog_name'] = DB::select('SELECT
              ctm.name AS editor_catalog_name
            FROM
              catalog_master AS ctm
            WHERE
              ctm.uuid = ?
            ORDER BY ctm.update_time DESC', [$editor_catalog_id])[0]->editor_catalog_name;
                    }

                    DB::statement("SET sql_mode = '' ");
                    if ($editor_content_id) {
                        $template_detail = DB::select('SELECT
            CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'", "/app/#/",
              IF(cm.content_type = 4, "editor", IF(cm.content_type = 9, "video-editor", "intro-editor")), "/", scm.uuid, "/", ctm.uuid, "/", cm.uuid) AS template_url,
            IF(cm.image != "", CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.image),"") AS sample_image,
            IF(cm.content_file != "", CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'", cm.content_file),"") AS preview_file,
            cm.content_type,
            COALESCE(cm.is_featured,"") AS is_featured,
            COALESCE(cm.is_free,0) AS is_free,
            COALESCE(cm.is_portrait,0) AS is_portrait,
            COALESCE(LENGTH(cm.json_pages_sequence) - LENGTH(REPLACE(cm.json_pages_sequence, ",","")) + 1,1) as total_pages
          FROM
            content_master AS cm
            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id,
            sub_category_master AS scm
          WHERE
            scc.sub_category_id = scm.id AND
            cm.uuid = ? AND
            ISNULL(cm.original_img) AND
            ISNULL(cm.display_img)
          GROUP BY cm.uuid
          ORDER BY cm.update_time DESC', [$editor_content_id])[0];

                        $final_results[$i]['editor_template_url'] = $template_detail->template_url;
                        $final_results[$i]['editor_sample_image'] = $template_detail->sample_image;
                        $final_results[$i]['editor_preview_file'] = $template_detail->preview_file;
                        $final_results[$i]['editor_content_type'] = $template_detail->content_type;
                        $final_results[$i]['editor_is_featured'] = $template_detail->is_featured;
                        $final_results[$i]['editor_is_free'] = $template_detail->is_free;
                        $final_results[$i]['editor_is_portrait'] = $template_detail->is_portrait;
                        $final_results[$i]['editor_total_pages'] = $template_detail->total_pages;
                    }
                }
                //dd($final_results);
            }

            $response = Response::json(['code' => 200, 'message' => 'Users design fetch Successfully.', 'cause' => '', 'data' => $final_results]);

        } catch (Exception $e) {
            Log::error('getUserSessionDetails : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'fetch user design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* ===========================| Manage Validation Module |=========================================*/

    /**
     * @api {post} addValidation   addValidation
     *
     * @apiName addValidation
     *
     * @apiGroup Admin
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
     * "category_id": 2, //compulsory
     * "validation_name": "sticker_image_size", //compulsory
     * "max_value_of_validation": 100, //compulsory
     * "is_featured":1, //compulsory
     * "is_catalog":1, //compulsory
     * "description":"test" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Validation added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addValidation(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter([
                'category_id',
                'validation_name',
                'max_value_of_validation',
                'is_featured',
                'is_catalog',
                'description',
            ], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $validation_name = trim($request->validation_name);
            $max_value_of_validation = $request->max_value_of_validation;
            $is_featured = $request->is_featured;
            $is_catalog = $request->is_catalog;
            $description = $request->description;
            $create_time = date('Y-m-d H:i:s');

            $result = DB::select('SELECT * FROM settings_master
                                      WHERE
                                        category_id = ? AND
                                        is_featured = ? AND
                                        is_catalog = ?', [$category_id, $is_featured, $is_catalog]);
            if (count($result) > 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Validation already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            DB::insert('INSERT INTO settings_master (
                            category_id,
                            validation_name,
                            max_value_of_validation,
                            is_featured,
                            is_catalog,
                            description,
                            is_active,
                            create_time
                            ) VALUES(?, ?, ?, ?, ?, ?, ?, ?)',
                [$category_id,
                    $validation_name,
                    $max_value_of_validation,
                    $is_featured,
                    $is_catalog,
                    $description,
                    1,
                    $create_time]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Validation added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addValidation', $e);
            //      Log::error("addValidation : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add validation.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} editValidation   editValidation
     *
     * @apiName editValidation
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
     * "setting_id":1, //compulsory
     * "category_id":0, //compulsory
     * "validation_name":"common_image_size", //compulsory
     * "max_value_of_validation":200, //compulsory
     * "is_featured":0, //compulsory
     * "is_catalog":0, //compulsory
     * "description":"Maximum size for all common images." //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Validation updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editValidation(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter([
                'setting_id',
                'category_id',
                'validation_name',
                'max_value_of_validation',
                'is_featured',
                'is_catalog',
                'description',
            ], $request)) != '') {
                return $response;
            }

            $category_id = $request->category_id;
            $setting_id = $request->setting_id;
            $validation_name = trim($request->validation_name);
            $max_value_of_validation = $request->max_value_of_validation;
            $is_featured = $request->is_featured;
            $is_catalog = $request->is_catalog;
            $description = $request->description;

            $result = DB::select('SELECT * FROM settings_master
                              WHERE
                                category_id = ? AND
                                is_featured = ? AND
                                is_catalog = ? AND
                                id != ?', [$category_id, $is_featured, $is_catalog, $setting_id]);

            if (count($result) > 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Validation already exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            DB::update('UPDATE
                      settings_master
                    SET
                      category_id = ?,
                      validation_name = ?,
                      max_value_of_validation = ?,
                      is_featured = ?,
                      is_catalog = ?,
                      description = ?
                    WHERE
                      id = ? ',
                [$category_id, $validation_name, $max_value_of_validation, $is_featured, $is_catalog, $description, $setting_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Validation updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('editValidation', $e);
            //      Log::error("editValidation : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit validation.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteValidation   deleteValidation
     *
     * @apiName deleteValidation
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
     * "setting_id":7 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Validation deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteValidation(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['setting_id'], $request)) != '') {
                return $response;
            }

            $setting_id = $request->setting_id;

            DB::beginTransaction();
            DB::delete('DELETE FROM settings_master WHERE id = ? ', [$setting_id]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Validation deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteValidation', $e);
            //      Log::error("deleteValidation : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete validation.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllValidationsForAdmin   getAllValidationsForAdmin
     *
     * @apiName getAllValidationsForAdmin
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All validations fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "setting_id": 1,
     * "category_id": 0,
     * "validation_name": "common_image_size",
     * "max_value_of_validation": "100",
     * "is_featured": 0,
     * "is_catalog": 0,
     * "description": "Maximum size for all common images. asasda dasd asd",
     * "update_time": "2019-07-17 06:08:01"
     * }
     * ],
     * "category_list": [
     * {
     * "category_id": 1,
     * "name": "Frame"
     * },
     * {
     * "category_id": 2,
     * "name": "Sticker"
     * },
     * {
     * "category_id": 3,
     * "name": "Background"
     * }
     * ]
     * }
     * }
     */
    public function getAllValidationsForAdmin()
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllValidationsForAdmin")) {
                $result = Cache::rememberforever('getAllValidationsForAdmin', function () {

                    $category_list = DB::select('SELECT
                                          ct.id AS category_id,
                                          ct.name
                                        FROM
                                        category AS ct
                                        WHERE is_active = ?', [1]);

                    $list_of_validations = DB::select('SELECT
                                                s.id AS setting_id,
                                                s.category_id,
                                                coalesce(c.name,"Default") AS category_name,
                                                s.validation_name,
                                                s.max_value_of_validation,
                                                s.is_featured,
                                                s.is_catalog,
                                                s.description,
                                                s.update_time
                                              FROM
                                                settings_master AS s
                                                LEFT JOIN category AS c ON c.id = s.category_id
                                              WHERE s.is_active = 1
                                              ORDER BY s.update_time DESC', [1]);

                    return ['result' => $list_of_validations, 'category_list' => $category_list];
                });
            }

            $redis_result = Cache::get('getAllValidationsForAdmin');

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All validations fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllValidationsForAdmin', $e);
            //      Log::error("getAllValidationsForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all validations.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| Redis Cache Operation |==============================================*/

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getRedisKeys",
     *        tags={"Redis"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getRedisKeys",
     *        summary="Get redis keys",
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
     * @api {post} getRedisKeys   getRedisKeys
     *
     * @apiName getRedisKeys
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Keys Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "keys_list": [
     * "pakt:I4IGClzRXZAjA9u8",
     * "pakt:getCatalogBySubCategoryId56-1",
     * "pakt:getAllCategory1",
     * "pakt:AV4SJwr8Rrf8O60a",
     * "pakt:getBackgroundCategory1",
     * "pakt:598068d3311b6315293306:standard_ref",
     * "pakt:tag:role_user:key",
     * "pakt:getLinkiOS-1",
     * "pakt:Btr0iNfysqBDree8",
     * "pakt:hNBS6Vxc66wL3Dux"
     * ]
     * }
     * }
     */
    public function getRedisKeys()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $redis_keys = Redis::keys(Config::get('constant.REDIS_KEY').':*');
            $result = ['keys_list' => $redis_keys];

            $response = Response::json(['code' => 200, 'message' => 'Redis keys fetched successfully.', 'cause' => '', 'data' => $result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getRedisKeys', $e);
            //      Log::error("getRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get redis-cache keys.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteRedisKeys",
     *        tags={"Redis"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteRedisKeys",
     *        summary="Delete redis keys",
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
     *           required={"keys_list"},
     *
     *           @SWG\Property(property="keys_list",type="array",description="comma seperated sub_category_id",
     *
     *                  @SWG\Items(type="object",@SWG\Property(property="key",type="string",example="pakt:getImagesByCatalogId57-1",description="redis key"))
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
    /**
     * @api {post} deleteRedisKeys   deleteRedisKeys
     *
     * @apiName deleteRedisKeys
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
     * "keys_list": [
     * {
     * "key": "pakt:getImagesByCatalogId33-1"
     * },
     * {
     * "key": "pakt:getImagesByCatalogId51-1"
     * },
     * {
     * "key":"pakt:getImagesByCatalogId57-1"
     * }
     *
     * ]
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Keys Deleted Successfully.",
     * "cause": "",
     * "data": "{}"
     * }
     */
    public function deleteRedisKeys(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParam(['keys_list'], $request)) != '') {
                return $response;
            }

            $keys = $request->keys_list;

            foreach ($keys as $rw) {
                if (($response = (new VerificationController())->validateRequiredParameter(['key'], $rw)) != '') {
                    return $response;
                }
            }

            foreach ($keys as $key) {
                Redis::del($key->key);
            }
            $response = Response::json(['code' => 200, 'message' => 'Redis keys deleted successfully.', 'cause' => '', 'data' => '{}']);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('deleteRedisKeys', $e);
            //      Log::error("deleteRedisKeys : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function deleteAllRedisKeysByKeyName(Request $request_body)
    {
        try {

            $arr_ip = explode(',', Config::get('constant.ALLOWED_IPS'));
            $this_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (! in_array("$this_ip", $arr_ip, true)) {
                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete redis keys.', 'cause' => 'Unauthorised IP', 'data' => json_decode('{}')]);
            }

            if ($request_body->header('api-key') != config('constant.API_KEY')) {
                Log::error('deleteAllRedisKeysByKeyName : Required field api_key is missing or empty or mismatch.', ['api-key' => $request_body->header('api-key')]);

                return Response::json(['code' => 201, 'message' => 'Required field api_key is missing or empty or mismatch.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParam(['key_name'], $request)) != '') {
                return $response;
            }

            $key_name = $request->key_name;

            $response = (new UserController())->deleteAllRedisKeys($key_name);

            $response = Response::json(['code' => 200, 'message' => 'Redis keys deleted successfully.', 'cause' => '', 'data' => $response]);

        } catch (Exception $e) {
            Log::error('deleteAllRedisKeysByKeyName : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllRedisKeysByKeyName(Request $request_body)
    {
        try {

            $arr_ip = explode(',', Config::get('constant.ALLOWED_IPS'));
            $this_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (! in_array("$this_ip", $arr_ip, true)) {
                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'run these Query.', 'cause' => 'Unauthorised IP', 'data' => json_decode('{}')]);
            }

            if ($request_body->header('api-key') != config('constant.API_KEY')) {
                Log::error('getAllRedisKeysByKeyName : Required field api_key is missing or empty or mismatch.', ['api-key' => $request_body->header('api-key')]);

                return Response::json(['code' => 201, 'message' => 'Required field api_key is missing or empty or mismatch.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParam(['key_name', 'is_return_ttl'], $request)) != '') {
                return $response;
            }

            $key_name = $request->key_name;
            $is_return_ttl = $request->is_return_ttl;

            $response = (new UserController())->getAllRedisKeys($key_name);

            if ($is_return_ttl) {
                foreach ($response as $i => $item) {
                    $response[$item] = Redis::ttl($item);
                    unset($response[$i]);
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Redis keys fetched successfully.', 'cause' => '', 'data' => $response]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error('getAllRedisKeysByKeyName : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getRandomRedisKeys(Request $request_body)
    {
        try {
            $arr_ip = explode(',', Config::get('constant.ALLOWED_IPS'));
            $this_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (! in_array("$this_ip", $arr_ip, true)) {
                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get random redis keys.', 'cause' => 'Unauthorised IP', 'data' => json_decode('{}')]);
            }

            if ($request_body->header('api-key') != config('constant.API_KEY')) {
                Log::error('getAllRedisKeysByKeyName : Required field api_key is missing or empty or mismatch.', ['api-key' => $request_body->header('api-key')]);

                return Response::json(['code' => 201, 'message' => 'Required field api_key is missing or empty or mismatch.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $result = [];
            $response = (new UserController())->getAllRedisKeys('*');
            //$response = json_decode(file_get_contents('/home/ubuntu/Downloads/mox.txt'));

            foreach ($response as $i => $item) {
                if (strlen($item) <= 21 && ! str_contains($item, 'getJsonData')) {
                    $result[]['key'] = $item;
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'Redis keys fetched successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            Log::error('getAllRedisKeysByKeyName : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getRedisKeyValueByKeyName(Request $request_body)
    {
        try {
            $arr_ip = explode(',', Config::get('constant.ALLOWED_IPS'));
            $this_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (! in_array("$this_ip", $arr_ip, true)) {
                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get this redis key.', 'cause' => 'Unauthorised IP', 'data' => json_decode('{}')]);
            }

            if ($request_body->header('api-key') != config('constant.API_KEY')) {
                Log::error('getRedisKeyValueByKeyName : Required field api_key is missing or empty or mismatch.', ['api-key' => $request_body->header('api-key')]);

                return Response::json(['code' => 201, 'message' => 'Required field api_key is missing or empty or mismatch.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParam(['key_name'], $request)) != '') {
                return $response;
            }

            $key_name = $request->key_name;

            $response = (new UserController())->getRedisKeyValue($key_name);

            $response = Response::json(['code' => 200, 'message' => 'Redis key\'s value fetched successfully.', 'cause' => '', 'data' => $response]);

        } catch (Exception $e) {
            Log::error('getRedisKeyValueByKeyName : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getRedisKeyDetail",
     *        tags={"Redis"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getRedisKeyDetail",
     *        summary="Get redis key detail",
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
     *          required={"key"},
     *
     *          @SWG\Property(property="key", type="string", example="pakt:getSubCategoryByCategoryId9-1", description=""),
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
    /**
     * @api {post} getRedisKeyDetail   getRedisKeyDetail
     *
     * @apiName getRedisKeyDetail
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
     * "key": "pakt:getSubCategoryByCategoryId9-1"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Key Detail Fetched Successfully.",
     * "cause": "",
     * "data": {
     * "keys_detail": [
     * {
     * "category_id": 11,
     * "name": "Testing"
     * },
     * {
     * "category_id": 10,
     * "name": "Frame"
     * },
     * {
     * "category_id": 9,
     * "name": "Sticker"
     * },
     * {
     * "category_id": 1,
     * "name": "Background"
     * }
     * ]
     * }
     * }
     */
    public function getRedisKeyDetail(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("getRedisKeyDetails Request:", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['key'], $request)) != '') {
                return $response;
            }

            $key = $request->key;
            $key_detail = \Illuminate\Support\Facades\Redis::get($key);

            //return $key_detail;
            $result = ['keys_detail' => unserialize($key_detail)];
            $response = Response::json(['code' => 200, 'message' => 'Redis key detail fetched successfully.', 'cause' => '', 'data' => $result]);
        } catch (Exception $e) {
            (new ImageController())->logs('getRedisKeyDetail', $e);
            //      Log::error("getRedisKeyDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get redis-cache key detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/clearRedisCache",
     *        tags={"Redis"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="clearRedisCache",
     *        summary="Clear redis cache",
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
     * @api {post} clearRedisCache   clearRedisCache
     *
     * @apiName clearRedisCache
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Redis Keys Deleted Successfully.",
     * "cause": "",
     * "data": "{}"
     * }
     */
    public function clearRedisCache()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            Redis::flushAll();
            //Redis::flushDb();
            $response = Response::json(['code' => 200, 'message' => 'Redis keys deleted successfully.', 'cause' => '', 'data' => '{}']);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('clearRedisCache', $e);
            //Log::error("clearRedisCache : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get redis-cache key detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function testMail()
    {
        try {

            $from_email_id = Config::get('constant.ADMIN_EMAIL_ID');
            $to_email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
            $template = 'simple';
            $subject = 'PhotoADKing: Test Mail';
            $message_body = [
                'message' => 'This is a test mail from PhotoADKing',
                'user_name' => 'Admin',
            ];
            $api_name = 'testMail';
            $api_description = 'Send test mail.';

            $this->dispatch(new SendMailJob($to_email_id, $from_email_id, $subject, $message_body, $template, $api_name, $api_description));
            $response = Response::json(['code' => 200, 'message' => 'Email sent successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Swift_TransportException $e) {
            Log::error('testMail (Swift_TransportException) : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'send mail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('testMail', $e);
            //Log::error("testMail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'send mail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getPhpInfo()
    {
        try {
            return $php_info = phpinfo();

        } catch (Exception $e) {
            //(new ImageController())->logs("getPhpInfo",$e);
            Log::error('getPhpInfo : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get php_info.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    //Fetch table information from database (use for only debugging query issue)
    public function getDatabaseInfo(Request $request_body)
    {
        try {
            $arr_ip = explode(',', Config::get('constant.ALLOWED_IPS'));
            $this_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

            if (! in_array("$this_ip", $arr_ip, true)) {
                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get this redis key.', 'cause' => 'Unauthorised IP', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['query'], $request)) != '') {
                return $response;
            }

            $query = $request->query;

            if (! ($this->validateQuery($query))) {
                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get database information.', 'cause' => 'Queries involving DELETE, DROP, or TRUNCATE are strictly prohibited.', 'data' => json_decode('{}')]);
            }

            return DB::select("$query");

        } catch (Exception $e) {
            Log::error('getDatabaseInfo : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'run these Query.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    private function validateQuery($query)
    {
        $sanitizedQuery = strtoupper($query);
        $disallowedKeywords = ['DELETE', 'DROP', 'TRUNCATE'];
        foreach ($disallowedKeywords as $keyword) {
            $pattern = '/\b'.preg_quote($keyword, '/').'\b/';
            if (preg_match($pattern, $sanitizedQuery)) {
                return false;
            }
        }

        return true;
    }

    /* ========================================= Store image into s3 bucket =========================================*/

    public function storeFileIntoS3Bucket(Request $request_body)
    {
        try {

            $base_url = (new ImageController())->getBaseUrl();
            if ($request_body->hasFile('file')) {
                $file = Input::file('file');

                /* Here we passed value following parameters as 0 bcs we use common validation for this images
                 * category = 0
                 * is_featured = 0
                 * is_catalog = 0
                 * */
                if (($response = (new ImageController())->verifyImage($file, 0, 0, 0)) != '') {
                    return $response;
                }

                $image = (new ImageController())->generateNewFileName('test_image', $file);
                (new ImageController())->saveOriginalImage($image);
                (new ImageController())->saveCompressedImage($image);
                (new ImageController())->saveThumbnailImage($image);

                $original_sourceFile = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$image;

                //Log::info('original path : ',['path' => $original_sourceFile]);

                //$original_sourceFile = $base_url . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY') . $image;
                $compressed_sourceFile = $base_url.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY').$image;
                $thumbnail_sourceFile = $base_url.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY').$image;

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                //if (fopen($original_sourceFile, "r")) {
                if (File::exists($original_sourceFile)) {

                    $original_targetFile = "$aws_bucket/original/".$image;
                    //Log::info('path : ',['path' => $original_sourceFile, 'target' => $original_targetFile]);
                    $disk->put($original_targetFile, file_get_contents($original_sourceFile), 'public');

                }

                /*if (fopen($compressed_sourceFile, "r")) {

                              $compressed_targetFile = "$aws_bucket/compressed/" . $image;
                              $disk->put($compressed_targetFile, file_get_contents($compressed_sourceFile), 'public');

                          }

                          if (fopen($thumbnail_sourceFile, "r")) {

                              $thumbnail_targetFile = "$aws_bucket/thumbnail/" . $image;
                              $disk->put($thumbnail_targetFile, file_get_contents($thumbnail_sourceFile), 'public');

                          }*/

            } else {
                return $response = Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //$contents = Storage::get();

            //https://s3-ap-southeast-1.amazonaws.com/maystr/original/5ac1b68fe3738_post_img_1522644623.png
            $value = "$aws_bucket/original/".$image;
            $disk = \Storage::disk('s3');
            $config = \Config::get('filesystems.disks.s3.bucket');
            if ($disk->exists($value)) {

                //Log::info('get path : ',['config' => $config, 'value' => $value]);
                $url = $disk->getDriver()->getAdapter()->getClient()->getObjectUrl($config, $value);

                //                $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('GetObject', [
                //                    'Bucket' => $config,
                //                    'Key' => $value,
                //                    //�ResponseContentDisposition� => �attachment;�//for download
                //                ]);
                //
                //                //return $command;
                //               $request = $disk->getDriver()->getAdapter()->getClient()->createPresignedRequest($command, '+10 minutes');
                //
                //                $generate_url = "'".$request->getUri()."'";
                $result = $url; //array('image_url' => $generate_url);
                $response = Response::json(['code' => 200, 'message' => 'Post uploaded successfully.', 'cause' => '', 'data' => $result]);

                //return $generate_url;
            }

        } catch (Exception $e) {
            (new ImageController())->logs('storeFileIntoS3Bucket', $e);
            //      Log::error('storeFileIntoS3Bucket : ', ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'store file into S3 bucket.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollback();
        }

        return $response;
    }

    public function checkFileExistWithFOpen(Request $request)
    {
        try {

            $request = json_decode($request->getContent());

            $url = $request->url;

            //if (File::exists($original_image_path)) {
            if (fopen($url, 'r')) {
                //File::delete($original_image_path);
                //unlink($original_image_path);
                $response = Response::json(['code' => 200, 'message' => 'File fetched successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'File not fetched successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('checkFileExistWithFOpen', $e);
            //      Log::error("checkFileExistWithFOpen : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'check file exist using fopen.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function checkFileExistWithFileExist(Request $request)
    {
        try {

            $request = json_decode($request->getContent());

            $file_name = $request->file_name;

            $original_image_path = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$file_name;

            if (File::exists($original_image_path)) {
                $response = Response::json(['code' => 200, 'message' => 'File fetched successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'File not fetched successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('checkFileExistWithFileExist', $e);
            //      Log::error("checkFileExistWithFileExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'check file exist using file_exist.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} saveImageIntoS3   saveImageIntoS3
     *
     * @apiName saveImageIntoS3
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
     * request_data:{ //compulsory
     * "is_replace":1, //1=replace, 0=do not replace
     * "directory_name":"extra"
     * },
     * file:"1.jpg", //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Image generated successfully.",
     * "cause": "",
     * "data": {
     * "url": "'https://photoadking-test.s3.amazonaws.com/photoadking-test/extra/5d205763c65c7_user_upload_1562400611.jpg'"
     * }
     * }
     */
    public function saveImageIntoS3(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['is_replace', 'directory_name'], $request)) != '') {
                return $response;
            }

            $is_replace = $request->is_replace;
            $directory_name = $request->directory_name;

            if ($request_body->hasFile('file')) {
                $image_array = Input::file('file');

                $file_name = $image_array->getClientOriginalName();

                $aws_bucket = Config::get('constant.AWS_BUCKET');
                $disk = Storage::disk('s3');
                $original_targetFile = "$aws_bucket/$directory_name/".$file_name;

                if ($is_replace == 1) {
                    if ($disk->exists($original_targetFile) == true) {
                        return Response::json(['code' => 201, 'message' => 'File is already exist.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }

                $disk->put($original_targetFile, file_get_contents($image_array), 'public');

            } else {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            $config = Config::get('filesystems.disks.s3.bucket');
            if ($disk->exists($original_targetFile)) {

                $url = "'".$disk->getDriver()->getAdapter()->getClient()->getObjectUrl($config, $original_targetFile)."'";

            } else {
                $url = '';
            }

            $response = Response::json(['code' => 200, 'message' => 'Save image into s3 successfully.', 'cause' => '', 'data' => ['url' => $url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('saveImageIntoS3', $e);
            //      Log::error("saveImageIntoS3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save image into s3.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* Video module*/
    /* =========================================| Catalog video |========================================= */

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addNormalVideos",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addNormalVideos",
     *        summary="Add normal videos",
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
     *          description="Give catalog_id in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","search_category"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="search_category",  type="string", example="landscape,portrait", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file[]",
     *         in="formData",
     *         description="array of normal video",
     *         required=true,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Normal videos added successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} addNormalVideos   addNormalVideos
     *
     * @apiName addNormalVideos
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
     * "request_data":{
     * "content_id":2 //compulsory
     * },
     * "file[]":1.mp4 //compulsory
     * "file[]":2.mp4
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal videos added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addNormalVideos(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //Log::info("Request Data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $file_array = Input::file('file');

                foreach ($file_array as $key) {
                    if (($response = (new ImageController())->verifyVideo($key)) != '') {
                        return $response;
                    }

                    $comp_video = (new ImageController())->generateNewFileName('normal_video', $key);
                    $org_path = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
                    (new ImageController())->saveOriginalVideo($comp_video, 'video', $org_path, $key);

                    $thum_video_file_name = (new ImageController())->generateThumbnailFileName('normal_video', $key);

                    $original_video_path = $org_path.$comp_video;
                    $originalFilePath = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$thum_video_file_name;

                    if (($response = (new ImageController())->getAndSaveOriginalImageFromVideo($original_video_path, $originalFilePath)) != '') {
                        return $response;
                    }

                    //generate and save thumbnail image
                    (new ImageController())->saveThumbnailImage($thum_video_file_name);

                    //generate & save webp images
                    $file_name = (new ImageController())->saveWebpOriginalImage($thum_video_file_name);
                    $dimension = (new ImageController())->saveWebpThumbnailImage($thum_video_file_name);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveVideoInToS3Bucket($comp_video, $thum_video_file_name);
                        (new ImageController())->saveWebpImageInToS3($file_name);
                    }

                    $org_img_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$thum_video_file_name;
                    $tag_list = (new TagDetectController())->getTagInImageByBytes($org_img_path);

                    /*if ($tag_list == "" or $tag_list == NULL) {
                      (new ImageController())->deleteVideo($comp_video);
                      (new ImageController())->deleteImage($thum_video_file_name);
                      return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarify.com.', 'cause' => '', 'data' => json_decode("{}")));
                    }*/
                    $content_type = Config::get('constant.CONTENT_TYPE_OF_VIDEO');

                    $uuid = (new ImageController())->generateUUID();
                    DB::insert('INSERT
                          INTO
                            content_master(
                                catalog_id,
                                uuid,
                                image,
                                webp_image,
                                content_type,
                                content_file,
                                search_category,
                                height,
                                width,
                                create_time)
                                VALUES(?, ?,?, ?, ?, ?, ?, ?, ?, ?) ',
                        [$catalog_id, $uuid, $thum_video_file_name, $file_name, $content_type, $comp_video, $tag_list, $dimension['height'], $dimension['width'], $create_at]);
                }

                DB::commit();
                $response = Response::json(['code' => 200, 'message' => 'Normal videos added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('addNormalVideos', $e);
            //      Log::error("addNormalVideos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add normal videos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateNormalVideo",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateNormalVideo",
     *        summary="Update normal videos",
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
     *          description="Give catalog_id in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","search_category"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="search_category",  type="string", example="landscape,portrait", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file[]",
     *         in="formData",
     *         description="array of normal video",
     *         required=true,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Normal videos updated successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} updateNormalVideo updateNormalVideo
     *
     * @apiName updateNormalVideo
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
     * "request_data":{
     * "content_id":2 //compulsory
     * },
     * "file[]":1.mp4 //compulsory
     * "file[]":2.mp4
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal videos updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateNormalVideo(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));

            //Log::info("Request Data:", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'search_category'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $search_category = strtolower($request->search_category);
            $thum_video_file_name = '';
            $comp_video = '';
            $dimension['height'] = '';
            $dimension['width'] = '';

            if ($search_category != null or $search_category != '') {
                if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '') {
                    return $response;
                }
            }
            $content = DB::select('select id,image,content_file,webp_image from  content_master where id=?', [$content_id]);
            if (count($content) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Normal video does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($request_body->hasFile('file')) {
                $file_array = Input::file('file');
                if (($response = (new ImageController())->verifyVideo($file_array)) != '') {
                    return $response;
                }

                $comp_video = (new ImageController())->generateNewFileName('normal_video', $file_array);
                $original_video_dir = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY');
                (new ImageController())->saveOriginalVideo($comp_video, 'video', $original_video_dir, $file_array);

                $thum_video_file_name = (new ImageController())->generateThumbnailFileName('normal_video', $file_array);

                $original_video_path = $original_video_dir.$comp_video;
                $thumbnailFilePath = '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$thum_video_file_name;
                if (($response = (new ImageController())->getAndSaveOriginalImageFromVideo($original_video_path, $thumbnailFilePath)) != '') {
                    return $response;
                }

                //generate thumbnail image
                (new ImageController())->saveThumbnailImage($thum_video_file_name);

                //generate & save webp images
                $file_name = (new ImageController())->saveWebpOriginalImage($thum_video_file_name);
                $dimension = (new ImageController())->saveWebpThumbnailImage($thum_video_file_name);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveVideoInToS3Bucket($comp_video, $thum_video_file_name);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }

                $org_img_path = Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').$thum_video_file_name;
                $tag_list = (new TagDetectController())->getTagInImageByBytes($org_img_path);

                /*if ($tag_list == "" or $tag_list == NULL) {
                  (new ImageController())->deleteVideo($comp_video);
                  (new ImageController())->deleteImage($thum_video_file_name);
                  return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarify.com.', 'cause' => '', 'data' => json_decode("{}")));
                }*/

                DB::beginTransaction();
                DB::update('UPDATE
                    content_master
                  SET
                      search_category = IF(? != "",?,search_category),
                      image = IF(? != "",?,image),
                      content_file = IF(? != "",?,content_file),
                      height = IF(? != "",?,height),
                      width = IF(? != "",?,width)
                  WHERE
                    id = ? ',
                    [
                        $search_category,
                        $search_category,
                        $thum_video_file_name,
                        $thum_video_file_name,
                        $comp_video,
                        $comp_video,
                        $dimension['height'],
                        $dimension['height'],
                        $dimension['width'],
                        $dimension['width'],
                        $content_id,
                    ]);
                DB::commit();

                //Delete old  Image,webp and video from image_bucket
                $image_name = $content[0]->image;
                $content_file = $content[0]->content_file;
                $webp_image = $content[0]->webp_image;

                (new ImageController())->deleteImage($image_name);
                (new ImageController())->deleteVideo($content_file);
                (new ImageController())->deleteWebpImage($webp_image);
            }

            $response = Response::json(['code' => 200, 'message' => 'Normal video updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateNormalVideo', $e);
            //      Log::error("updateNormalVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    //Bhargav
    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteNormalVideoByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteNormalVideoByAdmin",
     *        summary="Delete Normal Video By Admin",
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
     *          required={"content_id"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Normal video deleted successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *        ),
     *    )
     */
    /**
     * @api {post} deleteNormalVideoByAdmin deleteNormalVideoByAdmin
     *
     * @apiName deleteNormalVideoByAdmin
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
     * "content_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal video deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteNormalVideoByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $result = DB::select('SELECT image,content_file,webp_image FROM content_master WHERE id = ?', [$content_id]);
            if (count($result) > 0) {
                $image_name = $result[0]->image;
                $content_file = $result[0]->content_file;
                $webp_image = $result[0]->webp_image;

                DB::beginTransaction();

                DB::delete('DELETE FROM content_master WHERE id = ? ', [$content_id]);
                DB::delete('DELETE FROM image_details WHERE name = ? ', [$image_name]);

                DB::commit();

                //Image,webp and video delete in image_bucket
                (new ImageController())->deleteImage($image_name);
                (new ImageController())->deleteVideo($content_file);
                (new ImageController())->deleteWebpImage($webp_image);

                $response = Response::json(['code' => 200, 'message' => 'Normal video deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Normal video does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('deleteNormalVideoByAdmin', $e);
            //      Log::error("deleteNormalVideoByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete normal video by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /*========================================| Json Video |========================================*/

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/uploadJsonVideos",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="uploadJsonVideos",
     *        summary="Upload Json Videos",
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
     *          description="Give is_replace in json object",
     *
     *         @SWG\Schema(
     *              required={"is_replace"},
     *
     *              @SWG\Property(property="is_replace",  type="integer", example=0),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file[]",
     *         in="formData",
     *         description="Sample file uploading",
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
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Json videos uploaded successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=420,
     *            description="Error",
     *
     *         @SWG\Schema(
     *
     *            @SWG\Property(property="Sample_Response", type="string", example={"code":420,"message":"File already exists.","cause":"","data":{"existing_files":{{"url":"http://192.168.0.116/photoadking_testing/image_bucket/video/wedding_invitation_video_r38_14.mp4","name":"wedding_invitation_video_r38_14.mp4"}}}}, description=""),
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
     * @api {post} uploadJsonVideos   uploadJsonVideos
     *
     * @apiName uploadJsonVideos
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
     * request_data:{
     * "is_replace":0 //compulsory 0=do not replace the existing file, 2=replace the existing file
     * },
     * file[]:1.mp4,
     * file[]:2.mp4,
     * file[]:3.mp4,
     * file[]:4.mp4
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json videos added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function uploadJsonVideos(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['is_replace'], $request)) != '') {
                return $response;
            }

            $is_replace = $request->is_replace;

            if ($request_body->hasFile('file')) {
                $images_array = Input::file('file');

                if ($is_replace == 0) {
                    if (($response = (new ImageController())->checkIsVideosExist($images_array)) != '') {
                        return $response;
                    }
                }

                foreach ($images_array as $file_array) {

                    if (($response = (new ImageController())->verifyVideo($file_array)) != '') {
                        return $response;
                    }

                    $file_name = $file_array->getClientOriginalName();
                    //Log::info('$file_name', [$file_name]);
                    (new ImageController())->unlinkVideo($file_name);
                    (new ImageController())->saveOriginalVideo($file_name, 'video', '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'), $file_array);

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {

                        (new ImageController())->saveJsonVideoInToS3($file_name);

                    }
                }
            }
            $response = Response::json(['code' => 200, 'message' => 'Json videos uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('uploadJsonVideos', $e);
            //      Log::error("uploadJsonVideos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload json videos.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addJsonVideoByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addJsonVideoByAdmin",
     *        summary="Add Json Video By Admin",
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
     *          description="Give video_name,catalog_id,is_featured,is_free,json_data in json object",
     *
     *         @SWG\Schema(
     *              required={"video_name","catalog_id","is_featured","is_free","json_data"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1),
     *              @SWG\Property(property="video_name",type="string", example="1.mp4"),
     *              @SWG\Property(property="is_featured",type="integer", example=1),
     *              @SWG\Property(property="is_free",type="integer", example=1),
     *              @SWG\Property(property="json_data",type="object", example="{}"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="sample_image",
     *         in="formData",
     *         description="Sample file uploading",
     *         required=true,
     *         type="file"
     *     ),
     *          @SWG\Parameter(
     *         name="transparent_image",
     *         in="formData",
     *         description="Transparent image uploading",
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
     * @api {post} addJsonVideoByAdmin addJsonVideoByAdmin
     *
     * @apiName addJsonVideoByAdmin
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
     * "video_name":"1.mp4",//compulsory
     * "catalog_id":1, //compulsory
     * "is_featured":1, //compulsory
     * "is_free":1, //compulsory
     * "json_data":{}
     * }
     * sample_image:sample_image.png //compulsory
     * transparent_image:transparent_image.png //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json added successfully.",
     * "cause": "",
     * "data": {
     * "preview_id": 43,
     * "content_id": 66
     * }
     * }
     */
    public function addJsonVideoByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (! $request_body->hasFile('sample_image')) {
                return Response::json(['code' => 201, 'message' => 'Required field sample_image is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (! $request_body->hasFile('transparent_image')) {
                return Response::json(['code' => 201, 'message' => 'Required field transparent_image is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['video_name', 'catalog_id', 'is_featured', 'is_free', 'json_data', 'search_category'], $request)) != '') {
                return $response;
            }

            $sample_image = Input::file('sample_image');
            $transparent_image = Input::file('transparent_image');
            $video_name = $request->video_name;
            $catalog_id = $request->catalog_id;
            $json_data = $request->json_data;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : null;
            $search_category = strtolower($request->search_category);
            $created_at = date('Y-m-d H:i:s');

            if (($response = (new VerificationController())->validateFonts($json_data)) != '') {
                return $response;
            }

            if (($response = (new ImageController())->checkIsVideoExist($video_name)) != '') {
                return $response;
            }

            //      if ($search_category != NULL or $search_category != "") {
            //        $search_category = $search_category . ',';
            //      }

            DB::beginTransaction();

            if (($response = (new ImageController())->verifySampleImageForVideo($sample_image)) != '') {
                return $response;
            }

            if (($response = (new ImageController())->verifyMultipleImage($transparent_image, 'transparent_image')) != '') {
                return $response;
            }

            if (($response = (new ImageController())->validateHeightWidthOfSampleImage($sample_image, $json_data)) != '') {
                return $response;
            }

            $color_value = (new ImageController())->getRandomColor($sample_image);
            //      $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($sample_image));
            //      if ($tag_list == "" or $tag_list == NULL) {
            //        return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
            //      }
            //
            //      if (($response = (new VerificationController())->verifySearchCategory("$search_category$tag_list")) != '') {
            //        $response_details = (json_decode(json_encode($response), true));
            //        $data = $response_details['original']['data'];
            //        $tag_list = $data['search_tags'];
            //      } else {
            //        $tag_list = "$search_category$tag_list";
            //
            //      }

            $transparent_image_name = (new ImageController())->generateNewFileName('transparent_image', $transparent_image);
            (new ImageController())->saveMultipartTempFile($transparent_image_name, $transparent_image);

            $catalog_image = (new ImageController())->generateNewFileName('sample_image', $sample_image);
            (new ImageController())->saveMultipleOriginalImage($catalog_image, 'sample_image');
            (new ImageController())->saveMultipleCompressedImage($catalog_image, 'sample_image');
            (new ImageController())->saveMultipleThumbnailImage($catalog_image, 'sample_image');
            $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
            $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveImageInToS3($catalog_image);
                (new ImageController())->saveWebpImageInToS3($file_name);
            }
            $content_type = Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON');

            $uuid = (new ImageController())->generateUUID();
            $content_detail = [
                'catalog_id' => $catalog_id,
                'uuid' => $uuid,
                'image' => $catalog_image,
                'content_type' => $content_type,
                'json_data' => json_encode($json_data),
                'content_file' => $video_name,
                'is_free' => $is_free,
                'is_featured' => $is_featured,
                'is_portrait' => $is_portrait,
                'search_category' => $search_category,
                'height' => $dimension['height'],
                'width' => $dimension['width'],
                'color_value' => $color_value,
                'create_time' => $created_at,
                'webp_image' => $file_name,
            ];
            $content_id = DB::table('content_master')->insertGetId($content_detail);

            $height = $json_data->height;
            $width = $json_data->width;
            //Log::info('json_height_width', [$height, $width]);
            $dimension = (new ImageController())->generatePreviewVideoHeightWidth($width, $height);

            $preview_detail = [
                'catalog_id' => $catalog_id,
                'content_id' => $content_id,
                'template_video' => $video_name,
                'transparent_img' => $transparent_image_name,
                'output_height' => $dimension['height'],
                'output_width' => $dimension['width'],
                'create_time' => $created_at,
            ];

            $prv_id = DB::table('preview_video_jobs')->insertGetId($preview_detail);

            DB::commit();

            $job = new PreviewVideoJob($content_id, $prv_id);
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

                    if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON')) {
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

            if ($result['result_status'] == 0) {
                $response = Response::json(['code' => 201, 'message' => 'Json is unable to preview video', 'cause' => '', ['preview_id' => $prv_id, 'content_id' => $content_id, 'template_detail' => $template_details]]);
            } else {
                $preview_id = $result['preview_id'];
                $response = Response::json(['code' => 200, 'message' => 'Json added successfully.', 'cause' => '', 'data' => ['preview_id' => $preview_id, 'content_id' => $content_id, 'template_detail' => $template_details]]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('addJsonVideoByAdmin', $e);
            //      Log::error("addJsonVideoByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add json by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editJsonVideoByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="editJsonVideoByAdmin",
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
     *          description="Give video_name,content_id,catalog_id,is_featured,is_free,json_data in json object",
     *
     *         @SWG\Schema(
     *              required={"video_name","content_id","catalog_id","is_featured","is_free","json_data"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1),
     *              @SWG\Property(property="catalog_id",  type="integer", example=1),
     *              @SWG\Property(property="video_name",type="string", example="1.mp4"),
     *              @SWG\Property(property="is_featured",type="integer", example=1),
     *              @SWG\Property(property="is_free",type="integer", example=1),
     *              @SWG\Property(property="json_data",type="object", example="{}"),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="sample_image",
     *         in="formData",
     *         description="Sample file uploading",
     *         required=true,
     *         type="file"
     *     ),
     *          @SWG\Parameter(
     *         name="transparent_image",
     *         in="formData",
     *         description="Transparent image uploading",
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
     * @api {post} editJsonVideoByAdmin editJsonVideoByAdmin
     *
     * @apiName editJsonVideoByAdmin
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
     * "video_name":"1.mp4",
     * "content_id":66,//compulsory
     * "catalog_id":1, //compulsory
     * "is_featured":1, //compulsory
     * "is_free":1, //compulsory
     * "json_data":{}
     * }
     * }
     * sample_image:sample_image.png
     * transparent_image:transparent_image.png
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json updated successfully.",
     * "cause": "",
     * "data": {
     * "preview_id": 43,
     * "content_id": 66
     * }
     * }
     */
    public function editJsonVideoByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'catalog_id', 'is_featured', 'is_free', 'json_data', 'template_name'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $catalog_id = $request->catalog_id;
            $json_data = $request->json_data;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : null;
            $search_category = isset($request->search_category) ? strtolower($request->search_category) : null;
            $template_name = $request->template_name;
            $created_at = date('Y-m-d H:i:s');
            $content_type = Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON');
            $video_name = isset($request->video_name) ? $request->video_name : '';
            $dimension['height'] = null;
            $dimension['width'] = null;
            $color_value = '';
            $file_name = '';
            $catalog_image = '';

            if ($video_name or $request_body->hasFile('transparent_image')) {

                if (($response = (new VerificationController())->validateRequiredParameter(['video_name'], $request)) != '') {
                    return $response;
                }

                if (! $request_body->hasFile('transparent_image')) {
                    return Response::json(['code' => 201, 'message' => 'Required field transparent_image is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $transparent_image = Input::file('transparent_image');
                if (($response = (new ImageController())->verifySampleImageForVideo($transparent_image)) != '') {
                    return $response;
                }

                if (($response = (new ImageController())->checkIsVideoExist($video_name)) != '') {
                    return $response;
                }
            }

            if ($search_category != null or $search_category != '') {
                if (($response = (new VerificationController())->verifySearchCategory($search_category)) != '') {
                    return $response;
                }
            }

            if (($response = (new VerificationController())->validateFonts($json_data)) != '') {
                return $response;
            }

            $content_detail = DB::select('SELECT * FROM content_master WHERE id = ?', [$content_id]);
            $image = $content_detail[0]->image;
            $old_preview_video_name = $content_detail[0]->content_file;
            $webp_image = $content_detail[0]->webp_image;

            if ($request_body->hasFile('sample_image')) {
                $sample_image = Input::file('sample_image');

                if ($search_category != null or $search_category != '') {
                    $search_category = $search_category.',';
                }

                DB::beginTransaction();
                if (($response = (new ImageController())->verifySampleImageForVideo($sample_image)) != '') {
                    return $response;
                }

                //        if (($response = (new ImageController())->verifySampleImage($sample_image_array)) != '')
                //          return $response;

                if (($response = (new ImageController())->validateHeightWidthOfSampleImage($sample_image, $json_data)) != '') {
                    return $response;
                }

                $color_value = (new ImageController())->getRandomColor($sample_image);
                $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($sample_image));
                /* if ($tag_list == "" or $tag_list == NULL) {
                   return Response::json(array('code' => 201, 'message' => 'Tag not detected from clarifai.com.', 'cause' => '', 'data' => json_decode("{}")));
                 }*/

                if (($response = (new VerificationController())->verifySearchCategory("$search_category$tag_list")) != '') {
                    $response_details = (json_decode(json_encode($response), true));
                    $data = $response_details['original']['data'];
                    $tag_list = $data['search_tags'];
                } else {
                    $tag_list = "$search_category$tag_list";
                }

                $catalog_image = (new ImageController())->generateNewFileName('sample_image', $sample_image);
                (new ImageController())->saveMultipleOriginalImage($catalog_image, 'sample_image');
                (new ImageController())->saveMultipleCompressedImage($catalog_image, 'sample_image');
                $sample_image_dimension = (new ImageController())->saveMultipleThumbnailImage($catalog_image, 'sample_image');
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);
                }
                $search_category = $tag_list;

                //Delete old image
                if ($image) {
                    (new ImageController())->deleteImage($image);
                }

                //Webp image delete
                if ($webp_image) {
                    (new ImageController())->deleteWebpImage($webp_image);
                }

            }

            DB::beginTransaction();
            DB::update('UPDATE content_master
                        SET
                            catalog_id = IF(? != "",?,catalog_id),
                            image = IF(? != "",?,image),
                            content_type = ?,
                            json_data = ?,
                            content_file = IF(? != "",?,content_file),
                            is_free = IF(? != is_free,?,is_free),
                            is_featured = IF(? != is_featured,?,is_featured),
                            is_portrait = IF(? != is_portrait,?,is_portrait),
                            search_category = IF(? != "",?,search_category),
                            template_name = ?,
                            height = IF(? != NULL,?,height),
                            width = IF(? != NULL,?,width),
                            color_value = IF(? != "",?,color_value),
                            webp_image = IF(? != "",?,webp_image)
                            WHERE id = ?', [
                $catalog_id,
                $catalog_id,
                $catalog_image,
                $catalog_image,
                $content_type,
                json_encode($json_data),
                $video_name,
                $video_name,
                $is_free,
                $is_free,
                $is_featured,
                $is_featured,
                $is_portrait,
                $is_portrait,
                $search_category,
                $search_category,
                $template_name,
                $dimension['height'],
                $dimension['height'],
                $dimension['width'],
                $dimension['width'],
                $color_value,
                $color_value,
                $file_name,
                $file_name,
                $content_id]);

            DB::commit();

            if ($video_name) {

                $transparent_image_array = Input::file('transparent_image');
                $height = $json_data->height;
                $width = $json_data->width;
                //Log::info('json_height_width', [$height, $width]);
                $dimension = (new ImageController())->generatePreviewVideoHeightWidth($width, $height);

                if (($response = (new ImageController())->verifyMultipleImage($transparent_image_array, 'transparent_image')) != '') {
                    return $response;
                }

                $transparent_image = (new ImageController())->generateNewFileName('transparent_image', $transparent_image_array);
                (new ImageController())->saveMultipartTempFile($transparent_image, $transparent_image_array);

                //        Log::info('preview_height_width', [$dimension['height'], $dimension['width']]);
                $preview_detail = [
                    'catalog_id' => $catalog_id,
                    'content_id' => $content_id,
                    'template_video' => $video_name,
                    'transparent_img' => $transparent_image,
                    'output_height' => $dimension['height'],
                    'output_width' => $dimension['width'],
                    'create_time' => $created_at,
                ];

                $prv_id = DB::table('preview_video_jobs')->insertGetId($preview_detail);

                DB::commit();

                $job = new PreviewVideoJob($content_id, $prv_id, $old_preview_video_name);
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

                        if ($key->content_file != '' && $key->content_type == Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON')) {
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

                if ($result['result_status'] == 0) {
                    $response = Response::json(['code' => 201, 'message' => 'Json is unable to preview video', 'cause' => '', 'data' => ['preview_id' => $prv_id, 'content_id' => $content_id, 'template_detail' => $template_details]]);
                } else {
                    $preview_id = $result['preview_id'];
                    $response = Response::json(['code' => 200, 'message' => 'Json updated successfully. ', 'cause' => '', 'data' => ['preview_id' => $preview_id, 'content_id' => $content_id, 'template_detail' => $template_details]]);
                }

                //Delete old preview video
                //        if ($content_file) {
                //          (new ImageController())->deleteVideo($content_file);
                //        }

            } else {
                $response = Response::json(['code' => 200, 'message' => 'Json updated successfully.', 'cause' => '', 'data' => ['preview_id' => 0, 'content_id' => 0]]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('editJsonVideoByAdmin', $e);
            //      Log::error("editJsonVideoByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit json by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateVideoTemplateDetail",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateVideoTemplateDetail",
     *        summary="Update video template detail like is_free,featured,portrait,seach_tag",
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
     *          required={"content_id", "is_featured", "is_free"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="is_featured",  type="integer", example="1", description=""),
     *          @SWG\Property(property="is_free",  type="integer", example="1", description=""),
     *          @SWG\Property(property="is_portrait",  type="integer", example="1", description=""),
     *          @SWG\Property(property="search_category",  type="string", example="Frame", description=""),
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
    /**
     * @api {post} updateVideoTemplateDetail   updateVideoTemplateDetail
     *
     * @apiName updateVideoTemplateDetail
     *
     * @apiGroup Admin
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
     * "content_id":1, //compulsory
     * "is_featured":"1",//compulsory
     * "is_free":"1", //compulsory
     * "is_portrait":"1",
     * "search_category":"Nature"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video template updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateVideoTemplateDetail(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id', 'is_featured', 'is_free', 'template_name'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = isset($request->is_portrait) ? $request->is_portrait : null;
            $search_category = isset($request->search_category) && $request->search_category != '' ? strtolower($request->search_category) : null;
            $template_name = $request->template_name;

            $result = DB::select('SELECT 1 FROM content_master WHERE id = ?', [$content_id]);

            if (count($result) <= 0) {
                return $response = Response::json(['code' => 201, 'message' => 'Template does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                DB::beginTransaction();
                DB::update('UPDATE content_master
                        SET
                            is_free = ?,
                            is_featured = ?,
                            is_portrait = IF(? != is_portrait,?,is_portrait),
                            search_category = IF(? != "",?,search_category),
                            template_name = ?
                          WHERE id = ?', [

                    $is_free,
                    $is_featured,
                    $is_portrait,
                    $is_portrait,
                    $search_category,
                    $search_category,
                    $template_name,
                    $content_id]);

                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Video template updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateVideoTemplateDetail', $e);
            //      Log::error("updateVideoTemplateDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update a video template detail.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteJsonVideoByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteJsonVideoByAdmin",
     *        summary="Delete Json Video By Admin",
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
     *          required={"content_id"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Json video deleted successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *        ),
     *    )
     */
    /**
     * @api {post} deleteJsonVideoByAdmin deleteJsonVideoByAdmin
     *
     * @apiName deleteJsonVideoByAdmin
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
     * "content_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json video deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteJsonVideoByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $msg = '';
            $result = DB::select('SELECT * FROM content_master WHERE id = ?', [$content_id]);
            if ($result[0]->content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                $json_data = json_decode($result[0]->json_data);
                $audio_name = isset($json_data->audio_json) && isset($json_data->audio_json[0]->audio_name) ? $json_data->audio_json[0]->audio_name : '';
                if ($audio_name != '') {
                    (new ImageController())->deleteAudio($audio_name);
                }
            }

            if (count($result) > 0) {
                $image_name = $result[0]->image;
                $content_file = $result[0]->content_file;
                //        $video_name = $result[0]->template_video;
                $webp_image = $result[0]->webp_image;

                //$tv = DB::select('SELECT template_video FROM content_master WHERE id != ? AND template_video=?', [$content_id, $video_name]);

                /*if (count($tv) > 0) {
                    $msg = "Note: Video is not deleted because '$video_name' used in other templates.";
                } else {
                    (new ImageController())->deleteVideo($video_name);
                }*/

                DB::beginTransaction();

                DB::delete('DELETE FROM content_master WHERE id = ? ', [$content_id]);

                DB::commit();

                //Image,webp and video delete in image_bucket
                (new ImageController())->deleteImage($image_name);
                (new ImageController())->deleteVideo($content_file);
                (new ImageController())->deleteWebpImage($webp_image);

            }

            $response = Response::json(['code' => 200, 'message' => 'Json video deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteJsonVideoByAdmin', $e);
            //      Log::error("deleteJsonVideoByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete json video by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/checkReadyToPreviewVideo",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="checkReadyToPreviewVideo",
     *        summary="Check Ready To Preview Video",
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
     *          required={"preview_id","content_id"},
     *
     *          @SWG\Property(property="preview_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *            @SWG\Schema(
     *
     *              @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video is generate for preview","cause":"","data":{"status":1,"preview_video":"http://192.168.0.116/photoadking_testing/image_bucket/video/5cf8d1833af3b_preview_video_1559810435.mp4","est_time_sec":""}}, description=""),
     *            ),
     *        ),
     *
     *     @SWG\Response(
     *            response="default",
     *            description="Success",
     *
     *            @SWG\Schema(
     *
     *              @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Video is not generate for preview","cause":"","data":{"status":0,"preview_video":"","est_time_sec":2}}, description=""),
     *            ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *            @SWG\Schema(
     *
     *              @SWG\Property(property="Sample_Response", type="string", example={"code":201,"message":"Sorry, we couldn't generate video.Please, try again.","cause":"","data":{"status":2,"preview_video":"","est_time_sec":""}}, description=""),
     *          ),
     *        ),
     *    )
     */
    /**
     * @api {post} checkReadyToPreviewVideo checkReadyToPreviewVideo
     *
     * @apiName checkReadyToPreviewVideo
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
     * "preview_id":"1", //compulsory
     * "content_id":"1" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Video is ready to download",
     * "cause": "",
     * "data": {
     * "status": 1, //0=Queue,1=ready,2=failed
     * "output_video": "http://192.168.0.116/photoadking_testing/image_bucket/temp/5dfc88daa86c5_outputvideo_1576831194.mp4",
     * "est_time_sec":""
     * }
     * }
     */
    public function checkReadyToPreviewVideo(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['preview_id'], $request)) != '') {
                return $response;
            }

            $preview_id = $request->preview_id;
            $preview_video = '';
            $result = DB::select('SELECT * FROM preview_video_jobs WHERE id = ?', [$preview_id]);
            if (count($result) > 0) {
                $status = $result[0]->status;
                $preview_video = $result[0]->preview_video;
                $id = $result[0]->id;

                $queue_record = DB::select('SELECT count(*) total
                    FROM preview_video_jobs
                    WHERE status = 0 AND id <= ?', [$id]);
                if ($queue_record[0]->total != 0) {
                    $est_time_sec = $queue_record[0]->total * 4;
                } else {
                    $est_time_sec = '';
                }

            } else {
                $status = 2;
                $est_time_sec = '';
            }
            $msg = '';
            $http_code = 200;
            if ($status == 0) {
                $msg = 'Video is not generate for preview';
                $result = ['status' => $status, 'preview_video' => '', 'est_time_sec' => $est_time_sec];
            }
            if ($status == 1) {
                $msg = 'Video is generate for preview';
                $preview_video_file = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$preview_video;
                //        Log::info('generate_video : ', [$preview_video_file]);
                $result = ['status' => $status, 'preview_video' => $preview_video_file, 'est_time_sec' => ''];
            }
            if ($status == 2) {
                $http_code = 201;
                $msg = "Sorry, we couldn't generate video.Please, try again.";
                $result = ['status' => $status, 'preview_video' => '', 'est_time_sec' => $est_time_sec];
            }

            $response = Response::json(['code' => $http_code, 'message' => $msg, 'cause' => '', 'data' => $result]);
        } catch (Exception $e) {
            (new ImageController())->logs('checkReadyToPreviewVideo', $e);
            //      Log::error("checkReadyToPreviewVideo : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'check ready to preview video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }
    /**
     * - admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getPreviewVideoDetail",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getPreviewVideoDetail",
     *        summary="Get preview video detail.",
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
     *          required={"content_id"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Detail fetched successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *        ),
     *    )
     */

    /**
     * @api {post} getPreviewVideoDetail getPreviewVideoDetail
     *
     * @apiName getPreviewVideoDetail
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
     * "content_id":1, //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Detail fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": [
     * {
     * "preview_video_id": 2,
     * "format_name": "mov,mp4,m4a,3gp,3g2,mj2",
     * "duration": "00:00:05",
     * "width": "270",
     * "height": "480",
     * "size": "144.8",
     * "bit_rate": "231678",
     * "genre": "",
     * "tag": "",
     * "title": "",
     * "artist": "",
     * "preview_video": "http://192.168.0.116/photoadking_testing/image_bucket/video/5cc97d9bbf538_preview_video_1556708763.mp4"
     * }
     * ]
     * }
     * }
     */
    public function getPreviewVideoDetail(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;

            $content_detail = DB::select('SELECT * FROM content_master WHERE id = ?', [$content_id]);
            if (count($content_detail) > 0) {

                $content_file = $content_detail[0]->content_file;
                $preview_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$content_file;
                $result = DB::select('SELECT
                                        id AS preview_video_id,
                                        COALESCE(format_name,"") AS format_name,
                                        COALESCE(duration,"") AS duration,
                                        COALESCE(width,"") AS width,
                                        COALESCE(height,"") AS height,
                                        COALESCE(size,"") AS size,
                                        COALESCE(bit_rate,"") AS bit_rate,
                                        COALESCE(genre,"") AS genre,
                                        COALESCE(tag,"") AS tag,
                                        COALESCE(title,"") AS title,
                                        COALESCE(artist,"") AS artist
                                      FROM video_details WHERE content_id = ?', [$content_id]);
            }
            $result[0]->preview_video = $preview_video;
            $response = Response::json(['code' => 200, 'message' => 'Detail fetched successfully.', 'cause' => '', 'data' => ['result' => $result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('getPreviewVideoDetail', $e);
            //      Log::error("getPreviewVideoDetail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'details preview video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/reducePreviewVideo",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="reducePreviewVideo",
     *        summary="Reduce preview video.",
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
     *          required={"content_id"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Preview video generated successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *        ),
     *    )
     */
    /**
     * @api {post} reducePreviewVideo  reducePreviewVideo
     *
     * @apiName reducePreviewVideo
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
     * "content_id":1, //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Preview video generated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function reducePreviewVideo(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;

            $content_detail = DB::select('SELECT * FROM content_master WHERE id = ?', [$content_id]);
            if (count($content_detail) > 0) {

                $content_file = $content_detail[0]->content_file;
                $output_file_name = uniqid().'_'.'preview_video'.'_'.time().'.mp4'; // Generate preview name
                $old_preview_video = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$content_file;
                //        $new_preview_video = Config::get('constant.PREVIEW_VIDEO_DIRECTORY') . $output_file_name;
                $new_preview_video = '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$output_file_name;
                $ffmpeg = Config::get('constant.FFMPEG_PATH');

                $reduce = ' -crf 45 -y ';
                $cmd = "$ffmpeg -i $old_preview_video $reduce $new_preview_video".' 2>&1';

                //        Log::info('reducePreviewVideo', [$cmd]);
                exec($cmd, $output, $result);
                //        Log::info("reducePreviewVideo output : ", [$output]);
                //        Log::info("reducePreviewVideo results : ", [$result]);
                if (file_exists($new_preview_video) && $result == 0) {

                    $data = (new ImageController())->getVideoInformation($new_preview_video);

                    $format_name = $data['format_name'];
                    $duration = $data['duration'];
                    $size = $data['size'];
                    $bit_rate = $data['bit_rate'];
                    $title = $data['title'];
                    $genre = $data['genre'];
                    $artist = $data['artist'];
                    $width = $data['width'];
                    $height = $data['height'];

                    DB::beginTransaction();
                    DB::update('UPDATE content_master
                            SET content_file = ?
                             WHERE id = ?',
                        [$output_file_name, $content_id]);
                    DB::update('UPDATE video_details
                                SET format_name = ?,
                                    duration = ?,
                                    width=?,
                                    height = ?,
                                    size = ?,
                                    bit_rate = ?,
                                    genre = ?,
                                    title = ?,
                                    artist = ?
                                WHERE content_id = ?',
                        [
                            $format_name,
                            $duration,
                            $width,
                            $height,
                            $size,
                            $bit_rate,
                            $genre,
                            $title,
                            $artist,
                            $content_id,
                        ]);
                    DB::commit();

                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveJsonPreviewVideoInToS3($output_file_name, 2);
                    }

                    if (! empty($content_file)) {
                        (new ImageController())->deleteVideo($content_file);
                    }
                    $response = Response::json(['code' => 200, 'message' => 'Preview video generated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
                } else {
                    Log::error('reducePreviewVideo handle()_output : ', [$output]);
                    Log::error('reducePreviewVideo handle()_result : ', [$result]);
                    $response = Response::json(['code' => 201, 'message' => "Sorry, we couldn't generate preview video. Please try again", 'cause' => '', 'data' => json_decode('{}')]);
                }
            } else {
                $response = Response::json(['code' => 201, 'message' => "Sorry, we couldn't generate preview video. Please try again", 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('reducePreviewVideo', $e);
            //      Log::error("reducePreviewVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'reduce preview video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/retryToGenerateVideo",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="retryToGenerateVideo",
     *        summary="Retry To Generate Video",
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
     *          required={"preview_id","content_id"},
     *
     *          @SWG\Property(property="preview_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Preview video generated successfully.","cause":"","data":{"preview_id":1,"content_id":2845}}, description=""),
     *    ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *        ),
     *    )
     */
    /**
     * @api {post} retryToGenerateVideo retryToGenerateVideo
     *
     * @apiName retryToGenerateVideo
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
     * "preview_id":"1", //compulsory
     * "content_id":"1" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Json added successfully.",
     * "cause": "",
     * "data": {
     * "preview_id": 56,
     * "content_id": 67
     * }
     * }
     */
    public function retryToGenerateVideo(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['preview_id', 'content_id'], $request)) != '') {
                return $response;
            }

            $preview_id = $request->preview_id;
            $content_id = $request->content_id;

            $job = new PreviewVideoJob($content_id, $preview_id);
            $data = $this->dispatch($job);
            $result = $job->getResponse();

            if ($result['result_status'] == 0) {
                $response = Response::json(['code' => 201, 'message' => 'Video is unable to preview video', 'cause' => '', 'data' => ['preview_id' => $preview_id, 'content_id' => $content_id]]);
            } else {
                $preview_id = $result['preview_id'];
                $response = Response::json(['code' => 200, 'message' => 'Preview video generated successfully.', 'cause' => '', 'data' => ['preview_id' => $preview_id, 'content_id' => $content_id]]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('retryToGenerateVideo', $e);
            //      Log::error("retryToGenerateVideo : ", ['Exception' => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'retry to generate video.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }
    /* =========================================| Catalog audio |========================================= */

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addNormalAudio",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addNormalAudio",
     *        summary="Add normal audio",
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
     *          description="Give catalog_id,tag and title in json object",
     *
     *         @SWG\Schema(
     *              required={"catalog_id","tag","title"},
     *
     *              @SWG\Property(property="catalog_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="tag",  type="string", example="tag", description=""),
     *              @SWG\Property(property="title",  type="string", example="title", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="Array of normal audio",
     *         required=true,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *
     *      @SWG\Schema(
     *
     *          @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Normal audio added successfully.","cause":"","data":{}}, description=""),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} addNormalAudio addNormalAudio
     *
     * @apiName addNormalAudio
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
     * "request_data":{
     * "catalog_id":2, //compulsory
     * "tag":"sample", //compulsory
     * "title":"title" //compulsory
     * },
     * "file":1.mp3 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal audio added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addNormalAudio(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            //Log::info("Request Data :", [$request]);

            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'catalog_id', 'tag', 'title'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $tag = $request->tag;
            $title = $request->title;
            $credit_note = isset($request->credit_note) ? $request->credit_note : '{}';

            $create_at = date('Y-m-d H:i:s');
            DB::beginTransaction();
            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $audio_file = Input::file('file');
                $category_id = $request->category_id; //Audio category id
                $is_featured = 1; //Here we are passed 1 bcz resource images always uploaded from featured catalogs
                $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is resource images
                if (($response = (new ImageController())->verifyAudio($audio_file, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $audio_name = (new ImageController())->generateNewFileName('audio_file', $audio_file);

                (new ImageController())->saveOriginalAudio($audio_name, $audio_file);

                $content_type = Config::get('constant.CONTENT_TYPE_OF_AUDIO');
                $audio_detail = (new ImageController())->getAudioInformation('../..'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY').$audio_name);

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
                    (new ImageController())->saveAudioInToS3Bucket($audio_name);
                }

                $uuid = (new ImageController())->generateUUID();

                $catalog_detail = DB::select('SELECT name from catalog_master WHERE id = ?', [$catalog_id]);
                $new_tag = str_replace(' ', ',', strtolower(preg_replace('/[^A-Za-z ]/', '', $catalog_detail[0]->name)));
                if ($tag != '') {
                    $tag .= ','.$new_tag;
                } else {
                    $tag .= $new_tag;
                }
                $tag = implode(',', array_unique(array_filter(explode(',', $tag))));

                $data = [
                    'uuid' => $uuid,
                    'catalog_id' => $catalog_id,
                    'content_file' => $audio_name,
                    'content_type' => $content_type,
                    'search_category' => $tag,
                    'create_time' => $create_at,
                ];

                $content_id = DB::table('content_master')->insertGetId($data);

                DB::insert('INSERT INTO audio_master(content_id, file_name, format_name, duration, size, bit_rate, genre, tag, title, artist, credit_note, is_active, create_time)
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ', [$content_id, $audio_name, $format_name, $duration, $size, $bit_rate, $genre, $tag, $title, $artist, json_encode($credit_note), 1, $create_at]);
                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Normal audio added successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('addNormalAudio', $e);
            //      Log::error("addNormalAudio : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add audio by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateNormalAudio",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateNormalAudio",
     *        summary="Add normal audio",
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
     *          description="Give catalog_id,tag and title in json object",
     *
     *         @SWG\Schema(
     *              required={"content_id","tag","title"},
     *
     *              @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *              @SWG\Property(property="tag",  type="string", example="tag", description=""),
     *              @SWG\Property(property="title",  type="string", example="title", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="Array of normal audio",
     *         required=false,
     *         type="file",
     *     ),
     *
     *      @SWG\Response(
     *            response=200,
     *            description="success",
     *
     *      @SWG\Schema(
     *
     *          @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Audio updated successfully.","cause":"","data":{}}, description=""),
     *        ),
     *      ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} updateNormalAudio updateNormalAudio
     *
     * @apiName updateNormalAudio
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
     * "request_data":{
     * "content_id":2, //compulsory
     * "tag":"sample", //compulsory
     * "title":"title" //compulsory
     * },
     * "file":1.mp3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Audio updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateNormalAudio(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            //Required parameter
            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));

            if (($response = (new VerificationController())->validateRequiredParameter(['category_id', 'content_id', 'tag', 'title'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $tag = $request->tag;
            $title = $request->title;
            $credit_note = isset($request->credit_note) ? $request->credit_note : '{}';

            if ($request_body->hasFile('file')) {

                $array_of_audio = Input::file('file');

                $category_id = $request->category_id; //Audio category id
                $is_featured = 1; //Here we are passed 1 bcz resource images always uploaded from featured catalogs
                $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is resource images
                if (($response = (new ImageController())->verifyAudio($array_of_audio, $category_id, $is_featured, $is_catalog)) != '') {
                    return $response;
                }

                $audio_name = (new ImageController())->generateNewFileName('audio_file', $array_of_audio);
                (new ImageController())->saveOriginalAudio($audio_name, $array_of_audio);

                $result = DB::select('SELECT content_file FROM content_master WHERE id = ?', [$content_id]);
                $audio_file_old_name = $result[0]->content_file;

                $audio_detail = (new ImageController())->getAudioInformation('../..'.Config::get('constant.ORIGINAL_AUDIO_DIRECTORY').$audio_name);

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
                    (new ImageController())->saveAudioInToS3Bucket($audio_name);
                }

                DB::beginTransaction();
                DB::update('UPDATE
                              content_master
                            SET
                              content_file = ?
                            WHERE
                              id = ? ',
                    [$audio_name, $content_id]);

                DB::update('UPDATE audio_master
                            SET file_name = ?, format_name = ?, duration = ?, size = ?, bit_rate = ?, genre = ?, tag = ?, title = ?, artist=?, credit_note = ?
                            WHERE content_id = ?', [$audio_name, $format_name, $duration, $size, $bit_rate, $genre, $tag, $title, $artist, json_encode($credit_note), $content_id]);
                DB::update('UPDATE content_master
                            SET content_file = ?, search_category = ?
                            WHERE id = ?', [$audio_name, $tag, $content_id]);

                DB::commit();
                //Delete old Audio from image_bucket
                (new ImageController())->deleteAudio($audio_file_old_name);

            } else {
                $updated_at = date('Y-m-d H:i:s');
                DB::beginTransaction();
                DB::update('UPDATE
                        audio_master SET tag = ?, title = ?, credit_note = ?
                        WHERE content_id = ?', [$tag, $title, json_encode($credit_note), $content_id]);
                DB::update('UPDATE
                        content_master SET search_category = ?,update_time=?
                        WHERE id = ?', [$tag, $updated_at, $content_id]);
                DB::commit();
            }

            $response = Response::json(['code' => 200, 'message' => 'Audio updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateNormalAudio', $e);
            //      Log::error("updateNormalAudio : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update audio by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteNormalAudioByAdmin",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="deleteNormalAudioByAdmin",
     *        summary="Delete Normal Audio By Admin",
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
     *          required={"content_id"},
     *
     *          @SWG\Property(property="content_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *    @SWG\Schema(
     *
     *    @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Normal audio deleted successfully.","cause":"","data":{}}, description=""),
     *    ),
     *        ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *        ),
     *    )
     */
    /**
     * @api {post} deleteNormalAudioByAdmin deleteNormalAudioByAdmin
     *
     * @apiName deleteNormalAudioByAdmin
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
     * "content_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Normal audio deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteNormalAudioByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;
            $result = DB::select('SELECT content_file FROM content_master WHERE id = ?', [$content_id]);
            if (count($result) > 0) {
                $audio_file_name = $result[0]->content_file;

                DB::beginTransaction();

                DB::delete('DELETE FROM content_master WHERE id = ? ', [$content_id]);
                DB::delete('DELETE FROM audio_master WHERE content_id = ? ', [$content_id]);

                DB::commit();

                //Audio delete from image_bucket
                (new ImageController())->deleteAudio($audio_file_name);

                $response = Response::json(['code' => 200, 'message' => 'Normal audio deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Normal audio does not exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('deleteNormalAudioByAdmin', $e);
            //      Log::error("deleteNormalAudioByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete normal audio by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getTagFromImage",
     *        tags={"Admin_video"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getTagFromImage",
     *        summary="Get tag from image",
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
     *         name="sample_image",
     *         in="formData",
     *         description="Sample file uploading",
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
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Tag fetch successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *         @SWG\Schema(
     *
     *            @SWG\Property(property="Sample_Response", type="string", example={"code":201,"message":"Photoad king unable to fetch tag.","cause":"","data":{}}, description=""),
     *        ),
     *      ),
     *    )
     */
    /**
     * @api {post} getTagFromImage getTagFromImage
     *
     * @apiName getTagFromImage
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
     * "sample_image":"sample_image.png" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag fetch successfully.",
     * "cause": "",
     * "data": {
     * "tag_list": "illustration,vector,design,desktop,card,symbol,text,decoration,set,retro,image,art,graphic,collection,business,wallpaper,paper,element,pattern,no person"
     * }
     * }
     */
    public function getTagFromImage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->hasFile('sample_image')) {
                return Response::json(['code' => 201, 'message' => 'Required field sample_image is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $sample_image = Input::file('sample_image');

            if (($response = (new ImageController())->verifySampleImageForVideo($sample_image)) != '') {
                return $response;
            }

            $tag_list = strtolower((new TagDetectController())->getTagInImageByBytes($sample_image));
            if ($tag_list == '' or $tag_list == null) {
                $response = Response::json(['code' => 201, 'message' => 'Tag not detected from clarifai.com.Please write Manually', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Tag fetch successfully.', 'cause' => '', 'data' => ['tag_list' => $tag_list]]);
            }

            //      if (($response = (new VerificationController())->verifySearchCategory("$search_category$tag_list")) != '') {
            //        $response_details = (json_decode(json_encode($response), true));
            //        $data = $response_details['original']['data'];
            //        $tag_list = $data['search_tags'];
            //      } else {
            //        $tag_list = "$search_category$tag_list";
            //      }

        } catch (Exception $e) {
            (new ImageController())->logs('getTagFromImage', $e);
            //      Log::error("getTagFromImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'fetch tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*===================================| Video template Static page generate |================================*/
    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getTemplateBySearchTag",
     *        tags={"admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getTemplateBySearchTag",
     *        summary="Search template by tag",
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
     *          required={"search_category"},
     *
     *          @SWG\Property(property="search_category",  type="string", example="fashion,education,business", description=""),
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
    /**
     * @api {post} getTemplateBySearchTag getTemplateBySearchTag
     *
     * @apiName getTemplateBySearchTag
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
     * "search_category":"fashion,education,business" //compulsory
     * "page":1,//compulsory
     * }
     * @apiSuccessExample Success-Response:
     *{
     *"code": 200,
     *"message": "Template fetch successfully.",
     *"cause": "",
     *"data": {
     *"total_record": 146,
     *"is_next_page": false,
     *"result": [
     *{
     *"content_id": 6815,
     *"thumbnail_img": "http://192.168.0.116/photoadking_testing/image_bucket/thumbnail/5e2ec5e8014c4_sample_image_1580123624.jpg",
     *"compressed_img": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5e2ec5e8014c4_sample_image_1580123624.jpg",
     *"original_img": "http://192.168.0.116/photoadking_testing/image_bucket/original/5e2ec5e8014c4_sample_image_1580123624.jpg",
     *"content_file": "http://192.168.0.116/photoadking_testing/image_bucket/video/5e2ec5eb11744_preview_video_1580123627.mp4",
     *"svg_file": "",
     *"content_type": 9,
     *"catalog_id": 380,
     *"catalog_name": "Instagram post",
     *"sub_category_id": 111,
     *"sub_category_name": "Instagram Post Video",
     *"json_data": {
     *"text_json": [
     *{
     *"xPos": 516,
     *"yPos": 78,
     *"color": "#ffffff",
     *"text": "HERE, AT",
     *"size": 40,
     *"fontName": "Roboto-Bold",
     *"fontPath": "fonts/Roboto-Bold.ttf",
     *"alignment": 1,
     *"bg_image": "",
     *"texture_image": "",
     *"opacity": 100,
     *"angle": 0,
     *"shadowColor": "#000000",
     *"shadowRadius": 0,
     *"shadowDistance": 0
     *},
     *{
     *"xPos": 701,
     *"yPos": 78,
     *"color": "#ffffff",
     *"text": "HOUSEPLANNING",
     *"size": 40,
     *"fontName": "Roboto-Bold",
     *"fontPath": "fonts/Roboto-Bold.ttf",
     *"bg_image": "",
     *"texture_image": "",
     *"opacity": 100,
     *"angle": 0,
     *"shadowColor": "#000000",
     *"shadowRadius": 0,
     *"shadowDistance": 0
     *},
     *{
     *"xPos": 519,
     *"yPos": 135,
     *"color": "#ffffff",
     *"text": "WE BUILD",
     *"size": 60,
     *"fontName": "Roboto-Bold",
     *"fontPath": "fonts/Roboto-Bold.ttf",
     *"alignment": 1,
     *"bg_image": "",
     *"texture_image": "",
     *"opacity": 100,
     *"angle": 0,
     *"shadowColor": "#000000",
     *"shadowRadius": 0,
     *"shadowDistance": 0
     *},
     *{
     *"xPos": 819,
     *"yPos": 135,
     *"color": "#ffffff",
     *"text": "HOMES",
     *"size": 60,
     *"fontName": "Roboto-Bold",
     *"fontPath": "fonts/Roboto-Bold.ttf",
     *"alignment": 1,
     *"bg_image": "",
     *"texture_image": "",
     *"opacity": 100,
     *"angle": 0,
     *"shadowColor": "#000000",
     *"shadowRadius": 0,
     *"shadowDistance": 0
     *},
     *{
     *"xPos": 513,
     *"yPos": 917,
     *"color": "#ffffff",
     *"text": "TELL USE YOUR IDEA",
     *"size": 40,
     *"fontName": "Roboto-Medium",
     *"fontPath": "fonts/Roboto-Medium.ttf",
     *"alignment": 1,
     *"bg_image": "",
     *"texture_image": "",
     *"opacity": 100,
     *"angle": 0,
     *"shadowColor": "#000000",
     *"shadowRadius": 0,
     *"shadowDistance": 0
     *}
     *],
     *"sticker_json": [
     *{
     *"xPos": 351,
     *"yPos": 0,
     *"width": 723,
     *"height": 272,
     *"sticker_image": "instapost_sticker1_mj5_6.png",
     *"angle": 0,
     *"is_round": 0
     *},
     *{
     *"xPos": 429,
     *"yPos": 875,
     *"width": 552,
     *"height": 184,
     *"sticker_image": "instapost_sticker2_mj5_6.png",
     *"angle": 0,
     *"is_round": 0
     *}
     *],
     *"image_sticker_json": [],
     *"frame_json": {
     *"frame_image": "",
     *"frame_color": ""
     *},
     *"background_json": {
     *"background_image": "insta_business2_mj5_6.mp4",
     *"background_color": ""
     *},
     *"sample_image": "instapost_sample_mj5_6.jpg",
     *"height": 1080,
     *"width": 1080,
     *"is_portrait": 1,
     *"is_featured": 0
     *},
     *"is_featured": 1,
     *"is_free": 1,
     *"is_portrait": 1,
     *"search_category": "landscape,safety,financial security ,helmet,people,vertical,headwear,protection,builder,outdoors,adult,horizontal,danger,man,hardhat,trade protection,security,skill,business,industry,architecture",
     *"format_name": "",
     *"duration": "",
     *"size": 0,
     *"bit_rate": "0",
     *"genre": "",
     *"tag": "",
     *"title": ""
     *}
     * ]
     * }
     * }
     */
    public function getTemplateBySearchTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['search_category', 'page'], $request)) != '') {
                return $response;
            }

            $this->search_category = trim(strtolower($request->search_category));
            $this->page = $request->page;
            $this->item_count = isset($request->item_count) ? $request->item_count : Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->offset = ($this->page - 1) * $this->item_count;
            $search_text = trim($this->search_category, '%');
            $default_content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');
            $this->content_type = isset($request->content_type) ? $request->content_type : $default_content_type;

            $redis_result = Cache::rememberforever("getTemplateBySearchTag:$this->content_type:$this->search_category:$this->page:$this->item_count", function () {

                $total_row_result = DB::select('SELECT
                                                  cm.id
                                                FROM
                                                  content_master AS cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                    JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                                WHERE
                                                  cm.is_active = 1 AND
                                                  cm.content_type IN (?) AND
                                                  ctm.is_featured = 1 AND
                                                  isnull(cm.original_img) AND
                                                  isnull(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                 GROUP BY cm.id', [$this->content_type]);

                $total_row = count($total_row_result);
                DB::statement("SET sql_mode = '' ");
                $search_result = DB::select('SELECT
                                                    cm.uuid AS content_uuid,
                                                    ctm.uuid AS catalog_id,
                                                    scm.uuid AS sub_category_id,
                                                    ctm.name AS catalog_name,
                                                    scm.sub_category_name,
                                                    cm.content_type,
                                                    cm.id AS content_id,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                    IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                    COALESCE(cm.image,"") AS svg_file,
                                                    COALESCE(cm.height,0) AS height,
                                                    COALESCE(cm.width,0) AS width
                                                FROM
                                                    content_master as cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                    JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                                WHERE
                                                    cm.is_active = 1 AND
                                                    cm.content_type IN (?) AND
                                                    ctm.is_featured = 1 AND
                                                    ISNULL(cm.original_img) AND
                                                    ISNULL(cm.display_img) AND
                                                    (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                GROUP BY cm.id
                                                ORDER BY cm.update_time DESC LIMIT ?,?', [$this->content_type, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                return $result;
            });

            $search_result = $redis_result['result'];

            if (! $redis_result) {
                $message = "Sorry, we couldn't find any templates for '$search_text'.";
                $response = Response::json(['code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                if (count($search_result) > 0) {
                    $code = 200;
                    $message = 'Template fetch successfully.';
                } else {
                    $code = 201;
                    $message = "Sorry, we couldn't find any templates for '$search_text'.";
                }
                $response = Response::json(['code' => $code, 'message' => $message, 'cause' => '', 'data' => $redis_result]);
            }
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplateBySearchTag', $e);
            //Log::error("getTemplateBySearchTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function updateSearchTagTemplates(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['tag_id', 'tag'], $request)) != '') {
                return $response;
            }

            $tag_id = $request->tag_id;
            $tag = $request->tag;
            $content_ids = isset($request->content_ids) ? $request->content_ids : null;
            if ($content_ids == '') {
                $content_ids = null;
            }
            $sub_category_id = $content_type = 0;

            DB::beginTransaction();
            DB::update('UPDATE tag_master
                  SET
                    content_ids = ?
                  WHERE
                    id = ? ', [$content_ids, $tag_id]);
            DB::commit();

            (new UserController())->deleteAllRedisKeys('getAllTags');
            (new UserController())->deleteAllRedisKeys(Config::get('constant.REDIS_PREFIX')."searchTemplateBySubCategoryId:$sub_category_id:$tag:$content_type");

            $response = Response::json(['code' => 200, 'message' => 'Search tag templates updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateSearchTagTemplates', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update search tag templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*==================================| get user publish design for admin |==========================================*/
    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getUserPublishDesignForAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getUserPublishDesignForAdmin",
     *        summary="Get user publish design for admin",
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
     *          required={"is_publish"},
     *
     *          @SWG\Property(property="is_publish",  type="integer", example=1, description="0=pending ,1=approved, 2= rejected "),
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
    /**
     * @api {post} getUserPublishDesignForAdmin   getUserPublishDesignForAdmin
     *
     * @apiName getUserPublishDesignForAdmin
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
     * "is_publish":1 //0=pending ,1=approved, 2= rejected compulsory
     * "user_id":0,//Optional
     * "content_type":2,//1=image template,2 =video template
     * "page":1,
     * "item_count":10
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Design fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 5,
     * "is_next_page": false,
     * "result": [
     * {
     * "content_id": 207,
     * "user_id": 42,
     * "design_id": 1983,
     * "sub_category_id":23,
     * "thumbnail_img": "http://192.168.0.116/photoadking_testing/image_bucket/my_design/5e565593121b0_my_design_1582716307.jpg",
     * "compressed_img": "http://192.168.0.116/photoadking_testing/image_bucket/my_design/5e565593121b0_my_design_1582716307.jpg",
     * "original_img": "http://192.168.0.116/photoadking_testing/image_bucket/my_design/5e565593121b0_my_design_1582716307.jpg",
     * "content_type": 8,
     * "content_file":"",
     * "json_data": {},
     * "download_json":{}
     * },
     * {
     * "content_id": 21,
     * "user_id": 42,
     * "design_id": 1983,
     * "sub_category_id":24,
     * "thumbnail_img": "http://192.168.0.113/photoadking/image_bucket/thumbnail/5badede53bce0_normal_image_1538125285.png",
     * "compressed_img": "http://192.168.0.113/photoadking/image_bucket/compressed/5badede53bce0_normal_image_1538125285.png",
     * "original_img": "http://192.168.0.113/photoadking/image_bucket/original/5badede53bce0_normal_image_1538125285.png",
     * "content_type": 8,
     * "content_file":"",
     * "json_data": {},
     * "download_json":{}
     * }
     * ]
     * }
     * }
     */
    public function getUserPublishDesignForAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['is_publish', 'content_type', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->is_publish = $request->is_publish;
            $this->content_type = $request->content_type;
            $this->user_id = isset($request->user_id) ? $request->user_id : 0;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if ($this->user_id) {
                $this->by_user = "AND udm.user_id =$this->user_id";
            } else {
                $this->by_user = '';
            }

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getUserPublishDesignForAdmin$this->is_publish:$this->page:$this->item_count:$this->is_publish:$this->user_id:$this->content_type")) {
                $result = Cache::rememberforever("getUserPublishDesignForAdmin$this->is_publish:$this->page:$this->item_count:$this->is_publish:$this->user_id:$this->content_type", function () {
                    $total_row_result = DB::select("SELECT
                                          count(*) AS total
                                        FROM
                                          unpublish_design_master as udm
                                        WHERE
                                          udm.is_publish = ? AND
                                          udm.content_type =?
                                          $this->by_user", [$this->is_publish, $this->content_type]);
                    $total_row = $total_row_result[0]->total;

                    $design = DB::select('SELECT
                                    udm.id AS content_id,
                                    udm.user_id,
                                    udm.design_id,
                                    udm.sub_category_id,
                                    IF(udm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",udm.image),"") AS thumbnail_img,
                                    IF(udm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",udm.image),"") AS compressed_img,
                                    IF(udm.image != "",CONCAT("'.Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",udm.image),"") AS original_img,
                                    IF(udm.video_name != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",udm.video_name),"") AS content_file,
                                    udm.content_type,
                                    coalesce(udm.json_data,"") AS json_data,
                                    coalesce(udm.download_json,"") AS download_json
                                 FROM
                                     unpublish_design_master as udm
                                  WHERE
                                    udm.is_publish = ? AND
                                    udm.content_type =?
                                    '.$this->by_user.'
                                  ORDER BY udm.update_time DESC  LIMIT ?, ?', [$this->is_publish, $this->content_type, $this->offset, $this->item_count]);
                    if (count($design) > 0) {
                        foreach ($design as $row) {
                            $row->json_data = json_decode($row->json_data);
                            $row->download_json = json_decode($row->download_json);
                        }
                    }
                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $design];
                });
            }
            $redis_result = Cache::get("getUserPublishDesignForAdmin$this->is_publish:$this->page:$this->item_count:$this->is_publish:$this->user_id:$this->content_type");
            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Design fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getUserPublishDesignForAdmin', $e);
            //      Log::error("getUserPublishDesignForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get user publish design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* Publish user design by admin */
    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/publishUserDesignByAdmin",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="publishUserDesignByAdmin",
     *        summary="Publish user design by admin",
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
     *          required={"is_publish","design_id"},
     *
     *          @SWG\Property(property="is_publish",  type="integer", example=1, description="0=pending ,1=approved, 2= rejected "),
     *          @SWG\Property(property="design_id",  type="integer", example=1, description=""),
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
    /**
     * @api {post} publishUserDesignByAdmin   publishUserDesignByAdmin
     *
     * @apiName publishUserDesignByAdmin
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
     * "is_publish":1,//0=pending ,1=approved, 2= rejected compulsory
     * "design_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Design publish successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function publishUserDesignByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['is_publish', 'design_id', 'content_type'], $request)) != '') {
                return $response;
            }

            $is_publish = $request->is_publish; //1=approved,2=rejected
            $design_id = $request->design_id;
            $content_type = $request->content_type;

            if ($is_publish == 1) {
                //Save image design
                if ($content_type = Config::get('constant.IMAGE')) {
                    $result = $this->addImageTemplate($request);
                    $response = $result['response'];
                    if ($response != '') {
                        return $response;
                    }
                } else {
                    //Save video design
                    $result = $this->addImageTemplate($request);
                    $response = $result['response'];
                    if ($response != '') {
                        return $response;
                    }
                }

                $response = Response::json(['code' => 200, 'message' => 'Design publish successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                DB::beginTransaction();
                DB::update('update unpublish_design_master set is_publsih =? where design_id=?', [$is_publish, $design_id]);
                DB::update('update my_design_master set is_publsih =? where id=?', [$is_publish, $design_id]);
                DB::commit();

                $response = Response::json(['code' => 200, 'message' => 'Design rejected successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('publishUserDesignByAdmin', $e);
            //      Log::error("publishUserDesignByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get user publish design.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /*=================================Auto Upload Template from other server =======================================  */

    public function getSubCategoryByAppIdForAutoUpload(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['category_id'], $request)) != '') {
                return $response;
            }

            $this->category_id = $request->category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSubCategoryByAppIdForAutoUpload$this->category_id")) {
                $result = Cache::rememberforever("getSubCategoryByAppIdForAutoUpload$this->category_id", function () {
                    return DB::select('SELECT
                                          scm.id as sub_category_id,
                                          scm.category_id,
                                          scm.sub_category_name,
                                          scm.is_featured
                                        FROM
                                          sub_category_master AS scm
                                        WHERE
                                          scm.category_id = ? AND
                                          scm.is_active=?
                                        ORDER BY scm.update_time DESC ', [$this->category_id, 1]);

                });
            }

            $redis_result = Cache::get("getSubCategoryByAppIdForAutoUpload$this->category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sub Category fetched successfully.', 'cause' => '', 'data' => ['category_list' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getSubCategoryByAppIdForAutoUpload', $e);
            //      Log::error("getSubCategoryByAppIdForAutoUpload : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get sub category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getCatalogBySubCategoryIdForAutoUpload(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getCatalogBySubCategoryIdForAutoUpload$this->sub_category_id")) {
                $result = Cache::rememberforever("getCatalogBySubCategoryIdForAutoUpload$this->sub_category_id", function () {
                    return DB::select('SELECT
                                        ct.id as catalog_id,
                                        ct.name,
                                        ct.is_free,
                                        ct.is_featured
                                      FROM
                                        catalog_master as ct,
                                        sub_category_catalog as sct
                                      WHERE
                                        sct.sub_category_id = ? AND
                                        sct.catalog_id=ct.id AND
                                        ct.is_featured = 1 AND
                                        sct.is_active=1
                                      order by ct.update_time DESC', [$this->sub_category_id]);
                });
            }

            $redis_result = Cache::get("getCatalogBySubCategoryIdForAutoUpload$this->sub_category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Catalogs fetched successfully.', 'cause' => '', 'data' => ['total_record' => count($redis_result), 'category_list' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getCatalogBySubCategoryIdForAutoUpload', $e);
            //      Log::error("getCatalogBySubCategoryIdForAutoUpload : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get catalogs.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllValidationsForAdminForAutoUpload(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //      if (($response = (new VerificationController())->validateRequiredParameter(array('sub_category_id'), $request)) != '')
            //        return $response;
            //
            //      $this->sub_category_id = $request->sub_category_id;

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllValidationsForAdminForAutoUpload")) {
                $result = Cache::rememberforever('getAllValidationsForAdminForAutoUpload', function () {

                    //          $category_detail = DB::select('SELECT
                    //                                                     category_id
                    //                                                   FROM
                    //                                                     sub_category
                    //                                                   WHERE is_active = ? AND id =?', [1,$this->sub_category_id]);
                    //          if(count($category_detail) > 0){
                    //            $category_id = $category_detail[0]->category_id;
                    //          }else{
                    //            $category_id ="";
                    //          }

                    $list_of_validations = DB::select('SELECT
                                        id AS setting_id,
                                        category_id,
                                        validation_name,
                                        max_value_of_validation,
                                        is_featured,
                                        is_catalog,
                                        description,
                                        update_time
                                        FROM
                                        settings_master
                                        WHERE is_active = ?
                                        ORDER BY update_time DESC', [1]);

                    //return array('result' => $list_of_validations, 'category_id' => $category_id);
                    return ['result' => $list_of_validations, 'category_id' => null, 'is_multi_page_support' => 1];

                });
            }

            $redis_result = Cache::get('getAllValidationsForAdminForAutoUpload');

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All validations fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllValidationsForAdminForAutoUpload', $e);
            //      Log::error("getAllValidationsForAdminForAutoUpload : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get all validations.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function autoUploadTemplate(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['zip_url', 'zip_name', 'catalog_id', 'is_featured', 'is_free', 'search_category', 'template_name'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $category_id = isset($request->category_id) ? $request->category_id : null;
            $is_featured_catalog = 1; //Here we are passed 1 bcz resource images always uploaded from featured catalogs
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template images
            $is_free = $request->is_free;
            $zip_url = $request->zip_url;
            $zip_name = $request->zip_name;
            $is_featured = $request->is_featured;
            $is_portrait = $request->is_portrait;
            $search_category = strtolower($request->search_category);
            $template_name = $request->template_name;
            $created_at = date('Y-m-d H:i:s');
            $resource_video_name = isset($request->resource_video_name) ? $request->resource_video_name : null;
            $preview_video = isset($request->preview_video) ? $request->preview_video : null;
            $content_type = isset($request->content_type) ? $request->content_type : null;
            $color_value = isset($request->color_value) ? $request->color_value : null;
            $json_data = '';
            $json_file_name = '';
            $video_file_array = [];
            $json_file_array = [];
            $resource_image_array = [];
            $error_msg = '';

            /*Download zip file */
            //            if (Config::get('constant.APP_ENV') != 'local') {
            $zip_store_path = '../..'.Config::get('constant.TEMP_DIRECTORY').$zip_name;
            set_time_limit(0);
            $fp = fopen($zip_store_path, 'w+');
            $ch = curl_init($zip_url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            //            }

            $zip_file_info = pathinfo($zip_name);
            $folder_name = $zip_file_info['filename'];

            $extracted_dir = '../..'.Config::get('constant.TEMP_DIRECTORY').$folder_name;

            $validations = (new ImageController())->getValidationFromCache($category_id, $is_featured_catalog, $is_catalog);
            $IMAGE_MAXIMUM_FILESIZE = $validations * 1024;
            $VIDEO_MAXIMUM_FILESIZE = 5 * 1024 * 1024;

            /* validate file data and extract zip file */
            $zip = new \ZipArchive;

            if ($zip->open($zip_store_path) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $filesize = $zip->statIndex($i)['size'];
                    if ($filesize > 0) {
                        $fileinfo = pathinfo($filename);
                        $basename = $fileinfo['basename'];
                        $extension = $fileinfo['extension'];

                        if ((($extension == 'mp4' || $extension == 'webm' || $extension == 'mov') && $error_msg == '') && $content_type == Config::get('constant.VIDEO')) {
                            $resource_video_name = $basename;
                            array_push($video_file_array, $resource_video_name);
                            if ($filesize >= $VIDEO_MAXIMUM_FILESIZE) {
                                $video_size_mb = $filesize / 1024 / 1024;
                                $video_size_mb = round($video_size_mb, 2);
                                $error_msg = "Resource video file size is greater than 5MB.The size of Background video is $video_size_mb MB.";
                            }
                        } elseif (($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') && $error_msg == '') {
                            $resource_image_name = $basename;
                            array_push($resource_image_array, $resource_image_name);
                            if ($filesize >= $IMAGE_MAXIMUM_FILESIZE) {
                                $error_msg = "Resource image file size is greater than $validations KB.";
                            }
                        } elseif (($extension == 'json' || $extension == 'txt') && $error_msg == '') {
                            array_push($json_file_array, $basename);
                        }
                    }
                }
                $zip->extractTo('../..'.Config::get('constant.TEMP_DIRECTORY'));
                $zip->close();
            }

            /** Delete file if any error in zip data */
            if (empty($json_file_array) || $error_msg != '') {
                (new ImageController())->rrmdir($extracted_dir);
                (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));
            }
            if ($error_msg != '') {
                return Response::json(['code' => 201, 'message' => $error_msg, 'cause' => '', 'data' => json_decode('{}')]);
            }
            if (empty($video_file_array) && $content_type == Config::get('constant.VIDEO')) {
                return Response::json(['code' => 201, 'message' => 'Required file resource video is missing in zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            if (empty($json_file_array)) {
                return Response::json(['code' => 201, 'message' => 'Required file json/txt is missing in zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (is_dir($extracted_dir)) {
                foreach (glob($extracted_dir.'/*') as $filename) {
                    $fileinfo = pathinfo($filename);
                    $extension = $fileinfo['extension'];
                    $file_name = $fileinfo['basename'];
                    if ($extension == 'mp4' && $content_type == Config::get('constant.VIDEO')) {
                        copy($filename, '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$file_name);
                    } elseif ($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg') {
                        copy($filename, '../..'.Config::get('constant.RESOURCE_IMAGES_DIRECTORY').$file_name);
                    } elseif ($extension == 'json' || $extension == 'txt') {
                        $json_file_name = uniqid().'_json_file_'.time().'.'.$extension;
                        copy($filename, '../..'.Config::get('constant.TEMP_DIRECTORY').$json_file_name);
                        $json_data = json_decode(file_get_contents('../..'.Config::get('constant.TEMP_DIRECTORY').$json_file_name));
                    }
                }
            }
            /** Validate font */
            if (($response = (new VerificationController())->validateFonts($json_data)) != '') {
                return $response;
            }

            /* sample image*/
            $sample_image = $json_data->sample_image;
            $fileData = pathinfo(basename($sample_image));
            $catalog_image = uniqid().'_json_image_'.time().'.'.$fileData['extension'];
            copy($extracted_dir.'/'.$sample_image, '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$catalog_image);

            /* preview video*/
            if ($content_type == Config::get('constant.VIDEO')) {
                if ($preview_video) {
                    $fileData = pathinfo($preview_video);
                    $preview_video_name = uniqid().'_preview_video_'.time().'.'.$fileData['extension'];
                    copy($preview_video, '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY').$preview_video_name);
                    $preview_video_path = Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').$preview_video_name;
                } else {
                    (new ImageController())->rrmdir($extracted_dir);
                    (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));

                    if ($resource_video_name != '') {
                        (new ImageController())->unlinkFileFromLocalStorage($resource_video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
                    }
                    if (count($resource_image_array) > 0) {
                        foreach ($resource_image_array as $image_name) {
                            (new ImageController())->unlinkFileFromLocalStorage($image_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
                        }
                    }
                    if ($json_file_name != '') {
                        (new ImageController())->unlinkFileFromLocalStorage($json_file_name, Config::get('constant.TEMP_DIRECTORY'));
                    }

                    return Response::json(['code' => 201, 'message' => 'Required file preview video is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }

            /* check resource image/video exist */
            if (count($resource_image_array) > 0) {
                $exist_files_array = [];
                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    foreach ($resource_image_array as $image_name) {
                        if (($response = (new ImageController())->checkFileExistInS3('resource', $image_name)) == 1) {
                            array_push($exist_files_array, $image_name);
                        }
                    }
                }
                /* Delete file*/
                if (count($exist_files_array) > 0) {

                    (new ImageController())->rrmdir($extracted_dir);
                    (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));

                    if ($resource_video_name != '' && $content_type == Config::get('constant.VIDEO')) {
                        (new ImageController())->unlinkFileFromLocalStorage($resource_video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
                    }
                    if (count($resource_image_array) > 0) {
                        foreach ($resource_image_array as $image_name) {
                            (new ImageController())->unlinkFileFromLocalStorage($image_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
                        }
                    }
                    if ($json_file_name != '') {
                        (new ImageController())->unlinkFileFromLocalStorage($json_file_name, Config::get('constant.TEMP_DIRECTORY'));
                    }
                    /* preview video*/
                    if ($content_type == Config::get('constant.VIDEO')) {
                        (new ImageController())->unlinkFileFromLocalStorage($preview_video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
                    }
                    $array = ['existing_files' => $exist_files_array];
                    $result = json_decode(json_encode($array), true);

                    return $response = Response::json(['code' => 420, 'message' => 'Resource image already exists.', 'cause' => '', 'data' => $result]);
                }
            }
            if (Config::get('constant.STORAGE') === 'S3_BUCKET' && $content_type == Config::get('constant.VIDEO')) {
                if (($response = (new ImageController())->checkFileExistInS3('video', $resource_video_name)) == 1) {

                    (new ImageController())->rrmdir($extracted_dir);
                    (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));

                    if ($resource_video_name != '' && $content_type == Config::get('constant.VIDEO')) {
                        (new ImageController())->unlinkFileFromLocalStorage($resource_video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
                    }
                    if (count($resource_image_array) > 0) {
                        foreach ($resource_image_array as $image_name) {
                            (new ImageController())->unlinkFileFromLocalStorage($image_name, Config::get('constant.RESOURCE_IMAGES_DIRECTORY'));
                        }
                    }
                    if ($json_file_name != '') {
                        (new ImageController())->unlinkFileFromLocalStorage($json_file_name, Config::get('constant.TEMP_FILE_DIRECTORY'));
                    }
                    /* preview video*/
                    if ($content_type == Config::get('constant.VIDEO')) {
                        (new ImageController())->unlinkFileFromLocalStorage($preview_video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
                    }

                    return $response = Response::json(['code' => 420, 'message' => 'Resource video already exist. File name : '.$resource_video_name, 'cause' => '', 'data' => json_decode('{}')]);
                }
            }

            (new ImageController())->saveCompressedImage($catalog_image);
            (new ImageController())->saveThumbnailImage($catalog_image);
            $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
            $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

            if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                (new ImageController())->saveImageInToS3($catalog_image);
                (new ImageController())->saveWebpImageInToS3($file_name);

                /* resource video*/
                if ($content_type == Config::get('constant.VIDEO')) {
                    (new ImageController())->saveJsonVideoInToS3($resource_video_name);
                    (new ImageController())->saveJsonPreviewVideoInToS3($preview_video_name, 2);
                }

                /* resource image */
                if (count($resource_image_array) > 0) {
                    foreach ($resource_image_array as $image_name) {
                        (new ImageController())->saveResourceImageInToS3($image_name);
                    }
                }
            }

            $uuid = (new ImageController())->generateUUID();

            DB::beginTransaction();

            if ($content_type == Config::get('constant.VIDEO')) {
                $content_detail = [
                    'catalog_id' => $catalog_id,
                    'uuid' => $uuid,
                    'image' => $catalog_image,
                    'content_type' => Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON'),
                    'json_data' => json_encode($json_data),
                    'content_file' => $preview_video_name,
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
                    'attribute1' => 1,
                ];
                $content_id = DB::table('content_master')->insertGetId($content_detail);

            } else {
                $content_detail = [
                    'uuid' => $uuid,
                    'catalog_id' => $catalog_id,
                    'image' => $catalog_image,
                    'webp_image' => $file_name,
                    'content_type' => Config::get('constant.CONTENT_TYPE_OF_CARD_JSON'),
                    'json_data' => json_encode($json_data),
                    'is_free' => $is_free,
                    'is_featured' => $is_featured,
                    'is_portrait' => $is_portrait,
                    'search_category' => $search_category,
                    'template_name' => $template_name,
                    'height' => $dimension['height'],
                    'width' => $dimension['width'],
                    'color_value' => $color_value,
                    'create_time' => $created_at,
                    'attribute1' => 1,
                ];
                $content_id = DB::table('content_master')->insertGetId($content_detail);
            }
            DB::commit();

            if (strstr($file_name, '.webp')) {
                $response = Response::json(['code' => 200, 'message' => 'Template uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            } else {
                $response = Response::json(['code' => 200, 'message' => 'Template uploaded successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /** Delete json file from temp folder */
            (new ImageController())->unlinkFileFromLocalStorage($json_file_name, Config::get('constant.TEMP_DIRECTORY'));
            (new ImageController())->rrmdir($extracted_dir);
            (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));

        } catch (Exception $e) {
            (new ImageController())->logs('autoUploadTemplate', $e);
            //      Log::error("autoUploadTemplate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' upload template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            /** Delete json file from temp folder */
            if (isset($json_file_name) && $json_file_name != '') {
                (new ImageController())->unlinkFileFromLocalStorage($json_file_name, Config::get('constant.TEMP_DIRECTORY'));
            }
            if (isset($extracted_dir) && $extracted_dir != '') {
                (new ImageController())->rrmdir($extracted_dir);
                (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));
            }
            (new ImageController())->unlinkFileFromLocalStorage($preview_video_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
            DB::rollBack();
        }

        return $response;
    }

    //for multipage-to-single card upload
    public function autoUploadTemplateV3(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['zip_url', 'zip_name', 'catalog_id', 'is_featured', 'is_portrait', 'is_free', 'search_category'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $category_id = isset($request->category_id) ? $request->category_id : null;
            $is_featured_catalog = 1; //Here we are passed 1 bcz resource images always uploaded from featured catalogs
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template images
            $zip_url = $request->zip_url;
            $zip_name = $request->zip_name;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = $request->is_portrait;
            $search_category = strtolower($request->search_category);
            $created_at = date('Y-m-d H:i:s');
            $content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            $color_value = isset($request->color_value) ? $request->color_value : Config::get('constant.DEFAULT_RANDOM_COLOR_VALUE');
            $resource_image_array = [];
            $sample_image_array = [];
            $webp_image_array = [];
            $error_msg = '';
            $all_json_data = '';
            $uuid_array = [];
            $webp_warning = '';

            $zip_file_directory = '../..'.Config::get('constant.TEMP_DIRECTORY');
            $zip_store_path = $zip_file_directory.$zip_name;
            $pathInfo = pathinfo($zip_store_path);
            $folder_name = $pathInfo['filename'];
            $folder_path = $zip_file_directory.$folder_name;

            //copy designer zip to this server in temp directory
            if (! copy($zip_url, $zip_store_path)) {
                Log::info('autoUploadTemplateV3 : Failed to copy Zip file.');

                return Response::json(['code' => 201, 'message' => 'Failed to copy Zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            set_time_limit(0);

            //extract this zip to temp directory & delete zip file which previously copied
            $zip = new \ZipArchive;
            $res = $zip->open($zip_store_path);
            if ($res === true) {
                $zip->extractTo($zip_file_directory);
                $zip->close();
                (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));
            } else {
                Log::info('autoUploadTemplateV3 : Failed to extract Zip file.');

                return Response::json(['code' => 201, 'message' => 'Failed to extract Zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //get server validation for uploading all resource
            $validations = (new ImageController())->getValidationFromCache($category_id, $is_featured_catalog, $is_catalog);
            $IMAGE_MAXIMUM_FILESIZE = $validations * 1024;

            $all_files = array_diff(scandir($folder_path), ['.', '..']);

            //get all files one by one from extracted zip folder & validate it's size
            foreach ($all_files as $files) {
                $file_info = pathinfo($files);
                $extension = $file_info['extension'];
                $basename = $file_info['basename'];
                $file_size = filesize($folder_path.'/'.$files);

                if (($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'svg') && $error_msg == '') {

                    array_push($resource_image_array, $basename);
                    if ($file_size >= $IMAGE_MAXIMUM_FILESIZE) {
                        $error_msg = "Resource image file size is greater than $validations KB.";
                    }
                } elseif (($extension == 'json' || $extension == 'txt') && $error_msg == '') {

                    $all_json_data = json_decode(file_get_contents($folder_path.'/'.$files));
                }

            }

            //if validation fails then return error message
            if ($error_msg || ! $resource_image_array || ! $all_json_data) {
                (new ImageController())->rrmdir($folder_path);
                Log::info('autoUploadTemplateV3 : Failed to validate Zip data.', ['error_msg' => $error_msg, 'resource_image_array' => $resource_image_array, 'json_data' => $all_json_data]);

                return Response::json(['code' => 201, 'message' => 'Failed to validate Zip data.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            foreach ($all_json_data as $i => $json_data) {
                // check json font exists in this server
                if (($response = (new VerificationController())->validateFonts($json_data)) != '') {
                    (new ImageController())->rrmdir($folder_path);

                    return $response;
                }

                //check height & width of sample image is same as in json
                if (($response = (new VerificationController())->validateHeightWidthOfSampleImage($folder_path.'/'.$json_data->sample_image, $json_data)) != '') {
                    (new ImageController())->rrmdir($folder_path);

                    return $response;
                }

                //remove chart, barcode & qr-code sticker in sticker json
                $stickers_json = $json_data->sticker_json;
                foreach ($stickers_json as $j => $sticker_json) {
                    if ($sticker_json->sticker_type != 1) {
                        unset($all_json_data->{$i}->sticker_json[$j]);
                        unset($resource_image_array[array_search($sticker_json->sticker_image, $resource_image_array)]);
                    }
                }
            }

            $resource_image_directory = Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
            $resource_image_path = '../..'.$resource_image_directory;

            // check resource image is exist or not
            if (count($resource_image_array) > 0) {
                $exist_files_array = [];
                foreach ($resource_image_array as $i => $image_name) {
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        if (($is_exist = (new ImageController())->checkFileExistInS3('resource', $image_name)) == 1) {
                            if (strpos($image_name, '_mcm_')) {
                                unset($resource_image_array[$i]);
                            } else {
                                array_push($exist_files_array, $image_name);
                            }
                        }
                    } else {
                        if (($is_exist = (new ImageController())->checkFileExist($resource_image_path.$image_name)) != 0) {
                            if (strpos($image_name, '_mcm_')) {
                                unset($resource_image_array[$i]);
                            } else {
                                array_push($exist_files_array, $image_name);
                            }
                        }
                    }

                    //if resource image is exist then return error message
                    if (count($exist_files_array) > 0) {
                        (new ImageController())->rrmdir($folder_path);
                        $array = ['existing_files' => $exist_files_array];
                        $result = json_decode(json_encode($array), true);
                        Log::info('autoUploadTemplateV3 : Resource image already exists.', ['result' => $result]);

                        return Response::json(['code' => 420, 'message' => 'Resource image already exists.', 'cause' => '', 'data' => $result]);
                    }

                }
            }

            //upload all resources in resource directory
            if (count($resource_image_array) > 0) {
                foreach ($resource_image_array as $image_name) {
                    copy($folder_path.'/'.$image_name, $resource_image_path.$image_name);
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveResourceImageInToS3($image_name);
                    }
                }
            }

            DB::beginTransaction();
            foreach ($all_json_data as $json_data) {

                $sample_image = $json_data->sample_image;
                $fileData = pathinfo(basename($sample_image));
                $catalog_image = uniqid().'_json_image_'.time().'.'.$fileData['extension'];
                copy($folder_path.'/'.$sample_image, '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$catalog_image);

                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);

                }
                array_push($sample_image_array, $catalog_image);
                array_push($webp_image_array, $file_name);

                if (! (strstr($file_name, '.webp'))) {
                    $webp_warning = 'true';
                }

                $uuid = (new ImageController())->generateUUID();
                array_push($uuid_array, $uuid);

                $content_detail = [
                    'uuid' => $uuid,
                    'catalog_id' => $catalog_id,
                    'image' => $catalog_image,
                    'webp_image' => $file_name,
                    'content_type' => Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON'),
                    'json_data' => json_encode($json_data),
                    'is_free' => $is_free,
                    'is_featured' => $is_featured,
                    'is_portrait' => $is_portrait,
                    'search_category' => $search_category,
                    'height' => $dimension['height'],
                    'width' => $dimension['width'],
                    'color_value' => $color_value,
                    'create_time' => $created_at,
                    'attribute1' => 1,
                ];

                $content_id = DB::table('content_master')->insertGetId($content_detail);
            }

            if ($webp_warning) {
                $response = Response::json(['code' => 200, 'message' => 'Template uploaded successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Template uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            DB::commit();

            (new ImageController())->rrmdir($folder_path);

        } catch (Exception $e) {
            Log::error('autoUploadTemplateV3 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' upload template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            if (isset($folder_path)) {
                (new ImageController())->rrmdir($folder_path);
            }

            if (isset($resource_image_array)) {
                foreach ($resource_image_array as $resource_image_name) {
                    (new ImageController())->deleteResourceImages($resource_image_name);
                }
            }

            if (isset($webp_image_array)) {
                foreach ($webp_image_array as $webp_image_name) {
                    (new ImageController())->deleteWebpImage($webp_image_name);
                }
            }

            if (isset($sample_image_array)) {
                foreach ($sample_image_array as $sample_image_name) {
                    (new ImageController())->deleteImage($sample_image_name);
                }
            }

            DB::rollBack();
        }

        return $response;
    }

    //for multipage-to-multipage card upload
    public function autoUploadTemplateV2(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['zip_url', 'zip_name', 'catalog_id', 'is_featured', 'is_portrait', 'is_free', 'search_category', 'json_pages_sequence', 'template_name'], $request)) != '') {
                return $response;
            }

            $json_pages_sequence = explode(',', $request->json_pages_sequence);
            $catalog_id = $request->catalog_id;
            $category_id = isset($request->category_id) ? $request->category_id : null;
            $is_featured_catalog = 1; //Here we are passed 1 bcz resource images always uploaded from featured catalogs
            $is_catalog = 0; //Here we are passed 0 bcz this is not image of catalog, this is template images
            $zip_url = $request->zip_url;
            $zip_name = $request->zip_name;
            $is_free = $request->is_free;
            $is_featured = $request->is_featured;
            $is_portrait = $request->is_portrait;
            $search_category = json_decode(json_encode($request->search_category), true);
            $template_name = $request->template_name;
            $created_at = date('Y-m-d H:i:s');
            $content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            $color_value = isset($request->color_value) ? $request->color_value : Config::get('constant.DEFAULT_RANDOM_COLOR_VALUE');

            $resource_image_array = [];
            $sample_image_array = [];
            $webp_image_array = [];
            $multiple_images = [];
            $error_msg = '';
            $all_json_data = '';
            $webp_warning = '';
            $error_detail = [];

            $zip_file_directory = '../..'.Config::get('constant.TEMP_DIRECTORY');
            $zip_store_path = $zip_file_directory.$zip_name;
            $pathInfo = pathinfo($zip_store_path);
            $folder_name = $pathInfo['filename'];
            $folder_path = $zip_file_directory.$folder_name;

            //copy designer zip to this server in temp directory
            if (! copy($zip_url, $zip_store_path)) {
                Log::info('autoUploadTemplateV2 : Failed to copy Zip file.');

                return Response::json(['code' => 201, 'message' => 'Failed to copy Zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            set_time_limit(0);

            //extract this zip to temp directory & delete zip file which previously copied
            $zip = new \ZipArchive;
            $res = $zip->open($zip_store_path);
            if ($res === true) {
                $zip->extractTo($zip_file_directory);
                $zip->close();
                (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));
            } else {
                Log::info('autoUploadTemplateV2 : Failed to extract Zip file.');

                return Response::json(['code' => 201, 'message' => 'Failed to extract Zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //get server validation for uploading all resource
            $validations = (new ImageController())->getValidationFromCache($category_id, $is_featured_catalog, $is_catalog);
            $IMAGE_MAXIMUM_FILESIZE = $validations * 1024;

            $all_files = array_diff(scandir($folder_path), ['.', '..']);

            //get all files one by one from extracted zip folder & validate it's size
            foreach ($all_files as $files) {
                $file_info = pathinfo($files);
                $extension = $file_info['extension'];
                $basename = $file_info['basename'];
                $file_size = filesize($folder_path.'/'.$files);

                if (($extension == 'jpg' || $extension == 'png' || $extension == 'jpeg' || $extension == 'svg') && $error_msg == '') {

                    array_push($resource_image_array, $basename);
                    if ($file_size >= $IMAGE_MAXIMUM_FILESIZE) {
                        $error_msg = "Resource image file size is greater than $validations KB.";
                    }
                } elseif (($extension == 'json' || $extension == 'txt') && $error_msg == '') {

                    $all_json_data = json_decode(file_get_contents($folder_path.'/'.$files));
                }

            }

            //if validation fails then return error message
            if ($error_msg || ! $resource_image_array || ! $all_json_data) {
                (new ImageController())->rrmdir($folder_path);
                Log::info('autoUploadTemplateV2 : Failed to validate Zip data.', ['error_msg' => $error_msg, 'resource_image_array' => $resource_image_array, 'json_data' => $all_json_data]);

                return Response::json(['code' => 201, 'message' => 'Failed to validate Zip data.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            foreach ($all_json_data as $i => $json_data) {
                // check json font exists in this server
                if (($response = (new VerificationController())->validateFonts($json_data)) != '') {
                    $error_msg = json_decode(json_encode($response))->original;
                    $error_msg->page_id = $i;
                    $error_msg->pages_sequence = $json_pages_sequence;
                    $error_detail[] = $error_msg;
                }

                //check height & width of sample image is same as in json
                if (($response = (new VerificationController())->validateHeightWidthOfSampleImage($folder_path.'/'.$json_data->sample_image, $json_data)) != '') {
                    $error_msg = json_decode(json_encode($response))->original;
                    $error_msg->page_id = $i;
                    $error_msg->pages_sequence = $json_pages_sequence;
                    $error_detail[] = $error_msg;
                }

                //remove chart, barcode & qr-code sticker in sticker json
                /* Remove sticker object and it's images if it's not image sticker (1=image, 2=barcode, 3=qrcode, 4=chart) ONLY WHEN CARD UPLOAD IN PHOTOADKING  */
                $stickers_json = $json_data->sticker_json;
                foreach ($stickers_json as $j => $sticker_json) {
                    if (isset($sticker_json->sticker_type) && $sticker_json->sticker_type != 1) {
                        //unset($all_json_data->{$i}->sticker_json[$j]);
                        //array_splice($stickers_json,$j,1);
                        unset($stickers_json[$j]);
                        unset($resource_image_array[array_search($sticker_json->sticker_image, $resource_image_array)]);
                    }
                }
                //$all_json_data->{$i}->sticker_json = $stickers_json;
                $all_json_data->{$i}->sticker_json = array_values($stickers_json);
            }

            if ($error_detail) {
                (new ImageController())->rrmdir($folder_path);

                return Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'upload template.', 'data' => $error_detail]);
            }

            $resource_image_directory = Config::get('constant.RESOURCE_IMAGES_DIRECTORY');
            $resource_image_path = '../..'.$resource_image_directory;

            // check resource image is exist or not
            if (count($resource_image_array) > 0) {
                $exist_files_array = [];
                foreach ($resource_image_array as $i => $image_name) {
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        if (($is_exist = (new ImageController())->checkFileExistInS3('resource', $image_name)) == 1) {
                            if (strpos($image_name, '_mcm_')) {
                                unset($resource_image_array[$i]);
                            } else {
                                array_push($exist_files_array, $image_name);
                            }
                        }
                    } else {
                        if (($is_exist = (new ImageController())->checkFileExist($resource_image_path.$image_name)) != 0) {
                            if (strpos($image_name, '_mcm_')) {
                                unset($resource_image_array[$i]);
                            } else {
                                array_push($exist_files_array, $image_name);
                            }
                        }
                    }

                    //if resource image is exist then return error message
                    if (count($exist_files_array) > 0) {
                        $array = ['existing_files' => $exist_files_array];
                        $result = json_decode(json_encode($array), true);
                        Log::info('autoUploadTemplateV2 : Resource image already exists.', ['result' => $result]);

                        return Response::json(['code' => 420, 'message' => 'Resource image already exists.', 'cause' => '', 'data' => $result]);
                    }

                }
            }

            //upload all resources in resource directory
            if (count($resource_image_array) > 0) {
                foreach ($resource_image_array as $image_name) {
                    copy($folder_path.'/'.$image_name, $resource_image_path.$image_name);
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveResourceImageInToS3($image_name);
                    }
                }
            }

            DB::beginTransaction();
            foreach ($all_json_data as $i => $json_data) {

                if (! isset($json_data->total_objects)) {
                    (new UserController())->addIndexInImageJsonWhileCardUploading($json_data);
                }

                $sample_image = $json_data->sample_image;
                $fileData = pathinfo(basename($sample_image));
                $catalog_image = uniqid().'_json_image_'.time().'.'.$fileData['extension'];
                copy($folder_path.'/'.$sample_image, '../..'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY').$catalog_image);

                (new ImageController())->saveCompressedImage($catalog_image);
                (new ImageController())->saveThumbnailImage($catalog_image);
                $file_name = (new ImageController())->saveWebpOriginalImage($catalog_image);
                $dimension = (new ImageController())->saveWebpThumbnailImage($catalog_image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($catalog_image);
                    (new ImageController())->saveWebpImageInToS3($file_name);

                }
                array_push($sample_image_array, $catalog_image);
                array_push($webp_image_array, $file_name);

                $multiple_images[$i] = ['name' => $catalog_image, 'webp_name' => $file_name, 'width' => $dimension['width'], 'height' => $dimension['height']];

                if (! (strstr($file_name, '.webp'))) {
                    $webp_warning = 'true';
                }
            }

            $uuid = (new ImageController())->generateUUID();

            $content_detail = [
                'uuid' => $uuid,
                'catalog_id' => $catalog_id,
                'image' => $multiple_images[$json_pages_sequence[0]]['name'],
                'webp_image' => $multiple_images[$json_pages_sequence[0]]['webp_name'],
                'multiple_images' => json_encode($multiple_images),
                'content_type' => Config::get('constant.CONTENT_TYPE_OF_CARD_JSON'),
                'template_name' => $template_name,
                'json_data' => json_encode($all_json_data),
                'is_free' => $is_free,
                'is_featured' => $is_featured,
                'is_portrait' => $is_portrait,
                //'search_category' => implode(',',array_unique(array_filter($search_category))),
                'search_category' => implode(',', array_unique(array_filter(explode(',', implode(',', $search_category))))),
                'height' => $multiple_images[$json_pages_sequence[0]]['height'],
                'width' => $multiple_images[$json_pages_sequence[0]]['width'],
                'color_value' => $color_value,
                'create_time' => $created_at,
                'json_pages_sequence' => implode(',', $json_pages_sequence),
                'attribute1' => 1,
                'attribute2' => 1,
            ];

            DB::table('content_master')->insert($content_detail);
            DB::commit();

            if ($webp_warning) {
                $response = Response::json(['code' => 200, 'message' => 'Template uploaded successfully. Note: webp is not converted due to size grater than original.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'Template uploaded successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            (new ImageController())->rrmdir($folder_path);

        } catch (Exception $e) {
            Log::error('autoUploadTemplateV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' upload template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            if (isset($folder_path)) {
                (new ImageController())->rrmdir($folder_path);
            }

            if (isset($resource_image_array)) {
                foreach ($resource_image_array as $resource_image_name) {
                    (new ImageController())->deleteResourceImages($resource_image_name);
                }
            }

            if (isset($webp_image_array)) {
                foreach ($webp_image_array as $webp_image_name) {
                    (new ImageController())->deleteWebpImage($webp_image_name);
                }
            }

            if (isset($sample_image_array)) {
                foreach ($sample_image_array as $sample_image_name) {
                    (new ImageController())->deleteImage($sample_image_name);
                }
            }

            DB::rollBack();
        }

        return $response;
    }

    /* =====================================| operation in XML file |===================================== */

    public function editDataInXMLFileBackUp(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field xml file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $filename = '../../sitemap.xml';
            $file = file_get_contents($request_body->file('file'));

            file_put_contents($filename, $file);
            $result = file_get_contents($filename);

            $response = Response::json(['code' => 200, 'message' => 'File updated successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('editDataInXMLFile', $e);
            //Log::error("editDataInXMLFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getDataFromXMLFileBackUp(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $filename = '../../sitemap.xml';
            $result = file_get_contents($filename);
            $response = Response::json(['code' => 200, 'message' => 'File updated successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getDataFromXMLFile', $e);
            //Log::error("getDataFromXMLFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*
    Purpose : for edit the data in xml file from admin panel
    Description : This method compulsory take 2 argument as parameter.(no argument is optional )
    Return : return data of updated file if success otherwise error with specific status code
    */
    public function editDataInXMLFile(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->request_data);
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url', 'is_new_file'], $request)) != '') {
                return $response;
            }

            $page_url = preg_replace('/(\/+)/', '', $request->page_url);
            $is_new_file = $request->is_new_file;

            //check request data has file or not
            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field xml file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $filename = '../../'.$page_url;
            //check if file is new and file is exist or not

            if ($is_new_file && file_exists($filename)) {
                return Response::json(['code' => 201, 'message' => 'File is already exist on this path.', 'cause' => '', 'data' => json_decode('{}')]);

            } elseif ($is_new_file != 1 && ! file_exists($filename)) {
                return Response::json(['code' => 201, 'message' => 'No such file or directory exist on this path.', 'cause' => '', 'data' => json_decode('{}')]);

            } else {
                $file = file_get_contents($request_body->file('file'));
                //put the file content in xml file
                file_put_contents($filename, $file);
                $result = file_get_contents($filename);
            }

            $response = Response::json(['code' => 200, 'message' => 'File updated successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('editDataInXMLFile', $e);
            //Log::error("editDataInXMLFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*
    Purpose : for get the data of xml file from admin panel
    Description : This method compulsory take 1 argument as parameter.(no argument is optional )
    Return : return data of xml file if success otherwise error with specific status code
    */
    public function getDataFromXMLFile(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url'], $request)) != '') {
                return $response;
            }

            //getting data from url
            $page_url = preg_replace('/(\/+)/', '', $request->page_url);
            $filename = '../../'.$page_url;
            if (file_exists($filename)) {
                $result = file_get_contents($filename);
                $response = Response::json(['code' => 200, 'message' => 'File data get successfully.', 'cause' => '', 'data' => $result]);

            } else {
                return Response::json(['code' => 201, 'message' => 'No such file or directory exist on this path.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getDataFromXMLFile', $e);
            //Log::error("getDataFromXMLFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| operation in design page |===================================== */

    public function verifyURLForDesignPageCreation(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url'], $request)) != '') {
                return $response;
            }

            $page_url = preg_replace('/(\/+)/', '/', '/'.$request->page_url.'/');
            $file_name = isset($request->file_name) ? $request->file_name : 'index.html';
            $design_page_dir = '../../'.Config::get('constant.DESIGN_PAGE_DIRECTORY');
            $folder_path = $design_page_dir.$page_url;
            $file_dir = $folder_path.$file_name;

            if (substr_count($page_url, '/') > 3) {
                return Response::json(['code' => 201, 'message' => 'Url must be of parent/child or parent format.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (file_exists($file_dir)) {
                return Response::json(['code' => 201, 'message' => 'Design page already exist on this path.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (substr_count($page_url, '/') > 2) {

                $trim_page_url = trim($page_url, '/');
                $parent_url = '/'.substr($trim_page_url, 0, strpos($trim_page_url, '/')).'/';
                $parent_folder_path = $design_page_dir.$parent_url.$file_name;

                if (! file_exists($parent_folder_path)) {
                    return Response::json(['code' => 201, 'message' => 'index.html does not exist in parent folder.', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }

            $response = Response::json(['code' => 200, 'message' => 'URL verified successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('verifyURLForDesignPageCreation', $e);
            //Log::error("verifyURLForDesignPageCreation : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'verified URL.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function setChangesInDesignPageV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url'], $request)) != '') {
                return $response;
            }

            if (! $request_body->hasFile('file')) {
                Log::error('setChangesInDesignPage : Required field file is missing or empty. ');

                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $page_url = preg_replace('/(\/+)/', '/', '/'.$request->page_url.'/');
            $html_content = file_get_contents($request_body->file('file'));
            $file_name = isset($request->file_name) ? $request->file_name : 'index.html';
            $content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.IMAGE');
            $is_new_page = isset($request->is_new_page) ? $request->is_new_page : 1;
            $design_page_dir = '../../'.Config::get('constant.DESIGN_PAGE_DIRECTORY');
            $folder_path = $design_page_dir.$page_url;
            $file_dir = $folder_path.$file_name;

            if (substr_count($page_url, '/') > 3) {
                return Response::json(['code' => 201, 'message' => 'Url must be of parent/child or parent format.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($is_new_page) {
                if (file_exists($file_dir)) {
                    return Response::json(['code' => 201, 'message' => 'Design page already exist on this path.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                if (substr_count($page_url, '/') > 2) {

                    $trim_page_url = trim($page_url, '/');
                    $parent_url = '/'.substr($trim_page_url, 0, strpos($trim_page_url, '/')).'/';
                    $parent_folder_path = $design_page_dir.$parent_url.$file_name;

                    if (! file_exists($parent_folder_path)) {
                        return Response::json(['code' => 201, 'message' => 'index.html does not exist in parent folder.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }
            }

            DB::beginTransaction();
            $is_exist = DB::select('SELECT 1 FROM design_page_master WHERE page_url = ?', [$page_url]);

            if ($is_exist) {
                DB::update('UPDATE design_page_master SET html_content = ? WHERE page_url = ? AND version = ?', [$html_content, $page_url, 2]);

            } else {
                $uuid_v1 = (new ImageController())->generateUUID();
                $uuid_v2 = (new ImageController())->generateUUID();

                if (($is_exist = ((new ImageController())->checkFileExist($file_dir)) != 0)) {
                    $old_html_content = file_get_contents($file_dir);
                } else {
                    $old_html_content = $html_content;
                }

                $data = [
                    [
                        'uuid' => $uuid_v1,
                        'page_url' => $page_url,
                        'html_content' => $old_html_content,
                        'content_type' => $content_type,
                        'version' => 1,
                    ], [
                        'uuid' => $uuid_v2,
                        'page_url' => $page_url,
                        'html_content' => $html_content,
                        'content_type' => $content_type,
                        'version' => 2,
                    ]];

                DB::table('design_page_master')->insert($data);
            }

            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w');
            fwrite($handle, $html_content);
            (new UserController())->deleteAllRedisKeys('getDesignPageChangesList');

            if (! $is_new_page && Config::get('constant.LIVE_SERVER_NAME') == request()->getHttpHost()) {

                //run script file that contain git add, git commit & git push command
                $process = new Process(['..//git_script.sh']);
                $process->run();

                //executes after the command finishes
                if ($process->isSuccessful()) {
                    Log::info('setChangesInDesignPage : script file processed successfully.', ['output' => $process->getOutput()]);
                } else {
                    Log::error('setChangesInDesignPage : Unable to process script file.');
                    throw new ProcessFailedException($process);
                }
            }
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'File updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setChangesInDesignPage', $e);
            //Log::error("setChangesInDesignPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function setChangesInDesignPage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url', 'zip_url'], $request)) != '') {
                return $response;
            }

            $page_url = preg_replace('/(\/+)/', '/', '/'.$request->page_url.'/');
            $file_name = isset($request->file_name) ? $request->file_name : 'index.html';
            $content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.IMAGE');
            $is_new_page = isset($request->is_new_page) ? $request->is_new_page : 1;
            $design_page_dir = '../../'.Config::get('constant.DESIGN_PAGE_DIRECTORY');
            $folder_path = $design_page_dir.$page_url;
            $file_dir = $folder_path.$file_name;

            $zip_url = $request->zip_url;
            $temp_folder_path = '../..'.Config::get('constant.TEMP_DIRECTORY');
            $zip_array = explode(';base64,', $zip_url);
            $zip_contents = base64_decode($zip_array[1]);
            $zip_name = 'download.zip';
            $zip_store_path = $temp_folder_path.$zip_name;
            $text_file_name = 'request.txt';
            $request_file_path = $temp_folder_path.$text_file_name;

            if (substr_count($page_url, '/') > 3) {
                return Response::json(['code' => 201, 'message' => 'Url must be of parent/child or parent format.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($is_new_page) {
                if (file_exists($file_dir)) {
                    return Response::json(['code' => 201, 'message' => 'Design page already exist on this path.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                if (substr_count($page_url, '/') > 2) {

                    $trim_page_url = trim($page_url, '/');
                    $parent_url = '/'.substr($trim_page_url, 0, strpos($trim_page_url, '/')).'/';
                    $parent_folder_path = $design_page_dir.$parent_url.$file_name;

                    if (! file_exists($parent_folder_path)) {
                        return Response::json(['code' => 201, 'message' => 'index.html does not exist in parent folder.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }
            }

            file_put_contents($zip_store_path, $zip_contents);
            //extract this zip to temp directory & delete zip file which previously copied
            $zip = new \ZipArchive;
            $res = $zip->open($zip_store_path);
            if ($res === true) {
                $zip->extractTo($temp_folder_path);
                $zip->close();
                (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY'));
                $html_content = file_get_contents($request_file_path);
                (new ImageController())->unlinkFileFromLocalStorage($text_file_name, Config::get('constant.TEMP_DIRECTORY'));
            } else {
                Log::info('setChangesInDesignPage : Failed to extract Zip file.');

                return Response::json(['code' => 201, 'message' => 'Failed to extract Zip file.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            $is_exist = DB::select('SELECT 1 FROM design_page_master WHERE page_url = ?', [$page_url]);

            if ($is_exist) {
                DB::update('UPDATE design_page_master SET html_content = ? WHERE page_url = ? AND version = ?', [$html_content, $page_url, 2]);

            } else {
                $uuid_v1 = (new ImageController())->generateUUID();
                $uuid_v2 = (new ImageController())->generateUUID();

                if (($is_exist = ((new ImageController())->checkFileExist($file_dir)) != 0)) {
                    $old_html_content = file_get_contents($file_dir);
                } else {
                    $old_html_content = $html_content;
                }

                $data = [
                    [
                        'uuid' => $uuid_v1,
                        'page_url' => $page_url,
                        'html_content' => $old_html_content,
                        'content_type' => $content_type,
                        'version' => 1,
                    ], [
                        'uuid' => $uuid_v2,
                        'page_url' => $page_url,
                        'html_content' => $html_content,
                        'content_type' => $content_type,
                        'version' => 2,
                    ]];

                DB::table('design_page_master')->insert($data);
            }

            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w');
            fwrite($handle, $html_content);
            (new UserController())->deleteAllRedisKeys('getDesignPageChangesList');

            if (! $is_new_page && Config::get('constant.LIVE_SERVER_NAME') == request()->getHttpHost()) {

                //run script file that contain git add, git commit & git push command
                $process = new Process(['..//git_script.sh']);
                $process->run();

                //executes after the command finishes
                if ($process->isSuccessful()) {
                    Log::info('setChangesInDesignPage : script file processed successfully.', ['output' => $process->getOutput()]);
                } else {
                    Log::error('setChangesInDesignPage : Unable to process script file.');
                    throw new ProcessFailedException($process);
                }
            }
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'File updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setChangesInDesignPage', $e);
            //Log::error("setChangesInDesignPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            isset($zip_name) ? (new ImageController())->unlinkFileFromLocalStorage($zip_name, Config::get('constant.TEMP_DIRECTORY')) : '';
            isset($text_file_name) ? (new ImageController())->unlinkFileFromLocalStorage($text_file_name, Config::get('constant.TEMP_DIRECTORY')) : '';
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getDesignPageChangesList(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.IMAGE');

            $redis_result = Cache::rememberforever("getDesignPageChangesList:$this->content_type:$this->page:$this->item_count", function () {

                $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
                $design_page_dir = Config::get('constant.DESIGN_PAGE_DIRECTORY');

                $total_row_result = DB::select('SELECT
                                          COUNT(*) AS total
                                      FROM
                                          design_page_master AS dpm
                                      WHERE
                                          content_type = ? AND
                                          version = ? ', [$this->content_type, 2]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                          CONCAT("'.$active_path.'/'.$design_page_dir.'", dpm.page_url) AS page_url,
                                          dpm.update_time
                                      FROM
                                          design_page_master AS dpm
                                      WHERE
                                          content_type = ? AND
                                          version = ?
                                      ORDER BY update_time DESC
                                          LIMIT ?,?', [$this->content_type, 2, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];

            });

            if (! $redis_result) {
                $redis_result = json_decode('{}');
            }

            $response = Response::json(['code' => 200, 'message' => 'Changes list fetch successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getDesignPageChangesList', $e);
            //Log::error("getDesignPageChangesList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getDesignFileContent(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url'], $request)) != '') {
                return $response;
            }

            $page_url = preg_replace('/(\/+)/', '/', '/'.$request->page_url.'/');
            $file_name = 'index.html';
            $design_page_dir = Config::get('constant.DESIGN_PAGE_DIRECTORY');
            $folder_path = '../../'.$design_page_dir.$page_url;
            $file_dir = $folder_path.$file_name;

            if (file_exists($file_dir)) {
                $redis_result = file_get_contents($file_dir);
                $response = Response::json(['code' => 200, 'message' => 'Content list fetch successfully.', 'cause' => '', 'data' => $redis_result]);

            } else {
                $response = Response::json(['code' => 404, 'message' => 'File not found.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getDesignFileContent', $e);
            //Log::error("getDesignFileContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| operation in design page with json |===================================== */

    public function getDesignPageJsonList(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = $request->content_type;

            $redis_result = Cache::rememberforever("getDesignPageJsonList:$this->content_type:$this->page:$this->item_count", function () {

                $total_row_result = DB::select('SELECT
                                          COUNT(id) AS total
                                      FROM
                                          design_page_json_master AS dpjm
                                      WHERE
                                          dpjm.content_type = ?', [$this->content_type]);
                $total_row = $total_row_result[0]->total;

                $result = DB::select('SELECT
                                          dpjm.id,
                                          dpjm.uuid,
                                          IF(dpjm.page_url != "", CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'", dpjm.page_url), "") AS page_url,
                                          dpjm.json_data,
                                          dpjm.is_json,
                                          dpjm.update_time
                                      FROM
                                          design_page_json_master AS dpjm
                                      WHERE
                                          dpjm.content_type = ?
                                      ORDER BY
                                            dpjm.update_time DESC
                                      LIMIT ?, ?', [$this->content_type, $this->offset, $this->item_count]);

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result];

            });

            $response = Response::json(['code' => 200, 'message' => 'Design page json list fetch successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getDesignPageJsonList', $e);
            //Log::error("getDesignPageJsonList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function generateAllDesignPageByAdmin()
    {
        $this->api_name = 'generateAllDesignPageByAdmin';
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (Cache::has("$this->api_name")) {
                $response = Response::json(['code' => 200, 'message' => 'There are no any changes for generate pages.', 'cause' => '', 'data' => 0]);

            } else {
                $redis_result = Cache::rememberforever("$this->api_name", function () {

                    $counter = 0;
                    $results = DB::select('SELECT
                                          dpjm.page_url,
                                          dpjm.json_data,
                                          dpjm.content_type,
                                          dpjm.is_json
                                      FROM
                                          design_page_json_master AS dpjm');

                    foreach ($results as $i => $result) {

                        $counter++;
                        $file_name = 'index.html';
                        $html = $json_data = $result->json_data;
                        $folder_path = '../..'.$result->page_url;
                        $job_dir = "./..$result->page_url";

                        if ($result->is_json) {

                            if ($result->content_type == Config::get('constant.CONTENT_TYPE_OF_CARD_JSON')) {
                                $html = view('image_design_page', ['json_data' => json_decode($json_data)])->render();
                            } else {
                                $html = view('video_design_page', ['json_data' => json_decode($json_data)])->render();
                            }

                        }

                        if (! is_dir($folder_path)) {
                            Mkdir::dispatch($job_dir, 755, true, $this->api_name);
                        }
                        FilePutContents::dispatch($job_dir.$file_name, $html, $this->api_name);
                    }

                    return $counter;
                });

                $response = Response::json(['code' => 200, 'message' => 'Design pages generated successfully.', 'cause' => '', 'data' => $redis_result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs($this->api_name, $e);
            //Log::error($this->api_name . " : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate design page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function addCtaDetailInDesignPage()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $counter = 0;
            $results = DB::select('SELECT
                                  dpjm.id AS design_page_id,
                                  dpjm.page_url,
                                  dpjm.json_data,
                                  dpjm.content_type,
                                  dpjm.is_json
                              FROM
                                  design_page_json_master AS dpjm
                              WHERE
                                  dpjm.is_json = 1');

            DB::beginTransaction();
            foreach ($results as $i => $result) {

                $counter++;
                $cta_details = (new StaticPageController())->getMappingData(basename(parse_url($result->page_url, PHP_URL_PATH)));
                $json_data = json_decode($result->json_data);
                $app_cta_detail = json_decode('{}');
                $app_cta_detail->app_cta_text = $cta_details['cta_text'];
                $app_cta_detail->playStoreLink = $cta_details['destination']->playStoreLink;
                $app_cta_detail->appStoreLink = $cta_details['destination']->appStoreLink;
                $json_data->app_cta_detail = $app_cta_detail;

                DB::update('UPDATE
                      design_page_json_master
                    SET
                      json_data = ?,
                      update_time = update_time
                    WHERE
                      id = ? ',
                    [json_encode($json_data), $result->design_page_id]);
            }
            DB::commit();
            $response = Response::json(['code' => 200, 'message' => 'CTA detail added in design page successfully.', 'cause' => '', 'data' => json_decode('{}'), 'counter' => $counter]);

        } catch (Exception $e) {
            (new ImageController())->logs('addCtaDetailInDesignPage', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add cta detail in design page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function generateDesignPageByAdmin(Request $request_body)
    {
        $this->api_name = 'generateDesignPageByAdmin';
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page_url', 'json_data', 'content_type'], $request)) != '') {
                return $response;
            }

            $page_url = str_replace(config('constant.ACTIVATION_LINK_PATH'), '', $request->page_url);
            $design_page_url = preg_replace('/(\/+)/', '/', '/'.$page_url.'/');
            $json_data = $request->json_data;
            $content_type = $request->content_type;
            $file_name = isset($request->file_name) ? $request->file_name : 'index.html';
            $folder_path = '../..'.$design_page_url;
            $job_dir = "./..$design_page_url";

            if (strlen($request->page_url) == strlen($page_url)) {
                Log::error('editDesignPageByAdmin : requested_page_url == page_url.', ['requested_page_url' => $request->page_url, 'page_url' => $page_url]);

                return Response::json(['code' => 201, 'message' => 'Something went wrong.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();

            $uuid = (new ImageController())->generateUUID();
            $design_page_data = [
                'uuid' => $uuid,
                'page_url' => $design_page_url,
                'json_data' => json_encode($json_data),
                'content_type' => $content_type,
            ];

            DB::table('design_page_json_master')->insert($design_page_data);

            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_CARD_JSON')) {
                $html = view('image_design_page', compact('json_data'))->render();
            } else {
                $html = view('video_design_page', compact('json_data'))->render();
            }

            if (! is_dir($folder_path)) {
                Mkdir::dispatch($job_dir, 755, true, $this->api_name);
            }
            FilePutContents::dispatch($job_dir.$file_name, $html, $this->api_name);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Design page generated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            (new UserController())->deleteMultipleRedisKeys(["getDesignPageJsonList:$content_type", 'generateAllDesignPage']);

        } catch (Exception $e) {
            DB::rollBack();
            (new ImageController())->logs($this->api_name, $e);
            //Log::error($this->api_name . " : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function editDesignPageByAdmin(Request $request_body)
    {
        $this->api_name = 'editDesignPageByAdmin';
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['id', 'page_url', 'json_data', 'content_type'], $request)) != '') {
                return $response;
            }

            $id = $request->id;
            $page_url = str_replace(config('constant.ACTIVATION_LINK_PATH'), '', $request->page_url);
            $design_page_url = preg_replace('/(\/+)/', '/', '/'.$page_url.'/');
            $json_data = $request->json_data;
            $content_type = $request->content_type;
            $file_name = isset($request->file_name) ? $request->file_name : 'index.html';
            $folder_path = '../..'.$design_page_url;
            $job_dir = "./..$design_page_url";

            if (strlen($request->page_url) == strlen($page_url)) {
                Log::error('editDesignPageByAdmin : requested_page_url == page_url.', ['requested_page_url' => $request->page_url, 'page_url' => $page_url]);

                return Response::json(['code' => 201, 'message' => 'Something went wrong.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            $query = DB::update('UPDATE
                                    design_page_json_master
                                SET
                                    json_data = ?
                                WHERE
                                    id = ?', [json_encode($json_data), $id]);

            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_CARD_JSON')) {
                $html = view('image_design_page', compact('json_data'))->render();
            } else {
                $html = view('video_design_page', compact('json_data'))->render();
            }

            if (! is_dir($folder_path)) {
                Mkdir::dispatch($job_dir, 755, true, $this->api_name);
            }
            FilePutContents::dispatch($job_dir.$file_name, $html, $this->api_name);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Design page generated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            (new UserController())->deleteMultipleRedisKeys(["getDesignPageJsonList:$content_type", 'generateAllDesignPage']);

        } catch (Exception $e) {
            DB::rollBack();
            (new ImageController())->logs($this->api_name, $e);
            Log::error($this->api_name.' : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit design page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function moveDesignPageByAdmin(Request $request_body)
    {
        $this->api_name = 'moveDesignPageByAdmin';
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['id', 'old_page_url', 'new_page_url', 'content_type'], $request)) != '') {
                return $response;
            }

            $id = $request->id;
            $content_type = $request->content_type;
            $old_page_url = str_replace(config('constant.ACTIVATION_LINK_PATH'), '', $request->old_page_url);
            $old_design_page_url = preg_replace('/(\/+)/', '/', '/'.$old_page_url.'/');
            $old_file_dir = './..'.$old_design_page_url;

            $new_page_url = str_replace(config('constant.ACTIVATION_LINK_PATH'), '', $request->new_page_url);
            $new_design_page_url = preg_replace('/(\/+)/', '/', '/'.$new_page_url.'/');
            $new_file_dir = './..'.$new_design_page_url;

            RunCommandAsRoot::dispatch("mv $old_file_dir $new_file_dir", $this->api_name);

            DB::beginTransaction();
            $query = DB::update('UPDATE
                                    design_page_json_master
                                SET
                                    page_url = ?
                                WHERE
                                    id = ?', [$new_design_page_url, $id]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Design page moved successfully.', 'cause' => '', 'data' => json_decode('{}')]);

            (new UserController())->deleteMultipleRedisKeys(["getDesignPageJsonList:$content_type", 'generateAllDesignPage']);

        } catch (Exception $e) {
            DB::rollBack();
            (new ImageController())->logs($this->api_name, $e);
            //Log::error($this->api_name . " : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'moved design page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function previewDesignPageByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['json_data', 'content_type'], $request)) != '') {
                return $response;
            }

            $json_data = $request->json_data;
            $content_type = $request->content_type;

            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_CARD_JSON')) {
                $html = view('image_design_page', compact('json_data'))->render();
            } else {
                $html = view('video_design_page', compact('json_data'))->render();
            }

            $response = Response::json(['code' => 200, 'message' => 'Design page previewed successfully.', 'cause' => '', 'data' => $html]);

        } catch (Exception $e) {
            (new ImageController())->logs('previewDesignPageByAdmin', $e);
            //Log::error("previewDesignPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'previewed design page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /* =====================================| get user records with some condition |===================================== */

    public function getUserRecords(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            $limit = $request->limit;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $days = $request->days;

            $total_users = DB::select('SELECT
                                 count(*) AS total
                              FROM
                                  user_master AS um LEFT JOIN role_user AS ru ON um.id=ru.user_id
                                                    LEFT JOIN user_detail AS ud ON um.id=ud.user_id
                              WHERE
                                  ru.role_id = '.Config::get('constant.ROLE_ID_FOR_FREE_USER').' AND
                                  um.is_active = 1 AND
                                  um.attribute1 IS NULL AND
                                  DATE(um.create_time) BETWEEN ? AND ? AND
                                  um.id NOT IN (SELECT DISTINCT(user_id) FROM device_master WHERE DATE(create_time) BETWEEN DATE_SUB(DATE(NOW()), INTERVAL '.$days.' DAY) AND DATE(NOW()) )
                                  ORDER BY um.create_time ASC LIMIT ?', [$start_date, $end_date, $limit]);
            $total_record = $total_users[0]->total;

            $users = DB::select('SELECT
                                  um.id,
                                  um.uuid,
                                  ud.first_name,
                                  IF(um.signup_type = 1,"Email",IF(um.signup_type = 2,"Facebook","Google")) AS signup_type,
                                  um.email_id,
                                  COALESCE(DATEDIFF(DATE(NOW()),(SELECT DATE(create_time) FROM user_session WHERE user_id = um.id ORDER BY update_time DESC LIMIT 1)),DATEDIFF(DATE(NOW()),(SELECT DATE(create_time) FROM user_master WHERE id = um.id ORDER BY create_time DESC LIMIT 1))) AS inactivity_days
                              FROM
                                  user_master AS um LEFT JOIN role_user AS ru ON um.id=ru.user_id
                                                    LEFT JOIN user_detail AS ud ON um.id=ud.user_id
                              WHERE
                                  ru.role_id = '.Config::get('constant.ROLE_ID_FOR_FREE_USER').' AND
                                  um.is_active = 1 AND
                                  um.attribute1 IS NULL AND
                                  DATE(um.create_time) BETWEEN ? AND ? AND
                                  um.id NOT IN (SELECT DISTINCT(user_id) FROM device_master WHERE DATE(create_time) BETWEEN DATE_SUB(DATE(NOW()), INTERVAL '.$days.' DAY) AND DATE(NOW()) )
                                  ORDER BY um.create_time ASC LIMIT ?', [$start_date, $end_date, $limit]);

            return ['total_users' => $total_record, 'users_detail' => $users];
        } catch (Exception $e) {
            (new ImageController())->logs('Warning Mail Command.php()', $e);
            //      Log::error("Warning Mail Command.php() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //Artisan commands (use for only debugging artisan commands)
    public function runArtisanCommands(Request $request_body)
    {
        try {

            /*$token = JWTAuth::getToken();
            JWTAuth::toUser($token);*/

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['command'], $request)) != '') {
                return $response;
            }

            $command = $request->command;
            $exitCode = Artisan::call($command);

            return $exitCode;

        } catch (Exception $e) {
            (new ImageController())->logs('runArtisanCommands', $e);
            //      Log::error("runArtisanCommands : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'run artisan command.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllFileListFromS3(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $file_path = isset($request->file_path) ? $request->file_path : '';
            $dir_name = isset($request->dir_name) ? $request->dir_name : '';
            $disk = Storage::disk('s3');

            $all_files = $disk->allFiles($file_path);
            $all_dir = $disk->directories($dir_name);

            $response = Response::json(['code' => 201, 'message' => 's3 file fetch successfully.', 'cause' => '', 'data' => ['all_files' => $all_files, 'all_dir' => $all_dir]]);

        } catch (Exception $e) {
            //(new ImageController())->logs("getAllFileListFromS3",$e);
            Log::error('getAllFileListFromS3 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'run artisan command.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function deleteFileListFromS3(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            $file_path = isset($request->file_path) ? $request->file_path : '';
            $disk = Storage::disk('s3');
            $aws_bucket = Config::get('constant.AWS_BUCKET');

            $all_files = $disk->allFiles($file_path);

            foreach ($all_files as $i => $all_file) {
                $disk->delete($all_file);
            }

            $response = Response::json(['code' => 201, 'message' => 's3 file fetch successfully.', 'cause' => '', 'data' => ['all_files' => $all_files]]);

        } catch (Exception $e) {
            //(new ImageController())->logs("deleteFileListFromS3",$e);
            Log::error('deleteFileListFromS3 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'run artisan command.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function runExecCommands(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['command', 'is_root_user'], $request)) != '') {
                return $response;
            }

            $command = $request->command;
            $is_root_user = $request->is_root_user;

            if ($is_root_user) {
                $result = RunCommandAsRoot::dispatch($command, 'runExecCommands');
            } else {
                $result = shell_exec($command);
            }

            $response = Response::json(['code' => 201, 'message' => 'command runs successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            //(new ImageController())->logs("runExecCommands", $e);
            Log::error('runExecCommands : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'run artisan command.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - Redis ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/addCacheControlInToS3Object",
     *        tags={"Admin"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="addCacheControlInToS3Object",
     *        summary="Add cache-control into s3 object By Admin",
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
     *          required={"file_name","folder_name","content_type"},
     *
     *          @SWG\Property(property="file_name",  type="string", example="60a4cdeb24dd1_user_upload_1621413355.jpg", description=""),
     *          @SWG\Property(property="folder_name",  type="string", example="original", description=""),
     *          @SWG\Property(property="content_type",  type="string", example="image/jpg", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *      @SWG\Schema(
     *
     *        @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"cache-control added successfully.","cause":"","data":{}}, description=""),
     *      ),
     *    ),
     *
     * 		@SWG\Response(
     *            response=434,
     *            description="Error",
     *
     *         @SWG\Schema(
     *
     *            @SWG\Property(property="Sample_Response", type="string", example={"code":434,"message":"Unable to add cache-control","cause":"","data":{}}, description=""),
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
     * @api {post} addCacheControlInToS3Object addCacheControlInToS3Object
     *
     * @apiName addCacheControlInToS3Objectssss
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
     * "file_name":"60a4cdeb24dd1_user_upload_1621413355.jpg",
     * "folder_name":"original",
     * "content_type":"image/jpg"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Cache-control added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addCacheControlInToS3Object(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['file_name', 'folder_name', 'content_type'], $request)) != '') {
                return $response;
            }

            $file_name = $request->file_name;
            $folder_name = $request->folder_name;
            $content_type = $request->content_type;
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');
            $is_cdn_error = '';

            $original_targetFile = $aws_bucket.'/'.$folder_name.'/'.$file_name;

            /* Working */
            /*$disk = new \Aws\S3\S3Client([
              'version'=>'latest',
              'region' => 'us-east-1',
              'credentials' => [
                'key'    => Config::get('constant.AWS_KEY'),
                'secret' => Config::get('constant.AWS_SECRET'),
              ],
            ]);

              $result = $disk->copyObject([
                'ACL' => 'public-read',
                'Bucket' => $aws_bucket, // target bucket
                'ContentType' => 'image/jpeg',
                'CacheControl' => Config::get('constant.MAX_AGE'),
                'CopySource' => urlencode($aws_bucket . '/' . $original_targetFile),
                'Key' => $original_targetFile, // target file name
                'MetadataDirective' => 'REPLACE'
              ]);
            */

            $command = $disk->getDriver()->getAdapter()->getClient()->getCommand('copyObject', [
                'ACL' => 'public-read',
                'Key' => $original_targetFile,
                'Bucket' => $aws_bucket,
                'ContentType' => $content_type,
                'CopySource' => urlencode($aws_bucket.'/'.$original_targetFile),
                'MetadataDirective' => 'REPLACE',
                'CacheControl' => Config::get('constant.MAX_AGE'),
            ]);

            try {
                $result = $disk->getDriver()->getAdapter()->getClient()->execute($command);

                if (($response = (new ImageController())->deleteCDNCache(["/$original_targetFile"])) == '') {
                    $is_cdn_error = 'Note: CDN cache not removed.';
                }

                $response = Response::json(['code' => 200, 'message' => "Cache-control added successfully. $is_cdn_error", 'cause' => '', 'data' => json_decode('{}')]);
            } catch (\Guzzle\Service\Exception\CommandTransferException $e) {
                $successful = $e->getSuccessfulCommands();
                $failed = $e->getFailedCommands();
                Log::error('addCacheControlInToS3Object : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' add cache control.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('addCacheControlInToS3Object', $e);
            //      Log::error("addCacheControlInToS3Object : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' add cache control.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllLocalFile(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['path'], $request)) != '') {
                return $response;
            }

            $path = $request->path;
            $all_files = array_diff(scandir($path), ['.', '..']);

            $response = Response::json(['code' => 200, 'message' => 'All files fetch successfully.', 'cause' => '', 'data' => $all_files]);

        } catch (Exception $e) {
            (new ImageController())->logs('getAllLocalFile', $e);
            //Log::error("getAllLocalFile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' add cache control.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function deleteLocalFileByFileName(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['path'], $request)) != '') {
                return $response;
            }

            $path = $request->path;
            unlink($path);

            $response = Response::json(['code' => 200, 'message' => 'Files deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            //(new ImageController())->logs("deleteLocalFileByFileName", $e);
            Log::error('deleteLocalFileByFileName : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' add cache control.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} updateTemplateSearchingTagsByAdmin   updateTemplateSearchingTagsByAdmin
     *
     * @apiName updateTemplateSearchingTagsByAdmin
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
     * "img_ids":1,    //compulsory
     * "search_category":10, //compulsory
     * "search_category":"tag-name",   //compulsory
     * "sub_category_id":66, //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Search category updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateTemplateSearchingTagsByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['img_ids', 'search_category'], $request)) != '') {
                return $response;
            }

            $img_ids = $request->img_ids;
            $search_category = strtolower(trim($request->search_category));

            $img_id_array = explode(',', $img_ids);
            $search_category_array = explode(',', $search_category);
            $search_category_string = implode('","', $search_category_array);

            DB::update('UPDATE content_master
                        SET
                            update_time = update_time,
                            search_category = IF(search_category != "",CONCAT(search_category, ",'.$search_category.'"), "'.$search_category.'")
                        WHERE
                            id IN ('.$img_ids.') ');

            //            DB::update('UPDATE tag_analysis_master
            //                        SET
            //                            content_count = content_count + ?
            //                        WHERE
            //                            tag IN ("'.$search_category_string.'") ',[count($img_id_array)]);

            $image_details = DB::select('SELECT id,search_category FROM content_master WHERE id IN ('.$img_ids.') ');

            foreach ($image_details as $i => $image_detail) {
                $search_tag_array = explode(',', $image_detail->search_category);
                $search_tag_count = count($search_tag_array);

                $unique_search_tag_array = array_unique($search_tag_array);
                $unique_search_tag_count = count($unique_search_tag_array);

                if ($search_tag_count != $unique_search_tag_count) {

                    DB::update('UPDATE content_master
                                    SET
                                        update_time = update_time,
                                        search_category = ?
                                    WHERE
                                        id = ? ', [implode(',', $unique_search_tag_array), $image_detail->id]);

                }
            }

            (new UserController())->deleteAllRedisKeys('getContentByCatalogIdForAdmin');
            $response = Response::json(['code' => 200, 'message' => 'Search category updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error('updateTemplateSearchingTagsByAdmin : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update search category.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function updateMultipleTemplateNameByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_details'], $request)) != '') {
                return $response;
            }

            $content_details = $request->content_details;

            DB::beginTransaction();
            foreach ($content_details as $i => $content_detail) {

                DB::update('UPDATE content_master
                        SET
                            update_time = update_time,
                            template_name = ?
                        WHERE
                            id = ?', [$content_detail->template_name, $content_detail->content_id]);
            }
            DB::commit();

            (new UserController())->deleteMultipleRedisKeys(['getStaticPageTemplateListByContentIds', 'getTemplatesByCategoryId']);
            $response = Response::json(['code' => 200, 'message' => 'Template name updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('updateMultipleTemplateNameByAdmin : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update template name.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function updateMultipleTemplateByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_id', 'content_ids'], $request)) != '') {
                return $response;
            }

            $catalog_id = $request->catalog_id;
            $content_ids = $request->content_ids;
            $query = '';

            if (isset($request->is_free)) {
                $query .= " , is_free = $request->is_free ";
            }

            if (isset($request->is_featured)) {
                $query .= " , is_featured = $request->is_featured ";
            }

            if (isset($request->is_portrait)) {
                $query .= " , is_portrait = $request->is_portrait ";
            }

            if (isset($request->is_active)) {
                $query .= " , is_active = $request->is_active ";
            }

            DB::update('UPDATE
                        content_master
                    SET
                        update_time = update_time
                        '.$query.'
                    WHERE
                        id IN ('.$content_ids.') ');

            Redis::del(array_merge(Redis::keys(Config::get('constant.REDIS_KEY').":getContentByCatalogIdForAdmin:$catalog_id*"), ['']));
            $response = Response::json(['code' => 200, 'message' => 'Multiple template updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('updateMultipleTemplateByAdmin : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update multiple template.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function updateMultipleCatalogByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['catalog_ids', 'sub_category_id'], $request)) != '') {
                return $response;
            }

            $catalog_ids = $request->catalog_ids;
            $sub_category_id = $request->sub_category_id;
            $query = '';

            if (isset($request->is_free)) {
                $query .= " , is_free = $request->is_free ";
            }

            if (isset($request->is_featured)) {
                $query .= " , is_featured = $request->is_featured ";
            }

            if (isset($request->is_active)) {
                $query .= " , is_active = $request->is_active ";
            }

            DB::update('UPDATE catalog_master
                  SET
                      update_time = update_time
                      '.$query.'
                  WHERE
                      id IN ('.$catalog_ids.') ');

            Redis::del(array_merge(Redis::keys(Config::get('constant.REDIS_KEY').":getCatalogBySubCategoryId$sub_category_id*"), ['']));
            $response = Response::json(['code' => 200, 'message' => 'Multiple catalog updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('updateMultipleCatalogByAdmin : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update multiple catalog.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function deleteMultipleTemplateByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_ids'], $request)) != '') {
                return $response;
            }

            $content_ids = str_replace(',', '|', $request->content_ids);
            $detail = DB::select('SELECT 1 FROM static_page_master WHERE content_ids REGEXP "('.$content_ids.')"');
            if (count($detail) > 0) {
                $response = Response::json(['code' => 201, 'message' => 'You are trying to delete templates which are exist in static page.', 'cause' => '', 'data' => json_decode('{}')]);

                return $response;
            }

            DB::beginTransaction();
            DB::delete('DELETE FROM content_master WHERE id IN (?)', [$content_ids]);
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Multiple templates deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            Log::error('deleteMultipleTemplateByAdmin : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete multiple templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function emailNotifications(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            $this->message_id = $request->mail->messageId;
            $this->status = $request->eventType;
            $this->subject = $request->mail->commonHeaders->subject;

            $result = DB::select('SELECT 1 FROM email_monitor_master WHERE message_id = ?', [$this->message_id]);

            DB::beginTransaction();
            if (count($result)) {
                DB::update('UPDATE email_monitor_master
                    SET
                      status = CONCAT(status ,",", ?)
                    WHERE message_id = ?', [$this->status, $this->message_id]);
                $response = Response::json(['code' => 200, 'message' => 'Notification data updated successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                DB::insert('INSERT IGNORE INTO email_monitor_master (message_id, status, subject) VALUES( ?, ?, ?)', [$this->message_id, $this->status, $this->subject]);
                $response = Response::json(['code' => 200, 'message' => 'Notification data inserted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('emailNotifications', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'manage notification data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function removeDuplicateTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $counter = 0;
            $details = DB::select('SELECT
                                cm.id AS content_id,
                                LOWER(TRIM(cm.search_category)) AS search_category
                              FROM
                                content_master AS cm
                              ORDER BY update_time DESC');

            DB::beginTransaction();
            foreach ($details as $i => $detail) {

                $remove_space_tag = preg_replace('/\s+/', ',', $detail->search_category);
                $remove_duplicate_tag = implode(',', array_unique(explode(',', $remove_space_tag)));

                if ($remove_duplicate_tag != $detail->search_category) {

                    $counter++;
                    DB::update('UPDATE
                          content_master
                      SET
                          search_category = ?,
                          update_time = update_time
                      WHERE id = ?', [$remove_duplicate_tag, $detail->content_id]);
                }
            }
            DB::commit();
            $response = Response::json(['code' => 201, 'message' => 'Duplicate tag remove successfully.', 'cause' => '', 'data' => $counter]);

        } catch (Exception $e) {
            (new ImageController())->logs('removeDuplicateTag', $e);
            //Log::error("removeDuplicateTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'duplicate tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function addCatalogIdInDesignPage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_type'], $request)) != '') {
                return $response;
            }

            $counter = 0;
            $slider_counter = 0;
            $template_counter = 0;
            $this->content_type = $request->content_type;

            $results = DB::select('SELECT
                                  id,
                                  uuid,
                                  IF(page_url != "", CONCAT("'.Config::get('constant.ACTIVATION_LINK_PATH').'", page_url), "") AS page_url,
                                  json_data,
                                  is_json,
                                  update_time
                              FROM
                                  design_page_json_master
                              WHERE
                                  content_type = ?
                              ORDER BY
                                  update_time DESC', [$this->content_type]);

            DB::beginTransaction();
            foreach ($results as $result) {
                $design_page_id = $result->id;
                $json_data = json_decode($result->json_data);
                foreach ($json_data->slider_template_section as $key) {
                    if (! isset($key->catalog_id)) {
                        $slider_template_uuid = $key->template_id;
                        $slider_catalog_uuid = DB::select('SELECT
                                                    ctm.uuid as catalog_uuid
                                                FROM
                                                    content_master as cm
                                                    LEFT JOIN catalog_master as ctm ON ctm.id = cm.catalog_id
                                                WHERE cm.uuid = ?', [$slider_template_uuid]);
                        if (count($slider_catalog_uuid) > 0) {
                            $key->catalog_id = $slider_catalog_uuid[0]->catalog_uuid;
                            $slider_counter++;
                        } else {
                            Log::info('addCatalogIdInDesignPage : catalog uuid not found', ['slider_template_id' => $slider_template_uuid, 'design_page_id' => $design_page_id]);
                        }
                    }
                }

                foreach ($json_data->template_section->data as $val) {
                    if (! isset($val->catalog_id)) {
                        $template_uuid = $val->template_id;
                        $catalog_uuid = DB::select('SELECT
                                            ctm.uuid as catalog_uuid
                                        FROM
                                            content_master as cm
                                            LEFT JOIN catalog_master as ctm ON ctm.id = cm.catalog_id
                                        WHERE cm.uuid = ?', [$template_uuid]);
                        if (count($catalog_uuid) > 0) {
                            $val->catalog_id = $catalog_uuid[0]->catalog_uuid;
                            $template_counter++;
                        } else {
                            Log::info('addCatalogIdInDesignPage : catalog uuid not found', ['template_id' => $template_uuid, 'design_page_id' => $design_page_id]);
                        }
                    }
                }

                if ($slider_counter > 0 || $template_counter > 0) {
                    DB::update('UPDATE design_page_json_master
                      SET
                        json_data = ?,
                        update_time = update_time
                      WHERE
                        id = ? ',
                        [json_encode($json_data), $design_page_id]);
                    $counter++;
                }
            }
            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Catalog id added successfully in design page.', 'cause' => '', 'data' => json_decode('{}'), 'counter' => $counter]);

        } catch (Exception $e) {
            (new ImageController())->logs('addCatalogIdInDesignPage', $e);
            //Log::error("getDesignPageChangesList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update data.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }
}
