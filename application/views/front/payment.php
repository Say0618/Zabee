<div class="container">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>$this->lang->line('payment')),array("url"=>base_url("checkout"),"cat_name"=>$this->lang->line('checkout')))));?>
	<?php $this->load->view("front/progressbar",array("shipping"=>"completed","payment"=>"active","confirmation"=>""));?>
	<?php if(isset($_GET['status']) && $_GET['status'] == 'success'){echo '<div class="alert alert-success" role="alert">Card successfully deleted <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
	<div class="row">
		<div class="offset-md-3 col-md-6 col-12">
			<h2 class="text-center eycn-heading"><?php echo $this->lang->line('enter_card');?></h2>

			<div id="smart-button-container">
				<div style="text-align: center;">
					<div id="paypal-button-container">
						<button class="form-control payType mt-4 mb-4" value="paypal" id="paypal">
							<span class="paypal-logo">
								<i>Pay</i><i>Pal</i>
							</span>
						</button>
						<button class="form-control payType mb-4" data-show="false" value="card" id="card">Debit or Credit Card</button>
					</div>
				</div>
			</div>

			<!-- <input type="button" class="form-control payType" value="paypal" id="payType_paypal" name="payType">
			<input type="button" class="form-control payType mb-3" value="card" id="payType_card" name="payType"> -->

			<?php
			if($payment_error){
				echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><strong>Error!</strong> '.$payment_error_message.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
			}
			$currentYear = date("Y"); 
			$attributes = array('id' => 'order_form', "class" => "", 'name'=>'submitOrderForm');
			echo form_open('checkout/payment?add_card=1',$attributes);
			?>
			<input type="hidden" name="orderID" id="orderID" value="<?php echo $orderID; ?>" />
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<label class="form-control-label resposive-label payment-label" for="card_name"><?php echo $this->lang->line('card_no');?>:</label>
				</div>
				<div class="col-xs-12 col-sm-9">
				<input type="text" class="form-control cardNumber cardField required rounded-0" value="<?php echo set_value('card_number'); ?>" placeholder="xxxx - xxxx - xxxx - xxxx" id="card_number" name="card_number">
				<?php echo form_error('card_number'); ?>
				</div>
				<span for="card_number" class="error"></span>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('expiry_year');?>:</label>
				</div>
				<div class="col-3">
					<select name="exp_date" value="<?php echo set_value('exp_date'); ?>" id="exp_date" class="form-control cardExpiryDate cardField required rounded-0" alt="Expiry month">
						<?php
							$month2 = date("m");
							for($i = 0; $i < 12; $i++){
								$month = date("m",strtotime("+".$i." Month"));
								$monthAlp = date("M",strtotime("+".$i." Month"));
							?>
							<option <?php if($user['exp_month'] == $month){echo 'selected="selected" ';} if($month < $month2){echo 'disabled ';} ?>value="<?php echo $month; ?>"><?php echo $monthAlp; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-3 col-4">
					<select name="exp_year" value="<?php echo set_value('exp_year'); ?>" id="exp_year" class="form-control cardExpiry cardField required rounded-0" alt="Expiry year">
						<?php
							for($i = 0; $i < 11; $i++){
								$year = date("Y",strtotime("+".$i." Year"));
							?>
							<option <?php if($user['exp_year'] == $year){echo 'selected="selected" ';} ?>value="<?php echo $year; ?>"><?php echo $year; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('cvv');?>:</label>
				</div>
				<div class="col-xs-12 col-sm-9">
					<input type="number" class="form-control rounded-0  cardCCV cardField required" placeholder="xxx" id="ccv" name="ccv" value="<?php echo set_value('ccv'); ?>" /><span class="ccv_icon"></span>
					<?php echo form_error('ccv'); ?>
				</div>
				<span for="ccv" class="error"></span>
			</div>
			<div class="row">
				<div class="col-xs-12 col-sm-3">
					<label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('name');?>:</label>
				</div>
				<div class="col-xs-12 col-sm-9">
				<input type="text" class="form-control rounded-0 cardName cardField required" placeholder="<?php echo $this->lang->line('card_name');?>" id="card_name" name="card_name" value="<?php echo set_value('card_name'); ?>" >
				<?php echo form_error('card_name'); ?>
				</div>
				<span for="card_name" id="card-name" class="error"></span>
			</div>
			<div class="row card_name_row" style="display:none">
				<div class="col-xs-12 col-sm-3">
					<label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('name_card');?>:</label>
				</div>
				<div class="col-xs-12 col-sm-9">
				<input type="text" class="form-control rounded-0 cardName cardField <?php if($this->isloggedin){echo ' required';} ?>" placeholder="<?php echo $this->lang->line('namee_card');?>" id="custom_card_name" name="custom_card_name" value="<?php echo set_value('custom_card_name'); ?>" <?php if(!$this->isloggedin){echo ' readonly '; } ?>>
				<?php echo form_error('custom_card_name'); ?>
				</div>
				<span for="custom_card_name" id="custom-card-name" class="error"></span>
			</div>
			<div class="row">
				<div class="col-3">
					<label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('address');?>:</label>
				</div>
				<div class="col-xs-12 col-sm-9">
					<select name="billing_address" class="form-control cardName cardField rounded-0" id="billing_address">
						<option value="">-<?php echo $this->lang->line('select_billing');?>-</option>
						<?php foreach($location as $address){ 
							$state = (is_numeric($address->state))?getCountryNameByKeyValue('id', $address->state, 'state', true,'tbl_states'):$address->state;
							$city = $address->city;
							$country = getCountryNameByKeyValue('id', $address->country, 'nicename', true);
							$address_1 = $address->address_1;
						?>
							<option value="<?php echo $address->id?>"><?php echo $address_1.", ".$city.", ".$state.", ".$country;?></option>
						<?php }?>
							<option value="new" class="newopt">-Click Here To Add New Address-</option>
					</select>
				</div>
			</div>
			<?php 
			if($this->isloggedin){?>
				<div class="custom-control custom-checkbox col-lg-9 offset-lg-3 col-12">
					<div>
						<input type="checkbox" class="custom-control-input" value="save_card" id="save_card" name="save_card">
						<label class="custom-control-label custom-checkout-label sortCheckBox" for="save_card">
						<p ><?php echo $this->lang->line('save_card');?></p>
					</div>
				</div>
			<?php } ?>
			<input type="hidden" name="payment_method" value="stripe"  />
			<div class="error_div offset-3"></div>
			<div class="row mb-3 mt-2">
				<div class="offset-3 col-9">
					<div class="row">
						<div class="col-6">
							<a href="<?php echo base_url('checkout/payment'); ?>" class="btn toPayment w-100 btn-hover color-blue" title="Back"><?php echo $this->lang->line('back');?></a>
						</div>
						<div class="col-6">
							<?php echo form_submit('', $this->lang->line('next'), 'class="btn toPayment w-100 btn-hover color-blue"'); ?>
							<?php echo form_close();?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
	<div class="hide">
		<form name="payWithCard" id="payWithCard" method="post" action="<?php echo base_url('checkout/paymentwithcard'); ?>" novalidate>
			<input type="hidden" name="orderID" id="orderIDCard" value="<?php echo $orderID; ?>" />
			<input type="hidden" name="card_id" id="card_id" value="" />
			<input type="hidden" name="payment_method" value="stripe"  />
		</form>
	</div>
</div>

<div class="modal fade" id="confirmation-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">Delete Card!<button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="card_id" value="">
                        <strong>Are you sure, you want to delete card <span class="card_name"></span>?</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
				<span class="error d-none">Invalid Card</span>
              <button type="button" class="btn btn-danger " id="card_delete" data-dismiss="modal">Delete</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>