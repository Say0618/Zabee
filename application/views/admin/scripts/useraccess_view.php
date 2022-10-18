<?php
	$editurl = base_url()."seller/useraccess/editeduser";		
	$createurl = base_url()."seller/useraccess/createusertype";		
?>
<script>
function validate(oDiv,event)
{
	var check = false;
	$(oDiv).find("input").each
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
	if(check == false && ($(".selmodules").val() == "" || $(".selmodules").val() == null))
	{
		alert("All fields are compulsory");
		event.preventDefault();
		$(this).focus();				
	}
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
				$("#useredit_modal form").attr("action","<?php echo $createurl; ?>");
				$(".user_type_id").val("create");
				$(".modal-header h3").html("Add new User Type");
				$(".typename").val("");
				$(".displayname").val("");
				$(".selmodules").find("option").removeAttr("selected");				
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
		$("#useredit_modal form").attr("action","<?php echo $editurl; ?>");
		$(".usereditmodal-body").hide();
		$(".modal-loader").show();	
		$(".modal-header h3").html("Loading User Type...");
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
						$(".modal-header h3").html(ob_data.user_type_name);
						$(".typename").val(ob_data.user_type_name);
						$(".displayname").val(ob_data.user_type_dpname);
						if(ob_data.allowed_links != "")
						{
							$(".selmodules").find("option").removeAttr("selected");
							if(ob_data.allowed_links == "*")
							{
								$(".selmodules").find("option").each
								(
									function()
									{
										$(this).attr("selected","selected");
									}
								);
							}
							else
							{
								var splitter = ob_data.allowed_links.split(",");								
								for(i in splitter)
								{
									$(".selmodules").find("option").each
															(
																function()
																{
																	if($(this).attr("value").trim() == splitter[i].trim())
																	{
																		$(this).attr("selected","selected");
																	}
																}
															);
								}
							}																		
						}
						$(".user_type_id").val(ob_data.user_type_id);
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
		/**
* allowed_links: "test"
user_type_dpname: "UserA"
user_type_id: "2"
user_type_name: "usera"
*/
/*
window.onload = function ()
{
	<?php
		$nclks = "[";
		if(isset($nonclickable) && $nonclickable)
		{
			foreach($nonclickable as $nonclicks)
			{
				$nclks .= $nonclicks.",";
			}			
			$nclks = substr($nclks,0,strlen($nclks)-1);
		}		
		$nclks .= "]";
	?>	
//	if(typeof(g_table) !=="undefined"){
		g_table = g_table.replace("/\/g","");
		$(".datatable tbody").html(g_table);
		$('.datatable').dataTable({
				"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
				"sPaginationType": "bootstrap",
				"oLanguage": {
				"sLengthMenu": "_MENU_ records per page"
				},
				"aoColumnDefs": [
				{ 'bSortable': false, 'aTargets': <?php echo $nclks; ?> }]
			} );
			if(typeof(customTableEvent) == "function")customTableEvent();
			$('.btn-close').click(function(e){
				e.preventDefault();
				$(this).parent().parent().parent().fadeOut();
			});
			$('.btn-minimize').click(function(e){
				e.preventDefault();
				var $target = $(this).parent().parent().next('.box-content');
				if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
				else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
				$target.slideToggle();
			});
			$('.btn-setting').click(function(e){
				e.preventDefault();
				$('#myModal').modal('show');
			});
		$("#g-table-loader-custom").hide("slow");
		$(".dataTables_wrapper").show();
		setTimeout(function()
		{
			/*
			$(".dataTables_wrapper").css
			(
				{
					"overflow":"auto"		
				}
			);*/
			/*$(".table-box").each(function()
				{
					$(this).css({"width":$(this).find(".table").width()+50+"px"});
				}
			)
		},500);	*/
	//}
//}
//var g_table_dnt_initialise = true;
function askDelete(id){
		$('.id').val(id);
		$('#confirmation').modal('show');
	}
	$('.confirm_del').click(function(){
		var id = $('.id').val();
		data = {id:id};
		url = '<?php echo base_url('seller/useraccess/deleteusertype'); ?>/'+id;
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
            'sAjaxSource': '<?php echo site_url('seller/useraccess/get') ?>',
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
               {"bSortable": false}, null, null, null,{"bSortable": false}
            ]
        });
