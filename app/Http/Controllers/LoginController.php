<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03-Jan-18
 * Time: 1:07 PM
 */

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Role;
use App\User_Master;
use Config;
use DateTime;
use DB;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use JWTAuth;
use JWTException;
use Log;
use Mail;
use Response;
use Swagger\Annotations as SWG;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
 * Class LoginController
 */
class LoginController extends Controller
{
    //Status code 421 : for social user does not exist

    /**
     * - Admin ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/doLoginForAdmin",
     *        tags={"Admin"},
     *        operationId="doLoginForAdmin",
     *        summary="Login api of admin",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"email_id","password"},
     *
     *          @SWG\Property(property="email_id",  type="string", example="admin@gmail.com", description=""),
     *          @SWG\Property(property="password",  type="string", example="demo@123", description=""),
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
    public function doLoginForAdmin(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if ($response = (new VerificationController())->validateRequiredParameter(['email_id', 'password'], $request) != '') {
                return $response;
            }

            $email_id = $request->email_id;
            $password = $request->password;
            $credentials = ['email_id' => $email_id, 'password' => $password];
            if (! $token = JWTAuth::attempt($credentials)) {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);

            }

            $user_details = JWTAuth::toUser($token);
            $user_id = $user_details->id;
            $role_id = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);
            $admin = Config::get('constant.ADMIN_ID');
            $sub_admin = Config::get('constant.SUB_ADMIN_ID');
            $crm_user_role_id = Config::get('constant.CRM_USER_ROLE_ID');
            if (in_array("$user_id", [$admin, $sub_admin], true) || in_array($role_id[0]->role_id, [$crm_user_role_id])) {
                $google2fa_enable = $user_details->google2fa_enable;
                $user_uuid = $user_details->uuid;
                if ($google2fa_enable == 1 && ! isset($_COOKIE[$user_uuid])) {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => '', 'user_detail' => $user_details, 'role' => $role_id[0]->role_id]]);

                } elseif ($google2fa_enable != 1) {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_details, 'role' => $role_id[0]->role_id]]);

                } elseif (Hash::check($password, $_COOKIE[$user_uuid])) {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_details, 'role' => $role_id[0]->role_id]]);

                } else {
                    $this->createNewSession($user_id, $token);
                    $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => '', 'user_detail' => $user_details, 'role' => $role_id[0]->role_id]]);
                }

            } else {
                $response = Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again..', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (Exception $e) {
            (new ImageController())->logs('doLoginForAdmin', $e);
            //            Log::error("doLoginForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'login for admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollback();
        }

        return $response;
    }

    /*========================================================| User |=================================================*/

    public function doLoginForContentUploader_v2(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());

            //Mandatory Field
            if (($response = (new VerificationController())->validateRequiredParameter([
                'email_id',
                'password'], $request)) != ''
            ) {
                return $response;
            }

            $email_id = $request->email_id;
            $password = $request->password;
            //    $role_name = Config::get('constant.ROLE_FOR_CONTENT_UPLOADER');

