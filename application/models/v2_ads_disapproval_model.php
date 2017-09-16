<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class v2_ads_disapproval_model extends CI_Model {

    protected $CI;
    private $collection = 'v2_ads_disapproval';

    public function __construct() {
        parent::__construct();
        $this->CI =& get_instance();
        $this->CI->load->database();

        $this->load->library('google_adx');
    }

    /**
     * Insert Ad Status
     *
     * @param  array   $data
     * @return integer $insert_id
     */
    public function create(array $data)
    {
        $this->CI->db->insert($this->collection, $data);
        $insert_id = $this->CI->db->insert_id();
        return $insert_id;
    }

    /**
     * Check if Ad record exists
     * @param  [type]  $ad_id [description]
     * @param  [type]  $io    [description]
     * @return boolean        [description]
     */
    public function is_exists($ad_id, $io)
    {
        return $this->CI->db
            ->where(['ad_id' => $ad_id, 'io' => $io])
            ->get($this->collection)
            ->row_array();
    }

    /**
     * Update Ad status
     *
     * @param  integer $id
     * @param  array   $data
     * @return void
     */
    public function update($id, array $data)
    {
        return $this->CI->db->update($this->collection, $data, ['id' => $id]);
    }

    /**
     * Update Status by Ad Id
     *
     * @param  integer $ad_id
     * @param  array   $data
     * @return void
     */
    public function update_by_ad_id($ad_id, array $data)
    {
        return $this->CI->db
            ->where('ad_id', $ad_id)
            ->update($this->collection, $data);
    }

    /**
     * Update All Ad's status by Campaign IO
     *
     * @param  string $io
     * @param  array  $data
     * @return void
     */
    public function update_by_campaign_io($io, array $data)
    {
        return $this->CI->db
            ->where('io', $io)
            ->update($this->collection, $data);
    }

    /**
     * Update ad status by Ad ID and campaign Io
     *
     * @param  integer $ad_id
     * @param  string  $io
     * @param  array   $data
     * @return void
     */
    public function update_by_ad_id_and_campaign_io($ad_id, $io, array $data)
    {
        return $this->CI->db
            ->where(['io' => $io, 'ad_id' => $ad_id])
            ->update($this->collection, $data);
    }

    /**
     * Update All Ad's status by Campaign ID
     *
     * @param  integer $campaign_id
     * @param  array   $data
     * @return void
     */
    public function update_by_campaign_id($campaign_id, array $data)
    {
        return $this->CI->db
            ->where('io', $campaign_id)
            ->update($this->collection, $data);
    }

    /**
     * Get all ads which not APPROVED
     *
     * @param  array $params WHERE conditions
     * @return array
     */
    public function get_non_approved_ads(array $params = [])
    {
        $where = [];

        if ( is_array($params) ) {
            $where = array_merge($where, $params);
        }

        return $this->CI->db
            ->where($where)
            ->where_not_in('status', ['APPROVED', 'ACTIVE'])
            ->get($this->collection)
            ->result_array();
    }

    /**
     * Get List of Ad Id's already submitted to Google AdX
     */
    public function get_already_submitted_ad_ids()
    {
        $ad_ids = $this->CI->db->select('ad_id')->get($this->collection)->result_array();
        $ad_ids = array_column($ad_ids, 'ad_id');
        return $ad_ids;
    }
}
?>
