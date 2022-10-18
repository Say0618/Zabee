<?php
class Users extends SecureAccess 
{
	function __construct()
	{
		parent::__construct();	
		//$this->load->library("customtable_lib");
		$this->load->model("admin/User_Model");
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
		$data['oObj'] = $this;			
		$userData = $this->User_Model->getAllUsers();
		$headings = array
		(
			"userid"			=>"User ID",		
			"firstname"			=>"First Name",
			"middlename"		=>"Middle Name",
			"lastname"			=>"Last Name",
			"gender"			=>"Gender",
			"dob"				=>"Date Of Birth",
			"email"				=>"Email Address",
			"mobile"			=>"Mobile Number",
			"telephone"			=>"Phone Number",
			"address"			=>"Address",
			"area"				=>"Area",
			"PIN"				=>"PIN",
			"shippingaddress"	=>"Shipping Address",
			"shipping_area"		=>"Shipping Area",			
			"shipping_PIN"		=>"Shipping PIN",
		);
		$label = "Users/Customers Data";
		$this->data['page_name'] 		= 'user_view';
		$this->data['Breadcrumb_name'] 	= 'Users';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);		
	}

	public function get()
	{
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->User_Model->getAllUsers2($search, $offset, $length);
		$delete_link = "<a href='javascript:void(0);' onclick='askDelete(\"$1\")' title='<b> Delete</b>' data-content=\"<p>Are you sure?</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/users/delete/$1') . "'>I am Sure
						</a> <button class='btn btn-primary po-close'>No</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> Delete Item</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li><a href="' . site_url('seller/users/update/$1') . '"><i class="fa fa-edit"></i> Edit </a></li>';
        $action .= '<li class="divider"></li>
				<li>' . $delete_link . '</li>
			</ul>
		</div></div>';
        echo $this->datatables->generate();
	}
	
	public function get_sellers(){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$data = $this->User_Model->get_sellers($search['value'],$request);
		echo json_encode($data);
	}
	
	public function get_buyers(){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$data = $this->User_Model->get_buyers($search['value'],$request);
		echo json_encode($data);
	}
}

?>