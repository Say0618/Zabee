<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<link rel="stylesheet" href="<?php echo assets_url('plugins/lightslider-master/css/lightslider.css'); ?>">
<link rel="stylesheet" href="<?php echo assets_url('plugins/image-modal/ekko-lightbox.css')?>">
<script src="<?php echo assets_url();?>front/js/jzoom.js"></script> 
<script src="<?php echo assets_url();?>front/js/jquery.rateyo.min.js"></script>
<script type="application/javascript" src="<?php echo assets_url('front/js/velocity.min.js'); ?>"></script>
<script type="application/javascript" src="<?php echo assets_url('front/js/velocity.ui.min.js'); ?>"></script>
<script type="application/javascript" src="<?php echo assets_url('front/js/formatCurrency.js'); ?>"></script>
<script type="application/javascript" src="<?php echo assets_url('plugins/image-modal/ekko-lightbox.min.js')?>"></script>
<script type="application/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5ef595ffb900a200126bbfd6&product=sop"></script>
<script>
	var shipping_id = "";
	var shipping_title = "";
	var shipping_price = "";
	function changeShippingMethod(){
		shipping_id = $(".shipping_method:checked").attr('data-shipping_id');
		shipping_title = $(".shipping_method:checked").attr('data-title');
		// console.log(shipping_title);
		// shipping_price = $(".shipping_method:checked").attr('data-price');
		var price="";
		// if(shipping_price == 0){
			//price = "Free Shipping";
			//shipping_title = "";
			// label = "";
			// shipping_title = "Free Shipping";
		// }else{
			//price = "US $"+shipping_price;
			//shipping_title = " via "+shipping_title;
			label = "Shipped by:";
		// }
		//$("#shipping_method").html(' <a href="javascript:void(0)" class="bold-title" data-toggle="modal" data-target="#choose_shipping_method"><span class="price">'+price+'</span> '+shipping_title+'</a>');
		$("#shipping_method").html(label+' <a href="javascript:void(0)" class="bold-title" data-toggle="modal" data-target="#choose_shipping_method">'+shipping_title+'</a>');
	}
	$(document).on("change",'.shipping_method',function(){
		changeShippingMethod();
	});
	$(document).ready(function(e) {
		// console.log($(".st-btn[data-network=facebook]").attr("data-url", "google"));
		$('#lightSlider').lightSlider({
			gallery:true,
			//adaptiveHeight:true,
			item:1,
			//loop:true,
			//auto:true,
			thumbItem:5,
			//slideMargin:0,
			enableDrag: true,
			currentPagerPosition:'left',
			pauseOnHover: true,
			onBeforeSlide: function (el) {
				
			}
		});
		var hr_padding_top = $(".color-black").height();
		hr_padding_top = 4+hr_padding_top;
		$("#rightSideBarHr").css("margin-top",hr_padding_top+"px");
		var top = $(".short-description").height();
		if(top >= 30){
			top = 60+top;
		}else{
			top = 60;
		}
		$("#right-btn").css("top",top+"px");

		/*if ($(".other-product")[0]){
			//alert('exists');
		} else {
			$('.first-column-bottom').hide();
			$('.second-description').show();
		}*/
		changeShippingMethod();
		$('.price').formatCurrency();
		//$('.other-seller-next').click(function(){ $('.other-sellers-offers').carousel('next');return false; });
    	//$('.other-seller-prev').click(function(){ $('.other-sellers-offers').carousel('prev');return false; });
		
		$('.product-accessories-next').click(function(){ $('.product-accessories').carousel('next');return false; });
		$('.product-accessories-prev').click(function(){ $('.product-accessories').carousel('prev');return false; });
		
		$('.more-seller-next').click(function(){ $('.more-from-this-sellers').carousel('next');return false; });
		$('.more-seller-prev').click(function(){ $('.more-from-this-sellers').carousel('prev');return false; });
		$('[data-toggle="tooltip"]').tooltip();  
		$('#product').jzoom({
			height:300, 
			suffixName:'',
			opacity:0.2
		});
		$(document).on('click touchstart',".readmore", function(event) {
			var txt = $(".more-content").is(':visible') ? 'Show more (+)' : 'Show less (–)';
			$(this).prev(".more-content").toggleClass("cg-visible");
			$(this).html(txt);
			event.preventDefault();
		});
			
		//var country_id = getCookie('country_id');
		$(function() {
			$(document).on('click','.piclist li',function(event){
				var $pic = $(this).find('img');
				$('#product img').attr('src',$pic.attr('src'));
				$("#product div").remove();
				$('#product').jzoom({
			height:300,
			suffixName:'',
			opacity:0.2
		});
			});
		});
		$(".rateYo").rateYo({
			fullStar: true, 
			readOnly: true,
			starWidth: "13px",
            halfStar: true,
            multiColor: {
                "startColor": "#f47c26", //RED
                "endColor"  : "#f47c26"  //GREEN
            }
		}); 
        
        $(".rateYo2").rateYo({
			fullStar: true, 
			readOnly: true,
			starWidth: "20px",
            halfStar: true,
            multiColor: {
                "startColor": "#f47c26", //RED
                "endColor"  : "#f47c26"  //GREEN
            }
		}); 
		
		// var id = $("input[type=radio]:checked").data('shipping_id');
		// var link = $('#buyNowBtn');
		// link.attr('href', link.attr('href') + '/'+id);

		$("#choose_shipping_method").on('click','.shipping_method',function(){
			var url = "<?php echo base_url('buynow/');?>";
			var pv_id = $("#addToCartBtn").data('product_variant_id');
			var shipping_id = $(this).attr("data-shipping_id");
			var path = url+pv_id+"/"+shipping_id;
			$("#buyNowBtn").attr("href", path);
			//var link = $("#buyNowBtn").attr("href");
			//var parts = link.split('/');
			//parts[parts.length - 1] = $(this).attr("data-shipping_id");
			//var final_link = parts.join('/');

			//$("#buyNowBtn").attr("href", final_link)
		});
	});

	$variants = $('<input type="hidden" name="variant[]" data-type="none" value="0" />');
	$('.variant a').click(function(){
		if(!$(this).hasClass('selected')){
			var variant = [];
			var selectedVariant = $(this).attr('data-id')?$(this).attr('data-id'):"";
			console.log("variant: ".selectedVariant);
			var flagClass = $(this).attr('data-class');
			$('.'+flagClass).removeClass('selected');
			$(this).addClass('selected');
			$(".variant a.selected").each(function(index,value){
				variant.push($(value).attr("data-id"));
			});
			variant = variant.sort(function(a, b){return a - b}).join(",");
			var condition_id = $( ".condition_click a.selected").find( "span" ).attr('data-id');
			//var selectedVariant = $("ul.variant li.selected a");
			var p_id = $('#p_id').attr('data-id');
			getProductVariantData(condition_id,variant,p_id,selectedVariant);
		}
		return false;
	});
	$('.thumblist2 a').click(function(){
		$('.selected').removeClass('selected');
		$(this).parent().addClass('selected');
	});
	$('.condition_click a').click(function(){
		if(!$(this).hasClass('selected')){
			var condition_id = $(this).find('span').attr('data-id');
			$('.condition_click a').removeClass('selected');
			$(this).addClass('selected')
			var text = $(this).find('span').text();
			var span_class = $(this).find('span').attr('data-class');
			$("."+span_class).text(text);
			var p_id = $('#p_id').attr('data-id');
			$('input[name="conditin_id"]').val(condition_id);
			getProductVariantData(condition_id,"",p_id,"");
		}
		return false;
	});
	
	$('#buyProduct').submit(function(){
		var proceed = true;
	});
	$('#contact-seller').click(function(){
		$("#message-panel").modal('show');
	});
	$('#sendMessage').click(function(){
		var subject = $("#subject").val();
		var message = $("#message").val();
		var product_variant_id = $("#product_variant_id").val();
		var UTCDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
		if(message == ""){
			$("#message").next().html('<strong class="error">Please Enter Message!</strong>');
			return false;
		}
		if(subject !="" && message !=""){
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>product/saveMessage",
				data:{'receiver_id':"<?php echo $viewProductData['product']->seller_id;?>","item_id":"<?php echo $viewProductData['product']->sp_id?>","item_type":"product",'message':message,'seller_id':'<?php echo $viewProductData['product']->seller_id;?>','buyer_id':'<?php echo (isset($_SESSION['userid'])?$_SESSION['userid']:0); ?>','product_variant_id':product_variant_id,'time':UTCDateTime},
				success: function(response){
					if(response.status == 1){
						$('#message-panel').modal('toggle');
						$('#message-notification').modal('show');
						setTimeout(function() {
							$('#message-notification').modal('hide');
  							}, 4000);
						$('#change-message').text("Message sent successfully");
					}
				}
			});
		}
	});
	function getCondtionName(id){
		var condition="new";
		if(id == 2){
			condition = "manufatcurer_refurbished";
		}else if(id == 3){
			condition ='like_new';
		}else if(id == 4){
			condition ='very_good';
		}else if(id == 5){
			condition = 'fair';
		}
		return condition;
	}
	function getProductVariantData(condition_id,variant,p_id,selectedVariant){
		var userid = "<?php echo (isset($_SESSION['userid']))?$_SESSION['userid']:""?>";
		var onePercent = twoPercent = threePercent = fourPercent = fivePercent = 0;  
		var one = two = three = four = five = 0;  
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url()?>product/getProductData",
			data:{'condition_id':condition_id,"variant":variant,"product_id":p_id,"selected_variant":selectedVariant},
			success: function(response){
				$("#qty").val(response.gpvd[0].quantity);
				if(response.gpvdRows > 0){
					$(".variant a").addClass('grey');
					$(response.apvci).each(function(index, element) {
						$(".variant a.variant"+element.v_id).removeClass('grey'); 
					});
					$("#allQuestions").html("");
					$('#mainQuestionHeading').html("");
					var total_qst = (response.questions.total > 0) ? '('+response.questions.total+')' : "";
					$('#mainQuestionHeading').html('<h5 id="mainQuestionHeading">Questions about this Product&nbsp;&nbsp;'+total_qst+'</h5>');
					if(response.questions.total > 0){
						$('#sellerStoreHeading').html("");
						$('#allQuestions').append('<h5 id="sellerStoreHeading">Other questions answered by '+response.gpvd[0].store_name+'</h5>');
						$(response.questions.result).each(function(index, element) {
							$('#allQuestions').append('<div class="qna-item-group"><span class="fas fa-question"></span><span class="qna-content">'+element.question+'</span><div class="qna-meta text-secondary mb-2"><small>'+element.firstname+'&nbsp;&nbsp;&nbsp;'+element.asked_date+'</small></div>');
							if(element.answer != null){
								$('#allQuestions').append('<span class="fas fa-question"></span><span class="qna-content">'+element.answer+'</span><div class="qna-meta text-secondary"><small>'+element.answer_person+'&nbsp;&nbsp;&nbsp;'+element.answered_date+'</small></div>');
							}
							if(element.seller_id == userid && element.answer == "" ){
								$('#allQuestions').append('<button type="button" class="btn btn-secondary mt-3 ansBtn" data-toggle="modal" data-backdrop="static" data-id="'+element.id+'" data-product_id="'+element.product_id+'" data-pv_id="'+response.gpvd[0].pv_id+'" data-target="#exampleModal">Answer</button></div>');
								}
							$('#allQuestions').append('<hr>');
						});
					}
					$(response.gpvd).each(function(index, element) {
						window.history.replaceState("","",element.pv_id);
						if(element.seller_product_description !=""){
							$("#spd").html("<label class='p-2 m-0'>Note:<span class='pl-1 bold-title'>"+element.seller_product_description+"</span></label>");
						}else{
							$("#spd").html("");
						}
						if(element.warranty_id !=""){
							$("#spd").html('<label class="p-2 m-0"><?php echo $this->lang->line('warranty');?>:<span class="pl-1 bold-title" id="warranty">'+element.warranty+'</span></label>');
						}else{
							$("#spd").html("");
						}
						if(userid == element.seller_id){
							$(".hideButtons").addClass('d-none');	
						}else if(userid !="" && userid != element.seller_id){
							$(".hideButtons").removeClass('d-none');
						}
						$('#question_seller_id').val(element.seller_id);
						$('#question_sp_id').val(element.sp_id);
						$('#question_pv_id').val(element.pv_id);
						$('.condition_click a').removeClass('selected');
						$('ul.condition_click li').removeClass('selected');
						$('.condition'+element.condition_id).addClass('selected');
						current_date = new Date(<?php echo DateTime::createFromFormat('y-m-d', date('Y-m-d')); ?>);
						discount_from = new Date(element.valid_from);
						discount_till = new Date(element.valid_to);
						var current_date = $.datepicker.formatDate('yy-mm-dd', current_date);
						var discount_from = $.datepicker.formatDate('yy-mm-dd', discount_from);
						var discount_till = $.datepicker.formatDate('yy-mm-dd', discount_till);
						if((current_date >= discount_from) && (current_date <= discount_till)){
							$(".discountSpan").removeClass("d-none");
							$("#o_price").val(element.price);
							if(element.discount_type != "" && element.discount_type == "percent"){
								var v = (parseFloat(element.price) * parseFloat(element.discount_value) / 100);
								var discounted = parseFloat(element.price) - v;
								if(discounted < 0){discounted = element.price;}
								var extra = " % OFF";
								$("#o_price").text('$'+element.price);
								$("#d_price").text('US $'+discounted);
								$('#discount_valueLabel').text(element.discount_value);
								$("#extra").text(extra);
							}else if(element.discount_type != "" && element.discount_type == "fixed" ){
								var q = (parseFloat(element.discount_value) / parseFloat(element.price)) * 100;
								var fv = (parseFloat(element.price) * parseFloat(q) / 100);
								var discounted = parseFloat(element.price) - fv;
								if(discounted < 0){discounted = element.price;}
								var extra = " $ OFF";
								$("#o_price").text('$'+element.price);
								$("#d_price").text('US $'+discounted);
								$('#discount_valueLabel').text(element.discount_value);
								$("#extra").text(extra);
							}else{
								$("#d_price").text('US $'+element.price);
							}
						}
						else
						{
							$("#d_price").text('US $'+element.price);
							$(".discountSpan").addClass('d-none');
						}
						$("#desciption").text(element.seller_product_description);
						if(element.seller_id == userid){
							$("#addToCartBtn").addClass('disabled');
							$("#addToCartBtn").attr('data-product_variant_id',"");
						}else{
							$("#addToCartBtn").removeClass('disabled');
							$("#addToCartBtn").attr('data-product_variant_id',element.pv_id);	
						}
						$("#product_variant_id").val(element.pv_id);						
						$("#addToWishlistBtn").attr('data-product_variant_id',element.pv_id);
						$("#addToWishlistBtn").attr('data-id',element.product_id+"-"+element.pv_id);
						$("#buyProduct").attr('action',"<?php echo base_url(); ?>"+'cart/addtocart/'+element.pv_id);
						var seller_id = "<?php echo (isset($_SESSION['userid']) && $_SESSION['userid']!= "")?$_SESSION['userid']:""?>";
						if(seller_id != element.seller_id){
							$("#addToCartDiv").html('<a href="javascript:void(0)" class="btn btn-hover color-orange btn-xl" id="addToCartBtn" data-toggle="tooltip" title="Add to cart" data-product_variant_id="'+element.pv_id+'" data-available_shipping_ids="'+element.shipping_ids+'"><i class="fa fa-shopping-cart"></i>  Add to Cart</a>');
							$("#buyNowDiv").html('<a type="button" href="<?= base_url() ?>buynow/'+element.pv_id+'" id="buyNowBtn" class="btn btn-hover color-blue btn-xl" >Buy Now</a>');
						}else{
							$("#addToCartDiv").html('<a href="javascript:void(0)" class="btn btn-hover color-orange btn-xl disabled" data-toggle="tooltip" title="Add to cart"><i class="fa fa-shopping-cart"></i>  Add to Cart</a>');
							$("#buyNowDiv").html('<a href="javascript:void(0)" class="btn btn-hover color-blue btn-xl disabled" id="buyNowBtn" data-toggle="tooltip">Buy Now</a>');
						}
						saveProductHistory(element.pv_id);
						$("#store_name").text(element.store_name.stripSlashes());
						$("#store_name").attr('href',"<?php echo base_url('store/')?>"+element.store_id);
						$(".variant a").removeClass('selected');
						var variant_group = element.variant_group.split(',');
						$("#product_qty").attr('max',element.quantity);
						$(variant_group).each(function(index, element) {
						   $(".variant a.variant"+element).addClass('selected'); 
						});
						$('#product img').html('');
						// console.log(response); return false;
						// if(response.apci[0].image_link !=null || response.apci[0].external_image_link !=null || response.apci[0].video_link !=null){
					// 		if(response.apci[0].is_image != null){
					// 		$('#product img').append('<div class="page"><div class=""><img class="img-fluid ProdImgSize" src="" height="320" width="320" alt=""></div><ul class="piclist variantpiclist"></ul></div>');
					// 		// var img_link = response.apci[0].image_link.split(',');
					// 		/*Image Work*/
					// 		var productDetailImagesAndVideos = Array(); 
					// 		var image_link = Array();
					// 		var external_image_link = Array();
					// 		var video_link = Array();
					// 		var path = "<?php echo product_path();?>";
					// 		var firstImage = "";
					// 		if(response.apci !=""){
					// 			// image_link = response.apci[0].image_link.split(',');
					// 			var img_counter  = 0;
					// 			$(response.apci).each(function(index,value){
					// 				// console.log(response.apci[index].image_link);
					// 				image_link[img_counter] = path+response.apci[index].image_link;
					// 				img_counter++;
					// 			})
					// 		}
					// 		// if(response.apci[0].external_image_link !=null && response.apci[0].external_image_link !=""){
					// 		// 	external_image_link = response.apci[0].external_image_link.split(',');
					// 		// }
					// 		// if(response.apci[0].video_link !=null && response.apci[0].video_link !=""){
					// 			if(response.apci[0].video_link !=null && response.apci[0].is_image != 1){
					// 			video_link = response.apci[0].video_link.split(',');
					// 		}
					// 		if(response.apci[0].is_local == '1'){
					// 			if(response.apci[0].is_primary_image != null){
					// 				firstImage = response.apci[0].is_primary_image;
					// 				firstImage = firstImage.replace("_thumb", "");
					// 				firstImage = path+firstImage;  
					// 			}
					// 		}else if(response.apci[0].is_local == '0') {
					// 			firstImage = response.apci[0].is_primary_image;                                         
					// 		}
					// 		productDetailImagesAndVideos = image_link.concat(video_link);
					// 		/*Ends*/
					// 		$('.variantpiclist').html('');
					// 		if(response.apci[0].is_local == 1){
					// 			path = "<?php echo product_path();?>";
					// 		}else{
					// 			path = "";
					// 		}
					// 		$('#product img').attr('src',firstImage);
					// 		if(productDetailImagesAndVideos !== null){
					// 			$('.piclist').html('');
					// 			var i = 0;
					// 			var images = [];
					// 			$(productDetailImagesAndVideos).each(function(img_index, img_element) {
					// 				images.push(img_element);
					// 				$('.piclist').append('<li><img src="'+img_element+'" alt="" class="img-fluid" id="'+i+'_img"></li>');
					// 				i++;
					// 			});
					// 			changeProductImages(images);
					// 		}
					// 		$('#product img').hide();
					// 		$('#product img').show();
					// 		$(function(){
					// 			$('.piclist li').on('click',function(event){
					// 				var $pic = $(this).find('img');
					// 				$('#product img').attr('src',$pic.attr('src'));
										 
					// 			});
					// 		});
					// 	}else{
					// 		$('#product').jzoom().hide;
					// 		$('#product').jzoom().show;
					// 	}
					});
					//heer
					if(response.apci.length > 0){
						lightslider_refresh(response.apci);
					}
					link_refresh(response.gpvd[0].slug, response.gpvd[0].pv_id);
					$("#total_reviews").html(response.avgRating.result.total_review+" Reviews");
					$("#total_questions").html(response.questions.total+" Questions & Answers");
					var avg = 0;
					if(response.avgRating.result.avg_rating){
						$(".before-out-of").html(response.avgRating.result.avg_rating.substring(0,3));
						avg = response.avgRating.result.avg_rating;
					}else{
						$(".before-out-of").html(avg);
					}
					$("#review_rating").rateYo("option", "rating",avg);
					$("#review_rating2").rateYo("option", "rating",avg);
					if(response.reviews.total > 0){
						$( response.reviews.result ).each(function( index,value ) {
							//console.log( index + ": " + value.rating );
							if(value.rating < "2"){
								one++;
							}else if(value.rating < "3"){
								two++;
							}else if(value.rating < "4"){
								three++;
							}else if(value.rating < "5"){
								four++;
							}else if(value.rating == "5"){
								five++;    
							}
						});
					}
					if(one > 0){
						onePercent = one/response.reviews.total;
						onePercent *=  100;
					}
					if(two > 0){
						twoPercent = two/response.reviews.total;
						twoPercent *=  100;
					}
					if(three > 0){
						threePercent = three/response.reviews.total;
						threePercent *=  100;
					}
					if(four > 0){
						fourPercent = four/response.reviews.total;
						fourPercent *=  100;
					}
					if(five > 0){
						fivePercent = five/response.reviews.total;
						fivePercent *=  100;
					}
					$("#one-percent").css("width", onePercent+"%");
					$("#two-percent").css("width", twoPercent+"%");
					$("#three-percent").css("width", threePercent+"%");
					$("#four-percent").css("width", fourPercent+"%");
					$("#five-percent").css("width", fivePercent+"%");

					$(".reviewStats #one").text(one);
					$(".reviewStats #two").text(two);
					$(".reviewStats #three").text(three);
					$(".reviewStats #four").text(four);
					$(".reviewStats #five").text(five);
					$("#all-reviews").html(response.reviews.total);
					// console.log("one:"+one+" two:"+two+" three:"+three+" four:"+four+" five:"+five);
					var divClass = "";
					var divId = "";
					var reviewClass ='.customerReview';
					var showMoreClass = '.show-more';
					if(response.reviews.total > 3){$(showMoreClass).addClass('d-block');$(showMoreClass).removeClass('d-none');}
					else{$(showMoreClass).addClass('d-none');$(showMoreClass).removeClass('d-block');}
					if(response.reviews.total > 0){
						productReviews(response.reviews,reviewClass);
					}else{
						$(reviewClass).html("<Strong><p>No reviews added for this product</p></Strong>");
					}
					/*if(response.moreProductFromThisSeller.rows > 0){
						// console.log(response.moreProductFromThisSeller);
						divClass= '.more-from-this-sellers .carousel-inner';
						divId = "#more-from-container";
						addProductData(response.moreProductFromThisSeller,divClass,divId);
					}else{
						$('.more-from-this-sellers .carousel-inner').html("");
					}
					if(response.otherSeller.rows > 0){
						divClass= '.other-sellers-offers .carousel-inner';
						divId = "#other-container";
						addProductData(response.otherSeller,divClass,divId);
					}else{
						$('.other-sellers-offers .carousel-inner').html("");
					}*/
					if(response.shippingData){
						$("#choose_shipping_method tbody").html("");
						setShippingMethod(response.shippingData);
						changeShippingMethod();
					}
				}else{
					alert('Not Available!');
				}
				$('.price').formatCurrency();
				$(".rateYo").rateYo({
					fullStar: true, 
					readOnly: true,
					starWidth: "13px",
                    halfStar: true,
                    multiColor: {
                        "startColor": "#f47c26", //RED
                        "endColor"  : "#f47c26"  //GREEN
                    }
				});  
			}
		});
		$("#product_qty").val("1");
		$("#pops").attr("style","display: none");
	}
	String.prototype.stripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}

	function saveProductHistory(product_variant_id){
		$.ajax({
			type: "POST",
			url: "<?php echo base_url()?>product/saveProductHistory",
			data:{"product_variant_id":product_variant_id},
		});
	}

	function lightslider_refresh(pics){

		var data = "";
		var img_url = "<?php echo $this->config->item('product_path') ?>";
		$('#lightSlider').empty();
		$(".lSPager").remove();
		$(".lSAction").empty();

		var slider = $("#lightSlider").lightSlider({
			gallery: true,
			item: 1,
			thumbItem: 5,
			enableDrag: true,
			currentPagerPosition:'left',
			pauseOnHover: true
		});

		length = pics.length;

		for(i = 0; i < length; i++){
			if((pics[i].image_link).includes("<iframe")){
				data += "<li href='"+pics[i].image_link+"' data-thumb='<?php echo assets_url("front/images/play_icon.png"); ?>' data-toggle='lightbox' data-gallery='gallery' class='lslide'>"
					 +pics[i].image_link
					 +"</li>";
			}else{
				data += "<li href='"+img_url+pics[i].image_link+"' data-thumb='"+img_url+"thumbs/"+pics[i].is_primary_image+"' data-toggle='lightbox' data-gallery='gallery' class='lslide'>"
					 +"<img class='img img-fluid' src='"+img_url+pics[i].image_link+"'/>"
					 +"</li>";
			}
		}
		slider.prepend(data);
		slider.refresh();
	}	

	$('#reviewSubmit').click(function(){
	
		$('span.error').text('');
		var process = true;
		var username = $('input[name="name"]').val();
		var email = $('input[name="email"]').val();
		var product_name = $('input[name="pdt"]').val();
		var review = $('textarea[name="review"]').val();
		var rating = $('input[name="rating"]').val();
		//var datetime = $('#time').val($('#timeValue').text());
		var datetime = $('input[name="date"]').val();

		if(username == ''){
			$('input[name="name"]').next().text('Name is required');
			process = false;
		}
	if ( $.trim( $('input[name="name"]').val() ) == '' )
			alert('No white spaces allowed');
	
		if(email == ''){
			$('input[name="email"]').next().text('Email is required');
			process = false;
		}
		if(product_name == ''){
			$('input[name="pdt"]').next().text('Product Name is required');
			process = false;
		}
		if(review == ''){
			$('textarea[name="review"]').next().text('please write some description');
			process = false;
		}
		if ( $.trim( $('textarea[name="review"]').val() ) == '' )
			alert('Yo can not submit an empty review');
			
		if(rating == 0){
			$('.rateYo_error').text('rating is required');
			process = false;
		}
		var recaptcha = $("#g-recaptcha-response").val();
		if(recaptcha == ""){
			$('.recaptcha_error').text('recaptcha is required');
			process = false;
		}
		if(process){
			var date = new Date();
			var n = date.toDateString();
			var time = date.toLocaleTimeString();
			var dt = n + ' ' + time; 
			var reviewData = {
				name: username,
				email: email, 
				pdt: product_name,
				review: review,
				rating: rating,
				date: dt,
				pv_id:$('#product_variant_id').val(),
				product_id:$('#product_id').val()
			}
			

			var url = '<?php echo base_url("product/review"); ?>';
			ajaxRequest(reviewData, url, function(data){
				//console.log(data);
				if(data.status == 1){
					location.reload();
					$("#loader").show();

				} else {
					$('.showReviewError').text(data.message);
				}
			});
		}
		return false;
	});	
