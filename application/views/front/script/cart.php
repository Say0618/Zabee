<link rel="stylesheet" type="text/css" href="<?php echo assets_url('/front/css/cart.css')?>" >
<script type="application/javascript" src="<?php echo assets_url('front/js/formatCurrency.js'); ?>"></script>
<script src="<?php echo assets_url('front/js/cart.js'); ?>"></script>
<script>
/*$("[type='number']").keypress(function (evt) {
    evt.preventDefault();
});*/
/*$("[type='number']").keydown(function (e) {
  var key = e.keyCode || e.charCode;
  if (key == 8 || key == 46) {
      e.preventDefault();
      e.stopPropagation();
  }
});*/
$(function(){  
	$("[type='number']").bind('keypress',function(e){  
			  if(e.keyCode == '9' || e.keyCode == '16'){  
					return;  
			   }  
			   var code;  
			   if (e.keyCode) code = e.keyCode;  
			   else if (e.which) code = e.which;   
			   if(e.which == 46)  
					return false;  
			   if (code == 8 || code == 46)   
					return true;  
			   if (code < 48 || code > 57)  
					return false;  
		 }  
	);  
	$("[type='number']").bind("paste",function(e) {  
		 e.preventDefault();  
	});  
	$("[type='number']").bind('mouseenter',function(e){  
		  var val = $(this).val();  
		  if (val!='0'){  
			   val=val.replace(/[^0-9]+/g, "")  
			   $(this).val(val);  
		  }  
	});  
});

