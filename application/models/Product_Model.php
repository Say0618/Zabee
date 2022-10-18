<?php
class Product_Model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		$this->load->database("default");
		$this->load->helper("main_helper");

	}
	///////     ----------------------- Product -----------------------------      ////////
	function getProduct($product_id = "",$select = "",$where = "")
	{
		if(is_array($product_id)){
			$where = "product.product_id IN ('".implode("','",$product_id)."')";
		}else if($product_id){
			$where = " product.product_id = '".$product_id."'";

		}		if($select == ""){
			$select = "product.product_id,product.`product_name`,product.`product_description`, cat.category_id, cat.category_name, b.brand_id, b.brand_name, sp.upc_code, sp.seller_sku, sp.price, sp.sell_quantity, sp.discount, sp.is_return, v.`v_title`, users1.userid AS userid1, users1.firstname AS name1,pv.variant_group, pv.variant_cat_group";
		}
		($where)?$where = " WHERE ".$where:"";
		$sql = "SELECT ".$select."
				FROM ".DBPREFIX."_product as product
				LEFT JOIN ".DBPREFIX."_seller_product AS sp ON sp.`product_id` = product.`product_id`
			  	LEFT JOIN ".DBPREFIX."_seller_product_variant AS spv ON sp.`sp_id` = spv.`sp_id`
				LEFT JOIN ".DBPREFIX."_variant AS v ON v.`v_id` = spv.`v_id`
				LEFT JOIN ".DBPREFIX."_brands AS b ON b.`brand_id` = product.brand_id
    			LEFT JOIN ".DBPREFIX."_categories AS cat ON (cat.category_id = product.category_id OR cat.category_id = product.sub_category_id)
				LEFT JOIN ".DBPREFIX."_users as users1 ON product.created_id = users1.userid
				LEFT JOIN ".DBPREFIX."_product_variant AS pv ON pv.product_id = product.product_id
				".$where." GROUP BY product.product_id";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}
	}

	function getProductData($product_id = "",$select = "",$where = "")
	{
		if($select == ""){
			$select = "product.created_id,product.created_by,product.slug,product.product_id,product.`product_name`,product.`product_description`,product.short_description,GROUP_CONCAT(cat.category_id) as category_id ,cat.category_name,b.brand_id,b.brand_name,ps.dimension_type,ps.height,ps.width,ps.length,ps.weight,ps.weight_type,ps.shipping_note,pd.dimension_type AS 'prd_dimension_type',pd.height AS 'prd_height',pd.width AS 'prd_width',pd.length AS 'prd_length',pd.weight AS 'prd_weight',pd.weight_type AS 'prd_weight_type',users1.userid AS userid1,users1.firstname AS name1,GROUP_CONCAT(pvc.v_cat_id) AS variant_cat_group,product.upc_code, product.sku_code";
		}
		$this->db->select($select)
				 ->from(DBPREFIX.'_product AS product')
				 ->join(DBPREFIX."_product_variant_category as pvc", 'pvc.product_id = product.product_id', 'left')
				 ->join(DBPREFIX."_brands as b", ' b.brand_id = product.brand_id ')
				 ->join(DBPREFIX."_product_shipping_info as ps", ' ps.product_id = product.product_id','left')
				 ->join(DBPREFIX."_product_dimension as pd", ' pd.product_id = product.product_id','left')
				 ->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
				 ->join(DBPREFIX."_categories AS cat", 'cat.category_id = pro_cat.category_id')
				 ->join(DBPREFIX."_users as users1", 'product.created_id = users1.userid');

				if(is_array($product_id)){
					$this->db->where_in("product.product_id",$product_id);
				}else{
					$this->db->where("product.product_id",$product_id);
				}if($where){
					$this->db->where($where);
				}

		$this->db->group_by("product.product_id");
		$result = $this->db->get();
		//echo $this->db->last_query();die();
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}
	}
	public function getProductDataForInventory($product_id = "",$seller_id=""){
		$inActiveShipping = array();
		$picList = array();
		$this->db->select('product.product_id,vc.v_cat_title,vc.v_cat_id')
				 ->from(DBPREFIX.'_product AS product')
				 ->join(DBPREFIX."_product_variant_category as pvc", 'pvc.product_id = product.product_id', 'left')
				 ->join(DBPREFIX."_variant_category vc", 'vc.v_cat_id = pvc.v_cat_id', 'left')
				 ->where(array('product.product_id'=>$product_id));

		$preDefinedVariant = $this->db->get()->result();
		$this->db->select('pi.inventory_id,sp.condition_id,sp.seller_sku,sp.sp_id,sp.seller_product_description,sp.shipping_ids,sp.return_id,p.pv_id,p.variant_group,p.variant_cat_group,pi.price,IF((pi.quantity - pi.sell_quantity) > 0,(pi.quantity - pi.sell_quantity),0) AS quantity,pi.discount, w.warehouse_id, pi.shipping_ids AS shipping, pi.quantity as total_qty,pi.warranty_id,pi.hubx_id,pi.seller_sku,pi.approve')
				 ->from(DBPREFIX.'_seller_product sp')
				 ->join(DBPREFIX."_product_variant as p", 'p.sp_id = sp.sp_id', 'left')
				 ->join(DBPREFIX."_product_inventory as pi", 'pi.seller_product_id = sp.sp_id AND pi.product_variant_id=p.pv_id AND pi.product_id='.$product_id)
				 ->join(DBPREFIX."_warehouse_inventory as w", 'sp.sp_id = w.seller_product_id AND p.pv_id = w.product_variant_id', 'left')
				 ->where(array('sp.product_id'=>$product_id,'sp.seller_id'=>$seller_id))
				 ->group_by('p.pv_id')->order_by("sp.sp_id");
		$condition = $this->db->get()->result();
		//echo $this->db->last_query();die();
		foreach($condition as $con){
			/*if(!isset($inActiveShipping[$con->condition_id])){
			$inActiveShipping[$con->condition_id] = $this->db->select('shipping_id,price,title,duration')
						->from(DBPREFIX.'_product_shipping')
						->where('(is_active = "0" OR is_deleted = "1")',NULL,FALSE)->where_in('shipping_id',$con->shipping_ids,FALSE)->get()->result();
			}*/
			if(!isset($picList[$con->condition_id])){
				$sp_id = $con->sp_id;
				$picList[$con->condition_id] = $this->Product_Model->getData(DBPREFIX.'_product_media',"media_id,iv_link,thumbnail,is_local,is_image,is_cover",array('product_id'=>$product_id,'sp_id'=>$sp_id),"","","position");
			}
		}
		$result['inActiveShipping'] = array();//$inActiveShipping;
		$result['image'] = $picList;
		$result['preDefinedVariant'] = $preDefinedVariant;
		$result['condition'] = $condition;
		return $result;
	}

	public function getConditionData($select = "",$where = "")
	{
		if($select == ""){
			$select = "conditions.*";
		}
		$sql = "SELECT ".$select." FROM ".DBPREFIX."_product_conditions as conditions WHERE is_delete='0' AND active='1' ".$where;
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}

	}

	public function getVariantData($select = "",$where = "")
	{
		if($select == ""){
			$select = "category.*";
		}
		$sql = "SELECT ".$select."
				FROM ".DBPREFIX."_variant_category as category
				JOIN tbl_variant variant ON variant.v_cat_id = category.`v_cat_id`
				WHERE  category.is_active = '1'
				AND category.display_status = '1'
				AND variant.`is_active` = '1' ".$where. "  GROUP BY category.v_cat_id";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}

	}

	public function getVariant($where = "",$id = "",$select="")
	{
		if($select == ""){
			$select = "variant.*";
		}
		$sql = "SELECT ".$select." FROM ".DBPREFIX."_variant as variant WHERE is_active='1' ".$where;
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}

	}

	function saveQuestion($data){
		$d= array('question'=>$data['question'],'asked_date'=>$data['asked_date'],'product_id'=>$data['product_id'],'seller_id'=>$data['seller_id'],'sp_id'=>$data['sp_id'],'userid'=>$data['userid']);
		$this->db->insert(DBPREFIX.'_questions', $d); # Inserting data
		$question_id = $this->db->insert_id();
		if($question_id){
			return true;
		}
	}

	function saveAnswer($data){
		$d = array('answer'=>$data['answer'],'answered_date'=>$data['answered_date'],'answer_user_id'=>$data['user_ans_id']);
		$this->db->where('id',$data['question_id']);
		$this->db->update(DBPREFIX.'_questions', $d);
		if($this->db->affected_rows() > 0){
			return true;
				} else {
			return false;
			}
		}

	function getQuestions($id){
		$this->db->select('q.*,u.firstname,u.firstname as answer_person')
		->from(DBPREFIX.'_questions AS q')
		->join(DBPREFIX."_users AS u",'u.userid = q.userid')
		->join(DBPREFIX."_users AS AU",'AU.userid = q.answer_user_id','LEFT')
		->where('sp_id', $id)
		->order_by('asked_date', "desc");
		$query = $this->db->get();
		$result['total'] = $query->num_rows();
		$result['result'] = $query->result_array();
		return $result;
		}

	function insertProduct($product,$sellerProduct,$sellerProductVariant,$image,$region,$feature,$product_inventory="",$proVariant="",$keywords, $approve = "",$seller_id=""){
		$id = 0;
		$i=0;
		$return = array();
		$this->db->trans_start(); # Starting Transaction

		# product data
		$this->db->insert(DBPREFIX.'_product', $product); # Inserting data
		$product_id = $this->db->insert_id();
		$return['product_id'] = $product_id;
		$return['sp_id'] = array();
		# seller product data
		if($image){
			$imageData = array('product_id'=>$product_id,'thumbnail'=>implode(',',$image['thumbnail']), 'iv_link'=>implode(',',$image['original']), 'is_local'=>$image['is_local']);
			$this->db->insert(DBPREFIX.'_product_media', $imageData);
		}
		foreach($sellerProduct as $sp){
			$sp['product_id'] = $product_id;
			$sp['approve'] = $approve;
			$this->db->insert(DBPREFIX.'_seller_product', $sp);
			$sp_id = $this->db->insert_id();
			$return['sp_id'][$i] = $sp_id;
			if(isset($proVariant[$i]) && !empty($proVariant[$i])){
				$pvi = 0;
				$pv_idArray= array();
				foreach($proVariant[$i] as $pv){
					$pv['product_id'] = $product_id;
					$pv['sp_id'] = $sp_id;
					$this->db->insert(DBPREFIX.'_product_variant', $pv);
					$pv_id = $this->db->insert_id();
					$pv_idArray[$pvi] = $pv_id;
					if(isset($sellerProductVariant[$i]) && !empty($sellerProductVariant[$i])){
						foreach($sellerProductVariant[$i][$pvi] as $spv){
							$variantData['pv_id'] = $pv_id;
							$variantData['v_id'] = $spv;
							$variantData['sp_id'] = $sp_id;
							$this->db->insert(DBPREFIX.'_seller_product_variant', $variantData);
						}
					}
					$pvi++;
				}
			}

			$keywordsData = array('product_id'=>$product_id,'sp_id'=>$sp_id,'pv_id'=>$pv_id,'keywords'=>$keywords);
			$this->db->insert(DBPREFIX.'_meta_keyword', $keywordsData);

			if(!empty($region)){
				foreach($region as $reg){
					$regionData = array('country_id'=>$reg,'product_id'=>$product_id,'seller_product_id'=>$sp_id);
					$this->db->insert(DBPREFIX.'_product_regions', $regionData);
				}
			}else{
				$regionData = array('country_id'=>0,'product_id'=>$product_id,'seller_product_id'=>$sp_id);
				$this->db->insert(DBPREFIX.'_product_regions', $regionData);
			}
			if($product_inventory !=""){
				$qi =0;
				foreach($product_inventory[$i]['quantity'] as $q){
					$inventoryData = array('created_date'=>$product_inventory[$i]['created_date'],'seller_id'=>$product_inventory[$i]['seller_id'],'product_id'=>$product_id,'seller_product_id'=>$sp_id,'product_name'=>$product['product_name'],'condition_id'=>$product_inventory[$i]['condition_id'],'price'=>$product_inventory[$i]['price'][$qi],'quantity'=>$q,'product_variant_id'=>$pv_idArray[$qi]);
					$this->db->insert(DBPREFIX.'_product_inventory', $inventoryData);
					$qi++;
				}
			}
			$i++;
		}
		foreach($feature as $f){
			$fData = array('feature'=>$f,'seller_id'=>$seller_id,"product_id"=>$product_id);
			$this->db->insert(DBPREFIX.'_product_features', $fData);
		}
		$this->db->trans_complete(); # Completing transaction

		/*Optional*/

		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			return FALSE;
		} else {
			# Everything is Perfect.
			# Committing data to the database.
			$this->db->trans_commit();
			return $return;
		}
	}

	function createProduct($product,$dummy_id,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id,$product_dimension,$category_id, $platform = ""){
		$return = array();
		$this->db->trans_start(); # Starting Transaction

		# product data
		$product['product_platform'] = $platform; # Product Platfrom merge into Product array for insertion
		$this->db->insert(DBPREFIX.'_product', $product); # Inserting data
		$product_id = $this->db->insert_id();
		$return['product_id'] = $product_id;

		# product dimension data
		$product_dimension['product_id'] = $return['product_id'];
		$this->db->insert(DBPREFIX.'_product_dimension', $product_dimension); # Inserting data

		# seller product data
		if($dummy_id){
			$this->db->where("dummy_id",$dummy_id);
			$imageData = array('product_id'=>$product_id,"is_active"=>"1","dummy_id"=>'');
			$this->db->update(DBPREFIX.'_product_media', $imageData);
		}

		if($category_id){
			foreach($category_id as $c){
				$cData = array('category_id'=>$c,"product_id"=>$product_id);
				$this->db->insert(DBPREFIX.'_product_category', $cData);
			}
		}
		if($video_link){
			if(!empty($video_link[0])){
				foreach($video_link as $index=>$v){
					$videoData = array('product_id'=>$product_id,'iv_link'=>$v,'is_image'=>"0",'dummy_id'=>"",'condition_id'=>"1");
					if(isset($media_id['media']) && $media_id['media_id'][$index] != ""){
						$this->db->where('media_id',$media_id['media_id'][$index]);
						$this->db->update(DBPREFIX.'_product_media', $videoData);
					}
					else{
						$this->db->insert(DBPREFIX.'_product_media', $videoData);
					}
				}
			}
		}
		if($feature){
			foreach($feature as $f){
				$fData = array('feature'=>$f,'seller_id'=>$seller_id,"product_id"=>$product_id);
				$this->db->insert(DBPREFIX.'_product_features', $fData);
			}
		}
		if($variant_cat){
			foreach($variant_cat as $vc){
				$vcData = array('product_id'=>$product_id,'v_cat_id'=>$vc);
				$this->db->insert(DBPREFIX.'_product_variant_category', $vcData);
			}
		}
		if($keywords){
			$keywordsData = array('product_id'=>$product_id,'keywords'=>$keywords);
			$this->db->insert(DBPREFIX.'_meta_keyword', $keywordsData);
		}
		if($shippingInfo){
			$shippingInfo['product_id'] = $return['product_id'];
			//$shipInfoData = array('product_id'=>$product_id,'height'=>$shippingInfo['height'],'width'=>$shippingInfo['width'],'length'=>$shippingInfo['length'],'weight'=>$shippingInfo['weight'],'shipping_note'=>$shippingInfo['ship_note']);
			$this->db->insert(DBPREFIX.'_product_shipping_info', $shippingInfo);
		}
		$this->db->trans_complete(); # Completing transaction

		/*Optional*/
		if ($this->db->trans_status() === FALSE) {

			# Something went wrong.
			$this->db->trans_rollback();
			return FALSE;
		} else {
			# Everything is Perfect.
			# Committing data to the database.
			$this->db->trans_commit();
			return $return;
		}
	}
	function updateProduct($product_id,$product,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id,$product_dimension,$category_id){
		$this->db->trans_start(); # Starting Transaction
		# product data
		$this->db->where("product_id",$product_id);
		$this->db->update(DBPREFIX.'_product', $product); # Inserting data

		# seller product data
		if($video_link){
			if(!empty($video_link['video_link'][0])){
				foreach($video_link['video_link'] as $index=>$v){
					$videoData = array('product_id'=>$product_id,'iv_link'=>$v,'is_image'=>"0",'dummy_id'=>"",'condition_id'=>"1");
					if(isset($media_id) && $media_id['media_id'][$index] != ""){
					$this->db->where('media_id',$media_id['media_id'][$index]);
					$this->db->update(DBPREFIX.'_product_media', $videoData);
					}else{
						$this->db->insert(DBPREFIX.'_product_media', $videoData);
					}
				}
			}
		}
		if($feature){
			$this->db->where("product_id",$product_id);
			$this->db->delete(DBPREFIX.'_product_features');
			foreach($feature as $f){
				$fData = array('feature'=>$f,'seller_id'=>$seller_id,"product_id"=>$product_id);
				$this->db->insert(DBPREFIX.'_product_features', $fData);
			}
		}
		if($category_id){
			$this->db->where("product_id",$product_id);
			$this->db->delete(DBPREFIX.'_product_category');
			foreach($category_id as $c){
				$cData = array('category_id'=>$c,"product_id"=>$product_id);
				$this->db->insert(DBPREFIX.'_product_category', $cData);
			}
		}
		$this->db->where("product_id",$product_id);
		$this->db->delete(DBPREFIX.'_meta_keyword');
		if($keywords){
			$keywordsData = array('product_id'=>$product_id,'keywords'=>$keywords);
			$this->db->insert(DBPREFIX.'_meta_keyword', $keywordsData);
		}
		if($product_dimension){
			//$prd_dimensionData = array('product_id'=>$product_id,'dimension_type'=>$product_dimension['dimension_type'],'height'=>$product_dimension['height'],'width'=>$product_dimension['width'],'length'=>$product_dimension['length'],'weight'=>$product_dimension['weight'],'weight_type'=>$product_dimension['weight_type']);
			$this->db->where("product_id",$product_id);
			$this->db->update(DBPREFIX.'_product_dimension', $product_dimension);
			if($this->db->affected_rows() == 0){
				$product_dimension['product_id'] = $product_id;
				$this->db->insert(DBPREFIX.'_product_dimension', $product_dimension);
			}
		}
		if($shippingInfo){
			//$shipInfoData = array('product_id'=>$product_id,'dimension_type'=>$shippingInfo['dimension_type'],'height'=>$shippingInfo['height'],'width'=>$shippingInfo['width'],'length'=>$shippingInfo['length'],'weight_type'=>$shippingInfo['weight_type'],'weight'=>$shippingInfo['weight'],'shipping_note'=>$shippingInfo['ship_note']);
			$this->db->where("product_id",$product_id);
			if($this->db->update(DBPREFIX.'_product_shipping_info', $shippingInfo)){
				if($this->db->affected_rows() == 0){
					$shippingInfo['product_id'] = $product_id;
					$this->db->insert(DBPREFIX.'_product_shipping_info', $shippingInfo);
				}
			}
		}
		$this->db->where("product_id",$product_id);
		$this->db->delete(DBPREFIX.'_product_variant_category');
		if($variant_cat){
			foreach($variant_cat as $vc){
				$vcData = array('product_id'=>$product_id,'v_cat_id'=>$vc);
				$this->db->insert(DBPREFIX.'_product_variant_category', $vcData);
			}
		}
		$this->db->trans_complete(); # Completing transaction

		/*Optional*/
		if ($this->db->trans_status() === FALSE) {

			# Something went wrong.
			$this->db->trans_rollback();
			return FALSE;
		} else {
			# Everything is Perfect.
			# Committing data to the database.
			$this->db->trans_commit();
			return TRUE;
		}
	}
	function deleteVideoLink($id){
		$this->db->where("media_id",$id);
		$this->db->delete(DBPREFIX.'_product_media');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	function createInventory($product_name,$product_id,$sellerProduct,$sellerProductVariant,$dummy_id="",$region="",$product_inventory="",$proVariant="",$seller_id="",$video_link="",$media_id="",$warehouse_id="", $inventory_shipping_ids="", $platform = ""){
		//echo "<pre>";print_r($proVariant);die();
		$id = 0;
		$i=0;
		$return = array();
		$this->db->trans_start(); # Starting Transaction
		$checkActive = $this->db->select("is_active")->from("tbl_product")->where(array("product_id"=>$product_id,'is_active'=>"1"))->get()->num_rows();
		if($checkActive > 0 && $warehouse_id == "0"){
			$is_active = "1";
		}else{
			$is_active = "0";
		}
		# product data
		$return['sp_id'] = array();

		# seller product data
		foreach($sellerProduct as $sp){
			$date = (isset($sp['created_date']))?$sp['created_date']:$sp['updated_date'];
			$sp['product_id'] = $product_id;
			if($is_active == "1" && $sp['sp_id'] == ""){
				$sp['approved_date'] = $sp['created_date'];
			}
			if($sp['sp_id'] != "" && is_numeric($sp['sp_id'])){
				$sp_id = $sp['sp_id'];
				$this->db->where('sp_id',$sp_id);
				$this->db->update(DBPREFIX.'_seller_product', $sp);
			}else{
				$sp['approve'] = ($checkActive > 0)?"1":"0";
				$this->db->insert(DBPREFIX.'_seller_product', $sp);
				$sp_id = $this->db->insert_id();
			}
			$return['sp_id'][$i] = $sp_id;
			$condition_id = $sp['condition_id'];
			$imageData = array("sp_id"=>$sp_id);
			if($dummy_id !=""){
				$this->db->where("dummy_id",$dummy_id);
				$this->db->where(array("condition_id"=>$sp['condition_id'],"product_id"=>$product_id));
				$this->db->update(DBPREFIX.'_product_media', $imageData);
			}
			if($video_link){
				if(!empty($video_link['condition_video_link'.$sp['condition_id']][0])){
					foreach($video_link['condition_video_link'.$sp['condition_id']] as $index=>$v){
						$videoData = array('product_id'=>$product_id,'sp_id'=>$sp_id,'iv_link'=>$v,'is_image'=>"0","is_local"=>"0",'dummy_id'=>"",'condition_id'=>$sp['condition_id']);
						if(isset($media_id['media_id'.$sp['condition_id']][$index]) && $media_id['media_id'.$sp['condition_id']][$index] != ""){
						$this->db->where('media_id',$media_id['media_id'.$sp['condition_id']][$index]);
						$this->db->update(DBPREFIX.'_product_media', $videoData);
						}
						else{
							$this->db->insert(DBPREFIX.'_product_media', $videoData);
						}
					}
				}
			}
			if(isset($proVariant[$i]) && !empty($proVariant[$i])){
				$pvi = 0;
				$pv_idArray= array();
				//echo"<pre>"; print_r($proVariant); die("here");
				foreach($proVariant[$i] as $pv){
					//echo "<pre>";print_r($pv);die();
					if(isset($warehouse_id[$pv['condition_id']]) && $warehouse_id[$pv['condition_id']] !=""){
						$is_warehouse = "1";
					}else{
						$is_warehouse = "0";
					}
					$pv['product_id'] = $product_id;
					$pv['sp_id'] = $sp_id;
					$pv['is_active'] = $is_active;

					$discount = $pv['discount'];
					if(isset($pv['warranty']) && $pv['warranty'] != ""){
						$warranty = $pv['warranty'];
					}else{
						$warranty = 1; // Change krna hay issay product may add krni hay warranty phr uski id aaegi is jaga.
					}
					$hubx_id = $pv['hubx_id'];
					$seller_sku = $pv['seller_sku'];
					unset($pv['warranty']);
					unset($pv['hubx_id']);
					unset($pv['seller_sku']);
					//echo "<pre>";print_r($pv);echo "<hr>";
					$pv['previous_qty'] = ($pv['previous_qty'] == "")?0:$pv['previous_qty'];
					$pv['total_qty'] = ($pv['total_qty'] == "")?0:$pv['total_qty'];
					$qty = ($pv['quantity'] - $pv['previous_qty']) + $pv['total_qty'];
					unset($pv['discount']);
					unset($pv['total_qty']);
					unset($pv['previous_qty']);
					if($pv['pv_id'] !=""){
						// die("previous: ".$pv['previous_qty']."<br>given: ".$pv['quantity']."<br>total: ".$pv['total_qty']."<br>new: ".$qty);
						$pv_id = $pv['pv_id'];
						$pv['updated_date'] = $date ;
						$this->db->where(array('sp_id'=>$sp_id,'pv_id'=>$pv_id));
						$this->db->update(DBPREFIX.'_product_variant', $pv);
						$pv_idArray[$pvi] = $pv_id;

						//Inventroy Data Update
						$inventoryData = array('updated_date'=>$date,'seller_id'=>$pv['seller_id'],'product_id'=>$product_id,'seller_product_id'=>$sp_id,'product_name'=>$product_name,'condition_id'=>$pv['condition_id'],'price'=>$pv['price'],'quantity'=>$qty,'product_variant_id'=>$pv_id,"warehouse_id"=>$warehouse_id,'discount'=>$discount, 'shipping_ids'=>$inventory_shipping_ids[$pv['condition_id']][$pvi],"warranty_id"=>$warranty,"hubx_id"=>$hubx_id,"seller_sku"=>$seller_sku,"is_warning"=>"0");
						$piw = array('seller_product_id'=>$sp_id,"product_variant_id"=>$pv_id);
						$this->db->where($piw);
						$this->db->set("inventory_id","LAST_INSERT_ID(inventory_id)",false);
						$this->db->update(DBPREFIX.'_product_inventory', $inventoryData);
						//print_r($this->db->error());
						// echo $this->db->last_query();
						//Get last update id.
						$inventory_id = $this->db->query("SELECT last_insert_id() as inventory_id")->row();
						$wi_id = $this->db->select("warehouse_id,inventory_id")->from(DBPREFIX."_warehouse_inventory")->where($piw)->where("inventory_id",$inventory_id->inventory_id)->get()->row();

						//Warehouse Data
						if(!empty($wi_id) && $warehouse_id != "0"){
							$warehouseData = array('updated_date'=>$date,'inventory_id'=>$wi_id->inventory_id,'warehouse_id'=>$warehouse_id,'product_variant_id'=>$pv_id,"seller_product_id"=>$sp_id,"quantity"=>$pv['quantity']);
							$this->db->where(array("inventory_id"=>$wi_id->inventory_id,"product_variant_id"=>$pv_id,"seller_product_id"=>$sp_id));
							$this->db->update(DBPREFIX.'_warehouse_inventory', $warehouseData);
						}else if(empty($wi_id) && $warehouse_id !="0"){
							$warehouseData = array('created_date'=>$date,'inventory_id'=>$inventory_id->inventory_id,'warehouse_id'=>$warehouse_id,'product_variant_id'=>$pv_id,"seller_product_id"=>$sp_id,"quantity"=>$pv['quantity'],"is_received"=>"0");
							$this->db->insert(DBPREFIX.'_warehouse_inventory', $warehouseData);
						}

					}else{
						//Product Variant Data Create
						$pv['created_date'] = $date ;
						$this->db->insert(DBPREFIX.'_product_variant', $pv);
						$pv_id = $this->db->insert_id();
						$pv_idArray[$pvi] = $pv_id;
						//print_r($this->db->error());
						//Inventroy Data Create
						$inventoryData = array('created_date'=>$date,'seller_id'=>$pv['seller_id'],'product_id'=>$product_id,'seller_product_id'=>$sp_id,'product_name'=>$product_name,'condition_id'=>$pv['condition_id'],'price'=>$pv['price'],'quantity'=>$pv['quantity'],'product_variant_id'=>$pv_id,'approve'=>$is_active,"warehouse_id"=>$warehouse_id,'discount'=>$discount, 'shipping_ids' => $inventory_shipping_ids[$pv['condition_id']][$pvi], "inventory_platform" => $platform,"warranty_id"=>$warranty,"hubx_id"=>$hubx_id,"seller_sku"=>$seller_sku);
						if($warehouse_id == "0"){
							$inventoryData['approved_date'] = $date;
						}
						$this->db->insert(DBPREFIX.'_product_inventory', $inventoryData);
						//print_r($this->db->error());
						$inventory_id = $this->db->insert_id();
						if($warehouse_id != "0"){
							$warehouseData = array('created_date'=>$pv['created_date'],'inventory_id'=>$inventory_id,'warehouse_id'=>$warehouse_id,'product_variant_id'=>$pv_id,"seller_product_id"=>$sp_id,"quantity"=>$pv['quantity'],"is_received"=>"0");
							$this->db->insert(DBPREFIX.'_warehouse_inventory', $warehouseData);
						}
						//print_r($this->db->error());
					}
					if(isset($sellerProductVariant[$i]) && !empty($sellerProductVariant[$i])){
						$checkFirstTime = 0;
						foreach($sellerProductVariant[$i][$pvi] as $spv){
							$variantData = array('pv_id'=>$pv_id,"v_id"=>$spv,"sp_id"=>$sp_id,"is_active"=>$is_active);
							if($pv['pv_id'] !=""){
								if($checkFirstTime == 0){
									$this->db->where(array('sp_id'=>$sp_id,"pv_id"=>$pv_id));
									$this->db->delete(DBPREFIX.'_seller_product_variant');
									//print_r($this->db->error());
								}
								$this->db->insert(DBPREFIX.'_seller_product_variant', $variantData);
								//print_r($this->db->error());
							}else{
								$this->db->insert(DBPREFIX.'_seller_product_variant', $variantData);
								//print_r($this->db->error());
							}
							$checkFirstTime++;
						}
					}
					$this->megaSlug($product_id,$pv_id);
					$pvi++;
				}
			}
			if(!empty($region)){
				foreach($region as $reg){
					$regionData = array('country_id'=>$reg,'product_id'=>$product_id,'seller_product_id'=>$sp_id);
					$this->db->insert(DBPREFIX.'_product_regions', $regionData);
				}
			}else{
				$regionData = array('country_id'=>0,'product_id'=>$product_id,'seller_product_id'=>$sp_id);
				$this->db->insert(DBPREFIX.'_product_regions', $regionData);
			}
			$i++;
		}
		$this->db->trans_complete(); # Completing transaction

		/*Optional*/
		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			return FALSE;
		}
		else {
			# Everything is Perfect.
			# Committing data to the database.
			$this->db->trans_commit();
			return $return;
		}
	}

	public function frontProductDetails($product_id="", $created_id="", $condition_id="", $variant_id="",$pageing="0",$limit="",$order="",$where="",$group_by="",$select="", $seller_product_id = "", $region="", $isCount = false,$userid =""){
		if($pageing > 1){
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		$this->db->query('SET SESSION group_concat_max_len = 1000000');
		if($created_id){
			$this->db->where('product.created_id',$created_id);
		}
		if($product_id){
			if(is_array($product_id)){
				$product_id = implode(',',$product_id);
				$this->db->where_in('product.product_id',$product_id,FALSE);
			}else{
				$this->db->where('product.product_id',$product_id);
			}
		}
		if($where){
			$this->db->where($where);
		}
		if($region !=""){
			//$this->db->where('(pr.country_id='.$region.' OR pr.country_id = "0")',NULL,FALSE);
		}
		if($group_by == ""){
			$group_by="sp.seller_id";
		}
		if($order == ""){
			$order = "sp.sp_id DESC";
		}
		if($condition_id != ''){
			if(is_array($condition_id)){
				$condition_id = implode(',',$condition_id);
				$this->db->where_in('sp.condition_id',$condition_id,FALSE);
			}else{
				$this->db->where('sp.condition_id',$condition_id);
			}
		}
		if($seller_product_id !=""){
			$this->db->where('sp.sp_id',$seller_product_id);
		}
		if($select == ""){
			$select ='CONCAT(GROUP_CONCAT(DISTINCT REPLACE(product.product_name," ","-")),"-",GROUP_CONCAT(DISTINCT REPLACE(cat.category_name," ","-")),"-",GROUP_CONCAT(DISTINCT REPLACE(b.brand_name," ","-")),"-",GROUP_CONCAT(DISTINCT REPLACE(pc.condition_name," ","-")), IF(v.v_title != "",CONCAT("-",GROUP_CONCAT(DISTINCT REPLACE(v.v_title, " ", "-"))),""), IF(vc.v_cat_title != "",CONCAT("-",GROUP_CONCAT(DISTINCT REPLACE(vc.v_cat_title, " ", "-"))),"")) AS product_link,product.product_id, sp.sp_id AS seller_product_id, product.product_name, product.product_description,pm.`iv_link`AS product_image,pm.`thumbnail` AS product_image_primary,  pm.`thumbnail` AS thumbnail,GROUP_CONCAT(DISTINCT CONCAT(\'{"id":"\', cat.category_id, \'", "value":"\',cat.category_name,\'"}\')) AS category,
			GROUP_CONCAT(DISTINCT CONCAT(\'{"id":"\', b.brand_id, \'", "value":"\',b.brand_name,\'"}\')) AS brand,
			CONCAT("[",GROUP_CONCAT(DISTINCT CONCAT(\'{"id":"\', pc.condition_id, \'", "value":"\', pc.condition_name,\'","seller_product_id":"\',sp.sp_id,\'", "seller_id":"\',sp.seller_id,\'","seller":"\',users1.firstname,\'", "store_name":"\',ss.`store_name`,\'", "pv_id":"\',pv.pv_id,\'","price":"\',pin.price,\'"}\')),"]") AS conditions, sp.upc_code, sp.seller_sku, IF(sp.comments IS NULL,CONCAT("[",GROUP_CONCAT(DISTINCT CONCAT(\'{"price":"\',pin.price,\'", "condition_id":"\',pc.condition_id,\'", "seller_id":"\',sp.seller_id, \'"}\')),"]"),CONCAT("[",GROUP_CONCAT(DISTINCT CONCAT(\'{"price":"\',pin.price,\'", "condition_id":"\',pc.condition_id,\'", "pv_id":"\',pv.pv_id,\'" "comments":"\',sp.comments,\'", "seller_id":"\',sp.seller_id,\'"}\')),"]")) AS product_price ,MIN(pin.price) AS min_price, MAX(pin.price) AS max_price,d.value as discount_value,d.type as discount_type, d.valid_from as valid_from, d.valid_to as valid_to, sp.sell_quantity, sp.discount, sp.is_return ,CONCAT("[",GROUP_CONCAT(\'{"id":"\', v.v_id, \'", "value":"\', v.v_title,\'" , "category":"\',vc.v_cat_title,\'" , "condition_id":"\',pc.condition_id,\'"}\'),"]") AS variant, sp.seller_id,  CONCAT("[",GROUP_CONCAT(DISTINCT CONCAT(\'{"image":"\', pm.thumbnail,\'", "condition_id":"\',pc.condition_id,\'","original_image":"\',pm.iv_link as image_link,\'","is_local":"\',pm.is_local,\'"}\')),"]") AS variant_img_thumb,CONCAT("[",GROUP_CONCAT(DISTINCT CONCAT(\'{"image":"\',pm.image_link,\'", "condition_id":"\',pc.condition_id,\'","is_local":"\',pm.is_local,\'"}\')),"]") AS variant_img, users1.firstname AS name1, sp.seller_id, (pin.quantity - pin.sell_quantity) AS quantity,pr.country_id,ss.store_name,AVG(preview.`rating`) AS rating';
		}
		$this->db->select($select, false)
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			//->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id','LEFT')
			//->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id','LEFT')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"')
			->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
			->join(DBPREFIX."_categories AS cat",'cat.category_id = pro_cat.category_id AND `cat`.`display_status` = "1" AND `cat`.`is_active` = "1" AND `cat`.`is_private` = "0"' )
			->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')
			//->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND (pm.sp_id = sp.sp_id OR pm.condition_id = 1) AND pm.is_cover ="1"','LEFT')
			//->join(DBPREFIX.'_product_regions AS pr','pr.product_id = product.product_id','LEFT')
			//->join(DBPREFIX.'_product_variant AS pv','pv.sp_id = sp.sp_id AND pv.`product_id` = product.`product_id` AND pv.`seller_id` = sp.`seller_id` AND pv.`condition_id` = sp.`condition_id`','LEFT')
			->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id AND (pin.quantity - pin.sell_quantity) > 0 AND pin.seller_id !="1"')
			->join(DBPREFIX."_users AS users",'pin.seller_id = users.userid AND users.is_block="0" AND users.is_active="1"')
			->join(DBPREFIX."_seller_store ss", "ss.seller_id = pin.seller_id AND ss.is_approve = '1' AND ss.is_active = '1'")
			->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id` AND d.display_status = "1"',"LEFT")
			->join(DBPREFIX.'_product_reviews AS preview','preview.product_id = pin.product_id AND preview.pv_id = pin.product_variant_id','LEFT')
			->where(array('sp.approve'=>'1','sp.is_active'=>'1','sp.seller_id !='=>"1",'pin.approve'=>"1",'product.is_active'=>"1"))
			//->where("pin.product_variant_id = (SELECT `pin`.`product_variant_id` AS `pv_id` FROM `tbl_product_inventory` AS `pin` WHERE `pin`.`product_id` = product.product_id  AND (pin.quantity ) > 0 AND `pin`.`seller_id` = sp.seller_id AND `pin`.`approve` = '1' GROUP BY `pin`.`price` ORDER BY `pin`.`condition_id`,pin.price LIMIT 1)",NULL,FALSE)
			->group_by($group_by)
			->order_by($order);
			if($userid != ""){
				$this->db->join(DBPREFIX.'_wishlist as w',' w.product_id = product.product_id AND w.user_id="'.$userid.'"','LEFT');
			}
			$query = $this->db->get();
			//echo"<pre>"; print_r($this->db->last_query())."<hr>";
			$data['rows'] = $query->num_rows();
			$data['result']=$query->result_array();
			return $data;
	}

	public function forntProductData($product,$product_index = "seller_id",$type='0'){
		$categoryId = array();
		$productData = array();
		$product_price = "";
		$i=0;
		foreach($product['result'] as $p){
			$index = $p[$product_index];
			$categoryId = array();
			$categoryTitle = array();
			$condition 	= (isset($p['conditions']))?json_decode($p['conditions']):"";
			$variant	= json_decode($p['variant']);
			$brand 		= json_decode($p['brand']);
			$category 	= json_decode($p['category']);
			$variant_img 	= (isset($p['variant_img']))?json_decode($p['variant_img']):"";
			$variant_img_thumb 	= (isset($p['variant_img_thumb']))?json_decode($p['variant_img_thumb']):"";
			$product_link = "";
			$printTempCondition = 0;
			$tempCondition = array();
			$otherSeller = array();
			if(isset($productData[$index])){
				if($condition !=""){
					foreach ($condition as $c){
						if(isset($c->sellecr_id)){
							$otherSeller[$c->seller_id][$c->value]['product_name'] = $p['product_name'];
							$otherSeller[$c->seller_id][$c->value]['store_name'] = (isset($c->store_name))?$c->store_name:"";
							$otherSeller[$c->seller_id][$c->value]['store_address'] = (isset($c->store_address))?$c->store_address:"";
							$otherSeller[$c->seller_id][$c->value]['seller_id'] = (isset($c->seller_id))?$c->seller_id:"";
							$otherSeller[$c->seller_id][$c->value]['seller_product_id'] = (isset($c->seller_product_id))?$c->seller_product_id:"";
							$otherSeller[$c->seller_id][$c->value]['price'] = (isset($c->price))?$c->price:"";
							$otherSeller[$c->seller_id][$c->value]['condition'] = (isset($c->value))?$c->value:"";
							if(isset($c->pv_id)){
								$otherSeller[$c->seller_id][$c->value]['pv_id'] = $c->pv_id;
							}
						}
						if(!empty($variant)){
							foreach($variant as $v){
								if($c->id == $v->condition_id){
									$productData[$index]['details']['condition'][$c->value]['price'] = (isset($c->price))?$c->price:"";
									$productData[$index]['details']['condition'][$c->value]['id'] = (isset($c->id))?$c->id:"";
									$productData[$index]['details']['condition'][$c->value]['comments'] = (isset($c->comments))?$c->comments:"";
									$productData[$index]['details']['condition'][$c->value]['variant'][$v->category][$v->id] = $v->value;
									$productData[$index]['details']['condition'][$c->value]['variant']['condition']= $v->condition_id;
									$productData[$index]['details']['condition'][$c->value]['seller_product_id']= (isset($c->seller_product_id))?$c->seller_product_id:"";
									$productData[$index]['details']['condition'][$c->value]['seller_id']= (isset($c->seller_id))?$c->seller_id:"";
									if(isset($c->pv_id)){
										$productData[$index]['details']['condition'][$c->value]['pv_id'] = $c->pv_id;
									}
								}
							}
						}
						if(!empty($variant_img_thumb)){
							foreach($variant_img_thumb as $vit){
								if($c->id == $vit->condition_id){
									$productData[$index]['details']['condition'][$c->value]['variant_img_thumb'][] = $vit->image;
									$productData[$index]['details']['condition'][$c->value]['variant_img'][] = array('thumb'=>$vit->image, 'image'=>$vit->original_image,'is_local'=> $vit->is_local);
								}
							}
						}
					}
				}
			}else{
				$productData[$index]['thumbnail'] 					= isset($p['thumbnail'])?$p['thumbnail']:"";
				$productData[$index]['product_id'] 					= $p['product_id'];
				$productData[$index]['seller_product_id'] 			= $p['seller_product_id'];
				$productData[$index]['product_name'] 				= $p['product_name'];
				$productData[$index]['product_description'] 		= isset($p['product_description'])?$p['product_description']:"";
				$productData[$index]['product_image'] 				= explode(',',$p['product_image']);
				$productData[$index]['product_image_primary'] 		= isset($p['product_image_primary'])?$p['product_image_primary']:"";
				$productData[$index]['upc_code']					= $p['upc_code'];
				$productData[$index]['seller_sku'] 					= $p['seller_sku'];
				$productData[$index]['sell_quantity'] 				= $p['sell_quantity'];
				$productData[$index]['discount'] 					= $p['discount'];
				$productData[$index]['is_return']					= $p['is_return'];
				$productData[$index]['seller_id'] 					= $p['seller_id'];
				$productData[$index]['name1'] 						= $p['name1'];
				$productData[$index]['brand_id']					= (isset($brand->id))?$brand->id:"";
				$productData[$index]['brand'] 						= (isset($brand->value))?$brand->value:"";
				if($type==1){
				$productData[$index]['category_id']					= $p['sub_category_id'];
				$productData[$index]['category'] 					= "";
				}
				if(isset($p['pv_id'])){
					$productData[$index]['pv_id'] 					= $p['pv_id'];
				}else{
					$productData[$index]['pv_id'] 					= '';
				}
				if($condition != ""){
					foreach($condition as $c){
						if(isset($c->sellecr_id)){
							$tempCondition[$c->seller_id] = $c->value;
							$otherSeller[$c->seller_id][$c->value]['product_name'] = $p['product_name'];
							$otherSeller[$c->seller_id][$c->value]['store_name'] = $c->store_name;
							$otherSeller[$c->seller_id][$c->value]['store_address'] = $c->store_address;
							$otherSeller[$c->seller_id][$c->value]['seller_id'] = $c->seller_id;
							$otherSeller[$c->seller_id][$c->value]['seller_product_id'] = $c->seller_product_id;
							$otherSeller[$c->seller_id][$c->value]['price'] = $c->price;
							$otherSeller[$c->seller_id][$c->value]['condition'] = $c->value;
							if(isset($c->pv_id)){
								$otherSeller[$c->seller_id][$c->value]['pv_id'] = $c->pv_id;
							}
						}
						if(!empty($variant)){
							foreach($variant as $v){
								if($c->id == $v->condition_id){
									$productData[$index]['details']['condition'][$c->value]['price'] = (isset($c->price))?$c->price:"";
									$productData[$index]['details']['condition'][$c->value]['id'] = (isset($c->id))?$c->id:"";
									$productData[$index]['details']['condition'][$c->value]['comments'] = (isset($c->comments))?$c->comments:"";
									$productData[$index]['details']['condition'][$c->value]['variant'][$v->category][$v->id]= $v->value;
									$productData[$index]['details']['condition'][$c->value]['variant']['condition']= $v->condition_id;
									$productData[$index]['details']['condition'][$c->value]['seller_product_id']= (isset($c->seller_product_id))?$c->seller_product_id:"";
									$productData[$index]['details']['condition'][$c->value]['seller_id']= (isset($c->seller_id))?$c->seller_id:"";
									if(isset($c->pv_id)){
										$productData[$index]['details']['condition'][$c->value]['pv_id'] = $c->pv_id;
									}
								}
							}
						}
						if(!empty($variant_img_thumb)){
							foreach($variant_img_thumb as $vit){
								if($c->id == $vit->condition_id){
									$productData[$index]['details']['condition'][$c->value]['price'] = (isset($c->price))?$c->price:"";
									$productData[$index]['details']['condition'][$c->value]['id'] = (isset($c->id))?$c->id:"";
									$productData[$index]['details']['condition'][$c->value]['variant_img_thumb'][] = $vit->image;
									$productData[$index]['details']['condition'][$c->value]['variant_img'][] = array('thumb'=>$vit->image, 'image'=>$vit->original_image,'is_local'=> $vit->is_local);
									$productData[$index]['details']['condition'][$c->value]['seller_id']= (isset($c->seller_id))?$c->seller_id:"";
								}
							}
						}
						if(empty($variant) && empty($variant_img_thumb)){
							$productData[$index]['details']['condition'][$c->value]['price'] = (isset($c->price))?$c->price:"";
							$productData[$index]['details']['condition'][$c->value]['id'] = (isset($c->id))?$c->id:"";
							$productData[$index]['details']['condition'][$c->value]['seller_product_id']= (isset($c->seller_product_id))?$c->seller_product_id:"";
							if(isset($c->pv_id)){
								$productData[$index]['details']['condition'][$c->value]['pv_id'] = $c->pv_id;
							}
							$productData[$index]['details']['condition'][$c->value]['seller_id']= (isset($c->seller_id))?$c->seller_id:"";
						}
					}
				}
			}
			$productData[$index]['min_price'] = $p['min_price'];
			$productData[$index]['max_price'] = $p['max_price'];
			$productData[$index]['product_link'] = (isset($p['product_link']))?strtolower(implode('-',explode(',',$p['product_link']))):$p['product_name'];
			$productData[$index]['tempCondition'] = "";
			$productData[$index]['otherSellers'] = $otherSeller;
			$productData[$index]['store_name'] = $p['store_name'];
			$productData[$index]['store_address'] = isset($p['store_address'])?$p['store_address']:"";
			if(isset($p['pv_id'])){
				$productData[$index]['pv_id'] = $p['pv_id'];
			}
			$tempCondition = array_count_values($tempCondition);
			$ti = 1;
			$li = count($condition);
			foreach($tempCondition as $key=>$value){
				if($value > 1){
					$productData[$index]['printTempCondition'] = 1;
				}
				if($ti > 1){
					$productData[$index]['tempCondition'] .= '& '.$key;
				}else if($li == $ti){
					$productData[$index]['tempCondition'] .= $key;
				}else{
					$productData[$index]['tempCondition'] .= $key.'&nbsp;';
				}
				$ti++;
			}
			if(!isset($productData[$index]['printTempCondition'])){
				$productData[$index]['printTempCondition'] = (count($tempCondition) > 1)?1:0;
			}
			$productData[$index]['cleanUrl'] = urlClean($p['product_name']);
			$productData[$index]['tempCondition'] = (count($tempCondition) > 1)?($li-1).'&nbsp;'.$productData[$index]['tempCondition']:$productData[$index]['tempCondition'];
			$i++;
		}
		return $productData;
	}

	public function forntCategoryData($where){
		$query = $this->db->select('category_id,
		parent_category_id,
		case when( CHAR_LENGTH(category_name) > 20 ) THEN CONCAT(LEFT(category_name, 20), "...") else category_name end AS category_name,
		 category_image,
		 category_link,
		 slug,
		 is_private,
		 is_active')->from(DBPREFIX.'_categories')->where($where);
		$this->db->order_by("position");
		$query = $this->db->get();
		$result['numRows'] = $query->num_rows();
		$result['result'] = $query->result();
		return $result ;
	}

	public function searchStore($id = '',$term=''){
		$data = array('status'=>0, 'message'=>'No Product found');
		if($term != ''){
			$this->db->select('ss.store_id, ss.store_name, CONCAT(user.firstname, " ",user.lastname) AS name');
			$this->db->from(DBPREFIX.'_seller_store as ss');
			$this->db->join(DBPREFIX.'_users as user','ss.seller_id = user.userid');
			$this->db->where('((ss.store_name LIKE "'.$term.'%") OR (CONCAT(user.firstname, " " ,user.lastname) LIKE "'.$term.'%"))');
						$query = $this->db->get();
						$numRows = $query->num_rows();
						$result = $query->result();
						return $result ;
					}
		}

	public function searchProduct($id ='' ,$term = '',$isSearch=false,$is_admin = false,$user_id=""){
		if($term !=""){
			$term = urldecode($term);
		}
		$data = array('status'=>0, 'message'=>'No Product found');
		$this->db->select('product.product_id, product.product_name');
		$this->db->from(DBPREFIX.'_product as product')
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"');
		if($id != '')
			$this->db->where('product_id',$id);
		if($isSearch){
			$this->db->where('((product.product_name LIKE "%'.stripslashes($term).'") OR (product.product_name LIKE "%'.$term.'%")
						OR (product.product_name LIKE "'.$term.'%"))');
		}
		if($is_admin){
			$this->db->where('product.is_active','1');
		}
		$this->db->group_by('product.product_id');
		$query = $this->db->get();
		// echo"<pre>".$this->db->last_query(); die();
		$numRows = $query->num_rows();
		$result = $query->result();
		//GET UNAPPROVED PRODUCTS ALSO ONLY FOR THE CREATED USER..
		$this->db->select('product.product_id, product.product_name');
		$this->db->from(DBPREFIX.'_product as product')
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"');
		$this->db->where('`product`.`created_id` = "'.$user_id.'" AND product.is_active = "0"');
		$this->db->where('((product.product_name LIKE "%'.stripslashes($term).'") OR (product.product_name LIKE "%'.$term.'%")
						OR (product.product_name LIKE "'.$term.'%"))');
		$query2 = $this->db->get();
		$numRows2 = $query2->num_rows();
		$result2 = $query2->result();
		$result = array_merge($result, $result2);
		return $result;
	}

	// public function filter_data($result){
	// 	foreach($result as $key => $data){
	// 		if($data->created_id != $this->session->userdata("userid") && $data->is_active == "0")
	// 		unset($result[$key]);
	// 	}
	// 	return $result;
	// }

	public function searchProductForAccessories($accessory_id ='' ,$product_id = "",$term = ''){
		if($term !=""){
			$term = urldecode($term);
		}
		$this->db->select('product.product_id, product.product_name,')->from(DBPREFIX.'_product as product')
		->join("tbl_brands AS b", "b.brand_id = product.brand_id AND b.blocked = '0'")
		->join("tbl_product_inventory pin", "pin.product_id = product.product_id  AND (pin.quantity - pin.sell_quantity) > '0'");

		if($product_id){
			$this->db->where_not_in("product.product_id",$product_id);
		}
		if($accessory_id){
			$this->db->where_not_in("product.product_id",$accessory_id);
		}
		if($term){
			$this->db->where('((product.product_name LIKE "%'.stripslashes($term).'") OR (product.product_name LIKE "%'.$term.'%") OR (product.product_name LIKE "'.$term.'%"))');
		}
		$this->db->where("product.is_active = '1'");
		$this->db->group_by('product.product_id');
		$result = $this->db->get()->result();
		// echo"<pre>".$this->db->last_query(); die();
		return $result ;
	}

	public function getVariants($variants,$select="")
	{
		$ret = array();
		if($select == ""){
			$select = 'v_id,v.v_title, vc.v_cat_title';
		}
		$query = $this->db->select($select)->from(DBPREFIX.'_variant as v')->join(DBPREFIX.'_variant_category as vc', 'v.v_cat_id = vc.v_cat_id')->where_in('v_id ', $variants)->get();
		if($query->num_rows() > 0){
			$ret = $query->result();
		}
		return $ret;
	}

	function cartVariantFormat($variants)
	{
		$ret = array();
		foreach($variants as $variant){
			$ret[$variant->v_cat_title] = $variant->v_title;
		}
		return $ret;
	}

	public function forntBrandData($where){
		$query = $this->db->select('*')
						  ->from(DBPREFIX.'_brands')
						  ->where($where)
						  ->where('display_status', '1');
		$query = $this->db->get();
		$numRows = $query->num_rows();
		$result = $query->result();
		return $result ;
	}

	public function updateProductStauts($sp_id,$column,$value){
		$result= 0;
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$data = array($column=>$value,'updated_date'=>$date_utc);
		$this->db->where('product_id',$sp_id);
		$res = $this->db->update(DBPREFIX."_product",$data);
		if($res){
			$result = 1;
		}
		return $result;
	}

	public function getSaveForLater($pv_id){
		$this->db->select("*");
		$this->db->from(DBPREFIX."_save_for_later");
		$this->db->where("pv_id",$pv_id);
		$query = $this->db->get();
		$return = $query->result();
		return $return ;
	}

	public function getData($table_name, $select="*", $where="",$where_in="0",$group_by="",$order_by="",$join="",$limit="",$whereExtra="",$offset="",$anOtherWhere="",$isSingle=false,$joinType=array()){
		if(!empty($where)){
			if($where_in !="0" && $where_in !=""){
				if($whereExtra !=""){
					$this->db->where_in($where_in,$where,FALSE,NULL);
				}else{
					$this->db->where_in($where_in,$where);
				}
			}else{
				$this->db->where($where);
			}
			if($anOtherWhere !=""){
				$this->db->where($anOtherWhere);
			}
		}
		if($group_by !=""){
			$this->db->group_by($group_by);
		}
		if($order_by !=""){
			$this->db->order_by($order_by);
		}
		if($limit !=""){
			if($offset){
				$this->db->limit($offset,$limit);
			}else{
				$this->db->limit($limit);
			}
		}
		if($join !=""){
			foreach($join as $key=>$value){
				if(!empty($joinType) && isset($joinType[$key])){
					$this->db->join($key,$value,$joinType[$key]);
				}else{
					$this->db->join($key,$value);
				}
			}
		}
		$this->db->select($select)->from($table_name);
		$query = $this->db->get();
		//echo $this->db->last_query()."<hr></br>";die();
		if($isSingle){
			$return = $query->row();
		}else{
			$return = $query->result();
		}
		return $return ;
	}

	public function getProductForInventory($seller_id, $product_id){
		$this->db->select('p.product_id, sp.sp_id, p.product_name, GROUP_CONCAT(sp.condition_id) AS condition_id,sp.video_link, sp.seller_id')
				 ->from(DBPREFIX.'_product p')
				 ->join(DBPREFIX.'_seller_product sp','sp.product_id = p.product_id')
				 ->where(array('p.product_id'=>$product_id,'sp.seller_id'=>$seller_id));
		$query = $this->db->get();
		$return = $query->row();
		return $return;
	}

	public function searchProductBackend($id ='' ,$term = '',$seller_id="",$isSearch=false){
		if($term !=""){
			$term = urldecode($term);
		}
		$data = array('status'=>0, 'message'=>'No Product found');
		$this->db->select('product.product_id, product.product_name,');
		$this->db->from(DBPREFIX.'_product as product');
		if($id != '')
			$this->db->where('product_id',$id);
		if($isSearch){
			$this->db->where('((product.product_name LIKE "'.stripslashes($term).'%") OR (product.product_name LIKE "%'.$term.'%")
						OR (product.product_name LIKE "'.$term.'%") OR (product.product_name LIKE "%'.$term.'%"))');
		}
		if($isSearch){
			$this->db->where('product.is_active','1');
		}
		if($seller_id !=""){
			$this->db->join(DBPREFIX.'_seller_product sp' ,'sp.product_id = product.product_id');
			$this->db->where('sp.seller_id',$seller_id);
			$this->db->group_by('product.product_id');
		}
		$query = $this->db->get();
		$numRows = $query->num_rows();
		$result = $query->result();
		return $result ;
	}

	public function productDetails($product_id,$country_id,$product_variant_id = "",$user_id="",$select="",$condition_id="", $from = ""){
		if($select == ""){
			$select = "sp.sp_id,ss.store_name,ss.store_id,GROUP_CONCAT(DISTINCT sp.`sp_id`) AS seller_product_id,(COUNT(DISTINCT sp.`seller_id`) - 1) AS total_seller,pin.`price`,pin.`condition_id`, pin.`seller_id`,`product`.`product_id`,`c`.`category_id`,`c`.`category_name`,`b`.`brand_id`,`b`.`brand_name`,`product`.`upc_code`,product.sku_code,`product`.`product_name`,`product`.`product_description`,product.short_description,`product`.`is_active`,pin.product_variant_id AS pv_id,(pin.quantity - pin.sell_quantity) as quantity,sp.seller_sku,sp.shipping_ids, d.value as discount_value, d.valid_from as discount_start, d.valid_to as discount_end, d.type as discount_type, product.slug, sp.seller_product_description,warr.id as warranty_id,warr.warranty,pin.shipping_ids as inventory_shipping";
		}
		if($from != ""){
			$select .= ", GROUP_CONCAT(DISTINCT(v.v_title)) as variant, k.keywords, pcon.condition_name";
		}
		if($product_variant_id != ""){
			$this->db->where('pin.`product_variant_id`',$product_variant_id);
		}
		if($country_id !=""){
			//$this->db->where('(pr.country_id = "'.$country_id.'" OR pr.country_id = "0")',NULL,false);
		}
		if($product_id !=""){
			$this->db->where('product.product_id' ,$product_id);
		}
		if($user_id !=""){
			$select .= ",IF(w.wish_id, 1,0) AS already_saved";
		}
		if($condition_id !=""){
			$this->db->where('pin.condition_id' ,$condition_id);
		}
		$this->db->select($select)
		->from(DBPREFIX.'_product AS product')
		//->join(DBPREFIX.'_product_regions as pr','pr.`product_id` = product.product_id')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories c','c.`category_id` = pro_cat.category_id')
		->join(DBPREFIX.'_brands b','b.`brand_id` = product.`brand_id` AND b.blocked = "0"')
		->join(DBPREFIX.'_seller_product sp','sp.product_id = product.product_id',"LEFT")
		->join(DBPREFIX.'_product_inventory pin','pin.`seller_product_id` = sp.`sp_id` AND (pin.quantity - pin.sell_quantity) > 0 AND pin.seller_id !=1',"LEFT")
		->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id` AND d.display_status = "1"',"LEFT")
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pin.seller_id AND ss.is_approve = "1" AND ss.is_active = "1"')
		->join(DBPREFIX.'_warranty warr','warr.`id` = pin.warranty_id',"LEFT")
		//->where(array('sp.seller_id !='=>'1','sp.approve'=>"1"))
		->group_by('product.`product_id`');
		if($from != ""){
			$this->db->join(DBPREFIX.'_product_variant pv','pv.product_id = product.product_id AND pv.sp_id = sp.sp_id AND pv.condition_id = sp.condition_id')
			->join(DBPREFIX.'_variant v','FIND_IN_SET(v.v_id, pv.variant_group)',"LEFT",false)
			->join(DBPREFIX.'_product_conditions pcon','pcon.condition_id = sp.condition_id')
			->join(DBPREFIX.'_meta_keyword k','k.product_id = product.product_id',"LEFT")
			->group_by('pv.`pv_id`');
		}
		$this->db->order_by('sp.condition_id,pin.price');
		if($user_id != ""){
			$this->db->join(DBPREFIX.'_wishlist as w',' w.product_id = product.product_id  AND  w.user_id="'.$user_id.'"','LEFT')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND pm.is_cover','LEFT');
		}
		$product = $this->db->get();
		//echo"<pre>"; print_r($this->db->last_query());die();
		$data['productDataRows'] = $product->num_rows();
		$data['productData'] = $product->row();
		return $data;
	 }

	public function getProductVariantData($product_id,$condition_id="",$variant="",$variant_is_array =0,$condition="",$limit,$shipping_id,$selectedVariant="",$select=""){
		$ship="";
		if($variant !="" && $variant_is_array == 0){
			$this->db->where('pv.variant_group',$variant);
		}else if($variant !="" && $variant_is_array == 1){
			$this->db->where_in('spv.v_id',$selectedVariant,FALSE);
		}
		if($condition_id !=""){
			if($condition ==""){
				$this->db->where_in('pv.condition_id',$condition_id);
			}else{
				$where = "(SELECT MIN(`pv`.`condition_id`) FROM tbl_product_variant pv JOIN tbl_product_inventory pin ON pin.product_variant_id = pv.pv_id AND (pin.quantity - pin.sell_quantity) > 0 AND pin.approve='1' LEFT JOIN `tbl_seller_product_variant` `spv`
        ON `spv`.`pv_id` = `pv`.`pv_id` JOIN `tbl_users` `u` ON `u`.`userid` = `pv`.`seller_id` JOIN `tbl_seller_store` `ss` ON `ss`.`seller_id` = `pv`.`seller_id` WHERE `pv`.`product_id` = '".$product_id."' AND `pv`.`quantity` > 0 AND `spv`.`v_id` IN (".$selectedVariant.")) ";
				$this->db->where('pv.condition_id ='.$where,NULL,FALSE);
			}
		}
		if($shipping_id != ""){
			$ship = ' AND FIND_IN_SET('.$shipping_id.',pin.shipping_ids)';
		}
		if($select == ""){
			$select = 'pv.pv_id,CONCAT(u.firstname, " ", u.lastname) AS seller_name,ss.store_name,ss.store_logo,ss.store_id,pv.product_id,pv.seller_id,pv.sp_id,pv.seller_sku,pv.condition_id,pv.variant_group,p.product_name,pin.price,(pin.quantity ) as quantity,d.value as discount_value,d.type as discount_type,pm.iv_link as image_link,pm.thumbnail AS image_thumb,pm.is_local,pin.shipping_ids as inventory_shipping,sp.seller_product_description,d.valid_from,
			d.valid_to,p.slug,sp.shipping_ids,warr.id as warranty_id,warr.warranty';
		}
		$this->db->select($select)
		->from(DBPREFIX.'_product_variant pv')
		->join(DBPREFIX.'_seller_product_variant spv','spv.pv_id = pv.pv_id','LEFT')
		->join(DBPREFIX.'_product p','pv.product_id = p.product_id','LEFT')
		->join(DBPREFIX."_brands AS b",'b.brand_id = p.brand_id AND b.blocked = "0"')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = p.product_id')
		->join(DBPREFIX.'_categories AS c','c.`category_id` = pro_cat.category_id')
		->join(DBPREFIX.'_users u','u.userid = pv.seller_id','LEFT')
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pv.seller_id','LEFT')
		->join(DBPREFIX.'_seller_product sp','sp.sp_id = pv.sp_id'.$ship)
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = pv.product_id AND (pm.sp_id = sp.sp_id) AND pm.is_cover','LEFT')
		->join(DBPREFIX.'_product_inventory AS pin','pin.product_variant_id = pv.pv_id')
		->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id` AND d.display_status = "1"',"LEFT")
		->join(DBPREFIX.'_warranty warr','warr.`id` = pin.warranty_id',"LEFT")
		->where('pv.product_id',$product_id)->where('(pin.quantity - pin.sell_quantity) >',0)->where('pin.approve',"1")
		->group_by('pv.pv_id')
		->order_by('pv.price');
		if($limit  != ""){
			$this->db->limit(1);
		}
		$query = $this->db->get();
		// echo "<pre>".$this->db->last_query();die();
		$data['gpvdRows'] = $query->num_rows();
		$data['gpvd'] = $query->result();
		return $data;
	}

	public function allProductVariantByConditionId($product_id, $condition_id,$variant=""){
		if(!is_array($variant) && $variant !=""){
			$variant = explode(",",$variant);
		}
		if($variant !="" && is_array($variant)){
			$where = "(";
			foreach($variant as $v){
				$where .= "FIND_IN_SET('".$v."', pv.variant_group) OR ";
			}
			$where = rtrim($where,"OR ").")";
			$this->db->where($where);
		}
		$this->db->select('DISTINCT(spv.v_id)')
				 ->from(DBPREFIX.'_product_variant pv')
				 ->join(DBPREFIX.'_seller_product_variant spv','spv.`pv_id` = pv.`pv_id`')
				 ->join(DBPREFIX.'_product_inventory pin','pin.`product_variant_id` = pv.`pv_id`')
				 ->where(array('pv.product_id'=>$product_id,'pv.condition_id'=>$condition_id,'(pin.quantity - pin.sell_quantity) >'=>0,"pin.approve"=>"1"));
				 $query = $this->db->get();
		$data= $query->result();
		//echo $this->db->last_query();die();
		return $data;
	}

	public function inventoryPipline2($search = "", $offset = "", $length = "", $user_id = "", $usertype = "", $prd_id = "",$approve=""){
		//echo $length;
		if($user_id != ""){
			$this->datatables->where('pi.seller_id', $user_id);
		}
		if($approve == ""){
			$this->datatables->where("pi.approve != '3'",NULL);
		}else if($approve == "delete"){
			$this->datatables->where("pi.approve","3");
		}else{
			$this->datatables->where("pi.is_warning","1");
		}
		$select = "";
		if($usertype == "1") {
			$select = "ss.store_name,";
		}
		$this->datatables->SELECT("inventory_id as id, pi.`created_date`, pi.updated_date ,".$select."  p.`product_name`, pc.`condition_name`, GROUP_CONCAT(DISTINCT vc.v_cat_title, ':', v.v_title) AS variant, (pi.quantity - pi.sell_quantity) as quantity, pi.sell_quantity,pi.`price`, discount.id AS discount_id,
										CASE
											WHEN discount.value IS NULL THEN 'No'
											ELSE concat(discount.value,'%',' - expiry date: ',discount.valid_to)
										END")
					->FROM("tbl_product_inventory as `pi`")
					->JOIN(DBPREFIX.'_product_variant AS pv','pi.`product_variant_id` = pv.`pv_id`','left')
					->JOIN(DBPREFIX.'_policies AS discount','pi.`discount` = discount.`id`','left')
					->JOIN(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
					->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
					->join(DBPREFIX."_product_conditions AS pc",'pc.condition_id =  pi.condition_id','LEFT')
					->join(DBPREFIX.'_seller_store as ss','ss.seller_id = pi.seller_id')
					->join(DBPREFIX.'_product as p','p.product_id = pi.product_id');
					if($prd_id){
						$this->datatables->where("pi.product_id", $prd_id);
					}
					$this->datatables->group_by("pv.pv_id");
					$this->datatables->LIKE('p.product_name', $search);
					$this->db->order_by('pi.inventory_id','DESC');
			 if($length != -1){
			 	$this->db->limit($length,  $offset);
			 }
	}

	public function inventoryPiplineCount($search = "", $user_id = "", $prd_id = ""){
		if($user_id != ""){
			$this->db->where('pi.seller_id', $user_id);
		}
		$select = "";
		$this->db->SELECT("inventory_id as id")
					->FROM("tbl_product_inventory as `pi`")
					->JOIN(DBPREFIX.'_product_variant AS pv','pi.`product_variant_id` = pv.`pv_id`','left')
					->JOIN(DBPREFIX.'_policies AS discount','pi.`discount` = discount.`id`','left')
					->JOIN(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
					->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
					->join(DBPREFIX."_product_conditions AS pc",'pc.condition_id =  pi.condition_id','LEFT')
					->join(DBPREFIX.'_seller_store as ss','ss.seller_id = pi.seller_id');
					if($prd_id){
						$this->db->where("pi.product_id", $prd_id);
					}
					$this->db->group_by("pv.pv_id");
					$this->db->LIKE('product_name', $search);
					$this->db->order_by('pi.inventory_id','DESC');
					$query = $this->db->get()->num_rows();
					return $query;
	}

	public function getProductByProductVariantID($product_id,$select=""){
		$response = array('status'=>0, 'data'=>array());
		if($select == ""){
			$select = 'product.product_id, product.upc_code, product.product_name,w.wish_id as already_saved, pv.pv_id, pv.variant_group, ss.store_name, sp.sp_id,sp.shipping_ids,pin.shipping_ids as inventory_shipping, sp.seller_sku, sp.sp_id AS seller_product_id, pm.thumbnail AS product_image, pin.price,pin.warehouse_id, pin.condition_id, pin.seller_id,c.condition_name AS condition,pm.is_local, (pin.quantity - pin.sell_quantity) as quantity,pin.sell_quantity, d.id as discount_id, d.value as discount_value,d.type as discount_type, d.valid_from as valid_from, d.valid_to as valid_to, product.slug, GROUP_CONCAT(DISTINCT pc.category_id) as category_id,pin.hubx_id,ss.is_tax';
		}
		$this->db->select($select)
				 ->from(DBPREFIX.'_product_variant AS pv')
				 ->join(DBPREFIX.'_product AS product','pv.product_id = product.product_id')
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND pm.is_cover','LEFT')
				 ->join(DBPREFIX.'_seller_product AS  sp','pv.sp_id = sp.sp_id')
				 ->join(DBPREFIX.'_seller_store AS  ss','sp.seller_id = ss.seller_id')
				 ->join(DBPREFIX.'_product_inventory AS pin','pv.pv_id = pin.product_variant_id')
				 ->join(DBPREFIX.'_wishlist AS w','w.product_id = pin.product_id','left')
				 ->join(DBPREFIX.'_product_conditions AS c','pv.condition_id = c.condition_id')
				 ->join(DBPREFIX.'_product_category AS pc','product.product_id = pc.product_id')
				 ->join(DBPREFIX.'_policies AS d','pin.discount = d.id AND d.display_status = "1"','left')
				 ->where(array('pv.pv_id'=>$product_id,'pin.approve'=>"1",'pin.seller_id !='=>"1",'pin.product_variant_id' =>$product_id ))
				 ->group_by('pv.pv_id');
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query->num_rows() > 0){
			$response['status'] = 1;
			$response['data'] = $query->row();
		}
		return $response;
	}

	public function checkProductForDiscountByProductVariantID($product_ids){
		$response = array('status'=>0, 'data'=>array());
		$this->db->select('prod.product_variant_id as pv_id,ps.shipping_id,ps.title as shipping_title,ps.price as shipping_price, prod.product_name as name, prod.price, (prod.quantity - prod.sell_quantity) as qty , disc.type as discount_type, disc.value as discount_value, disc.valid_from as discount_from, disc.valid_to as discount_till, prod.price AS original, ps.free_after')
				 ->from(DBPREFIX.'_product_inventory AS prod')
				 ->join(DBPREFIX.'_policies AS disc','prod.discount = disc.id AND disc.display_status = "1"','left')
				 ->join(DBPREFIX.'_seller_product AS sp','sp.sp_id = prod.seller_product_id')
				 ->join(DBPREFIX.'_product_shipping AS ps','ps.shipping_id = (SELECT pps.shipping_id FROM tbl_product_shipping pps WHERE FIND_IN_SET(pps.shipping_id, IF(prod.shipping_ids,prod.shipping_ids,sp.shipping_ids)) ORDER BY pps.price LIMIT 1)',FALSE)
				 ->where(array('prod.approve'=>"1", 'prod.seller_id !='=>"1", "ps.is_active = "=>"1"))
				 ->where_in('prod.product_variant_id', $product_ids)
				 ->group_by('prod.product_variant_id');
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query->num_rows() > 0){
			$response['status'] = 1;
			$response['data'] = $query->result();
		}
		return $response;
	}

	public function shipping_charges($id)
	{
		$response = array('status' => 0, 'data' => array());
		$this->db->select("ship.*")
					->from(DBPREFIX."_product_shipping AS ship")
					->where_in("ship.shipping_id", $id);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$response = $query->result();
		}
		return $response;
	}

	public function getProductImage($product_id){
		$response = array('status'=>0, 'data'=>array());
		$query = $this->db->select('iv_link,is_local')->from(DBPREFIX.'_product_media')->where('product_id',$product_id)->get();
		if($query->num_rows() > 0){
			$response['status'] = 1;
			$response['data'] = $query->row();
		}
		return $response;
	}

	public function productImages($product_id){
		$this->db->select('thumbnail, is_local')
				 ->from(DBPREFIX.'_product_media')
				 ->where('product_id', $product_id);
		$query = $this->db->get();
		$data= $query->result();
		return $data;
	}

	public function primary_image($data, $original, $product_id, $thumb_data, $iv_link_data){
		$insert_data = array(
			'is_primary' => $original[1],
			'iv_link' => $iv_link_data,
			'thumbnail' => $thumb_data

		);
		$this->db->where('product_id', $product_id);
		if($this->db->update(DBPREFIX.'_product_media', $insert_data)){
			return true;
		} else {
			return false;
		}
	}

	public function primary_image_singleimage($data, $product_id){
		$insert_data = array(
			'is_primary' => $data[0],
		);
		$this->db->where('product_id', $product_id)->update(DBPREFIX.'_product_media', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	public function product_delete($prod_id){
		$this->db->where('product_id', $prod_id)->delete(DBPREFIX.'_product_inventory');
		$this->db->where('product_id', $prod_id)->delete(DBPREFIX.'_product_regions');
		$this->db->where('product_id', $prod_id)->delete(DBPREFIX.'_seller_product');
		$this->db->where('product_id', $prod_id)->delete(DBPREFIX.'_product_variant');
		$this->db->where('product_id', $prod_id)->delete(DBPREFIX.'_product');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function get_pv_id($id,$condition_id=""){
		$this->db->select('pin.product_variant_id  AS pv_id')
				 ->from(DBPREFIX.'_product_inventory AS pin')
				 ->join(DBPREFIX."_users AS users",'pin.seller_id = users.userid AND users.is_block="0" AND users.is_active="1"')
				 ->join(DBPREFIX."_seller_store ss", "ss.seller_id = pin.seller_id AND ss.is_approve = '1' AND ss.is_active = '1'")
				 ->where('pin.product_id', $id)
				 ->where('(pin.quantity - pin.sell_quantity) > ', 0)
				 ->where('pin.seller_id !=', "1")
				 ->where('pin.approve ', "1")
				 ->order_by('pin.condition_id,pin.price')
				 ->group_by('pin.price');
				 $this->db->limit(1);
		if($condition_id !=""){
			$this->db->where("pin.condition_id",$condition_id);
		}
		$query=$this->db->get()->row();
		// echo"<pre>"; print_r($this->db->last_query()); die();
		$data['pv_id']= ($query)?$query->pv_id:"";
		$this->db->select('COUNT(DISTINCT pin.`seller_id`) - 1 AS total_seller')
				 ->from(DBPREFIX.'_product_inventory AS pin')
				 ->join(DBPREFIX."_users AS users",'pin.seller_id = users.userid AND users.is_block="0" AND users.is_active="1"')
				 ->join(DBPREFIX."_seller_store ss", "ss.seller_id = pin.seller_id AND ss.is_approve = '1' AND ss.is_active = '1'")
				 ->where('pin.product_id', $id)
				 ->where('pin.seller_id !=',1)
				 ->where('(pin.quantity - pin.sell_quantity) >',0)
				 ->where('pin.approve',"1");
		$query2 = $this->db->get()->row();
		$data['total_seller']= (isset($query2->total_seller) && $query2->total_seller > 0)?$query2->total_seller:"";
		return $data;
	}

	public function get_seller_ids($id,$select='pin.seller_id')
	{
		$this->db->select($select)
				 ->from(DBPREFIX.'_product_inventory AS pin')
				 ->where('pin.product_id', $id)
				 ->where('pin.seller_id !=',1)
				 ->where('(pin.quantity - pin.sell_quantity) >',0)
				 ->where('pin.approve',"1")->group_by("pin.seller_id");
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}
		else{
			return FALSE;
		}
		return $query2;
	}

	public function inventory_update($id){
		$this->db->select("quantity, price,discount, product_variant_id AS pvId, sell_quantity")
				 ->from("tbl_product_inventory")
				 ->where('inventory_id', $id);
		$query = $this->db->get();
		$data= $query->row();
		return $data;
	}

	public function update_quantityandprice($data, $user_id, $usertype = ""){
		$inventory_id = $data['inventory_id'];
		unset($data['inventory_id']);
		if($usertype != "1" && $user_id !=""){
			$this->db->where("seller_id",$user_id);
		}
		$this->db->where('inventory_id', $inventory_id)->update(DBPREFIX.'_product_inventory', $data);
		if($usertype != "1" && $user_id !=""){
			$this->db->where("seller_id",$user_id)->where('pv_id',$inventory_id);
		}
		//unset($data['discount']);
		//$this->db->where('pv_id', $inventory_id)->update(DBPREFIX.'_product_variant', $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
		/*if($userid != "" && $usertype == "1"){
			$insert_data1 = array(
				'updated_date'	 =>$date,
				'quantity' =>  $data['Quantity'],
				'price' => $data['Price'],
				'discount' => $data['discount'],
			);
			$insert_data = array(
				'updated_date'	 =>$date,
				'quantity' =>  $data['Quantity'],
				'price' => $data['Price'],
			);
			 $this->db->where('inventory_id', $data['modal_id'])
			  		 ->update(DBPREFIX.'_product_inventory', $insert_data1);
			 $this->db->where('pv_id', $data['pvId'])
			 		 ->update(DBPREFIX.'_product_variant', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
		else if($userid != "" && $usertype != "1"){
			$insert_data = array(
				'updated_date'	 =>date("Y-m-d H:i:s",gmt_to_local(time(),"UP45")),
				'quantity' =>  $data['Quantity'],
				'price' => $data['Price']
			);
			$insert_data1 = array(
				'updated_date'	 =>date("Y-m-d H:i:s",gmt_to_local(time(),"UP45")),
				'quantity' =>  $data['Quantity'],
				'price' => $data['Price'],
				'discount' => $data['discount'],
			);
			$this->db->where('seller_id', $userid)
					 ->where('inventory_id', $data['modal_id'])
					 ->update(DBPREFIX.'_product_inventory', $insert_data1);
			$this->db->where('seller_id', $userid)
					 ->where('pv_id', $data['pvId'])
			 		 ->update(DBPREFIX.'_product_variant', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}*/
	}

	public function change_primary($id){
		$this->db->select('iv_link, is_local, thumbnail')
				 ->from(DBPREFIX.'_product_media')
				 ->where('product_id', $id);
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}

	function checkSellerReview($seller_id,$pdt){
		$where = array("seller_id"=>$seller_id,"product_id"=> $pdt);
		$this->db->select("seller_id")
				->from("tbl_product_inventory")
				->where($where);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	public function get_max_price($seller_id = "")
	{
		$this->db->select('MAX(pin.price) AS price')
				 ->from(DBPREFIX.'_product_inventory AS pin')
				 ->join(DBPREFIX."_seller_product AS sp",'sp.product_id = pin.product_id')
				 ->where('(pin.quantity - pin.sell_quantity) >', 0)
				 ->where('sp.is_active', '1')
				 ->where('sp.approve', '1');
		if($seller_id){
			$this->db->where("pin.seller_id",$seller_id);
		}
		$query = $this->db->get();
		$result = $query->row('price');
		return $result;
	}

	function saved_for_later($user_id,$pageing="0",$limit="", $catId = "",$select=""){
		if($pageing > 1){
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		if($catId != ""){
			$where = array('w.user_id'=>$user_id,' pin.approve'=>"1",'(pin.quantity - pin.sell_quantity) >'=>"0", 'w.category_id' => $catId);
		} else {
			$where = array('w.user_id'=>$user_id,' pin.approve'=>"1",'(pin.quantity - pin.sell_quantity) >'=>"0");
		}
		if($select == ""){
			$select = "w.wish_id,w.created_date,w.product_id,w.pv_id,p.product_name,sp.seller_id,sp.sp_id,p.short_description as seller_product_description,sp.condition_id,condition.condition_name,pm.thumbnail as is_primary_image,pm.is_local,pin.price, twc.category_name, p.slug";
		}
		$this->db->select($select)
		->from(DBPREFIX."_wishlist as w")
		->join(DBPREFIX."_product as p","p.product_id = w.product_id")
		->join(DBPREFIX."_brands AS b",'b.brand_id = p.brand_id AND b.blocked = "0"')
		->join(DBPREFIX."_wishlist_categoryname as twc","twc.id = w.category_id")
		->join(DBPREFIX."_product_inventory as pin","pin.product_id = w.product_id AND pin.product_variant_id = w.pv_id")
		->join(DBPREFIX."_seller_product as sp","sp.product_id = w.product_id AND sp.sp_id = pin.seller_product_id")
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.is_cover','LEFT')
		->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id` AND d.display_status = "1"',"LEFT")
		->join(DBPREFIX.'_product_reviews AS preview','preview.product_id = pin.product_id AND preview.pv_id = pin.product_variant_id','LEFT')
		->join(DBPREFIX.'_product_conditions AS condition', 'sp.condition_id = condition.condition_id')
		->WHERE($where)
		->WHERE("twc.is_delete", "0")
		->order_by('w.created_date', 'DESC')
		->group_by('sp.sp_id');
		$query = $this->db->get();
		// echo"<pre>".$this->db->last_query();die();
		$data['data'] = $query->result();
		return $data;
	}

	function count_saved($user_id, $catId = ""){
		if($catId == ""){
			$where = array('w.user_id'=>$user_id,' pin.approve'=>"1",'(pin.quantity - pin.sell_quantity) >'=>"0");
		} else {
			$where = array('w.user_id'=>$user_id,' pin.approve'=>"1",'(pin.quantity - pin.sell_quantity) >'=>"0", 'w.category_id' => $catId);
		}
		$this->db->select('w.wish_id AS wish_id')
		->from(DBPREFIX."_wishlist as w")
		->join(DBPREFIX."_product as p","p.product_id = w.product_id")
		->join(DBPREFIX."_product_inventory as pin","pin.product_id = w.product_id AND pin.product_variant_id = w.pv_id")
		->join(DBPREFIX."_seller_product as sp","sp.product_id = w.product_id AND sp.sp_id = pin.seller_product_id")
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND (pm.sp_id = sp.sp_id OR pm.condition_id = 1) AND pm.is_cover','LEFT')
		->WHERE($where)
		->order_by('w.created_date', 'DESC')
		->group_by('sp.sp_id');
		$query = $this->db->get();
		$data['data'] = $query->result();
		$data['total_rows'] =  $query->num_rows();
		return $data;
	}

	function delete_from_list($wish_id ="",$where = ""){
		if($wish_id){
			$where = array('wish_id'=>$wish_id);
		}
		$this->db->where($where)->delete(DBPREFIX.'_wishlist');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function toggle_tsp($id, $value){
		$insertData = array(
			'is_active' => $value
		);
		$this->db->where('sp_id', $id)->update(DBPREFIX.'_seller_product', $insertData);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function toggle_tspv($id, $value){
		$insertData = array(
			'is_active' => $value
		);
		$this->db->where('sp_id', $id)->update(DBPREFIX.'_seller_product_variant', $insertData);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function pending_Products($search = "", $request = "", $userid = ""){
		if($search ==""){
			$this->db->select("sp.seller_id,product.product_id, sp.sp_id, product.product_name, product.product_description, pm.is_image, pm.iv_link, pm.thumbnail, pm.is_primary, cat.category_name, b.brand_name,pc.`condition_name`, product.upc_code, sp.seller_sku, pin.price,sp.sell_quantity,sp.discount,sp.is_return,GROUP_CONCAT(v.v_title) AS v_title,users1.userid AS userid1, CONCAT(users1.firstname,' ',users1.lastname) AS name1,product.is_featured,sp.is_banner,pm.is_local, sp.is_active as active, sp.approve")
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id','LEFT')
			->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id','LEFT')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id')
			->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
			->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
			->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id','LEFT')
			->join(DBPREFIX."_users as users1",'sp.seller_id = users1.userid','LEFT')
			->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id')
			->where('sp.approve', '0')
			->group_by('product.product_id,sp.seller_id,pc.condition_name')->order_by('sp.sp_id','DESC ');
			if($userid){
				$this->db->where('sp.seller_id',$userid);
			}
			if($request['start'] != 0){
				$this->db->limit($request['length'],$request['start']);
			}
			$query = $this->db->get();
		} else {
			$search = trim($search);
			$this->db->select("sp.seller_id,product.product_id, sp.sp_id, product.product_name, product.product_description, pm.is_image, pm.iv_link, pm.thumbnail, pm.is_primary, cat.category_name, b.brand_name,pc.`condition_name`, product.upc_code, sp.seller_sku, pin.price,sp.sell_quantity,sp.discount,sp.is_return,GROUP_CONCAT(v.v_title) AS v_title,users1.userid AS userid1, CONCAT(users1.firstname,' ',users1.lastname) AS name1,product.is_featured,sp.is_banner,pm.is_local, sp.is_active as active, sp.approve")
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id','LEFT')
			->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id','LEFT')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id')
			->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
			->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
			->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id','LEFT')
			->join(DBPREFIX."_users as users1",'sp.seller_id = users1.userid','LEFT')
			->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id')
			->where('sp.approve', '0')
			->where('(((product.product_name LIKE "'.stripslashes($search).'%") OR (product.product_name LIKE "%'.$search.'%")) OR ((cat.category_name LIKE "'.stripslashes($search).'%") OR (cat.category_name LIKE "%'.$search.'%")) OR ((b.brand_name LIKE "'.stripslashes($search).'%") OR (b.brand_name LIKE "%'.$search.'%")))')
			->group_by('product.product_id,sp.seller_id,pc.condition_name')->order_by('sp.sp_id','DESC ');
			if($userid){
				$this->db->where('sp.seller_id',$userid);
			}
			if($request['start'] != 0){
				$this->db->limit($request['length'],$request['start']);
			}
			$query = $this->db->get();
		}
		$data['draw'] = $request['draw'];
		$data['data'] = $query->result();
		if($request['start'] != 0){
			$data['recordsTotal']= $request['recordsTotal'];
  			$data['recordsFiltered']= $request['recordsTotal'];
		}else{
			$data['recordsTotal']= $query->num_rows();
  			$data['recordsFiltered']= $query->num_rows();
		}
		return $data;
	}

	public function product_approve($ids,$productid,$sellerid){
		$this->db->trans_start();
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$insertData = array(
			'approve' => "1",
			'approved_date' => $date_utc
		);
		$this->db->where("product_id",$productid)->update(DBPREFIX.'_product', array('is_active'=>"1"));
		$this->db->where("product_id",$productid)->update(DBPREFIX.'_product_inventory', $insertData);
		$insertData['is_active'] = "1";
		$this->db->where("product_id",$productid)->update(DBPREFIX.'_seller_product', $insertData);
		$this->db->trans_complete(); # Completing transaction
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return FALSE;
		} else {
			$this->db->trans_commit();
			return true;
		}
	}

	public function product_decline($productid,$sellerid,$reason){
		$insertData = array(
			'is_declined' => "1",
			'declined_reason' => $reason
		);
		$this->db->where("product_id",$productid)->where("created_id",$sellerid)->update(DBPREFIX.'_product', $insertData);
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function already_saved($userid, $product_id,$product_variant_id=""){
		$this->db->select('wish_id')
				->from(DBPREFIX.'_wishlist')
				->where('user_id',$userid)
				->where('product_id',$product_id);
				if($product_variant_id){
					 $this->db->where('pv_id', $product_variant_id);
				}
		$result = $this->db->get();
		if($result && $result->num_rows()>0){
			return true;
		} else {
			return false;
		}
	}

	public function get_product_data($product_id)
	{
		$this->db->select('p.product_id,p.product_name, p.product_description,mk.keywords,pin.seller_product_id, pin.product_variant_id')
		->from(DBPREFIX.'_product as p')
		->join(DBPREFIX."_product_inventory AS pin",'p.product_id = pin.product_id')
		->join(DBPREFIX."_meta_keyword AS mk",'mk.product_id = p.product_id','LEFT')
		->where('p.product_id', $product_id);
		$query= $this->db->get();
		$data = $query->row();
		return $data;
	}

	public function save_edit_product($product_id,$product_name,$desc,$key){
		$this->db->select('keyword_id')
				 ->from(DBPREFIX.'_meta_keyword')
				 ->where('product_id',$product_id);
		$result=$this->db->get();
		if($result->num_rows()>0){
			$this->db->where('product_id', $product_id)->update(DBPREFIX."_meta_keyword", $key);
		} else {
			$this->db->insert(DBPREFIX.'_meta_keyword', $key);
		}
		$data=array('product_name'=>$product_name,'product_description'=>$desc);
		$this->db->where('product_id', $product_id)->update(DBPREFIX."_product", $data);
		$this->db->where('product_id', $product_id)->update(DBPREFIX."_product_inventory", array('product_name'=>$product_name));
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getNumberofPendingproducts($where){
		$this->db->select('is_active')
		->from(DBPREFIX.'_product')
		->where($where);
		$this->db->group_by('product_id');
		$result=$this->db->get();
		return $result->num_rows();
	}

	public function getProducts($product_id="", $created_id="", $condition_id="", $variant_id="",$pageing="0",$limit="",$order="",$where="",$group_by="",$select="", $seller_product_id = "", $region="", $keywords="")
	{
		if($pageing > 1){
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		$this->db->query('SET SESSION group_concat_max_len = 1000000');
		$userid = $this->session->userdata('userid');
		if($created_id){
			$this->db->where('product.created_id',$created_id);
		}
		if($product_id){
			if(is_array($product_id)){
				$product_id = implode(',',$product_id);
				$this->db->where_in('product.product_id',$product_id);
			}else{
				$this->db->where('product.product_id',$product_id);
			}
		}
		if($where){
			$this->db->where($where);
		}
		if($region !=""){
			$this->db->where('(pr.country_id='.$region.' OR pr.country_id = "0")',NULL,FALSE);
		}
		if($group_by == ""){
			$group_by="sp.seller_id";
		}
		if($order == ""){
			$order = "sp.sp_id DESC";
		}
		if($condition_id != ''){
			if(is_array($condition_id)){
				$condition_id = implode(',',$condition_id);
				$this->db->where_in('sp.condition_id',$condition_id,false);
			}else{
				$this->db->where('sp.condition_id',$condition_id);
			}
		}
		if($seller_product_id !=""){
			$this->db->where('sp.sp_id',$seller_product_id);
		}
		if($select == ""){
			$select ='cat.category_id,cat.category_name,product.product_id, sp.sp_id AS seller_product_id, sp.approve,sp.shipping_ids, product.product_name,product.product_description, pm.thumbnail AS product_image,pm.is_local AS is_local_product_img,b.brand_name as brand_name,b.brand_id, GROUP_CONCAT(DISTINCT pc.condition_name) conditions , pin.price,MIN(pin.price) AS min_price, MAX(pin.price) AS max_price, GROUP_CONCAT(DISTINCT vc.v_cat_title,":",v.v_title) AS variant, sp.seller_id,pv.pv_id,pm.thumbnail as is_primary';
		}
		$this->db->select($select, false)
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id','LEFT')
			->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id','LEFT')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id','LEFT')
			->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
			->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
			->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')
			->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
			->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND pm.is_cover','LEFT')
			->join(DBPREFIX."_users as users1",'(sp.seller_id = users1.userid OR sp.seller_id = 1)')
			->join(DBPREFIX.'_product_regions AS pr','pr.product_id = product.product_id','LEFT')
			->join(DBPREFIX.'_product_variant AS pv','pv.sp_id = sp.sp_id AND pv.`product_id` = product.`product_id` AND pv.`seller_id` = sp.`seller_id` AND pv.`condition_id` = sp.`condition_id`','LEFT')
			->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id AND pin.`quantity` > 0')
			->join(DBPREFIX."_seller_store AS ss","(ss.seller_id = users1.userid OR ss.seller_id = 1)")
			->group_by($group_by)
			->where('(pin.quantity - pin.sell_quantity) > 0')
			->where('sp.approve','1')
			->where('sp.is_active','1')
			->where('pin.approve','1')
			->where('cat.is_private','0')
			->order_by($order);
			if($keywords){
				$this->db->join(DBPREFIX."_meta_keyword AS mk",'mk.product_id = product.product_id');
				$this->db->like('mk.keywords',$keywords);
			}
			$query = $this->db->get();
			$result=$query->result_array();
			return $result;
	}

	public function deleteproduct_image($thumbnail, $prodid, $index, $total){
		$msg = "At least one image is required";
		if($total == 1){
			$result = $msg;
		} else {
			$total = $total - 1;
			$comma = ",";
			if($index == $total){
				$sql = "UPDATE tbl_product_media SET thumbnail = REPLACE(thumbnail,'".$comma."".$thumbnail."','') WHERE product_id = '".$prodid."'";
			} else {
				$sql = "UPDATE tbl_product_media SET thumbnail = REPLACE(thumbnail,'".$thumbnail."".$comma."','') WHERE product_id = '".$prodid."'";
			}
			$result = $this->db->query($sql);
		}
		return $result;
	}

	public function getPrimaryImage($id){
		$this->db->select('is_primary')
				 ->from(DBPREFIX.'_product_media')
				 ->where('product_id', $id);
		$query = $this->db->get();
		$data= $query->result();
		return $data;
	}

	public function setPrimarytoNull($product_id){
		$insert_data = array(
			'is_primary' => ''
		);
		$this->db->where('product_id', $product_id)->update(DBPREFIX.'_product_media', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function getSellerIdBysellerStore($id){
		$q = $this->db->select('seller_id')
					->where('store_id',$id)
					->get(DBPREFIX."_seller_store");
		return $q->row();
	}

	public function historyFilters($userid,$from,$to,$searchSeller,$searchStatus,$sellerId,$searchBrand,$searchCategory,$select=""){
		if($from != ""){
		$from = explode('/', $from);
		$from = date('Y-m-d', strtotime($from[2].'-'.$from[1].'-'.$from[0]));
		}
		if($to != ""){
		$to = explode('/', $to);
		$to = date('Y-m-d', strtotime($to[2].'-'.$to[1].'-'.$to[0]));
		}
		if($from!='' && $to!=''){
		$where = "((product.created_date BETWEEN'".$from." 00:00:00 'AND'".$to." 23:59:59') OR (sp.created_date BETWEEN'".$from."'and'".$to."'))";
		$this->db->where($where);
		}
		if($searchSeller != ""){
				$userid = $sellerId;
			}
		if($searchStatus != ""){
			$this->db->where('product.is_active',$searchStatus);
		}
		if($searchBrand != ""){
			$this->db->where('product.brand_id',$searchBrand);
		}
		if($searchCategory != ""){
			$this->db->where('product.sub_category_id',$searchCategory);
		}
		if($select == ""){
			$select = 'sp.created_date, product.created_date as pdtDate,`sp`.`seller_id`, `product`.`product_id`,pc.condition_name, GROUP_CONCAT(DISTINCT vc.v_cat_title, ":", v.v_title) AS variant,pin.price,pin.quantity,sp.sp_id, `product`.`product_name`, `cat`.`category_name`, `b`.`brand_name`, `product`.`upc_code`, `sp`.`seller_sku`, `ss`.`store_name` AS `name1`,ss.store_id,ss.store_address, `dis`.`value` AS discount, `dis`.`type` AS discount_type,  `sp`.`is_featured`, `sp`.`is_banner`, `sp`.`is_active` AS `active`, `sp`.`approve`';
		}
		$this->db->select($select)
		->from(DBPREFIX."_product as product")
		->join(DBPREFIX."_seller_product AS sp",'product.product_id = sp.product_id' ,"LEFT")
		->join(DBPREFIX."_product_conditions` AS pc",'sp.condition_id = pc.condition_id','LEFT')
		->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id','LEFT')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id AND cat.is_active = "1" AND cat.display_status = "1"')
		->join(DBPREFIX."_seller_store AS ss",'((sp.seller_id IS NULL AND product.created_id = ss.seller_id) OR (`sp`.`seller_id` = `ss`.`seller_id`AND sp.seller_id IS NOT NULL))','LEFT')
		->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id','LEFT')
		->join(DBPREFIX."_policies AS dis",'pin.discount = dis.id AND dis.active = "1"' ,"LEFT")
		->JOIN(DBPREFIX.'_product_variant AS pv','pin.`product_variant_id` = pv.`pv_id`','LEFT')
		->JOIN(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
		->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT');
		if($userid){
			$this->db->where("CASE WHEN product.created_id = '$userid' AND sp.seller_id IS NULL THEN product.created_id = '$userid' ELSE sp.seller_id = '$userid' END", false, false);
			}
		$this->db->group_by('pin.product_variant_id,product.product_id')->order_by('product.product_id','DESC ');
		$query = $this->db->get();
		// echo"<pre>". $this->db->last_query();die();
		$data['data'] = $query->result();
		return $data;
	}
	public function productPipline($search = "", $request = "", $userid = "",$totalProduct=""){
		if($search !=""){
			$search = trim($search);
			$this->db->group_start();
			$this->db->like('product.product_name',$search);
			$this->db->or_like('cat.category_name',$search);
			$this->db->or_like('b.brand_name',$search);
			$this->db->group_end();
		}
		$this->db->select('product.`product_id`,product.slug,`pm`.`thumbnail` AS `is_primary_image`,`pm`.`is_local`,`pm`.`is_image`,`product`.`product_name`,`cat`.`category_name`,`b`.`brand_name`,	`ss`.`store_name` AS `name1`,`product`.`is_active` AS `prd_active`, product.is_featured')
		->from(DBPREFIX."_product as product")
		//->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id','LEFT')
		//->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id','LEFT')
		//->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id','LEFT')
		->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked="0"')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
		//->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')
		//->join(DBPREFIX."_users as users1",'sp.seller_id = users1.userid','LEFT')
		->join(DBPREFIX."_seller_store AS ss",'ss.seller_id = product.created_id','LEFT')
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND is_image="1" AND pm.is_cover','LEFT')
		//->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id')
		->group_by('product.product_id')->order_by('product.created_date','DESC ');
		if($userid){
			$this->db->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id');
			$this->db->where('sp.seller_id',$userid);
			$this->db->where('sp.is_active',"1");
			$this->db->where('sp.approve',"1");
		}
		if($request['start'] != 0){
			$this->db->limit($request['length'],$request['start']);
		}else{
			$this->db->limit($request['length']);
		}
		$query = $this->db->get();
		// echo"<pre>". $this->db->last_query(); die();
		$data['draw'] = $request['draw'];
		$data['data'] = $query->result();
		/* if($request['start'] != 0){
			$data['recordsTotal']= $totalProduct;
			$data['recordsFiltered']= $totalProduct;
		}else{ */
		$data['recordsTotal'] = $totalProduct;
		$data['recordsFiltered'] = $totalProduct;
		//}
		return $data;
	}
	public function productCount($search = "", $userid = ""){
		//$this->db->cache_on();
		if($search !=""){
			$search = trim($search);
			$this->db->group_start();
			$this->db->like('product.product_name',$search);
			$this->db->or_like('cat.category_name',$search);
			$this->db->or_like('b.brand_name',$search);
			$this->db->group_end();
		}
		$this->db->select('COUNT(DISTINCT product.product_id) AS totalProduct')
		->from(DBPREFIX."_product as product")
		->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked="0"')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
		->join(DBPREFIX."_seller_store AS ss",'ss.seller_id = product.created_id','LEFT');
		if($userid){
			$this->db->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id');
			$this->db->where('sp.seller_id',$userid);
			$this->db->where('sp.is_active',"1");
			$this->db->where('sp.approve',"1");
		}
		$this->db->where('product.is_active',"1");
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		return $query->row();
	}

	public function productPendingPipline($search = "", $request = "", $userid = "",$select=""){
		if($search !=""){
			$search = trim($search);
			$this->db->group_start();
			$this->db->like('product.product_name',$search);
			$this->db->or_like('cat.category_name',$search);
			$this->db->or_like('b.brand_name',$search);
			$this->db->group_end();
		}
		if($select == ""){
			$select = 'product.product_id,pm.thumbnail AS is_primary_image,pm.is_local,pm.is_image,product.product_name,cat.category_name,b.brand_name,product.upc_code,users1.userid AS userid1, ss.store_name AS name1, product.is_active as active,product.declined_reason,product.is_declined';
		}
		$this->db->select($select)
		->from(DBPREFIX."_product as product")
		->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
		->join(DBPREFIX."_users as users1",'product.created_id = users1.userid','LEFT')
		->join(DBPREFIX."_seller_store AS ss",'ss.seller_id = product.created_id','LEFT')
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND pm.is_cover="1"','LEFT')
		->group_by('product.product_id')->order_by('product.created_date','DESC ');
		if($userid){
			$this->db->where('product.created_id',$userid);
		}
		if($request['is_active'] == 1 || $request['is_active'] == 0){
			$this->db->where('product.is_declined', '0');
			$this->db->where('product.is_active', $request['is_active']);
		}else{
			$this->db->where('product.is_declined', '1');
		}

		//f($request['start'] != 0){
			$this->db->limit($request['length'],$request['start']);
		//}
		$query = $this->db->get();
		$data['draw'] = $request['draw'];
		$data['data'] = $query->result();
		if($request['start'] != 0){
			$data['recordsTotal']= $request['recordsTotal'];
			$data['recordsFiltered']= $request['recordsTotal'];
		}else{
			$data['recordsTotal']= $query->num_rows();
			$data['recordsFiltered']= $query->num_rows();
		}
		return $data;

	}

	public function get_subDetails($sp_id){
		$data = array('row'=>0,'result'=>"");
		$variantData = array();
		$this->db->select('cp.condition_name,pv.variant_group,pv.variant_cat_group,pin.price,pin.quantity')
				->from(DBPREFIX.'_seller_product AS sp')
				->join(DBPREFIX.'_product_conditions AS cp','sp.condition_id = cp.condition_id')
				->join(DBPREFIX.'_product_variant as pv','sp.sp_id = pv.sp_id')
				->join(DBPREFIX.'_product_inventory pin ','pv.pv_id =pin.product_variant_id');
				$this->db->where_in('sp.sp_id',$sp_id,FALSE);
				$query = $this->db->get();
				// echo "<pre>".$this->db->last_query(); die();
				$result= $query->result();
				$row= $query->num_rows();
			if($row > 0){
				foreach($result as $vd)	{
					if(empty($vd->variant_group)){
						$variantData[$vd->condition_name]['v_title'][] = $vd->price;
						$variantData[$vd->condition_name]['v_title'][] = $vd->quantity;
						$variantData[$vd->condition_name]['variant_group'][] = 'price';
						$variantData[$vd->condition_name]['variant_group'][] = 'quantity';
						$data['row']= $query->num_rows();
						$data['result']= $variantData;
					}
					else{
						$this->db->select('v.v_id,v.v_title,v.v_cat_id,vc.v_cat_title')
						->from(DBPREFIX.'_variant AS v')
						->join(DBPREFIX.'_variant_category AS vc','v.v_cat_id = vc.v_cat_id');
						$this->db->where_in('v_id',$vd->variant_group,FALSE);
						$query1 = $this->db->get();
						$result1 = $query1->result();
						$variantData[$vd->condition_name]['variant_group'] = array();
						foreach($result1 as $r){
							if(!in_array($r->v_cat_title,$variantData[$vd->condition_name]['variant_group'])){
								$variantData[$vd->condition_name]['variant_group'][] = $r->v_cat_title;
							}
							$variantData[$vd->condition_name]['v_title'][] = $r->v_title;
						}
						$variantData[$vd->condition_name]['v_title'][] = $vd->price;
						$variantData[$vd->condition_name]['v_title'][] = $vd->quantity;
						$variantData[$vd->condition_name]['variant_group'][] = 'price';
						$variantData[$vd->condition_name]['variant_group'][] = 'quantity';
						$data['row']= $query1->num_rows();
						$data['result']= $variantData;
				}
			}
		}
		return $data;
	}
	public function get_inventories($product_id){
		$data = array('row'=>0,'result'=>"");
		$variantData = array();
		$this->db->select('sp.sp_id,sp.seller_id,sp.condition_id,`cp`.`condition_name`,	`pv`.`variant_group`,`pv`.`variant_cat_group`,`pin`.`price`,`pin`.`quantity`')
				->from(DBPREFIX.'_seller_product AS sp')
				->join(DBPREFIX.'_product_conditions AS cp','sp.condition_id = cp.condition_id')
				->join(DBPREFIX.'_product_variant as pv','sp.sp_id = pv.sp_id')
				->join(DBPREFIX.'_product_inventory pin ','pv.pv_id =pin.product_variant_id');
				$this->db->where_in('sp.product_id',$product_id,FALSE);
				$query = $this->db->get();
				$result= $query->result();
				$row= $query->num_rows();
			if($row > 0){
				foreach($result as $vd)	{
					if(empty($vd->variant_group)){
						$variantData[$vd->condition_name]['v_title'][] = $vd->price;
						$variantData[$vd->condition_name]['v_title'][] = $vd->quantity;
						$variantData[$vd->condition_name]['variant_group'][] = 'price';
						$variantData[$vd->condition_name]['variant_group'][] = 'quantity';
						$data['row']= $query->num_rows();
						$data['result']= $variantData;
					}
					else{
						$this->db->select('v.v_id,v.v_title,v.v_cat_id,vc.v_cat_title')
						->from(DBPREFIX.'_variant AS v')
						->join(DBPREFIX.'_variant_category AS vc','v.v_cat_id = vc.v_cat_id');
						$this->db->where_in('v_id',$vd->variant_group,FALSE);
						$query1 = $this->db->get();
						$result1 = $query1->result();
						$variantData[$vd->condition_name]['variant_group'] = array();
						foreach($result1 as $r){
							if(!in_array($r->v_cat_title,$variantData[$vd->condition_name]['variant_group'])){
								$variantData[$vd->condition_name]['variant_group'][] = $r->v_cat_title;
							}
							$variantData[$vd->condition_name]['v_title'][] = $r->v_title;
						}
						$variantData[$vd->condition_name]['v_title'][] = $vd->price;
						$variantData[$vd->condition_name]['v_title'][] = $vd->quantity;
						$variantData[$vd->condition_name]['variant_group'][] = 'price';
						$variantData[$vd->condition_name]['variant_group'][] = 'quantity';
						$data['row']= $query1->num_rows();
						$data['result']= $variantData;
				}
			}
		}
		return $data;
	}

	public function get_storeDetails($prd_id, $seller_id =""){
		$data = array('row'=>0,'result'=>"");
		$storeData = array();
		$this->db->select('DISTINCT(seller.store_name) AS store_name, seller.seller_id AS id')
				->from(DBPREFIX.'_seller_product AS sp')
				->join(DBPREFIX.'_seller_store AS seller','sp.seller_id = seller.seller_id')
				->where('sp.is_active',"1")
				->where('sp.approve',"1")
				->where('sp.product_id',$prd_id,FALSE);
				if($seller_id){
					$this->db->where('sp.seller_id',$seller_id);
				}
				$query = $this->db->get();
				$result= $query->result();
				foreach($result as $r){
					$storeData[] = array("store_name" => $r->store_name, "view" => "<a href='".base_url('seller/product/inventory_view?prd_id='.$prd_id.'&seller='.$r->id)."'>View</a>");
				}
				//echo "<pre>"; print_r($storeData); die();
				return $storeData;
	}

	public function fpt($select,$country_id=""){
		$select = "";
		if($country_id !=""){
			$this->db->where('(pr.country_id = "'.$country_id.'" OR pr.country_id = "0")',NULL,false);
		}
		$this->db->select($select)
		->from(DBPREFIX.'_product AS product')
		->join(DBPREFIX.'_product_regions as pr','pr.`product_id` = product.product_id')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories AS cat','cat.`category_id` = pro_cat.category_id')
		->join(DBPREFIX.'_brands b','b.`brand_id` = product.`brand_id`')
		->join(DBPREFIX.'_seller_product sp','sp.product_id = product.product_id')
		->join(DBPREFIX.'_product_inventory pin','pin.`seller_product_id` = sp.`sp_id` AND pin.`quantity` > 0 AND pin.seller_id !=1')
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pin.seller_id')
		->where(array('sp.seller_id !='=>'1','sp.approve'=>"1"))
		->group_by('product.`product_id`')
		->order_by('sp.condition_id,pin.price');
		if($user_id != ""){
			$this->db->join(DBPREFIX.'_wishlist as w',' w.product_id = product.product_id  AND w.pv_id = pin.product_variant_id AND w.user_id="'.$user_id.'"','LEFT');
		}
		$product = $this->db->get();
		$data['productDataRows'] = $product->num_rows();
		$data['productData'] = $product->row();
		return $data;
	 }

	public function deletePv($pvId=""){
		if($pvId !=""){
			$this->db->trans_start(); # Start transaction
			$this->db->where('pv_id',$pvId)->delete(DBPREFIX.'_seller_product_variant');
			$this->db->where("product_variant_id",$pvId)->delete("tbl_product_inventory");
			$this->db->where("pv_id",$pvId)->delete("tbl_product_variant");
			$this->db->trans_complete(); # Completing transaction
			if ($this->db->trans_status() === FALSE) {
				# Something went wrong.
				$this->db->trans_rollback();
				return FALSE;
			} else {
				# Everything is Perfect.
				# Committing data to the database.
				$this->db->trans_commit();
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}

	public function saveData($data,$table){
		if($this->db->insert($table,$data)){
			return $this->db->insert_id();
		}else{
			return false;
		}
	}

	// public function updateAccData($data,$table,$id, $user_id){
	// 	$query = array();
	// 	$notPresent = array();
	// 	$integerIDs = array();
	// 	$integerRealIDs = array();
	// 	$dummy = array();
	// 	for($i = 0; $i < count($data['accessories']); $i++){
	// 		$query[$i] = $this->prod_id_step_one($data['accessories'][$i]);
	// 		if($query[$i] == ""){
	// 			unset($query[$i]);
	// 		}
	// 	}
	// 	$query_from_tblprodacc = $this->prod_id_step_two($data['product_id'], $user_id);
	// 	//echo "<pre>";print_r($query_from_tblprodacc);die();
	// 	for($i = 0; $i < count($query_from_tblprodacc); $i++){
	// 		$temp = $query_from_tblprodacc[$i]->accessory_id;
	// 		$found = false;
	// 		for($j = 0; $j < count($query); ++$j){
	// 			if($query[$j][0]->product_id == $temp){
	// 				$found = true;
	// 			}
	// 		}
	// 		if(!$found) {
	// 			array_push($notPresent, $temp);
	// 			$notPresent = array_unique($notPresent);
	// 		}
	// 	}
	// 	echo "<pre>";
	// 	print_r($query);
	// 	print_r($query_from_tblprodacc);
	// 	print_r($notPresent);die();
	// 	for($i = 0; $i < count($data['accessories']); $i++){
	// 		$integerIDs[$i] = array_map('intval', explode(',', $data['accessories'][$i]));
	// 		if($integerIDs[$i][0] == 0){
	// 			unset($integerIDs[$i]);
	// 		}
	// 	}
	// 	$prod_id = json_decode(json_encode($integerIDs), True);
	// 	$i = 0;
	// 	foreach($prod_id as $pa){
	// 		$dummy[$i] = $pa;
	// 		$i++;
	// 	}
	// 	if($notPresent != ""){
	// 		foreach($notPresent as $np){
	// 			$resp2 = $this->set_notavailable_accIds_inactive($np, $data['product_id'], $user_id);
	// 		}
	// 	}
	// 	for($i = 0; $i < count($dummy); $i++){
	// 		$saveData = array("product_id"=>$data['product_id'],'accessory_id'=>$dummy[$i][0], "seller_id" => $user_id, "is_active"=>'1');
	// 		$resp = $this->db->insert($table,$saveData);
	// 	}
	// 	if(isset($resp) > 0 || isset($resp2) > 0){
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }

	/*public function updateAccData($data, $table, $user_id, $id){
		$this->db->where('id', $id);
		$acc_data = array("accessory_id" => $data);
		if($this->db->update($table, $acc_data)){
			return true;
		}else{
			return false;
		}
	}*/

	public function deleteData($id,$table,$where){
		$this->db->where($where,$id);
		if($this->db->delete($table)){
			return true;
		}else{
			return false;
		}
	}

	public function updateData($table,$data,$where){
		$this->db->where($where);
		if($this->db->update($table,$data)){
			return true;
		}else{
			return false;
		}
	}

	public function getproductAccessories($userid, $search, $offset, $length, $id = ""){
		$this->datatables->select('pa.id AS id, p.product_name, p.product_id, pa.accessory_id, GROUP_CONCAT(ap.product_name) as accessories')
		->from('tbl_product_accessories as pa')
		->join('tbl_product as p', 'pa.product_id = p.product_id', 'left')
		->join('tbl_product AS ap', 'FIND_IN_SET(ap.product_id ,pa.accessory_id)')
		->where('pa.seller_id', $userid)
		->where('pa.is_active', '1');
		$this->datatables->group_by('pa.product_id');
		$this->db->order_by('pa.id', 'DESC');
		if($id != ''){
			$this->datatables->where('id', $id);
		} else {
			$this->datatables->or_where('id', 0);
		}
	}

	function ProdNameAuthForAccessories($product_id){
		$this->db->select('product_name')
			 ->from("tbl_product")
			 ->where('product_id', $product_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return true;
		}else{
			return FALSE;
		}
	}

	function delete_prod_acc($id, $user_id){
		$data = array(
			"is_active" => '0'
		);
		$this->db->where('seller_id', $user_id)
					->where('id', $id)
					->update('tbl_product_accessories', $data);

		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function getAccDataById($id,$user_id){
		$this->db->select('*')
			 ->from("tbl_product_accessories")
			 ->where('seller_id', $user_id)
			 ->where_in('id', $id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0)
		{
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function getAccDataByProdId($userid, $id,$select="",$search="",$start="",$length="",$product_id="")
	{
		if($select == ""){
			$select = 'pa.id AS id, p.product_name AS product, p.product_id, pa.accessory_id, GROUP_CONCAT(CONCAT(ap.product_id,"-zabee-",ap.product_name)) AS accessory';
		}
		if($search !=""){
			$this->db->like("pa.product_name",$search);
		}
		if($id){
			$this->db->where('pa.id', $id);
		}
		if($product_id){
			$this->db->where('pa.product_id', $product_id);
		}
		$this->db->select($select)
				 ->from('tbl_product_accessories as pa')
				 ->join('tbl_product as p', 'pa.product_id = p.product_id', 'left')
				 ->join('tbl_product AS ap', 'FIND_IN_SET(ap.product_id ,pa.accessory_id)')
				 ->where('pa.seller_id', $userid)
				 ->where('pa.is_active',"1")
				 ->group_by("pa.id");
		$this->db->limit($length,$start);
		$query = $this->db->get();
		// echo $this->db->last_query(); die();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;
		}
	}

	function productIdByName($name){
		$this->db->select('product_id')
			 ->from("tbl_product")
			 ->where('product_name', $name);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;
		}
	}

	function getUserIdByProductId($id){
		$this->db->select('created_id')
			 ->from("tbl_product")
			 ->where('product_id', $id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;
		}
	}

	function getProdIdsFor_getproductAccessories($id){
		$this->db->select('product_id')
			 ->from("tbl_product_accessories")
			 ->where('seller_id', $id)
			 ->order_by('id', 'DESC');
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function prod_id_step_one($accessories_id = ''){
		$this->db->select('product_id')
		 ->from("tbl_product")
		 ->where('product_name', $accessories_id);
		 $query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function prod_id_step_two($product_id = '', $user_id){
		$this->db->select('accessory_id')
			 ->from("tbl_product_accessories")
			 ->where('seller_id', $user_id)
			 ->where('product_id', $product_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function set_notavailable_accIds_inactive($np, $product_id, $user_id){
		$insert_data = array(
			'is_active' => '0',
		);
		$this->db->where('accessory_id', $np);
		$this->db->where('product_id', $product_id);
		$this->db->where('seller_id', $user_id)->delete('tbl_product_accessories');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function selectWishlistCategories($user_id){
		$this->db->select('id AS category_id, category_name')
			 ->from("tbl_wishlist_categoryname")
			 ->where(array('user_id'=>$user_id, "is_delete"=>"0"));
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function selectWishlistCategoriesNames($cat_id, $user_id){
		$dummy = array();
		$cat_ids = json_decode(json_encode($cat_id), True);
		if($cat_ids == ""){
			return FALSE;
		} else {
			$count = count($cat_ids);
			for($i = 0; $i < $count; $i++){
				array_push($dummy, $cat_ids[$i]['category_id']);
			}
			$this->db->select('id, category_name')
				->from('tbl_wishlist_categoryname')
				->where('user_id', $user_id)
				->where_in('id', $dummy);
			$query = $this->db->get();
			if($query && $query->num_rows()>0){
				return $query->result();
			}else{
				return FALSE;
			}
		}
	}

	public function selectAllWishlistCategoriesNames($cat_id, $user_id){
			$this->db->select('id as category_id, category_name')
				->from('tbl_wishlist_categoryname')
				->where('user_id', $user_id);
			$query = $this->db->get();
			if($query && $query->num_rows()>0){
				return $query->result();
			} else {
				return FALSE;
			}
	}

	public function CountTotalProductsInWishlist($user_id){
		$this->db->select('count(w.category_id) as total')
			->from('tbl_wishlist AS w')
			->join(DBPREFIX."_product_inventory as pin","pin.product_id = w.product_id AND pin.product_variant_id = w.pv_id")
			->join(DBPREFIX."_wishlist_categoryname as twc","twc.id = w.category_id")
			->where('w.user_id', $user_id)
			->where(array('pin.approve'=>"1",'(pin.quantity - pin.sell_quantity) >'=>"0", "twc.is_delete"=>"0"));
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function CountProductsPerCategory($user_id, $catNames){
		$this->db->select('w.category_id, count(w.category_id) as forThisCategory')
			->from('tbl_wishlist AS w')
			->join(DBPREFIX."_product_inventory as pin","pin.product_id = w.product_id AND pin.product_variant_id = w.pv_id")
			->where('w.user_id', $user_id)
			->where(array('pin.approve'=>"1",'(pin.quantity - pin.sell_quantity) >'=>"0"))
			->group_by('w.category_id');
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function getCategoryNames($userid){
		$reMastered = array();
		$this->db->select('c.category_name as categoryName, p.product_id as prodId, c.category_id as catId, w.wish_id as id')
				 ->from('tbl_product AS p')
				 ->join('tbl_wishlist AS w', 'w.product_id = p.product_id')
				 ->join('tbl_product_category pc', 'pc.product_id = p.product_id')
				 ->join('tbl_categories AS c', 'c.category_id IN (pc.category_id)')
				 ->where('w.user_id', $userid)
				 ->order_by('w.wish_id', 'DESC');
		$query = $this->db->get();
		// echo"<pre>".$this->db->last_query(); die();
		if($query && $query->num_rows()>0)
		{
			$array = json_decode(json_encode($query->result()), true);
			for($i = 0; $i < count($array); $i++){
				for($j = $i + 1; $j < count($array); $j++){
					if($array[$i]['catId'] == $array[$j]['catId']){
						$array[$j]['catId'] = 0;
					}
				}
			}
			return $array;
		} else {
			return FALSE;
		}
	}

	function get_by_subcatid($user_id,$pageing="0",$limit="", $catId = ""){
		if($pageing > 1){
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		$this->db->select("w.wish_id,w.created_date,w.product_id,w.pv_id,p.product_name,p.slug,sp.seller_id,sp.sp_id,p.short_description as seller_product_description,sp.condition_id,condition.condition_name,pm.thumbnail as is_primary_image,pm.is_local,pin.price, twc.category_name")
				 ->from(DBPREFIX."_wishlist as w")
				 ->join(DBPREFIX."_product as p","p.product_id = w.product_id")
				 ->join(DBPREFIX."_wishlist_categoryname as twc","twc.id = w.category_id")
				 ->join(DBPREFIX."_product_inventory as pin","pin.product_id = w.product_id AND pin.product_variant_id = w.pv_id")
				 ->join(DBPREFIX."_seller_product as sp","sp.product_id = w.product_id AND sp.sp_id = pin.seller_product_id")
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
				 ->join(DBPREFIX.'_product_conditions AS condition', 'sp.condition_id = condition.condition_id')
				 ->join(DBPREFIX."_product_category pc", "pc.product_id = p.product_id")
				 ->WHERE("pc.category_id", $catId)
				 ->WHERE('w.user_id', $user_id);
		$query = $this->db->get();
		// echo"<pre>".$this->db->last_query(); die();
		$data['data'] = $query->result();
		return $data;
	}

	public function apiGetProductVariantData($product_id,$condition_id="",$variant="",$variant_is_array =0,$condition="",$limit="",$shipping_id=""){
		if($variant !="" && $variant_is_array == 0){
			$this->db->where('pv.variant_group',$variant);
		}else if($variant !="" && $variant_is_array == 1){
			$this->db->where_in('spv.v_id',$variant);
		}
		if($condition_id !=""){
			if($condition ==""){
				$this->db->where_in('pv.condition_id',$condition_id);
			}else{
				$where = "(SELECT MIN(`pv`.`condition_id`) FROM tbl_product_variant pv LEFT JOIN `tbl_seller_product_variant` `spv`
        ON `spv`.`pv_id` = `pv`.`pv_id` LEFT JOIN `tbl_users` `u` ON `u`.`userid` = `pv`.`seller_id` LEFT JOIN `tbl_seller_store` `ss`  ON `ss`.`seller_id` = `pv`.`seller_id` WHERE `pv`.`product_id` = '".$product_id."' AND `pv`.`quantity` > 0 AND `spv`.`v_id` IN ('".$variant."')) ";
				$this->db->where('pv.condition_id ='.$where,NULL,FALSE);
			}
		}
		if($shipping_id != ""){
			 $this->db->where_in('sp.shipping_ids',$shipping_id);
		}
		$this->db->select('sp.sp_id,ss.store_name,GROUP_CONCAT(DISTINCT sp.`sp_id`) AS seller_product_id,(COUNT(DISTINCT sp.`seller_id`) - 1) AS total_seller,pin.`price`,pin.`condition_id`, pin.`seller_id`,`p`.`product_id`,`c`.`category_id`,`c`.`category_name`,`b`.`brand_id`,`b`.`brand_name`,`p`.`upc_code`,`p`.`product_name`,`p`.`product_description`,`p`.`is_active`,(pin.quantity - pin.sell_quantity) as quantity,sp.seller_sku,sp.shipping_ids, pv.pv_id,pv.variant_group,d.value as discount_value,d.type as discount_type, UNIX_TIMESTAMP(d.valid_from) as discount_start, UNIX_TIMESTAMP(d.valid_to) as discount_end')
				 ->from(DBPREFIX.'_product_variant pv')
				 ->join(DBPREFIX.'_seller_product_variant spv','spv.pv_id = pv.pv_id','LEFT')
				 ->join(DBPREFIX.'_product p','pv.product_id = p.product_id','LEFT')
				 ->join(DBPREFIX.'_users u','u.userid = pv.seller_id','LEFT')
				 ->join(DBPREFIX.'_seller_store ss','ss.seller_id = pv.seller_id','LEFT')
				 ->join(DBPREFIX.'_seller_product sp','sp.sp_id = pv.sp_id')
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = pv.product_id AND (pm.sp_id = sp.sp_id) AND pm.is_cover','LEFT')
				 ->join(DBPREFIX.'_product_inventory AS pin','pin.product_variant_id = pv.pv_id')
				 ->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = p.product_id')
				 ->join(DBPREFIX.'_categories AS c','c.`category_id` = pro_cat.category_id')
				 ->join(DBPREFIX.'_brands b','b.`brand_id` = p.`brand_id`')
				 ->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id`',"LEFT")
				 ->where('pv.product_id',$product_id)->where('(pin.quantity - pin.sell_quantity) >',0)->where('pin.approve',"1")
				 ->group_by('pv.pv_id')
				 ->order_by('pv.price');
		if($limit  != ""){
			$this->db->limit(1);
		}
		$query = $this->db->get();
		$data['gpvdRows'] = $query->num_rows();
		if($data['gpvdRows'] > 0){
			$data['gpvd'] = $query->result();
		}else{
			$data['gpvd'] = array();
		}
		return $data;
	}

	public function SelectCategoryName($cat_id, $user_id){
		$this->db->select('id, category_name')
				 ->from('tbl_wishlist_categoryname')
				 ->where('user_id', $user_id)
				 ->where_in('id', $cat_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;
		}
	}

	public function CountProductsPerCategoryforPresent($user_id){
		$this->db->select('w.category_id, wc.category_name, count(w.category_id) as forThisCategory')
			->from('tbl_wishlist w')
			->join('tbl_wishlist_categoryname AS wc', 'wc.id = w.category_id')
			->where('w.user_id', $user_id)
			->group_by('category_id');
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function getProductName($id){
		$this->db->select('product_name')
			->from('tbl_product')
			->where('product_id', $id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;
		}
	}

	public function getNumberofApprovedProducts($user_id){
		$this->db->select('count(product_id) as approved')->from('tbl_product')->where('created_id', $user_id)->where('is_declined', '0')->where('is_active', '1');
        $query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result()[0]->approved;
		} else {
			return FALSE;
		}
	}

	public function getNumberofRejectedProducts($user_id){
		$this->db->select('count(product_id) as rejected')->from('tbl_product')->where('created_id', $user_id)->where('is_declined', '1')->where('is_active', '0');
        $query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result()[0]->rejected;
		} else {
			return FALSE;
		}
	}

	public function getProductList($brand_id="",$category_id="",$slug="",$pageing=0,$limit="",$order="",$range="",$group_by="",$keywords="",$select="",$userid = "",$shipping_id="",$seller_id=""){
		if($pageing > 1){
			$pageing = (int)$pageing;
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		$this->db->query('SET SESSION group_concat_max_len = 1000000');
		if($brand_id){
			$this->db->where_in("product.brand_id",$brand_id,FALSE);
		}
		if($category_id){
			$this->db->where_in("pro_cat.category_id",$category_id,FALSE);
		}
		$querySelect = "";
		if($userid){
			$this->db->join(DBPREFIX.'_wishlist as w',' w.product_id = product.product_id AND w.user_id="'.$userid.'"','LEFT');
			$querySelect = ", if(w.wish_id,1,0) as already_saved";
		}
		if($select == ""){
			$select ='product.product_name,product.slug,product.product_id,pm.thumbnail,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id AS pv_id,AVG(preview.`rating`) AS rating,pin.price,d.value AS discount_value,d.type AS discount_type,d.valid_from AS valid_from,d.valid_to AS valid_to,sp.seller_id, product.short_description AS description'.$querySelect;
		}else{
			$select = $select.$querySelect;
		}
		if($slug){
			$select .=",MATCH(ps.slug) AGAINST('".$slug."' IN BOOLEAN MODE) as rank";
			$this->db->where("MATCH(ps.slug) AGAINST('".$slug."' IN BOOLEAN MODE) > 0",NULL,FALSE);
			$this->db->join(DBPREFIX."_product_slug ps","ps.product_id = product.product_id");
		}
		if($group_by == ""){
			$group_by="product.product_id";
		}
		if($order == ""){
			if($slug){
				$order = "rank DESC";
			}else{
				$order = "sp.sp_id DESC";
			}
		}
		if($range){
			$this->db->where("pin.price BETWEEN ".$range[0]." AND ".$range[1],NULL,FALSE);
		}
		if($seller_id != ""){
			$this->db->where("sp.seller_id",$seller_id);
		}
		$this->db->select($select, false)
				 ->from(DBPREFIX."_product as product")
				 ->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id AND sp.is_active="1" AND sp.approve="1"')
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"')
				 ->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
				 ->join(DBPREFIX."_categories AS cat",'cat.category_id = pro_cat.category_id AND cat.is_active = "1" AND cat.is_private="0"')
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND pm.is_cover','LEFT')
				 ->join(DBPREFIX.'_product_regions AS pr','pr.product_id = product.product_id','LEFT')
				 ->join(DBPREFIX.'_meta_keyword AS meta_keyword','meta_keyword.product_id = product.product_id','LEFT')
				 //->join(DBPREFIX.'_product_variant AS pv','pv.sp_id = sp.sp_id AND pv.`product_id` = product.`product_id` AND pv.`seller_id` = sp.`seller_id` AND pv.`condition_id` = sp.`condition_id`','LEFT')
				 ->join(DBPREFIX."_product_inventory AS pin","pin.seller_product_id = sp.sp_id AND ((pin.quantity - pin.sell_quantity) > 0) AND pin.approve='1' AND pin.product_variant_id = (SELECT `pin`.`product_variant_id` AS `pv_id` FROM `tbl_product_inventory` AS `pin` WHERE `pin`.`product_id` = product.product_id  AND ((pin.quantity - pin.sell_quantity) > 0) AND `pin`.`seller_id` = sp.seller_id AND `pin`.`approve` = '1' GROUP BY `pin`.`price` ORDER BY `pin`.`condition_id`,pin.price LIMIT 1)")
				 ->join(DBPREFIX."_seller_store AS ss",'ss.seller_id = pin.seller_id AND ss.is_approve="1" AND ss.is_active = "1" ')
				 ->join(DBPREFIX.'_policies AS d','pin.`discount` = d.`id` AND d.display_status = "1"',"LEFT")
				 ->join(DBPREFIX.'_product_reviews AS preview','preview.product_id = pin.product_id','LEFT');
				if($shipping_id){
					$this->db->join(DBPREFIX."_product_shipping p_ship","FIND_IN_SET(p_ship.shipping_id, sp.shipping_ids) AND p_ship.price = 0");
				}
				 $this->db->group_by($group_by)
				 ->where('product.is_private','0')
				 ->where('product.is_active','1')
				 ->order_by($order);
			if($keywords){
				$this->db->join(DBPREFIX."_meta_keyword AS mk",'mk.product_id = product.product_id');
				$this->db->like('mk.keywords',$keywords);
			}
			$query = $this->db->get();
			//echo"<pre>". $this->db->last_query();die();
			$result=$query->result_array();
			return $result;
	}

	public function getProductListCount($brand_id="", $category_id="",$slug="",$range="",$shipping_id="",$seller_id=""){
		$this->db->query('SET SESSION group_concat_max_len = 1000000');
		$select = "COUNT(DISTINCT p.product_id) AS total, GROUP_CONCAT(DISTINCT pro_cat.category_id) AS cat_id,GROUP_CONCAT(DISTINCT p.product_id) AS p_id";
		if($brand_id){
			$this->db->where_in("p.brand_id",$brand_id,FALSE);
		}
		if($category_id){
			$this->db->where_in("pro_cat.category_id",$category_id,FALSE);
		}
		if($slug){
			$select .= ",MATCH(ps.slug) AGAINST('".$slug."' IN BOOLEAN MODE) > 0";
			$this->db->where("MATCH(ps.slug) AGAINST('".$slug."' IN BOOLEAN MODE) > 0",NULL,FALSE);
			$this->db->join(DBPREFIX."_product_slug ps","ps.product_id = p.product_id");
		}
		if($range){
			$this->db->where("pin.price BETWEEN " .$range[0]." AND ".$range[1],NULL,FALSE);
		}
		if($seller_id){
			$this->db->where("sp.seller_id",$seller_id);
		}
		$this->db->select($select, false)
				 ->from(DBPREFIX."_product as p")
				 ->join(DBPREFIX."_seller_product AS sp",'sp.product_id = p.product_id AND sp.approve="1" AND sp.is_active="1"')
				 ->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = p.product_id')
				 ->join(DBPREFIX."_categories AS cat",'cat.category_id = pro_cat.category_id AND cat.is_active = "1" AND cat.is_private="0"')
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = p.brand_id AND b.blocked = "0"')
				 ->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id AND (pin.quantity - pin.sell_quantity) > 0 AND pin.approve="1"')
				 ->join(DBPREFIX."_seller_store AS ss",'ss.seller_id = pin.seller_id AND ss.is_approve="1" AND ss.is_active = "1" ')
				 ->join(DBPREFIX.'_meta_keyword AS meta_keyword','meta_keyword.product_id = p.product_id','LEFT');
				if($shipping_id){
					$this->db->join(DBPREFIX."_product_shipping p_ship","FIND_IN_SET(p_ship.shipping_id, sp.shipping_ids) AND p_ship.price = 0");
				}
				$this->db->where('p.is_private','0')
				 ->where('p.is_active','1');
			$query = $this->db->get();
			//echo $this->db->last_query(); die();
			$result=$query->row();
			return $result;
	}

	public function getBrandAndCategory($product_ids="",$category_ids=""){
		$result["brands"] = $this->db->select("b.brand_id,b.brand_name")
									 ->from(DBPREFIX."_product as p")
									 ->join(DBPREFIX."_brands as b","p.brand_id=b.brand_id AND b.blocked='0'")
									 ->join(DBPREFIX."_product_category as pc","pc.product_id = p.product_id")
									 ->where_in("p.product_id",$product_ids,FALSE)
									 ->where_in("pc.category_id",$category_ids,FALSE)->group_by("b.brand_id")->get()->result();

		$result["categories"] = $this->db->select("c.category_id,c.category_name,c.category_link,c.slug")
										 ->from(DBPREFIX."_product as p")
										 ->join(DBPREFIX."_product_category as pc","pc.product_id = p.product_id")
										 ->join(DBPREFIX."_categories as c","c.category_id = pc.category_id")
										 ->where_in("p.product_id",$product_ids,FALSE)
										 ->where_in("pc.category_id",$category_ids,FALSE)->group_by("c.category_id")->get()->result();
		return $result;
	}

	public function storeCount($prd_id, $seller_id=""){
		//$this->db->cache_on();
		// if($search !=""){
		// 	$search = trim($search);
		// 	$this->db->group_start();
		// 	$this->db->like('product.product_name',$search);
		// 	$this->db->or_like('cat.category_name',$search);
		// 	$this->db->or_like('b.brand_name',$search);
		// 	$this->db->group_end();
		// }
		$this->db->select('COUNT(DISTINCT(seller.store_name)) AS store_name')
		->from(DBPREFIX."_seller_product AS sp")
		->join(DBPREFIX."_seller_store AS seller", "sp.seller_id = seller.seller_id")
		->where("sp.product_id","$prd_id");
		// ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked="0"')
		// ->join(DBPREFIX."_categories AS cat",'cat.category_id = product.sub_category_id AND cat.display_status="1"');
		if($seller_id){
			$this->db->where('sp.seller_id',$seller_id);
			// $this->db->where('pin.approve',"1");
		}
		$this->db->where('sp.is_active',"1");
		$query = $this->db->get();
		// print_r($this->db->last_query()); die();
		//echo $this->db->last_query();die();
		return $query->row();
	}
	public function getIdBySlug($table_name,$select,$where,$single=true){
		$query = $this->db->select($select)->from($table_name)->where($where)->get();
		if($single){
			$query = $query->row();
		}else{
			$query = $query->result();
		}
		//echo $this->db->last_query();die();
		return $query;
	}
	public function impression_increment($code){
		$this->db->where('referral_code',$code);
		$this->db->set('impression', 'impression+1', FALSE);
		$this->db->update(DBPREFIX.'_referral');
	}
	public function getInventoryBySeller($seller_id, $search = "", $usertype = "", $prd_id = ""){
		if($seller_id != "" && $usertype != "1"){
			$this->db->where('pi.seller_id', $seller_id);
		}
		$select = "";
		if($usertype == "1") {
			$select = "ss.store_name,";
		}
		$this->db->SELECT("inventory_id as inventory_id, pi.`created_date`, ".$select."  pi.`product_name`, pc.`condition_name`, GROUP_CONCAT(DISTINCT vc.v_cat_title, ':', v.v_title) AS variant, pi.`quantity`, (pi.quantity) AS 'stock left',pi.`price`, pv.pv_id")
					->FROM("tbl_product_inventory as `pi`")
					->join(DBPREFIX.'_product_variant AS pv','pi.`product_variant_id` = pv.`pv_id`','left')
					->join(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
					->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
					->join(DBPREFIX."_product_conditions AS pc",'pc.condition_id =  pi.condition_id','LEFT')
					->join(DBPREFIX.'_seller_store as ss','ss.seller_id = pi.seller_id');
		if($prd_id){
			$this->db->where("pi.product_id", $prd_id);
		}
		$this->db->group_by("pv.pv_id");
		$this->db->LIKE('product_name', $search);
		$this->db->order_by('pi.inventory_id','DESC');
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return array();
		}
	}
	public function getProductShipping($ids, $select = '')
	{
		$response = array('status' => 0, 'data' => array());
		if($select == ''){
			$select = 'ship.*';
		}
		$this->db->select($select)
					->from(DBPREFIX."_product_shipping_info AS ship")
					->where_in("ship.product_id", $ids);
		$query = $this->db->get();
		if($query->num_rows() > 0){
			$response = $query->result();
		}
		return $response;
	}
	public function inventoryList($search = "", $start = "", $length = "", $user_id = "", $usertype = "", $prd_id = "",$approve=""){
		if($user_id != ""){
			$this->db->where('pi.seller_id', $user_id);
		}
		$select = "";
		if($usertype == "1") {
			$select = "ss.store_name,";
		}
		if($approve == ""){
			$this->db->where("pi.approve != '3'",NULL);
		}else if($approve == "delete"){
			$this->db->where("pi.approve","3");
		}else{
			$this->db->where("pi.is_warning","1");
		}
		$this->db->SELECT("inventory_id as id, UNIX_TIMESTAMP(pi.`created_date`) as created_date, pi.`product_name`, pc.`condition_name`, GROUP_CONCAT(DISTINCT vc.v_cat_title, ':', v.v_title) AS variant, (pi.quantity - pi.sell_quantity) as sold, (pi.quantity) AS 'stock_left',pi.`price`, discount.id AS discount_id,CASE WHEN discount.value IS NULL THEN 'no discount' ELSE concat(discount.value,'%',' - expiry date: ',discount.valid_to) END as discount")
				->FROM(DBPREFIX."_product_inventory as `pi`")
				->JOIN(DBPREFIX.'_product_variant AS pv','pi.`product_variant_id` = pv.`pv_id`','left')
				->JOIN(DBPREFIX.'_policies AS discount','pi.`discount` = discount.`id`','left')
				->JOIN(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
				->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
				->join(DBPREFIX."_product_conditions AS pc",'pc.condition_id =  pi.condition_id','LEFT')
				->join(DBPREFIX.'_seller_store as ss','ss.seller_id = pi.seller_id');
		if($prd_id){
			$this->db->where("pi.product_id", $prd_id);
		}
		if($search){
			$this->db->LIKE('product_name', $search);
		}
		$this->db->limit($length,$start);
		$this->db->group_by("pv.pv_id");
		$this->db->order_by('pi.inventory_id','DESC');
		$query = $this->db->get()->result();
		//echo $this->db->last_query();die();
		return $query;
	}
	public function delete_wishlist_category($id){
		$where = array("id"=>$id);
		$this->db->where($where)
		->set("is_delete", "1")
		->update(DBPREFIX.'_wishlist_categoryname');
		if($this->db->affected_rows() > 0){
			$where = array("category_id"=>$id);
			$this->db->where($where)
			->delete(DBPREFIX.'_wishlist');
			if($this->db->affected_rows() > 0){
				return array("status"=>true, "message"=>"wishlist category and products in it deleted successfully");
			}
			return array("status"=>true, "message"=>"wishlist category deleted successfully");

		} else {
			return array("status"=>"false", "message"=>"unable to delete wishlist category");
		}
	}
	public function getMinAndMaxPrice($product_id, $condition_id="1"){
		if($condition_id !="1"){
			$this->db->where("condition_id","1");
		}
		$result = $this->db->select("MIN(price) AS lowprice,MAX(price) AS highprice")->from(DBPREFIX."_product_inventory")->where(array("product_id"=>$product_id,"approve"=>"1","quantity >"=>"0","seller_id !="=>"1"))->get()->row();
		return $result;
	}
	public function deleteCondition($condition_id,$seller_id,$sp_id){
		$this->db->trans_start(); # Start transaction
		$this->db->where(array("seller_product_id"=>$sp_id))->delete(DBPREFIX."_product_regions");
		$this->db->where(array("sp_id"=>$sp_id))->delete(DBPREFIX."_seller_product_variant");
		$this->db->where(array("seller_product_id"=>$sp_id,"condition_id"=>$condition_id,"seller_id"=>$seller_id))->delete(DBPREFIX."_product_inventory");
		$this->db->where(array("sp_id"=>$sp_id,"condition_id"=>$condition_id,"seller_id"=>$seller_id))->delete(DBPREFIX."_product_variant");
		$this->db->where(array("sp_id"=>$sp_id,"condition_id"=>$condition_id,"seller_id"=>$seller_id))->delete(DBPREFIX."_seller_product");
		$this->db->trans_complete(); # Completing transaction
		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			return FALSE;
		} else {
			# Everything is Perfect.
			# Committing data to the database.
			$this->db->trans_commit();
			return TRUE;
		}
	}
	//Create MagaSlug
	public function megaSlug($product_id="", $pv_id=""){
		$this->db->query('SET SESSION group_concat_max_len = 10000000');
		$where = "WHERE p.product_id=".$product_id." AND pv.pv_id =".$pv_id;
		if($product_id && $pv_id){
			$query = $this->db->select("pv_id")->from("tbl_product_slug")->where(array("pv_id"=>$pv_id,"product_id"=>$product_id))->get()->row();
			if($query){
				$this->db->where(array("pv_id"=>$pv_id,"product_id"=>$product_id));
				$this->db->delete("tbl_product_slug");
			}
			//echo $query->pv_id;
			set_time_limit(3000000);
			$data = $this->db->query("SELECT
			p.product_id,
			sp.sp_id,
			pv.pv_id,
			p.product_name,
			c.category_name,
			b.brand_name,
			pcon.condition_name,
			k.keywords,
			GROUP_CONCAT(DISTINCT v.v_title) as variant
			FROM tbl_product p
			JOIN tbl_product_category pc
				ON pc.product_id = p.product_id
			JOIN tbl_categories c
				ON c.category_id = pc.category_id
			JOIN tbl_brands b
				ON b.brand_id = p.brand_id
			JOIN tbl_seller_product sp
				ON sp.product_id = p.product_id
			JOIN tbl_product_conditions pcon
				ON pcon.condition_id = sp.condition_id
			JOIN tbl_product_inventory pin
				ON pin.product_id = p.product_id
			JOIN tbl_product_variant pv
				ON pv.product_id = p.product_id
				AND pv.sp_id = sp.sp_id
				AND pv.condition_id = sp.condition_id
			LEFT JOIN tbl_variant v
				ON FIND_IN_SET(v.v_id,pv.variant_group)
			LEFT JOIN tbl_meta_keyword k ON k.product_id = p.product_id
			".$where."
			GROUP BY pv.pv_id")->result();
			//echo $this->db->last_query();//die();
			//echo "<pre>";print_r($data);die();
			foreach($data as $d){
				$d->keywords = str_replace(","," ",trim($d->keywords));
				$d->variant = str_replace(","," ",trim($d->variant));
				$string = strtolower($d->product_name.' '.$d->variant.' '.$d->keywords.' '.$d->brand_name.' '.$d->category_name.' '.$d->condition_name);
				$string = explode(" ",$string);
				$string = array_unique($string);
				foreach($string as $key=>$value){
					if(strlen($value) < 3 && $value !=""){
						$string[$key] = $value."__";
					}
				}
				$slug = implode(" ",$string);
				$insertData = array("product_id"=>$d->product_id,"sp_id"=>$d->sp_id,"pv_id"=>$d->pv_id,"slug"=>$slug);
				$this->db->insert("tbl_product_slug",$insertData);
			}
		}else{
			return false;
		}
	}
	public function hubxProductPipline($search = "", $request = "", $userid = "",$select=""){
		if($search !=""){
			$search = trim($search);
			$this->db->group_start();
			$this->db->like('description',$search);
			$this->db->or_like('manufacturer',$search);
			$this->db->group_end();
		}
		if($select == ""){
			$select = '*';
		}
		$this->db->select($select)
		->from(DBPREFIX."_hubx_product")
		->where("condition_id !=","0")
		->where("availability !=","0")->order_by("exists","DESC");
		$this->db->limit($request['length'],$request['start']);
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		$data['draw'] = $request['draw'];
		$data['data'] = $query->result();
		if($request['start'] != 0){
			$data['recordsTotal']= $request['recordsTotal'];
			$data['recordsFiltered']= $request['recordsTotal'];
		}else{
			$data['recordsTotal']= $query->num_rows();
			$data['recordsFiltered']= $query->num_rows();
		}
		return $data;

	}
	public function getProducListForHubx($slug="",$pageing=1,$limit=12){
		if($pageing > 1){
			$pageing = (int)$pageing;
			$pageing = ($pageing-1)*$limit;
			$this->db->limit($limit,$pageing);
		}else{
			$this->db->limit($limit);
		}
		$select = "product.product_name,product.product_id,MATCH(product.slug) AGAINST('".$slug."' IN BOOLEAN MODE) as rank,product.is_active";
		$this->db->where("MATCH(product.slug) AGAINST('".$slug."' IN BOOLEAN MODE) > 0",NULL,FALSE);
		$group_by="product.product_id";
		$order = "rank DESC";
		$this->db->select($select, false)
				 ->from(DBPREFIX."_product as product")
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id AND b.blocked = "0"')
				 ->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
				 ->join(DBPREFIX."_categories AS cat",'cat.category_id = pro_cat.category_id AND cat.is_active = "1" AND cat.is_private="0"')
				 ->group_by($group_by)
				 ->order_by($order);
		$query = $this->db->get();
		//echo"<pre>". $this->db->last_query();die();
		$result=$query->result_array();
		return $result;
	}
	public function deleteInventory($id,$approve){
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$data = array("updated_date"=>$date_utc,"approve"=>$approve,"is_warning"=>"0");
		$this->db->where("inventory_id",$id);
		$this->db->update(DBPREFIX."_product_inventory",$data);
		return true;
	}
}
?>