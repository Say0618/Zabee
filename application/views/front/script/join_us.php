<script>
$.validator.addMethod('strongPassword', function(value, element) {
 return this.optional(element) 
 || value.length >= 6
 && /\d/.test(value)
 && /[a-z]/i.test(value);
},'Enter a combination of at least 6 characters and numbers')

$.validator.addMethod( "nowhitespace", function( value, element ) {
return this.optional( element ) || /^\S+$/i.test( value );
}, "No white space please" ); 

$.validator.addMethod( "lettersonly", function( value, element ) {
return this.optional( element ) || /^[a-z]+$/i.test( value );
}, "please enter a valid name" );

jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
);

$("#join-us-form").validate({
 // errorElement : 'div',
     rules: {
        email: {
          nowhitespace: true,
          required: true,
          email: true
        },
        password: {
          required: true,
          strongPassword:true,
          nowhitespace:true
        },
        confirm_password: {
          required: true,
          equalTo: '#password'
        },
          terms : { 
          required: true
        },
        first_name: {
          required: true,
          nowhitespace:true,
          lettersonly:true,
          minlength: 3
        },
        last_name: {
          required: true,
          nowhitespace:true,
          lettersonly:true,
          minlength: 3
        },
        middle_name: {
          nowhitespace:true,
          lettersonly:true,
          minlength: 3
        }           
      }, 
 
 messages: {
 email: {
 required: 'Email address required.',
 email: 'Please enter a <em>valid</em> email address.',
 },

 confirm_password: {
 equalTo:"Password doesn't match"
 }
 },
 errorElement : 'span',
 errorPlacement: function(error, element) {
   console.log(error);
   console.log(element);
 if(element.attr("name") == "terms") {
 error.appendTo( element.parent() );
 } else {
 error.appendTo(element.parent().parent());
 }
}

});
$("#join-us-form").submit(function(event) {

var recaptcha = $("#g-recaptcha-response").val();
$('.recaptcha_error').css('display','none');
if (recaptcha === "") {
  $('.recaptcha_error').text('Recaptcha is required');
  $('.recaptcha_error').css('display','block');
   event.preventDefault();
  //  alert("Please check the recaptcha");
}
});
$(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
            $('#show_hide_confirm_password input').attr('type', 'password');
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
            $('#show_hide_confirm_password input').attr('type', 'text');
        }
    });
});
</script>