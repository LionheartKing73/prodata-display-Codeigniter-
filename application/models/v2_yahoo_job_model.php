<?php
class V2_yahoo_job_model extends CI_Model
{
    private $collection = "v2_yahoo_jobs";

    function __construct()
    {
        $this->load->database();
        // Call the Model constructor
        parent::__construct();
    }

    public function create($data)
    {
        $this->db->insert($this->collection, $data);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_by_type_and_status($status, $type)
    {
        return $this->db->get_where($this->collection, ["status" => $status, "type" => $type])->row_array();
    }

    public function get_demographic_jobs_by_status($status)
    {
        return $this->db->get_where($this->collection, ["status" => $status, "type !=" => 'performance_stats', "type !=" => 'adjustment_stats ', "type !=" => 'video_stats '])->result_array();
    }

    public function get_jobs_count_by_status_and_type($status, $type)
    {
        return $this->db->get_where($this->collection, ["status" => $status, "type" => $type])->num_rows();
    }

    public function get_by_status($status)
    {
        return $this->db->get_where($this->collection, ["status" => $status])->result_array();
    }

    public function update($id, $data){
        return $this->db->where("id", $id)->update($this->collection, $data);
    }
    public function complete_demographic_jobs(){
        return $this->db->where_in("type", ['device_type', 'gender', 'age'])->where(['status'=>'submitted'])->update($this->collection, ['status'=>'completed']);
    }

}
?>
