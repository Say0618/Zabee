<script>
var imageLink = "<?php echo image_url('brands/') ?>"; 
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
		$.ajax({
				type: "GET",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url('seller/brands/soft_delete');?>/"+id,
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
					text: 'Add Brands',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/brands/form') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Brands" },
            "aaSorting": [[0, "asc"], [1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/brands/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
			},
			"aoColumnDefs": [{
				"aTargets": [2],
				"mRender": function ( data, type, full ) {
					if(data != null){
						return '<img src="<?php echo image_url('brands/')?>'+data+'" alt="" style="width:80px;" />';
					} else {
						return '';
					}
					
				},
			},{	
				"aTargets": [4],
				"mRender": function ( data, type, full ) {
					var checked ="";
					if(full[4] == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" id="toggle_'+full[0]+'" data-catid="'+full[0]+'" data-isactive="'+full[4]+'" id="category'+full[4]+'"  onchange="inactiveToggle(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
                    return checkbox;
				}
			},{	
				"aTargets": [5],
				"mRender": function ( data, type, full ) {
					var checked ="";
					if(full[5] == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" id="block_'+full[0]+'" data-catid="'+full[0]+'" data-isactive="'+full[5]+'" id="category'+full[4]+'"  onchange="blockToggle(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
                    return checkbox;
				}
			},
			],
			"aoColumns": [
                null, {"bSortable": false},{"bSortable": false},null,null,null,{"bSortable": false},
            ],
			"fnDrawCallback": function ( oSettings ) {
			$('.toggle-two').bootstrapToggle({
				on: 'Yes',
				off: 'No'
			});
			$('[data-toggle="tooltip"]').tooltip();   
			$('tr td:nth-child(1)').hide();
			$('tr th:nth-child(6)').hide();		
		},
  });


	function inactiveToggle(identifier){
	var id = $(identifier).data('catid');
	var status = $(identifier).data('isactive');
	if(status == 1){
		$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/brands/checkProductBrandId');?>",
		data: {'id':id},
		success: function(response){
		if(response){
		$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/brands/getAllbrands');?>",
		data:{'id':id},
		success: function(result){
			// console.log(response);
			// console.log(result);
				var stringHtml = "";
				$('#toggle_'+id).parent().addClass('on').removeClass('off');	
				stringHtml +='<form action="<?php echo base_url('seller/brands/updateProductBrand')?>" method="POST" id="catUpdate">';
				stringHtml += '<input type="hidden" name="old_brand_id" value="'+id+'" />'
				$(response).each(function(index,value){
						stringHtml += '<label id="productName">'+value.product_name+'</label>';
						stringHtml +='<input type="hidden" id="product_id" name="product_id[]" value="'+value.product_id+'">';
						stringHtml += '<div  class="col-sm-12">';
						stringHtml +='<select name = "brands_id[]" value="<?php echo set_value('brands_id[]'); ?>" class = "brands_id form-control" id="brands_id_'+index+'" data-placeholder="Select Brand"><option></option>';				
						$(result.data).each(function(index,value){
							stringHtml +="<option value = '"+value.brand_id+"' >"+value.brand_name+"</option>";
						});
						stringHtml += '</select></div>';	
				});
				stringHtml +='<div class="modal-footer d-flex justify-content-center"><button class="btn btn-success changeCat" id="changeCat">Change</button>';
				stringHtml +='</form>';	
				$('#modalCategory .modal-body fieldset').html(stringHtml);
				$('#modalCategory').modal('show');
				 $("#catUpdate").validate();
				// $("#subcategory_id_"+id).rules("add", "required");
				for(i=0;i<response.length;i++){
					$("#brands_id_"+i).rules("add", "required");
					}
				$("#catUpdate").validate();
				//  console.log(response);
				// $(identifier).data('isactive',1);
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
}

function blockToggle(identifier){
	var id = $(identifier).data('catid');
	var status = $("#block_"+id).attr("data-isactive");
	if(status == '1'){ status = 0;}
	else{status = 1;}
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/brands/brandActive');?>",
			data:{'id':id, 'status':status},
			success: function(result){
				$("#block_"+result.id).attr("data-isactive",result.status);
			}
		});
}


$(document).on("click","#changeCat",function(){
	var error = false;
	$(".brands_id").each(function(index,value){
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
$(document).on("change",".brands_id",function(){
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
		$(identifier).data('isactive',0);
		value = 0;
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
		url: "<?php echo base_url('seller/brands/delete');?>",
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
			<h5 class="modal-title">Delete Brand</h5>
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
<div class="modal fade" id="brandsmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<?php 
		$attr = array
		(
			"method"=>"POST",
			"class"=>"form-horizontal",
			"onsubmit"=>"validate(this,event);"
		);
		echo form_open_multipart("",$attr);
	?>
		<input type ="hidden" name = "brands_func" value = "" class = "brands_func"/>
		<input type ="hidden" name = "brands_count" value = "" class = "brands_count"/>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body">
				<div class="box-content brandsmodal-body">
					<fieldset></fieldset>
					<span class = "addrembtns">
						<span class = "btn btn-success addmorebtn">Add more</span>
						<span class = "btn btn-danger removemorebtn">Remove</span>
					</span>
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
      <div class="modal-header text-center">
        <p class="modal-title w-100 text-danger">Brand can not be deactivated, it is being used by product(s). Shift the product(s) to some other brand if you want to deactivate this brand.</p>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
			</div>
      <div class="modal-body mx-3" id="catModal">
        <div class="md-form mb-4" id="categoryChangeData">
					<fieldset></fieldset>
				</div>
      </div>
    </div>
  </div>
</div>