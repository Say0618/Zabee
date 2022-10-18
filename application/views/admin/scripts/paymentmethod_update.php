<script>
/*$('.nav-tabs a').click(function (e) {
	e.preventDefault();
	if(e.target.tagName.toLocaleLowerCase() != 'span'){
		$(this).parent().removeClass('hide');
		$(this).tab('show');
	}
});
$('.nav-tabs a').on('shown.bs.tab', function(event){
    console.log(event);
});
$('.closeTab').click(function (e) {
	var tabID = $(this).attr('data-tab');
	$('a[href="#'+tabID+'"]').parent().addClass('hide');
	$('#'+tabID).removeClass('active');
});*/
$(window).on("load",function(){
	//$('#paymentmethod').trigger('change');
	//$("#paymentmethod option:selected").trigger('change');	
});

$(document).ready(function(){
var elem = document.getElementById("paymentmethod");
setTimeout(function(){ $("#paymentmethod option:selected").trigger('change');	 }, 300);

$("#paymentmethod option:selected").trigger('change');	
elem.onchange = function(){
	var payment = $(this).val();
	//$('a[href="#'+payment+'"]').trigger('click');
	if(payment=='Paypal'){
		$('.Paypal_').parent().removeClass('hide');
		$('.Paypal_').parent().addClass('active');
		$($(".tab-content").find('#Paypal')).show();
		$($(".tab-content").find('#Paypal')).addClass('active');
		$($(".tab-content").find('#Paypal')).removeClass('fade');
		$($(".tab-content").find('#Stripe')).hide();
		$($(".tab-content").find('#BT')).hide();
		$('.Stripe_').parent().addClass('hide');
		$('.BT_').parent().addClass('hide');
	}
	if(payment=='Stripe'){
		$('.Stripe_').parent().removeClass('hide');
		$('.Stripe_').parent().addClass('active');
		$($(".tab-content").find('#Paypal')).hide();
		$($(".tab-content").find('#Stripe')).addClass('active');
		$($(".tab-content").find('#Stripe')).removeClass('fade');
		$($(".tab-content").find('#Stripe')).show();
		$($(".tab-content").find('#BT')).hide();
		$('.Paypal_').parent().addClass('hide');
		$('.BT_').parent().addClass('hide');
	}
	if(payment=='BT'){
		$('.BT_').parent().removeClass('hide');
		$('.BT_').parent().addClass('active');
		$($(".tab-content").find('#Paypal')).hide();
		$($(".tab-content").find('#Stripe')).hide();
		$($(".tab-content").find('#BT')).removeClass('fade');
		$($(".tab-content").find('#BT')).show();
		$($(".tab-content").find('#BT')).addClass('active');
		$('.Stripe_').parent().addClass('hide');
		$('.Paypal_').parent().addClass('hide');
	} 
};

$("#myform1").validate({
		rules: {
			PayPalAPIUsername:{
				required: function(element){
					return ($('#paymentmethod').val() == 'Paypal')}
			},
			PayPalAPIPassword:{
				required: function(element){
				return ($('#paymentmethod').val() == 'Paypal')}
			},
			PayPalAPISignature:{
				required: function(element){
				return ($('#paymentmethod').val() == 'Paypal')}
			},
			PayPalAPIApplicationID:{
				required: function(element){
				return ($('#paymentmethod').val() == 'Paypal')}
			},
			StripeAPIkey:{
				required: function(element){
				return ($('#paymentmethod').val() == 'Stripe')}
			},
			BrainTree_MerchantID:{
				required: function(element){
				return ($('#paymentmethod').val() == 'BT')}
			},
			BrainTree_PublicKey:{
				required: function(element){
				return ($('#paymentmethod').val() == 'BT')}
			},
			BrainTree_PrivateKey:{
				required: function(element){
				return ($('#paymentmethod').val() == 'BT')}
			}
		},
		messages:{
			PayPalAPIUsername:{
				required: "Please enter PayPal API Username"
			},
			PayPalAPIPassword:{
				required: "Please enter PayPal API Password"
			},
			PayPalAPISignature:{
				required: "Please enter PayPal API Signature"
			},
			PayPalAPIApplicationID:{
				required: "Please enter PayPal API ApplicationID"
			},
			StripeAPIkey:{
				required: "Please enter Stripe API key"
			},
			BrainTree_MerchantID:{
				required: "Please enter BrainTree Merchant ID"
			},
			BrainTree_PublicKey:{
				required: "Please enter BrainTree Public Key"
			},
			BrainTree_PrivateKey:{
				required: "Please enter BrainTree Private Key"
			}
		},
		submitHandler: function(form){
			form.submit();
		}	
});
});
</script>
		