<?php
class Product_model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	///////     ----------------------- Product -----------------------------      ////////
	function getProduct($product_id = "",$select = "",$where = "")
	{
		if(is_array($product_id)){
			$where = " AND product.product_id IN ('".implode("','",$product_id)."')";
		}else if($product_id){
			$where = " AND product.product_id = '".$product_id."'"; 
		}		
		if($select == ""){
			$select = "product.`product_name`,product.`product_description`,cat.category_name,b.brand_name,sp.upc_code,sp.seller_sku, sp.price,sp.sell_quantity,sp.discount,sp.is_return,v.`v_title`,users1.userid AS userid1,users1.firstname AS name1 ";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_product as product
				LEFT JOIN ".DBPREFIX."_seller_product AS sp ON sp.`product_id` = product.`product_id` 
			  	LEFT JOIN ".DBPREFIX."_seller_product_variant AS spv ON sp.`sp_id` = spv.`sp_id` 
				LEFT JOIN ".DBPREFIX."_variant AS v ON v.`v_id` = spv.`v_id`
				LEFT JOIN ".DBPREFIX."_brands AS b ON b.`brand_id` = product.brand_id 
    			LEFT JOIN ".DBPREFIX."_categories AS cat ON (cat.category_id = product.category_id OR cat.category_id = product.sub_category_id)
				LEFT JOIN ".DBPREFIX."_users as users1 ON product.created_id = users1.userid
				WHERE ".$where;	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
		
	}
	
	function deleteProduct($product_id,$data)
	{
		$arr = array('product_id' => $product_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_product", $data);
		return TRUE;
	}
	
	/**
	* this function is common for product and subproduct
	* @param undefined $product_id
	* @param undefined $data
	* 
	*/

	function updateProduct($product_id,$data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$arr = array('product_id' => $product_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_product", $data[0]);
		return TRUE;
	}
	
	function updateProducts($data)
	{
		$this->db->update_batch(DBPREFIX."_product", $data, 'product_id'); 
		return TRUE;
	}

	public function getConditionData($select = "",$where = "")
	{
		if($select == ""){
			$select = "conditions.*";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_product_conditions as conditions
				WHERE is_delete='0' AND active='1' ".$where;	
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
				WHERE deleted_id IS NULL AND is_active='1' ".$where;	
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
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_variant as variant
				WHERE deleted_id IS NULL AND is_active='1' ".$where;	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
	}

	function productPipline($search = "", $request = ""){
		$userid = $this->session->userdata('userid');
		if($search ==""){
			$this->db->select("product.product_name, product.product_description, cat.category_name, b.brand_name,pc.`condition_name`, sp.upc_code, sp.seller_sku, sp.price,sp.sell_quantity,sp.discount,sp.is_return,GROUP_CONCAT(v.v_title) AS v_title,users1.userid AS userid1,users1.firstname AS name1, sp.sp_id")
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id')
			->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id')
			->join(DBPREFIX."_categories AS cat",'cat.category_id = product.category_id OR cat.category_id = product.sub_category_id')
			->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')   
			->join(DBPREFIX."_users as users1",'product.created_id = users1.userid')
			->where('product.created_id',$userid)
			->group_by('pc.condition_name');
			if($request['start'] != 0){
				$this->db->limit($request['length'],$request['start']); 
			}
			$query = $this->db->get();
		}else{
			$search = trim($search);			
			$this->db->select("a.id,CONCAT(s.first_name,' ',s.last_name) AS student_name,CONCAT(t.first_name,' ',t.last_name) AS teacher_name,a.active,DATE_FORMAT(ad.note_for_lesson, '%Y/%m/%d') AS note_for_lesson,a.invite_status,s.email AS student_email")
			->from('tbl_assignment a')
			->join('tbl_student s','a.student_id = s.id')
			->join('tbl_teacher t','a.teacher_id = t.id OR t.id = a.teacher_sub')
			->join('tbl_assignment_details ad','a.id= ad.assignment_id')
			->where(array('a.isDelete'=>0,'s.isDelete'=>0,'t.isDelete'=>0))
			->where('((CONCAT(s.first_name," ",s.last_name) LIKE "'.stripslashes($search).'%") OR (CONCAT(s.first_name," ",s.last_name) LIKE "%'.$search.'%") OR (CONCAT(s.first_name," ",s.last_name) LIKE "'.$search.'%") OR (CONCAT(s.first_name," ",s.last_name) LIKE "%'.$search.'%")) OR ((ad.note_for_lesson LIKE "'.stripslashes($search).'%") OR (ad.note_for_lesson LIKE "%'.$search.'%")) OR ((CONCAT(t.first_name," ",t.last_name) LIKE "'.stripslashes($search).'%") OR (CONCAT(t.first_name," ",t.last_name) LIKE "%'.$search.'%") OR (CONCAT(t.first_name," ",t.last_name) LIKE "'.$search.'%") OR (CONCAT(t.first_name," ",t.last_name) LIKE "%'.$search.'%"))')
			->group_by('a.id')->order_by('a.id','DESC');
			$query = $this->db->get();
		}
		// echo $this->db->last_query(); die();
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

	public function frontProductDetails($product_id="", $created_id="", $condition_id="", $vaiant_id=""){
		$userid = $this->session->userdata('userid');
		$this->db->select('product.product_id, product.product_name, product.product_description, GROUP_CONCAT(DISTINCT CONCAT(\'{id:"\', cat.category_id, \'", value:"\',cat.category_name,\'"}\')) AS category,
    GROUP_CONCAT(DISTINCT CONCAT(\'{id:"\', b.brand_id, \'", value:"\',b.brand_name,\'"}\')) AS brand,
    GROUP_CONCAT(DISTINCT CONCAT(\'{id:"\', pc.condition_id, \'", value:"\', pc.condition_name,\'"}\')) AS conditions,, sp.upc_code, sp.seller_sku, sp.price, sp.sell_quantity, sp.discount, sp.is_return ,GROUP_CONCAT(CONCAT(\'{id:"\', v.v_id, \'", value:"\', v.v_title,\'" , category:"\',vc.v_cat_title,\'"}\')) AS variant, users1.userid AS userid1, users1.firstname AS name1', false)
			->from(DBPREFIX."_product as product")
			->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
			->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id')
			->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id')
			->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id')
			->join(DBPREFIX."_categories AS cat",'cat.category_id = product.category_id OR cat.category_id = product.sub_category_id')
			->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')   
			->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id')
			->join(DBPREFIX."_users as users1",'product.created_id = users1.userid')
			->group_by('pc.condition_name');
			$query = $this->db->get();
			$data['data'] = $query->result();
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
}	
?>