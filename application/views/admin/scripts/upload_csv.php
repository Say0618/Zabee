<script>
	 $('#uploadCSV').validate({
        rules:{
			csv_file:{ required:true },
			seller_id:{ required:true },
		},
        messages:
        {
		  csv_file:{ required: "You must select a file to upload." },
		  seller_id:{ required: "select any one seller" },
		},
		
	  });
$( "form" ).submit(function( event ) {
	$(".error_text").text("");
	if($('#keyword').val() != ""){
		if ($('#linktype').val() == 'ProductLink' && $("#product_id").val() == "") {
				$(".error_text").text("Product Not Found");
				return false;
		}
	}else{
		if($('#Banner_Link').val() != ""){
			return true;
		}
		$(".error_text").text("this field is required");
		return false;
	}
});
</script>