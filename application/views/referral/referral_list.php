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
					<th id="head">First Name</th>
					<th>Last Name</th>
					<th>Email</th>
					<th class="link">Referral Code</th>
					<th class="link" width="15%">Invite Time</th>
					<th width="10%" align="center" style="overflow: visible !important;">Status</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

