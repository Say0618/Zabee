<div class="container mb-5">
    <div class="row">
        <div class="">
            <?php $cover = ($sellerData[0]->cover_image != "") ? $sellerData[0]->cover_image : "default.jpg"; ?>
            <img src="<?php echo $this->config->item('store_cover_path').$cover;?>" class="img-fluid img mx-auto 
            <?php if(isset($_SESSION['userid'])){
                    if($_SESSION['userid']== $sellerData[0]->seller_id){ echo 'cover-image';}
                }else { echo 'cover-image'; } ?> cover-imagee" id="cover-image" />
            <!-- <?php 
                if(isset($_SESSION['userid'])){
                    if($_SESSION['userid']== $sellerData[0]->seller_id){ ?>
                        <div class="p-imagee">
                            <i class="fa fa-camera upload-buttonn"><span style="font-size: 20px; padding-left: 10px;">Edit Cover</span></i>
                            <form action="<?php echo base_url()?>home/coverimageupload" method="post" enctype="multipart/form-data" id="formimage2">
                                <input class="file-uploadd" type="file" accept="image/*" name="cover_image" style="display: none;">
                            </form>
                        </div>
            <?php } }?> -->
        </div>
        <div class="col d-none d-sm-block" id="prof-img-display">
            <?php $picture = ($sellerData[0]->store_logo != "") ? $sellerData[0]->store_logo : "default.jpg"; ?>
            <img src="<?php echo $this->config->item("store_logo_path").$picture;?>" class="img img-fluid mx-auto rounded-circle <?php 
            if(isset($_SESSION['userid'])){
                if($_SESSION['userid']== $sellerData[0]->seller_id){ echo 'prof-img-center';}
            }else { echo 'proff-img-center'; }?>" id="profile-img-center"  />
        </div>
        <!-- <?php 
            if(isset($_SESSION['userid'])){
                if($_SESSION['userid']== $sellerData[0]->seller_id){ ?>
            <div class="p-image">
                <i class="fa fa-camera upload-button" style="font-size: 40px;"><div class="upload-button" style="font-size: 22px!important;margin-left: -10px;">Update</div></i>
                <form action="<?php echo base_url()?>home/imageupload" method="post" enctype="multipart/form-data" id="formimage">
                    <input type="hidden" id="inputtt" value="<?php echo $_SESSION['userid'];?>">
                    <input class="file-upload" type="file" name="seller_image" style="display: none;">
                </form>
            </div>
        <?php } }?> -->
    </div>
</div>

    <?php 
        $today = date("Y-m-d");
        $today_time = strtotime($today);
        $this->found = (isset($_GET['search']) && $_GET['search'] !="")?'for "'.(trim($_GET['search'])).'"':"";
    ?>
