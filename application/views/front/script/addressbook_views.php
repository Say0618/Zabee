<script>
    $(document).ready(function(){
        var height = $( ".checkHeight" ).last().height();
        $("#add-location").height(height);

         $('input:radio').click(function() {  
        var value = $(this).val();
        var location = "<?php echo base_url('checkout/useAddressforbuyer/')?>"+value; 
       window.location = location
    }); 
    });

</script>
