<?php //print_r($_COOKIE); ?>
<form action="<?php echo base_url('seller/location/add');?>" id="myform" name="myForm" novalidate method="post">
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4">Bill To</h3>
			</div>
			<div class="card-body">
				<div class="col-sm-12">
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label" >Full name</label>
						</div>
						<div  class="col-sm-4">
							<input type="name" class="form-control" id="txtNumeric" class = "fname" placeholder="Full name" name="billfullname" >
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label">Contact</label>
						</div>
						<div  class="col-sm-4">
							<input type="contact" class="form-control" class="contact" placeholder = "Contact" name="billcontact" >
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label">Address Line 1</label>
						</div>
						<div  class="col-sm-4">
							<input type="address" class="form-control" class="address" placeholder = "Address" name="billaddress1" >
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label">Address Line 2</label>
						</div>
						<div  class="col-sm-4">
							<input type="address" class="form-control" class="address" placeholder = "Address" name="billaddress2" >
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label">Country</label>
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
							<label class="form-control-label resposive-label">State</label>
						</div>
						<div  class="col-sm-4">
							<input type="state" class="form-control" class="address" placeholder="State" name="billstate" id="billstate" />
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label">City</label>
						</div>
						<div  class="col-sm-4">
							<input type="name" class="form-control" class="city" placeholder = "City" name="billcity" >
						</div>
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label">ZIP</label>
						</div>
						<div  class="col-sm-4">
							<input type="number_format" class="form-control" class="zip" placeholder = "ZIP" name="billzip" >
						</div>
						<div class="clearfix"></div>
					</div>
				</div>	
			</div>
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4">Ship To</h3>
			</div>
			<div class="card-body">
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">Full name</label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class = "form-control" id="txtNumeric2" class = "fname" placeholder="Full name" name="shipfullname" >
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">Contact</label>
					</div>
					<div  class="col-sm-4">
						<input type="contact" class="form-control" class="contact" placeholder = "Contact" name="shipcontact" >
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">Address Line 1</label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "Address" name="shipaddress1" >
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">Address Line 2</label>
					</div>
					<div  class="col-sm-4">
						<input type="address" class="form-control" class="address" placeholder = "Address" name="shipaddress2" >
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">Country</label>
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
						<label class="form-control-label resposive-label">State</label>
					</div>
					<div class="col-sm-4">
						<input type="state" class="form-control" class="address" placeholder="State" name="shipstate" id="shipstate" />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">City</label>
					</div>
					<div  class="col-sm-4">
						<input type="name" class="form-control" class="city" placeholder = "City" name="shipcity">
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label">ZIP</label>
					</div>
					<div  class="col-sm-4">
						<input type="number_format" class="form-control" class="zip" placeholder = "ZIP" name="shipzip" >
					</div>
					<div class="clearfix"></div>
				</div>
				<hr>
				<div class="row form-group">
					<div class="col-sm-4 checkbox">
					  <label><input type="checkbox" value="" name="shipbill" onclick="return ShipBillForm(this.form);">Shipping to bill address</label>
					</div>
					<div class="col-sm-8">
						<div class="pull-right" style="margin: 0px 14px 14px;">
							<button type="reset" class="btn btn-danger" >Reset</button>
						</div>
						<div class="pull-right" style="margin: 0px 14px 14px;">
							<button type="submit" class="btn btn-primary" >Submit</button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
</form>