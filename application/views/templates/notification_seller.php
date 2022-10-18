<ul aria-labelledby="notifications" class="dropdown-menu" style="min-width: 350px;">
<?php
	$notification_id = '';
	if(count($notifications)>0){
		foreach($notifications as $item){
			$link = ($item->notification_link != '')?$item->notification_link:'#';
?>
	<li style="border-bottom:1px solid #ddd">
		<a rel="nofollow" href="<?php echo $link; ?>" class="dropdown-item"> 
			<div class="notification">
			  <div class="row notification-content" style="white-space: normal;">
			  	<div class="col-sm-2"><i class="fa fa-envelope bg-<?php echo $actions[$item->notification_type]; ?>"></i></div>
				<div class="col-sm-9"><p><?php echo $item->notification_message; ?></p><p style="margin-bottom:0; margin-top:0"><?php daysAgo($item->created); ?></p></div>
			</div>
			</div>
		</a>
	</li>
<?php 
	$notification_id = $item->id;
	}
} else {
?>
	<li>
		<a rel="nofollow" href="#" class="dropdown-item"> 
			<div class="notification"><h6 class="showMoreNotifications"><?php echo "No notification found"; ?></h6></div>
		</a>
	</li>
<?php } ?>
<?php if(count($totalNotifications) > count($notifications)){ ?>
	<hr />
	<li>
		<a rel="nofollow" href="<?php echo $link; ?>" class="dropdown-item"> 
			<div class="notification">
			  <div class="row notification-content" style="white-space: normal;">
			  		<h6 class="showMoreNotifications">Show more</h6>
			  </div>
			</div>
		</a>
	</li>
	<?php } ?>
<input type="hidden" name="notification_id" id="notification_id" value="<?php echo $notification_id; ?>" />
<input type="hidden" name="notification_usertype" id="notification_usertype" value="<?php echo $usertype; ?>" />
<input type="hidden" name="notifications_read" id="notifications_read" value="0" />
</ul>	
