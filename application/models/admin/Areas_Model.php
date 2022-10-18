<?php
class Areas_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	///////     ----------------------- areas -----------------------------      ////////
	function getAreas($area_id = "",$select = "")
	{
		$where = "";
		if($area_id){
			$where .= " AND area_id = '".$area_id."'";
		}
		if($select == ""){
			$select = "areas.*, users1.admin_name as name1, users2.admin_name as name2";
		}
			$sql = "SELECT  ".$select."
				FROM ".DBPREFIX."_areas_covered as areas
				LEFT JOIN ".DBPREFIX."_backend_users as users1 ON areas.created_id = users1.admin_id
				LEFT JOIN ".DBPREFIX."_backend_users as users2 ON areas.updated_id = users2.admin_id
				WHERE deleted_id is NULL ".$where. "
				ORDER BY area_name ASC
				";	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
	}
	
	/**
	* this function is common for areas and subareas
	* @param undefined $category_id
	* @param undefined $data
	* 
	*/

}	
?>