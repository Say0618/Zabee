<?php
	$editurl = base_url()."admin/purchaseinvoice/editedpurchaseinvoice";		
	$createurl = base_url()."admin/purchaseinvoice/createpurchaseinvoice";		
?>
<script>
function validate(oDiv,event)
{
	var check = false;//,[type=file]
	$(oDiv).find("[type=text],[type=radio]").each
	(
		function()
		{
			if(check == false && ($(this).val() == "" || $(this).val().trim() == ""))
			{
				check = true;
				alert("All fields are compulsory");
				event.preventDefault();
				$(this).focus();				
			}			
		}
	);
	$(".invoice_count").val(g_count);
	
}


function addField(isEdit)
{
	g_count++;
	$(".invoicemodal-body fieldset").append(getinvoiceHtml(g_count,isEdit));	
	$("#datepick_"+g_count).datepicker({ dateFormat: "dd-mm-yy" });
	addInvoiceItems(g_count,isEdit);			
	if(isEdit == undefined)
	{
		$(".invoice-addData:last").find("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();	
		setTimeout(function(){$(".cleditor").cleditor();},300);
	}	
}

var g_count = 0;
function getinvoiceHtml(count,isedit)
{
var strHtml = '<span class = "invoice-addData">';
	
	
	
	
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Select Purchaser Name</label>'
			+'<div class="controls">';			
	<?php 
		if($oObj->users)
		{
	?>	
	strHtml += '<select name = "purchaser_'+count+'" class = "purchaser">';
	<?php
			foreach($oObj->users as $parent)
			{
				echo "strHtml += \"<option value = '".$parent['admin_id']."'>".$parent['admin_name']."</option>\";";
			}
	?>
	strHtml	+='</select>';
	<?php
		}
		else
		{
			echo  'strHtml += "<span style = \'color:red;\'>No Users found.</span>Please <a href = \''.base_url().'admin/vendors\'>Add Vendors</a>";';
		}
	?>
	strHtml +='</div>'
			+'</div>';
	
	
	
	strHtml += '<div class="control-group">'
			+'		<label class="control-label" for="date01">Purchase Date</label>'
			+'	  	<div class="controls">'
			+'			<input type="text" name = "purchase_date_'+count+'" class="input-xlarge purchase_date datepicker" id="datepick_'+count+'" value="<?php echo date("d/m/Y");?>">'
			+'	  	</div>'
			+'	</div>';

			//transportation_cost
			
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Transportation Cost</label>'
			+'<div class="controls">'
   		    +' <input class="input-large focused transportation_cost" id="focusedInput" name="transportation_cost_'+count+'" type="text" value="" placeholder="Enter Transportation Cost">'
			+'</div>'
			+'</div>';

	strHtml += '<div class = "invoice-items">'			
			+'<div style = "text-align:center;font-size : 20px;padding:15px;"><strong>Add Invoice Items</strong></div>'
			+'<div class = "invoice-items_'+count+'"></div>'			
			+'<center><span class = "additembtns">'
			+'		<span class = "btn btn-success addmorebtn" onclick = "addInvoiceItems(\''+count+'\');">Add Item</span>'
			+'		<span class = "btn btn-danger removemorebtn" onclick = "removeInvoiceItems(this,\''+count+'\');">Remove Item</span>'
			+'	</span></center>'
			+'</div>';
		
	
	
	
	
	if(isedit != undefined)
	{
		strHtml	+= '<input type = "hidden" value = "" name = "invoice_id_'+count+'" class = "invoice_id"/>';
	}			
	strHtml	+= '<input type = "hidden" value = "1" name = "invoice_item_count_'+count+'" class = "invoice_item_count'+count+'"/>';
	strHtml += "<center style = 'width : 100%'>"
				+"<div style = 'width: 90%;border-top: 1px solid #B3B3B3;padding-bottom: 20px;padding-top: 20px;'></div></center>";
	strHtml += "</span>";
	return strHtml;
}

function removeInvoiceItems(oDiv,parent_count)
{
	var oParent =  $(oDiv).closest(".invoice-items");
	if(oParent.find(".invoice_items").length > 1)
	{
		oParent.find(".invoice_items").last().remove();
	}
	var count = oParent.find(".invoice_item_count"+parent_count).val();
	oParent.find(".invoice_item_count"+parent_count).val(--count);
}


function addInvoiceItems(parent_count,isedit)
{
	var count = $(".invoice_item_count"+parent_count).val();	
	var strHtml = "";
	if(isedit)
	{
		strHtml += "<input type = 'hidden' name = 'purchase_invoice_item_id_"+parent_count+"[]' class = 'purchase_invoice_item_id' />";
	}	
	strHtml += "<center><div class = 'invoice_items invoice_items_"+count+"'>";	
	if(isedit != undefined)
	{
		strHtml	+= '<input type = "hidden" value = "" name = "invoice_item_id_'+parent_count+'[]" class = "invoice_item_id"/>';
	}
	
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Select Product</label>'
			+'<div class="controls">';			
	<?php 
		if($oObj->products)
		{
	?>
	strHtml += '<select name = "product_id_'+parent_count+'[]" class = "product_id">';
	<?php
			foreach($oObj->products as $parent)
			{
				$product_image = (isset($parent['product_image']))?$parent['product_image']:"";
				$vendor_id = (isset($parent['vendor_ids']))?$parent['vendor_ids']:"0";
				echo "strHtml += \"<option value = '".$parent['product_id']."' pp = '".$parent['price']."' pv = '".$vendor_id."' >".$parent['product_name']." <img src = '".$product_image."' /></option>\";";
			}
	?>
	strHtml	+='</select>';
	<?php
		}
		else
		{
			echo  'strHtml += "<span style = \'color:red;\'>No products found.</span>Please <a href = \''.base_url().'admin/product\'>Add products</a>";';
		}
	?>
	strHtml +='</div>'
			+'</div>';
	
	
	
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Quantity</label>'
			+'<div class="controls">'
   		    +' <input class="input-large focused quantity" id="focusedInput" name="quantity_'+parent_count+'[]" type="text" value="" placeholder="Enter Quantity">'
			+'</div>'
			+'</div>';
			
			
			
			
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Purchase Rate</label>'
			+'<div class="controls">'
   		    +' <input class="input-large focused purchase_rate" name="purchase_rate_'+parent_count+'[]" type="text" value="" placeholder="Enter Purchase Rate">'
			+'</div>'
			+'</div>';
	
	
	
	
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Total Purchase Cost</label>'
			+'<div class="controls">'
   		    +' <input class="input-large focused total_purchase_rate" id="focusedInput" name="total_purchase_rate_'+parent_count+'[]" type="text" value="" placeholder="Enter Total Purchase Cost">'
			+'</div>'
			+'</div>';
			
			
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Date Of Purchase</label>'
			+'<div class="controls">'
   		    +' <input class="input-large focused item_purchase_date" id="item_purchase_date'+parent_count+'_'+count+'"  name="item_purchase_date_'+parent_count+'[]" type="text" value="" placeholder="Enter Transportation Cost">'
			+'</div>'
			+'</div>';
	
	
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">MRP/Selling Price</label>'
			+'<div class="controls">'
   		    +' <input class="input-large focused mrp_item" id="focusedInput" name="mrp_item_'+parent_count+'[]" type="text" value="" placeholder="Enter MRP or Selling Price">'
			+'</div>'
			+'</div>';
	
	
	
	
	
	
	//Select Vendor		
	strHtml += '<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Select Vendor</label>'
			+'<div class="controls">';			
	<?php 
		if($oObj->vendors)
		{
	?>
	strHtml += '<select name = "vendor_id_'+parent_count+'[]" class = "vendor_id">';
	<?php
			foreach($oObj->vendors as $parent)
			{
				echo "strHtml += \"<option value = '".$parent['vendor_id']."'>".$parent['vendor_name']."</option>\";";
			}
	?>
	strHtml	+='</select>';
	<?php
		}
		else
		{
			echo  'strHtml += "<span style = \'color:red;\'>No Vendors found.</span>Please <a href = \''.base_url().'admin/vendors\'>Add Vendors</a>";';
		}
	?>
	strHtml +='</div>'
			+'</div>';
			
	strHtml += "</div></center></div>";		
	
	
	$(".invoice-items_"+parent_count).append(strHtml);
	$('#item_purchase_date'+parent_count+'_'+count).datepicker({ dateFormat: "dd-mm-yy" });
	$('#item_purchase_date'+parent_count+'_'+count).val($('#datepick_'+parent_count).val());
	$(".invoice_items_"+count).find(".product_id").trigger("change");
	$(".invoice_item_count"+parent_count).val(++count);
			
	
}


$(document).ready
(
	function()
	{
		$.cleditor.defaultOptions.width = 300;
		$.cleditor.defaultOptions.height = 300;
		$(".quantity,.purchase_rate").live
		(
			"keyup paste cut",
			function()
			{
				var oParent = $(this).closest(".invoice_items");
				var quantity = parseFloat(oParent.find(".quantity").val());
				var purchase_rate = parseFloat(oParent.find(".purchase_rate").val());
				if(isNaN(quantity))quantity = 0;
				if(isNaN(purchase_rate))purchase_rate = 0;
				oParent.find(".total_purchase_rate").val(quantity*purchase_rate);
			}
		);
		
		$(".product_id").live
		(
			"change",
			function()
			{
				var oParent = $(this).closest(".invoice_items");
				var oEle = $(this);
				$(this).find("option").each
				(
					function()
					{
						if(oEle.val() == $(this).attr("value"))
						{
							oParent.find(".mrp_item").val($(this).attr("pp"));
							oParent.find(".vendor_id").val($(this).attr("pv").split(",")[0]);
						}
					}
				);
			}
		);
		
		
		$(".close,.closebtn").bind("click",function(){$("#invoicemodal").hide("slow");$(".addinvoice").show("slow");})
		$(".addmorebtn").bind
		(
			"click",
			function()
			{
				addField();				
				//docReady();
			}
		);
		
		$(".removemorebtn").live
		(
			"click",
			function()
			{
				if($(".invoice-addData").length > 1)
				{
					$(".invoice-addData").last().remove();
					g_count--;					
				}				
			}
		);
		
		$(".addinvoice").bind
		(
			"click",
			function()
			{
				g_count = 0;
				$(".addrembtns").show();
				$("#invoicemodal form").attr("action","<?php echo $createurl; ?>");
				$(".invoice_func").val("create");
				$(".modal-header h3").html("Add Purchase Invoice");		
				$(".invoicemodal-body fieldset").html("");
				addField();
				$(".modal-loader").hide();
				$(".invoicemodal-body").show();
				return false;
			}
		);
		var chk = setInterval(function(){$(".editbtn").attr("onclick","window.scrollTo(0,0);$('.addinvoice').show('slow');$('#invoicemodal').show('slow');return false;");},1000);
	}
);



function customTableEvent()
{
	$(".viewbtn").live
	(
		"click",
		function(e)
		{
			e.preventDefault();
			var height = 600;
			var top = (window.innerHeight - height)/2;
			var width = 1000;
			var left = (window.innerWidth - width)/2;
			window.open($(this).attr("href"),"View Purchase Invoice","height="+height+",top="+top+",width="+width+",left="+left+",menubar=1,titlebar=0,toolbar=0,fullscreen=0");
		}
	);
		
	$(".editbtn").live("click",function()
	{
		g_count = 0;
		$("#invoicemodal form").attr("action","<?php echo $editurl; ?>");
		$(".invoicemodal-body fieldset").html("");
		addField(true);
		$(".addrembtns").hide();
		$(".invoicemodal-body").hide();
		$(".modal-loader").show();	
		$(".modal-header h3").html("Loading User Type...");
		$(".invoice_func").val("edit");
		$.ajax
		(
			{
				url : $(this).attr("href"),
				success : function(data)
				{
					try
					{
						var response = JSON.parse(data);
					}
					catch(e)
					{
						alert("REQUEST FAILED!!! Reloading this page...");
						window.location.href = window.location.href;
					}	
					if(response.status == "success")
					{
					//	addField(true);
						var ob_data = response.invoice[0];
						$(".modal-header h3").html("Invoice # "+ob_data.invoice_id);
						//invoice_name  invoice_image display_status
						$(".purchaser").find("option").removeAttr("selected");
						$(".purchaser").find("option").each
						(
							function()
							{
								if(ob_data.purchaser == $(this).attr("value"))
								{
									$(this).attr("selected","selected");
								}
							}
						);
						$(".purchase_date").val(ob_data.purchase_date);
						$(".transportation_cost").val(ob_data.transportation_cost);
						var jsttisonce =  true;
						for (i in ob_data.invoiceitems)
						{							
							var invoice_item = ob_data.invoiceitems[i];
							i = parseInt(i)+1;
							if(jsttisonce)
							{
								jsttisonce = false;
							}
							else
							{
								addInvoiceItems(1,true);
							}	
							var oParent = $(".invoice_items_"+i);
							oParent.find(".product_id").find("option").removeAttr("selected");
							oParent.find(".product_id").find("option").each
							(
								function()
								{
									if(invoice_item.product_id == $(this).attr("value"))
									{
										$(this).attr("selected","selected");
									}
								}
							);
							oParent.find(".quantity").val(invoice_item.quantity);
							oParent.find(".item_purchase_date").val(invoice_item.item_purchase_date);
							oParent.find(".quantity").val(invoice_item.quantity);
							oParent.find(".mrp_item").val(invoice_item.mrp_item);
							oParent.find(".purchase_rate").val(invoice_item.purchase_rate);
							oParent.find(".total_purchase_rate").val(invoice_item.total_purchase_rate);
							oParent.find(".invoice_item_id").val(invoice_item.purchase_invoice_item_id);
							oParent.find(".vendor_id").find("option").removeAttr("selected");
							oParent.find(".vendor_id").find("option").each
							(
								function()
								{
									if(invoice_item.vendor_id == $(this).attr("value"))
									{
										$(this).attr("selected","selected");
									}
								}
							);	
						}						
						/*
						var arrCategory = ob_data.category_id.split(",");
						$(".category_id").find("option").each
						(
							function()
							{
								if(arrCategory.indexOf($(this).val()) != -1)
								{
									$(this).attr("selected","selected");
								}
							}
						);*/
						$(".invoice_id").val(ob_data.invoice_id);
						$(".modal-loader").hide();
						$(".invoicemodal-body").show();
						$(".invoice-addData:last").find("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();
						setTimeout(function(){$(".cleditor").cleditor();},300);
					}
				},
				fail : function()
				{
					alert("There is some problem with your request...");
					window.location.href = window.location.href;
				}
			}
		);				
	});
}

		
</script>



