<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/intlTelInput-jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/intl-tel-input/utils.js'); ?>"></script>
<script>
var stateid = [];
function scroll_to_class(element_class, removed_height) {
	var scroll_to = $(element_class).offset().top - removed_height;
	if($(window).scrollTop() != scroll_to) {
		$('html, body').stop().animate({scrollTop: scroll_to}, 0);
	}
}

if($('input[name=is_tax]:checked').val() == '1'){
	$('.taxState').removeClass('d-none');
}
$('.tax').on('change',function(){
	if($('input[name=is_tax]:checked').val() == '1'){
	$('.taxState').removeClass('d-none');
}
if($('input[name=is_tax]:checked').val() == '0'){
	$('.taxState').addClass('d-none');
	var userid = $(this).attr('data-userid');
	var store_id = $(this).attr('data-sid');
	$.ajax({
		  type: "POST",
          url: "<?php echo base_url('seller/dashboard/deleteState');?>",
          dataType: "json",
		  data:{'userid':userid,'store_id':store_id},
          success : function(response) {
			  console.log(response);
          }
        });
}	
});

$(function() {
    function split( val ) {
      return val.split( /,\s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
    $( "#stateTax" ).on( "keydown", function( event ) {
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).autocomplete( "instance" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        source: function( request, response ) {
			$.ajax({
          url: "<?php echo base_url('seller/dashboard/get_states');?>/"+extractLast(request.term),
          dataType: "json",
          success : function(data) {
            response(data);
          }
        });
        },
        search: function() {
           var term = extractLast(this.value);
          if (term.length < 3) {
            return false;
          }
        },
        focus: function() {
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          terms.pop();
		  stateid.pop();
          terms.push( ui.item.value );
		  stateid.push(ui.item.id);
          terms.push( "" );
		  stateid.push( "" );
          this.value = terms.join(", ");
		  $('#state_id').val(stateid.join(","));
          return false;
        }
      });
  });



$(function() {
	var phoneCheck = false;
	var telInput2 = $("#f1-contact-phone"),
		errorMsg = $("#error-msg"),
		validMsg = $("#valid-msg");	
	var reset = function() {
	telInput2.removeClass("error"); 
		errorMsg.addClass("hide");
		validMsg.addClass("hide");
	};
	var input2 = $("#f1-contact-phone"),output = $("#output");
		input2.intlTelInput({
		nationalMode: true
	});
	telInput2.on("keyup change", function(){
		reset();
		var intlNumber = input2.intlTelInput("getNumber");
		if(input2.intlTelInput("isValidNumber") && phoneCheck){
			validMsg.removeClass('hide');
			errorMsg.addClass("hide");
		}
		else{
			errorMsg.removeClass('hide');
			validMsg.addClass("hide");
		}
	});


jQuery(document).ready(function() {

	if($('#allState').is(":checked")){
		$('#stateTax').attr("disabled",'disabled');
		$('#state_id').val("0");
	}

	$('#allState').change(function() {
		if($('#allState').is(":checked")){
			$('#stateTax').attr("disabled",'disabled');
			$('#state_id').val("0");
		}else{
			$('#stateTax').removeAttr('disabled');
		}	
	});

	if($('#zabee').is(":checked")){
		$('#zab-yes').removeAttr("class");
	}
// console.log($('input[name=is_zabee]:checked').val());
	$($('input[name=is_zabee]')).change(function() {
		if($('input[name=is_zabee]:checked').val() == 0){
			$('#zab-yes').attr("class","d-none");
		}else{
			$('#zab-yes').removeAttr("class");
		}	
	});

    $('.f1').on('submit', function(e) {
    	// fields validation
    	$(this).find('.required, input[type="password"], textarea').each(function() {
    		if( $(this).val() == "" ) {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    	// fields validation
    });
});
$("#store_logo").change(function(){
	readURL2(this);
});
$("#store_cover").change(function(){
	readURL3(this);
});
function readURL2(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
 
        reader.onload = function (e) {
            $('#storeLogoPreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function readURL3(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
 
        reader.onload = function (e) {
            $('#storeCoverPreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$(document).ready(function(e) {
	$('#country').select2({width: 'resolve'});

$.validator.addMethod( "specialChars", function( value, element ) {
	return this.optional( element ) || /^[^\\]+$/i.test( value );
}, "please enter a valid name" );
jQuery.validator.addMethod("lettersonly", function(value, element) {
	return this.optional(element) || /^[A-Za-z," "]+$/i.test(value);
}, "Please Enter alphabets"); 

jQuery.validator.addMethod("contactNumber", function(value, element, params) {
	var result = "";
	if ($.trim(telInput2.val())) {
		if (telInput2.intlTelInput("isValidNumber")) {
			var international_number = $("#f1-contact-phone").intlTelInput("getNumber");
			$("#f1-contact-phone").val(international_number);
			result = true;
		}else{
			result = false;
		}
	}
	return result;
	//return (value.replace('_','').length==15);
},'Invalid Number.');
jQuery.validator.addMethod("alphanumeric", function(value, element) {
    return this.optional(element) || /^\w+$/i.test(value);
}, "Letters, numbers, and underscores only please");
jQuery.extend(jQuery.validator.messages, {
    remote: "Store Already Exist."
});
	$("#myform").validate({
		rules: {
			store_name:{
				required: true,
				lettersonly: true,
				FirstLetter: true,
				specialChars:true,
				remote: {
					url: '<?php echo base_url("seller/dashboard/check_store_exist")?>',
					type: "post",
					data:{s_id:'<?php echo (isset($user_store->s_id) && $user_store->s_id)?$user_store->s_id:"0";?>'}
				}
			},
			legal_busniess_type:{
				required:true
			},
			country:{
				required:true
			},
			contact_phone:{
				required:true,
				// number: true, 
				minlength: 4,
				// FirstLetter: true,
				contactNumber:true
			},
			contact_email:{
				required:true,
				emailAddress: true,
				FirstLetter: true
			},
		},
		messages:{
			store_name:{
				required: "Please Enter Store Name.",
				remote: "Store Already Exist."
			},
			store_address:{
				required: "Please Enter Store Address."
			},
			legal_busniess_type:{
				required: "Please Select Busniess Type."
			},
			contact_phone:{
				required: "Please Enter Contact Phone Number."
			},
			contact_email:{
				required: "Please Enter Contact Email."
			},
		},
		errorPlacement: function(error, element) {
			console.log(element);
			if(element[0].id == "f1-contact-phone"){
				error.appendTo(element.parent().parent());
			}else{
				error.appendTo(element.parent());
			} 
		}
	});
});
jQuery.validator.addMethod("emailAddress", function(value, element) 
{
	return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\/"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
}, "Email Address Not Valid");	
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");	
jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters");
$('#store_name').keyup(function() {
	var dInput = this.value.trim();	
	dInput = dInput.replace(/ /g,"-");
	dInput = dInput.toLowerCase();
	$("#store_id").text(dInput);
	$("#h_store_id").val(dInput);

});
$(function () {
	$(":file").change(function () {
		if (this.files && this.files[0]) {
			var reader = new FileReader();
			reader.onload = imageIsLoaded;
			reader.readAsDataURL(this.files[0]);
		}
	});
});
function imageIsLoaded(e) {
	$('#myImg').attr('src', e.target.result);
};
});
</script>