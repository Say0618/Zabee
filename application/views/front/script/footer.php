<!-- <div id="breaze_support" data-icon="https://web.breaze.co/assets/img/support_startup//6.jpg" data-title="Support" data-team="94a0a907715b3a1a" data-company="a99538907c865ff3" data-cname="QA Company" data-username="QA Admin" data-profile="https://web.breaze.co/repository/support_widget/support_logo_a99538907c865ff3.jpg?1566891303469" data-icon-border="#000093" data-chat-border="#3ce11e" data-title-color="#ffffff" data-head-color="#004080"></div>
<script src="https://web.breaze.co/assets/js/widget.js"></script> -->
<script src="<?php echo assets_url('front/js/popper.min.js'); ?>"></script>
<script type="application/javascript" src="<?php echo assets_url('common/bootstrap/js/bootstrap.min.js') ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/jquery-validation/jquery.validate.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/jquery-validation/additional-methods.js'); ?>"></script>
<script type="text/javascript" src="<?php echo assets_url('plugins/mCustomScrollbar.min.js')?>"></script>
<!-- <script type="text/javascript" src="<?php //echo assets_url('plugins/form-select2/select2.min.js'); ?>"></script> -->
<script type="text/javascript" src="<?php echo assets_url('front/js/scripts.js'); ?>"></script>
<script src="<?php echo assets_url('common/js/jquery-ui.min.js'); ?>"></script> 
<script type="application/javascript" src="<?php echo assets_url('plugins/lightslider-master/js/lightslider.js'); ?>"></script>
<script src="<?php echo assets_url("front/js/flyto.js")?>"></script>
<?php if(!$this->isloggedin && ($page_name=="login" || $page_name=="join_us")){?>
	<script src="https://apis.google.com/js/api:client.js"></script>
<?php }?>
<?php
$add_password=$this->session->flashdata('add_password');
?>
<script>
  (function (s, e, n, d, er) {
    s['Sender'] = er;
    s[er] = s[er] || function () {
      (s[er].q = s[er].q || []).push(arguments)
    }, s[er].l = 1 * new Date();
    var a = e.createElement(n)
,
        m = e.getElementsByTagName(n)[0];
    a.async = 1;
    a.src = d;
    m.parentNode.insertBefore(a, m)
  })(window, document, 'script', 'https://cdn.sender.net/accounts_resources/universal.js', 'sender');
  sender('5b1387eece2780')
</script>
<script>
var pageSegmentC = '<?php echo $this->uri->segment(1, 0); ?>';
var pageSegmentM = '<?php echo $this->uri->segment(2, 0); ?>';
var redirect = "<?php echo base_url();?>";
if(pageSegmentC == "login"){
	redirect = "<?php echo (isset($_SERVER['HTTP_REFERER'])) ?$_SERVER['HTTP_REFERER'] : ''; ?>";
}else{
	redirect = pageSegmentC+"/"+pageSegmentM;
}
var inputWidth = $("#input-search").width();  
$("#myDropdown").css("min-width",inputWidth);
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}
$("#myDropdown a").on("click",function(){
	$(".dropbtn").text($(this).text());
	$("#search_cat_id").val($(this).attr("data-id"));
	document.getElementById("myDropdown").classList.toggle("show");
})
function filterFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}
$("#search-button").on("click",function(){
	searchProducts();
});
$('#search-bar').bind('keypress', function(e) {
    if (e.keyCode == 13){
		searchProducts();
    } 
});

function searchProducts(){
	var search_cat_id = $("#search_cat_id").val();
	var text_search = $("#search-bar").val();
	var param = $("#current-url").val();
	var cat_name ="searchResult";
	if($("#input-search .dropbtn").length > 0){
		var cat_name = $("#input-search .dropbtn").html().toLowerCase();
		cat_name = cat_name != "" ? cat_name : "searchResult";
	}
	var path = "<?php echo base_url()?>"+cat_name;
	if(text_search !=""){
		if(param != ""){
			param = removeSearchParam(param);
			path = path+"?"+param+"&search="+text_search;
			if(path.includes("searchResults?&") == true){
				path = path.replace("searchResults?&", "searchResults?");
			}else if(path.includes(cat_name+"?&") == true){
				path = path.replace(cat_name+"?&", cat_name+"?");
			}
			if(path.includes("&&") == true){
				path = path.replace("&&", "&");
			}
		}else{
			path = path+"?search="+text_search;
		}
		// if(search_cat_id !=""){
		// 	path = path+"&category_search="+search_cat_id
		// }
		location.href = path;
		console.log('path is ...................', path);
	}
}
function removeSearchParam(param){
	var query = '';
	param = param.split('&');
	for (let index = 0; index < param.length; index++) {
		if(!param[index].startsWith('search')){
			query += param[index]+"&";
		}
	}
	return query;
}
          
