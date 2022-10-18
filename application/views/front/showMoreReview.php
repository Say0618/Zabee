<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<script src="<?php echo assets_url('front/js/jquery.rateyo.min.js');?>"></script>
<div class="container">
	<?php //$name = ;
		$this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>"All Reviews"), array("url"=>base_url("product/").$slug,"cat_name"=>$reviewData["result"][0]['product_name']))));
	?>
	<h3 class="p-3">Product Reviews</h3>
	<div class="row">
        <div class="col-12 ">
            <?php if(empty($reviewData)){
                        echo "No result found";
                    } else{ ?>
                <?php foreach($reviewData["result"] as $rd){
					// echo"<pre>"; print_r($rd); 
					if($rd['user_pic'] != ""){
						$img = explode('.', $rd['user_pic']);
						$img = $img[0]."_thumb.".$img[1];
						$img = profile_path("thumbs/".$img);
					}else{
						$img = assets_url("front/images/Preview.png");
					}
                    $breakreview = $rd['review'];
                ?>
                <div class="col-12 mb-4">
								<div class="row">
									<div class="col-sm-2 col-md-1 pb-2"><span class="user-review"><?php echo ucwords(($rd['fake'] == "1")?substr($rd['review_name'], 0, 3)."***":substr($rd['name'], 0, 3)."***"); ?></span></div>
									<div class="col-sm-3 col-md-3 pt-1"><div class='rateYo p-0' data-rateyo-rating='<?php echo $rd['rating'];?>'></div></div>
								</div>
								<div class="row">
									<div class="col-sm-2 col-md-1 mb-2">
										<?php  $imagePath = ($rd['fake'] != 1)?$img:assets_url("backend/images/defaultprofile.png"); ?>
										<?php  //echo profile_path("thumbs/".$img); ?>
										<img src="<?php echo $imagePath;?>" class="review-icon reviewImageHW img-fluid" alt="<?php echo image_url('Preview.png')?>">
									</div>
									<div class="col-sm-10 col-md-10 mb-2 user-review">
										<span class="small"><?php echo formatDateTime($rd['date'], false); ?></span><br>
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
										</span><br>
										<?php
										if(isset($rd['review_img']) && $rd['review_img'] != ""){
											if (strpos($rd['review_img'], ',' ) !== false ) {
											$review_images = explode(',', $rd['review_img']);
											foreach($review_images as $img){
												?>
												<img class="img-thumbnail" data-review="<?php echo $rd['review_id'] ?>" src="<?php echo image_url('review/thumbs/').$img ?>"  >
													
											<?php } } else{
												$review_images = $rd['review_img'];
											?>
											<img class="img-thumbnail" data-review="<?php echo $rd['review_id'] ?>" src="<?php echo image_url('review/thumbs/').$review_images ?>"  > 
											<?php }?>
											<div id="images-<?php echo $rd['review_id'] ?>" class="tab-pane mb-1 position-relative col-12 col-md-4 d-none">
												<button id="close-img-<?php echo $rd['review_id'] ?>" data-review="<?php echo $rd['review_id'] ?>" class="position-absolute close small cross-btn d-none">
													<span>x</span>
												</button>
												<img id="original_pic-<?php echo $rd['review_id'] ?>" class="p-4 img-fluid"/>
											</div>
											<?php } ?>
									</div>
								</div>
							</div>             
                            <?php } }  ?>   
                            
                <div class="clearfix"></div>
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

<script>
	$(document).ready(function(){
		$(".rateYo").rateYo({
			fullStar: true, 
			readOnly: true,
			starWidth: "20px",
			halfStar: true
		});
    });
    $(document).on('click touchstart',".readmore", function(event) {
			var txt = $(".more-content").is(':visible') ? 'Show more (+)' : 'Show less (–)';
			$(this).prev(".more-content").toggleClass("cg-visible");
			$(this).html(txt);
			event.preventDefault();
		});

    $(".img-thumbnail").click(function(){
        var link = $(this).attr('src');
        var id = $(this).data('review');
        console.log(id);
        link = link.replace('_thumb.','.');
        link = link.replace('/thumbs/','/');
        $("#original_pic-"+id).attr('src',link);
        $(".cross-btn").attr('style','right:0');
        $(".cross-btn").attr("href","javascript:void(0)");
        $("#close-img-"+id).removeClass("d-none").addClass( "d-block" );
        $("#images-"+id).removeClass("d-none").addClass( "d-block" );
    });

    $(".cross-btn").click(function(){
        var id = $(this).data('review');
        $("#images-"+id).removeClass("d-block").addClass( "d-none" );
        return false;
    });
</script>