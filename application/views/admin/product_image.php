<?php //echo "<pre>"; print_r($this->uri->segment(4)); echo "</pre>"; die(); ?>
<?php //die($isLink); ?>
<form action="<?php echo base_url('seller/product/selectimage/'.$this->uri->segment(4).'/1'.'/'.$pagestatus."/".$exist."/".$isLink);?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> 
<?php $imageLink = base_url('custom/images?img='); ?> 
<?php //echo "<pre>"; print_r($this->data);die(); echo "</pre>";?>
<div class="container">
	<div class="row">
		<h5 class="headingFive">Choose one of the following image(s) as a primary thumbnail for your product.</h5>
		<?php $i = 0; //echo "<pre>"; print_r($forimageloop); echo "</pre>"; die(); ?>
		<?php while($i < $forimageloop){ ?>
			<?php //echo "<pre>"; print_r($imageLink.$prod_images[$i]); echo "</pre>"; die(); ?>
			<?php //echo "<pre>"; print_r($is_local); echo "</pre>"; die(); ?>
			<div class="col-sm-3" style="position:relative">
					<div class="" style="">
						<a class="btn btn-default btn-sm product-delete" id="delete" href="<?php echo base_url('seller/product/delete_image/'.$product_id.'/'.$prod_images_thumbnail[$i].'/'.$i.'/'.$forimageloop);?>"><i class="fa fa-remove"></i></a>
					</div>
				<label>
					<div class="productImage" id="productImage_<?php echo $i ?>" >
						<?php //echo "<pre>"; print_r($prod_images_thumbnail[1]); echo "</pre>"; die(); ?>
						<div class="text-center height-css">
							<div class="now_imageDiv">
							<?php  if($is_local == 1){ ?>
								<img class="img-fluid max-height-css" src="<?php echo product_thumb_path($prod_images_thumbnail[$i]); ?>"> 
							<?php } else { ?>
								<?php //print_r($prod_images_thumbnail[$i]); die(); ?>
								<img class="img-fluid product-image" src="<?php echo $prod_images_thumbnail[$i]; ?>"> 
							<?php } ?>
							</div>
						</div>
						<div class="radio radioFontsize">
							<?php $radioclass = 'myradio_'.$i.''; ?>
							<span class='container-image'>
								<?php $value = $prod_images_thumbnail[$i]; //echo "<pre>"; print_r($prod_images); echo "</pre>"; die(); ?>
								<?php //print_r($value); die(); ?>
								<label class="primaryImagelable"><input type="radio" class="<?php echo $radioclass; ?>" name="primary_image" value="<?php echo $value; ?>" /> Select as primary image</label>
							</span>
						</div>
					</div>
				</label>
			</div>
			<script>
				/*$(document).ready(function () {
					$('.myradio_'+<?php echo $i ?>).click(function() {
						alert($('.myradio_'+<?php echo $i ?>).val());
					});
				});*/
			</script>	
		<?php $i++; ?>
		<?php } ?>
		
	<?php if($pagestatus == 1){ ?>
				<div class="col-sm-3">
					<label>
						<div class="addImage" id="">
						<div class='text-center addImagepadding'>
						<h6>Add Image</h6>
					</div>
							<center>
								<div class="image-cropper ">
									<div style="position: relative; padding: 0; cursor: pointer;" type="file">
										<?php 
										if(isset($prof->profile_imagelink) && $prof->profile_imagelink != ""){
											$link = image_url()."profile/".$prof->profile_imagelink.'?'.time();
										} else {
											$link = assets_url()."backend/images/plus-hi.png";
										}?>
										<img id="myImg" src="<?php echo $link; ?>" type="text" alt="profile picture"  class="rounded"/>
									</div>
								</div>
								<div class="col-sm-12" style="padding-bottom:10px">
									<center>
										<div class="custom-file-upload">
											<input class="addImageupload" type='file' name="product_image[]" accept="image/*" />
										</div>
									</center>
								</div>
							</center>
						</div>
					</label>
				</div>
			<?php } ?>
			
	</div>
	<span class="radioerror"></span>
	<hr>
	<div class="row">
		<div class="col-sm-12">
			<div class="pull-right" style="margin: 0px 14px 14px;">
				<button type="submit" class="btn btn-success image_submit" id="Submit">Save</button>
			</div>
			<div class="pull-right" style="margin: 0px 14px 14px;">
				<a href = "<?php echo base_url("seller/product"); ?>" class="btn btn-danger" >Back to Products</a>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
</form>