<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
class Api extends CI_Controller {
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->model('Product_Model');
		$this->load->model('User_Model');
		$this->load->model('admin/Secure_Model');
		$this->load->model('Utilz_Model');
		$this->load->model('Referral_Model');
		$this->data['media_url'] = $this->config->item('product_path');
		$this->data['media_url_thumb'] = $this->config->item('product_thumb_path');
		$this->data['profile_path'] = $this->config->item('profile_path');
		// $this->data['media_base'] = 'https://dev.zab.ee/zabee_uploads/dev/uploads/';
		//$this->data['media_url'] = $this->data['media_base'].'product/';
		//$this->data['media_url_thumb'] = $this->data['media_base'].'/product/thumbs/';
		//$this->data['profile_path'] = $this->data['media_base'].'profile/';
		$this->data['review_url'] = $this->config->item('image_url').'review/';
	}
	public function getProduct($top_rated = ""){
		
		$user_id = $this->input->get('user_id');
		$querySelect = "";
		if($user_id !=""){
			$querySelect = ", IF(w.wish_id, 1,0) AS already_saved";
		}
		$select = 'product.product_name,product.product_id, IF(pm.is_local = "1", CONCAT("'.$this->data['media_url_thumb'].'",pm.thumbnail), pm.thumbnail) as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,if(AVG(preview.`rating`) IS NULL,"0.0",AVG(preview.`rating`)) AS rating,pin.price,,d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end, sp.seller_id'.$querySelect;
		if($top_rated =="top_rated"){
			$where = array('preview.rating >'=>3,'product.is_private' =>'0',"product.is_featured"=>"0"); 
			$product = $this->Product_Model->frontProductDetails('','','','','','8','rating DESC',$where,'product.product_id',$select,'',"",false,$user_id);
		}else if($top_rated == "featured"){
			$where = array('product.is_private' =>'0','cat.is_homepage'=>"1","product.is_featured"=>"1");
			$product = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',"",false,$user_id);
			//$product = $this->User_Model->getHomeProduct("product.is_featured","",$select,$user_id);
		}else if($top_rated == "new"){
			$where = array('product.is_private' =>'0','cat.is_homepage'=>"1","product.is_featured"=>"0");
			$product = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',"",false,$user_id);
		}else if($top_rated =="cat"){
			$category_id = $this->Utilz_Model->getShowCategory();
			$category = array();
			if(!empty($category_id)){
				$i=0;
				foreach($category_id as $cat_id){
					$c_id = $this->getAllCategoriesChildId($cat_id->category_id);
					if($c_id ==""){
						$c_id = $cat_id->category_id;
					}
					$where = 'cat.category_id IN('.$c_id.') AND product.is_private="0" AND cat.is_homepage="1" AND product.is_featured="0"';
					$category[$i] = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
					unset($category[$i]['rows']);
					$category[$i]['cat_id'] = $cat_id->category_id;
					$category[$i]['cat_name'] = $cat_id->category_name;
					$i++;
				}
			}
			$product['result'] = $category;
		}else{
			$product['result'] = array("status"=>0,"error"=>"Wrong parameter");
		}
		echo json_encode($product['result']);
	}
	public function chatBot(){
		
		$user_id = $this->input->post('user_id');
		$top_rated = $this->input->post('cat_name');
		if(!$top_rated){
			$top_rated ="top_rated";
			//echo json_encode(array("status"=>0,"msg"=>"Cat Name is missing."));exit;
		}
		$querySelect = "";
		if($user_id !=""){
			$querySelect = ", IF(w.wish_id, 1,0) AS already_saved";
		}
		$select = 'product.product_name,product.product_id, IF(pm.is_local = "1", CONCAT("'.$this->data['media_url_thumb'].'",pm.thumbnail), pm.thumbnail) as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,if(AVG(preview.`rating`) IS NULL,"0.0",AVG(preview.`rating`)) AS rating,pin.price,,d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end, sp.seller_id'.$querySelect;
		if($top_rated =="top_rated"){
			$where = array('preview.rating >'=>3,'product.is_private' =>'0');
			$product = $this->Product_Model->frontProductDetails('','','','','','8','rating DESC',$where,'product.product_id',$select,'',"",false,$user_id);	
		}else if($top_rated == "featured"){
			$product = $this->User_Model->getHomeProduct("product.is_featured","",$select,$user_id);
		}else if($top_rated == "new"){
			$where = array('product.is_private' =>'0','cat.is_homepage'=>"1");
			$product = $this->Product_Model->frontProductDetails('','','','','','8','sp.approved_date DESC',$where,'product.product_id',$select,'',"",false,$user_id);
		}else if($top_rated="cat"){
			$category_id = $this->Utilz_Model->getShowCategory();
			$category = array();
			if(!empty($category_id)){
				$i=0;
				foreach($category_id as $cat_id){
					$c_id = $this->getAllCategoriesChildId($cat_id->category_id);
					if($c_id ==""){
						$c_id = $cat_id->category_id;
					}
					$where = 'cat.category_id IN('.$c_id.')';
					$category[$i] = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
					unset($category[$i]['rows']);
					$category[$i]['cat_name'] = $cat_id->category_name;
					$i++;
				}
			}
			$product['result'] = $category;
		}else{
			$product['result'] = array("status"=>0,"error"=>"Wrong parameter");
		}
		echo json_encode($product);
	}
	public function getProductDetails(){
		$this->load->model('Reviewmodel');
		$data = array();
		$productData = array();
		$prVariant = array();
		$total_seller="";
		$product_variant_id = "";
		$condition_id = "";
		$id = "";
		if($this->input->get('product_id')){
			$id = $this->input->get('product_id');
		}
		if($this->input->get('pv_id')){
			$product_variant_id = $this->input->get('pv_id');
		}
		if($this->input->get('condition_id')){
			$condition_id = $this->input->get('condition_id');
		}
		if($this->input->get('slug')){
			$id = $this->Product_Model->getIdBySlug(DBPREFIX."_product","product_id",array("is_active"=>"1","is_declined"=>"0","slug"=>$this->input->get('slug')));
			$id = $id->product_id;
		}
		$user_id = $this->input->get('user_id');
		if($id !=""){
			if(!$id && is_numeric($id)){
				$data['status'] = "0";
				$data['code'] = "-1";
				$data['message'] = "Product Not Found";
			}else{
				$p_variant_id = $this->Product_Model->get_pv_id($id,$condition_id);
				$seller_ids = $this->Product_Model->get_seller_ids($id);
				$total_seller_ids = array();
				if($seller_ids){
					foreach($seller_ids as $si){
						$total_seller_ids[] = $si->seller_id;
					}
				}
				if($product_variant_id==""){
					$product_variant_id = $p_variant_id['pv_id'];
				}
				$total_seller = $p_variant_id['total_seller'];
				//echo $total_seller;die();
				$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
				$product = $this->Product_Model->productDetails($id,$this->region,$product_variant_id,$user_id,"sp.sp_id,ss.store_name,ss.store_id,GROUP_CONCAT(DISTINCT sp.`sp_id`) AS seller_product_id,(COUNT(DISTINCT sp.`seller_id`) - 1) AS total_seller,pin.`price`,pin.`condition_id`, pin.`seller_id`,`product`.`product_id`,`c`.`category_id`,`c`.`category_name`,`b`.`brand_id`,`b`.`brand_name`,`product`.`upc_code`,`product`.`product_name`,`product`.`product_description`,product.short_description,`product`.`is_active`,pin.product_variant_id AS pv_id,pin.quantity,sp.seller_sku,sp.shipping_ids,pin.shipping_ids as inventory_shipping, d.value as discount_value, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end, d.type as discount_type, product.slug",$condition_id);
				//echo "<pre>";print_r($product);die();
				if($product['productDataRows'] > 0){
					// Product Variants
					//$product['productData']->total_seller = $total_seller;
					$product_id = $product['productData']->product_id;
					$seller_id = $product['productData']->seller_id;
					$seller_product_id = $product['productData']->sp_id;
					if($condition_id == ""){
						$condition_id = $product['productData']->condition_id;
					}
					//Related Products
					$querySelect = "";
					if($user_id !=""){
						$querySelect = ", IF(w.wish_id, 1,0) AS already_saved";
					}
					$select = 'product.product_name,product.product_id, IF(pm.is_local = "1", CONCAT("'.$this->data['media_url_thumb'].'",pm.thumbnail), pm.thumbnail) as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,if(AVG(preview.`rating`) IS NULL,"0.0",AVG(preview.`rating`)) AS rating,pin.price,d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end, sp.seller_id'.$querySelect;
					$where = array('product.brand_id '=>$product['productData']->brand_id,'product.sub_category_id'=>$product['productData']->category_id,'product.product_id !='=>$product['productData']->product_id);
					$relatedProduct = $this->Product_Model->frontProductDetails('','','','','','8','sp.created_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
					//------------------------------//
					//Accessories For This Product
					$accessories_id = $this->Product_Model->getData(DBPREFIX.'_product_accessories pa',"accessory_id",array('pa.product_id'=>$product_id));
					if($accessories_id){
						$accessoryArray = array();
						foreach($accessories_id as $ac){
							$accessoryArray[] = $ac->accessory_id;
						}
						$accessories = $this->Product_Model->frontProductDetails($accessoryArray,'','','','','','sp.created_date DESC',"",'product.product_id',$select,'',$this->region,false,$user_id);	
					}else{
						$accessories = array("rows"=>0,"result"=>"");
					}
					//-----------------------------//
					if($product_variant_id ==""){
						$product_variant_id = $product['productData']->pv_id;
					}
					$productVariants = $this->Product_Model->getData(DBPREFIX.'_seller_product_variant spv',"v.v_id,spv.pv_id",array('spv.pv_id'=>$product_variant_id),"0","v.v_cat_id","",array("tbl_variant v"=>"v.`v_id` = spv.v_id "));
					// Product Image
					if($condition_id == "1"){
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link, CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product['productData']->product_id,'condition_id'=>$condition_id,"is_image"=>"1"),"","","is_cover DESC");
					}else{
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link,CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product['productData']->product_id,'sp_id'=>$product['productData']->sp_id,"is_image"=>"1"),"","","is_cover DESC");
						if(empty($productImage)){
							$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link,CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product['productData']->product_id,'condition_id'=>1,"is_image"=>"1"),"","","is_cover DESC");
						}
					}
					if($product['productData']->shipping_ids != ""){
						$shipping_ids = ($product['productData']->inventory_shipping)?$product['productData']->inventory_shipping:$product['productData']->shipping_ids;
						$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,title,price,duration, shipping_type, base_weight, weight_unit, base_length, base_width, base_depth, dimension_unit, incremental_price, incremental_unit, free_after, description",$shipping_ids,"shipping_id",'','price',"","",1);
					}else{
						$shippingData = array(); 
					}
					
					$productShippingInfo = $this->Product_Model->getProductShipping($product_id, 'shipping_info_id, dimension_type, height, width, length, weight, weight_type, shipping_note');
					if(is_array($productShippingInfo) && count($productShippingInfo)>0){
						$shippingInfo = $productShippingInfo[0];
					} else {
						$shippingInfo = array();
					}
					//Product Features
					$productFeatures = $this->Product_Model->getData(DBPREFIX.'_product_features',"GROUP_CONCAT(feature SEPARATOR ',zab') as feature",array('product_id'=>$product_id/*,'seller_id'=>$seller_id*/));
					if($productFeatures[0]->feature == ""){
						$feature = array();
					}else{
						$feature = explode(", zab",$productFeatures[0]->feature);
					}
					$sp_id = explode(',',$product['productData']->sp_id);
					// All different seller's variants of this product. 
					$productAllVariants = $this->Product_Model->getData(DBPREFIX.'_seller_product_variant spv', "v.v_id, v.v_cat_id, vc.v_cat_title, v.v_title, spv.*", "SELECT sp_id FROM tbl_seller_product WHERE product_id=".$id, 'spv.sp_id', 'spv.v_id','',array('tbl_variant v'=>'v.v_id = spv.v_id','tbl_variant_category vc '=>'vc.v_cat_id = v.v_cat_id','tbl_product_inventory AS pin'=>'pin.product_variant_id = spv.pv_id AND pin.quantity > 0 AND pin.approve = "1" AND pin.seller_id != "1"'),'',1);
					// All different seller's product conditions. 
					$productConditions = $this->Product_Model->getData(DBPREFIX.'_product_inventory p',"pc.condition_id,pc.`condition_name`, p.seller_id",'p.product_id='.$product['productData']->product_id.' AND p.quantity > 0 AND p.seller_id !=1 AND p.approve="1"','','p.condition_id','p.condition_id',array('tbl_product_conditions pc'=>'pc.condition_id = p.condition_id'));
					$proAllVariant= array ();
					if(!empty($productAllVariants)){
						foreach($productAllVariants as $pv){
							$proAllVariant[] = array('variant_title'=>$pv->v_title,
													'variant_id'=>$pv->v_id,
													'variant_category_id'=>$pv->v_cat_id,
													'variant_category_title' =>$pv->v_cat_title,
													'seller_product_id'=>$pv->sp_id,
													'seller_product_variant_id'=>$pv->spv_id,
													'pv_id'=>$pv->pv_id);
						}
					}
					if(!empty($productVariants)){
						foreach($productVariants as $pv){
							$prVariant[] = $pv->v_id;
						}
					}
					//$product['productData']->product_description = strip_tags($product['productData']->product_description);
					$product['productData']->product_description = base64_encode($product['productData']->product_description);
					$reviewSelect ='r.review_id,r.date,u.email,u.user_pic,r.product_name,r.pv_id,r.product_id,r.review,r.rating,r.buyer_id,r.seller_id,r.order_id,r.sp_id,u.firstname as name, GROUP_CONCAT(CONCAT("'.media_url("uploads/review/").'",rm.picture)) as review_img,r.is_fake,r.name AS review_name';
					$reviews = $this->Reviewmodel->getdata($seller_product_id,$product_variant_id,$id,4,$reviewSelect);
					$reviewData = array();
					foreach($reviews['result'] as $index=>$r){
						//echo "<pre>";print_r($r);//die();
						$reviewData[$index] = array();
						$reviewData[$index]['review_id'] = $r['review_id'];
						$reviewData[$index]['date'] 	 = strtotime($r['date']);
						$reviewData[$index]['email'] 	 = $r['email'];
						$reviewData[$index]['user_pic']  = profile_path($r['user_pic']);
						$reviewData[$index]['product_name'] = $r['product_name'];
						$reviewData[$index]['review'] 	 = $r['review'];
						$reviewData[$index]['rating'] 	 = $r['rating'];
						$reviewData[$index]['name'] 	 = ($r['is_fake'] == "1")?$r['review_name']:$r['name'];
						$reviewData[$index]['review_img'] 	 = ($r['review_img'])?explode(',',$r['review_img']):array();
					}
					//echo "<pre>";print_r($reviewData);die();
					$allProductVariant = $this->Product_Model->allProductVariantByConditionId($product_id,$product['productData']->condition_id);
					$data['product'] = $product['productData'];
					if(!empty($allProductVariant) && is_array($allProductVariant)){
						foreach($allProductVariant as $apv){
							$data['productAllVariant'][] = $apv->v_id;
						}
					}else{
						$data['productAllVariant'] = array();
					}
					$data['productVariant'] = $prVariant;
					$data['productImage'] = $productImage;
					$data['Variants'] = $proAllVariant;
					$data['productConditions'] = $productConditions;
					$data['relatedProduct'] = $relatedProduct['result'];
					$data['productFeatures'] = $feature;
					if($accessories['rows'] > 0 ){
						$data['productAccessories'] = $accessories['result'];
					}else{
						$data['productAccessories'] = array();
					}
					if($total_seller){
						$product['productData']->total_seller = $total_seller;
					}
					$data['reviews'] = $reviewData;
					$avgRating = $this->Reviewmodel->avgRating($id,$seller_product_id,$product_variant_id);
					$data['productSummary'] = array();
					$data['productSummary']['avg_rating'] = ($avgRating['result']->avg_rating)?substr($avgRating['result']->avg_rating,0,4):"0.0";
					$data['productSummary']['total_review'] = $avgRating['result']->total_review;
					$data['shippingData'] = $shippingData;
					$data['shippingInfo'] = $shippingInfo;
					$data['seller_ids'] = $total_seller_ids;
				}else{
					$data['status'] = "0";
					$data['code'] = "-1";
					$data['message'] = "Product Not Found";
				}
			}
		}
		echo json_encode($data);
	}

	public function getProductData(){ 
		extract($_GET);
		$gpvd = array();
		$variant="";
		$selected_variant = "";
		if($this->input->get('variant_id')){
			$variant = $variant_id;
		}
		if($this->input->get('selected_variant')){
			$selected_variant = $this->input->get('selected_variant');
		}
		if(!$this->input->get('product_id')){
			echo json_encode(array("status"=>0,"msg"=>"Product id is missing."));exit;
		}
		if(!$this->input->get('condition_id')){
			echo json_encode(array("status"=>0,"msg"=>"Condition id is missing."));exit;
		}
		$user_id = "";
		if(!$this->input->get('user_id')){
			$user_id = $this->input->get('user_id');
		}
		if($product_id !="" && $condition_id !=""){
			if(is_array($variant)){
				sort($variant);
				$variant = implode(',',$variant);
			}
			$select = "sp.sp_id,ss.store_name,GROUP_CONCAT(DISTINCT sp.`sp_id`) AS seller_product_id,(COUNT(DISTINCT sp.`seller_id`) - 1) AS total_seller,pin.`price`,pin.`condition_id`, pin.`seller_id`,`p`.`product_id`,`c`.`category_id`,`c`.`category_name`,`b`.`brand_id`,`b`.`brand_name`,`p`.`upc_code`,`p`.`product_name`,`p`.`product_description`,`p`.`is_active`,(pin.quantity) as quantity,sp.seller_sku,sp.shipping_ids, pv.pv_id,pv.variant_group,d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end";
			$productData = $this->Product_Model->getProductVariantData($product_id,$condition_id,$variant,"","",1,"","",$select);
			if($productData['gpvdRows'] ==0){
				$productData = $this->Product_Model->getProductVariantData($product_id,$condition_id,$variant,1,"",1,"",$selected_variant,$select);
				if($productData['gpvdRows'] ==0){
					$productData = $this->Product_Model->getProductVariantData($product_id,$condition_id,$variant,1,1,1,"",$selected_variant,$select);
				}	
				$variant = $variant = $productData['gpvd'][0]->variant_group;	
			}
			if($productData['gpvdRows'] ==0){
				echo json_encode(array("status"=>0,"msg"=>"Invalid product, condition or variant id."));exit;
			}
			$product_variant_id = $productData['gpvd'][0]->pv_id;
			$seller_product_id = $productData['gpvd'][0]->sp_id;
			$seller_id = (isset($productData['gpvd'][0]->seller_id))?$productData['gpvd'][0]->seller_id:"";
			if($user_id){
				$already_saved=$this->Product_Model->already_saved($user_id, $product_id,$product_variant_id);
			}else{
				$already_saved = 0;
			}
			// reviews of this Product
			$this->load->model('Reviewmodel');
			$reviews = $this->Reviewmodel->getdata($seller_product_id,$product_variant_id,$product_id,4);
			$reviewData = array();
			foreach($reviews['result'] as $index=>$r){
				$reviewData[$index] = array();
				$reviewData[$index]['review_id'] = $r['review_id'];
				$reviewData[$index]['date'] 	 = strtotime($r['date']);
				$reviewData[$index]['email'] 	 = $r['email'];
				$reviewData[$index]['user_pic']  = profile_path($r['user_pic']);
				$reviewData[$index]['product_name'] = $r['product_name'];
				$reviewData[$index]['review'] 	 = $r['review'];
				$reviewData[$index]['rating'] 	 = $r['rating'];
				$reviewData[$index]['name'] 	 = $r['name'];
				$reviewData[$index]['review_img'] 	 = ($r['review_img'])?explode(',',$r['review_img']):array();
			}
			//All Product Variant
			$checkVariantGroup = explode(",",$productData['gpvd'][0]->variant_group);
			if(count($checkVariantGroup) == "1"){
				$variant = "";
			}
			$allProductVariant = $this->Product_Model->allProductVariantByConditionId($product_id,$productData['gpvd'][0]->condition_id,$variant);
			// Product Image
			if($productData['gpvd'][0]->condition_id == "1"){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link, CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>$productData['gpvd'][0]->condition_id,"is_image"=>"1"),"","","is_cover DESC");
			}else{
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link, CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'sp_id'=>$productData['gpvd'][0]->sp_id,"is_image"=>"1"),"","","is_cover DESC");
				if(empty($productImage)){
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link, CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>1,"is_image"=>"1"),"","","is_cover DESC");
				}
			}
			if($productData['gpvd'][0]->shipping_ids != ""){
				$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","*",$productData['gpvd'][0]->shipping_ids,"shipping_id",'','price',"","",1);
			}else{
				$shippingData = array();
			}
			$productShippingInfo = $this->Product_Model->getProductShipping($product_id, 'dimension_type, height, width, length, weight, weight_type, shipping_note');
			if(is_array($productShippingInfo) && count($productShippingInfo)>0){
				$shippingInfo = $productShippingInfo[0];
			} else {
				$shippingInfo = array();
			}
			$gpvd['product'] = $productData['gpvd'][0];
			if(!empty($allProductVariant) && is_array($allProductVariant)){
				foreach($allProductVariant as $apv){
					$gpvd['productAllVariant'][] = $apv->v_id;
				}
			}
			$gpvd['productVariant'] = ($productData['gpvd'][0]->variant_group)?explode(",",$productData['gpvd'][0]->variant_group):array();
			//$productData['gpvd'][0]->product_description = strip_tags($productData['gpvd'][0]->product_description);
			$productData['gpvd'][0]->product_description = base64_encode($productData['gpvd'][0]->product_description);
			unset($productData['gpvd'][0]->variant_group);
			unset($productData['gpvd'][0]->image_link);
			unset($productData['gpvd'][0]->image_thumb);
			unset($productData['gpvd'][0]->is_local);
			$gpvd['productImage'] = $productImage;
			$gpvd['shippingData'] = $shippingData;
			$gpvd['shippingInfo'] = $shippingInfo;
			$avgRating = $this->Reviewmodel->avgRating($product_id,$seller_product_id,$product_variant_id);
			$gpvd['productSummary'] = array();
			$gpvd['productSummary']['avg_rating'] = ($avgRating['result']->avg_rating)?substr($avgRating['result']->avg_rating,0,4):"0.0";
			$gpvd['productSummary']['total_review'] = $avgRating['result']->total_review;
			$gpvd['reviews'] = $reviewData;
		}
		echo json_encode($gpvd);
	}
	public function addtocart($id = ""){
		$response = array('status'=>0, 'message'=>'Invalid Product');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('pvid', 'Product ID', 'trim');
		$post_data = $this->input->post();
		if(isset($_GET['qty']) && $_GET['qty'] !=0 && $_GET['qty'] !=""){
			$quantity = $_GET['qty'];
		}else{
			$quantity = 1;
		}
		if(isset($_GET['pv_id']) && is_numeric($_GET['pv_id'])){
			$pv_id = $_GET['pv_id'];
			$product_id = $pv_id;
			$select = "product.product_id, product.upc_code, product.product_name,w.wish_id as already_saved, pv.pv_id, pv.variant_group, ss.store_name, sp.sp_id,sp.shipping_ids, sp.seller_sku, sp.sp_id AS seller_product_id, pm.thumbnail AS product_image, pin.price,pin.warehouse_id, pin.condition_id, pin.seller_id,c.condition_name AS condition,pm.is_local, (pin.quantity) as quantity,pin.sell_quantity, d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as valid_from, UNIX_TIMESTAMP(d.valid_to) as valid_to, product.slug, GROUP_CONCAT(DISTINCT pc.category_id) as category_id,pin.hubx_id,ss.is_tax,pin.shipping_ids as inventory_shipping";
			$product = $this->Product_Model->getProductByProductVariantID($product_id,$select);
			//echo "<pre>";print_r($product);die();
			if($product['status'] == 1){
				$product = $product['data'];
				if($quantity > $product->quantity){
					$response = array('status'=>0, 'message'=>'Product quantity is greater than max quantity!');
					echo json_encode($response);
					exit;
				}
				if($product->shipping_ids != ""){
					$shippings_id = ($product->inventory_shipping !="")?$product->inventory_shipping:$product->shipping_ids;
				}else{
					$response = array('status'=>0, 'message'=>'Product Shipping Info Not Found!');
					echo json_encode($response);
					exit;
				}
				$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,title,price,duration, shipping_type, base_weight, weight_unit, base_length, base_width, base_depth, dimension_unit, incremental_price, incremental_unit, free_after, description",$shippings_id,"shipping_id",'','price',"","",1);
				$variants = (!empty($product->variant_group))?explode(',', $product->variant_group):"";
				$allShippingIds = $product->shipping_ids;
				if(isset($_GET['shipping_id'])){
					$shipping_id = $_GET['shipping_id'];
					$invalid_id = true;
					foreach($shippingData as $sd){
						if($shipping_id == $sd->shipping_id){
							$shipping_title = $sd->title;
							$shipping_price = $sd->price;
							$invalid_id = false;
							break;
						}
					}
					if($invalid_id){
						$response = array('status'=>0, 'message'=>'Invalid Shipping Id!');
						echo json_encode($response);
						exit;
					}
				}else{
					$shipping_id = $shippingData[0]->shipping_id;
					$shipping_title =  $shippingData[0]->title;
					$shipping_price = $shippingData[0]->price;
				}
				if($product->condition_id == "1"){
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product->product_id,'condition_id'=>$product->condition_id),"","","is_cover DESC");
					if(!empty($productImage)){
					$product_img = $productImage[0]->is_primary_image;
					}
					else{
						$product_img =	$product->product_image;
					}
				}else{
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image",array('product_id'=>$product->product_id,'sp_id'=>$product->seller_product_id),"","","is_cover DESC");
					if(!empty($productImage)){
							$product_img = $productImage[0]->is_primary_image;
						}
					if(empty($productImage)){
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product->product_id,'condition_id'=>1),"","","is_cover DESC");
						$product_img = $productImage[0]->is_primary_image;
					}
				}
				//$today = date("Y-m-d");
				//$today_time = strtotime($today);
				$original = $product->price; 
				/*if(strtotime($product->valid_to) >= $today_time){
					$product->price = discount_forumula($product->price, $product->discount_value, $product->discount_type, $product->valid_from, $product->valid_to);
				}*/
				$options = (!empty($variants))?$this->Product_Model->cartVariantFormat($this->Product_Model->getVariants($variants)):null;
				$items = array
				(
				'rowid'					=>	$this->cart->rowid($product->pv_id,$options),
				'product_variant_id'	=> $product->pv_id,
				'product_id'			=> $product->product_id,
				'condition_id'	=> $product->condition_id,
				'seller_product_id'			=> $product->sp_id,	
				'quantity'			=> (int)$quantity,
				'price'			=> (string)$product->price,
				'product_title'			=> urlClean($product->product_name),
				'product_condition'		=> $product->condition,
				'product_image'           => ($product_img)?$product_img:"",
				'already_saved' => ($product->already_saved)?(int)$product->already_saved:0,
				'variant'		=> (!empty($variants))?$this->Product_Model->getVariants($variants,'v_id,v.v_title, vc.v_cat_title'):"",
				'upc_code'		=> $product->upc_code,
				'seller_sku'		=> $product->seller_sku,
				'seller_id'		=> $product->seller_id,
				'is_local'		=> $product->is_local,
				'max_quantity'		=> $product->quantity,
				'sell_quantity'  => $product->sell_quantity,
				'shipping_id'	=> $shipping_id,
				'shipping_title'	=> $shipping_title,
				'shipping_price'	=> $shipping_price,
				'available_shippings' => $allShippingIds,
				'shippingData' => $shippingData,
				'warehouse_id'  		 => $product->warehouse_id,
				'original'			 => (string)$original,
				'options' 			 => $options,
				'variant_ids'		 => (!empty($variants))?$variants:array(),
				'valid_to'			 => $product->valid_to,
				'valid_from'		 => $product->valid_from,
				'discount_type'		 => $product->discount_type,
				'discount_value'	 => $product->discount_value,
				'update_msg'			 => "",
				'referral'			 => "",
				'slug'				 => $product->slug,
				'category_id'		 => $product->category_id,
				'sp_description'		 => (isset($product->seller_product_description))?$product->seller_product_description:"",
				'store_name'			 => $product->store_name,
				'hubx_id'		 	 => $product->hubx_id,
				'collect_tax'		 => $product->is_tax,
				'is_tax'			 => "0",
				'tax'				 => "0"
				);
				$response = array('cart'=> $items);
			}
		}else{
			$response = array('status'=>0, 'message'=>'Invalid Product Variant Id');
		}
		echo json_encode($response);
	}
