<?php
class MusicPillars extends Securearea 
{ 
	function __construct(){		
		parent::__construct();
	}
	public function index($location,$role_id,$verify_code){	
		if($location == 1){
			redirect("http://localhost/web_projects/music_pillar-main/l/$role_id/$verify_code");
		}elseif($location == 2){
			redirect("http://localhost/web_projects/music_pillar-main/point_loma/l/$role_id/$verify_code");
		}else{
			redirect("http://localhost/web_projects/music_pillar-main/carmel_valley/l/carmel_valley/$role_id/$verify_code");
		}
	}
	
	function __destruct() {

  	}
}
?>