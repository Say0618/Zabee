<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script> 
<script>
	var vg = 0;
	var catVariant = "";
	var removeAttributeIndex = [];
	var variantVal = "";
	var messageLength = "";
	var minFeature = "";
	var media_id = "";
	var product_id = "";
	var submit_form = "";
	var userid = "<?php echo $this->session->userdata('userid')?>";
	var userType = "<?php echo $this->session->userdata['user_type']?>";
	minFeature = $('#featuresDiv').find('input').length;
	jQuery.validator.addMethod("YoutubeURL", function(value, element) {
		return this.optional(element) ||  /(http:|https:|)\/\/(player.|www.)?(dailymotion\.com|vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/.test(value);
	}, "Only Youtube, Vimeo and Dailymotion URL are required, make sure the entered URL is correct");
	jQuery.validator.addMethod("iframeValidator", function(value, element) {
		return this.optional(element) ||  /(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))?/.test(value);
	}, "Only embedded videos(must have iframe) are allowed");
	$(document).ready(function(){
		// $(".num").on("keyup", function(){
		// 	var regex = /^(\d+\.?\d*|\.\d+)$/;
		// 	num = $(this).val();
		// 	if(regex.test(num)){
		// 	}else{
		// 		$(this).val(num.slice(0, -1));
		// 	}
		// })
		var hubx_id = "<?php echo $this->input->get("hubx_id");?>"
		if(hubx_id){
			var productList;
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: '<?php echo base_url('seller/product/getHubxById') ?>',
				data: {"hubx_id":hubx_id},
				success: function(response){
					if(response.productList){
						productList = response.productList;
						hubx_html = "";
						$("#hubx_product").html(hubx_html);
						$(productList).each(function(index,value){
							hubx_html += '<li class="list-group-item"><span>'+value.product_name+'</span><span class="float-right "><a href="<?php echo base_url("seller/product/create?pi=")?>'+value.product_id+'" target="_blank" class="btn btn-primary mr-2" title="View Product"><i class="fa fa-eye" aria-hidden="true"></i></a><a href="<?php echo base_url("seller/product/inventory_add?pi=")?>'+value.product_id+'&pn='+encodeURI(value.product_name)+'&mpn=8P256SG-B&hubx_id='+hubx_id+'" class="btn btn-primary" title="Create Inventory"><i class="fa fa-cart-plus" aria-hidden="true"></i></a></span></li>';
							/*hubx_html += '<li class="list-group-item"><span>'+value.product_name+'</span><span class="float-right "><a href="<?php echo base_url("seller/product/create?pi=")?>'+value.product_id+'" target="_blank" class="btn btn-primary mr-2" title="View Product"><i class="fa fa-eye" aria-hidden="true"></i></a><a href="<?php echo base_url("seller/product/inventory_add?pi=")?>'+value.product_id+'&pn='+value.product_name+'&hubx_id='+hubx_id+'" class="btn btn-primary" title="Create Inventory"><i class="fa fa-cart-plus" aria-hidden="true"></i></a></span></li>';*/
						});
						$("#hubx_product").html(hubx_html);
						$("#hubx_list").modal("show");
					}
					$("#keyword").val(response.data.description);
					$("#sku_code").val(response.data.mpn);
					$("#brands_id").val(response.data.brand_id).trigger('change.select2');
					if(response.data.variant !="" && response.data.variant !=null && typeof(response.data.variant) !=="undefined"){
						catVariant = response.data.variant;
						$(catVariant).each(function(index, element) {
							$("#variant_cat option[value='" + element.v_cat_id + "']").prop("selected", true).trigger('change');
						});
					}else{
						$("#variant_cat option").prop("selected",false).trigger("change");
					}
					CKEDITOR.instances['product_description'].setData(response.data.description);
					CKEDITOR.on("instanceReady", function(event){
						CKEDITOR.instances['product_description'].setData(response.data.description);
					});
					console.log(response);
				}
			});
		}
		$(".num").on("focusout", function(){
			var regex = /^\s*(?=.*[1-9])\d*(?:\.\d{1,2})?\s*$/;
			num = $(this).val();
			if(num != ""){
				if(regex.test(num)){
				}else{
					if(num > 0){
						val = num.split('.');
						$(this).val(val[0]+'.'+val[1]);
					}else{
						$(this).val(1);
					}
				}
			}
		})

		$("#editTitle").parent().addClass("d-none");
		$("#editTitle").parent().parent().removeClass("input-group");
		$("#keyword").removeAttr("readonly");
		<?php 
		//if($this->session->userdata['user_type'] == "1"){ 
			if(isset($_GET['pi']) && $_GET['pi'] !=""){?>
				product_id = "<?php echo $_GET['pi']?>";
				fileInputInit("","");
				//$("#keyword").attr("readonly","readonly");
				createData(product_id);
		<?php }else{ ?>
				fileInputInit("","");
		<?php }?>
		/*$('#keyword').on('focusout',function(){
			var term = $('#keyword').val();
			$.ajax({
				url: "<?php //echo base_url('seller/product/get_product');?>",
				dataType: "json",
				type: 'POST',
				data:{'text':term},
				success: function( data ) {
					if(data != ""){
						createData(data[0].id);
						//location.href = "<?php //echo base_url("seller/product/inventory_add?pn=")?>"+data[0].value+"&pi="+data[0].id;

					}
				}
			});
		});*/

		$("#subcategory_id").on('change',function(){
			var value = $('#subcategory_id option:selected').attr('data-private'); 
			$('#is_private').val(value);
		});

		var featureTriggerValue = "";
		<?php if($this->input->post('feature')){ ?>
			featureTriggerValue = <?php echo json_encode($this->input->post('feature'));
		}?>

		var featureTriggerCount = (featureTriggerValue != "") ? featureTriggerValue.length - 1 : 2;
		for(var i =0; i< featureTriggerCount; i++){
			$("#addMoreFeatures").trigger("click");
			$(".feature").eq(i + 1).val(featureTriggerValue[i+1]);
		}
		$('#condition_fieldset').hide();
		$('#subcategory_id,#brands_id,#variant_cat').select2({width: 'resolve', placeholder: function(){
		$(this).data('placeholder');}}).on('change', function() {$(this).valid();});
		$('#category_id,#statesData').select2({width: 'resolve', placeholder: function(){$(this).data('placeholder');}});
		$('#product_keyword').tokenfield({ 
			typehead: {
				name: 'tags',
				local: [],
			}
		});
		// $('.input-b8').rules("add","maxlength");
		$("#product_keyword-tokenfield").focusout(function(){
			$('#product_keyword').tokenfield('createToken', $(this).val());
			$(this).val("");
		});
		$('#product_keyword-tokenfield,#upc_code').keyup(function() {
			var raw_text =  $(this).val();
			var return_text = raw_text.replace(/[^a-zA-Z0-9_/-]/g,'');
			$(this).val(return_text);
		});
		$('#product_keyword-tokenfield,#sku_code').keyup(function() {
			var raw_text =  $(this).val();
			var return_text = raw_text.replace(/[^a-zA-Z0-9_/-]/g,'');
			$(this).val(return_text);
		});
	});
	/*$("#input-b8").on('fileselect', function(event, numFiles, label) {
		$(".fileinput-upload").remove();
		$(".kv-file-upload").remove();
		$(".kv-file-upload").remove();
		<?php 
			/*if(!isset($_GET['pn']) && !isset($_GET['pi'])){?>
				if($(this).parents().eq(3).find('.product-cover-image').length == 0){
					$(".kv-file-remove").removeClass("d-none");
					$(this).parents().eq(3).find('.file-preview-image').eq(0).trigger('click');
					$(this).parents().eq(3).find(".kv-file-remove").addClass("d-none");
				}
		<?php }*/?>
	});*/
	$(document).on('click','.file-preview-image',function(index,value){
		var title = 0;//$(this).parents().eq(1).attr('data-fileindex');//$(this).attr('title');
		$(this).parent().css('border','0px'); 
		$(this).parent().css('border','1px solid #000000');
		$(this).parent().css('position','relative');
		//$('.kv-file-content').find('span').remove();
		var main_parent = $(this).parents().eq(5);
		main_parent.find('.product-cover-image').remove();
		$(this).parent().append('<span class="product-cover-image">Cover Image</span>');
		var thumb_preview = main_parent.find('.kv-preview-thumb').length;
		if(thumb_preview > 0){
			main_parent.find('.kv-preview-thumb').each(function(index,value){
				if($(this).find('.product-cover-image').length > 0){
					title = index;
				}
			})	
		}
		$("#productForm").append('<input type="hidden" value="'+title+'" name="coverImage" id="coverImage" />');
	});
	/*$(document).on('click','.fileinput-remove-button',function(index,value){
		$('#input-b8').fileinput('clear');
	});*/

jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");

$.validator.addMethod( "specialChars", function( value, element ) {
// return this.optional( element ) || /^[_A-z]+((-|\s)*(!|#|.)*[_A-z0-9]*)*$/g.test( value );
return this.optional( element ) || /^[_A-z]+((-|\s)*[_A-z0-9#%!.\'*&()]*)*$/g.test( value )
}, "please enter a valid name" );
// 	jQuery.validator.addMethod("FirstLetterNochars", function(value, element) 
// {
// 	return this.optional(element) || /^[^0-9$-/:-?{-~!_`"^\[\]]$/.test(value);
// }, "First letter cant be a space special character or a number");
	$('#productForm').validate({
		ignore: [],
		rules: {
			<?php if(!isset($_GET['pn']) && !isset($_GET['pi'])){?>
			product_name:{
				required: true,
				//specialChars:true,
				normalizer: function(value) {
					// Note: the value of `this` inside the `normalizer` is the corresponding
					// DOMElement. In this example, `this` reference the `username` element.
					// Trim the value of the input
					return $.trim(value);
				},
				minlength: 2
			},
			<?php }?>
		 	product_description: {
        		ckeditor_required:true,
			},
			// product_description:{
			// 	required: true,
			// 	minlength: 300
			// },
			// productLength_type:{
			// 	required: true
			// },
			// productLength:{
			// 	required: true
			// },
			// productWidth:{
			// 	required: true
			// },
			// productHeight:{
			// 	required: true
			// },
			// productWeight_type:{
			// 	required: true
			// },
			// productWeight:{
			// 	required: true
			// },
			shipNote:{
				maxlength:500,
				FirstLetter:true
			},
			shipLength:{
				required: true
			},
			shipWidth:{
				required: true
			},
			shipHeight:{
				required: true
			},
			shipWeight_type:{
				required: true
			},
			shipWeight:{
				required: true
			},
			"subcategory_id[]" :{
				required: true
			},
			"feature[]" : {
				// required:true,
				three_feature_required:true
			},
			'period1': {
				required: {
					depends: function(element){
						if ($('.is_return:checked').val() == true){
							return true;
						} else {
							return false;
						}
					}		
				}		
			},
			'period2': {
				required: {
					depends: function(element){
						if ($('.is_return:checked').val() == true){
							return true;
						} else {
							return false;
						}
					}		
				}		
			},
			'period3': {
				required: {
					depends: function(element){
						if ($('.is_return:checked').val() == true){
							return true;
						} else {
							return false;
						}
					}		
				}		
			},
			'period4': {
				required: {
					depends: function(element){
						if ($('.is_return:checked').val() == true){
							return true;
						} else {
							return false;
						}
					}		
				}		
			},
			'period5': {
				required: {
					depends: function(element){
						if ($('.is_return:checked').val() == true){
							return true;
						} else {
							return false;
						}
					}		
				}		
			},
			'brands_id[]':{
				required: true
			},
			'product_video_link[]':{
				//required: true,
				//YoutubeURL: true,
				iframeValidator: true
			},
		},	
		messages: {
			product_name :{required: "Please provide product name.",
			minlength: "Must be at least two characters long"},
			product_description :{ckeditor_required: "Please provide product description."},
			"subcategory_id[]" :{required: "Please select category."},
			period1:{required: "Please enter the return period."},
			period2:{required: "Please enter the return period."},
			period3:{required: "Please enter the return period."},
			period4:{required: "Please enter the return period."},
			period5:{required: "Please enter the return period."},
			'brands_id[]':{required: "<?php echo $this->lang->line('brands_name_error') ?>"},

		},errorPlacement: function(error, element) {
			if(element[0].classList[1] == "feature"){
			} 
			else{
				error.appendTo(element.parent());
			}
		},
		submitHandler: function(form,event) {
			 $("#create").attr("disabled","disabled");
			 form.submit();
		 }
	});
	
jQuery.validator.addMethod("ckeditor_required", function(value, element) {
	var editor_val = $.trim(CKEDITOR.instances['product_description'].document.getBody().getChild(0).getText());
	if (editor_val == '') {
		return false ;
	}
	return true ;	
}, "This field is required" );
jQuery.validator.addMethod("three_feature_required", function(value, element) {
	var flag = true;
	$("#featuresDiv").find(".featureError").remove();
	var featureLength = $('.feature').length;
	if(featureLength >= 3){
		$('.feature').each(function(index,value){
			v = $(this).val().trim();
			if(v == ""){
				$(this).after("<label class='featureError error'>This field is required</label>");
				flag = false;
			}
		});
	}else{
		$('#featuresDiv').append('<label class="error featureError">At least 3 features required</label>');
		flag = false;
	}
		return flag;
 }, "This field is required");
	 
	//------- Auto Complete--------//
	$("#keyword").autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "<?php echo base_url('seller/product/get_product');?>",
				dataType: "json",
				type: "POST",
				cache:false,
				async:true,
				data: {"text":request.term},
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
			product_id = ui.item.id;
			<?php if($this->session->userdata('user_type') != 1){ ?>
				location.href = "<?php echo base_url("seller/product/inventory_add?pn=")?>"+ui.item.value+"&pi="+product_id;
			<?php }else if($this->session->userdata('user_type') == 1){ ?>
				createData(product_id);
			<?php }?>
		}
	});
