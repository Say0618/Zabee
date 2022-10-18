<!-- Footer -->
<footer class="footer page-footer font-small indigo">
    <!-- Footer Links -->
    <div class="container">
        <div class="row">
            <div class="mx-auto">
            	<a href="<?php echo base_url("privacypolicy");?>"><?php echo $this->lang->line('privacy_policy');?></a>
            </div>
            <div class="mx-auto">
            	<a href="<?php echo base_url("termsandcondition");?>"><?php echo $this->lang->line('terms_condition');?></a>
            </div>
            <div class="mx-auto">
            	<a href="<?php echo base_url("faq");?>"><?php echo $this->lang->line('faq');?></a>
            </div>
            <div class="mx-auto">
            	<a href="<?php echo base_url("contact_us");?>"><?php echo $this->lang->line('support');?></a>
            </div>
            <?php if($this->isloggedin && $_SESSION['store_id'] == ""){?>
              <div class="mx-auto">
                <a href="<?php echo base_url('seller')?>"><?php echo "Sell on zabee";?></a>
              </div>
            <?php }?>
        </div>
    </div>
<!-- Footer Links -->
<div class="footer-copyright text-center"><?php echo date("Y"); ?> © <?php echo $this->lang->line('copyright');?> Zab.ee</div>
</footer>
<!-- Footer -->
 <!--  fb email required popup -->
<div id="fbmodal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" >
  <div class="modal-dialog">

	<!-- Modal content-->
	<div class="modal-content">
	  <div class="modal-header">
		<h4 class="modal-title"><?php echo $this->lang->line('notice');?></h4>
	  </div>
	  <div class="modal-body">
		<p><?php echo $this->lang->line('enter_email');?></p>
			<div class="form-group">
		  <input type="email" id="req_email" name="req_email" value=""> </input>
			</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal" id="fb_email" onclick="fb_req()" ><?php echo $this->lang->line('save');?></button>
	  </div>
	</div>

  </div>
</div>
<div id="countryModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" >
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-body">
        <p><?php echo $this->lang->line('select_country');?></p>
            <div class="form-group">
                <select class="form-control select2" id="countrySel"></select>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" ><?php echo $this->lang->line('cancel');?></button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" id="save_contry" ><?php echo $this->lang->line('save');?></button>
      </div>
    </div>

  </div>
</div>

<div class="modal fade" id="invalid">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('err');?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <?php echo $this->lang->line('invalidemail_pass');?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="add_password">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>

  
</div>

<div class="modal fade" id="invalidEmailModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('err');?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <?php echo $this->lang->line('email_not_reg');?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="invalidCodeModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('err');?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
      <?php echo $this->lang->line('code_not_valid');?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="passwordChanged">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('success');?></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
    <?php echo $this->lang->line('pass_changes');?>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>

    </div>
  </div>
</div>
 <!--  fb email required popup -->
 <div class="modal fade" id="message-notification" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color: orange;font-variant: small-caps;font-weight: bold;"><?php echo $this->lang->line('notification');?>!</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                        <strong style="color:green;font-variant: small-caps;" id="change-message"><?php echo $this->lang->line('sent_msg');?></strong>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div id="myModal3" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Wish List via Category Name</h4>
		<button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
		<form id="myform" name="myForm" role="form">
			<div class = "" id = "">
			<?php if(isset($wishlist_categories) && $wishlist_categories != ""){ ?>
				<?php //print_r($selWishCatNames); ?>
				<label><h6>Categories: </h6></label>&nbsp;
				<select name='alreadyExistingCategories' id='alreadyExistingCategories' class='alreadyExistingCategories form-control'>
					<?php for($i = 0; $i < count($wishlist_categories); $i++){ ?>
					<option value="<?php echo $wishlist_categories[$i]->id ?>"><?php echo $wishlist_categories[$i]->category_name ?></option>
					<?php } ?>
				</select>
			<?php } else {?>	
				<label><h6><?php echo $this->lang->line('categories');?>: </h6></label>&nbsp;
				<select class='alreadyExistingCategories'>
				  <option value="0" selected disabled><?php echo $this->lang->line('none_available');?></option>
				</select>
			<?php } ?>			
			</div>
			<input type="hidden" name="modal_product_id" id="modal_product_id" value="">
			<input type="hidden" name="modal_product_v_id" id="modal_product_v_id" value="">
		</form>
      </div>
      <div class="modal-footer">
        <!--<button type="" class="btn btn-primary ml-3" id="add" style="float:left">Add</button>-->
        <button type="button" class="btn btn-success ml-3" id="categorySubmit"><?php echo $this->lang->line('save');?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>
    </div>

  </div>
</div>

<!--  dealsSubscription popup -->
<div id="dealsSubscription" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center"><?php echo $this->lang->line('deals_subscribe_title');?></h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form name="dealsSubscriptionForm" id="dealsSubscriptionForm" method="post" action="<?php echo base_url('save_subscription'); ?>">
				<div class="form-group row">
					<div class="col-md-12">
						<input type="text" name="deals_sub_name" id="deals_sub_name" class="form-control" placeholder="<?php echo $this->lang->line('full_name');?>" />
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-6">
						<input type="text" name="deals_sub_email" id="deals_sub_email" class="form-control" placeholder="<?php echo $this->lang->line('email_deal_subscription');?>" />
						<?php echo $this->session->flashdata('deals_email'); ?>
					</div>
					<div class="col-md-6">
						<input type="text" name="deals_sub_phone" id="deals_sub_phone" class="form-control" placeholder="<?php echo $this->lang->line('phone_deal_subscription');?>" />
						<?php echo $this->session->flashdata('deals_phone'); ?>
					</div>
				</div>
				<hr />
				<div class="custom-control custom-radio">
					<input type="radio" class="custom-control-input" name="deals_selection" value="0" checked id="deals_selection_all">
					<label class="custom-control-label" for="deals_selection_all"><?php echo $this->lang->line('all_deal_selection');?></label>
				</div>
				<div class="custom-control custom-radio">
					<input type="radio" class="custom-control-input" name="deals_selection" value="1" id="deals_selection_manual">
					<label class="custom-control-label" for="deals_selection_manual"><?php echo $this->lang->line('few_deal_selection');?></label>
				</div>
				<div class="form-check deals_cat_list d-none">
					<?php foreach($parentCategory as $pc){ ?>
					<?php if($pc->is_private != "1"){ ?>
					<div class="custom-control custom-checkbox deal_cat_item">
						<input type="checkbox" class="custom-control-input" name="deal_cat_ids[]" id="deal_cat_id_<?php echo $pc->category_id; ?>" value="<?php echo $pc->category_id; ?>">
						<label class="custom-control-label" for="deal_cat_id_<?php echo $pc->category_id; ?>"><?php echo $pc->category_name;?></label>
					</div>
					<?php } } ?>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" onclick="closedDealsSignUp();">No thanks, I don't like deals</a>
				<button type="submit" class="btn sans-bold btn-hover color-orange"><?php echo $this->lang->line('signup');?></button>
			</div>
		</div>
	</div>
</div>