<form action="<?php echo base_url('seller/reviews/save/')?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Product Name</label>
			</div>
			<div  class="col-sm-6">
                    <input class="form-control product_name" id="keyword" name="product_name" value="<?php echo set_value('product_name'); ?>" type="text"  placeholder="<?php echo $this->lang->line('enter_product_info');?>">
                    <?php echo form_error('product_name'); ?>
                </div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Store</label>
			</div>
			<div  class="col-sm-6">
				<select name="store" id="store" class="form-control" readonly></select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Condition</label>
			</div>
			<div  class="col-sm-6">
				<select name="condition" id="condition" class="form-control" readonly></select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Variant</label>
			</div>
			<div  class="col-sm-6">
				<select name="variant" id="variant" class="form-control" readonly></select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Reviewer Name</label>
			</div>
			<div  class="col-sm-6">
				<input type="text" class="form-control" id="name" name="name" value=""/>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Review Date</label>
			</div>
			<div  class="col-sm-6">
			<input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="reviewDate" name="reviewDate" style="cursor:pointer; background-color:#fff;" readonly />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Review</label>
			</div>
			<div  class="col-sm-6">
				<textarea class="form-control" id="review" placeholder="<?php echo $this->lang->line('characters255');?>" name="review" value="" rows="6"></textarea>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Rating</label>
			</div>
			<div class="col-sm-6">
				<div class='text-center' id='rateYo' name = "ratYo"></div>
				<span class="rateYo_error" id="rateYo_error"></span>
                <input type='hidden' name='rating' id="rating" value='0' />
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Upload Images</label>
			</div>
			<div class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="profile_image[]" multiple type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none">Please select brand image</label>
				<span id="incorrect_file_format" style="display:none;"><label class="error">Can't update with an incorrect file format.</label></span>
			</div>
		</div>
		<hr>
		<div class="row float-right mb-3">
			<input type="hidden" value="" id="product_id" name="product_id">
			<input type="hidden" value="" id="seller_id" name="seller_id">
			<input type="hidden" value="" id="condition_id" name="condition_id">
			<input type="hidden" value="" id="pv_id" name="pv_id">
			<input type="hidden" value="" id="sp_id" name="sp_id">
			<a href="<?php echo base_url("seller/review")?>" class="btn btn-default" >Back</a>
			<button type="reset" class="btn btn-danger resetform ml-3" >Reset</button>
			<button type="submit" class="btn btn-success ml-3" id="submitBtn" name="submitBtn">Add</button>
			<div class="clearfix"></div>
		</div>
	</div>
</form>