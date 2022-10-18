<style>
.list .heading{
    color: #707070;
}
.detail img{
    width: 100%;
}
.detail span{
    border-top: 1px solid lightgrey;
}
.final{
    /* text-align: right; */
    border-top: 1px solid lightgrey;
    padding-left: 55%;
}
.final span{
    border: 0px;
}
.final span:first-of-type{
    float: left;
    margin-right: 15px
}
.final span:last-of-type{
    text-align: right;
    /* margin-right: 15px */
}
.page_order_list_mobile .dropdown-menu{
    min-width: 8rem !important;
    left: -43px !important;
}
</style>
<div class="container bg-transparent">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>"Order History"))));?>
	<div class="row bg-white mt-2 order-list-radius">
		<div class="col-sm-12 mt-3"> 
			<div class="panel panel-primary" >
			    <div class="panel-heading">
					<h4><?php echo $this->lang->line('order_history');?></h4>
				</div>
				<div class="panel-body" >
                    <?php $system_date = date_create(date('Y-m-d h:i:s A'));
                    foreach($orders as $order){ 
                        static $count = 0;?>
                        <div class="list">
                            <div class="row">
                                <div class="col-4 pr-0 heading"><strong>Order Date</strong></div>
                                <div class="col-5 pr-0 pl-0"><strong><?php echo $order->created; ?></strong></div>
                                <div class="col pl-0 pr-0">
                                    <a class="p-info" href="#order_<?php echo $order->order_id; ?>"  data-toggle="collapse" >
                                        <img src="<?php echo $this->config->item("website_img_path")?>detail.png" style="width: 24px;">
                                    </a>
                                </div>
                                <div class="col pl-0 pr-0">
                                    <a id="actionDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <img src="<?php echo $this->config->item("website_img_path")?>cog.png" style="width: 28px;">
                                    </a>
                                    <div class="dropdown-menu order-list-dropDown" aria-labelledby="actionDropdownMenuButton">
                                        <a href="<?php echo base_url('buyer/getInvoice/'.$order->order_id)?>" class="actionDropDownItems" role="button">Get Invoice</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4 pr-0 heading"><strong>Order ID</strong></div>
                                <div class="col-5 pr-0 pl-0"><strong><?php echo $order->order_id; ?></strong></div>
                            </div>
                            <div class="collapse" id="order_<?php echo $order->order_id; ?>">
                                <div class="mt-2 pt-2 detail">
                                    <?php foreach($order->orders as $item){ 
                                        // echo"<pre>"; print_r($item); die();
                                        if($item->product_image != ""){
                                            $image = ($item->is_local == "0")?$item->product_image:product_thumb_path().$item->product_image;
                                        }
                                        else{
                                            $image =  $this->config->item("website_img_path")."Preview.png";
                                        }?>
                                        <span class="row d-flex pt-2 mt-2">
                                            <div class="col-4"><img src="<?php echo $image ?>"/></div>

                                            <div class="col-8">
                                                <strong><?php echo $item->product_name; ?></strong><br>
                                                <strong class="heading">Sold By: </strong><strong><?php echo $item->store_name; ?></strong><br>
                                                <strong class="heading">Condition: </strong><strong><?php echo $item->condition_title; ?></strong><br>
                                                <strong class="heading">Price: </strong><strong><?php echo "$".$this->cart->format_number($item->price); ?> x <?php echo $item->qty; ?></strong><br>
                                                <?php 
                                                    $order_date = date_create($item->created." A");
                                                    $days = date_diff($order_date, $system_date)->days;
                                                        if($item->order_status == 0 && $item->item_confirm_status == 0 && $item->cancellation_pending == "0" &&  $item->is_cancel == "0"){
                                                            if($days < 7){
                                                                $status = $this->lang->line('pending');
                                                            }else{
                                                                $status = "Order Cancelled";
                                                            }
                                                        } else if($item->order_status == 1 && $item->item_confirm_status != 1 && $item->cancellation_pending == "0") {
                                                            $status = $this->lang->line('proceed');
                                                        } else if($item->order_status == 1 && $item->item_confirm_status == 1){
                                                            $status = $this->lang->line('order_rec');
                                                        } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "0"){
                                                            $status = "Cancel Requested";
                                                        } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "1"){
                                                            $status = "Cancelled";
                                                        } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "2"){
                                                            $status = "Not Cancelled";
                                                        }else if($item->order_status == "3"){
                                                            $status = "Refunded";
                                                        } else{
                                                            $status = $this->lang->line('declined');
                                                        }
                                                ?>
                                                <strong class="heading">Status: </strong><strong><?php echo $status ?></strong><br>
                                            </div>

                                            <div class="col-6">
                                                <button type="button" data-pv_id="<?php echo $item->product_vid;?>" data-sp_id="<?php echo $item->sp_id;?>" data-seller_id="<?php echo $item->seller_id;?>" data-store_name="<?php echo $item->store_name;?>" class="btn contactuser contact-seller"><i class="fa fa-user"></i><?php echo $this->lang->line('contact_seller');?></button>
                                            </div>

                                            <div class="col-6">
                                                <?php 
                                                    if($item->order_status == 0 && $item->cancellation_pending == "0") { 
                                                        if($days < 7){ ?>
                                                            <button type="button"  data-td_id="<?php echo $item->id; ?>" data-order_id="<?php echo $order->order_id; ?>" data-seller_id="<?php echo $item->seller_id; ?>" data-product_name="<?php echo $item->product_name; ?>" class="btn btn-danger cancel-order" id="cancel_btn_<?php echo $count ?>"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;Request Cancellation</button>
                                                        <?php } else { ?>
                                                            <button type="button" class="btn btn-danger cancel-order" disabled><i class="fa fa-times" aria-hidden="true"></i>&nbsp;Order Cancelled</button>
                                                        <?php } 
                                                    } else if($item->order_status == 1 && $item->cancellation_pending == "0") { ?>
                                                        <button type="button" class="btn btn-success cancel-order-approved" disabled>&nbsp;Order Approved</button>
                                                    <?php } else if($item->order_status == 2 && $item->cancellation_pending == "0") { ?>
                                                        <button type="button" class="btn btn-danger cancel-order" disabled>&nbsp;Order Declined</button>
                                                    <?php } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "0") { ?>
                                                        <button type="button" class="btn btn-dark cancel-order" disabled>&nbsp;Cancellation Pending</button>
                                                    <?php } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "1") { ?>
                                                        <button type="button" class="btn btn-success cancel-order" disabled>&nbsp;<?php echo $this->lang->line('approved_cancel');?></button>
                                                    <?php } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "2") { ?>
                                                        <button type="button" class="btn btn-danger cancel-order" disabled>&nbsp;<?php echo $this->lang->line('declined_cancel');?></button>
                                                    <?php }	else if($item->order_status == 3 ) { ?>	
                                                        <button type="button" class="btn cancel-order" id="refund-btn" disabled>&nbsp;Order Refunded</button>
                                                    <?php } else if($item->order_status != 1 ) { ?>	
                                                        <button type="button" class="btn btn-default cancel-order" disabled>&nbsp;<?php echo $this->lang->line('declined');?></button>
                                                    <?php } $count++; ?>
                                            </div>
                                        </span>
                                    <?php } ?>
                                    <div class="col mt-2 pt-2 pr-0 final">   
                                        <span>
                                            <strong class="heading">Shipping: </strong></br>
                                            <strong class="heading">Tax: </strong></br>
                                            <strong class="heading">Total: </strong>
                                        </span>
                                        <span>  
                                            <strong>$<?php echo $this->cart->format_number($order->shipping_amt); ?></strong></br>
                                            <strong>$<?php echo $this->cart->format_number($order->tax_amount); ?></strong></br>
                                            <strong>$<?php echo $this->cart->format_number($order->gross_amount); ?></strong>
                                        </span>
                                        <!-- <br>
                                        <span>   
                                            
                                        </span>  -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="message-panel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span><?php echo $this->lang->line('contact');?></span><span class="pl-1" id="storeName"></span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="panel-body">
                        <div class="form-group">
                          <textarea class="form-control" id="message" rows="8" style="border-radius:5px;"></textarea>
                          <span></span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-right">
							<input type="hidden" id="seller_id" />
							<input type="hidden" id="sp_id" />
							<input type="hidden" id="pv_id" />
                        	<a href="javascript:" class="btn btn-primary" id="sendMessage">Send</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
  <!-- Modal -->
  <div class="modal fade" id="item_confirmation" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
		<p><?php echo $this->lang->line('are_you_sure');?></p>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-footer">
			<input type="hidden" id="cancel_order_id" />
			<input type="hidden" id="td_id" />
			<input type="hidden" id="seller_id" />
			<input type="hidden" id="product_name" />
			<input type="hidden" id="button_id" />
			<a href="" class="btn btn-success" id="confirm_item" data-dismiss="modal"><?php echo $this->lang->line('yes');?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- modal start -->
<div id="Modal" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
	<div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title"><?php echo $this->lang->line('your_review');?></h4>
		</div>
		<div class="modal-body">
			<p id="reviewModal"></p>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
		</div>
	</div>
	</div>
</div>
<!-- end -->