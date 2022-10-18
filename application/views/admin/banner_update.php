<?php //echo"<pre>";print_r($bannersWeGot); die(); ?>
<form action="<?php echo base_url('seller/banner/update/'.$banner_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">
		<?php echo $this->lang->line('update_banner');?>
	</div>
	<div class="card-body">
		<div class="row form-group" id="SelectLinkType">
			<div class="col-sm-3 text-right">
				<label for="linktype" class="control-label resposive-label">&nbsp;<?php echo $this->lang->line('link_type');?>:</label>
			</div>
				<div class="col-sm-6">
				 <?php
					$banner_link = array(
						''=>$this->lang->line('external_link'),
						'ProductLink'=>$this->lang->line('product_link'),
						'ExternalLink'=>$this->lang->line('external_link'),
					);
					if($bannersWeGot->banner_link !=""){
						$selected = $this->lang->line('external_link');
					}else{
						$selected = $this->lang->line('product_link');
					}
					echo form_dropdown('linktype', $banner_link, $selected, 'class="form-control " id="linktype" name="linktype"');
					//echo form_dropdown('linktype', $banner_link, (isset($_POST['linktype'])?$_POST['linktype']:($bannerlink ? $bannerlink->linktype : '')), 'class="form-control " id="linktype" name="linktype"');
				?> 
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group ProductBox" style="display:none">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('product_link');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control Product_Link" id="keyword" name="Product_Link" type="text" value="<?php echo (isset($_POST['product_link']) ? $_POST['product_link'] : ($bannersWeGot ? $bannersWeGot->product_link : '')); ?>" placeholder="Product Name">	
				<div class="error_text"></div>
			</div>	
		</div>
		<div class="row form-group ExternalBox" style="display:none">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('external_link');?></label>
			</div>
			<div class="col-sm-6">
				<input type="url" class="form-control Banner_Link" id="Banner_Link" name="Banner_Link" type="text" value="<?php echo (isset($_POST['banner_link']) ? $_POST['banner_link'] : ($bannersWeGot ? $bannersWeGot->banner_link : '')); ?>" placeholder="<?php echo $this->lang->line('banner_link');?>">	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label"><?php echo $this->lang->line('extra_parameter');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" id="optional-field" name="optional_field" type="text" value="<?php echo (isset($_POST['extra_params']) ? $_POST['extra_params'] : ($bannersWeGot ? $bannersWeGot->extra_params : '')); ?>" placeholder="">	
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3 text-right">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('banner_image');?></label>
			</div>
			<div  class="col-sm-6">
				<div class="image-cropper-subcat">
					<?php 
						if(isset($bannersWeGot->banner_image) && $bannersWeGot->banner_image != ""){
					?>
				 	<div style="position: relative; padding: 0px; cursor: pointer;" type="file">
						<!-- <img id="myImg" src="<?php echo $link; ?>" type="text" class="rounded" style = "width:150px;height:100px;"/> -->
					</div>
					<?php }?>
						<div class="custom-file-upload file-loading">
							<input class="input-b8" id="input-b8" name="profile_image" type="file" accept="image/*" value="<?php echo $link; ?>">
						</div>
						<label for="input-b8" class="error" id="input-b8-error" style="display:none"><?php echo $this->lang->line('banner_imgerror');?></label>
						<span id="incorrect_file_format" style="display:none;"><label class="error"><?php echo $this->lang->line('banner_incorrectimg');?></label></span>
					</div>
				</div>
			</div>
		<div class="clearfix"></div>
		</div>	
		<hr>
		<input type="hidden" name="banner_id" id="banner_id" value="<?php echo $bannersWeGot->id; ?>">			
			<div class="col-12 offset-9 mb-3">
				<a href="<?php echo $_SERVER['REDIRECT_URL']?>" class="btn btn-danger"><?php echo $this->lang->line('reset');?></a>
					<!-- <button type="reset" class="btn btn-danger" >Reset</button> -->
					<button type="submit" class="btn btn-success  ml-3" id="Submit"><?php echo $this->lang->line('update');?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	