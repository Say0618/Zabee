<?php  
class Reviews extends SecureAccess{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Requests_Model");	
		$this->load->model("admin/Review_Model");	
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);		
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore && $this->session->userdata('user_type') != 1){
			redirect(base_url('admin'));
		}
		$this->lang->load('english', 'english');
	}

	public function index(){
		$this->data['page_name'] 		= 'review_list';
		$this->data['Breadcrumb_name'] 	= 'Review List';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}
	public function form(){
		$this->data['page_name'] 		= 'add_review';
		$this->data['Breadcrumb_name'] 	= 'Add Review';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}
	public function get(){
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');	
		$this->Review_Model->getReviewByID($search, $offset, $length);
		$delete_link = "<a class = 'actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/reviews/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs btn-primary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right p-2" role="menu"> 
			<li class="pl-2 pt-1"><a href="#" class="actions editReview" data-id="$1"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
		$this->datatables->add_column("Actions", $action, "id");
		echo $this->datatables->generate();
	}
	public function getStore($product_id=""){
		$return = $this->Review_Model->getStoreData($product_id);
		if($return){
			echo json_encode(array("status"=>1,"data"=>$return));
		}else{
			echo json_encode(array("status"=>0,"data"=>array()));
		}
	}
	public function getCondition($seller_id,$product_id){
		$return = $this->Review_Model->getCondtionData($product_id,$seller_id);
		if($return){
			echo json_encode(array("status"=>1,"data"=>$return));
		}else{
			echo json_encode(array("status"=>0,"data"=>array()));
		}
	}
	public function getVariant($product_id,$seller_id,$condition_id,$sp_id){
		$return = $this->Review_Model->getVariantData($product_id,$seller_id,$condition_id,$sp_id);
		if($return['status'] == 1){
			echo json_encode(array("status"=>1,"data"=>$return['data']));
		}else if($return['status'] == "2"){
			echo json_encode(array("status"=>2,"data"=>$return['data']));
		}else{
			echo json_encode(array("status"=>0,"data"=>array()));
		}
	}
	public function save(){
		extract($this->input->post());
		$buyer_id = $_SESSION['userid'];
		$email = $_SESSION['email'];
		if(isset($reviewDate) && $reviewDate != ""){	
			$date = $reviewDate;
		}else{
			$date = gmdate("Y-m-d H:i:s");
		}
		$image_count = $_FILES['profile_image']['name'];
		$reviewData = array("product_id"=>$product_id,
							"pv_id"=>$pv_id,
							"sp_id"=>$sp_id,
							"seller_id"=>$seller_id,
							"buyer_id"=>$buyer_id,
							"order_id"=>"",
							"product_name"=>$product_name,
							"name"=>$name,
							"email"=>$email,
							"date"=>$date,
							"review"=>$review,
							"rating"=>$rating,
							"is_approved"=>"1",
							"is_fake"=>"1");	
		if($this->db->insert(DBPREFIX."_product_reviews", $reviewData)){
			$review_id = $this->db->insert_id();
			if($image_count[0] != ""){
				$this->load->model("Utilz_Model");
				$this->load->model("Cart_Model");	
				$config['encrypt_name'] 		= true;
				$config['overwrite'] 			= FALSE;
				$config['upload_path'] = "review";
				$config['upload_thumbnail_path'] = "review/thumbs";
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['quality'] = "100%";
				$config['remove_spaces'] = TRUE;
				$config['upload_thumbnail_width']         = 50;
				$config['upload_thumbnail_height']       = 50;
				for ($i=0; $i < count($image_count); $i++) { 
					$params['file'] = curl_file_create($_FILES['profile_image']['tmp_name'][$i], $_FILES['profile_image']['type'][$i], $_FILES['profile_image']['name'][$i]);
					$params['image_type'] = "review";
					$params['filesize'] = $_FILES['profile_image']['size'][$i];
					$params['config'] = json_encode($config);
					$upload_server = $this->config->item('media_url').'/file/upload_media';
					$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
					$review_data['created_date'] = $date;
					$review_data['review_id'] = $review_id;
					$review_data['picture'] = $file->images->original->filename;
					$review_data['thumbnail'] = $file->images->thumbnail->filename;
					$this->Cart_Model->review_image($review_data);
				}
			}
			$this->session->set_flashdata("success","Review added successfully.");
			redirect(base_url('seller/reviews?status=success'));
		}else{
			$this->session->set_flashdata("success","Error in adding Review.");
			redirect(base_url('seller/reviews?status=error'));
		} 
	}

	public function edit(){
		$this->db->where("review_id",$this->input->post("review_id"));
		if($this->db->update(DBPREFIX."_product_reviews", $this->input->post())){
			echo json_encode(array("status"=>"1", "message"=>"updated successfuly", "code"=>"200"));
		}else{
			echo json_encode(array("status"=>"0", "message"=>"error while updating", "code"=>"401"));
		}
	}

	public function delete($id){
		$this->db->where("review_id",$id);
		if($this->db->update("tbl_product_reviews", array("is_delete"=>"1"))){
			echo json_encode(array("status"=>"1", "message"=>"Review Deleted Successfully", "code"=>"200"));
		}else{
			echo json_encode(array("status"=>"0", "message"=>"Unable to delete Review", "code"=>"401"));
		}
	}
}
?>