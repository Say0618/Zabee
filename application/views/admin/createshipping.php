<?php //print_r($post_data); ?>
<form action="<?php echo base_url('seller/shipping/createshipping/');?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('title');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="keyword" name="title" type="text" value="" placeholder="<?php echo $this->lang->line('enter_Shippingtitle');?>">
				<?php if($this->session->flashdata("shipping_name_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("shipping_name_error").'</label>';
				} ?>
			</div>
			<div class="clearfix"></div>
		</div>	
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Base <?php echo $this->lang->line('price');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="price" name="price" type="number" value="<?php echo (isset($post_data['price']) ? $post_data['price'] : ""); ?>" placeholder="<?php echo $this->lang->line('enter_Shippingprice');?>">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="basedOn">Price based on</label>
			</div>
			<div class="col-sm-6">
				<select class="form-control" id="basedOn" name="basedOn" onchange="changeBase();">
					<option value="item"<?php echo ((isset($post_data['basedOn']) &&  $post_data['basedOn'] == 1)? ' selected ':''); ?>>Item</option>
					<option value="weight"<?php echo ((isset($post_data['basedOn']) &&  $post_data['basedOn'] == 2)? ' selected ':''); ?>>Weight</option>
					<option value="dimension"<?php echo ((isset($post_data['basedOn']) &&  $post_data['basedOn'] == 3)? ' selected ':''); ?>>Dimension</option>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group baseGroup weightGroup d-none">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="base_weight">Base Weight</label>
			</div>
			<div class="col-sm-3">
				<input class="form-control" id="base_weight" name="base_weight" type="number" value="<?php echo (isset($post_data['base_weight']) ? $post_data['base_weight'] : ""); ?>" placeholder="Enter Base Weight">	
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
				<input class="form-control" id="base_length" name="base_length" type="number" value="<?php echo (isset($post_data['base_length']) ? $post_data['base_length'] : ""); ?>" placeholder="Enter Base Length">
			</div>
			<div class="col-sm-2">
				<input class="form-control" id="base_width" name="base_width" type="number" value="<?php echo (isset($post_data['base_width']) ? $post_data['base_width'] : ""); ?>" placeholder="Enter Base Width">
			</div>
			<div class="col-sm-2">
				<input class="form-control" id="base_depth" name="base_depth" type="number" value="<?php echo (isset($post_data['base_depth']) ? $post_data['base_depth'] : ""); ?>" placeholder="Enter Base Depth">
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
				<input class="form-control category_name" id="inc_unit" name="inc_unit" type="number" value="<?php echo (isset($post_data['inc_unit']) ? $post_data['inc_unit'] : ""); ?>" placeholder="Enter Incremental Unit">
			</div>
			<div class="col-sm-3">
				<input class="form-control category_name" id="inc_price" name="inc_price" type="number" value="<?php echo (isset($post_data['inc_price']) ? $post_data['inc_price'] : ""); ?>" placeholder="Enter Incremental Price">
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput">Free Shipping After</label>
			</div>
			<div class="col-sm-6">
				<input class="form-control category_name" id="free_after" name="free_after" type="number" value="<?php echo (isset($post_data['free_after']) ? $post_data['free_after'] : ""); ?>" placeholder="Shipping will be free after this amount reached, left blank if not applicable">	
			</div>
			<div class="clearfix"></div>
		</div>
        <div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('duration');?></label>
			</div>
			<div class="col-sm-3">
				<input class="form-control category_name" id="minimum_days" name="minimum_days" type="text" value="<?php echo (isset($post_data['minimum_days']) ? $post_data['minimum_days'] : ""); ?>" placeholder="<?php echo $this->lang->line('minimum_days');?>">	
			</div>
            <div class="col-sm-3">
				<input class="form-control category_name" id="maximum_days" name="maximum_days" type="text" value="<?php echo (isset($post_data['maximum_days']) ? $post_data['maximum_days'] : ""); ?>" placeholder="<?php echo $this->lang->line('maximum_days');?>">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="description">Description</label>
			</div>
			<div class="col-sm-6">
				<textarea class="form-control" id="description" name="description" placeholder="Max 100 characters allowed." maxlength="100"><?php echo isset($post_data['description'])?$post_data['description']:"";?></textarea> 	
			</div>
			<div class="clearfix"></div>
		</div>		
		<hr>
		<div class="row">
			<div class="col-sm-12 text-right">
				<a href="<?php echo base_url("seller/shipping")?>" class="btn btn-dark" ><?php echo $this->lang->line('back');?></a>
				<button type="submit" class="btn btn-success" id="Submit" name = "Submit"><?php echo $this->lang->line('save');?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	