function copytoclipboard() {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(location.href).select();
    document.execCommand("copy");
    $temp.remove();
	$('#costumModal27').modal('show');
}
//console.log($("#url"));
$(".modal").each(function(l){
	$(this).on("show.bs.modal",function(l){
		var o=$(this).attr("data-easein");
		"shake"==o?$(".modal-dialog").velocity("callout."+o):
		"pulse"==o?$(".modal-dialog").velocity("callout."+o):
		"tada"==o?$(".modal-dialog").velocity("callout."+o):
		"flash"==o?$(".modal-dialog").velocity("callout."+o):
		"bounce"==o?$(".modal-dialog").velocity("callout."+o):
		"swing"==o?$(".modal-dialog").velocity("callout."+o):
		$(".modal-dialog").velocity("transition."+o)
		setTimeout(function() {
			$('#costumModal27').modal('hide');
  			}, 4000);
		})
		});
		



$(document).on('click','.url', function(){
  var textarea = document.createElement('textarea');
  textarea.textContent = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>';
  document.body.appendChild(textarea);

  var selection = document.getSelection();
  var range = document.createRange();
//  range.selectNodeContents(textarea);
  range.selectNode(textarea);
  selection.removeAllRanges();
  selection.addRange(range);

  //console.log('copy success', document.execCommand('copy'));
  selection.removeAllRanges();

  document.body.removeChild(textarea);
  $('#costumModal27').modal('show');
  
})
$(document).on('click','#addToCartBtn',function(){
	var pv_id = $(this).attr('data-product_variant_id');
	var allShippingIds = $(this).attr('data-available_shipping_ids'); 
	var referral = $("#ref").val(); 
	var maxQty = parseInt($("#qty").val());
	var qty = parseInt($("#product_qty").val());
	shipping_id = $(".shipping_method:checked").attr('data-shipping_id');
	shipping_title = $(".shipping_method:checked").attr('data-title');
	shipping_price = $(".shipping_method:checked").attr('data-price');
	if(qty > maxQty){
		if(maxQty == 1){
			$("#pops").text('Quantity must be 1');
		}else{
			$("#pops").text('Quantity must be less than or equal to '+maxQty);
		}
		return false;
	}
	if(qty && qty !=0){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url()?>cart/addtocart",
			data:{"pvid":pv_id,"qty":qty,'is_ajax':true,'shipping':{'shipping_id':shipping_id,'title':shipping_title,'price':shipping_price,'allShippingIds':allShippingIds}, 'ref':referral},
			success: function(response){
				if(response.status == 1){
					location.href = "<?php echo base_url('cart');?>";
				}
			}
		});
	}else{
		$("#pops").text('Quantity must be 1');
		return false;
	}
});
$("#product_qty").on('click',function(){
	var value = $(this).val();
	var max = $(this).attr('max');
	var min = $(this).attr('min');
	value = parseInt(value);
	max = parseInt(max);
	// var str = "";
	// var str2 = "";
	// str = $(this).attr('id')
	// str2 = str.split("_");
	if(value >= max){
		alert("value >= max");
		$('#pops').show().html('Max quantity approached').css('padding-left','25px');
		}
		if(value > max){
		alert("value > max");
		$('#addToCartBtn').prop('disabled', true).css('background-color','#b9d594');
		}
		else if(value < max) {
		alert("value < max");
		$('#pops').hide();
		$('#addToCartBtn').prop('disabled', false).css('background-color','#8CC53D');
	}
});

