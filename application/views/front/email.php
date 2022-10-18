<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
* {
  box-sizing: border-box;
}

.box {
  float: left;
  width: 50%;
  padding: 50px;
}

.clearfix::after {
  content: "";
  clear: both;
  display: table;
}
.email_table {
  color: #333;
  font-family: sans-serif;
  font-size: 15px;
  font-weight: 300;
  text-align: center;
  border-collapse: separate;
  border-spacing: 0;
  width: 99%;
  margin: 6px auto;
  box-shadow:none;
}
table {
  color: #333;
  font-family: sans-serif;
  font-size: 15px;
  font-weight: 300;
  text-align: center;
  border-collapse: separate;
  border-spacing: 0;
  width: 99%;
  margin: 50px auto;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,.16);
}

.item_table{text-align:left;}
th {font-weight: bold; padding:10px; border-bottom:2px solid #000;}

tbody td {border-bottom: 1px solid #ddd; padding:10px;}
</style>
</head>
<body>
<?php $total=0;?>
<div class="container" style="background-color:#ccc;padding:1rem">
  <h2>ZABEE</h2>
  <div class="card" >
    <div class="card-body">
        <h3>Order Confirmation</h3>
        <hr>
        <p><b>Thank you for using zabee</b><a href="<?php echo base_url();?>"></a></p>
        <p><strong>Order No:</strong> <?php echo $order_id;?></p>
        <hr>
        <div class="clearfix">
  <div class="box" style="background-color:#ccc">
  <h3><p>Shipping Info:</p></h3>
        <p><b>Name:</b><?php echo $location['shipping']['name'];?></p>
        <p><strong>Address:</strong> <?php  echo $location['shipping']['address_1'].",".$location['shipping']['city'];?></p>
        <p><strong>Contact:</strong><?php  echo $location['shipping']['phone'];?></p>
  </div>
  <div class="box" style="background-color:#ccc">
  <h3><p>Billing Info:</p></h3>
        <p><b>Name:</b><?php echo $location['billing']['name'];?></p>
        <p><strong>Address:</strong> <?php  echo $location['billing']['address_1'].",".$location['billing']['city'];?></p>
        <p><strong>Contact:</strong><?php  echo $location['billing']['phone'];?></p>
  </div>
</div>
        <hr>
        <h3><p>ORDER SUMMARY</p></h3>
        <table class="item_table">
  <thead>
    <tr>
      <th>S.No</th>
      <th>Image</th>
      <th>Item Name</th>
	 
	  <th>Quantity</th>
	  <th>Price</th>
    <th>Tax</th>
    <th>Sub total</th>

    </tr>
  </thead>
  <tbody>
    <?php 
     $i=1;
      foreach ($products as $products) {
       
        $tax=10;
        $tax_type="fixed";
        $image = explode(',',$products['img']);
        if($tax_type=="percent")
        {
          $subtotal=(($products['qty']*$products['price'])+(($products['qty']*$products['price'])*($tax/100)));
        }
        elseif ($tax_type=="fixed")
        {
          $subtotal=(($products['price']+$tax)*$products['qty']);  
        }
 

    ?>
    <tr>
      <td><?php echo $i; ?></td>
      <td><img src="<?php echo base_url('uploads/product/thumbs/').$image[0]?>" style="width: 50px; height:  50px"></td>
      <td><?php echo $products['name'];?></td>
     
	  <td><?php echo $products['qty'];?></td>
	  <td><?php echo "$".$products['price'];?></td>
    <td><?php echo "$".$tax;?> </td>
    <td><?php echo $subtotal;?></td>
    </tr>
    
     <?php
     $total=$total+$subtotal;
     $i++;
   }

  ?>
	<tr>
	  <td colspan="7" style="text-align:right; padding:10px; padding-right: 20px">
	  <strong>Total Amount : </strong> <?php echo "$".$total; ?>
	  </td>
	</tr>
  </tbody>
</table>
<div>
</div>
    </div>
  </div>
</div>
</body>
</html>