<?php
class Discount extends SecureAccess{
	function __construct(){
		parent::__construct();
		$this->load->model("admin/Policies_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
	}

	public function index(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$policies = $this->Policies_Model->getUserPoliciesByUserId($user_id);
		$this->data['policies'] = null;
		if($policies){
		   $this->data['policies'] =  $policies;
		}
		$this->data['page_name'] 		= 'discount_view';
		$this->data['Breadcrumb_name']  = $this->lang->line('discount_policy');
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
		$this->form_validation->set_rules('discount_title','Title','trim|required');
		$this->form_validation->set_rules('fromDate','from date','required');
		$this->form_validation->set_rules('toDate','to date','required');
		$this->form_validation->set_rules('FixedorPercent','Fixed or Percent','required');
		$this->form_validation->set_rules('valueOfPercentOrFixed','value','trim|required');
		if($this->form_validation->run() === true){
			$resp = $this->Policies_Model->policies_add($post_data, $user_id);
			if($resp){
				$this->session->set_flashdata("success",$this->lang->line('discount_policy_added'));
				redirect(base_url('seller/discount?status=success'));
				die();
			}	
		}
		$this->data['page_name'] 		= 'discount_add';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_discount_policy');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);			
	}

	public function update($policy_id=''){
		if($policy_id == ''){
			redirect(base_url('seller/discount?status=invalid_policy&code=001'));
			die();
		}
		$this->data['policy_id'] = $policy_id;
		$data['oObj'] = $this;
		$post_data = $this->input->post();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$policies = $this->Policies_Model->getUserPoliciesByUserId($user_id, $policy_id);
		if(!$policies){
			redirect(base_url('seller/discount?status=policies&code=002'));
			die();
		}
		$policies_update = $policies[0];
		$this->data['policies_update'] =  $policies_update;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('title','title','required');
		$this->form_validation->set_rules('fromDate','from date','required');
		$this->form_validation->set_rules('toDate','to date','required');
		$this->form_validation->set_rules('FixedorPercent','Fixed or Percent','required');
		$this->form_validation->set_rules('valueOfPercentOrFixed','value','trim|required');
		if($this->form_validation->run() === true){
			$resp = $this->Policies_Model->policies_update($post_data, $user_id, $policy_id);
			if($resp){
				$this->session->set_flashdata("success",$this->lang->line('policy_updated'));
				redirect(base_url('seller/discount?status=success'));
				die();
			}	
		}
		$this->data['page_name'] 		= 'discount_update';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('update_discount_policy');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function get()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Policies_Model->getUserPolicies($user_id, $search, $offset, $length);
        echo $this->datatables->generate();
	}

	public function apply_all_products(){
		$respo = array();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$policy_id = $this->input->post('id'); 
		$resp = $this->Policies_Model->applyDiscount($policy_id, $user_id);
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('discount_policy_applied'));
			$respo['status'] = 'success';
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}

	public function delete($policy_id=''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$policy_id = $this->input->post('id');
		$value = $this->input->post('value');
		$resp = $this->Policies_Model->deleteDiscount($policy_id, $user_id, $value);
	}
	
