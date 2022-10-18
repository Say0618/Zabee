<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
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
		brand_name:{ required:true, FirstLetter: true },
		brand_description:{ required:true },
		branddisplay_status:{ required:true }
	},
	messages:
	{
		editcategory_name:{ required: "Please enter brand's name" },
		brand_description:{ required: "Please enter brand's description" },
		branddisplay_status:{ required: "Please select an option"}
	},
	errorPlacement: function(error, element) 
	{
		if ( element.is(":radio") ) 
		{
			error.appendTo( element.parents('.mt-2') );
		}
		else 
		{ // This is the default behavior 
			error.insertAfter( element );
		}
	}
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
/*$(document).on("click", "#Submit", function(event){
	 var val = $("#input-b8").val();
	 alert(val);
});*/
</script>