<?php
class Categories extends SecureAccess 
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
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('admin'));
		}
	}
	
	public function index(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$this->data['id'] 				= (isset($_GET['id']) && $_GET['id'] !="" && is_numeric($_GET['id']))?$_GET['id']:"";
		$this->data['page_name'] 		= 'category_view';
		$this->data['Breadcrumb_name'] 	= 'Category';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function getAllCategories(){
		$currentId = $_POST['id'];
		$categoryData =  $this->Category_Model->allCategories($currentId);
		echo json_encode(array('data'=>$categoryData)); 
	}

	public function getSubCategories(){
		$catId = $_POST['id'];
		$childData =  $this->Category_Model->allChildCategories($catId);
		echo json_encode(array('data'=>$childData)); 
	}

	public function updateProductCategory(){
		$cat_id = $_POST['old_cat_id'];
		foreach($_POST['product_id'] as $index=>$product){
			$data[] = $product." - ".$_POST['subcategory_id'][$index]."<br />";	
		}
		$response = false;
		if(isset($_POST['parentCat_id'])){
			foreach($_POST['category_id'] as $index=>$parentCatId){
				$pCats[] = $_POST['category_id'][$index]." - ".$_POST['parentCat_id'][$index];	
			}
			$response = $this->Category_Model->parentCatChange($pCats);
		}
		$result = $this->Category_Model->catChange($data);
		if($result || $response){
			if($this->delete($cat_id)){
				redirect(base_url('seller/categories?status=success'));
			}
		}
	}

	public function form($category_id=""){
		$user_id = $this->session->userdata('userid');
		$post_data = $this->input->post();
		$this->data['category_data'] = "";
		if($category_id !=""){
			$category_data = $this->Category_Model->getCategoriesByIdforEdit($category_id); 
			$this->data['category_data'] = $category_data[0];
		}
		$this->data['parentCategories'] = $this->Category_Model->getCat("","","1");
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('category_name', 'Category Name', 'xss_clean|trim|required');
		$this->form_validation->set_rules('cat_privacy','Private','required');
		if(isset($_POST['submitBtn'])){
			unset($_POST['submitBtn']);
			//echo "<pre>";print_r($_POST);die();
			$parent_category_id = $post_data['parent_category_id'];
			if($this->form_validation->run() === true){
				if($parent_category_id){
					$id = $this->Utilz_Model->getAllCategoriesParentId($parent_category_id);
					$bradcrumbs = array_reverse($id);
					$slug = "";
					foreach($bradcrumbs as $s){
						$slug .= strtolower(str_replace(" ","-",$s['cat_name']))."-"; 
					}
					$slug = $slug.$post_data['slug'];
				}else{
					$slug = $post_data['slug'];
				}
				$post_data['slug'] = $this->slugify($slug);
				$cat = $this->Category_Model->cat_update($post_data, $category_id, $user_id,$parent_category_id );
				if($cat){
					if($category_id){
						$msg = $this->lang->line('category_update_success');
					}else{
						$msg = $this->lang->line('category_add_success');
					}
					$this->session->set_flashdata("success",$msg);
					redirect(base_url('seller/categories?status=success'));
					die();
				} else {
					$this->session->set_flashdata("category_name_error","Category name already exists.");
				}	
			}
		} else {
			$this->session->set_flashdata("category_name_error","");
		}
		$this->data['page_name'] = 'createcategories';
		$this->data['post_data'] = $post_data;
		if($category_id !=""){
			$this->data['Breadcrumb_name'] = $this->lang->line('update_category');
		}else{
			$this->data['Breadcrumb_name'] = $this->lang->line('create_category');
		}
		$this->data['isScript'] = true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function getcategory($category_id)
	{
		echo json_encode(array("status"=>"success","data"=>$this->Category_Model->getParentCategories($category_id)));
	}
	
	public function editedcategories($category_id = ""){
		if($category_id == ''){
			redirect(base_url('seller/categories?status=invalid_category&code=001'));
			die();
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->data['category_id'] = $category_id;
		$post_data = $this->input->post();
		$categoryEdit = $this->Category_Model->getCategoriesByIdforEdit($category_id); 
		$img = '';
		$categoriesWeGot = $categoryEdit[0];
		$this->data['categoriesWeGot'] =  $categoriesWeGot;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('editcategory_name', 'Edit Category Name', 'xss_clean|trim|required');
		$this->form_validation->set_rules('editdisplay_status','Edit Display Status','required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$categoryPath = 'uploads/categories/';
				$img = (isset($_FILES["profile_image"]) &&  $_FILES["profile_image"]["name"] != "")?$this->do_upload_directly($categoryPath, $_FILES["profile_image"], 'categories'):'';
				if($img != ""){
					$img = $img['original'];
					$post_data['img'] = $img;
				}
				$cat = $this->Category_Model->cat_update($post_data, $category_id, $user_id);
				if($cat){
					$this->session->set_flashdata("success",$this->lang->line('category_update_success'));
					redirect(base_url('seller/categories?status=success'));
					die();
				} else {
					$this->session->set_flashdata("category_name_error",$this->lang->line('category_already_exist'));
				}	
			}
		} else {
			$this->session->set_flashdata("category_name_error","");
		}
		$this->data['img'] 				= $img;
		$this->data['page_name'] 		= 'editcategory';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('edit_category');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}
	
	public function deletecategories($category_id, $response = false)
	{
		$id = (isset($this->userData[0]["userid"]))?$this->userData[0]["userid"]:$this->userData[0]["admin_id"];
		$deleteUser = array
		(
			"deleted_id"	=> $id,
			"deleted_date"	=> $this->cur_date_time
		);
		$resp = $this->Category_Model->deleteCategory($category_id,$deleteUser);
		$this->session->set_flashdata("success", $this->lang->line('category_del_succ'));
		if(!$response){
			$this->RefreshListingPage();
		} else
			return $resp;
	}
	
	private function RefreshListingPage()
	{
		redirect(base_url()."seller/categories","refresh");
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
					<img style = 'height : ".$height."px;width : ".$width."px;' src='".$arr[$rowname]."' class='' /></a>";
			}
		}		
		return $arrData;
	}
	
	private function validatecategory($data,$is_edit = "")
	{
		$chkuniq = "";
		if(!$is_edit){
			 $chkuniq = '|callback_categoryname_check';//'|is_unique['.DBPREFIX.'_categories.category_name]';
		}
		if($this->posteddata && isset($this->posteddata['category_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['category_count']; $i++)			{
				$this->form_validation->set_rules('category_name_'.$i, 'Category Name', 'xss_clean|trim|required'.$chkuniq);
				$this->form_validation->set_rules('display_status_'.$i, 'Display Status', 'required');
				$this->form_validation->set_message('categoryname_check', $data['category_name_'.$i]. $this->lang->line('already_exists'));
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
		$config['upload_path'] = './images/uploads/categories/';
		$config['allowed_types'] = 'gif|jpg|png';
		for($i = 1; $i <= $this->posteddata['category_count']; $i++){
			if(isset($files["category_image_".$i])){	
				$id = (isset($this->userData[0]['id']))?$this->userData[0]['id']:$this->userData[0]['admin_id'];			
				$config['file_name']  = "category_".strtotime($this->cur_date_time).$id;	
				$this->load->library('upload', $config);
				if ( ! $this->upload->do_upload("category_image_".$i)){
					$error = array('info' => $this->upload->display_errors());
					$this->session->set_flashdata("error", $this->upload->display_errors());			
					$this->posteddata["category_image_".$i] = "";
				} else {
					$data = array('upload_data' => $this->upload->data());
					$arrImg = array
					(
						"base"		=>"uploads",
						"type"		=>"categories",
						"img"		=>$data['upload_data']['file_name'],
						"width"		=>100,
						"height"	=>100
					);
					$img_url = base_url()."custom/images?img=".base64_encode(serialize($arrImg));
										
					$this->posteddata["category_image_".$i] = $img_url;
				}				
			} else {
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
				$arrRetval[$cnt]['category_name'] = $this->posteddata["category_name_".$i];
				if(isset($this->posteddata["category_image_".$i]) && $this->posteddata["category_image_".$i] != ""){
					$arrRetval[$cnt]['category_image'] = $this->posteddata["category_image_".$i];
				}
				$arrRetval[$cnt]['display_status'] = $this->posteddata["display_status_".$i];
				$id = (isset($this->userData[0]['userid']))?$this->userData[0]['userid']:$this->userData[0]['admin_id'];			
				if($isUpdate){
					$arrRetval[$cnt]['updated_id'] = $id;
					$arrRetval[$cnt]['updated_date'] = $this->cur_date_time;
				} else {
					$arrRetval[$cnt]['created_id'] = $id;
					$arrRetval[$cnt]['created_date'] = $this->cur_date_time;
				}				
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		} else {
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

	public function get($id="")
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
		$this->Category_Model->getCategoriesById($search, $offset, $length,$id);
		$delete_link = "<a class = 'actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/categories/deletecategories/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right p-2" role="menu">
			<li class="pl-2 pb-1"><a href="' . site_url('seller/categories/form/$1') . '" class="actions"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
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
		$category_id = ($cat_id > 0)?$cat_id:(isset($_POST['id'])?$this->input->post('id'):0);
		if($category_id == 0){
			/* show error here and stop code */
		}
		$value = $this->input->post('value');
		$resp = $this->Category_Model->delete_category($category_id, $user_id, $value);
		return $resp;
	}

	public function hard_delete($category_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$respo = $this->Category_Model->hard_delete_category($category_id, $user_id);
		if($respo == ""){
			$this->session->set_flashdata("error",$this->lang->line('category_not_del'));
			$resp['status'] = 'error';
		} else {
			$this->session->set_flashdata("success",$this->lang->line('category_del_succ'));
			$resp['status'] = 'success';
		}
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}

	public function checkProductCatId(){
		$category_id = $_POST['id'];
		$resp = $this->Category_Model->checkSubcatIdInProduct($category_id);
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}	

	public function checkDependencies(){
		$response = array('status'=>0, 'message'=>$this->lang->line('inv_req'), 'code'=>'000', 'hasProducts'=>0, 'hascategories'=> 0, 'data' => array());
		$category_id = $_POST['id'];
		$resp = $this->Category_Model->checkSubcatIdInProduct($category_id);
		$categoryData =  $this->Category_Model->allChildCategories($category_id);
		if($resp){
			$response['status'] 			= 1;
			$response['message'] 			= 'OK';
			$response['hasProducts'] 		= 1;
			$response['data']['products'] 	= $resp;
		}
		if(count($categoryData)>0){
			$response['status'] 			= 1;
			$response['message'] 			= 'OK';
			$response['hascategories'] 		= 1;
			$response['data']['categories'] = $categoryData;
		}
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

	public function removeImage(){
		$category_id = $_POST['cat_id'];
		$resp = $this->Category_Model->deleteImage($category_id);
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}

	public function delete_image(){
		$table_name = DBPREFIX."_categories";
		$id = $_POST['key'];
		$where = array("category_id"=>$id);
		$data = array("category_image"=>"");
		$this->db->where($where);
		if($this->db->update($table_name,$data)){
			$params = array('filename'=>$_POST['name'],'filetype'=>"categories");
			$upload_server = $this->config->item('media_url').'/file/delete_media';
			$file = $this->Utilz_Model->curlRequest($params, $upload_server, true,false);
			echo json_encode(array("status"=>200,"msg"=>$this->lang->line('image_deleted')));
		}else{
			echo json_encode(array("status"=>0,"msg"=>$this->lang->line('image_cant_delete')));
		}
	}

	public function addPositions(){
		$parent_id = $this->db->select("category_id,position")->from("tbl_categories")->where('parent_category_id = 0 AND is_active="1"')->order_by("position")->get()->result();
		$p1 = 1;
		foreach($parent_id as $p){
			$this->addChildPositions($p->category_id);
			$p1++;
		}
	}

	public function addChildPositions($parent_category_id,$p2=1){
		$child_id = $this->db->select("category_id,position,parent_category_id")->from("tbl_categories")->where('parent_category_id ='.$parent_category_id.' AND is_active="1"')->order_by("position")->get()->result();
		$p2=1;
		if($child_id){
			echo "<hr>";
			foreach($child_id as $c){
				echo $p2." - ";
				print_r($c); 
				$this->db->where("category_id",$c->category_id);
				$this->db->update("tbl_categories",array("position"=>$p2));
				$this->addChildPositions($c->category_id,$p2++);
			}
			echo "<hr>";
		}
	}

	public function changePosition(){
		$data = $_POST['data'];
		$id = array();
		$position = array();
		foreach($data as $d){
			$explode = explode("-",$d);
			$id[] = $explode[1];
			$position[] = $explode[2];
		}
		$start = min($position);
		$end = max($position);
		$j = 0;
		for($i = $start; $i <=$end; $i++){
			$updateData = array("position"=>$i);
			$this->db->where("category_id",$id[$j]);
			$this->db->update("tbl_categories",$updateData);
			$j++;
		}		
		echo json_encode(array("status"=>1));
	}
	public function show_on_homepage(){
		$category_id = $this->input->post('id');
		$value = $this->input->post('value');
		$resp = $this->Category_Model->show_on_homepage($category_id, $value);
		echo json_encode($resp);
	}
}
?>
