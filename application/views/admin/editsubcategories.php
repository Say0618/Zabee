<form action="<?php echo base_url('seller/subcategories/editedsubcategories/'.$category_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">
		Edit Sub-Category
	</div>
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Category Name</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control focused editsubcategory_name" id="focusedInput" name="editsubcategory_name" type="text" value="<?php echo (isset($_POST['category_name']) ? $_POST['category_name'] : ($categoriesWeGot ? $categoriesWeGot->category_name : '')); ?>" placeholder="Enter Category name">		
				<?php if($this->session->flashdata("category_name_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("category_name_error").'</label>';
				} ?>		
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Sub-Category Image(Optional)</label>
			</div>
			<div  class="col-sm-6">
				<div class="image-cropper-subcat">
					<?php if(isset($categoriesWeGot->category_image) && $categoriesWeGot->category_image != ""){ ?>	
					<label class="control-label resposive-label" for="focusedInput">Uploaded Image</label>
					<input type="hidden" id="cat_id" value="<?php echo $categoriesWeGot->category_id ?>" />
					<?php $link = base_url()."uploads/categories/".$categoriesWeGot->category_image; ?>
                        <div class="img-container">
							<img id="myImg" src="<?php echo $link; ?>" type="text" class="rounded" style = "width:50%; max-height: 165px;object-fit:contain;"/>
							<button class="cross-btn">&times;</button>
						</div><br>
                    <?php }?>
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="profile_image" type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none">Please select sub-category image</label>
				<span id="incorrect_file_format" style="display:none;"><label class="error">Can't update with an incorrect file format.</label></span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group ">
			<div class="col-sm-3 text-right ">
				<label class="control-label resposive-label" for="">Parent Category</label>
			</div>
			<div class="col-sm-6 patcat">
			<?php 
				if($parentCategories)
				{
			?>
            	<select name = "editparent_category_id" class = "parent_category form-control" id="category_id">
				<?php
					foreach($parentCategories as $parent)
					{
						if($categoriesWeGot->parent_category_id == $parent['category_id']){
							$selected = "selected='selected'";
						}else{
							$selected = "";
						}	
						echo "<option ".$selected." value = '".$parent['category_id']."'>".$parent['category_name']."</option>\";";
					}
				?>
				</select>
				<?php
					}
					else
					{
						echo "<span style = \'color:red;\'>No Category found.</span>Please <a href = \''.base_url().'seller/categories\'>Add Category</a>";
					}
				?>
			</div>
			<div class="col-sm-3"></div>
			<div class="col-sm-9">
			<label for="editparent_category_id" class="error" id="editparent_category_id-error" style="display:none">Please select an option.</label>
			<div class="clearfix"></div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Status</label>		
			</div>
			<div class="col-sm-4">
				<p class='mt-2'>
					<label><input type = "radio" id="radioEnable"  <?php echo ($categoriesWeGot->display_status == 1)?'checked ':''; ?> class="display_status customRadio" name = "editdisplay_status" value = "1"/>Enable</label>
					<label><input type = "radio" id="radioDisable" <?php echo ($categoriesWeGot->display_status== 0)?'checked ':''; ?> class = "display_status customRadio" name = "editdisplay_status" value = "0"/>Disable</label>
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
					<button type="submit" class="btn btn-success  ml-3" id="Submit" name="Submit">Update</button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	
	