$(document).on('change','#mySelect', function() {
				  var value = $(this).val();
				  $('#return_id').val(value);
				});
$("#addMoreFeatures").on('click',function(){
	$("#featuresDiv").append('<div class="row features"><div class="col-sm-6 mt-2"><input type="text" class="form-control feature" name="feature[]"/></div><div class="col-sm-2"><button type="button" class="close feature_remove close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>');
});
$("#featuresDiv").on('click','.feature_remove',function(){
	$(this).parent().parent().remove();
})
$("#addMoreVideoLink").on('click',function(){
	var c = $(this).attr("data-c");
		c = parseInt(c);
		c = c+1;
		$(this).attr("data-c",c);
	$("#product_video_link").after('<div class="row video_link"><div class="col-sm-6 mt-2"><textarea class="form-control" name="product_video_link[]" id="video_link'+c+'"></textarea><input type="hidden" name="media_id[]" class="mediaIdClass" id="mediaId'+c+'" value=""/></div><div class="col-sm-2"><button type="button" class="close video_link_remove close_btn" data-mediaid="'+c+'" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>');
});
// $("#productVideoLinkDiv").on('click','.video_link_remove',function(){
// 	$(this).parent().parent().remove();
// })
$(document).on('click','.video_link_remove', function() {
	// $(this).parent().find('.mediaIdClass');
	var id = $(this).attr('data-mediaid');
	var media_id = $("#mediaId"+id).val();
	var selector = $(this);
	if(media_id == ""){
		$(this).parent().parent().remove();
	}else{
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: '<?php echo base_url('seller/product/deleteVideoLink') ?>',
			data: {id:media_id},
			success: function(response){
				selector.parent().parent().remove();
			}
		});
	}
})
function createData(product_id){
	if(product_id !=""){
		$("#editTitle").parent().parent().addClass("input-group");
		$("#editTitle").parent().removeClass("d-none");
		$("#keyword").attr("readonly","readonly");
		$("#productForm").attr("action","<?php echo base_url()."seller/product/updateProduct"?>");
		$("#product_id").val(product_id);
		var productPath = "<?php echo product_thumb_path()?>";
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/product/getProductData');?>",
			data:{'id':product_id },
			success: function(response){
				if(userType == 1 || response.data.product[0].created_id == userid ){
					$("#keyword").val(response.data.product[0].product_name);
					if(response.data.keywords){
						$('#product_keyword').tokenfield('setTokens', response.data.keywords);
					}
					$("#slug").val(response.data.product[0].slug);
					$("#upc_code").val(response.data.product[0].upc_code);
					$("#sku_code").val(response.data.product[0].sku_code);
					$("#productLength_type").val(response.data.product[0].prd_dimension_type);
					$("#productLength").val(response.data.product[0].prd_length);
					$("#productWidth").val(response.data.product[0].prd_width);
					$("#productHeight").val(response.data.product[0].prd_height);
					$("#productWeight_type").val(response.data.product[0].prd_weight_type);
					$("#productWeight").val(response.data.product[0].prd_weight);
					$("#shipLength_type").val(response.data.product[0].dimension_type);
					$("#shipLength").val(response.data.product[0].length);
					$("#shipWidth").val(response.data.product[0].width);
					$("#shipHeight").val(response.data.product[0].height);
					$("#shipWeight_type").val(response.data.product[0].weight_type);
					$("#shipWeight").val(response.data.product[0].weight);
					$("#shipNote").val(response.data.product[0].shipping_note);
					$("#short_description").val(response.data.product[0].short_description);
					if(response.data.product[0].variant_cat_group !="" && response.data.product[0].variant_cat_group !=null && typeof(response.data.product[0].variant_cat_group) !=="undefined"){
						catVariant = response.data.product[0].variant_cat_group.split(',');
						$(catVariant).each(function(index, element) {
							$("#variant_cat option[value='" + element + "']").prop("selected", true).trigger('change');
						});
					}else{
						$("#variant_cat option").prop("selected",false).trigger("change");
					}
					if(response.data.product[0].category_id){
						var selectedValues = response.data.product[0].category_id.split(',');
						$("#subcategory_id").val(selectedValues).trigger('change.select2');
					}
					if(response.data.product[0].brand_id){
						$("#brands_id").val(response.data.product[0].brand_id).trigger('change.select2');
					}
					featureLength = response.data.features.length;
					featureInputLength = $('.features').length+1;
					if(featureLength > featureInputLength){
						for(var i = featureInputLength; i<featureLength; i++){
							$("#addMoreFeatures").trigger("click");
						}
					}
					if(featureLength > 0){
						$(response.data.features).each(function(index,value){
							$('.feature').eq(index).val(value.feature);
						});
					}
					if(response.data.imagePreviewData !=""){
						$('#input-b8').fileinput('destroy');
						fileInputInit(response.data.imagePreviewData.initialPreview, response.data.imagePreviewData.initialPreviewConfig);
					}
					imageLength = response.data.image.length;
					if(imageLength > 0){
						//$("#addMoreVideoLink").attr("data-c",imageLength);
						var is_cover = "";
						var checkVideolink = 0;
						//$('#wrapper').html("");
						$(response.data.image).each(function(index,value){
							if(value.is_cover == 1){
								is_cover = '<div class="cover-image text-center">Cover</div>';	
							}else{
								is_cover = "";
							}
							if(value.is_image == "0"){
								if(checkVideolink == 0){
									$("#product_video_link").find("textarea").eq(0).val(value.iv_link);	
									$("#product_video_link").find("textarea").eq(0).next(".mediaIdClass").val(value.media_id);
									checkVideolink=1;
								}else{
									$("#addMoreVideoLink").trigger("click");
									$("#video_link"+index).val(value.iv_link);
									$("#mediaId"+index).val(value.media_id)
									//$("#product_video_link").after('<div class="row video_link"><div class="col-sm-6 mt-2"><input type="text" class="form-control" name="product_video_link[]" value="'+value.iv_link+'" /><input type="hidden" name="media_id[]" class="mediaIdClass" id="mediaId" value="'+value.media_id+'"/></div><div class="col-sm-2"><button type="button" data-mediaId = "'+value.media_id+'" class="close video_link_remove close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>');
								}
							}else{
								//$('#wrapper').append('<div class="images" id="img'+index+'"><div class="img" style="background-image:url('+productPath+value.is_primary_image+')" id="cover_id'+value.media_id+'"><span class="make_cover" title="Make Cover Image" data-id="'+value.media_id+'"><i class="fas fa-camera-retro"></i></span><span class="remove_image" data-link="'+value.is_primary_image+'" data-div_id="img'+index+'" data-id="'+value.media_id+'"><i class="fa fa-window-close" title="Delete Image" aria-hidden="true"></i></span>'+is_cover+'</div></div>');
							}
							$("#uploaded_Image").removeClass("d-none");
						});
					/*	if(imageLength < 12){
							maxImage = (12-imageLength);
							//$('#input-b8').fileinput('refresh', {maxFileCount: maxImage,uploadExtraData:{id:response.data.product[0].product_id}});
						}else{
							//$('#input-b8').fileinput('disable');
							maxImage = 0;
						}*/
					}
					CKEDITOR.instances['product_description'].setData(response.data.product[0].product_description);
					CKEDITOR.on("instanceReady", function(event){
						CKEDITOR.instances['product_description'].setData(response.data.product[0].product_description);
					});
					$("#create").text("update");
				}else{
					location.href = "<?php echo base_url("seller/product/inventory_add?pn=")?>"+response.data.product[0].product_name+"&pi="+response.data.product[0].product_id;
				}
			}
		});
	}
}
$(document).on('click','.remove_image',function(){ 
	var img_length = $(".images").length;
	if(img_length > 1){
		$("#forWhat").val("remove_image");
		$("#imgDel_id").val($(this).attr("data-id"));
		$("#div_id").val($(this).attr("data-div_id"));
		$("#link").val($(this).attr("data-link"));
		$(".confirm_del").removeClass("d-none");
		$("#mt").html("Remove Image?");
		$(".box-content").html("Are you sure?");
		$('#image_del').modal('show');
	}else{
		$("#mt").html("Warning!");
		$(".box-content").html("Can't remove product contains at least one image.");
		$(".confirm_del").addClass("d-none");
		$('#image_del').modal('show');
	}
});
$(document).on('click','.make_cover',function(){ 
	var img_id = $(this).attr("data-id");
	checkCoverImage = $("#cover_id"+img_id).find('.cover-image').length;//$(this).parent().find('.make_cover').length;
	if(checkCoverImage){
		$("#mt").html("Warning!");
		$(".box-content").html("This image already a cover image.");
		$(".confirm_del").addClass("d-none");
		$('#image_del').modal('show');
		return false;
	}else{
		$("#forWhat").val("cover");
		$("#imgDel_id").val(img_id);
		$(".confirm_del").removeClass("d-none");
		$("#mt").html("Change Cover Image?");
		$(".box-content").html("Are you sure?");
		$('#image_del').modal('show');
	}
});
$(document).on('click','.confirm_del',function(){
	var id = $("#imgDel_id").val();
	var divId = $("#div_id").val();
	var img_link = $("#link").val();
	var forWhat = $("#forWhat").val();
	var hitUrl = "";
	var data = "";
	var isCoverImage = $("#cover_id"+id).find('.cover-image').length;
	if(isCoverImage == 0){
		if(forWhat == "remove_image"){
			hitUrl = "<?php echo base_url('seller/product/deleteData');?>";
			data = {'i':id,'t':"product_media",'w':'media_id','ed':img_link};
		}else{
			hitUrl = "<?php echo base_url('seller/product/changeCoverImage');?>"
			data = {'pi':$('#product_id').val(),'i':id,'c':1}
		}
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: hitUrl,
			data: data,
			success: function(response){
				if(response && forWhat =="remove_image"){
					$("#"+divId).remove();
					$('#image_del').modal('hide');
					imageLength = $(".images").length;
					maxImage = (12-imageLength);
					$('#input-b8').fileinput('refresh', {maxFileCount: maxImage});
					//$('#input-b8').fileinput('destroy').fileinput({maxFileCount: maxImage})
					$('#input-b8').fileinput('enable');
				}
				if(response.status == 1){
					$(".cover-image").remove();
					$("#cover_id"+id).append('<div class="cover-image text-center">Cover</div>');
					$('#image_del').modal('hide');
				}
			}
		});
	}else{
		$("#mt").html("Warning!");
		$(".box-content").html("Can't delete a cover image.");
		$(".confirm_del").addClass("d-none");
		$('#image_del').modal('show');
	}
	return false;
});
var i=0;
function fileInputInit(preview,config){
	if(preview !="" && config !=""){
		preview = preview.split(',');
		config = JSON.parse(config);
	}else{
		preview = [];
		config = [];
	}
	$("#input-b8").fileinput({
		uploadUrl: "<?php echo base_url("seller/product/uploadAjax")?>",
		enableResumableUpload: true,
		// required:true,
		resumableUploadOptions: {
		// uncomment below if you wish to test the file for previous partial uploaded chunks
		// to the server and resume uploads from that point afterwards
		// testUrl: "http://localhost/test-upload.php"
		},
		uploadExtraData: {
			'uploadToken': 'zabee-product', // for access control / security
			'type': 'product',
			'user_id': '<?php echo $_SESSION['userid']?>',
			'tn':"pm",
			'id':product_id,
			'sp_id':"",
			'column':"product_id",
			'condition_id':1,
			"dummy_id": dummy_id(),
		},
		// minFileCount:1,
		//maxFileCount: 12,
		validateInitialCount:true,
		allowedFileTypes: ['image'],    // allow only images
		browseLabel: "Pick Image",
		showCancel: true,
		initialPreviewAsData: true,
		overwriteInitial: false,
		minImageWidth: 400,
		minImageHeight: 400,
		theme: 'fas',
		rtl: true,
		//showUpload: false,
		initialPreview: preview,
        initialPreviewConfig: config,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		deleteUrl:'<?php echo base_url('seller/product/deleteData'); ?>',
	});
}
$("#input-b8").on('fileuploaded', function(event, previewId, index, fileId) {
	// submit_form = 1;
	//console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
}).on('fileuploaderror', function(event, data, msg) {
	//console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
}).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
	if(submit_form == 1){
		$('#productForm').submit();
	}
}).on('filebeforedelete', function(event, key, data) {
	var aborted = !window.confirm('Are you sure you want to delete this file?');
	return aborted;
}).on('filedeleted', function() {
	setTimeout(function() {
		window.alert('File deletion was successful!');
	}, 900);
}).on('filesorted', function(event, params) {
	var data = [];
	$(".file-sortable").each(function(index,value){
		data.push({"index":index,"media_id":$(value).find(".kv-file-remove").data("key")})
	});
	if(params.oldIndex != params.newIndex){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url("seller/product/imagePosition")?>",
			data:{"data":data},
			success: function(response){
				console.log(response);
			}
		});
	}
});
function dummy_id() {
  var d = new Date();
  var n = d.valueOf();
  $("#dummy_id").val(n);
  return n;
}
$("#keyword").keyup(function(){
	var name = $(this).val();
	if(name.includes(" ")) {
		var new_name = name.replace(/ /g, '-');
		$("#slug").val(new_name);
	}else{
		$("#slug").val(name);
	}
});
$('.num').keypress(function(event){
	if(event.which != 8 && isNaN(String.fromCharCode(event.which)) && event.which != 47 && event.which != 46){
		event.preventDefault();
	}
});
$("fieldset").on("click","#editTitle",function(e){
	$("#pt").val($("#keyword").val());
	$("#changeTitle").modal("show");
});
$(document).on('click','#updateTitle', function() {
	// $(this).parent().find('.mediaIdClass');
	var product_id = $("#product_id").val();
	var title = $("#pt").val();
	if(product_id !=""){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: '<?php echo base_url('seller/product/updateProductTitle') ?>',
			data: {"product_id":product_id,"title":title},
			success: function(response){
				if(response.status == false){
					$("#notify").removeClass("d-none");
					$("#notify .alert").addClass("alert-warning");
					$("#notify .alert").removeClass("alert-success");
					$("#notify #head").text("Error");
					$("#notify #error").text(response.message);
				}else{
					$("#notify").removeClass("d-none");
					$("#notify .alert").removeClass("alert-warning");
					$("#notify .alert").addClass("alert-success");
					$("#notify #head").text("Success");
					$("#notify #error").text(response.message);
					$("#keyword").val(title);
					$("#slug").val(response.slug);
				}
				setTimeout(function(){ 
					$("#notify").addClass("d-none");
					$("#changeTitle").modal("hide"); 
				}, 2500);
			}
		});
	}
})
</script>	
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Return Policies</h4>
      </div>
     
      <div class="modal-footer">
        <button type="button" class="btn btn-default Save" data-dismiss="modal">Save</button>  
      </div>
</div>
<div class="modal fade" id="image_del" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="mt">Remove Image?</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				Are you sure?
			</div>				
		  </div>
		  <div class="modal-footer">
          	<input type="hidden" value="" id="imgDel_id" />
            <input type="hidden" value="" id="div_id" />
            <input type="hidden" value="" id="link" />
            <input type="hidden" value="" id="forWhat" />
            <button class="btn btn-primary confirm_del" >Yes</button>
			<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
		  </div>
		</div>
	</div>
</div>
<div class="modal fade" id="changeTitle" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="mt">Product Title</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="input-group mb-3">
				<input type="text" class="form-control" placeholder="Product Title" id="pt" aria-label="Product Title" aria-describedby="basic-addon2">
				<div class="input-group-append">
					<button class="btn btn-outline-secondary" id="updateTitle" type="button">Update</button>
				</div>
			</div>
			<div id="notify" class="col d-none">
				<div class="alert">
					<strong id="head"></strong> <span id="error"></span>
				</div>
			</div>
		  </div>
		  <div class="modal-footer">
          	<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
		  </div>
		</div>
	</div>
</div>

