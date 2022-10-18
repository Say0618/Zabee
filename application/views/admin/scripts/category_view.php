<script>
var imageLink = "<?php echo image_url('categories/') ?>"; 
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/categories/hard_delete'); ?>/"+id,
			success: function(response){
				setTimeout(function(){
					window.location.href = pageLink+'?status='+response.status;
				}, 200);
			}
		});	
	});
}
var oTable;
oTable = $('.datatables').dataTable({
			dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn',
					text: 'Add Categories',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/categories/form') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Category" },
            //"aaSorting": [[1, "asc"], [2, "asc"]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/categories/get/'.$id) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
					"value": "<?php echo $this->security->get_csrf_hash() ?>",
				});
				$.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"aoColumnDefs": [
				{
				"aTargets": [2],
				"mRender": function ( data, type, full ) {
					return '<a href="<?php echo base_url("seller/categories?id=")?>'+full[0]+'">'+full[2]+'</a>'
				
				},
			},{
				"aTargets": [3],
				"mRender": function ( data, type, full ) {
					url = (full[3] != "")?"<p class='font-weight-normal'>"+full[3]+"</p>":"";
					return url;
				
				},
			},{
				"aTargets": [4],
				"mRender": function ( data, type, full ) {
					//alert(imageLink+data);
					if(data != null){
						var n = imageLink+data.toString();
						var res = n.split("/");
						var last = res.pop();
						if(last != ""){
							return '<img src="'+imageLink+data+'" alt="" style="width:80px;" />';
						} else {
							return '';
						}
					} else {
						return '';
					}
				},
			},{
				"aTargets": [5],
				"mRender": function ( data, type, full ) {
					var checked ="";
					if(full[5] == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" id="toggle_'+full[0]+'" data-catid="'+full[0]+'" data-isactive="'+full[5]+'" id="category'+full[1]+'"  onchange="updatestatus(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
                    return checkbox;
				}
			},{
				"aTargets": [6],
				"mRender": function ( data, type, full ) {
					var checked ="";
					if(full[6] == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" id="toggle_'+full[0]+'" data-catid="'+full[0]+'" data-isactive="'+full[6]+'" id="homepage'+full[1]+'"  onchange="showOnHomepage(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
                    return checkbox;
				}
			}
			],
			"aoColumns": [
				{"bSortable": false},null, null, null, null, null,null, {"bSortable": false},
            ],
			"fnDrawCallback": function ( oSettings ) {
				$('.toggle-two').bootstrapToggle({
					on: 'Yes',
					off: 'No'
				});
			$('tr td:nth-child(1)').hide();
			$('tr th:nth-child(1)').hide();
			$('tr td:nth-child(2)').hide();
			$('tr th:nth-child(2)').hide();
		},
		"createdRow": function( row, data, dataIndex ) {
			$(row).attr( "data-position",data[1] );
			$(row).attr( "data-id",data[0] );
			$(row).attr("id","row-"+data[0]+"-"+data[1]);
		}
			
});
$("table tbody").sortable({
		items: 'tr',
		attribute :"data",
        stop : function(event, ui){
			//console.log($(this).sortable('toArray'))
			var array = $(this).sortable('toArray');
        	$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url('seller/categories/changePosition'); ?>",
				data:{data:array},
				success: function(response){
					//console.log(response);
				}
			});	
        }
    });
$("table tbody").disableSelection();
$('.EditModalBtn').on('click', function(e){
   e.preventDefault();
   $('#editcategorymodal').modal('show').find('.modal-content').load($(this).attr('href'));
  });
