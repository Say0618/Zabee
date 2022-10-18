<?php 
	if($this->session->userdata('is_guest')){
		$user_id = $this->session->userdata('guest_id');
	}else{
		$user_id = $this->session->userdata('userid');
	}
	$email = $this->session->userdata('email');
?>
<div class="container">
	<div class="row pb-3 mt-3">
        <div class="col-lg-12 text-center">
			<p><span class="congrats-text"><?php echo $this->lang->line('order_confirm');?>.</span><br/>
			<span class="congrats-text"><?php echo $this->lang->line('order_summary');?></span></p>
        </div>
        <div class="col-md-12">
			<section id="cart_items" class="card cp-border-radius p-3">
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
					$cart_total = $this->cart->total();
					$i = 1;
					$shipping_total = 0;
					if($this->cart->total_items()>0){
					foreach ($this->cart->contents() as $items):
						$shipping_total = $shipping_total+$items['shipping_price'];
						$img = $items['img'];
						if($items['is_local'] == 1){
							$img = product_thumb_path($img);
						}
				?>
				<div class="row">
					<div class="col-lg-1 col-3 text-center">
						<img src="<?php echo $img; ?>" alt="<?php echo $items['name']; ?>" class="img-fluid">
					</div>
					<div class="col-lg-5 col-9">
						<h6 class="cp-title m-0"><?php echo snippetwop($items['name'],'55','...')?></h6>
						<div class="product-description">
							<p><?php echo "Condition: <strong>".$items['condition']."</strong><br />"?>
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
								Shipping Cost: <strong><?php echo $shipping_price;?></strong> 
							</p>
						</div>
					</div>
					<div class="col-2 d-none d-lg-block text-right"><span class="currency"></span>$<?php echo $this->cart->format_number($items['price']); ?></div>
					<div class="col-1 d-none d-lg-block text-right pr-0"><?php echo $items['qty']; ?></div>
					<div class="col-1 d-none d-lg-block text-right pr-0">$<?php echo $this->cart->format_number(getTaxs($zip_code, $items['subtotal'])) ?></div>
					<?php $individual_total = getTaxs($zip_code, $items['subtotal'])+$items['subtotal']; ?>
					<div class="col-2 d-none d-lg-block text-center">$<span class="currency"></span><?php echo $this->cart->format_number($individual_total); ?></div>
				</div>
				<hr class="mt-0"/>
				<?php $i++; ?>
				<?php endforeach; ?>
				<div class="col-12 cash-totals mb-3">
					<div class="row">
						<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
							<div class="row">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('subtotal_title');?>:</div>
								<div class="col-6 forgot-label text-right ship-total">$<?php echo $this->cart->format_number($cart_total); ?></div>
							</div>
							<?php if(isset($hasDiscount) && $hasDiscount != ""): ?>
							<div class="row">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('coupon');?>:</div>
								<div class="col-6 forgot-label text-right ship-total">($<?php echo $this->cart->format_number($discount['discount_amount']); ?>)</div>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
							<div class="row">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('shipping_total');?>:</div>
								<div class="col-6 forgot-label text-right">$<?php echo $this->cart->format_number($shipping_total); ?></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
							<div class="row cp-hr">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('tax');?>:</div>
								<div class="col-6 forgot-label text-right">$<?php echo $this->cart->format_number($tax); ?></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 offset-lg-8 col-md-6 offset-md-4">
							<div class="row cp-dhr">
								<div class="col-6 resposive-label forgot-label"><?php echo $this->lang->line('order_tot');?>:</div>
								<?php 
								$grand_total = $cart_total+$tax+$shipping_total;
								if(isset($hasDiscount) && $hasDiscount != ""){
									$grand_total = $grand_total - $discount['discount_amount'];
								}
								
								?>
								<div class="col-6 forgot-label text-right">$<?php echo $this->cart->format_number($grand_total); ?></div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
			</section>
		</div>
		<?php if((!$this->isloggedin) && $this->session->userdata('guest_register') == 0){ ?>
			<div class="col-12 mt-3">
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input" id="customCheck1" name="SignIn">
					<label class="custom-control-label custom-checkout-label text-light-grey" for="customCheck1">Enter password to save information and view order detail.</label>
				</div>
			</div>
			<div class="col-sm-6 passDiv d-none">
				<label class="form-control-label resposive-label address-label">Password:</label>
				<input type="password" class="form-control input-box rounded-0" id="password" name="password" value="">
				<span class="error" id="p_error"></span>
			</div>
			<div class="col-sm-6 passDiv d-none">
				<label class="form-control-label resposive-label address-label">Confirm Password:</label>
				<input type="password" class="form-control input-box rounded-0" id="confirm_password" name="confirm_password" value="">
				<span class="error" id="cp_error"></span>
			</div>
		<?php } ?>
		<div class="text-center col mt-3">
			<a href="javascript:void(0)" id="continueShopping" class="btn doneOrder btn-hover color-blue"><?php echo $this->lang->line('continue_shopping');?>...</a>
		</div>
	</div>
	<script>
		function passwordValidation(password,confirmPassword){
			if(password == ""){
				return {"status":0,"msg":"Please enter password."};
			}else if((password.length >= 6 && /\d/.test(password) && /[a-z]/i.test(password)) == false){
				return {"status":0,"msg":"Enter a combination of at least 6 characters and numbers."};
			}else if(confirmPassword == ""){
				return {"status":2,"msg":"Please enter same password."};
			}else if((confirmPassword.length >= 6 && /\d/.test(confirmPassword) && /[a-z]/i.test(confirmPassword)) == false){
				return {"status":2,"msg":"Enter a combination of at least 6 characters and numbers."};
			}else if(password != confirmPassword){
				return {"status":0,"msg":"Please enter the same value again."};
			}else{
				return {"status":1,"msg":"Validated."};
			}
		}
		$("#customCheck1").on("change",function(){
			if($(this).prop("checked")){
				$(".passDiv").removeClass("d-none");
			}else{
				$(".passDiv").addClass("d-none");
			}
		});
		$("#continueShopping").on("click",function(){
			var id = "<?php echo $user_id?>";
			var email = "<?php echo $email?>";
			if($("#customCheck1:checked").val()){
				var password = $("#password").val();
				var confirm_password = $("#confirm_password").val();
				var validate = passwordValidation(password,confirm_password);
				if(validate.status == 1){
					var data = [];
					data = {"password":password,"is_active":"1","is_guest":"0","user_id":id,"email":email}
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url("home/updatePassword/1");?>',
						data: data,
						dataType: 'json',
						success: function(response){
							if(response.status == 1){
								window.location.href = '<?php echo base_url(); ?>';
							}else{
								$("#p_error").text(response.msg);
								$("#cp_error").text(response.msg);
							}
						}
					});	
				}else{
					if(validate.status == 0){
						$("#p_error").text(validate.msg);
						$("#cp_error").text("");
					}else if(validate.status == 2){
						$("#cp_error").text(validate.msg);
						$("#p_error").text("");
					}
				}
			}else{
				window.location.href = '<?php echo base_url(); ?>';
			}
			return false;
		});
		
	</script>
	<?php 
        $this->cart->destroy();
		$this->session->unset_userdata('checkout_card_view');
		$this->session->unset_userdata('checkout_card');
		$this->session->unset_userdata('checkout_ship');
		$this->session->unset_userdata('confirm_page');
		$this->session->unset_userdata('order_data');
		$this->session->unset_userdata('timestamp');
		$this->Cart_Model->destroyCartContents($user_id);
		if($this->session->userdata('is_guest')){
			$this->session->unset_userdata('email');
		}
		$this->session->unset_userdata('guest_id');
		$this->session->unset_userdata('is_guest');
		$this->session->unset_userdata('guest_register');
    ?>