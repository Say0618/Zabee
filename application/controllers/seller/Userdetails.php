<?php  
class Userdetails extends SecureAccess{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Userdetails_Model");	
		$this->load->model("admin/Profile_Model");	
		$this->load->model("admin/User_Model");	
		$this->load->model("admin/Utilz_Model");					
		$this->load->model("Product_Model");	
		$this->load->model('Cart_Model');				
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);		
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
	}

	public function index(){
		redirect(base_url('seller/dashboard/seller_management'));
	}

	public function seller_details($id = ""){
		if($id != ""){ 
			$user_id = $this->session->userdata('userid');
			$this->data['user_store'] = "";
			$checkUserStore = $this->User_Model->checkUserStore($id);
			if($checkUserStore['rows'] > 0){
				$this->data['user_store'] = $checkUserStore['result'];
			}
			$user_data = $this->Profile_Model->getProfiles($id);
			$user_data_image = $this->User_Model->getUserForAcc($id);
			$this->data['user_data_image'] 		= $user_data_image;
			$this->data['users'] 				= $this->User_Model->get_users($id);
			$this->data['profile']				= $this->User_Model->get_profile($id);
			$this->data['user_data'] 			= $user_data;
			$this->data['id_new'] 				= $id;
			$this->data['getLegalBusniessType'] = $this->Secure_Model->getLegalBusniessType();
			$this->load->model('Utilz_Model'); 
			$this->data['countryList'] 			= $this->Utilz_Model->countries();
			$this->data['page_name'] 			= 'seller_details';
			$this->data['Breadcrumb_name'] 		= $this->lang->line('user_details');
			$this->data['isScript'] 			= true;
			$this->load->view("admin/admin_template",$this->data);	
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function buyer_details($id = ""){
		if($id != ""){
			if($this->session->userdata('userid')){
				$user_id = $this->session->userdata('userid');
			} else {
				$user_id = $this->session->userdata('admin_id');
			}
			$this->data['user_store'] = "";
			$checkUserStore = $this->User_Model->checkUserStore($id);
			if($checkUserStore['rows'] > 0){
				$this->data['user_store'] = $checkUserStore['result'][0];
			}
			$user_data = $this->Profile_Model->getProfiles($id);
			$user_data_image = $this->User_Model->getUserForAcc($id);
			$this->data['user_data_image'] 		= $user_data_image;
			$this->data['users'] 				= $this->User_Model->get_users($id);
			$this->data['profile'] 				= $this->User_Model->get_profile($id);
			$this->data['user_data'] 			= $user_data;
			$this->data['id_new'] 				= $id;
			$this->load->model('Utilz_Model');
			$this->data['countryList'] 			= $this->Utilz_Model->countries();
			$this->data['page_name'] 			= 'buyer_details';
			$this->data['Breadcrumb_name'] 		= $this->lang->line('user_details');
			$this->data['isScript'] 			= true;
			$this->load->view("admin/admin_template",$this->data);	
		}else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function get_product_details($id = ""){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$user_type = $this->session->userdata('user_type');
		if($user_type != 1 && $user_type != ""){
			$id = $this->session->userdata('userid');
		}
		$totalProduct = $this->Product_Model->productCount($search['value'],$id);
		$data = $this->Product_Model->productPipline($search['value'],$request,$id,$totalProduct->totalProduct);
		echo json_encode($data);
	}

	public function buyer_order_history($user_id=""){
		$orders = $this->Cart_Model->getOrderByClientID($user_id, '', '', '');
		if(!empty($orders)){	
			foreach($orders as $o){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				if($o->product_image == ""){
					$o->product_image = $productImage[0]->is_primary_image; 
				}
			}
		}
		$this->data['orders'] 			= $this->Cart_Model->formatOrderList($orders, 'data');
		$this->data['page_name'] 		= 'buyer_order_history';
		$this->data['Breadcrumb_name']	= $this->lang->line('order_history'); 
		$this->data['isScript']			= true;
		$this->load->view("admin/admin_template",$this->data);	
	}
}	
?>