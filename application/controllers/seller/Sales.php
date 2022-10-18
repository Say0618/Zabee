<?php
class Sales extends SecureAccess 
{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Order_Model");
		$this->load->model("admin/Vendors_Model");
		$this->load->model("admin/Areas_Model");
		$this->load->model("Product_Model");
		$this->load->model("Cart_Model");
		$this->data = array(
			'page_name' => 'store_info',
			'isScript' => false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' => $this->notifications
		);
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
	}

	public function index($expired = false)
	{
		$this->data['page_name'] 		= 'pendingorders_view';
		$this->data['Breadcrumb_name'] 	= 'New Orders';
		$this->data['isScript'] 		= true;
		$this->data['delete'] 			= ($expired == 'expired')?1:'0';
		$this->load->view("admin/admin_template", $this->data);
	}

	/**
* 
* This function will edit and finalize the pending orders
* 
*/
	public function finalizeorder($order_id = "")
	{
		if(!$order_id)redirect(base_url()."seller/sales/pendingOrders","refresh");
		$data['oObj'] = $this;		
		$this->load->view("seller/includes/admin_header",$data);	
		$this->load->model("product_model");
		$this->load->model("admin/user_model");
		$this->load->model("admin/Vendors_Model");
		$orderData = $this->Order_Model->getOrders($order_id);
		$orderData = $orderData[0];
		$orderData['cart_data'] = $this->arrangeCartData($orderData['cart_data']);
		$pass["orderData"] = $orderData;
		$pass["areas"] = $this->Areas_Model->getAreas("","area_name,area_pin");
		$pass["deliveryusers"] = $this->user_model->getuserbytype("delivery");
		$pass["vendors"] = $this->Vendors_Model->getVendors();
		$pass["product_list"]=$this->product_model->getProductData("",""," product_name ");
		$this->load->view('admin/finalizeorders_view',$pass);			
		$this->load->view("admin/includes/admin_footer");
	}
	
	private function arrangeCartData($cartData)
	{
		$cartData = unserialize($cartData);
		$cart = array();
		foreach($cartData as $data){
			unset($data["rowid"]);
			$cart[$data["id"]] = $data;
		}
		return $cart;
	}
	
	private function arrangeData($orderData)
	{
		$retVal = array();
		if(!$orderData){return "";}		
		$vendors = $this->Vendors_Model->getVendors();	
		$i = 0;
		foreach($orderData as $order){			
			$order['order_id'] = ++$i;
			$retVal[$order['order_id']] = $order;
			$retVal[$order['order_id']]['customer_name'] = $order['firstname']." ".$order['lastname'];
			$cartData = unserialize($order['cart_data']);
			if($cartData){
				$total = 0;
				$strTable =  "<table class = 'table table-bordered'>
					<th>Product Name</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Total Price</th>
				";
				foreach($cartData as $products){
					$strTable .= "<tr>
						<td>".$products["name"]."</td>
						<td>".$products["qty"]."</td>
						<td>$&nbsp;".number_format($products["price"])."</td>
						<td>$&nbsp;".number_format($products["subtotal"])."</td>
						</tr>";
					$total += doubleval($products["subtotal"]);
				}
				$strTable .= "
					<td colspan = '3' style = 'text-align:right;padding-right : 10px; font-size : 16px;'><strong>Total </strong></td>
					<td>$&nbsp;".number_format($total)."</td>";
				$strTable .= "</table>";
			}
			$retVal[$order['order_id']]['cart_table'] = $strTable;			
			if($vendors){
				$area = $order['shipping_area'];
				$strVendors = "";
				foreach($vendors as $vendor){
					if(in_array($area,explode(",",$vendor['vendor_area']))){
						$strVendors .= "
							<h5>".$vendor['vendor_name']."</h5>
							<p><strong> Address : </strong>".$vendor["vendor_address"]."<br /> 
							<strong>Phone : </strong>".$vendor["vendor_phone"]."<br /> 
							<strong>Mobile : </strong>".$vendor["vendor_mobile"]."<br /> 
							<strong>Email : </strong>".$vendor["vendor_email"]."<br /> 
							</p>
							<hr class = 'soft'>";
					}
				}
				$retVal[$order['order_id']]['vendors'] = $strVendors;	
			}			
		}
		return $retVal;
	}
	
	function finalizedOrder()
	{
		$this->load->model("admin/sales_model");
		$rcvdarr = $_POST;
		$insertArr = array
		(
			"order_id" 				=>	1260 + intval($rcvdarr["orderid"]),
			"customer_id" 			=> $rcvdarr["custid"],
			"vendor_ids" 			=> serialize($rcvdarr["vendors"]),
			"order_date" 			=> $rcvdarr["order_date"],
			"shipping_address" 		=> $rcvdarr["shippingaddress"],
			"shipping_area" 		=> $rcvdarr["area"],
			"shipping_pin" 			=> $rcvdarr["shippingpin"],
			"product_ids" 			=> serialize($rcvdarr["prod_id"]),
			"product_quantities" 	=> serialize($rcvdarr["quantity"]),
			"delivered_by" 			=> $rcvdarr["delvby"],
			"created_by" 			=> $this->userData[0]["admin_id"],
			"created_date"			=> Date("Y-m-d H:m:s")
		);
		$this->sales_model->insertinvoice($insertArr);
		$this->sales_model->delete_pendingorder($rcvdarr["orderid"]);
		redirect(base_url()."admin/sales/sales_invoice","refresh");
	}
	
	function sales_invoice()
	{
		$this->load->model("admin/sales_model");
		$data['oObj'] = $this;		
		$this->load->view("admin/includes/admin_header",$data);			
		$orderData = $this->sales_model->getSalesInvoice();
		$headings = array
		( 
			"order_id"			=> "Order No.",
			"customer_name"		=> "Customer Name",
			"delivery_name" 	=> "Delivery Boy",
			"order_date"		=> "Order Date",
			"shipping_address"	=> "Shipping Address",
			"shipping_area"		=> "Shipping Area",
			"shipping_pin"		=> "Shipping PIN",
			"vendor_details"	=> "Vendor Details",
			"creator_name"		=> "Created By",
			"created_date"		=> "Created On"
		);
		$label = "Sales Invoice";
		$action = array
		(
			"btns"=>array("view"),
			"text"=>array("View Sales Invoice"),
			"dbcols"=>array("order_id"),
			"link"=>array(base_url()."admin/sales/disp_invoice/%@$%"),
			"clickable"=>array()
		);
		$this->load->view('admin/sales_inv_view');			
		$this->load->view("admin/includes/admin_footer");
	}
	
	public function disp_invoice($inv_id = "0")
	{
		$data = array(); 
		$this->load->model("admin/sales_model");
		$this->load->model("admin/user_model");
		$this->load->model("admin/product_model");		
		$data["inv"] = $this->sales_model->getSalesInvoice($inv_id);$data["inv"] = $data["inv"][0]; 
		$data["cust"] = $this->user_model->getAllUsers($data["inv"]["customer_id"]);$data["cust"] = $data["cust"][0];
		$productids = unserialize($data["inv"]["product_ids"]);
		$data["quantities"] = unserialize($data["inv"]["product_quantities"]);
		$data["products"] = $this->product_model->getProduct($productids," product_id, product_name, product_price, discount_price, discount_status ");		
		$this->load->view("admin/disp_invoice_view",$data);		
	}


	public function pendingorders_data(){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$id = ($this->session->userdata('userid'))?$this->session->userdata('userid'):$this->session->userdata('admin_id');
		$usertype = $this->session->userdata('user_type');
		$where = array('td.seller_id' => $id,'td.status' => "0", 'td.cancellation_pending' => '0');
		$order_status = ($request['expired'] == true)?2:1;
		$orders = $this->Order_Model->getOrders($id,'',$search['value'],$request,$where,$usertype, '', false, $order_status);
		foreach($orders['data'] as $o){
			if($o->image_link == ""){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				$o->image_link = (isset($productImage[0]) && $productImage[0] != "")?$productImage[0]->is_primary_image:""; 
			}
		}
		$result =  $this->Cart_Model->formatOrderList($orders['data']);
		//echo "<pre>";print_r($result);die();
		$totalResult = count($result);
		$data = array();
		if($result){
			foreach($result as $r){
				$data['data'][] = $r;
			} 
		}else{
			$data['data'] = array();
		}
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] 	 = $totalResult;
		$data['draw'] 			 = $orders['draw'];
		echo json_encode($data);
	}

	public function accept_order()
	{
		$action=$this->uri->segment(4);
		$orderid=$this->uri->segment(5);
		$trackingid=$this->uri->segment(6);
		$date = Date("Y-m-d H:m:s");
		$this->Order_Model->accept_order($orderid,$action,$trackingid,$date);
		redirect($_SERVER['HTTP_REFERER']);
	} 

	public function order_history(){
		$this->data['page_name'] 		= 'order_history';
		$this->data['Breadcrumb_name'] 	= 'Orders Report';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function filtered_orders(){
		// echo "<pre>";print_r($this->input->post()); die();
		$this->load->library('form_validation');
		$this->form_validation->set_rules('datepicker_order', 'date ', 'trim|xss_clean');
		$this->form_validation->set_rules('productId', 'Product', 'trim|xss_clean|integer');
		$this->form_validation->set_rules('search_status', 'status', 'trim|integer|xss_clean');
			if ($this->form_validation->run() == TRUE){
				$searchStatus = $this->input->post('search_status');
				$pd_id = $this->input->post('productId');
				if($this->session->userdata('user_type') == '1'){
					$search_seller = $this->input->post('search_seller');
					$from = $this->input->post('datepicker_from');
					$to = $this->input->post('datepicker_to');
					$sellerId="";
					if(!empty($search_seller) && $search_seller != ""){
						$seller_id = $this->Product_Model->getSellerIdBysellerStore($search_seller); 
						$sellerId = $seller_id->seller_id;
						}
						$data = $this->Order_Model->orderFilters($sellerId,$from,$to,$searchStatus,$pd_id);
					}else{
						$userid = $this->session->userdata('userid');
						$date = $this->input->post('datepicker_order');
						$data = $this->Order_Model->orderFilters($userid,$date,"",$searchStatus,$pd_id);
						}	
				$this->data['orders'] = $data['data'];
				$this->data['post_data'] = $this->input->post();
				$this->data['page_name'] = 'filtered_orders';
				$this->load->view('admin/reporting_template', $this->data);	
			}else{
				redirect(base_url('seller/sales/order_history'));
			}
	}

	public function getInvoice($order_id = '')
	{
		if($order_id == ''){
			$this->data['heading'] = '404 Error';
			$this->data['message'] = 'Invoice of order you are looking not found';
			$this->load->view('errors/html/error_404', $this->data);
			return;
		}
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');
		$orders = $this->Cart_Model->getOrderBySeller($user_id, $order_id,$user_type);
		if($orders){
			foreach($orders as $o){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				if($o->product_image == ""){
					$o->product_image = isset($productImage[0])?$productImage[0]->is_primary_image:""; 
				}
			}
			$sorders = array();
			$this->data['orders'] = $this->Cart_Model->formatOrderList($this->Cart_Model->getOrderBySeller($user_id, $order_id), 'data');
			for($i = 0;$i<count($orders); $i++){
				if(isset($sorders[$orders[$i]->seller_id])){
					$sorders[$orders[$i]->seller_id][] = $orders[$i];
				}else{
					$sorders[$orders[$i]->seller_id][] = $orders[$i];
					}
				}
			$this->data['sellerOrders'] = $sorders;
			$this->data['page_name'] 	= 'order_invoice';
			$this->data['title'] 		= "Order Invoice";
			$this->data['hasScript'] 	= true;
			$this->load->view('front/order_invoice', $this->data);
		}else{
			$this->session->set_flashdata('error', "Invalid Request!");
			redirect(base_url('seller/sales/acceptedOrders_view'));
		}
	}

	function declinedOrders_view(){
		$this->data['page_name'] 		= 'declinedOrders';
		$this->data['Breadcrumb_name'] 	= 'Cancelled Orders';
		$this->data['isScript']			= true;
		$this->load->view("admin/admin_template", $this->data);
	}

	function declinedOrders(){
		$search 	= $this->input->post('search');
		$request 	= $this->input->post();
		$id 		= (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$where 		= array('td.seller_id' => $id,'td.status' => 2);		
		$orders 	= $this->Order_Model->getOrders($id,'',$search['value'],$request,$where);
		foreach($orders['data'] as $o){
			$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
			if($o->image_link == ""){
				$o->image_link = $productImage[0]->is_primary_image; 
			}
		}
		$result =  $this->Cart_Model->formatOrderList($orders['data']);
		$totalResult = count($result);
		$data = array();
		if($result){
			foreach($result as $r){
				$data['data'][] = $r;
			} 
		}else{
			$data['data'] = array();
		}
		$data['recordsFiltered'] 	= $totalResult;
		$data['recordsTotal'] 		= $totalResult;
		$data['draw'] 				= $orders['draw'];
		echo json_encode($data);
	}

	function acceptedOrders_view() {
		$this->data['page_name'] 		= 'acceptedOrders';
		$this->data['Breadcrumb_name'] 	= 'Completed Orders';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}

	public function acceptedOrders(){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$where = array('td.seller_id' => $id,'td.status' => 1);		
		$orders = $this->Order_Model->getOrders($id,'',$search['value'],$request,$where);
		// echo"<pre>"; print_r($orders); die();
		foreach($orders['data'] as $o){
			$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
			if($o->image_link == ""){
				$o->image_link = $productImage[0]->is_primary_image; 
			}
		}
		$result =  $this->Cart_Model->formatOrderList($orders['data']);
		//echo "<pre>";print_r($result);die();
		$totalResult = count($result);
		$data = array();
		if($result){
			foreach($result as $r){
				$data['data'][] = $r;
			} 
		}else{
			$data['data'] = array();
		}
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] = $totalResult;
		$data['draw'] = $orders['draw'];
		echo json_encode($data);
	}

	public function get(){
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$this->Order_Model->getPendingCancelledOrders($id, $search);
		echo $this->datatables->generate();
		
	}

	public function cancel_orders_view(){
		$this->data['page_name'] 		= 'cancel_orders';
		$this->data['Breadcrumb_name'] 	= 'Cancel Order Requests';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}
	public function cancel_orders(){
		$status = $this->Order_Model->cancel_order_confirm($this->input->post());
		if($status['status'] == "1"){
			$emailData = $this->Order_Model->getProductDataByOrder($this->input->post('can_id'),$this->input->post('row_id'));
			// $loaction = array("shipping","billing");
			// $location['shipping'] = unserialize($emailData->shipping);
			// $location['shipping']['state'] = (is_numeric($location['shipping']['state']))?getCountryNameByKeyValue('id', $location['shipping']['state'], 'code', true,'tbl_states'):$location['shipping']['state'];
			// $location['shipping']['country'] = getCountryNameByKeyValue('id', $location['shipping']['country'], 'nicename', true);
			
			// $location['billing'] = unserialize($emailData->shipping);
			// $location['billing']['state'] = (is_numeric($location['billing']['state']))?getCountryNameByKeyValue('id', $location['billing']['state'], 'code', true,'tbl_states'):$location['billing']['state'];
			// $location['billing']['country'] = getCountryNameByKeyValue('id', $location['billing']['country'], 'nicename', true);

			// $product['products'] = array("price"=>$emailData->price,"qty"=>$emailData->qty,"shipping_price"=>$emailData->item_shipping_amount,"name"=>$emailData->product_name,"is_local"=>$emailData->is_local,"img"=>$emailData->thumbnail);
			$subject = "Zabee : Order Cancellation (".$this->input->post('can_id').")";
			$this->Utilz_Model->send_mail("","",$this->input->post('can_id'),$emailData->email, $type="buyer",$subject,"",$emailData->firstname, "buyer", $status['type']);
		}
		//print_r($status);
		echo json_encode($status);
	}

	public function get_all_order_items(){
		$o_id = $this->input->post('o_id');
		$is_admin = $this->input->post("is_admin");
		$is_failed = $this->input->post("is_failed");
		if(!$is_admin){
			$u_id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		}else{
			$u_id = "";
		}
		$data = $this->Order_Model->get_orderDetails($o_id, true,"",$u_id,$is_admin,$is_failed);
		echo json_encode($data);
	}

	public function get_cancel_orders_info(){
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$o_id = $this->input->post('o_id');
		$data = $this->Order_Model->get_canceOrderDetails($o_id, $id);
		echo json_encode($data);
	}

	public function approveOrder(){ 
		$postData = $this->input->post('data');
		$trackingData = $postData['shippingData'];
		unset($postData['shippingData']);
		unset($trackingData['payment_type']);
		//print_r($postData['data']);
		$data = $this->Order_Model->approveOrder($postData);
		/*echo "<pre>";
		foreach($data as $d){
			print_r($d);
		}
		die();*/
		//echo "<pre>";print_r($this->input->post());die();
		if(!empty($data)){
			$this->load->model("User_Model");	
			foreach($data as $obj){
				if($obj['status'] == '1'){
					$key = $obj['key'];
					$trans_details_Id = $postData[$key]['trans_details_Id'];
					$order_id = $postData[$key]['order_id'];
					$trackingData['trans_details_Id'] = $trans_details_Id;
					$trackingData['order_id'] = $order_id;
					//$trackingData = array("tracking_number"=>$this->input->post('tracking_number'),"shipping_provider"=>$this->input->post('shipping_provider'),"shipping_service"=>$this->input->post('shipping_service'),"trans_details_Id"=>$this->input->post('trans_details_Id'),"order_id"=>$this->input->post('order_id'));
					$this->User_Model->saveData($trackingData,DBPREFIX."_tracking");	
					$emailData = $this->Order_Model->getProductDataByOrder($order_id,$trans_details_Id);
					$loaction = array("shipping","billing");

					$location['shipping'] = unserialize($emailData->shipping);
					$location['shipping']['state'] = (is_numeric($location['shipping']['state']))?getCountryNameByKeyValue('id', $location['shipping']['state'], 'code', true,'tbl_states'):$location['shipping']['state'];
					$location['shipping']['country'] = getCountryNameByKeyValue('id', $location['shipping']['country'], 'nicename', true);
					
					$location['billing'] = unserialize($emailData->shipping);
					$location['billing']['state'] = (is_numeric($location['billing']['state']))?getCountryNameByKeyValue('id', $location['billing']['state'], 'code', true,'tbl_states'):$location['billing']['state'];
					$location['billing']['country'] = getCountryNameByKeyValue('id', $location['billing']['country'], 'nicename', true);

					$product['products'] = array("price"=>$emailData->price,"qty"=>$emailData->qty,"shipping_price"=>$emailData->item_shipping_amount,"name"=>$emailData->product_name,"is_local"=>$emailData->is_local,"img"=>$emailData->thumbnail);
					$subject = "Your Zab.ee Order ".$order_id." Is Confirmed";
					$shippingProvider = ($trackingData['shipping_provider'] !="")?$trackingData['shipping_provider']:"N/A";
					$trackingNumber = ($trackingData['tracking_number'] !="")?$trackingData['tracking_number']:"N/A";
					$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Great news! Your order '.$order_id.' is shipping soon. The tracking information, if available, is given below.</p><p style="margin-bottom:0px;font-size:16px; font-weight:bold">Shipping Method:</p><span>'.$shippingProvider.": ".$trackingNumber.'</span>';
					$this->Utilz_Model->send_mail($location,$product,$order_id,$emailData->email, $type="buyer",$subject,$text,$emailData->firstname);
					$this->session->set_flashdata('success', "Your order has been confirmed.");
				}
			}
		}
		echo json_encode($data);
	}

	public function declineOrder(){ 
		$postData = $this->input->post('data');
		$cancelData = $postData['cancelData'];
		unset($postData['cancelData']);
		$data = $this->Order_Model->declineOrder($postData);
		//echo "<pre>";print_r($data);die();
		if(!empty($data)){
			foreach($data as $obj){
				if($obj['status'] == '1'){
					$key = $obj['key'];
					$trans_details_Id = $postData[$key]['trans_details_Id'];
					$order_id = $postData[$key]['order_id'];
					
					$emailData = $this->Order_Model->getProductDataByOrder($order_id,$trans_details_Id);
					$loaction = array("shipping","billing");
					// echo "<pre>"; print_r($emailData); die();
					$location['shipping'] = unserialize($emailData->shipping);
					$location['shipping']['state'] = (is_numeric($location['shipping']['state']))?getCountryNameByKeyValue('id', $location['shipping']['state'], 'code', true,'tbl_states'):$location['shipping']['state'];
					$location['shipping']['country'] = getCountryNameByKeyValue('id', $location['shipping']['country'], 'nicename', true);
					
					$location['billing'] = unserialize($emailData->shipping);
					$location['billing']['state'] = (is_numeric($location['billing']['state']))?getCountryNameByKeyValue('id', $location['billing']['state'], 'code', true,'tbl_states'):$location['billing']['state'];
					$location['billing']['country'] = getCountryNameByKeyValue('id', $location['billing']['country'], 'nicename', true);

					$product['products'] = array("price"=>$emailData->price,"qty"=>$emailData->qty,"shipping_price"=>$emailData->item_shipping_amount,"name"=>$emailData->product_name,"is_local"=>$emailData->is_local,"img"=>$emailData->thumbnail, "discount"=>($emailData->discountData != "")?"1":"0", "discountData"=>$emailData->discountData);
					// echo "<pre>"; print_r($product); die();
					$subject = "Your Zab.ee Order ".$order_id." Is Canceled";
					$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Your order '.$order_id.' has been canceled. Your credit card has not been charged.</p><p style="margin-bottom:0px;font-size:16px; font-weight:bold">Reason for Cancelation:</p><span>'.$cancelData['reason'].'</span>';
					$this->Utilz_Model->send_mail($location,$product,$order_id,$emailData->email, $type="buyer",$subject,$text,$emailData->firstname);
					$this->session->set_flashdata('success', "Your order has rejected.");
				}
			}
		}else{
			if($data['status'] == 3){
				$this->session->set_flashdata('error', "Order Expired");
			}else{
				$this->session->set_flashdata('error', "Error in order rejecting");
			}
		}
		echo json_encode($data);
	}

	public function saleView($order_id){
		$data = $this->Order_Model->getSaleView($order_id);
		$this->data['shipping'] = unserialize($data['0']->shipping);
		$this->data['billing'] = ($data['0']->billing != "paypal")?unserialize($data['0']->billing):$data['0']->billing;
		$this->data['products'] = $data;
		$this->data['modal'] = "sale_view";
		foreach($data as $o){
			if($o->image_link == ""){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				$o->image_link = isset($productImage[0])?$productImage[0]->is_primary_image:""; 
			}
		}
		$this->load->view("admin/sale_view", $this->data);
	}
	public function forceAcceptOrder(){
		$id = $this->input->post('trans_id');
		$status = $this->Order_Model->forceAcceptOrder($id);
		echo json_encode($status);
	}

	public function failedOrders(){
		$this->data['page_name'] 		= 'failed_sales';
		$this->data['Breadcrumb_name'] 	= 'Failed Orders';
		$this->data['is_admin']			= ($this->session->userdata('user_type') == 1)?1:0;
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}

	public function failedorders_data(){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$id = ($this->session->userdata('userid'))?$this->session->userdata('userid'):$this->session->userdata('admin_id');
		// $usertype = $this->session->userdata('user_type');
		// $where = array('td.status' => "0", 'td.cancellation_pending' => '0');			
		$orders = $this->Order_Model->getFailedOrders($id,'',$search['value'],$request,"");
		foreach($orders['data'] as $o){
			if($o->image_link == ""){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				$o->image_link = (isset($productImage[0]) && $productImage[0] != "")?$productImage[0]->is_primary_image:""; 
			}
		}
		$result =  $this->Cart_Model->formatOrderList($orders['data']);
		//echo "<pre>";print_r($result);die();
		$totalResult = count($result);
		$data = array();
		if($result){
			foreach($result as $r){
				$data['data'][] = $r;
			} 
		}else{
			$data['data'] = array();
		}
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] 	 = $totalResult;
		$data['draw'] 			 = $orders['draw'];
		echo json_encode($data);
	}

	public function transferOrder(){
		$id = $this->input->post('order_id');
		$status = $this->Order_Model->transferOrder($id);
		echo json_encode($status);
	}

	public function transferSaleView($order_id){
		$data = $this->Order_Model->getTransferSaleView($order_id);
		$this->data['shipping'] = unserialize($data['0']->shipping);
		$this->data['billing'] = unserialize($data['0']->billing);
		$this->data['products'] = $data;
		$this->data['modal'] = "sale_view";
		foreach($data as $o){
			if($o->image_link == ""){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				$o->image_link = isset($productImage[0])?$productImage[0]->is_primary_image:""; 
			}
		}
		$this->load->view("admin/sale_view", $this->data);
	}

	// public function paypalApproveOrder(){
	// 	$result = $this->Order_Model->paypalApproveOrder($this->input->post());
	// 	return json_encode($result);
	// }
	// public function paypalDeclineOrder(){
	// 	$result = $this->Order_Model->paypalDeclineOrder($this->input->post());
	// 	return json_encode($result);
	// }
	public function orderList($expired = false){
		$this->data['page_name'] 		= 'order_list';
		$this->data['Breadcrumb_name'] 	= 'Orders List';
		$this->data['isScript'] 		= true;
		$this->data['delete'] 			= ($expired == 'expired')?1:'0';
		$this->load->view("admin/admin_template", $this->data);
	}
	public function order_admin_list($expired = false){
		$id = $this->session->userdata('userid');
		$usertype = $this->session->userdata('user_type');
		if($usertype != 1){
			redirect(base_url("seller"));
		}
		$search = $this->input->post('search');
		$request = $this->input->post();
		$where = array();//array('td.status' => "0", 'td.cancellation_pending' => '0');	
		//$request = array("start"=>"","length"=>"","draw"=>"","recordsTotal"=>"");
		$order_status = ($request['expired'] == true)?2:1;
		$orders = $this->Order_Model->getOrders($id,'',$search['value'],$request,$where,$usertype,"",false, $order_status);
		foreach($orders['data'] as $o){
			if($o->image_link == ""){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				$o->image_link = (isset($productImage[0]) && $productImage[0] != "")?$productImage[0]->is_primary_image:""; 
			}
		}
		$result =  $this->Cart_Model->formatOrderList($orders['data']);
		//echo "<pre>";print_r($result);die();
		$totalResult = count($result);
		$data = array();
		if($result){
			foreach($result as $r){
				$data['data'][] = $r;
			} 
		}else{
			$data['data'] = array();
		}
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] 	 = $totalResult;
		$data['draw'] 			 = $orders['draw'];
		//echo "<pre>";
		echo json_encode($data);
	}
	function bulkAction($action="",$ids=""){
		$return = array("response"=>"","status"=>"");
		if($action == "approve"){
			$return['response'] = "";
			$return['status'] = 1;
		}else{
			$return['response'] = "";
			$return['status'] = 0;
		}
	}
}
?>  