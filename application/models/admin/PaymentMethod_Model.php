<?php 
class PaymentMethod_Model extends CI_Model{
	
	function __construct(){
		parent::__construct();
		$this->load->database("default");
	}
	public function getPaymentGateway($user_id, $gateway_id = ''){
		
		$this->db->select('*')->where('userid', $user_id)->from('tbl_payment_gateway');
		if($gateway_id != ''){
			$this->db->where('id', $gateway_id);
		}
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->row();
		} else {
			return FALSE;			
		}
	}

	public function paymentmethod_add($insert_data){
		$return = 0;
		if($this->db->insert('tbl_payment_gateway', $insert_data)){
			$return = 1;
		}
		return $return;
	}
	
	public function paymentmethod_update($insert_data, $id){
		$return = 0;
		if($this->db->where('id', $id)->update('tbl_payment_gateway', $insert_data)){
			$return = 1;
		}
		return $return;
	}
}
?>