<div id="buyerAccount-page" class="container">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>base_url()."account","cat_name"=>$this->lang->line('profile'))))); ?>
	<div class="row">   
		<div class="col-12 col-sm-12 col-md-12 mb-5 background-light-color">
		<?php if(isset($_GET['status'])){?>
			<div class="alert alert-success alert-dismissable fade show" align="center" >
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong><?php echo $this->lang->line('success');?>!</strong> Profile Updated Successfully.
			</div>
		<?php }elseif($this->session->flashdata('changed')){?>
			<div class="alert alert-success alert-dismissable fade show" align="center" >
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong><?php echo $this->lang->line('success');?>!</strong> Password Updated Successfully.
			</div>
		<?php } ?>
			<div class="row mb-3">
				<h4 class="col-4"><?php echo $this->lang->line('profile');?></h4>
			</div>
			<?php
				if(isset($user->user_pic) && $user->user_pic !=""){
					$url = profile_path($user->user_pic.'?'.time()); 
				}else{
					$url = profile_path("defaultprofile.png");
				}
			?>
			<form id="buyerform" action="<?php echo base_url();?>buyer/delete_account" enctype="multipart/form-data" method="post">
			<div class="row">
				<div class="col-sm-2">
					<div class="edit editLabel">
						<label for="upload_img" class="btn btn-sm d-none text-color ml-3" id="img-label">
							<input id="upload_img" name="profile_image"  type="file" accept="image/*" hidden>
							<i class="far fa-edit"></i>
						</label>
					</div>
					<img id="user_img" alt="User Pic" src="<?php echo $url ?>" class="rounded-circle img-fluid" > 
				</div>
				<div class="col-sm-6 my-auto">
				<h3 class="hello-user"><?php echo $this->lang->line('hello');?>,<?php echo isset($user->firstname)?$user->firstname:"";?>!</h3>
			</div>
		</div>
		<hr>
		<div class="row form-group">
			<div class="">
				<label class="form-control-label resposive-label forgot-label label-lh text-center td_heading">Are you sure you want to delete account?</label>
			</div>
		</div>
		<div class="savebtn mb-5 col-10 offset-2 mt-5">
			<input type="hidden" name="profile_id" value="<?php echo (($profile_id == '')?'':$profile_id); ?>">
			<input type="submit" class="btn btn-primary" name="submit" value="<?php echo $this->lang->line('delete');?>">
			<a href="<?php echo base_url('account'); ?>" class="btn btn-light border border-dark" title="cancel"><?php echo $this->lang->line('cancel');?></a>
		</div>
	</form>
	</div>		
	</div>
</div>
