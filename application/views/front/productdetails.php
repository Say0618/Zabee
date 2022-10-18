<div id="social-side">
<?php $social_slug = base_url("product/").$viewProductData['product']->slug."/".$viewProductData['product_variant_id']; ?>
<ul>
	<li>
		<div data-network="facebook" data-url="<?php echo $social_slug;?>" class="btn fb-side st-custom-button"><i class="fab fa-facebook-f"></i></div> 
	</li>
	<li>
		<div data-network="twitter" data-url="<?php echo $social_slug;?>" class="btn tw-side st-custom-button"><i class="fab fa-twitter"></i></div>
	</li>
	<li>
		<div data-network="linkedin" data-url="<?php echo $social_slug;?>" class="btn in-side st-custom-button"><i class="fab fa-linkedin-in"></i></div> 
	</li>
	<li>
		<div data-network="whatsapp" data-url="<?php echo $social_slug;?>" class="btn wa-side st-custom-button"><i class="fab fa-whatsapp"></i></div>
	</li>
	<li>
		<div data-network="email" data-url="<?php echo $social_slug;?>" class="btn mail-side st-custom-button"><i class="fa fa-envelope"></i></div> 
	</li>
	<li>
		<button class="btn btn-info" id="share"><i class="fa fa-share-alt" aria-hidden="true"></i></button>
	</li>
</ul>
</div>

<script>

$(document).ready(function(){
	$("#social-side #share").on("click", function(){
		if($("#social-side #share").hasClass("active")){
			$("#social-side #share").removeClass("active")
			$('#social-side ul li:lt(5)').hide();
		}else{
			$("#social-side #share").addClass("active")
			$("li").show();
		}
	})
});

</script>

<?php 
	$productLink=urlClean($viewProductData['product']->product_name);
	$today = date("Y-m-d");
	$today_time = strtotime($today);
	$expireTo_product = $viewProductData['product']->discount_end; 
	$expireFrom_product =  $viewProductData['product']->discount_start; 
	$expire_time_to_product = strtotime($expireTo_product);
	$expire_time_from_product = strtotime($expireFrom_product);
    $product_name = ($viewProductData['product']->product_name);
 	if($viewProductData['product']->discount_value != "" && $expire_time_to_product >= $today_time && $expire_time_from_product <= $today_time){ 
		$discounted = discount_forumula($viewProductData['product']->price, $viewProductData['product']->discount_value, $viewProductData['product']->discount_type, $viewProductData['product']->discount_start, $viewProductData['product']->discount_end); 
		if($viewProductData['product']->discount_type != "" && $viewProductData['product']->discount_type == "percent" ){
			if($discounted < 0){$discounted = $viewProductData['product']->price; }
			$extra = "% OFF";
			$extra_sign = "";
		}else if($viewProductData['product']->discount_type != "" && $viewProductData['product']->discount_type == "fixed" ){
			if($discounted < 0){$discounted = $viewProductData['product']->price;}
			$extra = "/- OFF";
			$extra_sign = "$";
		}
		else{
			$discounted = $viewProductData['product']->price;
		} 
	}
	else{
		$extra = null;
		$discounted = $viewProductData['product']->price;
	}
