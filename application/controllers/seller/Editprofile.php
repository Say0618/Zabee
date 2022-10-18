<?php
class Editprofile extends SecureAccess{
	function __construct(){
		parent:: __construct();
		$this->load->model("admin/Profile_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
	}

	public function index(){
		$data['oObj'] = $this;
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->helper(array('form','url'));
		$this->load->library('form_validation');
		$this->data['error'] = '';
		$this->data['profile_id'] = '';
		$this->data['prof'] = false;
		$this->form_validation->set_rules('yourpublicname','YourPublicName','trim|required');
		$this->form_validation->set_rules('website','Website','trim|required');
		$this->form_validation->set_rules('email','Email','trim|required|valid_email');
		$this->form_validation->set_rules('bio','Bio','trim|required');
		if($this->form_validation->run() === true){
			$post_data = $this->input->post();
			$this->data['profile_id'] = $this->input->post('profile_id');
			$insert_data = array(
							'created' 			=> date('Y-m-d H:i:s'),
							'updated' 			=> date('Y-m-d H:i:s'),
							'userid' 			=> $user_id,
							'public_name' 		=> $post_data['yourpublicname'],
							'website_name' 		=> $post_data['website'],
							'the_email' 		=> $post_data['email'],
							'the_bio' 			=> $post_data['bio'],
							'facebook_link' 	=> $post_data['Facebook'],
							'twitter_link' 		=> $post_data['Twitter'],
							'instagram_link' 	=> $post_data['Youtube'],
							'youtube_link' 		=> $post_data['Pinterest'],
							'pinterest_link' 	=> $post_data['Instagram'],
							'active' 			=> 1
							);
			$_SESSION['public_name'] = $post_data['yourpublicname'];
			if($this->data['profile_id'] == ''){
				$img = $this->uploadImage('uploads/profile/', $_FILES['profile_image'], 'profiles', $user_id);
				$insert_data['profile_imagelink'] = $img['original'];
				$resp = $this->Profile_Model->profile_add($insert_data);
					if($resp){
						$this->session->set_flashdata("success","Profile added successfully.");
						redirect(base_url('seller/editprofile?status=success&action=add'));
						die();
					}
			} else {
				if($_FILES['profile_image']['name'] != ''){
					$img = $this->uploadImage('uploads/profile/', $_FILES['profile_image'], 'profiles', $user_id);
					$insert_data['profile_imagelink'] = $img['original'];
				}
				unset($insert_data['created']);
				$resp = $this->Profile_Model->profile_update($insert_data, $this->data['profile_id']);
				if($resp){
					$this->session->set_flashdata("success","Profile edited successfully.");
					redirect(base_url('seller/editprofile?status=success&action=update'));
					die();
				}
			}	
		}
		$profiles = $this->Profile_Model->getProfiles($user_id);
		if($profiles){
			$this->data['profile_id'] 	= $profiles->id;
			$this->data['prof'] 		= $profiles;
		}
		$this->data['page_name'] 		= 'editprofile';
		$this->data['Breadcrumb_name'] 	= 'Profile';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);
	}	
}
?>