<script src="https://use.fontawesome.com/36cc4fbd14.js"></script>
<style>
main{
	padding-bottom:0px !important;
}
</style>
<div id="join-us-bg"> 
		<h6 class="join-us-heading"> 
			<?php
				$activateAccpunt = $this->session->flashdata('already_registered');
				 if(!empty($activateAccpunt)){
            ?>
                <div class="row">
                    <div class="alert alert-warning alert-dismissable fade show offset-sm-3 col-sm-6" align="center" >
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong><?php echo $this->lang->line('alert');?>! </strong><?php echo $this->lang->line('not_registered');?>
                      </div>
                </div>
				<?php
				} 
				if($this->session->flashdata('alert')){ ?>
					<div class="row">
                        <div class="alert alert-warning alert-dismissable fade show offset-sm-3 col-sm-6" align="center" >
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Alert! </strong><?php echo $this->session->flashdata('alert')?>
                          </div>
                    </div>	
		        <?php } ?>
		</h6> 
    <div class="offset-sm-3 col-sm-6">
		<div class="row join-us-box text-center">
            <div class="col-sm-12">
                <h2 class="join-us-heading"><?php echo $this->lang->line('login');?></h2>
            </div>
            <div class="col-sm-12">
                <h6 class="join-us-heading2"><?php echo $this->lang->line('dont_have_account');?> <a href="<?php echo base_url('join_us');?>"><?php echo $this->lang->line('sign_up');?></a></h6>
            </div>
            <div class="col-sm-12">
                <div class="row">
                    <div class="offset-sm-2 col-sm-8">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-3 mt-3">
                            <a href="#" class="btn-face py-2" onclick="fblogin()">
                                <i class="fa fa-facebook-official"></i>
                                <span id="checkout-fb">Facebook</span>
                            </a>
                            </div>
                            <div class="col-md-6 col-sm-12 mb-3 mt-3" id="googleLogin">
                                <a href="javascript:void(0)" class="btn-google py-2">
                                    <img src="<?php echo assets_url('front/images/icon-google.png')?>" alt="GOOGLE">
                                    <span id="checkout-google">Google</span>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-5">
                                <hr class="or-hr" />
                            </div>
                            <div  class="or-span col-2">
                                <?php echo $this->lang->line('or');?>
                            </div>
                            <div class="col-5">
                                <hr class="or-hr" />
                            </div>
                        </div>
                        <form id="loginForm" action="<?php echo base_url('Home/login');?>" method="post" > 
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('email');?>:</label>
                                <div class="input-group">
                                    <input class="form-control" tabindex="1" type="text" placeholder = "<?php echo $this->lang->line('email_address');?>" onfocus = "this.placeholder = ''" onblur="this.placeholder = 'Email Address'" name="user_email" id="l_user_email" value="<?php echo (isset($_POST['user_email']))?$_POST['user_email']:''; ?>" />
                                </div>
                                
                            </div>
                            
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('password');?>:</label><span class="ml-1"><u><a href="<?php echo base_url('forgotpassword');?>" class="text-light-grey"><?php echo $this->lang->line('forgot');?>?</a></u></span>
                                <div class="input-group">
                                    <input class="form-control" tabindex="2" type="password" name="user_pass" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo $this->lang->line('password');?>'" id="l_user_pass" placeholder="<?php echo $this->lang->line('password');?>"  value="" />
                                </div>
                                <?php echo form_error('user_email'); ?>
                            </div>
                            <div class="row form-group text-left">
                                <div class="custom-control custom-checkbox mb-3">
                                	<input type="checkbox" class="custom-control-input" id="customCheck1" name="SignIn">
                                	<label class="custom-control-label custom-checkout-label text-light-grey" for="customCheck1"><?php echo $this->lang->line('keep_login');?></label>
                                </div>
                            </div>
                            <div class="row form-group text-center button">
                                <input type="hidden" value="<?php echo (isset($_SERVER['HTTP_REFERER'])) ?$_SERVER['HTTP_REFERER'] : ''; ?>" name="redirectUrl"/>
                                <input type="hidden" value="<?php echo (isset($_GET['from'])) ?($_GET['from']) : ''; ?>" name="from"/>
                                <button id="join-us-btn" class="btn-hover color-8" name="loginbtn"><?php echo $this->lang->line('login');?></button>
                            </div>
                        </form>
                    </div>
                </div>
			</div>
        </div>
    </div>
</div>