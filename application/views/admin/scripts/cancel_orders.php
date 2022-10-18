<script>
var oTable;
function format(orderResult){
	var stringHtml = "";
    var inc = 0;
    var inc2= 0;
    $.each(orderResult,function(index,value){
        stringHtml += '<div class="container-fluid mt-3"><table  cellpadding="5" class="table table-striped" cellspacing="0" border="0"><thead><tr>';
        $.each(value.order_group,function(ind,val){
            if(inc === 5){   
            return false;
            } else {
                stringHtml += '<th class="stripeHeader">'+val+'</th>';    
            }
            inc++;
        });
        stringHtml+='</tr></thead>';
        stringHtml += '<tbody>';
        $.each(value.order_title,function(ind,val){
            if(inc2 == 0){
                stringHtml += "<tr>";
            } else if(inc2 == 5){   
                stringHtml += "<tr>";
                inc2 =0;
            }
            stringHtml += '<td>'+val+ '</td>';
            inc2++;
            if( inc2 >= inc ){
                stringHtml +='</tr>';
                inc2 =0;
            }
        });
        stringHtml += '<tbody></table></div>';
        inc =0;
    });
    return stringHtml;
}
oTable = $('.datatables').DataTable({
			dom: 'lfrtip',
			language: { searchPlaceholder: "Search Orders" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/sales/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"aoColumnDefs": [
                {
                "aTargets": [1],
                "className":"details-control",
                "mRender": function ( data, type, full ) {
                        date = full[1].replace(/-/g,'/')
						date = new Date(date+" UTC");
                        date = new Date(date.toString());
                        return formatUTCAMPM(date);
                    },
                },
                {
                "aTargets": [3],
                "className":"details-control",
                "mRender": function ( data, type, full ) {
                        var varrient_button = '<button class="btn btn-secondary smallButton" type="button"><i class="fas fa-plus-square plusBtn" aria-hidden="true" data-isclick="0"></i></button>';
                        return  varrient_button;
                    },
                }
            ],
            "aoColumns": [
                null, {"bSortable": false}, null, null
            ],
			"fnDrawCallback": function ( oSettings ) {},
        });
function cancel_order_approve(identifier){  
    $('#btn_id').val($(identifier).attr('id'));
    $('#can_id').val($(identifier).attr('data-canid_approve'));
    $('#is_cancel').val($(identifier).attr('data-is_cancel_approve'));
    $('#user_id').val($(identifier).attr('data-userid'));
    $('#row_id').val($(identifier).attr('data-row_id'));
    $('#hubx_id').val($(identifier).attr('data-hubx_id'));
    $('#payment_type').val($(identifier).attr('data-payment_type'));
    $('#value').val(0);
    $('#confirmation').modal('show');
}		 
function cancel_order_decline(identifier){  
    $('#btn_id').val($(identifier).attr('id'));
    $('#can_id').val($(identifier).attr('data-canid_decline'));
    $('#is_cancel').val($(identifier).attr('data-is_cancel_decline'));
    $('#user_id').val($(identifier).attr('data-userid'));
    $('#row_id').val($(identifier).attr('data-row_id'));
    $('#hubx_id').val($(identifier).attr('data-hubx_id'));
    $('#payment_type').val($(identifier).attr('data-payment_type'));
    $('#value').val(1);
    $('#confirmation').modal('show');
}	
$(document).ready(function(){
    $(".confirm_del").on( "click", function() {
        var btn = $('#btn_id').val();
        var can_id = $('#can_id').val();
        var value = $('#value').val();
        var is_cancel = $('#is_cancel').val();
        var user_id = $('#user_id').val();
        var row_id = $('#row_id').val();
        var hubx_id = $('#hubx_id').val();
        var payment_type = $('#payment_type').val();
        var text = "Approved";
        if(value == 1){
            text = "Declined"
        }
        $.ajax({
        type: "POST",
        cache:false,
        dataType: "JSON",
        async:true,
        url: "<?php echo base_url()?>seller/sales/cancel_orders",
        data:{'can_id':can_id, 'is_cancel':is_cancel, 'value':value, 'user_id':user_id, 'row_id':row_id,'hubx_id':hubx_id,'payment_type':payment_type},
        success: function(response){
                $("#cancel_approve_"+can_id).remove()
                $("#cancel_decline_"+can_id).remove()
                if(text == "Approved"){
                    $("#input_"+row_id).after('<h5 style="color:grey">cancellation approved</h5>')
                } else {
                    $("#input_"+row_id).after('<h5 style="color:red">cancellation declined</h5>')
                }
                $('#confirmation').modal('hide');
            }	
        });
    });
    $('.dataTables_length select').addClass('form-control');
        $('.datatables tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var tdi = tr.find("i.plusBtn");
        var row = oTable.row(tr);
        var order_id = row.data()[0];
        //console.log(row.data());
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
            tdi.first().removeClass('fa-minus-square');
            tdi.first().addClass('fa-plus-square');
        } else {
            // Open this row
            //var isClick = tdi.attr('data-isclick');
            //if(isClick == 0){
                $.ajax({
                    type: "POST",
                    cache:false,
                    dataType: "JSON",
                    async:true,
                    url: "<?php echo base_url('seller/sales/get_cancel_orders_info');?>",
                    data:{'o_id':order_id},
                    success: function(data){ 
                        if(data.row > 0){
                            row.child(format(data.result)).show();
                            row.child().addClass('changeStripe');
                            tr.addClass('shown');
                            tdi.first().removeClass('fa-plus-square');
                            tdi.first().addClass('fa-minus-square');
                            //tdi.attr('data-isclick',1);		
                        }
                    
                    }
                });
        }
    });
    oTable.on("user-select", function (e, dt, type, cell, originalEvent) {
		if ($(cell.node()).hasClass("details-control")) {
			e.preventDefault();
		}
	});

});
	 
</script>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Are you sure?</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				Are you sure?
			</div>				
		  </div>
		  <div class="modal-footer">
            <input type="hidden" id="can_id" />
            <input type="hidden" id="btn_id" />
            <input type="hidden" id="hubx_id" />
            <input type="hidden" id="is_cancel" />
            <input type="hidden" id="value" />
            <input type="hidden" id="user_id" />
            <input type="hidden" id="row_id" />
            <input type="hidden" id="payment_type" />
            <a href="#" class="btn" data-dismiss="modal">No</a>
			<Button type = "submit" class="btn btn-primary confirm_del">Yes</Button>
		  </div>
		</div>
	</div>
</div>
