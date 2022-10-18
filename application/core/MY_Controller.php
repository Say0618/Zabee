<?php defined('BASEPATH') OR exit('See you next time.');

class MY_Controller extends CI_Controller
{
	var $data;
	public function __construct()
    {
		parent::__construct();
		if($this->config->item('maintenance_mode') == 3 || $this->config->item('maintenance_mode') == '3') {
			$content = $this->load->view('maintenance_view', '', true);
			echo $content;
			die();
		}
    }

	public function getTax($zip, $amount){
		/*echo "<pre>";
		print_r($zip);
		echo "<hr>";
		print_r($amount);
		die();*/
		$this->data['tax_api_key'] = $this->config->item('tax_api_key');
		$this->data['tax_api_url'] = $this->config->item('tax_api_url');
		if($zip != '' && $amount > 0){
			$params = '?key='.$this->data['tax_api_key'].'&postalcode='.$zip;
			$taxResponse = $this->Utilz_Model->curlRequest($params, $this->data['tax_api_url'], false, false);
			//echo "<pre>";print_r($taxResponse);die();
			if(isset($taxResponse->rCode) && $taxResponse->rCode == 100){
				$result = $taxResponse->results;
				if(count($result)>0){
					$amount = $result[0]->taxSales * $amount;
				} else {
					$amount = $this->config->item('vat_tax');
				}
			} else {
				$amount = $this->config->item('vat_tax');
			}
		}
		return $amount;
	}
}
class Secureaccess extends MY_Controller
{
	protected $userData = "";
	protected $cur_date_time = "";
	public $dateTimeFormat = "d-m-Y H:i:s a";
	public $dateFormat = "d-m-Y";
	public $checkUserStore = "";
	public $checkUserWarehouse = "";
	public $checkUserTextNotificaiton="";
	public $isScript = false;
	public $status = "";
	public $notificationCount  = 0;
	public $notifications  = array();
	public $getStoreImage = "";
	function __construct()
	{

		parent::__construct();
		if($this->config->item('maintenance_mode') == 2 || $this->config->item('maintenance_mode') == '2') {
			$content = $this->load->view('maintenance_view', '', true);
			echo $content;
			die();
		}
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper(array('form', 'url'));
		$this->load->helper('date');
		$this->load->helper('cookie');
		$this->load->model("admin/Secure_Model");
		$this->load->model("Product_Model");
		$this->load->model("admin/Sales_Model");
		$this->load->model("Utilz_Model");
		$this->load->model("admin/Requests_Model");
		$this->cur_date_time = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));
		//echo $this->cur_date_time;die;
		$this->userData = $this->checkLogin();
		$this->pendingProductData = $this->getPendingProducts();
		$this->requests = $this->getRequests();
		$this->approvedProducts = $this->getApprovedProducts();
		$this->rejectedProducts = $this->getRejectedProducts();
		$this->pendingOrders	= $this->getPendingOrders();
		$this->acceptedOrders	= $this->getAcceptedOrders();
		$this->rejectedOrders	= $this->getRejectedOrders();
		$this->cancellationOrders = $this->getCancellationRequestOrders();


		$id = "";
		if(isset($this->userData[0]["userid"])){
			$id = $this->userData[0]["userid"];
		}else if(isset($this->userData[0]["admin_id"])){
			$id = $this->userData[0]["admin_id"];
		}
		if(isset($this->userData[0]['user_type']) && $this->userData[0]['user_type'] == 1){
			$this->checkUserStore = true;
			$usertype = 0;
		}else{
			$this->checkUserStore = $this->checkStore($id);
			$usertype = 2;
		}
		if($id){
			$this->checkUserTextNotificaiton = $this->checkTextNotification($id);
			$this->notificationCount = $this->Utilz_Model->getNotifications($id, $usertype, 'new', true);
			$this->notifications = $this->Utilz_Model->getNotiFormatted($id, $usertype, 'new', false, 'seller');
		}
		$this->lang->load('english', 'english');
		//$this->getStoreImage = $this->get_profile_image(store_logo_path($this->session->userdata('store_pic')));
	}
	protected function checkLogin()
	{
		//die("here 2");
		$userData = $this->checkconditions();
		// /echo "<pre>"; print_r($userData);die();
		if($userData)
		{
			return $userData;
		}
		//echo $this->isLogin;die();
		if(isset($this->isLogin) && $this->isLogin)
		{
			return FALSE;
		}
		$this->backtologin();
	}

	/**
	* This function contains any operation when login fails
	*/
	protected function backtologin()
	{
		$status = "incorrect";
		$this->session->sess_destroy();
		redirect(base_url()."seller/login/".$status,"refresh");
	}

	protected function checkconditions(){
		//$getData = $this->input->cookie("ecomm_adminData");
		$getData = $this->input->cookie("ecomm_zabeeData");
		$checkDB = false;
		// echo "<pre>";print_r($this->session);print_r($getData);die();
		if($getData){
			$this->session->set_userdata(unserialize(base64_decode($getData)));
		}
		//echo "<pre>";print_r($this->session);print_r($userData);die();
		if($this->session->userdata("userid") || $this->session->userdata("admin_id") || isset($_POST['userid']) || isset($_POST['username']) || isset($_POST['password'])){
			//print_r($_POST);die();
			$userData = (isset($_POST['userid'])  || isset($_POST['username']) || isset($_POST['password']))? $_POST : $this->session->userdata;
			if(isset($userData['userid'])){

				$userid = $userData['userid'];
			}else{
				$userid = "";
			}
			if(isset($userData['username']) && isset($userData['password'])){

				$username = $userData['username'];
				$password = $userData['password'];
				$firstSalt = explode("@",$username);
				$lastSalt = "zab.ee";
				$hash = $firstSalt[0].$password.$lastSalt;
			}else{
				$username = "";
				$password = "";
			}
			$remember = isset($userData['remember'])?$userData['remember'] : "off";
			//print_r($userData);die();
			if($username && $password){
				$checkDB = $this->Secure_Model->checklogindetails("",$username,sha1($hash));
			}
			if($userid){
				$checkDB = $this->Secure_Model->checklogindetails($userid,"","");
			}
			if($checkDB != FALSE)
			{
				if(isset($_POST['userid']) || isset($_POST['username']) || isset($_POST['password']))
				{
					$_POST["remember"] = $remember;
					//session expires after 30 days if remember me is selected 30 * 3600 = 108000
					if($remember == "off")$this->session->sess_expiration = "7200";
					else
					{
						$cookie = array(
						    'name'   => 'zabeeData',
						    'value'  => base64_encode(serialize($checkDB[0])),
						    'expire' => '108000',
						    'domain' => '',
						    'path'   => '/',
						    'prefix' => 'ecomm_',
						    'secure' => FALSE
						);
						$this->input->set_cookie($cookie);
					}
					$this->session->set_userdata($checkDB[0]);
					//$this->session->set_userdata($_POST);
					//echo "<pre>";print_r($this->session);die();
				/*	$this->load->model("admin/Usertype_Model");
					$usertypes["user_types"] = $this->Usertype_Model->getadminUsers();
					$this->session->set_userdata($usertypes); */
				}
				$usertypes = $this->Secure_Model->checkusertype($this->uri->segment_array(),$checkDB);
				//echo "<pre>";print_r($usertypes);
				//print_r($usertypes);echo "<hr>";print_r($checkDB);die();
				if(!(empty($usertypes)))
				{
					$this->session->set_userdata($usertypes);
					$checkDB[] = $usertypes;
					//echo "<pre>";print_r($usertypes);echo "<hr>";print_r($checkDB);die();
					return $checkDB;
				}
			}
		}
		return FALSE;
	}

	private function checkStore($id){
		$checkUserStore = $this->Secure_Model->checkUserStore($id);
		return $checkUserStore;
	}
	public function checkTextNotification($id){
		$checkMsgNotification = $this->Secure_Model->checkMsgNotification($id);
		$i = 0;
		if($checkMsgNotification['rows'] > 0){
			foreach($checkMsgNotification['result'] as $bl){
				$checkMsgNotification['result'][$i]->product_link="";
				$checkMsgNotification['result'][$i]->product_id="";
				$checkMsgNotification['result'][$i]->product_name="";
				if($bl->product_variant_id){
					if($bl->item_type == "product"){
						$product = $this->Secure_Model->checkProductDetails('','',$bl->product_variant_id);
						if($product['productDataRows'] > 0){
							$checkMsgNotification['result'][$i]->product_link=$product['productData']->product_name."-".$product['productData']->brand_name."-".$product['productData']->category_name;
							$checkMsgNotification['result'][$i]->product_id=$product['productData']->product_id;
							$checkMsgNotification['result'][$i]->product_name=$product['productData']->product_name;
						}
					}
				}
				$i++;
			}
		}
		return $checkMsgNotification;
	}

	protected function getPendingProducts(){
		$user_id = $this->session->userdata("userid");
		$user_type = $this->session->userdata("user_type");
		//  echo"<pre>";print_r($user_id);die();
			if($user_type == 1){
				$where = array('is_active' => "0",'created_by'=>'seller', 'is_declined' => '0');
				// print_r($where);
				$pend_prod_number = $this->Product_Model->getNumberofPendingproducts($where);
						//  echo"<pre>";print_r($pend_prod_number);die();

			}
			if($user_type != 1){
				$where = array('is_active' => "0", 'created_id' => $user_id, 'is_declined' => '0');
				$pend_prod_number = $this->Product_Model->getNumberofPendingproducts($where);
						// print_r($pend_prod_number);die();

			}
			// print_r($where);die();
		return $pend_prod_number;
		// print_r($pend_prod_number);die();
	}
	protected function getRequests(){
		$user_id = $this->session->userdata("userid");
		$pend_req_number = 0;
		if($user_id == 1){
			$pend_req_number = $this->Requests_Model->getNumberofRequests();
		}
		return $pend_req_number;
	}
	protected function getApprovedProducts(){
		$user_id = $this->session->userdata("userid");
		$approve_number = 0;
		if($user_id != 1){
			$approve_number = $this->Product_Model->getNumberofApprovedProducts($user_id);
		}
		return $approve_number;
	}
	protected function getRejectedProducts(){
		$user_id = $this->session->userdata("userid");
		$reject_number = 0;
		if($user_id != 1){
			$reject_number = $this->Product_Model->getNumberofRejectedProducts($user_id);
		}
		return $reject_number;
	}
	protected function getPendingOrders(){
		$user_id = $this->session->userdata("userid");
		$pending_number = 0;
		if($user_id != 1){
			$pending_number = $this->Sales_Model->getNumberofPendingOrders($user_id);
		}
		return $pending_number;
	}
	protected function getAcceptedOrders(){
		$user_id = $this->session->userdata("userid");
		$accepted_number = 0;
		if($user_id != 1){
			$accepted_number = $this->Sales_Model->getNumberofAcceptedOrders($user_id);
		}
		return $accepted_number;
	}
	protected function getRejectedOrders(){
		$user_id = $this->session->userdata("userid");
		$rejected_number = 0;
		if($user_id != 1){
			$rejected_number = $this->Sales_Model->getNumberofRejectedOrders($user_id);
		}
		return $rejected_number;
	}
	protected function getCancellationRequestOrders(){
		$user_id = $this->session->userdata("userid");
		$cancellation_number = 0;
		if($user_id != 1){
			$cancellation_number = $this->Sales_Model->getNumberofCancellationRequestOrders($user_id);
		}
		return $cancellation_number;
	}
	public function uploadImage($path="uploads/profile/", $files, $type = 'product', $user_id = ""){
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		if($type == 'product' || $type == 'banner' || $type == 'categories' || $type == 'brands'){
			$config['encrypt_name'] = true;
		}else{
			$config['file_name'] = 'seller_'.$user_id.'.PNG';
			$config['overwrite'] = true;
		}
		//$name = "test";
		$profile_image = array();
		//echo "<pre>";print_r($files); die();
		if(isset($files)){
				$_FILES['userfile']['name'] = $files['name'];
				$_FILES['userfile']['type'] = $files['type'];
				$_FILES['userfile']['tmp_name'] = $files['tmp_name'];
				$_FILES['userfile']['error'] = $files['error'];
				$_FILES['userfile']['size'] = $files['size'];
				$this->load->library('upload', $config);

				if ( ! $this->upload->do_upload())
				{
					$error = array('info' => $this->upload->display_errors());
					$this->session->set_flashdata("error", $this->upload->display_errors());
				} else {
					$data = array('upload_data' => $this->upload->data());
					$img_name = ($type == 'product'  || $type == 'banner'  || $type == 'categories' || $type == 'brands')?$data['upload_data']['file_name']:'seller_'.$user_id.'.PNG'/*$data['upload_data']['file_ext']*/;
					$arrImg = array
					(
						"base"=>"uploads",
						"type"=>$type,
						"img"=>$img_name,
						"width"=>100,
						"height"=>100
					);
					$img_url = base64_encode(serialize($arrImg));

					$profile_image['thumbnail'] = $img_url;
					$profile_image['original'] = $img_name;
					//echo "<pre>";print_r($img_url); die();
				}

		}
		//var_dump($profile_image); die();
		return $profile_image;

	}
	public function do_upload_directly($path="uploads/profile/", $files, $type = 'categories', $user_id = ""){
		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		if($type == 'banner' || $type == 'categories' || $type == 'brands'){
			$config['encrypt_name'] = true;
		}else{
			$config['file_name'] = 'seller_'.$user_id.'.PNG';
			$config['overwrite'] = true;
		}
		$profile_image = array();
		if(isset($files)){
			$_FILES['userfile']['name'] = $files['name'];
			$_FILES['userfile']['type'] = $files['type'];
			$_FILES['userfile']['tmp_name'] = $files['tmp_name'];
			$_FILES['userfile']['error'] = $files['error'];
			$_FILES['userfile']['size'] = $files['size'];
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload()){
				$error = array('info' => $this->upload->display_errors());
				$this->session->set_flashdata("error", $this->upload->display_errors());
			} else {
				$data = array('upload_data' => $this->upload->data());
				$img_name = ($type == 'product'  || $type == 'banner'  || $type == 'categories' || $type == 'brands')?$data['upload_data']['file_name']:'seller_'.$user_id.'.PNG'/*$data['upload_data']['file_ext']*/;
				$arrImg = array(
					"base"=>"uploads",
					"type"=>$type,
					"img"=>$img_name,
					"width"=>100,
					"height"=>100
				);
				$img_url = base64_encode(serialize($arrImg));
				$profile_image['thumbnail'] = $img_url;
				$profile_image['original'] = $img_name;
			}
		}
		//print_r($profile_image);die();
		return $profile_image;

	}
	function uploadAjax(){
		// print_r($_POST);
		// print_r($_FILES);die();
		$outData = $this->upload(); // a function to upload the bootstrap-fileinput files
		echo json_encode($outData); // return json data
	}
	function upload() {
		$this->load->library('upload');
		$this->load->library('image_lib');
		$preview = $errors = [];
		$config = array();
		$type = $_POST['type'];

		$fileBlob = 'fileBlob';                      // the parameter name that stores the file blob
		if (isset($_FILES[$fileBlob]) && isset($_POST['uploadToken'])) {

			$date = date('Y-m-d H:i:s');
			$date_utc = gmdate("Y-m-d\TH:i:s");
			$date_utc = str_replace("T"," ",$date_utc);
			$file = $_FILES[$fileBlob];
			$id = $_POST['id'];
			$column = $_POST['column'];
			$user_id = $_POST['user_id'];

			$fileName = $_POST['fileName'];          // you receive the file name as a separate post data
			$fileSize = $_POST['fileSize'];          // you receive the file size as a separate post data
			$fileId = $_POST['fileId'];              // you receive the file identifier as a separate post data
			$index =  $_POST['chunkIndex'];          // the current file chunk index

			if($type != "user"){
				$config['encrypt_name'] = true;
				$config['overwrite'] = FALSE;
			}else{
				$config['file_name'] = 'seller_'.$user_id.'.PNG';
				$config['overwrite'] = true;
			}
			$config['upload_path'] = $type;
			$config['upload_thumbnail_path'] = $type."/thumbs";
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['quality'] = "100%";
			$config['remove_spaces'] = TRUE;

			$params['file'] = curl_file_create($_FILES[$fileBlob]['tmp_name'], $_FILES[$fileBlob]['type'], $_FILES[$fileBlob]['name']);
			$params['image_type'] = $type;
			$params['filesize'] = $_FILES[$fileBlob]['size'];
			$params['config'] = json_encode($config);
			// echo '<pre>';print_r($params);die();
			$upload_server = $this->config->item('media_url').'/file/upload_media';
			$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
			// print_r($file);die();
			if($file->status == 1){
				$image_name =  $file->images->original->filename;
				$targetUrl = $file->images->original->filepath.$image_name;
				//if($type == "product"){
				$thumbnail = $file->images->thumbnail->filename;
				//}
				$tn = $_POST['tn'];
				$table_name = DBPREFIX."_".$tn;
				if($tn == "pm"){
					$table_name = DBPREFIX."_product_media";
					$imageData = array('thumbnail'=>$thumbnail,'condition_id'=>$_POST['condition_id'], 'iv_link'=>$image_name, 'is_local'=>"1",'is_image'=>"1","is_active"=>"1");
					if($id !=""){
						$positionWhere = array("product_id"=>$id,"condition_id"=>$_POST['condition_id']);
						if($_POST['sp_id'] !=""){
							$positionWhere['sp_id'] = $_POST['sp_id'];
							$imageData["sp_id"] = $_POST['sp_id'];
						}else if($_POST['sp_id'] == "" && $_POST['condition_id'] !=1){
							$positionWhere['dummy_id'] = $_POST['dummy_id'];
							$imageData['dummy_id'] = $_POST['dummy_id'];
						}
						$imageData['product_id'] = $id;
					}else{
						$positionWhere = array("dummy_id"=>$_POST['dummy_id']);
						$imageData['dummy_id'] = $_POST['dummy_id'];
					}

				}else if($tn == "brands"){
					$imageData = array("brand_image"=>$image_name);
				}else if($tn == "banners"){
					$imageData = array("banner_image"=>$image_name);
				}else if($tn == "categories"){
					$imageData = array("category_image"=>$image_name);
				}else if($tn == "special_offers"){
					$imageData = array("offer_image"=>$image_name);
				}
				if($tn == "pm"){
					$position = $this->Product_Model->getData($table_name,"MAX(position) as position",$positionWhere);
					if($position[0]->position !=""){
						$position = $position[0]->position+1;
					}else{
						$position = 0;
						$imageData['is_cover'] = "1";
					}
					$imageData['position'] = $position;
					$imageData['created_date'] = $date;
					$this->db->insert($table_name, $imageData);
					$image_key =  $this->db->insert_id();
				}else if($tn == "special_offers"){
					if($id == ""){
						$imageData['created'] = $date;
						$this->db->insert($table_name, $imageData);
						$image_key =  $this->db->insert_id();
					}else{
						$imageData['updated'] = $date;
						$where = array($column=>$id);
						$this->db->where($where);
						$this->db->update($table_name, $imageData);
						$image_key = $id;
					}
				}else if($tn == "banners"){
					if($id == ""){
						$imageData['created'] = $date;
						$this->db->insert($table_name, $imageData);
						$image_key =  $this->db->insert_id();
					}else{
						$imageData['updated'] = $date;
						$where = array($column=>$id);
						$this->db->where($where);
						$this->db->update($table_name, $imageData);
						$image_key = $id;
					}
				}else{
					if($id == ""){
						$imageData['created_date'] = $date;
						$this->db->insert($table_name, $imageData);
						$image_key =  $this->db->insert_id();
					}else{
						$imageData['updated_date'] = $date;
						$where = array($column=>$id);
						$this->db->where($where);
						$this->db->update($table_name, $imageData);
						$image_key = $id;
					}
				}
				return [
					'chunkIndex' => $index,         // the chunk index processed
					'initialPreview' => $targetUrl, // the thumbnail preview data (e.g. image)
					'initialPreviewConfig' => [
						[
							'type' => 'image',      // check previewTypes (set it to 'other' if you want no content preview)
							'caption' => $fileName, // caption
							'key' => $image_key,       // keys for deleting/reorganizing preview
							'fileId' => $fileId,    // file identifier
							'size' => $fileSize,    // file size
							'zoomData' => $targetUrl, // separate larger zoom data,
							'extra' => ['name'=>$image_name,"thumb"=>$thumbnail]
						]
					],
					'append' => true
				];
			} else {
				return [
					'error' => $file->message
				];
			}
		}
		return [
			'error' => 'No file found'
		];
	}
	function imagePosition(){
		$data = $_POST['data'];
		$table = DBPREFIX."_product_media";
		foreach($data as $d){
			if($d['index'] == 0){
				$is_cover = "1";
			}else{
				$is_cover = "0";
			}
			$this->db->where("media_id",$d['media_id']);
			$this->db->update($table,array("position"=>$d['index'],"is_cover"=>$is_cover));
		}
		echo json_encode(array("status"=>true));
	}
	function block_user($seller_id,$active){
		if($active == "2"){
			$data = array('is_active'=>$active);
			$data2 = array('approve'=>$active);
			$where = array("seller_id"=>$seller_id,"is_active"=>"1");
			$where2 = array("seller_id"=>$seller_id,"approve"=>"1");
		}else{
			$data = array('is_active'=>$active);
			$data2 = array('approve'=>$active);
			$where = array("seller_id"=>$seller_id,"is_active"=>"2");
			$where2 = array("seller_id"=>$seller_id,"approve"=>"2");
		}
		$this->db->where($where);
		$this->db->update(DBPREFIX."_seller_product",$data);
		$this->db->where($where2);
		$this->db->update(DBPREFIX."_product_inventory",$data2);
		$this->db->where($where);
		$this->db->update(DBPREFIX."_product_accessories",$data);
		if($active == "1"){
			$msg = "User unblocked successfully!";
			$block = "0";
		}else{
			$msg = "User blocked successfully!";
			$block = "1";
		}
		$this->db->where("userid",$seller_id);
		if($this->db->update(DBPREFIX."_users",array("is_active"=>$active,"is_block"=>$block))){
			$this->session->set_flashdata("success",$msg);
			redirect(base_url("seller/dashboard/seller_management"));
		}

	}
	function slugify($text){
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}
}
class Securearea extends MY_Controller
{
	//public $CurlRequest;
	public $isloggedin = FALSE;
	public $userData = array();
	protected $cur_date_time = "";
	public $dateTimeFormat = "d-m-Y H:i:s a";
	public $dateFormat = "d-m-Y";
	public $categoryData = "";
	public $brandsList = "";
	public $productsList = "";
	public $cartitems = array();
	public $carttotalprice = 0;
	public $carttotalitems = 0;
	public $cart_id = "";
	public $hidecartlist = FALSE;
	public $areas = "";
	public $is_ajax = false;
	public $doLogin = false;
	public $menu = array();
	public $parentCategory = array();
	private $notificationCount  = 0;
	private $notifications  = array();

