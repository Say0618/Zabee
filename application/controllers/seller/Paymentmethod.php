<?php 
class Paymentmethod extends SecureAccess{
	
	function __construct(){
		parent::__construct();
		$this->load->model("admin/PaymentMethod_Model");
		$this->data = array(
			'page_name' => 'store_info',
			'isScript' => false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' => $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
	}

	public function index(){
		$data['oObj'] = $this;
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');
		}
		/*Validations and submittion*/
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] 			= '';
		$this->data['gateway_id'] 		= '';
		$this->data['paymentgateway'] 	= false;
		$paymentmethod 					= $this->input->post('paymentmethod');
		if($paymentmethod == 'Paypal'){
			$this->form_validation->set_rules('PayPalAPIUsername','PayPal_APIUsername','trim|required');
			$this->form_validation->set_rules('PayPalAPIPassword','PayPal_APIPassword','trim|required');
			$this->form_validation->set_rules('PayPalAPISignature','PayPal_APISignature','trim|required');
			$this->form_validation->set_rules('PayPalAPIApplicationID','PayPal_APIApplicationID','trim|required');
		}
		if($paymentmethod == 'Stripe'){
			$this->form_validation->set_rules('StripeAPIkey','Stripe_APIkey','trim|required');
		}
		if($paymentmethod == 'BT'){
			$this->form_validation->set_rules('BrainTree_MerchantID','BrainTreeMerchantID','trim|required');
			$this->form_validation->set_rules('BrainTree_PublicKey','BrainTreePublicKey','trim|required');
			$this->form_validation->set_rules('BrainTree_PrivateKey','BrainTreePrivateKey','trim|required');
		}
		if($this->form_validation->run() == TRUE){
			$post_data = $this->input->post();
			$this->data['gateway_id'] = $this->input->post('gateway_id');
			$insert_data = array(
							'created' 					=> date('Y-m-d H:i:s'),
							'updated' 					=> date('Y-m-d H:i:s'),
							'userid' 					=> $user_id,
							'PayPal_APIUsername' 		=> $post_data['PayPalAPIUsername'],
							'PayPal_APIPassword' 		=> $post_data['PayPalAPIPassword'],
							'PayPal_APISignature' 		=> $post_data['PayPalAPISignature'],
							'PayPal_APIApplicationID' 	=> $post_data['PayPalAPIApplicationID'],
							'Stripe_APIkey' 			=> $post_data['StripeAPIkey'],
							'BrainTree_Merchant_ID' 	=> $post_data['BrainTree_MerchantID'],
							'BrainTree_Public_Key' 		=> $post_data['BrainTree_PublicKey'],
							'BrainTree_Private_Key' 	=> $post_data['BrainTree_PrivateKey'],
							'paymentmethod' 			=> $paymentmethod,
							'active' 					=> 1
						);
			if($this->data['gateway_id'] == ''){
				$resp = $this->PaymentMethod_Model->paymentmethod_add($insert_data);
				if($resp){
					$this->session->set_flashdata("success","Payment Gateway added successfully.");
					redirect(base_url('seller/paymentmethod?status=success&action=add'));
					die();
				}
			} else {
				unset($insert_data['created']);
				$resp = $this->PaymentMethod_Model->paymentmethod_update($insert_data, $this->data['gateway_id']);
				if($resp){
					$this->session->set_flashdata("success","Payment Gateway edited successfully.");
					redirect(base_url('seller/paymentmethod?status=success&action=update'));
					die();
				}
			}
		}
		$payment_gateway = $this->PaymentMethod_Model->getPaymentGateway($user_id);
		if($payment_gateway){
			$this->data['gateway_id'] = $payment_gateway->id;
			$this->data['paymentgateway'] = $payment_gateway;
		}
		$this->data['page_name'] 		= 'paymentmethod_update';
		$this->data['Breadcrumb_name'] 	= 'Payment Method';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}
}
?>