<!DOCTYPE html>
<html lang= "en">
	<head>
		<?php $this->load->view('admin/admin_head'); ?>
	</head>
	<body class="page_<?php echo $page_name; ?>">
		<?php $this->load->view('admin/admin_header'); ?>
		<?php if($_SESSION['store_id'] != "" && $_SESSION['store_name'] != "" && (isset($_SESSION["store_status"]) && $_SESSION['store_status'] == "1")){
		 $this->load->view('admin/admin_leftmenu'); } ?>
		 	<div id="loading-background" class="d-none"></div>
			<div class="<?php echo (isset($_SESSION['store_id']) && isset($_SESSION['store_name']) && (isset($_SESSION["store_status"]) && $_SESSION['store_status'] == "1"))?"content-inner":"" ?>">
			<?php $this->load->view('admin/admin_breadcrumb'); ?>
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
						<div class="<?php ($page_name !="dashboard")?"card":""?> col-sm-12">
							<?php $this->load->view('admin/'.$page_name); ?>
						</div>	
					</div>
				</section>	
			</div>
		</div>
		<?php  $this->load->view('admin/admin_footer'); ?>
		<?php if($isScript){$this->load->view('admin/scripts/'.$page_name);} ?>
	</body>
</html>