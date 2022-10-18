<form id="myform" action="<?php echo base_url('seller/discount/update/'.$policy_id);?>" name="myForm" novalidate method="post" enctype="multipart/form-data">
			<div class="row card-header d-flex align-items-center">
				<?php echo $this->lang->line('discount_policy');?>
			</div>
			<?php //print_r($policies_update);die(); ?>
			<div class="card-body">
				<div class="row form-group">

					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label">Title</label>
					</div>
					<div  class="col-sm-10">
						<input type="text" class="form-control" placeholder="Discount Title" name="title" style="background-color:#fff;" value="<?php echo (isset($_POST['title']) ? $_POST['title'] : ($policies_update ? $policies_update->title : '')); ?>" />
					</div>

					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('valid_from');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="txtFromDate" name="fromDate" style="cursor:pointer; background-color:#fff;" value="<?php echo (isset($_POST['valid_from']) ? $_POST['valid_from'] : ($policies_update ? $policies_update->valid_from : '')); ?>" readonly />
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('valid_to');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="txtToDate" name="toDate" style="cursor:pointer; background-color:#fff;" value="<?php echo (isset($_POST['valid_to']) ? $_POST['valid_to'] : ($policies_update ? $policies_update->valid_to : '')); ?>" readonly />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label" ><?php echo $this->lang->line('type');?></label>
					</div>
					<div  class="col-sm-4 mt-2">
						<p class='container'>
							<label><input type = "radio" class = "discount_type" <?php echo ($policies_update->type == "fixed")?'checked ':''; ?> id="Fixed" name = "FixedorPercent" value = "fixed"/>Fixed</label>&nbsp;&nbsp;
							<label><input type = "radio" class = "discount_type" <?php echo ($policies_update->type == "percent")?'checked ':''; ?> id="Percent" name = "FixedorPercent" value = "percent"/><?php echo $this->lang->line('percent');?></label>
						</p>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label" ><?php echo $this->lang->line('value');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control" id="valueOfPercent" placeholder = "<?php echo $this->lang->line('value');?>" name="valueOfPercentOrFixed" value="<?php echo (isset($_POST['value']) ? $_POST['value'] : ($policies_update ? $policies_update->value : '')); ?>" />
					</div>
				</div>
				<hr>
					<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger resetform" ><?php echo $this->lang->line('reset');?></button>
					<button type="submit" class="btn btn-success  ml-3" id="Submit"><?php echo $this->lang->line('save');?></button>
				<div class="clearfix"></div>
			</div>
				</div>
			</div>
</form>