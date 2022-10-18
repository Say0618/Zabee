<div class="card-body">
<?php //echo"<pre>";print_r($_SESSION);?>
<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>
	<?php if($chart_data['status'] == 1 && count($chart_data['data']) > 0) { ?>
	<div class="row" style="margin-bottom:15px;">
		<canvas  id="myChart" height="100"></canvas >
		<hr />
	</div>
	<?php } ?>
	<div class="row">
		<div class="col-sm-12"><h4>Search With</h4></div>
		<div class="col-sm-5 form-group">
			<div class="col-sm-6"><label>Start</label></div>
			<div class="col-sm-12"><input type="text" name="startRange" id="startRange" class="form-control" /></div>
		</div>
		<div class="col-sm-5 form-group">
			<div class="col-sm-6"><label>End</label></div>
			<div class="col-sm-12"><input type="text" name="endRange" id="endRange" class="form-control" /></div>
		</div>
		<div class="col-sm-2 form-group">
			<div class="col-sm-6"><label>&nbsp;</label></div>
			<button class="btn btn-primary" id="generateReport">Generate</button>
		</div>
		<div class="col-sm-12"><span id="error_report"></span></div>
		<div class="clearfix"></div>
	</div>
	<div class="table-responsive">
		<table class="table table-striped table-sm datatables tableWidth">
			<thead>
				<tr>
					<th align="center" style="min-width:100px;">S.No</th>
					<th align="center" style="min-width:300px;">Date/Time</th>
					<th align="center" style="min-width:300px;">Product Name</th>
					<th align="center" style="min-width:300px;">Seller Store</th>
					<th align="center" style="min-width:100px;">Views</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
