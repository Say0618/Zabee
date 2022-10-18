<?php if($this->session->flashdata('error') || $this->session->flashdata('success')){?>
<div class="card-header">
	<h6 class="success"><?php echo $this->session->flashdata('success');?></h6>
	<h6 class="error"><?php echo $this->session->flashdata('error');?></h6>
</div>
<?php }?>
<div class="card-body">
	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th align="center"><?php echo $this->lang->line('seller_name');?></th>
					<th align="center"><?php echo $this->lang->line('store_name');?></th>
					<th align="center"><?php echo $this->lang->line('no_of_pdts');?></th>
					<th align="center"><?php echo $this->lang->line('block_unblock');?></th>
					<th align="center"><?php echo $this->lang->line('details');?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
