<div class="container">
    <div class="col-12 mt-4 pl-0 d-none d-md-block">
        <form class="header-centeralised" action="<?php echo base_url().'product/SearchResults'?>" method="get" name="search_form" id="search_form">
               <div class="input-group">
                    <div class="input-group col-sm-12 pl-0">
                        <input class="form-control rounded-0 pageSearch" type="search" value="<?php echo (isset($_GET['search']) && $_GET['search'] !="")?trim($_GET['search']):""?>" placeholder="Best Headphone" id="searchBar" name="search"  >
                        <div class="input-group-append searchCross">
                            <button class="btn crossBtn" type="button" id="crossBtn">x</button>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-secondary btn-lg searchbtn rounded-0 border-0" type="submit">Search</button>
                        </div>
                        </span>
                    </div>
                </div>
            </form>
        </div>
        
            <nav class="row collapse navbar-collapse mt-auto navbar-light p-3" id="sideNav">
            <div class="row">
        <div class="col-12">
            <div class="mb-2">
                <span id="filter-refine-cross" class="clear-filter">X</span>
                <span id="filter-refine-heading">Filter & Refine</span>
            </div>
                    <div class="clearfix"></div>
        <!-- side-nav -->
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a class="panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            Category
                            </a>
                        </h4>
                    </div>
        <div id="collapse1" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <ul class="navbar-nav">
                                        <div>
                                            <?php $i=1;
                                                $current_link = $_SERVER['REQUEST_URI'];
                                                $url=strtok($_SERVER["REQUEST_URI"],'?');
                                                $all_link = current_url();
                                                $brandCount = count($brandData);
                                                if(isset($_GET['page'])){}
                                                if(isset($_GET['search'])){
                                                $category_link = $current_link."&";
                                                $brand_link = $current_link."&";
                                                }
                                                else{
                                                    $category_link = $current_link."?";
                                                    $brand_link = $current_link."?";
                                                }
                                                if(empty($category)){
                                                    echo "No category Found";
                                                }
                                                else{
                                                    foreach($category as $key=>$value){?>
                                                    <li class="nav-item">
                                                        <a class="nav-link cat-link"  href="<?php echo $url."?"."category_search=".$key?>"><?php echo $value;?></a>
                                                    </li>
                                            <?php } }?> 
                                        </div>              
                                        </ul>
        </div>
        </div>                               
        <hr class="m-0">                 
                                    <div class="clearfix"></div>

                                  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
          Brand
        </a>
      </h4>
    </div>
    <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <ul class="navbar-nav">
                                <div>					
                                    <?php $i=1; 
                                        if(empty($brandData)){
                                            echo "No Brand Found";
                                            }
                                    foreach($brandData as $key=>$value){
                                    ?>
                                        <li class="nav-item">
                                        <a class="nav-link brand-link" href="<?php echo $brand_link."&brands_search=".$key?>"><?php echo $value;?></a>
                                    </li>
                                            <?php
                                        $i++; }?>
                                <div>
                                </ul>
</div>
</div>
</div>
<hr class="m-0">
                                <div class="clearfix"></div>
                                
                                <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingSix">
      <h4 class="panel-title">
        <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
            Tag
        </a>
       
      </h4>
    </div>
    <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
                <div class="row">    
                    <div class="col">
                        <input name="" class="form-control" placeholder="Enter Tags" id="">
                    </div>
                </div>
                </div>
                </div>
                <hr class="m-0">
                        <div class="clearfix"></div>        
