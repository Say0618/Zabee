<script>
	$(document).ready(function(){
		$('.deleteCard').click(function(){
			var card_id = $(this).attr('data-id');
			var card_name = $(this).attr('data-name');
			$('#card_id').val(card_id);
			$('.card_name').text(card_name);
			$('#confirmation-modal').modal('show');
		});
		$('#card_delete').click(function(){
			var card_id = $('#card_id').val();
			if(card_id == ''){
				$('#confirmation-modal .error').removeClass('d-none');
				return false;
			}
			var url = '<?php echo base_url("account/delete_card/"); ?>'+card_id;
			window.location.href = url;
		});
	});
</script> 