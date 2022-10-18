<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	

$.validator.addMethod( "specialChars", function( value, element ) {
return this.optional( element ) || /^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/g.test( value );
}, "please enter a valid name" );


$(document).ready(function(){
	// $("#input-b8").fileinput({
    //     //required:true,
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
		dropZoneEnabled: false,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		
	});
	$(".reset_btn").on('click', function () {
       $(".error").html("");
    });
	$(".btn-warning").on('click', function () {
        var $el = $("#file-4");
        if ($el.attr('disabled')) {
            $el.fileinput('enable');
        } else {
            $el.fileinput('disable');
        }
    });
	
	 $('#myform').validate({
        rules:{
			editcategory_name:{ required:true,FirstLetter:true, specialChars:true },
			editdisplay_status:{ required:true },
			//profile_image:{ required:true },
		},
        messages:
        {
		  editcategory_name:{ required: "Please enter category name" },
		  editdisplay_status:{ required: "Please select an option" },
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
		$("input").change(function(e) {

			for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {
				
				var file = e.originalEvent.srcElement.files[i];
				
				//var img = document.createElement("img");
				var reader = new FileReader();
				reader.onloadend = function() {
					 document.getElementById("myImgs").src  = reader.result;
				}
				reader.readAsDataURL(file);
				$("input").after(img);
			}
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
$(document).on("click", ".cross-btn", function(event){
	var cat_id = $("#cat_id").val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/categories/removeImage');?>",
		data:{"cat_id":cat_id},
		success: function(response){
			location.reload();
		}
	});
});

</script>
