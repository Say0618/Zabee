<?php
class Product extends SecureAccess 
{
	public $posteddata = "";
	public $productData = "";	
	public $categorydata = "";	
	public $subCategoryData = "";	
	public $brandsData = "";	
	public $vendorsData = "";	
	public $product_name = "";
	function __construct() 
	{
		parent::__construct();	
		$this->load->model("Product_Model");		
		$this->load->model("admin/Category_Model");
		$this->load->model("admin/Brands_Model");
		$this->load->model("admin/Returntype_Model");
		$this->load->model("Utilz_Model");
		$this->load->helper('string');
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications,
		);
		$data = array(
			'page_name' => 'store_info',
			'isScript' 	=> false,
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
		if(isset($_SESSION["store_status"]) && $_SESSION["store_status"] == "0"){
			$this->session->set_flashdata('error', 'Waiting for admin approval');
			redirect(base_url('seller/dashboard'));
		}
		if(isset($_SESSION["store_status"]) && $_SESSION["store_status"] == "2"){
			$this->session->set_flashdata('error', 'Admin declined your store request');
			redirect(base_url('seller/dashboard'));
		}
	}

	public function index(){
		$this->listProduct();
	}

	private function listProduct(){
		$this->data['page_name'] 		= 'product_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('product');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function create(){
		//$this->load->library('Shopping', 'shopping');
		/*$data =array(array("offer_id"=>"lg-aristo-3","title"=>"LG Aristo 3","brand"=>"LG",
		"description"=>"The smartphone comes with an impressive chipset of Qualcomm Snapdragon 425 processor. The smartphone is pack with 2 GB RAM and 16 GB internal storage which can be expanded with the microSD card.",
		"link"=>"https://zab.ee/product/lg-aristo-3",
		"image_link"=>"https://img.zab.ee/product/67eaa59481b1cbb063844cb53de41ea3.jpg","availablity"=>"in stock","condition"=>"3","gtin"=>"723755136229","price"=>"103.50"));
		//$this->shopping->insertProduct($data);
		die();*/
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->data['variantData'] 		=  $this->Product_Model->getVariantData('category.v_cat_id,category.v_cat_title','');	
		$this->data['subCategoryData'] 	=  $this->Category_Model->getCat("","category_id,category_name,is_private,parent_category_id",1);	
		$this->data['brandsData'] 		=  $this->Brands_Model->getBrands("","brand_id,brand_name",1);	
		$this->data['page_name'] 		= 'product_add';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('product');
		$this->data['isScript'] 		= true;
		// $this->data['short_description'] = $this->input->post("short_description");
		// $this->data['subcategory_id[]']	= $this->input->post("subcategory_id[]");
		$this->load->view("admin/admin_template", $this->data);
	}

	public function get_variant(){
		$result = array('status'=>0);
		$id = $this->input->post('id');
		if(is_array($id)){
			$v_cat_id = implode(',',$id);
			$where = "AND v_cat_id IN (".$v_cat_id.")";
		}else{
			$where = "AND v_cat_id =".$id;
		}
		$variantData =  $this->Product_Model->getVariant($where,'',"v_id,v_cat_id,v_title");	
		if($variantData){
			$result= array('status'=>1,'data'=>$variantData);
		}
		echo json_encode($result);
	}

	public function createProduct(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_name', 'product name', 'trim|required|callback_checkTitle');
		$this->form_validation->set_rules('slug', 'slug', 'trim|required|callback_checkSlug');
		$this->form_validation->set_rules('subcategory_id[]', 'Category', 'required');
		$this->form_validation->set_rules('brands_id[]', 'brand', 'required');
		if ($this->form_validation->run() == TRUE){
			$product = array();
			$video_link = array();
			$media_id = array();
			$imageCount = count($_FILES['file']['name']);
			$date = date('Y-m-d H:i:s');
			$date_utc = gmdate("Y-m-d\TH:i:s");
			$date_utc = str_replace("T"," ",$date_utc);
			$seller_id = $this->session->userdata['userid'];
			if(isset($this->session->userdata['user_type']) && $this->session->userdata['user_type'] != 1){
				$created_by = $this->lang->line('seller');
				$is_active = "0";
			}else{
				$created_by = $this->lang->line('admin');
				$is_active = "1";
			}
			$feature = $_POST['feature'];
			$product_dimensions = array('dimension_type' 	=> $_POST['productLength_type'],
											'height'=>$_POST['productHeight'],
											'width' 	=> $_POST['productWidth'],
											'length' 	=> $_POST['productLength'],
											'weight_type' 	=> $_POST['productWeight_type'],				
											'weight' => $_POST['productWeight']
										);
			$shippingInfo = array('dimension_type' 	=> $_POST['shipLength_type'],
											'height'=>$_POST['shipHeight'],
											'width' 	=> $_POST['shipWidth'],
											'length' 	=> $_POST['shipLength'],
											'weight_type' 	=> $_POST['shipWeight_type'],
											'weight' 	=> $_POST['shipWeight'],				
											'shipping_note' => $_POST['shipNote']
										);
			$product = array('product_name'			=>trim($_POST['product_name']),
							 'slug'					=> $this->slugify($_POST['slug']),
							 'product_description'	=>$_POST['product_description'],
							 //'sub_category_id' 		=>$_POST['subcategory_id'][0],
							 'is_private' 			=>(isset($_POST['cat_is_private']) && $_POST['cat_is_private'] !="")?$_POST['cat_is_private']:"0",
							 'brand_id'				=>$_POST['brands_id'][0], 
							 'created_id'			=>$seller_id,
							 'upc_code' 			=> ($_POST['product_upc_code'] !="")?$_POST['product_upc_code']:rand(0,time()),
							 'sku_code' 			=> ($_POST['product_sku_code'] !="")?$_POST['product_sku_code']:rand(0,time()),
							 'created_date'			=>$date_utc,
							 'created_by'			=>$created_by,
							 'is_active'			=>$is_active,
							 'short_description'	=>$_POST['short_description'],
							 );
			$keywords		=$_POST['product_keyword'];
			$variant_cat 	= isset($_POST['variant_cat'])?$_POST['variant_cat']:"";
			$video_link 	= (isset($_POST['product_video_link']))?$_POST['product_video_link']:"";
			$media_id 		= (isset($_POST['media_id']))?$_POST['media_id']:"";
			$dummy_id 		= (isset($_POST['dummy_id']))?$_POST['dummy_id']:"";
			$category_id	= (isset($_POST['subcategory_id']))?$_POST['subcategory_id']:"";
			$result 		= $this->Product_Model->createProduct($product,$dummy_id,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id,$product_dimensions,$category_id, 'web');
			if($result){
				$this->session->set_flashdata("success",$this->lang->line('pdt_created'));
				if($created_by == "Admin"){
					$this->RefreshListingPage();
				}else{
					$this->saveNotifications("admin");
					$this->saveNotifications("seller");
					redirect(base_url("seller/product/pending_view"));
				}
			}else{
				$this->session->set_flashdata("error",$this->lang->line('pdt_err'));
				$this->RefreshListingPage();
			}
		}
		else{
			$this->create();
		}
	}

