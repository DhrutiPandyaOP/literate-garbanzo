<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use Config;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Log;

class DeleteCancelDownloadDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $download_id;

    public function __construct($video_download_id)
    {
        $this->download_id = $video_download_id;
    }

    public function handle()
    {
        try {
            $result = DB::select('SELECT output_video FROM video_template_jobs WHERE download_id = ?', [$this->download_id]);
            if (count($result) > 0) {
                $output_video = $result[0]->output_video;
                if ($output_video != '') {
                    $original_video_path = './..'.Config::get('constant.TEMP_DIRECTORY').$output_video;
                    //            Log::info('original_video_path : ',[$original_video_path]);
                    if (($is_exist = (new ImageController())->checkFileExist($original_video_path)) != 0) {
                        unlink($original_video_path);
                    }
                }

                DB::beginTransaction();
                //DB::delete('DELETE FROM video_template_jobs WHERE download_id = ?', [$this->download_id]);
                DB::update('UPDATE video_template_jobs SET status=3,is_active=0 WHERE download_id = ?', [$this->download_id]);
                DB::commit();
                $get_download_id = DB::select('SELECT id FROM video_template_jobs WHERE download_id=?', [$this->download_id]);
                (new userController())->addVideoGenerateHistory('cancel', null, $get_download_id[0]->id, null, null, null, 3);
            }

        } catch (\Exception $e) {
            $failed = 2;
            (new ImageController())->logs('DeleteCancelDownloadDataJob.php handle catch()', $e);
            //      Log::error("DeleteCancelDownloadDataJob.php handle catch() : ", ["download_id" => $this->download_id, "\nerror_msg" => $e->getMessage(), "\ngetTraceAsString" => $e->getTraceAsString()]);
            DB::beginTransaction();
            DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
            DB::commit();
        }
    }

    public function failed(Exception $e)
    {
        $failed = 2;
        Log::error('DeleteCancelDownloadDataJob.php failed() : ', ['download_id' => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
        DB::beginTransaction();
        DB::update('UPDATE video_template_jobs SET status=? WHERE download_id =? ', [$failed, $this->download_id]);
        DB::commit();
    }
}
