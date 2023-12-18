<?php

namespace App\Jobs\RunPhpFunctionAsRoot;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Exception;

class Mkdir implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $folder_path;
    public $permission;
    public $recursive;
    public $api_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($folder_path, $permission, $recursive, $api_name)
    {
        $this->folder_path = $folder_path;
        $this->permission = $permission;
        $this->recursive = $recursive;
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

            $result = mkdir($this->folder_path, $this->permission, $this->recursive);

            if ($result == 0) {
                Log::error('Mkdir : Job unable to run function.', ['api_name' => $this->api_name, 'result' => $result]);
            }

        } catch (Exception $e) {
            Log::error('Mkdir : ', ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    public function failed()
    {
        Log::error('Mkdir.php failed()');
        $template = 'simple';
        $subject = 'Email failed';
        $message_body = array(
            'message' => 'PhotoADKing is unable to run Mkdir job by admin',
            'user_name' => 'Admin'
        );

        $data = array('template' => $template, 'subject' => $subject, 'message_body' => $message_body);
        Mail::send($data['template'], $data, function ($message) use ($data) {
            $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
            $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
        });
    }
}