$(function() {
    $('.dropdown-menu').addClass('dropright');
    $('a.dropdown-t').on("mouseover", function(e) {
        var submenu = $(this);
        submenu.next('.dropdown-menu').addClass('show');
        e.stopPropagation();
    });
    $('li.nav-item').on("mouseleave", function(e) {
        var submenu = $(this);
        // hide any open menus when parent closes
        submenu.parent().find('.dropdown-submenu > .dropdown-menu').removeClass('show');
        e.stopPropagation();
    }); 
});
$("#search_form").validate({
 	rules: {
		search: {
			required: true,
		}
	},
	 messages: {
		search: {
			required: 'Please enter some text to search.',
		}
	 },
	 errorPlacement: function (error, element) {
		error.appendTo(element.parent().parent());
		}
});
function dealEmailProvided() {
    return $('#deals_sub_email').val().length == 0;
}
function dealPhoneProvided() {
    return $('#deals_sub_phone').val().length == 0;
}
$("#dealsSubscriptionForm").validate({
 	rules: {
		deals_sub_name: {required: true},
		deals_sub_email: {required: dealPhoneProvided, email:true},
		deals_sub_phone: {required: dealEmailProvided, phoneUS:true}
	},
	 messages: {
		deals_sub_name: {required: 'Full name is required.'},
		deals_sub_email: {required: 'Email or phone number is required.', email: 'Valid email address is required'},
		deals_sub_phone: {required: 'Email or phone number is required.', phoneUS: 'Valid mobile number is required'}
	 }
});


