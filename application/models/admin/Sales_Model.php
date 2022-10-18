<?php
class Sales_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
	public function insertinvoice($insertArr = array())
	{		
		$this->db->insert(DBPREFIX."_sales",$insertArr);
	}	
	
	public function delete_pendingorder($order_id = "")
	{
		$this->db->where('order_id', $order_id);
		$this->db->delete(DBPREFIX."_orders");
	}
	
	public function getSalesInvoice($inv_id = "")
	{		
		$sql = "SELECT * FROM ".DBPREFIX."_sales";
		if($inv_id != ""){
			$sql = "SELECT * FROM ".DBPREFIX."_sales WHERE order_id = '".$inv_id."'";
		}
		$result = $this->db->query($sql);
		if($result && $result->num_rows()>0){			
			$sales = array();
			$sales = $result->result_array();
			$dataArr = array();
			$dataArr["customer"] = $dataArr["backend"] = $dataArr["vendors"] = "";
			foreach ($sales as $sale)
			{
				$dataArr["customer"] .= "'".$sale["customer_id"]."',";
				$dataArr["backend"] .= "'".$sale["delivered_by"]."',";
				$dataArr["backend"] .= "'".$sale["created_by"]."',";
				$vendors =  unserialize($sale["vendor_ids"]);
				for($i =0; $i<count($vendors); $i++){
					$dataArr["vendors"] .= "'".$vendors[$i]."',";
				}				
			}			
			$dataArr["customer"] = substr($dataArr["customer"],0,strlen($dataArr["customer"])-1);
			$dataArr["backend"] = substr($dataArr["backend"],0,strlen($dataArr["backend"])-1);
			$dataArr["vendors"] = substr($dataArr["vendors"],0,strlen($dataArr["vendors"])-1);
			$resArr = array();
			$customer = array();
			$backend = array();
			$vendors = array();
			$sql = "SELECT id, CONCAT(firstname,' ',lastname) as name FROM ".DBPREFIX."_users WHERE id IN (".$dataArr["customer"].")";
			$result = $this->db->query($sql);
			if($result && $result->num_rows()>0){				
				$resArr = $result->result_array();
				foreach($resArr as $arr)	{
					$customer[$arr["id"]] = $arr["name"];
				}
			}
			$sql = "SELECT admin_id, admin_name AS name FROM ".DBPREFIX."_backend_users WHERE admin_id IN (".$dataArr["backend"].")";
			$result = $this->db->query($sql);
			if($result && $result->num_rows()>0){				
				$resArr = $result->result_array();
				foreach($resArr as $arr)	{
					$backend[$arr["admin_id"]] = $arr["name"];
				}
			}
			$sql = "SELECT vendor_id, CONCAT('<b>',vendor_name,'</b><br />',vendor_address,'<br /> Mobile : ',vendor_mobile,'<br /> Phone :',vendor_phone,'<br /><br />') AS vendor_det FROM ".DBPREFIX."_vendors
					WHERE vendor_id IN (".$dataArr["vendors"].")";
			$result = $this->db->query($sql);
			if($result && $result->num_rows()>0){				
				$resArr = $result->result_array();
				foreach($resArr as $arr)	{
					$vendors[$arr["vendor_id"]] = $arr["vendor_det"];
				}
			}
			$retArr = array();
			foreach ($sales as $sale){				
				$sale["customer_name"] = $customer[$sale["customer_id"]];
				$sale["delivery_name"] = $backend[$sale["delivered_by"]];
				$sale["creator_name"] = $backend[$sale["created_by"]];
				$vendor = unserialize($sale["vendor_ids"]);
				$sale["vendor_details"] = ""; 
				for($i=0; $i<count($vendor);$i++){
					$sale["vendor_details"] .= $vendors[$vendor[$i]];
				}
				$retArr[] = $sale;
			}
			return $retArr;
		}
	}

	public function getNumberofPendingOrders($where){
		$this->db->select('order_id') 
		->from(DBPREFIX.'_transaction_details')
		->where("seller_id = '".$where."' AND status =  '0' AND cancellation_pending = '0' AND is_cancel = '0'");
		$this->db->group_by('order_id'); 
		$result=$this->db->get();
		return $result->num_rows();
	}
	public function getNumberofAcceptedOrders($where){
		$this->db->select('order_id') 
		->from(DBPREFIX.'_transaction_details')
		->where("seller_id = '".$where."' AND status =  '1'");
		$this->db->group_by('order_id'); 
		$result=$this->db->get();
		return $result->num_rows();
	}
	public function getNumberofRejectedOrders($where){
		$this->db->select('order_id') 
		->from(DBPREFIX.'_transaction_details')
		->where("seller_id = '".$where."' AND status =  '2'");
		$this->db->group_by('order_id'); 
		$result=$this->db->get();
		return $result->num_rows();
	}
	public function getNumberofCancellationRequestOrders($where){
		$this->db->select('order_id') 
		->from(DBPREFIX.'_transaction_details')
		->where("seller_id = '".$where."' AND (status =  '0' OR status = '3') AND cancellation_pending = '1' AND is_cancel = '0'");
		$this->db->group_by('order_id'); 
		$result=$this->db->get();
		// echo $this->db->last_query(); die();
		return $result->num_rows();
	}
}
?>