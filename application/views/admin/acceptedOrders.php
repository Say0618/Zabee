<?php if($this->session->flashdata('error')){?>
  <div class="alert alert-danger mb-0" role="alert">
  <strong><?php echo $this->session->flashdata('error');?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  </div>
<?php }?>
<div class="card-body">
 		<table class="product-table table-responsive w-100 d-block d-md-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th  align="center">S.No</th>
						<th align="center">Order ID</th>
						<th align="center"><center>Order Date</center></th>
						<th align="center">Ship to</th>
						<th align="center"><center>Amount</center></th>
						<th align="center">Status</th>
						<!--<th align="center">Action</th>-->
						<th align="center">Details</th>
						<th align="center">Invoice</th>
				</tr>
			</thead> 
			<tbody></tbody>
		</table>   
	
</div>
<div class="modal fade" id="myModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <input type="hidden" id="buyer_id" value="">
      <input type="hidden" id="sp_id" value="">
      <input type="hidden" id="pv_id" value="">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Order Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-responsive w-100 d-block d-md-table table-bordered">
              <tr>
                <th class="text-center" colspan="4" >Bill to</th>
              </tr>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>City</th>
                <th>Contact</th>
              </tr>
              <tr>
                <td><span id="billing_name"></span></td>
                <td><span id="billing_address"></span></td>
                <td> <span id="billing_city"></span></td>
                <td><span id="billing_phone"></span></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <table class="table table-responsive w-100 d-block d-md-table table-bordered">
              <tr>
                <th class="text-center" colspan="4">Ship to</th>
              </tr>
              <tr>
                <th>Name</th>
                <th>Address</th>
                <th>City</th>
                <th>Contact</th>
              </tr>
              <tr>
                <td><span id="shipping_name"></span></td>
                <td><span id="shipping_address"></span></td>
                <td><span id="shipping_city"></span></td>
                <td><span id="shipping_phone"></span></td> 
              </tr>
            </table>
          </div>
        </div>
        <div id="products"></div>
        <div class="col-sm-12 text-right">
          <!--  <b>Tax:&nbsp</b><span id="tax"></span><br> -->
          <b>Total:&nbsp</b><span id="total"></span><br>
        </div>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-primary contactuser contact-buyer" id="" ><i class="fa fa-user"></i>Contact buyer</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
    <div class="modal fade" id="message-panel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span>Contact Buyer</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12">
                    <div class="panel-body">
                        <div class="form-group">
                         <!-- <label class="control-label" for="inputBody">Message</label>-->
                          <textarea class="form-control" id="message" rows="8" style="border-radius:5px;"></textarea>
                          <span></span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-right">
                        	<a href="javascript:" class="btn btn-primary" id="sendMessage">Send</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="message-notification" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color: orange;font-variant: small-caps;font-weight: bold;">Notification!</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                        <strong style="color:green;font-variant: small-caps;" id="change-message"><?php echo $this->lang->line("sent_msg") ?></strong>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>