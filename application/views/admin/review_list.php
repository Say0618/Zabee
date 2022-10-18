<div class="card-body">
	<?php /*echo "<pre>";print_r($_SESSION);*/ if(isset($_GET['status']) && $_GET['status'] == "error" && $this->session->flashdata("error")){ ?>
    	<div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("error");?>
        </div>
	<?php } else if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ //die("here 2");?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>
	<div class="alert alert-success d-none" id="review-status">
		<strong id="heading"></strong><span id="detail"></span>
	</div>
 	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th align="center">Product Name</th>
					<th align="center">Store Name</th>
					<th align="center">Reviewer Name</th>
					<th align="center">Reviewer Email</th>
					<th align="center">Review</th>
					<th align="center">Rating</th>
					<th align="center">Created Date</th>
					<th align="center">Action 1</th>
					<th align="center">Actions</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>


