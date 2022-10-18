<form action="<?php echo base_url('seller/variantcategories/editedcategories/'.$category_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">
		<?php echo $this->lang->line('edit_variant_category');?>
	</div>
	<div class="card-body">
		<div class="row form-group">
				<div class="col-sm-3 text-right">
				<label class="control-label" for="focusedInput"><?php echo $this->lang->line('variant_category_name');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control focused variant_category_name_update" id="focusedInput" name="variant_category_name_update" type="text" value="<?php echo (isset($_POST['v_cat_title']) ? $_POST['v_cat_title'] : ($categoriesWeGot ? $categoriesWeGot->v_cat_title : '')); ?>" placeholder="<?php echo $this->lang->line('enter_variant_category_name');?>">	
				<div>
					<?php if($this->session->flashdata("variant_name_error")){
						echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("variant_name_error").'</label>';
					} ?>				
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr>
		<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger reset_btn" ><?php echo $this->lang->line('reset');?></button>
					<button type="submit" class="btn btn-success ml-3" id="Submit" name = "Submit"><?php echo $this->lang->line('update');?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	