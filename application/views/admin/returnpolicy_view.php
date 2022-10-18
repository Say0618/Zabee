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
		<th><?php echo $this->lang->line('title');?></th>
		<th><?php echo $this->lang->line('return_period');?></th>
		<th><?php echo $this->lang->line('restocking_fee');?></th>
		<th><?php echo $this->lang->line('return_type');?></th>
		<th><?php echo $this->lang->line('status');?></th>
		<th><?php echo $this->lang->line('actions');?></th>
	  </tr>
	</thead>
	<tbody></tbody>
  </table>
</div>
</div>
