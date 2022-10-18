<?php 
class Login extends WarehouseAccess 
{	
	public $isLogin = TRUE;
	function __construct()
	{
		parent::__construct();		
		$this->load->helper('url');
		$this->load->library('session');
		$this->lang->load('english', 'english');
	}

	public function index(){	
		$incorrect = "no";
		if($this->userData != FALSE){
			$this->user_login();
		}
		else{
			$this->data['incorrect'] = $incorrect;
			$this->load->view('warehouse/login', $this->data);
		}
	}	

	public function user_login(){
		if($this->userData != FALSE){
			redirect(base_url()."warehouse/dashboard","refresh");
		}
		$this->backToLoginWarehouse();
	}	

	public function incorrect(){	
		$incorrect = "yes";
		if($this->userData != FALSE){
			$this->user_login();
		}
		else{
			$this->data['incorrect'] = $incorrect;
			$this->load->view('warehouse/login',$this->data);
		}
	}	
}
?>