<script type="text/javascript" src="<?php echo assets_url('front/js/cart.js'); ?>"></script>
<?php if($payType == "paypal"){?>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PayPalClientId; ?>&disable-funding=credit,card&intent=authorize"></script>
<?php }?>
<script type="text/javascript">
var grandtotal = ($("#grand_total").html()).split("$")[1];
$('#i_agree').click(function(){
	$('#i_agree').parent().css('background', 'none');
});
$('.submitOrderForm').click(function(){
	$('#i_agree').parent().css('background', 'none');
	$('#order_sumbit').trigger('click');
});
jQuery.validator.addMethod("checkType", function(value, element) {
	return ($('input[name="pay_mode"]:checked').val() != 'paypal');
});
$("#order_form").validate({
	errorElement: 'span',
	rules: {
		fname: "required",
		lname: "required",
		address: "required",
		city: "required",
		State: "required",
		zip: {
			required:true,
			minlength: 5
		},
		phone: {
			required:true,
			minlength: 10
		},
		f_name_b: "required",
		l_name_b: "required",
		address_b: "required",
		city_b: "required",
		State_b: "required",
		zip_b: {
			required:true,
			minlength: 5
		},
		phone_b: {
			required:true,
			minlength: 10
		},
		card_name: {
			checkType: true,
			required:true
		},
		card_number: {
			checkType: true,
			required:true,
			creditcard: true
		},
		ccv: {
			checkType: true,
			required:true
		},
		exp_date: {
			checkType: true,
			required:true
		},
		exp_year: {
			checkType: true,
			required:true
		},
		i_agree: {
			required:true
		}
	},
	messages: {
		fname: "First name is required",
		lname: "Last name is required",
		address: "Address is required",
		city: "City is required",
		State: "State is required",
		zip: {
			required:'Zip code is required',
			minlength: 'Invalid zip code'
		},
		phone: {
			required:'Phone number is required',
			minlength: 'Invalid phone numer'
		},
		f_name_b: "First name is required",
		l_name_b: "Last name is required",
		address_b: "Address is required",
		city_b: "City is required",
		State_b: "State is required",
		zip_b: {
			required:'Zip code is required',
			minlength: 'Invalid zip code'
		},
		phone_b: {
			required:'Phone number is required',
			minlength: 'Invalid phone numer'
		},
		card_name: "Cardholder name is required",
		card_number: {
			required:'Card number is  required',
			creditcard: 'Invalid card number'
		},
		ccv: "<?php echo $this->lang->line("ccv_error"); ?>",
		exp_date: "Expiry month is required",
		exp_year: "Expiry year is required",
		i_agree: "Read terms and conditions and privacy policy. and accept it"
	},
	errorPlacement: function (error, element) {
		if(error.attr('for') == 'i_agree'){
			$('#i_agree').parent().css('background', '#ff0000');
		} else {
			$('span[for="'+error.attr("for")+'"]').text(error.text());
			element.parent().addClass('has-error');
		}
	}, submitHandler: function (form) {
		//console.log('test');
		$("#confirmBtn").remove();
		$("#imgLoad").removeClass("d-none");
		form.submit();
	}
	
});
/*$('input[name="pay_mode"]').click(function(){
	if($(this).val() != 'paypal'){
		$('#cardDetails').css('display', 'block');
	} else {
		$('#cardDetails').css('display', 'none');
	}
});*/
/*$(".sidenav li").click(function() {
    paymentMode = $(this).data('value');
	console.log(paymentMode);
});*/
$( ".creditCard" ).click(function() {
	$(".liforcreditCard").addClass("activePanel");
	$(".lifordebitCard").removeClass("activePanel");
	$(".lifornetBanking").removeClass("activePanel");
	$(".liforpaypal").removeClass("activePanel");
	$('.CreditCard_DebitCard_NetBanking').css('display', 'block');
	$('.PayPal').css('display', 'none');
	$('.SelectPayment').css('display', 'none');
});
$( ".debitCard" ).click(function() {
	$(".liforcreditCard").removeClass("activePanel");
	$(".lifordebitCard").addClass("activePanel");
	$(".lifornetBanking").removeClass("activePanel");
	$(".liforpaypal").removeClass("activePanel");
	$('.CreditCard_DebitCard_NetBanking').css('display', 'block');
	$('.PayPal').css('display', 'none');
	$('.SelectPayment').css('display', 'none');
});
$( ".netBanking" ).click(function() {
	$(".liforcreditCard").removeClass("activePanel");
	$(".lifordebitCard").removeClass("activePanel");
	$(".lifornetBanking").addClass("activePanel");
	$(".liforpaypal").removeClass("activePanel");
	$('.CreditCard_DebitCard_NetBanking').css('display', 'block');
	$('.PayPal').css('display', 'none');
	$('.SelectPayment').css('display', 'none');
});
$( ".paypal" ).click(function() {
	$(".liforcreditCard").removeClass("activePanel");
	$(".lifordebitCard").removeClass("activePanel");
	$(".lifornetBanking").removeClass("activePanel");
	$(".liforpaypal").addClass("activePanel");
	$('.CreditCard_DebitCard_NetBanking').css('display', 'none');
	$('.PayPal').css('display', 'block');
	$('.SelectPayment').css('display', 'none');
});
$(".item_qty").change(function(){
		var regex = /^\d?\d*$/;
		qty = $(this).val();
		if(regex.test(qty)){
			index = $(this).attr("data-index");
			if($("#shipping_method"+index).length > 0){
				shipping_id = $("#shipping_method"+index).attr('data-ship');
				rowid = $("#shipping_method"+index).attr('data-row_id');
			}
			var row = $(this).parent().parent().parent().find("section");
			var subt = $(this).parent().parent().parent().find(".inv-total");
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
						if(parseInt(response.max_qty) < parseInt(qty)){
							$(".cart"+key+" .item_qty").val(response.max_qty);
							$(".cart"+key+" .item_qty").attr("max", response.max_qty);
						}
						$(".cart"+key+" .shipping_link .price").html(value.shipping_price);
						price = parseFloat(value.subtotal.toFixed(2));
						if(value.tax > 0){
							tax = (value.tax.toString()).split('.');
							tax = tax[0]+"."+tax[1].substr(0,2);
							tax = parseFloat(tax);
							total = price + tax;
						}else{
							tax = "0.00";
							total = price;
						}
						$("#subtotal"+key).html(total.toFixed(2));
						$("#tax_"+key).html(tax);
						subt.html("$"+value.overall_sub);
					});
					subtotal = 0;
					$(row).each(function(index,value){
						qty = $(value).find(".item_qty").val();
						sub = qty*parseInt($(value).find(".item-price").text());
						$(value).find(".subtotal").text(sub.toFixed(2));
						subtotal = sub+subtotal;
					});
					row.parent().find(".sub-total").text(subtotal.toFixed(2));
					//$("#shipping_method"+index+" .price").html(response.shipping_price);
					$(".sub-total").html('$'+response.subtotal);
					$(".tax").html('$'+response.tax);
					$(".ship-total").html('$'+response.shipping_total);
					$("#grand_total").html('$'+response.grand_total);
					grandtotal = response.grand_total;
					/*withship = response['cart_total'] + parseFloat($("#cart-shipping").html());
					//console.log("cart-total: "+response['cart_total']);
					//console.log("ship-total: "+ parseFloat($("#cart-shipping").html()));
					//console.log("total: "+withship);
					$(".cart"+rowid+" .product-line-price").text(response[rowid]['subtotal'].toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					//$(".cart-shipping").text();
					$("#cart-total").html((withship).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));*/
					
				}
			});
		}else{
			$(this).val(Math.trunc(qty)).change();
		}
	});
	$('#apply_voucher').click(function(){
		var voucher_code = $('#voucher_code').val();
		if(voucher_code == '')
			return false;
		var url = '<?php echo base_url("checkout/apply_voucher"); ?>';
		$('.error_coupon').html('');
		ajaxRequest({voucher_code:voucher_code}, url, function(data){
			if(data.status == 1){
				if($('.coupon_div').length > 0){
					$('.coupon_div').remove();
					$('.coupon_row').removeClass('d-none');
				}
				$('.coupon_amount').html('($'+data.discount_amount+')');
				$('.cash-totals .tax span').text(data.tax);
				var shipping = parseFloat($('.cash-totals .ship-total span').text());
				$('#grand_total').text('$'+(parseFloat(data.discounted_cart_total)+parseFloat(data.tax)+shipping));
				grandtotal = (parseFloat(data.discounted_cart_total)+parseFloat(data.tax)+shipping);
				$('#coupon_modal').modal('hide');
			}
			if(data.status == 0){
				$('.error_coupon').html(data.message);
			}
		}, 'JSON');
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
				// if(value.price =="0"){
				// 	price = "Free Shipping";
				// }
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
							// '<td>'+price+'</td>'+
                            '<td>'+value.duration+' days</td>'+
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
			$('#shipping_method'+id).html('<a href="javascript:void(0)" class="shipping_title_'+pv_id+' stylelink"><strong><span class="shippingTitle" onClick="openModal('+id+','+ship_id+')">'+shipping_title+'</span></strong></a>');
			//$('#shipping_method'+id).html('<a href="javascript:void(0)" class="shipping_title_'+pv_id+' stylelink" onClick="openModal('+id+','+ship_id+')"><span class="price">'+response.shipping_price+'</span><strong><span class="shippingTitle"> via '+shipping_title+'</span></strong></a>');
			$("#shipping_method"+id+" .price").html(response.shipping_price);
			$(".sub-total").html(response.subtotal);
			$(".ship-total").html(response.shipping_total);
			$("#grand_total").html(response.grand_total);
			grandtotal = response.grand_total;
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
<?php if($payType == "paypal"){?>
paypal.Buttons({
  env: '<?php echo PayPalENV; ?>',
  style: {
	layout:  'vertical',
	size: 'responsive',
	shape: "rect",
	label: "checkout",
	height: 30
  },
	createOrder: function(data, actions) {
		return actions.order.create({
			application_context: {
                 shipping_preference: 'SET_PROVIDED_ADDRESS',
            },
			payer:{
				name:{
					given_name: "<?php echo $shipping_address['name']; ?>"
				}
			},
			purchase_units: [{
				amount: {
					value: grandtotal,
					currency: 'USD'
				},
				shipping: { 
					address: {
						address_line_1: "<?php echo $shipping_address['address_1']; ?>",
						address_line_2: "<?php echo $shipping_address['address_2']; ?>",
						admin_area_2:  	"<?php echo $shipping_address['city'] ?>",
						admin_area_1: 	"<?php echo (is_numeric($shipping_address['state']))?getCountryNameByKeyValue('id', $shipping_address['state'], 'code', true,'tbl_states'):$shipping_address['state']; ?>",
						phone: 			"<?php echo $shipping_address['phone']; ?>",
						postal_code: 	"<?php echo $shipping_address['zipcode']; ?>",
						country_code: 	"<?php echo getCountryNameByKeyValue('id', $shipping_address['country'], 'iso', true) ?>"
					},
					name: {
						full_name: "<?php echo $shipping_address['name']; ?>"
					}
				}
			}]
		});
	},onApprove: function(data, actions) {
		actions.order.authorize().then(function(authorization) {
			if(authorization.id != ""){
				console.log(authorization);
				$("#paypal_transID").val(authorization.purchase_units[0].payments.authorizations[0].id);
				console.log(authorization.payer);
				//$("#paypal_payer").val(authorization.payer.serialize());
				$("#paypal_payer").val(JSON.stringify(authorization.payer));
				console.log(JSON.stringify(authorization.payer));
				$( "#order_form" ).submit();
			}
		});
  	},onError: function (err) {
		console.log(err);return false;
	}
}).render('#paypal-button');
<?php }?>
</script>