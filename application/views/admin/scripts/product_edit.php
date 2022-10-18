
<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<link rel='stylesheet' type='text/css' href='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.css'); ?>' /> 
<script type='text/javascript' src='<?php echo assets_url('plugins/form-tokenfield/bootstrap-tokenfield.min.js'); ?>'></script> 
<script>
$(document).ready(function(){
		
		$('#product_edit').validate({
		rules: {
			product_name:{
				required: true,
				normalizer: function(value) {
					// Note: the value of `this` inside the `normalizer` is the corresponding
					// DOMElement. In this example, `this` reference the `username` element.
					// Trim the value of the input
					return $.trim(value);
				},
				minlength: 2
			},
			product_description:{
				required: true,
				minlength: 2
			},
		},
		messages: {
			product_name :{
				required: "Please provide product name.",
				minlength: "Must be at least two characters long"},
			product_description :{
				required: "Please provide product description.",
				minlength: "Must be at least two characters long"},
}
});
$('#product_keyword').tokenfield({
			typeahead: {
				name: 'tags',
				local: [],
			}
		});
});

</script>