function closedDealsSignUp(){
	ajaxRequest([],'<?php echo base_url('dealsSubscription/no'); ?>', function(){
		$('#dealsSubscription').modal('hide');
	});
}
var server = '<?php echo base_url(); ?>';
var result = {};
var email;
var googleUser = {};
$(document).ready(function () {
	$( document ).ajaxSend(function( event, jqxhr, settings ) {
    	if (settings.url != "<?php echo base_url('minicart');?>" && settings.url !="<?php echo base_url("chatbot/check_user_login");?>" ) {
			$( "#loading-background" ).removeClass("d-none");
			$( ".boxes" ).removeClass("d-none");
		}
	});

	$(document).ajaxStop(function(){
		$( "#loading-background" ).addClass("d-none");
		$( ".boxes" ).addClass("d-none");
	});
	$('#deals_selection_all').click(function(){
		$('input[name="deal_cat_ids[]"]').prop('checked', false);
		$('.deals_cat_list').addClass('d-none');
	});
	$('#deals_selection_manual').click(function(){
		$('.deals_cat_list').removeClass('d-none');
	});
	$('input[name="deal_cat_ids[]"]').click(function(){
		if($('#deals_selection_manual').is(':checked')) {}else {$('#deals_selection_manual').prop('checked', true);}
	});
	<?php if($add_password) { ?>
		$('#add_password').modal('show');
	<?php } ?>
	$("#sidebar").mCustomScrollbar({
		theme: "minimal"
	});
	$("#minicart-sidebar").load("<?php echo base_url('minicart')?>");
	$('#dismiss').on('click', function () {
		// hide sidebar
		$('#sidebar').removeClass('active');
	});

	$('#sidebarCollapse').on('click', function () {
		// open sidebar
		$('#sidebar').addClass('active');
		// fade in the overlay
		$('.overlay').addClass('active');
		$('.collapse.in').toggleClass('in');
		$('a[aria-expanded=true]').attr('aria-expanded', 'false');
	});
	
	$("#account-sidebar").mCustomScrollbar({
		theme: "minimal"
	});
	
	$('#accountDismiss').on('click', function () {
		// hide sidebar
		$('#account-sidebar').removeClass('active');
		// hide overlay
	});

	$('#accountSidebarCollapse,#accountsidebarCollapse2').on('click', function () {
		// open sidebar
		$('#account-sidebar').addClass('active');
		// fade in the overlay
		$('.collapse.in').toggleClass('in');
		$('a[aria-expanded=true]').attr('aria-expanded', 'false');
	});

	//Minicart SideBar
	$("#minicart-sidebar").mCustomScrollbar({
		theme: "minimal"
	});
	$("header").on('click','#minicartDismiss', function () {
		// hide sidebar
		$('#minicart-sidebar').removeClass('active');
		// hide overlay
	});
	$('#minicartSideBarCollapse,#minicartSideBarCollapse2').on('click', function () {
		// open sidebar
		$('#minicart-sidebar').addClass('active');
		// fade in the overlay
		$('.collapse.in').toggleClass('in');
		$('a[aria-expanded=true]').attr('aria-expanded', 'false');
	});
	//End
	$(document).mouseup(function (e){
		var container = $("#account-sidebar");
		if (!container.is(e.target) && container.has(e.target).length === 0){
			$('#account-sidebar').removeClass('active');
		}
		var container2 = $("#sidebarCollapse");
		if (!container2.is(e.target) && container2.has(e.target).length === 0){
			$('#sidebar').removeClass('active');
		}
		var container3 = $("#");
		if (!container3.is(e.target) && container3.has(e.target).length === 0){
			$('#minicart-sidebar').removeClass('active');
		}
		var container4 = $("#filter-sidebar");
		if (!container4.is(e.target) && container4.has(e.target).length === 0){
			$('#filter-sidebar').removeClass('active');
		}
	}); 

	$('#myModal3').on('show.bs.modal', function() {
		$('#alreadyExistingCategories').prop('selectedIndex',0);
	});

	$( "#categorySubmit" ).click(function() {
		prd_id = $("#myModal3 #modal_product_id").val();
		cat_name = $("#myModal4 #catNameInput #cat_name").val();
		prd_v_id = $("#myModal3 #modal_product_v_id").val();
		// console.log(prd_id+" "+cat_name+" "+prd_v_id);
		// return false;
		if(cat_name != ""){
			$.ajax({
				type: "POST",
				url: "<?php echo base_url()?>home/add_wishlist_category",
				dataType: "json",
				cache:false,
				data: $('form#myform').serialize(),
				success: function(response){
					$("#myModal4 #catNameInput #cat_name").val("");
					$('#myModal3').modal('hide');
					$('#myModal4').modal('hide');
					$('#change-message').text("");
					if(prd_id == ""){
						$('#change-message').text("New Category Added Successfully");
					}else{
						$('#change-message').text("Product saved for later");
					}
					$('#message-notification').modal('show');
					setTimeout(function() {
						$('#message-notification').modal('hide');
						}, 4000);
						if(pageSegmentC == "product"){
							var counter = $('.addToWishlistBtn[data-id = '+prd_id+'-'+prd_v_id+'] span').text();
							counter = (counter != "") ? '<span class="fs-14">&nbsp'+(parseInt(counter) + 1)+'</span>' : "";
							var heart = (counter == "") ? 'btn' : "";
							$('.addToWishlistBtn[data-id = '+prd_id+'-'+prd_v_id+']').replaceWith('<span class="already-saved '+heart+'" data-toggle="tooltip" title="Already Saved"><i class="fa fa-heart"></i></span>'+counter);
						}else{
							$('.addToWishlistBtn[data-id = '+prd_id+'-'+prd_v_id+']').replaceWith('<span class="already-saved btn <?php echo (isset($_SESSION['view']) && ($_SESSION['view'] == "list"))?"col-2 pl-1":"btn-left";?>" data-toggle="tooltip" title="Already Saved" ><i class="fa fa-heart"></i></span>');
						}
					if(response.data){
						$(".alreadyExistingCategories option[value='0']").remove();
						$(".alreadyExistingCategories option[value='1']").remove();
						if(prd_id == ""){
							parent = "<div>";
							var classes ="list-group-item category-list text-capitalize";
							if(response.data.category_name != "like"){
								classes = "list-group-item category-list text-capitalize ml-4";
								parent += '<button type="button" class="position-absolute delete-wish border-0 bg-transparent text-danger" value="'+response.data.id+'"><i class="fa fa-window-close red"></i></button>';
							}
							$("#wishlist").append(parent+'<a href="<?php echo base_url() ?>product/saved_for_later/'+response.data.id+'" class="'+classes+'">'+response.data.category_name+'<span class="badge customBage">0</span></a></div>')
						}
					}
				},
				error: function(){
					alert("Error");
				}
			});
		}else{
			$("#myModal4 #cat-error").text("Please insert a valid category name")
		}
	});
	<?php if($this->session->flashdata('check_for_email')): ?>
		document.getElementById("myModal").style.display = "display";
		$("#myModal").modal('show');
	<?php endif; ?>
	<?php if($this->session->flashdata('newsletter_subscribe')): ?>
		document.getElementById("myModal2").style.display = "display";
		$("#myModal2").modal('show');
	<?php endif; ?>
	$("input[type='text']").attr("maxLength","255");
		$("input[type='email']").attr("maxLength","255");
		$(".img-fluid").on("error", function(){
			$(this).attr('src', "<?php echo assets_url('front/images/Preview.png')?>");
		});
	$('[data-toggle="tooltip"]').tooltip(); 
		$.validator.addMethod("email", function(value, element) {
			if(isValidEmailAddress(value)){
			return this.optional( element ) || true;
			} else {
			return this.optional( element )|| false;
			}
	}, "Please enter a valid email.");
	$("#login_form").validate({
		rules: {
		user_email: {
		
		nowhitespace:true,
		required: true,
		email: true
		},
		user_pass: {
			required: true,
			nowhitespace:true
		}
		},
		messages: {
		email: {
			required: 'email address required.',
			email: 'Please enter a <em>valid</em> email address.',
		}
		}
		});
	$(window).scroll(function(){
		if($(this).scrollTop() > 20){
			$('.scroll').fadeIn();
			$('.BTT_button').show();
			$('.BTT_button').css('background-color','#333');
		}else{
			$('.scroll').fadeOut(); 
			$('.BTT_button').hide();
		}
	});
	$('.scroll').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 600);
		return false;
	});
	$("#myLoginBtn").click(function(){
		$("#myModal").modal();
	});
});
	<?php if(!$this->isloggedin && ($page_name=="login" || $page_name=="join_us")){?>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
		function statusChangeCallback(response, data) {
			data = typeof data !== 'undefined' ? data : 0;
			if (response.status === 'connected') {
				if(data == 1){
					testAPI(response);
				}
			}
		}
		function checkLoginState() {
			FB.getLoginStatus(function(response) {
				statusChangeCallback(response,1);
			});
		}
		window.fbAsyncInit = function() {
			FB.init({
				appId      : '<?php echo $this->config->item('fb_app_id'); ?>',
				cookie     : true, 
				xfbml      : true,
				version    : 'v2.8'
			});
			FB.getLoginStatus(function(response) {
				statusChangeCallback(response);
			});
		};
		function fblogin() {
			if(typeof(FB) !== "undefined"){
				FB.login(function(response) {
					statusChangeCallback(response,1);
				},{scope: 'public_profile,email',auth_type: 'rerequest'});
			}else{
				$(".btn-face").parent().append("<div class='error text-left'>Sorry, there was a problem connecting to Facebook. sign in with your email address.</div>")
			}
		}
		function testAPI(data) {
			FB.api('/me?fields=first_name,last_name,email,name', function(response) {
				result = response;
				if(typeof(response.email) !== "undefined" && response.email != ""){
					res = JSON.stringify(response);
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url(); ?>home/fb_account_exist',
						data: {data:res,platform:"fb"},
						dataType: 'json',
						success: function(data){
							if(data.status == 1){
								if(pageSegmentC == 'checkout'){
									window.location.href = '<?php echo base_url("checkout"); ?>';
								} else {
									window.location.href = document.referrer;
								}
							}
						}
					});	
				}
				else{
					res = JSON.stringify(response);
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url(); ?>home/user_exists',
						data: {data:res,platform:"fb"},
						dataType: 'json',
						success: function(data){
							if(data.status == 1){
								window.location.href = '<?php echo base_url(); ?>';
							}
							else
							{
								$('#fbmodal').modal('show');
							}
						}
					});	

				}
			});
			}
		function fb_req(){
			email = $('#req_email').val();
			if (email !="") {
				result.email = email;
				result = JSON.stringify(result);
				console.log(result);
				//return false;
				$.ajax({
					type: 'POST',
					url: '<?php echo base_url(); ?>home/fb_account_exist',
					data: {data:result,platform:"fb"},
					dataType: 'json',
					success: function(data){
						if(data.status == 1){
							window.location.href = '<?php echo base_url(); ?>';
						}
					}
				});				
			}
			else{
				setTimeout(function(){ $('#fbmodal').modal('show'); }, 1000);
			}
		}
	<?php }?>
	function formatUTCAMPM(date) {
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0'+minutes : minutes;
		var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
		var strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
		return strTime;
	}
	var from = atob("<?php print_r(isset($_GET['from'])?$_GET['from']:""); ?>");
	if(from != "" && window.location.href.indexOf("login") < 1){
		from = from.split('-');
		var date = new Date();
		var n = date.toDateString();
		var time = date.toLocaleTimeString();
		var dt = n + ' ' + time;
		var datetime = dt;
		if(from[2] !=""){
			$.ajax({	
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>product/save_for_later_2",
				data:{"user_id":from[3],"product_id":from[1],"product_variant_id":from[2],"created_date":datetime},
				success: function(response){
					if(response.status == 1){	
						$('#myModal3').modal('show');
						$("#myModal3 #modal_product_id").val(from[1]);
						$("#myModal3 #modal_product_v_id").val(from[2]);
					}
				}
			});
		}
	}
	
	$(document).on('click','.addToWishlistBtn',function(){
		var product_variant_id = $(this).attr("data-product_variant_id");
		if(product_variant_id == undefined){
			var product_variant_id = $(this).attr("data-product_varient_id");
		}
		var product_id = $(this).attr("data-product_id");
		var buttonObj = $(this);
		var date = new Date();
		var n = date.toDateString();
		var time = date.toLocaleTimeString();
		var dt = n + ' ' + time;
		
		var user_id = "<?php  echo (isset($_SESSION['userid']))?$_SESSION['userid']:""?>";
		if(user_id == ""){
			window.location = "<?php echo base_url('login?from=');?>"+btoa('wish-'+product_id+"-"+product_variant_id+"-"+user_id);
			return false;
		}
		var datetime = dt;
		if(product_variant_id !=""){
			$.ajax({	
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>product/save_for_later_2",
				data:{"user_id":user_id,"product_id":product_id,"product_variant_id":product_variant_id,"created_date":datetime},
				success: function(response){
					if(response.status == 1){	
						$('#myModal3').modal('show');
						$("#myModal3 #modal_product_id").val(product_id);
						$("#myModal3 #modal_product_v_id").val(product_variant_id);
					}
				}
			});
			return false;
		}
	});
	<?php if(!$this->isloggedin && ($page_name=="login" || $page_name=="join_us")){?>
		//Google SignIn
		var startApp = function() {
			gapi.load('auth2', function(){
				// Retrieve the singleton for the GoogleAuth library and set up the client.
				auth2 = gapi.auth2.init({
					client_id: '<?php echo $this->config->item('gmail_client_id');?>',
					cookiepolicy: 'single_host_origin',
					// Request scopes in addition to 'profile' and 'email'
					//scope: 'additional_scope'
				});
				attachSignin(document.getElementById('googleLogin'));
			});
		};
		function attachSignin(element) {
			auth2.attachClickHandler(element, {},
			function(googleUser) {
				var profile  = googleUser.getBasicProfile();
				res = {
					El:profile.getId(),
					email:profile.getEmail(),
					firstname:profile.getGivenName(),
					lastname:profile.getFamilyName(),
					social_id:profile.getId(),
					social_info:profile,
				};
				$.ajax({
					type: 'POST',
					url: '<?php echo base_url(); ?>home/google_account_exist',
					data: {data:JSON.stringify(res),platform:"google"},
					dataType: 'json',
					success: function(data){
						//console.log(data); return false;
						if(data.status == 1){
							if(pageSegmentC == 'checkout'){
								window.location.href = '<?php echo base_url('checkout'); ?>'
							}else{
								window.location.href = document.referrer;
							}
						}
						if(data.status == 2){
							$('#add_password').load('<?php echo base_url("home/set_password_view")?>', {el:data.EL, page:pageSegmentC}, function(){
								$("#add_password").modal('show');
							});
						}
						if(data.status == 3){
							//alert();
							window.location.href = document.referrer;
						}
						else{
							//window.location.href = 'google.com';
						}
					}
				});	
				}, function(error) {
				console.log(JSON.stringify(error, undefined, 2));
			});
		}
		startApp();
	<?php }?>
	// Cart
	function deleteFromCart(item_id, isRedirect,action){
		var data = {id:item_id};
		var url = server+'cart/delete/'+item_id+'/'+action;
		$.ajax({
			cache:false,
			dataType: "JSON",
			async:true,
			url: url,
			success: function(response){
				if(isRedirect){
					window.location.href = '<?php echo base_url('cart'); ?>';
				}else{
					if(response.total_items == 0){
						$("#minicartSideBarCollapse").attr("href","<?php echo base_url("cart")?>");
						$("#minicartSideBarCollapse2").attr("href","<?php echo base_url("cart")?>");
					}
					$("#minicart-sidebar").load("<?php echo base_url('minicart')?>");
				}
			}
		});
		/*ajaxRequest(data, url, function(data){
			console.log(data.total_items);
			$("#minicart-sidebar").load("minicart");
		});*/
		return false;
	}
	$(document).on('click','.addToCartBtn',function(){
		var pv_id = $(this).attr('data-product_variant_id');
		var allShippingIds = $(this).attr('data-available_shipping_ids'); 
		var redirect = $(this).attr('data-redirect'); 
		var maxQty = parseInt($("#qty").val());
		var qty = parseInt($("#product_qty").val());
		var shipping_data = "";
		shipping_id = $(".shipping_method:checked").attr('data-shipping_id');
		shipping_title = $(".shipping_method:checked").attr('data-title');
		shipping_price = $(".shipping_method:checked").attr('data-price');
		if(shipping_id && shipping_title && shipping_price){
			shipping_data = {'shipping_id':shipping_id,'title':shipping_title,'price':shipping_price,'allShippingIds':allShippingIds};
		}
		if(qty && maxQty){
			console.log("qty");
			if(qty > maxQty){
				if(maxQty == 1){
					$("#qty-error").text('Quantity must be 1');
				}else{
					$("#qty-error").text('Quantity must be less than or equal to '+maxQty);
				}
				return false;
			}
		}else{
			qty = 1;
		}
		//return false;
		if(qty && qty !=0){
			$.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url()?>cart/addtocart",
				data:{"pvid":pv_id,"qty":qty,'is_ajax':true,'shipping':shipping_data},
				success: function(response){
					if(response.status == 1 && !redirect){
						$("#minicart-sidebar").load("minicart");
						<?php if($this->cart->total_items() == 0){?>
							$(".sidebar-dropdown-toggle").attr("href","javascript:void(0)")
						<?php }?>
					}else{
						location.href = "<?php echo base_url('cart');?>";
					}
				}
			});
		}else{
			$("#qty-error").text('Quantity must be 1');
			return false;
		}
	});
	$('.itemsDiv').flyto({
		item      : '.itemsDiv',
		target    : '.cartBtn',
		button    : '.addToCartBtn'
	});
	/*$('.itemsDiv').flyto({
		shake: true
	});*/
	/*$('.wizard li').click(function() {
		$(this).prevAll().addClass("completed");
		$(this).nextAll().removeClass("completed")
	});*/
