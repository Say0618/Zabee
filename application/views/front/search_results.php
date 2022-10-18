<?php 
// $url = $_SERVER['QUERY_STRING'];
// $start = explode("?",$url);
// $url = parse_url($url);
// print_r($url);
// echo"<br>";
// $url = parse_str($url['path'],$parameter);
// unset($parameter["min_price"]);
// print_r($parameter);
// echo"<br>";
// print_r($url);
// echo"<br>";
// echo $start[0]."?".http_build_query($parameter);
// echo"<br>";
// die();
// echo"<pre>"; print_r($this->session->userdata()); die();
$today = date("Y-m-d");
$today_time = strtotime($today);
$this->found = (isset($_GET['search']) && $_GET['search'] !="")?'for "'.(trim($_GET['search'])).'"':"";
?>
<div class="container">
    <?php $this->load->view("front/bradcrumb");?>
    <div class="row py-3">
        <div class="col-sm-3 filter-column side">
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
                    <button type="button" class="btn collapse-btn" data-toggle="collapse" data-target="#categoriess" style="">
                        <h5 class="my-2"><?php echo $this->lang->line('categories');?> <i class="fa fa-angle-down float-right" ></i></h5>
                    </button>
                </div>
                <div class="col-sm-12">
                    <div id="categoriess" class="collapse show" >
                        <ul class="navbar-nav" id="category_slider">
                            <?php 
                            $see_all = ($this->input->get('see_all') == 1)?"see_all=1":"";
                            foreach($category as $cat){
                                $cat_title = $cat->category_name;
                                if($this->input->get('brands_search') != "" && $this->input->get('see_all') == "1"){
                                    $cat_path = base_url($cat->slug."?brands_search=".$_GET['brands_search']."&".$see_all);
                                }else{
                                    $cat_path = base_url($cat->slug."?".$see_all);
                                }
                                if(count($category) > 1){
                                    ?>
                                    <li class="nav-item">
                                    <a class="nav-link cat-link" href="<?php echo $cat_path;?>"><?php echo $cat_title;?></a>
                                    </li>
                                    <?php } else {
                                         //echo "<pre>";print_r($bradcrumbs);echo "</pre>";
                                         $parentLink = array_reverse($bradcrumbs);
                                         if(count($parentLink) > 1){
                                            array_pop($parentLink);
                                         }
                                         $slug = "";
                                         foreach($parentLink as $p){
                                            $slug .= strtolower($p['cat_name'])."-"; 
                                         }
                                         $slug = rtrim($slug,"-");
                                         if($this->input->get('brands_search')  != "" && $this->input->get('see_all') == "1"){
                                            $cat_path = base_url($slug."?brands_search=".$_GET['brands_search']."&".$see_all);
                                        }else{
                                            $cat_path = base_url($slug."?".$see_all);
                                        }
                                    ?>
                                        <li class="nav-item">
                                            <a class="nav-link cat-link" href="<?php echo $cat_path?>"> <span title="Remove" class="float-right px-1 border border-dark">X</span> <?php echo $cat->category_name;?></a>
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
                    <div class="d-flex flex-row bd-highlight w-100 my-3 price-row" >
                        <div class="">
                            <input type="text" name="price-from" class="form-control  minimumRange" placeholder="<?php echo $this->lang->line('min');?>" id="minimumRange1" required="" <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[0]); } ?>"  />
                        </div>
                        <div class="mx-2 dash-space" >-</div>
                        <input type="hidden" id="priceRange" readonly >
                        <div class="">
                            <input type="text" name="price-to" class="form-control  maximumRange" placeholder="<?php echo $this->lang->line('max');?>" id="maximumRange1" required=""<?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[1]); } ?>" />
                        </div>
                        <div class="px-2 range-search-button">
                            <a  class=" btn form-control go-btn" onclick="go(1)"><i class="fa fa-angle-right" ></i></a>
                        </div>
                    </div>
                    <span class="price-error d-none">Fill all fields first</span>
                </div>
            </div>
            <hr />

            <div class="row">
                <div class="col-sm-12">
                <h5>Shipping</h5>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input sort_by" value="1" data-from="fs" id="customCheckmobSA"
                                <?php if(isset($_GET['fs']) && $_GET['fs'] == '1' ){echo 'checked="checked"';}?> />
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
                    <button type="button" class="btn collapse-btn" data-toggle="collapse" data-target="#brandss" style="">
                        <h5 class="my-2"><?php echo $this->lang->line('brands');?> <i class="fa fa-angle-down float-right" ></i></h5>
                    </button>
                </div>
                <div class="col-sm-12">
                    <div id="brandss" class="collapse show" >
                        <ul class="navbar-nav" id="brand_slider">
                            <?php 
                            //echo"<pre>"; print_r($brandData); die();
                            foreach($brandData as $brand){
                                $newUrlLinkForBrand = explode("&",$brand_link); 

                                array_splice($newUrlLinkForBrand, 1, 1);
                                if(count($newUrlLinkForBrand) > 1){
                                    $newUrlLinkForBrand = implode("&",$newUrlLinkForBrand);
                                }else{
                                    $newUrlLinkForBrand = implode("",$newUrlLinkForBrand);
                                }
                            
                                if(isset($_GET['brands_search']) || (!isset($_GET['category_search']) && !isset($_GET['search']) ) ){
                                    $brand_link_revised = "?brands_search=".$brand->brand_id."&".$see_all;
                                }else{
                                    $brand_link_revised = $newUrlLinkForBrand."&brands_search=".$brand->brand_id."&".$see_all;
                                    
                                }

                                if(count($brandData) > 1){
                            ?>
                            <li class="nav-item">
                            <a class="nav-link brand-link" href="<?php echo $brand_link_revised;?>"><?php echo $brand->brand_name;?></a>
                            </li>
                            <?php }else{ if(strpos($current_link, "brands_search=$brand->brand_id&") !== false){
                                $new_link = str_replace("brands_search=$brand->brand_id&","",$current_link);
                                }else if(strpos($current_link, "&brands_search=$brand->brand_id") !== false){
                                    $new_link = str_replace("&brands_search=$brand->brand_id","",$current_link);
                                }else if(strpos($current_link, "?brands_search=$brand->brand_id") !== false){
                                    $new_link = str_replace("?brands_search=$brand->brand_id","",$current_link);
                                } 
                                ?>
                                <li class="nav-item">
                            <a class="nav-link brand-link" href="<?php echo isset($new_link)?$new_link:(isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:$current_link)?>"> <span title="Remove" class="float-right px-1 border border-dark">X</span> <?php echo $brand->brand_name;?></a>
                            </li>
                        <?php } }?> 
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
        <div class="col-sm-9 pr-sm-0">
            <div id="sort-by-row" >
                <div class="row">
                <div class="col-sm-3 my-auto d-sm-none">
            <div class="row">
                <?php if($detect->isMobile()){ ?>
                <div class="wrapper">
                <!-- Sidebar -->
                    <nav id="filter-sidebar">
                        <div class="pl-2 filter-header">
                            <div id="filterDismiss">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="d-inline-flex">
                                <div><span class="h_t_c"></span></div>
                                <span class="my-auto ml-2 f_s16">Filters</span>
                            </div>
                        </div>
                        <!-- FILTER BODY -->
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
                                            foreach($category as $cat){
                                                $cat_title = $cat->category_name;
                                                $cat_path = base_url($cat->slug);
                                                if(count($category) > 1){
                                                    ?>
                                                    <li class="nav-item">
                                                    <a class="nav-link cat-link" href="<?php echo $cat_path;?>"><?php echo $cat_title;?></a>
                                                    </li>
                                                    <?php } else { ?>
                                                        <li class="nav-item">
                                                            <a class="nav-link cat-link" href="<?php echo isset($new_link)?$new_link:base_url()."product"?>"> <span title="Remove" class="float-right px-1 border border-dark">X</span> <?php echo $cat->category_name;?></a>
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
                                    <div class="d-flex flex-row bd-highlight w-100 my-3 price-row" >
                                        <div class="">
                                            <input type="number" name="price-from" class="form-control minimumRange" placeholder="<?php echo $this->lang->line('min');?>" id="minimumRange1" required="" <?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[0]); } ?>"  />
                                        </div>
                                        <div class="mx-2 dash-space" >-</div>
                                        <input type="hidden" id="priceRange" readonly >
                                        <div class="">
                                            <input type="number" name="price-to" class="form-control maximumRange" placeholder="<?php echo $this->lang->line('max');?>" id="maximumRange1" required=""<?php if(isset($_GET['price_range'])){?> value="<?php print_r($minMax[1]); } ?>" />
                                        </div>
                                        <div class="px-2 range-search-button">
                                            <a  class=" btn form-control go-btn" onclick="go(1)"><i class="fa fa-angle-right" ></i></a>
                                        </div>
                                    </div>
                                    <span class="price-error d-none">Fill all fields first</span>
                                </div>
                            </div>
                            <hr />

                            <div class="row">
                                <div class="col-sm-12">
                                <h5>Shipping</h5>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input sort_by" value="1" data-from="fs" id="customCheckmobSA"
                                                <?php if(isset($_GET['fs']) && $_GET['fs'] == '1' ){echo 'checked="checked"';}?> />
                                                <label class="custom-control-label custom-checkout-label shipCheckBox ml-2" for="customCheckmobSA">Free</label>
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
                                            //echo"<pre>"; print_r($brandData); die();
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

                                                if(count($brandData) > 1){
                                            ?>
                                            <li class="nav-item">
                                            <a class="nav-link brand-link" href="<?php echo $brand_link_revised;?>"><?php echo $brand->brand_name;?></a>
                                            </li>
                                            <?php }else{ if(strpos($current_link, "brands_search=$brand->brand_id&") !== false){
                                                $new_link = str_replace("brands_search=$brand->brand_id&","",$current_link);
                                                }else if(strpos($current_link, "&brands_search=$brand->brand_id") !== false){
                                                    $new_link = str_replace("&brands_search=$brand->brand_id","",$current_link);
                                                }else if(strpos($current_link, "?brands_search=$brand->brand_id") !== false){
                                                    $new_link = str_replace("?brands_search=$brand->brand_id","",$current_link);
                                                } 
                                                ?>
                                                <li class="nav-item">
                                            <a class="nav-link brand-link" href="<?php echo isset($new_link)?$new_link:base_url()."product/searchResults"?>"> <span title="Remove" class="float-right px-1 border border-dark">X</span> <?php echo $brand->brand_name;?></a>
                                            </li>
                                        <?php } }?> 
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
                    </nav>
                <!-- Page Content -->
                </div>
                <?php } ?>
                <div class="wrapper">
                <!-- Sidebar -->
                    <nav id="minicart-sidebar"></nav>
                <!-- Page Content -->
                </div>
            </div>
        </div>
                    <?php if($itemsFound > 0 ){ 
                       $msg = $itemsFound." item(s) found ".$this->found;
                    //    $class = "col-sm-8";
                    }else{
                        $this->found = substr($this->found, 4);
                        $msg = "This item is not available right now want to make a request for it?? <a href='javascript:void(0)' onclick='requestModal(".$this->found.")'>click here</a>";
                        // $class = "col-sm-8";
                    }
                    ?>
                    <div class="col-8" id="item-found"><a class="d-sm-none pr-2" href="javascript:void(0)" id="filterSidebarCollapse"><i class="fas fa-align-justify"></i></a> <?php echo $msg ?></div>
                    <div class="col-4">
                        <div class="float-right" >
                            <button class="btn btn-primary-outline gridButton" onclick="sendingViewStatus('grid')" ><i class="fa fa-th-large"></i></button>
                            <button class="btn btn-primary-outline listButton active" onclick="sendingViewStatus('list')" ><i class="fa fa-bars"></i></button> 
                        </div>
                    </div>
                </div>
            </div>
            <?php (!isset($_SESSION['view']) || ($_SESSION['view'] == "list"))?$this->load->view("front/search_list"):$this->load->view("front/search_grid"); ?>
        </div>
    </div>
    <?php if(($links["links"])){ ?>
        <div class="row">
            <div class="offset-sm-3 col-sm-9 pagination-div" >
                <ul class="pagination pull-right mt-2">
                    <?php foreach($links['links'] as $page){ 
                        echo $page; echo "&nbsp;&nbsp;&nbsp;";
                    } ?>
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
                                    <input type="number" id="page_number" min="1" name="page" style="width:50px; margin-left:10px;" />
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

<div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
            <form id="request-form" novalidate="novalidate" method="post" action="#">
                <div class="modal-header">
                    <h5 class="modal-title">Request Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-content">
                        <div class="col-lg-12 col-sm-12">
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('first_name');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('first_name');?>" id="first_name" name="first_name" value="<?php if($this->session->userdata("firstname") != "") echo $this->session->userdata("firstname");?>" type="text" required />
                                </div>
                                <div class="errorTxt"></div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('last_name');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('last_name');?>" name="last_name" id="last_name"  value="<?php if($this->session->userdata("lastname") != "") echo $this->session->userdata("lastname");?>" type="text" required>
                                </div>
                                <div class="errorTxt"></div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('email');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('email_address');?>" name="email" id="email" type="email" required  value="<?php if($this->session->userdata("email") != "") echo $this->session->userdata("email");?>">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('contact');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('enter_contact_no');?>" name="contact" id="contact"  type="text"  value="<?php if($this->session->userdata("mobile") != "") echo $this->session->userdata("mobile");?>">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('price');?>:</label>
                                <div class="input-group">
                                    <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('desired_price');?>" name="price" id="price"  type="text">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('condition');?>:</label>
                                <div class="input-group">
                                <select class="form-control" id="condition" name="condition">
                                    <option value="new"><?php echo $this->lang->line('new');?></option>
                                    <option value="used"><?php echo $this->lang->line('used');?></option>
                                    <option value="both"><?php echo $this->lang->line('both');?></option>
                                </select>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="form-control-label resposive-label" ><?php echo $this->lang->line('description');?>:</label>
                                <div class="input-group">
                                    <textarea class="form-control input-lg" id="description" name="description" rows="3" cols="70"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="input-group">
                            <input id="userid" type="hidden"  value="<?php if($this->session->userdata("userid") != "") echo $this->session->userdata("userid");?>">
                            <input id="query" type="hidden"  value="<?php echo (isset($_GET['search']) && $_GET['search'] != "") ? $_GET['search'] : "" ?>">
                        </div>
                    </div>				
                </div>
                <div class="modal-footer">
                    <input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="confirm_request"/>
                    <a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
                </div>
            </form>
		</div>
	</div>
</div>

<div class="modal fade" id="request-complete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="title"></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<p id="msg"></p>
			</div>				
		  </div>
		</div>
	</div>
</div>