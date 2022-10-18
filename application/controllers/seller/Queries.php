<?php  
class Queries extends SecureAccess{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Requests_Model");	
		$this->load->model("User_Model");	
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);		
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
		$this->lang->load('english', 'english');
	}

	public function index(){
		$this->data['page_name'] 		= 'searched_query';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('Searched_req');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	// public function add(){
    //     $post_data = $this->input->post();
	// 	if($this->session->userdata('userid')){
	// 		$user_id = $this->session->userdata('userid');
	// 	} else {
	// 		$user_id = $this->session->userdata('admin_id');
	// 	}
	// 	$img = '';
	// 	$this->load->helper(array('form','url'));
	// 	$this->load->library('form_validation');
    //     $this->data['error'] = '';
    //     $this->form_validation->set_rules('request_type','request_type','trim|required');
    //     $this->form_validation->set_rules('request_name','request_name','trim|required');
    //     $this->form_validation->set_rules('info','info','trim|required');
	// 	if($this->form_validation->run() === true){
    //         $resp = $this->Requests_Model->requests_add($post_data, $user_id);
    //         if($resp){
	// 			$this->session->set_flashdata("success",$this->lang->line('addrequest_success'));
	// 			$this->sendNotification();
	// 			redirect(base_url('seller/requests?status=success'));
	// 			die();
    //         }
    //     }
	// 	$this->data['page_name'] 		= 'request_form';
	// 	$this->data['Breadcrumb_name'] 	= $this->lang->line('add_request');
	// 	$this->data['isScript'] 		= true;
	// 	$this->load->view("admin/admin_template", $this->data);	
    // }
    
	public function update($banner_id = ""){
	
    }
    
	public function get_searched_query()
	{
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');
        $this->load->library('datatables');	
		$this->Requests_Model->get_searched_query($user_id, $user_type);
		$delete_link = "<a class= 'actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/banner/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right" role="menu">
			<li class="pl-2 pb-1"><a href="' . site_url('seller/banner/requests/$1') . '" class="actions"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
        $this->datatables->add_column("Actions", $action, "id");
		echo $this->datatables->generate();
    }

	public function approveRequest(){
		$post_data 	= $this->input->post();
		$status 	= $this->Requests_Model->approve_request($post_data);
		echo json_encode($status);
	}
	
	public function sendNotification(){
		$user_id 	= $this->session->userdata('userid');
		$Username 	= $this->User_Model->getUserName($user_id);
		$action 	= $this->lang->line('req_from').$Username->firstname.$this->lang->line('added_req');
		$not_type 	= '0';
		$from 		= $user_id;
		$to 		= "1";
		$user_type 	= "0";
		$status 	= saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
		print_r($status);
		return json_encode($status);
	}
}
?>