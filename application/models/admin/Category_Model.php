<?php
class Category_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	///////     ----------------------- Categories -----------------------------      ////////
	function getParentCategories($category_id = "",$select = "",$status = "", $where = "")
	{
		if(is_array($category_id)){
			$where = " AND categories.category_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = " AND categories.category_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "categories.*";
		}
		
		if($status != ""){
			$where .= " AND categories.display_status = '".$status."' AND categories.is_active = '1' ";
		}
		
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_categories as categories
				WHERE parent_category_id = '0' ".$where." AND is_active = '1' ORDER BY category_id DESC";	
		$result = $this->db->query($sql);
		// echo "<pre>"; print_r($this->db->last_query()); die();
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}

	function allChildCategories($id){
		$this->db->select('*')
			->from(DBPREFIX."_categories")
			->where('parent_category_id',$id);
			$this->db->order_by('category_id','DESC');
			$result = $this->db->get();
			if($result && $result->num_rows()>0){
				return $result->result_array();			
			} else {
				return array();
			}
	}

	function allsubCategories($id){
		$where = array('category_id !='=> $id ,'display_status' => '1', 'is_active' =>'1');
		$this->db->select('*')
		->from(DBPREFIX."_categories")
		->where($where);
		$this->db->order_by('category_id','DESC');
		$result = $this->db->get();
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}

	function allCategories($id){
		$where = array('category_id !='=> $id ,'parent_category_id !='=>$id,'display_status' => '1', 'is_active' =>'1');
		$this->db->select('*')
			->from(DBPREFIX."_categories")
			->where($where);
			$this->db->order_by('category_id','DESC');
		$result = $this->db->get();
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}	

	function getCat($category_id = "",$select = "",$status = "", $where = "")
	{
		if(is_array($category_id)){
			$where = " AND categories.category_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = " AND categories.category_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "categories.*";
		}
		
		if($status != ""){
			$where .= " AND categories.display_status = '".$status."'";
		}
		
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_categories as categories
				WHERE is_active = '1'".$where." ORDER BY category_id DESC";	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}
	
	function insertCategory($data, $return = false)
	{
		if(!(isset($data[0])))$data[0] = $data;
		$this->db->insert_batch(DBPREFIX."_categories", $data); 
		if($return){
			$this->db->cache_delete('default', 'index');
			return $this->db->insert_id();
		}
		return TRUE;
	}
	
	function deleteCategory($category_id,$data)
	{
		$arr = array('category_id' => $category_id);		
		$this->db->where($arr);
		$this->db->or_where('parent_category_id',$category_id); 
		$this->db->update(DBPREFIX."_categories", $data);
		$in = array($category_id.",",",".$category_id);
		$this->db->where_in('parent_category_id',$in);
		
		$sql = "SELECT * FROM ".DBPREFIX."_categories 
			WHERE 
			parent_category_id LIKE '%,".$category_id."' OR
			parent_category_id LIKE '".$category_id.",%' OR
			parent_category_id LIKE '%,".$category_id.",%' 
		";
		$result  = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			foreach($result->result_array() as $categories){
				$id = $categories["category_id"];
				$parent = $categories["parent_category_id"];
				$parent = explode(",",$parent);
				$index = array_search($category_id,$parent);
				unset($parent[$index]);
				$parent = implode(",",$parent);
				$sql = "UPDATE ".DBPREFIX."_categories
					SET parent_category_id = '".$parent."'
					WHERE category_id = '".$id."'
				";
				$this->db->query($sql);
			}
		}
		$this->db->cache_delete('default', 'index');
		return TRUE;
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
		$arr = array('category_id' => $category_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_categories", $data[0]);	
		$this->db->cache_delete('default', 'index');	
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
		
		$sql = "SELECT *
			FROM ".DBPREFIX."_categories  as c1
			WHERE c1.parent_category_id != '0'".$where." ORDER BY c1.category_id DESC";
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

	function getCategories($category_id = "",$select = "",$status = "", $where = "", $return = false)
	{
		if(is_array($category_id)){
			$where = " categories.category_id IN ('".implode("','",$category_id)."')";
		}else if($category_id){
			$where = "categories.category_id = '".$category_id."'";
		}		
		if($select == ""){
			$select = "*";
		}
		
		if($status != ""){
			if($where){
				$where .= " AND categories.display_status = '".$status."'";
			}else{
				$where .= " categories.display_status = '".$status."'";
			}
		}
		
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_categories as categories
				WHERE ".$where;	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();
		} else {
			return FALSE;
		}	
	}

	public function getCategoriesById($search, $offset, $length, $category_id = '')
	{
		$this->datatables->select('c.category_id as id,c.position, category_name, category_link, category_image, display_status,show_homepage')
			 ->from('tbl_categories as c');
		if($category_id){
			$this->datatables->where('parent_category_id',$category_id)->where('c.is_active', "1");
		}else{
			$this->datatables->where('parent_category_id',"0")->where('c.is_active', "1");
		}
		$this->datatables->like('category_name', $search);
		$this->db->order_by('c.position');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		}
	}	

	public function getChildCategories2($search, $offset, $length, $category_id = '')
	{
		$this->datatables->select('sc.category_id as id, sc.category_name AS category_name, pc.category_name AS parent_category, sc.category_image, sc.display_status')
			->from('tbl_categories AS sc')
			->join('tbl_categories AS pc', 'sc.parent_category_id = pc.category_id')
			->where('sc.parent_category_id !=', 0)
			->where('sc.is_active !=', '0');
		  $this->datatables->like('sc.category_name', $search);
		  $this->db->order_by('sc.category_id', 'DESC');
		  if($length != -1){
			$this->db->limit($length,  $offset);		
		  }
	}

	public function getCategoriesByIdforEdit($category_id = ''){
		$this->db->select('*')
			 ->from("tbl_categories")
			 ->where('category_id', $category_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}
		else{
			return FALSE;			
		}
	}

	public function cat_add($data, $user_id){
		$category_name = $data['category_name'];
		$is_private = isset($data['cat_privacy'])?'1':'0';
		$this->db->select("category_name")
			 ->from('tbl_categories')
			 ->where('category_name', $category_name)
			 ->where('is_active', '1')
			 ->where('created_id', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows() > 0){
			return false;
		} else {
			$insert_data = array(
				'created_date' => date('Y-m-d H:i:s'),
				'category_name' => $data['category_name'],
				'display_status' => $data['catdisplay_status'],
				'is_private' => $is_private,
				'created_id' => $user_id,
				'category_image' => $data['img']
				);
			$this->db->insert('tbl_categories', $insert_data);
			if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');	
				return true;
			} else {
				return false;
			}
		}
	}

	public function cat_update($data,$category_id = '', $user_id,$parent_category_id=""){
		$editcategory_name = $data['category_name'];
		if($parent_category_id){
			$this->db->where("parent_category_id",$parent_category_id );
		}
		$query = $this->db->select("category_name")
			 ->from('tbl_categories')
			 ->where('category_name', $editcategory_name)
			 ->where('category_id !=',$category_id)
			 ->where('is_active',"1")
			 ->where('created_id', $user_id)->get();
		$nor = $query->num_rows();
		$result = ($nor>0)?$query->row():array();
		if($nor > 0 && $editcategory_name == $result->category_name){
			return false;
		} else {
			if($category_id){
				$insert_data = array(
					'updated_date' => date('Y-m-d H:i:s'),
					'category_name' => $data['category_name'],
					'category_link' => $data['category_url'],
					'slug' => $data['slug'],
					'is_private' => $data['cat_privacy'],
					'parent_category_id' => $data['parent_category_id'],
					'is_homepage' => $data['is_homepage']);
				$this->db->where('sub_category_id', $category_id)->update('tbl_product', array("is_private"=>$data['cat_privacy']));
				$this->db->where('category_id', $category_id)->update('tbl_categories', $insert_data);
			}else{
				$query = $this->db->select("position")
					->from('tbl_categories')
					->where('parent_category_id',"0")
					->where('is_active',"1")->order_by("position","desc")->limit(1)->get();
				$result = $query->row();
				$position = ($result->position+1);
				$insert_data = array(
					'created_date' => date('Y-m-d H:i:s'),
					'category_name' => $data['category_name'],
					'category_link' => $data['category_url'],
					'slug' => $data['slug'],
					'is_private' => $data['cat_privacy'],
					'created_id' => $user_id,
					'is_homepage' => $data['is_homepage'],
					'parent_category_id' => $data['parent_category_id'],
					'position' =>$position);
				$this->db->insert('tbl_categories', $insert_data);
			}
			if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');	
				return true;
			} else {
				return false;
			}
		}
	}

	public function subCategory_add($data, $user_id){
		$subcategory_name = $data['subcategory_name'];
		$this->db->select("category_name")
			 ->from('tbl_categories')
			 ->where('category_name', $subcategory_name)
			 ->where('is_active', '1')
			 ->where('created_id', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows() > 0){
			return false;
		} else {
			$insert_data = array(
				'created_id' => $user_id,
				'created_date' => date('Y-m-d H:i:s'),
				'category_name' => $data['subcategory_name'],
				'category_image' => $data['img'],
				'display_status' => $data['subdisplay_status'],
				'parent_category_id' => $data['parent_category_id'],
				'is_private' => ($data['cat_privacy'] == 'on')?"1":"0",
				);
				$this->db->insert('tbl_categories', $insert_data);
				if($this->db->affected_rows() > 0){
					$this->db->cache_delete('default', 'index');
					return true;
				} else {
					return false;
				}
		}	
	}

	public function subcat_update($data, $category_id = '', $user_id){
		$editsubcategory_name = $data['editsubcategory_name'];
		$query = $this->db->select("category_name")
			 ->from('tbl_categories')
			 ->where('category_name', $editsubcategory_name)
			 ->where('category_id !=',$category_id)
			 ->where('is_active !=',"0")
			 ->where('created_id', $user_id)->get();
		$nor = $query->num_rows();
		$result = ($nor>0)?$query->row():array();
		if($nor > 0 && $editsubcategory_name == $result->category_name){
			return false;
		} else {
		$insert_data = array(
			'updated_date' => date('Y-m-d H:i:s'),
			'category_id' => $category_id,
			'category_name' => $data['editsubcategory_name'],
			'display_status' => $data['editdisplay_status'],
			'is_private' => ($data['cat_privacy'] == 'on')?"1":"0",
			'parent_category_id' => $data['editparent_category_id']
			);
			if(isset($data['img'])){
				$insert_data['category_image'] =  $data['img'];
			}			
			$this->db->where('category_id', $category_id)->update('tbl_categories', $insert_data);
			if($this->db->affected_rows() > 0){
				$this->db->cache_delete('default', 'index');
				return true;
			} else {
				return false;
			}
		}
	}

	function delete_category($category_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('category_id', $category_id)
				 ->update('tbl_categories', $insert_data);	 
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	function delete_subcategory($subcategory_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('category_id', $subcategory_id)
				 ->update('tbl_categories', $insert_data);		 
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	function hard_delete_category($category_id, $user_id){
		$insert_data = array(
			'is_active' => '0'
		);
		$this->db->where('created_id', $user_id)
				 ->where('category_id', $category_id)
				 ->update('tbl_categories', $insert_data);
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	function delete_subcategory_soft($subcategory_id, $user_id){
		$checkStatus = $this->checkSubcatIdInProduct($subcategory_id);
		if($checkStatus == ""){
			$insert_data = array(
			'is_active' => '0'
			);
			$this->db->where('category_id', $subcategory_id)
					 ->update('tbl_categories', $insert_data);		 
			if($this->db->affected_rows() > 0){
				$this->db->cache_delete('default', 'index');
				return 'success';
			} else {
				return false;
			}
		} else {
			return 'failed';
		}
	}
		function catChange($data){
			$flag = false;
			foreach($data as $d){
				$c= explode('-',$d);
				$insert_data = array(
					'category_id' => $c[1]
					);
				$this->db->where('product_id',$c[0])
						->update('tbl_product_category',$insert_data);
						if($this->db->affected_rows() > 0){
							$this->db->cache_delete('default', 'index');
							$flag = true;
						}
				}
			return $flag;
			}

	function parentCatChange($pCats){
		$flag = false;
		foreach($pCats as $pc){
			$p = explode('-',$pc);
			$insert_data = array(
			'parent_category_id' => trim($p[1])
			);
			$this->db->where('category_id',trim($p[0]))
					->update('tbl_categories',$insert_data);
			if($this->db->affected_rows() > 0){
				$this->db->cache_delete('default', 'index');
				$flag = true;
			}
		}
		return $flag;
	}

	function checkSubcatIdInProduct($subcat_id){
		$this->db->select('p.product_id,p.product_name')
			 ->from("tbl_product_category pc")
			 ->join("tbl_product p","p.product_id = pc.product_id")
			 ->where('pc.category_id', $subcat_id)
			 ->group_by("p.product_id");
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{ 
			return FALSE;			
		}
	}

	function deleteImage($category_id){
		$insert_data = array(
			'category_image' => NULL
		);
		$this->db->where('category_id', $category_id)
				 ->update('tbl_categories', $insert_data);
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}
	function show_on_homepage($category_id, $value){
		$insert_data = array(
			'show_homepage' => $value
		);
		$this->db->where('category_id', $category_id);
		if($this->db->update('tbl_categories', $insert_data)){
			return true;
		} else {
			return false;
		}
	}
	
	public function searchCategory($term)
	{
		$this->db->select('sc.category_id as id, sc.category_name AS category_name, pc.category_name AS parent_category, sc.category_image, sc.display_status')
			->from('tbl_categories AS sc')
			->join('tbl_categories AS pc', 'sc.parent_category_id = pc.category_id', 'left')
			->where('sc.is_active !=', '0');
		$this->db->like('sc.category_name', $term);
		$this->db->order_by('sc.category_id', 'DESC');
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else{
			return array();			
		}
	}
}	
?>
