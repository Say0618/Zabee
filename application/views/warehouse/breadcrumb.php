<?php
//echo "<pre>";print_r($this->uri->segment_array());die();
$bread_crumbs = array
(
	"seller"=>"Seller",
	"dashboard"=>"Dashboard",
	"useraccess"=>"User Types",
	"adminusers"=>"Backend Users",
	"users"=>"Front-end Users (Customers)",
	"categories"=>"Categories",
	"subcategories"=>"Sub-Categories",
	"brands" =>"Brands",
	"product"=>"Products",
	"vendors"=>"Vendors",
	"purchaseinvoice"=>"Purchase Invoice",
	"Invoice_list" => "Invoices List",
	"carousel" => "Carousel",
	"areas" => "Areas",
	"quicksearchandadd" => "Quick Search And Create Products",
	"sales"=>"Sales",
	"pendingOrders" => "Pending Orders",
	"finalizeorder"=> "Finalize the Order",
	"sales_invoice" => "Sales Invoice",
	"selectimage" => "Select Image",
	"acceptedOrders_view" => "Accepted Orders",
	"declinedOrders_view" => "Declined Orders",
	"pendingorders_view" => "Pending Orders",
	"1" => "",
	"0" => "",
	"prodAccessories" => "Product Accessories",
);

$breadcrumb_link = $this->uri->segment_array();
//echo "<pre>"; print_r($breadcrumb_link);
$strlink = base_url();
echo '<header class="page-header">
			<div class="container-fluid">
			  <h2 class="no-margin-bottom">'.$Breadcrumb_name.'</h2>
			</div>
		</header>
		<div class="breadcrumb-holder container-fluid">
			<ul class="breadcrumb">';
if(array_key_exists("3", $breadcrumb_link) && $breadcrumb_link[3] == "selectimage"){
		echo '<li class="breadcrumb-item"><a href="'.base_url('seller/dashboard').'">Seller</a></li>';
		echo '<li class="breadcrumb-item"><a href="'.base_url('seller/product').'">Product</a></li>';
		echo '<li class="breadcrumb-item"><a href="">Select Image</a></li>';
}else if(array_key_exists("3", $breadcrumb_link) && $breadcrumb_link[3] == "accessories"){
		echo '<li class="breadcrumb-item"><a href="'.base_url('seller/dashboard').'">Seller</a></li>';
		echo '<li class="breadcrumb-item"><a href="'.base_url('seller/product').'">Product</a></li>';
		echo '<li class="breadcrumb-item"><a href="'.base_url('seller/product/prodAccessories').'">Product Accessories</a></li>';
		echo '<li class="breadcrumb-item"><a href="">Accessories</a></li>';
} else {
	foreach($breadcrumb_link as $links){
		$strlink = $strlink.$links."/";
		$links = (isset($bread_crumbs[$links]))? $bread_crumbs[$links] : $links;
		//echo "<pre>";print_r($links);
		echo '<li class="breadcrumb-item"><a href="'.$strlink.'">'.$links.'</a></li>';
	}
}
echo '	</ul>
		</div>';
//echo "<pre>";print_r($strlink[45]);
?>

	<!--<li>
			<a href="'.$strlink.'">'.$links.'</a>
		</li>-->

       
         