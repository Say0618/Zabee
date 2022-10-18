<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Curl_Request  {

	function __construct() {
		//parent::__construct();
	}

	public function curlGetRequest($path){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$path);
		//curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		$server_output = curl_exec ($ch);
		//echo $path;
		//print_r($data);
		//print_r(json_decode($server_output));die();
		curl_close ($ch);
		//die();
		/*$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);
		echo '<pre>';
		print_r($header);
		print_r($body);
		print_r($data);
		print_r($server_output);
		echo $path;
		die();*/
		return json_decode($server_output);
	}

}
?>