<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
$(document).ready(function(){
    jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-z]+$/i.test(value);
}, "Letters only please");
jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^[a-zA-Z 0-9.+_/]+$/i.test(value);
}, "Letters, numbers, and underscores only please");
	 $('#myform').validate({
        rules:{
			variant_name:{ required:true,
				FirstLetter: true,
				alphanumeric: true  },
			variant_status:{ required:true }
		},
        messages:
        {
		  variant_name:{ required: "Please enter variant name" },
		  variant_status:{ required: "Please select an option" }
		},
		errorPlacement: function(error, element) 
        { 
            if ( element.is(":radio") ) 
            {
                error.appendTo( element.parents('.container') );
            }
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         },
		
	  });	
	 
});
$(document).on("click", ".reset_btn", function(event){
    location.reload();
});
</script>