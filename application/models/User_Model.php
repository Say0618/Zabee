<?php
class User_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
		$this->load->model("Referral_Model");
	}
	
	function checkUserId($user_id, $single = false){
		$this->db->where("userid",$user_id);
		$result = $this->db->get(DBPREFIX."_users");
		if($result && $result->num_rows()>0){
			if($single){
				return $result->row();
			}else{
				return TRUE;
			}
		}else{
			return FALSE;			
		}
	}
	
	function addNewUser($data, $thirdParty = "", $referral = ""){
		if($thirdParty != ""){
			$data['email_verified'] = '1';
		}
		if($this->db->insert(DBPREFIX."_users",$data)){
			if($referral != ""){
				$this->Referral_Model->referralValidate($referral);
			}
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function getUserByID($user_id, $single = false, $email = ''){
		$this->db->where("social_id",$user_id);
		if($email != ''){
			$this->db->or_where('email', $email);
		}
		$result = $this->db->get(DBPREFIX."_users");
		if($result && $result->num_rows()>0){
			if($email){
				$this->db->where("email",$email);
				$this->db->update("tbl_users",array("social_id"=>$user_id));
			}
			if($single){
				return $result->row();
			} else {
				return $result->result_array();
			}
		}else{
			return FALSE;			
		}
	}
	
	function getUserByEmail($email_id){
		$this->db->where("email",$email_id);
		$result = $this->db->get(DBPREFIX."_users");
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}else{
			return FALSE;			
		}
	}

	public function addUser($user_id,$data)
	{
		$insert_data = array(
					'firstname' => $data['firstname'],
					'lastname'  => $data['lastname'],
					'mobile' => $data['contact_no'],
					'user_pic' => $data['img'],
				);
		$this->db->where('userid', $user_id);
		$this->db->update(DBPREFIX."_users",$insert_data);
		if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function updateUser($user_id,$data){
		if(isset($data['password'])){
			$insert_data = $data;
		}else{
		$insert_data = array(
				'userid'    => $user_id,
				'firstname' => $data['firstname'],
				'lastname'  => $data['lastname'],
				//  'mobile' => $data['contact_no']
			
			);
		}
		if(isset($data['img']) && $data['img'] != ''){
			$insert_data['user_pic'] = $data['img'];
		}
		$this->db->where('userid', $user_id);
		if($this->db->update(DBPREFIX."_users",$insert_data)){
		//if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function getUserForAcc($s_id,$select= ""){
		if($select == ""){
			$select = "id,firstname,lastname,email,password,mobile,user_pic";
		}
		$q = $this->db->select($select)
					  ->where('userid',$s_id)
					  ->get(DBPREFIX."_users");
		return $q->row();
	}

	public function getUserName($id){
		$q = $this->db->select('firstname,lastname')
					  ->where('userid',$id)
					  ->get(DBPREFIX."_users");
		return $q->row();
	}

	public function getHomeProduct($column="is_featured",$region="",$select="",$userid){
		if($region !=""){
			$this->db->where('(pr.country_id='.$region.' OR pr.country_id=0)',NULL,FALSE);
		}
		$where = array('sp.approve'=>'1','sp.is_active'=>'1','product.is_private'=>'0');
		if($select == ""){
			$select = "product.product_id, sp.sp_id, product.product_name,product.slug, pm.is_image, pm.is_cover as is_primary, product.is_featured,sp.is_banner,pm.is_local,pin.product_variant_id as pv_id,pin.price,d.value as discount_value,d.type as discount_type,d.valid_from as valid_from, d.valid_to as valid_to,pm.iv_link AS product_image,pm.`thumbnail` AS product_image_primary,  pm.`thumbnail` AS thumbnail,AVG(preview.`rating`) AS rating,sp.seller_id";
		}
		if($userid != ""){
			$select = "product.product_id, sp.sp_id, product.product_name,,product.slug, pm.is_image, pm.is_cover as is_primary, product.is_featured,sp.is_banner,pm.is_local,pin.product_variant_id as pv_id,pin.price,d.value as discount_value,d.type as discount_type,d.valid_from as valid_from, d.valid_to as valid_to,pm.iv_link AS product_image,pm.`thumbnail` AS product_image_primary,  pm.`thumbnail` AS thumbnail,AVG(preview.`rating`) AS rating,sp.seller_id, IF(w.wish_id, 1,0) AS already_saved";
		}
		$this->db->select($select)
				 ->from(DBPREFIX."_product as product")
				 ->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
				// ->join(DBPREFIX."_product_variant AS pv ",'sp.sp_id = pv.sp_id')
				 //->join(DBPREFIX."_variant AS v",'(FIND_IN_SET(v.v_id, pv.variant_group) > 0)','LEFT')
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"')
				 ->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
				 ->join(DBPREFIX."_categories AS cat",'cat.category_id = pro_cat.category_id AND `cat`.`is_active` = "1"')
				 //->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')   
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id','LEFT')   
				 ->join(DBPREFIX.'_product_regions AS pr','pr.product_id = product.product_id','LEFT')
				 ->join(DBPREFIX.'_product_inventory as pin', 'pin.`seller_product_id` = sp.`sp_id` AND (pin.quantity - pin.sell_quantity) > "0"')
				 ->join(DBPREFIX."_seller_store ss", "ss.seller_id = pin.seller_id AND ss.is_approve = '1' AND ss.is_active = '1'")
				 ->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id` AND d.display_status="1"',"LEFT")
				 ->join(DBPREFIX.'_product_reviews AS preview','preview.product_id = pin.product_id AND preview.seller_id = pin.seller_id','LEFT')
				 ->group_by('product.product_id')->where($column,'1')->where($where)->order_by('sp.updated_date','DESC');
			if($userid != ""){
				$this->db->join(DBPREFIX.'_wishlist as w',' w.product_id = product.product_id AND w.user_id="'.$userid.'"','LEFT');	
			}
			$this->db->limit(8);
			$query = $this->db->get();
			// echo"<pre>"; print_r($this->db->last_query()); die();
		if($query && $query->num_rows()>0){
			$data['rows'] = $query->num_rows();
			$data['result']=$query->result_array();
			return $data;
		}else{
			return FALSE;			
		}
	}

	public function saveData($data,$table){
		if($this->db->insert($table,$data)){
			return $this->db->insert_id();
		}else{
			return "false";
		}
	}

	public function updateData($data,$table,$where){
		$this->db->where($where);
		if($this->db->update($table,$data)){
			return "true";
		}else{
			return "false";
		}
	}

	public function Contact_us($data, $user_id){
		$insert_data = array(
			'created' => date('Y-m-d H:i:s'),
			'userid' => $user_id,
			'name' => $data['name'],
			'email' => $data['email'],
			'subject' => $data['subject'],
			'message' => $data['message'],
			'active' => 1
			);
		$this->db->insert('tbl_contactus', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}		
	}

	function getPassword($email, $data){
		$this->db->where('email',$email);
		$this->db->where('password',$data);
		$result = $this->db->get(DBPREFIX."_users");
		if($this->db->affected_rows() > 0){
			return TRUE;
	   	}else{
			return FALSE;
	 	}	 
	}

	function update_password($email, $data){
		$this->db->where('email',$email);
		$this->db->update(DBPREFIX."_users",$data);
		if($this->db->affected_rows() > 0){
		 	return TRUE;
		}else{
		 	return FALSE;
		}
	}

	public function set_password($el, $data){
		$this->db->where("social_id",$el);
		if($this->db->update(DBPREFIX."_users",$data)){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function getUserBySocialid($el){
		$this->db->where("social_id",$el);
		$result = $this->db->get(DBPREFIX."_users");
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}else{
			return FALSE;			
		}
	}

	function keywordSearch($keyword,$ip,$referer_url,$user_id,$type, $platform = 'W', $meta_info = '-', $os="", $session_id=""){ 
		/*
			access_platform = $platform: A:Android, W:Web, I:Iphone (app if A and I)
			os_platform = $os: Windows, Mac, Linux etc
			$meta_info: User agent
		*/
		$today = date('Y-m-d H:i:s');
		$data = array(
			'date_time'=>$today,
			'ip_address'=>$ip,
			'user_id'=>$user_id,
			'keyword'=>$keyword,
			'referer_url'=>$referer_url,
			'access_platform'=>$platform,
			'os_platform'=>$os,
			'meta_info'=>$meta_info,
			'session_id'=>$session_id,
			'type'=>$type
		);
		if($type == 'product'){
			$data['pv_id'] = $keyword;
		}
		$this->db->insert(DBPREFIX.'_search_keyword',$data);
	}

	public function newsandoffer_add($data){
		$insert_data = array(
			'created_at' => date('Y-m-d H:i:s'),
			'email_address' => $data,
			'active' => 1
			);
		$this->db->insert('tbl_newsletter', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}		
	}

	public function check_for_email($email)
	{
		$query = $this->db->get_where('tbl_newsletter',array('email_address'=>$email));
		$row = $query->row_array();
		if(empty($row)){
			return true;
		}else{
			return false;
		}
	}

	public function validate_email($post_data)
	{
		if (!preg_match("/^[a-zA-Z._-]+[a-zA-Z0-9._-]*@[a-zA-Z]+[a-zA-Z0-9]+\.[a-zA-Z]{2,6}$/", $post_data)){
			return FALSE;
		}else{
			return TRUE;
		}
	}

	public function save_for_later($data){
		if($this->db->insert('tbl_wishlist', $data)){
			return true;
		}else{
			return false;
		}
	}

	function record_passwords($data, $email = ""){
		if($email == "" || isset($data['thirdParty'])){
				$insert_data = array(
					'created_date' => date('Y-m-d H:i:s'),
					'updated_date' => NULL,
					'userid' => $data['userid'],
					'password' => $data['password'],
				);
			$this->db->insert('tbl_password_change', $insert_data);
			if($this->db->affected_rows() > 0){
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			$getCreatedDate = $this->getCreatedDate($email);
			$user_id = $this->getUseridFromEmailFromUserTable($email);
			$insert_data = array(
				'created_date' => $getCreatedDate[0]->created_date,
				'updated_date' => date('Y-m-d H:i:s'),
				'userid' => $user_id[0]->userid,
				'password' => $data['password'],
			);
			$this->db->insert('tbl_password_change', $insert_data);
			if($this->db->affected_rows() > 0){
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
	
	function getCreatedDate($email){
		$user_id = $this->getUseridFromEmailFromUserTable($email);
		$this->db->select("created_date")->from('tbl_password_change')->where('userid', $user_id[0]->userid);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	
	function getUseridFromEmailFromUserTable($email){
		$this->db->select("userid")->from('tbl_users')->where('email', $email);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	
	function getUserIdBySocialid($el){
		$this->db->select("userid")->from('tbl_users')->where('social_id', $el);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	
	function getUserEmailBySocialid($el){
		$this->db->select("email")->from('tbl_users')->where('social_id', $el);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	
	public function check_save_for_later($data){
		$this->db->select("*")->from('tbl_wishlist')->where('user_id', $data['user_id'])->where('pv_id', $data['product_variant_id'])->where('product_id', $data['product_id']);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return FALSE;
		}else{
			return TRUE;			
		}		
	}
	
	public function addDefaultWishlistForNewUser($userid){
		$insert_data = array(
			'user_id' => $userid,
			'category_name' => "like",
		);
		$this->db->insert('tbl_wishlist_categoryname', $insert_data);
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getSignups()
	{
		$this->db->select("*")->from('tbl_newsletter')->where('active', 2);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return array()	;			
		}		
	}

	public function getStripeCustomerID($user_id)
	{
		$this->db->select("customer_id_st")->from('tbl_users')->where('userid', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			$row = $query->row();
			return $row->customer_id_st;
		}else{
			return '';			
		}		
	}
	
	public function addStripeCustomerID($stripe_customer_id, $user_id)
	{
		$update_data = array('customer_id_st' => $stripe_customer_id);
		$this->db->where('userid', $user_id);
		$this->db->update(DBPREFIX."_users",$update_data);
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function addStripeCustomerCard($customer,$card, $card_name, $user_id,$address_id,$save_card="0"){
		$countId = $this->db->select("count(id) as id")->from(DBPREFIX.'_user_cards')->where(array("user_id"=>$user_id,"save_card"=>"1"))->get()->row();
		$today = date('Y-m-d H:i:s');
		$insert_data = array(
			'created' => $today,
			'updated' => $today,
			'user_id' => $user_id,
			'customer_id' => $customer,
			'card_id' => $card['id'],
			'holder_name' => $card['name'],
			'card_number' => $card['last4'],
			'card_type' => $card['brand'],
			'expiry_year' => $card['exp_year'],
			'expiry_month' => $card['exp_month'],
			'card_country' => $card['country'],
			'card_name' => $card_name,
			'address_id' => $address_id,
			'save_card' =>$save_card,
			'active' => 1
		);
		if($countId->id == 0){
			$insert_data['default'] = "1";
		}
		$this->db->insert('tbl_user_cards', $insert_data);
		if($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		} else {
			return FALSE;
		}
	}

	public function getStripeCards($user_id, $card_id = 0, $limit = 0, $offset = 0, $all =  false, $showUnsaved = false)
	{
		$response = array('status'=> 0, 'message'=> 'Error', 'code'=>'000', 'data'=>array());
		if($user_id == '')
			return $response;
		$where = array('c.user_id'=>$user_id, 'c.active'=> 1);
		if($card_id > 0){
			$where['c.id'] = $card_id;
		}
		if($all){
			$this->db->select("count(c.id) as rows");
		} else {
			$this->db->select("c.*");
		}
		$this->db->select("c.*,a.address_1,a.country,a.state,a.city,a.zip")->from(DBPREFIX.'_user_cards c')->where($where)->join(DBPREFIX."_user_address a","a.id = c.address_id","LEFT");
		$this->db->order_by('default,id','DESC');
		if(!$showUnsaved){
			$this->db->where('c.save_card', 1);
		}
		if($limit > 0){
			$this->db->limit($limit,$offset);
		}
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0)
		{
			$rows = $query->num_rows();
			$response['status'] = 1;
			$response['message'] = $rows.' cards found';
			$response['count'] = $rows;
			$response['data'] = $query->result();
		}
		return $response;		
	}
	
	public function deleteCard($user_id,$card_id)
	{
		$data = array('save_card' => 0);
		$this->db->where(array('user_id'=> $user_id, 'id'=>$card_id));
		$this->db->update("tbl_user_cards",$data);
		if($this->db->affected_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function makeDefaultCard($data, $data2, $user_id, $card_id){
		$this->db->where(array('default'=> "1", 'user_id'=>$user_id))
				 ->update(DBPREFIX.'_user_cards', $data2);
		$this->db->where('id', $card_id)
				 ->where('user_id', $user_id)
				 ->update(DBPREFIX.'_user_cards', $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function apiStripeCards($user_id, $card_id = 0, $limit = 0, $offset = 0, $unsaved = 0, $isApi = false){
		$response = array();//array('status'=> 0, 'message'=> 'Error', 'code'=>'000');
		$save_card = 1;
		if($user_id == ''){
			return $response;
		}
		if($unsaved == 1){
			$save_card = 0;	
		}
		$where = array('c.user_id'=>$user_id, 'c.active'=> 1,'c.save_card'=>$save_card);
		if($card_id){
			$this->db->where("c.id",$card_id);
		}
		$select = ($isApi)?"c.id,c.address_id,c.holder_name,c.card_number,c.expiry_month,c.expiry_year,c.card_name,c.card_type,c.default,a.address_1 as address,c.save_card, c.card_id, c.customer_id":"c.id,c.address_id,c.holder_name,c.card_number,c.expiry_month,c.expiry_year,c.card_name,c.card_type,c.default,a.address_1 as address,c.save_card";
		$this->db->select($select)->from(DBPREFIX.'_user_cards c')->join(DBPREFIX."_user_address a","a.id = c.address_id","LEFT");
		$this->db->order_by('c.default,c.id','DESC');
		$this->db->where($where);
		if($limit > 0){
			$this->db->limit($limit,$offset);
		}
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			$response = $query->result();
		}
		return $response;		
	}
	public function apiGetDefaultAddressAndCard($user_id=""){
		$data = array();
		if($user_id){
			$card = $this->db->select("c.id,c.holder_name,c.card_name,c.card_number,c.expiry_month,c.expiry_year,c.address_id,a.address_1 as address")
			->from(DBPREFIX."_user_cards c")
			->join(DBPREFIX."_user_address a", "a.id = c.address_id")
			->where(array("c.user_id"=>$user_id,"c.default"=>"1","save_card"=>"1"))->get()->row();
			$address = $this->db->select("a.id as address_id,a.address_1 as address,a.address_2,a.country,a.state,a.city,a.zip,a.contact as phone_number,a.fullname")->from(DBPREFIX."_user_address a")->where(array("a.user_id"=>$user_id,"a.use_address"=>"1"))->get()->row();
			if($address){
				$data['address'] = $address;
			}
			if($card){
				$data['card'] = $card;
			}
			if(empty($data)){
				$data = (object) array();
			}
			return $data;
		}else{
			return array("status"=>0,"msg"=>"User id is missing!");
		}
	}

	public function searched_query($data){
		$search = $this->db->select("*")->from(DBPREFIX."_search_query")->where(array("userid" => $data['userid'], "query" => $data['query']))->get()->num_rows();
		if($search > 0){
			return array("title" => "Warning", "msg" => "This query is already requested by you", "class" => "text-danger");
		}else{
			$this->db->insert(DBPREFIX."_search_query", $data);
			if($this->db->insert_id()){
				return array("title" => "Success", "msg" => "Request is submitted successfully", "class" => "text-success");
			}else{
				return array("title" => "Warning", "msg" => "Unable to process your request right now", "class" => "text-danger");
			}
		}
	}

	public function deleteUser($user_id)
	{
		$response = array('status'=> 0, 'message'=> 'Error', 'code'=>'000');
		$user = $this->getUserForAcc($user_id, '*');
		if($this->db->insert('tbl_users_deleted', $user)){
			$time = time();
			$email = 'delete_'.$time.'_'.$user->email;
			$is_deleted = 1;
			$update_data = array(
				"email"=>$email,
				"is_deleted" => "1",
				"social_id" => "",
				"social_platform" => "",
				"social_info" => "",
				"customer_id_st" => ""
			);
			
			if($this->db->where('userid', $user_id)->update("tbl_users",$update_data)){
				$response['status'] = 1;
				$response['message'] = "OK";
			} else {
				$response['code'] = "001";
			}
		} else {
			$response['code'] = "002";
		}
		return $response;
	}
}
?>