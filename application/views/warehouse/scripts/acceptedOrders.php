<script>
var total;
var t;
function format(orderResult){
	console.log(orderResult);
var stringHtml = "";
var inc = 0;
var inc2= 0;
$.each(orderResult,function(index,value){
    console.log(index);
	stringHtml += '<div class="container-fluid mt-3"><h5>Seller: '+index+'</h5><table  cellpadding="5" class="table table-striped" cellspacing="0" border="0"><thead><tr>';
	$.each(value.order_group,function(ind,val){
        // console.log("inc: "+inc);
        // console.log("ind: "+ind);
        // console.log("val: "+val);
        if(inc === 5){   
           return false;
        } else {
            stringHtml += '<th class="stripeHeader">'+val+'</th>';    
        }
        inc++;
	});
	stringHtml+='</tr></thead>';
	//for(var i=0; i<varients.length; i++){
	stringHtml += '<tbody>';
	$.each(value.order_title,function(ind,val){
        // console.log(inc2);
        // console.log(ind);
		if(inc2 == 0){
			stringHtml += "<tr>";
        } else if(inc2 == 5){   
            stringHtml += "<tr>";
            inc2 =0;
        }
		stringHtml += '<td>'+val+ '</td>';
		inc2++;
		// $(val).each(function(i,v){
		// 	stringHtml += '<td>'+v+ '</td>'; 
		// 	inc2++;
		// });
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
$.fn.dataTable.pipeline = function ( opts ) {
    // Configuration options
    var conf = $.extend( {
        pages: 5,     // number of pages to cache
        url: '',      // script url
        data: null,   // function or object with parameters to send to the server
                      // matching how `ajax.data` works in DataTables
        method: 'POST' // Ajax HTTP method
    }, opts );
 
    // Private variables for storing the cache
    var cacheLower = -1;
    var cacheUpper = null; 
    var cacheLastRequest = null;
    var cacheLastJson = null;
 
    return function ( request, drawCallback, settings ) {
    	var ajax          = false;
        var requestStart  = request.start;
        var drawStart     = request.start;
        var requestLength = request.length;
        var requestEnd    = requestStart + requestLength;
		request.recordsTotal = total;
        if ( settings.clearCache ) {
            // API requested that the cache be cleared
            ajax = true;
            settings.clearCache = false;
        }
        else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
            // outside cached data - need to make a request
            ajax = true;
        }
        else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                  JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                  JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
        ) {
            // properties changed (ordering, columns, searching)
            ajax = true;
        }
         
        // Store the request for checking next time around
        cacheLastRequest = $.extend( true, {}, request );
 
        if ( ajax ) {
            // Need data from the server
            if ( requestStart < cacheLower ) {
                requestStart = requestStart - (requestLength*(conf.pages-1));
 
                if ( requestStart < 0 ) {
                    requestStart = 0;
                }
            }
             
            cacheLower = requestStart;
            cacheUpper = requestStart + (requestLength * conf.pages);
 
            request.start = requestStart;
            request.length = requestLength*conf.pages;
 
            // Provide the same `data` options as DataTables.
            if ( $.isFunction ( conf.data ) ) {
                // As a function it is executed with the data object as an arg
                // for manipulation. If an object is returned, it is used as the
                // data object to submit
                var d = conf.data( request );
                if ( d ) {
                    $.extend( request, d );
                }
            }
            else if ( $.isPlainObject( conf.data ) ) {
                // As an object, the data given extends the default
                $.extend( request, conf.data );
            }
 
            settings.jqXHR = $.ajax( {
                "type":     conf.method,
                "url":      conf.url,
                "data":     request,
                "dataType": "json",
                "cache":    false,
                "success":  function ( json ) {
                    cacheLastJson = $.extend(true, {}, json);
 
                    if ( cacheLower != drawStart ) {
                        json.data.splice( 0, drawStart-cacheLower );
                    }
                  	json.data.splice( requestLength, json.data.length );
                    drawCallback( json );
                	
				}
            } );
        }
        else {
            json = $.extend( true, {}, cacheLastJson );
           	json.draw = request.draw; // Update the echo for each response
			json.data.splice( 0, requestStart-cacheLower );
            json.data.splice( requestLength, json.data.length );
 			drawCallback(json);
        }


    }
};
 
// Register an API method that will empty the pipelined data, forcing an Ajax
// fetch on the next draw (i.e. `table.clearPipeline().draw()`)
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    } );
} );
$(document).ready(function(e) {
   //var action;
   var a=1;
   var start;
   //var invite_button;
 //  var classname;
   var end;
  	t = $('.datatables').DataTable({
		"language": {
			"Search": "<?php echo $this->lang->line('search');?>:",
            "lengthMenu": "<?php echo $this->lang->line('display');?> _MENU_ <?php echo $this->lang->line('records_perpage');?>",
            "zeroRecords": "<?php echo $this->lang->line('not_found');?>",
            "info": "<?php echo $this->lang->line('showing');?> <?php echo $this->lang->line('of');?> _PAGES_",
            "infoEmpty": "<?php echo $this->lang->line('no_rec');?>",
            "infoFiltered": "(<?php echo $this->lang->line('filtered_from');?> _MAX_ <?php echo $this->lang->line('total_records');?>)",
            "paginate": {
                "previous": "<?php echo $this->lang->line('Previous');?>",
                "next" : "<?php echo $this->lang->line('next');?>"
                    },
        },
		"processing": true,
        "serverSide": true,
		"ajax": $.fn.dataTable.pipeline( {
            url: '<?php echo base_url('warehouse/sales/acceptedOrders')?>',
            pages: 5 ,// number of pages to cache
            method: "POST"
		} ),

		"columnDefs": [
			{
                "render": function ( data, type, row ) {
					return "";
                },
                
                "targets": 0
            },
            {
                "render": function ( data, type, row ) {
                 return  row.order_id;
            	},
                "targets": 1
            },
			{
                "render": function ( data, type, row ) {
						var date = "";
						row.created = row.created.replace(/-/g,'/')
						date = new Date(row.created+" UTC");
						date =  new Date(date.toString());
						return formatAMPM(date);
					
				},
                "targets": 2
            },
			{
                "render": function ( data, type, row ) {
					// console.log(row);
                    return row.shipping.address_1+"<br>"+row.shipping.name+"<br>"+row.shipping.phone;
				},
                "targets": 3
            },
			{
                "render": function ( data, type, row ) {
					return row.item_gross_amount;
                },
                "targets": 4
            },
			{
                "render": function ( data, type, row ) {
                    var checked =jparser(row);
                    var button = "<button  type='button' class='btn btn-primary' data-toggle='modal' onClick='openModal("+checked+")' data-target=''><?php echo $this->lang->line('view');?></button>";
                    return button;
                }, 
               
                "targets": 5
            },
            {
                "render": function ( data, type, row ) {
                   // var checked =jparser(row);
                    var button = "<a href='<?php echo base_url("warehouse/sales/getInvoice/")?>"+row.order_id+"' class='btn btn-info' role='button'><?php echo $this->lang->line('get_invioce');?></a>";
                    return button;
                },
            
                "targets": 6
            },
            
            {
                "render": function ( data, type, row ) {
                    var checked =jparser(row);
                    var button = "<button  type='button' class='btn btn-primary' data-toggle='modal' onClick='openWarehouseModal("+checked+")' data-target=''><?php echo $this->lang->line('select_warehouses');?></button>";
                    return button;
                },
               
                "targets": 7
            },
            {
                "render": function ( data, type, row )
                 {
                    console.log(row);
                    /*var checked =row.order_id;
                    var decline = "<?php //echo base_url('warehouse/sales/accept_order/2/')?>"+row.order_id;
                    var approve_button = '<div class="text-center"><a class="btn btn-success"  data-productid="" data-sellerid="" onclick="askApprove(this)" style="color:white; width:95px;">Approve</a></div>';
					var decline_button = '<div class="text-center"><a class="btn btn-danger"  href="'+decline+'" style="color:white; width:95px;margin-top:10px;">Decline</a></div>';
					return approve_button+decline_button;*/
                    var varrient_button = '<button class="btn btn-secondary smallButton" type="button"><i class="fas fa-plus-square plusBtn" aria-hidden="true" data-isclick="0"></i></button>';
                    return  varrient_button;
                },
                "className":"details-control text-center",
                "targets": 8
            },
        ],
		"fnDrawCallback": function ( oSettings ) {
			var d = 0;
			for ( var i=start, iLen=oSettings.aiDisplay.length ; i<end ; i++ )
			{
				$('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[d] ].nTr ).html( i+1 );
				d++;
			}
			total = oSettings._iRecordsDisplay;
			/*$(".description").each(function () {
				text = $(this).text();
				if (text.length > 200) {
					$(this).html(text.substr(0, 25) + '<span class="elipsis">' + text.substr(25) + '</span><a class="elipsis" href="#">...</a>');
				}
			});*/
		},
		 "fnFooterCallback": function( nFoot, aData, iStart, iEnd, aiDisplay ) {
			start = iStart;
			end = iEnd;
		},"initComplete": function(settings, json) {
			/*$(".description").each(function () {
				text = $(this).text();
				if (text.length > 200) {
					$(this).html(text.substr(0, 25) + '<span class="elipsis">' + text.substr(25) + '</span><a class="elipsis" href="#">...</a>');
				}
			});
			$(".description > a.elipsis").click(function (e) {
				e.preventDefault(); //prevent '#' from being added to the url
				$(this).prev('span.elipsis').fadeToggle(500);
			});*/
   		}
	});
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','<?php echo $this->lang->line('search');?>...');
    $('.dataTables_length select').addClass('form-control');
    $('.datatables tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var tdi = tr.find("i.plusBtn");
        var row = t.row(tr);
        //console.log(row.data().order_id);
        var order_id = row.data().order_id;
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
                    url: "<?php echo base_url('warehouse/sales/get_order_plus_button');?>",
                    data:{'o_id':order_id, 'data_from':'accept'},
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
    t.on("user-select", function (e, dt, type, cell, originalEvent) {
		if ($(cell.node()).hasClass("details-control")) {
			e.preventDefault();
		}
	});
});

function openTrackingInput(Id){
    $("#trackingIdModal").modal('show');
    var accept = "<?php echo base_url('seller/sales/accept_order/1/')?>"+Id;
    $('#trackingAccept').attr("href",accept);
}
function addTrackingNumber(){
    var tracking_number = $('#trackingNumber').val();
    var h = $('#trackingAccept').attr("href");
    acceptId = h+"/"+tracking_number;
    $('#trackingAccept').attr("href",acceptId);
}
function openWarehouseModal(data){   
    var warehouse_ids = [];
    var warehouse_name = [];
    var product_name = [];
    var div = [];
    var title = [];
    var prev = [];
    for(var i = 0; i <  data.orders.length; i++){
        warehouse_ids[i] = data.orders[i].warehouse_id;
        warehouse_name[i] = data.orders[i].warehouse_name;
        product_name[i] = data.orders[i].product_name;
    }
    var distinct_prod_name = [...new Set(product_name)];
    for(var i = 0; i < distinct_prod_name.length; i++){
        div[i] = $("<div>", {class: "col-sm-12 <?php echo $this->lang->line('warehouses_for');?>"+i, id: "<?php echo $this->lang->line('warehouses_for');?>"+i});
        title[i] = $("<div>", {class: "title_for_"+i, id: "title_for_"+i});
        ctrl = '';
        for(var j = 0; j < data.orders.length; j++){
            if(data.orders[j].product_name == distinct_prod_name[i]){
                prev[j] = data.orders[j].warehouse_id;
                if(prev[j] != prev[j-1]){
                    ctrl += '<div class="col-sm-12"/>'
                            +'<input type="checkbox" class="myCheck" name="'+data.orders[j].warehouse_name+'" id="'+i+'_'+data.orders[j].warehouse_name+'_'+ data.orders[j].warehouse_id+'" data-prodname="'+distinct_prod_name[i]+'" data-wareid="'+data.orders[j].warehouse_id+'" data-spid="'+data.orders[j].sp_id+'" data-reqQty="'+data.orders[j].qty+'" onclick="return OptionsSelected(this)" />'
                            +'<span class="warehouse-name">&nbsp;'+data.orders[j].warehouse_name+'</span>'
                        +'</div>';
                }
            }
        }
        prev.length = 0;
        $('.WarehousesHere').append(div[i]);
        $('.Warehouses_for_'+i).append(title[i]);
        $('.Warehouses_for_'+i).append(ctrl);
        $('.title_for_'+i).text("Warehouses for Product: "+distinct_prod_name[i]);
    }     
    $('#warehouseModal').modal('show');
}
function OptionsSelected(me){
    var ware_id = $(me).attr("data-wareid");
    var sp_id = $(me).attr("data-spid");
    div_id = handlingIDs(me.id);
    if(me.checked) {
        $.ajax({
        type: "POST",
        cache:false,
        dataType: "JSON",
        async:true,
        url: "<?php echo base_url()?>warehouse/sales/get_quantity_from_warehouse",
        data:{'warehouse_id':ware_id, 'sp_id':sp_id},
        success: function(response){
                var x = "";
                if(response != 0){
                    x = $('<div class="col-sm-12 div_'+div_id+'"/>')
                        .html("<div class='col-sm-12 warehouseReq' ><div class='warehouseReq-2'><?php echo $this->lang->line('quantity_requested');?>: <span class='qtyReq'>"+$(me).attr("data-reqQty")+"</span></div><div class='qtyAva' > Quantity available in "+me.name+" for "+$(me).attr("data-prodname")+": <span class='warehouseReq-3' >"+response+"</span></div></div>")
                        .append($('<input type="number" />')
                        .attr({
                            name: 'test',
                            id: "qty_"+div_id,
                            class: "warehouse-qty",
                            max: response,
                            min: 1,
                            "data-spid": sp_id
                        }));
                    $('.qty-heading').show();
                    $('.qty-confirm').prop('disabled', false);
                } else {
                    x = $('<div id="warn_'+div_id+'" class="col-sm-12 div_'+div_id+' warn"/>')
                        .html("<h6 style='color:red'><?php echo $this->lang->line('quantity_0');?> "+$(me).attr("data-prodname")+" in warehouse inventory "+me.name+"</h6>");
                }
                $('.WarehousesQtyHere').append(x);
            }	
        });
        
    } else {
        $('#qty_'+div_id).remove();
        $('.div_'+div_id).remove();
        setTimeout(function () {
            if(!$('.modal-body .WarehousesQtyHere div').hasClass('warn')){
                $('.qty-confirm').removeAttr("disabled");
        }
        }, 200);
    }
    var inputElements = [].slice.call(document.querySelectorAll('.myCheck'));
    var checkedValue = inputElements.filter(chk => chk.checked).length;
    var myWarn = "";
    if(checkedValue < 1){
        $('.qty-heading').hide();
        $('.qty-confirm').prop('disabled', true);
    } else if(checkedValue > 0){
        setTimeout(function () {
            if($('.modal-body .WarehousesQtyHere div').hasClass('warn')){
            $('.qty-confirm').prop('disabled', true);
        }
        }, 200);
    }
}
$(document).on('hide.bs.modal','#warehouseModal', function () {
    $(".WarehousesHere").empty();
    $('.qty-heading').hide();
    $('.WarehousesQtyHere').empty();
});
$('.qty-confirm').on('click', function(){
    ware_values = [];
    var abort = false;
    for(var i = 0; i < $('.WarehousesQtyHere input').length; i++){
        if($('.WarehousesQtyHere input:eq('+i+')').val() <= $('.WarehousesQtyHere input:eq('+i+')').attr("max")){
            ware_values[i] = $('.WarehousesQtyHere input:eq('+i+')').val()+
                        "+"+$('.WarehousesQtyHere input:eq('+i+')').attr("id")+
                        "+"+$('.WarehousesQtyHere input:eq('+i+')').attr("data-spid");
        } else if(isNaN($('.WarehousesQtyHere input:eq('+i+')').val())){
            abort = true;
        } else {
            abort = true;
        }
    }
    if(!abort){
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:true,
            url: "<?php echo base_url()?>warehouse/sales/subt_quantity_from_warehouse",
            data:{'ware_values':ware_values},
            success: function(response){
                $('#warehouseModal').modal('toggle');
            }	
        });
    } else {
        $('.error').show();
    }
});
$("input[type='number']").keypress(function(){
    $('.error').hide();
});
function handlingIDs(id){
    var string  = id;
    var arr = string.split(''); 
    if(arr.includes(" ")){
        id=id.replace(/ /g,"_");
    }
    return id;
}
function askApprove(identifier){
	$('#approve_modal').modal('show');
	$("#td_id").val($(identifier).data('tdid'));
	$("#t_id").val($(identifier).data('tid'));
	$("#s_id").val($(identifier).data('sellerid'));
}
$(document).on('click','#acceptBtn',function(){
    var trans_details_Id = $("#td_id").val();
	var sellerid = $("#s_id").val();
	var order_id = $("#t_id").val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('warehouse/sales/approveOrder');?>",
		data: {'trans_details_Id':trans_details_Id,'sellerid' :sellerid, 'order_id':order_id},
		success: function(response){
            if(response){
                $('#approve_modal').modal('hide');
                    $('a[data-tdid='+trans_details_Id+']').parent().html('<h6 style="color:green">Approve</h6>');
                    $('a[data-tdid='+trans_details_Id+']').remove();
				// setTimeout(function(){
				// 	location.reload();
				// }, 200); 
			}
		}
	});
});
function askDecline(identifier){
	$('#decline_modal').modal('show');
	// $("#declined_reason").next(".error").remove();
	$("#td_id").val($(identifier).data('tdid'));
	$("#s_id").val($(identifier).data('sellerid'));
}
$(document).on('click', '#rejectBtn', function(){
	var trans_details_Id = $("#td_id").val();
	var sellerid = $("#s_id").val();
	var reason = $("#declined_reason").val();
	reason = reason.trim();
	// if(reason == ""){
	// 	$("#declined_reason").next(".error").remove();
	// 	$("#declined_reason").after('<span class="error">Please add reason for reject.</span>');
	// 	return false;
	// }else{
		$.ajax({
			type: "POST",
			cache:false,
			dataType: "JSON",
			async:true,
			url: "<?php echo base_url('warehouse/sales/declineOrder');?>",
			data: {'trans_details_Id':trans_details_Id,'sellerid' :sellerid},
			success: function(response){
				if(response){
                    $('#decline_modal').modal('hide');
                    $('a[data-tdid='+trans_details_Id+']').parent().html('<h6 style="color:red">Declined</h6>');
                    $('a[data-tdid='+trans_details_Id+']').remove();
				}
			}
		});
	// }
});
function askRefund(identifier){
    $('#refund-modal').modal('show');
    $("#order_id").val($(identifier).data('order_id'));
	$("#tdId").val($(identifier).data('tdid'));
}
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
$('#refund-order').on('click',function(){
	var order_id = $("#order_id").val();
	var tdID = $("#tdId ").val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('checkout/ProcessRefund');?>",
		data: {'order_id':order_id, 'td_row_id':tdID},
		success: function(response){
            setTimeout(function(){
                window.location.href = pageLink+'?status='+response.status;
            }, 200);
		}
	});
});
function openModal(data){   
    $('#order_id').text(data.order_id); 
    $('#billing_name').text(data.billing.name);
    $('#billing_address').text(data.billing.address_1);
    $('#billing_city').text(data.billing.city);
    $('#billing_phone').text(data.billing.phone);
    $('#shipping_name').text(data.shipping.name);
    $('#shipping_address').text(data.shipping.address_1);
    $('#shipping_city').text(data.shipping.city);
    $('#shipping_phone').text(data.shipping.phone);
    
        var unit= data.currency_code;
    if(unit=='USD'){
        $('#total').text("$"+data.item_gross_amount);
        // $('#tax').text("$"+data.tax_amount);
    }
        else if(unit=='PKR'){
        $('#total').text("Rs"+data.item_gross_amount);
        // $('#tax').text("Rs"+data.tax_amount);
    }

    // console.log(unit);
    // $('#gross_amount').text(data.gross_amount);
        //$('#total').text(data.gross_amount);
    //$('#tax').text(data.tax_amount);

    var html = "<table class='new-table table-responsive sorted_table table table-striped table-bordered datatables tableWidth'><tr><th><?php echo $this->lang->line('image');?></th><th><?php echo $this->lang->line('product_name');?></th><th><?php echo $this->lang->line('product_cond');?></th><th><?php echo $this->lang->line('qty');?></th><th><?php echo $this->lang->line('price');?></th><th><?php echo $this->lang->line('tax');?></th><th><?php echo $this->lang->line('shipping_amount');?></th><th><?php echo $this->lang->line('subtotal');?></th></tr>";
    $(data.orders).each(function(index,element){
        if(element.condition_id == 2){
            var condition = "<?php echo $this->lang->line('manufacturer_refurbished');?>";
        }
        else if(element.condition_id == 3){
            var condition = "<?php echo $this->lang->line('used_like_new');?>";
        }
        else if(element.condition_id == 4){
            var condition = "<?php echo $this->lang->line('used_very_good');?>";
        }
        else if(element.condition_id == 5){
            var condition = "<?php echo $this->lang->line('used_fair');?>";
        }
        else{
            var condition = "<?php echo $this->lang->line('new');?>";
        }
        $('#buyer_id').val(element.user_id);
        $('#sp_id').val(element.sp_id);
        $('#pv_id').val(element.product_vid);
        
        html+='<tr><td><img src="<?php echo base_url()."uploads/product/thumbs/"?>'+element.image_link+'" class="img-fluid">'+'</td><td>'+element.product_name+'</td><td class="text-center">'+condition+'</td><td>'+element.qty+'</td><td>'+element.price+'</td>'+
            '</td><td>'+element.tax_amount+'<td>'+element.item_shipping_amount+'</td><td>'+element.item_gross_amount+'</td></tr>';
        // html += '<b>Product Name:</b> <div id="product_name">'+element.product_name+'</div>'+
        // '<b>Product Description:</b> <div id="product_description">'+element.product_description+'</div>'+
        // '<b>Product Quantity:</b> <div id="product_quantity">'+element.qty+'</div>'+
        // '<b>Price:</b> <div id="price">'+element.gross_amount+'</div>';
        

        // //var product_name= element.product_name;
        // var product_description= element.product_description;
        //  var product_quantity= element.qty;

        //  $("#product_name").append(element.product_name);
        //    $("#product_description").append(element.product_description);
        //     $("#product_quantity").append(element.qty);
            // var orders = '<p name="'+val+'<br>';
        // /console.log(product_quantity);    

    });
    html+="</table>";
    $("#products").html(html);
    $('#myModal').modal('show'); 
    //var v = url_de
}

