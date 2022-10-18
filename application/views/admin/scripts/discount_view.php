<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
	$('#confirmation').modal('show');
	$('.confirm_del').click(function(){
	/*url = '<?php echo base_url('seller/discount/hard_delete'); ?>/'+id;
	$.get(url, function(data,status){
			if(status == "success"){
				oTable._fnReDraw();
				$('#confirmation').modal('hide');
			} else {
				alert('try again!');
			}
		});*/
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('seller/discount/hard_delete'); ?>/"+id,
			success: function(response){
				setTimeout(function(){
					window.location.href = pageLink+'?status='+response.status;
				}, 200);
			}
		});	
	});
}
function askDiscount(id){
	$('#discountall').modal('show');
	$('.confirm_del').click(function(){
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			data:{'id':id},
			async:true,
			url: "<?php echo base_url('seller/discount/apply_all_products'); ?>/"+id,
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
					text: 'Add Discount Policy',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/discount/add') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Discount Policy" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/discount/get') ?>',
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
						date = full[2].replace(/-/g,'/')
						date = new Date(date+" UTC");
						date = new Date(date.toString());
						return formatAMPM(date);
						// return "abcd";
					}
				},
				{
					"aTargets": [3],
					"mRender": function ( data, type, full ) {
						date = full[3].replace(/-/g,'/')
						date = new Date(date+" UTC");
						date = new Date(date.toString());
						return formatAMPM(date);
						// return "abcd";
					}
				},
				{
					"aTargets": [6],
					"mRender": function ( data, type, full ) {
						if(full[7] != '1'){
							var checked ="";
							if(full[6] == 1){
								checked ="checked";
							}
							var checkbox = '<input type="checkbox" class="toggle-two toggle-css" data-catid="'+full[0]+'" data-isactive="'+full[6]+'" id="category'+full[6]+'"  onchange="updatestatus(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '+checked+'/>'
						}else{
							var checkbox = (full[6] == '1')?'<h5 class="pl-3 text-success">YES</h5>':'<h5 class="pl-3 text-danger">NO</h5>';
						}
						return checkbox;
						// return "abcd";
					}
				},
				{
					"aTargets": [7],
					"mRender": function ( data, type, full ) {

						if(full[7] != '1'){
							site = "<?php echo site_url('seller/discount/apply_all_products/'); ?>"+full[0];
							discount_link ="<a class='actions' href='javascript:void(0);' onclick='askDiscount("+full[0]+")' title='discount to all products'><i class='fa fa-asterisk'></i> apply to all</a>";
							
							delete_link = "<a class='actions' href='javascript:void(0);' onclick='askDelete("+full[0]+")' title='Delete'><i class='fa fa-trash'></i>Delete</a>";
							
							action = '<div><div class="btn-group text-left">'
									+'<button type="button" class="btn btn-secondary" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>'
									+'<ul class="dropdown-menu except-prod pull-right" role="menu">'
									+'<li class="pl-2 pt-1"><a class="actions" href="<?php echo base_url('seller/discount/update/')?>'+full[0]+'"><i class="fa fa-edit"></i>Edit</a></li>';
							action += '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>'
									+'<li class="pl-2 pt-1">' + delete_link + '</li> <li class="pl-2 pt-1">'+discount_link+'</li>'
										+'</ul>'
									+'</div></div>';	
						}else{
							action = "<h5 class=''>No Action</h5>";
						}
						return action;
					},
				},
			],
			"aoColumns": [
                {"bSortable": false},null,null,null,null,null,null,{"bSortable": false}
            ],
			"fnDrawCallback": function ( oSettings ) {
			$('.toggle-two').bootstrapToggle({
				on: 'Yes',
				off: 'No'
			});
			$('[data-toggle="tooltip"]').tooltip();   
			$('tr td:nth-child(1)').hide();
			$('tr th:nth-child(8)').hide();
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
		url: "<?php echo base_url('seller/discount/delete');?>",
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

function formatAMPM(date) {
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0'+minutes : minutes;
		var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		var strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
		return strTime;
	}
</script>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Policy</h5>
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

<div class="modal fade" id="discountall" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Apply this discount to all products</h5>
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