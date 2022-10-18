<div class="card-body">
	<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } if($this->session->flashdata("transfered")){ $class = ($this->session->flashdata("transfered") == "Inventory transfered successful")?"alert-success":"alert-danger"?>
    	<div class="alert <?php echo $class ?>" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("transfered");?>
        </div>
	<?php } ?>
 	<div class="table-responsive"  style="display:block !important;">
		<table  id="datatables" cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
				<th width="1%"><?php echo $this->lang->line('s_no');?>.&nbsp;</th>
				<th align="center"><?php echo $this->lang->line('shipping_company');?></th>
				<th align="center"><?php echo $this->lang->line('price');?></th>
				<th align="center">Increment per Unit</th>
				<th align="center">Free Shipping Above</th>
				<th align="center" style="width:15%" ><?php echo $this->lang->line('estimated_delivery_days');?></th>
				<th align="center"><?php echo $this->lang->line('active');?></th>
				<th align="center" style="width:8%"><?php echo $this->lang->line('action');?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
