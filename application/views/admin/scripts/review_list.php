<script src="<?php echo assets_url('front/js/jquery.rateyo.min.js');?>"></script>
<link rel="stylesheet" href="<?php echo assets_url('front/css/jquery.rateyo.min.css'); ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css" />
<script>

var row = "";

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

var imageLink = "<?php echo image_url('brands/') ?>"; 
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;

/*var oTable;
oTable = $('.datatables').dataTable({
			dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn',
					text: 'Add Reviews',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/reviews/form') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Store and Product" },
            "aaSorting": [[0, "asc"], [1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/reviews/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
			},
			"aoColumns": [
                null, null,null,null,null,null,null
            ],
			"fnDrawCallback": function ( oSettings ) {
			$('tr td:nth-child(1)').hide();
		},
  });*/
  var oTable;
oTable = $('.datatables').dataTable({
			dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn',
					text: 'Add Reviews',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/reviews/form') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Store and Product"},
            "aaSorting": [[0, "asc"], [1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/reviews/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
			},
			"aoColumns": [
                null, null,null,null,null,null,null,null,null
            ],
			"fnDrawCallback": function ( oSettings ) {
			$('tr td:nth-child(1)').hide();
			$('tr th:nth-child(8)').hide();		
			
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

	//UPDATE MODAL
	$(document).on("click",".editReview",function(){

		$("#review_date").datetimepicker({
			dateFormat: 'yy-m-dd',
			timeFormat: 'HH:mm:ss',
		});
		row = $(this);
		id = row.data("id");
		reviewer_name = row.closest("tr").find("td:nth-child(4)").text();
		review = row.closest("tr").find("td:nth-child(6)").text();
		rating = row.closest("tr").find("td:nth-child(7)").text();
		date   = row.closest("tr").find("td:nth-child(8)").text();
		$("#reviewer_name").val(reviewer_name);
		$("#review_date").val(date);
		$("#review").val(review);
		$("#rateYo").rateYo("rating", rating);
		$("#review_id").val(id);
		$("#editModal").modal("show");
	});

	//UPDATE METHOD
	$(document).on("click","#updateBtn",function(){

		id = $("#review_id").val();
		name = $("#reviewer_name").val();
		review = $("#review").val();
		rating = $("#rateYo").rateYo("rating");
		date = $("#review_date").val();
		$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/reviews/edit');?>",
		data: {'review_id':id, 'name':name, 'review':review, 'rating':rating, 'date':date},
		success: function(response){
			$("#editModal").modal("hide");
			$('#review-status').removeClass('d-none');
			if(response.status == 1){
				$("#heading").text("Success!");
				$("#detail").text("Review Updated Successfully!");
				row.closest("tr").find("td:nth-child(4)").text(name);
				row.closest("tr").find("td:nth-child(6)").text(review);
				row.closest("tr").find("td:nth-child(7)").text(rating);
				row.closest("tr").find("td:nth-child(8)").text(date);
			}else{
				$("#review-status").removeClass('alert-success');
				$("#review-status").addClass('alert-danger');
				$("#heading").text("Error!");
				$("#detail").text("Review Updated Failed!");
			}
			setTimeout(function() { 
				$('#review-status').addClass('d-none');
			}, 2500);
		}
		});
	});

	function askDelete(id){
	$('#deleteModal').modal('show');
	$('#deleteBtn').click(function(){
		$.ajax({
				type: "GET",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url('seller/reviews/delete');?>/"+id,
				success: function(response){
					$("#deleteModal").modal("hide");
					$('#review-status').removeClass('d-none');
					if(response.code == 200){
						$("#heading").text("Success!");
						$("#detail").text(response.message);
						setTimeout(function(){
							window.location.href = pageLink+'?status='+response.status;
						}, 2000);
					}else{
						$("#review-status").removeClass('alert-success');
						$("#review-status").addClass('alert-danger');
						$("#heading").text("Error!");
						$("#detail").text(response.message);
					}
				}
		});		
	});
}

</script>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	<!-- Modal Content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Edit Review</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <!-- Modal Body-->
		  <div class="modal-body">
			<div class="">
				<input type="hidden" id="review_id" />
                <label>Reviewer: </lable><div><input class="form-control mb-1" type="text" id="reviewer_name"></div>
				<label>Date: </lable><div><input class="form-control mb-1" type="text" id="review_date"></div>
                <label>Review: </lable><div><textarea class="form-control mb-1" rows="3" cols="50" id="review"></textarea></div>
                <label>Rating: </lable><div><div class='text-center' id='rateYo' name = "ratYo"></div>
                <span class="rateYo_error d-none" id="rateYo_error"></span></div>
            </div>				
		  </div>
			<div class="modal-footer">
				<button class="btn btn-primary" id="updateBtn" >Update</button>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>


<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
	<!-- Modal Content-->
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Review</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <!-- Modal Body-->
		  <div class="modal-body">
			<div class="">
				<input type="hidden" id="review_id" />
				<p>Are you sure you want to delete this review?</p>
            </div>				
		  </div>
			<div class="modal-footer">
				<button class="btn btn-primary" id="deleteBtn" >Delete</button>
				<a href="#" class="btn" data-dismiss="modal">Cancel</a>
			</div>
		</div>
	</div>
</div>