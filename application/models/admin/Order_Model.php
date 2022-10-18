<?php
class Order_model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
	function placeOrder($orderData)
	{
		$this->db->insert(DBPREFIX."_orders",$orderData);
	}
	
	public function orderFilters($seller_id,$date,$to,$search_status,$pd_id){		
			if($seller_id != ""){
				$this->db->where('td.seller_id',$seller_id);
				}
			if($search_status != ""){
					$this->db->where('td.status',$search_status);
				}
			if($pd_id != ""){
					$this->db->where('td.product_id',$pd_id);
				}
			if($date != ""){
				$date = explode('/', $date);
				$date = date('Y-m-d', strtotime($date[2].'-'.$date[1].'-'.$date[0]));
				}
			if($to != ""){
				$to = explode('/', $to);
				$to = date('Y-m-d', strtotime($to[2].'-'.$to[1].'-'.$to[0]));
				}
			if($date!='' && $to == ''){
				$this->db->like('td.created',$date);
				}
			if($date!='' && $to!=''){
				$where = "((td.created BETWEEN'".$date." 00:00:00 'AND'".$to." 23:59:59'))";
				$this->db->where($where);
				}
			$this->db->select('t.order_id,td.id as order_item_id, t.created,t.shipping, t.card_detail,c.condition_name, t.billing,td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,p.`product_description`,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,pv.price,td.status as action, ss.store_name as store, td.hasDiscount, t.voucher_code as code')
					 ->from('tbl_transaction_details AS td')
					 ->join('tbl_transaction AS t','t.order_id = td.order_id')
					 ->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
					 ->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
					 ->join('tbl_product AS p','td.product_id = p.product_id')
					 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
					 ->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
					 ->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
					 ->order_by('t.created','DESC');
			$query = $this->db->get();
			$data['data'] = $query->result();
			return $data;
		}	

	function getOrders($user_id, $order_id = '',$search = "", $request = "",$where,$user_type="2",$select="",$show_warehouse_orders=true, $showActiveOrders = 0){
		if($search !=""){
			$search = trim($search);
			$this->db->where('(p.product_name LIKE "'.stripslashes($search).'%") OR (t.order_id LIKE "%'.$search.'%")');
		}
		if($user_type == "1" && $show_warehouse_orders){
			$this->db->join('tbl_warehouse AS w',' w.warehouse_id = td.warehouse_id AND w.`user_id` = "'.$user_id.'"');
		}	
		if($where){
			$this->db->where($where);
		}
		if($select == ""){
			$select = "t.order_id,td.id as order_item_id, t.created,t.shipping,sp.condition_id,c.condition_name, t.billing, t.card_detail, td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,pv.price,td.status as action, SUM(td.item_gross_amount) AS seller_total,ss.store_name,td.hubx_id";
		}
		$this->db->select($select)
		->from('tbl_transaction_details AS td')
		->join('tbl_transaction AS t','t.order_id = td.order_id')
		->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
		->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
		->join('tbl_product AS p','td.product_id = p.product_id')
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
		->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
		->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
		->group_by('td.id')
		->order_by('t.created','DESC');
		if($request['start'] != -1){
			$this->db->limit($request['length'],$request['start']); 
		}
		if($showActiveOrders == 1){
			$this->db->having('(t.created >= DATE(NOW()) - INTERVAL 7 DAY)OR SUM(CASE WHEN td.`status` = 1 THEN 1 ELSE 0 END) > 0');
		}
		if($showActiveOrders == 2){
			$this->db->having('(t.created < DATE(NOW()) - INTERVAL 7 DAY) AND SUM(CASE WHEN td.`status` = 0 THEN 1 ELSE 0 END) > 0');
		}
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

	function getSaleView($order_id)
	{
		$this->db->select('t.order_id,td.id as order_item_id, t.created,t.shipping, t.card_detail,sp.condition_id,c.condition_name, t.billing,td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,td.price,td.status as action, user.email,td.cancel_reason')
		->from('tbl_transaction_details AS td')
		->join('tbl_transaction AS t','t.order_id = td.order_id')
		->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
		->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
		->join('tbl_product AS p','td.product_id = p.product_id')
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
		->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
		->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
		->join('tbl_users AS user','user.userid = t.user_id')
		->where('t.order_id', $order_id);
		if($this->session->userdata('user_type') != "1"){
			$this->db->where('td.seller_id', $this->session->userdata('userid'));
		}
		$this->db->group_by('td.id')
		->order_by('t.created','DESC');
		$query = $this->db->get();
		// echo $this->db->last_query();die();
		$data['data'] = $query->result();
		return $data['data'];
	}

	function getOrderProduct($product_id = "",$select = "",$where = "")
	{
		if(is_array($product_id)){
			$where = " AND product.product_id IN ('".implode("','",$product_id)."')";
		}else if($product_id){
			$where = " AND product.product_id = '".$product_id."'";
		}		
		if($select == ""){
			$select = "product.*, users1.userid as userid1, users2.userid as userid2, users1.firstname as name1, users2.firstname as name2";
		}
		$sql = "SELECT ".$select." 
				FROM ".DBPREFIX."_product as product
				LEFT JOIN ".DBPREFIX."_users as users1 ON product.created_id = users1.userid
				WHERE deleted_id is NULL ".$where;	
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){
			return $result->result_array();			
		}
	}

	public function send_mail($from_email, $from_name, $to, $message = 'Test Mail', $subject){
		$config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'smtp.socketlabs.com',
			'smtp_port' => '2525',
			'smtp_user' => 'server13556',
			'smtp_pass' => 'n7P6FpEz42Ztq3XYk8s',
			'mailtype' => 'html',
			'charset' => 'utf-8',
			'wordwrap' => TRUE
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from($from_email, $from_name);
		$this->email->to($to);
		$this->email->subject($subject);
		$this->email->message($message);
		if($this->email->send()){
			$user = $this->session->userdata('MMS_ADMIN_DATA');
			$log = $this->Utilz_Model->logs($user['uid'], $message, $subject);
			return array('status'=>1,'msg'=>'OK');
		}else{
			$message = $this->email->print_debugger();
			$user = $this->session->userdata('MMS_ADMIN_DATA');
			$log = $this->Utilz_Model->logs($user['uid'], $message, $subject);
			return array('status'=>0,'msg'=>$this->email->print_debugger());
        }
	}

	public function accept_order($order_id,$action,$trackingid,$date)
	{
		$user_id = $this->session->userdata('userid');
		$this->db->set('tracking_id',$trackingid);
		$this->db->set('order_accept_date',$date);
		$this->db->set('status', $action);   
		$this->db->where('order_id', $order_id); 
		$this->db->where('seller_id', $user_id); 
		$this->db->update(DBPREFIX.'_transaction_details'); 
	}
	
	public function getPendingCancelledOrders($user_id, $search = "")
	{
		if($search !=""){
			$search = trim($search);
			$this->db->where('(td.order_id LIKE "%'.$search.'%")');
		}
		$this->datatables->select('td.order_id as order_id, td.created as created, concat(u.firstname, " ", u.lastname) as username')
		->from('tbl_transaction_details td')
		->join('tbl_users as u','td.user_id = u.userid')
		->where('td.seller_id', $user_id)
		->where('td.cancellation_pending', '1')
		->group_by('td.order_id');
		$this->db->order_by("td.id",'desc');
	}

	public function cancel_order_confirm($data)
	{
		$status = "";
		if($data['value'] == 0){
			$insert_data = array(
				'is_cancel' => '1',
			);
			$status = "approved";
		} else {
			$insert_data = array(
				'is_cancel' => '2',
				'status'	=> '1',
			);
			$status = "declined";
		}
		$this->db->where('order_id', $data['can_id'])->where('id', $data['row_id'])->update('tbl_transaction_details', $insert_data);
		$sendData = array();
		$sendData[] = array("order_id"=>$data['can_id'],"trans_details_Id"=>$data['row_id'],"hubx_id"=>$data['hubx_id'],"reason"=>"You accept cancellation request.");
		if($this->db->affected_rows() > 0){
			//$data['order_id'] = $data['can_id'];
			//$data['trans_details_Id'] = $data['row_id'];
			if($status == "declined"){
				$return = $this->approveOrder($sendData);
			} else{
				$return = $this->declineOrder($sendData);
			}
			$return = $return[0];
			if($return['status'] == "1"){
				//$this->send_mails($data['user_id'], $data['can_id'], $status);
				return array("status"=>"1", "message"=>"transaction successfull", "type"=>$status);
			}else{
				return array("status"=>"0", "message"=>$return['message'], "type"=>$status);
			}
		} else {
			return array("status"=>"1", "message"=>"transaction failed", "type"=>"Error");
		}
	}

	private function send_mails($buyer_id, $order_id, $status){
		$data = array();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		}
		$seller_email = $this->get_email($user_id);
		$buyer_email = $this->get_email($buyer_id);
		$this->load->library('parser');
		$this->load->helper('url');
		$this->load->helper('email');
		$this->load->library('email');
		$config['mailtype'] = 'text';
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'smtp.socketlabs.com';
		$config['smtp_port'] = '2525';
		$config['smtp_user'] = 'server13556';
		$config['smtp_pass'] = 'n7P6FpEz42Ztq3XYk8s';
		$config['charset']    = 'utf-8';
		$config['newline']    = "\r\n";
		$config['mailtype'] = 'text'; // or html
		$config['validation'] = TRUE; // bool whether to validate email or not      
		$this->email->initialize($config);	
		$this->email->set_header('MIME-Version', '1.0; charset=utf-8');
		$this->email->set_header('Content-type', 'text/html');	
		$data['order_id'] = $order_id;
		$data['status'] = $status;
		
		$this->email->from($this->config->item("info_email"), $this->config->item("author"));
		$this->email->to($buyer_email[0]['email']); 
		$this->email->subject('Zabee : Order Cancellation');
		$data['page_name'] = "email_template_order_cancel";
		$message = $this->parser->parse('front/emails/email_template', $data, TRUE);
		$this->email->message($message);
		$check = $this->email->send();
		//print_r($check);die();
	}

	private function get_email($id){
		$this->db->select('email')
		->from('tbl_users')
		->where('userid',$id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	public function get_quantity_from_warehouse($data){
		$this->db->select('quantity')
		->from('tbl_warehouse_inventory')
		->where('warehouse_id',$data['warehouse_id'])
		->where('seller_product_id',$data['sp_id']);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	public function subt_quantity_from_warehouse($data){
		$sp_id = array();
		$w_id = array();
		$value = array();
		$name = array();
		$firstExplodeFromData = explode("+",$data['ware_values'][0]);
		$secondExplodeFromFEFD = explode("_",$firstExplodeFromData[1]);
		for($i = 0; $i < count($data['ware_values']); $i++){
			$firstExplodeFromData = explode("+",$data['ware_values'][$i]);
			$value[$i] = $firstExplodeFromData[0];
			$sp_id[$i] = $firstExplodeFromData[2];
			$secondExplodeFromFEFD = explode("_",$firstExplodeFromData[1]);
			$name[$i] = $secondExplodeFromFEFD[2];
			$w_id[$i] = $secondExplodeFromFEFD[3];
			$forqty = array(
				"warehouse_id" => $w_id[$i],
				"sp_id" => $sp_id[$i]
			);
			$existingQty = $this->get_quantity_from_warehouse($forqty);
			$existingQty = $existingQty[0]['quantity'];
			$value = $existingQty - $value[$i];
			$newdata = array('quantity'=> $value);
			$this->db->where('warehouse_id', $w_id[$i]);
			$this->db->where('seller_product_id', $sp_id[$i]);
			$this->db->update(DBPREFIX."_warehouse_inventory", $newdata);
		}
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function get_orderDetails($o_id, $fromSeller,$from="",$seller_id="",$is_admin ="", $is_failed=""){
		$data = array('row'=>0,'result'=>"");
		if($fromSeller){
			$where = array('t.order_id' => $o_id);
		} else {
			if($from == "accept"){
				$where = array('t.order_id' => $o_id, 'td.status' => '1', 'td.warehouse_id' => $this->session->userdata('warehouse_id'));
			}else if($from == "pending"){
				$where = array('t.order_id' => $o_id, 'td.status' => '0', 'td.warehouse_id' => $this->session->userdata('warehouse_id'));
			}else if($from == "decline"){
				$where = array('t.order_id' => $o_id, 'td.status' => '2', 'td.warehouse_id' => $this->session->userdata('warehouse_id'));
			}
			else{
				$where = array('t.order_id' => $o_id, 'td.warehouse_id' => $this->session->userdata('warehouse_id'));
			}
			
		}
		if($seller_id){
			$where["td.seller_id"] = $seller_id;
			$where['td.status'] = '0';	
			$where['td.cancellation_pending'] = '0';
		}
		$variantData = array();
		$transaction_table = "tbl_transaction";
		$transaction_detail_table = "tbl_transaction_details";
		$select = "";
		if($is_failed){
			$transaction_table = "tbl_transaction_backup";
			$transaction_detail_table = "tbl_transaction_details_backup";
			$select = ",t.error";
		}
		$this->db->select('t.order_id,td.id as order_item_id, t.pay_type, t.created,t.shipping, t.card_detail,sp.condition_id, t.billing,td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,pv.price,td.status as action, td.refund_id as refund,td.hubx_id as hubx_id'.$select)
				->from($transaction_detail_table.' AS td')
				->join($transaction_table.' AS t','t.order_id = td.order_id')
				->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
				->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
				->join('tbl_product AS p','td.product_id = p.product_id')
				->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
				->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
				->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
				->where($where)
				->group_by('td.id')
				->order_by('t.created','DESC');
				$query = $this->db->get();
				// echo $this->db->last_query(); die();
				$result= $query->result();
				$row= $query->num_rows();
			if($row > 0){
				$orderData = array();
				foreach($result as $od)	{
					// echo "<pre>"; print_r($od); die();;
					$tID = $od->order_id;
					if($od->user_id){
						$buyer = $this->getBuyerName($od->user_id);
					}
					if($od->shipping){
						$newShipping = array();
						$shipping = unserialize($od->shipping);
					}
					$days = $this->dateDifference($od->created);
					$orderData[$buyer]['order_title'][] = ($days < 7)?'<div class="custom-control custom-checkbox"><input type="checkbox" id="customCheck'.$od->order_item_id.'" data-tID="'.$tID.'" data-tdID="'.$od->order_item_id.'" data-hubx_id="'.$od->hubx_id.'" data-sellerid="'.$od->user_id.'" data-pay_type="'.$od->pay_type.'" class="custom-control-input childCheck'.$tID.'" onChange="showCheckAll('.$tID.')" name="example1"><label class="custom-control-label" for="customCheck'.$od->order_item_id.'"></label></div>':"x";
					$orderData[$buyer]['order_title'][] = $od->product_name;
					$orderData[$buyer]['order_title'][] = "Address: ".$shipping['address_1']."<br />"."Name: ".$shipping['name']."<br />"."Contact: ".$shipping['phone'];
					$orderData[$buyer]['order_title'][] = number_format($od->item_gross_amount, 2);
					if($days < 7 || $od->action > 0){
						if($od->action == 0){
							$jsAcceptButton = ($od->billing == "paypal")?"paypalCapture(this)":"askApprove(this,0)";
							$jsDeclineButton = ($od->billing == "paypal")?"paypalRefund(this)":"askDecline(this,0)";
							$status = "pending";
							if($fromSeller){
								if($_SESSION['is_zabee'] == 0 &&  $this->session->userdata('user_type') != 1){
									$ApproveButton = "<a class='btn btn-primary approveBtn' data-tID = '".$tID."' data-tdID = '".$od->order_item_id."'  data-hubx_id= '".$od->hubx_id."' data-sellerid = '".$od->user_id."' onclick='".$jsAcceptButton."'>Approve</a>";
									$DeclineButton = "<a class='btn btn-danger declineBtn ml-3' data-tID = '".$od->order_id."' data-tdID = '".$od->order_item_id."' data-sellerid = '".$od->user_id."' onclick='".$jsDeclineButton."'>Cancel</a>";
									$Button = $ApproveButton.$DeclineButton;
								} else if($_SESSION['is_zabee'] == 1 && $this->session->userdata('user_type') !=1) {
									$Button = "<h6 style='color:grey'>Waiting for warehouse approval</h6>";
								}else if($is_failed){
									$error =unserialize($od->error);
									$Button = "<h6 style='color:grey'>".$error["Errors"]."</h6>";
								}else{
									$Button = "<h6 style='color:orange'>Pending</h6>";
								}
								
							} else {
								$ApproveButton = "<a class='btn btn-primary approveBtn' data-tID = '".$tID."' data-hubx_id= '".$od->hubx_id."' data-tdID = '".$od->order_item_id."' data-sellerid = '".$od->user_id."' onclick='".$jsAcceptButton."'>Approve</a>";
								$DeclineButton = "<a class='btn btn-danger declineBtn ml-3' data-tID = '".$od->order_id."' data-tdID = '".$od->order_item_id."' data-sellerid = '".$od->user_id."' onclick='".$jsDeclineButton."'>Cancel</a>";
								$Button = $ApproveButton.$DeclineButton;
							}
						} else if($od->action == 1){ //if in case td.status is removed from $where
							$status = "approved";
							$Button = "<h6 style='color:green'>Approved</h6>";
						} else if($od->action == 2){
							$status = "declined";
							$Button = "<h6 style='color:red'>Canceled</h6>";
						}
					} else {
						$status = "expired";
						$Button = "<h6 style='color:red'>Order Expired</h6>";
					}
					$orderData[$buyer]['order_title'][] = $Button;
					if($fromSeller){
						if($_SESSION['is_zabee'] == 0){
							if($status == "approved"){
								if($od->refund != ""){
									$RefundButton =  "<p style='color:grey'>refunded</p>";	
								} else {
									$RefundButton = "<div class='text-center'><a class='btn btn-warning'  data-tdID = '".$od->order_item_id."' data-order_id='".$od->order_id."' onclick='askRefund(this)' style='color:white; width:95px;'>Refund</a></div>";
								}
							} else {
								$RefundButton =  "<p style='color:red'>Not available</p>";
							}
						} else {
							$RefundButton = "-";
						}
					} else {
						if($status == "approved"){
							if($od->refund != ""){
								$RefundButton =  "<p style='color:grey'>refunded</p>";	
							} else {
								$RefundButton = "<div class='text-center'><a class='btn btn-warning'  data-tdID = '".$od->order_item_id."' data-order_id='".$od->order_id."' onclick='askRefund(this)' style='color:white; width:95px;'>Refund</a></div>";
							}
						} else {
							$RefundButton =  "<p style='color:red'>Not available</p>";
						}
					}
					$orderData[$buyer]['order_title'][] = $RefundButton;
					$orderData[$buyer]['order_group'][] = ($days < 7)?'<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" onChange="checkAll(this,'.$od->order_id.')" name="example1" id="parentCheck'.$od->order_id.'"><label class="custom-control-label" for="parentCheck'.$od->order_id.'"></label></div>':"x";
					$orderData[$buyer]['order_group'][] = 'Product Name';
					$orderData[$buyer]['order_group'][] = 'shipping';
					$orderData[$buyer]['order_group'][] = 'Amount';
					$orderData[$buyer]['order_group'][] = 'Action';
					$orderData[$buyer]['order_group'][] = 'Refund';
					$data['row']= 1;
					$data['result']= $orderData;
					$data['result']['order_id'] = $od->order_id;
				}
			}		
		return $data;	
	}

	public function get_canceOrderDetails($o_id,$user_id,$api=""){
		$data = array('row'=>0,'result'=>"");
		$where = array('td.order_id' => $o_id, 'td.seller_id'=>$user_id, 'td.cancellation_pending' => '1');
		$variantData = array();
		$this->db->select('td.id as id, td.order_id as order_id,td.id as order_item_id,t.created,t.shipping, td.`item_gross_amount,p.product_name,pm.thumbnail AS image_link,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.is_cancel as cancelled,td.hubx_id,t.pay_type')
		//$this->db->select('td.id as id, td.order_id as order_id,td.id as order_item_id, t.created,t.shipping, sp.condition_id,td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,pv.price,td.status as action, td.refund_id as refund,td.is_cancel as cancelled')
				->from('tbl_transaction_details AS td')
				->join('tbl_transaction AS t','t.order_id = td.order_id')
				->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
				->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
				->join('tbl_product AS p','td.product_id = p.product_id')
				->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id','LEFT')
				->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
				->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
				->join('tbl_users as u','td.user_id = u.userid')
				->where($where)
				->group_by('td.id')
				->order_by('t.created','DESC');
				$query = $this->db->get();
				// echo"<pre>". $this->db->last_query(); die();
				$result= $query->result();
				if($api){
					return $result;
				}
				$row= $query->num_rows();		
			if($row > 0){
				foreach($result as $od)	{
					if($od->user_id){
						$buyer = $this->getBuyerName($od->user_id);
					}
					if($od->shipping){
						$newShipping = array();
						$shipping = unserialize($od->shipping);
					}
					$orderData[$buyer]['order_title'][] = "<img class='img' src='".product_thumb_path($od->image_link)."' height='50px' />";
					$orderData[$buyer]['order_title'][] = $od->product_name;
					$orderData[$buyer]['order_title'][] = "Address: ".$shipping['address_1']."<br />".$shipping['city'].", ".$shipping['state']." ".$shipping['zipcode']."<br/>".$shipping['country']."<br />"."Name: ".$shipping['name']."<br />"."Contact: ".$shipping['phone'];
					$orderData[$buyer]['order_title'][] = $od->item_gross_amount;
					if($od->cancelled == 0){
						$days = $this->dateDifference($od->created);
						if($days < 7){
							$status = "pending";
							$ApproveButton = '<a class="btn btn-primary approveBtn" id="cancel_approve_'.$od->order_id.'" data-row_id="'.$od->order_item_id.'" data-canid_approve="'.$od->order_id.'" data-is_cancel_approve="'.$od->cancelled.'" data-userid="'.$od->user_id.'" data-hubx_id="'.$od->hubx_id.'" data-payment_type="'.$od->pay_type.'" onclick="cancel_order_approve(this)" style="color:white">Approve</a><br />';
							$DeclineButton = '<a class="btn btn-danger declineBtn" id="cancel_decline_'.$od->order_id.'" data-row_id="'.$od->order_item_id.'" data-canid_decline="'.$od->order_id.'" data-is_cancel_decline="'.$od->cancelled.'" data-userid="'.$od->user_id.'" data-hubx_id="'.$od->hubx_id.'" data-payment_type="'.$od->pay_type.'" onclick="cancel_order_decline(this)" style="margin-top:10px; width: 92px; color:white">Cancel</a>';
							$hidden_input = '<input type="hidden" id="input_'.$od->order_item_id.'"  />';
							$Button = $ApproveButton.$DeclineButton.$hidden_input;
						} else {
							$status = "expired";
							$Button = "<h6 style='color:red'>Order Expired</h6>";
						}	
					} else if($od->cancelled == 1){ //if in case td.status is removed from $where.
						$status = "approved";
						$Button = "<h6 style='color:green'>Approved</h6>";
					} else if($od->cancelled == 2){
						$status = "declined";
						$Button = "<h6 style='color:red'>Canceled</h6>";
					}
					$orderData[$buyer]['order_title'][] = $Button;
					$orderData[$buyer]['order_group'][] = 'Image';
					$orderData[$buyer]['order_group'][] = 'Title';
					$orderData[$buyer]['order_group'][] = 'Address';
					$orderData[$buyer]['order_group'][] = 'Amount';
					$orderData[$buyer]['order_group'][] = 'Action';
					$data['row']= 1;
					$data['result']= $orderData;
				}
			}		
		return $data;	
	}

	private function getBuyerName($id){
		$this->db->select('concat(firstname, " ",lastname) as name')
		->from('tbl_users')
		->where('userid',$id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result[0]['name'];
	}

	public function approveOrder($dataSet, $currency = ""){
		$return = array();
		foreach($dataSet as $key=>$data){
			//$return[] = array("status"=>"1", "code"=>"Second Condition", "message"=>"Transaction Completed","key"=>$key);
			//continue;
			$token = "";
			$hubx_id = $data['hubx_id'];//"f9728c15-0094-46a7-ad79-11059059d462";//
			$where = "";
			if($hubx_id){
				$where = array("hubx_id"=>$hubx_id);
			}
			$subOrders = $this->OrderData($data['order_id'],"",$where);
			$days = $this->dateDifference($subOrders[0]['created']);
			$hubx_order_id = ""; // Means No Hubx order
			if($days < 7){
				$return = FALSE;
				$declined = $this->OrderData($data['order_id'], '2',$where); //DECLINED ORDER CHECK
				$chargeData = $this->getTransactionIdAndCaptureStatus($data['order_id']); //ALREADY CAPTURED?
				if(/*$data["hubx_id"]*/$hubx_id){
					$hubx_bypass = $this->input->post("hubx_bypass");
					$hubx_return = $this->hubx_order_approve($hubx_id,$subOrders,$hubx_bypass);
					if(isset($hubx_return['status']) && $hubx_return['status'] != 1){
						$return[] = array("hubx_return"=>$hubx_return,"key"=>$key);
						//return $hubx_return;
					}
					if($hubx_return["hubx_status"] == 1){
						$hubx_order_id = $hubx_return["hubx_order_id"];
					}else if(isset($hubx_return['error'])){
						$return[] = array("status"=>0,"message"=>$hubx_return["error"],"key"=>$key);
						//return array("status"=>"0", "message"=>$hubx_return["error"]); // IF Hubx return error
					}
					
				}
				if($chargeData[0]['is_capture'] != 1){
					if($chargeData[0]['transaction_id'] != ""){
						$amount = ($declined[0]['item_total_price'] > 0)?($subOrders[0]['total'] - $declined[0]['item_total_price']):$subOrders[0]['total']; //DIFFERENCE OF TOTAL AMOUNT AND DECLINED AMOUNT
						$declined[0]['pay_type'] = ($declined[0]['pay_type'] =="")?"card":$declined[0]['pay_type']; 
						switch($declined[0]['pay_type']){
							case "paypal":
								$this->load->library('PayPal', 'paypal');
								$capture = $this->paypal->captureOrder($chargeData[0]['transaction_id'], $amount, $currency);
								if($capture->statusCode == "201"){
									$dataSet = array('capture_id'=>$capture->result->id, 'is_capture'=>"1");
									$this->db->where("order_id",$data['order_id'])->update(DBPREFIX."_transaction", $dataSet);
									$token['status'] = "1";
								}
							case "card":
								$this->load->library('Stripe', 'stripe');
								$stripe_key = $this->config->item('stripe_key');
								$token = $this->stripe->captureCharge($chargeData[0]['transaction_id'], $stripe_key, ($amount * 100));
						}
						if($token['status'] == 1){
							$resp = $this->updateCapture($data['order_id']);
							if($resp){
								$transaction = $this->db->select("cancellation_pending")->from('tbl_transaction_details')->where('id',$data['trans_details_Id'])->get()->row();
								if($transaction->cancellation_pending == 0){
									$insertData = array(
										'status' => "1",
									);
									$this->db->where("id",$data['trans_details_Id'])->update(DBPREFIX.'_transaction_details', $insertData);
									if($this->db->affected_rows() > 0){
										// $this->db->where(array("order_id"=>$data['order_id'], 'status' => '2'))->update(DBPREFIX.'_transaction_details', array('status' => '3'));//Change declined refunded products status from 2 to 3
										//$this->session->set_flashdata("error", "Order has already been captured.");
										$this->Utilz_Model->saveNotification($chargeData[0]['user_id'], $this->session->userdata("userid"), '1', '3', "your order has been confirmed", "", 1);
										$return[] = array("status"=>"1", "code"=>"First Condition", "message"=>"Transaction Completed","key"=>$key);
										//return array("status"=>"1", "code"=>"First Condition", "message"=>"Transaction Completed");
									} else {
										$return[] = array("status"=>"0", "code"=>"First Condition", "message"=>"First query affected rows count is 0","key"=>$key);
										//return array("status"=>"0", "code"=>"First Condition", "message"=>"First query affected rows count is 0");
									} 
								}else{
									$return[] = array("status"=>"0", "message"=>"cancel requested","key"=>$key);
									//return array("status"=>"0", "message"=>"cancel requested");
								}
							}
						} else if($token['status'] == 0 && $token['status'] == "charge_already_captured"){
							$resp = $this->updateCapture($data['order_id']);
							if($resp){
								$transaction = $this->db->select("cancellation_pending")->from('tbl_transaction_details')->where('id',$data['trans_details_Id'])->get()->row();
								if($transaction->cancellation_pending == 0){
									$insertData = array(
										'status' => "1",
									);
									if($hubx_order_id){
										$insertData['hubx_order_id'] = $hubx_order_id;
									}
									$this->db->where("id",$data['trans_details_Id'])->update(DBPREFIX.'_transaction_details', $insertData);
									if($this->db->affected_rows() > 0){
										// $this->db->where(array("order_id"=>$data['order_id'], 'status' => '2'))->update(DBPREFIX.'_transaction_details', array('refund_id' => '3'));//Update declined products with refund_id
										//$this->session->set_flashdata("error", "Order has already been captured.");
										$this->Utilz_Model->saveNotification($chargeData[0]['user_id'], $this->session->userdata("userid"), '1', '3', "your order has been confirmed", "", 1);
										$return[] = array("status"=>"1", "code"=>"Second Condition", "message"=>"Transaction Completed","key"=>$key);
										//return array("status"=>"1", "code"=>"Second Condition", "message"=>"Transaction Completed");
									} else {
										$return[] = array("status"=>"0", "code"=>"Second Condition", "message"=>"First query affected rows count is 0","key"=>$key);
										//return array("status"=>"0", "code"=>"Second Condition", "message"=>"First query affected rows count is 0");
									} 
								}else{
									$return[] = array("status"=>"0", "message"=>"cancel requested","key"=>$key);
									//return array("status"=>"0", "message"=>"cancel requested");
								}
							}
						} else if($token['status'] == 0 && $token['status'] != "charge_already_captured"){
							//did'nt approve
							$this->session->set_flashdata("error", "Order can not be approved.");
							$return[] = array("status"=>"0", "code"=>"Third Condition", "message"=>"Error in Token status","key"=>$key);
							//return array("status"=>"0", "code"=>"Third Condition", "message"=>"Error in Token status");
						}
					} else{
						$this->session->set_flashdata("error", "Order can not be approved.");
						$return[] = array("status"=>"0", "code"=>"Forth Condition", "message"=>"Issue in charged data Transaction id","key"=>$key);
						//return array("status"=>"0", "code"=>"Forth Condition", "message"=>"Issue in charged data Transaction id");
					}
				} else if($chargeData[0]['is_capture'] == 1){
					$transaction = $this->db->select("cancellation_pending")->from('tbl_transaction_details')->where('id',$data['trans_details_Id'])->get()->row();
					if($transaction->cancellation_pending == 0){
						$insertData = array(
							'status' => "1",
						);
						if($hubx_order_id){
							$insertData['hubx_order_id'] = $hubx_order_id;
						}
						$this->db->where("id",$data['trans_details_Id'])->update(DBPREFIX.'_transaction_details', $insertData);
						if($this->db->affected_rows() > 0){
							//$this->session->set_flashdata("error", "Order has already been captured.");
							$this->Utilz_Model->saveNotification($chargeData[0]['user_id'], $this->session->userdata("userid"), '1', '3', "your order has been confirmed", "", 1);
							$return[] = array("status"=>"1", "code"=>"Fifth Condition", "message"=>"Transaction Completed","key"=>$key);
							//return array("status"=>"1", "code"=>"Fifth Condition", "message"=>"Transaction Completed");
						} else {
							$return[] = array("status"=>"0", "code"=>"Fifth Condition", "message"=>"Last query affected rows count is 0","key"=>$key);
							//return array("status"=>"0", "code"=>"Fifth Condition", "message"=>"Last query affected rows count is 0");
						}
						// $this->Utilz_Model->saveNotification($chargeData[0]['user_id'], $this->session->userdata("userid"), '1', '3', $this->lang->line("order_confirm"), "", 1);
					}else{
						$return[] = array("status"=>"0", "message"=>"cancel requested","key"=>$key);
						//return array("status"=>"0", "message"=>"cancel requested");
					}
				}
				//return $return;
			} else {
				$this->session->set_flashdata("error", "Order Expired");
				$this->Utilz_Model->saveNotification($chargeData[0]['user_id'], $this->session->userdata("userid"), '1', '3', "your order has expired", "", 1);
				$return[] = array("status" => '3', "message" => "Order Expired", "order_id" => $data['order_id'],"key"=>$key);
				//return array("status" => '3', "message" => "Order Expired", "order_id" => $data['order_id']);
			}
		}
		return $return;
	}

	public function declineOrder($dataSet, $currency = ""){
		$return = array();
		foreach($dataSet as $key=>$data){
			$token = array();

			$subOrders = $this->OrderData($data['order_id']);
			$days = $this->dateDifference($subOrders[0]['created']);
			$chargeData = $this->getTransactionIdAndCaptureStatus($data['order_id']);
			/*echo "<pre>";
			print_r($data);
			print_r($chargeData);die();*/
			if($days < 7){
				$id = isset($data['order_id']) ? $chargeData[0]['user_id'] : $data['sellerid'];
				if($chargeData[0]['is_capture'] != '1'){
					
					$insertData = array(
						'status' => "2",
						'cancellation_pending' => "0",
						"cancel_reason"=>$data['reason']
					);
					// $restock = $this->db->select("product_vid, qty")->from(DBPREFIX."_transaction_details")->where("id",$data['trans_details_Id'])->get()->result(); print_r($restock[0]->product_vid); die();
					$this->db->where("id",$data['trans_details_Id'])->update(DBPREFIX.'_transaction_details', $insertData);
					//$this->updateTransaction($data['trans_details_Id']);
				} else {
					$this->db->select('td.id, td.item_gross_amount AS amount, t.capture_id AS captureID,t.pay_type')
							->from(DBPREFIX."_transaction_details td")
							->join(DBPREFIX."_transaction t","td.order_id = t.order_id", "LEFT")
							->where('td.id',$data['trans_details_Id']);
					$result = $this->db->get();
					$result = $result->row();
					$amount = (int)($result->amount);
					$data['payment_type'] = ($result->pay_type =="")?"card":$result->pay_type; 
					switch($data['payment_type']){
						case "paypal":
							$this->load->library('PayPal', 'paypal');
							$capture = $this->paypal->refundOrder($result->captureID, $amount, $currency);
							if($capture->result->status == "COMPLETED"){
								$this->db->where("id",$data['trans_details_Id'])->update(DBPREFIX.'_transaction_details', array('status'=>'2', 'refund_id'=>$capture->result->id,"cancel_reason"=>$data['reason']));
								//$this->updateTransaction($data['trans_details_Id']);
							}
						case "card":
							$stripe_key = $this->config->item('stripe_key');
							$this->load->library('Stripe', 'stripe');
							$token = $this->stripe->getRefund($chargeData[0]['transaction_id'], $amount, $stripe_key);
					}
					//echo "<pre>";print_r($token);die();
					if(isset($token['status']) && $token['status'] == 1){
						$this->db->where("id",$data['trans_details_Id'])->update(DBPREFIX.'_transaction_details', array('status'=>'2', 'refund_id'=>$token['customer']->id,"cancel_reason"=>$data['reason']));
						//$this->updateTransaction($data['trans_details_Id']);
					}else if(isset($token['status']) && $token['status'] == 0){
						$message = (isset($token['message']))?$token['message']:"Error in order";
						$this->session->set_flashdata("error", $message);
						$return[] = array("status"=>"0", "code"=>"First Condition", "message"=>$message,"key"=>$key,"order_id" => $data['order_id']);
						continue;
					}
				}
				$this->Utilz_Model->saveNotification($id, $this->session->userdata("userid"), '1', '3', "Your Order has rejected", "", 1);
				if($this->db->affected_rows() > 0){
					$return[] = array("status"=>"1", "code"=>"First Condition", "message"=>"Your order has rejected","key"=>$key,"order_id" => $data['order_id']);
					//$result =  array("status" => '1', "message" => "Transaction Successfull", "order_id" => $data['order_id']);
				} else {
					$return[] = array("status" => '0', "message" => "Transaction Failed", "order_id" => $data['order_id'],"key"=>$key);
					//$result = array("status" => '0', "message" => "Transaction Failed", "order_id" => $data['order_id']);
				} 
			} else {
				$this->Utilz_Model->saveNotification($chargeData[0]['user_id'], $this->session->userdata("userid"), '1', '3', "your order has expired", "", 1);
				$return[] = array("status" => '3', "message" => "Order Expired", "order_id" => $data['order_id'],"key"=>$key);
				//$result = array("status" => '3', "message" => "Order Expired", "order_id" => $data['order_id']);
			}
			// again check the subOrders for the updated status
			$subOrders = $this->OrderData($data['order_id']);

			$allCancel = true;
			foreach($subOrders as $sub){
				if(($sub['status'] == "0") || $sub['status'] == "1"){
					$allCancel = false;
					break;
				}
			}
			if($allCancel){
				$amount = (int)($subOrders[0]['total'] * 100);
				switch($subOrders[0]['pay_type']){
					case "paypal":
						$this->load->library('PayPal', 'paypal');
						$capture = $this->paypal->refundOrder($result->captureID, $amount, $currency);
						if($capture->result->status == "COMPLETED"){
							$this->db->where("transaction_id",$chargeData[0]['transaction_id'])->update(DBPREFIX.'_transaction', array('complete_refund'=>'1', 'refund_id'=>$capture->result->id));
							//$this->updateTransaction($data['trans_details_Id']);
						}
					case "card":
						$stripe_key = $this->config->item('stripe_key');
						$this->load->library('Stripe', 'stripe');
						$token = $this->stripe->getRefund($chargeData[0]['transaction_id'], $amount, $stripe_key);
				}
				if(isset($token['status']) && $token['status'] == 1){
					$this->db->where("transaction_id",$chargeData[0]['transaction_id'])->update(DBPREFIX.'_transaction', array('complete_refund'=>'1', 'refund_id'=>$token['customer']->id));
				}
				/*$token = $this->stripe->getRefund($chargeData[0]['transaction_id'], $amount, $stripe_key);
				// echo"<pre>"; print_r($token); die();
				if(isset($token['status']) && $token['status'] == 1){
					$this->db->where("transaction_id",$chargeData[0]['transaction_id'])->update(DBPREFIX.'_transaction', array('complete_refund'=>'1', 'refund_id'=>$token['customer']->id));
				}*/
				//print_r($this->stripe->paymentRetrieve($chargeData[0]['transaction_id'], $stripe_key)); die();
			}
		}
		return $return;
	}

	public function getTransactionId($order_id){
		$this->db->select('transaction_id')
		->from('tbl_transaction')
		->where('order_id',$order_id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result[0]['transaction_id'];
	}

	public function getTransactionIdAndCaptureStatus($order_id){
		$this->db->select('user_id, transaction_id, is_capture')
		->from('tbl_transaction')
		->where('order_id',$order_id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}

	public function getAmount($order_id, $tdid){
		$this->db->select('item_gross_amount AS amount,seller_id')
		->from('tbl_transaction_details')
		->where('order_id',$order_id)
		->where('id',$tdid);
		$query = $this->db->get();
		$result = $query->row();
		return $result;
	}

	public function addRefundId($order_id, $refundID, $tdid){
		$data = array(
			'refund_id' => $refundID
		);
		$this->db->where('order_id', $order_id)->where('id',$tdid)
				 ->update('tbl_transaction_details', $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function updateCapture($order_id){
		$data = array(
			'is_capture' => "1"
		);
		$this->db->where('order_id', $order_id)
				 ->update('tbl_transaction', $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return array("status"=>"0", "code"=>"Update Capture", "message"=>"issue in update transaction");
		}
	}

	public function OrderData($order_id, $status = "",$where=""){
		if($status == ""){
			if($where){
				$this->db->where($where);
			}
			$result = $this->db->select("td.id AS trans_detail_id, t.created, td.seller_id, td.order_id, td.item_gross_amount AS item_total_price, td.status, t.transaction_id, t.is_capture, td.gross_amount AS total,t.shipping,td.price,td.qty,td.hubx_id,t.billing,t.pay_type")
						->from("tbl_transaction_details AS td")
						->join("tbl_transaction AS t", "td.order_id = t.order_id")
						->where("td.order_id", $order_id)->get()->result_array();
			//echo $this->db->last_query();die();
		} else if($status == '2'){
			$result = $this->db->select("GROUP_CONCAT(`td`.`id`) AS `trans_detail_id`, SUM(`td`.`item_gross_amount`) AS `item_total_price`, `t`.`is_capture`,t.pay_type")
						->from("tbl_transaction_details AS td")
						->join("tbl_transaction AS t", "td.order_id = t.order_id")
						->where(array("td.order_id"=>$order_id, "td.status" => '2'))->get()->result_array();
		}
		return $result;
	}

	public function dateDifference($order_date){
		$order_date = date_create($order_date." A");
		$system_date = date_create(date('Y-m-d h:i:s A'));
		$days  = date_diff($order_date,$system_date)->days;
		return $days;
	}
	public function getProductDataByOrder($order_id,$transaction_id){
		$query = $this->db->select("u.firstname,u.email,p.product_name,pm.thumbnail,pm.is_local,td.created,td.qty,td.item_shipping_amount,td.price,td.item_total,td.tax_amount,t.shipping,t.billing, td.discount_data as discountData")
		->from("tbl_transaction_details td")
		->join("tbl_transaction t","t.order_id = td.order_id")
		->join("tbl_product p","p.product_id = td.product_id")
		->join("tbl_users u","u.userid = td.user_id")
		->join("tbl_product_media pm","pm.product_id = p.product_id AND pm.is_cover = '1'", 'LEFT')
		->where(array("td.order_id"=>$order_id,"td.id"=>$transaction_id))->get()->row();
		// echo $this->db->last_query(); die();
		return $query;
	}
	public function forceAcceptOrder($transaction_id){
		$this->db->where("id",$transaction_id)->update('tbl_transaction_details',array("status"=>"1", "cancellation_pending"=>"0", "is_cancel" => "2"));
		if($this->db->affected_rows() > 0){
			return array("status"=>"success");
		} else {
			return array("status"=>"failed");
		}
	}

	public function updateTransaction($t_id){
		$restock = $this->db->select("product_vid, qty")->from(DBPREFIX."_transaction_details")->where("id",$t_id)->get()->result();
		$this->db->where("product_variant_id",$restock[0]->product_vid)->set("sell_quantity", "sell_quantity - ".$restock[0]->qty, FALSE)->update(DBPREFIX.'_product_inventory');
	}

	function getFailedOrders($user_id, $order_id = '',$search = "", $request = "",$where,$user_type="2",$select=""){
		if($search !=""){
			$search = trim($search);
			$this->db->where('(p.product_name LIKE "'.stripslashes($search).'%") OR (t.order_id LIKE "%'.$search.'%")');
		}
		if($user_type == "1"){
			$this->db->join('tbl_warehouse AS w',' w.warehouse_id = td.warehouse_id AND w.`user_id` = "'.$user_id.'"', 'LEFT');
		}	
		if($where){
			$this->db->where($where);
		}
		if($select == ""){
			$select = "t.order_id,td.id as order_item_id, t.created,t.shipping,sp.condition_id,c.condition_name, t.billing, t.card_detail, td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,pv.price,td.status as action, SUM(td.item_gross_amount) AS seller_total,ss.store_name";
		}
		$this->db->select($select)
		->from('tbl_transaction_details_backup AS td')
		->join('tbl_transaction_backup AS t','t.order_id = td.order_id')
		->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
		->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
		->join('tbl_product AS p','td.product_id = p.product_id')
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
		->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
		->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
		->group_by('td.id')
		->order_by('t.created','DESC');
		if($request['start'] != -1){
			$this->db->limit($request['length'],$request['start']); 
		}
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

	public function transferOrder($transaction_id){
		$transaction_detail = $this->db->select('*')->from('tbl_transaction_details_backup')->where('order_id', $transaction_id)->get()->result_array();
		$transaction = $this->db->select('*')->from('tbl_transaction_backup')->where('order_id', $transaction_id)->get()->result_array();
		unset($transaction[0]['error']);
		$transaction[0]['is_transfer'] = "1";
		$this->db->insert_batch(DBPREFIX."_transaction_details", $transaction_detail);
		$this->db->insert(DBPREFIX."_transaction", $transaction[0]);
		if($this->db->where('order_id', $transaction_id)->delete('tbl_transaction_details_backup')){
			if($this->db->where('order_id', $transaction_id)->delete('tbl_transaction_backup')){
				echo json_encode(array("status"=>"1","message"=>"success"));
			} else {
				echo json_encode(array("status"=>"0","message"=>"failed"));
			}
		} else {
			echo json_encode(array("status"=>"0","message"=>"unable to delete from backup"));
		}
	}

	function getTransferSaleView($order_id)
	{
		$this->db->select('t.order_id,td.id as order_item_id, t.created,t.shipping, t.card_detail,sp.condition_id,c.condition_name, t.billing,td.`gross_amount`,td.`item_gross_amount,td.item_shipping_amount,t.status,p.product_name,pm.thumbnail AS image_link,td.`qty`,t.transaction_id,p.product_id,sp.sp_id,td.user_id,td.product_vid,td.tax_amount,td.shipping_amt,td.currency_code,td.price,td.status as action')
		->from('tbl_transaction_details_backup AS td')
		->join('tbl_transaction_backup AS t','t.order_id = td.order_id')
		->join('tbl_product_variant AS pv','td.product_vid = pv.pv_id')
		->join('tbl_seller_product AS sp','pv.sp_id = sp.sp_id')
		->join('tbl_product AS p','td.product_id = p.product_id')
		->join(DBPREFIX."_product_media AS pm",'pm.product_id = p.product_id AND pm.sp_id = sp.sp_id AND pm.is_cover','LEFT')
		->join('tbl_product_conditions AS c','pv.condition_id = c.condition_id')
		->join('tbl_seller_store AS ss','pv.seller_id = ss.seller_id')
		->where('t.order_id', $order_id);
		if($this->session->userdata('user_type') != "1"){
			$this->db->where('td.seller_id', $this->session->userdata('userid'));
		}
		$this->db->group_by('td.id')
		->order_by('t.created','DESC');
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		$data['data'] = $query->result();
		return $data['data'];
	}

	// public function paypalApproveOrder($data){
	// 	$response = array("status"=>"2", "message"=>"Error in transaction");
	// 	$this->load->library('PayPal', 'paypal');
	// 	$capture = $this->paypal->captureOrder($data['order_id']);
	// 	if($capture->result->status == "201"){
	// 		$this->db->set('capture_id',$capture->result->id);
	// 		$this->db->set('is_capture',"1");
	// 		$this->db->update(DBPREFIX."_transaction")->where("order_id",$data['order_id']);
	// 		$response = array("status"=>"1", "message"=>"Transaction Complete");
	// 	}

	// 	return $response;
	// }

	// public function paypalDeclineOrder($data){
	// 	$response = array("status"=>"2", "message"=>"Error in transaction");
	// 	$this->load->library('PayPal', 'paypal');
	// 	$this->db->select('td.id, td.item_gross_amount AS amount, t.capture_id AS captureID')
	// 					->from(DBPREFIX."_transaction_details td")
	// 					->join(DBPREFIX."_transaction t","td.order_id = t.order_id")
	// 					->where('td.id',$data['trans_details_Id']);
	// 			$result = $this->db->get();
	// 			$result = $result->row();
	// 			$amount = (int)($result->amount);
	// 	$capture = $this->paypal->refundOrder($result->captureID, $amount, TRUE);
	// 	if($capture->result->status == "201"){
	// 		$this->db->set('refund_id',$capture->result->id);
	// 		$this->db->update(DBPREFIX."_transaction")->where("order_id",$data['order_id']);
	// 		$response = array("status"=>"1", "message"=>"Transaction Complete");
	// 	}

	// 	return $response;
	// }
	// Hubx Code
	public function hubx_order_approve($hubx_id,$order_detail,$hubx_bypass){
		if($hubx_id){
			$hubx_id = array($hubx_id);
			$hubx_detail = $this->getHubxProductDetail($hubx_id);
			$verify_hubx_product = $this->verifyHubxProductDetail($order_detail,$hubx_detail);
			if($verify_hubx_product['status'] == 1 || ($hubx_bypass == 1 && $verify_hubx_product['status'] == 2)){
				//print_r($order_detail);
				$shipping = unserialize($order_detail[0]['shipping']);
				$billing = unserialize($order_detail[0]['billing']);
				$location = array('billing'=> $billing, 'shipping'=>$shipping);
				//print_r($location);
				//die();
				$hubx_data= array(
					"vendorPartNumber"=> $order_detail[0]['hubx_id'],
					"quantity"=> $order_detail[0]['qty'],
					"unitPrice"=> $order_detail[0]['price'],
					"buyerPartNumber"=> "",
					"itemDescription"=> "",
					"unitOfMeasure"=> "Each",
					//"requestedDeliveryDate"=> "",
					//"requestedShipDate"=> ""
				);
				$order = $this->orderHubxProduct($location,$hubx_data,$order_detail[0]['order_id']);
				return $order;
			}else{
				return $verify_hubx_product;
			}
		}else{
			return array("status"=>0,"msg"=>"Hubx id not found");
		}
	}
	public function getHubxProductDetail($hubx_id){
		$this->checkToken();
		$cart_data = $this->cart->contents();
		$access_token = $this->session->userdata("access_token");
		$data = json_encode($hubx_id);
		$hubx_return = $this->hubx->get_hubx_product_detail($access_token,$hubx_id,$data);
		$hubx_return = json_decode($hubx_return);
		return $hubx_return;
	}
	public function verifyHubxProductDetail($order_detail,$hubx_detail){
		$return = array("status"=>1,"msg"=>array());
		$profit = ($hubx_detail[0]->unitPrice/100)*15;
		$hubx_price = $hubx_detail[0]->unitPrice+$profit;
		if($order_detail[0]['price'] != $hubx_price){
			$return['status'] = 2;
			if($order_detail[0]['price'] > $hubx_price){
				$incdec = "decreased";
			}else{
				$incdec = "increased";
			}
			$return['msg'] = "Price has been ".$incdec." from ".$order_detail[0]['price']." to ".$hubx_price;
		}
		if($order_detail[0]['qty'] > $hubx_detail[0]->availability ){
			$return['status'] = 4;
			$return['msg'] = "Hubx available quantity is ".$hubx_detail[0]->availability." and your order quantity is ".$order_detail[0]['qty'];
		}
		return $return;
	}
	public function orderHubxProduct($location,$detail,$order_id){
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$return = array();
		$this->checkToken();
		$access_token = $this->session->userdata("access_token");
		//echo "<pre>";print_r($item);
		$data = array("comments"=>"",
			"terms"=> "",
			"billingAddressCode"=> "",
			"shippingAddressCode"=> "",
			"billingAddress"=> array(
			"line1"=> $location["billing"]["address_1"],
			"line2"=> $location["billing"]["address_2"],
			"country"=> $location["billing"]["country"],
			"city"=> $location["billing"]["city"],
			"state"=> $location["billing"]["state"],
			"zipCode"=> $location["billing"]["zipcode"]
			),
			"shippingAddress"=> array(
			"companyName"=> "",
			"recipientName"=> $location["shipping"]["name"],
			"recipientPhoneNumber"=> $location["shipping"]["phone"],
			"line1"=> $location["shipping"]["address_1"],
			"line2"=> $location["shipping"]["address_2"],
			"country"=> $location["shipping"]["country"],
			"city"=> $location["shipping"]["city"],
			"state"=> $location["shipping"]["state"],
			"zipCode"=> $location["shipping"]["zipcode"]
			),
			"shippingCost"=> 0,
			"details"=> array($detail),
			"purchaseOrdernumber"=> $order_id,
			"creationDate"=> $date_utc,
			"version"=> 0
		);
		$data = json_encode($data);
		//print_r($data);die();
		$hubx_return = $this->hubx->order_hubx_product($access_token,$data);
		$hubx_return = json_decode($hubx_return);
		if(isset($hubx_return->metadata)){
			if(isset($hubx_return->metadata->hubxDocumentNumber)){
				$return["hubx_order_id"] = $hubx_return->metadata->hubxDocumentNumber;
			}
			if(isset($hubx_return->metadata->lines)){
				$return["order_details"] = $hubx_return->metadata->lines;
			}
			$return["hubx_order_status"] = $hubx_return->orderStatus;
			$return["hubx_status"] = $hubx_return->success;
			if(isset($hubx_return->error)){
				$return["error"] = $hubx_return->error;
			}
		}
		
		return $return;
	}
	public function checkToken(){
		$user_id = $this->session->userdata('userid');
		$user_type = $this->session->userdata('user_type');
		$hubx_info = $this->session->userdata('hubx_info');
		$access_token = "";
		$expires_in = "";
		$time = time();
		$params = array('user_id'=>$user_id,"user_type"=>$user_type,"client_id"=>$this->config->item("hubx_client_id"),"client_secret"=>$this->config->item("hubx_client_secret"));
		$this->load->library('Hubx',$params);
		if($hubx_info){
			$hubx_info = json_decode($hubx_info);
			if($time > $hubx_info->expires_in){
				$hubx_info = $this->getAccessToken($user_id);
				$hubx_info = json_decode($hubx_info);
			}
			
		}else{
			$hubx_info = $this->getAccessToken($user_id);
			$hubx_info = json_decode($hubx_info);
		}
		$this->session->set_userdata('access_token',$hubx_info->access_token);
		$this->session->set_userdata('expires_in',$hubx_info->expires_in);
	}
	public function getAccessToken($user_id){
		$access_token = $this->hubx->getAccessToken();
		$access_token = json_decode($access_token);
		$time = time()+$access_token->expires_in;
		$access_token->expires_in = $time;
		$access_token = json_encode($access_token);
		$this->db->where('userid',$user_id);
		$this->db->update(DBPREFIX."_users",array("hubx_info"=>$access_token));
		return $access_token;
	}
} 
?>