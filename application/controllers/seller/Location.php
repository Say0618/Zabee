<?php
class Location extends SecureAccess 
{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/User_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
	}

	public function index()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$locations = $this->User_Model->getUserLocationsByUserId($user_id);
		 $this->data['locations'] = null;
		 if($locations){
		   $this->data['locations'] =  $locations;
		  }
		$this->data['page_name'] 		= 'location_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('address');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
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
		$this->data['Breadcrumb_name'] 	= $this->lang->line('update_address');
		$this->data['isScript']			= true;
		$this->load->view("admin/admin_template",$this->data);
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
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_address');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
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
		$this->User_Model->getUserLocations($user_id, $search, $offset, $length);
		$delete_link = "<a href='javascript:void(0);' onclick='askDelete(\"$1\")' title='<b> Delete</b>' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/location/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i>".$this->lang->line('delete_itm')."</a>";
		$isDefault = "<a href='javascript:void(0);' onclick='isDefault(\"$1\")' title='<b> isDefault</b>' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> Default Address</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
		<ul class="dropdown-menu except-prod pull-right" role="menu">
			<li><a href="' . site_url('seller/location/update/$1') . '"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
		$action .= '<li class="divider"></li>
				<li>' . $delete_link . '</li>
				<li class="divider"></li>
				<li>' . $isDefault . '</li>
			</ul>
		</div></div>';
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