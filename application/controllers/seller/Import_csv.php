<?php
class Import_csv extends SecureAccess{
	function __construct(){
		parent:: __construct();
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);
		$data = array(
			'page_name' => 'store_info',
			'isScript' 	=> false,
		);
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		$this->load->model("admin/Category_Model");
		$this->load->model("admin/Brands_Model");
		$this->load->model("admin/Import_Model");
		$this->load->model("Product_Model");
		$this->load->model("Utilz_Model", 'Utilz');
		$this->data['csv_path'] = $this->config->item('csv_path');
	}

	public function index(){
		$this->data['page_name'] 		= 'import_products';
		$this->data['Breadcrumb_name'] 	= 'Import Products';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);	
	}

	public function add(){
		$this->form_validation->set_rules('seller_id','Seller','xss_clean|trim|required');
		$this->form_validation->set_rules('csv_file','CSV File','xss_clean|trim|check_filevalidate');
		if($this->form_validation->run() === true){
			$data = $this->input->post();
			//echo '<pre>';print_r($data);echo '</pre>';
			$config = array();
			$params = array();
			if(isset($_FILES["csv_file"])){
				$config['upload_path'] = 'csv';
				$config['allowed_types'] = 'csv';
				$config['encrypt_name'] = true;
				$config['quality'] = "100%";
				$config['overwrite'] = FALSE;
				$config['remove_spaces'] = TRUE;
				$params['file'] = curl_file_create($_FILES['csv_file']['tmp_name'], $_FILES['csv_file']['type'], $_FILES['csv_file']['name']);
				$params['filesize'] = $_FILES['csv_file']['size'];
				$params['config'] = json_encode($config);
				$upload_server = $this->config->item('media_url').'/file/upload_csv';
				$response = $this->Utilz_Model->curlRequest($params, $upload_server, true);
				if($response->status == 1){
					$file_name 	= $response->upload_data->file_name;
					$orig_name 	= $response->upload_data->orig_name;
					$date 		= date('Y-m-d H:i:s');
					$csv_data 	= array(
									'created' 		=>$date,
									'updated' 		=>$date,
									'seller_id' 	=>$this->session->userdata['userid'],
									'seller' 		=>$data['seller_id'],
									'csv_name' 		=>$orig_name,
									'csv_file' 		=>$file_name,
									'total_lines' 	=> 0,
									'status' 		=> 0,
									'active' 		=> 1,
								);
					$response = $this->Import_Model->addCSV($csv_data);
					if($response['status'] == 1){
						redirect(base_url('seller/import_csv?status=success'));
					}
				} else {
					$error = array('error' =>$response->message);
				}
			}else{
				$error = array('error' => $this->upload->display_errors('<p> Error </p>'));
			}
			/*$config['upload_path']		= $this->data['csv_path'];
			$config['allowed_types']	= 'csv';
			$config['encrypt_name']		= true;
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('csv_file')){
				$error = array('error' => $this->upload->display_errors('<p> Error </p>'));
			} else {
				$file 		= array('upload_data' => $this->upload->data());
				$file_name 	= $file['upload_data']['file_name'];
				$orig_name 	= $file['upload_data']['orig_name'];
				$date 		= date('Y-m-d H:i:s');
				$csv_data 	= array(
								'created' 		=>$date,
								'updated' 		=>$date,
								'seller' 		=>$data['seller_id'],
								'csv_name' 		=>$orig_name,
								'csv_file' 		=>$file_name,
								'total_lines' 	=> 0,
								'status' 		=> 1,
								'active' 		=> 1,
							);
				$response = $this->Import_Model->addCSV($csv_data);
				if($response['status'] == 1){
					redirect(base_url('seller/import_csv?status=success'));
				}
			}*/
		}
		$this->data['page_name'] 		= 'upload_csv';
		$this->data['Breadcrumb_name'] 	= 'Add CSV';
		$this->data['isScript'] 		= true;
		$this->data['sellerList'] 		= $this->Import_Model->getSellerImportList();
		$this->load->view("admin/admin_template",$this->data);
	}
	
	public function filevalidate() {
		$check = TRUE;
		if ((!isset($_FILES['csv_file'])) || $_FILES['csv_file']['size'] == 0) {
			$this->form_validation->set_message('filevalidate', 'The {field} field is required');
			$check = FALSE;
		}
		else if (isset($_FILES['csv_file']) && $_FILES['csv_file']['size'] != 0) {
			$allowedExts = array("csv");
			$allowedTypes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
			$extension = pathinfo($_FILES["csv_file"]["name"], PATHINFO_EXTENSION);
			$detectedType = exif_imagetype($_FILES['csv_file']['tmp_name']);
			$type = $_FILES['csv_file']['type'];
			if (!in_array($detectedType, $allowedTypes)) {
				$this->form_validation->set_message('csv_file', 'Invalid CSV Content!');
				$check = FALSE;
			}
			if(!in_array($extension, $allowedExts)) {
				$this->form_validation->set_message('csv_file', "Invalid file extension {$extension}");
				$check = FALSE;
			}
		}
		return $check;
	}
	
	public function get()
	{
		if($this->session->userdata('userid')){
			$user_id = $this->session->userdata('userid');
		} else {
			$user_id = $this->session->userdata('admin_id');
		}
		$this->load->library('datatables');	
		$this->Import_Model->getCSVList($user_id);
		$delete_link = "<a class= 'actions' href='javascript:void(0);' onclick='askDelete(\"$1\")' title='Delete' data-content=\"<p>Are you sure?</p>
						<a class='btn btn-danger po-delete1' id='a__$1' href='" . site_url('seller/import_csv/delete/$1') . "'>I am Sure
						</a> <button class='btn btn-primary po-close'>No</button>\"  rel='popover'><i class=\"fa fa-trash\"></i> Delete Item</a>";
        $action = '<div class="text-center"><div class="btn-group text-left">'
            . '<button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown"><i class="fas fa-wrench"></i></button>
		<ul class="dropdown-menu except-prod pull-right" role="menu">
			<li class="pl-2 pb-1"><a href="' . site_url('seller/import_csv/execute/$1') . '" class="actions"><i class="fa fa-edit"></i> Execute </a></li>';
        $action .= '<li class="divider" style="border-bottom:1px solid #bab8b8"></li>
				<li class="pl-2 pt-1">' . $delete_link . '</li>
			</ul>
		</div></div>';
        $this->datatables->add_column("Actions", $action, "id");
		echo $this->datatables->generate();
    }
	
	function delete(){
		
	}
	function execute($csv_id = ''){
		if($csv_id == ''){
			redirect(base_url('seller/import_csv?status=error'));
		}
		$getCSV =  $this->Import_Model->getCSV($csv_id);
		if($getCSV['status'] == 1){
			$filename = $this->config->item('csv_path').$getCSV['data'][0]->csv_file;
			//echo $filename;die();
			$response = $this->uploadcsv($filename, true);
			$date = date('Y-m-d H:i:s');
			$data = array(
				'created' 		=>$date,
				'updated' 		=>$date,
				'seller_id' 	=>$this->session->userdata['userid'],
				'csv_id' 		=>$csv_id,
				'status_report' => json_encode($response)
			);
			$response = $this->Import_Model->saveImportResult($data);
			if($response['status'] == 1){
				$getCSV =  $this->Import_Model->CSVListExecuted($csv_id);
				redirect(base_url('seller/import_csv/show_result/').$response['id']);
				exit;
			}
			redirect(base_url('seller/import_csv?status=error'));
		}
	}
	
	public function show_result($id = ''){
		if($id == '')
			redirect(base_url('seller/import_csv?status=error'));
		
		$this->data['page_name'] 		= 'show_result';
		$this->data['Breadcrumb_name'] 	= 'Show Import result';
		//$this->data['isScript'] 		= true;
		$results = $this->Import_Model->getImportResults($id);
		
		$this->data['success_import'] = array();
		$this->data['error_import'] = array();
		if($results['status'] == 1){
			$data = json_decode($results['data']->status_report);
			$this->data['success_import'] = $data->success;
			$this->data['error_import'] = $data->error;
		}
		//echo '<pre>';print_r($this->data['error_import']);echo '</pre>';die();
		$this->load->view("admin/admin_template",$this->data);	
	}
	
	public function processCSV($file_name){
		$user_type = $this->session->userdata['user_type'];
		$file_name = getcwd().'/uploads/csv/ProdList.csv';
		$this->load->library('csvreader');
		
        $itemList 	= $this->csvreader->parse_file($file_name, false, '|');
		$flag 		= true;
		
		$start 			= 7000;
		$limit 			= $start+1222;
		$banBrands 		= $this->Import_Model->getBanList("brand");
		$mapped 		= $this->Import_Model->categoryMap();
		$category 		= array();
		$categoryMapped = array();
		$a 				= array();
		$e 				= array();
		$seller_id 		= $this->session->userdata['userid'];

		if($user_type == 1){
			$created_by = "Admin";
			$flag = false;
			for($i = 0;$i<count($itemList);$i++){
				if(!isset($itemList[$i]))
					continue;
				$item = $itemList[$i];
				$product_name = $item['DESCRIPTION'];
				$where_product = 'product_name = "'.addslashes($product_name).'"';
				$get_product = $this->Product_Model->getProduct('','product.product_name, product.product_id, sp.seller_id ', $where_product);
				if($get_product){
					$e[] = $item['DESCRIPTION'].' -- Product already added';
					continue;
				}
				$UPC 				= $item['VENDOR SKU'];
				$searchA 			= explode(',', $item['SUBCATEGORY3']);
				$searchB 			= explode(',', $item['SUBCATEGORY2']);
				$sub_category_id 	= '';
				for($k = 0;$k<count($searchA);$k++){
					$key = array_search($searchA[$k], array_column($mapped, 'title'));
					if($key){
						$sub_category_id = $mapped[$key]['id'];
						break;
					}	
				}
				if($sub_category_id == ''){
					for($k = 0;$k<count($searchB);$k++){
						$key = array_search($searchB[$k], array_column($mapped, 'title'));
						if($key){
							$sub_category_id = $mapped[$key]['id'];
							break;
						}	
					}
					if($sub_category_id == ''){
						$e[] = $item['DESCRIPTION'].' -- No Category';
						continue;
					}
				}
				$product 	= array();
				$video_link = array();
				$media_id 	= array();
				$imageCount = 0;
				$date 		= date('Y-m-d H:i:s');
				$date_utc 	= gmdate("Y-m-d\TH:i:s");
				$date_utc 	= str_replace("T"," ",$date_utc);
				$seller_id	= $this->session->userdata['userid'];
				if(isset($this->session->userdata['user_type']) && $this->session->userdata['user_type'] != 1){
					$created_by = "Seller";
					$is_active = "0";
				}else{
					$created_by = "Admin";
					$is_active = "1";
				}
				$feature = explode(';', $item['SPECS']);
				if($feature[count($feature)-1] == '')
					unset($feature[count($feature)-1]);
				$brand_name = $item['BRAND NAME'];
				if(in_array($brand_name, $banBrands)){
					$e[] = $item['DESCRIPTION'].' -- Banned Brand';
					continue;
				}
				$result = $this->Brands_Model->getBrands("","","","","","AND brand_name='".addcslashes($brand_name, "'")."'", true);
				if($result){
					$brands_id = $result[0]['brand_id'];
				}else{
					$brand_data = array(
						array(
							'brand_name' 		=> $brand_name,
							'brand_description' => $brand_name,
							'display_status' 	=> '1',
							'active' 			=> '1',
						)
					);
					$brands_id = $this->Brands_Model->insertbrands($brand_data, true);
				}
				$description = $item['LONG DESC'];
				for($f = 0;$f<count($feature);$f++){
					if($feature[$f] != ''){
						$feature[$f]  = clean($feature[$f]);
						$description .= "\n".$feature[$f];
					}
				}
				$product = array('product_name'			=>addcslashes($item['DESCRIPTION'], "'"),
								 'product_description'	=>htmlentities(addcslashes($description, "'")),
								 'sub_category_id' 		=>$sub_category_id,
								 'is_private' 			=>"0",
								 'brand_id'				=>$brands_id,
								 'created_id'			=>$seller_id,
								 'upc_code' 			=> ($UPC !="")?$UPC:rand(0,time()),
								 'created_date'			=>$date_utc,
								 'created_by'			=>$created_by,
								 'is_active'			=>$is_active,
								 );
				$product_dimensions = array('height'=>$item['HEIGHT'],
									  'width' => $item['WIDTH'],
									  'length' => $item['LENGTH'],
									  'weight_type' => $item['WEIGHT TYPE'],
									  'weight' => floatval($item['WEIGHT-UNPACKED'])+ floatval($item['ESTIMATED SHIP WEIGHT']),
									  'ship_note' => $item['NOTES1']
									);
				$shippingInfo = array('height'=>$item['HEIGHT'],
									  'width' => $item['WIDTH'],
									  'length' => $item['LENGTH'],
									  'weight_type' => $item['WEIGHT TYPE'],
									  'weight' => floatval($item['WEIGHT-UNPACKED'])+ floatval($item['ESTIMATED SHIP WEIGHT']),
									  'ship_note' => $item['NOTES1']
									);
				$keywords 		= $item['KEYWORDS'];
				$variant_cat 	= '';
				$video_link 	= '';
				$media_id 		= '';
				$dummy_id 		= '';
				$keywords 		= $item['KEYWORDS'];
				$result 		= $this->Product_Model->createProduct($product,$dummy_id,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id, $product_dimensions);
				if($result){
					$a[] = $item['DESCRIPTION'];
				}else{
					$e[] = $item['DESCRIPTION'].' -- Failed to add in system';
				}
			}
		} else if($user_type == 2){
			$created_by = "Seller";
			for($i = 0;$i<count($itemList);$i++){
				if(!isset($itemList[$i]))
					continue;
				$item = $itemList[$i];
				$product_name = $item['DESCRIPTION'];
				$UPC = $item['UPC'];
				$where_product = 'product_name = "'.addslashes($product_name).'"';
				$get_product = $this->Product_Model->getProduct('','product.product_name, product.product_id, sp.seller_id ', $where_product);
				if(!$get_product){
					$e[] = $item['DESCRIPTION'].' -- no Product Found';
					continue;
				}
				$product_id 			= $get_product[0]['product_id'];
				$dummy_id 				= $seller_id.'-'.$i;
				$seller_product 		= array();
				$seller_product_variant = array();
				$region 				= array();
				$product_inventory 		= array();
				$video_link 			= array();
				$media_id 				= array();
				$date 					= date('Y-m-d H:i:s');
				$date_utc 				= gmdate("Y-m-d\TH:i:s");
				$date_utc 				= str_replace("T"," ",$date_utc);
				$seller_id 				= $this->session->userdata['userid'];
				$is_warehouse 			= "";
				$is_warehouse_approved 	="";
				$product_name 			= $item['DESCRIPTION'];
				$pvi 					= 0;
				if(isset($_SESSION['is_zabee']) && $_SESSION['is_zabee'] == "1"){
					$is_zabee = $_SESSION['is_zabee'];
					$warehouse_id = $_SESSION['warehouse_id'];
				}else{
					$is_zabee = "0";
					$warehouse_id = "0";
				}
				$feature = explode(';', $item['SPECS']);
				if($feature[count($feature)-1] == '')
					unset($feature[count($feature)-1]);
				$description = $item['LONG DESC'];
				for($f = 0;$f<count($feature);$f++){
					if($feature[$f] != ''){
						$feature[$f] = clean($feature[$f]);
						$description .= "\n".$feature[$f];
					}
				}
				$condition_id 									 = 1;
				$seller_product[0]['seller_id'] 				 = $seller_id;
				$seller_product[0]['created_date'] 				 = $date_utc;
				$seller_product[0]['seller_sku'] 				 = $item['PETRA SKU'];
				$seller_product[0]['seller_product_description'] = $description;
				$seller_product[0]['condition_id'] 				 = $condition_id;
				$seller_product[0]['sp_id'] 					 = "";

				$where =  array('user_id'=>"1" ,'is_deleted'=>"0","is_active"=>"1");
				$shipping_ids = $this->Product_Model->getData(DBPREFIX."_product_shipping","GROUP_CONCAT(shipping_id) as shipping_id",$where,0,'','shipping_id DESC');
				$seller_product[0]['shipping_ids'] 	= $shipping_ids[0]->shipping_id;
				$variantArray 						= array();
				$productVariant 					= array();
				$variant 							= array();
				$pc 								= 1;
				$pv_id 								= "";
				$discount 							= "";
				$productVariant[] = array('seller_id'=>$seller_id,'seller_sku'=>$item['PETRA SKU'],'condition_id'=>$condition_id,'return_id'=>0,'variant_group'=>'','price'=>$item['MSRP'],'quantity'=>$item['AVAILABLE'],'variant_cat_group'=>'','pv_id'=>'','discount'=>'');
				$seller_product_variant[0] 			= $variant;
				$proVariant[0] 						= $productVariant;
				//------------------------------------------------//
				$region 											= "";
				$video_link["condition_video_link".$condition_id] 	= "";
				$image_name 										= $item['IMAGE URL'];
				$imageData = array('product_id'=>$product_id, 'condition_id' => $condition_id, 'dummy_id'=>$dummy_id,'thumbnail'=>$image_name,'condition_id'=>"1", 'iv_link'=>$image_name, 'is_local'=>"0",'position'=>0,'is_image'=>"1","is_cover"=>"1");
				$this->db->insert('tbl_product_media', $imageData);
				$image_id = $this->db->insert_id();
				$media_id["media_id".$condition_id] = $image_id;
				$result = $this->Product_Model->createInventory($product_name,$product_id,$seller_product,$seller_product_variant,$dummy_id,$region,$product_inventory,$proVariant,$seller_id,$video_link,$media_id,$warehouse_id);
				if($result){
					$a[] = $item['DESCRIPTION'];
				}else{
					$e[] = $item['DESCRIPTION'].' -- Failed to add in system';
				}
			}
		}
		return array('success'=>$a, 'error'=>$e);
		//echo '<pre>';print_r($a);echo '</pre>';
		//echo '<pre>';print_r($e);echo '</pre>';die();
	}

	private function upload_csv()
	{
			$config['upload_path']          = './csv/upload/';
			$config['allowed_types']        = 'csv';
			$config['encrypt_name']        	= true;
			
			$this->load->library('upload', $config);
			$i = 1;
			while($i < 5){
				if(isset($_FILES)){
					if ( ! $this->upload->do_upload('csvfile'.$i)){
						$error = array('error' => $this->upload->display_errors('<p> Error </p>'));
					} else {
						$data = array('upload_data' => $this->upload->data());
						$file_name[] = $data['upload_data']['file_name'];
					}
				}
				$i++;
			}
			return $file_name;
	}

	public function uploadcsv($file_name = '', $isReturn = false){
		$user_type = $this->session->userdata['user_type'];
		//$file_name = getcwd().'/uploads/csv/testimport.csv';
		$this->load->library('CSVReader');
		$this->load->model("admin/Variant_Category_Model");
		
        $old_arr 	= $this->csvreader->parse_file($file_name, false, '|');
		$itemList = array();

		foreach ($old_arr as $key => $item) {
		   $itemList[$item['UPC']][] = $item;
		}
		ksort($itemList, SORT_NUMERIC);
		//echo '<pre>';print_r($itemList);echo '<pre>';die();

		
		$flag 				= true;
		$banBrands 			= $this->Import_Model->getBanList("brand");
		$variant_category 	= $this->Variant_Category_Model->getALLParentVariantCatgories();
		$variantData 		= $this->Variant_Category_Model->getAllVariantlist();
		$categoryData		=  $this->Category_Model->getChildCategories();
		$parentCategories	= $this->Category_Model->getCat("","","1");
		//echo '<pre>';print_r($variantData);echo '<pre>';die();
		$a 				= array();
		$e 				= array();
		$seller_id 		= $this->session->userdata['userid'];
		if($user_type == 1){
			$created_by = "Admin";
			$flag = false;
			$i = 0;
			foreach($itemList as $items){
				if(!isset($items[$i]))
					continue;
				$item = $items[$i];
				$product_name = $item['NAME'];
				$where_product = 'product_name = "'.addslashes($product_name).'"';
				$get_product = $this->Product_Model->getProduct('','product.product_name, product.product_id, sp.seller_id ', $where_product);
				//$get_product = false;
				if($get_product){
					$e[] = $item['NAME'].' -- Product already added';
					continue;
				}
				$UPC 				= $item['UPC'];
				$category			= $item['CATEGORY'];
				$sub_category		= $item['SUBCATEGORY'];
				$sub_category_id 	= '';
				$key = array_search(strtolower($sub_category), array_map('strtolower', array_column($parentCategories, 'category_name')));
				if($key){
					$sub_category_id = $parentCategories[$key]['category_id'];
				} else {
					$key = array_search(strtolower($sub_category), array_map('strtolower', array_column($categoryData, 'category_name')));
					if($key){
						$sub_category_id = $categoryData[$key]['category_id'];
					} else {
						$e[] = $item['SUBCATEGORY'].' -- No Category';
						continue;
					}
				}
				$product 	= array();
				$video_link = array();
				$media_id 	= array();
				$imageCount = 0;
				$date 		= date('Y-m-d H:i:s');
				$date_utc 	= gmdate("Y-m-d\TH:i:s");
				$date_utc 	= str_replace("T"," ",$date_utc);
				$seller_id	= $this->session->userdata['userid'];
				if(isset($this->session->userdata['user_type']) && $this->session->userdata['user_type'] != 1){
					$created_by = "Seller";
					$is_active = "0";
				}else{
					$created_by = "Admin";
					$is_active = "1";
				}
				$feature = explode(';', $item['FEATURES']);
				if($feature[count($feature)-1] == '')
					unset($feature[count($feature)-1]);
				$brand_name = $item['BRAND NAME'];
				if(in_array($brand_name, $banBrands)){
					$e[] = $item['NAME'].' -- Banned Brand';
					continue;
				}
				$result = $this->Brands_Model->getBrands("","","","","","AND brand_name='".addcslashes($brand_name, "'")."'", true);
				if($result){
					$brands_id = $result[0]['brand_id'];
				}else{
					$brand_data = array(
						array(
							'brand_name' 		=> $brand_name,
							'brand_description' => $brand_name,
							'display_status' 	=> '1',
							'active' 			=> '1',
						)
					);
					$brands_id = $this->Brands_Model->insertbrands($brand_data, true);
				}
				$description = $item['LONG DESC'];
				for($f = 0;$f<count($feature);$f++){
					if($feature[$f] != ''){
						$feature[$f]  = clean($feature[$f]);
						$description .= "\n".$feature[$f];
					}
				}
				$product = array('product_name'			=>addcslashes($item['NAME'], "'"),
								 'product_description'	=>htmlentities(addcslashes($description, "'")),
								 'sub_category_id' 		=>$sub_category_id,
								 'is_private' 			=>"0",
								 'brand_id'				=>$brands_id,
								 'created_id'			=>$seller_id,
								 'upc_code' 			=> ($UPC !="")?$UPC:rand(0,time()),
								 'created_date'			=>$date_utc,
								 'created_by'			=>$created_by,
								 'is_active'			=>$is_active,
								 );
				$product_dimensions = array('height'=>$item['HEIGHT'],
									  'width' => $item['WIDTH'],
									  'length' => $item['LENGTH'],
									  'weight_type' => isset($item['WEIGHT TYPE'])?$item['WEIGHT TYPE']:'cm',
									  'weight' => floatval($item['WEIGHT-UNPACKED'])+ floatval($item['ESTIMATED SHIP WEIGHT']),
									);
				$shippingInfo = array('height'=>$item['HEIGHT'],
									  'width' => $item['WIDTH'],
									  'length' => $item['LENGTH'],
									  'weight_type' => isset($item['WEIGHT TYPE'])?$item['WEIGHT TYPE']:'cm',
									  'weight' => floatval($item['WEIGHT-UNPACKED'])+ floatval($item['ESTIMATED SHIP WEIGHT']),
									  'ship_note' => $item['SHIP_NOTES']
									);
				$variant_cat = array();
				if(isset( $items[0]['VARIANT1']) && $items[0]['VARIANT1'] != ''){
					for($v = 0;$v<count($items);$v++){
						
						$quantity[] = $items[$v]['AVAILABLE'];
						$prices[] = $items[$v]['PRICE'];
						$vCatTemp = array();
						for($vi = 0;$vi<5;$vi++){
							$vitem = (isset($items[$v]['VARIANT'.$vi]) && $items[$v]['VARIANT1'] != '')?explode('-=-', $items[$v]['VARIANT'.$vi]):'';
							if(is_array($vitem) && count($vitem)> 1){
								$key = array_search(strtolower($vitem[0]), array_map('strtolower', array_column($variant_category, 'v_cat_title')));
								$vcat = $variant_category[$key];
								if($v == 0){
									$variant_cat[] = $vcat['v_cat_id'];
								} else {
									$vCatTemp[] = $vcat['v_cat_id'];
								}
							}
						}
						if($v>0){
							$compare = array_diff($variant_cat, $vCatTemp);
							if(!empty($compare)){
								$e[] = $item['NAME'].' -- Variants missmatched';
							}
						}
					}
				}
				$keywords 		= $item['KEYWORDS'];
				$video_link 	= '';
				$media_id 		= '';
				$dummy_id 		= '';
				$keywords 		= $item['KEYWORDS'];
				$result 		= $this->Product_Model->createProduct($product,$dummy_id,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id, $product_dimensions);
				if($result){
					$a[] = $item['NAME'];
				}else{
					$e[] = $item['NAME'].' -- Failed to add in system';
				}
			}
		} else if($user_type == 2){
			$created_by = "Seller";
			$i = 0;
			foreach($itemList as $items){
				$i++;
				if(!isset($items[0]))
					continue;
				$item = $items[0];
				$product_name = $item['NAME'];
				$where_product = 'product_name = "'.addslashes($product_name).'"';
				$get_product = $this->Product_Model->getProduct('','product.product_name, product.product_id, sp.seller_id ', $where_product);
				$product_id = '';
				//$get_product = true;
				if(!$get_product){
					$UPC 				= $item['UPC'];
					$category			= $item['CATEGORY'];
					$sub_category		= $item['SUBCATEGORY'];
					$sub_category_id 	= '';
					$key = array_search(strtolower($sub_category), array_map('strtolower', array_column($parentCategories, 'category_name')));
					if($key){
						$sub_category_id = $parentCategories[$key]['category_id'];
					} else {
						$key = array_search(strtolower($sub_category), array_map('strtolower', array_column($categoryData, 'category_name')));
						if($key){
							$sub_category_id = $categoryData[$key]['category_id'];
						} else {
							$e[] = $item['SUBCATEGORY'].' -- No Category';
							continue;
						}
					}
					
					$product 	= array();
					$video_link = array();
					$media_id 	= array();
					$imageCount = 0;
					$date 		= date('Y-m-d H:i:s');
					$date_utc 	= gmdate("Y-m-d\TH:i:s");
					$date_utc 	= str_replace("T"," ",$date_utc);
					$seller_id	= $this->session->userdata['userid'];
					if(isset($this->session->userdata['user_type']) && $this->session->userdata['user_type'] != 1){
						$created_by = "Seller";
						$is_active = "0";
					}else{
						$created_by = "Admin";
						$is_active = "1";
					}
					$feature = explode(';', $item['FEATURES']);
					if($feature[count($feature)-1] == '')
						unset($feature[count($feature)-1]);
					$brand_name = $item['BRAND NAME'];
					if(in_array($brand_name, $banBrands)){
						$e[] = $item['NAME'].' -- Banned Brand';
						continue;
					}
					$result = $this->Brands_Model->getBrands("","","","","","AND brand_name='".addcslashes($brand_name, "'")."'", true);
					if($result){
						$brands_id = $result[0]['brand_id'];
					}else{
						$brand_data = array(
							array(
								'brand_name' 		=> $brand_name,
								'brand_description' => $brand_name,
								'display_status' 	=> '1',
								'active' 			=> '1',
							)
						);
						$brands_id = $this->Brands_Model->insertbrands($brand_data, true);
					}
					$description = $item['LONG DESC'];
					for($f = 0;$f<count($feature);$f++){
						if($feature[$f] != ''){
							$feature[$f]  = clean($feature[$f]);
							$description .= "\n".$feature[$f];
						}
					}
					$product = array('product_name'			=>addcslashes($item['NAME'], "'"),
									 'product_description'	=>htmlentities(addcslashes($description, "'")),
									 'sub_category_id' 		=>$sub_category_id,
									 'is_private' 			=>"0",
									 'brand_id'				=>$brands_id,
									 'created_id'			=>$seller_id,
									 'upc_code' 			=> ($UPC !="")?$UPC:rand(0,time()),
									 'created_date'			=>$date_utc,
									 'created_by'			=>$created_by,
									 'is_active'			=>$is_active,
									 );
					$product_dimensions = array('height'=>$item['HEIGHT'],
									  'width' => $item['WIDTH'],
									  'length' => $item['LENGTH'],
									  'weight_type' => isset($item['WEIGHT TYPE'])?$item['WEIGHT TYPE']:'cm',
									  'weight' => floatval($item['WEIGHT-UNPACKED'])+ floatval($item['ESTIMATED SHIP WEIGHT']),
									);
					$shippingInfo = array('height'=>$item['HEIGHT'],
									  'width' => $item['WIDTH'],
									  'length' => $item['LENGTH'],
									  'weight_type' => isset($item['WEIGHT TYPE'])?$item['WEIGHT TYPE']:'cm',
									  'weight' => floatval($item['WEIGHT-UNPACKED'])+ floatval($item['ESTIMATED SHIP WEIGHT']),
									  'ship_note' => $item['SHIP_NOTES']
									);
					$variant_cat = array();
					if(isset( $items[0]['VARIANT1']) && $items[0]['VARIANT1'] != ''){
						for($v = 0;$v<count($items);$v++){
							$quantity[] = $items[$v]['AVAILABLE'];
							$prices[] = $items[$v]['PRICE'];
							$vCatTemp = array();
							for($vi = 0;$vi<5;$vi++){
								$vitem = (isset($items[$v]['VARIANT'.$vi]) && $items[$v]['VARIANT1'] != '')?explode('-=-', $items[$v]['VARIANT'.$vi]):'';
								if(is_array($vitem) && count($vitem)> 1){
									$key = array_search(strtolower($vitem[0]), array_map('strtolower', array_column($variant_category, 'v_cat_title')));
									$vcat = $variant_category[$key];
									if($v == 0){
										$variant_cat[] = $vcat['v_cat_id'];
									} else {
										$vCatTemp[] = $vcat['v_cat_id'];
									}
								}
							}
							if($v>0){
								$compare = array_diff($variant_cat, $vCatTemp);
								if(!empty($compare)){
									$e[] = $item['NAME'].' -- Variants missmatched';
								}
							}
						}
					}
					$keywords 		= $item['KEYWORDS'];
					$video_link 	= '';
					$media_id 		= '';
					$dummy_id 		= '';
					$keywords 		= $item['KEYWORDS'];
					$result 		= $this->Product_Model->createProduct($product,$dummy_id,$feature,$keywords,$variant_cat,$seller_id,$shippingInfo,$video_link,$media_id, $product_dimensions);
					if($result){
						$a[] = $item['NAME'].' - Product Created';
						$product_id = $result['product_id'];
					}else{
						$e[] = $item['NAME'].' -- Failed to add in system';
						continue;
					}
				} else {
					if($seller_id != $get_product[0]['seller_id']){
						$product_id 			= $get_product[0]['product_id'];
					} else {
						$e[] = $item['NAME'].' -- Product already added';
						continue;
					}
				}
				
				//$product_id = 654654+$i;
				if($product_id == ''){
					$e[] = $item['NAME'].' -- Invalid Product';
					continue;
				}
				
				$dummy_id 				= $seller_id.'-'.$i;
				$seller_product 		= array();
				$seller_product_variant = array();
				$region 				= array();
				$product_inventory 		= array();
				$video_link 			= array();
				$media_id 				= array();
				$date 					= date('Y-m-d H:i:s');
				$date_utc 				= gmdate("Y-m-d\TH:i:s");
				$date_utc 				= str_replace("T"," ",$date_utc);
				$seller_id 				= $this->session->userdata['userid'];
				$is_warehouse 			= "";
				$is_warehouse_approved 	="";
				$product_name 			= $item['NAME'];
				$pvi 					= 0;
				if(isset($_SESSION['is_zabee']) && $_SESSION['is_zabee'] == "1"){
					$is_zabee = $_SESSION['is_zabee'];
					$warehouse_id = $_SESSION['warehouse_id'];
				}else{
					$is_zabee = "0";
					$warehouse_id = "0";
				}
				$feature = explode(';', $item['FEATURES']);
				if($feature[count($feature)-1] == '')
					unset($feature[count($feature)-1]);
				$description = $item['LONG DESC'];
				for($f = 0;$f<count($feature);$f++){
					if($feature[$f] != ''){
						$feature[$f] = clean($feature[$f]);
						$description .= "\n".$feature[$f];
					}
				}
				$condition_id 									 = 1;
				$seller_product[0]['seller_id'] 				 = $seller_id;
				$seller_product[0]['created_date'] 				 = $date_utc;
				$seller_product[0]['seller_sku'] 				 = $item['SELLER SKU'];
				$seller_product[0]['seller_product_description'] = $description;
				$seller_product[0]['condition_id'] 				 = $condition_id;
				$seller_product[0]['sp_id'] 					 = "";

				$where =  array('user_id'=>"1" ,'is_deleted'=>"0","is_active"=>"1");
				$shipping_ids = $this->Product_Model->getData(DBPREFIX."_product_shipping","GROUP_CONCAT(shipping_id) as shipping_id",$where,0,'','shipping_id DESC');
				$seller_product[0]['shipping_ids'] 	= $shipping_ids[0]->shipping_id;
				$variantArray 						= array();
				$productVariant 					= array();
				$variant 							= array();
				$pc 								= 1;
				$pv_id 								= "";
				$discount 							= "";
				$variants = array();
				$variant_cat = array();
				if($items[$v]['VARIANT1'] != ''){
					for($v = 0;$v<count($items);$v++){
						$quantity[] = $items[$v]['AVAILABLE'];
						$prices[] = $items[$v]['PRICE'];
						for($vi = 0;$vi<5;$vi++){
							//echo $items[$v]['VARIANT'.$vi];
							$vitem = (isset($items[$v]['VARIANT'.$vi]) && $items[$v]['VARIANT1'] != '')?explode('-=-', $items[$v]['VARIANT'.$vi]):'';
							if(is_array($vitem) && count($vitem)> 1){
								$key = array_search(strtolower($vitem[1]), array_map('strtolower', array_column($variantData, 'v_title')));
								if(!$key){
									$e[] = $item['NAME'].' -- Invalid variant - '.$items[$v]['VARIANT'.$vi];
									continue;
								}
								$vval = $variantData[$key];
								if($vitem[0] != $vval['v_cat_title']){
									$key = array_search(strtolower($vitem[0]), array_map('strtolower', array_column($variant_category, 'v_cat_title')));
									$vcat = $variant_category[$key];
								} else {
									$vcat = $vval;
								}
								if($v == 0){
									$variant_cat[] = $vcat['v_cat_id'];
								}
								$variantArray[$v][] = $vval['v_id'];
								//echo '<pre>Cat-'.$v;print_r($vcat);echo '</pre>';
								//echo '<pre>Val'.$v;print_r($vval);echo '</pre>';
								//echo '<br>';
							}
						}
						$productVariant[] = array('seller_id'=>$seller_id,'seller_sku'=>$item['SELLER SKU'],'condition_id'=>$condition_id,'return_id'=>0,'variant_group'=>implode(',',$variantArray[$v]),'price'=>$items[$v]['PRICE'],'quantity'=>$items[$v]['AVAILABLE'],'variant_cat_group'=>implode(',',$variant_cat),'pv_id'=>'','discount'=>$discount);
					}
				} else {
					$productVariant[] = array('seller_id'=>$seller_id,'seller_sku'=>$item['PETRA SKU'],'condition_id'=>$condition_id,'return_id'=>0,'variant_group'=>'','price'=>$item['MSRP'],'quantity'=>$item['AVAILABLE'],'variant_cat_group'=>'','pv_id'=>'','discount'=>'');
				}
				$seller_product_variant[0] 			= $variantArray;
				$proVariant[0] 						= $productVariant;
				//------------------------------------------------//
				//continue;
				$region 											= "";
				$video_link["condition_video_link".$condition_id] 	= "";
				$image_name 										= $item['IMAGE URL'];
				$imageData = array('product_id'=>$product_id, 'condition_id' => $condition_id, 'dummy_id'=>$dummy_id,'thumbnail'=>$image_name,'condition_id'=>"1", 'iv_link'=>$image_name, 'is_local'=>"0",'position'=>0,'is_image'=>"1","is_cover"=>"1");
				$this->db->insert('tbl_product_media', $imageData);
				$image_id = $this->db->insert_id();
				$media_id["media_id".$condition_id] = $image_id;
				$result = $this->Product_Model->createInventory($product_name,$product_id,$seller_product,$seller_product_variant,$dummy_id,$region,$product_inventory,$proVariant,$seller_id,$video_link,$media_id,$warehouse_id);
				if($result){
					$a[] = $item['NAME'];
				}else{
					$e[] = $item['NAME'].' -- Failed to add in system';
				}
			}
		}
		if($isReturn)
			return array('success'=>$a, 'error'=>$e);
		else {
			echo '<pre>';print_r($a);echo '</pre>';
			echo '<pre>';print_r($e);echo '</pre>';die();
		}
	}
	
	public function map_array()
	{
		$array_a = array(
			(object)array(
				'ProductTitle'=>'Title1',
				'ProductPrice'=>'100'
			), 
			(object)array(
				'ProductTitle'=>'Title2',
				'ProductPrice'=>'110'
			)
		);
		$array_b = array();
		echo '<pre>';print_r($array_a);die();
	}
	
	function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}
}
?>