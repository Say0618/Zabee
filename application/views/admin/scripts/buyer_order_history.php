<script>
$(document).ready(function(){
    $(".order_time").each(function(){ 
			var date = $(this).text();
			date = date.replace(/-/g,'/')
			date = new Date(date+" UTC");
			date =  new Date(date.toString());
			$(this).text(formatUTCAMPM(date));
		});
});
</script>
