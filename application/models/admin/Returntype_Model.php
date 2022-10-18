<?php
class Returntype_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	public function getReturnPolicyforProduct($userid, $return_id = '',$select = "*")
	{
		$this->db->select($select)->from('tbl_return_type')->where('userid', $userid)
		->where('active',1);
		if($return_id != ''){
			$this->db->where('id', $return_id);
		} 
		$this->db->or_where('user_type', "1");
		$this->db->order_by("is_default", "desc");
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}

	public function getReturnPolicy($userid, $search, $offset, $length, $return_id = "", $user_type = "")
	{
		if($user_type != "1"){
			$where = "(userid = '".$userid."' OR user_type = '1' OR userid = '1')";
			$select = 'r.id as id, returnpolicy_name,return_period, restocking_fee, restocking_type, is_default, r.user_type, userid';
			$this->db->order_by("is_default_2", "desc");
		}else{
			$where = "(userid = '".$userid."' OR userid = '1')";
			$select = 'r.id as id, returnpolicy_name,return_period, restocking_fee, restocking_type, is_default_2, user_type, userid';
			$this->db->order_by("admin_default", "desc");
		}
		$this->datatables->select($select)
						 ->from('tbl_return_type as r')
						 ->where('active', 1)
						 ->where($where);
		$this->datatables->like('returnpolicy_name', $search);	
		if($length != -1){
			$this->db->limit($length,  $offset);		
		}	
		if($return_id != ''){
			$this->datatables->where('id', $return_id);
		} else {
			if($search == ''){
				$this->datatables->or_where('id', 0);
			}
		}
	}
	
	public function returnType_add($data, $user_id){
		$return_name = $data['returnPolicyName'];
		$this->db->select("returnpolicy_name")
			 ->from('tbl_return_type')
			 ->where('returnpolicy_name', $return_name)
			 ->where('active', '1')
			 ->where('userid', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0 || $return_name == "built-in default"){
			return false;
		} else {
			$insert_data = array(
					'created' => date('Y-m-d H:i:s'),
					'updated' => date('Y-m-d H:i:s'),
					'userid' => $user_id,
					'user_type' => $this->session->userdata('user_type'),
					'returns' => $data['return_YesNo'],
					'rma_required' => $data['rma_YesNo'],
					'restocking_type' => $data['percent_fixed'],
					'return_period' => $data['returnPeriod'],
					'restocking_fee' => $data['restockingFee'],
					'returnpolicy_name' => $data['returnPolicyName'],
					'active' => 1
				);
				$this->db->insert('tbl_return_type', $insert_data);
				if($this->db->affected_rows() > 0){
					return true;
				} else {
					return false;
				}
		}
	}	

	public function returnType_update($data, $user_id, $return_id){
		$return_name = $data['returnPolicyName'];
		$query = $this->db->select("returnpolicy_name")
			 ->from('tbl_return_type')
			 ->where('returnpolicy_name', $return_name)
			 ->where('id !=',$return_id)
			 ->where('active !=',0)
			 ->where('userid', $user_id)->get();
		$nor = $query->num_rows();
		$result = ($nor>0)?$query->row():array();
		if($nor > 0 && $return_name == $result->returnpolicy_name || $return_name == "built-in default"){
			return false;
		} else {
			$insert_data = array(
					'updated' => date('Y-m-d H:i:s'),
					'userid' => $user_id,
					'returns' => $data['return_YesNo'],
					'rma_required' => $data['rma_YesNo'],
					'restocking_type' => $data['percent_fixed'],
					'return_period' => $data['returnPeriod'],
					'restocking_fee' => $data['restockingFee'],
					'returnpolicy_name' => $data['returnPolicyName'],
					'active' => 1
			);
			$this->db->where('userid', $user_id)
					 ->where('id', $return_id)
					 ->update('tbl_return_type', $insert_data);
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
		}
	}

	public function getUserReturnsByUserId($user_id, $return_id = '')
	{
		$new = $this->db->select("*")
			->from('tbl_return_type')
			->where('userid', $user_id);
		if($return_id != ''){
			$this->db->where('id', $return_id);
		}
		$this->db->order_by("is_default", "desc");
		$this->db->order_by("id", "desc");
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();	
		}else{
			return FALSE;			
		}
		//print_r($this->db->last_query());die();
	}

	public function getUserReturnsByUniversalIdForDefault($return_id = ''){
		$this->db->select("*")->from('tbl_return_type')->where('userid', null);
		if($return_id != ''){
			$this->db->where('id', $return_id);
		}
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	public function returnType_forDefault($return_id, $userid){
		$this->db->where('userid', $userid)->set('is_default', '0')->update('tbl_return_type');
		$this->db->where('id', $return_id)->set('is_default', '1')->update('tbl_return_type');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	/*public function returnType_forDefault($return_id = '', $userid){
			$insert_data = array(
				'updated' => date('Y-m-d H:i:s'),
				'is_default' => 1,
				'is_default_2' => 1
			);
			$insert_data2 = array(
				'updated' => date('Y-m-d H:i:s'),
				'is_default' => 0,
				'is_default_2' => 0
			);	
			$insert_data3 = array(
				'child_return_ids' => $userid
			);	
			$this->db->where('id', '0')->set('is_default_2', '0')->update('tbl_return_type');
			$checker = false;
			$childColData = $this->getChildReturnData();
			$childColDataexploded = explode(",",$childColData[0]->child_return_ids);
			
			$this->db->where(array('is_default'=> 1, 'userid'=>$userid))->update('tbl_return_type', $insert_data2);
			$this->db->where('id', $return_id)->update('tbl_return_type', $insert_data);
			for($i = 0; $i < count($childColDataexploded); $i++){
				if($childColDataexploded[$i] == $userid){
					$checker = true;
				}
			}
			
			if($checker != true){
				$a = $this->db->where('id', '0')->set('child_return_ids', 'CONCAT(child_return_ids,\','.$userid.'\')',FALSE)->update('tbl_return_type');
			} else {
				$this->db->select("*")->from('tbl_return_type')->where('userid', $userid);
				if($return_id != ''){
					$this->db->where('id', $return_id);
				}
				$this->db->order_by("is_default", "desc");
				$this->db->order_by("id", "desc");
				$SecondCheckerForActive = $this->db->get();
			}
			if($this->db->affected_rows() > 0){
				return true;
			} else {
				return false;
			}
	}*/

	public function checkDefaultByUserId($user_id)
	{
		$this->db->select("is_default")->from('tbl_return_type')->where('userid', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}

	public function getChildReturnData($user_id = "0"){
		$this->db->select("child_return_ids")->from('tbl_return_type')->where('userid', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}
	
	// public function getReturnPolicy2($userid, $search, $offset, $length, $return_id = "")
	// {
	// 	$where = "(userid = '".$userid."')";
	// 	$this->datatables->select()
	// 					 ->from('tbl_return_type as r')
	// 					 ->where('active', 1)
	// 					 ->where($where);
	// 	$this->datatables->like('returnpolicy_name', $search);	
	// 	if($length != -1){
	// 		$this->db->limit($length,  $offset);		
	// 	}	
	// 	if($return_id != ''){
	// 		$this->datatables->where('id', $return_id);
	// 	} else {
	// 		if($search == ''){
	// 			$this->datatables->or_where('id', 0);
	// 		}
	// 	}
	// 	$this->db->order_by("is_default_2", "desc");
	// }

	function deleteReturn($return_id, $user_id, $data){
		$checker = $this->getDefaultsByUserId($user_id);
		$checker_2 = $this->getUserReturnsByUserId($user_id, $return_id);
		$this->db->where('userid', $user_id)
				 ->where('id', $return_id)
				 ->update('tbl_return_type', $data);
		if($checker_2[0]->is_default != '0' && $checker_2[0]->is_default_2 != '0'){
			if($checker != ""){
				if( $checker[0]->is_default == '1' && $checker[0]->is_default_2 == '1'){ //if no custom default selected
					$this->replaceChildReturn($user_id);
				}
			}
		}
		if($this->db->affected_rows() > 0){	
			return true;
		} else {
			return false;
		}
	}

	function replaceChildReturn($user_id){
		$this->db->where('id', '0')->set('child_return_ids', 'REPLACE(child_return_ids,",'.$user_id.'","")',FALSE)->update('tbl_return_type');
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function getDefaultsByUserId($user_id = '')
	{
		$this->db->select("is_default, is_default_2")
			->from('tbl_return_type')
			->where('userid', $user_id)
			->where("is_default","1")
			->where("is_default_2","1")	
			->where("active","1");
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();	
		}else{
			return FALSE;			
		}
	}

	public function getCountofRowPerUserId($user_id){
		$this->db->select("count(id)")
			->from('tbl_return_type')
			->where('userid', $user_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();	
		}else{
			return FALSE;			
		}
	}
}

?>