function jparser(data){
    var d =  JSON.stringify(data);
    return d;
}
function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var strTime = months[date.getMonth()]+'-'+date.getDate()+'-'+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
    return strTime;
}
$('.contact-buyer').click(function(){
        // $("#pv_id").val($(this).attr('data-pv_id'));
        // $("#sp_id").val($(this).attr('data-sp_id'));
        // $("#seller_id").val($(this).attr('data-seller_id'));
        // var s = $(this).attr('data-store_name');
        // $('#storeName').html($(this).attr('data-store_name'));
        $("#message-panel").modal('show');
    });
$('#sendMessage').click(function(){
    var subject = "";
    var message = $("#message").val();
    var pv_id = $("#pv_id").val();
    var buyer_id = $("#buyer_id").val();
    var sp_id = $("#sp_id").val();
    var UTCDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
    if(message == ""){
        // alert();
        $("#message").next().html('<strong class="error"><?php echo $this->lang->line('enter_message');?></strong>');
        return false;
    } 
    if(message !=""){
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:true,
            url: "<?php echo base_url()?>product/saveMessage",
            data:{'receiver_id':buyer_id,"item_id":sp_id,"item_type":"product",'message':message,'buyer_id':buyer_id,'seller_id':'<?php echo (isset($_SESSION['userid'])?$_SESSION['userid']:0); ?>','product_variant_id':pv_id,'subject':subject,'time':UTCDateTime},
            success: function(response){
                if(response.status == 1){
                    $('#message-panel').modal('toggle');
                    $('#message-notification').modal('show');
                    setTimeout(function() {
                        $('#message-notification').modal('hide'); 
                        }, 3000); 
                    $('#change-message').text("<?php echo $this->lang->line('sent_message');?>");
                    $("#message").val("");
                }
            }
        });
    }
}); 

