<div class="container">
    <div class="row">
        <?php 
    $condition = array(); 
    $ship = array();
        ?>
        <div class="col-12 mt-3">
         <h4 class="zabee-product-name"><?php echo($productName);?></h4>
        </div>
       <div class="col-sm-3 col-md-3 col-lg-3">
         <h4 class="zabee-product-name mt-3 mb-4"> Refine by &nbsp;<a class="text-color" href="<?php echo base_url('product/offerlisting/'.$detail_id)?>" style="font-size:14px;">clear all</a></h4>
            <h5 class="zabee-product-name mb-1">Shipping</h5>
            <?php foreach($shippingData as $sd){(isset($_GET['shipping']))?$ship = explode(',',base64_decode(urldecode($_GET['shipping']))):$Ship=array();?>
                    <div class="row">
                        <div class="col-12">
                            <div class="custom-control custom-checkbox mb-1 checkboxLine-height">
                                <input type="checkbox" class="custom-control-input shipping_class"<?php foreach($ship as $s){echo ($s == $sd->shipping_id)?"checked='checked'":"";}?>  id="<?php echo $sd->shipping_id?>" name="shipping" value="<?php echo $sd->shipping_id?>">
                                <label class="custom-control-label custom-checkout-label" for="<?php echo $sd->shipping_id?>"><?php if($sd->price == 0){echo "Free Shipping";}else{echo $sd->title;}?></label>
                            </div> 
                        </div>
                    </div>
            <?php } ?>
                <h5 class="zabee-product-name mb-1 mt-3">Condition</h5>
                <?php  foreach($productConditions as $conditions){ (isset($_GET['filter']))?$filter = explode(',',base64_decode(urldecode($_GET['filter']))):$filter=array(); //print_r($filter) ?>
				<div class="row">
                    <div class="col-12">
                        <div class="custom-control custom-checkbox mb-1 checkboxLine-height">
                            <input type="checkbox" class="custom-control-input condition_class"<?php foreach($filter as $f){echo ($f == $conditions->condition_id)?"checked='checked'":"";}?> value="<?php echo $conditions->condition_id?>" id="condition<?php echo $conditions->condition_id?>">
                            <label class="custom-control-label custom-checkout-label" for="condition<?php echo $conditions->condition_id?>"><?php echo $conditions->condition_name ?></label>
                        </div>
                    </div>
                </div>
				<?php }?>
       </div> 
       <div class="col-sm-9 col-md-9 col-lg-9">
       <ul class="clearfix ProdSpecs condition_click mt-3">
         <?php foreach($productConditions as $conditions){
             $condition[$conditions->condition_id] = $conditions->condition_name;
            	$current_link = $_SERVER['REQUEST_URI'];
				$url=strtok($_SERVER["REQUEST_URI"],'?');
				$condition_link = $current_link;
				$all_link = current_url();
				if(isset($_GET['condition'])){
					$condition_link = str_replace('condition='.$conditions->condition_id, '', $all_link);
				}
				if(isset($_GET['filter'])){
					$condition_link = str_replace('filter='.$conditions->condition_id,'', $all_link);
				}
			?>
         <?php } ?>
            <li>
                    <a class="firstImage" href='<?php echo $all_link;?>'>
                        <span>All</span>
                    </a>
                </li>
               <?php foreach($productConditions as $conditions){ ?>
            <li class="<?php echo ($condition_active == $conditions->condition_id )?"selected":""?>">
                    <a class="firstImage <?php echo ($condition_active == $conditions->condition_id)?"selected":""?>" href='<?php echo $condition_link.'?condition='.urlencode(base64_encode($conditions->condition_id));?>'>
                        <span><?php echo $conditions->condition_name;?></span>
                    </a>
                </li>
        <?php } ?>
        </ul>
       <?php $i = 0;?>
        <?php foreach($ProductData as $productdata){ 
           //echo "<pre>"; print_r($productdata);echo "</pre>";
            ?>
        <?php
        if($productdata->image_thumb == ""){
            // print_r($productImage); die();
            $image = ($productImage->is_local == 0 || $productImage->is_local == "")?$productImage->is_primary_image:product_thumb_path($productImage->is_primary_image);
        }else{
            $image = ($productdata->is_local == 0 || $productdata->is_local == "")?$productdata->image_thumb:product_thumb_path($productdata->image_thumb);
        }
        //$image = isset($image)?product_thumb_path($image):website_img_path('preview.png');
            ?>
            <?php $product_id = urlencode(base64_encode($productdata->product_id)); ?>
            <div id = "loader" style="display: none;">
            <center>
                <img src = "<?php echo website_img_path('preloader.gif'); ?>" />								
                <span style="color:#6ca2cc">Loading Products</span>
            </center>
        </div>
                <div class="row offerRow">
                    <div class="col-sm-3 col-xs-6 wordwrap mb-1">
                        <h5 class="zabee-product-name mb-2"><?php echo $condition[$productdata->condition_id];?></h5>
                        <div>
                        <?php
                            $ribbon = "";
                            $today = date("Y-m-d");
                            $today_time = strtotime($today);
                            $expireTo_feature = $productdata->valid_to; 
                            $expireFrom_feature =  $productdata->valid_from; 
                            $expire_time_to_feature = strtotime($expireTo_feature);
                            $expire_time_from_feature = strtotime($expireFrom_feature);
                            $discounted = discount_forumula($productdata->price, $productdata->discount_value, $productdata->discount_type, $productdata->valid_from, $productdata->valid_to);
                            if($productdata->discount_value != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
                                if($productdata->discount_type == "percent"){
                                    $ribbon = "<div class='ribbon ribbon-top-left'><span>".$productdata->discount_value."% OFF</span></div>";
                                }elseif($productdata->discount_type == "fixed"){
                                    $ribbon = ($discounted)?"<div class='ribbon ribbon-top-left'><span>$".$productdata->discount_value."/- OFF</span></div>":"";
                                }
                            }
                        ?>
                            <a href="<?php echo base_url().'product/'.$productdata->slug.'/'.$productdata->pv_id;?>"><img class="img img-fluid mx-auto my-auto heightWidth" src="<?php echo $image;?>" alt="<?php echo($productName)." $".$this->cart->format_number($productdata->price);?>"></a>
                            <?php if($productdata->discount_value != "" && $productdata->discount_type == "percent" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){?>
                                <!-- <div class="ribbon ribbon-top-left"><span><?php echo $productdata->discount_value?>% OFF</span></div> -->
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6 wordwrap">
                        <h5 class="zabee-product-name mb-2">Price + Shipping</h5>
                        <span class="price">
                            $<?php echo ($discounted)?$this->cart->format_number($discounted):$this->cart->format_number($productdata->price);?>
                            <?php if($productdata->discount_value != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time && $discounted){?>
                                <strike>$<?php echo $this->cart->format_number($productdata->price)?></strike>&nbsp;&nbsp;<span class="offer-disc"><?php echo ($productdata->discount_value == "percent")?$productdata->discount_value."% OFF":"$".$productdata->discount_value."/- OFF"?></span>
                            <?php } ?>
                        </span>
                        <div class="input-group">
                            <label id="shipping_method<?php echo $i;?>" data-ship="<?php echo $productdata->lowestShipping_id?>" data-id="<?php echo "asd".$productdata->shipping_ids;?>" class="col-form-label shipping_link"></label>
                        </div>
                        <span class="error small" id="qty-error"></span>
                        <input type="hidden" name="pvid" id="product_variant_id" value="<?php echo $productdata->pv_id;?>" />
                    </div>
        
                    <div class="col-sm-3 col-xs-6 wordwrap">
                        <h5 class="zabee-product-name  mb-2">Seller Information</h5>
                        <div class="price"><?php echo stripslashes($productdata->seller_name);?></div>	
                        <div class="price"><?php echo stripslashes($productdata->store_name);?></div> 
                    </div>
                    <div class="col-sm-3 col-xs-6 wordwrap" >
                        <h5 class="zabee-product-name mb-2">Actions</h5>
                        <div class="row">
                            <span class="col-1">
                                <a href="<?php echo base_url().'product/'.$productdata->slug.'/'.$productdata->pv_id;?>" class="info-icon" data-toggle="tooltip" title="Product detail"><i class="fas fa-info"></i></a>
                            </span>
                            <?php if(isset($_SESSION['userid']) && $productdata->seller_id != $_SESSION['userid']){?>
                                <div class="col-2">
                                    <a href="javascript:void(0)" class="info-icon cartAdd" id="addToCartBtn" data-row="<?php echo $i?>" data-toggle="tooltip" title="Add to cart" data-product_variant_id="<?php echo $productdata->pv_id ?>" data-available_shipping_ids="<?php echo $productdata->shipping_ids;?>"><i class="fa fa-shopping-cart"></i></a>
                                </div>
                            <?php }else if(!isset($_SESSION['userid'])){?>
                                <div class="col-2">
                                    <a href="javascript:void(0)" class="info-icon cartAdd" id="addToCartBtn" data-row="<?php echo $i?>" data-toggle="tooltip" title="Add to cart" data-product_variant_id="<?php echo $productdata->pv_id ?>" data-available_shipping_ids="<?php echo $productdata->shipping_ids;?>"><i class="fa fa-shopping-cart"></i></a>
                                </div>
                            <?php }?>
                            <div class="col-2">	
                                <!-- <?php if(isset($_SESSION['userid']) && $productdata->seller_id != $_SESSION['userid']){
                                        if($productdata->already_saved == ""){  ?>
                                        <a class="addToWishlistBtn" id="addToWishlistBtn" data-toggle="tooltip" title="Save for later" data-product_id="<?php echo $productdata->product_id?>" data-product_variant_id="<?php echo $productdata->pv_id;?>" data-id = "<?php echo $productdata->product_id."-".$productdata->pv_id?>" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></a>
                                <?php } else{ ?>
                                    <div class="already-saved" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></div>
                                <?php } } else if(!$this->isloggedin){ ?>
                                    <a class="addToWishlistBtn" id="addToWishlistBtn" data-toggle="tooltip" title="Save for later" data-product_id="<?php echo $productdata->product_id?>" data-product_variant_id="<?php echo $productdata->pv_id;?>" data-id = "<?php echo $productdata->product_id."-".$productdata->pv_id?>" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></a>
                                <?php } ?> -->
                            </div>
                        </div>
                    </div>
                    <?php if($productdata->seller_product_description !=""){?>
                    <div class="col-sm-12 py-2">
                        <strong>Note:</strong><span><?php echo $productdata->seller_product_description?></span>
                    </div>
                    <?php }?>
                </div>
                <div class="clearfix"></div>
       <?php $i++; } ?>
    </div>
</div>

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
                        <th>Shipping Cost</th>
                        <th>Tracking Information</th>
                    </thead>
                    <tbody>
                    <?php foreach($shippingData as $sd){ ?>
                    	<tr class="shippingRow shipping<?php echo $sd->shipping_id?>">
                            <td>
                            	<div class="custom-control custom-radio mb-3">
                                    <input type="radio" class="custom-control-input shipping_method" data-shipping_id="<?php echo $sd->shipping_id?>" data-title="<?php echo addslashes($sd->title)?>" data-price="<?php echo $sd->price?>" id="shipping<?php echo $sd->shipping_id?>" name="shipping_method" value="<?php echo $sd->shipping_id?>"/>
                                    <label class="custom-control-label radio-custom-input seller-info" for="shipping<?php echo $sd->shipping_id?>"><?php echo $sd->title?></label>
                                </div> 
                			</td>
                            <td><?php echo $sd->duration." days"?></td>
                            <td><?php echo ($sd->price == 0)?"Free Shipping":"US $".$this->cart->format_number($sd->price);?></td>
                            <td>Not Available</td>
                        </tr>
					<?php }?>
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