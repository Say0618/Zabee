<!DOCTYPE html>
<html lang= "en">
	<head>
    <style>
html,body{width:100%;}
body{overflow:visible;}

@media print {
html,body{width:100%;}
body{overflow:visible;}
                 #filtered_table{
                  width: 100%;  
                  height: 100%;
                 }
               .printableArea{
                 display: block;
                 width: auto;
                 height: auto;
                 overflow: visible;  
                }
               }
               table,th, td{border:1px solid #000;border-collapse:collapse;}
               td{padding:2px - 5px}
</style>
	</head>
	<body class="page_<?php echo $page_name; ?>">  
			<div class="page-wrapper">
				<section> 
					<?php $this->load->view('admin/'.$page_name); ?>
				</section>	
		</div>
	</body>
</html>
<style>
.page-wrapper {
    position: relative;
    min-height: calc(100vh - 70px);
}
.table-responsive{
    display:block !important;
}
</style>