/*
	public function addtocart($id = ""){
		$this->load->model('Cart_Model');
		$response = array('status'=>0, 'message'=>'Invalid Product');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('pvid', 'Product ID', 'trim');
		$user_id = $this->input->post("userid");
		$post_data = $this->input->post();
		if($this->input->post('qty')){
			$quantity = $this->input->post('qty');
		}else{
			$quantity = 1;
		}
		if($this->input->post('pv_id') && is_numeric($this->input->post('pv_id'))){
			$pv_id = $this->input->post('pv_id');
			$product_id = $pv_id;
			$product = $this->Product_Model->getProductByProductVariantID($product_id);
			if($product['status'] == 1){
				$product = $product['data'];
				if($quantity > $product->quantity){
					$response = array('status'=>0, 'message'=>'Product quantity is greater than max quantity!');
					echo json_encode($response);
					exit;
				}
				$variants = explode(',', $product->variant_group);
				//$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,title,price,incremental_price,free_after,description",$product->shipping_ids,"shipping_id",'','price',"","",1);
				if($this->data->post('shipping')){
					$shippingData = $this->data->post('shipping');
					$shipping_id = $shippingData['shipping_id'];
					$shipping_title = $shippingData['title'];
					$shipping_price =$shippingData['price'];
				}else{
					$shippingData = $this->Utilz_Model->getLowestShippingDataByPvid($product_id);
					$shipping_id = $shippingData->shipping_id;
					$shipping_title = $shippingData->title;
					$shipping_price = $shippingData->price;
				}
				if($product->shipping_ids == ""){
					$response = array('status'=>0, 'message'=>'Product Shipping Info Not Found!');
					echo json_encode($response);
					exit;
				}
				//$allShippingIds = $product->shipping_ids;
				/*if(isset($_GET['shipping_id'])){
					$shipping_id = $_GET['shipping_id'];
					$invalid_id = true;
					foreach($shippingData as $sd){
						if($shipping_id == $sd->shipping_id){
							$shipping_title = $sd->title;
							$shipping_price = $sd->price;
							$invalid_id = false;
							break;
						}
					}
					if($invalid_id){
						$response = array('status'=>0, 'message'=>'Invalid Shipping Id!');
						echo json_encode($response);
						exit;
					}
				}else{
					$shipping_id = $shippingData[0]->shipping_id;
					$shipping_title =  $shippingData[0]->title;
					$shipping_price = $shippingData[0]->price;
				}*//*
				if($product->condition_id == "1"){
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product->product_id,'condition_id'=>$product->condition_id),"","","is_cover DESC");
					if(!empty($productImage)){
					$product_img = $productImage[0]->is_primary_image;
					}
					else{
						$product_img =	$product->product_image;
					}
				}else{
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image",array('product_id'=>$product->product_id,'sp_id'=>$product->seller_product_id),"","","is_cover DESC");
					if(!empty($productImage)){
							$product_img = $productImage[0]->is_primary_image;
						}
					if(empty($productImage)){
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product->product_id,'condition_id'=>1),"","","is_cover DESC");
						$product_img = $productImage[0]->is_primary_image;
					}
				}
				$today = date("Y-m-d");
				$today_time = strtotime($today);
				$original = $product->price; 
				if(strtotime($product->valid_to) >= $today_time){
					$product->price = discount_forumula($product->price, $product->discount_value, $product->discount_type, $product->valid_from, $product->valid_to);
				}
				$items = array
				(
				'product_variant_id'	=> $product->pv_id,
				'product_id'			=> $product->product_id,
				'condition_id'	=> $product->condition_id,
				'seller_product_id'			=> $product->sp_id,	
				'quantity'			=> (int)$quantity,
				'price'			=> (string)$product->price,
				'product_title'			=> urlClean($product->product_name),
				'product_condition'		=> $product->condition,
				'product_image'           => ($product_img)?$this->data['media_url_thumb'] .$product_img:"",
				'already_saved' => ($product->already_saved)?(int)$product->already_saved:0,
				'variants'		=> (!empty($variants))?$this->Product_Model->getVariants($variants,'v_id,v.v_title, vc.v_cat_title'):array(),
				'upc_code'		=> $product->upc_code,
				'seller_sku'		=> $product->seller_sku,
				'seller_id'		=> $product->seller_id,
				'is_local'		=> $product->is_local,
				'max_quantity'		=> $product->quantity,
				'sell_quantity'  => $product->sell_quantity,
				'shipping_id'	=> $shipping_id,
				'shipping_title'	=> $shipping_title,
				'shipping_price'	=> $shipping_price,
				//'available_shippings' => $allShippingIds,
				//'shippingData' => $shippingData,
				'warehouse_id'  		 => $product->warehouse_id,
				'original'			 => (string)$original,
				'already_saved' 	 => ($product->already_saved)?(int)$product->already_saved:0,
				'options' 			 => (!empty($variants))?$this->Product_Model->cartVariantFormat($this->Product_Model->getVariants($variants)):null,
				'variant_ids'		 => (!empty($variants))?$variants:array(),
				'valid_to'			 => $product->valid_to,
				);
				if(isset($items['options']) && count($items['options']) > 0){
					$rowid = md5($items['id'].serialize($items['options']));
				}else{
					$rowid = md5($items['id']);
				}
				$recentCart = $this->input->post('cart_contents');
				if(isset($recentCart[$rowid])){
					$qty = (int)$recentCart[$rowid]['quantity']+(int)$items['quantity'];
					if($qty <= $items['max_quantity']){
						$subtotal = (int)$qty*(int)$product->price;
					}else{
						$qty = $items['max_quantity'];
						$subtotal = (int)$items['max_quantity']*(int)$product->price;
					}
					$items['subtotal'] = $subtotal;
					$recentCart[$rowid]['rowid'] = $rowid;
					$recentCart[$rowid]['quantity'] = $qty;
					$recentCart[$rowid]['max_quantity'] = $items['max_quantity'];
					$recentCart[$rowid]['subtotal'] = $subtotal;
					
				}else{
					array_push($recentCart,$items);
				}
				if($user_id){
					$oldCart = $this->Cart_Model->getCartContents($user_id);
					if($oldCart){
						$this->Cart_Model->updateCart($user_id,$recentCart);
					}else{
						$this->Cart_Model->addtoCart($recentCart,$user_id);
					}
				}
				$this->cartAjaxupdate($rowid,$qty,$shipping_id);
				$response = $recentCart;//array('cart'=> $items);
			}
		}else{
			$response = array('status'=>0, 'message'=>'Invalid Product Variant Id');
		}
		echo json_encode($response);
	}*/
	public function cartAjaxupdate($row_id="",$qty="",$shipping_id="",$cart="",$is_ajax=0){
		$data = array();
		$this->load->model('Cart_Model');
		if($this->input->post('rowid')){
			$data["rowid"] = $this->input->post('rowid');
		}else{
			$data["rowid"] = $row_id;
		}
		if($this->input->post('qty')){
			$data["qty"] = $this->input->post('qty');
		}else{
			$data["qty"] = $qty;
		}
		if($this->input->post('shipping_id')){
			$data["shipping_id"] = $this->input->post('shipping_id');
		}else{
			$data["shipping_id"] = $shipping_id;
		}
		if($this->input->post('cart')){
			$cart = $this->input->post('cart');
		}
		if($this->input->post('is_ajax')){
			$is_ajax = $this->input->post('is_ajax');
		}
		$item = $cart[$data["rowid"]];
		$row_ids = array();
		$subtotal = 0;
		$grand_total = 0;
		$shipping_item_price = 0;
		$shipping_total = 0;
		
		if($data["shipping_id"]){
			$shipping_item = $this->Cart_Model->calculateItemShipping($data["shipping_id"], $data["qty"], $item);
			if($shipping_item['status'] == 1){
				$shipping_item_price = $shipping_item['shipping']['shipping_amount'];
				$item['qty']  = $cart[$data["rowid"]]['qty'] = $data["qty"];
				$item['shipping_id']  = $cart[$data["rowid"]]['shipping_id'] = $shipping_item['shipping']['shipping_id'];
				$item['shipping_title']  = $data["shipping_title"] = $cart[$data["rowid"]]['shipping_title'] = $shipping_item['shipping']['title'];
				$item['shipping_price']  = $data["shipping_price"] = $cart[$data["rowid"]]['shipping_price'] = $shipping_item_price;
			}
		}
		$cart[$data["rowid"]]['subtotal'] = ($cart[$data["rowid"]]["price"]*$data["qty"]);
		$shipping_total = $this->Cart_Model->calculateShipping($cart);
		//If Subtotal equals to free shipping threshold then update all shipping to Free.
		if($shipping_item_price == 0){
			$price = "Free Shipping";
		}else{
			$price = "US $".$data['shipping_price'];
		}
		$grand_total =($grand_total+$shipping_total);
		//--------Update Cart---------//
		if($user_id){
			$this->Cart_Model->updateCart($user_id,$cart);
		}
		//-------Return Data--------//
		if($is_ajax){
			$return = array("grand_total"=>$grand_total,"shipping_total"=>$shipping_total,"cart"=>$cart);
			echo json_encode($return);exit();
		}
	}
	public function getCategory(){
		$categoryData = $this->Product_Model->forntCategoryData("display_status = '1' AND is_active='1' AND is_private = '0'");
		$category = array(
			'categories' => array(),
			'parent_cats' => array()
		);
		//build the array lists with data from the category table
		foreach($categoryData['result'] as $row){
			//creates entry into categories array with current category id ie. $categories['categories'][1]
			$category['categories'][$row->category_id] = $row;
			//creates entry into parent_cats array. parent_cats array contains a list of all categories with children
			$category['parent_cats'][$row->parent_category_id][] = $row->category_id;
		}
		$cat = $this->buildCategory(0,$category);
		echo json_encode($cat);
	}

	function buildCategory($parent, $category , $id="",$data=array()) {
		if (isset($category['parent_cats'][$parent])) {
			foreach ($category['parent_cats'][$parent] as $cat_id) {
				if (!isset($category['parent_cats'][$cat_id])) {
					$data[] = array("categoryId"=>$category['categories'][$cat_id]->category_id,"categoryName"=>$category['categories'][$cat_id]->category_name);
				} 
				if (isset($category['parent_cats'][$cat_id])) {
					$data[] = array('categoryName'=>$category['categories'][$cat_id]->category_name,'categoryId'=>$category['categories'][$cat_id]->category_id,'subCategories'=>$this->buildCategory($cat_id, $category, "cat".$category['categories'][$cat_id]->category_id));
				}
			}
		} 
		return $data;
	}

	public function searchResults(){
		$data = array();
		$product_name = (isset($_GET["search"]))? $_GET["search"]:"";
		$where="";
		$order="";
		$link = "";
		$prod_search = "";
		$keywords = "";
		$range = "";
		$cat_id = "";
		$brand_id = "";
		$category_id = "";
		$param = "";
		$seller_id="";
		$userid="";
		$plus =1;
		if($this->input->get("seller_id")){
			$seller_id = $this->input->get("seller_id");
		}
		if($this->input->get('user_id')){
			$userid = $this->input->get('user_id');
		}
		if(isset($_GET['keywords']) && $_GET['keywords'] !=""){
			$keywords = $_GET['keywords'];
			$searchParameters['keywords'] = $_GET['keywords'];
		}
		if(isset($_GET['price_range']) && $_GET['price_range']!="" ){
			$range= explode("-",$_GET['price_range']);
		}
		if(isset($_GET['product_name_order_by']) && $_GET['product_name_order_by'] !=""){
			$order="product_name ".$_GET['product_name_order_by'];
		}
		if(isset($_GET['price_order_by']) && $_GET['price_order_by'] !=""){
			$order="price ".$_GET['price_order_by'];
		}
		if(isset($_GET["search"]) && $_GET["search"]!="" ){
			$prod_search = stripslashes(trim($_GET["search"]));
			if($prod_search){
				$prod_search = urlClean(str_replace('-',' ',$prod_search));	
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
			$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
			$this->User_Model->keywordSearch($prod_search,$_SERVER['REMOTE_ADDR'],$referer_url,$userid,'keywords');
		}
		if(isset($_GET["category_search"]) && $_GET["category_search"]!=""){
			$category_id = trim($_GET["category_search"]);
			$cat_id = $this->getAllCategoriesChildId($category_id);
		}
		//echo $cat_id;//die();
		if(isset($_GET["brand_search"] ) && $_GET["brand_search"]!="" ){
			$brand_search = trim($_GET["brand_search"]);
			if($_GET["brand_search"] == "All")
			$_GET["brand_search"] = "";
			$brand_id = $brand_search;
		}
		$count=$this->Product_Model->getProductListCount($brand_id,$category_id,$prod_search,$range,"",$seller_id);
		//print_r($count);echo $count->cat_id;
		if($cat_id == ""){
			if($count->cat_id !=""){
				$cat_id = $count->cat_id;
			}else{
				$cat_id = $category_id;
			}
		}
		//echo "<hr>".$cat_id;die();
		if($this->input->get('page')){
			$page = $this->input->get('page');
		}else{
			$page = "1";
		}
		if($this->input->get("per_page")){
			$per_page = $this->input->get("per_page");
		}else{
			$per_page = "12";
		}
		$media_url = $this->config->item('product_path');
		$media_url_thumb = $this->config->item('product_thumb_path');
		$searchParameters = array('userid'=>$userid,'per_page'=>$per_page,'page'=>$page,'search'=>$prod_search,'category_search'=>$category_id,'brand_search'=>$brand_id,'price_range'=>$this->input->get("price_range"),'product_name_order_by'=>$this->input->get('product_name_order_by'),"price_order_by"=>$this->input->get('price_order_by'),"seller_id"=>$seller_id);
		$select = 'product.product_name,product.product_id,product.slug,CONCAT("'.$media_url_thumb.'",pm.thumbnail) as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id AS pv_id,if(AVG(preview.`rating`) IS NULL,"0.0",AVG(preview.`rating`)) AS rating,pin.price,d.value AS discount_value,d.type AS discount_type,UNIX_TIMESTAMP(d.valid_from) AS discount_start,UNIX_TIMESTAMP(d.valid_to) AS discount_end,sp.seller_id';
		$productData= $this->Product_Model->getProductList($brand_id,$cat_id,$prod_search,$page,$per_page,$order,$range,"","",$select,$userid,"",$seller_id);
		if($cat_id && $count->p_id){
			$brand_and_categories = $this->Product_Model->getBrandAndCategory($count->p_id,$cat_id);
		}else{
			$brand_and_categories['brands'] = array();
			$brand_and_categories['categories'] = array();
		}
		$data['searchParameters'] = $searchParameters;
		$data['brand'] = $brand_and_categories['brands'];
		$data['category'] = $brand_and_categories['categories'];
		$data['RelatedProduct'] = $productData;
		$data['maximum_price']=$this->Product_Model->get_max_price();
		if($seller_id){
			$this->load->model('Reviewmodel');
			$store_logo_path = $this->config->item('store_logo_path');
			$store_cover = $this->config->item('store_cover_path');
			$seller_info = $this->Product_Model->getData(DBPREFIX.'_seller_store ss', 'ss.s_id AS store_id,ss.seller_id,ss.store_name, ss.store_address, CONCAT("'.$store_logo_path.'",ss.store_logo) as store_logo, CONCAT("'.$store_cover.'",ss.cover_image) as cover_image', array('ss.seller_id'=>$seller_id));
			$data['seller_info'] = $seller_info;
			$reviews = $this->Reviewmodel->getReviewsbySeller($seller_id,"",3);
			$data['reviews'] = $reviews['result']; 
		}
		$wishlistcat = $this->Secure_Model->WishlistViaCategories($userid);
		if($wishlistcat){
			$data['wishlist_categories'] = $this->Secure_Model->WishlistViaCategories($userid);
		}else{
			$data['wishlist_categories'][0] = array('category_name'=>"","id"=>"");
		}
		$data['itemsFound'] = $count->total;
		echo json_encode($data);
	}

	public function getAllCategoriesChildId($category_id){
		$category_ids = $this->Utilz_Model->getAllCategoriesChildId($category_id);
		return $category_ids;
	}

	public function getAllCategoriesParentId($category_id){
		$category_ids = $this->Utilz_Model->getAllCategoriesParentId($category_id);
		return $category_ids;
	}

	public function login(){
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		$response = array("status"=>0,"msg"=>"");
		if($email == ""){
			$response['msg'] = "Email is required.";
		}
		if($password == ""){
			$response['msg'] = "password is required.";
		}
		if($email && $password){
			$firstSalt = explode("@",$email);
			$lastSalt = "zab.ee";
			$hash = $firstSalt[0].$password.$lastSalt;
			$isLogin = $this->Utilz_Model->apiLogin('',$email,sha1($hash),false,$this->data['profile_path']);
			if($isLogin){
				$response = $isLogin;
				$response->status = 1;
				$response->msg = "login success";
			}
		}else{
			$response['msg'] = "Something went wrong.";
		}
		echo json_encode($response);
		exit;
	}

	public function socialMediaLogin(){
		$email = $this->input->post("email");
		$social_id = $this->input->post("social_id");
		$platform = $this->input->post("platform");
		$first_name = $this->input->post("first_name");
		$last_name = $this->input->post("last_name");
		$user_id = uniqid();
		$error = array("status"=>0,"msg"=>"","email"=>$email,"firstname"=>"","lastname"=>"","user_id"=>"","user_pic"=>"");
		if($social_id == ""){
			$error['msg'] = "Social Id is required.";
			echo json_encode($error);
			exit;
		}
		if($platform == ""){
			$error['msg'] = "Platform is required.";
			echo json_encode($error);
			exit;
		}
		if($platform != "apple"){
			if($email == ""){
				$error['msg'] = "Email is required.";
				echo json_encode($error);
				exit;
			}
			if($first_name == ""){
				$error['msg'] = "First Name is required.";
				echo json_encode($error);
				exit;
			}
			if($last_name == ""){
				$error['msg'] = "Last Name is required.";
				echo json_encode($error);
				exit;
			}
		}
		if($social_id && $platform){
			$isExist = $this->User_Model->getUserByID($social_id,true,$email);
			if(!empty($isExist) ){
				if($isExist->is_active == "1" && $isExist->email_verified == "1"){
					if($isExist->social_id == ''){
						$data_user = array('social_id'=>$social_id, 'social_platform'=>$platform);
						$table = 'tbl_users';
						$where = array('userid'=>$isExist->userid,"email"=>$email);
						$this->User_Model->updateData($data_user, $table, $where);
					}
				} 
				$return = $this->Utilz_Model->apiLogin($isExist->userid,"","",false,$this->data['profile_path']);
				$return->status = 1;
				$return->msg = "login success";
				echo json_encode($return);
				exit;
			}else {
				if($email !=""){
					$array = array('userid'=>$user_id,"user_type"=>2, 'firstname'=>$first_name, 'lastname'=>$last_name, 'email'=>$email,'social_id'=>$social_id,'social_platform'=>$platform,'is_active'=>'1');
					$result = $this->User_Model->addNewUser($array, 1);
					if($result){
						$return['userid'] = $user_id;
						$return['status'] = 1;
						$return['password'] = 0;
						$return['msg'] = "login success";
						echo json_encode($return);
						exit;	
					}
				}else{
					echo json_encode(array("status"=>0,"msg"=>"Email does not exist."));
					exit();
				}
			}
		}else{
			$error['msg'] = "Something went wrong.";
			echo json_encode($error);
			exit;
		}

	}

	public function cartExists(){
		$user_id = $this->input->get("user_id");
		$error = array();
		if($user_id){
			$this->load->model('Cart_Model');
			$oldCart = $this->Cart_Model->getCartContents($user_id);
			//echo "<pre>"; print_r($oldCart);
			$cartData = array("cart"=>array(),"save_for_later"=>array());
			if($oldCart && is_array($oldCart)){
				foreach($oldCart as $oc){
					if(is_array($oc)){
						settype($oc['price'],"string");
						$today = date("Y-m-d");
						$today_time = strtotime($today);
						$original = $oc['price']; 
						$options  = (!empty($oc['options']))?$oc['options']:null;
						$items = array
							(
							'rowid'					=>	$this->cart->rowid($oc['id'],$options),
							'product_variant_id'	=> $oc['id'],
							'product_id'			=> $oc['prd_id'],
							'condition_id'	=> $oc['condition_id'],
							'seller_product_id'			=> $oc['sp_id'],	
							'quantity'			=> (int)$oc['qty'],
							'price'			=>  (string)$oc['price'],
							'product_title'			=> urlClean($oc['name']),
							'product_condition'		=> $oc['condition'],
							'product_image'           => ($oc['img'])?$oc['img']:"",
							'already_saved' => ($oc['already_saved'])?(int)$oc['already_saved']:0,
							'variant'		=> (!empty($oc['variant']))?$oc['variant']:"",
							'upc_code'		=> $oc['upc_code'],
							'seller_sku'		=> $oc['seller_sku'],
							'seller_id'		=> $oc['seller_id'],
							'is_local'		=> $oc['is_local'],
							'max_quantity'		=> (string)$oc['max_qty'],
							'sell_quantity'  => $oc['sell_quantity'],
							'shipping_id'	=> $oc['shipping_id'],
							'shipping_title'	=> $oc['shipping_title'],
							'shipping_price'	=> ($oc['shipping_price'])?(string)$oc['shipping_price']:"0",
							'available_shippings' => (isset($oc['available_shippings']))?$oc['available_shippings']:"",
							'shippingData' 		=> (isset($oc['shippingData']))?$oc['shippingData']:array(),
							'warehouse_id'  	=> (string)$oc['warehouse_id'],
							'original'			=> (isset($oc['original']))?(string)$oc['original']:(string)$oc['price'],
							'options' 			=> $options,
							'variant_ids'		=> (!empty($oc['variant_ids']))?$oc['variant_ids']:array(),
							'valid_to'			=> (isset($oc['valid_to']))?(string)strtotime($oc['valid_to']):null,
							'valid_from'		=> (isset($oc['valid_from']))?(string)strtotime($oc['valid_from']):null,
							'discount_type'		=> (isset($oc['discount_type']))?$oc['discount_type']:null,
							'discount_value'	=> (isset($oc['discount_value']))?$oc['discount_value']:null,
							'update_msg'			 => "",
							'referral'			=> "",
							'slug'				=> $oc['slug'],
							'category_id'		=> $oc['category_id'],
							'sp_description'	=> (isset($oc['seller_product_description']))?$oc['seller_product_description']:"",
							'store_name'		=> $oc['store_name'],
							"collect_tax"		=> 	(string)$oc['collect_tax'],
							"tax"				=> 	(string)$oc['tax'],
							"is_tax"			=>	(string)$oc['is_tax'],
							"hubx_id"			=>	(string)$oc['hubx_id']
							);
							$cartData['cart'][] = $items;
					}
				}
			}
			$oldSave = $this->Cart_Model->getSavedForLaterContents($user_id);
			//print_r($oldSave);die();
			if($oldSave && is_array($oldSave)){
				foreach($oldSave as $oc){
					if(is_array($oc)){
						settype($oc['price'],"string");
						$today = date("Y-m-d");
						$today_time = strtotime($today);
						$original = $oc['price']; 
						$options  = (!empty($oc['options']))?$oc['options']:null;
						$items = array
							(
							'rowid'					=>	$this->cart->rowid($oc['id'],$options),
							'product_variant_id'	=> $oc['id'],
							'product_id'			=> $oc['prd_id'],
							'condition_id'	=> $oc['condition_id'],
							'seller_product_id'			=> $oc['sp_id'],	
							'quantity'			=> (int)$oc['qty'],
							'price'			=>  (string)$oc['price'],
							'product_title'			=> urlClean($oc['name']),
							'product_condition'		=> $oc['condition'],
							'product_image'           => ($oc['img'])?$oc['img']:"",
							'already_saved' => ($oc['already_saved'])?(int)$oc['already_saved']:0,
							'variant'		=> (!empty($oc['variant']))?$oc['variant']:"",
							'upc_code'		=> $oc['upc_code'],
							'seller_sku'		=> $oc['seller_sku'],
							'seller_id'		=> $oc['seller_id'],
							'is_local'		=> $oc['is_local'],
							'max_quantity'		=> (string)$oc['max_qty'],
							'sell_quantity'  => $oc['sell_quantity'],
							'shipping_id'	=> $oc['shipping_id'],
							'shipping_title'	=> $oc['shipping_title'],
							'shipping_price'	=> ($oc['shipping_price'])?(string)$oc['shipping_price']:"0",
							'available_shippings' => (isset($oc['available_shippings']))?$oc['available_shippings']:"",
							'shippingData' => (isset($oc['shippingData']))?$oc['shippingData']:array(),
							'warehouse_id'  		 => (string)$oc['warehouse_id'],
							'original'			 => (isset($oc['original']))?(string)$oc['original']:(string)$oc['price'],
							'options' 			 => $options,
							'variant_ids'		 => (!empty($oc['variant_ids']))?$oc['variant_ids']:array(),
							'valid_to'			 => (isset($oc['valid_to']))?(string)strtotime($oc['valid_to']):null,
							'valid_from'		 => (isset($oc['valid_from']))?(string)strtotime($oc['valid_from']):null,
							'discount_type'		 => (isset($oc['discount_type']))?$oc['discount_type']:null,
							'discount_value'	 => (isset($oc['discount_value']))?$oc['discount_value']:null,
							'update_msg'			 => "",
							'referral'			 => "",
							'slug'				 => $oc['slug'],
							'category_id'		 => $oc['category_id'],
							'sp_description'		 => (isset($oc['seller_product_description']))?$oc['seller_product_description']:"",
							'store_name'			 => $oc['store_name']
							);
							$cartData['save_for_later'][] = $items;
					}
				}
			}
			echo json_encode($cartData);
			exit();
		}else{
			echo json_encode($error);
			exit();
		}
	}
	public function saveForLaterExists(){
		$user_id = $this->input->get("user_id");
		$error = array();
		if($user_id){
			$this->load->model('Cart_Model');
			$oldCart = $this->Cart_Model->getSavedForLaterContents($user_id);
			//echo "<pre>"; print_r($oldCart);
			if($oldCart && is_array($oldCart)){
				$cartData = array();
				foreach($oldCart as $oc){
					if(is_array($oc)){
						settype($oc['price'],"string");
						$today = date("Y-m-d");
						$today_time = strtotime($today);
						$original = $oc['price']; 
						$items = array
							(
							'product_variant_id'	=> $oc['id'],
							'product_id'			=> $oc['prd_id'],
							'condition_id'	=> $oc['condition_id'],
							'seller_product_id'			=> $oc['sp_id'],	
							'quantity'			=> (int)$oc['qty'],
							'price'			=>  (string)$oc['price'],
							'product_title'			=> urlClean($oc['name']),
							'product_condition'		=> $oc['condition'],
							'product_image'           => ($oc['img'])?$this->config->item('product_thumb_path').$oc['img']:"",
							'already_saved' => ($oc['already_saved'])?(int)$oc['already_saved']:0,
							'variants'		=> (!empty($oc['variant']))?$oc['variant']:array(),
							'upc_code'		=> $oc['upc_code'],
							'seller_sku'		=> $oc['seller_sku'],
							'seller_id'		=> $oc['seller_id'],
							'is_local'		=> $oc['is_local'],
							'max_quantity'		=> (string)$oc['max_qty'],
							'sell_quantity'  => $oc['sell_quantity'],
							'shipping_id'	=> $oc['shipping_id'],
							'shipping_title'	=> $oc['shipping_title'],
							'shipping_price'	=> ($oc['shipping_price'])?$oc['shipping_price']:"0",
							'available_shippings' => (isset($oc['available_shippings']))?$oc['available_shippings']:"",
							'shippingData' => (isset($oc['shippingData']))?$oc['shippingData']:array(),
							'warehouse_id'  		 => (string)$oc['warehouse_id'],
							'original'			 => (isset($oc['original']))?(string)$oc['original']:(string)$oc['price'],
							'options' 			 => (!empty($oc['options']))?$oc['options']:null,
							'variant_ids'		 => (!empty($oc['variant_ids']))?$oc['variant_ids']:array(),
							'valid_to'			 => (isset($oc['valid_to']))?$oc['valid_to']:"",
							);
							$cartData[] = $items;
					}
				}
				echo json_encode($cartData);
				exit();
			}else{
				echo json_encode($error);
				exit();
			}
		}else{
			echo json_encode($error);
			exit();
		}
	}

	public function userAddresses($type="get"){
		$this->load->model('Addressbook_Model');
		$error = array("status"=>"0");
		if($type == "get"){
			if($this->input->get('user_id')){
				$user_id = $this->input->get('user_id');
			}else{
				$error['msg'] = "userid is required.";
				echo json_encode($error);
				exit;
			}
			$usertype = "buyer";
			$return = $this->Addressbook_Model->getUserLocationsByUserIdforlocation($user_id, $usertype,"","","id as address_id,fullname,contact as phone_number,address_1 as address,address_2,country,state,city,zip,use_address");
			if($return){
				echo json_encode($return);
			}else{
				echo json_encode(array());
			}
			exit;
		}else if($type == "add" || $type == "update"){
			$data = $this->input->post();
			if(!isset($data['user_id']) && $data['user_id'] !=""){
				$error['msg'] = "UserId is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['fullname']) && $data['fullname'] !=""){
				$error['msg'] = "FullName is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['contact']) && $data['contact'] !=""){
				$error['msg'] = "contact is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['address1']) && $data['address1'] !=""){
				$error['msg'] = "Address is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['country']) && $data['country'] !=""){
				$error['msg'] = "Country is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['state']) && $data['state'] !=""){
				$error['msg'] = "State is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['city']) && $data['city'] !=""){
				$error['msg'] = "City is required.";
				echo json_encode($error);
				exit;
			}
			if(!isset($data['zip']) && $data['zip'] !=""){
				$error['msg'] = "Zip is required.";
				echo json_encode($error);
				exit;
			}
			$insert_data = array(
				'user_id' => $data['user_id'],
				'user_type' => "buyer", 
				'fullname' => $data['fullname'],
				'contact' => $data['contact'],
				'address_1' => $data['address1'],
				'address_2' => isset($data['address2'])?$data['address2']:"",
				'country' => $data['country'],
				'state' => $data['state'],
				'city' => $data['city'],
				'zip' => $data['zip'],
				'active' => "1",
			);
			if($type == "add"){
				$insert_data['created'] = date('Y-m-d H:i:s');
				$resp = $this->Addressbook_Model->add_addressbook($insert_data, $data['user_id']);
				$location_id = (string)$resp;
				if($location_id){
					echo json_encode(array("status"=>"1","msg"=>"Address added successfully.","id"=>$location_id));
				}else{
					echo json_encode(array("status"=>"0","msg"=>"Something went wrong.","id"=>$location_id));
				}
			}else{
				if(!$data['address_id']){
					$error['msg'] = "AddressId is required.";
					echo json_encode($error);
					exit;
				}
				$insert_data['updated'] = date('Y-m-d H:i:s');
				$location_id = $data['address_id'];
				$resp = $this->Addressbook_Model->update_addressbook($insert_data, $data['user_id'], $location_id);
				if($resp){
					echo json_encode(array("status"=>"1","msg"=>"Address added successfully.","id"=>$location_id));
				}else{
					echo json_encode(array("status"=>"0","msg"=>"Something went wrong.","id"=>$location_id));
				}
			}
		}else if($type == "delete"){
			if(!$this->input->post('address_id')){
				$error['msg'] = "AddressId is required.";
				echo json_encode($error);
				exit;
			}
			if(!$this->input->post('user_id')){
				$error['msg'] = "UserId is required.";
				echo json_encode($error);
				exit;
			}
			$user_id = $this->input->post('user_id');
			$location_id = $this->input->post('address_id');
			$deletelocations = $this->Addressbook_Model->deleteaddress($location_id,$user_id);
			if($deletelocations){
				echo json_encode(array("status"=>"1","msg"=>"Address deleted successfully."));
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Something went wrong."));
			}
		}else if($type == "default"){
			if(!$this->input->post('address_id')){
				$error['msg'] = "AddressId is required.";
				echo json_encode($error);
				exit;
			}
			if(!$this->input->post('user_id')){
				$error['msg'] = "UserId is required.";
				echo json_encode($error);
				exit;
			}
			$location_id = $this->input->post('address_id');
			$user_id = $this->input->post('user_id');
			$selectedlocations = $this->Addressbook_Model->getUserLocationsByUserIdforlocation($user_id,"buyer", $location_id);
			if($selectedlocations){
				$insert_data = array(
					'updated' 		=> date('Y-m-d H:i:s'),
					'user_id' 		=> $user_id,
					'use_address' 	=> "1"
				);
				$insert_data2 = array(
					'updated' 		=> date('Y-m-d H:i:s'),
					'user_id' 		=> $user_id,
					'use_address' 	=> "0"
				);
				$resp = $this->Addressbook_Model->update_addressonly($insert_data ,$insert_data2, $user_id, $location_id);
				if($resp){
					echo json_encode(array("status"=>"1","msg"=>"Address make default successfully."));
				}else{
					echo json_encode(array("status"=>"0","msg"=>"Something went wrong."));
				}
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Address not found."));
			}
		}
	}
	public function get($what="country"){
		if($what == "country"){
			$result = $this->Utilz_Model->countries("","","id,iso,name,nicename,iso3,numcode,CONCAT('+',phonecode) as phonecode");
		}else if($what == "state"){
			$result = $this->Utilz_Model->countries("","","",false,"tbl_states");
		}else{
			$result = array();
		}
		echo json_encode($result);
	}
	public function cards($method="get"){
		if($method == ""){
			echo json_encode(array("status"=>"0","msg"=>"Parameter is missing."));
			exit();
		}
		if($method == "get"){
			if(!$this->input->get('user_id')){
				echo json_encode(array("status"=>"0","msg"=>"User id is missing."));
				exit();
			}
			if(!$this->input->get('card_id')){
				$card_id = 0;
			}else{
				$card_id = $_GET['card_id'];
			}
			$user_id = $this->input->get('user_id');
			$limit = $this->input->get('limit');
			$offset = $this->input->get('offset');
			$save_card = ($this->input->get('unsaved'))?"1":"0";
			$getCards = $this->User_Model->apiStripeCards($user_id,$card_id,$limit,$offset,$save_card);
			echo json_encode($getCards);
		}else if($method == "add"){
			if(!$this->input->post('card_number') && $this->input->post('card_number') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card number is missing."));
				exit();
			}else if(!$this->input->post('exp_date') && $this->input->post('exp_date') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Expiry month is missing."));
				exit();
			}else if(!$this->input->post('exp_year') && $this->input->post('exp_year') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Expiry year is missing."));
				exit();
			}else if(!$this->input->post('card_name') && $this->input->post('card_name') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card holder name is missing."));
				exit();
			}else if(!$this->input->post('custom_card_name') && $this->input->post('custom_card_name') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card name is missing."));
				exit();
			}else if(!$this->input->post('email') && $this->input->post('email') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Email is missing."));
				exit();
			}else if(!$this->input->post('user_id') && $this->input->post('user_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"User id is missing."));
				exit();
			}else if(!$this->input->post('address_id') && $this->input->post('address_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Address id is missing."));
				exit();
			}else if(!$this->input->post('ccv') && $this->input->post('ccv') !=""){
				echo json_encode(array("status"=>"0","msg"=>"CCV is missing."));
				exit();
			}
			$user_id = $this->input->post('user_id');
			$card = array(
				'name' 		=> $this->input->post('card_name'),
				'number' 	=> str_replace('-', '', $this->input->post('card_number')),
				'exp_month' => $this->input->post('exp_date'),
				'exp_year' 	=> $this->input->post('exp_year'),
				'cvc' 		=> $this->input->post('ccv')
			);
			$save_card = ($this->input->post('save_card'))?"1":"0";
			$this->load->library('Stripe', 'stripe');
			$stripe_key = $this->config->item('stripe_key');
			$getStripeCustomerID = $this->User_Model->getStripeCustomerID($user_id);
			if($getStripeCustomerID == ''){
				$customer = $this->stripe->createCustomer($_POST['email'],$stripe_key);
				if($customer['status'] == 1){
					$customer_id = $customer['customer_id'];
					$this->User_Model->addStripeCustomerID($customer['customer_id'], $user_id);
				}
			} else {
				$customer_id = $getStripeCustomerID;
			}
			$card_name = $_POST['custom_card_name'];
			$card = $this->stripe->saveCard($card,$card_name,$customer_id,$stripe_key);
			if($card['status'] == 1){
				$card_id = $card['card_id'];
				$token = $card['card_id'];
				$getToken = false;
				$sc = $this->User_Model->addStripeCustomerCard($customer_id, $card['card'], $card_name, $user_id,$this->input->post('address_id'),$save_card);
				if($sc){
					echo json_encode(array("status"=>"1","msg"=>"Card Add successfully.","card_id"=>$sc));
				}
			}else{
				echo json_encode(array("status"=>"2","msg"=>$card['message']));
			}

		}else if($method == "update"){
			$data = $this->input->post();
		 	if(!$this->input->post('exp_date') && $this->input->post('exp_date') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Expiry month is missing."));
				exit();
			}else if(!$this->input->post('exp_year') && $this->input->post('exp_year') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Expiry year is missing."));
				exit();
			}else if(!$this->input->post('card_name') && $this->input->post('card_name') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card holder name is missing."));
				exit();
			}else if(!$this->input->post('user_id') && $this->input->post('user_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"User id is missing."));
				exit();
			}else if(!$this->input->post('custom_card_name') && $this->input->post('custom_card_name') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card name is missing."));
				exit();
			}else if(!$this->input->post('card_id') && $this->input->post('card_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card id is missing."));
				exit();
			}
			$getCard = $this->User_Model->apiStripeCards($data['user_id'], $data['card_id'],"","","",",c.card_id,c.customer_id", true);
			if(isset($getCard[0]) && $getCard[0]->id){
				$this->load->library('Stripe', 'stripe');
				$stripe_key = $this->config->item('stripe_key');
				$card_id = $getCard[0]->card_id;
				$customer_id = $getCard[0]->customer_id;
				$stripeData = array('exp_month'=> $data['exp_date'], 'exp_year'=>$data['exp_year'],'name'=>$data['card_name']);
				$updateData = array('expiry_month'=> $data['exp_date'], 'expiry_year'=>$data['exp_year'],'holder_name'=>$data['card_name'],'card_name'=>$data['custom_card_name']);
				$cards = $this->stripe->updateCard($stripeData, $card_id, $customer_id, $stripe_key);
				if($cards['status'] == 1){
					$update = $this->User_Model->updateData($updateData,DBPREFIX.'_user_cards',array("id"=>$data['card_id'],"user_id"=>$data['user_id']));
					if($update){
						echo json_encode(array("status"=>"1","msg"=>"Card update successfully!"));
						exit();
					}else{
						echo json_encode(array("status"=>"0","msg"=>"Unable to update card!"));
						exit();
					}
				}else{
					echo json_encode(array("status"=>"0","msg"=>"Unable to update card!"));
					exit();
				}
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Card not found!"));
				exit();
			}
		}else if($method =="delete"){
			if(!$this->input->post('user_id') && $this->input->post('user_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"User id is missing."));
				exit();
			}
			if(!$this->input->post('card_id') && $this->input->post('card_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card id is missing."));
				exit();
			}
			$user_id = $this->input->post('user_id');
			$card_id = $this->input->post('card_id');
			$deletecard = $this->User_Model->deleteCard($user_id, $card_id);
			if($deletecard){
				echo json_encode(array("status"=>"1","msg"=>"Card delete successfully."));
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Unable to delete card."));
			}
		}else if($method == "default"){
			if(!$this->input->post('user_id') && $this->input->post('user_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"User id is missing."));
				exit();
			}
			if(!$this->input->post('card_id') && $this->input->post('card_id') !=""){
				echo json_encode(array("status"=>"0","msg"=>"Card id is missing."));
				exit();
			}
			$user_id = $this->input->post('user_id');
			$card_id = $this->input->post('card_id');
			$insert_data = array(
				'updated' => date('Y-m-d H:i:s'),
				'user_id' => $user_id,
				'default' => "1"
			);
			$insert_data2 = array(
				'updated' => date('Y-m-d H:i:s'),
				'user_id' => $user_id,
				'default' => "0"
			);
			$resp = $this->User_Model->makeDefaultCard($insert_data ,$insert_data2, $user_id, $card_id);
			if($resp){
				echo json_encode(array("status"=>"1","msg"=>"Make card default successfully."));
			}else{
				echo json_encode(array("status"=>"0","msg"=>"Error on making card default."));
			}
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Wrong parameter."));
			exit();
		}
	}
	public function getDefault(){
		if(!$this->input->get('user_id')){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing."));
			exit();
		}
		$user_id = $this->input->get('user_id');
		$default = $this->User_Model->apiGetDefaultAddressAndCard($user_id);
		echo json_encode($default);

	}
	public function proceed_payment(){
		$orderID = time() * rand(1, 9);
		//$cart = '[{"product_variant_id":"3047","product_id": "1691","condition_id": "1","seller_product_id": "2634","quantity": 1,"price": "499","product_title": "iPhone 7 Plus","product_condition": "New","product_image": "https://sd400.zab.ee/uploads/product/thumbs/f9396e3e7cf99a3d56a25380bc0a3a44_thumb.jpg","already_saved": null,"variants": [{"variant_id": "57","variant_title": "Black","variant_category_title": "Color"},{"variant_id": "119","variant_title": "32GB","variant_category_title": "Capcity"}],"upc_code": "583714129","seller_sku": "","seller_id": "5dc40cd4eaf06",	"is_local": "1","max_quantity": "10","sell_quantity": "0","shipping_id": "3","shipping_title": "Free Standard Shipping","shipping_price": "0","available_shippings": "3","shippingData": [{"shipping_id": "3","created_date": "2019-10-23 04:33:07","updated_date": "2019-10-23 06:26:43","user_id": "1","title": "Free Standard Shipping","price": "0","duration": "3-7"}],"warehouse_id": "0","original": "499","options": {"Color": "Black","Capcity": "32GB"},"variant_ids": ["57","119"],"valid_to": null}]';
		$cart = $this->input->post('cart');
		$cart = json_decode($cart,true);
		$hasDiscount = $this->input->post("hasDiscount");
		$discount = $this->input->post("discount");
		//echo "<pre>";print_r($cart);die();
		if(!$cart){
			echo json_encode(array("status"=>"0","msg"=>"Invalid JSON.","code"=>"001"));
			exit();
		}
		if(!$this->input->post("user_id")){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"001"));
			exit();
		}else{
			$user_id = $this->input->post("user_id");
			//$user_id = "5dae955f9a842";
		}
		if(!$this->input->post("card_id")){
			echo json_encode(array("status"=>"0","msg"=>"Card id is missing.","code"=>"001"));
			exit();
		}else{
			$card_id = $this->input->post("card_id");
			//$card_id = "70";
		}
		if(!$this->input->post("shipping_address_id")){
			echo json_encode(array("status"=>"0","msg"=>"Shipping Address id is missing.","code"=>"001"));
			exit();
		}else{
			$shipping_id = $this->input->post("shipping_address_id");
			//$shipping_id = "16";
		}
		if(!$this->input->post("email")){
			echo json_encode(array("status"=>"0","msg"=>"Email is missing.","code"=>"001"));
			exit();
		}else{
			$email = $this->input->post("email");
			//$email = "qa1.kaygees@gmail.com";
		}
		$user = $this->User_Model->checkUserId($user_id, true);
		if(!$user){
			echo json_encode(array("status"=>"0","msg"=>"Invalid user id.","code"=>"001"));
			exit();
		}
		$payment_error = false;
		$card = $this->Utilz_Model->getAllData(DBPREFIX."_user_cards","id,address_id,customer_id,holder_name,card_number,expiry_month,expiry_year,card_name,card_id,save_card", array('user_id'=>$user_id,"id"=>$card_id));
		if($card){
			$card = $card[0];
			$card_number = '****-****-****-'.$card->card_number;
			$shipping = $this->Utilz_Model->getAllData(DBPREFIX."_user_address","address_1,address_2,city,contact as phone,zip as zipcode,state,country,fullname as name", array('id'=>$shipping_id));
			$location['shipping'] = (array)$shipping[0];
			$location['shipping']['state'] = (is_numeric($location['shipping']['state']))?getCountryNameByKeyValue('id', $location['shipping']['state'], 'code', true,'tbl_states'):$location['shipping']['state'];
			$location['shipping']['country'] = getCountryNameByKeyValue('id', $location['shipping']['country'], 'nicename', true);
			$billing = $this->Utilz_Model->getAllData(DBPREFIX."_user_address","address_1,address_2,city,contact as phone,zip as zipcode,state,country,fullname as name", array('id'=>$card->address_id));
			$location['billing'] = (array)$billing[0];
			$location['billing']['state'] = (is_numeric($location['billing']['state']))?getCountryNameByKeyValue('id', $location['billing']['state'], 'code', true,'tbl_states'):$location['billing']['state'];
			$location['billing']['country'] = getCountryNameByKeyValue('id', $location['billing']['country'], 'nicename', true);
			$cardView = array(
				'acct' 			=> $card_number,
				'expdate' 		=>  $card->expiry_month.$card->expiry_year,
				'cvv2' 			=> 'xxx',
				'startdate'		=> '',
				'issuenumber' 	=> '',
				'holder' 		=> $card->holder_name,
				'card_name' 	=> $card->card_name,
				'save_card' 	=> $card->save_card,
				'paywithcard' 	=> false,
				'address' 		=> $billing[0]->address_1.", ".$billing[0]->city.", ".$location['billing']['state'].", ".$location['billing']['country'],
				'customer_id' 	=> $card->customer_id,
				'card_id' 		=> $card->card_id,
			);
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Invalid Card.","code"=>"001"));
			exit();
		}
		$this->load->model('Cart_Model');
		$PayerName = array(
			'salutation' 	=> 'Mr.',
			'firstname' 	=> $user->firstname,
			'middlename' 	=> $user->middlename,
			'lastname' 		=> $user->lastname,
			'suffix' 		=> ''
		); 
		$PayerInfo = array();
		$shipping_total = 0;
		$cart_total = 0;
		$tax = 0;
		foreach ($cart as $key=>$items){
			$cart[$key]['shipping_price'] = $items['net_shipping_price'];
			$shipping_total = $shipping_total+$items['net_shipping_price'];//$items['shipping_price'];
			$cart_total = ($items['price'] * $items['quantity'])+$cart_total;
			$tax = $items['tax']+$tax;
			$cart[$key]['id'] = $items['product_variant_id'];
			$cart[$key]['qty'] = $items['quantity'];
			$cart[$key]['max_qty'] = $items['max_quantity'];
			$cart[$key]['prd_id'] = $items['product_id'];
			$cart[$key]['sp_id'] = $items['seller_product_id'];
			unset($cart[$key]['quantity']);
			unset($cart[$key]['max_quantity']);
			unset($cart[$key]['product_id']);
			unset($cart[$key]['product_variant_id']);
			unset($cart[$key]['seller_product_id']);
		}
		if($hasDiscount){
			$cart_total = $cart_total - $discount['discount_amount'];
		}
		//$tax = $this->Cart_Model->getTax($location['shipping']['zipcode'], $cart_total);
		$grand_total = $cart_total+$shipping_total+$tax;
		$PaymentDetails = array(
			'amt' 			=> number_format((float)$grand_total, 2, '.', ''),
			'currencycode' 	=> 'USD',
			'shippingamt' 	=> $shipping_total,
			'taxamt' 		=> $tax,
			'desc' 			=> 'Web Order',
			'custom' 		=> '', 
			'invnum' 		=> $orderID,
			'notifyurl' 	=> '' 
		); 
		$this->Cart_Model->save_order($orderID, $cart, $PaymentDetails, $location, $cardView, $PayerInfo, $user_id,"app",$discount);
		$this->load->library('Stripe', 'stripe');
		$package = new stdClass();
		$package->package_price = number_format((float)$grand_total, 2, '.', '')*100;
		$package->package_name = 'Purchasing items on Zabee';
		$stripe_key = $this->config->item('stripe_key');
		if($card->card_id == ''){
			$paymentResponse = array('status'=>0, 'message'=>'Error in transaction, please try again later',"code"=>001);
		} else {
			$paymentResponse = $this->stripe->doTransaction($card->card_id, $package, $email,$stripe_key, $card->customer_id);
		}
		if($paymentResponse['status'] == 0){
			$errors = array('Errors'=>$paymentResponse['message']);
			$this->session->set_flashdata('payment_error',$errors);
			$this->data['payment_error'] = true;
			$this->Cart_Model->deleteOrder($orderID);
			echo json_encode(array("status"=>"0","msg"=>$paymentResponse['message'],"code"=>"002"));
			exit();
		} else {
			$data = array('transaction_id'=>$paymentResponse['transaction_id'], 'card_id'=>$card_id);
			$response = $this->Cart_Model->complete_order($orderID, $data, $user_id);
			if($response){ 
				$buyer_email = $email;  
				$seller_data = array();
				$seller_id = array();
				foreach ($this->cart->contents() as $key => $value){
						if(!in_array($value['seller_id'], $seller_id)){
							$seller_data[$value['seller_id']][] = $value;
						}else{
							$seller_data[$value['seller_id']][] = $value;
						}
				}
				$i = 0;
				$warehouse_emails = array();
				foreach($seller_data as $key2 =>$sd){
					foreach($sd as $key => $s){
						if($s['warehouse_id'] != "" && $s['warehouse_id'] > 0){
							$warehouse_email = $this->Utilz_Model->getWarehouseEmails($s['warehouse_id']);
						}
					}
					$seller_email=$this->Utilz_Model->get_seller_email($key2);
					if($seller_email != ""){	
						$this->Utilz_Model->send_mail($location,$sd,$orderID,$seller_email->email, $type="seller");
						$this->Utilz_Model->send_mail($location,$sd,$orderID,$buyer_email, $type="buyer"); 
					}
					if($warehouse_email != ""){
						$this->Utilz_Model->send_mail($location,$sd,$orderID,$warehouse_email, $type="warehouse"); 
					}
				}
				
			} else {
				echo json_encode(array("status"=>"0","msg"=>"Error in transaction, please try again later"));
				exit();
			}
			echo json_encode(array("status"=>"1","msg"=>"Order Complete","order_id"=>$orderID,"code"=>"200"));
			exit();
		}
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
		echo json_encode($response);
	}
	public function getTaxRate(){
		$response = array("status"=>0,"msg"=>"Invalid request","code"=>"000", 'tax'=>0);
		$proceed = true;
		if(!$this->input->get("zipcode")){
			$response["msg"] = "zipcode is missing.";
			$response["code"] = "001";
			$proceed = false;
		}
		if($proceed){
			$zipcode = $this->input->get("zipcode");
			$cart_total = $this->input->get("cart_total");
			$this->load->model('Cart_Model');
			$tax = $this->Cart_Model->getTaxRate($zipcode);
			$response["status"] = 1;
			$response["msg"] = "OK";
			$response["tax"] = $tax;
		}
		echo json_encode($response);
	}
	public function signup(){
		if(!$this->input->post('firstname') && $this->input->post('firstname') !=""){
			echo json_encode(array("status"=>"0","msg"=>"First Name is missing."));
			exit();
		}else if(!$this->input->post('lastname') && $this->input->post('lastname') !=""){
			echo json_encode(array("status"=>"0","msg"=>"Last Name is missing."));
			exit();
		}else if(!$this->input->post('email') && $this->input->post('email') !=""){
			echo json_encode(array("status"=>"0","msg"=>"Email is missing."));
			exit();
		}else if(!$this->input->post('password') && $this->input->post('password') !=""){
			echo json_encode(array("status"=>"0","msg"=>"Password is missing."));
			exit();
		}
		$first_name=$this->input->post('firstname');
		$middle_name=($this->input->post('middlename'))?$this->input->post('middlename'):"";
		$last_name=$this->input->post('lastname');
		$email=$this->input->post('email');
		$password=$this->input->post('password');
		$checkEmail = $this->User_Model->getUserByEmail($email);
		if(!$checkEmail){
			$firstSalt = explode("@",$email);
			$lastSalt = "zab.ee";
			$password = $firstSalt[0].$password.$lastSalt;
			$password = sha1($password);
			$user_id = uniqid();
			$array = array('userid'=>$user_id,"user_type"=>2, 'firstname'=>$first_name, 'middlename'=>$middle_name, 'lastname'=>$last_name, 'email'=>$email, 'password'=>$password,'social_id'=>"",'social_platform'=>'','social_info'=>'','mobile'=>'','is_active'=>'1');
			$this->User_Model->addNewUser($array);
			$this->User_Model->addDefaultWishlistForNewUser($user_id);
			$this->User_Model->record_passwords($array);
			$return = $this->Utilz_Model->apiLogin("",$email,$password,false,$this->data['profile_path']);
			echo json_encode(array("status"=>"1","msg"=>"User registration successfully.",'user_data'=>$return));
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Email already exists."));
		}
	}
	public function password(){
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|callback_checkEmailExist['.$this->input->post('email').']');
		$email_data = array();
		if($this->form_validation->run() == TRUE){
			//$this->load->library('encrypt');
			$this->load->helper('url');
			$email= $_POST['email'];
			$reset_code= uniqid();
			$code=($email.'|'.$reset_code);
			$encrypted_code=base64_encode($code);
			$encryption=str_replace('/', '%',$encrypted_code);
			$this->load->helper('email');
			$data = $_POST;
			if(!valid_email($data["email"])){
				$this->session->set_flashdata("alert",json_encode(array("type"=>"block","msg"=>"The Email addresss you provided is not valid.")));
				redirect($data['redirect']);
			}		
			$user = $this->User_Model->getUserByEmail($data["email"]);
			$user = $user[0];
			$this->load->library('parser');
			$email_data['id'] = $reset_code;
			$email_data['encryption'] = $encryption;
			$email_data['base_path'] = base_url();
			$email_data['firstname'] = $user['firstname'];
			$email_data['lastname'] = $user['lastname'];
			if(isset($user)){
				$from = $this->config->item('info_email');
				$name = 'Zab.ee';
				$to = $data["email"]; 
				$subject = 'Zabee : Your request for password';
				$email_data['page_name'] = "email_template_for_password_reset";
				$message = $this->parser->parse('front/emails/email_template', $email_data, TRUE);
				if($this->Utilz_Model->email_config($from, $name, $to, $subject, $message)){
					$today = date("Y-m-d");
					$this->User_Model->saveData(array("email"=>$email,"reset_code"=>$reset_code,"datetime"=>$today),"tbl_reset_code");
					// $this->email->message($message);
					// $this->email->send();
					$this->session->set_flashdata('alert','Please check your mail');
					$this->session->set_flashdata('encryption',$encryption);
					$data['email'] = $email;
					$data['encryption']=$encryption;
					$data['page_name'] = 'forgotpassword';
					$data['title'] = 'Forgot Password';
					$data['hasStyle'] 	= NULL;
					$data['newsletter'] = false;
					$data['hasScript'] 	= TRUE;
					$this->load->view('front/template', $data);
				}
			}
			else{
				$this->session->set_flashdata('alert',json_encode(array("type"=>"block","msg"=>"Your Email addresss is not registered with us. Please <a href = '".base_url()."/register'>Register</a> here.")));
				redirect($data['redirect']);
			}
		}
		else{
			$this->session->set_flashdata('invalid_email',1);
			redirect('forgotpassword');
		}
	}
	public function getShippingData(){
		$error = array("code"=>0,"status"=>0,"msg"=>"");
		if(isset($_GET['pv_id']) && is_numeric($_GET['pv_id'])){
			$pv_id = $_GET['pv_id'];
		}else if(!isset($_GET['pv_id'])){
			$error['msg'] = "Product variant id missing"; 
			echo json_encode($error);exit();
		}else if(!is_numeric($_GET['pv_id'])){
			$error['msg'] = "Product variant id is not numeric"; 
			echo json_encode($error);exit();
		}
		$shipping = $this->Product_Model->getData(DBPREFIX."_seller_product sp","sp.shipping_ids,p.shipping_ids as inventory_shipping",array("pv.pv_id"=>$pv_id),"",'','',array("tbl_product_variant pv"=>"pv.sp_id = sp.sp_id","tbl_product_inventory p"=>"p.seller_product_id = sp.sp_id AND p.product_variant_id = pv.pv_id"),"1","","","",true);
		if($shipping->shipping_ids != ""){
			$shipping_id = ($shipping->inventory_shipping !="")?$shipping->inventory_shipping:$shipping->shipping_ids;
			$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","*",$shipping_id,"shipping_id",'','price',"","",1);
		}else{
			$shippingData = array(); 
		}
		echo json_encode($shippingData);
	}
	public function changePassword(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password','Password','trim|required');
		$this->form_validation->set_rules('confirm_password','confirm_password','trim|required|matches[password]');
		$this->form_validation->set_rules('current_password','current password','trim|required');
		if(!$this->input->post('email') && $this->input->post('email') !=""){
			echo json_encode(array("status"=>"0","msg"=>"Email is missing."));
			exit();
		}
		if($this->form_validation->run() == TRUE){
			$current_password = $this->input->post('current_password');
			$password=	$this->input->post('password');
			$email= $this->input->post('email');
			$firstSalt = explode("@",$email);
			$lastSalt = "zab.ee";
			$hash = $firstSalt[0].$password.$lastSalt;
			$hash2 = $firstSalt[0].$current_password.$lastSalt;
			$array = array('password'=>sha1($hash));
			$current_password = sha1($hash2);
			$return = $this->User_Model->getPassword($email,$current_password);
			if($return){
				$updated = $this->User_Model->update_password($email,$array);
				if($updated){
					$return = array("code"=>1,"status"=>1,"msg"=>"Password changed.");
					echo json_encode($return);exit();
				}else{
					$return = array("code"=>2,"status"=>0,"msg"=>"Password not changed.");
					echo json_encode($return);exit();
				}
			} else{
				$return = array("code"=>3,"status"=>0,"msg"=>"Wrong Password.");
				echo json_encode($return);exit();
			}
		}
		else{
			$return = array("code"=>4,"status"=>0,"msg"=>"Password and confirm password field must be same.");
			echo json_encode($return);exit();
		}
	}
	public function offerlisting(){
		$error = array("status"=>0,"code"=>0,"msg"=>"");
		$userid = $this->input->get("userid");
		$product_id = $this->input->get("product_id");
		$condition_id = "";
		$shipping = "";
		$this->data['condition_active'] = "";
		$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"CONCAT('".$this->data['media_url']."',iv_link) as image_link, CONCAT('".$this->data['media_url_thumb']."',thumbnail) as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>1),"","","is_cover DESC","",1);
		if($this->input->get("product_id") !=""){
			if($this->input->get('condition_id')){
				$condition_id = $this->input->get('condition_id');
				$this->data['condition_active'] = $condition_id;
			}else if($this->input->get('shipping_id')){
				$shipping = $this->input->get('shipping_id');
			}
			$select ='pv.pv_id,CONCAT(u.firstname, " ", u.lastname) AS seller_name,ss.store_name,pv.product_id,pv.seller_id,pv.sp_id,pv.seller_sku,pv.condition_id,pv.variant_group,pin.product_name,pin.price,pin.quantity,d.value as discount_value,d.type as discount_type,IF(is_local="1",CONCAT("'.$this->data['media_url'].'",pm.iv_link),pm.iv_link) as image_link,IF(is_local="1",CONCAT("'.$this->data['media_url_thumb'].'",pm.thumbnail),pm.thumbnail) AS thumbnail,pm.is_local,sp.shipping_ids,sp.seller_product_description,UNIX_TIMESTAMP(d.valid_from) AS discount_start,UNIX_TIMESTAMP(d.valid_to) AS discount_end';
			$product =$this->Product_Model->getProductVariantData($product_id,$condition_id,"","","","",$shipping,"",$select);
			if($product["gpvdRows"] > 0){
				$shippingData = array();
				foreach($product['gpvd'] as $productshipping){
					if($productshipping->image_link == ""){
						$productshipping->image_link = $productImage[0]->image_link;
						$productshipping->thumbnail = $productImage[0]->is_primary_image;
					}
					$product_name = $productshipping->product_name; 
					if($productshipping->variant_group){
						$variants = explode(',', $productshipping->variant_group);
						$variant = $this->Product_Model->getVariants($variants,'v_id,v.v_title, vc.v_cat_title');
						$productshipping->variant = $variant;
					}
					if($userid){
						$productshipping->already_saved = $this->Product_Model->already_saved($userid, $productshipping->product_id,$productshipping->pv_id);
					}else{
						$productshipping->already_saved = "";
					}
					if($productshipping->shipping_ids != ""){
						$lowestShippingId =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id",$productshipping->shipping_ids,"shipping_id",'','price',"","1",1);
						$productshipping->lowestShipping_id = isset($lowestShippingId[0]->shipping_id)?$lowestShippingId[0]->shipping_id:"";
						$shippingData[] = $productshipping->shipping_ids;
					}
				}
				$shipping_ids = implode(',',$shippingData);
				$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,title,price,incremental_price,free_after",$shipping_ids,"shipping_id",'','price',"","",1);
				$productConditions = $this->Product_Model->getData(DBPREFIX.'_product_inventory p',"pc.condition_id,pc.`condition_name`",'p.product_id='.$product_id.' AND p.quantity > 0 AND p.seller_id !=1 AND p.approve="1"','','p.condition_id','p.condition_id',array('tbl_product_conditions pc'=>'pc.condition_id = p.condition_id'));
				$data['ProductData'] 			= $product['gpvd'];
				$data['productConditions'] 	= $productConditions;
				$data['shippingData'] 		= $shippingData;
				$data['productName'] 			= isset($product_name)?$product_name:"";
				$data['productImage'] 		= $productImage[0];
				echo json_encode($data);
			}else{
				$error['msg'] = "Invalid data provide check your ids";
				echo json_encode($error);
			}
		}else{
			$error['msg'] = "ProductId required";
			echo json_encode($error);
		}
	}
	public function seller_info(){
		$this->load->model('Reviewmodel');
		$store_logo_path = $this->config->item('store_logo_path');
		$store_cover = $this->config->item('store_cover_path');
		$seller_id = $this->input->get("seller_id");
		$ship = $this->input->get("shipping_id");
		$where="";
		$order="";
		$range = $this->input->get('price_range');
		$cat_id = "";
		$brand_id = "";
		$user_id=$this->input->get('userid'); 
		$error = array("status"=>0,"code"=>0,"msg"=>"");
		$searchParameters = array('seller_id'=>$this->input->get('seller_id'),'userid'=>$this->input->get('user_id'),'per_page'=>$this->input->get('per_page'),'page'=>$this->input->get('page'),'search'=>$this->input->get('search'),'category_search'=>$this->input->get('category_search'),'brand_search'=>$this->input->get('brand_search'),'price_range'=>$this->input->get('price_range'),'product_name_order_by'=>$this->input->get('product_name_order_by'),"price_order_by"=>$this->input->get('price_order_by'));
		if($seller_id !=""){
			$seller_info = $this->Product_Model->getData(DBPREFIX.'_seller_store ss', 'ss.s_id AS store_id,ss.seller_id,ss.store_name, ss.store_address, CONCAT("'.$store_logo_path.'",ss.store_logo) as store_logo, CONCAT("'.$store_cover.'",ss.cover_image) as cover_image', array('ss.seller_id'=>$seller_id));
			if($seller_info){
				if($range){
					$range= explode("-",$_GET['price_range']);
					$where = "pin.price BETWEEN '$range[0]' AND '$range[1]'";
					if(!is_numeric($range[0]) || !is_numeric($range[1])){
						$error['msg'] = "Range value should be numeric";
						echo json_encode($error);exit();
					}
				}
				if($this->input->get('product_name_order_by')){
					$order="product_name ".$this->input->get('product_name_order_by');
				}
				if($this->input->get('price_order_by')){
					$order="price ".$this->input->get('price_order_by');
				}
				if($this->input->get('category_search')){
					$cat_id = $this->input->get('category_search');
					$where .= "cat.category_id IN(".$cat_id.") AND ";
				}
				if($this->input->get('brands_search')){
					$brand_id = $this->input->get('brands_search');
					if($where!=""){
						$where.=" AND ";
					}
					$where.=" b.brand_id='$brand_id'";
				}
				if($ship!="" && $ship == "free"){
					$ship = 0;
				}
				$count=$this->Utilz_Model->getSellerProductCount($seller_id,$brand_id,$cat_id,$ship,$range);
				$per_page = ($this->input->get("per_page"))?$this->input->get("per_page"):12;
				$last_page_number 			 = ceil($count/$per_page);
				if($this->input->get('page')){
					$page = $this->input->get('page');
				}else{
					$page = 1;
				}
				if($page > $last_page_number){
					$page = $last_page_number;
				}
				$select ='product.product_name,product.product_id,CONCAT("'.$this->data['media_url_thumb'].'",pm.thumbnail) as thumbnail,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id AS pv_id,AVG(preview.rating) AS rating,pin.price,d.value AS discount_value,d.type AS discount_type,UNIX_TIMESTAMP(d.valid_from) AS discount_start ,UNIX_TIMESTAMP(d.valid_to) AS discount_end,sp.seller_id';
				$productData= $this->Utilz_Model->getSellerProduct($seller_id,$brand_id,$cat_id,$page,$per_page,$order,$range,"","","$select",$user_id);
				$brand_and_categories = $this->Utilz_Model->getBrandAndCategory($seller_id);
				$data['reviews'] = $this->Reviewmodel->getReviewsbySeller($seller_id,"",3);
				$data['seller_info'] = $seller_info;
				$data['brand'] = $brand_and_categories['brands'];
				$data['category'] = $brand_and_categories['categories'];
				$data['RelatedProduct'] = $productData;
				$data['maximum_price']=$this->Product_Model->get_max_price($seller_id);
				if($user_id){
					$wishlistcat = $this->Secure_Model->WishlistViaCategories($user_id);
					if($wishlistcat){
						$data['wishlist_categories'] = $this->Secure_Model->WishlistViaCategories($user_id);
					}else{
						$data['wishlist_categories'][0] = array('category_name'=>"","id"=>"");
					}
				}else{
					$data['wishlist_categories'][0] = array('category_name'=>"","id"=>"");
				}
				$data['itemsFound'] = $count;
				$data['searchParameters'] = $searchParameters;
				echo json_encode($data);exit();
			}
		}else{
			$error['msg'] = "SellerId required";
			echo json_encode($error);exit();
		}
	}
	public function invite_people(){
		if($this->input->post('user_id') && $this->input->post('pv_id')){
			$post_data = $this->input->post();
			$firstSalt = $post_data['user_id'];
			$lastSalt = $post_data['pv_id'];
			$code = $firstSalt.'zabee'.$lastSalt;
			$code = sha1($code);
			$found = $this->Referral_Model->check_code($code);
			if($found == false){
				$date = date("Y-m-d H:i:s");
				$exp_date = date('Y-m-d H:i:s', strtotime($this->config->item('exp_time'), strtotime($date)));
				$post_data['referral_type'] = "product";
				$post_data['referral_code'] = $code;
				$post_data['invite_time'] = $date;
				$post_data['expire_time'] = $exp_date;
				$result = $this->User_Model->saveData($post_data, "tbl_referral");
				if($result != false){
					echo json_encode(array("status"=>1, 'link'=>$code,"code"=>1));
				}else{
					echo json_encode(array("status"=>2, 'link'=>'error in insertion',"code"=>0));
				}
			}else{
				echo json_encode(array('status'=>1,'code'=>$found));
			}
		}else{
			echo json_encode(array('status'=>0,'msg'=>"Parameter missing.","code"=>0));
		}
	}
	public function banner(){
		$this->load->model("admin/Banner_Model");
		$banner = $this->Banner_Model->getBannerForFront("","banner_image,banner_link");
		echo json_encode($banner);
	}
	public function add_to_wishlist(){
		$this->load->model("admin/Secure_Model");
		$user_id = $this->input->get("user_id");
		$product_id = $this->input->get("product_id");
		$pv_id = $this->input->get("pv_id");
		$category_id = $this->input->get("category_id");
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$product_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Id Is Missing.","code"=>2));
			exit;
		}
		if(!$pv_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Variant Id is Missing.","code"=>2));
			exit;
		}
		if(!$category_id){
			echo json_encode(array('status'=>0,'msg'=>"Category Id is Missing.","code"=>2));
			exit;
		}
		//$alreadyExistingCategories = $this->Secure_Model->getForcedWishListName($user_id,$cat_name);
		/*if(!$alreadyExistingCategories){
			//$word = "like";
			$insert_data = array(
				'user_id' => $user_id,
				'category_name' => $word
			);
			$alreadyExistingCategories = $this->Secure_Model->insertWishlistCategoryName($insert_data);
			echo json_encode($alreadyExistingCategories);
		}else{
			$alreadyExistingCategories = $alreadyExistingCategories->id;
		}*/
		//if($alreadyExistingCategories){
			$insert_data2 = array(
				'created_date' =>  date('Y-m-d H:i:s'),
				'category_id' => $category_id,
				'product_id' => $product_id,
				'pv_id' => $pv_id,
				'user_id' => $user_id,
			);
			$resp1 = $this->Secure_Model->insertWishlist($insert_data2);
			if($resp1){
				echo json_encode(array('status'=>1,'msg'=>"Added to Wishlist.","code"=>1));
			}else{
				echo json_encode(array('status'=>0,'msg'=>"Error to add Wishlist.","code"=>0));
			}
		/*}else{
			echo json_encode(array('status'=>2,'msg'=>"Error to add Wishlist Category.","code"=>0));
		}*/
	}
	public function updatePassword(){
		$this->load->model("admin/Secure_Model");
		$user_id = $this->input->post("user_id");
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$email){
			echo json_encode(array('status'=>0,'msg'=>"Email Is Missing.","code"=>2));
			exit;
		}
		if(!$password){
			echo json_encode(array('status'=>0,'msg'=>"Password is Missing.","code"=>2));
			exit;
		}
		$firstSalt = explode("@",$email);
		$lastSalt = "zab.ee";
		$hash = $firstSalt[0].$password.$lastSalt;
		$password = sha1($hash);
		$data = array("password"=>$password);
		$table = DBPREFIX."_users";
		$where = array("userid"=>$user_id,"email"=>$email);
		$return = $this->User_Model->updateData($data,$table,$where);
		if($return){
			echo json_encode(array('status'=>1,'msg'=>"Updated Successfully.","code"=>1));
		}else{
			echo json_encode(array('status'=>0,'msg'=>"Error in updating.","code"=>0));
		}
		exit;
	}
	public function wishlist(){
		$user_id = $this->input->get("user_id");
		$wish_cat_id = $this->input->get("wish_cat_id");
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$wish_cat_id){
			echo json_encode(array('status'=>0,'msg'=>"Wish Category Id  Is Missing.","code"=>2));
			exit;
		}
		if($this->input->get('page')){
			$page = $this->input->get('page');
		}else{
			$page = 1;
		}
		if($this->input->get('per_page')){
			$per_page = $this->input->get('per_page');
		}else{
			$per_page = 10;
		}
		//$alreadyExistingCategories = $this->Secure_Model->getForcedWishListName($user_id);
		//if(!empty($alreadyExistingCategories)){
			if($user_id !=""){
				$querySelect = ", IF(w.wish_id, 1,0) AS already_saved";
			}
			$select = 'p.product_name,p.product_id, CONCAT("'.$this->data['media_url_thumb'].'",pm.thumbnail) as product_image,pm.is_local,sp.sp_id AS seller_product_id,p.short_description,pin.product_variant_id as pv_id,if(AVG(preview.`rating`) IS NULL,"0.0",AVG(preview.`rating`)) AS rating,pin.price,,d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end, sp.seller_id'.$querySelect;
			$data = $this->Product_Model->saved_for_later($user_id,$page,$per_page,$wish_cat_id,$select);
		//}
		if(!empty($data['data'])){
			echo json_encode($data['data']);exit();
		}else{
			echo json_encode(array('status'=>0,'msg'=>"No Data Found.","code"=>0));exit();
		}
	}
	public function remove_to_wishlist(){
		$user_id = $this->input->get("user_id");
		$product_id = $this->input->get("product_id");
		$pv_id = $this->input->get("pv_id");
		//$wish_id = $this->input->get("wish_id");
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$product_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$pv_id){
			echo json_encode(array('status'=>0,'msg'=>"PV Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$pv_id){
			echo json_encode(array('status'=>0,'msg'=>"PV Id  Is Missing.","code"=>2));
			exit;
		}
		/*if(!$wish_id){
			echo json_encode(array('status'=>0,'msg'=>"Wish Id  Is Missing.","code"=>2));
			exit;
		}*/
		$where = array("product_id"=>$product_id,"user_id"=>$user_id,"pv_id"=>$pv_id);
		$return = $this->Product_Model->delete_from_list("",$where);
		if($return){
			echo json_encode(array('status'=>1,'msg'=>"Removed.","code"=>1));
			exit;
		}else{
			echo json_encode(array('status'=>0,'msg'=>"Error in Remove.","code"=>0));
			exit;
		}
	}
	public function cartJson(){
		$json = '[{"product_variant_id":"8578","product_id":"4177","condition_id":"1","seller_product_id":"7726","quantity":1,"price":"37.95","product_title":"EN-EL12 Rechargeable Battery - Nikon","product_condition":"New","product_image":"https://sd400.zab.ee/uploads/product/thumbs/caf603185f89790da58bedb3ab88f1d3_thumb.png","already_saved":70,"variants":[],"upc_code":"479156345","seller_sku":"","seller_id":"5dc40cd4eaf06","is_local":"1","max_quantity":"6","sell_quantity":"4","shipping_id":"3","shipping_title":"Free Standard Shipping","shipping_price":"0","available_shippings":"3","shippingData":[{"shipping_id":"3","title":"Free Standard Shipping","price":"0","duration":"3-7","incremental_price":null,"free_after":null}],"isChecked":true,"warehouse_id":"0","original":"37.95","variant_ids":[""],"valid_to":null},{"product_variant_id":"8579","product_id":"4178","condition_id":"1","seller_product_id":"7727","quantity":1,"price":"58.95","product_title":"HDMI Cable HC-E1 - Nikon","product_condition":"New","product_image":"https://sd400.zab.ee/uploads/product/thumbs/baa8315e7e7e4d4ff9485a9f56efeda6_thumb.png","already_saved":47,"variants":[],"upc_code":"1393903724","seller_sku":"","seller_id":"5dc40cd4eaf06","is_local":"1","max_quantity":"3","sell_quantity":"7","shipping_id":"3","shipping_title":"Free Standard Shipping","shipping_price":"0","available_shippings":"3","shippingData":[{"shipping_id":"3","title":"Free Standard Shipping","price":"0","duration":"3-7","incremental_price":null,"free_after":null}],"isChecked":true,"warehouse_id":"0","original":"58.95","variant_ids":[""],"valid_to":null},{"product_variant_id":"8497","product_id":"1618","condition_id":"5","seller_product_id":"7655","quantity":1,"price":"460","product_title":"LG V50 ThinQ 5G - LMV450PM","product_condition":"Good","product_image":"https://sd400.zab.ee/uploads/product/thumbs/79fa091c83c0d6e83310bbe990d4c197_thumb.jpg","already_saved":0,"variants":[],"upc_code":"862797445","seller_sku":"","seller_id":"5e4f925aa47a7","is_local":"1","max_quantity":"4","sell_quantity":"1","shipping_id":"3","shipping_title":"Free Standard Shipping","shipping_price":"0","available_shippings":"3","shippingData":[{"shipping_id":"3","title":"Free Standard Shipping","price":"0","duration":"3-7","incremental_price":null,"free_after":null}],"isChecked":true,"warehouse_id":"0","original":"460","variant_ids":[""],"valid_to":null},{"product_variant_id":"8487","product_id":"1628","condition_id":"5","seller_product_id":"7649","quantity":1,"price":"149.99","product_title":"LG V20","product_condition":"Good","product_image":"https://sd400.zab.ee/uploads/product/thumbs/5d28c0504c4664c62ebb52fde38fa141_thumb.jpg","already_saved":0,"variants":[{"v_id":"123","v_title":"Titan","v_cat_title":"Color"}],"upc_code":"766188706","seller_sku":"","seller_id":"5e4f925aa47a7","is_local":"1","max_quantity":"5","sell_quantity":"0","shipping_id":"3","shipping_title":"Free Standard Shipping","shipping_price":"0","available_shippings":"3","shippingData":[{"shipping_id":"3","title":"Free Standard Shipping","price":"0","duration":"3-7","incremental_price":null,"free_after":null}],"isChecked":true,"warehouse_id":"0","original":"149.99","options":{"Color":"Titan","Capcity":null},"variant_ids":["123"],"valid_to":null},{"product_variant_id":"8569","product_id":"4165","condition_id":"1","seller_product_id":"7718","quantity":1,"price":"2299.99","product_title":"ALIENWARE AREA-51M R2 GAMING","product_condition":"New","product_image":"https://sd400.zab.ee/uploads/product/thumbs/73789a9c678fe47b7a2d8ed8639afe3c_thumb.jpg","already_saved":69,"variants":[{"v_id":"349","v_title":"Dark Side of the Moon","v_cat_title":"Color"}],"upc_code":"1124723015","seller_sku":"","seller_id":"5e535e6e2a116","is_local":"1","max_quantity":"8","sell_quantity":"2","shipping_id":"3","shipping_title":"Free Standard Shipping","shipping_price":"0","available_shippings":"3","shippingData":[{"shipping_id":"3","title":"Free Standard Shipping","price":"0","duration":"3-7","incremental_price":null,"free_after":null}],"isChecked":true,"warehouse_id":"0","original":"2299.99","options":{"Color":"Dark Side of the Moon","Capcity":null},"variant_ids":["349"],"valid_to":null}]';
		$decode =  json_decode($json);
		echo "<pre>";
		print_r($decode);
	}
	public function get_order_list(){
		$user_id = $this->input->get("user_id");
		$order_id = $this->input->get("order_id"); 
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		$this->load->model("Cart_Model");
		if($this->input->get('page')){
			$page = $this->input->get('page');
		}else{
			$page = "";//1;
		}
		if($this->input->get('limit')){
			$limit = $this->input->get('limit');
		}else{
			$limit = "";//10;
		}
		$table_name = "tbl_transaction";
		$order_by = 'created DESC';
		$select = "order_id";
		$pageing = "";//($page-1)*$limit;
		if($order_id){
			$o_id = array($order_id);  
		}else{ 
			$where = array('user_id'=>$user_id);
			$order_id = $this->Utilz_Model->getAllData($table_name, $select, $where,0,"",$order_by,"",array($limit,$pageing));
			$o_id = array();  
			foreach($order_id as $order){
				$o_id[] = $order->order_id;
			}
		}
		$order_list = $this->Cart_Model->getOrderByClientID($user_id, $o_id);
		if( $order_list != ""){
		 foreach($order_list as $o){
			 $productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
			 if($o->product_image == ""){
				 if(isset($productImage[0]->is_primary_image)){
					 $o->product_image = $this->data['media_url_thumb'].$productImage[0]->is_primary_image; 
				 }else{
					 $o->product_image = "";
				 }
			 }
			$o->created = strtotime($o->created);
			$o->rating = ($o->rating)?$o->rating:"";
			$o->review_id = ($o->review_id)?$o->review_id:"";
			$o->review = ($o->review)?$o->review:"";
			$o->is_local = ($o->is_local)?$o->is_local:"0";
			$o->item_confirm_status = ($o->item_confirm_status)?$o->item_confirm_status:"0";
			$o->tracking_id = ($o->tracking_id)?$o->tracking_id:"";
		 }
		$this->data['orders'] = $this->Cart_Model->formatOrderList($order_list,'data',$page,$limit);
	 }
	 $dat = array_values($this->data['orders']);
	 echo json_encode($dat);
	}
	public function cancel_order(){
		$this->load->model('Cart_Model');
		$order_id = $this->input->get("order_id");
		$td_id = $this->input->get("id");
		if(!$order_id){
			echo json_encode(array('status'=>0,'msg'=>"Order Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$td_id){
			echo json_encode(array('status'=>0,'msg'=>"Id  Is Missing.","code"=>2));
			exit;
		}
		$data = array("order_id"=>$order_id,"td_id"=>$td_id);
		$return = $this->Cart_Model->cancel_order($data);
		if($return){
			echo json_encode(array('status'=>1,'msg'=>"Cancellation Request Pending.","code"=>1));
			exit;
		}else{
			echo json_encode(array('status'=>0,'msg'=>"Error in order cancellation.","code"=>0));
			exit;
		}
	}
	public function  write_a_review(){
		$buyer_id = $this->input->post("buyer_id");
		$seller_id = $this->input->post("seller_id");
		$email = $this->input->post("email");
		$name = $this->input->post("name");
		$product_name = $this->input->post("product_name");
		$review = $this->input->post("review");
		$pv_id = $this->input->post("product_variant_id");
		$rating = $this->input->post("rating");
		$product_id = $this->input->post("product_id");
		$sp_id = $this->input->post("sp_id");
		$order_id = $this->input->post("order_id");
		/*echo "<pre>";
		print_r($this->input->post());
		print_r($_FILES); die();*/
		if(!$buyer_id){
			echo json_encode(array('status'=>0,'msg'=>"Buyer Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$seller_id){
			echo json_encode(array('status'=>0,'msg'=>"Seller  Is Missing.","code"=>2));
			exit;
		}	
		if(!$email){
			echo json_encode(array('status'=>0,'msg'=>"Email  Is Missing.","code"=>2));
			exit;
		}
		if(!$name){
			echo json_encode(array('status'=>0,'msg'=>"Name Missing.","code"=>2));
			exit;
		}	
		if(!$product_name){
			echo json_encode(array('status'=>0,'msg'=>"Product Name Is Missing.","code"=>2));
			exit;
		}
		/*if(!$review){
			echo json_encode(array('status'=>0,'msg'=>"Review Is Missing.","code"=>2));
			exit;
		}*/	
		if(!$pv_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Variant Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$rating){
			echo json_encode(array('status'=>0,'msg'=>"Rating  Is Missing.","code"=>2));
			exit;
		}	
		if(!$product_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$sp_id){
			echo json_encode(array('status'=>0,'msg'=>"Seller Product Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$order_id){
			echo json_encode(array('status'=>0,'msg'=>"Order Id  Is Missing.","code"=>2));
			exit;
		}
		$this->load->model('Cart_Model');
		//$date = date("Y-m-d H:i:s UTC");
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		//echo "<pre>";print_r($_FILES);die();
		$image = $_FILES;
		$review = addslashes($review);
		$insert_data = array(
			'date' => $date_utc,
			'buyer_id' => $buyer_id,
			'seller_id' => $seller_id,
			'order_id' => $order_id,
			'email' => $email,
			'name' => $name,
			'product_name' => $product_name,
			'review' => $review,
			'pv_id' => $pv_id,
			'rating' => $rating,
			'product_id' => $product_id,
			'sp_id' => $sp_id
		);
		$reviewAdded = $this->Cart_Model->forReviews($insert_data,$buyer_id);
		if($reviewAdded){
			if(!empty($image)){
				$error_message = array();
				$config['upload_path'] = "review";
				$config['upload_thumbnail_path'] = "review/thumbs";
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['quality'] = "100%";
				$config['remove_spaces'] = TRUE;
				$config['upload_thumbnail_width']         = 250;
				$config['upload_thumbnail_height']       = 250;
				for ($i=0; $i < count($image); $i++) { 
					$params['file'] = curl_file_create($image['photo'.$i]['tmp_name'], $image['photo'.$i]['type'],$image['photo'.$i]['name']);
					$params['image_type'] = "review";
					$params['filesize'] = $image['photo'.$i]['size'];
					$params['config'] = json_encode($config);
					$upload_server = $this->config->item('media_url').'/file/upload_media';
					$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
					if($file->status == 1){
						$review_data['created_date'] = $date;
						$review_data['review_id'] = $reviewAdded;
						$review_data['picture'] = $file->images->original->filename;
						$review_data['thumbnail'] = $file->images->thumbnail->filename;
						$this->Cart_Model->review_image($review_data);
						$message[] = array("msg"=>"Review add successful");
					}else{
						$message[] = $file->message;
						//echo json_encode(array('status'=>1,'msg'=>$file->message,"code"=>2));exit;
					}
				}
			}else{
				$message[] = array("msg"=>"Review add successful");
			}
			echo json_encode(array('status'=>1,'msg'=>$message,"code"=>1));
			exit;
		}else{
			echo json_encode(array('status'=>0,'msg'=>"Error in review adding.","code"=>0));
			exit;
		}
		
		
	}
	public function contact_seller(){
		$sp_id = $this->input->post("sp_id");
		$message = $this->input->post("message");
		$sender_id = $this->input->post("sender_id");
		$receiver_id = $this->input->post("receiver_id");
		$seller_id = $this->input->post("seller_id");
		$buyer_id = $this->input->post("buyer_id");
		$item_type = $this->input->post("item_type");
		$product_variant_id = $this->input->post("product_variant_id");
		$date = $this->input->post("date");
		if(!$buyer_id){
			echo json_encode(array('status'=>0,'msg'=>"Buyer Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$seller_id){
			echo json_encode(array('status'=>0,'msg'=>"Seller  Is Missing.","code"=>2));
			exit;
		}	
		if(!$sender_id){
			echo json_encode(array('status'=>0,'msg'=>"Sender Id Is Missing.","code"=>2));
			exit;
		}
		if(!$receiver_id){
			echo json_encode(array('status'=>0,'msg'=>"Receiver Id Is Missing.","code"=>2));
			exit;
		}	
		if(!$product_variant_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Variant Id Is Missing.","code"=>2));
			exit;
		}
		if(!$message){
			echo json_encode(array('status'=>0,'msg'=>"Message Is Missing.","code"=>2));
			exit;
		}	
		if(!$sp_id){
			echo json_encode(array('status'=>0,'msg'=>"Seller Product Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$date){
			echo json_encode(array('status'=>0,'msg'=>"Date Is Missing.","code"=>2));
			exit;
		}
		if(!$item_type){
			echo json_encode(array('status'=>0,'msg'=>"Item type Is Missing.","code"=>2));
			exit;
		}
		//$date = date("Y-m-d H:i:s UTC");
		$data = array('sent_datetime'=>$date,'sender_id'=>$sender_id,'receiver_id'=>$receiver_id,'message'=>$message,'subject'=>"Contact Seller",'item_type'=>$item_type, 'item_id'=>$sp_id,'seller_id'=>$seller_id,'buyer_id'=>$buyer_id,'product_variant_id'=>$product_variant_id);
		$result = $this->User_Model->saveData($data,'tbl_message');
		if($result){
			echo json_encode(array('status'=>1,'msg'=>"Message send.","code"=>1));
			exit;
		}else{
			echo json_encode(array('status'=>0,'msg'=>"Error in message sending.","code"=>0));
			exit;
		}
	}
	public function get_review(){
		$sp_id = $this->input->get("sp_id");
		$review_id = $this->input->get("review_id");
		$product_id = $this->input->get("product_id");
		$limit = $this->input->get("limit");
		if(!$sp_id){
			echo json_encode(array('status'=>0,'msg'=>"Seller Product Id  Is Missing.","code"=>2));
			exit;
		}
		/*if(!$review_id){
			echo json_encode(array('status'=>0,'msg'=>"Review Id Is Missing.","code"=>2));
			exit;
		}*/
		if(!$product_id){
			echo json_encode(array('status'=>0,'msg'=>"Product Id Is Missing.","code"=>2));
			exit;
		}
		if($limit == 0){
			$limit = "";
		}else{
			$limit = 1;
		}	
		$this->load->model('Reviewmodel');
		$reviewSelect ='r.review_id,r.date,u.email,u.user_pic,r.product_name,r.pv_id,r.product_id,r.review,r.rating,r.buyer_id,r.seller_id,r.order_id,r.sp_id,u.firstname as name, GROUP_CONCAT(CONCAT("'.$this->data['review_url']."thumbs/".'",rm.thumbnail)) as review_img_thumb,GROUP_CONCAT(CONCAT("'.$this->data['review_url'].'",rm.picture)) as review_img,r.is_fake,r.name AS review_name';
		$return = $this->Reviewmodel->getdata($sp_id,"0",$product_id,$limit,$reviewSelect,$review_id);
		if($return['total'] > 0){
			$reviewData = array();
			foreach($return['result'] as $index=>$r){
				$reviewData[$index]['review_id'] = $r['review_id'];
				$reviewData[$index]['date'] 	 = strtotime($r['date']);
				$reviewData[$index]['email'] 	 = $r['email'];
				$reviewData[$index]['user_pic']  = $this->data['profile_path'].$r['user_pic'];
				$reviewData[$index]['product_name'] = $r['product_name'];
				$reviewData[$index]['review'] 	 = stripslashes($r['review']);
				$reviewData[$index]['rating'] 	 = $r['rating'];
				$reviewData[$index]['name'] 	 = ($r['is_fake'] == "1")?$r['review_name']:$r['name'];
				$reviewData[$index]['review_img'] 	 = ($r['review_img'])?explode(',',$r['review_img']):array();
				$reviewData[$index]['review_img_thumb']	= ($r['review_img_thumb'])?explode(',',$r['review_img_thumb']):array();
			}
			echo json_encode($reviewData);
		}else{
			echo json_encode(array("status"=>0,"msg"=>"Review not found.","code"=>"0"));
		}
	}
	public function forgot_password(){
		$email = $this->input->get('email');
		if(!$email){
			echo json_encode(array('status'=>0,'msg'=>"Email  Is Missing.","code"=>2));
			exit;
		}
		$data = $this->User_Model->getUserByEmail($email);
		if($data){
			$data = $data[0];
			$this->load->helper('url');
			$reset_code= uniqid();
			$this->load->helper('email');
			$this->load->library('parser');
			$email_data['id'] = $reset_code;
			$email_data['firstname'] = $data['firstname'];
			$email_data['lastname'] = $data['lastname'];
			$from = $this->config->item('info_email');
			$name = 'Zab.ee';
			$to = $data["email"]; 
			$subject = 'Zabee : Your request for password';
			$email_data['page_name'] = "email_template_for_password_reset_for_mobile";
			$message = $this->parser->parse('front/emails/email_template', $email_data, TRUE);
			if($this->Utilz_Model->email_config($from, $name, $to, $subject, $message)){
				$today = date("Y-m-d");
				$this->User_Model->saveData(array("email"=>$email,"reset_code"=>$reset_code,"datetime"=>$today),"tbl_reset_code");
				// $this->email->message($message);
				// $this->email->send();
				//$this->session->set_flashdata('encryption',$encryption);
				echo json_encode(array('status'=>1,'msg'=>"Please check your mail.","code"=>1));
				exit;
			}
		}
		else{
			echo json_encode(array('status'=>0,'msg'=>"Your Email addresss is not registered with us. Please <a href = '".base_url()."/register'>Register</a> here.","code"=>2));
			exit;
		}
	}
	public function reset_password(){
		$email=$this->input->get('email');
		$reset_code= $this->input->get('reset_code');
		$password = $this->input->get('password');
		if(!$email){
			echo json_encode(array('status'=>0,'msg'=>"Email Is Missing.","code"=>2));
			exit;
		}
		if(!$reset_code){
			echo json_encode(array('status'=>0,'msg'=>"Reset Code Is Missing.","code"=>2));
			exit;
		}
		if(!$password){
			echo json_encode(array('status'=>0,'msg'=>"Password Is Missing.","code"=>2));
			exit;
		}
		$return = $this->Utilz_Model->getAllData("tbl_reset_code","datetime,is_used",array("email"=>$email,"reset_code"=>$reset_code));
		if(isset($return[0]->datetime) && $return[0]->is_used =="0"){
			$reset_date = strtotime($return[0]->datetime);
			$today = strtotime($return[0]->datetime);
			if($today <= $reset_date){
				$firstSalt = explode("@",$email);
				$lastSalt = "zab.ee";
				$hash = $firstSalt[0].$password.$lastSalt;
				$password = sha1($hash);
				$data = array("password"=>$password);
				$table = DBPREFIX."_users";
				$where = array("email"=>$email);
				$return = $this->User_Model->updateData($data,$table,$where);
				echo json_encode(array('status'=>1,'msg'=>"Password Reset.","code"=>1));
			}else{
				echo json_encode(array('status'=>0,'msg'=>"Your code is expired.","code"=>2));
			}
		}else{
			echo json_encode(array('status'=>0,'msg'=>"This code is used.","code"=>0));
		}
	}
	
	public function apply_voucher(){
		$response = array('status'=>0, 'message'=>'Error, please try again later');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('voucher_code','voucher code','trim|required');
		$this->form_validation->set_rules('user_id','user id','trim|required');
		$this->form_validation->set_rules('cart_total','cart total','trim|required');
		$this->form_validation->set_rules('cart','cart','trim|required');

		if($this->form_validation->run() == TRUE){
			$code = $this->input->post('voucher_code');
			$user_id = $this->input->post('user_id');
			$isAdmin = false;
			$discount_amount = 0;
			$discounted_cart_toatl = 0;
			if($code != ''){
				$this->load->model("admin/Policies_Model");
				$this->load->model("Cart_Model");
				$voucher = $this->Policies_Model->getVoucher('', '', $code);
				$cart_total = $this->input->post('cart_total');
				$cart = $this->input->post('cart');
				//echo '<pre>';print_r($voucher);die();
				$response = $this->Cart_Model->calculateVoucher($code, $cart, $voucher, $cart_total, $discount_amount, $discounted_cart_toatl, $user_id, $isAdmin);
				
			}
		}
		echo json_encode($response);
	}
	public function buyer_account(){
		if(!$this->input->get("user_id")){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}else{
			$user_id = $this->input->get("user_id");
			//$user_id = "5dae955f9a842";
		}
		$select = "id,userid,firstname,lastname,email,password,mobile,CONCAT('".$this->data['profile_path']."',user_pic)as user_pic";
		$user_data= $this->User_Model->getUserForAcc($user_id,$select);
		if($user_data){
			echo json_encode($user_data); exit();
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Invalid User id.","code"=>"1"));exit();
		}
		
	}
	public function update_buyer_account(){
		if(!$this->input->post("userid")){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$this->input->post("firstname")){
			echo json_encode(array("status"=>"0","msg"=>"First Name is missing.","code"=>"0"));
			exit();
		}
		if(!$this->input->post("lastname")){
			echo json_encode(array("status"=>"0","msg"=>"Last Name is missing.","code"=>"0"));
			exit();
		}
		$user_id = $this->input->post("userid");
		$post_data = $this->input->post();
		$image = $_FILES;
		if(!empty($image)){
			$config['upload_path'] 			 = "profile";
			$config['upload_thumbnail_path'] = "profile/thumbs";
			$config['maintain_ratio'] = TRUE;
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['quality'] = "100%";
			$config['remove_spaces'] = TRUE;
			
			$params['file'] = curl_file_create($image['photo']['tmp_name'], $image['photo']['type'],$image['photo']['name']);
			$params['image_type'] = "profile";
			$params['filesize'] = $image['photo']['size'];
			$params['config'] = json_encode($config);
			$upload_server = $this->config->item('media_url').'/file/upload_media';
			$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
			if($file->status == 1){
				$post_data['img'] = $file->images->original->filename;
			}else{
				echo json_encode(array('status'=>0,'msg'=>$file->message,"code"=>2));
				exit;
			}
			
		}
		$resp = $this->User_Model->updateUser($user_id, $post_data);
		if($resp){
			echo json_encode(array('status'=>1,'msg'=>"Buyer Account Updated","code"=>1));
			exit;
		}else{
			echo json_encode(array('status'=>1,'msg'=>"Error in updating buyer account","code"=>0));
			exit;
		}
	}
	
	public function get_shipping_by_id($shipping_id){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'shipping'=> array());

		if($shipping_id != ''){
			$shipping = $this->Utilz_Model->getShipping($shipping_id);
			if(is_array($shipping) && count($shipping) > 0){
				$response['status'] = 1;
				$response['message'] = 'OK';
				$response['shipping'] = $shipping[0];
			} else {
				$response['message'] = 'No shipping found';
			}
		}
		echo json_encode($response);
	}
	//Notifications
	public function getNotifications(){
		$user_id = $this->input->get("user_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		//$this->notificationCount = $this->Utilz_Model->getNotifications($userid, 1, 'new', true);
		//$this->notifications = $this->Utilz_Model->getNotifications($userid, 1, 'new', false, 'buyer');
		$data['text_notification'] = $this->Secure_Model->checkMsgNotification($user_id);
		$data['order_notification'] = $this->Secure_Model->checkOrderNotification($user_id);
		echo json_encode($data);die();
	}
	public function updateNotification(){
		$user_id = $this->input->get("user_id");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$this->Secure_Model->updateOrderNotification($user_id);
		echo json_encode(array("status"=>"1","msg"=>"Updated","code"=>"1"));exit();
	}
	//Logout
	public function add_device(){
		$user_id = $this->input->post("user_id");
		$fmc_token = $this->input->post("fmc_token");
		$device_info = $this->input->post("device_info");
		$platform = $this->input->post("platform");
		$device_id = $this->input->post("device_id");
		$user_type = $this->input->post("user_type");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$fmc_token){
			echo json_encode(array("status"=>"0","msg"=>"FMC tocken is missing.","code"=>"0"));
			exit();
		}
		if(!$device_info){
			echo json_encode(array("status"=>"0","msg"=>"Device info is missing.","code"=>"0"));
			exit();
		}
		if(!$platform){
			echo json_encode(array("status"=>"0","msg"=>"Platform is missing.","code"=>"0"));
			exit();
		}
		if(!$device_id){
			echo json_encode(array("status"=>"0","msg"=>"Device id is missing.","code"=>"0"));
			exit();
		}
		if(!$user_type){
			echo json_encode(array("status"=>"0","msg"=>"User Type is missing.","code"=>"0"));
			exit();
		}
		$data = $this->input->post();
		$table = DBPREFIX."_device";
		$where = array("fmc_token"=>$fmc_token,"device_id"=>$device_id);
		$checkToken = $this->Product_Model->getData($table,"id,user_id",$where,"","","","","","","","",true);
		if(!$checkToken){
			$return = $this->User_Model->saveData($data,$table);
			if($return){
				echo json_encode(array("status"=>"1","msg"=>"Device add successfully.","code"=>"1"));
				exit();
			}	
		}else{
			if($checkToken->user_id != $user_id){
				$deleteToken = $this->Product_Model->deleteData($checkToken->id,$table,"id");
				if($deleteToken){
					$return = $this->User_Model->saveData($data,$table);		
					if($return){
						echo json_encode(array("status"=>"1","msg"=>"Device add successfully.","code"=>"1"));
						exit();
					}else{
						echo json_encode(array("status"=>"0","msg"=>"Error in adding device.","code"=>"0"));exit();
					}	
				}
			}
			echo json_encode(array("status"=>"1","msg"=>"Token already exists.","code"=>"0"));
			exit();
		}
	}
	public function get_device_data(){
		$user_id = $this->input->get("user_id");
		$device_id = $this->input->get("device_id");
		$user_type = $this->input->get("user_type");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$where = array("user_id"=>$user_id);
		if($device_id){
			$where['device_id']	= $device_id;
		}
		if($user_type){
			$where['user_type']	= $user_type;
		}
		$table = DBPREFIX."_device";
		$data = $this->Product_Model->getData($table,"device_id,fmc_token,platform,device_info,user_type",$where);
		echo json_encode($data);
	}
	public function logout(){
		$user_id = $this->input->post("user_id");
		$fmc_token = $this->input->post("fmc_token");
		$user_type = $this->input->post("user_type");
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$fmc_token){
			echo json_encode(array("status"=>"0","msg"=>"FMC token is missing.","code"=>"0"));
			exit();
		}
		if(!$user_type){
			echo json_encode(array("status"=>"0","msg"=>"User type is missing.","code"=>"0"));
			exit();
		}
		$table = DBPREFIX."_device";
		$where = array("user_id"=>$user_id,"fmc_token"=>$fmc_token,"user_type"=>$user_type);
		$this->db->where($where);
		if($this->db->delete($table)){
			echo json_encode(array("status"=>"1","msg"=>"Token delete successfully.","code"=>"1"));
			exit();
		}else{
			echo json_encode(array("status"=>"1","msg"=>"Token not found.","code"=>"1"));
			exit();
		}
	}
	//Messages
	/*public function saveMessage(){
		$sender_id = $this->input->post("sender_id");
		$receiver_id = $this->input->post('receiver_id');
		$message = $this->input->post('message');
		$pv_id = $this->input->post('pv_id');
		$datetime = $this->input->post('datetime');
		$item_type = $this->input->post('item_type');
		$seller_id = $this->input->post('seller_id');
		$buyer_id = $this->input->post('buyer_id');
		$item_id = $this->input->post('item_id');
		if(!$sender_id){
			echo json_encode(array("status"=>"0","msg"=>"Sender id is missing.","code"=>"0"));
			exit();
		}
		if(!$receiver_id){
			echo json_encode(array("status"=>"0","msg"=>"Receiver id is missing.","code"=>"0"));
			exit();
		}
		if(!$message){
			echo json_encode(array("status"=>"0","msg"=>"Message is missing.","code"=>"0"));
			exit();
		}
		if(!$pv_id){
			echo json_encode(array("status"=>"0","msg"=>"Pv id is missing.","code"=>"0"));
			exit();
		}
		if(!$datetime){
			echo json_encode(array("status"=>"0","msg"=>"Date Time is missing.","code"=>"0"));
			exit();
		}
		if(!$item_type){
			echo json_encode(array("status"=>"0","msg"=>"Item type is missing.","code"=>"0"));
			exit();
		}
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		if(!$buyer_id){
			echo json_encode(array("status"=>"0","msg"=>"Buyer id is missing.","code"=>"0"));
			exit();
		}
		if(!$item_id){
			echo json_encode(array("status"=>"0","msg"=>"Item id is missing.","code"=>"0"));
			exit();
		}
		$data = array('sent_datetime'=>$datetime,'sender_id'=>$sender_id,'receiver_id'=>$receiver_id,'message'=>$message,'product_variant_id'=>$pv_id,'item_type'=>$item_type,'seller_id'=>$seller_id,'buyer_id'=>$buyer_id,'item_id'=>$item_id);
		$result = $this->Message_Model->saveData($data,'tbl_message');
		if($result){
			echo json_encode(array('status'=>'1','messages'=>"Msg sent successfully."));
		}else{
			echo json_encode(array('status'=>'0','messages'=>'Error in msg sending.'));
		}
	}*/
	public function getMessageList(){
		$this->load->model("Message_Model");
		$id = $this->input->get("user_id");
		if(!$id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$buyerList = $this->Message_Model->getBuyerList($id);
		$i = 0;
		if($buyerList['rows'] > 0){
			foreach($buyerList['result'] as $bl){ 
				if($bl->userid == $bl->seller_id){
					$buyerList['result'][$i]->picCheck = "seller_";
				}else{
					$buyerList['result'][$i]->picCheck = "buyer_";
				}
				if($bl->product_variant_id){
					if($bl->item_type == "product"){
						$product = $this->Message_Model->getMessageProductDetail($bl->product_variant_id);
						if(!empty($product)){
							$buyerList['result'][$i]->product_link = $product->product_name."-".$product->brand_name."-".$product->category_name;
							$buyerList['result'][$i]->product_id=$product->product_id;
							$buyerList['result'][$i]->product_name=$product->product_name;
							$buyerList['result'][$i]->store_name=$product->store_name;
						}else{
							$buyerList['result'][$i]->product_link="";
							$buyerList['result'][$i]->product_id="";
							$buyerList['result'][$i]->product_name="";
							$buyerList['result'][$i]->store_name="";
						}
					}
				}else{
					$buyerList['result'][$i]->product_link="";
					$buyerList['result'][$i]->product_id="";
					$buyerList['result'][$i]->product_name="";
					$buyerList['result'][$i]->store_name="";
				}
				$i++;
			}
		}else{
			$buyerList['result'] = array();
		}
		echo json_encode($buyerList);
	}
	public function getMessages(){
		$this->load->model("Message_Model");
		$desc 				= 0;
		$loadLimit 			= 0;
		$sender_id 			= $this->input->get('sender_id');
		$product_variant_id = $this->input->get('product_variant_id');
		$item_type 			= $this->input->get('item_type');
		$receiver_id 		= $this->input->get('user_id');
		$length 		= $this->input->get('length');
		if(!$sender_id){
			echo json_encode(array("status"=>"0","msg"=>"Sender id is missing.","code"=>"0"));
			exit();
		}
		if(!$product_variant_id){
			echo json_encode(array("status"=>"0","msg"=>"Product variant id is missing.","code"=>"0"));
			exit();
		}
		if(!$item_type){
			echo json_encode(array("status"=>"0","msg"=>"Item type is missing.","code"=>"0"));
			exit();
		}
		if(!$length){
			echo json_encode(array("status"=>"0","msg"=>"Length is missing.","code"=>"0"));
			exit();
		}
		$user_pic 			= "";
		if($this->input->get('desc')){
			$desc = 1;
		}
		if($this->input->get('loadLimit')){
			$loadLimit = $this->input->get('loadLimit');
		}
		if($sender_id == "" || $product_variant_id == "" || $item_type == ""){
			echo json_encode(array('status'=>'0','error'=>"No message found"));
			return false;
		}
		$getMessages = $this->Message_Model->getMessages($sender_id,$receiver_id,$desc,$loadLimit,$product_variant_id,$item_type,$length);
		$messages 	 = array();
		if($getMessages['rows'] >0){
			foreach($getMessages['result'] as $message){
				$who 		= "you";
				if($message->sender_id == $receiver_id){
					$who = "me";
				}
				$data = array('text'=>$message->message, 'sendtime'=>strtotime($message->sent_datetime),'sender_name'=>$message->sender_name,'who'=>$who,"sender_id"=>$message->sender_id,"receiver_id"=>$receiver_id);
				$messages[] = $data;
			}
			echo json_encode($messages);
		}else{
			echo json_encode(array());
		}
		exit();
	}
	public function cart_confirm(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'code'=>'000', 'cart'=>array(), 'errors'=>array(), 'code'=>'000', 'cart_total'=>0, 'tax'=>0, 'shipping'=>0, 'discount_amount'=>0);
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('voucher_code',$this->lang->line('voucher_code'),'trim');
		$this->form_validation->set_rules('zip_code','Zip code is required','trim|required');
		$this->form_validation->set_rules('cart','cart','trim|required');
		if($this->form_validation->run() == TRUE){
			$response['status'] = 1;
			$response['message'] = 'OK';
			$this->load->model("Cart_Model");
			$data = $this->input->post();
			$cart_data = json_decode($data['cart'],true);
			$user_id = $data['user_id'];
			$zip_code = $data['zip_code'];
			$voucher_code = $data['voucher_code'];
			$state = $data['state'];
			$cart = array();
			$cart_total = 0;
			$i = 0;
			$voucherFailed = false;
			foreach($cart_data as $item){
				$item_total = 0;
				$options = (isset($item['options']))?$item['options']:(object) array();
				$rowid = (isset($item['rowid']))?$item['rowid']:$this->cart->rowid($item['product_variant_id'], $options);
				$cart_data[$i]['rowid'] = $rowid;
				//$cart['index'] = $i;
				$cart[$rowid] = $item;
				$cart[$rowid]['rowid'] = $rowid;
				$cart[$rowid]['id'] = $cart_data[$i]['id'] = $item['product_variant_id'];
				$cart_data[$i]['qty'] = $cart[$rowid]['qty'] = $item['quantity'];
				$cart_data[$i]['original_price'] = $cart[$rowid]['original_price'] = $item['original'];
				$cart[$rowid]['options'] = $options;
				$cart_data[$i]['messages'] = array();
				$item_total = $cart[$rowid]['subtotal'] = $cart_data[$i]['subtotal'] = $item['quantity'] * $item['price'];
				if(isset($item['collect_tax']) && $item['collect_tax'] == 1){
					$cart_data[$i]['tax'] = (string)$this->checkTaxInfo($item['seller_id'],$state,$zip_code,$item_total);	
				}
				$cart_total += $item_total;
				$i++;

				//echo "<pre>";print_r($items);echo "</pre>";
			}
			$response['cart_total'] = $cart_total;
			//echo "<pre>";print_r($cart_data);echo "</pre>";die();
			$callFrom = 'api';
			$verify_cart = $this->Cart_Model->verifyCartItems($cart_data, $callFrom);
			$response['tax_rate'] = $this->Cart_Model->getTaxRate($zip_code);
			if($verify_cart['status'] == 1){
				$cart_data = $verify_cart['cart'];
				if($voucher_code != ''){
					$this->load->model("admin/Policies_Model");
					$voucher = $this->Policies_Model->getVoucher('', '', $voucher_code);
					$discount_amount = 0;
					$discounted_cart_toatl = 0;
					$isAdmin = false;
					$discount_voucher = $this->Cart_Model->calculateVoucher($voucher_code, $cart, $voucher, $cart_total, $discount_amount, $discounted_cart_toatl, $user_id, $isAdmin);
					if($discount_voucher['status'] == 1){
						$response['discount'] = $discount_voucher;
						$response['message'] = $discount_voucher['message'];
						$response['discount_amount'] = $this->cart->format_number($discount_voucher['discount_amount']);
						//$response['tax'] = $discount['tax'];
						$response['cart_total'] =  $discount_voucher['discounted_cart_total'];
					} else {
						$voucherFailed = true;
						$response['status'] = 0;
						$response['code'] = $discount_voucher['code'];
						$response['message'] = $discount_voucher['message'];
					}
					//echo "<pre>";print_r($discount);
				}
				if(!$voucherFailed){
					$subtotal_seller = array();
					$item_detail = array();
					$i = 0;
					foreach($cart_data as $item){
						$shipping = $this->Cart_Model->calculateCartItemShipping($item, $item['quantity'], $item['shipping_id'], $subtotal_seller);
						$item_detail[] = $shipping['item']['shipping_price'];
						$subtotal_seller = $shipping['subtotal_seller'];
						unset($cart[$rowid]['rowid']);
						unset($cart[$rowid]['qty']);
						unset($cart[$rowid]['id']);
					}
					//echo "<pre>";print_r($subtotal_seller);
					$shipping_total = $this->Cart_Model->calculateCartShipping($subtotal_seller);
					$response['shipping'] = $shipping_total;
					$response['tax'] = "0";//$this->cart->format_number($this->Cart_Model->getTax($zip_code,$response['cart_total']));
					$response['cart_total'] = $this->cart->format_number($response['cart_total']);
					$cart_data = $this->Cart_Model->updateShippingPerItem($cart_data, $subtotal_seller);
					$response['cart'] = $cart_data;
				}
			} else {
				$response['status'] = 0;
				$response['cart'] = $verify_cart['cart'];
				$response['message'] = $verify_cart['message'];
			}
		} else {
			$response['message'] = $this->lang->line('err');
			$response['error'] = validation_errors_api();
			$response['code'] = '001';
		}
		//echo "<pre>";print_r($response);die();
		echo json_encode($response);
	}
	//Save Cart In DB
	public function saveCart(){
		$user_id = $this->input->post("user_id");
		$cart_data = $this->input->post("cart_data");
		$save_for_later = $this->input->post("save_for_later");
		/*echo "<pre>";
		print_r($cart_data);
		print_r($save_for_later);die();*/
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$cart_data){
			$cart_data = "";
		}
		if(!$save_for_later){
			$save_for_later = "";
		}
		$this->load->model('Cart_Model');
		$oldCart = $this->Cart_Model->getCartContents($user_id);
		if($cart_data){
			$cart_data = json_decode($cart_data);
			$cart_data = (array)$cart_data;
			$keys = array_keys($cart_data);
			foreach($keys as $k){
				if($k !="total_items" && $k !="cart_total"){
					$cart_data[$k] = (array)$cart_data[$k];
					$cart_data[$k]['options'] = (array)$cart_data[$k]['options'];
				}
			}
		}
		if($oldCart){
			$this->Cart_Model->updateCart($user_id,$cart_data);
		}else{
			$this->Cart_Model->addtoCart($cart_data,$user_id);
		}
		//Save For Later
		$oldSave = $this->Cart_Model->getSavedForLaterContents($user_id);
		//echo "<pre>";print_r($oldSave);print_r($save_for_later);die();
		if($save_for_later){
			$save_for_later = json_decode($save_for_later);
			$save_for_later = (array)$save_for_later;
			$keys = array_keys($save_for_later);
			foreach($keys as $k){
				if($k !="total_items" && $k !="cart_total"){
					$save_for_later[$k] = (array)$save_for_later[$k];
					$save_for_later[$k]['options'] = (array)$save_for_later[$k]['options'];
				}
			}
		}
		if($oldSave){
			$this->Cart_Model->updateSaveForLater($user_id,$save_for_later);
		}else{
			$this->Cart_Model->addtoSaveForLater($save_for_later,$user_id);
		}
		echo json_encode(array("status"=>"1","msg"=>"Cart saved.","code"=>"1"));
	}
	public function checkLastUpdateInventory($parameter = "warning"){
		if($parameter == "warning"){
			$data = $this->Utilz_Model->getInventories();
		}else if($parameter == "deactive"){
			$data = $this->Utilz_Model->getWarnedInventories();
		}
		//echo "<pre>";print_r($data);die();
	}
	public function checkTaxInfo($seller_id,$state,$zip_code,$total){
		/*$seller_id = $this->input->post("seller_id");
		$state = $this->input->post("state");
		$zip_code = $this->input->post("zip_code");
		$total = $this->input->post("total");*/
		$tax = 0;
		$return = array("status"=>"0","msg"=>"Error.","code"=>"0","tax"=>"0");
		/*echo "<pre>";
		print_r($cart_data);
		print_r($save_for_later);die();*/
		if(!$seller_id){
			echo json_encode(array("status"=>"0","msg"=>"Seller id is missing.","code"=>"0"));
			exit();
		}
		if(!$state){
			echo json_encode(array("status"=>"0","msg"=>"State is missing.","code"=>"0"));
			exit();
		}
		if(!$zip_code){
			echo json_encode(array("status"=>"0","msg"=>"Zip code is missing.","code"=>"0"));
			exit();
		}
		if(!$total){
			echo json_encode(array("status"=>"0","msg"=>"Total is missing.","code"=>"0"));
			exit();
		}
		$tax_info = $this->Product_Model->getData(DBPREFIX.'_state_tax', 'id', array('userid'=>$seller_id,"state_id"=>$state),"","","","","","","","",true);
		if(isset($tax_info->id)){
			$tax = $this->cart->format_number($this->Cart_Model->getTax($zip_code, $total));
			$return = $tax;//array("status"=>"1","msg"=>"Seller Tax is ".$tax,"code"=>"200","tax"=>$tax);
		}
		return $tax;
		//echo json_encode($return);exit();
	}

	public function delete_account(){
		$response = array('status'=>0, 'message'=>'Error, please try again later', 'code'=>'000', 'errors'=>array());
		$this->load->library('form_validation');
		$this->form_validation->set_rules('user_id',$this->lang->line('user_id'),'trim|required');
		$this->form_validation->set_rules('email',$this->lang->line('email'),'trim');
		$this->form_validation->set_rules('password','password is required','trim|required');
		if($this->form_validation->run() == TRUE){
			$user_id = $this->input->post("user_id");
			$email = $this->input->post("email");
			$password = $this->input->post("password");
			if($email && $password){
				$firstSalt = explode("@",$email);
				$lastSalt = "zab.ee";
				$hash = $firstSalt[0].$password.$lastSalt;
				$isLogin = $this->Utilz_Model->apiLogin('',$email,sha1($hash),false,$this->data['profile_path']);
				if($isLogin){
					$response= $this->User_Model->deleteUser($user_id);
				}
			}else{
				$response['message'] = "User Not found.";
				$response['code'] = "003";
			}
			
		}else{
			$response['code'] = "004";
			$response['message'] = $this->lang->line('err');
			$response['error'] = validation_errors_api();
		}
		
		echo json_encode($response); exit();
	}

	//WishList Category
	public function add_wishlist_category(){
		$post_data = $this->input->post();
		if(!$this->input->post('user_id')){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		if(!$this->input->post('cat_name')){
			echo json_encode(array("status"=>"0","msg"=>"Category name is missing.","code"=>"0"));
			exit();
		}
		$resp = $this->Secure_Model->wishlist_cat_add($post_data, $post_data['user_id']);
		if($resp){
			$respo['status'] = '1';
			$respo['msg'] = 'Category added successfully';
			//$data = $this->Secure_Model->getForcedWishListName($post_data['user_id']);
			//$respo['data'] = array("id"=>$data->id, "category_name"=>$data->category_name);
			echo json_encode($respo);
		}else{
			echo json_encode(array("status"=>"0","msg"=>"Error.","code"=>"0"));
			exit();
		}		
	}
	public function get_wishlist_cat(){
		$user_id = $this->input->get('user_id');
		if(!$user_id){
			echo json_encode(array("status"=>"0","msg"=>"User id is missing.","code"=>"0"));
			exit();
		}
		$data = $this->Secure_Model->WishlistViaCategories($user_id);
		echo json_encode($data);
		exit();
	}
	public function delete_wishlist_category(){
		$id = $this->input->post('id');
		if(!$id){
			echo json_encode(array("status"=>"0","msg"=>"Category id is missing.","code"=>"0"));
			exit();
		}
		$response = $this->Product_Model->delete_wishlist_category($id);
		echo json_encode($response);
	}
}
?>