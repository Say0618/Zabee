<script src="https://www.paypal.com/sdk/js?client-id=<?php echo PayPalClientId; ?>&disable-funding=credit,card&intent=authorize"></script>
<script>
	jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter can not be a space");
jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
);
$('#guestForm').validate({
    rules:{
        user_email:{
            required: true,
            FirstLetter:true,
            email:true
        },
        user_pass:{
            required: true,
            FirstLetter:true,
        }
    },
    errorPlacement: function (error, element) {
        error.appendTo(element.parent().parent());
	}
});


paypal.Buttons({
  env: '<?php echo PayPalENV; ?>',
  style: {
    layout:  'vertical',
    size: 'responsive',
    shape: "rect",
    height: 38,
  },
  // Set up the transaction
  createOrder: function(data, actions) {
      return actions.order.create({
          purchase_units: [{
              amount: {
                  value: <?php echo $this->cart->format_number($this->cart->total()); ?>
              }
          }]
      });
  },

  // Finalize the transaction
  onApprove: function(data, actions) {
    actions.order.authorize().then(function(authorization) {
			if(authorization.id != ""){
				console.log(authorization);
				$("#paypal_transID").val(authorization.purchase_units[0].payments.authorizations[0].id);
				$("#paypal_payer").val(JSON.stringify(authorization.payer));
				$( "#order_form" ).submit();
				
			}
		});
  }


}).render('#paypal_button_as_guest');

</script> 