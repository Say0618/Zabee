<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<script src="<?php echo assets_url();?>front/js/jquery.rateyo.min.js"></script>
<script>
	$(document).ready(function(){
		
		$(".rateYo").rateYo({
			readOnly: true,
			starWidth: "16px",
			halfStar: true
		});

		$('#Modal').on('show.bs.modal', function(e) {
			//get data-review attribute of the clicked element
			// console.log(e.relatedTarget);
			// var review = $(e.relatedTarget).attr('data-review');
			// var r = decodeURIComponent(window.atob(review));
			// var editedR = r.split("+").join(" ");
			// $('#reviewModal').html(editedR);
			var pictures;
			var data = $(e.relatedTarget).attr('data-encode');
			var order_data = decodeURIComponent(window.atob(data));
			order_data = order_data.split("-zabeeBreaker-");
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>buyer/getReviewData",
				data:{'orderData':order_data},
				success: function(response){
					$(".rateYo").rateYo("option", "rating", response[0].rating);
					$('.review-modal #reviewText').html(response[0].review);
					if($(".pictures img").length <= 0){
						if(response[0].thumbnail != null ){
							if(response[0].thumbnail.includes(',')){
								pictures = (response[0].thumbnail).split(',');
								for(index = 0; index < pictures.length; index++){
									if(index == 0){
										$(".review-modal .pictures").html("<img class='img-thumbnail' data-pic='"+pictures[index]+"' src='<?php echo $this->config->item("image_url");?>review/thumbs/"+pictures[index]+"'/>");
									}else{
										$(".review-modal .pictures").append("<img class='img-thumbnail' data-pic='"+pictures[index]+"' src='<?php echo $this->config->item("image_url");?>review/thumbs/"+pictures[index]+"'/>");
									}
								}
							}else{
								$(".review-modal .pictures").html("<img class='img-thumbnail' data-pic='"+response[0].thumbnail+"' src='<?php echo $this->config->item("image_url");?>review/thumbs/"+response[0].thumbnail+"'/>");
							}
							$(".review-modal .pictures img").css("max-width", "50px");
						}else{
							$(".review-modal .pictures").html("<span class='text-danger'>No media found</span>");
						}
					}
				}
			});
		});

		$(".pictures img").on("click", function(){
			console.log("abcd");
		});
		// function fullImage(){
		// 	image = ($(this).data("pic")).replace("_thumb","");
		// 	console.log(image);
		// 	$(".full-image-line").removeClass("d-none");
		// 	$(".original-img").removeClass("d-none");
		// 	$(".review-modal .original-img img").attr("src",'<?php echo $this->config->item("image_url");?>review/'+image);
		// }

		$(".order_time").each(function(){ 
			var date = $(this).text();
			date = date.replace(/-/g,'/')
			date = new Date(date+" UTC");
			date =  new Date(date.toString());
			$(this).text(formatUTCAMPM(date));
		})
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
		$('.item-modal').on('click',function(){
			var itemId = $(this).attr('data-order_item_id');
			$('#confirm_item').click(function(){
				$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>Buyer/saveStatus",
				data:{'id':itemId},
				success: function(response){
					console.log(response);
					if(response == true){
						window.location.reload();
					} 
				}
				});
			});
		});
		
		$('.cancel-order').on('click',function(){
			$('#cancel_order_id').val($(this).attr('data-order_id'));
			$('#td_id').val($(this).attr('data-td_id'));
			$('#seller_id').val($(this).attr('data-seller_id'));
			$('#product_name').val($(this).attr('data-product_name'));
			$('#button_id').val($(this).attr('id'));
			$('#item_confirmation').modal('show');
		});
		$('#confirm_item').click(function(){
			order_id = $('#cancel_order_id').val();
			button = $('#button_id').val();
			td_id = $('#td_id').val();
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>Buyer/CancelOrder",
				data:{'order_id':order_id, 'td_id':td_id},
				success: function(response){
					console.log(response);
					if(response.status == "1"){
						$("#status-"+order_id).html("Cancel Requested");
						$("#"+button).attr("class", "btn btn-dark cancel-order");
						$("#"+button).attr("disabled", "disabled");
						$("#"+button).html("Cancellation Request Pending");
						sendNotification($('#button_id').val(), $('#seller_id').val(), $('#product_name').val(), $('#cancel_order_id').val());
					}else if(response.status == "2"){
						$("#status-"+order_id).html(response.message);
						$("#"+button).attr("class", "btn btn-dark cancel-order");
						$("#"+button).attr("disabled", "disabled");
						$("#"+button).html(response.code);
						sendNotification($('#button_id').val(), $('#seller_id').val(), $('#product_name').val(), $('#cancel_order_id').val());
					}
				}	
			});
		});

		function sendNotification(btn, seller_id, product_name){
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>Buyer/sendNotification",
				data:{'seller_id':seller_id, 'product_name':product_name},
				success: function(response){
					$("#"+btn).text('Pending')
					$("#"+btn).css('background', '#ddd')
					$("#"+btn).css('border', '#ddd')
				}	
			});
		}
		function sendToRefundFunc(btn, seller_id, product_name, order_id){
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>Checkout/ProcessRefund",
				data:{'order_id':order_id},
				success: function(response){
					$("#"+btn).text('Pending')
					$("#"+btn).css('background', '#ddd')
					$("#"+btn).css('border', '#ddd')
				}	
			});
		}
	});
</script> 