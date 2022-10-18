<?php
class Images extends CI_Controller 
{
	public $basepath = "";
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('url'));
		$this->basepath = FCPATH;//str_replace("system/","",BASEPATH." ");
	}
	public function index()
	{
		$this->getImage();
	}

	private function getImage()
	{
		if(!(empty($_GET)))
		{			
			$img = $_GET['img'];
			$img = base64_decode($img);
			if(!(stripos($img,"}")))$img = $img."}";
			$img = unserialize(stripslashes($img));
			$width = (isset($img['width']))?$img['width']: 75;
			$width = (isset($_GET['width']))?$_GET['width']: $width;
			$height = (isset($img['height']))?$img['height']: 50;						
			$height = (isset($_GET['height']))?$_GET['height']: $height;	
			if($width == "*")$width = "";
			if($height == "*")$height = "";
			$config['image_library'] = 'gd2';
			if(isset($_GET['is_image'])  && $_GET['is_image'] == 1){
				$config['source_image'] = $this->basepath.$img['img'];
			}
			else{
				$config['source_image']	= $this->basepath.$img['base']."/".$img['type']."/".$img['img'];
			}
			$config['create_thumb'] 	= TRUE;
			$config['maintain_ratio'] 	= TRUE;
			$config["quality"] 			= "90%";
			$config['width'] 			= $width;
			$config['height']			= $height;
			$config['wm_text'] 			= 'Ibad Gore';
			$config['wm_type'] 			= 'text';
			$config['wm_font_size']		= '16';
			$config['wm_font_color'] 	= '000000';
			$config['wm_vrt_alignment'] = 'bottom';
			$config['wm_hor_alignment'] = 'center';
			$config['wm_padding'] 		= '20';

			$this->load->library('image_lib', $config); 			
				
			if($this->image_lib->orig_width > 5000 || $this->image_lib->orig_height > 5000 || $this->image_lib->mime_type == "image/gif"){				
				header("Content-Description: File Transfer");
				header("Content-Type: image/gif");
				header("Content-Disposition: inline; filename=".$this->image_lib->dest_image);
				header("Content-Transfer-Encoding: Binary");
				header("Pragma:public");
				session_cache_limiter('none');
				header('Cache-control: max-age='.(60*60*24*365));
				header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
				if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
					header('HTTP/1.1 304 Not Modified');
					die();
				}
				readfile($this->image_lib->full_src_path);
				exit;	
			}
			if($this->image_lib->resize()){	
				$resized_image = $this->image_lib;				
				if(isset($_GET["type"]) && $_GET["type"] == "imgtag"){
					$getparam = "?";
					foreach($_GET as $key=>$value){
						if($key != "type")
						$getparam .= $key."=".$value."&";
					}
					$url = base_url().$this->uri->uri_string.$getparam;
					$url = substr($url,0,strlen($url)-1);
					echo "<img src = '".$url."' />";
					unlink($resized_image->full_dst_path);
				} else {
					header("Content-Description: File Transfer");
					header("Content-Type: image/jpeg");
					header("Content-Disposition: inline; filename=".$resized_image->dest_image);
					header("Content-Transfer-Encoding: Binary");
					header("Pragma:public");
					session_cache_limiter('none');
					header('Cache-control: max-age='.(60*60*24*365));
					header('Expires: '.gmdate(DATE_RFC1123,time()+60*60*24*365));
					if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
					   header('HTTP/1.1 304 Not Modified');
					   die();
					}
					readfile($resized_image->full_dst_path);
					unlink($resized_image->full_dst_path);
				}
			}		
		}
	}
	
	public function getUploadedImages()
	{
		$this->load->library("Upload_image_lib");
		$images = $this->upload_image_lib->getImages();
		if($images){
			echo json_encode(array("status"=>"success","images"=>$images));
		} else {
			echo json_encode(array("status"=>"fail"));
		}
	}
}
?>