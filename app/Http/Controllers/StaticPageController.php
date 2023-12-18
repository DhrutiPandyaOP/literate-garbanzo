<?php

/*
Optimumbrew Technology

Project         :  Photoadking
File            :  StaticPageController.php

File Created    :  Thursday, 18th July 2019 05:22:26 pm
Author          :  Optimumbrew
Auther Email    :  info@optimumbrew.com
Last Modified   :  Monday, 09th August 2021 04:18:26 pm
-----
Purpose          :  This file is handel all static pages from admin panel & shows total pages in user side.
-----
Copyright 2018 - 2021 Optimumbrew Technology

*/

namespace App\Http\Controllers;

use Cache;
use Config;
use DB;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redis;
use Image;
use Log;
use Response;
use Swagger\Annotations as SWG;
use Tymon\JWTAuth\Facades\JWTAuth;

class StaticPageController extends Controller
{
    /*-------------------------------------- | Static Page For User | --------------------------------------*/

    /**
     * @api {post} getStaticPageTemplateListById  getStaticPageTemplateListById
     *
     * @apiName getStaticPageTemplateListById
     *
     * @apiGroup Client-side
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "static_page_id":2,
     * "page":1 //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Template fetched successfully.",
     * "cause": "",
     * "data": {
     * "sub_category_list": [
     * {
     * "static_page_id": 1,
     * "sub_category_id": 1,
     * "sub_category_name": "flyer",
     * "is_selected": 0
     * },
     * {
     * "static_page_id": 2,
     * "sub_category_id": 2,
     * "sub_category_name": "Snapchat Geo Filter",
     * "is_selected": 1
     * },
     * {
     * "static_page_id": 3,
     * "sub_category_id": 3,
     * "sub_category_name": "Snapchat Geo Filter",
     * "is_selected": 0
     * },
     * {
     * "static_page_id": 5,
     * "sub_category_id": 4,
     * "sub_category_name": "Snapchat Geo Filter",
     * "is_selected": 0
     * }
     * ],
     * "catalog_list": [
     * {
     * "static_page_id": 1,
     * "sub_category_id": 2,
     * "catalog_id": 70,
     * "catalog_name": "Test",
     * "page_url": "Snapchat-Geo-Filter/test",
     * "is_active": 1,
     * "is_selected": 0
     * },
     * {
     * "static_page_id": 2,
     * "sub_category_id": 2,
     * "catalog_id": 1,
     * "catalog_name": "3d card",
     * "page_url": "/flyers/3d-flyer/",
     * "is_active": 1,
     * "is_selected": 1
     * },
     * {
     * "static_page_id": 3,
     * "sub_category_id": 2,
     * "catalog_id": 4,
     * "catalog_name": "Christmas1",
     * "page_url": "/flyers/3d-flyer/",
     * "is_active": 1,
     * "is_selected": 0
     * },
     * {
     * "static_page_id": 5,
     * "sub_category_id": 2,
     * "catalog_id": null,
     * "catalog_name": "All",
     * "page_url": "/flyers",
     * "is_active": 1,
     * "is_selected": 0
     * }
     * ],
     * "template_list": [
     * {
     * "content_id": 2869,
     * "sample_image": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5cfdf56e0ccd9_sample_image_1560147310.jpg",
     * "catalog_id": 1,
     * "content_type": 9,
     * "is_featured": "1",
     * "is_free": 1,
     * "is_portrait": 0,
     * "height": 480,
     * "width": 270,
     * "color_value": "#f85439",
     * "update_time": "2019-06-10 06:15:46"
     * },
     * {
     * "content_id": 2868,
     * "sample_image": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5cfb8f9e9c8d5_sample_image_1559990174.jpg",
     * "catalog_id": 1,
     * "content_type": 9,
     * "is_featured": "1",
     * "is_free": 1,
     * "is_portrait": 0,
     * "height": 480,
     * "width": 270,
     * "color_value": "#f85439",
     * "update_time": "2019-06-08 10:36:20"
     * },
     * {
     * "content_id": 2861,
     * "sample_image": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5cfb40238c0a2_sample_image_1559969827.jpg",
     * "catalog_id": 1,
     * "content_type": 9,
     * "is_featured": "1",
     * "is_free": 1,
     * "is_portrait": 0,
     * "height": 480,
     * "width": 270,
     * "color_value": "#f85439",
     * "update_time": "2019-06-08 04:57:14"
     * },
     * {
     * "content_id": 2858,
     * "sample_image": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5cfa2b4f6c27c_sample_image_1559898959.jpg",
     * "catalog_id": 1,
     * "content_type": 9,
     * "is_featured": "1",
     * "is_free": 1,
     * "is_portrait": 0,
     * "height": 480,
     * "width": 270,
     * "color_value": "#f85439",
     * "update_time": "2019-06-07 09:20:03"
     * },
     * {
     * "content_id": 2856,
     * "sample_image": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5cf8d12972185_sample_image_1559810345.jpg",
     * "catalog_id": 1,
     * "content_type": 9,
     * "is_featured": "1",
     * "is_free": 1,
     * "is_portrait": 0,
     * "height": 480,
     * "width": 270,
     * "color_value": "#f85439",
     * "update_time": "2019-06-06 08:39:05"
     * }
     * ],
     * "total_record": 16,
     * "is_next_page": true
     * }
     * }
     */
    public function getStaticPageTemplateListById(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id'], $request)) != '') {
                return $response;
            }

