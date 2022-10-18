<!-- <html>

<body> -->
    <form id="my_form" class="form-inline">
		<label for="name">Name:&nbsp </label>
		<input type="text" class="form-control" id="name"  name="name" />&nbsp
		<select class="form-control"  name="parent" id="parent" style="margin-top: 10px" >
			<option>Select one</option>
			<option value="0">No parent</option>
			<?php foreach ($menu as $parent ) { ?>
			<option value="<?php echo $parent['menu_id']?>" data-count=""><?php echo $parent['menu_name']?></option>
			<?php } ?>
		</select> &nbsp
		<input id="submit_form" type="submit" value="Submit" name="submit_button"  class="btn btn-primary" />
    </form>
    <div class="cf nestable-lists">
		<div class="dd" id="nestable">
			<?php
			if(isset($menu)){

				function recursive($parent, $menu) {
				//echo "<pre>"; print_r($menu); echo "</pre>";
				$has_children = false;
				foreach($menu as $key => $value) {

					if ($value['parent_id'] == $parent) {       
						if ($has_children === false && $parent) {
							$has_children = true;
							echo ' <ol class="dd-list">' ."\n";


						}//var_dump($key);
						?>
						<li class='dd-item' data-id='<?php echo $value['menu_id'] ?>' id="<?php echo $value['menu_id'] ?>">
								<div class='dd-handle'><?php echo $value["menu_name"]?></div>
						 
				 
						<?php
						$key=$key+1;
						//var_dump($key);
							recursive($key, $menu);
						echo "</li>\n";
					}
				}
				if ($has_children === true && $parent) echo "</ol>\n";
				}
			}
			?>
			<ol class="dd-list"><?php echo recursive(0, $menu); ?></ol>
			<input type="submit" class="btn btn-primary" value="Submit" name="save"   onClick="save();" style="width: 100%" />
		</div>
	</div>