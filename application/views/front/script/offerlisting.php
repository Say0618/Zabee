<link href="<?php echo assets_url('front/css/jquery-picZoomer.css')?>" rel="stylesheet" />
<script src="<?php echo assets_url('front/js/jquery.prettyPhoto.js')?>"></script>
<script>
var minPrice = "<?php echo 0;//($minPrice)?$minPrice:0; ?>";
var maxPrice = "<?php echo 0;//($maxPrice)?$maxPrice:0; ?>";
var shipping_id = "";
var shipping_title = "";
var shipping_price = "";
var sid;
var shipping_ids;
function changeShippingMethod(){
	$(".shipping_link").each(function(index,value){
		var lowestShipping_id = $(this).attr('data-ship');
		shipping_id = $("#shipping"+lowestShipping_id).attr('data-shipping_id');
		shipping_title = $("#shipping"+lowestShipping_id).attr('data-title');
		shipping_price = $("#shipping"+lowestShipping_id).attr('data-price');
		shipping_ids = $("#shipping_method"+index).attr('data-id');
		$("+shipping"+lowestShipping_id).attr("checked", "checked");
		// console.log(shipping_ids);
		var price="";
		if(shipping_price == 0){
			price = "Free Shipping"; 
		}else{ 
			price = "US $"+shipping_price;
		} 
		
		$("#shipping_method"+index).html(' <a href="javascript:void(0)" class="shipping_title" data-link_Id="'+lowestShipping_id+'" data-link_Title="'+shipping_title+'" data-link_Price="'+shipping_price+'" checked="checked" onClick="openModal('+index+','+lowestShipping_id+')"><span class="price">'+price+'</span> via <span class="shippingTitle">'+shipping_title+'</span></a>');
	});
	//$("#shipping_method").html(' <a href="javascript:void(0)" class="shipping_title" data-toggle="modal" data-target="#choose_shipping_method"><span class="price">'+price+'</span> via '+shipping_title+'</a>');
}
$(document).on("change",'.shipping_method',function(){
	shipping_id = $(this).attr('data-shipping_id');
	shipping_title = $(this).attr('data-title');
	shipping_price = $(this).attr('data-price');
	var lowestShipping_id = $(".shipping_method:checked").val();
	// $("+shipping"+lowestShipping_id).attr("checked", "checked");

	$(this).attr('data-ship',lowestShipping_id);
	var price="";
	var id = $("#indexId").val();
	
	if(shipping_price == 0){
		price = "Free Shipping"; 
	}else{ 
		price = "US $"+shipping_price;
	}
	$('#shipping_method'+id).html(' <a href="javascript:void(0)" class="shipping_title" data-link_Id="'+lowestShipping_id+'" data-link_Title="'+shipping_title+'" data-link_Price="'+shipping_price+'" onClick="openModal('+id+','+lowestShipping_id+')"><span class="price">'+price+'</span> via '+shipping_title+'</a>');
});
function openModal(id,shipping_id){
	//$(".shippingRow").addClass("d-none");
	sid = $("#shipping_method"+id).attr('data-id');
	shipping_ids = sid.split(",");
	for(var i = 0; i< shipping_ids.length;i++){
		var x = shipping_ids[i];
		$('.shipping'+x).removeClass('d-none');
	}
	$("#indexId").val(id);
	$("#shipping"+shipping_id).trigger('click');
	$("#choose_shipping_method").modal('show');
}
$(document).ready(function(e){
	$(".offerRow").last().css("border-bottom", "none");
	changeShippingMethod();
	$("a[rel^='offerListing']").prettyPhoto();
	
	// $( "#Submit" ).click(function() {
	// 	prd_id = $("#myModal3 #modal_product_id").val();
	// 	prd_v_id = $("#myModal3 #modal_product_v_id").val();
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "<?php echo base_url()?>home/add_wishlist_category",
	// 		dataType: "json",
	// 		cache:false,
	// 		data: $('form#myform').serialize(),
	// 		success: function(response){
	// 			//alert(response.data.id);
	// 			$('#myModal3').modal('hide');
	// 			$('#change-message').text("");
	// 				$('#change-message').text("Product saved for later");
	// 				$('#message-notification').modal('show');
	// 				setTimeout(function() {
	// 					$('#message-notification').modal('hide');
	// 					}, 4000);
	// 				$('.addToWishlistBtn[data-id = '+prd_id+'-'+prd_v_id+']').replaceWith( '<span class="already-saved btn btn-left" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>' );
	// 				/*$('.alreadyExistingCategories').append($('<option>', {
	// 					value: data.id,
	// 					text: data.category_name
	// 				}));
	// 				*/
	// 				$(".alreadyExistingCategories option[value='0']").remove();
	// 				$(".alreadyExistingCategories option[value='1']").remove();
	// 				$('.alreadyExistingCategories').append('<option value="'+response.data.id+'">'+response.data.category_name+'</option>');
	// 		},
	// 		error: function(){
	// 			alert("Error");
	// 		}
	// 	});
	// });
	return false;
});
$('.condition_class').click(function(){
	$('#loader').show();
	var val =$('.condition_class:checkbox:checked');
	var queryString = "";
	var commaLength = $(val).length;
	var redirectLink = window.location.href.split('?')[0]+"?filter=";
	$(val).each(function(index, element) {
       queryString += $(element).val()
	   if((commaLength-1) != index){
	   	queryString += ",";
	   }
    });
	window.location = redirectLink+encodeURIComponent(btoa(queryString));
});
$('.shipping_class').click(function(){
	$('#loader').show();
	var val =$('.shipping_class:checkbox:checked');
	var queryString = "";
	var commaLength = $(val).length;
	var redirectLink = window.location.href.split('?')[0]+"?shipping=";
	$(val).each(function(index, element) {
       queryString += $(element).val()
	   if((commaLength-1) != index){
	   	queryString += ","; 
	   }
    });
	window.location = redirectLink+encodeURIComponent(btoa(queryString));
});
$('.cartAdd').on('click',function(){
	var pv_id = $(this).attr('data-product_variant_id');
	var allShippingIds = $(this).attr('data-available_shipping_ids'); 
	// var maxQty = parseInt($("#qty").val());
	// var qty = parseInt($("#product_qty").val());
	qty=1;
	var row_id = $(this).attr('data-row');
	shipping_id = $("#shipping_method"+row_id+" a.shipping_title").attr('data-link_Id');
	shipping_title = $("#shipping_method"+row_id+" a.shipping_title").attr('data-link_Title');
	shipping_price = $("#shipping_method"+row_id+" a.shipping_title").attr('data-link_Price');
	// alert(pv_id+","+shipping_id+","+shipping_title+","+shipping_price);
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url()?>cart/addtocart",
			data:{"pvid":pv_id,"qty":qty,'is_ajax':true,'shipping':{'shipping_id':shipping_id,'title':shipping_title,'price':shipping_price,'allShippingIds':allShippingIds}},
			success: function(response){
				if(response.status == 1){
					location.href = "<?php echo base_url('cart');?>";
				}
			}
		});
});
</script>
