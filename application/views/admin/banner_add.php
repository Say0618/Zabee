<?php $banner_id = (isset($post_data) && $post_data->id != "")?$post_data->id:"";?>
<form action="<?php echo base_url('seller/banner/form/'.$banner_id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="card-body">
		<div class="row form-group" id="SelectLinkType">
			<div class="col-sm-3 ">
				<label for="linktype" class="control-label resposive-label" style="float:left">&nbsp;<?php echo $this->lang->line('link_type');?>:</label>
			</div>
			<div class="col-sm-6">
				<?php
					$banner_link = array(
						''=>$this->lang->line('soption_error'),
						'ProductLink'=>'Zabee ' .$this->lang->line('product_link'),
						'ExternalLink'=> $this->lang->line('external_link'),
					);
					echo form_dropdown('linktype', $banner_link, (isset($_POST['linktype'])?$_POST['linktype']:($bannerlink ? $bannerlink->linktype : '')), 'class="form-control" id="linktype" value  name="linktype"');
				?>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label"><?php echo $this->lang->line('category_select');?></label>
			</div>
			<div class="col-sm-6">
				<?php $cat_id = (isset($post_data->category_id))?explode(",",$post_data->category_id):"";?>
				<select class="form-control select2" id="category_id" multiple="multiple" name="category_id[]">
					<?php 
					foreach($categoryData as $parent){
						if(in_array($parent['category_id'],$cat_id)){
							$selected = 'selected="selected"';
						}else{
							$selected = "";
						}
						echo "<option value = '".$parent['category_id']."' data-private = '".$parent['is_private']."' pcat = '".$parent['parent_category_id']."' ".$selected.">".$parent['category_name']."</option>";
					}
					?>
				</select>
			</div>	
		</div> 
		<div class="row form-group ProductBox" style="display:none">
			<div class="col-sm-3">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('search_product');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control Product_Link" id="keyword" name="Product_Link" type="text" value="<?php echo (isset($post_data) ? $post_data->product_link : "") ?>" placeholder="<?php echo $this->lang->line('banner_product_name');?>">	
				<label class="error_text"></label>
			</div>	
		</div>
		<div class="row form-group ExternalBox" style="display:none">
			<div class="col-sm-3 ">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('external_link');?></label>
			</div>
			<div class="col-sm-6">
				<input type="url" class="form-control Banner_Link" id="Banner_Link" name="Banner_Link" type="text" value="<?php echo ( isset($post_data) ? $post_data->banner_link : "") ?>" placeholder="<?php echo $this->lang->line('banner_link');?>" >	
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label"><?php echo $this->lang->line('extra_parameter');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control" id="optional-field" name="optional_field" type="text" value="<?php echo isset($post_data) ? $post_data->extra_params : "" ?>" placeholder="">	
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3 ">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('banner_image');?></label>
			</div>
			<div  class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="profile_image" type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none"><?php echo $this->lang->line('banner_imgerror');?></label>
				<span id="incorrect_file_format" style="display:none;"><label class="error"><?php echo $this->lang->line('banner_incorrectimg');?></label></span>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group">
				<div class="col-sm-3">
					<label class="control-label resposive-label" for="focusedInput">Image Alternate</label>
				</div>
			<div class="col-sm-6">
				<input class="form-control focused brand_name" id="focusedInput" name="image_alt" type="text" value="<?php echo isset($post_data) ? $post_data->image_alt : "" ?>" placeholder="Enter image alternate">	<?php if($this->session->flashdata("image_alt_error")){
					echo '<label class="return-error" style="color:red;">'.$this->session->flashdata("image_alt_error").'</label>';
				} ?>				
			</div>
			<div class="clearfix"></div>
		</div>	
		<hr>
			<input type="hidden" name="product_id" id="product_id">
			<div class="row float-right mb-3">
				<input type="hidden" value="<?php echo $banner_id;?>" id="banner_id" name="banner_id">
				<button type="reset" class="btn btn-danger resetform" >Reset</button>
				<button type="submit" class="btn btn-success ml-3" id="submitBtn" name="submitBtn"><?php echo ($banner_id !="")?"Update":"Save"?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	