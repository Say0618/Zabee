<?php
$type=$this->uri->segment(1);
if($type=="seller"){
    $this->load->view('front/head')
?>
<title><?php echo $title;?></title>
<?php } ?>
<?php 
	foreach($orders as $order){
		// echo "<pre>";print_r($order);
		$hasDiscount = isset($order->hasDiscount)?$order->hasDiscount:"";
		$discount_amount = isset($order->discount_amount)?$order->discount_amount:"";
// echo "<pre>"; print_r($sellerOrders); die();
?> 
<div class="container p-3">
    <nav class="navbar nav-backgroud justify-content-between" style="padding:0px">
        <p class="navbar-brand invoice-heading text-white" style="font-weight: 300; font-size: 36pt; letter-spacing: 15px; font-size: 36pt; letter-spacing: 15px; padding-left: 19px; margin-bottom: 0px;">INVOICE</p>
        <img class="img-fluid invoice-logo" src="<?php echo assets_url('front/images/LOGO_EMAIL.png') ?>" max-width="100%" height="auto" style="padding-right: 15px;"/>
    </nav>
    <div class="row p-2">
        <div class="col-4" >
            <h5 class="invoice-label"><?php echo $this->lang->line('bill_to');?>:</h5>
            <p>
                <?php if($order->card_holder != "paypal"){ echo $order->card_holder; ?><br />
                <?php  echo $order->billing['address_1'].",".$order->billing['city'];?><br />
                <?php  echo $order->billing['phone']; }else{ echo $order->card_holder;}?><br />
            </p>
        </div><br />
        <div class="col-4" >
            <h5 class="invoice-label"><?php echo $this->lang->line('ship_to');?>:</h5>
            <p>
                <?php  echo $order->shipping['name']; ?><br />
                <?php  echo $order->shipping['address_1'].",".$order->shipping['city'];?><br />
                <?php  echo $order->shipping['phone'];?><br />
            </p>
        </div>
        <div class="col-4">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h5 class="invoice-label"><?php echo $this->lang->line('invoice_no');?>:</h5>
                </div>
                <div class="col-sm-6 col-12">
                    <span class="text-break"><?php echo $type."_".$order->order_id; ?><span>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h5 class="invoice-label"><?php echo $this->lang->line('invoice_date');?>:</h5>
                </div>
                <div class="col-sm-6 col-12 pr-0">
                    <span id="invoice_date"><?php echo $order->created ?></span>
                </div>
            </div>
           <?php /* <span><label><h5 class="invoice-label">Invoice No:</h5></label><?php echo $type."_".$order->order_id; ?></span><br />
            <span><label><h5 class="invoice-label">Invoice Date:</h5></label><?php echo $order->created; ?></span><br /> */?>
        </div>
    </div>
        <?php } ?>
    <div class="row">
        <div class="col-12">
            <table class="table-bordered table table-responsive w-100 d-block d-md-table">
                <thead class="nav-backgroud"> 
                        <tr class="text-white text-center">
                            <th><?php echo $this->lang->line('title_inv');?></th>
                            <th><?php echo $this->lang->line('image_inv');?></th>
                            <th><?php echo $this->lang->line('qty_inv');?></th>
                            <th><?php echo $this->lang->line('price_inv');?></th>
                            <th><?php echo $this->lang->line('shipping_inv');?></th>
                            <th><?php echo $this->lang->line('tax_inv');?></th>
                            <th><?php echo $this->lang->line('total_inv');?></th>
                            </tr>
                     </thead>
                <tbody>
                <?php 
            $a=0;
            $total=array();
            $temp_total = 0;
            $shipAmt = 0;
            $taxAmt = 0;
            // echo"<Pre>";print_r($sellerOrders); die();
            foreach($sellerOrders as $key => $value){
                $subTotal=0;
                $length = count($sellerOrders [$key]); 
                for($i=0; $i<$length; $i++){
                    // echo "<pre>"; print_r($value[$i]); die();
                    $image = ($value[$i]->is_local == '0')?$value[$i]->product_image:product_thumb_path($value[$i]->product_image);
                    $subTotal = $subTotal + $value[$i]->item_gross_amount;
                    $temp_total = $temp_total + ($value[$i]->price * $value[$i]->qty);
                    $shipAmt += $value[$i]->item_shipping_amount;
                    $taxAmt = ($taxAmt + $value[$i]->tax_amount);
                    ?>
                        <tr>
                            <td>
                                <?php echo"<strong><u>".$value[$i]->product_name."</u></strong></br>";?>
                                <?php echo"<strong>Condition: </strong>". $value[$i]->condition_title."</br>";
                                    if(isset($value[$i]->seller_product_description) && $value[$i]->seller_product_description != ""){
                                        echo"<strong>Note: </strong>". $value[$i]->seller_product_description."</br>";
                                    }
                                    if(isset($value[$i]->variants) && $value[$i]->variants != ""){
                                        foreach(json_decode($value[$i]->variants) AS $variant){
                                            // echo"<pre>"; print_r($value); die();
                                            echo"<strong>".$variant->title.": </strong>". $variant->value."</br>";
                                        }
                                    }
                                ?>
                            </td>
                            <td align="center"><img src="<?php echo $image;?>" style="width: 50px; height:  50px"></td>
                            <td align="right"><?php echo $value[$i]->qty;?></td>
                            <td align="right"><?php echo "$".$this->cart->format_number($value[$i]->price); if(isset($value[$i]->discount) && $value[$i]->discount == "1"){ $disc_data = unserialize($value[$i]->discountData);?>&nbsp;&nbsp;&nbsp;<strike class="text-danger"><?php echo "$".$this->cart->format_number($disc_data['original']); ?></strike><?php } ?></td>
                            <td align="right"><?php echo"$".$this->cart->format_number($value[$i]->item_shipping_amount);?></td>  
                            <td align="right"><?php echo "$".$this->cart->format_number($value[$i]->tax_amount);?> </td>
                            <td align="right"><?php  echo "$".$this->cart->format_number($value[$i]->item_gross_amount);?></td>
                            </tr>
                            <?php } $total[$a] = $subTotal; ?>
                            <?php $a++;  } ?>

                    </tbody>
                </table>
                <table class="table-responsive table m-0">
                    <tbody class="float-right">
                        <tr class="invoice-label">
                            <td align="left" class="border-0 pb-0"><?php echo $this->lang->line('subtotal_title');?>:</td>
                            <td align="right" class="border-0 pb-0"><?php echo "$".$this->cart->format_number($temp_total);?></td>
                        </tr>
						<?php if($hasDiscount): ?>
                        <tr class="invoice-label">
                            <td align="left" class="pb-0"><?php echo $this->lang->line('coupon');?>:</td>
                            <td align="right" class="pb-0"><?php echo "$".$this->cart->format_number($discount_amount);?></td>
                        </tr>
						<?php endif; ?>
                        <tr class="invoice-label">
                            <td align="left" class="pb-0"><?php echo $this->lang->line('shipping');?>:</td>
                            <td align="right" class="pb-0"><?php echo "$".$this->cart->format_number($shipAmt);?></td>
                        </tr>
                        <tr class="invoice-label">
                            <td align="left" class="pb-0"><?php echo $this->lang->line('tax');?>:</td>
                            <td align="right" class="pb-0"><?php echo "$".$this->cart->format_number($taxAmt);?></td>
                        </tr>
                    </tbody>
                </table>
				<?php 
					$net_amount = ($hasDiscount)?array_sum($total)-$discount_amount:array_sum($total);
					
				?>
                <span class="float-right nav-backgroud text-white invoice-label p-2"><?php echo $this->lang->line('grand_total');?> : <?php echo "$".$this->cart->format_number($net_amount);?></span>
            </div>
        </div>    
    </div>
