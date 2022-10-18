<!DOCTYPE html>
<html>
  <head>
    <?php include('head.php') ?>
  </head>
  <body>
    <div class="page login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1>Dashboard</h1>
                  </div>
                  <p>ZaBee.</p>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
              <div class="form d-flex align-items-center">
                <div class="content">
                  <form id="login-form" name="sellerLoginForm" method="post" action="<?php echo base_url()."warehouse/login/user_login"; ?>">
                    <div class="form-group">
                      <input id="login-username" type="text" name="username" required="" class="input-material" placeholder="User Name">
                    </div>
                    <div class="form-group">
                      <input id="login-password" type="password" name="password" required="" class="input-material" placeholder="Password">
                    </div>
					<?php if($incorrect == "yes"){
						echo "<div class='incorrect_id_or_pass'>";
						echo "<h5>Incorrect ID or Password</h5>";
						echo "</div>";
					} ?>
					<br /><button id="login" type="submit" class="btn btn-primary">Login</button>
                    <!-- This should be submit button but I replaced it with <a> for demo purposes-->
                  </form><a href="<?php echo base_url('Home/forgotpassword'); ?>" class="forgot-pass">Forgot Password?</a><br><small>Do not have an account? </small><a href="<?php echo base_url('join_us'); ?>" class="signup">Signup</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Javascript files-->
      <script src="<?php echo media_url(); ?>assets/common/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo media_url(); ?>assets/vendor/popper.js/umd/popper.min.js"> </script>
    <script src="<?php echo media_url(); ?>assets/common/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo media_url(); ?>assets/common/vendor/jquery.cookie/jquery.cookie.js"> </script>
    <script src="<?php echo media_url(); ?>assets/vendor/chart.js/Chart.min.js"></script>
    <script src="<?php echo media_url(); ?>assets/common/vendor/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo media_url(); ?>assets/js/charts-home.js"></script>
    <script type="text/javascript" src="<?php echo media_url('assets/front/js/scripts.js'); ?>"></script>
    <!-- Main File-->
    <script src="js/front.js"></script>
    <script>
	jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter can not be a space");
jQuery.validator.addMethod("email", function(value, element) {
	if(isValidEmailAddress(value)){
	  return this.optional( element ) || true;
	} else {
	  return this.optional( element )|| false;
	}
}, "Please enter a valid email."
);
$('#login-form').validate({
    rules:{
      username:{
            required: true,
            FirstLetter:true,
            email:true
        },
        password:{
            required: true,
            FirstLetter:true,
        }
    },
    errorPlacement: function (error, element) {
        error.appendTo(element.parent());
	}
});
</script> 
  </body>
</html>
