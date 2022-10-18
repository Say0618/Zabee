<?php
class Checkout extends Securearea 
{
	public $oCart;
	function __construct()
	{		
		parent::__construct();
		$this->load->helper("url");
		$this->load->helper("security");
		$this->load->model("User_Model");
		$this->load->model("admin/Order_Model");
		$this->load->model("Addressbook_Model", 'AddressBook');
		$this->data['user_type'] = "buyer";
		$this->data['newsletter'] = false; 
		if(!$this->isloggedin && !$this->session->userdata('is_guest')){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($this->isloggedin){
			if(isset($_SESSION['timestamp']) && (time() - $_SESSION['timestamp']) > 300) { //subtract new timestamp from the old one
				//redirect(base_url('logout'),"refresh");
			} else {
				$_SESSION['timestamp'] = time(); //set new timestamp
			}
		}
		if(!$this->cart->contents()){
			redirect(base_url('cart'),"refresh");
		}
		$uri = end($this->uri->segments);
		/*if($uri !="proceed_payment" && $this->isloggedin){
			$this->cart_discount($this->cart->contents());	
		}*/
		$this->lang->load('english','english');
	}

	function index(){
		if(!$this->isloggedin){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($this->session->flashdata('same_product')){
			$this->session->set_flashdata("same_product",$this->session->flashdata('same_product'));
			redirect(base_url("cart"),"refresh");
		}
		$default = $this->getDefault($user_id);
		//print_r($default);die();
		if(isset($default['address']->id) &&  !isset($_GET['c'])){
			$this->useAddress($default['address']->id,0);
		}
		if(isset($default['card']->card_id)  &&  !isset($_GET['c'])){
			$data = array('payment_method'=>'stripe','card_id'=>$default['card']->card_id);
			$this->paymentwithcard(FALSE,$data);
		}
		//echo "<pre>";print_r($default);die();
		$locations = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type']);
		if($locations){
			$this->data['locations'] 			= $locations;
			$this->data['checkerForCheckout'] 	= "2";
			$this->data['page_name'] 			= 'checkout_already_address';
			$this->data['hasScript'] 			= false;
			$this->data['title'] 				= "Checkout";
			$this->data['progress_bar'] 		= true;
			$this->data['bradcrumb'] 			= "Checkout";
			$this->load->view('front/template', $this->data);
		} else {	
			redirect(base_url('shipping/add/2'),"refresh");
		}
	}

	public function useAddress($location_id="",$default=1){
		if(!$this->isloggedin){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($location_id == ''){
			redirect(base_url('checkout?status=invalid_addressbook&code=001'));
			die();
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$selectedlocations = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type'], $location_id);
		if($selectedlocations == ""){
			redirect(base_url(),"refresh"); 
		} else {
			$data = array(
						'id' 		=> $selectedlocations->id,
						'name' 		=> $selectedlocations->fullname,
						'phone' 	=> $selectedlocations->contact,
						'address_1' => $selectedlocations->address_1,
						'address_2' => $selectedlocations->address_2,
						'city' 		=> $selectedlocations->city,
						'state' 	=> $selectedlocations->state,
						'country' 	=> $selectedlocations->country,
						'zipcode' 	=> $selectedlocations->zip,
				);
			$this->session->set_userdata('checkout_ship', $data);
			// echo"<pre>"; print_r($this->session->userdata('checkout_card_view')); die();
			if(!empty($this->session->userdata('checkout_card_view'))){
				if($this->session->userdata('checkout_card_view')["customer_id"] != "PayPal"){
					redirect(base_url('checkout/payment?confirm_order=1'), "refresh");
				}else{
					$card_info = $this->AddressBook->getDefaultAddressAndCard($user_id);
					if(!empty($card_info['card'])){
						$card_data = array("holder_name"=>$card_info['card']->holder_name);
						$this->session->set_userdata('checkout_card_view', $card_data);
					}
					redirect(base_url('checkout/payment?pay=paypal'), "refresh");
				}
			}else{
				if($default == 1){
					redirect(base_url('checkout/payment'),"refresh"); 
				}else{
					return true;
				}
			}
		}
	}
	public function makeDefault($location_id="",$from="c"){
		if(!$this->isloggedin){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($location_id == ''){
			redirect(base_url('checkout?status=invalid_addressbook&code=001'));
				die();
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$selectedlocations = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type'], $location_id);
		if($selectedlocations == ""){
			redirect(base_url(),"refresh"); 
		} else {
			$insert_data = array(
				'updated' 		=> date('Y-m-d H:i:s'),
				'user_id' 		=> $user_id,
				'use_address' 	=> "1"
			);
			$insert_data2 = array(
				'updated' 		=> date('Y-m-d H:i:s'),
				'user_id' 		=> $user_id,
				'use_address' 	=> "0"
			);
			$resp = $this->AddressBook->update_addressonly($insert_data ,$insert_data2, $user_id, $location_id);
			if($resp){
				if($from == "c"){
					redirect(base_url('checkout'),"refresh"); 
				}else{
					redirect(base_url('shipping'),"refresh"); 
				}
			}
		}
	}
	public function makeCardDefault($card_id=""){
		if(!$this->isloggedin){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($card_id == ''){
			redirect(base_url('checkout?status=invalid_addressbook&code=001'));
			die();
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$insert_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'user_id' => $user_id,
			'default' => "1"
		);
		$insert_data2 = array(
			'updated' => date('Y-m-d H:i:s'),
			'user_id' => $user_id,
			'default' => "0"
		);
		$resp = $this->User_Model->makeDefaultCard($insert_data ,$insert_data2, $user_id, $card_id);
		if($resp){
			redirect(base_url('checkout/payment?status=success&msg=Default Card Changed Successfully'),"refresh"); 
		}else{
			redirect(base_url('checkout/payment?status=error&msg=Something not right'),"refresh"); 
		}
	}
	public function payment($type = '')
	{
		if(!$this->cart->contents() || !$this->session->userdata('checkout_ship')){
			redirect(base_url(),"refresh");
		}
		$this->data['payment_error'] 			= ($this->session->userdata('payment_error'))?true:false;
		$this->data['payment_error_message'] 	= ($this->data['payment_error'])?$this->session->userdata('payment_error'):'';
		$this->data['orderID'] 					= (isset($_POST['orderID']))?$_POST['orderID']:time() * rand(1, 9);
		$this->session->set_userdata('orderID', $this->data['orderID']);
		$location 								= $this->session->userdata('checkout_ship');
		$this->data['tax'] 						= 0;//$this->getTax($location['zipcode'], $this->cart->total());
		$this->data['zip_code'] 				= $location['zipcode'];

		$form_error = false;
		$this->form_validation->set_rules('card_name', 'Card holder name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('card_number', 'Card number', 'trim|integer|required|xss_clean');
		$this->form_validation->set_rules('ccv', 'CCV', 'trim|numeric|required|xss_clean');
		$this->form_validation->set_rules('exp_date', 'Exipry date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('exp_year', 'Exipry date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('billing_address', 'Billing Address', 'required');

		if($this->session->userdata('is_guest')){
			$user_id = $this->session->userdata('guest_id');
		}else{
			$user_id = $this->session->userdata('userid');
		}
		if ($this->form_validation->run() == TRUE ){
			$post_data = $this->input->post();
			$user = $this->User_Model->checkUserId($user_id, true);
			if($post_data['payment_method'] == "stripe"){
				$card = $this->formatCC($post_data);
			}else{
				$card = array(
					'acct' 			=> $post_data['card_number'],
					'expdate' 		=>  $post_data['exp_date'].$post_data['exp_year'],
					'cvv2' 			=> $post_data['ccv'],
					'startdate' 	=> '',
					'issuenumber' 	=> '',
					'holder' 		=> $post_data['card_name'],
				);
			}
			//Add Billing Location
			$billing_location = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type'],$post_data['billing_address']);
			$data = array(
				'id' 			=> $billing_location->id,
				'name' 			=> $billing_location->fullname,
				'phone' 		=> $billing_location->contact,
				'address_1' 	=> $billing_location->address_1,
				'address_2' 	=> $billing_location->address_2,
				'city' 			=> $billing_location->city,
				'state' 		=> $billing_location->state,
				'country' 		=> $billing_location->country,
				'zipcode' 		=> $billing_location->zip,
			);
			$state = (is_numeric($billing_location->state))?getCountryNameByKeyValue('id', $billing_location->state, 'code', true,'tbl_states'):$billing_location->state;
			$city = $billing_location->city;
			$country = getCountryNameByKeyValue('id', $billing_location->country, 'nicename', true);
			$address_1 = $billing_location->address_1;
			$save_card = (isset($post_data['save_card']))?"1":"0";
			$cardView = array(
					'acct' 			=> ccMasking($post_data['card_number'], '*'),
					'expdate' 		=>  $post_data['exp_date'].$post_data['exp_year'],
					'cvv2' 			=> '',
					'startdate'		=> '',
					'issuenumber' 	=> '',
					'holder' 		=> $post_data['card_name'],
					'card_name' 	=> $post_data['custom_card_name'],
					'save_card' 	=> $save_card,
					'paywithcard' 	=> false,
					'address' 		=> $address_1."<br/>".$city.", ".$state." ".$billing_location->zip."<br/>".$country
				);
			$this->load->library('Stripe', 'stripe');
			$stripe_key = $this->config->item('stripe_key');
			$getStripeCustomerID = $this->User_Model->getStripeCustomerID($user_id);
			if($getStripeCustomerID == ''){
				$customer = $this->stripe->createCustomer($_SESSION['email'],$stripe_key);
				if($customer['status'] == 1){
					$customer_id = $customer['customer_id'];
					$this->User_Model->addStripeCustomerID($customer['customer_id'], $user_id);
				}
			} else {
				$customer_id = $getStripeCustomerID;
			}
			$card_name = $post_data['custom_card_name'];
			
			$card = $this->stripe->saveCard($card,$card_name,$customer_id,$stripe_key,$this->formatStripeAddress($data, $state, $country));
			if($card['status'] == 1){
				$card_id = $card['card_id'];
				$token = $card['card_id'];
				$getToken = false;
				$this->User_Model->addStripeCustomerCard($customer_id, $card['card'], $card_name, $user_id,$billing_location->id,$save_card);
				$cardView['card_id'] = $card_id;
				$cardView['customer_id'] = $customer_id; 
				$this->session->set_userdata('checkout_billing', $data);

				$this->session->set_userdata('checkout_card_view', $cardView);
				$this->session->set_userdata('payment_method', $post_data['payment_method']);
				$this->session->set_userdata('order_data', $post_data);
				$this->session->set_userdata('checkout_card', $card);
				$this->session->set_userdata('confirm_page', 1);				
				
				redirect(base_url('checkout/payment?confirm_order=1'),"refresh");
			} else{
				$this->data['payment_error'] = true;
				$this->data['payment_error_message'] = $card['message'];
			}
		} else{
			$form_error = true;
		}
		$this->data['user'] = array(
			'cardholder' 	   => '',
			'card_number' 	   => '',
			'ccv' 			   => '',
			'card_type' 	   => '',
			'exp_month' 	   => '',
			'exp_year' 		   => '',
			'custom_card_name' => ''
		);
		$this->data['cards'] = array();
		if($this->input->get('confirm_order') || $this->input->get('pay')){
			$cart = array();
			$this->cart_discount($this->cart->contents());	
			if($this->cart->total_items() > 0){
				foreach($this->cart->contents() as $items){
					$items['tax'] = 0;
					$items['is_tax'] = 0;
					//echo "<pre>";print_r($this->Cart_Model->getTax($location['zipcode'], $items['subtotal']));die();
					if(isset($cart[$items['seller_id']])){
						if($cart[$items['seller_id']]['is_tax']){
							$items['tax'] = $this->cart->format_number($this->Cart_Model->getTax($location['zipcode'], $items['subtotal']));
						}
						$cart[$items['seller_id']][] = $items;
						$cart[$items['seller_id']]['subtotal'] = $cart[$items['seller_id']]['subtotal']+$items['subtotal'];
						$cart[$items['seller_id']]['total_tax'] = $cart[$items['seller_id']]['total_tax']+$items['tax'];
					}else{
						$cart[$items['seller_id']] = array();
						if(isset($items['collect_tax']) && $items['collect_tax'] == 1){
							$tax_info = $this->Product_Model->getData(DBPREFIX.'_state_tax', 'id', array('userid'=>$items['seller_id'],"state_id"=>$location['state']),"","","","","","","","",true);
							if(isset($tax_info->id)){
								$items['is_tax'] = 1;	
								$items['tax'] = $this->cart->format_number($this->Cart_Model->getTax($location['zipcode'], $items['subtotal']));
							}
						}
						$cart[$items['seller_id']][] = $items;
						$cart[$items['seller_id']]['is_tax'] = $items['is_tax'];
						$cart[$items['seller_id']]['total_tax'] = $items['tax'];
						$cart[$items['seller_id']]['subtotal'] = $items['subtotal'];
						$cart[$items['seller_id']]['store_name'] = isset($items['store_name'])?$items['store_name']:"";
						
					}
					$data = array(
						'rowid' => $items['rowid'],
						'tax'=>$items['tax'],
						'is_tax'=> $items['is_tax']
					);
					$this->cart->update($data);	
				}
			}
			//echo json_encode($cart);die();
			//echo "<pre>";print_r($cart);die();
			if($this->input->get("pay")){
				$card = array(
					'id' => NULL,
					'name' => "PayPal User",
					'last4' => "xxxx",
					'brand' => "PayPal",
					'exp_year' => "xxxx",
					'exp_month' => "xx",
					'country' => NULL
				);
				$cardView['card_id'] = $card['id'];
				$cardView['customer_id'] = "PayPal"; 
				//$this->User_Model->addStripeCustomerCard("123456", $card, "paypal", $user_id, "paypal", "1");
				$this->session->set_userdata('payment_method', "paypal");
				$this->session->set_userdata('checkout_card_view', $cardView);
			}
			$this->data['cart'] = $cart;
			$this->data['hasScript'] 		= true;
			$this->data['page_name'] 		= 'confirm_payment';
			$this->data['card'] 			= $this->session->userdata('checkout_card_view');
			$this->data['shipping_address'] = $location;
			$this->data['payType']			= ($this->input->get('pay'))?"paypal":"";
			$this->data['billing_address']  = $this->session->userdata('checkout_billing');
			$this->data['discount'] 		= ($this->session->userdata('discount_data'))?$this->session->userdata('discount_data'):array();
			$this->data['hasDiscount'] 		= ($this->session->userdata('discount_data'))?true:false;
		} else if(isset($_GET['add_card'])){
			$this->data['page_name'] = 'payment';
			$this->data['hasScript'] = true;
			$this->data['location']  = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type']);
			if($this->session->userdata('userid')){
				$getCards = $this->User_Model->getStripeCards($user_id);
				$this->data['cards'] = ($getCards['status'] == 1)?$getCards['data']:array();
			}
		}else{
			$this->data['page_name'] = 'user_cards';
			$this->data['hasScript'] = false;
			if($this->session->userdata('userid')){
				$getCards = $this->User_Model->getStripeCards($user_id);
				if($getCards['status'] == 1){
					$this->data['cards'] = ($getCards['status'] == 1)?$getCards['data']:array();
				}else{
					redirect(base_url("checkout/payment?add_card=1"));
				}
			}
			if($this->session->userdata('is_guest')){
				redirect(base_url("checkout/payment?add_card=1"));
			}
		}
		$this->data['title'] = "Payment";
		$this->load->view('front/template', $this->data);
	}
	public function paymentwithcard($default=TRUE,$data="")
	{
		if(!$this->cart->contents()){
			redirect(base_url('checkout/payment'),"refresh");
		}
		$this->form_validation->set_rules('card_id', 'Card ID', 'trim|required|xss_clean');
		$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');
		$this->form_validation->set_rules('orderID', 'Order ID', 'trim|required|xss_clean');

		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}
		if ($this->form_validation->run() == $default ){
			if($data == ""){
				$data = $this->input->post();
			}
			if($data['payment_method'] == "stripe"){
				$card_id = $data['card_id'];
				$getCard = $this->User_Model->getStripeCards($user_id, $card_id);
				if($getCard['status'] == 1){
					$getCard = $getCard['data'][0];
					$card_number = '****-****-****-'.$getCard->card_number;
					$cc = array(
						'card_name' 	=> $getCard->holder_name,
						'card_number' 	=> $card_number,
						'exp_date' 		=> $getCard->expiry_month,
						'exp_year' 		=> $getCard->expiry_year,
						'ccv' 			=>  'xxx',
					);
				}
				$card = $this->formatCC($cc);
				$card['address_id'] = $getCard->address_id;
				/*Billing Address*/
				$billing_location = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type'],$getCard->address_id,"1");
				$billing_data = array(
					'id' 		=>$billing_location->id,
					'name' 		=>$billing_location->fullname,
					'phone' 	=>$billing_location->contact,
					'address_1' =>$billing_location->address_1,
					'address_2' =>$billing_location->address_2,
					'city' 		=>$billing_location->city,
					'state' 	=>$billing_location->state,
					'country' 	=>$billing_location->country,
					'zipcode' 	=>$billing_location->zip,
				);
				$state = (is_numeric($billing_location->state))?getCountryNameByKeyValue('id', $billing_location->state, 'code', true,'tbl_states'):$billing_location->state;
				$city = $billing_location->city;
				$country = getCountryNameByKeyValue('id', $billing_location->country, 'nicename', true);
				$address_1 = $billing_location->address_1;
				$cardView = array(
					'acct' 			=> $card_number,
					'expdate' 		=>  $getCard->expiry_month.$getCard->expiry_year,
					'cvv2' 			=> 'xxx',
					'startdate' 	=> '',
					'issuenumber' 	=> '',
					'holder' 		=> $getCard->holder_name,
					'card_name' 	=> $getCard->holder_name,
					'save_card' 	=> false,
					'paywithcard' 	=> true,
					'customer_id' 	=> $getCard->customer_id,
					'card_id' 		=> $getCard->card_id,
					'address' 		=> $address_1."<br/>".$city.", ".$state." ".$billing_location->zip."<br/>".$country
				);
				$data['card_number'] = $card_number;
				$data['card_name'] 	 = $getCard->holder_name;
				$data['exp_date'] 	 = $getCard->expiry_month;
				$data['exp_year'] 	 = $getCard->expiry_year;
				$data['ccv'] 		 = 'xxx';
				
				$this->session->set_userdata('checkout_billing', $billing_data);
				$this->session->set_userdata('checkout_card_view', $cardView);
				$this->session->set_userdata('payment_method', $data['payment_method']);
				$this->session->set_userdata('order_data', $data);
				$this->session->set_userdata('checkout_card', $card);
				// $this->session->set_userdata('confirm_page', 1);
				redirect(base_url('checkout/payment?confirm_order=1'),"refresh");
			}elseif($data['payment_method'] == "paypal"){
				$this->session->set_userdata('confirm_page', 1);
				redirect(base_url('checkout/payment?pay=paypal'),"refresh");
			} else {
				redirect(base_url('checkout/payment?status=0&error=payment_method'),"refresh");
			}
		}
	}

	public function proceed_payment(){
		//$this->session->unset_userdata("payment_method");
		 //echo $this->input->post('orderID'); die("here");
		//echo $this->session->userdata('confirm_page')."<br>";
		//echo $this->input->post('orderID');//die("here");
		$hubx_data = array();
		$this->data['orderID'] = ($this->input->post('orderID'))?$this->input->post('orderID'):'';
		if($this->data['orderID'] == ''){
			redirect(base_url('cart'),"refresh");
		}
		if($this->session->userdata('is_guest')){
			$user_id = $this->session->userdata('guest_id');
		}else{
			$user_id = $this->session->userdata('userid');
		}
		$user = $this->User_Model->checkUserId($user_id, true);
		$this->data['payment_error'] = false;
		$this->config->load('paypal');
		$card = $this->session->userdata('checkout_card');
		if($this->input->post('paypal_transID') == ""){
			$card_view = $this->session->userdata('checkout_card_view');
			$card_name = $card_view['card_name'];
			$save_card = $card_view['save_card'];
			$paywithcard = $card_view['paywithcard'];
			$customer_id = $card_view['customer_id'];
			$card_id= $card_view['card_id'];
			$token = $card_id;
		}else{
			$card_view = "";
			$this->session->set_userdata("payment_method", "paypal");
		}
		$location = $this->session->userdata('checkout_ship');
		$this->data['tax'] = 0;//$this->getTax($location['zipcode'], $this->cart->total());
		
		$hasDiscount = ($this->session->userdata('discount_data'))?true:false;
		$discount = ($this->session->userdata('discount_data'))?$this->session->userdata('discount_data'):array();
		
		$PayerName = array(
			'salutation' 	=> 'Mr.',
			'firstname' 	=> $user->firstname,
			'middlename' 	=> $user->middlename,
			'lastname' 		=> $user->lastname,
			'suffix' 		=> ''
		); 
		$PayerInfo = array();
		if($this->input->post('paypal_transID') == ""){
			$checkout_billing = $this->session->userdata('checkout_billing');
			$checkout_billing['state'] = (is_numeric($checkout_billing['state']))?getCountryNameByKeyValue('id', $checkout_billing['state'], 'code', true,'tbl_states'):$checkout_billing['state'];
			$checkout_billing['country'] = getCountryNameByKeyValue('id', $checkout_billing['country'], 'nicename', true);
			$checkout_billing['name'] = $card_view['holder'];
			$BillingAddress = array(
				'id'			=> (isset($checkout_billing['id']))?$checkout_billing['id']:"",
				'street' 		=> $checkout_billing['address_1'],
				'street2' 		=> $checkout_billing['address_2'],
				'city' 			=> $checkout_billing['city'],
				'state' 		=> $checkout_billing['state'],
				'countrycode' 	=> strtoupper($checkout_billing['country']),
				'zip' 			=> $checkout_billing['zipcode'],
				'phonenum' 		=> $checkout_billing['phone'],
			);
		}else{
			$BillingAddress = "";
			$checkout_billing = "";
		}
		$location['state'] = (is_numeric($location['state']))?getCountryNameByKeyValue('id', $location['state'], 'code', true,'tbl_states'):$location['state'];
		$location['country'] = getCountryNameByKeyValue('id', $location['country'], 'nicename', true);
		$ShippingAddress = array(
			'id' 				=> (isset($location['id']))?$location['id']:"",
			'shiptoname' 		=> $location['name'],
			'shiptostreet' 		=> $location['address_1'],
			'shiptostreet2' 	=> $location['address_2'],
			'shiptocity' 		=> $location['city'],
			'shiptostate' 		=> $location['state'],
			'shiptozip' 		=> $location['zipcode'],
			'shiptocountry' 	=> strtoupper($location['country']),
			'shiptophonenum' 	=> $location['phone'],
		);
		$OrderItems = array();
		$cartItems = array();
		$shipping_total = 0;
		$hubx_ids = array();
		foreach ($this->cart->contents() as $key=>$items):
			$shipping_total = $shipping_total+$items['shipping_price'];
			$this->data['tax'] += $items['tax'];
			$Item = array(
				'l_name' 				 => $items['name'],
				'l_desc' 				 => '',
				'l_amt' 				 => $items['price'],
				'l_number' 				 => $items['prd_id'],
				'l_qty' 				 => $items['qty'],
				'l_shipping_id' 		 => $items['shipping_id'],
				'l_item_gross_amount' 	 => (($items['price'] * $items['qty']) + $items['shipping_price'] + $items['tax']),//(($items['price'] * $items['qty']) + $items['shipping_price']) + ($this->getTax($location['zipcode'], $items['price']) * $items['qty']),
				'l_shipping_amt' 		 => $items['shipping_price'], 
				'l_taxamt' 				 => $items['tax'],//($this->getTax($location['zipcode'], $items['price'])) * ($items['qty']),  // Item's sales tax amount.
				'l_ebayitemnumber' 		 => '', 				// eBay auction number of item.
				'l_ebayitemauctiontxnid' => '', 				// eBay transaction ID of purchased item.
				'l_ebayitemorderid' 	 => '' 					// eBay order ID for the item.
			);
			$created_id = $this->Order_Model->getOrderProduct($items['prd_id'],'created_id,email');
			$order = $this->session->userdata('order_data');
			$order['created_id'] = $created_id[0]['created_id'];
			if(!isset($sellerEmail[$created_id[0]['created_id']])){
				$sellerEmail[$created_id[0]['created_id']] = array();
			}
			if(!in_array($created_id[0]['email'],$sellerEmail[$created_id[0]['created_id']])){
				$sellerEmail[$created_id[0]['created_id']]['email'] = $created_id[0]['email'];
				$sellerEmail[$created_id[0]['created_id']]['product_detail'][] = array($key=>$items);
				$sellerEmail[$created_id[0]['created_id']]['user_detail'] = $order;
			}else{
				$sellerEmail[$created_id[0]['created_id']]['product_detail'][] = array($key=>$items);
				$sellerEmail[$created_id[0]['created_id']]['user_detail'] = $order;
			}
			$cartData = array($key=>$items);
			if($this->session->userdata('is_guest')){
				$buyerEmail[$this->session->userdata['guest_id']]['product_detail'][] = $cartData;
				$buyerEmail[$this->session->userdata['guest_id']]['user_detail'] = $order;
			}else{
				$buyerEmail[$this->session->userdata['userid']]['product_detail'][] = $cartData;
				$buyerEmail[$this->session->userdata['userid']]['user_detail'] = $order;
			}
			$order = array(
				'cart_data' 	=> serialize($cartData),
				'customer_id' 	=> $user->id,
				'created_id' 	=> $order['created_id'],
			);
			array_push($cartItems, $Item);
			if($items['hubx_id'] != ""){
				$hubx_ids[] = $items['hubx_id'];
			}
			$this->Order_Model->placeOrder($order);
		endforeach;
		if(!empty($hubx_ids)){
			$hubx_product_detail = $this->getHubxProductDetail($hubx_ids);
			$verify_hubx_product = $this->verifyHubxProductDetail($hubx_ids,$hubx_product_detail);
			$this->Cart_Model->verifyCartItems($verify_hubx_product['cart'], "core");
		}
		$cart_total = $this->cart->total();
		$hubx_order_id = array();
		if($hasDiscount){
			$cart_total = $cart_total - $discount['discount_amount'];
		}
		$grand_total = $cart_total+$shipping_total+$this->data['tax'];//$this->getTax($location['zipcode'], $cart_total);
		$PaymentDetails = array(
			'amt' 			=> number_format((float)$grand_total, 2, '.', ''),
			'currencycode' 	=> 'USD',
			'shippingamt' 	=> $shipping_total,
			'taxamt' 		=> $this->data['tax'] ,//$this->getTax($location['zipcode'], $cart_total),
			'desc' 			=> 'Web Order',
			'custom' 		=> '', 
			'invnum' 		=> $this->data['orderID'],
			'notifyurl' 	=> '' 
		); 
		$location = array('billing'=> $checkout_billing, 'shipping'=>$location);
		$payment_method = ($this->input->post('paypal_transID') != "") ? "paypal" : "card";
		$this->Cart_Model->save_order($this->data['orderID'], $this->cart->contents(), $PaymentDetails, $location, $card_view, $PayerInfo, $user_id, "web", $discount, $payment_method, $hubx_order_id);
		switch ($this->session->userdata("payment_method")) {
			case "paypal":
				$payer = json_decode($this->input->post("paypal_payer"));
				//echo "<pre>";print_r($payer);die();
				//$paymentResponse =$this->Cart_Model->paypal_payment($card, $PayerInfo, $PayerName, $BillingAddress, $ShippingAddress, $PaymentDetails, $OrderItems);
				$paymentResponse['status'] = "1";
				$paymentResponse['transaction_id'] = $this->input->post('paypal_transID');
				$paymentResponse['TIMESTAMP'] = date('Y-m-d H:i:s');
				$paymentResponse['payment'] = $PaymentDetails['amt'];
				$card_id = "paypal";
				$data2 = array("holder_name"=>$payer->name->given_name." ".$payer->name->surname, "email"=>$payer->email_address, "customer_id"=>$payer->payer_id);
				$this->db->where(array('default'=> "1", 'save_card'=>"1", 'user_id'=>$user_id))
				->update(DBPREFIX.'_user_cards', $data2);
				$this->session->unset_userdata("payment_method");
				break;
			case "stripe":
				$this->load->library('Stripe', 'stripe');
				$package = new stdClass();
				$package->package_price = number_format((float)$grand_total, 2, '.', '')*100;
				$package->package_name = 'Purchasing items on Zabee';
				$stripe_key = $this->config->item('stripe_key');
				if($token == ''){
					$paymentResponse = array('status'=>0, 'message'=>'Error in transaction, please try again later');
				} else {
					$paymentResponse = $this->stripe->doTransaction($token, $package, $this->session->userdata('email'),$stripe_key, $customer_id);
					break;
				}
		}
		if($paymentResponse['status'] == 0){
			$errors = array('Errors'=>$paymentResponse['message']);
			$this->session->set_flashdata('payment_error',$errors);
			$this->data['payment_error'] = true;
			$this->Cart_Model->deleteOrder($this->data['orderID'], $errors);
			redirect(base_url('checkout/payment'),"refresh");
		} else {
			$data = array('transaction_id'=>$paymentResponse['transaction_id'], 'card_id'=>$card_id);
			$response = $this->Cart_Model->complete_order($this->data['orderID'], $data, $user_id);
			$this->session->set_userdata('PayMentResult', $paymentResponse);
			if($response){ 
				$buyer_email = $this->session->userdata('email');  
				$seller_data = array();
				$seller_id = array();
				foreach ($this->cart->contents() as $key => $value){
						if(!in_array($value['seller_id'], $seller_id)){
							$seller_data[$value['seller_id']][] = $value;
						}else{
							$seller_data[$value['seller_id']][] = $value;
						}
				}
				$i = 0;
				$warehouse_email = "";
				foreach($seller_data as $key2 =>$sd){
					foreach($sd as $key => $s){
						if($s['warehouse_id'] != "" && $s['warehouse_id'] > 0){
							$warehouse_email = $this->Utilz_Model->getWarehouseEmails($s['warehouse_id']);
						}
					}
					$seller_email=$this->Utilz_Model->get_seller_email($key2);
					$first_name = $this->session->userdata('firstname');
					// print_r($seller_email);
					if($seller_email != ""){
						// echo "inside";
						$subject = "You have a new Order";
						$text = $this->lang->line("item_sold_email");
						$body[]= $this->Utilz_Model->send_mail($location,$sd,$this->data['orderID'],$seller_email->email, $type="seller",$subject,$text,$first_name);
						$subject = 'Your Zab.ee Order '.$this->data['orderID'].' Is Placed';
						$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Thank you for shopping with Zab.ee. Your order has been placed, please see below for details. You will receive another email when the order is shipped.</p>';
						$body[]= $this->Utilz_Model->send_mail($location,$sd,$this->data['orderID'],$buyer_email, $type="buyer",$subject,$text,$first_name); 
						$this->Utilz_Model->send_mail($location,$sd,$this->data['orderID'],$this->config->item("support_email"), $type="buyer",$subject,$text,$first_name); 
						// echo"<pre>"; print_r($body); die();
					}
					if($warehouse_email != ""){
						$subject = "You have a new Order";
						$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">'.$this->lang->line("item_sold_email").'</p>';
						$body[]= $this->Utilz_Model->send_mail($location,$sd,$this->data['orderID'],$warehouse_email, $type="warehouse",$subject,$text,$first_name); 
					}
				}
				$referral = array();
				foreach($this->cart->contents() as $item){
					if(isset($item['referral']) &&  $item['referral'] != ""){
						$referral[] = array(
							'code'  => $item['referral'],
							'price' => floatval($item['price'] * $item['qty']),
							'pv_id' => $item['id']
						);
					}
				}
				// print_r($referral); die();
				if(!empty($referral)){
					$this->Cart_Model->referral_purchase($referral, $user_id, $paymentResponse['transaction_id'], $this->config->item('percent'));
				}
				// die();
				$this->session->unset_userdata('confirm_page');
				redirect(base_url('home/order_complete'),"refresh");
			} else {
				$this->data['payment_error'] = true;
			}
		}
	}
	
	public function removeItem($row_id){
		$this->oCart->removeCartItem($row_id);	
		$this->oCart->getcurrentcart();
	}

	public function deleteaddress($location_id){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($location_id == ''){
			redirect(base_url('checkout?status=invalid_addressbook&code=001'));
				die();
		}
		$deletelocations = $this->AddressBook->deleteaddress($location_id,$user_id);
		if($deletelocations){
			redirect(base_url('checkout?status=deleted'));
		}else{
			redirect(base_url('checkout?status=error'));
		}
	}

	public function useAddressforbuyer($location_id){
		if($location_id == ''){
			redirect(base_url('checkout?status=invalid_addressbook&code=001'));
			die();
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$insert_data = array(
			'updated' 		=> date('Y-m-d H:i:s'),
			'user_id' 		=> $user_id,
			'use_address' 	=> 1
		);
		$insert_data2 = array(
			'updated' 		=> date('Y-m-d H:i:s'),
			'user_id' 		=> $user_id,
			'use_address' 	=> 0
		);
		$selectedlocations = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type'], $location_id);
		$data = array(
			'id' 		=> $selectedlocations->id,
			'name' 		=> $selectedlocations->fullname,
			'phone' 	=> $selectedlocations->contact,
			'address_1' => $selectedlocations->address_1,
			'address_2' => $selectedlocations->address_2,
			'city' 		=> $selectedlocations->city,
			'state' 	=> $selectedlocations->state,
			'country' 	=> $selectedlocations->country,
			'zipcode' 	=> $selectedlocations->zip,
		);
		$resp = $this->AddressBook->update_addressonly($insert_data ,$insert_data2, $user_id, $location_id);
		if($resp && $selectedlocations){
			$this->session->set_userdata('checkout_ship', $data);
			redirect(base_url('buyer/address_book'));
		}
	}

	public function tandc(){
		$this->data['page_name'] = 'tandc';
		$this->data['hasScript'] = true;
		$this->data['title']	 = "Terms & Conditions";
		$this->load->view('front/template', $this->data);
	}

	// public function privacypolicy(){
	// 	$this->data['page_name'] = 'pp';
	// 	$this->data['hasScript'] = true;
	// 	$this->data['title'] 	 = "Privacy Policy";
	// 	$this->load->view('front/template', $this->data);
	// }
	
	private function formatCC($post_data){
		$cc = array(
			'name' 		=> $post_data['card_name'],
			'number' 	=> str_replace('-', '', $post_data['card_number']),
			'exp_month' => $post_data['exp_date'],
			'exp_year' 	=> $post_data['exp_year'],
			'cvc' 		=>  $post_data['ccv']
		);
		return $cc;
	}

	public function ProcessRefund(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->library('Stripe', 'stripe');
		$stripe_key = $this->config->item('stripe_key');
		$charge_id = $this->Order_Model->getTransactionId($_POST['order_id']);
		$data = $this->Order_Model->getAmount($_POST['order_id'], $_POST['td_row_id']);
		if($data){
			$amount = number_format((float)$data->amount, 2, '.', '')*100;
			if($data->seller_id == $user_id){
				if($charge_id != ""){
					$refund = $this->stripe->getRefund($charge_id, $amount, $stripe_key);
					if($refund['status'] == 1){
						$recharge_id = $this->Order_Model->addRefundId($_POST['order_id'], $refund['charge_id'], $_POST['td_row_id']);
							if($recharge_id){
								$this->session->set_flashdata("success","Refunded successfully.");
								$resp['status'] = 1;
							} else {
								$this->session->set_flashdata("error","Refund not available.");
								$resp['status'] = 0;
							}
							echo json_encode($resp, JSON_UNESCAPED_UNICODE);
					} else {
						$this->session->set_flashdata("error", "Order has already been refunded.");
						$resp['status'] = 0;
						echo json_encode($resp, JSON_UNESCAPED_UNICODE);
					}
				}
			}else{
				$resp['msg'] = "Invalid Id";
				$resp['status'] = 0;
				echo json_encode($resp, JSON_UNESCAPED_UNICODE);
			}
		}
	}

	public function getDefault($user_id){
		$default = $this->AddressBook->getDefaultAddressAndCard($user_id);
		return $default;
	}

	public function addCard(){
		if(!$this->isloggedin){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if(!$this->cart->contents()){
			redirect(base_url("cart"),"refresh");
		}else if(!$this->session->userdata('checkout_ship')){
			redirect(base_url("checkout"),"refresh");
		}
		$this->data['page_name'] = 'payment';
		$this->data['hasScript'] = false;
		$this->data['title'] = "Add Card";
		$this->load->view('front/template', $this->data);
	}

	public function updateCard(){
		$response = array("status"=>0);
		$data = $this->input->post();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$getCard = $this->User_Model->getStripeCards($user_id, $data['id']);
		if($getCard['status'] == 1){
			$this->load->library('Stripe', 'stripe');
			$stripe_key = $this->config->item('stripe_key');
			$card_id = $getCard['data'][0]->card_id;
			$customer_id = $getCard['data'][0]->customer_id;
			$stripeData = array('exp_month'=> $data['expiry_month'], 'exp_year'=>$data['expiry_year'],'name'=>$data['holder_name']);
			$cards = $this->stripe->updateCard($stripeData, $card_id, $customer_id, $stripe_key);
			if($cards['status'] == 1){
				$update = $this->User_Model->updateData($data,DBPREFIX.'_user_cards',array("id"=>$data['id'],"user_id"=>$user_id));
				if($update){
					$response['status'] = 1;
					$response['msg'] = "Card update successfully!";
				}else{
					$response['msg'] = "Unable to update card!";	
				}
			}else{
				$response['msg'] = "Unable to update card!";
			}
		}else{
			$response['msg'] = "Card not found!";
			
		}
		echo json_encode($response);
	}
	
	public function apply_voucher(){
		$response = array('status'=>0, 'message'=>'Error, please try again later');
		$code = $this->input->post('voucher_code');
		$discount_amount = 0;
		$discounted_cart_toatl = 0;
		
		if($code != ''){
			if($this->session->userdata('userid')){
				$user_id = $this->session->userdata('userid');
				$isAdmin = ($this->session->userdata('user_type') == 1)?true:false;
			}
			$this->load->model("admin/Policies_Model");
			$voucher = $this->Policies_Model->getVoucher('', '', $code);
			$cart_total = $this->cart->total();
			$cart = $this->cart->contents();
			$ba = $this->session->userdata('checkout_card');
			$locations = $this->AddressBook->get_address($user_id,$ba['address_id'],$this->data['user_type']);
			$zip = ($locations['status'] == 1)?$locations['data']->zip:0;
			$response = $this->Cart_Model->calculateVoucher($code, $cart, $voucher, $cart_total, $discount_amount, $discounted_cart_toatl, $user_id, $isAdmin, $zip);
			
		}/*
		if($response['status'] == 0)
			$this->session->unset_userdata('discount_data');*/
		echo json_encode($response);
	}
	public function formatStripeAddress($data){
		$data = array(
			'address_line1'=> $data['address_1'],
			'address_line2'=> $data['address_2'],
			'address_city'=> $data['city'],
			'address_state'=> (is_numeric($data['state']))?getCountryNameByKeyValue('id', $data['state'], 'code', true,'tbl_states'):$data['state'],
			'address_zip'=> $data['zipcode'],
			'address_country'=> getCountryNameByKeyValue('id', $data['country'], 'nicename', true),
		);
		return $data;
	}

	public function checkDiscountMsg(){
		$count = 0;
		// echo "<pre>"; print_r($this->cart->contents()); die();
		foreach ($this->cart->contents() as $items) {
			if($items['update_msg'] != ""){
				$count++;
			}
		}
		return $count;
	}

	// public function fromPaypal($order_id){
	// 	$cart = array();
	// 	if($this->cart->total_items() > 0){
	// 		foreach($this->cart->contents() as $items){
	// 			if(isset($cart[$items['seller_id']])){
	// 				$cart[$items['seller_id']][] = $items;
	// 				$cart[$items['seller_id']]['subtotal'] = $cart[$items['seller_id']]['subtotal']+$items['subtotal'];
	// 			}else{
	// 				$cart[$items['seller_id']] = array();
	// 				$cart[$items['seller_id']][] = $items;
	// 				$cart[$items['seller_id']]['subtotal'] = $items['subtotal'];
	// 				$cart[$items['seller_id']]['store_name'] = isset($items['store_name'])?$items['store_name']:"";
	// 			}
	// 		}
	// 	}
	// 	$location 						= $this->session->userdata('checkout_ship');
	// 	$this->data['tax'] 				= $this->getTax($location['zipcode'], $this->cart->total());
	// 	$this->data['zip_code'] 		= $location['zipcode'];
	// 	$this->data['shipping_address'] = $location;
	// 	$this->data['billing_address']  = $this->session->userdata('checkout_billing');
	// 	$this->data['discount'] 		= ($this->session->userdata('discount_data'))?$this->session->userdata('discount_data'):array();
	// 	$this->data['hasDiscount'] 		= ($this->session->userdata('discount_data'))?true:false;
	// 	$this->data['orderID'] 			= $order_id;
	// 	$this->data['cart'] 			= $cart;
	// 	$this->data['payType']			= "paypal";
	// 	$this->data['hasScript'] 		= true;
	// 	$this->data['page_name'] 		= 'confirm_payment';
	// 	$this->load->view('front/template', $this->data);
	// }
	public function orderHubxProduct($location,$detail,$order_id){
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$return = array();
		$this->checkToken();
		$access_token = $this->session->userdata("access_token");
		foreach($detail as $item){
			//echo "<pre>";print_r($item);
			$data = array("comments"=>"",
				"terms"=> "",
				"billingAddressCode"=> "",
				"shippingAddressCode"=> "",
				"billingAddress"=> array(
				"line1"=> $location["billing"]["address_1"],
				"line2"=> $location["billing"]["address_2"],
				"country"=> $location["billing"]["country"],
				"city"=> $location["billing"]["city"],
				"state"=> $location["billing"]["state"],
				"zipCode"=> $location["billing"]["zipcode"]
				),
				"shippingAddress"=> array(
				"companyName"=> "",
				"recipientName"=> $location["shipping"]["name"],
				"recipientPhoneNumber"=> $location["shipping"]["phone"],
				"line1"=> $location["shipping"]["address_1"],
				"line2"=> $location["shipping"]["address_2"],
				"country"=> $location["shipping"]["country"],
				"city"=> $location["shipping"]["city"],
				"state"=> $location["shipping"]["state"],
				"zipCode"=> $location["shipping"]["zipcode"]
				),
				"shippingCost"=> 0,
				"details"=> array($item),
				"purchaseOrdernumber"=> $order_id,
				"creationDate"=> $date_utc,
				"version"=> 0
			);
			$data = json_encode($data);
			//print_r($data);
			$hubx_return = $this->hubx->order_hubx_product($access_token,$data);
			$hubx_return = json_decode($hubx_return);
			if(isset($hubx_return->metadata)){
				if(isset($hubx_return->metadata->hubxDocumentNumber)){
					$return[$item['vendorPartNumber']]["hubx_order_id"] = $hubx_return->metadata->hubxDocumentNumber;
				}
				if(isset($hubx_return->metadata->lines)){
					$return[$item['vendorPartNumber']]["order_details"] = $hubx_return->metadata->lines;
				}
				$return[$item['vendorPartNumber']]["hubx_order_status"] = $hubx_return->orderStatus;
				$return[$item['vendorPartNumber']]["hubx_status"] = $hubx_return->success;
				if(isset($hubx_return->error)){
					$return[$item['vendorPartNumber']]["error"] = $hubx_return->error;
				}
			}
		}
		return $return;
	}
	public function getHubxProductDetail($hubx_id){
		$this->checkToken();
		$cart_data = $this->cart->contents();
		$access_token = $this->session->userdata("access_token");
		$data = json_encode($hubx_id);
		$hubx_return = $this->hubx->get_hubx_product_detail($access_token,$hubx_id,$data);
		$hubx_return = json_decode($hubx_return);
		return $hubx_return;
	}
	public function verifyHubxProductDetail($hubx_id,$product_detail){
		$return = array("status"=>0,"msg"=>array());
		$cart = $this->cart->contents();
		$data = array();
		$hubx_id = array_column($cart,"hubx_id","rowid");
		foreach($hubx_id as $rowid=>$hubx_data){
			if($hubx_data){
				$hubx_key = array_search($hubx_data,$product_detail);
				$profit = ($product_detail[$hubx_key]->unitPrice/100)*15;
				$hubx_price = $product_detail[$hubx_key]->unitPrice+$profit;
				if($product_detail[$hubx_key]->unitPrice != $cart[$rowid]['original']){
					$data['price'] = $product_detail[$hubx_key]->unitPrice;
					$return['status'] = 1;
					$return['msg'][$rowid]["price"] = "Price has been updated";
					$cart[$rowid]['original'] = $product_detail[$hubx_key]->unitPrice;
				}
				if($product_detail[$hubx_key]->moq < $cart[$rowid]['qty']){
					$return['status'] = 1;
					$return['msg'][$rowid]["minimum_qty"] = "Select at least ".$product_detail[$hubx_key]->moq." quantity";
					$cart[$rowid]['qty'] = $product_detail[$hubx_key]->moq;
				}
				if($product_detail[$hubx_key]->availability != $cart[$rowid]['max_qty']){
					$data['quantity'] = $product_detail[$hubx_key]->availability+$cart[$rowid]['sell_quantity'];
					$return['status'] = 1;
					$return['msg'][$rowid]["max_quantity"] = "Max quantity has been updated";
					$cart[$rowid]['max_qty'] = $product_detail[$hubx_key]->availability;
					if($product_detail[$hubx_key]->availability == 0){
						$cart[$rowid]['qty'] = 0;
					}
				}
				if(!empty($data)){
					$this->db->where("product_variant_id",$cart[$rowid]['id']);
					$this->db->update(DBPREFIX."_product_inventory",$data);
				}
				$data = array();
			}
		}
		if($return['status'] == 1){
			$this->cart->update($cart);
		}
		$return['cart'] = $cart;
		return $return;
	}
	public function cancel_hubx_order($order_id){
		$this->checkToken();
		$access_token = $this->session->userdata("access_token");
		$return = $this->hubx->cancel_order_hubx_product($access_token,$order_id);
		//print_r($return);
		return $return;
	}
}
?>
