<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script> 
<script>
	var accessories_id = [];
	if($("#product_id").val() != ""){
		onUpdate();
		$("#save").val("Update");
	}
	$(document).ready(function(){
		$('#accessories').select2({width: 'resolve', placeholder: function(){
        $(this).data('placeholder');}}).on('change', function() {$(this).valid();});
	});
	jQuery.validator.addMethod("FirstLetter", function(value, element) 
	{
		return this.optional(element) || /^\S.*/.test(value);
	}, "First letter cant be a space");	
	$.validator.addMethod( "specialChars", function( value, element ) {
		return this.optional( element ) || /^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/g.test( value );
	}, "" );
	$('#productForm').validate({
		rules: {
			product_name:{
				required: true,
				//checkImage:true,
				normalizer: function(value) {
					// Note: the value of `this` inside the `normalizer` is the corresponding
					// DOMElement. In this example, `this` reference the `username` element.
					// Trim the value of the input
					return $.trim(value);
				},
				minlength: 2,
				FirstLetter: true,
				// specialChars: true,
			},
			"accessories[]":{
				required:true,
			},
		},
	
		messages: {
			product_name :{required: "Please provide product name.", minlength: "Must be at least two characters long"},
		},errorPlacement: function(error, element) {
			error.appendTo(element.parent());
		},
		submitHandler: function(form) {
			var productId = $("#product_id").val();
			 if(productId && accessories_id != "" ){
				form.submit();
			}
			else if(productId == ""){
				$("#keyword-error").html("Product not found");
				$("#keyword-error").css("display","block");
			}
			else{
				$("#keyword2-error").html("accessories not found");
				$("#keyword-error").css("display","block");
			}
 		 }
	});
	//------- Auto Complete--------//
	$("#keyword").autocomplete({
		source: function( request, response ) {
			$.ajax({
			  url: "<?php echo base_url('seller/product/get_product_for_accessories');?>",
			  type: "POST",
			  data:{'accessory_id':"",'text':request.term,'product_id':""},
			  dataType: "json",
			  success: function( data ) {
				$('label.error').html('');
				if(data != ""){
					response( data );
				}else{
					$("#keyword-error").html("Product not found");
					response("");
				}
			  }
			});	
		  },
		  minLength: 1,
		  select: function( event, ui ) {
			$("#product_id").val(ui.item.id);
			$.ajax({
			  url: "<?php echo base_url('product/product_acc');?>",
			  type: "POST",
			  data:{'product_id':ui.item.id,'userid':"<?php echo $this->session->userdata("userid"); ?>"},
			  dataType: "json",
			  success: function(data) {
				  if(data){
					window.location = "<?php echo base_url("seller/product/accessories/") ?>"+data[0].id;
				  }
			  }
			});
		}
	});
	$("#keyword2").autocomplete({
		source: function( request, response ) {
			$.ajax({
			  url: "<?php echo base_url('seller/product/get_product_for_accessories');?>",
			  type: "POST",
			  data:{'accessory_id':accessories_id,'text':request.term,'product_id':$("#product_id").val()},
			  dataType: "json",
			  success: function( data ) {
				$('label.error').html('');
				if(data != ""){
					response( data );
				}else{
					$("#keyword2-error").html("accessories not found");
					response("");
				}
			  }
			});
			},
			minLength: 1,
			select: function( event, ui ) {
				createData(ui.item.id,ui.item.value);
				ui.item.value = "";
			}
	});
function createData(product_id,product_name){
	if(product_id !="" && product_name){
		$("#accessories").append("<option value='"+product_id+"'>"+product_name+"</option>");
		$("#accessories option[value='"+product_id+"']").prop("selected", true).trigger('change');
		accessories_id.push(product_id);
		$("#keyword2").val("");
	}
}
$("#keyword").keyup(function(){
  $("#keyword-error").html("");
 
});

document.getElementById('keyword').addEventListener('keydown', function (event) {
    if (event.keyCode == 8) {
        $("#jq-error").html("");
	}
});

function onUpdate(){
	$("#accessories option").each(function(index,value){
		accessories_id.push($(value).val());
	});
}
$("#accessories").on("select2:unselect", function (e) { 
	remove_id = e.params.data.id; 
	accessories_id.splice( $.inArray(remove_id,accessories_id) ,1 );
});	
</script>	
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Return Policies</h4>
      </div>
     
      <div class="modal-footer">
        <button type="button" class="btn btn-default Save" data-dismiss="modal">Save</button>  
      </div>
</div>
