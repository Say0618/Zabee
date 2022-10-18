<?php
	$editurl = base_url()."seller/variantcategories/editedvariant";		
	$createurl = base_url()."seller/variantcategories/createvariant";		
?>
<script>
var pageLink = window.location.protocol+'//'+window.location.host+window.location.pathname;
$('.deletebtn').click(function() {
	id = $(this).attr('data-id');
	$('#vcat_id').val(id);
	$('#confirmation').modal('show');
	return false;
});
$(document).on('click', '.deletebtns', function(){
	$('#confirmation').modal('hide');
	id = $('#vcat_id').val();
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/Variantcategories/deletevariant');?>/"+id,
		success: function(response){
			$('#vcat_id').val('');
			setTimeout(function(){
				window.location.href = pageLink+'?status='+response.status;
			}, 200);
		}, error: function(status,b,c){
			
		}
	});
	return false;
});
var g_count = 0;
function getcategoryHtml(count,isedit)
{
var strHtml = '<span class = "category-addData"><div class="control-group">'
			+'<label class="control-label" for="focusedInput">Variant Name</label>'
			+'<div class="controls">'
   		    +' <input class="form-control category_name" id="focusedInput" name="v_title_'+count+'" type="text" value="" placeholder="Enter Variant Name">'
			+'</div>'
			+'</div>';
	strHtml	+='<div class="control-group">'
			+'<label class="control-label" for="focusedInput">Status</label>'
		+'<div class="row" style=" padding: 0px 0px 5px 0px;">'
			+'<div class="cusRadio"><input type = "radio" id="radioEnable" class = "display_status customRadio" name = "is_active_'+count+'" checked value = "1"/><label for="radioEnable" class="text-cursor radio-inline customCheck">Enable</label>'
			+'<input type = "radio" id="radioDisable" class = "display_status customRadio" name = "is_active_'+count+'" value = "0"/><label for="radioDisable" class="text-cursor radio-inline customCheck">Disable</label>'
			+'</div></div>'
			+'</div>';	
	if(isedit != undefined)
	{
		strHtml	+= '<input type = "hidden" value = "" name = "v_id_'+count+'" class = "category_id"/>';
	}			
	strHtml	+= '<input type = "hidden" value = "<?php echo $data[0]['v_cat_id']?>" name = "v_cat_id_'+count+'" class = "v_cat_id"/>';	
	strHtml += "</span>";
	return strHtml;
}//category_name  category_image display_status



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
	$(".category_count").val(g_count);
	
}

