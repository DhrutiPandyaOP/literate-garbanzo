<?php

namespace App\Console\Commands;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MailchimpController;
use App\Http\Controllers\UserController;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Illuminate\Console\Command;

use App\Http\Controllers\ImageController;
use Response;
use Config;
use DB;
use Log;
use Mail;
use Exception;

class AccountDeletionMailSendToUser extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'AccountDeletionMailSendToUser';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Delete deactivated user data after 45 days, if user does not login even after sending warning mail.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    try {
      $destination_array = array();

      //select user which are not logged in from last 45 days even after sent warning mail.
      $users = DB::select('SELECT
                                um.id,
                                um.uuid,
                                ud.first_name,
                                IF(um.signup_type = 1,"Email",IF(um.signup_type = 2,"Facebook","Google")) AS signup_type,
                                um.email_id,
                                COALESCE(DATEDIFF(DATE(NOW()),(SELECT DATE(update_time) FROM user_session WHERE user_id = um.id ORDER BY update_time DESC LIMIT 1)),DATEDIFF(DATE(NOW()),(SELECT DATE(update_time) FROM user_master WHERE id = um.id ORDER BY update_time DESC LIMIT 1))) AS inactivity_days
                            FROM
                                user_master AS um
                                LEFT JOIN role_user AS ru ON um.id=ru.user_id
                                LEFT JOIN user_detail AS ud ON um.id=ud.user_id
                            WHERE
                                ru.role_id = ' . Config::get('constant.ROLE_ID_FOR_FREE_USER') . ' AND
                                um.is_active = 1 AND
                                um.attribute1 = 1
                            ORDER BY um.update_time ASC');


      if (count($users) > 0) {

        $recipient = array('Destination' => [
          'ToAddresses' => ['janviborad.optimumbrew@gmail.com'],
        ],
          'ReplacementTemplateData' => "{ \"name\" : \"janvi\", \"email_id\" : \"janviborad.optimumbrew@gmail.com\", \"signup_type\" : \"Google\", \"uuid\" : \"abcd1234\"}",);
        array_push($destination_array, $recipient);

        foreach ($users as $i => $user) {
          array_push($user_id_lists, $user->id);

          $recipient = array('Destination' => [
            'ToAddresses' => [$user->email_id],
          ],
            'ReplacementTemplateData' => "{ \"name\" : \"$user->first_name\", \"email_id\" : \"$user->email_id\", \"signup_type\" : \"$user->signup_type\", \"uuid\" : \"$user->uuid\"}",);
          array_push($destination_array, $recipient);

          $create_time = date('Y-m-d H:i:s');
          $record_of_user_master = DB::select('SELECT * FROM user_master WHERE id = ?', [$user->id]);
          $record_of_user_detail = DB::select('SELECT * FROM user_detail WHERE user_id = ?', [$user->id]);
          $record_of_my_design_tracking = DB::select('SELECT * FROM my_design_tracking_master WHERE user_id = ? ORDER BY create_time DESC', [$user->id]);
          $record_of_subscriptions = DB::select('SELECT * FROM subscriptions WHERE user_id = ? ORDER BY update_time DESC', [$user->id]);
          $record_of_payment_status = DB::select('SELECT * FROM payment_status_master WHERE user_id = ? ORDER BY update_time DESC', [$user->id]);
          $record_of_stripe_subscription = DB::select('SELECT * FROM stripe_subscription_master WHERE user_id = ? ORDER BY update_time DESC', [$user->id]);

          $uuid = (new ImageController())->generateUUID();

          if ($uuid == "") {
            Log::error("DeleteDeactivatedUser : Something went wrong.Please try again.", ["user_id" => $user->id]);
            //return Response::json(array('code' => 201, 'message' => 'Something went wrong.Please try again.', 'cause' => '', 'data' => json_decode("{}")));
          } else {
            DB::beginTransaction();
            DB::insert('INSERT INTO deleted_user_bkp_master (user_id, uuid, record_of_user_master, record_of_user_detail, record_of_my_design_tracking, record_of_subscriptions, record_of_stripe_subscription, record_of_payment_status, is_deleted, is_active, create_time)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$user->id, $uuid, json_encode($record_of_user_master), json_encode($record_of_user_detail), json_encode($record_of_my_design_tracking), json_encode($record_of_subscriptions), json_encode($record_of_stripe_subscription), json_encode($record_of_payment_status), 1, 1, $create_time]);

            (new UserController())->deleteProfile($user->id);
            (new UserController())->deleteMyDesigns($user->id);
            $user_detail = (new LoginController())->getUserInfoByUserId($user->id);

            DB::delete('DELETE FROM user_master WHERE id = ?', [$user->id]);
            DB::update('UPDATE subscriptions SET first_name = ? WHERE user_id = ?', [$record_of_user_detail[0]->first_name, $user->id]);
            DB::commit();

            (new MailchimpController())->setTagIntoList($user_detail->mailchimp_subscr_id, 'account_deleted');
          }
        }

        $recipient = array('Destination' => [
          'ToAddresses' => ['moxesh.optimumbrew@gmail.com'],
        ],
          'ReplacementTemplateData' => "{ \"name\" : \"moxesh\", \"email_id\" : \"moxesh.optimumbrew@gmail.com\", \"signup_type\" : \"Emaill\", \"uuid\" : \"a1b2c3d4\"}",);
        array_push($destination_array, $recipient);

        $client = SesClient::factory(array(
          'version'=> 'latest',
          'region' => 'us-east-1',
          'credentials' => array(
            'key' => Config::get('constant.SES_KEY'),
            'secret'  => Config::get('constant.SES_SECRET'),
          )
        ));

        //send mail to users to inform their account is deleted.
        $destinations = array_chunk($destination_array,25);
        foreach ($destinations AS $i => $recipient_array) {
          try {
            $result = $client->sendBulkTemplatedEmail([
              'DefaultTemplateData' => "{ \"name\":\"Friends\",\"email_id\":\"email@gmail.com\",\"signup_type\":\"Email\",\"uuid\":\"abcd1234\"}",
              'Destinations' => $recipient_array,
              'Source' => 'PhotoAdKing <no-reply@photoadking.com>',
              'Template' => 'delete_deactive_user',
            ]);
            sleep(1);
            Log::info("WarningMailSendToUser SES : ",['result' => $result, 'recipient' => $recipient_array]);

          } catch (AwsException $e) {
            Log::error("WarningMailSendToUser SES Error : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString(), 'recipient' => $recipient_array]);
          }
        }

      }

    } catch (Exception $e) {
      Log::error("DeleteDeactivatedUser : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
      DB::rollBack();
    }
  }
}
