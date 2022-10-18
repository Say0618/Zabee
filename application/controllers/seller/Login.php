<?php 
class Login extends SecureAccess 
{	
	public $isLogin = TRUE;
	function __construct()
	{
		parent::__construct();		
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model("admin/User_Model");
	}

	public function index(){	
		$incorrect = "no";
		$this->data['title'] = "Seller Login";
		if($this->userData != FALSE){
			$this->user_login();
		}
		else{
			$this->data['incorrect'] = $incorrect;
			$this->load->view('admin/login', $this->data);
		}
	}	
	public function user_login(){
		if($this->userData != FALSE){
			$checkUserStore = $this->User_Model->checkUserStore($this->userData[0]['userid']);
			if(isset($checkUserStore) && $checkUserStore["result"]->store_logo != ""){
			$this->session->set_userdata('store_pic', $checkUserStore["result"]->store_logo);}
			$_SESSION['store_status'] = $checkUserStore['result']->is_approve;
			if($this->userData[0]['store_id'] ==""){
				redirect(base_url()."seller/dashboard/store","refresh");
			}else{
				redirect(base_url()."seller/dashboard","refresh");
			}		
		}
		$this->backtologin();
	}	

	public function incorrect(){	
		$incorrect = "yes";
		$this->data['title'] = "Seller Login";
		if($this->userData != FALSE){
			$this->user_login();
		}
		else{
			$this->data['incorrect'] = $incorrect;
			$this->load->view('admin/login',$this->data);
		}
	}	
}
?>