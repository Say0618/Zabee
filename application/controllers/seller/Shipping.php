<?php
class Shipping extends SecureAccess 
{
	public $posteddata = "";
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Shipping_Model");	
		$this->data = array(
			'page_name' 		=> 'shipping_view',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
		if($this->session->userdata('userid')){
			$this->data['user_id'] = $this->session->userdata('userid');
		}else{
			$this->data['user_id'] = $this->session->userdata('admin_id');	
		}
	}

	public function index(){
		$this->data['page_name'] 		= 'shipping_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('shipping');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	} 

	public function getShipping(){
		$search = $this->input->post('search');
		$request = $this->input->post();
		$data = $this->Shipping_Model->getShipping($search['value'],$request,$this->data['user_id']);
		echo json_encode($data);
	}

	public function createshipping()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$post_data = $this->input->post();
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$img = '';
		$this->form_validation->set_rules('title', 'Title', 'xss_clean|trim|required');
		$this->form_validation->set_rules('price', 'Base Price', 'xss_clean|trim|required');
		$this->form_validation->set_rules('basedOn', 'Price Base on', 'xss_clean|trim|required');
		$baseType = $this->input->post('basedOn');
		switch($baseType){
			case 'weight':
				$this->form_validation->set_rules('base_weight', 'Base Weight', 'xss_clean|trim|required');
				$this->form_validation->set_rules('weight_unit', 'Weight Unit', 'xss_clean|trim|required');
			break;
			case 'dimension':
				$this->form_validation->set_rules('base_length', 'Base length', 'xss_clean|trim|required');
				$this->form_validation->set_rules('base_width', 'Base Width', 'xss_clean|trim|required');
				$this->form_validation->set_rules('base_depth', 'Base Depth', 'xss_clean|trim|required');
				$this->form_validation->set_rules('dimension_unit', 'Dimension Unit', 'xss_clean|trim|required');
			break;
		}
		$this->form_validation->set_rules('inc_unit', 'Incremental Unit', 'xss_clean|trim');
		$this->form_validation->set_rules('inc_price', 'Incremental Price', 'xss_clean|trim');
		$this->form_validation->set_rules('free_after', 'Free Shipping After', 'xss_clean|trim');
		$this->form_validation->set_rules('minimum_days', 'Minimum Days', 'xss_clean|trim|required');
		$this->form_validation->set_rules('maximum_days', 'Maximum Days', 'xss_clean|trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$post_data['user_id'] = $this->data['user_id'];
				$post_data['user_type'] = $this->session->userdata['user_type'];
				$cat = $this->Shipping_Model->shipping_add($post_data, $user_id);
				if($cat){
					$this->session->set_flashdata("success",$this->lang->line('created_shipping'));
					redirect(base_url('seller/shipping/?status=success'));
					die();
				} else {
					$this->session->set_flashdata("shipping_name_error",$this->lang->line('already_exist_shipping'));
				}	
			}
		} else {
			$this->session->set_flashdata("shipping_name_error","");
		}
		$this->data['img'] 				= $img;
		$this->data['page_name'] 		= 'createshipping';
		$this->data['post_data'] 		= $post_data;
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_shipping');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function update_shipping_stauts(){
		extract($this->input->post());
		$user_id = $this->session->userdata('userid');
		$search = $this->Shipping_Model->checkShippingCount($shipping_id,$user_id);
		// echo"<pre>"; print_r($search); die();
		if($search['ship'] != "" && $value == "0"){
			$result = array("status"=>"2", "data"=>$search);
		}else{
			$result = $this->Shipping_Model->updateShippingStauts($shipping_id,$value,$user_id); 
		}
		echo json_encode($result);
	}

	public function delete_shipping_method(){
		extract($this->input->post());
		$respo['status'] = "error";
		$result = $this->Shipping_Model->delete_shipping($id,$userid); 
		if($result){
			$this->session->set_flashdata("success",$this->lang->line('shipping_deleted'));
			$respo['status'] = 'success';
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}

	private function RefreshListingPage()
	{
		redirect(base_url()."seller/shipping","refresh");
	}

	public function delete(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$category_id = $this->input->post('id');
		$value 		 = $this->input->post('value');
		$resp 		 = $this->Shipping_Model->delete_category($category_id, $user_id, $value);
	}

	public function hard_delete($category_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Shipping_Model->hard_delete_category($category_id, $user_id);
	}

	public function shipping_edit($id){
		$uri = $this->uri->segment_array();
		$shipping_id = end($uri);
		$post_data = $this->input->post();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->form_validation->set_rules('title', 'Title', 'xss_clean|trim|required');
		$this->form_validation->set_rules('price', 'Base Price', 'xss_clean|trim|required');
		$this->form_validation->set_rules('basedOn', 'Price Base on', 'xss_clean|trim|required');
		$baseType = $this->input->post('basedOn');
		switch($baseType){
			case 'weight':
				$this->form_validation->set_rules('base_weight', 'Base Weight', 'xss_clean|trim|required');
				$this->form_validation->set_rules('weight_unit', 'Weight Unit', 'xss_clean|trim|required');
			break;
			case 'dimension':
				$this->form_validation->set_rules('base_length', 'Base length', 'xss_clean|trim|required');
				$this->form_validation->set_rules('base_width', 'Base Width', 'xss_clean|trim|required');
				$this->form_validation->set_rules('base_depth', 'Base Depth', 'xss_clean|trim|required');
				$this->form_validation->set_rules('dimension_unit', 'Dimension Unit', 'xss_clean|trim|required');
			break;
		}
		$this->form_validation->set_rules('inc_unit', 'Incremental Unit', 'xss_clean|trim');
		$this->form_validation->set_rules('inc_price', 'Incremental Price', 'xss_clean|trim');
		$this->form_validation->set_rules('free_after', 'Free Shipping After', 'xss_clean|trim');
		$this->form_validation->set_rules('minimum_days', 'Minimum Days', 'xss_clean|trim|required');
		$this->form_validation->set_rules('maximum_days', 'Maximum Days', 'xss_clean|trim|required');

		
		if($this->form_validation->run() === true){
			//echo '<pre>';print_r($post_data);echo '</pre>';die();
			$post_data['user_type'] = $this->session->userdata['user_type'];
			$resp = $this->Shipping_Model->save_edit_shipping($post_data,$id,$user_id);
			if($resp == 1){
				$this->session->set_flashdata("success",$this->lang->line('updated_shipping'));
				redirect(base_url('seller/shipping?status=success'));
				die();
			} else if($resp == 0){
				$this->session->set_flashdata("shipping_name_error",$this->lang->line('already_exist_shipping'));
			} else {
				redirect(base_url('seller/shipping?status=shipping&code=002'));
			}
		}
		$shipping_data = $this->Shipping_Model->getShippingbyId($id);
		if(!$shipping_data){
			redirect(base_url('seller/shipping?status=shipping&code=002'));
				die();
		}
		$this->data['shippingId'] 		= $id; 
		$this->data['shippingData'] 	= $shipping_data[0]; 
		$this->data['page_name'] 		= 'shipping_edit';
		$this->data['post_data'] 		= $post_data;
		$this->data['Breadcrumb_name'] 	= $this->lang->line('edit_shipping');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}
	
	public function askdelete(){
		$id = $this->input->post('id');
		$s_delete = $this->Shipping_Model->shipping_delete($id);
			if($s_delete){
				$resp['success'] = true;
				echo json_encode($resp, JSON_UNESCAPED_UNICODE);
			}		
	}

	public function transferShipping(){
		// var_dump($this->input->post()); die();
		$transfer = $this->Shipping_Model->transferShipping($this->input->post('current_ship'), $this->input->post('ship_id'), $this->session->userdata('userid'));
		if($transfer['status'] == "0"){
			$this->session->set_flashdata("transfered","Inventory transfered successful");
			redirect(base_url("seller/shipping"));
		}else{
			$this->session->set_flashdata("Transfered","Inventory transfered Failed");
			redirect(base_url("seller/shipping"));
		}
	}
}
?>