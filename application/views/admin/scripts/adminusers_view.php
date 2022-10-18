<?php
	$editurl = base_url()."seller/adminusers/editeduser";		
	$createurl = base_url()."seller/adminusers/createadminuser";		
?>
<script>
function validate(oDiv,event)
{
	var check = false;
	$(oDiv).find("[type = text], select").each
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
}

$(document).ready
(
	function()
	{
		$(".addusertype").bind
		(
			"click",
			function()
			{
				$("#adminusers_modal form").attr("action","<?php echo $createurl; ?>");
				$(".user_type_id").val("create");
				$(".modal-header h3").html("Add new User Type");
					
				
				$(".admin_username").val("");
				$(".admin_password").val("");
				   
				$(".admin_name").val("");
				$(".usertype").val("");
				$(".admin_email").val("");
				$(".admin_mobile").val("");
				
				$(".admin_id").val("");
				
					
				$(".modal-loader").hide();
				$(".usereditmodal-body").show();
				return false;
			}
		);
	}
);		
function customTableEvent()
{
	$(".editbtn").live("click",function()
	{
		$("#adminusers_modal form").attr("action","<?php echo $editurl; ?>");
		$(".usereditmodal-body").hide();
		$(".modal-loader").show();	
		$(".modal-header h3").html("Loading User...");
		$.ajax
		(
			{
				url : $(this).attr("href"),
				success : function(data)
				{
					var response = JSON.parse(data);							
					if(response.status == "success")
					{
						var ob_data = response.data[0];
						$(".modal-header h3").html(ob_data.admin_name);
						$(".admin_username").val(ob_data.admin_username);
						$(".admin_password").val(ob_data.admin_password);
						   
						$(".admin_name").val(ob_data.admin_name);
						$(".user_type").val(ob_data.user_type_name);
						$(".admin_email").val(ob_data.admin_mobile);
						$(".admin_mobile").val(ob_data.admin_email);
						
						$(".admin_id").val(ob_data.admin_id);
						$(".modal-loader").hide();
						$(".usereditmodal-body").show();
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
function askDelete(id){
		$('.id').val(id);
		$('#confirmation').modal('show');
	}
	$('.confirm_del').click(function(){
		var id = $('.id').val();
		data = {id:id};
		url = '<?php echo base_url('seller/adminusers/deleteuser'); ?>/'+id;
		ajaxRequest(data, url, function(data){
			if(data.status == 1){
				oTable._fnReDraw();
				$('#confirmation').modal('hide');
			} else {
				alert('try again!');
			}
		});
	});
	
var oTable;
oTable = $('.datatables').dataTable({
            "aaSorting": [[1, "asc"], [2, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "iDisplayLength": 100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?php echo site_url('seller/adminusers/get') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?php echo $this->security->get_csrf_token_name() ?>",
                    "value": "<?php echo $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
			"fnDrawCallback": function ( oSettings ) {
				/* Need to redo the counters if filtered or sorted */
				if( oSettings.bSorted || oSettings.bFiltered )
				{
					for ( var i=0, iLen=oSettings.aiDisplay.length ; i<iLen ; i++ )
					{
						$('td:eq(0)', oSettings.aoData[ oSettings.aiDisplay[i] ].nTr ).html( i+1 );
					}
				}
			},
            "aoColumns": [
                {"bSortable": false}, null, null, null, null, null,null,{"bSortable": false}
            ]
        });
</script>
<div class="modal fade" id="adminusers_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<?php 
				//print_r($user_types);die();
				$attr = array
				(
				"method"=>"POST",
				"class"=>"form-horizontal",
				"onsubmit"=>"validate(this,event);"
				);
				echo form_open_multipart("",$attr);
			?>
		<input type ="hidden" name = "admin_id" value = "" class = "admin_id"/>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h4>Add new user</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				
			  </div>
			  <div class="modal-body">
				<div class="box-content usereditmodal-body">
					<fieldset>
						<div class="control-group">
								<label class="control-label" for="focusedInput">User Name</label>
								<div class="controls">
								  <input class="input-large focused admin_username" id="focusedInput" name="admin_username" type="text" value="" placeholder="Enter User name">
								</div>
						</div>
						<div class="control-group">
								<label class="control-label" for="focusedInput">Password</label>
								<div class="controls">
								  <input class="input-large focused admin_password" id="focusedInput" name="admin_password" type="text" value="" placeholder="Enter Password">
								</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="focusedInput">Name</label>
							<div class="controls">
							  <input class="input-large focused admin_name" id="focusedInput" name="admin_name" type="text" value="" placeholder="Enter Type name">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="focusedInput">User Type</label>
							<div class="controls">
							  <select name = "user_type" class = "user_type">
								<?php
									foreach($user_types as $usertype)
									{
										echo "<option value = '".$usertype['user_type_id']."'>".$usertype['user_type_dpname']."</option>";
									}
								?>
							  </select>
							</div> 
						</div>
						<div class="control-group">
							<label class="control-label" for="focusedInput">Email ID</label>
							<div class="controls">
							  <input class="input-large focused admin_email" id="focusedInput" name="admin_email" type="text" value="" placeholder="Enter Type name">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="focusedInput">Mobile</label>
							<div class="controls">
							  <input class="input-large focused admin_mobile" id="focusedInput" name="admin_mobile" type="text" value="" placeholder="Enter Type name">
							</div>
						</div>
					</fieldset>
				</div>				
			  </div>
			  <div class="modal-footer">
				<a href="#" class="btn" data-dismiss="modal">Close</a>
				<input type = "submit" value = "Save changes" class="btn btn-primary" />
			  </div>
			</div>
		</div>
</div>