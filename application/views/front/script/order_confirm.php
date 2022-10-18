<script>
$(document).ready(function(){
$('.contact-seller').click(function(){
			$("#pv_id").val($(this).attr('data-pv_id'));
			$("#sp_id").val($(this).attr('data-sp_id'));
			$("#seller_id").val($(this).attr('data-seller_id'));
			var s = $(this).attr('data-store_name');
			$('#storeName').html($(this).attr('data-store_name'));
			$("#message-panel").modal('show');
		});
	$('#sendMessage').click(function(){
		var subject = $("#subject").val();
		var message = $("#message").val();
		var pv_id = $("#pv_id").val();
		var seller_id = $("#seller_id").val();
		var sp_id = $("#sp_id").val();
		var UTCDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
		if(message == ""){
			$("#message").next().html('<strong class="error">Please Enter Message!</strong>');
			return false;
		}
		if(subject !="" && message !=""){
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>product/saveMessage",
				data:{'receiver_id':seller_id,"item_id":sp_id,"item_type":"product",'message':message,'seller_id':seller_id,'buyer_id':'<?php echo (isset($_SESSION['userid'])?$_SESSION['userid']:0); ?>','product_variant_id':pv_id,'time':UTCDateTime},
				success: function(response){
					if(response.status == 1){
						$('#message-panel').modal('toggle');
						$('#message-notification').modal('show');
						setTimeout(function() {
							$('#message-notification').modal('hide');
  							}, 3000); 
						$('#change-message').text("Message sent successfully");
						$("#message").val("");
					} 
				}
			});
		}
	});
});
</script>