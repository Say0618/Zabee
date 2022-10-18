<?php 
class Profile_Model extends CI_Model{
	function __construct(){
		parent:: __construct();
		$this->load->database("default");
	}

	public function getProfiles($user_id, $profile_id = ''){
		
		$this->db->select('*')->where('userid', $user_id)->from('tbl_profiles');
		if($profile_id != ''){
			$this->db->where('id', $profile_id);
		}
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->row();
		} else {
			return FALSE;			
		}
	}	

	public function profile_add($insert_data){
		$return = 0;
		if($this->db->insert('tbl_profiles', $insert_data)){
			$return = 1;
		}
		return $return;		
	}

	public function profile_update($insert_data, $id){
		$return = 0;
		if($this->db->where('id', $id)->update('tbl_profiles', $insert_data)){
			$return = 1;
		}
		return $return;
	}
}
?>