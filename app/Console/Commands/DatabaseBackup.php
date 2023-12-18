<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use App\Jobs\RunCommandAsRoot;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DatabaseBackup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Take Backup of database';

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
            $mysql_databse = Config::get('database.connections.mysql.database');
            $db_file_name = $mysql_databse . '.sql';
            $file_path = './..' . Config::get('constant.TEMP_DIRECTORY') . $db_file_name;

            $cmd = "mysqldump -u $mysql_user -p'$mysql_password' $mysql_databse > $file_path 2>&1";

            RunCommandAsRoot::dispatch($cmd, "DatabaseBackup Scheduler");

        } catch (Exception $e) {
            Log::error("DatabaseBackup command handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
