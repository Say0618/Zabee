<?php $offer_id = (isset($post_data) && $post_data->id != "")?$post_data->id:""; ?>
<form action="<?php echo base_url('seller/offers/form/'.$offer_id)?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">
		Add Offers
	</div>
	<div class="card-body">
		<div class="row form-group" id="SelectLinkType">
			<div class="col-sm-3 ">
				<label for="linktype" class="control-label resposive-label" style="float:left">&nbsp;Name:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="<?php echo $name = (isset($post_data) && $post_data->offer_name != "")?$post_data->offer_name:""; ?>" placeholder="Enter Offer Name Here...">	
				<label class="error_text"></label>
			</div>
			<div class="clearfix"></div>
		</div>
		<!-- <div class="row form-group ProductBox" style="display:none">
			<div class="col-sm-3">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('search_product');?></label>
			</div>
			<div class="col-sm-6">
				<input class="form-control Product_Link" id="keyword" name="Product_Link" type="text" value="" placeholder="<?php echo $this->lang->line('product_link');?>">	
				<label class="error_text"></label>
			</div>	
		</div> -->
		<!-- <div class="row form-group ExternalBox" style="display:none">
			<div class="col-sm-3 ">
				<label class="control-label resposive-label" for="focusedInput"><?php echo $this->lang->line('external_link');?></label>
			</div>
			<div class="col-sm-6">
				<input type="url" class="form-control Banner_Link" id="Banner_Link" name="Banner_Link" type="text" value="" placeholder="<?php echo $this->lang->line('banner_link');?>" >	
			</div>
			<div class="clearfix"></div>
		</div> -->
		<div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label">Position</label>
			</div>
			<div class="col-sm-6">
				<select class="form-control" name="position" id="position">
                    <option>Left</option>
                    <option>Right</option>
                </select>
			</div>	
		</div>
		<div class="row form-group">
			<div class="col-sm-3 ">
				<label class="control-label resposive-label" for="focusedInput">Offer Image</label>
			</div>
			<div  class="col-sm-6">
				<div class="custom-file-upload file-loading">
					<input class="input-b8" id="input-b8" name="offer_image" type="file" accept="image/*">
				</div>
				<label for="input-b8" class="error" id="input-b8-error" style="display:none"><?php echo $this->lang->line('banner_imgerror');?></label>
				<span id="incorrect_file_format" style="display:none;"><label class="error"><?php echo $this->lang->line('banner_incorrectimg');?></label></span>
			</div>
			<div class="clearfix"></div>
		</div>	
		<hr>
		<div class="row float-right mb-3">
			<input type="hidden" value="<?php echo $offer_id;?>" id="offer_id" name="offer_id">
			<button type="reset" class="btn btn-danger resetform" >Reset</button>
			<button type="submit" class="btn btn-success ml-3" id="submitBtn" name="submitBtn"><?php echo ($offer_id !="")?"Update":"Save"?></button>
			<div class="clearfix"></div>
		</div>
		</div>
	</div>
</form>
	