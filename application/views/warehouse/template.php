<!DOCTYPE html>
<html lang= "en">
	<head>
		<?php $this->load->view('warehouse/head'); ?>
	</head>
	<body class="page_<?php echo $page_name; ?>">  
		<?php $this->load->view('warehouse/header'); ?>
		<?php if($this->session->zabeeWarehouseData["warehouse_id"] && $this->session->zabeeWarehouseData['warehouse_title'] != "" ){
		 $this->load->view('warehouse/leftmenu'); ?>
		<?php } ?>
			<div class="<?php echo (isset($this->session->zabeeWarehouseData["warehouse_id"]) && isset($this->session->zabeeWarehouseData['warehouse_title']))?"content-inner":"" ?>">
			<?php $this->load->view('warehouse/breadcrumb'); ?>
			<div class="col-sm-12">
				<section class="forms" style="padding: 22px 0;"> 
				<div class="container-fluid">
						<div class="card col-sm-12">
							<?php $this->load->view('warehouse/'.$page_name); ?>
						</div>	
					</div>
				</section>	
			</div>
		</div>
		<?php  $this->load->view('warehouse/footer'); ?>
		<?php if($isScript){$this->load->view('warehouse/scripts/'.$page_name);} ?>
	</body>
</html>