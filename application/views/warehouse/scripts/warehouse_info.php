<script type="text/javascript" src="<?php echo media_url('assets/plugins/intl-tel-input/intlTelInput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo media_url('assets/plugins/intl-tel-input/intlTelInput-jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo media_url('assets/plugins/intl-tel-input/utils.js'); ?>"></script>
<script>
$(function() {
	/*phone validation starts*/
	var phoneCheck = false;
	var pCheck = "";
	$("#f1-contact-phone").intlTelInput();
	var telInput2 = $("#f1-contact-phone"),
		errorMsg = $("#error-msg"),
		validMsg = $("#valid-msg");	
	var reset2 = function() {
	telInput2.removeClass("error"); 
		errorMsg.addClass("hide");
		validMsg.addClass("hide");
	};
	var input2 = $("#f1-contact-phone"),output = $("#output");
		input2.intlTelInput({
		nationalMode: true
	});
jQuery(document).ready(function() {
    $('.f1 fieldset:first').fadeIn('slow');
    $('.f1 input[type="text"], .f1 input[type="password"], .f1 textarea').on('focus', function() {
    	$(this).removeClass('input-error');
    });
});
$(document).ready(function(e) {
	$('#country').select2({width: 'resolve'});

	$.validator.addMethod( "specialChars", function( value, element ) {
return this.optional( element ) || /^[^\\]+$/i.test( value );
}, "please enter a valid name" );

// $.validator.addMethod( "lettersonly", function( value, element ) {
// return this.optional( element ) || /^[a-z]+$/i.test( value );
// }, "please enter a valid name" );
jQuery.validator.addMethod("lettersonly", function(value, element) 
{
	return this.optional(element) || /^[A-Za-z," "]+$/i.test(value);
}, "Please Enter alphabets"); 
jQuery.validator.addMethod("phoneNumber", function(value, element, params) {
		var result = "";
		if ($.trim(telInput.val())) {
			if (telInput.intlTelInput("isValidNumber")) {
				var international_number = $("#f1-mobile").intlTelInput("getNumber");
				$("#f1-mobile").val(international_number);
				result = true;
			}else{
				result = false;
			}
		}
		return result;
		//return (value.replace('_','').length==15);
	},'<?php echo $this->lang->line('inv_num');?>');
	jQuery.validator.addMethod("customerNumber", function(value, element, params) {
		var result = "";
		if ($.trim(telInput1.val())) {
			if (telInput1.intlTelInput("isValidNumber")) {
				var international_number = $("#f1-service-phone").intlTelInput("getNumber");
				$("#f1-service-phone").val(international_number);
				result = true;
			}else{
				result = false;
			}
		}
		return result;
		//return (value.replace('_','').length==15);
	},'<?php echo $this->lang->line('inv_num');?>');
	jQuery.validator.addMethod("contactNumber", function(value, element, params) {
		var result = "";
		if ($.trim(telInput2.val())) {
			if (telInput2.intlTelInput("isValidNumber")) {
				var international_number = $("#f1-contact-phone").intlTelInput("getNumber");
				$("#f1-contact-phone").val(international_number);
				result = true;
			}else{
				result = false;
			}
		}
		return result;
		//return (value.replace('_','').length==15);
	},'<?php echo $this->lang->line('inv_num');?>');
	jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^\w+$/i.test(value);
}, "<?php echo $this->lang->line('letter_no');?>");
	if($("#country").val() !=226){
		$("#state").addClass('d-none');
		$("#province").removeClass('d-none');
			}else{
				$("#province").addClass('d-none');
				$("#state").removeClass('d-none');
				// $("f1-state").select2('val',f.billstate.value);
				$('#f1-state').select2({width: 'resolve'});
			}
		$("#country").on('change',function(){
		if($(this).val() !=226){ 
			$("#state").addClass('d-none');
			$("#province").removeClass('d-none');
		}else{
			$("#province").addClass('d-none');
			$("#state").removeClass('d-none');
			$('#f1-state').select2({width: 'resolve'});

		}		
	});
	jQuery.extend(jQuery.validator.messages, {
    remote: "<?php echo $this->lang->line('warehouse_already_exist');?>"
});
	$("#myform").validate({
		rules: {
			warehouse_title:{
				required: true,
				//lettersonly: true,
				FirstLetter: true,
				specialChars:true,

				remote: {
					url: '<?php echo base_url("warehouse/dashboard/check_warehouse_exist")?>',
					type: "post",
					data:{user_id:'<?php echo (isset($warehouse->user_id) && $warehouse->user_id)?$warehouse->user_id:"0";?>'}
				},
				FirstLetter: true
			},
			address:{
				required:true,
				minlength: 5,
				FirstLetter: true
				
			},
			warehouse_class:{
				required:true
			},
			country_id:{
				required:true
			},
			city:{
				required:true,
				lettersonly:true
			},
			contact_no:{
				required:true,
				// number: true, 
				minlength: 4,
				// FirstLetter: true,
				contactNumber:true
			},
			email:{
				required:true,
				emailAddress: true,
				FirstLetter: true
			},
			zip_code:{
				required:true,
				alphanumeric:true
			},
			province:{
				required:true,
				lettersonly: true,
				FirstLetter: true
			}
			},
			messages:{
				store_name:{
					required: "<?php echo $this->lang->line('enter_warehouse_title');?>",
					remote: "<?php echo $this->lang->line('warehouse_already_exist');?>"
				},
				store_address:{
					required: "<?php echo $this->lang->line('warehouse_address');?>"
				},
				legal_busniess_type:{
					required: "<?php echo $this->lang->line('select_warehouse_class');?>"
				},
				country:{
					required: "<?php echo $this->lang->line('select_country');?>"
				},
				contact_phone:{
					required: "<?php echo $this->lang->line('enter_contact_no');?>"
				},
				contact_email:{
					required: "<?php echo $this->lang->line('enter_email');?>"
				},
				f1_email:{
					required: "<?php echo $this->lang->line('enter_valid_email');?>"
				},
			},
			errorPlacement: function(error, element) {
				// console.log(element);
				if(element[0].id == 'f1-first-name'||element[0].id == 'f1-last-name'){
				error.appendTo(element.parent().parent());
				}
				else{
					error.appendTo(element.parent());
				}
			}
		});
});
jQuery.validator.addMethod("address", function(value, element) 
{
	return this.optional(element) || /^([A-Za-z0-9\-\\,.#])*[A-Za-z0-9\-\\,.#\s]+$/.test(value);
}, "<?php echo $this->lang->line('inv_inp');?>");
jQuery.validator.addMethod("emailAddress", function(value, element) 
{
	return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\/"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
}, "<?php echo $this->lang->line('not_valid_email');?>");	
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "<?php echo $this->lang->line('first_letter');?>");	
jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "<?php echo $this->lang->line('only_alpha');?>");
$('#warehouse_title').keyup(function() {
	var dInput = this.value.trim();	
	dInput = dInput.replace(/ /g,"-");
	dInput = dInput.toLowerCase();
	$("#warehouse_id").text(dInput);
	$("#warehouse_id").val(dInput);

});
});
</script>