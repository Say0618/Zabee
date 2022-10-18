<?php
class Dashboard extends WarehouseAccess 
{
	var $data;
	function __construct(){
		parent::__construct();
		$this->load->model("User_Model");
		$this->load->model("Warehouse_Model");
		$this->load->model("Utilz_Model");
		if($this->userData == FALSE){
			redirect(base_url("warehouse/login/").$status,"refresh");
		}
		$this->data = array(
			'page_name' => 'store_info',
			'isScript' => false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' => $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		$this->lang->load('english', 'english');
	}

	public function index()
	{
		if(!$this->checkUserWarehouse){
			$message=$this->lang->line('warehous_err');
			$this->session->set_flashdata('error', $message);	
			redirect(base_url('warehouse/create'));
		}
		$this->data['page_name'] 		= 'warehouse_list';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('warehouse_list');
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template",$this->data);
	}

	public function create($id=""){
		$userid							= $this->session->zabeeWarehouseData['userid'];
		$this->data['user_warehouse'] 	= "";
		$this->data['error'] 			= "";
		$checkProductSell 				= $this->Warehouse_Model->checkProductSell($id);
		$checkWarehouse 				= $this->Warehouse_Model->checkWarehouse($userid,$id);
		if($checkProductSell){
			$this->session->set_flashdata('notify', $this->lang->line('warehous_req'));
		}
		if(isset($checkWarehouse['rows']) && $checkWarehouse['rows'] > 0){
			$this->data['warehouse'] = $checkWarehouse['result'][0];
		}else{
			if($id !=""){
				$this->data['error'] = $this->lang->line('invalid_id');
			}
		}
		$this->data['countries']			= $this->Utilz_Model->countries();
		$this->data['statesList'] 			= $this->Utilz_Model->countries("","","",false,"tbl_states");
		$this->data['getWarehouseClass'] 	= $this->Warehouse_Model->getData("tbl_warehouse_class");
		$this->data['page_name'] 			= 'warehouse_info';
		$this->data['Breadcrumb_name'] 		= $this->lang->line('warehous');
		$this->data['isScript'] 			= true;
		$this->load->view("warehouse/template",$this->data);
	}

	public function saveWarehouse(){
		if($this->session->zabeeWarehouseData['userid']){
			$id = $this->session->zabeeWarehouseData['userid'];
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';

		$this->form_validation->set_rules('warehouse_title','Warehouse Title','trim|required');
		$this->form_validation->set_rules('contact_no','Contact No','trim|required');
		$this->form_validation->set_rules('email','Email','trim|required');
		$this->form_validation->set_rules('warehouse_class','Warehouse Class','required');
		$this->form_validation->set_rules('address','Address','trim|required');
		$this->form_validation->set_rules('country_id','Country id','trim|required');
		$this->form_validation->set_rules('province','Province','trim|required');
		$this->form_validation->set_rules('state_id','State','trim|required');
		$this->form_validation->set_rules('city','City','trim|required');
		$this->form_validation->set_rules('zip_code','Zip Code','trim|required');
		
		if($this->form_validation->run() === true){
			extract($this->input->post());
			$today = date('Y-m-d H:i:s');
			$data = array('created_date'			=>$today,
						  'user_id'					=>$id,
						  'warehouse_class_id' 		=>$warehouse_class,
						  'country_id'				=>$country_id,
						  'state_id'				=>($province)?0:$state_id,
						  'warehouse_title'			=>addslashes($warehouse_title),
						  'zip_code'				=>$zip_code,
						  'city'					=>$city,
						  'address'					=>$address,
						  'email'					=>$email,
						  'contact_no'				=>$contact_no,
						  'province'				=>$province
						  );
			$_SESSION['warehouse_title'] = $warehouse_title;
			if($warehouse_id){
				$result = $this->User_Model->updateData($data,DBPREFIX.'_warehouse',array('user_id'=>$id,'warehouse_id'=>$warehouse_id));
				$msg = $this->lang->line('warehous_updated');
			}else{
				$result = $this->User_Model->saveData($data,DBPREFIX.'_warehouse');
				$this->session->userdata['zabeeWarehouseData']['warehouse_id'] = $result;
				$this->session->userdata['zabeeWarehouseData']['warehouse_title'] = $warehouse_title;
				$msg = $this->lang->line('warehous_created');
			}
			if($result){
				$this->session->set_flashdata("success",$msg);
				redirect(base_url("warehouse"),"refresh");
			}else{
				$this->session->set_flashdata("error",$this->lang->line('went_wrong'));
				redirect(base_url("warehouse"),"refresh");
			}
		}
	}

	public function updateWarehouse(){
		if($this->session->zabeeWarehouseData['userid']){
			$id = $this->session->zabeeWarehouseData['userid'];
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';

		$this->form_validation->set_rules('store_name','Store Name','trim|required');
		$this->form_validation->set_rules('store_address','Store Address','trim|required');
		$this->form_validation->set_rules('legal_busniess_type','Legal Busniess Type','required');
		$this->form_validation->set_rules('country_id','Country id','trim|required');
		$this->form_validation->set_rules('contact_person','Contact Person','trim|required');
		$this->form_validation->set_rules('contact_phone','Contact Phone','trim|required');
		$this->form_validation->set_rules('contact_email','Contact Email','trim|required');
		$this->form_validation->set_rules('customer_service_phone','Customer Service Phone','trim|required');
		$this->form_validation->set_rules('customer_service_email','Customer Service Email','trim|required');
		if($this->form_validation->run() === true){
			extract($this->input->post());
			$today = date('Y-m-d H:i:s');
			$store_id = strtolower(str_replace(' ','-',$store_name));
			$data = array('updated_date'			=>$today,
						  'seller_id'				=>$id,
						  'store_name' 				=>addslashes($store_name),
						  'store_id'				=>$store_id,
						  'store_address'			=>$store_address,
						  'legal_busniess_type'		=>$legal_busniess_type,
						  'country_id'				=>$country_id,
						  'contact_person'			=>$contact_person,
						  'contact_phone'			=>$contact_phone,
						  'contact_email'			=>$contact_email,
						  'customer_service_phone'	=>$customer_service_phone,
						  'customer_service_email'	=>$customer_service_email);
			$_SESSION['store_name'] 	= $store_name;
			$_SESSION['store_id'] 		= $store_id;
			if($_FILES['profile_image']['name'] !=""){
				$image = $this->uploadImage('./uploads/store_logo/',$_FILES['profile_image']);
				if(!isset($image['original'])){
					$image['original'] = "";
				}else{
					unlink('./uploads/store_logo/'.$pre_store_logo);
				}
				$data['store_logo']	=$image['original'];
			}
			$result = $this->User_Model->updateData($data,DBPREFIX.'_seller_store',array('seller_id'=>$id,'s_id'=>$s_id));
			if($result){
				$this->session->set_flashdata("success",$this->lang->line('pdt_created'));
				$this->RefreshListingPage();	
			}else{
				$this->session->set_flashdata("success",$this->lang->line('pdt_created'));
				$this->RefreshListingPage();	
			}
		}
	}

	public function warehouse_list(){
		$user_id = $this->session->zabeeWarehouseData['userid'];
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Warehouse_Model->getWarehouse($user_id, $search, $offset, $length);
		$action = '<a href="' . base_url('warehouse/update/$1') . '"><i class="fa fa-edit"></i></a>';
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
	}
	
	//-----Inventiry------//
	public function inventory_view(){
		$this->data['page_name'] = 'inventory_view';
		$this->data['Breadcrumb_name'] = $this->lang->line('inventory');
		$this->data['warehouseList'] = $this->Utilz_Model->countries("user_id","1","warehouse_id,warehouse_title",false,"tbl_warehouse");
		$this->data['isScript'] = true;
		$this->load->view("warehouse/template",$this->data);		
	}

	public function editWareHouse(){
		$id 				= $_POST['id'];
		$w_id 				= $_POST['w_id'];
		$resp['success'] 	= 0;
		$resp['result'] 	=  "";
		$Inventorydata 		= $this->Warehouse_Model->warehouse_inventory_update($id,$w_id);
		if($this->session->zabeeWarehouseData['userid']){
			$user_id = $this->session->zabeeWarehouseData['userid'];
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		if($Inventorydata){
			$resp['success'] = 1;
			$resp['result'] = $Inventorydata;
		}
		echo json_encode($resp);
	}

	public function updateQuantityandWarehouseforInventory($inventory_id = ""){
		$post_data = $_POST;
		foreach($post_data as $data){
			$resp = $this->Warehouse_Model->update_quantityandWarehouse($data);
		}
		echo json_encode($resp);
	}

	private function RefreshListingPage($url="")
	{
		if($url){
			redirect($url,"refresh");
		}else{
			redirect(base_url()."warehouse","refresh");
		}		
	}

	public function check_warehouse_exist(){
		extract($this->input->post());
		$result = $this->Warehouse_Model->getData("tbl_warehouse","warehouse_id",array("warehouse_title"=>$warehouse_title,"user_id !="=>$user_id));
		if(empty($result)){
			echo "true";
		}else{
			echo "false";
		}
	}

	public function getWarehouseInventory(){
		if($this->session->zabeeWarehouseData['userid']){
			$user_id = $this->session->zabeeWarehouseData['userid'];
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$usertype 	= $this->session->userdata('user_type');
		$search 	= $this->input->post("sSearch");
		$offset 	= $this->input->post("iDisplayStart");
		$length 	= $this->input->post("iDisplayLength");
		$this->load->library('datatables');
		$this->Warehouse_Model->getWarehouseInventory($search, $offset, $length, $user_id, $usertype);
		$delete_link = "<a href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>Are you sure?</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/product/delete/$1') . "'>I am Sure
						</a> <button class='btn btn-primary po-close'>No</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> Delete Item</a>";
		$action = '<a href="javascript:void(0);" onclick="askUpdate(\'$1\',\'$2\',\'$3\');" class="btn btn-default upd-warehouse"><i class="fa fa-wrench" aria-hidden="true"></i></a>';
		$received = '<a href="javascript:void(0);" onclick="askReceived(\'$1\',\'$2\',this);" class="btn btn-default"><i class="fa fa-truck" aria-hidden="true"></i></a>';
		$this->datatables->add_column("Update", $action, "win.inventory_id,quantity,w_id");
		$this->datatables->add_column("Received", $received, "win.id,win.inventory_id");
		$this->datatables->add_column("is_received",'$1', "win.is_received");
		if($this->session->userdata('user_type') !="1"){
			$this->datatables->unset_column("store_name");
		}else{
			$this->datatables->unset_column("win.received_date");
		}
		$this->datatables->unset_column("win.is_received");
		$this->datatables->unset_column("win.inventory_id");
		$this->datatables->unset_column("w_id");
		$this->datatables->unset_column("win.id");
		echo $this->datatables->generate();
	}
	
	public function notification_status()
	{
		$data = $this->input->post();
		$proceed = true;
		$response = array('status'=>0,'message'=>'Invalid request', 'code'=>'000');
		if(!$this->session->zabeeWarehouseData['userid']){
			$response['message'] 	= $this->lang->line('user_not_logged_in');
			$response['code'] 		= '001';
			$proceed 				= false;
		}
		if($proceed && (!$data['notification_id'] || $data['notification_id'] == '')){
			$response['message'] 	= $this->lang->line('no_notif');
			$response['code'] 		= '002';
			$proceed 				= false;
		}
		if($proceed && (!$data['userid'] == $this->session->zabeeWarehouseData['userid'])){
			$response['message'] 	= $this->lang->line('inv_req');
			$response['code'] 		= '003';
			$proceed 				= false;
		}
		if($proceed){
			$this->Utilz_Model->updateNotificationStatus($data['userid'], $data['usertype'], $data['notification_id']);
			$response['message'] 	= $this->lang->line('updated_success');
			$response['status'] 	= 1;
		}
		echo json_encode($response);
	}

	public function is_received(){
		extract($_POST);
		$return = $this->Warehouse_Model->is_received($id,$inventory_id);
		echo json_encode($return);
	}

	/* PRODUCT */
	public function product_list(){
		$this->data['page_name'] 		= 'product_view';
		$this->data['Breadcrumb_name'] 	= 'Products';
		$this->data['isScript'] 		= true;
		$this->load->view("warehouse/template",$this->data);		
	}

	public function get_product_details(){
		$search 	= $this->input->post('search');
		$request 	= $this->input->post();
		$userid 	= $this->session->zabeeWarehouseData['userid'];
		$data 		= $this->Warehouse_Model->productPipline($search['value'],$request,$userid);
		echo json_encode($data);
	}
}
?>