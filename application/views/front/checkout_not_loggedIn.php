<div class="container">
    <?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=> $this->lang->line('sign_in')),array("url"=>base_url("checkout"),"cat_name"=>$this->lang->line('checkout')))));?>
    <?php
		$attributes = array('id' => 'order_form', 'name'=>'order_form');
		echo form_open('checkout/proceed_payment',$attributes);
	?>
    <input type="hidden" name="paypal_transID" id="paypal_transID" value="" />
	<input type="hidden" name="paypal_payer" id="paypal_payer" value="" />
</div>
<div class="container">
    <?php if(isset($_GET['status']) && $_GET['status'] == 'error'){echo '<div class="alert alert-danger" role="alert"> '.$_GET['msg'].'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
    <div class="row p-3 mt-3">
    	<div class="col-md-6">
            <h5 class="sans-bold"><?php echo $this->lang->line('register_save');?></h5>
            <h5 class="sans-bold"><?php echo $this->lang->line('checkout_guest');?></h5>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" id="guest" name="checkout_method" value="guest">
                <label class="custom-control-label radio-custom-input sans-regular cag" for="guest"><?php echo $this->lang->line('checkout_as_guest');?></label>
            </div>
            <div class="custom-control custom-radio mb-2">
                <input type="radio" class="custom-control-input" id="register" name="checkout_method" value="register" checked="checked">
                <label class="custom-control-label radio-custom-input sans-regular cag" for="register"><?php echo $this->lang->line('register');?></label>
            </div> 
            <?php /*<p class="mb-2"><strong><?php echo $this->lang->line('register_save');?></strong></p>*/?>
            <p class="sans-regular mb-2"> <?php echo $this->lang->line('register_future');?><br><?php echo $this->lang->line('register_fast');?><br><?php echo $this->lang->line('register_easy');?></p>
            <button id="onepage-guest-register-button" type="button" class="btn btn-hover color-green"><?php echo $this->lang->line('continue');?></button>
            
            <div style="margin-top: 20px;">
                <!-- <style>
                    #zoid-paypal-buttons-uid_5520e2bb4f_mti6ntg6nta {
                        min-width: 97px !important;
                    }
                </style> -->
                <div id="paypal_button_as_guest"></div>
            </div>
        </div>
        <div class="col-md-6">
            <h5 class="sans-bold"><?php echo $this->lang->line('login_acc');?></h5>
            <form id="guestForm" name="guest-Form" action="<?php echo base_url('Home/login');?>" method="post" > 
                <div class="form-group">
                    <label class="form-control-label resposive-label" ><?php echo $this->lang->line('email');?>:</label>
                    <div class="input-group">
                        <input class="form-control" type="text" placeholder="<?php echo $this->lang->line('email_address');?>" name="user_email" id="l_user_email" value="<?php echo (isset($_POST['user_email']))?$_POST['user_email']:''; ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-control-label resposive-label" ><?php echo $this->lang->line('password');?>:</label>
                    <div class="input-group">
                        <input class="form-control" type="password" name="user_pass" id="l_user_pass" placeholder="******"  value="" />
                    </div>
                </div>
                <?php if(form_error('user_email')){?>
                <div class="form-group"><p><?php echo form_error('user_email'); ?></p></div>
                <?php }?>
                <div class="form-group text-left">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="customCheck" name="rememberme">
                        <label class="custom-control-label custom-checkout-label" for="customCheck"><?php echo $this->lang->line('remember_comp');?></label>
                    </div>
                </div>
                <div class="d-flex">
                    <input type="hidden" value="<?php echo (isset($_SERVER['HTTP_REFERER'])) ?$_SERVER['HTTP_REFERER'] : ''; ?>" name="redirectUrl"/>
                    <input type="hidden" value="guest" name="fromGuest"  />
                    <button id="" class="btn loginbtn btn-hover color-blue" name="loginbtn" type="submit"><?php echo $this->lang->line('login');?></button>
                    <span class="ml-3 mr-3 border-right" ></span>
                    <span id="googleLogin">
                        <a href="javascript:void(0)" class="btn-google">
                            <img src="<?php echo assets_url('front/images/icon-google.png')?>" alt="GOOGLE">
                            <span id="checkout-google">Google</span>
                        </a>
                    </span>
                    <span class="ml-3 mr-3" id="guestOr"><strong>Or</strong></span>
                    <a href="#" class="btn-face" onclick="fblogin()">
                        <i class="fab fa-facebook"></i>
                        <span id="checkout-fb">Facebook</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    // $grand_total = $this->cart->total()+$tax+$shipping_total;
    // if($hasDiscount):
    //     $grand_total = $grand_total - $discount['discount_amount'];
    // endif;
    ?>
<script>
	$("#onepage-guest-register-button").on('click',function(){
		var page = $("input[name=checkout_method]:checked").val();
		if(page == "guest"){
			window.location = "<?php echo base_url('checkout/guest')?>";
		}else{
			window.location = "<?php echo base_url('join_us?w=1')?>";
		}
		return false;
	});
</script> 