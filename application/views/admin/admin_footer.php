          <!-- Page Footer-->
        </div>
	<!-- 	<footer class="main-footer">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-6">
                  <p class="text-left">&copy; <a href="<?php //echo COMPANYURL; ?>" target="_blank"><?php //echo COMPANYNAME; ?></a> 2017</p>
                </div>
                <div class="col-sm-6 text-right">
                 
				  <p class="pull-right">Powered by: <a href="http://smartmicros.com/">Smartmicros</a></p>
                </div>
              </div>
            </div>
          </footer> -->
      </div>
    </div>
<!--     <!-- Javascript files-->
	<script src="<?php echo assets_url('common/js/jquery-3.3.1.min.js'); ?>"></script>
	<script src="<?php echo assets_url('common/vendor/popper.js/umd/popper.min.js'); ?>"> </script>
	<script src="<?php echo assets_url('common/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<script src="<?php echo assets_url('common/bootstrap/js/bootstrap-toggle.min.js'); ?>"></script>
	<script src="<?php echo assets_url('common/vendor/jquery.cookie/jquery.cookie.js'); ?>"> </script>
	<script src="<?php echo assets_url('chat/js/chat.js');?>"></script>
    <script src="<?php echo assets_url('plugins/chart.js/Chart.min.js'); ?>"></script>
	<script src="<?php echo assets_url('plugins/jquery-validation/jquery.validate.min.js'); ?>"></script>
	<script src="<?php echo assets_url('plugins/form-select2/select2.min.js'); ?>"></script>
	<script src="<?php echo assets_url('plugins/datatable/datatable.js'); ?>"></script>
	<script src="<?php echo assets_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
	<script src="<?php echo assets_url('plugins/datatables/dataTables.buttons.min.js'); ?>"></script>
	<script src="<?php echo assets_url('plugins/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
	<script src="<?php echo assets_url('common/js/jquery.ui.timepicker.js'); ?>"></script>
	<script src="<?php echo assets_url('common/js/jquery-ui.min.js'); ?>"></script> 
	<script src="<?php echo assets_url('plugins/fileinput/js/plugins/sortable.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo assets_url('plugins/fileinput/js/plugins/purify.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo assets_url('plugins/fileinput/js/plugins/piexif.min.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo assets_url('common/bootstrap/js/bootstrap.bundle.min.js'); ?>"> </script>
	<script src="<?php echo assets_url('plugins/fileinput/js/fileinput.js'); ?>" type="text/javascript"></script>
	<script src="<?php echo assets_url('plugins/fileinput/themes/fas/theme.min.js'); ?>" type="text/javascript"></script>
	<script type="text/javascript" src="<?php echo assets_url('front/js/scripts.js'); ?>"></script>
    <!-- Main File-->
	<script type="text/javascript">
	$(document).ready(function(){
		<?php if($this->session->userdata['user_type'] == "1"){ ?>
			$('.reqAdd').hide();
		<?php }?>
		$("input[type='text']").attr("maxLength","255");
		$("input[type='email']").attr("maxLength","255");
		var res = String(window.location.href).split("/");
		res.forEach(myChecker);
		function myChecker(item, index) {
			if(item == "editedcategories" || item == "editedbrands" || item == "editedsubcategories" || item == "variant_add"){
				$(".breadcrumb-item").last().text($("#focusedInput").val())
				$(".breadcrumb-item").last().css("color", "#379392")
			}
			if(item == "variant_add"){
				var res = $(".variantName").text().split(" ");
				$(".breadcrumb-item").last().text(res[1])
				$(".breadcrumb-item").last().css("color", "#379392")
			}
		}
		
	});
	$("input[type='text']").on("keyup",function() {
		var maxLength = $(this).attr("maxlength");
		if(maxLength == $(this).val().length) {
			$('<p class="limit_error" id="limit_error_'+$(this).attr("id")+'">character limit reached.</p>').insertAfter("#"+$(this).attr("id"));
		} else {
			$(".limit_error").hide();
		}
		$('[id]').each(function () {
			$('[id="limit_error_'+$(this).attr("id")+'"]:gt(0)').remove();
		});
		if($('.return-error').text() == "Category name already exists." || $('.return-error').text() == "Brand name already exists." || $('.return-error').text() == "Sub-Category name already exists." || $('.return-error').text() == "Variant name already exists."){
			$('.return-error').hide()
		}
	});

	$('#notifications').click(function(){
		var isRead = parseInt($('#notifications_read').val());
		if(isRead == 0){
			setTimeout(function(){
				var notification_id = $('#notification_id').val();
				var usertype = $('#notification_usertype').val();
				$.ajax({
					url: "<?php echo base_url('seller_notifications');?>",
					data: {userid:'<?php echo $_SESSION['userid']; ?>', usertype:usertype,notification_id:notification_id},
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
		var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		var strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
		return strTime;
	}
	</script>

<script>
	$(document).ready(function(){
		$(document).ajaxStart(function(){
			$( "#loading-background" ).removeClass("d-none");
			$( ".backend-boxes" ).removeClass("d-none");
		});

		$(document).ajaxStop(function(){
			$( "#loading-background" ).addClass("d-none");
			$( ".backend-boxes" ).addClass("d-none");
		});
	});
</script>
	