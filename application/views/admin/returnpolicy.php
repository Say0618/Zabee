<style>
label{
		font-weight:bold !important;
	}
</style>
<div class="row">
	<div class="container">
		<form action="<?php echo base_url('admin/returnpolicy');?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
			<!--<input type="hidden" name="return_id" id="return_id" value="<?php echo $return_id; ?>" />-->
			<div class="panel-primary">
				<div class="panel-heading">
					<h4 class="pull-left HeadingColor">&nbsp;
						Return Policy
					</h4>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="panel-body">
			
			<h3>Product return policy</h4><br />
				<div class="row form-group">
					<div class="col-xs-2 text-right">
						<label class="control-label">Return </label>		
					</div>
					<div class="col-xs-3">
						<div class="radio-inline">
							<input type="radio" name="return_YesNo" value="1"/>Yes
						</div>
						<div class="radio-inline">
							<input type="radio" name="return_YesNo" value="0" checked />No
						</div>
					</div>
					<div class="col-xs-2 text-right">
						<label class="control-label" >Return Period</label>
					</div>
					<div  class="col-xs-3">
						<input type="text" class="form-control" class="Return_Period" placeholder = "Enter no. of day(s)" name="ReturnPeriod" value=""/>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					
						<div class="col-xs-2 text-right">
							<label class="control-label" >RMA Required</label>
						</div>
						<div  class="col-xs-3">
							<div class="radio-inline">
								<input type="radio" name="rma_YesNo" value="1"/>Yes
							</div>
							<div class="radio-inline">
								<input type="radio" name="rma_YesNo" value="0" checked />No
							</div>
						</div>
						<div class="col-xs-2 text-right">
							<label class="control-label" >Restocking Fee</label>
						</div>
						<div class="col-xs-3">
							<input type="name" class="form-control" class = "Restocking_Fee" placeholder="0" name="RestockingFee" value=""/>
						</div>
						<div class="clearfix"></div>
					
				</div>
				<div class="row form-group">
						<div class="col-xs-2 text-right">
							<label class="control-label" >Restocking Type</label>
						</div>
						<div  class="col-xs-3">
							<div class="radio-inline">
								<input type="radio" name="percent_fixed" value="percent" checked />Percent
							</div>
							<div class="radio-inline">
								<input type="radio" name="percent_fixed" value="fixed" />Fixed
							</div>
						<div class="clearfix"></div>
						</div>
						<div class="col-xs-2 text-right">
							<label class="control-label" >Return policy name</label>
						</div>
						<div class="col-xs-3">
							<input type="name" class="form-control" class = "Return_policy_name" placeholder="0" name="ReturnPolicyName" value=""/>
						</div>
						<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="col-xs-3 checkbox">
						<?php if($returns == ''){ ?>
						<?php echo "<h3>No return policy with your userid added as of yet</h3>"; } else {?>
					  <?php foreach($returns as $return){ ?>
						<label><input type="checkbox" value="" name="" class="defaultreturn" onclick="location.href='<?php echo base_url('admin/returnpolicy/update/'.$return->id); ?>'"><?php echo $return->returnpolicy_name; ?></label>
					  <?php } ?>
					  <?php } ?>
					</div>
					
					<div class=" pull-right" style="margin: 0px 14px 14px;">
						<button type="" class="btn btn-primary newBtn2" >Back</button>
					</div>
					<div class=" pull-right" style="margin: 0px 14px 14px;">
						<button type="submit" class="btn btn-success newBtn2" id="Submit">Save</button>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
	</div>
		</form>
	</div>
</div>
<script>
$(document).ready(function(){
	 $('#myform').validate({
        rules:{
			return_YesNo:{ required:true },
			rma_YesNo:{ required:true },
			percent_fixed:{ required:true },
			ReturnPeriod:{ required:true },
			RestockingFee:{ required:true }
		},
        messages:
        {
		  return_YesNo:{ required: "Please select an option" },
		  rma_YesNo:{ required: "Please select an option" },
		  percent_fixed:{ required: "Please select an option" },
		  ReturnPeriod:{ required: "Please provide a Return Period" },
		  RestockingFee:{ required: "Please provide Restocking Fee" }
        }
	  });	
});
</script>