function addField(isEdit)
{
	g_count++;
	$(".categoriesmodal-body fieldset").append(getcategoryHtml(g_count,isEdit));				
	if(isEdit == undefined)$(".category-addData:last").find("input:checkbox, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();	
}

$(document).ready
(
	function()
	{
		$(".addmorebtn").bind
		(
			"click",
			function()
			{
				addField();
				//docReady();
			}
		);
		
		$(".removemorebtn").bind
		(
			"click",
			function()
			{
				if($(".category-addData").length > 1)
				{
					$(".category-addData").last().remove();
					g_count--;					
				}				
			}
		);
		
		$(".addcategories").bind
		(
			"click",
			function()
			{
				g_count = 0;
				$(".addrembtns").show();
				$("#categorymodal form").attr("action","<?php echo $createurl; ?>");
				$(".category_func").val("create");
				$(".modal-header h3").html("Add new Categories");		
				$(".categoriesmodal-body fieldset").html("");
				addField();
				$(".modal-loader").hide();
				$(".categoriesmodal-body").show();
				return false;
			}
		);
	}
);		
//function customTableEvent()
//{
	
/*	$(".editbtn").on("click",function()
	{
		g_count = 0;
		$("#categorymodal form").attr("action","<?php echo $editurl; ?>");
		$(".categoriesmodal-body fieldset").html("");
		addField(true);
		$(".addrembtns").hide();
		$(".categoriesmodal-body").hide();
		$(".modal-loader").show();	
		$(".modal-header h3").html("Loading User Type...");
		$(".category_func").val("edit");
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
						//console.log(e);return false;
						alert("REQUEST FAILED!!! Reloading this page...");
						window.location.href = window.location.href;
					}	
					if(response.status == "success")
					{
						var ob_data = response.data[0];
						$(".modal-header h3").html(ob_data.v_title);
						//category_name  category_image display_status
						$(".category_name").val(ob_data.v_title);
						//$(".catImg").attr("src",ob_data.category_image);						
						$(".display_status").each
						(
							function()
							{
								if($(this).val() == ob_data.is_active)
								{
									$(".display_status").removeAttr("checked");
									$(this).attr("checked","checked");
								}
							}
						);
						$(".category_id").val(ob_data.v_id);
						//$(".modal-loader").hide();
						$(".categoriesmodal-body").show();
						$("#categorymodal").modal('show');
						$(".category-addData:last").find("input:checkbox, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();
					}
				},
				fail : function()
				{
					alert("There is some problem with your request...");
					window.location.href = window.location.href;
				}
			}
		);				
	});*/
//}
  $(document).ready(function() {
        $('.dropdown').click(function() {
                $('.dropdown-content').slideToggle("fast");
        });
    });

  $(document).ready(function(){
  $(".dropdown-toggle").dropdown();
  $(".toggle-on").text("Yes");
  $(".toggle-off").text("No");

		
});	

function updatestatus(identifier){  
	var id = $(identifier).data('catid');
	var status = 0;
	var active = $(identifier).val();
	var v_cat_id = $(identifier).data('v_cat_id');
	var value = 0;
	if(active == 1){
		$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/variantcategories/countActiveVariants');?>",
		data: {'id':v_cat_id},
		success: function(result){
			if(result.v_id > 1){
				console.log(result.v_id)
				$(identifier).data('isactive',0);
				$(identifier).val("0");
				value = 0;
				$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/variantcategories/deleteVar');?>",
		data: {'id':id, 'value':value},
		success: function(response){}
		});
			}
			else{
				alert('At least 1 should be active');
				$('#toggle'+id).parent().removeClass('off').addClass('on');	
			}
		}
		});
		
	} 
	else if((active) == 0){
		$(identifier).data('isactive',1);
		$(identifier).val("1");
		value = 1 ;
	
	$.ajax({
		type: "POST",
		cache:false,
		dataType: "JSON",
		async:true,
		url: "<?php echo base_url('seller/variantcategories/deleteVar');?>",
		data: {'id':id, 'value':value},
		success: function(response){}
});
	}
}



</script>
<div class="modal fade" id="categorymodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<?php 
				$attr = array
				(
				"method"=>"POST",
				"class"=>"form-horizontal",
				"onsubmit"=>"validate(this,event);"
				);
				echo form_open_multipart("",$attr);
			?>
		<input type ="hidden" name = "category_func" value = "" class = "category_func"/>
		<input type ="hidden" name = "category_count" value = "" class = "category_count"/>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body">
				<div class="box-content categoriesmodal-body">
					<fieldset></fieldset>
					<span class = "addrembtns">
						<span class = "btn btn-success addmorebtn">Add more</span>
						<span class = "btn btn-danger removemorebtn">Remove</span>
					</span>
				</div>				
			  </div>
			  <div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Close</a>
				<input type = "submit" value = "Save changes" class="btn btn-primary" />
			  </div>
			</div>
		</div>
</div>

<div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Delete Variant</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div class="box-content">
				Are you sure?
			</div>				
		  </div>
		  <div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">No</a>
			<a href="" class = "btn btn-primary deletebtns ml-2" ><i class='icon-trash icon-white'></i>Yes</a>
			<input type="hidden" value="" name="vcat_id" id="vcat_id" />
		  </div>
		</div>
	</div>
</div>
</div>