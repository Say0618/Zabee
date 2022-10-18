<?php //print_r($_COOKIE['country_value']); ?>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput-jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/utils.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url("plugins/form-select2/select2.min.css"); ?>" />
<script type="text/javascript" src="<?php echo assets_url('plugins/form-select2/select2.min.js'); ?>"></script>
<script>
//alert(<?php echo $_COOKIE['country_value'] ?>);
$(function() {
	$('.select2').select2({width: 'resolve'});
	/*phone validation starts*/
	var phoneCheck = false;
	var pCheck = "";
	$("#billcontact").intlTelInput();
	var telInput = $("#billcontact"),
		errorMsg = $("#error-msg"),
		validMsg = $("#valid-msg");	
	var telInput2 = $("#shipcontact");
	var reset = function() {
	telInput.removeClass("error");  
	telInput2.removeClass("error"); 
		errorMsg.addClass("hide");
		validMsg.addClass("hide");
	};
	var input = $("#billcontact"),output = $("#output");
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
	var input2 = $("#shipcontact"),shipoutput = $("#shipoutput");
	input2.intlTelInput({
		nationalMode: true
	});
	telInput2.on("keyup change", function(){
		reset();
		var intlNumber2 = input.intlTelInput("getNumber");
		// if (intlNumber2) {
		// 	shipoutput.text("International: " + intlNumber2);
		// }
		//  else {
		// 	shipoutput.text("Please enter a number above");
		// }
		if(input2.intlTelInput("isValidNumber") && phoneCheck){
			validMsg.removeClass('hide');
			errorMsg.addClass("hide");
		}
		else{
			errorMsg.removeClass('hide');
			validMsg.addClass("hide");
		}
	});
	$(document).on('focusout','#billcontact',function(){
		var intlNumber = input.intlTelInput("getNumber");
		$("#billcontact").val(intlNumber);
	});
	$(document).on('focusout','#shipcontact',function(){
		var intlNumber = input2.intlTelInput("getNumber");
		$("#shipcontact").val(intlNumber);
	});
	
	/*phone validation ends*/

	$(document).ready(function(){
		// var input = document.querySelector("#billcontact");
		// window.intlTelInput(input);
		$('select[name="billcountry"]').select2({ width: 'resolve', allowClear: true, placeholder: "<?php echo $_COOKIE['country_value']; ?>" });
		$('select[name="shipcountry"]').select2({ width: 'resolve', allowClear: true, placeholder: "<?php echo $_COOKIE['country_value']; ?>" });
		$('select[name="billstate"]').select2({ width: 'resolve', allowClear: true});
		$('select[name="shipstate"]').select2({ width: 'resolve', allowClear: true });
	});
	jQuery.validator.addMethod("phoneNumber2", function(value, element, params) {
		var result = "";
		if ($.trim(telInput2.val())) {
			if (telInput2.intlTelInput("isValidNumber")) {
				var international_number = $("#shipcontact").intlTelInput("getNumber");
				$("#shipcontact").val(international_number);
				result = true;
			}else{
				result = false;
			}
		}
		return result;
		//return (value.replace('_','').length==15);
	},'Invalid Number.');
	jQuery.validator.addMethod("phoneNumber", function(value, element, params) {
		var result = "";
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				var international_number = $("#billcontact").intlTelInput("getNumber");
				$("#billcontact").val(international_number);
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
	jQuery.validator.addMethod("pattername", function(value, element) 
	{
		return this.optional(element) || /^[a-zA-Z_]*$/.test(value);
	}, "Invalid input");
	jQuery.validator.addMethod("NamePattern", function(value, element) 
	{
		return this.optional(element) || /^[a-zA-Z]+$/.test(value);
	}, "Invalid input"); 
	jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");
jQuery.validator.addMethod("FirstLetter", function(value, element) 
	{
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter can not be a space");	
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
}, "Please enter a valid email."
);
	function ShipBillForm(f){
		if(f.shipbill.checked == true){
			f.shipfirst_name.value = f.billfirst_name.value;
			f.shiplast_name.value = f.billlast_name.value;
			f.shipcontact.value = f.billcontact.value;
			f.shipemail.value = f.billemail.value;
			f.shipaddress1.value = f.billaddress1.value;
			f.shipaddress2.value = f.billaddress2.value;
			f.shipcity.value = f.billcity.value;
			f.shipzip.value = f.billzip.value;
			if(f.billcountry.value !=226){
				$("#state2").addClass('d-none');
				$("#province2").removeClass('d-none');
				f.shipprovince.value = f.billprovince.value;
			}else{
				$("#province2").addClass('d-none');
				$("#state2").removeClass('d-none');
				$("#shipstate").select2('val',f.billstate.value);
			}
		 	$("#shipcountry").select2('val',f.billcountry.value);
		}
	}
	$("#billcountry").on('change',function(){
		if($(this).val() !=226){
			$("#state").addClass('d-none');
			$("#province").removeClass('d-none');
		}else{
			$("#province").addClass('d-none');
			$("#state").removeClass('d-none');
		}
	});
	$("#shipcountry").on('change',function(){
		if($(this).val() !=226){
			$("#state2").addClass('d-none');
			$("#province2").removeClass('d-none');
		}else{
			$("#province2").addClass('d-none');
			$("#state2").removeClass('d-none');
		}
	});
	$("#join-us-form").validate({
		rules: {
			billfirst_name:{
				FirstLetter: true,
				minlength: 3,
        	maxlength: 30,
				lettersonly: true,
                required: true
			},
			billlast_name:{
				FirstLetter: true,
				minlength: 3,
         		maxlength: 30,
				lettersonly: true,
				required: true
			},
			billemail: {
				nowhitespace: true,
				required: true,
				email: true
            },
			billcontact:{
				required:true,
				phoneNumber:true,
				phone_numbers:true
			},
			billaddress1:{
				FirstLetter: true,
				required:true
			},
			billstate:{
				FirstLetter: true,
				required:true,
			},
			billprovince:{
				FirstLetter: true,
				required:true,
				lettersonly: true,
			},
			billcity:{
				FirstLetter: true,
				required:true,
				lettersonly: true
			},
			billzip:{
				required:true,
				alphanumeric: true,
				minlength: 3
			},
			shipfirst_name:{
				FirstLetter: "#different:checked",
				minlength: 3,
        		maxlength: 30,
				lettersonly: "#different:checked",
         		required: "#different:checked"
			},
			shiplast_name:{
				FirstLetter: "#different:checked",
				minlength: 3,
        		maxlength: 30,
				lettersonly: "#different:checked",
        		required: "#different:checked"
			},
			shipemail: {
				nowhitespace: "#different:checked",
				required: "#different:checked",
				email: "#different:checked"
            },
			shipcontact:{
				required:"#different:checked",
				phone_numbers:"#different:checked",
				phoneNumber2:"#different:checked"
			},
			shipaddress1:{
				FirstLetter: "#different:checked",
				required:"#different:checked"
			},
			shipstate:{
				FirstLetter: "#different:checked",
				required:"#different:checked",
			},
			shipprovince:{
				FirstLetter: "#different:checked",
				required:"#different:checked", 
				lettersonly: "#different:checked",
			},
			shipcity:{
				FirstLetter: "#different:checked",
				required:"#different:checked"
			},
			shipzip:{
				FirstLetter: "#different:checked",
				required:"#different:checked",
				number: "#different:checked"
			}
		},
		messages:{
			
		},errorPlacement: function(error, element) {
			if(element[0].id == "billcontact" || element[0].id == "shipcontact"){
				error.appendTo(element.parent().parent());
			}else{
				error.appendTo(element.parent());
			} 
		}
	});
});
/*$('#txtNumeric').keydown(function (e) {
  if (e.shiftKey || e.ctrlKey || e.altKey) {
      e.preventDefault();
  } else {
  var key = e.keyCode;
  if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
        e.preventDefault();
  }
}
});*/
	
</script>