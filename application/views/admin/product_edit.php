<?php //print_r($data); ?>
<div class="row card-header d-flex align-items-center">
			  Edit Product
		</div>
<?php //print_r($redirect_to);die();?>
<form action="<?php echo base_url()."seller/product/save_edit_product/".$redirect_to; ?>" method="post" id="product_edit"><br>
<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label" >Product Name</label>
					</div>
                    <div  class="col-sm-10">
						<input class="form-control product_name" id="keyword" name="product_name" type="text" value="<?php if(isset( $data->product_name))echo $data->product_name; ?>" placeholder="Enter Product name">
                    </div>
					<div class="clearfix"></div>
 </div>  
 <div class="row form-group">
            <div class="col-sm-2 text-right">
                 <label class="form-control-label resposive-label" >Product Description</label>
            </div>
            <div  class="col-sm-10">
                <div class="controls">
				<!-- <input class="form-control product_name" id="product_description" name="product_description" type="text" value="<?php if(isset( $data->product_description))echo $data->product_description; ?>" placeholder="Enter Product name"> -->

                 <textarea class="ckeditor form-control product_description" id="product_description" name="product_description" placeholder="Enter Product Description"><?php echo stripslashes($data->product_description); ?></textarea>
                </div>					
            </div>
        	<div class="clearfix"></div>
        </div>
        <div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="form-control-label resposive-label" >Keywords</label>
					</div>
					<div  class="col-sm-4">
						<div class="controls">
						<input class="form-control" multiple="multiple" type="text" name="product_keyword" id="product_keyword" data-role="tagsinput" value="<?php if(isset( $data->keywords))echo $data->keywords; ?>">

						</div>
					</div>
				<div class="clearfix"></div>
			</div>
            <div class="clearfix"></div>
    <div class="col-sm-12 padding15">
    	<input type="hidden" name="product_id" value="<?php echo $data->product_id ?>" id="product_id" />
		<input type="hidden" name="sp_id" value="<?php echo $data->seller_product_id ?>" id="sp_id" />
    	<input type="hidden" name="pv_id" value="<?php echo $data->product_variant_id ?>" id="pv_id" />
        <a href="<?php echo base_url()."seller/product/";?>" class="btn  btn-danger pull-right" style="margin-left: 10px;">Cancel</a>
		<input type = "submit" value = "Save" class="btn btn-primary pull-right" /> <div class="clearfix"></div>
    </div>               
</form>