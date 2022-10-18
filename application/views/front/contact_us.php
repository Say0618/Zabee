<link rel="stylesheet" href="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.css'); ?>">
<script src='https://www.google.com/recaptcha/api.js'></script>
<div id="join-us-bg">
    <div class="offset-sm-3 col-sm-6">
    	<div class="row join-us-box text-center"> 
			<div class="col-sm-12">
                <div class="row">
                    <div class="offset-sm-2 col-sm-8">
                    	<?php if(!empty($_SESSION['message_sent'])){?>
						<div class="alert alert-success alert-dismissable fade show" align="center" >
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<strong><?php echo $this->lang->line('success');?>!</strong> <?php echo $this->lang->line('req_sent');?>.
						</div>
						<?php }	?>
                        <h2 class="forgot-pass-headings mb-3"><?php echo $this->lang->line('request_support');?></h2>
                        <div class="row">
                            <div class="col-12">
                                <form id="join-us-form" action="<?php echo base_url('home/contact_process'); ?>" method="post" > 
                                    <div class="row form-group">
                                        <label class="form-control-label resposive-label"><?php echo $this->lang->line('email');?>:</label>
                                        <div class="input-group">
                                            <input placeholder="<?php echo $this->lang->line('email');?>" class="form-control" type="email" name="email" value="<?php if(isset($email)){ echo $email; } ?>" tabindex="1">
                                        </div>
                                        <?php echo form_error('email'); ?>
                                    </div>
                                    <div class="row form-group">
                                        <label class="form-control-label resposive-label"><?php echo $this->lang->line('subject');?>:</label>
                                        <div class="input-group">
                                            <input placeholder="<?php echo $this->lang->line('subject');?>" class="form-control" type="text" name="subject" value="" tabindex="2" >
                                        </div>
                                        <?php echo form_error('subject'); ?>
                                    </div>
                                    <div class="row form-group">
                                        <label class="form-control-label resposive-label"><?php echo $this->lang->line('phone_number');?>:</label>
                                        <div class="input-group">
                                            <input placeholder="<?php echo $this->lang->line('phone_number');?>" class="form-control" id="phone_number" type="tel" name="phone_number" value="" maxlength="20" tabindex="3" >
                                                <span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="form-control-label resposive-label"><?php echo $this->lang->line('order_number');?>:</label>
                                        <div class="input-group">
                                            <input placeholder="<?php echo $this->lang->line('order_number');?>" class="form-control" type="text" name="order_number" value="" tabindex="4" >
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="form-control-label resposive-label"><?php echo $this->lang->line('message');?>:</label>
                                        <div class="input-group">
                                            <textarea class="form-control" rows="6" value="<?php if(isset($message)){ echo $message; } ?>" name="message"></textarea>
                                        </div>
                                        <?php echo form_error('message'); ?>
                                    </div>
                                    <div class="form-group">
                                        <div align="center">
                                            <div class="g-recaptcha" data-sitekey="<?php echo $this->config->item('recaptcha_site_key')?>"></div>
                                            <span class="recaptcha_error error"></span>
                                        </div>
                                    </div>
                                    <div class="row form-group text-center">
                                        <input type="hidden" name="user_id" value="<?php echo isset($_SESSION['userid'])?>">
                                        <button name="submit" type="submit" id="join-us-btn" data-submit="...Sending"><?php echo $this->lang->line('send_request');?></button>
                                        <div class="success"><?php if(isset($success)){ echo $success; } ?></div>
                                    </div>
                                </form>
                              </div>
                          </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<script>
$("#join-us-form").submit(function(event) {

var recaptcha = $("#g-recaptcha-response").val();
if (recaptcha === "") {
  $('.recaptcha_error').text('Recaptcha is required');
   event.preventDefault();
}
});

</script>