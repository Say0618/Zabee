<form action="<?php echo base_url('seller/returnpolicy/add');?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data" autocomplete="off"> 
			<!--<input type="hidden" name="return_id" id="return_id" value="<?php echo $return_id; ?>" />-->
			<div class="row card-header d-flex align-items-center">
				<?php echo $this->lang->line('return_policy');?>
			</div>
			<div class="card-body">
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('return_policy_name');?></label>
					</div>
					<div class="col-sm-4">
						<input type="name" class="form-control Return_policy_name" id = "Return_policy_name" placeholder="<?php echo $this->lang->line('return_policy_name');?>" name="returnPolicyName" value=""/>
						<?php if($this->session->flashdata("return_name_error")){
							echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("return_name_error").'</label>';
						} ?>
					</div>
				
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('return_period');?></label>
					</div>
					<div  class="col-sm-4">
						<input type="period" class="form-control Return_Period" id="Return_Period" placeholder = "<?php echo $this->lang->line('enter_policy_days');?>" name="returnPeriod" value="<?php echo (isset($post_data['returnPeriod']) ? $post_data['returnPeriod'] : ""); ?>"/>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('restocking_fee');?></label>
					</div>
					<div class="col-sm-4">
						<input type="name" class="form-control Restocking_Fee" id = "Restocking_Fee" placeholder="0" name="restockingFee" value="<?php echo (isset($post_data['restockingFee']) ? $post_data['restockingFee'] : ""); ?>"/>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('return');?></label>		
					</div>
					<div class="col-sm-4 mt-2">
						<label><input type = "radio" id="" <?php if(isset($post_data['return_YesNo'])){ if($post_data['return_YesNo'] == "1"){ ?> checked <?php }} ?> class="" name = "return_YesNo" value = "1"/><?php echo $this->lang->line('yes');?></label>
						<label><input type = "radio" id="" <?php if(isset($post_data['return_YesNo'])){ if($post_data['return_YesNo'] == "0"){ ?> checked <?php }} ?> class = "" name = "return_YesNo" value = "0"/><?php echo $this->lang->line('no');?></label><br />
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label"><?php echo $this->lang->line('restocking_type');?></label>
					</div>
					<div  class="col-sm-4 mt-2">
						<label><input type = "radio" id="percent_fixed" <?php if(isset($post_data['percent_fixed'])){ if($post_data['percent_fixed'] == "1"){ ?> checked <?php }} ?> class="percent_fixed" name = "percent_fixed" value = "1"/><?php echo $this->lang->line('percent');?></label>
						<label><input type = "radio" id="percent_fixed" <?php if(isset($post_data['percent_fixed'])){ if($post_data['percent_fixed'] == "0"){ ?> checked <?php }} ?> class = "percent_fixed" name = "percent_fixed" value = "0"/><?php echo $this->lang->line('fixed');?></label><br />
					<div class="clearfix"></div>
					</div>
					<div class="col-sm-2 text-right">
						<label class="control-label resposive-label">RMA Required</label>
					</div>
					<div  class="col-sm-4 mt-2">
						<label><input type = "radio" id="" <?php if(isset($post_data['rma_YesNo'])){ if($post_data['rma_YesNo'] == "1"){ ?> checked <?php }} ?> class="" name = "rma_YesNo" value = "1"/><?php echo $this->lang->line('yes');?></label>
						<label><input type = "radio" id="" <?php if(isset($post_data['rma_YesNo'])){ if($post_data['rma_YesNo'] == "0"){ ?> checked <?php }} ?> class = "" name = "rma_YesNo" value = "0"/><?php echo $this->lang->line('no');?></label><br />
					</div>
					<div class="clearfix"></div>
				</div>
				<hr>
				<div class="row float-right mb-3">
					<button type="reset" class="btn btn-danger resetform" ><?php echo $this->lang->line('reset');?></button>
					<button type="submit" class="btn btn-success  ml-3" id="Submit" name = "Submit" ><?php echo $this->lang->line('save');?></button>
				<div class="clearfix"></div>
			</div>


			</div>
			
</form>