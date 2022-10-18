<div class="card-body">
    <form id="OrderFiltersForm" action="<?php echo base_url('seller/sales/filtered_orders') ?>" method="POST">
      <?php if($this->session->userdata('user_type')== 1){ ?>
        <div class="form-group">
          <label id="date-label-from" class="date-label"><?php echo $this->lang->line('order_placed_from');?> </label>
          <input type="text" id="datepicker_from" name="datepicker_from" value="" class="text-input form-control" autocomplete="off" placeholder = "<?php echo $this->lang->line('date_from');?>"/>
          </div>
          <div class="form-group">
          <label id="date-label-to" class="date-label"><?php echo $this->lang->line('order_placed_to');?></label>
          <input type="text"  id="datepicker_to" name="datepicker_to" value="" class="text-input form-control" autocomplete="off" placeholder = "<?php echo $this->lang->line('date_to');?>"/>
          </div>
        <div class="form-group">
              <label><?php echo $this->lang->line('store_name');?></label>
             <input type="text" class="form-control" name="search_seller_store" id="search_seller_store" placeholder="<?php echo $this->lang->line('seller_st');?>">
              <input type="hidden" id="store_id" name="search_seller"/>
         </div>
      <?php } ?> 
      <?php if($this->session->userdata('user_type') != 1){ ?>
      <div class="form-group">
            <label id="date-label-from" class="date-label"><?php echo $this->lang->line('order_date');?> </label>
             <input type="text" id="datepicker_order" name="datepicker_order" value="" class="text-input form-control" autocomplete="off"/>
            </div>
      <?php } ?>
      <div class="form-group">
            <label><?php echo $this->lang->line('product_name');?></label>
            <input type="text" class="form-control" name="search_product" id="search_product" placeholder="<?php echo $this->lang->line('Product');?>">
            <input type="hidden" id="product_id" name="productId"/>
        </div>
      <div class="form-group">
            <label for="search-status"><?php echo $this->lang->line('order_status');?></label>
            <select class="form-control" name="search_status" id="search-status">
              <!-- <option></option> -->
              <option value="1"><?php echo $this->lang->line('approved');?></option>
              <option value="0"><?php echo $this->lang->line('pending');?></option>
              <option value="2"><?php echo $this->lang->line('declined');?></option>
            </select>
         </div>
      <button class="btn btn-primary float-right" id="reportSearch" type="submit"><?php echo $this->lang->line('search');?></button>
      </form>
  </div> 