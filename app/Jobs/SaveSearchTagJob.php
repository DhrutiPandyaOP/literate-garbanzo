<?php

namespace App\Jobs;

use App\Http\Controllers\ImageController;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Log;

class SaveSearchTagJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $template_count;

    public $search_category;

    public $sub_category_id;

    public $content_type;

    public $category_id;

    public $catalog_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($template_count, $search_category, $category_id = '', $sub_category_id = 0, $catalog_id = '', $content_type = 0)
    {
        try {
            $this->template_count = $template_count;
            $this->search_category = $search_category;
            $this->category_id = $category_id;
            if ($sub_category_id) {
                $sub_category_detail = DB::select('SELECT
                                             id
                                         FROM
                                             sub_category_master AS scm
                                         WHERE
                                            scm.uuid =?', [$sub_category_id]);
                $this->sub_category_id = $sub_category_detail[0]->id;
            } else {
                $this->sub_category_id = $sub_category_id;
            }
            $this->catalog_id = $catalog_id;
            $this->content_type = $content_type;
        } catch (Exception $e) {
            Log::error('SaveSearchTagJob.php construct() : ', ['Exception' => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $week_start_date = date('Y-m-d', strtotime('monday this week'));
            $week_end_date = date('Y-m-d', strtotime('sunday this week'));
            $create_time = date('Y-m-d H:i:s');
            //      Log::info("Template : ",["template cout" => $this->template_count,"search ctegory" => $this->search_category,"sub category id " =>$this->sub_category_id, "content type" =>$this->content_type]);
            if ($this->template_count != 0) {
                $tag = DB::select('SELECT
                                   id
                                FROM tag_analysis_master
                                WHERE
                                  tag=? AND
                                  is_success = ? AND
                                  category_id = ? AND
                                  sub_category_id=? AND
                                  catalog_id = ? AND
                                  content_type=? AND
                                  week_start_date <=NOW() AND
                                  week_end_date >= NOW()', [$this->search_category, 1, $this->category_id, $this->sub_category_id, $this->catalog_id, $this->content_type]);
                if (count($tag) > 0) {
                    $id = $tag[0]->id;
                    DB::update('UPDATE tag_analysis_master SET search_count = search_count+1,content_count= ? WHERE id=?', [$this->template_count, $id]);
                } else {
                    DB::insert('INSERT INTO
                               tag_analysis_master(tag,is_success,content_count,search_count,category_id,sub_category_id,catalog_id,content_type,week_start_date,week_end_date,create_time)
                               VALUES(?,?,?,?,?,?,?,?,?,?,?)', [$this->search_category, 1, $this->template_count, 1, $this->category_id, $this->sub_category_id, $this->catalog_id, $this->content_type, $week_start_date, $week_end_date, $create_time]);
                }
            } else {
                $tag = DB::select('SELECT
                                 id
                               FROM
                                 tag_analysis_master
                               WHERE
                                 tag=? AND
                                 is_success = ? AND
                                 category_id = ? AND
                                 sub_category_id=? AND
                                 catalog_id = ? AND
                                 content_type=? AND
                                 week_start_date <=NOW() AND
                                 week_end_date >= NOW()', [$this->search_category, 2, $this->category_id, $this->sub_category_id, $this->catalog_id, $this->content_type]);
                if (count($tag) > 0) {
                    $id = $tag[0]->id;
                    DB::update('UPDATE tag_analysis_master SET search_count = search_count+1,content_count= ? WHERE id=?', [$this->template_count, $id]);
                } else {
                    DB::insert('INSERT INTO
                             tag_analysis_master(tag,is_success,content_count,search_count,category_id,sub_category_id,catalog_id,content_type,week_start_date,week_end_date,create_time)
                             VALUES(?,?,?,?,?,?,?,?,?,?,?)', [$this->search_category, 2, $this->template_count, 1, $this->category_id, $this->sub_category_id, $this->catalog_id, $this->content_type, $week_start_date, $week_end_date, $create_time]);
                }
            }
        } catch (Exception $e) {
            (new ImageController())->logs('SaveSearchTagJob handle()', $e);
            //      Log::error("SaveSearchTagJob handle() : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
