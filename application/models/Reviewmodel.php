<?php
class Reviewmodel extends CI_model{

    public function getdata($seller_product_id,$product_variant_id,$id,$limit="",$select="",$review_id=""){
		if($select == ""){
			$select = 'r.review_id,r.date,u.email,u.user_pic,r.product_name,r.pv_id,r.product_id,r.review,r.rating,r.buyer_id,r.seller_id,r.order_id,r.sp_id,u.firstname as name, GROUP_CONCAT(rm.thumbnail) as review_img,r.is_fake,r.is_fake AS fake,r.name AS review_name';
		}
		$this->db->select($select)
				 ->from('tbl_product_reviews r')
				 ->join('tbl_users u','u.userid = r.buyer_id')
				 ->join('tbl_review_media rm','rm.review_id = r.review_id', 'LEFT')
				 ->where('r.product_id', $id)
				 ->where('r.sp_id',$seller_product_id)
				 ->where('r.is_delete',"0")
				 //->where('r.pv_id',$product_variant_id)
				 ->group_by("r.review_id")
				 ->order_by('r.date', "desc");
		if($limit){
			$this->db->limit($limit);
		}				 
		if($review_id){
			$this->db->where("r.review_id",$review_id);
		}
		 $query = $this->db->get();
		//  echo"<pre>".$this->db->last_query(); die();
		 $result['total'] = $query->num_rows();
		 $result['result'] = $query->result_array();
		 return $result;
	}

	public function getReviewsbySeller($seller_id,$select="*",$limit=""){
		$this->db->select($select)
				 ->from('tbl_product_reviews')
				 ->where('seller_id', $seller_id)
				 ->order_by('date', "desc");
		if($limit){
			$this->db->limit($limit);
		}				 
		$query = $this->db->get();
		$result['total'] = $query->num_rows();
		$result['result'] = $query->result_array();
		return $result; 
	}

	public function insert($data) {
		if($this->db->insert('tbl_product_reviews', $data)){
			return $this->db->insert_id(); 
		} else {
			return false;
		}
    }
    
    function getUserByEmail($email,$pdt)
	{
		$this->db->where("email",$email);
		$this->db->where("product_id",$pdt);
		$result = $this->db->get("tbl_product_reviews");
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}else{
			return FALSE;			
		}
	}	

	public function avgRating($product_id,$seller_product_id,$product_variant_id){
		$this->db->select('COUNT(review_id) AS total_review,AVG(rating) AS avg_rating')->from('tbl_product_reviews')
				 ->where('product_id', $product_id)
				 ->where('sp_id',$seller_product_id)
				 ->where('is_delete','0')
				 ->order_by('date', "desc");
		$query = $this->db->get();
		// echo"<pre>"; print_r($this->db->last_query()); die();
		$result['row'] = $query->num_rows();
		$result['result'] = $query->row();
		return $result;
	}	

	public function questionsAsked($product_id,$seller_product_id,$product_variant_id){
		$this->db->select('COUNT(id) AS total_questions')->from('tbl_questions')
				 ->where('product_id', $product_id)
				 ->where('sp_id',$seller_product_id);
		$query = $this->db->get();
		$result['row'] = $query->num_rows();
		$result['result'] = $query->row();
		return $result;
	}	

	public function checkReview($order_id, $pv_id, $from = ""){
		
		$this->db->select("review.*")->from(DBPREFIX."_product_reviews AS review")->where(array("review.pv_id"=>$pv_id, "review.order_id"=>$order_id));
		if($from != ""){
			$this->db->select("GROUP_CONCAT(media.picture) AS pictures, GROUP_CONCAT(media.thumbnail) AS thumbnail")
			->join(DBPREFIX."_review_media AS media", "review.review_id = media.review_id", "LEFT")
			->group_by('review.pv_id');
			return $this->db->get()->result();
			// echo "<pre>".$this->db->last_query(); die();
		}else{
			return $this->db->get()->num_rows();
		}
	}
}