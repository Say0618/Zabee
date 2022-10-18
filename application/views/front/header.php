<?php $word = (isset($_GET['search']) && $_GET['search'] !="")?trim($_GET['search']):""; ?>
<header class="nav-backgroud">
	<div class="container">
		<div class="row headerRow"> 
      		<div class="col-12 headerCol">
                <div class="row">
                    <div class="col d-inline-flex pl-0">
                        <div class="wrapper">
                            <!-- Sidebar -->
                            <nav id="sidebar">
                            	<div class="sidebar-header">
                                    <div id="dismiss">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="">
                                        <img src="<?php echo website_img_path('logo.png');?>" alt="<?php echo $title;?>" class="img-fluid"/>
                                    </div>
                                </div>
                        	</nav>
                            <div class="dropdown ctgdropdown btn-group mt-1">
                                <button class="btn ctgbtn" type="button" data-toggle="dropdown">
                                	<i class="fas fa-align-justify caret-color"></i>
                                </button>
                                <?php echo $this->menu; ?>
                            </div>
  						</div>
                        <div id="hide-in-small-screens" class="my-auto">
                            <a class="navbar-brand" href="<?php echo base_url();?>">
                                <img src="<?php echo website_img_path('logo.png');?>" alt="Zab.ee" class="img-fluid"/>
                            </a>
                        </div>
                        <div id="initially-hidden-only-visible-in-small-screens" class="my-auto float-right">
							<a class="navbar-brand" href="<?php echo base_url();?>">
								<img src="<?php echo website_img_path('logo.png');?>" alt="Zab.ee" class="img-fluid">
							</a>
						</div>
                    </div>
                    <?php if(!$detect->isMobile()){?>
                    <div class="input-group col-sm-6 my-auto" id="input-search">
                        <div class="input-group-prepend">
                            <div class="dropdown">
                              <button type="button" class="btn dropdown-toggle dropbtn" onclick="myFunction()"></button>
                              <div id="myDropdown" class="dropdown-content">
                                <div class="row">
                                	<div class="col-sm-12">
                                    	<input type="text"  placeholder="Search.." class="form-control" id="myInput" onkeyup="filterFunction()" >
                                    </div>
                                </div>
                                <div class="col-12 list-inline">
									<?php $i = 0; $close=0; $count = count($parentCategory); ?>                                
									<?php foreach($parentCategory as $pc){ ?>
                                        <?php if($pc->is_private != "1"){ ?>
									        <a href="javascript:void(0)" class="list-inline-item" data-id="<?php echo $pc->category_id;?>"><?php echo $pc->category_name;?></a>
									    <?php } ?>
									<?php } ?>
									<div class="clear"></div>
                                </div>  
                              </div>
                            </div>
                        </div>
                        <input type="search" placeholder="<?php echo $this->lang->line('search');?>" class="form-control search_text valid" name="search" id="search-bar" value="<?php echo $word ?>" />
                        <button class="btn" id="search-button" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <?php }?>
					<div class="col-4 my-auto">
                    	<div class="row float-right">
                            <div class="wrapper">
                            <!-- Sidebar -->
                                <nav id="account-sidebar">
                                    <div class="sidebar-header">
                                        <div id="accountDismiss">
                                            <i class="fas fa-times"></i>
                                        </div>
                                       	<div class="d-inline-flex">
                                        	<div class="h_i_s"><span class="h_t_c"><?php if($this->isloggedin){?><?php echo (isset($_SESSION["user_pic"]) && $_SESSION["user_pic"] !="")?'<img src="'.profile_path($_SESSION["user_pic"]).'" class="img img-fluid rounded-circle">':$_SESSION['firstname'][0].$_SESSION['lastname'][0]; }else{?><i class="fas fa-user"></i><?php }?></span></div>
                                            <span class="my-auto ml-2 f_s16"><?php if($this->isloggedin){?><?php echo $_SESSION['firstname']." ".$_SESSION['lastname'][0]."."; }else{ echo  $this->lang->line('account'); }?></span>
                                        </div>
                                	</div>
                                    <ul class="list-unstyled components">
                                    	<?php if(!$this->isloggedin){?>
                                        <li>
                                            <a href="<?php echo base_url("login")?>"><i class="fas fa-sign-in-alt pr-3"></i> <span class=""><?php echo $this->lang->line('sign_in');?></span></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url("join_us")?>"><i class="fas fa-user-plus pr-3"></i> <span class=""><?php echo $this->lang->line('create_account');?></span></a>
                                        </li>
                                        <li>
                                            <a href="<?php echo base_url("contact_us")?>"><i class="fas fa-info-circle pr-3"></i> <span class=""><?php echo $this->lang->line('help');?></span></a>
                                        </li>
                                        <?php }else{ ?>
                                        <?php if($_SESSION['store_id'] != ""){?>
                                            <li><a href="<?php echo base_url('seller')?>"><i class="fas fa-user-cog pr-3"></i><?php echo ($_SESSION['userid'] == "1")?$this->lang->line('seller_admin'):$this->lang->line('seller');?></a></li>
                                        <?php }?>
                                        <?php if($_SESSION['warehouse_id'] != ""){?>
                                            <li><a href="<?php echo base_url('warehouse')?>"><i class="fas fa-user-cog pr-3"></i><?php echo ($_SESSION['userid'] == "1")?$this->lang->line('warehouse_admin'):$this->lang->line('warehouse');?></a></li>
                                        <?php } ?>
                                        
                                        <li><a href="<?php echo base_url('orders')?>"><i class="fas fa-archive pr-3"></i><?php echo $this->lang->line('my_orders');?>&nbsp;<?php if(isset($order_notification) && $order_notification['rows'] > 0){?><span class="badge badge-info notify-badges"><?php echo $order_notification['rows']?></span><?php } ?></a></li>
                                        <li><a href="<?php echo base_url('message')?>"><i class="fa fa-inbox pr-3" aria-hidden="true"></i><?php echo $this->lang->line('messages');?>&nbsp;<?php if(isset($text_notification['rows']) && $text_notification['rows'] > 0){?><span class="badge badge-info notify-badges"><?php echo $text_notification['rows']?></span><?php } ?></a></li>
                                        <li><a href="<?php echo base_url('account')?>"><i class="fas fa-user-edit pr-3"></i><?php echo $this->lang->line('account');?></a></li>
                                        <li><a href="<?php echo base_url('shipping')?>"><i class="fas fa-shipping-fast pr-3"></i><?php echo $this->lang->line('shipping');?></a></li>
                                        <li><a href="<?php echo base_url('wish_list')?>"><i class="fas fa-star pr-3"></i><?php echo $this->lang->line('wishlist');?></a></li>
                                        <?php if(isset($_SESSION['affiliated_id']) && $_SESSION['affiliated_id'] != ""){ ?>
                                        <li><a href="<?php echo base_url('referral/referrals/')?>"><i class="fas fa-star pr-3"></i>Referrals</a></li>	
                                        <?php }else{ ?>
                                            <li><a href="<?php echo base_url('referral/affiliate/affiliate_user/').$this->session->userdata("userid"); ?>"><i class="fas fa-star pr-3"></i>Affiliate Signup</a></li>
                                        <?php } ?>										
                                        <li><a href="<?php echo base_url("contact_us")?>"><i class="fas fa-info-circle pr-3"></i> <span class="f_s16"><?php echo $this->lang->line('help');?></span></a></li>
                                        <li><a href="<?php echo base_url('logout')?>"><i class="fas fa-sign-out-alt pr-3"></i><?php echo $this->lang->line('sign_out');?></a></li>
                                        <?php }?>
                                    </ul>
                                </nav>
                            <!-- Page Content -->
                            </div>
                            <div class="wrapper">
                            <!-- Sidebar -->
                                <nav id="minicart-sidebar"></nav>
                            <!-- Page Content -->
                            </div>
                             <div class="flip-card">
                                <div class="flip-card-inner">
                                    <div class="flip-card-front">
                                       <a class="sidebar-dropdown-toggle" id="accountsidebarCollapse" href="javascript:void(0)"> <?php if($this->isloggedin){?><?php echo "<strong>".$_SESSION['firstname'][0].$_SESSION['lastname'][0]."</strong>"; }else{?><i class="fas fa-user"></i><?php }?></a>
                                    </div>
                                    <div class="flip-card-back">
                                      <a class="flipBtn sidebar-dropdown-toggle" id="accountsidebarCollapse2" href="javascript:void(0)"><?php echo $this->lang->line('account');?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="flip-card">
                              <div class="flip-card-inner">
                                <div class="flip-card-front">
                                	<a class="sidebar-dropdown-toggle cartBtn" id="minicartSideBarCollapse" href="<?php echo ($this->session->userdata('cart_contents') && $this->cart->total_items() > 0)?"javascript:void(0)":base_url('cart')?>"> <i class="fa fa-shopping-cart"></i></a>
                                </div>
                                <div class="flip-card-back">
                                    <a class="flipBtn sidebar-dropdown-toggle" id="minicartSideBarCollapse2" href="<?php echo ($this->session->userdata('cart_contents') && $this->cart->total_items() > 0)?"javascript:void(0)":base_url('cart')?>"><?php echo $this->lang->line('cart');?></a>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-8 col-12 d-sm-none customCssAddedForBackgroundColorOnly py-3">
                <div class="input-group w-100">
                    <input type="search" placeholder="<?php echo $this->lang->line('search');?>" class="form-control search_text valid mobile_search-input" name="search" id="search-bar" value="<?php echo $word ?>" />
                    <span class="input-group-btn" id="search-btn">
                    <button class="btn" id="search-button" type="submit">
                    <i class="fas fa-search"></i>
                    </button>
                    </span>
                </div>
            </div>
        </div>
	</div>
        <input type="hidden" name="url" id="current-url" value="<?php echo $_SERVER['QUERY_STRING']; ?>"/>
        <input type="hidden" value="" id="search_cat_id" />
</header>