<div class="container d-none d-sm-block">
    <div class="row">
        <div class="w-100 text-center mb-3">
            <hr />
            <a href="https://www.facebook.com/"><span class="social-icons fb-si"><i class="fab fa-facebook-f"></i></span></a>
            <a href="https://twitter.com/"><span class="social-icons tw-si"><i class="fab fa-twitter"></i></span></a>
            <a href="https://www.google.com/"><span class="social-icons gp-si"><i class="fab fa-google"></i></span></a>
        </div>
    </div>


  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style=" padding-right: 17px;">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="width: 90%;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      </div>
      <div style="padding: 11px;">
    <h5 style="color: red;"><?php echo $this->session->flashdata('check_for_email');?> </h5>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<div id="myModal2" class="modal fade"  aria-hidden="true">
  <div class="modal-dialog modal-confirm">
    <div class="modal-content" style="padding: 34px;">
      <div class="modal-body text-center">
        <h4><strong>Thankyou!</strong></h4>
        <p style="font-size: 20px;"><?php echo $this->session->flashdata('newsletter_subscribe'); ?>.</p>
        <button class="btn btn-success" data-dismiss="modal"><span>Close</span></button>
      </div>
    </div>
  </div>
</div>

<form id="newsletter1"  method="post" action="<?php echo base_url('Home/saveEmailforNewsandOffers'); ?>">
	<div class="row newsletter-row" id="newsletter">
		<div class="col-sm-4 newsletter-heading">
			<p><?php echo $this->lang->line('newsletter_sign_up');?></p>
			<span><?php echo $this->lang->line('newsletter_text');?></span>
		</div>
		<div class="col-sm-8">
			<div class="input-group h-75">

			  <input  id="validatefield" value="<?php echo set_value('email'); ?>" type="email" class="form-control rounded-0" name="newsemail" placeholder="<?php echo $this->lang->line('newsletter_box');?>"  autocomplete="off" required >
			  <div class="input-group-append" >
				<button type="submit" class="btn btn-newsletter pl-5 pr-5  btn-hover color-blue" type="button"><?php echo $this->lang->line('signup');?></button>
			  </div>
			</div>
			<div col="col-sm-6" id="emailvalidation" style="color:red;margin: 0 auto;"></div>
			<div col="col-sm-6" style="color:red;margin: 0 auto;"> <?php echo validation_errors(); ?> </div>
		<br>
		</div>
	</div>
</form>
	<div class="row">
  <?php if(isset($offer_left[0]->offer_image) && $offer_left[0]->offer_image != ""){
    $left = $offer_left[0]->offer_image;
  }else{ $left = "";}
  if(isset($offer_right[0]->offer_image) && $offer_right[0]->offer_image != ""){
    $right = $offer_right[0]->offer_image;
  }else{
    $right = "";
  }?>
        <div class="col-sm-6 p-0 pr-3">
            <img class="img img-fluid" style="min-height: 109px;max-height: 109px; width: 100%;" src="<?php echo image_url('special_offer/'.$left)?>"  />
        </div>
        <div class="col-sm-6 p-0 pl-3">
            <img class="img img-fluid" style="min-height: 109px;max-height: 109px; width: 100%;" src="<?php echo image_url('special_offer/'.$right)?>"  />
        </div>
    </div>
</div>
<div class="container mt-3 mb-3 d-sm-none text-center">
    <div class="row mb-4">
        <div class="w-100">
            <hr />
            <a href="https://www.facebook.com/"><span class="social-icons fb-si"><i class="fab fa-facebook-f"></i></span></a>
            <a href="https://twitter.com/"><span class="social-icons tw-si"><i class="fab fa-twitter"></i></span></a>
            <a href="https://www.google.com/"><span class="social-icons gp-si"><i class="fab fa-google"></i></span></a>
        </div>
    </div>
    <div class="row newsletter-row mb-3">
        <div class="col newsletter-heading">
            <h4>NEWSLETTER SIGN UP</h4>
            <span>Signup to be the first to know about our deals.</span>
        </div>
    </div>
    <div class="row">
        <div class="col">

            <input type="text" class="form-control mob-news-radius mb-3 text-center" placeholder="Type your email here to get newsletter">
            <button class="btn btn-newsletter mob-news-radius  border-0 pl-4 pr-4" type="button">SIGN UP</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-2">
            <img class="img img-fluid" src="<?php echo image_url('special_offer/'.$left)?>"  />
        </div>
        <div class="col-12 mb-2">
            <img class="img img-fluid" src="<?php echo image_url('special_offer/'.$right)?>"  />
        </div>
    </div>
</div>
<script type="text/javascript">

 $('#newsletter1').submit(function (e) {
    var email = $("#validatefield").val();
    isValidEmailAddress(email);
});



</script>