<?php 
class VariantCategories extends SecureAccess 
{
	public $posteddata = "";
	function __construct()
	{
		parent::__construct();			
		$this->load->model("admin/Variant_Category_Model");		
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
		$this->data['page_name'] 		= 'variant_category_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('variant_categories');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}
	
	public function listview()
	{
		$id = $this->uri->segment(4);
		if($id !=""){
			$variantCategoryData =  $this->Variant_Category_Model->getALLParentVariantCatgories($id,'','');
			$variantData =  $this->Variant_Category_Model->getAllVariantlist($id);
			$passData = array('data'=>$variantCategoryData,'variantData'=>$variantData);
			$this->data['user_id'] = ($this->session->userdata('userid'))?$this->session->userdata('userid'):"";
			$this->load->view("admin/admin_unnec", $passData);
			$this->data['page_name'] 		= 'variant_view';
			$this->data['Breadcrumb_name'] 	= $this->lang->line('variants');
			$this->data['isScript'] 		= true;
			$this->load->view("admin/admin_template", $this->data, $passData);				
							
		}else{
			$this->RefreshListingPage();
		}
	}

	public function createcategories()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$post_data = $this->input->post();
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$img = '';
		$this->form_validation->set_rules('variant_category_name', 'Category Name', 'xss_clean|trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$cat = $this->Variant_Category_Model->cat_add($post_data, $user_id);
				if($cat){
					$this->session->set_flashdata("success","Variant Category created successfully.");
					redirect(base_url('seller/variantcategories?status=success'));
					die();
				} else {
					$this->session->set_flashdata("variant_name_error","Variant name already exists.");
				}			
			}
		} else {
			$this->session->set_flashdata("variant_name_error","");
		}
		$this->data['page_name'] 		= 'variant_category_add';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_variant_categories');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function getcategory($category_id)
	{
		if($this->session->userdata('userid')){
			$userid = $this->session->userdata('userid');
			echo json_encode(array("status"=>"success","data"=>$this->Variant_Category_Model->getParentVariantCatgories($userid, $category_id)));
		}else{
			echo json_encode(array("status"=>"success","data"=>$this->Variant_Category_Model->getAllParentVariantCatgories($category_id)));	
		}
	}

	public function getvariant($category_id)
	{
		echo json_encode(array("status"=>"success","data"=>$this->Variant_Category_Model->getVariantlist('','','',' AND v_id='.$category_id)));
	}
	
