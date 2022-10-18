<?php 
class Debug extends CI_Controller{
	var $data = array();
	function __construct(){
		parent::__construct();
	} 

	public function index(){
		$r = time() * rand(1, 9);
		$random_name = alphaID($r,false,false, 'zab.ee');
		echo $random_name;die();
	}
	public function phpInfo(){
		echo phpinfo();
	}
	public function saveNoti(){
		$sale = rand(1,9)*10;
		$this->load->model("Utilz_Model");
		$userid = '1';
		$created_by = '5968b057a7e86';
		$usertype = 0;
		$notification_type = 1;
		$message = 'Product Reject';
		$link = 'http://localhost/zabee/portal/join_us';
		$this->Utilz_Model->saveNotification($userid, $created_by, $usertype, $notification_type, $message, $link);
	}
	public function getNoti($userid, $usertype, $status = 0, $isCount = 0){
		$noti = $this->Utilz_Model->getNotiFormatted($userid, $usertype, $status, $isCount);
		echo $noti;
	}
	
	public function check(){ 
		echo $_SESSION['view'];
	}
	
	public function cal_tax()
	{
		$this->load->model("Utilz_Model");
		$zip = '80111';
		$amount = '1500';
		if($zip != '' && $amount > 0){
			$params = '?key='.$this->config->item('tax_api_key').'&postalcode='.$zip;
			$taxResponse = $this->Utilz_Model->curlRequest($params, $this->config->item('tax_api_url'), false, false);
			if($taxResponse->rCode == 100){
				$result = $taxResponse->results;
				if(count($result)>0){
					$amount = $result[0]->taxSales * $amount;
				} else {
					$amount = $this->config->item('vat_tax');
				}
			} else {
				$amount = $this->config->item('vat_tax');
			}
			$amount = $this->config->item('vat_tax');
		}
		echo $amount;
	}

	public function stripe(){
		$this->load->library('Stripe', 'stripe');
		$this->load->model('User_Model');
		$stripe_key = $this->config->item('stripe_key');
		$card_name = 'Test Card 1';
		$email = 'mobeen.shakil@gmail.com';
		$customer_id = 'cus_G7CulzOmJy0jym';
		$card_id = 'card_1FayUPLKy1HumDLdxtJgzw4o';
		$card = array(
			'name' 		=> 'Mobeen Shakil',
			'number' 	=> '4242424242424242',
			'exp_month' => 4,
			'exp_year' 	=> 2020,
			'cvc' 		=>  123
		);
		
		/* Creating transaction */
		$package = new stdClass();
		$package->package_price = 51*100;
		$package->package_name = 'Purchasing items on Zabee';
		$paymentResponse = $this->stripe->doTransaction($card_id, $package, $email,$stripe_key, $customer_id);
		echo"<pre>";print_r($paymentResponse);die();
	}

	public function stripe_customer(){
		$this->load->library('Stripe', 'stripe');
		$this->load->model('User_Model');
		$stripe_key = $this->config->item('stripe_key');
		$email = 'mobeen.shakil@gmail.com';
	
		$paymentResponse = $this->stripe->createCustomer($email, $stripe_key);
		echo"<pre>";print_r($paymentResponse);die();
	}
	
	public function stripe_card(){
		$this->load->library('Stripe', 'stripe');
		$this->load->model('User_Model');
		$stripe_key = $this->config->item('stripe_key');
		$email = 'mobeen.shakil@gmail.com';
		$customer_id = 'cus_GqeLh5efvUNQHV';
		$card_name = 'Test Card 1';
		$card = array(
			'name' 		=> 'Mobeen Shakil',
			'number' 	=> '4242424242424242',
			'exp_month' => 4,
			'exp_year' 	=> 2021,
			'cvc' 		=>  123
		);
	
		$paymentResponse = $this->stripe->saveCard($card, $card_name, $customer_id, $stripe_key);
		echo"<pre>";print_r($paymentResponse);die();
	}

	public function stripe_auth(){
		$this->load->library('Stripe', 'stripe');
		$this->load->model('User_Model');
		$stripe_key = $this->config->item('stripe_key');
		$card_name = '';
		$email = 'mobeen.shakil@gmail.com';
		$customer_id = 'cus_GqeLh5efvUNQHV';
		$card_id = 'card_1GIx3YF83YP8BgzrSVUV3kRU';

		$token = $card_id;
		$package = new stdClass();
		$package->package_price = 100*100;
		$package->package_name = 'Purchasing items on Zabee';
		$paymentResponse = $this->stripe->doTransaction($token, $package, $email,$stripe_key, $customer_id);
		echo"<pre>";print_r($paymentResponse);die();
	}