	public function updateProduct(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_name', 'product name', 'trim|required|callback_checkTitle');
		$this->form_validation->set_rules('slug', 'slug', 'trim|required|callback_checkSlug');
		$this->form_validation->set_rules('subcategory_id[]', 'Category', 'required');
		$this->form_validation->set_rules('brands_id[]', 'brand', 'required');
		//echo "<pre>";print_r($_POST); die();
		if ($this->form_validation->run() == TRUE){
			$product 		= array();
			$video_link 	= array();
			$media_id 		= array();
			$product_id 	= $_POST['product_id'];
			$date 			= date('Y-m-d H:i:s');
			$date_utc 		= gmdate("Y-m-d\TH:i:s");
			$date_utc 		= str_replace("T"," ",$date_utc);
			$seller_id 		= $this->session->userdata['userid'];
			$feature 		= $_POST['feature'];
			$product_dimensions = array('dimension_type' 	=> $this->input->post('productLength_type'),
											'height'		=> $this->input->post('productHeight'),
											'width' 		=> $this->input->post('productWidth'),
											'length' 		=> $this->input->post('productLength'),
											'weight_type' 	=> $this->input->post('productWeight_type'),				
											'weight' 		=> $this->input->post('productWeight')
										);
			$shippingInfo = array('dimension_type' 	=> $this->input->post('shipLength_type'),
											'height'=>$this->input->post('shipHeight'),
											'width' 	=> $this->input->post('shipWidth'),
											'length' 	=> $this->input->post('shipLength'),
											'weight_type' 	=> $this->input->post('shipWeight_type'),
											'weight' 	=> $this->input->post('shipWeight'),				
											'shipping_note' => $this->input->post('shipNote')
										);
			$product = array('product_name'			=>trim($_POST['product_name']),
							 'slug'					=>$this->slugify($_POST['slug']),
							 'product_description'	=>$_POST['product_description'],
							 //'sub_category_id' 		=>$_POST['subcategory_id'][0],
							 'is_private' 			=>$_POST['cat_is_private'],
							 'brand_id'				=>$_POST['brands_id'][0],
							 'upc_code' 			=> ($_POST['product_upc_code'] !="")?$_POST['product_upc_code']:rand(0,time()),
							 'sku_code' 			=> ($_POST['product_sku_code'] !="")?$_POST['product_sku_code']:"",
							 'updated_date'			=>$date_utc,
							 'short_description'	=>$_POST['short_description']
							 );
			//echo "<pre>";print_r($product);print_r($shippingInfo);print_r($product_dimensions);die();
			if($_SESSION['user_type'] == "2" && $_SESSION['userid'] !="1"){
				$product['is_declined'] = "0";
				$product['is_active'] 	= "0";
			} 
			$keywords	 				= $_POST['product_keyword'];
			$variant_cat 				= isset($_POST['variant_cat'])?$_POST['variant_cat']:"";
			$video_link['video_link'] 	= (isset($_POST['product_video_link']))?$_POST['product_video_link']:"";
			$media_id['media_id'] 		= (isset($_POST['media_id']))?$_POST['media_id']:"";
			$category_id	= (isset($_POST['subcategory_id']))?$_POST['subcategory_id']:"";
			$product_variant = $this->Product_Model->getData(DBPREFIX."_product_variant","pv_id,sp_id,variant_group,variant_cat_group",array('product_id'=>$product_id));
			if($variant_cat && $product_variant){ //Update Existing Inventories.
				foreach($product_variant as $pv){
					$dbVariant = explode(",",$pv->variant_group);
					$dbVariantCat = explode(",",$pv->variant_cat_group);
					$variant_cat_group = $this->variant_filter($dbVariantCat,$variant_cat); //Variant Group Filter 
					$product_variant = $this->Product_Model->getData(DBPREFIX."_variant","GROUP_CONCAT(v_id) as v_id",$variant_cat_group,"v_cat_id","","","","","1");
					$product_variant = explode(",",$product_variant[0]->v_id);
					$variant_group = $this->variant_filter($product_variant,$dbVariant); //Variant Filter
					if($variant_cat_group && $variant_cat_group){
						$this->db->where("pv_id",$pv->pv_id);
						$this->db->update(DBPREFIX."_product_variant",array("variant_group"=>$variant_group,"variant_cat_group"=>$variant_cat_group));
					}
					if($variant_group){
						$this->db->where(array("sp_id"=>$pv->sp_id,"pv_id"=>$pv->pv_id));
						$this->db->where_not_in("v_id",$variant_group,FALSE,NULL);
						$this->db->delete(DBPREFIX."_seller_product_variant");
					}
					if($variant_cat_group){
						$this->db->where(array("product_id"=>$product_id));
						$this->db->where_not_in("v_cat_id",$variant_cat_group,FALSE,NULL);
						$this->db->delete(DBPREFIX."_product_variant_category");
					}
				}
			}else if($variant_cat == "" && $product_variant){
				foreach($product_variant as $pv){
					$this->db->where("pv_id",$pv->pv_id);
					$this->db->update(DBPREFIX."_product_variant",array("variant_group"=>"","variant_cat_group"=>""));
					
					$this->db->where(array("sp_id"=>$pv->sp_id,"pv_id"=>$pv->pv_id));
					$this->db->delete(DBPREFIX."_seller_product_variant");

					$this->db->where(array("product_id"=>$product_id));
					$this->db->delete(DBPREFIX."_product_variant_category");
				}
			}
			$result = $this->Product_Model->updateProduct($product_id,$product,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id,$product_dimensions,$category_id);
			if($result){
				$product_name 	= $_POST['product_name'];
				$created_id 	= $this->Product_Model->getUserIdByProductId($product_id);
				$created_id 	= $created_id[0]->created_id;
				$action 		= $this->lang->line('your_pdt').$product_name.$this->lang->line('admin_err') ;
				$not_type 		= '0';
				$from 			= "1";
				$to 			= $created_id;
				$user_type 		= "2";
				saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
				$this->session->set_flashdata("success",$this->lang->line('pdt_updated'));
				if($this->session->userdata['user_type'] ==1){
					redirect(base_url("seller/product"));
				}else{
					redirect(base_url("seller/product/pending_view"));
				}
			}else{
				$this->session->set_flashdata("error",$this->lang->line('pdt_err'));
				$this->RefreshListingPage();
			}
		}
		else{
			$this->create();
			// redirect(base_url("seller/product/create?pi=".$this->input->post('product_id')));
		}
	}

