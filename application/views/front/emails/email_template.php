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
    color: white !important;
    background-color: #007bff;
    border-color: #007bff;
}
</style>
</head>
<body>
<div class = "container">
	<div class = "row">
		<div class = "col-2"></div>
		<div class = "col-8">
			<div class = "row email_template_header">
				<?php $this->load->view('front/emails/email_template_head'); ?>
			</div>
			<div class="container">
				<div class = "row email_template_body">
					<?php $this->load->view('front/emails/'.$page_name); ?>
				</div>
			</div>
			<div class = "row email_template_footer">&nbsp;</div>
		</div>
	</div>
</div>
</body>
</html>