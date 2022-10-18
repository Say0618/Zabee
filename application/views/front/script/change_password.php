<script>
$.validator.addMethod('strongPassword', function(value, element) {
return this.optional(element) 
|| value.length >= 6
&& /\d/.test(value)
&& /[a-z]/i.test(value);
},'Use at least 6 characters including 1 alphabet and number');

$.validator.addMethod( "nowhitespace", function( value, element ) {
return this.optional( element ) || /^\S+$/i.test( value );
}, "No white space please" ); 
$("#change_password").validate({
rules: {
password: {
required: true,
strongPassword:true,
nowhitespace:true
},
confirm_password: {
required: true,
equalTo: '#password', 
nowhitespace:true
}
},
messages: {
confirm_password: {
equalTo:"Password doesn't match"
}
}
});

</script>