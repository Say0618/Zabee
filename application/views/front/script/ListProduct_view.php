<?php 
										$current_link = $_SERVER['REQUEST_URI'];
										$url=strtok($_SERVER["REQUEST_URI"],'?');
										$all_link = current_url();
                                        $brandCount = count($brandData);
										if(isset($_GET['category_search'])){
											$category_link = str_replace('&category_search='.$_GET['category_search'], '', $_SERVER['QUERY_STRING']);
										
											$category_link = $url."?".$category_link;
										}else{
											$category_link = $current_link.'/SearchResults?';
										}
										if(isset($_GET['brands_search'])){
											$brand_link =str_replace('&brands_search='.$_GET['brands_search'], '', $_SERVER['QUERY_STRING']);
		 									$brand_link = $url."?".$brand_link;
										}else{
											$brand_link = $current_link;
										}
										?> 
<script src = "<?php echo assets_url('common/js/jquery-ui.min.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script>
<script>	
$(document).ready(function(e) {
	<?php if(isset($_GET['product_name']) || isset($_GET['min_price'])){?>
		$("#headingFour a").trigger('click');
	<?php } ?>
	<?php if(isset($_GET['category_search'])){?>
		$("#headingOne a").trigger('click');
	<?php } ?>
	<?php if(isset($_GET['brands_search'])){?>
		$("#headingTwo a").trigger('click');
	<?php } ?>
	<?php if(isset($_GET['price_range'])){?>
		$("#headingThree a").trigger('click');
	<?php } ?>
	<?php if(isset($_GET['keywords'])){?>
		$("#headingSix a").trigger('click');
	<?php } ?>

	$('[data-toggle="tooltip"]').tooltip();  
	$(".range-input").click(function(){
		$(".go-btn").removeClass("btn-hide");
	});
	$('.product-row').each(function(index,value){ 
		var pd = $(this).find('.product-description').text().trim().length;
		var height = $(this).height();
		height = height+25;
		$(this).css('min-height',height);
	});
	// $('#tag').tokenfield({
	// 		typehead: {
	// 			name: 'tags',
	// 			local: [],
	// 		}
	// 	});
});	
  <?php $min=0; //$max=200;
  ?>
	var moreproducts ="";
	var isChanged = false;
	var min=  "<?php  echo $min; ?>";
	
	var price_range = 'price_range=0-200';
	var sort_order= "asc";
	var isSlide= false;
	var slideMax = "<?php  echo isset($max)?$max:''; ?>";
	<?php
		$range = "";
		if(isset($_GET['price_range']) && $_GET['price_range']!=""){
			$range = $_GET['price_range'];
			$range = (explode("-",$range));
			$min = $range[0];
			$max = $range[1];
		}
	?>
	var range = "<?php $range?>";
	
	function startProductAutoLoading()
	{
		stopProductAutoLoading();
		moreproducts = setInterval
			(
				function()
				{
					if(g_gettingmore == false && getScrollTop() >= $(".tab-content").height()-400)
					{
						if(isChanged)
						{
							getMoreProducts(false,isChanged);
						}
						else
						{
							getMoreProducts();
						}
					}
				},1000
			);
	}
	
	function stopProductAutoLoading()
	{
		clearInterval(moreproducts);
	}
	var g_gettingmore = false;
	function getMoreProducts(refresh,addwhere)
	{
		
		if(refresh != undefined && refresh != false)
		{
			$("#respmesg").remove();
			$("#blockView_ul").html("");
			//$("#listView").html("");
			$("#product_count").val(0);
		}
		if(addwhere == undefined){addwhere = "";}
		$("#loader").show();
		g_gettingmore = true;
		<?php 
			if(isset($addtourl))
			{
				echo "addwhere += '".$addtourl."';";
			}
		?>
		var url = "<?php if(isset($more_products_url))echo $more_products_url; ?>"+$("#product_count").val()+"?"+addwhere;
		$.ajax
		(
			{
				url : url,
				async: false,
				success : function(data)
						{	
							
						//	debugger;						
							try
							{
								var response = JSON.parse(data);
							}
							catch (exc)
							{								
								stopProductAutoLoading();
								$("#respmesg").remove();
									if($(".thumbnails li").length == 0)
									{
										$(".normal_list").before("<h4 id = 'respmesg' style='color:red'>"+data+"</h4>");
									}	
								$("#loader").hide();
								return;
							}
							if(response.status == "success")
							{
								addGridView(response.data);

								if(response.cat_flag==1){
									
								var i =0;
								var total_cat= response.categoryData.length;
								$.each(response.categoryData, function(index, element) {
									
								
								strHtml = "";

								var line =(total_cat !=i)?'<hr style="margin-left:0px; margin-bottom:5px;" />':"";
								var link = "<?php echo $category_link?>"+"&category_search="+element.category_id;
								strHtml +='<li class="nav-item"><a class="nav-link" href="'+link+'">'+element.category_name+'</a></li>'+line+'<br />';
										$("#cat_list").append(strHtml);
										i++;
								});
								}
								//addPagination(response.data);
								//arrangeThumbnails();
								$("#product_count").val(parseInt($("#product_count").val())+9);
								g_gettingmore = false;
								$("#load-more-product").removeClass('d-none');
								//$("#myTab").show("slow");
								//bindaddcart();
							}
							else
							{
								stopProductAutoLoading();
								if(response.data != undefined)
								{
									$("#respmesg").remove();
									if($(".thumbnails li").length == 0)
									{
										$(".normal_list").before("<h4 id = 'respmesg' style='color:red'>"+response.data+"</h4>");
										//$("#myTab").hide("slow");
									}									
								}
								$("#load-more-product").addClass('d-none');
							}
							$("#loader").hide();
						},
				fail : function(data)
						{
							displayalertblock("Cannot Load More Products");
							$("#loader").hide();
						},
			}
		);
	}
	function addGridView(data){
		var userid = "<?php echo (isset($_SESSION['userid']) && $_SESSION['userid'] !="")?$_SESSION['userid']:""?>";
		var base_url = "<?php echo base_url();?>";
		var product_path = "<?php echo product_path();?>";
		var strHtml = "";
		var img = "";
		var price = "";
		$.each(data, function(index, element) {
			strHtml = "";
			if(element.is_local_product_img == 1){
				img = product_path+element.product_image[0];
			}else{
				
				img = element.product_image[0];
			}
			//max_price.push(element.max_price);
			if(parseInt(element.max_price) > parseInt(element.min_price)){
				price = '$'+element.min_price+' - $'+element.max_price;
			}else{
				price = '$'+element.min_price;
			} 
			strHtml+='<div class="row mt-3">'+
			'<div class="col-xs-3 col-lg-4" >'+
			'<a href='+base_url+'product/detail/'+encodeURIComponent(btoa((element.product_name+"_"+element.product_id)))+' class="text-center" target="_new">'+
			 '<img class="img-fluid" id="pdt-img" src="'+img+'" data-toggle="tooltip" title="'+element.product_name+'"/>'+
        '</a>'+'<a href='+base_url+'product/detail/'+encodeURIComponent(btoa((element.cleanUrl+"_"+element.product_id)))+'  class="wordwrap" target="_new">'+
            
        '</a>'+
		 '</div>'+
    	'<div class="col-sm-4 col-md-12 col-lg-4">'+
		'<a style="font-size:20px;" href='+base_url+'product/detail/'+encodeURIComponent(btoa((element.cleanUrl+"_"+element.product_id)))+'  class="wordwrap" target="_new">'+
            '<span>'+element.product_name+'</span>'+
        '</a>'+
		'<div><p style="font-size:20px;" class="sellers_name">By:'+element.brand+'</p>'+
        '<div class="PriceTag">'+
            '<h4>'+price+'</h4>'+
        '</div></div></div>'+
        '<div class="col-sm-8 col-md-12 col-lg-4 pull-right" style="font-size:18px;">'+
        '<ul>';
		var variantKey = Array();
		var conditionKey ="";
		var conditionValue = Array();
		$.each(element.details,function (key, value){ 
			conditionKey = key;
			$.each(value,function(k, v){ 
				conditionValue.push(k);
				if(typeof(v.variant) !=="undefined"){
					$.each(v.variant,function(vk,vv){
						if(vk =="condition"){return true;};
						var array = $.map(vv, function(value, index) {
							return [value];
						});
						if(typeof(variantKey[vk]) !=="undefined"){
							$.each(array, function(vari, vark){
							//$.each(variantKey[vk], function(vari, vark){
								if($.inArray(vark,variantKey[vk]) == -1){
									variantKey[vk].push(vark);
								}
								
							});
						}else{

							variantKey[vk] = array;
						}
					});
				}
			});
		strHtml += '<li class="bulletPoints" style="text-transform:capitalize; display:inline-block;">'+conditionKey+': <b style="color: #008cba;">'+conditionValue.join(', ')+'</b></li>';
			for(k in variantKey){
			 if (typeof variantKey[k] !== 'function') {
				var array = $.map(variantKey[k], function(value, index) {
					return [value];
				});
         		strHtml +='<li class="bulletPoints">'+k+': <b style="color: #008cba;">'+array.join(', ')+'</b></li>';
    			}
		
			}
           strHtml +='</ul></div>';
        if(userid != element.seller_id){
			strHtml +='<a href="javascript:void(0)" onclick="submitForm('+element.pv_id+',1)" class="btn addtocart pull-right mt-3" style="margin-left:20px;" id="addToCartBtn"><i class="fa fa-shopping-cart"></i>ADD TO CART</a>';
		}
    strHtml +='</div><hr style="margin-left:0px;">'+
    '<div class="clearfix"></div>'+
'</div></div></div>';
		$("#blockView_ul").append(strHtml);
        });
		
	});}


	function arrangeThumbnails()
	{
		var maxheight = 0;
		$(".thumbnails li").css("height","auto");
		$(".thumbnails li").each(function(){maxheight = Math.max($(this).height(),maxheight)});
		$(".thumbnails li").css("height",maxheight+"px")
	}	
	var g_autoloading = false;
	function sortProducts(oObj,is_set,offautoloading)
	{
		if(g_autoloading == true)offautoloading = g_autoloading;
		if($(oObj).val() == '')
		{
			isChanged = "";
			getMoreProducts(true);
			return false;
		}
		if(isSlide){
			if($("#sort_by option:selected").val()){
				isChanged = $("#sort_by option:selected").val()+"&"+$("#priceRange").val();
			}else{
				isChanged = $(oObj).val();
			}
		}
		else{
			isChanged = $(oObj).val();
		}
		
		
		getMoreProducts(true,isChanged);
		if(offautoloading == undefined)
		{
			startProductAutoLoading();		
		}
		else
		{
			stopProductAutoLoading();
		} 
	}
	function getScrollTop()
	{
		var topscroll = 0;
		if(topscroll == 0)topscroll = $("html,body").scrollTop();
		if(topscroll == 0)topscroll = window.scrollY;
		return topscroll;
	}
	function loadMoreProduct(){
		if($("#sort_by option:selected").val() && $("#priceRange").val()){
			isChanged = $("#sort_by option:selected").val()+"&"+$("#priceRange").val();
		}else if($("#priceRange").val()){
			isChanged = $("#priceRange").val();
		}else if($("#sort_by option:selected").val()){
			isChanged = $("#sort_by option:selected").val();
		}else{
			isChanged = "";	
		}
		if(isChanged !=""){ 
		
			getMoreProducts(false,isChanged);
			
		}else{
			getMoreProducts();
			
		}
		return false;
	}
	function submitForm(pv_id,qty){
		$("#pv_id").val(pv_id);
		$("#qty").val(qty);
		var path = "<?php echo base_url('cart/addtocart/'); ?>"+pv_id;
		$("#formSubmit").attr('action',path);
		$("#formSubmit").submit();
	}

	$(function() {
	var min="<?php echo $min; ?>";
	var max="<?php echo $max; ?>";
	//$('#minimumRange').val(min);
	//$('#maximumRange').val(max);
$("#slider-label").text('Price Range: '+min+' - '+max);
	$("#price-range").slider({
	  range: true,
       min: 0, 
       max: slideMax,
		values: [ min, max], 
		slide: function(event, ui) {
			$("#priceRange").val(ui.values[0] + "-" + ui.values[1]);
			$("#slider-label").text('Price Range: '+ui.values[0] + "-" + ui.values[1]);
			$('#minimumRange').val(ui.values[0]);
			$('#maximumRange').val(ui.values[1]);	
			isSlide= true;
				}		
  		});

	});
