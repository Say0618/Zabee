<?php
$password_notsame=$this->session->flashdata('password_notsame');
if(!empty($password_notsame)){
?><br>
<div class="row">
<div class="col-md-4"></div>
<div class="alert alert-danger alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Warning!&nbsp</strong>Password and confirm password field should be same.
  </div>
  <div class="col-md-4"></div>
</div>
<?php
}
?>
<?php
$previous_password=$this->session->flashdata('previous_password');
if(!empty($previous_password)){
?><br>
<div class="row">
<div class="col-md-4"></div>
<div class="alert alert-danger alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>Warning!&nbsp</strong>Password should be different from current password.
  </div>
  <div class="col-md-4"></div>
</div>
<?php
}
?>
<div class="container">
    <div class="row">
        <div class="col-sm-4"></div>
          <div class="col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">
                          <h3><i class="fa fa-envelope fa-4x"></i></h3>
                          <h2 class="text-center">Password Reset</h2>                                                 
                            <div class="panel-body">
                              <form class="form" method="post" action="<?php echo base_url()."Home/reset_processing"; ?>"  id="reset_password-form" >
                                <fieldset>
                                  <input type="hidden" class = "input-xlarge" id="encrypted_code" name = "encrypted_code" value="<?php echo $encrypted_code;?>">  

                                	<input type="hidden" class = "input-xlarge" id="email" name = "email" value="<?php echo $email;?>">  
                                  <div class="form-group">
                                    <input type="password" name="reset_password" id="reset_password" class="form-control input-lg" placeholder="Password" tabindex="4" required>
                                  </div>
                                   <div class="form-group"> 
                                     <input type="password" name="confirm_password" value="" id="reset_confirm_password" class="form-control input-lg" placeholder="Confirm Password" tabindex="4" required>
                                  </div>
                                  <div class="form-group">
                                   <input name="submit" class="btn btn-lg btn-primary btn-block" value="Change" type="submit" >
                                  </div>
                                </fieldset>
                              </form>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>
                  <div class="col-sm-4"></div>
    </div>
</div>









