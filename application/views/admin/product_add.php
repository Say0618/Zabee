<?php
	$createurl = base_url()."seller/product/createProduct";
	$btn = $this->lang->line('create');			
    //if($this->session->userdata['user_type'] == "1"){ 
		if((isset($_GET['pn']) && $_GET['pn'] !="") && (isset($_GET['pi']) && $_GET['pi'] !="")){
			$createurl = base_url()."seller/product/updateProduct";		
			$btn = $this->lang->line('update');
		}
	//}	
?>
<?php 
	$attr = array(
		"method"=>"POST",
		"class"=>"form-horizontal",
		"id" => "productForm");
	echo form_open_multipart($createurl,$attr);
?>
    <input type="hidden" name="return_id" id="return_id" value="" />
    <div class="row card-header d-flex align-items-center">
            <?php echo $btn.$this->lang->line('product');?> 
    </div>
    <div class="card-body">	
        <input type ="hidden" name = "product_func" value = "" class = "product_func"/>
        <input type ="hidden" name = "product_count" value = "" class = "product_count"/>
        <?php $count=0; $isedit="";?>
        <fieldset>
            <legend><?php echo $this->lang->line('product_info');?></legend>
            <div class="row form-group">
                <!-- <button type="button" onclick="test()">Test</button> -->
                <div class="col-sm-2 text-right">
                    <label class="col-form-label " ><?php echo $this->lang->line('title');?></label>
                </div>
                <div  class="col-sm-4 input-group">
                    <input class="form-control product_name" id="keyword" name="product_name" value="<?php echo set_value('product_name'); ?>" type="text"  placeholder="<?php echo $this->lang->line('enter_product_info');?>">
                    <div class="input-group-append">
                        <span class="input-group-text btn" id="editTitle" title="Edit Product Title"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                    </div>
                    <?php echo form_error('product_name'); ?>
                </div>
                
                <div class="clearfix"></div>
                <div class="col-sm-2 text-right">
                    <label class="col-form-label " ><?php echo $this->lang->line('slug');?></label>
                </div>
                <div  class="col-sm-4">
                    <input class="form-control slug" id="slug" name="slug" value="<?php echo set_value('slug'); ?>" type="text" >
                    <?php echo form_error('slug'); ?>
                </div>
                
                <div class="clearfix"></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                    <label class="col-form-label " ><?php echo $this->lang->line('brands');?></label>	
                </div>
                <div  class="col-sm-4">
                        <select name = "brands_id[]" value="<?php echo set_value('brands_id[]'); ?>" class = "brands_id form-control" id="brands_id" data-placeholder="<?php echo $this->lang->line('brands_select');?>">
                        <option></option>
                        <?php 
                        if($brandsData){
                        ?>
                        <?php
                        // echo"<pre>"; print_r($this->input->post('brands_id'][0]); die();
                        foreach($brandsData as $parent)
                        {
                            if($this->input->post('brands_id')[0] == $parent['brand_id']){
                                echo "<option selected='true' value = '".$parent['brand_id']."'>".$parent['brand_name']."</option>";
                            }else{
                                echo "<option value = '".$parent['brand_id']."'>".$parent['brand_name']."</option>";
                            }
                            
                        }?>
                        
                        <?php
                        }else{
                            echo  "<span style = \'color:red;\'><?php echo $this->lang->line('no_brand');?>.</span>Please <a href = '".base_url('seller/brands')."'><?php echo $this->lang->line('add_brand');?></a>";
                        }
                    ?>
                    </select>
                    <?php if(!$brandsData)
                    {
                    ?>
                    <span style = color:red;><?php echo $this->lang->line('no_brand');?>.</span><br>Please <a href = "<?php echo base_url('seller/brands') ?>"><?php echo $this->lang->line('add_brand');?></a>
                        <?php
                    }
                    ?>
                    <?php echo form_error('brands_id[]'); ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-sm-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('categories');?></label>
                </div>
                <?php //echo"<pre>";print_r($subCategoryData);?>

                <div class="col-sm-4">
                    <?php //echo"<pre>"; print_r($subCategoryData);?>
                    <select name = "subcategory_id[]" value="<?php echo set_value('subcategory_id[]'); ?>" multiple="multiple" class = "subcategory_id form-control" id="subcategory_id" data-placeholder="<?php echo $this->lang->line('category_select');?>">
                        <option></option>
                        <?php //echo"<pre>"; print_r($subCategoryData); die();
                        if($subCategoryData)
                        { $flag = "";
                        ?>
                        <?php
                            foreach($subCategoryData as $parent)
                            {
                                foreach($this->input->post("subcategory_id") as $cat){
                                    if($cat == $parent['category_id']){
                                        echo "<option selected='true' value = '".$parent['category_id']."' data-private = '".$parent['is_private']."' pcat = '".$parent['parent_category_id']."'>".$parent['category_name']."</option>";
                                        $flag = $parent['category_id'];
                                        break;
                                    }
                                }
                                if($flag != $parent['category_id']){
                                    echo "<option value = '".$parent['category_id']."' data-private = '".$parent['is_private']."' pcat = '".$parent['parent_category_id']."'>".$parent['category_name']."</option>";
                                }
                            }
                        ?>
                        
                        <?php
                        }
                        else
                        {
                            //die();
                        echo  "<span style = \'color:red;\'><?php echo $this->lang->line('no_sub_category');?></span>Please <a href = ".base_url('seller/subcategories')."><?php echo $this->lang->line('add_sub_category');?></a>";
                        }
                        
                    ?>
                    </select>
                    <?php if(!$subCategoryData)
                    {
                    ?>
                    <span style = color:red;><?php echo $this->lang->line('no_sub_category');?></span><br>Please <a href = "<?php echo base_url('seller/subcategories') ?>"><?php echo $this->lang->line('add_sub_category');?></a>
                        <?php
                    }
                    echo '<input type="hidden" name="cat_is_private" id="is_private" value="0"/>';

                    ?>

                    <?php echo form_error('subcategory_id[]'); ?>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                    <label class="col-form-label"><?php echo $this->lang->line('upc_code');?></label>
                </div>
                <div  class="col-sm-4">
                    <input class="form-control product_upc_code" id="upc_code"  name="product_upc_code" value="<?php echo set_value('product_upc_code'); ?>" type="text" placeholder="<?php echo $this->lang->line('enter_upc');?>">
                </div>	
                <div class="clearfix"></div>
                <div class="col-sm-2 text-right">
                    <label class="col-form-label"><?php echo "Manufacture SKU (Optional)"?></label>
                </div>
                <div  class="col-sm-4">
                    <input class="form-control product_sku_code" id="sku_code"  name="product_sku_code" value="<?php echo set_value('product_sku_code'); ?>" type="text" placeholder="<?php echo $this->lang->line('enter_sku');?>">
                </div>	
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                    <label class="col-form-label"><?php echo $this->lang->line('keywords');?></label>
                </div>
                <div  class="col-sm-4">
                    <div class="controls">
                        <input class="form-control" multiple="multiple" type="text" name="product_keyword" value="<?php echo set_value('product_keyword'); ?>" id="product_keyword" data-role="tagsinput">
                    </div>
                </div>
                <div class="col-sm-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('variant_category');?></label>
                </div>
                <div  class="col-sm-4">
                <?php 
                //echo "<pre>";print_r($variantData);
                //print_r($this->input->post('variant_cat'));die();
                if($variantData){?>
                <select name="variant_cat[]" multiple="multiple" class="form-control valid" data-placeholder="<?php echo $this->lang->line('variant_select');?>" id="variant_cat">
                <?php $vflag = "";
                    foreach($variantData as $parent){
                        //print_r($parent);
                        /*foreach($this->input->post('variant_cat') as $vcat){
                            if($vcat == $parent['v_cat_id']){
                                echo "<option selected='true' value='".$parent['v_cat_id']."'>".$parent['v_cat_title']."</option>";
                                $vflag = $parent['v_cat_id'];
                                break;
                            }
                        }*/
                        if($vflag != $parent['v_cat_id']){
                            echo "<option value='".$parent['v_cat_id']."'>".$parent['v_cat_title']."</option>";
                        }
                }?>
                </select>
                <?php }else{?>
                <span style = 'color:red;'><?php echo $this->lang->line('no_var_cat');?></span>Please <a href='<?php echo base_url('seller/variantcategories');?>'>Add Variant Category</a> <?php } ?>
                </div>
                <?php echo form_error('variant_cat'); ?>
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('short_description');?></label>
                </div>
                <div  class="col-sm-10">
                    <div class="controls">
                        <textarea class="form-control shopt_description" id="short_description" placeholder="<?php echo $this->lang->line('characters255');?>" name="short_description" maxlength="255"><?php echo set_value('short_description'); ?></textarea>
                    </div>					
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                        <label class="col-form-label" ><?php echo $this->lang->line('description');?></label>
                </div>
                <div  class="col-sm-10">
                    <div class="controls">
                        <textarea class="ckeditor form-control product_description" id="product_description" name="product_description"><?php echo set_value('product_description'); ?></textarea>
                    </div>					
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                        <label class="col-form-label"><?php echo $this->lang->line('features');?></label>
                </div>
                <div  class="col-sm-10 p-relative" id="featuresDiv">
                <?php //echo"<pre>"; print_r($this->input->post('feature')); die(); ?>
                    <div id ="features" class="row">
                        <div class="col-sm-6">
                            <input type="text" class="form-control feature" value="" name="feature[]"/>
                        </div>
                    </div>			
                    <div class="offset-sm-6 col-sm-2">
                        <button type="button" class="btn whiteColor" id="addMoreFeatures"><?php echo $this->lang->line('add_more_features');?></button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </fieldset>
        <fieldset>
        <legend><?php echo $this->lang->line('product_media');?></legend>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                    <label class="col-form-label " ><?php echo $this->lang->line('image');?></label>
                </div>
                <div  class="for-label-append col-sm-10">
                    <?php if($isedit != "") 
                    {?>
                    <img src = "" class="prodImg" style = "width:50px;height:50px;"/>		
                    <?php }?>
                    <div class="file-loading">
                    <input class="product_image input-b8" id="input-b8" name="file[]" multiple type="file" accept="image/*" value="<?php echo set_value('file[]') ?>" <?php //if($this->session->userdata['user_type'] != "1"){ echo "required";}?> >
                    </div>
                    <label for="input-b8" class="error" id="input-b8-error" style="display:none"><?php echo $this->lang->line('please_select');?></label>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-sm-2 text-right">
                        <label class="col-form-label"><?php echo $this->lang->line('embedded');?></label>
                </div>
                <div  class="col-sm-10 pl-4" id="productVideoLinkDiv">
                    <div id ="product_video_link" class="row">
                        <div class="col-sm-6" id="link">
                            <textarea class="form-control" name="product_video_link[]" id="video_link0" row="3"></textarea>
                            <input type="hidden" name="media_id[]" class="mediaIdClass" id="mediaId0" value=""/>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn whiteColor" data-mediaid="" id="addMoreVideoLink" data-c="0"><?php echo $this->lang->line('add_more');?></button>
                        </div>
                    </div>			
                </div>
                <div class="clearfix"></div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Product Dimension</legend>
            <div class="ship_fieldset">
                <div class="row form-group">
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label">Length Type</label>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="productLength_type" id="productLength_type" class="form-control">
                            <option value="cm">cm</option>
                            <option value="m">m</option>
                            <option value="in">in</option>
                        </select>
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('length');?></label>
                    </div>
                    <div  class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="productLength" id="productLength" value="<?php echo set_value('productLength') ?>" class="form-control num"/>
                    </div>	
                    <div class="clearfix"></div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('width');?></label>
                    </div>
                    <div  class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="productWidth" id="productWidth" value="<?php echo set_value('productWidth') ?>" class="form-control num" />
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('height');?></label>
                    </div>
                    <div  class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="productHeight" id="productHeight" value="<?php echo set_value('productHeight') ?>" class="form-control num" />
                    </div>	
                    <div class="clearfix"></div>
                </div> 
                <div class="row from-group mt-4"> 
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label">Weight Type</label>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="productWeight_type" id="productWeight_type" class="form-control">
                            <option value="gm">gm</option>
                            <option value="kg">kg</option>
                            <option value="oz">oz</option>
                            <option value="lb">lb</option>
                        </select>
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('weight');?></label>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="productWeight" id="productWeight" value="<?php echo set_value('productWeight') ?>" class="form-control num" /> 
                    </div>
                </div>   
            </div>
        </fieldset>
        <fieldset>
            <legend><?php echo $this->lang->line('product_shipping_info');?></legend>
            <div class="ship_fieldset">
                <div class="row form-group">
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " >Length Type</label>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="shipLength_type" id="shipLength_type" class="form-control">
                            <option value="cm">cm</option>
                            <option value="m">m</option>
                            <option value="in">in</option>
                        </select>
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('length');?></label>
                    </div>
                    <div  class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="shipLength" value="<?php echo set_value('shipLength') ?>" id="shipLength" class="form-control num" />
                    </div>	
                    <div class="clearfix"></div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label"><?php echo $this->lang->line('width');?></label>
                    </div>
                    <div  class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="shipWidth" id="shipWidth" value="<?php echo set_value('shipWidth') ?>" class="form-control num" />
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('height');?></label>
                    </div>
                    <div  class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="shipHeight" id="shipHeight" value="<?php echo set_value('shipHeight') ?>" class="form-control num" />
                    </div>
                </div>
                <div class="row form-group mt-4">
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " >Weight Type</label>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="shipWeight_type" id="shipWeight_type" class="form-control">
                            <option value="gm">gm</option>
                            <option value="kg">kg</option>
                            <option value="oz">oz</option>
                            <option value="lb">lb</option>
                        </select>
                    </div>	
                    <div class="clearfix"></div>
                    <div class="col-sm-1 col-md-1 col-lg-2 text-right">
                        <label class="col-form-label " ><?php echo $this->lang->line('weight');?></label>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input type="text" name="shipWeight" id="shipWeight" value="<?php echo set_value('shipWeight') ?>" class="form-control num" /> 
                    </div>
                </div>
                <div class="row form-group mt-4">
                    <!-- <button type="button" onclick="test()">Test</button> -->
                    <div class="col-sm-2 text-right">
                        <label for="shipNote " class="text-right"><?php echo $this->lang->line('shipping_note');?></label>
                    </div>
                    <div  class="col-sm-10">
                        <textarea name="shipNote" id="shipNote" cols="" rows="" class="form-control"><?php echo set_value('shipNote') ?></textarea>
                        <!-- <input class="form-control product_name" id="keyword" name="product_name" value="<?php //echo set_value('product_name'); ?>" type="text"  placeholder="Enter Product name"> -->
                    </div>
                </div>      
            </div>
        </fieldset>
    </div>
	<div class="col-sm-12 pb-3">
        <input type="hidden" name="product_id" value="" id="product_id" />
        <input type="hidden" name="dummy_id" id="dummy_id" >
        <a href="<?php echo base_url()."seller/product";?>" class="btn btn-primary float-right" style="margin-left: 10px;"><?php echo $this->lang->line('back');?></a>
        <button  type = "submit" class="btn btn-primary float-right" id="create"><?php echo $btn;?></button>
        
	</div>
    <span class = "product-addData">
	<center style = 'width : 100%; padding-top: 23px;'></center>
	</span>
	</div> 
</form>
</div>
<div class="modal" id="hubx_list" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Similar Products</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <ul id="hubx_product" class="list-group">
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Load More Products</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
