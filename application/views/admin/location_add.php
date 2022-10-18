<?php //print_r($_COOKIE); ?>
<form action="<?php echo base_url('seller/location/add');?>" id="myform" name="myForm" novalidate method="post">
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4"><?php echo $this->lang->line('bill_to');?></h3>
			</div>
			<div class="card-body">
				<div class="col-sm-12">
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label" ><?php echo $this->lang->line('full_name');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="name" class="form-control" id="txtNumeric" class = "fname" placeholder="<?php echo $this->lang->line('full_name');?>" name="billfullname" >
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('contact');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="contact" class="form-control" class="contact" placeholder = "<?php echo $this->lang->line('contact');?>" name="billcontact" >
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('address_line_1');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="billaddress1" >
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('address_line_2');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="billaddress2" >
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('country');?></label>
						</div>
						<div  class="col-sm-4">
							<?php
								$countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
								foreach($countryList as $country){
									$countries[$country->id] = $country->nicename;
								}
								echo form_dropdown('billcountry', $countries, $_COOKIE['country_value'], 'class="form-control"', 'style="width:100%"');
							?>
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('state');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="state" class="form-control" class="address" placeholder="<?php echo $this->lang->line('state');?>" name="billstate" id="billstate" />
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('city');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="name" class="form-control" class="city" placeholder = "<?php echo $this->lang->line('city');?>" name="billcity" >
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label"><?php echo $this->lang->line('zip');?></label>
						</div>
						<div  class="col-sm-4">
							<input type="number_format" class="form-control" class="zip" placeholder = "<?php echo $this->lang->line('zip');?>" name="billzip" >
						</div>
						<div class="clearfix"></div>
					</div>
				</div>	
			</div>
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4"><?php echo $this->lang->line('ship_to');?></h3>
			</div>
			<div class="card-body">
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('full_name');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class = "form-control" id="txtNumeric2" class = "fname" placeholder="<?php echo $this->lang->line('full_name');?>" name="shipfullname" >
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('contact');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="contact" class="form-control" class="contact" placeholder = "<?php echo $this->lang->line('contact');?>" name="shipcontact" >
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('address_line_1');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="shipaddress1" >
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('address_line_2');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "<?php echo $this->lang->line('address');?>" name="shipaddress2" >
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('country');?></label>
					</div>
					<div  class="col-sm-4">
						<?php
							$countries = array($_COOKIE['country_id']=> $_COOKIE['country_value']);
							foreach($countryList as $country){
								$countries[$country->id] = $country->nicename;
							}
							echo form_dropdown('shipcountry', $countries, $_COOKIE['country_value'], 'class="form-control"','style="width:100%"');
						?>
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('state');?></label>
					</div>
					<div class="col-sm-4">
						<input type="state" class="form-control" class="address" placeholder="<?php echo $this->lang->line('state');?>" name="shipstate" id="shipstate" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('city');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class="form-control" class="city" placeholder = "<?php echo $this->lang->line('city');?>" name="shipcity">
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label"><?php echo $this->lang->line('zip');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="number_format" class="form-control" class="zip" placeholder = "<?php echo $this->lang->line('zip');?>" name="shipzip" >
					</div>
					<div class="clearfix"></div>
				</div>
				<hr>
				<div class="row form-group">
					<div class="col-sm-4 checkbox">
					  <label><input type="checkbox" value="" name="shipbill" onclick="return ShipBillForm(this.form);"><?php echo $this->lang->line('ship_to_bill');?></label>
					</div>
					<div class="col-sm-8">
						<div class="pull-right" style="margin: 0px 14px 14px;">
							<button type="reset" class="btn btn-danger" ><?php echo $this->lang->line('reset');?></button>
						</div>
						<div class="pull-right" style="margin: 0px 14px 14px;">
							<button type="submit" class="btn btn-primary" ><?php echo $this->lang->line('submit');?></button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
</form>