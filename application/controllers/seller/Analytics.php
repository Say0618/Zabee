<?php
class Analytics extends SecureAccess 
{
	public $posteddata = "";
	function __construct() 
	{
		parent::__construct();		
		$this->load->model("admin/Analytics_Model");	
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
		$endDate = date('Y-m-d');
		//$endDate = date('Y-m-d', strtotime('-60 days'));
		$startDate = date('Y-m-d', strtotime('-30 days'));
		$this->data['user_id'] 			= $this->session->userdata('userid');
		$this->data['user_type'] 		= $this->session->userdata['user_type'];
		$this->data['page_name'] 		= 'analytics_home';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('analytics');
		$this->data['startDate'] 		= $startDate;
		$this->data['endDate']	 		= $endDate;
		$this->data['isScript'] 		= true;
		$this->data['chart_data']		= $this->Analytics_Model->getViewsCountByDay($startDate, $endDate, $this->data['user_id'], $this->data['user_type']);
		$this->load->view("admin/admin_template",$this->data);
	}
	
	public function get(){
		if($this->session->userdata['userid']){
			$user_id = $this->session->userdata('userid');
		}
		$isAdmin = ($this->session->userdata['user_type'] == 1)?true:false;
		$postData = $this->input->post();
		//echo '<pre>';print_r($postData);echo '</pre>';die();
		//$postData['']
		$data = $this->Analytics_Model->getProduct($postData, $user_id, $isAdmin);
		echo json_encode($data);
	}
	
	public function getviewsbycount(){
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata['user_type'];
		$endDate = date('Y-m-d H:i:s', strtotime('-7 days'));
		$startDate = date('Y-m-d', strtotime('-120 days'));
		$data = $this->Analytics_Model->getViewsCountByDay($startDate, $endDate, $user_id, $user_type);
		echo '<pre>';print_r($data);echo '</pre>';die();
	}
}
?>
