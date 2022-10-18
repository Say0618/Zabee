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
					<th align="center"></th>
					<th align="center"></th>
					<th align="center"><?php echo $this->lang->line('title');?></th>
					<th align="center">Link</th>
					<th align="center"><?php echo $this->lang->line('image');?></th>
					<th align="center" style="width:10%"><?php echo $this->lang->line('active');?></th>
					<th align="center" style="width:10%"><?php echo $this->lang->line('show_on_homepage');?></th>
					<th align="center"><?php echo $this->lang->line('action');?></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

