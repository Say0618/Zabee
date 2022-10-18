          <!-- Page Footer-->
        </div>
		<footer class="main-footer">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-6">
                  <p class="text-left">&copy; <a href="<?php echo COMPANYURL; ?>" target="_blank"><?php echo COMPANYNAME; ?></a> 2017</p>
                </div>
                <div class="col-sm-6 text-right">
                 
				  <p class="pull-right"><?php echo $this->lang->line('powered_by');?>: <a href="http://smartmicros.com/">Smartmicros</a></p>
                </div>
              </div>
            </div>
          </footer>
      </div>
    </div>
<!-- Javascript files-->

	<script src="<?php echo media_url(); ?>assets/common/js/jquery-3.3.1.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/common/vendor/popper.js/umd/popper.min.js"> </script>
	<script src="<?php echo media_url(); ?>assets/common/bootstrap/js/bootstrap.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/common/bootstrap/js/bootstrap-toggle.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/common/vendor/jquery.cookie/jquery.cookie.js"> </script>
	<script src="<?php echo media_url('assets/chat/js/chat.js');?>"></script>
    <script src="<?php echo media_url(); ?>assets/plugins/chart.js/Chart.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/plugins/form-select2/select2.min.js"></script>
    <script src="<?php echo media_url(); ?>assets/plugins/datatable/datatable.js"></script>
	<script src="<?php echo media_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
	<script src="<?php echo media_url(); ?>assets/common/js/jquery.ui.timepicker.js"></script>
	<script src="<?php echo media_url(); ?>assets/common/js/jquery-ui.min.js"></script> 
	<!-- Main File-->
	<script type="text/javascript">
		$('#notifications').click(function(){
			var isRead = parseInt($('#notifications_read').val());
			if(isRead == 0){
				setTimeout(function(){
					var notification_id = $('#notification_id').val();
					var usertype = $('#notification_usertype').val();
					$.ajax({
						url: "<?php echo base_url('seller_notifications');?>",
						data: {userid:'<?php echo $this->session->zabeeWarehouseData['userid']; ?>', usertype:usertype,notification_id:notification_id},
						dataType: "json",
						type: "POST",
						success: function( data ) {
							$('#notifications_read').val(1);
						}
					});
				}, 1500);
			}
		});
		function formatUTCAMPM(date) {
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0'+minutes : minutes;
		var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var strTime = months[date.getMonth()]+'-'+date.getDate()+'-'+date.getFullYear()+' '+hours + ':' + minutes +''+ ampm;
		return strTime;
	}
	</script>
	