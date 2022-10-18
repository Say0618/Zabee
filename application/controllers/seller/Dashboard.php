<?php
class Dashboard extends SecureAccess 
{
	var $data;
	function __construct(){
		parent::__construct();
		$this->load->model("admin/User_Model");
		$this->load->model("Utilz_Model");
		$this->load->model("admin/Dashboard_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
	}
	public function index(){
		$userid=$_SESSION['userid'];
		$this->data['user_store'] = "";
		$id = $this->session->userdata('userid');
		$is_admin = 0;
		if(!$this->session->userdata('userid')){
			$id = $this->session->userdata('admin_id');
		}
		if($this->session->userdata['user_type'] == 1){
			$is_admin = 1;
		}
		$this->data['is_admin']				= $is_admin;
		$this->data['dashboard_settings']	= $this->Dashboard_Model->getDashboardSettings($id);
		$this->data['seller_id'] 			= $id;
		$this->data['page_name'] 			= 'dashboard';
		$this->data['Breadcrumb_name'] 		= $this->lang->line('dashboard');
		$this->data['isScript'] 			= true;
		//echo "<pre>";print_r($this->data);die();
		$this->load->view("admin/admin_template",$this->data);
	}
	public function store(){
		$userid=$_SESSION['userid'];
		$this->data['user_store'] = "";
		$id = "";
		if($this->session->userdata('userid')){
			$id = $this->session->userdata('userid');
		} else {
			$id = $this->session->userdata('admin_id');
		}
		$checkProductSell = $this->Secure_Model->checkProductSell($id);
		$checkUserStore = $this->User_Model->checkUserStore($id);
		if($checkProductSell){
			$this->session->set_flashdata('notify', $this->lang->line('new_order'));
		}
		if(isset($checkUserStore['rows']) && $checkUserStore['rows'] > 0){
			$this->data['user_store'] = $checkUserStore['result'];
		}else{
			$message=$this->lang->line('create_store');
			$this->session->set_flashdata('message', $message);	
		}
		//$this->data['users']				= $this->User_Model->get_users($userid);
		//$this->data['profile']				= $this->User_Model->get_profile($userid);
		$this->data['countries']			= $this->Utilz_Model->countries();
		$this->data['statesList'] 			= $this->Utilz_Model->countries("","","",false,"tbl_states");
		$this->data['getLegalBusniessType'] = $this->Secure_Model->getLegalBusniessType();
		$this->data['page_name'] 			= 'store_info';
		$this->data['Breadcrumb_name'] 		= "Store Info";
		$this->data['isScript'] 			= true;
		//echo "<pre>";print_r($this->data);die();
		$this->load->view("admin/admin_template",$this->data);
	}
	public function get_states($text=""){
		$result = "";
		if(isset($_POST['text'])){
			$text = $_POST['text'];
		}
		if($text){
			$get_state = $this->Utilz_Model->getState($text);
			$i = 0; 
			$result = array();
			$rows = count($get_state);
			while( $i < $rows  ){
				$result[] = array(
					'label' => stripslashes($get_state[$i]->state),
					'id'=> $get_state[$i]->id,
					'Value' => stripslashes($get_state[$i]->id),
				);
				$i++;
			}
		}
		echo json_encode($result);
	}
	
	public function saveStore(){
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('store_name','Store Name','trim|required');
		$this->form_validation->set_rules('country','Country','trim|required');
		$this->form_validation->set_rules('contact_phone','Contact Phone','trim|required');
		$this->form_validation->set_rules('contact_email','Contact Email','trim|required');
		$this->form_validation->set_rules('legal_busniess_type','Legal Busniess Type','required');
		$this->form_validation->set_rules('is_zabee','is zabee','required|is_natural');		
		if($this->form_validation->run() === true){
			$store_status = ($this->session->userdata('user_type') != '1')?'0':'1';
			extract($this->input->post());
			$today = date('Y-m-d H:i:s');
			$store_id = strtolower(str_replace(' ','-',$store_name));
			$data = array('created_date'			=>$today,
						  'seller_id'				=>$userid,
						  'store_name' 				=>addslashes($store_name),
						  'store_id'				=>$store_id,
						  'legal_busniess_type'		=>$legal_busniess_type,
						  'country_id'				=>$country,
						  'contact_phone'			=>$contact_phone,
						  'contact_email'			=>$contact_email,
						  'is_zabee'				=>$is_zabee,
						  'is_tax'                  =>(isset($is_tax))?$is_tax:"0",
						  'is_approve'				=>$store_status
						);
			if($_FILES['profile_image']['name'] !=""){
				$config['upload_path'] 				= "store_logo";
				$config['upload_thumbnail_path'] 	= "store_logo/thumbs";
				$config['allowed_types'] 			= 'gif|jpg|png|jpeg';
				$config['quality'] 					= "100%";
				$config['remove_spaces'] 			= TRUE;

				$params['file'] 		= curl_file_create($_FILES['profile_image']['tmp_name'], $_FILES['profile_image']['type'], $_FILES['profile_image']['name']);
				$params['image_type'] 	= "store_logo";
				$params['filesize'] 	= $_FILES['profile_image']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				//print_r($file); die();
			}
			if($_FILES['profile_image_cover']['name'] !=""){
				$config['upload_path'] 				= "store_cover";
				$config['upload_thumbnail_path'] 	= "store_cover/thumbs";
				$config['allowed_types'] 			= 'gif|jpg|png|jpeg';
				$config['quality'] 					= "100%";
				$config['remove_spaces'] 			= TRUE;
				
				$params['file'] 		= curl_file_create($_FILES['profile_image_cover']['tmp_name'], $_FILES['profile_image_cover']['type'], $_FILES['profile_image_cover']['name']);
				$params['image_type'] 	= "store_cover";
				$params['filesize'] 	= $_FILES['profile_image_cover']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server 			= $this->config->item('media_url').'/file/upload_media';
				$file 					= $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$data['cover_image']	= $file->images->original->filename;
			}
			$table = 'tbl_seller_store';
			$result = $this->User_Model->saveData($data,$table);
			$table = 'tbl_state_tax';
			$state_id = isset($stateId)?$stateId:"";
			if($state_id != ""){
				$states = explode(",",$state_id);
				foreach($states as $state){
				if($state != ""){
					$data = array(
						's_id' => $result,
						'state_id' => $state,
						'userid' => $userid
						);
						$return = $this->User_Model->saveData($data,$table);
					}
				}
			}
			$_SESSION['store_name'] 		= $store_name;
			$_SESSION['store_id'] 			= $result;
			$_SESSION['store_status']		= $store_status;
			$_SESSION['store_slug']			= $store_id;
			$_SESSION['is_zabee'] 			= $is_zabee;
			$_SESSION['warehouse_id'] 		= "0";
			$_SESSION['warehouse_title'] 	= "";
			if($is_zabee == '1'){
				$zabee_id = $this->User_Model->getZabeeWarehouse();
				if($zabee_id){
					$_SESSION['warehouse_id'] =$zabee_id[0]->warehouse_id;
					$_SESSION['warehouse_title'] =$zabee_id[0]->warehouse_title;				
				}
			}
			if($result){
				$this->session->set_flashdata("success",$this->lang->line('create_store_success')." Waiting For admin approval.");
				$this->RefreshListingPage();	
			}else{
				$this->session->set_flashdata("error",$this->lang->line('create_store_err'));
				$this->RefreshListingPage();	
			}
		}
	}

	public function deleteState(){
		extract($this->input->post());
		$deletePrevious = $this->User_Model->deleteData(array('userid'=>$userid,'s_id'=>$store_id));
		echo json_encode($deletePrevious);
	}
	
	public function updateStore(){
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('store_name','Store Name','trim|required');
		$this->form_validation->set_rules('country','Country id','trim|required');
		$this->form_validation->set_rules('contact_phone','Contact Phone','trim|required');
		$this->form_validation->set_rules('contact_email','Contact Email','trim|required');
		$this->form_validation->set_rules('legal_busniess_type','Legal Busniess Type','required');
		$this->form_validation->set_rules('is_zabee','is zabee','required|is_natural');
		if($this->form_validation->run() === true){	
			//echo "<pre>";print_r($_FILES);print_r($this->input->post());die();
			extract($this->input->post());
			$state_id = isset($stateId)?$stateId:"";
			$table = 'tbl_state_tax';
			if($state_id != ""){
				$states = explode(",",$state_id);
				$deletePrevious = $this->User_Model->deleteData(array('userid'=>$userid,'s_id'=>$s_id));
				foreach($states as $state){
				if($state != ""){
					$data = array(
						's_id' => $s_id,
						'state_id' => $state,
						'userid' => $userid
						);
						$return = $this->User_Model->saveData($data,$table);
					}
				}
			}
			$store_id = strtolower(str_replace(' ','-',$store_name));
			$today = date('Y-m-d H:i:s');
			$data = array('updated_date'			=>$today,
						  'seller_id'				=>$userid,
						  'store_name' 				=>addslashes($store_name),
						  'store_id'				=>$store_id,
						  'legal_busniess_type'		=>$legal_busniess_type,
						  'country_id'				=>$country,
						  'contact_phone'			=>$contact_phone,
						  'contact_email'			=>$contact_email,
						  'is_zabee'				=>$is_zabee,
						  'is_tax'                =>$is_tax
						  );
			$_SESSION['store_name'] = $store_name;
			$_SESSION['s_id'] 		= $s_id;
			$_SESSION['is_zabee'] 	= $is_zabee;
			if($is_zabee == '1'){
				$zabee_id = $this->User_Model->getZabeeWarehouse();
				if($zabee_id){
					$_SESSION['warehouse_id'] 	 = $zabee_id[0]->warehouse_id;
					$_SESSION['warehouse_title'] = $zabee_id[0]->warehouse_title;
				}
			}else{
				$_SESSION['warehouse_id'] 	 = "0";
				$_SESSION['warehouse_title'] = "";
			}
			if($_FILES['profile_image']['name'] !=""){
				$config['upload_path'] 			 = "store_logo";
				$config['upload_thumbnail_path'] = "store_logo/thumbs";
				$config['allowed_types'] 		 = 'gif|jpg|png|jpeg';
				$config['quality'] 				 = "100%";
				$config['remove_spaces'] 		 = TRUE;
				
				$params['file'] 		= curl_file_create($_FILES['profile_image']['tmp_name'], $_FILES['profile_image']['type'], $_FILES['profile_image']['name']);
				$params['image_type'] 	= "store_logo";
				$params['filesize'] 	= $_FILES['profile_image']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server = $this->config->item('media_url').'file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$data['store_logo']		= $file->images->original->filename;
				$this->session->set_userdata('store_pic', $file->images->original->filename);
			}
			if($_FILES['profile_image_cover']['name'] !=""){
				$config['upload_path'] 				= "store_cover";
				$config['upload_thumbnail_path'] 	= "store_cover/thumbs";
				$config['allowed_types'] 			= 'gif|jpg|png|jpeg';
				$config['quality'] 					= "100%";
				$config['remove_spaces'] 			= TRUE;
				
				$params['file'] 		= curl_file_create($_FILES['profile_image_cover']['tmp_name'], $_FILES['profile_image_cover']['type'], $_FILES['profile_image_cover']['name']);
				$params['image_type'] 	= "store_cover";
				$params['filesize'] 	= $_FILES['profile_image_cover']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server 			= $this->config->item('media_url').'file/upload_media';
				$file 					= $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$data['cover_image']	= $file->images->original->filename;
			}
			$table = 'tbl_seller_store';
			$result = $this->User_Model->updateData($data,array('seller_id'=>$userid,'s_id'=>$s_id),$table);
			if($result){
				$this->session->set_flashdata("success",$this->lang->line('create_store_upd'));
				$this->RefreshListingPage();	
			}else{
				$this->session->set_flashdata("error",$this->lang->line('err')."!.");
				$this->RefreshListingPage();	
			}
		}
	}

	private function RefreshListingPage($url="")
	{
		if($url){
			redirect($url,"refresh");
		} else {
			redirect(base_url()."seller/dashboard/store","refresh");
		}		
	}

	public function check_store_exist(){
		extract($this->input->post());
		$result = $this->User_Model->checkStoreExist($store_name,$s_id);
		if($result == 0){
			echo "true";
		}else{
			echo "false";
		}
	}

	public function chat()
	{
		$id = "";
		if($this->session->userdata('userid')){
			$id = $this->session->userdata('userid');
		} else {
			$id = $this->session->userdata('admin_id');
		}
		if(true){
			$this->session->set_flashdata('info', $this->lang->line('new_msg'));
		}
		$data['oObj'] = $this;	
		$this->load->view("admin/admin_header",$data);	
		$this->load->view("admin/chat");
		$this->load->view("admin/admin_footer");
	}

	public function menu(){
		if(isset($_POST['submit_button'])){
			$name=$_POST['name'];
			$parent=$_POST['parent'];
			$menu_link=$_POST['menu_link'];
			if($parent!=0){
				$next_order=$this->Utilz_Model->get_children($parent);
				$next_order=$next_order+1;
				$menu_order=$parent.".".$next_order;
			}
			elseif($parent==0){
				$menu_order=$this->Utilz_Model->get_next_order();
				$menu_order=floor($menu_order->menu_order)+1;				
			}
			$data=array('menu_name'=>$name, 'parent_id'=>$parent, 'menu_link'=>$menu_link,'menu_order'=> $menu_order);
			$result = $this->User_Model->insert_menu($data);
			if($result){
				redirect($_SERVER['HTTP_REFERER']);
			} else {
				echo "error";
			}				
		}
		$this->data['menu']			=$this->Utilz_Model->ordered_menu($this->Utilz_Model->getMenu());
		$this->data['menu']			=$this->User_Model->get_menu();
		$this->data['html']			=$this->Utilz_Model->html_ordered_menu($this->Utilz_Model->getMenu());
		$this->data['user_store'] 	= "";
		$id = "";
		if($this->session->userdata('userid')){
			$id = $this->session->userdata('userid');
		} else {
			$id = $this->session->userdata('admin_id');
		}
		$checkProductSell = $this->Secure_Model->checkProductSell($id);
		$checkUserStore = $this->User_Model->checkUserStore($id);
		if($checkProductSell){
			$this->session->set_flashdata('notify', $this->lang->line('new_req'));
		}
		if($checkUserStore['rows'] > 0){
			$this->data['user_store'] = $checkUserStore['result'][0];
		}
		$this->data['countries']			= $this->Utilz_Model->countries();
		$this->data['getLegalBusniessType'] = $this->Secure_Model->getLegalBusniessType();
		$this->data['page_name'] 			= 'menu';
		$this->data['Breadcrumb_name'] 		= 'Menu';
		$this->data['isScript'] 			= true;
		$this->load->view("admin/admin_template",$this->data);
	}

    public function save_list(){
       	$i['status'] = 1;
		$data=$this->input->post('data');
		$id = array();
        foreach ($data as $value) {
			$id[]=$value['id'];
        	$this->db->where('menu_id',$value['id']);
        	if(!$this->db->update('tbl_menu',array('menu_order'=>$value['menu_order'],'parent_id'=>$value['parent_id']))){
        		$i++;
        	}
		}
		$id = implode(',',$id);
		$this->db->where_not_in('menu_id',$id,FALSE);
		$this->db->delete('tbl_menu');
        echo json_encode($i);
	}

    public function create_relations($menu_id, $children,$i){
		$parent_id = $menu_id;
        echo "<pre>"; print_r($children); echo "</pre>";
		foreach ($children as $child) {
			$this->saveMenuItem(array(
				"menu_order"=>$i,
				"parent_id" =>$parent_id,
				"menu_id"=>$child->id

			));
			if(isset($child->children) or $child->children !=NULL ){
				$i++;
				$this->create_relations($child->id,$child->children,$i);
			} else {
				 $this->saveMenuItem(array(
				"menu_order"	=> $i,
				"parent_id" 	=> $parent_id,
				"menu_id"		=> $child->id
				));
				$i++;
			}
		}
		return true;
	}
	
    public function saveMenuItem($data){
        $this->User_Model->save_list($data);
    }
    
	public function wizard()
    {
		$datas=$this->input->post();
		$userid=$datas['userid'];
		if($datas['table_used'] == 'seller_store'){
			$table = "tbl_seller_store";
		}
		if($datas['table_used'] == 'users'){
			$table = "tbl_users";
		}
		if($datas['table_used'] == 'profiles'){
			$table = "tbl_profiles";
		}
		unset($datas['userid']);
		unset($datas['table_used']);
		unset($datas['remember']);
		if(isset($_FILES['image']) && $_FILES['image']['name'] !=""){
			$files=$_FILES['image']; 
			if($table=="tbl_profiles"){
				$Path = 'uploads/profile/';
				$type='profile';
			}
			elseif($table=="tbl_seller_store"){
				$Path = 'uploads/store_logo/';
				$type='store logo';
			}
			$img =$this->uploadImage($Path, $_FILES["image"], $type,$userid);
			$image['image'] =  $img['original'];
			if($table=="tbl_profiles"){
				$datas['profile_imagelink']=$image['image'];
			}
			elseif($table=="tbl_seller_store"){
				$store_id = strtolower(str_replace(' ','-',$datas['store_name']));
				$datas['store_logo']=$image['image'];
			}
		}
		$step_one = $this->User_Model->wizard($datas,$userid,$table);  
    	echo json_encode($step_one);
	}
	
	public function seller_management()
	{
		$this->data['page_name'] 		= 'seller_management';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('management');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);

	}

