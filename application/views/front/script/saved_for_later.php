<link rel="stylesheet" type="text/css" href="<?php echo assets_url('plugins/datatables/buttons.dataTables.min.css')?>" />
<link rel="stylesheet" type="text/css" href="<?php echo assets_url('plugins/datatables/dataTables.css')?>" />
<script src="<?php echo assets_url('plugins/datatable/datatable.js'); ?>"></script>
<script src="<?php echo assets_url('plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo assets_url('plugins/datatables/dataTables.buttons.min.js'); ?>"></script>
<script src="<?php echo assets_url('plugins/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script>
var total;
var user_id = "<?php echo $this->session->userdata('userid');?>";
$.fn.dataTable.pipeline = function ( opts ){
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
        request.user_id = user_id;
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
$.fn.dataTable.Api.register( 'clearPipeline()', function () {
    return this.iterator( 'table', function ( settings ) {
        settings.clearCache = true;
    } );
} );
$(document).ready(function(e) {

    $("#wishlist").on("click", ".delete-wish", function(){
        var cat = $(this);
        id = $(this).val();
        $("#deleteCat_Modal").modal("show");
        $("#deleteCat_Modal #delete").on("click", function(){
            $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>delete_wishlist_category",
                dataType: "JSON",
                cache:false,
                data: {"id":id},
                success: function(response){
                    if(response.status){
                        count = parseInt(cat.parent().find("span").html());
                        total = parseInt($("#all-count").html());
                        cat.parent().remove();
                        $("#all-count").html(total - count);
                        $("#deleteCat_Modal").modal("hide");
                        $(".row .bg-white").load(location.href + " .row .bg-white");
                        $('#change-message').text("Category Deleted Successfully");
                        $('#message-notification').modal('show');
                        setTimeout(function() {
                            $('#message-notification').modal('hide');
                        }, 4000);
                    }else{
                        $("#deleteCat_Modal").modal("hide");
                        $('#change-message').text(response.message);
                        $('#message-notification').modal('show');
                        setTimeout(function() {
                            $('#message-notification').modal('hide');
                        }, 4000);
                    }
                },
                error: function(){
                }
            });
        });
    });
    
    $('.product-row').each(function(index,value){ 
		var pd = $(this).find('.product-description').text().trim().length;
		var height = $(this).height();
		height = height+25;
		$(this).css('min-height',height);
	});
    
  	var t = $('.datatables').dataTable({
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
            url: '<?php echo base_url('product/get_saved_prod')?>',
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
                   // return row.product_link;
                    return '<a href="'+row.product_link+'" style="color:black">'+row.product_name+' </a>';
				},
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    if(row.sp_img)
                    {
                        path = row.sp_img.split(",");
                        if(row.sp_local=='1'){
                            img = '<img src="<?php echo product_path()?>'+path[0]+'" class="img-fluid" style="height:50px">';
                            return img;                        }
                        else{
                            img = '<img src="'+path[0]+'" class="img-fluid" style="height:50px">';
                            return img;
                        }
                        //return path[0];
                    }
                    else{
                        path = row.iv_link.split(",");
                        if(row.pm_local=='1'){
                            img = '<img src="<?php echo product_path()?>'+path[0]+'" class="img-fluid" style="height:50px">';
                            return img;
                            //return " iv db s laingy";
                            }
                        else{
                            img = '<img src="'+path[0]+'" class="img-fluid" style="height:50px">';
                            return img;
                        }
                    
                    }
					//return row.product_name;
				},
                "targets": 2
            },
            {
                "render": function ( data, type, row ) {
					return row.condition_name;
				},
                "targets": 3
            },
            {
                "render": function ( data, type, row ) {
					return row.price;
				},
                "targets": 4
            },
            {
                "render": function ( data, type, row ) {
					return row.category_name;
				},
                "targets": 5
            },
            {
                "render": function ( data, type, row ) {
					return row.brand_name;
				},
                "targets": 6
            },
            {
                "render": function ( data, type, row ) {
                    //console.log(row.wish_id);
					var delete_button = '<div class=""><button class="btn btn-danger" data-wishid="'+row.wish_id+'" id="delete" onclick="delete_from_list(this)">Delete</button></div>'
					return delete_button;
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
			$(".description").each(function () {
				text = $(this).text();
				if (text.length > 200) {
					$(this).html(text.substr(0, 25) + '<span class="elipsis">' + text.substr(25) + '</span><a class="elipsis" href="#">...</a>');
				}
			});
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
	
	/*$( "#add" ).click(function() {
		event.preventDefault();
		$('#cat_name').attr("disabled", false); 
		$("#alreadyExistingCategories").prop('disabled', true); 
		$("#catNameInput").show();
	});*/
	
	// $( "#Submit" ).click(function() {
	// 	$.ajax({
	// 		type: "POST",
	// 		url: "<?php echo base_url()?>home/add_wishlist_category",
	// 		cache:false,
	// 		data: $('form#myform').serialize(),
	// 		success: function(response){
	// 			$('#myModal4').modal('hide');
	// 			$('#change-message').text("");
	// 				$('#change-message').text("New Wish List Category created.");
	// 				$('#change-message').text("You can now add new products in your newly created wishlist.");
	// 				$('#message-notification').modal('show');
	// 				setTimeout(function() {
	// 					$('#message-notification').modal('hide');
	// 					}, 4000);
    //                    location.reload(); 
	// 		},
	// 		error: function(){
	// 			alert("Error");
	// 		}
	// 	});
	// });
    return false;
	$('#myselect').on('change', function() {
		  //alert( this.value );
		  $.ajax({
			type: "POST",
			url: "<?php echo base_url()?>product/saved_for_later",
			cache:false,
			data: $('form#myform').serialize(),
			success: function(response){
				
			},
			error: function(){
				alert("Error");
			}
		});
	});
});

