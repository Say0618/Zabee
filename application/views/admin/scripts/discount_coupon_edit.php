
<script>
var last_selected_type = 1;
$(document).ready(function(){
	
	//------- Auto Complete--------//
	$('#apply_on').change(function(){
		console.log(this.value);
		$('.apply_on').addClass('d-none');
		$('#type_'+this.value).removeClass('d-none');
		if(this.value == 'all'){
			$('#keyword').prop('readonly', true);
		} else {
			$('#keyword').prop('readonly', false);
		}
	});
	$('#apply_on').trigger('change');
	$("#keyword").autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: "<?php echo base_url('seller/discount/get_data');?>/"+request.term,
				dataType: "json",
				data: {
					term : request.term,
					apply_on : $("#apply_on").val()
				},
				success: function( data ) {
					$('label.error').html('');
					//$('#pn').text('');
					if(data != ""){
						response( data );
					}else{
						//$('#pn').text('No results found.');
						//response([{ label: 'No results found.', val: -1}]);
						response("");
						$('#apply_id').val('0');
					}
				}
			});
		},
		minLength: 1,
		select: function( event, ui ) {
			$("#apply_id").val(ui.item.id);
			product_id = ui.item.id;
		}
	});
	$.ui.autocomplete.prototype._renderItem = function(table, item) {
		var variant = (item.variant !='')?' ('+item.variant+')':'';
		var subDetails = (item.type == 'product')?"<br /><small>"+item.condition+""+variant+"</small>":'';
	  return $( "<tr></tr>" )
		.data( "item.autocomplete", item )
		.append( "<li>"+item.value+subDetails+"</li>" )
		.appendTo( table );
	};
	
	$("#txtFromDate").datepicker({
        numberOfMonths: 1,
		minDate: 0,
        onSelect: function(selected) {
			$("#txtToDate").datepicker("option","minDate", selected)
			if(selected !=""){
				$("#txtFromDate-error").remove();
			}
        }
    });
    $("#txtToDate").datepicker({ 
        numberOfMonths: 1,
		minDate: 0,
        onSelect: function(selected) {
           $("#txtFromDate").datepicker("option","maxDate", selected)
		   if(selected !=""){
				$("#txtToDate-error").remove();
			}
        }
    });
	$('#txtFromDate').click(function () {
        $('#txtToDate').removeAttr("disabled")
    });	
	$('input[name="discount_type"]').click(function () {
		if(last_selected_type != this.value){
			switch(this.value){
				case "0":
					$('#max_price').val(0).prop('readonly', true);
				break;
				case "1":
					$('#max_price').val(0).prop('readonly', false);
				break;
			}
			last_selected_type = this.value;
		}
    });	
	$('#generateCode').click(function () {
        $.ajax({
			url: "<?php echo base_url('seller/discount/generate_code');?>",
			type: "POST",
			dataType: "json",
			data: {},
			success: function( data ) {
				if(data.status == 1){
					$('#voucher_code').val(data.code);
				}
			}
		});
    });
	$('#Submit').click(function () {
		$("#voucher_add").submit();
		/*
        $.ajax({
			url: "<?php echo base_url('seller/discount/check_code');?>",
			type: "POST",
			dataType: "json",
			data: {code : $("#voucher_code").val()},
			success: function( data ) {
				if(data.status == 0){
					if($("#voucher_code-error").length){
						$("#voucher_code-error").css('display', 'inline-block')
					}
					$('#voucher_code-error').text('Voucher already exist.')
				}
				if(data.status == 1){
					$("#voucher_add").submit();
				}
			}
		});
		*/
    });
});

$("#voucher_add").validate({
  rules: {
		discount_title:{
		  required: true
		},
		discount_limit:{
		  required: true,
		  number: true,
		},
		fromDate:{
			required: true
		},
		toDate:{
			required: true
		},
		discount_type:{
			required: true
		},
		discount_value:{
			required: true,
			number: true,
		},
		min_amount:{
			required: true,
			number: true,
		},
		max_amount:{
			required: true,
			number: true,
		},
		keyword : {
			required: function(element){
				return $("#apply_on").val()!="all";
			}
        }
	  },
	 messages:{
		discount_title:{
			required: "Please enter voucher title" 
		},
		discount_limit:{
			required: "Please enter voucher usage limit",
			number: 'Only number is acceptable'
		},
		discount_value:{ 
			required: "Please enter a value"
		},
		fromDate:{
			required: "Valid From Date is required"
		},
		toDate:{
			required: "Valid To Date is required"
		},
		discount_value:{ 
			required: "Please enter voucher amount" ,
			number: 'Only number is acceptable'
		},
		min_amount:{ 
			required: "Please enter minimum amount" ,
			number: 'Only number is acceptable'
		},
		max_amount:{ 
			required: "Please enter maximum amount" ,
			number: 'Only number is acceptable'
		},
		keyword:{
			required: "Select required data"
		}
	},
	errorPlacement: function(error, element) 
        {
            if ( element.is(":radio")) 
            {
                error.appendTo( element.parents('.container') );
            }
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         }, 
});
$(document).on("click", ".ui-state-default", function(event){
	alert();
  $("#txtFromDate-error").html('');
  $("#txtToDate-error").html('');
});

$(document).on("click", ".resetform", function(event){
    location.reload();
});
</script>