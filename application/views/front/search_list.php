<?php
$today = date("Y-m-d");
$today_time = strtotime($today);
if(!empty($productData)){
    $i = 0;
    $cartbtn= 0;
    $heartbtn=0;
?>
    <div class="row py-3" id="showview">
    <?php $index = 0; foreach($productData as $pd){
        // echo"<pre>"; print_r($pd); //die();
        /*$product_link = urlClean( $pd['product_name']);
        $product_link=str_replace("(","", $product_link);
        $product_link=str_replace(")","", $product_link);*/
    ?>
    <div class="col-12 pl-3 mb-custom">
        <div class="polaroid">
            <div class="image-center-parent row">  
            <?php
                $ribbon = "";
                $expireTo_feature = $pd['valid_to']; 
                $expireFrom_feature =  $pd['valid_from']; 
                $expire_time_to_feature = strtotime($expireTo_feature);
                $expire_time_from_feature = strtotime($expireFrom_feature);
                $discounted = discount_forumula($pd['price'], $pd['discount_value'], $pd['discount_type'], $pd['valid_from'], $pd['valid_to']);
                // echo $discounted;
                if($pd['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
                    if($pd['discount_type'] == "percent"){
                        $ribbon = "<div class='ribbon ribbon-top-left'><span>".$pd['discount_value']."% OFF</span></div>";
                    }elseif($pd['discount_type'] == "fixed"){
                        $ribbon = ($discounted)?"<div class='ribbon ribbon-top-left'><span>$".$pd['discount_value']."/- OFF</span></div>":"";
                    }
                }
                // echo "<pre>"; print_r($today_time >= $expire_time_from_feature); die();
            ?>
            <?php if($pd['is_local'] == '1'){ ?>
                <a href="<?php echo base_url().'product/'.$pd['slug']."/".$pd["pv_id"]?>" class="btn col-4 col-sm-3 cart-buttons ">
                <img src="<?php echo product_thumb_path($pd['thumbnail'])?>" alt='<?php echo ($discounted)?$pd['product_name'].' $'.number_format($discounted, 2):$pd['product_name'].' $'.number_format($pd['price'], 2);?>' class="img img-fluid mx-auto my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
                <?php echo $ribbon; ?>
                </a>
            <?php }elseif($pd['is_local'] == '0'){ ?>
                <a href="<?php echo base_url().'product/'.$pd['slug']."/".$pd["pv_id"]?>" class="btn col-4 col-sm-3 cart-buttons ">
                <img src="<?php echo $pd['thumbnail']?>" alt='<?php echo ($discounted)?$pd['product_name'].' $'.number_format($discounted, 2):$pd['product_name'].' $'.number_format($pd['price'], 2);?>' class="img img-fluid mx-auto my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
                <?php echo $ribbon; ?>
                </a>
            <?php }else{?>
                    <a href="<?php echo base_url().'product/'.$pd['slug']."/".$pd["pv_id"]?>" class="btn col-4 col-sm-3 cart-buttons ">
                    <img src="<?php echo product_thumb_path($pd['thumbnail']) ?>" alt='<?php echo ($discounted)?$pd['product_name'].' $'.number_format($discounted, 2):$pd['product_name'].' $'.number_format($pd['price'], 2);?>' class="img img-fluid mx-auto my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
                    <?php echo $ribbon; ?>
                    </a>
            <?php }?>
                    
                <div class="product-container col-8 col-sm-9 pt-4 pb-4 pr-3">
                <div class="row">
                    <div class="col-sm-12">
                        <a href="<?php echo base_url().'product/'.$pd['slug']."/".$pd["pv_id"]?>" class="fullname" title="<?php echo ($pd['product_name']);?>">
                            <h4 class="zabee-product-name">
                                <?php echo $pd['product_name']; ?>
                            </h4>
                        </a>
                    </div>
                    <?php if(!$detect->isMobile()){?>
                        <div class="col-12 pt-2 <?php echo ($pd['description'] != "")?"":"d-none"; ?>">
                            <span><?php echo $pd['description']; ?></span>
                        </div>
                    <?php }?>
                    <div class="col-sm-12 pt-2">
                        <div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($pd['rating'])?$pd['rating']:0;?>"></div>
                    </div>
                    <div class="col-12">
                        <div class="row inner">
                            <div class="pl-3 pt-1 pr-0 top-rated-product-price">                                
                                $<?php echo ($discounted)?$this->cart->format_number($discounted):$this->cart->format_number($pd['price']);?>
                                <?php if($discounted && $pd['discount_type'] != ""){?>
                                &nbsp;&nbsp;&nbsp;<strike class="text-danger">$<?php echo $this->cart->format_number($pd['price'])?></strike>
                                <?php } ?>
                            </div>
                            <?php if(!$this->isloggedin){ ?>
                            <div class="pl-3">
                                <button type="button" class="btn cart-buttons addToWishlistBtn col-2 pl-0 pr-3" data-product_variant_id="<?php echo $pd['pv_id'];?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-product_id="<?php echo $pd['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
                            </div>
                            <div>
                                <a href="javascript:void(0)" data-product_variant_id="<?php echo $pd['pv_id']?>" class="btn cart-buttons pl-0 pr-0 addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
                            </div>
                            <?php }else if($this->isloggedin && $pd['seller_id'] != $user_id){?>

                            <?php /*?><a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($p['product_name'],$p['product_id'])?>" class="btn cart-buttons " data-toggle="tooltip"   title="Product Details"><i class="far fa-eye"></i></a> <?php */?>
                            <?php if($this->isloggedin && isset($pd['already_saved']) && $pd['already_saved'] == ""){?>
                                <div class="pl-3">
                                    <button type="button" class="btn cart-buttons addToWishlistBtn col-2 pl-0 pr-3" data-product_variant_id="<?php echo $pd['pv_id'];?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-product_id="<?php echo $pd['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
                                </div>
                            <?php } else if($this->isloggedin && isset($pd['already_saved']) && $pd['already_saved'] != "0") { ?>
                            <div class="pl-3">
                                <span class="already-saved btn col-2 pl-1" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
                            </div>
                            <?php }else{ ?>
                                <div class="pl-3">
                                    <button type="button" class="btn cart-buttons addToWishlistBtn col-2 pl-0 pr-3" data-product_variant_id="<?php echo $pd['pv_id'];?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-product_id="<?php echo $pd['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
                                </div>
                            <?php } ?>
                            <div>
                                <a href="javascript:void(0)" data-product_variant_id="<?php echo $pd['pv_id']?>" class="btn cart-buttons pl-0 pr-0 addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <?php $index++; }  } ?>   
</div> 