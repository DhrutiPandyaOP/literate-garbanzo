<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\UserController;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\ImageController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteDesignCancelDownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $download_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($design_download_id)
    {
      $this->download_id =$design_download_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      try{
        $result = DB::select('SELECT output_design,job_id FROM design_template_jobs WHERE download_id = ?', [$this->download_id]);

        if(count($result) > 0) {
          $output_design = $result[0]->output_design;
          $original_design_path = './..' . Config::get('constant.TEMP_DIRECTORY') . $output_design;
          if (((new ImageController())->checkFileExist($original_design_path)) != 0) {
            unlink($original_design_path);
          }

          DB::beginTransaction();
          
          $fail_reason = 'User canceled the job execution or stopped the download process.';

          DB::delete('DELETE FROM jobs WHERE id = ?', [$result[0]->job_id]);

          DB::update('UPDATE design_template_jobs SET status=3,is_active=0 ,fail_reason '.$fail_reason.' WHERE download_id = ?', [$this->download_id]);
          DB::commit();

        }

      } catch (\Exception $e) {
        $failed = 2;
        (new ImageController())->logs("DeleteDesignCancelDownloadJob.php handle catch()",$e);

        DB::beginTransaction();
        DB::update('UPDATE design_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
        DB::commit();
      }
    }
}
