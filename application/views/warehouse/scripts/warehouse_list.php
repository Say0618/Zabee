<script>
function askDelete(id){
	$('#confirmation').modal('show');
	$('#confirmation .modal-title').text('Delete Address');
	$('.confirm_del').click(function(){
	url = '<?php echo base_url('seller/location/hard_delete'); ?>/'+id;
	$.get(url, function(data,status){
			if(status == "success"){
				oTable._fnReDraw();
				$('#confirmation').modal('hide');
			} else {
				alert('try again!');
			}
		});
	});
}
function isDefault(id){
	$('#confirmation').modal('show');
	$('#confirmation .modal-title').text('Default Address');
	$('.confirm_del').click(function(){
	url = '<?php echo base_url('seller/location/is_default'); ?>/'+id;
	$.get(url, function(data,status){
			if(status == "success"){
				oTable._fnReDraw();
				$('#confirmation').modal('hide');
			} else {
				alert('try again!');
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
					text: '<?php echo $this->lang->line('create_warehouse');?>',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('warehouse/create') ?>';
						}        
				}
			],
			"oLanguage": {
            "sSearch": "<?php echo $this->lang->line('search');?>:",
            "sLengthMenu": "<?php echo $this->lang->line('show');?> _MENU_ <?php echo $this->lang->line('entries');?>",
            "sInfo": "<?php echo $this->lang->line('showing');?> _START_ to _END_ <?php echo $this->lang->line('of');?> _TOTAL_ <?php echo $this->lang->line('entries');?>",
            "sPrevious": "<?php echo $this->lang->line('Previous');?>",
            "sNext": "<?php echo $this->lang->line('next');?>"
             },
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('warehouse/dashboard/warehouse_list') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"aoColumnDefs": [{ "bSearchable": false, "bVisible": false, "aTargets": [ 0 ] },{ "sClass":"text-center","aTargets": [ 10 ] }],
			
			"aoColumns": [
                {"bSortable": false},null,null, null,null,null,null,null,null,null,{"bSortable": false},
            ],
			"fnDrawCallback": function ( oSettings ) {
		},
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
		url: "<?php echo base_url('seller/location/delete');?>",
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
			<h5 class="modal-title">Delete Address</h5>
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