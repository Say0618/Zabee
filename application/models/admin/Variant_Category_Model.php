<?php
class Variant_Category_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	///////     ----------------------- Categories -----------------------------      ////////
	function getParentVariantCatgories($userid, $category_id = "",$select = "",$status = "", $where = "")
	{
		if(is_array($category_id)){
			$where = " AND categories.v_cat_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = " AND categories.v_cat_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "categories.*, users1.userid as userid1, users2.userid as userid2, users1.firstname as name1, users2.firstname as name2";
		}
		if($status != ""){
			$where .= " AND categories.display_status = '".$status."'";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_variant_category as categories
				LEFT JOIN ".DBPREFIX."_users as users2 ON categories.updated_id = users2.userid
				WHERE is_active ='1'  ".$where;	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}

	function getParentVariantCatgories2($userid, $search, $offset, $length)
	{
		$this->datatables->select('c.v_cat_id as id, c.v_cat_title,c.v_cat_id as vc_id, display_status')
						->from('tbl_variant_category as c');
		$this->datatables->where('c.is_active', '1');	
		$this->datatables->like('v_cat_title', $search);
		$this->db->order_by('c.v_cat_id', 'desc');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		  }
	}

	function getVariantlist($category_id = "",$select = "",$status = "", $where = "")
	{
		if(is_array($category_id)){
			$where = " AND variant.v_cat_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = " AND variant.v_cat_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "variant.*,categories.v_cat_title, users1.userid as userid1, users2.userid as userid2, users1.firstname as name1, users2.firstname as name2";
		}
		if($status != ""){
			$where .= " AND categories.display_status = '".$status."'";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_variant as variant
				LEFT JOIN ".DBPREFIX."_variant_category as categories ON variant.v_cat_id = categories.v_cat_id
				LEFT JOIN ".DBPREFIX."_users as users2 ON variant.updated_id = users2.userid
				WHERE variant.is_active ='1' AND categories.`is_active`='1'  ".$where;	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
	}

	function insertCategory($data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$this->db->insert_batch(DBPREFIX."_variant_category", $data);
		return TRUE;
	}
	
	function insertVariant($data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$this->db->insert_batch(DBPREFIX."_variant", $data);
		return TRUE;
	}
	
	function deleteCategory($category_id,$data)
	{
		$arr = array('v_cat_id' => $category_id);		
		$this->db->where($arr);
		$this->db->update(DBPREFIX."_variant_category", $data);
		return TRUE;
	}
	
	function deleteVariant($category_id,$data)
	{ 
		$this->db->select('sp_id')
				 ->from(DBPREFIX."_seller_product_variant")
				 ->where("v_id",$category_id);
				 $query = $this->db->get();
		if($query->num_rows()>0){
			return false;
		}else{
			$this->db->where('v_id', $category_id);
			$this->db->delete('tbl_variant'); 
			return TRUE;
		}
	}
	/**
	* this function is common for categories and subcategories
	* @param undefined $category_id
	* @param undefined $data
	* 
	*/

	function updateCategory($category_id,$data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$arr = array('v_cat_id' => $category_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_variant_category", $data[0]);		
		return TRUE;
	}
	
	function updateVariant($category_id,$data)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$arr = array('v_id' => $category_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_variant", $data[0]);		
		return TRUE;
	}
	
	///////     ----------------------- Sub Categories -----------------------------      ////////
	function getChildCategories($subcategory_id = "",$parentcategory_id = "",$status = "")
	{
		$where = "";
		if($subcategory_id){
			$where = " AND c1.category_id = '".$subcategory_id."'";
		}else if(is_array($subcategory_id)){
			$where = " AND c1.category_id IN ('".implode("','",$subcategory_id)."')";
		}

		if($parentcategory_id){
			$where = " AND c1.parent_category_id = '".$parentcategory_id."'";
		}else if(is_array($parentcategory_id)){
			$where = " AND c1.parent_category_id IN ('".implode("','",$parentcategory_id)."')";
		}
		
		if($status != ""){
			$where .= " AND c1.display_status = '".$status."'";
		}
		$sql = "SELECT c1.*, users1.admin_id as userid1, users2.admin_id as userid2, users1.admin_name as name1, users2.admin_name as name2
			FROM ".DBPREFIX."_categories  as c1
			LEFT JOIN ".DBPREFIX."_backend_users as users1 
			LEFT JOIN ".DBPREFIX."_backend_users as users2 
			ON c1.updated_id = users2.admin_id
			WHERE c1.parent_category_id != '0'  AND c1.deleted_id is NULL".$where;
		$result = $this->db->query($sql);		
		if($result && $result->num_rows()>0){
			$categoryData = $result->result_array();			
			$parentCategories = array();
			foreach($categoryData as $key=>$category){
				$parentCategories = $this->getParentCategories(explode(",",$category['parent_category_id']),"categories.category_name");
				$arrParent = array();				
				foreach($parentCategories as $parents){
					 $arrParent[] = $parents["category_name"];
				}
				$categoryData[$key]['category_parent'] = implode(", ",$arrParent);
			}
			return $categoryData;
		}
	}

	function getAllParentVariantCatgories($category_id = "",$select = "",$status = "", $where = "")
	{
		if(is_array($category_id)){
			$where = " AND categories.v_cat_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = " AND categories.v_cat_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "categories.*";//, users1.userid as userid1, users2.userid as userid2, users1.firstname as name1, users2.firstname as name2";
		}
		if($status != ""){
			$where .= " AND categories.display_status = '".$status."'";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_variant_category as categories
				WHERE (categories.is_active ='1' OR categories.is_active ='0')  ".$where." ORDER BY categories.v_cat_id DESC";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}

	function getAllVariantlist($category_id = "",$select = "",$status = "", $where = "")
	{
		if(is_array($category_id)){
			$where = " AND variant.v_cat_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = " AND variant.v_cat_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "variant.*,categories.v_cat_title";//, users1.userid as userid1, users2.userid as userid2, users1.firstname as name1, users2.firstname as name2";
		}
		if($status != ""){
			$where .= " AND categories.display_status = '".$status."'";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_variant as variant
				LEFT JOIN ".DBPREFIX."_variant_category as categories ON variant.v_cat_id = categories.v_cat_id
				WHERE (variant.is_active ='1' OR variant.is_active ='0') AND categories.`is_active`='1'  ".$where." ORDER BY variant.v_id DESC";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
	}

	public function cat_add($data, $user_id){
		$v_cat_title = $data['variant_category_name'];
		$this->db->select("v_cat_title")
				->from('tbl_variant_category')
				->where('v_cat_title', $v_cat_title)
				->where('is_active', '1');
		$query = $this->db->get();
		if($query && $query->num_rows() > 0){
			return false;
		} else {
			$insert_data = array(
				'v_cat_title' => $data['variant_category_name'],
				'is_active' => '1'
			);
			$this->db->insert('tbl_variant_category', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function cat_update($data, $user_id, $category_id = ''){
		$v_cat_title = $data['variant_category_name_update'];
		$query = $this->db->select("v_cat_title")
						->from('tbl_variant_category')
						->where('v_cat_title', $v_cat_title)
						->where('v_cat_id !=',$category_id)->get();
		$result = $query->row();
		if(isset($result->v_cat_title) && ($v_cat_title == $result->v_cat_title)){
			return false;
		} else {
			$insert_data = array(
				'v_cat_title' => $data['variant_category_name_update'],
				'is_active' => '1'
			);
			$this->db->where('v_cat_id', $category_id)->update('tbl_variant_category', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function getVariantCategories($category_id){
		$this->db->select('*')
			->from('tbl_variant_category')
			->where('v_cat_id', $category_id)
			->where('is_active', '1');
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;			
		}
	}

	public function countVariants($category_id){
		$where = array('v_cat_id'=>$category_id,'is_active' => '1');
		$this->db->select('COUNT(v_id) AS v_id')
				->from('tbl_variant')
				->where($where);
		$query = $this->db->get()->row();
		return $query;
	}

	function deleteVariantCat($category_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('v_cat_id', $category_id)
				 ->update('tbl_variant_category', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function deleteVar($category_id, $user_id, $value){
		$insert_data = array(
			'is_active' => $value
		);
		$this->db->where('v_id', $category_id)
				 ->update('tbl_variant', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function variant_add($data, $user_id, $var_id){
		$variant_name = $data['variant_name'];
		$this->db->select("v_title")
				->from('tbl_variant')
				->where('v_title', $variant_name)
				->where('is_active', '1');
		$query = $this->db->get();
		if($query && $query->num_rows() > 0){
			return false;
		} else {
			$insert_data = array(
				'v_title' => $data['variant_name'],
				'v_cat_id' => $var_id,
				'is_active' => $data['variant_status']
			);
			$this->db->insert('tbl_variant', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function variant_update($data, $user_id, $var_id, $variantParent){
		$variant_name = $data['variant_name'];
		$query = $this->db->select("v_title")
						->from('tbl_variant')
						->where('v_title', $variant_name)
						->where('v_id !=',$var_id)->get();
		$result = $query->row();
		if(isset($result->v_title) && $variant_name == $result->v_title){
				return false;
		} else {
			$insert_data = array(
				'v_title' => $data['variant_name'],
				'is_active' => $data['variant_status']
			);
			$this->db->where('v_id', $var_id)
					 ->where('v_cat_id', $variantParent)
					 ->update('tbl_variant', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function selectVariants($var_id){
		$this->db->select('*')
			->from('tbl_variant')
			->where('v_id', $var_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;			
		}
	}

	function hard_delete_variantcategory($category_id, $user_id){
		$this->db->select('product_id')
				 ->from(DBPREFIX."_product_variant_category")
				 ->where("v_cat_id",$category_id);
		$query = $this->db->get();
		if($query->num_rows()>0){
			return 'failed';
		} else {
			$this->db->where('v_cat_id', $category_id)->delete('tbl_variant_category');
			if($this->db->affected_rows() > 0){
				return 'success';
			} else {
				return false;
			}
		}
	}	

	function variantCategoryTitleByVariantCatId($v_id){
		$this->db->select('v_cat_title')
					->from('tbl_variant_category')
					->where('v_cat_id',$v_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
				return FALSE;			
			}
	}
}	
?>