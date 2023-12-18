<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Config;
use DB;
use Log;
use Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\VerificationController;


class PaymentModuleController extends Controller
{

  /*-------------------------------------------------ADMIN------------------------------------------------*/

  /*------------------------------------Product-----------------------------------*/
  /**
   * @api {post} createProduct createProduct
   * @apiName createProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "name":"Photoadking payment module",
   * "discount_percentage":"0",
   * "is_applied":1,
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Product added successfully.",
   * "cause": "",
   * "data": {}
   * }
   */
  public function createProduct(Request $request_body){
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      //Log::info("request data :", [$request]);
      if (($response = (new VerificationController())->validateRequiredParameter(array('name','discount_percentage'), $request)) != '')
        return $response;

      $name = trim($request->name);
      $discount_percentage = $request->discount_percentage;
      $is_applied = 0;
      $create_time = date('Y-m-d H:i:s');

      DB::beginTransaction();
      DB::insert('INSERT INTO subscription_product_details(name,discount_percentage,is_applied,create_time) VALUES(?, ?, ?, ?)',
        [$name, $discount_percentage, $is_applied, $create_time]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Product added successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("createProduct",$e);
//      Log::error("createProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;
  }

  /**
   * @api {post} updateProduct updateProduct
   * @apiName updateProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "product_id":1,
   * "name":"Photoadking payment module",
   * "discount_percentage":"0",
   * "is_applied":1,
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Product updated successfully.",
   * "cause": "",
   * "data": {}
   * }
   */
  public function updateProduct(Request $request_body){
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('product_id','name','discount_percentage','is_applied'), $request)) != '')
        return $response;

      $product_id = $request->product_id;
      $name = trim($request->name);
      $discount_percentage = $request->discount_percentage;
      $is_applied = $request->is_applied;


      if($is_applied == 1){
        if (($response = (new VerificationController())->isAbleToApply($product_id)) != '')
            return $response;
      }
      if(isset($request->is_update_pricing)){
            if(($result = (new verificationController())->UpdatePricingByProductId($product_id,$discount_percentage)) != 1){
                return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update pricing by product.', 'cause' => '', 'data' => json_decode("{}")));
            }
      }

      DB::beginTransaction();
      DB::update('UPDATE subscription_product_details SET name = ?, discount_percentage = ?, is_applied = ? WHERE id = ?',
        [$name, $discount_percentage, $is_applied, $product_id]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Product updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("updateProduct",$e);
//      Log::error("updateProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;
  }

  /**
   * @api {post} deleteProduct deleteProduct
   * @apiName deleteProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "product_id":1,
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Product deleted successfully.",
   * "cause": "",
   * "data": {}
   * }
   */
  public function deleteProduct(Request $request_body){
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('product_id'), $request)) != '')
        return $response;

      $product_id = $request->product_id;

      DB::beginTransaction();
      DB::delete('DELETE FROM subscription_pricing_details WHERE product_id = ?', [$product_id]);
      DB::delete('DELETE FROM subscription_product_details WHERE id = ?', [$product_id]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Product deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("DeleteProduct",$e);
//      Log::error("DeleteProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;
  }

  /**
   * @api {post} getAllProducts getAllProducts
   * @apiName getAllProducts
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
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
   * "message": "All Product fetched successfully.",
   * "cause": "",
   * "data": {"result":[{"product_id":1,"name":"PAK payment","discount_percentage":10,"is_applied":1},{"product_id":2,"name":"PAK new payment","discount_percentage":50,"is_applied":0}]}
   * }
   */
  public function getAllProducts(Request $request_body){
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      if (!Cache::has("Config::get('constant.REDIS_KEY'):getAllProducts")) {
        $result = Cache::rememberforever("getAllProducts", function () {

          return DB::select('SELECT id AS product_id,
                        name,
                        discount_percentage,
                        is_applied,
                        create_time,
                        update_time
                        FROM subscription_product_details');
        });
      }

      $redis_result = Cache::get("getAllProducts");

      if (!$redis_result) {
        $redis_result = [];
      }

      $response = Response::json(array('code' => 200, 'message' => 'All products fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

    } catch (Exception $e) {
      (new ImageController())->logs("getAllCategory",$e);
//      Log::error("getAllCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get all products.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;


  }

  /*------------------------------------Pricing-----------------------------------*/

  /**
   * @api {post} addPricingToProduct addPricingToProduct
   * @apiName addPricingToProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "product_id":1,
   * "plan_name":"monthly starter",
   * "button_id":"1254g25",
   * "coupon_id":2,
   * "actual_amount":10,
   * "payable_amount":9,
   * "currency":"USD",
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Pricing added successfully.",
   * "cause": "",
   * "data": {}
   * }
   */
  public function addPricingToProduct(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('product_id' ,'plan_interval' ,'plan_title', 'button_id', 'fs_product_id', 'actual_amount', 'payable_amount', 'actual_amount_inr', 'payable_amount_inr','currency', 'is_active'), $request)) != '')
        return $response;

      $product_id = $request->product_id;
      $plan_interval = $request->plan_interval;
      $plan_title = $request->plan_title;
      $button_id = $request->button_id;
      $fs_product_id = $request->fs_product_id;
//      $coupon_id = $request->coupon_id;
      $actual_amount = $request->actual_amount;
      $payable_amount = $request->payable_amount;
      $actual_amount_inr = $request->actual_amount_inr;
      $payable_amount_inr = $request->payable_amount_inr;
      $currency = $request->currency;
      $is_active = $request->is_active;

      if (($response = (new VerificationController())->checkIfPricingExist($plan_title,$plan_interval,$pricing_id=null,$product_id)) != '')
          return $response;


        if(($plan_id = (new verificationController())->getPlanIdFromPlanName($plan_interval,$plan_title)) == 0){
          return Response::json(array('code' => 201, 'message' => 'Selected plan not registered, contact admin to add your plan.', 'cause' => '', 'data' => json_decode('{}')));;
      }

      $verify_button = DB::select('SELECT 1 FROM subscription_pricing_details WHERE button_id = ? AND payable_amount != ?',[$button_id,$payable_amount]);
      if(count($verify_button) > 0 ){
          return Response::json(array('code' => 201, 'message' => 'The same button ID has already been added to another plan, please check again.', 'cause' => '', 'data' => json_decode('{}')));;
      }

      DB::beginTransaction();
      DB::insert('INSERT INTO subscription_pricing_details
            (product_id,
            plan_id,
            plan_interval,
            plan_title,
            button_id,
            fs_product_id,
            actual_amount,
            payable_amount,
            actual_amount_inr,
            payable_amount_inr,
            currency,
            is_active)
                  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [$product_id,$plan_id,$plan_interval,$plan_title,$button_id,$fs_product_id,$actual_amount,$payable_amount,$actual_amount_inr,$payable_amount_inr,$currency,$is_active]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Pricing added successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("addPricingToProduct",$e);
//      Log::error("addPricingToProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'add pricing.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  /**
   * @api {post} updatePricingToProduct updatePricingToProduct
   * @apiName updatePricingToProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "pricing_id":1,
   * "product_id":1,
   * "plan_name":"monthly starter",
   * "button_id":"1254g25",
   * "coupon_id":2,
   * "actual_amount":10,
   * "payable_amount":9,
   * "currency":"USD",
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Pricing updated successfully.",
   * "cause": "",
   * "data": {}
   * }
   */
  public function updatePricingToProduct(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('pricing_id', 'product_id', 'plan_interval' ,'plan_title', 'button_id', 'fs_product_id', 'actual_amount', 'payable_amount', 'actual_amount_inr', 'payable_amount_inr', 'currency', 'is_active'), $request)) != '')
        return $response;

      $pricing_id = $request->pricing_id;
      $product_id = $request->product_id;
      $plan_interval = $request->plan_interval;
      $plan_title = $request->plan_title;
      $button_id = $request->button_id;
      $fs_product_id = $request->fs_product_id;
      $actual_amount = $request->actual_amount;
      $payable_amount = $request->payable_amount;
      $actual_amount_inr = $request->actual_amount_inr;
      $payable_amount_inr = $request->payable_amount_inr;
      $currency = $request->currency;
      $is_active = $request->is_active;

      if (($response = (new VerificationController())->checkIfPricingExist($plan_title,$plan_interval,$pricing_id,$product_id)) != '')
        return $response;


        if(($plan_id = (new verificationController())->getPlanIdFromPlanName($plan_interval,$plan_title)) == 0){
            return Response::json(array('code' => 201, 'message' => 'Selected plan not registered, contact admin to add your plan.', 'cause' => '', 'data' => json_decode('{}')));
        }

        $verify_button = DB::select('SELECT 1 FROM subscription_pricing_details WHERE button_id = ? AND payable_amount != ? AND id != ? ',[$button_id,$payable_amount,$pricing_id]);
        if(count($verify_button) > 0 ){
            return Response::json(array('code' => 201, 'message' => 'The same button ID has already been added to another plan, please check again.', 'cause' => '', 'data' => json_decode('{}')));;
        }

          DB::beginTransaction();
          DB::update('UPDATE subscription_pricing_details
                      SET product_id = ?,
                          plan_id = ?,
                          plan_interval = ?,
                          plan_title = ?,
                          button_id = ?,
                          fs_product_id = ?,
                          actual_amount = ?,
                          payable_amount = ?,
                          actual_amount_inr = ?,
                          payable_amount_inr = ?,
                          currency = ?,
                          is_active = ?
                      WHERE id = ?',
            [$product_id,$plan_id,$plan_interval,$plan_title,$button_id,$fs_product_id,$actual_amount,$payable_amount,$actual_amount_inr,$payable_amount_inr,$currency,$is_active,$pricing_id]);
          DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Pricing updated successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("updatePricingToProduct",$e);
