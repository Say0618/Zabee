      <!-- Top content -->
      
<?php
      if($_SESSION['store_id'] == "" && $_SESSION['store_name'] == "" ){
          $url =  base_url()."seller/dashboard/saveStore";		
      }
      else if($_SESSION['store_id'] != "" && $_SESSION['store_name'] != "" ){
        $url = base_url()."seller/dashboard/updateStore";
      }
      else{
        $url =  base_url()."seller/dashboard/saveStore";		
      }
?>
<?php if($this->session->flashdata('error') || $this->session->flashdata('success')){?>
<div class="card-header">
	<h6 class="success"><?php echo $this->session->flashdata('success');?></h6>
	<h6 class="error"><?php echo $this->session->flashdata('error');?></h6>
</div>
<?php }?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h3 align="center" class="m-3"><?php echo $this->lang->line('store_information');?>! <?php echo (isset($user_store->store_id) && $user_store->store_id)?'<span class="float-right"><a href="'.base_url("store/".stripslashes($user_store->store_id)).'">Store Link</a></span>':""?></h3>
            <form role="form" action="<?php echo $url;?>" method="post" class="pb-5" id="myform"  enctype="multipart/form-data">
                <fieldset>
                    <div class="row">  
                        <div class="col-sm-12" align="center">                              	
                             <div class="picture-container">
                             <?php if(isset($user_store->store_logo) && $user_store->store_logo != ""){
                                        $link =  $this->config->item("store_logo_path").$user_store->store_logo;
                                    }else {
                                        $link = $this->config->item("store_logo_path").'default.jpg';
                                    }
                                    if(isset($user_store->cover_image) && $user_store->cover_image != ""){
                                        $cover = $this->config->item("store_cover_path").$user_store->cover_image;
                                    }else {
                                        $cover = $this->config->item("store_cover_path").'default.jpg';
                                    }?>
                                    <div class="picture-cover" >
                                      <img src="<?php echo $cover."?".time();?>" class="picture-cover-src" id="storeCoverPreview"/>
                                      <input type="file" id="store_cover" name="profile_image_cover" accept="image/*" title="Click to Update Cover Photo" data-toggle="tooltip">
                                    </div>
                                  <div class="picture" >
                                      <img src="<?php echo $link."?".time();?>" class="picture-src" id="storeLogoPreview"/>
                                      <input type="file" id="store_logo" name="profile_image" accept="image/*" data-toggle="tooltip" data-placement="bottom" title="Click to Update Store Photo &#013;size must be 150x150!">
                                  </div>
                                  <h6><?php echo $this->lang->line('store_logo');?></h6>
                              </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label  for="store_name"><?php echo $this->lang->line('store_name');?> <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="bottom" title="Enter your Store Name"></i></label>
                                <input type="text" name="store_name" placeholder="<?php echo $this->lang->line('store_name');?>..." class="f1-store-name form-control required" id="store_name" value="<?php echo (isset($user_store->store_name) && $user_store->store_name)?stripslashes($user_store->store_name):((isset($_SESSION['store_name']) && $_SESSION['store_name'] != "") ? $_SESSION['store_name']: "")?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="store_id"><?php echo $this->lang->line('store_id');?></label>
                                <span id="store_id"  class="f1-store-id form-control"><?php echo (isset($user_store->store_id) && $user_store->store_id)?stripslashes($user_store->store_id):((isset($_SESSION['store_slug']) && $_SESSION['store_slug'] != "") ? $_SESSION['store_slug']: "")?></span>	                               		 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">   	
                            <div class="form-group">
                                <label  for="f1-country"><?php echo $this->lang->line('store_based');?></label>
                                <select id="country" class="f1-country form-control" name="country">
                                <?php /*?><option value="" style="visibility:hidden">-Select Country of Incorporation-</option><?php */?>
                                <optgroup>
                                <option value="38"><?php echo $this->lang->line('canada');?></option>
                                <option value="226"><?php echo $this->lang->line('us_states');?></option>
                                </optgroup>
                                <optgroup>
                            <?php foreach($countries as $country){?>
                                    <option value="<?php echo $country->id?>" <?php echo (isset($user_store->country_id) && $country->id == $user_store->country_id)?'selected="selected"':""?> ><?php echo $country->name.' ('.$country->iso.')'?></option>
                                    <?php }?>
                                </optgroup>
                                </select>
                                <!-- <input type="text" name="f1-facebook" placeholder="Country..." class="f1-facebook form-control" id="f1-facebook"> -->
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label  for="f1-business-type"><?php echo $this->lang->line('legal_buss');?></label>
                                <select id="legal_busniess_type" class="f1-business-type form-control" name="legal_busniess_type">
                                    <?php foreach($getLegalBusniessType as $lbt){?>
                                        <option value="<?php echo $lbt->legal_id?>" <?php echo (isset($user_store->legal_busniess_type) && $lbt->legal_id == $user_store->legal_busniess_type)?'selected="selected"':""?> ><?php echo $lbt->legal_busniess_type?></option>
                                    <?php }?>
                                </select>		                                  
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                             <div class="form-group">
                                <label  for="f1-contact-phone"><?php echo $this->lang->line('phone');?></label>
                                <input type="tel" name="contact_phone" placeholder="<?php echo $this->lang->line('contact_phone');?>..." class="f1-contact-phone form-control required" id="f1-contact-phone" value="<?php echo (isset($user_store->contact_phone) && $user_store->contact_phone)?$user_store->contact_phone:""?>">
                            </div>
                        </div>
                         <div class="col-sm-6">
                            <div class="form-group">
                                <label  for="f1-contact-email"><?php echo $this->lang->line('email');?></label>
                                <input type="text" name="contact_email" placeholder="<?php echo $this->lang->line('contact_email');?>..." class="f1-contact-email form-control required" id="f1-contact-email" value="<?php echo (isset($user_store->contact_email) && $user_store->contact_email)?$user_store->contact_email:""?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php /*?><div class="col-sm-6">
                            <div class="form-group">
                                <label  for="f1-store-address">Store Address</label>
                                <input type="text" name="store_address" placeholder="Store Address..." class="f1-store-address form-control required" id="f1-store-address" value="<?php echo (isset($user_store->store_address) && $user_store->store_address)?$user_store->store_address:""?>">
                            </div>
                        </div><?php */?>
                        <div class="col-sm-6">
                            <label  for="f1-store-address"><?php echo $this->lang->line('inventory_handle_by');?>: </label>
                            <div class="form-group form-control">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="zabee" name="is_zabee" value="1"<?php echo (isset($user_store->s_id) && $user_store->is_zabee == "1")?"checked":""?>>
                                    <label class="custom-control-label" for="zabee">Yes</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" id="self" name="is_zabee" value="0" <?php echo (isset($user_store->s_id) && $user_store->is_zabee == "0")?"checked":""?>>
                                    <label class="custom-control-label" for="self">No</label>
                                </div>
                            </div>
                            <p id="zab-yes" class="d-none">Zab.ee will contact you with instruction on where to send your inventory</p>
                        </div>
                        <div class="col-6">
                        <label  for="f1-store-address"><?php echo $this->lang->line('collect_taxs');?>: </label>
                            <div class="form-group form-control">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input tax" id="taxY" name="is_tax" value="1"<?php echo (isset($user_store->s_id) && $user_store->is_tax == "1")?"checked":""?> >
                                    <label class="custom-control-label" for="taxY"><?php echo $this->lang->line('yes');?></label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input tax" data-sid = "<?php echo (isset($user_store->s_id) && $user_store->s_id !="")?$user_store->s_id:""?>" data-userid="<?php echo (isset($_SESSION['userid']))?$_SESSION['userid']:"";?>" id="taxN" name="is_tax" value="0"<?php echo (isset($user_store->s_id) && $user_store->is_tax == "0")?"checked":""?> >
                                    <label class="custom-control-label" for="taxN"><?php echo $this->lang->line('no');?></label>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-6"></div>
                        <div class="col-2 taxState d-none">
                            <label  for="stateTax"><?php echo $this->lang->line('all-state');?>: </label>
                            <div class="form-group form-control">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="form-check-input states" id="allState" name="all_state" <?php echo (isset($user_store->s_id) && $user_store->is_tax == "1" && $user_store->state_id == "0" )?"checked":""?> >
                                    <label class="form-check-label" for="allState"><?php echo $this->lang->line('yes');?></label>
                                </div>
                            </div> 
                        </div>
                        <div class="col-4 taxState d-none">
                            <label  for="stateTax"><?php echo $this->lang->line('collect_tax');?>: </label>
                            <input type="text" class="form-control" name="state_tax" id="stateTax" value="<?php echo (isset($user_store->s_id) && $user_store->states)?$user_store->states:""?>">
                            <input type="hidden" id="state_id" name="stateId"/>
                        </div>
                    </div>
                    <div class="row state mt-3">
                        <div class="col-sm-12">
                            <div class="f1-buttons">
                                <a href="<?php echo isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:base_url() ?>" class="btn btn-previous"><?php echo $this->lang->line('back');?></a>
                                <button type="submit" class="btn btn-next"><?php echo (isset($user_store->s_id) && $user_store->s_id !="")?$this->lang->line('update'):$this->lang->line('create')?></button>
                                <input type="hidden" name="s_id" value="<?php echo (isset($user_store->s_id) && $user_store->s_id !="")?$user_store->s_id:""?>"  />
                                <input type="hidden" name="store_id" id="h_store_id" value="<?php echo (isset($user_store->store_id) && $user_store->store_id !="")?$user_store->store_id:""?>"  />
                                <input type="hidden" value="<?php echo (isset($_SESSION['userid']))?$_SESSION['userid']:"";?>" name="userid" id="userid" />
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>