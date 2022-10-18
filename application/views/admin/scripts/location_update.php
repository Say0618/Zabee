<script>
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
		}
	}
	jQuery.validator.addMethod("phone_numbers", function(value, element) 
	{
		return this.optional(element) || /^((\+[0-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/.test(value);
	}, "Invalid contact number"); 
	jQuery.validator.addMethod("pattername", function(value, element) 
	{
		return this.optional(element) || /^[a-zA-Z_]*$/.test(value);
	}, "Invalid input"); 
	jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters"); 
jQuery.validator.addMethod("FirstLetter", function(value, element) 
	{
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter cant be a space");	

	$("#myform").validate({
		rules: {
			billfullname:{
				minlength: 3,
                maxlength: 30,
				FirstLetter: true,
				lettersonly: true,
                required: true
			},
			billcontact:{
				required:true,
				phone_numbers:true,
				FirstLetter: true,
			},
			billaddress1:{
				minlength: 3,
				required:true,
				FirstLetter: true,
			},
			billstate:{
				required:true,
				FirstLetter: true,
				lettersonly: true,
			},
			billcity:{
				required:true,
				FirstLetter: true,
				lettersonly: true,
			},
			billzip:{
				required:true,
				number: true,
				FirstLetter: true,
				
			},
			billcountry:{
				required:true
			},
			shipfullname:{
				minlength: 3,
                maxlength: 30,
				FirstLetter: true,
				lettersonly: true,
                required: true
			},
			shipcountry:{
				required:true,
			},
			shipcontact:{
				required:true,
				phone_numbers:true,
				FirstLetter: true,
				
			},
			shipaddress1:{
				minlength: 3,
				required:true,
				FirstLetter: true,
			},
			shipstate:{
				required:true,
				number: false,
				FirstLetter: true,
				lettersonly: true,
			},
			shipcity:{
				required:true,
				FirstLetter: true,
				lettersonly: true,
			},
			shipzip:{
				required:true,
				number: true,
				FirstLetter: true,
				
			}
		},
		messages:{
			
		},
		submitHandler: function(form){
			form.submit();
		}	
	});
	$(function() {

$('#txtNumeric').keydown(function (e) {
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
</script>