function setParam(value,from){
	<?php $q = $_SERVER['QUERY_STRING']; ?>
	var qstr = "<?php echo $q ?>";
	var issetProductName = "<?php echo (isset($_GET['product_name']))?$_GET['product_name']:"";?>"; 
	var issetMinPrice = "<?php echo (isset($_GET['min_price']))?$_GET['min_price']:"";?>";
	var issetPriceRange = "<?php echo (isset($_GET['price_range']))?$_GET['price_range']:"";?>";
	var issetTag = "<?php echo (isset($_GET['keywords']))?$_GET['keywords']:""; ?>";

	if(from == "product_name" && issetProductName){	
		var product_name = "product_name="+issetProductName;
		rep = qstr.replace(new RegExp(product_name, "g"),"product_name="+value);
		<?php if (isset ($_GET['page'])){?>
			rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}
	else if(from == "keywords" && issetTag ){
		var tag = "keywords="+issetTag;
		rep = qstr.replace(new RegExp(tag, "g"),"keywords="+value);
		<?php if (isset ($_GET['page'])){?>
			rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}
	else if(from == "min_price" && issetMinPrice){
		var min_price = "min_price="+issetMinPrice;
		rep = qstr.replace(new RegExp(min_price, "g"),"min_price="+value);
		<?php if (isset ($_GET['page'])){?>
			rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}
	else if(from == "price_range" && issetPriceRange){
		var price = "price_range="+issetPriceRange;
		rep = qstr.replace(new RegExp(price, "g"),"price_range="+value);
		<?php if (isset ($_GET['page'])){?>
			rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}else{ 
		var querystr = "<?php echo $_SERVER['QUERY_STRING'];?>" + "&"+from+"="+ value;
		var rep = querystr;
		<?php if (isset ($_GET['page'])){ ?>
			rep = removeURLParameter(querystr, "page");
		<?php } ?>
		document.location.search = rep;
	}
}

function removeURLParameter(qstrs, parameter) {
       var prefix= encodeURIComponent(parameter)+'=';
        var pars= qstrs.split(/[&;]/g);
        for (var i= pars.length; i-- > 0;) {    
            	if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                	pars.splice(i, 1);
            	}
        	}
		qstrs= pars.join('&');
	    return qstrs;
	
}
$(".range-input").on("keypress keyup blur",function (event) {    
           $(this).val($(this).val().replace(/[^\d].+/, ""));
            if ((event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });
$(".keywords").keypress(function(e) {
    if(e.which == 13) {
		var value = $('.keywords').val();
		setParam(value,'keywords');
    }
});
$('#price-range').on('slidechange',function(event,ui){ var value =  $('#priceRange').val(); /*sortProducts($('#priceRange'))*/ setParam(value,'price_range');});
function go(i){
	var minr = $('#minimumRange'+i).val();
    var maxr = $('#maximumRange'+i).val();
	var min="<?php echo $min; ?>";
	var max="<?php echo $max; ?>";
	minr = parseInt(minr);
	maxr = parseInt(maxr);
	var value = "";
	if(isNaN(minr) && isNaN(maxr)){
		value = min+"-"+max;
		setParam(value,'price_range');	
	}else{
		if(minr>maxr){
			value = min+"-"+max;
			setParam(value,'price_range');
		}else{
			value = minr+"-"+maxr;
			setParam(value,'price_range');
		}
	}
}
$(document).on('click','.sort_by',function(){
	var value = $(this).val();
	var from = $(this).attr('data-from');
	setParam(value,from);
});

/*$('.addToWishlistBtn').click(function(){
    var product_variant_id = $(this).attr("data-product_varient_id");
    var product_id = $(this).attr("data-product_id");
    var buttonObj = $(this);
    var date = new Date();
			var n = date.toDateString();
			var time = date.toLocaleTimeString();
			var dt = n + ' ' + time;
  
		var user_id = "<?php if(isset($_SESSION['userid'])){ echo $_SESSION['userid'];  }
			else{
				"";
			}
		?>";
        if(user_id == ""){
            window.location.replace("<?php echo base_url('login');?>");

        }
		var datetime = dt;
    	if(product_variant_id !=""){
			$.ajax({	
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>product/save_for_later",
				data:{"user_id":user_id,"product_id":product_id,"product_variant_id":product_variant_id,"created_date":datetime},
				success: function(response){
					if(response.status == 1){
						$('.message-notification').modal('show');
						setTimeout(function() {
							$('.message-notification').modal('hide');
  							}, 4000);
						$('#change-message').text("Product saved for later");
						buttonObj.after('<span class="btn sf1" style="color:#F05928"  title="Save for later"><i class="fa fa-heart"></i></span>');
						$(".tooltip").remove();
						$('.sfl').tooltip();  
						buttonObj.remove();	
					}
					
				}
				
				
			});
			return false;
		}
	});*/
$('.panel-heading').on('click',function(){$(this).toggleClass( "active" );})
$('.clear-filter').on('click', function(){
	//alert();
	window.location.href = "<?php echo base_url('product');?>"
});
$("#crossBtn").on('click',function(){
	$('#searchBar').val('');
})
// $(".btn-circle").on("dblclick",function(){    
//     $(this).attr('disabled', true);
// });

$(".btn-circle").on('click',function(){
	if($('#sideNav').hasClass('show')){
		$("#filterInnerDiv").html("Advance Search");
		$("#semiBtn").html('<i class="fas fa-angle-down navbar-toggler" data-toggle="collapse" data-target="#sideNav" aria-controls="sideNav" aria-expanded="false" aria-label="Toggle navigation"></i>');
	}
	else{
		$("#filterInnerDiv").html('<span class="error">Close</span>');
		$("#semiBtn").html('<i class="fa fa-times error" navbar-toggler" data-toggle="collapse" data-target="#sideNav" aria-controls="sideNav" aria-expanded="false" aria-label="Toggle navigation"></i>');
	}
	
});

// $( "#Submit" ).click(function() {
// 		prd_id = $("#myModal3 #modal_product_id").val();
// 		prd_v_id = $("#myModal3 #modal_product_v_id").val();
// 		$.ajax({
// 			type: "POST",
// 			url: "<?php echo base_url()?>home/add_wishlist_category",
// 			dataType: "json",
// 			cache:false,
// 			data: $('form#myform').serialize(),
// 			success: function(response){
// 				//alert(response.data.id);
// 				$('#myModal3').modal('hide');
// 				$('#change-message').text("");
// 					$('#change-message').text("Product saved for later");
// 					$('#message-notification').modal('show');
// 					setTimeout(function() {
// 						$('#message-notification').modal('hide');
// 						}, 4000);
// 					$('.addToWishlistBtn[data-id = '+prd_id+'-'+prd_v_id+']').replaceWith( '<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>' );
// 					/*$('.alreadyExistingCategories').append($('<option>', {
// 						value: data.id,
// 						text: data.category_name
// 					}));
// 					*/
// 					$(".alreadyExistingCategories option[value='0']").remove();
// 					$(".alreadyExistingCategories option[value='1']").remove();
// 					$('.alreadyExistingCategories').append('<option value="'+response.data.id+'">'+response.data.category_name+'</option>');
// 			},
// 			error: function(){
// 				alert("Error");
// 			}
// 		});
// 	});

</script>

