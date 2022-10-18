<?php
header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->helper('url');
	}

	public function index(){
		echo phpinfo();
		echo 'Hello';
	}
	
	public function download_file()
	{
		$response = array('message'=>'error','status'=>0);
		$image_link = $this->input->post('image_link');
		$image_type = $this->input->post('image_type');
		$filenameOut = $this->input->post('filenameOut');
		//echo '<pre>';print_r($_POST);echo '</pre>';die();
		if($image_link != ''){
			$filenameIn  = $image_link;
			$file_base_path = $this->basePath($image_type);
			$isValid = false;
			if (exif_imagetype($filenameIn) == IMAGETYPE_GIF) {
				$image = imagecreatefromgif($filenameIn);
				$isValid = true;
				$ext = '.gif';
			}
			if (exif_imagetype($filenameIn) == IMAGETYPE_PNG) {
				$image = imagecreatefrompng($filenameIn);
				$isValid = true;
				$ext = '.png';
			}
			if (exif_imagetype($filenameIn) == IMAGETYPE_JPEG) {
				$image = imagecreatefromjpeg($filenameIn);
				$isValid = true;
				$ext = '.jpg';
			}
			if($isValid){
				$response['status'] = 1;
				$response['message'] = 'OK';
				$filenameOutThumb = $filenameOut.'_thumb'.$ext;
				$filenameOut .= $ext;
				$filenameOutPath = $file_base_path.$filenameOut;
				//echo $filenameOutPath;die();
				imagepng($image, $filenameOutPath);
				$this->load->library('image_lib');
				$config['image_library'] = 'gd2';
				$config['source_image'] = $filenameOutPath;
				$config['create_thumb'] = TRUE;
				$config['maintain_ratio'] = TRUE;
				$config['overwrite'] = FALSE;
				$config['width'] = 400;
				$config['height'] = 400;
				$config['new_image'] = $this->basePath('product_thumbnail'). $filenameOut;
				$this->image_lib->initialize($config);
				$this->image_lib->resize();
				$this->image_lib->clear();
				$response['images']['captured'] = array('iv_link' => $filenameOut, 'thumbnail' => $filenameOutThumb, 'extension'=> $ext);
				
			} else {
				$response['images']['errored'] = $filenameOut;
			}
		} else {
			$responsep['message'] = 'invalid link';
		}
		echo json_encode($response);

	}
	function upload_media(){
		$response = array('message'=>'error','status'=>0);
		
		$this->load->library('upload');
		$this->load->library('image_lib');
		$config = $tempPaths = json_decode($this->input->post('config'), true);
		$image_type = $this->input->post('image_type');
		//$config['upload_path'] = $this->basePath($image_type);
		//echo '<pre>';print_r($config);
		//echo '<pre>';print_r($_FILES);die();
		$config['upload_path'] = $upload_path = $this->basePath().$config['upload_path'];
		$config['upload_thumbnail_path'] = $upload_thumbnail_path = $this->basePath().$config['upload_thumbnail_path'];
		// echo"<pre>"; print_r($config); die();
		$upload_thumbnail_width = isset($config['upload_thumbnail_width'])?$config['upload_thumbnail_width']:0;
		$upload_thumbnail_height = isset($config['upload_thumbnail_height'])?$config['upload_thumbnail_height']:0;
		$this->upload->initialize($config);
		$_FILES['userfile']['name'] =$_FILES['file']['name'];
		$_FILES['userfile']['type'] =$_FILES['file']['type'];
		$_FILES['userfile']['tmp_name'] =$_FILES['file']['tmp_name'];
		$_FILES['userfile']['error'] =$_FILES['file']['error'];
		$_FILES['userfile']['size'] =$_FILES['file']['size'];
		if($this->upload->do_upload()){
			$data = $this->upload->data();
			$image_name =  $data['file_name'];
			unset($config);
			$thumbnail = $data['raw_name'].'_thumb'.$data['file_ext'];
			$config['image_library'] = 'gd2';
			$config['source_image'] = $upload_path.'/'.$image_name;
			$config['create_thumb'] = TRUE;
			$config['maintain_ratio'] = TRUE;
			$config['overwrite'] = FALSE;
			$config['width'] = ($upload_thumbnail_width>0)?$upload_thumbnail_width:400;
			$config['height'] = ($upload_thumbnail_height>0)?$upload_thumbnail_height:400;
			$config['new_image'] = $upload_thumbnail_path.'/'.$image_name;
			// echo '<pre>';print_r($config);die();
			$this->image_lib->initialize($config);
			if(!$this->image_lib->resize()){
				$response['message'] = $this->image_lib->display_errors();
				$response['code'] = 'thumb';
			} else {
				$response['status'] = 1;
				$response['message'] = 'OK';
				$response['images']['original']['filename'] = $image_name;
				$response['images']['original']['filepath'] = $this->basePathUrl('',true).$tempPaths['upload_path'].'/';
				$response['images']['thumbnail']['filename'] = $thumbnail;
				$response['images']['thumbnail']['filepath'] = $this->basePathUrl('',true).$tempPaths['upload_thumbnail_path'].'/';
				$response['images']['is_local'] = "1";
			}
			$this->image_lib->clear();
		} else {
			$response['message'] = $this->upload->display_errors();
			$response['code'] = 'main';
			$response['type'] = mime_content_type($_FILES['file']['tmp_name']);
		}
		echo json_encode($response);
	}

	function upload_csv(){
		$response = array('message'=>'error','status'=>0);
		
		$this->load->library('upload');
		$config = json_decode($this->input->post('config'), true);
		//$config['upload_path'] = $this->basePath($image_type);
		
		//echo '<pre>';print_r($_FILES);die();
		$config['upload_path'] = $this->basePath().$config['upload_path'];
		//echo '<pre>';print_r($config);die();
		$this->upload->initialize($config);
		$_FILES['userfile']['name'] =$_FILES['file']['name'];
		$_FILES['userfile']['type'] =$_FILES['file']['type'];
		$_FILES['userfile']['tmp_name'] =$_FILES['file']['tmp_name'];
		$_FILES['userfile']['error'] =$_FILES['file']['error'];
		$_FILES['userfile']['size'] =$_FILES['file']['size'];
		if($this->upload->do_upload()){
			$data = $this->upload->data();
			$response['status'] = 1;
			$response['message'] = 'OK';
			$response['upload_data'] = $data;
		} else {
			$response['message'] = $this->upload->display_errors();
		}
		echo json_encode($response);
	}

	function delete_media(){
		$response = array('message'=>'error','status'=>0);
		$filename = $this->input->post('filename');
		$filetype = $this->input->post('filetype');
		$path = $this->basePath($filetype);
		if(is_file($path.$filename)){
			unlink($path.$filename);
			if($filetype == 'product'){
				$extension_pos = strrpos($filename, '.'); // find position of the last dot, so where the extension starts
				$thumb = substr($filename, 0, $extension_pos) . '_thumb' . substr($filename, $extension_pos);
				$path = $this->basePath().$filetype.'/thumbs/';
				//echo $path.$thumb;die();
				if(is_file($path.$thumb)){
					unlink($path.$thumb);
				}
			}
			$response['status'] = 1;
			$response['message'] = 'OK';
		}
		echo json_encode($response);
	}
	function basePath($type = ''){
		$path = ($type != '')?$this->config->item($type):'';
		return getcwd().'/'.$this->config->item('base_directory').$path;
	}
	
	function basePathUrl($type = '', $external = false){
		$path = ($type != '')?$this->config->item($type):'';
		$base_path = ($external)?$this->config->item('media_url'): base_url();
		return $base_path.$this->config->item('base_directory').$path;
	}
	
	function testURL($t = ''){
		echo  $this->basePathUrl($t);
	}
}
