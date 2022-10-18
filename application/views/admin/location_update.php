<form action="<?php echo base_url('seller/location/update/'.$location_id);?>" id="myform" name="myForm" novalidate method="post">
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4"><?php echo $this->lang->line('bill_to');?></h3>
			</div>
			<?php //print_r($location);die(); ?>
			<div class="card-body">
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('full_name');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class="form-control" class = "fname" id="txtNumeric" placeholder="<?php echo $this->lang->line('full_name');?>" name="billfullname" value="<?php echo (isset($_POST['bill_fullname']) ? $_POST['bill_fullname'] : ($location ? $location->bill_fullname : '')); ?>"/ >
						
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('contact');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="contact" class="form-control" class="contact" placeholder = "<?php echo $this->lang->line('contact');?>" name="billcontact" value="<?php echo (isset($_POST['bill_contact']) ? $_POST['bill_contact'] : ($location ? $location->bill_contact : '')); ?>"/>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('address_line_1');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="billaddress1" value="<?php echo (isset($_POST['bill_address_1']) ? $_POST['bill_address_1'] : ($location ? $location->bill_address_1 : '')); ?>"/>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('address_line_2');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="billaddress2" value="<?php echo (isset($_POST['bill_address_2']) ? $_POST['bill_address_2'] : ($location ? $location->bill_address_2 : '')); ?>"/>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('country');?></label>
					</div>
					<div  class="col-sm-4">
						<?php
							$countries = array(''=>'Select Country');
							foreach($countryList as $country){
								$countries[$country->id] = $country->nicename;
							}
							echo form_dropdown('billcountry', $countries, (isset($_POST['billcountry'])?$_POST['billcountry']:($location ? $location->bill_country : '')), 'class="form-control"','style="width:100%"');
						?>
					</div>
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('state');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" class="address" placeholder="<?php echo $this->lang->line('state');?>" name="billstate" id="billstate" value="<?php echo (isset($_POST['bill_state']) ? $_POST['bill_state'] : ($location ? $location->bill_state : '')); ?>"/>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('city');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class="form-control" class="city" placeholder = "<?php echo $this->lang->line('city');?>" name="billcity" value="<?php echo (isset($_POST['bill_city']) ? $_POST['bill_city'] : ($location ? $location->bill_city : '')); ?>"/>
					</div>
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('zip');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="number_format" class="form-control" class="zip" placeholder = "<?php echo $this->lang->line('zip');?>" name="billzip" value="<?php echo (isset($_POST['bill_zip']) ? $_POST['bill_zip'] : ($location ? $location->bill_zip : '')); ?>"/>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4"><?php echo $this->lang->line('ship_to');?></h3>
			</div>
			<div class="card-body">
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('full_name');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class = "form-control" id="txtNumeric" class = "fname" placeholder="<?php echo $this->lang->line('full_name');?>" name="shipfullname" value="<?php echo (isset($_POST['ship_fullname']) ? $_POST['ship_fullname'] : ($location ? $location->ship_fullname : '')); ?>" />
						
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('contact');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="contact" class="form-control" class="contact" placeholder = "<?php echo $this->lang->line('contact');?>" name="shipcontact" value="<?php echo (isset($_POST['ship_contact']) ? $_POST['ship_contact'] : ($location ? $location->ship_contact : '')); ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('address_line_1');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="shipaddress1" value="<?php echo (isset($_POST['ship_address_1']) ? $_POST['ship_address_1'] : ($location ? $location->ship_address_1 : '')); ?>" />
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('address_line_2');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="shipaddress2" value="<?php echo (isset($_POST['ship_address_2']) ? $_POST['ship_address_2'] : ($location ? $location->ship_address_2 : '')); ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('country');?></label>
					</div>
					<div  class="col-sm-4">
						<?php
							$countries = array(''=>$this->lang->line('country_select'));
							foreach($countryList as $country){
								$countries[$country->id] = $country->nicename;
							}
							echo form_dropdown('shipcountry', $countries, (isset($_POST['shipcountry'])?$_POST['shipcountry']:($location ? $location->ship_country : '')), 'class="form-control"','style="width:100%"');
						?>
					</div>
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('state');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" class="address" placeholder="<?php echo $this->lang->line('state');?>" name="shipstate" id="shipstate" value="<?php echo (isset($_POST['ship_state']) ? $_POST['ship_state'] : ($location ? $location->ship_state : '')); ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('city');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class="form-control" class="city" placeholder = "<?php echo $this->lang->line('city');?>" name="shipcity" value="<?php echo (isset($_POST['ship_city']) ? $_POST['ship_city'] : ($location ? $location->ship_city : '')); ?>" />
					</div>
					<div class="col-sm-2 text-right">
						<label class=" control-label resposive-label"><?php echo $this->lang->line('zip');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="number_format" class="form-control" class="zip" placeholder = "<?php echo $this->lang->line('zip');?>" name="shipzip" value="<?php echo (isset($_POST['ship_zip']) ? $_POST['ship_zip'] : ($location ? $location->ship_zip : '')); ?>" />
					</div>
					<div class="clearfix"></div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4 checkbox">
					  <label><input type="checkbox" value="" name="shipbill" onclick="return ShipBillForm(this.form);"><?php echo $this->lang->line('ship_to_bill');?></label>
					</div>
					<div class="col-sm-8">
						<div class=" pull-right" style="margin: 0px 14px 14px;">
							<button type="reset" class="btn btn-danger newBtn2" ><?php echo $this->lang->line('reset');?></button>
						</div>
						<div class=" pull-right" style="margin: 0px 14px 14px;">
							<button type="submit" class="btn btn-primary newBtn2" ><?php echo $this->lang->line('update');?></button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
			
</form>