<div class="card-body">
	<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>
	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th style="width:50;">S.no</th>
					<th style="width:100px;">CSV Type</th>
					<th style="min-width:450px;" width="25%">Filename</th>
					<th style="width:100px;" align="center">Total Rows</th>
					<th style="width:150px;" align="center">Rows Completed</th>
					<th style="width:150px;" align="center">Status</th>
					<th style="width:100px;" style="overflow: visible !important;">Actions</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

