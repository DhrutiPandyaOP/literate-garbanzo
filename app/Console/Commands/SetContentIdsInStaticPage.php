<?php

namespace App\Console\Commands;

use App\Http\Controllers\ImageController;
use Config;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;

class SetContentIdsInStaticPage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetContentIdsInStaticPage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set content ids in image static page';

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

            $main_page_count = 0;
            $sub_page_count = 0;

            $static_page_details = DB::select('SELECT
                                                    sp.id AS static_page_id,
                                                    sp.static_page_sub_category_id, 
                                                    sp.sub_category_id,
                                                    sp.catalog_id,
                                                    sp.catalog_name,
                                                    sp.content_ids,
                                                    sp.catalog_path
                                                FROM  
                                                    static_page_master AS sp     
                                                WHERE 
                                                    sp.is_active = ? AND
                                                    sp.content_type = ?
                                                ORDER BY sp.update_time DESC', [1, Config::get('constant.IMAGE')]);

            foreach ($static_page_details as $i => $static_page_detail) {

                if ($static_page_detail->content_ids != '' || $static_page_detail->content_ids != null) {
                    Log::error('SetContentIdsInStaticPage : static page contain already content_id ', ['content_ids' => $static_page_detail->content_ids, '$i' => $i]);

                    continue;
                }

                $this->item_count = Config::get('constant.DEFAULT_ITEM_COUNT_TO_GET_CATALOG_CONTENT');
                $this->page = 1;
                $this->offset = ($this->page - 1) * $this->item_count;
                $this->is_active = 1;
                $sub_category_id = $static_page_detail->sub_category_id;
                $catalog_id = $static_page_detail->catalog_id;

                //Case 3 : Get all template by catalog_id
                if ($catalog_id) {

                    $template_list = DB::select('SELECT
                                                      cm.id AS content_id,
                                                      LEFT(cm.search_category, INSTR(cm.search_category, ",")-1) AS search_category
                                                  FROM
                                                      content_master AS cm
                                                      JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id 
                                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                      JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                                  WHERE
                                                    cm.is_active = 1 AND
                                                    cm.catalog_id = ? AND 
                                                    cm.content_type = ?
                                                    ORDER BY cm.update_time DESC limit ?, ?', [$catalog_id, 4, $this->offset, $this->item_count]);

                    $content_id_array = [];
                    $search_category_array = [];
                    foreach ($template_list as $id) {
                        array_push($content_id_array, $id->content_id);
                        if ($id->search_category != '' or $id->search_category != null) {
                            array_push($search_category_array, $id->search_category);
                        }
                    }
                    $this->content_ids = implode(',', $content_id_array);
                    $this->search_category = implode(',', array_unique($search_category_array));
                    Log::info('SetContentIdsInStaticPage : catalog_id : ', ['content_id' => $this->content_ids, 'catalog_id' => $catalog_id]);

                    DB::beginTransaction();
                    DB::update('UPDATE static_page_master SET content_ids = ?, search_category = ? WHERE id = ?', [$this->content_ids, $this->search_category, $static_page_detail->static_page_id]);
                    DB::commit();

                    $sub_page_count++;
                } else {

                    $template_list = DB::select('SELECT
                                                      cm.id AS content_id,
                                                      LEFT(search_category, INSTR(search_category, ",")-1) AS search_category
                                                  FROM
                                                      content_master AS cm
                                                      JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id 
                                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                      JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                                  WHERE
                                                      cm.is_active = 1 AND
                                                      cm.is_featured = 1 AND
                                                      cm.catalog_id IN (SELECT catalog_id FROM sub_category_catalog WHERE sub_category_id = ?)
                                                ORDER BY cm.update_time DESC limit ?, ?', [$sub_category_id, $this->offset, $this->item_count]);

                    $content_id_array = [];
                    $search_category_array = [];
                    foreach ($template_list as $id) {
                        array_push($content_id_array, $id->content_id);
                        if ($id->search_category != '' or $id->search_category != null) {
                            array_push($search_category_array, $id->search_category);
                        }
                    }
                    $this->content_ids = implode(',', $content_id_array);
                    $this->search_category = implode(',', array_unique($search_category_array));
                    Log::info('SetContentIdsInStaticPage : sub_category_id', ['content_id' => $this->content_ids, 'sub_category_id' => $sub_category_id]);

                    DB::beginTransaction();
                    DB::update('UPDATE static_page_master SET content_ids = ?, search_category = ?  WHERE id = ?', [$this->content_ids, $this->search_category, $static_page_detail->static_page_id]);
                    DB::commit();

                    $main_page_count++;
                }

                if (! $this->content_ids) {

                    Log::error('SetContentIdsInStaticPage : content_lis is empty.');

                    $template_list = DB::select('SELECT
                                                      cm.id AS content_id,
                                                      LEFT(cm.search_category, INSTR(cm.search_category, ",")-1) AS search_category
                                                  FROM
                                                      content_master AS cm
                                                      JOIN sub_category_catalog AS scc ON cm.catalog_id = scc.catalog_id 
                                                      JOIN catalog_master AS ctm ON ctm.id = scc.catalog_id
                                                      JOIN sub_category_master AS scm ON scc.sub_category_id=scm.id
                                                  WHERE
                                                      cm.is_active = 1 AND
                                                      cm.is_featured = 1 AND
                                                      cm.content_type = 4
                                                  ORDER BY cm.update_time DESC limit ?, ?', [$this->offset, $this->item_count]);

                    $content_id_array = [];
                    $search_category_array = [];
                    foreach ($template_list as $id) {
                        array_push($content_id_array, $id->content_id);
                        if ($id->search_category != '' or $id->search_category != null) {
                            array_push($search_category_array, $id->search_category);
                        }
                    }
                    $this->content_ids = implode(',', $content_id_array);
                    $this->search_category = implode(',', array_unique($search_category_array));
                    Log::info('SetContentIdsInStaticPage empty : ', ['content_id' => $this->content_ids, 'sub_category_id' => $sub_category_id, 'catalog_id' => $catalog_id]);

                    DB::beginTransaction();
                    DB::update('UPDATE static_page_master SET content_ids = ?, search_category = ? WHERE id = ?', [$this->content_ids, $this->search_category, $static_page_detail->static_page_id]);
                    DB::commit();

                }
            }

            Log::debug('SetContentIdsInStaticPage : ', ['sub_page_count' => $sub_page_count, 'main_page_count' => $main_page_count]);

        } catch (Exception $e) {
            (new ImageController())->logs('SetContentIdsInStaticPage', $e);
            //Log::error("SetContentIdsInStaticPage : ", ["Exception" => $e->getMessage(), "\nTraceAsString" => $e->getTraceAsString()]);
        }
    }
}
