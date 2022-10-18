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
	"requests" => "Requests",
	"message" => "Messages",
	"discount" => "Discounts",
	"returnpolicy" => "Return Policy",
	"shipping" => "Shipping",
	"location" => "Location",
	"purchaseinvoice"=>"Purchase Invoice",
	"Invoice_list" => "Invoices List",
	"carousel" => "Carousel",
	"areas" => "Areas",
	"quicksearchandadd" => "Quick Search And Create Products",
	"sales"=>"Sales",
	"pendingOrders" => "New Orders",
	"finalizeorder"=> "Finalize the Order",
	"sales_invoice" => "Sales Invoice",
	"selectimage" => "Select Image",
	"order_history" => "Orders Report",
	"inventory_view" => "Inventory List",
	"inventory_add" => "Add Inventory",
	"acceptedOrders_view" => "Completed Orders",
	"declinedOrders_view" => "Cancelled Orders",
	"cancel_orders_view" => "Cancel Order Requests",
	"1" => "",
	"0" => "",
	"prodAccessories" => "Product Accessories",
	"product_referrals" => "Product Referrals",
	"referrals" => "",
	"invite_people" => "Invite Referrals",
	"invite" => "Invite Peoples",
	"product_history" => "Product Report",
	"sales"	=> "Orders",
	"voucher_add" => "Add Voucher",
	"voucher" => "Voucher",
	"add" => "Add",
	"createshipping" => "create_shipping",
	"pending_view" => "Pending_view",
	"approved_view" => "Approved_view",
	"declined_view" => "Rejected_view",
);

$breadcrumb_link = $this->uri->segment_array();
// echo "<pre>"; print_r($breadcrumb_link); die();
if(isset($breadcrumb_link[2]) && $breadcrumb_link[2] == "referrals"){
	// $breadcrumb_link[1] = $breadcrumb_link[2];
	unset($breadcrumb_link[2]);
}
if(isset($breadcrumb_link[3]) && $breadcrumb_link[3] == "pending_view"){
	$breadcrumb_link[3] = ($_GET['v'] == "1") ? "approved_view" : (($_GET['v'] == "2") ? "declined_view" : $breadcrumb_link[3]);
}
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
		if($links == "referral"){$strlink .= "referral/referrals/";}else{
		$strlink = $strlink.$links."/";}
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

       
         