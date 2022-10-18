<script>
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
			$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/categories/hard_delete'); ?>/"+id,
			success: function(response){}
		});	
    });
}
var oTable;
oTable = $('.datatables').dataTable({
			dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn reqAdd',
					text: 'Add Requests',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/requests/add') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Category" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/requests/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
				$.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            <?php if($this->session->userdata['user_type'] == "1"){ ?>
			    "aoColumnDefs": [
                    {
                        "aTargets": [4],
                        "mRender": function ( data, type, full ) {
                            console.log(full);
                            if(full[4] == 0){
                                var approve_button = '<div class="btns_'+full[5]+'"><a class="btn btn-success approve_'+full[5]+'" data-rowid="'+full[5]+'" onclick="askApprove(this)" style="color:white; width:95px;">Approve</a>';
                                var decline_button = '&nbsp;&nbsp;<a class="btn btn-danger  decline_'+full[5]+'" data-rowid="'+full[5]+'" onclick="askDecline(this)" style="color:white; width:95px">Decline</a></div>';
                                var hidden_input = '<input type="hidden" id="input_'+full[5]+'"  />'; 
                                return approve_button+decline_button+hidden_input;
                            } else if(full[4] == 1) {
                                return '<h6 style="color:green">Approved</h6>';
                            } else if(full[4] == 2) {
                                return '<h6 style="color:red">Declined</h6>';
                            }
                        }
                    }    
                ],
            <?php } else { ?>
                "aoColumnDefs": [
                    {
                        "aTargets": [3],
                        "mRender": function ( data, type, full ) {
                            if(full[3] == 0){
                                console.log(full);
                                return '<h6 style="color:grey">Pending Approval</h6>';
                            } else if(full[3] == 1) {
                                return '<h6 style="color:green">Approved</h6>';
                            } else if(full[3] == 2) {
                                return '<h6 style="color:red">Declined</h6>';
                            }
                        },
                    }
                ],
            <?php }?>
			"aoColumns": [
                <?php if($this->session->userdata['user_type'] == "1"){ ?>
				    null, null, null, null, {"bSortable": false},
                <?php }else{?>
                    {"bSortable": false}, null, null, null,
                <?php }?>
            ],
});

function askApprove(identifier){
	$('#approval_modal').modal('show');
	$("#row_id").val($(identifier).data('rowid'));
	$("#value").val(1);
}
function askDecline(identifier){
	$('#approval_modal').modal('show');
	$('.box-content').html("Do you want to decline this request?");
	$('.modal-title').html('Request Decline')
	$("#row_id").val($(identifier).data('rowid'));
	$("#value").val(2);
	
}
$('#confirm_del').on('click',function(){
	var text = "";
	var rowid = $("#row_id").val();
	var value = $("#value").val();
    if(value == 1){
        text = "Approved"
    }
	else{
		text = "Declined";
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/requests/approveRequest');?>",
		data: {'row_id':rowid,'value' :value},
		success: function(response){
			console.log(response);
			$('#approval_modal').modal('hide');
            // $(".approve_"+rowid).remove()
            // $(".decline_"+rowid).remove()
            // $(".btns_"+rowid).remove()
            if(text == "Approved"){
				$('a[data-rowid='+rowid+']').parent().html('<h6 style="color:green">Approved</h6>');
            	$('a[data-rowid='+rowid+']').remove();
            } else {
				$('a[data-rowid='+rowid+']').parent().html('<h6 style="color:red">Declined</h6>');
            	$('a[data-rowid='+rowid+']').remove();
			}
		}
	});
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
