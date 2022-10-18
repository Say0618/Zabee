<div class="panel panel-primary py-3" >
	<div class="panel-body" >
		<div class="table-responsive-sm" >
			<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th align="center">Order Date</th>
						<th align="center">Order ID</th>
						<th align="center">Ship to</th>
						<th align="center">Amount</th>
						<th>Details</th>
						<!-- <th align="center">Status</th> -->
						
					</tr>
					<?php
					if(!empty($orders)){
						foreach($orders as $order) {
							if($order->status == 0){
								$orderStatus = 'Pending';
								$bgClass = 'info';
							} else {
								$orderStatus = 'Proceed';
								$bgClass = 'success';
							}
					?>
					<tr  href="#order_<?php echo $order->order_id; ?>" role="button" aria-expanded="false" aria-controls="order_<?php echo $order->order_id; ?>">
						<td class="order_time"><?php echo $order->created; ?></td>
						<td><?php echo $order->order_id; ?></td>
						<td><?php echo $order->shipping['name'].'<br />'.$order->shipping['address_1'].'<br />'.$order->shipping['city']; ?></td>
						<td>$ <?php echo number_format((float)$order->gross_amount, 2, '.', ''); ?></td>
						<!-- <td><span class="alert alert-<?php echo $bgClass; ?>"><?php echo $orderStatus; ?></span></td> -->
						<td>
						<a class="p-info" href="#order_<?php echo $order->order_id; ?>"  data-toggle="collapse" >
						<i class="fas fa-info-circle"></i></a></td>
					</tr>
					<tr class="collapse" id="order_<?php echo $order->order_id; ?>">
						<td colspan="6" class="no-padding">
							<?php foreach($order->orders as $item) {
								
								//echo"<pre>";print_r($item);
									// $img = explode(',',$item->product_image);
									// $img = (isset($img[0]))?$img[0]:$item->product_image;
									// if($item->is_local == 1){
									// 	$img = product_thumb_path($img);
									// }
									$img = product_thumb_path().$item->product_image;
								?>
									<div class="row mt-2">
									<div class="col-sm-3">
										<img src="<?php echo $img; ?>" width="100" alt="" class="img-fluid">
									</div>  
									<div class="col-sm-4">
										<p>
											<strong><?php echo /*$item->titile.' '.*/$item->product_name; ?></strong><br />
											Unit Price: <?php echo $item->price; ?><br />
											Quantity: <?php echo $item->qty; ?><br />
											Tax: <?php echo $item->tax_amount; ?><br />
											Shipping: <?php echo $item->shipping_amt; ?><br />
											Total: <?php echo $item->item_gross_amount; ?><br />
										</p>
									</div>
										<?php if($item->order_status == 0 && $item->item_confirm_status == 0){
												$item->order_status = 'Pending';
											} else if($item->order_status == 1 && $item->item_confirm_status == 0) {
												$item->order_status = 'Proceed';
												}
												else if($item->order_status == 1 && $item->item_confirm_status == 1){
													$item->order_status = 'Order Recieved';
												}
												// else{
												// 	$item->order_status = 'Declined';
												// }
											?>
									
									<div class="col-sm-4">
										<p>
											<!--Sell by: <a href="#<?php echo $item->store_id; ?>"><?php echo $item->store_name; ?></a><br />-->
											Sell by: <strong><?php echo $item->store_name; ?></strong><br />
											status:<strong><?php echo  $item->order_status; ?></strong><br />
											Email: <a href="mailto:<?php echo $item->seller_email; ?>"><?php echo $item->seller_email; ?></a><br />
											Phone: <a href="tel:<?php echo $item->seller_phone; ?>"><?php echo $item->seller_phone; ?></a><br />
											<!-- <button type="button" class="btn contactuser" id="contact-seller"><i class="fa fa-user"></i>Contact Seller</button> -->
										</p>
									</div>
									</div> 
							<?php } ?>
						</td>
					</tr>
				<?php }}else{ ?>
					<tr><td colspan="6" class="text-center"><strong>No Order Found.</strong></td></tr>
				<?php }?>	
				</thead>
			</table>
		</div>
	</div>
</div>
	