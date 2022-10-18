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
				<?php if($this->session->userdata['user_type'] == "1"){ ?>
                	<th width="10%"><?php echo $this->lang->line('created_by');?></th>
				<?php }?>
				<th width="8%" align="center"><?php echo $this->lang->line('image');?></th>
                <th align="center"><?php echo $this->lang->line('title');?></th>
				<th width="10%"><?php echo $this->lang->line('category');?></th>
				<th width="10%"><?php echo $this->lang->line('brand');?></th>
				<!-- <th align="center">Details</th> -->
				<?php if($this->session->userdata['user_type'] == "2"){ ?>
					<th width="12%"><?php echo ($_GET['v'] ==0 || $_GET['v'] ==1)?$this->lang->line('status'):"Reason"?></th>
				<?php }?>
				<th width="5%"><?php echo $this->lang->line('action');?></th>
				<?php if($this->session->userdata['user_type'] == "1"){ ?>
                    <th width="15%"><?php echo $this->lang->line('waiting_for_approved');?></th>
                <?php }?>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div class="modal fade" id="product_del" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('delete_product');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('are_you_sure');?>
			</div>				
		  </div>
			  <div class="modal-footer">
				<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary confirm_del" />
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			 </div>
		</div>
	</div>
</div>
<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('product_approval');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('modal_product_approval');?>
			</div>				
		  </div>
			<div class="modal-footer">
				<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="confirm_del" />
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="decline_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('product_rejection');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('ask_product_rejection');?>
				<textarea class="form-control mt-2" id="declined_reason" placeholder="Enter Reject Reason"></textarea>
			</div>
		</div>
			<div class="modal-footer">
				<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="rejectBtn" />
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="s_id">
<input type="hidden" id="p_id">