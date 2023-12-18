<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests;
use App\Jobs\SendMailJob;
use Illuminate\Support\Facades\Storage;
use JWTAuth;
use JWTFactory;
use Response;
use DB;
use Exception;
use Log;
use Cache;
use HttpHeaderException;
use GuzzleHttp\Client;
use Swagger\Annotations as SWG;
/**
 * Class AdminController
 *
 * @package api\app\Http\Controllers\api
 */
class PixabayController extends Controller
{
    public $total_key;

    public function __construct()
    {
        $keys = Config::get('constant.PIXABAY_API_KEY');
        $key = explode(',', $keys);
        $this->total_key = count($key);
    }

  /* =====================================| Fetch Images |==============================================*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   * path="/getImagesFromPixabay",
   * tags={"Users"},
   * security={
   * {"Bearer": {}},
   * },
   * operationId="getImagesFromPixabay",
   * summary="Get images from pixabay",
   * produces={"application/json"},
   * @SWG\Parameter(
   * in="header",
   * name="Authorization",
   * description="access token",
   * required=true,
   * type="string",
   * ),
   * @SWG\Parameter(
   * in="body",
   * name="request_body",
   * @SWG\Schema(
   * required={"search_query","page","item_count"},
   * @SWG\Property(property="search_query", type="string", example="Forest", description=""),
   * @SWG\Property(property="page", type="integer", example=1, description=""),
   * @SWG\Property(property="item_count",  type="integer", example=5, description="Item count must be >= 3 and <= 200"),
   * ),
   * ),
   * @SWG\Response(
   * response=200,
   * description="success",
   * @SWG\Schema(
   * @SWG\Property(property="Sample_Response", type="string", example={"code":200,"message":"Images fetched successfully.","cause":"","data":{"user_profile_url":"https://pixabay.com/users/","is_next_page":true,"is_cache":0,"result":{"totalHits":500,"hits":{{"largeImageURL":"https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e372e1d611b40c4090f4c97aa7eab0bbd9_1280.jpg","webformatHeight":373,"webformatWidth":640,"likes":1813,"imageWidth":3160,"id":1072823,"user_id":1720744,"views":491468,"comments":205,"pageURL":"https://pixabay.com/en/road-forest-season-autumn-fall-1072823/","imageHeight":1846,"webformatURL":"https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e372e1d611b40c4090f4c97aa7eab0bbd9_640.jpg","type":"photo","previewHeight":87,"tags":"road, forest, season","downloads":195129,"user":"valiunic","favorites":1559,"imageSize":3819762,"previewWidth":150,"userImageURL":"https://cdn.pixabay.com/user/2015/12/01/20-20-44-483_250x250.jpg","previewURL":"https://cdn.pixabay.com/photo/2015/12/01/20/28/road-1072823_150.jpg"}}}}}, description=""),
   * ),
   * ),
   * @SWG\Response(
   * response=201,
   * description="error",
   * ),
   * )
   *
   */
  /**
   * @api {post} getImagesFromPixabay getImagesFromPixabay
   * @apiName getImagesFromPixabay
   * @apiGroup User
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   * Key: Authorization
   * Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "page":1, //compulsory
   * "item_count":50,
   * "search_query":"nature"
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Images fetched successfully.",
   * "cause": "",
   * "data": {
   * "user_profile_url": "https://pixabay.com/users/",
   * "is_next_page": true,
   * "is_cache": 0,
   * "result": {
   * "totalHits": 500,
   * "hits": [
   * {
   * "largeImageURL": "https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e47ee0dc1fb70c4090f5c071a1edb3bcdd_1280.jpg",
   * "webformatHeight": 373,
   * "webformatWidth": 640,
   * "likes": 1918,
   * "imageWidth": 3160,
   * "id": 1072823,
   * "user_id": 1720744,
   * "views": 546053,
   * "comments": 218,
   * "pageURL": "https://pixabay.com/photos/road-forest-season-autumn-fall-1072823/",
   * "imageHeight": 1846,
   * "webformatURL": "https://pixabay.com/get/e835b60d20f6023ed1584d05fb1d4797e47ee0dc1fb70c4090f5c071a1edb3bcdd_640.jpg",
   * "type": "photo",
   * "previewHeight": 87,
   * "tags": "road, forest, season",
   * "downloads": 219192,
   * "user": "valiunic",
   * "favorites": 1634,
   * "imageSize": 3819762,
   * "previewWidth": 150,
   * "userImageURL": "https://cdn.pixabay.com/user/2015/12/01/20-20-44-483_250x250.jpg",
   * "previewURL": "https://cdn.pixabay.com/photo/2015/12/01/20/28/road-1072823_150.jpg"
   * }
   * ],
   * "total": 248144
   * }
   * }
   * }
   */
  public function getImagesFromPixabay(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $keys = Config::get('constant.PIXABAY_API_KEY');
      $request = json_decode($request_body->getContent());

      if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
        return $response;

      $search_query = isset($request->search_query) ? strtolower(trim($request->search_query)) : "";
      $page = $request->page;
      $per_page = $request->item_count;

      if (($response = (new VerificationController())->validateItemCount($per_page)) != '')
        return $response;

      if (strlen($search_query) > 100) {
        return Response::json(array('code' => 201, 'message' => 'The length of your search is too long.', 'cause' => '', 'data' => json_decode("{}")));
      }

      $kt = explode(',', $keys);
      $redis_keys = Redis::keys(Config::get("constant.REDIS_KEY").':currentKey:*');

      count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
      $result = $this->getPixabayImageForUser($search_query, $page, $per_page, $kt[$this->currentKey]);

      if ($result === 429 OR $result === 503 OR $result === 201) {
        return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
      }

      if ($result === 427) {
        if ($search_query) {
          $msg = "Sorry, we couldn't find images for " . $search_query;
        } else {
          $msg = "Sorry, we couldn't find images";
        }
        return Response::json(array('code' => 201, 'message' => $msg, 'cause' => '', 'data' => json_decode('{}')));
      }

      $response = Response::json(array('code' => 200, 'message' => 'Images fetched successfully.', 'cause' => '', 'data' => $result));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

    } catch (Exception $e) {
      (new ImageController())->logs("getImagesFromPixabay",$e);
//      Log::error("getImagesFromPixabay : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  /* =====================================| Fetch Videos |==============================================*/

  /**
   *
   * - Users ------------------------------------------------------
   *
   * @SWG\Post(
   *        path="/getVideosFromPixabay",
   *        tags={"Users"},
   *        security={
   *                  {"Bearer": {}},
   *                 },
   *        operationId="getVideosFromPixabay",
   *        summary="Get videos from pixabay",
   *        produces={"application/json"},
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
   *   	  @SWG\Schema(
   *          required={"search_query","page","item_count"},
   *          @SWG\Property(property="search_query",  type="string", example="Forest", description=""),
   *          @SWG\Property(property="page",  type="integer", example=1, description=""),
   *          @SWG\Property(property="item_count",  type="integer", example=5, description="Item count must be >= 3 and <= 200"),
   *        ),
   *      ),
   * 		@SWG\Response(
   *            response=200,
   *            description="success",
   *        ),
   * 		@SWG\Response(
   *            response=201,
   *            description="error",
   *        ),
   *    )
   *
   */
  public function getVideosFromPixabay(Request $request_body)
  {
    try {
      $keys = Config::get('constant.PIXABAY_API_KEY');
      $request = json_decode($request_body->getContent());

      if (($response = (new VerificationController())->validateRequiredParameter(array('page', 'item_count'), $request)) != '')
        return $response;

      $search_query = isset($request->search_query) ? strtolower(trim($request->search_query)) : "";
      $page = $request->page;
      $per_page = $request->item_count;

      if (($response = (new VerificationController())->validateItemCount($per_page)) != '')
        return $response;

      if (strlen($search_query) > 100) {
        return Response::json(array('code' => 201, 'message' => 'The length of your search is too long.', 'cause' => '', 'data' => json_decode("{}")));
      }

      $kt = explode(',', $keys);
      $redis_keys = Redis::keys(Config::get("constant.REDIS_KEY").':currentKey:*');

      count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
      $result = $this->getPixabayVideoForUser($search_query, $page, $per_page, $kt[$this->currentKey]);

      if ($result === 429) {
        $redis_keys = Redis::keys('Config::get("constant.REDIS_KEY"):currentKey:*');
        count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
        foreach ($redis_keys as $key) {
          //Log::info($key);
          $this->deleteRedisKey($key);
        }

        $getKey = $this->increaseCurrentKey($this->currentKey);

        $currentKey = $getKey + 1;
        if ($currentKey == $this->total_key) {
          $currentKey = 0;
        }
        $template = 'simple';
        $subject = 'PhotoADKing: Pixabay Rate Limit Exceeded';
        //$message_body = "Now, The current key is $currentKey.";
        $message_body = array(
          'message' => "The Rate limit for Pixabay is exceeded now.<br> The updated key is $currentKey.",
          'user_name' => 'Admin'
        );
        $api_name = 'getVideosFromPixabay';
        $api_description = 'Get videos from pixabay for user.';
        $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
        $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

        return Response::json(array('code' => 201, 'message' => 'The server is unable to load videos. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
      }

      $response = Response::json(array('code' => 200, 'message' => 'Videos fetched successfully.', 'cause' => '', 'data' => $result));;
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));
      /*return  $getKey = $this->increaseCurrentKey($this->currentKey);*/

    } catch (Exception $e) {
      (new ImageController())->logs("getVideosFromPixabay",$e);
//      Log::error("getVideosFromPixabay : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get videos from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  public function uploadPixabayVideo(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      $user_detail = JWTAuth::toUser($token);
      $this->user_id = $user_detail->id;

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('pixabay_video_url', 'pixabay_id'), $request)) != '')
        return $response;

