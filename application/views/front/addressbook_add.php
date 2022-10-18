<style>
.intl-tel-input .selected-flag{
    padding: 0 6px 0 8px !important;
}
</style>
<link rel="stylesheet" href="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.css'); ?>">
<div class="container">
    <?php 
        $get = (isset($_GET['b']) && $_GET['b'] !="")?"?b=".$_GET['b']:"";
        if($checkerForCheckout == 3 && $get){
            $path = base_url('home/guestAddress/'.$get);
        }else if($checkerForCheckout == 3){
            $path = base_url('home/guest_registration/');
        }
        else{
            $path = base_url('buyer/address_book_add/'.$checkerForCheckout.'/'.$location_id.$get);
        }
        $btn= (isset($locations->id))?$this->lang->line('update'):$this->lang->line('next')
    ?>
    <?php if($checkerForCheckout == "2"){
        $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>$this->lang->line("shipping")." ".$this->lang->line('address')),array("url"=>base_url("checkout"),"cat_name"=>$this->lang->line('checkout')))));
    }else{
        $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>$this->lang->line("shipping")." ".$this->lang->line('address')))));
    }?>
    <div class="row">
       <div class="col-sm-12">
            <h4 class="text-center"><?php echo $this->lang->line("shipping")?> <?php echo $this->lang->line('address');?>:</h4>
            <form action="<?php echo $path;?>" id="addressBookAdd" name="addressBookAdd" novalidate method="post">
                <?php if($this->session->flashdata("error")){ ?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <?php echo $this->session->flashdata("error");?>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="col-12">
                        <?php if($checkerForCheckout == 3 && !isset($_GET['b'])){?>
                            <div class="row form-group">
                                <div class="col-sm-6 address-form">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('first_name');?>:</label>
                                    <input class="form-control input-lg rounded-0" placeholder="<?php echo $this->lang->line('name');?>" name="first_name" value="<?php echo (isset($locations->fullname))?$locations->fullname:""?>" type="text" required />
                                    <?php echo form_error('first_name'); ?>
                                    <span class="error"></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('last_name');?>:</label>
                                    <input class="form-control input-lg rounded-0" placeholder="<?php echo $this->lang->line('name');?>" name="last_name" value="<?php echo (isset($locations->fullname))?$locations->fullname:""?>" type="text" required />
                                    <?php echo form_error('last_name'); ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-6 address-form">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('email');?>:</label>
                                    <input class="form-control input-lg rounded-0" placeholder="<?php echo $this->lang->line('email');?>" name="email" value="<?php echo (isset($locations->email))?$locations->email:""?>" type="email" required />
                                    <?php echo form_error('email'); ?>
                                    <span class="error"></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('cell_no');?>:</label>
                                    <input  type= "tel" class="form-control input-box rounded-0 contact" id="contact" name="contact" value="<?php echo (isset($locations->contact))?$locations->contact:""?>" maxlength="20"/>
                                    <?php echo form_error('contact'); ?>	 
                                    <span class="error"></span>
                                </div>
                            </div>
                        <?php }else{?>
                            <div class="row form-group">
                                <div class="col-sm-6 address-form">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('name');?>:</label>
                                    <input class="form-control input-box input-lg rounded-0" placeholder="<?php echo $this->lang->line('name');?>" name="fullname" value="<?php echo (isset($locations->fullname))?$locations->fullname:""?>" type="text" required />
                                    <?php echo form_error('fullname'); ?>
                                    <span class="error"></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('cell_no');?>:</label>
                                    <input  type= "tel" class="form-control input-box rounded-0 contact" id="contact" name="contact" value="<?php echo (isset($locations->contact))?$locations->contact:""?>" maxlength="20"/>
                                    <?php echo form_error('contact'); ?>	 
                                    <span class="error"></span>
                                </div>
                            </div>
                        <?php }?>
                            <div class="row form-group">
                                <div class="col-sm-6 address-form">
                                    <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('address');?>:</label>
                                    <input type= "text" class="form-control input-box rounded-0 address" id="" name="address1" value="<?php echo (isset($locations->address_1))?$locations->address_1:""?>" required/>
                                    <?php echo form_error('address1'); ?>

                                    <span class="error"></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('address2');?>(optional):</label>
                                    <input type= "text" class="form-control input-box rounded-0 address" id="" name="address2" value="<?php echo (isset($locations->address_2))?$locations->address_2:""?>"/>
                                    <?php echo form_error('address2'); ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-6 address-form <?php echo (isset($locations))?(($locations->country != 226) ? "d-none" : "d-block"):""; ?>" id="state">
                                    <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('state');?>:</label>
                                    <?php
                                        $states = array();
                                        $states[0] = "- Select State -";
                                        $selected ="";  
                                        foreach($statesList as $state){
                                            $states[$state->id] = $state->state;
                                        }
                                        echo form_dropdown('state', $states, (isset($locations->state)?$locations->state:''), 'id="state" class="form-control rounded-0 input-box" style=""');
                                    ?> 
                                </div>
                                <div class="col-sm-6 <?php echo isset($locations)?(($locations->country == 226) ? "d-none" : "d-block"):"d-none"; ?>" id="province">
                                    <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('province');?>:</label>
                                    <input  type= "text" class="form-control input-box rounded-0 address" id="province" name="province" value="<?php echo (isset($locations) && $locations->country != 226)?$locations->state:'' ?>"/>
                                    <span class="error"></span>
                                </div>
                                <div class="col-sm-6">
                                <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('country');?>:</label>
                                    <?php
                                        $countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
                                        foreach($countryList as $country){
                                        $countries[$country->id] = $country->nicename;
                                        }
                                        echo form_dropdown('country', $countries, (isset($locations->country)?$locations->country:''), 'id="country" class="form-control rounded-0" style=""');
                                    ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-6 address-form">
                                    <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('city');?>:</label>
                                    <input  type= "text" class="form-control input-box rounded-0 city" id="" name="city" value="<?php echo (isset($locations->city))?$locations->city:""?>"/>
                                    <?php echo form_error('city'); ?>

                                    <span class="error"></span>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('zip');?>:</label>
                                    <input  type= "text" class="form-control input-box rounded-0 zip" id="" name="zip" value="<?php echo (isset($locations->zip))?$locations->zip:""?>"/>
                                    <?php echo form_error('zip'); ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php if($checkerForCheckout == 3 && !isset($_GET['b'])){?>
                                <div class="row form-group">
                                    <div class="col-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1" name="SignIn">
                                            <label class="custom-control-label custom-checkout-label text-light-grey" for="customCheck1"><?php echo $this->lang->line('create_saveinfo');?></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group d-none" id="passDiv">
                                    <div class="col-sm-6">
                                        <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('password');?>:</label>
                                        <input  type= "password" class="form-control input-box rounded-0" id="password" name="password" value=""/>
                                        
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('confirm_password');?>:</label>
                                        <input  type= "password" class="form-control input-box rounded-0" id="confirm_password" name="confirm_password" value=""/>
                                    </div>
                                </div>
                            <?php }?>
                            <div class="row form-group text-right">
                                <div class="col text-right">
                                    <input type="hidden" value="<?php echo (isset($_SERVER['HTTP_REFERER'])) ?$_SERVER['HTTP_REFERER'] : ''; ?>" name="redirectUrl"/>
                                    <input type="hidden" value="<?php echo $checkerForCheckout; ?>" name="checkerForCheckout"/>
                                    <input type="hidden" value="<?php echo (isset($locations->id))?$locations->id:""?>" name="id"/>
                                    <a href="<?php echo (isset($_SERVER['HTTP_REFERER'])) ?$_SERVER['HTTP_REFERER'] : base_url("checkout"); ?>" class="btn mr-3 sans-bold btn-hover color-orange"><?php echo $this->lang->line('cancel');?></a>
                                    <button id="onepage-guest-register-button" type="submit" class="btn address_savebtn btn-hover color-green"><?php echo $btn ?></button>
                                </div>
                            </div>
                    </div>
                </div> 
            </form>
		</div>
	</div>
</div>
