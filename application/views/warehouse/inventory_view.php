<?php
	$editurl = base_url()."seller/product/editedproduct";		
	$createurl = base_url()."seller/product/createproduct";		
	if(isset($redirect))
	{
		$editurl = base_url()."seller/product/editedproduct".$redirect;
		$createurl = base_url()."seller/product/createproduct".$redirect;		
	}
?>

 
 <div class="card-body diff-card-body">
 	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th align="center"><?php echo $this->lang->line('created_on');?></th>
					<?php if($this->session->userdata('user_type') == "1"){ ?>
					<th align="center"><?php echo $this->lang->line('created_by');?></th>
					<?php } ?>
					<th align="center"><?php echo $this->lang->line('product_name');?></th>
					<th align="center" width="50"><?php echo $this->lang->line('condition');?></th>
					<th align="center"><?php echo $this->lang->line('variants');?></th>
					<th align="center"><?php echo $this->lang->line('quantity');?></th>
					<th align="center"><?php echo $this->lang->line('price');?></th>
					<th align="center"><?php echo $this->lang->line('warehouse');?></th>
					<?php if($this->session->userdata('user_type') == "1"){ ?>
					<th align="center"><?php echo $this->lang->line('update');?></th>
					<th align="center"><?php echo $this->lang->line('received');?></th>
					<?php }else{?>
					<th><?php echo $this->lang->line('received_date');?></th>
					<?php }?>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>


