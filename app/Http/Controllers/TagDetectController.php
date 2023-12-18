<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Clarifai\API\ClarifaiClient;
use Clarifai\DTOs\Inputs\ClarifaiFileImage;
use Clarifai\DTOs\Outputs\ClarifaiOutput;
use Clarifai\DTOs\Predictions\Concept;
use Clarifai\DTOs\Inputs\ClarifaiURLImage;
use Cache;
use Illuminate\Support\Facades\Input;
use Config;
use Response;
use App\Jobs\SendMailJob;
use Log;
use Illuminate\Support\Facades\Redis;

class TagDetectController extends Controller
{
    public function getTagInImageByViaURL(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(array('image_url'), $request)) != '')
                return $response;
            $image_url = $request->image_url;
        } catch (Exception $e) {
            (new ImageController())->logs("getTagInImageByViaURL",$e);
//            Log::error("getTagInImageByViaURL : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get tag in image by via URL.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollback();
        }
        return $response;
    }

    public function getTagInImageByBytes($photo)
    {
        try {

                //$keys = Config::get('constant.CLARIFAI_API_KEY');
                $tag = "";
                $keys = Config::get('constant.CLARIFAI_API_KEY', 'f3ac62c4427847d9aefda435f9c59185');

                //Temp Log Added By NRA
                // Log::error('getTagInImageByBytes : crarifai keys : ',[$keys]);

                $key = explode(',', $keys);
                foreach($key AS $i => $kt) {

                  $redis_keys = Redis::keys(Config::get("constant.REDIS_KEY").':currentKeyForGetTag:*');
                  count($redis_keys) > 0 ? $this->currentKey = substr($redis_keys[0], -1) : $this->currentKey = 0;

                  /*return $kt[$this->currentKey];*/

                  //Temp Log Added By NRA
                  // Log::error('getTagInImageByByte : Current Key :', ['API Key' => $kt[$this->currentKey]]);
                  // Log::error('getTagInImageByByte : ',['redis_keys'=>count($redis_keys),'currentKey'=>$this->currentKey]);

                  $result = $this->getTagByImage($photo, $key[$this->currentKey]);

                  //Temp Log Added By NRA
                  // Log::error('getTagInImageByBytes : results : ',[$result]);

                  if ($result['is_success'] == 0) {
                    $expire_key =  str_repeat('*', strlen($key[$this->currentKey]) - 4).substr($key[$this->currentKey], -4);  //get last 4 character

                    if ($redis_keys) {
                      $this->deleteRedisKey($redis_keys[0]);
                    }

                    $getKey = $this->increaseCurrentKey($this->currentKey);

                    $currentKey = $getKey + 1;
                    $template = 'simple';
                    $subject = 'Clarify account limits exceeded';
                    //$message_body = "Error Code : " . $result['statusCode'] . "<br>" . $result['description'] . "<br>Now, Current key is $currentKey";
                    $message_body = array(
                      'message' => "Clarify $result[description] . <br> The expire key is <b> $expire_key </b>",
                      'user_name' => 'Admin'
                    );
                    $api_name = 'getTagInImageByViaBytes';
                    $api_description = $result['description'];
                    $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
                    $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    //return Response::json(array('code' => 201, 'message' => 'The server is unable to load images. Please try again after 10-20 minutes.', 'cause' => '', 'data' => json_decode('{}')));
                    $tag = "";
                  } else {
                    $tag = $result['tag'];
                    break;
                  }
                }

        } catch (Exception $e) {
            (new ImageController())->logs("getTagInImageByBytes",$e);
//            Log::error("getTagInImageByBytes : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            //$response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'detect lable from image by amazon.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            $tag = "";
        }
        return $tag;

    }

    /* =====================================| Function |==============================================*/

    public function getTagByImage($photo, $currentKey)
    {
        try {
            //$client = new ClarifaiClient('bee6bec4edc24f9192feee0db7131ffc');
            $client = new ClarifaiClient($currentKey);

            $response = $client->publicModels()->generalModel()->predict(
                new ClarifaiFileImage(file_get_contents($photo)))
                ->executeSync();
            /*return $response->status()->statusCode();*/

            if ($response->isSuccessful()) {
                /** @var ClarifaiOutput $output */
                $output = $response->get();

                //echo "Predicted concepts:\n";
                /** @var Concept $concept */
                $tag = [];
                foreach ($output->data() as $concept) {
                    $tag[] = $concept->name();
                }
                $tag = implode(",", $tag);
                $result = ['is_success' => 1, 'tag' => $tag];
            } else {
              //Temp Log Added By NRA
//              Log::error('getTagInImageByByte: getTagByImage:', ['Plain Response' => $response->status()->errorDetails()]);

            $result = ['is_success' => 0, 'description' => $response->status()->description(), 'statusCode' => $response->status()->statusCode()];

            }

        } catch (Exception $e) {
            (new ImageController())->logs("getTagByImage",$e);
//            Log::error("getTagByImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $result = array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'detect label from image by amazon.', 'cause' => $e->getMessage(), 'data' => json_decode("{}"));
        }
        return $result;
    }

    public function getCurrentKey()
    {
        try {

            $redis_keys = Redis::keys('Config::get("constant.REDIS_KEY"):currentKeyForGetTag:*');
            $key = explode(',', $redis_keys);

            $result = isset($redis_keys) ? $redis_keys : '{}';

            $response = Response::json(array('code' => 200, 'message' => 'Current key fetched successfully.', 'cause' => '', 'data' => $result));
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs("getCurrentKey",$e);
//            Log::error("getCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get current key.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
            DB::rollBack();
        }
        return $response;
    }

    public function increaseCurrentKey($currentKey)
    {
        try {
            //$keys = Config::get('constant.CLARIFAI_API_KEY');
            $keys = Config::get('constant.CLARIFAI_API_KEY', 'f3ac62c4427847d9aefda435f9c59185');
            $countKey = count(explode(',', $keys));
            $countKey--;

            $this->currentKey = $currentKey;
            if ($this->currentKey == $countKey) {
                //Log::info('$this->currentKey = 0');
                $this->currentKey = 0;
            } else {
                //Log::info('$this->currentKey = $this->currentKey + 1');
                $this->currentKey = $this->currentKey + 1;
            }

            /*if (!Cache::has("aud:currentKeyForGetTag:$this->currentKey")) {
                $result = Cache::rememberforever("currentKeyForGetTag:$this->currentKey", function () {
                    Log::info('Current Key :' . $this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKeyForGetTag:$this->currentKey");*/

            if (!Cache::has("Config::get('constant.REDIS_KEY'):currentKeyForGetTag:$this->currentKey")) {
                $result = Cache::rememberforever("currentKeyForGetTag:$this->currentKey", function () {
                    //Log::info('Current Key :' . $this->currentKey);
                    return $this->currentKey;
                });
            }
            $redis_result = Cache::get("currentKeyForGetTag:$this->currentKey");


            /*Redis::expire("currentKeyForGetTag:$this->currentKey", 1);*/
            $response = $redis_result;
        } catch (Exception $e) {
            (new ImageController())->logs("increaseCurrentKey",$e);
//            Log::error("increaseCurrentKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'change API key of pixabay.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
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
//            Log::error("deleteRedisKey : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'Delete Redis Keys.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

}
