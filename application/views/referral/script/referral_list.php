<script>
id = "<?php echo (isset($_GET['userid']) && $_GET['userid'] != "")?$_GET['userid']:""; ?>";
var oTable;
oTable = $('.datatables').dataTable({
			language: { searchPlaceholder: "Search Invitation" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
			"aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo base_url('referral/referrals/referral_list') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': {"aoData":aoData, "userid":id}, 'success': fnCallback});
			},
			"aoColumnDefs": [
			{
				"aTargets": [0],
				"mRender": function ( data, type, full ) {
					return full[0];
				},
			},
			{
				"aTargets": [1],
				"mRender": function ( data, type, full ) {
					return full[1];
				},
			},
			{
				"aTargets": [2],
				"mRender": function ( data, type, full ) {
					return full[2];
				},
			},
			{
				"aTargets": [3],
				"mRender": function ( data, type, full ) {
					return full[3];
				},
			},
			{
				"aTargets": [4],
				"mRender": function ( data, type, full ) {
					var time = full[4].replace(/-/g,'/')
					var date = new Date(time+" UTC");
					date =  new Date(date.toString());
					return formatAMPM(date);
				},
			},
			{
				"aTargets": [5],
				"mRender": function ( data, type, full ) {
					var status ="";
					if(full[5] == 1){
						status ="proceed";
						color = "text-success";
					}else{
						status ="pending";
						color = "text-warning";
					}
					var checkbox = '<h4 class="'+color+'">'+status+'</h4>';
                    return checkbox;
				}
			},
			],
			"aoColumns": [
                null, null, null, null, null, null,
            ],
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