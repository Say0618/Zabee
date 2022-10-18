<link rel="stylesheet" type="text/css" href="<?php echo assets_url('front/css/buyeraddress.css')?>" >
<div class="container">
	<div class="row position-relative">
		<?php 
        	echo '<div class="col mt-2 mb-2"><a class="breadcrumb-item" href="'.base_url().'">Home </a><a class="breadcrumb-item" href="'.base_url('shipping').'">shipping Address</a></div>';
        ?>
    </div>
	<div class="row">
		<div class="col-sm-12 padding-above-address">
			<div class="row">
				<h4 class="col-4">Shipping</h4>
			</div>
			<?php if(!empty($locations)){ ?>
			<div class="row mt-5">
				<?php foreach($locations as $loc){?>
					<?php if($loc->active == 1){ ?>
						<div class="col-lg-6 col-sm-12 ">
							<div class="card mb-3 card-border" style="">
								<div class="card-body pt-0 checkHeight">
									<div class="row mt-2 mb-2">
										<div class="col-6 p-0">
											<label>
												<input type="radio" name="check" value="<?php echo $loc->id;?>" <?php if($loc->use_address == 1){echo 'checked="checked"';}?> >
												<a class="addDiffAddress" href="<?php echo base_url('checkout/useAddressforbuyer/'.$loc->id); ?>">use this location</a>
											</label>	
										</div>
										<div class="col-6 p-0 text-right">
											<?php if($loc->use_address == 0) {?>
												<a title="delete address" class="text-color" href="<?php echo base_url('buyer/deleteaddress/'.$loc->id); ?>">Remove <i class="fa fa-trash editColor bin-size"></i> </a>
											<?php } ?>
												<a class="btn btn-default btn-sm pull-right addDiffAddress" href="<?php echo base_url('shipping/edit/'.$loc->id."?c=0"); ?>" >Edit <i class="fa fa-edit editColor"></i></a>
										</div>
									</div>
									<div class="col-12 address_value">
										<?php echo $loc->ship_fullname?>
									</div>
									<div class="col-12 address_value">
										<?php echo $loc->ship_address_1?>
										<?php echo $loc->ship_address_2;?>,
										<?php echo $loc->ship_zip;?>
									</div>
									<div class="col-12 address_value">
										<?php echo $loc->ship_contact?>
									</div>
								</div>
							</div>
						</div>	
					<?php }?>
				<?php } } else{ ?>
					<div class="text-center mt-5 mb-5"><strong>NO ADDRESS FOUND</strong></div>
				<?php } ?>
				<div class="col-lg-6 col-sm-12 text-center">
					<div class="card">
						<a class="" href="<?php echo base_url('shipping/add');?>">
							<div class="card-body position-relative pt-0" id="add-location">
								<div id="add-new">
									<p class="card-text text-color mb-2">Add New</p>
									<i class="fas fa-plus-circle mb-5"></i>
								</div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Address</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				Are you sure?
			</div>				
		  </div>
		  <div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">No</a>
			<a type = "submit" class="btn btn-primary confirm_del" href="<?php echo base_url('buyer/deleteaddress/'.$loc->id); ?>">Yes</a>
		  </div>
		</div>
	</div>
</div>