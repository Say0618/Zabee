<?php
class Purchaseinvoice extends SecureAccess 
{
	public $posteddata = "";
	public $invoices = "";
	public $products = array();
	public $vendors = array();
	public $users = array();
	function __construct()
	{
		parent::__construct();		
		$this->load->model("admin/Purchase_Invoice_Model");		
		$this->load->model("Product_Model");	
		$this->load->model("admin/Vendors_Model");	
		$this->load->model("admin/Adminuser_Model");
		$this->data = array(
			'page_name' 		=> 'store_info',
			'isScript' 			=> false,
			'notificationCount' => $this->notificationCount->notifications,
			'notifications' 	=> $this->notifications
		);		
		$this->data['textNotification'] = $this->checkUserTextNotificaiton;
		if(!$this->checkUserStore){
			redirect(base_url('seller'));
		}
	}

	public function index()
	{
		$this->Invoice_list();
	}
	
	public function Invoice_list()
	{
		$data['oObj'] 	= $this;		
		$this->products = $this->Product_Model->getProduct();
		$this->vendors 	= $this->Vendors_Model->getVendors();
		$this->users 	= $this->Adminuser_Model->getbackendUsers();
		$this->invoices = $this->Purchase_Invoice_Model->getPurchaseInvoice();
		$this->processInvoice();	
		$headings = array(
					"invoice_id"			=> "Invoice Number",
					"vendor_name"			=> "Vendors",
					"purchaser"				=> "Purchased By",
					"purchase_date"			=> "Purchased On",
					"transportation_cost"	=> "Total Tranportation cost",
					"total_purchase_cost"	=> "Total Purchase Cost",
					"total_mrp"				=> "Total Selling Price",
					"total_margin"			=> "Total Estimated Margin",			
					);
		$action = array
		(
			"btns"		=>array("view","edit","delete"),
			"text"		=>array("View","Edit","Delete"),
			"dbcols"	=>array("invoice_id","invoice_id","invoice_id"),
			"link"		=>array(base_url()."seller/purchaseinvoice/viewInvoice/%@$%",base_url()."seller/purchaseinvoice/getpurchaseinvoice/%@$%",base_url()."seller/purchaseinvoice/deletepurchaseinvoice/%@$%"),
			"clickable" =>array("","")
		);
		$label = "Purchase Invoices";
		$this->data['page_name'] = 'purchase_invoice_lists_view';
		$this->data['Breadcrumb_name'] = 'Purchase Invoice';
		$this->data['isScript'] = false;
		$this->load->view("admin/admin_template",$this->data);			
	}
	
	public function deletepurchaseinvoice($master_id)
	{
		$this->Purchase_Invoice_Model->deleteInvoice($master_id);	
		$this->RefreshListingPage();
	}	
	
	public function viewInvoice($invoice_id)
	{
		$this->products = $this->Product_Model->getProduct();
		$this->vendors  = $this->Vendors_Model->getVendors();
		$this->users    = $this->Adminuser_Model->getbackendUsers();
		$this->invoices = $this->Purchase_Invoice_Model->getPurchaseInvoice($invoice_id);			
		$this->processInvoice(TRUE);				
		$this->data["invoices"] = $this->invoices[0];
		$this->data["products"] = array();
		$data["obj"] = $this;
		foreach($this->products as $prods){
			$this->data["products"][$prods["product_id"]] = $prods;
		}
		$this->data['page_name'] 		= 'invoice_view';
		$this->data['Breadcrumb_name'] 	= 'Invoice';
		$this->data['isScript'] 		= true;
		$this->load->view("admin/admin_template",$this->data);		
	}
	
	public function createpurchaseinvoice()
	{		
		if(!(empty($_POST)) && ($_POST['invoice_func'] == "create")){			
			$this->posteddata = $_POST;
			if($this->validateInvoice($_POST)){				
				$this->arrangePostData();
				$this->Purchase_Invoice_Model->insertInvoice($this->posteddata);		
				$this->session->set_flashdata("success","Purchase Invoice created successfully.");	
			}
			$this->RefreshListingPage();
		} else {
			$this->backtologin();
		}
	}
	
	public function editedpurchaseinvoice()
	{
		if(!(empty($_POST)) && ($_POST['invoice_func'] == "edit")){			
			$this->posteddata = $_POST;
			if($this->validateInvoice($_POST)){				
				$this->arrangePostData(TRUE);
				$this->Purchase_Invoice_Model->updateInvoice($this->posteddata);		
				$this->session->set_flashdata("success","Purchase Invoice created successfully.");	
			}
			$this->RefreshListingPage();
		}else{
			$this->backtologin();
		}
	}
	
	public function getpurchaseinvoice($invoice_id)
	{
		echo json_encode(array("status"=>"success","invoice" => $this->invoices = $this->Purchase_Invoice_Model->getPurchaseInvoice($invoice_id)));
	}
	
