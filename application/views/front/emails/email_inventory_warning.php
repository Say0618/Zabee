<html>
<head>
    <style>
		table, th, td {
			border: 1px solid black;
			padding:5px;
		}
		.word-wrap{
			word-wrap: break-word;
		}
	</style>
  </head>
	<body>
		<h2 class="heading_color_email_1"><?php echo $data[0]->store_name;?> <br /></h2>
		<div class = "row email_template_body_middle_div">
			<div class="container-fluid" style = "padding:10px">
				<p class="word-wrap">We wanted to inform you that the following items in your inventory have not been updated recently. 
					In order to ensure uptodate prices and accurate inventory level, you are required to periodically update your inventtory. 
					The following inventory listings are set to expire on <strong><?php echo $expire_date?></strong>.
					Please login and update the inventory status by either updating price and inventory level or by confirming that the inventory information is current and valid.
				</p>
					<h3>Items expiring:</h3>
					<table>
						<thead>
							<tr>
								<th>Product Name</th>
								<th>Quantity</th>
								<th>Price</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($data as $d){?>
							<tr>
								<td><?php echo $d->product_name?></td>
								<td><?php echo $d->quantity?></td>
								<td><?php echo $d->price?></td>
							</tr>
							<?php }?>
						</tbody>
					</table>
				<br/>
				<a href="<?php echo base_url() ?>" class="btn btn-primary">Click Here To GoTo Zab.ee</a>
			</div>
		</div>
	</body>
</html>
					