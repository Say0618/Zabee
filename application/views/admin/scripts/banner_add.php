<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<script>
var image_count = "<?php echo (isset($bannersWeGot) && $bannersWeGot->banner_image !="")?1:0;?>";
var submit_form = "";
$(document).ready(function(){
	$('#category_id').select2({width: 'resolve', placeholder: function(){
		$(this).data('placeholder');}}).on('change', function() {$(this).valid();});
	var id = $("#banner_id").val();
	var link_val = $("#linktype").val();
	if(link_val == "ProductLink"){
		$(".ProductBox").attr("style","display:'block'");
	} else if(link_val == "ExternalLink") {
		$(".ExternalBox").attr("style","display:'block'");
	}
	$("#input-b8").fileinput({
        uploadUrl: "<?php echo base_url("seller/banner/uploadAjax")?>",
		enableResumableUpload: true,
		required:true,
        resumableUploadOptions: {
           // uncomment below if you wish to test the file for previous partial uploaded chunks
           // to the server and resume uploads from that point afterwards
           // testUrl: "http://localhost/test-upload.php"
        },
        uploadExtraData: {
			'uploadToken': 'zabee-brand', // for access control / security
			'type': 'banner',
			'user_id': '<?php echo $_SESSION['userid']?>',
			'tn':"banners",
			'id':id,
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
		theme: 'fas',
		rtl: true,
		showUpload: false,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		<?php if(isset($bannersWeGot) && $bannersWeGot->banner_image !=""){?>
		showUpload: true,
		initialPreview: [
            "<?php echo image_url('banner/'.$bannersWeGot->banner_image); ?>"
        ],
        initialPreviewConfig: [
            {caption: "<?php echo $bannersWeGot->banner_image?>", width: "120px", key: <?php echo $bannersWeGot->id ?>,extra:{name:"<?php echo $bannersWeGot->banner_image?>"}},
        ],
		<?php }?>
		deleteUrl:'<?php echo base_url('seller/banner/delete_image'); ?>',
    }).on('fileuploaded', function(event, previewId, index, fileId) {
		submit_form = 1;
        //console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
    }).on('fileuploaderror', function(event, data, msg) {
        //console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
    }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
		//console.log('File Batch Uploaded', preview, config, tags, extraData);
		if(id == ""){
			b_id = Array();
			$(config).each(function(index,value){
				b_id.push(value.key);
			});
			id = b_id.join();
			$("#banner_id").val(id);
			extraData.name;
		}
		image_count = 1;
		if(submit_form == 1){
			// console.log(image_count+" - "+submit_form);
			$('#myform').submit();
		}
    }).on('filebeforedelete', function(event, key, data) {
		var aborted = !window.confirm('Are you sure you want to delete this file?');
		if(!aborted){
			image_count = 0;
			id = id.replace(","+key,"");
			submit_form = 0;
		}
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
	 $('#myform').validate({
        rules:{
			Banner_Link:{ required: function(element){
				return ($('#linktype').val() == 'ExternalLink')}
			},
			Product_Link:{ required: function(element){
				return ($('#linktype').val() == 'ProductLink')}
			},
			linktype:{ required:true }
		},
        messages:
        {
		  Banner_Link:{ required: "Please provide the external link or URL" },
		  linktype:{ required: "select any one link" },
		},
		submitHandler: function(form) {
			if(image_count ==1){
				form.submit();
			}else{
				$("#input-b8").fileinput('upload');
			}
		}
		
	  });
	$('#linktype').on('click', function() {
		//alert();
		var val = $(this).val();
		//alert(val);
		if(val == "ProductLink"){
			$(".ProductBox").show();
			$(".ExternalBox").hide();
		} else if(val == "ExternalLink") {
			$(".ExternalBox").show();
			$(".ProductBox").hide();
		} else {
			$(".ExternalBox").hide();
			$(".ProductBox").hide();
		}
		});
	});
		$("#keyword").autocomplete({
		source: function( request, response ) {
			$.ajax({
			  url: "<?php echo base_url('seller/product/get_product');?>/"+request.term,
			  dataType: "json",
			  success: function( data ) {
				$('label.error').html('');
				$('.error_text').html('');
				//$('#product_id').val('');
				//$('#pn').text('');
				if(data != ""){
					response( data );
				}else {
				//$('#pn').text('No results found.');
				//response([{ label: 'No results found.', val: -1}]);
				$(".error_text").text("Product Not Found");
				response("");
				$('#product_id').val('');
			}
			  }
			});
		  },
		  minLength: 1,
		  select: function( event, ui ) {
				$("#product_id").val(ui.item.id);
				//alert($("#product_id").val());				
			}
	});
/*$( "form" ).submit(function( event ) {
	$(".error_text").text("");
	if($('#keyword').val() != ""){
		if ($('#linktype').val() == 'ProductLink' && $("#product_id").val() == "") {
				$(".error_text").text("Product Not Found");
				return false;
		}
	}else{
		if($('#Banner_Link').val() != ""){
			return true;
		}
		$(".error_text").text("this field is required");
		return false;
	}
});*/
$(".Product_Link").on("keyup", function(e) {
	var code = e.which;
	if (code == 8) {
		$('#product_id').val('');
	}
});
/*$("#input-b8").change(function(e) {
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

})*/
</script>