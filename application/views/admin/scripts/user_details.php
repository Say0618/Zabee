
<script>
$(function() {
      $( 'ul.nav li' ).on( 'click', function() {
            $( this ).parent().find( 'li.active' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
      });
});

var total;
var t;
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
  	 t = $('.datatables').dataTable({
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
            url: '<?php echo base_url('seller/userdetails/get_product_details/').$id_new ?>',
            pages: 5 // number of pages to cache
		} ),
		"columnDefs": [
            {
                "render": function ( data, type, row ) {
                 return  "";
            	},
                "targets": 0
            },
			{
                "render": function ( data, type, row ) {
					if(row.name1 == null){
						return "Admin";
					}else{
						return row.name1;
					}
				},
                "targets": 1
            },
			{
                "render": function ( data, type, row ) {
					var enable = '';
					var disable = '<a href="#" class="productTooltip pull-right" data-toggle="tooltip" title="Disabled"><i class="fa fa-info-circle"></i></a>';
					var disableTooltip = (row.active == 1)?enable:disable;
					return row.product_name + disableTooltip;
				},
                "targets": 2
            },
			{
                "render": function ( data, type, row ) {
					var img ="";
					var path ="";
					if(row.is_image == 1){
						path = row.thumbnail.split(",");
						//alert(path);
						//console.log(path[0]);
                    
						if(row.is_local == "1"){
							img = '<img src="<?php echo product_path('thumbs/')?>'+path[0]+'" class="img-fluid">';
						}else{
							is_preview = path[0].startsWith("http");
							is_another_preview = path[0].startsWith(" http");
							if(is_preview || is_another_preview){
								img = '<img src="'+path[0]+'" class="img-fluid">';
							}else{
								img = '<img src="<?php echo product_path('thumbs/')?>'+path[0]+'" class="img-fluid">';
							}
						}
					}
					<?php //echo "<pre>"; print_r($img); echo "</pre>"; die(); ?>
					return img;
                },
                "targets": 3
            },
			{
                "render": function ( data, type, row ) {
					//console.log(row);
                    return row.category_name;
                },
                "targets": 4
            },
			{
                "render": function ( data, type, row ) {
                    return row.brand_name;
                },
                "targets": 5
            },
			{
                "render": function ( data, type, row ) {
                    return row.condition_name;
                },
                "targets": 6
            },
			{
                "render": function ( data, type, row ) {
                    return row.price;
                },
                "targets": 7
            },
			{
                "render": function ( data, type, row ) {
					var approved = "";
					if(row.approve == "1"){
						approved = '<div class="" style="color:green">Approved</div>';
					}
					if(row.approve == "0"){
						approved = '<div class="" style="color:red;">Waiting for approval</div>';
					}
					return approved;
                },
                "targets": 8
            },
			{
                "render": function ( data, type, row ) {
					//console.log(window.btoa(row.product_name,row.product_id));
					
					return '<a href="<?php echo base_url(); ?>product/detail/'+btoa(encodeURIComponent(row.product_name+"_"+row.product_id))+'" class="btn btn-primary" >Show</a>'
                },
                "targets": 9
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
			$(".description > a.elipsis").click(function (e) {
				e.preventDefault(); //prevent '#' from being added to the url
				$(this).prev('span.elipsis').fadeToggle(500);
			});
			$('[data-toggle="tooltip"]').tooltip();   
   		}
	});
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search...');
    $('.dataTables_length select').addClass('form-control');
	
	/*$(document).on("click",'.delete', function(){
		alert("here");
	  console.log($(this).parent());
	  t.row($(this).parents('t r')).remove().draw(false);
	  console.log(t);
	});*/
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
			}, 200);*/
			//setTimeout(function(){$("#preloadView2").fadeOut(500);},100);	
			//$('#loader').hide();
		}
	});
}
function deletestatus(identifier){  
	//$('#product_del').modal('show');
	//$('.confirm_del').click(function(){
	var id = $(identifier).data('prodid');
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/product/askdelete');?>",
		data: {'id':id},
		success: function(response){
			$("#btn"+id).parent().parent().remove()
			/*if(response.success == true){
				setTimeout(function(){
				   location.reload();
			  }, 200); 
			}*/
		}
	});
//});
}
function decodeHtml(html) {
    var txt = document.createElement("textarea");
    txt.innerHTML = html;
    return txt.value;
}
function askchangeimage(identifier){  
	var id = $(identifier).data('prodid');
	//alert(id);
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/product/askchangeimage');?>",
		data: {'id':id},
		success: function(response){
			if(response.success == true){
				setTimeout(function(){
				   location.reload();
			  }, 200); 
			}
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
</script>
<div class="modal fade" id="product_del" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Product?</h5>
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
			<a href="#" class="btn" data-dismiss="modal">No</a>
		  </div>
		</div>
	</div>
</div>
</script>