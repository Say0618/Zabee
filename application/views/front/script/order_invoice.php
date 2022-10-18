<link rel="stylesheet" type="text/css" href="<?php echo assets_url('front/css/invoice.css')?>" >
<script>
	$(document).ready(function(){
		var date = $("#invoice_date").text();
		date = date.replace(/-/g,'/')
		date = new Date(date+" UTC");
		date =  new Date(date.toString());
		var hours = date.getHours();
		var minutes = date.getMinutes();
		var ampm = hours >= 12 ? 'PM' : 'AM';
		hours = hours % 12;
		hours = hours ? hours : 12; // the hour '0' should be '12'
		minutes = minutes < 10 ? '0'+minutes : minutes;
		var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var strTime = months[date.getMonth()]+' '+date.getDate()+', '+date.getFullYear()+' '+hours + ':' + minutes + ' ' + ampm;
		$("#invoice_date").text(strTime);
	});
	$(".order_time").each(function(){ 
		var date = $(this).text();
		date = date.replace(/-/g,'/')
		date = new Date(date+" UTC");
		date =  new Date(date.toString());
		$(this).text(formatAMPM(date));
	});
		
</script>
