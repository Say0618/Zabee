<?php 
	$createurl = base_url()."seller/product/addAccessories";
	$attr = array
	(
		"method"=>"POST",
		"class"=>"form-horizontal",
		"id" => "productForm"
    );
    $acc_id = "";
    $getAccData = isset($getAccData[0])?$getAccData[0]:"";
    echo form_open_multipart($createurl,$attr);
?>
    <div class="row card-header d-flex align-items-center">
          <?php echo $this->lang->line('add_product_accessories');?>
    </div>
    <?php if($this->session->flashdata('success') || $this->session->flashdata('error') != ""){?>
    <div class="col mt-3 mb-3">
		<span class="success"><strong><?php echo ($this->session->flashdata('success'))?$this->session->flashdata('success'):""; ?></strong></span>
        <span class="error"><strong><?php echo ($this->session->flashdata('error'))?$this->session->flashdata('error'):""; ?></strong></span>
    </div>
    <?php } ?>
    <div class="card-body">	
        <div class="row form-group">
            <div class="col-sm-2 text-right">
                <label class="col-form-label " ><?php echo $this->lang->line('search_product');?></label>
            </div>
            <div  class="col-sm-4 forRemovingBrTag">
                <input class="form-control product_name" id="keyword" name="product_name" type="text"  placeholder="Enter Product name"
				value="<?php echo (isset($getAccData->product) ? $getAccData->product : ""); ?>">
                <label id="keyword-error" class="error"></label>
				<label id="jq-error" for="keyword" generated="true" class="error"></label>
                <?php echo form_error('product_name'); ?>
				<?php if($this->session->flashdata('error_2') != ""){?>
					<span style="color: red;font-weight: normal !important;font-size: 12px;"><?php echo ($this->session->flashdata('error_2'))?$this->session->flashdata('error_2'):""; ?></span>
				<?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-2 text-right">
                 <label class="col-form-label" ><?php echo $this->lang->line('search_accessories');?></label>
            </div>
            <div  class="col-sm-4">
                <input class="form-control product_name" id="keyword2" name="accessories" type="text"  placeholder="Enter Product name"/>
				<label id="keyword2-error" class="error"></label>
				<label class="errordiv2"></label>
			</div>
            <div class="clearfix"></div>
        </div>
        <div class="row form-group">
            <div class="col-sm-2 text-right">
                <label class="col-form-label " ><?php echo $this->lang->line('selected_accessories');?></label>
            </div>
            <div  class="col-sm-4">
                <select name="accessories[]" multiple="multiple" class="form-control" id="accessories">
                    <?php 
                    if(isset($getAccData->accessory)){
                    $acc = explode(",",$getAccData->accessory);
                    //print_r($acc);die();
                    foreach($acc as $accessory){ 
                        $value = explode("-zabee-",$accessory);
                        if($acc_id){
                            $acc_id = $acc_id.",".$value[0];
                        }else{
                            $acc_id = $value[0];
                        }
                        
                    ?>
					<option selected value="<?php echo $value[0];?>"><?php echo $value[1];?></option>
					<?php }} ?>
                </select>
            </div>
        </div>
        <div class="col-sm-12 padding15">
			<input type="hidden" name="product_id" value="<?php echo ($getAccData)?$getAccData->product_id:"" ?>" id="product_id" />
            <input type="hidden" name="id" value="<?php echo ($getAccData)?$getAccData->id:"" ?>" id="id" />
			<input type="hidden" name="prod_acc_id" value="<?php echo ($acc_id)?$acc_id:"";?>" id="prod_acc_id" />
            <a href="<?php echo base_url()."seller/product/prodAccessories";?>" class="btn btn-default pull-right" style="margin-left: 10px;"><?php echo $this->lang->line('back');?></a>
            <input type = "submit" value = "Save" class="btn btn-primary pull-right" id="save" />
        </div>
    </div> 
</form>
</div>
 