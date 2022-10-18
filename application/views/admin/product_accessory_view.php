<div class="card-body">
<?php //echo"<pre>";print_r($_SESSION);?>
<?php if($this->session->flashdata('success') || $this->session->flashdata('error') != ""){?>
    <div class="col mt-3 mb-3">
		<span class="success"><strong><?php echo ($this->session->flashdata('success'))?$this->session->flashdata('success'):""; ?></strong></span>
        <span class="error"><strong><?php echo ($this->session->flashdata('error'))?$this->session->flashdata('error'):""; ?></strong></span>
    </div>
    <?php } ?>
<div class="table-responsive">
  <table class="table table-striped table-sm datatables tableWidth">
	<thead>
	  <tr>
		<th><?php echo $this->lang->line('product');?></th>
		<th><?php echo $this->lang->line('accessories');?></th>
		<th><?php echo $this->lang->line('actions');?></th>
	  </tr>
	</thead>
	<tbody></tbody>
  </table>
</div>
</div>
