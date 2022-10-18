<?php
class User_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
	public function getAllUsers($cust_id = "")
	{
		if($cust_id == ""){
			$this->db->where("is_deleted","0");
		}else{
			$this->db->where(array("is_deleted"=>"0", "id"=>$cust_id));
		}				
		$result = $this->db->get(DBPREFIX."_users");		
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}else{
			return FALSE;			
		}
	}

	public function getAllUsers2($search, $offset, $length, $cust_id = "")
	{
		$this->datatables->select('userid as id, firstname, middlename, lastname, gender, dob, email, mobile, telephone, address, AREA, PIN, shippingaddress, shipping_area, shipping_PIN')
						->from('tbl_users')
						->where('is_active', 1);
		$this->db->like('firstname', $search);
		$this->db->limit($length,  $offset);	 
	}

	public function getuserbytype($typename = "")
	{
		$this->db->where("user_type",$typename);
		$result = $this->db->get(DBPREFIX."_backend_users");
		if($result && $result->num_rows()>0){
			return $result->result_array();
		}else{
			return FALSE;			
		}
	}

	public function update_profile($data, $user_id){
		$insert_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'ship_fullname' => $data['shipfullname'],
			'ship_contact' => $data['shipcontact'],
			'ship_address_1' => $data['shipaddress1'],
			'ship_address_2' => $data['shipaddress2'],
			'ship_country' => $data['shipcountry'],
			'ship_state' => $data['shipstate'],
			'ship_city' => $data['shipcity'],
			'ship_zip' => $data['shipzip'],
			'bill_fullname' => $data['billfullname'],
			'bill_contact' => $data['billcontact'],
			'bill_address_1' => $data['billaddress1'],
			'bill_address_2' => $data['billaddress2'],
			'bill_country' => $data['billcountry'],
			'bill_state' => $data['billstate'],
			'bill_city' => $data['billcity'],
			'bill_zip' => $data['billzip'],
			'active' => 1
		);
		$this->db->where('user_id', $user_id)->update('tbl_user_locations', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function getUserLocationsByUserId($user_id, $location_id = '')
	{
		$this->db->select('*')
				 ->from(DBPREFIX.'_user_locations as l')
				 ->where('l.active', 1)
				 ->where('l.user_id', $user_id);
		if($location_id != ''){
			$this->db->where('id', $location_id);
		}
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}

	public function getUserLocations($user_id, $search, $offset, $length, $location_id = '')
	{
		$this->datatables->select('l.id as id, CONCAT("Name: ", ship_fullname, "<br/>Contact: ", ship_contact, "<br/>Address: ", ship_address_1, "<br/>Country: ", ship_country) as shipping, CONCAT("Name: ", bill_fullname, "<br/>Contact: ", bill_contact, "<br/>Address: ", bill_address_1, "<br/>Country: ", bill_country) as billing,display_status,use_address')
						 ->from(DBPREFIX.'_user_locations as l')
						 ->where('user_id', $user_id)->where('user_type', "seller");;
		$this->datatables->like('ship_fullname', $search);
		$this->db->order_by('l.id', 'desc');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		  }				 
		if($location_id != ''){
			$this->datatables->where('id', $location_id);
		}
	}

	public function update_location($data, $user_id, $location_id){
		$insert_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'ship_fullname' => $data['shipfullname'],
			'ship_contact' => $data['shipcontact'],
			'ship_address_1' => $data['shipaddress1'],
			'ship_address_2' => $data['shipaddress2'],
			'ship_country' => $data['shipcountry'],
			'ship_state' => $data['shipstate'],
			'ship_city' => $data['shipcity'],
			'ship_zip' => $data['shipzip'],
			'bill_fullname' => $data['billfullname'],
			'bill_contact' => $data['billcontact'],
			'bill_address_1' => $data['billaddress1'],
			'bill_address_2' => $data['billaddress2'],
			'bill_country' => $data['billcountry'],
			'bill_state' => $data['billstate'],
			'bill_city' => $data['billcity'],
			'bill_zip' => $data['billzip'],
			'active' => 1
		);
		$this->db->where('user_id', $user_id)
				 ->where('id', $location_id)			
				 ->update(DBPREFIX.'_user_locations', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function add_location($data, $user_id, $user_type){
		$insert_data = array(
			'created' => date('Y-m-d H:i:s'),
			'updated' => date('Y-m-d H:i:s'),
			'user_id' => $user_id,
			'user_type' => $user_type,
			'ship_fullname' => $data['shipfullname'],
			'ship_contact' => $data['shipcontact'],
			'ship_address_1' => $data['shipaddress1'],
			'ship_address_2' => $data['shipaddress2'],
			'ship_country' => $data['shipcountry'],
			'ship_state' => $data['shipstate'],
			'ship_city' => $data['shipcity'],
			'ship_zip' => $data['shipzip'],
			'bill_fullname' => $data['billfullname'],
			'bill_contact' => $data['billcontact'],
			'bill_address_1' => $data['billaddress1'],
			'bill_address_2' => $data['billaddress2'],
			'bill_country' => $data['billcountry'],
			'bill_state' => $data['billstate'],
			'bill_city' => $data['billcity'],
			'bill_zip' => $data['billzip'],
			'active' => 1
		);
		$this->db->insert(DBPREFIX.'_user_locations', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function checkStoreExist($store_name,$s_id){
		if($s_id !=0){
			$this->db->where('s_id !=',$s_id);
		}
		$query = $this->db->select('store_name')
							->from('tbl_seller_store')
							->where('store_name',$store_name)
							->get()->num_rows();
		return $query;
	}

	public function saveData($data,$table){
		if($this->db->insert($table,$data)){
			return $this->db->insert_id();
		}else{
			return "false";
		}
	}

	public function getZabeeWarehouse(){
		$this->db->select('warehouse_id,warehouse_title')
					->from(DBPREFIX.'_warehouse')
					->where('user_id','1');
		$this->db->limit(1);
		$query = $this->db->get();
		return $query->result();			
		}

	public function updateData($data,$where,$table){
		$this->db->where($where);
		if($this->db->update($table,$data)){
			return "true";
		}else{
			return "false";
		}
	}

	public function deleteData($where){
		$this->db->where($where);
		$this->db->delete('tbl_state_tax');
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function checkUserStore($seller_id){
		$result['rows'] = 0;
		$result['result'] = ""; 
		$query =   $this->db->select('s.*,GROUP_CONCAT(`st`.state_id) AS state_id,GROUP_CONCAT(ts.state) AS states')
							->from('tbl_seller_store as s')
							->join('tbl_users u','s.seller_id=u.userid')
							->join('tbl_state_tax st', 's.s_id=st.s_id','LEFT')
							->join('tbl_states ts', 'ts.id=st.state_id','LEFT')
							->where('s.seller_id',$seller_id)->get(); 
		//$result = $query->result();
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $query->row();
		}
		return $result;
	}

	public function get_menu()
	{
		$query="SELECT * from tbl_menu ORDER BY menu_order ASC";
		$result = $this->db->query($query);
		return $result->result_array();	
	}

	public function insert_menu($data)
	{
		if($this->db->insert('tbl_menu', $data)){
			return true;
		}else{
			return false;
		}
	}


	public function save_list($data)
	{
		$this->db->where('menu_id', $data['menu_id'])->update("tbl_menu", $data);
		if($this->db->affected_rows() > 0){
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function wizard($data,$userid,$table)
	{ 
		if($table !="tbl_seller_store"){
			$return['status']=1; 
			$data['userid'] = $userid;
		}else{
			$data['seller_id'] = $userid;
			$return['status']=2;
		}
		if($table =="tbl_users"){
		$this->db->where('userid', $userid)->update($table, $data);
		}
		if( $table == "tbl_profiles"){
			if($data['id'] !=0){
				$this->db->where('id',$data['id'])->update($table, $data);
			}else{
				$this->db->insert($table, $data);
			}
		}
		if($table == "tbl_seller_store"){
			if($data['s_id'] !=0){
				$this->db->where('s_id',$data['s_id'])->update($table, $data);
			}else{
				$this->db->insert($table, $data);	
			}
			$_SESSION['store_name'] =$data['store_name'];
			$_SESSION['store_id'] =$data['store_id'];
		}
		return $return;
	}

	public function get_users($userid)
	{
		$this->db->select('*')
				->from('tbl_users')
				->where('userid',$userid);
		$query = $this->db->get();
		return $query->result_array();	
	}

	public function get_profile($userid)
	{
		$this->db->select('*')
				->from('tbl_profiles')
				->where('userid',$userid);
		$query = $this->db->get();
		return $query->result_array();	
	}

	function deleteLocation($location_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('id', $location_id)
				 ->update('tbl_user_locations', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	function hard_delete_location($location_id, $user_id){
		$this->db->where('id', $location_id)->delete('tbl_user_locations');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	function get_sellers($search = "", $request = "", $userid = ""){
		if($search !=""){
			$search = trim($search);		
			$this->db->where('(u.firstname LIKE "'.stripslashes($search).'%" OR store_name LIKE "%'.$search.'%" OR u.lastname LIKE "%'.$search.'%")');	
		}
		$this->db->select("ss.seller_id, store_name, u.is_active,u.is_block, CONCAT(u.FIRSTNAME, ' ', u.LASTNAME) AS seller_name, COUNT(DISTINCT sp.product_id) AS active_product")
				 ->from(DBPREFIX."_seller_store AS ss")
				 ->join(DBPREFIX."_users AS u",'ss.seller_id = u.userid')	
				 ->join(DBPREFIX."_seller_product as sp",'ss.seller_id = sp.seller_id',"LEFT")
				 ->group_by('ss.seller_id')
				 ->order_by("ss.s_id","DESC");
		if($request['start'] != 0){
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

	function get_buyers($search = "", $request = "", $userid = ""){
		if($search !=""){
			$search = trim($search);	
			$this->db->where('(((firstname LIKE "'.stripslashes($search).'%") OR (middlename LIKE "%'.$search.'%") OR (lastname LIKE "%'.$search.'%")))');
		}
		$this->db->select("CONCAT(firstname,' ', middlename, ' ',lastname) AS name ,userid, email, mobile,user_pic")
				->from(DBPREFIX."_users")
				->where('user_type','2')
				->where('is_active','1')	
				->group_by('userid')
				->order_by("id","DESC");
		if($request['start'] != -1){
			$this->db->limit($request['length'],$request['start']); 
		}
		$query = $this->db->get();
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

	public function getUserForAcc($s_id){
		$q = $this->db->select('id,firstname,lastname,email,mobile,user_pic')
					->where('userid',$s_id)
					->get(DBPREFIX."_users");
		return $q->row();
	}

	function is_default_address($location_id, $user_id){
		$data = array('use_address'=>0);
		$where = array('user_id'=>$user_id,'user_type'=>"seller");
		$this->db->where($where)->update('tbl_user_locations',$data);
		$data = array('use_address'=>1);
		$where = array('id'=>$location_id,'user_id'=>$user_id,'user_type'=>"seller");
		$this->db->where($where)->update('tbl_user_locations',$data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
}
?>