<div id="container">
    <form id="my_form" class="form-inline" method="post"  >
		<div class="row">
            <div class="form-group col-sm-4">
                <label for="name" class="col-2 col-form-label">Name:&nbsp </label>
                <div class="col-10">
                    <input type="text" class="form-control" id="name"  name="name" >
                </div>
            </div>  
            <div class="form-group col-sm-4">
                <label for="menu_link" class="col-2 col-form-label">Link:&nbsp </label>
                <div class="col-10">
                    <input type="text" class="form-control" id="menu_link" name="menu_link">
                </div>
            </div>
            <div class="form-group  col-sm-2">
                <select class="form-control"  name="parent" id="parent" style="margin-top: 10px"  >
                    <option value="0">-Select Parent-</option>
                    <?php foreach ($menu as $parent ) {
                        # code...
                    ?>
                      <option value="<?php echo $parent['menu_id']?>"><?php echo $parent['menu_name']?></option>
                    <?php
                    }
                      ?>
                </select>
            </div>
            <div class="form-group  col-sm-2">
                <input id="submit_form" type="submit" value="Submit" name="submit_button"  class="btn btn-primary"  >
            </div>
        </div>
    </form>
    <?php
		//var_dump($menu);
		if(!empty($menu))
		{
		?>
		
		<div class="cf nestable-lists">
		
		  <div class="dd" id="nestable">
		<?php
		function recursive($parent, $menu) {
		  //echo "<pre>"; print_r($menu); echo "</pre>";
		  $has_children = false;
		  $order = 1;
		 // echo $order;
		  foreach($menu as $key => $value)
		   {
			  if ($value['parent_id'] == $parent)
			   {       
				  if ($has_children == false && $parent)
				   {
					  $has_children = true;
					  //$key=$key+1;
					  echo ' <ol class="dd-list" id="accordion'.$value['parent_id'].'">' ."\n";
		
		
					}//var_dump($key);
		?>
					<li class='dd-item' data-parent_id="<?php echo $value['parent_id']; ?>" data-id='<?php echo $value['menu_id'] ?>' id="<?php echo $value['menu_id'] ?>" data-menu_order="<?php echo $value['menu_order']; ?>">
						
						  <div class=' dd-handle <?php echo $value['menu_id']; ?>'><?php echo $value["menu_name"]?></div>
						  
						  
		
		
		  
			<?php
						  $key=$value['menu_id'];
						 // / var_dump($key);
							  recursive($key, $menu);
						  echo "</li>\n";
				}
			}
			$order++;
				   if ($has_children === true && $parent) echo "</ol>\n";
		}
			?>
		 <ol class="dd-list"><?php echo recursive(0, $menu); ?></ol>
		
		<?php
		
		}
		?>
		
		<?php
		if(!empty($menu))
		{
		?>
		
			 <input type="submit" class="btn btn-primary" value="Submit" name="save"   onClick="save();" style="width: 100%" />
		<?php
		}
		else
		{
		?>
				  No data to display
		<?php
		}
		?>
		  </div>
		</div>
</div>

