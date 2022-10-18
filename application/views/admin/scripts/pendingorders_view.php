<input type="hidden" id="s_id">
<input type="hidden" id="td_id">
<input type="hidden" id="t_id">
<input type="hidden" id="hubx_id">
<input type="hidden" id="hubx_bypass" value="0">
<script>
    //setTimeout(function(){$(".viewbtn").attr("target","_blank");},1000);
    //	setTimeout(function(){window.location.href = window.location.href;},(1000*60*2));
    var total;
    var t;

    function format(orderResult) {
        var stringHtml = "";
        var inc = 0;
        var inc2 = 0;
        $.each(orderResult, function(index, value) {
            stringHtml += '<div class="container-fluid mt-3"><table  cellpadding="5" class="table table-striped" cellspacing="0" border="0"><thead><tr>';
            $.each(value.order_group, function(ind, val) {
                if (inc === 6) {
                    return false;
                } else {
                    stringHtml += '<th class="stripeHeader">' + val + '</th>';
                }
                inc++;
            });
            stringHtml += '</tr></thead>';
            //for(var i=0; i<varients.length; i++){
            stringHtml += '<tbody>';
            $.each(value.order_title, function(ind, val) {
                // console.log(inc2);
                // console.log(ind);
                if (inc2 == 0) {
                    stringHtml += "<tr class='order" + orderResult.order_id + "'>";
                } else if (inc2 == 6) {
                    stringHtml += "<tr class='order" + orderResult.order_id + "'>";
                    inc2 = 0;
                }
                stringHtml += '<td>' + val + '</td>';
                inc2++;
                // $(val).each(function(i,v){
                // 	stringHtml += '<td>'+v+ '</td>'; 
                // 	inc2++;
                // });
                if (inc2 >= inc) {
                    stringHtml += '</tr>';
                    inc2 = 0;
                }
            });
            stringHtml += '<tbody></table></div>';
            inc = 0;
        });
        return stringHtml;
    }
    $.fn.dataTable.pipeline = function(opts) {
        // Configuration options
        var conf = $.extend({
            pages: 5, // number of pages to cache
            url: '', // script url
            data: null, // function or object with parameters to send to the server
            // matching how `ajax.data` works in DataTables
            method: 'POST' // Ajax HTTP method
        }, opts);

        // Private variables for storing the cache
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;

        return function(request, drawCallback, settings) {
            var ajax = false;
            var requestStart = request.start;
            var drawStart = request.start;
            var requestLength = request.length;
            var requestEnd = requestStart + requestLength;
            request.recordsTotal = total;
            if (settings.clearCache) {
                // API requested that the cache be cleared
                ajax = true;
                settings.clearCache = false;
            } else if (cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper) {
                // outside cached data - need to make a request
                ajax = true;
            } else if (JSON.stringify(request.order) !== JSON.stringify(cacheLastRequest.order) ||
                JSON.stringify(request.columns) !== JSON.stringify(cacheLastRequest.columns) ||
                JSON.stringify(request.search) !== JSON.stringify(cacheLastRequest.search)
            ) {
                // properties changed (ordering, columns, searching)
                ajax = true;
            }

            // Store the request for checking next time around
            cacheLastRequest = $.extend(true, {}, request);

            if (ajax) {
                // Need data from the server
                if (requestStart < cacheLower) {
                    requestStart = requestStart - (requestLength * (conf.pages - 1));

                    if (requestStart < 0) {
                        requestStart = 0;
                    }
                }

                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);

                request.start = requestStart;
                request.length = requestLength * conf.pages;

                // Provide the same `data` options as DataTables.
                if ($.isFunction(conf.data)) {
                    // As a function it is executed with the data object as an arg
                    // for manipulation. If an object is returned, it is used as the
                    // data object to submit
                    var d = conf.data(request);
                    if (d) {
                        $.extend(request, d);
                    }
                } else if ($.isPlainObject(conf.data)) {
                    // As an object, the data given extends the default
                    $.extend(request, conf.data);
                }

                settings.jqXHR = $.ajax({
                    "type": conf.method,
                    "url": conf.url,
                    "data": request,
                    "dataType": "json",
                    "cache": false,
                    "success": function(json) {
                        cacheLastJson = $.extend(true, {}, json);

                        if (cacheLower != drawStart) {
                            json.data.splice(0, drawStart - cacheLower);
                        }
                        json.data.splice(requestLength, json.data.length);
                        drawCallback(json);

                    }
                });
            } else {
                json = $.extend(true, {}, cacheLastJson);
                json.draw = request.draw; // Update the echo for each response
                json.data.splice(0, requestStart - cacheLower);
                json.data.splice(requestLength, json.data.length);
                drawCallback(json);
            }


        }
    };

    // Register an API method that will empty the pipelined data, forcing an Ajax
    // fetch on the next draw (i.e. `table.clearPipeline().draw()`)
    $.fn.dataTable.Api.register('clearPipeline()', function() {
        return this.iterator('table', function(settings) {
            settings.clearCache = true;
        });
    });
    $(document).ready(function(e) {
        //var action;
        //var a=1;
        //var start;
        //var invite_button;
        //  var classname;
        // var end;
        var approvedForRefund = false;
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
                url: '<?php echo base_url('seller/sales/pendingorders_data') ?>',
                pages: 5, // number of pages to cache
                //method: "POST"
                data: {
                    expired: <?php echo $delete; ?>
                },
            }),

            "columnDefs": [{
                    "render": function(data, type, row) {
                        return "";
                    },

                    "targets": 0
                },
                {
                    "render": function(data, type, row) {
                        return row.order_id;
                    },
                    "targets": 1
                },
                {
                    "render": function(data, type, row) {
                        var date = "";
                        row.created = row.created.replace(/-/g, '/')
                        date = new Date(row.created + " UTC");
                        date = new Date(date.toString());
                        return formatAMPM(date);

                    },
                    "targets": 2
                },
                {
                    "render": function(data, type, row) {
                        // console.log(row);
                        return row.shipping.address_1 + "<br/>" + row.shipping.city + ',' + row.shipping.state + ' ' + row.shipping.zipcode + '<br/>' + row.shipping.country + '<br/>' + row.shipping.name + "<br/>" + row.shipping.phone;
                    },
                    "targets": 3
                },
                {
                    "render": function(data, type, row) {
                        return parseFloat(row.item_gross_amount).toFixed(2);
                    },
                    "targets": 4
                },
                {
                    "render": function(data, type, row) {
                        var link = "<?php echo base_url() ?>";
                        var button = "<button type='button' class='btn btn-primary' onClick='opmodal(" + row.order_id + ")'>View</button>";
                        return button;
                    },

                    "targets": 5
                },
                /* {
                "render": function ( data, type, row )
                 {
                    var all = [];
                    var allSame = true;
                    //console.log(row);
                    var checked =row.order_id;

                    // var accept = "<?php //echo base_url('seller/sales/accept_order/1/')
                                        ?>"+row.order_id;
                    var decline = "<?php //echo base_url('seller/sales/accept_order/2/')
                                    ?>"+row.order_id;
                    var action='Action';
                    <?php if ($_SESSION['is_zabee'] == 0) { ?>
                        var msg = '<div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">'+action+'</button><div class="dropdown-menu"><a class="dropdown-item" id="orderAcceptId" data-row_id="'+row.order_id+'" data-toogle="" onclick="openTrackingInput('+checked+')">Accept</a><a class="dropdown-item" href="'+decline+'">Decline</a></div></div>';
                    <?php } else { ?>
                        if(row.orders.length > 1){
                            $.each(row.orders, function(index, item) {
                                all.push(row.orders[index].action); 
                            });
                            var first = all[0];
                            all.every(function(element) {
                                if(element !== first){
                                    allSame = false;
                                }
                            });
                            if(allSame){
                                if(first == 1){
                                    var msg = '<div><h6 style="color:green">Approved</h6></div>';    
                                    approvedForRefund = true;
                                } else {
                                    var msg = '<div><h6 style="color:red">Declined</h6></div>';
                                    approvedForRefund = false;
                                }
                            } else {
                                '<div><h6 style="color:black">Partially Approved, waiting for the status of other items</h6></div>';
                                approvedForRefund = false;
                            }
                        } else {
                            //console.log(row.orders[0].action);
                            if(row.orders[0].action == 1){
                                var msg = '<div><h6 style="color:green">Approved</h6></div>';  
                                approvedForRefund = true;  
                            } else if(row.orders[0].action == 2){
                                var msg = '<div><h6 style="color:red">Declined</h6></div>';
                                approvedForRefund = false;
                            } else {
                                var msg = '<div><h6 style="color:grey">Waiting for warehouse approval</h6></div>';
                                approvedForRefund = false;
                            }
                        }
                        while(all.length > 0) {
                            all.pop();
                        }
                    <?php } ?>
                    return msg;
                },
               
                "targets": 6
            },*/
                {
                    "render": function(data, type, row) {
                        // var checked =jparser(row);
                        var button = "<a href='<?php echo base_url("seller/sales/getInvoice/") ?>" + row.order_id + "' class='btn btn-info' role='button'>Get Invoice</a>";
                        return button;
                    },

                    "targets": 6
                },
                // {
                //     "render": function ( data, type, row ) {
                //         // var checked =jparser(row);
                //         if(approvedForRefund){
                //             var button = '<div class="text-center"><a class="btn btn-default"  data-order_id="'+row.order_id+'" onclick="askRefund(this)" style="color:white; width:95px;">Refund</a></div>';
                //         } else {
                //             var button = "<p>Not Available</p>";    
                //         }
                //         return button;
                //     },

                //     "targets": 8
                // },
                {
                    "render": function(data, type, row) {
                        var varrient_button = '<button class="btn btn-secondary smallButton" type="button"><i class="fas fa-plus-square plusBtn" aria-hidden="true" data-isclick="0"></i></button>';
                        return varrient_button;
                    },
                    "className": "details-control text-center",
                    "targets": 7
                },


            ],
            "fnDrawCallback": function(oSettings) {
                var d = 0;
                for (var i = start, iLen = oSettings.aiDisplay.length; i < end; i++) {
                    $('td:eq(0)', oSettings.aoData[oSettings.aiDisplay[d]].nTr).html(i + 1);
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
            "fnFooterCallback": function(nFoot, aData, iStart, iEnd, aiDisplay) {
                start = iStart;
                end = iEnd;
            },
            "initComplete": function(settings, json) {
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
        $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Search...');
        $('.dataTables_length select').addClass('form-control');
        $('.datatables tbody').on('click', 'td.details-control', function() {
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
                    cache: false,
                    dataType: "JSON",
                    async: true,
                    url: "<?php echo base_url('seller/sales/get_all_order_items'); ?>",
                    data: {
                        'o_id': order_id
                    },
                    success: function(data) {
                        if (data.row > 0) {
                            console.log(row);
                            row.child(format(data.result)).show();
                            row.child().attr('id','changeStripe'+order_id);
                            tr.addClass('shown');
                            tdi.first().removeClass('fa-plus-square');
                            tdi.first().addClass('fa-minus-square');
                            //tdi.attr('data-isclick',1);		
                        }

                    }
                });
            }
        });
        t.on("user-select", function(e, dt, type, cell, originalEvent) {
            if ($(cell.node()).hasClass("details-control")) {
                e.preventDefault();
            }
        });
    });

    function openTrackingInput(Id) {
        $("#trackingIdModal").modal('show');
        var accept = "<?php echo base_url('seller/sales/accept_order/1/') ?>" + Id;
        $('#trackingAccept').attr("href", accept);
    }

    function addTrackingNumber() {
        var tracking_number = $('#trackingNumber').val();
        var h = $('#trackingAccept').attr("href");
        acceptId = h + "/" + tracking_number;
        $('#trackingAccept').attr("href", acceptId);
    }

    function multiApprove() {
        $(".childCheck:checked").each(function(index, value) {
            console.log(this);
            console.log(value);
        })
    }

    function askApprove(identifier,hit) {
        var id = $(identifier).attr("data-tid");
        var checkbox = $(identifier).attr("data-tdid");
        if(hit == 0){
            if(!$("#customCheck"+checkbox).is(":checked")){
                $("#customCheck"+checkbox).prop("checked",true);
            }
        }
        $("#t-id").val(id);
        $("#payment_type").val('');
        $("#track-number").val("");
        $("#shipping-provider").val("");
        $("#shipping-service").val("");
        $('#approve_modal').modal('show');
        //$("#td_id").val($(identifier).data('tdid'));
        //$("#t_id").val($(identifier).data('tid'));
        //$("#s_id").val($(identifier).data('sellerid'));
        //$("#hubx_id").val($(identifier).data('hubx_id'));
    }
    $(document).on('click', '#acceptBtn', function() {
        var dataSet = {};
        var keyString;
        var payment_type;
        //var trans_details_Id = $("#td_id").val();
        //var sellerid = $("#s_id").val();
        //var order_id = $("#t_id").val();
        //var hubx_id = $("#hubx_id").val();
        //var payment_type = $("#approve_modal #payment_type").val();
        var hubx_bypass = $("#hubx_bypass").val();
        var tracking_number = $("#track-number").val();
        var shipping_provider = $("#shipping-provider").val();
        var shipping_service = $("#shipping-service").val();
        var t_id = $("#t-id").val();
        var order_id = "";
        $(".childCheck"+t_id+":checked").each(function(index, value) {
            //console.log(this);
            keyString = "customCheck" + $(value).data("tdid");
            order_id = $(value).data("tid");
            payment_type = $(value).data("pay_type");
            dataSet[keyString] = {
                "trans_details_Id": $(value).data("tdid"),
                "sellerid": $(value).data("sellerid"),
                'order_id': $(value).data("tid"),
                "hubx_id": $(value).data("hubx_id"),
                "hubx_bypass": hubx_bypass
            }
            //dataSet.push(d);

        });
        //console.log(dataSet);
        //return false;
        dataSet["shippingData"] = {
            "tracking_number": tracking_number,
            "shipping_provider": shipping_provider,
            "shipping_service": shipping_service,
            "payment_type": payment_type,
        }
        //return false;
        var urlLink = "<?php echo base_url('seller/sales/approveOrder'); ?>";

        $.ajax({
            type: "POST",
            cache: false,
            dataType: "JSON",
            async: true,
            url: urlLink,
            data: {
                "data": dataSet
            }, //{'trans_details_Id':trans_details_Id,'sellerid' :sellerid, 'order_id':order_id,"tracking_number":tracking_number,"shipping_provider":shipping_provider,"shipping_service":shipping_service, "payment_type":payment_type,"hubx_id":hubx_id,"hubx_bypass":hubx_bypass},
            success: function(response) {
                $("#hubx_bypass").val(0);
                $(response).each(function(index, value) {
                    if (value.message == "cancel requested") {
                        $('#approve_modal').modal('hide');
                        $('#decision-modal').modal('show');
                        $("#decision-modal #td_id").val(dataSet[value.key].trans_details_Id);
                    }
                    if (value.status != 3) {
                        if (value.status == 1) {
                            $('#'+value.key).parents().eq(2).find("td:nth-last-child(2)").html("<h6 class='success'>" + value.message + "</h6>");;
                            $('#'+value.key).parent().remove();
                            $('#approve_modal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else if (value.status == 2) {
                            $('#approve_modal').modal('hide');
                            $("#message-notification").modal("show");
                            $("#change-message").html(value.msg);
                            $("#message-notification .panel-body").append('<hr><div class="text-right pr-2"><a href="javascript:void(0)" onClick="hubx_bypass(1)">Accept Order</a> | <a href="javascript:void(0)" onClick="hubx_bypass(2)">Reject Order</a></div>');
                        } else if (value.status == 4) {
                            $('#approve_modal').modal('hide');
                            $("#message-notification").modal("show");
                            $("#change-message").html(value.msg);
                            $("#message-notification .panel-body").append('<hr><div class="text-right pr-2"><a href="javascript:void(0)" onClick="hubx_bypass(2)">Reject Order</a></div>');
                        } else {
                            $('#approve_modal').modal('hide');
                            $(".order"+order_id + " td:nth-last-child(2)").html("<h6 class='error'>" + value.message + "</h6>");
                            $("#ac_all"+t_id).remove();
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                            return false;
                        }
                    } else {
                        $('#approve_modal').modal('hide');
                        $(".order" + order_id + " td:nth-last-child(2)").html("<h6 class='error'>" + value.message + "</h6>");
                        $("#ac_all"+t_id).remove();
                        return false;
                    }
                })
                return false;
                /*$("#hubx_bypass").val(0);
                if(response.message == "cancel requested"){
                    $('#approve_modal').modal('hide');
                    $('#decision-modal').modal('show');
                    $("#decision-modal #td_id").val($("#td_id").val());
                }
                if(response.status != '3'){
                    if(response.status == '1'){
                        setTimeout(function(){
                            location.reload();
                        }, 200); 
                    }else if(response.status == 2){
                        $('#approve_modal').modal('hide');
                        $("#message-notification").modal("show");
                        $("#change-message").html(response.msg);
                        $("#message-notification .panel-body").append('<hr><div class="text-right pr-2"><a href="javascript:void(0)" onClick="hubx_bypass(1)">Accept Order</a> | <a href="javascript:void(0)" onClick="hubx_bypass(2)">Reject Order</a></div>');
                    }else if (response.status == 4){
                        $('#approve_modal').modal('hide');
                        $("#message-notification").modal("show");
                        $("#change-message").html(response.msg);
                        $("#message-notification .panel-body").append('<hr><div class="text-right pr-2"><a href="javascript:void(0)" onClick="hubx_bypass(2)">Reject Order</a></div>');
                    }
                }else {
                    $('#approve_modal').modal('hide');
                    $(".order"+response.order_id+" td:nth-last-child(2)").html("<h6 class='error'>"+response.message+"</h6>");
                }*/
            }
        });
    });

    function hubx_bypass(trigger) {
        if (trigger == 1) {
            $("#hubx_bypass").val(1);
            $("#acceptBtn").trigger("click");
            $("#message-notification").modal("hide");
        } else {
            $("#message-notification").modal("hide");
            $('#decline_modal').modal('show');
            $("#rejectBtn").trigger("click");
            $('#decline_modal').modal('hide');
        }
        $("#message-notification .panel-body").html('<strong style="color:green;font-variant: small-caps;" id="change-message"></strong>');
    }

    function askDecline(identifier,hit) {
        var id = $(identifier).attr("data-tid");
        var checkbox = $(identifier).attr("data-tdid");
        if(hit == 0){
            if(!$("#customCheck"+checkbox).is(":checked")){
                $("#customCheck"+checkbox).prop("checked",true);
            }
        }
        $("#t-id").val(id);
        $('#decline_modal').modal('show');
        $("#decline_modal #payment_type").val("");
        return false;
        // $("#declined_reason").next(".error").remove();
        //$("#td_id").val($(identifier).data('tdid'));
        //$("#s_id").val($(identifier).data('sellerid'));
        //$("#t_id").val($(identifier).data('tid'));
    }
    $(document).on('click', '#rejectBtn', function() {
        var t_id = $("#t-id").val();
        //var payment_type = $("#decline_modal #payment_type").val();
        var payment_type;
        var dataSet = {};
        var keyString;
        //var trans_details_Id = $("#td_id").val();
        //var sellerid = $("#s_id").val();
        //var order_id = $("#t_id").val();
        var urlLink = "<?php echo base_url('seller/sales/declineOrder'); ?>";
        var reason = $("#declined_reason").val();
        reason = reason.trim();
        $(".childCheck"+t_id+":checked").each(function(index, value) {
            keyString = "customCheck" + $(value).data("tdid");
            order_id = $(value).data("tid");
            payment_type = $(value).data("pay_type");
            dataSet[keyString] = {
                "trans_details_Id": $(value).data("tdid"),
                "sellerid": $(value).data("sellerid"),
                'order_id': order_id,
                "reason": reason,
                "payment_type": payment_type
            };
        });

        dataSet["cancelData"] = {
            "reason": reason
        };
        if (reason == "") {
            $("#declined_reason").next(".error").remove();
            $("#declined_reason").after('<span class="error">Please add reason for reject.</span>');
            return false;
        } else {
            $.ajax({
                type: "POST",
                cache: false,
                dataType: "JSON",
                async: true,
                url: urlLink,
                data: {
                    "data": dataSet
                }, //{'trans_details_Id': trans_details_Id, 'sellerid': sellerid, 'order_id': order_id, "reason":reason, "payment_type":payment_type},
                success: function(response) {
                    $(response).each(function(index, value) {
                        if (value.status == 1) {
                            $('#'+value.key).parents().eq(2).find("td:nth-last-child(2)").html("<h6 class='success'>" + value.message + "</h6>");;
                            $('#'+value.key).parent().remove();
                            $('#decline_modal').modal('hide');
                        } else if (value.status == 3) {
                            $('#decline_modal').modal('hide');
                            $(".order" + order_id + " td:nth-last-child(2)").html("<h6 class='error'>" + value.message + "</h6>");
                            $("#ac_all"+t_id).remove();
                            return false;
                        } else {
                            location.reload();
                        }
                    })
                    /*if(response.status != '3'){
                        if(response.status == '1'){
                            setTimeout(function(){
                                location.reload();
                            }, 200); 
                        }
                    } else {
                        $('#decline_modal').modal('hide');
                        $(".order"+response.order_id+" td:nth-last-child(2)").html("<h6 class='error'>"+response.message+"</h6>");
                    }*/
                }
            });
        }
    });

    function askRefund(identifier) {
        $('#refund-modal').modal('show');
        $("#order_id").val($(identifier).data('order_id'));
        $("#td_id").val($(identifier).data('tdid'));
    }

    $('#refund-order').on('click', function() {
        var order_id = $("#order_id").val();
        var tdID = $("#td_id").val();
        $.ajax({
            type: "POST",
            cache: false,
            dataType: "JSON",
            async: true,
            url: "<?php echo base_url('checkout/ProcessRefund'); ?>",
            data: {
                'order_id': order_id,
                'td_row_id': tdID
            },
            success: function(response) {
                if (response.status == 1) {
                    setTimeout(function() {
                        location.reload();
                    }, 200);
                }
            }
        });
    });
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

    //     var html = "<table class='table table-responsive w-100 d-block d-md-table table-bordered'><tr><th>Image</th><th>Title</th><th>Condition</th><th>QTY</th><th>Price</th><th>Tax</th><th>Shipping</th><th>Total</th></tr>";
    //     $(data.orders).each(function(index,element){
    //         $('#buyer_id').val(element.user_id);
    //         $('#sp_id').val(element.sp_id);
    //         $('#pv_id').val(element.product_vid);

    //         html+='<tr><td><img src="<?php //echo base_url() . "uploads/product/thumbs/" ?>'+element.image_link+'" class="img" height="25">'+'</td><td>'+element.product_name+'</td><td>'+element.condition_name +'</td><td>'+element.qty+'</td><td>'+element.price+'</td>'+
    //          '</td><td>'+element.tax_amount+'<td>'+element.item_shipping_amount+'</td><td>'+element.item_gross_amount+'</td></tr>';
    //     });
    //     html+="</table>";
    //     $("#products").html(html);
    //     $('#myModal').modal('show');

    //     //var v = url_de
    // }
    function opmodal(id) {
        $("#myModal .modal-body").load("<?php echo base_url() ?>saleView/" + id);
        $('#myModal').modal('show');
    }

    function jparser(data) {
        var d = JSON.stringify(data);
        return d;
    }

    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var strTime = months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear() + ' ' + hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }
    $('.contact-buyer').click(function() {
        // $("#pv_id").val($(this).attr('data-pv_id'));
        // $("#sp_id").val($(this).attr('data-sp_id'));
        // $("#seller_id").val($(this).attr('data-seller_id'));
        // var s = $(this).attr('data-store_name');
        // $('#storeName').html($(this).attr('data-store_name'));
        $("#message-panel").modal('show');
    });
    $('#sendMessage').click(function() {
        var subject = "";
        var message = $("#message").val();
        var pv_id = $("#pv_id").val();
        var buyer_id = $("#buyer_id").val();
        var sp_id = $("#sp_id").val();
        var UTCDateTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
        if (message == "") {
            // alert();
            $("#message").next().html('<strong class="error">Please Enter Message!</strong>');
            return false;
        }
        if (message != "") {
            $.ajax({
                type: "POST",
                cache: false,
                dataType: "JSON",
                async: true,
                url: "<?php echo base_url() ?>product/saveMessage",
                data: {
                    'receiver_id': buyer_id,
                    "item_id": sp_id,
                    "item_type": "product",
                    'message': message,
                    'buyer_id': buyer_id,
                    'seller_id': '<?php echo (isset($_SESSION['userid']) ? $_SESSION['userid'] : 0); ?>',
                    'product_variant_id': pv_id,
                    'subject': subject,
                    'time': UTCDateTime
                },
                success: function(response) {
                    if (response.status == 1) {
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

    function checkAll(e,id) {
        var parentChecked = $(e);
        if (parentChecked.is(":checked")) {
            $("#changeStripe"+id).before('<td colspan=8 id="ac_all'+id+'"><button class="btn" onClick="askApprove(this,1)" data-tid="'+id+'">Approve All</button> <button class="btn btn-danger" onClick="askDecline(this,1)" data-tid="'+id+'">Cancel All</button></td>');
            $(".childCheck"+id).prop("checked", true);
        } else {
            $("#ac_all"+id).remove();
            $(".childCheck"+id).prop("checked", false);
        }
    }

    function showCheckAll(id) {
        var countCheck = $(".childCheck"+id+":checked").length
        console.log(countCheck);
        var checkBtn = $("#ac_all"+id).length;
        if (countCheck > 1) {
            if (checkBtn == 0) {
                $("#changeStripe"+id).before('<td colspan=8 id="ac_all'+id+'"><button class="btn" onClick="askApprove(this,1)" data-tid="'+id+'">Approve All</button> <button class="btn btn-danger" onClick="askDecline(this,1)" data-tid="'+id+'">Cancel All</button></td>');
            }
        } else {
            $("#parentCheck"+id).prop("checked",false);
            $("#ac_all"+id).remove();
        }
    }
    // $('#orderAcceptId').on("click",function(){
    // var rowId = $(this).attr('data-row_id'); 
    // console.log(rowId);
    // $("#trackingIdModal").modal('show');
    // var accept = "<?php //echo base_url('seller/sales/accept_order/1/') ?>"+row.order_id;
    // $('#trackingAccept').attr("href",accept);
    // });  
</script>

<!-- Modal -->
<div class="modal fade" id="trackingIdModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4>Tracking Id Number</h4>
            </div>
            <div class="modal-body">
                <input type="text" id="trackingNumber" required>
            </div>
            <div class="modal-footer">
                <a href="" onclick='addTrackingNumber()' class="btn btn-success" id="trackingAccept">Order Accept</a>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="approve_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approval</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="">
                    <label>Tracking Number: </lable>
                        <div><input type="text" id="track-number"></div>
                        <label>Shipping Provider: </lable>
                            <div><input type="text" id="shipping-provider"></div>
                            <label>Shipping Service: </lable>
                                <div><input type="text" id="shipping-service"></div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="payment_type" name="payment_type">
                <input type="hidden" id="t-id" />
                <button class="btn btn-primary" id="acceptBtn">Approve</button>
                <a href="#" class="btn" data-dismiss="modal">No</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="decline_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="box-content">
                    Do you want to cancel?
                    <textarea class="form-control mt-2" id="declined_reason" placeholder="Enter Reject Reason" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="payment_type" name="payment_type">
                <button class="btn btn-primary" id="rejectBtn">Yes</button>
                <a href="#" class="btn" data-dismiss="modal">No</a>
            </div>
        </div>
    </div>
</div>

<script>
    $("#decision-approve").on('click', function() {

        tran_id = $("#td_id").val();
        console.log(tran_id);
        $.ajax({
            type: "POST",
            cache: false,
            dataType: "JSON",
            async: true,
            url: "<?php echo base_url("seller/sales/forceAcceptOrder") ?>",
            data: {
                'trans_id': tran_id
            },
            success: function(response) {
                if (response.status == "success") {
                    $('#change-message').html('Order Accepted Successfuly');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            }
        });
    });

    $("#decision-decline").on('click', function() {

        location.reload();

    });

    function paypalCapture(identifier) {
        $("#track-number").val("");
        $("#shipping-provider").val("");
        $("#shipping-service").val("");
        $('#approve_modal').modal('show');
        $("#td_id").val($(identifier).data('tdid'));
        $("#t_id").val($(identifier).data('tid'));
        $("#s_id").val($(identifier).data('sellerid'));
        $("#payment_type").val('paypal');
    }

    function paypalRefund(identifier) {
        $('#decline_modal').modal('show');
        // $("#declined_reason").next(".error").remove();
        $("#td_id").val($(identifier).data('tdid'));
        $("#s_id").val($(identifier).data('sellerid'));
        $("#t_id").val($(identifier).data('tid'));
        $("#decline_modal #payment_type").val('paypal');
    }
</script>