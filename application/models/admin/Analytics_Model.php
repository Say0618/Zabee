<?php
class Analytics_Model extends CI_Model
{
	function __construct() 
	{
		parent::__construct();
		$this->load->database("default");
	}
	
	public function getViewsCountByDay($startDate = '', $endDate = '', $user_id, $user_type)
	{
		$response = array('status' => 0, 'message' => '', 'data' => array());
		$this->db->select(' COUNT(*) AS daily_views, DATE_FORMAT(date_time, "%Y/%m/%d") AS date_time')
				->from('tbl_search_keyword AS k')
				->join('tbl_product_variant AS pv', 'k.keyword = pv.pv_id')
				->join('tbl_seller_store AS ss', 'pv.seller_id = ss.seller_id')
				->where('k.type = "product"')
				->where('k.date_time >=', $startDate)
				->where('k.date_time <=', $endDate)
				->group_by('DATE_FORMAT(date_time, "%Y%m%d")')
				->order_by('k.date_time', 'asc');
		$records = $this->db->get();
		if($records->num_rows() > 0){
			$response['status'] = 1;
			$response['data'] = $records->result();
			$response['message'] = 'OK';
		} else {
			$response['message'] = 'no record found';
		}
		return $response; 
	}
	public function getProduct($postData, $user_id, $isAdmin)
	{
		$response = array();

		## Read value
		$draw = $postData['draw'];
		$start = (int)$postData['start'];
		$rowperpage = (int)$postData['length']; // Rows display per page
		$columnIndex = $postData['order'][0]['column']; // Column index
		$columnName = ($postData['columns'][$columnIndex]['data'] == 'date_time')?'k.date_time':$postData['columns'][$columnIndex]['data']; // Column name
		$columnSortOrder = $postData['order'][0]['dir']; // asc or desc
		$searchValue = $postData['search']['value']; // Search value

		// Custom search filter 
		$searchSeller = (isset($postData['searchSeller']))?$postData['searchSeller']:'';
		$searchProduct = (isset($postData['searchProduct']))?$postData['searchProduct']:'';
		$startRange = (isset($postData['startDate']))?$postData['startDate']:'';
		$endDate = (isset($postData['endDate']))?$postData['endDate']:'';

		## Search 
		$search_arr = array();
		$searchQuery = "";
		$search_arr[] = " k.pv_id IS NOT NULL ";
		if($searchValue != ''){
			$search_arr[] = " (p.product_name like '%".$searchValue."%' or 
			ss.store_name like'%".$searchValue."%' ) ";
		}
		if($searchSeller != ''){
			$search_arr[] = " ss.store_name='".$searchSeller."' ";
		}
		if($searchProduct != ''){
			$search_arr[] = " p.product_name='".$searchProduct."' ";
		}
		if($startRange != '' && $endDate != ''){
			$search_arr[] = 'k.date_time between "'.date('Y-m-d', strtotime($startRange)).'" AND "'.date('Y-m-d', strtotime($endDate)).'" ';
		} else {
			if($startRange != ''){
				$search_arr[] = " k.date_time>='".date('Y-m-d', strtotime($startRange))."' ";
			}
			if($endDate != ''){
				$search_arr[] = " k.date_time<='".date('Y-m-d', strtotime($endDate))."' ";
			}
		}
		//echo '<pre>';print_r($search_arr);die();
		if(count($search_arr) > 0){
			$searchQuery = implode(" and ",$search_arr);
		}

		## Total number of records without filtering
		$this->db->select('count(*) AS allcount')
						->from('tbl_search_keyword AS k')
						->join('tbl_product_variant AS pv', 'k.keyword = pv.pv_id')
						->join('tbl_product AS p', 'pv.product_id = p.product_id')
						->join('tbl_seller_store AS ss', 'pv.seller_id = ss.seller_id')
						->group_by('p.product_id, pv.seller_id');
		$records = $this->db->get()->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) AS allcount')
				 ->from('tbl_search_keyword AS k')
				 ->join('tbl_product_variant AS pv', 'k.keyword = pv.pv_id')
				 ->join('tbl_product AS p', 'pv.product_id = p.product_id')
				 ->join('tbl_seller_store AS ss', 'pv.seller_id = ss.seller_id')
				 ->group_by('p.product_id, pv.seller_id');
		if($searchQuery != '')
			$this->db->where($searchQuery);
		$records = $this->db->get();
		if($records->num_rows() > 0){
			$records = $records->result();
			$totalRecordwithFilter = $records[0]->allcount;
		} else {
			$totalRecordwithFilter = 0;
		}

		## Fetch records
		$this->db->select('p.product_name, ss.store_name, COUNT(p.product_id) as product_views,p.product_id, pv.sp_id, pv.pv_id, DATE_FORMAT(k.date_time, "%m/%d/%Y / %h:%i") AS datetime')
				->from('tbl_search_keyword AS k')
				->join('tbl_product_variant AS pv', 'k.keyword = pv.pv_id')
				->join('tbl_product p', 'pv.product_id = p.product_id')
				->join('tbl_seller_store ss', 'pv.seller_id = ss.seller_id');
		if($searchQuery != '')
		$this->db->where($searchQuery);
		$this->db->group_by('p.product_id, pv.seller_id');
		$this->db->order_by($columnName, $columnSortOrder);
		$this->db->limit($rowperpage, $start);
		$records = $this->db->get()->result();
		//echo $this->db->last_query();die();
		$data = array();
		$i = $start+1;
		if($totalRecordwithFilter > 0){
			foreach($records as $record ){

				$data[] = array(
					"sno"=>$i,
					"date_time"=>$record->datetime,
					"product_name"=>$record->product_name,
					"product_views"=>$record->product_views,
					"store_name"=>$record->store_name
				);
				$i++;
			}
		} 
		## Response
		$response = array(
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $data,
			//"SQL"=>$this->db->last_query()
		);

		return $response; 
	}
}