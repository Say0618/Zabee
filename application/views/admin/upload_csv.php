<form action="<?php echo base_url('seller/import_csv/add/');?>" id="uploadCSV" name="uploadCSV" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">Add CSV</div>
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label" for="focusedInput">Select Seller</label>
			</div>
			<div class="col-sm-6">
				<select name="seller_id" id="seller_id" class="form-control">
					<option value=""> Select Seller</option>
					<?php if(count($sellerList)>0){ foreach($sellerList as $seller){ ?>
					<option value="<?php echo $seller->id; ?>"><?php echo $seller->seller_name; ?></option>
					<?php } } ?>
				</select>
				<!--<input type="hidden" name="seller_id" id="seller_id" value="" />-->
				<label class="error_text"><?php echo form_error('seller_id'); ?></label>
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3 ">
				<label class="control-label resposive-label" for="csv_file">CSV File</label>
			</div>
			<div  class="col-sm-6">
				<input class="form-control" id="csv_file" name="csv_file" type="file" accept=".csv">
				<label class="error_text"><?php echo form_error('csv_file'); ?><label>
			</div>
			<div class="clearfix"></div>
		</div>	
		<hr>
		<input type="hidden" name="import_id" id="import_id">
		<div class="row float-right mb-3">
			<!-- <button type="reset" >Reset</button> -->
			<a href="<?php echo $_SERVER['REDIRECT_URL'] ?>" class="btn btn-danger" >Reset</a>
			<button type="submit" class="btn btn-success  ml-3" id="Submit">Save</button>
			<div class="clearfix"></div>
		</div>
	</div>
</form>
	