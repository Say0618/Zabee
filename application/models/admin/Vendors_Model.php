<?php
class Vendors_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	///////     ----------------------- Vendors -----------------------------      ////////
	function getVendors($vendor_id = "",$select = "")
	{
		$where = "";
		if($vendor_id){
			$where = " AND vendors.vendor_id = '".$vendor_id."'";
		}
		if(is_array($vendor_id)){
			$where = " AND vendors.vendor_id IN ('".implode("','",$vendor_id)."')";
		}
		if($select == ""){
			$select = "vendors.*, users1.admin_id as userid1, users2.admin_id as userid2, users1.admin_name as name1, users2.admin_name as name2";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_vendors as vendors
				LEFT JOIN ".DBPREFIX."_backend_users as users1 ON vendors.created_id = users1.admin_id
				LEFT JOIN ".DBPREFIX."_backend_users as users2 ON vendors.updated_id = users2.admin_id
				WHERE deleted_id is NULL ".$where;			
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
	}
	
	function insertVendors($data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$this->db->insert_batch(DBPREFIX."_vendors", $data); 
		return TRUE;
	}
	
	function deleteVendors($vendor_id,$data)
	{
		$arr = array('vendor_id' => $vendor_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_vendors", $data);
		return TRUE;
	}
	
	
	/**
	* this function is common for vendors and subvendors
	* @param undefined $vendor_id
	* @param undefined $data
	* 
	*/

	function updateVendors($vendor_id,$data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$arr = array('vendor_id' => $vendor_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_vendors", $data[0]);
		return TRUE;
	}
}	
?>