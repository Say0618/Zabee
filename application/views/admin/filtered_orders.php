<?php //echo "<pre>";print_r($orders);?> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php //echo"<pre>";print_r($products);die();?>
    <?php if(empty($orders)){ ?>
    <div><h3 class="p-5 text-center">No Product Found</h3></div>
    <?php } else{ ?>
    <div  id="printableArea">
    <h3 class="col-12 mt-1">Orders Report</h3>
    <div class="row mb-3 mt-3 pl-3">
    <?php if($this->session->userdata('user_type') != 1){ ?>
    <?php if($post_data['datepicker_order'] != ""){ ?>
        <div class="col-3">
                <div class="form-group">
                        <label for="" class="text-dark">Order Date:</label>
                        <span class="text-secondary"><?php echo $post_data['datepicker_order'] ?></span>
                    </div>
                </div>
            <?php } } ?>
        <?php if($post_data['search_status'] != ""){ ?>
        <div class="col-3">
                <div class="form-group">
                        <label for="" class="text-dark">Product Status:</label>
                        <span class="text-secondary"><?php if($post_data['search_status'] == "1"){echo "Approved";} else if($post_data['search_status'] == "2"){echo "Declined";} else{echo"Pending";} ?></span>
                    </div>
                </div>
            <?php } ?>
            <?php if($post_data['search_product'] != ""){ ?>
        <div class="col-3">
                <div class="form-group">
                        <label for="" class="text-dark">Product Name:</label>
                        <span class="text-secondary"><?php echo $post_data['search_product'] ?></span>
                    </div>
                </div>
            <?php } ?>
        <?php if($this->session->userdata('user_type') == 1){ ?>
            <?php if($post_data['search_seller'] != ""){ ?>
            <div class="col-3">
                <div class="form-group">
                        <label for="Student" class="text-dark">Store Name:</label>
                        <span class="text-secondary"><?php echo str_replace('-'," ",$post_data['search_seller']) ?></span>
                    </div>
                </div>
            <?php } ?>
        <?php if($post_data['datepicker_from'] != ""){ ?>
            <div class="col-3">
                <div class="form-group">
                    <label for="Student">Order Placed From:</label>
                    <?php $date_from = DateTime::createFromFormat('d/m/Y', $post_data['datepicker_from']); ?>
                        <span class="text-secondary"><?php echo formatDateTime($date_from->format('d-m-Y'), FALSE); ?></span>
                    </div>
                </div>
            <?php } ?>
        <?php if($post_data['datepicker_to'] != ""){ ?>
            <div class="col-3">
                <div class="form-group">
                    <label for="Student">Order Placed To:</label>
                    <?php $date_to = DateTime::createFromFormat('d/m/Y', $post_data['datepicker_to']); ?>
                        <span class="text-secondary"><?php echo formatDateTime($date_to->format('d-m-Y'), FALSE); ?></span>
                    </div>
                </div>
            <?php } } ?>
    </div>
    <div style="padding:15px">
        <table id="filtered_table" class="table-bordered">
            <thead>
                <th>S.No</th>
                <th>Order Date</th>
                <th>Order ID</th>
                <th>Seller Store</th>
                <th>Product Name</th>
                <th>Condition</th>
                <th>status</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Shipping</th>
                <th>Tax</th>
                <th>Has Voucher</th>
                <th>Voucher Code</th>
                <th>total</th>
            </thead>
            <tbody>
                <?php $i=1; 
                foreach($orders as $product){ //echo "<pre>"; print_r($product); die();?>
                <tr>
                    <td style="text-align:center"><?php echo $i;?></td>
                    <td><?php echo formatDateTime(date('d-m-Y', strtotime($product->created)), FALSE) ?></td>
                    <td><?php echo $product->order_id?></td>
                    <td><?php echo $product->store?></td>
                    <td><?php echo wordwrap($product->product_name,20,"<br />\n")?></td>
                    <td><?php echo $product->condition_name?></td>
                    <td><?php if($product->action == 0){echo "Pending";} else if($product->action == 1){echo "Accepted";} else{echo "Declined";}?></td>
                    <td><?php echo number_format($product->price, 2)?></td>
                    <td><?php echo $product->qty?></td>
                    <td><?php echo number_format($product->item_shipping_amount, 2)?></td>
                    <td><?php echo number_format($product->tax_amount, 2); ?></td>
                    <td><?php echo ($product->hasDiscount == 0)?"<span class='text-danger'>NO</span>":"<span class='text-success'>YES</span>"; ?></td>
                    <td><?php echo ($product->hasDiscount == 0)?"<span class='text-danger'>NOT FOUND</span>":$product->code; ?></td>
                    <td><?php echo number_format($product->item_gross_amount, 2)?></td>
                </tr>
                <?php $i++; }?>
            </tbody>
        </table>
        </div>
    </div>
    <div class="col-12">
            <button id="csvBtn" class="btn btn-secondary mb-3 mt-3">convert to Excel Sheet</button>
             <button class="btn btn-primary" onclick="printDiv('printableArea')">Print</button>
        </div>
    <?php } ?>
<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"crossorigin="anonymous"></script> -->
<script src="<?php echo assets_url('plugins/exportTable/slim.min.js') ?>"></script>
<script src="<?php echo assets_url('plugins/exportTable/tableHTMLExport.js') ?>"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/2.3.5/jspdf.plugin.autotable.min.js"></script> -->
<script> 
$('#csvBtn').on('click',function(){
        $("#filtered_table").tableHTMLExport({
        type:'csv',
        filename:'ProductHistory.csv'
        });
    });
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
        }
</script>