</script>
<!-- Start of Zendesk Widget script -->
<!-- <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=f95a7d08-c544-49bc-b8c0-f556063903cc"> </script> -->
<!-- End of Zendesk Widget script -->
<script>
	$("#filter-sidebar").mCustomScrollbar({
		theme: "minimal",
		direction: "rtl"
	});
	
	$('#filterDismiss').on('click', function () {
		// hide sidebar
		$('#filter-sidebar').removeClass('active');
		// hide overlay
	});
	$('#filterSidebarCollapse').on('click', function () {
		// open sidebar
		$('#filter-sidebar').addClass('active');
		// fade in the overlay
		$('.collapse.in').toggleClass('in');
		$('a[aria-expanded=true]').attr('aria-expanded', 'false');
	});
</script>
<?php if(ENVIRONMENT == "production"){?>
	<script type="text/javascript">
		window.omnisend = window.omnisend || [];
		omnisend.push(["accountID", "5f33215b99f0b70fa4ba80ab"]);
		omnisend.push(["track", "$pageViewed"]);
		!function(){var e=document.createElement("script");e.type="text/javascript",e.async=!0,e.src="https://omnisrc.com/inshop/launcher-v2.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)}();
	</script>
	<script type="application/ld+json">
		{
		"@context": "https://schema.org",
		"@type": "Organization",
		"url": "<?php echo base_url();?>",
		"logo": "<?php echo website_img_path('logo.png');?>"
		}
	</script>
	<script type="application/ld+json">
		{
		"@context": "https://schema.org",
		"@type": "WebSite",
		"url": "<?php echo base_url();?>",
		"potentialAction": [{
			"@type": "SearchAction",
			"target": "<?php echo base_url('/searchResults?search={search_term_string}')?>",
			"query-input": "required name=search_term_string"
		}]
		}
	</script>
<?php }?>