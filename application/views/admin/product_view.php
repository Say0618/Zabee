<div class="card-body">
 	<div class="table-responsive"  style="display:block !important;">
		<table  id="datatables" cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
				<th width="2%"><?php echo $this->lang->line('s_no');?>.&nbsp;</th>
				<th width="5%"><?php echo $this->lang->line('image');?></th>
				<?php if($this->session->userdata('user_type')== 1){ ?>
                <th><?php echo $this->lang->line('created_by');?></th>
				<?php } ?>
				<th><?php echo $this->lang->line('title');?></th>
				<th><?php echo $this->lang->line('category');?></th>
				<th><?php echo $this->lang->line('brand');?></th>
				<th><?php echo $this->lang->line('action');?></th>
                <th><?php echo $this->lang->line('details');?></th>
				<?php if($this->session->userdata('user_type')== 1){?>
				<th width="6%"><?php echo $this->lang->line('is_featured');?></th>
				<th width="6%">Block</th>
				<?php }?>
				<th width="5%"><?php echo $this->lang->line('inventory');?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
