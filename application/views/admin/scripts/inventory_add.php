<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script> 
<script>
	var vg = 0;
	var catVariant = Array();
	var removeAttributeIndex = [];
	var variantVal = "";
	var variantCatData = "";
	var conditionData = "";
	var discountData = "";
	var allConditions = <?php echo json_encode($conditionData)?>;
	var dummy_id = dummy_id();
	var varCat = Array();
	$(document).ready(function(){
		$("#condition_id").attr("disabled","disabled");
		<?php if((isset($_GET['pn']) && $_GET['pn'] !="") && (isset($_GET['pi']) && $_GET['pi'] !="")){?>
			var pid = "<?php echo $_GET['pi']?>";
			$("#keyword").val("<?php echo addslashes($_GET['pn'])?>");
			$("#keyword").attr("readonly","readonly");
			createData(pid);
		<?php }?>
		$('#condition_fieldset').hide();
		$('#condition_id,#variant_cat').select2({width: 'resolve', placeholder: function(){
        $(this).data('placeholder');}}).on('change', function() {$(this).valid();});
	});
	function addFieldSet(c){
		var approve = $("#approve").val();
		var lineClass = "";
		var recreateBtn = "";
		if(approve == 3){
			lineClass= "table-dark";
			recreateBtn = '<button type="button" class="close retweet" aria-label="Close" title="Re-Create this inventory"><i class="fas fa-retweet"></i></button>';
		}
		var heading="";
		var condition = "";
		var count = 0;
		var row = 0;
		var product_id = $("#product_id").val();
		var flag =true;
		if(product_id !="" && c==1){
			flag = false;
		}
		$(allConditions).each(function(index,value){
			if(c == value.condition_id){
				heading = value.condition_name;
				condition = heading.replace(" ","_").toLowerCase();
				return false;
			}
		});
		var strHtml ='<div class="col-xs-12">';
						<?php  if($_SESSION['is_zabee'] == "0"){ ?>
			var strHtml ='<div class="control-group">'+
							'<div class="row p-relative">'+ 
								'<div class="col-sm-4">'+
									'<label class="form-control-label col-form-label">Return Policy:</label>'+
								<?php if(!empty($returns)){?>
									'<select name="returnId'+c+'" id="mySelect'+c+'" class="form-control">'+
									<?php
										$hasDefault = false;
										foreach($returns as $return){
											if(!$hasDefault)
												$hasDefault = ($return->is_default == 1)?true:false;
									?>
										'<option <?php echo ($return->is_default == 1 || ($return->id == 0 && !$hasDefault))? 'selected="selected" ':''; ?>value="<?php echo $return->id; ?>"><?php echo $return->returnpolicy_name; ?></option>'+
									<?php }?>	
									'</select>'+
									<?php } else { ?>
										'<p style="color:red; margin-top: -10px;">No Default Return Policy Found. <a href="<?php echo base_url()."seller/returnpolicy"; ?>" class="btn btn-link btn-sm">Create Return Policy</a></p>'+
									<?php }?>	
								'</div>'+
								'<div class="col-sm-4">'+
								'<label class="form-control-label col-form-label">Shipping Methods:</label>'+
								<?php if(!empty($shippingData)){?>
									'<select name="shippingIds'+c+'[]" id="shippingIds'+c+'" class="form-control shippingIds" multiple data-placeholder="Select Shipping Methods">'+
									<?php
										foreach($shippingData as $sd){
										?>
										'<option value="<?php echo $sd->shipping_id; ?>"><?php echo ($sd->price !=0)?"$".$sd->price:"Free Shipping";echo " - ".$sd->title." -  Days: ".$sd->duration; ?></option>'+
									<?php }?>	
									'</select>'+
									<?php } else { ?>
										'<p style="color:red; margin-top: -10px;">No Shipping Policy Found. <a href="<?php echo base_url()."seller/shipping"; ?>" class="btn btn-link btn-sm">Create Shipping Policy</a></p>'+
									<?php }?>	
								'</div>'+
								'<div  class="col-sm-4">';
									//'<a class="addmorevariant" id="addmorevariant'+c+'" data-count="'+(vg-1)+'" data-condition="'+condition+'" data-condition_id="'+c+'" style="color:white; margin-right:5px">Add More Attribute</a>';
									if(vg > 0){
										strHtml += '<a class="btn btn-primary amr" id="addmorerow'+c+'" data-condition_id="'+c+'" data-condition="'+condition+'" data-count="0" style="color:white;position:absolute;bottom:10px;">Add More Inventory</a>';	
									}
					strHtml += '</div>'+
							'</div>'+
						'</div>';
						<?php }?>
		strHtml += '<div class="control-group">'+
			'<fieldset class="backColor table-responsive">'+
			/*'<div class="row pro_sku">'+
			'<div  class="col-sm-4">'+
				'<label class="form-control-label col-form-label" >Seller SKU (Optional)</label><input class="form-control product_seller_sku"  name="product_seller_sku'+c+'" id="seller_sku'+c+'" type="text" value="" placeholder="Enter Seller SKU">'+
			'</div>'+
			'<div  class="col-sm-8 pro_btn">'+
				'<a class="addmorevariant" id="addmorevariant'+c+'" data-count="'+(vg-1)+'" data-condition="'+condition+'" data-condition_id="'+c+'" style="color:white; margin-right:5px">Add More Attribute</a>';
				if(vg > 0){
					strHtml += '<a class="btn btn-primary amr" id="addmorerow'+c+'" data-condition_id="'+c+'" data-condition="'+condition+'" data-count="0" style="color:white;" >Add More Row</a>';	
				}
		strHtml += 	'</div></div><br />'+*/
			'<div class="clearfix" ></div>'+
			'<table class="pro_table table table'+c+' table-responsive ">'+
				'<thead id="variantHead'+c+'">'+
				'<tr><th></th><th>Quantity</th><th>Price</th><th>Seller sku</th>';
					if(c != 1){
						strHtml += '<th>Warranty</th>';
					}
					<?php if(!empty($discountData)){?>
						strHtml += '<th>Discount</th>';
					<?php }
						if(!empty($shippingData)){ ?>
						strHtml += '<th>Shipping</th>';
					<?php } ?>
				for(var vacount =0; vacount<vg; vacount++){
					if(typeof removeAttributeIndex[condition] === 'undefined'){
						removeAttributeIndex[condition]=[];
						removeAttributeIndex[condition].push(vacount);	
					}else{
						removeAttributeIndex[condition].push(vacount);	
					}
					strHtml+='<th>Attribute<button type="button" class="close removeAttribute" data-condition_id = "'+c+'" data-count="'+vacount+'" data-condition="'+condition+'" id="btn_variant_cat_'+c+'_'+vacount+'" data-cat_id="variant_cat_'+c+'_'+vacount+'" data-vari_class="variant'+vacount+c+'" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
					if(variantCatData){
						strHtml+='<select data-id="vcat_'+c+'_'+vacount+'" name="variant_cat'+c+'['+vacount+']" data-variantclass="'+vacount+'"  data-condition_id="'+c+'" class = "variant_cat variant_cat'+c+' form-control valid" id="variant_cat_'+c+'_'+vacount+'"><option value="">-Select-</option>';
						$(variantCatData).each(function(index,value){	
							strHtml+= '<option value = "'+value.v_cat_id+'">'+value.v_cat_title+'</option>';
						});
						strHtml+='</select>';
					}else{
						strHtml+="<span style = 'color:red;'>No Variant Category found.</span>Please <a href='<?php echo base_url('seller/variantcategories');?>'>Add Variant Category</a>";
					}
						strHtml+='</th>';
					}

						strHtml+='</tr></thead>'+
				'<tbody id="variantBody'+c+'"><tr class="totalrows'+c+' '+lineClass+'">'+
					'<td width="1%">'+recreateBtn+'</td><td width="7%"><input type="number" min="1" data-id="qty_'+c+'_'+row+'_'+count+'" name="quantity'+c+'['+count+']" value="1" placeholder="1" class="form-control variant_qty" /><input type="hidden" data-id="total_qty_'+c+'_'+row+'_'+count+'" name="total_qty_'+c+'['+count+']" class="form-control variant_total_qty"/><input type="hidden" data-id="previous_qty_'+c+'_'+row+'_'+count+'" name="previous_qty_'+c+'['+count+']" class="form-control variant_previous_qty"/></td>'+
					'<td  width="10%"><input type="number" min="1" maxlength="11" data-id="price_'+c+'_'+row+'_'+count+'" name="price'+c+'['+count+']" value="10" placeholder="10" class="form-control variant_price" /></td>'+
					'<td  width="13%"><input type="text" data-id="seller_sku_'+c+'_'+row+'_'+count+'" name="seller_sku'+c+'['+count+']" class="form-control variant_sku" /></td>';
					if(c != 1){
					<?php if(!empty($warrantyData)){?>
						strHtml+=	'<td><select name="warranty'+c+'['+count+']" id="warranty_'+c+'_'+row+'_'+count+'" data-warranty_id="warranty_'+c+'_'+row+'_'+count+'" class="form-control variant_warranty" data-placeholder="-Select Warranty-">'+
									'<option value="" disabled selected hidden>-Select Warranty-</option>'+
									<?php
										foreach($warrantyData as $wd){?>
										'<option value="<?php echo $wd->id; ?>"><?php echo $wd->warranty;?></option>'+
									<?php }?>	
									'</select></td>';
					<?php }?>}
					<?php if(!empty($discountData)){?>
						strHtml+=	'<td><select name="discount'+c+'['+count+']" data-discount_id="discount_'+c+'_'+row+'_'+count+'" class="form-control variant_discount">'+
								'<option value="">Select Discount</option>'+
									<?php
										foreach($discountData as $dd){
										?>
										'<option value="<?php echo $dd->id; ?>"><?php echo $dd->value."%"." -".addslashes($dd->title);echo" -  validity: ".$dd->valid_from;echo" till ".$dd->valid_to;?></option>'+
									<?php }?>	
									'</select></td>';
					<?php }?>
					<?php if(!empty($shippingData)){?>
						strHtml+='<td><select name="shipping'+c+'['+count+'][]" id="shipping'+c+'_'+row+'_'+count+'" data-shipping_id="shipping'+c+'_'+row+'_'+count+'"class="form-control shippingIds" multiple data-placeholder="Select Shipping Methods">'+
							<?php
								foreach($shippingData as $sd){
								?>
									'<option value="<?php echo $sd->shipping_id; ?>"><?php echo ($sd->price !=0)?"$".$sd->price:"Free Shipping";echo " - ".$sd->title." -  Days: ".$sd->duration; ?></option>'+
								<?php }?>	
						'</select></td>';
					<?php } ?>	
					for(var vcount =0; vcount<vg; vcount++){	
						strHtml+='<td><select data-id="variant_'+c+'_'+row+'_'+vcount+'" id="variant_'+c+'_'+row+'_'+vcount+'" name="variant'+c+'['+vcount+'][]"  class = "product_variant variant variant'+vcount+c+' form-control" ></select><span class="error"></span></td>';
					}

			strHtml+='<input type="hidden" value="" id="hubx_id_'+c+'_'+row+'_'+count+'" class="hubx_id" name="hubx_id'+c+'['+count+']"><input type="hidden" value="" id="inventory_id_'+c+'_'+row+'_'+count+'" class="inventory_id inventory_id'+c+'" name="inventory_id'+c+'['+count+']"><input type="hidden" value="" id="pv_id_'+c+'_'+row+'_'+count+'" class="pv_id pv_id'+c+'" name="pv_id'+c+'['+count+']"></tr></tbody></table>';
			if(flag){
				strHtml+=	'<div class="row form-group">'+
                    '<div class="col-sm-3 text-right" id="label_wrapper_'+c+'"></div>'+
                    '<div class="for-label-append col-sm-9">'+
                        '<div class="row" id="wrapper_'+c+'"></div>'+
                    '</div></div>'+
						'<div class="col-sm-12 '+condition+'_img form-group" >'+
					'<label class="form-control-label col-form-label">Product Condition Image</label>'+
					'<input class="form-control product_image preview-image-class" data-id="'+c+'" id="condition_image'+c+'" name="condition_image'+c+'[]" type="file" multiple="true" accept="image/*" >'+
				'</div>'+
				'<div class="clearfix" ></div>'+
				// '<div class="col-sm-12 form-group">'+
				// 	'<div class="row">'+
				// 		'<div class="col-sm-2" id="textarea-row-parent">'+
				// 			 '<label class="form-control-label col-form-label " >Video Link(Optional)</label>'+
				// 		'</div>'+
				// 		'<div  class="col-sm-6" id="textarea-row'+c+'">'+
				// 			'<div  class="col-sm-12">'+
				// 				'<textarea name="condition_video_link'+c+'[]" class="form-control" id="condition_video_link'+c+'" data-cond_id = "'+c+'"  placeholder="Enter Video Link "></textarea><br />'+
				// 			'<input type="hidden" name="media_id'+c+'[]" class="mediaId" id="mediaId'+c+'" value=""></div>'+
				// 		'</div>'+
				// 		'<div class="col-sm-2">'+
				// 			'<button type="button" class="btn whiteColor addMoreVideoLink" data-condition_id="'+c+'">Add More</button>'+
				// 		'</div>'+
				// 	'</div>'+	
					'<div class="row form-group">'+
            '<div class="col-sm-2 text-right">'+
                 '<label class="col-form-label">Video Link</label>'+
            '</div>'+
            '<div  class="col-sm-10" id="productVideoLinkDiv">'+
               '<div id ="product_video_link'+c+'" class="row ml-1">'+
										'<div class="col-sm-6 pl-1" id="link">'+
                        '<input type="text" class="form-control" name="condition_video_link'+c+'[]" id="condition_video_link'+c+'" data-cond_id = "'+c+'"  placeholder="Enter Video Link "/>'+
                        '<input type="hidden" name="media_id'+c+'[]" class="mediaId" id="mediaId'+c+'" value="">'+
                    '</div>'+
                    '<div class="col-sm-2">'+
                    	'<button type="button" class="btn whiteColor addMoreVideoLink" data-condition_id="'+c+'">Add More</button>'+
                    '</div>'+
                '</div>'+			
            '</div>'+
            '<div class="clearfix"></div>'+
        '</div>'+
					'<div class="clearfix"></div>'+
				'</div>'+
				'<div class="col-sm-12 form-group">'+
					'<div class="row">'+
						'<div class="col-sm-2 text-right">'+
							'<label class="form-control-label col-form-label " >Condition Description</label>'+
						'</div>'+
						'<div  class="col-sm-10">'+
							'<div class="controls">'+
								'<textarea class="form-control prod_des'+c+'" id="prod_des'+c+'" name="prod_des'+c+'" placeholder="Enter Product Description"></textarea>'+
							'</div>'+
							'<span id="prod_des'+c+'-error" ></span>'+					
						'</div>'+
					'</div>'+
				'</div>';
			}
			strHtml+='</fieldset>'+
		'<input type="hidden" id="sp_id'+c+'" name="sp_id'+c+'" ></div>'+
		'<div class="clearfix"></div>';
		
		if($('#'+condition).length ==0){
			$('#condition_fieldset .nav-tabs').append('<li class="nav-item active '+condition+'"><a data-toggle="tab" href="#'+condition+'" class="nav-link active">'+heading+'</a></li>');
			$('#condition_fieldset .tab-content').append('<div id="'+condition+'" class="tab-pane fade in active show" role="tabpanel">'+
				  '<p>'+strHtml+'</p>'+
				'</div>');
			$('input[data-id="qty_'+c+'_'+row+'_'+count+'"], input[data-id="qty_'+c+'_'+row+'_'+count+'"], input[data-id="price_'+c+'_'+row+'_'+count+'"]').rules("add", "required");
			for(var vacount =0; vacount<vg; vacount++){
				$("#variant_cat_"+c+"_"+vacount+" option[value='" + variantCatData[vacount].v_cat_id + "']").prop("selected", true).trigger('change');
			}
		}
		$('#shipping'+c+'_'+row+'_'+count).select2({width: 'resolve', placeholder: function(){
        $(this).data('placeholder');}}).on('change', function() {$(this).valid();});
		// CKEDITOR.replace('product_description'+c);
		// $('.product_description'+c).rules("add","required");
	}


	$(document).on('select2:select','#condition_id', function (evt) {
		var id = evt.params.data.id;
		var imageData = evt.params.data.imageData;
		var sp_id = evt.params.data.sp_id;
		var product_id = $('#product_id').val();
		if($("#condition_id option:selected").length != 0){
			$('#condition_fieldset').show();
		}

		$('#condition_fieldset .nav-tabs').find('li').removeClass('active');
		$('#condition_fieldset .nav-tabs').find('li a').removeClass('active');
		$('#condition_fieldset .tab-content').find('div').removeClass('in');
		$('#condition_fieldset .tab-content').find('div').removeClass('active');
		addFieldSet(id);
		/*$("#variant_cat option:selected").each(function(index,value){
			var v_cat_selected_id = $(value).val();
			console.log($(value).val());
			$('.variant_cat'+id).eq(index).val(v_cat_selected_id).trigger('change');
			$('.variant_cat'+id).eq(index).val(v_cat_selected_id).addClass('variantVal'+v_cat_selected_id);	
		});*/
		
		if(imageData !="" && typeof imageData !== 'undefined'){
			fileInputInit("#condition_image"+id,id,imageData.initialPreview,imageData.initialPreviewConfig,sp_id);
		}else{
			fileInputInit("#condition_image"+id,id,"","","");
		}
		/*$("#condition_image"+id).fileinput({
			// required:true
			allowedFileTypes: ["image"],
			browseLabel: "Pick Image",
			//uploadUrl: "<?php //echo base_url()?>",
			theme: "fas",
			minImageWidth: 400,
			minImageHeight: 400,
			maxFileCount: 12,
			validateInitialCount: true,
			overwriteInitial: false,
			showUpload: false,
			rtl: true,
			allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		});*/

		/*$("#condition_image"+id).on('fileselect', function(event, numFiles, label) {
			$(".fileinput-upload").remove();
			$(".kv-file-upload").remove();
			if($('#wrapper_'+id).contents().length == 0){
					if($(this).parents().eq(3).find('.product-cover-image').length == 0){
					$(".kv-file-remove").removeClass("d-none");
					$(this).parents().eq(3).find('.file-preview-image').eq(0).trigger('click');
					$(this).parents().eq(3).find(".kv-file-remove").addClass("d-none");
					}
				}
			});

		$(document).on('click','.fileinput-remove-button',function(index,value){
			$('#condition_image'+id).fileinput('clear');
		});*/
		$("#shippingIds"+id).rules("add","required");
		$("#prod_des"+id).rules( "add", {
			required:true,
  			// minlength: 300
		});
		//$("#condition_image"+id).rules("add","checkImage");
		$("#shippingIds"+id).select2({width: 'resolve', placeholder: function(){
        $(this).data('placeholder');}}).on('change', function() {$(this).valid();});
		$("#warehouseId"+id).select2({width: 'resolve', placeholder: function(){
        $(this).data('placeholder');}});
		var row =0;
		var selectId = "";
		for(var vcount =0; vcount<vg; vcount++){	
			selectId = 'variant_'+id+'_'+row+'_'+vcount;
			$('#'+selectId).rules("add", "required"); //Select Variant Required.
		}
		if(product_id !=""){
			$('.addmorevariant').addClass('d-none');
		}else{
			$('.addmorevariant').removeClass('d-none');
		}
	});
	/*$(document).on('click','.file-preview-image',function(index,value){
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
		var condition_id = main_parent.find('.preview-image-class').attr('data-id');
		$("#productForm").append('<input type="hidden" value="'+title+'" name="coverImage'+condition_id+'" id="coverImage'+condition_id+'" />');
	});*/
	
	$(document).on('select2:unselecting','#condition_id', function (evt) {
		//console.log(evt);
		//return false;
		var id = evt.params.args.data.id;
		var condition = "";
		$(allConditions).each(function(index,value){
			if(id == value.condition_id){
				condition = value.condition_name.replace(" ","_").toLowerCase();
				return false;
			}
		});
		$("#conditionName").val(condition);
		$("#conditionId").val(id);
		$("#condition_del").modal("show");
		return false;
	});
	$(document).on("click","#conditionDelConfirm",function(){
		condition = $("#conditionName").val();
		id = $("#conditionId").val();
		delCondition(id,condition);
	});
	function delCondition(id,condition){
		var data = {"id": id};
		sp_id = $("#sp_id"+id).val();
		if(sp_id){
			$.ajax({
				url: "<?php echo base_url('seller/product/deleteCondition')?>",
				type:'POST',
				data:{"condition_id":id,"sp_id":sp_id},
				dataType:'json',
				async:false,
				success: function(result){
					if(result.status != 1){
						alert("error in condition removing");
					}
				}
			});
		}
		$("#condition_id option[value='"+id+"']").prop("selected", false).trigger('change');
		$("#condition_id").trigger({
			type: 'select2:unselect',
			params: {data: data}
		});
		removeAttributeIndex[condition] = [];
		$('#condition_fieldset .nav-tabs').find('.'+condition).remove('li');
		$('#condition_fieldset .tab-content').find('#'+condition).remove('div');
		if($("#condition_id option:selected").length == 0){
			$('#condition_fieldset').hide();
		}
		if($('#condition_fieldset .nav-tabs li').hasClass('active') == 0){
			$('#condition_fieldset .nav-tabs').find('li').first().addClass('active');
			$('#condition_fieldset .nav-tabs').find('li a').first().addClass('active');
			$('#condition_fieldset .tab-content').find('div').first().addClass('in');
			$('#condition_fieldset .tab-content').find('div').first().addClass('active');
		}
		$("#coverImage"+id).remove();
		$("#condition_del").modal("hide");
	}
	$(document).on('change', '.variant_cat',function(e){
		var variant_cat_id = $(this).val();
		var variantClass = $(this).attr('data-variantclass');
		var condition_id = $(this).attr('data-condition_id');
		var cat_id = $(this).attr("id");
		var variantCatName = $("#"+cat_id+" option:selected").text();
		if(!varCat[variantCatName]){
			$.ajax({
				url: "<?php echo base_url('seller/product/get_variant')?>",
				type:'POST',
				data:{id:variant_cat_id},
				dataType:'json',
				async:false,
				success: function(result){
					if(result.status == 1){
						varCat[variantCatName] = result.data;
						$(".variant"+variantClass+condition_id).append('<option value="" disabled selected hidden>Please Select</option>');
						$(result.data).each(function(index, element) {
							$(".variant"+variantClass+condition_id).append('<option value="'+element.v_id+'">'+element.v_title+'</option>')
						});
					}
				}
			});
		}else{
			$(varCat[variantCatName]).each(function(index, element) {
				$(".variant"+variantClass+condition_id).append('<option value="'+element.v_id+'">'+element.v_title+'</option>')
			});
		}
		/*if(!varCat[d]){
			$.ajax({
				url: "<?php echo base_url('seller/product/get_variant')?>",
				type:'POST',
				data:{id:d},
				dataType:'json',
				async:false,
				success: function(result){
					if(result.status == 1){
						$(".variant"+variantClass+condition_id).append('<option value="" disabled selected hidden>Please Select</option>');
						$(result.data).each(function(index, element) {
							$(".variant"+variantClass+condition_id).append('<option value="'+element.v_id+'">'+element.v_title+'</option>')
						});
					}
				}
			});
		}else{
			var h = $(".variant"+variantClass+condition_id).html();
			varCat[d];
		}*/
		//var item = $(this);
		//var d = $(this).val();
		/*if(d != ""){
			var variant_this = $(this);
			var variantClass = $(this).attr('data-variantclass');
			var condition_id = $(this).attr('data-condition_id');
			$(".variant"+variantClass+condition_id).html('');
			var isDuplicate = false;
			$('#variantHead'+condition_id+' .variant_cat').each(function(i){
				
				if(d == $('.variant_cat'+condition_id+':eq('+i+')').val() && item.prop('name') != $('.variant_cat'+condition_id+':eq('+i+')').prop('name')){
					alert('Already selected');
					item.find('option[value=""]').prop('selected', true);
					isDuplicate = true;
				}
			});
			if(d && !isDuplicate){
				$.ajax({
					url: "<?php //echo base_url('seller/product/get_variant')?>",
					type:'POST',
					data:{id:d},
					dataType:'json',
					async:false,
					success: function(result){
						if(result.status == 1){
							$(".variant"+variantClass+condition_id).append('<option value="" disabled selected hidden>Please Select</option>');
							$(result.data).each(function(index, element) {
								$(".variant"+variantClass+condition_id).append('<option value="'+element.v_id+'">'+element.v_title+'</option>')
							});
						}
					}
				});
			}
		}*/
	});

	jQuery.validator.addMethod("checkImage", function(value, element) {
	var condition_id = $(element).attr('data-id');
	var ret = false;
	if($("#condition_image_link"+condition_id).val()=="" && $("#condition_video_link"+condition_id).val() == "" && $('#condition_image'+condition_id)[0].files.length == 0 ){
		ret = false;	
	}else{
		$("#condition_image"+condition_id).fileinput('destroy');
		$("#condition_image"+condition_id).fileinput({allowedFileExtensions: ["jpg", "png", "jpeg"],
			theme: "fas",
			minImageWidth: 400,
			minImageHeight: 400,
			maxFileCount: 12,
			validateInitialCount: true,
			//uploadUrl: "/file-upload-batch/2",
		});
		ret = true;
	}
	return ret;
}, "Image is Required");

	// jQuery.validator.addMethod("variantCheck", function(value, element) {
	// 	// var conditionId = value;
	// 	$($("#condition_id").val()).each(function(index,value){
	// 		console.log(value);
	// 	});
	// }, "variant is Required");
	function variantValidate(){
		var v_flag = true;
		var s_flag = true;
		var d_flag = true;
		var condition="new";
		var variantCheck = Array();
		var i =0;
		var j =0;
		var testVariable;
		$($("#condition_id").val()).each(function(index,condition_id){
			variantCatLength = ($("#variantHead"+condition_id+" .variant_cat").length)-1;
			//$($("#variant_cat").val()).each(function(v_cat_index,v_cat_id){
			$(allConditions).each(function(index,value){
				if(condition_id == value.condition_id){
					condition = value.condition_name.replace(" ","_").toLowerCase();
					return false;
				}
			});
			variantCheck[condition] = Array();
			i=0;
			j=0;
			testVariable = "";
			$("#variantBody"+condition_id+" .variant").each(function(index,element){
				if($(element).val()){
					$(element).next().html('');
				}else{
					$(element).next().html('<label class="error">This field is required</label>');	
					v_flag = false;
				}
				if(i == 0){
					//variantCheck[condition][j] = Array();
					testVariable = $(element).val();
					//variantCheck[condition][j][i] = $(element).val();
				}else{
					testVariable += ","+$(element).val();
					//variantCheck[condition][j][i] = $(element).val();
				}
				if(i == variantCatLength){
					//variantCheck[condition][j][i] = $(element).val();
					if($.inArray(testVariable,variantCheck[condition]) >= 0){
						//$("#variantBody"+condition_id+" tr").eq(j).append('<label class="error">Same Row Exists.</label>');	
						$(element).parent().parent().find(".product_variant").next().html('<label class="error">Same Data Exists.</label>');	
						v_flag = false;
					}
					variantCheck[condition][j]= testVariable;
					testVariable = "";
					j++;
					i=0;
				}else{	
					i++;
				}
			});
			//console.log(variantCheck);
			//return false;
			<?php if($_SESSION['is_zabee'] == "0"){?>
			if(!$.isEmptyObject($("#shippingIds"+condition_id).val())){
			//if($('#shippingIds'+condition_id).val()){
				$('#shippingIds'+condition_id).next().next().html('');
			}else{
				$('#shippingIds'+condition_id+'-error').css('display','block');	
				$('#shippingIds'+condition_id+'-error').html('<label class="error">This field is required</label>');	
				s_flag= false;
			}
			<?php }?>
			if(condition_id != "1"){
				if($('#prod_des'+condition_id).val()){
					$('#prod_des'+condition_id).next().html('');
				}else{
					$('#prod_des'+condition_id+'-error').html('<label class="error">This field is required</label>');
					d_flag = false;
				}
			}

			if(!v_flag || !s_flag || !d_flag){
				activaTab(condition);
				return false;
			}
		});
		if(v_flag && s_flag && d_flag){
				return true;
		}else{
			return false;
		}
		
	}
	function videoLinkValidate(){
		var flag = true;
		return true;
		/*jQuery( 'textarea[id*=condition_video_link]' ).each(function(index,value){
			if(!$("#"+$(value).attr("id")).val()){
				if($(value).attr("data-cond_id") < 10){
					$("#"+$(value).attr("id")).after('<label class="error">This field is required</label>');		
				} else {
					$("#video_link_remove_"+$(value).attr("data-cond_id")).after('<br /><label class="error">This field is required</label>');
				}
				flag = false;	
			} else {
				var patt = new RegExp(" /(http:|https:|)\/\/(player.|www.)?(dailymotion\.com|vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/");
				var res = patt.test($("#"+$(value).attr("id")).val());
				if(!res){
					if($(value).attr("data-cond_id") < 10){
					$("#"+$(value).attr("id")).after('<label class="error">Only Youtube, Vimeo and Dailymotion URL are allowed, make sure the entered URL is correct</label>');		
					} else {
						$("#video_link_remove_"+$(value).attr("data-cond_id")).after('<br /><label class="error">Only Youtube, Vimeo and Dailymotion URL are required, make sure the entered URL is correct</label>');
					}	
				}
			}

		});
		if(flag){
				return true;
		}else{
			return false;
		}*/
	}
	function activaTab(tab){
		$('.nav-tabs a[href="#'+tab+'"]').trigger('click');//.tab('show');
	};
	$('#productForm').validate({
		rules: {
			product_name:{
				required: true,
				//checkImage:true,
				normalizer: function(value) {
					// Note: the value of `this` inside the `normalizer` is the corresponding
					// DOMElement. In this example, `this` reference the `username` element.
					// Trim the value of the input
					return $.trim(value);
				},
				minlength: 2
			},
			"condition_id[]" :{
				required:true,
			},
		},
	
		messages: {
			product_name :{//required: "Please provide product name.", 
			minlength: "Must be at least two characters long"},
		},errorPlacement: function(error, element) {
			error.appendTo(element.parent());
		},
		submitHandler: function(form) {
			var validate = variantValidate();
			var validateVideo = videoLinkValidate();
			 if(validate && validateVideo){
				form.submit();			
			}
 		 }
	});
	
		
	 
	//------- Auto Complete--------//
	$("#keyword").autocomplete({
		source: function( request, response ) {
			$.ajax({
			  url: "<?php echo base_url('seller/product/get_product');?>/"+request.term,
			  dataType: "json",
			  success: function( data ) {
				$('label.error').html('');
				if(data != ""){
					response( data );
				}else{
					//$('#pn').text('No results found.');
					//response([{ label: 'No results found.', val: -1}]);
					$("#save").addClass("d-none");
					response("");
					$('#product_id').val('');
				}
			  }
			});
		  },
		  minLength: 1,
		  select: function( event, ui ) {
			//$("#product_id").val(ui.item.id);
			createData(ui.item.id)
				
			}
	});
	function addMoreRow(c,condition,count,totalVariants, totalRows){
		var ret = [];
		var ids = [];
		var amvHtml="";
		var approve = $("#approve").val();
		var button = '<button type="button" class="close removeRow" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		if(approve == 3){
			button = '<button type="button" class="close retweet" aria-label="Close"><i class="fas fa-retweet"></i></button>';
		}
		amvHtml +='<tr><td width="1%">'+button+'</td>'+
			 	'<td width="5%">'+			
				   '<input type="number" min="1" data-id="qty_'+c+'_'+totalRows+'_'+count+'" id="qty_'+c+'_'+totalRows+'_'+count+'" name="quantity'+c+'['+count+']" value="1" placeholder="1" class="form-control variant_qty" /><input type="hidden" data-id="total_qty_'+c+'_'+totalRows+'_'+count+'" name="total_qty_'+c+'['+count+']" class="form-control variant_total_qty"/><input type="hidden" data-id="previous_qty_'+c+'_'+totalRows+'_'+count+'" name="previous_qty_'+c+'['+count+']" class="form-control variant_previous_qty"/>'+
				'</td>'+
				'<td  width="13%">'+			
				   '<input type="number" min="1" maxlength="19" data-id="price_'+c+'_'+totalRows+'_'+count+'" id="price_'+c+'_'+totalRows+'_'+count+'" name="price'+c+'['+count+']" value="10" placeholder="10" class="form-control variant_price" />'+
				'</td>'+
				'<td  width="13%">'+			
					'<input type="text" data-id="seller_sku_'+c+'_'+totalRows+'_'+count+'" name="seller_sku'+c+'['+totalRows+']" class="form-control variant_sku" />'+
				'</td>';
				<?php if(!empty($discountData)){?>
					amvHtml+=	'<td width="40%"><select name="discount'+c+'['+count+']" data-discount_id="discount_'+c+'_'+totalRows+'_'+count+'" class="form-control variant_discount">'+
								'<option value="">Select Discount</option>'+
									<?php
										$hasDefault = false;
										foreach($discountData as $dd){
										?>
										'<option value="<?php echo $dd->id; ?>"><?php echo $dd->value."% -".addslashes($dd->title);echo" -  validity: ".$dd->valid_from;echo" till ".$dd->valid_to;?></option>'+
									<?php }?>	
									'</select></td>';
				<?php }?>
				if(c != 1){
				<?php if(!empty($warrantyData)){?>
					amvHtml+=	'<td><select name="warranty'+c+'['+count+']" id="warranty_'+c+'_'+totalRows+'_'+count+'" data-warranty_id="warranty_'+c+'_'+totalRows+'_'+count+'" class="form-control variant_warranty">'+
							'<option value="" disabled selected hidden>-Select Warranty-</option>'+
								<?php
									foreach($warrantyData as $wd){
										$day = ($wd->warranty > 1)?" Days":" Day";
									?>
									'<option value="<?php echo $wd->id; ?>"><?php echo $wd->warranty.$day;?></option>'+
								<?php }?>	
								'</select></td>';
				<?php }?>}
				<?php if(!empty($shippingData)){?>
					amvHtml+='<td><select name="shipping'+c+'['+count+'][]" id="shipping'+c+'_'+totalRows+'_'+count+'" data-shipping_id="shipping'+c+'_'+totalRows+'_'+count+'"class="form-control shippingIds" multiple data-placeholder="Select Shipping Methods">'+
						<?php
							$hasDefault = false;
							foreach($shippingData as $sd){
							?>
								'<option value="<?php echo $sd->shipping_id; ?>"><?php echo ($sd->price !=0)?"$".$sd->price:"Free Shipping";echo " - ".$sd->title." -  Days: ".$sd->duration; ?></option>'+
							<?php }?>	
					'</select></td>';
				<?php } ?>	
			$(totalVariants).each(function(index, element) {
			var h = $(".variant"+element+c).html();
			amvHtml+='<td>'+
				'<select name = "variant'+c+'['+element+'][]" data-id="variant_'+c+'_'+totalRows+'_'+element+'" id="variant_'+c+'_'+totalRows+'_'+element+'" class = "product_variant variant variant'+element+c+' form-control" >'+h+'</select><span class="error"></span>'+
			'</td>';
				ids.push('variant_'+c+'_'+totalRows+'_'+element);
			});	
		amvHtml+='<input type="hidden" value="" id="pv_id_'+c+'_'+totalRows+'_'+count+'" class="pv_id pv_id'+c+'" name="pv_id'+c+'['+count+']"><input type="hidden" value="" id="inventory_id_'+c+'_'+totalRows+'_'+count+'" class="inventory_id inventory_id'+c+'" name="inventory_id'+c+'['+count+']"></tr>';
		ret['content'] = amvHtml;
		ret['variants_ids'] = ids;
		return ret;
	}
	/*function addMoreVariant(c,condition,count){
		var amvHtml="";
		amvHtml +='<th>'+
				'Attribute<button type="button" class="close removeAttribute" data-condition_id = "'+c+'" data-count="'+count+'" data-condition="'+condition+'" id="btn_variant_cat_'+c+'_'+count+'" data-cat_id="variant_cat_'+c+'_'+count+'" data-vari_class="variant'+count+c+'" aria-label="Close"><span aria-hidden="true">&times;</span></button>';<?php 
						//if($variantData)
						//{
						//?>
				amvHtml+='<select name = "variant_cat'+c+'['+count+']" data-variantclass="'+count+'" data-condition_id="'+c+'" class = "variant_cat variant_cat'+c+' form-control valid variantVal'+variantVal+'" id="variant_cat_'+c+'_'+count+'"><option value="" disabled selected hidden>Please Select</option>';
						//<?php
						//foreach($variantData as $parent)
						//{
						// ?>
					amvHtml+= "<option value = '<?php //echo $parent['v_cat_id']?>'><?php //echo $parent['v_cat_title'];?></option>";
						<?php //}
						//?>
						amvHtml+='</select>';
						<?php
						//}
						//else
						//{
						?>
						amvHtml+="<span style = 'color:red;'>No Variant Category found.</span>Please <a href='<?php //echo base_url('seller/variantcategories');?>'>Add Variant Category</a>";
						<?php //}
						?>
					amvHtml+='</th>';//+
		return amvHtml;
	}*/
	$(document).on('click', '.amr',function(event){
		
		event.preventDefault();
		var condition_id = $(this).attr('data-condition_id'); 
		var condition = $(this).attr('data-condition'); 
		var count = $(this).attr('data-count');
		var totalRows = $("#variantBody"+condition_id+" tr").length;
		count = ++count;
		$(this).attr('data-count',count);
		var totalCols = removeAttributeIndex[condition];//$(this +' .totalrows'+condition_id).eq(0).find('.variant').length;
		var amr = addMoreRow(condition_id,condition,count,totalCols, totalRows);
		$("#variantBody"+condition_id).append(amr['content']);
		$('input[id="qty_'+condition_id+'_'+totalRows+'_'+count+'"]').rules("add", "required");
		$('input[id="price_'+condition_id+'_'+totalRows+'_'+count+'"]').rules("add", "required");
		for(i = 0;i<amr['variants_ids'].length;i++){
			var item = amr['variants_ids'][i];
			$("select[id="+item+"]").rules("add", "required");
			$("#productForm").validate(); //sets up the validator
			//console.log($('select[data-id="'+item+'"]').rules("add", "required"));
		}
		$('#shipping'+condition_id+'_'+totalRows+'_'+count).select2({width: 'resolve', placeholder: function(){
        $(this).data('placeholder');}}).on('change', function() {$(this).valid();});
	});
	/*$(document).on('click', '.addmorevariant',function(event){
		event.preventDefault();
		$('.addmorevariant').each(function(index,element){
			var condition_id = $(element).attr('data-condition_id'); 
			var condition = $(element).attr('data-condition'); 
			var count = $(element).attr('data-count');
			count = ++count;
			if(typeof removeAttributeIndex[condition] === 'undefined'){
				removeAttributeIndex[condition]=[];
				removeAttributeIndex[condition].push(count);	
			}else{
				removeAttributeIndex[condition].push(count);	
			}
			$(element).attr('data-count',count);
			//$(this).attr('data-count',count);
			var amv = addMoreVariant(condition_id,condition,count);
			$("#variantHead"+condition_id+">tr").append(amv)
			var totalRow = $("#variantBody"+condition_id+" tr").length;	
			//var totalRows = $('#variantBody1 tr').length;
			var totalSelect = $("#variantHead"+condition_id+" tr").find('select').length;
			for (var i=0; i<totalRow; i++){
				var trs = $("#variantBody"+condition_id+" tr").eq(i).find('select').length;
				if(trs < totalSelect){
					//for(var j = trs; j<totalSelect; j++){
						var id = 'variant_'+condition_id+'_'+i+'_'+count;
						//console.log(id);
						$("#variantBody"+condition_id+" tr").eq(i).append('<td><select data-id="'+id+'" id="'+id+'" name="variant'+condition_id+'['+count+'][]"  class="valid variant variant'+removeAttributeIndex[condition][trs]+condition_id+' form-control" ></select><span class="error"></span></td>');
						$("#productForm").validate(); //sets up the validator
						$('select[data-id="'+id+'"]').rules("add", "required");
					//}
				}
			}
			$('select[id="'+$('select.variant_cat').last().attr('id')+'"]').rules("add", "required");
			vg = count+1;
		});
	});*/
