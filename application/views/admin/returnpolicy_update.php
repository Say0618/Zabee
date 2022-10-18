<?php //print_r($post_data); ?>
<?php //print_r($_POST); ?>
<form action="<?php echo base_url('seller/returnpolicy/update/'.$return_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data" autocomplete="off"> 
			<div class="row card-header d-flex align-items-center">
				<?php echo $this->lang->line('return_policy');?>
			</div>
			<div class="card-body">
			
			<!-- <h3>Product return policy</h4><br /> -->
				<div class="row form-group">
						<div class="col-sm-2 text-right">
						<label class="control-label resposive-label" ><?php echo $this->lang->line('return_policy_name');?></label>
					</div>
					<div class="col-sm-4">
						<input type="name" class="form-control Return_policy_name" placeholder="Return policy name" name="returnPolicyName" value="<?php echo (isset($_POST['returnpolicy_name']) ? $_POST['returnpolicy_name'] : ($return_policy ? $return_policy->returnpolicy_name : '')); ?>"/>
						<?php if($this->session->flashdata("return_name_error")){
							echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("return_name_error").'</label>';
						} ?>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label" ><?php echo $this->lang->line('return_period');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="text" class="form-control Return_Period" placeholder = "Enter no. of day(s)" name="returnPeriod" value="<?php echo (isset($_POST['return_period']) ? $_POST['return_period'] : ($return_policy ? $return_policy->return_period : '')); ?>"/>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label" ><?php echo $this->lang->line('restocking_fee');?></label>
					</div>
					<div class="col-sm-4">
						<input type="name" class="form-control Restocking_Fee" placeholder="0" name="restockingFee" value="<?php echo (isset($_POST['restocking_fee']) ? $_POST['restocking_fee'] : ($return_policy ? $return_policy->restocking_fee : '')); ?>"/>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('return');?> </label>		
					</div>
					<div class="col-sm-4 mt-2">
						<!-- <p class='container'> -->
							<label><input type = "radio" id="" <?php echo ($return_policy->returns == 1)?'checked ':''; ?> class="" name = "return_YesNo" value = "1"/>Yes</label>
							<label><input type = "radio" id="" <?php echo ($return_policy->returns == 0)?'checked ':''; ?> class = "" name = "return_YesNo" value = "0"/>No</label>
						<!-- </p>	 -->
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="control-label resposive-label"><?php echo $this->lang->line('restocking_type');?></label>
						</div>
						<div  class="col-sm-4 mt-2">
							<!-- <p class='container'> -->
								<?php //print_r($return_policy); ?>
								<label><input type = "radio" id="percent_fixed" <?php echo ($return_policy->restocking_type == '1')?'checked ':''; ?> class="percent_fixed" name = "percent_fixed" value = "1"/>Percent</label>
								<label><input type = "radio" id="percent_fixed" <?php echo ($return_policy->restocking_type == '0')?'checked ':''; ?> class = "percent_fixed" name = "percent_fixed" value = "0"/>Fixed</label>
							<!-- </p>	 -->
						<div class="clearfix"></div>
						</div>
						<div class="col-sm-2 text-right">
							<label class="control-label resposive-label"><?php echo $this->lang->line('rma_required');?></label>
						</div>
						<div  class="col-sm-4 mt-2">
							<!-- <p class='container'> -->
								<label><input type = "radio" id="" <?php echo ($return_policy->rma_required == 1)?'checked ':''; ?> class="" name = "rma_YesNo" value = "1"/><?php echo $this->lang->line('yes');?></label>
								<label><input type = "radio" id="" <?php echo ($return_policy->rma_required == 0)?'checked ':''; ?> class = "" name = "rma_YesNo" value = "0"/><?php echo $this->lang->line('no');?></label>
							<!-- </p>	 -->
						</div>
					<div class="clearfix"></div>
				</div>
				<hr>
				<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger resetform" ><?php echo $this->lang->line('reset');?></button>
					<button type="submit" class="btn btn-success  ml-3" id="Submit" name="Submit"><?php echo $this->lang->line('update');?></button>
				<div class="clearfix"></div>
			</div>
			</div>
			
</form>
	