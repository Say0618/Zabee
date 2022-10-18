<div class="card-body">
 	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
				<th width="1%">Sno.&nbsp;</th>
				<?php if($this->session->userdata['user_type'] == "1"){ ?>
                	<th width="10%">Created By</th>
				<?php }?>
				<th width="8%" align="center">Image</th>
                <th align="center">Title</th>
				<th width="10%">Category</th>
				<th width="10%">Brand</th>
				<th width="5%">Action</th>
                </tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
