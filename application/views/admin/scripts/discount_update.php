<script>
$(document).ready(function(){
	$("#valueOfPercent").rules("add",{range:[1,99]});
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
});
	var is_checked = true;
	$('.radio').each(function(){
		is_checked = is_checked && $(this).is(':checked');
	});
	if ( ! is_checked ){
		
	}

$("#myform").validate({
  rules: {
		valueOfPercentOrFixed:{
		  required: true,
		},
		fromDate:{
			required: true
		},
		toDate:{
			required: true
		}
	  },
	  messages:	
        {
		  valueOfPercentOrFixed:{ 
			required: "Please enter a value" 
		  },
		  fromDate:{ 
			required: "Valid From Date is required" 
		  },
		  toDate:{ 
			required: "Valid To Date is required" 
		  },
		  FixedorPercent:{ 
			required: "Please select an option" 
		  }
        },
	   errorPlacement: function(error, element) 
        {
            if ( element.is(":radio") ) 
            {
                error.appendTo( element.parents('.container') );
            }
            else 
            { // This is the default behavior 
                error.insertAfter( element );
            }
         }, 
});
$(document).on("click", ".resetform", function(event){
  $("label.error").html('');
});

$(".discount_type").on("click",function(){
	var type = $(this).val();
	if(type == "fixed"){

		$("#valueOfPercent").rules("remove","range");
	}else{
		$("#valueOfPercent").rules("add",{range:[1,99]});
	}
});
/*function validateForm() {
    var radios = document.getElementsByName("yesno");
    var formValid = false;

    var i = 0;
    while (!formValid && i < radios.length) {
        if (radios[i].checked) {formValid = true;}
        i++;        
    }

    if (!formValid) alert("Must check some option!");
    return formValid;
}*/
</script>