$(document).on('change','#mySelect', function() {
				  var value = $(this).val();
				  $('#return_id').val(value);
				});
$(document).on('click','.removeAttribute',function(){
	var count = $(this).attr('data-count');
	var condition_id = "";
	var cat_id = "";
	var variant_class="";
	var condition = "";
	//var cat_id = $(this).attr('data-cat_id');
	//var variant_class =  $(this).attr('data-vari_class');
	//var condition = $(this).attr('data-condition');
	$("#condition_id option:selected").each(function(index,element){
		condition_id = $(element).val();
		cat_id = "variant_cat_"+condition_id+"_"+count;
		variant_class = "variant"+count+condition_id;
		condition = $("#"+cat_id).attr('data-condition');
		remove(removeAttributeIndex[condition],parseInt(count));
		$("#"+cat_id).parent().remove();
		$("."+variant_class).parent().remove();
	});
	//$(this).parent().remove();
	//$("."+variant_class).parent().remove();
});
$(document).on('click','.removeRow',function(){
	//$(this).parent().parent()
	var pvId = $(this).parent().parent().find("input[type=hidden].pv_id").val();
	var element = $(this);
	if(pvId){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/product/deletePv');?>",
			data:{'pvId':pvId },
			success: function(response){
				if(response){
					element.parent().parent().remove();
				}
			}
		});
	}else{
		$(this).parent().parent().remove();
	}
});
var inventoryElement = "";
$(document).on('click','.retweet',function(){
	var inventory_id = $(this).parent().parent().find("input[type=hidden].inventory_id").val();
	inventoryElement = this;
	$("#recreate_id").val(inventory_id);
	$("#recreate-modal").modal('show');
});
$(document).on("click","#recreate_btn",function(){
	var inventory_id = $("#recreate_id").val();
	var inventoryElement = $("input[value=8926].inventory_id");
	if(inventory_id){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/product/recreateInventory');?>",
			data:{'id':inventory_id},
			success: function(response){
				if(response.status){
					$("input[value="+inventory_id+"].inventory_id").parent().removeClass("table-dark");
					$("input[value="+inventory_id+"].inventory_id").parent().find(".retweet").remove();
				}
			}
		});
	}else{
		alert("Invetory id is missing!");
	}
});
function remove(array, element) {
    const index = array.indexOf(element);
    
    if (index !== -1) {
        array.splice(index, 1);
    }
}
function addValidation(classname){
	$(classname).each(function () {
		$(this).rules("add", {
			required: true
		});
	});
}
var i=0;
/*$(document).on('select2:select','#variant_cat', function (evt) {
	var id = evt.params.data.id;
	variantVal = id;
	if($("#condition_id option:selected").length == 0){
		alert('Please Select Condition First.');
		$(this).select2('val', '')
		return false;
	}else{
		//alert(i++);
		var condition_id = $("#condition_id option:selected").val();
		$.when($('#addmorevariant'+condition_id).trigger('click')).done(function( x ) {
		  $('.variantVal'+id).val(id).trigger('change');			
		});
		//$('.variantVal'+id).val(id).tigger('change');			
	}
		
	//addFieldSet(id);
});*/
/*$(document).on('select2:unselect','#variant_cat', function (evt) {
	var id = evt.params.data.id;
	var count = $(".addmorevariant").attr('data-count');
	$('.variantVal'+id).prev().trigger('click');
	count = (count-1);
	$(".addmorevariant").attr('data-count',count);
	vg = (vg-1);
	//console.log(vg);
});*/
$(document).on('mousedown','.variant_cat', function(e) {
   e.preventDefault();
   this.blur();
   window.focus();
});
$(document).on("change",'.variant,.product_variant',function(e){
	$(this).next().html('');
})
$("#addMoreFeatures").on('click',function(){
	$("#features").after('<div class="row features"><div class="col-sm-6 mt-2"><input type="text" class="form-control" name="feature[]"  /></div><div class="col-sm-2"><button type="button" class="close feature_remove" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>');
});
$("#featuresDiv").on('click','.feature_remove',function(){
	$(this).parent().parent().remove();
})
$("#keyword").on("keyup",function(e){
	if($(this).val().length == 0){
		$("#condition_id option:selected").each(function(index,value){ 
			removeAttributeIndex = [];
			var data = {"id": $(value).val()};
			$("#condition_id option[value='"+$(value).val()+"']").prop("selected", false).trigger('change');
			$("#condition_id").trigger({
				type: 'select2:unselect',
				params: {data: data}
			});
		});
		$("#condition_fieldset").hide();
		$('#condition_fieldset .nav-tabs li').remove();
		$('#condition_fieldset .tab-content div').remove();
	}
});
function createData(product_id){
	if(product_id !=""){
		$("#product_id").val(product_id);
		var seller_id = "<?php echo ($this->session->userdata['user_type'] == "1")?"60dc1beac4485":$this->session->userdata['userid'];?>";
		var mpn = "<?php echo $this->input->get("mpn")?>";
		var hubx_id = "<?php echo $this->input->get("hubx_id")?>";
		var productPath = "<?php echo product_path()?>";
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/product/getProductDataForInventory');?>",
			data:{'id':product_id,"seller_id":seller_id,"mpn":mpn,"hubx_id":hubx_id},
			success: function(response){
				if(response.status == "success"){
					//console.log("createData");
					$("#save").removeClass("d-none");
					$("#condition_id").removeAttr("disabled");
					$("#condition_id option:selected").each(function(index,value){ 
						removeAttributeIndex = [];
						var data = {"id": $(value).val()};
						$("#condition_id option[value='"+$(value).val()+"']").prop("selected", false).trigger('change');
						$("#condition_id").trigger({
							type: 'select2:unselect',
							params: {data: data}
						});
					});
					/*if(response.data[0].variant_cat_group !="" && typeof(response.data[0].variant_cat_group) !=="undefined"){
						catVariant = response.data[0].variant_cat_group.split(',');
					}else{
						catVariant = "";
					}*/
					if(response.data.preDefinedVariant[0].v_cat_id !=null && response.data.preDefinedVariant[0].v_cat_title !=null){
						variantCatData = response.data.preDefinedVariant;
						vg = response.data.preDefinedVariant.length;
					}
					conditionData = response.data.condition;
					var sp_id = "";
					var i =0;
					var condition_id = [];
					var sp_idData = Array();
					//console.log(response.data.condition);
					$(response.data.condition).each(function(index,value){
						$("#approve").val(value.approve);
						//console.log(value.sp_id);console.log(sp_id)
						if(sp_id != value.sp_id){
							i=0;
							if(jQuery.inArray(value.condition_id, condition_id) === -1) {
								$("#condition_id option[value='"+value.condition_id+"']").prop("selected", true).trigger('change');
								var data = {"id": value.condition_id,"imageData":response.data.imageData[value.condition_id],"sp_id":value.sp_id};
								$("#condition_id").trigger({
									type: 'select2:select',
									params: {data: data}
								});	
							}
							condition_id.push(value.condition_id);
							/*if(value.warehouse_id){
								
							}*/
							/*if(value.seller_sku){
								$("#seller_sku"+value.condition_id).val(value.seller_sku);	
							}*/
							$("#mySelect"+value.condition_id).val(value.return_id);
							var shipping_ids = value.shipping_ids.split(",");
							$(shipping_ids).each(function(shipping_index,shipping_value){
								$("#shippingIds"+value.condition_id+" option[value='"+shipping_value+"']").prop("selected", true).trigger('change');	
							});
							if(response.data.inActiveShipping[value.condition_id]){
								$(response.data.inActiveShipping[value.condition_id]).each(function(s_index,s_value){
									price = (s_value.price !=0)?"$"+s_value.price:"Free Shipping";
									title = s_value.title+" - Days: "+s_value.duration;
									var newOption = new Option(price+" - "+title, s_value.shipping_id, true, true);
    								$("#shippingIds"+value.condition_id).append(newOption).trigger('change');
								});
							}
							//console.log(response.data);
							if(response.data.image[value.condition_id]){
								// console.log(response.data.image);
								imageLength = response.data.image[value.condition_id].length;
										if(imageLength > 0){
										//$('#label_wrapper_'+value.condition_id).append('<label class="col-form-label " >Uploaded Images</label>');
										var is_cover = "";
										var checkVideolink =0;
										$(response.data.image[value.condition_id]).each(function(i,v){
											if(v.is_cover == 1){
												is_cover = '<div class="cover-image text-center">Cover</div>';	
											}else{
												is_cover = "";
											}
											if(v.is_image == 0){
													if(checkVideolink == 0){
													$("#product_video_link"+value.condition_id).find("input").eq(0).val(v.iv_link);	
													$("#mediaId"+value.condition_id).val(v.media_id);
													checkVideolink=1;
												}else{
													$("#product_video_link"+value.condition_id).after('<div class="row video_link"><div class="col-sm-6 mt-2"><input type="text" class="form-control" name="condition_video_link'+value.condition_id+'[]" id="condition_video_link'+value.condition_id+'" data-cond_id="'+value.condition_id+'" value="'+v.image_link+'" /><input type="hidden" name="media_id'+value.condition_id+'[]" class="mediaIdClass" id="mediaId'+value.condition_id+'" value="'+v.media_id+'"/></div><div class="col-sm-2"><button type="button" data-mediaId = "'+v.media_id+'" class="close video_link_remove close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>');
												}
											}else{
												//$('#wrapper_'+value.condition_id).append('<div class="images" id="img'+i+'"><div class="img" style="background-image:url('+productPath+v.image_link+')" id="cover_id'+v.media_id+'"><span class="make_cover" title="Make Cover Image" data-cond = "'+value.condition_id+'" data-id="'+v.media_id+'"><i class="fas fa-camera-retro"></i></span><span class="remove_image" data-condition_id ="'+value.condition_id+'" data-link="'+v.image_link+'" data-div_id="img'+i+'" data-id="'+v.media_id+'"><i class="fa fa-window-close" title="Delete Image" aria-hidden="true"></i></span>'+is_cover+'</div></div>');
											}
											});
										
										/*if(imageLength < 12){
											maxImage = (12-imageLength);
											$("#condition_image"+value.condition_id).fileinput('refresh', {maxFileCount: maxImage});
										}else{
											$("#condition_image"+value.condition_id).fileinput('disable');
											maxImage = 0;
										}*/
									}
							// 	$(response.data.image[value.condition_id]).each(function(s_index,s_value){
							// 		$('#wrapper').append('<div class="images" id="img'+index+'"><div class="img" style="background-image:url('+productPath+s_value.image_link+')" id="cover_id'+s_value.media_id+'"><span class="make_cover" title="Make Cover Image" data-id="'+s_value.media_id+'"><i class="fas fa-camera-retro"></i></span><span class="remove_image" data-link="'+s_value.image_link+'" data-div_id="img'+s_index+'" data-id="'+s_value.media_id+'"><i class="fa fa-window-close" title="Delete Image" aria-hidden="true"></i></span>'+s_value.is_cover+'</div></div>');
							// 	});
							}
							
							if(value.condition_id !="1"){
								$("#prod_des"+value.condition_id).text(value.seller_product_description);
							}
						}
						sp_id = value.sp_id;
						if(sp_id == value.sp_id){
							var variant = value.variant_group.split(",");
							if(i != 0){
								$("#addmorerow"+value.condition_id).trigger("click");
							}
							//console.log(i);
							//Inventory Shipping
							ship = value.shipping;
							if(ship){
								ship = ship.split(",");
								$(ship).each(function(shipping_index,shipping_value){
									$("#shipping"+value.condition_id+'_'+i+'_'+i+" option[value='"+shipping_value+"']").prop("selected", true).trigger('change');
								});
							}
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant").each(function(index,value){ $(this).val(variant)});
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_qty").val(value.quantity);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_total_qty").val(value.total_qty);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_previous_qty").val(value.quantity);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_price").val(value.price);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_discount").val(value.discount);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_warranty").val(value.warranty_id);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".hubx_id").val(value.hubx_id);
							$("#variantBody"+value.condition_id+" tr").eq(i).find(".variant_sku").val(value.seller_sku);
							$("#variantBody"+value.condition_id+" .pv_id"+value.condition_id).eq(i).val(value.pv_id);
							$("#variantBody"+value.condition_id+" .inventory_id"+value.condition_id).eq(i).val(value.inventory_id);
							i++;
							//console.log(i);
						}
						$("#sp_id"+value.condition_id).val(value.sp_id);
					});
					$("#approve").val("");
				}else{
					$("#keyword").next().append('<span class="error">No result found</span>');
				}
			}
		});
	}
}
$(document).on('click','.make_cover',function(){ 
	var img_id = $(this).attr("data-id");
	var cond_id = $(this).attr("data-cond");
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
		$("#condition").val(cond_id);
		$(".confirm_del").removeClass("d-none");
		$("#mt").html("Change Cover Image?");
		$(".box-content").html("Are you sure?");
		$('#image_del').modal('show');
	}
});

