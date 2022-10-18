<div class="page-content d-flex align-items-stretch"> 
    <nav class="side-navbar">
      <a href="<?php echo base_url("warehouse")?>">
          <div class="sidebar-header d-flex align-items-center">
            <div class="avatar"><img src="<?php echo base_url('home/get_image/?path='.urlencode(store_logo_path()).'&file='.urlencode($this->session->userdata('store_pic'))); ?>" alt="..." class="img-fluid rounded-circle avatar" /></div>
            <div class="title">
              <h4 class="mb-0"><?php echo (isset($this->session->zabeeWarehouseData['firstname']))?$this->session->zabeeWarehouseData['firstname']." ".$this->session->zabeeWarehouseData['lastname']:"Profile name";?></h4>
            </div>
          </div>
      </a>
    <ul class="list-unstyled">
        <li><a href="#viewProducts" aria-expanded="false" data-toggle="collapse"><i class="fas fa-briefcase" aria-hidden="true"></i></i><strong><?php echo $this->lang->line('product');?></strong></a>
          <ul id="viewProducts" class="collapse list-unstyled ">
            <li><a href="<?php echo base_url()."warehouse/product_list"?>"><strong><?php echo $this->lang->line('product_list');?></strong></a></li>
            <li><a href="<?php echo base_url()."warehouse/product/product_history"?>"><strong><?php echo $this->lang->line('product_report');?></strong></a></li> 
          </ul>
        </li>
        <li><a href="#viewInventory" aria-expanded="false" data-toggle="collapse"> <i class="fas fa-briefcase" aria-hidden="true"></i></i><strong><?php echo $this->lang->line('inventory');?></strong></a>
          <ul id="viewInventory" class="collapse list-unstyled">
            <li><a href="<?php echo base_url()."warehouse/inventory_view"?>"><strong><?php echo $this->lang->line('inventory_list');?></strong></a></li>
          </ul>
        </li>
        <li><a href="#viewSales" aria-expanded="false" data-toggle="collapse"><i class="far fa-money-bill-alt"></i><strong><?php echo $this->lang->line('sales');?></strong></a>
          <ul id="viewSales" class="collapse list-unstyled ">
            <li><a href="<?php echo base_url()."warehouse/sales"?>"><strong><?php echo $this->lang->line('pending_orders');?></strong></a></li>
            <li><a href="<?php echo base_url()."warehouse/sales/acceptedOrders_view"?>"><strong><?php echo $this->lang->line('accepted_orders');?></strong></a></li>
            <li><a href="<?php echo base_url()."warehouse/sales/declinedOrders_view"?>"><strong><?php echo $this->lang->line('declined_orders');?></strong></a></li>
            <li><a href="<?php echo base_url()."warehouse/sales/order_history"?>"><strong><?php echo $this->lang->line('orders_report');?></strong></a></li>
          </ul>
        </li>
    </ul>
</nav>       