<?php
class SecureAccess extends CI_Controller
{
	protected $userData = "";
	protected $cur_date_time = "";
	public $dateTimeFormat = "d-m-Y H:i:s a";
	public $dateFormat = "d-m-Y";
	public $scheckUserStore = "";
	public $isScript = false;
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');			
		$this->load->library('form_validation');
		$this->load->helper(array('form', 'url'));
		$this->load->helper('date');		
		$this->load->model("admin/Secure_Model");		
		$this->cur_date_time = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));	//	echo $this->cur_date_time;die;
		$this->userData = $this->checkLogin();
		$id = "";
		if(isset($this->userData[0]["userid"])){
			$id = $this->userData[0]["userid"];
		}else if(isset($this->userData[0]["admin_id"])){
			$id = $this->userData[0]["admin_id"];
		}
		if($this->userData[0]['user_type'] == 1){
			$this->checkUserStore = true;
		}else{
			$this->checkUserStore = $this->checkStore($id);
		}
	}	

	protected function checkLogin()
	{
		$userData = $this->checkconditions();	
		if($userData){
			return $userData;
		}
		if(isset($this->isLogin) && $this->isLogin){
			return FALSE;
		}
		$this->backtologin();
	}
	
	/**
	* This function contains any operation when login fails
	*/
	protected function backtologin()
	{
		$this->session->sess_destroy();
		redirect(base_url()."seller/login","refresh");
	}
	
	protected function checkconditions()
	{
		$getData = $this->input->cookie("ecomm_adminData");		
		if($getData){
			
			$this->session->set_userdata(unserialize(base64_decode($getData)));
		}		
		if($this->session->userdata("userid") || $this->session->userdata("admin_id") || isset($_POST['userid']) || isset($_POST['username']) || isset($_POST['password'])){
			$userData = (isset($_POST['userid'])  || isset($_POST['username']) || isset($_POST['password']))? $_POST : $this->session->userdata;
			if(isset($userData['userid'])){
					
				$userid = $userData['userid'];
			}else{
				$userid = "";
			}
			if(isset($userData['username']) && isset($userData['password'])){
				
				$username = $userData['username'];
				$password = $userData['password'];
			}else{
				$username = "";
				$password = "";
			}
			$remember = isset($userData['remember'])?$userData['remember'] : "off";
			if($username && $password){
				$checkDB = $this->Secure_Model->checklogindetails("",$username,$password);			
			}
			if($userid){
				$checkDB = $this->Secure_Model->checklogindetails($userid,"","");			
			}
			if($checkDB != FALSE){
				if(isset($_POST['userid']) || isset($_POST['username']) || isset($_POST['password'])){
					$_POST["remember"] = $remember;
					if($remember == "off")
						$this->session->sess_expiration = "7200";					
					else { 
						$cookie = array(
						    'name'   => 'adminData',
						    'value'  => base64_encode(serialize(array_merge($checkDB[0],$_POST))),
						    'expire' => '108000',
						    'domain' => '',
						    'path'   => '/',
						    'prefix' => 'ecomm_',
						    'secure' => FALSE
						);
						$this->input->set_cookie($cookie);					
					}
					$this->session->set_userdata($checkDB[0]);
					$this->session->set_userdata($_POST);
				}
				$usertypes = $this->Secure_Model->checkusertype($this->uri->segment_array(),$checkDB);
				if(!(empty($usertypes)))
				{
					$this->session->set_userdata($usertypes);
					$checkDB[] = $usertypes;
					return $checkDB;
				}
			}			
		}
		return FALSE;
	}

	public function uploadImage($path="./images/uploads/profile/"){
		$config['upload_path']   = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpegs';
		$config['encrypt_name']  = true;
		$profile_image			 = "";
		if(isset($_FILES["profile_image"])){
			$_FILES['userfile']['name'] = $_FILES['profile_image']['name'];
			$_FILES['userfile']['type'] = $_FILES['profile_image']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['profile_image']['tmp_name'];
			$_FILES['userfile']['error'] = $_FILES['profile_image']['error'];
			$_FILES['userfile']['size'] = $_FILES['profile_image']['size'];
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload()){
				$error = array('info' => $this->upload->display_errors());
				$this->session->set_flashdata("error", $this->upload->display_errors());			
			} else {
				$data = array('upload_data' => $this->upload->data());
				$arrImg = array
				(
					"base"		=>"uploads",
					"type"		=>"product",
					"img"		=>$data['upload_data']['file_name'],
					"width"		=>100,
					"height"	=>100
				);
				$img_url = base64_encode(serialize($arrImg));				
				$profile_image['thumbnail'] = $img_url;
				$profile_image['original'] = $data['upload_data']['file_name'];
			}				
		} 
		return $profile_image;
	}

	private function checkStore($id){
		$checkUserStore = $this->Secure_Model->checkUserStore($id);
		return $checkUserStore;
	}	
}
?>