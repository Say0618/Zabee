<div class="d-lg-none  mt-2  w-100 navbar-light">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#buyermenu" aria-controls="buyermenu" aria-expanded="false" aria-label="Toggle navigation">
                	<span class="navbar-toggler-icon"></span>
                </button>
            </div>
<div class="w-100">
<nav class="navbar navbar-expand-lg header-verticle-align-middle">
	<div class="collapse navbar-collapse tab buyermenu" id="buyermenu">
	<div class="tab buyermenu mt-3" id="buyermenu">
		<a class="tablinks<?php echo ($active_page=="orders")?" active":""?>" href="<?php echo base_url('orders')?>"><i class="fas fa-archive pr-3"></i>My Orders</a>
		<a class="tablinks<?php echo ($active_page=="message-panel")?" active":""?>" href="<?php echo base_url('message')?>"><i class="fa fa-inbox pr-3" aria-hidden="true"></i>Messages</a>
		<!-- <a class="tablinks<?php echo ($active_page=="billing-system")?" active":""?>" href="<?php echo base_url('billing')?>"><i class="fas fa-credit-card pr-3"></i>Billing System</a> -->
		<a class="tablinks<?php echo ($active_page=="profile")?" active":""?>" href="<?php echo base_url('account')?>"><i class="fas fa-user-edit pr-3"></i>Profile</a>
		<a class="tablinks<?php echo ($active_page == "address_book" || $active_page == "address_book_add" || $active_page == "address_book_update")?" active":""?>" href="<?php echo base_url('shipping')?>"><i class="fas fa-shipping-fast pr-3"></i>Shipping</a>
		<a class="tablinks<?php echo ($active_page=="saved_for_later")?" active":""?>" href="<?php echo base_url('wish_list')?>"><i class="fas fa-star pr-3"></i>Wish List</a>
	</div>
	</div>
	</div>
</nav>