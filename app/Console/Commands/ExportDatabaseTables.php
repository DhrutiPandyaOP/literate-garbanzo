<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use App\Jobs\RunCommandAsRoot;
use Aws\S3\S3Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Exception;
use ZipArchive;

class ExportDatabaseTables extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'ExportDatabaseTables';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Export specific tables from database.';

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
      $tables = Config::get('constant.DATABASE_TABLES');
      $db_file_name = $mysql_database . '.sql';
      $sql_file_path = './..' . Config::get('constant.TEMP_DIRECTORY') . $db_file_name;

      if (Config::get('constant.ACTIVATION_LINK_PATH') == 'https://photoadking.com') {

        //execute the export command
        $export_cmd = "mysqldump --single-transaction -u $mysql_user -p'$mysql_password' $mysql_database $tables > $sql_file_path";
        exec($export_cmd, $export_output, $export_result);

        if ($export_result != 0) {
          Log::error('ExportDatabaseTables : unable to run export command.', ['cmd' => $export_cmd, 'output' => $export_output, 'result' => $export_result]);
        } else {

          $aws_bucket = Config::get('constant.TEST_AWS_BUCKET');
          $disk = new S3Client([
            'version' => 'latest',
            'region' => Config::get('constant.TEST_AWS_REGION'),
            'credentials' => [
              'key' => Config::get('constant.TEST_AWS_KEY'),
              'secret' => Config::get('constant.TEST_AWS_SECRET'),
            ],
          ]);

          if (($is_exist = ((new ImageController())->checkFileExist($sql_file_path)) != 0)) {
            $key = $aws_bucket . '/temp/' . $db_file_name;
            $result = $disk->putObject([
              'Bucket' => $aws_bucket,
              'Key' => $key,
              'Body' => fopen($sql_file_path, 'r')
            ]);

            unlink($sql_file_path);
          } else {
            Log::error("ExportDatabaseTables : ", ['message' => 'File not exist.', 'cause' => '', 'data' => $sql_file_path]);
          }
        }

      }

    } catch
    (Exception $e) {
      Log::error("ExportDatabaseTables command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
    }
  }
}
