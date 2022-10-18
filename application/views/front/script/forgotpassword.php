
<!-- <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script> -->
<script>
  $.validator.addMethod( "nowhitespace", function( value, element ) {
    return this.optional( element ) || /^\S+$/i.test( value );
    }, "No white space please" );  
jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
); 
$("#form").validate({
  rules:{ 
    email:{
    nowhitespace:true,
    required: true,
    email: true,
  },
  userName:{
    required:true
  }
    },
    messages: {
    email: {
      required:  'Email address is required.',
      email: 'Please enter a <em>valid</em> email address.',
      
    }
    },
  errorPlacement : function(error,element) {$(element).parent().after(error);	}
    });

    $("#reset_form").validate({
  rules:{
    code: {
                required: {
                    depends:function(){
                        $(this).val($.trim($(this).val()));
                        return true;
                    }
                },
            },
     
    },
  messages:{
      code:{
        required:"Please enter code."
      },
      
  },
  
  });
  $('#pass-link-btn').on('click',function(){

  })
//   $( "#pass-link-btn" ).click(function() {
//   $( this ).addClass('d-none');
//   $('.code-field').removeClass('d-none')
// });
  
    </script>
  