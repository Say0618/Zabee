<script>
	//setTimeout(function(){$(".viewbtn").attr("target","_blank");},1000);
	
//	setTimeout(function(){window.location.href = window.location.href;},(1000*60*2));


	var total;
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
  	var t = $('.datatables').dataTable({
		"language": {
            searchPlaceholder: "<?php echo $this->lang->line('search_inventory');?>",
            "Search": "<?php echo $this->lang->line('search');?>:",
            "lengthMenu": "<?php echo $this->lang->line('display');?> _MENU_ <?php echo $this->lang->line('records_perpage');?>",
            "zeroRecords": "<?php echo $this->lang->line('not_found');?>",
            "info": "<?php echo $this->lang->line('showing');?> <?php echo $this->lang->line('of');?> _PAGE_ of _PAGES_",
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
            url: '<?php echo base_url('seller/sales/acceptedOrders')?>',
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
                    return row.shipping.address_1+"<br>"+row.shipping.city+','+row.shipping.state+' '+row.shipping.zipcode+'<br/>'+row.shipping.country+'<br/>'+row.shipping.name+"<br>"+row.shipping.phone;
				},
                "targets": 3
            },
			{
                "render": function ( data, type, row ) {
                    return parseFloat(row.orders[0].seller_total).toFixed(2);
                },
                "targets": 4
            },
			{
                "render": function ( data, type, row ) {
                    if(row.status==1)
					return "<b><div style='color:#4CAF50'>Success</div></b>";
                    else if(row.status==0)
                    return "<b><div style='color:#8B0000'>Pending</div></b>";
                },
                //className:"description","targets": [4]
                "targets": 5
            },
            {
                "render": function ( data, type, row ) {
                    // var checked =jparser(row);
                    var button = "<button  type='button' class='btn btn-primary' data-toggle='modal' onClick='openModal("+row.order_id+")' data-target=''><?php echo $this->lang->line('view');?></button>";
                    return button;
                }, 
               
                "targets": 6
            },
            //   {
            //     "render": function ( data, type, row )
            //      {
            //         var accept = "<?php echo base_url('seller/sales/accept_order/1/')?>"+row.order_id;
            //         var decline = "<?php echo base_url('seller/sales/accept_order/2/')?>"+row.order_id;
            //         if(row.orders[0].action==1 || row.status==0){
            //             if(row.status==0)
            //             {
            //                 var dropdown = '<div class="dropdown"><button type="button" disabled class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action</button><div class="dropdown-menu"><a class="dropdown-item" href="'+accept+'">Accept</a><a class="dropdown-item" href="'+decline+'">Decline</a></div></div>';

            //             }
            //             else if(row.orders[0].action==1)
            //             {
            //             var action="Accepted";
            //             var dropdown = '<div class="dropdown"><button type="button" disabled class="btn btn-primary dropdown-toggle" data-toggle="dropdown">'+action+'</button><div class="dropdown-menu except-prod"><a class="dropdown-item" href="'+accept+'">Accept</a><a class="dropdown-item" href="'+decline+'">Decline</a></div></div>';
            //             }
            //         }

            //         else if(row.orders[0].action==2 || row.status==1){
            //             if(row.orders[0].action==2)
            //             {
            //             var action="Declined";
            //             var dropdown = '<div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">'+action+'</button><div class="dropdown-menu"><a class="dropdown-item" href="'+accept+'">Accept</a></div></div>';

            //             }
            //             else if(row.status==1){
            //             var action='Action';
            //             var dropdown = '<div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">'+action+'</button><div class="dropdown-menu"><a class="dropdown-item" href="'+accept+'">Accept</a><a class="dropdown-item" href="'+decline+'">Decline</a></div></div>';

            //             }
                                
            //             }                
                    
            //         return dropdown;
            //     },
               
            //     "targets": 7
            // },
            {
                "render": function ( data, type, row ) {
                   // var checked =jparser(row);
                    var button = "<a href='<?php echo base_url("seller/sales/getInvoice/")?>"+row.order_id+"' class='btn btn-info' role='button'><?php echo $this->lang->line('get_invioce');?></a>";
                    return button;
                },
               
                "targets": 7
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
	$('.dataTables_filter input').addClass('form-control').attr('placeholder','Search...');
    $('.dataTables_length select').addClass('form-control');
});
    function openModal(id){
        $("#myModal .modal-body").load("<?php echo base_url() ?>seller/sales/saleView/"+id);
        $('#myModal').modal('show');
    }
	// function openModal(data){   
    //     $('#order_id').text(data.order_id); 
    //     $('#billing_name').text(data.billing.name);
    //     $('#billing_address').text(data.billing.address_1);
    //     $('#billing_city').text(data.billing.city);
    //     $('#billing_phone').text(data.billing.phone);
    //     $('#shipping_name').text(data.shipping.name);
    //     $('#shipping_address').text(data.shipping.address_1);
    //     $('#shipping_city').text(data.shipping.city);
    //     $('#shipping_phone').text(data.shipping.phone);
       
    //      var unit= data.currency_code;
    //     if(unit=='USD'){
    //         $('#total').text("$"+data.item_gross_amount);
    //         // $('#tax').text("$"+data.tax_amount);
    //     }
    //      else if(unit=='PKR'){
    //         $('#total').text("Rs"+data.item_gross_amount);
    //         // $('#tax').text("Rs"+data.tax_amount);
    //     }

    //     // console.log(unit);
    //     // $('#gross_amount').text(data.gross_amount);
    //      //$('#total').text(data.gross_amount);
    //     //$('#tax').text(data.tax_amount);

    //     var html = "<table class='table table-responsive w-100 d-block d-md-table table-bordered'><tr><th>Image</th><th>Title</th><th>Condition</th><th>Qty</th><th>Price</th><th>Tax</th><th>Shipping</th><th>Total</th></tr>";
    //     $(data.orders).each(function(index,element){
    //         $('#buyer_id').val(element.user_id);
    //         $('#sp_id').val(element.sp_id);
    //         $('#pv_id').val(element.product_vid);
            
    //         html+='<tr><td><img src="<?php echo base_url()."uploads/product/thumbs/"?>'+element.image_link+'" class="img" height="25">'+'</td><td>'+element.product_name+'</td><td class="text-center">'+element.condition_name+'</td><td>'+element.qty+'</td><td>'+element.price+'</td>'+
    //          '</td><td>'+element.tax_amount+'<td>'+element.item_shipping_amount+'</td><td>'+element.item_gross_amount+'</td></tr>';
    //         // html += '<b>Product Name:</b> <div id="product_name">'+element.product_name+'</div>'+
    //         // '<b>Product Description:</b> <div id="product_description">'+element.product_description+'</div>'+
    //         // '<b>Product Quantity:</b> <div id="product_quantity">'+element.qty+'</div>'+
    //         // '<b>Price:</b> <div id="price">'+element.gross_amount+'</div>';
            

    //         // //var product_name= element.product_name;
    //         // var product_description= element.product_description;
    //         //  var product_quantity= element.qty;

    //         //  $("#product_name").append(element.product_name);
    //         //    $("#product_description").append(element.product_description);
    //         //     $("#product_quantity").append(element.qty);
    //             // var orders = '<p name="'+val+'<br>';
    //         // /console.log(product_quantity);    

    //     });
    //     html+="</table>";
    //     $("#products").html(html);
    //     $('#myModal').modal('show'); 
    //     //var v = url_de

    // }
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
		var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		var strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
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
            alert();
			$("#message").next().html('<strong class="error">Please Enter Message!</strong>');
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
						$('#change-message').text("Message sent successfully");
						$("#message").val("");
					}
				}
			});
		}
	});            
</script>
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