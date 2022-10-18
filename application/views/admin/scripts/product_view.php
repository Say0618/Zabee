<script>
var total;
var t;
// console.log(variantResult);
function format(variantResult){
	// console.log(variantResult);
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
 
  	 t = $('#datatables').DataTable({
	
		dom: 'Blfrtip',
			buttons: [
				{
					className: 'btn btn-primary datatableBtn',
					text: 'Add Product',
					action: function ( e, dt, button, config ) {
						window.location = '<?php echo site_url('seller/product/create') ?>';
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
            url: '<?php echo base_url('seller/product/get_product_details')?>',
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
					var img ="";
					var path ="";
					if(row.is_local == '1'){
						img = '<img src="<?php echo product_thumb_path()?>'+row.is_primary_image+'" class="img-fluid img-small-size">';
					}else if(row.is_local == "0"){
						img = '<img src="'+row.is_primary_image+'" class="img-fluid img-small-size">';	

					}else{
						img = '<img src="<?php echo product_thumb_path()?>Preview.png" class="img-fluid img-small-size">';	
					}
					return img;
				},
				"class":"text-center",
                "targets": 1
            },
			<?php if($this->session->userdata('user_type') == 1){?>
			{
                "render": function ( data, type, row ) {
					if(row.name1 == null){
						return "Admin";
					}else{
						return row.name1;
					}
				},
                "targets": 2
				
            },
		<?php } ?>
			{
                "render": function ( data, type, row ) {
					var enable = '';
					var disable = '<a href="#" class="productTooltip pull-right" data-toggle="tooltip" title="Disabled"><i class="fa fa-info-circle"></i></a>';
					var disableTooltip = (row.prd_active == 1)?enable:disable;
					return row.product_name + disableTooltip;
				},
                //"targets": 3
				"targets": <?php echo ($this->session->userdata('user_type') == 1)?3:2?>
            },
			{
                "render": function ( data, type, row ) {
					//console.log(row);
                    return row.category_name;
                },
               // "targets": 4
			   "targets": <?php echo ($this->session->userdata('user_type') == 1)?4:3?>

            },
			{
                "render": function ( data, type, row ) {
                    return row.brand_name;
                },
                // "targets": 5
				"targets": <?php echo ($this->session->userdata('user_type') == 1)?5:4?>

            },
			{
                "render": function ( data, type, row ) {
					var button = '<a href="<?php echo base_url('seller/product/create?pi='); ?>'+row.product_id+'"><i class="fa fa-edit"></i>     Edit</a>';
                    return button;
                },
                // "targets": 7
				"targets": <?php echo ($this->session->userdata('user_type') == 1)?6:5?>
			},
			{
				
                "render": function ( data, type, row ) {
					var button = '<a href="<?php echo base_url(); ?>product/'+row.slug+'"><i class="fa fa-edit"></i> View</a>';
                    return button;
                },
                
                // "targets": 6
				"targets": <?php echo ($this->session->userdata('user_type') == 1)?7:6?>

            },
			<?php if($this->session->userdata('user_type') == 1){?>
			{
                "render": function ( data, type, row ) {
					var checked ="";
					if(row.is_featured == 1){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" data-prodid="'+row.sp_id+'" data-isactive="'+row.active+'" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" id="featured'+row.product_id+'"  onchange="updateStatus(this,\'featured\','+row.product_id+')" '+checked+' />'
                    return checkbox;
                },
                "targets": 8
            },{
                "render": function ( data, type, row ) {
					console.log(row);
					var checked ="";
					if(row.prd_active == 0){
						checked ="checked";
					}
					var checkbox = '<input type="checkbox" class="toggle-two toggle-css" data-prodid="'+row.product_id+'" data-isactive="'+row.prd_active+'" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" id="block'+row.product_id+'"  onchange="updateStatus(this,\'block\','+row.product_id+')" '+checked+' />'
                    return checkbox;
                },
                "targets": 9
            },
			<?php } ?>
			{
                "render": function ( data, type, row ) {
				// href = "<?php if($this->session->userdata("user_type") != "1"){ echo base_url()?>seller/product/inventory_view?prd_id="+row.product_id+"&seller=<?php echo $this->session->userdata("userid"); }else{} ?>";
					var varrient_button = '<button class="btn btn-secondary smallButton" type="button"><i class="fas fa-plus-square plusBtn" aria-hidden="true" data-isclick="0"></i></button>';
			     return  varrient_button;
            	},
				
				"className":"details-control text-center",
                "targets": <?php echo ($this->session->userdata('user_type') == 1)?10:7 ?>
				<?php /*?>"targets": <?php //echo ($this->session->userdata('user_type') == 1)?9:7?><?php */?>
            },
			
		],
		"fnDrawCallback": function ( oSettings ) {
			var d = 0;
			for ( var i=start, iLen=oSettings.aiDisplay.length; i<end ; i++ )
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
	// Add event listener for opening and closing details
	$('.datatables tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var tdi = tr.find("i.plusBtn");
		var row = t.row(tr);
		var sp_id = row.data().sp_id;
		var product_id = row.data().product_id;
	<?php if($this->session->userdata("user_type") == "1"){ ?>
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
				var stringHtml = "";
				$.ajax({
					type: "POST",
					cache:false,
					dataType: "JSON",
					async:true,
					url: "<?php echo base_url('seller/product/get_storeDetails/');?>"+product_id,
					//data:{'sp_id':sp_id, 'prd_id':product_id},
					success: function(data){ 
						stringHtml += '<div class="container mt-3"><h5></h5><table  cellpadding="5" class="table table-striped" cellspacing="0" border="0"><thead><tr><th>Store Name</th><th>Inventory</th></tr></thead>';
						stringHtml += '<tbody>';
						if(data.length > 0){
							$.each(data,function(ind,val){
								stringHtml += "<tr><td>"+val.store_name+"</td><td>"+val.view+"</td></td>";
							});
						}else{
							stringHtml +='<td colspan="2" class="error text-center"><strong>No Inventory Found</strong></td>';
						}
						stringHtml += '<tbody></table></div>';
						row.child(stringHtml).show();
						row.child().addClass('changeStripe');
						tr.addClass('shown');
						tdi.first().removeClass('fa-plus-square');
						tdi.first().addClass('fa-minus-square');
						tdi.attr('data-isclick',1);		
						// if(data.row > 0){
							// row.child(format(data.result)).show();
							// row.child().addClass('changeStripe');
							// tr.addClass('shown');
							// tdi.first().removeClass('fa-plus-square');
							// tdi.first().addClass('fa-minus-square');
							//tdi.attr('data-isclick',1);		
						// }
					
					}
				});
		}
	<?php }else { ?>
	window.location = "<?php echo base_url('seller/product/inventory_view?prd_id=');?>"+product_id+"&seller=<?php echo $this->session->userdata("userid") ?>";
	<?php }?>

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
	} else if(what == "block"){
		value = (value == "0")?"1":"0";
		column = "is_active";
	}
	console.log(value);
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/product/update_product_stauts');?>",
		data:{'product_id':id,'column':column,'value':value},
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
