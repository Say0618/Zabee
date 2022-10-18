<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput-jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/utils.js'); ?>"></script>
<link rel="stylesheet" type="text/css" href="<?php echo assets_url("plugins/form-select2/select2.min.css"); ?>" />
<script type="text/javascript" src="<?php echo assets_url('plugins/form-select2/select2.min.js'); ?>"></script>

<script>
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
		if (intlNumber) {
			output.text("International: " + intlNumber);
		}
		 else {
			output.text("Please enter a number above");
		}
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
		// nationalMode: true
	});
	telInput2.on("keyup change", function(){
		reset();
		var intlNumber2 = input.intlTelInput("getNumber");
		if (intlNumber2) {
			shipoutput.text("International: " + intlNumber2);
		}
		 else {
			shipoutput.text("Please enter a number above");
		}
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
		$('select[name="billcountry"]').select2({width: 'resolve', allowClear: true, placeholder: 'Select Country'});
		$('select[name="shipcountry"]').select2({width: 'resolve', allowClear: true, placeholder: 'Select Country'});
		
	});
	jQuery.validator.addMethod("FirstLetter", function(value, element) 
	{
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter cant be a space");	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters"); 

	function ShipBillForm(f){
		if(f.shipbill.checked == true){
			f.shipfullname.value = f.billfullname.value;
			f.shipcontact.value = f.billcontact.value;
			f.shipaddress1.value = f.billaddress1.value;
			f.shipaddress2.value = f.billaddress2.value;
			f.shipcity.value = f.billcity.value;
			f.shipzip.value = f.billzip.value;
			f.shipcountry.value = f.billcountry.value;
			f.shipstate.value = f.billstate.value;
			$('#myform').valid();
		}
	}
	$("#billcountry").on('change',function(){
		if($(this).val() !=226){
			$("#state").addClass('d-none');
			$("#province").removeClass('d-none');
			// $("#province").rule("add",required);
		}else{
			$("#province").addClass('d-none');
			$("#state").removeClass('d-none');
			// $("#province").rule("remove",required);

		}
	});
	$("#shipcountry").on('change',function(){
		if($(this).val() !=226){
			$("#state2").addClass('d-none');
			$("#province2").removeClass('d-none');
			// $("#province").rule("add",required);

		}else{
			$("#province2").addClass('d-none');
			$("#state2").removeClass('d-none');
			// $("#province2").rule("remove",required);
		}
	});

$(function() {
	$('.txtNumeric').keydown(function (e) {
	if (e.shiftKey || e.ctrlKey || e.altKey) {
		e.preventDefault();
    } else {
    var key = e.keyCode;
    if (!((key == 8) || (key == 32) || (key == 46) || (key >= 35 && key <= 40) || (key >= 65 && key <= 90))) {
      e.preventDefault();
    }
	}
  });
});	
// $(document).ready(function(e) {
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
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[\w.]+$/i.test(value);
}, "Letters, numbers, and underscores only please");
	$("#myform").validate({
		rules: {
			billfullname:{
				  minlength: 3,
        	maxlength: 30,
				  FirstLetter: true,
				  lettersonly: true,
        	required: true,
			},
			billcontact:{
				required:true,
				phoneNumber:true
			},
			billaddress1:{
				required:true,
			},
			billstate:{
				required:true,
			},
			billprovince:{
				required:true,
			},
			billcity:{
				required:true,
				lettersonly:true

			},
			billzip:{
				required:true,
				alphanumeric: true,
				minlength: 4,
			},
			shipfullname:{
				 minlength: 3,
          maxlength: 30,
				 FirstLetter: true,
				 lettersonly: true,
         required: true,
			},
			shipcontact:{
				required:true,
				phoneNumber2:true
			},
			shipaddress1:{
				required:true,
			},
			shipstate:{
				required:true,
			},
			shipprovince:{
			required:true,
			},
			shipcity:{
				required:true,
				lettersonly:true
			},
			shipzip:{
				required:true,
				alphanumeric:true,
				minlength: 4,
			}
		},
		messages:{
			billfullname: {
				required: "Please Enter Name",
				minlength: "Please Enter atleast 3 characters",
				maxlength: "Only 30 characters allowed ",
				
			},
			billcontact: {
				required: "Please Enter Contact Number",
			},
			billaddress1: {
				required: "Please Enter Address",
			},
			billstate: {
				required: "Please Enter State",
			},
			billcity: {
				required: "Please Enter City",
			},
			billzip: {
				required: "Please Enter ZIP Code",
			},
			shipfullname: {
				required: "Please Enter Name",
				minlength: "Please Enter atleast 3 characters",
				maxlength: "Only 30 characters allowed ",
				
			},
			shipcontact: {
				required: "Please Enter Contact Number",
			},
			shipaddress1: {
				required: "Please Enter Address",
			},
			shipstate: {
				required: "Please Enter State",
			},
			shipcity: {
				required: "Please Enter City",
			},
			shipzip: {
				required: "Please Enter ZIP Code",
			},

			
		},
		// submitHandler: function(form){
		// 	$('#preloadView2').show();
		// 	form.submit();
		// }	
	});
});

	
	//});
</script>