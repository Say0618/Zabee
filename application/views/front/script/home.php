<script>
$("#saveEmailforNewsandOffers").validate({
	rules: {
		email_newsandoffers:{
			required: true,
			email: true
		},
	},
	messages:{
		email_newsandoffers:{
			required: "Please Enter Your Email."
		},
	}
});
$(document).ready(function(e) {
	/*var dealsSubscription = getCookie('deals_signup');
	if(dealsSubscription == '')
	$('#dealsSubscription').modal('show');*/
	 // featured
	$('.more-feature-next').click(function(){ $('.more-from-this-features').carousel('next');return false; });
    $('.more-feature-prev').click(function(){ $('.more-from-this-features').carousel('prev');return false; });
    // top rated
    $('.more-seller-next').click(function(){ $('.more-from-this-seller').carousel('next');return false; });
    $('.more-seller-prev').click(function(){ $('.more-from-this-seller').carousel('prev');return false; });
    
    //all Products 
    $('.product-accessories-next').click(function(){ $('.product-accessories').carousel('next');return false; });
	$('.product-accessories-prev').click(function(){ $('.product-accessories').carousel('prev');return false; });
	
	$(".show_on_homepage").each(function(index,value){
		divClass = "."+$(value).attr("data-class");
		prevBtn  = "."+$(value).attr("data-prev");
		nextBtn  = "."+$(value).attr("data-next");
		homepage_btn(divClass,prevBtn,nextBtn);
	});
	function homepage_btn(divClass,prevBtn,nextBtn){
		$(nextBtn).click(function(){ $(divClass).carousel('next');return false; });
		$(prevBtn).click(function(){ $(divClass).carousel('prev');return false; });
	}
	$(".rateYo").rateYo({
		rating: 3,
		starWidth: "16px",
		readOnly: true,
		multiColor: {
			"startColor": "#8482cb",
			"endColor"  : "#8482cb"
		}
	});
	$('[data-toggle="tooltip"]').tooltip();  
});	
$('.bannercarousel').carousel({
	  interval: 2000
});
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>