            $this->static_page_id = $request->static_page_id;
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->page = isset($request->page) ? $request->page : 1;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getStaticPageTemplateListById$this->page:$this->item_count:$this->static_page_id")) {
                $result = Cache::rememberforever("getStaticPageTemplateListById$this->page:$this->item_count:$this->static_page_id", function () {

                    $this->is_active = 1;

                    /*
                     * Three cases are available
                     *
                     * Case 1 : If static_page_id = 0 then get all sub_category's featured template
                     * Case 2 : Get featured templates from all catalogs by sub_category_id
                     * Case 3 : Get all template by catalog_id
                     *
                     */

                    $selected_list = DB::select('SELECT
                              sp.sub_category_id,
                              sp.catalog_id
                              FROM static_page_master AS sp
                              WHERE sp.id = ?', [$this->static_page_id]);

                    if (count($selected_list) > 0) {
                        $sub_category_id = $selected_list[0]->sub_category_id;
                        $catalog_id = $selected_list[0]->catalog_id;
                    } else {
                        $sub_category_id = 0;
                        $catalog_id = 0;
                    }
                    //Get sub_category list for static page listing
                    /*   $sub_category_lists = DB::select('SELECT
                                                           sp.uuid AS static_page_id,
                                                           scm.uuid AS sub_category_id,
                                                           spsb.sub_category_name,
                                                           CONCAT("' . $this->static_page_dir . '",spsb.sub_category_path)AS page_url,
                                                           IF(sp.sub_category_id = ? ,1,0) AS is_selected
                                                           FROM static_page_sub_category_master AS spsb
                                                           LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                                           LEFT JOIN sub_category_master AS scm ON scm.id = sp.sub_category_id
                                                           WHERE sp.catalog_id IS NULL AND sp.is_active = ?
                                                           ORDER BY sp.update_time DESC', [$sub_category_id, $this->is_active]);*/

                    //If selected subcategory catalog exists for the static page then their list
                    /* $catalog_lists = DB::select('SELECT
                                                     sp.uuid AS static_page_id,
                                                     scm.uuid as sub_category_id,
                                                     spsb.sub_category_name,
                                                     IF(sp.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                                     coalesce(sp.catalog_name,"") AS catalog_name,
                                                     spsb.sub_category_path,
                                                     coalesce(sp.catalog_path,"") AS catalog_path,
                                                     CONCAT("' . $this->static_page_dir . '",spsb.sub_category_path,"/",sp.catalog_path) as page_url,
                                                     sp.is_active,
                                                     IF(sp.uuid = ? ,1,0) AS is_selected
                                                     FROM static_page_sub_category_master AS spsb
                                                     LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                                     LEFT JOIN sub_category_master AS scm ON scm.id = sp.sub_category_id
                                                     LEFT JOIN catalog_master AS ctm ON ctm.id = sp.catalog_id
                                                     WHERE sp.sub_category_id  = ? AND sp.catalog_id IS NOT NULL AND sp.is_active = ?
                                                     ORDER BY sp.update_time DESC', [$this->static_page_id, $sub_category_id, $this->is_active]);*/

                    if ($this->static_page_id !== 0) {
                        $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
                        $total_row_result = DB::select('SELECT COUNT(*) as total FROM content_master WHERE catalog_id = ?', [$catalog_id]);

                        $total_row = $total_row_result[0]->total;

                        //Case 3 : Get all template by catalog_id
                        if ($catalog_id) {
                            $template_list = DB::select('SELECT
                                  cm.uuid as content_id,
                                  ctm.uuid AS catalog_id,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                  IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                  cm.content_type,
                                  coalesce(cm.is_featured,"") as is_featured,
                                  coalesce(cm.is_free,0) as is_free,
                                  coalesce(cm.is_portrait,0) as is_portrait,
                                  coalesce(cm.height,0) as height,
                                  coalesce(cm.width,0) as width,
                                  coalesce(cm.color_value,"") AS color_value,
                                  cm.update_time,
                                  ctm.name as catalog_name,
                                  scm.uuid as sub_category_id,
                                  scm.sub_category_name
                                FROM
                                  content_master as cm
                                  JOIN sub_category_catalog AS scc
                                    ON cm.catalog_id = scc.catalog_id
                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                  JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                WHERE
                                  cm.is_active = 1 AND
                                  cm.catalog_id = ? AND
                                  cm.content_type = ?
                                ORDER BY cm.update_time DESC limit ?, ?', [$catalog_id, $content_type, $this->offset, $this->item_count]);
                        } else {

                            //Case 2 : Get featured templates from all catalogs by sub_category_id
                            $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                    FROM content_master
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1', [$sub_category_id]);
                            $total_row = $total_row_result[0]->total;

                            $template_list = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          IF(cm.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                          cm.content_type,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          cm.update_time,
                                          ctm.name as catalog_name,
                                          scm.uuid as sub_category_id,
                                          scm.sub_category_name
                                        FROM
                                          content_master as cm
                                          JOIN sub_category_catalog AS scc
                                            ON cm.catalog_id = scc.catalog_id
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                          JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                        where
                                          cm.is_active = 1 AND
                                          cm.is_featured = 1 AND
                                          cm.catalog_id in(select catalog_id FROM sub_category_catalog WHERE sub_category_id = ?)
                                        ORDER BY cm.update_time DESC limit ?, ?', [$sub_category_id, $this->offset, $this->item_count]);

                        }

                    } else {
                        //            $sub_category_lists[0]->is_selected=1;
                        //Case 1 : If static_page_id = 0 then get all sub_category's featured template
                        $total_row_result = DB::select('SELECT COUNT(*) AS total
                                              FROM content_master
                                              WHERE catalog_id IN (SELECT catalog_id
                                                                   FROM sub_category_catalog scc
                                                                     INNER JOIN static_page_sub_category_master s ON s.sub_category_id=scc.sub_category_id) AND is_featured = 1');
                        $total_row = $total_row_result[0]->total;

                        $template_list = DB::select('SELECT
                                          cm.uuid as content_id,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") as preview_file,
                                          IF(cm.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                          ctm.name as catalog_name,
                                          scm.uuid AS sub_category_id,
                                          scm.sub_category_name,
                                          cm.content_type,
                                          coalesce(cm.is_featured,"") as is_featured,
                                          coalesce(cm.is_free,0) as is_free,
                                          coalesce(cm.is_portrait,0) as is_portrait,
                                          coalesce(cm.height,0) as height,
                                          coalesce(cm.width,0) as width,
                                          coalesce(cm.color_value,"") AS color_value,
                                          cm.update_time
                                          FROM
                                            content_master as cm
                                            JOIN sub_category_catalog AS scc
                                              ON cm.catalog_id = scc.catalog_id AND scc.catalog_id IN (SELECT DISTINCT s.catalog_id
                                                                                                       FROM static_page_master s
                                                                                                           WHERE s.sub_category_id = scc.sub_category_id AND
                                                                                                              s.catalog_id IS NOT NULL)
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                            JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                          WHERE
                                            cm.is_featured = 1 AND
                                            cm.is_active = 1 AND
                                            isnull(cm.original_img) AND
                                            isnull(cm.display_img)
                                          ORDER BY cm.update_time DESC limit ?, ?', [$this->offset, $this->item_count]);

                    }

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $ACTIVATION_LINK_PATH = Config::get('constant.ACTIVATION_LINK_PATH');

                    return ['active_path' => $ACTIVATION_LINK_PATH, 'total_record' => $total_row, 'is_next_page' => $is_next_page, 'template_list' => $template_list];

                });

            }

            $redis_result = Cache::get("getStaticPageTemplateListById$this->page:$this->item_count:$this->static_page_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Template fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getStaticPageTemplateListById', $e);
            //      Log::error("getStaticPageTemplateListById : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    /**
     * @api {post} getStaticPageTemplateListByTag  getStaticPageTemplateListByTag
     *
     * @apiName getStaticPageTemplateListByTag
     *
     * @apiGroup Client-side
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "search_category":"bussiness,education", //empty when page open first time
     * "page":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     *"code": 200,
     * "message": "Template fetched successfully.",
     * "cause": "",
     * "data": {
     *"active_path": "http://192.168.0.116/photoadking_testing",
     *"tag_list": [
     * {
     *"static_page_id": 42,
     *"tag_title": "instagram post",
     * "search_category": "flyers,Business Cards",
     *"page_url": "http://192.168.0.116/photoadking_testing/templates/instagram-video",
     *"is_selected": 0,
     *"is_active": 1
     * },
     * {
     * "static_page_id": 41,
     * "tag_title": "quote",
     *"search_category": "flyers,Quotes",
     * "page_url": "http://192.168.0.116/photoadking_testing/templates/quotes",
     *"is_selected": 1,
     * "is_active": 1
     * }
     * ],
     *"total_record": 10,
     *"is_next_page": false,
     *"template_list": [
     *{
     *"content_id": 6797,
     *"compressed_img": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5e157cefe771a_sample_image_1578466543.jpg",
     *"content_file": "http://192.168.0.116/photoadking_testing/image_bucket/video/5e157cf120bae_preview_video_1578466545.mp4",
     * "svg_file": "5e157cefe771a_sample_image_1578466543.jpg",
     *"content_type": 9,
     * "catalog_id": 380,
     *"catalog_name": "Instagram post",
     *"sub_category_id": 111,
     *"sub_category_name": "Instagram Post Video",
     *"is_featured": 1,
     * "is_free": 1,
     *"is_portrait": 1,
     *"height": 960,
     *"width": 540,
     *"color_value": "#e5fcc4",
     *"update_time": "2020-03-11 03:55:22",
     * "search_text": 9.43025016784668
     *},
     *{
     *"content_id": 6821,
     *"compressed_img": "http://192.168.0.116/photoadking_testing/image_bucket/compressed/5e33b458a136a_sample_image_1580446808.jpg",
     *"content_file": "http://192.168.0.116/photoadking_testing/image_bucket/video/5e44cec8de160_preview_video_1581567688.mp4",
     *"svg_file": "5e33b458a136a_sample_image_1580446808.jpg",
     *"content_type": 9,
     *"catalog_id": 380,
     *"catalog_name": "Instagram post",
     *"sub_category_id": 111,
     * "sub_category_name": "Instagram Post Video",
     *"is_featured": 1,
     * "is_free": 1,
     *"is_portrait": 1,
     *"height": 250,
     * "width": 750,
     *"color_value": "#9e5d47",
     *"update_time": "2020-03-09 12:06:57",
     *"search_text": 9.43025016784668
     *}
     *]
     * }
     *}
     */
    public function getStaticPageTemplateListByTag(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            //      if (($response = (new VerificationController())->validateRequiredParameter(array(''), $request)) != '')
            //        return $response;

            $this->search_category = isset($request->search_category) && trim($request->search_category) != '' ? trim(strtolower($request->search_category)) : '';
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->page = isset($request->page) ? $request->page : 1;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->content_type = isset($request->content_type) ? $request->content_type : 2;
            if ($this->content_type == 3) {
                $this->content_type = Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');
                $this->page_content_type = 3;
            } else {
                $this->content_type = Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON');
                $this->page_content_type = 2;
            }

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getStaticPageTemplateListByTag$this->page:$this->item_count:$this->search_category")) {
                $result = Cache::rememberforever("getStaticPageTemplateListByTag$this->page:$this->item_count:$this->search_category", function () {
                    $this->is_active = 1;
                    //Get tag list for static page listing

                    if ($this->search_category == '') {
                        //            $total_row_result = DB::select('SELECT COUNT(*) AS total
                        //                                              FROM content_master
                        //                                              WHERE content_type IN(9,10) AND is_featured = 1');
                        //            $total_row = $total_row_result[0]->total;

                        DB::statement("SET sql_mode = '' ");
                        $template_list = DB::select('SELECT
                                    cm.uuid AS content_id,
                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                    IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                    coalesce(cm.image,"") AS svg_file,
                                    cm.content_type,
                                    ctm.uuid as catalog_id,
                                    ctm.name as catalog_name,
                                    scm.uuid as sub_category_id,
                                    scm.sub_category_name,
                                    coalesce(cm.is_featured,0) AS is_featured,
                                    coalesce(cm.is_free,0) AS is_free,
                                    coalesce(cm.is_portrait,0) AS is_portrait,
                                    coalesce(cm.height,0) as height,
                                    coalesce(cm.width,0) as width,
                                    coalesce(cm.color_value,"") AS color_value,
                                    cm.update_time
                                  FROM
                                     content_master as cm
                                     LEFT JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                      JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                  WHERE
                                    cm.is_active = 1 AND
                                    cm.content_type IN(9,10) AND
                                    isnull(cm.original_img) AND
                                    isnull(cm.display_img)
                                  GROUP by cm.id
                                    ORDER BY cm.update_time DESC LIMIT 20');

                    } else {
                        $page_tag_lists = DB::select('SELECT
                              sp.content_ids,
                              sp.id AS static_page_id
                              FROM  static_page_master AS sp
                              WHERE sp.content_type =? AND
                              sp.is_active = ? AND
                              sp.search_category = ?
                              ORDER BY sp.update_time DESC LIMIT ?', [$this->page_content_type, $this->is_active, $this->search_category, 1]);

                        if (count($page_tag_lists) > 0) {
                            $this->static_page_id = $page_tag_lists[0]->static_page_id;
                            $this->content_ids = $page_tag_lists[0]->content_ids;

                            if ($this->content_ids == null) {
                                $template_list = DB::select('SELECT
                          cm.id AS content_id
                          FROM
                          content_master as cm
                          WHERE
                          cm.is_active = ? AND
                          cm.content_type = ? AND
                          isnull(cm.original_img) AND
                          isnull(cm.display_img) AND
                          (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                          MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                          ORDER BY cm.update_time DESC LIMIT ?', [1, $this->content_type, $this->item_count]);

                                $content_id_array = [];
                                foreach ($template_list as $id) {
                                    array_push($content_id_array, $id->content_id);
                                }
                                $this->content_ids = implode(',', $content_id_array);

                                DB::beginTransaction();
                                DB::update('UPDATE static_page_master SET content_ids = ? WHERE id = ?', [$this->content_ids, $this->static_page_id]);
                                DB::commit();

                            }
                            if ($this->content_ids !== null) {
                                //                $content_id = explode(',',$this->content_ids);
                                //                $total_row = count($content_id);

                                DB::statement("SET sql_mode = '' ");
                                $template_list = DB::select('SELECT
                                                cm.uuid AS content_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") as sample_image,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                                IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                coalesce(cm.image,"") AS svg_file,
                                                cm.content_type,
                                                ctm.uuid as catalog_id,
                                                ctm.name as catalog_name,
                                                scm.uuid as sub_category_id,
                                                scm.sub_category_name,
                                                coalesce(cm.is_featured,0) AS is_featured,
                                                coalesce(cm.is_free,0) AS is_free,
                                                coalesce(cm.is_portrait,0) AS is_portrait,
                                                coalesce(cm.height,0) as height,
                                                coalesce(cm.width,0) as width,
                                                coalesce(cm.color_value,"") AS color_value,
                                                cm.update_time
                                             FROM content_master AS cm
                                                JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                JOIN sub_category_master as scm ON scc.sub_category_id=scm.id
                                             WHERE
                                                cm.is_active = 1 AND
                                                cm.content_type = ? AND
                                                isnull(cm.original_img) AND
                                                isnull(cm.display_img) AND
                                                cm.id IN ('.$this->content_ids.')
                                             GROUP by cm.id
                                                ORDER BY FIELD(cm.id, '.$this->content_ids.' )  ', [$this->content_type]);
                            }
                        } else {
                            Log::info('search_category does not exist : ', [$this->search_category]);
                            $template_list = json_decode('{}');
                        }
                    }
                    //          $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                    $ACTIVATION_LINK_PATH = Config::get('constant.ACTIVATION_LINK_PATH');

                    //          return array('active_path' => $ACTIVATION_LINK_PATH,'total_record' => $total_row, 'is_next_page' => $is_next_page, 'template_list' => $template_list);
                    return ['active_path' => $ACTIVATION_LINK_PATH, 'template_list' => $template_list];
                });
            }
            $this->search_category = isset($request->search_category) && trim($request->search_category) != '' ? trim(strtolower($request->search_category)) : '';
            $redis_result = Cache::get("getStaticPageTemplateListByTag$this->page:$this->item_count:$this->search_category");
            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Template fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getStaticPageTemplateListByTag', $e);
            //      Log::error("getStaticPageTemplateListByTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    /*
    Purpose : for searching in static page
    Description : This method compulsory take 4 argument as parameter.(if any argument is optional then define it here)
    Return : return code, message, total_record & result if success otherwise error with specific status code
    */
    public function searchStaticPageTemplateBySubCategoryId(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'page', 'item_count', 'content_type'], $request)) != '') {
                return $response;
            }

            $this->default_sub_category_id = $this->sub_category_id = $request->sub_category_id;
            //Remove '[\@()<>+*%"~-]' character from searching because if we add this character then mysql gives syntax error.
            $this->db_search_category = $this->search_category = isset($request->search_category) ? trim(mb_substr(preg_replace('/[\\\@()<>+*%"~-]/', '', mb_strtolower($request->search_category)), 0, 100)) : '';
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type_for_cache = $this->content_type = isset($request->content_type) ? $request->content_type : 0;
            $this->is_free_for_cache = $this->is_free = isset($request->is_free) ? $request->is_free : null;
            $this->is_portrait_for_cache = $this->is_portrait = isset($request->is_portrait) ? $request->is_portrait : null;
            $this->default_table_condition = $this->default_where_condition = $this->order_by_clause = $this->default_order_by_clause = null;
            $this->success_code = 200;
            $this->success_message = 'Templates fetched successfully.';
            $this->default_code = 433;
            $this->default_message = "Sorry, we couldn't find any templates for '$this->db_search_category', but we found some other templates you might like:";
            $this->is_search_category_changed = 0;
            $this->is_success = 0;

            if ($this->page <= 5) {
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

                    $this->default_table_condition = ' AND FIND_IN_SET(scc.sub_category_id,"'.$this->default_sub_category_id.'")';

                    $this->default_where_condition = ' AND cm.is_featured = 1 '.$this->content_type;

                    //when user search from specific sub_category and search tag is NOT NULL
                } elseif ($this->sub_category_id !== 0 && $this->search_category != '') {

                    $this->order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, ';

                    //below variables will use if data not found in specific sub_category for search tag
                    $this->default_where_condition = $this->content_type;

                    $this->default_order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, FIELD(cm.is_featured, 1) DESC, ';

                    //when user search from all sub_category and search tag is NOT NULL
                } elseif ($this->sub_category_id === 0 && $this->search_category != '') {

                    //get comma separated list of featured sub_categories
                    $this->default_sub_category_id = Config::get('constant.DEFAULT_SUB_CATEGORY_ID_TO_GET_FEATURED_TEMPLATES');

                    //below variable will use if data not found in all sub_category for search tag
                    $this->default_table_condition = ' AND FIND_IN_SET(scc.sub_category_id,"'.$this->default_sub_category_id.'")';

                    //below variable will use if data not found in all sub_category for search tag
                    $this->default_where_condition = ' AND cm.is_featured = 1 '.$this->content_type;

                    //when user search from specific sub_category and search tag is NOT NULL
                } elseif ($this->sub_category_id !== 0 && $this->search_category == '') {

                    $this->default_order_by_clause = ' FIELD(scm.uuid, "'.$this->sub_category_id.'") DESC, FIELD(cm.is_featured, 1) DESC, ';

                    $this->default_where_condition = $this->content_type;
                }

                if ($this->search_category != '') {
                    /*
                      * create redis keys to manage 24 hour caching when user search from specific sub_category or all sub_category
                      * this key is deleted only when any changes are made in database related to template
                     */
                    $redis_result = Cache::rememberforever("searchStaticPageTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache", function () {

                        $total_row_result = DB::select('SELECT
                                                COUNT(DISTINCT cm.id) AS total
                                            FROM
                                                content_master AS cm
                                                JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                                sub_category_master AS scm
                                            WHERE
                                                scc.sub_category_id = scm.id AND
                                                cm.is_active = 1 AND
                                                ISNULL(cm.original_img) AND
                                                ISNULL(cm.display_img) AND
                                                (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                                '.$this->content_type.'
                                                '.$this->is_free.'
                                                '.$this->is_portrait.'
                                            ');
                        $total_row = $total_row_result[0]->total;

                        if ($total_row) {
                            DB::statement("SET sql_mode = '' ");
                            $search_result = DB::select('SELECT
                                              cm.uuid AS content_id,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail_img,
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
                                              cm.update_time,
                                              MATCH(cm.search_category) AGAINST("'.$this->search_category.'") +
                                              MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE) AS search_text
                                           FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1,
                                              sub_category_master AS scm
                                           WHERE
                                              scc.sub_category_id = scm.id AND
                                              cm.is_active = 1 AND
                                              ISNULL(cm.original_img) AND
                                              ISNULL(cm.display_img) AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE))
                                              '.$this->content_type.'
                                              '.$this->is_free.'
                                              '.$this->is_portrait.'
                                           GROUP BY content_id
                                           ORDER BY '.$this->order_by_clause.' search_text DESC, cm.update_time DESC LIMIT ?, ?', [$this->offset, $this->item_count]);

                            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                            return ['code' => $this->success_code, 'message' => $this->success_message, 'result' => $search_result];

                        } else {

                            $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                            $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => []];

                            return ['code' => $this->default_code, 'message' => $this->default_message, 'result' => $search_result];
                        }

                    });

                    //if result not found then check if search tag is in other language and translate it then run same query for translated tag.
                    if (! $redis_result['result']['total_record'] && ! $this->is_search_category_changed) {
                        $this->is_search_category_changed = 1;
                        $translate_data = (new UserController())->translateLanguage($this->search_category, 'en');

                        if (isset($translate_data['data']['text']) && $translate_data['data']['text'] && $translate_data['data']['text'] !== $this->search_category) {
                            $this->search_category = trim($translate_data['data']['text']);
                            //delete old cache key.
                            Redis::del(Config::get('constant.REDIS_KEY').":searchStaticPageTemplateBySubCategoryId:$this->sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache");
                            goto run_same_query;
                        }
                    }

                    if ($redis_result['code'] == 200) {
                        $this->is_success = 1;
                    } else {
                        $this->is_success = 2;
                    }
                }

                //if result found successfully then no need for default result.
                if ($this->is_success != 1) {
                    //caching time of redis key to get default featured templates
                    $this->time_of_expired_redis_key = Config::get('constant.EXPIRATION_TIME_OF_REDIS_KEY_TO_GET_FEATURED_TEMPLATES');

                    /*
                     * If no data found of search category in all sub category or specific sub_category
                     * If user searched from specific sub_category then we provide following output from all sub_category(on priority of specific searched sub_category)
                     * If user searched from all sub_category then we provide following output from default featured sub_categories(37,41,39,44)
                     * output = featured templates + normal templates (order by featured & update_time desc)
                    */
                    $redis_result = Cache::remember("defaultStaticPageFeaturedTemplates$this->default_sub_category_id:$this->search_category:$this->content_type_for_cache:$this->page:$this->item_count:$this->is_free_for_cache:$this->is_portrait_for_cache", $this->time_of_expired_redis_key, function () {
                        DB::statement("SET sql_mode = '' ");
                        $total_row_result = DB::select('SELECT
                                              COUNT(DISTINCT cm.id) AS total
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

                        $total_row = $total_row_result[0]->total;

                        $search_result = DB::select('SELECT
                                            cm.uuid AS content_id,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                            IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                            IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail_img,
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

                        foreach ($search_result as $i => $search_detail) {
                            $search_detail->pages_sequence = explode(',', $search_detail->pages_sequence);
                        }

                        $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                        $search_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $search_result];

                        return ['code' => $this->default_code, 'message' => $this->default_message, 'result' => $search_result];

                    });
                    //when search tag is NULL then give success message.
                    if ($this->search_category == '') {
                        $redis_result['code'] = $this->success_code;
                        $redis_result['message'] = $this->success_message;
                    }
                }
            } else {
                $redis_result['result']['total_record'] = 0;
            }

            if (! $redis_result['result']['total_record']) {
                $redis_result = [];
                $response = Response::json(['code' => $this->default_code, 'message' => "Sorry, we couldn't find any templates for '$this->db_search_category'", 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                if ($this->search_category != '') {
                    if ($this->page == 1) {
                        if ($redis_result['code'] == 200) {
                            //when data come from cache and status 200 means is success tag  so increment success count
                            //SaveSearchTagJob::dispatch($redis_result['result']['total_record'],$this->search_category,4,$this->sub_category_id,'',$this->content_type_for_cache);

                        } else {
                            //when data come from cache and status not 200 means is fail tag so increment fail count
                            //SaveSearchTagJob::dispatch(0,$this->search_category,4,$this->sub_category_id,'',$this->content_type_for_cache);
                        }
                    }
                }
                $response = Response::json(['code' => $redis_result['code'], 'message' => $redis_result['message'], 'cause' => '', 'data' => $redis_result['result']]);
            }

            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            Log::error('searchStaticPageTemplateBySubCategoryId : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'search templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*-------------------------------------- | Static Page For Admin | --------------------------------------*/

    public function getTemplatesByCategoryId(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->catalog_id = isset($request->catalog_id) ? $request->catalog_id : null;
            $this->content_id = isset($request->content_id) ? $request->content_id : null;
            $this->content_type = isset($request->content_type) ? $request->content_type : Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::remember("getTemplatesByCategoryId:$this->sub_category_id:$this->catalog_id:$this->content_id:$this->content_type:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_7_DAYS'), function () {

                if ($this->content_id) {

                    $total_row = 1;
                    DB::statement("SET sql_mode = '' ");
                    $template_list = DB::select('SELECT
                                  cm.id AS content_id,
                                  cm.uuid AS content_uuid,
                                  ctm.uuid AS catalog_id,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                  IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                  IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                  cm.content_type,
                                  cm.template_name,
                                  COALESCE(cm.is_featured,"") AS is_featured,
                                  COALESCE(cm.is_free,0) AS is_free,
                                  COALESCE(cm.is_portrait,0) AS is_portrait,
                                  COALESCE(cm.height,0) AS height,
                                  COALESCE(cm.width,0) AS width,
                                  COALESCE(cm.color_value,"") AS color_value,
                                  cm.update_time,
                                  REPLACE(ctm.name,"\'","") AS catalog_name,
                                  scm.uuid AS sub_category_id,
                                  REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                FROM
                                  content_master AS cm
                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                  JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                WHERE
                                  cm.is_active = 1 AND
                                  ctm.is_featured = 1 AND
                                  cm.uuid = ? AND
                                  cm.content_type = ?
                                GROUP BY cm.id
                                  ORDER BY cm.update_time', [$this->content_id, $this->content_type]);

                } elseif ($this->catalog_id) {

                    $catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid=?', [$this->catalog_id]);
                    $this->catalog_id = $catalog_id[0]->id;

                    $total_row_result = DB::select('SELECT
                                                        COUNT(DISTINCT cm.id) AS total
                                                    FROM
                                                        content_master AS cm
                                                        JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                        JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                                    WHERE
                                                        ctm.is_featured = 1 AND
                                                        cm.catalog_id = ?', [$this->catalog_id]);
                    $total_row = $total_row_result[0]->total;

                    DB::statement("SET sql_mode = '' ");
                    $template_list = DB::select('SELECT
                                  cm.id AS content_id,
                                  cm.uuid AS content_uuid,
                                  ctm.uuid AS catalog_id,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                  IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                  IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                  cm.content_type,
                                  cm.template_name,
                                  COALESCE(cm.is_featured,"") AS is_featured,
                                  COALESCE(cm.is_free,0) AS is_free,
                                  COALESCE(cm.is_portrait,0) AS is_portrait,
                                  COALESCE(cm.height,0) AS height,
                                  COALESCE(cm.width,0) AS width,
                                  COALESCE(cm.color_value,"") AS color_value,
                                  cm.update_time,
                                  REPLACE(ctm.name,"\'","") AS catalog_name,
                                  scm.uuid AS sub_category_id,
                                  REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                FROM
                                  content_master AS cm
                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                  JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                WHERE
                                  cm.is_active = 1 AND
                                  ctm.is_featured = 1 AND
                                  cm.catalog_id = ? AND
                                  cm.content_type = ?
                                GROUP BY cm.id
                                  ORDER BY cm.update_time DESC limit ?, ?', [$this->catalog_id, $this->content_type, $this->offset, $this->item_count]);

                } else {

                    $sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid=?', [$this->sub_category_id]);
                    $this->sub_category_id = $sub_category_id[0]->id;

                    //Get featured templates from all catalogs by sub_category_id
                    $total_row_result = DB::select('SELECT COUNT(DISTINCT id) AS total
                                                    FROM content_master
                                                    WHERE catalog_id IN (SELECT catalog_id
                                                                     FROM sub_category_catalog
                                                                     WHERE sub_category_id = ?) AND is_featured = 1', [$this->sub_category_id]);
                    $total_row = $total_row_result[0]->total;

                    DB::statement("SET sql_mode = '' ");
                    $template_list = DB::select('SELECT
                                          cm.id AS content_id,
                                          cm.uuid AS content_uuid,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                          IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                          IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                          IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS preview_file,
                                          IF(cm.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                          cm.content_type,
                                          cm.template_name,
                                          COALESCE(cm.is_featured,"") AS is_featured,
                                          COALESCE(cm.is_free,0) AS is_free,
                                          COALESCE(cm.is_portrait,0) AS is_portrait,
                                          COALESCE(cm.height,0) AS height,
                                          COALESCE(cm.width,0) AS width,
                                          COALESCE(cm.color_value,"") AS color_value,
                                          cm.update_time,
                                          REPLACE(ctm.name,"\'","") AS catalog_name,
                                          scm.uuid AS sub_category_id,
                                          REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                        FROM
                                          content_master AS cm
                                          JOIN sub_category_catalog AS scc
                                            ON cm.catalog_id = scc.catalog_id
                                          JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                          JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                        WHERE
                                          cm.is_active = 1 AND
                                          cm.is_featured = 1 AND
                                          cm.catalog_id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = ?)
                                        GROUP BY cm.id
                                          ORDER BY cm.update_time DESC limit ?, ?', [$this->sub_category_id, $this->offset, $this->item_count]);

                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];

                return $result;
            });

            if (! $redis_result['result']) {
                $response = Response::json(['code' => 201, 'message' => 'Template does not exist in this category.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'All sub category are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplatesByCategoryId', $e);
            //Log::error("getTemplatesByCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /*
    Purpose : for get templates to set in static page
    Description : This method compulsory take 3 argument as parameter.(if any argument is optional then define it here)
    Return : return code, message, total_record & result if success otherwise error with specific status code
    */
    public function getTemplatesByCategoryIdV2BackUp(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'content_type'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_uuids'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
            $this->search_category = isset($request->search_category) ? $request->search_category : null;
            $this->catalog_id = isset($request->catalog_id) ? $request->catalog_id : null;
            $this->content_type = $request->content_type;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_uuids = $request->content_uuids;

            $redis_result = Cache::remember("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->search_category:$this->content_type:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                $this->content_uuids = '"'.implode('","', $this->content_uuids).'"';

                if ($this->page <= 5) {
                    //Get all template by catalog_id
                    if ($this->catalog_id) {

                        $catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid=?', [$this->catalog_id]);
                        $this->catalog_id = $catalog_id[0]->id;

                        $total_row_result = DB::select('SELECT
                                                  COUNT(DISTINCT cm.id) AS total
                                              FROM
                                                  content_master AS cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                  JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                              WHERE
                                                  cm.catalog_id = ? AND
                                                  cm.is_active = 1 AND
                                                  ctm.is_featured = 1 AND
                                                  cm.content_type = ? AND
                                                  cm.uuid NOT IN ('.$this->content_uuids.')', [$this->catalog_id, $this->content_type]);
                        $total_row = $total_row_result[0]->total;

                        DB::statement("SET sql_mode = '' ");
                        $template_list = DB::select('SELECT
                                                cm.uuid AS content_id,
                                                ctm.uuid AS catalog_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                cm.content_type,
                                                cm.template_name,
                                                COALESCE(cm.is_featured,"") AS is_featured,
                                                COALESCE(cm.is_free,0) AS is_free,
                                                COALESCE(cm.is_portrait,0) AS is_portrait,
                                                COALESCE(cm.height,0) AS height,
                                                COALESCE(cm.width,0) AS width,
                                                COALESCE(cm.color_value,"") AS color_value,
                                                cm.update_time,
                                                REPLACE(ctm.name,"\'","") AS catalog_name,
                                                scm.uuid AS sub_category_id,
                                                REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                              FROM
                                                content_master AS cm
                                                JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                              WHERE
                                                cm.is_active = 1 AND
                                                ctm.is_featured = 1 AND
                                                cm.catalog_id = ? AND
                                                cm.content_type = ? AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                              GROUP BY cm.id
                                              ORDER BY cm.update_time DESC limit ?, ?', [$this->catalog_id, $this->content_type, $this->offset, $this->item_count]);

                    } elseif ($this->sub_category_id) {

                        //Get all template by sub_category_id
                        $sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid=?', [$this->sub_category_id]);
                        $this->sub_category_id = $sub_category_id[0]->id;

                        //Get featured templates from all catalogs by sub_category_id
                        $total_row_result = DB::select('SELECT COUNT(DISTINCT id) AS total
                                              FROM content_master
                                              WHERE is_active = 1 AND
                                                    content_type = ? AND
                                                    uuid NOT IN ('.$this->content_uuids.') AND
                                                    catalog_id IN (SELECT catalog_id
                                                                   FROM sub_category_catalog
                                                                   WHERE sub_category_id = ?) AND is_featured = 1', [$this->content_type, $this->sub_category_id]);
                        $total_row = $total_row_result[0]->total;

                        DB::statement("SET sql_mode = '' ");
                        $template_list = DB::select('SELECT
                                                cm.uuid AS content_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                IF(cm.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                                cm.content_type,
                                                cm.template_name,
                                                COALESCE(cm.is_featured,"") AS is_featured,
                                                COALESCE(cm.is_free,0) AS is_free,
                                                COALESCE(cm.is_portrait,0) AS is_portrait,
                                                COALESCE(cm.height,0) AS height,
                                                COALESCE(cm.width,0) AS width,
                                                COALESCE(cm.color_value,"") AS color_value,
                                                cm.update_time,
                                                REPLACE(ctm.name,"\'","") AS catalog_name,
                                                scm.uuid AS sub_category_id,
                                                REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                              FROM
                                                content_master AS cm
                                                JOIN sub_category_catalog AS scc
                                                  ON cm.catalog_id = scc.catalog_id
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                              WHERE
                                                cm.is_active = 1 AND
                                                cm.is_featured = 1 AND
                                                cm.content_type = ? AND
                                                cm.catalog_id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = ?) AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                              GROUP BY cm.id
                                              ORDER BY cm.update_time DESC limit ?, ?', [$this->content_type, $this->sub_category_id, $this->offset, $this->item_count]);

                    } elseif ($this->search_category) {

                        $total_row_result = DB::select('SELECT cm.id
                                              FROM content_master AS cm
                                              WHERE
                                                cm.is_active = 1 AND
                                                cm.content_type = '.$this->content_type.' AND
                                                ISNULL(cm.original_img) AND
                                                ISNULL(cm.display_img) AND
                                                (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                  MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                               GROUP BY cm.id');
                        $total_row = count($total_row_result);

                        DB::statement("SET sql_mode = '' ");
                        $template_list = DB::select('SELECT
                                                cm.uuid AS content_id,
                                                ctm.uuid AS catalog_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") as webp_thumbnail,
                                                IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                scm.uuid AS sub_category_id,
                                                ctm.name AS catalog_name,
                                                scm.sub_category_name,
                                                cm.content_type,
                                                cm.template_name,
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
                                                cm.content_type = ? AND
                                                ISNULL(cm.original_img) AND
                                                ISNULL(cm.display_img) AND
                                                (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                            GROUP BY cm.id
                                            ORDER BY cm.update_time DESC LIMIT ?,?', [$this->content_type, $this->offset, $this->item_count]);

                    } else {

                        $total_row_result = DB::select('SELECT
                                                  COUNT(DISTINCT cm.id) AS total
                                              FROM
                                                  content_master AS cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                  JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                              WHERE
                                                  cm.is_active = 1 AND
                                                  ctm.is_featured = 1 AND
                                                  cm.content_type = ? AND
                                                  cm.uuid NOT IN ('.$this->content_uuids.')', [$this->content_type]);
                        $total_row = $total_row_result[0]->total;

                        DB::statement("SET sql_mode = '' ");
                        $template_list = DB::select('SELECT
                                                cm.uuid AS content_id,
                                                ctm.uuid AS catalog_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                cm.content_type,
                                                cm.template_name,
                                                COALESCE(cm.is_featured,"") AS is_featured,
                                                COALESCE(cm.is_free,0) AS is_free,
                                                COALESCE(cm.is_portrait,0) AS is_portrait,
                                                COALESCE(cm.height,0) AS height,
                                                COALESCE(cm.width,0) AS width,
                                                COALESCE(cm.color_value,"") AS color_value,
                                                cm.update_time,
                                                REPLACE(ctm.name,"\'","") AS catalog_name,
                                                scm.uuid AS sub_category_id,
                                                REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                              FROM
                                                content_master AS cm
                                                JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                              WHERE
                                                cm.is_active = 1 AND
                                                ctm.is_featured = 1 AND
                                                cm.content_type = ? AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                              GROUP BY cm.id
                                              ORDER BY cm.update_time DESC limit ?, ?', [$this->content_type, $this->offset, $this->item_count]);
                    }
                } else {
                    $total_row = 0;
                    $template_list = [];
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];

                return $result;
            });

            if (! $redis_result['result']) {
                $response = Response::json(['code' => 201, 'message' => 'Template does not exist in this category.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'All sub category are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplatesByCategoryId', $e);
            //Log::error("getTemplatesByCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getTemplatesByCategoryIdV2BackUp2(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'content_type'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_uuids'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : null;
            $this->search_category = isset($request->search_category) ? $request->search_category : null;
            $this->catalog_id = isset($request->catalog_id) ? $request->catalog_id : null;
            $this->content_type = $request->content_type;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_uuids = $request->content_uuids;

            $redis_result = Cache::remember("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->search_category:$this->content_type:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                $this->content_uuids = '"'.implode('","', $this->content_uuids).'"';

                if ($this->page <= 5) {
                    //Get all template by catalog_id
                    if ($this->catalog_id) {

                        $catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid=?', [$this->catalog_id]);
                        $this->catalog_id = $catalog_id[0]->id;

                        $total_row = Cache::remember("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->search_category:$this->content_type", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                            $total_row_result = DB::select('SELECT
                                                    COUNT(DISTINCT cm.id) AS total
                                                FROM
                                                    content_master AS cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                    JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                                WHERE
                                                    cm.catalog_id = ? AND
                                                    cm.is_active = 1 AND
                                                    ctm.is_featured = 1 AND
                                                    cm.content_type = ? AND
                                                    cm.uuid NOT IN ('.$this->content_uuids.')', [$this->catalog_id, $this->content_type]);

                            return $total_row_result[0]->total;
                        });

                        $template_list = DB::select('SELECT
                                              DISTINCT cm.uuid AS content_id,
                                              ctm.uuid AS catalog_id,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                              cm.content_type,
                                              cm.template_name,
                                              COALESCE(cm.is_featured,"") AS is_featured,
                                              COALESCE(cm.is_free,0) AS is_free,
                                              COALESCE(cm.is_portrait,0) AS is_portrait,
                                              COALESCE(cm.height,0) AS height,
                                              COALESCE(cm.width,0) AS width,
                                              COALESCE(cm.color_value,"") AS color_value,
                                              cm.update_time,
                                              REPLACE(ctm.name,"\'","") AS catalog_name,
                                              scm.uuid AS sub_category_id,
                                              REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                              cm.is_active = 1 AND
                                              ctm.is_featured = 1 AND
                                              cm.catalog_id = ? AND
                                              cm.content_type = ? AND
                                              cm.uuid NOT IN ('.$this->content_uuids.')
                                            ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->content_type, $this->offset, $this->item_count]);

                    } elseif ($this->sub_category_id) {

                        //Get all template by sub_category_id
                        $sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid=?', [$this->sub_category_id]);
                        $this->sub_category_id = $sub_category_id[0]->id;

                        $total_row = Cache::remember("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->search_category:$this->content_type", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                            //Get featured templates from all catalogs by sub_category_id
                            $total_row_result = DB::select('SELECT
                                                    COUNT(DISTINCT cm.id) AS total
                                                FROM
                                                    content_master AS cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1 AND scc.sub_category_id = ?
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                    JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                                WHERE
                                                    cm.is_active = 1 AND
                                                    ctm.is_featured = 1 AND
                                                    cm.content_type = ? AND
                                                    cm.uuid NOT IN ('.$this->content_uuids.') ', [$this->sub_category_id, $this->content_type]);

                            return $total_row_result[0]->total;
                        });

                        $template_list = DB::select('SELECT
                                              DISTINCT cm.uuid AS content_id,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                              IF(cm.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                              cm.content_type,
                                              cm.template_name,
                                              COALESCE(cm.is_featured,"") AS is_featured,
                                              COALESCE(cm.is_free,0) AS is_free,
                                              COALESCE(cm.is_portrait,0) AS is_portrait,
                                              COALESCE(cm.height,0) AS height,
                                              COALESCE(cm.width,0) AS width,
                                              COALESCE(cm.color_value,"") AS color_value,
                                              cm.update_time,
                                              REPLACE(ctm.name,"\'","") AS catalog_name,
                                              scm.uuid AS sub_category_id,
                                              REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1 AND scc.sub_category_id = ?
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                              cm.is_active = 1 AND
                                              ctm.is_featured = 1 AND
                                              cm.content_type = ? AND
                                              cm.uuid NOT IN ('.$this->content_uuids.')
                                            ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->sub_category_id, $this->content_type, $this->offset, $this->item_count]);

                    } elseif ($this->search_category) {

                        $total_row = Cache::remember("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->search_category:$this->content_type", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                            $total_row_result = DB::select('SELECT cm.id
                                                FROM content_master AS cm
                                                WHERE
                                                  cm.is_active = 1 AND
                                                  cm.content_type = '.$this->content_type.' AND
                                                  ISNULL(cm.original_img) AND
                                                  ISNULL(cm.display_img) AND
                                                  (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                    MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                                  cm.uuid NOT IN ('.$this->content_uuids.')
                                                 GROUP BY cm.id');

                            return count($total_row_result);
                        });

                        $template_list = DB::select('SELECT
                                                DISTINCT cm.uuid AS content_id,
                                                ctm.uuid AS catalog_id,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                scm.uuid AS sub_category_id,
                                                ctm.name AS catalog_name,
                                                scm.sub_category_name,
                                                cm.content_type,
                                                cm.template_name,
                                                cm.update_time,
                                                COALESCE(cm.image,"") AS svg_file,
                                                COALESCE(cm.height,0) AS height,
                                                COALESCE(cm.width,0) AS width
                                            FROM
                                                content_master AS cm
                                                JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                                JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                                cm.is_active = 1 AND
                                                cm.content_type = ? AND
                                                ISNULL(cm.original_img) AND
                                                ISNULL(cm.display_img) AND
                                                (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                MATCH(cm.search_category) AGAINST(REPLACE(concat("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                            ORDER BY cm.update_time DESC LIMIT ?,?', [$this->content_type, $this->offset, $this->item_count]);

                    } else {

                        $total_row = Cache::remember("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->search_category:$this->content_type", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                            $total_row_result = DB::select('SELECT
                                                    COUNT(DISTINCT cm.id) AS total
                                                FROM
                                                    content_master AS cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                    JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                                WHERE
                                                    cm.is_active = 1 AND
                                                    ctm.is_featured = 1 AND
                                                    cm.content_type = ? AND
                                                    cm.uuid NOT IN ('.$this->content_uuids.')', [$this->content_type]);

                            return $total_row_result[0]->total;
                        });

                        $template_list = DB::select('SELECT
                                              DISTINCT cm.uuid AS content_id,
                                              ctm.uuid AS catalog_id,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                              cm.content_type,
                                              cm.template_name,
                                              COALESCE(cm.is_featured,"") AS is_featured,
                                              COALESCE(cm.is_free,0) AS is_free,
                                              COALESCE(cm.is_portrait,0) AS is_portrait,
                                              COALESCE(cm.height,0) AS height,
                                              COALESCE(cm.width,0) AS width,
                                              COALESCE(cm.color_value,"") AS color_value,
                                              cm.update_time,
                                              REPLACE(ctm.name,"\'","") AS catalog_name,
                                              scm.uuid AS sub_category_id,
                                              REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                              cm.is_active = 1 AND
                                              ctm.is_featured = 1 AND
                                              cm.content_type = ? AND
                                              cm.uuid NOT IN ('.$this->content_uuids.')
                                            ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->content_type, $this->offset, $this->item_count]);

                    }
                } else {
                    $total_row = 0;
                    $template_list = [];
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;
                $result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];

                return $result;
            });

            if (! $redis_result['result']) {
                $response = Response::json(['code' => 201, 'message' => 'Template does not exist in this category.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'All sub category are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplatesByCategoryId', $e);
            //Log::error("getTemplatesByCategoryId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getTemplatesByCategoryIdV2(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page', 'item_count', 'content_type'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_uuids'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $this->sub_category_id_for_cache = isset($request->sub_category_id) ? $request->sub_category_id : null;
            $this->search_category = isset($request->search_category) ? $request->search_category : null;
            $this->catalog_id = $this->catalog_id_for_cache = isset($request->catalog_id) ? $request->catalog_id : null;
            $this->content_type = $request->content_type;
            $this->page = $request->page;
            $this->item_count = $request->item_count;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_uuids = $request->content_uuids;
            $this->content_uuids = '"'.implode('","', $this->content_uuids).'"';

            if ($this->page <= 5) {

                if ($this->catalog_id) {
                    /* Get all template by catalog_id */
                    $redis_result = Cache::rememberforever("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->catalog_id:$this->content_type:$this->page:$this->item_count", function () {

                        $catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid = ?', [$this->catalog_id]);
                        $this->catalog_id = $catalog_id[0]->id;

                        $total_row = Cache::rememberforever("getTemplatesByCategoryIdV2:$this->sub_category_id_for_cache:$this->catalog_id_for_cache:$this->content_type", function () {

                            $total_row_result = DB::select('SELECT
                                                  COUNT(DISTINCT cm.id) AS total
                                              FROM
                                                  content_master AS cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                  JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                              WHERE
                                                  cm.catalog_id = ? AND
                                                  cm.is_active = 1 AND
                                                  ctm.is_featured = 1 AND
                                                  cm.content_type = ? AND
                                                  cm.uuid NOT IN ('.$this->content_uuids.')', [$this->catalog_id, $this->content_type]);

                            return $total_row_result[0]->total;
                        });

                        $template_list = DB::select('SELECT
                                            DISTINCT cm.uuid AS content_id,
                                            ctm.uuid AS catalog_id,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                            IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                            IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                            cm.content_type,
                                            cm.template_name,
                                            COALESCE(cm.is_featured,"") AS is_featured,
                                            COALESCE(cm.is_free,0) AS is_free,
                                            COALESCE(cm.is_portrait,0) AS is_portrait,
                                            COALESCE(cm.height,0) AS height,
                                            COALESCE(cm.width,0) AS width,
                                            COALESCE(cm.color_value,"") AS color_value,
                                            cm.update_time,
                                            REPLACE(ctm.name,"\'","") AS catalog_name,
                                            scm.uuid AS sub_category_id,
                                            REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                          FROM
                                            content_master AS cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                            JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                          WHERE
                                            cm.is_active = 1 AND
                                            ctm.is_featured = 1 AND
                                            cm.catalog_id = ? AND
                                            cm.content_type = ? AND
                                            cm.uuid NOT IN ('.$this->content_uuids.')
                                          ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->catalog_id, $this->content_type, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count));

                        return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];
                    });

                } elseif ($this->sub_category_id) {
                    /* Get featured templates from all catalogs by sub_category_id */
                    $redis_result = Cache::rememberforever("getTemplatesByCategoryIdV2:$this->sub_category_id:$this->content_type:$this->page:$this->item_count", function () {

                        $sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid = ?', [$this->sub_category_id]);
                        $this->sub_category_id = $sub_category_id[0]->id;

                        $total_row = Cache::rememberforever("getTemplatesByCategoryIdV2:$this->sub_category_id_for_cache:$this->content_type", function () {

                            $total_row_result = DB::select('SELECT
                                                  COUNT(DISTINCT cm.id) AS total
                                              FROM
                                                  content_master AS cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1 AND scc.sub_category_id = ?
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                  JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                              WHERE
                                                  cm.is_active = 1 AND
                                                  cm.is_featured = 1 AND
                                                  cm.content_type = ? AND
                                                    cm.uuid NOT IN ('.$this->content_uuids.') ', [$this->sub_category_id, $this->content_type]);

                            return $total_row_result[0]->total;
                        });

                        $template_list = DB::select('SELECT
                                            DISTINCT cm.uuid AS content_id,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                            IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                            IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                            IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                            IF(cm.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                            cm.content_type,
                                            cm.template_name,
                                            COALESCE(cm.is_featured,"") AS is_featured,
                                            COALESCE(cm.is_free,0) AS is_free,
                                            COALESCE(cm.is_portrait,0) AS is_portrait,
                                            COALESCE(cm.height,0) AS height,
                                            COALESCE(cm.width,0) AS width,
                                            COALESCE(cm.color_value,"") AS color_value,
                                            cm.update_time,
                                            REPLACE(ctm.name,"\'","") AS catalog_name,
                                            scm.uuid AS sub_category_id,
                                            REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                          FROM
                                            content_master AS cm
                                            JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1 AND scc.sub_category_id = ?
                                            JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                            JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                          WHERE
                                            cm.is_active = 1 AND
                                            cm.is_featured = 1 AND
                                            cm.content_type = ? AND
                                            cm.uuid NOT IN ('.$this->content_uuids.')
                                          ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->sub_category_id, $this->content_type, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count));

                        return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];
                    });

                } elseif ($this->search_category) {
                    /* Get templates for search category of video */
                    $redis_result = Cache::rememberforever("getTemplatesByCategoryIdV2:$this->search_category:$this->content_type:$this->page:$this->item_count", function () {

                        $total_row = Cache::rememberforever("getTemplatesByCategoryIdV2:$this->search_category:$this->content_type", function () {

                            $total_row_result = DB::select('SELECT cm.id
                                              FROM content_master AS cm
                                              WHERE
                                                cm.is_active = 1 AND
                                                cm.content_type = '.$this->content_type.' AND
                                                ISNULL(cm.original_img) AND
                                                ISNULL(cm.display_img) AND
                                                (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                                  MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                                cm.uuid NOT IN ('.$this->content_uuids.')
                                              GROUP BY cm.id');

                            return count($total_row_result);
                        });

                        $template_list = DB::select('SELECT
                                              DISTINCT cm.uuid AS content_id,
                                              ctm.uuid AS catalog_id,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                              scm.uuid AS sub_category_id,
                                              ctm.name AS catalog_name,
                                              scm.sub_category_name,
                                              cm.content_type,
                                              cm.template_name,
                                              cm.update_time,
                                              COALESCE(cm.image,"") AS svg_file,
                                              COALESCE(cm.height,0) AS height,
                                              COALESCE(cm.width,0) AS width
                                          FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                          WHERE
                                              cm.is_active = 1 AND
                                              cm.content_type = ? AND
                                              ISNULL(cm.original_img) AND
                                              ISNULL(cm.display_img) AND
                                              (MATCH(cm.search_category) AGAINST("'.$this->search_category.'") OR
                                              MATCH(cm.search_category) AGAINST(REPLACE(CONCAT("'.$this->search_category.'"," ")," ","* ")  IN BOOLEAN MODE)) AND
                                              cm.uuid NOT IN ('.$this->content_uuids.')
                                          ORDER BY cm.update_time DESC LIMIT ?,?', [$this->content_type, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count));

                        return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];
                    });

                } else {
                    /* Get featured templates from all sub category */
                    $redis_result = Cache::remember("getTemplatesByCategoryIdV2:$this->content_type:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                        $total_row = Cache::remember("getTemplatesByCategoryIdV2:$this->content_type", Config::get('constant.CACHE_TIME_24_HOUR'), function () {

                            $total_row_result = DB::select('SELECT
                                                  COUNT(DISTINCT cm.id) AS total
                                              FROM
                                                  content_master AS cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                  JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                              WHERE
                                                  cm.is_active = 1 AND
                                                  ctm.is_featured = 1 AND
                                                  cm.content_type = ? AND
                                                    cm.uuid NOT IN ('.$this->content_uuids.')', [$this->content_type]);

                            return $total_row_result[0]->total;
                        });

                        $template_list = DB::select('SELECT
                                              DISTINCT cm.uuid AS content_id,
                                              ctm.uuid AS catalog_id,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                              IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                              IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                              IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                              cm.content_type,
                                              cm.template_name,
                                              COALESCE(cm.is_featured,"") AS is_featured,
                                              COALESCE(cm.is_free,0) AS is_free,
                                              COALESCE(cm.is_portrait,0) AS is_portrait,
                                              COALESCE(cm.height,0) AS height,
                                              COALESCE(cm.width,0) AS width,
                                              COALESCE(cm.color_value,"") AS color_value,
                                              cm.update_time,
                                              REPLACE(ctm.name,"\'","") AS catalog_name,
                                              scm.uuid AS sub_category_id,
                                              REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                            FROM
                                              content_master AS cm
                                              JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                              JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                              JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                            WHERE
                                              cm.is_active = 1 AND
                                              ctm.is_featured = 1 AND
                                              cm.content_type = ? AND
                                              cm.uuid NOT IN ('.$this->content_uuids.')
                                            ORDER BY cm.update_time DESC LIMIT ?, ?', [$this->content_type, $this->offset, $this->item_count]);

                        $is_next_page = ($total_row > ($this->offset + $this->item_count));

                        return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];
                    });

                }

            } else {
                $total_row = 0;
                $template_list = [];
                $is_next_page = ($total_row > ($this->offset + $this->item_count));
                $redis_result = ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'result' => $template_list];
            }

            if (! $redis_result['result']) {
                $response = Response::json(['code' => 201, 'message' => 'Template does not exist in this category.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 200, 'message' => 'All sub category are fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplatesByCategoryId', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getStaticPageSubCategoryByAdmin  getStaticPageSubCategoryByAdmin
     *
     * @apiName getStaticPageSubCategoryByAdmin
     *
     * @apiGroup admin-side
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
     * "page":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All sub category are fetched successfully.",
     * "cause": "",
     * "data": {
     * "sub_category_lists": [
     * {
     * "static_page_id": 4,
     * "sub_category_id": 4,
     * "sub_category_name": "Pinterest-Pins",
     * "total_catalog": 3,
     * "page_url": "http://192.168.0.116/photoadking_testing/templates/Pinterest-Pins"
     * }
     * ],
     * "total_record": 2,
     * "is_next_page": true
     * }
     * }
     */
    public function getStaticPageSubCategoryByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page'], $request)) != '') {
                return $response;
            }

            //$this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_STATIC_PAGE_SUB_CATEGORY_LIST');
            //$this->page = isset($request->page) ? $request->page : 1;
            //$this->offset = ($this->page - 1) * $this->item_count;
            $this->category_id = 4; //Image template
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getStaticPageSubCategoryByAdmin")) {
                $result = Cache::rememberforever('getStaticPageSubCategoryByAdmin', function () {

                    $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                   FROM static_page_sub_category_master AS sps');
                    $total_row = $total_row_result[0]->total;

                    $sub_category_lists = DB::select('SELECT
                              sp.id AS static_page_id,
                              scm.uuid AS sub_category_id,
                              spsb.sub_category_name,
                              CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path) AS page_url
                              FROM static_page_sub_category_master AS spsb
                              LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id,
                              sub_category_master AS scm
                              WHERE
                               scm.id = sp.sub_category_id AND
                               sp.catalog_id IS NULL
                              ORDER BY sp.rank DESC');

                    return ['sub_category_lists' => $sub_category_lists, 'total_record' => $total_row];

                });
            }

            $redis_result = Cache::get('getStaticPageSubCategoryByAdmin');

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All sub category are fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getStaticPageSubCategoryByAdmin', $e);
            //      Log::error("getStaticPageSubCategoryByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    /**
     * @api {post} getVideoStaticPageTagListByAdmin  getVideoStaticPageTagListByAdmin
     *
     * @apiName getVideoStaticPageTagListByAdmin
     *
     * @apiGroup admin-side
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
     * "page":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All tag fetched successfully.",
     * "cause": "",
     * "data": {
     * "total_record": 2,
     * "is_next_page": true,
     * "sub_category_lists": [
     * {
     * "static_page_id": 37,
     * "tag_title": "Logo",
     * "search_category": "business,education",
     * "page_url": "http://192.168.0.116/photoadking_testing/templates/business1-video",
     * "page_detail": "{\"page_title\":\"test\",\"meta\":\"test\",\"canonical\":\"test\"}",
     * "header_detail": "{\"h1\":\"test\",\"h2\":\"test\",\"cta_text\":\"test\",\"cta_link\":22}",
     * "sub_detail": "{\"h2\":\"test\",\"description\":\"<p>test<\\/p>\"}",
     * "is_active": 1
     * }
     * ]
     * }
     * }
     */
    public function getVideoStaticPageTagListByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['page'], $request)) != '') {
                return $response;
            }

            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_STATIC_PAGE_SUB_CATEGORY_LIST');
            $this->page = isset($request->page) ? $request->page : 1;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->content_type = isset($request->content_type) ? $request->content_type : 9;
            if ($this->content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                $this->content_type = 3;
            } else {
                $this->content_type = 2;
            }
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            $redis_result = Cache::rememberforever("getVideoStaticPageTagListByAdmin$this->content_type:$this->page:$this->item_count", function () {

                $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                   FROM static_page_master AS sps
                                                   WHERE sps.content_type=?', [$this->content_type]);
                $total_row = $total_row_result[0]->total;

                $tag_lists = DB::select('SELECT
                              sp.id static_page_id,
                              sp.tag_title,
                              sp.search_category,
                              CONCAT("'.$this->static_page_dir.'",sp.tag_in_url) AS page_url,
                              sp.app_cta_detail,
                              sp.page_detail,
                              sp.header_detail,
                              sp.sub_detail,
                              sp.is_active,
                              sp.guide_detail,
                              sp.faqs,
                              sp.guide_steps,
                              sp.rating_schema,
                              sp.content_ids
                              FROM  static_page_master AS sp
                              WHERE sp.content_type =?
                              ORDER BY sp.rank DESC ', [$this->content_type]);

                foreach ($tag_lists as $i => $tag) {

                    if ($tag->content_ids != '') {
                        $template_list = $this->getStaticPageTemplateListByContentIds($tag->content_ids);
                        $tag->template_list = $template_list['template_list'];

                    } else {
                        $tag->template_list = [];
                    }

                    $tag->similar_pages = $this->getSimilarPages($tag->static_page_id);
                }

                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'tag_lists' => $tag_lists];
            });

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All tag fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getVideoStaticPageTagListByAdmin', $e);
            //Log::error("getVideoStaticPageTagListByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    /**
     * @api {post} getAllVideoStaticPageTagListByAdmin  getAllVideoStaticPageTagListByAdmin
     *
     * @apiName getAllVideoStaticPageTagListByAdmin
     *
     * @apiGroup admin-side
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
     * "page":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     *   "code": 200,
     *   "message": "All tag fetched successfully.",
     *   "cause": "",
     *   "data": {
     *   "total_record": 14,
     *   "is_next_page": true,
     *   "tag_lists": [
     *   {
     *   "static_page_id": 118,
     *   "tag_title": "cad11",
     *   "search_category": "card,love",
     *   "content_type": 2,
     *   "page_url": "http://192.168.0.134/photoadking_testing_latest/templates/card12",
     *   "page_detail": "{\"page_title\":\"card18\",\"meta\":\"card19\",\"canonical\":\"card20\"}",
     *   "header_detail": "{\"h1\":\"card13\",\"h2\":\"card14\",\"cta_text\":\"card15\",\"cta_link\":\"u1j6j7d929914d\"}",
     *   "sub_detail": "{\"h2\":\"card16\",\"description\":\"<p>card17<\\/p>\"}",
     *   "is_active": 1
     *   }
     *   ]
     *   }
     *  }
     */
    public function getAllVideoStaticPageTagListByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //if (($response = (new VerificationController())->validateRequiredParameter(array('page'), $request)) != '')
            //return $response;

            $this->item_count = isset($request->item_count) ? $request->item_count : Config::get('constant.DEFAULT_ITEM_COUNT_TO_STATIC_PAGE_SUB_CATEGORY_LIST');
            $this->page = isset($request->page) ? $request->page : 1;
            $this->offset = ($this->page - 1) * $this->item_count;

            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllVideoStaticPageTagListByAdmin$this->page:$this->item_count")) {
                $result = Cache::rememberforever("getAllVideoStaticPageTagListByAdmin$this->page:$this->item_count", function () {

                    $total_row_result = DB::select('SELECT COUNT(*) AS total
                                                   FROM static_page_master AS sp
                                                   WHERE sp.content_type IN (?,?) ', [2, 3]);
                    $total_row = $total_row_result[0]->total;

                    $tag_lists = DB::select('SELECT
                              sp.id static_page_id,
                              sp.tag_title,
                              sp.search_category,
                              sp.content_type,
                              CONCAT("'.$this->static_page_dir.'",sp.tag_in_url) AS page_url,
                              sp.page_detail,
                              sp.header_detail,
                              sp.sub_detail,
                              sp.is_active
                              FROM  static_page_master AS sp
                              WHERE sp.content_type IN (?,?)
                              ORDER BY sp.rank DESC', [2, 3]);

                    $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                    return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'tag_lists' => $tag_lists];
                });
            }

            $redis_result = Cache::get("getAllVideoStaticPageTagListByAdmin$this->page:$this->item_count");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All tag fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getAllVideoStaticPageTagListByAdmin', $e);
            //      Log::error("getAllVideoStaticPageTagListByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    /**
     * @api {post} setStaticPageRankByAdmin setStaticPageRankByAdmin
     *
     * @apiName setStaticPageRankByAdmin
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
     * "static_page_ids":"[101,105,117,110]" //compulsory
     * "content_type":"2,3" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Static Page Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setStaticPageRankByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredArrayParameter(['static_page_ids', 'content_type'], $request)) != '') {
                return $response;
            }

            if (! is_array($request->static_page_ids) or count($request->static_page_ids) == 0) {
                return $response = Response::json(['code' => 201, 'message' => 'must be array or atleast one value', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $static_page_ids = $request->static_page_ids;
            $content_type = $request->content_type;

            $create_time = date('Y-m-d H:i:s');

            DB::beginTransaction();
            DB::update('UPDATE
                      static_page_master AS sp
                      SET sp.rank = NULL
                   WHERE content_type IN ('.$content_type.') ');
            DB::commit();

            $static_page_reverse = array_reverse($static_page_ids);
            foreach ($static_page_reverse as $i => $static_page_id) {
                //$rank++;
                DB::update('UPDATE
                      static_page_master AS sp
                      SET sp.rank = "'.$i.'"
                      WHERE
                      sp.content_type IN ('.$content_type.') AND
                      id = ?', [$static_page_id]);
                DB::commit();
            }
            $response = Response::json(['code' => 200, 'message' => 'Static Page Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setStaticPageRankByAdmin', $e);
            //      Log::error("setStaticPageRankByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set content rank.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} getStaticPageCatalogListByAdmin  getStaticPageCatalogListByAdmin
     *
     * @apiName getStaticPageCatalogListByAdmin
     *
     * @apiGroup admin-side
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
     * "sub_category_id":2
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "All sub category are fetched successfully.",
     * "cause": "",
     * "data": {
     * "catalog_lists": [
     * {
     * "static_page_id": 5,
     * "sub_category_id": 4,
     * "sub_category_name": "Pinterest-Pins",
     * "catalog_id": "22",
     * "catalog_name": "600-600",
     * "page_url": "http://192.168.0.116/photoadking_testing/templates/Pinterest-Pins/600-600",
     * "page_detail": "{\"page_title\":\"All PinterestPins 600-600 page title\",\"meta\":\"PinterestPins\",\"canonical\":\"PinterestPins\"}",
     * "header_detail": "{\"h1\":\"ALL PinterestPins 600-600 test page title\",\"h2\":\"PinterestPins page title PinterestPins page title\",\"cta_text\":\"PinterestPins\",\"cta_link\":\"..\\/..\\/..\\/app\\/#\\/editor\\/37\\/244\"}",
     * "sub_detail": "{\"h2\":\"PinterestPins page title\",\"description\":\"With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer.\"}",
     * "is_active": 1
     * },
     * {
     * "static_page_id": 4,
     * "sub_category_id": 4,
     * "sub_category_name": "Pinterest-Pins",
     * "catalog_id": "",
     * "catalog_name": "All",
     * "page_url": "http://192.168.0.116/photoadking_testing/templates/Pinterest-Pins",
     * "page_detail": "{\"page_title\":\"All PinterestPins Default page title\",\"meta\":\"PinterestPins\",\"canonical\":\"PinterestPins\"}",
     * "header_detail": "{\"h1\":\"ALL PinterestPins Default test page title\",\"h2\":\"PinterestPins page title PinterestPins page title\",\"cta_text\":\"PinterestPins\",\"cta_link\":\"..\\/..\\/..\\/app\\/#\\/editor\\/37\\/244\"}",
     * "sub_detail": "{\"h2\":\"PinterestPins page title\",\"description\":\"With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer.\"}",
     * "is_active": 1
     *}
     *]
     *}
     *}
     */
    public function getStaticPageCatalogListByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id'], $request)) != '') {
                return $response;
            }

            $this->sub_category_id = $request->sub_category_id;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getStaticPageCatalogListByAdmin$this->sub_category_id")) {
                $result = Cache::rememberforever("getStaticPageCatalogListByAdmin$this->sub_category_id", function () {

                    $catalog_lists = DB::select('SELECT
                                    sp.id AS static_page_id,
                                    scm.uuid AS sub_category_id,
                                    spsb.sub_category_name,
                                    IF(sp.catalog_id !="",ctm.uuid,0) AS catalog_id,
                                    sp.catalog_name,
                                    sp.content_ids AS content_ids,
                                    sp.search_category AS search_category,
                                    spsb.sub_category_path,
                                    coalesce(sp.catalog_path,"") AS catalog_path,
                                    CONCAT(spsb.sub_category_path,"/",sp.catalog_path) AS page_url_dir,
                                    CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path) as page_url,
                                    sp.app_cta_detail,
                                    sp.page_detail,
                                    sp.header_detail,
                                    sp.sub_detail,
                                    sp.guide_detail,
                                    sp.faqs,
                                    sp.guide_steps,
                                    sp.rating_schema,
                                    sp.is_active
                                FROM static_page_sub_category_master AS spsb
                                  LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                  LEFT JOIN sub_category_master AS scm ON scm.id = spsb.sub_category_id
                                  LEFT JOIN catalog_master AS ctm ON ctm.id = sp.catalog_id
                                WHERE scm.uuid = ?
                                ORDER BY sp.update_time ASC ', [$this->sub_category_id]);

                    foreach ($catalog_lists as $i => $tag) {

                        if ($tag->content_ids != '') {
                            $template_list = $this->getStaticPageTemplateListByContentIds($tag->content_ids);
                            $tag->template_list = $template_list['template_list'];

                        } else {
                            $tag->template_list = [];
                        }

                        $tag->similar_pages = $this->getSimilarPages($tag->static_page_id);
                    }

                    return ['catalog_lists' => $catalog_lists];
                });
            }

            $redis_result = Cache::get("getStaticPageCatalogListByAdmin$this->sub_category_id");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All sub category are fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getStaticPageCatalogListByAdmin', $e);
            //      Log::error("getStaticPageCatalogListByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    public function getStaticMainPageByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_type'], $request)) != '') {
                return $response;
            }

            $this->content_type = $request->content_type;

            if ($this->content_type != 0) {
                return Response::json(['code' => 201, 'message' => 'Something went wrong.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $redis_result = Cache::rememberforever("getStaticMainPageByAdmin:$this->content_type", function () {

                $catalog_lists = DB::select('SELECT
                                                id AS static_page_id,
                                                content_ids,
                                                search_category,
                                                app_cta_detail,
                                                page_detail,
                                                header_detail,
                                                sub_detail,
                                                guide_detail,
                                                faqs,
                                                guide_steps,
                                                rating_schema,
                                                is_active
                                            FROM
                                                static_page_master
                                            WHERE
                                                content_type = ?', [$this->content_type]);

                if (isset($catalog_lists['0']->content_ids) && $catalog_lists['0']->content_ids) {
                    $main_page_content_id = $catalog_lists[0]->content_ids;
                    $main_page_image_content_id = implode(',', json_decode($main_page_content_id)->{'1'});
                    $main_page_video_content_id = implode(',', json_decode($main_page_content_id)->{'2,3'});
                    foreach ($catalog_lists as $i => $tag) {

                        if ($tag->content_ids != '') {
                            $image_template_list = $this->getStaticPageTemplateListByContentIds($main_page_image_content_id, '4');
                            $video_template_list = $this->getStaticPageTemplateListByContentIds($main_page_video_content_id, '9,10');
                            $tag->image_template_list = $image_template_list['template_list'];
                            $tag->video_template_list = $video_template_list['template_list'];

                        } else {
                            $tag->template_list = [];
                        }
                    }
                }

                return $catalog_lists;
            });

            $response = Response::json(['code' => 200, 'message' => 'All templates are fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getStaticMainPageByAdmin', $e);
            //Log::error("getStaticMainPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get main static page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/generateStaticPageByAdmin",
     *        tags={"Admin_StaticPage"},
     *        operationId="generateStaticPageByAdmin",
     *        summary="generateStaticPageByAdmin",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"static_page_id","sub_category_id","sub_category_name","catalog_id","catalog_name","sub_category_path","old_sub_category_path","page_url","old_page_url","page_detail","header_detail","sub_detail"},
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=3, description=""),
     *          @SWG\Property(property="sub_category_name",  type="string", example="Instagram Story", description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=0, description=""),
     *          @SWG\Property(property="catalog_name",  type=0, example="All", description=""),
     *          @SWG\Property(property="sub_category_path",  type="string", example="Instagram-Story", description=""),
     *          @SWG\Property(property="catalog_path",  type="string", example="", description=""),
     *          @SWG\Property(property="page_url",  type="string", example="Instagram-Story", description=""),
     *          @SWG\Property(property="old_pag e_url",  type="string", example="InstagramStory", description=""),
     *          @SWG\Property(property="page_detail",  type="object", example={"page_title":"page_title","meta":"meta","canonical":"canonical"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="guide_steps",  type="object", example={"heading":"Heading","description":"description"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="faqs",  type="object", example={"question":"question","answer":"answer"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="rating_schema",  type="object", example={"userName":"john","name":"name","description":"review","ratingValue":4.8,"reviewCount":420}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="header_detail",  type="object", example={"h1":"Header 1 Description","h2":"Header 2 Description","cta_text":"Card Maker","cta_link":"../../../app/#/editor/37/244"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="sub_detail",  type="object", example={"h2":"Specification_title","description":"With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer.","main_h2":"main_h2","main_description":"main_description"}, description="All parameters in this object are optional"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Static page generated successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} generateStaticPageByAdmin generateStaticPageByAdmin
     *
     * @apiName generateStaticPageByAdmin
     *
     * @apiGroup admin-side
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
     * "sub_category_id":1,
     * "sub_category_name":"Snapchat Geo Filter",
     * "catalog_id":1,
     * "catalog_name":"3d card",
     * "page_url":"/flyers/3d-flyer/",
     * "page_generator": {
     * "page_title": "page_title",
     * "meta": "meta",
     * "canonical": "canonical",
     * "header_detail": {
     * "h1": "Header 1 Description",
     * "h2": "Header 2 Description",
     * "cta_text": "Card Maker",
     * "cta_link": "../../../app/#/editor/37/244"
     * },
     * "sub_detail": {
     * "h2": "Specification_title",
     * "description": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer.",
     * "main_h2": "Specification_title",
     * "main_description": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."
     * },
     * "guide_steps": {
     * "heading": "Specification_title",
     * "description": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."
     * },
     *"faqs": {
     * "question": "Specification_title",
     * "asnwer": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."
     * },
     *"rating_schema": {
     * "userName":"John",
     * "name":"name",
     * "description":"review",
     * "ratingValue":4.8,
     * "reviewCount":420
     * },
     *"guide_detail": {
     * "guide_heading" : "Hot to make poster",
     * "guide_btn_text":"Create you own logo"
     * }
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Static page generated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function generateStaticPageByAdmin(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'sub_category_name', 'sub_category_path', 'page_url', 'page_detail', 'header_detail', 'sub_detail'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $request->sub_category_id;
            $sub_category_name = $request->sub_category_name;
            $catalog_id = isset($request->catalog_id) && $request->catalog_id != '' ? $request->catalog_id : 0;
            $catalog_name = isset($request->catalog_name) ? $request->catalog_name : null;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $sub_category_path = $request->sub_category_path;
            $catalog_path = isset($request->catalog_path) ? $request->catalog_path : null;
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $page_url = $request->page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $active_image_tab = 'active';
            $search_category = '';
            $content_type = '';
            $rank = null;
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];

            if ($sub_category_path != '' && preg_match('/[A-Z]/', $sub_category_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($catalog_path != '' && preg_match('/[A-Z]/', $catalog_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `catalog name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            if (file_exists($file_dir)) {

                return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            $get_sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
            $sub_category_id = $get_sub_category_id[0]->id;

            if ($catalog_id === 0) {
                $catalog_id = null;
            } else {
                $get_catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid = ?', [$catalog_id]);
                $catalog_id = $get_catalog_id[0]->id;
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            //Check is it exist in DB
            if (($response = (new VerificationController())->checkIsPathAvailable('', $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';
            $create_time = date('Y-m-d H:i:s');

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            $data = ['sub_category_id' => $sub_category_id,
                'sub_category_name' => $sub_category_name,
                'sub_category_path' => $sub_category_path,
                'create_time' => $create_time,
            ];

            DB::beginTransaction();

            $is_exist = DB::select('SELECT * FROM static_page_sub_category_master WHERE sub_category_id = ?', [$sub_category_id]);

            if (count($is_exist) > 0) {
                $static_sbc_page_id = $is_exist[0]->id;
            } else {
                $static_sbc_page_id = DB::table('static_page_sub_category_master')->insertGetId($data);
                $select_rank = DB::select('SELECT max(spm.rank) AS rank FROM static_page_master AS spm WHERE content_type IN (?)', [1]);
                if ($select_rank[0]->rank !== null) {
                    $rank = $select_rank[0]->rank;
                    $rank++;
                } else {
                    $rank = 0;
                }
            }
            $uuid = (new ImageController())->generateUUID();
            if ($uuid == '') {
                return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $static_page_master_data = [
                'static_page_sub_category_id' => $static_sbc_page_id,
                'sub_category_id' => $sub_category_id,
                'uuid' => $uuid,
                'catalog_id' => $catalog_id,
                'catalog_name' => $catalog_name,
                'catalog_path' => $catalog_path,
                'page_detail' => json_encode($page_detail),
                'header_detail' => json_encode($header_detail),
                'sub_detail' => json_encode($sub_detail),
                'rating_schema' => json_encode($rating_schema),
                'faqs' => json_encode($faqs),
                'guide_steps' => json_encode($guide_steps),
                'guide_detail' => json_encode($guide_detail),
                'create_time' => $create_time,
                'rank' => $rank,

            ];

            $static_page_id = DB::table('static_page_master')->insertGetId($static_page_master_data);

            if (count($similar_tags) > 0) {
                //Delete cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                //Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get similar template page list
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $sub_pages = $this->getSubPages($static_sbc_page_id, 1);

            $API_getStaticPageTemplateListById = $active_path.'/api/public/api/getStaticPageTemplateListById';
            $API_getStaticPageTemplateListByTag = $active_path.'/api/public/api/getStaticPageTemplateListByTag';
            $path_of_header_cta_link = $active_path.'/app/#/editor/'.$header_cta_link;

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }
            $left_nav_html = $this->getLeftNavigation($static_page_id);
            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'API_getStaticPageTemplateListById' => $API_getStaticPageTemplateListById,
                'API_getStaticPageTemplateListByTag' => $API_getStaticPageTemplateListByTag,
                'static_page_id' => $static_page_id,
                'search_category' => $search_category,
                'active_image_tab' => $active_image_tab,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
            ];

            $html = view('static_page', compact('template'))->render();

            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w');

            fwrite($handle, $html);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('generateStaticPageByAdmin', $e);
            //      Log::error("generateStaticPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    /*
Purpose : for generate index file without calling api in static page
Description : This method compulsory take 7 argument as parameter.(if any argument is optional then define it here)
Return : return code, message, static page url if success otherwise error with specific status code
*/
    public function generateStaticPageByAdminV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['sub_category_id', 'sub_category_name', 'sub_category_path', 'page_url', 'page_detail', 'header_detail', 'sub_detail', 'app_cta_detail'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_ids', 'template_list'], $request)) != '') {
                return $response;
            }

            $sub_category_id = $sub_category_uuid = $request->sub_category_id;
            $sub_category_name = $request->sub_category_name;
            $catalog_id = $catalog_uuid = isset($request->catalog_id) && $request->catalog_id != '' ? $request->catalog_id : 0;
            $catalog_name = isset($request->catalog_name) ? $request->catalog_name : null;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $sub_category_path = $request->sub_category_path;
            $catalog_path = isset($request->catalog_path) ? $request->catalog_path : null;
            $page_url = $request->page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $app_cta_detail = $request->app_cta_detail;
            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $active_image_tab = 'active';
            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            $rank = null;
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];
            $content_ids = implode(',', $request->content_ids);
            $template_list = $request->template_list;
            $search_category = isset($request->search_category) ? $request->search_category : null;

            if ($sub_category_path != '' && preg_match('/[A-Z]/', $sub_category_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($catalog_path != '' && preg_match('/[A-Z]/', $catalog_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `catalog name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            if (file_exists($file_dir)) {
                return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            $get_sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
            $sub_category_id = $get_sub_category_id[0]->id;

            if ($catalog_id === 0) {
                $catalog_id = null;
            } else {
                $get_catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid = ?', [$catalog_id]);
                $catalog_id = $get_catalog_id[0]->id;
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            //Check is it exist in DB
            if (($response = (new VerificationController())->checkIsPathAvailable('', $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';
            $create_time = date('Y-m-d H:i:s');

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            $data = ['sub_category_id' => $sub_category_id,
                'sub_category_name' => $sub_category_name,
                'sub_category_path' => $sub_category_path,
                'create_time' => $create_time,
            ];

            DB::beginTransaction();

            $is_exist = DB::select('SELECT * FROM static_page_sub_category_master WHERE sub_category_id = ?', [$sub_category_id]);

            if (count($is_exist) > 0) {
                $static_sbc_page_id = $is_exist[0]->id;
                $is_image_main_page = 0;
            } else {
                $static_sbc_page_id = DB::table('static_page_sub_category_master')->insertGetId($data);
                $is_image_main_page = 1;
                $select_rank = DB::select('SELECT max(spm.rank) AS rank FROM static_page_master AS spm WHERE content_type IN (?)', [1]);
                if ($select_rank[0]->rank !== null) {
                    $rank = $select_rank[0]->rank;
                    $rank++;
                } else {
                    $rank = 0;
                }
            }
            $uuid = (new ImageController())->generateUUID();
            if ($uuid == '') {
                return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $static_page_master_data = [
                'static_page_sub_category_id' => $static_sbc_page_id,
                'sub_category_id' => $sub_category_id,
                'uuid' => $uuid,
                'catalog_id' => $catalog_id,
                'catalog_name' => $catalog_name,
                'catalog_path' => $catalog_path,
                'app_cta_detail' => json_encode($app_cta_detail),
                'page_detail' => json_encode($page_detail),
                'header_detail' => json_encode($header_detail),
                'sub_detail' => json_encode($sub_detail),
                'rating_schema' => json_encode($rating_schema),
                'faqs' => json_encode($faqs),
                'guide_steps' => json_encode($guide_steps),
                'guide_detail' => json_encode($guide_detail),
                'create_time' => $create_time,
                'rank' => $rank,
                'content_ids' => $content_ids,
                'search_category' => $search_category,
            ];

            $static_page_id = DB::table('static_page_master')->insertGetId($static_page_master_data);

            if (count($similar_tags) > 0) {
                //Delete cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                //Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get similar template page list
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $sub_pages = $this->getSubPages($static_sbc_page_id, 1);

            $path_of_header_cta_link = $active_path.'/app/#/editor/'.$header_cta_link;

            $API_getTemplatesByCategoryIdV2 = $active_path.'/api/public/api/getTemplatesByCategoryIdV2';

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigationV2('active', '', $static_page_id);

            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'app_cta_detail' => $app_cta_detail,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'static_page_id' => $static_page_id,
                'search_category' => '',
                'active_image_tab' => $active_image_tab,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
                'image_template_list' => json_encode($template_list),
                'catalog_id' => $catalog_uuid,
                'sub_category_id' => $sub_category_uuid,
                'API_getTemplatesByCategoryIdV2' => $API_getTemplatesByCategoryIdV2,
                'video_template_list' => json_encode([]),
                'is_image_main_page' => $is_image_main_page,
            ];

            $html = view('static_page_v2', compact('template'))->render();

            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w');

            fwrite($handle, $html);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('generateStaticPageByAdminV2', $e);
            //Log::error("generateStaticPageByAdminV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} generateVideoStaticPageByAdmin generateVideoStaticPageByAdmin
     *
     * @apiName generateVideoStaticPageByAdmin
     *
     * @apiGroup admin-side
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
     * "tag_title":"Snapchat",
     * "search_category":"business,education",
     * "page_url":"flyer-video",
     * "page_detail": {
     * "page_title": "page_title",
     * "meta": "meta",
     * "canonical": "canonical"
     * },
     * "header_detail": {
     * "h1": "Header 1 Description",
     * "h2": "Header 2 Description",
     * "cta_text": "Card Maker",
     * "cta_link": "../../../app/#/editor/37/244"
     * },
     * "sub_detail": {
     * "h2": "Specification_title",
     * "description": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Static page generated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function generateVideoStaticPageByAdmin(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['tag_title', 'search_category', 'page_url', 'page_detail', 'header_detail', 'sub_detail'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredArrayParameter(['content_ids'], $request)) != '') {
                return $response;
            }

            //      $sub_category_id = $request->sub_category_id;
            //      $sub_category_name = $request->sub_category_name;
            //      $catalog_id = isset($request->catalog_id) ? $request->catalog_id : 0;
            //      $catalog_name = isset($request->catalog_name) ? $request->catalog_name : NULL;
            //      $sub_category_path = $request->sub_category_path;
            //      $catalog_path = isset($request->catalog_path) ? $request->catalog_path : NULL;
            $tag_title = $request->tag_title;
            $search_category = $request->search_category;
            $page_url = $request->page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $content_ids = $request->content_ids;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';

            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $content_type = isset($request->content_type) ? $request->content_type : 9;
            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                $content_type = 3;
                $editor = 'intro-editor';
            } else {
                $content_type = 2;
                $editor = 'video-editor';
            }
            $active_video_tab = 'active';

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `page name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (is_array($content_ids) or count($content_ids) > 0) {
                $content_id = implode(',', $content_ids);
            } else {
                $content_id = null;
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            /*
            $check_url = DB::select('SELECT tag_in_url  FROM static_page_master AS spm WHERE tag_in_url=? ',[$page_url]);
            if(count($check_url) > 0){
              return Response::json(array('code' => 201, 'message' => 'This static page already exist having this path : ' . $this->static_page_dir . $page_url . '.', 'cause' => '', 'data' => json_decode("{}")));
            }
            */

            if (file_exists($file_dir)) {
                return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //      //Check is it exist in DB
            //      if (($response = (new VerificationController())->checkIsPathAvailable('', $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '')
            //        return $response;
            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';
            $create_time = date('Y-m-d H:i:s');

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            $uuid = (new ImageController())->generateUUID();
            if ($uuid == '') {
                return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $select_rank = DB::select('SELECT max(spm.rank) AS rank FROM static_page_master AS spm WHERE content_type IN (?,?)', [2, 3]);
            if ($select_rank[0]->rank !== null) {
                $rank = $select_rank[0]->rank;
                $rank++;
            } else {
                $rank = 0;
            }

            DB::beginTransaction();

            $static_page_master_data = [
                'uuid' => $uuid,
                'page_detail' => json_encode($page_detail),
                'header_detail' => json_encode($header_detail),
                'sub_detail' => json_encode($sub_detail),
                'rating_schema' => json_encode($rating_schema),
                'guide_steps' => json_encode($guide_steps),
                'faqs' => json_encode($faqs),
                'guide_detail' => json_encode($guide_detail),
                'content_type' => $content_type,
                'tag_in_url' => $page_url,
                'tag_title' => $tag_title,
                'search_category' => $search_category,
                'create_time' => $create_time,
                'rank' => $rank,
                'content_ids' => $content_id,

            ];

            $static_page_id = DB::table('static_page_master')->insertGetId($static_page_master_data);

            //Similar template pages
            if (count($similar_tags) > 0) {

                //Clear Redis cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                // Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get Similar template page list
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $sub_pages = $this->getSubPages($static_page_id);

            $API_getStaticPageTemplateListById = $active_path.'/api/public/api/getStaticPageTemplateListById';
            $API_getStaticPageTemplateListByTag = $active_path.'/api/public/api/getStaticPageTemplateListByTag';
            $path_of_header_cta_link = $active_path.'/app/#/'.$editor.'/'.$header_cta_link;

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigation($static_page_id);
            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'API_getStaticPageTemplateListById' => $API_getStaticPageTemplateListById,
                'API_getStaticPageTemplateListByTag' => $API_getStaticPageTemplateListByTag,
                'static_page_id' => 0,
                'search_category' => $search_category,
                'active_video_tab' => $active_video_tab,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
            ];

            $html = view('static_page', compact('template'))->render();

            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w');

            fwrite($handle, $html);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('generateVideoStaticPageByAdmin', $e);
            //      Log::error("generateVideoStaticPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    //generate index file without calling api in static page
    public function generateVideoStaticPageByAdminV2(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['tag_title', 'search_category', 'page_url', 'page_detail', 'header_detail', 'sub_detail', 'search_category', 'app_cta_detail'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_ids', 'template_list'], $request)) != '') {
                return $response;
            }

            //      $sub_category_id = $request->sub_category_id;
            //      $sub_category_name = $request->sub_category_name;
            //      $catalog_id = isset($request->catalog_id) ? $request->catalog_id : 0;
            //      $catalog_name = isset($request->catalog_name) ? $request->catalog_name : NULL;
            //      $sub_category_path = $request->sub_category_path;
            //      $catalog_path = isset($request->catalog_path) ? $request->catalog_path : NULL;
            $tag_title = $request->tag_title;
            $search_category = $request->search_category;
            $page_url = $request->page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $app_cta_detail = $request->app_cta_detail;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';
            $content_ids = implode(',', $request->content_ids);
            $template_list = $request->template_list;

            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $content_type = isset($request->content_type) ? $request->content_type : 9;
            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                $content_type = 3;
                $editor = 'intro-editor';
            } else {
                $content_type = 2;
                $editor = 'video-editor';
            }
            $active_video_tab = 'active';

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `page name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            /*
            $check_url = DB::select('SELECT tag_in_url  FROM static_page_master AS spm WHERE tag_in_url=? ',[$page_url]);
            if(count($check_url) > 0){
              return Response::json(array('code' => 201, 'message' => 'This static page already exist having this path : ' . $this->static_page_dir . $page_url . '.', 'cause' => '', 'data' => json_decode("{}")));
            }
            */

            if (file_exists($file_dir)) {
                return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //      //Check is it exist in DB
            //      if (($response = (new VerificationController())->checkIsPathAvailable('', $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '')
            //        return $response;
            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';
            $create_time = date('Y-m-d H:i:s');

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            $uuid = (new ImageController())->generateUUID();
            if ($uuid == '') {
                return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $select_rank = DB::select('SELECT max(spm.rank) AS rank FROM static_page_master AS spm WHERE content_type IN (?,?)', [2, 3]);
            if ($select_rank[0]->rank !== null) {
                $rank = $select_rank[0]->rank;
                $rank++;
            } else {
                $rank = 0;
            }

            DB::beginTransaction();

            $static_page_master_data = [
                'uuid' => $uuid,
                'app_cta_detail' => json_encode($app_cta_detail),
                'page_detail' => json_encode($page_detail),
                'header_detail' => json_encode($header_detail),
                'sub_detail' => json_encode($sub_detail),
                'rating_schema' => json_encode($rating_schema),
                'guide_steps' => json_encode($guide_steps),
                'faqs' => json_encode($faqs),
                'guide_detail' => json_encode($guide_detail),
                'content_type' => $content_type,
                'tag_in_url' => $page_url,
                'tag_title' => $tag_title,
                'search_category' => $search_category,
                'create_time' => $create_time,
                'rank' => $rank,
                'content_ids' => $content_ids,
            ];

            $static_page_id = DB::table('static_page_master')->insertGetId($static_page_master_data);

            //Similar template pages
            if (count($similar_tags) > 0) {

                //Clear Redis cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                // Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get Similar template page list
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $sub_pages = $this->getSubPages($static_page_id);

            $path_of_header_cta_link = $active_path.'/app/#/'.$editor.'/'.$header_cta_link;

            $API_getTemplatesByCategoryIdV2 = $active_path.'/api/public/api/API_getTemplatesByCategoryIdV2';

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigationV2('', 'active', $static_page_id);

            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'app_cta_detail' => $app_cta_detail,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'static_page_id' => 0,
                'search_category' => $search_category,
                'active_video_tab' => $active_video_tab,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
                'sub_category_id' => '',
                'API_getTemplatesByCategoryIdV2' => $API_getTemplatesByCategoryIdV2,
                'image_template_list' => json_encode([]),
                'video_template_list' => json_encode($template_list),
            ];

            $html = view('static_page_v2', compact('template'))->render();

            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w');

            fwrite($handle, $html);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('generateVideoStaticPageByAdminV2', $e);
            //Log::error("generateVideoStaticPageByAdminV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'generate static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editStaticPageByAdmin",
     *        tags={"Admin_StaticPage"},
     *        operationId="editStaticPageByAdmin",
     *        summary="editStaticPageByAdmin",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"static_page_id","sub_category_id","sub_category_name","catalog_id","catalog_name","sub_category_path","old_sub_category_path","page_url","old_page_url","page_detail","header_detail","sub_detail"},
     *
     *          @SWG\Property(property="static_page_id",  type="integer", example=7, description=""),
     *          @SWG\Property(property="sub_category_id",  type="integer", example=3, description=""),
     *          @SWG\Property(property="sub_category_name",  type="string", example="Instagram Story", description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=0, description=""),
     *          @SWG\Property(property="catalog_name",  type=0, example="All", description=""),
     *          @SWG\Property(property="sub_category_path",  type="string", example="Instagram-Story", description=""),
     *          @SWG\Property(property="catalog_path",  type="string", example="", description=""),
     *          @SWG\Property(property="old_sub_category_path",  type="string", example="InstagramStory", description=""),
     *          @SWG\Property(property="page_url",  type="string", example="Instagram-Story", description=""),
     *          @SWG\Property(property="old_page_url",  type="string", example="InstagramStory", description=""),
     *          @SWG\Property(property="page_detail",  type="object", example={"page_title":"page_title","meta":"meta","canonical":"canonical"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="header_detail",  type="object", example={"h1":"Header 1 Description","h2":"Header 2 Description","cta_text":"Card Maker","cta_link":"../../../app/#/editor/37/244"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="sub_detail",  type="object", example={"h2":"Specification_title","description":"With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."}, description="All parameters in this object are optional"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Static page generated successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} editStaticPageByAdmin editStaticPageByAdmin
     *
     * @apiName editStaticPageByAdmin
     *
     * @apiGroup admin-side
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
     * "static_page_id":7,
     * "sub_category_id": 3,
     * "sub_category_name": "Instagram Story",
     * "catalog_id": 0,
     * "catalog_name": "All",
     * "sub_category_path":"Instagram-Story",
     * "old_sub_category_path":"InstagramStory",
     * "page_url": "Instagram-Story",
     * "old_page_url":"InstagramStory",
     * "page_detail": {
     * "page_title": "Testing All Instagram-Story Default page title",
     * "meta": "Instagram-Story",
     * "canonical": "Instagram-Story"
     * },
     * "header_detail": {
     * "h1": "ALL Instagram-Story Default test page title",
     * "h2": "InstagramStory page title Instagram-Story page title",
     * "cta_text": "Instagram-Story",
     * "cta_link": "../../../app/#/editor/37/244"
     * },
     * "sub_detail": {
     * "h2": "Instagram-Story page title",
     * "description": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Static page generated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editStaticPageByAdmin(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id', 'sub_category_id', 'sub_category_name', 'sub_category_path', 'old_sub_category_path', 'old_page_url', 'page_url', 'page_detail', 'header_detail', 'sub_detail'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $sub_category_id = $request->sub_category_id;
            $sub_category_name = $request->sub_category_name;
            $catalog_id = isset($request->catalog_id) && ($request->catalog_id) ? $request->catalog_id : 0;
            $catalog_name = isset($request->catalog_name) ? $request->catalog_name : null;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $sub_category_path = $request->sub_category_path;
            $old_sub_category_path = $request->old_sub_category_path;
            $catalog_path = isset($request->catalog_path) ? $request->catalog_path : null;
            $old_catalog_path = isset($request->old_catalog_path) ? $request->old_catalog_path : null;
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $page_url = $request->page_url;
            $old_page_url = $request->old_page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $active_image_tab = 'active';

            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $old_file_dir = $static_page_dir.$old_page_url.'/'.$file_name;
            $old_file_rename = $folder_path.'/'.'index_'.date('Y_m_d_H_i_s').'.html';
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $search_category = '';
            $content_type = '';
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];

            if ($sub_category_path != '' && preg_match('/[A-Z]/', $sub_category_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($catalog_path != '' && preg_match('/[A-Z]/', $catalog_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `catalog name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            if ($catalog_id === 0) {
                $catalog_id = null;
            } else {
                $get_catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid = ?', [$catalog_id]);
                $catalog_id = $get_catalog_id[0]->id;
            }
            $get_sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
            $sub_category_id = $get_sub_category_id[0]->id;

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            //Check is it exist in DB
            if (($response = (new VerificationController())->checkIsPathAvailable($static_page_id, $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '') {
                return $response;
            }

            if (file_exists($old_file_dir)) {
                //check sub_category_path and old_sub_category_path same
                if ($sub_category_path != $old_sub_category_path) {

                    //Check new sub category already exist or not
                    if (file_exists($static_page_dir.$sub_category_path)) {
                        return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    if (! rename($static_page_dir.$old_sub_category_path, $static_page_dir.'/'.$sub_category_path)) {
                        return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t rename url of sub category path.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }

                if ($catalog_path != $old_catalog_path) {
                    if (! rename($static_page_dir.$sub_category_path.'/'.$old_catalog_path, $static_page_dir.$sub_category_path.'/'.$catalog_path)) {
                        return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t rename url of catalog path.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }
            }

            if ($catalog_path != '') {
                $file_dir = $static_page_dir.$sub_category_path.'/'.$catalog_path.'/'.$file_name;
            } else {
                $file_dir = $static_page_dir.$sub_category_path.'/'.$file_name;
            }

            if (! rename($file_dir, $old_file_rename)) {
                return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t generate this page. Try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();

            DB::update('UPDATE static_page_master
                                      SET sub_category_id = ?,
                                      catalog_id = ?,
                                      catalog_name = ?,
                                      catalog_path = ?,
                                      guide_detail=?,
                                      rating_schema=?,
                                      faqs =? ,
                                      guide_steps =?,
                                      page_detail = ?,
                                      header_detail = ?,
                                      sub_detail = ?
                                      WHERE id = ?', [
                $sub_category_id,
                $catalog_id,
                $catalog_name,
                $catalog_path,
                json_encode($guide_detail),
                json_encode($rating_schema),
                json_encode($faqs),
                json_encode($guide_steps),
                json_encode($page_detail),
                json_encode($header_detail),
                json_encode($sub_detail),
                $static_page_id,
            ]);

            //      if ($catalog_id === 0) {
            DB::update('UPDATE static_page_sub_category_master
                                      SET sub_category_path = ?,
                                      sub_category_name = ?
                                      WHERE sub_category_id = ?',
                [$sub_category_path, $sub_category_name, $sub_category_id]);
            //      }

            //Similar template pages
            if (count($similar_tags) > 0) {

                //Clear Redis cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                // Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get similar template pages
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $main_page_detail = DB::select('SELECT static_page_sub_category_id FROM static_page_master WHERE id=?', [$static_page_id]);

            $sub_pages = $this->getSubPages($main_page_detail[0]->static_page_sub_category_id, 1);

            $API_getStaticPageTemplateListById = $active_path.'/api/public/api/getStaticPageTemplateListById';
            $API_getStaticPageTemplateListByTag = $active_path.'/api/public/api/getStaticPageTemplateListByTag';
            $path_of_header_cta_link = $active_path.'/app/#/editor/'.$header_cta_link;

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');

            //Get html code for static page's analytics
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigation($static_page_id);

            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'API_getStaticPageTemplateListById' => $API_getStaticPageTemplateListById,
                'API_getStaticPageTemplateListByTag' => $API_getStaticPageTemplateListByTag,
                'static_page_id' => $static_page_id,
                'active_image_tab' => $active_image_tab,
                'search_category' => $search_category,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
            ];

            //Get html code for static page
            $html = view('static_page', compact('template'))->render();

            //Create static page on path
            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w') or exit('Cannot open file');

            fwrite($handle, $html);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('editStaticPageByAdmin', $e);
            //      Log::error("editStaticPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;

    }

    /*
    Purpose : for edit index file without calling api in static page
    Description : This method compulsory take 10 argument as parameter.(if any argument is optional then define it here)
    Return : return code, message, static page url if success otherwise error with specific status code
    */
    public function editStaticPageByAdminV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id', 'sub_category_id', 'sub_category_name', 'sub_category_path', 'old_sub_category_path', 'old_page_url', 'page_url', 'page_detail', 'header_detail', 'sub_detail', 'app_cta_detail'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_ids', 'template_list'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $sub_category_id = $sub_category_uuid = $request->sub_category_id;
            $sub_category_name = $request->sub_category_name;
            $catalog_id = $catalog_uuid = isset($request->catalog_id) && ($request->catalog_id) ? $request->catalog_id : 0;
            $catalog_name = isset($request->catalog_name) ? $request->catalog_name : null;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $sub_category_path = $request->sub_category_path;
            $old_sub_category_path = $request->old_sub_category_path;
            $catalog_path = isset($request->catalog_path) ? $request->catalog_path : null;
            $old_catalog_path = isset($request->old_catalog_path) ? $request->old_catalog_path : null;
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $page_url = $request->page_url;
            $old_page_url = $request->old_page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $app_cta_detail = $request->app_cta_detail;
            $active_image_tab = 'active';

            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $old_file_dir = $static_page_dir.$old_page_url.'/'.$file_name;
            $old_file_rename = $folder_path.'/'.'index_'.date('Y_m_d_H_i_s').'.html';
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON');
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];
            $content_ids = implode(',', $request->content_ids);
            $template_list = $request->template_list;
            $search_category = isset($request->search_category) ? $request->search_category : null;

            if ($sub_category_path != '' && preg_match('/[A-Z]/', $sub_category_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($catalog_path != '' && preg_match('/[A-Z]/', $catalog_path)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `catalog name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `category name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            if ($catalog_id === 0) {
                $catalog_id = null;
            } else {
                $get_catalog_id = DB::select('SELECT id FROM catalog_master WHERE uuid = ?', [$catalog_id]);
                $catalog_id = $get_catalog_id[0]->id;
            }
            $get_sub_category_id = DB::select('SELECT id FROM sub_category_master WHERE uuid = ?', [$sub_category_id]);
            $sub_category_id = $get_sub_category_id[0]->id;

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            //Check is it exist in DB
            if (($response = (new VerificationController())->checkIsPathAvailable($static_page_id, $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '') {
                return $response;
            }

            if (file_exists($old_file_dir)) {
                //check sub_category_path and old_sub_category_path same
                if ($sub_category_path != $old_sub_category_path) {

                    //Check new sub category already exist or not
                    if (file_exists($static_page_dir.$sub_category_path)) {
                        return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    if (! rename($static_page_dir.$old_sub_category_path, $static_page_dir.'/'.$sub_category_path)) {
                        return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t rename url of sub category path.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }

                if ($catalog_path != $old_catalog_path) {
                    if (! rename($static_page_dir.$sub_category_path.'/'.$old_catalog_path, $static_page_dir.$sub_category_path.'/'.$catalog_path)) {
                        return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t rename url of catalog path.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }
            }

            if ($catalog_path != '') {
                $is_image_main_page = 0;
                $file_dir = $static_page_dir.$sub_category_path.'/'.$catalog_path.'/'.$file_name;
            } else {
                $is_image_main_page = 1;
                $file_dir = $static_page_dir.$sub_category_path.'/'.$file_name;
            }

            if (! rename($file_dir, $old_file_rename)) {
                return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t generate this page. Try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();

            DB::update('UPDATE static_page_master
                                      SET sub_category_id = ?,
                                      catalog_id = ?,
                                      catalog_name = ?,
                                      search_category = ?,
                                      content_ids = ?,
                                      catalog_path = ?,
                                      guide_detail=?,
                                      rating_schema=?,
                                      faqs =? ,
                                      guide_steps =?,
                                      app_cta_detail = ?,
                                      page_detail = ?,
                                      header_detail = ?,
                                      sub_detail = ?
                                      WHERE id = ?', [
                $sub_category_id,
                $catalog_id,
                $catalog_name,
                $search_category,
                $content_ids,
                $catalog_path,
                json_encode($guide_detail),
                json_encode($rating_schema),
                json_encode($faqs),
                json_encode($guide_steps),
                json_encode($app_cta_detail),
                json_encode($page_detail),
                json_encode($header_detail),
                json_encode($sub_detail),
                $static_page_id,
            ]);

            //      if ($catalog_id === 0) {
            DB::update('UPDATE static_page_sub_category_master
                                      SET sub_category_path = ?,
                                      sub_category_name = ?
                                      WHERE sub_category_id = ?',
                [$sub_category_path, $sub_category_name, $sub_category_id]);
            //      }

            //Similar template pages
            if (count($similar_tags) > 0) {

                //Clear Redis cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                // Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get similar template pages
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $main_page_detail = DB::select('SELECT static_page_sub_category_id FROM static_page_master WHERE id=?', [$static_page_id]);

            $sub_pages = $this->getSubPages($main_page_detail[0]->static_page_sub_category_id, 1);

            $path_of_header_cta_link = $active_path.'/app/#/editor/'.$header_cta_link;

            $API_getTemplatesByCategoryIdV2 = $active_path.'/api/public/api/getTemplatesByCategoryIdV2';

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');

            //Get html code for static page's analytics
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigationV2('active', '', $static_page_id);

            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'app_cta_detail' => $app_cta_detail,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'static_page_id' => $static_page_id,
                'active_image_tab' => $active_image_tab,
                'search_category' => '',
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
                'image_template_list' => json_encode($template_list),
                'catalog_id' => $catalog_uuid,
                'sub_category_id' => $sub_category_uuid,
                'API_getTemplatesByCategoryIdV2' => $API_getTemplatesByCategoryIdV2,
                'video_template_list' => json_encode([]),
                'is_image_main_page' => $is_image_main_page,
            ];

            //Get html code for static page
            $html = view('static_page_v2', compact('template'))->render();

            //Create static page on path
            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }

            $handle = fopen($file_dir, 'w') or exit('Cannot open file');

            fwrite($handle, $html);

            DB::commit();

            (new UserController())->deleteAllRedisKeys("getTemplatesByCategoryIdV2:$sub_category_uuid:$content_type");
            (new UserController())->deleteAllRedisKeys("getTemplatesByCategoryIdV2:$sub_category_uuid:$catalog_uuid:$content_type");
            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('editStaticPageByAdminV2', $e);
            //Log::error("editStaticPageByAdminV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function editStaticMainPageByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_ids', 'page_detail', 'header_detail', 'sub_detail', 'app_cta_detail'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $this->active_link_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir_value = Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->static_page_dir = $this->active_link_path.$this->static_page_dir_value;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $app_cta_detail = $request->app_cta_detail;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $file_name = 'index.html';
            $static_page_dir = '../..'.$this->static_page_dir_value;
            $content_type = '';
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? json_decode(json_encode($request->guide_steps), true) : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? json_decode(json_encode($request->faqs), true) : [];
            $content_ids = isset($request->content_ids) ? $request->content_ids : null;
            $image_template_list = isset($request->image_template_list) ? $request->image_template_list : null;
            $video_template_list = isset($request->video_template_list) ? $request->video_template_list : null;

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text'], $header_detail)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }
            }
            DB::beginTransaction();

            DB::update('UPDATE static_page_master
                                      SET
                                      content_ids = ?,
                                      guide_detail=?,
                                      rating_schema=?,
                                      faqs =? ,
                                      guide_steps =?,
                                      app_cta_detail = ?,
                                      page_detail = ?,
                                      header_detail = ?,
                                      sub_detail = ?
                                      WHERE content_type = ?', [
                json_encode($content_ids),
                json_encode($guide_detail),
                json_encode($rating_schema),
                json_encode($faqs),
                json_encode($guide_steps),
                json_encode($app_cta_detail),
                json_encode($page_detail),
                json_encode($header_detail),
                json_encode($sub_detail),
                $content_type,
            ]);

            $image_pages = DB::select('SELECT
                                   spsb.id,
                                   sp.rank,
                                   spsb.sub_category_name AS tag,
                                   CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/") AS page_url
                                 FROM static_page_sub_category_master AS spsb
                                 LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                 WHERE sp.is_active = 1 AND
                                       sp.content_type = 1 AND
                                       sp.catalog_id IS NULL
                                 ORDER BY sp.rank DESC');

            $video_pages = DB::select('SELECT
                                  sp.id AS static_page_id,
                                  sp.tag_title AS tag,
                                  sp.rank,
                                  CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url,
                                  sp.is_active
                                FROM  static_page_master AS sp
                                WHERE sp.content_type IN(2,3) AND
                                  sp.is_active = 1
                                ORDER BY sp.rank DESC');
            $main_sub_pages = array_merge($image_pages, $video_pages);

            $left_nav_html = $this->getLeftNavigationV2('active', '', $static_page_id);
            $main_template = [
                'left_nav' => $left_nav_html,
                'sub_pages' => $main_sub_pages,
                'page_detail' => $page_detail,
                'header_detail' => $header_detail,
                'sub_detail' => $sub_detail,
                'rating_schema' => $rating_schema,
                'guide_detail' => $guide_detail,
                'app_cta_detail' => $app_cta_detail,
                'guide_steps' => $guide_steps,
                'faqs' => $faqs,
                'image_template_list' => json_encode($image_template_list),
                'video_template_list' => json_encode($video_template_list),
                'API_getTemplatesByCategoryIdV2' => $this->active_link_path.'/api/public/api/getTemplatesByCategoryIdV2',
            ];
            //Get html code for static page
            $html = view('static_page_main_v2', compact('main_template'))->render();

            //Create static page on path
            if (! is_dir($static_page_dir)) {
                mkdir($static_page_dir, 0777, true);
            }

            $handle = fopen($static_page_dir.$file_name, 'w') or exit('Cannot open file');

            fwrite($handle, $html);

            DB::commit();

            (new UserController())->deleteAllRedisKeys('getStaticMainPageByAdmin');

            $response = Response::json(['code' => 200, 'message' => 'Static landing page generated successfully.', 'cause' => '', 'data' => ['result' => $static_page_dir.$file_name]]);

        } catch (Exception $e) {
            (new ImageController())->logs('editStaticMainPageByAdmin', $e);
            //Log::error("editStaticMainPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit static landing page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/editVideoStaticPageByAdmin",
     *        tags={"Admin_StaticPage"},
     *        operationId="editVideoStaticPageByAdmin",
     *        summary="editVideoStaticPageByAdmin",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"static_page_id","content_ids","sub_category_id","sub_category_name","catalog_id","catalog_name","sub_category_path","old_sub_category_path","page_url","old_page_url","page_detail","header_detail","sub_detail"},
     *
     *          @SWG\Property(property="static_page_id",  type="integer", example=7, description=""),
     *          @SWG\Property(property="content_ids",  type="array", example="[{},{},{},{}]", description="",
     *
     *                @SWG\Items(type="integer",example=1),),
     *
     *          @SWG\Property(property="sub_category_id",  type="integer", example=3, description=""),
     *          @SWG\Property(property="sub_category_name",  type="string", example="Instagram Story", description=""),
     *          @SWG\Property(property="catalog_id",  type="integer", example=0, description=""),
     *          @SWG\Property(property="catalog_name",  type=0, example="All", description=""),
     *          @SWG\Property(property="sub_category_path",  type="string", example="Instagram-Story", description=""),
     *          @SWG\Property(property="catalog_path",  type="string", example="", description=""),
     *          @SWG\Property(property="old_sub_category_path",  type="string", example="InstagramStory", description=""),
     *          @SWG\Property(property="page_url",  type="string", example="Instagram-Story", description=""),
     *          @SWG\Property(property="old_page_url",  type="string", example="InstagramStory", description=""),
     *          @SWG\Property(property="page_detail",  type="object", example={"page_title":"page_title","meta":"meta","canonical":"canonical"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="header_detail",  type="object", example={"h1":"Header 1 Description","h2":"Header 2 Description","cta_text":"Card Maker","cta_link":"../../../app/#/editor/37/244"}, description="All parameters in this object are optional"),
     *          @SWG\Property(property="sub_detail",  type="object", example={"h2":"Specification_title","description":"With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."}, description="All parameters in this object are optional"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Static page generated successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} editVideoStaticPageByAdmin editVideoStaticPageByAdmin
     *
     * @apiName editVideoStaticPageByAdmin
     *
     * @apiGroup admin-side
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
     *"static_page_id":37,
     *"content_ids":[1,2,3,4],
     * "tag_title":"Logo1",
     * "search_category":"business,card",
     * "old_page_url":"old_page_url",
     * "page_url": "business1-video",
     * "page_detail": {
     * "page_title": "test",
     * "meta": "test",
     * "canonical": "test"
     * },
     * "header_detail": {
     * "h1": "ALL Instagram-Story Default test page title",
     * "h2": "InstagramStory page title Instagram-Story page title",
     * "cta_text": "Instagram-Story",
     * "cta_link": "../../../app/#/editor/37/244"
     * },
     * "sub_detail": {
     * "h2": "Instagram-Story page title",
     * "description": "With a near extinction to normal flyers, 3D flyers mark a turning point to stand out your business of rest.The 3D flyer templates we mumble around are something that altogether adds a different perspective to your flyer."
     * }
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Static page generated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function editVideoStaticPageByAdmin(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id', 'tag_title', 'search_category', 'old_page_url', 'page_url', 'page_detail', 'header_detail', 'sub_detail'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredArrayParameter(['content_ids'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $tag_title = $request->tag_title;
            $search_category = $request->search_category;
            $page_url = $request->page_url;
            $old_page_url = $request->old_page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $content_ids = $request->content_ids;
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $old_file_dir = $static_page_dir.$old_page_url.'/'.$file_name;
            $old_file_rename = $folder_path.'/'.'index_'.date('Y_m_d_H_i_s').'.html';
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $content_type = isset($request->content_type) ? $request->content_type : 9;
            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                $content_type = 3;
                $editor = 'intro-editor';
            } else {
                $content_type = 2;
                $editor = 'video-editor';
            }
            $active_video_tab = 'active';
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `page name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            if (is_array($content_ids) or count($content_ids) > 0) {
                $content_id = implode(',', $content_ids);
            } else {
                $content_id = '';
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            //Check is it exist in DB
            //      if (($response = (new VerificationController())->checkIsPathAvailable($static_page_id, $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '')
            //        return $response;

            if (file_exists($old_file_dir)) {

                //check sub_category_path and old_sub_category_path same
                if ($old_page_url != $page_url) {
                    //Check new sub category already exist or not
                    if (file_exists($static_page_dir.$page_url)) {
                        return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    if (! rename($static_page_dir.$old_page_url, $static_page_dir.$page_url)) {
                        return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t rename url of sub category path.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                    $file_dir = $static_page_dir.$page_url;
                }
                //        if ($catalog_path != $old_catalog_path) {
                //          if (!rename($static_page_dir . '/' . $sub_category_path . '/' . $old_catalog_path, $static_page_dir . '/' . $sub_category_path . '/' . $catalog_path)) {
                //            return Response::json(array('code' => 201, 'message' => 'Sorry, We couldn\'t rename url of catalog path.', 'cause' => '', 'data' => json_decode("{}")));
                //          }
                //        }
            }
            if (! rename($file_dir, $old_file_rename)) {
                return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t generate this page. Try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();

            DB::update('UPDATE static_page_master
                                      SET page_detail = ?,
                                      header_detail = ?,
                                      sub_detail = ?,
                                      rating_schema=?,
                                      guide_steps=?,
                                      faqs=?,
                                      guide_detail=?,
                                      content_type= ?,
                                      tag_in_url = ?,
                                      tag_title= ?,
                                      search_category = ?,
                                      content_ids = IF(? != "",?,content_ids)
                                      WHERE id = ?', [
                json_encode($page_detail),
                json_encode($header_detail),
                json_encode($sub_detail),
                json_encode($rating_schema),
                json_encode($guide_steps),
                json_encode($faqs),
                json_encode($guide_detail),
                $content_type,
                $page_url,
                $tag_title,
                $search_category,
                $content_id,
                $content_id,
                $static_page_id,
            ]);

            //Similar template pages
            if (count($similar_tags) > 0) {

                //Clear Redis cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                // Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get Similar template page list
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $sub_pages = $this->getSubPages($static_page_id);

            $API_getStaticPageTemplateListById = $active_path.'/api/public/api/getStaticPageTemplateListById';
            $API_getStaticPageTemplateListByTag = $active_path.'/api/public/api/getStaticPageTemplateListByTag';
            $path_of_header_cta_link = $active_path.'/app/#/'.$editor.'/'.$header_cta_link;

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');

            //Get html code for static page's analytics
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigation($static_page_id);

            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'API_getStaticPageTemplateListById' => $API_getStaticPageTemplateListById,
                'API_getStaticPageTemplateListByTag' => $API_getStaticPageTemplateListByTag,
                'static_page_id' => 0,
                'search_category' => $search_category,
                'active_video_tab' => $active_video_tab,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
            ];

            //Get html code for static page
            $html = view('static_page', compact('template'))->render();

            //Create static page on path
            //      Log::info("folder path : ",[$folder_path]);
            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }
            //      Log::info("file path :",[$file_dir]);
            $handle = fopen($file_dir, 'w') or exit('Cannot open file');

            fwrite($handle, $html);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('editVideoStaticPageByAdmin', $e);
            //      Log::error("editVideoStaticPageByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;

    }

    public function editVideoStaticPageByAdminV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id', 'tag_title', 'search_category', 'old_page_url', 'page_url', 'page_detail', 'header_detail', 'sub_detail', 'app_cta_detail'], $request)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateRequiredParameterIsArray(['content_ids', 'template_list'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $tag_title = $request->tag_title;
            $search_category = $request->search_category;
            $page_url = $request->page_url;
            $old_page_url = $request->old_page_url;
            $page_detail = $request->page_detail;
            $header_detail = $request->header_detail;
            $sub_detail = $request->sub_detail;
            $app_cta_detail = $request->app_cta_detail;
            $rating_schema = isset($request->rating_schema) ? $request->rating_schema : null;
            $guide_detail = isset($request->guide_detail) ? $request->guide_detail : null;
            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir.$page_url;
            $file_dir = $folder_path.'/'.$file_name;
            $old_file_dir = $static_page_dir.$old_page_url.'/'.$file_name;
            $old_file_rename = $folder_path.'/'.'index_'.date('Y_m_d_H_i_s').'.html';
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $content_type = $content_type_for_cache = isset($request->content_type) ? $request->content_type : 9;
            if ($content_type == Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                $content_type = 3;
                $editor = 'intro-editor';
            } else {
                $content_type = 2;
                $editor = 'video-editor';
            }
            $active_video_tab = 'active';
            $similar_tags = isset($request->similar_tags) && ! empty($request->similar_tags) ? $request->similar_tags : [];
            $guide_steps = isset($request->guide_steps) && ! empty($request->guide_steps) ? $request->guide_steps : [];
            $faqs = isset($request->faqs) && ! empty($request->faqs) ? $request->faqs : [];
            $userName = '';
            $ratingName = '';
            $ratingDescription = '';
            $ratingValue = '';
            $reviewCount = '';
            $guide_heading = '';
            $guide_btn_text = '';
            $content_ids = implode(',', $request->content_ids);
            $template_list = $request->template_list;

            if (preg_match('/[A-Z]/', $page_url)) {
                return Response::json(['code' => 201, 'message' => 'Please enter characters from a-z,0-9 and - only in `page name in url`.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($guide_steps) > 7) {
                return Response::json(['code' => 201, 'message' => 'You can only add 7 or fewer user guide step.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (count($faqs) > 8) {
                return Response::json(['code' => 201, 'message' => 'You can only add 8 or fewer FAQs.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if (($response = (new VerificationController())->validateFaqs($faqs)) != '') {
                return $response;
            }

            if (($response = (new VerificationController())->validateGuideStep($guide_steps)) != '') {
                return $response;
            }

            if (count($guide_steps) > 0) {
                if (($response = (new VerificationController())->validateRequiredParameter(['guide_heading', 'guide_btn_text'], $guide_detail)) != '') {
                    return $response;
                }

                $guide_heading = $guide_detail->guide_heading;
                $guide_btn_text = $guide_detail->guide_btn_text;
            }

            if (($response = (new VerificationController())->validateRequiredParameter(['page_title', 'meta', 'canonical'], $page_detail)) != '') {
                return $response;
            }

            $page_title = $page_detail->page_title;
            $meta = $page_detail->meta;
            $canonical = $page_detail->canonical;

            if (($response = (new VerificationController())->validateRequiredParameter(['h1', 'h2', 'cta_text', 'cta_link'], $header_detail)) != '') {
                return $response;
            }

            $header_h1 = $header_detail->h1;
            $header_h2 = $header_detail->h2;
            $header_cta_text = $header_detail->cta_text;
            $header_cta_link = $header_detail->cta_link;

            if (($response = (new VerificationController())->validateRequiredParameter(['h2', 'description'], $sub_detail)) != '') {
                return $response;
            }

            $sub_h2 = $sub_detail->h2;
            $sub_description = $sub_detail->description;
            $main_h2 = isset($sub_detail->main_h2) && $sub_detail->main_h2 != '' ? $sub_detail->main_h2 : '';
            $main_description = isset($sub_detail->main_description) && $sub_detail->main_description != '' ? $sub_detail->main_description : '';

            if ($rating_schema) {
                if (($response = (new VerificationController())->validateRequiredParameter(['userName', 'name', 'description', 'ratingValue', 'reviewCount'], $rating_schema)) != '') {
                    $rating_schema = null;
                }

                if ($rating_schema != '') {
                    $userName = $rating_schema->userName;
                    $ratingName = $rating_schema->name;
                    $ratingDescription = $rating_schema->description;
                    $ratingValue = $rating_schema->ratingValue;
                    $reviewCount = $rating_schema->reviewCount;
                }
            }

            //Check is it exist in DB
            //      if (($response = (new VerificationController())->checkIsPathAvailable($static_page_id, $sub_category_path, $catalog_path, $sub_category_id, $catalog_id)) != '')
            //        return $response;

            if (file_exists($old_file_dir)) {

                //check sub_category_path and old_sub_category_path same
                if ($old_page_url != $page_url) {
                    //Check new sub category already exist or not
                    if (file_exists($static_page_dir.$page_url)) {
                        return Response::json(['code' => 201, 'message' => 'This static page already exist having this path : '.$this->static_page_dir.$page_url.'.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    if (! rename($static_page_dir.$old_page_url, $static_page_dir.$page_url)) {
                        return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t rename url of sub category path.', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                    $file_dir = $static_page_dir.$page_url;
                }
                //        if ($catalog_path != $old_catalog_path) {
                //          if (!rename($static_page_dir . '/' . $sub_category_path . '/' . $old_catalog_path, $static_page_dir . '/' . $sub_category_path . '/' . $catalog_path)) {
                //            return Response::json(array('code' => 201, 'message' => 'Sorry, We couldn\'t rename url of catalog path.', 'cause' => '', 'data' => json_decode("{}")));
                //          }
                //        }
            }
            if (! rename($file_dir, $old_file_rename)) {
                return Response::json(['code' => 201, 'message' => 'Sorry, We couldn\'t generate this page. Try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();

            DB::update('UPDATE static_page_master
                                      SET app_cta_detail = ?,
                                      page_detail = ?,
                                      header_detail = ?,
                                      sub_detail = ?,
                                      rating_schema=?,
                                      guide_steps=?,
                                      faqs=?,
                                      guide_detail=?,
                                      content_type= ?,
                                      tag_in_url = ?,
                                      tag_title= ?,
                                      search_category = ?,
                                      content_ids = ?
                                      WHERE id = ?', [
                json_encode($app_cta_detail),
                json_encode($page_detail),
                json_encode($header_detail),
                json_encode($sub_detail),
                json_encode($rating_schema),
                json_encode($guide_steps),
                json_encode($faqs),
                json_encode($guide_detail),
                $content_type,
                $page_url,
                $tag_title,
                $search_category,
                $content_ids,
                $static_page_id,
            ]);

            //Similar template pages
            if (count($similar_tags) > 0) {

                //Clear Redis cache
                Redis::del(Config::get('constant.REDIS_KEY').":getSimilarPages:$static_page_id");

                // Remove duplicate tag_name.
                $similar_tags = array_reverse(array_values(array_column(
                    array_reverse($similar_tags),
                    null,
                    'tag_name'
                )));
                foreach ($similar_tags as $tag) {
                    $this->addSimilarTemplatePage($tag, $static_page_id);
                }
            }

            //get Similar template page list
            $similar_pages = $this->getSimilarPages($static_page_id);

            //get sub page list
            $sub_pages = $this->getSubPages($static_page_id);

            $path_of_header_cta_link = $active_path.'/app/#/'.$editor.'/'.$header_cta_link;

            $API_getTemplatesByCategoryIdV2 = $active_path.'/api/public/api/getTemplatesByCategoryIdV2';

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');

            //Get html code for static page's analytics
            if ($http_host == $live_server) {
                $analytics = view('analytics')->render();
            } else {
                $analytics = '';
            }

            $left_nav_html = $this->getLeftNavigationV2('', 'active', $static_page_id);

            $template = [
                'page_title' => $page_title,
                'meta' => $meta,
                'analytic' => $analytics,
                'canonical' => $canonical,
                'app_cta_detail' => $app_cta_detail,
                'header_h1' => $header_h1,
                'header_h2' => $header_h2,
                'header_cta_text' => $header_cta_text,
                'header_cta_link' => $path_of_header_cta_link,
                'sub_h2' => $sub_h2,
                'sub_description' => $sub_description,
                'main_h2' => $main_h2,
                'main_description' => $main_description,
                'guide_heading' => $guide_heading,
                'guide_btn_text' => $guide_btn_text,
                'ratingName' => $ratingName,
                'userName' => $userName,
                'ratingDescription' => $ratingDescription,
                'ratingValue' => $ratingValue,
                'reviewCount' => $reviewCount,
                'static_page_id' => 0,
                'search_category' => $search_category,
                'active_video_tab' => $active_video_tab,
                'content_type' => $content_type,
                'left_nav' => $left_nav_html,
                'similar_pages' => $similar_pages,
                'sub_pages' => $sub_pages,
                'guide_steps' => $guide_steps,
                'page_faqs' => $faqs,
                'sub_category_id' => '',
                'catalog_id' => '',
                'API_getTemplatesByCategoryIdV2' => $API_getTemplatesByCategoryIdV2,
                'image_template_list' => json_encode([]),
                'video_template_list' => json_encode($template_list),
            ];

            //Get html code for static page
            $html = view('static_page_v2', compact('template'))->render();

            //Create static page on path
            //      Log::info("folder path : ",[$folder_path]);
            if (! is_dir($folder_path)) {
                mkdir($folder_path, 0777, true);
            }
            //      Log::info("file path :",[$file_dir]);
            $handle = fopen($file_dir, 'w') or exit('Cannot open file');

            fwrite($handle, $html);

            DB::commit();

            (new UserController())->deleteAllRedisKeys("getTemplatesByCategoryIdV2:$search_category:$content_type_for_cache");
            $response = Response::json(['code' => 200, 'message' => 'Static page generated successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir.$page_url]]);

        } catch (Exception $e) {
            (new ImageController())->logs('editVideoStaticPageByAdminV2', $e);
            //Log::error("editVideoStaticPageByAdminV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'edit static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/setStaticPageOnTheTopByAdmin",
     *        tags={"Admin_StaticPage"},
     *        operationId="setStaticPageOnTheTopByAdmin",
     *        summary="setStaticPageOnTheTopByAdmin",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"static_page_id"},
     *
     *          @SWG\Property(property="static_page_id",  type="integer", example=7, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Rank set successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} setStaticPageOnTheTopByAdmin setStaticPageOnTheTopByAdmin
     *
     * @apiName setStaticPageOnTheTopByAdmin
     *
     * @apiGroup admin-side
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
     * "static_page_id":6
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Rank set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setStaticPageOnTheTopByAdminBackUp(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $create_time = date('Y-m-d H:i:s');

            $is_main_page = DB::select('SELECT
                                    id,
                                    static_page_sub_category_id
                                  FROM
                                   static_page_master
                                  WHERE
                                    id = ? AND
                                    catalog_id IS NULL AND
                                    content_type = 1', [$static_page_id]);

            DB::update('UPDATE static_page_master
                                      SET update_time = ?
                                      WHERE id = ?', [
                $create_time,
                $static_page_id,
            ]);
            if (count($is_main_page) > 0) {
                $id = $is_main_page[0]->static_page_sub_category_id;
                DB::update('UPDATE static_page_sub_category_master
                                      SET update_time = ?
                                      WHERE id = ?', [
                    $create_time,
                    $id,
                ]);
            }

            $response = Response::json(['code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setStaticPageOnTheTopByAdmin', $e);
            //      Log::error("setStaticPageOnTheTopByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set static page sequence.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function setStaticPageOnTheTopByAdmin(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;

            $oldest_static_page = DB::select('SELECT
                                            update_time
                                        FROM
                                            static_page_master
                                        WHERE
                                            sub_category_id = (SELECT
                                                                  sub_category_id
                                                                FROM
                                                                  static_page_master
                                                                WHERE
                                                                  id = ? AND
                                                                  content_type = 1 )
                                        ORDER BY update_time ASC LIMIT 1', [$static_page_id]);

            if ($oldest_static_page) {
                $update_time = date('Y-m-d H:i:s', strtotime($oldest_static_page[0]->update_time) - 60);

                $is_main_page = DB::select('SELECT
                                      id,
                                      static_page_sub_category_id
                                    FROM
                                     static_page_master
                                    WHERE
                                      id = ? AND
                                      catalog_id IS NULL AND
                                      content_type = 1', [$static_page_id]);

                DB::update('UPDATE static_page_master
                    SET update_time = ?
                    WHERE id = ?', [$update_time, $static_page_id]);

                if (count($is_main_page) > 0) {
                    $id = $is_main_page[0]->static_page_sub_category_id;
                    DB::update('UPDATE static_page_sub_category_master
                      SET update_time = ?
                      WHERE id = ?', [$update_time, $id]);
                }

                $response = Response::json(['code' => 200, 'message' => 'Rank set successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {
                Log::error('setStaticPageOnTheTopByAdmin : Oldest static page not found', ['static_page_id' => $static_page_id]);
                $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set static page sequence.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('setStaticPageOnTheTopByAdmin', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set static page sequence.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/setStatusOfStaticPage",
     *        tags={"Admin_StaticPage"},
     *        operationId="setStatusOfStaticPage",
     *        summary="setStatusOfStaticPage",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"static_page_id","status"},
     *
     *          @SWG\Property(property="static_page_id",  type="integer", example=7, description=""),
     *          @SWG\Property(property="status",  type="integer", example=0, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Status set successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} setStatusOfStaticPage setStatusOfStaticPage
     *
     * @apiName setStatusOfStaticPage
     *
     * @apiGroup admin-side
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
     * "static_page_id":6,
     * "status":0
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Status set successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function setStatusOfStaticPage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());

            if (($response = (new VerificationController())->validateRequiredParameter(['static_page_id', 'status'], $request)) != '') {
                return $response;
            }

            $static_page_id = $request->static_page_id;
            $status = $request->status;

            DB::update('UPDATE static_page_master
                                      SET is_active = ?
                                      WHERE id = ?', [
                $status,
                $static_page_id,
            ]);

            $response = Response::json(['code' => 200, 'message' => 'Status set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setStatusOfStaticPage', $e);
            //      Log::error("setStatusOfStaticPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set status of static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} getAllSubCategoryListForStaticPage getAllSubCategoryListForStaticPage
     *
     * @apiName getAllSubCategoryListForStaticPage
     *
     * @apiGroup admin-side
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
     * "sub_category_id": 2, //optional
     * "is_featured" :1 //optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Sub categories are fetched successfully.",
     * "cause": "",
     * "data": {
     * "result": {
     * "sub_categories": [
     * {
     * "sub_category_id": 1,
     * "sub_category_name": "Snapchat Geo Filter",
     * "update_time": "2019-06-10 08:26:32"
     * },
     * {
     * "sub_category_id": 2,
     * "sub_category_name": "Facebook Canvas",
     * "update_time": "2019-06-10 08:01:25"
     * }
     * ],
     * "catalogs": [
     * {
     * "catalog_id": 60,
     * "catalog_name": "Test",
     * "sub_category_id": 1,
     * "update_time": "2019-02-01 02:03:11"
     * }
     * ]
     * }
     * }
     * }
     */
    public function getAllSubCategoryListForStaticPage(Request $request)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());

            $this->sub_category_id = isset($request->sub_category_id) ? $request->sub_category_id : 0;
            $this->category_id = isset($request->category_id) && ($request->category_id) ? $request->category_id : 0;
            $this->is_featured = isset($request->is_featured) ? $request->is_featured : 1; //to identify catalog is_featured or not
            if ($this->category_id) {
                $this->db_category_id = "AND scm.category_id = $this->category_id";
            } else {
                $this->db_category_id = '';
            }

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllSubCategoryListForStaticPage$this->sub_category_id:$this->is_featured")) {
                $result = Cache::rememberforever("getAllSubCategoryListForStaticPage$this->sub_category_id:$this->is_featured", function () {

                    if ($this->sub_category_id !== 0) {

                        $sub_categories = DB::select('SELECT
                                            DISTINCT scm.uuid AS sub_category_id,
                                            scm.sub_category_name,
                                            coalesce((SELECT sub_category_name FROM static_page_sub_category_master WHERE sub_category_id = scm.id LIMIT 1),"") name_of_main_page,
                                            coalesce((SELECT sub_category_path FROM static_page_sub_category_master WHERE sub_category_id = scm.id LIMIT 1) ,"") main_page_url
                                          FROM sub_category_master scm
                                            LEFT JOIN sub_category_catalog AS scc ON scm.id=scc.sub_category_id AND scc.is_active=1
                                          WHERE
                                            scm.is_active = 1 AND
                                            scm.is_featured = 1
                                            '.$this->db_category_id.'
                                          ORDER BY scm.sub_category_name ASC');

                        $catalogs = DB::select('SELECT
                                          DISTINCT cm.uuid AS catalog_id,
                                          cm.name AS catalog_name,
                                          scm.uuid AS sub_category_id
                                        FROM sub_category_catalog AS scc
                                          JOIN catalog_master AS cm
                                            ON cm.id=scc.catalog_id AND
                                               cm.is_active=1 AND
                                               cm.is_featured = ?,
                                         sub_category_master AS scm
                                        WHERE
                                          scm.id = scc.sub_category_id AND
                                          scc.is_active = 1 AND
                                          scm.uuid = ?
                                        ORDER BY cm.name ASC', [$this->is_featured, $this->sub_category_id]);
                    } else {
                        $sub_categories = DB::select('SELECT
                                            DISTINCT scm.uuid AS sub_category_id,
                                            scm.sub_category_name,
                                            coalesce((SELECT sub_category_path FROM static_page_sub_category_master WHERE sub_category_id = scm.id LIMIT 1),"") name_of_main_page,
                                            coalesce((SELECT sub_category_path FROM static_page_sub_category_master WHERE sub_category_id = scm.id LIMIT 1) ,"") main_page_url
                                          FROM sub_category_master scm
                                            LEFT JOIN sub_category_catalog AS scc ON scm.id=scc.sub_category_id AND scc.is_active=1
                                          WHERE
                                            scm.is_active = 1 AND
                                            scm.is_featured = 1
                                            '.$this->db_category_id.'
                                          ORDER BY scm.sub_category_name ASC');
                        //            Log::info('sub category list : ',[$sub_categories]);
                        $catalogs = DB::select('SELECT
                                          DISTINCT  cm.uuid AS catalog_id,
                                          cm.name AS catalog_name,
                                          scm.uuid AS sub_category_id,
                                          cm.update_time
                                        FROM sub_category_catalog AS scc
                                          JOIN catalog_master AS cm
                                            ON cm.id=scc.catalog_id AND
                                               cm.is_active=1 AND
                                               cm.is_featured = ?,
                                        sub_category_master as scm
                                        WHERE
                                          scm.id =  scc.sub_category_id AND
                                          scc.is_active = 1 AND
                                          scm.uuid = ?
                                        ORDER BY cm.name ASC', [$this->is_featured, $sub_categories[0]->sub_category_id]);
                    }

                    return ['sub_categories' => $sub_categories, 'catalogs' => $catalogs];

                });

            }

            $redis_result = Cache::get("getAllSubCategoryListForStaticPage$this->sub_category_id:$this->is_featured");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Sub categories are fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('getAllSubCategoryListForStaticPage', $e);
            //      Log::error("getAllSubCategoryListForStaticPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').' get all sub category for static page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getAllTemplateListForMainStaticPage(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_type', 'page', 'item_count'], $request)) != '') {
                return $response;
            }

            $this->content_type = $request->content_type;
            $this->item_count = $request->item_count;
            $this->page = $request->page;
            $this->offset = ($this->page - 1) * $this->item_count;

            $redis_result = Cache::rememberforever("getAllTemplateListForMainStaticPage:$this->page:$this->item_count:$this->content_type", function () {

                if ($this->content_type != Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO')) {
                    return Response::json(['code' => 201, 'message' => 'Something went wrong', 'cause' => '', 'data' => json_decode('{}')]);
                } else {
                    $total_row_result = DB::select('SELECT
                                  count(*) AS total
                               FROM
                                  content_master AS cm
                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                  JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                WHERE
                                  cm.is_active = 1 AND
                                  cm.content_type IN ('.$this->content_type.')
                                GROUP BY cm.id ');
                    $total_row = count($total_row_result);
                    DB::statement("SET sql_mode = '' ");
                    $template_list = DB::select('SELECT
                                  cm.id AS content_id,
                                  cm.uuid AS content_uuid,
                                  ctm.uuid AS catalog_id,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                  IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                  IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                  cm.content_type,
                                  COALESCE(cm.is_featured,"") AS is_featured,
                                  COALESCE(cm.is_free,0) AS is_free,
                                  COALESCE(cm.is_portrait,0) AS is_portrait,
                                  COALESCE(cm.height,0) AS height,
                                  COALESCE(cm.width,0) AS width,
                                  COALESCE(cm.color_value,"") AS color_value,
                                  cm.update_time,
                                  REPLACE(ctm.name,"\'","") AS catalog_name,
                                  scm.uuid AS sub_category_id,
                                  REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                FROM
                                  content_master AS cm
                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                  JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                WHERE
                                  cm.is_active = 1 AND
                                  cm.content_type IN ('.$this->content_type.')
                                GROUP BY cm.id
                                  ORDER BY cm.update_time DESC limit ?, ?', [$this->offset, $this->item_count]);

                }
                $is_next_page = ($total_row > ($this->offset + $this->item_count)) ? true : false;

                return ['total_record' => $total_row, 'is_next_page' => $is_next_page, 'content_list' => $template_list];

            });

            $redis_result = Cache::get("getAllTemplateListForMainStaticPage:$this->page:$this->item_count:$this->content_type");

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'All templates are fetched successfully.', 'cause' => '', 'data' => $redis_result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getAllTemplateListForMainStaticPage', $e);
            //Log::error("getAllTemplateListForMainStaticPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static main page template list admin', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    /* generate what's new static page*/
    /**
     * - Admin_StaticPage ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getWhatsNewContent",
     *        tags={"Admin_StaticPage"},
     *        operationId="getWhatsNewContent",
     *        summary="getWhatsNewContent",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Get Whats's new content successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} getWhatsNewContent getWhatsNewContent
     *
     * @apiName getWhatsNewContent
     *
     * @apiGroup admin-side
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Get Whats's new content successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getWhatsNewContent(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            if (! Cache::has("Config::get('constant.REDIS_KEY'):getWhatsNewContent")) {
                $result = Cache::rememberforever('getWhatsNewContent', function () {

                    $html_data = DB::select('SELECT json_html_block FROM whats_new_html WHERE page_id = 1');
                    if (count($html_data) > 0) {
                        $json_html_block = $html_data[0]->json_html_block;
                    } else {
                        $json_html_block = '[]';
                    }
                    $files = DB::select('SELECT
                          file_type,
                          id,
                          IF(file!= "" AND file_type =1,CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",file),CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",file)) as file_url
                          FROM whats_new_media
                          ORDER BY id desc');
                    $result_array = [
                        'content_json' => $json_html_block,
                        'files' => $files,
                    ];

                    return $result_array;
                });
            }

            $redis_result = Cache::get('getWhatsNewContent');

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Get What\'s new content successfully.', 'cause' => '', 'data' => ['result' => $redis_result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('getWhatsNewContent', $e);
            //      Log::error("getWhatsNewContent : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get Whats\'s new content.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    /**
     * - Admin_StaticPage ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/uploadMediaForWhatsNew",
     *        tags={"Admin_StaticPage"},
     *        operationId="uploadMediaForWhatsNew",
     *        summary="uploadMediaForWhatsNew",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"file_type"},
     *
     *          @SWG\Property(property="file_type",  type="integer", example=1, description="1=image,2=video"),
     *        ),
     *      ),
     *
     *   @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="upload file",
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
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Media uploaded successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} uploadMediaForWhatsNew uploadMediaForWhatsNew
     *
     * @apiName uploadMediaForWhatsNew
     *
     * @apiGroup admin-side
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
     * "file_type":1 //compulsory
     * }
     * "file":1.mp4
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Media uploaded successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function uploadMediaForWhatsNew(Request $request_body)
    {

        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            if (! $request_body->has('request_data')) {
                return Response::json(['code' => 201, 'message' => 'Required field request_data is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $request = json_decode($request_body->input('request_data'));
            if (($response = (new VerificationController())->validateRequiredParameter(['file_type'], $request)) != '') {
                return $response;
            }

            $file_type = isset($request->file_type) ? $request->file_type : 1; // 1=image 2=video
            $create_time = date('Y-m-d H:i:s');

            if (! $request_body->hasFile('file')) {
                return Response::json(['code' => 201, 'message' => 'Required field file is missing or empty.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $file = Input::file('file');

                if ($file_type == 1) {
                    /* Here we passed value following parameters as 0 bcs we use common validation for advertise images
         * category = 0
         * is_featured = 0
         * is_catalog = 0
         * */
                    if (($response = (new ImageController())->verifyImage($file, 0, 0, 0)) != '') {
                        return $response;
                    }

                    $file_name = (new ImageController())->generateNewFileName('whats_new_image', $file);
                    (new ImageController())->saveOriginalImage($file_name);
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveImageInToS3($file_name);
                    }
                } else {
                    if (($response = (new ImageController())->verifyVideo($file)) != '') {
                        return $response;
                    }

                    $file_name = (new ImageController())->generateNewFileName('whats_new_video', $file);
                    (new ImageController())->saveVideo($file_name, '../..'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'), $file);
                    if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                        (new ImageController())->saveVideoInToS3($file_name);
                    }
                }
            }

            DB::beginTransaction();
            DB::insert('INSERT into whats_new_media (
                              file,
                              file_type,
                              is_active,
                              create_time)
                              VALUES(?, ?, ?, ?)', [$file_name, $file_type, 1, $create_time]);
            DB::commit();

            $files = DB::select('SELECT
                          file_type,
                          id,
                          IF(file!= "" AND file_type =1,CONCAT("'.Config::get('constant.ORIGINAL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",file),CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",file)) as file_url
                          FROM whats_new_media
                          ORDER BY id desc');
            if (count($files) == 0) {
                $files = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Media uploaded successfully.', 'cause' => '', 'data' => ['result' => $files]]);

        } catch (Exception $e) {
            (new ImageController())->logs('uploadMediaForWhatsNew', $e);
            //      Log::error("uploadMediaForWhatsNew : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'media uploaded.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    /**
     * - Admin_StaticPage------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/saveWhatNewHtml",
     *        tags={"Admin_StaticPage"},
     *        operationId="saveWhatNewHtml",
     *        summary="saveWhatNewHtml",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"html_block","content_json"},
     *
     *          @SWG\Property(property="html_block",  type="string", example="<div></div>", description=""),
     *           @SWG\Property(property="content_json",  type="string", example="<div></div>", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Save what's new successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} saveWhatNewHtml saveWhatNewHtml
     *
     * @apiName saveWhatNewHtml
     *
     * @apiGroup admin-side
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
     * "html_block":"<div></div>
     * "content_json":"<div></div> //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Save what's new successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function saveWhatNewHtml(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['html_block', 'content_json'], $request)) != '') {
                return $response;
            }

            $html_block = $request->html_block;
            $json_html_block = $request->content_json;
            $file_name = 'index.html';
            $static_page_dir = '../..'.Config::get('constant.WHATS_NEW_STATIC_PAGE_DIRECTORY');
            $folder_path = $static_page_dir;
            $file_dir = $folder_path.$file_name;
            $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $active_path.Config::get('constant.WHATS_NEW_STATIC_PAGE_DIRECTORY');
            $create_time = date('Y-m-d H:i:s');

            $page_html = DB::select('SELECT id FROM whats_new_html WHERE page_id = 1');
            DB::beginTransaction();
            if (count($page_html) == 0) {
                DB::insert('INSERT into whats_new_html (
                              html_block,
                              json_html_block,
                              page_id,
                              create_time)
                              VALUES(?, ?, ?, ?)', [$html_block, $json_html_block, 1, $create_time]);
            } else {
                DB::update('UPDATE whats_new_html set html_block= ?,json_html_block=? where page_id= 1', [$html_block, $json_html_block]);
            }

            // To render data directly to a file
            /*$html_block =DB::select('SELECT html_block FROM whats_new_html WHERE page_id = 1');
            $html_block =$html_block[0]->html_block;

            $html = view('whats_new', compact('html_block'))->render();

            if (!is_dir($folder_path)) {
              mkdir($folder_path, 0777, true);
            }
//      Log::info('whats new index :',[$file_dir]);
            $handle = fopen($file_dir, 'w');

            fwrite($handle, $html);*/

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Save what\'s new successfully.', 'cause' => '', 'data' => ['result' => $this->static_page_dir]]);

        } catch (Exception $e) {
            (new ImageController())->logs('saveWhatNewHtml', $e);
            //      Log::error("saveWhatNewHtml : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'save what\'s new.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    /**
     * - Admin_StaticPage ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/deleteWhatsNewMedia",
     *        tags={"Admin_StaticPage"},
     *        operationId="deleteWhatsNewMedia",
     *        summary="deleteWhatsNewMedia",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          required={"id"},
     *
     *          @SWG\Property(property="file_type",  type="integer", example=2, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Delete what's new media successfully","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} deleteWhatsNewMedia deleteWhatsNewMedia
     *
     * @apiName deleteWhatsNewMedia
     *
     * @apiGroup admin-side
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
     * "id":1 //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Delete what's new media successfully",
     * "cause": "",
     * "data": {}
     * }
     */
    public function deleteWhatsNewMedia(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['id'], $request)) != '') {
                return $response;
            }

            $id = $request->id;

            $media = DB::select('SELECT file_type,file FROM whats_new_media WHERE id = ?', [$id]);
            if (count($media) == 0) {
                return Response::json(['code' => 201, 'message' => 'Media file does not found.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $file_type = $media[0]->file_type;
            $file_name = $media[0]->file;
            DB::beginTransaction();
            $res = DB::delete('DELETE from whats_new_media where id = ?', [$id]);
            DB::delete('DELETE FROM image_details WHERE name = ? ', [$file_name]);

            DB::commit();

            if ($res) {
                if ($file_type == 1) {
                    (new ImageController())->unlinkFileFromLocalStorage($file_name, Config::get('constant.ORIGINAL_IMAGES_DIRECTORY'));
                    (new ImageController())->deleteObjectFromS3($file_name, 'original');
                } else {
                    (new ImageController())->unlinkFileFromLocalStorage($file_name, Config::get('constant.ORIGINAL_VIDEO_DIRECTORY'));
                    (new ImageController())->deleteObjectFromS3($file_name, 'video');
                }
            }
            $response = Response::json(['code' => 200, 'message' => 'Delete what\'s new media successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('deleteWhatsNewMedia', $e);
            //      Log::error("deleteWhatsNewMedia : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete what\'s new media.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    /**
     * - Admin_StaticPage ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getWhatsNewHtmlBlocks",
     *        tags={"Admin_StaticPage"},
     *        operationId="getWhatsNewHtmlBlocks",
     *        summary="Get whats new html blocks",
     *        produces={"application/json"},
     *
     *  @SWG\Parameter(
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
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Get Whats's new content successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} getWhatsNewHtmlBlocks getWhatsNewHtmlBlocks
     *
     * @apiName getWhatsNewHtmlBlocks
     *
     * @apiGroup admin-side
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     *  Key: Authorization
     *  Value: Bearer token
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Get Whats's new html blocks successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getWhatsNewHtmlBlocks(Request $request_body)
    {
        try {

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getWhatsNewHtmlBlocks")) {
                $result = Cache::rememberforever('getWhatsNewHtmlBlocks', function () {

                    $html_data = DB::select('SELECT html_block FROM whats_new_html WHERE page_id = 1');
                    if (count($html_data) > 0) {
                        $html_block = $html_data[0]->html_block;
                    } else {
                        $html_block = '[]';
                    }

                    return $html_block;
                });
            }

            $redis_result = Cache::get('getWhatsNewHtmlBlocks');

            if (! $redis_result) {
                $redis_result = [];
            }

            $response = Response::json(['code' => 200, 'message' => 'Get Whats\'s new html blocks successfully.', 'cause' => '', 'data' => ['html_block' => $redis_result]]);

        } catch (Exception $e) {
            (new ImageController())->logs('getWhatsNewHtmlBlocks', $e);
            //      Log::error("getWhatsNewHtmlBlocks : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get Whats\'s new blocks.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();

        }

        return $response;
    }

    public function getLeftNavigation($static_page_id = '')
    {
        try {
            $this->page_id = $static_page_id;
            $this->file_name = 'sidebar.html';
            $this->sidebar_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->file_dir = $this->sidebar_page_dir.'/'.$this->file_name;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getLeftNavigation:$this->page_id")) {
                $result = Cache::rememberforever("getLeftNavigation:$this->page_id", function () {
                    $this->is_active = 1;
                    DB::statement("SET sql_mode = '' ");
                    $image_pages = DB::select('SELECT
                                              spsb.id,
                                              spsb.sub_category_name,
                                              CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/")AS page_url
                                              FROM static_page_sub_category_master AS spsb
                                              LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                              WHERE sp.is_active = ? AND
                                                    sp.content_type = 1
                                              GROUP BY spsb.id
                                              ORDER BY sp.rank DESC', [$this->is_active]);
                    foreach ($image_pages as $key) {

                        $image_sub_pages = DB::select('SELECT
                                          sp.id AS static_page_id,
                                          spsb.sub_category_name,
                                          coalesce(sp.catalog_name,"") AS catalog_name,
                                          spsb.sub_category_path,
                                          coalesce(sp.catalog_path,"") AS catalog_path,
                                          CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path,"/") as page_url,
                                          sp.is_active
                                          FROM static_page_sub_category_master AS spsb
                                          LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                          WHERE sp.static_page_sub_category_id = ? AND
                                                sp.catalog_id IS NOT NULL AND
                                                sp.is_active = ? AND
                                                sp.content_type = 1
                                          ORDER BY sp.update_time DESC', [$key->id, $this->is_active]);
                        $key->sub_page = $image_sub_pages;
                    }
                    $video_pages = DB::select('SELECT
                              sp.id AS static_page_id,
                              sp.tag_title,
                              sp.search_category,
                              sp.content_type,
                              CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url,
                              sp.is_active
                              FROM  static_page_master AS sp
                              WHERE sp.content_type IN(2,3) AND
                              sp.is_active = ?
                              ORDER BY sp.rank DESC', [$this->is_active]);

                    $all_pages = ['image_pages' => $image_pages, 'video_pages' => $video_pages];
                    $left_nav_html = view('sidebar', compact('all_pages'))->render();
                    //          $handle = fopen($this->file_dir, 'w');
                    //          fwrite($handle, $html);
                    //          $file_path  = $this->static_page_dir.$this->file_name;
                    //          $left_nav_html= file_get_contents($file_path);
                    return $left_nav_html;
                });
            }
            $response = Cache::get("getLeftNavigation:$this->page_id");

        } catch (Exception $e) {
            Log::error('getLeftNavigation : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get left navigation bar.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;
    }

    public function getLeftNavigationV2($image_class_name = '', $video_class_name = '', $static_page_id = '')
    {
        try {
            $this->page_id = $static_page_id;
            $this->image_class_name = $image_class_name;
            $this->video_class_name = $video_class_name;
            $this->file_name = 'sidebar.html';
            $this->sidebar_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->file_dir = $this->sidebar_page_dir.'/'.$this->file_name;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->is_active = 1;

            $response = Cache::rememberforever("getLeftNavigationV2:$this->page_id:$this->image_class_name:$this->video_class_name", function () {

                DB::statement("SET sql_mode = '' ");
                $image_pages = DB::select('SELECT
                                                  spsb.id,
                                                  spsb.sub_category_name,
                                                  CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/") AS page_url
                                            FROM static_page_sub_category_master AS spsb
                                                LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                            WHERE sp.is_active = ? AND
                                                sp.content_type = 1
                                                GROUP BY spsb.id
                                            ORDER BY sp.rank DESC', [$this->is_active]);

                foreach ($image_pages as $key) {

                    $image_sub_pages = DB::select('SELECT
                                      sp.id AS static_page_id,
                                      spsb.sub_category_name,
                                      coalesce(sp.catalog_name,"") AS catalog_name,
                                      spsb.sub_category_path,
                                      coalesce(sp.catalog_path,"") AS catalog_path,
                                      CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path,"/") as page_url,
                                      sp.is_active
                                      FROM static_page_sub_category_master AS spsb
                                      LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                      WHERE sp.static_page_sub_category_id = ? AND
                                            sp.catalog_id IS NOT NULL AND
                                            sp.is_active = ? AND
                                            sp.content_type = 1
                                      ORDER BY sp.update_time ASC', [$key->id, $this->is_active]);
                    $key->sub_page = $image_sub_pages;
                }

                $video_pages = DB::select('SELECT
                          sp.id AS static_page_id,
                          sp.tag_title,
                          sp.search_category,
                          sp.content_type,
                          CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url,
                          sp.is_active
                          FROM  static_page_master AS sp
                          WHERE sp.content_type IN(2,3) AND
                          sp.is_active = ?
                          ORDER BY sp.rank DESC', [$this->is_active]);

                $all_pages = ['image_pages' => $image_pages, 'video_pages' => $video_pages, 'image_class_name' => $this->image_class_name, 'video_class_name' => $this->video_class_name];
                $left_nav_html = view('sidebar_v2', compact('all_pages'))->render();
                //$handle = fopen($this->file_dir, 'w');
                //fwrite($handle, $html);
                //$file_path  = $this->static_page_dir.$this->file_name;
                //$left_nav_html= file_get_contents($file_path);
                return $left_nav_html;
            });

        } catch (Exception $e) {
            Log::error('getLeftNavigationV2 : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get left navigation bar.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function generateAllStaticPage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->cache = 0;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            $this->left_nav_html = $this->getLeftNavigation();

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $this->analytics = view('analytics')->render();
            } else {
                $this->analytics = '';
            }

            /*Update left navigation in main html file */
            $image_pages = DB::select('SELECT
                                   spsb.id,
                                   sp.rank,
                                   spsb.sub_category_name AS tag,
                                   CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/") AS page_url
                                 FROM static_page_sub_category_master AS spsb
                                 LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                 WHERE sp.is_active = 1 AND
                                       sp.content_type = 1 AND
                                       sp.catalog_id IS NULL
                                 ORDER BY sp.rank DESC');
            $video_pages = DB::select('SELECT
                                  sp.id AS static_page_id,
                                  sp.tag_title AS tag,
                                  sp.rank,
                                  CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url,
                                  sp.is_active
                                FROM  static_page_master AS sp
                                WHERE sp.content_type IN(2,3) AND
                                  sp.is_active = 1
                                ORDER BY sp.rank DESC');

            $main_sub_pages = array_merge($image_pages, $video_pages);

            $main_static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY').'index.html';
            $main_template = [
                'left_nav' => $this->left_nav_html,
                'sub_pages' => $main_sub_pages,
            ];

            $main_html = view('static_page_main', compact('main_template'))->render();

            $main_handle = fopen($main_static_page_dir, 'w');
            fwrite($main_handle, $main_html);

            if (! Cache::has("Config::get('constant.REDIS_KEY'):generateAllStaticPage")) {
                $result = Cache::rememberforever('generateAllStaticPage', function () {
                    $this->cache = 1;
                    $is_active = 1;
                    $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
                    $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');

                    $file_name = 'index.html';

                    $pages = DB::select('SELECT
                                sp.id AS static_page_id,
                                sp.tag_title,
                                sp.search_category,
                                sp.content_type,
                                spm.sub_category_path,
                                sp.static_page_sub_category_id,
                                sp.catalog_name,
                                sp.catalog_path,
                                sp.page_detail,
                                sp.header_detail,
                                sp.sub_detail,
                                sp.guide_steps,
                                sp.faqs,
                                sp.guide_detail,
                                sp.rating_schema,
                                sp.tag_in_url
                              FROM
                                 static_page_master AS sp
                              LEFT JOIN
                                 static_page_sub_category_master AS spm
                              ON
                                 sp.static_page_sub_category_id = spm.id
                              WHERE
                                  sp.is_active = ?
                              ORDER BY sp.update_time DESC', [$is_active]);

                    foreach ($pages as $row) {

                        $page_detail = json_decode($row->page_detail);
                        $header_detail = json_decode($row->header_detail);
                        $sub_detail = json_decode($row->sub_detail);
                        $rating_schema = json_decode($row->rating_schema);
                        $guide_detail = json_decode($row->guide_detail);
                        $guide_steps = json_decode($row->guide_steps);
                        $faqs = json_decode($row->faqs);

                        if ($guide_steps == null) {
                            $guide_steps = [];
                        }

                        if ($faqs == null) {
                            $faqs = [];
                        }

                        $page_url = $row->sub_category_path;
                        $folder_path = $static_page_dir.$row->tag_in_url;

                        /*Some page which are no need to generate again so we ignore this page when generate all pages
                        if($folder_path == "../../templates/gaming-intro-maker"){
                          continue;
                        }*/

                        $static_page_id = 0;
                        if ($row->content_type == 3) {
                            $editor = 'intro-editor';
                        } elseif ($row->content_type == 2) {
                            $editor = 'video-editor';
                        } else {
                            $editor = 'editor';
                            $static_page_id = $row->static_page_id;
                            if ($row->catalog_path) {
                                $folder_path = $static_page_dir.$page_url.'/'.$row->catalog_path;
                            } else {
                                $folder_path = $static_page_dir.$page_url;
                            }
                        }
                        $file_dir = $folder_path.'/'.$file_name;

                        $page_title = $page_detail->page_title;
                        $meta = $page_detail->meta;
                        $canonical = $page_detail->canonical;
                        $header_h1 = $header_detail->h1;
                        $header_h2 = $header_detail->h2;
                        $header_cta_text = $header_detail->cta_text;
                        $header_cta_link = $header_detail->cta_link;
                        $sub_h2 = $sub_detail->h2;
                        $sub_description = $sub_detail->description;
                        $main_h2 = isset($sub_detail->main_h2) ? $sub_detail->main_h2 : '';
                        $main_description = isset($sub_detail->main_description) ? $sub_detail->main_description : '';
                        $ratingName = isset($rating_schema->name) ? $rating_schema->name : '';
                        $userName = isset($rating_schema->userName) ? $rating_schema->userName : '';
                        $ratingDescription = isset($rating_schema->description) ? $rating_schema->description : '';
                        $ratingValue = isset($rating_schema->ratingValue) ? $rating_schema->ratingValue : '';
                        $reviewCount = isset($rating_schema->reviewCount) ? $rating_schema->reviewCount : '';
                        $guide_heading = isset($guide_detail->guide_heading) ? $guide_detail->guide_heading : '';
                        $guide_btn_text = isset($guide_detail->guide_btn_text) ? $guide_detail->guide_btn_text : '';

                        //get Similar template page list
                        $similar_pages = $this->getSimilarPages($static_page_id);

                        //get sub page list
                        if ($row->content_type == 1) {
                            $sub_pages = $this->getSubPages($row->static_page_sub_category_id, 1);
                        } else {
                            $sub_pages = $this->getSubPages($row->static_page_id);
                        }
                        $API_getStaticPageTemplateListById = $active_path.'/api/public/api/getStaticPageTemplateListById';
                        $API_getStaticPageTemplateListByTag = $active_path.'/api/public/api/getStaticPageTemplateListByTag';
                        $path_of_header_cta_link = $active_path.'/app/#/'.$editor.'/'.$header_cta_link;

                        $template = [
                            'page_title' => $page_title,
                            'meta' => $meta,
                            'analytic' => $this->analytics,
                            'canonical' => $canonical,
                            'header_h1' => $header_h1,
                            'header_h2' => $header_h2,
                            'header_cta_text' => $header_cta_text,
                            'header_cta_link' => $path_of_header_cta_link,
                            'sub_h2' => $sub_h2,
                            'sub_description' => $sub_description,
                            'main_h2' => $main_h2,
                            'main_description' => $main_description,
                            'guide_heading' => $guide_heading,
                            'guide_btn_text' => $guide_btn_text,
                            'ratingName' => $ratingName,
                            'userName' => $userName,
                            'ratingDescription' => $ratingDescription,
                            'ratingValue' => $ratingValue,
                            'reviewCount' => $reviewCount,
                            'API_getStaticPageTemplateListById' => $API_getStaticPageTemplateListById,
                            'API_getStaticPageTemplateListByTag' => $API_getStaticPageTemplateListByTag,
                            'static_page_id' => $static_page_id,
                            'search_category' => $row->search_category,
                            'active_image_tab' => '',
                            'content_type' => $row->content_type,
                            'left_nav' => $this->left_nav_html,
                            'similar_pages' => $similar_pages,
                            'sub_pages' => $sub_pages,
                            'guide_steps' => $guide_steps,
                            'page_faqs' => $faqs,
                        ];

                        $html = view('static_page', compact('template'))->render();

                        if (! is_dir($folder_path)) {
                            mkdir($folder_path, 0777, true);
                        }
                        $handle = fopen($file_dir, 'w');

                        fwrite($handle, $html);

                        //            if ($row->content_type == 1) {
                        //              $folder_path = $static_page_dir . $page_url;
                        //              $file_dir = $folder_path . '/' . $file_name;
                        //              if (!is_dir($folder_path)) {
                        //                mkdir($folder_path, 0777, true);
                        //              }
                        //              $handle = fopen($file_dir, 'w');
                        //
                        //              fwrite($handle, $html);
                        //            }
                    }

                    return count($pages);
                });
            }
            $page_count = Cache::get('generateAllStaticPage');
            if ($this->cache) {
                $result = ['generated_pages' => $page_count];
                $message = 'Static pages generated successfully.';
            } else {
                $result = ['generated_pages' => 0];
                $message = 'There are no any changes for generate pages.';
            }

            $response = Response::json(['code' => 200, 'message' => $message, 'cause' => '', 'data' => $result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('generateAllStaticPage', $e);
            //      Log::error("generateAllStaticPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    public function generateAllStaticPageV2(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->cache = 0;
            $this->active_link_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = $this->active_link_path.Config::get('constant.STATIC_PAGE_DIRECTORY');

            $this->left_nav_html = $this->getLeftNavigationV2('active', '');
            $this->left_video_nav_html = $this->getLeftNavigationV2('', 'active');

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $this->analytics = view('analytics')->render();
            } else {
                $this->analytics = '';
            }

            /*Update left navigation in main html file */
            $image_pages = DB::select('SELECT
                                   spsb.id,
                                   sp.rank,
                                   spsb.sub_category_name AS tag,
                                   CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/") AS page_url
                                 FROM static_page_sub_category_master AS spsb
                                 LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                 WHERE sp.is_active = 1 AND
                                       sp.content_type = 1 AND
                                       sp.catalog_id IS NULL
                                 ORDER BY sp.rank DESC');

            $video_pages = DB::select('SELECT
                                  sp.id AS static_page_id,
                                  sp.tag_title AS tag,
                                  sp.rank,
                                  CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url,
                                  sp.is_active
                                FROM  static_page_master AS sp
                                WHERE sp.content_type IN(2,3) AND
                                  sp.is_active = 1
                                ORDER BY sp.rank DESC');

            $main_sub_pages = array_merge($image_pages, $video_pages);

            $image_template_list = $this->getStaticPageTemplateListByContentIds('', '4');
            $video_template_list = $this->getStaticPageTemplateListByContentIds('', '9,10');

            $main_static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY').'index.html';
            $main_template = [
                'left_nav' => $this->left_nav_html,
                'sub_pages' => $main_sub_pages,
                'image_template_list' => json_encode($image_template_list['template_list']),
                'video_template_list' => json_encode($video_template_list['template_list']),
                'sub_category_uuid' => '7e6xbmd929914d',
                'API_getTemplatesByCategoryIdV2' => $this->active_link_path.'/api/public/api/getTemplatesByCategoryIdV2',
            ];

            $main_html = view('static_page_main_v2', compact('main_template'))->render();

            $main_handle = fopen($main_static_page_dir, 'w');
            fwrite($main_handle, $main_html);

            $page_count = Cache::rememberforever('generateAllStaticPageV2', function () {
                $this->cache = 1;
                $is_active = 1;
                $active_path = Config::get('constant.ACTIVATION_LINK_PATH');
                $API_getTemplatesByCategoryIdV2 = $active_path.'/api/public/api/getTemplatesByCategoryIdV2';
                $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');

                $file_name = 'index.html';

                $pages = DB::select('SELECT
                                            scm.uuid AS sub_category_uuid,
                                            cm.uuid AS catalog_uuid,
                                            sp.id AS static_page_id,
                                            sp.tag_title,
                                            sp.search_category,
                                            sp.content_ids,
                                            sp.content_type,
                                            spm.sub_category_path,
                                            sp.static_page_sub_category_id,
                                            sp.catalog_name,
                                            sp.catalog_path,
                                            sp.page_detail,
                                            sp.header_detail,
                                            sp.sub_detail,
                                            sp.guide_steps,
                                            sp.faqs,
                                            sp.guide_detail,
                                            sp.rating_schema,
                                            sp.tag_in_url
                                        FROM
                                            static_page_master AS sp
                                        LEFT JOIN
                                            static_page_sub_category_master AS spm ON
                                            sp.static_page_sub_category_id = spm.id
                                        LEFT JOIN
                                            sub_category_master AS scm ON
                                            sp.sub_category_id = scm.id
                                        LEFT JOIN
                                            catalog_master AS cm ON
                                            sp.catalog_id = cm.id
                                        WHERE
                                            sp.is_active = ?
                                        ORDER BY sp.update_time DESC', [$is_active]);

                DB::statement('SET SESSION group_concat_max_len = 1000000');
                $all_content_ids = DB::select('SELECT GROUP_CONCAT(content_ids) AS content_ids FROM static_page_master WHERE is_active = 1');
                $content_ids = $all_content_ids[0]->content_ids;
                $template_list = $this->getStaticPageTemplateListByContentIds($content_ids);
                $template_list = $template_list['template_list'];
                $collection = collect($template_list)->keyBy('content_id');
                $template_list = $collection->all();

                //                $all_content_ids = DB::select("SELECT content_ids FROM static_page_master WHERE content_ids IS NOT NULL AND is_active=1");
                //                $content_id_array = array_column($all_content_ids, 'content_ids');
                //                $content_ids = implode(',',$content_id_array);
                //                $template_list = $this->getStaticPageTemplateListByContentIds($content_ids);

                foreach ($pages as $i => $row) {

                    $page_detail = json_decode($row->page_detail);
                    $header_detail = json_decode($row->header_detail);
                    $sub_detail = json_decode($row->sub_detail);
                    $rating_schema = json_decode($row->rating_schema);
                    $guide_detail = json_decode($row->guide_detail);
                    $guide_steps = json_decode($row->guide_steps);
                    $faqs = json_decode($row->faqs);
                    $sub_category_id = $row->sub_category_uuid;
                    $catalog_id = $row->catalog_uuid;

                    if ($guide_steps == null) {
                        $guide_steps = [];
                    }

                    if ($faqs == null) {
                        $faqs = [];
                    }

                    $page_url = $row->sub_category_path;
                    $folder_path = $static_page_dir.$row->tag_in_url;

                    /*Some page which are no need to generate again so we ignore this page when generate all pages
                    if($folder_path == "../../templates/gaming-intro-maker"){
                        continue;
                    }*/

                    $static_page_id = 0;
                    $is_image_main_page = 0;
                    if ($row->content_type == 3) {
                        $editor = 'intro-editor';
                    } elseif ($row->content_type == 2) {
                        $editor = 'video-editor';
                    } else {
                        $editor = 'editor';
                        $static_page_id = $row->static_page_id;
                        if ($row->catalog_path) {
                            $folder_path = $static_page_dir.$page_url.'/'.$row->catalog_path;
                        } else {
                            $is_image_main_page = 1;
                            $folder_path = $static_page_dir.$page_url;
                        }
                    }
                    $file_dir = $folder_path.'/'.$file_name;

                    $page_title = $page_detail->page_title;
                    $meta = $page_detail->meta;
                    $canonical = $page_detail->canonical;
                    $header_h1 = $header_detail->h1;
                    $header_h2 = $header_detail->h2;
                    $header_cta_text = $header_detail->cta_text;
                    $header_cta_link = $header_detail->cta_link;
                    $sub_h2 = $sub_detail->h2;
                    $sub_description = $sub_detail->description;
                    $main_h2 = isset($sub_detail->main_h2) ? $sub_detail->main_h2 : '';
                    $main_description = isset($sub_detail->main_description) ? $sub_detail->main_description : '';
                    $ratingName = isset($rating_schema->name) ? $rating_schema->name : '';
                    $userName = isset($rating_schema->userName) ? $rating_schema->userName : '';
                    $ratingDescription = isset($rating_schema->description) ? $rating_schema->description : '';
                    $ratingValue = isset($rating_schema->ratingValue) ? $rating_schema->ratingValue : '';
                    $reviewCount = isset($rating_schema->reviewCount) ? $rating_schema->reviewCount : '';
                    $guide_heading = isset($guide_detail->guide_heading) ? $guide_detail->guide_heading : '';
                    $guide_btn_text = isset($guide_detail->guide_btn_text) ? $guide_detail->guide_btn_text : '';
                    $image_template_list = [];
                    $video_template_list = [];
                    $content_ids_array = explode(',', $row->content_ids);

                    //get Similar template page list
                    $similar_pages = $this->getSimilarPages($static_page_id);

                    //get sub page list
                    if ($row->content_type == 1) {
                        $sub_pages = $this->getSubPages($row->static_page_sub_category_id, 1);
                        $row->search_category = '';

                        foreach ($content_ids_array as $i => $content_id) {
                            $template_list[$content_id]->content_id = $template_list[$content_id]->content_uuid;
                            //                            unset($template_list[$content_id]->content_uuid);
                            array_push($image_template_list, $template_list[$content_id]);
                        }

                    } else {
                        $sub_pages = $this->getSubPages($row->static_page_id);
                        $this->left_nav_html = $this->left_video_nav_html;

                        foreach ($content_ids_array as $i => $content_id) {
                            $template_list[$content_id]->content_id = $template_list[$content_id]->content_uuid;
                            //                            unset($template_list[$content_id]->content_uuid);
                            array_push($video_template_list, $template_list[$content_id]);
                        }

                    }
                    $path_of_header_cta_link = $active_path.'/app/#/'.$editor.'/'.$header_cta_link;

                    $template = [
                        'page_title' => $page_title,
                        'meta' => $meta,
                        'analytic' => $this->analytics,
                        'canonical' => $canonical,
                        'header_h1' => $header_h1,
                        'header_h2' => $header_h2,
                        'header_cta_text' => $header_cta_text,
                        'header_cta_link' => $path_of_header_cta_link,
                        'sub_h2' => $sub_h2,
                        'sub_description' => $sub_description,
                        'main_h2' => $main_h2,
                        'main_description' => $main_description,
                        'guide_heading' => $guide_heading,
                        'guide_btn_text' => $guide_btn_text,
                        'ratingName' => $ratingName,
                        'userName' => $userName,
                        'ratingDescription' => $ratingDescription,
                        'ratingValue' => $ratingValue,
                        'reviewCount' => $reviewCount,
                        'static_page_id' => $static_page_id,
                        'search_category' => $row->search_category,
                        'active_image_tab' => '',
                        'content_type' => $row->content_type,
                        'left_nav' => $this->left_nav_html,
                        'similar_pages' => $similar_pages,
                        'sub_pages' => $sub_pages,
                        'guide_steps' => $guide_steps,
                        'page_faqs' => $faqs,
                        'image_template_list' => json_encode($image_template_list),
                        'sub_category_id' => $sub_category_id,
                        'catalog_id' => $catalog_id,
                        'API_getTemplatesByCategoryIdV2' => $API_getTemplatesByCategoryIdV2,
                        'video_template_list' => json_encode($video_template_list),
                        'is_image_main_page' => $is_image_main_page,
                    ];

                    $html = view('static_page_v2', compact('template'))->render();

                    if (! is_dir($folder_path)) {
                        mkdir($folder_path, 0777, true);
                    }
                    $handle = fopen($file_dir, 'w');

                    fwrite($handle, $html);
                    //                    if ($row->content_type == 1) {
                    //                        $folder_path = $static_page_dir . $page_url;
                    //                        $file_dir = $folder_path . '/' . $file_name;
                    //                        if (!is_dir($folder_path)) {
                    //                            mkdir($folder_path, 0777, true);
                    //                        }
                    //                        $handle = fopen($file_dir, 'w');
                    //
                    //                        fwrite($handle, $html);
                    //                    }
                }

                return count($pages);
            });

            if ($this->cache) {
                $result = ['generated_pages' => $page_count];
                $message = 'Static pages generated successfully.';
            } else {
                $result = ['generated_pages' => 0];
                $message = 'There are no any changes for generate pages.';
            }

            $response = Response::json(['code' => 200, 'message' => $message, 'cause' => '', 'data' => $result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('generateAllStaticPageV2', $e);
            //Log::error("generateAllStaticPageV2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;

    }

    public function generateAllStaticPageV3(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $this->cache = 0;
            $this->active_link_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');
            ini_set('memory_limit', '-1');

            $this->left_nav_html = $this->getLeftNavigationV2('active', '');
            $this->left_video_nav_html = $this->getLeftNavigationV2('', 'active');

            $http_host = $_SERVER['HTTP_HOST'];
            $live_server = Config::get('constant.LIVE_SERVER_NAME');
            if ($http_host == $live_server) {
                $this->analytics = view('analytics')->render();
            } else {
                $this->analytics = '';
            }

            /*Update left navigation in main html file */
            $image_pages = DB::select('SELECT
                                   spsb.id,
                                   sp.rank,
                                   spsb.sub_category_name AS tag,
                                   CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/") AS page_url
                                 FROM static_page_sub_category_master AS spsb
                                 LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                 WHERE sp.is_active = 1 AND
                                       sp.content_type = 1 AND
                                       sp.catalog_id IS NULL
                                 ORDER BY sp.rank DESC');

            $video_pages = DB::select('SELECT
                                  sp.id AS static_page_id,
                                  sp.tag_title AS tag,
                                  sp.rank,
                                  CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url,
                                  sp.is_active
                                FROM  static_page_master AS sp
                                WHERE sp.content_type IN(2,3) AND
                                  sp.is_active = 1
                                ORDER BY sp.rank DESC');

            $main_pages = DB::select('SELECT
                                  sp.id AS static_page_id,
                                  sp.app_cta_detail,
                                  sp.page_detail AS page_detail,
                                  sp.header_detail AS header_detail,
                                  sp.sub_detail AS sub_detail,
                                  sp.rating_schema AS rating_schema,
                                  sp.faqs AS faqs,
                                  sp.guide_detail AS guide_detail,
                                  sp.guide_steps AS guide_steps,
                                  sp.is_active,
                                  sp.content_type AS content_type,
                                  sp.content_ids AS content_ids
                                FROM  static_page_master AS sp
                                WHERE sp.content_type = 0 AND
                                  sp.is_active = 1
                                ORDER BY sp.rank DESC');

            $main_page_image_content_id = '';
            $main_page_video_content_id = '';
            $app_cta_detail = '';
            $main_page_detail = '';
            $main_header_detail = '';
            $main_sub_detail = '';
            $main_rating_schema = '';
            $main_guide_steps = '';
            $main_faqs = '';
            $main_guide_detail = '';

            if (isset($main_pages[0]->content_ids) && $main_pages[0]->content_ids) {
                $main_page_content_id = $main_pages[0]->content_ids;
                $main_page_image_content_id = implode(',', json_decode($main_page_content_id)->{'1'});
                $main_page_video_content_id = implode(',', json_decode($main_page_content_id)->{'2,3'});
                $app_cta_detail = json_decode($main_pages[0]->app_cta_detail);
                $main_page_detail = json_decode($main_pages[0]->page_detail);
                $main_header_detail = json_decode($main_pages[0]->header_detail);
                $main_sub_detail = json_decode($main_pages[0]->sub_detail);
                $main_rating_schema = json_decode($main_pages[0]->rating_schema);
                $main_guide_detail = json_decode($main_pages[0]->guide_detail);
                $main_guide_steps = json_decode(json_encode(json_decode($main_pages[0]->guide_steps)), true);
                $main_faqs = json_decode(json_encode(json_decode($main_pages[0]->faqs)), true);

            }

            $main_sub_pages = array_merge($image_pages, $video_pages);
            $image_template_list = $this->getStaticPageTemplateListByContentIds($main_page_image_content_id, '4');
            $video_template_list = $this->getStaticPageTemplateListByContentIds($main_page_video_content_id, '9,10');
            $this->active_path = Config::get('constant.ACTIVATION_LINK_PATH');
            $this->API_getTemplatesByCategoryIdV2 = $this->active_path.'/api/public/api/getTemplatesByCategoryIdV2';
            $templatesOfImages = $image_template_list['template_list'];
            $templatesOfVideos = $video_template_list['template_list'];
            foreach ($templatesOfImages as $key => $value) {
                $templatesOfImages[$key]->{'content_id'} = $templatesOfImages[$key]->{'content_uuid'};
            }
            foreach ($templatesOfVideos as $key => $value) {
                $templatesOfVideos[$key]->{'content_id'} = $templatesOfVideos[$key]->{'content_uuid'};
            }
            $main_static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY').'index.html';
            $main_template = [
                'left_nav' => $this->left_nav_html,
                'sub_pages' => $main_sub_pages,
                'page_detail' => $main_page_detail,
                'header_detail' => $main_header_detail,
                'sub_detail' => $main_sub_detail,
                'rating_schema' => $main_rating_schema,
                'guide_detail' => $main_guide_detail,
                'guide_steps' => $main_guide_steps,
                'faqs' => $main_faqs,
                'app_cta_detail' => $app_cta_detail,
                'image_template_list' => json_encode($image_template_list['template_list']),
                'video_template_list' => json_encode($video_template_list['template_list']),
                'API_getTemplatesByCategoryIdV2' => $this->active_link_path.'/api/public/api/getTemplatesByCategoryIdV2',

            ];
            $main_html = view('static_page_main_v2', compact('main_template'))->render();

            $main_handle = fopen($main_static_page_dir, 'w');
            fwrite($main_handle, $main_html);

            $page_count = Cache::rememberforever('generateAllStaticPageV3', function () {
                $this->cache = 1;
                $is_active = 1;
                $static_page_dir = '../..'.Config::get('constant.STATIC_PAGE_DIRECTORY');

                $file_name = 'index.html';

                $pages = DB::select('SELECT
                                            scm.uuid AS sub_category_uuid,
                                            cm.uuid AS catalog_uuid,
                                            sp.id AS static_page_id,
                                            sp.tag_title,
                                            sp.search_category,
                                            sp.content_ids,
                                            sp.content_type,
                                            spm.sub_category_path,
                                            sp.static_page_sub_category_id,
                                            sp.catalog_name,
                                            sp.catalog_path,
                                            sp.app_cta_detail,
                                            sp.page_detail,
                                            sp.header_detail,
                                            sp.sub_detail,
                                            sp.guide_steps,
                                            sp.faqs,
                                            sp.guide_detail,
                                            sp.rating_schema,
                                            sp.tag_in_url
                                        FROM
                                            static_page_master AS sp
                                        LEFT JOIN
                                            static_page_sub_category_master AS spm ON
                                            sp.static_page_sub_category_id = spm.id
                                        LEFT JOIN
                                            sub_category_master AS scm ON
                                            sp.sub_category_id = scm.id
                                        LEFT JOIN
                                            catalog_master AS cm ON
                                            sp.catalog_id = cm.id
                                        WHERE
                                            sp.is_active = ? AND
                                            sp.content_type != 0
                                        ORDER BY sp.update_time DESC', [$is_active]);

                DB::statement('SET SESSION group_concat_max_len = 10000000');
                $all_content_ids = DB::select('SELECT GROUP_CONCAT(content_ids) AS content_ids FROM static_page_master WHERE is_active = 1 AND content_type != 0');
                $content_ids = $all_content_ids[0]->content_ids;
                $template_list = $this->getStaticPageTemplateListByContentIds($content_ids);
                $template_list = $template_list['template_list'];
                $collection = collect($template_list)->keyBy('content_id');
                $template_list = $collection->all();

                //                $all_content_ids = DB::select("SELECT content_ids FROM static_page_master WHERE content_ids IS NOT NULL AND is_active=1");
                //                $content_id_array = array_column($all_content_ids, 'content_ids');
                //                $content_ids = implode(',',$content_id_array);
                //                $template_list = $this->getStaticPageTemplateListByContentIds($content_ids);
                DB::beginTransaction();
                foreach ($pages as $i => $row) {

                    $app_cta_detail = json_decode($row->app_cta_detail);
                    $page_detail = json_decode($row->page_detail);
                    $header_detail = json_decode($row->header_detail);
                    $sub_detail = json_decode($row->sub_detail);
                    $rating_schema = json_decode($row->rating_schema);
                    $guide_detail = json_decode($row->guide_detail);
                    $guide_steps = json_decode($row->guide_steps);
                    $faqs = json_decode($row->faqs);
                    $sub_category_id = $row->sub_category_uuid;
                    $catalog_id = $row->catalog_uuid;

                    if ($guide_steps == null) {
                        $guide_steps = [];
                    }

                    if ($faqs == null) {
                        $faqs = [];
                    }

                    $page_url = $row->sub_category_path;
                    $folder_path = $static_page_dir.$row->tag_in_url;

                    /*Some page which are no need to generate again so we ignore this page when generate all pages
                    if ($folder_path == "../../templates/gaming-intro-maker") {
                        continue;
                    }*/

                    $static_page_id = 0;
                    $is_image_main_page = 0;
                    if ($row->content_type == 3) {
                        $editor = 'intro-editor';
                    } elseif ($row->content_type == 2) {
                        $editor = 'video-editor';
                    } else {
                        $editor = 'editor';
                        $static_page_id = $row->static_page_id;
                        if ($row->catalog_path) {
                            $folder_path = $static_page_dir.$page_url.'/'.$row->catalog_path;
                        } else {
                            $is_image_main_page = 1;
                            $folder_path = $static_page_dir.$page_url;
                        }
                    }
                    $file_dir = $folder_path.'/'.$file_name;

                    $page_title = $page_detail->page_title;
                    $meta = $page_detail->meta;
                    $canonical = $page_detail->canonical;
                    $header_h1 = $header_detail->h1;
                    $header_h2 = $header_detail->h2;
                    $header_cta_text = $header_detail->cta_text;
                    $header_cta_link = $header_detail->cta_link;
                    $sub_h2 = $sub_detail->h2;
                    $sub_description = $sub_detail->description;
                    $main_h2 = isset($sub_detail->main_h2) ? $sub_detail->main_h2 : '';
                    $main_description = isset($sub_detail->main_description) ? $sub_detail->main_description : '';
                    $ratingName = isset($rating_schema->name) ? $rating_schema->name : '';
                    $userName = isset($rating_schema->userName) ? $rating_schema->userName : '';
                    $ratingDescription = isset($rating_schema->description) ? $rating_schema->description : '';
                    $ratingValue = isset($rating_schema->ratingValue) ? $rating_schema->ratingValue : '';
                    $reviewCount = isset($rating_schema->reviewCount) ? $rating_schema->reviewCount : '';
                    $guide_heading = isset($guide_detail->guide_heading) ? $guide_detail->guide_heading : '';
                    $guide_btn_text = isset($guide_detail->guide_btn_text) ? $guide_detail->guide_btn_text : '';
                    $image_template_list = [];
                    $video_template_list = [];
                    $content_ids_array = explode(',', $row->content_ids);

                    //get Similar template page list
                    $similar_pages = $this->getSimilarPages($static_page_id);

                    //get sub page list
                    if ($row->content_type == 1) {
                        $sub_pages = $this->getSubPages($row->static_page_sub_category_id, 1);
                        $row->search_category = '';

                        foreach ($content_ids_array as $i => $content_id) {
                            $template_list[$content_id]->content_id = $template_list[$content_id]->content_uuid;
                            //                            unset($template_list[$content_id]->content_uuid);
                            array_push($image_template_list, $template_list[$content_id]);
                        }

                    } else {
                        $sub_pages = $this->getSubPages($row->static_page_id);
                        $this->left_nav_html = $this->left_video_nav_html;

                        foreach ($content_ids_array as $i => $content_id) {
                            $template_list[$content_id]->content_id = $template_list[$content_id]->content_uuid;
                            //                            unset($template_list[$content_id]->content_uuid);
                            array_push($video_template_list, $template_list[$content_id]);
                        }

                    }
                    $path_of_header_cta_link = $this->active_path.'/app/#/'.$editor.'/'.$header_cta_link;

                    $template = [
                        'page_title' => $page_title,
                        'meta' => $meta,
                        'analytic' => $this->analytics,
                        'canonical' => $canonical,
                        'app_cta_detail' => $app_cta_detail,
                        'header_h1' => $header_h1,
                        'header_h2' => $header_h2,
                        'header_cta_text' => $header_cta_text,
                        'header_cta_link' => $path_of_header_cta_link,
                        'sub_h2' => $sub_h2,
                        'sub_description' => $sub_description,
                        'main_h2' => $main_h2,
                        'main_description' => $main_description,
                        'guide_heading' => $guide_heading,
                        'guide_btn_text' => $guide_btn_text,
                        'ratingName' => $ratingName,
                        'userName' => $userName,
                        'ratingDescription' => $ratingDescription,
                        'ratingValue' => $ratingValue,
                        'reviewCount' => $reviewCount,
                        'static_page_id' => $static_page_id,
                        'search_category' => $row->search_category,
                        'active_image_tab' => '',
                        'content_type' => $row->content_type,
                        'left_nav' => $this->left_nav_html,
                        'similar_pages' => $similar_pages,
                        'sub_pages' => $sub_pages,
                        'guide_steps' => $guide_steps,
                        'page_faqs' => $faqs,
                        'image_template_list' => json_encode($image_template_list),
                        'sub_category_id' => $sub_category_id,
                        'catalog_id' => $catalog_id,
                        'API_getTemplatesByCategoryIdV2' => $this->API_getTemplatesByCategoryIdV2,
                        'video_template_list' => json_encode($video_template_list),
                        'is_image_main_page' => $is_image_main_page,
                    ];

                    $html = view('static_page_v2', compact('template'))->render();
                    //shell_exec("rm -r $folder_path/*");

                    if (! is_dir($folder_path)) {
                        mkdir($folder_path, 0777, true);
                    }
                    $handle = fopen($file_dir, 'w');

                    fwrite($handle, $html);

                    //                    if ($row->content_type == 1) {
                    //                        $folder_path = $static_page_dir . $page_url;
                    //                        $file_dir = $folder_path . '/' . $file_name;
                    //                        if (!is_dir($folder_path)) {
                    //                            mkdir($folder_path, 0777, true);
                    //                        }
                    //                        $handle = fopen($file_dir, 'w');
                    //
                    //                        fwrite($handle, $html);
                    //                    }
                }
                DB::commit();

                return count($pages);
            });

            if ($this->cache) {
                $result = ['generated_pages' => $page_count];
                $message = 'Static pages generated successfully.';
            } else {
                $result = ['generated_pages' => 0];
                $message = 'There are no any changes for generate pages.';
            }

            $response = Response::json(['code' => 200, 'message' => $message, 'cause' => '', 'data' => $result]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('generateAllStaticPageV3', $e);
            //Log::error("generateAllStaticPageV3 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get static page template list id.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;
    }

    public function getAllStaticPageURL(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getAllStaticPageURL")) {
                $result = Cache::rememberforever('getAllStaticPageURL', function () {
                    $image_pages = DB::select('SELECT
                                          sp.id AS static_page_id,
                                          spsb.sub_category_path,
                                          coalesce(sp.catalog_path,"") AS catalog_path,
                                          CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path) as page_url,
                                          CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path) as sub_page_url,
                                          sp.is_active
                                      FROM
                                          static_page_sub_category_master AS spsb
                                      LEFT JOIN
                                          static_page_master AS sp
                                      ON
                                          spsb.id = sp.static_page_sub_category_id
                                      WHERE
                                           sp.is_active = 1
                                      ORDER BY
                                           sp.update_time DESC');

                    $video_pages = DB::select('SELECT
                                      sp.id AS static_page_id,
                                      sp.content_type,
                                      sp.tag_in_url,
                                      CONCAT("'.$this->static_page_dir.'",sp.tag_in_url)AS video_page_url
                                    FROM
                                       static_page_master AS sp
                                    WHERE
                                        sp.is_active = 1 AND
                                        sp.content_type in(2,3)
                                    ORDER BY sp.update_time DESC');

                    return ['image_pages' => $image_pages, 'video_pages' => $video_pages];
                });
            }
            $redis_result = Cache::get('getAllStaticPageURL');

            $image_pages = $redis_result['image_pages'];
            $video_pages = $redis_result['video_pages'];

            $image_page = [];
            $video_page = [];
            $intro_page = [];

            foreach ($image_pages as $row) {
                array_push($image_page, $row->sub_page_url);
            }
            foreach ($video_pages as $row) {
                if ($row->content_type == 2) {
                    array_push($video_page, $row->video_page_url);
                } else {
                    array_push($intro_page, $row->video_page_url);
                }
            }
            $total_pages = count($image_page) + count($video_page) + count($intro_page);

            $response = Response::json(['code' => 200, 'message' => 'URL fetch successfully.', 'cause' => '', 'data' => ['total_pages' => $total_pages, 'image_page' => $image_page, 'video_page' => $video_page, 'intro_page' => $intro_page]]);

        } catch (Exception $e) {
            (new ImageController())->logs('getAllStaticPageURL', $e);
            //      Log::error("getAllStaticPageURL : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'URL fetch', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);

        }

        return $response;
    }

    public function getMappingData($keyword)
    {
        try {
            $json_file_path = '../..'.Config::get('constant.TEMP_DIRECTORY').'mapping.json';
            $file_content = file_get_contents($json_file_path);
            $decoded_json = json_decode($file_content, false);
            foreach ($decoded_json as $key) {
                if (in_array($keyword, $key->keywords)) {
                    return ['cta_text' => $key->cta_text, 'destination' => $key->destination];
                }
            }

            foreach ($decoded_json as $value) {
                $array1 = ['maker'];
                $array2 = explode('|', str_replace('-', '|', implode('|', $value->keywords)));
                $array_diff = implode('|', array_diff($array2, $array1));
                if (preg_match('('.$array_diff.')', $keyword) === 1) {
                    return ['cta_text' => $value->cta_text, 'destination' => $value->destination];
                }
            }

            return ['cta_text' => 'Create Your Design Now', 'destination' => (object) ['playStoreLink' => 'https://play.google.com/store/apps/details?id=com.oneintro.intromaker&referrer=utm_source%3DOB_PAK', 'appStoreLink' => 'https://apps.apple.com/us/app/intro-maker-outro-maker/id1516421168']];

        } catch (Exception $e) {
            (new ImageController())->logs('getMappingData', $e);
        }
    }

    /*============================== Similar template pages ===================================*/
    /**
     * @api {post} getPageFromTag getPageFromTag
     *
     * @apiName getPageFromTag
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
     * "tag_name":"flyer",
     * "content_type":1,
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Pages fetched successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getPageFromTag(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['tag_name'], $request)) != '') {
                return $response;
            }

            $this->tag_name = trim(strtolower($request->tag_name));
            $this->tag_name = trim(preg_replace('/[^A-Za-z ]/', '', $this->tag_name));
            if (strlen($this->tag_name) >= 50 || $this->tag_name == '') {
                return Response::json(['code' => 201, 'message' => 'Invalid search text please enter valid search text.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $this->content_type = isset($request->content_type) ? $request->content_type : '';
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');

            if (! Cache::has("Config::get('constant.REDIS_KEY'):getPageFromTag:$this->tag_name:$this->content_type")) {
                $result = Cache::rememberforever("getPageFromTag:$this->tag_name:$this->content_type", function () {
                    $sub_pages = [];

                    if ($this->content_type == Config::get('constant.IMAGE')) {
                        $main_pages = DB::select('SELECT
                                        sp.id,
                                        CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/") AS page_url
                                      FROM static_page_sub_category_master AS spsb
                                      LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                      WHERE
                                        sp.is_active = 1 AND
                                        sp.catalog_id IS NULL AND
                                        sp.content_type = 1 AND
                                        spsb.sub_category_path LIKE '."'%".$this->tag_name."%'".'
                                      ORDER BY sp.update_time DESC');

                        $sub_pages = DB::select('SELECT
                                        sp.id,
                                        IF(sp.catalog_path !="",CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path,"/"),"") as page_url
                                      FROM static_page_sub_category_master AS spsb
                                      LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                                      WHERE
                                        sp.catalog_id IS NOT NULL AND
                                        sp.is_active = 1 AND
                                        sp.content_type = 1 AND
                                        (spsb.sub_category_path LIKE '."'%".$this->tag_name."%'".' OR sp.catalog_path LIKE  '."'%".$this->tag_name."%'".')
                                      ORDER BY sp.update_time DESC');
                    } elseif ($this->content_type == Config::get('constant.VIDEO')) {
                        $main_pages = DB::select('SELECT
                                        sp.id,
                                        CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url
                                      FROM static_page_master AS sp
                                      WHERE
                                        sp.is_active = 1 AND
                                        sp.catalog_id IS NULL AND
                                        sp.content_type IN(2,3) AND
                                        sp.search_category LIKE '."'%".$this->tag_name."%'".'
                                      ORDER BY sp.update_time DESC');
                    } else {
                        $this->search_tag_name = '%'.$this->tag_name.'%';
                        $main_pages = DB::select('SELECT
                                        IF(sp.content_type != 1,CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/"),IF(sp.catalog_id IS NULL,CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/"),CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path,"/"))) AS page_url
                                      FROM static_page_master AS sp
                                      LEFT JOIN static_page_sub_category_master AS spsb ON spsb.id = sp.static_page_sub_category_id
                                      WHERE
                                        sp.is_active = 1 AND
                                        (spsb.sub_category_path LIKE "'.$this->search_tag_name.'" OR sp.catalog_path LIKE  "'.$this->search_tag_name.'" OR sp.search_category LIKE "'.$this->search_tag_name.'" )
                                      ORDER BY sp.update_time DESC');
                    }

                    return array_merge($main_pages, $sub_pages);
                });
            }

            $redis_result = Cache::get("getPageFromTag:$this->tag_name:$this->content_type");
            if (count($redis_result) > 0) {
                $response = Response::json(['code' => 200, 'message' => 'Pages fetched successfully.', 'cause' => '', 'data' => $redis_result]);
            } else {
                $response = Response::json(['code' => 201, 'message' => 'No pages found of this tag.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('getPageFromTag', $e);
            //      Log::error("getPageFromTag : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get page.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function addSimilarTemplatePage($tag, $static_page_id)
    {
        try {
            $tag_name = trim(preg_replace('/[^A-Za-z\- ]/', '', $tag->tag_name));
            if (strlen($tag_name) > 0) {

                $page_url_id = $tag->page_url_id;
                $page_type = $tag->page_type;
                $create_time = date('Y-m-d H:i:s');

                $is_exist = DB::select('SELECT id FROM similar_template_page WHERE tag_name=? AND page_id=?', [$tag_name, $static_page_id]);
                if (count($is_exist) <= 0) {
                    DB::beginTransaction();
                    DB::insert('INSERT INTO similar_template_page(tag_name,page_id,page_url_id,page_type,create_time) VALUES(?,?,?,?,?)', [$tag_name, $static_page_id, $page_url_id, $page_type, $create_time]);
                    DB::commit();
                }
            }
        } catch (Exception $e) {
            (new ImageController())->logs('deleteSimilarTemplatePage', $e);
            //      Log::error("deleteSimilarTemplatePage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
    }

    /**
     * @api {post} updateSimilarTemplatePage   updateSimilarTemplatePage
     *
     * @apiName updateSimilarTemplatePage
     *
     * @apiGroup Admin
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     * Key: Authorization
     * Value: Bearer token
     * }0,
     * @apiSuccessExample Request-Body:
     * {
     * "id":1, //compulsory
     * "tag_name":"flyer" //compulsory
     * "static_page_id":41 //compulsory
     * "page_type":1 //if select template page then 1,add url manually then 2
     * "page_url_id":"101" //if select template page then id (1),add url manually then url
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Tag updated successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function updateSimilarTemplatePage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            //Log::info("request data :", [$request]);
            if (($response = (new VerificationController())->validateRequiredParameter(['id', 'tag_name', 'page_type', 'page_url_id', 'static_page_id'], $request)) != '') {
                return $response;
            }

            $id = $request->id;
            $tag_name = trim(strtolower($request->tag_name));
            $page_type = $request->page_type;
            $static_page_id = $request->static_page_id;
            $page_url_id = ($request->page_url_id != '') ? $request->page_url_id : null;

            $tag_name = trim(preg_replace('/[^A-Za-z\- ]/', '', $tag_name));
            if (strlen($tag_name) > 50) {
                return Response::json(['code' => 201, 'message' => 'Tag must be less than or equal 50 character.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            if (strlen($tag_name) == 0) {
                return Response::json(['code' => 201, 'message' => 'Tag name must be alphabets.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $is_exist = DB::select('SELECT id FROM similar_template_page WHERE tag_name=? AND page_id = ? AND id !=?', [$tag_name, $static_page_id, $id]);
            if (count($is_exist) > 0) {
                return Response::json(['code' => 201, 'message' => 'Tag already exist for this page.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            if ($page_type != 1) {

                DB::beginTransaction();
                DB::update('UPDATE
                          similar_template_page
                        SET
                          tag_name = ?,
                          page_type = ?,
                          page_url_id=?
                        WHERE
                          id = ? ',
                    [$tag_name, $page_type, $page_url_id, $id]);
                DB::commit();

            }

            $response = Response::json(['code' => 200, 'message' => 'Similar tag updated successfully.', 'cause' => '', 'data' => ['tag' => $tag_name]]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateSimilarTemplatePage', $e);
            //      Log::error("updateSimilarTemplatePage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update similar tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * @api {post} deleteSimilarTemplatePage   deleteSimilarTemplatePage
     *
     * @apiName deleteSimilarTemplatePage
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
    public function deleteSimilarTemplatePage(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['id'], $request)) != '') {
                return $response;
            }

            $id = $request->id;

            DB::beginTransaction();

            DB::delete('DELETE FROM similar_template_page WHERE id = ? ', [$id]);

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Similar tag deleted successfully.', 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('deleteSimilarTemplatePage', $e);
            //      Log::error("deleteSimilarTemplatePage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'delete similar tag.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getSimilarPages($static_page_id)
    {
        try {

            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->static_page_id = $static_page_id;
            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSimilarPages:$this->static_page_id")) {
                Cache::rememberforever("getSimilarPages:$this->static_page_id", function () {
                    return DB::select('SELECT
                                stp.id,
                                stp.page_type,
                                stp.tag_name,
                                IF(stp.page_type = 1,
                                   IF(sp.content_type = 1,
                                     IF(sp.catalog_path !="",
                                        CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path,"/"),
                                        CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/")),
                                   CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/")),
                                stp.page_url_id) AS page_url
                             FROM
                                similar_template_page AS stp
                             LEFT JOIN static_page_master AS sp ON sp.id = stp.page_url_id
                             LEFT JOIN static_page_sub_category_master AS spsb ON spsb.id=sp.static_page_sub_category_id
                             WHERE
                                stp.page_id=?
                             ORDER BY stp.update_time DESC', [$this->static_page_id]);
                });
            }

            $redis_result = Cache::get("getSimilarPages:$this->static_page_id");
            if (count($redis_result) > 0) {
                return $redis_result;
            } else {
                return [];
            }

        } catch (Exception $e) {
            Log::error('getSimilarPages : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return [];
        }
    }

    public function getSubPages($static_page_id, $is_image_page = 0)
    {
        try {
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');
            $this->static_page_id = $static_page_id;
            $this->is_image_page = $is_image_page;
            /**
             * is_image_page
             * 1 = image page other wise video/intro page
             */
            if (! Cache::has("Config::get('constant.REDIS_KEY'):getSubPages:$this->static_page_id:$this->is_image_page")) {
                Cache::rememberforever("getSubPages:$this->static_page_id:$this->is_image_page", function () {
                    if ($this->is_image_page) {
                        return DB::select('SELECT
                                 sp.id as static_page_id,
                                 coalesce(sp.catalog_name,"") AS tag,
                                 spsb.sub_category_name,
                                 CONCAT("'.$this->static_page_dir.'",spsb.sub_category_path,"/",sp.catalog_path,"/") as page_url
                              FROM static_page_sub_category_master AS spsb
                              LEFT JOIN static_page_master AS sp ON spsb.id = sp.static_page_sub_category_id
                              WHERE sp.static_page_sub_category_id = ? AND
                                 sp.catalog_id IS NOT NULL AND
                                 sp.is_active = 1 AND
                                 sp.content_type = 1
                              ORDER BY sp.update_time DESC', [$this->static_page_id]);
                    } else {
                        return DB::select('SELECT
                                 sp.id as static_page_id,
                                 sp.tag_title AS tag,
                                 CONCAT("'.$this->static_page_dir.'",sp.tag_in_url,"/") AS page_url
                               FROM static_page_master AS sp
                               WHERE sp.content_type IN(2,3) AND
                                sp.is_active = 1 AND
                                sp.id != ?
                               ORDER BY sp.rank DESC', [$this->static_page_id]);
                    }
                });
            }

            $redis_result = Cache::get("getSubPages:$this->static_page_id:$this->is_image_page");
            if (count($redis_result) > 0) {
                return $redis_result;
            } else {
                return [];
            }
        } catch (Exception $e) {
            (new ImageController())->logs('getSubPages', $e);
            //      Log::error("getSubPages : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return [];
        }
    }

    /* =================================| Sub Functions |=============================*/
    public function getStaticPageTemplateListByContentIds($content_ids = '', $content_type = '')
    {
        try {
            $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
            $this->page = 1;
            $this->offset = ($this->page - 1) * $this->item_count;
            $this->static_page_dir = Config::get('constant.ACTIVATION_LINK_PATH').Config::get('constant.STATIC_PAGE_DIRECTORY');
            $default_content_type = Config::get('constant.CONTENT_TYPE_OF_CARD_JSON').','.Config::get('constant.CONTENT_TYPE_OF_VIDEO_JSON').','.Config::get('constant.CONTENT_TYPE_OF_INTRO_VIDEO');
            $this->content_type = isset($content_type) && ! empty($content_type) ? $content_type : $default_content_type;
            $this->content_ids = $content_ids;
            $this->is_active = 1;

            //            $redis_result = Cache::remember("getStaticPageTemplateListByContentIds$this->content_type:$this->content_ids:$this->page:$this->item_count", Config::get('constant.CACHE_TIME_7_DAYS'), function () {

            if ($this->content_type == Config::get('constant.CONTENT_TYPE_OF_CARD_JSON')) {
                $image_template_query = 'AND scc.catalog_id IN (SELECT DISTINCT s.catalog_id FROM static_page_master AS s WHERE s.sub_category_id = scc.sub_category_id AND s.catalog_id IS NOT NULL)';
            } else {
                $image_template_query = '';
            }

            if (! $this->content_ids) {

                DB::statement("SET sql_mode = '' ");
                $template_list = DB::select('SELECT
                                                    cm.uuid AS content_id,
                                                    ctm.uuid AS catalog_id,
                                                    scm.uuid AS sub_category_id,
                                                    REPLACE(ctm.name,"\'","") AS catalog_name,
                                                    REPLACE(scm.sub_category_name,"\'","") AS sub_category_name,
                                                    cm.content_type,
                                                    cm.template_name,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                    IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                    COALESCE(cm.image,"") AS svg_file,
                                                    COALESCE(cm.height,0) AS height,
                                                    COALESCE(cm.width,0) AS width
                                                FROM
                                                    content_master AS cm
                                                    LEFT JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id '.$image_template_query.'
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.is_featured = 1
                                                    JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id
                                                WHERE
                                                    cm.is_active = 1 AND
                                                    cm.content_type IN ('.$this->content_type.') AND
                                                    ISNULL(cm.original_img) AND
                                                    ISNULL(cm.display_img)
                                                    GROUP by cm.id
                                                ORDER BY cm.update_time DESC LIMIT 20');

            } else {

                DB::statement("SET sql_mode = '' ");
                $template_list = DB::select('SELECT
                                                    cm.id AS content_id,
                                                    cm.uuid AS content_uuid,
                                                    ctm.uuid AS catalog_id,
                                                    scm.uuid AS sub_category_id,
                                                    REPLACE(ctm.name,"\'","") AS catalog_name,
                                                    REPLACE(scm.sub_category_name,"\'","") AS sub_category_name,
                                                    cm.content_type,
                                                    cm.template_name,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS thumbnail_img,
                                                    IF(cm.webp_image != "",CONCAT("'.Config::get('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                    IF(cm.image != "",CONCAT("'.Config::get('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                    IF(cm.content_file != "",CONCAT("'.Config::get('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                    COALESCE(cm.image,"") AS svg_file,
                                                    COALESCE(cm.height,0) as height,
                                                    COALESCE(cm.width,0) as width
                                                FROM content_master AS cm
                                                    JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id
                                                    JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                    JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id
                                                WHERE
                                                    cm.is_active = 1 AND
                                                    cm.content_type IN ('.$this->content_type.') AND
                                                    ISNULL(cm.original_img) AND
                                                    ISNULL(cm.display_img) AND
                                                    cm.id IN ('.$this->content_ids.')
                                                    GROUP by cm.id
                                                ORDER BY FIELD(cm.id, '.$this->content_ids.' )  ');

            }

            return ['template_list' => $template_list];
            //            });
            //
            //            return $redis_result;

        } catch (Exception $e) {
            Log::error('getStaticPageTemplateListByContentIds : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            return ['template_list' => []];
        }
    }

    public function getTemplateDetailsBySubCategoryId(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['content_id'], $request)) != '') {
                return $response;
            }

            $content_id = $request->content_id;

            $content_details = DB::select('SELECT
                                        DISTINCT cm.uuid AS content_id,
                                        ctm.uuid AS catalog_id,
                                        scm.uuid AS sub_category_id,
                                        IF(cm.image != "",CONCAT("'.config('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                        IF(cm.webp_image != "",CONCAT("'.config('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                        IF(cm.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                        IF(cm.content_file != "",CONCAT("'.config('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                        COALESCE(cm.search_category,"") AS search_category,
                                        COALESCE(cm.is_featured,"") AS is_featured,
                                        COALESCE(cm.is_free,0) AS is_free,
                                        COALESCE(cm.is_portrait,0) AS is_portrait,
                                        COALESCE(cm.height,0) AS height,
                                        COALESCE(cm.width,0) AS width,
                                        COALESCE(cm.color_value,"") AS color_value,
                                        COALESCE(cm.multiple_images,"") AS multiple_images,
                                        COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                        cm.content_type,
                                        cm.template_name,
                                        cm.update_time,
                                        scm.uuid AS sub_category_id,
                                        REPLACE(ctm.name,"\'","") AS catalog_name,
                                        REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                      FROM
                                        content_master AS cm
                                        JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                        JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                        JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1
                                      WHERE
                                        cm.is_active = 1 AND
                                        cm.uuid = ? LIMIT 1', [$content_id]);

            if ($content_details) {
                $sub_category_id = $content_details[0]->sub_category_id;
                $catalog_id = $content_details[0]->catalog_id;

                $related_template_details = DB::select('SELECT
                                                  DISTINCT cm.uuid AS content_id,
                                                  ctm.uuid AS catalog_id,
                                                  IF(cm.image != "",CONCAT("'.config('constant.THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS sample_image,
                                                  IF(cm.webp_image != "",CONCAT("'.config('constant.WEBP_THUMBNAIL_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.webp_image),"") AS webp_thumbnail,
                                                  IF(cm.image != "",CONCAT("'.config('constant.COMPRESSED_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.image),"") AS compressed_img,
                                                  IF(cm.content_file != "",CONCAT("'.config('constant.ORIGINAL_VIDEO_DIRECTORY_OF_DIGITAL_OCEAN').'",cm.content_file),"") AS content_file,
                                                  cm.content_type,
                                                  cm.template_name,
                                                  COALESCE(cm.search_category,"") AS search_category,
                                                  COALESCE(cm.is_featured,"") AS is_featured,
                                                  COALESCE(cm.is_free,0) AS is_free,
                                                  COALESCE(cm.is_portrait,0) AS is_portrait,
                                                  COALESCE(cm.height,0) AS height,
                                                  COALESCE(cm.width,0) AS width,
                                                  COALESCE(cm.color_value,"") AS color_value,
                                                  COALESCE(cm.multiple_images,"") AS multiple_images,
                                                  COALESCE(cm.json_pages_sequence,"") AS pages_sequence,
                                                  cm.update_time,
                                                  REPLACE(ctm.name,"\'","") AS catalog_name,
                                                  scm.uuid AS sub_category_id,
                                                  REPLACE(scm.sub_category_name,"\'","") AS sub_category_name
                                                FROM
                                                  content_master AS cm
                                                  JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id AND scc.is_active = 1
                                                  JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id AND ctm.uuid = ?
                                                  JOIN sub_category_master AS scm ON scc.sub_category_id = scm.id AND scm.is_active = 1 AND scm.uuid = ?
                                                WHERE
                                                  cm.is_active = 1 AND
                                                  cm.uuid != ?
                                                ORDER BY cm.update_time DESC LIMIT 20', [$catalog_id, $sub_category_id, $content_id]);
                $content_details = array_merge($content_details, $related_template_details);
            } else {
                return Response::json(['code' => 201, 'message' => 'Template doesn\'t exist.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $response = Response::json(['code' => 200, 'message' => 'Template details get successfully.', 'cause' => '', 'data' => ['content_details' => $content_details]]);

        } catch (Exception $e) {
            (new ImageController())->logs('getTemplateDetailsBySubCategoryId', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'get templates.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }
}