function productReviews(response,reviewClass){
	$(document).ready(function(e) {
	$(".rateYo").rateYo({
			fullStar: true, 
			readOnly: true,
			starWidth: "13px",
            halfStar: true,
            multiColor: {
                "startColor": "#f47c26", //RED
                "endColor"  : "#f47c26"  //GREEN
            }
            
		}); 
		
	})

	var reviewDiv = "";
	if(response.total > 0 && response.result[0]['review_id']){
		reviewDiv += '<div class="row">';
		var profile_path = "<?php echo profile_path()?>";
		$(response.result).each(function(index,value){
			// console.log(response);
			if(index < 3){
			var file = 'buyer_'+value.buyer_id+'_thumb.png';
			// if(file.exists){
			// 	alert();
			// 	var link = profile_path+'buyer_'+value.buyer_id+'.png';
			// }
			// else{
			// 	var link = profile_path+'defaultprofile.png';
			// }
			/*if(file_exists('./uploads/profile/seller_'$rec->buyer_id.'.PNG')){
				$link = profile_path('seller_'.$rec->buyer_id.'.PNG');
				//print_r($link);
			} else {
				$link = <?php //assets_url('backend/img/images/defaultprofile.png');?>
				//print_r($link);
			}*/ 
			link = "<?php echo assets_url('backend/images/defaultprofile.png');?>";
			// var user_img = encodeURI(file);
			// user_img = user_img.split(".");
			var imagePath = (value.is_fake == "1")?link:"<?php echo base_url('home/get_image/?path='.urlencode(profile_path()).'thumbs/&file=');?>" + encodeURI(file);
			var name = (value.is_fake == "1")?(value.review_name).substring(0,3)+"***":(value.name).substring(0,3)+"***";
			reviewDiv+='<div class="col-12 mb-5">'+
							'<div class="row">'+
								'<div class="col-sm-2 col-md-1 pb-2"><span class="ml-1 user-review">'+name+'</span></div>'+
								'<div class="col-sm-3 col-md-3 pt-1"><div class="rateYo p-0" data-rateyo-rating='+value.rating+'></div></div>'+
							'</div>'+
							'<div class="row">'+
									'<div class="col-sm-2 col-md-1 mb-2">'+
										'<img src="'+imagePath+'" class="review-icon reviewImageHW img-fluid" alt="">'+
									'</div>'+
									'<div class="col-sm-10 col-md-10 mb-2 user-review">'+
										'<span class="small">'+formatAMPM(value.date)+'</span><br>';
							 if(value.review != null){
								 if(value.review.length > 250){
								reviewDiv+='<span>'+
											'<i>'+value.review.substr(0,250).stripSlashes()+'</i>'+
											'<i class="more-content">'+value.review.substr(250).stripSlashes()+'</i>'+
											'<a class="readmore text-color" href="#"><?php echo $this->lang->line('show_more');?> (+)</a>'
										'</span>';
								}else{
									reviewDiv+='<i>'+value.review.stripSlashes()+'</i>';
								}
								reviewDiv+="</br>";
								if(value.review_img != null){
									if(value.review_img.indexOf(',') == -1) {
										reviewDiv+='<img class="img-thumbnail" data-review="'+value.review_id+'" src="<?php echo image_url('review/thumbs/')?>'+value.review_img+'">';
									}else{
										var images = value.review_img.split(",");
										images.forEach(function(image){
											reviewDiv+='<img class="img-thumbnail" data-review="'+value.review_id+'" src="<?php echo image_url('review/thumbs/')?>'+image+'">'
										});
									}
									reviewDiv+='<div id="images-'+value.review_id+'" class="tab-pane mb-1 position-relative col-12 col-md-4 d-none">'+
													'<button id="close-img-'+value.review_id+'" data-review="'+value.review_id+'" class="position-absolute close small cross-btn d-none">'+
														'<span>x</span>'+
													'</button>'+
													'<img id="original_pic-'+value.review_id+'" class="p-4 img-fluid"/>'+
												'</div>';
								}
							 }
							 reviewDiv += '</div></div></div>';				 
			}
			});
			//reviewDiv += '</div>'
		$(reviewClass).html(reviewDiv);
		// console.log(response);
		if(response.total > 3){
				$(response.result).each(function(index,value){
				var encode_data = value.seller_id+"-zabeeBreaker-"+value.product_id+"-zabeeBreaker-"+value.sp_id+"-zabeeBreaker-"+value.pv_id;
				var url_val = btoa(encodeURI(encode_data));
	 			var link =  "<?php echo base_url('product/showMoreReview/') ?>"+url_val;
					$('#review-show-more').attr("href",link);
				});
				}

}
	else{		
		reviewDiv =+ "<Strong><p>No reviews added for this product</p></Strong>";
		$('#review-show-more').removeClass('d-block');
		} 
}
function addProductData(data,divClass,divId){
	if(data.rows > 0){
		var closeDiv = 0;
		var mpftsCount = 0;
		var moreproductDiv = "";
		mpftsCount = data.rows;
		var product_image = "";
		var product_rating = 0;
		var cartLink = "";
		var productDetailLink  = "";
		var breakDiv = 1;
		var user_id = "<?php echo $user_id?>";
		var isloggedin = "<?php echo $this->isloggedin?>";
		<?php $i=0;?>
		$(divClass).html("");
		$(data.result).each(function(index,value){
			if(index == 0){
				activeClass = "active";
			}else{
				activeClass = "";
			}
			<?php if(!$detect->isMobile()){?>
			if(index == 0){
				moreproductDiv +='<div class="carousel-item active">'+
									'<div class="card-deck">';
			}
			if(index > 0 && index%4 == 0 && mpftsCount >4){
				closeDiv = (index+3);
				moreproductDiv +='<div class="carousel-item">'+
									'<div class="card-deck">';
			}
			breakDiv = 3;
			<?php }else{?> moreproductDiv +='<div class="carousel-item '+activeClass+'">'+
									'<div class="card-deck">'; <?php }?>
			if(value.is_local == 1){
				product_image = "<?php echo product_thumb_path()?>"+value.product_image;
			}else{
				product_image = value.product_image;
			}
			if(value.rating){
				product_rating = value.rating;
			}else{
				product_rating = 0;
			}
			cartLink = "<?php echo base_url('cart/addtocart/'); ?>"+value.pv_id;
			productDetailLink = btoa(encodeURI(value.product_name+'_'+value.product_id));
			productDetailLink = "<?php echo base_url().'product/detail/'?>"+cleanUrl(productDetailLink);//+'/'+value.pv_id;
			
			moreproductDiv +='<div class="card col-lg-3 col-12 text-center border-0">'+
								'<div class="card-img-top image-center-parent">'+
									'<a href="'+productDetailLink+'"><img src="'+product_image+'" alt="'+value.product_name+'" class="img img-fluid mx-auto d-flex my-auto image-center" data-toggle="tooltip"  title="'+value.product_name+'" /></a>'+
								'</div>'+
								'<div class="card-body p-0">'+
									'<a href="'+productDetailLink+'"><h2 class="other-seller-product-name">'+value.product_name+'</h2></a>'+
									'<div class="col-sm-12">'+
										'<div style="display:inline-block;" class="rateYo" data-rateyo-rating="'+product_rating+'"></div>'+
									'</div>'+
									'<div class="col-sm-12 top-rated-product-price price">$'+value.price+'</div>'+
									'<div class="col-sm-12">';
										if(!isloggedin){ 
											moreproductDiv+='<a href="'+cartLink+'" class="btn cart-buttons"  title="Add to cart"><i class="fa fa-shopping-cart"></i></a>'; 
										}else if(isloggedin && value.seller_id != user_id){
											moreproductDiv+='<a href="'+cartLink+'" class="btn cart-buttons"  title="Add to cart"><i class="fa fa-shopping-cart"></i></a> ';
										}
										//moreproductDiv+='<a href="'+productDetailLink+'" class="btn cart-buttons"  title="Product Details"><i class="far fa-eye"></i></a>' 
										moreproductDiv+='<button type="button" class="btn cart-buttons addToWishlistBtn" data-product_variant_id="'+value.pv_id+'" data-product_id="'+value.product_id+'" data-id="'+value.product_id+'-'+value.pv_id+'" title="Save for later" ><i class="far fa-heart"></i></button>'+
									'</div>'+
								'</div>'+
							'</div>';
			<?php if(!$detect->isMobile()){ ?>
			if(index ==3){
				 moreproductDiv+="</div></div>";
			} 
			if(closeDiv > 0 && index%closeDiv == 0  && mpftsCount > 4){
				moreproductDiv+="</div></div>";
			}if(mpftsCount == 1){ moreproductDiv+="</div></div>";}
			<?php }else{ ?> moreproductDiv+="</div></div>"; <?php }?>
			
		});
		if(mpftsCount > breakDiv){
			$(divId).removeClass('d-none');
		}else{
			$(divId).addClass('d-none');
		}
		$(divClass).html(moreproductDiv);
	}	
}
function cleanUrl(string){
	string = string.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');	
	return string;
}
function setShippingMethod(data){
	var html = "";
	var checked = "";
	var price = 0;
	//console.log(data);
	$(data).each(function(index,value){
		if(index == 0){
			checked = "checked";
		}else{
			checked = "";
		}
		if(value.price == 0){
			price = "Free Shipping";
		}else{
			price = "US $"+value.price;
		}
		html += '<tr>'+
					'<td> '+
						'<div class="custom-control custom-radio mb-3">'+
							'<input type="radio" class="custom-control-input shipping_method" data-shipping_id="'+value.shipping_id+'" data-title="'+value.title+'" data-price="'+value.price+'" id="shipping'+value.shipping_id+'" name="shipping_method"  '+checked+' value="'+value.shipping_id+'">'+
							'<label class="custom-control-label radio-custom-input seller-info" for="shipping'+value.shipping_id+'">'+value.title+'</label>'+
						'</div>'+ 
					'</td>'+
					'<td>'+value.duration+" days"+'</td>'+
					'<td>Not Available</td>'+
				'</tr>';
	});
	$("#choose_shipping_method tbody").append(html);
}
var buynow = $("#buyNowBtn").attr("href");
$(".qty-max").on('click',function(){
	var value = $('#product_qty').val();
	var max = $('#product_qty').attr('max');
	var min = $('#product_qty').attr('min');
	value = parseInt(value);
	max = parseInt(max);
	if(value > 8){
		$("#for-input-width").removeClass("this-input-width");
		$("#for-input-width").addClass("this-input-width-double-digit");
		$("#for-input-width").removeClass("this-input-width-triple-digit");
	} else if(value > 98){
		$("#for-input-width").removeClass("this-input-width");
		$("#for-input-width").removeClass("this-input-width-double-digit");
		$("#for-input-width").addClass("this-input-width-triple-digit");
	} 
	if(value >= max){
		$('#pops').show().html('Max quantity approached').css('padding-left','25px');
	}
	if(value > max){
		$('#addToCartBtn').prop('disabled', true).css('background-color','#b9d594');
	}
	$('#product_qty').val( function(i, oldval) {
		if(parseInt( oldval, 10) + 1 < $("#product_qty").attr("max")){
			return parseInt( oldval, 10) + 1;
		} else {
			return $("#product_qty").attr("max");
		}
	});
	value = (value >= max) ? max - 1 : value;
	$("#buyNowBtn").attr("href", buynow+"?qty="+(value + 1));
});
$(".qty-min").on('click',function(){
	var value = $('#product_qty').val();
	var max = $('#product_qty').attr('max');
	var min = $('#product_qty').attr('min');
	value = parseInt(value);
	value = value - 1;
	max = parseInt(max);
	if(value < 10){
		$("#for-input-width").addClass("this-input-width");
		$("#for-input-width").removeClass("this-input-width-double-digit");
		$("#for-input-width").removeClass("this-input-width-triple-digit");
	}
	if(value < max) {
		$('#pops').hide();
		$('#addToCartBtn').prop('disabled', false).css('background-color','#8CC53D');
	}
	$('#product_qty').val( function(i, oldval) {
		if(parseInt( oldval, 10) - 1 > $("#product_qty").attr("min")){
			return parseInt( oldval, 10) - 1;
		} else {
			return $("#product_qty").attr("min");
		}
	});
	value = (value < 1) ? 1 : value;
	$("#buyNowBtn").attr("href", buynow+"?qty="+value);
});
/*var i = 0;
var switch_img_array = [];
var current_index = 0;
var last_li = $( ".piclist li img" ).last().attr("id");
var result_last_li = last_li.split("_");
result_last_li = result_last_li[0];
//console.log(result) 
$('.piclist li img').each(function(index, value){
	switch_img_array[i] = $(value).attr("src");
	i++;
});
$(".right_Image").on('click',function(){
	current_index = switch_img_array.indexOf($('.current_img').attr('src'));
	//console.log(current_index);
	$(".current_img").attr("src",switch_img_array[current_index+1]);
});
$(".left_Image").on('click',function(){
	current_index = switch_img_array.indexOf($('.current_img').attr('src'));
	//console.log(current_index);
	$(".current_img").attr("src",switch_img_array[current_index-1]);
});*/
$('.plugin_images').click(function () {
    if($(this).attr('src').includes("youtube")){
		var res = $(this).attr('src').split("vi/");
		res = res[1].split("/");
		res = res[0];
		res = res.split("v=");
		res = res[1];
		var youtube = "https://www.youtube.com/embed/"+res+"?autoplay=1&showinfo=0";
		//alert(youtube);
		$( ".current_img" ).replaceWith('<iframe id="iframeElem" class="current_img" width="420" height="315" src="'+youtube+'" frameborder="0" allowfullscreen> </iframe> ');
		

	} else {
		$( ".current_img" ).replaceWith('<img  src="'+$(this).attr('src')+'" alt="" class="img-fluid current_img" style="max-height: 650px;">');
	}
});

