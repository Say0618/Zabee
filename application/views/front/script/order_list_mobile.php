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

    });
</script>