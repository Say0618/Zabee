<div class="page-content d-flex align-items-stretch"> 
    <nav class="side-navbar">
      <a href="<?php echo base_url("seller/dashboard")?>">
          <div class="sidebar-header d-flex align-items-center">
            <div class="avatar"><img src="<?php echo ($_SESSION['user_pic'] != "")?profile_path().$_SESSION['user_pic']:profile_path("defaultprofile.png") ?>" alt="..." class="img-fluid rounded-circle avatar" /></div>
            <div class="title">
              <h1 class="h4"><?php echo stripslashes((isset($_SESSION['userid']))?$_SESSION['firstname']." ".$_SESSION['lastname']:"name");?></h1>
            </div>
          </div>
      </a>
            <ul class="list-unstyled">
                <li><a href="<?php echo base_url()."referral/referrals/" ?>" aria-expanded="false"> <i class="fas fa-briefcase" aria-hidden="true"></i></i><strong>Products Referral List</strong></a>
                </li>
                <li><a href="<?php echo base_url()."referral/referrals/invite" ?>" aria-expanded="false"><i class="fas fa-briefcase" aria-hidden="true"></i></i><strong>Invite People</strong></a>
                </li>
                <li><a href="<?php echo base_url()."referral/referrals/invite_list" ?>" aria-expanded="false"> <i class="fas fa-briefcase" aria-hidden="true"></i></i><strong>Invited People List</strong></a>
                </li>
                <?php if($this->session->userdata("user_type") == 1){ ?>
                  <li><a href="<?php echo base_url()."referral/referrals/user_history"?>" aria-expanded="false"><i class="fas fa-users"></i></i><strong><?php echo "Referrals History"?></strong></a>
                  </li>
                <?php } ?>
            </ul>
    </nav>
       