<link href="<?php echo media_url('assets/chat/css/front_chat.css');?>" rel="stylesheet">
<link href="<?php echo media_url('assets/chat/css/font-awesome.css');?>" rel="stylesheet">

<!-- <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet"> -->
<div class="">
  <div class="row chatBox">
    	<table id="preloadView2" >
            <tr>
                <td></td>
                <td>				
                    <center>
                        <img src="<?php echo media_url(); ?>assets/front/images/loading-main1.gif">
                        <br><br><br>
                        <div style="color: #60697E;font-size: 30px;"><b>Hold On</b> fetching messages...</div>
                    </center>			
                </td>
                <td></td>
            </tr>		
        </table>
        <div class="col-lg-3 buyerList text-center">
        	Message List
        </div>
        <div class="col-lg-8 buyerList hide text-center" id="chatPanel">
        </div>
        <div class="clearfix">
        </div>
        <div class="conversation-wrap col-lg-3">
    		<?php 
			if($buyerList['rows'] >0){
				foreach($buyerList['result'] as $buyer){
					$product_variant_id = ($buyer->product_variant_id)?$buyer->product_variant_id:"";
					//echo "<pre>";print_r($buyer);echo "<pre>";?>
					<div class="media conversation" id="media-<?php echo $buyer->userid.'-'.$buyer->product_variant_id.'-'.$buyer->item_type;?>">
						<a class="pull-left" href="#">
                        	<img class="media-object" style="width: 50px; height: 50px;" src="<?php echo ($buyer->user_pic =="")?media_url('assets/backend/images/blank-profile-picture.png'):buyerprofile_path($buyer->user_pic)?>">
						</a>
						<div class="media-body open-chat handCursor" data-id="<?php echo $buyer->userid.'-'.$product_variant_id.'-'.$buyer->item_type;?>" onclick="openChat('<?php echo $buyer->userid;?>','<?php echo $product_variant_id?>','<?php echo $buyer->item_type?>','<?php echo $buyer->seller_id?>','<?php echo $buyer->buyer_id?>','<?php echo $buyer->item_id?>','<?php echo $buyer->product_link?>')">
							<h5 class="media-heading"><?php echo ucfirst($buyer->sender_name)?></h5>
							<small id="message-<?php echo $buyer->userid.'-'.$product_variant_id.'-'.$buyer->item_type;?>"><?php echo $buyer->message?></small>
                        </div>
                        <a href='<?php echo base_url().'product/detail/'.$buyer->product_link.'-'.encodeProductID($buyer->product_name,$buyer->product_id)?>' class="fa fa-info-circle msg-info" title="<?php echo $buyer->product_name?>"></a>
					</div>
            <?php }}else{?>
            	<div class="media conversation">
                	No Buyer Found.
                </div>
            <?php }?>
        </div>
		<div class="message-wrap col-lg-8 hide">
        	<div class="text-center">
            	<img id="loader" src="http://opengraphicdesign.com/wp-content/uploads/2009/01/loader64.gif">
            </div>
            <div class="msg-wrap">
            </div>
			<div class="send-wrap ">
            	<input type="hidden" id="sender_id" value=""  />
                <input type="hidden" id="product_variant_id" value=""  />
                <input type="hidden" id="seller_id" value=""  />
                <input type="hidden" id="buyer_id" value=""  />
                <input type="hidden" id="item_id" value=""  />
                <input type="hidden" id="item_type" value=""  />
            	<input type="hidden" id="user_pic" value="<?php echo ($_SESSION['user_pic'] =="")?media_url('assets/backend/images/blank-profile-picture.png'):buyerprofile_path($_SESSION['user_pic'])?>" />
                <input type="hidden" id="userid" value="<?php echo $_SESSION['userid'];?>" />
                <input type="hidden" id="name" value="<?php echo $_SESSION['firstname']." ".$_SESSION['lastname'];?>" />
		    	<textarea class="form-control send-message" rows="3" placeholder="Write a reply..."></textarea>
			</div>
            <div class="btn-panel">
               <!-- <a href="" class=" col-lg-3 btn   send-message-btn " role="button"><i class="fa fa-cloud-upload"></i> Add Files</a>-->
                <a href="javascript:" class=" col-lg-4 text-center btn btn-success send-message-btn pull-right" role="button"><i class="fa fa-plus"></i> Send Message</a>
            </div>
        </div>
    </div>
</div>