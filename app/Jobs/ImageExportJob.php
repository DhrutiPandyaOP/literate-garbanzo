<?php

namespace App\Jobs;

use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\ImageExportController;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImageExportJob extends Job implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * The number of times the job may be attempted.
   *
   * @var int
   */
  public $tries = 1;
  protected $request;
  protected $download_id; // Download_id for download output design
  protected $result_status; // All process status : 1=Success, 0=Fail
  protected $type;
  protected $size;
  protected $userRoleId; //

  protected $get_download_id; //
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($request, $type, $size, $userRoleId, $download_id)
  {
    $this->request = $request;
    $this->type = $type;
    $this->size = $size;
    $this->userRoleId = $userRoleId;
    $this->download_id = $download_id;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {

      $node_response = (new ImageExportController())->testNodeServer($this->download_id);

      if ($node_response == 200) {
        $record = DB::select('SELECT id,create_time FROM design_template_jobs WHERE download_id = ?', [$this->download_id]);
        $start = $record[0]->create_time;

        if ($this->type == 1) {
          $par_type = 'jpg';
        } elseif ($this->type == '2') {
          $par_type = 'png';
        } else {
          $par_type = 'pdf';
        }

        $details = [
          'requestData' => $this->request,
          'download_id' => $this->download_id,
          'size' => $this->size,
          'type' => $par_type,
          'userRoleId' => $this->userRoleId,
        ];

        $url = Config::get('constant.NODE_API_URL_IP') . '/user/getImage';
        $ch = curl_init($url);
        $payload = json_encode($details);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $render_start_time = date('Y-m-d H:i:s');

        //$render_start_time when node will php will send request to node
        DB::update('UPDATE design_template_jobs SET render_start_time = ? WHERE download_id =?', [$render_start_time, $this->download_id]);
      }else{
        Log::error("ImageExportJob.php failed() : ", ["download_id" => $this->download_id, "\ngetTraceAsString" => 'Node has been stopped', "\nerror_msg" => 'Node has been stopped']);
      }
    } catch (Exception $e) {
      $failed = 2;
      Log::error("ImageExportJob.php failed() : ", ["download_id" => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
      $fail_reason = json_encode(array("download_id" => $this->download_id, "\nerror_msg" => $e->getMessage()));

      DB::beginTransaction();
      DB::update('UPDATE design_template_jobs SET status=? , fail_reason=? WHERE download_id =?', [$failed, $this->download_id, $fail_reason]);
      DB::commit();

      Log::info('set status=2 failed()');
    }
  }

  public function failed(Exception $e)
  {
    $failed = 2;
    Log::error("ImageExportJob.php failed() : ", ["download_id" => $this->download_id, "\ngetTraceAsString" => $e->getTraceAsString(), "\nerror_msg" => $e->getMessage()]);
    $fail_reason = json_encode(array("download_id" => $this->download_id, "\nerror_msg" => $e->getMessage()));
    DB::beginTransaction();
    DB::update('UPDATE design_template_jobs SET status=? , fail_reason=? WHERE download_id =?', [$failed, $this->download_id, $fail_reason]);
    DB::commit();
    Log::info('set status=2 failed()');
  }
}
