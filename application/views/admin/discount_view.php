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
<div class="table-responsive">
  <table class="table table-striped table-sm datatables tableWidth">
	<thead>
	  <tr>
	  		<th align="center">Title</th>
			<th align="center">Valid From</th>
			<th align="center">Valid To</th>
			<th align="center">Type</th>
			<th align="center">Value</th>
			<th align="center">Active</th>
			<th align="center">Actions</th>
			<th align="center">Actions</th>
	  </tr>
	</thead>
	<tbody></tbody>
  </table>
</div>
</div>
