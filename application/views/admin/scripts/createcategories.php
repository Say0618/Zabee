<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
var image_count = "<?php echo ($category_data && $category_data->category_image !="")?1:0;?>"
var submit_form = "";
$(document).ready(function(){

    $("#keyword").keyup(function(){
        var name = $(this).val();
        if(name.includes(" ")) {
            var new_name = name.replace(/ /g, '-');
            $("#slug_name").val(new_name);
        }else{
            $("#slug_name").val(name);
        }
  });

    $("#category_id").select2();
    var category_id = $("#category_id").val();
	$("#input-b8").fileinput({
        uploadUrl: "<?php echo base_url("seller/categories/uploadAjax")?>",
		enableResumableUpload: true,
		required:true,
        resumableUploadOptions: {
           // uncomment below if you wish to test the file for previous partial uploaded chunks
           // to the server and resume uploads from that point afterwards
           // testUrl: "http://localhost/test-upload.php"
        },
        uploadExtraData: {
			'uploadToken': 'zabee-category', // for access control / security
			'type': 'categories',
			'user_id': '<?php echo $_SESSION['userid']?>',
			'tn':"categories",
			'id':category_id,
			'column':"category_id",
		},
        maxFileCount: 1,
		allowedFileTypes: ['image'],    // allow only images
		browseLabel: "Pick Image",
        showCancel: true,
        initialPreviewAsData: true,
		overwriteInitial: true,
        autoReplace: true,
        showUpload: false,
		minImageWidth: 150,
		minImageHeight: 150,
		// initialPreview: [],          // if you have previously uploaded preview files
        // initialPreviewConfig: [],    // if you have previously uploaded preview files
		theme: 'fas',
		rtl: true,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
        <?php if($category_data && $category_data->category_image !=""){?>
        showUpload: true,
		initialPreview: [
            "<?php echo base_url('uploads/categories/'.$category_data->category_image); ?>"
        ],
        initialPreviewConfig: [
            {caption: "<?php echo $category_data->category_image?>", width: "120px", key: <?php echo $category_data->category_id?>,extra:{name:"<?php echo $category_data->category_image?>"}},
        ],
		<?php }?>
		deleteUrl:'<?php echo base_url('seller/categories/delete_image'); ?>',
    }).on('fileuploaded', function(event, previewId, index, fileId) {
		//console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
    }).on('fileuploaderror', function(event, data, msg) {
        //console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
    }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
		//console.log('File Batch Uploaded', preview, config, tags, extraData);
    }).on('filebeforedelete', function(event, key, data) {
		var aborted = !window.confirm('Are you sure you want to delete this file?');
		return aborted;
    }).on('filedeleted', function() {
        setTimeout(function() {
            window.alert('File deletion was successful!');
        }, 900);
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
			category_name:{ required:true, FirstLetter:true },
			catdisplay_status:{ required:true },
		},
        messages:
        {
		  category_name:{ required: "Please enter category name" },
		  catdisplay_status:{ required: "Please select an option" },
		},
		submitHandler: function(form) {
			form.submit();
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
         }
	  });	
	$(document).on("click", ".resetform", function(event){
		location.reload();
	});
});
</script>