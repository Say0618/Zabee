<form action="<?php //echo base_url('admin/profile/update');?>" id="myform1" name="myForm1" novalidate method="post">	
		<div class="row card-header d-flex align-items-center">
					Payment Method Gateway
		</div>
		<input type="hidden" name="gateway_id" id="gateway_id" value="<?php echo $gateway_id; ?>" />
		<div class="card-body">	
			<div class="col-sm-12">
				<div class="col-sm-6 form-group" id="SelectPaymentMethod" >
					<label for="paymentmethod" class="control-label resposive-label"  style="float:left">&nbsp;Payment Method:</label>
					<div class="col-sm-5 pull-left">
						<?php
							$gateways_options = array(
								'Paypal'=>'Paypal',
								'Stripe'=>'Stripe',
								'BT'=>'Brain Tree',
							);
							echo form_dropdown('paymentmethod', $gateways_options, (isset($_POST['paymentmethod'])?$_POST['paymentmethod']:($paymentgateway ? $paymentgateway->paymentmethod : '')), 'class="form-control" id="paymentmethod"');
						?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			
			<div class="col-sm-12" id="showMe">
			<hr>
			 <!--<ul class="nav nav-tabs">
				<li class="hide"><a data-toggle="tab" href="#Paypal" class="Paypal_ thenav ">Paypal</a></li>
				<li class="hide"><a data-toggle="tab" href="#Stripe" class="Stripe_ thenav">Stripe</a></li>
				<li class="hide"><a data-toggle="tab" href="#BT" class="BT_ thenav">Brain Tree</a></li>
			  </ul>-->

			<div class="tab-content openTab" style="overflow:hidden;">
				<div role="tabpanel" id="Paypal" class="tab-pane fade">
					<!--<span data-tab="Paypal" class="pill-right closeTab topright">x</span>-->
					
						<div class="row">
						<div class="card-body">
							<div class="row form-group">
								<div class="col-sm-4 text-right">
									<label class=" control-label resposive-label" >PayPal API Username</label>
								</div>
								<div  class="col-sm-6">
									<input type="name" class="form-control" class="PayPalAPIUsername" placeholder="PayPal API Username" name="PayPalAPIUsername" value="<?php echo (isset($_POST['PayPal_APIUsername']) ? $_POST['PayPal_APIUsername'] : ($paymentgateway ? $paymentgateway->PayPal_APIUsername : '')); ?>" />
									
								</div>
							</div>
							<div class="row form-group">	
								<div class="col-sm-4 text-right">
									<label class="control-label resposive-label" >PayPal API Password</label>
								</div>
								<div  class="col-sm-6">
									<input type="password" class="form-control" class="PayPalAPIPassword" placeholder = "PayPal API Password" name="PayPalAPIPassword" value="<?php echo (isset($_POST['PayPal_APIPassword']) ? $_POST['PayPal_APIPassword'] : ($paymentgateway ? $paymentgateway->PayPal_APIPassword : '')); ?>" />
								</div>
								<div class="clearfix"></div>
							</div>
							<div class="row form-group">
								<div class="col-sm-4 text-right">
									<label class="control-label resposive-label" >PayPal API Signature</label>
								</div>
								<div  class="col-sm-6">
									<input type="name" class="form-control" class="PayPalAPISignature" placeholder = "PayPal API Signature" name="PayPalAPISignature" value="<?php echo (isset($_POST['PayPal_APISignature']) ? $_POST['PayPal_APISignature'] : ($paymentgateway ? $paymentgateway->PayPal_APISignature : '')); ?>" />
								</div>
							</div>	
							<div class="row form-group">	
								<div class="col-sm-4 text-right">
									<label class="control-label resposive-label" >PayPal API ApplicationID</label>
								</div>
								<div  class="col-sm-6">
									<input type="text" class="form-control" class="PayPalAPIApplicationID" placeholder = "PayPal API ApplicationID" name="PayPalAPIApplicationID" value="<?php echo (isset($_POST['PayPal_APIApplicationID']) ? $_POST['PayPal_APIApplicationID'] : ($paymentgateway ? $paymentgateway->PayPal_APIApplicationID : '')); ?>" />
								</div>
								<div class="clearfix"></div>
							</div>
							<hr>
							<div class="row">
								<div class="col-sm-12">
									<div class=" pull-right" style="margin: 0px 14px 14px;">
											<button type="submit" class="btn btn-primary" id="Submit3">Submit</button>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							</div>
						</div>
				</div>
				<div role="tabpanel" id="Stripe" class="tab-pane fade">
					<!--<span data-tab="Stripe" class="pill-right closeTab topright">x</span>-->
				<div class="row">
					<div class="card-body">
						<div class="row form-group">
							<div class="col-sm-4 text-right">
								<label class=" control-label resposive-label" >Stripe API key</label>
							</div>
							<div  class="col-sm-6">
								<input type="name" class="form-control" class = "StripeAPIkey" placeholder="Stripe API key" name="StripeAPIkey" value="<?php echo (isset($_POST['Stripe_APIkey']) ? $_POST['Stripe_APIkey'] : ($paymentgateway ? $paymentgateway->Stripe_APIkey : '')); ?>" />
								
							</div>
							<div class="clearfix"></div>
						</div>
						<hr>
						<div class="row">
							<div class="col-sm-12">
								<div class=" pull-right" style="margin: 0px 14px 14px;">
										<button type="submit" class="btn btn-primary " id="Submit2">Submit</button>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						</div>
						</div>
				</div>
				<div role="tabpanel" id="BT" class="tab-pane fade">
					<!--<span data-tab="BT" class="pill-right closeTab topright">x</span>-->
				<div class="row">
					<div class="card-body">
						<div class="row form-group">
							<div class="col-sm-4 text-right">
								<label class=" control-label resposive-label" >BrainTree_Merchant ID</label>
							</div>
							<div  class="col-sm-6">
								<input type="name" class="form-control" class = "BrainTree_MerchantID" placeholder="BrainTree_Merchant ID" name="BrainTree_MerchantID" value="<?php echo (isset($_POST['BrainTree_Merchant_ID']) ? $_POST['BrainTree_Merchant_ID'] : ($paymentgateway ? $paymentgateway->BrainTree_Merchant_ID : '')); ?>" />
								
							</div>
						</div>	
						<div class="row form-group">	
							<div class="col-sm-4 text-right">
								<label class="control-label resposive-label" >BrainTree_Public Key</label>
							</div>
							<div  class="col-sm-6">
								<input type="password" class="form-control" class="BrainTree_PublicKey" placeholder = "BrainTree_Public Key" name="BrainTree_PublicKey" value="<?php echo (isset($_POST['BrainTree_Public_Key']) ? $_POST['BrainTree_Public_Key'] : ($paymentgateway ? $paymentgateway->BrainTree_Public_Key : '')); ?>" />
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="row form-group">
							<div class="col-sm-4 text-right">
								<label class="control-label resposive-label" >BrainTree_Private Key</label>
							</div>
							<div  class="col-sm-6">
								<input type="name" class="form-control" class="BrainTree_PrivateKey" placeholder = "BrainTree_Private Key" name="BrainTree_PrivateKey" value="<?php echo (isset($_POST['BrainTree_Private_Key']) ? $_POST['BrainTree_Private_Key'] : ($paymentgateway ? $paymentgateway->BrainTree_Private_Key : '')); ?>" />
							</div>
							<div class="clearfix"></div>
						</div>
						<hr>
						<div class="row">
							<div class="col-sm-12">
								<div class=" pull-right" style="margin: 0px 14px 14px;">
									<button type="submit" class="btn btn-primary " id="Submit">Submit</button>
								</div>
								<div class="clearfix"></div>
							</div>
						</div>
						</div>
						</div>
				</div>
			</div>
		</div>
		</div>
	</form>

		