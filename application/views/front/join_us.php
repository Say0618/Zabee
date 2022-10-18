<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://use.fontawesome.com/36cc4fbd14.js"></script>
<style>
main{
	padding-bottom:0px !important;
}
</style>


<?php
$confirm_password=$this->session->flashdata('confirm_password');
if(!empty($confirm_password)){
?>
<div class="row">
<div class="col-md-4"></div>
<div class="alert alert-warning alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $this->lang->line('warning');?>!</strong><?php echo $this->lang->line('pass_confirmpass');?>
  </div>
  <div class="col-md-4"></div>
</div>
<?php
}
	if($this->session->flashdata('alert')){ ?>
        <div class="row">
            <div class="alert alert-warning alert-dismissable fade show offset-sm-3 col-sm-6" align="center" >
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong><?php echo $this->lang->line('alert');?>! </strong><?php echo $this->session->flashdata('alert')?>
              </div>
        </div>	
<?php } ?>
<div id="join-us-bg"> 
    <?php
        $registered=$this->session->flashdata('registered');
        if(!empty($registered)){
    ?>
        <div class="row">
        <div class="col-md-4"></div>
        <div class="alert alert-success alert-dismissable fade show col-md-4" align="center" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong><?php echo $this->lang->line('success');?>!</strong> <?php echo $this->lang->line('success_register');?>
        </div>
        <div class="col-md-4"></div>
        </div>
    <?php
        }
    ?>
    <?php
        $email_exist=$this->session->flashdata('email_exist');
        if(!empty($email_exist)){
            //echo "Registration successful";
    ?>
        <div class="row">
        <div class="col-md-4"></div>
        <div class="alert alert-warning alert-dismissable fade show col-md-4" align="center" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong><?php echo $this->lang->line('alert');?>!</strong><?php echo $this->lang->line('already_registered');?>
        </div>
        <div class="col-md-4"></div>
        </div>
    <?php
        } 
    ?>
    <div class="offset-sm-3 col-sm-6">
        <div class="row join-us-box text-center">
            <div class="col-sm-12">
                <h2 class="join-us-heading"><?php echo $this->lang->line('create_account');?></h2>
            </div>
            <div class="col-sm-12 mb-3">
                <h6 class="join-us-heading2"><?php echo $this->lang->line('already_account');?> <a href="<?php echo base_url('login');?>"><?php echo $this->lang->line('log_in');?></a></h6>
            </div>          
			<div class="col-sm-12">
            	<div class="row">
                    <div class="offset-lg-2 col-lg-8 col-sm-12">
                        <div class="row mb-3 mt-3">
                            <div class="col-6">
                            <a href="#" class="btn-face" onclick="fblogin()">
                                <i class="fa fa-facebook-official"></i>
                                <span id="checkout-fb">Facebook</span>
                            </a>
                            </div>
                            <div class="col-6" id="googleLogin">
                                <a href="javascript:void(0)" class="btn-google">
                                    <img src="<?php echo assets_url("front/images/icon-google.png"); ?>" alt="GOOGLE">
                                    <span id="checkout-google">Google</span>
                                </a>
                            </div>
                        </div>
                       	<form id="join-us-form" action="<?php echo base_url('Home/registration');?>" method="post" > 
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('first_name');?>:</label>
                                <div class="input-group">
                                     <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('first_name');?>" name="first_name" value="<?php if(isset($_POST['first_name'])) echo $first_name;?>"type="text" required />
                                    <div class="input-group-append">
                                        <i class="fas fa-star-of-life"></i>
                                    </div>
                                </div>
                                <div class="errorTxt"></div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('middle_name');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('middle_name');?>" name="middle_name"  value="<?php if(isset($_POST['middle_name'])) echo $middle_name;?>" type="text" >
                                    <div class="input-group-append">
                                        <i class="fas fa-star-of-life invisible"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('last_name');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('last_name');?>" name="last_name"  value="<?php if(isset($_POST['last_name'])) echo $last_name;?>" type="text" required>
                                    <div class="input-group-append">
                                        <i class="fas fa-star-of-life"></i>
                                    </div>
                                </div>
                                <div class="errorTxt"></div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('email');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('email_address');?>" name="email"  type="email" required  value="<?php if(isset($_POST['email'])) echo $email;?>">
                                    <div class="input-group-append">
                                        <i class="fas fa-star-of-life"></i>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('password');?>:</label>
                                <div class="input-group" id="show_hide_password">
                                    <input class="form-control input-lg" id="password" placeholder="<?php echo $this->lang->line('password');?> (eg:shop1?)" name="password"  value="" type="password" required >
                                    <div class="input-group-append">
                                        <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('confirm_password');?>:</label>
                                <div class="input-group" id="show_hide_confirm_password">
                                    <input class="form-control input-lg" id="confirm_password" placeholder="<?php echo $this->lang->line('confirm_password');?>" name="confirm_password"  value="" type="password" required>
                                    <div class="input-group-append">
                                        <i class="fas fa-star-of-life"></i>
                                    </div>
                                </div>
                             
                            </div>
                            <div class="form-group">
                                <div align="center">
                                    <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('recaptcha_site_key')?>"></div>
                                    <span class="recaptcha_error error"></span>
                                </div>
                                <div class="row custom-control custom-checkbox mb-3 mt-3">
                                	<input type="checkbox" class="custom-control-input" id="customCheck" name="terms">
                                	<label class="custom-control-label custom-checkout-label text-light-grey" for="customCheck"><?php echo $this->lang->line('confirm_term');?><a class="text-color" href="<?php echo base_url("termsandcondition")?>"> <?php echo $this->lang->line('read_terms');?></a></label>
                                </div>
                            </div>
                            <div class="row form-group text-center">
                            
                                <input type="hidden" value="<?php echo (isset($_GET['cw']) && $_GET['cw'] ==1)?$_GET['cw']:0?>" name="cw" >
                                <input type="hidden" value="<?php echo (isset($_GET['w']) && $_GET['w'] ==1)?$_GET['w']:0?>" name="w" >
								<input type="hidden" value="<?php echo (isset($_GET['ref']) && $_GET['ref'] != "")?$_GET['ref']:""?>" name="ref_code" >
                                <input type="hidden" value="<?php echo (isset($_GET['type']) && $_GET['type'] != "")?$_GET['type']:""?>" name="type" >
								<button class="btn" id="join-us-btn" type="submit"><?php echo $this->lang->line('continue');?></button>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  