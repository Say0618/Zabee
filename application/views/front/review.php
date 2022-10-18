<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<div class="container">
    <div class="row">
        <div class="col-sm-12 m-3">
            <div class="card">
                <div class="card-header">
                    <h6>Product Review</h6>
                </div>
                <div class="card-body">
                <form action="<?php echo base_url('buyer/forReviews');?>" id="reviewAdd" name="reviewAdd" novalidate method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <input class='form-control input-lg' readonly placeholder='E-mail Address' name='email' type='email' value="<?php if(isset($_SESSION['email'])){ echo $_SESSION['email'];} ?>">
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <input class='form-control input-lg' readonly placeholder='Product Name' id="prod_name" name='pdt' type='text' value="<?php echo urldecode(str_replace('+',' ',$product_data[0]))?>">
                        </div>
                    </div>
					<div class="col-sm-12 mb-3">
                        <div class="custom-file-upload file-loading">
                            <input class="input-b8" id="input-b8" name="profile_image[]" multiple type="file" accept="image/*">
                        </div>
                        <label for="input-b8" class="error" id="input-b8-error" style="display:none">Please select brand image</label>
                        <span id="incorrect_file_format" style="display:none;"><label class="error">Can't update with an incorrect file format.</label></span>
                    </div>
                    <div class="clearfix"></div>
                    <div class='col-sm-12'>
                        <div class='form-group'>
                            <textarea class='form-control input-lg' placeholder='Write your review here' name='review' type='text' value= "<?php echo set_value('review'); ?>"></textarea>
                        </div>
                    </div>
                    <div class='col-sm-12'>
                        <div class='text-center' id='rateYo' name = "ratYo"></div>
                        <span class="rateYo_error" id="rateYo_error"></span>
                        <input type='hidden' name='rating' id="rating" value='0' />
                        <input type='hidden' name='product_name' value='<?php echo $product_data[0]?>' />
                        <input type='hidden' name='order_id' value='<?php echo $product_data[1]?>' />
                        <input type='hidden' name='product_id' value='<?php echo $product_data[2]?>' />
                        <input type='hidden' name='product_variant_id' value='<?php echo $product_data[3]?>' />
                        <input type='hidden' name='sp_id' value='<?php echo $product_data[4]?>' />
                        <input type='hidden' name='seller_id' value='<?php echo $product_data[5]?>' />
                        <input type='hidden' name='name' value='<?php if(isset($_SESSION['firstname'])){ echo $_SESSION['firstname'];} ?>' />
                    </div>
                    <div class="col-sm-12">
                        <button class="btn btn-lg mt-4" type="submit" id='reviewSubmit' name='btn' >Add Review</button>
                        <img src="<?php echo assets_url('front/images/preloader.gif')?>" class= "d-none" id="preloaderImage" alt="">
                    </div>
                    </div>
                </form>
                </div>
            </div>

        </div>
    </div>
</div>