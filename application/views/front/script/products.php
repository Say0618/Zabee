<script>
	var moreproducts ="";
	var isChanged = false;	
	$(document).ready
	(
		function()
		{			
			getMoreProducts();				
			<?php 
				if(isset($noautoload))echo "g_autoloading = true;";
				else
				echo "startProductAutoLoading();";
			?>
		}
	);
	function startProductAutoLoading()
	{
		stopProductAutoLoading();
		moreproducts = setInterval
			(
				function()
				{
					if(g_gettingmore == false && getScrollTop() >= $(".tab-content").height()-400)
					{
						if(isChanged)
						{
							getMoreProducts(false,isChanged);
						}
						else
						{
							getMoreProducts();
						}
					}
				},1000
			);
	}
	
	function stopProductAutoLoading()
	{
		clearInterval(moreproducts);
	}
	var g_gettingmore = false;
	function getMoreProducts(refresh,addwhere)
	{
		if(refresh != undefined && refresh != false)
		{
			$("#respmesg").remove();
			$("#blockView_ul").html("");
			//$("#listView").html("");
			$("#product_count").val(0);
		}
		if(addwhere == undefined){addwhere = "";}
		$("#loader").show();
		g_gettingmore = true;
		<?php 
			if(isset($addtourl))
			{
				echo "addwhere += '".$addtourl."';";
			}
		?>
		var url = "<?php if(isset($more_products_url))echo $more_products_url; ?>"+$("#product_count").val()+"?"+addwhere;
	//	console.log(url);
		$.ajax
		(
			{
				url : url,
				success : function(data)
						{	
						//	debugger;						
							try
							{
								var response = JSON.parse(data);
							}
							catch (exc)
							{								
								stopProductAutoLoading();
								$("#respmesg").remove();
									if($(".thumbnails li").length == 0)
									{
										$(".normal_list").before("<h4 id = 'respmesg' style='color:red'>"+data+"</h4>");
										//$("#myTab").hide("slow");
									}	
								$("#loader").hide();
								return;
							}
							if(response.status == "success")
							{
								addGridView(response.data);
								//addPagination(response.data);
								//arrangeThumbnails();
								$("#product_count").val(parseInt($("#product_count").val())+9);
								g_gettingmore = false;
								$("#load-more-product").removeClass('d-none');
								//$("#myTab").show("slow");
								//bindaddcart();
							}
							else
							{
								stopProductAutoLoading();
								if(response.data != undefined)
								{
									$("#respmesg").remove();
									if($(".thumbnails li").length == 0)
									{
										$(".normal_list").before("<h4 id = 'respmesg' style='color:red'>"+response.data+"</h4>");
										//$("#myTab").hide("slow");
									}									
								}
								$("#load-more-product").addClass('d-none');
							}
							$("#loader").hide();
						},
				fail : function(data)
						{
							displayalertblock("Cannot Load More Products");
							$("#loader").hide();
						},
			}
		);
	}
	function addGridView(data)	
	{
		var base_url = "<?php echo base_url();?>";
		var product_path = "<?php echo product_path();?>";
		var strHtml = "";
		var img = "";
		var price = "";
		//console.log(data);
		// die();
		$.each(data, function(index, element) {
          	strHtml = "";
			if(element.is_local_product_img == 1){
				img = element.product_image.split(',');
				img = product_path+img[0];
			}else{
				img = element.product_image;
			}
			if(element.max_price > element.min_price){
				price = '$'+element.min_price+'- $'+element.max_price;
			}else{
				price = '$'+element.min_price;
			} 
			strHtml+='<div class="list-group-item">'+
			'<div><div class="col-xs-3">'+
			'<a href='+base_url+'product/detail/'+encodeURIComponent(btoa((element.product_name+"_"+element.product_id)))+' class="text-center" target="_new">'+
			 '<img class="img-thumbnail" src="'+img+'"  title="'+element.product_name+'"/>'+
        '</a>'+'<a href='+base_url+'product/detail/'+encodeURIComponent(btoa((element.product_name+"_"+element.product_id)))+'  class="wordwrap" target="_new">'+
            
        '</a>'+
		 '</div></div>'+
    	'<div class="col-sm-4">'+
		'<a href='+base_url+'product/detail/'+encodeURIComponent(btoa((element.product_name+"_"+element.product_id)))+'  class="wordwrap" target="_new">'+
            '<span>'+element.product_name+'</span>'+
        '</a>'+
		'<div><p class="sellers_name">By:'+element.brand+'</p>'+
		//'<p class="sellers_n ame">'+(element.brand)?"By:"+element.brand:""+'</p>'+
        '<div class="PriceTag">'+
            '<h4>'+price+'</h4>'+
        '</div></div></div>'+
        '<div><div class="col-sm-8 pull-right" style="font-size:12px;">'+
        '<ul></div>';
		var variantKey = Array();
		var conditionKey ="";
		var conditionValue = Array();
		$.each(element.details,function (key, value){ 
			conditionKey = key;
			$.each(value,function(k, v){ 
				conditionValue.push(k);
				if(typeof(v.variant) !=="undefined"){
					$.each(v.variant,function(vk,vv){
						if(vk =="condition"){return true;};
						var array = $.map(vv, function(value, index) {
							return [value];
						});
						if(typeof(variantKey[vk]) !=="undefined"){
							$.each(array, function(vari, vark){
							//$.each(variantKey[vk], function(vari, vark){
								if($.inArray(vark,variantKey[vk]) == -1){
									variantKey[vk].push(vark);
								}
								
							});
							//variantKey[vk] = vv;
						}else{
							//variantKey[vk] = Array();
							variantKey[vk] = array;//implode(', ',$vv);
						}
					});
				}
			});
		strHtml += '<li class="bulletPoints" style="text-transform:capitalize; display:inline-block;">'+conditionKey+': <b style="color: #008cba;">'+conditionValue.join(', ')+'</b></li>';
			for(k in variantKey){
			 if (typeof variantKey[k] !== 'function') {
				var array = $.map(variantKey[k], function(value, index) {
					return [value];
				});
         		strHtml +='<li class="bulletPoints">'+k+': <b style="color: #008cba;">'+array.join(', ')+'</b></li>';
    			}
		
			}
           strHtml +='</ul>'+
        '</div><a href="javascript:void(0)" onclick="submitForm('+element.pv_id+',1)" class="btn addtocart pull-right" id="addToCartBtn"><i class="fa fa-shopping-cart"></i>ADD TO CART</a>'+
    '</div>'+
    '<div class="clearfix"></div>'+
'</div></div></div>';
		$("#blockView_ul").append(strHtml);
        });
		
	});}
	function arrangeThumbnails()
	{
		var maxheight = 0;
		$(".thumbnails li").css("height","auto");
		$(".thumbnails li").each(function(){maxheight = Math.max($(this).height(),maxheight)});
		$(".thumbnails li").css("height",maxheight+"px")
	}	
	var g_autoloading = false;
	function sortProducts(oObj,offautoloading)
	{
		if(g_autoloading == true)offautoloading = g_autoloading;
		if($(oObj).val() == '')
		{
			isChanged = "";
			getMoreProducts(true);
			return false;
		}
		/*var product_price = $("#product_price option:selected").val();
		var product_name = $("#product_name option:selected").val();
		if(product_price != "" && product_name != ""){
			if(name == "product_price"){
				isChanged = 'product_name,min_price='+$(oObj).val()
			}else{
				isChanged = 'min_price,product_name='+$(oObj).val()
			}
		}else{
			isChanged = name+'='+$(oObj).val();
		}*/
		//isChanged = name+'='+$(oObj).val(); 
		isChanged = $(oObj).val(); 
		getMoreProducts(true,isChanged);
		if(offautoloading == undefined)
		{
			startProductAutoLoading();		
		}
		else
		{
			stopProductAutoLoading();
		} 
	}
	function getScrollTop()
	{
		var topscroll = 0;
		if(topscroll == 0)topscroll = $("html,body").scrollTop();
		if(topscroll == 0)topscroll = window.scrollY;
		return topscroll;
	}
	function loadMoreProduct(){
		isChanged = $("#sort_by option:selected").val();
		if(isChanged !=""){ 
			getMoreProducts(false,isChanged);
		}else{
			getMoreProducts();
		}
		return false;
	}
	function submitForm(pv_id,qty){
		$("#pv_id").val(pv_id);
		$("#qty").val(qty);
		var path = "<?php echo base_url('cart/addtocart/'); ?>"+pv_id;
		$("#formSubmit").attr('action',path);
		$("#formSubmit").submit();
		//console.log(path);
	}
</script>
