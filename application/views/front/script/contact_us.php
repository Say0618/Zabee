<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput-jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/utils.js'); ?>"></script>
<script type="text/javascript">
$(function() {
	/*phone validation starts*/
	var phoneCheck = false;
	var pCheck = "";
	$("#phone_number").intlTelInput();
	var telInput = $("#phone_number"),
		errorMsg = $("#error-msg"),
		validMsg = $("#valid-msg");	
	var reset = function() {
	telInput.removeClass("error"); 
		errorMsg.addClass("hide");
		validMsg.addClass("hide");
	};
	var input = $("#phone_number"),output = $("#output");
		input.intlTelInput({
		nationalMode: true
	});
	telInput.on("keyup change", function(){
		reset();
		var intlNumber = input.intlTelInput("getNumber");
		// if (intlNumber) {
		// 	output.text("International: " + intlNumber);
		// }
		//  else {
		// 	output.text("Please enter a number above");
		// }
		if(input.intlTelInput("isValidNumber") && phoneCheck){
			validMsg.removeClass('hide');
			errorMsg.addClass("hide");
		}
		else{
			errorMsg.removeClass('hide');
			validMsg.addClass("hide");
		}
	});
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");

jQuery.validator.addMethod("phoneNumber", function(value, element, params) {
		var result = "";
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				var international_number = $("#phone_number").intlTelInput("getNumber");
				$("#phone_number").val(international_number);
				result = true;
			}else{
				result = false;
			}
		}
		return result;
		//return (value.replace('_','').length==15);
	},'Invalid Number.');
jQuery.validator.addMethod("phone_numbers", function(value, element) 
{
	return this.optional(element) || /^((\+[0-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/.test(value);
}, "Invalid input");
jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[\w.]+$/i.test(value);
}, "Invalid order number");
jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
);
$("#join-us-form").validate({
	rules:{
			email: {
				required: true,
				FirstLetter:true,
				email:true
			},

			message: {
				required: true,
				FirstLetter:true
			},
			subject: {
				required: true,
				FirstLetter:true
			},
			phone_number: {
				required: true,
				phone_numbers:true,
				phoneNumber:true
			},
			order_number:{
				alphanumeric:true
			}
		},
	errorPlacement: function(error, element) {
		error.appendTo( element.parent().parent() );
	}
});
$("#contact").submit(function(event) {

var recaptcha = $("#g-recaptcha-response").val();
$('.recaptcha_error').css('display','none');
if (recaptcha === "") {
  $('.recaptcha_error').text('Recaptcha is required');
  $('.recaptcha_error').css('display','block');
   event.preventDefault();
  //  alert("Please check the recaptcha");
}
});	
});
$( "#phone_number" ).keypress(function() {
	if($( "#phone_number" ).val() == ""){
		$('#phone_number-error').html('');
	}
	$('#phone_number-error').html('');
});
</script>