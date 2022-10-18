<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
$(document).ready(function(){
	jQuery.validator.addMethod("FirstLetter", function(value, element) 
	{
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter cant be a space");	
	jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-z' ]+$/i.test(value);
}, "Letters and Space only please");
	 $('#myform').validate({
        rules:{
			variant_category_name:{ 
				required:true,
				FirstLetter: true,
				lettersonly: true 
			},
		},
        messages:
        {
		  variant_category_name:{ 
			required: "Variant category title required" 
		  },
		}
	  });	
});
$(document).on("click", ".reset_btn", function(event){
    location.reload();
});
</script>