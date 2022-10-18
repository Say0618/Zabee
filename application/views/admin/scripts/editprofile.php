<script>
jQuery.validator.addMethod("emailAddress", function(value, element) 
{
	return this.optional(element) || /^(([^<>()[\]\\.,;:\s@\/"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(value);
}, "Email Address Not Valid");
jQuery.validator.addMethod("pattername", function(value, element) 
{
	return this.optional(element) || /^[a-zA-Z_]*$/.test(value);
}, "Invalid input");
jQuery.validator.addMethod("NamePattern", function(value, element) 
{
	return this.optional(element) || /^[a-zA-Z]+$/.test(value);
}, "Invalid input"); 
jQuery.validator.addMethod("FirstLetter", function(value, element) 
{
	return this.optional(element) || /^\S.*/.test(value);
}, "First letter cant be a space");
jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters"); 
$("#myform").validate({
	rules:{
		yourpublicname: {
			minlength: 3,
			maxlength: 30,
			lettersonly: true,
			FirstLetter: true,
			required: true
		},
		website: {
			required: true,
			url: true
		},
		email: {
			required: true,
			emailAddress: true
		},
		bio: {
			required: true
		},
	},
	messages: {
		Yourpublicname: {
			required: "Your public name is required"
		},
		website: {
			required: "Your website's url is required",
			url: "Proper url required"
		},
		Email: {
			required: "Your email address is required",
			email: "Incorrect email address"
		},
		Bio: {
			required: "Tell us a little about yourself"
		},
		occupation: {
			required: "Your occupation is required"
		}
	},
		submitHandler: function(form){
			$('#preloadView2').show();
			form.submit();
		}	
});
// Get the modal
var modal = document.getElementById('myModal');

// Get the image and insert it inside the modal - use its "alt" text as a caption
var img = document.getElementById('myImg');
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
img.onclick = function(){
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
}

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() { 
    modal.style.display = "none";
}
$(function () {
    $(":file").change(function () {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = imageIsLoaded;
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function imageIsLoaded(e) {
    $('#myImg').attr('src', e.target.result);
};
$("#profileImage").click(function(e) {
    $("#imageUpload").click();
});

</script>
