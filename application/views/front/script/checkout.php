<script type="text/javascript">
$(document).ready(function(){
	 $('#checkout').validate({
        rules:{
			billing_full_name:{ 
				required:true 
			},
			billing_phone:{ 
				required:true,
				number: true
			},
			billing_address_1:{ 
				required:true 
			},
			billing_address_2:{ 
				required:true 
			},
			billing_city:{ 
				required:true 
			},
			billing_state:{ 
				required:true 
			},
			billing_zipcode:{ 
				required:true 
			},
			shipping_full_name:{ 
				required:true 
			},
			shipping_phone:{ 
				required:true,
				number: true
			},
			shipping_address_1:{ 
				required:true 
			},
			shipping_address_2:{ 
				required:true 
			},
			shipping_city:{ 
				required:true 
			},
			shipping_state:{ 
				required:true 
			},
			shipping_zipcode:{ 
				required:true 
			},
		},
		  messages:
        {
		  billing_full_name:{ required: "Billing name is required" },
		  billing_phone:{ required: "Billing phone is required" },
		  billing_address_1:{ required: "Billing address is required" },
		  billing_city:{ required: "Billing city is required" },
		  billing_state:{ required: "Billing state is required" },
		  billing_zipcode:{ required: "Billing zipcode is required" },
		  shipping_full_name:{ required: "shipping name is required" },
		  shipping_phone:{ required: "shipping phone is required" },
		  shipping_address_1:{ required: "shipping address is required" },
		  shipping_city:{ required: "shipping city is required" },
		  shipping_state:{ required: "shipping state is required" },
		  shipping_zipcode:{ required: "shipping zipcode is required" },
		},
    });	
});
	$('#same_shipping').change(function(){
		if($('#same_shipping').prop('checked')){
			$('#shipping_full_name').val($('#billing_full_name').val());
			$('#shipping_phone').val($('#billing_phone').val());
			$('#shipping_address_1').val($('#billing_address_1').val());
			$('#shipping_address_2').val($('#billing_address_2').val());
			$('#shipping_city').val($('#billing_city').val());
			$('#shipping_state').val($('#billing_state').val());
			$('#shipping_zipcode').val($('#billing_zipcode').val());
			$('#shipping_country option[value="'+$('#billing_country option:selected').val()+'"]').prop('selected', true);
		}
	})
</script>