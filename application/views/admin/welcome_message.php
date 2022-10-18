<!-- <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> -->
<script src="<?php echo assets_url('backend/js/jquery.validate.min.js'); ?>"></script>

<style>
.error{
    color: red;
}
label.control-label{
	line-height:34px;
}
#imageUpload
{
    display: none;
}

#profileImage
{
    cursor: pointer;
}
.user {
  display: inline-block;
  width: 150px;
  height: 150px;
  border-radius: 50%;

  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
}
.one {
  background-image: url('http://placehold.it/400x200');
}

.two {
  background-image: url('http://placehold.it/200x200');
}

.three {
  background-image: url('http://placehold.it/200x400');
}
.image-cropper {
    max-width: 100px;
    height: auto;
    position: relative;
    overflow: hidden;
}

.insideimg {
 
 text-align:center;
 width:100%;
 background:white;
 bottom:0;

 padding:20px 0;
 opacity:.5
}
#myImg {
   
    cursor: pointer;
    transition: 0.3s;
	display: block;
    margin: 0 auto;
    height: auto;
    width: 100%;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    -ms-border-radius: 50%;
    -o-border-radius: 50%;
    border-radius: 50%;
}

#myImg:hover {opacity: 0.7;}
.modal {
  display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1 !important; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
	margin: -421px 0 0 -277px;
	height: 85%; /* Full height */
    overflow: hidden !important; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
}

/* Caption of Modal Image */
#caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
}

/* Add Animation */
.modal-content, #caption {    
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {-webkit-transform:scale(0)} 
    to {-webkit-transform:scale(1)}
}

@keyframes zoom {
    from {transform:scale(0)} 
    to {transform:scale(1)}
}

/* The Close Button */
.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
    .modal-content {
        width: 100%;
    }
}
#preloadView2{
	width: 100%;
    height: 100%;
    position: fixed;
    top: 0%;
    left: 0%;
    background-color: #FDFDFD;
    z-index: 1000000;
	display:none;
}
</style>
<div class="row" style="padding:10px">
	<form action="<?php //echo (!empty($user_store))?base_url('admin/dashboard/updateStore'):base_url('admin/dashboard/saveStore');?>" id="myform" name="myForm" novalidate method="post" enctype="multipart/form-data"> <!--onSubmit="return validateForm();"-->
			<div class="card-header d-flex align-items-center">
			    <h3 class="h4">Store Info</h3>
			</div>
			<div class="card-body">
				<div class="row">
                 	<!--<div id="preloadView2">
                        <center>
                            <img src="<?php //echo base_url(); ?>images/loading-main1.gif">
                            <br><br><br>
                            <div style="color: #60697E;font-size: 30px;"><b>Hold On</b> updating Store...</div>
                        </center>		
					</div>-->
                    <div class="col-sm-12">
					<center>
						
							<div class="image-cropper">
								<div style="position: relative; padding: 0; cursor: pointer;" type="file">
									<?php /*
									if(isset($user_store->store_logo) && $user_store->store_logo != ""){
										$link = base_url()."images/uploads/store_logo/".$user_store->store_logo;
									} else {
										$link = base_url()."images/uploads/store_logo/logo.jpg";
									}*/?>
									<img id="myImg" src="<?php //echo $link; ?>" type="text" alt="Store Logo"  class="rounded"/>
								</div>
							</div>
							<div class="col-sm-12" style="padding-bottom:10px">
							<center>
								<input type='file' name="profile_image" />
							</center>
							</div>
					</center>
					</div>
                    <div id="myModal" class="modal imgModal">
                      <span class="close closeModal">&times;</span>
                      <img class="modal-content modContent" id="img01">
                      <div id="caption"></div>
                    </div>					
					<div class="clearfix"></div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class="control-label">Store Name</label>
					</div>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="store_name" placeholder="Store Name" name="store_name" value="<?php //echo (isset($user_store->store_name) && $user_store->store_name)?$user_store->store_name:""?>" />
					</div>
					<div class="col-sm-2 col-xs-3 text-right ">
						<label class="col-xs-2 control-label" >Store ID</label>
					</div>
					<div class="col-sm-4 col-xs-5">
						<input id="store_id" class="form-control" value="">
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						 <label class="control-label" >Store Address</label>
					</div>
					<div class="col-sm-4">
						  <textarea class="form-control" id="store_address" placeholder = "Store Address" name="store_address" ><?php //echo (isset($user_store->store_address) &&  $user_store->store_address)?$user_store->store_address:""?></textarea>
					</div>
					<div class="col-sm-2 col-xs-3 text-right ">
						 <label class="control-label" >Legal Business Type</label>
					</div>
					<div class="col-sm-4 col-xs-5">
						<select id="legal_busniess_type" class="form-control" name="legal_busniess_type">
						<option value="">-Select Busniess Type-</option>
						<?php //foreach($getLegalBusniessType as $lbt){?>
							<option value="<?php //echo $lbt->legal_id?>" <?php //echo (isset($user_store->legal_busniess_type) && $lbt->legal_id == $user_store->legal_busniess_type)?'selected="selected"':""?> ><?php //echo $lbt->legal_busniess_type?></option>
						<?php //}?>
                    </select>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label" >Country</label>
					</div>
					<div class="col-sm-4">
						<select id="country_id" class="form-control" name="country_id">
                        	<option value="">-Select Country of Incorporation-</option>
						<?php //foreach($countries as $country){?>
							<option value="<?php //echo $country->id?>" <?php //echo (isset($user_store->country_id) && $country->id == $user_store->country_id)?'selected="selected"':""?> ><?php echo $country->name.' ('.$country->iso.')'?></option>
						<?php //}?>
                        </select>
					</div>
					<div class="col-sm-2 col-xs-3 text-right ">
						<label class=" control-label" >Contact Person</label>
					</div>
					<div class="col-sm-4 col-xs-5">
						<input type="text" value="<?php //echo (isset($user_store->contact_person) && $user_store->contact_person)?$user_store->contact_person:""?>" class="form-control" id="contact_person" placeholder="Contact Person" name="contact_person" />
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label" >Contact Phone</label>
					</div>
					<div class="col-sm-4">
						<input type="tel" class="form-control" value="<?php //echo (isset($user_store->contact_phone) && $user_store->contact_phone)?$user_store->contact_phone:""?>" id="contact_phone" placeholder="Contact Phone" name="contact_phone" />
					</div>
					<div class="col-sm-2 col-xs-3 text-right ">
						<label class=" control-label" >Contact Email</label>
					</div>
					<div class="col-sm-4 col-xs-5">
						<input type="email" class="form-control" value="<?php //echo (isset($user_store->contact_email) && $user_store->contact_email)?$user_store->contact_email:""?>" id="contact_email" placeholder = "Contact Email" name="contact_email" />
					</div>
				</div>
				<div class="row form-group">
					<div class="col-sm-2 text-right">
						<label class=" control-label" >Customer Service Phone</label>
					</div>
					<div class="col-sm-4">
						<input type="tel" class="form-control" id="customer_service_phone" placeholder="Customer Service Phone" name="customer_service_phone"  value="<?php //echo (isset($user_store->customer_service_phone) && $user_store->customer_service_phone)?$user_store->customer_service_phone:""?>" />
					</div>
					<div class="col-sm-2 col-xs-3 text-right ">
						<label class=" control-label" >Customer Service Email</label>
					</div>
					<div class="col-sm-4 col-xs-5">
						<input type="email" class="form-control" id="customer_service_email" placeholder = "Customer Service Email" name="customer_service_email" value="<?php //echo (isset($user_store->customer_service_email) && $user_store->customer_service_email)?$user_store->customer_service_email:""?>" />
					</div>
				</div>
				<div class="row form-group">
                	<div class="col-sm-12">
                    	<input type="hidden" value="<?php //echo (isset($user_store->s_id) && $user_store->s_id)?$user_store->s_id:"";?>" name="s_id"  /> 
                        <input type="hidden" value="<?php //echo (isset($user_store->store_logo) && $user_store->store_logo)?$user_store->store_logo:"";?>" name="pre_store_logo"  /> 
                		<input type="submit" value="<?php echo (!empty($user_store))?"Update Store":"Save Store"?>" class="btn btn-primary pull-right"  />
                	</div>
                </div>
			</div>
	</form>