/*function ChangeImageEveryFiveSeconds(){
	//console.log(current_index);
	if(current_index < result_last_li){
		current_index = switch_img_array.indexOf($('.current_img').attr('src'));
		current_index = current_index + 1;
		$(".current_img").attr("src",switch_img_array[current_index]);
	} else if(current_index == result_last_li){
		current_index = 0;
		$(".current_img").attr("src",switch_img_array[current_index]);
	}
}
setInterval(function(){ 
	ChangeImageEveryFiveSeconds()
}, 5000);*/
/*
function toggle(){
	var last_li = $( ".piclist li img" ).last().attr("id");
	
}
*/
/*var last_li = $( ".piclist li img" ).last().attr("id");
var result = last_li.split("_");
result = result[0];
console.log(result) 
function ChangeImageEveryFiveSeconds(length, start){
	if(start < length){
		SrcValue = $("#"+start+"_img").attr("src");
		setInterval(function(){$(".current_img").attr("src", SrcValue);}, 1000);
		start++;
		console.log(start)
	} else if(start == length){
		SrcValue = $("#"+start+"_img").attr("src");
		setInterval(function(){ $(".current_img").attr("src", SrcValue); }, 1000);
		start = 0;
		console.log(start)
	}
	setInterval(function(){ChangeImageEveryFiveSeconds(length, start);}, 3000);
}
ChangeImageEveryFiveSeconds(result, 0);*/
var res = [];
var j = 0;
function changeProductImages(images){
	switch_img_array = images;
}

