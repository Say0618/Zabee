<?php 
class Buyer extends Securearea{
	var $data = array();
	function __construct(){
		parent::__construct();
		$this->load->helper("url");
		$this->load->helper("security");
		$this->data['isloggedin'] = $this->isloggedin;
		if(!$this->isloggedin){
			redirect(base_url('login?required_login=1&redirect=buyer'),"refresh");
		}
		$this->data['active_page']	 = 'profile';
		$this->data['hasScript']	 = true;
		$this->data['user_type']	 = "buyer";
		$this->data['newsletter'] = false;
		// $this->lang->load('english','english');
		$this->lang->load('english', 'english');
	} 

	public function index(){ 
		$this->data['page_name'] 		= 'buyer_account_view';
		$this->data['title'] 			= "Profile";
		$this->data['showSidepanel']	= true;
		$this->data['newsletter'] 		= false;
		$this->data['active_page'] 		= 'profile';
		$s_id = $this->session->userdata('userid');
		$user_data= $this->User_Model->getUserForAcc($s_id);
		$this->data['profile_id'] = $s_id;
		$this->data['user'] = $user_data;
		$this->load->view('front/template', $this->data);
	}

	public function address_book(){
		$this->load->model("Addressbook_Model", 'AddressBook');
		if(!$this->isloggedin){
			redirect(base_url('checkout/signin'),"refresh");
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		if($this->session->flashdata('same_product')){
			$this->session->set_flashdata("same_product",$this->session->flashdata('same_product'));
			redirect(base_url("cart"),"refresh");
		}
		$locations = $this->AddressBook->getUserLocationsByUserIdforlocation($user_id, $this->data['user_type']);
		if($locations){
			$this->data['locations'] = $locations;
			$this->data['page_name'] = 'checkout_already_address';
			$this->data['checkerForCheckout'] = "1";
			$this->data['hasScript'] = false;
			$this->data['newsletter']	= true;
			$this->data['progress_bar'] = false;
			$this->data['bradcrumb'] = "Address";
			$this->data['title'] = "Address";
			$this->load->view('front/template', $this->data);
		} else {	
			redirect(base_url('buyer/address_book_add/1'),"refresh");
		}
	}

	public function address_book_add($checkerForCheckout = "",$location_id = ""){
		if($checkerForCheckout != 1 && $checkerForCheckout != 2){
			redirect(base_url());
		}
		$this->data['page_name']		= 'addressbook_add';
		$this->data['hasScript'] 		= true;
		$this->data['showSidepanel']	= true;
		$this->data['title'] 			= "Add Address";
		$this->data['active_page'] 		= 'address_book_add';
		$this->data['newsletter']		= false;
		$this->load->model("Addressbook_Model", 'AddressBook');
		$this->data['checkerForCheckout']	= $checkerForCheckout;
		$this->data['location_id'] = $location_id;
		$user_id = $this->session->userdata('userid');
		$this->load->library('form_validation');
		if($location_id !=""){
			$locations = $this->AddressBook->get_address($user_id,$location_id,$this->data['user_type']);
			if($locations['status'] == "1"){
				$this->data['locations'] = $locations['data'];
			}else{
				$this->session->set_flashdata('error', $locations['msg']);
			}
		}
		$this->form_validation->set_rules('fullname','Fullname','trim|required|min_length[2]');
		$this->form_validation->set_rules('contact','Phone','trim|required');
		$this->form_validation->set_rules('address1','Address','trim|required');
		$this->form_validation->set_rules('address2','Address','trim');
		$this->form_validation->set_rules('state','State','trim|required');
		$this->form_validation->set_rules('city','City','trim|required');
		$this->form_validation->set_rules('zip','Zip','trim|required|min_length[4]');
		$this->form_validation->set_rules('country','Country','trim|required');
		if($this->form_validation->run() == TRUE){
			$data = $this->input->post();
			$data['state'] = (isset($data['province']) && $data['province'] !="" && $data['country'] !=226)?$data['province']:$data['state'];
			$insert_data = array(
				'user_id' 	=> $user_id,
				'user_type' => $this->data['user_type'], 
				'fullname' 	=> $data['fullname'],
				'contact' 	=> $data['contact'],
				'address_1' => $data['address1'],
				'address_2' => $data['address2'],
				'country' 	=> $data['country'],
				'state' 	=> $data['state'],
				'city' 		=> $data['city'],
				'zip' 		=> $data['zip'],
				'active' 	=> "1",
			);
			//echo "<pre>";print_r($data);die();
			if($data['id'] == ""){
				$insert_data['created'] = date('Y-m-d H:i:s');
				$msg = "Address Added Successfully!";
				$resp = $this->AddressBook->add_addressbook($insert_data, $user_id);
				$location_id = $resp;
			}else{
				$insert_data['updated'] = date('Y-m-d H:i:s');
				$msg = "Address Updated Successfully!";
				$location_id = $data['id'];
				$resp = $this->AddressBook->update_addressbook($insert_data, $user_id, $location_id);
			}
			if($resp && $data['checkerForCheckout'] == 1){
				redirect(base_url('shipping?status=success&msg='.$msg));
			}else if($resp && $data['checkerForCheckout'] == 2){
				if(isset($_GET['b']) && $_GET['b']== "2"){
					redirect(base_url('checkout/payment?add_card=1&a='.$location_id));
				}else{
					redirect(base_url('checkout/useAddress/'.$location_id));
				}
			} else {
				redirect(base_url('shipping?status=success&msg='.$msg));
			}
		} 
		/* End */
		$this->load->model('Utilz_Model');
		$this->data['countryList'] = $this->Utilz_Model->countries();
		$this->data['statesList'] = $this->Utilz_Model->countries("","","",false,"tbl_states");
		$this->load->view('front/template', $this->data);
	}

	public function address_book_update($location_id){
		if($location_id == ''){
			redirect(base_url('address_book?status=invalid_addressbook&code=001'));
			die();
		}
		$this->data['location_id']		= $location_id;
		$this->data['page_name']		= 'addressbook_update';
		$this->data['showSidepanel']	= true;
		$this->data['title'] 			= "Edit Shipping Address";
		$this->data['newsletter'] 		= false;
		$this->data['active_page'] 		= 'address_book_update';
		$user_id = $this->session->userdata('userid');
		$this->load->library('form_validation');
		if(isset($_POST['ship_address']) &&  $_POST['ship_address'] == "different"){
			$this->form_validation->set_rules('shipfullname','Fullname','trim|required|min_length[2]');
			$this->form_validation->set_rules('shipcontact','Phone','trim|required');
			$this->form_validation->set_rules('shipaddress1','Address','trim|required');
			$this->form_validation->set_rules('shipaddress2','Address','trim');
			$this->form_validation->set_rules('shipstate','State','trim|required');
			$this->form_validation->set_rules('shipcity','City','trim|required'); 
			$this->form_validation->set_rules('shipzip','Zip','trim|required');
		}
		$this->form_validation->set_rules('billfullname','Fullname','trim|required|min_length[2]');
		$this->form_validation->set_rules('billcontact','Phone','trim|required');
		$this->form_validation->set_rules('billaddress1','Address','trim|required');
		$this->form_validation->set_rules('billaddress2','Address','trim');
		$this->form_validation->set_rules('billstate','State','trim|required');
		$this->form_validation->set_rules('billcity','City','trim|required');
		$this->form_validation->set_rules('billzip','Zip','trim|required|min_length[4]');
		$this->form_validation->set_rules('billcountry','Country','trim|required');
		$this->load->model("Addressbook_Model", 'AddressBook');
		if($this->form_validation->run() == TRUE){
			$data = $this->input->post();
			if($data['ship_address'] == "same"){
				$data['shipfullname'] = $data['billfullname'];
				$data['shipcontact']  = $data['billcontact'];
				$data['shipaddress1'] = $data['billaddress1'];
				$data['shipaddress2'] = $data['billaddress2'];
				$data['shipcountry']  = $data['billcountry'];
				$data['shipstate']    = (isset($data['billprovince']) && $data['billprovince'] !="" && $data['billcountry'] !=226)?$data['billprovince']:$data['billstate'];
				$data['shipcity']     =  $data['billcity'];
				$data['shipzip']      =  $data['billzip'];
			}
			$data['billstate'] = (isset($data['billprovince']) && $data['billprovince'] !="" && $data['billcountry'] !=226)?$data['billprovince']:$data['billstate'];
			$insert_data = array(
				'created' 		 => date('Y-m-d H:i:s'),
				'updated' 		 => date('Y-m-d H:i:s'),
				'user_id' 		 => $user_id,
				'user_type' 	 => $this->data['user_type'], 
				'ship_fullname'  => $data['shipfullname'],
				'ship_contact' 	 => $data['shipcontact'],
				'ship_address_1' => $data['shipaddress1'],
				'ship_address_2' => $data['shipaddress2'],
				'ship_country' 	 => $data['shipcountry'],
				'ship_state' 	 => $data['shipstate'],
				'ship_city' 	 => $data['shipcity'],
				'ship_zip' 		 => $data['shipzip'],
				'bill_fullname'  => $data['billfullname'],
				'bill_contact'   => $data['billcontact'],
				'bill_address_1' => $data['billaddress1'],
				'bill_address_2' => $data['billaddress2'],
				'bill_country'   => $data['billcountry'],
				'bill_state' 	 => $data['billstate'],
				'bill_city' 	 => $data['billcity'],
				'bill_zip' 		 => $data['billzip'],
				'active' 		 => 1,
			);
			$resp = $this->AddressBook->update_addressbook($insert_data, $user_id, $location_id);
			if($resp){
				if(isset($data['c']) && $data['c'] == 0){
					redirect(base_url('shipping'));
				}else if(isset($data['c']) && $data['c'] == 1){
					redirect(base_url('checkout'));
				}else{
					redirect(base_url('buyer/address_book?status=success'));
				}
			}
		/* End */
	}
		$locations = $this->AddressBook->getUserLocationsByUserId($user_id, $this->data['user_type'], $location_id);
		if(!$locations){
			redirect(base_url('address_book?status=invalid_addressbook&code=002'));
				die();
		}
		$location = $locations[0];
		$this->data['location'] =  $location;
		$this->load->model('Utilz_Model');
		$this->data['countryList'] = $this->Utilz_Model->countries();
		$this->data['statesList'] = $this->Utilz_Model->countries("","","",false,"tbl_states");
		$this->load->view('front/template', $this->data);
	}

	public function buyer_order($offset=0) 
	{
		$this->data['page_name']		= 'order_list';
		$this->data['showSidepanel']	= true;
		$this->data['title'] 			= "Orders"; 
		$this->data['active_page'] 		= 'orders';
		$this->data['newsletter'] 		= false;
		$s_id = $this->session->userdata('userid'); 
		$user_id = $this->session->userdata('userid');	
		$this->load->library('pagination');
		$config['base_url'] = base_url('orders/');
			$limit 						 = 10;
			$config["per_page"] 		 = $limit;
			$config['use_page_numbers']  = TRUE;
			$config['num_links'] 		 = 1;
			$config['full_tag_open'] = '<ul class="pagination flex-wrap">';
            $config['full_tag_close'] = '</ul>';
			$config['cur_tag_open'] 	 = '<li class="active"><a class="page-link current">';
			$config['cur_tag_close'] 	 = '</a></li>, ';
			$config['next_link'] 		 = '<span class="styleAngle">&gt</span>';
			$config['prev_link'] 		 =  '<span class="styleAngle">&lt</span>';
			$config['num_tag_open'] 	 = '<li>';
			$config['num_tag_close'] 	 = '</li>';
			$config['prev_tag_open'] 	 = '<li class="pg-tag">';
			$config['prev_tag_close'] 	 = '</li>';
			$config['next_tag_open'] 	 = '<li class="pg-tag">';
			$config['next_tag_close'] 	 = '</li>';
			$config['first_link'] 		 = FALSE;
			$config['last_link']  		 = FALSE;
			$config['first_tag_open'] 	 = '<li>';
			$config['first_tag_close'] 	 = '</li>';
			$config['last_tag_open'] 	 = '<li>';
			$config['last_tag_close'] 	 = '</li>';
			$config['page_query_string'] = TRUE;
	   if($this->input->get('page')){
		   $page = $this->input->get('page');
	   }else{
		   $page = 1;
	   }
	   $table_name = "tbl_transaction";
	   $order_by = 'created DESC';
	   $select = "order_id"; 
	   $where = array('user_id'=>$user_id);
	   $pageing = ($page-1)*$limit;
	   $order_id = $this->Utilz_Model->getAllData($table_name, $select, $where,0,"",$order_by,"",array($limit,$pageing));
	   $o_id = array();  
	   foreach($order_id as $order){
		$o_id[] = $order->order_id;
	   }
	   $order_list = $this->Cart_Model->getOrderByClientID($user_id, $o_id);
	   if( $order_list != ""){
		foreach($order_list as $o){
			$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
			if($o->product_image == ""){
				if(isset($productImage[0]->is_primary_image)){
					$o->product_image = isset($productImage[0])?$productImage[0]->is_primary_image:""; 
				}else{
					$o->product_image = "";
				}
			}
		}
	   $this->data['orders'] = $this->Cart_Model->formatOrderList($order_list,'data',$page,$config["per_page"]);
	//    echo "<pre>"; print_r($this->data['orders']); die();
	}
	    $this->session->set_userdata('review_prod_id', $this->input->post( 'product_id' ));
		$this->session->set_userdata('review_product_variant_id', $this->input->post( 'product_variant_id' ));
		$this->session->set_userdata('review_order_id', $this->input->post( 'order_id' ));
		$this->session->set_userdata('review_product_name', $this->input->post( 'product_name' ));
		$table_name = "tbl_transaction";
		$select = "COUNT(id) AS total_rows";
		$where = array('user_id'=>$user_id);
		$totalRows = $this->Utilz_Model->getAllData($table_name, $select, $where);
		$totalRows = $totalRows[0]->total_rows;
		$config["total_rows"] = $totalRows;
		$config['first_link'] ='First';
		$last_page_number = ceil($config["total_rows"]/$config["per_page"]);
		$config['last_link']  = 'Last';
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$links["links"] = explode(', ', $str_links);
		$this->data['links'] = $links;
		$this->Secure_Model->updateOrderNotification($user_id);
		$this->load->view('front/template', $this->data);
	}

	public function order_confirm($item_id){
		$user_id = $this->session->userdata('userid');	
		$order_item_id = $this->uri->segment(3);
		$order_list = $this->Cart_Model->getOrderByitemID($user_id, $order_item_id);
		$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$order_list[0]->product_id,'condition_id'=>'1'),"","","is_cover DESC");
			if($order_list[0]->product_image == ""){
				$order_list[0]->product_image = isset($productImage[0])?$productImage[0]->is_primary_image:""; 
			}
		$shippingAddress =  unserialize($order_list[0]->shipping);
		if(is_numeric($shippingAddress['state'])){
			$state = $this->Utilz_Model->getStatebyId($shippingAddress['state']);
			$this->data['state'] = $state[0]['state'];
		}
		$country = $this->Utilz_Model->getCountrybyId($shippingAddress['country']);
		$shipping_table = 'tbl_product_shipping';
		$select = array('title','duration');
		$where = array('shipping_id'=>$order_list[0]->item_shipping_id);
		$item_shipping_details = $this->Utilz_Model->getAllData($shipping_table, $select, $where);
		$this->data['page_name']			= 'order_confirm';
		$this->data['title'] 				= "Order Confirm";
		$this->data['hasScript'] 			= true;
		$this->data['active_page'] 			= 'order_confirm';
		$this->data['newsletter'] 			= false;
		$this->data['item_data'] 			= $order_list;
		$this->data['country'] 				= isset($country[0]['nicename'])?$country[0]['nicename']:"";
		$this->data['item_shipping_data']   = $item_shipping_details;
		$this->load->view('front/template', $this->data);
		}

	public function saveStatus(){
		$item_id = $_POST['id'];
		$status = $this->Cart_Model->update_item_status($item_id);
		return json_encode($status);
	}

	public function review($url_val)
	{
		$this->load->model("Reviewmodel");
		$decode_data = base64_decode(urldecode($url_val));
		$DATA = explode('-zabeeBreaker-',$decode_data);
		$result = $this->Reviewmodel->checkReview($DATA[1], $DATA[3]);
		if($result < 1){
			$this->data['page_name']		= 'review';
			$this->data['title'] 			= "Reviews";
			$this->data['hasScript'] 		= true;
			$this->data['active_page'] 		= 'review';
			$this->data['newsletter'] 		= false;
			$this->data['product_data'] 	= $DATA;
			$this->load->view('front/template', $this->data);
		}else{
			redirect(base_url("orders"));
		}
	}

	public function  forReviews(){
		$data = $this->input->post();
		$data['date'] = date("Y-m-d H:i:s UTC");
		$user_id = $this->session->userdata('userid');
		$image_count = $_FILES['profile_image']['name'];
		// print_r($image_count[0]); die();
		$review = addslashes($data['review']);
		$insert_data = array(
			'date' => $data['date'],
			'buyer_id' => $user_id,
			'seller_id' => $data['seller_id'],
			'order_id' => $data['order_id'],
			'email' => $data['email'],
			'name' => $data['name'],
			'product_name' => $data['pdt'],
			'review' => $review,
			'pv_id' => $data['product_variant_id'],
			'rating' => $data['rating'],
			'product_id' => $data['product_id'],
			'sp_id' => $data['sp_id'],
			'is_approved' => "1"
		);
		$reviewAdded = $this->Cart_Model->forReviews($insert_data,$user_id);
		if($reviewAdded){
			if($image_count[0] != ""){
				$config['encrypt_name'] 		= true;
				$config['overwrite'] 			= FALSE;
				$config['upload_path'] = "review";
				$config['upload_thumbnail_path'] = "review/thumbs";
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['quality'] = "100%";
				$config['remove_spaces'] = TRUE;
				$config['upload_thumbnail_width']         = 250;
				$config['upload_thumbnail_height']       = 250;
				for ($i=0; $i < count($image_count); $i++) { 
					$params['file'] = curl_file_create($_FILES['profile_image']['tmp_name'][$i], $_FILES['profile_image']['type'][$i], $_FILES['profile_image']['name'][$i]);
					$params['image_type'] = "review";
					$params['filesize'] = $_FILES['profile_image']['size'][$i];
					$params['config'] = json_encode($config);
					$upload_server = $this->config->item('media_url').'/file/upload_media';
					$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
					if($file->status != ""){
						$review_data['created_date'] = $data['date'];
						$review_data['review_id'] = $reviewAdded;
						$review_data['picture'] = $file->images->original->filename;
						$review_data['thumbnail'] = $file->images->thumbnail->filename;
						$this->Cart_Model->review_image($review_data);
						$message[] = array("msg"=>"Review add successful");
					}else{
						$message[] = array("name"=>$file->images->original->filename, "error"=>$file->message);
						//echo json_encode(array('status'=>1,'msg'=>$file->message,"code"=>2));exit;
					}
				}
			}
		}
		$this->data['reviewAdded'] = 1 ;
		redirect(base_url('orders?review=success'));
	}

	public function deleteaddress($location_id = ''){
		$this->load->model("Addressbook_Model", 'AddressBook');
		if($location_id == ''){
			redirect(base_url('checkout?status=invalid_addressbook&code=001'));
				die();
		}
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$deletelocations = $this->AddressBook->deleteaddress($location_id,$user_id);
		if($deletelocations){
			redirect(base_url('buyer/address_book?status=success&msg=Address Deleted Successfully!'));
		}
		else "something went wrong!";
	}

	public function save_account(){
		$s_id = $this->session->userdata('userid');
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->data['profile_id'] = '';
		$this->form_validation->set_rules('firstname','Fullname','trim|required|xss_clean');
		$this->form_validation->set_rules('lastname','Phone','trim|required|xss_clean');
		if($this->form_validation->run() == TRUE){
			$post_data = $this->input->post();
			$post_data['img'] = '';
			$this->data['profile_id'] = ($_POST['profile_id'] != "")?$_POST['profile_id']:'';
			if($this->data['profile_id'] == ''){
				$config['upload_path'] = "profile";
				$config['upload_thumbnail_path'] = "profile/thumbs";
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['quality'] = "100%";
				$config['remove_spaces'] = TRUE;
				
				$params['file'] = curl_file_create($_FILES['profile_image']['tmp_name'], $_FILES['profile_image']['type'], $_FILES['profile_image']['name']);
				$params['image_type'] = "profile";
				$params['filesize'] = $_FILES['profile_image']['size'];
				$params['config'] = json_encode($config);
				$upload_server = $this->config->item('media_url').'/file/upload_media';
				$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				$post_data['img'] = $file->images->original->filename;
				$resp = $this->User_Model->addUser($s_id, $post_data);
				if($resp){
					$_SESSION["user_pic"] = $post_data['img'];
					redirect(base_url('account?status=success'));
				} 
				redirect(base_url().'account');
			} else {
				//echo "<pre>";print_r($post_data);die();
				if($_FILES['profile_image']['name'] != ''){
					$config['upload_path'] 			 = "profile";
					$config['upload_thumbnail_path'] = "profile/thumbs";
					$config['allowed_types'] 		 = 'gif|jpg|png|jpeg';
					$config['quality'] 				 = "100%";
					$config['remove_spaces'] 		 = TRUE;
					$params['file'] 				 = curl_file_create($_FILES['profile_image']['tmp_name'], $_FILES['profile_image']['type'], $_FILES['profile_image']['name']);
					$params['image_type'] 			 = "profile";
					$params['filesize'] 			 = $_FILES['profile_image']['size'];
					$params['config'] 				 = json_encode($config);
					$upload_server = $this->config->item('media_url').'/file/upload_media';
					$file = $this->Utilz_Model->curlRequest($params, $upload_server, true);
					$post_data['img'] = $file->images->original->filename;
				}
				$resp = $this->User_Model->updateUser($s_id, $post_data);
				if($resp){
					$_SESSION['firstname'] = $post_data['firstname'];
					$_SESSION['lastname'] = $post_data['lastname'];
					$_SESSION["user_pic"] = $post_data['img'];
					redirect(base_url('account?status=success'));
				}
				redirect(base_url().'account');
			}
		}
		$profiles = $this->User_Model->getUserForAcc($s_id);
		if($profiles){
			$this->data['profile_id'] = $profiles->id;
			$this->data['user'] = $profiles;
		}
		$this->data['page_name'] 	 = 'buyer_account_view';
		$this->data['title'] 		 = "Account";
		$this->data['showSidepanel'] = true;
		$this->load->view('front/template', $this->data);
	}

	public function buyer_account(){
		$this->data['page_name'] 	= 'buyer_account_view';
		$this->data['title'] 		= "Account";
		$s_id = $this->session->userdata('userid');
		$user_data= $this->User_Model->getUserForAcc($s_id);
		$this->data['user'] = $user_data;
		$this->data['showSidepanel'] = true;
		$this->load->view('front/template', $this->data);
	}

	public function uploadImage($path="./uploads/profile/", $user_id = ""){
		$config['upload_path'] 	 = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['file_name'] 	 = 'buyer_'.$user_id.'.png';
		$config['overwrite'] 	 = true;
		$profile_image= array();
		if(isset($_FILES["profile_image"])){
			$_FILES['userfile']['name'] 	= $_FILES['profile_image']['name'];
			$_FILES['userfile']['type'] 	= $_FILES['profile_image']['type'];
			$_FILES['userfile']['tmp_name'] = $_FILES['profile_image']['tmp_name'];
			$_FILES['userfile']['error'] 	= $_FILES['profile_image']['error'];
			$_FILES['userfile']['size'] 	= $_FILES['profile_image']['size'];
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload()){
				$error = array('info' => $this->upload->display_errors());
				$this->session->set_flashdata("error", $this->upload->display_errors());	
				print_r($error);
			} else {
				$data = array('upload_data' => $this->upload->data());
				$img_name = 'buyer_'.$user_id.'.png';
				$profile_image['original'] = $img_name;
				}	
		} 
			return $profile_image;
	}
	
	public function getInvoice($order_id = '')
	{
		if($order_id == ''){
			$this->data['heading'] = '404 Error';
			$this->data['message'] = 'Invoice of order you are looking not found';
			$this->load->view('errors/html/error_404', $this->data);
			return;
		}
		$user_id = $this->session->userdata('userid');
		$orders = $this->Cart_Model->getOrderByClientID($user_id, $order_id);

		if(count($orders)> 0 ){
			foreach($orders as $o){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$o->product_id,'condition_id'=>'1'),"","","is_cover DESC");
				if($o->product_image == ""){
					$o->product_image = isset($productImage[0])?$productImage[0]->is_primary_image:""; 
				}
			}
			$sorders = array();
			$this->data['orders'] = $this->Cart_Model->formatOrderList($orders, 'data');

			// echo"<pre>"; print_r($this->data['orders']); die();
			for($i = 0;$i<count($orders); $i++){
				if(isset($sorders[$orders[$i]->seller_id])){
					$sorders[$orders[$i]->seller_id][] = $orders[$i];
				}else{
					$sorders[$orders[$i]->seller_id][] = $orders[$i];
					}
			} 
		}else {
			$this->data['heading'] = '404 Error';
			$this->data['message'] = 'Invoice of order you are looking not found';
			$this->load->view('errors/html/error_404', $this->data);
			return;
		}
		// echo"<pre>"; print_r($sorders); die();
		$this->data['sellerOrders'] = $sorders;
		$this->data['page_name'] 	= 'order_invoice';
		$this->data['title'] 		= "Order Invoice";
		$this->data['hasScript'] 	= true;
		$this->load->view('front/template', $this->data);
	}
	
	/*public function place_order(){
		$this->Cart_Model->save_order("","","","","","","","","","");
	}*/
	
	public function CancelOrder(){
		$status = $this->Cart_Model->cancel_order($this->input->post());
		if($status['status'] == "1"){
			$seller_email = $this->Utilz_Model->get_seller_email($status['code']);
			$subject = "Order Cancellation Request (".$this->input->post('order_id').")";
			$this->Utilz_Model->send_mail("","",$this->input->post('order_id'),$seller_email->email, $type="seller",$subject,"",$seller_email->firstname, "seller");
		}
		//print_r($status);
		echo json_encode($status);
	}
	
	public function sendNotification(){
		$user_id   = $this->session->userdata('userid');
		$action    = "Order cancellation request of your Product: ".$_POST['product_name']." is pending."; //$msg, $type, $user_id, $to, $u_type, $Utilz_Model
		$not_type  = '0';
		$from 	   = $user_id;
		$to 	   = $_POST['seller_id'];
		$user_type = "2";
		$status    = saveNoti($action, $not_type, $from, $to, $user_type, $this->load->model("Utilz_Model"));
		// /print_r($status);
		return json_encode($status);
		
	}
	
	public function buyer_cards($offset=0) 
	{
		$this->data['page_name']		= 'cards_list';
		$this->data['showSidepanel']	= true;
		$this->data['title'] 			= "Saved Cards"; 
		$this->data['active_page'] 		= 'saved_cards';
		$this->data['newsletter'] 		= false;
		$user_id = $this->session->userdata('userid');
		$this->load->library('pagination');
		$config['base_url'] 		 = base_url('account/saved_cards/');
		$limit 						 = 20;
		$config["per_page"] 		 = $limit;
		$config['use_page_numbers']  = TRUE;
		$config['num_links'] 		 = 1;
		$config['cur_tag_open'] 	 = '<li class="active"><a class="page-link current">';
		$config['cur_tag_close'] 	 = '</a></li>, ';
		$config['next_link'] 		 = '<span class="styleAngle">&gt</span>';
		$config['prev_link'] 		 =  '<span class="styleAngle">&lt</span>';
		$config['num_tag_open'] 	 = '<li>';
		$config['num_tag_close'] 	 = '</li>';
		$config['prev_tag_open'] 	 = '<li class="pg-tag">';
		$config['prev_tag_close'] 	 = '</li>';
		$config['next_tag_open'] 	 = '<li class="pg-tag">';
		$config['next_tag_close'] 	 = '</li>';
		$config['first_link'] 		 = FALSE;
		$config['last_link']  		 = FALSE;
		$config['first_tag_open'] 	 = '<li>';
		$config['first_tag_close'] 	 = '</li>';
		$config['last_tag_open'] 	 = '<li>';
		$config['last_tag_close'] 	 = '</li>';
		$config['page_query_string'] = TRUE;
	   if($this->input->get('page')){
		   $page = $this->input->get('page');
	   }else{
		   $page = 1;
	   }
		$pageing = ($page-1)*$limit;
		$getCards = $this->User_Model->getStripeCards($user_id, 0, $limit, $pageing);
		$this->data['cards'] = ($getCards['status'] == 0)?array():$getCards['data'];
		$getCardsCount = $this->User_Model->getStripeCards($user_id, 0, 0, 0, true);
		//echo "<pre>";print_r($this->data['cards']);die();
		$config["total_rows"] = ($getCardsCount['status'] == 1)?$getCardsCount['data'][0]->rows:0;
		$config['first_link'] ='First';
		$last_page_number = ceil($config["total_rows"]/$config["per_page"]);//print_r($last_page_number);
		$config['last_link']  = 'Last';
		$this->pagination->initialize($config);
		$str_links = $this->pagination->create_links();
		$links["links"] = explode(', ', $str_links);
		$this->data['links'] = $links;
		$this->load->view('front/template', $this->data);
	}
	
	public function delete_card($card_id = '',$from=""){
		if($card_id == ''){
			redirect(base_url('account/saved_cards/?status=invalid_card&code=001'));
			die();
		}
		$user_id = $this->session->userdata('userid');
		$deletecard = $this->User_Model->deleteCard($user_id, $card_id);
		if($deletecard){
			if($from == ""){
				redirect(base_url('account/saved_cards/?status=success'));
			}else{
				redirect(base_url('checkout/payment?status=success&msg=Card Deleted Successfully'));
			}
		} else {
			redirect(base_url('account/saved_cards/?status=invalid_card&code=002'));
		}
	}

	public function getReviewData(){
		// echo"<pre>"; print_r($this->input->post("orderData")); die();
		$this->load->model("Reviewmodel");
		$orderData = $this->input->post("orderData");
		echo json_encode($this->Reviewmodel->checkReview($orderData[1], $orderData[3], "buyer"));
	}

	public function delete_account(){
		$user_id = $this->session->userdata('userid');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('profile_id','User ID','trim|required|xss_clean');
		if($this->form_validation->run() == TRUE){
			$resp = $this->User_Model->deleteUser($user_id);
			if($resp['status'] == 1){
				redirect(base_url('logout'));
			}
		}
		$this->data['page_name'] 	= 'buyer_account_delete';
		$this->data['title'] 		= "Delete Account";
		$user_data= $this->User_Model->getUserForAcc($user_id);
		$this->data['user'] = $user_data;
		$this->data['profile_id'] 	= $user_data->id;
		$this->data['hasScript'] 	= false;
		$this->load->view('front/template', $this->data);
	}
}
?> 