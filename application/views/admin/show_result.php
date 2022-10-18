<div class="card-body">
	<?php  if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>
 	<div class="success results">
		<h3>Success:</h3>
		<?php
		if(count($success_import)>0){
			echo '<ul>';
			foreach($success_import as $success){
				echo '<li>'.$success.'</li>';
			}
			echo '</ul>';
		} else {
			echo 'NONE';
		}
		?>
	</div>
	<hr />
 	<div class="error results">
		<h3>Errors:</h3>
		<?php
		if(count($error_import)>0){
			echo '<ul>';
			foreach($error_import as $error){
				echo '<li>'.$error.'</li>';
			}
			echo '</ul>';
		} else {
			echo 'NONE';
		}
		?>
	</div>
</div>

