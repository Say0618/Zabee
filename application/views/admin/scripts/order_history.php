<script>
$( "#search_product" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "<?php echo base_url('seller/product/get_product');?>/"+request.term,
          dataType: "json",
          data: {
            'text': request.term
          },
          success : function(data) {
            response(data);
          }
        });
      },
      minLength: 1,
    	select: function( event, ui ) { 
				$('#product_id').val(ui.item.id);
			},
    });
$( "#search_seller_store" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "<?php echo base_url('seller/product/get_seller_store');?>/"+request.term,
          dataType: "json",
          data: {
            'text': request.term
          },
          success : function(data) {
            response(data);
          }
        });
      },
      minLength: 1,
    	select: function( event, ui ) {
				$('#store_id').val(ui.item.id);
			},
    });
$("#datepicker_order").datepicker({dateFormat: 'dd/mm/yy' });
$("#datepicker_from").datepicker({dateFormat: 'dd/mm/yy' });
$("#datepicker_to").datepicker({dateFormat: 'dd/mm/yy' });
$('#datepicker_to').datepicker('setDate', 'today');
$('#datepicker_from').datepicker('setDate', 'today');


function makeTime(value){
				value = value.split('/');
				value = value[2]+'-'+value[1]+'-'+value[0];
				return new Date(value);
			}
jQuery.validator.addMethod("greaterThan", function(value, element, params) {
  if($('#datepicker_to').val() == "" && $('#datepicker_from').val() == ""){
				return true;
			}
			else{
				return makeTime(value).getTime() >= makeTime(jQuery(params).val()).getTime()
			}
	},'Must be greater than {0}.');
	
	$("#OrderFiltersForm").validate({
		rules: {
      datepicker_from: {
				required:true
				// required: {
				// 	depends: function(element){
				// 		if ($('#datepicker_to').val() != ""){
				// 			return true;
				// 		} else {
				// 			return false;
				// 		}
				// 	}		
				// }		
			},
      datepicker_to: {
				required:true,
        // required: {
				// 	depends: function(element){ 
				// 		if ($('#datepicker_from').val() != ""){
				// 			return true;
				// 		} else {
				// 			return false;
				// 		}
				// 	}		
				// },
				greaterThan: "#datepicker_from"
	  		},
      },
      messages:{
        datepicker_to:{greaterThan: "Date Created from must be lesser then Date To"},
      }
    });
$(document).ready(function(){
	$('#OrderFiltersForm').trigger("reset");
	$('#datepicker_to').on('change',function(){$("#datepicker_to-error").remove();});
	$('#datepicker_from').on('change',function(){$("#datepicker_from-error").remove();});

	});
</script>