<?php
class Message_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	public function getBuyerList($receiver_id,$limit=""){
		$result['rows'] =0;
		$result['result'] = "";
		if($limit){
			$this->db->limit($limit);
		}
		$query =$this->db->select("m1.message_id,u.userid,CONCAT(u.`firstname`, ' ', u.lastname) AS sender_name,u.user_pic,m1.subject,m1.message, m1.product_variant_id,m1.item_type,m1.seller_id,m1.buyer_id,m1.item_id,m1.seen_datetime,m1.sent_datetime")
						->from('tbl_message m1')
						->join('tbl_users u','(m1.sender_id = u.userid OR m1.receiver_id = u.userid) AND u.userid !="'.$receiver_id.'"',null,false)
						->where('(m1.receiver_id="'.$receiver_id.'" OR m1.sender_id ="'.$receiver_id.'") AND m1.`message_id` IN (SELECT MAX(m.message_id) AS max_id FROM tbl_message m GROUP BY LEAST(m.sender_id, m.receiver_id),GREATEST(m.sender_id, m.receiver_id),m.product_variant_id)',NULL,FALSE)
						->group_by('u.userid,m1.product_variant_id')->order_by('m1.message_id','desc')->get();
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $query->result();
		}
		return $result;		 
	}

	public function getMessages($sender_id, $receiver_id,$desc = 0, $loadLimit =0,$product_variant_id,$item_type,$length=7){
		$result['rows'] =0;
		$result['result'] = "";
		if($desc == 0){
			$desc = "ASC";
		}else{
			$desc = "DESC";
		}
		$query = $this->db->query('SELECT * FROM (SELECT m1.*,u.userid, CONCAT(u.firstname," ", u.lastname) AS sender_name,u.user_pic FROM tbl_message m1 JOIN tbl_users u ON m1.sender_id = u.userid WHERE ((m1.sender_id = "'.$sender_id.'" AND m1.receiver_id = "'.$receiver_id.'") OR (m1.sender_id = "'.$receiver_id.'" AND m1.receiver_id = "'.$sender_id.'")) AND m1.is_delete = "0" AND m1.product_variant_id='.$product_variant_id.' AND m1.item_type="'.$item_type.'" ORDER BY m1.message_id DESC LIMIT '.$loadLimit.','.$length.') tmp ORDER BY tmp.message_id '.$desc);
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $query->result();
		}
		return $result;		
	}

	public function saveData($data,$table){
		if($this->db->insert($table,$data)){
			return $this->db->insert_id();
		}else{
			return "false";
		}
	}
	public function saveSeenDateTime($where,$datetime){
		$result['status'] = 0;
		$query = $this->db->select('seen_datetime')->from('tbl_message')->where($where)->order_by('message_id','desc')->get()->row();	
		if($query && !$query->seen_datetime){
			$this->db->where($where);
			if($this->db->update('tbl_message',$datetime)){
				$result['status'] = 1;
			}
		}
		return $result;	
	}
	public function getMessageProductDetail($pv_id){
		$select = "ss.store_name,`product`.`product_id`,`c`.`category_name`,`b`.`brand_name`,`product`.`product_name`,product.slug";
		$this->db->select($select)
		->from(DBPREFIX.'_product AS product')
		->join(DBPREFIX."_product_category AS pro_cat",'pro_cat.product_id = product.product_id')
		->join(DBPREFIX.'_categories c','c.`category_id` = pro_cat.category_id')
		->join(DBPREFIX.'_brands b','b.`brand_id` = product.`brand_id`')
		->join(DBPREFIX.'_product_inventory pin','pin.product_id=product.product_id AND pin.product_variant_id='.$pv_id)
		->join(DBPREFIX.'_seller_store ss','ss.seller_id = pin.seller_id')
		->group_by('product.`product_id`');
		$product = $this->db->get()->row();
		//echo $this->db->last_query();die();
		return $product;
	}
}
?>