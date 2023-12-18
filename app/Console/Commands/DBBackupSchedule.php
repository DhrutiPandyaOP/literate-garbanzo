<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use App\Jobs\DbBackupJob;
use Exception;
use Illuminate\Console\Command;
use Log;

class DBBackupSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DBBackup:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Today\'s database backup and store into s3';

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
            dispatch(new DbBackupJob());
        } catch (Exception $e) {
            (new ImageController())->logs('DBBackupSchedule', $e);
            //      Log::error("DBBackupSchedule : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
