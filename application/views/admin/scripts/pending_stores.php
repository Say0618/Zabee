<script>
var total;
var t;
function format(variantResult){
var stringHtml = "";
var inc = 0;
var inc2= 0;
$.each(variantResult,function(index,value){
	stringHtml += '<div class="container mt-3"><h5>'+index+'</h5><table  cellpadding="5" class="table table-striped" cellspacing="0" border="0"><thead><tr>';
	$.each(value.variant_group,function(ind,val){
		stringHtml += '<th class="stripeHeader">'+val+'</th>';
		inc++;
	});
	stringHtml+='</tr></thead>';
	//for(var i=0; i<varients.length; i++){
	stringHtml += '<tbody>';
	$.each(value.v_title,function(ind,val){  
		if(inc2 == 0){
			stringHtml += "<tr>";
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
	var page = "<?php echo (isset($_GET['store']) && $_GET['store'] != "") ? $_GET['store'] : ''; ?>";
	var pending = "<?php echo base_url('seller/stores/get_stores/0')?>";
	var approved = "<?php echo base_url('seller/stores/get_stores/1')?>";
	var declined = "<?php echo base_url('seller/stores/get_stores/2')?>";
	if(page!=""){
		$(".page-header h2").text(page+" stores");
		$('.page-header h2').css('textTransform', 'capitalize');
	}
	//var action;
  // var a=1;
  // var start;
   //var invite_button;
 //  var classname;
  // var end;
  var count = 0;
  	var t = $('.datatables').DataTable({
		"language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)"
        },
		"processing": true,
        "serverSide": true,
		"ajax": $.fn.dataTable.pipeline({
            url: (page == "") ? pending : ((page == "approved") ? approved : ((page == "declined") ? declined : '')),
            pages: 5 // number of pages to cache
		}),
		"columnDefs": [
            {
                "render": function ( data, type, row ) {
                 return  count += 1;
            	},
                "targets": 0
            },
			{
                "render": function ( data, type, row ) {
                    return row.name
				},
                "targets": 1
            },
			{
                "render": function ( data, type, row ) {
					return row.email;
                },
                "targets": 2
            },
			{
                "render": function ( data, type, row ) {
					return row.store;
                },
                "targets": 3
            },
			{
                "render": function ( data, type, row ) {
					if(page == ""){
						var approve_button = '<a class="btn btn-success"  data-storeid="'+row.id+'" data-sellerid="'+row.seller_id+'" onclick="askApprove(this)" style="color:white; width:95px; margin-left:50px;">Approve</a> &nbsp&nbsp&nbsp';
						var decline_button = '<a class="btn btn-danger"  data-storeid="'+row.id+'" data-sellerid="'+row.seller_id+'" onclick="askDecline(this)" style="color:white; width:95px;">Decline</a>';
						return approve_button+decline_button;
					}else{
						if(row.zabee == "0"){
							return '<h5>NO</h5>';
						}else{
							return '<h5>YES</h5>';
						}
					}
                },
                "targets": 4
            },
			{
                "render": function ( data, type, row ) {
					if(page == "declined"){
						var approve_button = '<a class="btn btn-success"  data-storeid="'+row.id+'" data-sellerid="'+row.seller_id+'" data-country="'+row.country+'" data-contact="'+row.contact+'" data-tax="'+row.tax+'" onclick="forceApprove(this)" style="color:white; width:95px; margin-left:50px;">Approve</a> &nbsp&nbsp&nbsp';	
					}else if(page == "approved"){
						var checked ="";
						if(row.is_active == 1){
							checked ="checked";
						}
						var approve_button = '<center><input type="checkbox" class="toggle-two toggle-css" data-prodid="'+row.id+'" data-isactive="'+row.is_active+'" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" id="block'+row.id+'"  onchange="updateStatus(this,'+row.id+')" '+checked+' /></center>'
					}
					return approve_button;
                },
                "targets": (page != "") ? 5 : null
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
			$(".description").each(function () {
				text = $(this).text();
				if (text.length > 200) {
					$(this).html(text.substr(0, 25) + '<span class="elipsis">' + text.substr(25) + '</span><a class="elipsis" href="#">...</a>');
				}
			});
			$('.toggle-two').bootstrapToggle({
				on: 'Yes',
				off: 'No'
			});
			$('[data-toggle="tooltip"]').tooltip();
		},
		 "fnFooterCallback": function( nFoot, aData, iStart, iEnd, aiDisplay ) {
			start = iStart;
			end = iEnd;
		},"initComplete": function(settings, json) {
			$(".description").each(function () {
				text = $(this).text();
				if (text.length > 200) {
					$(this).html(text.substr(0, 25) + '<span class="elipsis">' + text.substr(25) + '</span><a class="elipsis" href="#">...</a>');
				}
			});
   		}
	});
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search...');
    $('.dataTables_length select').addClass('form-control');

	t.on("user-select", function (e, dt, type, cell, originalEvent) {
		if ($(cell.node()).hasClass("details-control")) {
			e.preventDefault();
		}
	});

	$("#force_approve_model #name").val()
});
	
function askApprove(identifier){
	$('#approve_modal').modal('show');
	$("#store_id").val($(identifier).data('storeid'));
	$("#seller_id").val($(identifier).data('sellerid'));
}
$('#confirm_approve').on('click',function(){
	approve($("#store_id").val(), $("#seller_id").val());
});

function forceApprove(identifier){
	seller_name = $(identifier).closest("tr").find("td:nth-child(2)").html();
	store_name = $(identifier).closest("tr").find("td:nth-child(4)").html();
	email = $(identifier).closest("tr").find("td:nth-child(3)").html();
	service = $(identifier).closest("tr").find("td:nth-child(5)").html();
	contact = identifier.dataset.contact;
	country = identifier.dataset.country;
	tax = (identifier.dataset.tax == "1") ? "<h5>YES</h5>" : "<h5>NO</h5>";
	$('#force_approve_modal').modal('show');
	$('#force_approve_modal #name').html(seller_name);
	$('#force_approve_modal #store').html(store_name);
	$('#force_approve_modal #email').html(email);
	$('#force_approve_modal #contact').html(contact);
	$('#force_approve_modal #country').html(country);
	$('#force_approve_modal #service').html(service);
	$('#force_approve_modal #tax').html(tax);
	$("#store_id").val($(identifier).data('storeid'));
	$("#seller_id").val($(identifier).data('sellerid'));
}
$('#force_approve_modal #confirm_approve').on('click',function(){
	approve($("#store_id").val(), $("#seller_id").val());
});

function approve(storeid, sellerid){
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/stores/updateStore');?>",
		data: {'s_id':storeid,'seller_id' :sellerid, 'status': 'approve'},
		success: function(response){
			if(response.status == "1"){
				setTimeout(function(){
					location.reload();
				}, 200); 
			}
		}
	});
}

function askDecline(identifier){
	$('#decline_modal').modal('show');
	$("#store_id").val($(identifier).data('storeid'));
	$("#seller_id").val($(identifier).data('sellerid'));
}
$('#rejectBtn').on('click',function(){
	var storeid = $("#store_id").val();
	var sellerid = $("#seller_id").val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/stores/updateStore');?>",
		data: {'s_id':storeid,'seller_id' :sellerid, 'status': 'decline'},
		success: function(response){
			if(response.status == "1"){
				setTimeout(function(){
					location.reload();
				}, 200); 
			}
		}
	});
});

function updateStatus(btn, id){
	var value = 0;
	if(btn.checked) {
        value = 1;
    }
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/stores/update_store_status');?>",
		data:{'s_id':id, 'value':value},
		success: function(response){
			/*setTimeout(function(){
			   location.reload();
			}, 200);*/
			//setTimeout(function(){$("#preloadView2").fadeOut(500);},100);	
			//$('#loader').hide();
		}
	});
}
</script>