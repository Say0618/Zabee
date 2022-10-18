<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
$(document).ready(function(){
	jQuery.validator.addMethod("lettersonly", function(value, element) {
  return this.optional(element) || /^[a-z' ]+$/i.test(value);
}, "Letters and Space only please");
	 $('#myform').validate({
        rules:{
			variant_category_name_update:{ 
				required:true,
				FirstLetter: true,
				lettersonly:true,
				
			 },
		},
        messages:
        {
		  variant_category_name_update:{ required: "Please enter the name" },
		}
	  });	
});
$(document).on("click", ".reset_btn", function(event){
    location.reload();
});
</script>