<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use Config;
use DB;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Log;
use Mail;

class DbBackupJob extends Job implements ShouldQueue
{
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle()
    {
        try {
            //EX : photoadking.com_photoadking_testing_14-02-2020-12-26-06.sql
            $db_file_name = Config::get('constant.SERVER_NAME').'_'.env('DB_DATABASE_PHOTOADKING').'_'.date('d-m-Y-H-i-s').'.sql';

            $MYSQL_USER = Config::get('database.connections.mysql.username');
            $MYSQL_PASSWORD = Config::get('database.connections.mysql.password');
            $MYSQL_DATABASE = Config::get('database.connections.mysql.database');

            //      $MYSQL_USER = env ('DB_USERNAME');
            //      $MYSQL_PASSWORD = env ('DB_PASSWORD');
            //      $MYSQL_DATABASE = env ('DB_DATABASE');
            $FILE_PATH = '../DB_updates/'.$db_file_name;

            /*Mysqldump is a part of the mysql relational database package that allows you to "dump" a database,
            or a collection of databases, for backup or transferral to another SQL server.*/

            $cmd = "mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE >$FILE_PATH 2>&1";
            Log::info('command : ', [$cmd]);
            if (Config::get('constant.APP_ENV') != 'local') {
                $return_val = (! shell_exec($cmd));
                Log::info('Live : shell_exec');
            } else {
                $return_val = (! exec($cmd, $output, $result));
                Log::info('Local : shell_exec');
            }

            Log::info('DbBackupJob : return_val : ', ['cmd' => $return_val]);
            //exec($cmd,$output,$result);
            if (isset($result)) {
                switch ($result) {
                    case 0:
                        Log::info('Import file<b>'.$db_file_name.'</b> successfully imported to db_backup_dir');
                        break;
                    case 1:
                        Log::info('There was an error during import. Please make sure the import file is saved in the same folder as this script and check your values');
                        break;
                }
            }

            $db_backup_directory = Config::get('constant.DATABASE_BACKUP_DIRECTORY');
            $db_sourceFile = './..'.$db_backup_directory.$db_file_name;
            $aws_bucket = Config::get('constant.AWS_BUCKET');
            $disk = Storage::disk('s3');

            if (($is_exist = ((new ImageController())->checkFileExist($db_sourceFile)) != 0)) {
                //store database backup in temp folder[temporary]
                $db_targetFile = "$aws_bucket/temp/".$db_file_name;
                $disk->put($db_targetFile, file_get_contents($db_sourceFile), 'public');
                Log::info('save database file into s3 : ', ['bucket_name' => $aws_bucket, 'target' => $db_targetFile, 'source' => $db_sourceFile]);

                //If db_backup_file is successfully stored in s3, remove it from local
                if ($disk->exists($db_targetFile)) {
                    (new ImageController())->unlinkFileFromLocalStorage($db_file_name, $db_backup_directory);
                }

            } else {
                Log::info('file not found');
                $template = 'simple';
                $subject = 'PhotoADKing: Database backup';
                $message_body = [
                    'message' => '<p><b>PhotoADKing is unable to get today\'s database backup,<br>Please verify following details</b><br><br><b><u>Command</u></b> : '.$cmd.'<br><b><u>Output</u></b> : '.json_encode($output).'<br><b><u>Result</u></b> : '.$result.'</p>',
                    'user_name' => 'Admin',
                ];
                $api_name = 'DbBackupJob';
                $api_description = 'Get database backup and storeinto s3-bucket.';
                dispatch(new SendMailJob('1', Config::get('constant.SUB_ADMIN_EMAIL_ID'), $subject, $message_body, $template, $api_name, $api_description));

            }

        } catch (Exception $e) {
            Log::error('DbBackupJob.php handle catch() : ', ['preview_id' => 1, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
            $this->result_status = 0;
        }
    }

    public function failed()
    {
        Log::error('DbBackupJob.php failed()', ['failed_job_id' => 1]);
        $user_id = 1;
        $api_name = 'DbBackupJob';
        $api_description = 'Get database backup and store into s3-bucket.';
        $job_name = 'SendMailJob';

        // get failed job max id
        $failed_job_id_result = DB::select('SELECT max(id) as max_id FROM failed_jobs');
        if (count($failed_job_id_result) > 0) {

            $failed_job_id = $failed_job_id_result[0]->max_id;
            if ($failed_job_id == null) {
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
            $subject = 'Email failed';
            $message_body = [
                'message' => 'Failed Job Id = '.$failed_job_id.'<br>'.'User Id = '.$user_id.'<br>'.'API Name = '.$api_name.'<br>'.'API Description = '.$api_description,
                'user_name' => 'Admin',
            ];
            //$message_body = 'Failed Job Id = ' . $failed_job_id . '<br>' . 'User Id = ' . $user_id . '<br>' . 'API Name = ' . $api_name . '<br>' . 'API Description = ' . $api_description;
            $data = ['template' => $template, 'email' => $email_id, 'subject' => $subject, 'message_body' => $message_body];
            Mail::send($data['template'], $data, function ($message) use ($data) {
                $message->to($data['email'])->subject($data['subject']);
                $message->bcc(Config::get('constant.SUB_ADMIN_EMAIL_ID'))->subject($data['subject']);
            });
        }

        // log failed job
        Log::error('DbBackupJob.php failed()', ['failed_job_id' => $failed_job_id, 'user_id' => $user_id, 'api_name' => $api_name, 'api_description' => $api_description]);
    }
}
