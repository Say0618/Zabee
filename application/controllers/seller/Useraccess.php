<?php 
class Useraccess extends SecureAccess 
{
	function __construct()
	{
		parent::__construct();	
		//$this->load->library("customtable_lib");
		$this->load->model("admin/Usertype_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
	}

	public function index()
	{
		$this->data['oObj'] 	= $this;		
		$this->data['modules'] 	= $this->Usertype_Model->getAllmodules();
		$userData 				= $this->Usertype_Model->getadminUsers();
		$headings = array
		(
			"user_type_name"	=> "User Name",
			"user_type_dpname"	=> "Display Name",
			"allowed_links"		=> "Allowed Modules"
		);		
		$action = array
		(
			"btns"		=>array("edit"),
			"text"		=>array("Edit"),
			"dbcols"	=>array("user_type_id"),
			"link"		=>array(base_url()."seller/useraccess/editusertype/%@$%"),
			"clickable"	=>array("#useredit_modal","")
		);
		$label 							= "User Type and Module Access";
		$this->data['page_name'] 		= 'useraccess_view';
		$this->data['Breadcrumb_name'] 	= 'Users';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);	
	}
	
	function createusertype()
	{
		if(!(empty($_POST)) && $this->validate_usertype($_POST)){
			$postedData = $_POST;
			$postedData ['allowed_modules'] = implode(",",$postedData ['allowed_modules']);
			$this->Usertype_Model->createusertype($postedData);
			redirect(base_url()."seller/useraccess","refresh");
		}else{
			$this->backtologin();
		}
	}
	
	function deleteusertype($user_type_id = "")
	{
		if($user_type_id){
			$this->Usertype_Model->delete_usertype($user_type_id);
			redirect(base_url()."seller/useraccess","refresh");
		}else{
			$this->backtologin();
		}
	}
	
	function editusertype($type_id)
	{
		$data = $this->Usertype_Model->getusertype($type_id);
		echo json_encode(array("status"=>"success","data"=>$data));
	}
	
	function editeduser()
	{
		if(!(empty($_POST)) && $this->validate_usertype($_POST)){
			$postedData = $_POST;
			$postedData ['allowed_modules'] = implode(",",$postedData ['allowed_modules']);
			$this->Usertype_Model->editusertype($postedData);
			redirect(base_url()."seller/useraccess","refresh");
		}else{
			$this->backtologin();
		}
	}
	
	private function validate_usertype($postData)
	{
		return TRUE;
	}
	
	public function get()
	{
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Usertype_Model->getadminUsers2($search, $offset, $length);
		$delete_link = "<a href='javascript:void(0);' onclick='askDelete(\"$1\")' title='<b> Delete</b>' data-content=\"<p>Are you sure?</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/useraccess/deleteusertype/$1') . "'>I am Sure
						</a> <button class='btn btn-primary po-close'>No</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> Delete Item</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li><a href="' . site_url('seller/useraccess/editusertype/$1') . '"><i class="fa fa-edit"></i> Edit </a></li>';
        $action .= '<li class="divider"></li>
			</ul>
		</div></div>';
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
	}
}
?>