<?php if($this->session->flashdata('msg') || $this->session->flashdata('msg')){?>
<div class="card-header">
	<h6 class="success"><?php echo $this->session->flashdata('msg');?></h6>	
</div>
<?php }?>
<div class="card-body">
	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
			    <tr>
					<th width="1%"><?php echo $this->lang->line('s_no');?>.&nbsp;</th>
					<th width="20%"><?php echo $this->lang->line('created_by');?></th>
					<th align="center">Store Name</th>
					<th width="25%"><?php echo $this->lang->line('waiting_for_approved');?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Approve Store</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<p>Are you sure you want to approve this store?
			</div>				
		  </div>
			<div class="modal-footer">
				<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="confirm_approve"/>
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="decline_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Decline Store</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<p>Are you sure you want to decline this store?
			</div>
		</div>
			<div class="modal-footer">
				<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="rejectBtn" />
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="store_id">
<input type="hidden" id="seller_id">