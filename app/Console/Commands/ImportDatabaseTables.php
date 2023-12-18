<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportDatabaseTables extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ImportDatabaseTables';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Import specific tables in database.';

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
      $mysql_user = Config::get('database.connections.mysql.username');
      $mysql_password = Config::get('database.connections.mysql.password');
      $mysql_database = Config::get('database.connections.mysql.database');
      $db_file_name = $mysql_database . '.sql';
      $sql_file_path = './..' . Config::get('constant.TEMP_DIRECTORY') . $db_file_name;

      if (Config::get('constant.ACTIVATION_LINK_PATH') == 'https://test.photoadking.com') {
        $aws_bucket = Config::get('constant.AWS_BUCKET');
        $disk = Storage::disk('s3');
        $expiry = "+5 minutes";
        $client = $disk->getDriver()->getAdapter()->getClient();
        $key = $aws_bucket . '/temp/' . $db_file_name;

        $command = $client->getCommand('GetObject', [
          'Bucket' => $aws_bucket,
          'Key' => $key
        ]);

        $request = $client->createPresignedRequest($command, $expiry);
        $sql_pre_signed_url = (string)$request->getUri();

        $file_content = file_get_contents($sql_pre_signed_url);
        file_put_contents($sql_file_path, $file_content);
        if (($is_exist = ((new ImageController())->checkFileExist($sql_file_path)) != 0)) {
          //import the sql file into database
          $flag = '--init-command="SET SESSION FOREIGN_KEY_CHECKS=0;"';
          $import_cmd = "mysql $flag -u $mysql_user -p'$mysql_password' -f -D $mysql_database < $sql_file_path";
          exec($import_cmd, $import_output, $import_result);
          if ($import_result != 0) {
            Log::error('ImportDatabaseTables : unable to run import command.', ['cmd' => $import_cmd, 'output' => $import_output, 'result' => $import_result]);
          }
          unlink($sql_file_path);
        } else {
          Log::error("ImportDatabaseTables : ", ['message' => 'File not exist.', 'cause' => '', 'data' => $sql_file_path]);
        }

      }

    } catch (Exception $e) {
      Log::error("ImportDatabaseTables command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }
}
