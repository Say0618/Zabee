<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
var image_count = "<?php echo ($offersWeGot && $offersWeGot->offer_image !="")?1:0;?>"
var submit_form = "";
$(document).ready(function(){
	var offer_id = $("#offer_id").val();
	$("#position").val("<?php echo $this->session->userdata('position') ?>");
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
		// initialPreview: [],          // if you have previously uploaded preview files
        // initialPreviewConfig: [],    // if you have previously uploaded preview files
		theme: 'fas',
		rtl: true,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		<?php if($offersWeGot && $offersWeGot->offer_image !=""){?>
		initialPreview: [
            "<?php echo image_url('special_offer/'.$offersWeGot->offer_image); ?>"
        ],
        initialPreviewConfig: [
            {caption: "<?php echo $offersWeGot->offer_image?>", width: "120px", key: <?php echo $offersWeGot->id?>,extra:{name:"<?php echo $offersWeGot->offer_image?>"}},
        ],
		<?php }?>
		deleteUrl:'<?php echo base_url('seller/offers/delete_image'); ?>',
    }).on('fileuploaded', function(event, previewId, index, fileId) {
		submit_form = 1;
        //console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
    }).on('fileuploaderror', function(event, data, msg) {
        //console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
    }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
		//console.log('File Batch Uploaded', preview, config, tags, extraData);
		if(id == ""){
			o_id = Array();
			$(config).each(function(index,value){
				b_id.push(value.key);
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
    });;
	 $('#myform').validate({
        rules:{
			brand_name:{ required:true, FirstLetter: true },
			brand_description:{ required:true },
			display_status:{ required:true }
		},
        messages:
        {
			editcategory_name:{ required: "Please enter brand's name" },
			brand_description:{ required: "Please enter brand's description" },
			display_status:{ required: "Please select an option"}
		},
		submitHandler: function(form) {
			if(image_count ==1){
				form.submit();
			}else{
				$("#input-b8").fileinput('upload');
			}
		},
		errorPlacement: function(error, element) 
        {
            if ( element.is(":radio") ) 
            {
                error.appendTo( element.parent().parent() );
            }
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         }
	  });	
});
$(document).on("click", ".resetform", function(event){
    location.reload();
});
</script>