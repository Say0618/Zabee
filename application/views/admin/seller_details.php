<?php  //echo "<pre>"; print_r($user_store); echo "</pre>"; die(); ?>
<?php // print_r($id_new);die(); ?>
<div id="exTab2" class="">	
	<ul class="nav nav-tabs ul-padding-for-userdetails">
		<li class="tabs-css active">
			<a class="user-store-details" href="#user" data-toggle="tab"><?php echo $this->lang->line('store');?></a>
		</li>&nbsp;
		<li class="tabs-css">
			<a class="user-prod-details" href="#prod" data-toggle="tab"><?php echo $this->lang->line('products');?></a>
		</li>&nbsp;
	</ul>
	<div class="tab-content tab-padding">
		<div class="tab-pane" id="">
			<h3><?php echo $this->lang->line('choose_option');?></h3>
		</div>
		<div class="tab-pane active" id="user">
			<h3><?php echo $this->lang->line('store_information');?></h3>
			<hr>
			<div class="container emp-profile">
				<div class="row">
                    <div class="col-md-2">
                        <div class="profile-img">
							<?php 
								if(isset($user_store->store_logo) && $user_store->store_logo != ""){
									$link = store_logo_path($user_store->store_logo).'?'.time();
								} else {
									$link = image_url("store_cover/default.jpg");
								}?>
                         <img class="card-img-top" src="<?php echo $link; ?>" alt="image" style="width:100%">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-head mt-5">
							<h5><?php echo $user_store->store_name ?></h5>
							 <div class="">
							</div>
						</div>
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-md-8 padding-top-info">
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('store_id');?></label>
							</div>
							<div class="col-md-9">
								<p> <?php echo $user_store->store_id ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('store_address');?></label>
							</div>
							<div class="col-md-9">
								<p><?php echo $user_store->store_address ?></p>
							</div>
						</div>
						<?php foreach($getLegalBusniessType as $businesType){
                            if($user_store->legal_busniess_type == $businesType->legal_id ){
                        $user_store->legal_busniess_type = $businesType->legal_busniess_type; }}?>
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('business_type');?></label> 
							</div>
							<div class="col-md-9">
								<p><?php echo  $user_store->legal_busniess_type ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('country');?></label>
							</div>
							<div class="col-md-9">
								<?php
									foreach($countryList as $country){
										if($user_store->country_id == $country->id){?>
											<p><?php echo $country->nicename;?></p>
										<?php }
									} 
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('contact_person');?></label>
							</div>
							<div class="col-md-9">
								<p> <?php echo $user_store->contact_person ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('contact_no');?></label>
							</div>
							<div class="col-md-9">
								<p><?php echo $user_store->contact_phone ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label><?php echo $this->lang->line('email');?></label>
							</div>
							<div class="col-md-9">
								<p><?php echo $user_store->contact_email ?></p>
							</div>
						</div>
						<!-- <div class="row">
							<div class="col-md-6">
								<label>Customer Service no.</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->customer_service_phone ?></p>
							</div>
						</div> -->
						<!-- <div class="row">
							<div class="col-md-6">
								<label>Customer Service Email</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->customer_service_email ?></p>
							</div>
						</div> -->
                    </div>
                </div>
			<hr>
        </div>
		</div>
		<div class="tab-pane" id="prod">
			<h3><?php echo $this->lang->line('pdt_info');?></h3>
			<hr>
			<!-- <div class="card-body"> -->
					<div class="table-responsive" style="display:block !important;">
							<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table  table table-striped table-bordered datatables">

								<thead>
										<tr>
										<th width="2%"><?php echo $this->lang->line('s_no');?>.&nbsp;</th>
										<th width="5%"><?php echo $this->lang->line('image');?></th>
										<th><?php echo $this->lang->line('created_by');?></th>
										<th><?php echo $this->lang->line('title');?></th>
										<th><?php echo $this->lang->line('category');?></th>
										<th><?php echo $this->lang->line('brand');?></th>
										<th><?php echo $this->lang->line('action');?></th>
										<th><?php echo $this->lang->line('details');?></th>
										<th width="6%"><?php echo $this->lang->line('is_featured');?></th>
										<th width="6%">Block</th>
										<th width="5%"><?php echo $this->lang->line('inventory');?></th>
										</tr>
								</thead>
								<tbody></tbody>
							</table>
					</div>
			<!-- </div> -->
		<hr>
	</div>
	</div>
</div>