	public function stripe_charge(){
		$this->load->library('Stripe', 'stripe');
		$this->load->model('User_Model');
		$stripe_key = $this->config->item('stripe_key');
		$charge = 'ch_1GIx3tF83YP8BgzrPG2CGWFm';
		$amount = 0;
		$paymentResponse = $this->stripe->captureCharge($charge, $stripe_key, $amount);
		echo"<pre>";print_r($paymentResponse);die();
	}
	
	public function getStripeCards($card_id = 0){
		$this->load->model('User_Model');
		$user_id = $this->session->userdata('userid');
		$cards = $this->User_Model->getStripeCards($user_id, $card_id);
		echo"<pre>";print_r($cards);die();
	}

	public function updateStripeCards($card_id = 0){
		$this->load->model('User_Model');
		$this->load->library('Stripe', 'stripe');
		$stripe_key = $this->config->item('stripe_key');
		$user_id = $this->session->userdata('userid');
		$card_id = 'card_1Fc4ivLKy1HumDLdMM4744U6';
		$customer_id = 'cus_G8LNa9WXRJj39P';
		$data = array('exp_month'=> 12, 'exp_year'=>2020);
		$cards = $this->stripe->updateCard($data, $card_id, $customer_id, $stripe_key);
		echo"<pre>";print_r($cards);die();
	}
	
	public function capture_product_image(){
		$response = array('message'=>'error','status'=>0);
		$this->load->model('Debug_Model');
		$this->load->database();
		$media = $this->Debug_Model->getProductMediaByLocation('0');
		$image_type = 'product';
		if(count($media) > 0){
			foreach($media AS $product){
				$this->load->model("Utilz_Model");
				$r = time() * rand(1, 9);
				$random_name = alphaID($r,false,false, 'zab.ee');
				$sp_id = ($product->sp_id)?$product->sp_id:'00';
				$filenameIn  = $product->iv_link;
				$filenameOut = $r.'_'.$random_name.'_'.$product->product_id.'_'.$sp_id;
				$params = array(
					'image_link' => $filenameIn,
					'image_type' => $image_type,
					'filenameOut' => $filenameOut,
				);
				$upload_server = $this->config->item('media_url').'/file/download_file';
				$dlResponse = $this->Utilz_Model->curlRequest($params, $upload_server, true, false);
				if($dlResponse->status == 1){
					$response['status'] = 1;
					$response['message'] = 'OK';
					$response['images']['captured'][] = $dlResponse->images->captured->iv_link;
					$this->db->where('media_id', $product->media_id)->update('tbl_product_media', array('iv_link'=>$dlResponse->images->captured->iv_link, 'thumbnail'=>$dlResponse->images->captured->thumbnail,'is_local'=>'1', 'dl_status'=>'1'));
				} else {
					$this->db->where('media_id', $product->media_id)->update('tbl_product_media', array('dl_status'=>'2'));
					$response['images']['errored'][] = $filenameOut;
				}
			}
		}
		echo json_encode($response);
	}
	
	public function upload_media(){
		header('Access-Control-Allow-Origin: *');
		
		$product_image= array();
		$config = array();
		$params = array();
		if(isset($_FILES["file"])){
			$config['upload_path'] = 'product';
			$config['upload_thumbnail_path'] = 'product/thumbs';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['encrypt_name'] = true;
			$config['quality'] = "100%";
			$config['overwrite'] = FALSE;
			$config['remove_spaces'] = TRUE;
			for($i = 0; $i < count($_FILES["file"]['name']); $i++){
				$params['file'] = curl_file_create($_FILES['file']['tmp_name'][$i], $_FILES['file']['type'][$i], $_FILES['file']['name'][$i]);
				$params['image_type'] = 'product';
				$params['filesize'] = $_FILES['file']['size'][$i];
				$params['config'] = json_encode($config);
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				echo '<pre>';print_r($file);die();			
			}
		}else{
			return false;
		}
		return $product_image;
	}

	public function orderrr_detail($order_id)
	{
		$result = $this->db->select("prd.product_name AS Product, td.price AS 'Unit Price', td.qty AS 'Qty', td.item_shipping_amount AS Shipping, td.tax_amount AS Tax, td.item_gross_tax_amount AS 'Gross Tax', td.item_gross_amount AS 'Gross Product Amount', td.item_total AS 'Product Total'")
		->from('tbl_product prd')
		->join('tbl_transaction_details td', 'prd.product_id = td.product_id')
		->where('order_id', $order_id)->get()->result();
		echo "<pre>"; print_r($result);
	}

