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
	  		<th align="center" class="d-none">ID</th>
	  		<th align="center" style="width:200px;">Title</th>
	  		<th align="center" style="width:100px;">Code</th>
			<th align="center" style="width:150px;">Valid From</th>
			<th align="center" style="width:150px;">Valid To</th>
			<th align="center" style="width:120px;">Apply type</th>
			<th align="center" style="width:180px;">Apply on</th>
			<th align="center" style="width:50px;">Value</th>
			<th align="center" style="width:50px;">Max/Used</th>
			<th align="center" style="width:50px;">Actions</th>
	  </tr>
	</thead>
	<tbody></tbody>
  </table>
</div>
</div>
