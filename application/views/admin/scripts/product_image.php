<style>
.radioerror{ color:red !important; font-size:20px !important;}
</style>
<script>
$(document).ready(function(){
	 $('#myform').validate({
        rules:{
			primary_image:{ 
				required:true 
			},
		},
        messages:
        {
		  primary_image:{ required: "Select a image" },
		},
		errorPlacement: function(error, element) 
        {
            if ( element.is(":radio") ) 
            {
				$('.radioerror').html('Select any one Image as pimrary');
				//element.parents().parents().parents().parents().parents().find('.radioerror').html('Select an Image');
				//error.appendTo( element.parents().parents().parents('.container-image'));
            }
            else 
            { // This is the default behavior 
			   error.insertAfter( element );
            }
         }
	});	
});
$(document).on('click','.container-image',function(){
	$('.radioerror').html('');
});
$('.productImage').click(function() {
    $('.productImage').removeClass('select-box');
	$(this).addClass('select-box');
});
$(function () {
    $(":file").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function imageIsLoaded(e) {
    $('#myImg').attr('src', e.target.result);
};
$('.product-delete').click(function() {
   var value = <?php echo $forimageloop ?>;
	if(value == 1){
		alert("At least one image is required");
	}
});
</script>