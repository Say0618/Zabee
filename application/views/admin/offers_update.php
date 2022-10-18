<?php //echo"<pre>";print_r($bannersWeGot); die(); ?>
<form action="<?php echo base_url('seller/offers/offer_update/'.$id);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
	<div class="row card-header d-flex align-items-center">
		Update Offer
	</div>
	<div class="card-body">
        <div class="row form-group" id="SelectLinkType">
			<div class="col-sm-3 ">
				<label for="linktype" class="control-label resposive-label" style="float:left">&nbsp;Name:</label>
			</div>
            <div class="col-sm-6">
				<input class="form-control" name="offer_name" type="text" value="<?php echo $offersWeGot->offer_name ?>" placeholder="Enter Offer Name Here...">	
				<label class="error_text"></label>
			</div>
			<div class="clearfix"></div>
		</div>
		<div class="row form-group" id="SelectLinkType">
            <div class="col-sm-3">
                    <label class="control-label resposive-label">Position</label>
                </div>
                <div class="col-sm-6">
                    <select class="form-control" name="position" id="position">
                        <option>Left</option>
                        <option>Right</option>
                        <!-- <option>Top</option>
                        <option>Bottom</option> -->
                    </select>
                </div>	
            </div>
            <div class="row form-group">
			<div class="col-sm-3">
				<label class="control-label resposive-label" for="focusedInput">Offer Image</label>
			</div>
			<div  class="col-sm-6">
				<div class="image-cropper-subcat">
					<?php if(isset($offersWeGot->offer_image) && $offersWeGot->offer_image != ""){?>
                    <div style="position: relative; padding: 0px; cursor: pointer;" type="file">
						<!-- <img id="myImg" src="<?php echo $link; ?>" type="text" class="rounded" style = "width:150px;height:100px;"/> -->
					</div>
                    <?php }?>
						<div class="custom-file-upload file-loading">
							<input class="input-b8" id="input-b8" name="profile_image" type="file" accept="image/*">
						</div>
						<label for="input-b8" class="error" id="input-b8-error" style="display:none">Please select banner image</label>
						<span id="incorrect_file_format" style="display:none;"><label class="error">Can't update with an incorrect file format.</label></span>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr>
		<input type="hidden" value="<?php echo $offersWeGot->id;?>" id="offer_id" name="offer_id">
			<div class="col-12 offset-9 mb-3">
				<a href="<?php echo $_SERVER['REDIRECT_URL']?>" class="btn btn-danger"><?php echo $this->lang->line('reset');?></a>
					<!-- <button type="reset" class="btn btn-danger" >Reset</button> -->
					<button type="submit" class="btn btn-success  ml-3" id="Submit"><?php echo $this->lang->line('update');?></button>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</form>
	