<div class="container">
    <?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>$this->lang->line('payment')),array("url"=>base_url("checkout"),"cat_name"=>$this->lang->line('checkout')))));?>
	<?php $this->load->view("front/progressbar",array("shipping"=>"completed","payment"=>"active","confirmation"=>""));?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'){echo '<div class="alert alert-success" role="alert">'.$_GET['msg'].' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
    
    <div id="smart-button-container">
        <div id="paypal-btn-container">
            <div id="paypal-button-container">
                <button class="form-control payType mt-4 mb-4" value="paypal" id="paypal">
                    <span class="paypal-logo">
                        <i>Pay</i><i>Pal</i>
                    </span>
                </button>
                <button class="form-control payType mb-4" data-show="0" value="card" id="card">Debit or Credit Card</button>
            </div>
        </div>
    </div>

    <div class="col-12 add-card-heading d-none">
        <h5><a href="<?php echo base_url('checkout/payment?add_card=1'); ?>" class="addDiffAddress"><i class="fa fa-plus"></i>&nbsp;<?php echo $this->lang->line('add_new_card');?></a></h5>
    </div>
    <?php
        if($payment_error){
            echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><center><strong>Error!</strong> '.$payment_error_message['Errors'].'</center><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
    ?>
    <div class="row p-3 card-list d-none">
    <?php 
        $default_id = "";
        // echo"<pre>";print_r($cards); die();
        foreach($cards as $card){
            if($card->default == "1"){
                $default_id = $card->id;
            }
        ?>

    <div class="col-lg-6 col-sm-12 mb-3">
        <div class="card card-color">
            <div class="card-body">
            <div class="row">
            <?php if(count($cards) > 1){ ?>
                <div class="col-7">
                <?php if($card->default != "1"){?>
                    <label class="addDiffAddress btn-sm default">
                    <input type="radio" name="check" value="<?php echo $card->id;?>" class="uta" /> <?php echo $this->lang->line('usethiscard');?></label>
                <?php } ?>    
                    <label class="addDiffAddress btn-sm default">
                    <input type="radio" name="default" value="<?php echo $card->id;?>" class="make-default" <?php if($card->default == "1"){echo 'checked="checked"';}?> /> <?php echo ($card->default == "1")?$this->lang->line('default'):$this->lang->line('makedefault')?></label>
                </div>
            <?php } ?>
                <div class=" <?php echo (count($cards) > 1) ? "col-5" : "col-12" ?> text-right">
                    <?php if($card->default == 0) {?>
                        <a title="Delete Card" class="deleteCard btn btn-default btn-sm pull-right addDiffAddress pt-0" data-id="<?php echo $card->id; ?>" data-name="<?php echo addslashes($card->card_name); ?>" href="javascript:void(0)"><?php echo $this->lang->line('delete');?> <i class="fa fa-trash editColor bin-size"></i> </a>
                    <?php } ?>
                    <a class="btn btn-default btn-sm pull-right addDiffAddress pt-0" onclick="editCard('<?php echo $card->id?>','<?php echo $card->holder_name?>','<?php echo $card->expiry_month?>','<?php echo $card->expiry_year?>','<?php echo $card->card_name;?>')" href="javascript:void(0)" ><?php echo $this->lang->line('edit');?> <i class="fa fa-edit editColor"></i></a>
                </div>
            </div>
            <div class="col-12 address_value">
                <?php echo $card->holder_name?>
            </div>
            <div class="col-12 address_value">
                <?php echo "xxxx-xxxx-xxxx-".$card->card_number."</br>";?>
                <?php echo $card->expiry_month." / ".$card->expiry_year;?>,
                <?php echo $card->card_type?>
            </div>
        </div>
    </div>
</div>
<?php } ?> 
    <form name="payWithCard" id="payWithCard" method="post" action="<?php echo base_url('checkout/paymentwithcard'); ?>" novalidate>
        <input type="hidden" name="orderID" id="orderIDCard" value="<?php echo $orderID; ?>" />
        <input type="hidden" name="card_id" id="card_id" value="<?php echo $default_id;?>" />
        <input type="hidden" name="payment_method" value="stripe"  />
    </form>
    <div class="col-12 text-center">
        <a href="<?php echo base_url('checkout?c=1'); ?>" class="btn backToCart rounded-0" title="Back"><?php echo $this->lang->line('backToship');?></a>
        <a href="javascript:void(0)" class="btn btn-primary ml-3 toPayment rounded-0" id="payWithCardBtn" title="Next"><?php echo $this->lang->line('next');?></a>
    </div>
    </div> <!-- 1st Row div  -->
</div> <!-- Main container div  -->

<div class="modal fade" id="confirmation-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><?php echo $this->lang->line('delete_card');?>!<button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="card_id" value="<?php echo $default_id;?>">
                        <strong><?php echo $this->lang->line('delete_card2');?> <span class="card_name"></span>?</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span class="error d-none"><?php echo $this->lang->line('invalid_card');?></span>
                <button type="button" class="btn btn-danger " id="card_delete" data-dismiss="modal"><?php echo $this->lang->line('delete');?></button>
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $this->lang->line('cancel');?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="card-update" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><?php echo $this->lang->line('update_card');?>!<button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('name_card');?></label>
                        </div>
                        <div class="col-8">
                        <input type="text" class="form-control cardField rounded-0" placeholder="<?php echo $this->lang->line('namee_card');?>" id="modal_card_name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('holder_name');?>:</label>
                        </div>
                        <div class="col-8">
                        <input type="text" class="form-control rounded-0 cardField" placeholder="<?php echo $this->lang->line('card_name');?>" id="modal_holder_name" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="form-control-label resposive-label payment-label"><?php echo $this->lang->line('expiry_year');?>:</label>
                        </div>
                        <div class="col-4">
                            <select id="modal_exp_date" class="form-control cardField rounded-0" alt="Expiry month">
                                <?php
                                    $month2 = date("m");
                                    for($i = 0; $i < 12; $i++){
                                        $month = date("m",strtotime("+".$i." Month"));
                                        $monthAlp = date("M",strtotime("+".$i." Month"));
                                    ?>
                                    <option <?php if($month < $month2){echo 'disabled';} ?> value="<?php echo $month; ?>"><?php echo $monthAlp; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-4">
                            <select id="modal_exp_year" class="form-control cardField rounded-0" alt="Expiry year">
                                <?php
                                    for($i = 0; $i < 11; $i++){
                                        $year = date("Y",strtotime("+".$i." Year"));
                                    ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <span class="error d-none" id="modal_error"><?php echo $this->lang->line('all_fields');?></span>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="modal_card_id" value="" />
                <button type="button" class="btn toPayment rounded-0" id="card_update"><?php echo $this->lang->line('update');?></button>
                <button type="button" class="btn backToCart rounded-0" data-dismiss="modal"><?php echo $this->lang->line('cancel');?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script>
$( document ).ready(function() {
    $('.uta').click(function() {  
        var value = $(this).val();
        $('#card_id').val(value);
    }); 
    $('.make-default').click(function() {  
        var value = $(this).val();
        var loc = "<?php echo base_url('checkout/makeCardDefault/')?>"+value; 
        location.href = loc;
    }); 
    $('#payWithCardBtn').click(function(){
        $('#payWithCard').submit();
    });
    $('.deleteCard').click(function(){
        var card_id = $(this).attr('data-id');
        var card_name = $(this).attr('data-name');
        $('#card_id').val(card_id);
        $('.card_name').text(card_name);
        $('#confirmation-modal').modal('show');
    });
    $('#card_delete').click(function(){
        var card_id = $('#card_id').val();
        if(card_id == ''){
            $('#confirmation-modal .error').removeClass('d-none');
            return false;
        }
        var url = '<?php echo base_url("buyer/delete_card/"); ?>'+card_id+"/1";
        window.location.href = url;
    });
    
});
function editCard(card_id,holder_name,month,year,name){
    $("#card-update").modal("show");
    $("#modal_card_name").val(name);
    $("#modal_holder_name").val(holder_name);
    monthLength = month.length;
    if(monthLength == 1){
        month = "0"+month;
    }
    $("#modal_exp_date").val(month);
    $("#modal_exp_year").val(year);
    $("#modal_card_id").val(card_id);
}
$("#card_update").on("click",function(){
    name = $("#modal_card_name").val();
    holder_name = $("#modal_holder_name").val();
    month = $("#modal_exp_date").val();
    year = $("#modal_exp_year").val();
    card_id = $("#modal_card_id").val();
    var data = {"card_name":name,"holder_name":holder_name,"expiry_month":month,"expiry_year":year,"id":card_id};
    console.log(data);
    if(name && holder_name && month && year && card_id){
        $("#modal_error").addClass("d-none");
        $.ajax({
            type: 'POST',
            url: '<?php echo base_url(); ?>checkout/updateCard',
            data: data,
            dataType: 'json',
            success: function(response){
                if(response.status == 1){
                    location.reload();
                }else{
                    $("#modal_error").removeClass("d-none");
                    $("#modal_error").text(response.msg);
                }
            }
        });	
    }else{
        $("#modal_error").removeClass("d-none");
    }
});

$(document).on("click", ".payType", function(){
		var type = $(this).val();
		var order_id = $("#orderIDCard").val();
		if(type == "paypal"){
			window.location.replace("<?php echo base_url("checkout/payment?pay=paypal"); ?>");
		}else{
            show = $("#card").attr("data-show");
			if(show == '0'){
				$("#card").attr("data-show", '1');
				$(".add-card-heading").removeClass("d-none");
                $(".card-list").removeClass("d-none");
			}else{
				$("#card").attr("data-show", '0');
				$(".add-card-heading").addClass("d-none");
                $(".card-list").addClass("d-none");
			}
		}
	});
</script>