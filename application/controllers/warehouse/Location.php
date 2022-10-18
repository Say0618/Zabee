<?php
class Location extends SecureAccess 
{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("Warehouse_Model");
		$this->data = array(
			'page_name' => 'store_info',
			'isScript' => false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' => $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserWarehouse){
			redirect(base_url('warehouse'));
		}
			$this->lang->load('english', 'english');
	}

	public function index()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$locations = $this->Secure_Model->getData("tbl_warehouse","",array("user_id"=>$user_id));
		 $this->data['locations'] = null;
		 if($locations){
		   $this->data['locations'] =  $locations;
		  }
		$this->data['page_name'] = 'location_view';
		$this->data['Breadcrumb_name'] = 'Address';
		$this->data['isScript'] = true;
		$this->load->view("warehouse/template",$this->data);
	}

	public function update($location_id = '')
	{
		if($location_id == ''){
			redirect(base_url('seller/location?status=invalid_location&code=001'));
			die();
		}
		$this->data['location_id'] = $location_id;
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$post_data = $this->input->post();

		/*
			Validations and submittion
		*/
		$locations = $this->User_Model->getUserLocationsByUserId($user_id, $location_id);
		if(!$locations){
			redirect(base_url('seller/location?status=invalid_location&code=002'));
			die();
		}
		$location = $locations[0];
		$this->data['location'] =  $location;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';

		$this->form_validation->set_rules('billfullname','Billfullname','trim|required|min_length[2]');
		$this->form_validation->set_rules('billcontact','Billcontact','trim|required|min_length[2]');
		$this->form_validation->set_rules('billaddress1','Billaddress1','trim|required|min_length[2]');
		$this->form_validation->set_rules('billaddress2','Billaddress2','trim|required|min_length[2]');
		$this->form_validation->set_rules('billstate','Billstate','trim|required|min_length[2]');
		$this->form_validation->set_rules('billcity','Billcity','trim|required|min_length[2]');
		$this->form_validation->set_rules('billzip','Billzip','trim|required');
		$this->form_validation->set_rules('shipfullname','Shipfullname','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipcontact','Shipcontact','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipaddress1','Shipaddress1','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipaddress2','Shipaddress2','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipstate','Shipstate','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipcity','Shipcity','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipzip','Shipzip','trim|required');
		if($this->form_validation->run() == TRUE){
			$resp = $this->User_Model->update_location($post_data, $user_id, $location_id);
			if($resp){
				redirect(base_url('seller/location?status=success'));
				die();
			}
		}
		$this->load->model('Utilz_Model');
		$this->data['countryList'] 		= $this->Utilz_Model->countries();
		$this->data['page_name'] 		= 'location_update';
		$this->data['Breadcrumb_name'] 	= 'Update Address';
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template",$this->data);
	}

	public function add()
	{
		$post_data = $this->input->post();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$user_type = "seller";

		/*
			Validations and submittion
			
		*/
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('billfullname','Billfullname','trim|required|min_length[2]');
		$this->form_validation->set_rules('billcontact','Billcontact','trim|required|min_length[2]');
		$this->form_validation->set_rules('billaddress1','Billaddress1','trim|required|min_length[2]');
		$this->form_validation->set_rules('billstate','Billstate','trim|required|min_length[2]');
		$this->form_validation->set_rules('billcity','Billcity','trim|required|min_length[2]');
		$this->form_validation->set_rules('billzip','Billzip','trim|required');
		$this->form_validation->set_rules('shipfullname','Shipfullname','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipcontact','Shipcontact','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipaddress1','Shipaddress1','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipstate','Shipstate','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipcity','Shipcity','trim|required|min_length[2]');
		$this->form_validation->set_rules('shipzip','Shipzip','trim|required');
		if($this->form_validation->run() == TRUE){
			$resp = $this->User_Model->add_location($post_data, $user_id, $user_type);
			if($resp){
				redirect(base_url('seller/location?status=success'));
				die();
			}
		/* End */
		} else {
			echo validation_errors();
		}
		$this->load->model('Utilz_Model');
		$this->data['countryList'] 		= $this->Utilz_Model->countries();
		$this->data['page_name'] 		= 'location_add';
		$this->data['Breadcrumb_name'] 	= 'Add Address';
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template",$this->data);
	}

	public function get(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Warehouse_Model->getWarehouse($user_id, $search, $offset, $length);
		$action = '<a href="' . site_url('warehouse/$1') . '"><i class="fa fa-edit"></i> Update </a>';
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
	}
	
	public function delete(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$location_id = $this->input->post('id');
		$value = $this->input->post('value');
		$resp = $this->User_Model->deleteLocation($location_id, $user_id, $value);
	}

	public function hard_delete($location_id = ''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->User_Model->hard_delete_location($location_id, $user_id);
	}
	
	public function is_default($location_id = ''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->User_Model->is_default_address($location_id, $user_id);
	}
}
?>