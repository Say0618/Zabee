<script>
function askUpdate(id, discount){
	show();
	$('.id').val(id);
	$('#modal-id').val(id);
	$.ajax({  
		type: "GET",
		url: "<?php echo site_url('seller/product/updateInventory')?>/"+id+"/"+discount,
		dataType: "json",
		success: function (response) {
			console.log(response);
			if(response.success == 1){
				hide();
				var strHtml1 ='<option value="0">Select Discount</option>';
				$("#update-quantity").val(response.result.quantity - response.result.sell_quantity);
				$("#update-price").val(response.result.price);
				$("#pvId").val(response.result.pvId);
				$("#sell_qty").val(response.result.sell_quantity);
				$(response.discountData).each(function(index,value){
					today = "<?php echo date("Y-m-d"); ?>";
					console.log(today);
					if(today <= value.valid_to){
						var type = (value.type == 'percent')?'%':'/';
						if(value.id == discount){
							strHtml1 += '<option value="'+value.id+'" selected>'+value.value+type+'-'+value.title+'-'+formatAMPM(value.valid_from)+' till '+formatAMPM(value.valid_to)+'</option>';
						}else{
							strHtml1 += '<option value="'+value.id+'">'+value.value+type+'-'+value.title+'-'+formatAMPM(value.valid_from)+' till '+formatAMPM(value.valid_to)+'</option>';
						}
					}
				});
			}
			$('.variant_discount').html(strHtml1);
			$("#myModal").modal('show');
		}
	});
}
function askDelete(id){
	$("#delete_id").val(id);
	$("#delete-modal").modal('show');
}
$(document).on("click","#delete_btn",function(){
	show();
	var url = "<?php echo site_url('seller/product/deleteInventory')?>"
	<?php if($delete){?>
		url = "<?php echo site_url('seller/product/recreateInventory')?>"
	<?php }?>
	var id = $("#delete_id").val();
	$.ajax({  
		type: "POST",
		url: url,
		dataType: "JSON",
		data: {"id":id},
		success: function (response) {
			hide();
			if(response.status == 1){
				location.reload();
			}
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
			language: { searchPlaceholder: "Search Inventory" },
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 10,
            'bProcessing': true, 'bServerSide': true,
			<?php $prd_id = (isset($prd_id) && $prd_id != "")?'?prd_id='.$prd_id:"";
				  $seller = (isset($seller) && $seller != "")?'&seller='.$seller:"";
				  $check = (isset($prd_id) && $prd_id == "")?"?":"&";
				  $delete = (isset($delete) && $delete != "")?$check.'delete='.$delete:""; ?>
            'sAjaxSource': '<?php echo site_url('seller/product/get_inventory_details2'.$prd_id.$seller.$delete) ?>',
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
						return formatAMPM(full[0], true);
					}
				},
				{
					"aTargets": [1],
					"mRender": function ( data, type, full ) {
						if(full[1]){
							return formatAMPM(full[1], true);
						}else{
							return formatAMPM(full[0], true);
						}
					}
				},
				{
				 <?php if($this->session->userdata('user_type') == "1"){ ?>
					"aTargets": [4],
				<?php } else { ?>
					"aTargets": [3],
				<?php } ?>
				"mRender": function ( data, type, full ) {
					return data;
				},
				<?php if($this->session->userdata('user_type') == "1"){ ?>
					"aTargets": [9],
				<?php } else { ?>
					"aTargets": [8],
				<?php } ?>
				"mRender": function ( data, type, full, id ) {
					// console.log(data);
					var discountDate = data.split(":");
					
					if(discountDate[1]){
						discountDate[1] = formatAMPM(discountDate[1], false);
						data = discountDate.join(': ');
					}
					return data;
				}
				}
				],
            "aoColumns": [
                <?php if($this->session->userdata('user_type') == "1"){ ?>
					null, null, null, null, null, null, null, null,null,null,null
				<?php } else { ?>
 					null, null, null, null, null, null, null,null, null,null
				<?php } ?>
            ],
		});
</script>
<div id="myModal" class="modal fade" role="dialog">
<div id = "myLoading" style=""><img id = "myImage" src = "<?php echo assets_url('backend/images/loader2.gif');?>"></div><br>
<form action="<?php echo base_url('seller/product/updateQuantityandPriceforInventory');?>" id="inventoryForm" name="myForm" novalidate method="post" enctype="multipart/form-data">
  <input type="hidden" name="modal_id" id="modal-id">
  <input type="hidden" name="pvId" id="pvId">
  <input type="hidden" name="sell_qty" id="sell_qty">
  <div class="modal-dialog">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label">Quantity</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" id="update-quantity" name="Quantity" type="text" value="" placeholder="Quantity">	
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label">Price</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" id="update-price" name="Price" type="text" value="" placeholder="Price">	
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label">Discount</label>
			</div>
			<div class="col-sm-6">
			<select name="discount" class="form-control variant_discount" id="update-discount"></select>

		</div>	
		</div>
      </div>
      <div class="modal-footer">
		<button type="submit" class="btn btn-success updateInventory">Update</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
	</div>
</form>	
</div>
<script>
$("#inventoryForm").validate({
  rules: {
		Quantity:{
			required: true,
			digits: true
		},
		Price:{
			required: true,
			numbers: true
		}
	},
  messages:{},
});		

function formatAMPM(date, time = false) {
	date = date.replace(/-/g,'/')
	date = new Date(date+" UTC");
	date =  new Date(date.toString());
	var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	var strTime = "";
	if(time == true){
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0'+minutes : minutes;
		strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
	}else{
		strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear();
	}
	
	return strTime;
}
</script>