?>
<div class="container container_<?php echo $page_name; ?>">
	<form id="buyProduct" name="buyProduct" method="post" action="<?php echo base_url('cart/addtocart/'.$viewProductData['product_variant_id']); ?>">
		<?php $this->load->view("front/bradcrumb",array("product_name"=>$product_name));?>
		<div class="row product-info-row">
			<div class="col-sm-12">
				<div class="row">
					<div class="col-sm-3 pt-8px" >
						<ul id="lightSlider">
						<?php $json_ld_image ="";?>
						<?php foreach($viewProductData['productImage'] as $productImage){
							if($productImage->is_image == "1" && $productImage->is_local == "1"){
								$image_thumb_path = product_thumb_path($productImage->is_primary_image);
								$image_path = product_path($productImage->image_link);
							?>
								<li href="<?php echo $image_path?>" data-thumb="<?php echo $image_thumb_path?>" data-toggle="lightbox" data-gallery="gallery">
									<img class="img img-fluid" src="<?php echo $image_path?>" alt="<?php echo ($discounted)?$product_name." $".number_format($discounted, 2):$product_name." $".number_format($viewProductData['product']->price, 2);?>"/>
								</li>
							<?php	
							}else if($productImage->is_image == "1" && $productImage->is_local == "0"){
								$image_thumb_path = $productImage->image_link;
								$image_path = $productImage->image_link;
							?>
								<li href="<?php echo $image_path?>" data-thumb="<?php echo $image_thumb_path?>" data-toggle="lightbox" data-gallery="gallery">
									<img class="img img-fluid" src="<?php echo $image_path?>" alt="<?php echo ($discounted)?$product_name." $".number_format($discounted, 2):$product_name." $".number_format($viewProductData['product']->price, 2);?>"/>
								</li>
							<?php
							}else{
								$image_thumb_path = assets_url("front/images/play_icon.png");
								$image_path = $productImage->image_link;
							?>
								<li data-thumb="<?php echo $image_thumb_path?>">
									<?php echo $image_path?>
								</li>
							<?php
							}
							$json_ld_image .= '"'.$image_path.'",';
						}?>
						</ul>
					</div>
					<div class="col-sm-6 pt-0px position-relative">
						<h4 class="color-black m-0" ><b><?php echo $product_name;?></b></h4>
						<hr class="mt-1 mb-1 mrm-15"/>
						<?php if($viewProductData['product']->short_description){?>
							<h6 class="short-description"><?php echo $viewProductData['product']->short_description;?></h6>
							<!-- <div class="col-sm-2 col-md-11">
								
							</div> -->
						<?php }?>
						<div class="row" id="right-btn">
							<?php if($avgRating['result']->total_review > 0){?>
								<div class="mr-2 mt-1">
									<div class=""><div  class='rateYo' id="review_rating" data-rateyo-rating='<?php echo ($avgRating['result']->avg_rating)?$avgRating['result']->avg_rating:0 ;?>'></div></div>
									<div class="ml-2 total-reviews"><span class="q-a-fontsize"  id="total_reviews"><?php echo $avgRating['result']->total_review ?> <?php echo $this->lang->line('reviews');?></span></div>
								</div>
							<?php }?>
							<?php if($this->isloggedin && isset($_SESSION['affiliated_id']) && $_SESSION['affiliated_id'] != ""){?>
								<a class="cart-buttons" id="share_this" data-toggle="tooltip" data-placement="bottom" title="Generate Referral Link" data-product_variant_id="<?php echo $viewProductData['product_variant_id'];?>">
									<div class="mr-2 d-inline-block" >
										<span class="product-favs p-2 pr-3 pl-3">
											<i class="fa fa-users"></i>
										</span>
									</div>
								</a>
							<?php } if($this->isloggedin && $viewProductData['product']->seller_id != $this->session->userdata('userid')){ ?>
							<div id="product-icon-row" class="mr-2" >
								<span class="product-favs p-2 pr-3 pl-3">
									<?php if($this->isloggedin && isset($viewProductData['product']->already_saved) && $viewProductData['product']->already_saved == "0"){?>
										<a class="cart-buttons addToWishlistBtn" id="addToWishlistBtn" data-toggle="tooltip" title="Add to wishlist" data-product_id="<?php echo $viewProductData['product']->product_id?>" data-product_variant_id="<?php echo $viewProductData['product_variant_id'];?>" data-id = "<?php echo $viewProductData['product']->product_id."-".$viewProductData['product_variant_id']?>" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i> <span class="fs-14" ><?php echo $wishlist_fav[0]->totalfav ?></span></a>
									<?php } else if($this->isloggedin && isset($viewProductData['product']->already_saved) && $viewProductData['product']->already_saved == "1") { ?>
										<span class="already-saved" data-toggle="tooltip" title="Already wishlisted"><i class="fa fa-heart"></i></span> <span class="fs-14" ><?php echo $wishlist_fav[0]->totalfav ?></span>
									<?php }else{ ?>
										<a class="cart-buttons addToWishlistBtn" id="addToWishlistBtn" data-toggle="tooltip" title="Add to wishlist" data-product_id="<?php echo $viewProductData['product']->product_id?>" data-product_variant_id="<?php echo $viewProductData['product_variant_id'];?>" data-id = "<?php echo $viewProductData['product']->product_id."-".$viewProductData['product_variant_id']?>" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i> <span class="fs-14" ><?php echo $wishlist_fav[0]->totalfav ?></span></a>
									<?php }?>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="row mb-2 d-inline-block">
							<div class="col-sm-12">            
								<span><?php if($viewProductData['productConditions']){ ?>Condition(s):<?php } ?></span>
							</div>
							<div class="col-sm-12 condition_click" >
							<?php foreach($viewProductData['productConditions'] as $condition){ $productLink.='-'.$condition->condition_name ?>
								<a class="tag_box condition<?php echo $condition->condition_id;?> <?php echo ($viewProductData['product']->condition_id == $condition->condition_id)?"selected" :""?>" href='javascript:void(0);'>
									<span data-class="<?php echo $condition->condition_name?>" data-id="<?php echo $condition->condition_id?>"><?php echo ($condition->condition_name);?></span>
								</a>
								<?php } ?>               
							</div>
						</div>
						<?php  if(!empty($viewProductData['productAllVariants'])){ 
								foreach($viewProductData['productAllVariants'] as $pavKey=>$paValue){ 
								$flagClass = str_replace(' ',"_",strtolower($pavKey));	
								?>                
						<div class="row mb-2">
							<div class="col-sm-12">         
								<span class="" ><?php echo ($pavKey)?>:</span>
							</div>
							<div class="col-sm-12 variant" >
								<?php	foreach($paValue as $pav){
									$productLink.='-'.$pav['variant_title'];?>
									<a class="tag_box_variant firstImage <?php echo $flagClass;?> variant<?php echo $pav['variant_id'];?> <?php echo (in_array($pav['variant_id'],$viewProductData['productVariant']))?"selected":"";?>" data-class="<?php echo $flagClass;?>" data-id="<?php echo $pav['variant_id'];?>" href='javascript:void(0);'>
										<span data-class="<?php echo $pavKey?>"><?php echo $pav['variant_title'];?></span>
									</a>
								<?php } ?>   
							</div>
						</div>
						<?php }}?>
						<?php if($viewProductData['productFeatures']){?>
						<div class="row">
							<div class="col-sm-12 mb-0">
								<h4 class="color-black m-0"><b>Features</b></h4>
								<hr class="mt-1 mb-1 mrm-15">
							</div>
							<div class="col-sm-12">
								<ul id="features">
									<?php foreach($viewProductData['productFeatures'] as $pf){?>
										<li><?php echo $pf->feature;?></li>
									<?php }?>
								</ul>
							</div>
						</div>
						<?php }?>    
					</div>
					<div class="col-sm-3 rightmost-div border-left-for-extreme-right-div" id="rightSideBarHr">
						<div class="row">
							<div class="col-sm-12 mt-3">
								<div class="d-flex flex-row">
								<div class="pt-2 pb-2 pl-2 pr-0" > 
									<span class="price-fone-size" ><?php echo $this->lang->line('price');?>:</span>   
								</div>
								<div class="p-1"><span class="price-child-span pl-1 pr-1" >
									<span class="price-second-child-span"><b id="d_price" class="price"><?php echo($discounted)?'$'.$this->cart->format_number($discounted):$viewProductData["product"]->price;?></b></span>&nbsp;
										<?php $class = ($viewProductData['product']->discount_type != "" && $extra != null)?"":"d-none"; ?>  
										<span class="price-third-child-span discountSpan <?php echo $class?>">
											<?php echo ($discounted)?'<strike><b id="o_price" class="price">$'.$this->cart->format_number($viewProductData["product"]->price).'</b></strike>':""; ?>
										</span>&nbsp;
										<?php if($discounted){ ?>
										<span class="price-fourth-child-span discountSpan <?php echo $class?>" >
											<strong class="price-fourth-child-span-strongTag" id="discount_valueLabel" ><?php echo $extra_sign.$viewProductData['product']->discount_value?></strong>
											<strong class="price-fourth-child-span-strongTag" id="extra"><?php echo $extra?></strong>
										</span>
										<?php } ?>
									</span>
								</div>
							</div>
						</div>
						<div class="col-sm-12" id="spd">
							<?php if(isset($viewProductData['product']->seller_product_description) && $viewProductData['product']->seller_product_description != ""){?>
								<label class="p-2 m-0">Note:&nbsp;
									<span class="bold-title"><?php echo $viewProductData['product']->seller_product_description?></span>
							</label>
							<?php }?>
						</div>
						<div class="col-sm-12 soldby">
							<label class="p-2 m-0"><?php echo $this->lang->line('sold_by');?>: 
								<span>
									<a href="<?php echo base_url('store/'.$viewProductData['product']->store_id);?>" class="bold-title" id="store_name"><?php echo(stripslashes($viewProductData['product']->store_name));?></a>
								</span>
							</label>
						</div>
						<div class="col-sm-12" id="warrantyby">
						<?php if($viewProductData['product']->warranty){?>
							<label class="p-2 m-0"><?php echo $this->lang->line('warranty');?>: 
								<span>
									<a href="javascript:void(0)" class="bold-title" id="warranty"><?php echo $viewProductData['product']->warranty;?></a>
								</span>
							</label>
						<?php }?>
						</div>
						<div class="col-sm-12">
							<!-- <span class="p-2"><label id="shipping_method" class="col-form-label"></label></span> -->
							<label id="shipping_method" class="col-form-label p-2 m-0"></label>
						</div>
						<?php if($viewProductData['product']->total_seller > 0){?>
							<div class="col-sm-12 morethanoneseller">
								<label class="p-2 m-0">
									<a class="bold-title" id="offerlisting" href="<?php echo base_url('product/offerlisting/'.$viewProductData['product']->product_id);?>"><?php echo $this->lang->line('sellers_for_product');?></a>
								</label>
							</div>
						<?php } ?>
							<hr class="hr-class" />
							<div class="col-sm-12">
								<div class="d-flex" >
									<span class="pl-2 pt-1 pr-1 qty-lh"><?php echo $this->lang->line('qty');?>: </span>
									<a href="javascript:void(0)" class="btn qty-min ml-2" >
										<span>-</span>
									</a>
									<span id="for-input-width" class="input-group this-input-width mx-2" >
										<input type="number" name="product_qty" id="product_qty" class=" disabled form-control rounded-0" value="1" min="1" max="<?php echo $viewProductData['product']->quantity;?>"  />
										<span class="error small" id="qty-error"></span>
										<input type="hidden" name="pvid" id="product_variant_id" value="<?php echo $viewProductData['product_variant_id'];?>" />
									</span> 
									<a href="javascript:void(0)" class="btn qty-max" >
										<span>+</span>
									</a>
								</div>
								<span id="pops"  ><?php echo $this->lang->line('max_qty_app');?></span>	<br />
							</div>
							<div class="col-sm-12 pt-3 pb-3">
								<div class="row button-center" id="addToCartDiv">
									<?php if(isset($_SESSION['userid']) && $viewProductData['product']->seller_id == $_SESSION['userid']){ ?>
										<a href="javascript:void(0)" class="btn btn-hover color-orange btn-xl  disabled" id="addToCartBtn" data-toggle="tooltip"><i class="fa fa-shopping-cart"></i>  <?php echo $this->lang->line('add_tocart');?></a>
									<?php }else{?>
										<a href="javascript:void(0)" class="btn btn-hover color-orange btn-xl" id="addToCartBtn" data-toggle="tooltip" data-product_variant_id="<?php echo $viewProductData['product_variant_id'];?>" data-available_shipping_ids="<?php echo $viewProductData['product']->shipping_ids;?>"><i class="fa fa-shopping-cart"></i>  <?php echo $this->lang->line('add_tocart');?></a>
									<?php }?>            
								</div>
							</div>
							<div class="col-sm-12 pb-3">
								<div class="row button-center" id="buyNowDiv">
									<?php if(isset($_SESSION['userid']) && $viewProductData['product']->seller_id == $_SESSION['userid']){ ?>
										<a href="javascript:void(0)" class="btn btn-hover color-blue btn-xl disabled" id="buyNowBtn" data-toggle="tooltip"><?php echo $this->lang->line('buy_now');?></a>
									<?php }else{ $link = base_url()."buynow/".$viewProductData['product_variant_id'];?>
										<a type="button" href="<?php echo $link; ?>" id="buyNowBtn" class="btn btn-hover color-blue btn-xl" ><?php echo $this->lang->line('buy_now');?></a>
									<?php }?>            
								</div>
							</div>
							<?php if($viewProductData['otherSeller']['rows'] > 0){ ?>
								<hr class="hr-class"/>
							<div class="col-sm-12 other-product">
								<div class="row">
									<div class="col-sm-12 other-product-heading">
										<h5><?php echo $this->lang->line('other_products');?></h5>
									</div>
									<?php foreach($viewProductData['otherSeller']['result'] as $otherSeller){ ?>
									<?php 
										$product_link = urlClean($otherSeller['product_name']);
										$product_link=str_replace("(","", $product_link);
										$product_link=str_replace(")","", $product_link);
									?>
									<div class="col-sm-12 other-product-data mb-3">
										<div class="row">
											<a href="<?php echo base_url().'product/'.$otherSeller['slug']."/".$otherSeller['pv_id']?>">
												<div class="col-12" id="other-seller">
													<span class="price-second-child-span line-height30"><b><?php echo "$".number_format($otherSeller['price'], 2) ?></b></span>
													<?php if(isset($_SESSION['userid']) && $otherSeller['seller_id'] == $_SESSION['userid']){ ?>
														<a href="javascript:void(0)" class="btn btn-hover color-orange btn-sm float-right disabled" data-toggle="tooltip"><i class="fa fa-shopping-cart"></i>  <?php echo $this->lang->line('add_tocart');?></a>
													<?php }else{?>
														<a href="javascript:void(0)" class="btn btn-hover color-orange btn-sm float-right addToCartBtn" data-redirect="1"  data-toggle="tooltip" data-product_variant_id="<?php echo $otherSeller['pv_id'];?>"><i class="fa fa-shopping-cart"></i>  <?php echo $this->lang->line('add_tocart');?></a>
													<?php }?>     
													
												</div>
												<div class="col-12">
													Condition: <strong><?php echo $otherSeller['condition_name'] ?></strong>
												</div>
												<div class="col-12">
												Sold by: <strong><?php echo $otherSeller['store_name'] ?></strong>
												</div>
												
											</a>
										</div>
									</div>
									<?php } ?>
								</div>
							</div> 
							<?php } ?>
						<?php if(isset($product_tags)){ ?>
							<hr class="hr-class" />
							<div class="col-sm-12 pt-3">
								<div class="col-sm-12">
									<h4><?php echo $this->lang->line('product_tags');?></h4>
								</div>
							</div>
							<div class="col-sm-12 pt-3">
								<div class="container-fluid">
									<div class="row">
										<?php foreach($product_tags as $pt){?>
											<div class="col-sm-2 q-a-fontsize pb-3">
												<a href="" class="tags p-2 tags-custom" ><?php echo $pt; ?></a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
					<hr class="hr-class mt-0">
				</div>
					<div class="col-sm-12 first-column-bottom mCustomScrollbar" data-mcs-theme="dark">
						<div class="row">
							<div class="col-sm-12">       
								<p><?php echo $viewProductData['product']->product_description; ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>	
		<?php if($viewProductData['productAccessories']['rows'] > 0){
			$productAccessoriesCount = $viewProductData['productAccessories']['rows'];?>
		<div class="row">
			<div class="col-sm-12 pt-2">
				<hr />
				<div class="product-accessories carousel slide" data-ride="carousel" data-interval="false">
					<div class="mt-2 position-relative">
						<?php if($productAccessoriesCount > 4){?>
							<div id="product-accessories-container" class="col-12">
								<span class="prev-span <?php echo ($productAccessoriesCount < 3)?"d-none":""?>">
								<a class="btn btn-hover color-8 rounded-circle product-accessories-prev" href="" data-toggle="tooltip" ><i class="fa fa-chevron-left"></i></a>
								</span>
								<span class="next-span <?php echo ($productAccessoriesCount < 3)?"d-none":""?>">
								<a class="btn btn-hover color-8 rounded-circle product-accessories-next" href="" data-toggle="tooltip" ><i class="fa fa-chevron-right"></i></a>
								</span>
							</div>
						<?php }?>
						<h3 ><b><?php echo $this->lang->line('accessories');?></b></h3>
						<div class="carousel-inner">
							<?php 
							$i = 0; $maskGroup=1;
							$close = 0;
							foreach($viewProductData['productAccessories']['result'] as $productAccessories){
								if($productAccessories['is_local'] == 1){
									$product_image = product_thumb_path($productAccessories['product_image']);
								}else{
									$product_image = $productAccessories['product_image'];
								}
								if(!$detect->isMobile()){
								if($i == 0){
								?>
								<div class="carousel-item active">
									<div class="card-deck">
										<?php }
										if($i > 0 && $i%4 == 0 && $productAccessoriesCount > 4){
											$close = $i+3;
										?>
										<div class="carousel-item">
											<div class="card-deck">
										<?php }}else{ echo ($i == 0)?'<div class="carousel-item active"><div class="card-deck">':'<div class="carousel-item"><div class="card-deck">';}?>
											<div class="card col-lg-3 col-12 text-center border-0">
													<div class="card-img-top seller-center-parent">
													<?php
														$ribbon = "" ;
														$expireTo_feature = $productAccessories['valid_to']; 
														$expireFrom_feature =  $productAccessories['valid_from']; 
														$expire_time_to_feature = strtotime($expireTo_feature);
														$expire_time_from_feature = strtotime($expireFrom_feature);
														$prd_acc_discounted = discount_forumula($productAccessories['price'], $productAccessories['discount_value'], $productAccessories['discount_type'], $productAccessories['valid_from'], $productAccessories['valid_to']);
														if($productAccessories['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
															if($productAccessories['discount_type'] == "percent"){
																$ribbon = "<div class='ribbon ribbon-top-left'><span>".$productAccessories['discount_value']."% OFF</span></div>";
															}elseif($productAccessories['discount_type'] == "fixed"){
																$ribbon = ($prd_acc_discounted)?"<div class='ribbon ribbon-top-left'><span>$".$productAccessories['discount_value']."/- OFF</span></div>":"";
															}
														}
													?>
													<a href="<?php echo base_url().'product/'.$productAccessories['slug']?>"> <img src="<?php echo $product_image?>" alt="<?php echo ($prd_acc_discounted)?$productAccessories['product_name']." $".$this->cart->format_number($prd_acc_discounted):$productAccessories['product_name']." $".$this->cart->format_number($productAccessories['price'])?>" class="img img-fluid mx-auto d-flex my-auto image-center" data-toggle="tooltip"  title="<?php echo $productAccessories['product_name']; ?>" />
													<?php echo $ribbon; ?>
													</a> 
													</div>
													<div class="card-body p-0">
														<a href="<?php echo base_url().'product/'.$productAccessories['slug']?>">
														<h2 class="other-seller-product-name"> <?php echo (strlen($productAccessories['product_name']) > 20)?substr($productAccessories['product_name'],0,20)."...":$productAccessories['product_name']; ?></h2></a>
														<div class="col-sm-12">
															<div  class='rateYo' data-rateyo-rating="<?php echo ($productAccessories['rating'])?$productAccessories['rating']:0;?>"></div>
														</div>
														<div class="col-sm-12 top-rated-product-price">
															<?php echo ($prd_acc_discounted)?"$".$this->cart->format_number($prd_acc_discounted):$this->cart->format_number($productAccessories['price']);?>
															<?php if($productAccessories['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){?>
																<?php if($prd_acc_discounted){ ?><strike>$<?php echo $this->cart->format_number($productAccessories['price'])?></strike> <?php } ?>
															<?php } ?>
														</div>
														<div class="col-sm-12">
															<?php if(!$this->isloggedin){ ?>
																<a href="javascript:void(0)" class="btn cart-buttons addToCartBtn" data-product_variant_id="<?php echo $productAccessories['pv_id']; ?>" data-toggle="tooltip" title="Add to cart"><i class="fa fa-shopping-cart"></i></a> 												
															<?php }else if($this->isloggedin && $viewProductData['product']->seller_id != $user_id){?>
																<a href="javascript:void(0)" class="btn cart-buttons addToCartBtn" data-toggle="tooltip" title="Add to cart"><i class="fa fa-shopping-cart"></i></a> 								
															<?php }?>
															<?php if($this->isloggedin && isset($productAccessories['already_saved']) && $productAccessories['already_saved'] == ""){?>
																<button type="button" class="btn cart-buttons addToWishlistBtn " data-product_variant_id="<?php echo $productAccessories['pv_id'];?>" data-id = "<?php echo $productAccessories['product_id']."-".$productAccessories['pv_id']?>" data-product_id="<?php echo $productAccessories['product_id']?>" data-toggle="tooltip"  data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></button>
															<?php } else if($this->isloggedin && isset($productAccessories['already_saved']) && $productAccessories['already_saved'] != "0") { ?>
																<span class="already-saved btn" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
															<?php }else{ ?>
																<button type="button" class="btn cart-buttons addToWishlistBtn " data-product_variant_id="<?php echo $productAccessories['pv_id'];?>" data-product_id="<?php echo $productAccessories['product_id']?>" data-id = "<?php echo $productAccessories['product_id']."-".$productAccessories['pv_id']?>" data-toggle="tooltip"  data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></button>
															<?php }?>
														</div>
													</div>
												</div>
										<?php if(!$detect->isMobile()){ if($i ==3){?></div></div><?php } if($close > 0 && $i%$close == 0 && $productAccessoriesCount > 4){ ?>
										</div>
										</div>
									<?php } if($productAccessoriesCount == 1){echo "</div></div>";}}else{ echo "</div></div>";}$i++; $maskGroup++;}?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }?>
	<?php if($viewProductData['relatedProduct']['rows'] > 0){
			$relatedProductCount = $viewProductData['relatedProduct']['rows'];?>
		<div class="row">
			<div class="col-sm-12 pt-2">
				<hr class="row" />
				<div class="more-from-this-sellers carousel slide" data-ride="carousel" data-interval="false">
					<div class="mt-2 position-relative">
					<?php if($relatedProductCount > 4){?>
						<div id="product-accessories-container" class="col-12">
							<span class="prev-span <?php echo ($relatedProductCount < 3)?"d-none":""?>">
							<a class="btn btn-hover color-8 rounded-circle more-seller-prev" href="" data-toggle="tooltip" ><i class="fa fa-chevron-left"></i></a>
							</span>
							<span class="next-span <?php echo ($relatedProductCount < 3)?"d-none":""?>">
							<a class="btn btn-hover color-8 rounded-circle more-seller-next" href="" data-toggle="tooltip" ><i class="fa fa-chevron-right"></i></a>
							</span>
						</div>
					<?php }?>
						<h3 ><b><?php echo $this->lang->line('related_searches');?></b></h3>
						<div class="carousel-inner">
							<?php 
							$i = 0; $maskGroup=1;
							$close = 0;
							foreach($viewProductData['relatedProduct']['result'] as $relatedProduct){
								if($relatedProduct['is_local'] == 1){
									$product_image = product_thumb_path($relatedProduct['product_image']);
								}else{
									$product_image = $relatedProduct['product_image'];
								}
								if(!$detect->isMobile()){
								if($i == 0){
								?>
								<div class="carousel-item active">
									<div class="card-deck">
										<?php }
										if($i > 0 && $i%4 == 0 && $relatedProductCount > 4){
											$close = $i+3;
										?>
										<div class="carousel-item">
											<div class="card-deck">
										<?php
										}
								}else{
								echo ($i == 0)?'<div class="carousel-item active"><div class="card-deck">':'<div class="carousel-item"><div class="card-deck">';}?>
											<div class="card col-lg-3 col-12 text-center border-0">
												<div class="card-img-top seller-center-parent">
												<?php
													$ribbon = "";
													$expireTo_feature = $relatedProduct['valid_to']; 
													$expireFrom_feature =  $relatedProduct['valid_from']; 
													$expire_time_to_feature = strtotime($expireTo_feature);
													$expire_time_from_feature = strtotime($expireFrom_feature);
													$discounted = discount_forumula($relatedProduct['price'], $relatedProduct['discount_value'], $relatedProduct['discount_type'], $relatedProduct['valid_from'], $relatedProduct['valid_to']);
													if($relatedProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
														if($relatedProduct['discount_type'] == "percent"){
															$ribbon = "<div class='ribbon ribbon-top-left'><span>".$relatedProduct['discount_value']."% OFF</span></div>";
														}elseif($relatedProduct['discount_type'] == "fixed"){
															$ribbon = ($discounted)?"<div class='ribbon ribbon-top-left'><span>$".$relatedProduct['discount_value']."/- OFF</span></div>":"";
														}
													}
												?>
												<a href="<?php echo base_url().'product/'.$relatedProduct['slug']?>"> <img src="<?php echo $product_image?>" alt="<?php echo ($discounted)?$relatedProduct['product_name']." $".$this->cart->format_number($discounted):$relatedProduct['product_name']." $".$this->cart->format_number($relatedProduct['price'])?>" class="img img-fluid mx-auto d-flex my-auto image-center" data-toggle="tooltip"  title="<?php echo $relatedProduct['product_name']; ?>" />
													<?php echo $ribbon; ?>
												</a> 
												</div>
												<div class="card-body p-0">
													<a href="<?php echo base_url().'product/',$relatedProduct['slug']?>">
													<h2 class="other-seller-product-name"> <?php echo (strlen($relatedProduct['product_name']) > 20)?substr($relatedProduct['product_name'],0,20)."...":$relatedProduct['product_name']; ?></h2></a>
													<div class="col-sm-12">
														<div  class='rateYo' data-rateyo-rating="<?php echo ($relatedProduct['rating'])?$relatedProduct['rating']:0;?>"></div>
													</div>
													<div class="col-sm-12 top-rated-product-price">
														<?php echo ($discounted)?"$".$this->cart->format_number($discounted):$this->cart->format_number($relatedProduct['price']);?>
													<?php if($relatedProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){?>
														<?php if($discounted){ ?><strike>$<?php echo $this->cart->format_number($relatedProduct['price'])?></strike><?php } ?>
													<?php } ?>
													</div>
													<div class="col-sm-12">
													<?php if(!$this->isloggedin){ ?>
														<a href="javascript:void(0)" class="btn cart-buttons addToCartBtn" data-product_variant_id="<?php echo $relatedProduct['pv_id']; ?>" data-toggle="tooltip" title="Add to cart"><i class="fa fa-shopping-cart"></i></a> 												
														<button type="button" class="btn cart-buttons addToWishlistBtn " data-product_variant_id="<?php echo $relatedProduct['pv_id'];?>" data-product_id="<?php echo $relatedProduct['product_id']?>" data-id = "<?php echo $relatedProduct['product_id']."-".$relatedProduct['pv_id']?>" data-toggle="tooltip" title="Save For Later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></button>
													<?php }else if($this->isloggedin && $viewProductData['product']->seller_id != $user_id){?>
														<a href="javascript:void(0)" class="btn cart-buttons addToCartBtn" data-product_variant_id="<?php echo $relatedProduct['pv_id']; ?>" data-toggle="tooltip" title="Add to cart"><i class="fa fa-shopping-cart"></i></a> 								
													
														<?php if($this->isloggedin && isset($relatedProduct['already_saved']) && $relatedProduct['already_saved'] == ""){?>
															<button type="button" class="btn cart-buttons addToWishlistBtn " data-product_variant_id="<?php echo $relatedProduct['pv_id'];?>" data-product_id="<?php echo $relatedProduct['product_id']?>" data-id = "<?php echo $relatedProduct['product_id']."-".$relatedProduct['pv_id']?>" data-toggle="tooltip" title="Save For Later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></button>
														<?php } else if($this->isloggedin && isset($relatedProduct['already_saved']) && $relatedProduct['already_saved'] != "0") { ?>
															<span class="already-saved btn" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
														<?php }else{ ?>
															<button type="button" class="btn cart-buttons addToWishlistBtn " data-product_variant_id="<?php echo $relatedProduct['pv_id'];?>" data-product_id="<?php echo $relatedProduct['product_id']?>" data-id = "<?php echo $relatedProduct['product_id']."-".$relatedProduct['pv_id']?>" data-toggle="tooltip" title="Save For Later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></button>
														<?php } }?>
													</div>
												</div>
											</div>
										<?php if(!$detect->isMobile()){ if($i ==3){?></div></div><?php } if($close > 0 && $i%$close == 0 && $relatedProductCount > 4){ ?>4
										</div>
										</div>
									<?php } if($relatedProductCount == 1){echo "</div></div>";}}else{ echo "</div></div>";}$i++; $maskGroup++;}?>
							
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="">
			<div class="container pt-2">
				<hr />
				<div class="">
					<h5><?php echo $this->lang->line('customer_reviews');?></h5>
				</div>
					<div class="row">
						<div class="p-2 col-sm-3" > 
							<div class="col-sm-12">
								<span class="before-out-of pl-3 "><?php echo sprintf('%0.1f', $avgRating['result']->avg_rating); ?></span> <span><?php echo $this->lang->line('out_of_5');?></span><br>
								<div class="pl-2"><div  class='rateYo2' id="review_rating2" data-rateyo-rating='<?php echo ($avgRating['result']->avg_rating)?$avgRating['result']->avg_rating:0 ;?>'></div></div>               
							</div>
						</div>
					<?php
						$zero = $one = $two = $three = $four = $five = 0;
						$onePercent = $twoPercent = $threePercent = $fourPercent = $fivePercent = 0;  
						if(isset($reviews['total_rating']['result'][0]['review_id']) && $reviews['total_rating']['result'][0]['review_id'] != null){
							if($reviews['total_rating']['result']){
								// echo"<pre>"; print_r($reviews['total_rating']); die();
								for($i = 0; $i < $reviews['total_rating']['total']; $i++){
									if($reviews['total_rating']['result'][$i]['rating'] < "2"){
										$one++;
									}else if($reviews['total_rating']['result'][$i]['rating'] < "3"){
										$two++;
									}else if($reviews['total_rating']['result'][$i]['rating'] < "4"){
										$three++;
									}else if($reviews['total_rating']['result'][$i]['rating'] < "5"){
										$four++;
									}else if($reviews['total_rating']['result'][$i]['rating'] == "5"){
										$five++;    
									}
								}
							}
						}
						if($one > 0){
							$onePercent = $one/$reviews['total_rating']['total'];
							$onePercent *=  100;
						}
						if($two > 0){
							$twoPercent = $two/$reviews['total_rating']['total'];
							$twoPercent *=  100;
						}
						if($three > 0){
							$threePercent = $three/$reviews['total_rating']['total'];
							$threePercent *=  100;
						}
						if($four > 0){
							$fourPercent = $four/$reviews['total_rating']['total'];
							$fourPercent *=  100;
						}
						if($five > 0){
							$fivePercent = $five/$reviews['total_rating']['total'];
							$fivePercent *=  100;
						}
					?>
					
					<div class="p-2 col-sm-3" >
						<div class="row reviewStats">
							<span class="star-size" ><?php echo $this->lang->line('5_star');?></span>&nbsp;
							 <div class="progress">
								<div class="progress-bar progress-bar-success" id="five-percent" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $fivePercent ?>%">
								</div>
							</div>&nbsp;&nbsp;
							<span class="star-size" id="five" ><?php echo $five; ?></span>&nbsp;
						</div>
						<div class="row reviewStats">
							<span class="star-size" ><?php echo $this->lang->line('4_star');?></span>&nbsp;
							<div class="progress">
								<div class="progress-bar progress-bar-success" id="four-percent" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $fourPercent ?>%">
								</div>
							</div>&nbsp;&nbsp;
							<span class="star-size" id="four"><?php echo $four; ?></span>&nbsp;
						</div>
						<div class="row reviewStats">
							<span class="star-size" ><?php echo $this->lang->line('3_star');?></span>&nbsp;   
							<div class="progress">
								<div class="progress-bar progress-bar-success" id="three-percent" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $threePercent ?>%">
								</div>
							</div>&nbsp;&nbsp;
							<span class="star-size" id="three" ><?php echo $three; ?></span>&nbsp;
						</div>
						<div class="row reviewStats">
							<span class="star-size" ><?php echo $this->lang->line('2_star');?></span>&nbsp;
							<div class="progress">
								<div class="progress-bar progress-bar-success" id="two-percent" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $twoPercent ?>%">
								</div>
							</div>&nbsp;&nbsp;
							<span class="star-size" id="two" ><?php echo $two; ?></span>&nbsp;
						</div>
						<div class="row reviewStats">
							<span class="star-size" ><?php echo $this->lang->line('1_star');?></span>&nbsp;
							<div class="progress">
								<div class="progress-bar progress-bar-success" id="one-percent" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $onePercent ?>%">
								</div>
							</div>&nbsp;&nbsp;
							<span class="star-size" id="one" ><?php echo $one; ?></span>&nbsp;
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="">
			<div class="container pt-2">
				<hr />
				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<?php $value = ($avgRating['result']->total_review > 0) ? $avgRating['result']->total_review : '0'; ?>
					<a class="nav-link navLink active" data-toggle="tab" href="#all"><?php echo $this->lang->line('all');?>(<span id="all-reviews"><?php echo $value; ?></span>)</a>
					</li>
					<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#images"><?php echo $this->lang->line('images');?>(0)</a>
					</li>
					<li class="nav-item">
					<a class="nav-link" data-toggle="tab" href="#videos"><?php echo $this->lang->line('videos');?>(0)</a>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content">
					<div id="all" class="tab-pane active"><br>
						<div id="customer-review" class="collapse show col-12 customerReview">
							<?php
							if(isset($reviews['result'][0]['review_id']) && $reviews['result'][0]['review_id'] == null) { 
								echo "<Strong><p>".$this->lang->line('no_reviews')."</p></Strong>";
							}else{ 
								echo '<div class="row">';
								$i = 1;
								$maxReview = count($reviews['result']);
								foreach($reviews['result'] as $rec){
									// $date = getUtcDate($rec['date']);
									$date = date_create($rec['date']);
									$date = date_format($date, "Y-m-d");
								?> 
								<?php  $breakreview = stripslashes($rec['review']);
								if($i <= 3){
									// echo substr(stripslashes($breakreview),0,100);
								?> 
							<div class="col-12 mb-4">
								<div class="row">
									<div class="col-sm-2 col-md-1 pb-2"><span class="user-review"><?php echo ($rec['fake'] == "1")?substr($rec['review_name'], 0, 3)."***":substr($rec['name'], 0, 3)."***"; ?></span></div>
									<div class="col-sm-3 col-md-3 pt-1"><div class='rateYo p-0' data-rateyo-rating='<?php echo $rec['rating'];?>'></div></div>
								</div>
								<div class="row">
									<div class="col-sm-2 col-md-1 mb-2">
									<?php if($rec['user_pic'] != ""){
										$img = explode('.', $rec['user_pic']);
										$img = $img[0]."_thumb.".$img[1];
										$img = profile_path("thumbs/".$img);
									}else{
										$img = assets_url("front/images/Preview.png");
									} ?>
										<?php  $imagePath = ($rec['fake'] != 1)?$img:assets_url("backend/images/defaultprofile.png"); ?>
										<img src="<?php echo $imagePath;?>" class="review-icon reviewImageHW img-fluid" alt="<?php echo image_url('Preview.png')?>">
									</div>
									<div class="col-sm-10 col-md-10 mb-2 user-review">
										<!-- <div class='rateYo p-0' data-rateyo-rating='<?php echo $rec['rating'];?>'></div>&nbsp;&nbsp;&nbsp; -->
										<span class="small"><?php echo formatDateTime($date, false); ?></span><br>
										<span class="reviews-data">
										<?php 
											$str = substr($breakreview,0,250);
											$strLength = 250;
											if(strlen($breakreview) > 250){
												$strLength = strripos($str, " ");
												$str = substr($str, 0, $strLength);  
											}
											$str2 = substr($breakreview, $strLength);
										?>
											<i><?php echo $str;?></i>
											<?php if(strlen($breakreview) > 250){ ?>
												<i class="more-content"><?php echo $str2 ?></i>	
												<a class="readmore text-color" href="#"><?php echo $this->lang->line('show_more');?> (+)</a>
											<?php } ?>
										</span>
										<br>
										<?php
										if(isset($rec['review_img']) && $rec['review_img'] != ""){
											if (strpos($rec['review_img'], ',' ) !== false ) {
											$review_images = explode(',', $rec['review_img']);
											foreach($review_images as $img){
												?>
												<img class="img-thumbnail" data-review="<?php echo $rec['review_id'] ?>" src="<?php echo image_url('review/thumbs/').$img ?>"  >
													
											<?php } } else{
												$review_images = $rec['review_img'];
											?>
											<img class="img-thumbnail" data-review="<?php echo $rec['review_id'] ?>" src="<?php echo image_url('review/thumbs/').$review_images ?>"  > 
											<?php }?>
											<div id="images-<?php echo $rec['review_id'] ?>" class="tab-pane mb-1 position-relative col-12 col-md-4 d-none">
												<button id="close-img-<?php echo $rec['review_id'] ?>" data-review="<?php echo $rec['review_id'] ?>" class="position-absolute close small cross-btn d-none">
													<span>x</span>
												</button>
												<img id="original_pic-<?php echo $rec['review_id'] ?>" class="p-4 img-fluid"/>
											</div>
											<?php } ?>
									</div>
								</div>
							</div>
							<?php } 
							$i++; } } echo "</div>"; ?>
						</div>
					</div>
					<div id="videos" class="container-fluid tab-pane fade"><br>
						<p></p>
					</div>
				</div>
			</div>
			<div class="col-sm-12 pt-1">
				<div class="offset-sm-6 col-sm-6 showMoreButton">
					<?php $slug = explode("/", $_SERVER['REQUEST_URI'])[2]; $encode_data = $viewProductData['product']->seller_id.'-zabeeBreaker-'.$viewProductData['product']->product_id.'-zabeeBreaker-'.$viewProductData['product']->sp_id.'-zabeeBreaker-'.$viewProductData['product']->pv_id.'-zabeeBreaker-'.$slug;
					$url_val=base64_encode(urlencode($encode_data)); ?>
					<a href="<?php echo base_url('product/showMoreReview/'.$url_val); ?>" id="review-show-more" class="show-more <?php echo ($reviews['total'] <= 3 ) ?'d-none' :'d-block' ?>" ><?php echo $this->lang->line('show_more');?> <i class="fas fa-angle-down angel-down"></i></a>
				</div>
			</div>
		</div>
		<input type="hidden" name="product_id" value="<?php echo encodeProductID($viewProductData['product']->product_name, $viewProductData['product']->product_id); ?>" id="p_id" data-id="<?php echo $viewProductData['product']->product_id?>" />
		<input type="hidden" name="seller_id" value="<?php echo $viewProductData['product']->seller_id;?>"   />
		<input type="hidden" name="qty" id="qty" value="<?php echo $viewProductData['product']->quantity;?>" />
		<input type="hidden" name="conditin_id" value="0" />
		<input type="hidden" id='ref' name='ref' value="<?php echo isset($_GET['ref'])?$_GET['ref']:""; ?>" />
	</form>
	<div class="">
	<div class="container pb-2">
		<hr />
		<div class="card">
		  <div class="card-body">
			  <div class="col-12">
			  <h5 id="mainQuestionHeading"><?php echo $this->lang->line('question');?>&nbsp;&nbsp;<?php echo (isset($questions['total']) && $questions['total'] > 0)?"(".$questions['total'].")":""; ?></h5>
			  <form id="qnaForm" action="<?php echo base_url('product/qna/')?>" METHOD="POST">
					<div class="input-group mb-3">
						<input type="text" class="form-control" name="question" placeholder="<?php echo $this->lang->line('enter_question');?>" aria-label="Recipient's username" aria-describedby="basic-addon2">
						<input type="hidden" id="asked_date" name ="asked_date" value="">
						<input type="hidden" id="question_product_id" name ="product_id" value="<?php echo $viewProductData['product']->product_id;?>">
						<input type="hidden" id="question_pv_id" name ="pv_id" value="<?php echo $viewProductData['product']->pv_id;?>">
						<input type="hidden" id="question_seller_id" name ="seller_id" value="<?php echo $viewProductData['product']->seller_id;?>">
						<input type="hidden" id="question_sp_id" name ="sp_id" value="<?php echo $viewProductData['product']->seller_product_id;?>">
						<input type="hidden" id="userid" name ="userid" value="<?php echo isset($_SESSION['userid'])?$_SESSION['userid']:"";?>">
						<div class="input-group-append">
							 <button class="btn btn-hover color-8" name="btn" value="ques" type="submit"><?php echo $this->lang->line('ask_question');?></button>
						</div>
					</div>
				</form>
				<section id="allQuestions">
				<?php if(isset($questions['total']) && $questions['total'] > 0 ) {
					$current_user = ($this->session->userdata('userid')) ? $this->session->userdata('userid') : "";
				?>
				  <h5 id="sellerStoreHeading"><?php echo $this->lang->line('other_ques');?> <?php echo $viewProductData['product']->store_name ?></h5>
					<?php } foreach($questions['result'] as $q){ ?>
						<div class="qna-item-group">
							<span class="fas fa-question"></span>
							<span class="qna-content"><?php echo (isset($q['question']))?$q['question']:"" ?></span>
							<div class="qna-meta text-secondary mb-2"><small><?php echo (isset($q['firstname']))?$q['firstname']:"" ?>&nbsp;&nbsp;&nbsp;<?php echo (isset($q['asked_date']))?formatDateTime($q['asked_date'], false):"" ?></small></div>
							<?php if(isset($q['answer']) && $q['answer'] != ""){ ?>
								<span class="fas fa-question"></span>
								<span class="qna-content"><?php echo $q['answer'] ?></span>
								<div class="qna-meta text-secondary"><small><?php echo $q['answer_person'] ?>&nbsp;&nbsp;&nbsp;<?php echo $q['answered_date']?></small></div>
							<?php } elseif((isset($q['seller_id'])) && $q['seller_id'] == $current_user){ ?>
								<button type="button" class="btn btn-secondary mt-3 ansBtn" data-toggle="modal" data-backdrop="static" data-id="<?php echo $q['id']?>" data-product_id="<?php echo $q['product_id']?>" data-pv_id="<?php echo $viewProductData['product']->pv_id;?>" data-target="#exampleModal"><?php echo $this->lang->line('answer');?></button>           
							<?php } ?>
						</div>
						<hr>
				  	<?php } ?>
				</section>
			  </div>
		  </div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

	<div id="costumModal27" class="modal" data-easein="shake"  tabindex="-1" role="dialog" aria-labelledby="costumModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				<h4 class="modal-title">
					   <?php echo $this->lang->line('copy_clipboard');?>
					</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						×
					</button>
					
				</div>
				<div class="modal-body">
					<p>
					   <?php echo $this->lang->line('link_copied');?>
					</p>
				</div>
				<div class="modal-footer">
					<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
						<?php echo $this->lang->line('close');?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="message-panel" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<span><?php echo $this->lang->line('message_form');?></span>
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
								<a href="javascript:" class="btn btn-primary" id="sendMessage"><?php echo $this->lang->line('send');?></a> 
							</div>
						</div>
					</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	<div id="choose_shipping_method" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><?php echo $this->lang->line('choose_ship');?></h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button> 
				</div>
				<div class="modal-body">
					<table class="table table-bordered">
						<thead>
							<th><?php echo $this->lang->line('ship_company');?></th>
							<!-- <th>Shipping Cost</th> -->
							<th><?php echo $this->lang->line('estimate_delivery');?></th>
							<th><?php echo $this->lang->line('description');?></th>
						</thead>
						<tbody>
						<?php $i=0;foreach($shippingData as $sd){ ?>
							<tr>
								<td> 
									<div class="custom-control custom-radio mb-3">
										<input type="radio" class="custom-control-input shipping_method" data-shipping_id="<?php echo $sd->shipping_id?>" data-title="<?php echo $sd->title?>" data-price="<?php echo $sd->price?>" id="shipping<?php echo $sd->shipping_id?>" name="shipping_method" <?php echo ($i ==0)?"checked":""?> value="<?php echo $sd->shipping_id?>">
										<label class="custom-control-label radio-custom-input seller-info" for="shipping<?php echo $sd->shipping_id?>"><?php echo $sd->title?></label>
									</div> 
								</td>
								<!-- <td><?php echo "US $".$sd->price?></td> -->
								<td><?php echo $sd->duration." days"?></td>
								<td><?php echo $sd->description;?></td>
							</tr>
						<?php $i++;}?>
						</tbody>
						
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('ok');?></button>
				</div>
			</div>
		</div>
	</div>

<!-- qna Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="exampleModalLabel"><?php echo $this->lang->line('answer_question');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
		  <form action="<?php echo base_url('product/qna') ?>" METHOD="POST">
				<div class="input-group mb-3">
					<input type="text" class="form-control" name="answer" placeholder="Enter Your Answer here" aria-label="Recipient's username" aria-describedby="basic-addon2">
					<input type="hidden" name='answered_date' id="answered_date" value="">
					<input type="hidden" id="user_ans_id" name ="user_ans_id" value="<?php echo (isset($_SESSION['userid']))?$_SESSION['userid']:""; ?>">
					<input type="hidden" id="question_id" name="question_id" value="">
					<input type="hidden" id="qna_product_id" name="product_id" value="">
					<input type="hidden" id="modal_pv_id" name="pv_id" value="">
					<div class="input-group-append">
						 <button class="btn btn-outline-secondary" name="btn" value="ans" type="submit"><?php echo $this->lang->line('answer');?></button>
					</div>
				</div>
			</form>
		  </div>
		</div>
	  </div>
	</div>
		<!-- The Modal -->
  <div class="modal fade" id="shareModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Share This Product</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
		<div class="modal-body">
			<div class="row">
				<div class="social col-12 mt-2">
					<div data-network="facebook" data-url="<?php echo base_url()?>" class="st-custom-button">
						<a class="social-buttons__button social-button social-button--facebook" aria-label="Facebook">
							<span class="social-button__inner">
								<i class="fab fa-facebook-f"></i>
							</span>
						</a>
					</div>
					<div data-network="twitter" data-url="<?php echo base_url()?>" class="st-custom-button">
						<a class="social-buttons__button social-button social-button--twitter" aria-label="Twitter">
							<span class="social-button__inner">
								<i class="fab fa-twitter"></i>
							</span>
						</a>
					</div>
					<div data-network="linkedin" data-url="<?php echo base_url()?>" class="st-custom-button">
						<a class="social-buttons__button social-button social-button--linkedin" aria-label="LinkedIn">
							<span class="social-button__inner">
								<i class="fab fa-linkedin-in"></i>
							</span>
						</a>
					</div>
					<div data-network="whatsapp" data-url="<?php echo base_url()?>" class="st-custom-button">
						<a class="social-buttons__button social-button social-button--whatsapp" aria-label="WhatsApp">
							<span class="social-button__inner">
								<i class="fab fa-whatsapp"></i>
							</span>
						</a>
					</div>
					<div data-network="email" data-url="<?php echo base_url()?>" class="st-custom-button">
						<a class="social-buttons__button social-button social-button--mail" aria-label="Mail">
							<span class="social-button__inner">
								<i class="fa fa-envelope"></i>
							</span>
						</a>
					</div>
				</div>
				<div class="col-12 mt-4">
					<div class="input-group h-75">
						<input id="share_link" value="" type="text" class="form-control rounded-0" name="share_link" disabled maxlength="255">
						<div class="input-group-append">
							<button id="copy_link" class="btn pl-5 pr-5 btn-success">COPY THIS</button>
						</div>
					</div>
				</div>
				<div class="col-12 mt-4">
					<div class="alert alert-success text-center notif">
						<strong>Link Copied Successfully.</strong> 
					</div>
				</div>
			</div>
		</div>
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        
      </div>
    </div>
  </div>
<?php 
if(ENVIRONMENT == "production"){
	$jsonld_condition;
	switch ($viewProductData['product']->condition_id) {
		case 1:
		$jsonld_condition = "new";
		break;
		case 2:
			$jsonld_condition = "refurbished";
		break;
		case $viewProductData['product']->condition_id > 2:
			$jsonld_condition = "used";
		break;
		default:
			$jsonld_condition = "DamagedCondition";
	}
	$jsonld_quantity = ($viewProductData['product']->quantity > 0)?"InStock":"OutOfStock"; 
	$lowprice = ($low_and_high_price->lowprice)? $low_and_high_price->lowprice:$viewProductData['product']->price;
	if($discounted && ($discounted < $lowprice)){
		$lowprice = $this->cart->format_number($discounted);
	}
	$highprice = ($low_and_high_price->highprice)? $low_and_high_price->highprice:$viewProductData['product']->price;
	?>
	<script type="application/ld+json">
		{
			"@context": "https://schema.org/",
			"@type": "Product",
			"name": "<?php echo $product_name?>",
			"productID": "<?php echo $viewProductData['product']->product_id?>",
			"image": [
				"<?php echo ltrim(rtrim($json_ld_image, ',"'),'"');?>"
			],
			<?php if($viewProductData['product']->condition_id != 1){?>
				"excluded_destination":"Shopping Actions",
			<?php }?>
			"description": "<?php echo ($viewProductData['product']->short_description)?$viewProductData['product']->short_description:implode(",",$viewProductData['productFeatures'])?>",
			"gtin12": "<?php echo $viewProductData['product']->upc_code;?>",
			"mpn": "<?php echo $viewProductData['product']->sku_code;?>",
			"brand": {
				"@type": "Brand",
				"name": "<?php echo $viewProductData['product']->brand_name?>"
			},
			"review": {
				"@type": "Review",
				"reviewRating": {
				"@type": "Rating",
				"ratingValue": "3",
				"bestRating": "5",
				"worstRating": "1"
				},
				"author": {
				"@type": "Person",
				"name": "<?php echo $viewProductData['product']->store_name?>"
				}
			},
			"aggregateRating": {
				"@type": "AggregateRating",
				"bestRating": "5",
				"worstRating": "1",
				"ratingValue": "<?php echo ($avgRating['result']->avg_rating)?$avgRating['result']->avg_rating:5 ;?>",
				"reviewCount": "<?php echo ($avgRating['result']->total_review)?$avgRating['result']->total_review:1 ?>"
			},
			"offers": {
				"@type": "Offer",
				"url": "<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>",
				"priceCurrency": "USD",
				"price": "<?php echo ($discounted)?$this->cart->format_number($discounted):$viewProductData["product"]->price?>",
				"itemCondition": "<?php echo $jsonld_condition?>",
				"lowPrice": "<?php echo $lowprice?>",
				"highPrice": "<?php echo $highprice?>",
				"availability": "https://schema.org/<?php echo $jsonld_quantity?>"
			}
		}
	</script>
	<!-- Facebook Pixel Code -->
	<script>
	!function(f,b,e,v,n,t,s)
	{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
	n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];
	s.parentNode.insertBefore(t,s)}(window, document,'script',
	'https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '247459330040351');
	fbq('track', 'PageView');
	</script>
	<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=247459330040351&ev=PageView&noscript=1"/></noscript>
	<!-- End Facebook Pixel Code -->
<?php }?>