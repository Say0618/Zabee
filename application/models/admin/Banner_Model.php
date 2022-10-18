<?php
class Banner_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		//$this->load->database("default");
	}

	public function getBanner($userid, $banner_id = '')
	{
		$this->datatables->select('b.id as id, CONCAT(u.firstname," ",u.lastname) as fullname, banner_image, SUBSTRING(`banner_link`, 1, 50), SUBSTRING(`product_link`, 1, 50), display_status')
				->from('tbl_banners as b')->join("tbl_users u","u.userid = b.userid");
				//->where('userid', $userid);
		$this->datatables->where("b.banner_image !=''",NULL);		
		$this->db->order_by('b.display_status DESC,b.id DESC');		
		if($banner_id != ''){
			$this->datatables->where('id', $banner_id);
		} else {
			//$this->datatables->or_where('id', 0);
		}
	}

	public function getBannerForFront($userid="",$select="*",$cat_id="")
	{
		$this->db->cache_on();
		if($userid){
			$this->db->where('userid', $userid);
		}
		$where = 'active="1" AND display_status="1"';
		$this->db->select($select)->from('tbl_banners')
				 //->where('active', 1)->where('display_status',"1")
				 ->order_by('id','DESC');
		if($cat_id){
			$where .= " AND FIND_IN_SET($cat_id,category_id)";
		}
		$this->db->where($where,NULL);
		$query = $this->db->get();
		//echo $this->db->last_query();die();
		if($query && $query->num_rows()>0){
			$this->db->cache_off();
			return $query->result();
		}else{
			$this->db->cache_off();
			return FALSE;			
		}
	}

	public function banner_add($data, $user_id){
		$insert_data = array(
			'created' => date('Y-m-d H:i:s'),
			'updated' => date('Y-m-d H:i:s'),
			'userid' => $user_id,
			'banner_link' => ($data['linktype'] == 'ExternalLink')?$data['Banner_Link']:'',
			'product_link' => ($data['linktype'] == 'ProductLink')?$data['Product_Link']:'',
			'banner_image' => $data['image'],
			'image_alt'		=> $data['image_alt'],
			'banner_thumbnail' => $data['thumbnail'],
			'extra_params' => $data['optional_field'],
			'product_id' => $data['product_id'],
			'category_id' => ($data['category_id'])?implode(",",$data['category_id']):"",
			'active' => 1
		);
		$this->db->insert('tbl_banners', $insert_data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}

	public function banner_update($data,$banner_id = ''){
		$insert_data = array(
			'updated' => date('Y-m-d H:i:s'),
			'userid'=>$data['userid'],
			'banner_link' =>($data['linktype'] == 'ExternalLink')?$data['Banner_Link']:'',
			'product_link' => ($data['linktype'] == 'ProductLink')?$data['Product_Link']:'',
			'extra_params' => $data['optional_field'],
			'product_id' => $data['product_id'],
			'image_alt'  => $data['image_alt'],
			'category_id' => ($data['category_id'])?implode(",",$data['category_id']):"",
			'active' => 1
			);
		if(isset($data['image'])){
			$insert_data['banner_image'] =  $data['image'];
			$insert_data['banner_thumbnail'] = $data['thumbnail'];
		}
		//echo "<pre>";print_r($insert_data);die();
		$this->db->where('id', $banner_id)->update('tbl_banners', $insert_data);
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	public function getBannerByIdForEdit($banner_id = ''){
		$this->db->select('*')
			 ->from("tbl_banners")
			 ->where('id', $banner_id);
		$query = $this->db->get();
		if($query && $query->num_rows()>0){
			return $query->result();
		}else{
			return FALSE;			
		}
	}

	public function deleteBanner($banner_id, $user_id, $value){
		$insert_data = array(
			'display_status' => $value
		);
		$this->db->where('id', $banner_id)
				 ->update('tbl_banners', $insert_data);
				// echo $banner_id."<br>".$user_id; die();
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}

	function hard_delete_banner($banner_id, $user_id){
		$this->db->where('id', $banner_id)->delete('tbl_banners');
		if($this->db->affected_rows() > 0){
			$this->db->cache_delete('default', 'index');
			return true;
		} else {
			return false;
		}
	}
}	
?>