<div class="container">
    <div class="row ">
        <div class="col-sm-3 filter-column">
            <?php 
            $current_link = $_SERVER['REQUEST_URI'];
            $url=strtok($_SERVER["REQUEST_URI"],'?');
            $all_link = current_url();
            if(isset($_GET['search'])){
                $category_link = $current_link;
                $brand_link = $current_link;
            } else {
                $category_link = $current_link;
                $brand_link = $current_link;
            }

            ?>
            <?php if(!empty($category)){ ?>
            <div class="row">
                <div class="col-sm-12">
                    <button type="button" class="btn collapse-btn" data-toggle="collapse" data-target="#categories" style="">
                        <h5 class="my-2"><?php echo $this->lang->line('categories');?> <i class="fa fa-angle-down float-right" ></i></h5>
                    </button>
                </div>
                <div class="col-sm-12">
                    <div id="categories" class="collapse show" >
                        <ul class="navbar-nav" id="category_slider">
                            <?php 
                            // echo"<pre>"; print_r($category); die();
                            foreach($category as $cat){
                                $newUrlLinkForCategory = explode("&",$category_link); 

                                array_splice($newUrlLinkForCategory, 1, 1);
                                if(count($newUrlLinkForCategory) > 1){
                                    $newUrlLinkForCategory = implode("&",$newUrlLinkForCategory);
                                }
                                else{
                                    $newUrlLinkForCategory = implode("",$newUrlLinkForCategory);
                                }
                               
                                if(isset($_GET['category_search']) || (!isset($_GET['brands_search']) && !isset($_GET['search']) ) ){
                                    $category_link_revised = "?category_search=".$cat->category_id;
                                }else{
                                    $category_link_revised = $newUrlLinkForCategory."&category_search=".$cat->category_id;   
                                }
                                if(isset($_GET['category_search']) && $cat->category_id ==$_GET['category_search'] ){
                                    if(strpos($current_link, "category_search=$cat->category_id&") !== false){
                                        $new_link = str_replace("category_search=$cat->category_id&","",$current_link);
                                    }else if(strpos($current_link, "&category_search=$cat->category_id") !== false){
                                        $new_link = str_replace("&category_search=$cat->category_id","",$current_link);
                                    }else if(strpos($current_link, "?category_search=$cat->category_id") !== false){
                                        $new_link = str_replace("?category_search=$cat->category_id","",$current_link);
                                    } 
                                    $cat_title = '<span title="Remove" class="float-right px-1 border border-dark">X</span>'.$cat->category_name;
                                    $cat_path = $new_link;
                                }else{
                                    $cat_title = $cat->category_name;
                                    $cat_path = $category_link_revised;
                                }
                                if(!isset($_GET['category_search'])){
                                    ?>
                                    <li class="nav-item">
                                     <a class="nav-link cat-link" href="<?php echo $cat_path;?>"><?php echo $cat_title;?></a>
                                    </li>
                                    <?php }elseif(isset($_GET['category_search']) && $_GET['category_search'] == $cat->category_id){ ?>
                                        <li class="nav-item">
                                            <a class="nav-link cat-link" href="<?php echo isset($new_link)?$new_link:base_url()."product/SearchResults"?>"> <span title="Remove" class="float-right px-1 border border-dark">X</span> <?php echo $cat->category_name;?></a>
                                        </li>
                                   <?php } }?> 
                            <div id="loadMore"><h6  class="viewmore-heading" >VIEW MORE</h6></div>
                            <div id="showLess"><h6  class="viewmore-heading" >SHOW LESS</h6></div>  
                        </ul>
                    </div>
                </div>
            </div>
            <hr>
            <?php }?>
            <div class="row <?php echo (empty($category))?'pt-3':''?>">
                <div class="col-sm-12">
                    <h5 class="price-heading"><?php echo $this->lang->line('price');?></h5>
                    <?php if(isset($_GET['price_range'])){ 
                    $minMax = (explode("-",$_GET['price_range'])); } ?>               
                    <div class="d-flex flex-row bd-highlight w-100 my-3" >
                        <div class="">
                            <input name="price-from" class="form-control  minimumRange" placeholder="<?php echo $this->lang->line('min');?>" id="minimumRange1" required="" <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[0]); } ?>"  />
                        </div>
                        <div class="mx-2 dash-space" >-</div>
                        <input type="hidden" id="priceRange" readonly >
                        <div class="">
                            <input name="price-to" class="form-control  maximumRange" placeholder="<?php echo $this->lang->line('max');?>" id="maximumRange1" required=""<?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[1]); } ?>" />
                        </div>
                        <div class="px-2 range-search-button">
                            <a  class=" btn form-control go-btn" onclick="go(1)"><i class="fa fa-angle-right" ></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr />

            <div class="row">
                <div class="col-sm-12">
                <h5>Shipping</h5>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input sort_by" value="free" data-from="fs" id="customCheckmobSA"
                                <?php if(isset($_GET['fs']) && $_GET['fs'] == 'free' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-checkout-label  shipCheckBox" for="customCheckmobSA">Free</label>
                            </div>
                        </div>         
                    </div>
                </div>
            </div> 
            <hr />
            <?php if(!empty($brandData)){ ?>
            <div class="row">
                <div class="col-sm-12">
                    <button type="button" class="btn collapse-btn" data-toggle="collapse" data-target="#brands" style="">
                        <h5 class="my-2"><?php echo $this->lang->line('brands');?> <i class="fa fa-angle-down float-right" ></i></h5>
                    </button>
                </div>
                <div class="col-sm-12">
                    <div id="brands" class="collapse show" >
                        <ul class="navbar-nav" id="brand_slider">
                            <?php 
                            foreach($brandData as $brand){
                                $newUrlLinkForBrand = explode("&",$brand_link); 
                                array_splice($newUrlLinkForBrand, 1, 1);
                                if(count($newUrlLinkForBrand) > 1){
                                    $newUrlLinkForBrand = implode("&",$newUrlLinkForBrand);
                                }else{
                                    $newUrlLinkForBrand = implode("",$newUrlLinkForBrand);
                                }
                               
                                if(isset($_GET['brands_search']) || (!isset($_GET['category_search']) && !isset($_GET['search']) ) ){
                                    $brand_link_revised = "?brands_search=".$brand->brand_id;
                                }else{
                                    $brand_link_revised = $newUrlLinkForBrand."&brands_search=".$brand->brand_id;
                                    
                                }
                                if(isset($_GET['brands_search']) && $brand->brand_id ==$_GET['brands_search'] ){
                                    if(strpos($current_link, "brands_search=$brand->brand_id&") !== false){
                                        $new_link = str_replace("brands_search=$brand->brand_id&","",$current_link);
                                    }else if(strpos($current_link, "&brands_search=$brand->brand_id") !== false){
                                        $new_link = str_replace("&brands_search=$brand->brand_id","",$current_link);
                                    }else if(strpos($current_link, "?brands_search=$brand->brand_id") !== false){
                                        $new_link = str_replace("?brands_search=$brand->brand_id","",$current_link);
                                    } 
                                    $brand_title = '<span title="Remove" class="float-right px-1 border border-dark">X</span>'.$brand->brand_name;
                                    $brand_path = $new_link;
                                }else{
                                    $brand_title = $brand->brand_name;
                                    $brand_path = $brand_link_revised;
                                }
                                if(!isset($_GET['brands_search'])){
                            ?>
                            <li class="nav-item">
                                <a class="nav-link brand-link" href="<?php echo $brand_path;?>"><?php echo $brand_title;?></a>
                            </li>
                            <?php }elseif(isset($_GET['brands_search']) && $_GET['brands_search'] == $brand->brand_id){ ?>
                                <li class="nav-item">
                                    <a class="nav-link brand-link" href="<?php echo isset($new_link)?$new_link:base_url()."store/".$sellerData[0]->store_slug?>"> <span title="Remove" class="float-right px-1 border border-dark">X</span> <?php echo $brand->brand_name;?></a>
                                </li>
                                <?php } 
                            }?> 
                            <div id="loadMore2"><h6  class="viewmore-heading" >VIEW MORE</h6></div>
                            <div id="showLess2"><h6  class="viewmore-heading" >SHOW LESS</h6></div>  
                        </ul>
                    </div>
                </div>
            </div>
            <hr>
            <?php }?>
            <div class="row">
                <div class="col-sm-12">
                <h5><?php echo $this->lang->line('sort_by');?></h5>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input sort_by" value="name-asc" data-from="sort" id="customCheckmobA" name="sort_by"
                                <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name-asc' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-radio-label" for="customCheckmobA"><?php echo $this->lang->line('a_z');?></label>
                            </div>
                        </div>         
                        <div class="col-sm-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input sort_by" value="name-desc" name="sort_by" data-from="sort" id="defaultCheckmobB"
                                <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name-desc' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-radio-label" for="defaultCheckmobB"><?php echo $this->lang->line('z_a');?></label>
                            </div>
                        </div>    
                        <div class="col-sm-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input sort_by" name="sort_by" value="price-asc" data-from="sort" id="defaultCheckmobC"
                                <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price-asc' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-radio-label" for="defaultCheckmobC"><?php echo $this->lang->line('low_high');?></label>
                            </div>
                        </div> 
                        <div class="col-sm-12">
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input sort_by" value="price-desc" name="sort_by" data-from="sort" id="defaultCheckmobD"
                                <?php if(isset($_GET['sort']) && $_GET['sort'] == 'price-desc' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-radio-label" for="defaultCheckmobD"><?php echo $this->lang->line('high_low');?></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
        <div class="col-sm-9">
            <?php
            if(!empty($productData)){
                $i = 0;
                $cartbtn= 0;
                $heartbtn=0;
            ?>
             <div class="row">
                <?php foreach($productData as $pd){?>
                <div class="col-sm-4 col-6 mb-custom">
                    <div class="polaroid">
                        <div class="image-center-parent">  
                        <?php
                            $ribbon = "";
                            $expireTo_feature = $pd['valid_to']; 
                            $expireFrom_feature =  $pd['valid_from']; 
                            $expire_time_to_feature = strtotime($expireTo_feature);
                            $expire_time_from_feature = strtotime($expireFrom_feature);
                            $discounted = discount_forumula($pd['price'], $pd['discount_value'], $pd['discount_type'], $pd['valid_from'], $pd['valid_to']); 
                            if($pd['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
                                if($pd['discount_type'] == "percent"){
                                    $ribbon = "<div class='ribbon ribbon-top-left'><span>".$pd['discount_value']."% OFF</span></div>";
                                }elseif($pd['discount_type'] == "fixed"){
                                    $ribbon = ($discounted)?"<div class='ribbon ribbon-top-left'><span>$".$pd['discount_value']."/- OFF</span></div>":"";
                                }
                            }
                        ?>
                        <?php if($pd['is_local'] == '1'){ ?>
                            <a href="<?php echo base_url().'product/'.$pd['slug']?>" class="btn cart-buttons ">
                            <img src="<?php echo product_thumb_path($pd['thumbnail'])?>" class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
                            <?php echo $ribbon; ?>
                            </a>
                        <?php }else if($pd['is_local'] == '0'){?>
                            <a href="<?php echo base_url().'product/'.$pd['slug']?>" class="btn cart-buttons "><img src="<?php echo $pd['thumbnail']?>" alt='<?php echo $pd['product_name'].' $'.number_format($discounted, 2);?>' class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
                            <?php echo $ribbon; ?>
                            </a>
                        <?php } else{?>
                            <a href="<?php echo base_url().'product/'.$pd['slug']?>" class="btn cart-buttons ">
                            <img src="<?php echo $pd['thumbnail']?>" alt="<?php echo assets_url('front/images/Preview.png');?>" class="img img-fluid mx-auto  my-auto image-center pro_img" data-i="<?php echo $i; ?>" > 
                            <?php echo $ribbon; ?>
                            </a>
                        <?php }?>
                        <?php if(!$this->isloggedin){ ?>
                                <a href="javascript:void(0)" data-product_variant_id="<?php echo $pd['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
                                <button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $pd['pv_id'];?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-product_id="<?php echo $pd['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
                            <?php }else if($this->isloggedin && $pd['seller_id'] != $user_id){?>
                                <a href="javascript:void(0)" data-product_variant_id="<?php echo $pd['pv_id']?>" class="btn cart-buttons btn-right addToCartBtn"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
                            <?php /*?><a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($p['product_name'],$p['product_id'])?>" class="btn cart-buttons " data-toggle="tooltip"   title="Product Details"><i class="far fa-eye"></i></a> <?php */?>
                            <?php if($this->isloggedin && isset($pd['already_saved']) && $pd['already_saved'] == ""){?>
                                    <button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $pd['pv_id'];?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-product_id="<?php echo $pd['product_id']?>" title="Save for later" data-toggle="tooltip" data-toggle="modal" data-target="#myModal3" ><i class="far fa-heart"></i></button>
                            <?php } else if($this->isloggedin && isset($pd['already_saved']) && $pd['already_saved'] != "0") { ?>
                                <span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
                            <?php }else{ ?>
                                    <button type="button" class="btn cart-buttons addToWishlistBtn btn-left" data-product_variant_id="<?php echo $pd['pv_id'];?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-product_id="<?php echo $pd['product_id']?>" title="Save for later" data-toggle="tooltip" ><i class="far fa-heart"></i></button>
                            <?php } }?>
                                <div class="col-sm-12 text-center position-absolute b-0">
                                <div style="display:inline-block;" class='rateYo' data-rateyo-rating="<?php echo ($pd['rating'])?$pd['rating']:0;?>"></div>
                            </div>
                        </div>
                        <div class="product-container ">
                            <div class="row text-center <?php /*?>fixed-bottom-text<?php */?>">
                                <div class="col-sm-12">
                                    <a href="<?php echo base_url().'product/'.$pd['slug']?>" title="<?php echo ($pd['product_name']);?>">
                                        <h4 class="zabee-product-name">
                                            <?php echo (strlen($pd['product_name']) > 20)?(substr($pd['product_name'],0,15))."...":($pd['product_name']); ?>
                                        </h4>
                                    </a>
                                </div>
                                <div class="col-sm-12 top-rated-product-price">                                
                                    $<?php echo ($discounted)?$this->cart->format_number($discounted):$this->cart->format_number($pd['price']);?>
                                    <?php if($pd['discount_value'] != "" && $expire_time_to_feature >= $today_time && $expire_time_from_feature <= $today_time){
                                        echo ($discounted)?"<strike>$".$this->cart->format_number($pd['price'])."</strike>":"";
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } } ?>   
            </div> 
        </div>
    </div>
    <?php if(($links["links"])){ ?>
        <div class="row">
            <div class="offset-sm-3 col-sm-9 pagination-div" >
                <ul class="pagination pull-right mt-2 flex-wrap">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <?php foreach($links['links'] as $page){ 
                                echo $page;
                            } ?>
                        </ul>
                    </nav>
                    <?php if($links["links"][0] != ""){ ?>
                    <li class="inputClass" >
                        <form id="goto-page-form" action="<?php echo base_url('product/listview_pagination');?>" method="post" >
                            <div class="input-group h-75">
                                <input type="hidden" name="range" value="<?php echo (isset($_GET['price_range'])) ? $_GET['price_range']:"" ?>" />
                                <input type="hidden" name="search" value="<?php echo (isset($_GET['search']))?$_GET['search']:"" ?>" />
                                <input type="hidden" name="brand_search" value="<?php echo (isset($_GET['brands_search']))?$_GET['brands_search']:"" ?>" />
                                <input type="hidden" name="name_search" value="<?php echo (isset($_GET['product_name']))?$_GET['product_name']:"" ?>" />
                                <input type="hidden" name="price_order" value="<?php echo (isset($_GET['min_price']))?$_GET['min_price']:"" ?>" />
                                <input type="hidden" name="shipping" value="<?php echo (isset($_GET['fs']))?$_GET['fs']:"" ?>" />
                                <input type="hidden" name="cat_search" value="<?php echo $this->input->get('category_search') ?>" />
                                <div class="offset-sm-3" id="resp-box-1">
                                    <div class="input-group-append" >
                                        <p style="width:40px; color: #969494 !important; margin: auto; text-align-last: justify;">Go To</p>
                                        <input type="number" name="page" min="1" id="page_number" style="width:50px; margin-left:10px;" />
                                        <button class="btn" id="goto-btn" type="submit" >Go</button>
                                    </div>
                                </div> 

                            </div>   
                        </form>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    <?php } ?>
</div>
<div class="clearfix"></div>
</div>