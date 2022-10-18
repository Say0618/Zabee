<div class="minicart-header">
    <div class='minicart--item-container'>
        <?php echo $this->lang->line('you_have');?>
        <span class='minicart--item-count' style='font-weight: 600'><?php echo $this->cart->total_items()?> <?php echo $this->lang->line('items');?></span>
        <?php echo $this->lang->line('items_cart');?>
        <div id="minicartDismiss">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <hr />
</div>
<div class="minicart">
<?php 
	 if($this->session->userdata('cart_contents') && $this->cart->total_items() > 0){
		$shipping_total = 0;
		?>
   
	  <ul class="list-unstyled">
	   <?php foreach ($this->cart->contents() as $items){
		   $shipping_total = (int)$shipping_total+$items['shipping_price'];
			$img = $items['img'];
			if($items['is_local'] == 1){
				$img = product_thumb_path($img);
			}
			?>
		<li class='minicart--item'>
		  <div class='minicart-placeholder'><img src="<?php echo $img; ?>" alt='<?php echo $items['name'].' $'.$this->cart->format_number($items['price']); ?>' class="img img-fluid mx-auto  my-auto image-center inherit-height"></div>
		  <h1 class='title'><?php echo (strlen($items['name']) > 18)?(substr($items['name'],0,18))."...":($items['name']);?></h1>
		  <p class='material'>
			<span style='font-weight: 600'><?php echo $this->lang->line('condition');?>:</span>
			<?php echo $items['condition']?>
		  </p>
		  <p class="size" >
			<span style='font-weight: 600'><?php echo $this->lang->line('shipping');?>:</span>
			<?php echo (strlen($items['shipping_title']) > 15)?(substr($items['shipping_title'],0,15))."...":($items['shipping_title']);?>
		  </p>
		  <p class='price'>$<?php  echo $this->cart->format_number($items['price']); ?> USD (x <?php echo $items['qty']?>)</p>
		  <p class='remove error'>
			<a onclick="deleteFromCart('<?php echo $items['rowid']; ?>', false,'cart')" class="remove-product" title="remove Item">
			  <i class='fa fa-trash-o'></i>
			  <?php echo $this->lang->line('remove_from_cart');?>
			</a>
		  </p>
		</li>
		<?php }?>
	  </ul>
	  <div class='minicart--subtotal'>
		<!--<p class='minicart--subtotal-title'><?php echo $this->lang->line('subtotal_title');?></p>
		<p class='minicart--subtotal-amount'>$<?php echo $this->cart->format_number($this->cart->total()); ?> USD</p>
		<div class="clearfix"></div>
		<p class='minicart--subtotal-title'><?php echo $this->lang->line('shipping_title');?></p>
		<p class='minicart--subtotal-amount'>$<?php echo $this->cart->format_number($shipping_total); ?> USD</p>
		<div class="clearfix"></div>-->
		<p class='minicart--subtotal-title'><?php echo $this->lang->line('grand_total');?></p>
		<p class='minicart--subtotal-amount'>$<?php echo $this->cart->format_number($this->cart->total()); ?> USD</p>
	  </div>
	  <a href="<?php echo base_url("cart");?>">
	  <input type='button' value='<?php echo $this->lang->line('view_cart');?>'>
	  </a>
	  <a href="<?php echo base_url("checkout");?>">
	  <input type='button' value='<?php echo $this->lang->line('checkout');?>'>
	  </a>
<?php }?>
</div>