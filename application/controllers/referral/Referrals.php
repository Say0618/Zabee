<?php

class Referrals extends Securearea {
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->helper("url");
		$this->load->library('session');	
		$this->load->library('form_validation');
		$this->load->library('Mobile_Detect');	
		$detect = new Mobile_Detect;
		$this->data['detect'] = $detect;
		$this->load->model("User_Model");
        $this->load->model("Utilz_Model");
        $this->load->model("Referral_Model");		
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
        $this->data['page_name'] 		= 'product_referral_list';
        $this->data['Breadcrumb_name'] 	= "Product Referral";
        $this->data['isScript'] 		= true;
        $this->load->view("referral/referral_template",$this->data);
    }
    
	public function invite(){
		if($this->session->userdata("userid") != null && (isset($_SESSION['affiliated_id']) && $_SESSION['affiliated_id'] != "")){
			$data['page_name'] 	= 'invite';
			$data['Breadcrumb_name'] 	= "Invite Peoples";
			$data['isScript'] 	= true;
			$data['newsletter'] = false;
			$data['title'] 		= "Invite Peoples";
			$this->load->view('referral/referral_template', $data);
		}else{
			redirect(base_url("login"));
		}
    }

    public function invite_people($type = ""){
		if($type == "product"){
			$post_data = $this->input->post();
			$firstSalt = $post_data['sender_id'];
			$lastSalt = $post_data['prd_id'];
			$code = $firstSalt.'zabee'.$lastSalt;
			$code = sha1($code);
			$found = $this->Referral_Model->check_code($code);
			if($found == false){
				$date = date("Y-m-d H:i:s");
				$exp_date = date('Y-m-d H:i:s', strtotime($this->config->item('exp_time'), strtotime($date)));
				$post_data['referral_type'] = $type;
				$post_data['referral_code'] = $code;
				$post_data['invite_time'] = $date;
				$post_data['expire_time'] = $exp_date;
				$result = $this->User_Model->saveData($post_data, "tbl_referral");
				if($result != false){
					echo json_encode(array("status"=>'1', 'code'=>$code));
				}else{
					echo json_encode(array("status"=>'2', 'code'=>'error in insertion'));
				}
			}else{
				echo json_encode(array('status'=>'1','code'=>$found));
			}
		}else{
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
			if (!$result->success) {
				$this->session->set_flashdata('error', "Captcha verification Failed.");
				redirect(base_url("invite"));
			}else{
				$checkuser = $this->User_Model->getUserByEmail($this->input->post('email'));
				$already_sent = $this->Referral_Model->foundUserReferral($this->session->userdata('userid'), $this->input->post('email'));
				if($checkuser == null){
					if($already_sent['status'] == "1"){
						$user_type = (!empty($this->input->post('user_type')) && $this->input->post('user_type') == "seller") ? "&type=".$this->input->post('user_type') : ""; 
						$first_name = $this->input->post('first_name');
						$last_name = $this->input->post('last_name');
						$email = $this->input->post('email');
						$date = date("Y-m-d H:i:s");
						$exp_date = date('Y-m-d H:i:s', strtotime($this->config->item('exp_time'), strtotime($date)));
						$firstSalt = explode("@",$email);
						$lastSalt = $first_name.$last_name;
						$code = $firstSalt[0].$date.$lastSalt;
						$code = sha1($code);
						$info_data = array("sender_id"=>$this->session->userdata("userid"),
											"receiver_firstname"=>$first_name,
											"receiver_lastname"=>$last_name,
											"receiver_email"=>$email,
											"referral_type"=>"invite",
											"referral_code"=>$code,
											"invite_time"=>$date,
											"expire_time"=>$exp_date);
						$result = $this->User_Model->saveData($info_data, "tbl_referral");
						if($result != false){
							$from = $this->config->item('info_email');
							$name = $this->config->item('author');
							$subject = 'Zabee : Your Referral Code'; 
							$this->load->library('parser');
							$data['base_path'] 	= base_url(); 
							$data['page_name'] 	= "email_template_for_referral_code"; 
							$data['firstname'] 	= $first_name.$last_name;
							$data['email']	   	= $email;
							$data['senderid']  	= $this->session->userdata("userid");
							$data['type']		= $user_type;
							$data['encryption'] = $code;
							$message = $this->parser->parse('front/emails/email_template', $data, TRUE);
							if($this->Utilz_Model->email_config($from, $name, $email, $subject, $message)){
								$this->session->set_flashdata('invite',$code);
								$this->invite_list();
							}else{
								return array('status'=>0,'msg'=>$this->email->print_debugger());
							}
						}
					}else{
						$this->session->set_flashdata('error', $already_sent['message']);
						redirect(base_url("invite"));
					}
				}else{
					$this->session->set_flashdata('error', "Account against this email already exist.");
					redirect(base_url("invite"));
				}
			}
		}
    }
	
	public function invite_list(){
		$this->data['page_name'] 		= 'referral_list';
		$this->data['Breadcrumb_name'] 	= "Referral";
		$this->data['isScript'] 		= true;
		$this->load->view("referral/referral_template",$this->data);
	}
	
	public function referral_list() 
	{
		if(!empty($this->input->post('userid'))){
			$user_id = $this->input->post('userid');
		}else{
			$user_id = $this->session->userdata('userid');
		}
		if($user_id != null && (isset($_SESSION['affiliated_id']) && $_SESSION['affiliated_id'] != "")){
			$this->load->library('datatables');	
			$search = $this->input->post("aoData")[35]['value'];
			$offset = $this->input->post("aoData")[3]['value'];
			$length = $this->input->post("aoData")[4]['value'];
			$this->Referral_Model->get_referral($user_id, $search, $offset, $length);
			// $this->Referral_Model->get_referral($user_id);
			echo $this->datatables->generate();
		}else{
			redirect(base_url("login"));
		}
	}

	public function product_referral_list() 
	{
		if(!empty($this->input->post('userid'))){
			$user_id = $this->input->post('userid');
		}else{
			$user_id = $this->session->userdata('userid');
		}
		if($user_id != null && (isset($_SESSION['affiliated_id']) && $_SESSION['affiliated_id'] != "")){
				$this->load->library('datatables');
				$search = $this->input->post("aoData")[35]['value'];
				$offset = $this->input->post("aoData")[3]['value'];
				$length = $this->input->post("aoData")[4]['value'];
				$this->Referral_Model->get_product_referral($user_id, $search, $offset, $length);
                echo $this->datatables->generate();
		}else{
			redirect(base_url("login"));
		}
	}

	public function user_history(){
		$this->data['page_name'] 		= 'user_history_view';
		$this->data['Breadcrumb_name'] 	= "User History";
		$this->data['isScript'] 		= true;
		$this->load->view("referral/referral_template",$this->data);
	}

	public function getdata(){
		$this->load->library('datatables');	
		$this->Referral_Model->get_data();
		echo $this->datatables->generate();
	}
}
?>