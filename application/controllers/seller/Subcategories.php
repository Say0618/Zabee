<?php
class Subcategories extends SecureAccess 
{
	public $posteddata = "";
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Category_Model");	
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications'		=> $this->notifications
		);
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;		
	}

	public function index()
	{
		$this->data['page_name'] 		= 'subcategory_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('subcategory');
		$this->data['isScript'] 		= true;
		$this->data['user_id'] 			= ($this->session->userdata('userid'))?$this->session->userdata('userid'):"";
		$this->data['parentCategories'] = $this->Category_Model->getCat("","",1);
		$this->load->view("admin/admin_template", $this->data);	
	}

	public function updateProductSubCategory(){
		$cat_id = $_POST['old_cat_id'];
		foreach($_POST['product_id'] as $index=>$product){
			$data[] = $product." - ".$_POST['subcategory_id'][$index]."<br />";	 
		}
		$result = $this->Category_Model->catChange($data);
		if($result){
				if($this->delete($cat_id)){
					redirect(base_url('seller/subcategories?status=success'));
				}
			}
	}

	public function	getAllSubCategories(){
		$currentId = $_POST['id'];
		$categoryData =  $this->Category_Model->allsubCategories($currentId);
		echo json_encode(array('data'=>$categoryData));
	}

	private function listsubCategories()
	{				
		$this->data['user_id'] = ($this->session->userdata('userid'))?$this->session->userdata('userid'):"";
		$this->data['parentCategories'] = $this->Category_Model->getCat("","","1");
		$categoryData =  $this->Category_Model->getChildCategories();
		$categoryData = $this->createimageTag($categoryData,"category_image");
		$this->data['categoryData'] 	= $categoryData;
		$this->data['page_name'] 		= 'subcategory_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('subcategory');
		$this->data['isScript'] 		= false;
		$this->load->view("admin/admin_template", $this->data);		
	}
	
	public function createsubcategories()
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
		$this->form_validation->set_rules('subcategory_name','Sub Category Name','required');
		$this->form_validation->set_rules('subdisplay_status','Sub Category Display Status','required');
		$this->form_validation->set_rules('parent_category_id','Sub Category Parent Category','required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$categoryPath = 'uploads/categories/';
				if(isset($_FILES["profile_image"])){
					if($_FILES["profile_image"]['name'] != ""){
						$img = (isset($_FILES["profile_image"]))?$this->do_upload_directly($categoryPath, $_FILES["profile_image"], 'categories'):'';
						$img = $img['original'];
						$post_data['img'] = $img;
					}
				} else {
					$post_data['img'] = "";
				}
				$resp = $this->Category_Model->subCategory_add($post_data, $user_id);
				if($resp){
					$this->session->set_flashdata("success","Sub-Categories added successfully.");
					redirect(base_url('seller/subcategories?status=success'));
					die();
				} else {
					$this->session->set_flashdata("subcategory_name_error","Sub-Category name already exists."); 
				}	
			}
		} else {
			$this->session->set_flashdata("subcategory_name_error","");
		}
		$this->data['img'] 				= $img;
		$this->data['parentCategories'] = $this->Category_Model->getCat("","","1");
		$this->data['page_name'] 		= 'createsubcategories';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('add_subcategory');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function getsubcategory($subcategory_id)
	{
		echo json_encode(array("status"=>"success","data"=>$this->Category_Model->getChildCategories($subcategory_id)));
	}
	
	public function editedsubcategories($category_id="")
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($category_id == ''){
			redirect(base_url('seller/subcategories?status=invalid_category&code=001'));
			die();
		}
		$this->data['category_id'] 		= $category_id;
		$post_data 						= $this->input->post();
		$categoryEdit 					= $this->Category_Model->getCategoriesByIdforEdit($category_id); 
		$img 							= '';
		$categoriesWeGot 				= $categoryEdit[0];
		$this->data['categoriesWeGot']	=  $categoriesWeGot;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('editsubcategory_name', 'Edit Category Name', 'xss_clean|trim|required');
		$this->form_validation->set_rules('editdisplay_status','Edit Display Status','required');
		$this->form_validation->set_rules('editparent_category_id','Sub Category Parent Category','required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$categoryPath = 'uploads/categories/';
				$img = (isset($_FILES["profile_image"]) &&  $_FILES["profile_image"]["name"] != "")?$this->do_upload_directly($categoryPath, $_FILES["profile_image"], 'categories'):'';
				if($img != ""){	
					$img = $img['original'];
					$post_data['img'] = $img;
				}
				$cat = $this->Category_Model->subcat_update($post_data, $category_id, $user_id);
				if($cat){
					$this->session->set_flashdata("success","Sub-Category updated successfully.");
					redirect(base_url('seller/subcategories?status=success'));
					die();
				} else {
					$this->session->set_flashdata("category_name_error","Category name already exists."); 
				}	
			}
		} else {
			$this->session->set_flashdata("category_name_error","");
		}
		$this->data['parentCategories'] = $this->Category_Model->getCat("","","1");
		$this->data['img'] 				= $img;
		$this->data['page_name'] 		= 'editsubcategories';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('edit_subcategory');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function deletesubcategories($subcategory_id)
	{
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];	
		$deleteUser = array
		(
			"deleted_id"	=> $id,
			"deleted_date"	=> $this->cur_date_time
		);
		$this->Category_Model->deleteCategory($subcategory_id,$deleteUser);
		$this->session->set_flashdata("info", $this->lang->line('subcategory_del_succ'));			
		$this->RefreshListingPage();
	}
	
	private function RefreshListingPage()
	{
		redirect(base_url()."admin/subcategories","refresh");
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

	private function validatesubcategory($data,$is_edit="")
	{
		$chkuniq = "";
		if(!$is_edit){
			 $chkuniq = '|callback_categoryname_check';
		}
		if($this->posteddata && isset($this->posteddata['category_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$this->form_validation->set_rules('category_name_'.$i, 'Sub-Category Name', 'xss_clean|trim|required'.$chkuniq);
				$this->form_validation->set_rules('parent_category_id_'.$i.'[]', 'Parent Category', 'required');				
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
	
	private function uploadsubCategoryFiles($files)
	{
		$config['upload_path'] 	 = './images/uploads/categories/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		for($i = 1; $i <= $this->posteddata['category_count']; $i++){
			if(isset($files["category_image_".$i])){
				$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];				
				$config['file_name']  = "category_".strtotime($this->cur_date_time).$id;	
				$this->load->library('upload', $config);

				if ( ! $this->upload->do_upload("category_image_".$i)){
					$error = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata("info", $this->upload->display_errors());			
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
			$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
			$arrRetval = array();
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$arrRetval[$cnt]['category_name']      = $this->posteddata["category_name_".$i];
				$arrRetval[$cnt]['parent_category_id'] = implode(",",$this->posteddata["parent_category_id_".$i]);
				
				if(isset($this->posteddata["category_image_".$i]) && $this->posteddata["category_image_".$i] != ""){
					$arrRetval[$cnt]['category_image'] = $this->posteddata["category_image_".$i];
				}
				$arrRetval[$cnt]['display_status'] = $this->posteddata["display_status_".$i];
				if($isUpdate){
					$arrRetval[$cnt]['updated_id']   = $id;
					$arrRetval[$cnt]['updated_date'] = $this->cur_date_time;
				}else{
					$arrRetval[$cnt]['created_id']   = $id;
					$arrRetval[$cnt]['created_date'] = $this->cur_date_time;
				}				
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		}else{
			$this->backtologin();
		}
	}

	public function categoryname_check($str){
		$result = $this->Category_Model->getCategories("","","","AND categories.category_name='".$str."'");
		if($result){
			return false;
		}else{
			return true;
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
		$this->Category_Model->getChildCategories2($search, $offset, $length);
		$delete_link = "<a class='actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/subcategories/deletesubcategories/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right p-2" role="menu">
			<li class="pl-2 pb-1"><a class="actions" href="' . site_url('seller/categories/form/$1') . '"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
        $this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
	}
	
	public function delete($cat_id = 0){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$subcategory_id = ($cat_id > 0)?$cat_id:(isset($_POST['id'])?$this->input->post('id'):0);
		$value = $this->input->post('value');
		$resp = $this->Category_Model->delete_subcategory($subcategory_id, $user_id, $value);
		return $resp;
	}
	
	public function hard_delete($category_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Category_Model->hard_delete_category($category_id, $user_id);
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('policy_deleted'));
		}
	}
	
	public function soft_delete($id){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$respo = $this->Category_Model->delete_subcategory_soft($id, $user_id);
		if($respo == "failed"){
			$this->session->set_flashdata("error",$this->lang->line('subcat_cant_delete'));
			$resp['status'] = 'error';
		} else if($respo == "success") {
			$this->session->set_flashdata("success",$this->lang->line('subcategory_del_succ'));
			$resp['status'] = 'success';
		}
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}
}
?>