	public function buyer_management()
	{
		$this->data['page_name'] 		= 'buyer_management';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('management');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);

	}

	public function notification_status()
	{
		$data = $this->input->post();
		$proceed = true;
		$response = array('status'=>0,'message'=>'Invalid request', 'code'=>'000');
		if(!$this->session->userdata('userid')){
			$response['message'] = $this->lang->line('user_not_logged_in');
			$response['code'] 	 = '001';
			$proceed 			 = false;
		}
		if($proceed && (!$data['notification_id'] || $data['notification_id'] == '')){
			$response['message'] = $this->lang->line('no_notif');
			$response['code'] 	 = '002';
			$proceed 			 = false;
		}
		if($proceed && (!$data['userid'] == $this->session->userdata('userid'))){
			$response['message'] = $this->lang->line('invalid_user');
			$response['code'] 	 = '003';
			$proceed 			 = false;
		}
		if($proceed){
			$this->Utilz_Model->updateNotificationStatus($data['userid'], $data['usertype'], $data['notification_id']);
			$response['message'] = $this->lang->line('updated_success');
			$response['status']  = 1;
		}
		echo json_encode($response);
	}
	public function deals_signup(){
		$this->data['page_name'] 		= 'deals_signup';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('deals_signup');
		$this->data['isScript'] 		= true;
		$this->data['signups'] = $this->Utilz_Model->getDealSignups();
		$this->load->view("admin/admin_template",$this->data);

	}
	public function saveSettings(){
		extract($this->input->post());
		$return = $this->Dashboard_Model->saveSettings($id,$seller_id,$box_setting);
		echo json_encode($return);
	}
	public function getOrdersData(){
		extract($this->input->post());
		if($is_admin == 1){
			$seller_id = "";
		}
		$return = $this->Dashboard_Model->getDashboardOrderData($status,$seller_id);
		echo json_encode($return);
	}
	public function getRequestData(){
		extract($this->input->post());
		$return = $this->Dashboard_Model->getDashboardRequestData($seller_id,$is_admin);
		echo json_encode($return);
	}
	public function getInventoryData(){
		extract($this->input->post());
		$return = $this->Dashboard_Model->getDashboardInventoryData($seller_id,$approve,$is_admin);
		echo json_encode($return);
	}
	public function getProductData(){
		extract($this->input->post());
		$return = $this->Dashboard_Model->getDashboardProductData($seller_id,$approve,$is_admin);
		echo json_encode($return);
	}
	public function getMessageData(){
		$this->load->model("Message_Model");
		extract($this->input->post());
		$buyerList = $this->Message_Model->getBuyerList($seller_id,5);
		$i = 0;
		if($buyerList['rows'] > 0){
			foreach($buyerList['result'] as $bl){ 
				if($bl->userid == $bl->seller_id){
					$buyerList['result'][$i]->picCheck = "seller_";
				}else{
					$buyerList['result'][$i]->picCheck = "buyer_";
				}
				if($bl->product_variant_id){
					if($bl->item_type == "product"){
						$product = $this->Product_Model->productDetails('','',$bl->product_variant_id);
						if($product['productDataRows'] > 0){
							$buyerList['result'][$i]->product_link=$product['productData']->product_name."-".$product['productData']->brand_name."-".$product['productData']->category_name;
							$buyerList['result'][$i]->product_id=$product['productData']->product_id;
							$buyerList['result'][$i]->product_name=$product['productData']->product_name;
						}else{
							$buyerList['result'][$i]->product_link="";
							$buyerList['result'][$i]->product_id="";
							$buyerList['result'][$i]->product_name="";
						}
					}
				}else{
					$buyerList['result'][$i]->product_link="";
					$buyerList['result'][$i]->product_id="";
					$buyerList['result'][$i]->product_name="";
				}
				$i++;
			}
		}
		echo json_encode($buyerList);
	}
}
?>