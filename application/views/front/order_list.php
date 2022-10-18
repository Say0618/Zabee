<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<div class="container bg-transparent">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>"Order History"))));?>
	<div class="row bg-white mt-2 order-list-radius">
		<div class="col-sm-12 mt-3"> 
			<div class="panel panel-primary" >
			<div class="panel-heading">
					<h4 class="ml-3"><?php echo $this->lang->line('order_history');?></h4>
				</div>
				<div class="panel-body" >
					<div class="table-responsive-sm" >
						<table cellpadding="0" cellspacing="0" border="0" class="table table-responsive p-2">
							<thead>
								<tr>
									
									<th align="center"><?php echo $this->lang->line('order_date');?></th>
									<th align="center"><?php echo $this->lang->line('order_id');?></th>
									<th align="center"><?php echo $this->lang->line('amount');?></th>
									<th align="center"><?php echo $this->lang->line('details');?></th>
									<th width="1%" align="center"><?php echo $this->lang->line('actions');?></th>
								</tr>
								<?php
								if(!empty($orders)){
									foreach($orders as $order) {
										if($order->status == 0){
											$orderStatus = 'Pending';
											$bgClass = 'danger';
										} else {
											$orderStatus = 'Proceed';
											$bgClass = 'success';
										}
								?>
								<tr class="td-line-height">
									<td class="order_time"><?php echo formatDateTime($order->created, true); ?></td>
									<td><?php echo $order->order_id; ?></td>
									
									<td><?php echo "$".$this->cart->format_number($order->gross_amount); ?></td>
									<td>
									<a class="p-info" href="#order_<?php echo $order->order_id; ?>"  data-toggle="collapse" >
									<img src="<?php echo assets_url("front/images/detail.png")?>" ></a></td>					
									<td> 
										<div class="dropdown text-center">
											<a  id="actionDropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
												<img src="<?php echo assets_url("front/images/cog.png")?>" >
											</a>
											<div class="dropdown-menu order-list-dropDown" aria-labelledby="actionDropdownMenuButton">
												<a href="<?php echo base_url('buyer/getInvoice/'.$order->order_id)?>" class="actionDropDownItems" role="button">Get Invoice</a>
											</div>
										</div>
									</td>
								</tr>
								<tr class="collapse" id="order_<?php echo $order->order_id; ?>">
									<td colspan="6" class="no-padding">
										<div class="list-group row">

										<?php
										$system_date = date_create(date('Y-m-d h:i:s A'));
										foreach($order->orders as $item) { 
											static $count = 0;
											if($item->product_image != ""){
												$image = ($item->is_local == "0")?$item->product_image:product_thumb_path().$item->product_image;
											}
											else{
												$image =  assets_url('front/images/Preview.png');
											}
										?>
										<div class="card mb-1">
											<div class="card-header bg-body">
												<div class="row">									
													<div class="col">
														<h6><strong><?php echo $this->lang->line('unit_price');?>:</strong> <?php echo "$".$this->cart->format_number($item->price); ?></h6>
													</div>
													<div class="col">
														<h6><strong><?php echo $this->lang->line('quantity');?>:</strong> <?php echo $item->qty; ?></h6>
													</div>
													<div class="col">
														<h6><strong><?php echo $this->lang->line('tax');?>:</strong> <?php echo "$".$this->cart->format_number($item->tax_amount); ?></h6>
													</div>
													<div class="col">
														<h6><strong><?php echo $this->lang->line('shipping');?>:</strong> <?php echo "$".$this->cart->format_number($item->item_shipping_amount); ?></h6>
													</div>
													<div class="col">
														<h6><strong><?php echo $this->lang->line('total');?>:</strong> <?php echo "$".$this->cart->format_number($item->item_gross_amount); ?></h6>
													</div>
												</div> 
											</div>
											<div class="card-body order_card-body"> 
												<div class="row">
													<div class="col-2 order_list_image_div">
														<img src="<?php echo  $image; ?>" alt="" class="img-fluid">
													</div>
													<div class="col-3">
													<span class="text-capitalize"><strong><?php echo (strlen($item->product_name) > 24)?substr($item->product_name,0,24)."...":$item->product_name; ?></strong></span><br/>
														<span class="text-capitalize"><?php echo stripslashes($item->store_name); ?></span><br />
														<span class="text-capitalize"><?php echo $item->condition_title;?></span>
													</div>	

													<div class="col-3 col-md-4">
														<strong>Shipping Status: </strong><span id="status-<?php echo $order->order_id ?>">
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
															echo  $status; 
														?></span><br />
														<strong>Shipping To: </strong><?php echo $order->shipping['name']?><br/>
														<?php echo $order->shipping['address_1'] ?><br/>
														<?php echo $order->shipping['city'] ?><br/>
													</div>
													<?php $encode_data = $item->product_name.'-zabeeBreaker-'.$item->order_id.'-zabeeBreaker-'.$item->product_id.'-zabeeBreaker-'.$item->product_vid.'-zabeeBreaker-'.$item->sp_id.'-zabeeBreaker-'.$item->seller_id;
													$url_val=base64_encode(urlencode($encode_data)); ?>
													<div class="col-4 col-md-3">
														<div class="row mb-1">
															<?php if($item->review_id !== NULL) { ?>
																<!-- <div class='rateYo' data-rateyo-rating='<?php echo $item->rating;?>'></div> -->
																<a class="btn contactuser" data-toggle="modal" data-encode="<?php echo $url_val; ?>" href="#Modal"><i class="far fa-comment"></i> Show Review</a></div>
																<div class="row mb-1">
															<?php } else if($item->order_status == 1){ ?>
																<a class="btn contactuser mb-1" href="<?php echo base_url('buyer/review/'.$url_val)?>"><i class="fas fa-comment-dots"></i>&nbsp;Write a Review</a>	
															<?php  } ?>
															<?php if($item->order_status == 0 && $item->cancellation_pending == "0") { 
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
																<button type="button" class="btn btn-dark cancel-order" disabled>&nbsp;Cancellation Request Pending</button>
															<?php } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "1") { ?>
																<button type="button" class="btn btn-success cancel-order" disabled>&nbsp;<?php echo $this->lang->line('approved_cancel');?></button>
															<?php } else if($item->cancellation_pending == "1" &&  $item->is_cancel == "2") { ?>
																<button type="button" class="btn btn-danger cancel-order" disabled>&nbsp;<?php echo $this->lang->line('declined_cancel');?></button>
															<?php }	else if($item->order_status == 3 ) { ?>	
																<button type="button" class="btn cancel-order" id="refund-btn" disabled>&nbsp;Order Refunded</button>
															<?php } else if($item->order_status != 1 ) { ?>	
																<button type="button" class="btn btn-default cancel-order" disabled>&nbsp;<?php echo $this->lang->line('declined');?></button>
															<?php }
															$count++; ?>
														</div>
														<div class="row">
															<button type="button" data-pv_id="<?php echo $item->product_vid;?>" data-sp_id="<?php echo $item->sp_id;?>" data-seller_id="<?php echo $item->seller_id;?>" data-store_name="<?php echo $item->store_name;?>" class="btn contactuser contact-seller"><i class="fa fa-user"></i><?php echo $this->lang->line('contact_seller');?></button>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php } ?>
									</td>
								</tr>	 
							</div>
							<?php }}else{ ?>
								<tr><td colspan="6" class="text-center"><strong><?php echo $this->lang->line('no_order');?>.</strong></td></tr>
							<?php }?>	
							</thead>
						</table>
					</div>
					<?php
							if(($links["links"])){
						?>
						<div class="clearfix"></div>
						<div class="pagination-div">
							<ul class="pagination pull-right mt-5">
								<?php foreach($links['links'] as $page){ 
									echo $page;
									} ?>
							</ul>
						</div>
						<?php
						}
						?>
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
			<h4>Order Cancellation</h4>
		  	<button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
		<div class="modal-body">
			<p>Are you sure you want to cancel this order?</p>
		</div>
        <div class="modal-footer">
			<input type="hidden" id="cancel_order_id" />
			<input type="hidden" id="td_id" />
			<input type="hidden" id="seller_id" />
			<input type="hidden" id="product_name" />
			<input type="hidden" id="button_id" />
			<a href="" class="btn btn-success" id="confirm_item" data-dismiss="modal"><?php echo $this->lang->line('yes');?></a>
			<a href="" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
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
		<div class="modal-body review-modal">
			<div class="row pb-1">
				<div class="col-2">
					<strong>Rating: </strong>
				</div>
				<div class="col-10">
					<div class='rateYo pl-0' data-rateyo-rating="0"></div>
				</div>
			</div>
			<div class="row pb-1">
				<div class="col-2">
					<strong>Review: </strong>
				</div>
				<div class="col-10">
					<span id="reviewText"></span><br>
				</div>
			</div>
			<div class="row pb-1">
			<div class="col-2">
				<strong>Media: </strong>
			</div>
			<div class="col-10">
				<span class="pictures"></span>

				<hr class="d-none full-image-line">
				<div class="tab-pane mb-1 position-relative col-12 col-md-4 d-none original-img">
					<button id="close-img" class="position-absolute close small cross-btn d-block" style="right:0" href="javascript:void(0)"><span>x</span></button>
					<img class="p-4 img-fluid">
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
		</div>
	</div>
	</div>
</div>
<!-- end -->
