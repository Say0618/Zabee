<?php  //echo "<pre>"; print_r($user_store); echo "</pre>"; die(); ?>
<?php // print_r($id_new);die(); ?>
<div id="exTab2" class="container">	
	<div class="tab-content tab-padding">
		<div class="tab-pane" id="">
			<h3><?php echo $this->lang->line('choose_option');?></h3>
		</div>
		<div class="tab-pane active" id="store">
			<h3><?php echo $this->lang->line('buyer_info');?></h3>
			<hr></hr>
			<div class="container emp-profile">
				<div class="row">
                    <div class="col-md-4">
                        <div class="profile-img">
							<?php 
								if(isset($user_data_image->user_pic) && $user_data_image->user_pic != ""){
									$link = profile_path($user_data_image->user_pic).'?'.time();
								} else {
									$link = assets_url('backend/img/images/defaultprofile.png');
								}
							?>
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
								<label><?php echo $this->lang->line('email');?></label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_data->the_email ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label><?php echo $this->lang->line('websites');?></label>
							</div>
							<div class="col-md-6">
								<p><?php echo $user_data->website_name ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label><?php echo $this->lang->line('bio');?></label>
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
		
	</div>
</div>