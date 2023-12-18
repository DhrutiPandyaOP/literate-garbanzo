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

class EmailJob extends Job implements ShouldQueue
{

    use InteractsWithQueue, SerializesModels;


    protected $user_id;

    protected $email_id;

    protected $subject;

    protected $message_body;

    protected $template;

    protected $api_name;

    protected $api_description;


    public function __construct($user_id, $email_id, $subject, $message_body, $template, $api_name, $api_description)
    {
        $this->user_id = $user_id;
        $this->email_id = $email_id;
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
        $subject = $this->subject;
        $message_body = $this->message_body;
        $api_name = $this->api_name;
        $api_description = $this->api_description;
        $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body, 'api_name' => $api_name);

        if(Config::get('constant.IS_MAIL_DEBUG_PROCESS_ENABLE')){

            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', Config::get('constant.MAIL_X_SES_CONFIGURATION_SET_HEADER'));
                $message->to($data['email'])->subject($data['subject']);
                $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
            });
        }else{

            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                if ($data['api_name'] == 'orderFailedEvent'){
                  $message->bcc(Config::get('constant.CRM_EMAIL_ID'))->subject($data['subject']);
                }
            });
        }
    }


    public function failed()
    {
        Log::error('EmailJob.php failed()', ['failed_job_id' => 1]);
        $user_id = $this->user_id;
        $api_name = $this->api_name;
        $api_description = $this->api_description;
        $job_name = 'EmailJob';

        // get failed job max id
        $failed_job_id_result = DB::select('SELECT max(id) as max_id FROM failed_jobs');
        if (count($failed_job_id_result) > 0) {

            $failed_job_id = $failed_job_id_result[0]->max_id;
            if ($failed_job_id == NULL) {
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
            $email_id = Config::get('constant.ADMIN_EMAIL_ID');
            $subject = 'Email failed';
            $message_body = array(
                'message' => 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description,
                'user_name' => 'Admin'
            );
            //$message_body = 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description;
            $data = array('template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body);
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                $message->bcc(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });
        }

        // log failed job
        Log::error('EmailJob.php failed()', ['failed_job_id' => $failed_job_id, 'user_id' => $user_id, 'api_name' => $api_name, 'api_description' => $api_description]);
    }
}
