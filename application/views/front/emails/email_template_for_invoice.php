<html>
<head>
<meta name="viewport" width=device-width content="initial-scale = 1.0,maximum-scale = 1.0" />
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800&display=swap" rel="stylesheet"> 
<style>

@font-face {
   font-family: 'Open Sans Light';
   font-style: normal;
   font-weight: 300;
   src:url(https://fonts.gstatic.com/s/opensans/v13/DXI1ORHCpsQm3Vp6mXoaTRa1RVmPjeKy21_GQJaLlJI.woff) format('woff');
}
body{
	font-family: 'Open Sans';
	background: #f2f2f2;
}
body{margin:0px;padding:0px;}
.container{
	width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    margin-right: auto;
    margin-left: auto;
}
table{
    color: #333;
    font-size: 13px;
    font-weight: 300;
    text-align: center;
    border-collapse: separate;
    border-spacing: 0;
    width: 99%;
    margin: 0 auto;
    /* box-shadow: 0 4px 8px 0 rgba(0,0,0,.16); */
}
table td{padding: .75rem;vertical-align: top;border-top: 1px solid #dee2e6;}
p{font-size:13px;}
.my-own-navbar{
	position: relative;
	display: -webkit-box;
	display: -ms-flexbox;
	display: flex;
	-ms-flex-wrap: wrap;
	flex-wrap: wrap;
	-webkit-box-align: center;
	-ms-flex-align: center;
	align-items: center;
	-webkit-box-pack: justify;
	-ms-flex-pack: justify;
	justify-content: space-between;
	padding: .5rem 1rem;
}

.my-own-nav-backgroud{
	background: #003363;
}

.my-own-justify-content-between{
	-webkit-box-pack: justify !important;
	-ms-flex-pack: justify !important;
	justify-content: space-between !important;
}

.my-own-text-white{
	color: #fff !important;
}

.my-own-navbar-brand{
	display: inline-block;
	padding-top: .3125rem;
	padding-bottom: .3125rem;
	margin-right: 1rem;
	font-size: 1.25rem;
	line-height: inherit;
	white-space: nowrap;
}

.my-own-img-fluid{
	max-width: 100%;
	height: 32px;
	float: right;
}
.my-own-row{
	flex-wrap: wrap;
}
.my-own-row-2{
	flex-wrap: wrap;
	margin-right: -7px;
	margin-left: -7px;
}
.invoice-label{
	font-size:18px;
	color:#000000;
    font-family: 'Open Sans';
}

.my-own-col-4{
	-webkit-box-flex: 0;
	-ms-flex: 0 0 33.333333%;
	flex: 0 0 33.333333%;
	max-width: 33.333333%;
	position: relative;
	width: auto;
	min-height: 1px;
	padding-right: 15px;
	padding-left: 15px;
	float:left;
}
.my-own-col-4:last-child{padding-right:0px;}
.my-own-col-3{
	-webkit-box-flex: 0;
	-ms-flex: 0 0 25%;
	flex: 0 0 25%;
	max-width: 25%;
	position: relative;
	width: 100%;
	min-height: 1px;
	padding-right: 15px;
	padding-left: 15px;
}
.my-own-col-7{
	-webkit-box-flex: 0;
	-ms-flex: 0 0 58.333333%;
	flex: 0 0 58.333333%;
	/*max-width: 58.333333%;*/
	position: relative;
	width: 100%;
	min-height: 1px;
	padding-right: 15px;
}
.my-own-col-12{
	-webkit-box-flex: 0;
	-ms-flex: 0 0 100%;
	flex: 0 0 100%;
	position: relative;
	width: 100%;
	min-height: 1px;
	
}

.my-own-text-dark {
    color: #343a40 !important;
}
.my-own-table-bordered {
    border: 1px solid #dee2e6;
}
.my-own-offset-9 {
    margin-left: 75%;
}
.my-own-text-right {
    text-align: right !important;
}
td{
	border:1px solid #ddd;
	padding:.75rem;
	color:#000;
}
th{
	padding: 7px;
}
.bill-to{
	margin-left: -9px;
}
.ship-to{
	margin-left: -9px;	
}
@media (min-width: 576px){
	.container {
		max-width: 540px;
	}
}
@media (min-width: 768px){
	.container {
		max-width: 720px;
	}
}
@media (min-width: 992px){
	.container {
		max-width: 960px;
	}
}

@media (min-width: 1200px){
	.container {
		max-width: 1140px;
	}
}
@media screen and (max-width: 415px) {
	.my-own-container{
		padding:15px !important;
		width: 600px !important;
	}
	.my-own-row{
		padding: 25px !important;
	}
	.my-own-col-4{
	width:100% !important;
	flex: 0 0 100%;
	max-width: 100%;
	padding-left:0px;
	padding-right:0px;
	}
	.my-own-offset-9{
		margin-left: 75% !important;
		max-width:100% !important;
	}
	.invoice-for-smaller-screen{
		font-weight: 300 !important;
		letter-spacing: 15px !important;
		font-size: 30pt !important;
		padding-left: 0% !important;
	}
	.invoice{
		float: left !important;
		width: 188px !important;
		margin-left: -23px;
	}
	.bill-to{
	margin-left: -9px;
	}
	.ship-to{
		margin-left: -9px;	
	}
	
}
.clear{clear:both;}
.sub_total_table{font-size: 18px;color: #000000;font-family:'Open Sans';font-weight:600}
.sub_total_table td{border:0px;border-top:1px solid #dee2e6;vertical-align:top;}
.sub_total_table tr:first-child td{border-top:0px;}
.grand_total{background:#003363;font:bold 18px 'Open Sans';color: #fff;padding:.75rem}
</style>
</head>
<body>
<div class="container">
	<div style="padding:15px; background: #fff;">
		<p style="font-weight: 300; font-size: 16px; margin:0px">Hello <?php echo $name?>,</p>
		<?php echo $text?>
	</div>
</div>
<div class="container">
	<div class="my-own-border" style="margin-top:5px;background: #fff;">
		<div class="my-own-navbar my-own-row my-own-nav-backgroud my-own-justify-content-between" style="background: #003363;">
			<div class="my-own-col-12">
				<span class="my-own-text-white invoice-for-smaller-screen" style="font-weight: 300; font-size: 24px; letter-spacing: 5px; padding-left:0;">Order Summary</span>
				<img class="my-own-img-fluid" src="<?php echo media_url('assets/front/images/LOGO_EMAIL.png') ?>" max-width="100%" height="auto" />
			</div>
		</div>
		<div style="width: 100%;margin-left: 10px;margin-right: 10px;margin-top:10px;">
			<?php if((isset($location['billing']['id']) && isset($location['shipping']['id'])) && $location['billing']['id'] == $location['shipping']['id']){?>
				<div class="my-own-col-4">
					<h5 class="invoice-label" style="margin: 0;">Ship/Bill To:</h5>
					<p><?php echo ucfirst($location['billing']['name']);?><br />
					<?php  echo $location['billing']['address_1']?><br />
					<?php echo ucfirst($location['billing']['city']).", ".$location['billing']['state']." ".$location['billing']['zipcode']."<br>".$location['billing']['country'];?><br />
					<?php  echo $location['billing']['phone'];?></p>
				</div>
			<?php }else{?>
				<div class="my-own-col-4">
					<h5 class="invoice-label" style="margin: 0;">Bill To:</h5>
					<p><?php echo ucfirst($location['billing']['name']);?><br />
					<?php  echo $location['billing']['address_1']?><br />
					<?php echo ucfirst($location['billing']['city']).", ".$location['billing']['state']." ".$location['billing']['zipcode']."<br>".$location['billing']['country'];?><br />
					<?php  echo $location['billing']['phone'];?></p>
				</div>
				<div class="my-own-col-4">
					<h5 class="invoice-label" style="margin: 0;">Ship To:</h5>
					<p><?php echo ucfirst($location['shipping']['name']);?><br />
					<?php  echo $location['shipping']['address_1']?><br />
					<?php echo ucfirst($location['shipping']['city']).", ".$location['shipping']['state']." ".$location['shipping']['zipcode']."<br>".$location['shipping']['country'];?><br />
					<?php  echo $location['shipping']['phone'];?></p>
				</div>
			<?php }?>
			<div class="my-own-col-4">
				<h5 class="my-own-text-dark" style="font-size: 14px; margin: 0;">Order No: <b class="" style="font-size: 14px;"><?php echo $order_id; ?></b></h5>
				<h5 class="my-own-text-dark" style="font-size: 14px; margin: 0;">Order Date: <b class="" style="font-size: 14px;"><?php echo formatDateTime($created, true); ?></b></h5><br />
			</div>
			<div class="clear"></div>
		</div>
		<div class="my-own-row-2">
            <table class="my-own-table-bordered">
                <thead class="my-own-nav-backgroud"> 
					<tr class="my-own-text-white">
						<th>TITLE</th>
						<th>IMAGE</th>
						<th>QTY</th>
						<th>PRICE</th>
						<th>SHIPPING</th>
						<th>TAX</th>
						<th>TOTAL</th>
					</tr>
				</thead>
                <tbody>
					<?php 
					$i=1;
					$total="";
					$ship_total="";
					$tax_total="";
					$tax="";
					foreach ($products as $products) {
						$tax_type="fixed";
						$proTotal =($products['price']*$products['qty']); 
						$tax = (!isset($products['tax_amount']))?getTaxs($location['shipping']['zipcode'], $proTotal):$products['tax_amount'];
						$image = explode(',',$products['img']);
						if($tax_type=="percent"){
							$subtotal=(($products['qty']*$products['price'])+(($products['qty']*$products['price'])*($tax/100)));
						}else if ($tax_type=="fixed"){
							$subtotal=(($products['price'] * $products['qty']) + (float)$tax) + (float)$products['shipping_price'];  
						}
						
					?>
    			<tr>
					<td><?php echo $products['name'];?></td>
					<td><img src="<?php echo ($products['is_local'] == '0')?$image[0]:product_thumb_path($image[0]);?>" style="width: 50px; height:  50px"></td>
					<td align="right"><?php echo $products['qty'];?></td>
					<td align="right"><?php echo "$".number_format((float)$products['price'], 2, '.', ''); if(isset($products['discountData']) && $products['discountData'] != ""){ $disc_data = unserialize($products['discountData']); ?>&nbsp;&nbsp;&nbsp;<strike><?php echo "$".$this->cart->format_number($disc_data['original']); ?></strike><?php }elseif($products['discount_value']){?>&nbsp;&nbsp;&nbsp;<strike class="text-danger"><?php echo "$".$this->cart->format_number($products['original']); ?></strike><?php }?></td>
					<td align="right"><?php echo "$".number_format((float)$products['shipping_price'], 2, '.', '');?> </td>
					<td align="right"><?php echo "$".number_format((float)$tax, 2, '.', '');?> </td>
					<td align="right"><?php echo "$".number_format((float)$subtotal, 2, '.', '');?></td>
				</tr>
				<?php
					$total= (float)$total + ((float)$products['price'] * $products['qty']);
					$ship_total = (float)$ship_total + (float)$products['shipping_price'];
					$tax_total= (float)$tax_total + (float)$tax;
					$i++;
				}
					$grand_total = $total + $ship_total + $tax_total;
				?>
				</tbody>
			</table>
			<table class="sub_total_table">
				<tbody style="float:right;">
					<tr class="invoice-label">
						<td align="left" class="border-0 pb-0">Sub-Total:</td>
						<td align="right" class="border-0 pb-0"><?php echo "$".$this->cart->format_number($total);?></td>
					</tr>
					<tr class="invoice-label">
						<td align="left" class="pb-0">Shipping:</td>
						<td align="right" class="pb-0"><?php echo "$".$this->cart->format_number($ship_total);?></td>
					</tr>
					<tr class="invoice-label">
						<td align="left" class="pb-0">Tax:</td>
						<td align="right" class="pb-0"><?php echo "$".$this->cart->format_number($tax_total);?></td>
					</tr>
					<tr class="invoice-label">
						<td align="left" class="grand_total">Grand Total:</td>
						<td align="right" class="grand_total"><?php echo "$".$this->cart->format_number($grand_total);?></td>
					</tr>
				</tbody>
			</table>
        </div>    
    </div>
</div>
    <!--<div class="" style="margin-top:50px">
        <h5>Payment Method</h5><p>The passage experienced a surge in popularity during the 1960s when Letraset used it on their dry-transfer sheets, and again during the 90s as desktop publishers bundled the text with their software. Today it's seen all around the web; on templates, websites, and stock designs. Use our generator to get your own, or read on for the authoritative history of lorem ipsum</p>
        <h5>Terms & Conditions</h5><p>Lorem ipsum, or lipsum as it is sometimes known, is dummy text used in laying out print, graphic or web designs. The passage is attributed to an unknown typesetter in the 15th century who is thought to have scrambled parts of Cicero's De Finibus Bonorum et Malorum for use in a type specimen book.</p>
        </div>
    </div>-->
</body>
</html>