// $('#orderAcceptId').on("click",function(){
// var rowId = $(this).attr('data-row_id'); 
// console.log(rowId);
// $("#trackingIdModal").modal('show');
// var accept = "<?php echo base_url('seller/sales/accept_order/1/')?>"+row.order_id;
// $('#trackingAccept').attr("href",accept);
// });  

</script>

  <!-- Modal -->
  <div class="modal fade" id="trackingIdModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4><?php echo $this->lang->line('tracking_id_number');?></h4>
        </div>
        <div class="modal-body">
          <input type="text" id="trackingNumber" required>
        </div>
        <div class="modal-footer">
          <a href="" onclick='addTrackingNumber()' class="btn btn-success" id="trackingAccept"><?php echo $this->lang->line('order_accept');?></a>
        </div>
      </div>
      
    </div>
  </div>
  <div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('product_approval');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('modal_product_approval');?>
			</div>				
		  </div>
			<div class="modal-footer">
				<button class="btn btn-primary" id="acceptBtn" ><?php echo $this->lang->line('yes');?></button>
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="decline_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('product_rejection');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('ask_product_rejection');?>
				<textarea class="form-control mt-2" id="declined_reason" placeholder="Enter Reject Reason"></textarea>
			</div>
		</div>
			<div class="modal-footer">
				<button class="btn btn-primary" id="rejectBtn" ><?php echo $this->lang->line('yes');?></button>
				<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			</div>
		</div>
	</div>
</div>
<!-- 
<style>
table {
   /* font-family: arial, sans-serif;*/
    color:black;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border-right: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
img{
    width: 50%;
}
</style> -->