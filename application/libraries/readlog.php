<?php
set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());
require_once APPPATH . 'third_party\log\vendor\autoload.php';
class Readlog {
	private $logViewer;
	public function __construct() {    
		$this->logViewer = new \CILogViewer\CILogViewer();    
	}
	public function index() {    echo $this->logViewer->showLogs();    return;}
}
?>