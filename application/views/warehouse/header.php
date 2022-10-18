<div class="page">
      <!-- Main Navbar-->
      <header class="header">
        <nav class="navbar">
          <!-- Search Box-->
          <div class="search-box">
            <button class="dismiss"><i class="icon-close"></i></button>
            <form id="searchForm" action="#" role="search">
              <input type="search" placeholder="What are you looking for..." class="form-control">
            </form>
          </div>
          <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
              <!-- Navbar Header-->
              <div class="navbar-header">
                <!-- Navbar Brand --><a href="<?php echo base_url(); ?>" class="navbar-brand">
                  <div class="brand-text brand-big"><span>Za </span><strong>Bee</strong></div>
                  <div class="brand-text brand-small"><strong>ZB</strong></div></a>
                <!-- Toggle Button--><a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
              </div>
              <!-- Navbar Menu -->
              <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
                <!-- Search-->
                <li class="nav-item d-flex align-items-center"><a id="search" href="#"><i class="icon-search"></i></a></li>
                <!-- Notifications-->
                <li class="nav-item dropdown"> <a id="notifications" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-bell"></i><span class="badge bg-red"><?php echo $notificationCount; ?></span></a>
                    <?php echo $notifications; ?>
                </li>
                <!-- Messages                        -->
                <li class="nav-item dropdown"> <a id="messages" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-envelope"></i><span class="badge bg-orange" id="textNotification"><?php echo ($textNotification['rows'] > 0)?$textNotification['rows']:"";?></span></a>
                  <ul aria-labelledby="notifications" class="dropdown-menu">
                    <?php
						if($textNotification['rows'] > 0){
					 foreach($textNotification['result'] as $tn){
							$product_variant_id = ($tn->product_variant_id)?$tn->product_variant_id:"";
							$url = $tn->userid.','.$product_variant_id.','.$tn->item_type.','.$tn->seller_id.','.$tn->buyer_id.','.$tn->item_id.','.$tn->product_link.','.$_SESSION['userid'];
							$url = base64_encode($url);
						?>
                    	 <li id="notification-<?php echo $tn->userid."-".$product_variant_id."-".$tn->item_type?>"><a rel="nofollow" href="<?php echo base_url("seller/message?open=".$url)?>" class="dropdown-item d-flex"> 
                        <div class="msg-profile"> <img src="<?php echo ($tn->user_pic =="")?media_url('assets/backend/images/blank-profile-picture.png'):profile_path($tn->user_pic)?>" alt="..." class="img-fluid rounded-circle"></div>
                        <div class="msg-body">
                          <h3 class="h5"><?php echo $tn->sender_name;?></h3><span><?php echo (strlen($tn->message)>16)?substr($tn->message,0,16).'...':$tn->message;?></span>
                        </div></a></li>
					<?php }?>
                    <li><a rel="nofollow" href="<?php echo base_url('warehouse/message');?>" class="dropdown-item all-notifications text-center"> <strong><?php echo $this->lang->line('read_mess');?></strong></a></li>
                    <?php }else{?> <li><a rel="nofollow" href="<?php echo base_url('warehouse/message');?>" class="dropdown-item all-notifications text-center"> <strong><?php echo $this->lang->line('no_mess');?></strong></a></li><?php }?>
                  </ul>
                </li>
                <!-- Logout    -->
                <li class="nav-item"><a href="<?php echo base_url()."warehouse/logout" ?>" class="nav-link logout"><?php echo $this->lang->line('logout');?><i class="fa fa-sign-out"></i></a></li>
              </ul>
            </div>
          </div>
        </nav>
      </header>
      