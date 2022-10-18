<?php
class Vendors extends SecureAccess 
{
	public $posteddata = "";
	public $areas = "";
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Vendors_Model");
		$this->load->model("admin/Areas_Model");
		$this->areas = $this->Areas_Model->getAreas("","area_name,area_pin");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}			
	}

	public function index()
	{
		$this->listVendors();
	}
	
	private function listVendors()
	{
		$data['oObj'] = $this;		
		$vendorsData =  $this->Vendors_Model->getVendors();
		$headings = array(
			"vendor_name"		=>"Name",
			"vendor_address"	=> "Address",
			"vendor_pin"		=> "PINCODE",
			"vendor_mobile"		=> "Mobile Numbers",
			"vendor_phone"		=> "Phone Numbers",
			"vendor_email"		=>"Email Id",
			"vendor_area"		=> "Areas",
			"name1"				=>"Created By",
			"created_date"		=>"Created On",
			"name2"				=>"Updated By",
			"updated_date"		=>"Updated On"
			);		
		$action = array
		(
			"btns"		=>array("edit","delete"),
			"text"		=>array("Edit","Delete"),
			"dbcols"	=>array("vendor_id","vendor_id"),
			"link"		=>array(base_url()."seller/vendors/getvendors/%@$%",base_url()."seller/vendors/deletevendors/%@$%"),
			"clickable" =>array("#vendorsmodal","")
		);
		$label = "Vendors List";
		$this->data['page_name'] = 'underconstruction';
		$this->data['Breadcrumb_name'] = 'Vendors';
		$this->data['isScript'] = false;
		$this->load->view("admin/admin_template",$this->data);		
	}
	
	public function createvendors()
	{
		if(!(empty($_POST)) && ($_POST['vendors_func'] == "create")){
			$this->posteddata = $_POST;
			if($this->validatevendors($_POST)){							
				$this->arrangePostData();
				$this->Vendors_Model->insertvendors($this->posteddata);		
				$this->session->set_flashdata("success","<strong>Vendors created successfully.</strong>");	
			}
			$this->RefreshListingPage();
		} else {
			$this->backtologin();
		}
	}
	
	public function getvendors($vendor_id)
	{
		echo json_encode(array("status"=>"success","data"=>$this->Vendors_Model->getVendors($vendor_id)));
	}
	
	public function editedvendors()
	{
		if(!(empty($_POST)) && ($_POST['vendors_func'] == "edit")){
			$this->posteddata = $_POST;
			if($this->validatevendors($_POST,TRUE)){				
				$vendor_id = $this->posteddata['vendor_id_1'];				
				$this->arrangePostData(TRUE);
				$this->Vendors_Model->updatevendors($vendor_id,$this->posteddata);	
				$this->session->set_flashdata("success","Vendors updated successfully.");			
			}			
			$this->RefreshListingPage();
		} else {
			$this->backtologin();
		}
	}
	
	public function deletevendors($vendor_id)
	{
		$deleteUser = array
		(
			"deleted_id"	=>$this->userData[0]["admin_id"],
			"deleted_date"	=>$this->cur_date_time
		);
		$this->Vendors_Model->deletevendors($vendor_id,$deleteUser);
		$this->session->set_flashdata("info", "Vendor deleted successfully.");			
		$this->RefreshListingPage();
	}
	
	private function RefreshListingPage()
	{
		redirect(base_url()."admin/vendors","refresh");
	}	
	
	private function validatevendors($data,$is_edit = "")
	{
		return TRUE;
		$chkuniq = "";
		if(!$is_edit){
			 $chkuniq = '|is_unique['.DBPREFIX.'_vendors.vendor_name]';
		}
		if($this->posteddata && isset($this->posteddata['vendors_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['vendors_count']; $i++)			{
				$this->form_validation->set_rules('vendor_name_'.$i, 'vendors Name', 'xss_clean|trim|required'.$chkuniq);
				$this->form_validation->set_rules('display_status_'.$i, 'Display Status', 'required');
			}
		}		
		if ($this->form_validation->run() == FALSE){
			$errors = validation_errors();
			$this->session->set_flashdata("error", $errors);			
			return FALSE;
		}
		else			
		return TRUE;
	}
	
	private function arrangePostData($isUpdate = FALSE)
	{		
		if($this->posteddata && isset($this->posteddata['vendors_count'])){
		$this->posteddata['vendors_count'] = 1;
			$arrRetval = array();
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['vendors_count']; $i++)			{
				$arrRetval[$cnt]['vendor_name'] 	= $this->posteddata["vendor_name_".$i];
				$arrRetval[$cnt]['vendor_address'] 	= $this->posteddata["vendor_address_".$i];
				$arrRetval[$cnt]['vendor_pin'] 		= $this->posteddata["vendor_pin_".$i];
				$arrRetval[$cnt]['vendor_mobile'] 	= $this->posteddata["vendor_mobile_".$i];
				$arrRetval[$cnt]['vendor_phone'] 	= $this->posteddata["vendor_phone_".$i];
				$arrRetval[$cnt]['vendor_area'] 	= implode(",",$this->posteddata["vendor_area_".$i]);
				$arrRetval[$cnt]['vendor_email'] 	= $this->posteddata["vendor_email_".$i];
								
				if($isUpdate){
					$arrRetval[$cnt]['updated_id'] 		= $this->userData[0]["admin_id"];
					$arrRetval[$cnt]['updated_date']	= $this->cur_date_time;
				}else{
					$arrRetval[$cnt]['created_id'] 		= $this->userData[0]["admin_id"];
					$arrRetval[$cnt]['created_date'] 	= $this->cur_date_time;
				}				
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		}else{
			$this->backtologin();
		}
	}
}
?>