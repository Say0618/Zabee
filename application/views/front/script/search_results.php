<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<script src="<?php echo assets_url('front/js/jquery.rateyo.min.js');?>"></script>
<script src = "<?php echo assets_url('common/js/jquery-ui.min.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script>

<?php 
	$current_link = $_SERVER['REQUEST_URI'];
	$url=strtok($_SERVER["REQUEST_URI"],'?');
	$all_link = current_url();
	if(!empty($this->input->get('category_search'))){
		$category_link = str_replace('&category_search='.$$this->input->get('category_search'), '', $_SERVER['QUERY_STRING']);
	
		$category_link = $url."?".$category_link;
	}else{
		$category_link = $current_link.'/searchResults?';
	}
	if(!empty($this->input->get('brands_search'))){
		$brand_link =str_replace('&brands_search='.$this->input->get('brands_search'), '', $_SERVER['QUERY_STRING']);
		$brand_link = $url."?".$brand_link;
	}else{
		$brand_link = $current_link;
	}
	?> 
<script>

$.validator.addMethod( "nowhitespace", function( value, element ) {
return this.optional( element ) || /^\S+$/i.test( value );
}, "No white space please" ); 

$.validator.addMethod( "lettersonly", function( value, element ) {
return this.optional( element ) || /^[a-z]+$/i.test( value );
}, "please enter a valid name" );

jQuery.validator.addMethod("numbersAndDecimalsOnly", function(value, element) 
{
	return this.optional(element) || /^[0-9]\d*(\.\d{0,2})?$/i.test(value);
}, "Numbers with 2 decimals only please"); 

jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
);

$("#request-form").validate({
 // errorElement : 'div',
     rules: {
        email: {
          nowhitespace: true,
          required: true,
          email: true
        },
        first_name: {
          required: true,
          nowhitespace:true,
          lettersonly:true,
          minlength: 3
        },
        last_name: {
          required: true,
          nowhitespace:true,
          lettersonly:true,
          minlength: 3
        }, 
		contact: {
          minlength: 11,
		  maxlength: 14
        },
		price:{
			required: true,
			numbersAndDecimalsOnly: true,
		}        
      }, 
	messages: {
		email: {
		required: 'Email address required.',
		email: 'Please enter a <em>valid</em> email address.',
		},
	},
 	errorElement : 'span',
	errorPlacement: function(error, element) {
		console.log(error);
		console.log(element);
		if(element.attr("name") == "terms") {
		error.appendTo( element.parent() );
		} else {
		error.appendTo(element.parent().parent());
		}
	},
	submitHandler: function(){
		submitdata();
	}
});

