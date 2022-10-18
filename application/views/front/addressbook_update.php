<link rel="stylesheet" href="<?php echo base_url('assets/plugins/intl-tel-input/intlTelInput.css'); ?>">
<style>
.intl-tel-input .selected-flag{
    padding: 0 6px 0 8px !important;
}
</style>
<div class="container">
    <div class="row position-relative">
        <?php 
            echo '<div class="col mt-2 mb-2"><a class="breadcrumb-item" href="'.base_url().'">'.$this->lang->line('home').' </a><a class="breadcrumb-item" href="'.base_url("shipping/edit/$location_id").'">'.$this->lang->line('edit_shippingAdd').'</a></div>';
        ?>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="row mb-4">
                <h4 class=" col-4"><?php echo $this->lang->line('edit_shipping');?></h4>
            </div>
            <?php
                if(isset($_GET['checkout']) && $_GET['checkout']!=""){ ?>
                <form action="<?php echo base_url('shipping/edit/'.$location_id.'?checkout=1');?>" id="myform" name="myForm" novalidate method="post">
            <?php } else { ?>
                <form action="<?php echo base_url('shipping/edit/'.$location_id);?>" id="myform" name="myForm" novalidate method="post"> 
            <?php } ?>
            <div class="row">
                <div class="col-12">
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('name');?>:</label>
                            <input class="form-control input-lg" placeholder="First Name" name="billfullname" value="<?php echo (isset($_POST['bill_fullname']) ? $_POST['bill_fullname'] : ($location ? $location->bill_fullname : '')); ?>"type="text" required />
                            <?php echo form_error('billfullname'); ?>
                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label" ><?php echo $this->lang->line('cell_number');?>:</label>
                            <input  type= "tel" class="form-control input-box rounded-0 contact" id="billcontact" name="billcontact" value="<?php echo (isset($_POST['bill_contact']) ? $_POST['bill_contact'] : ($location ? $location->bill_contact : '')); ?>" maxlength="20"/>
                            <?php echo form_error('billcontact'); ?>	 
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('address');?>:</label>
                            <input type= "text" class="form-control input-box rounded-0 address" id="" name="billaddress1" value="<?php echo (isset($_POST['bill_address_1']) ? $_POST['bill_address_1'] : ($location ? $location->bill_address_1 : '')); ?>"/>
                            <?php echo form_error('billaddress1'); ?>

                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('address2');?><small>(optional)</small>:</label>
                            <input type= "text" class="form-control input-box rounded-0 address" id="" name="billaddress2" value="<?php echo (isset($_POST['bill_address_2']) ? $_POST['bill_address_2'] : ($location ? $location->bill_address_2 : '')); ?>"/>
                            <?php echo form_error('billaddress2'); ?>
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6 <?php echo ($location->bill_country != 226) ? "d-none" : "d-block"; ?>" id="state">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('state');?>:</label>
                            <?php
                                $states = array();
                                foreach($statesList as $state){
                                $states[$state->id] = $state->state;
                                }
                                echo form_dropdown('billstate', $states, (isset($_POST['billstate'])?$_POST['billstate']:$location->bill_state), 'id="billstate" class="form-control" style=""');
                            ?> 
                        </div>
                        <div class="col-sm-6" id="province">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('province');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 address" id="billprovince" name="billprovince" value="<?php echo isset($_POST['billstate'])?$_POST['billstate']:$location->bill_state; ?>"/>
                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                        <label class="form-control-label resposive-label address-label">Country:</label>
                            <?php
                                $countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
                                foreach($countryList as $country){
                                $countries[$country->id] = $country->nicename;
                                }
                                echo form_dropdown('billcountry', $countries, (isset($_POST['billcountry'])?$_POST['billcountry']:$location->bill_country), 'id="billcountry" class="form-control" style=""');
                            ?>
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('city');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 city" id="" name="billcity" value="<?php echo (isset($_POST['bill_city']) ? $_POST['bill_city'] : ($location ? $location->bill_city : '')); ?>"/>
                            <?php echo form_error('billcity'); ?>
                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('zip');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 zip" id="" name="billzip" value="<?php echo (isset($_POST['bill_zip']) ? $_POST['bill_zip'] : ($location ? $location->bill_zip : '')); ?>"/>
                            <?php echo form_error('billzip'); ?>
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="same" name="ship_address" value="same" checked="checked">
                                <label class="custom-control-label radio-custom-input" for="same"><?php echo $this->lang->line('ship_address');?></label>
                            </div> 
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" id="different" name="ship_address" value="different">
                                <label class="custom-control-label radio-custom-input" for="different"><?php echo $this->lang->line('ship_Difaddress');?></label>
                            </div> 
                        </div>
                    </div>
                </div>
            </div> 
            <h3 class="text-center mb-3 mt-3 shipto d-none"><?php echo $this->lang->line('ship_to');?>:</h3>
            <div class="row shipto d-none">
                <div class="col-12">
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label  address-label" ><?php echo $this->lang->line('name');?>:</label>
                            <input class="form-control input-lg" placeholder="First Name" name="shipfullname" value="<?php echo (isset($_POST['ship_fullname']) ? $_POST['ship_fullname'] : ($location ? $location->ship_fullname : '')); ?>"type="text" required />
                            <?php echo form_error('shipfullname'); ?>
                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label  address-label" ><?php echo $this->lang->line('cell_number');?>:</label>
                            <input  type= "tel" class="form-control input-box rounded-0 contact" id="shipcontact" name="shipcontact" value="<?php echo (isset($_POST['ship_contact']) ? $_POST['ship_contact'] : ($location ? $location->ship_contact : '')); ?>" maxlength="20"/>
                            <?php echo form_error('shipcontact'); ?>
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('address');?>:</label>
                            <input type= "text" class="form-control input-box rounded-0 address" id="" name="shipaddress1" value="<?php echo (isset($_POST['ship_address_1']) ? $_POST['ship_address_1'] : ($location ? $location->ship_address_1 : '')); ?>"/>
                            <?php echo form_error('shipaddress1'); ?>
                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('address2');?><small>(optional)</small>:</label>
                            <input type= "text" class="form-control input-box rounded-0 address" id="" name="shipaddress2" value="<?php echo (isset($_POST['ship_address_2']) ? $_POST['ship_address_2'] : ($location ? $location->ship_address_2 : '')); ?>"/>
                            <?php echo form_error('shipaddress2'); ?>
                            <span class="error"></span>
                        </div>
                    </div>
                    <?php if($location->ship_country != 226){?>
                    <div class="row form-group">
                        <div class="col-sm-6 d-none" id="state2">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('state');?>:</label>
                            <?php
                                $states = array();
                                foreach($statesList as $state){
                                    $states[$state->id] = $state->state;
                                }
                                echo form_dropdown('shipstate', $states, (isset($_POST['shipstate'])?$_POST['shipstate']:''), 'id="shipstate" class="form-control" style=""');
                            ?>
                        </div>
                        <div class="col-sm-6" id="province2">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('province');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 address" id="shipprovince" name="shipprovince" value="<?php echo isset($_POST['shipstate'])?$_POST['shipstate']:$location->ship_state; ?>"/>
                            <span class="error"></span>
                        </div>
                    <?php } else {?>
                    <div class="row form-group">
                        <div class="col-sm-6" id="state2">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('state');?>:</label>
                            <?php
                                $states = array();
                                foreach($statesList as $state){
                                    $states[$state->id] = $state->state;
                                }
                                echo form_dropdown('shipstate', $states, (isset($_POST['shipstate'])?$_POST['shipstate']:$location->ship_state), 'id="shipstate" class="form-control" style=""');
                            ?>
                        </div>
                        <div class="col-sm-6 d-none" id="province2">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('province');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 address" id="shipprovince" name="shipprovince" value=""/>
                            <span class="error"></span>
                        </div>
                    <?php } ?>
                        <div class="col-sm-6">
                        <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('country');?>:</label>
                            <?php
                                $countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
                                foreach($countryList as $country){
                                $countries[$country->id] = $country->nicename;
                                }
                                echo form_dropdown('shipcountry', $countries, (isset($_POST['shipcountry'])?$_POST['shipcountry']:$location->ship_country), 'id="shipcountry" class="form-control" style=""');
                            ?>
                            <span class="error"></span>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('city');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 city" id="" name="shipcity" value="<?php echo (isset($_POST['ship_city']) ? $_POST['ship_city'] : ($location ? $location->ship_city : '')); ?>"/>
                            <?php echo form_error('shipcity'); ?>
                            <span class="error"></span>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-control-label resposive-label address-label"><?php echo $this->lang->line('zip');?>:</label>
                            <input  type= "text" class="form-control input-box rounded-0 zip" id="" name="shipzip" value="<?php echo (isset($_POST['ship_zip']) ? $_POST['ship_zip'] : ($location ? $location->ship_zip : '')); ?>"/>
                            <?php echo form_error('shipzip'); ?>
                            <span class="error"></span>
                        </div> 
                    </div>
                </div>
            </div>
            <div class="row form-group text-right">
                <div class="col text-right">
                    <input type="hidden" name="c" value="<?php echo (isset($_GET['c']) && $_GET['c'] !="")?$_GET['c']:""?>">
                    <button id="onepage-guest-register-button" type="submit" class="btn address_savebtn "><?php echo $this->lang->line('submit');?></button>
                    <a class="btn btn-danger" href='<?php echo base_url($_SERVER["REDIRECT_QUERY_STRING"]."?".$_SERVER['QUERY_STRING']) ?>'><?php echo $this->lang->line('reset');?></a>
                </div>
            </div>
                </form>
            </div>
        </div>
    </div>

<div class="container"> </div>
<script>
	$("input[type=radio]").on('click',function(){
		if($(this).val() == "same"){
			$(".shipto").addClass('d-none');	
		}else{
			$(".shipto").removeClass('d-none');
		}
	});
</script>