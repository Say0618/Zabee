<script type="text/javascript" src="<?php echo assets_url('front/js/cart.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/mask-Plugin/jquery.mask.min.js'); ?>"></script>
<!-- <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD" data-sdk-integration-source="button-factory"></script> -->
<script type="text/javascript">
$(document).ready(function(){
	var address = "<?php echo isset($_GET['a'])?$_GET['a']:""; ?>";
	if(address != ""){	
		$("#billing_address").val(address);
	}

	$(".payType").on("click", function(){
		var type = $(this).val();
		var order_id = $("#orderID").val();
		if(type == "paypal"){
			window.location.replace("<?php echo base_url("checkout/payment?pay=paypal"); ?>");
		}else{
			if($(this).data("show") == false){
				$(this).data("show", "true");
				$("#order_form").removeAttr("class");
			}else{
				$(this).data("show", "false");
				$("#order_form").addClass("d-none");
			}
		}
	});

	// function initPayPalButton() {
    //   paypal.Buttons({
    //     style: {
    //       shape: 'rect',
    //       color: 'gold',
    //       layout: 'vertical',
    //       label: 'paypal',
    //     },
    //     createOrder: function(data, actions) {
    //       return actions.order.create({
    //         purchase_units: [{"amount":{"currency_code":"USD","value":1}}]
    //       });
    //     },

    //     onApprove: function(data, actions) {
    //       return actions.order.capture().then(function(details) {
    //         alert('Transaction completed by ' + details.payer.name.given_name + '!');
    //       });
    //     },

    //     onError: function(err) {
    //       console.log(err);
    //     }
    //   }).render('#paypal-button-container');
    // }
    // initPayPalButton();

});
$(function () {
if(getCookie("card_num") != "0")
{
	$("#card_number").val(getCookie("card_num"));
	$("#card_name").val(getCookie("card_name"));
	deleteCookie();
}
	$("#billing_address").on("click",function(){
		if($(this).val() == "new"){
			var isLogin = "<?php echo $this->isloggedin?>";
			sessionStorage.setItem("card_number", $("#card_number").val());
			sessionStorage.setItem("exp_date", $("#exp_date").val());
			sessionStorage.setItem("exp_year", $("#exp_year").val());
			sessionStorage.setItem("ccv", $("#ccv").val());
			sessionStorage.setItem("card_name", $("#card_name").val());
			if(isLogin){
				location.href = "<?php echo base_url('shipping/add/2?b=2');?>"	
			}else{
				location.href = "<?php echo base_url('checkout/guest?b=2');?>"
			}
		}
	})
	$('.payWithCardBtn').click(function(){
		var id = $(this).attr('data-id');
		$('#card_id').val(id);
		$('#payWithCard').submit();
	});
	var current_year = "<?php echo date("Y"); ?>";
       $("#exp_year").change(function () {
            var selected_year = $(this);
            current_year = parseInt(current_year);
			selected_year = parseInt(selected_year.val());
			if(selected_year > current_year){
				$("#exp_date option").removeAttr('disabled');
			}else{
				var d = new Date();
				var n = d.getMonth();
				var month = n + 1;
				var current_month = parseInt(month);
				$("#exp_date option").each(function(){
				  if ($(this).val() < month) {
					$(this).attr("disabled", "disabled");
				  }
				});
			}
		});
    }); 
// $('#i_agree').click(function(){
// 	$('#i_agree').parent().css('background', 'none');
// });
// $('.submitOrderForm').click(function(){
// 	//alert();
// 	$('#i_agree').parent().css('background', 'none');
// 	//$('#order_sumbit').trigger('click');
// 	//return false;
	
// });

function getCookie(name)
{
	var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) {
    	return (match[2]);
    }
}

