<?php  
class Configuration extends SecureAccess{
	function __construct()
	{
		parent::__construct();	
		// $this->load->model("admin/Offers_Model");	
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
		$this->data['page_name'] 		= 'configuration';
		$this->data['Breadcrumb_name'] 	= 'Configuration';
		$this->data['isScript'] 		= false;
		$this->load->view("admin/admin_template",$this->data);	
	}
}
?>