</script>
<div class="modal fade" id="useredit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<?php 
				//print_r($modules);die();
				$attr = array
				(
				"method"=>"POST",
				"class"=>"form-horizontal",
				"onsubmit"=>"validate(this,event);"
				);
				echo form_open_multipart("",$attr);
			?>
		<input type ="hidden" name = "user_type_id" value = "" class = "user_type_id"/>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<h4>Add new user type</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				
			  </div>
			  <div class="modal-body">
				<div class="box-content usereditmodal-body">
					<fieldset>
						<div class="control-group">
							<label class="control-label" for="focusedInput">Type Name</label>
							<div class="controls">
							  <input class="input-large focused typename" id="focusedInput" name="typename" type="text" value="" placeholder="Enter Type name">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="focusedInput">Display Name</label>
							<div class="controls">
							  <input class="input-large focused displayname" id="focusedInput" name="displayname" type="text" value="" placeholder="Enter display name">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="selectError1">Select Modules</label>
							<div class="controls">
							  <select id="selectError1" class = "selmodules" multiple name = "allowed_modules[]" >
								<?php 
									
									if($modules)
									{
										foreach($modules as $module)
										{
											echo "<option value = '".$module['module_name']."'>".$module['module_name']."</option>";
										}
									}
								?>
							  </select>
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
<?php 
	ob_start();
	function getActionHtml($btns)
	{		
		$strHtml = "";
		foreach($btns as $btn)
		{
			$onclick = "";
			if($btn['modal'])
			{
				$onclick = 'onclick = "$(\''.trim($btn['modal']).'\').modal(\'show\');return false;"';
			}
			if($btn['type'] == "view")
			{
				$strHtml.='<a href="'.$btn['link'].'" class="btn btn-success viewbtn" style = "margin:5px;" '.$onclick.'>
										<i class="icon-zoom-in icon-white"></i>  
										'.$btn['text'].'  
									</a>';
			}
			else if($btn['type'] == "edit")
			{
				$strHtml.='<a href="'.$btn['link'].'" class="btn btn-info editbtn" style = "margin:5px;" '.$onclick.'>
										<i class="icon-edit icon-white"></i>  
										'.$btn['text'].'                                        
									</a>';
			}
			else if($btn['type'] == "delete")
			{
				$strHtml.='<a href="'.$btn['link'].'" class="btn btn-danger deletebtn" style = "margin:5px;" '.$onclick.'>
										<i class="icon-trash icon-white"></i> 
										'.$btn['text'].'  
									</a>';
			}			
		}
		return $strHtml;
	}
	function getStatusHtml($status)
	{
//		echo "<pre>"; print_r($status);
		$strHtml = '';
		if($status['type'] == "active")
		{			
			$strHtml.= '<span class="label label-success">'.$status['text'].'</span>';			
		}
		else if($status['type'] == "pending")
		{
			$strHtml.= '<span class="label label-warning">'.$status['text'].'</span>';
		}
		else if($status['type'] == "danger")
		{
			$strHtml.= '<span class="label label-important">'.$status['text'].'</span>';
		}
		else if($status['type'] == "inactive")
		{
			$strHtml.= '<span class="label label-inactive">'.$status['text'].'</span>';
		}
		return $strHtml;
	}		
?>
<?php 
	if(isset($descFirst) && $descFirst == TRUE)
	{
		echo '<script>
			function customTableEvent()
			{
				$(".datatable th:first-child").trigger("click");
			}
			</script>';
	}
	ob_end_flush();	
?>