$("#allQuestions").on('click','.ansBtn',function(){
    $('#question_id').val($(this).attr('data-id'));
	$('#qna_product_id').val($(this).attr('data-product_id'));
    $('#modal_pv_id').val($(this).attr('data-pv_id'));

    $('#answered_date').val(dateTime);
  });

var today = new Date();
var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
var dateTime = date+' '+time;
$('#asked_date').val(dateTime);

$("#qnaForm").validate({
	rules:{
		question: {
				required: true,
			},
		},
	errorPlacement: function(error, element) {
		error.appendTo( element.parent().parent() );
	}
});
$("#customer-review").on('click',".img-thumbnail",function(){
	var link = $(this).attr('src');
	var id = $(this).data('review');
	
	link = link.replace('_thumb.','.');
	link = link.replace('/thumbs/','/');
	$("#original_pic-"+id).attr('src',link);
	$(".cross-btn").attr('style','right:0');
	$(".cross-btn").attr("href","javascript:void(0)");
	$("#close-img-"+id).removeClass("d-none").addClass( "d-block" );
	$("#images-"+id).removeClass("d-none").addClass( "d-block" );
	return false;
});

$("#customer-review").on('click',".cross-btn",function(){
	var id = $(this).data('review');
	$("#images-"+id).removeClass("d-block").addClass( "d-none" );
	return false;
});

