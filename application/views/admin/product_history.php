<div class="card-body">
    <form id="historyForm" action="<?php echo base_url('seller/product/get_product_history_filters') ?>" method="POST">
      <div class="form-group">
        <label id="date-label-from" class="date-label"><?php echo $this->lang->line('product_created_from');?></label>
        <input type="text" id="datepicker_from" name="datepicker_from" value="" class="text-input form-control" autocomplete="off"/>
      </div>
      <div class="form-group">
        <label id="date-label-to" class="date-label"><?php echo $this->lang->line('product_created_to');?></label>
        <input type="text"  id="datepicker_to" name="datepicker_to" value="" class="text-input form-control" autocomplete="off"/>
      </div>
      <div class="form-group">
        <label id="date-label-to" class="date-label">Select Brand</label>
        <select class="form-control" name="search_brand" id="search-brand">
          <option value="">-Select brand-</option>
          <?php foreach($brands as $brand){?>
          <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name'];?></option>
          <?php } ?>
        </select>
      </div>
      <div class="form-group">
        <label id="date-label-to" class="date-label">Select Category</label>
        <select class="form-control" name="search_category" id="search-category">
          <option value="">-Select Category-</option>
          <?php foreach($categories as $category){?>
          <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name'];?></option>
          <?php } ?>
        </select>
      </div>
      <?php if($this->session->userdata('user_type')== 1){ ?>
        <div class="form-group">
      <label><?php echo $this->lang->line('store_name');?></label>
      <input type="text" class="form-control" name="search_seller_store" id="search_seller_store" placeholder="<?php echo $this->lang->line('seller_st');?>">
      <input type="hidden" id="store_id" name="search_seller"/>
      </div>
      <?php } ?>
      <div class="form-group">
        <label for="search-status"><?php echo $this->lang->line('product_status');?></label>
        <select class="form-control" name="search_status" id="search-status">
          <option value="">-Select Status-</option>
          <option value="1"><?php echo $this->lang->line('approved');?></option>
          <option value="0"><?php echo $this->lang->line('pending');?></option>
        </select>
      </div>
      <button class="btn btn-primary float-right" id="reportSearch" type="submit"><?php echo $this->lang->line('search');?></button>
      </form>
</div> 