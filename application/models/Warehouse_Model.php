<?php
class Warehouse_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	public function checkWarehouse($user_id,$warehouse_id = ""){
		$result['rows'] = 0;
		$result['result'] = ""; 
		$this->db->where('w.warehouse_id',$warehouse_id);
		$query =   $this->db->select('w.*')
						->from('tbl_warehouse w')
						->where('w.user_id',$user_id)->get();	 
		$result = $query->result();
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $query->result();
		}
		return $result;
	}

	public function getWarehouse($user_id, $search, $offset, $length, $warehouse_id = '')
	{
		$this->datatables->select('w.warehouse_id as id,w.warehouse_title,wc.warehouse_class,w.address,w.email,w.contact_no,c.nicename,if(w.state_id=0,w.province,s.state),w.city,w.zip_code')
						 ->from(DBPREFIX.'_warehouse w')
						 ->join(DBPREFIX.'_country c','w.country_id=c.id')
						 ->join(DBPREFIX.'_states s','w.state_id=s.id',"LEFT")
						 ->join(DBPREFIX.'_warehouse_class wc','w.warehouse_class_id=wc.warehouse_class_id')
						 ->where('w.user_id', $user_id);
		$this->datatables->like('w.warehouse_title', $search);
		$this->datatables->like('w.address', $search);
		$this->datatables->like('w.email', $search);
		$this->datatables->like('w.contact_no', $search);
		$this->db->order_by('w.warehouse_id', 'desc');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		  }				 
		if($warehouse_id != ''){
			$this->datatables->where('w.warehouse_id', $warehouse_id);
		}
	}

	public function getWarehouseInventory($search = "", $offset = "", $length = "", $user_id = "", $usertype = ""){
		if($usertype == 1){
			$pi = 'pi.inventory_id = win.inventory_id AND pi.seller_product_id = win.seller_product_id AND pi.product_variant_id = win.product_variant_id AND win.warehouse_id IN (SELECT warehouse_id FROM tbl_warehouse WHERE user_id ="'.$user_id.'")';
		}else{
			$pi = 'pi.inventory_id = win.inventory_id AND pi.seller_product_id = win.seller_product_id AND pi.product_variant_id = win.product_variant_id AND pi.seller_id="'.$user_id.'"';
		}
		$this->datatables->SELECT("win.id,win.inventory_id, pi.`created_date`,ss.store_name as store_name,  pi.`product_name`, pc.`condition_name`, GROUP_CONCAT(v_title) AS variant, win.`quantity` as quantity, pi.`price`,w.warehouse_title,win.warehouse_id as w_id,win.received_date,win.is_received")
						 ->FROM(DBPREFIX."_warehouse_inventory as `win`")
						 ->JOIN(DBPREFIX.'_warehouse AS w','w.`warehouse_id` = win.`warehouse_id`')
						 ->JOIN(DBPREFIX."_product_inventory as `pi`",$pi,FALSE)
						 ->JOIN(DBPREFIX.'_product_variant AS pv','pi.`product_variant_id` = pv.`pv_id`')
						 ->JOIN(DBPREFIX.'_product_conditions AS pc','pi.`condition_id` = pc.`condition_id`')
						 ->JOIN(DBPREFIX.'_variant AS v ', ' FIND_IN_SET(v.v_id, pv.variant_group) > 0', 'left')
						 ->join("tbl_users as users1",'pi.seller_id = users1.userid','LEFT')
						 ->join(DBPREFIX.'_seller_store as ss','ss.seller_id = pi.seller_id')
						 ->group_by("win.`id`");
		$this->datatables->LIKE('product_name', $search);
		$this->db->order_by('pi.inventory_id','DESC');
		if($length != -1){
			$this->db->limit($length,  $offset);		
		}
	}

	public function is_received($id,$inventory_id){
		$this->db->trans_start(); # Starting Transaction
		$date = date('Y-m-d H:i:s');
		$date_utc = gmdate("Y-m-d\TH:i:s");
		$date_utc = str_replace("T"," ",$date_utc);
		$warehouseData = array("is_received"=>"1","received_date"=>$date_utc);
		$this->db->where("id",$id);
		$this->db->update("tbl_warehouse_inventory",$warehouseData);
		$pvData = array("approve"=>"1","approved_date"=>$date_utc);
		$this->db->where("inventory_id",$inventory_id);
		$this->db->update("tbl_product_inventory",$pvData);
		$this->db->trans_complete(); # Completing transaction
		if ($this->db->trans_status() === FALSE) {
			# Something went wrong.
			$this->db->trans_rollback();
			return array("status"=>0,"msg"=>"Update Failed!");
		} else {
			# Everything is Perfect. 
			# Committing data to the database.
			$this->db->trans_commit();
			return array("status"=>1,"msg"=>"Update successfully!");
		}
	}

	/* Login */
	function checkWarehouseLoginDetails($userid="",$username="",$password="",$isUser = false)
	{
		$this->db->select('u.id,u.email, u.firstname,u.middlename, u.lastname, u.social_id, u.userid, u.user_pic,u.social_id,u.social_platform,u.is_active,u.is_deleted,u.email_verified,w.warehouse_id,w.warehouse_title');
		if($userid){
			if($isUser){
				$this->db->where(array("u.social_id"=>$userid));
			}else{
				$this->db->where(array("u.userid"=>$userid));
			}
		}else{
			$this->db->where(array("u.email"=>$username,"u.password"=>$password));
		}
		$this->db->join(DBPREFIX."_warehouse w",'w.user_id =u.userid','LEFT',FALSE);
		$this->db->limit(1);
		$this->db->from(DBPREFIX."_users u");
		$query = $this->db->get();
		if ($query->num_rows() > 0){
			return $query->result_array();
		}		
		return FALSE;
	}

	function checkWarehouseLink($link,$cur_user)
	{	
		$str_link = "";
		foreach($link as $eachlink){ 
			$str_link .= $eachlink."/";
		}
		if(isset($link[1]) && $link[1] =="warehouse"){
			$str_link = $link[1];
		}else{
			$str_link = "";
		}
		$extrawhere = "";	
		$sql = "SELECT * FROM ".DBPREFIX."_backend_usertype WHERE user_type_id = '".$cur_user."' ".$extrawhere;							
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$data = $query->result_array();
			if(in_array($str_link,explode(",",$data[0]["allowed_links"])) || $str_link == "login" || $str_link == "" || $data[0]["allowed_links"] == "*"){
				return array("display_name"=>$data[0]['user_type_dpname'],"allowedmodules"=>$data[0]['allowed_links']);
			}			
		}		
		return FALSE;
	}

	public function checkUserWarehouse($user_id){
		$query = $this->db->select('w.warehouse_id')->from('tbl_warehouse w')->join('tbl_users u','w.user_id=u.userid')->where('w.user_id',$user_id)->get();	
		$result = $query->num_rows();
		if ($result > 0){
			return 1;
		}		
		return FALSE;
	}

	/*Login Ends*/
	public function getData($table_name, $select="*", $where="",$where_in="0",$group_by="",$order_by="",$join="",$limit="",$whereExtra="",$offset="",$anOtherWhere=""){
		if(!empty($where)){
			if($where_in !="0" && $where_in !=""){
				if($whereExtra !=""){
					$this->db->where_in($where_in,$where,FALSE,NULL);
				}else{
					$this->db->where_in($where_in,$where);
				}
			}else{
				$this->db->where($where);
			}
			if($anOtherWhere !=""){
				$this->db->where($anOtherWhere);
			}
		}
		if($group_by !=""){
			$this->db->group_by($group_by);
		}
		if($order_by !=""){
			$this->db->order_by($order_by);
		}
		if($limit !=""){
			if($offset){
				$this->db->limit($offset,$limit);
			}else{
				$this->db->limit($limit);
			}
		}
		if($join !=""){
			foreach($join as $key=>$value){
				$this->db->join($key,$value);
			}
		}
		$this->db->select($select)->from($table_name);
		$query = $this->db->get();
		$return = $query->result();
		return $return ;
	}

	public function checkProductSell($created_id){
		$sql = "SELECT * FROM ".DBPREFIX."_orders WHERE `created_id` = '".$created_id."' && is_viewed='0'";		
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0){
			return 1;
		}		
		return FALSE;
	}

	public function checkMsgNotification($receiver_id){
		$result['rows'] =0;
		$result['result'] = "";
		$query =$this->db->select("m1.message_id,u.userid,m1.seen_datetime,CONCAT(u.`firstname`, ' ', u.lastname) AS sender_name,u.user_pic,m1.message, m1.product_variant_id,m1.item_type,m1.seller_id,m1.buyer_id,m1.item_id")
						 ->from('tbl_message m1')
						 ->join('tbl_users u','(m1.sender_id = u.userid OR m1.receiver_id = u.userid) AND u.userid !="'.$receiver_id.'"',null,false)
						 ->where('m1.receiver_id="'.$receiver_id.'" AND m1.`seen_datetime` IS NULL ',NULL,FALSE)
						 ->group_by('u.userid,m1.product_variant_id')->order_by('m1.message_id','desc')->get();
		if ($query->num_rows() >0){
			$result['rows'] = $query->num_rows();
			$result['result'] = $query->result();
		}
		return $result;		 
	}

	public function checkProductDetails($product_id,$country_id,$product_variant_id = ""){
		if($product_variant_id != ""){
			$this->db->where('pin.`product_variant_id`',$product_variant_id);
		}
		if($country_id !=""){
			$this->db->where('(pr.country_id = "'.$country_id.'" OR pr.country_id = "0")',NULL,false);
		}
		if($product_id !=""){
			$this->db->where('product.product_id' ,$product_id);
		}
		$this->db->select('sp.sp_id,ss.store_name,GROUP_CONCAT(DISTINCT sp.`sp_id`) AS seller_product_id,(COUNT(DISTINCT sp.`seller_id`) - 1) AS total_seller,pin.`price`,pin.`condition_id`, pin.`seller_id`,`product`.`product_id`,`c`.`category_id`,`c`.`category_name`,`b`.`brand_id`,`b`.`brand_name`,`product`.`upc_code`,`product`.`product_name`,`product`.`product_description`,`product`.`is_active`,pin.product_variant_id AS pv_id,pin.quantity ')
				 ->from('tbl_product AS product')
				 ->join('tbl_product_regions as pr','pr.`product_id` = product.product_id')
				 ->join('tbl_categories c','c.`category_id` = product.`sub_category_id`')
				 ->join('tbl_brands b','b.`brand_id` = product.`brand_id`')
				 ->join('tbl_seller_product sp','sp.product_id = product.product_id')
				 ->join('tbl_product_inventory pin','pin.`seller_product_id` = sp.`sp_id` AND pin.`quantity` > 0 ')
				 ->join('tbl_seller_store ss','ss.seller_id = pin.seller_id')
				 ->group_by('product.`product_id`')
				 ->order_by('sp.condition_id,pin.price');
		$product = $this->db->get();
		$data['productDataRows'] = $product->num_rows();
		$data['productData'] = $product->row();
		return $data;
	 }

	 public function productPipline($search = "", $request = "", $userid = ""){
		if($search !=""){ 
			$search = trim($search);	
			$this->db->group_start();		
			$this->db->like('product.product_name',$search);
			$this->db->or_like('cat.category_name',$search);
			$this->db->or_like('b.brand_name',$search);
			$this->db->group_end();
		}
		$this->db->select('sp.seller_id,product.product_id,pm.thumbnail AS is_primary_image ,pm.is_local ,pm.is_image, product.product_name, cat.category_name, b.brand_name,pin.discount,sp.is_active')
				 ->from(DBPREFIX."_product as product")
				 ->join(DBPREFIX."_seller_product AS sp",'sp.product_id = product.product_id')
				 ->join(DBPREFIX."_seller_product_variant AS spv",'sp.sp_id = spv.sp_id','LEFT') 
				 ->join(DBPREFIX."_variant AS v",'v.v_id = spv.v_id','LEFT')
				 ->join(DBPREFIX."_brands AS b",'b.brand_id = product.brand_id')
				 ->join(DBPREFIX."_categories AS cat",'(cat.category_id = product.category_id OR cat.category_id = product.sub_category_id)')
				 ->join(DBPREFIX."_product_conditions` AS pc",'pc.condition_id = sp.condition_id')   
				 ->join(DBPREFIX."_product_media AS pm",'pm.product_id = product.product_id AND (pm.sp_id = sp.sp_id OR pm.condition_id = 1)  AND pm.is_cover','LEFT')
				 ->join(DBPREFIX."_product_inventory AS pin",'pin.seller_product_id = sp.sp_id')
				 ->join(DBPREFIX."_warehouse_inventory AS wi","wi.inventory_id = pin.inventory_id AND wi.warehouse_id IN(SELECT warehouse_id FROM tbl_warehouse WHERE user_id='".$userid."' )")
				 ->group_by('product.product_id,sp.seller_id')->order_by('sp.created_date','DESC ');
		$this->db->where('sp.is_active',"1");
		$this->db->where('sp.approve',"1");
		if($request['start'] != 0){
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

	public function warehouse_inventory_update($id,$w_id){
		$where = array('wi.inventory_id'=> $id,'wi.warehouse_id' => $w_id);
		$this->db->select("wi.quantity,wi.warehouse_id,w.warehouse_title")
				 ->from("tbl_warehouse_inventory as wi")
				 ->join(DBPREFIX."_warehouse as w", 'wi.`warehouse_id` = w.`warehouse_id`', 'left')
				 ->where($where);
		$query = $this->db->get();
		$data= $query->row();
		return $data;
	}

	public function update_quantityandWarehouse($data){
		static $i = 0;
		static $newArr = array();
		static $tempQty = 0;

		$where = array('inventory_id'=>$data['inventory_id'],'warehouse_id'=>$data['warehouse_id']);
		$totalqty = $this->getTotalQtyOfInventory($data['inventory_id']);
		$totalqty = $totalqty[0]->quantity;
		$newArr[$i] = $totalqty;
		$i++;
		$qty = $this->getDataFromExistingWarehouseInventories($data['warehouse_id'], $data['inventory_id']);
		print_r($qty);
		$qty = $qty[0]->quantity;
		
		if($data['warehouse_id'] == $data['current_warehouse']){
			$insert_data = array(
				'updated_date' =>date("Y-m-d H:i:s",gmt_to_local(time(),"UP45")),
				'quantity' =>  $data['quantity'],
				'warehouse_id' => $data['warehouse_id']
			);
		} else {
			$insert_data = array(
				'updated_date' =>date("Y-m-d H:i:s",gmt_to_local(time(),"UP45")),
				'quantity' => $data['quantity'] + $qty,
				'warehouse_id' => $data['warehouse_id']
			);
			$dqpq = $data['quantity'] + $qty;
			$tempQty = $tempQty + $dqpq;
			if($tempQty == $newArr[0]){
				$insert_data_2 = array(
					'updated_date' =>date("Y-m-d H:i:s",gmt_to_local(time(),"UP45")),
					'quantity' => '0'
				);
			}
		}

		$this->db->where($where)->update(DBPREFIX.'_warehouse_inventory', $insert_data);
				if($this->db->affected_rows() == 0){
					$this->db->where('inventory_id',$data['inventory_id']); 
					$query = $this->db->get(DBPREFIX.'_warehouse_inventory');
					$result = $query->result();
					$date = date("Y-m-d H:i:s",gmt_to_local(time(),"UP45"));
					$data = array(
						'warehouse_id' => $data['warehouse_id'],
						'inventory_id' => $result[0]->inventory_id,
						'product_variant_id' => $result[0]->product_variant_id,
						'seller_product_id' => $result[0]->seller_product_id,
						'shipping_id' => $result[0]->shipping_id,
						'quantity' => $data['quantity'],
						'created_date' => $result[0]->created_date,
						'updated_date' => $date,
						'received_date' => $result[0]->received_date,
						'seller_shipped' => $result[0]->seller_shipped,
						'is_received' => $result[0]->is_received,
						'is_closed' => $result[0]->is_closed
					);
					$this->db->insert(DBPREFIX.'_warehouse_inventory',$data);
					return true;
				} else {
					if(isset($insert_data_2)){
						$where = array('inventory_id'=>$data['inventory_id'],'warehouse_id'=>$data['current_warehouse']);
						$this->db->where($where)->update(DBPREFIX.'_warehouse_inventory', $insert_data_2);
					}
					if($this->db->affected_rows() > 0){
						return true;
					} else {
						return true;
					}
				}	
		}

	public function getDataFromExistingWarehouseInventories($warehouse_id, $inventory_id){
		$this->db->select("quantity")
				 ->from("tbl_warehouse_inventory")
				 ->where('warehouse_id', $warehouse_id)
				 ->where('inventory_id', $inventory_id);
		$query = $this->db->get();
		$data= $query->row();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;			
		}
	}
		
	public function getTotalQtyOfInventory($inventory_id){
		$this->db->select("sum(quantity) as quantity")
				->from("tbl_warehouse_inventory")
				->where('inventory_id', $inventory_id);
		$query = $this->db->get();
		$data= $query->row();
		if($query && $query->num_rows()>0){
			return $query->result();
		} else {
			return FALSE;			
		}
	}
}
?>