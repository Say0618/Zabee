<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends Securearea {
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->helper("url");
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->model("admin/Banner_Model");
		$this->load->model("User_Model");
		$this->load->model("Utilz_Model");
		$this->load->model("Affiliate_Model");
		$this->data['el']="";
		if(isset($_COOKIE['country_id']) && $_COOKIE['country_id'] != 0){
			$this->region = $_COOKIE['country_id'];
		}
		if($this->session->userdata('userid')){
			$this->data['user_id'] = $this->session->userdata('userid');
		}else{
			$this->data['user_id'] = "";
		}
		$this->lang->load('english','english');
	}
	public function index(){
		unset($_SESSION['view']);
		$this->data['wishlist_categories']		= FALSE;
		$this->data['check_email_verif']		= FALSE;
		$this->data['wishlist_check']			= FALSE;
		$this->data['text_notification']		= FALSE;
		$user_id = $this->data['user_id'];
		$querySelect = "";
		if($user_id !=""){$querySelect = ", w.wish_id as already_saved";}
		$region="";
		if(isset($_COOKIE['country_id']) && $_COOKIE['country_id'] != 0){ $region = $_COOKIE['country_id'];}
		$category_id = $this->Utilz_Model->getShowCategory();
		//Featured Products.
		$where = array('product.is_private' =>'0','cat.is_homepage'=>"1","product.is_featured"=>"1");
		$select = 'product.product_name,product.slug,product.product_id,pm.thumbnail,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,AVG(preview.`rating`) AS rating,pin.price,d.value as discount_value,d.type as discount_type, d.valid_from as valid_from, d.valid_to as valid_to, sp.seller_id'.$querySelect;
		$featured = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
		//New in Store
		$where = array('product.is_private' =>'0','cat.is_homepage'=>"1","product.is_featured"=>"0");
		$product = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
		// echo $this->db->last_query();die();
		//Top rated products.
		$where = array('preview.rating >'=>3,'product.is_private' =>'0',"product.is_featured"=>"0");
		$topRated = $this->Product_Model->frontProductDetails('','','','','','8','rating DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
		//Category wise products.
		$category = array();
		if(!empty($category_id)){
			$i=0;
			foreach($category_id as $cat_id){
				$c_id = $this->getAllCategoriesChildId($cat_id->category_id);
				if($c_id ==""){
					$c_id = $cat_id->category_id;
				}
				$where = 'cat.category_id IN('.$c_id.') AND product.is_private="0" AND cat.is_homepage="1" AND product.is_featured="0"';
				$category[$i] = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
				$category[$i]['cat_name'] = $cat_id->category_name;
				$category[$i]['is_slider'] = 1;
				$i++;
			}
		}
		if($user_id){
			$this->data['check_email_verif']	= $this->Secure_Model->check_email_verification($user_id);
			$this->data['wishlist_categories']	= $this->Secure_Model->WishlistViaCategories($user_id);
			$this->data['wishlist_check']		= "";//$this->Secure_Model->ClearWishlistIfNoCategories($user_id);
			$this->data['text_notification']	= $this->Secure_Model->checkMsgNotification($user_id);
		}
		$this->data['banner']					= $this->Banner_Model->getBannerForFront();
		$this->data['page_name'] 				= 'home';
		$this->data['title'] 					= $this->config->item("web_name");
		$this->data['show_on_homepage'] 		= (!empty($category))?$category:array();
		$this->data['product']					= (!empty($product))?$product:array();
		$this->data['topRated']					= (!empty($topRated))?$topRated:array();
		$this->data['featured'] 				= (!empty($featured))?$featured:array();
		$this->data['hasScript'] 				= true;
		$this->data['newsletter'] 				= true;
		$this->data['hasStyle'] 				= false;
		$this->load->view('front/template', $this->data);
	}

	public function change_password()
	{
		$this->data['isloggedin'] = $this->isloggedin;
		if(!$this->isloggedin){
			redirect(base_url('login?required_login=1'),"refresh");
		}
		$this->data['page_name'] 	= 'change_password';
		$this->data['hasScript'] = true;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Change Password";
		$this->load->view('front/template', $this->data);

	}
	public function change_password_process(){
		$this->form_validation->set_rules('password','Password','trim|required');
		$this->form_validation->set_rules('confirm_password','confirm_password','trim|required|matches[password]');
		$this->form_validation->set_rules('current_password','current password','trim|required');
		if($this->form_validation->run() == TRUE){
			$current_password = $_POST['current_password'];
			$password=$_POST['password'];
			$userid=$_SESSION['userid'];
			$email=$_SESSION['email'];
			$firstSalt = explode("@",$email);
			$lastSalt = "zab.ee";
			$hash = $firstSalt[0].$password.$lastSalt;
			$hash2 = $firstSalt[0].$current_password.$lastSalt;
			$array = array('password'=>sha1($hash));
			$current_password = sha1($hash2);
			$return = $this->User_Model->getPassword($email,$current_password);
			if($return){
				$updated = $this->User_Model->update_password($email,$array);
				if($updated){
					//echo "hi" die();
					$this->session->set_flashdata('changed',1);
					redirect('account');
					}else{
						$this->session->set_flashdata('previous_password',1);
						redirect('change_password');
					}
			} else{
				$this->session->set_flashdata('Worng_Current_Password',$current_password);
				redirect('change_password');

			}
			$this->data['page_name'] 	= 'change_password';
			$this->data['title'] 		= "Change Password";
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);
		}
		else{
			$this->session->set_flashdata('confirm_password',1);
			redirect('change_password');
			$this->data['page_name'] 	= 'change_password';
			$this->data['title'] 		= "Change Password";
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);
		}
	}

	public function join_us()
	{
		if(!$this->isloggedin){
			$this->data['page_name'] 	= 'join_us';
			$this->data['title'] 		= "Register";
			$this->data['hasScript'] 	= TRUE;
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);
		}else{
			redirect(base_url());
		}
	}
	// public function email()
	// {
	// 	$this->data['page_name'] 	= 'email';
	// 	$this->data['title'] 		= "Register";
	// 	$this->data['hasScript'] 	= TRUE;
	// 	$this->data['newsletter'] = false;
	// 	$this->load->view('front/template', $this->data);
	// }
	public function registration(){
		$post_data = http_build_query(
			array(
				'secret' => $this->config->item("recaptcha_secret"),
				'response' => $_POST['g-recaptcha-response'],
				'remoteip' => $_SERVER['REMOTE_ADDR']
			)
		);
		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $post_data
			)
		);
		$context  = stream_context_create($opts);
		$response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
		$result = json_decode($response);
		if (!$result->success) {
			$this->session->set_flashdata('alert','CAPTCHA verification failed.');
			redirect(base_url('join_us?status=error'));
		}else{
			$type = $this->input->post('type');
			$data = $this->input->post();
			$first_name=trim($this->input->post('first_name'));
			$middle_name=trim($this->input->post('middle_name'));
			$last_name=trim($this->input->post('last_name'));
			$email=trim($this->input->post('email'));
			$password=trim($this->input->post('password'));
			$confirm_password=trim($this->input->post('confirm_password'));
			$ref_code = $_POST['ref_code'];
			$this->form_validation->set_rules('email','Email','trim|required|valid_email|callback_checkEmailExist['.$this->input->post('email').']');
			if($this->form_validation->run() == FALSE){
				//die("here");
				if($password==$confirm_password){
					$firstSalt = explode("@",$email);
					$lastSalt = "zab.ee";
					$password = $firstSalt[0].$password.$lastSalt;
					$password = sha1($password);
					$array = array('userid'=>uniqid(),"user_type"=>2, 'firstname'=>$first_name, 'middlename'=>$middle_name, 'lastname'=>$last_name, 'email'=>$email, 'password'=>$password,'social_id'=>"",'social_platform'=>'','social_info'=>'','mobile'=>'','is_active'=>'1');
					$this->User_Model->addNewUser($array, "", $ref_code);
					$this->User_Model->addDefaultWishlistForNewUser($array['userid']);
					$this->User_Model->record_passwords($array);
					$response = $this->Secure_Model->checklogindetails('',$email,$password);
					//print_r($response);die();
					if($response){
						// $this->doLogin = false;
						// $check_user = $this->checkuser($response);
						$send_email = $this->sendEmailAfterRegistration($email);
						if($send_email['status'] == 1){
							if($type == "seller"){
								// $response = $this->Secure_Model->checklogindetails('',$email,sha1($hash));
								$this->doLogin = true;
								$check_user = $this->checkuser($response);
								redirect(base_url('seller'));
							}else{
								$this->session->set_flashdata('alert','Please check your mail for email verification');
								if(isset($_POST['cw']) && $_POST['cw'] =="1"){
									redirect(base_url('login?cw=1'));
								}else{
									if(isset($_POST['w']) && $_POST['w'] =="1"){
										redirect(base_url('checkout'));
									}else{
										redirect(base_url('login'));
									}
								}
							}
						}else{
							$this->session->set_flashdata('alert',$send_email['msg']);
							if(isset($_POST['cw']) && $_POST['cw'] =="1"){
								redirect(base_url('join_us?cw=1'));
							}else{
								if($type == "seller"){
									redirect(base_url('join_us?type=seller'));
								}else{
									redirect(base_url('join_us'));
								}
							}
						}
						$email_verification = $this->Secure_Model->check_email_verification($_POST);
						//print_r($email_verification);die();
					}
					//$this->session->set_flashdata('registered',1);
					//redirect('Home/join_us');
				} else {
					$this->session->set_flashdata('confirm_password',1);
					$this->data['page_name'] 	= 'join_us';
					$this->data['title'] = "Register";
					$this->data['hasStyle'] 	= NULL;
					$this->data['hasScript'] 	= TRUE;
					$this->data['newsletter'] = false;
					$this->load->view('front/template',$this->data);
				}
			} else {
				//die("here 2");
				$this->session->set_flashdata('email_exist',1);
				redirect('join_us');
			}
		}
	}
	public function checkEmailExist($user_name = ""){
		$flag = true;
		if($user_name == ""){
			$user_name = $_GET['email'];
			$flag= false;
		}
		$exist = $this->User_Model->getUserByEmail($user_name);
		if($flag){
			if($exist){
				return true;
			}else{
				return false;
			}
		}else{
			if($exist){
				echo "false";
			}else{
				echo "true";
			}
		}
	}

	public function login()
	{
		if(!$this->isloggedin){
			$this->session->set_flashdata('error', 'errors');
			$this->form_validation->set_rules('user_email','Email','trim|required|valid_email|callback_checkEmail['.$this->input->post('user_pass').']');
			$this->load->helper(array('form','url'));
			$this->load->library('form_validation');
			$this->form_validation->set_rules('user_pass','Password','trim|required');
			$this->form_validation->set_error_delimiters('<strong class="invalid_error text-danger">', '</strong>');
			if($this->form_validation->run() == TRUE){
				$email = $this->input->post('user_email');
				$passw = $this->input->post('user_pass');
				$wish  = $this->input->post('from');
				$firstSalt = explode("@",$email);
				$lastSalt = "zab.ee";
				$hash = $firstSalt[0].$passw.$lastSalt;
				$response = $this->Secure_Model->checklogindetails('',$email,sha1($hash));
				$this->doLogin = true;
				$check_user = $this->checkuser($response);
				$user_id = (isset($response[0]['userid']))?$response[0]['userid']:"";
				$affiliated = $this->Affiliate_Model->check_affiliated($user_id);
				if($affiliated['status'] == "1"){
					$response[0]['affiliated_id'] = $affiliated['affiliated_id'];
					$_SESSION['affiliated_id'] = $affiliated['affiliated_id'];
				}
				$this->cartExists($user_id);
				if($check_user){
					if($response == ""){
						$this->session->set_flashdata("alert","This User is BLOCKED by Admin, Please Contact Support.");
						redirect(base_url("login"));
					}else if(isset($_POST['fromGuest'])){
						redirect("checkout");
					}else if($_POST['redirectUrl'] != ""){
						$wish = ($wish != "")?"?from".$wish:"";
						redirect($_POST['redirectUrl'].$wish);
					}else {
						redirect(base_url());
					}
				}
				$data = $this->input->post();
			}
			if(isset($_POST['fromGuest'])){
				$this->data['page_name'] = 'checkout_not_loggedIn';
				$this->data['title'] = 'Not loggedIn';
			}else{
				$this->data['page_name'] = 'login';
				$this->data['title'] = "Login";
				$this->data['hasScript'] = true;
			}
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);
		}else{
			redirect(base_url());
		}
	}
	public function checkEmail($email, $password){
		$response = $this->User_Model->getUserByEmail($email);
		if($response){
			$firstSalt = explode("@",$email);
			$lastSalt = "zab.ee";
			$hash = $firstSalt[0].$password.$lastSalt;
			if(sha1($hash) == $response[0]['password']){
				return TRUE;
			} else {
				$msg = 'Invalid email or password.';
				if($password == ""){
					$msg = "";
				}
				$this->form_validation->set_message('checkEmail', $msg);
				return FALSE;
			}
		} else {
			$this->form_validation->set_message('checkEmail', 'Email not found ');
			return FALSE;
		}
	}

	public function forgotpassword()
	{
		if(!$this->isloggedin){
			$this->data['page_name'] = 'forgotpassword';
			$this->data['title'] = 'Forgot Password';
			$this->data['hasScript'] = TRUE;
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);
		}else{
			redirect(base_url());
		}
	}
	public function checkout_not_loggedIn(){
		if(!$this->isloggedin){
			$this->data['page_name'] = 'checkout_not_loggedIn';
			$this->data['hasScript'] = TRUE;
			$this->data['title'] = 'Not loggedIn';
			$this->data['newsletter'] = false;

			$this->cart_discount($this->cart->contents());



			$this->load->view('front/template', $this->data);
		}else{
			$this->cart_discount($this->cart->contents());
			redirect(base_url('checkout'));
		}
   }
	public function paymentDetails(){
		if($this->isloggedin){
			$this->data['page_name'] = 'paymentDetails';
			$this->data['title'] = 'Payment Details';
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);

		}

	}
	public function confirm_payment(){
		if($this->isloggedin){
			$this->data['page_name'] = 'confirm_payment';
			$this->data['title'] = 'Payment Preview';
			//$this->data['address_book'] = $location;
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);

		}

	}
	public function done()
	{
		if(!$this->isloggedin){
			$this->data['page_name'] = 'done';
			$this->data['title'] = 'done';
			$this->data['newsletter'] = false;
			$this->load->view('front/template', $this->data);

		 }
	}
	public function password(){
		$this->form_validation->set_rules('email','Email','trim|required|valid_email|callback_checkEmailExist['.$this->input->post('email').']');
		$email_data = array();
		if($this->form_validation->run() == TRUE){
			//$this->load->library('encrypt');
			$this->load->helper('url');
			$email= $_POST['email'];
			$reset_code= uniqid();
			$code=($email.'|'.$reset_code);
			$encrypted_code=base64_encode($code);
			$encryption=str_replace('/', '%',$encrypted_code);
			$this->load->helper('email');
			$data = $_POST;
			if(!valid_email($data["email"])){
				$this->session->set_flashdata("alert",json_encode(array("type"=>"block","msg"=>"The Email addresss you provided is not valid.")));
				redirect($data['redirect']);
			}
			$user = $this->User_Model->getUserByEmail($data["email"]);
			$user = $user[0];
			$this->load->library('parser');
			$email_data['id'] = $reset_code;
			$email_data['encryption'] = $encryption;
			$email_data['base_path'] = base_url();
			$email_data['firstname'] = $user['firstname'];
			$email_data['lastname'] = $user['lastname'];
			if(isset($user)){
				$from = $this->config->item('info_email');
				$name = 'Zab.ee';
				$to = $data["email"];
				$subject = 'Zabee : Your request for password';
				$email_data['page_name'] = "email_template_for_password_reset";
				$message = $this->parser->parse('front/emails/email_template', $email_data, TRUE);
				if($this->Utilz_Model->email_config($from, $name, $to, $subject, $message)){
					$today = date("Y-m-d");
					$this->User_Model->saveData(array("email"=>$email,"reset_code"=>$reset_code,"datetime"=>$today),"tbl_reset_code");
					// $this->email->message($message);
					// $this->email->send();
					$this->session->set_flashdata('alert','Please check your mail');
					$this->session->set_flashdata('encryption',$encryption);
					$this->data['email'] = $email;
					$this->data['encryption']=$encryption;
					$this->data['page_name'] = 'forgotpassword';
					$this->data['title'] = 'Forgot Password';
					$this->data['hasStyle'] 	= NULL;
					$this->data['newsletter'] = false;
					$this->data['hasScript'] 	= TRUE;
					$this->load->view('front/template', $this->data);
				}
			}
			else{
				$this->session->set_flashdata('alert',json_encode(array("type"=>"block","msg"=>"Your Email addresss is not registered with us. Please <a href = '".base_url()."/register'>Register</a> here.")));
				redirect($data['redirect']);
			}
		}
		else{
			$this->session->set_flashdata('invalid_email',1);
			redirect('forgotpassword');
		}
	}
	public function logout()
	{
		$this->session->sess_destroy();
		delete_cookie("ecomm_userData");
		delete_cookie("ecomm_adminData");
		// if(!isset($_COOKIE['zabee_rememberMe'])){
		// 	delete_cookie("zabee_rememberMe");
		// }
		delete_cookie("zabee_rememberMe");
		delete_cookie("zabee_SignedIn");
		$array_items = array('social_id' => '');
		$this->session->unset_userdata($array_items);
		redirect(base_url(),"refresh");
		/*if(isset($_GET['redirectlink']))
			{
				redirect($_GET['redirectlink'],"refresh");
			}
			else
			{
				redirect(base_url(),"refresh");
			}*/
	}
	public function google_account_exist(){
		$return['status'] = 0;
		$_POST['platform'] = 'google';
		$data = json_decode($this->input->post('data'));
		$isExist = $this->User_Model->getUserByID($data->El, true, $data->email);
		if(!empty($isExist) ){
			if($isExist->is_active == "1" && $isExist->email_verified == "1"){
				if($isExist->social_id == ''){
					$data_user = array('social_id'=>$data->El, 'social_platform'=>'google');
					$table = 'tbl_users';
					$where = array('userid'=>$isExist->userid);
					$this->User_Model->updateData($data_user, $table, $where);
				}
				$this->doLogin=TRUE;
				$check_user = $this->checkuser();
				$this->cartExists($isExist->userid);
				if($check_user){
					$return['session'] = $this->session->userdata('userid');
					$return['status']=1;
				}
			} else {
				$return=array(
    				'status' => 3,
    				'EL' => $data->El,
    				'session'=>$this->session->userdata('userid')
				);
			}
		} else {
			$user_id = uniqid();
			$array = array('userid'=>$user_id,"user_type"=>2, 'firstname'=>$data->firstname, 'lastname'=>$data->lastname, 'email'=>$data->email,'social_id'=>$data->social_id,'social_platform'=>'google','social_info'=>json_encode($data->social_info),'is_active'=>'1');
			$result = $this->User_Model->addNewUser($array, 1);
			if($result){
				$return=array(
    				'status' => 2,
    				'EL' => $data->El,
    				'session'=>$user_id
				);
				$this->cartExists($user_id);
				//$this->session->set_flashdata('alert','Please check your mail for email verification');
			}
		}
		echo json_encode($return);
	}
	public function fb_account_exist(){
		$return['status'] = 0;
		$_POST['platform'] = 'fb';
		extract($this->input->post());
		$data = json_decode($data);
		$isExist = $this->User_Model->getUserByID($data->id,true, $data->email);
		if(!empty($isExist)){
			$check_user = $this->checkuser();
			$this->cartExists($isExist->userid);
			if($check_user){
				$return['status']=1;
			}
		}else{
			$array = array('userid'=>uniqid(),"user_type"=>2, 'firstname'=>$data->first_name, 'lastname'=>$data->last_name, 'email'=>$data->email,'social_id'=>$data->id,'social_platform'=>'fb','social_info'=>json_encode($data),'is_active'=>'1');
			$result = $this->User_Model->addNewUser($array);
			$check_user = $this->checkuser();
			if($result){
				$return['status']=1;
			}
		}
		echo json_encode($return);
	}
	public function user_exists(){
		extract($this->input->post());
		$data = json_decode($data);
		$isExist = $this->User_Model->getUserByID($data->id,true);
		if(!empty($isExist)){
			$check_user = $this->checkuser();
			if($check_user){
				$return['status']=1;
			}
		}
		else
		{
			$return['status']=0;
		}
		echo json_encode($return);
	}
	public function reset($uri){
		$encrypted_code=$uri;
		$decryption=str_replace('%', '/', $encrypted_code);
		$decrypted_code=base64_decode($decryption);
        $encryption=explode("|",$decrypted_code);
		$email=$encryption[0];
		$reset_code=$encryption[1];
		$return = $this->Utilz_Model->getAllData("tbl_reset_code","datetime,is_used",array("email"=>$email,"reset_code"=>$reset_code));
		if(isset($return[0]->datetime) && $return[0]->is_used =="0"){
			$reset_date = strtotime($return[0]->datetime);
			$today = strtotime($return[0]->datetime);
			if($today <= $reset_date){
				$this->data['email']=$email;
				$this->data['encrypted_code']=$encrypted_code;
				$this->data['page_name'] = 'reset_view';
				$this->data['title'] = 'Forgot Password';
				$this->data['newsletter'] = false;
				$this->data['hasStyle'] 	= NULL;
				$this->data['hasScript'] 	= TRUE;
				$this->load->view('front/template', $this->data);
			}else{
				echo 'Your code is expired <a href="'.base_url("forgotpassword").'">Click here</a>';
			}
		}else{
			echo 'Your code is expired <a href="'.base_url("forgotpassword").'">Click here</a>';
		}
	}
	public function check_code(){
		$encryption=$_POST['encryption'];
		$decryption=str_replace('%', '/', $encryption);
		$decrypted_code=base64_decode($decryption);
        $encryption_code=explode("|",$decrypted_code);
		$id=$encryption_code[1];
		if($_POST['code']==$id){
			redirect(base_url()."reset/$encryption");
		}
		else{
			$this->session->set_flashdata('invalid_code',1);
			$this->data['encryption']=$_POST['encryption'];
			$this->data['page_name'] 	= 'forgotpassword';
			$this->data['hasStyle'] 	= NULL;
			$this->data['newsletter'] = false;
			$this->data['hasScript'] 	= TRUE;
			$this->data['title'] 	= 'Reset Password';
			$this->load->view('front/template', $this->data);
		}
	}
	public function reset_processing()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		// $this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('reset_password','password','trim|required');
		$this->form_validation->set_rules('confirm_password','confirm_password','trim|required|matches[reset_password]');

		if($this->form_validation->run() == TRUE){
			$password=$this->input->post('reset_password');
			$confirm_password=$this->input->post('confirm_password');
			$email= $this->input->post('email');
			$firstSalt = explode("@",$email);
			$lastSalt = "zab.ee";
			$hash = $firstSalt[0].$password.$lastSalt;
			$password = sha1($hash);
			$data = array('password' => $password);
			$updated = $this->User_Model->update_password($email,$data);
			if($updated){
				$this->User_Model->updateData(array("is_used"=>"1"),"tbl_reset_code",array("email"=>$email));
				$this->session->set_flashdata('passwordChanged',1);
				$changed = $this->User_Model->record_passwords($data, $email);
				redirect(base_url());
			}
			else{
				$this->session->set_flashdata('previous_password',1);
				$encryption=$this->input->post('encrypted_code');
				redirect('reset/'.$encryption);
			}
		}
		else{
				$this->session->set_flashdata('password_notsame',1);
				$this->load->library('user_agent');
				redirect($this->agent->referrer());
		}
	}
	public function add_password()
	{
		$this->data['el']=$this->uri->segment(3);
		$this->data['page_name'] 	= 'home';
		$this->data['hasStyle'] 	= NULL;
		$this->data['hasScript'] 	= NULL;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Shopping";
		$this->session->set_flashdata('el',$this->uri->segment(3));
		$this->session->set_flashdata('add_password',1);
		$this->load->view('front/template', $this->data);
	}
	public function contact_us(){
		$parentCategory = $this->Product_Model->forntCategoryData("parent_category_id = '0' AND display_status = '1' AND is_active='1'");
		$this->data['parentCategory'] = $parentCategory['result'];
		$this->data['page_name'] 	= 'contact_us';
		$this->data['hasStyle'] 	= false;
		$this->data['hasScript'] 	= true;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Contact";
		$this->load->view('front/template', $this->data);

	}
	public function contact_process(){
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('subject','subject','trim|required');
		$this->form_validation->set_rules('message','message','trim|required');
		$checkCaptcha = false;
		if(isset($_POST['g-recaptcha-response'])){
			$post_data = http_build_query(
				array(
					'secret' => $this->config->item("recaptcha_secret"),
					'response' => $_POST['g-recaptcha-response'],
					'remoteip' => $_SERVER['REMOTE_ADDR']
				)
			);
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $post_data
				)
			);
			$context  = stream_context_create($opts);
			$response = file_get_contents($this->config->item('recaptcha_verify_url'), false, $context);
			$result = json_decode($response);
			$checkCaptcha= ($result->success)?true:false;
		}
		if($checkCaptcha){
			if($this->form_validation->run() == TRUE){
				$this->load->helper('url');
				$email= $_POST['email'];
				$this->load->helper('email');
				$data = $_POST;
				if(!valid_email($data["email"])){
					$this->session->set_flashdata("alert",json_encode(array("type"=>"block","msg"=>"The Email addresss you provided is not valid.")));
					redirect($data['redirect']);
				}
				$addData = $this->Utilz_Model->contact_usData($data);
				if($addData){
					$this->load->library('parser');
					$i = 0;
					$from = $this->config->item('info_email');
					$name= $this->config->item("author");
					while($i < 2){
						if($i == 0){
							$to = $this->config->item('info_email');
							$showSubject = "Zabee : Contact Us - ".$_POST['subject'];
							$showMessage = $_POST['message'];
						} else {
							$to = $data["email"];
							$showSubject = "Zabee : Email recieved - ".$_POST['subject'];
							$showMessage = "Your support email for the order number you mentioned has been recieved.";
						}
						$showOrdNo = $_POST['order_number'];
						$this->data['text'] = $showMessage;
						$this->data['order'] = $showOrdNo;
						$this->data['page_name'] = "email_template_for_contact_us";
						$message = $this->parser->parse('front/emails/email_template', $this->data, TRUE);
						$this->Utilz_Model->email_config($from, $name, $to, $showSubject, $message);
						$i++;
					}

					$this->session->set_flashdata('message_sent',1);
					redirect(base_url('contact_us'));
				} else {
					redirect(base_url('contact_us'));
				}
			} else {
				$this->session->set_flashdata('invalid_email',1);
				redirect(base_url('contact_us'));
			}
		} else {
			$this->session->set_flashdata("alert",json_encode(array("type"=>"block","msg"=>"CAPTCHA verification failed.")));
			redirect(base_url('contact_us'));
		}
	}
	public function set_password($el = '', $page=''){
		$this->data['product']		= "";
		$el=$_POST['el'];
		$page=$_POST['page'];
		$this->data['el']=$el;
		$this->data['page_name'] 	= 'home';
		$this->data['hasStyle'] 	= NULL;
		$this->data['hasScript'] 	= NULL;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Set Password";
		$this->data['add_password']	= 1;
		$this->data['error']		= '';
		$this->form_validation->set_rules('el','Google ID','trim|required');
		$this->form_validation->set_rules('password','password','trim|required');
		$this->form_validation->set_rules('confirm_password','confirm_password','trim|required|matches[password]');
		if($this->form_validation->run() == TRUE){
		$password=$this->input->post('password');
			$isExist = $this->User_Model->getUserBySocialid($el);
			if(!empty($isExist)){
					$email = $this->User_Model->getUserEmailBySocialid($el);
					$userid = $this->User_Model->getUserIdBySocialid($el);
					$firstSalt = explode("@",$email[0]->email);
					$lastSalt = "zab.ee";
					$password = $firstSalt[0].$password.$lastSalt;
					$password = sha1($password);
					$data=array('password'=>$password,'is_active'=>'1');
					$data_for_rec=array('password'=>$password,'is_active'=>'1', 'thirdParty'=>'1', 'userid' =>$userid[0]->userid);
					$set_password=$this->User_Model->set_password($el,$data);
					if($set_password){
						$rec_pass = $this->User_Model->record_passwords($data_for_rec, $email[0]->email);
						if($page == "checkout"){
							redirect(base_url("checkout"),"refresh");
						} else{
							redirect(base_url(),"refresh");
						}
						/*if($rec_pass){
							//$this->session->set_flashdata('alert','Please check your mail for email verification');
							$check_user = $this->checkuser();
							//print_r($check_user);die();
							if($check_user){
								$return['session'] = $this->session->userdata('userid');
								$return['status']=1;
							}
							print_r($_SESSION);die();
							redirect(base_url(),"refresh");
						} else {
							$this->session->set_flashdata('alert','Error occured, try again.');
						}*/
					} else {
						$this->data['error'] = 'User not found';
					}
					/*$check_user = $this->checkuser();
					if($check_user){
						redirect(base_url(),"refresh");
					}*/
		    } else {
				$this->data['error'] = 'Error in setting password';
			}
 		} elseif($this->form_validation->run() == FALSE) {
 			$this->data['not_same'] = true;
		}
 		$this->load->view('front/template', $this->data);
	}
	public function set_password_view(){
		$this->data['el']=$_POST['el'];
		$this->data['page']=$_POST['page'];
		$this->data['page_name'] 	= 'set_password';
		$this->data['hasStyle'] 	= NULL;
		$this->data['hasScript'] 	= NULL;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Password";
		$this->load->view('front/set_password', $this->data);
	}

	public function thankyou()
	{
		$this->data["page_name"]="order_complete";
		$this->data["hasStyle"]=TRUE;
		$this->data['newsletter'] = false;
		$this->load->view('front/template',$this->data);
	}
	public function saveEmailforNewsandOffers(){
		$this->load->library('form_validation');
		$config = array(
		array(
			'field' => 'newsemail',
			'label' => 'Email',
			'rules' => 'trim|required|callback_check_for_email'   //here we have added the callback which we have created by appending callback_ to its method name
			)

		);
		$this->form_validation->set_rules($config);
		$post_data = $this->input->post('newsemail');
		if($this->form_validation->run() == TRUE){
			if($this->User_Model->validate_email($post_data)){
				$resp = $this->User_Model->newsandoffer_add($post_data);
				if($resp){
					$this->session->set_flashdata('newsletter_subscribe','You have successfully subcribed the newsletter');
					$this->session->set_flashdata('check_for_email','');
					$subject = 'Newsletter'; // replace it with relevant subject
					$body = $this->load->view('front/emails/email_template_for_newsletter','', TRUE);
					$this->Utilz_Model->email_config($this->config->item('info_email'), $this->config->item('Author'), $post_data, $subject, $body);
					redirect(base_url('home?status=success#newsletter'));
				}
			}else{
				$this->session->set_flashdata('check_for_email','Please enter correct email');
				redirect(base_url('home').'#newsletter');
			}
		}
		redirect(base_url('home').'#newsletter');
	}
	public function about_us(){
		$this->load->view('front/about_us', $this->data);
	}
	public function check_for_email($email){
		$this->session->set_flashdata('check_for_email',' E-mail already exists. Please try some new e-mail to subscribe for our newsletter.');
		if($this->User_Model->check_for_email($email)){
			return true;
		}

		else{

			return false;
		}
	}
	/*public function seller_info_test($seller_id, $decode = true){
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->data['hasScript'] = true;
		$this->data['hasStyle'] = false;
		$this->data['newsletter'] = true;
		$this->load->model('Utilz_Model');
		$this->load->model('Reviewmodel');
		$sellerData = array();
		$querySelect="";
		$user_id= "";
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
			$querySelect = ", w.wish_id as already_saved";
		}
		if($seller_id !=""){
			//$id = explode('-',$id);
			//$id = end($id);
			//$index = str_replace(' ','_',strtolower(base64_decode(urldecode($id))));
			//$id = explode('_',base64_decode(urldecode($id)));
			//echo base64_encode(urlencode('59670db356327'));
			if($decode){
				$seller_id = base64_decode(urldecode($seller_id));
			}
			$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
			//Seller's Info
			$seller_info = $this->Product_Model->getData(DBPREFIX.'_seller_store ss', 'ss.s_id AS store_id, ss.contact_person, ss.store_name, ss.store_address, ss.contact_phone, ss.store_logo, ss.cover_image', array('ss.seller_id'=>$seller_id));
			$this->data['title'] = $seller_info[0]->store_name;
			if($seller_info){
				//Seller's All Product
				$select = 'product.product_name,product.slug,product.product_id,pm.thumbnail as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,AVG(preview.`rating`) AS rating,pin.price,pin.sell_quantity, sp.seller_id'.$querySelect;
				$where = array('sp.seller_id'=>$seller_id);
				$allProducts = $this->Product_Model->frontProductDetails('','','','','','','sp.created_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
				//print_r($allProducts);die();
				//Seller's Popular items
				$where = array('sp.seller_id'=>$seller_id,'preview.rating >'=>3);
				$popularItems = $this->Product_Model->frontProductDetails('','','','','','','preview.rating DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
			  	//echo "<pre>";print_r($allProducts);die();
				//echo "<pre>";print_r($popularItems);die();
				$this->data['wishlist_categories']	= $this->Secure_Model->WishlistViaCategories($user_id);
				$sellerData['allProducts'] = $allProducts;
				$sellerData['popularItems'] = $popularItems;
				$sellerData['seller_info'] = $seller_info;
				$this->data['sellerData'] = $sellerData;
				$this->data['page_name'] = 'seller_profile';
				$this->data['reviews'] = $this->Reviewmodel->getReviewsbySeller($seller_id,"",3);
				$this->load->view('front/template', $this->data);

			}else{
				$this->data['hasScript'] = false;
				$this->data['heading'] = "Error 404!";
				$this->data['message'] = "Product Not Found";
				$this->data['page_name'] = 'error_404';
				$this->load->view('front/template', $this->data);
			}
		}*/
	public function seller_info($seller_id, $decode = true){
		unset($_SESSION['view']);
		$this->load->library('pagination');
		$this->load->model('Reviewmodel');
		$_SESSION['view'] = (isset($_GET['view']))?$_GET['view']:((isset($_SESSION['view']))?$_SESSION['view']:'grid');
		$product_name = (isset($_GET["search"]))? $_GET["search"]:"";
		$ship = (isset($_POST["ship"])) ? $_POST["ship"]:"";
		$where="";
		$order="";
		$link = "";
		$prod_search = "";
		$config = array();

		$range = "";
		$cat_id = "";
		$brand_id = "";
		$category_id = "";
		$param = "";
		$user_id=$this->session->userdata('userid');
		if($seller_id !=""){
			if($decode){
				$seller_id = base64_decode(urldecode($seller_id));
			}
			$seller_info = $this->Product_Model->getData(DBPREFIX.'_seller_store ss', 'ss.s_id AS store_id,ss.store_id as store_slug,ss.seller_id, ss.contact_person, ss.store_name, ss.store_address, ss.contact_phone, ss.store_logo, ss.cover_image', array('ss.seller_id'=>$seller_id));
			if($seller_info){
				if(isset($_GET['fs']) && $_GET['fs'] != ""){
					$ship = $_GET['fs'];
				}
				if(isset($_GET['price_range']) && $_GET['price_range']!="" ){
					$range= explode("-",$_GET['price_range']);
					$where = "pin.price BETWEEN '$range[0]' AND '$range[1]'";
					if(!is_numeric($range[0]) || !is_numeric($range[1])){
						redirect(base_url('product'));
					}
					if($param == ""){
						$param .='?price_range='.$_GET['price_range'];
					}else{
						$param .='&price_range='.$_GET['price_range'];
					}
				}
				if(isset($_GET['product_name'])){
					$order="product_name ".$_GET['product_name'];
					if($param == ""){
						$param .='?product_name='.$_GET['product_name'];
					}else{
						$param .='&product_name='.$_GET['product_name'];
					}
				}
				if(isset($_GET['min_price'])){
					$order="price ".$_GET['min_price'];
					if($param == ""){
						$param .='?min_price='.$_GET['min_price'];
					}else{
						$param .='&min_price='.$_GET['min_price'];
					}
				}
				if($this->input->get('sort')){
					$value = explode('-',$this->input->get('sort'));
					if($value[0] == "price"){
						$order="price ".$value[1];
					}else{
						$order="product_name ".$value[1];
					}
					if($param == ""){
						$param .='?sort='.$this->input->get('sort');
					}else{
						$param .='&sort='.$this->input->get('sort');
					}
				}
				if(isset($_GET["category_search"]) && $_GET["category_search"]!=""){
					$category_id = trim($_GET["category_search"]);
					$link .="&category_search=".$category_id;
					if($cat_id == ""){
						$cat_id = $category_id;
					}
				}
				if(isset($_GET["brands_search"] ) && $_GET["brands_search"]!="" ){
					$brand_search = trim( $_GET["brands_search"]);
					$link .="&brands_search=".$brand_search;
					if($_GET["brands_search"] == "All")
					$_GET["brands_search"] = "";
					$brand_id = $brand_search;
				}
				$url=strtok($_SERVER["REQUEST_URI"],'?');
				$link=$_SERVER['REQUEST_URI'];
				if(isset($_GET['page']) && isset($_GET['page'])){
					if(strpos($_SERVER['REQUEST_URI'],'?page='.$_GET['page'])){
						$link = str_replace('?page='.$_GET['page'], '', $_SERVER['REQUEST_URI']);
					}else{
						$link = str_replace('&page='.$_GET['page'], '', $_SERVER['REQUEST_URI']);
					}
				}
				if(isset($cat_id) && $cat_id!=""){
					$where .= "cat.category_id IN(".$cat_id.") AND ";
					if($param == ""){
						$param .='?category_search='.$category_id;
					}
					else{
						$param .= '&category_search='.$category_id;
					}
				}
				if(isset($brand_id) && $brand_id!=""){
					$where.=" b.brand_id='$brand_id'";
					if($where!=""){
						$where.=" AND ";
					}
					if($param == ""){
						$param .='?brands_search='.$brand_id;
					}else{
						$param .='&brands_search='.$brand_id;
					}
				}
				if($ship!=""){
					if($ship == "free"){
						$ship = 0;
					}
					if($param == ""){
						$param .='?fs='.$ship;
					}else{
						$param .='&fs='.$ship;
					}
				}
				$config["base_url"] = base_url('store/'.$seller_info[0]->store_slug.$param);
				$count=$this->Utilz_Model->getSellerProductCount($seller_id,$brand_id,$cat_id,$ship,$range);
				$config["total_rows"] 		 = $count;
				$config["per_page"] 		 = 12;
				$config['use_page_numbers']  = TRUE;
				$config['num_links'] 		 = 1;
				$config['cur_tag_open'] 	 = '<li class="active text-center"><a class="page-link current">';
				$config['cur_tag_close'] 	 = '</a></li>, ';
				$config['next_link'] 		 = '<span class=""><i class="fa fa-angle-right" style="font-size:20px"></i></span>';
				$config['prev_link'] 		 = '<span class=""><i class="fa fa-angle-left" style="font-size:20px"></i></span>';
				$config['num_tag_open'] 	 = '<li class="text-center">';
				$config['num_tag_close'] 	 = '</li>';
				$config['first_link'] 		 ='First';
				$last_page_number 			 = ceil($config["total_rows"]/$config["per_page"]);
				$config['last_link']  		 = $last_page_number;
				$config['prev_tag_open'] 	 = '<li class="pg-tag text-center">';
				$config['prev_tag_close'] 	 = '</li>';
				$config['next_tag_open'] 	 = '<li class="pg-tag-next text-center">';
				$config['next_tag_close'] 	 = '</li>';
				$config['first_tag_open'] 	 = '<li class="text-center">';
				$config['first_tag_close'] 	 = '</li>';
				$config['last_tag_open'] 	 = '<li class="text-center">';
				$config['last_tag_close'] 	 = '</li>';
				$config['page_query_string'] = TRUE;
				$this->pagination->initialize($config);
				if($this->input->get('page')){
					$page = $this->input->get('page');
				}else{
					$page = 1;
				}
				if($page > $last_page_number){
					$page = $last_page_number;
				}
				$productData= $this->Utilz_Model->getSellerProduct($seller_id,$brand_id,$cat_id,$page,$config["per_page"],$order,$range,"","","",$user_id);
				// echo"<pre>"; print_r($productData); die();
				$brand_and_categories = $this->Utilz_Model->getBrandAndCategory($seller_id);
				$i=0;
				$this->data['title'] = $seller_info[0]->store_name;
				$this->data['brandData'] 			= $brand_and_categories['brands'];
				$this->data['category'] 			= $brand_and_categories['categories'];
				$this->data['productData'] 			= $productData;
				$str_links 							= $this->pagination->create_links();
				$links["links"] 					= explode(', ', $str_links);
				$this->data['links'] 				= $links;
				$this->data['link'] 				= $link;
				$this->data['max']					= $this->Product_Model->get_max_price();
				$this->data['wishlist_categories'] 	= $this->Secure_Model->WishlistViaCategories($user_id);
				$this->data['wishlist_check'] 		= "";//$this->Secure_Model->ClearWishlistIfNoCategories($user_id);
				$this->data['page_name'] 			= 'seller_profile';
				$this->data['hasScript']			= TRUE;
				$this->data['hasStyle']				= TRUE;
				$this->data['showSorting'] 			= true;
				$this->data['newsletter'] 			= true;
				$this->data['reviews'] = $this->Reviewmodel->getReviewsbySeller($seller_id,"",3);
				$sellerData['seller_info'] = $seller_info;
				$this->data['sellerData'] = $seller_info;
				$this->load->view('front/template', $this->data);
			}
		}else{
			$this->data['hasScript'] = false;
			$this->data['heading'] = "Error 404!";
			$this->data['message'] = "Product Not Found";
			$this->data['page_name'] = 'error_404';
			$this->load->view('front/template', $this->data);
			exit();
		}
	}
	public function store($store_name){
		$seller_id = $this->Utilz_Model->getStoreId($store_name);
		if($seller_id){
			$this->seller_info($seller_id, false);
		}else{
			$this->data['hasScript'] = false;
			$this->data['newsletter'] = true;
			$this->data['heading'] = "Error 404!";
			$this->data['message'] = "Store Not Found";
			$this->data['page_name'] = 'error_404';
			$this->load->view('front/template', $this->data);
		}
	}

	public function checkout_as_guest(){
		if(!$this->isloggedin && $this->cart->contents()){
			/*$this->load->model('Utilz_Model');
			$this->data['countryList'] = $this->Utilz_Model->countries();
			$this->data['statesList'] = $this->Utilz_Model->countries("","","",false,"tbl_states");
			$this->data['page_name'] = 'checkout_as_guest';
			$this->data['title'] = 'Not loggedIn';
			$this->data['newsletter'] = false;
			$this->data['hasScript'] = true;
			$this->load->view('front/template', $this->data);*/
			$this->data['page_name']		= 'addressbook_add';
			$this->data['hasScript'] 		= true;
			$this->data['showSidepanel']	= true;
			$this->data['title'] 			= "Add Address";
			$this->data['active_page'] 		= 'address_book_add';
			$this->data['newsletter']	= false;
			$this->load->model("Addressbook_Model", 'AddressBook');
			$this->data['checkerForCheckout']	= 3;
			$this->data['location_id'] = "";
			$this->load->model('Utilz_Model');
			$this->data['countryList'] = $this->Utilz_Model->countries();
			$this->data['statesList'] = $this->Utilz_Model->countries("","","",false,"tbl_states");
			//echo '<pre>';print_r($this->data['countryList'] );echo '</pre>';die();
			$this->load->view('front/template', $this->data);
		}else{
			redirect(base_url('checkout/signin?status=error&msg=Cart is empty'));
		}
   }

	public function guest_registration(){
		$data = $this->input->post();
		$this->session->set_userdata('guest_register', '0');
		//echo "<pre>";print_r($data);//die();
		$data['state'] = (isset($data['province']) && $data['province'] !="" && $data['country'] !=226)?$data['province']:$data['state'];
		$insert_data = array(
			'user_type' => 'buyer',
			'fullname' => $data['first_name']." ".$data['last_name'],
			'contact' => $data['contact'],
			'address_1' => $data['address1'],
			'address_2' => $data['address2'],
			'country' => $data['country'],
			'state' => $data['state'],
			'city' => $data['city'],
			'zip' => $data['zip'],
			'active' => "1",
		);
		//print_r($insert_data);die();
		$isEmailExist = $this->User_Model->getUserByEmail($data['email']);
		$insert_data['created'] = date('Y-m-d H:i:s');
		if($isEmailExist){
			$user_id = $isEmailExist[0]['userid'];
			$insert_data['user_id'] = $user_id;
			$this->load->model("Addressbook_Model", 'AddressBook');
			$resp = $this->AddressBook->add_addressbook($insert_data, $user_id);
			unset($insert_data['zip']);
			unset($insert_data['created']);
			unset($insert_data['fullname']);
			unset($insert_data['contact']);
			$insert_data['zipcode'] = $data['zip'];
			$insert_data['name'] = $data['first_name']." ".$data['last_name'];
			$insert_data['phone'] = $data['contact'];
			$this->session->set_userdata('checkout_ship', $insert_data);
			if($resp){
				$this->session->set_userdata('guest_id',$user_id);
				$this->session->set_userdata('is_guest',1);
				$this->session->set_userdata('email',$data['email']);
			}
		}else{
			$user_id = uniqid();
			$insert_data['user_id'] = $user_id;
			$array = array('userid'=>$user_id,"user_type"=>2, 'firstname'=>$data['first_name'], 'middlename'=>"", 'lastname'=>$data['last_name'], 'email'=>$data['email'], 'password'=>"",'social_id'=>"",'social_platform'=>'','social_info'=>'','mobile'=>$data['contact'],'is_active'=>'0','is_guest'=>'1');
			if(isset($data['SignIn']) && $data['SignIn'] && isset($data['password']) && $data['password'] != ""){
				$password  			= $data['password'];
				$firstSalt 			= explode("@",$data['email']);
				$lastSalt  			= "zab.ee";
				$password  			= $firstSalt[0].$password.$lastSalt;
				$password  			= sha1($password);
				$array['password'] 	= $password;
				$array['is_active']	= '1';
				$array['is_guest']	= '0';
			}
			//print_r($array);die();
			if($this->User_Model->addNewUser($array)){
				//unset($insert_data['zip']);
				$this->load->model("Addressbook_Model", 'AddressBook');
				$resp = $this->AddressBook->add_addressbook($insert_data, $user_id);
				if(isset($data['SignIn']) && $data['SignIn'] && isset($data['password']) && $data['password'] != ""){
					$response = $this->Secure_Model->checklogindetails('',$data['email'],$password);
					$this->doLogin = true;
					$check_user = $this->checkuser($response);
					$this->session->set_userdata('guest_register', '1');
					$send_email = $this->sendEmailAfterRegistration($data['email']);
				}
				unset($insert_data['zip']);
				unset($insert_data['created']);
				unset($insert_data['fullname']);
				unset($insert_data['contact']);
				$insert_data['name'] 		= $data['first_name']." ".$data['last_name'];
				$insert_data['phone'] 		= $data['contact'];
				$insert_data['zipcode'] 	= $data['zip'];
				$this->session->set_userdata('checkout_ship', $insert_data);
				if($resp){
					$this->session->set_userdata('guest_id',$user_id);
					$this->session->set_userdata('is_guest',1);
					$this->session->set_userdata('email',$data['email']);
				}
			}
		}
		redirect(base_url('checkout/payment?add_card=1'));
	}
	public function guestAddress(){
		$data = $this->input->post();
		//echo "<pre>";print_r($data);//die();
		$data['state'] = (isset($data['province']) && $data['province'] !="" && $data['country'] !=226)?$data['province']:$data['state'];
		$insert_data = array(
			'user_type' => 'buyer',
			'fullname' => $data['fullname'],
			'contact' => $data['contact'],
			'address_1' => $data['address1'],
			'address_2' => $data['address2'],
			'country' => $data['country'],
			'state' => $data['state'],
			'city' => $data['city'],
			'zip' => $data['zip'],
			'active' => "1",
		);
		//print_r($insert_data);die();
		$isEmailExist = $this->User_Model->getUserByEmail($this->session->userdata('email'));
		$insert_data['created'] = date('Y-m-d H:i:s');
		if($isEmailExist){
			$user_id = $isEmailExist[0]['userid'];
			$insert_data['user_id'] = $user_id;
			$this->load->model("Addressbook_Model", 'AddressBook');
			$resp = $this->AddressBook->add_addressbook($insert_data, $user_id);
			unset($insert_data['zip']);
			unset($insert_data['contact']);
			$insert_data['zipcode'] = $data['zip'];
			$insert_data['name'] = $data['fullname'];
			$insert_data['phone'] = $data['contact'];
		}else{
			redirect(base_url('home/guest'));
		}
		if($this->session->userdata('checkout_card_view') && isset($_GET['b']) && $_GET['b'] == 3){
			$this->session->set_userdata('checkout_ship', $insert_data);
			redirect(base_url('checkout/payment?confirm_order=1'));
		}else{
			redirect(base_url('checkout/payment?add_card=1&a='.$resp));
		}
	}
	public function privacypolicy()
	{
		$this->data['page_name'] 	= 'privacy_policy';
		$this->data['hasScript'] = false;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Zabee Privacy Policy";
		$this->load->view('front/template', $this->data);

	}
	public function termsandcondition()
	{
		$this->data['page_name'] 	= 'termsandcondition';
		$this->data['hasScript'] = false;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Zabee Terms and Condition";
		$this->load->view('front/template', $this->data);

	}
	public function sendEmailAfterRegistration($email){
		$this->load->helper('url');
		$user_name = $this->Secure_Model->getUsernameViaEmail($email);
		$id = $user_name[0]->userid;
		$code=($email.'|'.$id);
		$encrypted_code = base64_encode($code);
		$encryption = str_replace('/', '%',$encrypted_code);
		$decryption = str_replace('%', '/', $encryption);
		$decrypted_code = base64_decode($encrypted_code);
		$this->data['encryption'] = $encryption;
		$this->data['firstname'] = $user_name[0]->firstname;
		$data = $_POST;
		if(!empty($user_name) && $user_name[0]->userid !=""){
			$from = $this->config->item('info_email');
			$to = $email;
			$name = $this->config->item('author');
			$subject = 'Zabee : Activate your account';
			$this->load->library('parser');
			$data['base_path'] = base_url();
			$data['page_name'] = "email_template_for_acount_confirmation";
			$data['firstname'] = $user_name[0]->firstname;
			$data['encryption'] = $encryption;
			$message = $this->parser->parse('front/emails/email_template', $data, TRUE);
			if($this->Utilz_Model->email_config($from, $name, $to, $subject, $message)){
				$this->session->set_flashdata('encryption',$encryption);
				return array('status'=>1,'msg'=>'OK');
			}else{
				return array('status'=>0,'msg'=>$this->email->print_debugger());
			}
		}
	}

	public function verify_email($encrypted_code){
		$decryption=str_replace('%', '/', $encrypted_code);
		$decrypted_code=base64_decode($decryption);
        $encryption=explode("|",$decrypted_code);
		$email=$encryption[0];
		$id=$encryption[1];
		$verified = $this->Secure_Model->VerifyEmailByUserId($id);
		if($verified == "success"){
			$this->doLogin = true;
			$this->session->set_flashdata('alert','Your email is verified!');
			redirect(base_url('login'));
		} else if($verified == "1"){
			$this->session->set_flashdata('alert','Your email already verified please login!');
			redirect(base_url('login'));
		}else{
			$this->session->set_flashdata('alert','Error in email verification!');
			redirect(base_url('login'));
		}
	}
	public function changeAllPassword(){
		$data = $this->db->select("*")->from("tbl_users")->get()->result();
		foreach($data as $d){
			$firstSalt = explode("@",$d->email);
			$lastSalt = "zab.ee";
			$hash = $firstSalt[0]."admin".$lastSalt;
			$password = sha1($hash);
			$updateData = array("password"=>$password);
			$this->db->where("id",$d->id);
			$this->db->update("tbl_users",$updateData);
			//echo "<pre>";print_r($d);
		}
	}
	public function clearWishListCategoriesNameTable(){
		$this->db->empty_table('tbl_wishlist_categoryname');
		echo "tbl_wishlist_categoryname truncated.";
	}

	public function clearWishListTable(){
		$this->db->empty_table('tbl_wishlist');
		echo "tbl_wishlist truncated.";
	}
	public function userStatusUpdate(){
		$email = $_GET['email'];
		$this->db->where("email",$email);
		$this->db->update("tbl_users",array("email_verified"=>"0"));
		echo "<pre>";print_r($email);
	}
	public function email_template_view(){
		$this->data['page_name'] 	= 'email_template';
		$this->data['hasScript'] = false;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Email";
		$this->load->view('front/template', $this->data);
	}
	public function add_wishlist_category(){
		$post_data = $this->input->post();
		// echo"<pre>"; print_r($post_data); die();
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$resp = $this->Secure_Model->wishlist_cat_add($post_data, $user_id);
		if($resp){
			$respo['status'] = 'success';
			$data = $this->Secure_Model->getForcedWishListName($user_id);
			$respo['data'] = array("id"=>$data->id, "category_name"=>$data->category_name);//$data[$index - 1];
		}
		echo json_encode($respo, JSON_UNESCAPED_UNICODE);
	}

	//for testing purpose
	public function cartDestroy(){
		$this->cart->destroy();
		$this->saveforlater->destroy();
	}
	public function cartDelete($user_id = ""){
		$this->db->where("user_id",$user_id);
		$this->db->delete("tbl_tmpcart");
		$this->db->where("user_id",$user_id);
		$this->db->delete("tbl_tmp_save_for_later");
	}
	public function InvoiceForMail()
	{
		$this->data['page_name'] 	= 'emails/email_template_for_invoice_Copy';
		$this->data['hasScript'] = false;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Zabee Terms and Condition";
		$this->load->view('front/template', $this->data);
	}
	public function minicart(){
		$this->load->view('front/minicart');
	}
	public function updatePassword($from = 0){
		$return = array("status"=>0,"msg"=>"Error in password update!");
		$data = $this->input->post();
		$table = DBPREFIX."_users";
		$where = array("userid"=>$data['user_id'],"");
		$password = $data['password'];
		$firstSalt = explode("@",$data['email']);
		$lastSalt = "zab.ee";
		$password = $firstSalt[0].$password.$lastSalt;
		$password = sha1($password);
		$data['password'] = $password;
		$email = $data['email'];
		unset($data['user_id']);
		unset($data['email']);
		$response = $this->User_Model->updateData($data,$table,$where);
		if($response){
			$return = array("status"=>1,"msg"=>"Password updated Successfully!");
			if($from == 1){
				$response = $this->Secure_Model->checklogindetails('',$email,$password);
				$this->doLogin = true;
				$check_user = $this->checkuser($response);
			}
		}
		echo json_encode($return);
	}

	public function notexist($heading="", $message="")
	{
		$this->data['page_name'] 	= 'error_404';
		$this->data['hasScript'] = false;
		$this->data['newsletter'] = true;
		$this->data['heading'] = $heading;
		$this->load->view('front/template', $this->data);
	}

	public function order_complete()
	{
		$this->data["page_name"]="order_complete";
		if($this->session->userdata('PayMentResult')){
			$payment_info = $this->session->userdata('PayMentResult');
			$this->data['order_date'] 	= date('D M d Y H:i:s', strtotime($payment_info['TIMESTAMP']));
			$this->data['order_amount'] = $payment_info['payment'];
			$this->data['newsletter'] 	= false;
			$location 					= $this->session->userdata('checkout_ship');
			$this->data['tax'] 			= $this->getTax($location['zipcode'], $this->cart->total());
			$this->data['zip_code'] 	= $location['zipcode'];
			//-----------------------------------------//

		} else {
			redirect(base_url('orders'));
		}
		$this->session->unset_userdata('PayMentResult');
		$this->load->view('front/template',$this->data);
	}
	public function get_image(){
		header("Content-Type: image/png");
		$file = urldecode($_GET['file']);
		$path = urldecode($_GET['path']);
		$url = $path.$file;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
		$res = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($rescode == 200){
			curl_close($ch) ;
			echo $res;
		} else {
			$url = assets_url('backend/images/defaultprofile.png');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
			$res = curl_exec($ch);
			$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch) ;
			echo $res;
		}
	}
	public function grocerySalesPartner(){
		$this->data['page_name'] 	= 'grocery-sales-partner';
		$this->data['hasStyle'] 	= false;
		$this->data['hasScript'] 	= false;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Grocery Sales Partner";
		$this->load->view('front/template', $this->data);
	}
	/*function makeSlug(){
		$slug = 'HP P274 27" 16:9 IPS Monitor';
		$slug = $this->slugify($slug);
		echo $slug;
	}
	public function updateSlug(){
		$query = $this->db->select("category_id,category_name,parent_category_id")->from("tbl_categories")->get()->result();
		//echo "<pre>";print_r($query);
		foreach($query as $q){
			if($q->parent_category_id){
				$id = $this->Utilz_Model->getAllCategoriesParentId($q->parent_category_id);
				$bradcrumbs = array_reverse($id);
				$slug = "";
				foreach($bradcrumbs as $s){
					$slug .= strtolower(str_replace(" ","-",$s['cat_name']))."-";
				}
				$slug = $slug.$q->category_name;

			}else{
				$slug = $q->category_name;
			}
			$slug = $this->slugify($slug);
			echo $slug."<br>";
			$this->db->where("category_id",$q->category_id);
			$this->db->update("tbl_categories",array("slug"=>$slug));
		}
	}
	public function updateProductSlug(){
		$query = $this->db->select("product_id,product_name")->from("tbl_product")->get()->result();
		echo "<pre>";print_r($query);
		set_time_limit(300);
		foreach($query as $q){
			$slug = $this->slugify($q->product_name);
			$this->db->where("product_id",$q->product_id);
			$this->db->update("tbl_product",array("slug"=>$slug));
		}
	}
	function slugify($text){
		// replace non letter or digits by -
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, '-');

		// remove duplicate -
		$text = preg_replace('~-+~', '-', $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}*/
	public function save_subscription(){
		$this->load->library('form_validation');
		$config = array(
		array(
			'field' => 'deals_sub_name',
			'label' => 'Full name',
			'rules' => 'trim|required'
		),array(
			'field' => 'deals_sub_email',
			'label' => 'Email',
			'rules' => 'trim|required|callback_check_for_deals_email'   //here we have added the callback which we have created by appending callback_ to its method name
		),array(
			'field' => 'deals_sub_phone',
			'label' => 'Phone',
			'rules' => 'trim|required|callback_check_for_deals_phone'   //here we have added the callback which we have created by appending callback_ to its method name
		),

		);
		$this->form_validation->set_rules($config);

		$deals_selection = $this->input->post('deals_selection');
		$deal_cat_ids = $this->input->post('deal_cat_ids');
		$cat_ids = ($deals_selection == 0)?0:implode($_POST['deal_cat_ids'], ',');

		$data = array(
			'created' => date('Y-m-d H:i:s'),
			'updated' => date('Y-m-d H:i:s'),
			'first_name'=> $this->input->post('deals_sub_name'),
			'email_address'=> $this->input->post('deals_sub_email'),
			'phone_number'=> $this->input->post('deals_sub_phone'),
			'cat_ids'=> $cat_ids,
			'active'=>1
		);
		if($this->form_validation->run() == TRUE){
			if($this->User_Model->validate_email($data['email_address'])){
				$resp = $this->Utilz_Model->deals_signup($data);
				if($resp){
					$this->session->set_flashdata('deals_subscribe','You have successfully sign-up for the deals');
					$this->session->set_flashdata('deals_email','');
					$this->load->helper('cookie');
					$cookie= array(
						'name'   => 'deals_signup',
						'value'  => '1',
						'expire' => time()+3600,
					);
					$this->input->set_cookie($cookie);
					redirect(base_url('?status=success#deals_signup'));
				}
			}else{
				$this->session->set_flashdata('deals_email','<label id="deals_sub_email-error" class="error" for="deals_sub_email">Please enter correct email</label>');
				redirect(base_url().'?status=error#deals_signup');
			}
		} else {
			echo validation_errors();
			//redirect(base_url('home').'?status=error#deals_signup');
		}
	}
	public function check_for_deals_email($email){
		$this->form_validation->set_message('check_for_deals_email', 'E-mail already exists. Please try some new e-mail to sign-up for our deals.');
		if($this->Utilz_Model->check_for_deals_email($email)){
			return true;
		} else {
			$this->session->set_flashdata('deals_email','<label id="deals_sub_email-error" class="error" for="deals_sub_email">E-mail already exists. Please try new phone number to sign-up for our deals.</label>');
			return false;
		}
	}
	public function check_for_deals_phone($phone){
		$this->form_validation->set_message('check_for_deals_phone', '<label id="deals_sub_phone-error" class="error" for="deals_sub_phone">Phone number already exists. Please try some new e-mail to sign-up for our deals.</label>');
		if($this->Utilz_Model->check_for_deals_phone($phone)){
			return true;
		} else {
			$this->session->set_flashdata('deals_phone','Phone number already exists. Please try some new e-mail to sign-up for our deals.');
			return false;
		}
	}

	public function disable_subscription(){
		$response = array('status'=>1, 'message'=>'request accepted');
		$this->load->helper('cookie');
		$cookie= array(
			'name'   => 'deals_signup',
			'value'  => '1',
			'expire' => time()+3600,
		);
		//echo time()+3600;
		$this->input->set_cookie($cookie);
		echo json_encode($response);
	}
	public function makePassword(){
		$email = "zab.ee@kaygees.com";
		$firstSalt = explode("@",$email);
		$lastSalt = "zab.ee";
		$password = $firstSalt[0]."admin".$lastSalt;
		$password = sha1($password);
		echo $password;
	}
	/*
	public function makeCategoryData(){
		$query = $this->db->select("product_id,sub_category_id")->from("tbl_product")->get()->result();
		echo "<pre>";
		set_time_limit(300);
		foreach($query as $q){
			$data = array("product_id"=>$q->product_id,"category_id"=>$q->sub_category_id);
			$this->db->insert("tbl_product_category",$data);
			print_r($q);
		}

	}*/
	/*
	function checkEmailTemplate(){
		$location = $this->session->userdata('checkout_ship');
		$checkout_billing = $this->session->userdata('checkout_billing');
		$location['state'] = (is_numeric($location['state']))?getCountryNameByKeyValue('id', $location['state'], 'code', true,'tbl_states'):$location['state'];
		$location['country'] = getCountryNameByKeyValue('id', $location['country'], 'nicename', true);
		$checkout_billing['state'] = (is_numeric($checkout_billing['state']))?getCountryNameByKeyValue('id', $checkout_billing['state'], 'code', true,'tbl_states'):$checkout_billing['state'];
		$checkout_billing['country'] = getCountryNameByKeyValue('id', $checkout_billing['country'], 'nicename', true);
		$first_name = ucfirst($this->session->userdata('firstname'));
		$location = array('billing'=> $checkout_billing, 'shipping'=>$location);
		$product= array (array( 'id' => 3066, 'prd_id' => 1694, 'condition_id' => 1, 'sp_id' => 2639, 'qty' => 1, 'original' => 110.99, 'price' => 110.99, 'warehouse_id' => 0, 'name' => 'LG Tribute Empire', 'condition' => 'New', 'img' => '9c4a675ea3d52ced3c18fe4ac8eb6eb2_thumb.jpg', 'already_saved' => 15, 'options' => array(), 'variant' => array(), 'variant_ids' => array(), 'upc_code' => 1452015903, 'seller_sku' => "",'seller_id' => '5dc40cd4eaf06', 'is_local' => 1, 'max_qty' => 9, 'sell_quantity' => 8, 'store_name' => 'Pitstop', 'shipping_id' => 3, 'shipping_title' => 'Free Standard Shipping', 'shipping_price' => 0, 'valid_to' => "",'discount_value' =>"",'discount_type' => "",'update_msg' =>"", 'referral' =>"", 'slug' => "lg-tribute-empire",'rowid' => "d8ab1a52f058358b947cdf8261b5e1a2", 'subtotal' => 110.99 ) );

		$subject = "You have a new Order";
		$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Great new, you have sold an item on Zab.ee. See the details below. Please refer to Zab.ee Terms & Service with regards to shipping requirements.</p>';
		echo "Subject-> ".$subject;
		$body= $this->Utilz_Model->send_mail($location,$product,"123",'qa1.kaygees@gmail.com', $type="seller",$subject,$text,$first_name);
		echo $body;
		echo "<hr>";
		$subject = 'Your Zab.ee Order 123 Is Placed';
		$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Thank you for shopping with Zab.ee. Your order has been placed, please see below for details. You will receive another email when the order is shipped.</p>';
		echo "Subject-> ".$subject;
		$body= $this->Utilz_Model->send_mail($location,$product,"123","devwarlock@gmail.com", $type="buyer",$subject,$text,$first_name);

		echo "<hr>";
		$subject = "Your Zab.ee Order 123 Is Confirmed";
		$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Great news! Your order 123 is shipping soon. The tracking information, if available, is given below</p><p style="margin-bottom:0px;font-size:16px; font-weight:bold">Shipping Method:</p><span>UPS Ground: tr12345</span>';
		echo "Subject-> ".$subject;
		$body= $this->Utilz_Model->send_mail($location,$product,"123","devwarlock@gmail.com", $type="buyer",$subject,$text,$first_name);

		echo "<hr>";
		$subject = "Your Zab.ee Order 123 Is Canceled";
		$text = '<p style="font-weight: 300; font-size: 16px;margin-bottom:0px">Your order 123 has been canceled. Your credit card has not been charged.</p><p style="margin-bottom:0px;font-size:16px; font-weight:bold">Reason for Cancelation:</p><span>Sorry out of stock</span>';
		echo "Subject-> ".$subject;
		$body= $this->Utilz_Model->send_mail($location,$product,"123","devwarlock@gmail.com", $type="buyer",$subject,$text,$first_name);
	}*/
	// public function megaSlug(){
	// 	$this->load->library('elasticsearch');
	// 	$elasticsearch = new elasticsearch;
	// 	set_time_limit(3000000);
	// 	$data = $this->db->query('SELECT
	// 	p.product_id,
	// 	p.product_name,
	// 	pin.price,
	// 	sp.seller_id,
	// 	sp.sp_id,
	// 	pv.pv_id,
	// 	c.category_name,
	// 	b.brand_name,
	// 	pcon.condition_name,
	// 	sp.condition_id,
	// 	k.keywords,
	// 	GROUP_CONCAT(DISTINCT v.v_title) as variant,
	// 	AVG(preview.`rating`) AS rating,
	// 	p.short_description,
	// 	d.value AS discount_value,
	// 	d.type AS discount_type,
	// 	d.valid_from AS valid_from,
	// 	d.valid_to AS valid_to,
	// 	p.brand_id,
	// 	GROUP_CONCAT(DISTINCT pc.category_id) as category_id
	// 	FROM tbl_product p
	// 	  JOIN tbl_product_category pc
	// 		ON pc.product_id = p.product_id
	// 	  JOIN tbl_categories c
	// 		ON c.category_id = pc.category_id
	// 	  JOIN tbl_brands b
	// 		ON b.brand_id = p.brand_id
	// 	  JOIN tbl_seller_product sp
	// 		ON sp.product_id = p.product_id
	// 	  JOIN tbl_product_conditions pcon
	// 		ON pcon.condition_id = sp.condition_id
	// 	  JOIN tbl_product_inventory pin
	// 		ON pin.product_id = p.product_id
	// 	  JOIN tbl_product_variant pv
	// 		ON pv.product_id = p.product_id
	// 		  AND pv.sp_id = sp.sp_id
	// 		  AND pv.condition_id = sp.condition_id
	// 	  LEFT JOIN tbl_variant v
	// 		ON FIND_IN_SET(v.v_id,pv.variant_group)
	// 	  LEFT JOIN tbl_meta_keyword k ON k.product_id = p.product_id
	// 	  LEFT JOIN `tbl_product_reviews` AS `preview` ON `preview`.`product_id` = `pin`.`product_id`
	// 	  LEFT JOIN `tbl_policies` AS `d` ON pin.`discount` = d.`id` AND `d`.`display_status` ="1"
	// 	GROUP BY pv.pv_id')->result();
	// 	foreach($data as $d){
	// 		$string = strtolower($d->product_name.' '.$d->variant.' '.$d->keywords.' '.$d->brand_name.' '.$d->category_name.' '.$d->condition_name);
	// 		$slug = str_replace(","," ",trim($string));
	// 		$slug = urlClean($slug);
	// 		//echo $slug;die();
	// 		//$string = explode(",",$string);
	// 		//$slug = preg_replace('/(\w{2,})(?=.*?\\1)\W*/', '', $string);
	// 		//$slug = "";
	// 		//echo $string;
	// 		//echo "<br>";
	// 		//foreach($string as $s){
	// 		//	$slug .= $s;
	// 			//$slug = preg_replace("/\b(\w+)\s+\\1\b/i", "$1", $slug)." - ";
	// 			//$slug = preg_replace('/(\w{2,})(?=.*?\\1)\W*/', '', $slug)." - ";
	// 		//}
	// 		//echo $slug;
	// 		//$slug = urlClean($slug);
	// 		//echo $slug;die();
	// 		$params = [
	// 			'index' => 'product',
	// 			'type' => '_doc',
	// 			'id' => $d->pv_id,
	// 			'body' => [ "product_id"=>$d->product_id,
	// 						"product_name"=>$d->product_name,
	// 						"slug"=> $slug,
	// 						"condition_id"=>$d->condition_id,
	// 						"brand_id"=>$d->brand_id,
	// 						"category_id"=>$d->category_id,
	// 						"seller_product_id"=> $d->sp_id,
	// 						"pv_id"=> $d->pv_id,
	// 						"rating"=>$d->rating,
	// 						"price"=>$d->price,
	// 						"discount_value"=>$d->discount_value,
	// 						"discount_type"=>$d->discount_type,
	// 						"valid_from"=>$d->valid_from,
	// 						"valid_to"=>$d->valid_to,
	// 						"seller_id"=>$d->seller_id,
	// 						"description"=>$d->short_description],
	// 			'client' => [
	// 				'curl' => [
	// 					CURLOPT_CUSTOMREQUEST => 'POST',
	// 					CURLOPT_HTTPHEADER =>array('Content-Type: application/json')
	// 				]
	// 			]
	// 		];
	// 		$data = $elasticsearch->add($params);
	// 		//$insertData = array("product_id"=>$d->product_id,"sp_id"=>$d->sp_id,"pv_id"=>$d->pv_id,"slug"=>$slug);
	// 		//$this->db->insert("tbl_product_slug",$insertData);
	// 		//echo "<pre>";print_r($insertData);die();
	// 	}
	// 	echo "<pre>";print_r($data);//die();
	// }
	public function megaSlug(){
		set_time_limit(3000000);
		$data = $this->db->query('SELECT
			p.product_id,
			sp.sp_id,
			pv.pv_id,
			p.product_name,
			c.category_name,
			b.brand_name,
			pcon.condition_name,
			k.keywords,
			GROUP_CONCAT(DISTINCT v.v_title) as variant
			FROM tbl_product p
			  JOIN tbl_product_category pc
				ON pc.product_id = p.product_id
			  JOIN tbl_categories c
				ON c.category_id = pc.category_id
			  JOIN tbl_brands b
				ON b.brand_id = p.brand_id
			  JOIN tbl_seller_product sp
				ON sp.product_id = p.product_id
			  JOIN tbl_product_conditions pcon
				ON pcon.condition_id = sp.condition_id
			  JOIN tbl_product_inventory pin
				ON pin.product_id = p.product_id
			  JOIN tbl_product_variant pv
				ON pv.product_id = p.product_id
				  AND pv.sp_id = sp.sp_id
				  AND pv.condition_id = sp.condition_id
			  LEFT JOIN tbl_variant v
				ON FIND_IN_SET(v.v_id,pv.variant_group)
			  LEFT JOIN tbl_meta_keyword k ON k.product_id = p.product_id
			GROUP BY pv.pv_id')->result();
		foreach($data as $d){
			$d->keywords = str_replace(","," ",trim($d->keywords));
			$d->variant = str_replace(","," ",trim($d->variant));
			$string = strtolower($d->product_name.' '.$d->variant.' '.$d->keywords.' '.$d->brand_name.' '.$d->category_name.' '.$d->condition_name);
			$string = explode(" ",$string);
			$string = array_unique($string);
			foreach($string as $key=>$value){
				if(strlen($value) < 3 && $value !=""){
					$string[$key] = $value."__";
				}
			}
			$slug = implode(" ",$string);
			$insertData = array("product_id"=>$d->product_id,"sp_id"=>$d->sp_id,"pv_id"=>$d->pv_id,"slug"=>$slug);
			$this->db->insert("tbl_product_slug",$insertData);
		}
	}
	public function remaningSlug(){
		$this->db->query('SET SESSION group_concat_max_len = 10000000');
		$query = $this->db->select("GROUP_CONCAT(pv_id) as pv_id")->from("tbl_product_variant")->where_not_in("pv_id","SELECT pv_id FROM tbl_product_slug",FALSE)->get()->row();
		//echo $this->db->last_query();die();
		if($query->pv_id){
			//echo $query->pv_id;
			set_time_limit(3000000);
			$data = $this->db->query("SELECT
			p.product_id,
			sp.sp_id,
			pv.pv_id,
			p.product_name,
			c.category_name,
			b.brand_name,
			pcon.condition_name,
			k.keywords,
			GROUP_CONCAT(DISTINCT v.v_title) as variant
			FROM tbl_product p
			JOIN tbl_product_category pc
				ON pc.product_id = p.product_id
			JOIN tbl_categories c
				ON c.category_id = pc.category_id
			JOIN tbl_brands b
				ON b.brand_id = p.brand_id
			JOIN tbl_seller_product sp
				ON sp.product_id = p.product_id
			JOIN tbl_product_conditions pcon
				ON pcon.condition_id = sp.condition_id
			JOIN tbl_product_inventory pin
				ON pin.product_id = p.product_id
			JOIN tbl_product_variant pv
				ON pv.product_id = p.product_id
				AND pv.sp_id = sp.sp_id
				AND pv.condition_id = sp.condition_id
			LEFT JOIN tbl_variant v
				ON FIND_IN_SET(v.v_id,pv.variant_group)
			LEFT JOIN tbl_meta_keyword k ON k.product_id = p.product_id
			WHERE pv.pv_id IN (".$query->pv_id.")
			GROUP BY pv.pv_id")->result();
			//echo $this->db->last_query();die();
			//echo "<pre>";print_r($data);die();
			foreach($data as $d){
				$d->keywords = str_replace(","," ",trim($d->keywords));
				$d->variant = str_replace(","," ",trim($d->variant));
				$string = strtolower($d->product_name.' '.$d->variant.' '.$d->keywords.' '.$d->brand_name.' '.$d->category_name.' '.$d->condition_name);
				$string = explode(" ",$string);
				$string = array_unique($string);
				foreach($string as $key=>$value){
					if(strlen($value) < 3 && $value !=""){
						$string[$key] = $value."__";
					}
				}
				$slug = implode(" ",$string);
				$insertData = array("product_id"=>$d->product_id,"sp_id"=>$d->sp_id,"pv_id"=>$d->pv_id,"slug"=>$slug);
				$this->db->insert("tbl_product_slug",$insertData);
			}
		}else{
			echo "No slug remaining";
		}
	}
	public function makeCategoryData(){
		$query = $this->db->select("product_id,sub_category_id")->from("tbl_product")->get()->result();
		echo "<pre>";
		set_time_limit(3000000);
		foreach($query as $q){
			$data = array("product_id"=>$q->product_id,"category_id"=>$q->sub_category_id);
			$this->db->insert("tbl_product_category",$data);
			print_r($q);
		}

	}
	public function remaningCategoryData(){
		$q = $this->db->select("GROUP_CONCAT(product_id) as product_id")->from("tbl_product")->where_not_in("product_id","(SELECT product_id FROM tbl_product_category)",FALSE)->get()->row();
		//echo $this->db->last_query();die();
		if($q->product_id){
			$query = $this->db->select("product_id,sub_category_id")->from("tbl_product")->where_in("product_id",$q->product_id,FALSE)->get()->result();
			echo "<pre>";
			print_r($query);
			set_time_limit(3000000);
			foreach($query as $qa){
				$data = array("product_id"=>$qa->product_id,"category_id"=>$qa->sub_category_id);
				$this->db->insert("tbl_product_category",$data);
				print_r($qa);
			}
		}else{
			echo "No cat remaining";
		}

	}
	//ye kya kia huwa hay ?
	public function orderListMobile(){
		$this->data['page_name'] 	= 'order_list_mobile';
		$this->data['hasScript'] = true;
		$this->data['newsletter'] = false;
		$this->data['title'] 		= "Order History";

		$user_id = $this->session->userdata('userid');
		$table_name = "tbl_transaction";
		$order_by = 'created DESC';
		$select = "order_id";
		$where = array('user_id'=>$user_id);
		$order_id = $this->Utilz_Model->getAllData($table_name, $select, $where,0,"",$order_by,"");
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
					$o->product_image = $productImage[0]->is_primary_image;
				}else{
					$o->product_image = "";
				}
			}
		}
	   $this->data['orders'] = $this->Cart_Model->formatOrderList($order_list,'data');
	//    echo "<pre>"; print_r($this->data['orders']); die();
	}
	    $this->session->set_userdata('review_prod_id', $this->input->post( 'product_id' ));
		$this->session->set_userdata('review_product_variant_id', $this->input->post( 'product_variant_id' ));
		$this->session->set_userdata('review_order_id', $this->input->post( 'order_id' ));
		$this->session->set_userdata('review_product_name', $this->input->post( 'product_name' ));
		// $table_name = "tbl_transaction";
		// $select = "COUNT(id) AS total_rows";
		// $where = array('user_id'=>$user_id);
		// $totalRows = $this->Utilz_Model->getAllData($table_name, $select, $where);
		// $totalRows = $totalRows[0]->total_rows;
		// $config["total_rows"] = $totalRows;
		// $config['first_link'] ='First';
		// $last_page_number = ceil($config["total_rows"]/$config["per_page"]);
		// $config['last_link']  = 'Last';
		// $this->pagination->initialize($config);
		// $str_links = $this->pagination->create_links();
		// $links["links"] = explode(', ', $str_links);
		// $this->data['links'] = $links;
		$this->Secure_Model->updateOrderNotification($user_id);
		$this->load->view('front/template', $this->data);
	}

	public function searched_query(){
		$data = array(
			'userid' => $this->input->post('userid'),
			'first_name'=> $this->input->post('first_name'),
			'last_name'=> $this->input->post('last_name'),
			'contact'=> $this->input->post('contact'),
			'email'=> $this->input->post('email'),
			'price'=> $this->input->post('price'),
			'item_condition'=> $this->input->post('condition'),
			'query'=> $this->input->post('query'),
			'description'=> $this->input->post('description'),
		);

		$result = json_encode($this->User_Model->searched_query($data));
		echo $result;
	}
	public function create_mobile_thumbs(){
		$data = $this->db->select("thumbnail")->from("tbl_product_media")->where(array("is_local"=>"1","is_image"=>"1"))->get()->result();
	}
	/*public function deleteSess(){
		$this->session->sess_destroy();
		delete_cookie("ecomm_userData");
		delete_cookie("ecomm_adminData");
		$this->isloggedin = FALSE;
		die("here");
	}*/
}
?>