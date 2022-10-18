<style>
	.close{
		line-height: 40px;
	}
</style>
<?php
//echo "<pre>";print_r($subCategoryData); die();

	$editurl = base_url()."seller/product/editedproduct";		
	$createurl = base_url()."seller/product/createInventory";		
	if(isset($redirect))
	{
		$editurl = base_url()."seller/product/editedproduct".$redirect;
		$createurl = base_url()."seller/product/createInventory".$redirect;		
	}
	
?>
<?php 
		$attr = array
		(
			"method"=>"POST",
			"class"=>"form-horizontal",
			"id" => "productForm"
		);
		echo form_open_multipart($createurl,$attr);
	?>

		<input type="hidden" name="return_id" id="return_id" value="" />
		<div class="row card-header d-flex align-items-center">
			  Create/Update Inventory
		</div>
		<div class="card-body">	
		<input type ="hidden" name = "product_func" value = "" class = "product_func"/>
		<input type ="hidden" name = "product_count" value = "" class = "product_count"/>
		<?php $count=0; $isedit="";?>
			<div class="row form-group">
					<!-- <button type="button" onclick="test()">Test</button> -->
					<div class="col-sm-2 text-right">
						<label class="col-form-label " >Title</label>
					</div>
					<div  class="col-sm-4">
						<input class="form-control product_name" id="keyword" name="product_name" value="<?php echo set_value('product_name'); ?>" type="text"  placeholder="Enter Product name">
						<?php echo form_error('product_name'); ?>
					</div>
					
					<div class="clearfix"></div>
                    <div class="col-sm-2 text-right">
						<label class="col-form-label " >Condition</label>
                    </div>
                    <div  class="col-sm-4">
					<?php 
                    if($conditionData){
                    ?>
                        <select name = "condition_id[]"  multiple  id="condition_id" class="form-control" data-placeholder="Select Condition">
        
                        <option></option>
                        <?php
                        foreach($conditionData as $parent)
                        {
                        echo "<option value = '".$parent['condition_id']."'>".$parent['condition_name']."</option>";
                        }
                        ?>
                        </select>
                    <?php
                    }
                    else{
                        echo  "<span style = \'color:red;\'>No Product Condition found.</span>Please <a href = '".base_url('seller/condition')."'>Add Condition</a>";
                    }
                    ?>
                    <?php echo form_error('condition_id[]'); ?>
                    </div>
                <div class="clearfix"></div>
			</div>
        <!-- <div class="" id="condition_fieldset"></div>-->
            <div class="col-sm-12 condition_fieldset" id="condition_fieldset" style="display:none">
                <h2>Product Condition</h2>
                <ul class="nav nav-tabs" role="tablist"></ul>
                <div class="tab-content"></div>
            </div>
    <div class="col-sm-12 padding15">
    	<input type="hidden" name="product_id" value="" id="product_id" />
		<input type="hidden" name="dummy_id" value="" id="dummy_id" />
		<input type="hidden" id="approve" />
       	<!-- <a href="javascript:void(0)" onClick="variantValidate()" >Variant Check</a> -->
       	<a href="<?php echo base_url()."seller/product";?>" class="btn btn-primary float-right  mr-3" style="margin-left: 10px;">Back</a>
        <input type = "submit" value = "Save" class="btn btn-primary float-right d-none" id="save"/>
    </div>
    <span class = "product-addData">
	<center style = 'width : 100%; padding-top: 23px;'></center>
	</span>
	</div> 
</form>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Return Policies</h4>
      </div>
      <div class="modal-body">
		<h3>Select return policy:</h3>
		
			<?php if(!empty($returns)){?>
			<select name="returnId" id="mySelect">
			<?php
				$hasDefault = false;
				foreach($returns as $return){
					if(!$hasDefault)
						$hasDefault = ($return->is_default == 1)?true:false;
			?>
				<option <?php echo ($return->is_default == 1 || ($return->id == 0 && !$hasDefault))? 'selected="selected" ':''; ?>value="<?php echo $return->id; ?>"><?php echo $return->returnpolicy_name; ?></option>
			<?php }?>	
			</select>
			<?php } else { ?>
				<strong>No Default Return Policy Found.</strong>
			<?php }?>	
		</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default Save" data-dismiss="modal">Close</button>  
      </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Return Policies</h4>
      </div>
     
      <div class="modal-footer">
        <button type="button" class="btn btn-default Save" data-dismiss="modal" >Save</button>  
      </div>
</div>
<div class="modal fade" id="recreate-modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <span style="color: orange;font-weight: bold;">Alert</span>
               	<button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" style="padding: 0px 0px 0px 15px;">
                <div class="row" style="padding: 15px;">
                    <div class="panel-body">
                       <input type="hidden" id="recreate_id" value="">
                        <span>Are you sure you want to <strong>Re-create</strong> this Inventory?</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" id="recreate_btn" data-dismiss="modal">Yes</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>     
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