	public function refundProcess(){
		$error = array();

		$this->load->library('Stripe', 'stripe');
		$stripe_key = $this->config->item('stripe_key');

		$orders = $this->db->select("detail.id AS 'trans_details_Id', tran.created, tran.order_id, tran.transaction_id, tran.is_capture, DATEDIFF(SYSDATE(),tran.created) AS days, detail.item_total AS total")
		->from("tbl_transaction tran")
		->join("tbl_transaction_details detail", "tran.order_id = detail.order_id")
		->where("(DATEDIFF(SYSDATE(), tran.created) >= 7)")
		->where("detail.status = '0'")->get()->result();
		foreach($orders as $order){
			if($order->is_capture == "1"){//IF ORDER IS CAPTURED -> REFUND PROCESS
				$amount = (int)($order->total*100);
				$token = $this->stripe->getRefund($order->transaction_id, $amount, $stripe_key);
				if($token['status'] == 1){
					$this->db->where("id",$order->trans_details_Id)->update(DBPREFIX.'_transaction_details', array('status'=>'3', "is_cancellation_pending"=>'1', "is_cancel"=>'2'));
				}else{
					$error[] = array("trans_details_id"=>$order->trans_details_Id, "message"=>"Unable to refund amount: ".$amount);
				}
			}else{//UNCAPTURED ORDERS -> STATUS CHANGED TO CANCEL
				$this->db->where("id",$order->trans_details_Id)->update(DBPREFIX.'_transaction_details', array('status'=>'2', "is_cancellation_pending"=>'1', "is_cancel"=>'1'));
			}
		}
		if($error){
			echo"<pre>"; print_r($error);
		}else{
			echo"all goes well";
		}
	}

	public function createDefaultWishlist(){
		$user_ids = $this->db->select('userid')
		->from(DBPREFIX.'_users')
		->where('userid not in (SELECT user_id from tbl_wishlist_categoryname)')->get()->result();
		if(count($user_ids) > 0){
			$ids = array();
			$ids['category_name'] = "like";
			$ids['is_delete'] = "0";
			foreach($user_ids as $userid){
				$ids['user_id'] = $userid->userid;
				$this->db->insert(DBPREFIX."_wishlist_categoryname", $ids);
			}
			echo"<pre>"; print_r(array("status" => "1", "message" => "transaction success")); die();
		}else{
			echo"<pre>"; print_r(array("status" => "0", "message" => "all userids are already in the wishlist table")); die();
		}
	}
	public function test_lib(){
		echo MD5('admin_321');
		//$this->cart->rowid();
	}
	
