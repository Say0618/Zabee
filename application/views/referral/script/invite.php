<script>
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

$("#invite-form").validate({
 // errorElement : 'div',
     rules: {
        email: {
          nowhitespace: true,
          required: true,
          email: true
        },
        // password: {
        //   required: true,
        //   strongPassword:true,
        //   nowhitespace:true
        // },
        // confirm_password: {
        //   required: true,
        //   equalTo: '#password'
        // },
        //   terms : { 
        //   required: true
        // },
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
      }, 
 
 messages: {
 email: {
 required: 'Email address required.',
 email: 'Please enter a <em>valid</em> email address.',
 },

//  confirm_password: {
//  equalTo:"Password doesn't match"
//  }
 },
 errorElement : 'span',
 errorPlacement: function(error, element) {
  console.log(error);
  console.log(element);
  error.appendTo( element.parent() );
}

});
$("#invite-form").submit(function(event) {

var recaptcha = $("#g-recaptcha-response").val();
$('.recaptcha_error').css('display','none');
if (recaptcha === "") {
  $('.recaptcha_error').text('Recaptcha is required');
  $('.recaptcha_error').css('display','block');
   event.preventDefault();
  //  alert("Please check the recaptcha");
}
});
</script>