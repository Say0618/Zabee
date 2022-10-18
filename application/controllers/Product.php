<?php
class Product extends Securearea
{
	public $productname = "";
	public $product = "";
	public $region = "";
	public $data = array();
	function __construct()
	{
		parent::__construct();
		$this->load->helper("url");
		$this->load->library('Mobile_Detect');
		$detect = new Mobile_Detect;
		$this->data['detect'] = $detect;
		if(isset($_COOKIE['country_id']) && $_COOKIE['country_id'] != 0){
			$this->region = $_COOKIE['country_id'];
		}
		if($this->session->userdata('userid')){
			$this->data['user_id'] = $this->session->userdata('userid');
		}else{
			$this->data['user_id'] = "";
		}
		$this->lang->load('english', 'english');
	}

	public function index(){
		$this->data['page_name'] = 'products';
		$this->data['title'] 	 = $this->config->item('title');
		$this->data['css_name']  = 'homecss';
		$where = array('product.is_private' => 0);
		$product = $this->Product_Model->frontProductDetails('','','','','','','',$where,'product.product_name','','',$this->region);
		if(!empty($product)){
			$productData = $this->Product_Model->forntProductData($product,'product_name');
			$brandData = $this->Product_Model->forntBrandData("deleted_id IS NULL");
			$this->data['productData'] 	= $productData;
			$this->data['categoryData'] = $categoryData;
			$this->data['brandData'] 	= $brandData;
			$this->data['showSidebar'] 	= true;
			$this->load->view('front/template', $this->data);
		}else{
			$this->data['heading'] 		= "Error 404!";
			$this->data['message'] 		= "Product Not Found";
			$this->data['page_name'] 	= 'error_404';
			$this->load->view('front/template', $this->data);
		}
	}

	public function viewProduct($id = "",$seller_product_id = "")
	{
		$this->data['title'] = "Products Details";
		$this->data['hasScript'] = true;
		$this->data['hasStyle'] = false;
		if($id !=""){
			$id = explode('-',$id);
			$id = end($id);
			$index = str_replace(' ','_',strtolower(base64_decode(urldecode($id))));
			$id = explode('_',base64_decode(urldecode($id)));
			$count = count($id);
			$id = $id[$count-1];
			$where = array('product.is_private' => 0);
			$product	= $this->Product_Model->frontProductDetails($id,'','','','','','pin.price DESC',$where,'','',$seller_product_id,$this->region);
			if(!empty($product)){
				$productData = $this->Product_Model->forntProductData($product);
				$this->data['page_name'] 		= 'productdetails';
				$this->data['viewProductData'] 	= $productData;
				$this->data['countryList'] 		= $this->Utilz_Model->countries();
				$this->load->view('front/template', $this->data);
			}else{
				$this->data['hasScript'] = false;
				$this->data['heading'] 	 = "Error 404!";
				$this->data['message'] 	 = "Product Not Found";
				$this->data['page_name'] = 'error_404';
				$this->load->view('front/template', $this->data);
			}
		}
	}

	public function showMoreReview($url_val){
		$this->load->library('pagination');
		$this->load->model('Reviewmodel');
		$decode_data = base64_decode(urldecode($url_val));
		$DATA = explode('-zabeeBreaker-',$decode_data);
		$dataCount = count($DATA);
		$join = array("tbl_users u"=>"u.`userid` = r.buyer_id ");
		if($dataCount > 1){
		$where= array('product_id'=>$DATA[1],'sp_id'=>$DATA[2],'pv_id'=>$DATA[3]);
		}else{
			$where= array('seller_id'=>$DATA[0]);
		}
		$table_name					 ="tbl_product_reviews r";
		$total 						 = $this->Utilz_Model->getAllData($table_name, "COUNT(r.review_id) AS total", $where ,"0","","",$join);
		$config['base_url'] 		 = base_url('product/showMoreReview/'.$url_val);
		$config["total_rows"] 		 = $total[0]->total;
		$config["per_page"] 		 = 10;
		$config['use_page_numbers']  = TRUE;
		$config['num_links'] 		 = 1;
		$config['cur_tag_open'] 	 = '<li class="active"><a class="page-link current">';
		$config['cur_tag_close'] 	 = '</a></li>, ';
		$config['next_link'] 		 = '<span class="styleAngle">&gt</span>';
		$config['prev_link'] 		 = '<span class="styleAngle">&lt</span>';
		$config['num_tag_open'] 	 = '<li>';
		$config['num_tag_close'] 	 = '</li>';
		$config['prev_tag_open'] 	 = '<li class="pg-tag">';
		$config['prev_tag_close'] 	 = '</li>';
		$config['next_tag_open'] 	 = '<li class="pg-tag">';
		$config['next_tag_close'] 	 = '</li>';
  		$config['first_tag_open'] 	 = '<li>';
		$config['first_tag_close'] 	 = '</li>';
		$config['last_tag_open'] 	 = '<li>';
		$config['last_tag_close'] 	 = '</li>';
		$config['page_query_string'] = TRUE;
		$this->pagination->initialize($config);

		if($this->input->get('page')){
			$page = $this->input->get('page');
		}else{
			$page = 1;
		}
		if($page > 1){
			$page = ($page-1)*$config["per_page"];
			$limit = array($config["per_page"],$page);
		}else{
			$limit = $config["per_page"];
		}
		$select = "r.*,CONCAT(u.firstname,' ',u.lastname) AS buyer_name";
		$review = $this->Reviewmodel->getdata($DATA[2],$DATA[3],$DATA[1],$limit);//$this->Utilz_Model->getAllData($table_name, $select, $where ,$where_in="0",$group_by="",$order_by="",$join,$limit,$whereExtra="");
		$str_links = $this->pagination->create_links();
		$links["links"] = explode(', ', $str_links);
		$this->data['links'] 		= $links;
		$this->data['page_name'] 	= 'showMoreReview';
		$this->data['title'] 		= "Review";
		$this->data['reviewData'] 	= $review;
		$this->data['slug'] 		= $DATA[4];
		// echo"<pre>"; print_r($review["result"][0]); die();
		//echo "<pre>";print_r($review);die();
		$this->data['newsletter'] 	= true;
		$this->load->view('front/template', $this->data);
	}

