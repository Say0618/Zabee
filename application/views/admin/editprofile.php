	
	<form id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data">
			<div class="card-body">
				<div class="row">
                 	<div class="col-sm-12">
					<center>
							<div class="image-cropper">
							<div style="position: relative; padding: 0; cursor: pointer;" type="file">
								<?php 
								if(isset($prof->profile_imagelink) && $prof->profile_imagelink != ""){
									$link = image_url()."profile/".$prof->profile_imagelink.'?'.time();
								} else {
									$link = assets_url("backend/img/images/defaultprofile.png");
								}?>
								<img id="myImg" src="<?php echo $link; ?>" type="text" alt="profile picture"  class="rounded"/>
							</div>
						</div>
							<div class="col-sm-12" style="padding-bottom:10px">
							<center>
								<div class="custom-file-upload">
									<input type='file' name="profile_image" accept="image/*" />
								</div>
							</center>
							</div>
					</center>
					</div>
                    <div id="myModal" class="modal imgModal">
                      <span class="close closeModal">&times;</span>
                      <img class="modal-content modContent" id="img01">
                      <div id="caption"></div>
                    </div>					
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label">Public name</label>
					</div>
					<div class="col-sm-4">
						<input type="name" class="form-control" class="Yourpublicname" placeholder = "Your public name" name="yourpublicname" value="<?php echo (isset($_POST['public_name']) ? $_POST['public_name'] : ($prof ? $prof->public_name : '')); ?>"/>
						<?php echo form_error('yourpublicname'); ?>
					</div>
					<div class="col-sm-2 text-right ">
						<label class="form-control-label" >Website</label>
					</div>
					<div class="col-sm-4">
						<input type="url" class="form-control" class="website" placeholder = "Share your website" name="website" value="<?php echo (isset($_POST['website_name']) ? $_POST['website_name'] : ($prof ? $prof->website_name : '')); ?>"/>
						<?php echo form_error('website'); ?>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						 <label class="form-control-label">Email</label>
						
					</div>
					<div class="col-sm-4">
						  <input type="email" class="form-control" class="email" placeholder = "Share an email address" name="email" value="<?php echo (isset($_POST['the_email']) ? $_POST['the_email'] : ($prof ? $prof->the_email : '')); ?>"/>
						<?php echo form_error('email'); ?>
					</div>
					<div class="col-sm-2 text-right ">
						 <label class="form-control-label">Bio</label>
					</div>
					<div class="col-sm-4">
						<textarea type="name" class="form-control" class="Bio" placeholder = "Share a little something about you" name="bio"><?php echo (isset($_POST['the_bio']) ? $_POST['the_bio'] : ($prof ? $prof->the_bio : '')); ?></textarea>
						<?php echo form_error('username'); ?>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label">Facebook</label>
					</div>
					<div class="col-sm-4">
						<input type="name" class="form-control" class="website" placeholder = "http://www.facebook.com/..." name="Facebook" value="<?php echo (isset($_POST['facebook_link']) ? $_POST['facebook_link'] : ($prof ? $prof->facebook_link : '')); ?>"/>
						<?php echo form_error('Facebook'); ?>
					</div>	
					<div class="col-sm-2 text-right">
						<label class="form-control-label" >Twitter</label>
					</div>
					<div class="col-sm-4">
						<input type="url" class="form-control" class="website" placeholder = "http://www.twitter.com/..." name="Twitter" value="<?php echo (isset($_POST['twitter_link']) ? $_POST['twitter_link'] : ($prof ? $prof->twitter_link : '')); ?>"/>
						<?php echo form_error('Twitter'); ?>
					</div>
				</div>	
				<div class="row form-group">
					<div class="col-sm-2 text-right ">
						<label class="form-control-label">Youtube</label>
					</div>
					<div class="col-sm-4">
						<input type="url" class="form-control" class="website" placeholder = "http://www.youtube.com/..." name="Youtube" value="<?php echo (isset($_POST['youtube_link']) ? $_POST['youtube_link'] : ($prof ? $prof->youtube_link : '')); ?>"/>
						<?php echo form_error('Youtube'); ?>
					</div>
					<div class="col-sm-2 text-right">
						<label class="form-control-label">Pinterest</label>
					</div>
					<div class="col-sm-4">
						<input type="url" class="form-control" class="website" placeholder = "http://www.pinterest.com/..." name="Pinterest" value="<?php echo (isset($_POST['pinterest_link']) ? $_POST['pinterest_link'] : ($prof ? $prof->pinterest_link : '')); ?>"/>
						<?php echo form_error('Pinterest'); ?>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right ">
						<label class="form-control-label">Instagram</label>
					</div>
					<div class="col-sm-4">
						<input type="url" class="form-control" class="website" placeholder = "http://www.instagram.com/..." name="Instagram" value="<?php echo (isset($_POST['instagram_link']) ? $_POST['instagram_link'] : ($prof ? $prof->instagram_link : '')); ?>"/>
						<?php echo form_error('Instagram'); ?>
					</div>
				</div>
				<hr>
				<div class="row form-group">
                	<div class="col-sm-12">
						<div class="pull-right">
							<a href="<?php echo base_url('seller/dashboard'); ?>" class="btn btn-danger" style="margin: 0px 14px 14px;">Back to Dashboard</a>
						</div>
						<div class="pull-right">
							<input type="hidden" name="profile_id" value="<?php echo (($profile_id == '')?'':$profile_id); ?>">
							<button type="submit" class="btn btn-primary" id="Submit" style="margin: 0px 14px 14px;">Update</button>
						</div>
					</div>
                </div>
			</div>
	</form>
</div>