<?php
class Subcription_Model extends CI_model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
		$this->load->library('session');
	}
	
	function unsubscribe($email)
	{
		$this->db->where("email",$email);
		if($this->db->delete(DBPREFIX."_subscriptions"))
		return TRUE;
		return FALSE;
	}
}
?>