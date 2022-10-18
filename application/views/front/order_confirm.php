<div class="container">
<div class="row">
    <div class="col-12">
      <h3 class="mb-5 mt-2">Order Confirmation</h3>
        <div class="card">
          <h5 class="card-header"><label for="">Order Number:</label><span class="pl-1"><?php echo $item_data[0]->order_id?></span></h5>
         <div class="card-body">
         <div class="row">
         <div class="col-4">
         <h5 class="card-title"><label for="">Tracking ID:</label><span class="pl-1"><?php echo $item_data[0]->tracking_id?></span></h5>
         <label for="">Shipping Company:</label><span class="pl-1"><?php echo $item_shipping_data[0]->title?></span><br/>
         <label for="">Estimated Deliver time:</label><span class="pl-1"><?php echo $item_shipping_data[0]->duration." days"?></span><br/>
         <label for="">Processing time:</label><span class="pl-1"><?php echo "7 days"?></span>
         </div>
         <div class="col-4">
         <h5 class="card-title">Remarks</h5>
         <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
         </div>
         <div class="col-4">
         <h5 class="card-title">Details</h5>
         <label for="">2019.04.26</label><span class="pl-1">Delivered to Address</span><br/>
         </div>
      </div>
    </div>
    </div>
    <!-- hi -->
    <div class="card mt-5">
      <div id="exTab1" class="container">	
        <ul  class="nav nav-pills mb-3">
        <li class="active">
          <a class="grey-color" href="#1a" data-toggle="tab">Overview</a>
        </li>
        <li class="ml-5"><a  class="grey-color" href="#2a" data-toggle="tab">Financial</a>
        </li>
        </ul>
        <div class="tab-content clearfix">
        <!-- tab1 -->
            <div class="tab-pane active" id="1a">
            </span>
            <label for="">Store:</label><span class="pl-1 pr-3"><a href="<?php echo base_url('seller_info/'.base64_encode(urlencode($item_data[0]->seller_id)));?>" class="grey-color" id=""><?php echo stripslashes($item_data[0]->store_name);?></a></span>
            <button type="button" data-pv_id="<?php echo $item_data[0]->product_vid;?>" data-sp_id="<?php echo $item_data[0]->sp_id;?>" data-seller_id="<?php echo $item_data[0]->seller_id;?>" data-store_name="<?php echo $item_data[0]->store_name;?>" class="btn contactuser contact-seller mb-2"><i class="fa fa-user"></i>Contact Seller</button>

            <?php
        $img= product_thumb_path().$item_data[0]->product_image;
        $shippingAddress =  unserialize($item_data[0]->shipping)?>
        <!-- card1 -->
            <div class="card mb-3">
              <div class="card-header">
                 <label for="">Name:</label><span class="pl-1 grey-color"><?php echo $shippingAddress['name']?></span><br/>
                 <label for="">Address:</label><span class="pl-1 grey-color"><?php echo $shippingAddress['address_1']?></span><span class="pl-1 grey-color"><?php echo $shippingAddress['city']?></span><span class="pl-1 grey-color"><?php echo (is_numeric($shippingAddress['state']))?$state:$shippingAddress['state']?></span><span class="pl-1 grey-color"><?php echo $country?></span><br/>
                 <label for="">Zip Code:</label><span class="pl-1 grey-color"><?php echo $shippingAddress['zipcode']?></span><br/>
                 <label for="">Phone:</label><span class="pl-1 grey-color"><?php echo $shippingAddress['phone']?></span><br/>
                </div>
              </div>
            <!-- card1 end -->
            <!-- card2 -->
            <div class="card mb-5">
              <div class="card-header">
                <div class="row">
                <div class="col-5">Product Details</div>
                <div class="col">price</div>
                <div class="col">Quantity </div>
                <div class="col">Total</div>
                <div class="col">Status</div>
                </div>
                </div>
                <div class="card-body">
                <div class="row">
                  <div class="col-2 order_list_image_div">
                    <img src="<?php echo  $img; ?>" alt="" class="pdImage img img-fluid mx-auto my-auto w_image-center pro_img">
                    </div>
                  <div class="col-3">
                  <span class=""><?php echo $item_data[0]->product_name?></span><br/>
                  <span class="grey-color"><?php echo $item_data[0]->condition_title?></span>
                    </div>
                  <div class="col-2">
                    <?php echo $item_data[0]->price?>
                   </div>
                  <div class="col-1">
                    <?php echo $item_data[0]->qty?>
                    </div>
                  <div class="col-2 text-center">
                    <?php echo $item_data[0]->item_total?>
                   </div>             
                   <div class="col-2 text-center">
                    <p>Confirmation Received</p>
                   </div> 
                  </div>
                </div>
                <div class="card-footer text-right">
                  <label for="">Shipping Amount:</label><span class="pl-1 grey-color"><?php echo $item_data[0]->item_shipping_amount?></span><br/>
                  <label for="">Total Amount:</label><span class="pl-1 grey-color"><?php echo $item_data[0]->item_gross_amount?></span>
                </div>
              </div>
            <!-- endcard2 -->
            </div>
          <!-- end -->
            <div class="tab-pane" id="2a">
              <h3>We use the class nav-pills instead of nav-tabs which automatically creates a background color for the tab</h3>
            </div>
        </div>

        </div>
      </div>
    <!-- bye --> 
    </div>
</div>
</div>



<div class="modal fade" id="message-panel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span>Contact</span><span class="pl-1" id="storeName"></span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="panel-body">
                        <div class="form-group">
                          <textarea class="form-control" id="message" rows="8" style="border-radius:5px;"></textarea>
                          <span></span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-right">
							<input type="hidden" id="seller_id" />
							<input type="hidden" id="sp_id" />
							<input type="hidden" id="pv_id" />
                        	<a href="javascript:" class="btn btn-primary" id="sendMessage">Send</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>