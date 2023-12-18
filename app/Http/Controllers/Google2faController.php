<?php

namespace App\Http\Controllers;

use Auth;
use Config;
use DB;
use Exception;
use Illuminate\Http\Request;
use Image;
use JWTAuth;
use Log;
use Response;

/**
 * Class Google2faController
 */
class Google2faController extends Controller
{
    /**
     * - 2fa ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/enable2faByAdmin",
     *        tags={"2fa"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="enable2faByAdmin",
     *        summary="enable2faByAdmin",
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
     *            description="success",
     *        ),
     *      @SWG\Response(
     *            response=201,
     *            description="error",
     *          ),
     *      )
     */
    /**
     * @api {post} enable2faByAdmin enable2faByAdmin
     *
     * @apiName enable2faByAdmin
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
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "2FA has been enabled successfully.",
     * "cause": "",
     * "data": {
     * "google2fa_url": "https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth%3A%2F%2Ftotp%2FOB%2520ADS%3Aadmin%2540gmail.com%3Fsecret%3D3WJMFHPL2XBLWNT3%26issuer%3DOB%2520ADS",
     * "google2fa_secret": "JMFHPL2XBLWNT3"
     * }
     * }
     */
    public function enable2faByAdmin()
    {
        try {
            $token = JWTAuth::getToken();
            $user_detail = JWTAuth::toUser($token);

            $user_id = $user_detail->id;
            $email_id = $user_detail->email_id;
            $google2fa = app('pragmarx.google2fa');
            $google2fa_secret = $google2fa->generateSecretKey();
            $sub_admin = Config::get('constant.SUB_ADMIN_ID');

            // Generate the QR image. This is the image the user will scan with their app
            $google2fa->setAllowInsecureCallToGoogleApis(true);
            $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                Config::get('constant.APP_HOST_NAME'),
                $email_id,
                $google2fa_secret
            );
            DB::beginTransaction();

            DB::update('UPDATE user_master
                                SET google2fa_secret = ?, google2fa_enable = 1
                                WHERE id IN (?,?)', [$google2fa_secret, $user_id, $sub_admin]);

            DB::delete('DELETE FROM user_session WHERE user_id IN(?,?) AND token != ?', [$user_id, $sub_admin, $token]);
            DB::commit();

            $result = [
                'google2fa_url' => $google2fa_url,
                'google2fa_secret' => $google2fa_secret,
            ];

            $response = Response::json(['code' => 200, 'message' => '2FA has been enabled successfully.', 'cause' => '', 'data' => $result]);

        } catch (Exception $e) {
            (new ImageController())->logs('enable2faByAdmin', $e);
            //Log::error("enable2faByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'enable 2fa by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - 2fa ------------------------------------------------------
     *
     * @SWG\Post(
     *        path="/verify2faOTP",
     *        tags={"2fa"},
     *        security={
     *                  {"Bearer": {}},
     *                 },
     *        operationId="verify2faOTP",
     *        summary="verify2faOTP",
     *        consumes={"multipart/form-data" },
     *        produces={"application/json"},
     *
     *     @SWG\Parameter(
     *        in="body",
     *        name="request_body",
     *
     *   	  @SWG\Schema(
     *          required={"verify_code","user_id","google2fa_secret"},
     *
     *          @SWG\Property(property="verify_code",  type="integer", example="557537", description=""),
     *          @SWG\Property(property="user_id",  type="integer", example="1", description=""),
     *          @SWG\Property(property="google2fa_secret",  type="string", example="sdhfkdhjfkh", description=""),
     *        ),
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
     * @api {post} verify2faOTP verify2faOTP
     *
     * @apiName verify2faOTP
     *
     * @apiGroup Admin
     *
     * @apiVersion 1.0.0
     *
     * @apiSuccessExample Request-Header:
     * {
     * }
     * @apiSuccessExample Request-Body:
     * {
     * "verify_code": "557537", //compulsory
     * "user_id": "557537", //compulsory
     * "google2fa_secret": "557537" //compulsory
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "OTP verified successfully.",
     * "cause": "",
     * "data": {
     * "user_detail": {
     * "id": 1,
     * "user_name": "admin",
     * "email_id": "admin@gmail.com",
     * "google2fa_enable": 1,
     * "google2fa_secret": "CY3VRNFBMJBA75EA",
     * "social_uid": null,
     * "signup_type": null,
     * "profile_setup": 0,
     * "is_active": 1,
     * "create_time": "2017-08-02 12:08:30",
     * "update_time": "2018-10-20 06:11:38",
     * "attribute1": null,
     * "attribute2": null,
     * "attribute3": null,
     * "attribute4": null
     * }
     * }
     * }
     */
    public function verify2faOTP(Request $request_body)
    {
        try {
            /*   $token = JWTAuth::getToken();
               JWTAuth::toUser($token);
               $user = Auth::user();
               */
            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['verify_code', 'user_id', 'google2fa_secret'], $request)) != '') {
                return $response;
            }
            $secret = $request->verify_code;
            $google2fa_secret = $request->google2fa_secret;
            $user_id = $request->user_id;

            $user_detail = DB::select('SELECT * FROM user_master WHERE id = ? AND google2fa_secret = ?', [$user_id, $google2fa_secret]);
            if (! $user_detail) {
                $response = Response::json(['code' => 201, 'message' => 'Invalid user.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $google2fa = app('pragmarx.google2fa');
                $valid = $google2fa->verifyKey($google2fa_secret, $secret);
                if ($valid) {
                    $user_token = DB::select('SELECT token FROM user_session WHERE user_id = ? AND is_active = 1 ORDER BY id DESC limit 1', [$user_id]);
                    if (! $user_token) {
                        $response = Response::json(['code' => 201, 'message' => 'Invalid verification, You have to login first.', 'cause' => '', 'data' => json_decode('{}')]);
                    } else {
                        $token = $user_token[0]->token;
                        if (! isset($_COOKIE[$user_detail[0]->uuid])) {
                            setcookie($user_detail[0]->uuid, $user_detail[0]->password, time() + Config::get('constant.EXPIRATION_TIME_OF_2FA_COOKIE'), '/');
                        }
                        $response = Response::json(['code' => 200, 'message' => 'OTP verified successfully.', 'cause' => '', 'data' => ['token' => $token, 'user_detail' => JWTAuth::toUser($token)]]);
                    }
                } else {
                    $response = Response::json(['code' => 201, 'message' => 'Invalid verification code, Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                }

            }

        } catch (Exception $e) {
            (new ImageController())->logs('verify2faOTP', $e);
            //          Log::error("verify2faOTP : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'verify the code, please try again.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }

    /**
     * - 2fa ------------------------------------------------------
     *
     * @SWG\Post(
     * path="/disable2faByAdmin",
     * tags={"2fa"},
     * security={
     * {"Bearer": {}},
     * },
     * operationId="disable2faByAdmin",
     * summary="disable2faByAdmin",
     * consumes={"multipart/form-data" },
     * produces={"application/json"},
     *
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
     *
     * @SWG\Schema(
     * required={"verify_code","google2fa_secret"},
     *
     * @SWG\Property(property="verify_code", type="integer", example=123456, description=""),
     * @SWG\Property(property="google2fa_secret", type="string", example="ABCDEFGH", description=""),
     * ),
     *
     * ),
     *
     * @SWG\Response(
     * response=200,
     * description="success",
     * ),
     * @SWG\Response(
     * response=201,
     * description="error",
     * ),
     * )
     */
    /**
     * @api {post} disable2faByAdmin disable2faByAdmin
     *
     * @apiName disable2faByAdmin
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
     * "verify_code": 123456,
     * "google2fa_secret":"ABCDEF"
     *
     * }
     * @apiSuccessExample Success-Response:
     * {
     * "code": 200,
     * "message": "2FA has been disabled successfully",
     * "cause": "",
     * "data": {
     * "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4LjAuMTEzL3Bob3RvYWRraW5nX3Rlc3RpbmcvYXBpL3B1YmxpYy9hcGkvZG9Mb2dpbkZvckFkbWluIiwiaWF0IjoxNTQ3MzQ5NDY2LCJleHAiOjE1NDc5NTQyNjYsIm5iZiI6MTU0NzM0OTQ2NiwianRpIjoieDA5WUNoWUtudHlwYklWdiJ9.SifYqWURQBhpTG3jocKV1ng-zLx2KSeiCebwUKbl-E0",
     * "user_detail": {
     * "id": 1,
     * "user_name": "admin@gmail.com",
     * "email_id": "admin@gmail.com",
     * "google2fa_enable": 0,
     * "google2fa_secret": "7A7RMQ33CHLQQU5E",
     * "social_uid": null,
     * "signup_type": null,
     * "profile_setup": 1,
     * "mailchimp_subscr_id": null,
     * "is_active": 1,
     * "is_verify": 1,
     * "create_time": "2018-09-21 06:37:46",
     * "update_time": "2019-01-13 07:40:51",
     * "attribute1": null,
     * "attribute2": null,
     * "attribute3": null,
     * "attribute4": null
     * }
     * }
     * }
     */
    public function disable2faByAdmin(Request $request_body)
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::toUser($token);
            $user = Auth::user();
            $user_id = $user->id;
            $sub_admin = Config::get('constant.SUB_ADMIN_ID');

            $request = json_decode($request_body->getContent());
            if (($response = (new VerificationController())->validateRequiredParameter(['verify_code', 'google2fa_secret'], $request)) != '') {
                return $response;
            }

            $secret = $request->verify_code;
            $google2fa_secret = $request->google2fa_secret;

            $user_detail = DB::select('SELECT * FROM user_master WHERE id = ? AND google2fa_secret = ?', [$user_id, $google2fa_secret]);
            if (! $user_detail) {
                $response = Response::json(['code' => 201, 'message' => 'Invalid user.', 'cause' => '', 'data' => json_decode('{}')]);
            } else {

                $google2fa = app('pragmarx.google2fa');
                $valid = $google2fa->verifyKey($google2fa_secret, $secret);
                if ($valid) {

                    $requestHeaders = apache_request_headers();
                    $jwt_token = str_ireplace('Bearer ', '', $requestHeaders['Authorization']);

                    DB::beginTransaction();
                    DB::update('UPDATE user_master SET google2fa_enable = 0 WHERE id IN(?,?)', [$user_id, $sub_admin]);
                    DB::commit();

                    $response = Response::json(['code' => 200, 'message' => '2FA has been disabled successfully', 'cause' => '', 'data' => ['token' => $jwt_token, 'user_detail' => JWTAuth::toUser($token)]]);

                } else {
                    $response = Response::json(['code' => 201, 'message' => 'Invalid verification code, Please try again.', 'cause' => '', 'data' => json_decode('{}')]);
                }

            }

        } catch (Exception $e) {
            (new ImageController())->logs('disable2faByAdmin', $e);
            //          Log::error("disable2faByAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(['code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR').'disable 2fa by admin.', 'cause' => $e->getMessage(), 'data' => json_decode('{}')]);
        }

        return $response;
    }
}
