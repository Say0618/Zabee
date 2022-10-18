<?php
class Sellerapi extends CI_Controller {
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->model("Product_Model");
		$this->load->model("admin/Secure_Model");
		$this->load->model("Utilz_Model");
		$this->load->model("admin/User_Model","Admin_User_Model");
		$this->load->model("User_Model");
		$this->load->helper('date');
		$this->lang->load('english', 'english');
	}
	public function check_store_exist(){
		$user_id = $this->input->get("user_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$checkUserStore = $this->Admin_User_Model->checkUserStore($user_id);
		if($checkUserStore['result']->s_id){
			echo json_encode($checkUserStore['result']);exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Store not exist.","code"=>"0"));exit();
		}
	}
	/* Product Api*/ 
	public function get_product_list(){
		$user_id = $this->input->get("user_id");
		$search = $this->input->get("search");
		$start = $this->input->get("start");
		$length = $this->input->get("length");
		$user_type = $this->input->get("user_type");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if($user_type ==1) {
			$user_id = "";
		}
		if(!$start){
			$start = 0;
		}
		if(!$length){
			$length = 10;
		}
		$limit = array("start"=>$start,"length"=>$length,"draw"=>"");
		$data = $this->Product_Model->productPipline($search,$limit,$user_id);
		echo json_encode($data["data"]);exit();
	}
	public function other_product_list(){
		$user_id  = $this->input->get("user_id");
		$active = $this->input->get("is_active");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$active && $active !=0){
			echo json_encode(array("status"=>"0","msg"=>"Is Active is missing.","code"=>"0"));
			exit();
		}
		$search  = $this->input->get('search');
		$start = $this->input->get("start");
		$length = $this->input->get("length");
		if(!$start && is_numeric($start)){
			$start = 0;
		}
		if(!$length){
			$length = 10;
		}
		$request = array("start"=>$start,'length'=>$length,"is_active"=>$active,"draw"=>"","recordsTotal"=>0,'recordsFiltered'=>0);
		$data = $this->Product_Model->productPendingPipline($search,$request,$user_id);
		echo json_encode($data['data']);exit();
	}

	public function get_product_accessories(){
		$user_id = $this->input->get("user_id");
		$search = $this->input->get("search");
		$start = $this->input->get("start");
		$length = $this->input->get("length");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$start){
			$start = 0;
		}
		if(!$length){
			$length = 10;
		}
		$data = $this->Product_Model->getAccDataByProdId($user_id, $search, $start, $length);	
		//echo "<pre>";print_r($data);die();
		echo json_encode($data);exit();
	}
	public function get_product_count(){
		$user_id = $this->input->get("user_id");
		$search = $this->input->get("search");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$totalProduct = $this->Product_Model->productCount($search,$user_id);
		echo json_encode($totalProduct);exit();
	}
	
	public function product_form_data(){
		$this->load->model("admin/Category_Model");
		$this->load->model("admin/Brands_Model");
		$data = array();
		$data["variantData"] 	=  $this->Product_Model->getVariantData("category.v_cat_id,category.v_cat_title","");	
		$data["categoryData"] 	=  $this->Category_Model->getCat("","category_id,category_name,is_private",1);	
		$data["brandData"] 		=  $this->Brands_Model->getBrands("","brand_id,brand_name",1);
		echo json_encode($data);exit();
	}
	public function product_suggestions(){
		$user_type	= $this->input->get("user_type");
		$text		= $this->input->get("text");
		$user_id	= $this->input->get("user_id");
		$result = "";
		if($text){
			if($user_type != "1"){ 
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
					'id'	=> $get_product[$i]->product_id
				);
				$i++;
			}
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Search text is missing.","code"=>"0"));
			exit();
		}
		echo json_encode($result);exit();
		
	}
	public function create_product(){
		//echo "<pre>";print_r($_POST);die();
		$seller_id 				= $this->input->post("user_id");
		$user_type 				= $this->input->post("user_type");
		$feature 				= $this->input->post("feature");
		$product_name 			= $this->input->post("product_name");
		$product_description 	= $this->input->post("product_description");
		$brand_id 				= $this->input->post("brand_id");
		$upc_code				= $this->input->post("product_upc_code");
		$sku_code				= $this->input->post("product_sku_code");
		$short_description		= $this->input->post("short_description");
		$keywords				= $this->input->post("product_keyword");
		$variant_cat			= $this->input->post("variant_cat");
		$product_video_link		= $this->input->post("product_video_link");
		$dummy_id				= $this->input->post("dummy_id");
		$media_id				= $this->input->post("media_id");
		$category_id			= $this->input->post("category_id");
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$feature && is_array($feature)){
			echo json_encode(array("status"=>"0","msg"=>"Feature is missing.","code"=>"0"));
			exit();
		}else if(count($feature) < 3){
			echo json_encode(array("status"=>"0","msg"=>"Add at least 3 feature.","code"=>"0"));
			exit();
		}
		if(!$product_name){
			echo json_encode(array("status"=>"0","msg"=>"Product Name is missing.","code"=>"0"));
			exit();
		}
		/*if(!$slug){
			echo json_encode(array("status"=>"0","msg"=>"Product Slug is missing.","code"=>"0"));
			exit();
		}*/
		if(!$product_description){
			echo json_encode(array("status"=>"0","msg"=>"Product Description is missing.","code"=>"0"));
			exit();
		}
		if(!$brand_id){
			echo json_encode(array("status"=>"0","msg"=>"Brand id is missing.","code"=>"0"));
			exit();
		}
		if(!$upc_code){
			$upc_code = rand(0,time());
		}
		if(!$sku_code){
			$sku_code = rand(0,time());
		}
		if(!$category_id && is_array($category_id)){
			echo json_encode(array("status"=>"0","msg"=>"Category id is missing.","code"=>"0"));
			exit();
		}
		/*Product Dimension*/
		$productLength_type 	= $this->input->post("productLength_type");
		$productHeight 			= $this->input->post("productHeight");
		$productWidth 			= $this->input->post("productWidth");
		$productLength		 	= $this->input->post("productLength");
		$productWeight_type 	= $this->input->post("productWeight_type");
		$productWeight 			= $this->input->post("productWeight");
		/*if(!$productLength_type){
			echo json_encode(array("status"=>"0","msg"=>"Product Length Type is missing.","code"=>"0"));
			exit();
		}
		if(!$productHeight){
			echo json_encode(array("status"=>"0","msg"=>"Product Height is missing.","code"=>"0"));
			exit();
		}
		if(!$productWidth){
			echo json_encode(array("status"=>"0","msg"=>"Product Width is missing.","code"=>"0"));
			exit();
		}
		if(!$productLength){
			echo json_encode(array("status"=>"0","msg"=>"Product Length is missing.","code"=>"0"));
			exit();
		}
		if(!$productWeight_type){
			echo json_encode(array("status"=>"0","msg"=>"Product Weight Type is missing.","code"=>"0"));
			exit();
		}
		if(!$productWeight){
			echo json_encode(array("status"=>"0","msg"=>"Product Weight is missing.","code"=>"0"));
			exit();
		}*/
		/*---------*/
		/*Shipping Dimension*/
		$shipLength_type 	= $this->input->post("shipLength_type");
		$shipHeight 		= $this->input->post("shipHeight");
		$shipWidth 			= $this->input->post("shipWidth");
		$shipLength		 	= $this->input->post("shipLength");
		$shipWeight_type 	= $this->input->post("shipWeight_type");
		$shipWeight 		= $this->input->post("shipWeight");
		$shipNote 			= $this->input->post("shipNote");
		if(!$shipLength_type){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Length Type is missing.","code"=>"0"));
			exit();
		}
		if(!$shipHeight){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Height is missing.","code"=>"0"));
			exit();
		}
		if(!$shipWidth){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Width is missing.","code"=>"0"));
			exit();
		}
		if(!$shipLength){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Length is missing.","code"=>"0"));
			exit();
		}
		if(!$shipWeight_type){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Weight Type is missing.","code"=>"0"));
			exit();
		}
		if(!$shipWeight){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Weight is missing.","code"=>"0"));
			exit();
		}
		/*---------*/
		$product = array();
		$video_link = array();
		$media_id = array();
		$date = date("Y-m-d H:i:s");
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$slug = str_replace(" ","-",trim($product_name));
		if($user_type == 1){
			$created_by = "Admin";
			$is_active = "1";
		}else{
			$created_by = "Seller";
			$is_active = "0";
		}
		$product_dimensions = array("dimension_type"=>	$productLength_type,
									"height"		=>	$productHeight,
									"width" 		=>	$productWidth,
									"length" 		=>	$productLength,
									"weight_type" 	=>	$productWeight_type,				
									"weight" 		=>	$productWeight);
		$shippingInfo = array(	"dimension_type"=>	$shipLength_type,
								"height"		=>	$shipHeight,
								"width" 		=>	$shipWidth,
								"length" 		=>	$shipLength,
								"weight_type" 	=>	$shipWeight_type,
								"weight" 		=>	$shipWeight,				
								"shipping_note" =>	$shipNote);
		$product = array("product_name"				=>	trim($product_name),
							"slug"					=>	$this->slugify($slug),
							"product_description"	=>	base64_decode($product_description),
							"is_private" 			=>	"0",
							"brand_id"				=>	$brand_id[0], 
							"created_id"			=>	$seller_id,
							"upc_code" 				=>	$upc_code,
							"sku_code"				=>	$sku_code,
							"created_date"			=>	$date_utc,
							"created_by"			=>	$created_by,
							"is_active"				=>	$is_active,
							"short_description"		=>	$short_description
							);
		/*echo "<pre>";
		print_r($product_dimensions);
		echo "<hr>";
		print_r($shippingInfo);
		echo "<hr>";
		print_r($product);
		echo "<hr>";
		print_r($keywords);
		echo "<hr>";
		print_r($variant_cat);
		die();*/
		if(!empty($product_video_link)){
			foreach($product_video_link as $vl){
				$video_link[] = base64_decode($vl);
			}
		}
		$result	= $this->Product_Model->createProduct($product,$dummy_id,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id,$product_dimensions,$category_id);
		if($result){
			echo json_encode(array("status"=>"1","msg"=>"Product create successfully.","code"=>$result));
			exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in creating product.","code"=>"2"));
			exit();
		}
	}
	public function  updateProduct(){
		$seller_id 				= $this->input->post("user_id");
		$user_type 				= $this->input->post("user_type");
		$feature 				= $this->input->post("feature");
		$product_name 			= $this->input->post("product_name");
		$product_description 	= $this->input->post("product_description");
		$brand_id 				= $this->input->post("brand_id");
		$upc_code				= $this->input->post("product_upc_code");
		$sku_code				= $this->input->post("product_sku_code");
		$short_description		= $this->input->post("short_description");
		$keywords				= $this->input->post("product_keyword");
		$variant_cat			= $this->input->post("variant_cat");
		$product_video_link		= $this->input->post("product_video_link");
		$dummy_id				= $this->input->post("dummy_id");
		$product_media_id		= $this->input->post("media_id");
		$category_id			= $this->input->post("category_id");
		//echo "<pre>";print_r($feature); echo count($feature);die();
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$feature && is_array($feature)){
			echo json_encode(array("status"=>"0","msg"=>"Feature is missing.","code"=>"0"));
			exit();
		}else if(count($feature) < 3){
			echo json_encode(array("status"=>"0","msg"=>"Add at least 3 feature.","code"=>"0"));
			exit();
		}
		if(!$product_name){
			echo json_encode(array("status"=>"0","msg"=>"Product Name is missing.","code"=>"0"));
			exit();
		}
		/*if(!$slug){
			echo json_encode(array("status"=>"0","msg"=>"Product Slug is missing.","code"=>"0"));
			exit();
		}*/
		if(!$product_description){
			echo json_encode(array("status"=>"0","msg"=>"Product Description is missing.","code"=>"0"));
			exit();
		}
		if(!$brand_id){
			echo json_encode(array("status"=>"0","msg"=>"Brand id is missing.","code"=>"0"));
			exit();
		}
		if(!$upc_code){
			$upc_code = rand(0,time());
		}
		if(!$sku_code){
			$sku_code = rand(0,time());
		}
		if(!$category_id && is_array($category_id)){
			echo json_encode(array("status"=>"0","msg"=>"Category id is missing.","code"=>"0"));
			exit();
		}
		/*Product Dimension*/
		$productLength_type 	= $this->input->post("productLength_type");
		$productHeight 			= $this->input->post("productHeight");
		$productWidth 			= $this->input->post("productWidth");
		$productLength		 	= $this->input->post("productLength");
		$productWeight_type 	= $this->input->post("productWeight_type");
		$productWeight 			= $this->input->post("productWeight");
		/*---------*/
		/*Shipping Dimension*/
		$shipLength_type 	= $this->input->post("shipLength_type");
		$shipHeight 		= $this->input->post("shipHeight");
		$shipWidth 			= $this->input->post("shipWidth");
		$shipLength		 	= $this->input->post("shipLength");
		$shipWeight_type 	= $this->input->post("shipWeight_type");
		$shipWeight 		= $this->input->post("shipWeight");
		$shipNote 			= $this->input->post("shipNote");
		if(!$shipLength_type){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Length Type is missing.","code"=>"0"));
			exit();
		}
		if(!$shipHeight){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Height is missing.","code"=>"0"));
			exit();
		}
		if(!$shipWidth){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Width is missing.","code"=>"0"));
			exit();
		}
		if(!$shipLength){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Length is missing.","code"=>"0"));
			exit();
		}
		if(!$shipWeight_type){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Weight Type is missing.","code"=>"0"));
			exit();
		}
		if(!$shipWeight){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Weight is missing.","code"=>"0"));
			exit();
		}
		$product 		= array();
		$video_link 	= array();
		$media_id 		= array();
		$product_id 	= $_POST['product_id'];
		$date 			= date('Y-m-d H:i:s');
		$date_utc 		= gmdate("Y-m-d\TH:i:s");
		$date_utc 		= str_replace("T"," ",$date_utc);
		$seller_id 		= $seller_id;
		$feature 		= $feature;
		$slug = str_replace(" ","-",trim($product_name));
		$product_dimensions = array("dimension_type"=>	$productLength_type,
								"height"		=>	$productHeight,
								"width" 		=>	$productWidth,
								"length" 		=>	$productLength,
								"weight_type" 	=>	$productWeight_type,				
								"weight" 		=>	$productWeight);
		$shippingInfo = array(	"dimension_type"=>	$shipLength_type,
								"height"		=>	$shipHeight,
								"width" 		=>	$shipWidth,
								"length" 		=>	$shipLength,
								"weight_type" 	=>	$shipWeight_type,
								"weight" 		=>	$shipWeight,				
								"shipping_note" =>	$shipNote);
		$product = array("product_name"				=>	trim($product_name),
							"slug"					=>	$this->slugify($slug),
							"product_description"	=>	base64_decode($product_description),
							"is_private" 			=>	"0",
							"brand_id"				=>	$brand_id[0], 
							"created_id"			=>	$seller_id,
							"upc_code" 				=>	$upc_code,
							"sku_code"				=>	$sku_code,
							"updated_date"			=>	$date_utc,
							"short_description"		=>	$short_description
							);
		//echo "<pre>";print_r($shippingInfo);print_r($product_dimensions);die();
		if($user_type == "2" && $user_type !="1"){
			$product['is_declined'] = "0";
			$product['is_active'] 	= "0";
		} 
		$keywords	 				= $_POST['product_keyword'];
		if(!empty($product_video_link)){
			foreach($product_video_link as $vl){
				$video_link['video_link'][] = base64_decode($vl);
			}
		}
		$media_id['media_id'] 		= $product_media_id;
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
			echo json_encode(array("status"=>"1","msg"=>"Product updated.","code"=>"1"));
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in updation.","code"=>"2"));
		}
	
	}
	public function getProductData(){
		$product_id = $this->input->get("product_id");
		if(!$product_id){
			echo json_encode(array("status"=>"0","msg"=>"Product id is missing.","code"=>"0"));
			exit();
		}
		$data['product'] = $this->Product_Model->getProductData($product_id);
		$data['product'] = $data['product'][0];
		$data['product']['product_description'] = base64_encode($data['product']['product_description']);
		$data['features'] = $this->Product_Model->getData(DBPREFIX.'_product_features',"feature",array('product_id'=>$product_id));
		$keywords = $this->Product_Model->getData(DBPREFIX.'_meta_keyword',"GROUP_CONCAT(keywords) as keyword",array('product_id'=>$product_id));
		if(isset($keywords[0]) && $keywords[0]->keyword !=""){
			$data['keywords'] = $keywords[0]->keyword; 
		}else{
			$data['keywords'] = "";
		}
		$data['image_data'] = array();
		$data['video_link'] = array();
		$where = array("product_id" =>$product_id,"condition_id"=>"1","is_active"=>"1");
		$imagePreviewData =  $this->Product_Model->getData(DBPREFIX."_product_media","media_id,thumbnail,iv_link,is_image,is_local,is_cover",$where,0,'','position');
		if($imagePreviewData){
			foreach($imagePreviewData as $ipd){
				if($ipd->is_image == "1"){
					$data['image_data'][] =array('key'=> $ipd->media_id,'original'=>$ipd->iv_link,"thumb"=>$ipd->thumbnail);
					
				}else{
					$ipd->iv_link = base64_encode($ipd->iv_link);
					array_push($data['video_link'],$ipd);
				}
			}
		}
		echo json_encode($data);
	}
	public function deletePv(){
		$pv_id = $this->input->post('pv_id');
		if(!$pv_id){
			echo json_encode(array("status"=>"0","msg"=>"pv_id is missing.","code"=>"404"));
			exit();
		}
		$result = $this->Product_Model->deletePv($pv_id);
		if($result){
			echo json_encode(array("status"=>"1","msg"=>"success","code"=>"200"));
		}else{
			echo json_encode(array("status"=>"0","msg"=>"error","code"=>"500"));
		}
	}
	public function deleteCondition(){
		$condition_id = $this->input->post("condition_id");
		$seller_id = $this->input->post("seller_id");
		$sp_id = $this->input->post("sp_id");
		if(!$condition_id){
			echo json_encode(array("status"=>"0","msg"=>"condition_id is missing.","code"=>"404"));
			exit();
		}
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"seller_id is missing.","code"=>"404"));
			exit();
		}
		if(!$sp_id){
			echo json_encode(array("status"=>"0","msg"=>"sp_id is missing.","code"=>"404"));
			exit();
		}
		$return = $this->Product_Model->deleteCondition($condition_id,$seller_id,$sp_id);
		if($return){
			echo json_encode(array("status"=>"1","msg"=>"success","code"=>"200"));
		}else{
			echo json_encode(array("status"=>"0","msg"=>"error","code"=>"500"));
		}
	}
	/*Product Api Ends*/
	/*Inventory Api*/
	public function get_inventory_list(){
		$product_id = $this->input->get("product_id");
		$user_id 	= $this->input->get("user_id");
		$search 	= $this->input->get("search");
		$user_type 	= $this->input->get("user_type");
		$start 		= $this->input->get("start");
		$length 	= $this->input->get("length");
		$approve 	= $this->input->get("approve");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$user_type){
			echo json_encode(array("status"=>"0","msg"=>"User type is missing.","code"=>"0"));
			exit();
		}
		if(!$start && is_numeric($start)){
			$start = 0;
		}
		if(!$length){
			$length = 10;
		}
		if($user_type == 1 && $user_id == ""){
			$user_id = "";
		}
		// $count = $this->Product_Model->inventoryPiplineCount($search, $user_id, $prd_id);
		$data = $this->Product_Model->inventoryList($search, $start, $length, $user_id, $user_type, $product_id,$approve);
		echo json_encode($data);
		// echo $this->db->last_query(); die();
	}
	public function getProductDataForInventory(){
		$product_id = $this->input->get("product_id");
		$seller_id 	= $this->input->get("user_id");
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$product_id){
			echo json_encode(array("status"=>"0","msg"=>"Product id is missing.","code"=>"0"));
			exit();
		}
		$data = $this->Product_Model->getProductDataForInventory($product_id,$seller_id);
		$data['image_data'] = array();
		$data['video_link'] = array();
		foreach($data['image'] as $key=>$value){
			$data["image_data"][$key] = array();
			$data["video_link"][$key] = array();
			if(!empty($value)){
				foreach($value as $ipd){
					if($ipd->is_image == "1"){
						$data['image_data'][$key][] =array('key'=> $ipd->media_id,'original'=>$ipd->iv_link,"thumb"=>$ipd->thumbnail);	
					}else{
						$ipd->iv_link = base64_encode($ipd->iv_link);
						$data['video_link'][$key][] = $ipd;
						//array_push($data['video_link'],$ipd);
					}
				}
			}
		}
		unset($data['image']);
		$data['warrantyData'] 	=  $this->Product_Model->getData(DBPREFIX."_warranty","*",array("is_active"=>"1"),0,'','warranty');
		if(empty($data['condition']) && $data['preDefinedVariant'][0]->v_cat_id == ""){
			$data['preDefinedVariant'] = array();
			echo json_encode($data);exit();
		}else if($data['preDefinedVariant'][0]->v_cat_id == ""){
			$data['preDefinedVariant'] = array();
			echo json_encode($data);exit();
		}else{
			echo json_encode($data);exit();
		}
	}
	public function get_variant(){
		$result = array();
		$id = $this->input->get('id');
		if($id !=""){
			if(is_array($id)){
				$v_cat_id = implode(',',$id);
				$where = "AND v_cat_id IN (".$v_cat_id.")";
			}else{
				$where = "AND v_cat_id =".$id;
			}
			$variantData =  $this->Product_Model->getVariant($where,'',"v_id,v_cat_id,v_title");	
			if($variantData){
				$result= $variantData;
			}
		}else{
			$result = array("status"=>0,"msg"=>"Id is missing","code"=>"0");
		}
		echo json_encode($result);
	}
	public function inventory_form_data(){
		$user_id = $this->input->get("user_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$this->load->model("admin/Returntype_Model");
		$select = "id,returns,return_period,rma_required,restocking_fee,restocking_type,returnpolicy_name,is_default";
		$data['returnPolicy']  = $this->Returntype_Model->getReturnPolicyforProduct($user_id,"",$select);
		$where = "(userid='$user_id' OR user_type='1') AND active='1' AND display_status='1'";
		$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,valid_from,valid_to,value,title,type',$where);
		$discounts=array();
		if($discount){
			foreach($discount as $d){
				if($d->valid_to >= date('Y-m-d')){
					$discounts[] = $d;
				}
			}
		}
		$data['discountData'] =  $discounts;
		//$data['returns_default'] 	 =  $returns_default;	
		$data['conditionData'] =  $this->Product_Model->getConditionData('condition_id,condition_name');	
		//$data['variantData'] 	 =  $this->Product_Model->getVariantData('category.v_cat_id,category.v_cat_title,category.is_active','');	
		if($user_id == 1){
			$where =  array('user_id'=>$user_id ,'is_deleted'=>"0","is_active"=>"1");
		}else{
			$where = '(user_id="'.$user_id.'" OR user_type =1) AND is_deleted="0" AND is_active="1"';
		}		
		$data['shippingData'] 	=  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,user_id,user_type,title,price,duration",$where,0,'','shipping_id DESC');
		echo json_encode($data);exit();
	}
	public function updateInventoryPriceAndQuantity(){
		$user_id 	= $this->input->post("user_id");
		$id			= $this->input->post("inventory_id");
		$user_type	= $this->input->post("user_type");
		$price 		= $this->input->post("price");
		$quantity	= $this->input->post("quantity");
		$discount	= $this->input->post("discount");
		if(!$id){
			echo json_encode(array("status"=>"0","msg"=>"Inventory id is missing.","code"=>"0"));
			exit();
		}
		if(!$user_type){
			echo json_encode(array("status"=>"0","msg"=>"User type is missing.","code"=>"0"));
			exit();
		}
		if(!$price){
			echo json_encode(array("status"=>"0","msg"=>"Price is missing.","code"=>"0"));
			exit();
		}
		if(!$quantity){
			echo json_encode(array("status"=>"0","msg"=>"Quantity is missing.","code"=>"0"));
			exit();
		}
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$date = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));
		$data = array("updated_date"=>$date,"inventory_id"=>$id,"price"=>$price,"quantity"=>$quantity,"discount"=>$discount);
		$resp = $this->Product_Model->update_quantityandprice($data, $user_id, $user_type);
		if($resp){
			echo json_encode(array("status"=>"1","msg"=>"Inventory updated.","code"=>"1"));exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in updating.","code"=>"2"));exit();
		}
		
	}
	public function  createInventory(){ 
		$this->load->library('form_validation');
		$this->form_validation->set_rules('product_name', 'product name', 'trim|required');
		//echo "<pre>";print_r($this->input->post());die();
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
			$seller_id 				= $this->input->post('user_id');
			$is_warehouse 			= "";
			$is_warehouse_approved 	= "";
			$i						= 0;
			$product_name 			= $_POST['product_name'];
			$condition_img 			= array();
			$pvi 					= 0;
			$is_changeCoverImage 	= array();
			$inventory_shipping = array();
			//echo "<pre>";print_r($this->input->post());die();
			if($this->input->post('is_zabee')){
				$is_zabee = $this->input->post('is_zabee');
				$warehouse_id = $this->input->post('warehouse_id');
			}else{
				$is_zabee = "0";
				$warehouse_id = "0";
			}
			/*foreach($_POST['condition_id'] as $index=>$condition_id){
				if(isset($_POST['warehouseId'.$condition_id]) && $_POST['warehouseId'.$condition_id] !=""){
					$warehouse_id =  $this->input->post('warehouse_id');
				}
				$seller_product[$i]['seller_id'] 			= $seller_id;
				if(isset($_POST['sp_id'.$condition_id]) && $_POST['sp_id'.$condition_id] ==""){
					$seller_product[$i]['created_date'] 	= $date_utc;
				}else{
					$seller_product[$i]['updated_date'] 	= $date_utc;
				}
				$seller_product[$i]['seller_sku'] 					= $_POST['product_seller_sku'.$condition_id];
				$seller_product[$i]['seller_product_description'] 	= isset($_POST['prod_des'.$condition_id])?$_POST['prod_des'.$condition_id]:"";
				$seller_product[$i]['condition_id'] 				= $condition_id;
				$seller_product[$i]['sp_id'] 						= (isset($_POST['sp_id'.$condition_id]))?$_POST['sp_id'.$condition_id]:"";
				$seller_product[$i]['return_id'] 					= isset($_POST['returnId'.$condition_id])?$_POST['returnId'.$condition_id]:"";
				if($is_zabee == "0" && isset($_POST['shippingIds'.$condition_id])){
					$seller_product[$i]['shipping_ids']		= implode(',',$_POST['shippingIds'.$condition_id]);
				}else{
					$where =  array('user_id'=>"1" ,'is_deleted'=>"0","is_active"=>"1");
					$shipping_ids = $this->Product_Model->getData(DBPREFIX."_product_shipping","GROUP_CONCAT(shipping_id) as shipping_id",$where,0,'','shipping_id DESC');
					$seller_product[$i]['shipping_ids'] = $shipping_ids[0]->shipping_id;
				}
				
				$variantArray = array();
				//$shippingArray = array();
				$productVariant = array();
				$variant = array();
				if(isset($_POST['variant'.$condition_id])){
					$vci=0;
					$vc = count($_POST['variant'.$condition_id]);
					$pc = count($_POST['price'.$condition_id]);
					//$sc = count();
					
					for($pi=0; $pi<$pc; $pi++){
						//echo "<pre>";print_r($_POST['shipping'.$condition_id]);
						$inventory_shipping[$condition_id][$pi] = isset($_POST['shipping'.$condition_id][$pi])?implode(",",$_POST['shipping'.$condition_id][$pi]):"";
						$variantArray[$pi] = array();
						if(isset($pv_id_array[$pvi])){
							$pv_id = $pv_id_array[$pvi];
						}else{
							$pv_id = "";
						}
						for($vi =0; $vi<$vc; $vi++){
							if(isset($_POST['variant'.$condition_id][$vi][$pi])){
								$variant[$pi][] = $_POST['variant'.$condition_id][$vi][$pi];
								$variantArray[$pi][] = $_POST['variant'.$condition_id][$vi][$pi];
							}
						}
						// for($si = 0; $si < $sc; $si++){
						// 	if(isset($_POST['shipping'.$condition_id][$pi][$si])){
						// 		$shipping[$pi][] = implode(',',$_POST['shipping'.$condition_id][$pi][$si]);
						// 		//$shippingArray[$pi][] = $_POST['shipping'.$condition_id][$pi][$si];
						// 	}
						// }
						$discount = (isset($_POST['discount'.$condition_id][$pi]))?$_POST['discount'.$condition_id][$pi]:"";
						sort($variantArray[$pi]);
						$productVariant[] = array('seller_id'=>$seller_id,'seller_sku'=>$_POST['product_seller_sku'.$condition_id],'condition_id'=>$condition_id,'return_id'=>$_POST['return_id'],'variant_group'=>implode(',',$variantArray[$pi]),'price'=>$_POST['price'.$condition_id][$pi],'quantity'=>$_POST['quantity'.$condition_id][$pi],'variant_cat_group'=>implode(',',$_POST['variant_cat'.$condition_id]),'pv_id'=>$_POST['pv_id'.$condition_id][$pi],'discount'=>$discount);
						$pvi++;
					}
				}else{
					$pc = count($_POST['price'.$condition_id]);
					for($pi=0; $pi<$pc; $pi++){
						if(isset($pv_id_array[$pvi])){
							$pv_id = $pv_id_array[$pvi];
						}else{
							$pv_id = "";
						}
						$inventory_shipping[$condition_id][$pi] = isset($_POST['shipping'.$condition_id][$pi])?implode(",",$_POST['shipping'.$condition_id][$pi]):"";
						$discount = (isset($_POST['discount'.$condition_id][$pi]))?$_POST['discount'.$condition_id][$pi]:"";
						$productVariant[] = array('seller_id'=>$seller_id,'seller_sku'=>$_POST['product_seller_sku'.$condition_id],'condition_id'=>$condition_id,'return_id'=>$_POST['return_id'],'variant_group'=>'','price'=>$_POST['price'.$condition_id][$pi],'quantity'=>$_POST['quantity'.$condition_id][$pi],'variant_cat_group'=>'','pv_id'=>$_POST['pv_id'.$condition_id][$pi],'discount'=>$discount);
						$pvi++;
					}
				}
				$seller_product_variant[$i] = $variant;
				$proVariant[$i] = $productVariant;
				//------------------------------------------------//
				$region = (isset($_POST['statesData']))?$_POST['statesData']:"";
				$video_link["condition_video_link".$condition_id] = (isset($_POST['condition_video_link'.$condition_id]))?$_POST['condition_video_link'.$condition_id]:"";
				$media_id["media_id".$condition_id] = (isset($_POST['media_id'.$condition_id]))?$_POST['media_id'.$condition_id]:"";
				$i++;
			}*/
			/*foreach($shippingArray as $ship){
				$shipping[] = implode(',', $ship);
			}*/
			foreach($_POST['condition_id'] as $index=>$condition_id){
				/*if(isset($_POST['warehouseId'.$condition_id]) && $_POST['warehouseId'.$condition_id] !=""){
					$warehouse_id = isset($_SESSION['warehouse_id'])?$_SESSION['warehouse_id']:"";
				}*/
				$seller_product[$i]['seller_id'] 			= $seller_id;
				if(isset($_POST['sp_id'.$condition_id]) && $_POST['sp_id'.$condition_id] ==""){
					$seller_product[$i]['created_date'] 	= $date_utc;
				}else{
					$seller_product[$i]['updated_date'] 	= $date_utc;
				}
				//$seller_product[$i]['seller_sku'] 					= $_POST['product_seller_sku'.$condition_id];
				$seller_product[$i]['seller_product_description'] 	= isset($_POST['prod_des'.$condition_id])?$_POST['prod_des'.$condition_id]:"";
				$seller_product[$i]['condition_id'] 				= $condition_id;
				$seller_product[$i]['sp_id'] 						= (isset($_POST['sp_id'.$condition_id]))?$_POST['sp_id'.$condition_id]:"";
				$seller_product[$i]['return_id'] 					= (isset($_POST['returnId'.$condition_id]))?$_POST['returnId'.$condition_id]:"";
				if($is_zabee == "0" && isset($_POST['shippingIds'.$condition_id])){
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
					//$inventory_shipping[$condition_id][$pi] = isset($_POST['shipping'.$condition_id][$pi])?implode(",",$_POST['shipping'.$condition_id][$pi]):"";
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
				echo json_encode(array("status"=>1,"msg"=>"Success","code"=>1));exit();
			}else{
				echo json_encode(array("status"=>0,"msg"=>"Error","code"=>0));exit();
			}
		}
		echo json_encode(array("status"=>0,"msg"=>"Error","code"=>0));exit();
	}
	/*Ends*/
	function slugify($text){
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}
	function upload() {	
		$uploadData = array();
		$this->load->library('upload');
		$this->load->library('image_lib');
		$preview = $errors = [];
		$config = array();
		$user_id = $this->input->post('user_id');
		$type = $this->input->post('type');
		$condition_id = $this->input->post('condition_id');
		$dummy_id = $this->input->post('dummy_id');
		$sp_id = $this->input->post('sp_id');
		$id = $this->input->post('product_id');
		$column = $this->input->post('column');
		if(!$condition_id){
			echo json_encode(array("status"=>"0","msg"=>"Condition id is missing.","code"=>"0"));
			exit();
		}
		if(!$type){
			echo json_encode(array("status"=>"0","msg"=>"Type is missing.","code"=>"0"));
			exit();
		}
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$column){
			echo json_encode(array("status"=>"0","msg"=>"Column is missing.","code"=>"0"));
			exit();
		}
		if (isset($_FILES)){
			$date = date('Y-m-d H:i:s');
			$date_utc = gmdate("Y-m-d\TH:i:s");
			$date_utc = str_replace("T"," ",$date_utc);
			$file = $_FILES;
			if($type != "user"){
				$config['encrypt_name'] = true;
				$config['overwrite'] = FALSE;
			}else{
				$config['file_name'] = 'seller_'.$user_id.'.PNG';
				$config['overwrite'] = true;
			}
			$config['upload_path'] = $type;
			$config['upload_thumbnail_path'] = $type."/thumbs";
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['quality'] = "100%";
			$config['remove_spaces'] = TRUE;
			foreach($file as $f){
				$params['file'] = curl_file_create($f['tmp_name'], $f['type'], $f['name']);
				$params['image_type'] = $type;
				$params['filesize'] = $f['size'];
				$params['config'] = json_encode($config);
				//echo '<pre>';print_r($params);die();
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);	
				$params['file'] = curl_file_create($f['tmp_name'], $f['type'], $f['name']);
				$params['image_type'] = $type;
				$params['filesize'] = $f['size'];
				$params['config'] = json_encode($config);
				//echo '<pre>';print_r($params);die();
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				if(isset($file->status) && $file->status == 1){
					$image_name =  $file->images->original->filename;
					$targetUrl = $file->images->original->filepath.$image_name;
					//if($type == "product"){
					$thumbnail = $file->images->thumbnail->filename;
					//}
					$tn = $_POST['tn'];
					$table_name = DBPREFIX."_".$tn;
					if($tn == "pm"){
						$table_name = DBPREFIX."_product_media";
						$imageData = array('thumbnail'=>$thumbnail,'condition_id'=>$condition_id, 'iv_link'=>$image_name, 'is_local'=>"1",'is_image'=>"1","is_active"=>"1");
						if($id !=""){
							$positionWhere = array("product_id"=>$id,"condition_id"=>$condition_id);
							if($sp_id !=""){
								$positionWhere['sp_id'] = $sp_id;
								$imageData["sp_id"] = $sp_id;
							}else if($sp_id == "" && $condition_id !=1){
								$positionWhere['dummy_id'] = $dummy_id;
								$imageData['dummy_id'] = $dummy_id;
							}
							$imageData['product_id'] = $id;	
						}else{
							$positionWhere = array("dummy_id"=>$dummy_id);
							$imageData['dummy_id'] = $dummy_id;
						}
						
					}else if($tn == "brands"){
						$imageData = array("brand_image"=>$image_name);	
					}else if($tn == "banners"){
						$imageData = array("banner_image"=>$image_name);	
					}else if($tn == "categories"){
						$imageData = array("category_image"=>$image_name);	
					}else if($tn == "special_offers"){
						$imageData = array("offer_image"=>$image_name);	
					}
					if($tn == "pm"){
						$position = $this->Product_Model->getData($table_name,"MAX(position) as position",$positionWhere);
						if($position[0]->position !=""){
							$position = $position[0]->position+1;
						}else{
							$position = 0;
							$imageData['is_cover'] = "1";
						}
						$imageData['position'] = $position;
						$imageData['created_date'] = $date;
						$this->db->insert($table_name, $imageData);
						$image_key =  $this->db->insert_id();	
					}else if($tn == "special_offers"){
						if($id == ""){
							$imageData['created'] = $date;
							$this->db->insert($table_name, $imageData);
							$image_key =  $this->db->insert_id();
						}else{
							$imageData['updated'] = $date;
							$where = array($column=>$id);
							$this->db->where($where);
							$this->db->update($table_name, $imageData);
							$image_key = $id;
						}
					}else if($tn == "banners"){
						if($id == ""){
							$imageData['created'] = $date;
							$this->db->insert($table_name, $imageData);
							$image_key =  $this->db->insert_id();
						}else{
							$imageData['updated'] = $date;
							$where = array($column=>$id);
							$this->db->where($where);
							$this->db->update($table_name, $imageData);
							$image_key = $id;
						}
					}else{
						if($id == ""){
							$imageData['created_date'] = $date;
							$this->db->insert($table_name, $imageData);
							$image_key =  $this->db->insert_id();
						}else{
							$imageData['updated_date'] = $date;
							$where = array($column=>$id);
							$this->db->where($where);
							$this->db->update($table_name, $imageData);
							$image_key = $id;
						}
					}
					$uploadData[] = array(
								'type' => 'image',      // check previewTypes (set it to 'other' if you want no content preview)
								'key' => $image_key,       // keys for deleting/reorganizing preview
								'error' => $file->message,
								'status'=>"1",
								'name'=>$image_name,
								"thumb"=>$thumbnail,
								"error"=>"");
				} else {
					$uploadData[] = array(
								'type' => 'image',      // check previewTypes (set it to 'other' if you want no content preview)
								'key' => "",       // keys for deleting/reorganizing preview
								'error' => $file->message,
								'status'=>"0",
								'name'=>"",
								"thumb"=>"",
								'error' => $file->message);
				}
			}
			echo json_encode($uploadData); exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"No file found"));exit();
		}
		
	}
	public function deleteVideoLink(){
		$id =  $_POST['id'];
		$response = $this->Product_Model->deleteVideoLink($id); 
		echo json_encode($response);
	}
	public function delete_media(){
		$id = $this->input->get('key');
		$name = $this->input->get('name');
		if(!$id){
			echo json_encode(array("status"=>"0","msg"=>"Media Id is missing.","code"=>"0"));
			exit();
		}
		if(!$name){
			echo json_encode(array("status"=>"0","msg"=>"File name is missing.","code"=>"0"));
			exit();
		}
		$table 			= DBPREFIX."_product_media";
		$where 			= array("media_id"=>$id);
		$return 		= $this->Product_Model->deleteData($id,$table,$where);
		$params 		= array('filename'=>$name,'filetype'=>"product");
		$upload_server 	= $this->config->item('media_url').'/file/delete_media';
		$file 			= $this->Utilz_Model->curlRequest($params, $upload_server, true,false);
		if($return){
			echo json_encode(array("status"=>"1","msg"=>"Remove Successfully.","code"=>"1"));
			exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in image deletation.","code"=>"2"));
			exit();
		}	
	}
	public function updateProductTitle(){
		$title = $this->input->post('title');
		$product_id = $this->input->post('product_id');
		if(!$title){
			echo json_encode(array("status"=>"0","msg"=>"Title is missing.","code"=>"0"));
			exit();
		}
		if(!$product_id){
			echo json_encode(array("status"=>"0","msg"=>"Product Id is missing.","code"=>"0"));
			exit();
		}
		$slug = str_replace(" ","-",$title);
		$slug = $this->slugify($slug);
		$where = "product_name='".$title."' AND slug='".$slug."'  AND product_id != ".$product_id;
		$check = $this->Product_Model->getData(DBPREFIX."_product","",$where);
		if(!empty($check)){
			$data = array("status"=>"0", "code"=>"2" , "message"=>"Product Title or Slug already exist");
		}else{
			$data = array("product_name"=>$title,"slug"=>$slug);
			$where = array("product_id"=>$product_id);
			$return = $this->Product_Model->updateData(DBPREFIX."_product",$data,$where);
			if($return){
				$data = array("status"=>"1","code"=>"1" , "message"=>"Product title changed successfully.");
			}else{
				$data = array("status"=>"0","code"=>"0" , "message"=>"Error.");
			}
			
		}
		echo json_encode($data);
	}
	public function checkProductTitle(){
		$title = $this->input->post('title');
		$product_id = $this->input->post('product_id');
		if(!$title){
			echo json_encode(array("status"=>"0","msg"=>"Title is missing.","code"=>"0"));
			exit();
		}
		$where = "product_name='".$title."'";
		if($product_id != ''){
			$where .= " AND product_id != '".$product_id."'";
		}
		$check = $this->Product_Model->getData(DBPREFIX."_product","",$where);
		if(isset($check[0]->product_id) && $check[0]->product_id != ""){
			$code = $check[0]->product_id."-".$check[0]->created_id;
			$data = array("status"=>"1","code"=>$code , "message"=>"Product Already Exists.");
		}else{
			$data = array("status"=>"0","code"=>"0" , "message"=>"Product not exists.");
		}
		echo json_encode($data);
	}
	/* Orders Api*/ 
	public function get_orders_list(){
		$this->load->model("admin/Order_Model");
		$this->load->model('Cart_Model');
		$user_id = $this->input->get("user_id");
		$order_id = $this->input->get("order_id");
		$search = $this->input->get("search");
		$start = $this->input->get("start");
		$length = $this->input->get("length");
		$order_status = $this->input->get("order_status");
		$cancellation_pending = $this->input->get("cancellation_pending");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(! is_numeric($order_status)){
			echo json_encode(array("status"=>"0","msg"=>"Order status is missing.","code"=>"0"));
			exit();
		}
		if(!$start){
			$start = 0;
		}
		if(!$length){
			$length = 10;
		}
		if(!$cancellation_pending){
			$cancellation_pending = "0";
		}
		$limit = array("start"=>$start,"length"=>$length,"draw"=>"","recordsTotal"=>"");
		$where = array('td.seller_id' => $user_id, 'td.status' => $order_status,'td.cancellation_pending' => $cancellation_pending);
		$orders = $this->Order_Model->getOrders($user_id, $order_id, $search, $limit, $where);
		foreach($orders['data'] as $o){
			if($o->image_link == ""){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				$o->image_link = (isset($productImage[0]) && $productImage[0] != "")?$productImage[0]->is_primary_image:""; 
			}
			$days = $this->dateDifference($o->created);
			$o->days = $days;
			$o->created = strtotime($o->created);
		}
		$result =  $this->Cart_Model->formatOrderList($orders['data']);
		$dat = array_values($result);
	 	//print_r($result);die();
		echo json_encode($dat);
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
	public function approveOrder(){ 
		$this->load->model("admin/Order_Model");
		$d = $this->input->post("data");
		$s = $this->input->post("shippingData");
		if(empty($d)){
			echo json_encode(array("status"=>"0","msg"=>"Data is missing.","code"=>"0"));
			exit();
		}
		if(empty($s)){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Data is missing.","code"=>"0"));
			exit();
		}
		$trans_details_Id = $d[0]["trans_details_Id"];
		$seller_id = $d[0]["sellerid"];
		$order_id = $d[0]["order_id"];
		$tracking_number = $s["tracking_number"];
		$shipping_provider = $s["shipping_provider"];
		$shipping_service = $s["shipping_service"];
		if(!$trans_details_Id){
			die("here");
			echo json_encode(array("status"=>"0","msg"=>"Transaction detail id is missing.","code"=>"0"));
			exit();
		}
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		if(!$order_id){
			echo json_encode(array("status"=>"0","msg"=>"Order id is missing.","code"=>"0"));
			exit();
		}
		$post_data = $d;
		$data = $this->Order_Model->approveOrder($post_data);
		if($data){
			$this->load->model("User_Model");	
			$trackingData = array("tracking_number"=>$tracking_number,"shipping_provider"=>$shipping_provider,"shipping_service"=>$shipping_service,"trans_details_Id"=>$trans_details_Id,"order_id"=>$order_id);
			$this->User_Model->saveData($trackingData,DBPREFIX."_tracking");	
			$emailData = $this->Order_Model->getProductDataByOrder($order_id,$trans_details_Id);
			$loaction = array("shipping","billing");

			$location['shipping'] = unserialize($emailData->shipping);
			$location['shipping']['state'] = (is_numeric($location['shipping']['state']))?getCountryNameByKeyValue('id', $location['shipping']['state'], 'code', true,'tbl_states'):$location['shipping']['state'];
			$location['shipping']['country'] = getCountryNameByKeyValue('id', $location['shipping']['country'], 'nicename', true);
			
			$location['billing'] = unserialize($emailData->shipping);
			$location['billing']['state'] = (is_numeric($location['billing']['state']))?getCountryNameByKeyValue('id', $location['billing']['state'], 'code', true,'tbl_states'):$location['billing']['state'];
			$location['billing']['country'] = getCountryNameByKeyValue('id', $location['billing']['country'], 'nicename', true);

			$product['products'] = array("price"=>$emailData->price,"qty"=>$emailData->qty,"shipping_price"=>$emailData->item_shipping_amount,"name"=>$emailData->product_name,"is_local"=>$emailData->is_local,"img"=>$emailData->thumbnail);
			$subject = "Your Zab.ee Order ".$order_id." Is Confirmed";
			$shippingProvider = ($shipping_provider !="")?$shipping_provider:"N/A";
			$trackingNumber = ($tracking_number !="")?$tracking_number:"N/A";
			$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Great news! Your order '.$order_id.' is shipping soon. The tracking information, if available, is given below.</p><p style="margin-bottom:0px;font-size:16px; font-weight:bold">Shipping Method:</p><span>'.$shippingProvider.": ".$trackingNumber.'</span>';
			//$this->Utilz_Model->send_mail($location,$product,$order_id,$emailData->email, $type="buyer",$subject,$text,$emailData->firstname);
		}else{
			$data = array("status"=>"0", "code"=>"0", "message"=>"Error in order approve.");
		}
		echo json_encode($data);
	}
	public function declineOrder(){ 
		$this->load->model("admin/Order_Model");
		$postData = $this->input->post('data');
		$cancelData = $this->input->post('cancelData');

		$trans_details_Id = $postData[0]["trans_details_Id"];
		$seller_id = $postData[0]["sellerid"];
		$order_id = $postData[0]["order_id"];
		$reason = $cancelData[0]["reason"];//$this->input->post("reason");
		//unset($postData['cancelData']);
		/*if(!$trans_details_Id){
			echo json_encode(array("status"=>"0","msg"=>"Transaction detail id is missing.","code"=>"0"));
			exit();
		}
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		if(!$order_id){
			echo json_encode(array("status"=>"0","msg"=>"Order id is missing.","code"=>"0"));
			exit();
		}
		if(!$reason){
			echo json_encode(array("status"=>"0","msg"=>"Reason is missing.","code"=>"0"));
			exit();
		}
		$post_data = $this->input->post();*/
		//echo "<pre>";print_r($_POST);die();
		$data = $this->Order_Model->declineOrder($postData);
		if($data){
			$emailData = $this->Order_Model->getProductDataByOrder($order_id,$trans_details_Id);
			$loaction = array("shipping","billing");

			$location['shipping'] = unserialize($emailData->shipping);
			$location['shipping']['state'] = (is_numeric($location['shipping']['state']))?getCountryNameByKeyValue('id', $location['shipping']['state'], 'code', true,'tbl_states'):$location['shipping']['state'];
			$location['shipping']['country'] = getCountryNameByKeyValue('id', $location['shipping']['country'], 'nicename', true);
			
			$location['billing'] = unserialize($emailData->shipping);
			$location['billing']['state'] = (is_numeric($location['billing']['state']))?getCountryNameByKeyValue('id', $location['billing']['state'], 'code', true,'tbl_states'):$location['billing']['state'];
			$location['billing']['country'] = getCountryNameByKeyValue('id', $location['billing']['country'], 'nicename', true);

			$product['products'] = array("price"=>$emailData->price,"qty"=>$emailData->qty,"shipping_price"=>$emailData->item_shipping_amount,"name"=>$emailData->product_name,"is_local"=>$emailData->is_local,"img"=>$emailData->thumbnail);
			$subject = "Your Zab.ee Order ".$order_id." Is Canceled";
			$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Your order '.$order_id.' has been canceled. Your credit card has not been charged.</p><p style="margin-bottom:0px;font-size:16px; font-weight:bold">Reason for Cancelation:</p><span>'.$reason.'</span>';
			//$this->Utilz_Model->send_mail($location,$product,$order_id,$emailData->email, $type="buyer",$subject,$text,$emailData->firstname);
		}
		echo json_encode($data);
	}
	public function getCanellationRequests(){
		$seller_id = $this->input->get("seller_id");
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		$start = $this->input->get("start");
		$offset = $this->input->get("length");
		/*if(!$start){
			$start = 10;
		}
		if(!$offset){
			$offset = 0;
		}*/
		$where = array("td.seller_id"=>$seller_id,"td.cancellation_pending"=>"1");
		$join = array(DBPREFIX.'_users u'=>'td.user_id = u.userid');
		$select = 'td.order_id as order_id, UNIX_TIMESTAMP(td.created) as created, concat(u.firstname, " ", u.lastname) as username';
		$table = DBPREFIX.'_transaction_details td';
		$data =  $this->Product_Model->getData($table,$select,$where,"","td.order_id","td.id DESC",$join,$start,"",$offset);
		echo json_encode($data);
	}
	public function getCancelOrderDetails(){
		$this->load->model("admin/Order_Model");
		$user_id = $this->input->get("user_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$order_id = $this->input->get("order_id");
		if(!$order_id){
			echo json_encode(array("status"=>"0","msg"=>"Order id is missing.","code"=>"0"));
			exit();
		}
		$data = $this->Order_Model->get_canceOrderDetails($order_id,$user_id,1);
		echo json_encode($data);
	}
	public function acceptOrDeclinedCancelOrder(){
		$this->load->model("admin/Order_Model");
		$user_id = $this->input->post("user_id");
		$order_id = $this->input->post("order_id");
		$hubx_id = $this->input->post("hubx_id");
		$id = $this->input->post("transaction_detail_id");
		$value = $this->input->post("value");
		if(!$value){
			// value 1 means approve - value 2 means decline
			echo json_encode(array("status"=>"0","msg"=>"Action value is missing.","code"=>"0"));
			exit();
		}
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$order_id){
			echo json_encode(array("status"=>"0","msg"=>"Order id is missing.","code"=>"0"));
			exit();
		}
		if(!$order_id){
			echo json_encode(array("status"=>"0","msg"=>"Transaction detail id is missing.","code"=>"0"));
			exit();
		}
		$value = ($value == "1")?0:1;
		$data = array("value"=>$value,"can_id"=>$order_id,"row_id"=>$id,"user_id"=>$user_id,"hubx_id"=>$hubx_id);
		$return = $this->Order_Model->cancel_order_confirm($data);
		if($return){
			echo json_encode(array("status"=>"1","msg"=>"Success.","code"=>"1"));
			exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error.","code"=>"0"));
			exit();
		}
	}
	//Discount Policy
	public function getDiscountPolices(){
		$user_id = $this->input->get("user_id");
		$is_active = $this->input->get("is_active");
		$select = "";
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if($is_active){
			$select = "AND display_status='1'";
		}
		$where = "(userid='$user_id' OR user_type='1') AND active='1'".$select;
		//array("userid"=>$user_id,"active"=>"1","display_status"=>"1")
		$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,UNIX_TIMESTAMP(valid_from) as valid_from,UNIX_TIMESTAMP(valid_to) as valid_to,value,title,type,display_status,user_type',$where);
		//$discount =  $this->Product_Model->getData(DBPREFIX.'_policies','id,valid_from,valid_to,value,title',array("userid"=>$user_id,"active"=>"1","display_status"=>"1"));
		if($is_active){
			$return=array();
			if($discount){
				$today = strtotime(date('Y-m-d'));
				foreach($discount as $d){
					if($d->valid_to >= $today){
						$return[] = $d;
					}
				}
			}
		}else{
			$return = $discount;
		}
		echo json_encode($return);
	}
	public function dateDifference($order_date){
		$order_date = date_create($order_date." A");
		$system_date = date_create(date('Y-m-d h:i:s A'));
		$days  = date_diff($order_date,$system_date)->days;
		return $days;
	}
	public function addDiscount(){
		$user_id = $this->input->post("user_id");
		$value = $this->input->post("value");
		$type = $this->input->post("type");
		$valid_from = $this->input->post("valid_from");
		$valid_to = $this->input->post("valid_to");
		$title = $this->input->post("title");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$title){
			echo json_encode(array("status"=>"0","msg"=>"Title is missing.","code"=>"0"));
			exit();
		}
		if(!$valid_from){
			echo json_encode(array("status"=>"0","msg"=>"Valid From is missing.","code"=>"0"));
			exit();
		}
		if(!$valid_to){
			echo json_encode(array("status"=>"0","msg"=>"Valid To is missing.","code"=>"0"));
			exit();
		}
		if(!$type){
			echo json_encode(array("status"=>"0","msg"=>"Type is missing.","code"=>"0"));
			exit();
		}
		if(!$value){
			echo json_encode(array("status"=>"0","msg"=>"Value is missing.","code"=>"0"));
			exit();
		}
		$this->load->model("admin/Policies_Model");
		$insert_data = array("fromDate"=>$valid_from,"toDate"=>$valid_to,"FixedorPercent"=>$type,"valueOfPercentOrFixed"=>$value,"discount_title"=>$title);
		$resp = $this->Policies_Model->policies_add($insert_data, $user_id);
		if($resp){
			echo json_encode(array("status"=>"1","msg"=>"Policy Add Successfully.","code"=>"1"));exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in adding.","code"=>"2"));exit();
		}
	}
	public function updateDiscount(){
		$this->load->model("admin/Policies_Model");
		$user_id = $this->input->post("user_id");
		$policy_id = $this->input->post("policy_id");
		$value = $this->input->post("value");
		$type = $this->input->post("type");
		$valid_from = $this->input->post("valid_from");
		$valid_to = $this->input->post("valid_to");
		$title = $this->input->post("title");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$policy_id){
			echo json_encode(array("status"=>"0","msg"=>"Policy id is missing.","code"=>"0"));
			exit();
		}
		if(!$title){
			echo json_encode(array("status"=>"0","msg"=>"Title is missing.","code"=>"0"));
			exit();
		}
		if(!$valid_from){
			echo json_encode(array("status"=>"0","msg"=>"Valid From is missing.","code"=>"0"));
			exit();
		}
		if(!$valid_to){
			echo json_encode(array("status"=>"0","msg"=>"Valid To is missing.","code"=>"0"));
			exit();
		}
		if(!$type){
			echo json_encode(array("status"=>"0","msg"=>"Type is missing.","code"=>"0"));
			exit();
		}
		if(!$value){
			echo json_encode(array("status"=>"0","msg"=>"Value is missing.","code"=>"0"));
			exit();
		}
		$insert_data = array("fromDate"=>$valid_from,"toDate"=>$valid_to,"FixedorPercent"=>$type,"valueOfPercentOrFixed"=>$value,"title"=>$title);
		$resp = $this->Policies_Model->policies_update($insert_data, $user_id,$policy_id);
		if($resp){
			echo json_encode(array("status"=>"1","msg"=>"Policy Update Successfully.","code"=>"1"));exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in adding.","code"=>"2"));exit();
		}
	}
	public function policySDA(){
		$this->load->model("admin/Policies_Model");
		$user_id = $this->input->post("user_id");
		$policy_id = $this->input->post("policy_id");
		$action = $this->input->post("action");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$policy_id){
			echo json_encode(array("status"=>"0","msg"=>"Policy id is missing.","code"=>"0"));
			exit();
		}
		if($action == "delete"){
			$resp = $this->Policies_Model->hard_delete_discount($policy_id,$user_id);
		}else if($action == "apply_to_all"){
			$resp = $this->Policies_Model->applyDiscount($policy_id, $user_id);
		}else{
			$status = $this->input->post('status');
			if(!$status){
				$status = "0";
			}
			$resp = $this->Policies_Model->deleteDiscount($policy_id, $user_id, $status);
		}
		if($resp){
			$code = (isset($resp['code']))?"3":"1";
			$msg = (isset($resp['code']))?$resp['code']:"Success.";
			echo json_encode(array("status"=>"1","msg"=>$msg,"code"=>$code));exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error.","code"=>"2"));exit();
		}
	}
	//Product Accessories
	//Temprory
	public function getproductAccessories(){ 
		$id = $this->input->get("id");
		$user_id = $this->input->get("seller_id");
		$search = $this->input->get("search");
		$start = $this->input->get("start");
		$length = $this->input->get("length");
		$product_id = $this->input->get("product_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		if(!$start){
			$start = 0;
		}
		if(!$length){
			$length = 10;
		}
		if($user_id != ""){
			//$select = "pa.id as id,p.product_id, p.product_name as product,pa.accessory_id, a.product_name as accessory";
			$getAccData = $this->Product_Model->getAccDataByProdId($user_id, $id,"",$search,$start,$length,$product_id);
			//echo "<pre>";print_r($getAccData);die();
			$data =array();
			if($getAccData){
				foreach($getAccData as $ind=>$get){
					$data[$ind]['product_id'] = $get->product_id;
					$data[$ind]['product_name'] = $get->product;
					$data[$ind]['pro_acc_id'] = $get->id;
					$accessory = explode(",",$get->accessory);
					foreach($accessory as $index=>$value){
						$acc = explode("-zabee-",$value);
						$data[$ind]['accessories'][$index]['accessory_id'] = $acc[0];
						$data[$ind]['accessories'][$index]['accessory'] = $acc[1];
					}
				}
			}
		}
		echo json_encode($data);		
	}
	public function accessoriesAddEdit(){
		$return = false;
		$user_id = $this->input->post("user_id");
		$accessories_id = $this->input->post("accessories");
		$product_id = $this->input->post("product_id");
		$product_name = $this->input->post("product_name");
		$prod_acc_id = $this->input->post("prod_acc_id");
		$table  = DBPREFIX."_product_accessories";
		//echo "<pre>";print_r($this->input->post());die();
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$accessories_id){
			echo json_encode(array("status"=>"0","msg"=>"Accessories id is missing.","code"=>"0"));
			exit();
		}
		if(!$product_id){
			echo json_encode(array("status"=>"0","msg"=>"Product id is missing.","code"=>"0"));
			exit();
		}
		if(!$product_name){
			echo json_encode(array("status"=>"0","msg"=>"Product name is missing.","code"=>"0"));
			exit();
		}
		$table  = DBPREFIX."_product_accessories";
		if($prod_acc_id == ""){
			$acc_id = implode(",", $accessories_id);
			$saveData = array("product_id"=>$product_id,'accessory_id'=>$acc_id, "seller_id" => $user_id);
			$return = $this->Product_Model->saveData($saveData,$table);
			if($return){
				$return = true;
			}else{
				echo json_encode(array("status"=>0,"msg"=>"Error in adding.","code"=>2));exit();
			}
		}else{
			$acc_data = array("accessory_id" => implode(",",$accessories_id));
			$where = array("id"=> $prod_acc_id);
			$resp = $this->Product_Model->updateData($table,$acc_data,$where);
			if($resp){
				$return = true;
			} else {
				echo json_encode(array("status"=>0,"msg"=>"Error in updating.","code"=>2));exit();
			}
		}
		
		if($return){
			$msg = ($prod_acc_id)?"Update":"Add";
			echo json_encode(array("status"=>1,"msg"=>"Accessories ".$msg." successfully.","code"=>1));exit();	
		}
	}
	public function prod_acc_delete(){
		$user_id = $this->input->post("user_id");
		$prod_acc_id = $this->input->post("prod_acc_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$prod_acc_id){
			echo json_encode(array("status"=>"0","msg"=>"Pro Acc id is missing.","code"=>"0"));
			exit();
		}
		$resp = $this->Product_Model->delete_prod_acc($prod_acc_id, $user_id);	
		if($resp){
			echo json_encode(array("status"=>1,"msg"=>"Accessories delete successfully.","code"=>1));exit();	
		}else{
			echo json_encode(array("status"=>0,"msg"=>"Error in deleting.","code"=>0));exit();	
		} 
	}
	//Return Policy
	public function getReturnPolicy(){
		$this->load->model("admin/Returntype_Model");
		$user_id = $this->input->get("user_id");
		$return_id = $this->input->post("user_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$select = "id,user_type,returns,return_period,rma_required,restocking_fee,restocking_type,returnpolicy_name,is_default,active";
		$returns = $this->Returntype_Model->getReturnPolicyforProduct($user_id,$return_id,$select);
		echo json_encode($returns);exit();
	}
	public function returnPolicyAddEdit(){
		$this->load->model("admin/Returntype_Model");
		$post_data = $this->input->post();
		//echo "<pre>";print_r($post_data);die();
		$user_id = $this->input->post("user_id");
		$return_id = $this->input->post("return_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$this->load->library('form_validation');
		$this->form_validation->set_rules('return_YesNo','Return Yes or No','required');
		$this->form_validation->set_rules('rma_YesNo','RMA Yes or No','required');
		$this->form_validation->set_rules('percent_fixed','Percent/Fixed','required');
		$this->form_validation->set_rules('returnPeriod','Return Period','trim|required');
		$this->form_validation->set_rules('restockingFee','Restocking Fee','trim|required');
		$this->form_validation->set_rules('returnPolicyName','Return Policy Name','trim|required');
		if($this->form_validation->run() === true){
			if($return_id){
				$resp = $this->Returntype_Model->returnType_update($post_data, $user_id, $return_id);
			}else{
				$resp = $this->Returntype_Model->returnType_add($post_data, $user_id);
			}
			if($resp){
				echo json_encode(array("status"=>"1","msg"=>"Return Policy Add/Update Successfully.","code"=>"1"));
				exit();
			} else {
				echo json_encode(array("status"=>"0","msg"=>"Error in adding return policy.","code"=>"0"));
				exit();
			}	
		}else{
			echo json_encode(array("status"=>"0","msg"=>validation_errors_api(),"code"=>"2"));
			exit();
		}
	} 
	public function returnPolicyDefault(){
		$this->load->model("admin/Returntype_Model");
		$user_id = $this->input->post("user_id");
		$return_id = $this->input->post("return_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$return_id){
			echo json_encode(array("status"=>"0","msg"=>"Return id is missing.","code"=>"0"));
			exit();
		}
		$resp = $this->Returntype_Model->returnType_forDefault($return_id, $user_id);
		if($resp){
			echo json_encode(array("status"=>"1","msg"=>"Return Policy Default Successfully.","code"=>"1"));
			exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in Default Return Policy.","code"=>"0"));
			exit();
		}	
	}
	public function returnPolicyDelete(){
		$this->load->model("admin/Returntype_Model");
		$user_id = $this->input->post("user_id");
		$return_id = $this->input->post("return_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$return_id){
			echo json_encode(array("status"=>"0","msg"=>"Return id is missing.","code"=>"0"));
			exit();
		}
		$insert_data = array(
			'active' 		=> 0,
			'is_default' 	=> '0',
			'is_default_2' 	=> '0'
		);
		$resp = $this->Returntype_Model->deleteReturn($return_id, $user_id, $insert_data);
		if($resp){
			echo json_encode(array("status"=>"1","msg"=>"Return Policy Delete Successfully.","code"=>"1"));
			exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error in Delete Return Policy.","code"=>"0"));
			exit();
		}	
	}
	//Discount Voucher
	public function get_discount_voucher(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'vouchers'=> array());
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('voucher_id',$this->lang->line('voucher_id'),'trim');
		$this->form_validation->set_rules('voucher_code',$this->lang->line('voucher_code'),'trim');
		$this->form_validation->set_rules('search',$this->lang->line('search'),'trim');
		$this->form_validation->set_rules('page',$this->lang->line('page'),'trim');
		$this->form_validation->set_rules('rows',$this->lang->line('rows'),'trim');
		if($this->form_validation->run() === true){
			$this->load->model("admin/Policies_Model");
			$seller_id = $this->input->post('user_id');
			$voucher_id = $this->input->post('voucher_id');
			$voucher_code = $this->input->post('voucher_code');
			$search = $this->input->post('search');
			$page = $this->input->post('page');
			$length = ($this->input->post('rows'))?$this->input->post('rows'):100;
			$offset = ($page > 1)?($page-1) * $length:0;
			//print_r($this->input->post());die();
			//echo $offset .' - '.$length;die();
			$vouchers = $this->Policies_Model->getVoucher($seller_id, $voucher_id, $voucher_code, $search, $offset, $length);
			$response['status'] = 1;
			$response['message'] = 'OK';
			$response['vouchers'] = $vouchers;
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function save_discount_voucher(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('voucher_code',$this->lang->line('dicount_coupon'),'trim|required|callback_checkVoucher');
		$this->form_validation->set_rules('discount_title',$this->lang->line('title'),'trim|required');
		$this->form_validation->set_rules('discount_limit',$this->lang->line('limit'),'trim|required');
		$this->form_validation->set_rules('fromDate',$this->lang->line('valid_from'),'required');
		$this->form_validation->set_rules('toDate',$this->lang->line('valid_to'),'required');
		$this->form_validation->set_rules('discount_type',$this->lang->line('discount_type'),'required');
		$this->form_validation->set_rules('discount_value',$this->lang->line('discount_value'),'required');
		$this->form_validation->set_rules('min_price',$this->lang->line('min_amount'),'trim|required');
		$this->form_validation->set_rules('max_price',$this->lang->line('max_amount'),'trim|required');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		if($this->form_validation->run() === true){
			$this->load->model("admin/Policies_Model");
			$seller_id = $this->input->post('user_id');
			$post_data = $this->input->post();
			$isAdmin = false;
			$resp = $this->Policies_Model->voucher_add($post_data, $seller_id, $isAdmin);
			if($resp){
				$response['status'] = 1;
				$response['message'] = 'OK';
			} else {
				$response['message'] = 'Error in adding';
			}
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function update_discount_voucher(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('voucher_id',$this->lang->line('voucher_id'),'trim|required');
		$this->form_validation->set_rules('discount_title',$this->lang->line('title'),'trim|required');
		$this->form_validation->set_rules('discount_limit',$this->lang->line('limit'),'trim|required');
		$this->form_validation->set_rules('fromDate',$this->lang->line('valid_from'),'required');
		$this->form_validation->set_rules('toDate',$this->lang->line('valid_to'),'required');
		$this->form_validation->set_rules('discount_type',$this->lang->line('discount_type'),'required');
		$this->form_validation->set_rules('discount_value',$this->lang->line('discount_value'),'required');
		$this->form_validation->set_rules('min_price',$this->lang->line('min_amount'),'trim|required');
		$this->form_validation->set_rules('max_price',$this->lang->line('max_amount'),'trim|required');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		if($this->form_validation->run() === true){
			$this->load->model("admin/Policies_Model");
			$user_id = $this->input->post('user_id');
			$voucher_id = $this->input->post('voucher_id');
			$post_data = $this->input->post();
			$isAdmin = false;
			$resp = $this->Policies_Model->voucher_update($post_data, $user_id, $voucher_id, $isAdmin);
			if($resp){
				$response['status'] = 1;
				$response['message'] = 'OK';
			} else {
				$response['message'] = 'Error in adding';
			}
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function delete_discount_voucher(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('voucher_id',$this->lang->line('voucher_id'),'trim|required');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		if($this->form_validation->run() === true){
			$voucher_id = $this->input->get('voucher_id');
			$user_id = $this->input->get('user_id');
			$this->load->model("admin/Policies_Model");
			if($this->Policies_Model->deleteDiscountVoucher($voucher_id, $user_id)){
				$response['status'] = 1;
				$response['message'] = 'Deleted';
			} else {
				$response['message'] = 'Error in deleting';
			}
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function check_discount_voucher_code(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('voucher_code',$this->lang->line('dicount_coupon'),'trim|required');
		if($this->form_validation->run() === true){
			$this->load->model("admin/Policies_Model");
			$code = $this->input->get('voucher_code');
			$check = $this->Policies_Model->checkVoucher($code);
			if($check){
				$response['status'] = 1;
				$response['message'] = 'OK';
			} else {
				$response['message'] = 'Voucher already exist.';
			}
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function checkVoucher($code){
		$this->load->model("admin/Policies_Model");
		$check = $this->Policies_Model->checkVoucher($code);
		if(!$check){
			$this->form_validation->set_message('checkVoucher', 'Voucher already exist.');
			return FALSE;
		} else {
			return TRUE;
		}
	}
	public function get_discount_voucher_data(){
		$response = array();
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('term',$this->lang->line('term'),'trim|required');
		$this->form_validation->set_rules('apply_on',$this->lang->line('apply_on'),'trim|required');
		if($this->form_validation->run() === true){
			$term = $this->input->get('term');
			$apply_on = $this->input->get('apply_on');
			$user_id = $this->input->get('user_id');
			$user_type = $this->input->get('user_type');
			$this->load->model("admin/Policies_Model");
			$response = $this->Policies_Model->getDiscountItemsData($term, $apply_on, $user_id, $user_type);
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	public function get_shippings(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'shipping'=> array(), 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('shipping_id',$this->lang->line('shipping_id'),'trim');
		$this->form_validation->set_rules('search',$this->lang->line('search'),'trim');
		$this->form_validation->set_rules('page',$this->lang->line('page'),'trim');
		$this->form_validation->set_rules('rows',$this->lang->line('rows'),'trim');
		if($this->form_validation->run() === true){
			$user_id = $this->input->get('user_id');
			$shipping_id = $this->input->get('shipping_id');
			$search = $this->input->get('search');
			$page = $this->input->get('page');
			$length = ($this->input->get('rows'))?$this->input->get('rows'):100;
			$offset = ($page > 1)?($page-1) * $length:0;
			$this->load->model("admin/Shipping_Model");
			$response['shipping'] = $this->Shipping_Model->getShippingByUser($user_id, $shipping_id, $search, $offset, $length);
			$response['status'] = 1;
			$response['message'] = 'OK';
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	
	public function save_shipping(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('user_type',$this->lang->line('user_type'),'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'xss_clean|trim|required');
		$this->form_validation->set_rules('price', 'Base Price', 'xss_clean|trim|required');
		$this->form_validation->set_rules('shipping_type', 'Price Base on', 'xss_clean|trim|required');
		$baseType = $this->input->post('shipping_type');
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
		$this->form_validation->set_rules('incremental_unit', 'Incremental Unit', 'xss_clean|trim|required');
		$this->form_validation->set_rules('incremental_price', 'Incremental Price', 'xss_clean|trim|required');
		$this->form_validation->set_rules('free_after', 'Free Shipping After', 'xss_clean|trim');
		$this->form_validation->set_rules('minimum_days', 'Minimum Days', 'xss_clean|trim|required');
		$this->form_validation->set_rules('maximum_days', 'Maximum Days', 'xss_clean|trim|required');
		$this->form_validation->set_rules('description', 'Description', 'xss_clean|trim');
		if($this->form_validation->run() === true){
			$post_data = $this->input->post();
			$post_data['basedOn'] = $post_data['shipping_type'];
			$post_data['inc_price'] = $post_data['incremental_price'];
			$post_data['inc_unit'] = $post_data['incremental_unit'];
			//echo '<pre>';print_r($post_data);echo '</pre>';die();
			$this->load->model("admin/Shipping_Model");
			$cat = $this->Shipping_Model->shipping_add($post_data, $post_data['user_id']);
			if($cat){
				$response['message'] = $this->lang->line('created_shipping');
				$response['status'] = 1;
			} else {
				$response['message'] = $this->lang->line('already_exist_shipping');
			}	
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	function update_shipping(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'code'=>'000', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('shipping_id',$this->lang->line('shipping_id'),'trim|required');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('user_type',$this->lang->line('user_type'),'trim|required');
		$this->form_validation->set_rules('title', 	$this->lang->line('title'), 'xss_clean|trim|required');
		$this->form_validation->set_rules('price', $this->lang->line('base_price'), 'xss_clean|trim|required');
		$this->form_validation->set_rules('shipping_type', $this->lang->line('shipping_type'), 'xss_clean|trim|required');
		$baseType = $this->input->post('shipping_type');
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
		$this->form_validation->set_rules('incremental_unit', 'Incremental Unit', 'xss_clean|trim|required');
		$this->form_validation->set_rules('incremental_price', 'Incremental Price', 'xss_clean|trim|required');
		$this->form_validation->set_rules('free_after', 'Free Shipping After', 'xss_clean|trim');
		$this->form_validation->set_rules('minimum_days', 'Minimum Days', 'xss_clean|trim|required');
		$this->form_validation->set_rules('maximum_days', 'Maximum Days', 'xss_clean|trim|required');
		$this->form_validation->set_rules('description', 'Description', 'xss_clean|trim');
		if($this->form_validation->run() === true){
			$post_data = $this->input->post();
			$user_id = $post_data['user_id'];
			$shipping_id = $post_data['shipping_id'];
			$post_data['basedOn'] = $post_data['shipping_type'];
			$post_data['inc_price'] = $post_data['incremental_price'];
			$post_data['inc_unit'] = $post_data['incremental_unit'];
			$this->load->model("admin/Shipping_Model");
			$response = $this->Shipping_Model->save_edit_shipping($post_data,$shipping_id,$user_id);
		} else {
			$response['message'] = 'Error!';
			$response['code'] = '001';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function status_shipping(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('shipping_id',$this->lang->line('shipping_id'),'trim|required');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('status',$this->lang->line('status'),'trim|required');
		if($this->form_validation->run() === true){
			$shipping_id = $this->input->post('shipping_id');
			$user_id = $this->input->post('user_id');
			$status = $this->input->post('status');
			$this->load->model("admin/Shipping_Model");
			$s_delete = $this->Shipping_Model->updateShippingStauts($shipping_id, $status, $user_id);
			if($s_delete['status'] == 1){
				$response['status'] = 1;
				$response['message'] = $this->lang->line('updated_shipping');
			} else {
				$response['message'] = 'Error in updating status';
			}
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function delete_shipping(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('shipping_id',$this->lang->line('shipping_id'),'trim|required');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		if($this->form_validation->run() === true){
			$shipping_id = $this->input->get('shipping_id');
			$user_id = $this->input->get('user_id');
			$this->load->model("admin/Shipping_Model");
			if($this->Shipping_Model->delete_shipping($shipping_id, $user_id)){
				$response['status'] = 1;
				$response['message'] = 'Deleted';
			} else {
				$response['message'] = 'Error in deleting';
			}
		} else {
			$response['message'] = 'Error!';
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	public function getTax(){
		$response = array("status"=>0,"msg"=>"Invalid request","code"=>"000", 'tax'=>0);
		$proceed = true;
		if(!$this->input->get("zipcode")){
			$response["msg"] = "zipcode is missing.";
			$response["code"] = "001";
			$proceed = false;
		}
		if(!$this->input->get("cart_total")){
			$response["msg"] = "Cart total is missing.";
			$response["code"] = "002";
			$proceed = false;
		}
		if($proceed){
			$zipcode = $this->input->get("zipcode");
			$cart_total = $this->input->get("cart_total");
			$this->load->model('Cart_Model');
			$tax = $this->Cart_Model->getTax($zipcode, $cart_total);
			$response["status"] = 1;
			$response["msg"] = "OK";
			$response["tax"] = $tax;
		}
		echo json_encode($response);exit();
	}
	//Store Api
	public function getStore(){
		$seller_id = $this->input->get("seller_id");
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		$data = array("msg"=>"","getLegalBusniessType"=>array(),"user_store"=>array());
		$store 							= $this->Admin_User_Model->checkUserStore($seller_id);
		if($store['rows'] > 0 && $store['result']->s_id !=""){
			$data['getLegalBusniessType'] 	= $this->Secure_Model->getLegalBusniessType();
			$data['user_store']			= $store['result'];
		}else{
			$data['msg'] = "Store is not verified";
		}
		echo json_encode($data);exit();
	}
	public function saveStore(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('store_name','Store Name','trim|required');
		$this->form_validation->set_rules('country','Country','trim|required');
		$this->form_validation->set_rules('contact_phone','Contact Phone','trim|required');
		$this->form_validation->set_rules('contact_email','Contact Email','trim|required');
		$this->form_validation->set_rules('legal_busniess_type','Legal Busniess Type','required');
		$this->form_validation->set_rules('is_zabee','is zabee','required|is_natural');		
		if($this->form_validation->run() === true){
			extract($this->input->post());
			$today = date('Y-m-d H:i:s');
			$store_id = strtolower(str_replace(' ','-',$store_name));
			$data = array('updated_date'			=>$today,
						  'seller_id'				=>$seller_id,
						  'store_name' 				=>addslashes($store_name),
						  'store_id'				=>$store_id,
						  'legal_busniess_type'		=>$legal_busniess_type,
						  'country_id'				=>$country,
						  'contact_phone'			=>$contact_phone,
						  'contact_email'			=>$contact_email,
						  'is_zabee'				=>$is_zabee,
						  'is_tax'                  =>(isset($is_tax))?$is_tax:"0",
						  'is_approve'				=>"0"
						);
			if(isset($_FILES['store_image']) && $_FILES['store_image']['name'] !=""){
				$config['upload_path'] 				= "store_logo";
				$config['upload_thumbnail_path'] 	= "store_logo/thumbs";
				$config['allowed_types'] 			= 'gif|jpg|png|jpeg';
				$config['quality'] 					= "100%";
				$config['remove_spaces'] 			= TRUE;

				$params['file'] 		= curl_file_create($_FILES['store_image']['tmp_name'], $_FILES['store_image']['type'], $_FILES['store_image']['name']);
				$params['image_type'] 	= "store_logo";
				$params['filesize'] 	= $_FILES['store_image']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				//print_r($file); die();
			}
			if(isset($_FILES['store_image_cover']) && $_FILES['store_image_cover']['name'] !=""){
				$config['upload_path'] 				= "store_cover";
				$config['upload_thumbnail_path'] 	= "store_cover/thumbs";
				$config['allowed_types'] 			= 'gif|jpg|png|jpeg';
				$config['quality'] 					= "100%";
				$config['remove_spaces'] 			= TRUE;
				
				$params['file'] 		= curl_file_create($_FILES['store_image_cover']['tmp_name'], $_FILES['store_image_cover']['type'], $_FILES['store_image_cover']['name']);
				$params['image_type'] 	= "store_cover";
				$params['filesize'] 	= $_FILES['store_image_cover']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server 			= $this->config->item('media_url').'/file/upload_media';
				$file 					= $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$data['cover_image']	= $file->images->original->filename;
			}
			$table = 'tbl_seller_store';
			$result = $this->Admin_User_Model->saveData($data,$table);
			$table = 'tbl_state_tax';
			$state_id = isset($stateId)?$stateId:"";
			if($state_id != ""){
				$states = explode(",",$state_id);
				foreach($states as $state){
				if($state != ""){
					$data = array(
						's_id' => $result,
						'state_id' => $state,
						'userid' => $seller_id
						);
						$return = $this->Admin_User_Model->saveData($data,$table);
					}
				}
			}
			if($result){
				echo json_encode(array("status"=>"1","msg"=>"Store Create Successfully.","code"=>"1"));exit();
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Error in Store Creation.","code"=>"0"));exit();
			}
		}
	}
	public function updateStore(){
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->form_validation->set_rules('store_name','Store Name','trim|required');
		$this->form_validation->set_rules('country','Country id','trim|required');
		$this->form_validation->set_rules('contact_phone','Contact Phone','trim|required');
		$this->form_validation->set_rules('contact_email','Contact Email','trim|required');
		$this->form_validation->set_rules('legal_busniess_type','Legal Busniess Type','required');
		$this->form_validation->set_rules('is_zabee','is zabee','required|is_natural');
		if($this->form_validation->run() === true){	
			extract($this->input->post());
			$state_id = isset($stateId)?$stateId:"";
			$table = 'tbl_state_tax';
			if($state_id != ""){
				$states = explode(",",$state_id);
				$deletePrevious = $this->Admin_User_Model->deleteData(array('userid'=>$seller_id,'s_id'=>$s_id));
				foreach($states as $state){
				if($state != ""){
					$data = array(
						's_id' => $s_id,
						'state_id' => $state,
						'userid' => $seller_id
						);
						$return = $this->Admin_User_Model->saveData($data,$table);
					}
				}
			}
			$store_id = strtolower(str_replace(' ','-',$store_name));
			$today = date('Y-m-d H:i:s');
			$data = array('updated_date'			=>$today,
						  'seller_id'				=>$seller_id,
						  'store_name' 				=>addslashes($store_name),
						  'store_id'				=>$store_id,
						  'legal_busniess_type'		=>$legal_busniess_type,
						  'country_id'				=>$country,
						  'contact_phone'			=>$contact_phone,
						  'contact_email'			=>$contact_email,
						  'is_zabee'				=>$is_zabee,
						  'is_tax'                	=>$is_tax
						  );
			/*if($is_zabee == '1'){
				$zabee_id = $this->Admin_User_Model->getZabeeWarehouse();
				if($zabee_id){
					$data['warehouse_id'] 	 = $zabee_id[0]->warehouse_id;
					$data['warehouse_title'] = $zabee_id[0]->warehouse_title;
				}
			}else{
				$data['warehouse_id'] 	 = "0";
				$data['warehouse_title'] = "";
			}*/
			if(isset($_FILES['store_image']) && $_FILES['store_image']['name'] !=""){
				$config['upload_path'] 			 = "store_logo";
				$config['upload_thumbnail_path'] = "store_logo/thumbs";
				$config['allowed_types'] 		 = 'gif|jpg|png|jpeg';
				$config['quality'] 				 = "100%";
				$config['remove_spaces'] 		 = TRUE;
				
				$params['file'] 		= curl_file_create($_FILES['store_image']['tmp_name'], $_FILES['store_image']['type'], $_FILES['store_image']['name']);
				$params['image_type'] 	= "store_logo";
				$params['filesize'] 	= $_FILES['store_image']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$data['store_logo']		= $file->images->original->filename;
			}
			if(isset($_FILES['store_image_cover']) && $_FILES['store_image_cover']['name'] !=""){
				$config['upload_path'] 				= "store_cover";
				$config['upload_thumbnail_path'] 	= "store_cover/thumbs";
				$config['allowed_types'] 			= 'gif|jpg|png|jpeg';
				$config['quality'] 					= "100%";
				$config['remove_spaces'] 			= TRUE;
				
				$params['file'] 		= curl_file_create($_FILES['store_image_cover']['tmp_name'], $_FILES['store_image_cover']['type'], $_FILES['store_image_cover']['name']);
				$params['image_type'] 	= "store_cover";
				$params['filesize'] 	= $_FILES['store_image_cover']['size'];
				$params['config'] 		= json_encode($config);
				$upload_server 			= $this->config->item('media_url').'/file/upload_media';
				$file 					= $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$data['cover_image']	= $file->images->original->filename;
			}
			$table = 'tbl_seller_store';
			$result = $this->Admin_User_Model->updateData($data,array('seller_id'=>$seller_id,'s_id'=>$s_id),$table);
			if($result){
				echo json_encode(array("status"=>"1","msg"=>"Store update Successfully.","code"=>"1","data"=>$data));exit();
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Error in Store Updation.","code"=>"0"));exit();
			}
		}
	}
	public function get_notifications(){
		$response = array('status'=>"0", 'message'=>$this->lang->line('err_try_again'), 'error'=>array(), 'new_notifications'=> 0, 'notifications'=> array());
		$this->load->library('form_validation');
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('user_type',$this->lang->line('user_type'),'trim|required');
		$this->form_validation->set_rules('status',$this->lang->line('status'),'trim|required');
		if($this->form_validation->run() === true){
			$user_id = $this->input->get('user_id');
			$user_type = $this->input->get('user_type');
			$status = $this->input->get('status');
			$this->load->model("admin/Utilz_Model");
			$notifications_count = $this->Utilz_Model->getNotifications($user_id, $user_type, 'new', true);
			$response['new_notifications'] = $notifications_count->notifications;
			if($status == 'all'){
				$response['notifications'] = $this->Utilz_Model->getTotalNotifications($user_id, $user_type, $status, false);
			} else {
				$response['notifications'] = $this->Utilz_Model->getNotifications($user_id, $user_type, $status, false);
			}
			if($notifications_count->notifications > 0){
				$response['status'] = "1";
				$response['message'] = $this->lang->line('ok');
			}
		} else {
			$response['message'] = $this->lang->line('err');
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	
	public function update_notifications(){
		$response = array('status'=>"0", 'message'=>$this->lang->line('err_try_again'), 'error'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('user_type',$this->lang->line('user_type'),'trim|required');
		$this->form_validation->set_rules('notification_id',$this->lang->line('notification_id'),'trim|required');
		if($this->form_validation->run() === true){
			$user_id = $this->input->post('user_id');
			$user_type = $this->input->post('user_type');
			$notification_id = $this->input->post('notification_id');
			if($this->Utilz_Model->updateNotificationStatus($user_id, $user_type, $notification_id)){
				$response['status'] = "1";
				$response['message'] = $this->lang->line('ok');
			} else {
				$response['message'] = 'unable to flag notificatins!';
			}
		} else {
			$response['message'] = $this->lang->line('err');
			$response['error'] = validation_errors_api();
		}
		echo json_encode($response);
	}
	//Notificaiton
	public function getNotifications(){
		$user_id = $this->input->get("user_id");
		$user_type = $this->input->get("user_type");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$user_type){
			echo json_encode(array("status"=>"0","msg"=>"User type is missing.","code"=>"0"));
			exit();
		}
		$data = array('message'=>"",'notification_count'=>0,'notification'=>"");
		$data['message'] = $this->checkUserTextNotificaiton = $this->checkTextNotification($user_id);
		$notification_count = $this->notificationCount = $this->Utilz_Model->getNotifications($user_id, $user_type, 'new', true);
		if(isset($notification_count->notifications) && $notification_count->notifications > 0){
			$data['notification_count'] = $notification_count->notifications;
		}
		$data['notification'] = $this->notifications = $this->Utilz_Model->getNotifications($user_id, $user_type, 'new', false);
		echo json_encode($data);die();
	}
	public function checkTextNotification($id){
		$checkMsgNotification = $this->Secure_Model->checkMsgNotification($id);
		$i = 0;
		if($checkMsgNotification['rows'] > 0){
			foreach($checkMsgNotification['result'] as $bl){
				$checkMsgNotification['result'][$i]->product_link="";
				$checkMsgNotification['result'][$i]->product_id="";
				$checkMsgNotification['result'][$i]->product_name="";
				if($bl->product_variant_id){
					if($bl->item_type == "product"){
						$product = $this->Secure_Model->checkProductDetails('','',$bl->product_variant_id);
						if($product['productDataRows'] > 0){
							$checkMsgNotification['result'][$i]->product_link=$product['productData']->product_name."-".$product['productData']->brand_name."-".$product['productData']->category_name;
							$checkMsgNotification['result'][$i]->product_id=$product['productData']->product_id;
							$checkMsgNotification['result'][$i]->product_name=$product['productData']->product_name;
						}
					}
				} 
				$i++;
			}
		}
		return $checkMsgNotification;
	}
	//Inventory
	public function deleteInventory(){
		$id = $this->input->post("inventory_id");
		if(!$id){
			echo json_encode(array("status"=>"0","msg"=>"inventory id is missing.","code"=>404));die();
		}
		if($id){
			$return = $this->Product_Model->deleteInventory($id,"3");
			echo json_encode(array("status"=>1,"msg"=>"Inventory Delete Successfully!","code"=>200));die();
		}else{
			echo json_encode(array("status"=>0,"message"=>"Error!","code"=>500));die();
		}
	}
	public function recreateInventory(){
		$id = $this->input->post("inventory_id");
		if(!$id){
			echo json_encode(array("status"=>"0","msg"=>"inventory id is missing.","code"=>404));die();
		}
		if($id){
			$return = $this->Product_Model->deleteInventory($id,"1");
			echo json_encode(array("status"=>1,"msg"=>"Inventory has been re-created Successfully!","code"=>200));die();
		}else{
			echo json_encode(array("status"=>0,"msg"=>"Inventory Id Missing!","code"=>500));die();
		}
	}
}
?>