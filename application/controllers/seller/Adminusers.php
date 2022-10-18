<?php
class Adminusers extends SecureAccess 
{
	function __construct()
	{
		parent::__construct();	
		//$this->load->library("customtable_lib");
		$this->load->model("admin/adminuser_model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);	
	}

	public function index()
	{
		$this->data['oObj'] = $this;		
		$this->load->model("admin/usertype_model");
		$this->data["user_types"] = $this->usertype_model->getadminUsers();
		//The datatable creation
		$userData = $this->adminuser_model->getbackendUsers();
		$headings = array
		(
			"admin_username"	=> "User Name",
			"admin_password"	=> "Password",			
			"admin_name"		=> "Name",
			"user_type_dpname"	=> "User Type",
			"admin_email"		=> "Email ID",
			"admin_mobile"		=> "Mobile",
			"creator"			=> "Created By",
			"created_date"		=> "Created On"
		);		
		$action = array
		(
			"btns"		=>array("edit","delete"),
			"text"		=>array("Edit","Delete"),
			"dbcols"	=>array("admin_id","admin_id"),
			"link"		=>array(base_url()."seller/adminusers/getuserbyid/%@$%",base_url()."seller/adminusers/deleteuser/%@$%"),
			"clickable"	=>array("#adminusers_modal","")
		);
		$label 							= "Backend Users Data";
		$this->data['page_name'] 		= 'adminusers_view';
		$this->data['Breadcrumb_name'] 	= 'Backend User';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);		
	}
	
	function createadminuser()
	{
		if(!(empty($_POST)) && $this->validate_admin_user($_POST)){
			$postedData 				= $_POST;
			$postedData['creator_id'] 	= $this->userData[0]['admin_id'];
			$postedData['created_date'] = "".$this->cur_date_time;
			$this->adminuser_model->createadminuser($postedData);
			$this->listPage();
		} else {
			$this->backtologin();
		}
	}
	
	function deleteuser($admin_id = "")
	{
		if($admin_id){
			$this->adminuser_model->delete_admin($admin_id);
			$this->listPage();
		} else {
			$this->backtologin();
		}
	}
	
	function getuserbyid($user_id)
	{
		$data = $this->adminuser_model->getbackendUsers($user_id);
		echo json_encode(array("status"=>"success","data"=>$data));
	}
	
	function editeduser()
	{
		if(!(empty($_POST)) && $this->validate_admin_user($_POST)){
			$postedData = $_POST;		
			$this->adminuser_model->editadminuser($postedData);
			$this->listPage();
		} else {
			$this->backtologin();
		}
	}

	private function listPage()
	{
		redirect(base_url()."seller/adminusers","refresh");
	}

	private function validate_admin_user($postData)
	{
		return TRUE;
	}

	public function get()
	{
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->adminuser_model->getbackendUsers2($search, $offset, $length);
		$delete_link = "<a href='javascript:void(0);' onclick='askDelete(\"$1\")' title='<b> Delete</b>' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/adminusers/deleteuser/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li><a href="' . site_url('seller/adminusers/editeduser/$1') . '"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider"></li>
			</ul>
		</div></div>';
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
    }
}
?>