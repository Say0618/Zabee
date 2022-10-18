<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
$.validator.addMethod( "specialChars", function( value, element ) {
return this.optional( element ) || /^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/g.test( value );
}, "please enter a valid name" );
$(document).ready(function(){

	$('#category_id').select2({width: 'resolve', placeholder: function(){ $(this).data('placeholder');}}).on('change', function() {$(this).valid();});
	// $("#input-b8").fileinput({
	// 	rtl: true,
	// 	dropZoneEnabled: false,
	// 	allowedFileExtensions: ["jpg", "png", "gif"]
	// });
	$("#input-b8").fileinput({
		required: true,
		allowedFileTypes: ["image"],
		browseLabel: "Pick Image",
		uploadUrl: "<?php echo base_url()?>",
		theme: "fas",
		minImageWidth: 400,
		minImageHeight: 400,
		maxFileCount: 1,
		validateInitialCount: true,
		overwriteInitial: false,
		showUpload: false,
		rtl: true,
		showUpload: false,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
	});
    $(".btn-warning").on('click', function () {
        var $el = $("#file-4");
        if ($el.attr('disabled')) {
            $el.fileinput('enable');
        } else {
            $el.fileinput('disable');
        }
    });
    $(".btn-info").on('click', function () {
        $("#file-4").fileinput('refresh', {previewClass: 'bg-info'});
    });
	 $('#myform').validate({
        rules:{
			subcategory_name:{ required:true, FirstLetter:true },
			subdisplay_status:{ required:true },
			parent_category_id:{ required:true },
			subdisplay_status:{ required:true }
		},
        messages:
        {
		  subcategory_name:{ required: "Please enter category name" },
		  subdisplay_status:{ required: "Please select an option" },
		  parent_category_id:{ required: "Please select an option" },
		  subdisplay_status:{ required: "Please select an option" }
		},
		errorPlacement: function(error, element) 
        {
            if ( element.is(":radio") ) 
            {
                error.appendTo( element.parents('.container') );
            }
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         },
		
	  });	
});
$("#input-b8").change(function(e) {
    var val = $(this).val();
	var res = val.split('.');
	res = res.reverse();
	if(res[0] == "png" || res[0] == "jpg" || res[0] == "gif" || res[0] == "PNG" || res[0] == "JPG" || res[0] == "GIF"){
		$("#Submit").prop('disabled', false);
		$("#incorrect_file_format").css('display','none');
	} else {
		$("#Submit").prop('disabled', true);
		$("#incorrect_file_format").css('display','block');
	}
	setTimeout(function(){
		if($(".file-error-message").attr("style") != "display: none;"){
			$("#Submit").prop('disabled', true);
		} 
		}, 500
	);
});
$(document).on("click", ".fileinput-remove", function(event){
	$("#Submit").prop('disabled', false);
	$("#incorrect_file_format").css('display','none');
});
$(document).on("click", ".resetform", function(event){
    location.reload();
});
</script>