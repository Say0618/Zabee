<html>
<head>
<style>

.email_template_header{
	background-color: #193560;
	margin-top: 20px;
}

.email_template_body{
	margin-top: 30px;
	color:black
}

.email_template_body_middle_div{
	background-color: #193560;
	margin-top: 20px;
}

.email_template_body_lower_div{
	margin-top: 20px;
}

.email_template_footer{
	background-color: #193560;
	margin-top: 20px;
}

.social_media_logos_for_email{
	width:30px;
	margin-top: 13px;
	margin-left: 22px;
}

.zabee_logo_for_email{
	margin-top: 10px;
	margin-bottom: 7px;
	margin-left: 10px;
}

.heading_color_email{
	color:white !important
}
.heading_color_email_1{
	color:black !important
}
.btn {
    display: inline-block;
    font-weight: 400;
    color: #212529;
    text-align: center;
    vertical-align: middle;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: .25rem;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-primary {
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}
</style>
</head>
<body>
<div class = "container">
	<div class = "row">
		<div class = "col-8">
			<div class = "email_template_header">
					<img class="zabee_logo_for_email" src = "<?php echo website_img_path('LOGO_EMAIL.png'); ?>" />
					<img class="social_media_logos_for_email" src = "<?php echo website_img_path('facebook-icon-18-256.png'); ?>" style="float:right"/>
					<img class="social_media_logos_for_email" src = "<?php echo website_img_path('twitter-2-256.png'); ?>" style="float:right"/>
					<img class="social_media_logos_for_email" src = "<?php echo website_img_path('instagram-icon-18-256.png'); ?>" style="float:right"/>
					<img class="social_media_logos_for_email" src = "<?php echo website_img_path('email_icon.jpg'); ?>" style="float:right"/>
			</div>
			<div class="container">
				<div class = "row email_template_body">
					<h2 class="heading_color_email_1">Dear XYZ, <br />An account already exists with this email address</h2>
					<div class = "row email_template_body_middle_div">
						<div class="container-fluid" style = "padding:10px">
							<h3 class="heading_color_email">You can recover this account if you have forgotten your credentials, or continue to create a new Zab.ee account instead</h3>
							<a class="btn btn-secondary">Find My Account</a>
							<a class="btn btn-primary">Create My Account</a>
						</div>
					</div>
					<div class = "row email_template_body_lower_div">
						<div class="container-fluid" style = "padding:10px">
							<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
							<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
						</div>
					</div>
				</div>
			</div>
			<div class = "row email_template_footer">&nbsp;</div>
		</div>
	</div>
</div>
</body>
</html>