	function __construct()
	{
		parent::__construct();
		if($this->config->item('maintenance_mode') == 1 || $this->config->item('maintenance_mode') == '1') {
			$content = $this->load->view('maintenance_view', '', true);
			echo $content;
			die();
		}
		$this->load->helper(array("form","url","date","cookie"));
		$this->load->library('form_validation');
		$this->load->model("admin/Secure_Model");
		$this->load->model("admin/Brands_Model");
		$this->load->model("admin/Areas_Model");
		$this->load->model("User_Model");
		$this->load->model("Category_Model");
		$this->load->model("Product_Model");
		$this->load->model("Cart_Model");
		$this->load->model("Utilz_Model");
		$this->load->library('Mobile_Detect');
		$detect = new Mobile_Detect;
		$this->data['detect'] = $detect;
		$this->cur_date_time = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));
		$this->checkuser();
		$this->categoryData = array();
		$this->brandsList = array();
		$this->productsList = array();
		//$this->cart_discount($this->cart->contents());
		$this->getcartTotal();
		$this->areas = $this->Areas_Model->getAreas("","area_name,area_pin");
		$this->data['title'] = $this->config->item('web_name');
		$this->is_ajax = $this->input->is_ajax_request();
		$this->data['hasStyle'] = false;
		$this->data['hasScript'] = false;
		$this->data['showSorting'] = false;
		$this->data['showSidebar'] = false;
		$this->data['showSidepanel'] = false;
		$this->db->cache_on();
		// $categoryData = $this->Product_Model->forntCategoryData("display_status = '1' AND is_active='1' AND is_private = '0'");
		$categoryData = $this->Product_Model->forntCategoryData("display_status = '1' AND is_active='1'");
		$this->data['categoryData'] = $categoryData['result'];
		//echo "<pre>";print_r($this->data['categoryData']);die();
		$parentCategory = $this->Product_Model->forntCategoryData("parent_category_id = '0' AND display_status = '1' AND is_active='1'");
		$this->db->cache_off();
		$this->data['parentCategory'] = $parentCategory['result'];
		$this->menu = $this->getMenu($categoryData);
		$expire = (86400*365);
		if(!isset($_COOKIE['country_id'])){
			$country_value= array(
				  'name'   => 'country_value',
				  'value'  => "UNITED STATES (US)",
				  'expire' => $expire,
				);
			$country_id= array(
				  'name'   => 'country_id',
				  'value'  => 226,
				  'expire' => $expire,
				);
			$country_code= array(
				  'name'   => 'country_code',
				  'value'  => 'USA',
				  'expire' => $expire,
				);
			$this->input->set_cookie($country_value);
			$this->input->set_cookie($country_id);
			$this->input->set_cookie($country_code);
		}
		$this->db->cache_on();
		$this->data['offer_left'] = $this->Utilz_Model->getAllData('tbl_special_offers', 'offer_image', array('position'=>'left', 'is_active' => '1'));
		$this->data['offer_right'] = $this->Utilz_Model->getAllData('tbl_special_offers', 'offer_image', array('position'=>'right', 'is_active' => '1'));
		$this->db->cache_off();
		if(!$this->isloggedin){
			if(isset($_COOKIE['zabee_SignedIn'])){
				$uri = $this->uri->segment_array();
				$signInData = json_decode(base64_decode($this->input->cookie('zabee_SignedIn')));
				$userData = $this->checklogindetails($signInData->userid);
				if(isset($userData[0]['is_active']) && $userData[0]['is_active'] == 1){
					$this->doLogin = true;
					$this->isloggedin = TRUE;
					$this->session->set_userdata($userData[0]);
					$this->userData = $userData[0];
				}elseif(isset($userData[0]['is_active']) && $userData[0]['is_active'] == 0 && end($uri) != "login"){
					delete_cookie("zabee_SignedIn");
					$name = $userData[0]->firstname." ".$userData[0]->lastname;
					$name = $name." you are BLOCKED by Admin, Please Contact Support.";
					$this->session->set_flashdata("alert",$name);
					redirect(base_url("login"));
				}
			}
		}
		if($this->isloggedin){
			$this->notificationCount = $this->Utilz_Model->getNotifications($this->userData["userid"], 1, 'new', true);
			$this->notifications = $this->Utilz_Model->getNotifications($this->userData["userid"], 1, 'new', false, 'buyer');
			$this->data['text_notification'] = $this->Secure_Model->checkMsgNotification($this->userData['userid']);
			$this->data['order_notification'] = $this->Secure_Model->checkOrderNotification($this->userData['userid']);
		}
		$this->lang->load('english', 'english');
	}

	function checkuser($post_data=array())
	{
		$getData = $this->input->cookie("zabee_rememberMe");
		//print_r($_COOKIE);die();
		$data = "";
		if(isset($_POST['data'])){
			$data = json_decode($_POST['data']);
		}
		if($this->doLogin && !$this->isloggedin){
			if(isset($_POST['SignIn'])){
				$this->keepMeSignIn($post_data);
			}
			/*if(isset($_POST["remember"])){
				$this->rememberme($post_data);
			}*/
			if(isset($post_data[0])){
				$this->session->set_userdata($post_data[0]);
				$this->userData = $post_data[0];
			}else{
				$this->session->set_userdata($post_data);
				$this->userData = $post_data;
			}
			$this->isloggedin = TRUE;
			if($data !=""){
				$decodingForThirdParty = $data;
				if(!isset($this->userData['userid'])){
					$userid = $this->Secure_Model->getUserIdIfFromThirdParty($decodingForThirdParty->email);
					$this->userData['userid'] = $userid[0]->userid;
				}
			}
			//if($this->userData['userid']){}
			$this->session->unset_userdata('alert');
			return TRUE;
		} else if(isset($_POST['platform'])){
			if(isset($data->id)){
				$social_id = $data->id;

			}elseif(isset($_POST['new_account'])){
				$social_id =$_POST['el'];
			}else{
				$social_id = $data->El;
			}
			$userData = $this->checklogindetails($social_id,'','',true);
			if($userData && $userData[0]['is_active'] == 1){
				if(isset($_POST["SignIn"]))	$this->keepMeSignIn($userData[0]);

				$this->session->set_userdata($userData[0]);
				$this->isloggedin = TRUE;
				$this->userData = $userData[0];
				$this->session->unset_userdata('alert');
				return TRUE;
			}
			else{
				$this->session->set_flashdata("alert",json_encode(array("type"=>"block","msg"=>"Please Check your username/Email Id and Password")));
			}
		}
		else if($this->session->userdata("social_id") && !$this->isloggedin){
			$userData = $this->checklogindetails($this->session->userdata("social_id"),'','',true);
			if($userData){
				$this->isloggedin = TRUE;
				$this->userData = $userData[0];
				return TRUE;
			}
		}
		else if($this->session->userdata("userid") && !$this->isloggedin){
			$userData = $this->checklogindetails($this->session->userdata("userid"),'','');
			if($userData){
				$this->isloggedin = TRUE;
				$this->userData = $userData[0];
				return TRUE;
			}
		}
		$this->isloggedin = FALSE;
		$this->userData = array();
	}
	public function cartExists($user_id){
		$recentCart = $this->session->userdata('cart_contents');
		$oldCart = $this->Cart_Model->getCartContents($user_id);
		$recentSaveForLater = $this->session->userdata('save_contents');
		$oldSaveForLater = $this->Cart_Model->getSavedForLaterContents($user_id);
		$same_product = array();
		//echo "<pre>";print_r($oldCart);die();
		if($recentCart){
			if(!empty($oldCart) || $oldCart =="is_exists"){
				if($oldCart == "is_exists"){
					$oldCart = array();
					$oldCart['total_items'] = 0;
					$oldCart['cart_total'] = 0;
				}
				foreach($recentCart as $key=>$rc){
					if(is_array($rc)){
						$id = $rc['rowid'];
						if(isset($oldCart[$id])){
							$qty = (int)$oldCart[$id]['qty']+(int)$rc['qty'];
							if($qty <= $rc["max_qty"]){
								$subtotal = (int)$qty*(int)$rc['price'];
							}else{
								$qty = $rc['max_qty'];
								$subtotal = (int)$rc['max_qty']*(int)$rc['price'];
							}
							$data = array(
									'rowid' => $id,
									'qty'   => $qty,
									'subtotal'=>$subtotal
							);
							if($rc['seller_id'] != $user_id){
								$this->cart->update($data);
								$oldCart['total_items'] = $oldCart['total_items']-$oldCart[$id]['qty'];
								$oldCart['cart_total']  = $oldCart['cart_total'] - $oldCart[$id]['subtotal'];
								$oldCart[$id]['qty'] = $qty;
								$oldCart[$id]['subtotal'] = $subtotal;
								$oldCart['total_items'] = $oldCart['total_items']+$qty;
								$oldCart['cart_total']  = $oldCart['cart_total'] + $subtotal;
								$this->Cart_Model->updateCart($user_id,$oldCart);
							}else{
								$same_product[] = $rc['name'];
								$this->cart->remove($id);
							}
						}else{
							if($rc['seller_id'] != $user_id){
								if(isset($oldCart['total_items']) && isset($oldCart['cart_total'])){
									$oldCart['total_items'] += $recentCart['total_items'];
									$oldCart['cart_total'] += $recentCart['cart_total'];
									$oldCart[$id] = $rc;
								}else{
									$oldCart['total_items'] = $recentCart['total_items'];
									$oldCart['cart_total'] = $recentCart['cart_total'];
									$oldCart[$id] = $rc;
								}
								$this->Cart_Model->updateCart($user_id,$oldCart);
							}else{
								$same_product[] = $rc['name'];
								$this->cart->remove($id);
							}
						}
					}
				}
				$this->session->set_flashdata("same_product",$same_product);
				foreach($oldCart as $key=>$oc){
					if(is_array($oc)){
						$id = $oc['rowid'];
						if(!isset($recentCart[$id])){
							$this->cart->insert($oc);
						}
					}
				}

			}else{
				foreach($recentCart as $key=>$rc){
					if(is_array($rc)){
						$id = $rc['rowid'];
						if($rc['seller_id'] == $user_id){
							$same_product[] = $rc['name'];
							$this->cart->remove($id);
						}

					}
				}
				$this->session->set_flashdata("same_product",$same_product);
				if($this->session->userdata('cart_contents')){
					$this->Cart_Model->addtoCart($this->session->userdata('cart_contents'),$user_id);
				}
			}
		}else{
			if(!empty($oldCart) && $oldCart !="is_exists"){
				$this->session->set_userdata('cart_contents', $oldCart);
			}else{
				$data= array("total_items"=>0,"cart_total"=>0);
				$this->session->set_userdata('cart_contents', $data);
			}
		}
		if($recentSaveForLater){
			if(!empty($oldSaveForLater) || $oldSaveForLater =="is_exists"){
				if($oldSaveForLater == "is_exists"){
					$oldSaveForLater = array();
					$oldSaveForLater['total_items'] = 0;
					$oldSaveForLater['cart_total'] = 0;
				}
				foreach($recentSaveForLater as $key=>$rs){
					if(is_array($rs)){
						$id = $rs['rowid'];
						if(isset($oldSaveForLater[$id])){
							$qty = (int)$oldSaveForLater[$id]['qty']+(int)$rs['qty'];
							if($qty <= $rs['max_qty']){
								$subtotal = (int)$qty*(int)$rs['price'];
							}else{
								$qty = $rs['max_qty'];
								$subtotal = (int)$rs['max_qty']*(int)$rs['price'];
							}
							$data = array(
									'rowid' => $id,
									'qty'   => $qty,
									'subtotal'=>$subtotal
							);
							if($rs['seller_id'] != $user_id){
								$this->saveforlater->update($data);
								$oldSaveForLater['total_items'] = $oldSaveForLater['total_items']-$oldSaveForLater[$id]['qty'];
								$oldSaveForLater['cart_total']  = $oldSaveForLater['cart_total'] - $oldSaveForLater[$id]['subtotal'];
								$oldSaveForLater[$id]['qty'] = $qty;
								$oldSaveForLater[$id]['subtotal'] = $subtotal;
								$oldSaveForLater['total_items'] = $oldSaveForLater['total_items']+$qty;
								$oldSaveForLater['cart_total']  = $oldSaveForLater['cart_total'] + $subtotal;

								$this->Cart_Model->updateSaveForLater($user_id,$oldSaveForLater);
							}else{
								$_SESSION['same_product'][] = $rs['name'];
								$this->saveforlater->remove($id);
							}
						}else{
							if($rs['seller_id'] != $user_id){
								if(isset($oldSaveForLater['total_items']) && isset($oldSaveForLater['cart_total'])){
									$oldSaveForLater['total_items'] += $recentSaveForLater['total_items'];
									$oldSaveForLater['cart_total'] += $recentSaveForLater['cart_total'];
									$oldSaveForLater[$id] = $rs;
								}else{
									$oldSaveForLater['total_items'] = $recentSaveForLater['total_items'];
									$oldSaveForLater['cart_total'] = $recentSaveForLater['cart_total'];
									$oldSaveForLater[$id] = $rs;
								}
								$this->Cart_Model->updateSaveForLater($user_id,$oldSaveForLater);
							}else{
								$_SESSION['same_product'][] = $rs['name'];
								$this->cart->remove($id);
							}
						}
					}
				}
				foreach($oldSaveForLater as $key=>$os){
					if(is_array($os)){
						if(!isset($recentSaveForLater[$key])){
							$this->saveforlater->insert($os);
						}
					}
				}
			}else{
				$this->Cart_Model->addtoSaveForLater($this->session->userdata('save_contents'),$user_id);
			}
		}else{
			if(!empty($oldSaveForLater) && $oldSaveForLater !="is_exists"){
				$this->session->set_userdata('save_contents', $oldSaveForLater);
			}else{
				$data= array("total_items"=>0,"cart_total"=>0);
				$this->session->set_userdata('save_contents', $data);
			}
		}
	}
	function rememberme($userData)
	{
		$cookie = array(
			'name'   => 'rememberMe',
			'value'  => base64_encode(json_encode($userData[0])),
			'expire' => '108000',
			'domain' => '',
			'path'   => '/',
			'prefix' => 'zabee_',
			'secure' => FALSE
		);
		$this->input->set_cookie($cookie);
	}
	function keepMeSignIn($userData)
	{
		$expire = (86400*365);
		$cookie = array(
			'name'   => 'SignedIn',
			'value'  => base64_encode(json_encode($userData[0])),
			'expire' => $expire,
			'domain' => '',
			'path'   => '/',
			'prefix' => 'zabee_',
			'secure' => FALSE
		);
		$this->input->set_cookie($cookie);
	}
	function checklogindetails($id="",$user_name="",$password='',$isUser=false)
	{
		$userData = $this->Secure_Model->checklogindetails($id,'','',$isUser);
		return $userData;
	}

	/**
	* This function loads the header and all the required parameters
	* @param undefined $obj The object of the current class
	* @param $carousel is carousel or not
	*/
	function loadHeader($obj,$carousel = TRUE,$title = "")
	{
		if($title)
		{
			$header["title"] = $title;
		}
		$header["obj"] = $obj;
		$header["isCarousel"] = $carousel;
		$header["areas"] = $this->areas;
		$header['categories'] = $obj->categoryData;
		$obj->load->view("includes/header",$header);
	}

	function loadSidebar($obj)
	{
		$sidebar["obj"] = $obj;
		$sidebar["categoryData"] = $obj->categoryData;
		$obj->load->view("includes/sidebar",$sidebar);
	}

	function loadFooter($obj)
	{
		$footer["obj"] = $obj;
		$obj->load->view("includes/footer",$footer);
	}

	function checkifajax()
	{
		if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function getBreadCrumb()
	{
		$name = array
		(
			"product"=>"Products",
			"viewLatestProducts"=>"Latest Products",
			"viewFeaturedProducts"=>"Featured Products",
			"categories"=>"displaynot",
			"subcategories"=>"displaynot",
			"register"=>"Registration Form",
			"useredit"=>"displaynot",
			"aboutus"=>"About Us",
			"tac"=>"Terms and Conditions",
			"faq"=>"Frequently Asked Questions",
			"contactus"=>"Contact Us",
		);
		$breadCrumb = array();
		$links = base_url();
		$val["link"] = $links;
		$val["name"] = "Home";
		$breadCrumb[] = $val;
		$uris = $this->uri->segment_array();
		$was_sub = FALSE;
		foreach($uris as $key=>$eachuri)
		{
			$links = $links.$eachuri."/";
			if(isset($name[$eachuri]) && $name[$eachuri] == "displaynot")
			{
				if($eachuri == "subcategories")
				{$was_sub = "subcategories";}
				continue;
			}
			$check = FALSE;
			if(is_numeric($eachuri))
			{
				if($uris[1] == "categories" || $uris[1] == "subcategories")
				{
					$catname = "";
					foreach($this->categoryData as $data)
					{
						if($uris[1] == "subcategories" && $data["category_id"] == $eachuri)
						{
							$val["link"] = base_url()."categories/".$data["category_id"];
							$val["name"] = $data["category_name"];
							$breadCrumb[] = $val;
							$check = TRUE;
							break;
						}
						else if($data["category_id"] == $eachuri)
						{
							$catname = $data["category_name"];
							break;
						}
						else if(isset($data["sub_categories"]) && is_array($data["sub_categories"]))
						{
							foreach($data["sub_categories"] as $subs)
							{
								if($subs["category_id"] == $eachuri)
								{
									$catname = $subs["category_name"];
									break;
								}
							}
						}
					}
					$name[$eachuri] = $catname;
				}
				else
				{
					continue;
				}
				//echo "<pre>";print_r($this->categoryData);die;
			}
			if($check == TRUE)continue;
			$val["link"] = $links;
			if(isset($name[$eachuri]))$val["name"] = $name[$eachuri];
			else $val["name"] = ucfirst(urldecode(removeunderscores($eachuri)));
			$breadCrumb[] = $val;
		}
		return $breadCrumb;

	}
	private function getBrandsList()
	{
		$brands = $this->Brands_Model->getBrands("","brands.brand_name, brands.brand_id, brands.brand_image, brands.brand_description","1","1"," brand_name ASC, ");
		$retval = array();
		if($brands)
		{
			foreach($brands as $each)
			{
				$retval[$each["brand_id"]] = $each;
			}
		}
		return $retval;
	}
	private function getcartTotal()
	{
		$cart_id = $this->input->cookie("cart_id");
		if($cart_id)
		{
			if($this->Cart_Model->checkcartid($cart_id))
			{
				$this->cart_id = $cart_id;
			}
		}
		if($this->cart_id)
		{
			$cart = $this->Cart_Model->getCartContents($this->cart_id );
			$totalPrice = 0;
			if(is_array($cart))
			{
				foreach($cart as $each)
				{
					$totalPrice += doubleval($each['price'] * $each['qty']);
				}
				$this->cartitems = $cart;
				$this->carttotalitems = count($cart);
				$this->carttotalprice = $totalPrice;
				$this->cart->insert($cart);
			}
		}
		else
		{
			$this->cartitems = array();
			$this->carttotalitems = 0;
			$this->carttotalprice = 0;
		}
	}
	public function getcountries(){
		$countries = array();
		$countries=$this->Utilz_Model->countries();
		echo json_encode($countries);
	}

	protected function getMenu($categoryData){
		$menu = array();
		//$menu = $this->Utilz_Model->html_ordered_menu($this->Utilz_Model->getMenu(), 0,$categoryData);
		$category = array(
			'categories' => array(),
			'parent_cats' => array()
		);

		//build the array lists with data from the category table
		foreach($categoryData['result'] as $row){
			//creates entry into categories array with current category id ie. $categories['categories'][1]
			$category['categories'][$row->category_id] = $row;
			//creates entry into parent_cats array. parent_cats array contains a list of all categories with children
			$category['parent_cats'][$row->parent_category_id][] = $row->category_id;
		}
		//echo "<pre>";print_r($category);die();
		$menu = $this->Utilz_Model->buildCategory(0,$category);
		return $menu;
	}

	public function getMedia($product_id){
		$media_data=$this->Utilz_Model->getMedia($product_id);
		return $media_data;
		//echo "<pre>";print_r($media_data); echo "</pre>";//die();
	}
	public function getAllCategoriesChildId($category_id){
		$category_ids = $this->Utilz_Model->getAllCategoriesChildId($category_id);
		//echo $category_ids;
		return $category_ids;
	}
	public function getAllCategoriesParentId($category_id){
		$category_ids = $this->Utilz_Model->getAllCategoriesParentId($category_id);
		//echo $category_ids;
		return $category_ids;
	}

	public function cart_discount($cart){
		//echo "<pre>";print_r($this->cart->contents());die();
		$callFrom = 'core';

		//$this->Cart_Model->verifyCartItems($cart, $callFrom);

		$return = $this->Cart_Model->testCart2($cart, $callFrom);
		 //echo "<pre>"."asdasdasdasdasdasdasdasdas"; print_r($this->cart->contents()); die();
		//echo "<pre>";print_r($products);die();
		return $return;
	}

	public function add_log($keyword, $type){
		$this->load->library('user_agent');
		if($this->session->userdata('userid')){
			$userid = $this->session->userdata('userid');
		}else{
			$userid="";
		}
		$remote_ip = (isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:"-";
		$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
		$meta_info = $this->agent->agent_string();
		$os = $this->agent->platform();
		$platform = 'W';
		$session_id = $this->session->session_id;
		$this->User_Model->keywordSearch($keyword,$remote_ip,$referer_url,$userid,$type, $platform, $meta_info, $os, $session_id);
	}
	//Hubx Code
	public function getAccessToken($user_id){
		$access_token = $this->hubx->getAccessToken();
		$access_token = json_decode($access_token);
		$time = time()+$access_token->expires_in;
		$access_token->expires_in = $time;
		$access_token = json_encode($access_token);
		$this->db->where('userid',$user_id);
		$this->db->update(DBPREFIX."_users",array("hubx_info"=>$access_token));
		return $access_token;
	}
	public function checkToken(){
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');
		$hubx_info = $this->session->userdata('hubx_info');
		$access_token = "";
		$expires_in = "";
		$time = time();
		$params = array('user_id'=>$user_id,"user_type"=>$user_type,"client_id"=>$this->config->item("hubx_client_id"),"client_secret"=>$this->config->item("hubx_client_secret"));
		$this->load->library('Hubx',$params);
		if($hubx_info){
			$hubx_info = json_decode($hubx_info);
			if($time > $hubx_info->expires_in){
				$hubx_info = $this->getAccessToken($user_id);
				$hubx_info = json_decode($hubx_info);
			}

		}else{
			$hubx_info = $this->getAccessToken($user_id);
			$hubx_info = json_decode($hubx_info);
		}
		$this->session->set_userdata('access_token',$hubx_info->access_token);
		$this->session->set_userdata('expires_in',$hubx_info->expires_in);
	}
}
class WarehouseAccess extends MY_Controller
{
	protected $userData = "";
	protected $cur_date_time = "";
	public $dateTimeFormat = "d-m-Y H:i:s a";
	public $dateFormat = "d-m-Y";
	public $checkUserWarehouse = "";
	public $checkUserTextNotificaiton="";
	public $isScript = false;
	public $status = "";
	public $notificationCount  = 0;
	public $notifications  = array();
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->helper(array('form', 'url'));
		$this->load->helper('date');
		$this->load->helper('cookie');
		$this->load->model("Warehouse_Model");
		$this->load->model("Utilz_Model");
		$this->cur_date_time = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));
		$this->userData = $this->checkLoginWarehouse();
		//$this->pendingProductData = $this->getPendingProducts();
		$id = "";
		if(isset($this->userData[0]["userid"])){
			$id = $this->userData[0]["userid"];
			$usertype = $this->userData[0]["user_type"];
			$this->notificationCount = $this->Utilz_Model->getNotifications($this->userData[0]["userid"], $usertype, 'new', true);
			$this->notifications = $this->Utilz_Model->getNotiFormatted($this->userData[0]["userid"], $usertype, 'new', false, 'seller');
		}
		$this->checkUserWarehouse = $this->checkWarehouse($id);
		$this->checkUserTextNotificaiton = $this->checkTextNotification($id);

	}
	protected function checkLoginWarehouse()
	{
		//die("here 2");
		$userData = $this->checkWarehouseConditions();
		//echo "<pre>"; print_r($userData);die();
		if($userData)
		{
			//echo "<pre>"; print_r($userData);die();
			return $userData;
		}
		//echo $this->isLogin;die();
		if(isset($this->isLogin) && $this->isLogin)
		{
			return FALSE;
		}
		//echo "<pre>"; print_r($userData);die();
		$this->backToLoginWarehouse();
	}

	/**
	* This function contains any operation when login fails
	*/
	protected function backToLoginWarehouse()
	{
		$status = "incorrect";
		$this->session->sess_destroy();
		redirect(base_url()."warehouse/login/".$status,"refresh");
	}

	protected function checkWarehouseConditions(){
		//$getData = $this->input->cookie("ecomm_adminData");
		$getData = $this->input->cookie("zabeeWarehouseData");
		$checkDB = false;
		// echo "<pre>";print_r($this->session->zabeeWarehouseData);print_r($this->session);print_r($getData);die();
		if($getData){
			$this->session->set_userdata(unserialize(base64_decode($getData)));
		}
		//print_r($_POST);die();
		//echo "<pre>";print_r($this->session->userdata['userid']);print_r($this->session->zabeeWarehouseData["userid"]);die();
		if(isset($this->session->zabeeWarehouseData["userid"]) || isset($this->session->userdata['userid']) || isset($_POST['userid']) || isset($_POST['username']) || isset($_POST['password'])){
			//print_r($_POST);die();
			if(isset($this->session->userdata['userid']) && !isset($this->session->zabeeWarehouseData['userid'])){
				$_POST['userid'] = $this->session->userdata['userid'];
			}
			$userData = (isset($_POST['userid'])  || isset($_POST['username']) || isset($_POST['password']))? $_POST : $this->session->zabeeWarehouseData;
			if(isset($userData['userid'])){
				$userid = $userData['userid'];
			}else{
				$userid = "";
			}
			if(isset($userData['username']) && isset($userData['password'])){
				$username = $userData['username'];
				$password = $userData['password'];
				$firstSalt = explode("@",$username);
				$lastSalt = "zab.ee";
				$hash = $firstSalt[0].$password.$lastSalt;
			}else{
				$username = "";
				$password = "";
			}
			$remember = isset($userData['remember'])?$userData['remember'] : "off";
			//print_r($userData);die();
			if($username && $password){
				$checkDB = $this->Warehouse_Model->checkWarehouseLoginDetails("",$username,sha1($hash));
			}
			if($userid){
				$checkDB = $this->Warehouse_Model->checkWarehouseLoginDetails($userid,"","");
			}
			//echo "<pre>";print_r($getData);die();
			if($checkDB != FALSE){
				$checkDB[0]['user_type']="3";
				if(isset($_POST['userid']) || isset($_POST['username']) || isset($_POST['password']))
				{
					$_POST["remember"] = $remember;
					//session expires after 30 days if remember me is selected 30 * 3600 = 108000
					if($remember == "off")$this->session->sess_expiration = "7200";
					else
					{
						$cookie = array(
						    'name'   => 'zabeeWarehouseData',
						    'value'  => base64_encode(serialize($checkDB[0])),
						    'expire' => '108000',
						    'domain' => '',
						    'path'   => '/',
						    //'prefix' => 'ecomm_',
						    'secure' => FALSE
						);
						$this->input->set_cookie($cookie);
					}
					//$this->session->set_userdata("zabeeWarehouseData",$checkDB[0]);
				}
				//print_r($checkDB);die();
				$usertypes = $this->Warehouse_Model->checkWarehouseLink($this->uri->segment_array(),$checkDB[0]['user_type']);
				//print_r($usertypes);
				if(!(empty($usertypes))){
					//$this->session->zabeeWarehouseData.push($usertypes);
					//$this->session->set_userdata($usertypes);
					//echo "<pre>";print_r($this->session->zabeeWarehouseData);echo "</pre>";
					array_push($checkDB[0],$usertypes);
					//print_r($checkDB);die();
					$this->session->set_userdata("zabeeWarehouseData",$checkDB[0]);
					return $checkDB;
				}
			}
		}
		return FALSE;
	}
	private function checkWarehouse($id){
		$checkUserWarehouse = $this->Warehouse_Model->checkUserWarehouse($id);
		return $checkUserWarehouse;
	}
	public function checkTextNotification($id){
		$checkMsgNotification = $this->Warehouse_Model->checkMsgNotification($id);
		$i = 0;
		if($checkMsgNotification['rows'] > 0){
			foreach($checkMsgNotification['result'] as $bl){
				$checkMsgNotification['result'][$i]->product_link="";
				$checkMsgNotification['result'][$i]->product_id="";
				$checkMsgNotification['result'][$i]->product_name="";
				if($bl->product_variant_id){
					if($bl->item_type == "product"){
						$product = $this->Warehouse_Model->checkProductDetails('','',$bl->product_variant_id);
						if($product['productDataRows'] > 0){
							$checkMsgNotification['result'][$i]->product_link=$product['productData']->product_name."-".$product['productData']->brand_name."-".$product['productData']->category_name;
							$checkMsgNotification['result'][$i]->product_id=$product['productData']->product_id;
							$checkMsgNotification['result'][$i]->product_name=$product['productData']->product_name;
						}
					}
				}
				$i++;
			}
		}
		return $checkMsgNotification;
	}

	/*protected function getPendingProducts(){
		$user_id = $this->session->userdata("userid");
		//  echo"<pre>";print_r($user_id);die();
			if($user_id == 1){
				$where = array('is_active' => "0",'created_by'=>'seller', 'is_declined' => '0');
				// print_r($where);
				$pend_prod_number = $this->Product_Model->getNumberofPendingproducts($where);
						//  echo"<pre>";print_r($pend_prod_number);die();

			}
			if($user_id != 1){
				$where = array('is_active' => "0", 'created_id' => $user_id, 'is_declined' => '0');
				$pend_prod_number = $this->Product_Model->getNumberofPendingproducts($where);
						// print_r($pend_prod_number);die();

			}
			// print_r($where);die();
		return $pend_prod_number;
		// print_r($pend_prod_number);die();
	}*/
}
?>