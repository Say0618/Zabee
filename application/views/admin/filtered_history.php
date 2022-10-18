<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php //echo"<pre>";print_r($products);die();?>
    <?php if(empty($products['data'])){ ?>
    <div><h3 class="p-5 text-center">No Product Found</h3></div>
    <?php } else{ ?>
    <div  id="printableArea" class="p-3">
        <h3>Products Report</h3>
        <?php if($this->session->userdata('user_type') != 1){ ?>
        <div class="row">
            <div class="col-3 pl-5">
                <div class="form-group">
                        <label for="" class="text-dark">Store Name:</label>
                        <span class="text-secondary"><?php echo $products['data'][0]->name1?></span>
                    </div>
                </div> 
            <div class="col-5">
                <div class="form-group">
                        <label for="" class="text-dark">Store Address:</label>
                        <span class="text-secondary"><?php echo $products['data'][0]->store_address ?></span>
                    </div>
                </div>
            </div>
            <?php } ?>
        <div class="row mb-3 mt-3">
            <?php if($this->session->userdata('user_type')== 1){ ?>
            <?php if($post_data['search_seller'] != ""){ ?>
                <div class="col-3 pl-5">
                    <div class="form-group">
                            <label for="Student" class="text-dark">Store Name:</label>
                            <span class="text-secondary"><?php echo str_replace('-'," ",$post_data['search_seller']) ?></span>
                        </div>
                    </div>
                <?php } } ?>
            <?php if($post_data['search_status'] != ""){ ?>
            <div class="col-3 <?php if($this->session->userdata('user_type') != 1){ ?>pl-5 <?php }?>">
                    <div class="form-group">
                            <label for="" class="text-dark">Product Status:</label>
                            <span class="text-secondary"><?php if($post_data['search_status'] == "1"){echo "approved";}else{echo"pending";} ?></span>
                        </div>
                    </div>
                <?php } ?>
            <?php if($post_data['datepicker_from'] != ""){ ?>
                <div class="col-3">
                    <div class="form-group">
                        <label for="Student">Product Created From:</label>
                            <span class="text-secondary"><?php echo $post_data['datepicker_from'] ?></span>
                        </div>
                    </div>
                <?php } ?>
            <?php if($post_data['datepicker_to'] != ""){ ?>
                <div class="col-3">
                    <div class="form-group">
                        <label for="Student">Product Created To:</label>
                            <span class="text-secondary"><?php echo $post_data['datepicker_to'] ?></span>
                        </div>
                    </div>
                <?php } ?>
        </div>
        <div class="table-responsive">
            <table id="filtered_table" class="table table-bordered">
                <thead>
                    <th>S.No</th>
                    <th>Created Date</th>
                    <?php if($this->session->userdata('user_type') == 1){?>
                    <th>Created By</th>
                    <?php } ?>
                    <th >Title</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Condition</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Discount</th>
                    <th>Attributes</th>
                </thead>
                <tbody>
                    <?php $i=1; 
                    foreach($products['data'] as $product){
                        //if(!empty($product)){?>
                    <tr>
                        <td style="text-align:center"><?php echo $i;?></td>
                        <td><?php if($product->created_date != ""){echo date('Y-m-d', strtotime($product->created_date));}else{echo date('Y-m-d', strtotime($product->pdtDate));}?></td>
                        <?php if($this->session->userdata('user_type') == 1){?>
                            <td><?php echo $product->name1?></td>
                        <?php } ?>
                        <td><?php echo wordwrap($product->product_name,20,"<br />\n")?></td>
                        <td><?php echo $product->category_name?></td>
                        <td><?php echo $product->brand_name?></td>
                        <td><?php echo $product->condition_name?></td>
                        <td><?php echo $product->price?></td>
                        <td><?php echo $product->quantity?></td>
                        <?php if($product->discount_type == "percent"){?>
                        <td><?php echo $product->discount?>%</td>
                        <?php } else{ ?>
                        <td><?php echo $product->discount?></td>
                        <?php } ?>
                        <td><?php echo $product->variant?></td>
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
<script src="<?php echo media_url('assets/plugins/exportTable/slim.min.js') ?>"></script>
<script src="<?php echo media_url('assets/plugins/exportTable/tableHTMLExport.js') ?>"></script>
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
