<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $title ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="<?php echo base_url('favicon.ico'); ?>">
	<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="<?php echo assets_url('login/css/util.css'); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo assets_url('login/css/main.css'); ?>">
<!--===============================================================================================-->
</head>
<body style="background-color: #666666;">
	
	<div class="limiter">
		<div class="container-login100">
			<div class="wrap-login100">
				<form class="login100-form validate-form" id="login-form" name="sellerLoginForm" method="post" action="<?php echo base_url()."seller/login/user_login"; ?>">
					<span class="login100-form-title p-b-43">
						Login to continue
					</span>
					
					
					<div class="wrap-input100 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
						<input class="input100 input-material has-val" type="text" id="login-username" name="username" required="">
						<span class="focus-input100"></span>
						<span class="label-input100">Email</span>
					</div>
					
					
					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<input class="input100 input-material has-val" type="password" id="login-password" name="password" required="">
						<span class="focus-input100"></span>
						<span class="label-input100">Password</span>
					</div>
                    <div>
                        <?php if($incorrect == "yes"){
                            echo "<div class='incorrect_id_or_pass p-l-4 m-t-12 m-b-12 text-danger'>";
                            echo "<h5>Incorrect ID or Password</h5>";
                            echo "</div>";
                        } ?>
                    </div>
					<div class="flex-sb-m w-full p-t-3 p-b-32 text-right">
						<div class="w-full">
							<a href="<?php echo base_url('forgotpassword'); ?>" class="txt1">
								Forgot Password?
							</a>
						</div>
					</div>
			

					<div class="container-login100-form-btn">
						<button class="login100-form-btn" id="login" type="submit">
							Login
						</button>
					</div>
					
					<div class="text-center p-t-20 p-b-20">
						<span class="txt2">
							or sign up using
						</span>
					</div>

					<div class="login100-form-social flex-c-m">
						<a href="<?php echo base_url('join_us?type=seller'); ?>" class="login100-form-btn signup" id="login" type="submit">
							Signup
						</a>
					</div>
					<div class="container-login100-form-btn" style="margin-top:15px;">
						<a href="https://play.google.com/store/apps/details?id=ee.zab.zabee_seller">
						<img src="<?php echo assets_url("front/images/")?>google-play-badge.png" style="width: 112px;height:39px;border-radius:6px;margin-top:1px;margin-right:15px;">
						</a>
						<a href="https://apps.apple.com/us/app/zab-ee-seller/id1540150429"><img src="<?php echo assets_url("front/images/")?>Download_on_the_App_Store_Badge_US-UK_RGB_blk_092917.svg" style="width: 120px;height: 40px;"></a>
					</div>
				</form>

				<div class="login100-more" style="background-image: url('<?php echo assets_url('login/images/bg-01.jpg')?>');">
				</div>
			</div>
		</div>
	</div>
	
<!--===============================================================================================-->
    <script src="<?php echo assets_url('common/vendor/jquery/jquery.min.js'); ?>"></script>
<!--===============================================================================================-->
	<script src="<?php echo assets_url('login/js/main.js'); ?>"></script>

</body>
</html>