<?php

namespace App\Http\Controllers;

use Config;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Response;

class MailchimpController extends Controller
{
    //

    public function subscribeUserByEmail($email_id, $tag_name)
    //public function subscribeUserByEmail(Request $request)
    {
        try {

            /*$request = json_decode($request->getContent());
            $email_id = $request->email_id;
            $tag_name = $request->tag_name;*/

            $post_body = [
                'members' => [[
                    'email_address' => $email_id,
                    'status' => 'subscribed']],
                'update_existing' => true,
            ];

            //dd(json_encode($post_body));
            $apiKey = Config::get('constant.MAILCHIMP_API_KEY');
            $url = Config::get('constant.MAILCHIMP_API_URL').Config::get('constant.MAILCHIMP_LIST_ID');

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); // Full path is - https://us19.api.mailchimp.com/3.0/lists/a658037610
            curl_setopt($curl, CURLOPT_POST, true); // Use POST
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_body)); // Setup post body
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERPWD, 'user:'.$apiKey);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);

            // Execute request and read response
            $resultJSON = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($resultJSON) {
                $response = json_decode($resultJSON, true);
                //Log::info('http_status of mailchimp : ',['http_status' => $http_status]);
                if ($http_status == 200) {
                    if ($response['new_members'] != []) {
                        $id = $response['new_members'][0]['id'];
                    } elseif ($response['updated_members'] != []) {
                        $id = $response['updated_members'][0]['id'];
                    } else {
                        $id = '';
                    }

                    if ($id != '') {
                        DB::begintransaction();
                        DB::update('UPDATE user_master SET mailchimp_subscr_id = ? WHERE email_id = ?', [$id, $email_id]);
                        DB::commit();
                        //Log::debug('Success of mailchimp subscribe list : ',['id' => $id]);
                        $this->setTagIntoList($id, $tag_name);
                    } else {
                        Log::error('subscribeUserByEmail : ', ['error_message' => $response['errors'][0]['error'], 'email_id' => $response['errors'][0]['email_address']]);

                    }

                } else {
                    Log::error('subscribeUserByEmail : ', ['status_code' => $http_status, 'response' => $response]);

                }

            } else {

                $error = curl_error($curl).'('.curl_errno($curl).')';
                Log::error('subscribeUserByEmail : ', ['status_code' => $http_status, 'curl_exception' => $error]);
            }

            curl_close($curl);

        } catch (Exception $e) {
            Log::error('subscribeUserByEmail : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            DB::rollBack();
        }
    }

    //public function setTagIntoList(Request $request)
    public function setTagIntoList($id, $tag_name)
    {
        try {

            /*$request = json_decode($request->getContent());
            $id = $request->id;*/

            //remove old tag
            $this->deleteTagFromSubscriber($id);

            $post_body = [
                'tags' => [[
                    'name' => $tag_name,
                    'status' => 'active']],
            ];

            $this->deleteTagFromSubscriber($id);

            $apiKey = Config::get('constant.MAILCHIMP_API_KEY');
            $url = Config::get('constant.MAILCHIMP_API_URL').Config::get('constant.MAILCHIMP_LIST_ID').'/members/'.$id.'/tags';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); // Full path is - https://us19.api.mailchimp.com/3.0/lists/a658037610/members/0575c43fd80c79ed13a5ceb81d210a68/tags
            curl_setopt($curl, CURLOPT_POST, true); // Use POST
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_body)); // Setup post body
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERPWD, 'user:'.$apiKey);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);

            // Execute request and read response
            $resultJSON = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($http_status == 204) {

                //Log::debug('Success of mailchimp tag');

            } else {

                Log::debug('setTagIntoList Error :', ['status_code' => $http_status]);

            }

            curl_close($curl);

        } catch (Exception $e) {
            (new ImageController())->logs('setTagIntoList', $e);
            //            Log::error("setTagIntoList : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }

    //public function deleteTagFromSubscriber(Request $request)
    public function deleteTagFromSubscriber($id)
    {
        try {

            /*$request = json_decode($request->getContent());
            $id = $request->id;*/

            $post_body = [
                'tags' => [
                    [
                        'name' => 'account_deleted',
                        'status' => 'inactive'],
                    [
                        'name' => 'free_user',
                        'status' => 'inactive'],
                    [
                        'name' => 'monthly_pro',
                        'status' => 'inactive'],
                    [
                        'name' => 'monthly_starter',
                        'status' => 'inactive'],
                    [
                        'name' => 'signup_not_verified',
                        'status' => 'inactive'],
                    [
                        'name' => 'subscription_cancelled',
                        'status' => 'inactive'],
                    [
                        'name' => 'yearly_pro',
                        'status' => 'inactive'],
                    [
                        'name' => 'yearly_starter',
                        'status' => 'inactive'],
                ],
            ];

            $apiKey = Config::get('constant.MAILCHIMP_API_KEY');
            $url = Config::get('constant.MAILCHIMP_API_URL').Config::get('constant.MAILCHIMP_LIST_ID').'/members/'.$id.'/tags';

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); // Full path is - https://us19.api.mailchimp.com/3.0/lists/a658037610/members/0575c43fd80c79ed13a5ceb81d210a68/tags
            curl_setopt($curl, CURLOPT_POST, true); // Use POST
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_body)); // Setup post body
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERPWD, 'user:'.$apiKey);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);

            // Execute request and read response
            $resultJSON = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($http_status == 204) {

                //Log::debug('Success of remove mailchimp tag');

            } else {

                Log::debug('deleteTagFromSubscriber Error :', ['status_code' => $http_status]);

            }

            curl_close($curl);

        } catch (Exception $e) {
            (new ImageController())->logs('deleteTagFromSubscriber', $e);
            //            Log::error("deleteTagFromSubscriber : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);

        }
    }
}
