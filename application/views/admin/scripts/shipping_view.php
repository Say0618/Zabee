<script>
var total=0;
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
 
            settings.jqXHR = $.ajax({
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
	var user_id = "<?php echo $this->session->userdata('userid')?>";
	 t = $('#datatables').DataTable({
	
		dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn',
					text: 'Add Shipping',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/shipping/createshipping') ?>';
						}        
				}
			],
		"language": {
            "lengthMenu": "Display _MENU_ records per page",
            "zeroRecords": "Nothing found - sorry",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",	
			"paginate": {
        		"previous": "<",
				"next" : ">"
    				},
        },
		"processing": true,
        "serverSide": true,
		"ajax": $.fn.dataTable.pipeline( {
            url: '<?php echo base_url('seller/shipping/getShipping')?>',
            pages: 5 // number of pages to cache
		}),
		"columnDefs": [
            {
                "render": function ( data, type, row ) {
			     return  "";
            	},
                "targets": 0
            },
			{
                "render": function ( data, type, row ) {
					return row.title
				},
                "targets": 1
            },
			{
                "render": function ( data, type, row ) {
					return (row.price == 0)?"Free Shipping":row.price;
				},
                "targets": 2
            },
			{
                "render": function ( data, type, row ) {
					return row.incremental_price;
                },
                "targets": 3
            },
			{
                "render": function ( data, type, row ) {
					return row.free_after;
                },
                "targets": 4
            },
			{
                "render": function ( data, type, row ) {
					return row.duration;
                },
                "targets": 5
            },
			{
                "render": function ( data, type, row ) {
					if(row.shipping_id == "0"){
						var span ="";
						var span = '<span style="color:green"><h4>Yes</h4></span>'
						return span;
					} else {
						var checked ="";
						if(row.is_active == 1){
							checked ="checked";
						}
						if(user_id == row.user_id){
							var checkbox = '<input type="checkbox" class="toggle-two toggle-css" data-prodid="'+row.shipping_id+'" data-isactive="'+row.display_status+'" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" id="featured'+row.shipping_id+'"  onchange="updateStatus(this,'+row.shipping_id+')" '+checked+' />'
						}else{
							var checkbox = 'YES'
						}
						return checkbox;
					}
                },
                "targets": 6
            },
			{
                "render": function ( data, type, row ) {
					/*if(row.user_id == 1){
					var dropdown_button = '<button class="btn btn-secondary smallButton btn-disabled " style="opacity:0.2"><i class="fas fa-cog"></i></button>';
					return dropdown_button;
					}
					// console.log(row);
					else{*/
					if(row.shipping_id == "0"){
						return null;
					} else {
						if(user_id == row.user_id){
						var dropdown_button = '<div class="dropdown" id="btn'+row.shipping_id+'"><button class="btn btn-secondary smallButton" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-wrench"></i></button><div class="dropdown-menu prod-vew-drop" aria-labelledby="dropdownMenuButton"><a class="dropdown-item" href="<?php echo base_url('seller/shipping/shipping_edit/'); ?>'+row.shipping_id+'"><i class="fa fa-edit"></i>     Edit</a><div class="dropdown-divider" style="border-bottom:1px solid #bab8b8"></div><button class="btn btn-outline delete" style="color: #212529!important;font-weight: 400!important;" data-userId='+row.user_id+' data-shipId="'+row.shipping_id+'" id="delete" data-target="#shipping_del" onclick="askDelete(this)"><i class="fa fa-trash"></i>     Delete</button></div></div>';
						}else{
							var dropdown_button = '<h5>No Action</h5>';
						}
						return dropdown_button;
					}
				},
             
					"targets": 7
			},
		],
		"fnDrawCallback": function ( oSettings ) {
			var d = 0;
			for ( var i=start, iLen=oSettings.aiDisplay.length; i<end ; i++ )
			{
				$('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[d] ].nTr ).html( i+1 );
				d++;
			}
			$('.toggle-two').bootstrapToggle({
				on: 'Yes',
				off: 'No'
			});
			$('[data-toggle="tooltip"]').tooltip();   
		},
		
		 "fnFooterCallback": function( nFoot, aData, iStart, iEnd, aiDisplay ) {
			start = iStart;
			end = iEnd;
		}
	});
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search...');
    $('.dataTables_length select').addClass('form-control');
});
function updateStatus(e,id){
	var value = "0";
	if(e.checked) {
        value = "1";
    }
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/shipping/update_shipping_stauts');?>",
		data:{'shipping_id':id,'value':value},
		success: function(response){
			if(response.status == "2"){
				var ship = response.data['ship'];
				var stringHtml = "";
				stringHtml = "<input type='hidden' id='current_ship' name='current_ship' value="+id+" />"+
							"<label>New Shipping:</label>"+
							"<select class='form-control' name='ship_id' id='ship_id'>";
								ship.forEach(function(item){
									stringHtml += "<option value="+item.shipping_id+">"+item.title+"-$"+item.price+"</option>";
								});
							stringHtml += "</select>";
				$('#modalShipping #close-model').attr("data-id",id);
				$('#modalShipping .modal-body fieldset').html(stringHtml);
				$('#modalShipping').modal('show');
			}
		}
	});
}
function askDelete(id){
	$('#shipping_del').modal('show');
	$('.confirm_del').click(function(){
	// console.log(id);
		var shipping_id= $(id).attr('data-shipId');
		var user_id= $(id).attr('data-userId');
		deletestatus(shipping_id,user_id);
	});
}
function deletestatus(shipping_id,user_id){  
	// var id = $(identifier).data('prodid');
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/shipping/delete_shipping_method');?>",
		data: {'id':shipping_id,'userid':user_id},
		success: function(response){
			window.location.href = "<?php echo base_url("seller/shipping")?>"+'?status='+response.status;
			// $("#btn"+id).parent().parent().remove()
		}
	});
}
function toggleactive(identifier){  
	var id = $(identifier).data("prodid");
	var is_active = $(identifier).data("isactive");
	var value = $(identifier).data("isactive");
	//alert(value);
	if (value == 1) {
		value = "0";	
	}
	else {
		value = "1";
	}
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/product/togglechange');?>",
		data: {'id':id, 'value':value},
		success: function(response){
			if(response.success == true){
				setTimeout(function(){
				   location.reload();
			  }, 200); 
			}
		}
	});	
}
$('.table-responsive').on('show.bs.dropdown', function (e) {
	$(e.relatedTarget).next('div[aria-labelledby="dropdownMenuButton"]').appendTo("body");
});
// $('.adminAction').addClass('btn-disabled');
$(document).on('hide.bs.modal',"#modalShipping",function(){
	id = $("#modalShipping #close-model").data("id");
	$("#featured"+id).parent().removeClass("btn-default off").addClass("btn-success");
	$("#featured"+id).prop("checked", true);
});
</script>
<div class="modal fade" id="shipping_del" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete shipping?</h5>
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
			<input type = "submit" value = "Yes" class="btn btn-primary confirm_del" />

			<!-- <a href="" onclick="deletestatus()" data-dismiss="modal" class="btn">Yes</a> -->
			<a href="#" class="btn" data-dismiss="modal">No</a>
		  </div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalShipping" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form method="post" action="<?php echo base_url("seller/shipping/transferShipping") ?>">
			<div class="modal-content">
				<div class="modal-header text-center">
					<p class="modal-title w-100 text-danger">Shipping can not be deactivated, it is being used by product(s). Shift the product(s) to some other Shipping if you want to deactivate this shipping.</p>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body mx-3" id="shipModal">
					<div class="md-form mb-4" id="shipChangeData">
						<fieldset></fieldset>
					</div>
				</div>
				<div class="modal-footer">
					<input type = "submit" value = "Yes" class="btn btn-primary" />
					<a href="#" id="close-model" data-id="" class="btn" data-dismiss="modal">No</a>
				</div>
			</div>
		</form>
	</div>
</div>

