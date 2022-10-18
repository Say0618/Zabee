<link href="<?php echo assets_url('plugins/fileinput/css/fileinput.min.css'); ?>" media="all" rel="stylesheet" type="text/css"/>
<script src="<?php echo assets_url('plugins/fileinput/js/fileinput.js'); ?>" type="text/javascript"></script>
<script src="<?php echo assets_url('plugins/fileinput/themes/fas/theme.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo assets_url('plugins/fileinput/js/plugins/sortable.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo assets_url('plugins/fileinput/js/plugins/purify.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo assets_url('plugins/fileinput/js/plugins/piexif.min.js'); ?>" type="text/javascript"></script>

<script>
jQuery.validator.addMethod("lettersonly", function(value, element) {
	return this.optional(element) || /^[A-Za-z," "]+$/i.test(value);
}, "Invalid input"); 

jQuery.validator.addMethod("FirstLetter", function(value, element) {
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	

$(function () {
 $("#rateYo").rateYo({
   rating: 0,
   halfStar: true,
   onSet: function (rating, rateYoInstance) {
        $('#rating').val(rating);
        $("#rateYo_error").html('');
    }
 }); 
});

var submit_form = "";
$(document).ready(function(){

    $("#reviewAdd").validate({
		errorElement: 'span',
		rules: {
			review: {
				FirstLetter: true,
				required:true,
			},
		},
  
        submitHandler: function(form) {
            if($('#rating').val() > 0){
                form.submit();
            }
            else{
                $("#rateYo_error").html("<span class='error ml-2'>rating required</span>");
	       return false;
                }
        },
		messages: {
			review: {
				required: "Review is required",
				FirstLetter: "First input cant be space",
            },
		},
    });

	var review_id = $("#review_id").val();
	$("#input-b8").fileinput({
        uploadUrl: "<?php echo base_url("buyer/uploadAjax")?>",
		enableResumableUpload: true,
        resumableUploadOptions: {
           // uncomment below if you wish to test the file for previous partial uploaded chunks
           // to the server and resume uploads from that point afterwards
           // testUrl: "http://localhost/test-upload.php"
        },
        uploadExtraData: {
			'uploadToken': 'zabee-brand', // for access control / security
			'type': 'review',
			'user_id': '<?php echo $_SESSION['userid']?>',
			'tn':"review_media",
			'id':review_id,
			'column':"review_id",
		},
        maxFileCount: 5,
		allowedFileTypes: ['image'],    // allow only images
		browseLabel: "Pick Image",
        showCancel: true,
        initialPreviewAsData: true,
		overwriteInitial: true,
		showUpload: false,
		autoReplace: true,
		minImageWidth: 150,
		minImageHeight: 150,
		// initialPreview: [],          // if you have previously uploaded preview files
        // initialPreviewConfig: [],    // if you have previously uploaded preview files
		theme: 'fas',
		rtl: true,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
	
    }).on('fileuploaded', function(event, previewId, index, fileId) {
		submit_form = 1;
        //console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
    }).on('fileuploaderror', function(event, data, msg) {
        // console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
    }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
		//console.log('File Batch Uploaded', preview, config, tags, extraData);
		if(review_id == ""){
			r_id = Array();
			$(config).each(function(index,value){
				r_id.push(value.key);
			});
			review_id = r_id.join();
			$("#review_id").val(review_id);
			extraData.name;
		}
		image_count = 1;
		if(submit_form == 1){
			$('#reviewAdd').submit();
		}
    });
	$("#reviewAdd").validate({
		errorElement: 'span',
		rules: {
			review: {
				FirstLetter: true,
				required:true,
			},
		},
        submitHandler: function(form) {
            if($('#rating').val() > 0){
				if(image_count ==1){
					form.submit();
				}else{
					$("#input-b8").fileinput('upload');
				}
            }
            else{
                $("#rateYo_error").html("<span class='error ml-2'>rating required</span>");
	       		return false;
            }
        },
		messages: {
			review: {
				required: "Review is required",
				FirstLetter: "First input cant be space",
            },
		},
    });
});
</script>