            $credential = ['email_id' => $email_id, 'password' => $password];
            if (! $token = JWTAuth::attempt($credential)) {
                return Response::json(['code' => 201, 'message' => 'Invalid email or password.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            //    if (($response = (new VerificationController())->verifyUser($email_id, $role_name)) != '')
            //      return $response;

            $result = DB::select('SELECT
                                        um.id,
                                        um.is_active
                                        FROM user_master um
                                        WHERE um.email_id = ? AND um.is_active = ?', [$email_id, 1]);
            if (count($result) == 0) {
                return $response = Response::json(['code' => 201, 'message' => 'You are inactive user. Please contact administrator.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            //      $user_session_id = $this->createNewSession($result[0]->id, $token);

            //      if (($response = (new VerificationController())->checkIfUserIsActive($email_id)) != ''){
            //        Log::info('in_active email_id & password');
            //        return $response;
            //      }

            $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token]]);

            //      Log::info("Login token",["token :" => $token,"time" => date('H:m:s')]);

        } catch (JWTException $e) {
            Log::error('doLoginForContentUploader_v2(JWTException) : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => 'Could not create token.'.$e->getMessage(), 'cause' => '', 'data' => json_decode('{}')]);
        } catch (Exception $e) {
            (new ImageController())->logs('doLoginForContentUploader_v2', $e);
            //      Log::error("doLoginForContentUploader_v2 : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/doLoginForUser",
     *        tags={"Users"},
     *        operationId="doLoginForUser",
     *        summary="Login api of email user",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"email_id","password","device_info","ip_address"},
     *
     *          @SWG\Property(property="email_id",  type="string", example="steave@grr.la", description=""),
     *          @SWG\Property(property="password",  type="string", example="demo@123", description=""),
     *          @SWG\Property(property="ip_address",  type="string", example="192.168.0.134", description=""),
     *          @SWG\Property(property="device_info",  type="object", example={"device_carrier":"","device_country_code":"IN","device_reg_id":"115a1a110","device_default_time_zone":"Asia/Calcutta","device_language":"en","device_latitude":"","device_library_version":"1","device_local_code":"NA","device_longitude":"","device_model_name":"Micromax AQ4501","device_os_version":"6.0.1","device_platform":"android","device_registration_date":"2016-05-06T15:58:11 +0530","device_resolution":"480x782","device_type":"phone","device_udid":"109111aa1121","device_vendor_name":"Micromax","project_package_name":"com.optimumbrew.projectsetup","device_application_version":"1.0"}, description="All parameters in this object are optional"),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Login Successfully.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4NjkzOTc3LCJleHAiOjE1NjkyOTg3NzcsIm5iZiI6MTU2ODY5Mzk3NywianRpIjoibnN1dG1aNlJOUHFEcG54VSJ9.m8IurymsHdsn9OcIxX1IgSwJl9ZnDrAaaAtX7YRUTew","user_detail":{"user_id":23,"user_name":"rushita.optimumbrew@gmail.com","first_name":"rushita","email_id":"rushita.optimumbrew@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":0,"mailchimp_subscr_id":"3d7d82c763761fa3edb6b175ed254330","role_id":7,"create_time":"2019-01-12 00:55:39","update_time":"2019-01-12 00:55:46","subscr_expiration_time":"2020-09-17 04:19:37","next_billing_date":"2020-09-17 04:19:37","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to login.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function doLoginForUserBackUp(Request $request_body)
    {
        try {

            $request = json_decode($request_body->getContent());

            //Mandatory Field
            if (($response = (new VerificationController())->validateRequiredParameter([
                'email_id',
                'password',
                'device_info'], $request)) != ''
            ) {
                return $response;
            }

            $device_info = $request->device_info;
            /*if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid'), $device_info)) != '') {
                return $response;
            }*/
            $email_id = $request->email_id;
            $password = $request->password;

            if ($email_id != '') {
                if (! filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
                    $response = Response::json(['code' => 201, 'message' => 'E-mail is in the wrong format.', 'cause' => '', 'data' => '']);

                    return $response;
                }
            }

            $social_user = DB::select('SELECT 1 FROM user_master WHERE email_id = ? AND signup_type IN(2,3)', [$email_id]);
            if (count($social_user) > 0) {
                return Response::json(['code' => 201, 'message' => 'You have done the signup using your social account like Facebook or Gmail. Please try to login using Facebook or Gmail.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $credential = ['user_name' => $email_id, 'password' => $password];
            if (! $token = JWTAuth::attempt($credential)) {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $user_id = JWTAuth::toUser($token)->id;
            $admin = Config::get('constant.ADMIN_ID');
            $sub_admin = Config::get('constant.SUB_ADMIN_ID');
            if (! in_array("$user_id", [$admin, $sub_admin], true)) {
                $active_user = DB::select('select * from user_master where id=? and is_active=1', [$user_id]);

                if (count($active_user) == 1) {

                    $user_profile = $this->getUserInfoByUserId($user_id);
                    // create user session
                    $user_session_id = $this->createNewSession($user_id, $token);
                    DB::beginTransaction();
                    DB::delete('DELETE FROM user_session WHERE user_id=? AND is_active=0', [$user_id]);
                    DB::commit();

                    $device_udid = isset($device_info->device_udid) ? $device_info->device_udid : '';
                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($device_info->device_library_version) ? $device_info->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_latitude = isset($device_info->device_latitude) ? $device_info->device_latitude : 0;
                    $device_longitude = isset($device_info->device_longitude) ? $device_info->device_longitude : 0;
                    $project_package_name = isset($device_info->project_package_name) ? $device_info->project_package_name : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';
                    $device_registered_from = 1; //0=device registered from signup API, 1=device registered from login API
                    $ip_address = isset($request->ip_address) ? $request->ip_address : \Request::ip();
                    $user_session_id = isset($user_session_id) ? $user_session_id : null;

                    $this->addNewDeviceToUser(
                        $user_id,
                        $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_carrier,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_application_version,
                        $device_type,
                        $device_registration_date,
                        $device_library_version,
                        $device_latitude,
                        $device_longitude,
                        $project_package_name,
                        $device_registered_from,
                        $ip_address,
                        $user_session_id
                    );

                    // create user session
                    //                      $this->createNewSession($user_id, $token);

                    /*$core_ip = $_SERVER['REMOTE_ADDR'];
                    $ip = \Request::ip();
                    $location = \Location::get($ip);
                    Log::info('doLoginForUser',['core_ip'=>$core_ip]);
                    Log::info('doLoginForUser',['ip_adress'=>$ip]);
                    Log::info('location',[$location]);*/

                    $response = Response::json(['code' => 200, 'message' => 'Login Successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]]);

                } else {
                    $response = Response::json(['code' => 201, 'message' => 'Your account has been deactivated.', 'cause' => '', 'data' => json_decode('{}')]);
                }
                DB::commit();
            } else {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('doLoginForUser', $e);
            //            Log::error('doLoginForUser : ', ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollback();
        }

        return $response;
    }

    public function doLoginForUser(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            /* Required request data parameter */
            if (($response = (new VerificationController())->validateRequiredParameter(['email_id', 'password', 'device_info'], $request)) != '') {
                return $response;
            }

            $email_id = $request->email_id;
            $password = $request->password;
            $device_info = $request->device_info;
            $device_info->ip_address = request()->ip();

            /* Required device_info parameter */
            if (($response = (new VerificationController())->validateRequiredParam(['device_application_version', 'device_carrier', 'device_country_code', 'device_default_time_zone', 'device_language', 'device_latitude', 'device_library_version', 'device_local_code', 'device_longitude', 'device_model_name', 'device_os_version', 'device_platform', 'device_reg_id', 'device_registration_date', 'device_resolution', 'device_type', 'device_udid', 'device_vendor_name', 'project_package_name', 'ip_address'], $device_info)) != '') {
                Log::error('doLoginForUser : Required field some device_info\'s data is missing or empty.', ['response' => $response]);

                return $response;
            }

            /* Verify email format */
            if (! filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
                Log::error('doLoginForUser : E-mail is in the wrong format.', ['email_id' => $email_id]);

                return Response::json(['code' => 201, 'message' => 'E-mail is in the wrong format.', 'cause' => '', 'data' => '']);
            }

            /* Check if social user tries to login with email */
            $social_user = DB::select('SELECT signup_type, is_active, is_multi_login FROM user_master WHERE email_id = ?', [$email_id]);
            if (! $social_user) {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                /* Check if user does social and email login both */
            } elseif (! $social_user[0]->is_multi_login) {
                if ($social_user[0]->signup_type == 2) {
                    return Response::json(['code' => 201, 'message' => 'You have done the signup using your Facebook account. If you want to login with email then first you have to set your password or you can use forgot password option.', 'cause' => '', 'data' => json_decode('{}')]);
                } elseif ($social_user[0]->signup_type == 3) {
                    return Response::json(['code' => 201, 'message' => 'You have done the signup using your Google account. If you want to login with email then first you have to set your password or you can use forgot password option.', 'cause' => '', 'data' => json_decode('{}')]);
                }
                /* Check user is active or not  */
            } elseif (! $social_user[0]->is_active) {
                return Response::json(['code' => 201, 'message' => 'Your account has been deactivated.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /* Verify user with the credential and generate token */
            $credential = ['email_id' => $email_id, 'password' => $password];
            if (! $token = JWTAuth::attempt($credential)) {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $user_id = JWTAuth::toUser($token)->id;
            $admin = Config::get('constant.ADMIN_ID');
            $sub_admin = Config::get('constant.SUB_ADMIN_ID');

            /* Check if user role is admin or sub_admin then return with error message */
            if (! in_array("$user_id", [$admin, $sub_admin], true)) {

                /* Create user session */
                $this->createNewSessionV2($user_id, $token, json_encode($device_info));

                /* Get user details by user_id */
                $user_profile = $this->getUserInfoByUserId($user_id);
                $response = Response::json(['code' => 200, 'message' => 'Login Successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]]);

            } else {
                $response = Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('doLoginForUser', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollback();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/doLoginForSocialUser",
     *        tags={"Users"},
     *        operationId="doLoginForSocialUser",
     *        summary="Login api of social user",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"first_name","email_id","social_uid","signup_type"},
     *
     *          @SWG\Property(property="first_name",  type="string", example="Steave", description=""),
     *          @SWG\Property(property="email_id",  type="string", example="steave@grr.la", description=""),
     *          @SWG\Property(property="social_uid",  type="string", example="12346566456", description=""),
     *          @SWG\Property(property="signup_type",  type="integer", example=2, description="1=email, 2=facebook, 3=google"),
     *          @SWG\Property(property="device_info",  type="object", example={"device_carrier":"","device_country_code":"IN","device_reg_id":"115a1a110","device_default_time_zone":"Asia/Calcutta","device_language":"en","device_latitude":"","device_library_version":"1","device_local_code":"NA","device_longitude":"","device_model_name":"Micromax AQ4501","device_os_version":"6.0.1","device_platform":"android","device_registration_date":"2016-05-06T15:58:11 +0530","device_resolution":"480x782","device_type":"phone","device_udid":"109111aa1121","device_vendor_name":"Micromax","project_package_name":"com.optimumbrew.projectsetup","device_application_version":"1.0"}, description="All parameters in this object are optional"),
     *        ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Login Successfully.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4NjkzOTc3LCJleHAiOjE1NjkyOTg3NzcsIm5iZiI6MTU2ODY5Mzk3NywianRpIjoibnN1dG1aNlJOUHFEcG54VSJ9.m8IurymsHdsn9OcIxX1IgSwJl9ZnDrAaaAtX7YRUTew","user_detail":{"user_id":23,"user_name":"rushita.optimumbrew@gmail.com","first_name":"rushita","email_id":"rushita.optimumbrew@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":0,"mailchimp_subscr_id":"3d7d82c763761fa3edb6b175ed254330","role_id":7,"create_time":"2019-01-12 00:55:39","update_time":"2019-01-12 00:55:46","subscr_expiration_time":"2020-09-17 04:19:37","next_billing_date":"2020-09-17 04:19:37","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to login.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function doLoginForSocialUserBackUp(Request $request)
    {
        try {
            $request = json_decode($request->getContent());

            $response = (new VerificationController())->validateRequiredParameter(['social_uid', 'signup_type', 'device_info'], $request);
            if ($response != '') {
                return $response;
            }

            $social_uid = $request->social_uid;
            $signup_type = $request->signup_type;
            $device_info = $request->device_info;
            /*if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid'), $device_info)) != '') {
                return $response;
            }*/

            $create_time = date('Y-m-d H:i:s');
            $exist_id = DB::select('select id from user_master where social_uid = ? and signup_type = ?', [$social_uid, $signup_type]);

            //Log::info('User exist :', ['user_id' => $exist_id]);

            if (count($exist_id) > 0) {

                if (($response = (new VerificationController())->checkIfUserIsActive($exist_id[0]->id)) != '') {
                    return $response;
                }

                $user_id = $exist_id[0]->id;

                $password = '$'.$social_uid.'#';
                //Log::info('Exist record social credential', ['social_uid' => $social_uid, 'password' => $password]);

                $credential = ['user_name' => $social_uid, 'password' => $password];
                if (! $token = JWTAuth::attempt($credential)) {
                    return Response::json(['code' => 201, 'message' => 'Invalid credential', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $device_udid = $device_info->device_udid;
                $active_user = DB::select('select * from user_master where id=? and is_active=1', [$user_id]);

                if (count($active_user) == 1) {

                    $user_session_id = (new LoginController())->createNewSession($user_id, $token);
                    DB::beginTransaction();
                    DB::delete('DELETE FROM user_session WHERE user_id=? AND is_active=0', [$user_id]);
                    DB::commit();

                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($device_info->device_library_version) ? $device_info->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_latitude = isset($device_info->device_latitude) ? $device_info->device_latitude : 0;
                    $device_longitude = isset($device_info->device_longitude) ? $device_info->device_longitude : 0;
                    $project_package_name = isset($device_info->project_package_name) ? $device_info->project_package_name : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';
                    $device_registered_from = 1; //0=device registered from signup API, 1=device registered from login API
                    $ip_address = isset($request->ip_address) ? $request->ip_address : \Request::ip();
                    $user_session_id = isset($user_session_id) ? $user_session_id : null;

                    $this->addNewDeviceToUser(
                        $user_id,
                        $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_carrier,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_application_version,
                        $device_type,
                        $device_registration_date,
                        $device_library_version,
                        $device_latitude,
                        $device_longitude,
                        $project_package_name,
                        $device_registered_from,
                        $ip_address,
                        $user_session_id
                    );

                    //Log::info("Login token", ["token :" => $token, "time" => date('H:m:s')]);
                    /*$core_ip = $_SERVER['REMOTE_ADDR'];
                    $ip = \Request::ip();
                    $location = \Location::get($ip);
                    Log::info('doLoginForSocialUser',['core_ip'=>$core_ip]);
                    Log::info('doLoginForSocialUser',['ip_adress'=>$ip]);
                    Log::info('location',[$location]);*/

                    $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                    $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_detail]]);

                }

            } else {

                $response = (new VerificationController())->validateRequiredParameter(['first_name', 'email_id'], $request);
                if ($response != '') {
                    return $response;

                }
                $email_id = $request->email_id;
                $first_name = (new ImageController())->removeEmoji($request->first_name);

                if (($response = (new VerificationController())->checkDisposableEmail($email_id)) != '') {
                    return $response;
                }

                if (($response = (new VerificationController())->checkIfEmailExist($email_id)) != '') {
                    return $response;
                }

                DB::beginTransaction();

                $data = ['email_id' => $email_id, 'request_json' => json_encode($request)];
                $user_reg_temp_id = DB::table('user_registration_temp')->insertGetId($data);

                $password = '$'.$social_uid.'#';
                $db_password = Hash::make($password);

                $uuid = (new ImageController())->generateUUID();
                if ($uuid == '') {
                    return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $user_master_data = [
                    'user_name' => $social_uid,
                    'uuid' => $uuid,
                    'password' => $db_password,
                    'email_id' => $email_id,
                    'social_uid' => $social_uid,
                    'signup_type' => $signup_type,
                    'is_active' => 1,
                    'is_verify' => 1,
                    'is_once_logged_in' => 0,
                    'profile_setup' => 1,
                    'create_time' => $create_time,
                ];
                //Log::info('Social sign up user_id', ['user_master_data' => $user_master_data]);

                $id = DB::table('user_master')->insertGetId($user_master_data);
                DB::commit();

                $user_role_data = [
                    'role_id' => Config::get('constant.ROLE_ID_FOR_USER'),
                    'user_id' => $id,
                ];
                DB::table('role_user')->insert($user_role_data);
                DB::commit();
                $user_details_data = [
                    'user_id' => $id,
                    'first_name' => $first_name,
                    'email_id' => $email_id,
                    'create_time' => $create_time,
                ];

                DB::table('user_detail')->insert($user_details_data);

                DB::commit();

                if (($response = (new VerificationController())->checkIfUserIsActive($id)) != '') {
                    return $response;
                }

                $credential = ['user_name' => $social_uid, 'password' => $password];
                if (! $token = JWTAuth::attempt($credential)) {

                    return Response::json(['code' => 201, 'message' => 'Invalid credential.', 'cause' => 'Email', 'data' => json_decode('{}')]);
                }

                $device_udid = $device_info->device_udid;
                $active_user = DB::select('select * from user_master where id=? and is_active=1', [$id]);

                if (count($active_user) == 1) {

                    $user_session_id = (new LoginController())->createNewSession($id, $token);
                    DB::beginTransaction();
                    DB::delete('DELETE FROM user_session WHERE user_id=? AND is_active=0', [$id]);
                    DB::commit();

                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($device_info->device_library_version) ? $device_info->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_latitude = isset($device_info->device_latitude) ? $device_info->device_latitude : 0;
                    $device_longitude = isset($device_info->device_longitude) ? $device_info->device_longitude : 0;
                    $project_package_name = isset($device_info->project_package_name) ? $device_info->project_package_name : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';
                    $device_registered_from = 0; //0=device registered from signup API, 1=device registered from login API
                    $ip_address = isset($request->ip_address) ? $request->ip_address : \Request::ip();
                    $user_session_id = isset($user_session_id) ? $user_session_id : null;

                    $this->addNewDeviceToUser(
                        $id,
                        $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_carrier,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_application_version,
                        $device_type,
                        $device_registration_date,
                        $device_library_version,
                        $device_latitude,
                        $device_longitude,
                        $project_package_name,
                        $device_registered_from,
                        $ip_address,
                        null
                    );

                    $this->addNewDeviceToUser(
                        $id,
                        $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_carrier,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_application_version,
                        $device_type,
                        $device_registration_date,
                        $device_library_version,
                        $device_latitude,
                        $device_longitude,
                        $project_package_name,
                        1,
                        $ip_address,
                        $user_session_id
                    );

                    $user_detail = (new LoginController())->getUserInfoByUserId($id);

                    (new MailchimpController())->subscribeUserByEmail($email_id, 'free_user');

                    $template = 'user_account_activation';
                    $subject = 'PhotoADKing: Account Activation';
                    $message_body = [
                        'message' => '<p><b>Welcome to PhotoADKing</b><br><br>You have successfully registered on PhotoADKing! Enjoy designing.</p>',
                        'user_name' => $first_name,
                    ];
                    $api_name = 'verifyUser';
                    $api_description = 'Send mail after verify user.';
                    $this->dispatch(new SendMailJob($id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    /*$core_ip = $_SERVER['REMOTE_ADDR'];
                    $ip = \Request::ip();
                    $location = \Location::get($ip);
                    Log::info('doLoginForSocialUser_Register',['core_ip'=>$core_ip]);
                    Log::info('doLoginForSocialUser_Register',['ip_adress'=>$ip]);
                    Log::info('location',[$location]);*/

                    $response = Response::json(['code' => 200, 'message' => 'User registered successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_detail]]);

                }

            }

        } catch (Exception $e) {
            (new ImageController())->logs('doLoginForSocialUser', $e);
            //            Log::error("doLoginForSocialUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function doLoginForSocialUser(Request $request)
    {
        try {
            $request = json_decode($request->getContent());
            /* Required request data parameter */
            if (($response = (new VerificationController())->validateRequiredParameter(['signup_type', 'device_info'], $request)) != '') {
                Log::error('doLoginForSocialUser : Required field some data is missing or empty.', ['response' => $response]);

                return $response;
            }

            $signup_type = $request->signup_type;
            $access_token = isset($request->access_token) ? $request->access_token : null;
            $id_token = isset($request->id_token) ? $request->id_token : null;
            $device_info = $request->device_info;
            $device_info->ip_address = request()->ip();

            /* Required device_info parameter */
            if (($response = (new VerificationController())->validateRequiredParam(['device_application_version', 'device_carrier', 'device_country_code', 'device_default_time_zone', 'device_language', 'device_latitude', 'device_library_version', 'device_local_code', 'device_longitude', 'device_model_name', 'device_os_version', 'device_platform', 'device_reg_id', 'device_registration_date', 'device_resolution', 'device_type', 'device_udid', 'device_vendor_name', 'project_package_name', 'ip_address'], $device_info)) != '') {
                Log::error('doLoginForSocialUser : Required field some device_info\'s data is missing or empty.', ['response' => $response]);

                return $response;
            }

            /* Get social user information using access_token and id_token*/
            if ($access_token) {
                $user_details = $this->socialUserDetail($access_token, $signup_type);
            } else {
                $user_details = $this->socialUserDetail($id_token, $signup_type);
            }

            /* Check if result is in array format because success response will be in array format */
            if (! is_array($user_details)) {
                return Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'login.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $social_uid = $user_details['social_uid'];
            $email_id = $user_details['email'];
            $first_name = $user_details['first_name'];
            $last_name = $user_details['last_name'];
            $create_time = date('Y-m-d H:i:s');
            $password = '$'.$social_uid.'#';

            /* Check if social user exist or not */
            $exist_id = DB::select('SELECT id, is_active, is_multi_login FROM user_master WHERE social_uid = ? AND signup_type = ?', [$social_uid, $signup_type]);
            if ($exist_id) {

                /* Check if user is inactive, If yes then return with error message */
                if (! $exist_id[0]->is_active) {
                    return Response::json(['code' => 201, 'message' => 'You are inactive user. Please contact administrator.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $user_id = $exist_id[0]->id;
                /* Check if user does social and email login both */
                if ($exist_id[0]->is_multi_login) {
                    /* Generate token from existing user data */
                    if (! $token = JWTAuth::fromUser($exist_id[0])) {
                        return Response::json(['code' => 201, 'message' => 'Invalid credential', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                } else {
                    /* Verify user with the credential and generate token */
                    $credential = ['user_name' => $social_uid, 'password' => $password];
                    if (! $token = JWTAuth::attempt($credential)) {
                        return Response::json(['code' => 201, 'message' => 'Invalid credential', 'cause' => '', 'data' => json_decode('{}')]);
                    }
                }
                $message = 'Login successfully.';

            } else {

                /* Check if email user tries to login with social account */
                $user = DB::select('SELECT id, is_active, is_multi_login FROM user_master WHERE email_id = ? AND signup_type = 1', [$email_id]);
                if ($user) {

                    /* Check if user is inactive, If yes then return with error message */
                    if (! $user[0]->is_active) {
                        return Response::json(['code' => 201, 'message' => 'You are inactive user. Please contact administrator.', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    $user_id = $user[0]->id;
                    /* Generate token from existing user data */
                    if (! $token = JWTAuth::fromUser($user[0])) {
                        return Response::json(['code' => 201, 'message' => 'Invalid credential', 'cause' => '', 'data' => json_decode('{}')]);
                    }

                    /* Update the multi login tracker and add social_uid */
                    if (! $user[0]->is_multi_login) {
                        DB::beginTransaction();
                        DB::update('UPDATE user_master
                            SET
                            social_uid = ?,
                            is_multi_login = ?,
                            update_time = update_time
                            WHERE email_id = ?', [$social_uid, 1, $email_id]);
                        DB::commit();
                    }
                    $message = 'Login successfully.';

                } else {

                    /* Check email is valid or not */
                    if (($response = (new VerificationController())->checkDisposableEmail($email_id)) != '') {
                        return $response;
                    }

                    /* Check email is already exist in database or not */
                    if (($response = (new VerificationController())->checkIfEmailExist($email_id)) != '') {
                        return $response;
                    }

                    /* Generate unique id and register social user data into user_master table */
                    DB::beginTransaction();
                    $db_password = Hash::make($password);
                    $uuid = (new ImageController())->generateUUID();

                    $user_master_data = [
                        'user_name' => $social_uid,
                        'uuid' => $uuid,
                        'password' => $db_password,
                        'email_id' => $email_id,
                        'social_uid' => $social_uid,
                        'signup_type' => $signup_type,
                        'is_active' => 1,
                        'is_verify' => 1,
                        'is_once_logged_in' => 0,
                        'profile_setup' => 1,
                        'create_time' => $create_time,
                    ];
                    $user_id = DB::table('user_master')->insertGetId($user_master_data);

                    /* Insert user_id into role_user table */
                    $user_role_data = [
                        'role_id' => config('constant.ROLE_ID_FOR_USER'),
                        'user_id' => $user_id,
                    ];
                    DB::table('role_user')->insert($user_role_data);

                    /* Insert user information into user_detail table */
                    $user_details_data = [
                        'user_id' => $user_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email_id' => $email_id,
                        'device_json' => json_encode($device_info),
                        'create_time' => $create_time,
                    ];
                    DB::table('user_detail')->insert($user_details_data);
                    DB::commit();

                    /* Verify user with the credential and generate token */
                    $credential = ['user_name' => $social_uid, 'password' => $password];
                    if (! $token = JWTAuth::attempt($credential)) {
                        Log::error('doLoginForSocialUser : There is a major error :', ['credential' => $credential]);

                        return Response::json(['code' => 201, 'message' => 'Invalid credential.', 'cause' => 'Email', 'data' => json_decode('{}')]);
                    }

                    /* This process is for marketing purpose */
                    (new MailchimpController())->subscribeUserByEmail($email_id, 'free_user');

                    /* Dispatch job to send account activation mail to user */
                    $template = 'user_account_activation';
                    $subject = 'PhotoADKing: Account Activation';
                    $message_body = [
                        'message' => '<p><b>Welcome to PhotoADKing</b><br><br>You have successfully registered on PhotoADKing! Enjoy designing.</p>',
                        'user_name' => $first_name,
                    ];
                    $api_name = 'verifyUser';
                    $api_description = 'Send mail after verify user.';
                    $this->dispatch(new SendMailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                    $message = 'User registered successfully.';
                }
            }

            /* Create user session */
            $this->createNewSessionV2($user_id, $token, json_encode($device_info));

            /* Get user details by user_id */
            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
            $response = Response::json(['code' => 200, 'message' => $message, 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_detail]]);

        } catch (Exception $e) {
            if ($e instanceof QueryException) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    Log::error('doLoginForSocialUser : Duplicate entry occurred.');

                    return Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'login, Please try again later', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }
            (new ImageController())->logs('doLoginForSocialUser', $e);
            $response = Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/checkSocialUserExist",
     *        tags={"Users"},
     *        operationId="checkSocialUserExist",
     *        summary="Check social user is already exist or not",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"social_uid","signup_type","device_info"},
     *
     *          @SWG\Property(property="social_uid",  type="string", example="12346566456", description=""),
     *          @SWG\Property(property="signup_type",  type="integer", example=2, description="1=email, 2=facebook, 3=google"),
     *          @SWG\Property(property="device_info",  type="object", example={"device_carrier":"","device_country_code":"IN","device_reg_id":"115a1a110","device_default_time_zone":"Asia/Calcutta","device_language":"en","device_latitude":"","device_library_version":"1","device_local_code":"NA","device_longitude":"","device_model_name":"Micromax AQ4501","device_os_version":"6.0.1","device_platform":"android","device_registration_date":"2016-05-06T15:58:11 +0530","device_resolution":"480x782","device_type":"phone","device_udid":"109111aa1121","device_vendor_name":"Micromax","project_package_name":"com.optimumbrew.projectsetup","device_application_version":"1.0"}, description="All parameters in this object are optional"),
     *        ),
     *      ),
     *
     *     @SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Login successfully.","cause":"","data":{"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjIzLCJpc3MiOiJodHRwOi8vMTkyLjE2OC4wLjExMy9waG90b2Fka2luZ192YWxpZGF0aW9uX2ludGVncmF0aW9uL2FwaS9wdWJsaWMvYXBpL2RvTG9naW5Gb3JVc2VyIiwiaWF0IjoxNTY4NjkzOTc3LCJleHAiOjE1NjkyOTg3NzcsIm5iZiI6MTU2ODY5Mzk3NywianRpIjoibnN1dG1aNlJOUHFEcG54VSJ9.m8IurymsHdsn9OcIxX1IgSwJl9ZnDrAaaAtX7YRUTew","user_detail":{"user_id":23,"user_name":"rushita.optimumbrew@gmail.com","first_name":"rushita","email_id":"rushita.optimumbrew@gmail.com","thumbnail_img":"","compressed_img":"","original_img":"","social_uid":"","signup_type":1,"profile_setup":1,"is_active":1,"is_verify":1,"is_once_logged_in":0,"mailchimp_subscr_id":"3d7d82c763761fa3edb6b175ed254330","role_id":7,"create_time":"2019-01-12 00:55:39","update_time":"2019-01-12 00:55:46","subscr_expiration_time":"2020-09-17 04:19:37","next_billing_date":"2020-09-17 04:19:37","is_subscribe":1}}}, description="'is_once_logged_in' : 1=at least 1time logged in, 0=never logged in"),),
     *        ),
     *
     *     @SWG\Response(
     *            response=421,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":421,"message":"Couldn't find your PhotoADKing account.","cause":"","data":"{}"}), description=""),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to login.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    //Unused API
    public function checkSocialUserExist(Request $request)
    {
        try {
            $request = json_decode($request->getContent());

            $response = (new VerificationController())->validateRequiredParameter(['social_uid', 'signup_type', 'device_info'], $request);
            if ($response != '') {
                return $response;
            }

            $social_uid = $request->social_uid;
            $signup_type = $request->signup_type;
            $device_info = $request->device_info;
            /*if (($response = (new VerificationController())->validateRequiredParameter(array('device_udid'), $device_info)) != '') {
                return $response;
            }*/

            $exist_id = DB::select('select id from user_master where social_uid = ? and signup_type = ?', [$social_uid, $signup_type]);

            //Log::info('User exist :', ['user_id' => $exist_id]);

            if (count($exist_id) > 0) {

                if (($response = (new VerificationController())->checkIfUserIsActive($exist_id[0]->id)) != '') {
                    return $response;
                }

                $user_id = $exist_id[0]->id;

                $password = '$'.$social_uid.'#';
                //Log::info('Exist record social credential', ['social_uid' => $social_uid, 'password' => $password]);

                $credential = ['user_name' => $social_uid, 'password' => $password];
                if (! $token = JWTAuth::attempt($credential)) {
                    return Response::json(['code' => 201, 'message' => 'Invalid credential', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $device_udid = $device_info->device_udid;
                $active_user = DB::select('select * from user_master where id=? and is_active=1', [$user_id]);

                if (count($active_user) == 1) {

                    $user_session_id = (new LoginController())->createNewSession($user_id, $token);
                    DB::beginTransaction();
                    DB::delete('DELETE FROM user_session WHERE user_id=? AND is_active=0', [$user_id]);
                    DB::commit();

                    $device_reg_id = isset($device_info->device_reg_id) ? $device_info->device_reg_id : '';
                    $device_platform = isset($device_info->device_platform) ? $device_info->device_platform : '';
                    $device_model_name = isset($device_info->device_model_name) ? $device_info->device_model_name : '';
                    $device_vendor_name = isset($device_info->device_vendor_name) ? $device_info->device_vendor_name : '';
                    $device_os_version = isset($device_info->device_os_version) ? $device_info->device_os_version : '';
                    $device_resolution = isset($device_info->device_resolution) ? $device_info->device_resolution : '';
                    $device_carrier = isset($device_info->device_carrier) ? $device_info->device_carrier : '';
                    $device_country_code = isset($device_info->device_country_code) ? $device_info->device_country_code : '';
                    $device_language = isset($device_info->device_language) ? $device_info->device_language : '';
                    $device_local_code = isset($device_info->device_local_code) ? $device_info->device_local_code : '';
                    $device_default_time_zone = isset($device_info->device_default_time_zone) ? $device_info->device_default_time_zone : '';
                    $device_library_version = isset($device_info->device_library_version) ? $device_info->device_library_version : '';
                    $device_application_version = isset($device_info->device_application_version) ? $device_info->device_application_version : '';
                    $device_type = isset($device_info->device_type) ? $device_info->device_type : '';
                    $device_latitude = isset($device_info->device_latitude) ? $device_info->device_latitude : 0;
                    $device_longitude = isset($device_info->device_longitude) ? $device_info->device_longitude : 0;
                    $project_package_name = isset($device_info->project_package_name) ? $device_info->project_package_name : '';
                    $device_registration_date = isset($device_info->device_registration_date) ? $device_info->device_registration_date : '';
                    $device_registered_from = 1; //0=device registered from signup API, 1=device registered from login API
                    $ip_address = isset($request->ip_address) ? $request->ip_address : \Request::ip();
                    $user_session_id = isset($user_session_id) ? $user_session_id : null;

                    $this->addNewDeviceToUser(
                        $user_id,
                        $device_reg_id,
                        $device_platform,
                        $device_model_name,
                        $device_vendor_name,
                        $device_os_version,
                        $device_udid,
                        $device_resolution,
                        $device_carrier,
                        $device_country_code,
                        $device_language,
                        $device_local_code,
                        $device_default_time_zone,
                        $device_application_version,
                        $device_type,
                        $device_registration_date,
                        $device_library_version,
                        $device_latitude,
                        $device_longitude,
                        $project_package_name,
                        $device_registered_from,
                        $ip_address,
                        $user_session_id
                    );

                    $user_detail = (new LoginController())->getUserInfoByUserId($user_id);
                    /*$core_ip = $_SERVER['REMOTE_ADDR'];
                    $ip = \Request::ip();
                    $location = \Location::get($ip);
                    Log::info('checkSocialUserExist',['core_ip'=>$core_ip]);
                    Log::info('checkSocialUserExist',['ip_adress'=>$ip]);
                    Log::info('location',[$location]);*/

                    $response = Response::json(['code' => 200, 'message' => 'Login successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_detail]]);

                }

            } else {

                $response = Response::json(['code' => 421, 'message' => "Couldn't find your PhotoADKing account.", 'cause' => '', 'data' => json_decode('{}')]);

            }

        } catch (Exception $e) {
            (new ImageController())->logs('checkSocialUserExist', $e);
            //            Log::error("checkSocialUserExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/forgotPassword",
     *        tags={"Users"},
     *        operationId="forgotPassword",
     *        summary="Send otp to forgot password",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"email_id"},
     *
     *          @SWG\Property(property="email_id",  type="string", example="steave@grr.la", description=""),
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
    public function forgotPassword(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['email_id'], $request)) != '') {
                return $response;
            }

            //Mandatory field
            $email_id = $request->email_id;

            $response = (new VerificationController())->checkIfEmailExist($email_id);
            if ($response == '') {
                return Response::json(['code' => 201, 'message' => "Couldn't find your email address.", 'cause' => '', 'data' => json_decode('{}')]);
            }

            //            if(($response = (new VerificationController())->checkUserRegistrationType($email_id)) != 1)
            //              return Response::json(array('code' => 201, 'message' => 'You have done the signup using your social account like Facebook or Gmail. Please try to login using Facebook or Gmail.', 'cause' => '', 'data' => json_decode("{}")));

            $verification_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token
            $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+'.Config::get('constant.RESET_PASSWORD_LINK_EXPIRATION_TIME').' minutes'));

            //log::info(strtotime($otp_token_expire));
            DB::beginTransaction();
            //DB::delete('delete from user_pwd_reset_token_master WHERE email_id = ?', [$email_id]);
            //DB::commit();
            DB::insert('INSERT INTO user_pwd_reset_token_master
                            (email_id,reset_token,reset_token_expire)
                            values (? ,? ,?)',
                [$email_id, $verification_token, $otp_token_expire]);

            DB::commit();
            $response = Response::json(['code' => 200, 'message' => 'Reset password link has been sent to your email address.', 'cause' => '', 'data' => json_decode('{}')]);
            $user_detail = DB::select('select * from user_detail where email_id = ?', [$email_id]);

            if (count($user_detail) > 0) {
                $template = 'reset_password';
                $subject = 'PhotoADKing: Reset Password';
                $message_body = [
                    'user_name' => $user_detail[0]->first_name,
                    'token' => $verification_token,
                    'email_id' => $email_id,
                    'redirect_url' => Config::get('constant.RESET_PASSWORD_LINK_REDIRECT_URL'),
                ];
                $api_name = 'forgotPassword';
                $api_description = 'before user login forgot his password then send mail here.';
                $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

            }

        } catch (Exception $e) {
            (new ImageController())->logs('forgotPassword', $e);
            //            Log::error("forgotPassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'forgot Password.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/verifyResetPasswordLink",
     *        tags={"Users"},
     *        operationId="verifyResetPasswordLink",
     *        summary="Verify link of reset password",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"email_id","token"},
     *
     *          @SWG\Property(property="email_id",  type="string", example="steave@grr.la", description=""),
     *          @SWG\Property(property="token",  type="string", example="djfjbjdbgjdghjbdjfbkbcjfuhudbjsbcjsbd", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response="201",
     *            description="error",
     *        ),
     *    )
     */
    public function verifyResetPasswordLink(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['token', 'email_id'], $request)) != '') {
                return $response;
            }

            //Mandatory field
            $token = $request->token;
            $email_id = $request->email_id;

            if (($response = (new VerificationController())->verifyTokenForResetPassword($email_id, $token)) != '') {
                return $response;
            }

            $response = Response::json(['code' => 200, 'message' => 'Link verified successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('verifyResetPasswordLink', $e);
            //            Log::error("verifyResetPasswordLink :", ["Exception" => $e->getMessage(), "\nTraceAsString :" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'verify reset password link.', 'cause' => '', 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/resetPassword",
     *        tags={"Users"},
     *        operationId="resetPassword",
     *        summary="Reset password",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"email_id","token","new_password"},
     *
     *          @SWG\Property(property="email_id",  type="string", example="robert@grr.la", description=""),
     *          @SWG\Property(property="token",  type="string", example="djfjbjdbgjdghjbdjfbkbcjfuhudbjsbcjsbd", description=""),
     *          @SWG\Property(property="new_password",  type="string", example="demo@1234", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="success",
     *        ),
     * 		@SWG\Response(
     *            response="201",
     *            description="error",
     *        ),
     *    )
     */
    public function resetPassword(Request $request_body)
    {
        try {
            $request = json_decode($request_body->getContent());
            $response = (new VerificationController())->validateRequiredParameter(['email_id', 'token', 'new_password'], $request);
            if ($response != '') {
                return $response;
            }

            //Mandatory field
            $email_id = $request->email_id;
            $token = $request->token;
            $string = (new ImageController())->removeEmoji($request->new_password);
            if ($request->new_password != $string) {
                return Response::json(['code' => 201, 'message' => 'Password must be contain only alphabets,numeric and special character.', 'cause' => '', 'data' => '']);
            }
            $new_password = Hash::make($request->new_password);
            $create_time = date(Config::get('constant.DATE_FORMAT'));

            if (($response = (new VerificationController())->verifyTokenForResetPassword($email_id, $token)) != '') {
                return $response;
            }

            $result = DB::select('SELECT
                                    email_id,
                                    reset_token,
                                    reset_token_expire
                                  FROM
                                    user_pwd_reset_token_master
                                  WHERE
                                    email_id = ? AND
                                    reset_token = ? AND
                                    reset_token_expire > ?', [$email_id, $token, $create_time]);
            DB::beginTransaction();

            if (count($result) > 0) {

                $social_user = DB::select('SELECT 1 FROM user_master WHERE email_id = ? AND is_multi_login = ? AND signup_type IN(2,3)', [$email_id, 0]);
                if ($social_user) {
                    DB::update('UPDATE user_master SET password = ?, is_multi_login = ? WHERE email_id = ?', [$new_password, 1, $email_id]);
                } else {
                    DB::update('UPDATE user_master SET password = ? WHERE email_id = ?', [$new_password, $email_id]);
                }

                DB::delete('DELETE FROM user_pwd_reset_token_master WHERE email_id = ? AND reset_token = ?', [$email_id, $token]);
                $response = Response::json(['code' => 200, 'message' => 'Password set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

                $user_detail = $this->getUserInfoByEmailId($email_id);
                if (! $social_user) {
                    $this->invalidateUserSessions($user_detail->user_id);
                    DB::delete('DELETE FROM user_session WHERE user_id = ?', [$user_detail->user_id]);
                }

                //send email
                $template = 'simple';
                $subject = 'PhotoADKing: Reset Password';
                //$message_body = 'Your password has been updated successfully.';
                $message_body = [
                    'message' => "The password for your PhotoADKing Account $email_id has been updated successfully.",
                    'user_name' => $user_detail->first_name];
                $api_name = 'resetPassword';
                $api_description = 'reset password.';
                $this->dispatch(new SendMailJob(1, $email_id, $subject, $message_body, $template, $api_name, $api_description));

            } else {
                $response = Response::json(['code' => 201, 'message' => 'Please enter valid credential.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::commit();

        } catch (Exception $e) {
            (new ImageController())->logs('resetPassword', $e);
            //            Log::error("resetPassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'reset password.', 'cause' => '', 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /*--------------------------------------------------------Common for ALL------------------------------------------*/

    /**
     * - Common For All ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/doLogout",
     *        tags={"Common For All"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="doLogout",
     *        summary="Logout",
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
    public function doLogout()
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);
            $user_id = $user_detail->id;

            DB::delete('DELETE FROM user_session WHERE token = ? AND user_id = ?', [$token, $user_id]);
            //DB::update('UPDATE user_session SET is_active = 0 WHERE token = ? AND user_id = ?', [$token, $user_id]);
            JWTAuth::invalidate($token);
            $response = Response::json(['code' => 200, 'message' => 'User have successfully logged out.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('doLogout', $e);
            //Log::error("doLogout : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'logout user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * @api {post} doUserAllSessionLogout   doUserAllSessionLogout
     *
     * @apiName doUserAllSessionLogout
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
     * "user_id":1
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "User have successfully logged out.",
     * "cause": "",
     * "data": {
     *
     * }
     * }
     */
    public function doUserAllSessionLogout(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['user_id'], $request)) != '') {
                return $response;
            }

            $user_id = $request->user_id;

            //      $user_token = DB::select('SELECT token FROM user_session WHERE user_id=?',[$user_id]);
            //      foreach ($user_token AS $i => $all_token){
            //        JWTAuth::invalidate($all_token->token);
            //      }

            DB::beginTransaction();
            DB::delete('DELETE FROM user_session WHERE user_id=?', [$user_id]);
            DB::commit();
            $response = Response::json(['code' => 200, 'message' => 'User all session have successfully logged out.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('doUserAllSessionLogout', $e);
            //      Log::error("doUserAllSessionLogout : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'logout user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Common For All ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/changePassword",
     *        tags={"Common For All"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="changePassword",
     *        summary="Set new password",
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
     *          required={"current_password","new_password"},
     *
     *          @SWG\Property(property="current_password",  type="string", example="demo@123", description=""),
     *          @SWG\Property(property="new_password",  type="string", example="demo@1234", description=""),
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
    public function changePassword(Request $request)
    {
        try {
            //get token & match the token
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['current_password', 'new_password'], $request)) != '') {
                return $response;
            }
            //Mandatory field
            $user_data = JWTAuth::parseToken()->authenticate();
            $email_id = $user_data->email_id;
            $user_id = $user_data->id;
            $current_password = $request->current_password;
            $string = (new ImageController())->removeEmoji($request->new_password);
            if ($request->new_password != $string) {
                return Response::json(['code' => 201, 'message' => 'Password must be contain only alphabets,numeric and special character.', 'cause' => '', 'data' => '']);
            }
            $new_password = Hash::make($request->new_password);

            $credential = ['email_id' => $email_id, 'password' => $current_password];
            if (! $old_token = JWTAuth::attempt($credential)) {

                return Response::json(['code' => 201, 'message' => 'Current password is incorrect.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            DB::beginTransaction();
            $social_user = DB::select('SELECT 1 FROM user_master WHERE email_id = ? AND is_multi_login = ? AND signup_type IN(2,3)', [$email_id, 0]);
            if ($social_user) {
                DB::update('UPDATE user_master SET password = ?, is_multi_login = ? WHERE email_id = ?', [$new_password, 1, $email_id]);
            } else {
                DB::update('UPDATE user_master SET password = ? WHERE email_id = ?', [$new_password, $email_id]);
            }
            DB::commit();

            $credential = ['email_id' => $email_id, 'password' => $request->new_password];

            if ($new_token = JWTAuth::attempt($credential)) {
                //Log::info('change pass  :', ['old_token' => $token, 'new_token' => $new_token]);
                $this->invalidateUserSessions($user_id);
                DB::beginTransaction();
                $result = DB::update('UPDATE user_session
                          SET
                          token = ?
                          WHERE token = ?', [$new_token, $token]);
                DB::commit();

                DB::delete('DELETE FROM user_session WHERE token != ? AND user_id = ?', [$new_token, $user_id]);

                DB::commit();
                //Log::info('result of change password :', ['result' => $result]);
            }

            $response = Response::json(['code' => 200, 'message' => 'Password changed successfully.', 'cause' => '', 'data' => ['token' => $new_token]]);

        } catch (Exception $e) {
            (new ImageController())->logs('changePassword', $e);
            //            Log::error("changePassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'change password.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function setPassword(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user_data = JWTAuth::toUser($token);

            $request = json_decode($request->getContent());
            /* Required request data parameter */
            if (($response = (new VerificationController())->validateRequiredParameter(['new_password'], $request)) != '') {
                return $response;
            }

            /* Mandatory fields */
            $email_id = $user_data->email_id;
            $user_id = $user_data->id;
            $string = (new ImageController())->removeEmoji($request->new_password);
            if ($request->new_password != $string) {
                return Response::json(['code' => 201, 'message' => 'Password must be contain only alphabets,numeric and special character.', 'cause' => '', 'data' => '']);
            }
            $new_password = Hash::make($request->new_password);

            DB::beginTransaction();
            DB::update('UPDATE user_master
                  SET
                    password = ?,
                    is_multi_login = 1
                  WHERE email_id = ?', [$new_password, $email_id]);

            $credential = ['email_id' => $email_id, 'password' => $request->new_password];
            if ($new_token = JWTAuth::attempt($credential)) {

                DB::update('UPDATE user_session
                    SET
                      token = ?
                    WHERE token = ?', [$new_token, $token]);
                DB::commit();
            }
            $response = Response::json(['code' => 200, 'message' => 'Password changed successfully.', 'cause' => '', 'data' => ['token' => $new_token, 'is_multi_login' => 1]]);

        } catch (Exception $e) {
            (new ImageController())->logs('setPassword', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'change password.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /*========================================================| Manage 1st time logged in user |=================================================*/

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/setValueOfFirstTimeLogin",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="setValueOfFirstTimeLogin",
     *        summary="setValueOfFirstTimeLogin",
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
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Value set successfully.","cause":"","data":{}}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Error",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to set value of first time login.","cause":"Exception message","data":"{}"}),),
     *        ),
     *      )
     */
    public function setValueOfFirstTimeLogin()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user_id = JWTAuth::toUser($token)->id;

            DB::beginTransaction();
            DB::update('UPDATE user_master SET is_once_logged_in = 1 WHERE id = ?', [$user_id]);
            DB::commit();
            $response = Response::json(['code' => 200, 'message' => 'Value set successfully.', 'cause' => '', 'data' => json_decode('{}')]);

        } catch (Exception $e) {
            (new ImageController())->logs('setValueOfFirstTimeLogin', $e);
            //        Log::error("setValueOfFirstTimeLogin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'set value of first time login.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /*========================================================| Sub functions |=================================================*/

    // create new user session
    public function createNewSession($user_id, $token)
    {
        try {

            $create_time = date('Y-m-d H:i:s');
            $user_session_data = [
                'user_id' => $user_id,
                'token' => $token,
                'create_time' => $create_time,
            ];

            DB::beginTransaction();
            $user_session_id = DB::table('user_session')->insertGetId($user_session_data);
            DB::commit();

            //              DB::beginTransaction();
            //            DB::insert('INSERT INTO user_session
            //                                    (user_id, token, create_time)
            //                                    VALUES (?,?,?)',
            //                [$user_id, $token, $create_time]);
            //            DB::commit();
            $response = $user_session_id;

        } catch (Exception $e) {
            (new ImageController())->logs('createNewSession', $e);
            //            Log::error("createNewSession : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'create session.', 'cause' => '', 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function createNewSessionV2($user_id, $token, $device_json)
    {
        try {
            $user_session_data = [
                'user_id' => $user_id,
                'token' => $token,
                'device_json' => $device_json,
            ];
            DB::table('user_session')->insert($user_session_data);

            $response = true;

        } catch (Exception $e) {
            (new ImageController())->logs('createNewSessionV2', $e);
            $response = false;
        }

        return $response;
    }

    public function getUserInfoByUserId($user_id)
    {
        try {

            $result = DB::select('select
                                              um.uuid as user_id,
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
                                              COALESCE(um.is_multi_login,0) as is_multi_login,
                                              COALESCE(um.is_verify,0) as is_verify,
                                              COALESCE(um.is_once_logged_in,0) as is_once_logged_in,
                                              COALESCE(um.mailchimp_subscr_id,"") as mailchimp_subscr_id,
                                              COALESCE(scd.balance,"") as balance,
                                              COALESCE(ud.user_keyword,"") AS user_keyword,
                                              ru.role_id,
                                              um.create_time,
                                              um.update_time
                                              FROM
                                                user_master um LEFT JOIN role_user AS ru ON um.id = ru.user_id
                                              LEFT JOIN user_detail ud ON um.id = ud.user_id
                                              LEFT JOIN stripe_customer_details scd ON um.id = scd.user_id
                                              WHERE
                                                um.id=?', [$user_id]);

            /** Check if user subscription is in already pending status (using this avoid to make payment two time by user) */
            $is_exist = DB::select('SELECT
                                        id
                                      FROM
                                        payment_status_master
                                      WHERE
                                        user_id = ? AND
                                        ipn_status = 0 AND
                                        is_active = 0 AND
                                        expiration_time IS NULL AND
                                        DATE(update_time) >= DATE(DATE_SUB(NOW(), INTERVAL 24 HOUR))
                                      ORDER BY update_time DESC LIMIT 0,1', [$user_id]);

            if (isset($result[0]->role_id)) {

                if (count($is_exist) > 0) {
                    $result[0]->is_pending = 1;
                } else {
                    $result[0]->is_pending = 0;
                }

                $expiry_detail = $this->getPaymentsExpiryTime($user_id, $result[0]->role_id);

                $result[0]->subscr_expiration_time = $expiry_detail['final_expiration_time'];
                $result[0]->next_billing_date = $expiry_detail['next_billing_date'];
                $result[0]->is_subscribe = $expiry_detail['is_subscribe'];
                $result[0]->payment_type = $expiry_detail['payment_type'];
                $result[0]->is_new_rule_applied = $expiry_detail['is_new_rule_applied'];

                $response = (count($result) != 0) ? $result[0] : json_decode('{}');
            } else {
                $response = json_decode('{}');
            }
            //          Log::info('result : ',[$result]);

        } catch (Exception $e) {
            (new ImageController())->logs('getUserInfoByUserId', $e);
            //            Log::error("getUserInfoByUserId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'fetch user detail.', 'cause' => '', 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getUserDetailsByUserId($user_id)
    {
        try {
            if (is_numeric($user_id)) {
                $where_condition = 'um.id=?';
            } else {
                $where_condition = 'um.uuid=?';
            }
            $response = DB::select('SELECT
                                      um.uuid AS user_uuid,
                                      um.id AS user_id,
                                      COALESCE(um.user_name,"") AS user_name,
                                      COALESCE(um.email_id,"") AS email_id,
                                      COALESCE(ud.first_name,"") AS first_name
                                FROM
                                      user_master AS um
                                        LEFT JOIN user_detail AS ud ON um.id = ud.user_id
                                WHERE
                                      '.$where_condition.'  ', [$user_id]);

        } catch (Exception $e) {
            (new ImageController())->logs('getUserDetailsByUserId', $e);
            //Log::error("getUserDetailsByUserId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'fetch user name & email_id.', 'cause' => '', 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function getUserInfoByEmailId($email_id)
    {
        try {

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
                                      COALESCE(um.is_once_logged_in,0) as is_once_logged_in,
                                      COALESCE(um.mailchimp_subscr_id,"") as mailchimp_subscr_id,
                                      ru.role_id,
                                      um.create_time,
                                      um.update_time
                                    FROM
                                      user_master um LEFT JOIN role_user AS ru ON um.id = ru.user_id
                                      LEFT JOIN user_detail ud ON um.id = ud.user_id
                                    WHERE
                                      um.email_id=?', [$email_id]);

            $expiry_detail = $this->getPaymentsExpiryTime($result[0]->user_id, $result[0]->role_id);

            $result[0]->subscr_expiration_time = $expiry_detail['final_expiration_time'];
            $result[0]->next_billing_date = $expiry_detail['next_billing_date'];
            $result[0]->is_subscribe = $expiry_detail['is_subscribe'];

            $response = (count($result) != 0) ? $result[0] : json_decode('{}');

        } catch (Exception $e) {
            (new ImageController())->logs('getUserInfoByEmailId', $e);
            //            Log::error("getUserInfoByEmailId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'fetch user detail.', 'cause' => '', 'data' => json_decode('{}')]);
        }

        return $response;
    }

    public function addNewDeviceToUser($user_id, $device_reg_id, $device_platform, $device_model_name, $device_vendor_name, $device_os_version, $device_udid, $device_resolution, $device_carrier, $device_country_code, $device_language, $device_local_code, $device_default_time_zone, $device_application_version, $device_type, $device_registration_date, $device_library_version, $device_latitude, $device_longitude, $project_package_name, $device_registered_from, $ip_address, $user_session_id)
    {
        try {
            //            $created_date = date(Config::get('constant.DATE_FORMAT'));

            /*$device_result = DB::select('SELECT
                                            device_id
                                          FROM
                                            device_master
                                          WHERE
                                            user_id = ? AND
                                            device_udid = ? AND
                                            device_registered_from = ?', [$user_id, $device_udid, $device_registered_from]);

            if (count($device_result) != 0) {
                DB::beginTransaction();
                DB::update('UPDATE device_master
                            SET is_active = 0
                                WHERE  user_id = ?',
                    [$user_id]);
                DB::commit();

                DB::update('UPDATE device_master SET
                                device_reg_id = ?,
                                is_active = 1
                              WHERE
                                user_id = ? AND
                                device_udid = ? AND
                                device_registered_from = ?', [$device_reg_id, $user_id, $device_udid, $device_registered_from]);

                DB::commit();

                $response = $device_result[0]->device_id;
            } else {*/
            DB::beginTransaction();
            DB::update('UPDATE device_master
                            SET is_active = 0
                                WHERE  user_id = ?',
                [$user_id]);

            if ($device_latitude == '' or $device_latitude == null) {
                $device_latitude = 0.0;
            }

            if ($device_longitude == '' or $device_longitude == null) {
                $device_longitude = 0.0;
            }

            DB::commit();
            $results = DB::table('device_master')->insertGetId(
                ['user_id' => $user_id,
                    'device_reg_id' => $device_reg_id,
                    'device_platform' => $device_platform,
                    'device_model_name' => $device_model_name,
                    'device_vendor_name' => $device_vendor_name,
                    'device_os_version' => $device_os_version,
                    'device_udid' => $device_udid,
                    'device_resolution' => $device_resolution,
                    'device_carrier' => $device_carrier,
                    'device_country_code' => $device_country_code,
                    'device_language' => $device_language,
                    'device_local_code' => $device_local_code,
                    'device_default_time_zone' => $device_default_time_zone,
                    'device_library_version' => $device_library_version,
                    'device_application_version' => $device_application_version,
                    'device_type' => $device_type,
                    'device_latitude' => $device_latitude,
                    'device_longitude' => $device_longitude,
                    'project_package_name' => $project_package_name,
                    'device_registration_date' => $device_registration_date,
                    'device_registered_from' => $device_registered_from,
                    'ip_address' => $ip_address,
                    'user_session_id' => $user_session_id,
                ]
            );
            DB::commit();
            $response = $results;
            //Log::debug('device_reg_id', ["Exception" => $device_reg_id]);
            /*}*/

        } catch (Exception $e) {
            (new ImageController())->logs('addNewDeviceToUser', $e);
            //            Log::error("addNewDeviceToUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'add user device.', 'cause' => '', 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function getPaymentsExpiryTime($user_id, $role_id)
    {
        try {

            if ($role_id == 7) {
                $expires = date('Y-m-d H:i:s');
                $date = new DateTime($expires);
                $date->modify('+1 year');
                $final_expiration_time = $date->format('Y-m-d H:i:s');
                $next_billing_date = $final_expiration_time;
                $is_subscribe = 1;
                $payment_type = 0;
                $is_new_rule_applied = 1;
            } else {
                /*$result = DB::select('SELECT
                                          psm.id,
                                          COALESCE (scm.expiration_time,"") AS expiration_time,
                                          COALESCE (scm.final_expiration_time,"") AS final_expiration_time,
                                          psm.expiration_time AS temp_expiration_time,
                                          COALESCE (psm.is_active,0) AS is_active
                                        FROM subscriptions AS scm
                                          LEFT JOIN payment_status_master AS psm ON  psm.txn_id = scm.transaction_id
                                        WHERE psm.user_id = ? ORDER BY scm.expiration_time DESC', [$user_id]);*/

                //changed order by clause from exp_time to final_exp_time because of http://159.203.133.168/redmine/issues/16239.
                $result = DB::select('SELECT
                                          psm.id,
                                          COALESCE (scm.create_time,"") AS create_time,
                                          COALESCE (scm.expiration_time,"") AS expiration_time,
                                          COALESCE (scm.final_expiration_time,"") AS final_expiration_time,
                                          psm.expiration_time AS temp_expiration_time,
                                          COALESCE (scm.is_active,0) AS is_active,
                                          COALESCE (scm.payment_type,0) AS payment_type
                                        FROM subscriptions AS scm
                                          LEFT JOIN payment_status_master AS psm ON  psm.txn_id = scm.transaction_id
                                        WHERE scm.user_id = ? UNION
                                        SELECT
                                          psm.id,
                                          COALESCE (scm.create_time,"") AS create_time,
                                          COALESCE (scm.expiration_time,"") AS expiration_time,
                                          COALESCE (scm.final_expiration_time,"") AS final_expiration_time,
                                          psm.expiration_time AS temp_expiration_time,
                                          COALESCE (psm.is_active,0) AS is_active,
                                          COALESCE (scm.payment_type,0) AS payment_type
                                        FROM payment_status_master AS psm
                                          LEFT JOIN subscriptions AS scm ON  psm.txn_id = scm.transaction_id
                                        WHERE psm.user_id = ?
                                        ORDER BY final_expiration_time DESC', [$user_id, $user_id]);

                if (count($result) > 0) {
                    if ($result[0]->final_expiration_time != '' or $result[0]->final_expiration_time != null) {
                        $final_expiration_time = $result[0]->final_expiration_time;
                        $next_billing_date = $result[0]->expiration_time;

                    } else {
                        $final_expiration_time = $result[0]->temp_expiration_time;
                        $next_billing_date = $result[0]->temp_expiration_time;

                    }
                    $is_active = $result[0]->is_active;
                    $payment_type = $result[0]->payment_type;
                    if ($is_active == 1) {
                        $is_subscribe = 1;
                    } else {
                        $is_subscribe = 0;
                    }

                    if (($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER') || $role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) && $result[0]->create_time <= Config::get('constant.DATE_OF_NEW_RULES')) {
                        $is_new_rule_applied = 0;
                    } else {
                        $is_new_rule_applied = 1;
                    }

                    if ($role_id == Config::get('constant.ROLE_ID_FOR_LIFETIME_PRO') && $final_expiration_time <= date('Y-m-d H:i:s') && $is_active = 1) {
                        $result[0]->subscr_expiration_time = date('Y-m-d H:i:s', strtotime('+5 years'));
                        DB::update('UPDATE subscriptions SET final_expiration_time = ? WHERE user_id = ?', [$result[0]->subscr_expiration_time, $user_id]);
                    }

                } else {
                    $final_expiration_time = '';
                    $next_billing_date = '';
                    $is_subscribe = 2;
                    $payment_type = 0;
                    $is_new_rule_applied = 1;
                }
            }

            //0=subscription is inactive/expired/cancel,1=subscribed,2=subscription not purchased

            $response = ['is_new_rule_applied' => $is_new_rule_applied, 'final_expiration_time' => $final_expiration_time, 'next_billing_date' => $next_billing_date, 'is_subscribe' => $is_subscribe, 'payment_type' => $payment_type];

        } catch (Exception $e) {
            (new ImageController())->logs('getPaymentsExpiryTime', $e);
            //            Log::error("getPaymentsExpiryTime : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = ['is_new_rule_applied' => 1, 'final_expiration_time' => '', 'next_billing_date' => '', 'is_subscribe' => 2];
        }

        return $response;
    }

    public function invalidateUserSessions($user_id)
    {
        try {

            $user_sessions = DB::select('SELECT token FROM user_session WHERE user_id = ?', [$user_id]);
            foreach ($user_sessions as $i => $user_session) {
                try {
                    JWTAuth::setToken($user_session->token);
                    JWTAuth::invalidate($user_session->token);
                } catch (TokenExpiredException $e) {
                    Log::error('invalidateUserSessions : TokenExpiredException :', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                } catch (TokenBlacklistedException $e) {
                    Log::error('invalidateUserSessions : TokenBlacklistedException :', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                } catch (JWTException $e) {
                    Log::error('invalidateUserSessions : JWTException :', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
                }
            }
            $response = true;

        } catch (Exception $e) {
            (new ImageController())->logs('invalidateUserSessions', $e);
            $response = false;
        }

        return $response;
    }

    public function socialUserDetail($token, $signup_type)
    {
        try {
            $client = new Client();
            if ($signup_type == 2) {
                /* Get facebook user information by passing access_token */
                $response = $client->get('https://graph.facebook.com/me?access_token='.$token.'&fields=name%2Cemail%2Cfirst_name%2Clast_name&method=get&pretty=0&sdk=joey&suppress_http_code=1');
                $user_details = isset($response) ? json_decode($response->getBody()->getContents()) : '';
                if ($user_details) {
                    $fb_email = isset($user_details->email) ? $user_details->email : '';
                    $fb_social_uid = isset($user_details->id) ? $user_details->id : '';
                    $first_name = isset($user_details->first_name) ? $user_details->first_name : '';
                    $last_name = isset($user_details->last_name) ? $user_details->last_name : '';

                    return ['email' => $fb_email, 'social_uid' => $fb_social_uid, 'first_name' => $first_name, 'last_name' => $last_name];
                }
                Log::error('socialUserDetail : unable to get social user information.', ['response' => $response]);

                return $response = '';

            } elseif ($signup_type == 3) {
                /* Get google user information by passing id_token */
                $response = $client->get('https://oauth2.googleapis.com/tokeninfo?id_token='.$token);
                $user_details = isset($response) ? json_decode($response->getBody()->getContents()) : '';
                if ($user_details) {
                    $google_email = isset($user_details->email) ? $user_details->email : '';
                    $google_social_uid = isset($user_details->sub) ? $user_details->sub : '';
                    $first_name = isset($user_details->given_name) ? $user_details->given_name : '';
                    $last_name = isset($user_details->family_name) ? $user_details->family_name : '';

                    return ['email' => $google_email, 'social_uid' => $google_social_uid, 'first_name' => $first_name, 'last_name' => $last_name];
                }
                Log::error('socialUserDetail : unable to get social user information.', ['response' => $response]);

                return $response = '';

            } else {
                return $response = Response::json(['code' => 201, 'message' => 'Invalid signup type for social login.', 'cause' => '', 'data' => json_decode('{}')]);
            }
        } catch (RequestException  $e) {
            (new ImageController())->logs('socialUserDetail', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get social user information.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }
}
