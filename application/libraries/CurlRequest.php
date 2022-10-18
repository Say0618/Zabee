<?php defined('BASEPATH') OR exit('No direct script access allowed');
class CurlRequest {
    
    public function __construct()
    {
		// $CI = & get_instance();
     	include_once APPPATH.'/third_party/curl_request.php';
	}
}
?>