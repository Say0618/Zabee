<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$add_password=$this->session->flashdata('add_password');
$imageLink = $this->config->item('banner_path'); 
$product_link = "";
$today = date("Y-m-d");
$today_time = strtotime($today);
?>
<div class="container itemsDiv">
<?php if(!empty($banner)){ ?>
<div id="banner" class="bannercarousel carousel slide row" data-ride="carousel">
  <ul class="carousel-indicators">
  	<?php $i=0; 
        foreach($banner as $b){ $active=""; if($i ==0){$active = "active";} ?>
            <li data-target="#banner" data-slide-to="<?php echo $i?>" class="<?php echo $active;?>"></li>
    <?php $i++;}?>
  </ul>
  <div class="carousel-inner">
        <?php $i=0;
            foreach($banner as $b){ $active=""; 
			if($i ==0){$active = "active";} //print_r($b);?>
                <div class="carousel-item <?php echo $active?>"> 
                    <a href="<?php if($b->product_link){
						$name = $b->product_link;
						$name = str_replace(" ", "-", $name);
						// $id = $b->product_id;
						// $ProductEncode = base64_encode($name."_".$id); 
						echo base_url().'product/'.$name; 
					} else { 
						echo $b->banner_link; 
					} ?>"><img class="img-fluid mx-auto img d-flex" src="<?php echo $imageLink.$b->banner_image ?>" alt="<?php echo (isset($b->image_alt) && $b->image_alt != "")?$b->image_alt:"Banner";?>" /></a>
                </div>
        <?php $i++; } ?>
  </div>
  <a class="carousel-control-prev" href="#banner" data-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </a>
  <a class="carousel-control-next" href="#banner" data-slide="next">
    <span class="carousel-control-next-icon"></span>
  </a>
