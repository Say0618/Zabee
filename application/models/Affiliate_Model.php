<?php

class Affiliate_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
    }
    
    public function check_affiliated($userid){
        $query = $this->db->select("affiliated_id")->from("tbl_referral_users")->where('user_id',$userid)->get()->result();
        if(isset($query[0]) && $query[0]->affiliated_id != ""){
            return array("status"=>"1", "message"=>"This account is affiliated for referrals", "affiliated_id" => $query[0]->affiliated_id);
        }else{
            return array("status"=>"0", "message"=>"this account is not affiliated for referrals", "affiliated_id" => "");
        }
    }

    public function affilite_user($userid = ""){
		if($userid == ""){
			$userid = $this->session->userdata('userid');
		}
		$data = array("user_id"=>$userid,"date"=>date('Y-m-d H:i:s'));
		$result = $this->db->insert("tbl_referral_users",$data);
		if($result){
			$id = $this->db->insert_id();
			$this->db->where('id', $id);
			$this->db->set("affiliated_id", $userid.'_'.$id);
			$update = $this->db->update('tbl_referral_users');
			if($update){
				$_SESSION['affiliated_id'] = $userid.'_'.$id;
				return array("status"=>"1", "message"=>"User Affiliated Successfully");
				// redirect(base_url('referral/referrals/'));
			}else{
                return array("status"=>"0", "message"=>"Error while updating data into database");
            }
		}else{
            return array("status"=>"0", "message"=>"Error while inserting data into database");
        }
    }

    public function getUserData($email){
        return $this->db->select("*")->from("tbl_users")->where('email',$email)->get()->result();
    }
}

?>