      $pixabay_video_url = $request->pixabay_video_url;
      $pixabay_id = $request->pixabay_id;
      $stock_video_path = '../..' . Config::get('constant.STOCK_VIDEOS_IMAGES_DIRECTORY');
      $stock_video_full_path = Config::get('constant.STOCK_VIDEOS_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN');
      //$stock_video_full_path = Config::get('constant.STOCK_VIDEOS_IMAGES_DIRECTORY_OF_S3');

      $old_data = DB::select('SELECT video FROM stock_videos_master WHERE pixabay_id = ?', [$pixabay_id]);

      if ($old_data) {
        $file_name = $old_data[0]->video;

      } else {
        $url = pathinfo($pixabay_video_url, PATHINFO_EXTENSION);
        $extension = strtok($url, '?');
        $file_name = 'stock_videos_id_' . $pixabay_id . '.' . strtolower($extension);
        //ini_set('memory_limit', '640M');
        copy($pixabay_video_url, $stock_video_path . $file_name);

        if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
          $aws_bucket = Config::get('constant.AWS_BUCKET');
          $disk = Storage::disk('s3');

          if (($is_exist = (new ImageController())->checkFileExist($stock_video_path . $file_name) != 0)) {
            $original_targetFile = "$aws_bucket/stock_videos/" . $file_name;
            //$disk->put($original_targetFile, file_get_contents($stock_video_path . $file_name), 'public');
            $disk->put($original_targetFile, fopen($stock_video_path . $file_name, 'r+'), 'public');
            unlink($stock_video_path . $file_name);
          } else {
            Log::error("uploadPixabayVideo : File does not exist : ", ["path" => $stock_video_path . $file_name]);
          }
        }
        DB::insert('INSERT INTO stock_videos_master(video, pixabay_id) VALUES (?, ?) ', [$file_name, $pixabay_id]);
      }

      $response = Response::json(array('code' => 200, 'message' => 'Video uploaded successfully.', 'cause' => '', 'data' => $stock_video_full_path . $file_name));

    } catch (Exception $e) {
      (new ImageController())->logs("uploadPixabayVideo", $e);
      //Log::error("uploadPixabayVideo : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'upload video.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  /* =====================================| Function |==============================================*/

  public function getCurrentKey()
  {
    try {

      $redis_keys = Redis::keys('Config::get("constant.REDIS_KEY"):currentKey*');
      $result = isset($redis_keys) ? $redis_keys : '{}';

      $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

    } catch (Exception $e) {
      (new ImageController())->logs("getCurrentKey",$e);
//      Log::error("getCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

    public function increaseCurrentKey($currentKey)
    {
        try {
            $keys = Config::get('constant.PIXABAY_API_KEY');
            $countKey = count(explode(',', $keys));
            $countKey--;
            $this->currentKey = $currentKey;

            if ($this->currentKey == $countKey) {
                $this->currentKey = 0;
            } else {
                $this->currentKey = $this->currentKey + 1;
            }

            $redis_result = Cache::rememberforever("currentKey:$this->currentKey", function () {
                //Log::info('Current Key :'.$this->currentKey);
                return $this->currentKey;
            });

            $response = $redis_result;
        } catch (Exception $e) {
            (new ImageController())->logs("increaseCurrentKey",$e);
            //Log::error("increaseCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

  public function getPixabayImageForUser($category, $page, $per_page, $key)
  {
    try {
      $this->category = $category;
      $this->page = $page;
      $this->per_page = $per_page;
      $this->is_cache = 1;

      $this->url = Config::get('constant.PIXABAY_API_URL') . '?key=' . $key . '&q=' . $this->category .'&safesearch=true&page=' . $this->page . '&per_page=' . $this->per_page;
//      Log::info('url : ',['url' => $this->url]);

      if (!Cache::has("Config::get('constant.REDIS_KEY'):getPixabayImageForUser:$this->category:$this->page:$this->per_page")) {
        $result = Cache::remember("getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1440, function () {

          $this->is_cache = 0;

          try {
            $client = new Client();
            $host_name = request()->getHttpHost();
            $response = $client->request('get', $this->url);
            $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');

            if (count($this->getRateRemaining) > 0) {
              $rate_remaining = intval($this->getRateRemaining[0]);
            } else {
              $rate_remaining = 0;
            }

            //We changed from 100 to 10, because limit changed from 5000/hr to 100/min
            if ($rate_remaining <= 5) {

              $redis_keys = Redis::keys(Config::get("constant.REDIS_KEY").':currentKey:*');
              count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

              foreach ($redis_keys as $key) {
                $this->deleteRedisKey($key);
              }

              $current_key = $this->currentKey;
              $getKey = $this->increaseCurrentKey($this->currentKey);
              $template = 'simple';
              $subject = "PhotoADKing: Pixabay Rate Limit <= 5 (host: $host_name).";
              $message_body = array(
                'message' => "5 request is remaining from key $current_key.<br>Your currently used key is $getKey.",
                'user_name' => 'Admin'
              );
              $api_name = 'getPixabayImageForUser';
              $api_description = 'Get pixabay image for user.';
              $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
              $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

            }elseif ($rate_remaining <= 10) {

                if ($this->currentKey == $this->total_key) {
                    $this->currentKey = 0;
                }
                $template = 'simple';
                $subject = "PhotoADKing: Pixabay Rate Limit <= 10 (host: $host_name).";
                $message_body = array(
                    'message' => "10 request is remaining from key $this->currentKey.",
                    'user_name' => 'Admin'
                );
                $api_name = 'getPixabayImageForUser';
                $api_description = 'Get pixabay image for user.';
                $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
                $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));
            }

            $http_status = $response->getStatusCode();
            $result = json_decode($response->getBody()->getContents(), true);

          } catch (Exception $e) {
            (new ImageController())->logs("getPixabayImageForUser Exception",$e);
            Log::debug('getPixabayImageForUser Exception', ['getCode' => $e->getCode()]);
            // $http_status = $e->getResponse()->getStatusCode();
            $result = $e->getResponse()->getBody()->getContents();
            Log::debug('getPixabayImageForUser Exception', ['getStatusCode' => $http_status]);
          }
          return ['http_status' => $http_status, 'result' => $result];
        });
      }

      $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;

      $redis_result = Cache::get("getPixabayImageForUser:$this->category:$this->page:$this->per_page");

//      Redis::expire("Config::get('constant.REDIS_KEY'):getPixabayImageForUser:$this->category:$this->page:$this->per_page", 1);
      //Log::info($redis_result);
      $http_status = $redis_result['http_status'];
      $result = $redis_result['result'];
      if ($http_status === 429) {
        Log::error("getPixabayImageForUser: \n", ["http_status_code" => $http_status, "\nresult" => $result]);
        $this->deleteRedisKey("Config::get('constant.REDIS_KEY'):getPixabayImageForUser:$this->category:$this->page:$this->per_page");

        $redis_keys = Redis::keys('Config::get("constant.REDIS_KEY"):currentKey:*');
        count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;
        foreach ($redis_keys as $key) {
          //Log::info($key);
          $this->deleteRedisKey($key);
        }

        $getKey = $this->increaseCurrentKey($this->currentKey);

        $currentKey = $getKey + 1;
        if ($currentKey == $this->total_key) {
          $currentKey = 0;
        }
        $template = 'simple';
        $host_name = request()->getHttpHost();
        $subject = "PhotoADKing: Pixabay rate limit exceeded(host: $host_name).";
        $message_body = array(
          'message' => "The Rate limit for Pixabay is exceeded now.<br> The updated key is $currentKey.",
          'user_name' => 'Admin'
        );
        $api_name = 'getPixabayImageForUser';
        $api_description = 'Get images from pixabay for user.';
        $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
        $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

        return 429;
      }

      if ($http_status == 503) {
        if ($is_cache == 0) {
          Log::error("getPixabayImageForUser failed to fetch stock images. Pixabay site is in maintenance mode : \n", ["http_status_code" => $http_status, "\nresult" => $result]);
          $host_name = request()->getHttpHost();
          $template = 'simple';
          $subject = "PhotoADKing: Pixabay failed to fetch stock images (host: $host_name).";
          $message_body = array(
            'message' => "getPixabayImageForUser failed to fetch stock images. Pixabay site is in maintenance mode.<br><span style='font-size: medium; font-weight: bold'>HTTP status codes : $http_status </span> <br> Error : $result",
            'user_name' => 'Admin'
          );
          $api_name = 'getPixabayImageForUser';
          $api_description = 'Get pixabay image for user.';
          $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
          $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));
        }
        return 503;
      }

      if ($http_status != 200) {

        if ($is_cache == 0) {
          Log::error("getPixabayImageForUser failed to fetch stock images : \n", ["http_status_code" => $http_status, "\nresult" => $result]);
          $host_name = request()->getHttpHost();
          $template = 'simple';
          $subject = "PhotoADKing: Pixabay failed to fetch stock images (host: $host_name).";
          $message_body = array(
            'message' => "getPixabayImageForUser failed to fetch stock images.<br><span style='font-size: medium; font-weight: bold'>HTTP status codes : $http_status </span> <br> Error : $result",
            'user_name' => 'Admin'
          );
          $api_name = 'getPixabayImageForUser';
          $api_description = 'Get pixabay image for user.';
          $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
          $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));
        }

        return 201;
      }

      $total_hits = count($result['hits']);
      if ($total_hits == 0) {
        return 427;
      }

      $total_row = $result['totalHits'];
      $this->offset = ($page - 1) * $this->per_page;
      $is_next_page = ($total_row >= ($this->offset + $this->per_page)) ? true : false;
      return ['user_profile_url' => 'https://pixabay.com/users/', 'is_next_page' => $is_next_page, 'is_cache' => $is_cache, 'result' => $result];

    } catch (Exception $e) {
      (new ImageController())->logs("getPixabayImageForUser",$e);
//      Log::error("getPixabayImageForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get images from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

    public function getPixabayVideoForUser($category, $page, $per_page, $key)
    {
        try {
            $this->category = $category;
            $this->page = $page;
            $this->per_page = $per_page;
            $this->is_cache = 1;
            $this->url = Config::get('constant.PIXABAY_API_URL') . 'videos/?key=' . $key . '&q=' . $this->category . '&safesearch=true&page=' . $this->page . '&per_page=' . $this->per_page;

            $redis_result = Cache::remember("getPixabayVideoForUser:$this->category:$this->page:$this->per_page", 1440, function () {

                $this->is_cache = 0;
                $client = new Client();
                $host_name = request()->getHttpHost();
                try {
                    $response = $client->request('get', $this->url);
                    $this->getRateRemaining = $response->getHeader('X-RateLimit-Remaining');

                    if (count($this->getRateRemaining) > 0) {
                        $rate_remaining = intval($this->getRateRemaining[0]);
                    } else {
                        $rate_remaining = 0;
                    }

                    //We changed from 100 to 10, because limit changed from 5000/hr to 100/min
                    if ($rate_remaining <= 5) {

                        $redis_keys = Redis::keys(Config::get("constant.REDIS_KEY").':currentKey:*');
                        count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

                        foreach ($redis_keys as $key) {
                          $this->deleteRedisKey($key);
                        }

                        $current_key = $this->currentKey;
                        $getKey = $this->increaseCurrentKey($this->currentKey);
                        $template = 'simple';
                        $subject = "PhotoADKing: Pixabay Rate Limit <= 5 (host: $host_name).";
                        $message_body = array(
                          'message' => "5 request is remaining from key $current_key.<br>Your currently used key is $getKey.",
                          'user_name' => 'Admin'
                        );
                        $api_name = 'getPixabayImageForUser';
                        $api_description = 'Get pixabay image for user.';
                        $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
                        $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    }elseif ($rate_remaining <= 10) {

                        if ($this->currentKey == $this->total_key) {
                            $this->currentKey = 0;
                        }
                        $template = 'simple';
                        $subject = "PhotoADKing: Pixabay Rate Limit <= 10 (host: $host_name).";
                        $message_body = array(
                            'message' => "10 request is remaining from key $this->currentKey.",
                            'user_name' => 'Admin'
                        );
                        $api_name = 'getPixabayImageForUser';
                        $api_description = 'Get pixabay image for user.';
                        $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
                        $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));
                    }
                    return json_decode($response->getBody()->getContents());

                } catch (Exception $e) {
                    (new ImageController())->logs("deleteRelatedTags",$e);
                    return $e->getResponse()->getStatusCode();
                }
            });

            $this->is_cache == 0 ? $is_cache = 0 : $is_cache = 1;
            //Redis::expire("Config::get('constant.REDIS_KEY'):getPixabayVideoForUser:$this->category:$this->page:$this->per_page", 1);

            if ($redis_result === 429) {
                $this->deleteRedisKey("Config::get('constant.REDIS_KEY'):getPixabayVideoForUser:$this->category:$this->page:$this->per_page");
                return 429;
                /*$response = Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));*/
            }

            if (!$redis_result) {
                $redis_result = [];
            }

            $this->offset = ($page - 1) * $this->per_page;
            $total_row = $redis_result->totalHits;
            //$total_row = $redis_result['totalHits'];
            $is_next_page = ($total_row > ($this->offset + $this->per_page)) ? true : false;
            return ["user_profile_url"=> "https://pixabay.com/users/",'is_next_page' => $is_next_page, 'is_cache' => $is_cache, 'result' => $redis_result];

            //return ['is_cache' => $is_cache, 'result' => $redis_result];

        } catch (Exception $e) {
            (new ImageController())->logs("getPixabayVideoForUser",$e);
            //Log::error("getPixabayVideoForUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get videos from pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function deleteRedisKey($keys)
    {
        try {
            Redis::del($keys);
            $redis_keys = Redis::keys($keys);

            $response = $redis_keys ? 0 : 1;
        } catch (Exception $e) {
            (new ImageController())->logs("deleteRedisKey",$e);
            //Log::error("deleteRedisKey : ", ["Exception" => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete redis keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }
}
