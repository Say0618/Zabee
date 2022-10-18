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
		$this->lang->load('english', 'english');
	}

	public function index()
	{
		$this->data['page_name'] 		= 'pendingorders_view';
		$this->data['Breadcrumb_name'] 	= 'Pending Orders';
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template", $this->data);
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
		$orderData 				= $this->Order_Model->getOrders($order_id);
		$orderData 				= $orderData[0];
		$orderData['cart_data'] = $this->arrangeCartData($orderData['cart_data']);
		$pass["orderData"] 		= $orderData;
		$pass["areas"] 			= $this->Areas_Model->getAreas("","area_name,area_pin");
		$pass["deliveryusers"] 	= $this->user_model->getuserbytype("delivery");
		$pass["vendors"] 		= $this->Vendors_Model->getVendors();
		$pass["product_list"]	= $this->product_model->getProductData("",""," product_name ");
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
					<th>Total Price</th>";
				foreach($cartData as $products){
					$strTable .= "<tr>
						<td>".$products["name"]."</td>
						<td>".$products["qty"]."</td>
						<td>$&nbsp;".number_format($products["price"])."</td>
						<td>$&nbsp;".number_format($products["subtotal"])."</td>
					</tr>";
					$total += doubleval($products["subtotal"]);
				}
				$strTable .= "<td colspan = '3' style = 'text-align:right;padding-right : 10px; font-size : 16px;'><strong>Total </strong></td>
							  <td>$&nbsp;".number_format($total)."</td>";
				$strTable .= "</table>";
			}
			$retVal[$order['order_id']]['cart_table'] = $strTable;			
			if($vendors){
				$area = $order['shipping_area'];
				$strVendors = "";
				foreach($vendors as $vendor){
					if(in_array($area,explode(",",$vendor['vendor_area']))){
						$strVendors .= "<h5>".$vendor['vendor_name']."</h5>
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
		$insertArr = array("order_id" 				=> 1260 + intval($rcvdarr["orderid"]),
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
		$headings = array( "order_id"			=> "Order No.",
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
			"btns"		=>array("view"),
			"text"		=>array("View Sales Invoice"),
			"dbcols"	=>array("order_id"),
			"link"		=>array(base_url()."admin/sales/disp_invoice/%@$%"),
			"clickable"	=>array()
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
		$data["inv"] 		= $this->sales_model->getSalesInvoice($inv_id);$data["inv"] = $data["inv"][0]; 
		$data["cust"] 		= $this->user_model->getAllUsers($data["inv"]["customer_id"]);$data["cust"] = $data["cust"][0];
		$productids 		= unserialize($data["inv"]["product_ids"]);
		$data["quantities"] = unserialize($data["inv"]["product_quantities"]);
		$data["products"] 	= $this->product_model->getProduct($productids," product_id, product_name, product_price, discount_price, discount_status ");		
		$this->load->view("admin/disp_invoice_view",$data);		
	}


	public function pendingorders_data()
	{
		$search = $this->input->post('search');
		$request = $this->input->post();
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$where = "t.status = '0' AND td.status = '0' AND td.cancellation_pending = '0' AND td.warehouse_id = '".$this->session->userdata('warehouse_id')."'";		
		$orders = $this->Order_Model->getOrders($id,'',$search['value'],$request,$where,$this->session->userdata("user_type"));
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
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] = $totalResult;
		$data['draw'] = $orders['draw'];
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
		$this->data['page_name'] = 'order_history';
		$this->data['Breadcrumb_name'] = 'Orders Report';
		$this->data['isScript'] = true;
		$this->load->view("admin/admin_template",$this->data);	
	}
	public function filtered_orders(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('datepicker_order', 'date ', 'trim|xss_clean');
		$this->form_validation->set_rules('productId', 'Product', 'trim|xss_clean|integer');
		$this->form_validation->set_rules('search_status', 'status', 'trim|integer|xss_clean');
			if ($this->form_validation->run() == TRUE){
				$searchStatus = $_POST['search_status'];
				$pd_id = $_POST['productId'];
				if($this->session->userdata('userid') == '1'){
					$search_seller = $_POST['search_seller'];
					$from = $_POST['datepicker_from'];
					$to = $_POST['datepicker_to'];
					$sellerId="";
					if(!empty($search_seller) && $search_seller != ""){
						$seller_id = $this->Product_Model->getSellerIdBysellerStore($search_seller); 
						$sellerId = $seller_id->seller_id;
						}
						$data = $this->Order_Model->orderFilters($sellerId,$from,$to,$searchStatus,$pd_id);
					}else{
						$userid = $this->session->userdata('userid');
						$date = $_POST['datepicker_order'];
						$data = $this->Order_Model->orderFilters($userid,$date,"",$searchStatus,$pd_id);
						}	
				$this->data['orders'] 	 = $data['data'];
				$this->data['post_data'] = $_POST;
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
		$orders = $this->Cart_Model->getOrderBySeller($user_id, $order_id);
		foreach($orders as $o){
			$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
			if($o->product_image == ""){
				$o->product_image = $productImage[0]->is_primary_image; 
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
		$this->load->view('front/order_invoice', $this->data);
	}

	function declinedOrders_view(){
		$this->data['page_name'] 		= 'declinedOrders';
		$this->data['Breadcrumb_name'] 	= 'Declined Orders';
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template", $this->data);
	}

	function declinedOrders(){
		$search 	= $this->input->post('search');
		$request 	= $this->input->post();
		$id 		= (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$where 		= array('td.status' => 2);		
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
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] 	 = $totalResult;
		$data['draw'] 			 = $orders['draw'];
		echo json_encode($data);
	}

	function acceptedOrders_view() {
		$this->data['page_name'] 		= 'acceptedOrders';
		$this->data['Breadcrumb_name'] 	= 'Accepted Orders';
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template", $this->data);
	}

	public function acceptedOrders(){
		$search	 = $this->input->post('search');
		$request = $this->input->post();
		$id 	 = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$where 	 = array('td.status' => 1);		
		$orders  = $this->Order_Model->getOrders($id,'',$search['value'],$request,$where);
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
		$data['recordsFiltered'] = $totalResult;
		$data['recordsTotal'] 	 = $totalResult;
		$data['draw'] 			 = $orders['draw'];
		echo json_encode($data);
	}

	public function get(){
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$this->Order_Model->getPendingCancelledOrders($id);
		echo $this->datatables->generate();
	}

	public function cancel_orders_view(){
		$this->data['page_name'] 		= 'cancel_orders';
		$this->data['Breadcrumb_name'] 	= 'Cancel Orders';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}

	public function cancel_orders(){
		$status = $this->Order_Model->cancel_order_confirm($_POST);
		print_r($status);
		return json_encode($status);
	}

	public function get_quantity_from_warehouse(){
		$status = $this->Order_Model->get_quantity_from_warehouse($_POST);
		if(empty($status)){
			$status = 0;
		} else {
			$status = $status[0]['quantity'];
		}
		print_r($status);
		return json_encode($status);
	}

	public function subt_quantity_from_warehouse(){
		$status = $this->Order_Model->subt_quantity_from_warehouse($_POST);
		print_r($status);
		return json_encode($status);
	}

	public function get_order_plus_button(){ 
		$o_id = $this->input->post('o_id');
		$from = $this->input->post('data_from');
		if($from == 'accept'){
		$data = $this->Order_Model->get_orderDetails($o_id, false, 'accept');
		}else if($from == 'pending'){
			$data = $this->Order_Model->get_orderDetails($o_id, false, 'pending');
		}else if($from == 'decline'){
			$data = $this->Order_Model->get_orderDetails($o_id, false, 'decline');
		}
		else{
			$data = $this->Order_Model->get_orderDetails($o_id, false);
		}
		echo json_encode($data);
	}

	public function approveOrder(){ 
		$data = $this->Order_Model->approveOrder($_POST);
		echo json_encode($data);
	}

	public function declineOrder(){ 
		$data = $this->Order_Model->declineOrder($_POST);
		echo json_encode($data);
	}
}
?>  