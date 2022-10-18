<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * function that generate the action buttons edit, delete
 * This is just showing the idea you can use it in different view or whatever fits your needs
 */
	if ( ! function_exists('urlClean'))
	{
		function urlClean($str) {
			$str = @trim($str);
			$str = preg_replace('/[^a-zA-Z0-9_ \-]/s', '', $str);
			return $str;
		}
	}
	if ( ! function_exists('clean'))
	{
		function clean($str) {
			$str = @trim($str);
			$str = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $str);
			return $str;
		}
	}
	if ( ! function_exists('linktag'))
	{
		function linktag($address) {
			echo '<link rel="stylesheet" type="text/css" href="'.base_url($address).'" media="all" />';
		}
	}
	if ( ! function_exists('web_title'))
	{
		function web_title($title = array(), $dir, $web_name)
		{
			$web_title = '';
			foreach($title as $item){
				$web_title .= $item.' | ';
			}
			if($dir != '')
				$web_title .= $dir.' | ';
			$web_title .= $web_name;
			echo $web_title;
		}
	}
	if ( ! function_exists('meta_title'))
	{
		function meta_title($title = array())
		{
			$meta_title = '';
			foreach($title as $item){
				$meta_title .= $item.' | ';
			}
			$meta_title .= 'G Sports';
			echo $meta_title;
		}
	}
	if ( ! function_exists('meta_description'))
	{
		function meta_description($description = array())
		{
			$meta_description = '';
			foreach($description as $item){
				$meta_description .= $item.' ';
			}
			$meta_description .= 'G Sports';
			echo $meta_description;
		}
	}
	if ( ! function_exists('meta_keyword'))
	{
		function meta_keyword($keyword = array())
		{
			$meta_keyword = '';
			foreach($keyword as $item){
				$meta_keyword .= $item.', ';
			}
			$meta_keyword .= 'G Sports';
			echo $meta_keyword;
		}
	}
	if ( ! function_exists('snippetwop'))
	{
		function snippetwop($text,$length,$tail = "")
		{
			$text = trim($text);
			$txtl = strlen($text);
			if($txtl > $length)
			{
				for($i=1;$text[$length-$i]!=" ";$i++) {
					if($i == $length) {
						return substr($text,0,$length) . $tail;
					}
				}
				for(;$text[$length-$i]=="," || $text[$length-$i]=="." || $text[$length-$i]==" ";$i++) {;}
				$text = substr($text,0,$length-$i+1) . $tail;
			}
			return $text;
		}
	}
	if ( ! function_exists('userinfo'))
	{
		function userinfo($data, $key)
		{
			$user = $data;
			return $user[$key];
		}
	}
	if ( ! function_exists('getAuthCode'))
	{
		function getAuthCode()
		{
			$auth_code = '';
			for ($i=0; $i<6; $i++){
				$d=rand(1,30)%2;
				$auth_code .= $d ? chr(rand(65,90)) : chr(rand(48,57));
			}
			return $auth_code;
		}
	}
	if ( ! function_exists('alphaID'))
	{
		function alphaID($in, $to_num = false, $pad_up = false, $passKey = null){
			$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			if ($passKey !== null) {
				// Although this function's purpose is to just make the
				// ID short - and not so much secure,
				// with this patch by Simon Franz (http://blog.snaky.org/)
				// you can optionally supply a password to make it harder
				// to calculate the corresponding numeric ID
				for ($n = 0; $n<strlen($index); $n++) {
					$i[] = substr( $index,$n ,1);
				}
				$passhash = hash('sha256',$passKey);
				$passhash = (strlen($passhash) < strlen($index))
					? hash('sha512',$passKey)
					: $passhash;

				for ($n=0; $n < strlen($index); $n++) {
					$p[] =  substr($passhash, $n ,1);
				}
				array_multisort($p,  SORT_DESC, $i);
				$index = implode($i);
			}
			$base  = strlen($index);
			if ($to_num) {
				// Digital number  <<--  alphabet letter code
				$in  = strrev($in);
				$out = 0;
				$len = strlen($in) - 1;
				for ($t = 0; $t <= $len; $t++) {
					$bcpow = bcpow($base, $len - $t);
					$out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
				}
				if (is_numeric($pad_up)) {
					$pad_up--;
					if ($pad_up > 0) {
						$out -= pow($base, $pad_up);
					}
				}
				$out = sprintf('%F', $out);
				$out = substr($out, 0, strpos($out, '.'));
			} else {
				// Digital number  -->>  alphabet letter code
				if (is_numeric($pad_up)) {
					$pad_up--;
					if ($pad_up > 0) {
						$in += pow($base, $pad_up);
					}
				}
				$out = "";
				for ($t = floor(log($in, $base)); $t >= 0; $t--) {
					$bcp = bcpow($base, $t);
					$a   = floor($in / $bcp) % $base;
					$out = $out . substr($index, $a, 1);
					$in  = $in - ($a * $bcp);
				}
				$out = strrev($out); // reverse
			}
			return $out;
		}
	}
	if ( ! function_exists('makeURL'))
	{
		function makeURL($string) {
		   $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
		   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
		}
	}
	if ( ! function_exists('clean_api'))
	{
		function clean_api($val)
		{
			$val = preg_replace('/[^(\x20-\x7F)]*/','', $val);
			return $val;
		}
	}
	if ( ! function_exists('validiate'))
	{
		function validiate($value)
		{
			if($value == ''){
				return false;
			} else {
				return true;
			}
		}
	}
	if ( ! function_exists('create_guid'))
	{
		function create_guid($namespace = '') {
			static $guid = '';
			$uid = uniqid("", true);
			$data = $namespace;
			$data .= $_SERVER['REQUEST_TIME'];
			$data .= $_SERVER['HTTP_USER_AGENT'];
			//$data .= $_SERVER['LOCAL_ADDR'];
			//$data .= $_SERVER['LOCAL_PORT'];
			$data .= $_SERVER['REMOTE_ADDR'];
			$data .= $_SERVER['REMOTE_PORT'];
			$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
			$guid = '' .  
					substr($hash,  0,  8) .
					'-' .
					substr($hash,  8,  4) .
					'-' .
					substr($hash, 12,  4) .
					'-' .
					substr($hash, 16,  4) .
					'-' .
					substr($hash, 20, 12) .
					'';
			return $guid;
		}
	}
	if ( ! function_exists('objToArr'))
	{
		function objToArr($obj)
		{
			$array = array();
			foreach($obj as $key => $value){
				$array[$key] = $value;
			}
			return $array;
		}
	}
	if ( ! function_exists('getIndexByID'))
	{
		function getIndexByID($obj, $id)
		{
			for($t = 0;$t<count($obj);$t++){
				if($obj[$t]['id'] == $id)
					return $t;
			}
			return $obj;
		}
	}
	if ( ! function_exists('encodeProductID'))
	{
		function encodeProductID($product_name, $product_id)
		{
			$obj = urlencode(base64_encode($product_name.'_'.$product_id));
			return $obj;
		}
	}
	if ( ! function_exists('decodeProductID'))
	{
		function decodeProductID($product_id)
		{
			$obj = explode('_',base64_decode(urldecode($product_id)));
			return $obj;
		}
	}
	
	if ( ! function_exists('addunderscores'))
	{
		function addunderscores($str)
		{
			return str_replace(" ","_",$str);	
		}
	}
	if ( ! function_exists('getSecureId'))
	{
		function getSecureId($id)
		{
			$index = str_replace(' ','_',strtolower(urldecode(base64_decode($id))));
			$id = explode('_',urldecode(base64_decode($id)));
			$count = count($id);
			$id = $id[$count-1];
			return $id;
		}
	}
	if ( ! function_exists('removeunderscores'))
	{
		function removeunderscores($str)
		{
			return str_replace("_"," ",$str);	
		}
	}
	if ( ! function_exists('website_img_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function website_img_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['website_img_path'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('product_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function product_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['product_path'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('buyerprofile_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function buyerprofile_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['buyerprofile_path'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('categories_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function categories_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['categories_path'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('profile_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function profile_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['profile_path'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('store_logo_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function store_logo_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['store_logo_path'].$uri;
		}
	}
if ( ! function_exists('store_cover_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function store_cover_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['store_cover_path'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('media_url'))
	{
		/**
		 * Website Image Url URL
		 */
		function media_url($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['media_url'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('image_url'))
	{
		/**
		 * Website Image Url URL
		 */
		function image_url($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['image_url'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('assets_url'))
	{
		/**
		 * Website Image Url URL
		 */
		function assets_url($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['assets_url'].$uri;
		}
	}
// ------------------------------------------------------------------------
if ( ! function_exists('subcategories_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function subcategories_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['subcategories_path'].$uri;
		}
	}

	// ------------------------------------------------------------------------
if ( ! function_exists('getCountryNameByKeyValue'))
	{
		/**
		 * Website Image Url URL
		 */
		function getCountryNameByKeyValue($key="", $value="", $select='', $singleRow =false,$table="tbl_country")
		{
			$CI =& get_instance();
			$country = $CI->Utilz_Model->countries($key, $value,$select,$singleRow,$table);
			if($country)
				if($table == "tbl_country" && $select != "iso"){
					return $country->nicename;
				}else{
					return $country->$select;
				}
			return '-';
		}
	}
	if ( ! function_exists('ccMasking'))
	{
		function ccMasking($number, $maskingCharacter = 'X') {
			return substr($number, 0, 4) . str_repeat($maskingCharacter, strlen($number) - 8) . substr($number, -4);
		}
	}
	if ( ! function_exists('getParentMenu'))
	{
		/**
		 * Get Parent Menu
		 */
		function getParentMenu($parent_id)
		{
			$CI =& get_instance();
			$get_menu = $CI->Utilz_Model->getMenu($parent_id);
			return $get_menu;
		}
	}
	if ( ! function_exists('getChildMenu'))
	{
		/**
		 * Get Parent Menu
		 */
		function getChildMenu($parent_id,$html="")
		{
			$CI =& get_instance();
			$get_menu = $CI->Utilz_Model->getMenu($parent_id);
			foreach($get_menu as $menu){
				if($menu->has_children == 1){
				$html .= '<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink'.$menu->menu_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$menu->menu_name.'</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink'.$menu->menu_id.'">
								<a class="dropdown-item" href="#">'.$menu->menu_name.'</a>
							</div>
						</li>';
				}else{
					$html .='<a class="dropdown-item" href="#">'.$menu->menu_name.'</a>';
				}
			}
			return $html;
		}
	}
	//-----------------------------------------------------
	if ( ! function_exists('product_thumb_path'))
	{
		/**
		 * Website Image Url URL
		 */
		function product_thumb_path($uri="")
		{
			$CI =& get_instance();
			return $CI->config->config['product_thumb_path'].$uri;
		}
	}
	if ( ! function_exists('discount_forumula'))
	{
		/**
		 * Website Image Url URL
		 */
		function discount_forumula($original="", $discount="", $type="", $from ="", $to = "")
		{
			$discounted = false;
			$today = date("Y-m-d");
			$expireTo = $to; 
			$expireFrom = $from; 
			$today_time = strtotime($today);
			$expire_time_to = strtotime($expireTo);
			$expire_time_from = strtotime($expireFrom);
			if (($today_time >= $expire_time_from) && ($today_time <= $expire_time_to)) {
				if($type == "percent"){
					$v = ($original * $discount)/100;
					$discounted =$original - $v;
					if($discounted < 0){ 
						$discounted = $original; 
					}
				} else if($type == "fixed"){
					if($original > $discount){
						$discounted = ($original - $discount);
					}
				} else {
					$discounted = $original;
				} 
			}
			return $discounted;
		}
	}
	if ( ! function_exists('saveNoti'))
	{
		function saveNoti($msg, $type, $user_id, $to, $u_type, $Utilz_Model, $link = ""){
			$CI = get_instance();
			$CI->load->model('Utilz_Model');
			$userid = $to;
			$created_by = $user_id;
			$usertype = $u_type;
			$notification_type = $type;
			$message = $msg;
			$send = "";
			if($link != ""){
				$send = $CI->Utilz_Model->saveNotification($userid, $created_by, $usertype, $notification_type, $message, $link);
			} else {
				$send = $CI->Utilz_Model->saveNotification($userid, $created_by, $usertype, $notification_type, $message);
			}
			return $send;
		}
	}
	if ( ! function_exists('daysAgo'))
	{
		function daysAgo($dateinDB){
			$date1 = date_create(date("Y-m-d"));
			$date2 = $dateinDB;
			$date2 = explode(" ", $date2);
			$date2 = date_create($date2[0]);
			$diff=date_diff($date2,$date1);
			if( $diff->m == 0 && $diff->days != 0){
				echo $diff->days." days ago";
			} else if($diff->m != 0){
				echo $diff->m." months ago";
			} else if($diff->m == 0 && $diff->days == 0){
				$time1 = date("Y-m-d h:i:s");
				$time1 = explode(" ", $time1);
				$time1 = $time1[1];
				$time2 = explode(" ", $dateinDB);
				$time2 = $time2[1];
				$t1 = explode(":", $time1);
				$t2 = explode(":", $time2);
				if($t1[0] == $t2[0] && $t1[1] == $t2[1]){
					echo $t1[2] - $t2[2]." secs ago";
				} else if($t1[0] == $t2[0] && $t1[1] != $t2[1]){
					echo $t1[1] - $t2[1]." mins ago";
				} else if($t1[0] != $t2[0]){	
					echo $t1[0] - $t2[0]." hours ago";
				}
			}
		}
	}
	if ( ! function_exists('getTax'))
	{
		function getTaxs($zip, $total){
			$CI = get_instance();
			$amount = $CI->getTax($zip, $total);
			return $amount;
		}
	}
	if ( ! function_exists('formatDate'))
	{
		function formatDate($date){
			$date = Date('l jS F, Y', strtotime($date));
			return $date;
		}
	}
	if ( ! function_exists('setUtcDate'))
	{
		function setUtcDate(){
			$time = gmdate("Y-m-d\TH:i:s");
			$time = str_replace("T"," ",$time);
			return $time;
		}
	}
	if ( ! function_exists('getUtcDate'))
	{
		function getUtcDate($date){
			$time = strtotime($date.' UTC');
			$dateInLocal = date("Y-m-d g:i A", $time);
			return $dateInLocal;
		}
	}
	if (!function_exists('validation_errors_array')) {

		function validation_errors_array($prefix = '', $suffix = '') {
		  if (FALSE === ($OBJ = & _get_validation_object())) {
			return '';
		  }
		  return $OBJ->error_array($prefix, $suffix);
		}
	}
	
	if (!function_exists('validation_errors_api')) {

		function validation_errors_api($prefix = '', $suffix = '') {
		  if (FALSE === ($OBJ = & _get_validation_object())) {
			return '';
		  }

		  $errors =  $OBJ->error_array($prefix, $suffix);
		  $return = array();
		  foreach($errors as $item){
			  array_push($return, $item);
		  }
		  return $return;
		}
	}
	if ( ! function_exists('formatDateTime'))
	{
		function formatDateTime($date, $time){
			if($time == false){
				$date = Date('F j, Y', strtotime($date));
			}else{
				$date = date('F j, Y g:i A', strtotime($date." UTC"));
			}
			return $date;
		}
	}
	if (!function_exists('curl_file_create')) {
		function curl_file_create($filename, $mimetype = '', $postname = '') {
			return "@$filename;filename="
			. ($postname ?: basename($filename))
			. ($mimetype ? ";type=$mimetype" : '');
		}
	}
// ------------------------------------------------------------------------
/* End of file datatables_helper.php */
/* Location: ./application/helpers/datatables_helper.php */
