<?php  
class Banner extends SecureAccess{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Banner_Model");	
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
	}

	public function index(){
		$this->data['page_name'] 		= 'banner_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('banner');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function form($banner_id=""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$linktype = $this->input->post('linktype');
		if($linktype == 'ProductLink'){
			$this->form_validation->set_rules('Product_Link','Product Link','required');
		}
		if($linktype == 'ExternalLink'){
			$this->form_validation->set_rules('Banner_Link','Banner Link','required');
		}

		$post_data = "";
		$this->data['banner_data'] = "";
		$this->load->model("admin/Category_Model");
		$this->data['categoryData'] 	=  $this->Category_Model->getCat("","category_id,category_name,is_private,parent_category_id",1);
		if($banner_id !=""){
			$bannerEdit = $this->Banner_Model->getBannerByIdForEdit($banner_id);
			$bannersWeGot = $bannerEdit[0];
			$this->data['bannersWeGot'] =  $bannersWeGot; 
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$img = '';
		if(isset($_POST['submitBtn'])){
			unset($_POST['submitBtn']);
			if($this->form_validation->run() === true){
				$post_data = $this->input->post();
				$banner_id = $_POST['banner_id'];
				if($banner_id ==""){
					$resp = $this->Banner_Model->banner_add($post_data, $user_id);
				}
				else{
					$post_data['userid'] = $user_id;
					$resp = $this->Banner_Model->banner_update($post_data, $banner_id);	
				}
				if($resp){
					$this->session->set_flashdata("success","Banner added successfully.");
					redirect(base_url('seller/banner?status=success'));
					die();
				} else {
					$this->session->set_flashdata("brand_name_error","Banner name already exists.");
				}	
			}
		} else {
			$this->session->set_flashdata("brand_name_error","");
		}
		$this->data['page_name'] 	= 'banner_add';
		$this->data['banner_id'] 	= $banner_id;
		if($banner_id !=""){
			$_POST['linktype'] = $bannerEdit[0]->product_link != "" ? "ProductLink" : "ExternalLink";
			$this->data['post_data'] 	= $bannerEdit[0];
			$this->data['bannerlink'] 		= '';
			$this->data['Breadcrumb_name'] = "Update Banner";
		}else{
			$this->data['bannerlink'] 		= '';
			$this->data['Breadcrumb_name'] = "Create Banner";
		}
		$this->data['isScript'] = true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function get()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->library('datatables');	
		$this->Banner_Model->getBanner($user_id);
		$delete_link = "<a class= 'actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/banner/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right" role="menu">
			<li class="pl-2 pb-1"><a href="' . site_url('seller/banner/form/$1') . '" class="actions"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
		$this->datatables->add_column("Actions", $action, "id");
		echo $this->datatables->generate();
	}
	
	public function delete($banner_id = ''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$banner_id = $this->input->post('id');
		$value = $this->input->post('value');
		$resp = $this->Banner_Model->deleteBanner($banner_id, $user_id, $value);	
	}

	public function hard_delete($banner_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Banner_Model->hard_delete_banner($banner_id, $user_id);
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('banner_deleted'));
			$respo['status'] = 'success';
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}

	public function delete_image(){
		$table_name = DBPREFIX."_banners";
		$id 		= $_POST['key'];
		$where 		= array("id"=>$id);
		$data 		= array("banner_image"=>"");
		$this->db->where($where);
		if($this->db->update($table_name,$data)){
			$params = array('filename'=>$_POST['name'],'filetype'=>"brands");
			$upload_server = $this->config->item('media_url').'/file/delete_media';
			$file = $this->Utilz_Model->curlRequest($params, $upload_server, true,false);
			echo json_encode(array("status"=>200,"msg"=>$this->lang->line('image_deleted')));
		}else{
			echo json_encode(array("status"=>0,"msg"=>$this->lang->line('image_cant_delete')));
		}
	}
}
?>