</div>
<script>/*
	$(document).ready(function(e) {
        $('#country_id').select2({width: 'resolve'});
    });
	$("#myform").validate({
		rules: {
			store_name:{
				required:true,
				remote: {
					url: '<?php echo base_url("admin/dashboard/check_store_exist")?>',
					type: "post",
					data:{s_id:'<?php echo (isset($user_store->s_id) && $user_store->s_id)?$user_store->s_id:"0";?>'}
				}
			},
			store_address:{
				required:true
			},
			legal_busniess_type:{
				required:true
			},
			country:{
				required:true
			},
			contact_person:{
				required:true
			},
			contact_phone:{
				required:true
			},
			contact_email:{
				required:true
			},
			customer_service_phone:{
				required:true
			},
			customer_service_email:{
				required:true
			}
		},
		messages:{
			store_name:{
				required: "Please Enter Store Name.",
				remote: "Store Already Exist."
			},
			store_address:{
				required: "Please Enter Store Address."
			},
			legal_busniess_type:{
				required: "Please Select Busniess Type."
			},
			country:{
				required: "Please Select Country."
			},
			contact_person:{
				required: "Please Enter Contact Person."
			},
			contact_phone:{
				required: "Please Enter Contact Phone."
			},
			contact_email:{
				required: "Please Enter Contact Email."
			},
			customer_service_phone:{
				required: "Please Enter Customer Service Phone."
			},
			customer_service_email:{
				required: "Please Enter Customer Service Email."
			}
		},
		submitHandler: function (form) {
			$('.btn').remove();
		 	$("#preloadView2").show();
			form.submit();
		}
	});
	$('#store_name').keyup(function() {
		var dInput = this.value;
		dInput = dInput.replace(/ /g,"-");
		dInput = dInput.toLowerCase();
		$("#store_id").text(dInput);
	});
	// Get the modal
	var modal = document.getElementById('myModal');
	
	// Get the image and insert it inside the modal - use its "alt" text as a caption
	var img = document.getElementById('myImg');
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");
	img.onclick = function(){
		modal.style.display = "block";
		modalImg.src = this.src;
		captionText.innerHTML = this.alt;
	}
	
	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];
	
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() { 
		modal.style.display = "none";
	}
	$(function () {
		$(":file").change(function () {
			if (this.files && this.files[0]) {
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
	});
	
	function imageIsLoaded(e) {
		$('#myImg').attr('src', e.target.result);
	};
	$("#profileImage").click(function(e) {
		$("#imageUpload").click();
	});*/
</script>