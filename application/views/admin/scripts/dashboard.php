<script>
    var box_setting = [];
    var ds_id="";
    var current_setting;
    var is_admin = <?php echo $is_admin?>;
    <?php if($dashboard_settings){?>
        ds_id = "<?php echo $dashboard_settings->id?>";
        current_setting = "<?php echo $dashboard_settings->box_setting;?>";
    <?php }?>
    window.onload = function() {
        fixBrokenImages('<?php echo profile_path("defaultprofile.png");?>');
    }
    $(document).ready(function(e){
        if(current_setting){
            current_setting = current_setting.split(",");
            $(current_setting).each(function(index,value){
                $("[data-function='"+value+"']").trigger("click");
                //createBox(value);
            });
        }else{
            orderList("");
            messageList("");
            inventoryList("");
            productList("");
            requestList("");
        }
        $(".breadcrumb-holder").css("position","relative");
        $(".breadcrumb-holder").append('<span class="setting-icon" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-cogs"></i></span>');
    });
    function saveSettings(){
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:true,
            url: "<?php echo base_url()?>seller/dashboard/saveSettings",
            data:{"id":ds_id,'seller_id':"<?php echo $seller_id;?>","box_setting":box_setting},
            success: function(response){
                $("#exampleModal").modal("hide")
                console.log(response);
            }
        });
    }
    function createBox(func){
        if(func == "orderList"){
            orderList("");
        }else if(func == "inventoryList"){
            inventoryList("");
        }else if(func == "productList"){
            productList("");
        }else if(func == "storeList" && is_admin){
            storeList("");
        }else if(func == "requestList"){
            requestList("");
        }else if(func == "messageList"){
            messageList("");
        }
        box_setting.push(func);
    }
    function settings(e){
        id = "#"+$(e).attr("id");
        func = $(e).data("function");
        if($(id).is(":checked")){
            createBox(func);      
        }else{
            var check = box_setting.indexOf(func);
            if (check > -1) {
                box_setting.splice(check, 1); // 2nd parameter means remove one item only
            }
            $("#"+func).remove();
        }
    }
    function orderList(data){
        var status = 1;
        var listData = "";
        var invoice = "";
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:false,
            url: "<?php echo base_url()?>seller/dashboard/getOrdersData",
            data:{"status":status,'seller_id':"<?php echo $seller_id;?>","is_admin":is_admin},
            success: function(response){
                if(response.status == "success"){
                    $(response.result).each(function(index,value){
                        invoice = "<a href='<?php echo base_url("seller/sales/getInvoice/") ?>" + value.order_id + "' class='btn btn-info' role='button' target='_blank'>Get Invoice</a>";
                        listData += '<tr><td>'+value.order_id+'</td><td>'+value.created+'</td><td>'+value.amount+'</td><td>'+invoice+'</td></tr>';
                    })
                }else{
                    listData = '<tr><td align="center" colspan=4>No Order Found.</td></tr>';
                }
                orderTable(listData);
            }
        });
    }
    function orderTable(listData){
        var order = '<div class="col col-md-6 dash_box" id="orderList">'+
                        '<div class="table-responsive">'+
                            '<table class="table table-striped bg-white border_radius">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan=2><h4>Orders</h4></th>'+
                                        '<th colspan=2 class="text-right"><select><option>New</option><option>Completed</option><option>Cancelled</option><option>Cancel Order Request</option></select></th>'+
                                    '</tr>'+
                                    '<tr>'+
                                        '<th scope="col">Id</th>'+
                                        '<th scope="col">Date</th>'+
                                        '<th scope="col">Amount</th>'+
                                        '<th scope="col">Invoice</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    listData+
                                '</tbody>'+
                                '<tfoot>'+
                                    '<tr>'+
                                        '<td align="center" colspan=4><a href="">View All</a></td>'+
                                    '</tr>'+
                                '</tfoot>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        
        $("#dashboard").append(order);
    }
    function inventoryList(data){
        var listData = "";
        var approve = "";
        var title = "";
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:false,
            url: "<?php echo base_url()?>seller/dashboard/getInventoryData",
            data:{'seller_id':"<?php echo $seller_id;?>","approve":approve,"is_admin":is_admin},
            success: function(response){
                if(response.status == "success"){
                    $(response.result).each(function(index,value){
                        icon = "";
                        variant = (value.variant)?value.variant:"None";
                        title = "";
                        if(value.is_warning != "1"){
                            title = "Near to expire";
                            icon = '<i class="fa fa-info-circle text-warning"></i>';
                        }
                        listData += '<tr title="'+title+'"><td>'+value.product_name+" "+icon+'</td><td>'+value.condition_name+'</td><td>'+variant+'</td><td>'+value.price+'</td><td>'+value.quantity+'</td></tr>';
                    })
                }else{
                    listData = '<tr><td align="center" colspan=4>No Inventory Found.</td></tr>';
                }
                inventoryTable(listData);
            }
        });
    }
    function inventoryTable(listData){
        var invenory = '<div class="col col-md-6 dash_box" id="inventoryList">'+
                        '<div class="table-responsive">'+
                            '<table class="table table-striped bg-white border_radius">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan=3><h4>Invnetory</h4></th>'+
                                        '<th colspan=2 class="text-right"><select><option>List</option><option>Deleted List</option></select></th>'+
                                    '</tr>'+
                                    '<tr>'+
                                        '<th scope="col">Title</th>'+
                                        '<th scope="col">Condition</th>'+
                                        '<th scope="col">Variant</th>'+
                                        '<th scope="col">Price</th>'+
                                        '<th scope="col">Stock</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    listData+
                                '</tbody>'+
                                '<tfoot>'+
                                    '<tr>'+
                                        '<td align="center" colspan=4><a href="">View All</a></td>'+
                                    '</tr>'+
                                '</tfoot>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        $("#dashboard").append(invenory);
    }
    function productList(data){
        var listData = "";
        var approve = "";
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:false,
            url: "<?php echo base_url()?>seller/dashboard/getProductData",
            data:{'seller_id':"<?php echo $seller_id;?>","approve":approve,"is_admin":is_admin},
            success: function(response){
                if(response.status == "success"){
                    $(response.result).each(function(index,value){
                        listData += '<tr><td>'+value.product_name+'</td><td>'+value.category_name+'</td><td>'+value.brand_name+'</td><td>'+value.store_name+'</td></tr>';
                    })
                }else{
                    listData = '<tr><td align="center" colspan=4>No Product Found.</td></tr>';
                }
                productTable(listData);
            }
        });
    }
    function productTable(listData){
        console.log(listData);
        var product = '<div class="col col-md-6 dash_box" id="productList">'+
                        '<div class="table-responsive">'+
                            '<table class="table table-striped bg-white border_radius">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan=3><h4>Product</h4></th>'+
                                        '<th colspan=2 class="text-right"><select><option>Pending</option><option>Deleted List</option></select></th>'+
                                    '</tr>'+
                                    '<tr>'+
                                        '<th scope="col">Title</th>'+
                                        '<th scope="col">Category</th>'+
                                        '<th scope="col">Brand</th>'+
                                        '<th scope="col">Store Name</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    listData+
                                '</tbody>'+
                                '<tfoot>'+
                                    '<tr>'+
                                        '<td align="center" colspan=4><a href="">View All</a></td>'+
                                    '</tr>'+
                                '</tfoot>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        $("#dashboard").append(product);
    }
    function storeList(data){
        var listData = "";
        var approve = "";
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:false,
            url: "<?php echo base_url()?>seller/dashboard/getStoreData",
            data:{"is_admin":is_admin},
            success: function(response){
                if(response.status == "success"){
                    $(response.result).each(function(index,value){
                        listData += '<tr><td>'+value.product_name+'</td><td>'+value.category_name+'</td><td>'+value.brand_name+'</td><td>'+value.store_name+'</td></tr>';
                    })
                }else{
                    listData = '<tr><td align="center" colspan=4>No Product Found.</td></tr>';
                }
                storeTable(listData);
            }
        });
    }
    function storeTable(listData){
        var store = '<div class="col col-md-6 dash_box" id="storeList">'+
                        '<div class="table-responsive">'+
                            '<table class="table table-striped bg-white border_radius">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan=3><h4>Store</h4></th>'+
                                        '<th colspan=2 class="text-right"><select><option>Pending</option><option>Deleted List</option></select></th>'+
                                    '</tr>'+
                                    '<tr>'+
                                        '<th scope="col">Created by</th>'+
                                        '<th scope="col">Email</th>'+
                                        '<th scope="col">Store Name</th>'+
                                        '<th scope="col">Fullfillment</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    '<tr>'+
                                        '<th>1</th>'+
                                        '<td>Mark</td>'+
                                        '<td>Otto</td>'+
                                        '<td>@mdo</td>'+
                                    '</tr>'+
                                '</tbody>'+
                                '<tfoot>'+
                                    '<tr>'+
                                        '<td align="center" colspan=4><a href="">View All</a></td>'+
                                    '</tr>'+
                                '</tfoot>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        $("#dashboard").append(store);
    }
    function requestList(data){
        var listData = "";
        var status = '<td class="text-warning">Pending</td>';
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:false,
            url: "<?php echo base_url()?>seller/dashboard/getRequestData",
            data:{'seller_id':"<?php echo $seller_id;?>","is_admin":is_admin},
            success: function(response){
                if(response.status == "success"){
                    $(response.result).each(function(index,value){
                        if(value.status == 1){
                            status = '<td class="text-success">Approved</td>';
                        }else if(value.status == 2){
                            status = '<td class="text-danger">Rejected</td>';
                        }
                        listData += '<tr><td>'+value.request_for+'</td><td>'+value.request_name+'</td><td>'+value.request_info+'</td>'+status+'</tr>';
                    })
                }else{
                    listData = '<tr><td align="center" colspan=4>No Request Found.</td></tr>';
                }
                requestTable(listData);
            }
        });
    }
    function requestTable(listData){
        var request = '<div class="col col-md-6 dash_box" id="requestList">'+
                        '<div class="table-responsive">'+
                            '<table class="table table-striped bg-white border_radius">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan=2><h4>Request</h4></th>'+
                                        '<th colspan=2 class="text-right"><select><option>New</option><option>Completed</option><option>Cancelled</option><option>Cancel Order Request</option></select></th>'+
                                    '</tr>'+
                                    '<tr>'+
                                        '<th scope="col">Request for</th>'+
                                        '<th scope="col">Request Name</th>'+
                                        '<th scope="col">Additional Info</th>'+
                                        '<th scope="col">Status</th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                listData+
                                '</tbody>'+
                                '<tfoot>'+
                                    '<tr>'+
                                        '<td align="center" colspan=4><a href="">View All</a></td>'+
                                    '</tr>'+
                                '</tfoot>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        $("#dashboard").append(request);
    }
    function messageList(data){
        var listData ="";
        $.ajax({
            type: "POST",
            cache:false,
            dataType: "JSON",
            async:false,
            url: "<?php echo base_url()?>seller/dashboard/getMessageData",
            data:{'seller_id':"<?php echo $seller_id;?>"},
            success: function(response){
                if(response.rows > 0){
                    $(response.result).each(function(index,value){
                        listData += '<tr><td class="position-relative"><a href="<?php echo base_url("seller/message?id=")?>'+value.buyer_id+"-"+value.product_variant_id+"-"+value.item_type+'"><img height="50px" width="50px" src="<?php echo profile_path("buyer_")?>'+value.buyer_id+'.png"/><div class="chat-div"><h5>'+value.sender_name+'</h5><small>'+value.message+'</small> <small class="float-right">'+value.sent_datetime+'</small></div></a></td></tr>';
                    })
                }else{
                    listData = '<tr><td align="center">No Message Found.</td></tr>';
                }
                messageTable(listData);
            }
        });
    }
    function messageTable(listData){
        var message = '<div class="col col-md-6 dash_box" id="messageList">'+
                        '<div class="table-responsive">'+
                            '<table class="table table-striped bg-white border_radius">'+
                                '<thead>'+
                                    '<tr>'+
                                        '<th colspan=2><h4>Message</h4></th>'+
                                    '</tr>'+
                                '</thead>'+
                                '<tbody>'+
                                    listData+
                                '</tbody>'+
                                '<tfoot>'+
                                    '<tr>'+
                                        '<td align="center" colspan=4><a href="">View All</a></td>'+
                                    '</tr>'+
                                '</tfoot>'+
                            '</table>'+
                        '</div>'+
                    '</div>';
        $("#dashboard").append(message);
    }
    fixBrokenImages = function( url ){
        var img = document.getElementsByTagName('img');
        var i=0, l=img.length;
        for(;i<l;i++){
            var t = img[i];
            if(t.naturalWidth === 0){
                //this image is broken
                t.src = url;
            }
        }
    }
</script>