function delete_from_list(wish_id,e){  
	//var id = $(this).attr('data-wishid');
    
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('product/delete_from_list');?>",
		data: {'wish_id':wish_id},
		success: function(response){
			if(response){
                location.reload();
                //$(e).parent().parent().parent().parent().parent().remove();
            }
            else{
                // alert('something went wrong');
            }
		}
	});
}
var elements = document.getElementsByClassName("column");

// Declare a loop variable
var i;

// List View
function listView() {
	$(".product-row").removeClass( "col-4" );
	$(".product-row").addClass( "col-12" );
	$(".ProductImageForChangingInAnotherView").removeClass( "offset-md-3 col-6" );
	$(".PriceTagForChangingInAnotherView").removeClass( "col-6" );
	$(".pdBox").removeClass("col-sm-12 text-center");
    $(".forMargin").removeClass( "offset-md-2 col-sm-9");
    $(".ProductImageForChangingInAnotherView").addClass( "col-2" );
	$(".PriceTagForChangingInAnotherView").addClass( "col-3" );
    $(".forMargin").addClass( "col-sm-12" );
    $(".wishlistBtnsRow").addClass( "row" );
    //$(".pdBox").addClass("row");
    $('.listButton').addClass("active");
    $('.gridButton').removeClass("active");
    $(".wishlistBtnsRow-forgridview").hide();
    $(".wishlistBtnsRow").show();
}

// Grid View
function gridView() {
	$(".product-row").removeClass( "col-12" );
	$(".ProductImageForChangingInAnotherView").removeClass( "col-2" );
	$(".PriceTagForChangingInAnotherView").removeClass( "col-3" );
	$(".forMargin").removeClass( "col-sm-12" );
    $(".wishlistBtnsRow-forgridview").removeClass("row");
    $(".pdBox").removeClass("row");
	$(".product-row").addClass( "col-4" );
	$(".ProductImageForChangingInAnotherView").addClass( "offset-md-3 col-6" );
	$(".forMargin").addClass( "col-sm-12" );
    $(".wishlistBtnsRow-forgridview").addClass("col-sm-12 text-center");
    $(".pdBox").addClass("col-sm-12 text-center");
    $(".wishlistBtnsRow").hide();
    $('.gridButton').addClass("active");
    $('.listButton').removeClass("active");
    $(".wishlistBtnsRow-forgridview").show();
}
<?php
    if(isset($_SESSION['wishlist_view']) && $_SESSION['wishlist_view']=="list"){ ?>
        listView();	
<?php }
    if(isset($_SESSION['wishlist_view']) && $_SESSION['wishlist_view']=="grid"){ ?>
        gridView();
    <?php } ?>
function sendingViewStatus(view){
	currentView = "&wishlist_view=" + view;
	var currentLocation = window.location.href+currentView;
	$.ajax
    ({ 
        url: '<?php echo base_url('product/updateViewForWishlist');?>',
		type: "POST",
		dataType :'html',
		data: {wishlist_view:view},
		success: function(result){},
    	error: function (request, status, error) {
        	console.log("request: "+request);
        	console.log("status: "+status);
        	console.log("error: "+error);
        }
    });
	if(view == 'grid'){
		gridView();
	}
	if(view == 'list'){
		listView();
	}
}
</script>