/*function inactiveToggle(identifier){
	var id = $(identifier).data('catid');
	var status = $(identifier).data('isactive');	
	if(status == 1){
		$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/categories/checkDependencies');?>",
		data: {'id':id},
		success: function(response){
			if(response.hasProducts != 0 || response.hascategories != 0 ){
				$.ajax({
					type: "POST",
					cache:false,
					dataType: "JSON",
					async:true,
					url: "<?php echo base_url('seller/categories/getAllCategories');?>",
					data:{'id':id},
					success: function(result){
						var stringHtml = "";
						$('#toggle_'+id).parent().addClass('on').removeClass('off');	
						stringHtml +='<form action="<?php echo base_url('seller/categories/updateProductCategory')?>" method="POST" id="catUpdate">';
						stringHtml += '<input type="hidden" name="old_cat_id" value="'+id+'" />'
						if(response.hasProducts == 1){
							stringHtml +='<fieldset><p class="text-red">Category can not be deactivated, it is being used by product(s). Shift the product(s) to some other category if you want to deactivate this category.</p>';
							$(response.data.products).each(function(index,value){
									stringHtml += '<label id="productName">'+value.product_name+'</label>';
									stringHtml +='<input type="hidden" id="product_id" name="product_id[]" value="'+value.product_id+'">';
									stringHtml += '<div  class="col-sm-12">';
									stringHtml +='<select name = "subcategory_id[]" value="<?php echo set_value('subcategory_id[]'); ?>" class = "subcategory_id form-control" id="subcategory_id_'+index+'" data-placeholder="Select Categories"><option></option>';
									$(result.data).each(function(index,value){
										stringHtml +="<option value = '"+value.category_id+"' pcat = '"+value.parent_category_id+"'>"+value.category_name+"</option>";
									});
									stringHtml += '</select></div>';	
							});
							stringHtml +='</fieldset>';
							}
							if(response.hascategories == 1){
							stringHtml +='<fieldset class="mt-3"><p class="text-red">Category can not be deactivated, it is being used by SubCategory(ies). Shift the category(ies) to some other Parent category if you want to deactivate this category.</p>';
							$(response.data.categories).each(function(index,value){
								stringHtml += '<label id="childCatName">'+value.category_name+'</label>';
								stringHtml += '<div  class="col-sm-12">';
									stringHtml +='<select name = "parentCat_id[]" value="<?php echo set_value('parentCat_id[]'); ?>" class = "parentCat_id form-control" id="parentCat_id'+index+'" data-placeholder="Select Categories"><option></option>';
									$(result.data).each(function(index,value){
									stringHtml +="<option value = '"+value.category_id+"' pcat = '"+value.parent_category_id+"'>"+value.category_name+"</option>";
									});
									stringHtml += '</select></div>';
									stringHtml += "<input type='hidden' name='category_id[]' value='"+value.category_id+"'/>";	
							});
							stringHtml +='</fieldset>';
							}
							stringHtml +='<div class="modal-footer d-flex justify-content-center"><button class="btn btn-success changeCat" id="changeCat">Change</button>';
							stringHtml +='</form>';	
							$('#modalCategory .modal-body div').html(stringHtml);
							$('#modalCategory').modal('show');
							$("#catUpdate").validate();
							// $("#subcategory_id_"+id).rules("add", "required");
							for(i=0;i<response.length;i++){
								$("#subcategory_id_"+i).rules("add", "required");
								}
							$("#catUpdate").validate();
								//  console.log(response);
							// $(identifier).data('isactive',1);
					// 	}
					// });
					}
				});
			}
			else{
				updatestatus(identifier);
			}
		}
	});
	}
	else{
		updatestatus(identifier);
	}
}*/
$(document).on("click","#changeCat",function(){
	var error = false;
	$(".subcategory_id").each(function(index,value){
		if($(value).val() ==""){
			error = true;
			$(this).parent().find(".error").remove();
			$(this).parent().append("<span class='error'>This is required.</span>")
		}else{
			$(this).parent().find(".error").remove();
		}
	});
	if(!error){
		$("#catUpdate").submit();
	}
	return false;
});
$(document).on("change",".subcategory_id",function(){
	if($(this).val() !=""){
		$(this).parent().find(".error").remove();
	}else{
		$(this).parent().append("<span class='error'>This is required.</span>")
	}
});
function updatestatus(identifier){  
	var id = $(identifier).data('catid');
	var status = $(identifier).data('isactive');
	var value = 0;
	if(status == 1){
		value = 0;
		$(identifier).data('isactive',0);
	} 
	else if(status == 0){
		$(identifier).data('isactive',1);
		value = 1;
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/categories/delete');?>",
		data: {'id':id, 'value':value},
		success: function(response){
			//$("#btn"+id).parent().parent().remove()
			/*if(response.success == true){
				setTimeout(function(){
				   location.reload();
			  }, 200); 
			}*/
		}
	});
	//});
}
function showOnHomepage(identifier){  
	var id = $(identifier).data('catid');
	var status = $(identifier).data('isactive');
	var value = 0;
	if(status == 1){
		value = 0;
		$(identifier).data('isactive',0);
	} 
	else if(status == 0){
		$(identifier).data('isactive',1);
		value = 1;
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/categories/show_on_homepage');?>",
		data: {'id':id, 'value':value},
		success: function(response){
			
		}
	});
}
</script>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Category</h5>
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
			<a href="#" class="btn" data-dismiss="modal">No</a>
			<input type = "submit" value = "Yes" class="btn btn-primary confirm_del" />
		  </div>
		</div>
	</div>
</div>
	
<div class="modal fade" id="categorymodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<?php 
			$attr = array
			(
				"method"=>"POST",
				"class"=>"form-horizontal",
				"onsubmit"=>"validate(this,event);"
			);
			echo form_open_multipart("",$attr);
		?>
	<input type ="hidden" name = "category_func" value = "" class = "category_func"/>
	<input type ="hidden" name = "category_count" value = "" class = "category_count"/>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Create Category</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content categoriesmodal-body">
				<fieldset></fieldset>
			</div>				
		  </div>
		  <div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Close</a>
			<input type = "submit" value = "Save changes" class="btn btn-primary" />
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!-- <div class="modal-header text-center">
        <p class="modal-title w-100 text-red pdtheader">Category can not be deactivated, it is being used by product(s). Shift the product(s) to some other category if you want to deactivate this category.</p>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
			</div> -->
      <div class="modal-body mx-3" id="catModal">
        <div class="md-form mb-4" id="categoryChangeData">
					<div></div>
				</div>
      </div>
    </div>
  </div>
</div>