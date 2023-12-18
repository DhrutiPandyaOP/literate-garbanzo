<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 20-Sep-18
 * Time: 3:12 PM
 */

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\User_Master;
use Auth;
use Config;
use DB;
use Exception;
use File;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use JWTAuth;
use Log;
use Mail;
use Response;
use Swagger\Annotations as SWG;

/**
 * Class RegisterController
 */
class RegisterController extends Controller
{
    /**
     * @SWG\Swagger(
     *        basePath=L5_SWAGGER_BASE_PATH,
     *        host=L5_SWAGGER_CONST_HOST,
     *        produces={"application/json"},
     *        consumes={"application/json"},
     *
     * 		@SWG\Info(
     *            title="PhotoADKing",
     *            description="API Documentation of PhotoADKing",
     *            version="1.0",
     *            termsOfService="http://optimumbrew.com/#contact",
     *
     *         @SWG\Contact(name="Rushita Talaviya",url="http://192.168.0.115/photoadking_testing_saveasmodule/swagger-ui-master/dist/#/",email="rushita.optimumbrew@gmail.com"),
     *        ),
     *        schemes={L5_SWAGGER_SCHEMES},
     *
     *      @SWG\SecurityScheme(
     *         securityDefinition="Bearer",
     *         type="apiKey",
     *         name="Authorization",
     *         in="header"
     *        ),
     * )
     */

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/registerUserDeviceByDeviceUdid",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="registerUserDeviceByDeviceUdid",
     *        summary="registerUserDeviceByDeviceUdid",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_data",
     *        type="string",
     *
     *   	  @SWG\Schema(
     *
     *          @SWG\Property(property="device_reg_id",  type="string"),
     *          @SWG\Property(property="device_udid",  type="string"),
     *          @SWG\Property(property="device_platform",  type="string"),
     *          @SWG\Property(property="device_model_name",  type="string"),
     *          @SWG\Property(property="device_vendor_name",  type="string"),
     *          @SWG\Property(property="device_os_version",  type="string"),
     *          @SWG\Property(property="device_resolution",  type="string"),
     *          @SWG\Property(property="device_carrier",  type="string"),
     *          @SWG\Property(property="device_country_code",  type="string"),
     *          @SWG\Property(property="device_language",  type="string"),
     *          @SWG\Property(property="device_local_code",  type="string"),
     *          @SWG\Property(property="device_default_time_zone",  type="string"),
     *          @SWG\Property(property="device_application_version",  type="string"),
     *          @SWG\Property(property="device_type",  type="string"),
     *          @SWG\Property(property="device_registration_date",  type="string"),
     *          @SWG\Property(property="device_library_version",  type="string")
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Device registered successfully.","cause":"","data":"{}"}),),
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
    public function registerUserDeviceByDeviceUdid(Request $request_body)
    {
        try {

            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode(file_get_contents('php://input'));

            //$request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['device_udid'], $request)) != '') {
                return $response;
            }

            $device_udid = $request->device_udid; //compulsory
            $device_reg_id = isset($request->device_reg_id) ? $request->device_reg_id : ''; //compulsory
            $device_platform = isset($request->device_platform) ? $request->device_platform : ''; //compulsory
            $device_model_name = isset($request->device_model_name) ? $request->device_model_name : '';
            $device_vendor_name = isset($request->device_vendor_name) ? $request->device_vendor_name : '';
            $device_os_version = isset($request->device_os_version) ? $request->device_os_version : '';
            $device_resolution = isset($request->device_resolution) ? $request->device_resolution : '';
            $device_carrier = isset($request->device_carrier) ? $request->device_carrier : '';
            $device_country_code = isset($request->device_country_code) ? $request->device_country_code : '';
            $device_language = isset($request->device_language) ? $request->device_language : '';
            $device_local_code = isset($request->device_local_code) ? $request->device_local_code : '';
            $device_default_time_zone = isset($request->device_default_time_zone) ? $request->device_default_time_zone : '';
            $device_library_version = isset($request->device_library_version) ? $request->device_library_version : '';
            $device_application_version = isset($request->device_application_version) ? $request->device_application_version : '';
            $device_type = isset($request->device_type) ? $request->device_type : '';
            $device_latitude = isset($request->device_latitude) ? $request->device_latitude : '';
            $device_longitude = isset($request->device_longitude) ? $request->device_longitude : '';
            $project_package_name = isset($request->project_package_name) ? $request->project_package_name : '';
            $device_registration_date = isset($request->device_registration_date) ? $request->device_registration_date : '';

            $result = DB::select('SELECT 1 FROM device_master WHERE device_udid = ?', [$device_udid]);
            //Log::info('registerUserDeviceByDeviceUdid', ['total device having udid from request' => sizeof($result)]);
            if (count($result) == 0) {
                //Log::info('registerUserDeviceByDeviceUdid', ['device_reg_id' => $device_reg_id]);
                DB::beginTransaction();

                $result = DB::insert('INSERT INTO device_master
                            (user_id,
                            device_reg_id,
                            device_platform,
                            device_model_name,
                            device_vendor_name,
                            device_os_version,
                            device_udid,
                            device_resolution,
                            device_carrier,
                            device_country_code,
                            device_language,
                            device_local_code,
                            device_default_time_zone,
                            device_library_version,
                            device_application_version,
                            device_type,
                            device_latitude,
                            device_longitude,
                            project_package_name,
                            device_registration_date)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ',
                    [$user_id, $device_reg_id,
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
                        $device_library_version,
                        $device_application_version,
                        $device_type,
                        $device_latitude,
                        $device_longitude,
                        $project_package_name,
                        $device_registration_date,
                    ]);
                //Log::info(['result' => $result]);

            } else {
                $result = DB::update('UPDATE device_master
                            SET device_reg_id = ?, user_id = ?
                                WHERE device_udid = ?',
                    [$device_reg_id, $user_id, $device_udid]);
                //Log::info(['update result' => $result]);

                DB::commit();
            }

            DB::commit();

            $response = Response::json(['code' => 200, 'message' => 'Device registered successfully.', 'cause' => '', 'data' => json_decode('{}')]);
            $response->headers->set('Cache-Control', Config::get('constant.RESPONSE_HEADER_CACHE'));

        } catch (Exception $e) {
            (new ImageController())->logs('registerUserDeviceByDeviceUdid', $e);
            //          Log::error("registerUserDeviceByDeviceUdid : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'register device.', 'cause' => '', 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/userSignUp",
     *        tags={"Users"},
     *        operationId="userSignUp",
     *        summary="userSignUp",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"first_name","email_id","password","signup_type"},
     *
     *          @SWG\Property(property="first_name",  type="string", example="Steave", description=""),
     *          @SWG\Property(property="email_id",  type="string", example="steave@grr.la", description=""),
     *          @SWG\Property(property="password",  type="string", example="demo@123", description=""),
     *          @SWG\Property(property="signup_type",  type="integer", example=1, description="1=email, 2=facebook, 3=google"),
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
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Verification link has been sent to your email. Please verify your account by verification link.","cause":"","data":{"user_registration_temp_id":44}}),),
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
    public function userSignUp(Request $request)
    {
        try {

            $request = json_decode($request->getContent());
            /* Required request data parameter */
            if (($response = (new VerificationController())->validateRequiredParameter(['first_name', 'email_id', 'password', 'signup_type', 'device_info'], $request)) != '') {
                return $response;
            }

            $device_info = $request->device_info;
            $device_info->ip_address = request()->ip();
            $create_time = date('Y-m-d H:i:s');
            $request_json = json_encode($request);
            $email_id = $request->email_id;

            /* Required device_info parameter */
            if (($response = (new VerificationController())->validateRequiredParam(['device_application_version', 'device_carrier', 'device_country_code', 'device_default_time_zone', 'device_language', 'device_latitude', 'device_library_version', 'device_local_code', 'device_longitude', 'device_model_name', 'device_os_version', 'device_platform', 'device_reg_id', 'device_registration_date', 'device_resolution', 'device_type', 'device_udid', 'device_vendor_name', 'project_package_name', 'ip_address'], $device_info)) != '') {
                Log::error('userSignUp : Required field some device_info\'s data is missing or empty.', ['response' => $response]);

                return $response;
            }

            /* Remove emojis from password */
            $string = (new ImageController())->removeEmoji($request->password);
            if ($request->password != $string) {
                return Response::json(['code' => 201, 'message' => 'Password must be contain only alphabets,numeric and special character.', 'cause' => '', 'data' => '']);
            }

            /* Remove emojis from first_name */
            $first_name = (new ImageController())->removeEmoji($request->first_name);
            if (trim($first_name) == '') {
                return Response::json(['code' => 201, 'message' => 'Please enter valid first name.', 'cause' => '', 'data' => '']);
            }

            /* Verify email format */
            if (! filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
                Log::error('userSignUp : E-mail is in the wrong format.', ['email_id' => $email_id]);

                return Response::json(['code' => 201, 'message' => 'E-mail is in the wrong format.', 'cause' => '', 'data' => '']);
            }

            /* Check email is valid or not */
            if (($response = (new VerificationController())->checkDisposableEmail($email_id)) != '') {
                return $response;
            }

            /* Check email is already exist in database or not */
            if (($response = (new VerificationController())->checkIfEmailExist($email_id)) != '') {
                return $response;
            }

            $verification_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token

            DB::beginTransaction();
            /* Generate unique id and store user registration data temporary in database */
            $uuid = (new ImageController())->generateUUID();
            $data = ['email_id' => $email_id,
                'request_json' => $request_json,
                'uuid' => $uuid,
                //'token' => $verification_token,
                'create_time' => $create_time,
            ];
            $user_reg_temp_id = DB::table('user_registration_temp')->insertGetId($data);

            /* Set expiration time for verification token in database */
            $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+'.Config::get('constant.OTP_EXPIRATION_TIME').' minutes'));
            DB::insert('INSERT INTO otp_codes
                        (email_id, otp_token,otp_token_expire,user_registration_temp_id,create_time)
                        values (? ,? ,?, ?, ?)',
                [$email_id, $verification_token, $otp_token_expire, $user_reg_temp_id, $create_time]);
            DB::commit();

            /* Dispatch job to send verification mail to user */
            $template = 'verify_user';
            $subject = 'PhotoADKing: Verification Link';
            $message_body = [
                'message' => 'Please verify your email by click on below link',
                'token' => $verification_token,
                'user_registration_temp_id' => $uuid,
                'email_id' => $email_id,
                'first_name' => $first_name,
                'redirect_url' => Config::get('constant.USER_VERIFICATION_LINK_URL'),
            ];
            $api_name = 'userSignUp';
            $api_description = 'Send mail for OTP verification.';
            $this->dispatch(new SendMailJob($uuid, $email_id, $subject, $message_body, $template, $api_name, $api_description));

            /* This process is for marketing purpose */
            (new MailchimpController())->subscribeUserByEmail($email_id, 'signup_not_verified');
            $response = Response::json(['code' => 200, 'message' => 'Verification link has been sent to your email. Please verify your account by verification link.', 'cause' => '', 'data' => ['user_registration_temp_id' => $uuid]]);

        } catch (Exception $e) {
            (new ImageController())->logs('userSignUp', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'register user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/resendVerificationLink",
     *        tags={"Users"},
     *        operationId="resendVerificationLink",
     *        summary="resendVerificationLink",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"user_registration_temp_id"},
     *
     *          @SWG\Property(property="user_registration_temp_id",  type="integer", example=1, description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Verification link has been sent to your email. Please verify your account by verification link.","cause":"","data":"{}"}),),
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
    public function resendVerificationLink(Request $request)
    {
        try {

            $request = json_decode($request->getContent());

            $response = (new VerificationController())->validateRequiredParameter(['user_registration_temp_id'], $request);
            if ($response != '') {
                return $response;
            }

            DB::beginTransaction();

            $user_registration_temp_id = $request->user_registration_temp_id;

            $user_id = DB::select('select id from user_registration_temp where uuid=?', [$user_registration_temp_id]);
            if (count($user_id) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $user_registration_temp_id = $user_id[0]->id;

            $result = DB::select('SELECT email_id, otp_token,
                                          otp_token_expire
                                          FROM otp_codes
                                          WHERE user_registration_temp_id = ? order by create_time desc limit 1', [$user_registration_temp_id]);

            if (count($result) > 0) {
                $email_id = $result[0]->email_id;
                $otp_token_expire = $result[0]->otp_token_expire;
                //$create_time = date(Config::get('constant.DATE_FORMAT'));

                if (($response = (new VerificationController())->checkIfEmailExist($email_id)) != '') {
                    return Response::json(['code' => 201, 'message' => 'Your email already verified. Please login.', 'cause' => '', 'data' => json_decode('{}')]);
                }
                //return $response;

                if (strtotime(date('Y-m-d H:i:s')) > strtotime($otp_token_expire)) {

                    $verification_token = bin2hex(openssl_random_pseudo_bytes(50)); //generate a random token
                    $otp_token_expire = date(Config::get('constant.DATE_FORMAT'), strtotime('+'.Config::get('constant.OTP_EXPIRATION_TIME').' minutes'));

                    DB::update('UPDATE otp_codes
                                    SET otp_token = ?,
                                    otp_token_expire = ?
                                    WHERE user_registration_temp_id = ?', [$verification_token, $otp_token_expire, $user_registration_temp_id]);
                    $response = Response::json(['code' => 200, 'message' => 'Verification link has been sent to your email. Please verify your account by verification link.', 'cause' => '', 'data' => json_decode('{}')]);

                } else {
                    $verification_token = $result[0]->otp_token;
                    $response = Response::json(['code' => 200, 'message' => 'Your verification link already sent to your email address. Please check your email.', 'cause' => '', 'data' => json_decode('{}')]);

                }

                DB::commit();

                $active_user = DB::select('select request_json from user_registration_temp where id=?', [$user_registration_temp_id]);
                //Log::info($active_user);

                if (count($active_user) == 1) {
                    $request_json = json_decode($active_user[0]->request_json);
                    $first_name = isset($request_json->first_name) ? $request_json->first_name : null;
                } else {
                    $first_name = '';
                }

                //(new MailchimpController())->subscribeUserByEmail($email_id, 'signup_not_verified');

                $template = 'verify_user';
                $subject = 'PhotoADKing: Verification Link';
                $message_body = [
                    'message' => 'Please verify your email by click on below link',
                    'token' => $verification_token,
                    'user_registration_temp_id' => $request->user_registration_temp_id,
                    'email_id' => $email_id,
                    'first_name' => $first_name,
                    'redirect_url' => Config::get('constant.USER_VERIFICATION_LINK_URL'),
                ];
                $api_name = 'resendVerificationLink';
                $api_description = 'Send mail for OTP verification.';

                //Send email
                $this->dispatch(new SendMailJob($request->user_registration_temp_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));
            } else {
                $response = Response::json(['code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('resendVerificationLink', $e);
            //            Log::error("resendVerificationLink : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'resend verification link.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/verifyUser",
     *        tags={"Users"},
     *        operationId="verifyUser",
     *        summary="verify user by registration link",
     *        produces={"application/json"},
     *
     * 		@SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"user_registration_temp_id","token"},
     *
     *          @SWG\Property(property="user_registration_temp_id",  type="integer", example=1, description=""),
     *          @SWG\Property(property="token",  type="string", example="dfjdfhjdjfjdfbjdfbvhjdbfvjzxcxcxvx", description=""),
     *        ),
     *      ),
     *
     * 		@SWG\Response(
     *            response=200,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":200,"message":"Your account verified successfully. Please login.","cause":"","data":"{}"}),),
     *        ),
     *
     *      @SWG\Response(
     *            response=201,
     *            description="Success",
     *
     *        @SWG\Schema(
     *
     *          @SWG\Property(property="Sample Response",  type="object", example={"code":201,"message":"PhotoADKing is unable to verify user.","cause":"Exception message","data":"{}"}),),
     *        ),
     *    )
     */
    public function verifyUserBackUp(Request $request)
    {
        try {

            $request = json_decode($request->getContent());

            $response = (new VerificationController())->validateRequiredParameter(['user_registration_temp_id', 'token'], $request);
            if ($response != '') {
                return $response;
            }

            $user_registration_temp_id = $request->user_registration_temp_id;
            $token = $request->token;
            $create_time = date('Y-m-d H:i:s');

            $user_id = DB::select('select id from user_registration_temp where uuid=?', [$user_registration_temp_id]);
            if (count($user_id) <= 0) {
                return Response::json(['code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $user_registration_temp_id = $user_id[0]->id;

            if (($response = (new VerificationController())->verifyVerificationLinkToken($user_registration_temp_id, $token)) != '') {
                return $response;
            }

            $active_user = DB::select('select request_json, mailchimp_subscr_id from user_registration_temp where id=?', [$user_registration_temp_id]);

            if (count($active_user) == 1) {
                $request_json = json_decode($active_user[0]->request_json);
                $is_active = 1;
                $email_id = $request_json->email_id;
                $first_name = isset($request_json->first_name) ? $request_json->first_name : null;
                $password = $request_json->password;
                $signup_type = $request_json->signup_type;
                $device_info = isset($request_json->device_info) ? $request_json->device_info : null;

                $response = (new VerificationController())->checkIfEmailExist($email_id);
                if ($response != '') {
                    return $response = Response::json(['code' => 200, 'message' => 'Your account is already verified. Please login', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $uuid = (new ImageController())->generateUUID();
                if ($uuid == '') {
                    return Response::json(['code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                }

                $login_data = [
                    'user_name' => $email_id,
                    'uuid' => $uuid,
                    'password' => Hash::make($password),
                    'email_id' => $email_id,
                    'signup_type' => $signup_type,
                    'profile_setup' => 1,
                    'is_active' => $is_active,
                    'is_verify' => 0,
                    'is_once_logged_in' => 0,
                    'create_time' => $create_time,
                ];
                //Log::info($login_data);

                DB::begintransaction();
                $user_id = DB::table('user_master')->insertGetId($login_data);
                DB::commit();

                $user_role_data = [
                    'role_id' => Config::get('constant.ROLE_ID_FOR_USER'),
                    'user_id' => $user_id,
                ];
                DB::table('role_user')->insert($user_role_data);
                DB::commit();

                DB::insert('insert into user_detail
                                                              (user_id,
                                                              first_name,
                                                              email_id,
                                                              create_time) values(?,?,?,?)', [$user_id, $first_name, $email_id, $create_time]);

                if ($device_info != null or $device_info != '') {
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
                    $device_registered_from = 0; //0=device registered from signup API, 1=device registered from login API
                    $ip_address = isset($request->ip_address) ? $request->ip_address : \Request::ip();

                    (new LoginController())->addNewDeviceToUser(
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
                        null
                    );
                }

                //old templates
                /*$template = 'simple';
                $subject = 'PhotoADKing: Account Activation';
                $message_body = array(
                    'message' => '<p><b>Welcome to PhotoADKing</b><br><br>You have successfully registered on PhotoADKing! Enjoy designing.</p>',
                    'user_name' => $first_name
                );
                $api_name = 'verifyUser';
                $api_description = 'Send mail after verify user.';
                $this->dispatch(new SendMailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));*/

                //new templates
                $template = 'user_account_activation';
                $subject = 'PhotoADKing: Account Activation';
                $message_body = [
                    'user_name' => $first_name,
                ];
                $api_name = 'verifyUser';
                $api_description = 'Send mail after verify user.';
                $this->dispatch(new SendMailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

                $select_user_master = DB::select('select * from user_master where is_verify=? and email_id=?', [1, $email_id]);

                if (count($select_user_master) == 0) {

                    DB::begintransaction();
                    DB::update('UPDATE user_master SET is_verify = 1 WHERE id = ?', [$user_id]);
                    DB::commit();

                    $response = Response::json(['code' => 200, 'message' => 'Your account verified successfully. Please login', 'cause' => '', 'data' => json_decode('{}')]);

                }

                (new MailchimpController())->subscribeUserByEmail($email_id, 'free_user');

                /*$core_ip = $_SERVER['REMOTE_ADDR'];
                $ip = \Request::ip();
                $location = \Location::get($ip);
                Log::info('VerifyUser',['core_ip'=>$core_ip]);
                Log::info('VerifyUser',['ip_adress'=>$ip]);
                Log::info('location',[$location]);*/

            } else {

                return $response = Response::json(['code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            (new ImageController())->logs('verifyUser', $e);
            //            Log::error("verifyUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'verify user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    public function verifyUser(Request $request)
    {
        try {

            $request = json_decode($request->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['user_registration_temp_id', 'token', 'is_mobile', 'device_info'], $request)) != '') {
                Log::error('verifyUser : Required field some request\'s data is missing or empty.', ['response' => $response]);

                return $response;
            }

            $user_registration_temp_id = $request->user_registration_temp_id;
            $is_mobile = $request->is_mobile;
            $new_device_info = $request->device_info;
            $new_device_info->ip_address = request()->ip();
            $token = $request->token;
            $create_time = date('Y-m-d H:i:s');

            /* Select user detail from temp_registration_table using uuid */
            $user_detail = DB::select('SELECT id, request_json, mailchimp_subscr_id FROM user_registration_temp WHERE uuid = ?', [$user_registration_temp_id]);
            if (! $user_detail) {
                return Response::json(['code' => 201, 'message' => 'Invalid registration id.', 'cause' => '', 'data' => json_decode('{}')]);
            }
            $user_registration_temp_id = $user_detail[0]->id;

            /* Check if verification link is expired or not */
            if (($response = (new VerificationController())->verifyVerificationLinkToken($user_registration_temp_id, $token)) != '') {
                return $response;
            }

            $request_json = json_decode($user_detail[0]->request_json);
            $email_id = $request_json->email_id;
            $first_name = $request_json->first_name;
            $password = $request_json->password;
            $signup_type = $request_json->signup_type;
            $device_info = $request_json->device_info;
            $user_keyword = (new UserController())->validateUserKeyword($request_json->user_keyword);

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

            /* Check if email is already exist into database */
            $response = (new VerificationController())->checkIfEmailExist($email_id);
            if ($response != '') {
                return $response = Response::json(['code' => 200, 'message' => 'Your account is already verified. Please login', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /* Generate unique id and register user data into user_master table */
            $uuid = (new ImageController())->generateUUID();
            $login_data = [
                'user_name' => $email_id,
                'uuid' => $uuid,
                'password' => Hash::make($password),
                'email_id' => $email_id,
                'signup_type' => $signup_type,
                'profile_setup' => 1,
                'is_active' => 1,
                'is_verify' => 1,
                'is_once_logged_in' => 0,
                'create_time' => $create_time,
            ];
            DB::begintransaction();
            $user_id = DB::table('user_master')->insertGetId($login_data);

            /* Insert user_id into role_user table */
            $user_role_data = [
                'role_id' => Config::get('constant.ROLE_ID_FOR_USER'),
                'user_id' => $user_id,
            ];
            DB::table('role_user')->insert($user_role_data);

            /* Insert user information into user_detail table */
            DB::insert('INSERT INTO user_detail
                              (user_id,
                              first_name,
                              email_id,
                               attribute1,
                              user_keyword,
                              device_json,
                              create_time) VALUES (?, ?, ?, ?, ?, ?, ?)', [$user_id, $first_name, $email_id, $utm_para, empty($filteredArray) ? null : $filteredArray, json_encode($new_device_info), $create_time]);
            DB::commit();

            /* Dispatch job to send account activation mail to user */
            $template = 'user_account_activation';
            $subject = 'PhotoADKing: Account Activation';
            $message_body = [
                'user_name' => $first_name,
            ];
            $api_name = 'verifyUser';
            $api_description = 'Send mail after verify user.';
            $this->dispatch(new SendMailJob($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description));

            /* This process is for marketing purpose */
            (new MailchimpController())->subscribeUserByEmail($email_id, 'free_user');

            /* If the user verify from mobile then return the success message and end the process otherwise continue with the login process */
            if ($is_mobile) {
                //Log::info('verifyUser : user register from mobile.', ['email' => $email_id]);
                return Response::json(['code' => 200, 'message' => 'Your account verified successfully. Please login', 'cause' => '', 'data' => json_decode('{}')]);
            }

            /*------------------------------user login process------------------------------*/

            /* Verify user and generate token with user credential */
            $credential = ['user_name' => $email_id, 'password' => $password];
            if (! $token = JWTAuth::attempt($credential)) {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

            $user_id = JWTAuth::toUser($token)->id;
            $admin = Config::get('constant.ADMIN_ID');
            $sub_admin = Config::get('constant.SUB_ADMIN_ID');

            /* Check if user role is admin or sub_admin then return with error message */
            if (! in_array("$user_id", [$admin, $sub_admin], true)) {
                $user_profile = (new LoginController())->getUserInfoByUserId($user_id);

                /* Create user session */
                (new LoginController())->createNewSessionV2($user_id, $token, json_encode($device_info));

                $response = Response::json(['code' => 200, 'message' => 'Login Successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => $user_profile]]);
            } else {
                return Response::json(['code' => 201, 'message' => 'The email and password you entered did not match our records. Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
            }

        } catch (Exception $e) {
            if ($e instanceof QueryException) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode == 1062) {
                    Log::error('verifyUser : Duplicate entry occurred.');

                    return Response::json(['code' => 201, 'message' => config('constant.EXCEPTION_ERROR').'verify user, Please try again later', 'cause' => '', 'data' => json_decode('{}')]);
                }
            }
            (new ImageController())->logs('verifyUser', $e);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'verify user.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/updateUserProfile",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="updateUserProfile",
     *        summary="Update user profile",
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
     *          type="string",
     *          description="Give first_name in json object",
     *
     *         @SWG\Schema(
     *
     *              @SWG\Property(property="first_name",type="string", example="Elsa", description=""),
     *              ),
     *     ),
     *
     *     @SWG\Parameter(
     *         name="file",
     *         in="formData",
     *         description="user profile",
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
    public function updateUserProfile(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $request = json_decode($request_body->input('request_data'));

            $first_name = isset($request->first_name) ? $request->first_name : null;
            if ($first_name) {
                $first_name = (new ImageController())->removeEmoji($first_name);
                if (trim($first_name) == '') {
                    return Response::json(['code' => 201, 'message' => 'Please enter valid first name.', 'cause' => '', 'data' => '']);
                }
            }

            DB::beginTransaction();
            if ($first_name != null) {

                DB::update('UPDATE user_detail SET first_name = ? WHERE user_id = ?', [$first_name, $user_id]);

            }

            if ($request_body->hasFile('file')) {
                $file = Input::file('file');
                $file_type = $file->getMimeType();

                if (($response = (new UserVerificationController())->verifyImage($file)) != '') {
                    return $response;
                }

                $image = (new ImageController())->generateNewFileName('profile_img', $file);
                (new ImageController())->saveOriginalImage($image);
                (new ImageController())->saveCompressedImage($image);
                (new ImageController())->saveThumbnailImage($image);

                if (Config::get('constant.STORAGE') === 'S3_BUCKET') {
                    (new ImageController())->saveImageInToS3($image);
                }

                DB::update('UPDATE user_detail SET profile_img = ? WHERE user_id = ?', [$image, $user_id]);
            }

            DB::commit();

            $user_profile = (new LoginController())->getUserInfoByUserId($user_id);
            $response = Response::json(['code' => 200, 'message' => 'Profile updated successfully.', 'cause' => '', 'data' => $user_profile]);

        } catch (Exception $e) {
            (new ImageController())->logs('updateUserProfile', $e);
            //            Log::error("updateUserProfile : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'update user profile.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }

    /**
     * - Users ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/getUserProfile",
     *        tags={"Users"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="getUserProfile",
     *        summary="Update user profile",
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
    public function getUserProfile()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);

            $user = Auth::user();
            $user_id = $user->id;

            $user_detail = (new LoginController())->getUserInfoByUserId($user_id);

            $response = Response::json(['code' => 200, 'message' => 'Profile fetched successfully.', 'cause' => '', 'data' => $user_detail]);

        } catch (Exception $e) {
            (new ImageController())->logs('getUserProfile', $e);
            //            Log::error('getUserProfile  : ', ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'get user profile.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
            DB::rollBack();
        }

        return $response;
    }
}
