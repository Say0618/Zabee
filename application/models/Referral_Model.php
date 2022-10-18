<?php
class Referral_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
    public function check_code($code){
        $result = $this->db->select('referral_code')
                        ->from(DBPREFIX."_referral")
                        ->where('referral_code',$code)->get();
        if($result->num_rows() > 0){
            $response = $result->result();
            return $response[0]->referral_code;
        }else{
            return false;
        }	
    }

    public function get_referral($id, $search, $offset = 0, $length = 10){
		$this->datatables->select('receiver_firstname, receiver_lastname, receiver_email, referral_code, invite_time, maturity AS status')
                ->from('tbl_referral')
                ->where("referral_type", "invite")
				->where("sender_id", $id);
				if($search != ''){
					$this->db->like('receiver_email', $search);
				}
				$this->db->limit($length,  $offset);		
                $this->datatables->group_by("referral_code");		
	}

	public function get_product_referral($id, $search, $offset = 0, $length = 10){
		$this->datatables->select("prd.product_name AS 'product', ref.sale_count AS 'visits',ref.impression AS 'sales',ref.referral_code AS 'code',ref.invite_time AS 'date', SUM(tran.points) AS 'points'")
				->from('tbl_referral ref')
				->join('tbl_product_inventory prd','ref.prd_id = prd.inventory_id')
				->join('tbl_affiliated_transactions tran','ref.referral_code = tran.referral_code', 'LEFT')
				->where("ref.referral_type", "product")
				->where("ref.sender_id", $id);
				if($search != ''){
					$this->db->like('prd.product_name', $search);
				}
				$this->db->limit($length,  $offset);	
				$this->datatables->group_by("ref.referral_code");
	}

	public function getReferralCode($email, $senderid){
		$code = $this->db->select("referral_code")->from("tbl_referral")->where(array("receiver_email"=>$email, "sender_id"=>$senderid))->get()->result();	
		if(isset($code[0]) && $code[0]->referral_code != ""){
			return $this->referralValidate($code[0]->referral_code);
		}
	}

	public function referralValidate($referral){
		if($this->compare_date($referral) == "valid"){
			$ref_data['accept_time'] = date("y-m-d H:i:s"); 
			$ref_data['maturity'] = "1";
			$this->db->where("referral_code",$referral);
			$this->db->update("tbl_referral",array("accept_time"=>$ref_data['accept_time'], "maturity"=>$ref_data['maturity']));
			return array("status"=>"1", "message"=>"Referral Code Valid", "code"=>$referral);
		}else{
			return array("status"=>"0", "message"=>"Referral Code Expired", "code"=>$referral);
		}
    }
    
    public function compare_date($code){
		$data = $this->db->select("expire_time, maturity")
					->from(DBPREFIX."_referral")
					->where("referral_code", $code)->get()->result();
		if((date("Y-m-d H:i:s") < $data[0]->expire_time) && $data[0]->maturity == '0' ){
			return "valid";
		}else{
			return "not valid or expired";
		}
    }
    
    public function get_data(){
		$this->datatables->select("user.firstname, user.lastname, user.email, r.sender_id, SUM(IF(r.`referral_type` = 'product', 1, 0)) AS referral_count, SUM(IF(r.`referral_type` = 'invite', 1, 0)) AS invite_count")
				->from(DBPREFIX."_referral r")
				->join(DBPREFIX."_users user","user.userid = r.sender_id")
				->group_by("r.sender_id");
	}

	public function foundUserReferral($userid, $receiver_email){
		$data = $this->db->select("*")
					->from(DBPREFIX."_referral")
					->where(array("sender_id"=>$userid, "receiver_email"=>$receiver_email))->get()->result();
		if((isset($data[0])) && $data[0]->id != ""){
			return array("status"=>"0", "message"=>"This email is already invited by you");
		}else{
			return array("status"=>"1", "message"=>"This email is ready to invited by you");
		}
	}
}