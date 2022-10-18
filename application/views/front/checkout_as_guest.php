<link rel="stylesheet" href="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.css'); ?>">
<div class="container">
    <?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>"Shipping"),array("url"=>base_url("checkout"),"cat_name"=>"Checkout"))));?>
    <div class="row">
        <div class="col-12 mt-3">
            <div class="progress progressbar">
                <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 5%" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="row">
                <div class="col-4">
                    <h4 class="progress-shipping">Shipping</h4>
                </div>
                <div class="col-4 text-center">
                    <h4 class="progress-payment">Payment</h4>
                </div>
                <div class="col-4  text-right">
                    <h4 class="progress-payment">Confirmation</h4>
                </div>
            </div>
        </div>
    </div>
    <form id="join-us-form" action="<?php echo base_url('Home/guest_registration');?>" method="post" > 
        <h3 class="text-center mb-3 mt-3">Contact Information:</h3>
        <div class="row">
        	<div class="col-12">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label" >First Name:</label>
                        <input class="form-control input-lg" placeholder="First Name" name="billfirst_name" value="<?php if(isset($_POST['first_name'])) echo $first_name;?>"type="text" required />
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label" >Last Name:</label>
                        <input class="form-control input-lg" placeholder="Last Name" name="billlast_name"  value="<?php if(isset($_POST['last_name'])) echo $last_name;?>" type="text" required>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label" >Email:</label>
                        <input class="form-control input-lg" placeholder="E-mail Address" name="billemail"  type="email" required  value="<?php if(isset($_POST['email'])) echo $email;?>">
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label" >Cell Number:</label>
                        <input  type= "tel" class="form-control input-box contact" id="billcontact" name="billcontact" value="" maxlength="20"/>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label ">Address of Delivery:</label>
                        <input type= "text" class="form-control input-box address" id="" name="billaddress1" value=""/>
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label ">Address Line<small>(optional)</small>:</label>
                        <input type= "text" class="form-control input-box address" id="" name="billaddress2" value=""/>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6" id="state">
                        <label class="juf-label">State:</label>
                        <?php
                            $states = array();
                            $states[0] = "- Select State -";
                            foreach($statesList as $state){
                                $states[$state->id] = $state->state;
                            }
                            echo form_dropdown('billstate', $states, (isset($_POST['state'])?$_POST['state']:''), 'id="billstate" class="form-control" style=""');
                        ?>
                    </div>
                    <div class="col-sm-6 d-none" id="province">
                        <label class="juf-label ">Province:</label>
                        <input  type= "text" class="form-control input-box address" id="billprovince" name="billprovince" value=""/>
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                       <label class="juf-label ">Country:</label>
						<?php
                            $countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
                            foreach($countryList as $country){
                                $countries[$country->id] = $country->nicename;
                            }
                            echo form_dropdown('billcountry', $countries, (isset($_POST['billcountry'])?$_POST['billcountry']:''), 'id="billcountry" class="form-control" style=""');
                        ?>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label ">City:</label>
                        <input  type= "text" class="form-control input-box city" id="" name="billcity" value=""/>
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label ">Zip:</label>
                        <input  type= "text" class="form-control input-box zip" id="" name="billzip" value=""/>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-12">
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" class="custom-control-input" id="same" name="ship_address" value="same" checked="checked">
                            <label class="custom-control-label radio-custom-input juf-label" for="same">Ship to this address</label>
                        </div> 
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="different" name="ship_address" value="different">
                            <label class="custom-control-label radio-custom-input juf-label" for="different">Ship to different address</label>
                        </div> 
                    </div>
                </div>
            </div>
        </div> 
        <h3 class="text-center mb-3 mt-3 shipto d-none">Ship to:</h3>
        <div class="row shipto d-none">
            <div class="col-12">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label" >First Name:</label>
                        <input class="form-control input-lg" placeholder="First Name" name="shipfirst_name" value="<?php if(isset($_POST['first_name'])) echo $first_name;?>"type="text" required />
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label" >Last Name:</label>
                        <input class="form-control input-lg" placeholder="Last Name" name="shiplast_name"  value="<?php if(isset($_POST['last_name'])) echo $last_name;?>" type="text" required>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label" >Email:</label>
                        <input class="form-control input-lg" placeholder="E-mail Address" name="shipemail"  type="email" required  value="<?php if(isset($_POST['email'])) echo $email;?>">
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label" >Cell Number:</label>
                        <input  type= "tel" class="form-control input-box contact" id="shipcontact" name="shipcontact" value="" maxlength="20"/>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label ">Address of Delivery:</label>
                        <input type= "text" class="form-control input-box address" id="" name="shipaddress1" value=""/>
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label ">Address Line<small>(optional)</small>:</label>
                        <input type= "text" class="form-control input-box address" id="" name="shipaddress2" value=""/>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6" id="state2">
                        <label class="juf-label ">State:</label>
                        <?php
                            echo form_dropdown('shipstate', $states, (isset($_POST['shipstate'])?$_POST['shipstate']:''), 'id="shipstate" class="form-control" style=""');
                        ?>
                    </div>
                    <div class="col-sm-6 d-none" id="province2">
                        <label class="juf-label ">Province:</label>
                        <input  type= "text" class="form-control input-box address" id="shipprovince" name="shipprovince" value=""/>
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                       <label class="juf-label ">Country:</label>
						<?php
                            echo form_dropdown('shipcountry', $countries, (isset($_POST['shipcountry'])?$_POST['shipcountry']:''), 'id="shipcountry" class="form-control" style=""');
                        ?>
                        <span class="error"></span>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="juf-label ">City:</label>
                        <input  type= "text" class="form-control input-box city" id="" name="shipcity" value=""/>
                        <span class="error"></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="juf-label ">Zip:</label>
                        <input  type= "text" class="form-control input-box zip" id="" name="shipzip" value=""/>
                        <span class="error"></span>
                    </div>
                </div>
            </div>
        </div>
         <div class="row form-group text-right">
            <div class="col text-right">
                <button id="onepage-guest-register-button" type="submit" class="btn rounded-0">Continue</button>
           </div>
        </div>
    </form>
</div>
<script>
	$("input[type=radio]").on('click',function(){
		if($(this).val() == "same"){
			$(".shipto").addClass('d-none');	
		}else{
			$(".shipto").removeClass('d-none');
		}
	});
</script>
