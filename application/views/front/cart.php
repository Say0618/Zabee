<?php
	$browser = 0;
?>
<script>
var browser = "";
if (/MSIE 10/i.test(navigator.userAgent)) { browser = 1; }
if (/MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent)) { browser = 2; }
if (/Edge\/\d./i.test(navigator.userAgent)){ browser = 3; }
document.cookie = "browser = " + browser
</script>	
<div class="container" id="cart-container">
<?php 
// echo "<pre>"; print_r($this->cart->contents()); die();
	if($this->session->flashdata('same_product')){ ?>
		<div class="alert alert-danger alert-dismissible fade show mt-3">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
				<div class="" align="center">
					<strong><?php echo $this->lang->line('warning');?>!&nbsp</strong><?php echo $this->lang->line('following_removed');?>
					<?php
					foreach($this->session->flashdata('same_product') as $same_product){
						echo $same_product."&nbsp";
					}?>
				</div>
		</div>
	<?php } ?>
    <?php if($this->session->userdata("names") != null){ ?>
		<div class="alert alert-danger alert-dismissible fade show mt-3">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="" align="center">
				<strong>Warning!&nbsp <?php echo implode(',', $this->session->userdata("names")) ?></strong>
			</div>
		</div>
	<?php }
		$this->session->set_userdata("names" ,""); 
	?>
        <?php 
            if($this->cart->total_items() > 0){
                foreach($cart as $c){
        ?>
        <div class="row">            
            <div class="shopping-cart my-3 w-100">
                <div class="mb-3">
                    <label class="w-25"> <?php echo $c['store_name']?></label>
                </div>
                <div class="column-labels">
                    <label class="product-image p-0"><?php echo $this->lang->line('items');?></label>
                    <label class="product-details"><?php echo $this->lang->line('product');?></label>
                    <label class="product-price"><?php echo $this->lang->line('price');?></label>
                    <label class="product-quantity"><?php echo $this->lang->line('quantity');?></label>
                    <label class="product-line-price"><?php echo $this->lang->line('total');?></label>
                </div>
			    <?php
                    $i = 0;
                    $j = 0;
                    $shipping_total = 0;
                ?>
                <?php
                    foreach ($c as $items){
                        if(is_array($items)){
                        $img = $items['img'];
                        if($items['is_local'] == 1){
                            $img = product_thumb_path($img);
                        }

                    $product_link = urlClean($items['name']);
					$product_link=str_replace("(","", $product_link);
					$product_link=str_replace(")","", $product_link);
					$product_link=str_replace(" ","-", $product_link);
                ?>
                <div class="cart-product cart<?php echo $items['rowid']?>">
                    <div class="product-image pr-3 p-0">
                      <img src="<?php echo $img; ?>" alt="<?php echo $items['name']." $".$this->cart->format_number($items['price']); ?>" class="img img-fluid">
                    </div>
                    <div class="product-details">
                      <div class="product-title"><a href="<?php echo base_url().'product/'.$items['slug']?>" class="cart-title" title="<?php echo $items['name'];?>"><?php echo (snippetwop($items['name'],'55','...'))?></a></div>
                        <p class="product-description"><?php echo "Condition: <strong>".$items['condition']."</strong>"?><br />
                        <?php if(isset($items['sp_description']) && $items['sp_description'] != ""){ echo "Note: <strong>".$items['sp_description']."</strong> <br />"; } ?>
                        <?php if ($this->cart->has_options($items['rowid']) == TRUE){ 
                            if($this->cart->product_options($items['rowid'])){
                        ?>
                        <?php 
                            foreach ($this->cart->product_options($items['rowid']) as $option_name => $option_value){ ?>
                            <?php echo $option_name; ?>: <strong><?php echo $option_value; ?></strong><br />
                            <?php } ?>
                        <?php } } ?>
                        <?php 	
                            $shipping_total = (int)$shipping_total+$items['shipping_price'];
                            $label = "Shipped by";
                            $shipping_title = $items['shipping_title'];
                        ?>
                            <?php /*<strong>Shipping Cost:</strong> <span id="shipping_method<?php echo $j;?>" data-ship="<?php echo $items['shipping_id']?>" data-pv_id="<?php echo $items['id'];?>" data-row_id="<?php echo $items['rowid'];?>" class="col-form-label shipping_link"><a href="javascript:void(0)" class="stylelink" checked="checked" onClick="openModal('<?php echo $j?>','<?php echo $items['shipping_id']?>')"><span class="price"><?php echo $shipping_price?></span> <strong><span class="shippingTitle"><?php echo "via ".$items['shipping_title']?></span></strong></a></span>?*/?>
                            Shipped by: <span id="shipping_method<?php echo $j;?>" data-ship="<?php echo $items['shipping_id']?>" data-pv_id="<?php echo $items['id'];?>" data-row_id="<?php echo $items['rowid'];?>" class="col-form-label shipping_link"><strong><span class="shippingTitle"><?php echo $shipping_title?></span></strong></span>
                            <?php if(isset($items['update_msg']) && $items['update_msg'] != ""){ ?>
                                <br><strong><span class="text-warning"><?php echo $items['update_msg']; ?></span></strong>
                            <?php } ?>
                        </p>
                        <p class="product-action">
                            <a onclick="deleteFromCart('<?php echo $items['rowid']; ?>', true,'cart')"class="remove-product" title="remove Item"><?php echo $this->lang->line('delete');?></a>
                            <span class="pl-2 pr-2"><strong>|</strong></span>
                            <a class="remove-product saveormove" data-row_id = "<?php echo $items['rowid'] ?>" data-action="saveforlater"><?php echo $this->lang->line('save_later');?></a>
                        </p>    
                    </div>
                    <?php 
                    $maximumPrice = 0; 
                    if($items['qty'] > $items['max_qty']){ 
                        $maximumPrice = $items['max_qty']*$items['price']; 
                    } else { 
                        $maximumPrice = $this->cart->format_number($items['subtotal']); 
					}
					?>
                    <div class="product-price position-relative">
                    <?php if(($items['original'] != $items['price'])){
                        $discount = ""; 
                        if($items['discount_type'] == "percent"){ $discount = "%"; $dis_type = "";}else if($items['discount_type'] == "fixed"){ $discount = "/- OFF"; $dis_type = "$";}
                    ?>
                        <?php if($items['price']){ ?><strike class="cart-discount-price">$<?php echo $this->cart->format_number($items['original'])?></strike>
                        <span class="discount-extra position-absolute <?php echo ($discount)?"d-block":"d-none"?>">
                            <strong class=""><?php echo $dis_type.$items['discount_value']?></strong>
                            <strong class=""><?php echo $discount?></strong>
                        </span>
                    <?php } } $items['price'] = ($items['price'])?$items['price']:$items['original']; ?>

                    <span class="currency item-price"><?php  echo $this->cart->format_number($items['price']); ?></span></div>
                    <div class="product-quantity">
                        <?php
                            $browser = (isset($_COOKIE['browser']))?$_COOKIE['browser']:1;
                        ?>
                        <input type="<?php echo ($browser != 3)?'number':'text'?>" name="<?php echo "qty".$i."[]" ?>" data-row="<?php echo $items['rowid']?>" data-index ="<?php echo $i?>" value = "<?php echo ($items['qty'] > $items['max_qty'] ) ? $items['max_qty'] : $items['qty'] ?>" min = "1" max = "<?php echo $items['max_qty'] ?>" size = "5" class = "item_qty text-right" id="<?php echo "item_qty".$i ?>"> <br />
                        <span class="<?php echo $i."_pops item-qty-error d-none" ?>" style="font-size: 12px; color: red;" >Max quantity approached</span>
                        <span class="<?php echo $i."_pops item-qty-error d-none invalid" ?>" style="font-size: 12px; color: red;" >Invalid quantity</span>	
                    </div>
                    <div class="product-line-price"><span class="currency subtotal" id="subtotal<?php echo $i?>"><?php echo $maximumPrice;?></span></div>
                </div>
                <?php //$shipData[]=array('pv_id' => $items['id'],'available_shippings' => $items['available_shippings'],'shipping_id' => $items['shipping_id'],'title' => $items['shipping_title'],'price' => $items['price'], 'allShipData' =>$items['shippingData'] ); ?>
                <?php $i++; $j++; ?>
                <hr>
                <?php }}?>
                <div class="totals">
                    <div class="totals-item totals-item-total">
                        <label><?php echo $this->lang->line('subtotal_title');?></label>
                        <!-- <div class="totals-value product-line-price" id="cart-total"><?php //echo $this->cart->format_number(($this->cart->total()+$shipping_total+$this->config->item('vat_tax'))); ?></div> -->
                        <div class="totals-value product-line-price currency sub-total"><?php echo $this->cart->format_number($c['subtotal']); ?></div>
                    </div>
                </div>
            </div>
        </div>
                <?php } ?>
    <div class="row">
        <div class="shopping-cart my-3 w-100">
            <div class="totals">
                <div class="totals-item totals-item-total mb-2">
                <label><?php echo $this->lang->line('grand_total');?></label>
                <div class="totals-value product-line-price" id="cart-total"><?php echo $this->cart->format_number($this->cart->total()); ?></div>
                </div>
            </div>
            <a class="btn btn-hover color-green float-right ml-2" id="checkoutBtn" href="javascript:void(0)"><?php echo $this->lang->line('checkout');?></a>
            <a class="btn btn-hover color-blue float-right"  href="<?php echo base_url();?>"><?php echo $this->lang->line('continue_shopping');?></a>
        </div>
    </div>
    <?php } else { ?>
        <div class="row">
            <div class="shopping-cart w-100">
    	        <div class="text-center"><b><?php echo $this->lang->line('cart_empty');?></b></div>
            </div>
        </div>
    <?php } ?>
