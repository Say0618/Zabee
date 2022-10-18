<form action="<?php echo base_url('seller/configuration/')?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">
		Configuration
	</div>
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for="linktype" class="control-label resposive-label">Option 1:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="" placeholder="Enter Offer Name Here...">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for="linktype" class="control-label resposive-label">Option 2:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="" placeholder="Enter Offer Name Here...">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for="linktype" class="control-label resposive-label">Option 3:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="" placeholder="Enter Offer Name Here...">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for="linktype" class="control-label resposive-label">Option 4:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="" placeholder="Enter Offer Name Here...">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for="linktype" class="control-label resposive-label">Option 5:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="" placeholder="Enter Offer Name Here...">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label for="">Option 6:</label>		
			</div>
			<div class="col-sm-6">
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="customRadioInline1" name="cat_privacy" value="1" class="custom-control-input">
					<label class="custom-control-label" for="customRadioInline1">Yes</label>
				</div>
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="customRadioInline2" name="cat_privacy" checked="checked" value="0" class="custom-control-input">
					<label class="custom-control-label" for="customRadioInline2">No</label>
				</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label">Option 7:</label>
			</div>
			<div class="col-sm-6">
				<select class="form-control" name="position" id="position">
                    <option>Left</option>
                    <option>Right</option>
                </select>
			</div>	
		</div>
		<!-- <div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label" for="focusedInput">Offer Image</label>
			</div>
			<div  class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="offer_image" type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none"><?php echo $this->lang->line('banner_imgerror');?></label>
				<span id="incorrect_file_format" style="display:none;"><label class="error"><?php echo $this->lang->line('banner_incorrectimg');?></label></span>
			</div>
			<div class="clearfix"></div>
		</div>	 -->
		<hr>
		<div class="row float-right mb-3">
			<input type="hidden" value="" id="offer_id" name="offer_id">
			<button type="reset" class="btn btn-danger resetform" >Reset</button>
			<button type="submit" class="btn btn-success ml-3" id="submitBtn" name="submitBtn">Save</button>
			<div class="clearfix"></div>
		</div>
		</div>
	</div>
</form>