<script>
jQuery.validator.addMethod("numberssonly", function(value, element) 
{
	return this.optional(element) || /^[0-9," "]+$/i.test(value);
}, "Numbers only please"); 

jQuery.validator.addMethod("numbersAndDecimalsOnly", function(value, element) 
{
	return this.optional(element) || /^[0-9]\d*(\.\d{0,2})?$/i.test(value);
}, "Numbers with 2 decimals only please"); 

jQuery.validator.addMethod("noSpace", function(value, element) { 
	return value.indexOf(" ") < 0 && value != ""; 
}, "No space please and don't leave it empty");

jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	

$.validator.addMethod( "specialChars", function( value, element ) {
return this.optional( element ) || /^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/g.test( value );
}, "please enter a valid name" );

function changeBase(){
	var type = $('#basedOn').val();
	$('.baseGroup').addClass('d-none');
	$('.'+type+'Group').removeClass('d-none');
	console.log(type);
}
$(document).ready(function(){
	
	changeBase();
	$('#myform').validate({
        rules:{
			title:{ required:true, FirstLetter: true, specialChars: true },
			price:{ required:true, numbersAndDecimalsOnly: true},
			inc_unit:{ required:true, numberssonly: true},
			base_weight: { required:true, numbersAndDecimalsOnly: true},
			base_length: { required:true, numbersAndDecimalsOnly: true},
			base_width: { required:true, numbersAndDecimalsOnly: true},
			base_depth: { required:true, numbersAndDecimalsOnly: true},
			inc_price:{ required:true, numbersAndDecimalsOnly: true},
			free_after:{ numbersAndDecimalsOnly: true},
			minimum_days:{ required:true, numberssonly: true},
			maximum_days:{ required:true, numberssonly: true, greaterThanEqual:true }
		},
        errorPlacement: function(error, element) {
        {
            error.appendTo(element.parent());
		}
			},
			submitHandler: function(form,event) {
				var minDays = parseInt($('#minimum_days').val());
				var maxDays = parseInt($('#maximum_days').val());
				if(maxDays > minDays ){
					form.submit();
				}
		 	}
				 
	});
		jQuery.validator.addMethod("greaterThanEqual", function(value, element, params) {
			// alert();
			var minDays = parseInt($('#minimum_days').val());
			var maxDays = parseInt($('#maximum_days').val());
			if(minDays > maxDays){
				return false;
			}
			if(minDays == maxDays){
				return false;
			}else{
				return true;
			}
		},"Minimum Days must be less than maximum days");

	  	$(document).on("click", ".resetform", function(event){
			location.reload();
		});

	$("#inc_unit").on("keypress keyup blur",function (event) {    
		$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
	});
});
</script>