<!-- range slider --> 
<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title">
        <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
            Price
        </a>
      </h4>
    </div>
    <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                <div class="row">
                <?php if(isset($_GET['price_range'])){ 
                    $minMax = (explode("-",$_GET['price_range'])); } ?>
                    <div class="col-4">
                        <span class="price-span">$</span> 
                        <input name="price-from" class="form-control range-input minimumRange" placeholder="Min" id="minimumRange2" required=""
                        <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[0]); } ?>" />
                    </div>
                            <input type="hidden" id="priceRange" readonly >
                    <div class="col-4">
                        <span class="price-span">$</span> 
                        <input name="price-to" class="form-control range-input maximumRange" placeholder="Max" id="maximumRange2" required=""
                        <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[1]); } ?>" />
                    </div>
                    <div class="col-3 go-div">
                        <a style="color:#fff" class=" btn form-control go-btn btn-hide" onclick="go(2)">></a>
                    </div>
                </div>
                </div>
                </div>
                <hr class="m-0">
                <div class="clearfix"></div>
            <div class="mt-0">
            <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFour">
        <h4 class="panel-title">
            <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
                Sort By
            </a>
        </h4>
    </div>
    <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
            <div class="row">
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input sort_by" value="asc" data-from="product_name" id="customCheckmobA" name="example1"
                                <?php if(isset($_GET['product_name']) && $_GET['product_name'] == 'asc' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customCheckmobA">A-Z</label>
                            </div>
                    </div>            
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input sort_by" type="checkbox" value="desc" data-from="product_name" id="defaultCheckmobB"
                    <?php if(isset($_GET['product_name']) && $_GET['product_name'] == 'desc' ){echo 'checked="checked"';}?> />
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheckmobB">Z-A</label>
                    </div>
                </div>    
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input sort_by" type="checkbox" value="asc" data-from="min_price" id="defaultCheckmobC"
                    <?php if(isset($_GET['min_price']) && $_GET['min_price'] == 'asc' ){echo 'checked="checked"';}?> />
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheckmobC">Lowest to Highest</label>
                    </div>
                </div>

                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input sort_by" type="checkbox" value="desc" data-from="min_price" id="defaultCheckmobD"
                    <?php if(isset($_GET['min_price']) && $_GET['min_price'] == 'desc' ){echo 'checked="checked"';}?> />
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheckmobD">Highest to Lowest</label>
                    </div>
                </div>
            </div>
    </div>
    </div>
    </div>
    <hr class="m-0">
                    <div class="clearfix"></div>


                      <div class="">
            <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFive">
        <h4 class="panel-title">
            <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse6">
                Sales
            </a>
           
        </h4>
    </div>
    <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingfive">
        <div class="row">
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" class="custom-control-input" id="customCheckOne" name="example1">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customCheckOne">Low</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" value="desc" data-from="product_name" id="customCheck2">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customCheck2">Medium</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" value="asc" data-from="min_price" id="customCheck3">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customCheck3">High</label>
                   </div>
                </div>
                <div class="col-6">
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" value="decs" data-from="min_price" id="customCheck4">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customCheck4">Top Sales</label>
                    </div>
                </div>
        </div>
    </div>
    </div>
    </div>
                    <!-- end -->
            </nav>
            <div class="d-sm-none row navbar-light  filterBtnDiv" id="filterBtnDiv" >
                    <div class="col-12 text-center p-2" id="filterInnerDiv">Advance search</div>
            </div>
                <div class="text-center">
                <button type="button" class="d-sm-none btn btn-default btn-circle navbar-toggler btn-xl" id="semiBtn" data-toggle="collapse" data-target="#sideNav" aria-controls="sideNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-angle-down navbar-toggler" data-toggle="collapse" data-target="#sideNav" aria-controls="sideNav" aria-expanded="false" aria-label="Toggle navigation"></i>
                </button>
                </div>
            
    <div class="row mt-5">
        <div class="col-lg-3 col-md-3 col-12 d-none d-md-block">
            <div class="mb-2">
                <span id="filter-refine-cross" class="clear-filter">X</span>
                <span id="filter-refine-heading">Filter & Refine</span>
            </div>
                    <div class="clearfix"></div>
        <!-- side-nav -->
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a class="panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Category
                            </a>
                        </h4>
                    </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
            <ul class="navbar-nav">
                                        <div>
                                            <?php $i=1;
                                                $current_link = $_SERVER['REQUEST_URI'];
                                                $url=strtok($_SERVER["REQUEST_URI"],'?');                           
                                                $all_link = current_url();                                           
                                                $brandCount = count($brandData);
                                                if(isset($_GET['page'])){}
                                                if(isset($_GET['search'])){
                                                $category_link = $current_link."&";
                                                $brand_link = $current_link."&";
                                                }
                                                else{
                                                    $category_link = $current_link."?";
                                                    $brand_link = $current_link."?";
                                                }
                                                if(empty($category)){
                                                    echo "No category Found";
                                                }
                                                else{
                                                    foreach($category as $key=>$value){?>
                                                    <li class="nav-item">
                                                        <a class="nav-link cat-link"  href="<?php echo $url."?"."category_search=".$key?>"><?php echo $value;?></a>
                                                    </li>
                                            <?php } }?> 
                                        </div>              
                                        </ul>
        </div>
        </div>                               
        <hr class="m-0">                 
                                    <div class="clearfix"></div>

                                  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingTwo">
      <h4 class="panel-title">
        <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Brand
        </a>
        
      </h4>
     
    </div>
    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                                    <ul class="navbar-nav">
                                <div>					
                                    <?php $i=1; 
                                        if(empty($brandData)){
                                            echo "No Brand Found";
                                            }
                                    foreach($brandData as $key=>$value){
                                    ?>
                                        <li class="nav-item">
                                        <a class="nav-link brand-link" href="<?php echo $url."?"."brands_search=".$key?>"><?php echo $value;?></a>
                                    </li>
                                            <?php
                                        $i++; }?>
                                <div>
                                </ul>
</div>
</div>
</div>
<hr class="m-0">
                                <div class="clearfix"></div>
                                
                                <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingSix">
      <h4 class="panel-title">
        <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
            Tag
        </a>
       
      </h4>
    </div>
    <div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
                <div class="row">    
                        <div class="col">
                        <input class="form-control keywords mb-3" multiple="multiple" type="text" name="tags" id="tag" data-role="tagsinput"
                        <?php if(isset($_GET['keywords'])){?> value="<?php echo $_GET['keywords']; }?>" />
                        </div>
                </div>
                </div>
                </div>
                <hr class="m-0">
                <div class="clearfix"></div>
                                
<!-- range slider --> 
<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingThree">
      <h4 class="panel-title">
        <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            Price
        </a>
        
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                <div class="row desktop mb-3">    
                    <?php if(isset($_GET['price_range'])){ 
                    $minMax = (explode("-",$_GET['price_range'])); } ?>
                    <div class="col-4 pr-0">
                        <span class="price-span">$</span> 
                        <input name="price-from" class="form-control range-input minimumRange" placeholder="Min" id="minimumRange1" required=""
                        <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[0]); } ?>" />

                    </div>
                            <input type="hidden" id="priceRange" readonly >
                    <div class="col-4 pr-0">
                        <span class="price-span">$</span> 
                        <input name="price-to" class="form-control range-input maximumRange" placeholder="Max" id="maximumRange1" required=""
                        <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[1]); } ?>" />
                    </div>
                    <div class="col-4 go-div" >
                        <a style="color:#fff" class=" btn form-control go-btn btn-hide" onclick="go(1)">GO</a>
                    </div>
                </div>
                </div>
                </div>
                <hr class="m-0">
                <div class="clearfix"></div>
            <div class="mt-0">
            <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFour">
        <h4 class="panel-title">
            <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                Sort By
            </a>
        </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                    <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input sort_by" value="asc" data-from="product_name" id="customChecka" name="example1"
                                <?php if(isset($_GET['product_name']) && $_GET['product_name'] == 'asc' ){echo 'checked="checked"';}?> />
                                <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customChecka">A-Z</label>
                            </div>
                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input sort_by" type="checkbox" value="desc" data-from="product_name" id="defaultCheckb"
                    <?php if(isset($_GET['product_name']) && $_GET['product_name'] == 'desc' ){echo 'checked="checked"';}?> />
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheckb">Z-A</label>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input sort_by" type="checkbox" value="asc" data-from="min_price" id="defaultCheckc"
                    <?php if(isset($_GET['min_price']) && $_GET['min_price'] == 'asc' ){echo 'checked="checked"';}?> />
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheckc">Lowest to Highest</label>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input sort_by" type="checkbox" value="desc" data-from="min_price" id="defaultCheckd"
                    <?php if(isset($_GET['min_price']) && $_GET['min_price'] == 'desc' ){echo 'checked="checked"';}?> />

                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheckd">Highest to Lowest</label>
                    </div>
                    
    </div>
    </div>
    </div>
    <hr class="m-0">
                    <div class="clearfix"></div>

                      <div class="">
            <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingFive">
        <h4 class="panel-title">
            <a class="collapsed panel-link" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                Sales
            </a>
           
        </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingfive">
                    <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="customCheck1" name="example1">
                                <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="customCheck1">Low</label>
                            </div>

                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" value="desc" data-from="product_name" id="defaultCheck1">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheck1">Medium</label>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" value="asc" data-from="min_price" id="defaultCheck2">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheck2">High</label>
                    </div>

                    <div class="custom-control custom-checkbox mb-3">
                    <input class="custom-control-input" type="checkbox" value="decs" data-from="min_price" id="defaultCheck3">
                    <label class="custom-control-label custom-checkbox-label ml-2 sortCheckBox" for="defaultCheck3">Top Sales</label>
                    </div>
    </div>
    </div>
    </div>
   
    <div id = "loader" style="display: none;">
        <center>
            <img src = "<?php echo assets_url('front/images/preloader.gif'); ?>" />	
            <span style="color:#6ca2cc">Loading Products</span>
        </center> 
    </div>
                    <!-- end -->
        <!-- end of side nav -->
        </div>
        
        <div class="col-lg-8 col-md-8 offset-md-1 offset-lg-1 col-12">
                <?php if(empty($productData)){
                        echo "No result found";
                        }  
                        else{
                ?>
                <?php  $i = 0;
            $cartbtn= 0;
            $heartbtn=0;?>
            
                <?php foreach($productData as $pd){
                            $varientAttribute = array();
                                if($pd['variant']!=""){
                                    $varientAttribute_info = $pd['variant'] ;
                                    $p=explode(',', $varientAttribute_info) ;
                                    foreach ($p as $property) {  
                                        $part = explode(':', $property);
                                            if(isset($varientAttribute[$part[0]])){ 
                                            $varientAttribute[$part[0]] =  $varientAttribute[$part[0]].", ".$part[1];
                                                    }
                                            else{
                                                $varientAttribute[$part[0]] =  $part[1];
                                                }
                                            }
                                        }
                                if($pd['is_local_product_img'] == 1){ 
                                    if($pd['is_primary']){
                                        $img = product_thumb_path($pd['is_primary']);
                                    }else{
                                        $img = assets_url('front/images/Preview.png');
                                    }
                                }else{
                                    $img =$pd['is_primary'];
                                }
                                $product_link = urlClean( $pd['product_name']);
                                $product_link=str_replace("(","", $product_link);
                                $product_link=str_replace(")","", $product_link);   
                                    ?>
                                        
                                    <div class="col-12 product-row mb-3" style="" >
                                    <div class="row">
                                        <div class="col-sm-3 col-4">
                                         <a href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($pd['product_name'],$pd['product_id'])?>" class="text-center" target="_new">
                                         <img src="<?php echo $img;?>" alt="<?php echo $pd['product_name']?>" class="img img-fluid mx-auto pdImage" data-i="<?php echo $i; ?>"  data-toggle="tooltip"  title="<?php echo $pd['product_name']; ?>"> 
                                         </a>
                                        </div>
                                        <div class="col-sm-9 col-8 pdBox">
                                                <div class="row">
                                                        <a class="product-title wordwrap d-none d-sm-block" href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($pd['product_name'],$pd['product_id'])?>" data-toggle="tooltip" title="<?php echo $pd['product_name']?>"  target="_new">
                                                        <p><?php echo (strlen($pd['product_name']) > 30)?(substr($pd['product_name'],0,40)."..."):(trim($pd['product_name']));?> </p>
                                                        </a>
                                                        <a class="product-title wordwrap d-block d-sm-none" href="<?php echo base_url().'product/detail/'.$product_link.'-'.encodeProductID($pd['product_name'],$pd['product_id'])?>" data-toggle="tooltip" title="<?php echo $pd['product_name']?>"  target="_new">
                                                        <p><?php echo (strlen($pd['product_name']) > 25)?(substr($pd['product_name'],0,25)."..."):(trim($pd['product_name']));?> </p>
                                                        </a>
                                                </div>
                                                <?php if($pd['product_description']){?>
                                                <div class="row product-description">
                                                     <?php echo (strlen($pd['product_description']) > 150)?trim(substr(strip_tags($pd['product_description']),0,150))."...":trim(strip_tags($pd['product_description'])); ?>
                                                </div>
                                                <?php }?>
                                                <div class="row btnsRow">    
                                                                <span class="PriceTag">$<?php echo ($pd['min_price']==$pd['max_price'])?$pd['max_price']:$pd['min_price'].' - $'.$pd['max_price'];?></span>
                                                                
                                                                <?php 
                                                             $user_id = (isset($_SESSION['userid']) && $_SESSION['userid'] !="")?$_SESSION['userid']:"";	
                                                            ?>    
                                                                
                                                             <?php if(!$this->isloggedin){ ?>
                    	                                        <a href="<?php echo base_url('cart/addtocart/'.$pd['pv_id']); ?>" class="btn cart-buttons"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
                                                                     <?php }else if($this->isloggedin && $pd['seller_id'] != $user_id){?>
                    	                                                <a href="<?php echo base_url('cart/addtocart/'.$pd['pv_id']); ?>" class="btn cart-buttons"  title="Add to cart" data-toggle="tooltip" ><i class="fa fa-shopping-cart"></i></a> 
                                                                        <?php }?>
                                                                 <?php if($pd['already_saved']==""){?> 
                                                                <a class="addToWishlistBtn" id="addToWishlistBtn" title="Save for later" data-product_varient_id="<?php echo $pd['pv_id'];?>" data-product_id="<?php echo $pd['product_id']?>" data-id = "<?php echo $pd['product_id']."-".$pd['pv_id']?>" data-toggle="modal" data-target="#myModal3"><i class="far fa-heart"></i></a>
                                                                <?php } else {?>
                                                                    <span class="already-saved btn" style="padding-top: 15px;" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>
                                                                <?php } ?>
                                                              
                                                            </div>
                                                </div>
                                                <div class="clearfix"></div>
                    </div>
                                               <?php $i++;?>
                                              </div> 
                                              
                                    <?php } } ?>   
                                  
                                <?php
                                            if(($links["links"])){
                                                    ?>
                                                    <div class="clearfix"></div>
                                            <div class="col-lg-6 offset-lg-6 col-12">
                                                <ul class="pagination pull-right mt-5">
                                                
                                                <?php foreach($links['links'] as $page){ 
                                                    echo $page;
                                                } ?>
                                                </ul>

                                            </div>
                                            <?php
                                            }
                                        ?>
                   </div>
                                    <div class="clearfix"></div>
<div id="myModal3" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Wish List via Category Name</h4>
		<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		<form id="myform" name="myForm" role="form">
			<div class = "" id = "">
			<?php if($wishlist_categories){ ?>
				<label><h6>Categories: </h6><label>&nbsp;
				<select name='alreadyExistingCategories' id='alreadyExistingCategories' class='alreadyExistingCategories'>
					<?php for($i = 0; $i < count($wishlist_categories); $i++){ ?>
					<option value="<?php echo $wishlist_categories[$i]->id ?>"><?php echo $wishlist_categories[$i]->category_name ?></option>
					<?php } ?>
				</select>
			<?php } else {?>	
				<label><h6>Categories: </h6><label>&nbsp;
				<select class='alreadyExistingCategories'>
				  <option value="0" selected disabled>none available.</option>
				</select>
			<?php } ?>			
			</div>
			<input type="hidden" name="modal_product_id" id="modal_product_id" value="">
			<input type="hidden" name="modal_product_v_id" id="modal_product_v_id" value="">
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success  ml-3" id="Submit">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
