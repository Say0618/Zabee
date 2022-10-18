<!-- container -->
<div class="sort-header">
	<div class="row">
        <label for="sort" class="control-label sort-label">Sort by:</label>
            <div class="col-sm-3">
               <select name = 'sort_by' onchange = "sortProducts(this);return false;" id="sort_by" class="form-control">
                <option  value = "">-Select Sort By-</option>
                  <option value = "product_name=asc">A - Z</option>
                  <option value = "product_name=desc">Z - A</option>					              
              <option value = "min_price=asc">Lowest to Highest</option>
              <option value = "min_price=desc">Highest to Lowest</option>					              
            </select>
        </div>
    </div>
    <div id = "loader" style="display: none;">
        <center>
            <img src = "<?php echo assets_url("front/images/preloader.gif"); ?>" />	
            <span style="color:#6ca2cc">Loading Products</span>
        </center>
    </div>
</div>
<div class="page"> 
<div class="side-nav"><div class= "quantHead4" style="color:grey">
							<label>&nbsp;Categories</label>
						<div class="thirdCol">
							<div class="otherSeller" >
								<div class= "diffSellers" >
									<?php $i=1; 
									
										$current_link = $_SERVER['REQUEST_URI'];
										$url=strtok($_SERVER["REQUEST_URI"],'?');
										$all_link = current_url();
										$catCount = count($categoryData);
										$brandCount = count($brandData);
										if(isset($_GET['category_search'])){
											$category_link = str_replace('&category_search='.$_GET['category_search'], '', $_SERVER['QUERY_STRING']);
										
											$category_link = $url."?".$category_link;
										}else{
											$category_link = $current_link;
										}
										if(isset($_GET['brands_search'])){
											$brand_link =str_replace('&brands_search='.$_GET['brands_search'], '', $_SERVER['QUERY_STRING']);
											$brand_link = $url."?".$brand_link;
										}else{
											$brand_link = $current_link;
										}
										foreach($categoryData as $cd){
											if($cd->category_name !=""){
											?>
                                    	<a href="<?php echo $category_link."&category_search=".$cd->category_id?>"><?php echo $cd->category_name;?></a><?php echo ($catCount !=$i)?'<hr style="margin-left:0px; margin-bottom:5px;" />':"";?><!--<br />-->
                                    <?php }$i++; }?>
								</div>
							</div>
						</div>
					</div>
                    <div class="hidden-xs" style="padding-top:10px">
						<div class= "quantHead4" style="color:grey">
							<label>&nbsp;Brands</label>
						</div>
						<div class="thirdCol">
							<div class="otherSeller" >
								<div class= "diffSellers" >
									<?php $i=1; 
										foreach($brandData as $bd){
											if($bd->brand_name !=""){
									?>
                                    	<a href="<?php echo $brand_link."&brands_search=".$bd->brand_id?>"><?php echo $bd->brand_name;?></a><?php echo ($brandCount !=$i)?'<hr style="margin-left:0px; margin-bottom:5px;" />':"";?><!--<br />-->
                                    <?php }$i++; }?>
								</div>
							</div>
						</div>
                    <div id="narrow-results" class="collapse hidden-sm hidden-md hidden-lg">
                        <div class= "quantHead4" style="color:grey">
                            <h4>&nbsp;Categories</h4>
                        </div>
                        <div class="thirdCol" style="border-left:none; border-top:none; width:220px;" >
                            <div class="otherSeller" >
                                <div class= "diffSellers" >
                                   <?php $i=1; 
										
										foreach($categoryData as $cd){
											if($i%2==0){echo '<hr style="margin-left:0px; margin-bottom:5px;" />';}
											?>
                                    	<a href=""><?php echo $cd->category_name;?></a><br />
                                    <?php $i++; }?>
                                </div>
                            </div>
                        </div>
                        <div class= "quantHead4" style="color:grey">
                            <h4>&nbsp;Brands</h4>
                        </div>
                        <div class="thirdCol" style="border-left:none; border-top:none; width:220px;" >
                            <div class="otherSeller" >
                                <div class= "diffSellers" >
                                    <?php $i=1; 
										foreach($brandData as $bd){
											if($i%2==0){echo '<hr style="margin-left:0px; margin-bottom:5px;" />';}?>
                                    	<a href=""><?php echo $bd->brand_name;?></a><br />
                                    <?php $i++; }?>
                                </div>
                            </div>
                        </div>
                    </div>
				</div></div>
                <div class="grid_view"><h4> <label style="wordwrap">Top Selling Products:</label></h4>
                    <div id="blockView_ul"></div>
                    <div class="load-more text-center">
                    	<div class="product_list normal_list list-group list-group-horizontal" style="border:none !important"></div>	
                    	<div class="clearfix"></div>	
                        <a href="javascript:void(0)" onclick="loadMoreProduct()" class="btn btn-info d-none" id="load-more-product">Load More Product</a>
                    </div>
                </div>
                <div class="clearfix"></div>
			</div>	
        </div>	
	</div>
	<hr class="soft"/></div>
</div>
<form method="post" action="<?php echo base_url()?>" id="formSubmit">
<input type="hidden" id="pv_id" value="" name="pvid" />
<input type="hidden" id="qty" value="" name="qty" />
<input type="hidden" value="0" id="product_count"  />
</form>