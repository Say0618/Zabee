<div class="container bg-transparent">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>"Saved Cards"),array("url"=>base_url('buyer'),"cat_name"=>"Account"))));?>
	<div class="row bg-white mt-2 order-list-radius">
		<div class="col-sm-12 mt-3"> 
		<?php if(isset($_GET['status']) && $_GET['status'] == 'success'){echo '<div class="alert alert-success" role="alert">Card successfully deleted <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
		<?php if(isset($_GET['status']) && $_GET['status'] == 'invalid_card'){echo '<div class="alert alert-danger" role="alert">Unable to delete card. <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
			<div class="panel panel-primary" >
			<div class="panel-heading">
					<h4 class="ml-3">Saved Cards</h4>
				</div>
				<div class="panel-body" >
					<div class="table-responsive-sm" >
						<table cellpadding="0" cellspacing="0" border="0" class="table table-responsive p-2">
							<thead>
								<tr>
									
									<th align="center">Card Name</th>
									<th align="center">Card No</th>
									<th align="center">Type</th>
									<th align="center">Expiry</th>
									<th width="1%" align="center">Actions</th>
								</tr>
								<?php
								if(!empty($cards)){
									foreach($cards as $card) {
								?>
								<tr class="td-line-height">
									<td class="order_time"><?php echo $card->card_name; ?></td>
									<td><?php echo 'XXXX-XXXX-XXXX-'.$card->card_number; ?></td>
									<td><?php echo $card->card_type; ?></td>
									<td><?php echo $card->expiry_month.'/'.$card->expiry_year; ?></td>
									<td> 
										<div class="dropdown text-center">
											<a href="#" class="deleteCard" data-id="<?php echo $card->id; ?>" data-name="<?php echo addslashes($card->card_name); ?>">Delete</a>
										</div>
									</td>
								</tr>
							</div>
							<?php }}else{ ?>
								<tr><td colspan="6" class="text-center"><strong>No Card Found.</strong></td></tr>
							<?php }?>	
							</thead>
						</table>
					</div>
					<?php
						if(($links["links"])){
					?>
						<div class="clearfix"></div>
						<div class="pagination-div">
							<ul class="pagination pull-right mt-5">
								<?php foreach($links['links'] as $page){ 
									echo $page;
								} ?>
							</ul>
						</div>
					<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="confirmation-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">Delete Card!<button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="card_id" value="">
                        <strong>are you sure, you want to delete card <span class="card_name"></span>?</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
				<span class="error d-none">Invalid Card</span>
              <button type="button" class="btn btn-danger " id="card_delete" data-dismiss="modal">Delete</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>