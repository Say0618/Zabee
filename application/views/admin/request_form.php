<?php //print_r($_COOKIE); ?>
<form action="<?php echo base_url('seller/requests/add');?>" id="myform" name="myForm" novalidate method="post">
			<div class="row card-header d-flex align-items-center">
				<h3 class="h4">Add Requests</h3>
			</div>
			<div class="card-body">
				<div class="col-sm-12">
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label mt-2" >Type</label>
						</div>
						<div  class="col-sm-4">
                            <select class="form-control" name="request_type" style="width: 100%;">
                                <option value="Variant">Variant</option>
                                <option value="Variant Category">Variant Category</option>
                                <option value="Category">Category</option>
                                <option value="Brand">Brand</option>
                            </select> 
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label mt-2">Name</label>
						</div>
						<div  class="col-sm-4">
							<input type="text" class="form-control" placeholder = "Enter Name" name="request_name" >
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="row form-group">
						<div class="col-sm-2 text-right">
							<label class="form-control-label resposive-label mt-3">Additional Info</label>
						</div>
						<div  class="col-sm-4">
                            <textarea class="form-control" name="info" style="width: 100%;"></textarea> 
						</div>
						<div class="clearfix"></div>
					</div>
				<hr>
				<div class="row form-group">
					<div class="col-sm-4">
						<div class="pull-right" style="margin: 0px 14px 14px;">
							<button type="reset" class="btn btn-danger" >Reset</button>
							<button type="submit" class="btn btn-primary" >Save</button>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
</form>