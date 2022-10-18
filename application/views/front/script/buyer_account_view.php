<script>
function editfunc(){
	//console.log(id);
	
	//$("#buyerform .editable-field").removeAttr('readonly').removeClass('border-0','bg-transparent');
	//$("#buyerform .editable-field").eq(0).focus();
	$("#buyerform .editSpan").remove();
	$(".editable-field").removeClass('d-none');
	$(".editbtn").removeClass("d-none");
	$(".savebtn").removeClass("d-none");
	$('#img-label').removeClass("d-none");
	
	}

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			$('#user_img').attr('src', e.target.result);
		}
		
		reader.readAsDataURL(input.files[0]);
	}
}

$("#upload_img").change(function(){
	readURL(this);
});
jQuery.validator.addMethod("phone_numbers", function(value, element) 
{
	return this.optional(element) || /^((\+[0-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/.test(value);
}, "Invalid input"); 
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
jQuery.validator.addMethod("Lettersonly", function(value, element) 
	{
		return this.optional(element) || /^[A-Za-z," "]+$/i.test(value);
	}, "Invalid input"); 
$(document).ready(function(){
	 $('#buyerform').validate({
        rules:{
			firstname: { 
				required:true,
				FirstLetter:true,
				Lettersonly: true
			},
				lastname: { 
				required:true,
				FirstLetter:true,
				Lettersonly: true
			}, 
			contact_no: { 
				required:true, 
				phone_numbers:true 
			},
		},
        messages:
        {
			firstname:{
				Lettersonly:'please enter  alphabetic characters only',
			},
			lastname:{
				Lettersonly:'please enter  alphabetic characters only',
			}
		 
        }
	  });	
});
function showPassword() {
  var x = document.getElementById("myInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>