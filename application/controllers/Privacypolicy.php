<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Privacypolicy extends CI_Controller {
	public $data = array();
	public $isloggedin = FALSE;
	function __construct()
	{
		parent::__construct();
		$this->load->library('Mobile_Detect');	
		$detect = new Mobile_Detect;
		$this->config->set_item('isTablet', "Tablet - ".$detect->isTablet());
		$this->config->set_item('isMobile', "Mobile - ".$detect->isMobile());
	}

	public function index()
	{
		$this->isloggedin = FALSE;
		$this->data['page_name'] 	= 'privacy_policy';
		$this->data['hasStyle'] 	= false;
		$this->data['hasScript'] 	= false;
		$this->data['newsletter'] 	= false;
		$this->data['title'] 		= "Zab.ee Privacy Policy";
		$this->load->view('front/template', $this->data);
 
	}
	
	public function termsandcondition()
	{
		$this->data['page_name'] 	= 'termsandcondition';
		$this->data['hasScript'] 	= false;
		$this->data['newsletter'] 	= false;
		$this->data['title'] 		= "Zab.ee Terms and Conditions";
		$this->load->view('front/template', $this->data);
 
	}
}
?>