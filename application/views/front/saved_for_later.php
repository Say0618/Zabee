<div class="container wishlist_container">
	<?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>base_url()."wish_list","cat_name"=>$this->lang->line('wishlist'))))); ?>  
	<div class="row mt-3">
		<div class="col-4 p-0 d-none">
			<button class="btn btn-primary-outline gridButton pl-0 d-none" onclick="sendingViewStatus('grid')"><i class="fa fa-th-large fa-lg"></i></button>
			<button class="btn btn-primary-outline listButton active pl-0 d-none" onclick="sendingViewStatus('list')"><i class="fa fa-align-justify fa-lg" ></i></button> 
		</div>
		<div class = "col-sm-3 pl-0">
			<div class="row">
				<div class="container wishlist_container">
				</div>
				<?php if($selWishCat == ""){ ?>	
					<div class="container wishlist_container">
						<div class="list-group wishlist-cat-div">
								<span class="list-group-item your-wish-list"><?php echo $this->lang->line('your_wish');?></span>
								<span class="list-group-item your-wish-list"><?php echo $this->lang->line('none_avail');?></span>
								<span class="list-group-item your-wish-list"></span>
						</div><br />
					</div>
				<?php } else { ?>
					<div class="container wishlist_container">
						<div class="list-group">
								<div id="wishlist">
									<?php for($i = 0; $i < count($selWishCat); $i++){ ?>
									<div>
									<?php if($selWishCat[$i]->category_name != "like"){?>
										<button type="button" class="position-absolute delete-wish border-0 bg-transparent text-danger" value="<?php echo $selWishCat[$i]->category_id?>"><i class="fa fa-window-close red"></i></button>
									<?php } ?>
										<a href = "<?php echo base_url('product/saved_for_later/'.$selWishCat[$i]->category_id);  ?>" class="<?php if($this->uri->segment(3)){if($selWishCat[$i]->category_id == $this->uri->segment(3)){?> active <?php }} ?> list-group-item category-list text-capitalize <?php echo $selWishCat[$i]->category_name != "like" ? "ml-4":""; ?>"><?php echo $selWishCat[$i]->category_name ?>
										<span class="badge customBage">
											<?php
											if(isset($productsPerCategory) && $productsPerCategory != "" ){
												$num = "0";
												for($j = 0; $j < count($productsPerCategory); $j++){
													if(($selWishCat[$i]->category_id) == ($productsPerCategory[$j]->category_id)){
														$num = $productsPerCategory[$j]->forThisCategory;
														break;
													}
												}
												echo $num;
											}else{
												echo "0";
											}
											?>
										</span>
										</a>
									</div>
									<?php } ?>
								</div>
							<?php $all = "all";?>	
							<a href = "<?php echo base_url('product/saved_for_later/'.$all);  ?>" class="<?php if($this->uri->segment(3)){if($this->uri->segment(3) == $all){?> active <?php }} ?> list-group-item category-list"><?php echo $this->lang->line('show_all');?> <span id="all-count" class="badge customBage"><?php echo isset($totalProducts[0]->total)?$totalProducts[0]->total:"0" ?></span></a>
						</div>
					</div>
				<?php } ?>		
			</div>
		</div>
		
        <div class="col-sm-9">
				<?php
                $cartbtn= 0;
                $heartbtn=0;
                ?> 
                <div class="row bg-white">
					<div class="container flex" >
						<div class="row mt-2 flex border-bottom #ddd pb-2" >
							<div class="col-sm-4" id="save-cat">
								<form id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data">
									<select class="p-1" name="myselect" id="myselect" onchange="this.form.submit()" style="box-shadow: -1px -1px 3px #ddd;">
									  <option value="" disabled selected><?php echo $this->lang->line('category_select');?></option>
										<?php if($getCategoryNames){ foreach($getCategoryNames as $gcn){ ?>
											<?php if($gcn['catId'] != 0){ ?>
												<option class="dropcatName" value="<?php echo $gcn['catId']; ?>" <?php echo (isset($selected) && $selected == $gcn['catId'])?'selected="selected"':""; ?>><?php echo $gcn['categoryName'] ?>	</option>
											<?php } ?>
										<?php } }?>
									</select>
								</form>
							</div>
							<div class="col-sm-8" id="save-btn">
								<button type="button" class="btn btn-sm btn-hover color-blue float-right" data-toggle="modal" data-target="#myModal4"> + <?php echo $this->lang->line('create_wish');?></button>
							</div>
						</div>
					</div>
                <?php foreach($save_for_later_data['data']  as $w){
					// echo"<pre>"; print_r($save_for_later_data['data']); die();
                    if(is_array($w->is_primary_image)){
                     $img =  product_thumb_path(isset($w->is_primary_image[0]) ? $w->is_primary_image[0]->is_primary_image : "");
                    }
                    else{
                        $img =  product_thumb_path($w->is_primary_image);
					}
                    $pn = $w->product_name;
                    $product_link = clean($pn);
                    $product_link=str_replace("(","", $product_link);
					$product_link=str_replace(")","", $product_link);
					$price = discount_forumula($w->price, $w->value, $w->type, $w->valid_from, $w->valid_to);
					?>
                    <div class="col-12 mt-3 pb-3 product-row column">
                    <div class="row">
						<div class="col-2 ProductImageForChangingInAnotherView">
							<a href="<?php echo base_url().'product/'.$w->slug ?>" class="text-center" target="_new">
								<img src="<?php echo $img;?>" alt="<?php echo $w->product_name." $".($price)?$this->cart->format_number($price):$this->cart->format_number($w->price)?>" class="pdImage img img-fluid mx-auto my-auto w_image-center pro_img d-block"  data-toggle="tooltip"  title="<?php echo $w->product_name; ?>"> 
							</a>
						</div>
							<div class="row d-none btnsRow wishlistBtnsRow-forgridview">  
								<div class="col-sm-12 PriceTagForChangingInAnotherView">
									<span class="PriceTag">Price - US <?php echo ($price)?$this->cart->format_number($price):$this->cart->format_number($w->price) ?></span>&nbsp;&nbsp;
									<?php if($price){ ?><strike>$<?php echo $this->cart->format_number($w->price)?></strike> <?php } ?>
									<?php if(!$this->isloggedin){ ?>
										<a href="javascript:void(0)" data-product_variant_id="<?php echo $w->pv_id?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a>  
											<?php }else if($this->isloggedin && $w->seller_id != $user_id){?>
												<a href="javascript:void(0)" data-product_variant_id="<?php echo $w->pv_id?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
											<?php } ?>
										<a class="cart-buttons btn delete-saved" href="javascript:void(0)" onclick="delete_from_list('<?php echo $w->wish_id ?>',this)" ><i class="far fa-trash-alt"></i></a> 
								</div>
								<?php 
									$user_id = (isset($_SESSION['userid']) && $_SESSION['userid'] !="")?$_SESSION['userid']:"";	
								?> 
							</div>
							<div class="col-8 pl-0 pdBox">
								<div class="col-sm-12 pl-0 forMargin">
										<a class="product-title wordwrap d-none d-sm-block" href="<?php echo base_url().'product/'.$w->slug ?>" data-toggle="tooltip" title="<?php echo $w->product_name?>"  target="_new">
										<p class="wishlist-pd-name mb-0"><?php echo (strlen($w->product_name) > 30)?(substr($w->product_name,0,40)."..."):(trim($w->product_name));?> </p>
										</a>
										<a class="product-title wordwrap d-block d-sm-none" href="<?php echo base_url().'product/'.$w->slug ?>" data-toggle="tooltip" title="<?php echo $w->product_name?>"  target="_new">
										<p class="wishlist-pd-name mb-0"><?php echo (strlen($w->product_name) > 18)?(substr($w->product_name,0,18)."..."):(trim($w->product_name));?> </p>
										</a>
										<p class="wish_list-text mb-0"><?php echo $w->condition_name; ?></p>
								</div>
								<div class="col-sm-12 pl-0 product-description forMargin">                                            
								 <?php 
									$pro_description = trim(strip_tags(html_entity_decode($w->seller_product_description))); 
									echo (strlen($pro_description) > 150)?substr($pro_description,0,150)."...":$pro_description; 
								?>
								</div>
								<div class="row btnsRow wishlistBtnsRow">  
									<div class="col-sm-12 PriceTagForChangingInAnotherView" > 
										<span class="PriceTag">Price - US <?php echo ($price)?$this->cart->format_number($price):$this->cart->format_number($w->price) ?></span> &nbsp;&nbsp;
										<?php if($price){ ?><strike>$<?php echo $this->cart->format_number($w->price)?></strike> <?php } ?>
										<?php if(!$this->isloggedin){ ?>
											<a href="javascript:void(0)" data-product_variant_id="<?php echo $w->pv_id?>" class="btn cart-buttons addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
												 <?php }else if($this->isloggedin && $w->seller_id != $user_id){?>
													<a href="javascript:void(0)" data-product_variant_id="<?php echo $w->pv_id?>" class="btn cart-buttons addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
												<?php }?>
											<a class="cart-buttons btn delete-saved" href="javascript:void(0)" onclick="delete_from_list('<?php echo $w->wish_id ?>',this)" ><i class="far fa-trash-alt"></i></a> 
									</div>
									<?php 
										$user_id = (isset($_SESSION['userid']) && $_SESSION['userid'] !="")?$_SESSION['userid']:"";	
									?> 
								</div>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
				   <?php  } ?>	
				</div>
        <div class="offset-lg-8 col-lg-4 col-12 mt-5"> <a class="archive-link d-none" href="<?php echo base_url('orders')?>"><?php echo $this->lang->line('visit_archieve');?> <i class="fas fa-archive pl-2"></i></a></div>
                <div class="col-12 ">
                    <?php if(($links["links"])){ ?>
                    <div class="clearfix"></div>
                    <div class="pagination-div">
                        <ul class="pagination pull-right mt-5">
                            <?php foreach($links['links'] as $page){ 
                                echo $page;
                            } ?>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
	        </div>
</div>
<div id="myModal4" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
      <div class="modal-header">
		<h4 class="modal-title"><?php echo $this->lang->line('create_wishlist');?></h4>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		</div>
      <div class="modal-body">
			<form id="myform" name="myForm" role="form">
				<div class = "catNameInput" id = "catNameInput">
					<label><?php echo $this->lang->line('add_new_cat');?>: <label>&nbsp;
					<input type="text" name="cat_name" id="cat_name" placeholder = "default...">
				</div>
				<span id="cat-error" class="text-danger"></span>
			</form>
      </div>
      <div class="modal-footer">
		<button type="submit" class="btn btn-success  ml-3" id="categorySubmit"><?php echo $this->lang->line('add_wishlist');?></button>	
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div id="deleteCat_Modal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
	  	<h4 class="modal-title">Delete Category</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this category and all the products inside it?</p>
		<span class="text-success text-center" id="message"></span>
      </div>
      <div class="modal-footer">
	  	<button type="button" class="btn btn-danger" id="delete">Delete</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>