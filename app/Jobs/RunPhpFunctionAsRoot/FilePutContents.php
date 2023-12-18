<?php

namespace App\Jobs\RunPhpFunctionAsRoot;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FilePutContents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file_path;

    public $file_contents;

    public $api_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file_path, $file_contents, $api_name)
    {
        $this->file_path = $file_path;
        $this->file_contents = $file_contents;
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

            $result = file_put_contents($this->file_path, $this->file_contents);

            if ($result == 0) {
                Log::error('FilePutContents : Job unable to run function.', ['api_name' => $this->api_name, 'result' => $result]);
            }

        } catch (Exception $e) {
            Log::error('FilePutContents : ', ['Exception' => $e->getMessage(), '\nTraceAsString' => $e->getTraceAsString()]);
        }
    }

    public function failed()
    {
        Log::error('FilePutContents.php failed()');
        $template = 'simple';
        $subject = 'Email failed';
        $message_body = [
            'message' => 'PhotoADKing is unable to run FilePutContents job by admin',
            'user_name' => 'Admin',
        ];

        $data = ['template' => $template, 'subject' => $subject, 'message_body' => $message_body];
        Mail::send($data['template'], $data, function ($message) use ($data) {
            $message->to(Config::get('constant.ADMIN_EMAIL_ID'))->subject($data['subject']);
            $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
        });
    }
}
