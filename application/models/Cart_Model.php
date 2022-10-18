<?php
class Cart_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");	
	}
	
	function checkcartid($user_id = "")
	{
		$this->db->where("user_id",$user_id);
		$result = $this->db->get(DBPREFIX."_tmpcart");
		if($result && $result->num_rows())
		{
			return true;		
		}
		return false;
	} 
	
	function addtoCart($cartData,$user_id="")
	{
		if($user_id == ""){return "";}
		if($cartData != ""){
			$cartData = serialize($cartData);
		}
		$time = gmdate("Y-m-d\TH:i:s");
		$time = str_replace("T"," ",$time);	
		$cartData = array("created_date"=>$time,"cart_data"=>$cartData,'user_id'=>$user_id);
		$this->db->insert(DBPREFIX."_tmpcart",$cartData);
		// $this->db->last_query();die();
		return $this->db->insert_id();
	}
	
	function updateCart($user_id,$cartData)
	{
		$time = gmdate("Y-m-d\TH:i:s");
		$time = str_replace("T"," ",$time);	
		if($user_id == "")return;
		if($cartData != "")
		{
			$cartData = serialize($cartData);
		}
		$updateData = array("updated_date"=>$time,"cart_data"=>$cartData);
		$this->db->where("user_id",$user_id);		
		$this->db->update(DBPREFIX."_tmpcart",$updateData);			
	}
	function getCartContents($user_id)
	{
		$this->db->where("user_id",$user_id);
		$result = $this->db->get(DBPREFIX."_tmpcart");
		if($result && $result->num_rows())
		{
			$retdata = $result->result_array();
			$data = @unserialize($retdata[0]["cart_data"]);									
			if(!empty($data))
			{
				return $data;
			}else{
				return "is_exists";
			}			
		}
		return "";
	}
	function getSavedContents($user_id)
	{
		$this->db->where("user_id",$user_id);
		$result = $this->db->get(DBPREFIX."_save_for_later");
		if($result && $result->num_rows())
		{
			$retdata = $result->result_array();
			return $retdata;
		}
		return "";
	}
	
	function paypal_payment($CCDetails, $PayerInfo, $PayerName, $BillingAddress, $ShippingAddress, $PaymentDetails, $OrderItems)
	{
		$DPFields = array(
			'paymentaction' => 'Sale', 						// How you want to obtain payment.  Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
			'ipaddress' => $_SERVER['REMOTE_ADDR'], 							// Required.  IP address of the payer's browser.
			'returnfmfdetails' => '1' 					// Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
		);
		
		$Secure3D = array(
			'authstatus3d' => '', 
			'mpivendor3ds' => '', 
			'cavv' => '', 
			'eci3ds' => '', 
			'xid' => ''
		);
		$PaymentDetails['taxamt'] = 0; /* fix regarding paypal issue */
		$BillingAddress['countrycode'] = $this->getCountryCodeById($BillingAddress['countrycode']);
		$ShippingAddress['shiptocountry'] = $this->getCountryCodeById($ShippingAddress['shiptocountry']);
		if(is_numeric($BillingAddress['state'])){
			$BillingAddress['state'] = $this->getStateCodeById($BillingAddress['state']);
		}
		if(is_numeric($ShippingAddress['shiptostate'])){
			$ShippingAddress['shiptostate'] = $this->getStateCodeById($ShippingAddress['shiptostate']);
		}
		$PayPalRequestData = array(
		'DPFields' => $DPFields, 
		'CCDetails' => $CCDetails, 
		'PayerInfo' => $PayerInfo, 
		'PayerName' => $PayerName, 
		'BillingAddress' => $BillingAddress, 
		'ShippingAddress' => $ShippingAddress, 
		'PaymentDetails' => $PaymentDetails, 
		'OrderItems' => $OrderItems, 
		'Secure3D' => $Secure3D
		);
		$this->config->load('paypal');
		$config = array(
			'Sandbox' => $this->config->item('Sandbox'), 			// Sandbox / testing mode option.
			'APIUsername' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
			'APIPassword' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
			'APISignature' => $this->config->item('APISignature'), 	// PayPal API signature of the API caller
			'APISubject' => '', 									// PayPal API subject (email address of 3rd party user that has granted API permission for your app)
			'APIVersion' => $this->config->item('APIVersion')		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
		);
		$this->config->load('paypal');
		$this->load->library('paypal/Paypal_pro', $config);
		$PayPalResult = $this->paypal_pro->DoDirectPayment($PayPalRequestData);
		if(!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])){
			$response['status'] =0;
			$response['message'] = $PayPalResult['ERRORS'];
		}else{
			$response['status'] =1;
			$response['transaction_id'] = $PayPalResult['TRANSACTIONID'];
			$response['TIMESTAMP'] = $PayPalResult['TIMESTAMP']; 
			$response['payment'] = $PayPalResult['AMT'];
		}
		return $response;
	}
	function save_order($order_id, $items, $payment_details, $location, $card, $payer_info, $user_id, $platform = "", $discount = "", $payment_method = ""){
		$cart_data = array();
		$i = 0;
		$time = gmdate("Y-m-d\TH:i:s");
		$time = str_replace("T"," ",$time);
		$transaction_data = array(
			'created' => $time,
			'updated' => $time,
			'user_id' => $user_id,
			'order_id' => $order_id,
			'shipping' => serialize($location['shipping']),
			'billing' => ($payment_method == "paypal")?"paypal":serialize($location['billing']),
			'card_detail' => ($payment_method == "paypal")?"paypal":serialize($card),
			'payer_info' => serialize($payer_info),
			'order_platform'	=> $platform,
			"pay_type"	=> $payment_method,
		);
		$this->db->insert('tbl_transaction', $transaction_data);
		$trans_id = $this->db->insert_id();
		foreach ($items as $key=>$item):
			$l_taxamt = $item['tax'];//($this->getTax($location['shipping']['zipcode'], $item['price'])) * ($item['qty']);
			$item_gross_amount = (($item['price'] * $item['qty']) + $item['shipping_price']) + $l_taxamt;
			$cart_item = array(
				'created' => $time,
				'updated' => $time,
				'user_id' => $user_id,
				't_id'	  => $trans_id,
				'currency_code' => $payment_details['currencycode'],
				'shipping_amt' => $payment_details['shippingamt'],
				'gross_tax_amount' =>$payment_details['taxamt'],
				'item_gross_tax_amount' => $l_taxamt,
				'gross_amount' =>$payment_details['amt'],
				'item_gross_amount' => $item_gross_amount,
				'qty' => $item['qty'],
				'price' => $item['price'],
				'warehouse_id' => isset($item['warehouse_id'])?$item['warehouse_id']:0,
				'item_shipping_id' => $item['shipping_id'],
				'item_shipping_amount' =>$item['shipping_price'], 				
				'item_total' => ($item['price']*$item['qty'])+$l_taxamt,
				'tax_amount' =>$l_taxamt,
				'seller_id' => $item['seller_id'],
				'order_id' => $order_id,
				'product_id' => $item['prd_id'],
				'product_vid' => $item['id'],
				'condition_id' => $item['condition_id'],
				'variants' => serialize($item['variant_ids']),
				'status' => 0,
				'hasDiscount' => ($item['discount_type'] != "")?"1":"0",
				"discount_data" => ($item['discount_type'] != "")?serialize(array("id"=>$item['discount_id'], "type"=>$item['discount_type'], "value"=>$item['discount_value'], "original"=>$item['original'])): "",
				"hubx_id" => $item['hubx_id']
			);
			array_push($cart_data, $cart_item);
			$sellQty = $item['sell_quantity']+$item['qty'];
			$this->db->set('sell_quantity',$sellQty);
			$this->db->where('seller_product_id',$item['sp_id']);
			$this->db->update('tbl_product_inventory');
			// $qty = $item['max_qty']-$item['qty'];
			// $this->db->set('quantity',$qty);
			// $this->db->where(array('seller_product_id'=>$item['sp_id'], 'product_variant_id'=>$item['id']));
			// $this->db->update('tbl_product_inventory');
			$i++;
		
		endforeach;
		$this->db->insert_batch('tbl_transaction_details', $cart_data);
		if($cart_data != ""){
			foreach($cart_data as $cd){
				$product_name = $this->Product_Model->getProductName($cd['product_id']);
				$product_name = $product_name[0]->product_name;
				$Username = $this->User_Model->getUserName($cd['user_id']);
				$Username = $Username->firstname." ".$Username->lastname;
				$action = "Order(s) of your Product: ".$product_name." was placed by ".$Username."." ; //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
				$not_type = '0';
				$from = $cd['user_id'];
				$to = $cd['seller_id'];
				$user_type = "2";
				saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
			}
		}
	}
	
	function complete_order($order_id, $payment, $user_id){
		$update_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'transaction_id' => $payment['transaction_id'],
			'card_id' => $payment['card_id'],
			'status' => 1,
		);
		$this->db->where('order_id', $order_id)->update('tbl_transaction',$update_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function getOrderByClientID($user_id="", $order_id="", $pageing="0",$limit=""){
		if($pageing > 1){
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		} 
		//t.billing, t.card_detail,
		$this->db->select(' td.id, t.created, p.product_name, c.condition_name as condition_title, pm.thumbnail AS product_image,pm.is_local, ss.store_id,
							ss.store_name, users.email as seller_email, users.mobile as seller_phone, ss.store_logo,pr.rating,pr.review_id,pr.review, 
							CONCAT(\'[\',GROUP_CONCAT(DISTINCT CONCAT(\'{"id":"\', v.v_id, \'", "title":"\', vc.v_cat_title, \'", "value":"\',v.v_title,\'"}\')),\']\') AS variants,
							td.id as p_trans_id,td.status as order_status, td.price, td.qty, t.order_id, t.transaction_id, t.shipping,t.billing, t.card_detail, t.status, td.cancellation_pending, td.is_cancel,
							td.id AS order_item_id,td.tracking_id,td.item_received_confirmation as item_confirm_status, td.currency_code,td.shipping_amt,td.tax_amount, td.item_total, td.gross_amount,td.item_gross_amount,td.item_shipping_amount,
							td.seller_id, td.product_id, td.product_vid, td.condition_id, sp.sp_id, sp.seller_product_description, td.hasDiscount as discount, td.discount_data as discountData')
				 ->from(DBPREFIX.'_transaction AS t')
				 ->join(DBPREFIX.'_transaction_details AS td','t.order_id = td.order_id')
				 ->join(DBPREFIX.'_product_variant AS pv','td.product_vid = pv.pv_id')
				 ->join(DBPREFIX.'_seller_product AS sp','pv.sp_id = sp.sp_id')
				 ->join(DBPREFIX.'_users AS users','users.userid = td.seller_id','left')
				 ->join(DBPREFIX.'_product AS p','td.product_id = p.product_id')
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
				 ->join(DBPREFIX.'_product_conditions AS c','pv.condition_id = c.condition_id')
				 ->join(DBPREFIX.'_product_reviews AS pr','pr.order_id = t.order_id AND td.product_vid = pr.pv_id AND sp.sp_id = pr.sp_id','left')
				 ->join(DBPREFIX.'_variant AS v','FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left', false)
				 ->join(DBPREFIX.'_variant_category AS vc','v.v_cat_id = vc.v_cat_id', 'left', false)
				 ->join(DBPREFIX.'_seller_store AS ss','pv.seller_id = ss.seller_id')
				 ->where('t.user_id', $user_id)
				 ->group_by('t.order_id')
				 ->group_by('td.product_vid')
				 ->order_by('t.created', 'desc')
				 ->order_by('t.id', 'desc')
				 ->order_by('t.status', 'desc');

		if(is_array($order_id) && !empty($order_id)){
			$this->db->where_in('t.order_id', $order_id);
		}else if(!empty($order_id)){
			$this->db->where_in('t.order_id', $order_id);
		}
		$result = $this->db->get();
		//echo $this->db->last_query();die();
		if($result->num_rows() > 0){
			return $result->result();
		}else{
			return array();
		}
	}
	function getOrderByitemID($user_id="", $item_id=""){
		$this->db->select(' t.id, t.created, p.product_name, c.condition_name as condition_title, pm.thumbnail AS product_image,pm.is_local, ss.store_id,
							ss.store_name, profile.customer_service_email as seller_email, profile.customer_service_phone as seller_phone, ss.store_logo,pr.rating,pr.review_id,pr.review, 
							CONCAT(\'[\',GROUP_CONCAT(DISTINCT CONCAT(\'{"id":"\', v.v_id, \'", "title":"\', vc.v_cat_title, \'", "value":"\',v.v_title,\'"}\')),\']\') AS variants,
							td.id as p_trans_id,td.status as order_status, td.price, td.qty, t.order_id, t.transaction_id, t.shipping, t.billing, t.card_detail, t.status, 
							td.id AS order_item_id,td.tracking_id,td.item_shipping_id,td.item_received_confirmation as item_confirm_status, td.currency_code,td.shipping_amt,td.tax_amount, td.item_total, td.gross_amount,td.item_gross_amount,td.item_shipping_amount,
							td.seller_id, td.product_id, td.product_vid, td.condition_id, sp.sp_id')
				 ->from(DBPREFIX.'_transaction AS t')
				 ->join(DBPREFIX.'_transaction_details AS td','t.order_id = td.order_id')
				 ->join(DBPREFIX.'_product_variant AS pv','td.product_vid = pv.pv_id')
				 ->join(DBPREFIX.'_seller_product AS sp','pv.sp_id = sp.sp_id')
				 ->join(DBPREFIX.'_profiles AS profile','profile.userid = td.seller_id')
				 ->join(DBPREFIX.'_product AS p','td.product_id = p.product_id')
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
				 ->join(DBPREFIX.'_product_conditions AS c','pv.condition_id = c.condition_id')
				 ->join(DBPREFIX.'_product_reviews AS pr','pr.order_id = t.order_id AND td.product_vid = pr.pv_id AND sp.sp_id = pr.sp_id','left')
				 ->join(DBPREFIX.'_variant AS v','FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left', false)
				 ->join(DBPREFIX.'_variant_category AS vc','v.v_cat_id = vc.v_cat_id', 'left', false)
				 ->join(DBPREFIX.'_seller_store AS ss','pv.seller_id = ss.seller_id')
				 ->where('td.id', $item_id);
		$result = $this->db->get();
		if($result->num_rows() > 0)
		{
			return $result->result();
		}else{
			return array();
		}
	}
	
	function formatOrderList($orders, $return = 'data'){
		$forders = array();
		$itemGrossAmt = array();
		if($orders){
			for($i = 0;$i<count($orders); $i++){
				if($return == 'data'){
					if(!is_array($orders)){
						$orders = array($orders);
					}
					if(isset($forders[$orders[$i]->order_id])){
						$forders[$orders[$i]->order_id]->item_gross_amount = $forders[$orders[$i]->order_id]->item_gross_amount + $orders[$i]->item_gross_amount;
						$forders[$orders[$i]->order_id]->orders[] = $orders[$i];
					} else {
						$card = ($orders[$i]->billing != "paypal")?unserialize($orders[$i]->card_detail):$orders[$i]->card_detail;
						$discountValue = (isset($orders[$i]->discount) && $orders[$i]->discount == "1")?unserialize($orders[$i]->discountData):"";
						//$card['acct'] = ccMasking($card['acct'], '*');
						$forders[$orders[$i]->order_id] = (object)array( 
							'order_id'=>$orders[$i]->order_id,
							'created'=>$orders[$i]->created,
							'status'=>$orders[$i]->status,
							'transaction_id'=>$orders[$i]->transaction_id,
							'shipping'=> unserialize($orders[$i]->shipping),
							'billing'=> ($orders[$i]->billing != "paypal")?unserialize($orders[$i]->billing):$orders[$i]->billing,
							'card_holder'=> ($orders[$i]->billing != "paypal")?$card['holder']:"paypal",
							'currency_code'=>$orders[$i]->currency_code,
							'gross_amount'=>$orders[$i]->gross_amount,
							'item_gross_amount' => $orders[$i]->item_gross_amount,
							'shipping_amt'=>$orders[$i]->shipping_amt,
							'condition_id' =>$orders[$i]->condition_id,
							'item_shipping_amount' =>$orders[$i]->item_shipping_amount,
							'tax_amount'=>$orders[$i]->tax_amount,
							"discount" => $discountValue,
							'store_name'=> $orders[$i]->store_name
						);
						$forders[$orders[$i]->order_id]->orders[] = $orders[$i];
					}
				} else {
					$forders[$orders[$i]->order_id] = $orders[$i]->order_id;
				}
			}
		}
		switch($return){
			case 'rows':
				return count($forders);
			break;
			case 'data':
				return $forders;
			break;
		}
	}
	function getOrderBySeller($user_id, $order_id = '',$user_type=""){
		$this->db->select('t.id, t.created, p.product_name, c.condition_name as condition_title,pm.thumbnail AS product_image,pm.is_local,ss.store_id,
		ss.store_name, profile.customer_service_email as seller_email, profile.customer_service_phone as seller_phone, ss.store_logo, 
		CONCAT(\'[\',GROUP_CONCAT(DISTINCT CONCAT(\'{"id":"\', v.v_id, \'", "title":"\', vc.v_cat_title, \'", "value":"\',v.v_title,\'"}\')),\']\') AS variants,
		td.id as p_trans_id, td.price, td.qty, t.order_id, t.transaction_id, t.shipping, t.billing, t.card_detail, t.status, 
		td.id AS order_item_id, td.currency_code,td.shipping_amt,td.item_shipping_amount,td.tax_amount, td.item_total, td.gross_amount,td.item_gross_amount,
		td.seller_id, td.product_id, td.product_vid, td.condition_id, sp.sp_id, td.hasDiscount as discount, td.discount_data as discountData')->from('tbl_transaction_details AS td')
			->join('tbl_transaction AS t','t.order_id = td.order_id')
			->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
			->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
			->join('tbl_product AS p','td.product_id = p.product_id')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT') 
			->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
			->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
			->join('tbl_profiles AS profile','profile.userid = td.seller_id',"LEFT")
			->join('tbl_variant AS v','FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left', false)
			->join(DBPREFIX.'_variant_category AS vc','v.v_cat_id = vc.v_cat_id', 'left', false)
			->where('td.seller_id', $user_id)
			->group_by('t.order_id')
			->group_by('td.product_vid');
		if($order_id != ''){
			$this->db->where('t.order_id', $order_id);
		}
		$result = $this->db->get();
		if($result->num_rows() > 0)
		{
			return $result->result_object();
		}
	}

	private function getCountryCodeById($country_id)
	{
		$this->db->select('iso')->from(DBPREFIX.'_country')->where('id',$country_id);
		$result = $this->db->get();
		if($result->num_rows() > 0){
			$item = $result->row();
			return $item->iso;
		} else {
			return 'US';
		}
		
	}
	private function getStateCodeById($state_id)
	{
		$this->db->select('code')->from(DBPREFIX.'_states')->where('id',$state_id);
		$result = $this->db->get();
		if($result->num_rows() > 0){
			$item = $result->row();
			return $item->code;
		} else {
			return 'US';
		}
		
	}
	private function getProductImage($product_id, $sp_id){
		$product = (object) array();
		$product_image = $this->Product_Model->getData('tbl_seller_product',"thumbnails,image_link,is_local",array('sp_id'=>$sp_id));
		
		if($product_image[0]->image_link == ""){
			$getProductImage = $this->Product_Model->getProductImage($product_id);
			if($getProductImage['status'] == 1){
				$product->image_link = explode(',',$getProductImage['data']->iv_link);
				$product->image_link = $product->image_link[0];
				$product->is_local = $getProductImage['data']->is_local;
			}
		} else {
			$product = $product_image[0];
		}
		return $product;
	}
	
	function destroyCartContents($user_id)
	{
		$this->db->where("user_id",$user_id);
		$result = $this->db->delete(DBPREFIX."_tmpcart");
	}
	
	function forReviews($insert_data, $userid){
		if($insert_data){
			$this->db->insert('tbl_product_reviews', $insert_data);
			if($this->db->affected_rows() > 0){
				return $this->db->insert_id();
			} else {
				return false;
			}
		}else{
			return false;
		}
		
		
	}
 	
	public function getReviews($user_id,$sp_id){
		$where = array('sp_id'=>$sp_id,'buyer_id'=>$user_id);
		$this->db->select('*')
			 ->from("tbl_product_reviews")
			 ->where($where);
		$this->db->group_by('order_id'); 	 
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	public function update_item_status($id){
		$this->db->set('item_received_confirmation',1);
		$this->db->where('id', $id); 
		$this->db->update('tbl_transaction_details');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	//Save For Later
	function getSavedForLaterContents($user_id)
	{
		$this->db->where("user_id",$user_id);
		$result = $this->db->get(DBPREFIX."_tmp_save_for_later");
		if($result && $result->num_rows())
		{
			$retdata = $result->result_array();								
			$data = @unserialize($retdata[0]["saved_data"]);
			if(!empty($data))
			{
				return $data;
			}else{
				return "is_exists";
			}				
		}
		return "";
	}
	function addtoSaveForLater($cartData,$user_id="")
	{
		if($user_id == ""){return "";}
		if($cartData != ""){
			$cartData = serialize($cartData);
		}
		$cartData = array("saved_data"=>$cartData,'user_id'=>$user_id);
		$this->db->insert(DBPREFIX."_tmp_save_for_later",$cartData);
		return $this->db->insert_id();
	}
	
	function updateSaveForLater($user_id,$cartData)
	{
		if($user_id == ""){return "";}
		if($cartData != ""){
			$cartData = serialize($cartData);
		}
		$updateData = array("saved_data"=>$cartData);
		$this->db->where("user_id",$user_id);		
		$this->db->update(DBPREFIX."_tmp_save_for_later",$updateData);				
	}
	
	function cancel_order($data)
	{
		$transaction = $this->db->select("td.status, td.seller_id,td.created,t.capture_id,t.pay_type,t.transaction_id,td.item_gross_amount,td.id as trans_details_Id,t.is_capture")->from(DBPREFIX.'_transaction_details td')->join(DBPREFIX."_transaction t","t.id=td.t_id")->where('td.id',$data['td_id'])->get()->row();
		/*Time Difference*/
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$start = strtotime($transaction->created);
		$end = strtotime($date_utc);
		$minutes = ($end - $start) / 60;
		$return = array();
		if($transaction->status == 0){
			if($minutes <= 30){
			 	$update_data = array("is_cancel"=>"1",'cancellation_pending' => '1',"cancel_reason"=>"Buyer cancel this order");
				$return = array("status"=>"2", "message"=>"Your order is cancel.", "code" => "Cancellation Approved");
				$amount = (int)$transaction->item_gross_amount;
				if($transaction->is_capture == 1){
					if($transaction->capture_id || $transaction->transaction_id){
						switch($transaction->pay_type){
							case "paypal":
								$this->load->library('PayPal', 'paypal');
								$currency = "";
								$capture = $this->paypal->refundOrder($transaction->capture_id, $amount, $currency);
								if($capture->result->status == "COMPLETED"){
									$this->db->where("id",$transaction->trans_details_Id)->update(DBPREFIX.'_transaction_details', array('status'=>'2', 'refund_id'=>$capture->result->id,"cancel_reason"=>"Buyer cancel this order"));
								}
							case "card":
								$stripe_key = $this->config->item('stripe_key');
								$this->load->library('Stripe', 'stripe');
								$token = $this->stripe->getRefund($transaction->transaction_id, $amount, $stripe_key);
								if(isset($token['status']) && $token['status'] == 1){
									$this->db->where("id",$transaction->trans_details_Id)->update(DBPREFIX.'_transaction_details', array('status'=>'2', 'refund_id'=>$token['customer']->id,"cancel_reason"=>"Buyer cancel this order"));
									return $return;
								}
						}
					}
				}else {
					$this->db->where("id",$transaction->trans_details_Id)->update(DBPREFIX.'_transaction_details', array('status'=>'2', 'cancellation_pending'=>"0","cancel_reason"=>"Buyer cancel this order"));
					return $return;
				}
			}else{
			 	$update_data = array('cancellation_pending' => '1');
				$return = array("status"=>"1", "message"=>"Cancellation request successfully send", "code" => $transaction->seller_id);
			}
			$this->db->where('order_id', $data['order_id'])->where('id', $transaction->trans_details_Id)->update('tbl_transaction_details',$update_data);
			if($this->db->affected_rows() > 0){
				return $return;
			} else {
				return array("status"=>"0", "message"=>"Cancellation request failed", "code" => "");
			}
		}elseif($transaction->status == 2){
			return array("status"=>"2", "message"=>"Order is already cancelled by seller", "code" => "Already Cancelled");
		}elseif($transaction->status == 1){
			return array("status"=>"2", "message"=>"Order is already approved by seller", "code" => "Already Approved");
		}else{
			return array("status"=>"2", "message"=>"Order cancellation already approved", "code" => "");
		}
	}
	
	function deleteOrder($order_id, $errors = array(""))
	{
		$transaction_detail = $this->db->select('*')->from('tbl_transaction_details')->where('order_id', $order_id)->get()->result_array();
		$transaction = $this->db->select('*')->from('tbl_transaction')->where('order_id', $order_id)->get()->result_array();
		$transaction[0]['error'] = serialize($errors);
		unset($transaction[0]['is_transfer']);
		$this->db->insert_batch(DBPREFIX."_transaction_details_backup", $transaction_detail);
		//echo $this->db->last_query();
		//echo "<pre>";print_r($transaction_detail);die();
		$this->db->insert(DBPREFIX."_transaction_backup", $transaction[0]);
		if($this->db->where('order_id', $order_id)->delete('tbl_transaction_details')){
			if($this->db->where('order_id', $order_id)->delete('tbl_transaction')){
				// $action = "Order(s) of your Product: ".$product_name." was placed by ".$Username."." ; //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
				// $not_type = '0';
				// $from = $transaction[0]['user_id'];
				// $to = $transaction_detail[0]['seller_id'];
				// $user_type = "2";
				// saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function getTax($zip, $amount){
		if($zip != '' && $amount > 0){
			$this->load->model('Utilz_Model');
			$params = '?key='.$this->config->item('tax_api_key').'&postalcode='.$zip;
			$taxResponse = $this->Utilz_Model->curlRequest($params, $this->config->item('tax_api_url'), false, false);
			//echo "<pre>";print_r($taxResponse);die();
			if(isset($taxResponse->rCode) && $taxResponse->rCode == 100){
				$result = $taxResponse->results;
				if(count($result)>0){
					$amount = $result[0]->taxSales * $amount;
				} else {
					$amount = $this->config->item('vat_tax');
				}
			} else {
				$amount = $this->config->item('vat_tax');
			}
		}
		return $amount;
	}
	
	public function getTaxRate($zip){
		 $taxRate = 0;
		if($zip != ''){
			$this->load->model('Utilz_Model');
			$params = '?key='.$this->config->item('tax_api_key').'&postalcode='.$zip;
			$taxResponse = $this->Utilz_Model->curlRequest($params, $this->config->item('tax_api_url'), false, false);
			//echo "<pre>";print_r($taxResponse);die();
			if(isset($taxResponse->rCode) && $taxResponse->rCode == 100){
				$result = $taxResponse->results;
				if(count($result)>0){
					$taxRate = $result[0]->taxSales;
				}
			}
		}
		return $taxRate;
	}
	public function review_image($data){
		$this->db->insert(DBPREFIX."_review_media",$data);
	}

	public function referral_purchase($referrals, $userid, $transaction_id, $percent){
		foreach($referrals as $code){
			$data = $this->db->select("sender_id, accept_time, expire_time")->from(DBPREFIX.'_referral')->where('referral_code', $code['code'])->get()->result();
			
			//UPDATE QUERY's WHERE CLAUSE
			$this->db->where('referral_code',$code['code']);

			$expiry = new DateTime($data[0]->expire_time);
			$current = date('Y-m-d h:i:s');

			if(($data[0]->sender_id != $userid) && $expiry > $current){
				if($data[0]->accept_time == ""){
					$this->db->set(array('accept_time'=> $current, 'first_order'=> $current, 'maturity'=>'1'));
				}else{
					$this->db->set('maturity', '1');
				}
				$this->db->set('sale_count', 'sale_count+1', FALSE);
				$this->db->update(DBPREFIX.'_referral');
				$points = floatval(($code['price'] / 100) * $percent);
				// $points = number_format($points, 2);
				$data = array("user_id"=>$userid, "referral_code"=>$code['code'], "transaction_id"=>$transaction_id, 'pv_id'=>$code['pv_id'], 'percent'=>$percent, "amount"=>$code['price'], "points"=>$points);
				$this->db->insert("tbl_affiliated_transactions", $data);
			}else{

			}
		}
	}
	
	public function calculateVoucher($code, $cart, $voucher, $cart_total, $discount_amount, $discounted_cart_toatl, $user_id, $isAdmin, $zip = 0)
	{
		/*
			echo $cart_total;
		*/
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'code'=>'002');
		$proceed = true;
		$discount_total = 0;
		$cartTotal = $cart_total;
		if(count($voucher) > 0){
			$voucher = $voucher[0];
			$seller_id = $voucher->seller_id;
			if($voucher->used_limit > $voucher->voucher_used){
				if($voucher->voucher_used > 0){
					$chekVoucherUse = $this->Policies_Model->buyerUsedVouchers($user_id, $code);
					if(count($chekVoucherUse)>0){
						$response['code'] = "0021";
						$response['message'] = "Voucher is already used";
						$proceed = false;
					}
				}
				$today = time();
				$seller_totals = array();
				
				if($proceed){
					if($voucher->valid_from > $today){
						$response['message'] = "Voucher is not valid yet";
						$response['code'] = "0022";
					} else {
						if($voucher->valid_to <= $today){
							$response['message'] = "Voucher is expired";
							$response['code'] = "0023";
						} else {
							if(($voucher->min_price > 0 && $cart_total > $voucher->min_price) || $voucher->min_price == 0){
								$discount_data = array('discount_amount'=>$discount_amount, 'items'=>array(), 'discount_code'=>$code, 'discount_data'=>$voucher);
								$discount = $voucher->discount;
								if($seller_id != '' && ($voucher->apply_on == 'all' || $voucher->apply_on == 'user')){
									foreach($cart as $item){
										//echo '('.$seller_id.' != \'\' && '.$item['seller_id'].' == '.$seller_id.') || '.$seller_id.' == \'\'<br />';
										if(($seller_id != '' && $item['seller_id'] == $seller_id) || $seller_id == ''){
											$item_price = $item['price'] * $item['qty'];
											$discount_data['items'][$item['rowid']] = array('hasDiscount'=>true, 'discount_amount'=>0);
											$discount_total += $item_price;
											$seller_totals[$item['seller_id']] = (isset($seller_totals[$item['seller_id']]))?$seller_totals[$item['seller_id']]+$item_price:$item_price;
										}
									}
								}
								switch($voucher->apply_on){
									case 'all':
										if($seller_id != '')
											$cart_total = (isset($seller_totals[$seller_id]))?$seller_totals[$seller_id]:0;
										if($voucher->min_price != 0 && $voucher->min_price > $cart_total){
											$response['message'] = 'Amount is lower than minimum amount required for this voucher';
											$response['code'] = "0024";
											break;
										}
										if($voucher->discount_type == 1){
											$discount_amount = ($discount/100)*$cart_total;
										}
										if($voucher->discount_type == 0){
											$discount_amount = $discount;
										}
										$discount_total = $cart_total;
										$response['status'] = 1;
									break;
									case 'product':
										$product_id = $voucher->apply_id;
										$prd_amount = 0;
										foreach($cart as $item){
											if($item['id'] == $product_id){
												if(($seller_id != '' && $item['seller_id'] == $seller_id) || $seller_id == ''){
													$item_price = $item['price'] * $item['qty'];
													$prd_amount += $item_price;
													$discount_data['items'][$item['rowid']] = array('hasDiscount'=>true, 'discount_amount'=>0);
													$seller_totals[$item['seller_id']] = (isset($seller_totals[$item['seller_id']]))?$seller_totals[$item['seller_id']]+$item_price:$item_price;
												}
											}
										}
										$discount_total = $prd_amount;
										if($voucher->discount_type == 1){
											$discount_amount = ($discount/100)*$prd_amount;
										}
										if($voucher->discount_type == 0){
											$discount_amount = $discount;
										}
										if($discount_amount > $voucher->max_price && $voucher->max_price != 0){
											$discount_amount = $voucher->max_price;
										}
										$response['status'] = 1;
									break;
									case 'category':
										$category_id = $voucher->apply_id;
										$cat_price = 0;
										foreach($cart as $item){
											if(isset($item['category_id'])){
												$cat_ids = explode(',', $item['category_id']);
												if(in_array($category_id, $cat_ids)){
													if(($seller_id != '' && $item['seller_id'] == $seller_id) || $seller_id == ''){
														$item_price = $item['price'] * $item['qty'];
														$cat_price += $item_price;
														$discount_data['items'][$item['rowid']] = array('hasDiscount'=>true, 'discount_amount'=>0);
														$seller_totals[$item['seller_id']] = (isset($seller_totals[$item['seller_id']]))?$seller_totals[$item['seller_id']]+$item_price:$item_price;
													}
												}
											}
										}
										
										if($voucher->min_price != 0 && $voucher->min_price > $cat_price){
											$response['message'] = 'Amount is lower than minimum amount required for this voucher';
											$response['code'] = "0025";
											break;
										}
										if($voucher->discount_type == 1){
											$discount_amount = ($discount/100)*$cat_price;
										}
										if($voucher->discount_type == 0){
											$discount_amount = $discount;
										}
										if($discount_amount > $voucher->max_price && $voucher->max_price != 0){
											$discount_amount = $voucher->max_price;
										}
										$discount_total = $cat_price;
										$response['status'] = 1;
									break;
									case 'seller':
										$seller_amount = 0;
										foreach($cart as $item){
											if($item['seller_id'] == $voucher->apply_id){
												$item_price = $item['price'] * $item['qty'];
												$seller_amount += $item_price;
												$seller_totals[$item['seller_id']] = (isset($seller_totals[$item['seller_id']]))?$seller_totals[$item['seller_id']]+$item_price:$item_price;
											}
										}
										if($voucher->min_price != 0 && $voucher->min_price > $seller_amount){
											$response['message'] = 'Amount is lower than minimum amount required for this voucher';
											$response['code'] = "0025";
											break;
										}
										
										if($voucher->discount_type == 1){
											$discount_amount = ($discount/100)*$seller_amount;
										}
										if($voucher->discount_type == 0){
											$discount_amount = $discount;
										}
										$discount_total = $seller_amount;
										$response['status'] = 1;
									break;
									case 'user':
										if($user_id == $voucher->apply_id){
											if($voucher->min_price != 0 && $voucher->min_price > $cart_total){
												$response['message'] = 'Amount is lower than minimum amount required for this voucher';
												$response['code'] = "0025";
												break;
											}
											if($voucher->discount_type == 1){
												$discount_amount = ($discount/100)*$cart_total;
											}
											if($voucher->discount_type == 0){
												$discount_amount = $discount;
											}
											$discount_total = $cart_total;
											$response['status'] = 1;
										} else {
											$response['code'] = "";
											$response['message'] = "Invalid voucher.";
											$response['code'] = "0026";
										}
									break;
								}
								if($response['status'] == 1){
									if($discount_amount > 0){
										if($discount_amount > $voucher->max_price && $voucher->max_price != 0){
											$discount_amount = $voucher->max_price;
										}
										if($discount_amount > $cartTotal){
											$discount_amount = $cartTotal;
										}
										$discounted_cart_toatl = $cartTotal - $discount_amount;
										$response['message'] = 'Discounted cart total is '.$this->cart->format_number($discounted_cart_toatl);
										$response['discount_amount'] = $this->cart->format_number($discount_amount);
										$response['discounted_cart_total'] = $this->cart->format_number($discounted_cart_toatl);
										$tax = ($zip == 0)?0:$this->cart->format_number($this->getTax($zip,$discounted_cart_toatl));
										$response['tax'] = $tax;
										$discount_data['tax'] = $tax;
										$discount_data['discount_amount'] = $discount_amount;
										$discount_data['discounted_cart_total'] = $discounted_cart_toatl;
										$discount_data['discount_total'] = $discount_total;
										$discount_data['seller_totals'] = $seller_totals;
										//$discount_data = $this->calculateSellerDiscount($discount_data);
										$response['discount'] = $discount_data;
										$this->session->set_userdata('discount_data', $discount_data);
									} else {
										$response['status'] = 0;
										$response['message'] = 'Voucher is not applicable';
										$response['code'] = "0027";
									}
								}
							} else {
								$response['message'] = "Minimum amount to avail this Coupon is ".$this->cart->format_number($voucher->min_price);
								$response['code'] = "0028";
							}
						}
					}
				}
			} else {
				$response['message'] = "Voucher is used";
				$response['code'] = "0029";
			}
		} else {
			$response['message'] = "Voucher not found";
			$response['code'] = "0030";
		}
		return $response;
	}
	
	public function calculateItemShipping($shipping_id, $qty, $cart_item)
	{
		$response = array('status'=>0, 'message'=>'Invalid request', 'shipping'=>array(), 'code'=>'000');
		$shipping = $this->Utilz_Model->getShipping($shipping_id);
		if(is_array($shipping) && count($shipping) > 0){
			$base_price = $shipping[0]->price;
			$shipping_type = $shipping[0]->shipping_type;
			$ids = array();
			$tot_qty = 0;
			$total_price = 0;
			$total_price = $qty * $cart_item['price'];
			$shipping_amount = 0;
			$productShippingInfo = array();
			if($shipping[0]->free_after > 0 && $total_price >= $shipping[0]->free_after){
			} else {
				$productShippingInfo = $this->Product_Model->getProductShipping($cart_item['prd_id']);
				$tier_unit = $shipping[0]->incremental_unit;
				$tier_price = $shipping[0]->incremental_price;
				$i = 0;
				switch($shipping_type){
					case'item':
						$base_quantity = 1;
						/* Calculate Price*/
						$tier_amount = ceil(($qty - $base_quantity) / $tier_unit ) * $tier_price;
					break;
					case 'weight':
						$tot_weight = 0;
						$base_weight = $shipping[0]->base_weight;
						/* Calculate Weight */
						foreach($productShippingInfo as $item){
							$tot_weight = ($item->weight*$qty);
							$i++;
						}
						/* Calculate Price*/
						
						if($base_weight >= $tot_weight){
							$tier_amount = 0;
						} else {
							$tier_amount = (ceil(($tot_weight- $base_weight)/$tier_unit) ) * $tier_price;
						}
					break;
					case 'dimension':
						$total_volume = 0;
						$base_valume = $shipping[0]->base_length * $shipping[0]->base_width * $shipping[0]->base_depth;
						/* Calculate Volume */
						foreach($productShippingInfo as $item){
							$total_volume = $total_volume+(($item->length * $item->width * $item->height)*$qty);
							$i++;
						}
						/* Calculate Price*/
						if($base_valume >= $total_volume){
							$tier_amount = 0;
						} else {
							$tier_amount = (ceil(($total_volume - $base_valume)/$tier_unit) ) * $tier_price;
						}
					break;
				}
				$shipping_amount = $base_price + $tier_amount;
			}
			$response['status'] = 1;
			$response['message'] = 'OK';
			$response['shipping'] = array(
				'shipping_id' => $shipping[0]->shipping_id,
				'title' => $shipping[0]->title,
				'shipping_amount' => $shipping_amount,
				'shipping_data' => $shipping
			);
		}
		return $response;
	}
	
	function calculateShipping($cart)
	{
		$shipping_amount = 0;
		foreach($cart as $item){
			$shipping_amount += $item['shipping_price'];
		}
		return $shipping_amount;
	}
	
	function calculateCartShipping($cart_item)
	{
		$shipping_amount = 0;
		foreach($cart_item as $item){
			foreach($item as $a){
				$shipping_amount += $a['shipping'];
			}
		}
		return $shipping_amount;
	}
	public function calculateCartItemShipping($item="",$qty="",$shipping_id="",$subtotal_seller){
		$data = array();
		$subtotal = 0;
		$shipping_item_price = 0;
		$item['prd_id'] = (isset($item['product_id']))?$item['product_id']:$item['prd_id'];
		if($shipping_id){
			$shipping_item = $this->calculateItemShipping($shipping_id, $qty, $item);
			//echo "<pre>";print_r($shipping_item);echo "</pre>";
			if($shipping_item['status'] == 1){
				$shipping_item_price = $shipping_item['shipping']['shipping_amount'];
				$item['qty']  = $qty;
				$item['shipping_id']  = $shipping_item['shipping']['shipping_id'];
				$item['shipping_title'] = $shipping_item['shipping']['title'];
				$item['shipping_price']  = $shipping_item_price;
				$item['subtotal'] = $item["price"]*$qty;
				//If Subtotal equals to free shipping threshold then update all shipping to Free.
				if(isset($subtotal_seller[$item['seller_id']]) && isset($subtotal_seller[$item['seller_id']][$shipping_id])){
					$subtotal_seller[$item['seller_id']][$shipping_id]['subtotal'] += $item['subtotal'];
					$subtotal_seller[$item['seller_id']][$shipping_id]['shipping'] += $item['shipping_price'];
					$subtotal_seller[$item['seller_id']][$shipping_id]['row_id'][] = $item['rowid'];
				} else {
					$subtotal_seller[$item['seller_id']][$shipping_id]['subtotal'] = $item['subtotal'];
					$subtotal_seller[$item['seller_id']][$shipping_id]['shipping'] = $item['shipping_price'];
					$subtotal_seller[$item['seller_id']][$shipping_id]['row_id'][] = $item['rowid'];
				}
				$free_after = $shipping_item['shipping']['shipping_data'][0]->free_after;
				if($free_after <= $subtotal_seller[$item['seller_id']][$shipping_id]['subtotal']){
					$subtotal_seller[$item['seller_id']][$shipping_id]['shipping'] = 0;
					$shipping_item_price = 0;
				}
			}
		}
		unset($item['prd_id']);
		
		if($shipping_item_price == 0){
			$price = "Free Shipping";
		}else{
			$price = "US $".$item['shipping_price'];
		}
		
		return array("item"=>$item, 'subtotal_seller'=>$subtotal_seller);
	}
	public function updateShippingPerItem($cart, $shipping_seller)
	{
		for($i = 0; $i<count($cart);$i++){
			$cart[$i]['net_shipping_price'] = $shipping_seller[$cart[$i]['seller_id']][$cart[$i]['shipping_id']]['shipping'];
		}
		return $cart;
	}
	
	public function verifyCartItems($cart, $callFrom)
	{
		//echo "<pre>";print_r($cart); die();
		/* 
			$callFrom
			- core: do not return, update data
			- api: do not update session data, return data
		*/
		$response = array('status'=>1, 'message'=>'None', 'code'=>'000', 'cart'=>$cart);
		$data = array();
		$isUpdated = false;
		$mapped = array();
		if(!empty($cart)){
			$i = 0;
			foreach($cart as $items){
				$is_shipping_changed = isset($items['is_shipping_changed'])?$items['is_shipping_changed']:"";
				$product_variant_id[] = $items['id'];
				$row[$items['id']] = array("pv_id"=>$items['id'], "row_id"=>$items['rowid'],"qty"=>$items['qty'], "ship_id" => $items['shipping_id'], "ship_title" => $items['shipping_title'], "ship_price" => $items['shipping_price'], "con_id"=>$items['condition_id'], "original_price"=>$items['original'], "update_msg"=>(isset($items['update_msg']))?$items['update_msg']:'', "price"=>$items['price'], "discount"=>$items['discount_value']);
				$mapped[$items['rowid']] = $i;
				$i++;
			}
			$products = $this->Product_Model->checkProductForDiscountByProductVariantId($product_variant_id);
			//echo"<pre>";print_r($products); die();
			// print_r($products);
			foreach($products['data'] as $product){
				$msg = array();
				// $isUpdated = false;
				// print_r($product);print_r($row[$product->pv_id]);die();
				$data['rowid'] 			= $row[$product->pv_id]['row_id'];
				$index = $mapped[$data['rowid']];
				if($product->qty >= $row[$product->pv_id]['qty']){
					if($product->price != $row[$product->pv_id]['original_price']){
						$data['price']   		= $cart[$index]['price'] 	= $product->price;
						$data['original']   	= $cart[$index]['original'] = $product->price;
						$msg[] = "Price has been updated";
						$cart[$index]['messages'][] = 'Price has been updated';
						$isUpdated = true;
					}
					if(strtotime(date("y-m-d")) >= strtotime($product->discount_from) && strtotime(date("y-m-d")) <= strtotime($product->discount_till)){
						// echo"<pre>"; print_r($this->cart->contents()); die();
						$price = discount_forumula($product->price, $product->discount_value, $product->discount_type, $product->discount_from, $product->discount_till);
						// print_r($price."&nbsp;&nbsp;");
						if($price){
							$data['price']   		= $price;
							$data['valid_from']		= $product->discount_from;
							$data['valid_to']		= $product->discount_till;
							$data['discount_type'] 	= $product->discount_type;
							$data['discount_value'] = $product->discount_value;
							$gross_prce 			= ($price * $row[$product->pv_id]['qty']);
							$data['subtotal'] 		= $gross_prce;
							/* for api */
							$cart[$index]['price'] 			= (string) $price;
							$cart[$index]['discount_type']	= (string) $product->discount_type;
							$cart[$index]['discount_value'] = (string) $product->discount_value;
							$cart[$index]['subtotal'] 		= (string) $gross_prce;
							$cart[$index]['valid_from'] 	= (string) strtotime($product->discount_from);
							$cart[$index]['valid_to'] 		= (string) strtotime($product->discount_till);
							if($row[$product->pv_id]['discount'] == ""){
								// echo $row[$product->pv_id]['update_msg']; die();
								$msg[] = "Discount has been added";
								$cart[$index]['messages'][] = 'Discount has been added';
								$isUpdated = true;
							}elseif((string)$price != (string)$row[$product->pv_id]['price']){
								$msg[] = "Discount has been updated";
								$cart[$index]['messages'][] = 'Discount has been updated';
								$isUpdated = true;
							}
						}else{
							$data['price']   				= $product->price;
							$data['valid_from']				= "";
							$data['valid_to']				= "";
							$data['discount_type'] 			= "";
							$data['discount_value'] 		= "";
							
							$cart[$index]['price'] 			= (string) $product->price;
							$cart[$index]['valid_from'] 	= "";//(string) $product->discount_from;
							$cart[$index]['valid_to'] 		= "";//(string) $product->discount_till;
							$cart[$index]['discount_type'] 	= "";//(string) $product->discount_type;
							$cart[$index]['discount_value'] = "";//(string) $product->discount_value;
						}
					}else{
						$data['price']   				= $product->price;
						$data['valid_from']				= "";
						$data['valid_to']				= "";
						$data['discount_type'] 			= "";
						$data['discount_value'] 		= "";
						$gross_prce 					= ($product->price * $row[$product->pv_id]['qty']);
						$data['subtotal'] 				= $gross_prce;
						$cart[$index]['subtotal'] 		= (string) $gross_prce;
						$cart[$index]['valid_to'] 		= "";//(string) $product->discount_till;
						$cart[$index]['price'] 			= (string) $product->price;
						$cart[$index]['valid_from'] 	= "";//(string) $product->discount_from;
						$cart[$index]['discount_type'] 	= "";//(string) $product->discount_type;
						$cart[$index]['discount_value'] = "";//(string) $product->discount_value;
						if($row[$product->pv_id]['discount'] != ""){
							$msg[] = "Discount has been removed";
							$cart[$index]['messages'][] = 'Discount has been removed';
							$isUpdated = true;
						}
					}
					//echo  $product->shipping_price.' != '.$row[$product->pv_id]['ship_price'].'<br />';
					//echo '<pre>';print_r($product);echo '</pre>';
					//echo '<pre>';print_r($row[$product->pv_id]);echo '</pre>';die();
					//New Shipping Work.....
					if($is_shipping_changed){
						$where = array("shipping_id"=>$row[$product->pv_id]['ship_id']);
						$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,price,title,free_after",$where,"",'','',"","","","","",true);
						$ship_id = $shippingData->shipping_id;
						$ship_title = $shippingData->title;
						$free_after = $shippingData->free_after;
						$ship_price = $shippingData->price;
					}else{
						$ship_id = $product->shipping_id;
						$ship_title = $product->shipping_title;
						$free_after = $product->free_after;
						$ship_price  = $product->shipping_price;
					}
					
					if($free_after <= ($row[$product->pv_id]['price'] * $row[$product->pv_id]['qty']) && $free_after > 0){
						$data['shipping_id'] 	= $cart[$index]['shipping_id'] 		=  (string) $ship_id;
						$data['shipping_price'] = $cart[$index]['shipping_price'] 	=  "0";
						$data['shipping_title'] = $cart[$index]['shipping_title'] 	= 	$ship_title;
					}elseif(($ship_id != $row[$product->pv_id]['ship_id'] && $is_shipping_changed) || $ship_price != $row[$product->pv_id]['ship_price']){
						$data['shipping_id'] 	= $cart[$index]['shipping_id'] 		=  (string) $ship_id;
						$data['shipping_price'] = $cart[$index]['shipping_price'] 	=  (string) $ship_price;
						$data['shipping_title'] = $cart[$index]['shipping_title'] 	= 	$ship_title;
						if($ship_id == $row[$product->pv_id]['ship_id']){
							$msg[] = "Shipping price has been updated";
							$cart[$index]['messages'][] = 'Shipping has been updated';
							$isUpdated = true;
						}elseif($ship_id != $row[$product->pv_id]['ship_id'] && $is_shipping_changed){
							$msg[] = "Shipping has been updated";
							$cart[$index]['messages'][] = 'Shipping has been updated';
							$isUpdated = true;
						}
					}
					if($msg != ""){
						$data['update_msg'] = implode(", ", $msg);
					}
					if($callFrom != 'api'){
						$cart_id = $this->cart->update($data);
					}
				}else{
					$isUpdated = true;
					$msg = "";
					$data = array(
						'rowid' => $row[$product->pv_id]['row_id'],
						'qty'   => $product->qty
					);
					$cart[$index]['qty'] = $cart[$index]['quantity'] = $product->qty;
					$cart[$index]['quantity'] = $product->qty;
					if($product->qty == "0"){
						$msg = " got out of stock <br>";
						$cart[$index]['messages'][] = 'got out of stock';
					}else{
						$msg = "'s stock has been updated <br>";
						$cart[$index]['messages'][] = 'stock has been updated.';
					}
					$names[] = $product->name.$msg;
					if($callFrom != 'api'){
						$del_cart = $this->cart->update($data);
					}
				}
			}
			// echo"<pre>";print_r($this->cart->contents());die();
			// echo $isUpdated; die();
			$response['cart'] = $cart;
			$response['status'] = ($isUpdated)?0:1;
			$response['message'] = ($isUpdated)?'Updated':'None';
			if($callFrom != 'api'){
				if(isset($names)){
					$this->session->set_userdata("names", $names);
					redirect(base_url("cart"));
				}
				if($isUpdated){
					redirect(base_url("cart"));
				}
			}
			if($callFrom == 'api'){
				return $response;
			}
		}
	}
	public function cart_confirm($voucher_code="",$zip_code,$user_id,$cart_data,$callFrom="api",$cart_total=0){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'code'=>'000', 'cart'=>array(), 'errors'=>array(), 'code'=>'000', 'cart_total'=>0, 'tax'=>0, 'shipping'=>0, 'discount_amount'=>0);
		if($voucher_code != ''){
			$this->load->model("admin/Policies_Model");
			$voucher = $this->Policies_Model->getVoucher('', '', $voucher_code);
			$discount_amount = 0;
			$discounted_cart_toatl = 0;
			$isAdmin = false;
			$discount_voucher = $this->Cart_Model->calculateVoucher($voucher_code, $cart_data, $voucher, $cart_total, $discount_amount, $discounted_cart_toatl, $user_id, $isAdmin);
			if($discount_voucher['status'] == 1){
				$discount = $this->session->userdata('discount_data');
				$response['message'] = $discount_voucher['message'];
				$response['discount_amount'] = $this->cart->format_number($discount['discount_amount']);
				//$response['tax'] = $discount['tax'];
				$response['cart_total'] =  $discount['discounted_cart_total'];
			} else {
				$response['code'] = '002';
			}
			//echo "<pre>";print_r($discount);
		}
		$subtotal_seller = array();
		$i = 0;
		foreach($cart_data as $item){
			$shipping = $this->calculateCartItemShipping($item, $item['quantity'], $item['shipping_id'], $subtotal_seller);
			$subtotal_seller = $shipping['subtotal_seller'];
		}
		//echo "<pre>";print_r($subtotal_seller);
		$shipping_total = $this->calculateCartShipping($subtotal_seller);
		$response['shipping'] = $shipping_total;
		$response['tax'] = ($zip_code !="")?$this->cart->format_number($this->Cart_Model->getTax($zip_code,$response['cart_total'])):0;
		$response['cart_total'] = $this->cart->format_number($response['cart_total']);
		$cart_data = $this->updateShippingPerItem($cart_data, $subtotal_seller);
		$response['cart'] = $cart_data;
		//echo "<pre>";print_r($response);die();
		echo json_encode($response);
	}
	public function testCart($cart, $callFrom)
	{
		//echo "<pre>";print_r($cart); die();
		/* 
			$callFrom
			- core: do not return, update data
			- api: do not update session data, return data
		*/
		$response = array('status'=>1, 'message'=>'None', 'code'=>'000', 'cart'=>$cart);
		$data = array();
		$isUpdated = false;
		$subtotal_seller = array();
		$mapped = array();
		if(!empty($cart)){
			$i = 0;
			foreach($cart as $items){
				$product_variant_id[] = $items['id'];
				$row[$items['id']] = array("pv_id"=>$items['id'], "row_id"=>$items['rowid'],"qty"=>$items['qty'], "ship_id" => $items['shipping_id'], "ship_title" => $items['shipping_title'], "ship_price" => $items['shipping_price'], "con_id"=>$items['condition_id'], "original_price"=>$items['original'], "update_msg"=>(isset($items['update_msg']))?$items['update_msg']:'', "price"=>$items['price'], "discount"=>$items['discount_value'],"seller_id"=>$items['seller_id']);
				$mapped[$items['rowid']] = $i;
				$i++;
			}
			$products = $this->Product_Model->checkProductForDiscountByProductVariantId($product_variant_id);
			//echo"<pre>";print_r($products); die();
			// print_r($products);
			foreach($products['data'] as $product){
				/*echo "1";
				echo "<br>";
				print_r($product);*/
				$msg = array();
				// $isUpdated = false;
				// print_r($product);print_r($row[$product->pv_id]);die();
				$data['rowid'] 			= $row[$product->pv_id]['row_id'];
				$index = $mapped[$data['rowid']];
				if($product->qty >= $row[$product->pv_id]['qty']){
					if($product->price != $row[$product->pv_id]['original_price']){
						$data['price']   		= $cart[$index]['price'] 	= $product->price;
						$data['original']   	= $cart[$index]['original'] = $product->price;
						$msg[] = "Price has been updated";
						$cart[$index]['messages'][] = 'Price has been updated';
						$isUpdated = true;
					}
					if(strtotime(date("y-m-d")) >= strtotime($product->discount_from) && strtotime(date("y-m-d")) <= strtotime($product->discount_till)){
						// echo"<pre>"; print_r($this->cart->contents()); die();
						$price = discount_forumula($product->price, $product->discount_value, $product->discount_type, $product->discount_from, $product->discount_till);
						// print_r($price."&nbsp;&nbsp;");
						if($price){
							$data['price']   		= $price;
							$data['valid_from']		= $product->discount_from;
							$data['valid_to']		= $product->discount_till;
							$data['discount_type'] 	= $product->discount_type;
							$data['discount_value'] = $product->discount_value;
							$gross_prce 			= ($price * $row[$product->pv_id]['qty']);
							$data['subtotal'] 		= $gross_prce;
							/* for api */
							$cart[$index]['price'] 			= (string) $price;
							$cart[$index]['discount_type']	= (string) $product->discount_type;
							$cart[$index]['discount_value'] = (string) $product->discount_value;
							$cart[$index]['subtotal'] 		= (string) $gross_prce;
							$cart[$index]['valid_from'] 	= strtotime($product->discount_from);
							$cart[$index]['valid_to'] 		= strtotime($product->discount_till);
							if($row[$product->pv_id]['discount'] == ""){
								// echo $row[$product->pv_id]['update_msg']; die();
								$msg[] = "Discount has been added";
								$cart[$index]['messages'][] = 'Discount has been added';
								$isUpdated = true;
							}elseif($price != $row[$product->pv_id]['price']){
								$msg[] = "Discount has been updated";
								$cart[$index]['messages'][] = 'Discount has been updated';
								$isUpdated = true;
							}
						}else{
							$data['price']   				= $product->price;
							$data['valid_from']				= "";
							$data['valid_to']				= "";
							$data['discount_type'] 			= "";
							$data['discount_value'] 		= "";
							
							$cart[$index]['price'] 			= (string) $product->price;
							$cart[$index]['valid_from'] 	= "";//(string) $product->discount_from;
							$cart[$index]['valid_to'] 		= "";//(string) $product->discount_till;
							$cart[$index]['discount_type'] 	= "";//(string) $product->discount_type;
							$cart[$index]['discount_value'] = "";//(string) $product->discount_value;
						}
					}else{
						$data['price']   				= $product->price;
						$data['valid_from']				= "";
						$data['valid_to']				= "";
						$data['discount_type'] 			= "";
						$data['discount_value'] 		= "";
						$gross_prce 					= ($product->price * $row[$product->pv_id]['qty']);
						$data['subtotal'] 				= $gross_prce;
						$cart[$index]['subtotal'] 		= (string) $gross_prce;
						$cart[$index]['valid_to'] 		= "";//(string) $product->discount_till;
						$cart[$index]['price'] 			= (string) $product->price;
						$cart[$index]['valid_from'] 	= "";//(string) $product->discount_from;
						$cart[$index]['discount_type'] 	= "";//(string) $product->discount_type;
						$cart[$index]['discount_value'] = "";//(string) $product->discount_value;
						if($row[$product->pv_id]['discount'] != ""){
							$msg[] = "Discount has been removed";
							$cart[$index]['messages'][] = 'Discount has been removed';
							$isUpdated = true;
						}
					}
					//echo  $product->shipping_price.' != '.$row[$product->pv_id]['ship_price'].'<br />';
					//echo '<pre>';print_r($product);echo '</pre>';
					//echo '<pre>';print_r($row[$product->pv_id]);echo '</pre>';die();
					/*echo "<pre>";
					print_r($row);*/
					//die();
					$shipping = $this->calculateCartItemShipping($cart[$row[$product->pv_id]['row_id']],  $row[$product->pv_id]['qty'],  $row[$product->pv_id]['ship_id'], $subtotal_seller);
					$subtotal_seller = $shipping['subtotal_seller'];
					$sub_ship_total = $shipping['subtotal_seller'][$row[$product->pv_id]['seller_id']][$row[$product->pv_id]['ship_id']];
					$free_after_cal = $sub_ship_total['subtotal']+$sub_ship_total['shipping'];
					
					/*echo "<pre>";
					print_r($cart[$row[$product->pv_id]['row_id']]);
					echo "-----------Shipping-------------";
					print_r($shipping['item']);
					echo "-------------Free------------";
					print_r($free_after_cal);*/
					//die();
					$not_update = true;
					if(!empty($shipping['item'])){
						$free_after = $shipping['item']['shippingData']->free_after;
						$shipping_item_price = $shipping['item']['shipping_price'];
						$row_ids[$data["rowid"]]['shipping_price'] = $shipping_item_price;
						//echo "<hr>";
						$rowdata = array();
						if($free_after <= $free_after_cal && $free_after  > 0){
							foreach($row as $r){
								//echo "<pre>";print_r($r);echo "</pre>";
								if($r['ship_id'] == $data['shipping_id']){
									$row_ids[$data["rowid"]]['shipping_price'] = 0;
									$rowdata[] = array(
										'rowid'  => $r["row_id"],
										'shipping_id'  => $shipping['item']['shipping_id'],
										'shipping_title' => $shipping['item']['shipping_title'],
										'shipping_price' => 0,
										'shippingData' => $shipping['item']['shippingData'],
										"messages" =>""
									);
									$shipping_item_price = 0;
									/*echo "<pre>";
									print_r($r);
									echo"row: "; print_r($rowdata);*/
									$isUpdated = false;
								}
							}
							$row[$product->pv_id]['ship_price'] = 0;
							$this->cart->update($rowdata);
							$not_update = false;
							//echo "<pre>";print_r($this->cart->contents());die();
							//echo "<pre>";print_r($rowdata);
						}else if($shipping['item']['shipping_id'] != $row[$product->pv_id]['ship_id']){
							$data['shipping_id'] 	= $shipping['item']['shipping_id'];
							$data["shipping_title"] = $shipping['item']['shipping_title'];
							$data["shipping_price"] = $shipping_item_price;
							$row[$product->pv_id]['ship_id'] = $shipping['item']['shipping_id'];
							//die("here");
							$msg[] = "Shipping has been updated";
							$cart[$index]['messages'][] = 'Shipping has been updated';
							$isUpdated = true;
						}else if($row[$product->pv_id]['ship_price'] != $shipping_item_price && $free_after >= $free_after_cal){
							$row[$product->pv_id]['ship_id'] = $shipping['item']['shipping_id'];
							$msg[] = "Shipping price has been updated";
							$cart[$index]['messages'][] = 'Shipping has been updated';
							$isUpdated = true;
						}
						/*if($shipping_item['shipping']['shipping_data'][0]->free_after <= $subtotal_seller_shipping && $shipping_item['shipping']['shipping_data'][0]->free_after >0){
							$data["shipping_price"] = 0;
						}elseif($shipping_item['shipping']['shipping_id'] != $row[$product->pv_id]['ship_id']){
							$msg[] = "Shipping price has been updated";
							$cart[$index]['messages'][] = 'Shipping has been updated';
							$isUpdated = true;
						}else if($row[$product->pv_id]['ship_price'] != $data["shipping_price"]){
							$msg[] = "Shipping has been updated";
							$cart[$index]['messages'][] = 'Shipping has been updated';
							$isUpdated = true;
						}*/
						//$this->cart->update($rowdata);
					}
					//print_r($subtotal_seller_shipping);
					//die();
					if($msg != ""){
						$data['update_msg'] = implode(", ", $msg);
					}
					if($callFrom != 'api' && $not_update){
						$cart_id = $this->cart->update($data);
					}
				}else{
					$isUpdated = true;
					$msg = "";
					$data = array(
						'rowid' => $row[$product->pv_id]['row_id'],
						'qty'   => $product->qty
					);
					$cart[$index]['qty'] = $cart[$index]['quantity'] = $product->qty;
					$cart[$index]['quantity'] = $product->qty;
					if($product->qty == "0"){
						$msg = " got out of stock <br>";
						$cart[$index]['messages'][] = 'got out of stock';
					}else{
						$msg = "'s stock has been updated <br>";
						$cart[$index]['messages'][] = 'stock has been updated.';
					}
					$names[] = $product->name.$msg;
					if($callFrom != 'api'){
						$del_cart = $this->cart->update($data);
					}
				}
			}
			//die();
			// echo"<pre>";print_r($this->cart->contents());die();
			// echo $isUpdated; die();
			$response['cart'] = $cart;
			$response['status'] = ($isUpdated)?0:1;
			$response['message'] = ($isUpdated)?'Updated':'None';
			//echo "<pre>";print_r($response);die();
			if($callFrom != 'api'){
				if(isset($names)){
					$this->session->set_userdata("names", $names);
					redirect(base_url("cart"));
				}
				if($isUpdated){
					redirect(base_url("cart"));
				}
			}
			if($callFrom == 'api'){
				return $response;
			}
		}
	}
	public function testCart2($cart, $callFrom)
	{
		//echo "<pre>";print_r($cart); die();
		/* 
			$callFrom
			- core: do not return, update data
			- api: do not update session data, return data
		*/
		$response = array('status'=>1, 'message'=>'None', 'code'=>'000', 'cart'=>$cart);
		$isChanged = 0;
		$data = array();
		$subtotal = 0;
		$names = array();
		$free_after_seller = array();
		$subtotal_seller = array();
		if(!empty($cart)){
			//echo "<pre>";
			foreach($cart as $index=>$item){
				$product = $this->Product_Model->checkProductForDiscountByProductVariantId($item['id']);
				if($product['status'] == 1){
					$product = $product['data'][0];
					$subtotal = ($product->price * $item['qty']);
					$data = array(
						'rowid' => $item['rowid'],
						'price'=>$product->price,
						'original'=> $product->original,
						'qty'=> $item['qty'],
						'valid_from'=>"",
						'valid_to'=>"",
						'discount_type'=>"",
						'discount_value'=>"",
						"update_msg" =>"",
						"subtotal" => $subtotal
					);
					if($product->qty >= $item['qty']){
						if($product->price != $item['original']){
							$data["update_msg"] ="Price has been updated";
							$this->cart->update($data);	
							$isChanged = 1;
						}
						if(strtotime(date("y-m-d")) >= strtotime($product->discount_from) && strtotime(date("y-m-d")) <= strtotime($product->discount_till)){
							$price = discount_forumula($product->price, $product->discount_value, $product->discount_type, $product->discount_from, $product->discount_till);
							if($price){
								$data['price']   		= $price;
								$data['valid_from']		= $product->discount_from;
								$data['valid_to']		= $product->discount_till;
								$data['discount_type'] 	= $product->discount_type;
								$data['discount_value'] = $product->discount_value;
								$subtotal 				= ($price * $item['qty']);
								$data['subtotal'] 		= $subtotal;
								if($item['discount_value'] == ""){
									$data["update_msg"] = "Discount has been added";
									$isChanged = 1;
								}elseif($price != $item['price']){
									$data["update_msg"] = "Discount has been updated";
									$isChanged = 1;
								}
							}else{
								$data['price']  = $product->price;
							}
						}else{
							if($item['discount_value'] != ""){
								$data["update_msg"] = "Discount has been removed";
								$isChanged = 1;
							}
						}
						$this->cart->update($data);	
						/**$shipping = $this->calculateCartItemShipping($item,  $item['qty'],  $item['shipping_id'], $subtotal_seller);
						if(!empty($shipping['item'])){
							$subtotal_seller = $shipping['subtotal_seller'];
							$sub_ship_total = $shipping['subtotal_seller'][$item['seller_id']][$item['shipping_id']];
							$free_after_cal = $sub_ship_total['subtotal'];
							$free_after = $shipping['item']['shippingData']->free_after;
							$data = array("rowid"=>$item['rowid'],'shipping_id'=>$shipping['item']['shipping_id'],'shipping_title'=>$shipping['item']['shipping_title'],'shipping_price'=>$shipping['item']['shipping_price'],"updated_message"=>"");
							if($free_after > 0 && $free_after <= $free_after_cal){
								$free_after_seller[$item['seller_id']] = $sub_ship_total['row_id'];
							}else if($shipping['item']['shipping_id'] != $item['shipping_id']){
								$data["update_msg"] = "Shipping has been updated";
								$isChanged =1;
							}else if($item['shipping_price'] != $shipping['item']['shipping_price']){
								$data["update_msg"] =  "Shipping price has been updated";
								$isChanged =1;
							}
							$this->cart->update($data);	
						}*/
						$shippingCal = $this->shippingCal($item,$free_after_seller,$subtotal_seller);
						$isChanged = $shippingCal["isChanged"];
						$free_after_seller = $shippingCal["free_after_seller"];
						$subtotal_seller = $shippingCal["subtotal_seller"];
					}else{
						$data['qty'] = $product->qty;
						if($product->qty == "0"){
							$data['update_msg']= $product->name." got out of stock <br>";
							
						}else{
							$data['update_msg']= $product->name."'s stock has been updated <br>";
						}
						$names[] = $data['update_msg'];//$product->name.$msg;
						$this->cart->update($data);
						$isChanged = 1;
					}
				}
			}
			$isChanged = $this->cartShipping($free_after_seller);
			/*if(!empty($free_after_seller)){
				foreach($free_after_seller as $fas){
					foreach($fas as $row_id){
						$data = array('shipping_price'=>0,"update_msg"=>"","rowid"=>$row_id);
						$this->cart->update($data);
					}
				}
				$isChanged = 0;
			}*/
			$this->session->set_userdata("names", $names);
		}
		if($isChanged){
			//echo $isChanged;die();
			redirect(base_url("cart"));
		}
	}
	public function shippingCal($item,$free_after_seller,$subtotal_seller){
		$isChanged = 0;
		//echo $item['qty']."<br/>";
		$shipping = $this->calculateCartItemShipping($item,  $item['qty'],  $item['shipping_id'], $subtotal_seller);
		//echo "<pre>";print_r($shipping);
		if(!empty($shipping['item'])){
			$subtotal_seller = $shipping['subtotal_seller'];
			$sub_ship_total = $shipping['subtotal_seller'][$item['seller_id']][$item['shipping_id']];
			$free_after_cal = $sub_ship_total['subtotal'];
			$free_after = $shipping['item']['shippingData']->free_after;
			//print_r($shipping);
			$data = array("rowid"=>$item['rowid'],'shipping_id'=>$shipping['item']['shipping_id'],'shipping_title'=>$shipping['item']['shipping_title'],'shipping_price'=>$shipping['item']['shipping_price'],"updated_message"=>"");
			//echo "Free After:".$free_after." - Cal: ".$free_after_cal;
			if($free_after > 0 && $free_after <= $free_after_cal){
				$free_after_seller[$item['seller_id']] = $sub_ship_total['row_id'];
			}else if($shipping['item']['shipping_id'] != $item['shipping_id']){
				$data["update_msg"] = "Shipping has been updated";
				$isChanged = 1;
			}else if($item['shipping_price'] != $shipping['item']['shipping_price']){
				$data["update_msg"] =  "Shipping price has been updated";
				$isChanged = 1;
			}
			$this->cart->update($data);	
		}
		return array('isChanged'=>$isChanged,"free_after_seller"=>$free_after_seller,"subtotal_seller"=>$subtotal_seller);
	}
	public function cartShipping($free_after_seller){
		$isChanged = "";
		if(!empty($free_after_seller)){
			foreach($free_after_seller as $fas){
				foreach($fas as $row_id){
					$data = array('shipping_price'=>0,"update_msg"=>"","rowid"=>$row_id);
					$this->cart->update($data);
				}
			}
			$isChanged = 0;
		}
		return $isChanged;
	}
}