	/*public function searchResults($slug=""){
		$this->data['page_name'] 			= 'search_results';
		$this->data['hasScript']			= TRUE;
		$this->data['hasStyle']				= TRUE;
		$this->data['showSorting'] 			= TRUE;
		$this->data['newsletter'] 			= TRUE;
		$this->load->library('pagination');
		$_SESSION['view'] = ($this->input->get('view'))?$this->input->get('view'):((isset($_SESSION['view']))?$_SESSION['view']:'list');
		$product_name = ($this->input->get("search"))? $this->input->get("search"):"";
		$ship = $this->input->post("ship");
		$where="";
		$order="";
		$link = "";
		$prod_search = "";
		$config = array();
		$keywords = "";
		$range = "";
		$cat_id = "";
		$brand_id = "";
		$bradcrumb = array();
		$category_id = "";
		$param = "";
		$brand_search = trim($this->input->get("brands_search"));
		$user_id=$this->session->userdata('userid');
		if($slug !=""){
			$cat_id = $this->Product_Model->getIdBySlug(DBPREFIX."_categories","category_id",array("is_active"=>"1","slug"=>$slug));
			// print_r($this->db->last_query()); die();
			if(isset($cat_id->category_id)){
				$cat_id = $cat_id->category_id;

				$category_id = $this->getAllCategoriesChildId($cat_id);
				// var_dump($category_id); die();
				if($category_id == ""){
					$category_id = $cat_id;
				}
				// else{
				// 	$category_id .= ','.$cat_id;
				// }
				$bradcrumb = $this->getAllCategoriesParentId($cat_id);
			}elseif($slug != "searchResult" && !isset($cat_id->category_id)){
				// die("here");
				$this->data['itemsFound'] 			= 0;
				$this->data['links'] 				= 0;
				$this->load->view('front/template', $this->data);
				return false;
			}
		}else{
			redirect(base_url());
		}
		if($this->input->get('keywords') !=""){
			$keywords = $this->input->get('keywords');
		}
		if($this->input->get('fs') != ""){
			$ship = $this->input->get('fs');
		}
		if($this->input->get('price_range') !=""){
			$range= explode("-",$this->input->get('price_range'));
			$where = "pin.price BETWEEN '$range[0]' AND '$range[1]'";
			if(!is_numeric($range[0]) || !is_numeric($range[1])){
				redirect(base_url('product'));
			}
			if($param == ""){
				$param .='?price_range='.$this->input->get('price_range');
			}else{
				$param .='&price_range='.$this->input->get('price_range');
			}
		}

		if($this->input->get('product_name')){
			$order="product_name ".$this->input->get('product_name');
			if($param == ""){
				$param .='?product_name='.$this->input->get('product_name');
			}else{
				$param .='&product_name='.$this->input->get('product_name');
			}
		}
		if($this->input->get('min_price')){
			$order="price ".$this->input->get('min_price');
			if($param == ""){
				$param .='?min_price='.$this->input->get('min_price');
			}else{
				$param .='&min_price='.$this->input->get('min_price');
			}
		}

		if($this->input->get('sort')){
			$value = explode('-',$this->input->get('sort'));
			if($value[0] == "price"){
				$order="price ".$value[1];
			}else{
				$order="product_name ".$value[1];
			}
			if($param == ""){
				$param .='?sort='.$this->input->get('sort');
			}else{
				$param .='&sort='.$this->input->get('sort');
			}
		}
		// echo $param; die();
		if(isset($_GET["search"]) && $this->input->get("search")==""){
			redirect(base_url());
		}
		if($this->input->get("search")!="" ){
			$prod_search = stripslashes(trim($this->input->get("search")));
			$link .="&search=".stripslashes($prod_search);
			if($param == ""){
				$param .='?search='.stripslashes($prod_search);
			}else{
				$param .='&search='.stripslashes($prod_search);
			}
			if($this->session->userdata('userid')){
				$userid = $this->session->userdata('userid');
			}else{
				$userid="";
			}
			$this->load->library('user_agent');
			$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
			$meta_info = $this->agent->agent_string();
			$os = $this->agent->platform();
			$platform = 'W';
			$session_id = $this->session->session_id;
			$this->User_Model->keywordSearch($prod_search,$_SERVER['REMOTE_ADDR'],$referer_url,$userid,'keywords', $platform, $meta_info, $os, $session_id);
			$type = 'keywords';
			$this->add_log($prod_search, $type);
		}
		if($brand_search){
			if(is_numeric($brand_search)){
				$link .="&brands_search=".$brand_search;
				if($this->input->get("brands_search") == "All"){
					$this->input->get("brands_search");
				}
				$brand_id = $brand_search;
			}else{
				redirect(base_url());
			}
		}
		$url=strtok($_SERVER["REQUEST_URI"],'?');
		$link=$_SERVER['REQUEST_URI'];
		if($this->input->get('page')){
			if(strpos($_SERVER['REQUEST_URI'],'?page='.$this->input->get('page'))){
				$link = str_replace('?page='.$this->input->get('page'), '', $_SERVER['REQUEST_URI']);
			}else{
				$link = str_replace('&page='.$this->input->get('page'), '', $_SERVER['REQUEST_URI']);
			}
		}
		if($category_id!=""){
			$where .= "cat.category_id IN(".$category_id.") AND ";

			// if($param == ""){
			// 	$param .='?category_search='.$cat_id;
			// }
			// else{
			// 	$param .= '&category_search='.$cat_id;
			// }

		}
		if(isset($brand_id) && $brand_id!=""){
			$where.=" b.brand_id='$brand_id'";
			if($where!=""){
				$where.=" AND ";
			}
			if($param == ""){
				$param .='?brands_search='.$brand_id;
			}else{
				$param .='&brands_search='.$brand_id;
			}
		}
		if($ship!=""){
			if($ship == "free"){
				$ship = 0;
			}
			if($param == ""){
				$param .='?fs='.$ship;
			}else{
				$param .='&fs='.$ship;
			}
		}
		// if(strpos($order, "price") === true){

		// }
		$config["base_url"] = base_url($slug.$param);
		// echo $config["base_url"]."<br>"."slug: ".$slug."<br>"."param: ".$param; die();
		$plus = 1;
		if($prod_search){
			$prod_search = urlClean(str_replace('-',' ',$prod_search));
			$word = explode(" ",$prod_search);
			$prod_search = "";
			foreach($word as $w){
				if(strlen($w) < 3){
					$prod_search .= $w."__* ";
				}else{
					if($plus==1){
						$prod_search .="+";
						$plus++;
					}
					$prod_search .= $w." ";
				}
			}
			$prod_search = trim($prod_search);
		}
		// var_dump($brand_id,$category_id,$prod_search,$range,$ship);
		$count=$this->Product_Model->getProductListCount($brand_id,$category_id,$prod_search,$range,$ship);
		// print_r($count); die();
		if($category_id == ""){
			$category_id = $count->cat_id;
		}
		$total_row 					 = $count->total;
		$config["total_rows"] 		 = $total_row;
		$config["per_page"] 		 = 12;
		$config['use_page_numbers']  = TRUE;
		$config['num_links'] 		 = 1;
		$config['cur_tag_open'] 	 = '<li class="active text-center"><a class="page-link current">';
		$config['cur_tag_close'] 	 = '</a></li>, ';
		$config['next_link'] 		 = '<span class=""><i class="fa fa-angle-right" style="font-size:20px"></i></span>';
		$config['prev_link'] 		 = '<span class=""><i class="fa fa-angle-left" style="font-size:20px"></i></span>';
		$config['num_tag_open'] 	 = '<li class="text-center">';
		$config['num_tag_close'] 	 = '</li>';
		$config['first_link'] 		 ='First';
		$last_page_number 			 = ceil($config["total_rows"]/$config["per_page"]);
  		$config['last_link']  		 = $last_page_number;
		$config['prev_tag_open'] 	 = '<li class="pg-tag text-center">';
		$config['prev_tag_close'] 	 = '</li>';
		$config['next_tag_open'] 	 = '<li class="pg-tag-next text-center">';
		$config['next_tag_close'] 	 = '</li>';
		$config['first_tag_open'] 	 = '<li class="text-center">';
		$config['first_tag_close'] 	 = '</li>';
		$config['last_tag_open'] 	 = '<li class="text-center">';
		$config['last_tag_close'] 	 = '</li>';
		$config['page_query_string'] = TRUE;
		$this->pagination->initialize($config);
		if($this->input->get('page')){
			$page = $this->input->get('page');
		}else{
			$page = 1;
		}
		if($page > $last_page_number){
			$page = $last_page_number;
		}
		$productData= $this->Product_Model->getProductList($brand_id,$category_id,$prod_search,$page,$config["per_page"],$order,$range,"","","",$user_id,$ship);
		// echo"<pre>"; print_r($productData); die();
		$i=0;
		if($category_id && $count->p_id){
			$brand_and_categories = $this->Product_Model->getBrandAndCategory($count->p_id,$category_id);
			// echo"<pre>"; print_r($brand_and_categories['brands']); die();
		}else{
			$brand_and_categories['brands'] = "";
			$brand_and_categories['categories'] = "";
		}
		$this->data['bradcrumbs'] 			= $bradcrumb;
		$this->data['brandData'] 			= $brand_and_categories['brands'];
		$this->data['category'] 			= $brand_and_categories['categories'];
		$this->data['productData'] 			= $productData;
		$str_links 							= $this->pagination->create_links();
		$links["links"] 					= explode(', ', $str_links);
		$this->data['links'] 				= $links;
		$this->data['link'] 				= $link;
		$this->data['max']					= $this->Product_Model->get_max_price();
		$this->data['wishlist_categories'] 	= $this->Secure_Model->WishlistViaCategories($user_id);
		$this->data['wishlist_check'] 		= $this->Secure_Model->ClearWishlistIfNoCategories($user_id);
		$this->data['itemsFound'] 			= $total_row;
		if(isset($bradcrumb[0]['cat_name'])){
		$this->data['title'] 				= "Shop ".$bradcrumb[0]['cat_name']." at Zab.ee";
		}else{
			$this->data['title'] = $this->config->item('title');
		}
		// print_r($this->data['link']);
		$this->load->view('front/template', $this->data);
	}*/
	//New Function
	public function searchResults($slug=""){
		$this->data['page_name'] 			= 'search_results';
		$this->data['hasScript']			= TRUE;
		$this->data['hasStyle']				= TRUE;
		$this->data['showSorting'] 			= TRUE;
		$this->data['newsletter'] 			= TRUE;
		$this->load->library('pagination');
		$_SESSION['view'] = ($this->input->get('view'))?$this->input->get('view'):((isset($_SESSION['view']))?$_SESSION['view']:'list');
		$product_name = ($this->input->get("search"))? $this->input->get("search"):"";
		$ship = $this->input->post("ship");
		$where="";
		$order="";
		$link = "";
		$prod_search = "";
		$config = array();
		$keywords = "";
		$range = "";
		$cat_id = "";
		$brand_id = "";
		$bradcrumb = array();
		$category_id = "";
		$param = "";
		$brand_search = trim($this->input->get("brands_search"));
		$user_id=$this->session->userdata('userid');
		$see_all = $this->input->get("see_all");
		if($slug !=""){
			if($slug != "searchResult"){
				$cat_id = $this->Product_Model->getIdBySlug(DBPREFIX."_categories","category_id,category_name",array("is_active"=>"1","slug"=>$slug));
				// print_r($this->db->last_query()); die();
				if(isset($cat_id->category_id)){
					$cat_name = $cat_id->category_name;
					$cat_id = $cat_id->category_id;

					$category_id = $this->getAllCategoriesChildId($cat_id);
					// print_r($this->db->last_query()); die();
					// var_dump($category_id); die();
					if($category_id == "" || $category_id->category_ids === NULL){
						$category_id = $cat_id;
					}
					// else{
					// 	$category_id .= ','.$cat_id;
					// }
					$bradcrumb = $this->getAllCategoriesParentId($cat_id);
				}else{
					// die("here");
					$this->data['itemsFound'] 			= 0;
					$this->data['links'] 				= 0;
					$this->load->view('front/template', $this->data);
					return false;
				}
			}
		}else{
			redirect(base_url());
		}
		if($see_all){
			$param = "?see_all=1";
		}
		if($this->input->get('price_range') !=""){
			$range= explode("-",$this->input->get('price_range'));
			$where = "pin.price BETWEEN '$range[0]' AND '$range[1]'";
			if(!is_numeric($range[0]) || !is_numeric($range[1])){
				redirect(base_url('product'));
			}
			if($param == ""){
				$param .='?price_range='.$this->input->get('price_range');
			}else{
				$param .='&price_range='.$this->input->get('price_range');
			}
		}

		if($this->input->get('product_name')){
			$order="product_name ".$this->input->get('product_name');
			if($param == ""){
				$param .='?product_name='.$this->input->get('product_name');
			}else{
				$param .='&product_name='.$this->input->get('product_name');
			}
		}
		if($this->input->get('min_price')){
			$order="price ".$this->input->get('min_price');
			if($param == ""){
				$param .='?min_price='.$this->input->get('min_price');
			}else{
				$param .='&min_price='.$this->input->get('min_price');
			}
		}

		if($this->input->get('sort')){
			$value = explode('-',$this->input->get('sort'));
			if($value[0] == "price"){
				$order="price ".$value[1];
			}else{
				$order="product_name ".$value[1];
			}
			if($param == ""){
				$param .='?sort='.$this->input->get('sort');
			}else{
				$param .='&sort='.$this->input->get('sort');
			}
		}
		if($brand_search){
			if(is_numeric($brand_search)){
				$link .="&brands_search=".$brand_search;
				if($this->input->get("brands_search") == "All"){
					$this->input->get("brands_search");
				}
				$brand_id = $brand_search;
			}else{
				redirect(base_url());
			}
		}
		$url=strtok($_SERVER["REQUEST_URI"],'?');
		$link=$_SERVER['REQUEST_URI'];
		if($this->input->get('page')){
			if(strpos($_SERVER['REQUEST_URI'],'?page='.$this->input->get('page'))){
				$link = str_replace('?page='.$this->input->get('page'), '', $_SERVER['REQUEST_URI']);
			}else{
				$link = str_replace('&page='.$this->input->get('page'), '', $_SERVER['REQUEST_URI']);
			}
		}
		if($category_id!=""){
			$where .= "cat.category_id IN(".$category_id.") AND ";

			// if($param == ""){
			// 	$param .='?category_search='.$cat_id;
			// }
			// else{
			// 	$param .= '&category_search='.$cat_id;
			// }

		}
		if(isset($brand_id) && $brand_id!=""){
			$where.=" b.brand_id='$brand_id'";
			if($where!=""){
				$where.=" AND ";
			}
			if($param == ""){
				$param .='?brands_search='.$brand_id;
			}else{
				$param .='&brands_search='.$brand_id;
			}
		}
		if($ship!=""){
			if($ship == "free"){
				$ship = 0;
			}
			if($param == ""){
				$param .='?fs='.$ship;
			}else{
				$param .='&fs='.$ship;
			}
		}
		//echo $param;die();
		if($slug !="" && $cat_id && $see_all !=1 && $param == ""){
			$this->load->model("admin/Banner_Model");
			unset($_SESSION['view']);
			$user_id = $this->data['user_id'];
			$querySelect = "";
			if($user_id !=""){$querySelect = ", w.wish_id as already_saved";}
			$region="";
			if(isset($_COOKIE['country_id']) && $_COOKIE['country_id'] != 0){ $region = $_COOKIE['country_id'];}
			//Featured Products.
			$where = array('product.is_private' =>'0','cat.is_homepage'=>"1","product.is_featured"=>"1","pro_cat.category_id"=>$cat_id);
			$select = 'product.product_name,product.slug,product.product_id,pm.thumbnail,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,AVG(preview.`rating`) AS rating,pin.price,d.value as discount_value,d.type as discount_type, d.valid_from as valid_from, d.valid_to as valid_to, sp.seller_id'.$querySelect;
			$featured = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
			//New in Store
			$where = array('product.is_private' =>'0','cat.is_homepage'=>"1","product.is_featured"=>"0","pro_cat.category_id"=>$cat_id);
			$product = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
			//Top rated products.
			$where = array('preview.rating >'=>3,'product.is_private' =>'0',"product.is_featured"=>"0","pro_cat.category_id"=>$cat_id);
			$topRated = $this->Product_Model->frontProductDetails('','','','','','8','rating DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
			//Category wise products.
			$category = array();
			$i=0;
			$c_id = $this->getAllCategoriesChildId($cat_id);
			if($c_id == "" || $c_id->category_ids === NULL){
				$c_id = $cat_id;
			}
			//echo $c_id;
			$where = 'cat.category_id IN('.$c_id.') AND product.is_private="0" AND cat.is_homepage="1" AND product.is_featured="0"';
			$category[$i] = $this->Product_Model->frontProductDetails('','','','','','8','pin.approved_date DESC',$where,'product.product_id',$select,'',$this->region,false,$user_id);
			$category[$i]['cat_name'] = $cat_name;
			$category[$i]['is_slider'] = 0;
			//echo "<pre>";print_r($category);die();
			if($user_id){
				$this->data['check_email_verif']	= $this->Secure_Model->check_email_verification($user_id);
				$this->data['wishlist_categories']	= $this->Secure_Model->WishlistViaCategories($user_id);
				$this->data['wishlist_check']		= "";//$this->Secure_Model->ClearWishlistIfNoCategories($user_id);
				$this->data['text_notification']	= $this->Secure_Model->checkMsgNotification($user_id);
			}
			$banner = $this->Banner_Model->getBannerForFront("","banner_image,banner_link,product_link",$cat_id);
			if(empty($banner) && $cat_id !=""){
				$banner = $this->Banner_Model->getBannerForFront("","banner_image,banner_link,product_link");
			}
			$this->data['banner']					= $banner;
			//echo $cat_id;
			//print_r($this->data["banner"]);die();
			$this->data['check_email_verif']		= FALSE;
			$this->data['wishlist_categories']		= FALSE;
			$this->data['wishlist_check']			= FALSE;
			$this->data['text_notification']		= FALSE;
			$this->data['page_name'] 				= 'home';
			$this->data['title'] 					= $this->config->item("web_name");
			$this->data['show_on_homepage'] 		= (!empty($category))?$category:array();
			$this->data['product']					= (!empty($product))?$product:array();
			$this->data['topRated']					= (!empty($topRated))?$topRated:array();
			$this->data['featured'] 				= (!empty($featured))?$featured:array();
			$this->data['hasScript'] 				= true;
			$this->data['newsletter'] 				= true;
			$this->data['hasStyle'] 				= false;
			$this->data['slug']						= $slug;
		}else{
			if($this->input->get('keywords') !=""){
				$keywords = $this->input->get('keywords');
			}
			if($this->input->get('fs') != ""){
				$ship = $this->input->get('fs');
			}
			// echo $param; die();
			if(isset($_GET["search"]) && $this->input->get("search")==""){
				redirect(base_url());
			}
			if($this->input->get("search")!="" ){
				$prod_search = stripslashes(trim($this->input->get("search")));
				$link .="&search=".stripslashes($prod_search);
				if($param == ""){
					$param .='?search='.stripslashes($prod_search);
				}else{
					$param .='&search='.stripslashes($prod_search);
				}
				if($this->session->userdata('userid')){
					$userid = $this->session->userdata('userid');
				}else{
					$userid="";
				}
				$this->load->library('user_agent');
				$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
				$meta_info = $this->agent->agent_string();
				$os = $this->agent->platform();
				$platform = 'W';
				$session_id = $this->session->session_id;
				$this->User_Model->keywordSearch($prod_search,$_SERVER['REMOTE_ADDR'],$referer_url,$userid,'keywords', $platform, $meta_info, $os, $session_id);
				$type = 'keywords';
				$this->add_log($prod_search, $type);
			}

			// if(strpos($order, "price") === true){

			// }
			$config["base_url"] = base_url($slug.$param);
			// echo $config["base_url"]."<br>"."slug: ".$slug."<br>"."param: ".$param; die();
			$plus = 1;
			if($prod_search){
				$prod_search = urlClean(str_replace('-',' ',$prod_search));
				$word = explode(" ",$prod_search);
				$prod_search = "";
				foreach($word as $w){
					if(strlen($w) < 3){
						$prod_search .= $w."__* ";
					}else{
						if($plus==1){
							$prod_search .="+";
							$plus++;
						}
						$prod_search .= $w." ";
					}
				}
				$prod_search = trim($prod_search);
			}
			// var_dump($brand_id,$category_id,$prod_search,$range,$ship);
			$count=$this->Product_Model->getProductListCount($brand_id,$category_id,$prod_search,$range,$ship);
			// print_r($count); die();
			if($category_id == ""){
				$category_id = $count->cat_id;
			}
			$total_row 					 = $count->total;
			$config["total_rows"] 		 = $total_row;
			$config["per_page"] 		 = 12;
			$config['use_page_numbers']  = TRUE;
			$config['num_links'] 		 = 1;
			$config['cur_tag_open'] 	 = '<li class="active text-center"><a class="page-link current">';
			$config['cur_tag_close'] 	 = '</a></li>, ';
			$config['next_link'] 		 = '<span class=""><i class="fa fa-angle-right" style="font-size:20px"></i></span>';
			$config['prev_link'] 		 = '<span class=""><i class="fa fa-angle-left" style="font-size:20px"></i></span>';
			$config['num_tag_open'] 	 = '<li class="text-center">';
			$config['num_tag_close'] 	 = '</li>';
			$config['first_link'] 		 ='First';
			$last_page_number 			 = ceil($config["total_rows"]/$config["per_page"]);
			$config['last_link']  		 = $last_page_number;
			$config['prev_tag_open'] 	 = '<li class="pg-tag text-center">';
			$config['prev_tag_close'] 	 = '</li>';
			$config['next_tag_open'] 	 = '<li class="pg-tag-next text-center">';
			$config['next_tag_close'] 	 = '</li>';
			$config['first_tag_open'] 	 = '<li class="text-center">';
			$config['first_tag_close'] 	 = '</li>';
			$config['last_tag_open'] 	 = '<li class="text-center">';
			$config['last_tag_close'] 	 = '</li>';
			$config['page_query_string'] = TRUE;
			$this->pagination->initialize($config);
			if($this->input->get('page')){
				$page = $this->input->get('page');
			}else{
				$page = 1;
			}
			if($page > $last_page_number){
				$page = $last_page_number;
			}
			$productData= $this->Product_Model->getProductList($brand_id,$category_id,$prod_search,$page,$config["per_page"],$order,$range,"","","",$user_id,$ship);
			//echo "<pre>";print_r($productData);die();
			$i=0;
			if($category_id && $count->p_id){
				$brand_and_categories = $this->Product_Model->getBrandAndCategory($count->p_id,$category_id);
				// echo"<pre>"; print_r($brand_and_categories['brands']); die();
			}else{
				$brand_and_categories['brands'] = "";
				$brand_and_categories['categories'] = "";
			}
			$this->data['bradcrumbs'] 			= $bradcrumb;
			$this->data['brandData'] 			= $brand_and_categories['brands'];
			$this->data['category'] 			= $brand_and_categories['categories'];
			$this->data['productData'] 			= $productData;
			$str_links 							= $this->pagination->create_links();
			$links["links"] 					= explode(', ', $str_links);
			$this->data['links'] 				= $links;
			$this->data['link'] 				= $link;
			$this->data['max']					= $this->Product_Model->get_max_price();
			$this->data['wishlist_categories'] 	= $this->Secure_Model->WishlistViaCategories($user_id);
			$this->data['wishlist_check'] 		= "";//$this->Secure_Model->ClearWishlistIfNoCategories($user_id);
			$this->data['itemsFound'] 			= $total_row;
		}
		if(isset($bradcrumb[0]['cat_name'])){
			$this->data['title'] = "Shop ".$bradcrumb[0]['cat_name']." at Zab.ee";
		}else{
			$this->data['title'] = $this->config->item('title');
		}
		// print_r($this->data['link']);

		$this->load->view('front/template', $this->data);
	}

	public function productOfferListing($id=""){
		$this->data['hasScript'] 		= true;
		$this->data['hasStyle'] 		= false;
		$this->data['title'] 			= "Products Offer Listing";
		$this->data['condition_active'] = "";
		$this->data['newsletter'] 		= false;
		$this->data['page_name'] 		= 'offerlisting';
		if($id !=""){
			$userid = (isset($this->userData["userid"]))?$this->userData["userid"]:"";
			$detail_id = $id;
			$id = explode('-',$id);
			$product_id = end($id);
			$condition_id = "";
			$shipping = "";
			$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>1),"","","is_cover DESC","",1);
			if($this->input->get('condition') !=""){
				$condition_id = $this->input->get('condition');
				$condition_id = base64_decode(urldecode($condition_id));
				$this->data['condition_active'] = $condition_id;
			}else if($this->input->get('filter') !=""){
				$filter = array();
				$filter = $this->input->get('filter');
				$filter = base64_decode(urldecode($filter));
				$condition_id = explode(',',$filter);
			}else if($this->input->get('shipping') !=""){
				$shipping = array();
				$shipping = $this->input->get('shipping');
				$shipping = base64_decode(urldecode($shipping));
				if($shipping){
					$shipping = (int)$shipping;
				}
			}
			$product =$this->Product_Model->getProductVariantData($product_id,$condition_id,"","","","",$shipping);
			$shippingData = array();
			foreach($product['gpvd'] as $productshipping){
				$product_name = $productshipping->product_name;
				if($userid){
					$productshipping->already_saved = $this->Product_Model->already_saved($userid, $productshipping->product_id,$productshipping->pv_id);
				}
				else{
					$productshipping->already_saved = "";
				}
				if($productshipping->shipping_ids != ""){
					$lowestShippingId =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id",$productshipping->shipping_ids,"shipping_id",'','price',"","1",1);
					$productshipping->lowestShipping_id = isset($lowestShippingId[0]->shipping_id)?$lowestShippingId[0]->shipping_id:"";
					$shippingData[] = $productshipping->shipping_ids;
				}
			}
			$shipping_ids = implode(',',$shippingData);
			$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","shipping_id,title,price,incremental_price,free_after",$shipping_ids,"shipping_id",'','price',"","",1);
			$productConditions = $this->Product_Model->getData(DBPREFIX.'_product_inventory p',"pc.condition_id,pc.`condition_name`",'p.product_id='.$product_id.' AND (p.quantity - p.sell_quantity) > 0 AND p.seller_id !=1 AND p.approve="1"','','p.condition_id','p.condition_id',array('tbl_product_conditions pc'=>'pc.condition_id = p.condition_id'));
			$this->data['wishlist_categories'] 	= $this->Secure_Model->WishlistViaCategories($userid);
			$this->data['ProductData'] 			= $product['gpvd'];
			$this->data['productConditions'] 	= $productConditions;
			$this->data['shippingData'] 		= $shippingData;
			$this->data['detail_id'] 			= $detail_id;
			$this->data['productName'] 			= isset($product_name)?$product_name:"";
			$this->data['productImage'] 		= isset($productImage[0]) ? $productImage[0] : "";
			//echo "<pre>";print_r($this->data['productImage']);die();
			$this->load->view('front/template', $this->data);
		}
	}

	public function saveMessage(){
		$sender_id = "";
		if(isset($this->userData["userid"])){
			$sender_id = $this->userData["userid"];
		}else{
			$sender_id = $this->userData["admin_id"];
		}
		$data = array('sent_datetime'=>$this->input->post('time'),'sender_id'=>$sender_id,'receiver_id'=>$this->input->post('receiver_id'),'message'=>$this->input->post('message'),'subject'=>$this->input->post('subject'),'item_type'=>$this->input->post('item_type'), 'item_id'=>$this->input->post('item_id'),'seller_id'=>$this->input->post('seller_id'),'buyer_id'=>$this->input->post('buyer_id'),'product_variant_id'=>$this->input->post('product_variant_id'));
		$result = $this->User_Model->saveData($data,'tbl_message');
		if($result != false){
			echo json_encode(array('status'=>'1','messages'=>$result));
		}else{
			echo json_encode(array('status'=>'0','error'=>'Message not found.'));
		}
	}

	public function get_profile_image($id=""){
		if($id == ""){
			if($this->input->get('image') != ""){
			$id = $this->input->get('image');
			}
			else{
				$id = "defaultprofile.png";
			}
		}
		$url = product_path($id);
		header("Content-Type: image/png");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
		$res = curl_exec($ch);
		$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($rescode == 200){
			curl_close($ch) ;
			echo $res;
		} else {
			$url = base_url('assets/uploads').'/'.($this->input->get('map'))?'blank.png':'default_thumb.png';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
			$res = curl_exec($ch);
			$rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch) ;
			echo $res;
		}
	}

	public function qna(){
		if($this->isloggedin){
			if(!empty($this->input->post()) && $this->input->post() != ""){
				$data = $this->input->post();
				if($data['btn'] == "ans"){
					$result = $this->Product_Model->saveAnswer($data);
				}
				if($data['btn'] == "ques"){
					$result = $this->Product_Model->saveQuestion($data);
				}
				if($result){
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		} else {
			redirect('login');
		}
	}

	public function getProductData(){
		extract($_POST);
		// echo"<pre>";print_r($this->input->post()); die();
		$gpvd = "";
		$user_id = (isset($_SESSION['userid']))?$_SESSION['userid']:"";
		if($product_id !=""){
			if(is_array($variant)){
				sort($variant);
				$variant = implode(',',$variant);
			}
			$gpvd = $this->Product_Model->getProductVariantData($product_id,$condition_id,$variant,"","",1,"");
			if($gpvd['gpvdRows'] ==0){
				$gpvd = $this->Product_Model->getProductVariantData($product_id,$condition_id,$variant,1,"",1,"",$selected_variant);
				//echo "<pre>";print_r($gpvd);die();
				if($gpvd['gpvdRows'] ==0){
					$gpvd = $this->Product_Model->getProductVariantData($product_id,$condition_id,$variant,1,1,1,"",$selected_variant);
				}
				$variant = $gpvd['gpvd'][0]->variant_group;
			}
			//echo "<pre>";print_r($gpvd);die();
			$product_variant_id = $gpvd['gpvd'][0]->pv_id;
			$seller_product_id = $gpvd['gpvd'][0]->sp_id;
			$seller_id = (isset($gpvd['gpvd'][0]->seller_id))?$gpvd['gpvd'][0]->seller_id:"";
			if($user_id){
				$already_saved=$this->Product_Model->already_saved($user_id, $product_id,$product_variant_id);
			}
			else{
				$already_saved = 0;
			}
			// reviews of this Product
			$this->load->model('Reviewmodel');
			$reviews = $this->Reviewmodel->getdata($seller_product_id,$product_variant_id,$product_id,4);
			// $reviews['total_stars'] = $this->Reviewmodel->getdata($seller_product_id,$product_variant_id,$product_id);
			// echo "<pre>"; print_r($reviews); die();
			$questions = $this->Product_Model->getQuestions($seller_product_id);
			//echo $variant;
			$checkVariantGroup = explode(",",$gpvd['gpvd'][0]->variant_group);
			if(count($checkVariantGroup) == "1"){
				$variant = "";
			}
			$allProductVariant = $this->Product_Model->allProductVariantByConditionId($product_id,$gpvd['gpvd'][0]->condition_id,$variant);
			// Product Image
			if($gpvd['gpvd'][0]->condition_id == "1"){
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>$gpvd['gpvd'][0]->condition_id),"","","is_cover DESC");
			}else{
				$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'sp_id'=>$gpvd['gpvd'][0]->sp_id),"","","is_cover DESC");
				if(empty($productImage)){
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>1),"","","is_cover DESC");
				}
			}
			$shipping_id = ($gpvd['gpvd'][0]->inventory_shipping)?$gpvd['gpvd'][0]->inventory_shipping:$gpvd['gpvd'][0]->shipping_ids;
			$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","*",$shipping_id,"shipping_id",'','price',"","",1);
			$index = 0;
			foreach($reviews['result'] as $r){
				$date = date_create($r['date']);
				$reviews['result'][$index]['date'] = date_format($date, "Y-m-d");
				$index++;
			}
			$gpvd['apvci'] 			= $allProductVariant;
			$gpvd['apci'] 			= $productImage;
			$gpvd['shippingData'] 	= $shippingData;
			$gpvd['avgRating'] 		= $this->Reviewmodel->avgRating($product_id,$seller_product_id,$product_variant_id);
			$gpvd['reviews'] 		= $reviews;
			$gpvd['questions'] 		= $questions;
			$gpvd['already_saved'] 	= ($already_saved)?1:"";
		}
		echo json_encode($gpvd);
	}

	public function saveProductHistory(){

		if($this->session->userdata('userid')){
			$userid = $this->session->userdata('userid');
		}else{
			$userid="";
		}
		$type = 'product';
		$this->add_log($this->input->post('product_variant_id'), $type);
	}

	public function test(){
		//$product = $this->Product_Model->productDetails('','','23');
		//echo "<pre>";print_r($product);
	}

	public function review(){
		$response = array('status'=>0,'message'=>'Failed', 'code'=>'000');
		$this->load->model('Reviewmodel');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name','Name','trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('pdt', 'Product Name', 'trim|required');
		$this->form_validation->set_rules('review', 'Review', 'trim|required');
		$this->form_validation->set_rules('rating', 'Rating', 'trim|required');
		$this->form_validation->set_rules('pv_id', 'Product ID', 'trim|required');
		if ($this->form_validation->run() == FALSE){
			$response['message'] = "";
			$response['code'] = '001';
		} else {
			$email = $this->input->post('email');
			$pdt = $this->input->post('pdt');
			$product_id = $this->input->post('product_id');
			$seller_id = $this->input->post('seller_id');
			$reviewCheck = $this->Reviewmodel->getUserByEmail($email,$product_id);
			if($reviewCheck){
				$this->session->set_flashdata('email_error',1);
				$response['message'] = 'You can not submit review on same product twice.';
				$response['code'] = '002';
			} else {
				$name = $this->input->post('name');
				$date = $this->input->post('date');
				$review = $this->input->post('review');
				$rating = $this->input->post('rating');
				$pv_id = $this->input->post('pv_id');

				$data = array(
					'name' 			=> $name,
					'date' 			=> date('Y-m-d H:i:s UTC', strtotime($date)),
					'email' 		=> $email,
					'review' 		=> $review,
					'product_name' 	=> $pdt,
					'rating' 		=> $rating,
					'pv_id' 		=> $pv_id,
					'product_id'	=> $product_id
				);
				$id = $this->Reviewmodel->insert($data);
				if($id){
					$response['message'] = '';
					$response['status'] = 1;
				} else {
					$response['message'] = 'INSERT ERROR';
					$response['code'] = '003';
					$response['sql'] = $this->db->last_query();
				}
			}
		}
		header("Content-type:application/json");
		echo json_encode($response);
	}

	public function save_for_later(){
		$date = $this->input->post('created_date');
		$data = array('user_id'=>$this->input->post('user_id'),'product_id'=>$this->input->post('product_id'),'pv_id'=>$this->input->post('product_variant_id'),'created_date'=> date('Y-m-d H:i:s', strtotime($date)));
		$result = $this->User_Model->save_for_later($data);
		if($result){
			echo json_encode(array('status'=>'1','sucess'=>'Added to wishlist'));
		}else{
			echo json_encode(array('status'=>'0','error'=>'Not added to wishlist'));
		}
	}

	public function save_for_later_2(){
		$post_data = $this->input->post();
		$result = $this->User_Model->check_save_for_later($post_data);
		if($result){
			echo json_encode(array('status'=>'1','sucess'=>'proceed to modal'));
		}else{
			echo json_encode(array('status'=>'0','error'=>'Not added to wishlist'));
		}
	}

	public function saved_for_later($categoryId = ""){
		$this->load->library('pagination');
		if(!$this->isloggedin){
			redirect(base_url('login?required_login=1&redirect=buyer'),"refresh");
		}
		$user_id = $this->session->userdata('userid');
		$selWishCat = $this->Product_Model->selectWishlistCategories($user_id);
		$getCategoryNames = $this->Product_Model->getCategoryNames($user_id);
		$productsPerCategory = $this->Product_Model->CountProductsPerCategory($user_id, $selWishCat);
		if($categoryId == ""){
			redirect(base_url('product/saved_for_later/all'));
		}
		$config = array();
		$link= "";

		$user_type = $this->session->userdata('user_type');
		if($categoryId == "all"){
			$get_saved = $this->Product_Model->count_saved($user_id);
		} else {
			$get_saved = $this->Product_Model->count_saved($user_id, $categoryId);
		}
		 $url=strtok($_SERVER["REQUEST_URI"],'?');
		 $link=$_SERVER['REQUEST_URI'];
		 if($this->input->get('page')){
			if(strpos($_SERVER['REQUEST_URI'],'?page='.$this->input->get('page'))){
				 $link = str_replace('?page='.$this->input->get('page'), '', $_SERVER['REQUEST_URI']);
			} else {
				 $link = str_replace('&page='.$this->input->get('page'), '', $_SERVER['REQUEST_URI']);
			}
		 }
		$config["base_url"] 		 = $link;
		$count 						 = $get_saved['total_rows'];
		$config["total_rows"] 		 = $count;
		$config["per_page"] 		 = 6;
		$config['use_page_numbers']  = TRUE;
		$config['num_links'] 		 = 1;
		$config['cur_tag_open'] 	 = '<li class="active"><a class="page-link current">';
		$config['cur_tag_close'] 	 = '</a></li>, ';
		$config['next_link'] 		 = '<span class="styleAngle">&gt</span>';
		$config['prev_link'] 		 =  '<span class="styleAngle">&lt</span>';
		$config['num_tag_open'] 	 = '<li>';
		$config['num_tag_close'] 	 = '</li>';
		$config['prev_tag_open'] 	 = '<li class="pg-tag">';
		$config['prev_tag_close'] 	 = '</li>';
		$config['next_tag_open'] 	 = '<li class="pg-tag">';
		$config['next_tag_close'] 	 = '</li>';
  		$config['first_tag_open'] 	 = '<li>';
		$config['first_tag_close'] 	 = '</li>';
		$config['last_tag_open'] 	 = '<li>';
		$config['last_tag_close'] 	 = '</li>';
		$config['page_query_string'] = TRUE;
		$this->pagination->initialize($config);
		if($this->input->get('page')){
			$page = $this->input->get('page');
		}else{
			$page = 1;
		}
		if($this->input->post('myselect')){
			$data = $this->Product_Model->get_by_subcatid($user_id,$page,$config['per_page'], $this->input->post('myselect'));
			$this->data['selected'] = $this->input->post('myselect');
		} else {
			if($categoryId == "all"){
				// echo"here"; die();
				$data = $this->Product_Model->saved_for_later($user_id,$page,$config['per_page']);
				// echo"<pre>"; print_r($data); die();
			} else {
				$data = $this->Product_Model->saved_for_later($user_id,$page,$config['per_page'], $categoryId);
			}
		}
		$totalProducts = $this->Product_Model->CountTotalProductsInWishlist($user_id);
		foreach($data['data'] as $d){
			$product_id = $d->product_id;
			$sp_id = $d->sp_id;
			if($d->is_primary_image == ""){
				$d->is_primary_image = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product_id,'condition_id'=>1),"","","is_cover DESC");
			}
			//if($d->seller_product_description == ""){
				$sd = $this->Product_Model->getData(DBPREFIX.'_product AS prd',"prd.`product_description` as `seller_product_description`, dis.valid_from, dis.valid_to, dis.type, dis.value",array('pin.product_variant_id'=>$d->pv_id),"0","prd.product_id", "", array("tbl_product_inventory AS pin" => "prd.product_id = pin.product_id", "tbl_policies AS dis" => "pin.discount = dis.id AND dis.display_status = '1'"),"","","","",false,array("tbl_policies AS dis"=>"LEFT"));
				// echo"<pre>"; print_r($sd); die();
				//$d->seller_product_description = $sd[0]->seller_product_description;
				$d->valid_from = $sd[0]->valid_from;
				$d->valid_to = $sd[0]->valid_to;
				$d->type = $sd[0]->type;
				$d->value = $sd[0]->value;
			//}
		}
		if(empty($data['data']) && $page > 1){
			redirect(base_url('wish_list'),"refresh");
		}
		$str_links 							= $this->pagination->create_links();
		$links["links"] 					= explode(', ', $str_links);
		$this->data['links'] 				= $links;
		$this->data['selWishCat'] 			= $selWishCat;
		$this->data['getCategoryNames'] 	= $getCategoryNames;
		$this->data['totalProducts'] 		= $totalProducts;
		$this->data['productsPerCategory'] 	= $productsPerCategory;
		$this->data['page_name']			= 'saved_for_later';
		$this->data['hasScript']			= true;
		$this->data['showSidepanel']		= true;
		$this->data['title'] 				= "Wish List";
		$this->data['active_page'] 			= 'saved_for_later';
		$this->data['newsletter'] 			= false;
		$this->data['save_for_later_data'] 	= $data;
		// echo"<pre>"; print_r($data); die();
		$this->load->view('front/template', $this->data);
	}

	public function get_saved_prod(){
		$user_id = $this->input->post('user_id');
		$search = $this->input->post('search');
		$request = $this->input->post();
		$user_type = $this->session->userdata('user_type');
		$ids=$this->Product_Model->getData('tbl_wishlist',"GROUP_CONCAT(product_id) AS product_id, GROUP_CONCAT(pv_id) AS pv_id",array('user_id'=>$user_id));
		$where = 'pin.`product_id` IN ("") AND pin.`product_variant_id` IN ("")';
		$data = $this->Product_Model->saved_for_later($search['value'],$request,$where,$user_id);
		$prdouct_link;
		foreach($data['data'] as $d){
			$product_link = $d->product_name."-".$d->category_name."-".$d->brand_name."-".$d->condition_name;
			$product_link = urlClean(str_replace(' ','-',$product_link));
			$prdouct_link =  base_url().'product/detail/'.$product_link.'-'.encodeProductID($d->product_name,$d->product_id).'/'.$d->pv_id;
			$d->product_link = $prdouct_link;
		}
		echo json_encode($data);
	}

	public function delete_from_list()
	{
		$wish_id=$this->input->post('wish_id');
		$result = $this->Product_Model->delete_from_list($wish_id);
		echo json_encode($result);
	}

	public function delete_from_list_cart()
	{
		$wish_id=$this->input->post('wish_id');
		$result = $this->Product_Model->delete_from_list($wish_id);
		echo json_encode($result);
	}

	public function minicart(){
		$this->load->view('front/minicart');
	}

	public function productDetails($slug = "",$product_variant_id = ""){
		$this->data['hasScript'] = true;
		$this->data['hasStyle'] = true;
		$this->data['newsletter'] = true;
		$this->load->model('Utilz_Model');
		$this->load->model('Reviewmodel');
		$this->data['title'] = "Products Details";
		if($this->input->get('ref') != ""){
			$this->Product_Model->impression_increment($this->input->get('ref'));
		}
		$productData = array();
		$prVariant = array();
		$total_seller="";
		if($slug !=""){
			// echo $slug;
			$id = $this->Product_Model->getIdBySlug(DBPREFIX."_product","product_id",array("is_active"=>"1","is_declined"=>"0","slug"=>$slug));
			// echo"<pre>"; print_r($id); die();
			if(!isset($id->product_id)){
				set_status_header(404);
				$this->data['hasScript'] = false;
				$this->data['heading'] = "Error 404!";
				$this->data['message'] = "Product Not Found";
				$this->data['page_name'] = 'error_404';
				$this->load->view('front/template', $this->data);
			}else{
				$id = $id->product_id;
				$p_variant_id = $this->Product_Model->get_pv_id($id);
				// echo "<pre>"; print_r($p_variant_id); die();
				$seller_ids = $this->Product_Model->get_seller_ids($id);
				if($product_variant_id==""){
					$product_variant_id = $p_variant_id['pv_id'];
				}
				$total_seller = $p_variant_id['total_seller'];
				$querySelect = "";
				if($this->session->userdata('userid')){
					$userid = $this->session->userdata('userid');
					$querySelect = ", w.wish_id as already_saved";
				}else{
					$userid="";
				}
				$referer_url = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:"";
				$product = $this->Product_Model->productDetails($id,$this->region,$product_variant_id,$userid);
				//echo"<pre>"; print_r($product); die();
				if($product['productDataRows'] > 0){
					$bradcrumb = $this->getAllCategoriesParentId($product['productData']->category_id);
					$product_id = $product['productData']->product_id;
					$seller_id = $product['productData']->seller_id;
					$seller_product_id = $product['productData']->sp_id;
					$condition_id = $product['productData']->condition_id;
					$questions = $this->Product_Model->getQuestions($seller_product_id);
					$this->data['questions'] = $questions;
					//Other Seller Sell This Product
					$select = 'product.product_name,product.product_id,product.slug,pm.thumbnail as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,AVG(preview.`rating`) AS rating,pin.price, sp.seller_id, d.value as discount_value, d.type as discount_type, d.valid_from as valid_from, d.valid_to as valid_to, pc.condition_name, ss.store_name'.$querySelect;
					$where = array('sp.seller_id !='=>$seller_id,'product.product_id'=>$id,'product.is_private' => '0');
					$otherSeller = $this->Product_Model->frontProductDetails('','','','','','','pin.price DESC',$where,'pin.condition_id',$select,'',$this->region,false,$userid);
					// echo"<pre>".$this->db->last_query(); die();
					//Related Products.
					//$select = 'product.product_name,product.product_id,pm.thumbnail as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,AVG(preview.`rating`) AS rating,pin.price, sp.seller_id'.$querySelect;
					$where = array('product.brand_id '=>$product['productData']->brand_id,'product.sub_category_id'=>$product['productData']->category_id,'product.product_id !='=>$product_id,'product.is_private' => '0');
					$relatedProduct = $this->Product_Model->frontProductDetails('','','','','','','sp.created_date DESC',$where,'product.product_id',$select,'',$this->region,false,$userid);
					// echo"<pre>"; print_r($this->db->last_query()); die();
					//Accessories For This Product
					$accessories_id = $this->Product_Model->getData(DBPREFIX.'_product_accessories pa',"GROUP_CONCAT(accessory_id) as accessory_id",array('pa.product_id'=>$product_id, 'pa.is_active'=>'1'),"","","",array(DBPREFIX."_product p"=>"p.product_id = pa.product_id", DBPREFIX."_brands b"=>"p.brand_id = b.brand_id AND b.blocked = '0'"),"","","","",true);
					if($accessories_id->accessory_id){
						$accessoryArray = explode(",",$accessories_id->accessory_id);
						//$select = 'product.product_name,product.product_id,pm.thumbnail as product_image,pm.is_local,sp.sp_id AS seller_product_id,pin.product_variant_id as pv_id,AVG(preview.`rating`) AS rating,pin.price, sp.seller_id'.$querySelect;
						$where = array('product.is_private' => "0");
						$accessories = $this->Product_Model->frontProductDetails($accessoryArray,'','','','','','sp.created_date DESC',$where,'product.product_id',$select,'',$this->region,false,$userid);
					}else{
						$accessories = array("rows"=>0,"result"=>"");
					}
					if($product_variant_id ==""){
						$product_variant_id = $product['productData']->pv_id;
					}
					$productVariants = $this->Product_Model->getData(DBPREFIX.'_seller_product_variant spv',"v.v_id,spv.pv_id",array('spv.pv_id'=>$product_variant_id),"0","v.v_cat_id","",array("tbl_variant v"=>"v.`v_id` = spv.v_id "));
					// Product Image
					if($condition_id == "1"){
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product['productData']->product_id,'condition_id'=>$condition_id),"","","is_image,position");
					}else{
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image",array('product_id'=>$product['productData']->product_id,'sp_id'=>$product['productData']->sp_id),"","","is_image,position");
						if(empty($productImage)){
							$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product['productData']->product_id,'condition_id'=>1),"","","is_image,position");
						}
					}
					//Product Shipping
					//print_r($product['productData']->shipping_ids);die();
					if($product['productData']->shipping_ids != ""){
						$shipping_id = ($product['productData']->inventory_shipping !="")?$product['productData']->inventory_shipping:$product['productData']->shipping_ids;
						$shippingData =  $this->Product_Model->getData(DBPREFIX."_product_shipping","*",$shipping_id,"shipping_id",'','price',"","",1);
					}else{
						$shippingData = array();
					}
					//Product Features
					$productFeatures = $this->Product_Model->getData(DBPREFIX.'_product_features',"feature",array('product_id'=>$product_id/*,'seller_id'=>$seller_id*/));
					$sp_id = explode(',',$product['productData']->sp_id);
					// All different seller's variants of this product.
					$productAllVariants = $this->Product_Model->getData(DBPREFIX.'_seller_product_variant spv', "v.v_id, v.v_cat_id, vc.v_cat_title, v.v_title, spv.*", "SELECT sp_id FROM tbl_seller_product WHERE product_id=".$id, 'spv.sp_id', 'spv.v_id','',array('tbl_variant v'=>'v.v_id = spv.v_id','tbl_variant_category vc '=>'vc.v_cat_id = v.v_cat_id','tbl_product_inventory AS pin'=>'pin.product_variant_id = spv.pv_id AND (pin.quantity - pin.sell_quantity) > 0 AND pin.approve = "1" AND pin.seller_id != "1"'),'',1);
					// All different seller's product conditions.
					$productConditions = $this->Product_Model->getData(DBPREFIX.'_product_inventory p',"pc.condition_id,pc.`condition_name`, p.seller_id",'p.product_id='.$product['productData']->product_id.' AND (p.quantity - p.sell_quantity) > 0 AND p.seller_id !=1 AND p.approve="1"','','p.condition_id','p.condition_id',array('tbl_product_conditions pc'=>'pc.condition_id = p.condition_id'));
					$proAllVariant= array ();
					if(!empty($productAllVariants)){
						foreach($productAllVariants as $pv){
							$proAllVariant[$pv->v_cat_title][] = array('variant_title'=>$pv->v_title,
																	   'variant_id'=>$pv->v_id,
																	   'variant_cat_id'=>$pv->v_cat_id,
																	   'seller_product_id'=>$pv->sp_id,
																	   'seller_product_variant_id'=>$pv->spv_id);
						}
					}
					if(!empty($productVariants)){
						foreach($productVariants as $pv){
							$prVariant[] = $pv->v_id;
						}
					}
					$productData['product_variant_id'] 		= $product_variant_id;
					$productData['product'] 				= $product['productData'];
					$productData['productVariant'] 			= $prVariant;
					$productData['productImage'] 			= $productImage;
					$productData['productAllVariants'] 		= $proAllVariant;
					$productData['productConditions'] 		= $productConditions;
					$productData['relatedProduct'] 			= $relatedProduct;
					$productData['otherSeller'] 			= $otherSeller;
					$productData['productFeatures'] 		= $productFeatures;
					$productData['productAccessories'] 		= $accessories;
					if($total_seller){
						$productData['product']->total_seller = $total_seller;
					}
					$this->data['title'] 								= $product['productData']->product_name;
					$this->data['page_name'] 							= 'productdetails';
					$this->data['viewProductData'] 						= $productData;
					$this->data['bradcrumbs'] 							= $bradcrumb;
					$this->data['countryList'] 							= $this->Utilz_Model->countries();
					$this->data['reviews'] 								= $this->Reviewmodel->getdata($seller_product_id,$product_variant_id,$id,4);
					$this->data['reviews']['total_rating'] 				= $this->Reviewmodel->getdata($seller_product_id,$product_variant_id,$id);
					$this->data['avgRating'] 							= $this->Reviewmodel->avgRating($id,$seller_product_id,$product_variant_id);
					$this->data['questionsNew'] 						= $this->Reviewmodel->questionsAsked($id,$seller_product_id,$product_variant_id);
					$this->data['user_id'] 								= $userid;
					$this->data['shippingData'] 						= $shippingData;
					$this->data['seller_ids'] 							= $seller_ids;
					$this->data['wishlist_categories']					= $this->Secure_Model->WishlistViaCategories($userid);
					$this->data['wishlist_fav']							= $this->Secure_Model->AllWishlistData($product_id, $product_variant_id);
					$this->data['low_and_high_price'] 					= $this->Product_Model->getMinAndMaxPrice($product_id);
					//echo "<pre>";print_r($this->data['reviews']);die();
					if(isset( $_SESSION['RecentlyViewed']['id']) &&  $_SESSION['RecentlyViewed']['id']!="")
					$match= array_search($id, $_SESSION['RecentlyViewed']['id']);
					if(isset($match) && $match !==FALSE){
						array_splice($_SESSION['RecentlyViewed']['id'], $match, 1);
					}
					$_SESSION['RecentlyViewed']['id'][]=$id;
					$prod_count=sizeof($_SESSION['RecentlyViewed']['id']);
					if($prod_count>6){
						array_splice($_SESSION['RecentlyViewed']['id'], 0, 1);
					}
					$type = 'product';
					$this->add_log($product_variant_id, $type);
					$this->load->view('front/template', $this->data);

				}else{
					$this->data['hasScript'] = false;
					$this->data['heading'] = "Error 404!";
					$this->data['message'] = "Product Not Found";
					$this->data['page_name'] = 'error_404';
					$this->load->view('front/template', $this->data);
					// redirect(base_url("product/".$slug));
				}
			}
		}
	}

	public function listview_pagination(){
		$store = $_SERVER['HTTP_REFERER'];
		$url_link = "";
		if($this->input->post('search') != ""){
			if($url_link == ""){
				$url_link .= '?search='.$this->input->post('search');
			}else{
				$url_link .= '&search='.$this->input->post('search');
			}
		}
		if($this->input->post('brand_search') != ""){
			if($url_link == ""){
				$url_link .= '?brands_search='.$this->input->post('brand_search');
			}else{
				$url_link .= '&brands_search='.$this->input->post('brand_search');
			}
		}
		if($this->input->post('cat_search') != ""){
			if($url_link == ""){
				$url_link .= '?category_search='.$this->input->post('cat_search');
			}else{
				$url_link .= '&category_search='.$this->input->post('cat_search');
			}
		}
		if($this->input->post('range') != ""){
			if($url_link == ""){
				$url_link .= '?price_range='.$this->input->post('range');
			}else{
				$url_link .= '&price_range='.$this->input->post('range');
			}
		}
		if($this->input->post('name_search') != ""){
			if($url_link == ""){
				$url_link .= '?product_name='.$this->input->post('name_search');
			}else{
				$url_link .= '&product_name='.$this->input->post('name_search');
			}
		}
		if($this->input->post('price_order') != ""){
			if($url_link == ""){
				$url_link .= '?min_price='.$this->input->post('price_order');
			}else{
				$url_link .= '&min_price='.$this->input->post('price_order');
			}
		}
		if($this->input->post('shipping') != ""){
			if($url_link == ""){
				$url_link .= '?fs='.$this->input->post('shipping');
			}else{
				$url_link .= '&fs='.$this->input->post('shipping');
			}
		}
		if($this->input->post('page')){
			if($this->input->post('page') != ""){
				if($url_link == ""){
					$url_link .= '?page='.$this->input->post('page');
				}else{
					$url_link .= '&page='.$this->input->post('page');
				}
			}else{
				if($url_link == ""){
					$url_link .= '?page=1';
				}else{
					$url_link .= '&page=1';
				}
			}
		}
		if(strpos($store, 'store') !== false){
			$url = explode('?', $store);
			redirect($url[0].$url_link);
			die();
		}
		$slug = explode('/', parse_url($store, PHP_URL_PATH));
		$slug_count = count($slug) - 1;
		$slug = $slug[$slug_count];
		redirect(base_url().$slug.$url_link);
	}

	public function updateView(){
		$_SESSION['view'] = $this->input->post('view');
		if($_SESSION['view'] != "grid" || $_SESSION['view'] != "list"){
			echo json_encode(array("status" => 0, "view" => $_SESSION['view'])) ;
		} else {
			echo json_encode(array("status" => 1, "view" => "no view found"));
		}
	}

	public function updateViewForWishlist(){
		$_SESSION['wishlist_view'] = $this->input->post('wishlist_view');
		if($_SESSION['wishlist_view'] != "grid" || $_SESSION['wishlist_view'] != "list"){
			echo 0;
		} else {
			echo 1;
		}
	}

	public function delete_wishlist_category(){
		$id = $this->input->post('id');
		$response = $this->Product_Model->delete_wishlist_category($id);
		echo json_encode($response);
	}

	public function product_acc(){
		$product_id = $this->input->post('product_id');
		$userid = $this->input->post('userid');

		$return = $this->Product_Model->getAccDataByProdId($userid, '', '', '', '', '', $product_id);
		echo json_encode($return);
	}
}
?>