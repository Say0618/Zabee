<?php
class Utilz_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
	public function countries($key = '', $value = '', $select = '*', $singleRow = false,$table="tbl_country"){
		$this->db->select($select)->from($table);
		if($key != '' && $value != ''){
			$this->db->where($key, $value);
		}
		$query = $this->db->get();
		$numRows = $query->num_rows();
		if($singleRow){
			$result = $query->row();
		} else {
			$result = $query->result(); 
		}
		return $result ;
	}

	public function getState($text){
		$this->db->select('*')
		 ->from('tbl_states');
		 $this->db->like('state',$text);
		 $query = $this->db->get();
		 $result = $query->result(); 
		return $result ;
	}

	
	public function curlRequest($params, $service_url, $isPost = false,$isFileUpload = true)
	{
		set_time_limit(3000000);
		try{
			$ch = curl_init();
			if(!$isPost){
				$url = $service_url.$params;
			} else {
				$url = $service_url;
				curl_setopt($ch, CURLOPT_POST, 1);
				if($isFileUpload){
					curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:multipart/form-data"));
					curl_setopt($ch, CURLOPT_INFILESIZE, $params['filesize']);
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			$response = curl_exec($ch);
			// echo"<pre>"; print_r($response); die();
			$response = json_decode(curl_exec($ch));
			if(curl_errno($ch)){
				$response = (object) array('status'=>0, 'message'=> 'ERROR', 'response'=>(object)array('resultCode'=>-201, 'reason'=>curl_error($ch)));
			}
			curl_close($ch);
		}
		catch (Exception $e) {
			$response = (object) array('status'=>0, 'message'=> 'ERROR', 'response'=>array('resultCode'=>-200, 'reason'=>'Invalid request'));
		}
		return $response;
	}

	public function send_mail($location,$products,$order_id,$email,$type,$subject,$text="",$name="", $cancel = "", $status = "")
	{
		$this->load->library('parser');	
		$data['location']=$location;
		$data['products']=$products;
		$data['order_id']=$order_id;
		$data['type']=$type;
		$data['text'] = $text;
		$data['name'] = ucfirst($name);
		$now = new DateTime();
		$data['created']=$now->format('Y-m-d H:i:s'); 
		$data['status']=$status; //FOR ORDER CANCEL REQUEST EMAIL
		$from = $this->config->item('info_email');
		$name = $this->config->item('author');
		$to = $email;
		$template = ($cancel == "buyer")?"front/emails/email_template_order_cancel":(($cancel == "seller")?"front/emails/email_template_order_cancel_seller":"front/emails/email_template_for_invoice");
		$body = $this->parser->parse($template, $data, TRUE);
		// print_r($body);
		$this->email_config($from, $name, $to, $subject, $body);
	}

	public function get_seller_email($seller_id)
	{
		$this->db->select('email, firstname')->from('tbl_users');
		$this->db->where('userid', $seller_id);
		$query = $this->db->get();
		$result = $query->row();
		return $result;
	}
	
	public function getWarehouseEmails($warehouse_id)
	{
		$this->db->select('user_id')->from('tbl_warehouse');
		$this->db->where('warehouse_id', $warehouse_id);
		$query = $this->db->get();
		if($query->num_rows > 0){
			$result = $query->row();
			$this->db->select('email')->from('tbl_users');
			$this->db->where('userid', $result->user_id);
			$query = $this->db->get();
			$result = $query->row();
			return $result->email;
		} else {
			return '';
		}
	}
	
	public function getMenu()
	{
		$query="SELECT * FROM tbl_menu ORDER BY menu_order ASC";
		$result = $this->db->query($query);
		if($result->num_rows() > 0){
			return $result->result();
		} else {
			return array();
		}
	}

	public function ordered_menu($array,$parent_id = 0)
	{
		$temp_array = array();
		foreach($array as $element){
			if($element->parent_id==$parent_id){
				$element->subs = $this->ordered_menu($array,$element->menu_id);
				$temp_array[] = $element;
			}
		}
		return $temp_array;
	}
	
	function html_ordered_menu($array,$parent_id = 0,$categoryData=array('numRows'=>0), $menu_class="navbar-nav")
	{
		if($menu_class != ''){
			$menu_class = ' class="'.$menu_class.'"';
		}
		$menu_html = '<ul class= "sm sm-blue" id="main-menu" '.$menu_class.'>';
		foreach($array as $element){
			if($element->parent_id==$parent_id){
				$menu_html .= '<li class="nav-item"><a class="nav-link" href="'.$element->menu_link.'">'.$element->menu_name.'</a>';
				if($this->hasSubMenu($array, $element->menu_id))
					$menu_html .= $this->html_ordered_menu($array,$element->menu_id, '');
				$menu_html .= '</li>';
			}
		}
		if($categoryData['numRows'] > 0){
			foreach($categoryData['result'] as $cd){
				$menu_html .= '<li class="nav-item"><a class="nav-link" href="'.base_url('product/SearchResults?&category_search=').$cd->category_id.'">'.$cd->category_name.'</a></li>';
			}
		}
		$menu_html .= '</ul>';
		return $menu_html;
	}
	private function hasSubMenu($array, $parent){
		foreach($array as $element){
			if($element->parent_id == $parent)
			return true;
		}
		return false;
	}

	function buildCategory($parent, $category , $class="dropdown-menu",$id="") {
		$html = "";
		if (isset($category['parent_cats'][$parent])) {
			$html .= "<ul class='".$class."' id='".$id."'>";	
			foreach ($category['parent_cats'][$parent] as $cat_id) {
				if($category['categories'][$cat_id]->category_link !=""){
					$url = base_url($category['categories'][$cat_id]->slug).$category['categories'][$cat_id]->category_link;
				}else{
					$url = base_url($category['categories'][$cat_id]->slug);
				}
				if (!isset($category['parent_cats'][$cat_id])) {
					$html .= "<li class='nav-item'><a  class='nav-link cat-hover' href='" . $url . "'>" . $category['categories'][$cat_id]->category_name . "</a></li>";
				} 
				if (isset($category['parent_cats'][$cat_id])) {
					$html .= '<li class="nav-item dropdown-submenu"><a class="nav-link cat-hover dropdown-t" href="' . $url .'">'. $category['categories'][$cat_id]->category_name . '<i class="fas fa-caret-right float-right"></i></a>';
					$html .= $this->buildCategory($cat_id, $category, "dropdown-menu","cat".$category['categories'][$cat_id]->category_id);
					$html .= "</li>";
				}
			}
			$html .= "</ul>\n";
		} 
		return $html;
	}

	public function getAllCategoriesChildId($category_id,$previous=""){
		$result = $this->db->select('GROUP_CONCAT(category_id) as category_ids')
						   ->from("(SELECT * FROM tbl_categories ORDER BY parent_category_id, category_id) products_sorted, (SELECT @pv := '$category_id') initialisation")
						   ->where('FIND_IN_SET(parent_category_id, @pv) AND LENGTH(@pv := CONCAT(@pv, ",", category_id))',NULL,FALSE)->get()->row();
		if($result->category_ids){
			$result = $category_id.",".$result->category_ids;
		}
		//echo $this->db->last_query(); die();
		/*$result = $this->db->select("GROUP_CONCAT(category_id) as category_ids")->from("tbl_categories")->where_in("parent_category_id",$category_id,FALSE)->get()->row();
		if($result->category_ids){
			$previous .= $result->category_ids.",";
			return $this->getAllCategoriesChildId($result->category_ids,$previous);
		}else{
			return rtrim($previous,",");
		}*/
		return $result;
	}

	public function getAllCategoriesParentId($category_id,$previous=array()){
		$result = $this->db->select("parent_category_id,category_name,category_id,slug")->from("tbl_categories")->where("category_id",$category_id,FALSE)->get()->row();
		if($result->parent_category_id){
			$previous [] = array("cat_name"=>$result->category_name,"cat_id"=>$result->category_id,"url"=>base_url($result->slug));
			return $this->getAllCategoriesParentId($result->parent_category_id,$previous);
		}else{
			$previous [] = array("cat_name"=>$result->category_name,"cat_id"=>$result->category_id,"url"=>base_url($result->slug));
			return $previous;
		}
	}

	public function createMenu($menu, $children, $wrap = 'li'){
		$html = '';
		foreach($menu as $item){
			switch($wrap){
				case 'li':
					$html .= '<li><a href="'.$item->menu_link.'"></a>';
					if(count($children[$item->menu_id])>0){
						$this->createMenu(array($item));
					}
					$html .= '</li>';
				break;
			}	
		}
		return $html;
	}

	public function get_children($parent){
		$this->db->select('*')->from('tbl_menu');
		$this->db->where('parent_id', $parent);
		$query = $this->db->get();
		return $result = $query->num_rows();
	}

	public function get_next_order(){
		$this->db->select_max('menu_order')->from('tbl_menu');
		$query = $this->db->get();
		$result = $query->row();
   		return $result;
	}

	public function getMedia($product_id){
		$this->db->select('thumbnail,iv_link,is_local,is_image,is_cover as is_primary')
		->from('tbl_product_media')
		->where('product_id',$product_id);
		$query = $this->db->get();
		$data= $query->row();
		return $data;
	}

	public function getAllData($table_name, $select="*", $where="",$where_in="0",$group_by="",$order_by="",$join="",$limit="",$whereExtra=""){
		$this->db->select($select)->from($table_name);
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
		}
		if($group_by !=""){
			$this->db->group_by($group_by);
		}
		if($order_by !=""){
			$this->db->order_by($order_by);
		}
		if($limit !=""){
			if(is_array($limit)){
				$this->db->limit($limit[0],$limit[1]);
			}else{
				$this->db->limit($limit);
			}
		}
		if($join !=""){
			foreach($join as $key=>$value){
				$this->db->join($key,$value);
			}
		}	
		$query = $this->db->get();
		//echo $this->db->last_query().'<br />';
		if($query->num_rows() > 0){
			$return = $query->result();
		} else {
			$return = array();
		}
		return $return;
	}

	public function contact_usData($data){
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$contact_us = array(
			'created_date' => $date_utc,
			'email' => $data['email'],
			'subject' => $data['subject'],
			'phone_number' => $data['phone_number'],
			'order_number' => $data['order_number'],
			'message' => $data['message'],
			'user_id' =>$data['user_id']
		);
		$this->db->insert('tbl_contact_us', $contact_us);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function getNotifications($userid, $usertype, $status = 'new', $isCount = true)
	{
		/*
			userid => signed in user
			usertype => 0 -> admin, 1->buyer, 2-> seller
			status => 0 -> new, 1 -> read
			isCount => true -> return only notification count, false -> return notifications
		*/
		$response = array();
		$select = ($isCount)?'count(*) as notifications':'*';
		$this->db->select($select)->from('tbl_notifications')
				 ->where(array(
					'to_id'=> $userid,
					'display_to'=> $usertype
				 ));
		$this->db->order_by("id",'desc');
		$this->db->limit('5');
		if($status != 'all'){
			$status = ($status == 'new')?0:1;
			($isCount)?$this->db->where('status', $status):'';
		}
		$q = $this->db->get();
		//echo $this->db->last_query();
		if($q->num_rows() > 0){
			$response = (!$isCount)?$q->result():$q->row();
		}
		return $response;
	}
	
	public function getTotalNotifications($userid, $usertype, $status = 'new', $isCount = true)
	{
		$response = array();
		$select = '*';
		$this->db->select($select)->from('tbl_notifications')
				 ->where(array(
					'to_id'=> $userid,
					'display_to'=> $usertype
				 ));
		$this->db->order_by("id",'desc');
		$q = $this->db->get();
		//echo $this->db->last_query();
		if($q->num_rows() > 0){
			$response = (!$isCount)?$q->result():$q->row();
		}
		return $response;
	}

	public function saveNotification($to_id, $from_id, $usertype, $notification_type, $message, $link = "", $active=0)
	{
		/*
			to_id => userid whom it will show
			from_id => userid who created this event
			usertype => 0 -> admin, 1->buyer, 2-> seller, 3->warehouse, 4->referral
			notification_type => 0->notification, 1-> warning, 2->promotion, 3->order_notification
			message => notification content
			link => link to go if click on notification(optional)
		*/
		$date = date('Y-m-d H:i:s');
		$data = array(
			'created' => $date,
			'updated' => $date,
			'to_id' => $to_id,
			'from_id' => $from_id,
			'display_to' => $usertype,
			'notification_type' => $notification_type,
			'notification_message' => $message,
			'notification_link' => $link,
			'active' => $active
		);
		if($this->db->insert('tbl_notifications', $data)){
			return true;
		} else {
			return false;
		}
	}
	
	public function getNotiFormatted($userid, $usertype, $status = '0', $isCount = false, $view = 'seller')
	{
		$views = array('buyer'=> 'notification_buyer', 'seller'=> 'notification_seller');
		$data = array(
			'actions'=> array('action', 'danger', 'primary'),
			'notifications' => $this->getNotifications($userid, $usertype, $status, $isCount),
			'totalNotifications' => $this->getTotalNotifications($userid, $usertype, $status, $isCount),
			'usertype' => $usertype
		);
		// echo"<pre>"; print_r($data); die();
		$html = $string = $this->load->view('templates/'.$views[$view], $data, TRUE);
		return $html;
	}
	
	public function updateNotificationStatus($userid, $usertype, $notification_id){
		$date = date('Y-m-d H:i:s');
		$data = array(
			'updated' => $date,
			'status' => 1
		);
		$this->db->where(array(
			'to_id'=> $userid,
			'display_to'=> $usertype,
			'id <= '=> $notification_id
		));
		if($this->db->update('tbl_notifications', $data)){
			return true;
		} else {
			return false;
		}
	}
	
	public function getCountrybyId($id){
		$this->db->from('tbl_country');
		$this->db->select('nicename');
		$this->db->where('id',$id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	public function getStatebyId($id){
		$this->db->from('tbl_states');
		$this->db->select('state');
		$this->db->where('id',$id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	public function getStoreId($storeName){
		$this->db->from('tbl_seller_store');
		$this->db->select('seller_id');
		$storeName = strtolower(str_replace(" ","-",$storeName));
		$this->db->where(array('store_id'=>$storeName, "is_approve"=>"1", "is_active"=>"1"));
		$query = $this->db->get();
		$result = $query->result_array();
		if($result){
			return $result[0]['seller_id'];
		}else{
			return false;
		}
	}

	public function email_config($from, $name, $to, $subject, $msg)
	{
		$this->load->library('email');
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'smtp.socketlabs.com',
			'smtp_port' => '2525',
			'smtp_user' => 'server13556',
			'smtp_pass' => 'n7P6FpEz42Ztq3XYk8s',
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'wordwrap' => TRUE
		);      
		$this->email->initialize($config);
		$this->email->set_newline("\r\n"); 
		$this->email->set_crlf( "\r\n" ); 			
		$this->email->from($from, $name);
		$this->email->to($to); 
		$this->email->subject($subject);
		$this->email->message($msg);	
		//if($this->email->send()){
			// print_r("done");
			return true;
		//}
		//print_r($msg);
		//print_r($this->email->print_debugger());
	}

	function apiLogin($userid="",$username="",$password="",$isUser = false,$media_url)
	{
		$this->db->select('u.email, u.firstname,u.lastname,u.user_type ,u.userid, IF(u.user_pic IS NOT NULL, CONCAT("'.$media_url.'",u.user_pic),"") as user_pic,IF(password = "", 0, 1) as password');
		if($userid){
			if($isUser){
				$this->db->where(array("u.social_id"=>$userid));
			}else{
				$this->db->where("u.userid",$userid);
			}
		}else{
			$this->db->where(array("u.email"=>$username,"u.password"=>$password));
		}
		$this->db->where(array("u.is_block"=>"0"));
		$this->db->limit(1);
		$this->db->from(DBPREFIX."_users u");
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if ($query->num_rows() > 0){
			return $query->row();
		}		
		return FALSE;
	}
	public function getSellerProductCount($seller_id,$brand_id="",$category_id="",$shipping_id = "",$range = ""){
		if($brand_id){
			$this->db->where("p.brand_id",$brand_id);
		}
		if($category_id){
			$this->db->where("p.sub_category_id",$category_id);
		}
		if($shipping_id){
			$this->db->join(DBPREFIX."_product_shipping ps","FIND_IN_SET(ps.shipping_id, sp.shipping_ids) AND ps.price = 0");
		}
		if($range){
			$this->db->where("pin.price BETWEEN ".$range[0]." AND ".$range[1],NULL,FALSE);
		}
		$count = $this->db->select("p.product_id")
				 		  ->from(DBPREFIX."_product p")
						  ->join(DBPREFIX."_seller_product sp","sp.product_id = p.product_id AND sp.approve = '1' AND sp.is_active = '1'") 
						  ->join(DBPREFIX."_product_inventory pin","pin.product_id = p.product_id AND pin.approve = '1' AND (pin.quantity - pin.sell_quantity) > '0' AND pin.seller_id = '".$seller_id."'")
						  ->join(DBPREFIX.'_product_category pc','pc.product_id = p.product_id')
						  ->where("p.is_active","1")
						  ->group_by("p.product_id")
						  ->get()->num_rows();
						//   echo"<pre>";print_r($this->db->last_query()); die();
		return $count;
	}
	public function getSellerProduct($seller_id,$brand_id="",$category_id="",$pageing="0",$limit="",$order="",$range="",$group_by="",$keywords="",$select="",$userid = ""){
		if($pageing > 1){
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		$this->db->query('SET SESSION group_concat_max_len = 1000000');
		if($brand_id){
			$this->db->where_in("product.brand_id",$brand_id,FALSE);
		}
		if($category_id){
			$this->db->where_in("product.sub_category_id",$category_id,FALSE);
		}
		if($group_by == ""){
			$group_by="product.product_id";
		}
		if($order == ""){
			$order = "sp.sp_id DESC";
		}
		if($range){
			$this->db->where("pin.price BETWEEN ".$range[0]." AND ".$range[1],NULL,FALSE);
		}
		$querySelect = "";
		if($userid){
			$this->db->join(DBPREFIX.'_wishlist as w',' w.product_id = product.product_id AND w.user_id="'.$userid.'"','LEFT');	
			$querySelect = ", if(w.wish_id,1,0) as already_saved";
		}
		if($select == ""){
			$select ='product.product_name,product.slug,product.product_id,pm.thumbnail,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id AS pv_id,AVG(preview.`rating`) AS rating,pin.price,d.value AS discount_value,d.type AS discount_type,d.valid_from AS valid_from,d.valid_to AS valid_to,sp.seller_id'.$querySelect;
		}else{
			$select = $select.$querySelect;
		}
		$this->db->select($select, false)
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"')
			->join(DBPREFIX.'_product_category pc','pc.product_id = product.product_id')
			->join(DBPREFIX."_categories AS cat",'cat.category_id = pc.category_id')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND pm.is_cover','LEFT') 
			->join(DBPREFIX.'_product_regions AS pr','pr.product_id = product.product_id','LEFT')
			->join(DBPREFIX.'_product_variant AS pv','pv.sp_id = sp.sp_id AND pv.`product_id` = product.`product_id` AND pv.`seller_id` = sp.`seller_id` AND pv.`condition_id` = sp.`condition_id`','LEFT')
			->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id')
			->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id`',"LEFT")
			->join(DBPREFIX.'_product_reviews AS preview','preview.product_id = pin.product_id AND preview.pv_id = pin.product_variant_id','LEFT')
			->group_by($group_by)
			->where('(pin.quantity - pin.sell_quantity) > "0"')
			->where('sp.approve','1')
			->where('sp.is_active','1')
			->where('pin.approve','1')
			// ->where('cat.is_private','0')
			// ->where('product.is_private','0')
			->where('product.is_active','1')
			->where('pin.seller_id',$seller_id)
			->where("pin.product_variant_id = (SELECT `pin`.`product_variant_id` AS `pv_id` FROM `tbl_product_inventory` AS `pin` WHERE `pin`.`product_id` = product.product_id  AND pin.quantity > '0' AND `pin`.`seller_id` = sp.seller_id AND `pin`.`approve` = '1' GROUP BY `pin`.`price` ORDER BY `pin`.`condition_id`,pin.price LIMIT 1)",NULL,FALSE)
			->order_by($order);
		$query = $this->db->get();
		// echo"<pre>". $this->db->last_query();die();
		$result=$query->result_array();
		// echo"<pre>". print_r($result);die();
		return $result;

	}
	public function getBrandAndCategory($seller_id = ""){
		$result["brands"] = $this->db->select("b.brand_id,b.brand_name")
									 ->from(DBPREFIX."_product as p")
									 ->join(DBPREFIX."_brands as b","p.brand_id=b.brand_id AND b.blocked='0'")
									 ->join(DBPREFIX."_product_inventory pin","pin.product_id = p.product_id AND pin.seller_id='".$seller_id."'")
									 ->group_by("b.brand_id")->get()->result();
									 
		$result["categories"] = $this->db->select("c.category_id,c.category_name")
										 ->from(DBPREFIX."_product as p")
										 ->join(DBPREFIX."_categories as c","p.sub_category_id=c.category_id")
										 ->join(DBPREFIX."_product_inventory pin","pin.product_id = p.product_id AND pin.seller_id='".$seller_id."'")
										 ->group_by("c.category_id")->get()->result();
		return $result;
	}
	public function getLowestShippingDataByPvid($pv_id,$single=1){
		// $shipping_id = $this->db->select("shipping_ids")->from(DBPREFIX."_seller_product")->where("sp_id =(SELECT sp_id FROM ".DBPREFIX."_product_variant pv WHERE pv_id =".$pv_id.")",NULL,FALSE)->get()->row();
		$shipping_id = $this->db->select("shipping_ids")->from(DBPREFIX."_product_inventory")->where("product_variant_id = '".$pv_id."'",NULL,FALSE)->get()->row();
		$this->db->select("shipping_id,title,price,duration, shipping_type, base_weight, weight_unit, base_length, base_width, base_depth, dimension_unit, incremental_price, incremental_unit, free_after, description")->from(DBPREFIX."_product_shipping")->where_in("shipping_id",$shipping_id->shipping_ids,FALSE)->order_by("price");
		if($single){
			$lowestShippingData = $this->db->limit(1)->get()->row();
		}else{
			$lowestShippingData = $this->db->get()->result();
		}
		// echo $this->db->last_query(); die();
		return $lowestShippingData;
	}
	public function getShippingDataByid($shipping_id,$single=1){
		// $shipping_id = $this->db->select("shipping_ids")->from(DBPREFIX."_seller_product")->where("sp_id =(SELECT sp_id FROM ".DBPREFIX."_product_variant pv WHERE pv_id =".$pv_id.")",NULL,FALSE)->get()->row();
		$this->db->select("shipping_id,title,price,duration, shipping_type, base_weight, weight_unit, base_length, base_width, base_depth, dimension_unit, incremental_price, incremental_unit, free_after, description")->from(DBPREFIX."_product_shipping")->where("shipping_id",$shipping_id);
		if($single){
			$lowestShippingData = $this->db->limit(1)->get()->row();
		}else{
			$lowestShippingData = $this->db->get()->result();
		}
		// echo $this->db->last_query(); die();
		return $lowestShippingData;
	}
	public function deals_signup($data){
		$this->db->insert('tbl_deal_subscription', $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function check_for_deals_email($email){
		$query = $this->db->get_where('tbl_deal_subscription',array('email_address'=>$email));
		if(empty(($query->row_array()))){
			return true;
		}else{
			return false;
		}
	}
	public function check_for_deals_phone($phone){
		$query = $this->db->get_where('tbl_deal_subscription',array('phone_number'=>$phone));
		if(empty(($query->row_array()))){
			return true;
		}else{
			return false;
		}
	}
	public function getShowCategory(){
		$query = $this->db->select("category_id,category_name")->from("tbl_categories")->where("show_homepage","1")->get()->result();
		// echo $this->db->last_query();
		return $query;
	}
public function getDealSignups($isCount = false, $limit = 0, $offset = 0)
	{
		$response = array();
		$select = ($isCount)?'count(*) as signups':'ds.*,CONCAT(\'[\',GROUP_CONCAT(DISTINCT CONCAT(\'{"category_id":"\', c.category_id, \'", "category_name":"\', c.category_name, \'"}\')),\']\') AS categories';
		$this->db->select($select)->from('tbl_deal_subscription as ds');
		if(!$isCount){
			$this->db->join('tbl_categories as c', 'FIND_IN_SET(c.category_id ,ds.cat_ids)', 'left', false)->group_by('ds.id');
		}
		$this->db->order_by("id",'desc');
		if($limit > 0){
			$this->db->limit($limit, $offset);
		}
		$q = $this->db->get();
		if($q->num_rows() > 0){
			$response = (!$isCount)?$q->result():$q->row();
		}
		return $response;
	}
	public function getShipping($shipping_id = ''){
		if($shipping_id){
			$select = '*';
			$this->db->where('shipping_id', $shipping_id);
		} else {
			$select = 'shipping_id,title,price,duration, shipping_type, base_weight, weight_unit, base_length, base_width, base_depth, dimension_unit, incremental_price, incremental_unit, free_after, description';
		}
		$this->db->select($select)->from(DBPREFIX.'_product_shipping');
		
		$result = $this->db->get();
		if($result->num_rows() > 0){
			return $result->result();
		} else {
			return array();
		}
	}
	
	function getUserDevices($user_id, $user_type)
	{
		
		/*{"notification": {"body": "this is a body","title": "this is a title"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "id": "1", "status": "done"}, "to": "<FCM TOKEN>"}*/
	}
	function sendFCMNotifications($notification_title, $notification_body, $devices)
	{
		//echo '<pre>';print_r($devices);die();
		//API URL of FCM
		$url = 'https://fcm.googleapis.com/fcm/send';

		/*api_key available in:
		Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key*/    
		//$api_key = 'AAAAncmG3-I:APA91bHQU7jHzezowXkrnVbJrJKSm6nRUEtzl56lwES-stHqH8jzrhIOXfZr_BMDftIJZ874A0g4MThERuDzyYz7_h5Kcnfyw_Y89INWFqN5Dkm9oE8RZS50cyyfJk_ygjsLBWyu2z0k';
		$api_key = 'AAAAWrjWte0:APA91bGWF4tfnQjIb6bFpH4PaRxdKTBlldjh8rGK445nIuY5osI0PA-XMNVCdCVPHjRR9YKLcq8alsdl6ODmJVOqUjgXPdHEau4gtUPXikTurW1w14W-SHw7JU4nbWCnuEfwfH8EWqLe';
		
		$notification = array (
			'registration_ids' => array (
					$devices
			),
			'data' => array (
					"message" => $notification_body
			)
		);
		
		$headers = array(
			'Content-Type:application/json',
			'Authorization:key='.$api_key
		);
		/*foreach($devices as $device){
			$notification = array(
				"notification"=> array (
					"body"=> $notification_body, 
				),
				"priority"=> "high",
				"data"=> array(
					"click_action"=> "FLUTTER_NOTIFICATION_CLICK",
					"id"=> "1",
					"status"=> "done"
				),
				"to"=> $device->fmc_token
			);*/
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
			$result = curl_exec($ch);
			if ($result === FALSE) {
				die('FCM Send Error: ' . curl_error($ch));
			}
			curl_close($ch);
			//echo '<pre>';print_r($result);
		//}
		//die();
		return $result;
		
	}
	
	public function getDeviceColumn($user_devices, $column)
	{
		$devices = array();
		
		foreach($user_devices as $device){
			array_push($devices, $device->$column);
			
		}
		return $devices;
	}
	//Chat Bot Api Functions
	public function get_order_list_for_chatbot($user_id,$order_id){
		$query = $this->db->select(" p.product_name,ss.store_name,td.status,td.cancellation_pending,td.is_cancel,td.item_received_confirmation,td.created")
				 ->from(DBPREFIX."_transaction_details td")
				 ->join(DBPREFIX."_product p","p.product_id = td.product_id")
				 ->join(DBPREFIX."_seller_store ss","ss.seller_id = td.seller_id")
				 ->where(array("td.user_id"=>$user_id,"td.order_id"=>$order_id))
				 ->get()->result();
		return $query;
	}
	//Inventory Warning Email Code
	public function getInventories(){
		$where = array("pin.is_warning"=>"0","pin.approve"=>"1","(pin.quantity - pin.sell_quantity) >"=>"0");
		$result = $this->db->select("pin.created_date,pin.updated_date,pin.inventory_id,pin.seller_id,pin.product_name, pin.price, (pin.quantity - pin.sell_quantity) AS quantity,ss.contact_email,ss.store_name")
		->from(DBPREFIX."_product_inventory pin")
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pin.seller_id AND ss.is_active = "1"	AND ss.is_approve = "1"')
		->where($where)->limit(50)->order_by("pin.seller_id","DESC")->get()->result();
		//echo $this->db->last_query();die();
		$seller_data = array();
		foreach($result as $data){
			$now = time(); // or your date as well
			$date = ($data->updated_date)?$data->updated_date:$data->created_date;
			$your_date = strtotime($date);
			$datediff = $now - $your_date;
			$expireData = date("d-M-Y",strtotime('1 week'));
			$days = round($datediff / (60 * 60 * 24));
			if($days >= 21){
				if(isset($seller_data[$data->seller_id])){
					$seller_data[$data->seller_id][] = $data;
				}else{
					$seller_data[$data->seller_id] = array();
					$seller_data[$data->seller_id][] = $data;
				}
			}
		}
		$this->sendWarnings($seller_data,$expireData);die();
		return $result;
	}
	public function sendWarnings($data,$expireData){
		$date = date('Y-m-d H:i:s');
		$updateData = array("is_warning"=>"1","updated_date"=>$date);
		foreach($data as $d){
			$return = $this->warning_mail($d,$expireData);
			foreach($d as $c){
				$this->updateInventoryWarning($c,$updateData);
			}
		}
	}
	public function warning_mail($data,$expireData){
		$this->load->library('parser');	
		$parse['data'] = $data;
		$parse['expire_date'] = $expireData;
		$now = new DateTime();
		$from = $this->config->item('info_email');
		$name = $this->config->item('author');
		$subject = "Zab.ee Inventory Expire Warning";
		$to = "qa.kaygees@gmail.com";//"devwarlock@gmail.com";//$data[0]->contact_email;
		$template = "front/emails/email_inventory_warning";
		$body = $this->parser->parse($template, $parse, TRUE);
		$this->email_config($from, $name, $to, $subject, $body);
	}
	public function updateInventoryWarning($data,$updateData){
		$where = array("inventory_id"=>$data->inventory_id);
		$this->db->where($where);
		$this->db->update(DBPREFIX."_product_inventory",$updateData);
	}
	public function getWarnedInventories(){
		$where = array("pin.is_warning"=>"1","pin.approve"=>"1","(pin.quantity - pin.sell_quantity) >"=>"0");
		$result = $this->db->select("pin.created_date,pin.updated_date,pin.inventory_id,pin.seller_id,pin.product_name, pin.price, (pin.quantity - pin.sell_quantity) AS quantity,ss.contact_email,ss.store_name")
		->from(DBPREFIX."_product_inventory pin")
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pin.seller_id AND ss.is_active = "1"	AND ss.is_approve = "1"')
		->where($where)->limit(50)->order_by("pin.seller_id","DESC")->get()->result();
		//echo $this->db->last_query();die();
		$updateData = array("approve"=>"3");
		foreach($result as $data){
			$now = time(); // or your date as well
			$date = ($data->updated_date)?$data->updated_date:$data->created_date;
			$your_date = strtotime($date);
			$datediff = $now - $your_date;
			$days = round($datediff / (60 * 60 * 24));
			if($days >= 7){
				$this->updateInventoryWarning($data,$updateData);
			}
		}
		return $result;
	}
	//Delete Hubx Product if no updates in a month
	public function checkHubxProductInventory(){
		$where = array("pin.is_warning"=>"1","pin.approve"=>"1","(pin.quantity - pin.sell_quantity) >"=>"0");
		$result = $this->db->select("pin.created_date,pin.updated_date,pin.inventory_id,pin.seller_id,pin.product_name, pin.price, (pin.quantity - pin.sell_quantity) AS quantity,ss.contact_email,ss.store_name")
		->from(DBPREFIX."_product_inventory pin")
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pin.seller_id AND ss.is_active = "1"	AND ss.is_approve = "1"')
		->where($where)->limit(50)->order_by("pin.seller_id","DESC")->get()->result();
		//echo $this->db->last_query();die();
		$updateData = array("approve"=>"3");
		foreach($result as $data){
			$now = time(); // or your date as well
			$date = ($data->updated_date)?$data->updated_date:$data->created_date;
			$your_date = strtotime($date);
			$datediff = $now - $your_date;
			$days = round($datediff / (60 * 60 * 24));
			if($days >= 7){
				$this->updateInventoryWarning($data,$updateData);
			}
		}
		return $result;
	}
}
?>
