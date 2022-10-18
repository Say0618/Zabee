<link rel="stylesheet" type="text/css" href="<?php echo assets_url('front/css/message.css')?>" >
<div class="container">
<div class="row position-relative">
		<?php 
        echo '<div class="col mt-2 mb-2"><a class="breadcrumb-item" href="'.base_url().'">'. $this->lang->line('home').' </a><a class="breadcrumb-item" href="'.base_url('message').'">'.$this->lang->line('inbox').'</a></div>';
        ?>
    </div>
	<div class="row">
		<div class="col-12">
			<div class="row">
                <div class="col-3">
                    <h4 class="" style=""><?php echo $this->lang->line('mess_inbox');?></h4>
                </div>
            </div>
		</div>
		<div class="col-12">
            <div class="row">
                <div class="col-5 col-sm-3" style="">
                    <div class="row">
                         <div class="custom-control custom-checkbox my-auto ml-3">
                             <input type="checkbox" class="custom-control-input" id="customCheck" name="rememberme">
                             <label class="custom-control-label custom-checkbox-label" for="customCheck"></label>
                         </div>
                        <div class="dropdown">
                          <button class="btn border-0 bg-transparent" ><i class="fas fa-angle-down"></i></button>
                          <div class="dropdown-content">
                            <a href="#">Link 1</a>
                            <a href="#">Link 2</a>
                            <a href="#">Link 3</a>
                          </div>
                        </div>
                        
                        <div class="dropdown p-0">
                          <button class="btn broder-0 bg-transparent"><i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                          <div class="dropdown-content">
                            <a href="#">Link 1</a>
                            <a href="#">Link 2</a>
                            <a href="#">Link 3</a>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="col-7 col-sm-9">
                    <div class="row">
                        <div class="col-4 col-sm-3 dates" >	
                            <input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="txtFromDate" name="fromDate" readonly />
                        </div>
                        <div class="ml-2 col-4 col-sm-3 dates" >
                            <input type="text" class="form-control" placeholder="<?php echo $this->lang->line('select_date');?>" id="txtToDate" name="toDate" readonly />
                        </div>
                        <div class="col-3">
                            <a href="#" class="next btn rounded-0"><i class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
			<table class="table <?php echo ($buyerList['rows'] > 0)?"table-responsive":""?>">
				<thead>
					<tr class="d-none d-sm-table-row">
						<th class="border-0" width="25%">
							<span class=""><?php echo $this->lang->line('all');?></span> <a href="" class="ml-2" ><i class="fas fa-angle-down"></i></a>
						</th>
                        <th class="border-0"></th>
                        <th class="border-0" width="5%"></th>
						<th class="border-0" width="25%">
							<span><?php echo $this->lang->line('date');?></span><a href="" class="ml-2" ><i class="fas fa-angle-down"></i></a>
						</th>
					</tr>
                    <tr class="d-sm-none d-table-row">
						<th class="border-0" width="25%">
							<span class=""><?php echo $this->lang->line('all');?></span> <a href="" class="" ><i class="fas fa-angle-down"></i></a>
						</th>
                        <th class="border-0" width="25%">
							<span><?php echo $this->lang->line('date');?></span><a href="" class="ml-2" ><i class="fas fa-angle-down"></i></a>
						</th>
                        <th class="border-0"></th>
                        <th class="border-0" width="5%"><?php echo $this->lang->line('reply');?></th>
					</tr>
				</thead>
				<tbody>
                	<?php if($buyerList['rows'] > 0){?>
						<?php foreach($buyerList['result'] as $index=>$message){ 
							$product_variant_id = ($message->product_variant_id)?$message->product_variant_id:"";
							$url = $message->userid.','.$product_variant_id.','.$message->item_type.','.$message->seller_id.','.$message->buyer_id.','.$message->item_id.','.$message->product_link.','.$_SESSION['userid'];
							$url = base64_encode($url);
						?>
                        <tr>
                            <td class="d-none d-sm-table-cell"> 
                                <div class="custom-control custom-checkbox d-inline">
                                     <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $index?>" name="rememberme">
                                     <label class="custom-control-label custom-checkbox-label" for="customCheck<?php echo $index?>"></label>
                                 </div>
								 <span class="fa fa-star StarMessage" id="StarMessage" data-myval = "0"></span>
							</td>
                            <td class="d-sm-none d-table-cell"> 
							 
                                <div class="custom-control custom-checkbox d-inline">
                                     <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $index?>" name="rememberme">
                                     <label class="custom-control-label custom-checkbox-label" for="customCheck<?php echo $index?>"></label>
                                 </div>
								
                                 <label class="star glyphicon glyphicon-star-empty"><input type="checkbox" title="bookmark page" id="bookmark<?php echo $index?>"><label class="ml-2" for="bookmark<?php echo $index?>"></label></label>
							</td>
                            <td class="d-sm-none d-table-cell"><?php echo ucwords($message->sender_name);?><br /><?php echo $message->seen_datetime;?></td>
                            <td><?php echo (strlen($message->message) > 43)?substr($message->message,0,43)."...":$message->message;?></td>
                            <td align="middle"><a href="<?php echo base_url('message/getChat?open='.$url)?>"><i class="fas fa-reply"></i></a></td>
                            <td class="d-none d-sm-table-cell"><?php echo formatDateTime($message->sent_datetime, true);?></td>
                        </tr> 
                        <?php }?>
                    <?php }else{?>
                    	<tr><td colspan="4" align="middle">No message found.</td></tr>
					<?php }?>
				</tbody>
			  </table>
		
	</div>
</div>
<script>
$(document).ready(function(){
	$("#txtFromDate").datepicker({
        numberOfMonths: 1,
		minDate: 0,
        onSelect: function(selected) {
			$("#txtToDate").datepicker("option","minDate", selected)
        }
    });
    $("#txtToDate").datepicker({ 
        numberOfMonths: 1,
		minDate: 0,
        onSelect: function(selected) {
           $("#txtFromDate").datepicker("option","maxDate", selected)
        }
    });
	$('#txtFromDate').click(function () {
        $('#txtToDate').removeAttr("disabled")
    });
});
$( ".StarMessage" ).click(function() {
	var a = $('#StarMessage').data('myval'); //getter
	if(a == 1){
		$('#StarMessage').attr("data-myval","0"); //setter
		$(".StarMessage").css("color", "black");
	} else if(a == 0) {
		$('#StarMessage').attr("data-myval","1"); //setter	
		$(".StarMessage").css("color", "orange");	
	}
	
	
});
</script>
