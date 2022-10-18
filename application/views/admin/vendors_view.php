<div class="row card-header d-flex align-items-center">
	
</div>
<div class="card-close">
		  <div class="dropdown">
			<button type="button" id="closeCard4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
			<div aria-labelledby="closeCard4" class="dropdown-menu dropdown-menu-right has-shadow"><a href="" onclick="$('#vendorsmodal').modal('show');return false;" class="dropdown-item edit"><i class="fa fa-plus" aria-hidden="true"></i>Create Vendors</a><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a></div>
		  </div>
</div>
<div class="card-body">
  <table class="table table-striped table-sm datatables">
	<thead>
	  <tr>
		<th width="1%">Name</th>
		<th>Address</th>
		<th>PINCODE</th>
		<th width="16%" align="center">Actions</th>
	  </tr>
	</thead>
	<tbody></tbody>
  </table>
</div>
<div class="modal fade" id="vendorsmodal">
	<!--<form action = "" method = "POST" class="form-horizontal" onsubmit="">-->
	<?php 
		$attr = array
		(
			"method"=>"POST",
			"class"=>"form-horizontal",
			"onsubmit"=>"validate(this,event);"
		);
		echo form_open_multipart("",$attr);
	?>
		<input type ="hidden" name = "vendors_func" value = "" class = "vendors_func"/>
		<input type ="hidden" name = "vendors_count" value = "" class = "vendors_count"/>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3></h3>
			</div>
			<div class="modal-body">
				<div class="box-content vendorsmodal-body">
				<span class = "addrembtns">
					<span class = "btn btn-success addmorebtn">Add more</span>
					<span class = "btn btn-danger removemorebtn">Remove</span>
				</span>
				</div>				
			</div>
			<div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Close</a>
				<input type = "submit" value = "Save changes" class="btn btn-primary" />
			</div>
	</form>
</div>