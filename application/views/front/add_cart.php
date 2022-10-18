	<script>
		$(function() {
			$('#api_call').submit(function(){
				var url = 'http://localhost/web_projects/zabee/cart/addtocart/';
				$.ajax({
					url:url,
					type:'POST',
					dataType:'json',
					async : false,
					crossDomain: true,
					data: $(this).serialize(),
					success:function (data) {
						alert(data.status);
					},
					error:function (xhr, textStatus, errorThrown) {
						console.log(xhr);
					}
				});
				return  false;
			});
		});
	</script>
	<h1>jQuery Ajax!</h1>
	<form name="api_call" id="api_call" method="post" action="http://localhost/web_projects/zabee/cart/addtocart/SHV3YWVpXzM%3D">
		<input type="text" name="pvid" value="1" />
		<input type="text" name="qty" value="1" />
		<input type="submit" name="submit" value="Submit" />
	</form>