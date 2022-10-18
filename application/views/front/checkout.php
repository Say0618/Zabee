<section id="do_action">
	<div class="container">
		<div class="breadcrumbs">
			<ol class="breadcrumb">
				<li><a href="<?php echo base_url(); ?>">Home</a></li>
				<li>/</li>
				<li class="active">Check out</li>
			</ol>
		</div><!--/breadcrums-->
		<form name="checkout" id="checkout" method="post" action="<?php echo base_url('checkout/addressform'); ?>">
		<div class="shopper-informations">
			<div class="row">
				<div class="col-sm-4">
					<div class="bill-to">
						<p>Bill To</p>
						<div class="form-group">
							<input type="text" class="form-control" name="billing_full_name" id="billing_full_name" placeholder="Full name" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="billing_phone" id="billing_phone" placeholder="Phone" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="billing_address_1" id="billing_address_1" placeholder="Address 1" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="billing_address_2" id="billing_address_2" placeholder="Address 2" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="billing_city" id="billing_city" placeholder="City" />
						</div>	
						<div class="form-group">
							<input type="text" class="form-control" name="billing_state" id="billing_state" placeholder="State/Province/Region" />
						</div>	
						<div class="form-group">
							<input type="text" class="form-control" name="billing_zipcode" id="billing_zipcode" placeholder="Zipcode" />
						</div>	
						<div class="form-group">
							<?php
								$countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
								foreach($countryList as $country){
									$countries[$country->id] = $country->nicename;
								}
								echo form_dropdown('billing_country', $countries, (isset($_POST['billing_country'])?$_POST['billing_country']:''), 'id="billing_country" class="form-control" style="width:100%"');
							?>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="bill-to">
						<p>Ship To</p>
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_full_name" id="shipping_full_name" placeholder="Full name" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_phone" id="shipping_phone" placeholder="Phone" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_address_1" id="shipping_address_1" placeholder="Address 1" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_address_2" id="shipping_address_2" placeholder="Address 2" />
						</div>
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_city" id="shipping_city" placeholder="City" />
						</div>	
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_state" id="shipping_state" placeholder="State/Province/Region" />
						</div>	
						<div class="form-group">
							<input type="text" class="form-control" name="shipping_zipcode" id="shipping_zipcode" placeholder="Zipcode" />
						</div>	
						<div class="form-group">
							<?php
								$countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
								foreach($countryList as $country){
									$countries[$country->id] = $country->nicename;
								}
								echo form_dropdown('shipping_country', $countries, (isset($_POST['shipping_country'])?$_POST['shipping_country']:''), 'id="shipping_country" class="form-control"');
							?>
						</div>
					</div>
				</div>
				<div class="col-sm-4 clearfix">
					<div class="order-message">
						<p>Shipping Order</p>
						<textarea name="message"  placeholder="Notes about your order, Special Notes for Delivery" rows="16"></textarea>
						<label><input type="checkbox" name="same_shipping" id="same_shipping" /> Shipping to bill address</label>
					</div>	
				</div>
				<div class="col-sm-12">
					<?php echo form_submit('', 'Proceed to Payment', 'class="btn btn-default"'); ?>					
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	</section> <!--/#cart_items-->