//      Log::error("updatePricingToProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update444 pricing.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  /**
   * @api {post} deletePricingToProduct deletePricingToProduct
   * @apiName deletePricingToProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
   * @apiSuccessExample Request-Header:
   * {
   *  Key: Authorization
   *  Value: Bearer token
   * }
   * @apiSuccessExample Request-Body:
   * {
   * "pricing_id":1,
   * }
   * @apiSuccessExample Success-Response:
   * {
   * "code": 200,
   * "message": "Pricing deleted successfully.",
   * "cause": "",
   * "data": {}
   * }
   */
  public function deletePricingToProduct(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('pricing_id'), $request)) != '')
        return $response;

      $pricing_id = $request->pricing_id;

      DB::beginTransaction();
      DB::delete('DELETE FROM subscription_pricing_details WHERE id = ?', [$pricing_id]);
      DB::commit();

      $response = Response::json(array('code' => 200, 'message' => 'Pricing deleted successfully.', 'cause' => '', 'data' => json_decode('{}')));
    } catch (Exception $e) {
      (new ImageController())->logs("deletePricingToProduct",$e);
//      Log::error("deletePricingToProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'delete pricing.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;

  }

  /**
   * @api {post} getPricingByProduct getPricingByProduct
   * @apiName getPricingByProduct
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
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
   * "message": "All pricing fetched successfully.",
   * "cause": "",
   * "data": {"result":[{"pricing_id":2,"product_id":1,"plan_name":"Monthly starter","button_id":"1254","coupon_id":1,"actual_amount":10,"payable_amount":9,"currency":"USD"},{"pricing_id":3,"product_id":1,"plan_name":"Yearly starter","button_id":"5241","coupon_id":1,"actual_amount":100,"payable_amount":90,"currency":"USD"}]}
   * }
   */
  public function getPricingByProduct(Request $request_body){
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      $request = json_decode($request_body->getContent());
      if (($response = (new VerificationController())->validateRequiredParameter(array('product_id'), $request)) != '')
        return $response;

      $this->product_id = $request->product_id;


      if (!Cache::has("Config::get('constant.REDIS_KEY'):getAllProducts$this->product_id")) {
        $result = Cache::rememberforever("getPricingByProduct$this->product_id", function () {

          return DB::select('SELECT id AS pricing_id,
                        product_id,
                        plan_interval,
                        plan_title,
                        button_id,
                        fs_product_id,
                        actual_amount,
                        payable_amount,
                        COALESCE(actual_amount_inr, 0) AS actual_amount_inr,
                        COALESCE(payable_amount_inr, 0) AS payable_amount_inr,
                        currency,
                        is_active,
                        create_time,
                        update_time
                        FROM subscription_pricing_details WHERE product_id  = ?',[$this->product_id]);
        });
      }

      $redis_result = Cache::get("getPricingByProduct$this->product_id");

      if (!$redis_result) {
        $redis_result = [];
      }

      $response = Response::json(array('code' => 200, 'message' => 'All pricing fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

    } catch (Exception $e) {
      (new ImageController())->logs("getPricingByProduct",$e);
//      Log::error("getPricingByProduct : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get pricing by products.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;


  }

  /**
   * @api {post} getCouponFromStripe getCouponFromStripe
   * @apiName getCouponFromStripe
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
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
   * "message": "Coupons details fetched successfully.",
   * "cause": "",
   * "data": {"coupon_detail":[{"id":"2","object":"coupon","amount_off":null,"created":1589794177,"currency":null,"duration":"forever","duration_in_months":null,"livemode":false,"max_redemptions":null,"metadata":[],"name":"New discount","percent_off":50,"redeem_by":null,"times_redeemed":116,"valid":true},{"id":"1","object":"coupon","amount_off":null,"created":1588941489,"currency":null,"duration":"once","duration_in_months":null,"livemode":false,"max_redemptions":null,"metadata":[],"name":"PhotoAdking discount coupon","percent_off":50,"redeem_by":null,"times_redeemed":15,"valid":true}]}
   * }
   */
  public function getCouponFromStripe(Request $request_body)
  {
    try {
      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);

      \Stripe\Stripe::setApiKey(Config::get('constant.STRIPE_API_KEY'));

      $coupon_details = \Stripe\Coupon::all();

      $coupons = $coupon_details->data;
      Log::info('data : ',[$coupons]);

      $response = Response::json(array('code' => 200, 'message' => 'Coupons details fetched successfully.', 'cause' => '', 'data' => ['coupon_detail' =>$coupons]));
    } catch (Exception $e) {
      (new ImageController())->logs("getCouponFromStripe",$e);
//      Log::error("getCouponFromStripe : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'fetch coupons details.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
      DB::rollBack();
    }
    return $response;
  }

  /*-------------------------------------------------User------------------------------------------------*/


  /**
   * @api {post} getPricingByUser getPricingByUser
   * @apiName getPricingByUser
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
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
   * "message": "Pricing details fetched successfully.",
   * "cause": "",
   * "data": {"result":{"product_id":1,"product_name":"PAK payment","discount_percentage":10,"plans":[{"pricing_id":2,"product_id":1,"plan_name":"Monthly starter","button_id":"1254","coupon_id":1,"actual_amount":10,"payable_amount":9,"currency":"USD"},{"pricing_id":3,"product_id":1,"plan_name":"Yearly starter","button_id":"5241","coupon_id":1,"actual_amount":100,"payable_amount":90,"currency":"USD"},{"pricing_id":7,"product_id":1,"plan_name":"Yearly pro","button_id":"585241","coupon_id":2,"actual_amount":1000,"payable_amount":900,"currency":"INR"},{"pricing_id":8,"product_id":1,"plan_name":"Monthly pro","button_id":"8941","coupon_id":1,"actual_amount":200,"payable_amount":190,"currency":"INR"}]}}
   * }
   */
  public function getPricingByUser(Request $request_body){
    try {

      $token = JWTAuth::getToken();
      JWTAuth::toUser($token);
      $this->user_id = JWTAuth::toUser($token)->id;

//      Log::info('user_id :',[$this->user_id]);

      $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$this->user_id]);
      $user_role_id = $get_user_role[0]->role_id;
//      Log::info('$user_role_id :',[$user_role_id]);
      $free_user_role = Config::get('constant.ROLE_ID_FOR_FREE_USER');
//      $currency = (new verificationController())->VerifyIsIndianUser($this->user_id);
//      $this->currency_condition = ' AND currency = "'.$currency.'"';
//      Log::info('$this->currency_condition :',[$this->currency_condition]);
      If($user_role_id == $free_user_role){

        if (!Cache::has("Config::get('constant.REDIS_KEY'):getPricingByUser$user_role_id")) {
          $result = Cache::rememberforever("getPricingByUser$user_role_id", function () {

            $product_details = DB::select('SELECT id AS product_id,
              name AS product_name,
              discount_percentage
              FROM subscription_product_details
              WHERE is_applied = 1');

            if(count($product_details) > 0){
                $product_id = $product_details[0]->product_id;

                $pricing_details =  DB::select('SELECT spd.id AS pricing_id,
                        spd.product_id,
                        spd.plan_interval,
                        spd.plan_title,
                        spd.plan_id,
                        spd.button_id,
                        spd.fs_product_id,
                        spd.actual_amount,
                        spd.payable_amount,
                        COALESCE(spd.actual_amount_inr, 0) AS actual_amount_inr,
                        COALESCE(spd.payable_amount_inr, 0) AS payable_amount_inr,
                        spd.currency,
                        spd.is_active,
                        spd.create_time,
                        spd.update_time
                        FROM subscription_pricing_details AS spd WHERE product_id  = ?
                        ORDER BY plan_id ',[$product_id]);

                $product_details[0]->plans = $pricing_details;
                return $product_details[0];
            }
          });
        }
        $redis_result = Cache::get("getPricingByUser$user_role_id");

      }else {

        if (!Cache::has("Config::get('constant.REDIS_KEY'):getPricingByUser$user_role_id:$this->user_id")) {
          $result = Cache::rememberforever("getPricingByUser$user_role_id:$this->user_id", function () {

            $product_details = DB::select('SELECT spd.id AS product_id,
                                        spd.name AS product_name,
                                        spd.discount_percentage FROM subscriptions AS sub
                                            LEFT JOIN subscription_product_details AS spd ON sub.product_id = spd.id
                                         WHERE sub.user_id = ? ORDER BY sub.update_time DESC',[$this->user_id]);

            if(count($product_details) > 0){
                $product_id = $product_details[0]->product_id;

                $pricing_details =  DB::select('SELECT spd.id AS pricing_id,
                        spd.product_id,
                        spd.plan_id,
                        spd.plan_interval,
                        spd.plan_title,
                        spd.button_id,
                        spd.fs_product_id,
                        spd.actual_amount,
                        spd.payable_amount,
                        COALESCE(spd.actual_amount_inr, 0) AS actual_amount_inr,
                        COALESCE(spd.payable_amount_inr, 0) AS payable_amount_inr,
                        spd.currency,
                        spd.is_active,
                        spd.create_time,
                        spd.update_time
                        FROM subscription_pricing_details AS spd WHERE product_id  = ?
                        ORDER BY plan_id',[$product_id]);

                $product_details[0]->plans =$pricing_details;
                return $product_details[0];
            }

          });
        }
        $redis_result = Cache::get("getPricingByUser$user_role_id:$this->user_id");
      }
        if (!$redis_result) {
            return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get product details.', 'cause' => '', 'data' => json_decode('{}')));
        }

      $user_detail = (new LoginController())->getUserInfoByUserId($this->user_id);
      $redis_result->user_detail = $user_detail;
      $response = Response::json(array('code' => 200, 'message' => 'Pricing fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

    } catch (Exception $e) {
      (new ImageController())->logs("getPricingByUser",$e);
//      Log::error("getPricingByUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get pricing by products.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }

  /*-------------------------------------------------User------------------------------------------------*/


  /**
   * @api {post} getPricingForStaticPage getPricingForStaticPage
   * @apiName getPricingForStaticPage
   * @apiGroup super-admin[payment]
   * @apiVersion 1.0.0
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
   * "message": "Pricing details fetched successfully.",
   * "cause": "",
   * "data": {"result":{"product_id":1,"product_name":"PAK payment","discount_percentage":10,"plans":[{"pricing_id":2,"product_id":1,"plan_name":"Monthly starter","button_id":"1254","coupon_id":1,"actual_amount":10,"payable_amount":9,"currency":"USD"},{"pricing_id":3,"product_id":1,"plan_name":"Yearly starter","button_id":"5241","coupon_id":1,"actual_amount":100,"payable_amount":90,"currency":"USD"},{"pricing_id":7,"product_id":1,"plan_name":"Yearly pro","button_id":"585241","coupon_id":2,"actual_amount":1000,"payable_amount":900,"currency":"INR"},{"pricing_id":8,"product_id":1,"plan_name":"Monthly pro","button_id":"8941","coupon_id":1,"actual_amount":200,"payable_amount":190,"currency":"INR"}]}}
   * }
   */
  public function getPricingForStaticPage(Request $request_body){
    try {
      if (!Cache::has("Config::get('constant.REDIS_KEY'):getPricingForStaticPage")) {
        $result = Cache::rememberforever("getPricingForStaticPage", function () {

          $product_details = DB::select('SELECT id AS product_id,
            name AS product_name,
            discount_percentage
            FROM subscription_product_details
            WHERE is_applied = 1');
          if (count($product_details) > 0){
              $product_id = $product_details[0]->product_id;

              $pricing_details =  DB::select('SELECT spd.id AS pricing_id,
                      spd.product_id,
                      spd.plan_interval,
                      spd.plan_title,
                      spd.plan_id,
                      spd.actual_amount,
                      spd.payable_amount,
                      COALESCE(spd.actual_amount_inr, 0) AS actual_amount_inr,
                      COALESCE(spd.payable_amount_inr, 0) AS payable_amount_inr,
                      spd.currency,
                      spd.create_time,
                      spd.update_time
                      FROM subscription_pricing_details AS spd WHERE product_id  = ?
                      ORDER BY plan_id ',[$product_id]);

              $product_details[0]->plans = $pricing_details;
              return $product_details[0];
          }
        });
      }

      $redis_result = Cache::get("getPricingForStaticPage");
      if (!$redis_result) {
          return Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get product details.', 'cause' => '', 'data' => json_decode('{}')));
      }

      $response = Response::json(array('code' => 200, 'message' => 'Pricing fetched successfully.', 'cause' => '', 'data' => ['result' => $redis_result]));
      $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

    } catch (Exception $e) {
      (new ImageController())->logs("getPricingForStaticPage",$e);
//      Log::error("getPricingForStaticPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get pricing by products.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
    }
    return $response;
  }




  //routes
  /*
  Route::post('createProduct', 'PaymentModuleController@createProduct');
  Route::post('UpdateProduct', 'PaymentModuleController@UpdateProduct');
  Route::post('DeleteProduct', 'PaymentModuleController@DeleteProduct');
  Route::post('addPricingToProduct', 'PaymentModuleController@addPricingToProduct');
  Route::post('updatePricingToProduct', 'PaymentModuleController@updatePricingToProduct');
  Route::post('deletePricingToProduct', 'PaymentModuleController@deletePricingToProduct');
  Route::post('getCouponFromStripe', 'PaymentModuleController@getCouponFromStripe');
*/

  //caching
  /*//Discount payment module [product]
      if ($api == '/api/createProduct' or $api == '/api/updateProduct' or $api == '/api/deleteProduct') {
        //getAllProducts
        $keys = Redis::keys(Config::get("constant.REDIS_KEY").':getAllProducts*');
        foreach ($keys as $key) {
          Redis::del($key);
        }
      }

      //Discount payment module [pricing]
      if ($api == '/api/addPricingToProduct' or $api == '/api/updatePricingToProduct' or $api == '/api/deletePricingToProduct') {
        //getPricingByProduct
        $keys = Redis::keys(Config::get("constant.REDIS_KEY").':getPricingByProduct*');
        foreach ($keys as $key) {
          Redis::del($key);
        }
  */
}
