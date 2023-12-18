<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteDesignJobDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-design-template-job-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
      // Delete the user data from the table
      DB::delete('DELETE FROM design_template_jobs WHERE create_time < DATE_SUB(NOW(), INTERVAL 7 DAY)');
      Log::info('DeleteDesignJobDataCommand.php : all data that are older then 7 days from design_template_job has been deleted');
    }
}
