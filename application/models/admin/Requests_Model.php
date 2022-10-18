<?php
class Requests_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
    
    public function getRequests($userid, $user_type){
		if($user_type == "1"){
		$this->datatables->select('request_for, request_name, request_info, created_by, status, r.id as id');
		}else{
			$this->datatables->select('request_for, request_name, request_info, status')
			->where('user_id', $userid);
		}
		$this->datatables->from('tbl_requests as r');
		$this->db->order_by('r.id', 'DESC');		
	}

	public function get_searched_query($userid, $user_type){
		$this->datatables->select('CONCAT(first_name, " ", last_name), email, query, item_condition, price, description, r.id as id');
		$this->datatables->from('tbl_search_query as r');
		$this->db->order_by('r.id', 'DESC');
		// print_r($this->db->last_query()); die();		
	}
    
    public function getNumberofRequests(){
        $this->db->select('count(id) as total')->from('tbl_requests');
        $query = $this->db->get();	
		if($query && $query->num_rows()>0){
			return $query->result()[0]->total;
		}else{
			return FALSE;			
		}
	}
	
	public function requests_add($data, $user_id){
        $name = $this->createdUserName($user_id);
        $insert_data = array(
			'user_id' => $user_id,
			'created_by' => $name,
			'request_for' => $data['request_type'],
			'request_name' => $data['request_name'],
			'request_info' => $data['info'],
			'is_active' => 1
		);
		$this->db->insert('tbl_requests', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function approve_request($data){
        $update_data = array(
			'status' => $data['value'],
		);
		$this->db->where('id', $data['row_id'])->update('tbl_requests', $update_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
    }
    
	public function createdUserName($user_id){
        $this->db->from('tbl_users');
		$this->db->select('concat(firstname, " ", lastname) as name');
		$this->db->where('userid',$user_id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result[0]['name'];
	}
}	
?>