$(document).on('click','.remove_image',function(){ 
	var img_length = $(".images").length;
	if(img_length > 1){
		$("#forWhat").val("remove_image");
		$("#imgDel_id").val($(this).attr("data-id"));
		var c = $(this).attr("data-condition");
		$("#div_id").val($(this).attr("data-div_id"));
		$("#link").val($(this).attr("data-link"));
		$(".confirm_del").removeClass("d-none");
		$("#condition").val($(this).attr("data-condition_id"));
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

$(document).on('click','.confirm_del',function(){
	var id = $("#imgDel_id").val();
	var divId = $("#div_id").val();
	var img_link = $("#link").val();
	var forWhat = $("#forWhat").val();
	var hitUrl = "";
	var data = "";
	var c = $("#condition").val();
	var isCoverImage = $("#cover_id"+id).find('.cover-image').length;
	if(isCoverImage == 0){
		if(forWhat == "remove_image"){
			hitUrl = "<?php echo base_url('seller/product/deleteData');?>";
			data = {'i':id,'t':"product_media",'w':'media_id','ed':img_link};
		}else{
			hitUrl = "<?php echo base_url('seller/product/changeCoverImage');?>"
			data = {'pi':$('#product_id').val(),'i':id,'c':c}
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
					$('#condition_image'+c).fileinput('refresh', {maxFileCount: maxImage});
					//$('#input-b8').fileinput('destroy').fileinput({maxFileCount: maxImage})
					$('#condition_image'+c).fileinput('enable');
				}
				if(response.status == 1){
					$("#wrapper_"+c).find(".cover-image").remove();
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
/*$(document).on('click','#addMoreVideoLink', function() {
	if($( ".textarea" ).find('span').length == 0){
		var lastTextareaId = $('#textarea-row .textarea').children('textarea').last().attr('id');
	} else {
		var lastTextareaId = $('#textarea-row .textarea span').children('textarea').last().attr('id');
	}
	var splitted = lastTextareaId.split("_");
	var bmpDigits = /[0-9\u0660-\u0669\u06F0-\u06F9\u07C0-\u07C9\u0966-\u096F\u09E6-\u09EF\u0A66-\u0AE6\u0AE6-\u0AEF\u0B66-\u0B6F\u0BE6-\u0BEF\u0C66-\u0C6F\u0CE6-\u0CEF\u0D66-\u0D6F\u0DE6-\u0DEF\u0E50-\u0E59\u0ED0-\u0ED9\u0F20-\u0F29\u1040-\u1049\u1090-\u1099\u17E0-\u17E9\u1810-\u1819\u1946-\u194F\u19D0-\u19D9\u1A80-\u1A89\u1A90-\u1A99\u1B50-\u1B59\u1BB0-\u1BB9\u1C40-\u1C49\u1C50-\u1C59\uA620-\uA629\uA8D0-\uA8D9\uA900-\uA909\uA9D0-\uA9D9\uA9F0-\uA9F9\uAA50-\uAA59\uABF0-\uABF9\uFF10-\uFF19]/;
	var hasNumber = RegExp.prototype.test.bind(bmpDigits);
	if(hasNumber(splitted[2])){
		var num = splitted[2].replace(/[^0-9]/g,'');
		if(num < 10){
			num = num + '0';
			num = parseInt(num);
		}
		num = parseInt(num);
		num = num + 1; 
	}

	$(".textarea").append('<span style="display:flex;"><textarea name="condition_video_link'+num+'[]" data-cond_id = "'+num+'" class="form-control" id="condition_video_link'+num+'"  placeholder="Enter Video Link "></textarea>&nbsp;&nbsp;&nbsp;<button type="button" id="video_link_remove_'+num+'" class="close video_link_remove close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button></span><br />');
});*/
$(document).on('click','.addMoreVideoLink', function() {
		var condition_id = $(this).attr("data-condition_id");
		$("#product_video_link"+condition_id).after('<div class="row video_link"><div class="col-sm-6 mt-2"><input type="text" class="form-control" name="condition_video_link'+condition_id+'[]" /><input type="hidden" name="media_id'+condition_id+'[]" class="mediaIdClass" id="mediaId'+condition_id+'" value=""/></div><div class="col-sm-2"><button type="button" data-mediaid = "" class="close video_link_remove close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button></div></div>');
		// $("#textarea-row"+condition_id).append('<div class="col-sm-12" style="display:flex;"><textarea name="condition_video_link'+condition_id+'[]" class="form-control"  placeholder="Enter Video Link "></textarea>&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="media_id'+condition_id+'[]" class="mediaIdClass" id="mediaId'+condition_id+'" value=""/><button type="button" data-mediaid = "" class="close video_link_remove close_btn" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><br />');
});


$(document).on('click','.video_link_remove', function() {
	// $(this).parent().find('.mediaIdClass');
	var media_id = $(this).attr('data-mediaid');
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
$(document).on("keypress keyup blur",".variant_qty",function (event) {    
	$(this).val($(this).val().replace(/[^\d].+/, ""));
		if ((event.which < 48 || event.which > 57)) {
			event.preventDefault();
		}
});
function fileInputInit(id,condition_id,preview,config,sp_id){
	if(preview !="" && config !=""){
		preview = preview.split(',');
		config = JSON.parse(config);
	}else{
		preview = [];
		config = [];
	}
	var product_id = $("#product_id").val();
	$(id).fileinput({
		uploadUrl: "<?php echo base_url("seller/product/uploadAjax")?>",
		enableResumableUpload: true,
		//required:true,
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
			'column':"product_id",
			'sp_id':sp_id,
			'condition_id':condition_id,
			"dummy_id": dummy_id,
		},
		//minFileCount:1,
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
		initialPreview: preview,
        initialPreviewConfig: config,
		allowedFileExtensions: ["jpg", "png", "gif","jpge"],
		deleteUrl:'<?php echo base_url('seller/product/deleteData'); ?>',
	}).on('fileuploaded', function(event, previewId, index, fileId) {
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
		var find_id = $("#condition_id option[value="+condition_id+"]").text().replace(" ","_").toLowerCase();
		find_id = "."+find_id+"_img";
		$(find_id+" .file-sortable").each(function(index,value){
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
}
function dummy_id() {
  var d = new Date();
  var n = d.valueOf();
  $("#dummy_id").val(n);
  return n;
}
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
			<input type="hidden" value="" id="condition" />
            <button class="btn btn-primary confirm_del"  >Yes</button>
			<a href="#" class="btn btn-default" data-dismiss="modal">Close</a>
		  </div>
		</div>
	</div>
</div>
<div class="modal fade" id="condition_del" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Condition</h5>
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
          	<input type="hidden" value="" id="conditionName" />
            <input type="hidden" value="" id="conditionId" />
            <button class="btn btn-primary" id="conditionDelConfirm" >Yes</button>
			<a href="#" class="btn btn-default" data-dismiss="modal">No</a>
		  </div>
		</div>
	</div>
</div>