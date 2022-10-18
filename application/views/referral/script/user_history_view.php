<script>
var oTable;
oTable = $('.datatables').dataTable({
			language: { searchPlaceholder: "Search Referrals" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('referral/referrals/getdata') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"aoColumnDefs": [
			{
				"aTargets": [0],
				"mRender": function ( data, type, full ) {
					return full[0]+" "+full[1];
				},
			},
			{
				"aTargets": [1],
				"mRender": function ( data, type, full ) {
					return full[2];
				},
			},
			{
				"aTargets": [2],
				"mRender": function ( data, type, full ) {
					if(full[4] > 0){
						var link = "<?php echo base_url().'referral/referrals/index?userid=';?>"+full[3];
						return '<a href="'+link+'">Show Referrals ('+full[4]+')</a>';
					}else{
						return "No referrals"
					}
				},
			},
			{
				"aTargets": [3],
				"mRender": function ( data, type, full ) {
					if(full[5] > 0){
						var link = "<?php echo base_url().'referral/referrals/invite_list?userid=';?>"+full[3];
						return '<a href="'+link+'">Show Referrals ('+full[5]+')</a>';
					}else{
						return "<span class='text-danger'>No referrals</span>"
					}
				},
			},
			],
			"aoColumns": [
                null, null, null, null
            ],
		});
</script>