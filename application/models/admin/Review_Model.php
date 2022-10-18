<?php
class Review_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	public function getReviewByID($search, $offset = 0, $length = 100, $review_id = ''){
		$this->datatables->select('r.review_id as id,r.product_name, ss.store_name,r.name ,r.email, r.review, r.rating, r.date AS date')
			 ->from(DBPREFIX.'_product_reviews as r')->join('tbl_seller_store as ss','ss.seller_id = r.seller_id');
		$this->datatables->like('ss.store_name', $search);
		$this->db->or_like('r.product_name', $search);	
		$this->datatables->where('r.is_fake', "1");
		$this->datatables->where('r.is_delete', "0");	
		
		$this->db->order_by('r.review_id', 'DESC');
		$this->db->limit($length,  $offset);			
		if($review_id != '')
			$this->datatables->where('r.review_id', $brand_id);	
	}

	public function getStoreData($product_id){
		$return = $this->db->select("ss.seller_id,ss.store_name")->from(DBPREFIX."_seller_product as sp")
		->join(DBPREFIX."_seller_store as ss","ss.seller_id=sp.seller_id")
		->where(array("sp.product_id"=>$product_id,"sp.is_active"=>"1","sp.approve"=>"1"))
		->group_by("sp.seller_id")->get()->result();
		//echo $this->db->last_query();die();
		return $return;
	}
	public function getCondtionData($product_id,$seller_id){
		$return = $this->db->select("sp.sp_id,pc.condition_id,pc.condition_name")
		->from(DBPREFIX."_seller_product sp")
		->join(DBPREFIX."_product_conditions as pc","pc.condition_id = sp.condition_id")
		->where(array("sp.product_id"=>$product_id,"sp.seller_id"=>$seller_id))->get()->result();
		//echo $this->db->last_query();die();
		return $return;
	}
	public function getVariantData($product_id,$seller_id,$condition_id,$sp_id){
		$result = $this->db->select("GROUP_CONCAT(distinct v.v_title) as variant,pv.pv_id")
		->from("tbl_product_variant as pv")
		->join("tbl_variant as v","FIND_IN_SET(v.v_id,pv.variant_group) >0")
		->where(array("pv.product_id"=>$product_id,"pv.seller_id"=>$seller_id,'pv.condition_id'=>$condition_id))->group_by("pv.pv_id")->get()->result();
		if(!$result){
			$result2 = $this->db->select("pv_id")->from(DBPREFIX."_product_variant")->where(array("sp_id"=>$sp_id,"product_id"=>$product_id))->get()->row();
			$return = array("status"=>2,"data"=>$result2);
		}else{
			$return = array("status"=>1,"data"=>$result);
		}
		return $return;
	}

}	
?>