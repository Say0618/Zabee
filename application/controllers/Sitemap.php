<?php 
class Sitemap extends CI_Controller{
	var $data = array();
	function __construct(){
		parent::__construct();
	} 

	public function index(){
		$this->data['data'] 			= array();
		$this->data['hasScript'] 		= true;
		$this->data['page_name'] 		= 'confirm_payment';
		$this->load->view('sitemap', $this->data);
	}
}
?> 