<?php
class Import_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}

	function addCSV($data)
	{
		$response = array('status'=>0,'message'=>'Invalid Request', 'code'=>'000');
		if($this->db->insert('tbl_import_csv', $data)){
			$response['status'] = 1;
			$response['message'] = 'OK';
		}
		return $response;
	}
	function saveImportResult($data)
	{
		$response = array('status'=>0,'message'=>'Invalid Request', 'code'=>'000');
		if($this->db->insert('tbl_import_csv_result', $data)){
			$response['status'] = 1;
			$response['id'] = $this->db->insert_id();
			$response['message'] = 'OK';
		}
		return $response;
	}

	function getSellerImportList($seller_id = "", $seller_name = "", $active = 1)
	{
		$this->db->select('*')
				 ->from('tbl_seller_list_import')
				 ->where('active', $active);
		if($seller_id){
			$this->db->where('id', $seller_id);
		}
		if(is_array($seller_id)){
			$this->db->where_in('id', $seller_id);
		}
		if($seller_name != ""){
			$this->db->like('seller_name', $seller_name);
		}
		$result = $this->db->get();
		if($result && $result->num_rows()>0){
			return $result->result();
		} else {
			return array();
		}
	}
	
	function getCSVList($user_id, $import_id = '')
	{
		$this->datatables->select('ic.id as id, is.seller_name, ic.csv_name, ic.total_lines, ic.completed_lines, ic.status, ic.active')
				->from('tbl_import_csv as ic')
				->join('tbl_seller_list_import as is', 'ic.seller = is.id');
		$this->db->order_by('ic.id', 'DESC');
		$this->datatables->where('ic.seller_id', $user_id);
		if($import_id != ''){
			$this->datatables->where('ic.id', $import_id);
		}
	}

	function getCSV($import_id = '')
	{
		$response = array('status'=>0,'message'=>'Invalid Request', 'code'=>'000');
		$this->db->select('ic.id as id, is.seller_name, is.method_name, ic.csv_name, ic.csv_file, ic.total_lines, ic.completed_lines, ic.status, ic.active')
				->from('tbl_import_csv as ic')
				->join('tbl_seller_list_import as is', 'ic.seller = is.id');
		$this->db->order_by('ic.id', 'DESC');
		if($import_id != ''){
			$this->db->where('ic.id', $import_id);
		}
		$result = $this->db->get();
		//echo $this->db->last_query();
		if($result && $result->num_rows()>0){
			$response['status'] = 1;
			$response['data'] = $result->result();
		} else {
			$response['code'] = '001';
			$response['message'] = 'CSV not found';
		}
		return $response;
	}
	
	function getBanList($type)
	{
		return array();
	}
	
	function CSVListExecuted($csv_id)
	{
		$response = array('status'=>0,'message'=>'Invalid Request', 'code'=>'000');
		$date = date('Y-m-d H:i:s');
		$data = array(
			'updated'=> $date,
			'status'=> 2,
			
		);
		$this->db->where('id',$csv_id);
		$this->db->update('tbl_import_csv', $data);
		if($this->db->affected_rows() > 0){
			return true;
		} else {
			return false;
		}
	}
	
	function categoryMap()
	{
		$map = array(
			array('title'=>'Television Accessories', 'id'=>62),
			array('title'=>'Speakers', 'id'=>50),
			array('title'=>'Subwoofers & Accessories', 'id'=>50),
			array('title'=>'Blu-ray & DVD Players', 'id'=>60),
			array('title'=>'Televisions', 'id'=>58),
			array('title'=>'Home Theater Systems & Soundbars', 'id'=>58),
			array('title'=>'Remote Controls & Accessories', 'id'=>50),
			array('title'=>'CD Players & Cassette Decks', 'id'=>33),
			array('title'=>'Wearable Tech & Fitness Accessories', 'id'=>19),
			array('title'=>'Action Cameras & Accessories', 'id'=>92),
			array('title'=>'USB Peripherals & Accessories', 'id'=>35),
			array('title'=>'Keyboard & Keypads', 'id'=>33),
			array('title'=>'Mice & Mouse Pads', 'id'=>34),
			array('title'=>'Routers', 'id'=>36),
			array('title'=>'Memory Cards', 'id'=>93),
			array('title'=>'Digital Media Readers', 'id'=>93),
			array('title'=>'Web Cameras & Accessories', 'id'=>95),
			array('title'=>'Speakers & Accessories', 'id'=>94),
			array('title'=>'Printers & Printing Supplies', 'id'=>96),
			array('title'=>'Flash Drives', 'id'=>93),
			array('title'=>'SATA Enclosures & Accessories', 'id'=>35),
			array('title'=>'USB & Network Adapters', 'id'=>41),
			array('title'=>'Ethernet Switches', 'id'=>37),
			array('title'=>'Hard Drives', 'id'=>29),
			array('title'=>'Wall Chargers', 'id'=>20),
			array('title'=>'USB Charge & Sync Cable', 'id'=>20),
			array('title'=>'Cellphone Mounts', 'id'=>20),
			array('title'=>'Car Chargers', 'id'=>20),
			array('title'=>'Batteries', 'id'=>20),
			array('title'=>'Wall/Car Combo Chargers', 'id'=>20),
			array('title'=>'Samsung Galaxy S', 'id'=>17),
			array('title'=>'iPhone', 'id'=>17),
			array('title'=>'iPhone  Plus', 'id'=>17),
			array('title'=>'Apple Watch', 'id'=>19),
			array('title'=>'iPhone /S/SE', 'id'=>17),
			array('title'=>'IPHONE X PLUS', 'id'=>17),
			array('title'=>'Screen Protectors', 'id'=>20),
			array('title'=>'iPhone /S', 'id'=>17),
			array('title'=>'iPhone Plus/S Plus', 'id'=>17),
			array('title'=>'Headsets', 'id'=>20),
			array('title'=>'Signal Booster Antennas', 'id'=>20),
			array('title'=>'Signal Booster Accessories', 'id'=>20),
			array('title'=>'Signal Boosters', 'id'=>20),
			array('title'=>'Outdoor HDTV Antennas', 'id'=>62),
			array('title'=>'HDMI Cables', 'id'=>62),
			array('title'=>'Video Cables', 'id'=>62),
			array('title'=>'Digital Audio Cables', 'id'=>55),
			array('title'=>'Audio Cables', 'id'=>55),
			array('title'=>'Indoor/Outdoor HDTV Antennas', 'id'=>62),
			array('title'=>'Indoor HDTV Antennas', 'id'=>62),
			array('title'=>'Bookshelf Speakers', 'id'=>50),
			array('title'=>'Center Channel Speakers', 'id'=>50),
			array('title'=>'Tower Speakers', 'id'=>50),
			array('title'=>'Subwoofers', 'id'=>50),
			array('title'=>'In-Ceiling Speakers', 'id'=>50),
			array('title'=>'In-Wall Speakers', 'id'=>50),
			array('title'=>'Streaming Media Players', 'id'=>59),
			array('title'=>'DVD Players', 'id'=>60),
			array('title'=>'LED TVs', 'id'=>58),
			array('title'=>'Soundbars', 'id'=>58),
			array('title'=>'Home Theater Systems', 'id'=>56),
			array('title'=>'LCD TVs', 'id'=>58),
			array('title'=>'Blu-ray Players', 'id'=>60),
			array('title'=>'GPS & Fitness Watches', 'id'=>19),
			array('title'=>'Action Cameras', 'id'=>92),
			array('title'=>'USB Cables', 'id'=>35),
			array('title'=>'Power Adapters', 'id'=>35),
			array('title'=>'Keyboards', 'id'=>33),
			array('title'=>'Wireless Mice & Presenters', 'id'=>34),
			array('title'=>'Keyboard & Mouse Kits', 'id'=>33),
			array('title'=>'Mouse Pads & Wrist Rests', 'id'=>34),
			array('title'=>'Portable Mice', 'id'=>33),
			array('title'=>'Web Cameras', 'id'=>95),
			array('title'=>'USB Adapters', 'id'=>35),
			array('title'=>'USB Docking Stations', 'id'=>35),
			array('title'=>'Adapters', 'id'=>35),
			array('title'=>'USB Hubs', 'id'=>35),
			array('title'=>'Communication Headphones', 'id'=>35),
			array('title'=>'Keypads', 'id'=>34),
			array('title'=>'Wired Mice', 'id'=>33),
		);
		return $map;
	}
	
	function searchForId($id, $array) {
	   foreach ($array as $key => $val) {
		   if ($val['uid'] === $id) {
			   return $key;
		   }
	   }
	   return null;
	}
	
	function getImportResults($import_id)
	{
		$response = array('status'=>0,'message'=>'Invalid Request', 'code'=>'000');
		$this->db->select('status_report')
				->from('tbl_import_csv_result')
				->where('id', $import_id);
		$result = $this->db->get();
		//echo $this->db->last_query();
		if($result && $result->num_rows()>0){
			$response['status'] = 1;
			$response['data'] = $result->row();
		} else {
			$response['code'] = '001';
			$response['message'] = 'CSV not found';
		}
		return $response;
	}
}	
?>