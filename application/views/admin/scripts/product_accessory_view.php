<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/product/prod_acc_delete'); ?>/"+id,
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
					className: 'btn btn-primary dts_button',
					text: 'Add Product Accessories',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/product/accessories') ?>';
					}        
				}
			],
            "aaSorting": [[0, "asc"], [1, "asc"]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/product/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
			},
			"aoColumnDefs": [
				{
					"render": function ( data, type, row ) {
						return row[0];
					},
					"targets": 0
				},
				{
					"render": function ( data, type, row ) {
						data = row[3].split(',');
						accessories = "";
						data.forEach(function(value) {
							accessories += value+"<br>"
						});
						return accessories;
					},
					"targets": 1
				},
				{
					"render": function ( data, type, row ) {
						return row[4];
					},
					"targets": 2
				},
			],
			"aoColumns": [
                {"bSortable": false}, null, {"bSortable": false},
            ],
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
		url: "<?php echo base_url('seller/product/productAccessoryDelete');?>",
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