<?php //echo"<pre>";print_r($shippingData);
$duration = explode("-",  $shippingData->duration);
?>
<form action="<?php echo base_url('seller/shipping/shipping_edit/'.$shippingId);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="keyword"><?php echo $this->lang->line('title');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="keyword" name="title" type="text" value="<?php echo isset($shippingData->title)?$shippingData->title:(isset($post_data['title']) ? $post_data['title'] : "");?>" placeholder="<?php echo $this->lang->line('enter_Shippingtitle');?>">	
				<?php if($this->session->flashdata("shipping_name_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("shipping_name_error").'</label>';
				} ?>
			</div>
			<div class="clearfix"></div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="price">Base <?php echo $this->lang->line('price');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="price" name="price" type="number" value="<?php echo isset($shippingData->price)?$shippingData->price:(isset($post_data['title']) ? $post_data['title'] : "");?>" placeholder="<?php echo $this->lang->line('enter_Shippingprice');?>">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="basedOn">Price based on</label>
			</div>
			<div class="col-sm-6">
				<select class="form-control" id="basedOn" name="basedOn" onchange="changeBase();">
					<option value="item"<?php echo (isset($shippingData->shipping_type) && $shippingData->shipping_type == 'item')?' selected ':(isset($post_data['basedOn']) &&  $post_data['basedOn'] == 'item')? ' selected ':''; ?>>Item</option>
					<option value="weight"<?php echo (isset($shippingData->shipping_type) && $shippingData->shipping_type == 'weight')?' selected ':(isset($post_data['basedOn']) &&  $post_data['basedOn'] == 'weight')? ' selected ':''; ?>>Weight</option>
					<option value="dimension"<?php echo (isset($shippingData->shipping_type) && $shippingData->shipping_type == 'dimension')?' selected ':(isset($post_data['basedOn']) &&  $post_data['basedOn'] == 'dimension')? ' selected ':''; ?>>Dimension</option>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group baseGroup weightGroup d-none">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="base_weight">Base Weight</label>
			</div>
			<div class="col-sm-3">
				<input class="form-control" id="base_weight" name="base_weight" type="number" value="<?php echo isset($shippingData->base_weight)?$shippingData->base_weight:(isset($post_data['base_weight']) ? $post_data['base_weight'] : "");?>" placeholder="Enter Base Weight">	
			</div>
			
			<div class="col-sm-2">
				<select class="form-control" id="weight_unit" name="weight_unit">
					<option value="lbs">lbs</option>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group baseGroup dimensionGroup d-none">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="base_dimension">Base Dimension</label>
			</div>
			<div class="col-sm-2">
				<input class="form-control" id="base_length" name="base_length" type="number" value="<?php echo isset($shippingData->base_length)?$shippingData->base_length:(isset($post_data['base_length']) ? $post_data['base_length'] : ""); ?>" placeholder="Enter Base Length">
			</div>
			<div class="col-sm-2">
				<input class="form-control" id="base_width" name="base_width" type="number" value="<?php echo isset($shippingData->base_width)?$shippingData->base_width:(isset($post_data['base_width']) ? $post_data['base_width'] : ""); ?>" placeholder="Enter Base Width">
			</div>
			<div class="col-sm-2">
				<input class="form-control" id="base_depth" name="base_depth" type="number" value="<?php echo isset($shippingData->base_depth)?$shippingData->base_depth:(isset($post_data['base_depth']) ? $post_data['base_depth'] : ""); ?>" placeholder="Enter Base Depth">
			</div>
			<div class="col-sm-2">
				<select class="form-control" id="dimension_unit" name="dimension_unit">
					<option value="inches">Inches</option>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Incremental Unit / Price</label>
			</div>
			<div class="col-sm-3">
				<input class="form-control category_name" id="inc_unit" name="inc_unit" type="number" value="<?php echo (isset($shippingData->incremental_unit)?$shippingData->incremental_unit:(isset($post_data['inc_unit']) ? $post_data['inc_unit'] : "")); ?>" placeholder="Enter Incremental Unit">
			</div>
			<div class="col-sm-3">
				<input class="form-control category_name" id="inc_price" name="inc_price" type="number" value="<?php echo (isset($shippingData->incremental_price)?$shippingData->incremental_price:(isset($post_data['inc_price']) ? $post_data['inc_price'] : "")); ?>" placeholder="Enter Incremental Price">
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="free_after">Free Shipping After</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="free_after" name="free_after" type="number" value="<?php echo isset($shippingData->free_after)?$shippingData->free_after:"";?>" placeholder="Shipping will be free after this amount reached">	
			</div>
			<div class="clearfix"></div>
		</div>
        <div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="minimum_days"><?php echo $this->lang->line('duration');?></label>
			</div>
			<div class="col-sm-3">
				<input class="form-control category_name" id="minimum_days" name="minimum_days" type="text" value="<?php echo isset($duration[0])?$duration[0]:"";?>" placeholder="<?php echo $this->lang->line('minimum_days');?>">	
			</div>
            <div class="col-sm-3">
				<input class="form-control category_name" id="maximum_days" name="maximum_days" type="text" value="<?php echo isset($duration[0])?$duration[1]:"";?>" placeholder="<?php echo $this->lang->line('maximum_days');?>">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="description">Description</label>
			</div>
			<div class="col-sm-6">
				<textarea class="form-control" id="description" name="description" placeholder="Max 100 characters allowed."><?php echo isset($shippingData->description)?$shippingData->description:"";?></textarea> 	
			</div>
			<div class="clearfix"></div>
		</div>	
		<hr>
		<div class="row">
			<div class="col-sm-12 text-right">
				<a href="<?php echo base_url("seller/shipping")?>" class="btn btn-dark" ><?php echo $this->lang->line('back');?></a>
				<button type="submit" class="btn btn-success" id="Submit" name = "Submit"><?php echo $this->lang->line('update');?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>