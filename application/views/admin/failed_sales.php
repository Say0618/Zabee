<div class="card-body">
 		<table class="product-table table-responsive w-100 d-block d-md-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th  align="center">S.No</th>
                        <th align="center">Sold By</th>
                        <th align="center">Order ID</th>
						<th align="center"><center>Order Date</center></th>
						<th align="center">Ship to</th>
						<th align="center"><center>Amount</center></th>
						<th align="center">Details</th>
						<!--<th align="center">Action</th>-->
						<th align="center">Invoice</th>
						<th align="center">All Orders</th>
                        <!--<th align="center">Action</th>-->
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
<div class="modal fade" id="refund-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color: orange;font-variant: small-caps;font-weight: bold;">Alert</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="order_id" value="">
                       <input type="hidden" id="td_id" value="">
                        <strong style="color:green;font-variant: small-caps;" id="change-message">Are you sure you want to refund?</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary refundorder" id="refund-order" data-dismiss="modal"><i class="fa fa-user"></i>Yes</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>     
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div class="modal fade" id="decision-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color: orange;font-variant: small-caps;font-weight: bold;">Alert</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="order_id" value="">
                       <input type="hidden" id="td_id" value="">
                        <strong style="color:green;font-variant: small-caps;" id="change-message">This order is requested for cancellation, are you sure you want to decline the request and approve the order?</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary refundorder" id="decision-approve" data-dismiss="modal">Proceed</button>
              <button type="button" class="btn btn-danger" id="decision-decline" data-dismiss="modal">No</button>
            </div>     
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
