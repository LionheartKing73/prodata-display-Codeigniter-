<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Iab_categories extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("v2_iab_category_model");
    }

    public function get_categories()
    {
        debug($this->v2_iab_category_model->get_by_iab_category_id('IAB25'));
    }

    // http://reporting.prodata.media.am/v2/iab_categories/import_to_db
    public function import_to_db()
    {
        $csv_path = FCPATH . 'IAB_Categories.csv';
        $data = [];

        if ( ($handle = fopen($csv_path, 'r')) !== false ) {

            while ( ($row = fgetcsv($handle)) !== false ) {

                $category = trim($row[0]);

                if ( empty($category) || strtolower($category) == 'category' ) continue;

                $is_match = preg_match_all("/(IAB\d+)\s?-\s?(.*)/", $category, $matches);

                if ( $is_match && !empty($matches) ) {

                    $category_id = trim($matches[1][0]);

                    $insert_id = $this->v2_iab_category_model->create([
                        'category_id' => $category_id,
                        'name' => trim($matches[2][0]),
                    ]);

                    $data[$category_id] = [
                        'id' => $insert_id,
                        'parent_id' => null,
                        'category_id' => $category_id,
                        'name' => $name,
                        'sub_categories' => []
                    ];
                } else {
                    $category_id = $category;

                    $insert_id = $this->v2_iab_category_model->create([
                        'parent_id' => $data[$category_id]['id'],
                        'category_id' => trim($row[1]),
                        'parent_category_id' => $category_id,
                        'name' => trim($row[2])
                    ]);

                    $data[$category_id]['sub_categories'][] = [
                        'id' => $insert_id,
                        'parent_id' => $data[$category_id]['id'],
                        'category_id' => $category_id,
                        'parent_category_id' => trim($row[1]),
                        'name' => trim($row[2])
                    ];
                }

            }

            fclose($handle);
        }

        debug($data, 0);
    }
}