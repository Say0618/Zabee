<style>
.content_confirm_payment .breadcrumb-row{
	margin:0px;
}	
</style>
<div class="container back-ground-none">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>$this->lang->line('confirm_order')),array("url"=>base_url("checkout/payment"),"cat_name"=>$this->lang->line('payment')),array("url"=>base_url("checkout"),"cat_name"=>$this->lang->line('checkout')))));?>
	<?php $this->load->view("front/progressbar",array("shipping"=>"completed","payment"=>"completed","confirmation"=>"active"));?>
	<?php
		$attributes = array('id' => 'order_form', 'name'=>'order_form');
		echo form_open('checkout/proceed_payment',$attributes);
	?>
	<input type="hidden" name="orderID" id="orderID" value="<?php echo $orderID; ?>" />
	<input type="hidden" name="paypal_transID" id="paypal_transID" value="" />
	<input type="hidden" name="paypal_payer" id="paypal_payer" value="" />
	<?php if($this->session->userdata("names") != null){ ?>
		<div class="alert alert-danger alert-dismissible fade show mt-3">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="" align="center">
				<strong><?php echo $this->lang->line('warning');?>!&nbsp <?php echo implode(',', $this->session->userdata("names")) ?></strong>.
			</div>
		</div>
	<?php }
		$this->session->set_userdata("names" ,""); 
	?>
	<div class="row">
		<div class="col-md-3 col-sm-12 pr-0 user_info">
			<div class="card mb-3 cp-border-radius">
				<div class="px-3 pt-3 pb-2">
					<div>
						<h5 class="d-inline"><?php echo $this->lang->line('shipping');?> <span class="d-none d-lg-inline-block"><?php echo $this->lang->line('info');?><span></h5><a class="ml-3 cp-title" title="Change Shipping Information" href="<?php echo ($this->isloggedin)?base_url("checkout?c=1"):base_url("checkout/guest?b=3")?>"><i class="fa fa-edit editColor"></i></a>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label pr-0 d-none d-lg-block"><?php echo $this->lang->line('name');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php echo $shipping_address['name']; ?>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label pr-0 d-none d-lg-block"><?php echo $this->lang->line('address');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php 
							$state = (is_numeric($shipping_address['state']))?getCountryNameByKeyValue('id', $shipping_address['state'], 'code', true,'tbl_states'):$shipping_address['state'];
							$city = $shipping_address['city'];
							$country = getCountryNameByKeyValue('id', $shipping_address['country'], 'nicename', true);
							$address = $shipping_address['address_1'];
							echo $address."<br />".$city.", ".$state." ".$shipping_address['zipcode']."<br/>".$country; ?>
							
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label d-none d-lg-block"><?php echo $this->lang->line('phone');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php echo $shipping_address['phone']; ?>
						</div>
					</div>
					
				</div>
			</div>
			<div class="card mb-3 cp-border-radius <?php echo ($payType == "paypal")?"d-none":""?>">
				<div class="px-3 pt-3 pb-2">
					<div>
						<h5 class="d-inline"><?php echo $this->lang->line('payment');?> <span class="d-none d-lg-inline-block"><?php echo $this->lang->line('info');?><span></h5><a class="ml-3 cp-title" title="Change Payment Information" href="<?php echo base_url("checkout/payment")?>"><i class="fa fa-edit editColor"></i></a>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label pr-0 d-none d-lg-block"><?php echo $this->lang->line('number');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php echo ccMasking($card['acct'], '*'); ?>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label d-none d-lg-block"><?php echo $this->lang->line('expiry');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php echo substr($card['expdate'],0,-4)."/".substr($card['expdate'],-4); ?>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label d-none d-lg-block"><?php echo $this->lang->line('name');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php echo $card['holder']; ?>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label d-none d-lg-block"><?php echo $this->lang->line('address');?>:</label>
						<div class="col-sm-8 col-form-label">
							<?php echo $card['address']; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9">
		<?php 
		$shipping_total = 0;
		if($this->cart->total_items()>0){
			$j = 0;
			foreach($cart as $c){
				 ?>
			<section id="cart_items" class="card cp-border-radius p-3 mb-3">
				<div class="row">
					<label class="col label-bold"> <?php echo $c['store_name']?></label>
                </div>
				<div class="row">
					<div class="col-6">
						<h6><?php echo $this->lang->line('items');?></h6>
					</div>
					<div class="col-2 d-none d-lg-block text-right">
						<h6><?php echo $this->lang->line('price');?></h6>
					</div>
					<div class="col-1 text-right d-none d-lg-block p-0">
						<h6><?php echo $this->lang->line('qty');?></h6>
					</div>
					<div class="col-1 text-right d-none d-lg-block p-0">
						<h6><?php echo $this->lang->line('tax');?></h6>
					</div>
					<div class="col-2 d-none d-lg-block text-center">
						<h6><?php echo $this->lang->line('total');?></h6>
					</div>
				</div>
				<hr class="mt-0">
				<?php
					$i = 0;
					foreach ($c as $items):
						if(is_array($items)){
							$shipping_total = $shipping_total+$items['shipping_price'];
							$img = $items['img'];
							$individual_tax  = 0;
							if($items['is_local'] == 1){
								$img = product_thumb_path($img);
							}
				?>
							<div class="row cart<?php echo $items['rowid']?>" id="">
								<div class="col-lg-1 col-3 text-center">
									<img src="<?php echo $img; ?>" alt='<?php echo $items['name'].' $'.$this->cart->format_number($items['price']); ?>' class="img-fluid">
								</div>
								<div class="col-lg-5 col-9">
									<h6 class="cp-title m-0"><?php echo snippetwop($items['name'],'55','...')?></h6>
									<div class="product-description">
										<p><?php echo "Condition: <strong>".$items['condition']."</strong><br />"?>
										<?php if(isset($items['sp_description']) && $items['sp_description'] != ""){ echo "Note: <strong>".$items['sp_description']."</strong> <br />"; } ?>
										<?php if ($this->cart->has_options($items['rowid']) == TRUE): 
											if($this->cart->product_options($items['rowid'])){
										?>
											<?php } endif; ?>

											<?php 
											foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value): ?>
											<?php echo $option_name; ?>: <strong><?php echo $option_value; ?></strong> <br />
											<?php endforeach; 
											if($items['shipping_price'] > 0){
												$shipping_price = "US $".$items['shipping_price'].' via '.$items['shipping_title'];
											}else{ 	
												$shipping_price = "Free"; 
											}?>
											Shipping Cost: <strong><span id="shipping_method<?php echo $j;?>" data-ship="<?php echo $items['shipping_id']?>" data-pv_id="<?php echo $items['id'];?>" data-row_id="<?php echo $items['rowid'];?>" class="col-form-label shipping_link"><a href="javascript:void(0)" class="stylelink" checked="checked" onClick="openModal('<?php echo $j?>','<?php echo $items['shipping_id']?>')"><strong><span class="shippingTitle"><?php echo $items['shipping_title']?></span></strong></a></span></strong> 
											<?php if(isset($items['update_msg']) && $items['update_msg'] != ""){ ?>
												<br><strong><span class="text-warning"><?php echo $items['update_msg']; ?></span></strong>
											<?php } ?>
										</p>
									</div>
								</div>
								<div class="col-2 d-none d-lg-block text-right">$<span class="currency" id="sub_total<?php echo $items['rowid'];?>"><?php echo $this->cart->format_number($items['price']); ?></span></div>
								<?php
									$browser = (isset($_COOKIE['browser']))?$_COOKIE['browser']:1;
								?>
								<div class="col-1 d-none d-lg-block text-right pr-0">
									<input type="<?php echo ($browser != 3)?'number':'text'?>" name="<?php echo "qty".$i."[]" ?>" data-index ="<?php echo $j?>" data-row = "<?php echo $items['rowid'] ?>" value = "<?php echo ($items['qty'] > $items['max_qty'] ) ? $items['max_qty'] : $items['qty'] ?>" min = "1" max = "<?php echo $items['max_qty'] ?>" size = "5" class = "item_qty text-right" id="<?php echo "item_qty".$j ?>"> <br />
                        			<span class="<?php echo $i."_pops" ?>" style="font-size: 12px; color: red; display:none" >Max quantity approached</span>	<?php //echo $items['qty']; ?>
								</div>
								<div class="col-1 d-none d-lg-block text-right pr-0">$<span id="tax_<?php echo $items['rowid'] ?>"><?php echo $items['tax'];?></span></div>
								<?php $individual_total = $items['tax']+$items['subtotal']; $tax +=$items['tax']; ?>
								<div class="col-2 d-none d-lg-block text-center">$<span class="currency" id="subtotal<?php echo $items['rowid'];?>"><?php echo $this->cart->format_number($individual_total); ?></span></div>
							</div>
							<hr class="mt-0"/>
							<?php $i++; $j++; ?>
				<?php }endforeach; ?>
					<div class="row px-3">
						<span class="w-75 text-right forgot-label"><?php echo $this->lang->line('subtotal_title');?></span>
						<!-- <div class="totals-value product-line-price" id="cart-total"><?php //echo $this->cart->format_number(($this->cart->total()+$shipping_total+$this->config->item('vat_tax'))); ?></div> -->
						<div class="w-25 text-right forgot-label inv-total" id="inv_subtotal<?php echo $j - 1; ?>">$<?php echo $this->cart->format_number($c['subtotal'] + $c['total_tax']); ?></div>
						
					</div>
				</section> <!--/#cart_items-->
				<?php } ?>
				<section class="card cp-border-radius p-3 mb-3">
					<div class="col-12 cash-totals mb-3">
						<div class="row">
						<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
							<?php if(!$hasDiscount): ?>
							<div class="row coupon_div">
								<div class="col-12 forgot-label text-right"><span class="coupon_model" data-toggle="modal" data-target="#coupon_modal"><?php echo $this->lang->line('have_coupon');?></span></div>
							</div>
							<?php endif; ?>
							<div class="row">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('subtotal_title');?>:</div>
								<div class="col-6 forgot-label text-right sub-total">$<?php echo $this->cart->format_number($this->cart->total()); ?></div>
							</div>
							<?php $couponRowShow = ' d-none'; if($hasDiscount):$couponRowShow = '';endif; ?>
							<div class="row coupon_row<?php echo $couponRowShow; ?>">
								<div class="col-6 resposive-label coupon-label forgot-label"><span><?php echo $this->lang->line('coupon');?>:</span><br /><small class="coupon_model" data-toggle="modal" data-target="#coupon_modal"><?php echo $this->lang->line('use_another');?></small></div>
								<div class="col-6 forgot-label text-right coupon_amount">($<?php echo ($hasDiscount)?$this->cart->format_number($discount['discount_amount']):'0'; ?>)</div>
							</div>
							<div class="row">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('shipping_total');?>:</div>
								<div class="col-6 forgot-label text-right ship-total">$<span><?php echo $this->cart->format_number($shipping_total); ?></span></div>
							</div>
						</div>
						</div>
						<div class="row">
							<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
								<div class="row cp-hr">
									<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('tax');?>:</div>
									<div class="col-6 forgot-label text-right tax">$<span><?php echo $this->cart->format_number($tax); ?></span></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
								<div class="row cp-dhr">
									<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('order_tot');?>:</div>
									<?php 
									$grand_total = $this->cart->total()+$tax+$shipping_total;
									if($hasDiscount):
										$grand_total = $grand_total - $discount['discount_amount'];
									endif;
									?>
									<div class="col-6 forgot-label text-right" id="grand_total">$<?php echo $this->cart->format_number($grand_total); ?></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4" id="confirmBtn">
							<div class="row">
								<div class="col-6">
									<a href="<?php echo base_url('checkout/payment'); ?>" class="btn cp-bte btn-hover color-green backToCart" title="Back"><?php echo $this->lang->line('back_to_edit');?></a>
								</div>
								<div class="col-6 text-right">
									<?php echo ($payType != "paypal") ? form_submit('', $this->lang->line('confirm'), 'class="submitOrderForm btn cp-c btn-hover color-blue"'): '<div id="paypal-button-container"><div id="paypal-button"></div></div>'; ?>	
								</div>
							</div>
						</div>
						<div class="col d-none" id="imgLoad">
							<div class="col-md-1 col-3 float-right">
								<img src="<?php echo assets_url("front/images/loading-main1.gif")?>" class="img-thumbnail">
							</div>
						</div>
					</div>
				</section>
			<?php }else { ?>
				<div><?php echo $this->lang->line('cart_empty');?>y</div>
			<?php } ?>
		</div>
	</div>
</div>
<!-- modal starts -->
<div id="choose_shipping_method" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title">Choose Shipping Method</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
                	<thead>
						<th>Shipping Company</th>
						<!-- <th>Shipping Cost</th> -->
                        <th>Estimated Delivery Time</th>
                        <!-- <th>Tracking Information</th> -->
                        <th>Description</th>
                    </thead>
                    <tbody id="shipping_tbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="indexId" />
            	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 

<!-- Coupon modal starts -->
<div id="coupon_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title">Enter Coupon</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
            	<div class="input-group mb-3">
					<input type="text" class="form-control" name="voucher_code" id="voucher_code" placeholder="Voucher" aria-label="Voucher" aria-describedby="basic-addon2">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary" type="button" id="apply_voucher">Apply</button>
					</div>
				</div>
				<label class="error error_coupon"></label>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="indexId" />
            	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> 