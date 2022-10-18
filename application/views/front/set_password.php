
<div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Set Password</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
          <div class="container py-3">
            <?php error_reporting(0);
                  $not_same=$_POST['not_same'];
                  if(isset($not_same)){ ?>
            <div class="alert alert-danger">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <?php echo "Both fields should be same."; ?>
            </div>
            <?php } ?>
          <?php echo form_error('el'); ?>
        <?php
        error_reporting(0);
        if($error != ''){
                echo $error;
            }
        ?>
        <form action="<?php echo base_url()."home/set_password"; ?>" method="post" id="set_password" novalidate>
            <fieldset>
                <input type="hidden" class = "input-xlarge" id="el" name = "el" value="<?php echo $el ;?>" > 

                <input type="hidden" class = "input-xlarge" id="page" name = "page" value="<?php echo $page ;?>" >  
            
                <div class="form-group">
                    <input type="hidden" class = "input-xlarge" id="platform" name = "platform" value="" >  
                    <input class="form-control input-lg" placeholder="Enter password" name="password" value="" type="password" required id="pass">
                    <?php echo form_error('password'); ?>
                </div>
                <div class="form-group">
                    <input type="hidden" class = "input-xlarge" id="new_account" name = "new_account" value="" >
                    <input class="form-control input-lg" placeholder="Confirm password" name="confirm_password"  value="" type="password" required id="confirm_pass" >
                    <?php echo form_error('confirm_password'); ?>
                </div>
                <input class="btn btn-lg btn-primary btn-block" value="Set" type="submit">
            </fieldset>
        </form>
      </div></div>

      <!-- Modal footer -->
      <div class="modal-footer">

      </div>

    </div>
<script>
$.validator.addMethod('strongPassword', function(value, element) {
return this.optional(element) 
|| value.length >= 6
&& /\d/.test(value)
&& /[a-z]/i.test(value);
},'Use at least 6 characters including 1 alphabet and number');

$.validator.addMethod( "nowhitespace", function( value, element ) {
return this.optional( element ) || /^\S+$/i.test( value );
}, "No white space please" ); 
$("#set_password").validate({
rules: {
password: {
required: true,
strongPassword:true,
nowhitespace:true
},
confirm_password: {
required: true,
equalTo: '#pass',
nowhitespace:true
}
},
messages: {
confirm_password: {
equalTo:"Password doesn't match"
}
}
});
</script>