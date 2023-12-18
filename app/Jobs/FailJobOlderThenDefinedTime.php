<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\ImageController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class FailJobOlderThenDefinedTime implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct()
  {

  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      // Define the fail reason for these jobs
      $fail_reason = "Queue is older than " . Config::get('constant.QUEUE_AGE_LIMIT') . " minutes";
      // Begin a database transaction
      DB::beginTransaction();

      // Execute an SQL update to mark jobs as failed
      $sql = DB::update(
        'UPDATE design_template_jobs
             SET status = ?, is_active = ?, fail_reason = ?, attribute1 = ? ,user_end_time = ?
             WHERE (status = ? AND TIMESTAMPDIFF(MINUTE, create_time, NOW()) > ?)',
        [2, 0, $fail_reason, 1001 , date('Y-m-d H:i:s'), 0, Config::get('constant.QUEUE_AGE_LIMIT')]
      );

      // Commit the transaction
      DB::commit();

    } catch (Exception $e) {
      // If an exception occurs during the process
      (new ImageController())->logs("FailJobOlderThenDefinedTime.php handle catch()", $e);

      // Define the fail reason for exception-raising jobs
      $fail_reason = "Raised an exception in fail job";

      // Begin a database transaction
      DB::beginTransaction();

      // Attempt to update the job status and attributes
      DB::update(
        'UPDATE design_template_jobs
             SET status = ?, is_active = ?, fail_reason = ?, attribute1 = ?
             WHERE (status = ? AND TIMESTAMPDIFF(MINUTE, create_time, NOW()) > ?)',
        [3, 0, $fail_reason, 1001, 0, Config::get('constant.QUEUE_AGE_LIMIT')]
      );

      // Commit the transaction
      DB::commit();
    }
  }
}
