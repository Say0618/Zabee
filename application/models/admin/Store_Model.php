<?php
class Store_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
    }

    public function get_stores($status, $request){
        $search = $request['search']['value'];
        if($search !=""){
            $search = trim($search);
            $this->db->where('(CONCAT(user.firstname, " ",user.lastname) LIKE "'.stripslashes($search).'%" OR store.store_name LIKE "%'.$search.'%" OR store.contact_email LIKE "%'.$search.'%")');
        }
        $query = $this->db->select('store.s_id AS id, CONCAT(user.firstname, " ", user.lastname) AS name, store.contact_email AS email, store.store_name AS store, store.is_zabee AS zabee, store.seller_id, store.contact_phone AS contact, country.name AS country, store.is_tax AS tax, store.is_active')
                ->from(DBPREFIX.'_seller_store store')
                ->join(DBPREFIX.'_users user', 'user.userid = store.seller_id')
                ->join(DBPREFIX.'_country country', 'country.id = store.country_id')
                ->where('store.is_approve = "'.$status.'" AND user.user_type != 1')->get();
                // echo"<pre>"; print_r($this->db->last_query()); die();
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

    public function Request($store_id, $seller_id, $type){
        if($type == "approve"){
            $data = array("is_approve"=>"1");
            $c_msg = "Store approved successfully";
            $r_msg = "Store approved failed";
        }else{
            $data = array("is_approve"=>"2");
            $c_msg = "Store declined successfully";
            $r_msg = "Store declined failed";
        }
        if($store_id != "" && $seller_id != ""){
			$query = $this->db->where(array("s_id" => $store_id, "seller_id" => $seller_id))
                    ->update(DBPREFIX.'_seller_store', $data);
            if($query){
                return array("status"=>"1", "message"=>$c_msg);
            }else{
                return array("status"=>"0", "message"=>$r_msg);
            }
                     
		}else{
			return array("status"=>"0", "message"=>"Store or Seller id is missing");
		}
    }

    public function updateStoreStatus($store_id, $value){
		$date = date('Y-m-d H:i:s');
		$data = array('is_active'=>$value,'updated_date'=>$date);
		$this->db->where('s_id',$store_id);
		$res = $this->db->update(DBPREFIX."_seller_store",$data);
		if($res){
			return array("status"=>"1", "message"=>"Status Updated");
		}else{
            return array("status"=>"0", "message"=>"Status Update failed");
        }
		return $result;
	}
}