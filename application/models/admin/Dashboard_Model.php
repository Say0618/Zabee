<?php
class Dashboard_Model extends CI_Model{
	function __construct() {
		parent::__construct();
	}
	public function getDashboardDetails(){
		$total_order;
		//SELECT SUM(td.price) AS price FROM tbl_transaction_details td WHERE td.status =1 AND seller_id = "5dc40cd4eaf06"
	}
	public function saveSettings($id,$seller_id,$settings){
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		//$this->db->where(array("seller_id"=>$seller_id));
		$data = array(
			"id" => $id,
			'seller_id' => $seller_id,
			'created_date'  => $date_utc,
			'box_setting'  => implode(",",$settings)
		);
		//print_r($data);die();
		$this->db->replace(DBPREFIX."_dashboard_setting",$data);
	}
	public function getDashboardSettings($seller_id){
		$result = $this->db->select("id,box_setting")->from(DBPREFIX."_dashboard_setting")->where("seller_id",$seller_id)->get()->row();
		return $result;
	}
	public function getDashboardOrderData($status,$seller_id,$is_admin=0){
		$where = array('td.status' => "0", 'td.cancellation_pending' => '0');
		if(!$is_admin){
			$where["td.seller_id"] = $seller_id;
		}
		if($status == 1){
			$this->db->having('(t.created >= DATE(NOW()) - INTERVAL 7 DAY)OR SUM(CASE WHEN td.`status` = 1 THEN 1 ELSE 0 END) > 0');
		}
		if($status == 2){
			$this->db->having('(t.created < DATE(NOW()) - INTERVAL 7 DAY) AND SUM(CASE WHEN td.`status` = 0 THEN 1 ELSE 0 END) > 0');
		}
		$this->db->where($where);
		$result = $this->db->select("t.created,t.order_id,SUM(td.price) AS amount")->from(DBPREFIX."_transaction t")
		->join(DBPREFIX."_transaction_details td","td.order_id=t.order_id")->group_by("t.order_id")->order_by("t.created","DESC")->limit("5")->get();
		//echo $this->db->last_query();
		if($result->num_rows() > 0){
			$return = array("code"=>200,"status"=>"success","result"=>$result->result());
		}else{
			$return = array("code"=>0,"status"=>"error","result"=>array());
		}
		return $return;
	}
	public function getDashboardRequestData($seller_id,$is_admin=0){
		$select = "request_for, request_name, request_info, status";
		if($is_admin){
			$select .= ",created_by,id";
		}else{
			$this->db->where("user_id",$seller_id);
		}
		$result = $this->db->select($select)->from(DBPREFIX."_requests")->limit(5)->get();
		if($result->num_rows() > 0){
			$return = array("code"=>200,"status"=>"success","result"=>$result->result());
		}else{
			$return = array("code"=>0,"status"=>"error","result"=>array());
		}
		return $return;
	}
	public function getDashboardInventoryData($seller_id,$approve="",$is_admin=0){
		if(!$is_admin){
			$this->db->where('pi.seller_id', $seller_id);
		}	
		if($approve == ""){
			$this->db->where("pi.approve != '3'",NULL);
		}else if($approve == "delete"){
			$this->db->where("pi.approve","3");
		}else{
			$this->db->where("pi.is_warning","1");
		}
		$select = "";
		if($is_admin == "1") {
			$select = "ss.store_name,";
		}
		$result = $this->db->SELECT("p.`product_name`, pc.`condition_name`, GROUP_CONCAT(DISTINCT vc.v_cat_title, ':', v.v_title) AS variant, pi.quantity ,pi.`price`,pi.is_warning")
					->FROM("tbl_product_inventory as `pi`")
					->JOIN(DBPREFIX.'_product_variant AS pv','pi.`product_variant_id` = pv.`pv_id`','left')
					->JOIN(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
					->join(DBPREFIX."_variant_category AS vc",'vc.v_cat_id = v.v_cat_id','LEFT')
					->join(DBPREFIX."_product_conditions AS pc",'pc.condition_id =  pi.condition_id','LEFT')
					->join(DBPREFIX.'_seller_store as ss','ss.seller_id = pi.seller_id')
					->join(DBPREFIX.'_product as p','p.product_id = pi.product_id')
					->group_by("pv.pv_id")
					->order_by("pi.updated_date,pi.created_date,pi.inventory_id","DESC")->limit(5)->get();
		//echo $this->db->last_query();die();
		if($result->num_rows() > 0){
			$return = array("code"=>200,"status"=>"success","result"=>$result->result());
		}else{
			$return = array("code"=>0,"status"=>"error","result"=>array());
		}
		return $return;			
	}
	public function getDashboardProductData($seller_id,$approve="",$is_admin=0){
		if(!$is_admin){
			$this->db->where('p.created_id', $seller_id);
		}	
		if($approve){
			$this->db->where("p.approve = '".$approve."'",NULL);
		}
		$result = $this->db->SELECT(" p.product_name,c.category_name,b.brand_name,ss.store_name")
					->FROM(DBPREFIX."_product p")
					->JOIN(DBPREFIX.'_categories AS c','c.category_id = p.sub_category_id')
					->join(DBPREFIX."_brands AS b",'b.brand_id = p.brand_id')
					->join(DBPREFIX.'_seller_store as ss','ss.seller_id = p.created_id')
					->order_by("p.created_by,p.updated_date","DESC")->limit(5)->get();
		//echo $this->db->last_query();die();
		if($result->num_rows() > 0){
			$return = array("code"=>200,"status"=>"success","result"=>$result->result());
		}else{
			$return = array("code"=>0,"status"=>"error","result"=>array());
		}
		return $return;			
	}
}	
?>