<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput-jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/utils.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url("plugins/form-select2/select2.min.css"); ?>" />
<script type="text/javascript" src="<?php echo assets_url('plugins/form-select2/select2.min.js'); ?>"></script>
<script type="text/javascript">
$(function() {
$('.select2').select2({width: 'resolve'});
/*phone validation starts*/
	var phoneCheck = false;
	var pCheck = "";
	$("#contact").intlTelInput();
	var telInput = $("#contact"),
		errorMsg = $("#error-msg"),
		validMsg = $("#valid-msg");	
	var reset = function() {
		telInput.removeClass("error");  
		errorMsg.addClass("hide");
		validMsg.addClass("hide");
	};
	var input = $("#contact"),output = $("#output");
	input.intlTelInput({
		nationalMode: true
	});
	telInput.on("keyup change", function(){
		reset();
		var intlNumber = input.intlTelInput("getNumber");
		if(input.intlTelInput("isValidNumber") && phoneCheck){
			validMsg.removeClass('hide');
			errorMsg.addClass("hide");
		}
		else{
			errorMsg.removeClass('hide');
			validMsg.addClass("hide");
		}
	});
	$(document).on('focusout','#contact',function(){
		var intlNumber = input.intlTelInput("getNumber");
		$("#contact").val(intlNumber);
	});
	/*phone validation ends*/
	$(document).ready(function(){
		$('select[name="country"]').select2({ width: 'resolve', allowClear: true, placeholder: "<?php echo $_COOKIE['country_value']; ?>" });
	});
	jQuery.validator.addMethod("phoneNumber", function(value, element, params) {
		var result = "";
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				var international_number = $("#contact").intlTelInput("getNumber");
				$("#contact").val(international_number);
				result = true;
			}else{
				result = false;
			}
		}
		return result;
		//return (value.replace('_','').length==15);
	},'Invalid Number.');
	
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
    	return this.optional(element) || /^[\w.]+$/i.test(value);
	}, "Letters, numbers, and underscores only please");
	
	jQuery.validator.addMethod("phone_numbers", function(value, element) {
		return this.optional(element) || /^((\+[0-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/.test(value);
	}, "Invalid input"); 
	
	jQuery.validator.addMethod("pattername", function(value, element) {
		return this.optional(element) || /^[a-zA-Z_]*$/.test(value);
	}, "Invalid input");
	
	jQuery.validator.addMethod("NamePattern", function(value, element) {
		return this.optional(element) || /^[a-zA-Z]+$/.test(value);
	}, "Invalid input"); 
	
	jQuery.validator.addMethod("FirstLetter", function(value, element) {
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter cant be a space");
	
	jQuery.validator.addMethod("FirstLetter", function(value, element) {
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter cant be a space");	
	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
    	return this.optional(element) || /^[a-z\s]+$/i.test(value);
	}, "Only alphabetical characters");
	$.validator.addMethod( "nowhitespace", function( value, element ) {
		return this.optional( element ) || /^\S+$/i.test( value );
	}, "No white space please" );
	jQuery.validator.addMethod("email", function(value, element) {
		if(isValidEmailAddress(value)){
		return this.optional( element ) || true;
		} else {
		return this.optional( element )|| false;
		}
	}, "Please enter a valid email.");
	$.validator.addMethod('strongPassword', function(value, element) {
		return this.optional(element) 
		|| value.length >= 6
		&& /\d/.test(value)
		&& /[a-z]/i.test(value);
	},'Enter a combination of at least 6 characters and numbers');

	$("#country").on('change',function(){
		if($(this).val() !=226){
			$("#state").addClass('d-none').removeClass('d-block');
			$("#province").addClass('d-block').removeClass('d-none');
		}else{
			$("#province").addClass('d-none').removeClass('d-block');
			$("#state").addClass('d-block').removeClass('d-none');
		}
	});
	$("#addressBookAdd").validate({
		rules: {
			fullname:{
				 minlength: 3,
                 maxlength: 30,
				 FirstLetter: true,
				 lettersonly: true,
                 required: true
			},
			contact:{
				required:true,
				phone_numbers:true,
				phoneNumber:true,


			},
			email:{
				nowhitespace: true,
				required: true,
				email: true,
				/*remote: {
					url: "<?php echo base_url("home/checkEmailExist")?>",
				}*/
			},
			address1:{
				required:true
			},
			state:{
				required:true,
				// lettersonly: true,
			},
			province : {
				required:true,
				lettersonly: true,
			},
			city:{
				required:true,
				lettersonly: true 
			},
			zip:{
				required:true,
				alphanumeric: true,
				minlength: 5,
				maxlength: 5,
			},
			 password: {
				required: true,
				strongPassword:true,
				nowhitespace:true
			},
			confirm_password: {
				required: true,
				equalTo: '#password'
			}
		},
		messages: {
			email: {
				required: "Please enter Email Address.",
				email: "Invalid Email",
				remote: 'Email already exist. <a href="<?php echo base_url("login")?>">Click here to login!</a>'
			},
		},
		errorPlacement: function(error, element) {
			if(element[0].id == "contact"){
				error.appendTo(element.parent().parent());
			}else{
				error.appendTo(element.parent());
			} 
		}
	});
	$("#customCheck1").on("change",function(){
		if($(this).prop("checked")){
			$("#passDiv").removeClass("d-none");
		}else{
			$("#passDiv").addClass("d-none");
		}
	});
});
</script>