function deleteCookie() {
  document.cookie = 'card_num=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
  document.cookie = 'card_name=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}
jQuery.validator.addMethod("checkType", function(value, element) {
	return ($('input[name="pay_mode"]:checked').val() != 'paypal');
});
$(document).ready(function(){
	$('#card_number').mask('0000-0000-0000-0000');
	$("#order_form").validate({
		errorElement: 'span',
		rules: {
			card_name: {
				lettersonly: true,
				FirstLetter: true,
				required:true
			},
			card_number: {
				required: true,
				creditcard: true,
				
			},
			ccv: {
				required:true,
				number: true,
				minlength: 3,			
				maxlength:4
			},
			exp_date: {
				required:true
			},
			exp_year: {
				required:true
			},
			billing_address:{
				required:true
			},
			i_agree: {
				required:true
			}
		},
		messages: {
			card_name: {
				required: "Cardholder name is required",
				FirstLetter: "First input can not be space",
				lettersonly: "Enter a valid name"
			},
			card_number: {
				required:'Card number is  required',
				creditcard: 'Invalid card number'
			},
			ccv:{
				required:'<?php echo $this->lang->line("ccv_error"); ?>',
				number: "<?php echo $this->lang->line("ccv_invalid"); ?>"
			},
			exp_date: "Expiry should be set after current date",
			exp_year: "Expiry year is required",
			billing_address: "Billing address is required",
			i_agree: "check the checkbox to proceed",
		},
		errorPlacement: function (error, element) {
			if(element.attr("name") == "i_agree"){
				error.appendTo('.error_div');	
			} else {
				//$('span[for="'+error.attr("for")+'"]').text(error.text());
				//element.parent().addClass('has-error');
				$(element).after(error);
			}
		},
		submitHandler: function(form) {
			$('#card_number').val($('#card_number').cleanVal());
			form.submit();
  		}
	});

	$("#card_number").val( (sessionStorage.getItem("card_number") ? sessionStorage.getItem("card_number") : "") );
	$("#exp_date").val( (sessionStorage.getItem("exp_date") ? sessionStorage.getItem("exp_date") : $("#exp_date option:first").val()) );
	$("#exp_year").val( (sessionStorage.getItem("exp_year") ? sessionStorage.getItem("exp_year") : $("#exp_year option:first").val()) );
	$("#ccv").val( (sessionStorage.getItem("ccv") ? sessionStorage.getItem("ccv") : "") );
	$("#card_name").val( (sessionStorage.getItem("card_name") ? sessionStorage.getItem("card_name") : "") );

	sessionStorage.setItem("card_number", "");
	sessionStorage.setItem("exp_date", "");
	sessionStorage.setItem("exp_year", "");
	sessionStorage.setItem("ccv", "");
	sessionStorage.setItem("card_name", "");

});
jQuery.validator.addMethod("lettersonly", function(value, element) 
{
	return this.optional(element) || /^[A-Za-z," "]+$/i.test(value);
}, "Invalid input"); 
jQuery.validator.addMethod("address", function(value, element) 
{
	return this.optional(element) || /^([A-Za-z0-9\-\\,.#])*[A-Za-z0-9\-\\,.#\s]+$/.test(value);
}, "Invalid input"); 
jQuery.validator.addMethod("emailAddress", function(value, element) 
{
	return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\/"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
}, "Email Address Not Valid");
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
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
if ($('input.custom-control-input').prop('checked')) {
	
}
$(function()
    {
      $('#save_card').change(function()
      {
        if ($(this).is(':checked')) {
			$(".card_name_row").show();
        } else {
			$(".card_name_row").hide();
		};
      });
    });
// jQuery(function ( $ ){
// $(".credit").credit();
// });
$('.deleteCard').click(function(){
	var card_id = $(this).attr('data-id');
	var card_name = $(this).attr('data-name');
	$('#card_id').val(card_id);
	$('.card_name').text(card_name);
	$('#confirmation-modal').modal('show');
});
$('#card_delete').click(function(){
	var card_id = $('#card_id').val();
	if(card_id == ''){
		$('#confirmation-modal .error').removeClass('d-none');
		return false;
	}
	var url = '<?php echo base_url("buyer/delete_card/"); ?>'+card_id+"/1";
	window.location.href = url;
});
</script>