</div>
<?php } ?>
	<div class="row my-3 d-none">
        <div class="col-2 festo mx-auto">
            <span class="orange-color float-left"><i class="fas fa-paper-plane"></i></span>
            <span class="orange-color festo-heading"><?php echo $this->lang->line('fast_shipping_home');?></span>
        </div>
        <div class="col-2 festo mx-auto">
            <span class="float-left"><i class="fas fa-hourglass-half"></i></span>
            <span class="festo-heading"><?php echo $this->lang->line('easy_returns');?></span>
        </div>
        <div class="col-2 festo mx-auto">
            <span class="skyblue-color float-left"><i class="fas fa-lock"></i></span>
            <span class="skyblue-color festo-heading"><?php echo $this->lang->line('secure_payment');?></span>
        </div>
        <div class="col-2 festo mx-auto">
            <span class="lightgreen-color float-left"><i class="fas fa-user"></i></span>
            <span class="lightgreen-color festo-heading"><?php echo $this->lang->line('trusted_sellers');?></span>
        </div>
        <div class="col-2 festo mx-auto">
            <span class="orange-color float-left"><i class="fas fa-headset"></i></span>
            <span class="orange-color festo-heading"><?php echo $this->lang->line('online_support');?></span>
        </div>
    </div>
	<?php  if($this->session->flashdata("affiliated")){ ?>
    	<div class="alert alert-success alert-dismissable fade show mt-3" align="center" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo"<strong>Alert! ".$this->session->flashdata("affiliated")."</strong>";?>
        </div>
	<!-- start -->
    <?php }
	if($featured['rows'] > 0){
		$featuredCount = $featured['rows'];
	?>
	<div class="row">
		<div class="col-sm-12 more-from-this-features carousel slide" data-ride="carousel" data-interval="false">
			<hr class="my-2" />
			<div class="position-relative">
				<?php if($featuredCount > 4 || $detect->isMobile()){
					$btn_condition = (!$detect->isMobile() && $featuredCount < 3) || ($detect->isMobile() && $featuredCount <= 1);
				?>
				<div id="featured-container" class="col-12">
					<span class="prev-span <?php echo ($btn_condition)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle more-feature-prev" href="" data-toggle="tooltip" ><i class="fa fa-chevron-left"></i></a>
					</span>
					<span class="next-span <?php echo ($btn_condition)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle more-feature-next" href="" data-toggle="tooltip" ><i class="fa fa-chevron-right"></i></a>
					</span>
				</div>
				<?php }?>
				<h4 class="zabee-heading"><?php echo $this->lang->line('featured');?></h4>
				<div class="carousel-inner">
					<div class="carousel-item active">
						<div class="card-deck m-1">
					<?php 
					$i = 0; 
					foreach($featured['result'] as $featuredProduct){
						//echo product_thumb_path($featuredProduct['thumbnail']);die();
						// echo "<pre>";print_r($featuredProduct);die();
						/*$product_link = urlClean($featuredProduct['product_name']);
						$product_link=str_replace("(","", $product_link);
						$product_link=str_replace(")","", $product_link);
						$product_link=str_replace(" ","-", $product_link);*/
						if($i != 0 && $i%4 == 0 && !$detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}else if($i!=0 && $detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}
						?>
							<div class="col-sm-3 col-12">
								<div class="polaroid">
									<div class="image-center-parent">  
									<?php
										$ribbon = "";
										$expireTo_feature = $featuredProduct['valid_to']; 
										$expireFrom_feature =  $featuredProduct['valid_from']; 
										$expire_time_to_feature = strtotime($expireTo_feature);
										$expire_time_from_feature = strtotime($expireFrom_feature);
										$link = base_url().'product/'.$featuredProduct['slug']."/".$featuredProduct['pv_id'];
										if ($featuredProduct['is_local'] == '1'){
											$imagePath = product_thumb_path($featuredProduct['thumbnail']);
										}else if($featuredProduct['is_local'] == '0'){
											$imagePath = $featuredProduct['thumbnail'];
										}else{
											$imagePath = product_thumb_path("Preview.png");
										}
										$discounted = discount_forumula($featuredProduct['price'], $featuredProduct['discount_value'], $featuredProduct['discount_type'], $featuredProduct['valid_from'], $featuredProduct['valid_to']);
										if($featuredProduct['discount_value'] != "" && $featuredProduct['discount_type'] == "percent" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
											if($featuredProduct['discount_type'] == "percent"){
												$ribbon = "<div class='ribbon ribbon-top-left'><span>".$featuredProduct['discount_value']."% OFF</span></div>";
											}elseif($featuredProduct['discount_type'] == "fixed"){
												$ribbon = ($discounted)?"<div class='ribbon ribbon-top-left'><span>$".$featuredProduct['discount_value']."/- OFF</span></div>":"";
											}
										}
									?>
										
										<a href="<?php echo $link?>" class="btn cart-buttons "><img src="<?php echo $imagePath?>" alt='<?php echo ($discounted)?$featuredProduct['product_name'].' $'.number_format($discounted, 2):$featuredProduct['product_name'].' $'.number_format($featuredProduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
										<?php echo $ribbon; ?>
										</a>
										<?php if(!$this->isloggedin){ ?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $featuredProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
												<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php }else if($this->isloggedin && $featuredProduct['seller_id'] != $user_id){?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $featuredProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
											
											<?php /*?><a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($p['product_name'],$p['product_id'])?>" class="btn cart-buttons " data-toggle="tooltip"   title="Product Details"><i class="far fa-eye"></i></a> <?php */?>
											<?php if($this->isloggedin && isset($featuredProduct['already_saved']) && $featuredProduct['already_saved'] == ""){?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
											<?php } else if($this->isloggedin && isset($featuredProduct['already_saved']) && $featuredProduct['already_saved'] != "0") { ?>
												<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
											<?php }else{ ?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php } }?>
												<div class="col-sm-12 text-center position-absolute b-0">
												<div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($featuredProduct['rating'])?$featuredProduct['rating']:0;?>"></div>
											</div>
									</div>
									<div class="product-container ">
										<div class="row text-center <?php /*?>fixed-bottom-text<?php */?>">
											<div class="col-sm-12">
												<a href="<?php echo $link?>" title="<?php echo $featuredProduct['product_name'];?>">
													<h4 class="zabee-product-name">
														<?php echo (strlen($featuredProduct['product_name']) > 20)?substr($featuredProduct['product_name'],0,20)."...":$featuredProduct['product_name']; ?>
													</h4>
												</a>
											</div>
											
											<div class="col-sm-12 top-rated-product-price">                                
													$<?php echo ($discounted)?$this->cart->format_number($discounted):$this->cart->format_number($featuredProduct['price']);?>
													<?php if($featuredProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
														echo ($discounted)?"<strike>$".$this->cart->format_number($featuredProduct['price'])."</strike>":"";
													} ?>
											</div>
										</div>
									</div>
								</div>
							</div>   
					<?php  $i++;}?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php }?>
	<!-- end -->
	<!-- start -->
    <?php
	if($topRated['rows'] > 0){
		$topRatedProductCount = $topRated['rows'];
	?>
	<div class="row">
		<div class="col-sm-12 more-from-this-seller carousel slide" data-ride="carousel" data-interval="false">
			<hr class="my-2"/>
			<div class="position-relative">
				<?php if($topRatedProductCount > 4 || $detect->isMobile()){?>
				<div id="top-rated-container" class="col-12">
					<span class="prev-span <?php echo ($topRatedProductCount < 3)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle more-seller-prev" href="" data-toggle="tooltip" ><i class="fa fa-chevron-left"></i></a>
					</span>
					<span class="next-span <?php echo ($topRatedProductCount < 3)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle more-seller-next" href="" data-toggle="tooltip" ><i class="fa fa-chevron-right"></i></a>
					</span>
				</div>
				<?php }?>
				<h4 class="zabee-heading"><?php echo $this->lang->line('top_rated');?></h4>
				<div class="carousel-inner">
					<div class="carousel-item active">
						<div class="card-deck m-1">
					<?php 
					$i = 0;
					foreach($topRated['result'] as $topRatedProduct){
						$product_link = urlClean($topRatedProduct['product_name']);
						$product_link=str_replace("(","", $product_link);
						$product_link=str_replace(")","", $product_link);
						$product_link=str_replace(" ","-", $product_link);
						$link = base_url().'product/'.$topRatedProduct['slug']."/".$topRatedProduct['pv_id'];
						if ($topRatedProduct['is_local'] == '1'){
							$imagePath = product_thumb_path($topRatedProduct['thumbnail']);
						}else if($topRatedProduct['is_local'] == '0'){
							$imagePath = $topRatedProduct['thumbnail'];
						}else{
							$imagePath = product_thumb_path("Preview.png");
						}
						if($i != 0 && $i%4 == 0 && !$detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}else if($i!=0 && $detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}?>
							<div class="col-sm-3 col-12">
								<div class="polaroid">
									<div class="image-center-parent">  
									<?php
										$ribbon = "";
										$expireTo_topRated = $topRatedProduct['valid_to']; 
										$expireFrom_topRated =  $topRatedProduct['valid_from']; 
										$expire_time_to_topRated = strtotime($expireTo_topRated);
										$expire_time_from_topRated = strtotime($expireFrom_topRated);
										$t_discounted = discount_forumula($topRatedProduct['price'], $topRatedProduct['discount_value'], $topRatedProduct['discount_type'], $topRatedProduct['valid_from'], $topRatedProduct['valid_to']);
										if($topRatedProduct['discount_value'] != "" && $expire_time_to_topRated >= $today_time && $expire_time_from_topRated <= $today_time){
											if($topRatedProduct['discount_type'] == "percent"){
												$ribbon = "<div class='ribbon ribbon-top-left'><span>".$topRatedProduct['discount_value']."% OFF</span></div>";
											}elseif($topRatedProduct['discount_type'] == "fixed"){
												$ribbon = ($t_discounted)?"<div class='ribbon ribbon-top-left'><span>$".$topRatedProduct['discount_value']."/- OFF</span></div>":"";
											}
										}
									?>
										<a href="<?php echo $link?>" class="btn cart-buttons "><img src="<?php echo $imagePath?>" alt='<?php echo ($t_discounted)?$topRatedProduct['product_name'].' $'.number_format($t_discounted, 2):$topRatedProduct['product_name'].' $'.number_format($topRatedProduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
										<?php echo $ribbon; ?>
										</a>
										<?php if(!$this->isloggedin){ ?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $topRatedProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
												<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $topRatedProduct['pv_id'];?>" data-id = "<?php echo $topRatedProduct['product_id']."-".$topRatedProduct['pv_id']?>" data-product_id="<?php echo $topRatedProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php }else if($this->isloggedin && $topRatedProduct['seller_id'] != $user_id){?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $topRatedProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
											<?php /*?><a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($p['product_name'],$p['product_id'])?>" class="btn cart-buttons " data-toggle="tooltip"   title="Product Details"><i class="far fa-eye"></i></a> <?php */?>
											<?php if($this->isloggedin && isset($topRatedProduct['already_saved']) && $topRatedProduct['already_saved'] == ""){?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $topRatedProduct['pv_id'];?>" data-id = "<?php echo $topRatedProduct['product_id']."-".$topRatedProduct['pv_id']?>" data-product_id="<?php echo $topRatedProduct['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
											<?php } else if($this->isloggedin && isset($topRatedProduct['already_saved']) && $topRatedProduct['already_saved'] != "0") { ?>
												<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
											<?php }else{ ?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $topRatedProduct['pv_id'];?>" data-id = "<?php echo $topRatedProduct['product_id']."-".$topRatedProduct['pv_id']?>" data-product_id="<?php echo $topRatedProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php } }?>
												<div class="col-sm-12 text-center position-absolute b-0">
												<div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($topRatedProduct['rating'])?$topRatedProduct['rating']:0;?>"></div>
											</div>
									</div>
									<div class="product-container ">
										<div class="row text-center <?php /*?>fixed-bottom-text<?php */?>">
											<div class="col-sm-12">
												<a href="<?php echo $link?>" title="<?php echo $topRatedProduct['product_name'];?>">
													<h4 class="zabee-product-name">
														<?php echo (strlen($topRatedProduct['product_name']) > 20)?substr($topRatedProduct['product_name'],0,20)."...":$topRatedProduct['product_name']; ?>
													</h4>
												</a>
											</div>
											
											<div class="col-sm-12 top-rated-product-price">                                
													$<?php echo ($t_discounted)?$this->cart->format_number($t_discounted):$this->cart->format_number($topRatedProduct['price']);?>
													<?php if($topRatedProduct['discount_value'] != "" && $expire_time_to_topRated >= $today_time && $expire_time_from_topRated <= $today_time){
														echo ($t_discounted)?"<strike>$".$this->cart->format_number($topRatedProduct['price'])."</strike>":"";
													} ?>
											</div>
										</div>
									</div>
								</div>
							</div>   
							<?php  $i++;}?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php }?>
	<!-- end -->
	<!-- start -->
    <?php
	if($product['rows'] > 0){
		$productCount = $product['rows'];
	?>
	<div class="row">
		<div class="col-sm-12 product-accessories carousel slide" data-ride="carousel" data-interval="false">
			<hr class="my-2"/>
			<div class="position-relative">
				<?php if($productCount > 4 || $detect->isMobile()){?>
				<div id="product-accessories-container" class="col-12">
					<span class="prev-span <?php echo ($productCount < 3)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle product-accessories-prev" href="" data-toggle="tooltip" ><i class="fa fa-chevron-left"></i></a>
					</span>
					<span class="next-span <?php echo ($productCount < 3)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle product-accessories-next" href="" data-toggle="tooltip" ><i class="fa fa-chevron-right"></i></a>
					</span>
				</div>
				<?php }?>
				<h4 class="zabee-heading"><?php echo $this->lang->line('new_in_store');?></h4>
				<div class="carousel-inner">
					<div class="carousel-item active">
						<div class="card-deck m-1">
					<?php 
					$i = 0; 
					foreach($product['result'] as $allproduct){
						// echo "<pre>";print_r($featuredProduct);die();
						$product_link = urlClean($allproduct['product_name']);
						$product_link=str_replace("(","", $product_link);
						$product_link=str_replace(")","", $product_link);
						$product_link=str_replace(" ","-", $product_link);
						$link = base_url().'product/'.$allproduct['slug']."/".$allproduct['pv_id'];
						if ($allproduct['is_local'] == '1'){
							$imagePath = product_thumb_path($allproduct['thumbnail']);
						}else if($allproduct['is_local'] == '0'){
							$imagePath = $allproduct['thumbnail'];
						}else{
							$imagePath = product_thumb_path("Preview.png");
						}
						if($i != 0 && $i%4 == 0 && !$detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}else if($i!=0 && $detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}?>
							<div class="col-sm-3 col-12">
								<div class="polaroid">
									<div class="image-center-parent">  
									<?php
										$ribbon = "";
										$expireTo_allproduct = $allproduct['valid_to']; 
										$expireFrom_allproduct =  $allproduct['valid_from']; 
										$expire_time_to_allproduct = strtotime($expireTo_allproduct);
										$expire_time_from_allproduct = strtotime($expireFrom_allproduct);
										$a_discounted = discount_forumula($allproduct['price'], $allproduct['discount_value'], $allproduct['discount_type'], $allproduct['valid_from'], $allproduct['valid_to']);
										if($allproduct['discount_value'] != "" && $expire_time_to_allproduct >= $today_time && $expire_time_from_allproduct <= $today_time){
											if($allproduct['discount_type'] == "percent"){
												$ribbon = "<div class='ribbon ribbon-top-left'><span>".$allproduct['discount_value']."% OFF</span></div>";
											}elseif($allproduct['discount_type'] == "fixed"){
												$ribbon = ($a_discounted)?"<div class='ribbon ribbon-top-left'><span>$".$allproduct['discount_value']."/- OFF</span></div>":"";
											}
										}
									?>
										<a href="<?php echo $link;?>" class="btn cart-buttons "><img src="<?php echo $imagePath;?>" alt='<?php echo ($a_discounted)?$allproduct['product_name'].' $'.number_format($a_discounted, 2):$allproduct['product_name'].' $'.number_format($allproduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
										<?php echo $ribbon; ?>
										</a>
										<?php if(!$this->isloggedin){ ?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $allproduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
												<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $allproduct['pv_id'];?>" data-id = "<?php echo $allproduct['product_id']."-".$allproduct['pv_id']?>" data-product_id="<?php echo $allproduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php }else if($this->isloggedin && $allproduct['seller_id'] != $user_id){?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $allproduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
											
											<?php if($this->isloggedin && isset($allproduct['already_saved']) && $allproduct['already_saved'] == ""){?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $allproduct['pv_id'];?>" data-id = "<?php echo $allproduct['product_id']."-".$allproduct['pv_id']?>" data-product_id="<?php echo $allproduct['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
											<?php } else if($this->isloggedin && isset($allproduct['already_saved']) && $allproduct['already_saved'] != "0") { ?>
												<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
											<?php }else{ ?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $allproduct['pv_id'];?>" data-id = "<?php echo $allproduct['product_id']."-".$allproduct['pv_id']?>" data-product_id="<?php echo $allproduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php } }?>
												<div class="col-sm-12 text-center position-absolute b-0">
												<div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($allproduct['rating'])?$allproduct['rating']:0;?>"></div>
											</div>
									</div>
									<div class="product-container ">
										<div class="row text-center <?php /*?>fixed-bottom-text<?php */?>">
											<div class="col-sm-12">
												<a href="<?php echo $link?>" title="<?php echo $allproduct['product_name'];?>">
													<h4 class="zabee-product-name">
														<?php echo (strlen($allproduct['product_name']) > 20)?substr($allproduct['product_name'],0,20)."...":$allproduct['product_name']; ?>
													</h4>
												</a>
											</div>
											
											<div class="col-sm-12 top-rated-product-price">                                
												$<?php echo ($a_discounted)?$this->cart->format_number($a_discounted):$this->cart->format_number($allproduct['price']);?>
												<?php if($allproduct['discount_value'] != "" && $expire_time_to_allproduct >= $today_time && $expire_time_from_allproduct <= $today_time){
													echo ($a_discounted)?"<strike>$".$this->cart->format_number($allproduct['price'])."</strike>":"";
												} ?>
											</div>
										</div>
									</div>
								</div>
							</div>   
						<?php  $i++;}?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }?>
    <!-- end -->
	<?php
	if(!empty($show_on_homepage)){
		foreach($show_on_homepage as $cat){
		if($cat['rows'] > 0 && $cat['is_slider'] == 1){
			$catCount = $cat['rows'];
			$class= str_replace(" ","-",strtolower($cat['cat_name']));
			$divClass= $class;
			$prevBtn = $class."-prev";
			$nextBtn = $class."-next";
	?>
	<div class="row show_on_homepage" data-class="<?php echo $divClass?>" data-prev="<?php echo $prevBtn?>" data-next="<?php echo $nextBtn?>">
		<div class="col-sm-12 <?php echo $divClass?> carousel slide" data-ride="carousel" data-interval="false">
			<hr class="my-2" />
			<div class="position-relative">
				<?php if($catCount > 4 || $detect->isMobile()){?>
				<div id="featured-container" class="col-12">
					<span class="prev-span <?php echo ($catCount < 3)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle <?php echo $prevBtn?>" href="" data-toggle="tooltip" ><i class="fa fa-chevron-left"></i></a>
					</span>
					<span class="next-span <?php echo ($catCount < 3)?"d-none":""?>">
						<a class="btn btn-hover color-8 rounded-circle <?php echo $nextBtn?>" href="" data-toggle="tooltip" ><i class="fa fa-chevron-right"></i></a>
					</span>
				</div>
				<?php }?>
				<h4 class="zabee-heading"><?php echo $cat['cat_name'];?></h4>
				<div class="carousel-inner">
					<div class="carousel-item active">
						<div class="card-deck m-1">
					<?php 
					$i = 0; 
					foreach($cat['result'] as $featuredProduct){
						if($i != 0 && $i%4 == 0 && !$detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}else if($i!=0 && $detect->isMobile()){
							echo '</div></div><div class="carousel-item"><div class="card-deck m-1">';
						}
						?>
							<div class="col-sm-3 col-12">
								<div class="polaroid">
									<div class="image-center-parent">  
									<?php
										$ribbon = "";
										$expireTo_feature = $featuredProduct['valid_to']; 
										$expireFrom_feature =  $featuredProduct['valid_from']; 
										$expire_time_to_feature = strtotime($expireTo_feature);
										$expire_time_from_feature = strtotime($expireFrom_feature);
										$link = base_url().'product/'.$featuredProduct['slug']."/".$featuredProduct['pv_id'];
										if ($featuredProduct['is_local'] == '1'){
											$imagePath = product_thumb_path($featuredProduct['thumbnail']);
										}else if($featuredProduct['is_local'] == '0'){
											$imagePath = $featuredProduct['thumbnail'];
										}else{
											$imagePath = product_thumb_path("Preview.png");
										}
										$show_home = discount_forumula($featuredProduct['price'], $featuredProduct['discount_value'], $featuredProduct['discount_type'], $featuredProduct['valid_from'], $featuredProduct['valid_to']); 
										if($featuredProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
											if($featuredProduct['discount_type'] == "percent"){
												$ribbon = "<div class='ribbon ribbon-top-left'><span>".$featuredProduct['discount_value']."% OFF</span></div>";
											}elseif($featuredProduct['discount_type'] == "fixed"){
												$ribbon = ($show_home)?"<div class='ribbon ribbon-top-left'><span>$".$featuredProduct['discount_value']."/- OFF</span></div>":"";
											}
										}
									?>
									
										<a href="<?php echo $link?>" class="btn cart-buttons "><img src="<?php echo $imagePath?>" alt='<?php echo ($show_home)?$featuredProduct['product_name'].' $'.number_format($show_home, 2):$featuredProduct['product_name'].' $'.number_format($featuredProduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
										<?php echo $ribbon; ?>
										</a>
										<?php if(!$this->isloggedin){ ?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $featuredProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
												<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php }else if($this->isloggedin && $featuredProduct['seller_id'] != $user_id){?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $featuredProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
											
											<?php /*?><a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($p['product_name'],$p['product_id'])?>" class="btn cart-buttons " data-toggle="tooltip"   title="Product Details"><i class="far fa-eye"></i></a> <?php */?>
											<?php if($this->isloggedin && isset($featuredProduct['already_saved']) && $featuredProduct['already_saved'] == ""){?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
											<?php } else if($this->isloggedin && isset($featuredProduct['already_saved']) && $featuredProduct['already_saved'] != "0") { ?>
												<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
											<?php }else{ ?>
													<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
											<?php } }?>
												<div class="col-sm-12 text-center position-absolute b-0">
												<div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($featuredProduct['rating'])?$featuredProduct['rating']:0;?>"></div>
											</div>
									</div>
									<div class="product-container ">
										<div class="row text-center <?php /*?>fixed-bottom-text<?php */?>">
											<div class="col-sm-12">
												<a href="<?php echo $link?>" title="<?php echo $featuredProduct['product_name'];?>">
													<h4 class="zabee-product-name">
														<?php echo (strlen($featuredProduct['product_name']) > 20)?substr($featuredProduct['product_name'],0,20)."...":$featuredProduct['product_name']; ?>
													</h4>
												</a>
											</div>
											<div class="col-sm-12 top-rated-product-price">                                
												$<?php echo ($show_home)?$this->cart->format_number($show_home):$this->cart->format_number($featuredProduct['price']);?>
												<?php if($featuredProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
													echo ($show_home)?"<strike>$".$this->cart->format_number($featuredProduct['price'])."</strike>":"";
												} ?>
											</div>
										</div>
									</div>
								</div>
							</div>   
					<?php  $i++;}?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php }else if($cat['rows'] > 0 && $cat['is_slider'] == 0){ ?>
		<div class="row">
			<div class="col-12">
				<hr class="my-2" />
				<h4 class="zabee-heading"><?php echo $cat['cat_name'];?></h4>
			</div>
		<?php 
			$i = 0; 
			foreach($cat['result'] as $featuredProduct){
				if($i != 0 && $i%4 == 0 && !$detect->isMobile()){
					echo '<div class="col-12 my-4"></div>';
				}else if($i!=0 && $detect->isMobile()){
					echo '<div class="col-12 my-4"></div>';
				}
				?>
					<div class="col-sm-3 col-12">
						<div class="polaroid">
							<div class="image-center-parent">  
							<?php
								$ribbon = "";
								$expireTo_feature = $featuredProduct['valid_to']; 
								$expireFrom_feature =  $featuredProduct['valid_from']; 
								$expire_time_to_feature = strtotime($expireTo_feature);
								$expire_time_from_feature = strtotime($expireFrom_feature);
								$show_home = discount_forumula($featuredProduct['price'], $featuredProduct['discount_value'], $featuredProduct['discount_type'], $featuredProduct['valid_from'], $featuredProduct['valid_to']); 
								if($featuredProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
									if($featuredProduct['discount_type'] == "percent"){
										$ribbon = "<div class='ribbon ribbon-top-left'><span>".$featuredProduct['discount_value']."% OFF</span></div>";
									}elseif($featuredProduct['discount_type'] == "fixed"){
										$ribbon = ($show_home)?"<div class='ribbon ribbon-top-left'><span>$".$featuredProduct['discount_value']."/- OFF</span></div>":"";
									}
								}
							?>
							<?php if($featuredProduct['is_local'] == '1'){ ?>
									<a href="<?php echo base_url().'product/'.$featuredProduct['slug']?>" class="btn cart-buttons "><img src="<?php echo product_thumb_path($featuredProduct['thumbnail'])?>" alt='<?php echo ($show_home)?$featuredProduct['product_name'].' $'.number_format($show_home, 2):$featuredProduct['product_name'].' $'.number_format($featuredProduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
									<?php echo $ribbon; ?>
									</a>
								<?php }else if($featuredProduct['is_local'] == '0'){?>
										<a href="<?php echo base_url().'product/'.$featuredProduct['slug']?>" class="btn cart-buttons "><img src="<?php echo $featuredProduct['thumbnail']?>" alt='<?php echo ($show_home)?$featuredProduct['product_name'].' $'.number_format($show_home, 2):$featuredProduct['product_name'].' $'.number_format($featuredProduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
										<?php echo $ribbon; ?>
										</a>
								<?php } else {?>
										<a href="<?php echo base_url().'product/'.$featuredProduct['slug']?>" class="btn cart-buttons "><img src="<?php echo product_thumb_path("Preview.png")?>" alt='<?php echo ($show_home)?$featuredProduct['product_name'].' $'.number_format($show_home, 2):$featuredProduct['product_name'].' $'.number_format($featuredProduct['price'], 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
										<?php echo $ribbon; ?>
										</a>
								<?php }?>
								<?php if(!$this->isloggedin){ ?>
										<a href="javascript:void(0)" data-product_variant_id="<?php echo $featuredProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
										<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
									<?php }else if($this->isloggedin && $featuredProduct['seller_id'] != $user_id){?>
										<a href="javascript:void(0)" data-product_variant_id="<?php echo $featuredProduct['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
									
									<?php /*?><a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($p['product_name'],$p['product_id'])?>" class="btn cart-buttons " data-toggle="tooltip"   title="Product Details"><i class="far fa-eye"></i></a> <?php */?>
									<?php if($this->isloggedin && isset($featuredProduct['already_saved']) && $featuredProduct['already_saved'] == ""){?>
											<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
									<?php } else if($this->isloggedin && isset($featuredProduct['already_saved']) && $featuredProduct['already_saved'] != "0") { ?>
										<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
									<?php }else{ ?>
											<button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $featuredProduct['pv_id'];?>" data-id = "<?php echo $featuredProduct['product_id']."-".$featuredProduct['pv_id']?>" data-product_id="<?php echo $featuredProduct['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
									<?php } }?>
										<div class="col-sm-12 text-center position-absolute b-0">
										<div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($featuredProduct['rating'])?$featuredProduct['rating']:0;?>"></div>
									</div>
							</div>
							<div class="product-container ">
								<div class="row text-center <?php /*?>fixed-bottom-text<?php */?>">
									<div class="col-sm-12">
										<a href="<?php echo base_url().'product/'.$featuredProduct['slug']?>" title="<?php echo $featuredProduct['product_name'];?>">
											<h4 class="zabee-product-name">
												<?php echo (strlen($featuredProduct['product_name']) > 20)?substr($featuredProduct['product_name'],0,20)."...":$featuredProduct['product_name']; ?>
											</h4>
										</a>
									</div>
									<div class="col-sm-12 top-rated-product-price">                                
										$<?php echo ($show_home)?$this->cart->format_number($show_home):$this->cart->format_number($featuredProduct['price']);?>
										<?php if($featuredProduct['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
											echo ($show_home)?"<strike>$".$this->cart->format_number($featuredProduct['price'])."</strike>":"";
										} ?>
									</div>
								</div>
							</div>
						</div>
					</div>   
			<?php  $i++;}?>
			<div class="col-12 text-center pt-3">
				<a href="<?php echo base_url($slug)."?see_all=1";?>" class="btn">See all products in <?php echo $cat['cat_name'];?></a>
			</div>
		</div>
	<?php }} }?>
</div>
<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<script src="<?php echo assets_url('front/js/jquery.rateyo.min.js'); ?>"></script>