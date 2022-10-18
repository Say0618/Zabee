
<?php 
$changed=$this->session->flashdata('changed');
if(!empty($changed)){
    echo "Registration successful";
?><div class="row changedSucessfully">
<div class="col-md-4"></div>
<div class="alert alert-success alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $this->lang->line('success');?>!</strong> <?php echo $this->lang->line('pw_success');?>
  </div>
  <div class="col-md-4"></div>
</div>
<?php } ?>
<?php $wrongCurrentPassword=$this->session->flashdata('Worng_Current_Password');
         if(!empty($wrongCurrentPassword)){
?>
<div class="row wrongCurrentPassword">
<div class="col-md-4"></div>
<div class="alert alert-warning alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $this->lang->line('warning');?>!</strong> <?php echo $this->lang->line('enter_pw');?>
  </div>
  <div class="col-md-4"></div>
</div>
<?php } ?>
<?php
     $confirm_password=$this->session->flashdata('confirm_password');
     if(!empty($confirm_password)){
?><div class="row confirmPassworsMismatch">
<div class="col-md-4"></div>
<div class="alert alert-warning alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $this->lang->line('warning');?>!</strong><?php echo $this->lang->line('pw_confirm');?>
  </div>
  <div class="col-md-4"></div> 
</div>

<?php } ?>
<?php
$previous_password=$this->session->flashdata('previous_password');
 if(!empty($previous_password)){
?><br>
<div class="row sameNewPassword">
<div class="col-md-4"></div>
<div class="alert alert-danger alert-dismissable fade show col-md-4" align="center" >
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><?php echo $this->lang->line('warning');?>!&nbsp</strong><?php echo $this->lang->line('pw_different');?>
  </div>
  <div class="col-md-4"></div>
</div>
<?php } ?>
<div class="container py-3">
    <div class="row">
        <div class="col-md-6 col-lg-4 mx-auto">
                <div class="card card-body">
                    <div align="center">
                    <h3><i class="fa fa-lock fa-4x"></i></h3></div>
 
                    <h3 class="text-center mb-4"><?php echo $this->lang->line('change_pw');?></h3>
                    <form id="change_password" action="<?php echo base_url('Home/change_password_process');?>" method="post"  novalidate>
                    <fieldset>
                    <div class="form-group">
                            <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('enter_cur_pw');?>" name="current_password" value="" type="password" id="current_password" required > 
                        </div>
                           <div class="form-group">
                            <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('enter_new_pw');?>" name="password" value="" type="password" id="password" required > 
                        </div>
                        <div class="form-group">
                            <input class="form-control input-lg" placeholder="<?php echo $this->lang->line('confirm_pw');?>" name="confirm_password"  value="" type="password" id="confirm_password" required >
                        </div>  
                        <input class="btn btn-lg btn-primary btn-block" value="<?php echo $this->lang->line('change');?>" type="submit">
                    </fieldset>
                </form>
                </div>
        </div>
    </div>
</div>