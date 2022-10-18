<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
class Chatbot extends CI_Controller {
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->model('Product_Model');
		$this->load->model('User_Model');
		$this->load->model('Utilz_Model');
		$this->data['media_url'] = $this->config->item('product_path');
		$this->data['media_url_thumb'] = $this->config->item('product_thumb_path');
		$this->data['profile_path'] = $this->config->item('profile_path');
		$this->data['review_url'] = $this->config->item('image_url').'review/';
		$this->lang->load('english','english');
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
	public function get_order_detail_list(){
		$user_id = $this->input->post("user_id");
		$order_id = $this->input->post("order_id"); 
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		if(!$order_id){
			echo json_encode(array('status'=>0,'msg'=>"Order Id  Is Missing.","code"=>2));
			exit;
		}
		$system_date = date_create(date('Y-m-d h:i:s A'));
		$order_list = $this->Utilz_Model->get_order_list_for_chatbot($user_id, $order_id);
		if(!empty($order_list)){
			foreach($order_list as $od){
				$order_date = date_create($od->created." A");
				$days = date_diff($order_date, $system_date)->days;
				if($od->status == 0 && $od->item_received_confirmation == 0 && $od->cancellation_pending == "0" &&  $od->is_cancel == "0"){
					if($days < 7){
						$od->status = $this->lang->line('pending');
					}else{
						$od->status = "Order Cancelled";
					}
				} else if($od->status == 1 && $od->item_received_confirmation != 1 && $od->cancellation_pending == "0") {
					$od->status = $this->lang->line('proceed');
				} else if($od->order_status == 1 && $od->item_confirm_status == 1){
					$od->status = $this->lang->line('order_rec');
				} else if($od->cancellation_pending == "1" &&  $od->is_cancel == "0"){
					$od->status = "Cancel Requested";
				} else if($od->cancellation_pending == "1" &&  $od->is_cancel == "1"){
					$od->status = "Cancelled";
				} else if($od->cancellation_pending == "1" &&  $od->is_cancel == "2"){
					$od->status = "Not Cancelled";
				}else if($od->order_status == "3"){
					$od->status = "Refunded";
				} else{
					$od->status = $this->lang->line('declined');
				}
				unset($od->cancellation_pending);
				unset($od->is_cancel);
				unset($od->item_received_confirmation);
				unset($od->created);
			}
		}
		echo json_encode($order_list);
	}
	public function check_user_login(){
		$this->load->library('session');
		if($this->session->userdata('userid')){
			echo json_encode(array("status"=>1,"msg"=>"yes","user_id"=>$this->session->userdata('userid')));
		}else{
			echo json_encode(array("status"=>0,"msg"=>"no","user_id"=>""));
		}
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
	public function get_order_list(){
		$user_id = $this->input->post("user_id");
		$order_id = $this->input->post("order_id"); 
		if(!$user_id){
			echo json_encode(array('status'=>0,'msg'=>"User Id  Is Missing.","code"=>2));
			exit;
		}
		$this->load->model("Cart_Model");
		if($this->input->post('page')){
			$page = $this->input->post('page');
		}else{
			$page = "";//1;
		}
		if($this->input->post('limit')){
			$limit = $this->input->post('limit');
		}else{
			$limit = "";//2;
		}
		$table_name = "tbl_transaction t";
		$order_by = 't.created DESC';
		$select = "UNIX_TIMESTAMP(t.created) as created,t.order_id,t.status,td.gross_amount";
		$pageing = //($page-1)*$limit;
		$join = array("tbl_transaction_details td"=>"td.order_id = t.order_id");
		$where = array('t.user_id'=>$user_id);
		$order_list = $this->Utilz_Model->getAllData($table_name, $select, $where,0,"",$order_by,$join,array($limit,$pageing));
		echo json_encode($order_list);
	}
}
?>