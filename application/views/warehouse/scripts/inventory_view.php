<script>
var warehouseData = {};
var tempQty= 0;
var currentBtn = "";
function askUpdate(id,qty,w_id){
		show();
		$('.id').val(id);
		$('#modal-id').val(id);
		$('#maxQty').val(qty);
		$('#current_warehouse').val(w_id);
		$.ajax({  
			type: "POST",
			url: "<?php echo site_url('warehouse/dashboard/editWareHouse');?>",
			data:{'id':id,'w_id':w_id},
			dataType: "json",
			success: function (response) {
				console.log(response);
				if(response.success == 1){
					hide();
					warehouseData = {};
					tempQty = 0;
					$('#modalHeader').html("");
					$("#update-quantity").val(response.result.quantity);
					$("#selected_warehouse").find("option[value="+response.result.warehouse_id+"]").attr('selected', true);
					// $("#warehouse_id").val(response.result.warehouse_id);
					// $("#warehouse_id").text(response.result.warehouse_title);
					$("#myModal").modal('show');
				}
			}
	  });
}
function askReceived(id,inventory_id,btn){
	$("#inventory_id").val(inventory_id);
	$("#warehouse_id").val(id);
	currentBtn = btn;
	$('#confirmation').modal('show');
}
$(document).on('click','#confirm_del',function(){
	var inventory_id = $("#inventory_id").val();
	var id = $("#warehouse_id").val();
	var btn = $("#btn").val();
	$.ajax({
		type: "POST",
		dataType: "JSON",
		data:{"id":id,"inventory_id":inventory_id},
		url: "<?php echo site_url('warehouse/dashboard/is_received'); ?>",
		success: function(response){
			if(response.status == 1){
				$(currentBtn).parent().html('<span class="btn"><i class="fa fa-check" aria-hidden="true"></i></span>');
				$("#confirmation").modal('hide');
			}
			console.log(response);
		}
	});	
});
function show() {
    document.getElementById("myLoading").style.display="block";
}

function hide() {
    document.getElementById("myLoading").style.display="none";
}

var oTable;
oTable = $('.datatables').dataTable({
			language: { searchPlaceholder: "<?php echo $this->lang->line('search_inventory');?>",
			"Search": "<?php echo $this->lang->line('search');?>:",
            "lengthMenu": "<?php echo $this->lang->line('show');?> _MENU_ <?php echo $this->lang->line('entries');?>",
            "zeroRecords": "<?php echo $this->lang->line('not_found');?>",
            "info": "<?php echo $this->lang->line('showing');?> <?php echo $this->lang->line('of');?> _PAGE_ of _PAGES_",
            "infoEmpty": "<?php echo $this->lang->line('no_rec');?>",
			"paginate": {
        		"previous": "<?php echo $this->lang->line('Previous');?>",
				"next" : "<?php echo $this->lang->line('next');?>"
    				},
 },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('warehouse/dashboard/getWarehouseInventory') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"aoColumnDefs": [
				{
					"aTargets": [0],
					"mRender": function ( data, type, full ) {
						console.log(full)
						var time = full[0].replace(/-/g,'/')
						var date = new Date(time+" UTC");
						date =  new Date(date.toString());
						var hours = date.getHours();
						var minutes = date.getMinutes();
						var ampm = hours >= 12 ? 'PM' : 'AM';
						hours = hours % 12;
						hours = hours ? hours : 12; // the hour '0' should be '12'
						minutes = minutes < 10 ? '0'+minutes : minutes;
						var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
						var strTime = months[date.getMonth()]+'-'+date.getDate()+'-'+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
						return strTime; 
					}
				},
				<?php if($this->session->userdata('user_type') != "1") {?>
				{
					"aTargets": [7],
					"mRender": function ( data, type, full ) {
						if(full[7]){
							var time = full[7].replace(/-/g,'/')
							var date = new Date(time+" UTC");
							date =  new Date(date.toString());
							var hours = date.getHours();
							var minutes = date.getMinutes();
							var ampm = hours >= 12 ? 'PM' : 'AM';
							hours = hours % 12;
							hours = hours ? hours : 12; // the hour '0' should be '12'
							minutes = minutes < 10 ? '0'+minutes : minutes;
							var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
							var strTime = months[date.getMonth()]+'-'+date.getDate()+'-'+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
							return strTime; 
						}else{
							return "Not received yet.";
						}
					}
				},	
				<?php }?>
				<?php if($this->session->userdata('user_type') == "1") {?>
				{
					"aTargets": [9],
					"mRender": function ( data, type, full ) {
						if(full[10] == "1"){
							return '<a href="javascript:void(0);" class="btn"><i class="fa fa-check" aria-hidden="true"></i></a>'; 
						}else{
							return full[9]
						}
					}
				},	
				<?php }?>
				
				],
            "aoColumns": [
                <?php if($this->session->userdata('user_type') == "1"){ ?>
					null, null, null, null, null, null, null, null,null,null
				<?php } else { ?>
 					null, null, null, null, null, null,null,null
				<?php } ?>
            ],
		});
