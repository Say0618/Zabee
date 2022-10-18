<!DOCTYPE html>
<html lang = "en">
	<head>
		<?php $this->load->view('front/head'); $temp = $page_name.".css";?>
		<?php if($hasStyle){ echo '<link rel="stylesheet" type="text/css" href="'.assets_url("front/css/".$temp).'">';} ?>
	</head>
	<body class="page_<?php echo $page_name; ?>">
		<main role="main">
		
		<?php $this->load->view('front/header'); ?>
		<div class="content_<?php echo $page_name?>" style="position: relative;">
		<div id="loading-background" class="d-none"></div>
			<div class="boxes mx-auto d-none">
				<div class="box">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
				<div class="box">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
				<div class="box">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
				<div class="box">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
			</div>
			<?php $this->load->view('front/'.$page_name); ?>
			<?php if($newsletter){$this->load->view('front/newsletter'); }?>
		 </div>
		</main>

		<?php $this->load->view('front/footer'); ?>
		<?php $this->load->view('front/script/footer'); ?>

		<?php if($hasScript){$this->load->view('front/script/'.$page_name); } ?>
		<div id="notification" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Notification</h4>
						</div>
						<div class="modal-body text-center"><p></p></div>
						<div class="modal-footer">
							<div class="form-group">
								<button type="button" class="btn btn-defualt" data-dismiss="modal" aria-label="Close">Close</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
<?php
$invalid=$this->session->flashdata('invalid');

if($invalid){ ?>
  <script> $('#invalid').modal('show'); </script>
<?php } 
$invalid_email=$this->session->flashdata('invalid_email');
if($invalid_email)
{ ?>
  <script> $('#invalidEmailModal').modal('show');</script>
<?php }
$invalid_code=$this->session->flashdata('invalid_code');
if($invalid_code)
{ ?>
  <script> $('#invalidCodeModal').modal('show');</script>
<?php }
$passwordChanged=$this->session->flashdata('passwordChanged');
if($passwordChanged)
{ ?>
  <script> $('#passwordChanged').modal('show');</script>
<?php }
$add_password=$this->session->flashdata('add_password');
if($add_password){ ?>
  <script> $('#add_password').modal('show');</script>
<?php }
if(isset($not_same)){ ?>
  <script>
   $('#add_password').load('<?php echo base_url("home/set_password_view")?>', {el:"<?php echo $el;?>",not_same:"1"}, function(){
	$("#add_password").modal('show');
});
   </script>
<?php } ?>