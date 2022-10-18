<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="<?php echo assets_url('plugins/ckeditor/ckeditor.js');?>"></script>
<div class="container">
<h2 class="mt-2">Terms and Conditions</h2>
<?php $text =  $TermsAndConditions['result'][0]['T&C']; print_r($text)?>
  <textarea cols="80" id="editor1" name="editor1" rows="10" value="<?php echo htmlspecialchars_decode($text);?>" data-sample-short></textarea>
  <button type="submit" class="btn btn-secondary mt-2 mb-2" id="submit-terms">submit</button>
  </div>
  <script>
    CKEDITOR.replace('editor1', {
      height: 170,
      width: 950,
    });
    
    $(document).on('click','#submit-terms',function(){
    var data = CKEDITOR.instances.editor1.getData(); 
    var id = <?php echo $_SESSION['userid'];?> 
    $.ajax({
				type: "POST",
				cache:false,
				dataType: "JSON",
				async:true,
				url: "<?php echo base_url('seller/terms_and_conditions_add/saveTerms');?>",
				data:{'terms':data,'user_id':id},
				success: function(response){
          window.location.href="<?php echo base_url('seller/terms_and_conditions_add')?>"
        }
    });  
});
      </script>