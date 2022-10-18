<form action="<?php echo base_url('seller/variantcategories/variant_update/'.$variantWeGot->v_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<?php //print_r($variantWeGot->v_id);die(); ?>
	<div class="row card-header d-flex align-items-center">
		<?php echo $this->lang->line('add_variant');?>
	</div>
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('variant_name');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control variant_category_name" id="keyword" name="variant_name" type="text" value="<?php echo $variantWeGot->v_title;  ?>" placeholder="Enter Variant Name">	
				<br /><?php if($this->session->flashdata("variant_name_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("variant_name_error").'</label>';
				} ?>	
			</div>
			<div class="clearfix"></div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('status');?></label>		
			</div>
			<div class="col-sm-4">
				<p class='mt-2'>
					<label><input type = "radio" id="radioEnable" <?php echo ($variantWeGot->is_active == 1)?'checked ':''; ?> class="variant_status customRadio" name = "variant_status" value = "1"/><?php echo $this->lang->line('enable');?></label>
					<label><input type = "radio" id="radioDisable" <?php echo ($variantWeGot->is_active == 0)?'checked ':''; ?> class = "variant_status customRadio" name = "variant_status" value = "0"/><?php echo $this->lang->line('disable');?></label>
				</p>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr>
		<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger reset_btn" ><?php echo $this->lang->line('reset');?></button>
					<button type="submit" class="btn btn-success ml-3" id="Submit" name="Submit"><?php echo $this->lang->line('update');?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	