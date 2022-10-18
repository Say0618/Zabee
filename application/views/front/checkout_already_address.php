<?php $destination = ""; if($bradcrumb == "Checkout"){$destination = "checkout";} else{ $destination = "buyer";} ?>
<div class="container">
    <?php $this->load->view("front/bradcrumb",array("bradcrumbs"=>array(array("url"=>"#","cat_name"=>$bradcrumb))));?>
    <?php if($progress_bar){?>
        <?php $this->load->view("front/progressbar",array("shipping"=>"active","payment"=>"","confirmation"=>""));?>
    <?php }?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'){echo '<div class="alert alert-success" role="alert"> '.(isset($_GET['msg'])?$_GET['msg']:"Address added successfully").'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'){echo '<div class="alert alert-success" role="alert"> '.(isset($_GET['msg'])?$_GET['msg']:"Address deleted successfully").'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';} ?>
    <div class="col-12 pt-3">
        <h5><a href="<?php echo base_url('shipping/add/'.$checkerForCheckout); ?>" class="addDiffAddress"><i class="fa fa-plus"></i>&nbsp;<?php echo $this->lang->line('add_new_address');?></a></h5>
    </div>
    <div class="row p-3">
    <?php 
        $default_id = "";
        $from = ($bradcrumb == "Checkout")?"c":"s";
        // echo"<pre>"; print_r(count($locations)); die();
        foreach($locations as $loc){
            if($loc->use_address == "1"){
                $default_id = $loc->id;
            }
        ?>
    <div class="col-lg-6 col-sm-12 mb-3">
        <div class="card card-color">
            <div class="card-body">
            <div class="row">
                <?php if(count($locations) > 1 ){ ?>
                    <div class="col-7">
                        <?php
                        if($bradcrumb == "Checkout"){?>
                        <?php if($loc->use_address != "1"){ ?>
                            <label class="addDiffAddress btn-sm default">
                            <input type="radio" name="check" value="<?php echo $loc->id;?>" class="uta" /> <?php echo $this->lang->line('usethisAddress');?></label>
                        <?php } }?>
                        <label class="addDiffAddress btn-sm default">
                        <input type="radio" name="default" value="<?php echo $loc->id;?>" class="make-default" <?php if($loc->use_address == "1"){echo 'checked="checked"';}?> /> <?php echo ($loc->use_address == "1")?$this->lang->line('default'):"Make Default"?></label>
                    </div>
                <?php } ?>
                <div class="<?php echo (count($locations) > 1 ) ? "col-5" : "col-12"?> text-right">
                    <?php if($loc->use_address == 0) { ?>
                        <a title="delete address" class="btn btn-default btn-sm pull-right addDiffAddress pt-0" href="<?php echo base_url("$destination/deleteaddress/".$loc->id); ?>"><?php echo $this->lang->line('delete');?> <i class="fa fa-trash editColor bin-size"></i> </a>
                    <?php } ?>
                        <a class="btn btn-default btn-sm pull-right addDiffAddress pt-0" href="<?php echo base_url('shipping/add/'.$checkerForCheckout.'/'.$loc->id."?c=1"); ?>" ><?php echo $this->lang->line('edit');?> <i class="fa fa-edit editColor"></i></a>
                </div>
            </div>
            <div class="col-12 address_value">
                <?php echo $loc->fullname?>
            </div>
            <div class="col-12 address_value">
                <?php echo $loc->address_1."</br>"?>
                <?php echo $loc->address_2;?>
                <?php echo $loc->city?>,
                <?php echo (is_numeric($loc->state))?getCountryNameByKeyValue('id', $loc->state, 'state', true,'tbl_states'):$loc->state;?>,
                <?php echo $loc->zip?>,
                <?php echo getCountryNameByKeyValue('id', $loc->country, 'nicename', true)?>
            </div>
            <div class="col-12 address_value ">
                <?php echo $loc->contact?>
            </div>
        </div>
    </div>
</div>
<?php } ?> 
    <div class="col-12 text-center">
        <?php if($bradcrumb == "Checkout"){?>
            <a href="<?php echo base_url('cart'); ?>" class="btn backToCart rounded-0" title="Back"><?php echo $this->lang->line('backTocart');?></a>
            <a href="<?php echo base_url('checkout/useAddress/'.$default_id); ?>" class="btn btn-primary ml-3 toPayment rounded-0" id="toPayment" title="Next"><?php echo $this->lang->line('next');?></a>
        <?php } ?>
    </div>
    </div> <!-- 1st Row div  -->
</div> <!-- Main container div  -->
<script>
$( document ).ready(function() {
    $('.uta').click(function() {  
        var value = $(this).val();
        var location = "<?php echo base_url('checkout/useAddress/')?>"+value; 
        $("#toPayment").attr('href',location); 
    }); 
    $('.make-default').click(function() {  
        var value = $(this).val();
        var loc = "<?php echo base_url('checkout/makeDefault/')?>"+value+"/"+"<?php echo $from?>"; 
        location.href = loc;
    }); 
});

</script>