var shipping_id = "";
var shipping_title = "";
var shipping_price = "";
var sid;
var shipping_ids;
var pv_id;
function getShippingData(pv_id,shipping_id,rid){
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url()?>cart/getShippingData",
		data:{'pv_id':pv_id,"single":0},
		success: function(response){
			console.log(response);
			var html = "";
			$(response).each(function(index,value){
				selected = "";
				price = "US $"+value.price;
				if(value.price =="0"){
					price = "Free Shipping";
				}
				if(shipping_id == value.shipping_id){
					selected = 'checked="checked"';
				}
				html +='<tr class="shippingRow shipping'+value.shipping_id+' ship'+pv_id+'">'+
                            '<td>'+
                            	'<div class="custom-control custom-radio mb-3">'+
                                    '<input type="radio" class="custom-control-input shipping_method" '+selected+' data-pv_id="'+pv_id+'" data-shipping_id="'+value.shipping_id+'" data-title="'+value.title+'" data-price="'+value.price+'" id="shipping'+value.shipping_id+pv_id+'" data-rid="'+rid+'"  value="'+value.shipping_id+'">'+
                                    '<label class="custom-control-label radio-custom-input seller-info" for="shipping'+value.shipping_id+pv_id+'">'+value.title+'</label>'+
                                '</div>'+ 
                			'</td>'+
                            '<td>'+value.duration+' days</td>'+
                            //'<td>'+price+'</td>'+
                            //'<td>Not Available</td>'+
							'<td>'+value.description+'</td>'+
                        '</tr>';
			});
			$("#shipping_tbody").html(html);
			
		}
	});
}
$(document).on("change",'.shipping_method',function(){
	shipping_title = $(this).attr('data-title');
	ship_id = $(this).attr('data-shipping_id');
	var price="";
	var id = $("#indexId").val();
	if($("#shipping_method"+id).length > 0){
		rowid = $("#shipping_method"+id).attr('data-row_id');
		pv_id = $("#shipping_method"+id).attr('data-pv_id');
		qty = $("#item_qty"+id).val();
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url()?>cart/cartAjaxupdate",
		data:{'rowid':rid,"shipping_id":ship_id,"qty":qty},
		success: function(response){
			$("#shipping_method"+id).attr('data-ship',ship_id);
			if(response.shipping_price == "Free Shipping"){
				shipping_title = response.shipping_price;
			}
			$('#shipping_method'+id).html('<a href="javascript:void(0)" class="shipping_title_'+pv_id+' stylelink"><strong><span class="shippingTitle">'+shipping_title+'</span></strong></a>');
			//$('#shipping_method'+id).html('<a href="javascript:void(0)" class="shipping_title_'+pv_id+' stylelink" onClick="openModal('+id+','+ship_id+')"><span class="price">'+response.shipping_price+'</span><strong><span class="shippingTitle"> via '+shipping_title+'</span></strong></a>');
			$("#shipping_method"+id+" .price").html(response.shipping_price);
			$("#cart-shipping").html(response.shipping_total);
			$("#cart-total").html(response.subtotal);
			// $(".cart"+response.cart_row+" #ship-price").html(response.shipping_price);
			$("#choose_shipping_method").modal('hide');		
		}
	});
	
});
function openModal(id,shipping_id){
	$(".shippingRow").addClass("d-none");
	// sid = $("#shipping_method"+id).attr('data-id');
	// shipping_ids = sid.split(",");
	sid = $("#shipping_method"+id).attr('data-pv_id');
	rid = $("#shipping_method"+id).attr('data-row_id');
	getShippingData(sid,shipping_id,rid);
	$('.ship'+sid).removeClass('d-none');
	// for(var i = 0; i< shipping_ids.length;i++){
	// 	var x = shipping_ids[i];
	// 	$('.shipping'+x).removeClass('d-none');
	// }
	$("#indexId").val(id);
	//$("#shipping"+shipping_id+sid).trigger('click');
	$("#choose_shipping_method").modal('show');
}
$(document).ready(function(){
	//changeShippingMethod();
	var popup = "";
    /*$(".item_qty").each(function(index,value){
		popup = $(this).attr('data-popup');
		if(popup ==1){
			$(this).popover();  
			$(this).trigger('click');	
		}
	});*/	
	$(".item_qty").on('click',function(){
		var value = $(this).val();
		var max = $(this).attr('max');
		var min = $(this).attr('min');
		value = parseInt(value);
		max = parseInt(max);
		var str = "";
		var str2 = "";
		str = $(this).attr('id')
		str2 = str.split("_");
		var pop = "."+str2[0]+"_pops"
		if(value >= max){
			$(pop).show();
			//$('#checkoutBtn').prop('disabled', true);
		} else if(value < max) {
			$(pop).hide();
			$('#checkoutBtn').prop('disabled', false);
		}
	});
	
	$("#checkoutBtn").on('click',function(){
		flag = true;
		$( '.item_qty' ).each(function(index,value) {
			var qty = $(value).val();
			var max = $(value).attr('max');
			var min = $(value).attr('min');
			var key = $(value).data('row');
			qty = parseInt(qty);
			max = parseInt(max);
			// alert(qty +" "+ max +" "+ min);
			if(qty > max || qty == 0){
				flag = false;
				// $(".cart"+key+" .invalid").removeClass("d-none");
				// $(".cart"+key+" .item-qty-error").addClass("d-none");
				// window.location.reload();
				$(value).next().show();
			}else{
				// $(".cart"+key+" .invalid").addClass("d-none");
				$(value).next().hide();
			}	
		});
		if(flag){
			window.location.href = "<?php echo base_url("checkout")?>";
		}
	});

	/*$(".item_qty").change(function(){
		cart_id = $(this).attr("id");
		cart_id = cart_id[0];
		rowid = cart_id[0]+"[rowid]";
		ship_id = cart_id[0]+"[shipping_id]";
		ship_title = cart_id[0]+"[shipping_title]";
		ship_price = cart_id[0]+"[shipping_price]";
		qty = cart_id[0]+"[qty]";

		rowid = ($("input[name*='"+rowid+"'").val());
		ship_id = ($("input[name*='"+ship_id+"'").val());
		ship_title = ($("input[name*='"+ship_title+"'").val());
		ship_price = ($("input[name*='"+ship_price+"'").val());
		qty = ($("input[name*='"+qty+"'").val());
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url()?>cart/cartAjaxupdate",
			data:{'rowid':rowid, 'shipping_id':ship_id, 'shipping_title':ship_title, 'shipping_price':ship_price, 'qty':qty},
			success: function(response){
				withship = response['cart_total'] + parseFloat($("#cart-shipping").html());
				console.log("cart-total: "+response['cart_total']);
				console.log("ship-total: "+ parseFloat($("#cart-shipping").html()));
				console.log("total: "+withship);
				$(".cart"+rowid+" .product-line-price").text(response[rowid]['subtotal'].toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
				//$(".cart-shipping").text();
				$("#cart-total").html((withship).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
				
			}
		});
	});*/
	// $('.item_qty').on('change',function(){
	// 	var qty = $('#pd_qty1').val();
	// 	var price = $('#pd_qty1').attr('data-price');
	// 	$('.changedPrice').html(qty*price);
	// });
	$(".item_qty").change(function(){
		qty = $(this).val();
		index = $(this).attr("data-index");
		rowid = $(this).attr("data-row");
		if(qty == "0" || qty == ""){
			console.log(rowid);
			$(".cart"+rowid+" .item-qty-error").addClass("d-none");
			$(".cart"+rowid+" .invalid").removeClass("d-none");
		}else{
			if($("#shipping_method"+index).length > 0){
				shipping_id = $("#shipping_method"+index).attr('data-ship');
			}
			var row = $(this).parent().parent().parent().find(".cart-product");
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>cart/cartAjaxupdate",
				data:{'rowid':rowid, 'shipping_id':shipping_id, 'qty':qty},
				success: function(response){
					$(response.row_ids).each(function(index,value){
						key = Object.keys(value)[index];
						value = value[key];
						$(".cart"+key+" .shipping_link .price").html(value.shipping_price);
						// $(".cart"+key+" #ship-price").html(value.shipping_price);
						var price = $(".cart"+key+" .product-price .currency").html().replace(/[^\d\.\-]/g, "");
						if(parseInt(response.max_qty) >= parseInt(qty)){
							qty = $(".cart"+key+" .item_qty").val();
							$(".cart"+key+" .item-qty-error").addClass("d-none");
						}else{
							qty = response.max_qty;
							$(".cart"+key+" .item_qty").attr("max",qty)
							$(".cart"+key+" .item_qty").val(qty)
							$(".cart"+key+" .item-qty-error").removeClass("d-none");
							$(".cart"+key+" .invalid").addClass("d-none");
						}
						var total = parseFloat(price) * parseFloat(qty);
						total = total.toFixed(2);
						$(".cart"+key+" .product-line-price .currency").text(total);
					});
					subtotal = 0;
					$(row).each(function(index,value){
						qty = $(value).find(".item_qty").val();
						sub = qty * parseFloat($(value).find(".item-price").text().replace(/[^\d\.\-]/g, ""));
						$(value).find(".subtotal").text(sub);
						$(value).find(".subtotal").formatCurrency();
						subt = $(value).find(".subtotal").text().replace("$","");
						$(value).find(".subtotal").text(subt);
						subtotal = sub+subtotal;
					});
					row.parent().find(".sub-total").text(subtotal);
					var v = row.parent().find(".sub-total").formatCurrency();
					subtotal = row.parent().find(".sub-total").html().replace("$","");
					row.parent().find(".sub-total").text(subtotal);
					$("#cart-shipping").html(response.shipping_total);
					$("#cart-total").html(response.subtotal);

					price = parseFloat($(".cart"+response.cart_row+" .item-price").html());
				}
			});
		}
	});
});

$(document).on('click','.saveormove',function(){
	var row_id = $(this).attr("data-row_id");
	var action = $(this).attr("data-action")
	var url = "<?php echo base_url()?>cart/saveForLater";
	if(action !="saveforlater" && action !="movetocart"){
		alert("Error!");
		return false;
	}
	$.ajax({	
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: url,
		data:{"row_id":row_id,"action":action},
		success: function(response){
			window.location.reload();
		}
	});
});

</script>