<div class="page-content d-flex align-items-stretch"> 
    <nav class="side-navbar">
      <a href="<?php echo base_url("seller/dashboard/store")?>">
          <div class="sidebar-header d-flex align-items-center">
            <?php $picture = $this->session->userdata('store_pic') != "" ? $this->session->userdata('store_pic') : 'default.jpg'; ?>
            <div class="avatar"><img src="<?php echo $this->config->item("store_logo_path").$picture; ?>" alt="..." class="img-fluid rounded-circle avatar" /></div>
            <div class="title">
              <h1 class="h4"><?php echo stripslashes((isset($_SESSION['store_name']))?$_SESSION['store_name']:"Store name");?></h1>
              <!-- <p><?php echo stripslashes((isset($_SESSION['store_name']))?$_SESSION['store_name']:"Store name");?></p> -->
            </div>
          </div>
      </a>
     <?php if($this->session->userdata('user_type') == 1){?>
        <ul class="list-unstyled">
            <li><a href="#viewCategories" aria-expanded="false" data-toggle="collapse"><i class="far fa-chart-bar"></i>&nbsp;<strong><?php echo $this->lang->line('categories');?></strong></a>
              <ul id="viewCategories" class="collapse list-unstyled ">
                <li><a href="<?php echo base_url()."seller/categories"?>">&nbsp;<strong><?php echo $this->lang->line('categories_list');?></strong></a></li>
                <li><a href="<?php echo base_url()."seller/subcategories"?>">&nbsp;<strong><?php echo $this->lang->line('sub-categories_list');?></strong></a></li>
              </ul> 
            </li>
            <li class=""><a href="<?php echo base_url()."seller/product/hubxProductList"; ?>"><i class="fas fa-undo"></i>&nbsp;<strong>Hubx Products</strong></a></li>
            <li><a href="#viewProducts" aria-expanded="false" data-toggle="collapse"><i class="fas fa-briefcase"></i>&nbsp;<strong><?php echo $this->lang->line('product');?></strong></a>
              <ul id="viewProducts" class="collapse list-unstyled ">
                <li><a href="<?php echo base_url()."seller/product"?>"><strong><?php echo $this->lang->line('product_list');?></strong></a></li> 
                <li><a href="<?php echo base_url()."seller/product/create"?>"><strong><?php echo $this->lang->line('create_product');?></strong></a></li>
				        <li><a href="<?php echo base_url()."seller/product/pending_view"?>"><strong><?php echo $this->lang->line('pending_products');?></strong><?php if($this->pendingProductData > 0){?><div class="button__badge"><?php echo $this->pendingProductData; ?></div><?php }?></a></li>
                <li><a href="<?php echo base_url()."seller/product/prodAccessories"?>"><strong><?php echo $this->lang->line('pdt_accessories');?></strong></a></li>
                <li><a href="<?php echo base_url()."seller/product/product_history"?>"><strong><?php echo $this->lang->line('product_report');?></strong></a></li> 
              </ul>
            </li>
            <li><a href="<?php echo base_url()."seller/brands"?>"><i class="fas fa-star"></i>&nbsp;<strong><?php echo $this->lang->line('brands');?></strong></a></li>
            <li><a href="<?php echo base_url()."seller/offers"?>"><i class="fas fa-star"></i>&nbsp;<strong>Special Offers</strong></a></li>
            <li class=""><a href="<?php echo base_url()."seller/product/inventory_view"?>"><i class="fas fa-briefcase"></i>&nbsp;<strong><?php echo $this->lang->line('inventory');?></strong></span></a></li>
            <li><a href="#viewSales" aria-expanded="false" data-toggle="collapse"><i class="far fa-money-bill-alt"></i>&nbsp;<strong><?php echo $this->lang->line('order');?></strong></a>
              <ul id="viewSales" class="collapse list-unstyled ">
                <li><a href="<?php echo base_url()."seller/sales/orderList"?>">&nbsp;<strong>Order List</strong></a></li>
                <li><a href="<?php echo base_url()."seller/sales/failedOrders"?>">&nbsp;<strong>Failed Order List</strong></a></li>
                <li><a href="<?php echo base_url()."seller/sales/order_history"?>">&nbsp;<strong><?php echo $this->lang->line('orders_report');?></strong></a></li>
              </ul>
            </li>
            <?php /* <li><a href="<?php echo base_url()."seller/sales"?>"><i class="far fa-money-bill-alt"></i><strong>Sales</strong></a></li> 
            <li><a href="<?php echo base_url()."seller/sales/order_history"?>"><strong>Orders Report</strong></a></li> */?>
            <li><a href="<?php echo base_url()."seller/variantcategories"?>"><i class="fas fa-bars"></i>&nbsp;<strong><?php echo $this->lang->line('variant_categories');?></strong></a></li>
            <?php /*<li class=""><a href="<?php echo base_url()."seller/editprofile"; ?>"><i class="fas fa-user-alt"></i><span class="">&nbsp;<strong>Profile</strong></span></a></li> */ ?>
            <li class=""><a href="<?php echo base_url()."seller/location"; ?>"><i class="fas fa-location-arrow"></i>&nbsp;<strong><?php echo $this->lang->line('location');?></strong></a></li> 
            <li class=""><a href="<?php echo base_url()."seller/banner"; ?>"><i class="fas fa-image"></i>&nbsp;<strong><?php echo $this->lang->line('banner');?></strong></a></li>
            <li class=""><a href="<?php echo base_url()."seller/message"; ?>"><i class="fas fa-envelope"></i>&nbsp;<strong><?php echo $this->lang->line('message');?></strong></a></li>
            
            <li class=""><a href="<?php echo base_url()."seller/returnpolicy"; ?>"><i class="fas fa-undo"></i>&nbsp;<strong><?php echo $this->lang->line('return_policy');?></strong></a></li>
            <li class=""><a href="<?php echo base_url()."seller/shipping"; ?>"><i class="fas fa-shipping-fast"></i>&nbsp;<strong><?php echo $this->lang->line('shipping');?></strong></a></li>
            <li><a href="#discount_menu" aria-expanded="false" data-toggle="collapse"><i class="fas fa-users"></i>&nbsp;<strong><?php echo $this->lang->line('discount');?></strong></a>
              <ul id="discount_menu" class="collapse list-unstyled ">
                <li><a href="<?php echo base_url()."seller/discount"?>"><strong><?php echo $this->lang->line('discount');?></strong></a></li>
                <li><a href="<?php echo base_url()."seller/discount/voucher"?>"><strong><?php echo $this->lang->line('discount_coupon');?></strong></a></li>
              </ul>
            </li>
			<li class=""><a href="<?php echo base_url()."seller/import_csv"; ?>"><i class="fas fa-cut"></i><span class="">&nbsp;<strong><?php echo $this->lang->line('import_csv');?></strong></span></a></li>
            <li><a href="#user_management" aria-expanded="false" data-toggle="collapse"><i class="fas fa-users"></i>&nbsp;<strong><?php echo $this->lang->line('user_management');?></strong></a>
              <ul id="user_management" class="collapse list-unstyled ">
                <li><a href="<?php echo base_url()."seller/dashboard/seller_management"?>"><strong><?php echo $this->lang->line('sellers');?></strong></a></li>
                <li><a href="<?php echo base_url()."seller/dashboard/buyer_management"?>"><strong><?php echo $this->lang->line('buyers');?></strong></a></li>
              </ul>
            </li>
            <li class=""><a href="<?php echo base_url()."seller/requests"; ?>"><i class="fa fa-question-circle"></i>&nbsp;<strong><?php echo $this->lang->line('requests');?></strong><?php if($this->requests > 0){?><div class="button__badge"><?php echo $this->requests; ?></div><?php }?></a></li>
            <li class=""><a href="<?php echo base_url()."seller/reviews"; ?>"><i class="fa fa-edit"></i>&nbsp;<strong>Add Reviews</strong></a></li>
            <li><a href="#store_management" aria-expanded="false" data-toggle="collapse"><i class="fa fa-question-circle"></i>&nbsp;<strong>Store Management</strong></a>
              <ul id="store_management" class="collapse list-unstyled ">
                <li><a href="<?php echo base_url()."seller/stores";?>"><strong>Pending Stores</strong></a></li>
                <li><a href="<?php echo base_url()."seller/stores?store=approved" ?>"><strong>Approved Stores</strong></a></li>
                <li><a href="<?php echo base_url()."seller/stores?store=declined";?>"><strong>Declined Stores</strong></a></li>
              </ul>
            </li>
            <li class=""><a href="<?php echo base_url()."seller/queries"; ?>"><i class="fa fa-question-circle"></i>&nbsp;<strong>Search Requests</strong><?php if($this->requests > 0){?><div class="button__badge"><?php echo $this->requests; ?></div><?php }?></a></li>
            <li class=""><a href="<?php echo base_url()."seller/sales/failedOrders"; ?>"><i class="fa fa-times-circle"></i>&nbsp;<strong>Failed Orders</strong><?php if($this->requests > 0){?><div class="button__badge"><?php echo $this->requests; ?></div><?php }?></a></li>
        </ul>
        <?php $allowedmodules = $this->session->userdata("allowedmodules");
        } else {?>
            <ul class="list-unstyled">
                <li class=""><a href="<?php echo base_url()."seller/dashboard"; ?>"><i class="fas fa-location-arrow"></i>&nbsp;<strong><?php echo $this->lang->line('dashboard');?></strong></a></li>
                <li><a href="#viewProducts" aria-expanded="false" data-toggle="collapse"><i class="fas fa-briefcase" aria-hidden="true"></i>&nbsp;<strong><?php echo $this->lang->line('product');?></strong></a>
                  <ul id="viewProducts" class="collapse list-unstyled ">
                    <li><a href="<?php echo base_url()."seller/product"?>"><strong><?php echo $this->lang->line('product_list');?></strong></a></li>
                    <li><a href="<?php echo base_url()."seller/product/create"?>"><strong><?php echo $this->lang->line('create_product');?></strong></a></li>
                    <li><a href="<?php echo base_url()."seller/product/pending_view?v=0"?>"><strong><?php echo $this->lang->line('pending_products');?></strong><?php if($this->pendingProductData > 0){?><div class="button__badge"><?php echo $this->pendingProductData; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/product/pending_view?v=1"?>"><strong><?php echo $this->lang->line('approved_products');?></strong><?php if($this->approvedProducts > 0){?><div class="button__badge"><?php echo $this->approvedProducts; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/product/pending_view?v=2"?>"><strong><?php echo $this->lang->line('rejected_products');?></strong><?php if($this->rejectedProducts > 0){?><div class="button__badge"><?php echo $this->rejectedProducts; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/product/prodAccessories"?>"><strong><?php echo $this->lang->line('pdt_accessories');?></strong></a></li>
                    <li><a href="<?php echo base_url()."seller/product/product_history"?>"><strong><?php echo $this->lang->line('product_report');?></strong></a></li>
                  </ul>
                </li>
                <li><a href="#viewInventory" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-briefcase" aria-hidden="true"></i>&nbsp;<strong><?php echo $this->lang->line('inventory');?></strong></a>
                  <ul id="viewInventory" class="collapse list-unstyled">
                    <li><a href="<?php echo base_url()."seller/product/inventory_view"?>"><strong><?php echo $this->lang->line('inventory_list');?></strong></a></li>
                    <li><a href="<?php echo base_url()."seller/product/inventory_add"?>"><strong><?php echo $this->lang->line('create_inventory');?></strong></a></li>
                    <li><a href="<?php echo base_url()."seller/product/inventory_view/delete"?>"><strong><?php echo $this->lang->line('deleted_inventory_list');?></strong></a></li>
                  </ul>
                </li>
                <li><a href="#viewSales" aria-expanded="false" data-toggle="collapse"><i class="far fa-money-bill-alt"></i>&nbsp;<strong><?php echo $this->lang->line('order');?></strong></a>
                  <ul id="viewSales" class="collapse list-unstyled ">
                    <li><a href="<?php echo base_url()."seller/sales/"?>"><strong><?php echo $this->lang->line('pending_orders');?></strong><?php if($this->pendingOrders > 0){?><div class="button__badge"><?php echo $this->pendingOrders; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/sales/acceptedOrders_view"?>"><strong><?php echo $this->lang->line('accepted_orders');?></strong><?php if($this->acceptedOrders > 0){?><div class="button__badge"><?php echo $this->acceptedOrders; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/sales/declinedOrders_view"?>"><strong><?php echo $this->lang->line('declined_orders');?></strong><?php if($this->rejectedOrders > 0){?><div class="button__badge"><?php echo $this->rejectedOrders; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/sales/cancel_orders_view"?>"><strong><?php echo $this->lang->line('cancel_orders');?></strong><?php if($this->cancellationOrders > 0){?><div class="button__badge"><?php echo $this->cancellationOrders; ?></div><?php }?></a></li>
                    <li><a href="<?php echo base_url()."seller/sales/order_history"?>"><strong><?php echo $this->lang->line('orders_report');?></strong></a></li>
                  </ul>
                </li>
                <?php /* <li class=""><a href="<?php echo base_url()."seller/editprofile"; ?>"><i class="fas fa-user-alt"></i><span class="">&nbsp;<strong>Profile</strong></span></a></li> */?>
                <?php if($_SESSION['is_zabee'] != 1){?>
                <li class=""><a href="<?php echo base_url()."seller/location"; ?>"><i class="fas fa-location-arrow"></i>&nbsp;<strong><?php echo $this->lang->line('location');?></strong></a></li>
                <li class=""><a href="<?php echo base_url()."seller/shipping"; ?>"><i class="fas fa-shipping-fast"></i>&nbsp;<strong><?php echo $this->lang->line('shipping');?></strong></a></li>
                <li class=""><a href="<?php echo base_url()."seller/returnpolicy"; ?>"><i class="fas fa-undo"></i>&nbsp;<strong><?php echo $this->lang->line('return_policy');?></strong></a></li>
                <?php }?>
                <li><a href="#discount_menu" aria-expanded="false" data-toggle="collapse"><i class="fas fa-users"></i>&nbsp;<strong><?php echo $this->lang->line('discount');?></strong></a>
                  <ul id="discount_menu" class="collapse list-unstyled ">
                    <li><a href="<?php echo base_url()."seller/discount"?>"><strong><?php echo $this->lang->line('discount');?></strong></a></li>
                    <li><a href="<?php echo base_url()."seller/discount/voucher"?>"><strong><?php echo $this->lang->line('discount_coupon');?></strong></a></li>
                  </ul>
                </li>
		            <li class=""><a href="<?php echo base_url()."seller/import_csv"; ?>"><i class="fas fa-cut"></i>&nbsp;<strong><?php echo $this->lang->line('import_csv');?></strong></a></li>
                <li class=""><a href="<?php echo base_url()."seller/message"; ?>"><i class="fas fa-envelope"></i>&nbsp;<strong><?php echo $this->lang->line('message');?></strong></a></li>
                <li class=""><a href="<?php echo base_url()."seller/requests"; ?>"><i class="fa fa-question-circle"></i>&nbsp;<strong><?php echo $this->lang->line('requests');?></strong></a></li>
            </ul>
        <?php }	?>
    </nav>