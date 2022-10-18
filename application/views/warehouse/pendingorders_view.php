<style>.table-responsive {
    display:list-item;
}</style>
<div class="card-body">
<?php /*echo "<pre>";print_r($_SESSION);*/ if(isset($_GET['status']) && $_GET['status'] == "error" && $this->session->flashdata("error")){ ?>
    	<div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("error");?>
        </div>
	<?php } else if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ //die("here 2");?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>
 	<div class="table-responsive" style="display:block !important;">
		<table cellpadding="0" cellspacing="0" border="0" class="product-table sorted_table table-sm table table-striped table-bordered datatables">
			<thead>
				<tr>
					<th  align="center"><?php echo $this->lang->line('s_no');?></th>
						<th align="center"><?php echo $this->lang->line('order_id');?></th>
						<th align="center"><center><?php echo $this->lang->line('order_date');?></center></th>
						<th align="center"><?php echo $this->lang->line('ship_to');?></th>
						<th align="center"><center><?php echo $this->lang->line('amount');?></center></th>
						<th align="center"><?php echo $this->lang->line('details');?></th>
						<th align="center"><?php echo $this->lang->line('invoice');?></th>
						<th align="center"><?php echo $this->lang->line('warehouses');?></th>
						<th align="center"><?php echo $this->lang->line('order_info');?></th>
				</tr>
			</thead> 
			<tbody></tbody>
		</table>   
	</div>
</div>
<div class="modal fade" id="refund-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span><?php echo $this->lang->line('refund');?>!</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="order_id" value="">
                       <input type="hidden" id="tdId" value="">
                        <strong style="font-variant: small-caps;" id="change-message"><?php echo $this->lang->line('refund_pd');?></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary refundorder" id="refund-order" data-dismiss="modal"><?php echo $this->lang->line('accept');?></button>
              <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
            </div>     
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="warehouseModal" role="dialog">
  <input type="hidden" id="warehouse_selected" value="">
  <input type="hidden" id="warehouse_values" value="">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo $this->lang->line('select_warehouses');?></h4>
      </div>
      <div class="modal-body">
        <div class="row WarehousesHere"></div><br />
        <div class="row qty-heading" style="display:none"><div class="col-sm-12"><?php echo $this->lang->line('select_quantities');?>:</div></div>
        <div class="row WarehousesQtyHere"></div>
        <div class="row error" style="display:none">
          <div class="col-sm-12">
            <h6 style="color:red">
              <?php echo $this->lang->line('more_quantities');?>
            </h6>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
        <button type="button" class="btn btn-success qty-confirm"><?php echo $this->lang->line('confirm');?></button>
      </div>
    </div>
  </div>
</div>

 <div class="modal fade" id="myModal">
      <input type="hidden" id="buyer_id" value="">
      <input type="hidden" id="sp_id" value="">
      <input type="hidden" id="pv_id" value="">
      <div class="modal-dialog"> 
        <div class="modal-content"> <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title"> <?php echo $this->lang->line('order_details');?></h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
        <!-- Modal body -->
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12">
              <table class="table new-table ">
                <tr>
                  <th class="text-center" colspan="4" ><?php echo $this->lang->line('bill_to');?></th>
                </tr>
                <tr>
                  <th><?php echo $this->lang->line('name');?></th>
                  <th><?php echo $this->lang->line('address');?></th>
                  <th><?php echo $this->lang->line('city');?></th>
                  <th><?php echo $this->lang->line('contact');?></th>
                </tr>
                <tr>
                  <td><span id="billing_name"></span></td>
                  <td><span id="billing_address"></span></td>
                  <td> <span id="billing_city"></span></td>
                  <td><span id="billing_phone"></span></td>
                </tr>
              </table>

               <table class="table new-table">
                  <tr>
                    <th class="text-center" colspan="4"><?php echo $this->lang->line('ship_to');?></th>
                  </tr>
                  <tr>
                    <th><?php echo $this->lang->line('name');?></th>
                    <th><?php echo $this->lang->line('address');?></th>
                    <th><?php echo $this->lang->line('city');?></th>
                    <th><?php echo $this->lang->line('contact');?></th>
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
            <b><?php echo $this->lang->line('total');?>:&nbsp</b><span id="total"></span><br>
          </div>

        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
        <button type="button" class="btn btn-primary contactuser contact-buyer" id="" ><i class="fa fa-user"></i><?php echo $this->lang->line('contact_buyer');?></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
        </div>       
      </div>
    </div>
    <div class="modal fade" id="message-panel" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span><?php echo $this->lang->line('contact_buyer');?></span>
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
                        	<a href="javascript:" class="btn btn-primary" id="sendMessage"><?php echo $this->lang->line('send');?></a> 
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
                <span style="color: orange;font-variant: small-caps;font-weight: bold;"><?php echo $this->lang->line('notification');?>!</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                        <strong style="color:green;font-variant: small-caps;" id="change-message"><?php echo $this->lang->line('send_success');?>!</strong>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<input type="hidden" id="s_id">
<input type="hidden" id="td_id">
<input type="hidden" id="t_id">


