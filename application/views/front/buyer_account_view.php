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
			<form id="buyerform" action="<?php echo base_url();?>buyer/save_account" enctype="multipart/form-data" method="post">
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
		<div class="w-100">
			<span class="Personal-info-text"><?php echo $this->lang->line('Personal-info-text');?></span>
			<a class="edit-link" href="javascript:void(0)" onclick="editfunc()"><?php echo $this->lang->line('edit');?>
				<i class="far fa-edit"></i>
			</a>
			<a class="edit-link" href="<?php echo base_url('buyer/delete_account'); ?>" onclick="editfunc()"><?php echo $this->lang->line('delete');?>
				<i class="fa fa-times"></i>
			</a>
		</div>
		<hr>
		<div class="row form-group">
			<div class="col-lg-3 col-6 text-right">
				<label class="form-control-label resposive-label forgot-label label-lh text-right td_heading"> <?php echo $this->lang->line('first_name');?>:</label>
			</div>
			<div class="col-lg-5 col-6">
				<span class="editSpan inputText textLineHeight"><?php echo $user->firstname; ?></span>
				<input type= "text" class="editable-field d-none form-control inputText" id="firstname" name="firstname" value="<?php echo $user->firstname; ?>" required/>
				<?php echo form_error('firstname'); ?>
			</div>
			<span class="error"></span>
		</div>
		<div class="row form-group">
			<div class="col-lg-3 col-6 text-right">
				<label class="form-control-label resposive-label forgot-label label-lh text-right td_heading"> <?php echo $this->lang->line('last_name');?>:</label>
			</div>
			<div class="col-lg-5 col-6">
				<span class="editSpan inputText textLineHeight"><?php echo $user->lastname; ?></span>
				<input type= "text" class="d-none form-control editable-field inputText" id="lastname" name="lastname" value="<?php echo $user->lastname; ?>"  required/>
				<?php echo form_error('lastname'); ?>
			</div>
			<span class="error"></span>
		</div>
		<div class="row form-group">
			<div class="col-lg-3 col-6 text-right">
				<label class="form-control-label resposive-label forgot-label label-lh text-right td_heading"><?php echo $this->lang->line('email');?>:</label>
			</div>
			<div class="col-lg-9 col-6">
			<span class="inputText textLineHeight"><?php echo $user->email; ?></span>
				<input type= "Email" class="d-none bg-transparent form-control inputText" id="Email" name="Email" value="<?php echo $user->email; ?>"readonly required/>
			</div>
			<span class="error"></span>
		</div>
		<div class="row form-group">
			<div class="col-lg-3 col-6 text-right">
				<label class="form-control-label resposive-label forgot-label label-lh text-right td_heading pass_heading"><?php echo $this->lang->line('current_pw');?>:</label>
			</div>
			<div class="col-lg-4 col-5">
				<input class="border-0 form-control bg-transparent inputText"  type="password" value="<?php echo $user->password; ?>" id="myInput" readonly required/>
			</div>
			<div class="col-lg-3 col-1 p-0">
				<a class="editbtn d-none" href="<?php echo base_url('change_password');?>"><i class="far fa-edit"></i></a>
			</div>
				<span class="error"></span>
		</div>
		<div class="savebtn d-none mb-5 col-10 offset-2 mt-5">
			<input type="hidden" name="profile_id" value="<?php echo (($profile_id == '')?'':$profile_id); ?>">
			<input type="submit" class="btn btn-primary" name="submit" value="<?php echo $this->lang->line('save');?>">
			<a href="<?php echo base_url('account'); ?>" class="btn btn-light border border-dark" title="cancel"><?php echo $this->lang->line('cancel');?></a>
		</div>
	</form>
	</div>		
	</div>
</div>
