<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<script src="<?php echo assets_url('front/js/jquery.rateyo.min.js');?>"></script>
<script src = "<?php echo assets_url('common/js/jquery-ui.min.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script>

<?php 
	$current_link = $_SERVER['REQUEST_URI'];
	$url=strtok($_SERVER["REQUEST_URI"],'?');
	$all_link = current_url();
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
<script>	
$(document).ready(function(e) {

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
	if(size_li < x){
		$('#loadMore').hide();
	} else if(size_li == x){
		$('#loadMore').hide();
	}
	brand_li = $("#brand_slider li").length;
    max_li=5;
	if(brand_li < max_li){
		$('#loadMore2').hide();
	} else if(brand_li == max_li){
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
	
    $('#category_slider li:lt('+x+')').show();
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

	$('#brand_slider li:lt('+max_li+')').show();
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
	var slideMax = "<?php  echo $max; ?>";
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
//	alert(minr +"---"+ maxr);
	var min="<?php echo $min; ?>";
	var max="<?php echo $max; ?>";
	//alert(min +"---"+ max);
	minr = parseInt(minr);
	maxr = parseInt(maxr);
	//alert(minr +"---"+ maxr);
	var value = "";
	if(isNaN(minr) && isNaN(maxr)){
		$( '<div class=""><p class="error">Only numbers allowed</p></div>' ).insertAfter( ".price-row" );
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
	qstr = qstr.replace("&min_price=desc", "");
	qstr = qstr.replace("&min_price=asc", "");
	qstr = qstr.replace("&product_name=asc", "");
	qstr = qstr.replace("&product_name=desc", "");
	console.log(qstr+"&"+from+"="+value);
	window.location.href = "SearchResults?"+qstr+"&"+from+"="+value;
});
$('.panel-heading').on('click',function(){$(this).toggleClass( "active" );})
$('.clear-filter').on('click', function(){
	//alert();
	window.location.href = "<?php echo base_url('product');?>"
});
$("#crossBtn").on('click',function(){
	$('#searchBar').val('');
})

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
$( ".pg-tag-next" ).insertAfter( "li:last" );
$( ".inputClass" ).insertAfter( ".pg-tag-next" );
</script>