<script type="text/javascript" >
jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
);
	$("#loginForm").validate({
		//errorElement: 'span',
		rules: {
			user_pass: "required",
			user_email: {
			required: true,
			email: true
			}
		},
		messages: {
			user_pass: "Password is required",
			user_email: {
			required: 'Email is required',
			email: 'Valid email is required'
			}
		},
		errorPlacement: function (error, element) {
			<?php if(!form_error('user_email')){?>
				error.appendTo( element.parent().parent());
			<?php }?>
			/*var e = $('.login_error_css');
			if(e.length > 0){
				e.remove();
			}
			element.parent().parent().append('<span class="login_error_css" style="color:red" for="'+error.attr("for")+'">'+error.text()+'</span>');
			element.parent().addClass('has-error');*/
		}
	});
	/*$('#triggerGoogleLogin').click(function(){
		$('#googleLogin').trigger('click');
	});*/
	$( ".invalid_error" ).appendTo(".message-info");
</script>