	public function hard_delete($policy_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Policies_Model->hard_delete_discount($policy_id, $user_id);
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('discount_policy_deleted'));
			$respo['status'] = 'success';
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}

	public function voucher(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->data['page_name'] 		= 'discount_coupon_view';
		$this->data['Breadcrumb_name']  = $this->lang->line('discount_coupon');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function get_vouchers()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Policies_Model->getDiscountCoupons($user_id, '', $search, $offset, $length);
		$delete_link = "<a class='actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/discount/voucher/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i> ".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right" role="menu">
			<li class="pl-2 pt-1"><a class="actions" href="' . site_url('seller/discount/voucher/update/$1') . '"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
		$action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
        $this->datatables->add_column("Actions", $action, "dv.id");
        //$this->datatables->unset_column("dv.id");
        echo $this->datatables->generate();
	}
	
	public function voucher_add(){
		$post_data = $this->input->post();
		if($this->session->userdata['userid']){
			$user_id = $this->session->userdata('userid');
		}
		$isAdmin = ($this->session->userdata['user_type'] == 1)?true:false;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('voucher_code',$this->lang->line('dicount_coupon'),'trim|required|callback_checkVoucher');
		$this->form_validation->set_rules('discount_title',$this->lang->line('title'),'trim|required');
		$this->form_validation->set_rules('discount_limit',$this->lang->line('limit'),'trim|required');
		$this->form_validation->set_rules('fromDate',$this->lang->line('valid_from'),'required');
		$this->form_validation->set_rules('toDate',$this->lang->line('valid_to'),'required');
		$this->form_validation->set_rules('discount_type',$this->lang->line('discount_type'),'required');
		$this->form_validation->set_rules('discount_value',$this->lang->line('discount_value'),'required');
		$this->form_validation->set_rules('min_price',$this->lang->line('min_amount'),'trim|required');
		$this->form_validation->set_rules('max_price',$this->lang->line('max_amount'),'trim|required');
		if($this->form_validation->run() === true){
			$resp = $this->Policies_Model->voucher_add($post_data, $user_id, $isAdmin);
			if($resp){
				$this->session->set_flashdata("success",$this->lang->line('discount_policy_added'));
				redirect(base_url('seller/discount/voucher?status=success'));
				die();
			}	
		}
		$this->data['page_name'] 		= 'discount_coupon_add';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_discount_coupon');
		$this->data['isScript'] 		= true;
		$this->data['isAdmin'] 			= $isAdmin;
		$this->load->view("admin/admin_template",$this->data);			
	}
	
	public function delete_voucher($voucher_id=''){
		$response = array('status'=>0, 'message'=>'Error');
		if($this->session->userdata['userid']){
			$user_id = $this->session->userdata('userid');
		}
		$isAdmin = ($this->session->userdata['user_type'] == 1)?true:false;
		if($voucher_id != ''){
			if($this->Policies_Model->deleteDiscountVoucher($voucher_id, $user_id)){
				$response = array('status'=>1, 'message'=>'OK');
			} else {
				$response['message'] = 'Fail in deleting';
			}
		}
		echo json_encode($response );
	}
	
	public function voucher_edit($voucher_id = ''){
		if($voucher_id == ''){
			redirect(base_url('seller/discount/voucher?status=invalid'));
		}
		$post_data = $this->input->post();
		if($this->session->userdata['userid']){
			$user_id = $this->session->userdata('userid');
		}
		$isAdmin = ($this->session->userdata['user_type'] == 1)?true:false;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		
		//$this->form_validation->set_rules('voucher_code',$this->lang->line('dicount_coupon'),'trim|required|callback_checkVoucher');
		$this->form_validation->set_rules('discount_title',$this->lang->line('title'),'trim|required');
		$this->form_validation->set_rules('discount_limit',$this->lang->line('limit'),'trim|required');
		$this->form_validation->set_rules('fromDate',$this->lang->line('valid_from'),'required');
		$this->form_validation->set_rules('toDate',$this->lang->line('valid_to'),'required');
		$this->form_validation->set_rules('discount_type',$this->lang->line('discount_type'),'required');
		$this->form_validation->set_rules('discount_value',$this->lang->line('discount_value'),'required');
		$this->form_validation->set_rules('min_price',$this->lang->line('min_amount'),'trim|required');
		$this->form_validation->set_rules('max_price',$this->lang->line('max_amount'),'trim|required');
		if($this->form_validation->run() === true){
			$resp = $this->Policies_Model->voucher_update($post_data, $user_id, $voucher_id, $isAdmin);
			if($resp){
				$this->session->set_flashdata("success",$this->lang->line('discount_policy_updated'));
				redirect(base_url('seller/discount/voucher?status=success'));
				die();
			}	
		}
		$vouchers = array();
		$vouchers = $this->Policies_Model->getVoucher($user_id, $voucher_id);
		if(count($vouchers) == 0){
			redirect(base_url('seller/discount/voucher?status=invalid'));
		}
		$this->data['voucher'] 		= $vouchers[0];
		//echo '<pre>';print_r($this->data['voucher']);echo '</pre>';die();
		$this->data['page_name'] 		= 'discount_coupon_edit';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('update_discount_coupon');
		$this->data['isScript'] 		= true;
		$this->data['isAdmin'] 			= $isAdmin;
		$this->load->view("admin/admin_template",$this->data);			
	}
	
	public function get_data(){
		$result = array();
		$term = $this->input->get('term');
		$apply_on = $this->input->get('apply_on');
		if($this->session->userdata['userid']){
			$user_id = $this->session->userdata('userid');
			$user_type = $this->session->userdata('user_type');
			$isAdmin = ($this->session->userdata['user_type'] == 1)?true:false;
		}
		if(isset($_POST['term'])){
			$term = $_POST['term'];
		}
		if(isset($_POST['apply_on'])){
			$apply_on = $_POST['apply_on'];
		}
		if($term != '' && $apply_on != ''){
			$result = $this->Policies_Model->getDiscountItemsData($term, $apply_on, $user_id, $user_type);
		}
		echo json_encode($result);
	}
	
	public function generate_code($length = 10){
		$response = array('status'=>0, 'message'=>'Error', 'code'=>'');
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		$response['status'] = 1;
		$response['message'] = 'OK';
		$response['code'] = $randomString;
		echo json_encode($response);
	}
	public function check_code(){
		$response = array('status'=>0, 'message'=>'Error');
		$code = $this->input->post('code');
		$check = $this->Policies_Model->checkVoucher($code);
		if($check){
			$response['status'] = 1;
			$response['message'] = 'OK';
		}
		echo json_encode($response);
	}
	
	public function checkVoucher($code){
		$check = $this->Policies_Model->checkVoucher($code);
		if(!$check){
			$this->form_validation->set_message('checkVoucher', 'Voucher already exist.');
			return FALSE;
		} else {
			return TRUE;
		}
	}
}
?>