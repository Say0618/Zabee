<div class="row">
          <div class="col-md-6">
            <table class="table table-responsive w-100 d-block d-md-table table-bordered">
              <tr>
                <th class="text-center" colspan="4" >Bill to</th>
              </tr>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Contact</th>
              </tr>
              <?php if($billing != "paypal"){ ?>
                <tr>
                  <td><span id="billing_name"></span><?php echo $billing['name'] ?></td>
                  <td><span id="billing_address"></span><?php echo $billing['address_1']."<br />".$billing['city'].", ".$billing['state']." ".$billing['zipcode']."<br/>".$billing['country']."<br/>". $billing['address_2'] ?></td>
                  <td><span id="billing_phone"></span><?php echo $billing['phone'] ?></td>
                </tr>
              <?php }else{ ?>
                <tr>
                  <td><span id="billing_name"></span><?php echo $shipping['name'] ?></td>
                  <td><span id="billing_address"></span>PAYPAL</td>
                  <td><span id="billing_phone"></span><?php echo $products[0]->email ?></td>
                </tr>
              <?php } ?>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-responsive w-100 d-block d-md-table table-bordered">
              <tr>
                <th class="text-center" colspan="4">Ship to</th>
              </tr>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Contact</th>
              </tr>
              <tr>
                <td><span id="shipping_name"></span><?php echo $shipping['name'] ?></td>
                <td><span id="shipping_address"></span><?php echo $shipping['address_1']."<br />".$shipping['city'].", ".$shipping['state']." ".$shipping['zipcode']."<br/>".$shipping['country']."<br/>". $shipping['address_2'] ?></td>
                <td><span id="shipping_phone"></span><?php echo $shipping['phone'] ?></td> 
              </tr>
            </table>
          </div>
        </div>
        <div id="products">
            <table class='table table-responsive w-100 d-block d-md-table table-bordered'>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Condition</th>
                    <th>QTY</th>
                    <th>Price</th>
                    <th>Tax</th>
                    <th>Shipping</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Reason</th>
                </tr>
                <?php
                $total = 0.0;
                foreach($products as $product){
                  $action = $product->action;
                  switch($product->action){
                    case '0':
                      $action = 'Pending';
                      $action_class = 'text-warning';
                    break;
                    case '1':
                      $action = 'Approved';
                      $action_class = 'text-success';
                    break;
                    case '2':
                      $action = 'Declined';
                      $action_class = 'text-danger';
                    break;
                    case '3':
                      $action = 'Refunded';
                      $action_class = 'text-warning';
                    break;
                  }
                ?>
                    <tr>
                        <th><img src="<?php echo product_thumb_path($product->image_link) ?>" class="img" height="25"></th>
                        <th><?php echo $product->product_name ?></th>
                        <th><?php echo $product->condition_name ?></th>
                        <th><?php echo $product->qty ?></th>
                        <th><?php echo $this->cart->format_number($product->price) ?></th>
                        <th><?php echo $this->cart->format_number($product->tax_amount) ?></th>
                        <th><?php echo $this->cart->format_number($product->item_shipping_amount) ?></th>
                        <th><?php echo $this->cart->format_number($product->item_gross_amount) ?></th>
                        <th><span class="<?php echo $action_class; ?>"><?php echo $action; ?></span></th>
                        <th><span><?php echo $product->cancel_reason; ?></span></th>
                    </tr> 
                <?php $total = floatval($total + $product->item_gross_amount); } ?>
        </div>
        <div class="col-sm-12 text-right">
          <!--  <b>Tax:&nbsp</b><span id="tax"></span><br> -->
          <b>Total:&nbsp</b><span id="total"><?php echo $this->cart->format_number($total); ?></span><br>
        </div>

        <input type="hidden" id="buyer_id" value="<?php echo $products[0]->user_id?>">
        <input type="hidden" id="sp_id" value="<?php echo $products[0]->order_id?>">
        <input type="hidden" id="pv_id" value="<?php echo $products[0]->product_vid?>">
