
<script>
 $(window).on('load', function () {
	var sz = $(window).width();
	//alert(sz);
	if(sz == 1263){
		$("#navbarNavDropdown2").css('visibility', 'hidden');
	} else if(sz != 1263){
		$("#navbarNavDropdown2").css('visibility', 'visible');
	}
 });
</script>
