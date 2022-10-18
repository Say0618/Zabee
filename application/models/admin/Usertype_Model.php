<?php
class Usertype_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	function getadminUsers($select = "",$where = "")
	{
		if($select == ""){
			$select = "*";
		}
		$sql = "SELECT ".$select." FROM ".DBPREFIX."_backend_usertype ORDER BY user_type_id ASC";		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			return $query->result_array();
		}		
		return FALSE;
	}

	function getadminUsers2($search, $offset, $length)
	{
		$this->datatables->select('ua.user_type_id as id, user_type_name, user_type_dpname, allowed_links')
			 			->from('tbl_backend_usertype as ua');
		$this->db->like('user_type_name', $search);
		$this->db->order_by("ua.user_type_id", "asc");
		$this->db->limit($length,  $offset);
	}
	
	function getAllmodules()
	{
		$this->db->order_by("module_id", "asc"); 
		$query = $this->db->get(DBPREFIX."_admin_modules");
		if ($query->num_rows() > 0){
			return $query->result_array();
		}		
		return FALSE;
	}
	
	function delete_usertype($usertype)
	{
		$this->db->where('user_type_id', $usertype);
		$this->db->delete(DBPREFIX."_backend_usertype");
	}
	
	function getusertype($typeid)
	{
		$this->db->where('user_type_id', $typeid);		
		$query = $this->db->get(DBPREFIX."_backend_usertype");
		if ($query->num_rows() > 0){
			return $query->result_array();
		}		
		return FALSE;
	}
	
	public function createusertype($data)
	{
		$sql = "INSERT INTO ".DBPREFIX."_backend_usertype 
			(user_type_name,user_type_dpname,allowed_links)
			VALUES 
			('".$data['typename']."','".$data['displayname']."','".$data['allowed_modules']."')";		
		$this->db->query($sql);
	}
	
	public function editusertype($data)
	{
		$sql = "UPDATE ".DBPREFIX."_backend_usertype 
			SET 
			user_type_name = '".$data['typename']."',
			user_type_dpname = '".$data['displayname']."',
			allowed_links = '".$data['allowed_modules']."'
			WHERE 
			user_type_id = '".$data['user_type_id']."'";
		$this->db->query($sql);		
	}
}
?>