	public function cart(){
		$cart = array();
		foreach($this->cart->contents() as $key => $val){
			//echo '<pre>';print_r($val);die();
			$item = array(
				"product_variant_id"=> $val['id'],
				"product_id"=> $val['prd_id'],
				"condition_id"=> $val['condition_id'],
				"seller_product_id"=> $val['sp_id'],
				"quantity"=> $val['qty'],
				"price"=> $val['price'],
				"product_title"=> $val['name'],
				"product_condition"=> $val['condition'],
				"product_image"=> $val['img'],
				"options"=> $val['options'],
				"already_saved"=> $val['already_saved'],
				"variants"=> $val['variant'],
				"upc_code"=> $val['upc_code'],
				"seller_sku"=> $val['seller_sku'],
				"seller_id"=> $val['seller_id'],
				"is_local"=> $val['is_local'],
				"max_quantity"=> $val['max_qty'],
				"sell_quantity"=> $val['sell_quantity'],
				"shipping_id"=> $val['shipping_id'],
				"shipping_title"=> $val['shipping_title'],
				"shipping_price"=> $val['shipping_price'],
				"available_shippings"=> '',
				"shippingData"=> $val['shippingData'],
				"isChecked"=> $val['max_qty'],
				"warehouse_id"=> $val['max_qty'],
				"original"=> $val['original'],
				"variant_ids"=> $val['variant_ids'],
				"valid_to"=> $val['valid_to'],
				"valid_from"=> $val['valid_from'],
				"discount_type"=> $val['discount_type'],
				"discount_value"=> $val['discount_value']
			);
			array_push($cart, $item);
		}
		echo json_encode($cart);
	}
	public function email_config()
	{
		$from = $this->config->item('info_email');
		$name = $this->config->item('author');
		$to = "mobeen.shakil@gmail.com";
		$subject = "Test Email";
		$msg = "Sb sahi chal rha hay.";
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
		if($this->email->send()){
			echo $this->email->print_debugger();
			print_r("done");
			//return true;
		}else{
			echo $this->email->print_debugger();
		}
	}
	public function db_error(){
		try{
			$query = $this->db->select("*")->from("tbl_rec")->get()->result();
			if(!$query){
				print_r(array("msg"=>"","error"=>$query));
			}
		}catch(Exception $e){
			echo "asd";
			$query = $this->db->error(); 
			print_r(array("msg"=>$e,"error"=>$query));
		}catch(Error $e){
			$query = $this->db->error(); 
			echo "<pre>";print_r(array("msg"=>$e,"error"=>$query));
		}
	}
	public function get_hubx_products($pageNumber="1",$pageSize="1",$onlyActive="true"){
		$access_token = "eyJhbGciOiJSUzI1NiIsImtpZCI6IkF5YjAwUTBfY09peFlUWmZUTldFX1EiLCJ0eXAiOiJKV1QifQ.eyJuYmYiOjE2MjU0ODUxNTEsImV4cCI6MTYyNTU3MTU1MSwiaXNzIjoiaHR0cHM6Ly9odWJ4LWF1dGhlbnRpY2F0aW9uYXBpLWRldi5henVyZXdlYnNpdGVzLm5ldCIsImF1ZCI6WyJodHRwczovL2h1YngtYXV0aGVudGljYXRpb25hcGktZGV2LmF6dXJld2Vic2l0ZXMubmV0L3Jlc291cmNlcyIsImh1YngtY3VzdG9tZXItYXBpLXRlc3QiXSwiY2xpZW50X2lkIjoiM2JkOGI5MDI3M2UzNGZkN2E0NWRiODg3YzhkOWQxMTMiLCJodHRwczovL3d3dy5odWJ4LmNvbS9hcHBfbWV0YWRhdGEiOiJ7XCJjYXJkQ29kZVwiOlwiMjAwMTc0MVwiLFwiY3VzdG9tZXJJZFwiOlwiNDhBNDJGQzEtMTIyNC00NDg2LTg1NjgtQzhCOUMwRTBCREE0XCJ9Iiwic2NvcGUiOlsiaHVieC1jdXN0b21lci1hcGktdGVzdCJdfQ.fLKgzHnr8112dHa59v5SqF01WO9jPIDZbqzNyN9XEcDoLKPUOiKXci-cAVHhHhuHERbbRfPhNHK3WXkVdrMLm8zPeww4ncokZNb2DRLw9_3Thvrkc1mtA7GToTwR5CSWreHwnOwpb3modyKGYoBN7RGFbJAhlhExJYSvIfcsP1csfnJuiewyGSBM2xGadDqwNwfgw1fEFEOJlzHpUFfGmfUEjfqTmicSkyR3YdfHcbFBJdU2j8hrUp5lsO1I_A9A7uFnsia8FCqWQmOg5zzAG2_Qg6d9of6PRhoHhzd-T9Xmnm16K6jLw1La00tERiOwu7UKiGYZIptoCNs40Toy6w";
		$headers = array("Authorization:Bearer ".$access_token);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"http://hubx-customerapi-staging.azurewebsites.net/api/products?pageNumber=".$pageNumber."&pageSize=".$pageSize."&onlyActive=".$onlyActive);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS,"postvar1=value1&postvar2=value2&postvar3=value3");

		// In real life you should use something like:
		// curl_setopt($ch, CURLOPT_POSTFIELDS, 
		//          http_build_query(array('postvar1' => 'value1')));

		// Receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($ch);

		curl_close ($ch);

		// Further processing ...
		if ($server_output) {
			$server_output = json_decode($server_output);
			//echo "<pre>";print_r($server_output); 
			if($this->session->userdata['user_type'] == 1){
				$seller_id = "60dc1beac4485";
			}else{
				$seller_id = $this->session->userdata['userid'];
			}
			foreach($server_output->products as $o){
				$attr = $o->attributes;
				if($o->moq == 1 && !empty($attr)){
					$data = array("hubx_id"=>$o->id,
									"seller_id"=>$seller_id,
									"manufacturer"=> $o->manufacturer,
									"description"=> $o->description,
									"mpn"=> $o->mpn,
									"moq"=> $o->moq,
									"mxq"=> $o->mxq,
									"unit_price"=> $o->unitPrice,
									"part_number"=> $o->partNumber,
									"lead_time_days"=> $o->leadTimeDays,
									"availability"=> $o->availability,
									"exw"=> $o->exw,
									"is_active"=> $o->isActive,
									"attributes"=> ($o->attributes)?serialize($o->attributes):"",
									"comments"=> ($o->comments)?serialize($o->comments):"",
									"prices"=> ($o->prices)?serialize($o->prices):"");
					//echo "<pre>";print_r($data);print_r($o->attributes);
					foreach($attr as $a){
						$a = (array)$a;
						if(in_array("Condition",$a)){
							if($a['value'] !=""){
								$new = array("New","New Factory Sealed");
								$mfr = array("Samsung Certified Pre-Owned","Factory Refurbished","Refurbished - Off Lease","3rd Party Refurbished");
								$like_new = array("New Open Box","Store Return - As is","New Re-Imaged","Store Return - 3rd Party Refurbished","Store Return - 3rd Party Refurbished NEW OPEN BOX");
								$very_good = array("Renewed Grade A","Store Return - 3rd Party Refurbished B GRADE");
								$good = array("Renewed Grade B");
								$fair = array("Factory Refurbished Scratch & Dent","Renewed Grade C");
								if(in_array($a['value'],$new)){
									$data['condition_id'] = 1;
								}else if(in_array($a['value'],$mfr)){
									$data['condition_id'] = 2;
								}else if(in_array($a['value'],$like_new)){
									$data['condition_id'] = 3;
								}else if(in_array($a['value'],$very_good)){
									$data['condition_id'] = 4;
								}else if(in_array($a['value'],$good)){
									$data['condition_id'] = 5;
								}else if(in_array($a['value'],$fair)){
									$data['condition_id'] = 6;
								}else{
									$data['condition_id'] = 0;
								}
							}
							
						}
					}
					/*$variant = $this->getVariantByHubxTitle($o->description);
					if($variant[0]->v_cat_id !=""){
						$data['v_cat_id'] = $variant[0]->v_cat_id;
						$data['v_id'] = $variant[0]->v_id;
					}*/
					$this->db->insert("tbl_hubx_product",$data);
				}
			}
		} else { 
			echo "error";
		}
	}
	public function getVariantByHubxTitle($title="",$group_by="",$select="GROUP_CONCAT(DISTINCT v.v_cat_id) as v_cat_id,GROUP_CONCAT(DISTINCT v.v_id) as v_id"){
		$this->load->model("Product_Model");		
		$word = explode(" ",$title);
		$tablename = DBPREFIX."_variant v";
		$join = array(DBPREFIX."_variant_category vc"=>"vc.v_cat_id = v.v_cat_id");
		$v_cat_id = $this->Product_Model->getData($tablename,$select,$word,"v.v_title",$group_by,"",$join,"","","","");
		return $v_cat_id;
	}
	public function insertHubxProduct(){
		ini_set('max_execution_time', '300'); 
		for($i=1; $i<=199; $i++){
			$this->get_hubx_products($i,10);
		}
	}
	public function getHubPro(){
		//attributes,comments,prices
		echo "<pre>";
		$pro = $this->db->select("*")->from("tbl_hubx_product")->get()->result();
		//->where(array("attributes !="=>"","comments !="=>"","prices !="=>""))->limit(10)->get()->result();
		//echo $this->db->last_query();
		$condition = array();
		foreach($pro as $p){
			$attr = unserialize($p->attributes);
			$comm = unserialize($p->comments);
			$prices = unserialize($p->prices);
			//print_r($p);
			if($attr){
				foreach($attr as $a){
					$a = (array)$a;
					// /print_r($a);
					if(in_array("Warranty",$a)){
						$check = $this->db->select("id")->from("tbl_warranty")->where("warranty",$a['value'])->get()->row();
						if(!$check){
							$wdata = array("warranty"=>$a['value']);
							$this->db->insert("tbl_warranty",$wdata);
						}
						echo $a['value']."<br>";
					}
					/*if(in_array("Packaging",$a)){
						echo $a['value']."<br>";
					}
					if(in_array("Condition",$a)){
						if(!in_array($a['value'],$condition) && $a['value'] !=""){
							//print_r($condition);
							//echo $a['value']."<hr>";
							$condition[] = $a['value'];
						}
						//echo $a['value']."<br>";
					}*/
					/*if(in_array("Restrictions",$a)){
						echo $a['value']."<br>";
					}*/
				}
			}
			//echo "<pre>";
			/*echo "<br> ------------------attributes----------------------";
			print_r($attr);
			echo "<br> ------------------comments----------------------";
			print_r($comm);
			echo "<br> ------------------prices----------------------";
			print_r($prices);
			echo "<hr>";*/
		}
		//print_r($condition);
	}
	public function testHubxLib($user_id){
		$this->db->select('hubx_info')->from('tbl_users');
		$this->db->where('userid', $user_id);
		$query = $this->db->get()->row();
		if(isset($query->hubx_info)){
			$return = json_decode($query->hubx_info);
			$time = time();
			if($time > $return->expires_in){
				$return = $this->getAccessToken($user_id);
			}
		}else{
			$return = $this->getAccessToken($user_id);
		}
		return $return;
	}
	public function getAccessToken($user_id){
		$access_token = $this->hubx->getAccessToken();
		$access_token = json_decode($access_token);
		$time = time()+$access_token->expires_in;
		$access_token->expires_in = $time;
		$access_token = json_encode($access_token);
		$this->db->where('userid',$user_id);
		$this->db->update(DBPREFIX."_users",array("hubx_info"=>$access_token));
		return $access_token;
	}
	public function checkToken(){
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');
		$hubx_info = $this->session->userdata('hubx_info');
		$access_token = "";
		$expires_in = "";
		$time = time();
		$params = array('user_id'=>$user_id,"user_type"=>$user_type,"client_id"=>$this->config->item("hubx_client_id"),"client_secret"=>$this->config->item("hubx_client_secret"));
		$this->load->library('Hubx',$params);
		if($hubx_info){
			$hubx_info = json_decode($hubx_info);
			if($time > $hubx_info->expires_in){
				$hubx_info = $this->getAccessToken($user_id);
				$hubx_info = json_decode($hubx_info);
			}
			
		}else{
			$hubx_info = $this->getAccessToken($user_id);
			$hubx_info = json_decode($hubx_info);
		}
		$this->session->set_userdata('access_token',$hubx_info->access_token);
		$this->session->set_userdata('expires_in',$hubx_info->expires_in);
	}
	public function insertHubxProducts($pageNumber=1,$pageSize=100){
		$this->checkToken();
		$access_token = $this->session->userdata("access_token");
		if($access_token){
			$server_output = $this->hubx->get_hubx_product_list($access_token,$pageNumber,$pageSize,"true");
			if($this->session->userdata('user_type') == 1){
				$seller_id = "60dc1beac4485";
			}else{
				$seller_id = $user_id;
			}
			if($server_output){
				$server_output = json_decode($server_output);
				foreach($server_output->products as $o){
					$attr = $o->attributes;
					if($o->moq == 1 && !empty($attr)){
						$profit = ($o->unitPrice/100)*15;
						$price = $o->unitPrice+$profit;
						$data = array("hubx_id"=>$o->id,
										"seller_id"=>$seller_id,
										"manufacturer"=> $o->manufacturer,
										"description"=> $o->description,
										"mpn"=> $o->mpn,
										"moq"=> $o->moq,
										"mxq"=> $o->mxq,
										"unit_price"=> $price,
										"part_number"=> $o->partNumber,
										"lead_time_days"=> $o->leadTimeDays,
										"availability"=> $o->availability,
										"exw"=> $o->exw,
										"is_active"=> $o->isActive,
										"attributes"=> ($o->attributes)?serialize($o->attributes):"",
										"comments"=> ($o->comments)?serialize($o->comments):"",
										"prices"=> ($o->prices)?serialize($o->prices):"");
						//echo "<pre>";print_r($data);print_r($o->attributes);
						foreach($attr as $a){
							$a = (array)$a;
							if(in_array("Condition",$a)){
								if($a['value'] !=""){
									$new = array("New","New Factory Sealed");
									$mfr = array("Samsung Certified Pre-Owned","Factory Refurbished","Refurbished - Off Lease","3rd Party Refurbished");
									$like_new = array("New Open Box","Store Return - As is","New Re-Imaged","Store Return - 3rd Party Refurbished","Store Return - 3rd Party Refurbished NEW OPEN BOX");
									$very_good = array("Renewed Grade A","Store Return - 3rd Party Refurbished B GRADE");
									$good = array("Renewed Grade B");
									$fair = array("Factory Refurbished Scratch & Dent","Renewed Grade C");
									$data['condition'] = $a['value'];
									if(in_array($a['value'],$new)){
										$data['condition_id'] = 1;
									}else if(in_array($a['value'],$mfr)){
										$data['condition_id'] = 2;
									}else if(in_array($a['value'],$like_new)){
										$data['condition_id'] = 3;
									}else if(in_array($a['value'],$very_good)){
										$data['condition_id'] = 4;
									}else if(in_array($a['value'],$good)){
										$data['condition_id'] = 5;
									}else if(in_array($a['value'],$fair)){
										$data['condition_id'] = 6;
									}else{
										$data['condition_id'] = 0;
									}
								}
								
							}
						}
						//$variant = $this->getVariantByHubxTitle($o->description);
						//if($variant[0]->v_cat_id !=""){
						//	$data['v_cat_id'] = $variant[0]->v_cat_id;
						//	$data['v_id'] = $variant[0]->v_id;
						//}
						$this->db->insert("tbl_hubx_product",$data);
						echo "<pre>";print_r($data);
						echo "<hr>";
					}
				}	
			}else{
				echo json_encode(array("code"=>00,"msg"=>"Empty Data","status"=>0));
			}
		}else{
			echo json_encode(array("code"=>01,"msg"=>"No access to hubx","status"=>0));
		}
	}
	public function orderHubxProduct(){
		$this->checkToken();
		$access_token = $this->session->userdata("access_token");
		$cart = $this->cart->contents();
		$hubx_id = array_column($cart,"hubx_id","rowid");
		print_r($hubx_id);
		echo "<pre>";print_r($cart);
		$data = array("comments"=>"string",
			"terms"=> "string",
			"billingAddressCode"=> "string",
			"shippingAddressCode"=> "string",
			"billingAddress"=> array(
			  "line1"=> "string",
			  "line2"=> "string",
			  "country"=> "string",
			  "city"=> "string",
			  "state"=> "string",
			  "zipCode"=> "string"
			),
			"shippingAddress"=> array(
			  "companyName"=> "string",
			  "recipientName"=> "string",
			  "recipientPhoneNumber"=> "string",
			  "line1"=> "string",
			  "line2"=> "string",
			  "country"=> "string",
			  "city"=> "string",
			  "state"=> "string",
			  "zipCode"=> "string"
			),
			"shippingCost"=> 0,
			"details"=> array(array(
				"vendorPartNumber"=> "string",
				"quantity"=> 0,
				"unitPrice"=> 0,
				"buyerPartNumber"=> "string",
				"itemDescription"=> "string",
				"unitOfMeasure"=> "Each",
				"requestedDeliveryDate"=> "2020-04-23T19:54:29.497Z",
				"requestedShipDate"=> "2020-04-23T19:54:29.497Z"
			  )
			),
			"purchaseOrdernumber"=> "string",
			"creationDate"=> "2020-04-23T19:54:29.497Z",
			"version"=> 0
		);
		print_r($data);
	}
	public function updateHubxList(){
		$cli = $this->input->is_cli_request();
		$date 					= date('Y-m-d H:i:s');
		$date_utc 				= gmdate("Y-m-d\TH:i:s");
		$date_utc 				= str_replace("T"," ",$date_utc);
		if($cli){
			error_reporting(0);
            ini_set('display_errors', 0);
		}
		$user_id = $this->config->item("hubx_seller_id");
		$user_type = 2;
		$this->load->model("Product_Model");
		$hubx_info = $this->Product_Model->getData(DBPREFIX."_users","hubx_info",array("userid"=>$user_id),"","","","","","","","",true);
		$hubx_info = $hubx_info->hubx_info;
		$access_token = "";
		$expires_in = "";
		$params = array('user_id'=>$user_id,"user_type"=>$user_type,"client_id"=>$this->config->item("hubx_client_id"),"client_secret"=>$this->config->item("hubx_client_secret"));
		$this->load->library('Hubx',$params);
		$hubx_info = $this->checkAccessToken($hubx_info,$user_id);
		ini_set('max_execution_time', '3000'); 
		$this->db->query('SET SESSION group_concat_max_len = 1000000');
		$zabee_hubx_data = $this->Product_Model->getData(DBPREFIX."_hubx_product","GROUP_CONCAT(hubx_id) as hubx_id","","","","","","","","",true);
		//echo "<pre>";print_r($zabee_hubx_data);
		$zabee_hubx_id = explode(",",$zabee_hubx_data[0]->hubx_id);
		$zabee_hubx_id = array_flip($zabee_hubx_id);
		//echo "<pre>";print_r($zabee_hubx_id);
		//die();
		$loopCondition = 2;
		for($i=1; $i<=$loopCondition; $i++){
			$server_output = $this->hubx->get_hubx_product_list($hubx_info->access_token,$i,1000,"true");	
			$server_output = json_decode($server_output);
			//print_r($server_output);
			if(isset($server_output->products)){
				$loopCondition = $server_output->pagination->totalPages;
				foreach($server_output->products as $o){
					$profit = ($o->unitPrice/100)*15;
					$price = $o->unitPrice+$profit;
					if(isset($zabee_hubx_id[$o->id])){
						$updateData = array("updated_date"=>$date_utc,"moq"=>$o->moq,"mxq"=>$o->mxq,"unit_price"=>$price,"availability"=>$o->availability,"is_active"=>$o->isActive);
						$this->db->where(array("hubx_id"=>$o->id));
						$this->db->update(DBPREFIX."_hubx_product",$updateData);
						//echo $this->db->last_query();die();

						$this->db->set('quantity', $o->availability.'+sell_quantity', FALSE);
						$this->db->set('price', $price);
						$this->db->set('approve', (String)$o->isActive);
						$this->db->set("updated_date",$date_utc);
						$this->db->where(array("hubx_id"=>$o->id));
						$this->db->update(DBPREFIX."_product_inventory");
					}else{
						$attr = $o->attributes;
						if($o->moq == 1 && !empty($attr)){
							$data = array(	"created_date"=>$date_utc,
											"hubx_id"=>$o->id,
											"seller_id"=>$user_id,
											"manufacturer"=> $o->manufacturer,
											"description"=> $o->description,
											"mpn"=> $o->mpn,
											"moq"=> $o->moq,
											"mxq"=> $o->mxq,
											"unit_price"=> $price,
											"part_number"=> $o->partNumber,
											"lead_time_days"=> $o->leadTimeDays,
											"availability"=> $o->availability,
											"exw"=> $o->exw,
											"is_active"=> $o->isActive,
											"attributes"=> ($o->attributes)?serialize($o->attributes):"",
											"comments"=> ($o->comments)?serialize($o->comments):"",
											"prices"=> ($o->prices)?serialize($o->prices):"");
							//echo "<pre>";print_r($data);print_r($o->attributes);
							foreach($attr as $a){
								$a = (array)$a;
								if(in_array("Condition",$a)){
									if($a['value'] !=""){
										$new = array("New","New Factory Sealed");
										$mfr = array("Samsung Certified Pre-Owned","Factory Refurbished","Refurbished - Off Lease","3rd Party Refurbished");
										$like_new = array("New Open Box","Store Return - As is","New Re-Imaged","Store Return - 3rd Party Refurbished","Store Return - 3rd Party Refurbished NEW OPEN BOX");
										$very_good = array("Renewed Grade A","Store Return - 3rd Party Refurbished B GRADE");
										$good = array("Renewed Grade B");
										$fair = array("Factory Refurbished Scratch & Dent","Renewed Grade C");
										$data['condition'] = $a['value'];
										if(in_array($a['value'],$new)){
											$data['condition_id'] = 1;
										}else if(in_array($a['value'],$mfr)){
											$data['condition_id'] = 2;
										}else if(in_array($a['value'],$like_new)){
											$data['condition_id'] = 3;
										}else if(in_array($a['value'],$very_good)){
											$data['condition_id'] = 4;
										}else if(in_array($a['value'],$good)){
											$data['condition_id'] = 5;
										}else if(in_array($a['value'],$fair)){
											$data['condition_id'] = 6;
										}else{
											$data['condition_id'] = 0;
										}
									}
									
								}
							}
							$this->db->insert(DBPREFIX."_hubx_product",$data);
						}
					}
				}
			}
			//print_r($server_output);
			//echo "<hr>";
		}
	}
	function checkAccessToken($hubx_info,$user_id){
		$time = time();
		if($hubx_info){
			$hubx_info = json_decode($hubx_info);
			if($time > $hubx_info->expires_in){
				$hubx_info = $this->getAccessToken($user_id);
				$hubx_info = json_decode($hubx_info);
			}
			
		}else{
			$hubx_info = $this->getAccessToken($user_id);
			$hubx_info = json_decode($hubx_info);
		}
		return $hubx_info;
	}
	public function testupdatequery(){
		$this->db->set('quantity', '4+sell_quantity', FALSE);
		$this->db->where(array("inventory_id"=>3));
		$this->db->update(DBPREFIX."_product_inventory");
		
	}
}
?> 