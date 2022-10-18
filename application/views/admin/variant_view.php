

<div class="card-body">
	<?php if(isset($_GET['status']) && $_GET['status'] == "error" && $this->session->flashdata("error")){ ?>
    	<div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("error");?>
        </div>
	<?php } else if(isset($_GET['status']) && $_GET['status'] == "success" && $this->session->flashdata("success")){ ?>
    	<div class="alert alert-success" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            	<span aria-hidden="true">&times;</span>
            </button>
          <?php echo $this->session->flashdata("success");?>
        </div>
	<?php } ?>
	
<?php //$this->session->flashdata('info'); ?>

<div class="variantviewBtn"><?php //print_r($variantData);die(); ?>
	<a class="btn btn-primary" href="<?php echo base_url('seller/variantcategories/variant_add/'.$this->uri->segment(4)); ?>"><i class="fa fa-plus" aria-hidden="true"></i> Create <?php echo $data[0]['v_cat_title']?> Variant</a>
</div>
<input type="hidden" value="<?php echo $data[0]['v_cat_title']?>">
<?php if($this->session->flashdata('delete_info') != "" ) { ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong><?php echo $this->session->flashdata('delete_info'); ?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php } ?>
<?php /*if($this->session->flashdata('info') != "" ) { ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong><?php echo $this->session->flashdata('info'); ?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php } */?>
<div class="table-responsive">

	<table cellpadding="0" cellspacing="0" border="0" class="sorted_table table-sm table table-striped table-bordered datatables ">
		<thead>
			<tr>
				<th width="1%">S.No</th>
				<th align="center">Title</th>
				<th align="center"style="width:7%">Active</th>
				<th align="center"style="width:20%"><center>Action</center></th>
			</tr>
		</thead>
		<tbody>

			<?php 
				$i=1;
				if($variantData){
					foreach($variantData as $vd){
						// echo"<pre>";print_r($vd);die();
						$name = (!empty($vd['name1']))?$vd['name1']:'Seller';
						$updatename = "";
						if(!empty($vd['name2'])){
							$updatename = $vd['name2'];
						}else{
							if(!empty($vd['updated_date'])){
								$updatename = "Seller";
							}
						}
						if($vd['is_active'] == "1"){
							$class='checked="checked"';

						}else{
							$class="";
						}
						// $status = ($vd['is_active'])?'Active':'Deactive';
						$status = '<input type="checkbox" class="toggle-two toggle-css" id="toggle'.$vd['v_id'].'" value="'.$vd['is_active'].'" data-catid="'.$vd['v_id'].'" " data-v_cat_id="'.$vd['v_cat_id'].'" data-isactive="" id="category-'.$vd['v_id'].'"  onchange="updatestatus(this)" data-toggle="toggle" data-onstyle="success" data-offstyle="default"  data-style="android" '.$class.' />';
// echo "<input type=\"text\" name=\"activestatus\" value="" class=\"activestatus\">";



						//echo "<tr><td>".$i."</td><td>".$vd['v_title']."</td><td>".$name."</td><td>".$vd['created_date']."</td><td>".$updatename."</td><td>".$vd['updated_date']."</td><td>".$status."</td>";
						echo "<tr><td>".$i."</td><td>".$vd['v_title']."</td><td>".$status."</td>";
						//if(($user_id && $user_id == $vd['created_id']) || $this->session->userdata('user_type') == 1){
							echo "<td><center>

<div class=\"dropdown\">
    <button class=\"btn btn-secondary btn-xs btn-primary\" id=\"menu1\" type=\"button\" data-toggle=\"dropdown\"><i class=\"fas fa-wrench\"></i>
    <span class=\"caret\"></span></button>
    <ul class=\"dropdown-menu except-prod p-2\" style=\"left: 100px;!important;\" role=\"menu\" aria-labelledby=\"menu1\">
      <li role=\"presentation\"><a class=\"actions pl-2 pb-1\" role=\"menuitem\" tabindex=\"-1\" href='".base_url().'seller/variantcategories/variant_update/'.$vd['v_id']."' style=\"color: #707070;
    text-decoration: none!important;
    padding-bottom: .25rem!important;\"><i class=\"fa fa-edit\"></i>Edit</a></li>

 <li role=\"presentation\" class=\"divider\" style=\"border-bottom:1px solid #bab8b8\"></li>

      <li role=\"presentation\"><a class=\"btn deletebtn\" data-toggle=\"modal\" data-target=\"#confirmation\" data-id='".$vd['v_id']."' style=\"color: #707070;
    text-decoration: none!important;
    padding-bottom: .25rem!important;margin-left: -5px;\"><i class=\"fa fa-trash\"></i>Delete</a></li>
      
    </ul>
  </div>

  

							</center></td>";
					/*	}else{
							echo "<td>No Access.</td>";
						} */
					echo "</tr>";
						$i++;
					}
				} else {
					echo '<tr><td colspan="8" style="text-align:center">'.$data[0]['v_cat_title'].' Variant Data Not Found.</td></tr>';
				}
			?>
		</tbody>
	</table>
</div>