	public function editedcategories($category_id = "")
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($category_id == ''){
			redirect(base_url('seller/variantcategories?status=invalid_category&code=001'));
			die();
		}
		$this->data['category_id'] = $category_id;
		$post_data = $this->input->post();
		$categoryEdit = $this->Variant_Category_Model->getVariantCategories($category_id); 
		$categoriesWeGot = $categoryEdit[0];
		$this->data['categoriesWeGot'] =  $categoriesWeGot;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('variant_category_name_update', 'Edit Category Name', 'xss_clean|trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$cat = $this->Variant_Category_Model->cat_update($post_data, $user_id, $category_id);
				if($cat){
					$this->session->set_flashdata("success",$this->lang->line('variant_category_updated'));
					redirect(base_url('seller/variantcategories?status=success'));
					die();
				} else {
					$this->session->set_flashdata("variant_name_error",$this->lang->line('variant_category_exist'));
				}	
			}
		} else {
			$this->session->set_flashdata("variant_name_error","");
		}
		$this->data['page_name'] 		= 'variant_category_update';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('upd_variant_category');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function deletecategories($category_id)
	{
		if(isset($this->userData[0]["userid"])){
			$user_id = $this->userData[0]["userid"];
		}else{
			$user_id = $this->userData[0]["admin_id"];
		}
		$deleteUser = array
		(
			"is_active" => "0"
		);
		$this->Variant_Category_Model->deleteCategory($category_id,$deleteUser);
		$this->session->set_flashdata("info", $this->lang->line('variant_category_deleted'));			
		$this->RefreshListingPage();
	}

	public function createvariant()
	{
		if(!(empty($_POST)) && ($_POST['category_func'] == "create")){
			$this->posteddata = $_POST;
			if($this->validatevariant($_POST)){
				$this->arrangeVarinatPostData();
				$this->Variant_Category_Model->insertVariant($this->posteddata);		
				$this->session->set_flashdata("success",$this->lang->line('variantdeleted'));	
			}
			$this->RefreshListingPage();
		}else{
			$this->backtologin();
		}
	}

	public function editedvariant()
	{
		die("here");
		if(!(empty($_POST)) && ($_POST['category_func'] == "edit")){
			$this->posteddata = $_POST;
			if($this->validatevariant($_POST,TRUE)){
				$category_id = $this->posteddata['v_id_1'];
				$this->arrangeVarinatPostData(TRUE);
				$this->Variant_Category_Model->updateVariant($category_id,$this->posteddata);	
				$this->session->set_flashdata("success",$this->lang->line('variantupdated'));			
			}			
			$this->RefreshListingPage();
		}else{
			$this->backtologin();
		}
	}

	public function deletevariant($category_id)
	{
		if(isset($this->userData[0]["userid"])){
			$user_id = $this->userData[0]["userid"];
		}else{
			$user_id = $this->userData[0]["admin_id"];
		}
		$deleteUser = array 
		(
			"is_active" => "0"
		);
		$resp = $this->Variant_Category_Model->deleteVariant($category_id,$deleteUser);
		$result = json_encode($resp);
		if($result == "true"){
			$this->session->set_flashdata("success", $this->lang->line('variantdelete'));
			$respo['status'] = 'success';
		}
		if($result == "false"){
			$this->session->set_flashdata("error", $this->lang->line('variant_cant_update'));
			$respo['status'] = 'error';
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}

	private function RefreshListingPage()
	{
		$actual_link = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
		if($actual_link !=""){
			redirect($actual_link);
		}else{
			redirect(base_url()."admin/variantcategories","refresh");
		}
	}
	
	private function createimageTag($arrData,$rowname,$width = "",$height = "")
	{
		if(!$width && !$height){
			$width = 100;
		}
		if($arrData){
			foreach($arrData as $key=>$arr){		
				$arrData[$key][$rowname] = "
					<a href = '".$arr[$rowname]."&width=500&height=500&type=imgtag' class='cboxElement'>
					<img style = 'height : ".$height."px;width : ".$width."px;' src='".$arr[$rowname]."' class='' />
					</a>";
			}
		}		
		return $arrData;
	}
	
	private function validatecategory($data,$is_edit = "")
	{
		$this->load->helper('security');
		$chkuniq = "";
		if(!$is_edit){
		}
		if($this->posteddata && isset($this->posteddata['category_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$this->form_validation->set_rules('v_cat_title_'.$i, 'Variant Category Name', 'xss_clean|trim|required'.$chkuniq);
				$this->form_validation->set_rules('is_active_'.$i, 'Display Status', 'required');
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

	private function validatevariant($data,$is_edit = "")
	{
		$chkuniq = "";
		if(!$is_edit){
		}
		if($this->posteddata && isset($this->posteddata['category_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$this->form_validation->set_rules('v_title_'.$i, 'Variant Name', 'xss_clean|trim|required'.$chkuniq);
				$this->form_validation->set_rules('is_active_'.$i, 'Display Status', 'required');
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

	private function uploadCategoryFiles($files)
	{
		$config['upload_path'] 	 = './images/uploads/categories/';
		$config['allowed_types'] = 'gif|jpg|png';
		for($i = 1; $i <= $this->posteddata['category_count']; $i++){
			if(isset($files["category_image_".$i])){				
				$config['file_name']  = "category_".strtotime($this->cur_date_time).$this->userData[0]['id'];	
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload("category_image_".$i)){
					$error = array('info' => $this->upload->display_errors());
					$this->session->set_flashdata("error", $this->upload->display_errors());			
					$this->posteddata["category_image_".$i] = "";
				}else{
					$data = array('upload_data' => $this->upload->data());
					$arrImg = array
					(
						"base"	=>"uploads",
						"type"	=>"categories",
						"img"	=>$data['upload_data']['file_name'],
						"width"	=>100,
						"height"=>100
					);
					$img_url = base_url()."custom/images?img=".base64_encode(serialize($arrImg));					
					$this->posteddata["category_image_".$i] = $img_url;
				}				
			}else{
				$this->posteddata["category_image_".$i] = "";
			}			
		}
	}
	
	private function arrangePostData($isUpdate = FALSE)
	{		
		if($this->posteddata && isset($this->posteddata['category_count'])){
			$arrRetval = array();
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$arrRetval[$cnt]['v_cat_title'] = $this->posteddata["v_cat_title_".$i];
				if(isset($this->posteddata["category_image_".$i]) && $this->posteddata["category_image_".$i] != ""){
					$arrRetval[$cnt]['category_image'] = $this->posteddata["category_image_".$i];
				}
				$arrRetval[$cnt]['is_active'] = $this->posteddata["is_active_".$i];
				if($isUpdate){
					$arrRetval[$cnt]['updated_id'] = $this->userData[0]["userid"];
					$arrRetval[$cnt]['updated_date'] = $this->cur_date_time;
				}else{
					$arrRetval[$cnt]['created_id'] = $this->userData[0]["userid"];
					$arrRetval[$cnt]['created_date'] = $this->cur_date_time;
				}				
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		}else{
			$this->backtologin();
		}
	}

	private function arrangeVarinatPostData($isUpdate = FALSE)
	{		
		if($this->posteddata && isset($this->posteddata['category_count'])){
			$arrRetval = array();
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$arrRetval[$cnt]['v_title'] = $this->posteddata["v_title_".$i];
				$arrRetval[$cnt]['v_cat_id'] = $this->posteddata["v_cat_id_".$i];
				if(isset($this->posteddata["category_image_".$i]) && $this->posteddata["category_image_".$i] != ""){
					$arrRetval[$cnt]['category_image'] = $this->posteddata["category_image_".$i];
				}
				$arrRetval[$cnt]['is_active'] = $this->posteddata["is_active_".$i];
				if($isUpdate){
					$arrRetval[$cnt]['updated_id']   = $this->userData[0]["userid"];
					$arrRetval[$cnt]['updated_date'] = $this->cur_date_time;
				}else{
					$arrRetval[$cnt]['created_id']   = $this->userData[0]["userid"];
					$arrRetval[$cnt]['created_date'] = $this->cur_date_time;
				}				
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		}else{
			$this->backtologin();
		}
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
		$this->Variant_Category_Model->getParentVariantCatgories2($user_id, $search, $offset, $length);
		$delete_link = "<a class='actions pl-2 pt-1' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
					<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/variantcategories/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
		$action = '<div class="text-center"><div class="btn-group text-left">'
			. '<button type="button" class="btn btn-secondary btn-xs btn-primary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right p-2" role="menu">
			<li ><a href="' . site_url('seller/variantcategories/editedcategories/$1') . '" class="actions pl-2 pb-1"><i class="fa fa-edit"></i>'.$this->lang->line('edit').'</a></li>';
		$action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li>' . $delete_link . '</li>
			</ul>
		</div></div>';
		$this->datatables->add_column("Actions", $action, "id");
		echo $this->datatables->generate();
	}

	public function variant_add($var_id = ''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else { 
			$user_id = $this->session->userdata('admin_id');
		}
		$variantTitle = $this->Variant_Category_Model->variantCategoryTitleByVariantCatId($var_id);
		$post_data    = $this->input->post();
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$img = '';
		$this->form_validation->set_rules('variant_name', 'Variant Name', 'xss_clean|trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$var = $this->Variant_Category_Model->variant_add($post_data, $user_id, $var_id);
				if($var){
					$this->session->set_flashdata("success",$this->lang->line('variantdeleted'));
					redirect(base_url('seller/variantcategories/listview/'.$var_id.'?status=success'));
					die();
				} else {
					$this->session->set_flashdata("variant_name_error",$this->lang->line('variant_category_exist'));
				}	
			}
		} else {
			$this->session->set_flashdata("variant_name_error","");
		}
		$this->data['page_name'] 		= 'variant_add';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_variant');
		$this->data['isScript'] 		= true;
		$this->data['v_title'] 			= $variantTitle[0]->v_cat_title;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function variant_update($var_id = ''){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$variant_data 				= $this->Variant_Category_Model->selectVariants($var_id);
		$variantWeGot 				= $variant_data[0];
		$this->data['variantWeGot'] =  $variantWeGot;
		$variantParent 				= $variant_data[0]->v_cat_id;
		$post_data 					= $this->input->post();
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$img = '';
		$this->form_validation->set_rules('variant_name', 'Variant Name', 'xss_clean|trim|required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$var = $this->Variant_Category_Model->variant_update($post_data, $user_id, $var_id, $variantParent);
				if($var){
					$this->session->set_flashdata("success",$this->lang->line('variantupdated'));
					redirect(base_url('seller/variantcategories/listview/'.$variantParent.'?status=success'));
					die();
				} else {
					$this->session->set_flashdata("variant_name_error",$this->lang->line('variant_category_exist'));
				}	
			}
		} else {
			$this->session->set_flashdata("variant_name_error","");
		}
		$this->data['page_name'] 		= 'variant_update';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_variant');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function delete(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
			
		} else {
			$user_id = $this->session->userdata('admin_id');

		}
		echo $user_id;
		$category_id = $this->input->post('id'); 
		$value = $this->input->post('value');
		$resp = $this->Variant_Category_Model->deleteVariantCat($category_id, $user_id, $value);
	}

	 public function countActiveVariants(){
		extract($this->input->post());
		$resp = $this->Variant_Category_Model->countVariants($id);
		echo json_encode($resp); 
	}

	public function deleteVar(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
			
		} else {
			$user_id = $this->session->userdata('admin_id');

		}
		$category_id = $this->input->post('id'); 
		$value = $this->input->post('value');
		$resp = $this->Variant_Category_Model->deleteVar($category_id, $user_id, $value);
	}

	public function hard_delete($category_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$respo = $this->Variant_Category_Model->hard_delete_variantcategory($category_id, $user_id);
		if($respo == "failed"){
			$this->session->set_flashdata("error",$this->lang->line('variant_cant_update'));
			$resp['status'] = 'error';
		} else if($respo == "success") {
			$this->session->set_flashdata("success",$this->lang->line('variant_category_deleted'));
			$resp['status'] = 'success';
		}
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}
}
?>