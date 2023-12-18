<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Log;
use Response;

class SearchTagAnalyticsController extends Controller
{
    /**
     * *
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getSearchAnalytics",
     *        tags={"Admin"},
     *        operationId="getSearchAnalytics",
     *        summary="getSearchAnalytics",
     *        produces={"application/json"},
     *
     *        @SWG\Parameter(
     *       in="header",
     *       name="Authorization",
     *       description="bearer token",
     *       required=true,
     *       type="string",
     *      ),
     *        @SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *       @SWG\Schema(
     *          required={"date"},
     *
     *          @SWG\Property(property="date",  type="string", example="20-04-2020", description=""),
     *          @SWG\Property(property="end_date",  type="string", example="26-04-2020", description=""),
     *          @SWG\Property(property="item_count",  type="integer", example="10", description=""),
     *        ),
     *      ),
     *
     *        @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Get search analytics successfully.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoAdking is unable to .....","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    /**
     * @api {post} getSearchAnalytics getSearchAnalytics
     *
     * @apiName getSearchAnalytics
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
     * "date":"20-04-2020",
     * "end_date":"20-04-2020",//optional
     * "item_count":10,//optional
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "Get search analytics successfully.",
     * "cause": "",
     * "data": {}
     * }
     */
    public function getSearchAnalytics(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['date', 'item_count'], $request)) != '') {
                return $response;
            }

            $item_count = isset($request->item_count) && ($request->item_count) ? $request->item_count : 10;
            $date = date('Y-m-d', strtotime($request->date));
            $end_date = isset($request->end_date) && ($request->end_date) ? date('Y-m-d', strtotime($request->end_date)) : '';
            if ($end_date) {
                $where = "DATE_FORMAT(tam.week_start_date, '%Y-%m-%d') >= '$date' AND
                          DATE_FORMAT(tam.week_end_date, '%Y-%m-%d') <= '$end_date'";
            } else {
                $where = "DATE_FORMAT(tam.update_time,'%Y-%m-%d')='$date'";
            }
            $data = DB::select('(SELECT 
                                    tam.id,
                                    tam.tag,
                                    tam.is_success,
                                    tam.content_count,
                                    scm.sub_category_name,
                                    tam.content_type,
                                    tam.search_count,
                                    CASE WHEN tam.content_type = 4 THEN "Image"
                                         WHEN tam.content_type = 9 THEN "Video"
                                         ELSE "All"
                                         END AS content_type,
                                    tam.update_time 
                                 FROM tag_analysis_master as tam
                                 LEFT JOIN  
                                    sub_category_master as scm
                                 ON 
                                    scm.id=tam.sub_category_id
                                 WHERE tam.is_success=1 AND 
                                    '.$where.'
                                    LIMIT ?)
                                 UNION  
                                 (SELECT
                                    tam.id,
                                    tam.tag,
                                    tam.is_success,
                                    tam.content_count,
                                    scm.sub_category_name,
                                    tam.content_type,
                                    tam.search_count,
                                    CASE WHEN tam.content_type = 4 THEN "Image"
                                         WHEN tam.content_type = 9 THEN "Video"
                                         ELSE "All"
                                         END AS content_type,
                                    tam.update_time 
                                  FROM tag_analysis_master as tam
                                  LEFT JOIN  
                                    sub_category_master as scm
                                  ON 
                                    scm.id=tam.sub_category_id
                                  WHERE 
                                    tam.is_success=2 AND 
                                    '.$where.'
                                    LIMIT ?)
                                 ORDER BY is_success,search_count DESC', [$item_count, $item_count]);

            return Response::json(['code' => 200, 'message' => 'Get search analytics successfully.', 'cause' => '', 'data' => $data]);
        } catch (Exception $e) {
            (new ImageController())->logs('getSearchAnalytics', $e);
            //      Log::error("getSearchAnalytics : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(['code' => 201, 'message' => Config('constants.EXCEPTION_ERROR').' get search analitics', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }
    }
}
