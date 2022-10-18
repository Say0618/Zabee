
<form action="<?php echo base_url('seller/subcategories/createsubcategories');?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Sub-Category Name</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control focused subcategory_name" id="focusedInput" name="subcategory_name" type="text" value="" placeholder="Enter Sub-Category name">	
				<?php if($this->session->flashdata("subcategory_name_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("subcategory_name_error").'</label>';
				} ?>		
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Sub-Category Image(optional)</label>
			</div>
			<div class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="profile_image" type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none">Please select sub-category image</label>
				<span id="incorrect_file_format" style="display:none;"><label class="error">Can't update with an incorrect file format.</label></span>
			</div>
		<div class="clearfix"></div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Parent Category</label>
			</div>
			<div class="col-sm-6">
				<?php 
					if($parentCategories)
					{
				?>
					<select name = "parent_category_id" class = "parent_category form-control" id="category_id">
					<?php
						foreach($parentCategories as $parent)
						{
							echo "<option value = '".$parent['category_id']."'>".$parent['category_name']."</option>\";";
						}
					?>
					</select>
					
					<?php
						}
						else
						{
							echo "<span style = \'color:red;\'>No Category found.</span>Please <a href =".base_url().'seller/categories'.">Add Category</a>";
						}
					?>
					<span class="myerror"><?php echo form_error("parent_category_id"); ?></span>
			</div>
			<div class="col-sm-3"></div>
			<div class="col-sm-9">
			<label for="parent_category_id" class="error" id="parent_category_id-error" style="display:none">Please select an option..</label>
			<div class="clearfix"></div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Status</label>		
			</div>
			<div class="col-sm-4">
				<p class='mt-2'>
					<label><input type = "radio" id="radioEnable" class="display_status customRadio" checked="checked" name = "subdisplay_status" value = "1" />Enable</label>
					<label><input type = "radio" id="radioDisable" class = "display_status customRadio" name = "subdisplay_status" value = "0"/>Disable</label>
				</p>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="">private</label>		
			</div>
			<div class="col-sm-4">
				<input type="checkbox" id="cat_privacy" name = "cat_privacy" data-toggle="toggle">
			</div>
			
			<div class="clearfix"></div>
		</div>
		<hr>
		<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger" >Reset</button>
					<button type="submit" class="btn btn-success  ml-3" id="Submit" name="Submit">Save</button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	
	