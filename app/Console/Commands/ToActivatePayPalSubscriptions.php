<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Illuminate\Console\Command;
use Log;
use App\Jobs\ToActivatePayPalSubscriptionJob;
use Exception;


class ToActivatePayPalSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ToActivatePayPalSubscriptions:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use to activate PayPal subscriptions';

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
            dispatch(new ToActivatePayPalSubscriptionJob());
        } catch (Exception $e) {
           (new ImageController())->logs("ToActivatePayPalSubscriptions",$e);
//            Log::error("ToActivatePayPalSubscriptions.php() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
