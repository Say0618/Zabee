<?php
class Logout extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url','cookie'));	
		$this->load->library('session');		
	}
	function index()
	{
		$this->isLogin = FALSE;
		$this->session->sess_destroy();
		delete_cookie("ecomm_adminData");
		redirect(base_url()."seller/login","refresh");
	}
}
?>