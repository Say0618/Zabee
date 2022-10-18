<?php  
class Offers extends SecureAccess{
	function __construct()
	{
		parent::__construct();	
		$this->load->model("admin/Offers_Model");	
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
		$this->data['page_name'] 		= 'offers_view';
		$this->data['Breadcrumb_name'] 	= 'Offers';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function form($offer_id=""){
		$post_data = "";
		$this->data['offer_data'] = "";
		if($offer_id !=""){
			$offerEdit = $this->Offers_Model->getOfferByIdForEdit($offer_id);
			if($offerEdit != ""){
				$offersWeGot = $offerEdit[0];
				$this->data['offersWeGot'] =  $offersWeGot; 
			}
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('offer_name','Offer Name','required');
		$this->form_validation->set_rules('position','Position','required');
		$img = '';
		if(isset($_POST['submitBtn'])){
			unset($_POST['submitBtn']);
			if($this->form_validation->run() === true){
				$post_data = $this->input->post();
				$offer_id = $_POST['offer_id'];
				if($offer_id ==""){
					$resp = $this->Offers_Model->offer_add($post_data);
				}
				else{
					if(!isset($post_data['status'])){
						$post_data['status'] = "0";
					}
					$resp = $this->Offers_Model->offer_update($post_data, $offer_id);	
				}
				if($resp){
					$this->session->set_flashdata("success","Offer added successfully.");
					redirect(base_url('seller/offers?status=success'));
					die();
				} else {
					$this->session->set_flashdata("brand_name_error","Offer name already exists.");
				}	
			}
		} else {
			$this->session->set_flashdata("brand_name_error","");
		}
		$this->data['page_name'] 	= 'offers_add';
		$this->data['offer_id'] 	= $offer_id;
		if($offer_id !="" && isset($offerEdit) && $offerEdit != ""){
			$this->data['post_data'] 	= $offerEdit[0];
			$this->data['bannerlink'] 		= '';
			$this->data['Breadcrumb_name'] = "Update Offer";
		}else{
			$this->data['bannerlink'] 		= '';
			$this->data['Breadcrumb_name'] = "Create Offer";
		}
		$this->data['isScript'] = true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function offers_get()
	{
		$this->load->library('datatables');	
		$a = $this->Offers_Model->getOffers();
		$delete_link = "<a class= 'actions' id='delete-\$1' href='javascript:void(0);' onclick='askDelete(\"$1\",\"$2\",\"$3\",\"$4\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' data-img='' href='" . site_url('seller/banner/offer_delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash\"></i>".$this->lang->line('delete_itm')."</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right" role="menu">
			<li class="pl-2 pb-1"><a href="' . site_url('seller/offers/form/$1') . '" class="actions"><i class="fa fa-edit"></i>'.$this->lang->line("edit").'</a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
		$this->datatables->add_column("Actions", $action, "id, offer_image, position, is_active");
		echo $this->datatables->generate();
    }

	public function offer_delete($id = ''){
		$offer_id 	= $this->input->post('id');
		$value 		= $this->input->post('value');
		$position 	= $this->input->post('position');
		$resp 		= $this->Offers_Model->deleteOffer($offer_id, $value, $position);
		echo $resp;
	}

	public function hard_delete_offer($id){
		$respo['status'] = 'Failed';
		$params = array('filename'=>$_POST['name'],'filetype'=>"special_offer");
		$upload_server = $this->config->item('media_url').'/file/delete_media';
		$file = $this->Utilz_Model->curlRequest($params, $upload_server, true,false);
		if($file){
			$resp = $this->Offers_Model->offer_hard_delete($id, $_POST['position'], $_POST['status']);
			if($resp){
				$this->session->set_flashdata("success", "Offer Deleted Successfully");
				$respo['status'] = 'success';
			}
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
    }
    
    public function offer_update($id = ""){
		if($id == ''){
			redirect(base_url('seller/offers?status=invalid_category&code=001'));
			die();
		}
		$this->data['id'] = $id;
		$offerEdit = $this->Offers_Model->getOfferByIdForEdit($id); 
		$img = '';
        $offersWeGot = $offerEdit[0];
		$this->data['offersWeGot'] =  $offersWeGot;
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$post_data = $this->input->post();
		$this->data['error'] = '';
		$this->form_validation->set_rules('offer_name','Offer Name','required');
		$this->form_validation->set_rules('position','Position','required');
		if($this->form_validation->run() == TRUE){
			$config['upload_path'] 			 = "special_offer";
			$config['allowed_types'] 		 = 'gif|jpg|png|jpeg';
			$config['quality'] 				 = "100%";
			$config['remove_spaces'] 		 = TRUE;
			
			$params['file'] 		= curl_file_create($_FILES['profile_image']['tmp_name'], $_FILES['profile_image']['type'], $_FILES['profile_image']['name']);
			$params['image_type'] 	= "special_offer";
			$params['filesize'] 	= $_FILES['profile_image']['size'];
			$params['config'] 		= json_encode($config);
			$upload_server = $this->config->item('media_url').'/file/upload_media';
			$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
			if($file != ""){
                $post_data['image'] =  $file->images->original->filename;
            }
			$post_data['status'] = $offersWeGot->is_active;
			$this->data['offer_id'] = $offersWeGot->id;
			$resp = $this->Offers_Model->offer_update($post_data, $id);
			if($resp){
				$this->session->set_flashdata("success","Offer Updated Successfully");
				redirect(base_url('seller/offers?status=success'));
				die();
			}	
		}
		$this->data['img'] 				= $img;
        $this->session->set_userdata("position", $offersWeGot->position);
		$this->data['page_name'] 		= 'offers_update';
		$this->data['Breadcrumb_name'] 	= "Update Offer";
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function delete_image(){
		$table_name = DBPREFIX."_special_offers";
		$id 		= $_POST['key'];
		$where 		= array("id"=>$id);
		$data 		= array("offer_image"=>"");
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