$("#share_this").click(function(){
	var pv_id = $(this).attr('data-product_variant_id');
	var userid = '<?php echo $this->session->userdata('userid'); ?>';
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url()?>referral/referrals/invite_people/product",
		data:{"prd_id":pv_id,"sender_id":userid},
		success: function(response){
			if(response.status == 1){
				var link = (window.location.href.indexOf("?from=") >= 0)?window.location.href.replace("                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        =", ""):window.location.href;
				link = link+"/"+pv_id+"?ref="+response.code;
				$("#share_link").val(link);
				$(".st-custom-button").attr('data-url', link);
				$(".st-btn").attr("style","display:inline-block");
				$(".notif").hide();
				$("#shareModal").modal('show');
			}
		}
	});
});

$("#copy_link").click(function(){
	var text = $("#share_link").val();
	copyToClipboard(text);
});

function copyToClipboard(text) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(text).select();
    document.execCommand("copy");
    $temp.remove();
	$(".notif").show();
	setTimeout(function() {
		$(".notif").hide();
	}, 2500 );
}

function link_refresh(slug, variant_id){
	var shareButtons = document.querySelectorAll("#social-side .st-custom-button[data-network]");
   	for(var i = 0; i < shareButtons.length; i++) {
		var shareButton = shareButtons[i];
		$(shareButton).attr("data-url", "<?php echo base_url() ?>"+slug+"/"+variant_id);
   	}
}

function formatAMPM(date) {
		// var hours = date.getHours();
		// var minutes = date.getMinutes();
		// var ampm = hours >= 12 ? 'PM' : 'AM';
		// hours = hours % 12;
		// hours = hours ? hours : 12; // the hour '0' should be '12'
		// minutes = minutes < 10 ? '0'+minutes : minutes;
		date =  date.replace(/-/g,'/')
		date =  new Date(date+" UTC");
		date =  new Date(date.toString());
		var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		var strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear();
		return strTime;
	}

(function($){
        $(window).on("load",function(){
            $(".content").mCustomScrollbar();
        });
    })(jQuery);
$(document).on("click", '[data-toggle="lightbox"]', function(event) {
  event.preventDefault();
  $(this).ekkoLightbox();
});
</script>