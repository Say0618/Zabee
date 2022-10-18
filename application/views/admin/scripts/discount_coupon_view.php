<script src="<?php echo assets_url('common/js/sugar.js'); ?>"></script>
<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
function askDelete(id){
	$('#voucher_id').val(id);
	$('#confirmation').modal('show');
	
}
function deleteItem(){
	var id = $('#voucher_id').val();
	var action_type = $('#action_type').val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/discount/voucher/delete'); ?>/"+id,
		success: function(response){
			setTimeout(function(){
				window.location.href = pageLink+'?status='+response.status;
			}, 200);
		}
	});	
}
var oTable;
oTable = $('.datatables').dataTable({
			dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn',
					text: 'Add Discount Coupon',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/discount/voucher_add') ?>';
						}        
				}
			],
			language: { searchPlaceholder: "Search Discount Coupon" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/discount/get_vouchers') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"aoColumns": [
                {"bSortable": false},{"bSortable": false},null,null,{"bSortable": false},null,{"bSortable": false},{"bSortable": false},{"bSortable": false},{"bSortable": false}
            ],
			"fnDrawCallback": function ( oSettings ) {
			$('tr td:nth-child(1)').remove();

			},
			'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                return nRow;
            },
			"aoColumnDefs": [{
                "aTargets": [3],
				"mRender": function ( data, type, full ) {
					valid_from = new Date(data * 1000);
					return formatAMPM(valid_from);
				}
			},{
                "aTargets": [4],
				"mRender": function ( data, type, full ) {
					valid_to = new Date(data * 1000);
					return formatAMPM(valid_to);
				}
			},]
			
        });	

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
			<h5 class="modal-title">Delete Coupon</h5>
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
			<a href="#" class="btn btn-primary" onclick="deleteItem()">Yes</a>
			<input type="hidden" value="" name="voucher_id" id="voucher_id"/>
			<input type="hidden" value="" name="action_type" id="action_type"/>
		  </div>
		</div>
	</div>
</div>