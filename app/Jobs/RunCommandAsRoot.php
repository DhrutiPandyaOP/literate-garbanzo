<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Exception;

class RunCommandAsRoot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $cmd;
    public $api_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($cmd, $api_name)
    {
        $this->cmd = $cmd;
        $this->api_name = $api_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            exec($this->cmd, $output, $result);

            if ($result != 0) {
                Log::error('RunCommandAsRoot : unable to run command.', ['api_name' => $this->api_name, 'cmd' => $this->cmd, 'output' => $output, 'result' => $result]);
            }

        } catch (Exception $e) {
            Log::error('RunCommandAsRoot : ', ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    public function failed()
    {
        Log::error('RunCommandAsRoot.php failed()', ['failed_job_id' => 1]);
        $template = 'simple';
        $subject = 'Command failed';
        $message_body = array(
            'message' => 'PhotoADKing is unable to run command by admin <br> api_name = ' . $this->api_name,
            'user_name' => 'Admin'
        );

        $data = array('template' => $template, 'subject' => $subject, 'message_body' => $message_body);
        Mail::send($data['template'], $data, function ($message) use ($data) {
            $message->to(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
        });
    }
}