	private function arrangePostData($isUpdate = FALSE)
	{			
		if($this->posteddata && isset($this->posteddata['invoice_count'])){
			$arrRetval = array();
			$cnt = 0;
			for($i=1; $i<=$this->posteddata['invoice_count']; $i++){
				$vendors = array();
				foreach($this->posteddata["vendor_id_".$i] as $vendorid){
					if(!(in_array($vendorid,$vendors))){
						$vendors[] = $vendorid;
					}
				}	
				$arrRetval[$cnt]['vendor_ids'] 			= implode(",",$vendors);
				$arrRetval[$cnt]['purchaser'] 			= $this->posteddata["purchaser_".$i];
				$arrRetval[$cnt]['purchase_date'] 		= date("Y-m-d H:i:s",strtotime($this->posteddata["purchase_date_".$i]));
				$arrRetval[$cnt]['transportation_cost'] = $this->posteddata["transportation_cost_".$i];
				$itemcnt = count($this->posteddata["product_id_".$i]);	
				if($isUpdate){
					$arrRetval[$cnt]['invoice_id'] = $this->posteddata["invoice_id_".$i];
				}			
				$purchasecost = 0;$total_mrp = 0;
				for($j = 0; $j < $itemcnt;$j++)	{
					$arrRetval[$cnt]["items"][$j] = array();
					if($isUpdate){	
						if(isset($this->posteddata["invoice_item_id_".$i][$j]))					
						$arrRetval[$cnt]["items"][$j]["purchase_invoice_item_id"] = $this->posteddata["invoice_item_id_".$i][$j];
					}					
					$arrRetval[$cnt]["items"][$j]["quantity"] 				= $this->posteddata["quantity_".$i][$j];
					$arrRetval[$cnt]["items"][$j]["purchase_rate"] 			= $this->posteddata["purchase_rate_".$i][$j];
					$arrRetval[$cnt]["items"][$j]["total_purchase_rate"] 	= $this->posteddata["total_purchase_rate_".$i][$j];
					$itemprate 												= doubleval($this->posteddata["total_purchase_rate_".$i][$j]);
					$purchasecost 											+= $itemprate;
					$arrRetval[$cnt]["items"][$j]["item_purchase_date"]		= date("Y-m-d H:i:s",strtotime($this->posteddata["item_purchase_date_".$i][$j]));
					$arrRetval[$cnt]["items"][$j]["mrp_item"] 				= $this->posteddata["mrp_item_".$i][$j];
					$total_mrp 												+= (doubleval($this->posteddata["mrp_item_".$i][$j]) * intval($this->posteddata["quantity_".$i][$j]));
					$arrRetval[$cnt]["items"][$j]["vendor_id"] 				= $this->posteddata["vendor_id_".$i][$j];
					$arrRetval[$cnt]["items"][$j]["product_id"]				= $this->posteddata["product_id_".$i][$j];
					$itemsrate 												= doubleval(doubleval($arrRetval[$cnt]["items"][$j]["quantity"]) * doubleval($arrRetval[$cnt]["items"][$j]["mrp_item"]));
					$arrRetval[$cnt]["items"][$j]["item_margin"] 			= ($itemsrate-$itemprate);
				}
				$arrRetval[$cnt]["total_purchase_cost"] = $purchasecost;
				$arrRetval[$cnt]["total_mrp"] 			= $total_mrp;
				$arrRetval[$cnt]["total_margin"] 		= $total_mrp - $purchasecost - intval($arrRetval[$cnt]['transportation_cost']);
				$cnt++;
			}
			$this->posteddata = $arrRetval;			
		} else {			
			$this->backtologin();
		}
	}
	
	private function validateInvoice()
	{
		return TRUE;
	}
	
	private function processInvoice($getArr = FALSE)
	{		
		$this->getNamefromIds($this->vendors,"vendor_id","vendor_name","vendor_name","vendor_ids",",<br /><br />",$getArr);				
		$this->getNamefromIds($this->users,"admin_id","admin_name","purchaser","purchaser",",<br /><br />");	
		if($this->invoices)		{
			foreach($this->invoices as $key=>$invoices){
				$this->invoices[$key]["purchase_date"] 			= date($this->dateFormat,strtotime($invoices["purchase_date"]));
				$this->invoices[$key]["transportation_cost"] 	= number_format($invoices["transportation_cost"]);
				$this->invoices[$key]["total_purchase_cost"] 	= number_format($invoices["total_purchase_cost"]);
				$this->invoices[$key]["total_mrp"] 				= number_format($invoices["total_mrp"]);
				$this->invoices[$key]["total_margin"] 			= number_format($invoices["total_margin"]);
				foreach($invoices["invoiceitems"] as $itemkeys=>$item)
				{
					$this->invoices[$key]["invoiceitems"][$itemkeys]["item_purchase_date"] = date($this->dateFormat,strtotime($item["item_purchase_date"]));
				}
			}
		}			 
	}
	
	/**
	* This function will get the values from the array
	* @param $arrAll = array consisting of all data
	* @param $id = the column name in the invoice array
	* @param $name = The column name to be converted to.
	* @param $arrname = The index name of the invoice array to be saved to.
	*/
	private function getNamefromIds($arrAll,$id,$name,$arrname,$dtname,$imploder = ",",$getArr = FALSE)
	{
		if($this->invoices){
			foreach($this->invoices as $key=>$value){
				$categories = explode(",",$value[$dtname]);
				$this->invoices[$key][$arrname] = array();
				foreach($arrAll as $eachcat){					
					if(in_array($eachcat[$id],$categories)){
						if($getArr){
							$this->invoices[$key][$arrname][] = $eachcat;
						}else
							$this->invoices[$key][$arrname][] = $eachcat[$name];
					}
				}
				if($getArr){
					$this->invoices[$key][$arrname] = $this->invoices[$key][$arrname];
				}else
					$this->invoices[$key][$arrname] = implode($imploder,$this->invoices[$key][$arrname]);
			}
		}
	}
	
	private function RefreshListingPage()
	{
		redirect(base_url()."seller/purchaseinvoice/Invoice_list","refresh");
	}
}
?>