</div>
<?php  if($this->session->userdata('save_contents') && $this->saveforlater->total_items() > 0){
	?>
    <div class="container" id="saved-for-later_container">
        <div class="cart-order mt-4 pb-4"><?php echo $this->lang->line('save_later');?></div>
        <div class="shopping-save-for-later">
            <div class="column-labels">
                <label class="product-image p-0"><?php echo $this->lang->line('items');?></label>
                <label class="product-details"><?php echo $this->lang->line('product');?></label>
                <label class="product-price"><?php echo $this->lang->line('price');?></label>
            </div>
                <?php
                    $i = 0;
                    $j = 0;
                    $shipping_total = 0;
                    foreach ($this->saveforlater->contents() as $items){
                        $img = $items['img'];
                        if($items['is_local'] == 1){
                            $img = product_thumb_path($img);
                        }
                ?>
                <div class="cart-product saveforlater<?php echo $items['rowid'] ?>">
                    <div class="product-image pr-3 p-0">
                      <img src="<?php echo $img; ?>" alt="<?php echo $items['name']." $".$this->cart->format_number($items['price']);?>" class="img img-fluid">
                    </div>
                    <div class="product-details">
                      <div class="product-title"><a href="#" class="cart-title" title="<?php echo $items['name'];?>"><?php echo (snippetwop($items['name'],'55','...'))?></a></div>
                        <p class="product-description"><?php echo "<strong>Condition:</strong> ".$items['condition']?><br />
                        <?php if ($this->saveforlater->has_options($items['rowid']) == TRUE){ 
                            if($this->saveforlater->product_options($items['rowid'])){
                        ?>
                        <?php 
                            foreach ($this->saveforlater->product_options($items['rowid']) as $option_name => $option_value){ ?>
                            <strong><?php echo $option_name; ?>:</strong> <?php echo $option_value; ?><br />
                            <?php } ?>
                        <?php } } ?>
                        <?php
                            $shipping_total = $shipping_total+$items['shipping_price'];
                            if($items['shipping_price'] > 0){
                                $shipping_price = "US $".$items['shipping_price'];
                            }else{ 	 
                                $shipping_price = "Free Shipping"; 
                            }?>
                            <!-- <strong>Shipping Cost:</strong> <span class="price">US $<?php echo $items['shipping_price']?></span> <strong>via <span class="shippingTitle"><?php echo $items['shipping_title']?></span></strong> -->
                            Shipped by: <span id="shipping_method<?php echo $j;?>" data-ship="<?php echo $items['shipping_id']?>" data-pv_id="<?php echo $items['id'];?>" data-row_id="<?php echo $items['rowid'];?>" class="col-form-label shipping_link"><strong><span class="shippingTitle"><?php echo $items['shipping_title']?></span></strong></span>
                         </p>
                         <p class="product-action">
                            <a onclick="deleteFromCart('<?php echo $items['rowid']; ?>', true,'saveforlater')"class="remove-product" title="remove Item">Delete</a>
                            <span class="pl-2 pr-2"><strong>|</strong></span>
                            <a class="remove-product saveormove" data-row_id = "<?php echo $items['rowid'] ?>" data-action="movetocart">Move to cart</a>
                         </p>    
                    </div>
                    <div class="product-price position-relative">
                    <?php if(($items['original'] != $items['price'])){
                        $discount = ""; 
                        if($items['discount_type'] == "percent"){ $discount = "%"; $dis_type = "";}else if($items['discount_type'] == "fixed"){ $discount = "/- OFF"; $dis_type = "$";}
                    ?>
                        <?php if($items['price']){ ?><strike class="cart-discount-price">$<?php echo $this->cart->format_number($items['original'])?></strike>
                        <span class="discount-extra position-absolute <?php echo ($discount)?"d-block":"d-none"?>">
                            <strong class=""><?php echo $dis_type.$items['discount_value']?></strong>
                            <strong class=""><?php echo $discount?></strong>
                        </span>
                    <?php } } $items['price'] = ($items['price'])?$items['price']:$items['original']; ?>
                        <span class="currency">
                            <?php  echo $this->saveforlater->format_number($items['price']); ?>
                        </span>
                    </div>
                </div>
                <?php $i++; $j++; ?>
                <?php } ?>
        </div> 
    </div>
<?php } ?>
<!-- modal starts -->
<div id="choose_shipping_method" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            	<h4 class="modal-title">Choose Shipping Method</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
            </div>
            <div class="modal-body">
            	<table class="table table-bordered">
                	<thead>
                    	<th>Shipping Company</th>
                        <th>Estimated Delivery Time</th>
                        <!-- <th>Shipping Cost</th>
                        <th>Tracking Information</th> -->
                        <th>Description</th>
                    </thead>
                    <tbody id="shipping_tbody">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="indexId" />
            	<button type="button" class="btn btn-default" data-dismiss="modal">close</button>
            </div>
        </div>
    </div>
</div> 