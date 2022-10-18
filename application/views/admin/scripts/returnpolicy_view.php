<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
	/*url = '<?php echo base_url('seller/returnpolicy/delete'); ?>/'+id;
	
	$.get(url, function(data,status){
		alert(status);
			if(status == "success"){
				oTable._fnReDraw();
			} else {
				alert('try again!');
			}
		});*/
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/returnpolicy/delete'); ?>/"+id,
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
					text: 'Add Return Policy',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/returnpolicy/add') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Return Policy" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
			'sAjaxSource': '<?php echo site_url('seller/returnpolicy/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
				
            },
			"columnDefs":[
				
				
			],
			"aoColumnDefs": [{
				
				"aTargets": [5],
				"mRender": function ( data, type, full, rowIndex) {
					if(full[7] == "<?php echo $this->session->userdata("userid"); ?>"){
						edit = '<?php echo base_url('seller/returnpolicy/update/')?>'+full[0];
						setDefault = '<?php echo base_url('seller/returnpolicy/isDefault/')?>'+full[0];

						delete_link = "<a class='actions' href='javascript:void(0);' onclick='askDelete("+full[0]+")' title='Delete'><i class='fa fa-trash'></i>Delete Item</a>";
						action = '<div class="btn-group text-left">'
						+'<button type="button" class="btn btn-secondary btn-xs btn-primary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>'
						+'<ul class="dropdown-menu except-prod  pull-right" role="menu">'
						+'<li class="pl-3"><a href="'+edit+'" class="actions"><i class="fa fa-edit"></i> Edit </a></li>'
						+'<li class="defualtCheck testClass'+full[5]+' pl-3" data-isdefualt="'+full[5]+'"><a href="'+setDefault+'" class="actions"><i class="fa fa-edit"></i> Set as Default </a></li>'
						+'<li class="divider"></li>'
						+'<li class="pl-3">'+delete_link+'</li>'
						+'</ul>'
						+'</div>';
						return action;
					}else{
						return "<h5>No Action</h5>";
					}
				}
			 },{
				"aTargets": [4],
				"mRender": function ( data, type, full ) {
					console.log(full);
					if(full[5] == 1){
						btnClass= 'success';
						btnText= 'Default';
					} else {
						btnClass= 'error';
						btnText= 'Not Default';
					}
					return '<span style="cursor: context-menu" class="btn '+btnClass+' p-0">'+btnText+'</span>';
				}
			},{
				"aTargets": [0],
				"mRender": function ( data, type, full ) {
					return full[1];
				}
			}
			,{
				"aTargets": [1],
				"mRender": function ( data, type, full ) {
					return full[2];
				}
			}
			,{
				"aTargets": [2],
				"mRender": function ( data, type, full ) {
					return full[3];
				}
			}
			,{
				"aTargets": [3],
				"mRender": function ( data, type, full ) {
					var text = "";
					if(full[4] == 1){
						text = "percent"
					} else {
						text = "fixed";
					}
					return text;
					
				}
			}
			],
			"aoColumns": [
                null, null,{"bSortable": false}
            ],
			"fnDrawCallback": function ( oSettings ) {
				$('.testClass1').remove();
			}
		});
	$("div.dt-buttons a").removeClass("dt-buttons");	
</script>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Return Policy</h5>
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