<div class="card-body">
	<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php }?>
 	<div class="table-responsive"  style="display:block !important;">
		<table  id="datatables" cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
				<th width="1%"><?php echo $this->lang->line('s_no');?>.&nbsp;</th>
				<th align="center"><?php echo $this->lang->line('date');?></th>
				<th align="center"><?php echo $this->lang->line('full_name');?></th>
				<th align="center"><?php echo $this->lang->line('email');?></th>
				<th align="center"><?php echo $this->lang->line('phone_number');?></th>
				<th align="center"><?php echo $this->lang->line('categories');?></th>
				</tr>
			</thead>
			<tbody>
				<?php if(count($signups)> 0):
						foreach($signups as $item):
							$categories = ($item->categories != '')?json_decode($item->categories):'ALL';
				?>
				<tr>
					<td></td>
					<td><?php echo date('Y-m-d\TH:i:s', strtotime($item->created)); ?></td>
					<td><?php echo $item->first_name; ?></td>
					<td><?php echo ($item->email_address != '')?$item->email_address:'-'; ?></td>
					<td><?php echo ($item->phone_number != '')?$item->phone_number:'-'; ?></td>
					<td>
						<?php
							if(is_array($categories)):
								foreach($categories as $category):
									echo $category->category_name.', ';
								endforeach;
							else:
								echo $categories;
							endif;
						?>
					</td>
				</tr>
				<?php
						endforeach;
					endif;
				?>
			</tbody>
		</table>
	</div>
</div>
