<div class="card-body">
	<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>

	<?php  if($this->session->flashdata("affiliated")){ ?>
    	<div class="alert alert-success alert-dismissable fade show" align="center" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo"Success! ".$this->session->flashdata("affiliated");?>
        </div>
	<?php } ?>
 	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th id="head">Product</th>
					<th>Sale Count</th>
					<th>Visit Count</th>
					<th class="link">Referral Code</th>
					<th class="link" width="15%">Date</th>
					<th class="link" width="15%">Total Points</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

