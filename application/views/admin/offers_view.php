<div class="card-body">
	<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
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
                    <th>Name</th>
					<th><?php echo $this->lang->line('image');?></th>
					<th width="25%">Position</th>
					<th width="26%" align="center"><?php echo $this->lang->line('active');?></th>
					<th width="26%" align="center" style="overflow: visible !important;"><?php echo $this->lang->line('actions');?></th>
					<th width="26%" align="center" style="overflow: visible !important;"><?php echo $this->lang->line('actions');?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

