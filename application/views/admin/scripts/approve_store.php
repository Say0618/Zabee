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
		"ajax": $.fn.dataTable.pipeline( {
            url: '<?php echo base_url('seller/stores/get_pending_stores')?>',
            pages: 5 // number of pages to cache
		} ),
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
					return row.store;
                },
                "targets": 2
            },
			{
                "render": function ( data, type, row ) {
					var approve_button = '<a class="btn btn-success"  data-storeid="'+row.id+'" data-sellerid="'+row.seller_id+'" onclick="askApprove(this)" style="color:white; width:95px; margin-left:50px;">Approve</a> &nbsp&nbsp&nbsp';
					var decline_button = '<a class="btn btn-danger"  data-storeid="'+row.id+'" data-sellerid="'+row.seller_id+'" onclick="askDecline(this)" style="color:white; width:95px;">Decline</a>';
					return approve_button+decline_button;
                },
                "targets": 3
            },
			// {
            //     "render": function ( data, type, row ) {
			// 		if(row.active == "1"){
			// 			return '<div class="success">Approved</div>';
			// 		}else if(row.active == "0" && row.is_declined == "0"){
			// 			return '<div class="error">Waiting for approval</div>';
			// 		}else{
			// 			return '<div class="error">'+row.declined_reason+'</div>';
			// 		}
            //     },
            //     "targets": 5
            // }
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
			// $('.toggle-two').bootstrapToggle({
			// 	on: 'Yes',
			// 	off: 'No'
			// });
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
			$(".description > a.elipsis").click(function (e) {
				e.preventDefault(); //prevent '#' from being added to the url
				$(this).prev('span.elipsis').fadeToggle(500);
			});
   		}
	});
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search...');
    $('.dataTables_length select').addClass('form-control');


	$('.datatables tbody').on('click', 'td.details-control', function () {
	
	var tr = $(this).closest('tr');
	var tdi = tr.find("i.plusBtn");
	var row = t.row(tr);
	var sp_id = row.data().sp_id;
	//console.log(row.data());
	if (row.child.isShown()) {
		// This row is already open - close it
		row.child.hide();
		tr.removeClass('shown');
		tdi.first().removeClass('fa-minus-square');
		tdi.first().addClass('fa-plus-square');
	}
	else {
		// Open this row
		//var isClick = tdi.attr('data-isclick');
		//if(isClick == 0){
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url('seller/product/get_subDetails');?>",
				data:{'sp_id':sp_id},
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
		// }else{
		// 	row.child().show();
			
		// 	tr.addClass('shown');
		// 	tdi.first().removeClass('fa-plus-square');
			
		// 	tdi.first().addClass('fa-minus-square');
				
		// }
	}
});

t.on("user-select", function (e, dt, type, cell, originalEvent) {
	if ($(cell.node()).hasClass("details-control")) {
		e.preventDefault();
	}
});
});

function updateStatus(e,what,id){
	var column = "is_banner";
	var value = 0;
	//$("#preloadView2").show();
	if(e.checked) {
        value = 1;
    }
	if(what == "featured"){
		column = "is_featured";
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/product/update_product_stauts');?>",
		data:{'sp_id':id,'column':column,'value':value},
		success: function(response){
			/*setTimeout(function(){
			   location.reload();
			}, 200);
			//setTimeout(function(){$("#preloadView2").fadeOut(500);},100);	
			//$('#loader').hide();*/
		}
	});
}

function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
	
function askApprove(identifier){
	$('#approve_modal').modal('show');
	$("#store_id").val($(identifier).data('storeid'));
	$("#seller_id").val($(identifier).data('sellerid'));
}
$('#confirm_approve').on('click',function(){
	var storeid = $("#store_id").val();
	var sellerid = $("#seller_id").val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/stores/approveStore');?>",
		data: {'s_id':storeid,'seller_id' :sellerid},
		success: function(response){
			if(response.status == "1"){
				setTimeout(function(){
					location.reload();
				}, 200); 
			}
		}
	});
});
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
		url: "<?php echo base_url('seller/stores/declineStore');?>",
		data: {'s_id':storeid,'seller_id' :sellerid},
		success: function(response){
			if(response.status == "1"){
				setTimeout(function(){
					location.reload();
				}, 200); 
			}
		}
	});
});
</script>