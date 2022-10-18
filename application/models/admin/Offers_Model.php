<?php
class Offers_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	public function getOffers($offer_id = '')
	{
		$this->datatables->select('id, offer_name, offer_image, position, is_active')
				->from('tbl_special_offers')
				->where('is_deleted', '0');
		$this->db->order_by('id', 'DESC');
		if($offer_id != ''){
			$this->datatables->where('id', $offer_id);
		 }else {
			$this->datatables->or_where('id', 0);
		}
	}
	public function getOffersForFront($position)
	{
		$this->db->select("offer_image")->from('tbl_special_offers')
				 ->where('is_active', '1')->where('is_deleted',"0")->where('position', $position)->order_by('id','DESC')->limit(1);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	
	public function offer_add($data){
		$insert_data = array(
			'created' => date('Y-m-d H:i:s'),
			'updated' => date('Y-m-d H:i:s'),
			'offer_name' => $data['offer_name'],
			'position' => $data['position'],
			'offer_image' => $data['image'],
			'is_active' => 0,
			'is_deleted' => 0
		);
		print_r($insert_data); die();
		$this->db->insert('tbl_special_offers', $insert_data);
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	public function getOfferByIdForEdit($id = ''){
		$this->db->select('*')
			 ->from("tbl_special_offers")
			 ->where('id', $id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}

	public function offer_update($data,$id = ''){
		$insert_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'offer_name' => $data['offer_name'],
			'position' => $data['position'],
			'is_active' => $data['status'],
			'is_deleted' => '0'
			);
		if(isset($data['image'])){
			$insert_data['offer_image'] =  $data['image'];
		}
		$this->db->where('id', $id)->update('tbl_special_offers', $insert_data);
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	public function deleteOffer($offer_id, $value, $position){
		if($value == 0){
			$insert_data = array(
				'is_active' => $value
			);
			$this->db->where('id', $offer_id)
					->update('tbl_special_offers', $insert_data);
			if($this->db->affected_rows() > 0){
				$id = ($position == 'left')?'1':'2';
				$this->db->where('id', $id)
						->update('tbl_special_offers', array('is_active' => '1'));
						$this->db->cache_delete('default', 'index');
				return true;
			} else {
				return false;
			}
		} else {
			$insert_data = array(
				'is_active' => $value
			);
			$this->db->where('position', $position)
					->update('tbl_special_offers', array('is_active'=>'0'));
			if($this->db->affected_rows() > 0){
				$this->db->where('id', $offer_id)
						->update('tbl_special_offers', $insert_data);
						$this->db->cache_delete('default', 'index');
				return true;
			} else {
				return false;
			}
		}
	}

	function offer_hard_delete($id, $position, $status){
		$this->db->where('id', $id)->delete('tbl_special_offers');
		if($this->db->affected_rows() > 0){
			if($status == '1'){
				$id = ($position == "right")?'2':'1';
				$this->db->where('id', $id)
						->update('tbl_special_offers', array('is_active'=>'1'));
			}
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}	
}	
?>