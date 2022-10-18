<script>
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/import_csv/delete'); ?>/"+id,
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
					text: 'Add Import CSV',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/import_csv/add') ?>';
					}        
				}
			],
            "aaSorting": [[0, "asc"], [1, "asc"]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/import_csv/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
			},
			"aoColumnDefs": [
			{
				"aTargets": [5],
				"mRender": function ( data, type, full ) {
					statusClass = 'info';
					statusText = 'Pending';
					switch(parseInt(data)){
						case 1:
							statusClass = 'dark';
							statusText = 'In Progress';
						break;
						case 2:
							statusClass = 'success';
							statusText = 'Completed';
						break;
						case 3:
							statusClass = 'danger';
							statusText = 'Paused';
						break;
					}
					return '<span class="alert  alert-'+statusClass+'">'+statusText+'</span>';
				}
			},{
				"aTargets": [6],
				"mRender": function ( data, type, full ) {
					return full[7];
				}
			}],
			"aoColumns": [
                {"bSortable": false}, null, null, null, null, null, {"bSortable": false},
            ]
        });
</script>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Banner?</h5>
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