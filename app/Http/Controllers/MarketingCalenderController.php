<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  MarketingCalenderController.php

File Created    :  Monday, 15th March 2021 05:22:26 pm
Author          :  Optimumbrew
Auther Email    :  info@optimumbrew.com
Last Modified   :  Monday, 25th March 2021 05:22:26 pm
-----
Purpose          :  This file adds events for perticuler date or schedule from admin panel.
                    & shows total events in user side .
-----
Copyright 2018 - 2021 Optimumbrew Technology

*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Config;
use DB;
use Log;
use File;
use Cache;
use Exception;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;
use Image;

class MarketingCalenderController extends Controller
{
    /*================================| Admin |================================*/

    /*/-----------| Post suggestion |-----------/*/
    /*
    Purpose : for add events from admin panel
    Description : This method compulsory take 7 argument as parameter.(template_content_ids argument is optional )
    Return : return "Post suggestion added successfully." if success otherwise error with specific status code
    */
    /**
     * @api {post} addPostSuggestionByAdmin addPostSuggestionByAdmin
     * @apiName addPostSuggestionByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * request_data:{
     * "name": "Friday",  //compulsory
     * "title": "Offer Friday",  //compulsory
     * "preview_content_id": 1,  //compulsory
     * "template_content_ids": [1,2,3],
     * "tag": "Friday",  //compulsory
     * "related_tag": "Offer,Friday,Sale,Marketting",  //compulsory
     * "short_description": "This is friday",  //compulsory
     * "long_description": "To get 50% discount in all product"  //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post suggestion added successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function addPostSuggestionByAdmin(Request $request_body)
    {

        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('name', 'title', 'preview_content_id', 'tag', 'short_description', 'long_description'), $request)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('content_ids'), $request)) != '')
                return $response;

            $template_content_ids = $request->content_ids;
            $preview_content_id = $request->preview_content_id;
            $title = trim($request->title);
            $name = preg_replace('/[^a-zA-Z0-9_-]/', "", $request->name);
            $tag = strtolower($request->tag);
            $related_tag = isset($request->related_tag) && !empty($request->related_tag) ? $request->related_tag : array();
            $short_description = $request->short_description;
            $long_description = $request->long_description;
            $create_at = date('Y-m-d H:i:s');

            $max_template_allow = Config::get('constant.DEFAULT_ITEM_COUNT_TO_POST_SUGGESTION_TEMPLATE_LIST');
            if (is_array($template_content_ids) AND count($template_content_ids) > 0 AND count($template_content_ids) <= $max_template_allow) {
                $content_ids = implode(',', $template_content_ids);
            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'The post template should not exceed the max length than ' . $max_template_allow . ' templates Or it accept only array', 'cause' => '', 'data' => json_decode('{}')));
            }

            $max_title_length = Config::get('constant.MAX_LENGTH_FOR_POST_SUGGESTION_TITLE');
            if ($max_title_length < strlen($title)) {
                return $response = Response::json(array('code' => 201, 'message' => 'The event title should not exceed the max length than ' . $max_title_length . '.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $max_name_length = Config::get('constant.MAX_LENGTH_FOR_POST_SUGGESTION_NAME');
            if ($max_name_length < strlen($name)) {
                return $response = Response::json(array('code' => 201, 'message' => 'The event name should not exceed the max length than ' . $max_name_length . '.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $is_exist = DB::select('SELECT *
                                      FROM post_suggestion_master
                                    WHERE (title = ? OR event_name=?)', [$title,$name]);
            if (count($is_exist) > 0) {
                return Response::json(array('code' => 201, 'message' => 'This post title or name already exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

            if (($response = (new VerificationController())->verifySearchCategory($tag)) != '')
                return $response;

            $search_text_tag = str_replace(",", " ", $tag);
            if (($response = (new VerificationController())->verifySearchText($search_text_tag)) != 1)
                return $response = Response::json(array('code' => 201, 'message' => 'Invalid tag name. Please enter valid tag name.', 'cause' => '', 'data' => json_decode('{}')));

            $uuid = (new ImageController())->generateUUID();
            if($uuid == ""){
                return Response::json(array('code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();
            $post_suggestion_master_data = array(
                'uuid' => $uuid,
                'event_name' => $name,
                'title' => $title,
                'preview_content_id' => $preview_content_id,
                'template_content_ids' => $content_ids,
                'tag' => $tag,
                'short_description' => $short_description,
                'long_description' => $long_description
            );

            $event_page_id = DB::table('post_suggestion_master')->insertGetId($post_suggestion_master_data);

            $this->addRelatedTags($related_tag, $event_page_id);

            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Post suggestion added successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
          (new ImageController())->logs("addPostSuggestionByAdmin",$e);
//            Log::error("addPostSuggestionByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add post suggestion by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : for update events from admin panel
    Description : This method compulsory take 8 argument as parameter.(template_content_ids argument is optional )
    Return : return "Post suggestion updated successfully." if success otherwise error with specific status code
    */
    /**
     * @api {post} updatePostSuggestionByAdmin updatePostSuggestionByAdmin
     * @apiName updatePostSuggestionByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "post_suggestion_id": 3,     //compulsory
     * "title": "Offer Friday",
     * "name": "Friday",
     * "preview_content_id": 1,
     * "template_content_ids": [1,2,3],
     * "tag": "Friday",
     * "related_tag": "Offer,Friday,Sale,Marketting",
     * "short_description": "This is friday",
     * "long_description": "To get 50% discount in all product"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post suggestion updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updatePostSuggestionByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('post_suggestion_id', 'title', 'preview_content_id', 'tag', 'short_description', 'long_description'), $request)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('content_ids'), $request)) != '')
                return $response;

            $post_suggestion_id = $request->post_suggestion_id;
            $template_content_ids = $request->content_ids;
            $preview_content_id = $request->preview_content_id;
            $title = trim($request->title);
            $name = preg_replace('/[^a-zA-Z0-9_-]/', "", $request->name);
            $tag = strtolower($request->tag);
            $related_tag = isset($request->related_tag) && !empty($request->related_tag) ? $request->related_tag : array();
            $short_description = $request->short_description;
            $long_description = $request->long_description;
            $create_at = date('Y-m-d H:i:s');

            $max_template_allow = Config::get('constant.DEFAULT_ITEM_COUNT_TO_POST_SUGGESTION_TEMPLATE_LIST');
            if (is_array($template_content_ids) AND count($template_content_ids) > 0 AND count($template_content_ids) <= $max_template_allow) {
                $content_ids = implode(',', $template_content_ids);
            } else {
                return $response = Response::json(array('code' => 201, 'message' => 'The post template should not exceed the max length than ' . $max_template_allow . ' templates Or it requires an array', 'cause' => '', 'data' => json_decode('{}')));
            }

            $max_title_length = Config::get('constant.MAX_LENGTH_FOR_POST_SUGGESTION_TITLE');
            if ($max_title_length < strlen($title)) {
                return $response = Response::json(array('code' => 201, 'message' => 'The post title should not exceed the max length than ' . $max_title_length . '.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $max_name_length = Config::get('constant.MAX_LENGTH_FOR_POST_SUGGESTION_NAME');
            if ($max_name_length < strlen($name)) {
                return $response = Response::json(array('code' => 201, 'message' => 'The event name should not exceed the max length than ' . $max_name_length . '.', 'cause' => '', 'data' => json_decode('{}')));
            }

            $is_exist = DB::select('SELECT 1
                                    FROM post_suggestion_master
                                    WHERE (title = ? OR event_name = ?) AND id != ?', [$title, $name, $post_suggestion_id ]);
            if (count($is_exist) > 0) {
                return Response::json(array('code' => 201, 'message' => 'This post title or name already exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

            if (($response = (new VerificationController())->verifySearchCategory($tag)) != '')
                return $response;

            $search_text_tag = str_replace(",", " ", $tag);
            if (($response = (new VerificationController())->verifySearchText($search_text_tag)) != 1)
                return $response = Response::json(array('code' => 201, 'message' => 'Invalid tag name. Please enter valid tag name.', 'cause' => '', 'data' => json_decode('{}')));

            DB::beginTransaction();
            DB::update('UPDATE post_suggestion_master
                        SET event_name = ?,
                            title = ?,
                            preview_content_id = ?,
                            template_content_ids = ?,
                            tag = ?,
                            short_description = ?,
                            long_description = ?
                            WHERE id = ?',
                [$name, $title, $preview_content_id, $content_ids, $tag, $short_description, $long_description, $post_suggestion_id ]);
            DB::commit();

            $this->addRelatedTags($related_tag, $post_suggestion_id);

            $response = Response::json(array('code' => 200, 'message' => 'Post suggestion updated successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
          (new ImageController())->logs("updatePostSuggestionByAdmin",$e);
//            Log::error("updatePostSuggestionByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update post suggestion by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : for delete events from admin panel
    Description : This method compulsory take 1 argument as parameter.(no argument is optional )
    Return : return "Post suggestion deleted successfully." if success otherwise error with specific status code
    */
    /**
     * @api {post} deletePostSuggestionByAdmin deletePostSuggestionByAdmin
     * @apiName deletePostSuggestionByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "post_suggestion_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post suggestion deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deletePostSuggestionByAdmin(Request $request_body)
    {

        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('post_suggestion_id'), $request)) != '')
                return $response;

            $post_suggestion_id = $request->post_suggestion_id;

            if (($response = (new VerificationController())->checkIsPostSchedulerUsed($post_suggestion_id, '')) != '')
                return $response;

            DB::beginTransaction();
            DB::delete('DELETE FROM related_tag_master WHERE event_id = ? ',[$post_suggestion_id]);
            DB::delete('DELETE FROM post_suggestion_master WHERE id = ? ',[$post_suggestion_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Post suggestion deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
          (new ImageController())->logs("deletePostSuggestionByAdmin",$e);
//            Log::error("deletePostSuggestionByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete post suggestion by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : for get events from admin panel
    Description : This method compulsory take 2 argument as parameter.(no argument is optional )
    Return : return "Post suggestion fetched successfully." if success otherwise error with specific status code
    */
    /**
     * @api {post} getPostSuggestionByAdmin getPostSuggestionByAdmin
     * @apiName getPostSuggestionByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "page":1, //compulsory
     * "item_count":2 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post suggestion fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 6,
     * "is_next_page": true,
     * "result": [
     * {
     * "post_suggestion_id": 3,
     * "title": "Offer Friday",
     * "preview_content_id": 1,
     * "template_content_ids": [1,2,3],
     * "tag": "Friday",
     * "related_tag": "Offer,Friday,Sale,Marketting",
     * "short_description": "This is friday",
     * "long_description": "To get 50% discount in all product",
     * "image": "5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "thumbnail_img": "http://192.168.0.105/photoadking/image_bucket/thumbnail/5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "compressed_img": "http://192.168.0.105/photoadking/image_bucket/compressed/5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "original_img": "http://192.168.0.105/photoadking/image_bucket/original/5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "webp_original_img": "http://192.168.0.105/photoadking/image_bucket/webp_original/5d3e7e943e706_post_suggestion_1564376724.webp",
     * "webp_thumbnail_img": "http://192.168.0.105/photoadking/image_bucket/webp_thumbnail/5d3e7e943e706_post_suggestion_1564376724.webp",
     * "is_active": 1
     * },
     * {
     * "post_suggestion_id": 3,
     * "title": "Offer Friday",
     * "preview_content_id": 1,
     * "template_content_ids": [1,2,3],
     * "tag": "Friday",
     * "related_tag": "Offer,Friday,Sale,Marketting",
     * "short_description": "This is friday",
     * "long_description": "To get 50% discount in all product",
     * "image": "5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "thumbnail_img": "http://192.168.0.105/photoadking/image_bucket/thumbnail/5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "compressed_img": "http://192.168.0.105/photoadking/image_bucket/compressed/5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "original_img": "http://192.168.0.105/photoadking/image_bucket/original/5d3e7e943e706_post_suggestion_1564376724.jpg",
     * "webp_original_img": "http://192.168.0.105/photoadking/image_bucket/webp_original/5d3e7e943e706_post_suggestion_1564376724.webp",
     * "webp_thumbnail_img": "http://192.168.0.105/photoadking/image_bucket/webp_thumbnail/5d3e7e943e706_post_suggestion_1564376724.webp",
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getPostSuggestionByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
                return $response;

            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            if (!Cache::has("Config::get('constant.REDIS_KEY'):getPostSuggestionByAdmin$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getPostSuggestionByAdmin$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) as total
                                                    FROM  post_suggestion_master
                                                    WHERE is_active = 1');
                    $total_row = $total_row_result[0]->total;

                    $result = DB::select('SELECT
                                          psm.id AS post_suggestion_id,
                                          psm.title AS ps_title,
                                          psm.event_name AS ps_name,
                                          IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                          IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                          IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                          IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_original_img,
                                          IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_thumbnail_img,
                                          IF(cm.content_file != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.content_file),"") AS content_file,
                                          COALESCE(cm.content_type,"") AS content_type,
                                          psm.preview_content_id AS content_id,
                                          psm.tag AS tag,
                                          COALESCE(psm.template_content_ids,"") AS content_ids,
                                          psm.short_description,
                                          psm.long_description,
                                          psm.is_active,
                                          psm.create_time
                                      FROM  post_suggestion_master AS psm
                                            LEFT JOIN content_master AS cm ON cm.id=psm.preview_content_id
                                        ORDER BY psm.update_time DESC
                                      LIMIT ?, ?', [$this->offset, $this->item_count]);

                    foreach ($result as $i => $tag){
                        $result[$i]->related_tag = $this->getAllRelatedTags($tag->post_suggestion_id);
                        if($tag->content_ids){
                            $result[$i]->template_list = $this->getTemplateListByContentIds($tag->content_ids);
                        }else{
                            $result[$i]->template_list = array();
                        }
                        unset($tag->content_ids);
                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    return array('total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $result);

                });
            }

            $redis_result = Cache::get("getPostSuggestionByAdmin$this->page:$this->item_count");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Post suggestion fetched successfully.', 'cause' => '', 'data' => $redis_result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
          (new ImageController())->logs("getPostSuggestionByAdmin",$e);
//            Log::error("getPostSuggestionByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched post suggestion by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*
    Purpose : Add post suggestion's rank on top
    Description : This method compulsory take 1 argument as parameter.(no argument is optional )
    Return : return "Post suggestion rank set successfully." if success otherwise error with specific status code
    */
    /**
     * @api {post} setPostSuggestionRankOnTheTopByAdmin setPostSuggestionRankOnTheTopByAdmin
     * @apiName setPostSuggestionRankOnTheTopByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "post_suggestion_id":10, //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post suggestion rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setPostSuggestionRankOnTheTopByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('post_suggestion_id'), $request)) != '')
                return $response;

            $post_suggestion_id = $request->post_suggestion_id;
            $create_at = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::update('UPDATE post_suggestion_master
                        SET update_time = ?
                            WHERE id = ? ',[$create_at, $post_suggestion_id]);
            DB::commit();


            $response = Response::json(array('code' => 200, 'message' => 'Post suggestion rank set successfully.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
          (new ImageController())->logs("setPostSuggestionRankOnTheTopByAdmin",$e);
//            Log::error("setPostSuggestionRankOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set post suggestion rank by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*/-----------| Post schedule |-----------/*/
    /*
    Purpose : Add or update post schedule's to perticuler events
    Description : This method compulsory take 2 argument as parameter.(post_schedule_id argument is optional .If we want to update schedule then compulsary)
    Return : return "Post schedule set successfully.." if success otherwise error with specific status code
    */
    /**
     * @api {post} setPostScheduleByAdmin setPostScheduleByAdmin
     * @apiName setPostScheduleByAdmin
     * @apiGroup Admin-PostSuggestionsetPostScheduleByAdmin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "post_schedule_id":3, // Optional
     * "post_date":"2019-08-01", // The date in the format "YYYY-MM-DD"
     * "post_ids":[1,2,3],
     * "description":"Test description" //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post schedule set successfully.",
     * "cause": "",
     * "data": {
     * "post_schedule_id": 61
     * }
     * }
     */
    public function setPostScheduleByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('post_date'), $request)) != '')
                return $response;

            if (($response = (new VerificationController())->validateRequiredArrayParameter(array('post_ids'), $request)) != '')
                return $response;

            $description = isset($request->description) ? $request->description : NULL;
            $post_schedule_id = isset($request->post_schedule_id) ? $request->post_schedule_id : NULL;
            $post_date = $request->post_date;
            $post_ids = $request->post_ids;
            $create_at = date('Y-m-d H:i:s');

            if (!is_array($post_ids))
                return $response = Response::json(array('code' => 201, 'message' => 'The post event id is accept only array', 'cause' => '', 'data' => json_decode('{}')));

            if(count($post_ids) !== count(array_unique($post_ids)))
                return Response::json(array('code' => 201, 'message' => 'Duplicate post are not allowed at same day.', 'cause' => '', 'data' => json_decode("{}")));

            $max_post_to_schedule = Config::get('constant.DEFAULT_ITEM_COUNT_TO_ADD_EVENT_TO_SCHEDULE');
            if(count($post_ids) <= 0 OR count($post_ids) > $max_post_to_schedule) {
                return $response = Response::json(array('code' => 201, 'message' => 'The post event should be between 1 and '.$max_post_to_schedule.' .', 'cause' => '', 'data' => json_decode('{}')));
            }

            $post_ids = implode(',', $post_ids);
            $date_arr = explode('-', $post_date);
            $month = $date_arr[1];
            $day = $date_arr[2];
            $year = $date_arr[0];
            if (!checkdate($month, $day, $year)) {
                return Response::json(array('code' => 201, 'message' => 'Invalid date format or Invalid date. Please enter the date in the format "YYYY-MM-DD".', 'cause' => '', 'data' => json_decode('{}')));
            }

            if ($post_schedule_id) {

                $is_exist = DB::select('SELECT 1
                                    FROM post_schedule_master
                                    WHERE post_date = ? AND id != ? AND is_active = 1', [$post_date,$post_schedule_id]);

            } else {

                $is_exist = DB::select('SELECT 1
                                    FROM post_schedule_master
                                    WHERE post_date = ? AND is_active = 1', [$post_date]);
            }

            if (count($is_exist) > 0) {
                return Response::json(array('code' => 201, 'message' => 'Post date already exist.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();
            if ($post_schedule_id) {

                DB::update('UPDATE post_schedule_master
                        SET description = ?,
                            post_date = ?,
                            post_ids = ?
                            WHERE id = ?', [$description,$post_date,$post_ids,$post_schedule_id]);

            } else {

                $uuid = (new ImageController())->generateUUID();
                if($uuid == ""){
                    return Response::json(array('code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode("{}")));
                }

                $schedule_data = array(
                    'uuid' => $uuid,
                    'description' => $description,
                    'post_date' => $post_date,
                    'post_ids' => $post_ids
                );
                $post_schedule_id = DB::table('post_schedule_master')->insertGetId($schedule_data);

            }
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Post schedule set successfully.', 'cause' => '', 'data' => ['post_schedule_id' => $post_schedule_id]));

        } catch (Exception $e) {
          (new ImageController())->logs("setPostScheduleByAdmin",$e);
//            Log::error("setPostScheduleByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'set post schedule by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : delete post schedule's.
    Description : This method compulsory take 1 argument as parameter.(No argument is optional .)
    Return : return "Post schedule set successfully.." if success otherwise error with specific status code
    */
    /**
     * @api {post} deletePostScheduleByAdmin deletePostScheduleByAdmin
     * @apiName deletePostScheduleByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "post_schedule_id":3
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post schedule deleted successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deletePostScheduleByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('post_schedule_id'), $request)) != '')
                return $response;

            $post_schedule_id = $request->post_schedule_id;

            DB::beginTransaction();
            DB::delete('DELETE
                            FROM post_schedule_master
                        WHERE id = ?', [$post_schedule_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Post schedule deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));

        } catch (Exception $e) {
          (new ImageController())->logs("deletePostScheduleByAdmin",$e);
//            Log::error("deletePostScheduleByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete post schedule by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    /*
    Purpose : Get monthly schedule post list with total events.
    Description : This method compulsory take 2 argument as parameter.(No argument is optional .)
    Return : return "Post scheduler fetched successfully." if success otherwise error with specific status code
    */
    /**
     * @api {post} getPostScheduleDetailByAdmin getPostScheduleDetailByAdmin
     * @apiName getPostScheduleDetailByAdmin
     * @apiGroup Admin-PostSuggestion
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "month":8,       //Get monthly schedule post list
     * "year":2019
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Post scheduler fetched successfully.",
     * "cause": "",
     * "data": {
     * "result":[
     * {
     * "post_schedule_id": 2,
     * "post_date": "2019-07-03"
     * },
     * {
     * "post_schedule_id": 1,
     * "post_date": "2019-07-30"
     * }
     * ]
     * }
     * }
     */
    public function getPostScheduleDetailByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('month', 'year'), $request)) != '')
                return $response;

            $this->month = $request->month;
            $this->year = $request->year;

            if (!Cache::has("Config::get('constant.REDIS_KEY'):getPostScheduleDetailByAdmin$this->month:$this->year")) {
                $result = Cache::rememberforever("getPostScheduleDetailByAdmin$this->month:$this->year", function () {

                    $post_schedules = DB::select('SELECT
                                          id AS post_schedule_id,
                                          post_ids,
                                          post_date,
                                          create_time
                                      FROM  post_schedule_master
                                        WHERE EXTRACT(YEAR FROM post_date) = ? AND EXTRACT(MONTH FROM post_date) = ? AND
                                              is_active = 1
                                      ORDER BY post_date ASC ', [$this->year, $this->month]);

                    foreach ($post_schedules AS $i => $post_schedule) {

                        $events = DB::select('SELECT
                                            psm.id AS post_schedule_id,
                                            cm.content_type AS content_type,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                            IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_original_img,
                                            IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_thumbnail_img,
                                            IF(cm.content_file != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.content_file),"") AS content_file,
                                            psm.title AS title,
                                            psm.event_name AS name,
                                            psm.tag AS tag,
                                            COALESCE(psm.template_content_ids,"") AS content_ids,
                                            psm.short_description AS short_description,
                                            psm.long_description AS long_description
                                          FROM post_suggestion_master AS psm
                                            LEFT JOIN content_master AS cm ON cm.id=psm.preview_content_id
                                          WHERE psm.id IN (' . $post_schedule->post_ids . ')
                                            ORDER BY FIELD(psm.id, '. $post_schedule->post_ids .')  ');

                        foreach ($events as $j => $tag){
                            $events[$j]->related_tag = $this->getAllRelatedTags($tag->post_schedule_id);
                            if($tag->content_ids){
                                $events[$j]->template_list = $this->getTemplateListByContentIds($tag->content_ids);
                            }else{
                                $events[$j]->template_list = array();
                            }
                            unset($tag->content_ids);
                        }
                        $post_schedules[$i]->events = $events;

                    }
                    return $post_schedules;

                });
            }

            $redis_result = Cache::get("getPostScheduleDetailByAdmin$this->month:$this->year");

            if (!$redis_result) {
                $redis_result = [];
            }

            $response = Response::json(array('code' => 200, 'message' => 'Post scheduler fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
            //$response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
          (new ImageController())->logs("getPostScheduleDetailByAdmin",$e);
//            Log::error("getPostScheduleDetailByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetched post scheduler by admin.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*
    Purpose : Get monthly schedule post list with total events.
    Description : This method compulsory take 2 argument as parameter.(No argument is optional .)
    Return : return "Post scheduler fetched successfully." if success otherwise error with specific status code
    */
    public function getAllTemplateBySearchTag(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('search_category','page','item_count'), $request)) != '')
                return $response;

            $this->search_category = trim(strtolower($request->search_category));
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $default_content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');
            $this->content_type = isset($request->content_type) ? $request->content_type : $default_content_type;
            $search_text = trim($this->search_category, "%");

            if (!Cache::has("Config::get('constant.REDIS_KEY'):getAllTemplateBySearchTag:$this->search_category")) {
                $result = Cache::rememberforever("getAllTemplateBySearchTag:$this->search_category", function () {

                    DB::statement("SET sql_mode = '' ");
                    /* To get templates same as dashboard search, query has been changed. */
                    /*$search_result = DB::select('SELECT
                                            IF(cm.uuid != "",CONCAT("'. Config::get('constant.ACTIVATION_LINK_PATH') .'","/app/#/",IF(cm.content_type = 4,"editor",IF(cm.content_type = 9,"video-editor","intro-editor")),"/",scm.uuid,"/",ctm.uuid,"/",cm.uuid),NULL) AS template_url,
                                            cm.id AS content_id,
                                            scm.uuid AS sub_category_id,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                            IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_original_img,
                                            IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_thumbnail_img,
                                            IF(cm.content_file != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.content_file),"") AS content_file,
                                            cm.content_type,
                                            cm.update_time
                                          FROM
                                             content_master as cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                              JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                          WHERE
                                            cm.is_active = 1 AND
                                            cm.content_type IN ('. $this->content_type .') AND
                                            isnull(cm.original_img) AND
                                            isnull(cm.display_img) AND
                                            (MATCH(cm.search_category) AGAINST("' . $this->search_category . '") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(concat("' . $this->search_category .'"," ")," ","* ")  IN BOOLEAN MODE))
                                                GROUP BY cm.id
                                          ORDER BY cm.update_time DESC ');*/
                    $search_result = DB::select('SELECT
                                                    IF(cm.uuid != "",CONCAT("'. Config::get('constant.ACTIVATION_LINK_PATH') .'","/app/#/",IF(cm.content_type = 4,"editor",IF(cm.content_type = 9,"video-editor","intro-editor")),"/",scm.uuid,"/",ctm.uuid,"/",cm.uuid),NULL) AS template_url,
                                                    cm.id AS content_id,
                                                    scm.uuid AS sub_category_id,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                                    IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                                    IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_original_img,
                                                    IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") AS webp_thumbnail_img,
                                                    IF(cm.content_file != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.content_file),"") AS content_file,
                                                    cm.content_type,
                                                    cm.update_time,
                                                    MATCH(cm.search_category) AGAINST("' . $this->search_category . '") +
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("' . $this->search_category . '"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                                  FROM
                                                    content_master AS cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id  AND ctm.is_featured = 1
                                                    JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id
                                                  WHERE
                                                    cm.is_active = 1 AND
                                                    cm.content_type IN ('. $this->content_type .') AND
                                                    ISNULL(cm.original_img) AND
                                                    ISNULL(cm.display_img) AND
                                                    (MATCH(cm.search_category) AGAINST("' . $this->search_category . '") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("' . $this->search_category .'"," ")," ","* ")  IN BOOLEAN MODE))
                                                  GROUP BY cm.id
                                                  ORDER BY search_text DESC, cm.update_time DESC');

                    $total_row = count($search_result);
                    return array('total_record' => $total_row, 'result' => $search_result);

                });
            }
            $redis_result = Cache::get("getAllTemplateBySearchTag:$this->search_category");

            $search_result = $redis_result['result'];

            $redis_result['result'] = array_slice($redis_result['result'], $this->offset, $this->item_count);
            $redis_result['is_next_page'] = ($redis_result['total_record'] > ($this->offset + $this->item_count)) ? true : false;

            if (!$redis_result) {
                $message = "Sorry, we couldn't find any templates for '$search_text'.";
                $response = array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}"));
            } else {
                if(count($search_result) > 0){
                    $code = 200;
                    $message = "Template fetch successfully.";
                }else{
                    $code = 201;
                    $message = "Sorry, we couldn't find any templates for '$search_text'.";
                }
                $response = array('code' => $code, 'message' => $message, 'cause' => '', 'data' =>$redis_result);
            }

            return $response;

        } catch (Exception $e) {
          (new ImageController())->logs("getAllTemplateBySearchTag",$e);
//            Log::error("getAllTemplateBySearchTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    /*================================| User |================================*/

    public function getPostScheduleListForPreview(Request $request_body){
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('event_name'), $request)) != '')
                return $response;

            $this->event_name = $request_body->event_name;

            if (!Cache::has("Config::get('constant.REDIS_KEY'):getPostScheduleListForPreview$this->event_name")) {
                $result = Cache::rememberforever("getPostScheduleListForPreview$this->event_name", function () {

                    $template_list = array();
                    $more_template_url = NULL;

                    $event_detail = DB::select('SELECT
                                            sug.id AS event_id,
                                            sug.title AS title,
                                            sug.tag AS tag,
                                            COALESCE(sug.template_content_ids,"") AS content_ids,
                                            sug.short_description AS short_description,
                                            sug.long_description AS long_description,
                                            sug.event_name AS event_name
                                          FROM post_suggestion_master AS sug
                                            WHERE sug.event_name = ? ',[$this->event_name]);

                    if(count($event_detail) > 0) {
                        $event_detail[0]->related_tag = $this->getAllRelatedTags($event_detail[0]->event_id,'');

                        if($event_detail[0]->content_ids != NULL) {
                            if($event_detail[0]->content_ids){
                                $template_list = $this->getTemplateListByContentIds($event_detail[0]->content_ids,'');
                            }

                            if($template_list) {
                                foreach ($template_list AS $i => $list) {
                                    $id_array[$i] = $list->more_template_id;
                                }
                                $most_relavent_values = array_count_values($id_array);
                                arsort($most_relavent_values);
                                $relavent_id = array_slice(array_keys($most_relavent_values), 0, 1, true);
                                $more_template_url = Config::get('constant.USER_CUSTOM_TEMPLATE_URL') . "/" . $relavent_id[0];
                            }

                            unset($event_detail[0]->content_ids);
                        }
                    }
                    return array('event_detail'=>$event_detail,'template_list'=> $template_list,'more_template_url'=>$more_template_url);

                });
            }

            $redis_result = Cache::get("getPostScheduleListForPreview$this->event_name");

            if (!$redis_result) {
                $code = 201;
                $message = "Sorry, we couldn't find any events.";
                $response = Response::json(array('code' => $code, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $code = 200;
                $message = "Event fetched successfully.";
                $response = Response::json(array('code' => $code, 'message' => $message, 'cause' => '', 'data' =>$redis_result));
            }

        } catch (Exception $e) {
          (new ImageController())->logs("getPostScheduleListForPreview",$e);
//            Log::error("getPostScheduleListForPreview : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get post schedule.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;

    }

    //Get all schedule date and their post suggestion detail for user-side which we had inserted from admin panel
    public function getPostScheduleList(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('event_date'), $request)) != '')
                return $response;

            $this->event_name = isset($request_body->event_name) ? $request_body->event_name : "";
            $this->event_date = $request_body->event_date ;

            //Check is date is valid or proper format if not then return with proper error msg
            $date_arr = explode('-', $request->event_date);
            if(count($date_arr) != 3){
                return Response::json(array('code' => 201, 'message' => 'Invalid date format. ', 'cause' => '', 'data' => json_decode('{}')));
            }
            $year = $date_arr[0];
            $month = $date_arr[1];
            $day = $date_arr[2];

            //check request date is "YYYY-MM-DD" format & all date numbres are numeric
            if((strlen($year) != 4) || (strlen($month) != 2) || (strlen($day) != 2) || (!is_numeric($year)) || (!is_numeric($month)) || (!is_numeric($day))){
                return Response::json(array('code' => 201, 'message' => 'Please enter the date in the format "YYYY-MM-DD". ', 'cause' => '', 'data' => json_decode('{}')));
            }

            //check date is valid or not with checkdate() php function
            if (!checkdate($month, $day, $year)) {
                return Response::json(array('code' => 201, 'message' => 'Invalid date.', 'cause' => '', 'data' => json_decode('{}')));
            }

            //Check if date verify our proper requirement (if date between -2week to +7week) if not then return with proper error msg
            $this->next_date_to_allow = date('Y-m-d', strtotime('+7 week'));
            $this->prev_date_to_allow = date('Y-m-d', strtotime('-2 week'));
            if (!($this->event_date >= $this->prev_date_to_allow && $this->event_date < $this->next_date_to_allow)) {
                return Response::json(array('code' => 201, 'message' => "Sorry, we couldn't find any events.", 'cause' => '', 'data' => json_decode("{}")));
            }

            //Check event_name is exist in our database if not then return with proper error msg
            if($this->event_name){
                $check_event_exists = DB::select('SELECT id FROM post_suggestion_master WHERE event_name=?', [$this->event_name]);
                if(count($check_event_exists) > 0) {
                    $this->event_id = $check_event_exists[0]->id;
                }else{
                    return Response::json(array('code' => 201, 'message' => "Sorry, we couldn't find any events.", 'cause' => '', 'data' => json_decode("{}")));
                }
            }

            //total days to show users from current date or re
            $this->days = Config::get('constant.NO_OF_DAYS_TO_SHOW_EVENT_TO_USER');

            if (!Cache::has("Config::get('constant.REDIS_KEY'):getPostScheduleList$this->event_date:$this->event_name")) {
                $result = Cache::rememberforever("getPostScheduleList$this->event_date:$this->event_name", function () {

                    //check only date is arrived in request that means get data for home page that is https://photoadking.com/social-media-content-calendar/
                    //else if both name & date are arrived in request that means get data for detail page that is https://photoadking.com/social-media-content-calendar/templates/?date=2021-06-01&event=world-milk-day
                    if(($this->event_date) && (!$this->event_name) ) {

                        //show full date
                        $this->date_heading = date("d", strtotime($this->event_date)) . ' To ' . date('d F, Y', strtotime('+'.$this->days.' days', strtotime($this->event_date)));

                        //Just get a schedule for 7 days
                        $post_schedules = DB::select('SELECT
                                                        uuid AS post_schedule_id,
                                                        sch.post_date,
                                                        sch.post_ids
                                                      FROM post_schedule_master AS sch
                                                        WHERE sch.post_date BETWEEN "' . $this->event_date . '" AND DATE_ADD("' . $this->event_date . '", INTERVAL ' . $this->days . ' DAY)
                                                      ORDER BY sch.post_date ASC ');

                        //get all event with this particular schedule. Means the loop runs 7 times
                        foreach ($post_schedules AS $i => $post_schedule) {
                            DB::statement("SET sql_mode = '' ");
                            $events = DB::select('SELECT
                                                sug.uuid AS event_id,
                                                cnt.content_type AS content_type,
                                                COALESCE(cnt.height,0) AS height,
                                                COALESCE(cnt.width,0) AS width,
                                                IF(cnt.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cnt.image),"") AS thumbnail_img,
                                                IF(cnt.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cnt.image),"") AS compressed_img,
                                                IF(cnt.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cnt.image),"") AS original_img,
                                                IF(cnt.webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cnt.webp_image),"") as webp_original_img,
                                                IF(cnt.webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cnt.webp_image),"") as webp_thumbnail_img,
                                                IF(cnt.content_file != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",cnt.content_file),"") AS content_file,
                                                IF(sug.preview_content_id != "",CONCAT("'. Config::get('constant.ACTIVATION_LINK_PATH') .'","/app/#/",IF(cnt.content_type = 4,"editor",IF(cnt.content_type = 9,"video-editor","intro-editor")),"/",scm.uuid,"/",cat.uuid,"/",cnt.uuid),NULL) AS template_url,
                                                sug.event_name AS event_name,
                                                sug.title AS title,
                                                sug.tag AS tag,
                                                sug.related_tag AS related_tag,
                                                sug.short_description AS short_description,
                                                sug.long_description AS long_description
                                              FROM post_suggestion_master AS sug
                                                  LEFT JOIN content_master AS cnt ON cnt.id=sug.preview_content_id
                                                  LEFT JOIN catalog_master AS cat ON cnt.catalog_id=cat.id
                                                  LEFT JOIN sub_category_catalog AS scc ON scc.catalog_id=cat.id
                                                  LEFT JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                                WHERE sug.id IN (' . $post_schedule->post_ids . ')
                                                    GROUP BY cnt.id
                                              ORDER BY FIELD(sug.id, ' . $post_schedule->post_ids . ' )  ');
                            $post_schedules[$i]->events = $events;
                            //unset this post id because this id is only used to get the event, which we used inside the for loop
                            unset($post_schedules[$i]->post_ids);
                        }

                        return array('date_heading' => $this->date_heading, 'result' => $post_schedules);
                    } else {

                        $template_list=NULL;
                        $next_schedule_date=NULL;
                        $next_event_details = array();
                        $prev_event_details = array();
                        $event_detail = array();
                        $more_template_url = NULL;

                        //show full date
                        $this->date_heading = date("d F, Y", strtotime($this->event_date));

                        //get previous date event when user click on left arraow in detail page
                        $prev_schedule = DB::select('SELECT post_date AS post_date FROM post_schedule_master WHERE post_date < "' . $this->event_date . '" AND post_date >= "' . $this->prev_date_to_allow . '" ORDER BY post_date DESC LIMIT 1');
                        if($prev_schedule){
                            $prev_schedule_date =  '"'.$prev_schedule[0]->post_date.'"';
                        }else{
                            $prev_schedule_date =  '"'.$this->event_date.'"';
                        }

                        $next_schedule = DB::select('SELECT post_date AS post_date FROM post_schedule_master WHERE post_date >= "' . $this->event_date . '" AND post_date < "' . $this->next_date_to_allow . '" ORDER BY post_date ASC LIMIT 4');
                        if($next_schedule){
                            foreach ($next_schedule AS $next_sche) {
                                $next_schedule_date .= '"'.$next_sche->post_date.'",';
                            }
                            $next_schedule_date = trim($next_schedule_date,',');
                        }else{
                            $next_schedule_date =  $this->event_date;
                        }

                        $between_schedule_date = $prev_schedule_date .','. $next_schedule_date;

                        //With the use of this select query we get total 5 days schedule. a).previous 1 day, b)current request data, c)next 3 days
                        $post_schedules = DB::select('SELECT
                                                    sch.post_date,
                                                    sch.post_ids,
                                                    DATE_FORMAT(sch.post_date, "%d %M") AS date_sub_heading
                                                  FROM post_schedule_master AS sch
                                                    WHERE sch.post_date IN ('.$between_schedule_date.')
                                                  ORDER BY sch.post_date ASC ');


                        foreach ($post_schedules AS $i => $post_schedule) {

                            //get next 3 days event when user click on right arrow in detail page or click any bottom event, when i=2,3,4
                            if($post_schedule->post_date > $this->event_date){

                                $next_event_detail = DB::select('SELECT
                                            sug.title AS title,
                                            sug.short_description AS short_description,
                                            sug.long_description AS long_description,
                                            sug.event_name AS event_name
                                          FROM post_suggestion_master AS sug
                                            WHERE sug.id IN ("'.$post_schedule->post_ids.'")  ');

                                if(count($next_event_detail) > 0) {
                                    $next_event_detail[0]->date_sub_heading = $post_schedule->date_sub_heading;
                                    $next_event_detail[0]->post_date = $post_schedule->post_date;
                                    array_push($next_event_details, $next_event_detail[0]);
                                }


                            }

                            //get previous date event when user click on left arrow in detail page, when i=0
                            if($post_schedule->post_date < $this->event_date){

                                $prev_event_detail = DB::select('SELECT
                                            sug.title AS title,
                                            sug.short_description AS short_description,
                                            sug.long_description AS long_description,
                                            sug.event_name AS event_name
                                          FROM post_suggestion_master AS sug
                                            WHERE sug.id IN ("'.$post_schedule->post_ids.'")  ');

                                if(count($prev_event_detail) > 0) {
                                    $prev_event_detail[0]->date_sub_heading = $post_schedule->date_sub_heading;
                                    $prev_event_detail[0]->post_date = $post_schedule->post_date;
                                    array_push($prev_event_details, $prev_event_detail[0]);
                                }


                            }

                            //get requested current date event & it's all detail, when i=1
                            if($post_schedule->post_date == $this->event_date) {

                                //This is main query for get detail page (event) data, we get all data from requested event name
                                $event_detail = DB::select('SELECT
                                            sug.id AS event_id,
                                            sug.title AS title,
                                            sug.tag AS tag,
                                            COALESCE(sug.template_content_ids,"") AS content_ids,
                                            sug.short_description AS short_description,
                                            sug.long_description AS long_description,
                                            sug.event_name AS event_name
                                          FROM post_suggestion_master AS sug
                                            WHERE sug.event_name = ? ',[$this->event_name]);

                                if(count($event_detail) > 0){
                                    $event_detail[0]->related_tag = $this->getAllRelatedTags($event_detail[0]->event_id,'');
                                    if($event_detail[0]->content_ids){
                                        $template_list = $this->getTemplateListByContentIds($event_detail[0]->content_ids,'');
                                    }

                                    if($template_list) {
                                        foreach ($template_list AS $i => $list) {
                                            $id_array[$i] = $list->more_template_id;
                                        }
                                        $most_relavent_values = array_count_values($id_array);
                                        arsort($most_relavent_values);
                                        $relavent_id = array_slice(array_keys($most_relavent_values), 0, 1, true);
                                        $more_template_url = Config::get('constant.USER_CUSTOM_TEMPLATE_URL') . "/" . $relavent_id[0];
                                    }

                                    unset($event_detail[0]->content_ids);
                                    unset($event_detail[0]->event_id);

                                }else{
                                    $event_detail[0]->related_tag = [];
                                }

                            }

                        }

                        return array('prev_event_details'=>$prev_event_details,'next_event_details'=>$next_event_details,'event_detail'=>$event_detail,'template_list'=> $template_list,'date_heading'=>$this->date_heading,'more_template_url'=>$more_template_url);

                    }
                });
            }

            $redis_result = Cache::get("getPostScheduleList$this->event_date:$this->event_name");

            if (!$redis_result) {
                $code = 201;
                $message = "Sorry, we couldn't find any events.";
                $response = Response::json(array('code' => $code, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $code = 200;
                $message = "Event fetched successfully.";
                $response = Response::json(array('code' => $code, 'message' => $message, 'cause' => '', 'data' =>$redis_result));
            }


        } catch (Exception $e) {
          (new ImageController())->logs("getPostScheduleList",$e);
//            Log::error("getPostScheduleList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get post schedule.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function getTemplateListByContentIds($content_ids,$column_name = 'cm.id AS content_id,'){

        DB::statement("SET sql_mode = '' ");
        $template_list = DB::select('SELECT
                                            IF(cm.uuid != "",CONCAT("'. Config::get('constant.ACTIVATION_LINK_PATH') .'","/app/#/",IF(cm.content_type = 4,"editor",IF(cm.content_type = 9,"video-editor","intro-editor")),"/",scm.uuid,"/",ctm.uuid,"/",cm.uuid),NULL) AS template_url,
                                            CONCAT(scm.uuid,"/",ctm.uuid) AS more_template_id,
                                            '. $column_name .'
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS thumbnail_img,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS compressed_img,
                                            IF(cm.image != "",CONCAT("' . Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.image),"") AS original_img,
                                            IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") as webp_original_img,
                                            IF(cm.webp_image != "",CONCAT("' . Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.webp_image),"") as webp_thumbnail_img,
                                            IF(cm.content_file != "",CONCAT("' . Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN') . '",cm.content_file),"") AS content_file,
                                            COALESCE(cm.height,0) as height,
                                            COALESCE(cm.width,0) as width,
                                            cm.content_type
                                    FROM  content_master as cm
                                          JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                          JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                    WHERE cm.catalog_id = scc.catalog_id AND
                                    cm.id IN (' . $content_ids . ')
                                        GROUP BY cm.id
                                    ORDER BY FIELD(cm.id, ' . $content_ids . ' )  ');

        return $template_list;

    }

    /*============================== Related Tags ===================================*/

    public function addRelatedTags($related_tags,$event_page_id){

        try {

            foreach ($related_tags AS $i => $related_tag) {
                $tag_name = trim(preg_replace('/[^A-Za-z\- ]/', "", $related_tag->tag_name));
                if (strlen($tag_name) > 0) {
                    $page_url = $related_tag->page_url;

                    $is_exist = DB::select('SELECT id FROM related_tag_master WHERE tag_name=? AND event_id=?', [$tag_name, $event_page_id]);
                    if (count($is_exist) <= 0) {
                        DB::beginTransaction();
                        DB::insert('INSERT INTO related_tag_master(tag_name,event_id,page_url) VALUES(?,?,?)', [$tag_name, $event_page_id, $page_url]);
                        DB::commit();
                    }
                }
            }

        } catch (Exception $e) {
          (new ImageController())->logs("addRelatedTags",$e);
//            Log::error("addRelatedTags : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
    }

    public function getAllRelatedTags($suggestion_id,$column_name = "rtm.id,"){
        $related_tags = DB::select('SELECT
                                '. $column_name .'
                                rtm.tag_name,
                                rtm.page_url AS page_url
                             FROM
                                related_tag_master AS rtm
                             WHERE
                                rtm.event_id IN (?)
                             ORDER BY FIELD(rtm.event_id, ?) DESC', [$suggestion_id,$suggestion_id]);
        return $related_tags;
    }

    /**
     * @api {post} updateRelatedTags  updateRelatedTags
     * @apiName updateRelatedTags
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * },
     * @apiSuccessExample Request-Body:
     * {
     * "id":1, //compulsory
     * "tag_name":"flyer" //compulsory
     * "event_id":41 //compulsory
     * "page_url":"http://192.168.0.105/photoadking/design/covers"
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Related tag updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateRelatedTags(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(array('id', 'tag_name','page_url','event_id'), $request)) != '')
                return $response;

            $id = $request->id;
            $tag_name = trim(strtolower($request->tag_name));
            $event_id = $request->event_id;
            $page_url = ($request->page_url !="") ? $request->page_url : NULL ;

            $tag_name = trim(preg_replace('/[^A-Za-z\- ]/', "", $tag_name));
            if(strlen($tag_name) > 50) {
                return Response::json(array('code' => 201, 'message' => 'Tag must be less than or equal 50 character.', 'cause' => '', 'data' => json_decode("{}")));
            }
            if(strlen($tag_name) == 0 ) {
                return Response::json(array('code' => 201, 'message' => 'Tag name must be alphabets.', 'cause' => '', 'data' => json_decode("{}")));
            }
            $is_exist = DB::select('SELECT id FROM related_tag_master WHERE tag_name=? AND event_id = ? AND id !=?', [$tag_name,$event_id,$id]);
            if(count($is_exist) > 0) {
                return Response::json(array('code' => 201, 'message' => 'Tag already exist for this page.', 'cause' => '', 'data' => json_decode("{}")));
            }

            DB::beginTransaction();
            DB::update('UPDATE
                      related_tag_master
                    SET
                      tag_name = ?,
                      page_url=?
                    WHERE
                      id = ? AND event_id = ? ',
                [$tag_name, $page_url, $id, $event_id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Related tag updated successfully.', 'cause' => '', 'data' => array("tag"=>$tag_name)));

        } catch (Exception $e) {
          (new ImageController())->logs("updateRelatedTags",$e);
//            Log::error("updateRelatedTags : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update similar tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteRelatedTags   deleteRelatedTags
     * @apiName deleteRelatedTags
     * @apiGroup Admin
     * @apiVersion 1.0.0
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Similar tag deleted successfully!.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteRelatedTags(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('id'), $request)) != '')
                return $response;

            $id = $request->id;

            DB::beginTransaction();
            DB::delete('DELETE FROM related_tag_master WHERE id = ? ', [$id]);
            DB::commit();

            $response = Response::json(array('code' => 200, 'message' => 'Related tag deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
        } catch (Exception $e) {
          (new ImageController())->logs("deleteRelatedTags",$e);
//            Log::error("deleteRelatedTags : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete related tag.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }
}
