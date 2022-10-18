<?php
class Addressbook_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	public function getUserLocationsByUserId($user_id, $user_type = 'seller', $location_id = '')
	{
		$this->db->select("ul.*, bc.nicename as billing_country, sc.nicename as shiping_country")
				 ->from(DBPREFIX.'_user_locations AS ul')
				 ->join(DBPREFIX.'_country AS bc', 'ul.bill_country = bc.id OR ul.`bill_country` = bc.`iso`','left')
				 ->join(DBPREFIX.'_country AS sc', 'ul.ship_country = sc.id OR ul.`bill_country` = sc.`iso`','left')
				 ->where('ul.user_id', $user_id)
				 ->where('ul.user_type', $user_type);
		if($location_id != ''){
			$this->db->where('ul.id', $location_id);
		}
		$query = $this->db->get();
		if($query && $query->num_rows()>0)
		{
			return $query->result();
		} else {
			return FALSE;			
		}
	}

	public function getUserLocationsByUserIdforlocation($user_id, $user_type = 'seller', $location_id="",$active = "",$select="*"){
		$this->db->select($select)
				 ->from(DBPREFIX.'_user_address')
				 ->where('user_id', $user_id)
				 ->where('user_type', $user_type);
		if($active == ""){
			$this->db->where('active',"1");
		}
		if($location_id){
			$this->db->where('id', $location_id);
		}
		$this->db->order_by('use_address,id','DESC');
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0)
		{
			if($location_id){
				return $query->row();
			}else{
				return $query->result();
			}
		} else {
			return FALSE;			
		}
	}
	public function update_addressbook($data, $user_id, $location_id){
		$this->db->where('id', $location_id)
				 ->where('user_id', $user_id)
				 ->update(DBPREFIX.'_user_address', $data);
		//  echo $this->db->last_query();die();
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function update_addressonly($data, $data2, $user_id, $location_id){
		$this->db->where(array('use_address'=> "1", 'user_id'=>$user_id))
				 ->update(DBPREFIX.'_user_address', $data2);
		$this->db->where('id', $location_id)
				 ->where('user_id', $user_id)
				 ->update(DBPREFIX.'_user_address', $data);
		// echo $this->db->last_query();die();
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function deleteaddress($location_id,$user_id=""){
		//echo $location_id;die();
		$value= array('active' => "0");
		$this->db->where('id', $location_id);
		$this->db->where('user_id', $user_id);
		if($this->db->update(DBPREFIX.'_user_address', $value)){
			//echo $this->db->last_query();die();
			return TRUE;
		}else{
			return FALSE;
		}
	}
	public function add_addressbook($data, $user_id){
		$countId = $this->db->select("count(id) as id")->from(DBPREFIX.'_user_address')->where("user_id",$user_id)->get()->row();
		if($countId->id == 0){
			$data['use_address'] = "1";
		}
		$this->db->insert(DBPREFIX.'_user_address', $data);
	//	echo $this->db->last_query();die();
		if($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	public function get_addressbook_latest_record(){
		$this->db->select('id')
						 ->from(DBPREFIX.'_user_locations')
						 ->order_by('id', 'desc')
						 ->limit(1);
		$query = $this->db->get();
		if($query && $query->num_rows()>0)
		{
			return $query->result();
		} else {
			return array();			
		}
	}

	public function get_address($user_id,$location_id,$user_type="buyer"){
		if($location_id && $user_id){
			$this->db->select('*')
							->from(DBPREFIX.'_user_address')
							->where(array('id'=>$location_id,"user_id"=>$user_id,"user_type"=>$user_type))
							;
			$query = $this->db->get();
			//echo $this->db->last_query();die();
			if($query && $query->num_rows()>0)
			{
				return array("status"=>1,"data"=>$query->row());
			} else {
				return array("status"=>0,"msg"=>"Invalid Id");			
			}
		}else{
			return array("status"=>0,"msg"=>"User Id or Location Id missing!");
		}
	}
	public function getDefaultAddressAndCard($user_id=""){
		if($user_id){
			$data['card'] = $this->db->select("c.card_id,c.address_id, c.holder_name")->from(DBPREFIX."_user_cards c")->where(array("c.user_id"=>$user_id,"c.default"=>"1","save_card"=>"1"))->get()->row();
			$data['address'] = $this->db->select("a.id")->from(DBPREFIX."_user_address a")->where(array("a.user_id"=>$user_id,"a.use_address"=>"1"))->get()->row();
			return $data;
		}else{
			return array("status"=>0,"msg"=>"User id is missing!");
		}
	}
}	
?> 