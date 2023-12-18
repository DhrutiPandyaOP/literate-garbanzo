<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Job;
use Mail;
use Log;
use DB;
use Config;

class FeedbackEmailJob extends Job implements ShouldQueue
{

  use InteractsWithQueue, SerializesModels;


  protected $user_id;

  protected $email_id;

  protected $admin_email_id;

  protected $super_admin_email_id;

  protected $subject;

  protected $message_body;

  protected $template;

  protected $api_name;

  protected $api_description;


  public function __construct($user_id, $email_id, $admin_email_id, $super_admin_email_id, $subject, $message_body, $template, $api_name, $api_description)
  {
    $this->user_id = $user_id;
    $this->email_id = $email_id;
    $this->admin_email_id = $admin_email_id;
    $this->super_admin_email_id = $super_admin_email_id;
    $this->subject = $subject;
    $this->message_body = $message_body;
    $this->template = $template;
    $this->api_name = $api_name;
    $this->api_description = $api_description;
  }
  public function handle()
  {
    $user_id = $this->user_id;
    $template = $this->template;
    $email_id = $this->email_id;
    $admin_email_id = $this->admin_email_id;
    $super_admin_email_id = $this->super_admin_email_id;
    $subject = $this->subject;
    $message_body = $this->message_body;
    $api_name = $this->api_name;
    $api_description = $this->api_description;
    $data = array('template'=>$template, 'email' => $email_id, 'admin_email_id' => $admin_email_id, 'super_admin_email_id' => $super_admin_email_id, 'subject' =>$subject, 'message_body' => $message_body );

    Mail::send($data['template'], $data, function($message) use ($data) {
      $message->to(Config::get('constant.PHOTOADKING_FEEDBACK_SENT_TO_ADDRESS'))->subject($data['subject']);
//            $message->to($data['email'])->subject($data['subject']);
//            $message->cc($data['email'])->subject($data['subject']);
      $message->bcc($data['admin_email_id'])->subject($data['subject']);
      if ($this->super_admin_email_id){
        $message->bcc($data['super_admin_email_id'])->subject($data['subject']);
      }
      $message->replyTo($data['email'],$data['message_body']['send_by']);

    });


  }


  public function failed()
  {
    Log::error('FeedbackEmailJob.php failed()',['failed_job_id'=>1]);
    $user_id = $this->user_id;
    $api_name = $this->api_name;
    $api_description = $this->api_description;
    $job_name = 'FeedbackEmailJob';

    // get failed job max id
    $failed_job_id_result = DB::select('SELECT max(id) as max_id FROM failed_jobs');
    if (count($failed_job_id_result) > 0) {

      $failed_job_id = $failed_job_id_result[0]->max_id;
      if($failed_job_id == NULL)
      {
        $failed_job_id = 1;
      }

      // add failed job detail
      DB::beginTransaction();
      DB::insert('INSERT INTO failed_jobs_detail
                        (failed_job_id, user_id, api_name, api_description, job_name)
                        VALUES (?,?,?,?,?)',
        [$failed_job_id, $user_id, $api_name, $api_description, $job_name]);
      DB::commit();

      // send email to admin
      $template = 'simple';
      $email_id = Config::get('constant.SUB_ADMIN_EMAIL_ID');
      $subject = 'Feedback Email failed';
      $message_body = array(
        'message' => 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description,
        'user_name' => 'Admin'
      );
      //$message_body = 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description;
      $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
      Mail::send($data['template'], $data, function ($message) use ($data) {
        $message->to($data['email'])->subject($data['subject']);
        $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
      });
    }

    // log failed job
    Log::error('FeedbackEmailJob.php failed()',['failed_job_id'=>$failed_job_id,'user_id'=>$user_id,'api_name'=>$api_name,'api_description'=> $api_description]);
  }
}
