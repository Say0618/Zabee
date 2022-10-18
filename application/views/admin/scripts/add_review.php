<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script>
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
var submit_form = "";
var review_id = "";
var requiredCondition = false;
$(document).ready(function(){
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
	$("#rateYo").rateYo({
		rating: 0,
		halfStar: true,
		onSet: function (rating, rateYoInstance) {
			$('#rating').val(rating);
			$("#rateYo_error").html('');
		}
	});
	$('#myform').validate({
		rules:{
			product_name:{ required:true},
			store:{ required:true},
			condition:{ required:true},
			variant:{ required:requiredCondition},
			name:{ required:true},
			review:{ required:true},
			rating:{ required:true},
		},
		submitHandler: function(form) {
			if($('#rating').val() > 0){
				form.submit();
			}
            else{
                $("#rateYo_error").html("<span class='error ml-2'>rating required</span>");
	       		return false;
            }
			form.submit();
		},
		errorPlacement: function(error, element) {
			if ( element.is(":radio") ){
				error.appendTo( element.parent().parent() );
			}else { // This is the default behavior 
				error.insertAfter( element );
			}
		}
	});	
	$("#keyword").autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "<?php echo base_url('seller/product/get_product');?>/"+request.term,
				dataType: "json",
				success: function( data ) {
					$('label.error').html('');
					//$('#pn').text('');
					if(data != ""){
						response( data );
					}else{
						//$('#pn').text('No results found.');
						//response([{ label: 'No results found.', val: -1}]);
						response("");
						$("#editTitle").parent().hide();
						$("#keyword").removeAttr("readonly");
						$('#product_id').val('');
					}
				}
			});
		},
		minLength: 1,
		select: function( event, ui ) {
			$("#product_id").val(ui.item.id);
			$("#store").html("");
			$("#condition").html("");
			$("#variant").html("");
			$.ajax({
				url: "<?php echo base_url('seller/reviews/getStore');?>/"+ui.item.id,
				dataType: "json",
				success: function(response) {
					if(response.status == 1){
						$("#store").removeAttr("readonly");
						$("#store").append('<option value="">-Select Store-</option>');
						$(response.data).each(function(index,value){
							$("#store").append('<option value="'+value.seller_id+'">'+value.store_name+'</option>');
						});
					}else{
						$("#store").attr("readonly");
						$("#condition").attr("readonly");
						$("#variant").attr("readonly");
					}
				}
			});
		}
	});
});
$("#store").on("change",function(){
	seller_id = $(this).val();
	product_id = $("#product_id").val();
	$("#condition").html("");
	$("#seller_id").val(seller_id);
	if(seller_id !=""){
		$("#store-error").remove();
		$.ajax({
			url: "<?php echo base_url('seller/reviews/getCondition');?>/"+seller_id+"/"+product_id,
			dataType: "json",
			success: function(response) {
				if(response.status == 1){
					$("#condition").removeAttr("readonly");
					$("#condition").append('<option value="">-Select Condition-</option>');
					$(response.data).each(function(index,value){
						$("#condition").append('<option value="'+value.condition_id+'" data-sp_id="'+value.sp_id+'">'+value.condition_name+'</option>');
					});
					$("#sp_id").val(sp_id);
				}else{
					$("#condition").attr("readonly");
				}
			}
		});
	}else{
		$("#store").parent().append('<span class="error" id="store-error">Please Select Store</span>');
	}
});
$("#condition").on("change",function(){
	condition_id = $(this).val();
	var sp_id = $('option:selected', this).attr('data-sp_id');
	seller_id = $("#seller_id").val();
	product_id = $("#product_id").val();
	$("#variant").html("");
	$("#condition_id").val(condition_id);
	if(condition_id !=""){
		$("#condition-error").remove();
		$.ajax({
			url: "<?php echo base_url('seller/reviews/getVariant');?>/"+product_id+"/"+seller_id+"/"+condition_id+"/"+sp_id,
			dataType: "json",
			success: function(response) {
				$("#sp_id").val(sp_id);
				if(response.status == 1){
					$("#variant").removeAttr("readonly");
					$("#variant").append('<option value="">-Select Variant-</option>');
					requiredCondition = true;
					$(response.data).each(function(index,value){
						$("#variant").append('<option value="'+value.pv_id+'">'+value.variant+'</option>');
					});
				}else if(response.status == 2){
					$("#variant").removeAttr("readonly");
					$("#variant").append('<option value="'+response.data.pv_id+'">-No variant found-</option>');
					$("#pv_id").val(response.data.pv_id);
				}else{
					$("#variant").attr("readonly");
				}
			}
		});
	}else{
		$("#sp_id").val("");
		$("#condition").parent().append('<span class="error" id="condition-error">Please Select Condition</span>');
	}
});
$("#variant").on("change",function(){
	pv_id = $(this).val();
	$("#pv_id").val(pv_id);
	if(pv_id !=""){
		$("#variant-error").remove();
	}else{
		$("#variant").parent().append('<span class="error" id="variant-error">Please Select Variant</span>');
	}
});
$(document).on("click", ".resetform", function(event){
    location.reload();
});

$("#reviewDate").datetimepicker({
	dateFormat: 'yy-m-dd',
	timeFormat: 'HH:mm:ss'
});
</script>