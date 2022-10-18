<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
    $('#confirmation').modal('show');
	$('.confirm_del').click(function(){
	/*url = '<?php echo base_url('seller/variantcategories/hard_delete'); ?>/'+id;
	$.get(url, function(data,status){
		console.log(data);
		if(data == "true"){
				oTable._fnReDraw();
				$('#confirmation').modal('hide');
			} else {
				$('#confirmation').modal('hide');
				alert('product is there for this variant category');
			}*/
			$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/variantcategories/hard_delete'); ?>/"+id,
				success: function(response){
					setTimeout(function(){
						window.location.href = pageLink+'?status='+response.status;
					}, 200);
				}, 
				error: function(status,b,c){
					console.log(status);
					console.log(b);
					console.log(c);
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
					text: 'Add Variant Category',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/variantcategories/createcategories') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Variant Category" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/variantcategories/get') ?>',
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
					return '<a class="btn btn-dark" href="<?php echo base_url("seller/Variantcategories/listview/"); ?>'+data+'" class="btn btn-sm">Click here</a>';
				},
			},
			{
				"aTargets": [3],
				"mRender": function ( data, type, full ) {
					var checked ="";
					if(full[3] == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" data-catid="'+full[0]+'" data-isactive="'+full[3]+'" id="category'+full[3]+'"  onchange="updatestatus(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
                    return checkbox;
				}
			},
			],
			"aoColumns": [
              {"bSortable": false},  null, null, null, {"bSortable": false}
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
	var status = $(identifier).data('isactive');
	var value = 0;
	if(status == 1){
		value = 0;
		$(identifier).data('isactive',0);
	} 
	else if(status == 0){
		value = 1;
		$(identifier).data('isactive',1);
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/variantcategories/delete');?>",
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