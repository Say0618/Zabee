<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<script>
var image_count = "<?php echo (isset($offersWeGot) && $offersWeGot->offer_image !="")?1:0;?>"
var submit_form = "";
$(document).ready(function(){
	$("#position").val("<?php echo (isset($offersWeGot) && $offersWeGot->offer_image !="")?ucwords($offersWeGot->position):""?>");
	var offer_id = $("#offer_id").val();
	$("#input-b8").fileinput({
        uploadUrl: "<?php echo base_url("seller/offers/uploadAjax")?>",
		enableResumableUpload: true,
		required:true,
        resumableUploadOptions: {
           // uncomment below if you wish to test the file for previous partial uploaded chunks
           // to the server and resume uploads from that point afterwards
           // testUrl: "http://localhost/test-upload.php"
        },
        uploadExtraData: {
			'uploadToken': 'zabee-brand', // for access control / security
			'type': 'special_offer',
			'user_id': '<?php echo $_SESSION['userid']?>',
			'tn':"special_offers",
			'id':offer_id,
			'column':"id",
		},
        maxFileCount: 1,
		allowedFileTypes: ['image'],    // allow only images
		browseLabel: "Pick Image",
        showCancel: true,
        initialPreviewAsData: true,
		overwriteInitial: true,
		autoReplace: true,
		minImageWidth: 150,
		minImageHeight: 150,
		showUpload: false,
		// initialPreview: [],          // if you have previously uploaded preview files
        // initialPreviewConfig: [],    // if you have previously uploaded preview files
		theme: 'fas',
		rtl: true,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		<?php if(isset($offersWeGot) && $offersWeGot->offer_image !=""){ ?>
		initialPreview: [
            "<?php echo image_url('special_offer/'.$offersWeGot->offer_image); ?>"
        ],
        initialPreviewConfig: [
            {caption: "<?php echo $offersWeGot->offer_image?>", width: "120px", key: <?php echo $offersWeGot->id?>,extra:{name:"<?php echo $offersWeGot->offer_image?>"}},
        ],
		<?php } ?>
		deleteUrl:'<?php echo media_url('seller/special_offer/delete_image'); ?>',
    }).on('fileuploaded', function(event, previewId, index, fileId) {
		submit_form = 1;
        //console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
    }).on('fileuploaderror', function(event, data, msg) {
        //console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
    }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
		//console.log('File Batch Uploaded', preview, config, tags, extraData);
		if(offer_id == ""){
			o_id = Array();
			$(config).each(function(index,value){
				o_id.push(value.key);
			});
			offer_id = o_id.join();
			$("#offer_id").val(offer_id);
			extraData.name;
		}
		image_count = 1;
		if(submit_form == 1){
			$('#myform').submit();
		}
    }).on('filebeforedelete', function(event, key, data) {
		var aborted = !window.confirm('Are you sure you want to delete this file?');
		if(!aborted){
			image_count = 0;
			offer_id = offer_id.replace(","+key,"");
			submit_form = 0;
		}
        return aborted;
    }).on('filedeleted', function() {
        setTimeout(function() {
            window.alert('File deletion was successful!');
        }, 900);
    });
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
			offer_name:{ required: true},
			position:{ required:true }
		},
        messages:{
			offer_name:{ required: "Please provide a name" },
			position:{ required: "select any one position" },
		},
		submitHandler: function(form) {
			if(image_count ==1){
				form.submit();
			}else{
				$("#input-b8").fileinput('upload');
			}
		}
		
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
});
$(document).on("click", ".fileinput-remove", function(event){
	$("#Submit").prop('disabled', false);
	$("#incorrect_file_format").css('display','none');
});
$(".input-b8").change(function(){

if($(".file-caption-name").attr('title')!=""){
$("#input-b8-error").css("display","none");

}

})
</script>