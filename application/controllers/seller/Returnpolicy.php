<?php
class Returnpolicy extends SecureAccess{
	function __construct(){
		parent::__construct();
		$this->load->model("admin/Returntype_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}		
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
	}	

	public function index()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$returns = $this->Returntype_Model->getUserReturnsByUserId($user_id);
		$returnsDefault = $this->Returntype_Model->checkDefaultByUserId($user_id);
		$this->data['returns'] = null;
		if($returns){
		   $this->data['returns'] =  $returns;
		}
		$this->data['page_name'] 		= 'returnpolicy_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('return_policy');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function add(){
		$post_data = $this->input->post();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->data['return_name_error'] = ""; 
		$this->form_validation->set_rules('return_YesNo','Return Yes or No','required');
		$this->form_validation->set_rules('rma_YesNo','RMA Yes or No','required');
		$this->form_validation->set_rules('percent_fixed','Percent/Fixed','required');
		$this->form_validation->set_rules('returnPeriod','Return Period','trim|required');
		$this->form_validation->set_rules('restockingFee','Restocking Fee','trim|required');
		$this->form_validation->set_rules('returnPolicyName','Return Policy Name','trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
			$resp = $this->Returntype_Model->returnType_add($post_data, $user_id);
				if($resp){
					$this->session->set_flashdata("success",$this->lang->line('rt_added'));
					redirect(base_url('seller/returnpolicy?status=success'));
					die();
				} else {
					$this->session->set_flashdata("return_name_error",$this->lang->line('rt_already_exist'));
				}	
			}
		} else {
			$this->session->set_flashdata("return_name_error","");
		}
		$this->data['page_name'] 		= 'returnpolicy_add';
		$this->data['post_data'] 		= $post_data;
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_return_policy');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);		
	} 
	public function update($return_id = '')
	{
		if($return_id == ''){
			redirect(base_url('seller/returnpolicy?status=invalid_returnpolicy&code=001'));
				die();
		}
		$this->data['return_id'] = $return_id;		
		$post_data = $this->input->post();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		/*Validations and submition*/
		$returns = $this->Returntype_Model->getUserReturnsByUserId($user_id, $return_id);
		if(!$returns){
			redirect(base_url('seller/returnpolicy?status=returnpolicy&code=002'));
			die();
		}
		$return_policy = $returns[0];
		$this->data['return_policy'] =  $return_policy;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->data['return_name_error'] = "";
		$this->form_validation->set_rules('return_YesNo','Return Yes or No','required');
		$this->form_validation->set_rules('rma_YesNo','RMA Yes or No','required');
		$this->form_validation->set_rules('percent_fixed','Percent/Fixed','required');
		$this->form_validation->set_rules('returnPeriod','Return Period','trim|required');
		$this->form_validation->set_rules('restockingFee','Restocking Fee','trim|required');
		$this->form_validation->set_rules('returnPolicyName','Return Policy Name','trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$resp = $this->Returntype_Model->returnType_update($post_data, $user_id, $return_id);
				if($resp){
					$this->session->set_flashdata("success",$this->lang->line('rt_updated'));
					redirect(base_url('seller/returnpolicy?status=success'));
					die();
				} else {
					$this->session->set_flashdata("return_name_error",$this->lang->line('rt_already_exist'));
				}	
			}
		} else {
			$this->session->set_flashdata("return_name_error","");
		}
		$this->data['page_name'] 		= 'returnpolicy_update';
		$this->data['post_data'] 		= $post_data;
		$this->data['Breadcrumb_name'] 	= $this->lang->line('update_rt_policy');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function isDefault($return_id = ''){
		if($return_id == ''){
			redirect(base_url('seller/returnpolicy?status=invalid_returnpolicy&code=001'));
			die();
		}
		$this->data['return_id'] = $return_id;
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Returntype_Model->returnType_forDefault($return_id, $user_id);
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('default_val_succ'));
			redirect(base_url('seller/returnpolicy?status=success'));
			die();
		}	
		$this->data['page_name'] 		= 'returnpolicy_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('return_policy');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function get()
	{
		// if($this->session->userdata('userid')){
		// } else {
		// 	$user_id = $this->session->userdata('admin_id');
		// }
		// $switcher = $this->getChildColData();
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');

		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Returntype_Model->getReturnPolicy($user_id, $search, $offset, $length,"",$user_type);
		echo $this->datatables->generate();
	}
	
	public function delete($return_id = ''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$insert_data = array(
			'active' 		=> 0,
			'is_default' 	=> '0',
			'is_default_2' 	=> '0'
		);
		$resp = $this->Returntype_Model->deleteReturn($return_id, $user_id, $insert_data);
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('rt_deleted'));
			$respo['status'] = 'success';
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}
	
	// public function getChildColData(){
	// 	$user_id = $this->session->userdata('userid');
	// 	$checker = false;
	// 	$childColData = $this->Returntype_Model->getChildReturnData($user_id);
	// 	if($childColData != ""){
	// 		$childColDataexploded = explode(",",$childColData[0]->child_return_ids);
	// 		for($i = 0; $i < count($childColDataexploded); $i++){
	// 			if($childColDataexploded[$i] == $user_id){
	// 				$checker = true;
	// 			}
	// 		}
	// 		return $checker;
	// 	} else {
	// 		return $checker;
	// 	}
	// }
}

?>