	public function getproduct($product_id="")
	{
		if(isset($_POST['id'])){
			$product_id = $_POST['id'];
		}
		$data['product'] 	= $this->Product_Model->getProduct($product_id);
		$data['features'] 	= $productFeatures = $this->Product_Model->getData(DBPREFIX.'_product_features',"feature",array('product_id'=>$product_id));
		$data['image'] 		= $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>"1"),"","","is_cover DESC");
		echo json_encode(array("status"=>"success","data"=>$data));
	}

	public function getProductData($product_id="")
	{
		if(isset($_POST['id'])){
			$product_id = $_POST['id'];
		}
		$data['product'] = $this->Product_Model->getProductData($product_id);
		$data['features'] = $this->Product_Model->getData(DBPREFIX.'_product_features',"feature",array('product_id'=>$product_id));
		$keywords = $this->Product_Model->getData(DBPREFIX.'_meta_keyword',"GROUP_CONCAT(keywords) as keyword",array('product_id'=>$product_id));
		if(isset($keywords[0]) && $keywords[0]->keyword !=""){
			$data['keywords'] = $keywords[0]->keyword; 
		}else{
			$data['keywords'] = "";
		}
		$data['imagePreviewData'] = "";
		$data['image'] = array();
		$where = array("product_id" =>$product_id,"condition_id"=>"1","is_active"=>"1");
		$imagePreviewData =  $this->Product_Model->getData(DBPREFIX."_product_media","media_id,thumbnail,iv_link,is_image,is_local,is_cover",$where,0,'','position');
		if($imagePreviewData){
			$imageData = array("initialPreview"=>array(),"initialPreviewConfig"=>array());
			foreach($imagePreviewData as $ipd){
				if($ipd->is_image == "1"){
					array_push($imageData['initialPreview'],product_thumb_path($ipd->thumbnail));
					array_push($imageData['initialPreviewConfig'],
						[
							'caption' 	=> $ipd->thumbnail, // caption
							'key' 		=> $ipd->media_id,       // keys for deleting/reorganizing preview
							'width' 	=> "120px", 
							'extra' 	=> ['name'=>$ipd->iv_link,"thumb"=>$ipd->thumbnail],
						]
					);
				}else{
					array_push($data['image'],$ipd);
				}
			}
			$data['imagePreviewData'] = array();
			$data['imagePreviewData']['initialPreview'] = implode(",",$imageData['initialPreview']);
			$data['imagePreviewData']['initialPreviewConfig'] = json_encode($imageData['initialPreviewConfig']);
		}
		echo json_encode(array("status"=>"success","data"=>$data));
	}

	public function getProductDataForInventory($product_id=""){
		if(isset($_POST['id'])){
			$product_id = $_POST['id'];
		}
		if(isset($_POST['seller_id'])){
			$seller_id = $_POST['seller_id'];
		}else{
			$seller_id = $this->session->userdata['userid'];
		}
		$mpn 	 = $this->input->post("mpn");
		$hubx_id = $this->input->post("hubx_id");
		//Test k bd remove krni hay
		//$seller_id = "5dae955f9a842";
		
		$data = $this->Product_Model->getProductDataForInventory($product_id,$seller_id);
		//echo "<pre>";print_r($data);die();
		if(empty($data['condition']) && $hubx_id !=""){
			$tablename = DBPREFIX."_hubx_product";
			$select = "seller_id,condition_id,moq,mxq,unit_price,availability,attributes,comments";
			$where = array("hubx_id"=>$hubx_id);
			$hubx_data = $this->Product_Model->getData($tablename,$select,$where,"","","","","","","","");
			$data['condition'] = array();
			foreach($hubx_data as $index=>$hd){
				$attribute = unserialize($hd->attributes);
				$comments = unserialize($hd->comments);
				/*echo "<pre>";print_r($hd);
				print_r($attribute);
				print_r($comments);
				die();*/
				$data['condition'][$index] = new stdClass();
				$data['condition'][$index]->condition_id= $hd->condition_id;
                $data['condition'][$index]->seller_sku = "";
				$data['condition'][$index]->sp_id = "hubx";
				$data['condition'][$index]->seller_product_description = (isset($comments[0]->comment))?$comments[0]->comment:"";
				$data['condition'][$index]->shipping_ids = "";
				$data['condition'][$index]->return_id = 0;
				$data['condition'][$index]->pv_id = "";
				$data['condition'][$index]->variant_group = "";
                $data['condition'][$index]->variant_cat_group = "";
				$data['condition'][$index]->price = $hd->unit_price;
				$data['condition'][$index]->quantity = $hd->availability;
				$data['condition'][$index]->discount = 0;
				$data['condition'][$index]->warehouse_id = "";
				$data['condition'][$index]->shipping = "";
				$data['condition'][$index]->total_qty = $hd->availability;
				$data['condition'][$index]->hubx_id = $hubx_id;
				$data['condition'][$index]->warranty_id = "";
				foreach($attribute as $a){
					$a = (array)$a;
					if(in_array("Warranty",$a)){
						$check = $this->db->select("id")->from("tbl_warranty")->where("warranty",$a['value'])->get()->row();
						if(!$check){
							$wdata = array("warranty"=>$a['value']);
							$this->db->insert("tbl_warranty",$wdata);
							$warranty_id = $this->db->insert_id();
						}else{
							$warranty_id = $check->id;
						}
						$data['condition'][$index]->warranty_id = $warranty_id;
					}
				}
				$data['image'][$hd->condition_id] = array();
			}
		}
		//echo "<pre>";print_r($data);die();
		$imageData = array();
		foreach($data['image'] as $key=>$value){
			if(!empty($value)){
				$imageData[$key] = array();
				$imageData[$key]['initialPreview'] = array();
				$imageData[$key]['initialPreviewConfig'] = array();
				foreach($value as $ipd){
					if($ipd->is_image == "1"){
						array_push($imageData[$key]['initialPreview'],product_thumb_path($ipd->thumbnail));
						array_push($imageData[$key]['initialPreviewConfig'],
							[
								'caption' 	=> $ipd->thumbnail, // caption
								'key' 		=> $ipd->media_id,       // keys for deleting/reorganizing preview
								'width' 	=> "120px", 
								'extra' 	=> ['name'=>$ipd->iv_link,"thumb"=>$ipd->thumbnail],
							]
						);
					}else{
						// array_push($data['image'][$key],$ipd);
					}
				}
				$data["imageData"][$key]['initialPreview'] = implode(",",$imageData[$key]['initialPreview']);
				$data["imageData"][$key]['initialPreviewConfig'] = json_encode($imageData[$key]['initialPreviewConfig']);
			}else{
				$data["imageData"][$key] = "";
			}
		}
		//unset($data['image']);
		// echo "<pre>";print_r($data);die();
		if(empty($data['condition']) && empty($data['preDefinedVariant'])){
			echo json_encode(array("status"=>"error","data"=>$data));	
		}else{
			echo json_encode(array("status"=>"success","data"=>$data));
		}
	}

	private function RefreshListingPage($url="")
	{
		if($url){
			redirect($url,"refresh");
		} else {
			redirect(base_url()."seller/product","refresh");
		}		
	}

	private function validateproduct($data,$is_edit = "")
	{
		$chkuniq = "";
		if(!$is_edit){
			 $chkuniq = '|is_unique['.DBPREFIX.'_product.product_name]';
		}
		if($this->posteddata && isset($this->posteddata['product_count'])){
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['product_count']; $i++)			
			{
				$this->form_validation->set_rules('product_name_'.$i, 'Product Name', 'xss_clean|trim|required'.$chkuniq);
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

	private function uploadProductFiles($files,$is_edit = FALSE)
	{
		$this->load->library('upload');
		$this->load->library('image_lib');
		$product_image= array();
		$config = array();
		if(isset($_FILES["product_image"])){
			$config['upload_path'] = './uploads/product/';
			if(!is_dir($config['upload_path'])){ 
				mkdir($config['upload_path']);
				mkdir($config['upload_path'].'thumbs');
			}
			for($i = 0; $i < count($files["product_image"]['name']); $i++){		
				$config['upload_path'] 			= './uploads/product/';
				$config['allowed_types'] 		= 'gif|jpg|png|jpeg';
				$config['encrypt_name'] 		= true;
				$config['quality'] 				= "100%";
				$config['overwrite'] 			= FALSE;
				$config['remove_spaces'] 		= TRUE;
				$_FILES['userfile']['name'] 	= $_FILES['product_image']['name'][$i];
				$_FILES['userfile']['type'] 	= $_FILES['product_image']['type'][$i];
				$_FILES['userfile']['tmp_name'] = $_FILES['product_image']['tmp_name'][$i];
				$_FILES['userfile']['error'] 	= $_FILES['product_image']['error'][$i];
				$_FILES['userfile']['size'] 	= $_FILES['product_image']['size'][$i];
				$this->upload->initialize($config);
				if ( ! $this->upload->do_upload()){
					$error = array('info' => $this->upload->display_errors());
					$this->session->set_flashdata("error", $this->upload->display_errors());			
					if($is_edit){
						$product_image['thumbnail'][] = 'Preview.png';
						$product_image['original'][]  = 'Preview.png';
					}
					$product_image['is_local'] = "1";
				} else {
					$data 		= array('upload_data' => $this->upload->data());
					$thumbnail 	= $data['upload_data']['raw_name'].'_thumb'.$data['upload_data']['file_ext'];
					$image_name =  $data['upload_data']['file_name'];
					unset($config);
					$config['image_library'] 	= 'gd2';
					$config['source_image'] 	= './uploads/product/'. $image_name;
					$config['create_thumb'] 	= TRUE;
					$config['maintain_ratio'] 	= TRUE;
					$config['overwrite'] 		= FALSE;
					$config['width'] 			= 200;
					$config['height'] 			= 200;
					$config['new_image'] 		= './uploads/product/thumbs/'. $image_name;
					
					$this->image_lib->initialize($config);
					$this->image_lib->resize();
					$this->image_lib->clear();
					$product_image['thumbnail'][] = $thumbnail;
					$product_image['original'][]  = $data['upload_data']['file_name'];
					$product_image['is_local'] 	  = "1";
				}				
			}
		}else{
			return false;
		}
		return $product_image;
	}

	public function uploadFiles($seller_id,$iscover=""){
		$this->load->library('upload');
		$this->load->library('image_lib');
		$is_ajax = (isset($_POST['is_ajax']))?TRUE:FALSE;
		$product_image= array();
		$config = array();
		if(isset($_FILES)){
			$config['upload_path'] = './uploads/product/';
			if(!is_dir($config['upload_path'])){ 
				mkdir($config['upload_path']);
				mkdir($config['upload_path'].'thumbs');
			}
			for($i = 0; $i < count($_FILES["file"]['name']); $i++){		
				$config['upload_path'] 			= './uploads/product/';
				$config['allowed_types'] 		= 'gif|jpg|png|jpeg';
				$config['encrypt_name'] 		= true;
				$config['quality'] 				= "100%";
				$config['overwrite'] 			= FALSE;
				$config['remove_spaces'] 		= TRUE;
				$_FILES['userfile']['name'] 	= $_FILES['file']['name'][$i];
				$_FILES['userfile']['type'] 	= $_FILES['file']['type'][$i];
				$_FILES['userfile']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
				$_FILES['userfile']['error'] 	= $_FILES['file']['error'][$i];
				$_FILES['userfile']['size'] 	= $_FILES['file']['size'][$i];
				$this->upload->initialize($config);
				if ( ! $this->upload->do_upload()){
					$error = array('info' => $this->upload->display_errors());		
					return $error;
				} else {
					$data = array('upload_data' => $this->upload->data());
					$thumbnail = $data['upload_data']['raw_name'].'_thumb'.$data['upload_data']['file_ext'];
					$image_name =  $data['upload_data']['file_name'];
					unset($config);
					$config['image_library'] 	= 'gd2';
					$config['source_image'] 	= './uploads/product/'. $image_name;
					$config['create_thumb'] 	= TRUE;
					$config['maintain_ratio'] 	= TRUE;
					$config['overwrite'] 		= FALSE;
					$config['width'] 			= 400;
					$config['height'] 			= 400;
					$config['new_image'] 		= './uploads/product/thumbs/'. $image_name;
					
					$this->image_lib->initialize($config);
					$this->image_lib->resize();
					$this->image_lib->clear();
					$cover_img = "0";
					if($i == 0 && $iscover !=""){
						$imageData = array('dummy_id'=>$seller_id,'thumbnail'=>$thumbnail,'condition_id'=>"1", 'iv_link'=>$image_name, 'is_local'=>"1",'position'=>$i,'is_image'=>"1","is_cover"=>"1");
					} else {
						$imageData = array('dummy_id'=>$seller_id,'thumbnail'=>$thumbnail,'condition_id'=>"1", 'iv_link'=>$image_name, 'is_local'=>"1",'position'=>$i,'is_image'=>"1","is_cover"=>"0");
					}
					$this->db->insert('tbl_product_media', $imageData);
				}				
			}
		} else {
			return false;
		}
		if($is_ajax){
			echo json_encode(array("status"=>"success"));
		} else {
			return TRUE;
		}
	}

	private function uploadProductConditionFiles($files,$condition_id="",$seller_id,$iscover="")
	{
		$this->load->library('upload');
		$this->load->library('image_lib');
		$upload_path 	= './uploads/product/';
		$product_image 	= array();
		$config = array();
		if(!is_dir($upload_path)){ 
			mkdir($upload_path);
		}
		if(!is_dir($upload_path.'thumbs')){ 
			mkdir($upload_path.'thumbs');
		}
		if(isset($_FILES["condition_image".$condition_id])){
			for($i = 0; $i < count($files["condition_image".$condition_id]['name']); $i++){		
				unset($config);
				$config['upload_path'] 			= './uploads/product/';
				$config['allowed_types'] 		= 'gif|jpg|png|jpeg';
				$config['encrypt_name'] 		= true;
				$config['quality'] 				= "100%";
				$config['overwrite'] 			= FALSE;
				$config['remove_spaces'] 		= TRUE;
				$_FILES['userfile']['name'] 	= $files['condition_image'.$condition_id]['name'][$i];
				$_FILES['userfile']['type'] 	= $files['condition_image'.$condition_id]['type'][$i];
				$_FILES['userfile']['tmp_name'] = $files['condition_image'.$condition_id]['tmp_name'][$i];
				$_FILES['userfile']['error'] 	= $files['condition_image'.$condition_id]['error'][$i];
				$_FILES['userfile']['size'] 	= $files['condition_image'.$condition_id]['size'][$i];
				$this->upload->initialize($config);
				if ( ! $this->upload->do_upload())
				{
					$error = array('info' => $this->upload->display_errors());
					$this->session->set_flashdata("error", $this->upload->display_errors());			
					return $error;	
				} else {
					$data = array('upload_data' => $this->upload->data());
					$thumbnail = $data['upload_data']['raw_name'].'_thumb'.$data['upload_data']['file_ext'];
					$image_name =  $data['upload_data']['file_name'];
					unset($config);
					$config['image_library'] 	= 'gd2';
					$config['source_image'] 	= './uploads/product/'. $image_name;
					$config['create_thumb'] 	= TRUE;
					$config['maintain_ratio'] 	= TRUE;
					$config['overwrite'] 		= FALSE;
					$config['width'] 			= 350;
					$config['height'] 			= 250;
					$config['new_image'] 		= './uploads/product/thumbs/'. $image_name;
					
					$this->image_lib->initialize($config);
					$this->image_lib->resize();
					$this->image_lib->clear();
					$product_image = $condition_id;
					if($i == 0 && $iscover !=""){
						$imageData = array('dummy_id'=>$seller_id,'thumbnail'=>$thumbnail,'condition_id'=>$condition_id,'iv_link'=>$image_name, 'is_local'=>"1",'position'=>$i,'is_image'=>"1","is_cover"=>"1");
					} else {
						$imageData = array('dummy_id'=>$seller_id,'thumbnail'=>$thumbnail,'condition_id'=>$condition_id,'iv_link'=>$image_name, 'is_local'=>"1",'position'=>$i,'is_image'=>"1","is_cover"=>"0");
					}
					$this->db->insert('tbl_product_media', $imageData);
				}				
			}
		} else {
			return false;
		} 
		return $product_image;
	}

	public function get_product_details(){
		$search 	= $this->input->post('search');
		$request 	= $this->input->post();
		$userid 	= "";
		$user_type 	= $this->session->userdata('user_type');
		if($user_type != 1 && $user_type != ""){
			$userid = $this->session->userdata('userid');
		}
		$totalProduct = $this->Product_Model->productCount($search['value'],$userid);
		$data = $this->Product_Model->productPipline($search['value'],$request,$userid,$totalProduct->totalProduct);
		echo json_encode($data);
	}

	public function product_history(){
		$this->data['brands'] 			= $this->Brands_Model->getBrands();
		$this->data['categories'] 		= $this->Category_Model->getParentCategories("", "", "1", "");
		// echo"<pre>"; print_r($this->data['categories']); die();
		$this->data['page_name'] 		= 'product_history';
		$this->data['Breadcrumb_name'] 	= 'Product Report';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function get_seller_store($text=""){
		$result = "";
		if(isset($_POST['text'])){
			$text = $_POST['text'];
		}
		if($text){
			$get_store = $this->Product_Model->searchStore('',$text);		
			$i = 0; 
			$result = array();
			$rows = count($get_store);
			while( $i < $rows  ){
				$result[] = array(
					'label' => stripslashes($get_store[$i]->name.' - '.$get_store[$i]->store_name),
					'id'=> $get_store[$i]->store_id,
					'Value' => stripslashes($get_store[$i]->store_id),
				);
				$i++;
			}
		}
			echo json_encode($result);
	}

	public function get_product_history_filters(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('datepicker_from', 'date ', 'trim|xss_clean');
		$this->form_validation->set_rules('datepicker_to', 'date ', 'trim|xss_clean');
		$this->form_validation->set_rules('search_brand', 'brand ', 'trim|xss_clean');
		$this->form_validation->set_rules('search_category', 'category', 'trim|xss_clean');
		$this->form_validation->set_rules('search_seller', 'store name', 'trim|xss_clean');
		$this->form_validation->set_rules('search_status', 'status', 'trim|xss_clean');
		if ($this->form_validation->run() == TRUE){
			$dateFrom 		= $this->input->post('datepicker_from');
			$dateTo 		= $this->input->post('datepicker_to');
			$searchBrand 	= $this->input->post('search_brand');
			$searchCategory = $this->input->post('search_category');
			$searchStatus 	= $_POST['search_status'];
			$userid 		= "";
			$seller 		= "";
			$user_type 		= $this->session->userdata('user_type');
			if($user_type != 1 && $user_type != ""){
					$userid = $this->session->userdata('userid');
				}
			if($this->session->userdata('user_type')== 1){
					$seller = $this->input->post('search_seller');
					$sellerId = "";
					if(!empty($seller) && $seller != ""){
					$seller_id = $this->Product_Model->getSellerIdBysellerStore($seller); 
					$sellerId = $seller_id->seller_id;
					}
					$select = "";
					$data = $this->Product_Model->historyFilters($userid,$dateFrom,$dateTo,$seller,$searchStatus,$sellerId,$searchBrand,$searchCategory);
			} else {
				$data = $this->Product_Model->historyFilters($userid,$dateFrom,$dateTo,$seller,$searchStatus,$userid,$searchBrand,$searchCategory);	
			}
			$this->data['products']  = $data;
			$this->data['post_data'] = $this->input->post();
			$this->data['page_name'] = 'filtered_history';
			$this->load->view('admin/reporting_template', $this->data);
		}
		else{
			redirect(base_url('seller/product/product_history'));
		}
	}
	
	public function get_product_pending_details(){
		$search  = $this->input->post('search');
		$request = $this->input->post();
		$userid  = "";
		$user_type = $this->session->userdata('user_type');
		if($user_type != 1 && $user_type != ""){
			$userid = $this->session->userdata('userid');
		}
		$data = $this->Product_Model->productPendingPipline($search['value'],$request,$userid);
		echo json_encode($data);
	}

	public function get_subDetails(){ 
		$sp_id = $this->input->post('sp_id');
		$data = $this->Product_Model->get_subDetails($sp_id);
		echo json_encode($data);
	}
	public function get_inventories(){ 
		$product_id = $this->input->post('product_id');
		$data = $this->Product_Model->get_inventories($product_id);
	}
	public function get_storeDetails($prd_id, $seller= ""){ 
		// $prd_id = $this->input->post('prd_id');
		$data = $this->Product_Model->get_storeDetails($prd_id, $seller);
		echo json_encode($data);
	}
	
	public function front_product_details(){
		$data = $this->Product_Model->frontProductDetails();
		echo json_encode($data);
	}

	public function get_product($text=""){
		$result = "";
		if($this->input->post('text')){
			$text = $this->input->post('text');
		}
		$user_id = $this->session->userdata('userid');
		if($text){
			if($this->session->userdata['user_type'] != "1"){ 
				$get_product = $this->Product_Model->searchProduct('',$text,true,true,$user_id);		
			}else{
				$get_product = $this->Product_Model->searchProduct('',$text,true,"",$user_id);		
			}
			$i = 0; 
			$result = array();
			$rows = count($get_product);
			while( $i < $rows  ){	
				$result[] = array(
					'label' => stripslashes($get_product[$i]->product_name),
					'id'	=> $get_product[$i]->product_id,
					'value'	=> stripslashes($get_product[$i]->product_name),
				);
				$i++;
			}
		}
		echo json_encode($result);
	}

	public function get_product_for_accessories(){
		$text = $_POST['text'];
		$accessories_id = (isset($_POST['accessory_id']))?$_POST['accessory_id']:"";
		$product_id = $_POST['product_id'];
		$get_product = $this->Product_Model->searchProductForAccessories($accessories_id,$product_id,$text);		
		$result = array();
		foreach($get_product as $gp){
			$result[] = array(
				'label' => stripslashes($gp->product_name),
				'id'	=> $gp->product_id,
				'value'	=> stripslashes($gp->product_name),
			);
		}
		echo json_encode($result);
	}	

	public function update_product_stauts(){
		extract($this->input->post());
		$result = $this->Product_Model->updateProductStauts($product_id,$column,$value);
		echo json_encode($result);
	}

	//-----Inventiry------//
	public function inventory_view($delete=""){
		$this->data['page_name'] 		= 'inventory_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('inventory');
		$this->data['isScript'] 		= true;
		$this->data['delete'] = $delete;
		if($delete == "delete"){
			$this->data['Breadcrumb_name'] 	= $this->lang->line('deleted_inventory_list');
		}else if($delete == "warning"){
			$this->data['Breadcrumb_name'] 	= $this->lang->line('warned_inventory_list');
		}
		if(isset($_GET['prd_id']) && $_GET['prd_id'] != ""){
			$this->data['prd_id'] = $_GET['prd_id'];
		}
		if(isset($_GET['seller']) && $_GET['seller'] != ""){
			$this->data['seller'] = $_GET['seller'];
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,valid_from,valid_to,value,title,type',array("userid"=>$user_id,"active"=>"1","display_status"=>"1"));
		if($discount){
			$this->data['discountData'] =  $discount;
		}
		$this->load->view("admin/admin_template",$this->data);		
	}
	public function inventory_add(){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$returns 		 = $this->Returntype_Model->getReturnPolicyforProduct($user_id);
		$returns_default = $this->Returntype_Model->getUserReturnsByUniversalIdForDefault();
		$this->data['returns'] = null;
		if($returns){
		   $this->data['returns'] =  $returns;
		}		
		$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,valid_from,valid_to,value,title',array("userid"=>$user_id,"active"=>"1","display_status"=>"1"));
		// print_r($discount); print_r(date('Y-m-d')); die();
		if($discount){
			$discounts=array();
			foreach($discount as $d){
				if($d->valid_to >= date('Y-m-d')){
					$discounts[] = $d;
				}
			}
			$this->data['discountData'] =  $discounts;
		}

		$data['returns_default'] 	 =  $returns_default;	
		$this->data['conditionData'] =  $this->Product_Model->getConditionData('','');	
		//$this->data['variantData'] 	 =  $this->Product_Model->getVariantData('category.v_cat_id,category.v_cat_title,category.display_status,category.is_active','');	
		//echo "<pre>";print_r($this->data['variantData']);die();
		if($user_id == 1){
			$where =  array('user_id'=>$user_id ,'is_deleted'=>"0","is_active"=>"1");
		}else{
			$where = '(user_id="'.$user_id.'" OR user_id =1) AND is_deleted="0" AND is_active="1"';
		}		
		$this->data['shippingData'] 	=  $this->Product_Model->getData(DBPREFIX."_product_shipping","*",$where,0,'','shipping_id DESC');
		$this->data['warrantyData'] 	=  $this->Product_Model->getData(DBPREFIX."_warranty","*",array("is_active"=>"1"),0,'','warranty');
		$this->data['page_name']		= 'inventory_add';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('inventory');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}

	public function deleteVideoLink(){
		$id =  $_POST['id'];
		$response = $this->Product_Model->deleteVideoLink($id); 
		echo json_encode($response);
	}

	public function createInventory(){ 
		$this->load->library('form_validation');
		//echo "<pre>";print_r($this->input->post());die();
		$this->form_validation->set_rules('product_name', 'product name', 'trim|required');
		if ($this->form_validation->run() == TRUE){
			$seller_product 		= array();
			$seller_product_variant = array();
			$region 				= array();
			$product_inventory 		= array();
			$video_link 			= array();
			$media_id 				= array();
			$date 					= date('Y-m-d H:i:s');
			$date_utc 				= gmdate("Y-m-d\TH:i:s");
			$date_utc 				= str_replace("T"," ",$date_utc);
			$seller_id 				= ($this->session->userdata['user_type'] == 1)?$this->config->item('hubx_seller_id'):$this->session->userdata['userid'];
			$is_warehouse 			= "";
			$is_warehouse_approved 	= "";
			$i						= 0;
			$product_name 			= $_POST['product_name'];
			$condition_img 			= array();
			$pvi 					= 0;
			$is_changeCoverImage 	= array();
			$inventory_shipping = array();
			if(isset($_SESSION['is_zabee']) && $_SESSION['is_zabee'] == "1"){
				$is_zabee = $_SESSION['is_zabee'];
				$warehouse_id = $_SESSION['warehouse_id'];
			}else{
				$is_zabee = "0";
				$warehouse_id = "0";
			}
			foreach($_POST['condition_id'] as $index=>$condition_id){
				if(isset($_POST['warehouseId'.$condition_id]) && $_POST['warehouseId'.$condition_id] !=""){
					$warehouse_id = isset($_SESSION['warehouse_id'])?$_SESSION['warehouse_id']:"";
				}
				$seller_product[$i]['seller_id'] 			= $seller_id;
				if(isset($_POST['sp_id'.$condition_id]) && $_POST['sp_id'.$condition_id] ==""){
					$seller_product[$i]['created_date'] 	= $date_utc;
				}else{
					$seller_product[$i]['updated_date'] 	= $date_utc;
				}
				$seller_product[$i]['seller_product_description'] 	= isset($_POST['prod_des'.$condition_id])?$_POST['prod_des'.$condition_id]:"";
				$seller_product[$i]['condition_id'] 				= $condition_id;
				$seller_product[$i]['sp_id'] 						= (isset($_POST['sp_id'.$condition_id]))?$_POST['sp_id'.$condition_id]:"";
				$seller_product[$i]['return_id'] 					= (isset($_POST['returnId'.$condition_id]))?$_POST['returnId'.$condition_id]:"";
				if($_SESSION['is_zabee'] == "0" && isset($_POST['shippingIds'.$condition_id])){
					$seller_product[$i]['shipping_ids']				= implode(',',$_POST['shippingIds'.$condition_id]);
				}else{
					$where =  array('user_id'=>"1" ,'is_deleted'=>"0","is_active"=>"1");
					$shipping_ids = $this->Product_Model->getData(DBPREFIX."_product_shipping","GROUP_CONCAT(shipping_id) as shipping_id",$where,0,'','shipping_id DESC');
					$seller_product[$i]['shipping_ids'] = $shipping_ids[0]->shipping_id;
				}
				
				$variantArray = array();
				$productVariant = array();
				$variant = array();
				$pc = count($_POST['price'.$condition_id]);
				for($pi=0; $pi<$pc; $pi++){
					$inventory_shipping[$condition_id][$pi] = isset($_POST['shipping'.$condition_id][$pi])?implode(",",$_POST['shipping'.$condition_id][$pi]):implode(',',$_POST['shippingIds'.$condition_id]);
					$pv_id = "";
					$variant_group = "";
					$variant_cat_group = "";
					if(isset($_POST['variant'.$condition_id])){
						$vci=0;
						$vc = count($_POST['variant'.$condition_id]);
						for($vi =0; $vi<$vc; $vi++){
							if(isset($_POST['variant'.$condition_id][$vi][$pi])){
								$variant[$pi][] = $_POST['variant'.$condition_id][$vi][$pi];
								$variantArray[$pi][] = $_POST['variant'.$condition_id][$vi][$pi];
							}
						}
						sort($variantArray[$pi]);
						$variant_group = implode(',',$variantArray[$pi]);
						$variant_cat_group = implode(',',$_POST['variant_cat'.$condition_id]);
					}
					$discount = (isset($_POST['discount'.$condition_id][$pi]))?$_POST['discount'.$condition_id][$pi]:"";
					$warranty = (isset($_POST['warranty'.$condition_id][$pi]))?$_POST['warranty'.$condition_id][$pi]:"";
					$hubx_id  = (isset($_POST['hubx_id'.$condition_id][$pi]))?$_POST['hubx_id'.$condition_id][$pi]:"";
					$productVariant[] = array('seller_id'=>$seller_id,'condition_id'=>$condition_id,'return_id'=>$_POST['return_id'],'variant_group'=>$variant_group,'price'=>$_POST['price'.$condition_id][$pi],'quantity'=>$_POST['quantity'.$condition_id][$pi],'variant_cat_group'=>$variant_cat_group,'pv_id'=>$_POST['pv_id'.$condition_id][$pi],'discount'=>$discount, 'total_qty'=>$_POST['total_qty_'.$condition_id][$pi], 'previous_qty'=>$_POST['previous_qty_'.$condition_id][$pi],"warranty"=>$warranty,"hubx_id"=>$hubx_id,"seller_sku"=>$_POST["seller_sku".$condition_id][$pi]);
					$pvi++;
				}
				$seller_product_variant[$i] = $variant;
				$proVariant[$i] = $productVariant;
				//------------------------------------------------//
				$region = (isset($_POST['statesData']))?$_POST['statesData']:"";
				$video_link["condition_video_link".$condition_id] = (isset($_POST['condition_video_link'.$condition_id]))?$_POST['condition_video_link'.$condition_id]:"";
				$media_id["media_id".$condition_id] = (isset($_POST['media_id'.$condition_id]))?$_POST['media_id'.$condition_id]:"";
				$i++;
			}
			if(isset($_POST['product_id']) && $_POST['product_id'] !="" && $_POST['product_id'] !=0){
				$product_id = $_POST['product_id'];
				$result = $this->Product_Model->createInventory($product_name,$product_id,$seller_product,$seller_product_variant,$_POST['dummy_id'],$region,$product_inventory,$proVariant,$seller_id,$video_link,$media_id,$warehouse_id, $inventory_shipping, "web");
			} 
			if($result){
				$this->session->set_flashdata("success",$this->lang->line('inventory_created'));
				redirect(base_url()."seller/product/inventory_view","refresh");
			}else{
				$this->session->set_flashdata("error",$this->lang->line('pdt_err'));
				$this->RefreshListingPage();
			}
		}
		else{
			$this->create();
		}
	}

	public function getproductforinventory($product_id="")
	{
		if(isset($_POST['id'])){
			$product_id = $_POST['id'];
		}
		if(isset($this->userData[0]['userid'])){
			$seller_id = $this->userData[0]['userid'];
		}else{
			$seller_id = $this->userData[0]['admin_id'];
		}
		echo json_encode(array("status"=>"success","data"=>$this->Product_Model->getProductForInventory($seller_id,$product_id)));
	}

	public function get_product_backend($text)
	{
		if(isset($this->userData[0]['userid'])){
			$seller_id = $this->userData[0]['userid'];
		}else{
			$seller_id = $this->userData[0]['admin_id'];
		}
		$get_product = $this->Product_Model->searchProductBackend('',$text,$seller_id,true);		
		$i = 0;
		$result = array();
			$rows = count($get_product);
			while( $i < $rows  ){
				$result[] = array(
					'label' => stripslashes($get_product[$i]->product_name),
					'id'=> $get_product[$i]->product_id,
					'value'=> stripslashes($get_product[$i]->product_name),
				);
				$i++;
			}
		echo json_encode($result);
	}

	public function get_inventory_details2(){
		$prd_id = (isset($_GET['prd_id']) && $_GET['prd_id'] != "")?$_GET['prd_id']:"";
		$seller = (isset($_GET['seller']) && $_GET['seller'] != "")?$_GET['seller']:"";
		$delete = (isset($_GET['delete']) && $_GET['delete'] != "")?$_GET['delete']:"";
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$usertype = $this->session->userdata('user_type');
		$search = $this->input->post("sSearch");
		$offset = $this->input->post("iDisplayStart");
		$length = $this->input->post("iDisplayLength");
		$this->load->library('datatables');
		$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,valid_from,valid_to,value,title',array("userid"=>$user_id,"active"=>"1","display_status"=>"1"));
		if($discount){
			$this->data['discountData'] =  $discount;
		 }
		 if($usertype == 1 && $seller != ""){
			 $user_id = $seller;
		 }else if($usertype == 1 && $seller == ""){
			 $user_id = "";
		 }
		// $count = $this->Product_Model->inventoryPiplineCount($search, $user_id, $prd_id);
		$this->Product_Model->inventoryPipline2($search, $offset, $length, $user_id, $usertype, $prd_id,$delete);
		/*$delete_link = "<a href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>".$this->lang->line('are_you_sure')."</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/product/delete/$1') . "'>".$this->lang->line('im_sure')."
						</a> <button class='btn btn-primary po-close'>".$this->lang->line('no')."</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i>".$this->lang->line('delete_itm')."</a>";*/
		if($delete == ""){
			$action = '<div style="width:max-content"><a href="javascript:void(0);" onclick="askUpdate(\'$1\', \'$2\');" class="btn"><i class="fas fa-edit"></i></a><a href="javascript:void(0);" onclick="askDelete(\'$1\');" class="btn"><i class="fas fa-trash"></i></a></div>';
			$this->datatables->add_column("Actions", $action, "id, discount_id");
		}else{
			$action = '<a href="javascript:void(0);" onclick="askDelete(\'$1\');" class="btn" title="Re-create this inventory"><i class="fas fa-retweet"></i></a>';
			$this->datatables->add_column("Actions", $action, "id");
		}
		$this->datatables->unset_column("id");
		$this->datatables->unset_column("discount_id");
		echo $this->datatables->generate();
		//echo $this->db->last_query(); die();
	}
	
	private function RefreshListingPageToImage($product_ids, $img = "", $prod_id, $checkProduct, $isLink, $url="")
	{
		if($url){
			redirect($url,"refresh");
		} else {
			if($checkProduct == false && $_SESSION['user_type'] != '1'){
				$exist = 0;
				$allParams = $prod_id."-"."0"."-"."0"."-".$exist."-".$isLink;
				$gibberish = base64_encode($allParams);
				redirect(base_url()."seller/product/selectimage/".$gibberish,"refresh");
			} else if($checkProduct == true && $_SESSION['user_type'] != '1'){
				redirect(base_url()."seller/product","refresh");
			} else if($checkProduct == false && $_SESSION['user_type'] == '1'){
				$exist = 0;
				$allParams = $prod_id."-"."0"."-"."0"."-".$exist."-".$isLink;
				$gibberish = base64_encode($allParams);
				redirect(base_url()."seller/product/selectimage/".$gibberish,"refresh");
			} else if($checkProduct == true && $_SESSION['user_type'] == '1'){
				$exist = 1;
				$allParams = $prod_id."-"."0"."-"."0"."-".$exist."-".$isLink;
				$gibberish = base64_encode($allParams);
				redirect(base_url()."seller/product/selectimage/".$gibberish,"refresh");
			}
		}		
	}
	
	public function askdelete(){
		$id 			= $this->input->post('id');
		$product_name 	= $this->Product_Model->getProductName($id);
		$product_name 	= $product_name[0]->product_name;
		$created_id 	= $this->Product_Model->getUserIdByProductId($id);
		$created_id 	= $created_id[0]->created_id;
		$p_delete 		= $this->Product_Model->product_delete($id);
		if($p_delete){
			$action 	= $this->lang->line('your_pdt').$product_name.$this->lang->line('admin_errr') ; //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
			$not_type 	= '0';
			$from 		= "1";
			$to 		= $created_id;
			$user_type 	= "2";
			saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
			$resp['success'] = true;
			echo json_encode($resp, JSON_UNESCAPED_UNICODE);
		}		
	}

	public function selectimageforspid($sp_id){
		$post_data = $this->input->post();	
	}

	public function updateInventory($id, $discount_id=0){
		$resp['success'] = 0;
		$resp['result']  =  "";
		$Inventorydata   = $this->Product_Model->inventory_update($id);
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,valid_from,valid_to,value,title,type',array("userid"=>$user_id,"active"=>"1","display_status"=>"1"));
		if($discount){
			$resp['discountData'] =  $discount;
		 }
		if($Inventorydata){
			$resp['success'] = 1;
			$resp['result'] = $Inventorydata;
		}
		echo json_encode($resp);
	}

	public function updateQuantityandPriceforInventory($inventory_id = ""){
		$usertype = $this->session->userdata('user_type');
		$post_data = $this->input->post();
		// echo"<pre>"; print_r($post_data); die();
		$date = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));
		$this->data['inventory_id'] = $inventory_id;
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}else{
			$user_id = $this->session->userdata('admin_id');	
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('Quantity','Quantity','trim|required');
		$this->form_validation->set_rules('Price','Price','trim|required');
		$this->form_validation->set_rules('modal_id','Modal Id','trim|required');
		if($this->form_validation->run() == TRUE){
			$qty = ($post_data['Quantity'] - ($post_data['Quantity'] - $post_data['sell_qty'])) + $post_data['Quantity'];
			$data = array("updated_date"=>$date,"inventory_id"=>$post_data['modal_id'],"price"=>$post_data['Price'],"quantity"=>$qty,"discount"=>$post_data['discount']);
			// echo"<pre>"; print_r($data); die();
			$resp = $this->Product_Model->update_quantityandprice($data, $user_id, $usertype);
			if($resp){
				redirect(base_url('seller/product/inventory_view?status=success'));
				die();
			}
		/* End */
		}
		$this->data['page_name'] 		= 'inventory_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('inventory');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}

	public function selectimage($encrpytedParams){
		$allParams 		= base64_decode($encrpytedParams);
		$breakParams 	= explode("-", $allParams);
		$product_ids 	= $breakParams[0];
		$p 				= $breakParams[1];
		$s 				= $breakParams[2];
		$exist			= $breakParams[3];
		if(array_key_exists("4", $breakParams)){
			$isLink = $breakParams[4];
		} else {
			$isLink = 0;
		}
		$post_data 					= $this->input->post();
		$this->data['pagestatus'] 	= $s;
		$this->data['exist'] 		= $exist;
		$prod_images 				= $this->Product_Model->productImages($product_ids);
		$this->data['prod_images_thumbnail'] = explode(",",$prod_images[0]->thumbnail);
		if($this->data['prod_images_thumbnail'][0] == ""){
			$forimageloop = 0;
		} else {
			$forimageloop = sizeof($this->data['prod_images_thumbnail']);
		}
		$this->data['forimageloop'] = $forimageloop;
		$this->data['is_local'] 	= $prod_images[0]->is_local;
		if($forimageloop == 1){
			if($p == 0 && $exist == 0){
				$p_image = $this->Product_Model->primary_image_singleimage($this->data['prod_images_thumbnail'], $product_ids);
				if($p_image){
					redirect(base_url('seller/product'));
					die();
				}
			}	
			if($p == 0 && $exist == 1){
				redirect(base_url('seller/product'));
				die();
			}	
			else if($p == 1) {
				$this->forSelectimageOnly($product_ids, $status = "single",  $post_data, $s, $isLink );	
			} 
		} else {
			$this->forSelectimageOnly($product_ids, $status = "many", $post_data, $s, $isLink);
		}
	}

	private function forSelectimageOnly($product_ids, $status = "", $data, $pagestatus, $isLink){
		$usertype = $this->session->userdata('user_type');
		$post_data = $this->input->post();
		$this->data['isLink'] = $isLink;
		$productIvlink = $this->Product_Model->change_primary($product_ids);
		if(isset($_FILES["product_image"])){
			$new_img_url = (isset($_FILES["product_image"]))?$this->uploadProductFiles($_FILES,true):'';
			if($new_img_url['thumbnail'][0] == "./uploads/product/thumbs/Preview.png" || $new_img_url['thumbnail'][0] == ""){
				$concatProduct = $productIvlink[0]->thumbnail;	
			} else {
				if($productIvlink[0]->thumbnail == ""){
					$concatProductthumb = $new_img_url['thumbnail'][0];
					$concatProductorig = $new_img_url['thumbnail'][0];
				} else {
					$new_img_url = $new_img_url['thumbnail'][0];
					$concatProduct = $productIvlink[0]->thumbnail . "," . $new_img_url; //concats with the rest of thumnails in db
					$concatProductorig = $productIvlink[0]->iv_link . "," . $new_img_url;
				}
			}
		} else {
			$concatProduct = $productIvlink[0]->thumbnail;
			$concatProductorig = $productIvlink[0]->iv_link;
		}
		$this->data['status'] = $status;
		$this->form_validation->set_rules('primary_image','Primary Image','required');
		if($this->form_validation->run() === true){
			$this->data['abc'][1] = $post_data['primary_image'];
			$this->data['abc'][0] = "";
			$p_image = $this->Product_Model->primary_image($post_data, $this->data['abc'], $product_ids, $concatProduct, $concatProductorig);
			if($p_image){
				redirect(base_url('seller/product'));
				die();
			}
		} 
		$this->data['product_id'] 		= $product_ids;
		$this->data['page_name'] 		= 'product_image';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('pdt_imgs');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function togglechange(){
		$id 			= $this->input->post('id');
		$value 			= $this->input->post('value');
		$toggle_tsp 	= $this->Product_Model->toggle_tsp($id, $value);
		$toggle_tspv 	= $this->Product_Model->toggle_tspv($id, $value);
		if($toggle_tsp){
			 $resp['success'] = true;
			 echo json_encode($resp, JSON_UNESCAPED_UNICODE);
		}
	}

	public function pending_view(){
		if(!isset($_GET['v'])){
			$_GET['v'] = "0";
		}
		if($_GET['v'] == "1"){
			$this->data['Breadcrumb_name'] = $this->lang->line('approved_products');
		}else if($_GET['v'] =="2"){
			$this->data['Breadcrumb_name'] = $this->lang->line('rejected_products');
		}else{
			$this->data['Breadcrumb_name'] = $this->lang->line('pending_products');
		}
		$this->data['page_name'] = 'pending_products';
		$this->data['isScript']  = true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function pendingProducts(){
		$search 	= $this->input->post('search');
		$request 	= $this->input->post();
		$userid 	= "";
		$user_type = $this->session->userdata('user_type');
		if($user_type != 1 && $user_type != ""){
			$userid = $this->session->userdata('userid');
		}
		$data = $this->Product_Model->pending_Products($search['value'],$request,$userid);
		echo json_encode($data);
	}

	public function products_view(){
		$this->data['page_name'] 		= 'products'; 
		$this->data['Breadcrumb_name'] 	= $this->lang->line('products');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function approveProduct(){
		$id 			= $this->input->post('id');
		$productid 		= $this->input->post('productid');
		$sellerid 		= $this->input->post('sellerid');
		$product_name 	= $this->Product_Model->getProductName($productid);
		$product_name 	= $product_name[0]->product_name;
		$approveit 		= $this->Product_Model->product_approve($id,$productid,$sellerid);
		if($approveit){
			$action 	= $this->lang->line('your_pdt').$product_name.$this->lang->line('admin_errrr') ; //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
			$not_type 	= '0';
			$from 		= "1";
			$to 		= $sellerid;
			$user_type 	= "2";
			saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
			$resp['success'] = true;
			echo json_encode($resp, JSON_UNESCAPED_UNICODE);
		}
		$this->session->set_flashdata('msg', $this->lang->line('pdt_approved'));
	}

	public function declineProduct(){
		$reason 		= $this->input->post('reason');
		$productid 		= $this->input->post('productid');
		$sellerid 		= $this->input->post('sellerid');
		$product_name 	= $this->Product_Model->getProductName($productid);
		$product_name 	= $product_name[0]->product_name;
		$declineit 		= $this->Product_Model->product_decline($productid,$sellerid,$reason);
		if($declineit){
			$action 	= $this->lang->line('your_pdt').$product_name.$this->lang->line('admin_errrrr') ; //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
			$not_type 	= '0';
			$from 		= "1";
			$to 		= $sellerid;
			$user_type 	= "2";
			saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
			$resp['success'] = true;
			echo json_encode($resp, JSON_UNESCAPED_UNICODE);
		}
		$this->session->set_flashdata('msg', $this->lang->line('pdt_rejected'));
	}

	public function edit_product(){
		$for_redirecting = $_SERVER['HTTP_REFERER'];
		if($for_redirecting == "http://localhost/zabee/portal/seller/product"){
			$redirect_to = 0;
		} else {
			$redirect_to = 1;
		}
		$this->data['redirect_to'] 		= $redirect_to;
		$product_id 					= $this->uri->segment(4);
		$data							= $this->Product_Model->get_product_data($product_id);
		$this->data['Productdata']		= $data;
		$this->data['page_name'] 		= 'product_edit';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('edit');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function save_edit_product($redirect_to){
		$product_id		= $_POST['product_id'];
		$product_name	= $_POST['product_name'];
		$product_desc	= $_POST['product_description'];
		$key = array('product_id'=>$product_id,
						'sp_id'		=>$_POST['sp_id'],
						'pv_id'  	=>$_POST['pv_id'],
						'keywords' 	=>$_POST['product_keyword']	
					);
		$update=$this->Product_Model->save_edit_product($product_id,$product_name,$product_desc,$key);
		if($redirect_to == 0){
			redirect(base_url('seller/product?status=success'));
		} else {
			redirect(base_url('seller/product/pending_view'));
		}
	}

	public function deletePv(){
		$pv_id = $_POST['pvId'];
		$result = $this->Product_Model->deletePv($pv_id);
		if($result){
			echo json_encode(TRUE);
		}else{
			echo json_encode(FALSE);
		}
	}

	public function accessories($id = ""){ 
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($id != ""){
			$this->data['getAccData'] = $this->Product_Model->getAccDataByProdId($user_id, $id);
		}
		//echo "<pre>";print_r($this->data['getAccData']);die();
		$this->data['page_name'] 		= 'product_accessories';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('accessories');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}

	// OLD METHOD
	// public function addAccessories(){
	// 	if($this->session->userdata('userid')){
	// 		$user_id = $this->session->userdata('userid');
	// 	} else {
	// 		$user_id = $this->session->userdata('admin_id');
	// 	}
	// 	$data   = $this->input->post();
	// 	$table  = DBPREFIX."_product_accessories";
	// 	$return = true;
	// 	//echo "<pre>";print_r($this->input->post());die();
	// 	foreach($data['accessories'] as $acc_id){
	// 		$saveData = array("product_id"=>$data['product_id'],'accessory_id'=>$acc_id, "seller_id" => $user_id);
	// 		//echo "<pre>";print_r($data);print_r($saveData);die();
	// 		if(!$this->Product_Model->ProdNameAuthForAccessories($data['product_name'])){
	// 			$return = false;
	// 			$this->session->set_flashdata("error_2",$this->lang->line('pdt_not_found'));
	// 			redirect(base_url("seller/product/accessories"));
	// 		} 
	// 		if($data['prod_acc_id'] == ""){
	// 			//echo "<pre>";print_r($saveData);//die();
	// 			$this->Product_Model->saveData($saveData,$table);
	// 			$return = true;
	// 			$this->session->set_flashdata("success",$this->lang->line('accessories_add'));
	// 		} else if($data['prod_acc_id'] != "") {
	// 			$data['accessories'] = array_unique($data['accessories']);
	// 			//echo "<pre>";print_r($data);die();
	// 			$resp = $this->Product_Model->updateAccData($data,$table,$data['prod_acc_id'], $user_id);
	// 			//print_r($resp);die();
	// 			if(!$resp){
	// 				$return = false;
	// 				$this->session->set_flashdata("error",$this->lang->line('accessories_not_update'));
	// 				redirect(base_url("seller/product/prodAccessories"));
	// 			} else {
	// 				//die();
	// 				$return = true;
	// 				$this->session->set_flashdata("success",$this->lang->line('accessories_update'));
	// 				redirect(base_url("seller/product/prodAccessories"));
	// 			}
	// 		} else {
	// 			$return = false;
	// 			$this->session->set_flashdata("error",$this->lang->line('accessories_not_add'));
	// 			redirect(base_url("seller/product/prodAccessories"));
	// 		}
	// 	}
	// 	//die();
	// 	if($return){
	// 		$this->session->set_flashdata("success",$this->lang->line('accessories_add'));
	// 		redirect(base_url("seller/product/prodAccessories"));
	// 	}
	// }

	public function addAccessories(){
		$user_id = $this->session->userdata('userid');
		$data   = $this->input->post();
		$table  = DBPREFIX."_product_accessories";
		$return = true;
		$acc_id = implode(",", $data['accessories']);
		// print_r($data); die();
		if($data['prod_acc_id'] == ""){
			$saveData = array("product_id"=>$data['product_id'],'accessory_id'=>$acc_id, "seller_id" => $user_id);
			// print_r($saveData); die();
			$this->Product_Model->saveData($saveData,$table);
			$return = true;
			$this->session->set_flashdata("success",$this->lang->line('accessories_add'));
		} else if($data['prod_acc_id'] != "") {
			$acc_data = array("accessory_id" => $acc_id);
			$where = array("id"=> $data['id']);
			$resp = $this->Product_Model->updateData($table, $acc_data,$where);
			//$resp = $this->Product_Model->updateAccData($acc_id, $table, $user_id, $data['id']);
			if(!$resp){
				$return = false;
				$this->session->set_flashdata("error",$this->lang->line('accessories_not_update'));
				redirect(base_url("seller/product/prodAccessories"));
			} else {
				$return = true;
				$this->session->set_flashdata("success",$this->lang->line('accessories_update'));
				redirect(base_url("seller/product/prodAccessories"));
			}
		} else {
			$return = false;
			$this->session->set_flashdata("error",$this->lang->line('accessories_not_add'));
			redirect(base_url("seller/product/prodAccessories"));
		}
		if($return){
			$this->session->set_flashdata("success",$this->lang->line('accessories_add'));
			redirect(base_url("seller/product/prodAccessories"));
		}
	}

	public function deleteData(){
		$id 			= $this->input->post('key');
		$table 			= DBPREFIX."_product_media";
		$where 			= array("media_id"=>$id);
		$return 		= $this->Product_Model->deleteData($id,$table,$where);
		$params 		= array('filename'=>$_POST['name'],'filetype'=>"product");
		$upload_server 	= $this->config->item('media_url').'/file/delete_media';
		$file 			= $this->Utilz_Model->curlRequest($params, $upload_server, true,false);
		echo json_encode($return);
	}

	public function changeCoverImage(){
		$return 		= array("status"=>0,"erorr"=>"");
		$product_id 	= $this->input->post('pi');
		$media_id 		= $this->input->post('i');
		$condition_id 	= $this->input->post('c');
		$where 			= array("product_id"=>$product_id,"condition_id"=>$condition_id);
		$table 			= DBPREFIX."_product_media";
		$data 			= array("is_cover"=>"0");
		$response 		= $this->Product_Model->updateData($table,$data,$where);
		if($response){
			$where 	  = array("product_id"=>$product_id,"condition_id"=>$condition_id,"media_id"=>$media_id);
			$data 	  = array("is_cover"=>"1");
			$response = $this->Product_Model->updateData($table,$data,$where);
			$return   = array("status"=>1,"erorr"=>"");
			echo json_encode($return);
		}else{
			echo json_encode($return);
		}
	}

	public function demo_file_upload(){
		$this->data['page_name'] 		= 'demo_file_upload';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('accessories');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template", $this->data);
	}
	
	public function prodAccessories()
	{
		$this->data['page_name'] 		= 'product_accessory_view';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('product_accessories');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
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
		$this->Product_Model->getproductAccessories($user_id, $search, $offset, $length);	
		$delete_link = "<a class='actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>Are you sure?</p>
			  <a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/product/prod_acc_delete/$1') . "'>I am Sure
			  </a> <button class='btn btn-primary po-close'>No</button>\"  rel='popover'><i class=\"fa fa-trash\"></i> Delete All Accessories</a>";
				$action = '<div class="btn-group text-left">'
					. '<button type="button" class="btn btn-secondary btn-xs btn-primary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		  <ul class="dropdown-menu except-prod  pull-right" role="menu">
		   <li class="pl-3"><a href="' . site_url('seller/product/accessories/$1') . '" class="actions"><i class="fa fa-edit"></i> Edit </a></li>
		   <li class="pl-3">' . $delete_link . '</li>
		   </ul>
		  </div>';
        $this->datatables->add_column("Actions", $action, "id");
		$this->datatables->unset_column("id");
		echo $this->datatables->generate();
    }
	
	public function prod_acc_delete($id = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$resp = $this->Product_Model->delete_prod_acc($id, $user_id);	
		if($resp){
			$this->session->set_flashdata("success",$this->lang->line('accessories_del'));
			$respo['status'] = 'success';
		} 
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}
	
	public function saveNotifications($msg = ""){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($msg == "admin"){
			$action 	= $this->lang->line('product_accessory_add'); //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
			$not_type 	= '0';
			$from 		= $user_id;
			$to 		= '1';
			$user_type 	= "0";
			saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
		}
		if($msg == "seller"){
			$action 	= $this->lang->line('product_accessory_created'); //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
			$not_type 	= '0';
			$from 		= '1';
			$to 		= $user_id;
			$user_type 	= "2";
			saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
		}
	}

	function test(){
		$vc 				= array(4,5);
		$product_id 		= 10381;
		$product_variant 	= $this->Product_Model->getData(DBPREFIX."_product_variant","pv_id,variant_group,variant_cat_group",array('product_id'=>$product_id));
		foreach($product_variant as $pv){
			$variant 		= explode(",",$pv->variant_group);
			$variant_cat 	= explode(",",$pv->variant_cat_group);
			$result 		= $this->variant_filter($variant,$variant_cat,$vc);
			$this->db->where("pv_id",$pv->pv_id);
			$this->db->update(DBPREFIX."_product_variant",array("variant_group"=>$result['variant_group'],"variant_cat_group"=>$result['variant_cat_group']));
		}
	}

	function variant_filter($variant,$existing_vcat,$variant_cat=""){
		$variant_temp = array();
		foreach($existing_vcat as $v){
			if(in_array($v,$variant)){
				$key = array_search($v,$variant);
				array_push($variant_temp,$variant[$key]);
			}
		}
		$return = implode(",",$variant_temp);
		return $return;
	}
	public function get_inventory_detail($product_id){
		$totalProduct = $this->Product_Model->storeCount($product_id);
		$data = $this->Product_Model->productPipline($search['value'],$request,$userid,$totalProduct->totalProduct);
		echo json_encode($data);
	}
	public function updateProductTitle(){
		$title = trim($this->input->post("title"));
		$slug = str_replace(" ","-",$title);
		$slug = $this->slugify($slug);
		$product_id = $this->input->post("product_id");
		$check = $this->checkProductTitle($title, $slug,$product_id);
		if(!$check){
			$data = array("status"=>false, "slug"=>$slug , "message"=>"Product Title or Slug already exists");
		}else{
			$data = array("product_name"=>$title,"slug"=>$slug);
			$where = array("product_id"=>$product_id);
			$return = $this->Product_Model->updateData(DBPREFIX."_product",$data,$where);
			$data = array("status"=>$return,"slug"=>$slug , "message"=>"Product title changed successfully.");
		}
		echo json_encode($data);
	}

	public function checkProductTitle($title = "", $slug = "", $product_id){
		$where = "(product_name='".$title."' OR slug='".$slug."')";
		if($product_id != ''){
			$where .= " AND product_id != '".$product_id."'";
		}
		$check = $this->Product_Model->getData(DBPREFIX."_product","",$where);
		if(isset($check[0]->product_id) && $check[0]->product_id != ""){
			return false;
		}else{
			return true;
		}
	}

	public function checkTitle($title){
		$where = "product_name = '".trim($title)."'";
		$product_id = $this->input->post('product_id');
		if($product_id != ''){
			$where .= " AND product_id != '".$product_id."'";
		}
		$check = $this->Product_Model->getData(DBPREFIX."_product","",$where);
		if(isset($check[0]->product_id) && $check[0]->product_id != ""){
			$this->form_validation->set_message('checkTitle', '<span class="text-danger">Product already exists.</span>');
			return false;
		}else{
			return true;
		}
	}

	public function checkSlug($slug){
		$slug = $this->slugify($slug);
		$where = "slug = '".$slug."'";
		$product_id = $this->input->post('product_id');
		if($product_id != ''){
			$where .= " AND product_id != '".$product_id."'";
		}
		$check = $this->Product_Model->getData(DBPREFIX."_product","",$where);	
		if(isset($check[0]->product_id) && $check[0]->product_id != ""){
			$this->form_validation->set_message('checkSlug', '<span class="text-danger">Slug already exists.</span>');
			return FALSE;
		}else{
			return TRUE;
		}
	}
	public function deleteCondition(){
		$condition_id = $this->input->post("condition_id");
		$seller_id = $this->session->userdata['userid'];
		$sp_id = $this->input->post("sp_id");
		$return = $this->Product_Model->deleteCondition($condition_id,$seller_id,$sp_id);
		if($return){
			echo json_encode(array("status"=>1));
		}else{
			echo json_encode(array("status"=>0));
		}
	}
	public function deleteInventory(){
		$id = $this->input->post("id");
		if($id){
			$return = $this->Product_Model->deleteInventory($id,"3");
			$this->session->set_flashdata('success', 'Inventory Delete Successfully!');
			echo json_encode(array("status"=>1,"message"=>"Inventory Delete Successfully!"));
		}else{
			echo json_encode(array("status"=>0,"message"=>"Inventory Id Missing!"));
		}
	}
	public function recreateInventory(){
		$id = $this->input->post("id");
		if($id){
			$return = $this->Product_Model->deleteInventory($id,"1");
			$this->session->set_flashdata('success', 'Inventory has been re-created Successfully!');
			echo json_encode(array("status"=>1,"message"=>"Inventory has been re-created Successfully!"));
		}else{
			echo json_encode(array("status"=>0,"message"=>"Inventory Id Missing!"));
		}
	}
	// Hubx Product Code
	public function hubxProductList(){
		$this->data['page_name'] 		= 'hubx_products';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('product');
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}
	public function get_hubx_products(){
		$search  = $this->input->post('search');
		$request = $this->input->post();
		$userid  = "";
		$user_type = $this->session->userdata('user_type');
		if($user_type != 1 && $user_type != ""){
			$userid = $this->session->userdata('userid');
		}
		$data = $this->Product_Model->hubxProductPipline($search['value'],$request,$userid);
		echo json_encode($data);
	}
	public function getHubxById(){
		$hubx_id = $this->input->post("hubx_id");
		if($hubx_id){
			$tablename = DBPREFIX."_hubx_product";
			$select = "manufacturer,description,mpn";
			$where = array("hubx_id"=>$hubx_id);
			$data = $this->Product_Model->getData($tablename,$select,$where,"","","","","","","","",true);
			if($data){
				$plus = 1;
				if($data->description){
					$prod_search = urlClean(str_replace('-',' ',$data->description));	
					$word = explode(" ",$prod_search);
					$prod_search = "";
					foreach($word as $w){
						if(strlen($w) < 3){
							$prod_search .= $w."__* ";
						}else{
							if($plus==1){
								$prod_search .="+";
								$plus++;	
							}
							$prod_search .= $w." ";
						}
					}
					$prod_search = trim($prod_search);
				}
				$productList = $this->Product_Model->getProducListForHubx($prod_search);
				//echo "<pre>";print_r($productList);die();
				$tablename = DBPREFIX."_brands";
				$select = "brand_id";
				$where = array("brand_name"=>$data->manufacturer);
				$brand_id = $this->Product_Model->getData($tablename,$select,$where,"","","","","","","","",true);
				if(!$brand_id){
					$date_utc = gmdate("Y-m-d\TH:i:s");
					$date_utc = str_replace("T"," ",$date_utc);
					$brand_data = array("brand_name"=>$data->manufacturer,"created_by"=>"hubx","created_date"=>$date_utc);
					$this->db->insert($tablename, $brand_data);
					$brand_id = $this->db->insert_id();
				}else{
					$brand_id = $brand_id->brand_id;
				}
				$data->variant = $this->getVariantByHubxTitle($data->description);
				$data->brand_id = $brand_id;
				echo json_encode(array("status"=>1,"data"=>$data,"productList"=>$productList));
			}else{
				echo json_encode(array("status"=>0,"data"=>"Product not found in db."));	
			}
		}else{
			echo json_encode(array("status"=>0,"data"=>"Id is missing."));
		}
	}
	public function getVariantByHubxTitle($title="",$group_by="",$select="v.v_cat_id,v.v_title,v.v_id,v_cat_title"){
		$word = explode(" ",$title);
		$tablename = DBPREFIX."_variant v";
		$join = array(DBPREFIX."_variant_category vc"=>"vc.v_cat_id = v.v_cat_id");
		$v_cat_id = $this->Product_Model->getData($tablename,$select,$word,"v.v_title",$group_by,"",$join,"","","","");
		return $v_cat_id;
	}
	public function insertHubxProducts(){
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');
		$hubx_info = $this->session->userdata('hubx_info');
		$access_token = "";
		$expires_in = "";
		$time = time();
		$params = array('user_id'=>$user_id,"user_type"=>$user_type,"client_id"=>$this->config->item("hubx_client_id"),"client_secret"=>$this->config->item("hubx_client_secret"));
		$this->load->library('Hubx',$params);
		if($hubx_info){
			$hubx_info = json_decode($hubx_info);
			if($time > $hubx_info->expires_in){
				$hubx_info = $this->getAccessToken($user_id);
				$hubx_info = json_decode($hubx_info);
			}
			
		}else{
			$hubx_info = $this->getAccessToken($user_id);
			$hubx_info = json_decode($hubx_info);
		}
		$this->session->set_userdata('access_token',$hubx_info->access_token);
		$this->session->set_userdata('expires_in',$hubx_info->expires_in);
		if($hubx_info->access_token){
			$server_output = $this->hubx->get_hubx_product_list($hubx_info->access_token);
			$server_output = json_decode($server_output);
			if($this->session->userdata('user_type') == 1){
				$seller_id = "60dc1beac4485";
			}else{
				$seller_id = $user_id;
			}
			if($server_output){
				foreach($server_output->products as $o){
					$attr = $o->attributes;
					if($o->moq == 1 && !empty($attr)){
						$profit = ($o->unitPrice/100)*15;
						$price = $o->unitPrice+$profit;
						$data = array("hubx_id"=>$o->id,
										"seller_id"=>$seller_id,
										"manufacturer"=> $o->manufacturer,
										"description"=> $o->description,
										"mpn"=> $o->mpn,
										"moq"=> $o->moq,
										"mxq"=> $o->mxq,
										"unit_price"=> $price,
										"part_number"=> $o->partNumber,
										"lead_time_days"=> $o->leadTimeDays,
										"availability"=> $o->availability,
										"exw"=> $o->exw,
										"is_active"=> $o->isActive,
										"attributes"=> ($o->attributes)?serialize($o->attributes):"",
										"comments"=> ($o->comments)?serialize($o->comments):"",
										"prices"=> ($o->prices)?serialize($o->prices):"");
						//echo "<pre>";print_r($data);print_r($o->attributes);
						foreach($attr as $a){
							$a = (array)$a;
							if(in_array("Condition",$a)){
								if($a['value'] !=""){
									$new = array("New","New Factory Sealed");
									$mfr = array("Samsung Certified Pre-Owned","Factory Refurbished","Refurbished - Off Lease","3rd Party Refurbished");
									$like_new = array("New Open Box","Store Return - As is","New Re-Imaged","Store Return - 3rd Party Refurbished","Store Return - 3rd Party Refurbished NEW OPEN BOX");
									$very_good = array("Renewed Grade A","Store Return - 3rd Party Refurbished B GRADE");
									$good = array("Renewed Grade B");
									$fair = array("Factory Refurbished Scratch & Dent","Renewed Grade C");
									$data['condition'] = $a['value'];  
									if(in_array($a['value'],$new)){
										$data['condition_id'] = 1;
									}else if(in_array($a['value'],$mfr)){
										$data['condition_id'] = 2;
									}else if(in_array($a['value'],$like_new)){
										$data['condition_id'] = 3;
									}else if(in_array($a['value'],$very_good)){
										$data['condition_id'] = 4;
									}else if(in_array($a['value'],$good)){
										$data['condition_id'] = 5;
									}else if(in_array($a['value'],$fair)){
										$data['condition_id'] = 6;
									}else{
										$data['condition_id'] = 0;
									}
								}
								
							}
						}
						//$variant = $this->getVariantByHubxTitle($o->description);
						//if($variant[0]->v_cat_id !=""){
						//	$data['v_cat_id'] = $variant[0]->v_cat_id;
						//	$data['v_id'] = $variant[0]->v_id;
						//}
						$this->db->insert("tbl_hubx_product",$data);
						//echo "<pre>";print_r($data);
					}
				}	
			}else{
				echo json_encode(array("code"=>00,"msg"=>"Empty Data","status"=>0));
			}
		}else{
			echo json_encode(array("code"=>01,"msg"=>"No access to hubx","status"=>0));
		}
	}
	public function updateHubxList(){
		$user_id = "60dc1beac4485";
		$user_type = 2;
		$hubx_info = $this->db->select("hubx_info")->where("userid",$user_id)->get()->row();
		echo "<pre>";
		print_r($hubx_info);
		die();
		$access_token = "";
		$expires_in = "";
		$time = time();
		$params = array('user_id'=>$user_id,"user_type"=>$user_type,"client_id"=>$this->config->item("hubx_client_id"),"client_secret"=>$this->config->item("hubx_client_secret"));
		$this->load->library('Hubx',$params);
		if($hubx_info){
			$hubx_info = json_decode($hubx_info);
			if($time > $hubx_info->expires_in){
				$hubx_info = $this->getAccessToken($user_id);
				$hubx_info = json_decode($hubx_info);
			}
			
		}else{
			$hubx_info = $this->getAccessToken($user_id);
			$hubx_info = json_decode($hubx_info);
		}
	}
}
?>