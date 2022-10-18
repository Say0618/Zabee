<script>
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
				//store_id = ui.item.name+'_'+ui.item.Value;
			},
    });
$("#datepicker_from").datepicker({dateFormat: 'dd/mm/yy' });
$("#datepicker_to").datepicker({dateFormat: 'dd/mm/yy' });
// $('#datepicker_to').datepicker('setDate', 'today');

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
	
	$("#historyForm").validate({
		rules: {
      datepicker_from: {
				required: {
					depends: function(element){
						if ($('#datepicker_to').val() != ""){
							return true;
						} else {
							return false;
						}
					}		
				}		
			},
      datepicker_to: {
        required: {
					depends: function(element){
						if ($('#datepicker_from').val() != ""){
							return true;
						} else {
							return false;
						}
					}		
				},
				greaterThan: "#datepicker_from"
	  		},
				search_seller: {
        required: {
					depends: function(element){
						if ($('#search_seller_store').val() != ""){
							return true;
						} else {
							return false;
						}
					}		
				}
	  		}			
      },
      messages:{
        datepicker_to:{greaterThan: "<?php echo $this->lang->line('date_lesser') ?>"},
				search_seller:{required: "This field is required"},
      }
    });
	$(document).ready(function(){
	$('#historyForm').trigger("reset");
	$('#datepicker_to').on('change',function(){$("#datepicker_to-error").remove();});
	});
  </script>
