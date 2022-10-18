<?php
class Brands extends SecureAccess 
{
	public $posteddata = "";
	function __construct() 
	{
		parent::__construct();		
		$this->load->model("admin/Brands_Model");	
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
		$this->data['user_id'] 			= ($this->session->userdata('userid'))?$this->session->userdata('userid'):"";
		$this->data['page_name'] 		= 'brands_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('brands');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function form($brand_id=""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$post_data = "";
		$this->data['brand_data'] = "";
		if($brand_id !=""){
			$brandsEdit = $this->Brands_Model->getBrandsByIdforEdit($brand_id); 
			$this->data['brand_data'] = $brandsEdit[0];
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$img = '';
		$this->form_validation->set_rules('brand_name','Brand Name','xss_clean|trim|required');
		//$this->form_validation->set_rules('brand_description','Brand Description','required');
		$this->form_validation->set_rules('display_status','Brand Status','required');
		if(isset($_POST['submitBtn'])){
			unset($_POST['submitBtn']);
			if($this->form_validation->run() === true){
				$post_data = $this->input->post();
				if($brand_id ==""){
					$brand_id = $_POST['brand_id'];
				}
				if($brand_id == ""){
					echo "here";
					print_r($post_data);
					$resp = $this->Brands_Model->insertbrands($post_data);
				}else{
					$resp = $this->Brands_Model->updatebrands($brand_id,$post_data);
				}
				if($resp){
					
					$this->session->set_flashdata("success","Brands added successfully.");
					redirect(base_url('seller/brands?status=success'));
					die();
				} else {
					$this->session->set_flashdata("brand_name_error","Brand name already exists.");
				}	
			}
		} else {
			$this->session->set_flashdata("brand_name_error","");
		}
		$this->data['page_name'] 	= 'createbrands';
		$this->data['brand_id'] 	= $brand_id;
		$this->data['post_data'] 	= $post_data;
		if($brand_id !=""){
			$this->data['Breadcrumb_name'] = $this->lang->line('update_brand');
		}else{
			$this->data['Breadcrumb_name'] = $this->lang->line('create_brand');
		}
		$this->data['isScript'] = true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function checkProductBrandId(){
		$brand_id = $_POST['id'];
		$resp = $this->Brands_Model->checkBrandsIdInProduct($brand_id);
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}

	public function getAllbrands(){
		$currentId = $_POST['id'];
		$brandsData =  $this->Brands_Model->allBrands($currentId);
		echo json_encode(array('data'=>$brandsData));
	}

	public function updateProductBrand(){
		foreach($_POST['product_id'] as $index=>$product){
			$data[] = $product." - ".$_POST['brands_id'][$index]."<br />";	
		}
		$result = $this->Brands_Model->brandChange($data);
		if($result){
			$brand_id = $_POST['old_brand_id'];
			if($this->delete($brand_id)){
				redirect(base_url('seller/brands?status=success'));
			}
		}
	}

	public function brandActive(){
		$id = $_POST['id'];
		$status = $_POST['status'];
		$result = $this->Brands_Model->brandActiveChange($id, $status);
		if($result){
			$arr = array("status"=>"success", "id"=>$id, "status"=>$status);
			echo json_encode($arr);
		}else{
			$arr = array("status"=>"failed");
			echo json_encode($arr);
		}
	}

	public function getbrandsbyid($brands_id)
	{
		echo json_encode(array("status"=>"success","data"=>$this->Brands_Model->getBrands($brands_id)));
	}
	
	public function editedbrands($brands_id = "")
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}	
		if($brands_id == ''){
			redirect(base_url('seller/brands?status=invalid_category&code=001'));
				die();
		}
		$this->data['brands_id'] = $brands_id;
		$post_data = $this->input->post();
		$brandsEdit = $this->Brands_Model->getBrandsByIdforEdit($brands_id); 
		$img = '';
		$brandsWeGot = $brandsEdit[0];
		$this->data['brandsWeGot'] =  $brandsWeGot;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('brand_name', 'Edit Brand Name', 'xss_clean|trim|required');
		$this->form_validation->set_rules('brand_description','Brand Description','required');
		$this->form_validation->set_rules('branddisplay_status','Brand Display Status','required');
		if(isset($_POST['Submit'])){
			if($this->form_validation->run() === true){
				$brandPath = 'uploads/brands/';
				$img = (isset($_FILES["profile_image"]) &&  $_FILES["profile_image"]["name"] != "")?$this->do_upload_directly($brandPath, $_FILES["profile_image"], 'brands'):'';
				if($img != ""){	
					$img = $img['original'];
					$post_data['img'] = $img;
				}
				$cat = $this->Brands_Model->brands_update($post_data, $brands_id, $user_id);
				if($cat){
					$this->session->set_flashdata("success",$this->lang->line('brands_update_succ'));
					redirect(base_url('seller/brands?status=success'));
					die();
				} else {
					$this->session->set_flashdata("brand_name_error",$this->lang->line('brands_name_exist'));
				}
			}
		} else {
			$this->session->set_flashdata("brand_name_error","");
		}
		$this->data['img'] 				= $img;
		$this->data['page_name'] 		= 'editbrands';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('edit_brand');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function deletebrands($brands_id)
	{
		$id = (isset($this->userData[0]['id']))?$this->userData[0]['id']:$this->userData[0]['admin_id'];
		$deleteUser = array
		(
			"deleted_id"	=> $id, 
			"deleted_date"	=> $this->cur_date_time
		);
		$this->Brands_Model->deletebrands($brands_id,$deleteUser);
		$this->session->set_flashdata("info", $this->lang->line('brand_deleted'));			
		$this->RefreshListingPage();
	}
	
	private function RefreshListingPage()
	{
		redirect(base_url()."seller/brands","refresh");
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
	
	private function validatebrands($data,$is_edit="")
	{
		$chkuniq = "";
		if(!$is_edit){
			 $chkuniq = '|callback_brandname_check';
		}
		if($this->posteddata && isset($this->posteddata['brands_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['brands_count']; $i++)			{
				$this->form_validation->set_rules('brands_name_'.$i, 'Brand Name', 'xss_clean|trim|required'.$chkuniq);
				$this->form_validation->set_rules('brands_description_'.$i, 'Brand Description', 'required');	
				$this->form_validation->set_message('brandname_check', $data['brands_name_'.$i].' Already Exists.');
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
	
	private function uploadBrandFiles($files){
		$config['upload_path'] = './images/uploads/brands/';
		$config['allowed_types'] = 'gif|jpg|png';
		for($i = 1; $i <= $this->posteddata['brands_count']; $i++){
			if(isset($files["brands_image_".$i])){				
				$id = (isset($this->userData[0]['id']))?$this->userData[0]['id']:$this->userData[0]['admin_id'];	
				$config['file_name']  = "brands_".strtotime($this->cur_date_time).$id;//$this->userData[0]['id'];	
				$this->load->library('upload', $config);

				if ( ! $this->upload->do_upload("brands_image_".$i)){
					$error = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata("info", $this->upload->display_errors());			
					$this->posteddata["brands_image_".$i] = "";
				} else {
					$data = array('upload_data' => $this->upload->data());
					$arrImg = array
					(
						"base"		=> "uploads",
						"type"		=> "brands",
						"img"		=> $data['upload_data']['file_name'],
						"width"		=> 100,
						"height"	=> 100
					);
					$img_url = base_url()."custom/images?img=".base64_encode(serialize($arrImg));				
					$this->posteddata["brands_image_".$i] = $img_url;
				}				
			} else {
				$this->posteddata["brands_image_".$i] = "";
			}			
		}
	}
	
	private function arrangePostData($isUpdate = FALSE)
	{		
		if($this->posteddata && isset($this->posteddata['brands_count'])){
			$arrRetval = array();
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['brands_count']; $i++)			{
				$arrRetval[$cnt]['brand_name'] = $this->posteddata["brands_name_".$i];
				if(isset($this->posteddata["brands_image_".$i]) && $this->posteddata["brands_image_".$i] != ""){
					$arrRetval[$cnt]['brand_image'] = $this->posteddata["brands_image_".$i];
				}
				$arrRetval[$cnt]['display_status'] = $this->posteddata["display_status_".$i];
				$arrRetval[$cnt]['brand_description'] = $this->posteddata["brands_description_".$i];
				$id = (isset($this->userData[0]['userid']))?$this->userData[0]['userid']:$this->userData[0]['admin_id'];			
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		}
		else{
			$this->backtologin();
		}
	}

	public function brandname_check($str){
		$result = $this->Brands_Model->getBrands("","","","","","AND brands.brand_name='".$str."'");
		if($result){
			return false;
		}else{
			return true;
		}
	}

	public function get(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Brands_Model->getBrandsByID($search, $offset, $length);
		$delete_link = "<a class = 'actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/brands/deletebrands/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs btn-primary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right p-2" role="menu"> 
			<li class="pl-2 pt-1"><a href="' . site_url('seller/brands/form/$1') . '" class="actions"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
		$this->datatables->add_column("Actions", $action, "id");
        echo $this->datatables->generate();
	}
	
	public function delete($brand_id = 0){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$brands_id =($brand_id > 0)?$brand_id:(isset($_POST['id'])?$this->input->post('id'):0);
		if($brand_id == 0){
			/* show error here and stop code */
		}
		$value = $this->input->post('value');
		$resp = $this->Brands_Model->deleteBrands($brands_id, $user_id, $value);
		return $resp;
	}

	public function hard_delete($brands_id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Brands_Model->hard_delete_brands($brands_id, $user_id);	
		echo json_encode($resp);
	}
	
	public function soft_delete($brands_id){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$respo = $this->Brands_Model->delete_brands_soft($brands_id, $user_id);
		if($respo == "failed"){
			$this->session->set_flashdata("error",$this->lang->line('brand_cant_deleted'));
			$resp['status'] = 'error';
		} else if($respo == "success") {
			$this->session->set_flashdata("success",$this->lang->line('brand_deleted'));
			$resp['status'] = 'success';
		}
		echo json_encode($resp, JSON_UNESCAPED_UNICODE);
	}

	public function delete_image(){
		$table_name = DBPREFIX."_brands";
		$id = $_POST['key'];
		$where = array("brand_id"=>$id);
		$data = array("brand_image"=>"");
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