$(document).ready(function(e) {

	$('#filter-sidebar .filter-column #brand_slider li:gt(4)').hide();
	$('#brand_slider li:gt(4)').hide();
	
	$("#minimumRange1").on("keyup",function(){
		var regex = /^(\d+\.?\d*|\.\d+)$/;
		min = $("#minimumRange1").val();
		if(regex.test(min)){
		}else{
			$("#minimumRange1").val(min.slice(0, -1));
		}
	});

	$("#maximumRange1").on("keyup",function(){
		var regex = /^(\d+\.?\d*|\.\d+)$/;
		max = $("#maximumRange1").val();
		if(regex.test(max)){
		}else{
			$("#maximumRange1").val(max.slice(0, -1));
		}
	});

	$("#goto-btn").attr('disabled', true);

	$("#page_number").on("keyup", function(){
		if($("#page_number").val() != ""){
			$("#goto-btn").attr('disabled', false);
		}else{
			$("#goto-btn").attr('disabled', true);
		}
	});

	
	var url_string = window.location.href;
	var url = new URL(url_string);
	var c = url.searchParams.get("product_name");
	var d = url.searchParams.get("min_price");
	if(c != null && c=="asc"){
		$('#myselect option:selected').removeAttr('selected');
		$('#myselect #product_name_asc').attr('selected','true');
	} else if(c != null && c=="desc"){
		$('#myselect option:selected').removeAttr('selected');
		$('#myselect #product_name_desc').attr('selected','true');
	}
	if(d != null && d=="asc"){
		$('#myselect option:selected').removeAttr('selected');
		$('#myselect #min_price_asc').attr('selected','true');
	} else if(d != null && d=="desc"){
		$('#myselect option:selected').removeAttr('selected');
		$('#myselect #min_price_desc').attr('selected','true');
	}
	size_li = $("#category_slider li").length;
    x=5;
	if(size_li <= x){
		$('#loadMore').hide();
	}

	brand_li = $("#brand_slider li").length;
    max_li=5;
	if(brand_li <= max_li){
		$('#loadMore2').hide();
	}
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
	
	$('#filter-sidebar .filter-column #category_slider li:gt('+x+')').hide();
	$('#category_slider li:gt('+x+')').hide();
    // $('#category_slider li:lt('+x+')').show();
    $('#loadMore').click(function () {
        x =  size_li;
	    $('#category_slider li:lt('+x+')').show();
         $('#showLess').show();
        if(x == size_li){
            $('#loadMore').hide();
        }
    });
    $('#showLess').click(function () {
        x = 5;
	    $('#category_slider li').not(':lt('+x+')').hide();
        $('#loadMore').show();
         $('#showLess').show();
        if(x == 5){
            $('#showLess').hide();
        }
    });

	$('#filter-sidebar .filter-column #category_slider #loadMore').click(function () {
        x =  size_li;
	    $('#filter-sidebar .filter-column #category_slider li:gt(0)').show();
         $('#filter-sidebar .filter-column #category_slider #showLess').show();
        if(x == size_li){
            $('#filter-sidebar .filter-column #category_slider #loadMore').hide();
        }
    });

	$('#filter-sidebar .filter-column #category_slider #showLess').click(function () {
        x = 5;
	    $('#filter-sidebar .filter-column #category_slider li:gt(4)').hide();
        $('#filter-sidebar .filter-column #category_slider #loadMore').show();
         $('#filter-sidebar .filter-column #category_slider #showLess').show();
        if(x == 5){
            $('#filter-sidebar .filter-column #category_slider #showLess').hide();
        }
    });

	$('#filter-sidebar .filter-column #brand_slider li:gt('+x+')').hide();
	$('#brand_slider li:gt('+x+')').hide();
	// $('#brand_slider li:lt('+max_li+')').show();
    $('#loadMore2').click(function () {
        max_li =  brand_li;
		$('#brand_slider li:lt('+max_li+')').show();
         $('#showLess2').show();
        if(max_li == brand_li){
            $('#loadMore2').hide();
        }
    });
    $('#showLess2').click(function () {
		max_li = 5;
		$('#brand_slider li').not(':lt('+max_li+')').hide();
        $('#loadMore2').show();
        $('#showLess2').show();
        if(max_li == 5){
            $('#showLess2').hide();
        }
    });

	$('#filter-sidebar .filter-column #brand_slider #loadMore2').click(function () {
        x =  size_li;
	    $('#filter-sidebar .filter-column #brand_slider li:gt(0)').show();
         $('#filter-sidebar .filter-column #brand_slider #showLess2').show();
        if(x == size_li){
            $('#filter-sidebar .filter-column #brand_slider #loadMore2').hide();
        }
    });

	$('#filter-sidebar .filter-column #brand_slider #showLess2').click(function () {
        x = 5;
	    $('#filter-sidebar .filter-column #brand_slider li:gt(4)').hide();
        $('#filter-sidebar .filter-column #brand_slider #loadMore2').show();
         $('#filter-sidebar .filter-column #brand_slider #showLess2').show();
        if(x == 5){
            $('#filter-sidebar .filter-column #brand_slider #showLess2').hide();
        }
    });

});	
$(function () {
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
	var max="<?php echo isset($max)?$max:''; ?>";
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
function setParam(value,from, select=""){
	var baseUrl = "";
	<?php $q = $_SERVER['QUERY_STRING']; ?>
	var qstr = "<?php echo $q ?>";
	var issetProductName = "<?php echo (isset($_GET['product_name']))?$_GET['product_name']:"";?>"; 
	var issetShipping = "<?php echo (isset($_GET['fs']))?$_GET['fs']:"";?>"; 
	var issetMinPrice = "<?php echo (isset($_GET['min_price']))?$_GET['min_price']:"";?>";
	var issetPriceRange = "<?php echo (isset($_GET['price_range']))?$_GET['price_range']:"";?>";
	var issetTag = "<?php echo (isset($_GET['keywords']))?$_GET['keywords']:""; ?>";
	var issetSort = "<?php echo (isset($_GET['sort']))?$_GET['sort']:"";?>"; 
	if(from == "product_name" && issetProductName){	
		var product_name = "product_name="+issetProductName;
		rep = qstr.replace(new RegExp(product_name, "g"),"product_name="+value);
		<?php if (isset ($_GET['page'])){?>
			rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}else if(from == "fs" && issetShipping ){
		var shipping = "fs="+issetShipping;
		rep = qstr.replace(new RegExp(shipping, "g"),"fs="+value);
		<?php if (isset ($_GET['page'])){?>
		rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}else if(from == "sort" && issetSort){
		var sort = "sort="+issetSort;
		rep = qstr.replace(new RegExp(sort, "g"),"sort="+value);
		<?php if (isset ($_GET['page'])){?>
		rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}else if(from == "keywords" && issetTag ){
		var tag = "keywords="+issetTag;
		rep = qstr.replace(new RegExp(tag, "g"),"keywords="+value);
		<?php if (isset ($_GET['page'])){?>
		rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}else if(from == "min_price" && issetMinPrice){
		var min_price = "min_price="+issetMinPrice;
		rep = qstr.replace(new RegExp(min_price, "g"),"min_price="+value);
		<?php if (isset ($_GET['page'])){?>
			rep = removeURLParameter(rep, "page");
		<?php } ?>
		document.location.search = rep;
	}else if(from == "price_range" && issetPriceRange){
		//alert(issetPriceRange);
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
	if(minr == "" && maxr == ""){
		var minr = $('#filter-sidebar .filter-column #minimumRange'+i).val();
    	var maxr = $('#filter-sidebar .filter-column #maximumRange'+i).val();
	}
//	alert(minr +"---"+ maxr);
	var min="<?php echo $min; ?>";
	var max="<?php echo isset($max)?$max:''; ?>";
	//alert(min +"---"+ max);
	minr = parseInt(minr);
	maxr = parseInt(maxr);
	//alert(minr +"---"+ maxr);
	var value = "";
	if(isNaN(minr) && isNaN(maxr)){
		$(".price-error").removeClass("d-none").addClass("d-block error");
		//value = min+"-"+max;
		//setParam(value,'price_range');	
	}else{
		if(minr>maxr){
			value = min+"-"+max;
			setParam(value,'price_range');
		} else if(typeof minr == 'number' && isNaN(maxr)) {
			value = minr+"-"+max;
			setParam(value,'price_range');
		} else if(typeof maxr == 'number' && isNaN(minr)) {
			value = min+"-"+maxr;
			setParam(value,'price_range');
		} else {
			value = minr+"-"+maxr;
			setParam(value,'price_range');
		}
	}
}
$(document).on('click','.sort_by',function(){
	var value = $(this).val();
	var from = $(this).attr('data-from');
	var stringPath = window.location.toString();
	if($(this).attr("checked")){
		if(from =="min_price"){
			rep = removeParam('min_price', window.location.toString());
		}else if(from =="product_name"){
			rep = removeParam('product_name', window.location.toString())
		}else if(from =="sort"){
			rep = removeParam('sort', window.location.toString());
		}else if(from =="fs"){
			rep = removeParam('fs', window.location.toString())
		}
		rep = removeURLParameter(rep, "page");
		document.location.search = rep;
	}else{
		/*if(from =="product_name" || from == "min_price"){
			min_price = removeParam('min_price', stringPath);
			pathStart = stringPath.split("?")[0];
			stringPath = pathStart+"?"+min_price;
			product_name = removeParam('product_name', stringPath);
			stringPath = pathStart+"?"+product_name;
		}*/
		setParam(value,from);
	}
	
});
/*var selected = localStorage.getItem('selected');
if (selected) {
  $("#myselect").val(selected);
}*/
function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = params_arr.join("&");
    }
    return rtn;
}

$('#myselect').change(function() {
    var value = $(this).val();
	var from = 	$(this).find(':selected').data('from')
	/*localStorage.setItem('selected', $(this).val());*/
	let select = true;
	var dropdown = "dropdown";
	<?php $q = $_SERVER['QUERY_STRING']; ?>
	var qstr = "<?php echo $q ?>";
	console.log(qstr);
	qstr = qstr.replace("&sort=price-desc", "");
	qstr = qstr.replace("&sort=price-asc", "");
	qstr = qstr.replace("&sort=name-asc", "");
	qstr = qstr.replace("&sort=name-desc", "");
	console.log(qstr+"&"+from+"="+value);
	window.location.href = "searchResults?"+qstr+"&"+from+"="+value;
});
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
// 					$('.addToWishlistBtn[data-id = '+prd_id+'-'+prd_v_id+']').replaceWith( '<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved"><i class="fa fa-heart"></i></span>' );
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
	$( ".pg-tag-next" ).insertAfter( "li:last" );
	$( ".inputClass" ).insertAfter( ".pg-tag-next" );
	//$( "#goto-btn" ).insertAfter( ".page" );
	<?php
		if(isset($_SESSION['view']) && $_SESSION['view']=="list"){ ?>
			listView();	
	<?php }
		if(isset($_SESSION['view']) && $_SESSION['view']=="grid"){ ?>
			gridView();
		<?php } ?>
	/*$('.gridButton').on( "click", function() {
		gridView();
	});
	$('.listButton').on( "click", function() {
		listView();
	});*/
// List View	
function listView() {
	$("#account-sidebar").css("z-index", "1200");
	$(".image-center-parent").attr("style", "min-height: 133px; height: auto !important;");
	$(".image-center-parent a div").attr("style","left: 3px !important");
	maxwidth = "<?php echo (!$detect->isMobile())?"150px":"103px"?>";
	$(".polaroid a img").attr("style", "max-width:"+maxwidth+" !important");
	// console.log($("#showview .polaroid .image-center-parent").forEach(function(item){
	// 	item.children('div')[0]});
	// $("#showview .col-6").removeClass("col-sm-4");
	// $("#showview .col-6").addClass("col-sm-12");
	// $("#showview .polaroid").css("height", "170px");
	// $("#showview .image-center-parent").addClass("row");
	// $("#showview .image-center-parent .cart-buttons").addClass("col-2 pr-0");
	$("#showview .image-center-parent img").css("left", "15px");

	// var data = $("#showview").children("div");
	// for (let index = 0; index < data.length; index++) {

	// 	$("<div>",{class: "product-container col-10 pt-4 pr-3"}).appendTo("#"+index+" .image-center-parent");
	// 	$("<div>",{class: "row"}).appendTo("#"+index+" .image-center-parent .product-container");
		
	// 	$($("#"+index+" .product-container .row").children('div')[0]).detach().appendTo("#"+index+" .image-center-parent .product-container .row");
	// 	$("#"+index+" .zabee-product-name").html($("#"+index+" .fullname").attr("title"));
	// 	$($("#"+index+" .image-center-parent").children('div')[0]).detach().appendTo("#"+index+" .image-center-parent .product-container .row");
	// 	$($("#"+index+" .image-center-parent .product-container .row").children('div').eq(1)).removeClass("text-center position-absolute b-0");
	// 	$("<div>",{class: "row inner"}).appendTo("#"+index+" .image-center-parent .product-container .row");
	// 	$("#"+index+" .top-rated-product-price").detach().appendTo("#"+index+" .image-center-parent .product-container .row .inner");
	// 	$($("#"+index+" .image-center-parent").children("span")).detach().appendTo("#"+index+" .image-center-parent .product-container .row .inner");
	// 	$($("#"+index+" .image-center-parent").children("button")).detach().appendTo("#"+index+" .image-center-parent .product-container .row .inner");
	// 	$($("#"+index+" .image-center-parent").children("a")[1]).detach().appendTo("#"+index+" .image-center-parent .product-container .row .inner");	
	// 	// console.log($("#"+index+" .fullname").attr("title"));
	// }

	// $(".polaroid .top-rated-product-price").removeClass("col-sm-12");
	// $(".polaroid .top-rated-product-price").addClass("col-8 pt-1");
	// $(".polaroid .top-rated-product-price").css("padding-left", "30px");
	// $(".polaroid .inner span").removeClass("btn-left");
	// $(".polaroid .inner span").addClass("col-2 pl-1");
	$(".polaroid .inner span").css("padding-top", "5px");
	// $(".polaroid .inner button").removeClass("btn-left");
	// $(".polaroid .inner button").addClass("col-2 pl-0 pr-3");
	$(".polaroid .inner button").css("padding-top", "3px");
	// $(".polaroid .inner a").removeClass("btn-right");
	// $(".polaroid .inner a").addClass("pl-0 pr-0");
	$(".polaroid .inner a").css("padding-top", "2px");
	$(".polaroid .rateYo").addClass("pl-0");
	// $("<div>",{class: "product-container col-10 pt-4 pr-3"}).appendTo("#showview .image-center-parent");
	// $("<div>",{class: "row"}).appendTo("#showview .image-center-parent .product-container");
	
	// $($("#showview .polaroid .product-container .row").children('div')[0]).detach().appendTo(".image-center-parent .product-container .row");
	// $($("#showview .polaroid .image-center-parent").children('div')[0]).detach().appendTo(".image-center-parent .product-container .row");
	// $($(".image-center-parent .product-container .row").children('div').eq(1)).removeClass("text-center position-absolute b-0");
	// $("<div>",{id:"first", class: "col-sm-12"}).appendTo("#showview .image-center-parent .product-container .row");
	// $("#showview .polaroid").children('div').eq(1).html().insertAfter($("#showview .image-center-parent"));
	// console.log(document.getElementsByClassName('addToCartBtn')[0]);
		$("#showview").parent().css("z-index", "999");

	$(".imgag").addClass( "col-sm-2 col-3" );
	$(".pdBox").removeClass("offset-sm-1 col-sm-10 text-center");
	$(".pdBox").addClass( "col-sm-9 col-8" );
	$(".gridButton").removeClass("active");
	$(".listButton").addClass("active");
	$(".listButton").attr("disabled","disabled");
	$(".gridButton").removeAttr("disabled");
	listShowHide();
	listCss();
}

function listCss(){
	$(".prodname").css("justify-content","left");
	$(".pdBox").css("margin-top","0px");
	$(".product-row").css("padding-top","10px");
	$(".product-row").css("background","white");
	$(".product-row-2").css("border","0");
	$(".product-row-2").css("padding","10px");
	$(".imgag-parent").css("padding","0px");
	$(".img-layout").css("max-height","165px");
	$(".img-layout").css("display","block");
	$(".product-row-2").css({ 'height' : ''});
	$(".card").css({ 'border-right' : '0'});
	$(".card").css({ 'border-left' : '0'});
	$(".second-border").css("border","");
	$(".second-border").css("height","");
}

function listShowHide(){
	$(".dash").show();
	$(".btnsRow").show();
	$(".product-description").show();
	$(".show-2").hide();
	$(".show-3").hide();
}

// Grid View
function gridView(from = ""){
	if(from != ""){
		$("#showview").load(location.href + " #showview>*", "");
	}
	// $(".product-row-2").removeClass("col-12");
	// $(".pdBox").removeClass("col-sm-9 col-8");
	// $(".imgag").removeClass("col-sm-2 col-3");
	// $(".product-row-2").addClass( "col-sm-2" );
	// $(".pdBox").addClass( "offset-sm-1 col-sm-10 text-center" );
	// $(".imgag").addClass( "offset-sm-2 col-sm-8" );
	$(".listButton").removeClass("active");
	$(".listButton").removeAttr("disabled");
	$(".gridButton").addClass( "active" );
	$(".gridButton").attr("disabled","disabled")
	// gridShowHide();
	// gridCss();
}

function gridCss(){
	/*$(".prodname").css("justify-content","center");
	$(".pdBox").css("margin-top","10px");
	$(".product-row").css("padding-top","0px");
	$(".product-row").css("background","none");
	$(".product-row").css("min-height","");
	$(".product-row-2").css("border","5px solid #eeeeee");
	$(".product-row-2").css("padding","0");
	$(".product-row-2").css("background","white");
	$(".product-row-2").css("height","350px");
	$(".imgag-parent").css("padding","10px");
	$(".img-layout").css("max-height","165px");
	$(".img-layout").css("display","block");
	$(".card").css({ 'border-right' : '4px solid #eeeeee'});
	$(".card").css({ 'border-left' : '4px solid #eeeeee'});
	$(".card").css({ 'border-left' : '4px solid #eeeeee'});
	$(".second-border").css("border","2px solid #d7d7d7");
	$(".second-border").css("height","342px");*/
}

function gridShowHide(){
	$(".dash").hide();
	$(".btnsRow").hide();
	$(".product-description").hide();
	$(".show-2").show();
	$(".show-3").show();
}

function sendingViewStatus(view){
	currentView = "&view=" + view;
	var currentLocation = window.location.href+currentView;
	//currentLocation.concat(currentView);
	$.ajax
    ({ 
        url: '<?php echo base_url('product/updateView');?>',
		type: "POST",
		dataType :'JSON',
		data: {view: view},
		success: function(result){
			// console.log(result);
			// if(result.view == 'grid'){
			// 	window.location("")
			// }
			// if(result.view == 'list'){
			// 	listView();
			// }
			location.reload();
		},
    	error: function (request, status, error) {
        	console.log("request: "+request);
        	console.log("status: "+status);
        	console.log("error: "+error);
        }
    });
	// if(view == 'grid'){
	// 	gridView();
	// }
	// if(view == 'list'){
	// 	listView();
	// }
}

function requestModal(query){
	$("#request_modal").modal("show");
}

function submitdata(){
	var userid = $("#userid").val();
	var firstname = $("#first_name").val();
	var lastname = $("#last_name").val();
	var email = $("#email").val();
	var contact = $("#contact").val();
	var price = $("#price").val();
	var condition = $("#condition").val();
	var description = $("#description").val();
	var query = $("#query").val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('home/searched_query');?>",
		data:{'userid':userid, 'first_name':firstname, 'last_name':lastname, 'email':email, 'contact':contact, 'price':price, "condition": condition, 'description':description, 'query':query },
		success: function(response){
			$("#request-complete #msg").removeClass();
			$("#request_modal").modal("hide");
			$("#request-complete").modal("show"); 
			$("#request-complete #title").html(response.title);
			$("#request-complete #msg").addClass(response.class);
			$("#request-complete #msg").html(response.msg);
			setTimeout(function(){
					$("#request-complete").modal("hide"); 
			}, 3000);
		}
	});
}
</script>

