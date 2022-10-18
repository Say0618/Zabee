<style>
.intl-tel-input .selected-flag{
    padding: 0 6px 0 8px !important;
}
</style>
<script src='https://www.google.com/recaptcha/api.js'></script>
<link rel="stylesheet" href="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.css'); ?>">
<div class="container">
<!-- <?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>"invite",array("url"=>base_url("checkout"),"cat_name"=>$this->lang->line('checkout'))))));?> -->
    <div class="row">
        <div class="offset-sm-2 col-sm-8">
            <?php if(!empty($_SESSION['alert'])){?>
            <div class="alert alert-warning alert-dismissable fade show" align="center" >
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong><?php echo "alert";?>!</strong> <?php echo $this->session->flashdata('alert');?>.
            </div>
            <?php }	?>
        </div>
    </div>
    <div class="row">
       <div class="col-sm-12">
            <!-- <h4 class="text-center">Invite People:</h4> -->
            <br>
            <form action="<?php echo base_url('referral/Referrals/invite_people'); ?>" id="invite-form" method="post">
                <?php if($this->session->flashdata("error")){ ?>
                    <div class="alert alert-danger alert-dismissable fade show" align="center" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo "Error! ".$this->session->flashdata("error");?>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-12">
                            <div class="row form-group">
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('first_name');?>:</label>
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('name');?>" name="first_name" type="text" required />
                                    <?php echo form_error('first_name'); ?>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('last_name');?>:</label>
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('name');?>" name="last_name" type="text" required />
                                    <?php echo form_error('last_name'); ?>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="<?php echo ($this->session->userdata('user_type') == "1")?"col-sm-6":"col-sm-12"?>">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('email');?>:</label>
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('email');?>" name="email" type="email" required />
                                    <?php echo form_error('email'); ?>
                                </div>
                            <?php if($this->session->userdata('user_type') == "1"){ ?>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label" >User Type:</label>
                                    <!-- <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('email');?>" name="email" type="email" required />-->
                                    <select class="form-control" name="user_type">
                                        <option value="seller" selected>Seller</option>
                                        <option value="buyer">Buyer</option>
                                    </select>
                                    <?php echo form_error('email'); ?>
                                </div> 
                            <?php } ?>   
                            </div>
                            <div class="row form-group">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <div align="center">
                                    <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('recaptcha_site_key')?>"></div>
                                    <span class="recaptcha_error error"></span>
                                </div>
                            </div>
                            <div class="col-4"></div>
                            </div>
                            <div class="row form-group text-right">
                                <div class="col text-right">
                                    <input type="hidden" value="<?php echo (isset($_SERVER['HTTP_REFERER'])) ?$_SERVER['HTTP_REFERER'] : ''; ?>" name="redirectUrl"/>
                                    <!-- <input type="hidden" value="<?php echo $checkerForCheckout; ?>" name="checkerForCheckout"/> -->
                                    <!-- <input type="hidden" value="<?php echo (isset($locations->id))?$locations->id:""?>" name="id"/> -->
                                    <a href="<?php echo base_url("invite"); ?>" class="btn mr-3 sans-bold btn-hover color-orange"><?php echo $this->lang->line('cancel');?></a>
                                    <button id="onepage-guest-register-button" type="submit" class="btn address_savebtn btn-hover color-green">Invite</button>
                                </div>
                            </div>
                    </div>
                </div> 
            </form>
		</div>
	</div>
</div>
