<?php
  $invalid_email=$this->session->flashdata('invalid_email');
  if($invalid_email)
  {
?>  
<script>$('#invalidEmailModal').modal('show');</script>
<?php
}
?>
<?php
$invalid_code=$this->session->flashdata('invalid_code');
if($invalid_code)
{
?>
  <script> $('#invalidCodeModal').modal('show');</script>
  
<?php
}
if(isset($encryption)){?>
<div id="join-us-bg"> 
    <div class="container">
        <div class="col-12 forgot-pass-box">
            <div class="offset-lg-2 col-lg-8 col-12">
              <p class="pass-link-text"><?php echo $this->lang->line('we_have');?></p>
              <span><?php echo $this->lang->line('you_havenot_receive');?></span><a class="price ml-1" href="<?php echo base_url('Home/forgotpassword'); ?>"><?php echo $this->lang->line('click_here');?></a>
            </div> 
        </div>
    </div>
</div>
<?php
}else{ ?>
<div id="join-us-bg"> 
    <div class="container">
      <div class="row"> 
        <div class="offset-lg-2 col-12 col-lg-8 forgot-pass-box padding-less">
            <div class="row">
              <div class="offset-lg-2 offset-3 p-0">
                <h2 class="forgot-pass-headings mb-5"><?php echo $this->lang->line('forgot_pass');?></h2>
              </div>
              <div class="col-sm-12 mb-3 d-block d-sm-none">
                <h5 class="join-us-heading2 text-center"><?php echo $this->lang->line('dont_have_account');?> <a href="<?php echo base_url('join_us');?>"><?php echo $this->lang->line('sign_up');?></a></h5>
            </div>
              <div class="offset-2 col-8">
                <form class="form" name="form" id="form" method="post" action="<?php echo base_url('Home/password');?>" novalidate="novalidate">
                  <div class="row form-group">
                    <label class="form-control-label resposive-label forgot-label"><?php echo $this->lang->line('email');?></label>
                    <div class="input-group">
                      <input id="emailInput" name="email" placeholder="<?php echo $this->lang->line('email_address');?>" data-error="#emailInput" class="form-control rounded-0" type="email" oninvalid="setCustomValidity()"  onchange="try{setCustomValidity('')}catch(e){}" required="">
                    </div>
                    <div class="errorTxt"></div>
                  </div>
                  <div class="row form-group text-center">
                    <div class="col">
                      <input class="btn btn-primary rounded-0 change-pass-btn" id="change-pass-btn" value="<?php echo $this->lang->line('reset');?>" type="submit">
                    </div>
                  </div>
                </form>
                </div>
            </div>
        </div>  
      </div>
    </div>
</div>
<?php
}
if(isset($encryption)){?>
<script>
$(document).ready(function(e){
  $('.content_forgotpassword').css('background-image','url(<?php echo assets_url("front/images/contact-us.jpg")?>)');
});
</script>
<?php }?>
