<?php $queryString = explode('/',$_SERVER['REDIRECT_QUERY_STRING']);
	if(isset($queryString[1]) && $queryString[1] == "seller"){
	?>	
    	<link href="<?php echo assets_url('chat/css/admin_chat.css');?>" rel="stylesheet">	
	<?php }else{
	?>
    	<link href="<?php echo assets_url('chat/css/front_chat.css');?>" rel="stylesheet">
	<?php }
?>
<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<div class="">
  <div class="row chatBox">
    	<table id="preloadView2" >
            <tr>
                <td></td>
                <td>				
                    <center>
                        <img src="<?php echo assets_url('front/images/loading-main1.gif'); ?>">
                        <br><br><br>
                        <div style="color: #60697E;font-size: 30px;"><b><?php echo $this->lang->line('hold_on');?></b> <?php echo $this->lang->line('fetching_msgs');?>...</div>
                    </center>			
                </td>
                <td></td>
            </tr>		
        </table>
        <div class="col-lg-3 buyerList text-center <?php echo (isset($buyerList['class'])?$buyerList['class']:"");?>">
        	<?php echo $this->lang->line('msgs_list');?>
        </div>
        <div class="<?php echo (isset($buyerList['class'])?"col-12":"col-8");?> buyerList invisible text-center" id="chatPanel">
        </div>
        <div class="clearfix">
        </div>
        <div class="conversation-wrap col-lg-3 <?php echo (isset($buyerList['class'])?$buyerList['class']:"");?>">
    		<?php 
			// echo "<pre>";print_r($buyerList);die();//print_r($_SESSION);echo "</pre>";
			if($buyerList['rows'] >0){
				foreach($buyerList['result'] as $buyer){
					$product_variant_id = ($buyer->product_variant_id)?$buyer->product_variant_id:"";
					//echo "<pre>";print_r($buyer);//echo "</pre>"; echo base_url('uploads/profile/buyer_'.$buyer->userid.'.PNG'); echo (file_exists('./uploads/profile/buyer_'.$buyer->userid.'.PNG'))?"1":"0";?>
					<div class="media conversation" id="media-<?php echo $buyer->userid.'-'.$buyer->product_variant_id.'-'.$buyer->item_type;?>" data-seentime="<?php echo $buyer->seen_datetime?>">
						<a class="pull-left" href="#">
                        	<img class="media-object" style="width: 50px; height: 50px;" src="<?php echo base_url('home/get_image/?path='.urlencode(profile_path()).'&file='.urlencode($buyer->picCheck.$buyer->userid.'.png')); ?>">
						</a>
						<div class="media-body open-chat handCursor" data-id="<?php echo $buyer->userid.'-'.$product_variant_id.'-'.$buyer->item_type;?>" onclick="openChat('<?php echo $buyer->userid;?>','<?php echo $product_variant_id?>','<?php echo $buyer->item_type?>','<?php echo $buyer->seller_id?>','<?php echo $buyer->buyer_id?>','<?php echo $buyer->item_id?>','<?php echo urlClean($buyer->product_link)?>','<?php echo $_SESSION['userid']?>')">
							<h5 class="media-heading"><?php echo ucfirst($buyer->sender_name)?></h5>
							<small id="message-<?php echo $buyer->userid.'-'.$product_variant_id.'-'.$buyer->item_type;?>"><?php echo $buyer->message?></small>
                        </div>
						<?php if($buyer->subject != ""){?>
							<a href="<?php echo base_url('seller_info/'.base64_encode(urlencode($buyer->seller_id)));?>" class="fa fa-info-circle msg-info" title="<?php echo $buyer->sender_name?>"></a>
							<?php }else{ ?>
                       	 <a href='<?php echo base_url().'product/detail/'.$buyer->product_link.'-'.encodeProductID($buyer->product_name,$buyer->product_id)?>' class="fa fa-info-circle msg-info" title="<?php echo $buyer->product_name?>"></a>
						<?php } ?>
					</div>
            <?php }}else{?> 
            	<div class="media conversation">
                	<b class="error"><?php echo $this->lang->line('no_user_found');?></b>
                </div>
            <?php }?>
        </div>
        <div class="message-wrap <?php echo (isset($buyerList['class'])?"col-sm-12":"col-sm-8");?> d-none">
        	<div class="text-center">
            	<img id="loader" src="http://opengraphicdesign.com/wp-content/uploads/2009/01/loader64.gif">
            </div>
            <div class="msg-wrap">
            </div>
			<div class="send-wrap ">
            	<?php 
					$uri = explode('/',$_SERVER['REQUEST_URI']);
					if(file_exists('./uploads/profile/seller_'.$_SESSION['userid'].'.PNG||png') && isset($uri[2]) && $uri[2] == "seller"){
						$link = profile_path('seller_'.$_SESSION['userid'].'.PNG');
					}else if(file_exists('./uploads/profile/buyer_'.$_SESSION['userid'].'.PNG||png') && isset($uri[2]) && $uri[2] != "seller"){
						$link = profile_path('buyer_'.$_SESSION['userid'].'.PNG');
					}else {
						$link = assets_url('backend/images/defaultprofile.png');
					} 
				?>
            	<input type="hidden" id="sender_id" value=""  />
                <input type="hidden" id="product_variant_id" value=""  />
                <input type="hidden" id="seller_id" value=""  />
                <input type="hidden" id="buyer_id" value=""  />
                <input type="hidden" id="item_id" value=""  />
                <input type="hidden" id="item_type" value=""  />
            	<input type="hidden" id="user_pic" value="" />
                <input type="hidden" id="userid" value="<?php echo $_SESSION['userid'];?>" />
                <input type="hidden" id="name" value="<?php echo $_SESSION['firstname']." ".$_SESSION['lastname'];?>" />
		    	<textarea class="form-control send-message" rows="3" placeholder="<?php echo $this->lang->line('write_reply');?>..."></textarea>
			</div>
            <div class="btn-panel">
               <!-- <a href="" class=" col-lg-3 btn   send-message-btn " role="button"><i class="fa fa-cloud-upload"></i> Add Files</a>-->
                <a href="javascript:void(0)" class=" col-lg-4 text-center btn btn-success send-message-btn pull-right" role="button"><i class="fa fa-plus"></i> <?php echo $this->lang->line('send_msg');?></a>
            </div>
        </div>
    </div>
</div>