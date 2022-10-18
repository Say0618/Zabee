<?php
class Message extends SecureAccess 
{
	function __construct(){
		parent::__construct();
		$this->load->model("Message_Model");
		$this->load->model("Product_Model");
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

	public function index()
	{
		$id = "";
		if(isset($this->userData[0]["userid"])){
			$id = $this->userData[0]["userid"];
		}else{
			$id = $this->userData[0]["admin_id"];
		}
		$buyerList = $this->Message_Model->getBuyerList($id);
		$i = 0;
		if($buyerList['rows'] > 0){
			foreach($buyerList['result'] as $bl){ 
				if($bl->userid == $bl->seller_id){
					$buyerList['result'][$i]->picCheck = "seller_";
				}else{
					$buyerList['result'][$i]->picCheck = "buyer_";
				}
				if($bl->product_variant_id){
					if($bl->item_type == "product"){
						$product = $this->Product_Model->productDetails('','',$bl->product_variant_id);
						if($product['productDataRows'] > 0){
							$buyerList['result'][$i]->product_link=$product['productData']->product_name."-".$product['productData']->brand_name."-".$product['productData']->category_name;
							$buyerList['result'][$i]->product_id=$product['productData']->product_id;
							$buyerList['result'][$i]->product_name=$product['productData']->product_name;
						}else{
							$buyerList['result'][$i]->product_link="";
							$buyerList['result'][$i]->product_id="";
							$buyerList['result'][$i]->product_name="";
						}
					}
				}else{
					$buyerList['result'][$i]->product_link="";
					$buyerList['result'][$i]->product_id="";
					$buyerList['result'][$i]->product_name="";
				}
				$i++;
			}
		}
		$this->data['buyerList'] 		= $buyerList;
		$this->data['page_name'] 		= 'chat';
		$this->data['Breadcrumb_name'] 	= $this->lang->line('messages');
		$this->data['isScript'] 		= false;
		$this->load->view("admin/admin_template",$this->data);
	}

	public function getMessages(){
		$desc 				= 0;
		$loadLimit 			= 0;
		$sender_id 			= $this->input->post('sender_id');
		$product_variant_id = $this->input->post('product_variant_id');
		$item_type 			= $this->input->post('item_type');
		$user_pic 			= "";
		if($this->input->post('desc')){
			$desc = 1;
		}
		if($this->input->post('loadLimit')){
			$loadLimit = $this->input->post('loadLimit');
		}
		if($sender_id == "" || $product_variant_id == "" || $item_type == ""){
			echo json_encode(array('status'=>'0','error'=>$this->lang->line('no_msg_found')));
			return false;
		}
		$receiver_id = "";
		if(isset($this->userData[0]["userid"])){
			$receiver_id = $this->userData[0]["userid"];
		}else{
			$receiver_id = $this->userData[0]["admin_id"];
		}
		$getMessages = $this->Message_Model->getMessages($sender_id,$receiver_id,$desc,$loadLimit,$product_variant_id,$item_type);
		$messages 	 = array();
		if($getMessages['rows'] >0){
			$i=0;
			foreach($getMessages['result'] as $message){
				$who 		= "you";
				$date 		= strtotime($message->sent_datetime);
				$sentdate 	= date("j F Y", $date);
				if($sentdate == date("j F Y")){
					$sentdate = "Today";
				}
				//if($message->userid == $message->seller_id){
					//$pic = base_url('home/get_image/?path='.urlencode(profile_path()).'&file='.urlencode('seller_'.$message->userid.'.png'));
						
					
					/*if(file_exists('./uploads/profile/seller_'.$message->userid.'.PNG')){
						$pic = base_url('uploads/profile/seller_'.$message->userid.'.PNG');
					}elseif(file_exists('./uploads/profile/seller_'.$message->userid.'.png')){
						$pic = base_url('uploads/profile/seller_'.$message->userid.'.png');
					}else{
						$pic = media_url('assets/backend/images/defaultprofile.png');
					}*/
				//}
				//if($message->userid == $message->buyer_id){
					/*if(base_url('home/get_image/?path='.urlencode(profile_path()).'&file='.urlencode('buyer_'.$message->userid.'.png'))){
						$pic = profile_path("buyer_".$message->userid.'.png');	
					}*/
					//$pic = base_url('home/get_image/?path='.urlencode(profile_path()).'&file='.urlencode('buyer_'.$message->userid.'.png'));
					/*if(file_exists('./uploads/profile/buyer_'.$message->userid.'.PNG')){
						$pic = base_url('uploads/profile/buyer_'.$message->userid.'.PNG');
					}else if(file_exists('./uploads/profile/buyer_'.$message->userid.'.png')){
						$pic = base_url('uploads/profile/buyer_'.$message->userid.'.png');
					}else{
						$pic = media_url('assets/backend/images/defaultprofile.png');
					}*/
				//}
				$pic="";
				if($message->sender_id == $receiver_id){
					$who = "me";
					$user_pic = $pic;
				}
				$data = array('text'=>$message->message, 'sendtime'=>$message->sent_datetime,'sender_name'=>$message->sender_name,'pic'=>$pic,'who'=>$who);
				$messages[$sentdate]['messages'][] = $data;
				$i++;
			}
			echo json_encode(array('status'=>'1',"user_pic"=>$user_pic,'messages'=>$messages));
		}else{
			echo json_encode(array('status'=>'0','error'=>$this->lang->line('no_msg_found')));
		}
	}

	public function saveMessage(){
		$sender_id = "";
		if(isset($this->userData[0]["userid"])){
			$sender_id = $this->userData[0]["userid"];
		}else{
			$sender_id = $this->userData[0]["admin_id"];
		}
		$data = array('sent_datetime'=>$this->input->post('time'),'sender_id'=>$sender_id,'receiver_id'=>$this->input->post('receiver_id'),'message'=>$this->input->post('message'),'product_variant_id'=>$this->input->post('product_variant_id'),'item_type'=>$this->input->post('item_type'),'seller_id'=>$this->input->post('seller_id'),'buyer_id'=>$this->input->post('buyer_id'),'item_id'=>$this->input->post('item_id'));
		$result = $this->Message_Model->saveData($data,'tbl_message');
		if($result){
			echo json_encode(array('status'=>'1','messages'=>$result));
		}else{
			echo json_encode(array('status'=>'0','error'=>$this->lang->line('no_msg_found')));
		}
	}

	public function saveSeenTime(){
		extract($this->input->post());
		$data = array('receiver_id'=>$receiver_id,'product_variant_id'=>$product_variant_id,'item_type'=>$item_type,'item_id'=>$item_id,'seller_id'=>$seller_id,'buyer_id'=>$buyer_id);
		$result = $this->Message_Model->saveSeenDateTime($data,array('seen_datetime'=>$seen_datetime));
		echo json_encode($result);
	}
	
	function __destruct() {

  	}
}
?>