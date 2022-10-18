<?php
class Secure_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database("default");
	}

	function checklogindetails($userid="",$username="",$password="",$isUser = false)
	{
		$this->db->select('u.id,u.email, u.firstname,u.middlename, u.lastname, u.social_id, u.userid, u.user_pic, u.user_type, ss.store_name, ss.s_id as store_id, ss.is_zabee, p.public_name,u.social_id,u.social_platform,u.is_active,u.is_deleted, u.email_verified,w.warehouse_id,w.warehouse_title, u.mobile,u.hubx_info');
		if($userid){
			if($isUser){
				$this->db->where(array("u.social_id"=>$userid));
				$this->db->join(DBPREFIX."_profiles p",'p.userid = u.userid','LEFT');
				$this->db->join(DBPREFIX."_seller_store ss",'ss.seller_id = u.userid','LEFT');
			}else{
				$this->db->where(array("u.userid"=>$userid));
				$this->db->join(DBPREFIX."_profiles p",'p.userid ="'.$userid.'"','LEFT');
				$this->db->join(DBPREFIX."_seller_store ss",'ss.seller_id ="'.$userid.'"','LEFT');
			}
		}else{
			$this->db->where(array("u.email"=>$username,"u.password"=>$password));
			$this->db->join(DBPREFIX."_profiles p",'p.userid = u.userid','LEFT');
			$this->db->join(DBPREFIX."_seller_store ss",'ss.seller_id = u.userid','LEFT');
		}
		$this->db->where(array("u.is_block"=>"0"));
		$this->db->join(DBPREFIX."_warehouse w",'IF(ss.is_zabee="1",w.user_id=1,"")','LEFT',FALSE);
		$this->db->limit(1);
		$this->db->from(DBPREFIX."_users u");
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}
		return FALSE;
	}

	function checkusertype($link,$cur_user)
	{
		$str_link = "";
		foreach($link as $eachlink){
			$str_link .= $eachlink."/";
		}
		if(isset($link[1]) && $link[1] =="seller" && isset($link[2])){
			$str_link = $link[2];
		}else{
			 $str_link = "";
		}
		$extrawhere = "";
		$sql = "SELECT * FROM ".DBPREFIX."_backend_usertype WHERE user_type_id = '".$cur_user[0]['user_type']."' ".$extrawhere;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0){
			$data = $query->result_array();
			if(in_array($str_link,explode(",",$data[0]["allowed_links"])) || $str_link == "login" || $str_link == "" || $data[0]["allowed_links"] == "*"){
				return array("display_name"=>$data[0]['user_type_dpname'],"allowedmodules"=>$data[0]['allowed_links']);
			}
		}
		return FALSE;
	}

	public function checkProductSell($created_id){
		$sql = "SELECT * FROM ".DBPREFIX."_orders WHERE `created_id` = '".$created_id."' && is_viewed='0'";
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0){
			return 1;
		}
		return FALSE;
	}

	function getLegalBusniessType(){
		$query = $this->db->select('*')->from('tbl_legal_busniess_type')->get();
		$result = $query->result();
		if ($result > 0){
			return $result;
		}
		return FALSE;
	}

	public function checkUserStore($seller_id){
		$query = $this->db->select('s.s_id')->from('tbl_seller_store s')->join('tbl_users u','s.seller_id=u.userid')->where(array('s.seller_id' => $seller_id))->get();
		$result = $query->num_rows();
		if ($result > 0) {
			return 1;
		}
		return FALSE;
	}

	public function checkMsgNotification($receiver_id){
		$result['rows'] =0;
		$result['result'] = "";
		$query =$this->db->select("m1.message_id,u.userid,m1.sent_datetime,m1.seen_datetime,CONCAT(u.`firstname`, ' ', u.lastname) AS sender_name,u.user_pic,m1.message, m1.product_variant_id,m1.item_type,m1.seller_id,m1.buyer_id,m1.item_id")
				 ->from('tbl_message m1')
				 ->join('tbl_users u','(m1.sender_id = u.userid OR m1.receiver_id = u.userid) AND u.userid !="'.$receiver_id.'"',null,false)
				 ->where('m1.receiver_id="'.$receiver_id.'" AND m1.`seen_datetime` IS NULL ',NULL,FALSE)
				 ->group_by('u.userid,m1.product_variant_id')->order_by('m1.message_id','desc')->limit(5)->get();
		// echo"<pre>". $this->db->last_query();die();
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $query->result();
		}
		return $result;
	}

	public function checkOrderNotification($receiver_id){
		$result['rows'] =0;
		$result['result'] = "";
		$query =$this->db->select("id,created,notification_message")
				 ->from('tbl_notifications')
				 ->where('notification_type = 3 AND to_id = "'.$receiver_id.'" AND active = 1 AND display_to=1',NULL,FALSE)->get();
		$val = $query->result();
		//print_r($val);die();
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $val;
		}
		return $result;
	}

	public function updateOrderNotification($buyer_id){
		$notify_id = $this->db->select("id")
					->from('tbl_notifications')
					->where('to_id = "'.$buyer_id.'" AND notification_type = 3 AND active = 1 AND display_to = 1',NULL,FALSE)->get()->result();
		$data = array('active' => 0);
		foreach($notify_id as $id){
		$this->db->where('id', $id->id);
		$this->db->update('tbl_notifications', $data);
		}
	}

	public function checkProductDetails($product_id,$country_id,$product_variant_id = ""){
		if($product_variant_id != ""){
			$this->db->where('pin.`product_variant_id`',$product_variant_id);
		}
		if($country_id !=""){
			$this->db->where('(pr.country_id = "'.$country_id.'" OR pr.country_id = "0")',NULL,false);
		}
		if($product_id !=""){
			$this->db->where('product.product_id' ,$product_id);
		}
		$this->db->select('sp.sp_id,ss.store_name,GROUP_CONCAT(DISTINCT sp.`sp_id`) AS seller_product_id,(COUNT(DISTINCT sp.`seller_id`) - 1) AS total_seller,pin.`price`,pin.`condition_id`, pin.`seller_id`,`product`.`product_id`,`c`.`category_id`,`c`.`category_name`,`b`.`brand_id`,`b`.`brand_name`,`product`.`upc_code`,`product`.`product_name`,`product`.`product_description`,`product`.`is_active`,pin.product_variant_id AS pv_id,pin.quantity ')
				->from('tbl_product AS product')
				->join('tbl_product_regions as pr','pr.`product_id` = product.product_id')
				->join('tbl_categories c','c.`category_id` = product.`sub_category_id`')
				->join('tbl_brands b','b.`brand_id` = product.`brand_id`')
				->join('tbl_seller_product sp','sp.product_id = product.product_id')
				->join('tbl_product_inventory pin','pin.`seller_product_id` = sp.`sp_id` AND pin.`quantity` > 0 ')
				->join('tbl_seller_store ss','ss.seller_id = pin.seller_id')
				->group_by('product.`product_id`')
				->order_by('sp.condition_id,pin.price');
		$product = $this->db->get();
		$data['productDataRows'] = $product->num_rows();
		$data['productData'] = $product->row();
		return $data;
 	}

	public function check_email_verification($data){
		if($data != "1"){
			$this->db->select('email_verified')
				 ->from("tbl_users")
				 ->where('userid', $data);
			$query = $this->db->get();
			//echo $this->db->last_query();die();
			if($query && $query->num_rows()>0){
				return $query->result();
			}else{
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function getUsernameViaEmail($email){
		$this->db->select('userid, firstname')
			 ->from("tbl_users")
			 ->where('email', $email);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function VerifyEmailByUserId($userid){
		$insert_data = array(
			'email_verified' => '1'
		);
		$this->db->where('userid', $userid)
				 ->update('tbl_users', $insert_data);
		if($this->db->affected_rows() > 0){
			return 'success';
		} else {
			$email_verified = $this->db->select('email_verified')->from("tbl_users")->where('userid', $userid)->get()->row();
			return $email_verified->email_verified;
		}
	}

	public function WishlistViaCategories($userid){
		$this->db->select('category_name, id')
			 ->from("tbl_wishlist_categoryname")
			 ->where(array('user_id'=>$userid, 'is_delete'=>'0'));
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	private function getLastWordNumber($userid, $word){
		$Present = array();
		$numbers = array();
		$largest = "";
		$this->db->select('category_name, id')
				->from("tbl_wishlist_categoryname")
				->where('user_id', $userid)
				->where('is_delete', "0")
				->like('category_name', $word, 'after');
		 $query = $this->db->get();
		 if($query && $query->num_rows()>0){
			$list_of_words = $query->result();
			for($i = 0; $i < count($list_of_words); $i++){
				$explodedResultOfList = explode('_',$list_of_words[$i]->category_name);
				array_push($Present, $explodedResultOfList);
			}
			for($i = 0; $i < count($Present); $i++){
				if(isset($Present[$i][1])){
					array_push($numbers, $Present[$i][1]);
				}
			}
			$largest = (isset($numbers[0])) ? $numbers[0]: "0" ;
			for($i = 0; $i < count($numbers); $i++){
				if($numbers[$i] > $largest){
					$largest = $numbers[$i];
				}
			}
			return $largest;
		} else {
			return $word;
		}
	}

	public function insertWishlistCategoryName($data){
		$resp1 = $this->db->insert('tbl_wishlist_categoryname', $data);
		if($resp1){
			return $this->db->insert_id();
		} else {
			return false;
		}
	}

	public function insertWishlist($data){
		$resp2 = $this->db->insert('tbl_wishlist', $data);
		if($resp2){
			return true;
		} else {
			return false;
		}
	}

	public function getForcedWishListName($userid,$cat_name=""){
		if($cat_name){
			$this->db->where("category_name",$cat_name);
		}
		$this->db->select('id, category_name')
		->from("tbl_wishlist_categoryname")
		->where('user_id', $userid)
		->where('is_delete','0')
		->order_by("id","DESC");
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows() > 0){
			return $query->row();
		} else {
			return false;
		}
	}

	public function wishlist_cat_add($data, $userid){
		$dataInserted = false; //will check if data was inserted in the input field or not.
		if(isset($data['cat_name']) &&  $data['cat_name'] != ""){ //if inserted, it checks if the word inserted exists in the db or not, if exists, it 	incremements an int at the end of that word.
			$word = $data['cat_name'];
			$WordValueToBe = $this->getLastWordNumber($userid, $word);
			if($WordValueToBe == $word){
				$data['cat_name'] = $WordValueToBe;
			} else {
				$WordValueToBe = $WordValueToBe + 1; //word is incremented here
				$data['cat_name'] = $word."_".$WordValueToBe;
			}
			$insert_data = array(
				'user_id' => $userid,
				'category_name' => $data['cat_name']
			);
			$resp1 = $this->insertWishlistCategoryName($insert_data);
			if($resp1){
				$dataInserted = true;
				return true;
			} else {
				return false;
			}
		} else if(isset($data['cat_name']) && $data['cat_name'] == ""){ //if not inserted, it checks if the default exists in the db or not, if exists, it incremements an int at the end of that default.
			$word = "like";
			$WordValueToBe = $this->getLastWordNumber($userid, $word);
			if($WordValueToBe == $word){
				$data['cat_name'] = $WordValueToBe;
			} else {
				$WordValueToBe = $WordValueToBe + 1; //default is incremented here
				$data['cat_name'] = $word."_".$WordValueToBe;
			}
			$insert_data = array(
				'user_id' => $userid,
				'category_name' => $data['cat_name']
			);
			$resp1 = $this->insertWishlistCategoryName($insert_data);
			if($resp1){
				$dataInserted = true;
				return true;
			} else {
				return false;
			}
		}
		if(!$dataInserted){
			$word = "like";
			if(!isset($data['alreadyExistingCategories'])){
				$resp1 = $this->getForcedWishListName($userid, $word);
				if($resp1){
					$resp1 = $resp1->id;
				} else {
					$insert_data = array(
						'user_id' => $userid,
						'category_name' => $word
					);
					$resp1 = $this->insertWishlistCategoryName($insert_data);
				}
				if($resp1){
					//$forcedInserted = $this->getForcedWishListName($userid, $word);
					$insert_data2 = array(
						'created_date' =>  date('Y-m-d H:i:s'),
						'category_id' => $resp1,
						'product_id' => $data['modal_product_id'],
						'pv_id' => $data['modal_product_v_id'],
						'user_id' => $userid,
					);
					$resp2 = $this->insertWishlist($insert_data2);
					if($resp2){
						return true;
					} else {
						return false;
					}

				} else {
					return false;
				}
			} else {
				$insert_data2 = array(
					'created_date' =>  date('Y-m-d H:i:s'),
					'category_id' => $data['alreadyExistingCategories'],
					'product_id' => $data['modal_product_id'],
					'pv_id' => $data['modal_product_v_id'],
					'user_id' => $userid,
				);
				$resp2 = $this->insertWishlist($insert_data2);
				if($resp2){
					return true;
				} else {
					return false;
				}
			}
		}
	}

	public function selectWishlistCategories($user_id){
		$this->db->distinct('category_id');
		$this->db->select('category_id')
			 ->from("tbl_wishlist")
			 ->where('user_id', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function selectWishlistCategoriesNames($cat_id, $user_id){
		$dummy = array();
		$cat_ids = json_decode(json_encode($cat_id), True);
		if($cat_ids == ""){
			return FALSE;
		} else {
			$count = count($cat_ids);
			for($i = 0; $i < $count; $i++){
				array_push($dummy, $cat_ids[$i]['category_id']);
			}
			$this->db->select('id, category_name')
				->from('tbl_wishlist_categoryname')
				->where('user_id', $user_id)
				->where_in('id', $dummy);
			$query = $this->db->get();
			if($query && $query->num_rows()>0){
				return $query->result();
			}else{
				return FALSE;
			}
		}
	}

	public function selectTableWishlistCategories($user_id){
		$this->db->select('id')
			->from('tbl_wishlist_categoryname')
			->where('user_id', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function ClearWishlistIfNoCategories($user_id){
		return FALSE;
		/*$tbl_wishlist_cat_id = $this->selectWishlistCategories($user_id);
		$tbl_wishlistcatname_id = $this->selectTableWishlistCategories($user_id);
		$notInTheTable = array();
		$found = false;
		if($tbl_wishlist_cat_id && $tbl_wishlistcatname_id){
			for($i = 0; $i < count($tbl_wishlist_cat_id); $i++){
				$temp = $tbl_wishlist_cat_id[$i]->category_id;
				$found = false;
				for($j = 0; $j < count($tbl_wishlistcatname_id); ++$j){
					if($tbl_wishlistcatname_id[$j]->id == $temp){
						$found = true;
					}
				}
				if(!$found) {
					array_push($notInTheTable, $temp);
					$notInTheTable = array_unique($notInTheTable);
				}
			}
		} else {
			if(!empty($tbl_wishlist_cat_id)){
				for($i = 0; $i < count($tbl_wishlist_cat_id); $i++){
					$temp = $tbl_wishlist_cat_id[$i]->category_id;
					array_push($notInTheTable, $temp);
					$notInTheTable = array_unique($notInTheTable);
				}
			}
		}
		if(!empty($notInTheTable)){
			$count = count($notInTheTable);
			if($count > 1){
				for($i = 0; $i < $count; $i++){
					$this->db->where("category_id", $notInTheTable[$i]);
					$this->db->delete("tbl_wishlist");
				}
			} else if($count > 1){
				$this->db->where("category_id", $notInTheTable[0]);
				$this->db->delete("tbl_wishlist");
			}
		}*/
	}

	public function getData($table_name, $select="*", $where="",$where_in="0",$group_by="",$order_by="",$join="",$limit="",$whereExtra="",$offset="",$anOtherWhere=""){
		if(!empty($where)){
			if($where_in !="0" && $where_in !=""){
				if($whereExtra !=""){
					$this->db->where_in($where_in,$where,FALSE,NULL);
				}else{
					$this->db->where_in($where_in,$where);
				}
			}else{
				$this->db->where($where);
			}
			if($anOtherWhere !=""){
				$this->db->where($anOtherWhere);
			}
		}
		if($group_by !=""){
			$this->db->group_by($group_by);
		}
		if($order_by !=""){
			$this->db->order_by($order_by);
		}
		if($limit !=""){
			if($offset){
				$this->db->limit($offset,$limit);
			}else{
				$this->db->limit($limit);
			}
		}
		if($join !=""){
			foreach($join as $key=>$value){
				$this->db->join($key,$value);
			}
		}
		$this->db->select($select)->from($table_name);
		$query = $this->db->get();
		$return = $query->result();
		return $return ;
	}

	public function AllWishlistData($product_id, $pv_id){
		$this->db->select('count(wish_id) as totalfav')
				->from('tbl_wishlist')
				->where('product_id', $product_id);
				// ->where('pv_id', $pv_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function getUserIdIfFromThirdParty($email){
		$this->db->select('userid')
			->from('tbl_users')
			->where('email', $email);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}
}
?>