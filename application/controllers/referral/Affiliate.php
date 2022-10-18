<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Affiliate extends Securearea {
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->helper("url");
		$this->load->library('session');	
		$this->load->library('Mobile_Detect');	
		$detect = new Mobile_Detect;
		$this->data['detect'] = $detect;
		$this->load->model("Referral_Model");
		$this->load->model("Affiliate_Model");	
		$this->data['el']="";
		if(isset($_COOKIE['country_id']) && $_COOKIE['country_id'] != 0){
			$this->region = $_COOKIE['country_id'];
		}
		if($this->session->userdata('userid')){
			$this->data['user_id'] = $this->session->userdata('userid');
		}else{
			$this->data['user_id'] = "";
		}
		$this->lang->load('english','english');
    }

    public function user_affiliation(){
		$email = isset($_GET['email'])?$_GET['email']:"";
		$referral = isset($_GET['ref'])?$_GET['ref']:"";
		$senderid = isset($_GET['id'])?$_GET['id']:"";
		$type = isset($_GET['type'])?"&type=".$_GET['type']:"";

		$query = $this->Affiliate_Model->getUserData($email);
		if(isset($query[0]) && $query[0]->id != ""){
			//registered user
			$this->Referral_Model->getReferralCode($email, $senderid);
			$check = $this->Affiliate_Model->check_affiliated($query[0]->userid);
			if(isset($check) && $check['status'] == "1"){
				//already affiliated
				$this->session->set_flashdata('affiliated',"You are already an affiliated user");
				redirect(base_url());
			}else{
					$this->session->set_flashdata('affiliated',"You are now an affiliated user");
					$this->affiliate_user($query[0]->userid, "user_affiliation");
			}
		}else{
			redirect(base_url("join_us?ref=".$referral.$type));
		}
    }

    public function affiliate_user($userid = "", $from = ""){
        $result = $this->Affiliate_Model->affilite_user($userid);
		if($result['status'] == "1"){
			if($from == "user_affiliation"){
				redirect(base_url());
			}else{
				redirect(base_url("referral/referrals/"));
			}
		}
    }
}
?>