<?php $category_id = ($category_data && $category_data->category_id)?$category_data->category_id:"";//print_r($post_data); ?>
<form action="<?php echo base_url('seller/categories/form/'.$category_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('title');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="keyword" name="category_name" type="text" value="<?php echo (isset($_POST['category_name']) ? $_POST['category_name'] : ($category_data ? $category_data->category_name : '')); ?>" placeholder="Enter Category name">	
				<?php if($this->session->flashdata("category_name_error")){
					echo '<label class="return-error error">'.$this->session->flashdata("category_name_error").'</label>';
				} ?>
			</div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Slug:</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="slug_name" name="slug" type="text" value="<?php echo (isset($_POST['slug']) ? $_POST['slug'] : ($category_data ? strtolower($category_data->category_name) : '')); ?>" placeholder="Enter Slug">	
				<?php if($this->session->flashdata("slug_name_error")){
					echo '<label class="return-error error">'.$this->session->flashdata("slug_name_error").'</label>';
				} ?>
			</div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('parent_category');?></label>
			</div>
			<div class="col-sm-6">
				<?php 
					if($parentCategories)
					{
				?>
					<select name = "parent_category_id" class = "parent_category form-control select2" id="category_id">
						<option value="0">-<?php echo $this->lang->line('select_parent');?>-</option>
					<?php
						foreach($parentCategories as $parent)
						{
							if($category_data && $category_data->parent_category_id == $parent['category_id']){
								$select = 'selected="selected"';
							}else{
								$select = "";
							}
							if($category_data && $category_data->category_id != $parent['category_id']){
								echo "<option value = '".$parent['category_id']."' ".$select.">".$parent['category_name']."</option>";
							}else if(empty($category_data)){
								echo "<option value = '".$parent['category_id']."' ".$select.">".$parent['category_name']."</option>";
							}
						}
					?>
					</select>
					
					<?php
						}
						else
						{
							echo "<span style = \'color:red;\'><?php echo $this->lang->line('no_cat_found');?></span>Please <a href =".base_url().'seller/categories'."><?php echo $this->lang->line('add_category');?></a>";
						}
					?>
					<span class="myerror"><?php echo form_error("parent_category_id"); ?></span>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Category Link</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_url" name="category_url" type="text" value="<?php echo (isset($_POST['category_link']) ? $_POST['category_link'] : ($category_data ? $category_data->category_link : '')); ?>" placeholder="Enter Category Link">	
			</div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('image');?></label>
			</div>
			<div class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="profile_image[]" type="file" accept="image/*">
				</div>
				<span id="incorrect_file_format" style="display:none;"><label class="error"><?php echo $this->lang->line('banner_incorrectimg');?></label></span>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for=""><?php echo $this->lang->line('private');?></label>		
			</div>
			<div class="col-sm-6">
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="customRadioInline1" name="cat_privacy" <?php if($category_data && $category_data->is_private=="1"){ echo 'checked="checked"'; } ?> value="1" class="custom-control-input">
					<label class="custom-control-label" for="customRadioInline1"><?php echo $this->lang->line('yes');?></label>
				</div>
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="customRadioInline2" name="cat_privacy" <?php if($category_data && $category_data->is_private=="0"){ echo 'checked="checked"'; }else if(empty($category_data)){echo 'checked="checked"';} ?> value="0" class="custom-control-input">
					<label class="custom-control-label" for="customRadioInline2"><?php echo $this->lang->line('no');?></label>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for=""><?php echo $this->lang->line('show_on_home');?></label>		
			</div>
			<div class="col-sm-6">
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="customRadioInline3" name="is_homepage" <?php if($category_data && $category_data->is_homepage=="1"){ echo 'checked="checked"'; }else if(empty($category_data)){echo 'checked="checked"';} ?> value="1" class="custom-control-input">
					<label class="custom-control-label" for="customRadioInline3"><?php echo $this->lang->line('yes');?></label>
				</div>
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="customRadioInline4" name="is_homepage" <?php if($category_data && $category_data->is_homepage=="0"){ echo 'checked="checked"'; } ?> value="0" class="custom-control-input">
					<label class="custom-control-label" for="customRadioInline4"><?php echo $this->lang->line('no');?></label>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="offset-sm-3 col-sm-6 text-right">
				<input type="hidden" value="<?php echo $category_id?>" id="category_id" name="category_id">
				<a href="<?php echo base_url("seller/categories");?>" class="btn btn-zabee-default" ><?php echo $this->lang->line('back');?></a>
				<button type="submit" class="btn btn-zabee ml-3" id="submitBtn" name = "submitBtn"><?php echo ($category_id !="")?$this->lang->line('update'):$this->lang->line('save')?></button>
			</div>
		</div>
	</div>
</form>
	