<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Config;
use Exception;
use Illuminate\Console\Command;
use Mail;
use ZipArchive;

class SendDeactiveUserReportFileToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendDeactiveUserReportFileToAdmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send total de-active user report file(csv) to admin monthly.';

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
            $file_name = 'user_record_csv.csv';
            $zip_name = 'user_record_zip.zip';
            $dir = Config::get('constant.TEMP_DIRECTORY');
            $original_csv_path = './..'.$dir.$file_name;
            $original_zip_path = './..'.$dir.$zip_name;

            //make zip file from csv file in temp folder
            $zip = new ZipArchive();
            if ($zip->open($original_zip_path, ZipArchive::CREATE) === true) {
                foreach (glob($original_csv_path) as $key => $value) {
                    $relative_name_in_zip_file = basename($value);
                    $zip->addFile($value, $relative_name_in_zip_file);
                }
                $zip->close();
            }

            $subject = 'PhotoADKing: MonthlyReport: WarningMailSendToUser';
            $template = 'total_deactive_user_report';
            $data = ['template' => $template, 'subject' => $subject, 'original_zip_path' => $original_zip_path];

            //send report to super admin attach with zip file
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
                $message->attach($data['original_zip_path'], ['as' => 'monthly_report.zip', 'mime' => 'application/pdf']);
            });

            //remove zip & csv file in temp folder
            if (($is_exist = ((new ImageController())->checkFileExist($original_csv_path)) != 0)) {
                unlink($original_csv_path);
            }
            if (($is_exist = ((new ImageController())->checkFileExist($original_zip_path)) != 0)) {
                unlink($original_zip_path);
            }
        } catch (Exception $e) {
            Log::error('SendDeactiveUserReportFileToAdmin : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
            //remove zip & csv file in temp folder
            if (($is_exist = ((new ImageController())->checkFileExist($original_csv_path)) != 0)) {
                unlink($original_csv_path);
            }
            if (($is_exist = ((new ImageController())->checkFileExist($original_zip_path)) != 0)) {
                unlink($original_zip_path);
            }
        }
    }
}
