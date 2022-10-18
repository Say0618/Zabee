<script>

jQuery.validator.addMethod("numberssonly", function(value, element) 
{
	return this.optional(element) || /^[0-9," "]+$/i.test(value);
}, "Numbers only please"); 
jQuery.validator.addMethod("numbersAndDecimalsOnly", function(value, element) 
{
	return this.optional(element) || /^[0-9]\d*(\.\d+)?$/i.test(value);
}, "Numbers only please"); 
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
$(document).ready(function(){
	 $('#myform').validate({
        rules:{
			returnPolicyName: { 
				required:true,
				FirstLetter: true,
				specialChars: true,
			},
			return_YesNo:{ 
				required:true,
			},
			rma_YesNo:{ 
				required:true
			},
			percent_fixed:{ 
				required:true 
			},
			returnPeriod:{ 
				required:true, 
				numberssonly:true,
				FirstLetter: true,
				minlength:1,
				maxlength:2
			},
			restockingFee:{ 
				required:true, 
				FirstLetter: true,
				numbersAndDecimalsOnly:true,
				minlength:1,
			}
		},
        messages:	
        {
		  ReturnPolicyName:{ 
			required: "Return Policy name required" 
		  },
		  return_YesNo:{ 
			required: "Please select an option" 
		  },
		  rma_YesNo:{ 
			required: "Please select an option" 
		  },
		  percent_fixed:{ 
			required: "Please select an option" 
		  },
		  ReturnPeriod:{ 
			required: "Please provide a Return Period" 
		  },
		  RestockingFee:{ 
			required: "Please provide Restocking Fee" 
			}
        },
		success: function(element) {
			$(element).closest('input').removeClass('error').addClass('has-success');
		  },
		errorPlacement: function(error, element) 
        {
            if ( element.is(":radio") ) 
            {
                error.appendTo( element.parents('.mt-2') );
            }
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         },
	  });	
	  
	$(".percent_fixed").on("click",function(){
		var type = $(this).val();
		console.log(type);
		if(type == "0"){
			$("#Restocking_Fee").rules("remove","maxlength");
		}else{
			$("#Restocking_Fee").rules("add",{maxlength:2});
		}
	});
	
});
var returnName = $("#Return_policy_name");
var returnPeriod = $("#Return_Period");
var restockingFee = $("#Restocking_Fee");
/*$( "input" ).keypress(function() {
	if(returnName.val() != ""){
		$('label[for=Return_policy_name]').remove();
	}
	if(returnPeriod.val() != ""){
		$('label[for=Return_Period]').remove();
	}
	if(restockingFee.val() != ""){
		$('label[for=Restocking_Fee]').remove();
	}	
});*/
$( "#Restocking_Fee" ).keypress(function() {
	//alert();
	$('#Restocking_Fee-error').html('');
});
$( "#Return_Period" ).keypress(function() {
	$('#Return_Period-error').html('');
});

$( "#Return_policy_name" ).keypress(function() {
	$('.return-error').html('');
	$('#Return_policy_name-error').html('');
});
$( "#Restocking_Fee" ).keydown(function() {
	//alert();
	$('#Restocking_Fee-error').html('');
});
$( "#Return_Period" ).keydown(function() {
	$('#Return_Period-error').html('');
});

$( "#Return_policy_name" ).keydown(function() {
	$('.return-error').html('');
	$('#Return_policy_name-error').html('');
});

$(document).on("click", ".resetform", function(event){
    location.reload();
});

</script>
