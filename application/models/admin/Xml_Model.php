<?php 
function product_info($data){
		$this->db->insert('tbl_product', $insert_data);
		$insert_id = $this->db->insert_id();
		return insert_id;
}
?>