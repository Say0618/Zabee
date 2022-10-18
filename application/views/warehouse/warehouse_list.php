<?php if($this->session->flashdata('error') || $this->session->flashdata('success')){?>
<div class="card-header">
	<h6 class="success"><?php echo $this->session->flashdata('success');?></h6>
	<h6 class="error"><?php echo $this->session->flashdata('error');?></h6>
</div>
<?php }?>
<div class="card-body">
<div class="table-responsive">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-sm table-striped table-bordered datatables">
		<thead>
			<tr>
                <th></th>
            	<th><?php echo $this->lang->line('title');?></th>
				<th><?php echo $this->lang->line('class');?></th>
                <th><?php echo $this->lang->line('address');?></th>
                <th><?php echo $this->lang->line('email');?></th>
                <th><?php echo $this->lang->line('contact_no');?></th>
				<th><?php echo $this->lang->line('country');?></th>
				<th><?php echo $this->lang->line('state_prov');?></th>
                <th><?php echo $this->lang->line('city');?></th>
                <th><?php echo $this->lang->line('zip');?></th>
                <th><?php echo $this->lang->line('update');?></th>
			</tr>
		</thead>
	</table>
</div>
</div>

        