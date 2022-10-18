<!DOCTYPE html>
<html lang= "en">
	<head>
		<?php $this->load->view('./admin/admin_head'); ?>
	</head>
	<body class="page_<?php echo $page_name; ?>">
		<?php $this->load->view('referral/referral_header');
			  $this->load->view('referral/referral_sidebar'); ?>
		 	<div id="loading-background" class="d-none"></div>
			<div class="content-inner">
			<?php $this->load->view('./admin/admin_breadcrumb'); ?>
			<div class="col-sm-12">
				<section class="forms" style="padding: 22px 0;"> 
					<div class="container-fluid">
						<div class="backend-boxes mx-auto d-none">
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
						<div class="card col-sm-12">
							<?php $this->load->view('referral/'.$page_name); ?>
						</div>	
					</div>
				</section>	
			</div>
		</div>
		<?php  $this->load->view('./admin/admin_footer'); ?>
		<?php if($isScript){$this->load->view('referral/script/'.$page_name);} ?>
	</body>
</html>