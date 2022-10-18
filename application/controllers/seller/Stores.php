<?php
class Stores extends SecureAccess 
{
	public $posteddata = "";
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Store_Model");	
		$this->data = array(
			'page_name' 		=> 'pending_stores',
			'isScript' 			=> true,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
		if($this->session->userdata('userid')){
			$this->data['user_id'] = $this->session->userdata('userid');
		}else{
			$this->data['user_id'] = $this->session->userdata('admin_id');	
		}
	}

	public function index(){
		$this->data['page_name'] 		= 'pending_stores';
		$this->data['Breadcrumb_name'] 	= "Pending Stores";
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
    }

    public function get_stores($status){
		$request = $this->input->post();
        $data = $this->Store_Model->get_stores($status, $request);
        echo json_encode($data);
	}
	
	public function updateStore(){
		$id = $this->input->post('s_id'); 
		$seller_id = $this->input->post('seller_id');
		$status = $this->input->post('status');
		$result = $this->Store_Model->Request($id, $seller_id, $status);
		echo json_encode($result);
	}

	public function update_store_status(){
		extract($this->input->post());
		$result = $this->Store_Model->updateStoreStatus($s_id, $value);
		echo json_encode($result);
	}
}
