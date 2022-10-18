<?php  //echo "<pre>"; print_r($user_store); echo "</pre>"; die(); ?>
<?php // print_r($id_new);die(); ?>
<div id="exTab2" class="container">	
	<ul class="nav nav-tabs ul-padding-for-userdetails">
		<?php if($position != "Buyer"){ ?>
		<li class="tabs-css active">
			<a  class="user-user-details" href="#store" data-toggle="tab"><?php echo $position; ?></a>
		</li>&nbsp;
		<li class="tabs-css">
			<a class="user-store-details" href="#user" data-toggle="tab">Store</a>
		</li>&nbsp;
		<li class="tabs-css">
			<a class="user-prod-details" href="#prod" data-toggle="tab">Product(s)</a>
		</li>&nbsp;
		<?php } ?>
	</ul>
	<div class="tab-content tab-padding">
		<div class="tab-pane" id="">
			<h3>Choose an option</h3>
		</div>
		<div class="tab-pane active" id="store">
			<h3><?php echo $position; ?> Information</h3>
			<hr></hr>
			<div class="container emp-profile">
				<div class="row">
                    <div class="col-md-4">
                        <div class="profile-img">
							
							<?php 
								if($position == "Seller"){
									if(isset($user_data->profile_imagelink) && $user_data->profile_imagelink != ""){
										$link = profile_path($user_data->profile_imagelink).'?'.time();
									} else {
										$link = assets_url("backend/img/images/defaultprofile.png");
									}
								} else if($position == "Buyer") {
									if(isset($user_data_image->user_pic) && $user_data_image->user_pic != ""){
										$link = profile_path($user_data_image->user_pic).'?'.time();
									} else {
										$link = assets_url("backend/img/images/defaultprofile.png");
									}
								}?>
                         <img class="card-img-top" src="<?php echo $link; ?>" alt="image" style="width:100%">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-head">
							<h5><?php echo $user_data->public_name ?></h5>
							<div class="">
							</div>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-8 padding-top-info">
						<div class="row">
							<div class="col-md-6">
								<label>Email</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_data->the_email ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Website</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_data->website_name ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Bio</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_data->the_bio ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Facebook</label>
							</div>
							<div class="col-md-6">
								<p><?php echo ($user_data->facebook_link != "") ? $user_data->facebook_link : " -- ";?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Twitter</label>
							</div>
							<div class="col-md-6">
								<p><?php echo ($user_data->twitter_link != "") ? $user_data->twitter_link : " -- ";?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Youtube</label>
							</div>
							<div class="col-md-6">
								<p><?php echo ($user_data->youtube_link != "") ? $user_data->youtube_link : " -- ";?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Pinterest</label>
							</div>
							<div class="col-md-6">
								<p><?php echo ($user_data->pinterest_link != "") ? $user_data->pinterest_link : " -- ";?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Instagram</label>
							</div>
							<div class="col-md-6">
								<p><?php echo ($user_data->instagram_link != "") ? $user_data->instagram_link : " -- ";?></p>
							</div>
						</div>
                    </div>
                    
                </div>
        </div>
		<hr></hr>
		</div>
		<?php if($position != "Buyer"){ ?>
		<div class="tab-pane" id="user">
			<h3>Store Information</h3>
			<div class="container emp-profile">
				<div class="row">
                    <div class="col-md-4">
                        <div class="profile-img">
							<?php 
								if(isset($user_store->store_logo) && $user_store->store_logo != ""){
									$link = store_logo_path($user_store->store_logo).'?'.time();
								} else {
									$link = assets_url("backend/img/images/store-logo.png");
								}?>
                         <img class="card-img-top" src="<?php echo $link; ?>" alt="image" style="width:100%">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-head">
							<h5><?php echo $user_store->store_name ?></h5>
							 <div class="">
							</div>
						</div>
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-md-8 padding-top-info">
						<div class="row">
							<div class="col-md-6">
								<label>Store ID</label>
							</div>
							<div class="col-md-6">
								<p> <?php echo $user_store->store_id ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Store Address</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->store_address ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Business Type</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->legal_busniess_type ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Country</label>
							</div>
							<div class="col-md-6">
								<?php
									foreach($countryList as $country){
										if($user_store->country_id == $country->id){
											echo $country->nicename;
										}
									} 
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Contact Person</label>
							</div>
							<div class="col-md-6">
								<p> <?php echo $user_store->contact_person ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Contact no.</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->contact_phone ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Email</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->contact_email ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Customer Service no.</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->customer_service_phone ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Customer Service Email</label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_store->customer_service_email ?></p>
							</div>
						</div>
                    </div>
                </div>
			<hr></hr>	
        </div>
		</div>
		<div class="tab-pane" id="prod">
			<h3>Product Information</h3>
			<hr></hr>
			<div class="card-body">
				<div class="table-responsive" style="display:block !important;">
					<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
						<thead>
							<tr>
								<th width="1%">Sno.&nbsp;</th>
							<th align="center">Created By</th>
							<th align="center">Name</th>
							<th align="center">Image</th>
							<th align="center">Category</th>
							<th align="center">Brand</th>
							<th align="center">Condition</th>
							<th align="center">Price</th>
							<th align="center">Status</th>
							<th align="center">Show</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		<hr></hr>
		</div>
		<?php } ?>
	</div>
</div>