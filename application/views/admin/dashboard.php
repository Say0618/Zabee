<style>
    .setting-icon{
        position: absolute;
        top: 12px;
        z-index: 7;
        right: 50px;
        font-size: 1.5rem;
        cursor: pointer;
    }
    .dashboard-box{
        border-radius: 10px;
        padding: 1rem;
        color:#fff;
        position:relative;
    }
    .dashboard-box span{
        font-size:2rem;
    }
    .box-1{
        background:#ff9f1c;
    }
    .box-2{
        background:#4361ee;
    }
    .box-3{
        background:#ff6b6b;
    }
    .box-4{
        background:#de6bff;
    }

    .icon{
        border-radius: 50%;
        position: absolute;
        top: 11px;
        right: 16px;
    }
    .icon-1{
        padding: 1rem 1.5rem;
        border: 1px solid #ffc270;
        background: #ffc270;
    }
    .icon-2{
        border: 1px solid #748cff;
        background: #748cff;
        padding: 1rem 1.7rem;
    }
    .icon-3{
        padding: 1rem 1.3rem;
        border: 1px solid #ff9797;
        background: #ff9797;
    }
    .icon-4{
        padding: 1rem 1.3rem;
        border: 1px solid #e397ff;
        background: #e397ff;
    }
    .table thead th{
        border:none;
    }
    .table td, .table th {
        border:none;
    }
    .border_radius{
        border-radius: 1rem;
        box-shadow: 1px 1px 5px 0px #686a76;
        height:98.2%;
    }
    .chat-div{
        display: inline-block;
        margin: auto;
        position: absolute;
        padding: 0px 8px;
        width:90%;
        color:#333;
    }
</style>
<!--<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="dashboard-box box-1">
            <div><small>Weekly Sale</small></div>
            <div><span>$775</span></div>
            <span class="icon icon-1"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="dashboard-box box-2">
            <div><small>Total Order</small></div>
            <div><span>$58.5k</span></div>
            <span class="icon icon-2"><i class="fa fa-clipboard-check"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="dashboard-box box-3">
            <div><small>Total Store</small></div>
            <div><span>5,000</span></div>
            <span class="icon icon-3"><i class="fa fa-store"></i></span>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="dashboard-box box-4">
            <div><small>Total Active Products</small></div>
            <div><span>10,000</span></div>
            <span class="icon icon-4"><i class="fa fa-box-open"></i></span>
        </div>
    </div>
</div>-->
<div class="row my-4" id="dashboard">
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Settings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-check">
            <div class="row">
                <div class="col-6">
                    <input class="form-check-input" type="checkbox" id="order" data-function="orderList" onchange="settings(this)">
                    <label class="form-check-label" for="order">
                        Orders
                    </label>
                </div>
                <div class="col-6">
                    <input class="form-check-input" type="checkbox" id="inventory" data-function="inventoryList" onchange="settings(this)">
                    <label class="form-check-label" for="inventory">
                        Inventory
                    </label>
                </div>
                <div class="col-6">
                    <input class="form-check-input" type="checkbox" id="product" data-function="productList" onchange="settings(this)">
                    <label class="form-check-label" for="product">
                        Product
                    </label>
                </div>
                <div class="col-6">
                    <input class="form-check-input" type="checkbox" id="store" data-function="storeList" onchange="settings(this)">
                    <label class="form-check-label" for="store">
                        Store
                    </label>
                </div>
                <div class="col-6">
                    <input class="form-check-input" type="checkbox" id="request" data-function="requestList" onchange="settings(this)">
                    <label class="form-check-label" for="request">
                        Request
                    </label>
                </div>
                <div class="col-6">
                    <input class="form-check-input" type="checkbox" id="message" data-function="messageList" onchange="settings(this)">
                    <label class="form-check-label" for="message">
                        Message
                    </label>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onClick="saveSettings()">Save changes</button>
      </div>
    </div>
  </div>
</div>
