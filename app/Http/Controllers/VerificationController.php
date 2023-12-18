<?php

//////////////////////////////////////////////////////////////////////////////
//                   OptimumBrew Technology Pvt. Ltd.                       //
//                                                                          //
// Title:            PhotoAdKing                                            //
// File:             VerificationController.php                             //
// Since:            20-september-2016                                      //
//                                                                          //
// Author:           Pinal Patel                                            //
// Email:            rushita.optimumbrew@gmail.com                            //
//                                                                          //
//////////////////////////////////////////////////////////////////////////////

namespace App\Http\Controllers;

use App\Http\Requests;
use DateTime;
use FontLib\Font;
use Response;
use DB;
use Exception;
use Log;
use Config;

class VerificationController extends Controller
{
    // validate required and empty field
    public function validateRequiredParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                if (!is_object($request_params->$value)) {
                    if (strlen($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    public function validateRequiredArrayParameter($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (isset($request_params->$value)) {
                /*if (!is_array($request_params->$value)) {
                    $error = true;
                    $error_fields .= ' ' . $value . ',';
                } else {
                    if (count($request_params->$value) == 0) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    }
                }*/
            } else {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';

        return $response;
    }

    public function validateRequiredParameterIsArray($required_fields, $request_params)
    {
        try {
            $error = false;
            $error_fields = '';

            foreach ($required_fields as $key => $value) {
                if (isset($request_params->$value)) {
                    if (!is_array($request_params->$value)) {
                        $error = true;
                        $error_fields .= ' ' . $value . ',';
                    } else {
                        if (count($request_params->$value) == 0) {
                            $error = true;
                            $error_fields .= ' ' . $value . ',';
                        }
                    }
                } else {
                    $error = true;
                    $error_fields .= ' ' . $value . ',';
                }
            }

            if ($error) {
                // Required field(s) are missing or empty
                $error_fields = substr($error_fields, 0, -1);
                $message = 'Required field(s)' . $error_fields . ' is missing or empty.';
                $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
            } else
                $response = '';

        }catch (Exception $e){
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("validateRequiredParameterIsArray : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }

        return $response;
    }

    // validate required field
    public function validateRequiredParam($required_fields, $request_params)
    {
        $error = false;
        $error_fields = '';

        foreach ($required_fields as $key => $value) {
            if (!(isset($request_params->$value))) {
                $error = true;
                $error_fields .= ' ' . $value . ',';
            }
        }

        if ($error) {
            // Required field(s) are missing or empty
            $error_fields = substr($error_fields, 0, -1);
            $message = 'Required field(s)' . $error_fields . ' is missing.';
            $response = Response::json(array('code' => 201, 'message' => $message, 'cause' => '', 'data' => json_decode("{}")));
        } else
            $response = '';
        return $response;
    }

    // verify otp
    public function verifyOTP($registration_id, $otp_token)
    {
        try {
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE user_registration_temp_id = ? AND
                                        otp_token = ?', [$registration_id, $otp_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP is invalid.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'OTP token expired.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyOTP : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // check if user is active
    public function checkIfUserIsActive($user_id)
    {
        try {

            $result = DB::select('SELECT
                                        um.is_active
                                        FROM user_master um
                                        WHERE um.id = ? AND um.is_active = ?', [$user_id, 1]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'You are inactive user. Please contact administrator.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }


        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }
        return $response;
    }

    // check if user is Subscribed
    public function checkIfUserIsSubscribed($user_id)
    {
        try {
            $current_time = date("Y-m-d H:i:s");
            $result = DB::select('SELECT sub.user_id
                                        FROM subscriptions sub,
                                             user_master um
                                        WHERE sub.user_id=um.user_id AND
                                              sub.expiration_time > ? AND
                                              um.user_id = ?', [$current_time, $user_id]);


            if (count($result) == 0) {
                $reActivationURL = (new Utils())->getBaseUrl() . "/join/#/resubscribe/" . $user_id;
                $response = Response::json(array('code' => '402', 'message' => 'Your subscription has been expired.', 'cause' => $reActivationURL, 'data' => json_decode("{}")));
            } else {
                $response = '';
            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => 'Your subscription has been expired.', 'data' => json_decode("{}")));
        }
        return $response;
    }

    // check if subscription_id is exist
    public function checkIsSubscriptionIdExist($subscr_id)
    {
        try {
            $result = DB::select('SELECT id
                                        FROM subscriptions
                                        WHERE paypal_id = ?', [$subscr_id]);
            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Subscription Id already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }

        } catch (Exception $e) {
            Log::error("checkIsSubscriptionIdExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }
        return $response;
    }

    // check if  user is active
    public function checkIfUserExist($email_id)
    {
        try {
            $result = DB::select('SELECT 1 FROM user_master WHERE email_id = ?', [$email_id]);
            $response = (sizeof($result) != 0) ? 1 : 0;

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfUserExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // verify user
    public function verifyUser($email_id, $role_name)
    {
        try {
            $result = DB::select('SELECT r.name
                                  FROM role_user ru, roles r, user_master um
                                  WHERE r.id = ru.role_id AND
                                        um.id = ru.user_id AND
                                        um.email_id = ?', [$email_id]);
            $response = (sizeof($result) > 0 && $result[0]->name == $role_name) ? '' : Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // get user role
    public function getUserRole($user_id)
    {
        try {
            $result = DB::select('SELECT
                                        r.name
                                        FROM role_user ru, user_master um, roles r
                                        WHERE
                                          um.id = ru.user_id AND
                                          ru.role_id = r.id AND
                                          um.id = ?', [$user_id]);

            $response = (count($result) > 0) ? $result[0]->name : '';

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }
        return $response;
    }

    public function checkIfAdvertisementExist($url, $platform)
    {
        try {
            $result = DB::select('SELECT * from advertise_links WHERE url = ? AND platform = ?', [$url, $platform]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Advertisement already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIfAdvertisementExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    public function checkIfPromoCodeExist($promo_code, $package_name)
    {
        try {
            $result = DB::select('SELECT * from promocode_master WHERE promo_code = ?', [$promo_code, $package_name]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Promo code already exists.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIfPromoCodeExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    //validateItemCount
    public function validateItemCount($item_count)
    {
        try {

            if ($item_count < 3 or $item_count > 200) {
                $response = Response::json(array('code' => 201, 'message' => 'Item count must be >= 3 and <= 200.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("validateItemCount : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    //validateItemCount
    public function validateAdvertiseServerId($server_id)
    {
        try {

            $result = DB::select('SELECT * from sub_category_advertise_server_id_master WHERE server_id = ?', [$server_id]);
            if (count($result) >= 1) {
                $response = Response::json(array('code' => 201, 'message' => 'Server id already exists.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("validateAdvertiseServerId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    // check if email is exist
    public function checkIfEmailExist($email_id)
    {
      try {

        $result = DB::select('SELECT signup_type FROM user_master WHERE email_id = ? OR user_name = ?', [$email_id, $email_id]);
        if ($result) {
          /* Check if user signup with facebook */
          if ($result[0]->signup_type == 2) {
            $response = Response::json(array('code' => 201, 'message' => 'You have done the signup using your Facebook account. Please try to login using Facebook.', 'cause' => '', 'data' => json_decode("{}")));
            /* Check if user signup with google */
          } elseif ($result[0]->signup_type == 3) {
            $response = Response::json(array('code' => 201, 'message' => 'You have done the signup using your Google account. Please try to login using Google.', 'cause' => '', 'data' => json_decode("{}")));
          } else {
            $response = Response::json(array('code' => 201, 'message' => 'An account with the email address you entered already exists. Please use the login form instead, or use the "forgot password" option if you don\'t remember your password.', 'cause' => '', 'data' => json_decode("{}")));
          }
        } else {
          $response = '';
        }

      } catch (Exception $e) {
        $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        Log::error("checkIfEmailExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      }
      return $response;
    }

    // verifyOTPForForgotPassword
    public function verifyTokenForResetPassword($email_id, $reset_token)
    {
        try {
            $result = DB::select('SELECT reset_token_expire
                                  FROM user_pwd_reset_token_master
                                  WHERE email_id = ? AND
                                        reset_token = ?', [$email_id, $reset_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Your password has already been reset.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->reset_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'The reset link you clicked has expired. Please request a new one.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyTokenForResetPassword : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // verify otp
    public function verifyVerificationLinkToken($user_registration_temp_id, $otp_token)
    {
        try {
            $result = DB::select('SELECT otp_token_expire
                                  FROM otp_codes
                                  WHERE user_registration_temp_id = ? AND
                                        otp_token = ?', [$user_registration_temp_id, $otp_token]);
            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid verification link.', 'cause' => '', 'data' => json_decode("{}")));
            } elseif (strtotime(date(Config::get('constant.DATE_FORMAT'))) > strtotime($result[0]->otp_token_expire)) {
                $response = Response::json(array('code' => 201, 'message' => 'Verification link has been expired.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("verifyVerificationLinkToken : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // checkIfSubCategoryExist
    public function checkIfSubCategoryExist($sub_category_name, $id)
    {
        try {
            if ($id != 0) {
                $result = DB::select('SELECT *
                                  FROM sub_category_master
                                  WHERE sub_category_name = ? AND id != ?', [$sub_category_name, $id]);
            } else {
                $result = DB::select('SELECT *
                                  FROM sub_category_master
                                  WHERE sub_category_name = ?', [$sub_category_name]);
            }

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Sub category already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfSubCategoryExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // checkIfCatalogExist
    public function checkIfCatalogExist($sub_category_id, $catalog_name, $catalog_id)
    {
        try {
            if ($catalog_id) {
                $result = DB::select('SELECT
                                      ct.id
                                    FROM
                                      catalog_master as ct,
                                      sub_category_catalog as sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id = ct.id AND
                                      ct.name = ? AND
                                      ct.id != ? AND
                                      sct.is_active = 1', [$sub_category_id, trim($catalog_name), $catalog_id]);
            } else {
                $result = DB::select('SELECT
                                      ct.id
                                    FROM
                                      catalog_master as ct,
                                      sub_category_catalog as sct
                                    WHERE
                                      sct.sub_category_id = ? AND
                                      sct.catalog_id = ct.id AND
                                      ct.name = ? AND
                                      sct.is_active = 1', [$sub_category_id, trim($catalog_name)]);
            }

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => "'$catalog_name' already exist in this category.", 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfCatalogExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // checkDisposableEmail
    public function checkDisposableEmail($email_id)
    {
        try {
            $checker = app()->make('email.checker');
            if ($checker->isValid($email_id)) {
                $response = '';
            } else {
                $response = Response::json(array('code' => 201, 'message' => 'Disposable email addresses are not allowed.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkDisposableEmail : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // checkIsFeedbackGiven
    public function checkIsFeedbackGiven($user_id)
    {
        try {
            $result = DB::select('SELECT * FROM feedback_master WHERE user_id = ? AND is_active = 1', [$user_id, 1]);

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Feedback already submitted.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIsFeedbackGiven : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // checkIfCategoryExist
    public function checkIfCategoryExist($category_name)
    {
        try {
            $result = DB::select('SELECT 1 FROM category WHERE name = ?', [$category_name]);

            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Category already exist.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIfCategoryExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    //checkIsObjectImageExist
    public function checkIsObjectImageExist($image_array)
    {
        try {
            $exist_files_array = array();
            //$base_url = (new ImageController())->getBaseUrl();
            //dd($image_array);
            foreach ($image_array as $key) {

                $image = $key->getClientOriginalName();


                $image_path = DB::select('SELECT 1 FROM my_design_3d_image_master WHERE image = ?', [$image]);

                if (count($image_path) > 0) {
                    //Log::info('existed',$image_path);
                    $exist_files_array[] = array('url' => Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image, 'name' => $image);
                }

            }
            if (sizeof($exist_files_array) > 0) {
                $array = array('existing_files' => $exist_files_array);

                $result = json_decode(json_encode($array), true);
                return $response = Response::json(array('code' => 201, 'message' => 'File already exists.', 'cause' => '', 'data' => $result));
            } else {
                return $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsObjectImageExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }


    }

    //checkIsTransparentImageExist
    public function checkIsTransparentImageExist($image_array)
    {
        try {
            $exist_files_array = array();
            foreach ($image_array as $key) {

                $image = $key->getClientOriginalName();

                $image_path = DB::select('SELECT 1 FROM my_design_transparent_image_master WHERE image = ?', [$image]);
                if (count($image_path) > 0) {
                    $exist_files_array[] = array('url' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image, 'name' => $image);
                }

            }
            if (sizeof($exist_files_array) > 0) {
                $array = array('existing_files' => $exist_files_array);

                $result = json_decode(json_encode($array), true);
                return $response = Response::json(array('code' => 201, 'message' => 'File already exists.', 'cause' => '', 'data' => $result));
            } else {
                return $response = '';
            }
        } catch (Exception $e) {
            Log::error("checkIsTransparentImageExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }


    }

    //checkIsStockPhotosExist
    public function checkIsStockPhotosExist($pixabay_image_id)
    {
        try {

            $result = DB::select('SELECT 1 FROM stock_photos_master WHERE pixabay_image_id = ?', [$pixabay_image_id]);

            if (count($result) > 0) {
                return $response = 1;
            } else {
                return $response = 0;
            }
        } catch (Exception $e) {
            Log::error("checkIsStockPhotosExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }


    }

    //getPixabayImageId
    public function getPixabayImageId($image_array)
    {
        try {

            $image = $image_array->getClientOriginalName();

            $file_name = substr(strstr($image, '_id_'), 4); //return string after occur "_id_"
            $extension = substr(strstr($file_name, '.'), 1); //return string after occur "."
            $pixabay_image_id = str_replace("." . $extension, "", $file_name);

            return $pixabay_image_id;

        } catch (Exception $e) {
            Log::error("getPixabayImageId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }


    }

    //checkIsObjectImageExist
    public function checkIsObjectImageExistToEditDesign($image_array, $my_design_id)
    {
        try {
            $exist_files_array = array();
            //$base_url = (new ImageController())->getBaseUrl();
            //dd($image_array);
            foreach ($image_array as $key) {

                $image = $key->getClientOriginalName();


                $update_image_list = DB::select('SELECT 1 FROM my_design_3d_image_master WHERE image = ? AND find_in_set(?,my_design_id)', [$image, $my_design_id]);

                if (count($update_image_list) > 0) {
                    //Log::info($image_path);
                    //$exist_files_array[] = array('url' => Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image, 'name' => $image);
                } else {
                    $image_already_exist = DB::select('SELECT id,my_design_id FROM my_design_3d_image_master WHERE image = ?', [$image]);
                    if (count($image_already_exist) > 0) {
                        //Log::info($image_path);
                        $exist_files_array[] = array('url' => Config::get('constant.3D_OBJECT_IMAGES_DIRECTORY_OF_DIGITAL_OCEAN') . $image, 'name' => $image);
                    }

                }
                if (sizeof($exist_files_array) > 0) {
                 if (in_array($my_design_id, $image_already_exist[0]->my_design_id)) {
                    $array = array('existing_files' => $exist_files_array);
                    $result = json_decode(json_encode($array), true);
                    return $response = Response::json(array('code' => 201, 'message' => 'File already exists.', 'cause' => '', 'data' => $result));
                  } else {
                    $my_design_ids = $image_already_exist[0]->my_design_id . "," . $my_design_id;
                    DB::select('UPDATE FROM my_design_3d_image_master SET my_design_id = ?  WHERE id = ?', [$my_design_ids, $image_already_exist[0]->id]);
                    return $response = 1;
                  }
                } else {
                    return $response = '';
                }
            }
        } catch
        (Exception $e) {
            Log::error("checkIsObjectImageExistToEditDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }


    }

    //checkIsTransparentImageExistToEditDesign
    public function checkIsTransparentImageExistToEditDesign($image_array, $my_design_id)
    {
        try {
            $exist_files_array = array();
            foreach ($image_array as $key) {

                $image = $key->getClientOriginalName();

                $update_image_list = DB::select('SELECT 1 FROM my_design_transparent_image_master WHERE image = ? AND find_in_set(?,my_design_id)', [$image, $my_design_id]);

                if (count($update_image_list) > 0) {
                    //$exist_files_array[] = array('url' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY') . $image, 'name' => $image);
                } else {
                    $image_already_exist = DB::select('SELECT id,my_design_id FROM my_design_transparent_image_master WHERE image = ?', [$image]);

                    if (count($image_already_exist) > 0) {
                        $exist_files_array[] = array('url' => Config::get('constant.MY_DESIGN_IMAGES_DIRECTORY') . $image, 'name' => $image);
                    }

                }
                if (sizeof($exist_files_array) > 0) {
                  if (in_array($my_design_id, explode(',',$image_already_exist[0]->my_design_id))) {
                    $array = array('existing_files' => $exist_files_array);

                    $result = json_decode(json_encode($array), true);
                    return $response = Response::json(array('code' => 201, 'message' => 'File already exists.', 'cause' => '', 'data' => $result));
                } else {
            $my_design_ids = $image_already_exist[0]->my_design_id . "," . $my_design_id;
            DB::select('UPDATE my_design_transparent_image_master SET my_design_id = ?  WHERE id = ?', [$my_design_ids, $image_already_exist[0]->id]);
            return $response = 1;
          }
            } else {
                    return $response = '';
                }
            }
        } catch (Exception $e) {
            Log::error("checkIsTransparentImageExistToEditDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }


    }

    public function differenceBetweenTwoDate($create_time, $update_time)
    {
        $diff = abs(strtotime($create_time) - strtotime($update_time));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        return $days;
    }

    // get interval in minutes from time stamp
    public function getInterval($end_at, $start_at)
    {
        return round(abs(strtotime($end_at) - strtotime($start_at)) / 60, 2);
    }

    // Delete unused stock photos
    public function deleteUnusedStockPhotos($pixabay_image_id)
    {
        try {

            DB::beginTransaction();
            DB::delete('DELETE FROM stock_photos_master WHERE pixabay_image_id = ? AND (my_design_ids = "" OR my_design_ids IS NULL)', [$pixabay_image_id]);
            DB::commit();
            return '';

        } catch (Exception $e) {
            Log::error("deleteUnusedStockPhotos : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    public function addDaysIntoDate($date, $days_to_add)
    {
        try {

            $date = new DateTime($date);
            $date->modify("+$days_to_add day");
            $final_date = $date->format('Y-m-d H:i:s');
            return $final_date;

        } catch (Exception $e) {
            Log::error("addDaysIntoDate : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return $response = '';
        }
    }

    // checkIsBillingInfoAdded
    public function checkIsBillingInfoAdded($user_id)
    {
        try {
            $result = DB::select('SELECT * FROM billing_master WHERE user_id = ? AND is_active = 1', [$user_id, 1]);

            if (count($result) > 0) {
                $response = 1;
            } else {
                $response = 0;
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIsBillingInfoAdded : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // checkIsSubscriptionAlreadyExist
    public function checkIsSubscriptionAlreadyExist($user_id)
    {
        try {
            $result = DB::select('SELECT * FROM subscriptions
                                    WHERE user_id = ? AND
                                      cancellation_date IS NULL
                                    ORDER BY create_time DESC limit 1', [$user_id]);

            if (count($result) > 0) {
                Log::info('checkIsSubscriptionAlreadyExist : You are running on multiple subscriptions, please unsubscribe any one of them from the Paypal using below button, else you will be charged for all active subscriptions.');
                $response = Response::json(array('code' => 419, 'message' => 'You are running on multiple subscriptions, please unsubscribe any one of them from the Paypal using below button, else you will be charged for all active subscriptions.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("checkIsSubscriptionAlreadyExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // validateUserToCreateDesign
    public function validateUserToCreateDesign($user_id,$content_type)
    {
        try {
            $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

            if (count($get_user_role) > 0) {

              if ($content_type == Config::get('constant.IMAGE')) {
                $user_design_count = 'coalesce(my_design_count,0) AS total_my_design_count,coalesce((SELECT  my_design_count FROM my_design_tracking_master WHERE user_id = ? AND
                                                     DATE_FORMAT(create_time, "%Y-%m") = DATE_FORMAT(NOW(), "%Y-%m")),0) AS my_design_count_per_month';
              } else {
                $user_design_count = 'coalesce(my_video_design_count,0) AS total_my_design_count,coalesce((SELECT my_video_design_count FROM my_design_tracking_master WHERE user_id = ? AND
                                                     DATE_FORMAT(create_time, "%Y-%m") = DATE_FORMAT(NOW(), "%Y-%m")),0) AS my_design_count_per_month';
              }

                $role_id = $get_user_role[0]->role_id;
                $user_detail = DB::select('SELECT
                                                  user_id,
                                                  '.$user_design_count.'
                                                FROM
                                                  user_detail
                                                WHERE
                                                  user_id = ?', [$user_id, $user_id]);

                if (count($user_detail) > 0) {
                    $total_my_design_count = $user_detail[0]->total_my_design_count;
                    $my_design_count_per_month = $user_detail[0]->my_design_count_per_month;
                    //Log::info("Count : ",["Total design " =>$total_my_design_count,"my design per month"=>$my_design_count_per_month ]);

                    if ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER')) {

                      if ($content_type == Config::get('constant.IMAGE')) {
                        if ($my_design_count_per_month >= Config::get('constant.MY_DESIGN_COUNT_FOR_MONTHLY_STARTER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create image design limit exceeded for Monthly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }else{
                        if ($my_design_count_per_month >= Config::get('constant.MY_VIDEO_DESIGN_COUNT_FOR_MONTHLY_STARTER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create video design limit exceeded for Monthly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) {
                      if ($content_type == Config::get('constant.IMAGE')) {
                        if ($my_design_count_per_month >= Config::get('constant.MY_DESIGN_COUNT_FOR_YEARLY_STARTER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create image design limit exceeded for Yearly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }else{
                        if ($my_design_count_per_month >= Config::get('constant.MY_VIDEO_DESIGN_COUNT_FOR_YEARLY_STARTER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create video design limit exceeded for Yearly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO')) {

                        //unlimited
                        $response = '';


                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_PRO')) {

                        //unlimited
                        $response = '';

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_LIFETIME_PRO')) {

                        //unlimited
                        $response = '';


                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_PREMIUM_USER')) {
                      if ($content_type == Config::get('constant.IMAGE')) {
                        if ($my_design_count_per_month >= Config::get('constant.MY_DESIGN_COUNT_FOR_PREMIUM_USER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create image design limit exceeded for Premium User.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }else{
                        if ($my_design_count_per_month >= Config::get('constant.MY_VIDEO_DESIGN_COUNT_FOR_PREMIUM_USER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create video design limit exceeded for Premium User.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }


                    } else {
                      if ($content_type == Config::get('constant.IMAGE')) {
                        if ($total_my_design_count >= Config::get('constant.MY_DESIGN_COUNT_FOR_FREE_USER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create image design limit exceeded for free plan.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }else{
                        if ($total_my_design_count >= Config::get('constant.MY_VIDEO_DESIGN_COUNT_FOR_FREE_USER')) {

                          return Response::json(array('code' => 432, 'message' => 'Create video design limit exceeded for free plan.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                          $response = '';
                        }
                      }
                    }

                } else {
                    return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
                }

            } else {
                return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("validateUserToCreateDesign : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

  // getRemain image & size of user
  public function getTotalRemainCountToUploadImageOfUSer($user_id)
  {
    try {
      $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

      if (count($get_user_role) > 0) {
        $role_id = $get_user_role[0]->role_id;

        $user_detail = DB::select('SELECT uploaded_img_count, uploaded_img_total_size FROM user_detail WHERE user_id = ?', [$user_id]);
        if (count($user_detail) > 0) {

          $uploaded_img_count = $user_detail[0]->uploaded_img_count ;
          $uploaded_img_total_size = $user_detail[0]->uploaded_img_total_size ;

          if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
            $remain_upload_image = Config::get('constant.UPLOAD_IMAGE_COUNT_FOR_FREE_USER') - $uploaded_img_count;
            $remain_upload_size = Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_FREE_USER') - $uploaded_img_total_size;
//
          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER')) {
            $remain_upload_image = NULL;
            $remain_upload_size = Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_STARTER') - $uploaded_img_total_size;

          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) {
            $remain_upload_image = NULL;
            $remain_upload_size = Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_STARTER') - $uploaded_img_total_size;

          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO')) {
            $remain_upload_image = NULL;
            $remain_upload_size = Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_PRO') - $uploaded_img_total_size;

          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_PREMIUM_USER')) {
            $remain_upload_image = NULL;
            $remain_upload_size = Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_PREMIUM_USER') - $uploaded_img_total_size;

          } else {
            $remain_upload_image = NULL;
            $remain_upload_size = Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_PRO') - $uploaded_img_total_size;
          }
          return array('remain_upload_image' => $remain_upload_image,'remain_upload_size' => $remain_upload_size);

        } else {
          return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
        }

      } else {
        return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
      }
    } catch (Exception $e) {
      $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
      Log::error("validateUserToUploadImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
    return $response;
  }

  // validateUserToTotalUploadImage
  public function validateUserToUploadMultipleImage($user_id,$uploading_img_count,$uploading_img_total_size)
  {
    try {
      $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

      if (count($get_user_role) > 0) {
        $role_id = $get_user_role[0]->role_id;

        $user_detail = DB::select('SELECT uploaded_img_count, uploaded_img_total_size FROM user_detail WHERE user_id = ?', [$user_id]);
        if (count($user_detail) > 0) {

          $uploaded_img_count = $user_detail[0]->uploaded_img_count + $uploading_img_count;
          $uploaded_img_total_size = $user_detail[0]->uploaded_img_total_size + $uploading_img_total_size;

          if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {

            if ($uploaded_img_count > Config::get('constant.UPLOAD_IMAGE_COUNT_FOR_FREE_USER')) {

              return Response::json(array('code' => 432, 'message' => 'You have exceeded the maximum image uploading limit.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
              if ($uploaded_img_total_size > Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_FREE_USER')) {
                return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Free Plan.', 'cause' => '', 'data' => json_decode("{}")));
              } else {
                $response = '';
              }
            }

          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER')) {


            if ($uploaded_img_total_size > Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_STARTER')) {
              return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Monthly Starter.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
              $response = '';
            }


          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) {

            if ($uploaded_img_total_size > Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_STARTER')) {
              return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Yearly Starter.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
              $response = '';
            }

          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO')) {

            if ($uploaded_img_total_size > Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_PRO')) {
              return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Monthly Pro.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
              $response = '';
            }

          } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_PREMIUM_USER')) {

            if ($uploaded_img_total_size > Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_PREMIUM_USER')) {
              return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Premium User.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
              $response = '';
            }

          } else {

            if ($uploaded_img_total_size > Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_PRO')) {
              return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Yearly Pro.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
              $response = '';
            }

          }

        } else {
          return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
        }

      } else {
        return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
      }
    } catch (Exception $e) {
      $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
      Log::error("validateUserToUploadImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
    return $response;
  }

    // validateUserToUploadImage
    public function validateUserToUploadImage($user_id)
    {
        try {
            $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

            if (count($get_user_role) > 0) {
                $role_id = $get_user_role[0]->role_id;

                $user_detail = DB::select('SELECT uploaded_img_count, uploaded_img_total_size FROM user_detail WHERE user_id = ?', [$user_id]);
                if (count($user_detail) > 0) {

                    $uploaded_img_count = $user_detail[0]->uploaded_img_count;
                    $uploaded_img_total_size = $user_detail[0]->uploaded_img_total_size;


                    if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {

                        if ($uploaded_img_count >= Config::get('constant.UPLOAD_IMAGE_COUNT_FOR_FREE_USER')) {

                            return Response::json(array('code' => 432, 'message' => 'You have exceeded the maximum image uploading limit.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {

                            if ($uploaded_img_total_size >= Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_FREE_USER')) {
                                return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Free Plan.', 'cause' => '', 'data' => json_decode("{}")));
                            } else {
                                $response = '';
                            }
                        }
                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER')) {


                        if ($uploaded_img_total_size >= Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_STARTER')) {
                            return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Monthly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }


                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) {

                        if ($uploaded_img_total_size >= Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_STARTER')) {
                            return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Yearly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO')) {

                        if ($uploaded_img_total_size >= Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_MONTHLY_PRO')) {
                            return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Monthly Pro.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_PREMIUM_USER')) {

                        if ($uploaded_img_total_size >= Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_PREMIUM_USER')) {
                            return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Premium User.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }

                    } else {

                        if ($uploaded_img_total_size >= Config::get('constant.UPLOAD_IMAGE_SIZE_LIMIT_FOR_YEARLY_PRO')) {
                            return Response::json(array('code' => 201, 'message' => 'Upload image size limit is exceeded for Yearly Pro.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }

                    }

                } else {
                    return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
                }

            } else {
                return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("validateUserToUploadImage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // validateUserToUploadFont
    public function validateUserToUploadFont($user_id)
    {
        try {
            $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

            if (count($get_user_role) > 0) {
                $role_id = $get_user_role[0]->role_id;

                $user_detail = DB::select('SELECT uploaded_font_count FROM user_detail WHERE user_id = ?', [$user_id]);
                if (count($user_detail) > 0) {

                    $uploaded_font_count = $user_detail[0]->uploaded_font_count;

                    if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {

                        if ($uploaded_font_count >= Config::get('constant.UPLOAD_FONT_COUNT_FOR_FREE_USER')) {

                            return $response = Response::json(array('code' => 430, 'message' => 'You must upgrade your plan with any paid plan to enable this feature.', 'cause' => '', 'data' => json_decode("{}")));

                        } else {
                            $response = '';
                        }
                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER')) {

                        if ($uploaded_font_count >= Config::get('constant.UPLOAD_FONT_COUNT_FOR_MONTHLY_STARTER')) {

                            return Response::json(array('code' => 432, 'message' => 'Font uploading limit is exceeded for Monthly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }
                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) {

                        if ($uploaded_font_count >= Config::get('constant.UPLOAD_FONT_COUNT_FOR_YEARLY_STARTER')) {

                            return Response::json(array('code' => 432, 'message' => 'Font uploading limit is exceeded for Yearly Starter.', 'cause' => '', 'data' => json_decode("{}")));
                        } else {
                            $response = '';
                        }

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_PRO')) {

                        //unlimited
                        $response = '';

                    } elseif ($role_id == Config::get('constant.ROLE_ID_FOR_PREMIUM_USER')) {

                        //unlimited
                        $response = '';

                    } else {

                        //unlimited
                        $response = '';

                    }

                } else {
                    return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
                }

            } else {
                return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("validateUserToUploadFont : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    // validateUserToGet3DObject
    public function validateUserToGet3DObject($user_id, $sub_category_id)
    {
        try {

            $is_3d_object = DB::select('SELECT id FROM sub_category_master WHERE id = ? AND category_id = ?', [$sub_category_id, 6]);

            if (count($is_3d_object) > 0) {
                $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

                if (count($get_user_role) > 0) {
                    $role_id = $get_user_role[0]->role_id;

                    if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {

                        $response = Response::json(array('code' => 430, 'message' => 'You must upgrade your plan with any paid plan to enable this feature.', 'cause' => '', 'data' => json_decode("{}")));
                    } else {

                        $response = '';
                    }

                } else {
                    $response = Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
                }
            } else {
                $response = '';
            }

        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("validateUserToGet3DObject : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }

    //verifySearchCategory
    public function verifySearchCategory($search_category)
    {
        try {
            $count = 0;
            $array_of_search_text = (explode(",", strtolower($search_category)));
            $result = array();
            $repeated_tags = array();
            foreach ($array_of_search_text as $key) {
                if (!in_array($key, $result) == true) {
                    $result[] = $key;
                } else {
                    $count = $count + 1;
                    $repeated_tags[] = $key;
                }
            }
            ///Log::info('verifySearchCategory search_tags : ',['search_tags' => implode(',',$result)]);

            if ($count > 0) {
                return $response = Response::json(array('code' => 201, 'message' => 'Please remove duplicate entry of "'. implode(',',$repeated_tags) .'" from tag selection.', 'cause' => '', 'data' => ['search_tags' => implode(',',$result)]));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("verifySearchCategory : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            return Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    //Validate string to restrict some special characters
    public function verifySearchText($text)
    {

        /*
         * Here following special characters are restricted
         * @%*()-+\'"<>/
         * also only allow some special characters & alphanumeric values
         * */

        $string_array = str_split($text);
        foreach ($string_array as $key)
        {
            $is_valid = preg_match ('/[[:alnum:] `!#$₹^&_={}[]|:;,.?]+/', $key);
            if($is_valid == 0)
            {
                return $is_valid;
            }
        }

        //return 1 if search text is valid 0 otherwise
        return $is_valid;
    }

    //Check this post is used in user side
    public function checkIsPostSchedulerUsed($post_suggestion_id,$post_schedule_id){

        if($post_suggestion_id){
            $result = DB::select('SELECT 1
                                    FROM post_schedule_master
                                  WHERE
                                    find_in_set(?,post_ids) AND
                                    is_active = 1 ',[$post_suggestion_id]);
        }

        if($post_schedule_id){
            $result = DB::select('SELECT 1
                            FROM post_schedule_master
                            WHERE id = ? AND is_active = 1 AND
                            post_date <= CURRENT_DATE() ',[$post_schedule_id]);
        }

        if(count($result)>0){
            $response = Response::json(array('code' => 201, 'message' => 'Sorry, We can\'t delete or modify this post because of it\'s being used by the users.', 'cause' => '', 'data' => json_decode('{}')));
        }else{
            $response = '';
        }
        return $response;
    }

    // validateRole
    public function validateRole($role_id)
    {
        try {
            $result = DB::select('SELECT * FROM roles
                                    WHERE id = ? AND id != ?', [$role_id, 1]);


            if (count($result) == 0) {
                $response = Response::json(array('code' => 201, 'message' => 'Invalid role id. Please enter valid role id.', 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }
        } catch (Exception $e) {
            $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
            Log::error("validateRole : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
        return $response;
    }


    //checkIsFontExist
    public function checkIsFontExist($file_array)
    {
        try {

            $font_file = (new ImageController())->generateNewFileName('user_uploaded_fonts', $file_array);

            $temp_directory = Config::get('constant.EXTRA_IMAGES_DIRECTORY');

            $file_path = '../..' . $temp_directory . $font_file;
            $destination_path = '../..' . $temp_directory;
            $file_array->move($destination_path, $font_file);

            $font = Font::load($file_path);
            $FontFullName = $font->getFontFullName();
            $font->close();

            $result = DB::select('SELECT
                                      uuf.id AS user_font_id,
                                      uuf.font_name
                                    FROM
                                      user_uploaded_fonts AS uuf
                                    WHERE
                                      (uuf.font_file = ? OR uuf.font_name = ?)', [$font_file, $FontFullName]);

            if (count($result) > 0) {
                (new ImageController())->unlinkFileFromLocalStorage($font_file, $temp_directory);
                $response = array('is_exist' => 1, 'font_name' => $result[0]->font_name, 'file_name' => $font_file);
            } else {
                $response = array('is_exist' => 0, 'font_name' => $FontFullName, 'file_name' => $font_file);
            }

        } catch (Exception $e) {
            Log::error("checkIsFontExist : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check font is exist or not.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //checkIsFontExist
    public function checkIsFontExistForAdmin($file_array)
    {
        try {

            //$file_name = $file_array->getClientOriginalName();
            $file_name = str_replace(" ","",strtolower($file_array->getClientOriginalName()));
            $temp_directory = Config::get('constant.EXTRA_IMAGES_DIRECTORY');

            $file_path = '../..' . $temp_directory . $file_name;
            $destination_path = '../..' . $temp_directory;
            $file_array->move($destination_path, $file_name);

            $font = Font::load($file_path);
            $FontFullName = $font->getFontFullName();
            $font->close();

            $result = DB::select('SELECT
                                      cm.id AS catalog_id,
                                      cm.name
                                    FROM
                                      font_master AS fm,
                                      catalog_master AS cm
                                    WHERE
                                      fm.catalog_id = cm.id AND
                                      (fm.font_file = ? OR fm.font_name = ?)', [$file_name, $FontFullName]);

            if (count($result) > 0) {
                (new ImageController())->unlinkFileFromLocalStorage($file_name, $temp_directory);
                $catalog_name = $result[0]->name;
                $response = Response::json(array('code' => 420, 'message' => "Font already exist in '$catalog_name' category.", 'cause' => '', 'data' => json_decode("{}")));
            } else {
                $response = '';
            }

        } catch (Exception $e) {
            Log::error("checkIsFontExistForAdmin : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'check font is exist or not.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //getFontName
    public function getFontName($file_path, $file_name)
    {
        try {

            $file_path = '../..' . $file_path . $file_name;
            //$file_path = '../..' . Config::get('constant.USER_UPLOAD_FONTS_DIRECTORY') . $file_name;

            $font = Font::load($file_path);
            $FontFullName = $font->getFontFullName(); //used to fetch font sub_family name
            $font->close(); //This is must be compulsory to close font object
            return $FontFullName;


        } catch (Exception $e) {
            Log::error("getFontName : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //validate height-width of sample_image
    public function validateHeightWidthOfSampleImage($image_array, $json_data)
    {
        //open image as a string
        $data = file_get_contents($image_array);

        //getimagesizefromstring function accepts image data as string & return file info
        $file_info = getimagesizefromstring($data);

        //display the image content
        $width = $file_info[0];
        $height = $file_info[1];

        //Log::info('validateHeightWidthOfSampleImage height & width : ',['height_from_img' => $height, 'width_from_img' => $width, 'height_from_json' => $json_data->height, 'width_from_json' => $json_data->width]);

        if ($json_data->height == $height && $json_data->width == $width) {
            $response = '';
        } else {
            return $response = Response::json(array('code' => 201, 'message' => 'Height & width of the sample image doesn\'t match with height & width given in json.', 'cause' => '', 'data' => json_decode("{}")));
        }

        return $response;
    }

    // Validate Fonts
    public function validateFonts($json_data)
    {
        $text_json = $json_data->text_json;
        $exist_count = 0;
        $mismatch_fonts = array();
        $incorrect_fonts = array();

        foreach ($text_json as $key) {
            $ios_font_name = $key->fontName;
            $android_font_name = $key->fontPath;

            $is_exist = DB::select('SELECT id FROM font_master WHERE BINARY ios_font_name = ? AND BINARY android_font_name = ?', [$ios_font_name, $android_font_name]);

            if (count($is_exist) == 0) {
                Log::info('validateFonts font not exist : ', ['query_result' => $is_exist, 'ios_font_name' => $ios_font_name, 'android_font_name' => $android_font_name]);

                $is_ios_font_name_exist = DB::select('SELECT id, ios_font_name, android_font_name FROM font_master WHERE ios_font_name = ?', [$ios_font_name]);
                $is_android_font_name_exist = DB::select('SELECT id, ios_font_name, android_font_name FROM font_master WHERE android_font_name = ?', [$android_font_name]);

                $is_correct_name = 0;
                $is_correct_path = 0;

                if (count($is_ios_font_name_exist) > 0) {
                    $is_correct_name = (strcmp($ios_font_name, $is_ios_font_name_exist[0]->ios_font_name) != 0) ? 0 : 1;
                }

                if (count($is_android_font_name_exist) > 0) {
                    $is_correct_path = (strcmp($android_font_name, $is_android_font_name_exist[0]->android_font_name) != 0) ? 0 : 1;

                }


                if (count($is_android_font_name_exist) > 0 && count($is_ios_font_name_exist) > 0 && $is_correct_name == 1 && $is_correct_path == 1) {


                    $mismatch_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => $is_ios_font_name_exist[0]->android_font_name,
                        'correct_font_name' => $is_android_font_name_exist[0]->ios_font_name
                    );

                } elseif (count($is_android_font_name_exist) == 0 && count($is_ios_font_name_exist) == 0) {
                    $incorrect_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => 'Font not available',
                        'correct_font_name' => 'Font not available',
                        'is_correct_path' => 0,
                        'is_correct_name' => 0
                    );
                } elseif (count($is_android_font_name_exist) > 0) {

                    $incorrect_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => $is_android_font_name_exist[0]->android_font_name,
                        'correct_font_name' => $is_android_font_name_exist[0]->ios_font_name,
                        'is_correct_path' => (strcmp($android_font_name, $is_android_font_name_exist[0]->android_font_name) != 0) ? 0 : 1,
                        'is_correct_name' => (strcmp($ios_font_name, $is_android_font_name_exist[0]->ios_font_name) != 0) ? 0 : 1
                    );
                } elseif (count($is_ios_font_name_exist) > 0) {
                    $incorrect_fonts[] = array(
                        'font_name' => $ios_font_name,
                        'font_path' => $android_font_name,
                        'correct_font_path' => $is_ios_font_name_exist[0]->android_font_name,
                        'correct_font_name' => $is_ios_font_name_exist[0]->ios_font_name,
                        'is_correct_path' => (strcmp($android_font_name, $is_ios_font_name_exist[0]->android_font_name) != 0) ? 0 : 1,
                        'is_correct_name' => (strcmp($ios_font_name, $is_ios_font_name_exist[0]->ios_font_name) != 0) ? 0 : 1
                    );
                }

                $exist_count = $exist_count + 1;
            }

        }

        if ($exist_count > 0) {
            $response = Response::json(array('code' => 435, 'message' => 'Fonts used by json does not exist in the server.', 'cause' => '', 'data' => ['mismatch_fonts' => $mismatch_fonts, 'incorrect_fonts' => $incorrect_fonts]));
        } else {
            $response = '';
        }

        return $response;
    }

    public function validateIntrosFonts($json_data)
    {
      $text_json = $json_data->text_json;
      foreach ($text_json as $key) {
        $android_font_name = $key->font_file;

        $is_exist = DB::select('SELECT id FROM font_master WHERE BINARY android_font_name = ?', [$android_font_name]);

        if (count($is_exist) == 0) {
          $incorrect_fonts[] = array(
            'font_name' => $android_font_name,
            'font_path' => $android_font_name,
            'correct_font_path' => 'Font not available',
            'correct_font_name' => 'Font not available',
            'is_correct_path' => 0,
            'is_correct_name' => 0
          );
          Log::info('validateIntrosFonts font not exist(For intros) : ', ['query_result' => $is_exist, 'android_font_name' => $android_font_name]);
          $response = Response::json(array('code' => 435, 'message' => 'Fonts used by json does not exist in the server.', 'cause' => '', 'data' =>  ['incorrect_fonts' => $incorrect_fonts]));
        } else {
          $response = '';
        }
        return $response;
      }
    }

    //Check this static page is exist or not
    public function checkIsPathAvailable($id, $sub_category_path, $catalog_path, $sub_category_id, $catalog_id){
     try{

      $catalog_path = ($catalog_path != NULL OR $catalog_path != "") ? " sp.catalog_path = \"$catalog_path \"" : "sp.catalog_path IS NULL ";
      $catalog_id = ($catalog_id != NULL OR $catalog_id != "") ? " sp.catalog_id = $catalog_id " : " sp.catalog_id IS NULL ";

      if($id){
        $result = DB::select('SELECT 1
                                      FROM static_page_master AS sp LEFT JOIN static_page_sub_category_master AS spsb
                                      ON spsb.id = sp.static_page_sub_category_id
                                      WHERE sp.id != ? AND ((spsb.sub_category_path = ? AND '.$catalog_path.' ) OR (spsb.sub_category_id = ? AND '.$catalog_id.' ))', [
          $id, $sub_category_path,  $sub_category_id
        ]);
      }else{
        $result = DB::select('SELECT 1
                                      FROM static_page_master AS sp LEFT JOIN static_page_sub_category_master AS spsb
                                      ON spsb.id = sp.static_page_sub_category_id
                                      WHERE (spsb.sub_category_path = ? AND '.$catalog_path.' ) OR (spsb.sub_category_id = ? AND '.$catalog_id.' )', [
          $sub_category_path, $sub_category_id
        ]);
      }

      $response = (count($result) < 1) ? '' : Response::json(array('code' => 201, 'message' => 'This static page already exist.', 'cause' => '', 'data' => json_decode("{}")));

    }catch (Exception $e){
       (new ImageController())->logs("checkIsPathAvailable",$e);
//      Log::error("checkIsPathAvailable : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      $response = Response::json(array('code' => 201, 'message' => 'This category or url already exist.', 'cause' => '', 'data' => json_decode("{}")));
    }
    return $response;
  }

    //get update json with remove static fontPath from json
    public function getJsonWithUpdatedFontPath($json_data)
    {
        try {
            $canvas_json = $json_data->canvasJSON;
            $objects = $canvas_json->objects;
            $AWS_BUCKET_LINK_PATH_PHOTOADKING = Config::get('constant.AWS_BUCKET_LINK_PATH_PHOTOADKING') . '/';

            foreach ($objects as $key) {

                if (isset($key->font_path)) {
//                  Log::info('getJsonWithUpdatedFontPath : ',['font_path' => $key,'bucket_path' => $AWS_BUCKET_LINK_PATH_PHOTOADKING]);
                  $key->font_path = str_replace($AWS_BUCKET_LINK_PATH_PHOTOADKING, '', $key->font_path);

                }
            }

            $json_data->canvasJSON->objects = $objects;
            $response = $json_data;

        } catch (Exception $e) {
            Log::error("getJsonWithUpdatedFontPath : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'get json with updated fontPath.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }

        return $response;
    }

    // validate User To Create Folder
    public function validateUserToCreateFolder($user_id)
    {
      try {
        $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

        if (count($get_user_role) > 0) {
          $role_id = $get_user_role[0]->role_id;
          if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {
            return Response::json(array('code' => 432, 'message' => 'Free user can\'t create folder. Try to upgrade your plan.', 'cause' => '', 'data' => json_decode("{}")));

          }elseif ($role_id == Config::get('constant.ROLE_ID_FOR_MONTHLY_STARTER') || $role_id == Config::get('constant.ROLE_ID_FOR_YEARLY_STARTER')) {

              $new_rules_date = Config::get('constant.DATE_OF_NEW_RULES');
              $get_subscr_time = DB::select('SELECT 1 FROM subscriptions WHERE create_time >= ? AND user_id = ? ORDER BY update_time DESC LIMIT 1', [$new_rules_date, $user_id]);
              if($get_subscr_time) {
                  return Response::json(array('code' => 432, 'message' => 'Users with starter plans can\'t create folder. Try to upgrade your plan.', 'cause' => '', 'data' => json_decode("{}")));
              }
          }
        } else {
          return Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
        }
        $response = '';
      } catch (Exception $e) {
      $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
      Log::error("validateUserToCreateFolder : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
      return $response;
    }

    // check user registration type
    public function checkUserRegistrationType($email_id){
      try{
        $user = DB::select('SELECT signup_type FROM user_master WHERE email_id LIKE ?',[$email_id]);
        $user_type = $user[0]->signup_type;
        if($user_type == 1){
          $response = 1;
        }else{
          $response = 0;
        }
      }catch (Exception $e) {
        $response = Response::json(array('code' => 201, 'message' => $e->getMessage(), 'cause' => '', 'data' => json_decode("{}")));
        Log::error("checkUserRegistrationType : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      }
      return $response;

    }

    //check user type
    public function checkIsUserPro($user_id)
  {

    $get_user_role = DB::select('SELECT role_id FROM role_user WHERE user_id = ?', [$user_id]);

    if (count($get_user_role) > 0) {
      $role_id = $get_user_role[0]->role_id;

      if ($role_id == Config::get('constant.ROLE_ID_FOR_FREE_USER')) {

        $response = Response::json(array('code' => 430, 'message' => 'You must upgrade your plan with any paid plan to enable this feature.', 'cause' => '', 'data' => json_decode("{}")));
      } else {
        $response = '';
      }

    } else {
      $response = Response::json(array('code' => 201, 'message' => 'Unauthorized user.', 'cause' => '', 'data' => json_decode("{}")));
    }

    return $response;
  }

    //verify billing information
    public function verifyBillingInfo($user_id){
      $billing_details = DB::select('SELECT
                                                  id AS billing_id,
                                                  user_id,
                                                  COALESCE (full_name, "") AS full_name,
                                                  COALESCE (address, "") AS address,
                                                  COALESCE (country, "") AS country,
                                                  COALESCE (state, "") AS state,
                                                  COALESCE (city, "") AS city,
                                                  COALESCE (zip_code, "") AS zip_code,
                                                  COALESCE (attribute1, "") AS country_code,
                                                  update_time
                                                FROM
                                                  billing_master
                                                WHERE
                                                  user_id = ? AND
                                                  is_active = ?
                                                ORDER BY update_time DESC', [$user_id, 1]);

      if(count($billing_details) <= 0){
        $response =  Response::json(array('code' => 434, 'message' => 'Your billing information is missing. Please fill it to continue...', 'cause' => '', 'data' => ['billing_info'=> json_decode("{}")]));
      }elseif((empty($billing_details[0]->full_name)) || (empty($billing_details[0]->address)) || (empty($billing_details[0]->country)) || (empty($billing_details[0]->state)) || (empty($billing_details[0]->city)) || (empty($billing_details[0]->zip_code))){
        $response =  Response::json(array('code' => 434, 'message' => 'Your billing information is missing. Please fill it to continue...', 'cause' => '', 'data' => ['billing_info'=> $billing_details[0]]));
      }else{
        if((empty($billing_details[0]->country_code))){
          $country_code = (new UserVerificationController())->getCountryCode($billing_details[0]->country);
          Log::info('verifyBillingInfo : payment gateway ',['country_code' => $country_code]);
          if(isset($country_code) && strlen($country_code) == 2){

            DB::beginTransaction();
            DB::update('UPDATE billing_master SET
                              attribute1 = ?
                              WHERE
                              user_id = ?', [$country_code,$user_id]);
            DB::commit();

          }else{
            Log::error('verifyBillingInfo : we are unable to get country code,plz check on P1 :',['user_id'=>$user_id,'country_code'=>$country_code]);
          }
        }
        $response = '';
      }
      return $response;
    }

    //get billing information
    public function getBillingInfoByUser($user_id)
    {
      $billing_details = DB::select('SELECT
                                                  id AS billing_id,
                                                  user_id,
                                                  COALESCE (full_name, "") AS full_name,
                                                  COALESCE (address, "") AS address,
                                                  COALESCE (country, "") AS country,
                                                  COALESCE (state, "") AS state,
                                                  COALESCE (city, "") AS city,
                                                  COALESCE (zip_code, "") AS zip_code,
                                                  COALESCE (attribute1, "") AS country_code,
                                                  update_time
                                                FROM
                                                  billing_master
                                                WHERE
                                                  user_id = ? AND
                                                  is_active = ?
                                                ORDER BY update_time DESC', [$user_id, 1]);

      return $billing_details[0];
    }

    //get Plan For India by given plan
    public function getPlanForIndia($actual_plan_id,$user_id)
  {
    $billing_details = DB::select('SELECT COALESCE (country, "") AS country
                                                FROM
                                                  billing_master
                                                WHERE
                                                  user_id = ? AND
                                                  is_active = ?
                                                ORDER BY update_time DESC', [$user_id, 1]);

    $country = $billing_details[0]->country;
    /*Now we create 8 plan to resolve issue of currency related*/
    /*check country and also check with supported plans*/
    if($country == 'India'){
      switch ($actual_plan_id) {
        case Config::get('constant.MONTHLY_STARTER'):
          $plan_id = Config::get('constant.INDIAN_MONTHLY_STARTER');
          break;
        case Config::get('constant.YEARLY_STARTER'):
          $plan_id = Config::get('constant.INDIAN_YEARLY_STARTER');
          break;
        case Config::get('constant.MONTHLY_PRO'):
          $plan_id = Config::get('constant.INDIAN_MONTHLY_PRO');
          break;
        case Config::get('constant.YEARLY_PRO'):
          $plan_id = Config::get('constant.INDIAN_YEARLY_PRO');
          break;
        default:
          // Unexpected event type
          $plan_id = $actual_plan_id;

      }
      return $plan_id;
    }
    return $actual_plan_id;
  }

    //Verify the user is indian or not
    public function VerifyIsIndianUser($user_id)
  {
    $billing_details = DB::select('SELECT COALESCE (country, "") AS country
                                                FROM
                                                  billing_master
                                                WHERE
                                                  user_id = ? AND
                                                  is_active = ?
                                                ORDER BY update_time DESC', [$user_id, 1]);
    if(count($billing_details) > 0) {
      $country = $billing_details[0]->country;
      if ($country == 'India') {
        return "INR";
      }
    }
    return "USD";
  }

    //get Plan For India by given plan
    public function getPlanIdFromPlanName($plan_interval,$plan_title)
    {
        if($plan_interval == 'Monthly' && $plan_title == 'Starter'){
            $plan_id = Config::get('constant.MONTHLY_STARTER');
        }elseif ($plan_interval == 'Yearly' && $plan_title == 'Starter'){
            $plan_id = Config::get('constant.YEARLY_STARTER');
        }elseif ($plan_interval == 'Monthly' && $plan_title == 'Pro'){
            $plan_id = Config::get('constant.MONTHLY_PRO');
        }elseif ($plan_interval == 'Yearly' && $plan_title == 'Pro'){
            $plan_id = Config::get('constant.YEARLY_PRO');
        }elseif ($plan_interval == 'LifeTime' && $plan_title == 'Pro'){
            $plan_id = Config::get('constant.LIFETIME_PRO');
        }else{
            // We need to handle plans when we change plans. So then it is necessary to contact the admin
            $plan_id = 0;
        }
        return $plan_id;
    }

    //To use update pricing by product discount
    public function UpdatePricingByProductId($product_id,$discount_percentage)
    {
        try {
            $result = DB::select('SELECT
                                      id AS pricing_id,
                                      actual_amount
                                    FROM
                                      subscription_pricing_details
                                    WHERE
                                      product_id = ?',[$product_id]);

            if (count($result) > 0) {
                foreach ($result AS $pricing){
                    $pricing_id = $pricing->pricing_id;
                    $actual_amount = $pricing->actual_amount;

                    $discount_amount = $actual_amount * $discount_percentage / 100;
                    $calculated_amount = $actual_amount - $discount_amount;
                    $payable_amount = round($calculated_amount,2);
                    DB::beginTransaction();
                    DB::update('UPDATE subscription_pricing_details
                                  SET payable_amount = ?
                                  WHERE id = ?',
                        [$payable_amount,$pricing_id]);
                    DB::commit();
                }
                return 1;
            } else {
                return 0;
            }
        } catch (Exception $e) {
            Log::error("UpdatePricingByProductId : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    //To use verify product contains 4 plan
    public function isAbleToApply($product_id)
    {
        try {
            $result = DB::select('SELECT
                                      count(id) AS pricing_count
                                    FROM
                                      subscription_pricing_details
                                    WHERE
                                      product_id = ?',[$product_id]);
                if($result[0]->pricing_count < 4) {
                    $response = Response::json(array('code' => 201, 'message' => 'Please manage four pricing first to enable the product.', 'cause' => '', 'data' => json_decode("{}")));
                }elseif($result[0]->pricing_count > 4){
                    $response = Response::json(array('code' => 201, 'message' => 'Please manage four pricing first to enable the product.', 'cause' => '', 'data' => json_decode("{}")));
                }else{
                    $response = '';
                }


        } catch (Exception $e) {
            Log::error("isAbleToApply : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }

    //To use verify product contains 4 plan
    public function checkIfPricingExist($plan_title,$plan_interval,$pricing_id,$product_id)
    {
        try {

            if($pricing_id == '') {
                $result = DB::select('SELECT
                                          id
                                        FROM
                                          subscription_pricing_details
                                        WHERE
                                          (plan_title = ? AND plan_interval = ?) AND product_id = ?', [$plan_title, $plan_interval,$product_id]);
            }else{
                $result = DB::select('SELECT
                                          id
                                        FROM
                                          subscription_pricing_details
                                        WHERE
                                          id != ?
                                          AND plan_title = ?
                                          AND plan_interval = ?
                                          AND product_id = ?', [$pricing_id ,$plan_title, $plan_interval,$product_id]);
            }
            if (count($result) > 0) {
                $response = Response::json(array('code' => 201, 'message' => 'The plan already exists with equal intervals.', 'cause' => '', 'data' => json_decode("{}")));
            }else{
                $response = '';
            }
        } catch (Exception $e) {
            Log::error("isAbleToApply : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            $response = Response::json(array('code' => 201, 'message' => Config::get('constant.EXCEPTION_ERROR') . 'update product.', 'cause' => $e->getMessage(), 'data' => json_decode("{}")));
        }
        return $response;
    }


    public function validateFaqs($faqs)
    {
      foreach ($faqs as $key) {
        if($key->question =="" || $key->answer == ""){
          return Response::json(array('code' => 201, 'message' => 'Required field question or answer missing or empty', 'cause' => '', 'data' => json_decode("{}")));
        }
      }
      return "";
    }

    public function validateGuideStep($guide_steps)
    {
      foreach ($guide_steps as $key) {
        if($key->heading =="" || $key->description == ""){
          return Response::json(array('code' => 201, 'message' => 'Required field heading or description missing or empty', 'cause' => '', 'data' => json_decode("{}")));
        }

        if(strlen($key->heading) > 250 ){
          return Response::json(array('code' => 201, 'message' => 'Guide step heading must be less then or equal 250 character.', 'cause' => '', 'data' => json_decode("{}")));
        }
      }
      return "";
    }

}
