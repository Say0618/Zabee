<?php $brand_id = ($brand_data && $brand_data->brand_id)?$brand_data->brand_id:""; ?>
<form action="<?php echo base_url('seller/brands/form/'.$brand_id)?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group">
				<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Name</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control focused brand_name" id="focusedInput" name="brand_name" type="text" value="<?php echo (isset($_POST['brand_name']) ? $_POST['brand_name'] : ($brand_data ? $brand_data->brand_name : '')); ?>" placeholder="Enter Brand name">	<?php if($this->session->flashdata("brand_name_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("brand_name_error").'</label>';
				} ?>				
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Image</label>
			</div>
			<div class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="profile_image[]" type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none">Please select brand image</label>
				<span id="incorrect_file_format" style="display:none;"><label class="error">Can't update with an incorrect file format.</label></span>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Description</label>		
			</div>
			<div class="col-sm-6">
				<textarea  class="input-large focused brand_description form-control" name="brand_description" placeholder="  Describe Brand here..." ><?php echo (isset($_POST['brand_description']) ? $_POST['brand_description'] : ($brand_data ? $brand_data->brand_description : '')); ?></textarea >
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Status</label>		
			</div>
			<div class="col-sm-4">
				<div class='mt-2'>
					<input type = "radio" id="radioEnable" class="branddisplay_status customRadio"  name = "display_status" <?php if((isset($post_data['display_status']) && $post_data['display_status'] == "1") || ($brand_data && $brand_data->display_status=="1")){ echo 'checked="checked"'; } ?> value = "1"/>Enable
					<input type = "radio" id="radioDisable" class = "branddisplay_status customRadio" name = "display_status" <?php if((isset($post_data['display_status']) && $post_data['display_status'] == "0") || ($brand_data && $brand_data->display_status=="0")){ echo 'checked="checked"'; } ?> value = "0"/>Disable
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr>
		<div class="row float-right mb-3">
			<input type="hidden" value="<?php echo $brand_id;?>" id="brand_id" name="brand_id">
			<button type="reset" class="btn btn-danger resetform" >Reset</button>
			<button type="submit" class="btn btn-success ml-3" id="submitBtn" name="submitBtn"><?php echo ($brand_id !="")?"Update":"Save"?></button>
			<div class="clearfix"></div>
		</div>
	</div>
</form>