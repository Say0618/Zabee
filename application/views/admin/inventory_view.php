<?php if($this->session->flashdata('success')){?>
  <div class="alert alert-success mb-0" role="alert">
  <strong><?php echo $this->session->flashdata('success');?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  </div>
<?php }?>
<?php if($this->session->flashdata('error')){?>
  <div class="alert alert-danger mb-0" role="alert">
  <strong><?php echo $this->session->flashdata('error');?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  </div>
<?php }
	$editurl = base_url()."seller/product/editedproduct";		
	$createurl = base_url()."seller/product/createproduct";		
	if(isset($redirect))
	{
		$editurl = base_url()."seller/product/editedproduct".$redirect;
		$createurl = base_url()."seller/product/createproduct".$redirect;		
	}
?>
 
 <div class="card-body">
	 <?php if(!$delete){?>
	 <div>
		 <a href="<?php echo base_url("seller/product/inventory_view/warning")?>" class="btn btn-default">Click here to check near to expire inventory</a>
	 </div>
	 <hr/>
	 <?php }?>
	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th align="center">Created On</th>
					<th align="center">Updated On</th>
					<?php if($this->session->userdata('user_type') == "1"){ ?>
						<th align="center">Created By</th>
					<?php } ?>
					<th align="center">Product Name</th>
					<th align="center" width="50">Condition</th>
					<?php //if($this->session->userdata('user_type') == "1"){ ?>
					<th align="center">Variants</th>
					<?php //} ?>
					<th align="center">Stock</th>
					<th align="center">Sold</th>
					<th align="center">Price</th> 
					<th align="center">Discount</th>
					<th align="center">Action</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<div class="modal fade" id="delete-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color: orange;font-weight: bold;">Alert</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="delete_id" value="">
					   	<?php if($delete == "delete"){?>
							<span>Are you sure you want to <strong>Re-create</strong> this Inventory?</span>	
						<?php }elseif($delete == "warning"){?>
							<span>Are you sure you want to <strong>Refresh</strong> this Inventory?</span>
						<?php }else{?>
							<span>Are you sure you want to <strong>Delete</strong> this Inventory?</span>
						<?php }?>	
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="delete_btn" data-dismiss="modal">Yes</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>     
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>