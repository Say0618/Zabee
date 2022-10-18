<?php $word = (isset($_GET['search']) && $_GET['search'] !="")?trim($_GET['search']):"";?>
<div class="row breadcrumb-row">
    <div class="col-sm-12 pl-3 p-2" >
        <span><i class="fa fa-home"></i> 
        <?php 
            $home = $this->lang->line('home');
            echo '<a class="breadcrumb-link-pre-color" href="'.base_url().'">'.$home.'</a> / ';
            if(!empty($bradcrumbs)){
                $bradcrumbs = array_reverse($bradcrumbs);
                $countBradcrumbs = count($bradcrumbs);
                $i = 1;
                foreach($bradcrumbs as $key=>$b){
                    if($i == $countBradcrumbs && !isset($product_name)){
                        $class = "breacrumb-latter-color";
                        $slash = "";
                    }else{
                        $class = "breadcrumb-link-pre-color";
                        $slash = "/ ";
                    }
                    echo '<a class="'.$class.'" href="'.$b['url'].'">'.ucfirst($b['cat_name']).' </a> '.$slash;
                    $i++;
                }
                if(isset($product_name) && $product_name !=""){
                    echo '<a class="" href="#"><span class="breacrumb-latter-color">'.$product_name.'</span></a>';
                }
            }else{
                echo '<a class="breacrumb-latter-color" href="#">'.ucfirst($word).' </a> ';
            } 
        ?>
        </span>
    </div>
</div>