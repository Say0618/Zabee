<script>
var imageLink = "<?php echo base_url('uploads/special_offer/') ?>"; 
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id, image, position, status){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/offers/hard_delete_offer'); ?>/"+id,
			data: {'name':image, "position":position, "status":status},
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
			dom: 'Blrtip',
			buttons: [
				{
					className: 'btn btn-primary',
					text: 'Add Offers',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/offers/form') ?>';
					}        
				}
			],
            //"aaSorting": [[0, "asc"], [1, "asc"]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/offers/offers_get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
			},
			"aoColumnDefs": [
            {
				"aTargets": [2],
				"mRender": function ( data, type, full ) {
					// console.log(full[3]);
					return '<img data-img='+full[2]+' src="<?php echo image_url("special_offer/")?>'+full[2]+'" alt="" style="width:80px;" />';
				},
			},{
				"aTargets": [4],
				"mRender": function ( data, type, full ) {
					var checked ="";
					if(full[4] == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" data-pic="'+full[2]+'" data-catid="'+full[0]+'" data-position="'+full[3]+'" data-isactive="'+full[4]+'" id="category'+full[0]+'"  onchange="updatestatus(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
                    return checkbox;
				}
			},
			],
			"aoColumns": [
                {"bSortable": false}, null, null, null, {"bSortable": false}, {"bSortable": false},
            ],
			"fnDrawCallback": function ( oSettings ) {
				$('.toggle-two').bootstrapToggle({
					on: 'Yes',
					off: 'No'
				});
				$('[data-toggle="tooltip"]').tooltip();
				$('tr td:nth-child(1)').hide();
				$('tr th:nth-child(5)').hide();				
			},
        });
function updatestatus(identifier){  
	var id = $(identifier).data('catid');
	var status = $("#category"+id).attr('data-isactive');
	var position = $(identifier).data('position');
	var pic = $(identifier).data('pic');
	var value = 0;
	if(status == 1){
		$(identifier).data('isactive',"0");
		value = 0;
	} else {
		$(identifier).data('isactive',"1");
		value = 1;	
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/offers/offer_delete');?>",
		data: {'id':id, 'value':value, 'position':position},
		success: function(response){
			$("#category"+id).attr("data-isactive", value);
			$("#delete-"+id).attr("onclick","askDelete("+'"'+id+'"'+","+'"'+pic+'"'+","+'"'+position+'"'+","+'"'+value+'"'+")")
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
			<h5 class="modal-title">Delete Offer?</h5>
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