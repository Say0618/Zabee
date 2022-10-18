<link rel="stylesheet" href="<?php echo media_url('assets/plugins/intl-tel-input/intlTelInput.css'); ?>">
<style>
.intl-tel-input .selected-flag{
    padding: 0 6px 0 8px !important;
}
div.intl-tel-input{
    width : 97.9% !important
}
/* .intl-tel-input .selected-flag .iti-arrow {height: 36px;} */
/* .iti-flag.us{height: 43px;} */
/* .iti-flag{    background-color: #ffffff;box-shadow: 0px 0px 0px 0px #ffffff;} */
.fa-star-of-life {
    color: #FF0101;
    font-size: 8px;
    line-height: 35px;
    margin-left: 10px;}
</style>
      <!-- Top content -->
	  	<div class="top-content">
			<div class="container">
                <div class="row">
                    <div class="col-sm-12">
                    	<h3 align="center"><?php echo $this->lang->line('warehouse_info');?> !</h3>
                        <h6 class="error"><?php echo $this->session->flashdata('error');?></h6>
                        <?php if($error){ echo '<h6 class="error">'.$error."</h6>";}?>
                        <form role="form" action="<?php echo base_url("warehouse/dashboard/saveWarehouse")?>" method="post" id="myform"  enctype="multipart/form-data">
						<input type="hidden" value="<?php ?>">
                    		<fieldset>
                                <?php /*?><div class="row">  
                                    <div class="col-sm-12" align="center">                              	
                                         <div class="picture-container">
                                              <div class="picture" >
												  <?php 
                                                    if(isset($warehouse->store_logo) && $warehouse->store_logo != ""){
                                                        $link =  base_url('uploads/store_logo/').$warehouse->store_logo;
                                                    } else {
                                                        $link = media_url('assets/backend/images/store-default.png');
                                                    }
													?>
                                                  <img src="<?php echo $link."?".time();?>" class="picture-src" id="storeLogoPreview" title="" />
                                                  <input type="file" id="store-logo" name="store_logo" accept="image/*">
                                              </div>
                                              <h6>Choose Picture</h6>
                                          </div>
                                    </div>
                                </div><?php */?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="" for="store_name"><?php echo $this->lang->line('warehouse_title');?></label>
                                            <input type="text" name="warehouse_title" class="form-control required" id="warehouse_title" value="<?php echo (isset($warehouse->warehouse_title) && $warehouse->warehouse_title)?stripslashes($warehouse->warehouse_title):""?>">
                                        </div>
                                    </div>
                                </div>
                             <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="" for="f1-contact-email"><?php echo $this->lang->line('email');?></label>
                                        <input type="text" name="email" class="f1-contact-email form-control required" id="f1-contact-email" value="<?php echo (isset($warehouse->email) && $warehouse->email)?$warehouse->email:""?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                     <div class="form-group">
                                        <label for="f1-contact-phone"><?php echo $this->lang->line('contact_no');?></label>
                                        <input type="tel" name="contact_no" class="f1-contact-phone form-control required" id="f1-contact-phone" value="<?php echo (isset($warehouse->contact_no) && $warehouse->contact_no)?$warehouse->contact_no:""?>">
                                    </div>
                                </div>
							</div> 
                             <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="" for="f1-store-address"><?php echo $this->lang->line('address');?></label>
                                        <input type="text" name="address" class="f1-store-address form-control required" id="f1-store-address" value="<?php echo (isset($warehouse->address) && $warehouse->address)?$warehouse->address:""?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                     <div class="form-group">
                                        <label class="" for="f1-business-type"><?php echo $this->lang->line('class');?></label>
                                        <select id="warehouse_class" class="f1-business-type form-control" name="warehouse_class">
                                            <option value="" style="display:none">-<?php echo $this->lang->line('warehouse_select');?>-</option>
                                            <?php foreach($getWarehouseClass as $gwc){?>
                                                <option value="<?php echo $gwc->warehouse_class_id?>" <?php echo (isset($warehouse->warehouse_class_id) && $gwc->warehouse_class_id == $warehouse->warehouse_class_id)?'selected="selected"':""?> ><?php echo $gwc->warehouse_class?></option>
                                            <?php }?>
                                        </select>		                                  
                                    </div>
                                </div>
                            </div>
								<input type="hidden" class="warehouse_id" name="warehouse_id" value="<?php echo (isset($warehouse->warehouse_id) && $warehouse->warehouse_id)?$warehouse->warehouse_id:""?>">
							<div class="row">
                            	<div class="col-sm-3">   	
                                    <div class="form-group">
                                        <label class="" for="f1-country"><?php echo $this->lang->line('country');?></label>
                                        <select id="country" class="f1-country form-control" name="country_id">
                                        <option value="" style="visibility:hidden">-<?php echo $this->lang->line('country_select');?>-</option>
                                        <optgroup>
                                        <option value="226">UNITED STATES (US)</option>
                                        </optgroup>
                                        <optgroup>
                                    <?php foreach($countries as $country){?>
                                         <option value="<?php echo $country->id?>" <?php echo (isset($warehouse->country_id) && $country->id == $warehouse->country_id)?'selected="selected"':""?> ><?php echo $country->name.' ('.$country->iso.')'?></option>
                                         <?php }?>
                                        </optgroup>
                                        </select>
                                        <!-- <input type="text" name="f1-facebook" placeholder="Country..." class="f1-facebook form-control" id="f1-facebook"> -->
                                    </div>
                                </div>
                                <div class="col-sm-3" id="state">
                                    <div class="form-group">
                                        <label class="" for="f1-state"><?php echo $this->lang->line('state');?></label>
                                        <?php
                                         $states = array();
                                        foreach($statesList as $state){
                                        $states[$state->id] = $state->state;
                                             }
                                         echo form_dropdown('state_id', $states, (isset($warehouse->state_id)?$warehouse->state_id:''), 'id="f1-state" class="form-control f1-state required" style=""');
                                          ?>		
                                    </div>
                                </div>
                                <div class="col-sm-3 d-none" id="province">
                                    <div class="form-group">
                                        <label class="" for="f1-province"><?php echo $this->lang->line('province');?></label>
                                        <input type="text" name="province" placeholder="Province..." class="f1-province form-control required" id="f1-province" value="<?php echo (isset($warehouse->province) && $warehouse->province)?$warehouse->province:""?>" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                     <div class="form-group">
                                        <label class="" for="f1-city"><?php echo $this->lang->line('city');?></label>
                                        <input type="text" name="city" class="f1-city form-control required" id="f1-city" value="<?php echo (isset($warehouse->city) && $warehouse->city)?$warehouse->city:""?>" />
                                    </div>
                            	</div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="" for="f1-zip"><?php echo $this->lang->line('zip');?></label>
                                        <input type="text" name="zip_code" class="f1-zip form-control" id="f1-zip" value="<?php echo (isset($warehouse->zip_code) && $warehouse->zip_code)?$warehouse->zip_code:""?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row state">
                                <div class="col-sm-12">
                                    <div class="f1-buttons">
                                        <button type="button" class="btn btn-previous"><?php echo $this->lang->line('back');?></button>
                                        <button type="submit" class="btn btn-next" id="create"><?php echo (isset($warehouse->warehouse_id) && $warehouse->warehouse_id)?$this->lang->line('update'):$this->lang->line('create')?></button>
                                        <input type="hidden" value="<?php echo (isset($this->session->zabeeWarehouseData["userid"]))?$this->session->zabeeWarehouseData["userid"]:"";?>" name="user_id" id="userid" />
                                    </div>
                                </div>
                            </div>
                           </fieldset>
                    	</form>
                    </div>
                </div>
            </div>
        </div>