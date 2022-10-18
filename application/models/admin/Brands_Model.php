<?php
class Brands_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	///////     ----------------------- brands -----------------------------      ////////
	function getBrands($brand_id = "",$select = "",$status="",$noname = "",$order = "", $where = "")
	{		
		if($brand_id){
			$where = " AND brands.brand_id = '".$brand_id."'";
		}
		if(is_array($brand_id)){
			$where = " AND brands.brand_id IN ('".implode("','",$brand_id)."')";
		}
		if($select == ""){
			$select = "*";
		}
		if($status != ""){
			$where .= " AND brands.display_status = '".$status."'";
		}
		$sql = "SELECT ".$select." FROM ".DBPREFIX."_brands as brands";
		$sql .= " WHERE active='1' AND blocked ='0' AND brand_name IS NOT NULL AND brand_name !=''".$where."  ORDER BY ".$order." brand_id DESC";
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		} else {
			return array();
		}
	}

	function allBrands($id){
		$where = array('brand_id !='=> $id ,'display_status' => '1', 'active' =>'1', 'blocked' =>'0', "brand_name !=" => "");
		$this->db->select('*')
			->from(DBPREFIX."_brands")
			->where($where);
			$this->db->order_by('brand_id','DESC');
			$result = $this->db->get();
			if($result && $result->num_rows()>0){
				return $result->result_array();			
			} else {
				return array();
			}
		}
	
	function insertbrands($data, $return = false)
	{
		//if(!(isset($data[0])))$data[0] = $data;
		$this->db->insert(DBPREFIX."_brands", $data); 
		if($return){
			return $this->db->insert_id();
		}
		return TRUE;
	}

	function updatebrands($brand_id,$data){
		$arr = array('brand_id' => $brand_id);		
		$this->db->where($arr); 
		$this->db->update(DBPREFIX."_brands", $data);
		return TRUE;
	}
	
	public function getBrandsByID($search, $offset, $length, $brand_id = '')
	{
		$this->datatables->select('b.brand_id as id, brand_name, brand_image, IF(CHAR_LENGTH(brand_description) > 50,
									CONCAT(LEFT(brand_description,50), "..."),
									brand_description) brand_description, display_status, blocked')
			 ->from('tbl_brands as b')->where('b.active !=', '0');
		$this->datatables->like('brand_name', $search);	
		$this->datatables->where('brand_name !=', "");	
		
		$this->db->order_by('b.brand_id', 'DESC');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		  }		
		if($brand_id != '')
			$this->datatables->where('brand_id', $brand_id);	
	}

	public function getBrandsByIdforEdit($brands_id = ''){
		$this->db->select('*')
			 ->from("tbl_brands")
			 ->where('brand_id', $brands_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}

	public function brands_update($data,$brands_id = '',$user_id){
		$brand_name = $data['brand_name'];
		$query = $this->db->select("brand_name")
			 ->from('tbl_brands')
			 ->where('brand_name', $brand_name)
			 ->where('brand_id !=',$brands_id)->get();
		$result = $query->row();
		if($brand_name == $result->brand_name){
			return false;
		} else {
			$insert_data = array(
					'brand_id' => $brands_id,
					'brand_name' => $data['brand_name'],
					'brand_description' => $data['brand_description'],
					'display_status' => $data['branddisplay_status'],
				);
			if(isset($data['img'])){
				$insert_data['brand_image'] =  $data['img'];
			}		
			$this->db->where('brand_id', $brands_id)->update('tbl_brands', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function deleteBrands($brand_id,$user_id,$value)
	{
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('brand_id', $brand_id)
				 ->update('tbl_brands', $insert_data);	 
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function hard_delete_brands($brand_id, $user_id){
		$this->db->select('product_id')
				 ->from(DBPREFIX."_product")
				 ->where("brand_id",$brand_id);
				 $query = $this->db->get();
		if($query->num_rows()>0){
			return false;
		}else{
			$this->db->where('brand_id', $brand_id)->delete('tbl_brands');
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}	
	
	function delete_brands_soft($brand_id, $user_id){
		$checkStatus = $this->checkBrandsIdInProduct($brand_id);
		if($checkStatus == ""){
			$insert_data = array(
			'active' => '0'
			);
			$this->db->where('brand_id', $brand_id)
					 ->update('tbl_brands', $insert_data);	 
			if($this->db->affected_rows() > 0){
				return 'success';
			} else {
				return false;
			}
		} else {
			return 'failed';
		}
	}
	
	function checkBrandsIdInProduct($brand_id){
		$this->db->select('product_id,product_name')
			 ->from("tbl_product")
			 ->where('brand_id', $brand_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}


	function brandChange($data){
		foreach($data as $d){
			$c= explode('-',$d);
			$insert_data = array(
				'brand_id' => $c[1]
				);
			$display = array('display_status' => '1');
			$this->db->where('product_id',$c[0])
					->update('tbl_product',$insert_data);
			$this->db->where('brand_id',$c[1])
					->update('tbl_brands',$display);
			}
		return true;
	}

	function brandActiveChange($brand_id, $status){
		$display = array('blocked' => $status);
		$query = $this->db->where('brand_id',$brand_id)
				 ->update('tbl_brands',$display);
		if($query){
			return true;
		}
		return false;
	}
}	
?>