</script>
<div id="myModal" class="modal fade" role="dialog">
<div id = "myLoading" style=""><img id = "myImage" src = "<?php echo media_url('assets/backend/images/loader2.gif');?>"></div><br>
<!-- <form action="<?php echo base_url('warehouse/dashboard/updateQuantityandWarehouseforInventory');?>" id="inventoryForm" name="myForm" novalidate method="post" enctype="multipart/form-data"> -->
  <input type="hidden" name="modal_id" id="modal-id">
  <input type="hidden" name="maxQty" id="maxQty">
  <input type="hidden" name="current_warehouse" id="current_warehouse">
  <div class="modal-dialog">
	<div class="modal-content">
      <div class="modal-header" id="modalHeader">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label"><?php echo $this->lang->line('quantity');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" id="update-quantity" name="Quantity" type="text" value="" placeholder="Quantity">	
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label"><?php echo $this->lang->line('warehouse');?></label>
			</div>
			<?php //echo "<pre>";print_r($warehouseList);?>
			<div class="col-sm-6">
				<select name="selected_warehouse" id="selected_warehouse">
				<?php foreach($warehouseList as $wl){ ?>
				<option class="form-control" id="warehouse_id" name="warehouse_title" type="text" value="<?php echo $wl->warehouse_id ?>"><?php echo $wl->warehouse_title ?></option>
				<?php } ?>
				</select>				
			</div>	
		</div>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-success" id="updateInventory"><?php echo $this->lang->line('update');?></button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $this->lang->line('close');?></button>
      </div>
    </div>
	</div>
<!-- </form>	 -->
</div>
<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title"><?php echo $this->lang->line('received_pdt');?></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				<?php echo $this->lang->line('are_you_sure');?>
			</div>				
		  </div>
		  <div class="modal-footer">
			<input type="hidden" id="warehouse_id">
			<input type="hidden" id="inventory_id">
			<a href="#" class="btn" data-dismiss="modal"><?php echo $this->lang->line('no');?></a>
			<input type = "submit" value = "<?php echo $this->lang->line('yes');?>" class="btn btn-primary" id="confirm_del" />
		  </div>
		</div>
	</div>
</div>

<script>
$("#inventoryForm").validate({
  rules: {
		Quantity:{
			required: true,
			digits: true
		}
	},
  messages:{  },
});		
$('#updateInventory').on('click',function(){
	// alert();
	var qty = parseInt($('#update-quantity').val());
	var quantity = parseInt($("#maxQty").val());
	var warehouse = $('#selected_warehouse').val();
	var current_warehouse = $('#current_warehouse').val();
	var id = $('#modal-id').val();
	tempQty = tempQty+qty;
	var	rem = quantity - tempQty;
	if(quantity >= tempQty){
		// $(function(event){
			$('#modalHeader').html(`<span><?php echo $this->lang->line('warehouse_remaining');?> <b class="text-red">${rem}</b> <?php echo $this->lang->line('items');?></span>`);
			$('#update-quantity').val(rem);
		// });
		if(typeof(warehouseData["warhouse_"+warehouse]) !== "undefined"){
			warehouseData["warhouse_"+warehouse].quantity = warehouseData["warhouse_"+warehouse].quantity+qty
		}else{
			warehouseData["warhouse_"+warehouse] = {
				"quantity":qty,
				"warehouse_id":warehouse,
				"inventory_id":id,
				"current_warehouse":current_warehouse
			};
		}
		if(rem == 0){
			//data = {'quantity':qty,'selected_warehouse':warehouse,'modal_id':id };
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url('warehouse/dashboard/updateQuantityandWarehouseforInventory');?>",
				data: warehouseData,
				success: function(response){
					// console.log(response);
					$("#myModal").modal('hide');
					location.reload(); 
				}
			});	
		}
		// return false;
	}else{
		alert("Value Greater than current value");
		tempQty = tempQty-qty;
		return false;
	}
});
</script>