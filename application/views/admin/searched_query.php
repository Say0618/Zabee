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
 	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th align="center"><?php echo $this->lang->line('requester_name');?></th>
                    <th align="center"><?php echo $this->lang->line('email');?></th>
                    <th align="center"><?php echo $this->lang->line('request_for');?></th>
					<th align="center"><?php echo $this->lang->line('condition');?></th>
                    <th align="center"><?php echo $this->lang->line('price');?></th>
                    <th align="center"><?php echo $this->lang->line('description');?></th>
                    <!-- <th style="width: 20%;" align="center"><?php echo $this->lang->line('action');?></th> -->
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('additional_info');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('request_approvalques');?>
			</div>				
		  </div>
			<div class="modal-footer">
				<input type="hidden" id="row_id">
				<input type="hidden" id="value">
				<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="confirm_del" />
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>


