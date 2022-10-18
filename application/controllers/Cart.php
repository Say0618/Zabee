<?php
class Cart extends Securearea 
{	
	function __construct()
	{
		parent::__construct();
		$this->lang->load('english', 'english');
	}

	public function index()
	{
 		$this->data['page_name'] 	= 'cart';
		$this->data['hasScript'] 	= true;
		$this->data['newsletter'] 	= true;
		$this->data['title'] 		= "Cart";
		$this->data['cartData'] 	= array();
		$recentSaved 				= array();
		if($this->isloggedin){ 
			if(isset($this->userData["userid"])){
				$user_id = $this->userData["userid"];
			}else{
				$user_id = $this->userData["admin_id"];
			}
		}
		// $this->cart_discount($this->cart->contents());
		$cart = array();
		//echo "<pre>" ; echo json_encode($this->session->userdata('cart_contents')); die();
		if($this->cart->total_items() > 0){
			foreach($this->cart->contents() as $items){
				if(isset($cart[$items['seller_id']])){
					$cart[$items['seller_id']][] = $items;
					$cart[$items['seller_id']]['subtotal'] = $cart[$items['seller_id']]['subtotal']+$items['subtotal'];
				}else{
					$cart[$items['seller_id']] = array();
					$cart[$items['seller_id']][] = $items;
					$cart[$items['seller_id']]['subtotal'] = $items['subtotal'];
					$cart[$items['seller_id']]['store_name'] = $items['store_name'];
				}
			}
		}
		//print_r($cart);
		//die();
		$this->data['cart'] =$cart;

		$this->load->view('front/template', $this->data); 
	}
	public function beta()
	{
 		$this->data['page_name'] 	= 'cart_beta';
		$this->data['hasScript'] 	= true;
		$this->data['newsletter'] 	= true;
		$this->data['title'] 		= "Cart";
		$this->data['cartData'] 	= array();
		$recentSaved 				= array();
		if($this->isloggedin){ 
			if(isset($this->userData["userid"])){
				$user_id = $this->userData["userid"];
			}else{
				$user_id = $this->userData["admin_id"];
			}
		}
		//$this->cart_discount($this->cart->contents());
		$cart = array();
		//echo "<pre>";
		if($this->cart->total_items() > 0){
			foreach($this->cart->contents() as $items){
				if(isset($cart[$items['seller_id']])){
					$cart[$items['seller_id']][] = $items;
					$cart[$items['seller_id']]['subtotal'] = $cart[$items['seller_id']]['subtotal']+$items['subtotal'];
				}else{
					$cart[$items['seller_id']] = array();
					$cart[$items['seller_id']][] = $items;
					$cart[$items['seller_id']]['subtotal'] = $items['subtotal'];
					$cart[$items['seller_id']]['store_name'] = $items['store_name'];
				}
			}
		}
		//print_r($cart);
		//die();
		$this->data['cart'] = $cart;
		$this->load->view('front/template', $this->data); 
	}
	public function addtocart($id = "", $referral = ""){
		$response = array('status'=>0, 'message'=>'Invalid Product');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('pvid', 'Product ID', 'trim');
		$post_data = $this->input->post();
		$is_ajax = (isset($post_data['is_ajax']))?true:false;
		$shippingData = array();
		//echo "<pre>";print_r($_POST);die();
		$flag = false;
		if($this->form_validation->run() == true && count($_POST) > 0){
			$flag = true;
		}else if($id != ""){
			$flag = true;
		}else{
			$flag = false;
		}
		
		if($flag)
		{
			$product_id = (isset($post_data['pvid']))?$post_data['pvid']:$id;
			$quantity = (isset($post_data['qty']))?$post_data['qty']:'1';
			$product = $this->Product_Model->getProductByProductVariantID($product_id);
			//echo "<pre>";print_r($product);die();
			if($product['status'] == 1){
				$product = $product['data'];
				$variants = explode(',', $product->variant_group);
				if($is_ajax){
					if(!empty($post_data['shipping'])){
						$shipping_id = $post_data['shipping']['shipping_id'];
						$shipping_title = $post_data['shipping']['title'];
						$shipping_price =$post_data['shipping']['price'];
						$shippingData = $this->Utilz_Model->getShippingDataByid($shipping_id);
					}else{
						$shippingData = $this->Utilz_Model->getLowestShippingDataByPvid($product_id);
						$shipping_id = $shippingData->shipping_id;
						$shipping_title = $shippingData->title;
						$shipping_price = $shippingData->price;
					}
				}
				//echo "<pre>";print_r($shippingData);die();
				if($product->condition_id == "1"){
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product->product_id,'condition_id'=>$product->condition_id),"","","is_cover DESC");
					if(!empty($productImage)){
					$product_img = $productImage[0]->is_primary_image;
					}
					else{
						$product_img =	$product->product_image;
					}
				}else{
					$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image",array('product_id'=>$product->product_id,'sp_id'=>$product->seller_product_id),"","","is_cover DESC");
					if(!empty($productImage)){
							$product_img = $productImage[0]->is_primary_image;
						}
					if(empty($productImage)){
						$productImage = $this->Product_Model->getData(DBPREFIX.'_product_media',"iv_link as image_link,thumbnail as is_primary_image,is_local,is_image,is_cover",array('product_id'=>$product->product_id,'condition_id'=>1),"","","is_cover DESC");
						$product_img = $productImage[0]->is_primary_image;
					}
				}
				
				$cart_id = ""; 
				$today = date("Y-m-d");
				$today_time = strtotime($today);
				$original = $product->price; 
				if($today_time >= strtotime($product->valid_from) && $today_time <= strtotime($product->valid_to)){
					$product->price = discount_forumula($product->price, $product->discount_value, $product->discount_type, $product->valid_from, $product->valid_to);
				}
				$items = array
				(
				   'id'					 => $product->pv_id,
				   'prd_id'				 => $product->product_id,
				   'condition_id'		 => $product->condition_id,
					'sp_id'				 => $product->sp_id,	
				   'qty'				 => $quantity,
				   'original'			 => $original,
				   'price'				 => ($product->price)?$product->price:$original,
				   'warehouse_id'  		 => $product->warehouse_id,
				   'name'				 => urlClean($product->product_name),
				   'condition'			 => $product->condition,
					'img'           	 => $product_img,
					'already_saved' 	 => $product->already_saved,
					'options' 			 => (!empty($variants))?$this->Product_Model->cartVariantFormat($this->Product_Model->getVariants($variants)):"",
				   'variant'			 => (!empty($variants))?$this->Product_Model->getVariants($variants):"",
				   'variant_ids'		 => (!empty($variants))?$variants:"",
				   'upc_code'			 => $product->upc_code,
				   'seller_sku'			 => $product->seller_sku,
				   'seller_id'			 => $product->seller_id,
				   'is_local'			 => $product->is_local,
				   'max_qty'		 	 => $product->quantity,
				   'sell_quantity'  	 => $product->sell_quantity,  
				   'store_name'			 => $product->store_name,
				   //'prod_qty'			 => $product->quantity,
				   'shipping_id'		 => $shipping_id,
				   'shipping_title'		 => $shipping_title,
				   'shipping_price'		 => $shipping_price,
				   //'available_shippings' => $product->shipping_ids,
				   'shippingData' 		 => $shippingData,
				   'discount_id'		 => $product->discount_id,
				   'valid_from'			 => $product->valid_from,
				   'valid_to'			 => $product->valid_to,
				   'discount_value'		 => $product->discount_value,
				   'discount_type'		 => $product->discount_type,
				   'update_msg'			 => "",
				   'referral'			 => isset($post_data['ref'])?$post_data['ref']:$referral,
				   'slug'				 => $product->slug,
				   'category_id'		 => $product->category_id,
				   'hubx_id'		 	 => $product->hubx_id,
				   'sp_description'		 => (isset($product->seller_product_description))?$product->seller_product_description:"",
				   'collect_tax'		 => $product->is_tax,
				   'is_tax'				 => 0,
				   'tax'				 => 0
				);
				// echo"<pre>"; print_r($items); die();
				if(isset($items['options']) && count($items['options']) > 0){
					$rowid = md5($items['id'].serialize($items['options']));
				}else{
					$rowid = md5($items['id']);
				}
				$recentCart = $this->session->userdata('cart_contents');
				// echo"<pre>"; print_r($items); echo"<br>"; print_r($recentCart); print_r($rowid); die();
				if(isset($recentCart[$rowid])){
					$qty = (int)$recentCart[$rowid]['qty']+(int)$items['qty'];
					$ref = ($recentCart[$rowid]['referral'] != "")?$recentCart[$rowid]['referral']:$items['referral'];
					if($qty <= $items['max_qty']){
						$subtotal = (int)$qty*(int)$items['original'];
					}else{
						$qty = $items['max_qty'];
						$subtotal = (int)$items['max_qty']*(int)$items['original'];
					}
					$items['subtotal'] = $subtotal;
					$data = array(
						'rowid' => $rowid,
						'qty'   => $qty,
						'max_qty' =>$items['max_qty'],
						'subtotal'=>$subtotal,
						'referral'=> $ref
					);
					$cart_id = $this->cart->update($data);	
				}else{
					$qty = "";
					$cart_id = $this->cart->insert($items);	
				}
				if($this->isloggedin){
					if(isset($this->userData["userid"])){
						$user_id = $this->userData["userid"];
					}else{
						$user_id = $this->userData["admin_id"];
					}
					$oldCart = $this->Cart_Model->getCartContents($user_id);
					if($oldCart !=""){
						$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
					}else{
						$this->Cart_Model->addtoCart($this->session->userdata('cart_contents'),$user_id);
					}
				}
				$qty = ($qty)?$qty:$quantity;
				$this->cartAjaxupdate($rowid,$qty,$shipping_id,1);
				if(isset($_POST['fromBuyNow']) && $_POST['fromBuyNow'] == "1"){
					$response = array('status'=>1, 'message'=>'Success', 'cart_id'=> $cart_id, 'buynow'=>1);
				} else {
					$response = array('status'=>1, 'message'=>'Success', 'cart_id'=> $cart_id, 'buynow'=>0);
				}
				$response = array('status'=>1, 'message'=>'Success', 'cart_id'=> $cart_id);
			}
		}
		//$this->cart_discount($this->cart->contents());
		if($is_ajax){
			if(isset($_POST['is_buynow'])){
				return json_encode($response);
			}else{
				echo json_encode($response);
			}
		} else {	
			redirect(base_url('cart'),"refresh");
		}	
	}

	function saveForLater($id="",$action=""){
		$login = $this->isloggedin;
		$saveItem = "";
		if($id == "" && $action ==""){
			$id = $_POST['row_id'];
			$action = $_POST['action'];
		}
		$response = array('status'=>0, 'message'=>'Invalid Product');
		if($id){
			if($action == "saveforlater"){
				$cartItem = $this->cart->get_item($id);
				if($cartItem){
					$saveItem = $this->saveforlater->insert($cartItem);
					$this->cart->remove($id);
				}
			}else{
				$savedItem = $this->saveforlater->get_item($id);
				// echo $savedItem."<br>"; 
				if($savedItem){
					if(empty($login)){
						$saveItem = $this->cart->insert($savedItem);
						$this->saveforlater->remove($id);
					}else{
						if($savedItem["seller_id"] == $this->session->userdata("userid")){
							$this->session->set_flashdata("same_product",array($savedItem["name"]));
							$this->saveforlater->remove($id);
						}else{
							$saveItem = $this->cart->insert($savedItem);
							$this->saveforlater->remove($id);
						}
					}
				}
			}
			if($login){
				if(isset($this->userData["userid"])){
					$user_id = $this->userData["userid"];
				}else{
					$user_id = $this->userData["admin_id"];
				}
				if($user_id){
					if($action == "movetocart"){
						$oldCart = $this->Cart_Model->getCartContents($user_id);
						if(!empty($oldCart) && $oldCart !="is_exists"){
							if(isset($oldCart[$id])){
								$qty = (int)$oldCart[$id]['qty']+(int)$savedItem['qty'];
								if($qty <= $savedItem['max_qty']){
									$subtotal = (int)$qty*(int)$savedItem['price'];
								}else{
									$qty = $savedItem['max_qty'];
									$subtotal = (int)$savedItem['max_qty']*(int)$savedItem['price'];
								}
								$data = array(
										'rowid'   => $id,
										'qty'     => $qty,
										'subtotal'=>$subtotal
								);
								$this->cart->update($data);
								$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
							}else{
								$this->cart->update($savedItem);
								$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
							}
						}else{
							if($oldCart == "is_exists"){
								$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
							}else{
								$this->Cart_Model->addtoCart($this->session->userdata('cart_contents'),$user_id);
							}
						}
						$this->Cart_Model->updateSaveForLater($user_id,$this->session->userdata('save_contents'));
					}
					if($action == "saveforlater"){
						$oldSaveForLater = $this->Cart_Model->getSavedForLaterContents($user_id);
						if(!empty($oldSaveForLater) && $oldSaveForLater !="is_exists"){
							if(isset($oldSaveForLater[$id])){
								$qty = (int)$oldSaveForLater[$id]['qty']+(int)$cartItem['qty'];
								if($qty <= $cartItem['max_qty']){
									$subtotal = (int)$qty*(int)$cartItem['price'];
								}else{
									$qty = $cartItem['max_qty'];
									$subtotal = (int)$cartItem['max_qty']*(int)$cartItem['price'];
								}
								$data = array(
										'rowid'   => $id,
										'qty'     => $qty,
										'subtotal'=>$subtotal
								);
								$this->saveforlater->update($data);
								$this->Cart_Model->updateSaveForLater($user_id,$this->session->userdata('save_contents'));
							}else{
								$this->saveforlater->update($cartItem);
								$this->Cart_Model->updateSaveForLater($user_id,$this->session->userdata('save_contents'));
							}
						}else{
							if($oldSaveForLater == "is_exists"){
								$this->Cart_Model->updateSaveForLater($user_id,$this->session->userdata('save_contents'));
							}else{
								$this->Cart_Model->addtoSaveForLater($this->session->userdata('save_contents'),$user_id);
							}
						}
						$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
					}
				}
			}
			$response = array('status'=>1, 'message'=>'Success', 'saved_id'=> $saveItem);
		}
	 	echo json_encode($response);	
	}

	public function getcurrentcart($getValues = FALSE)
	{	
		if(intval($this->carttotalitems) == 0){if($getValues == TRUE){return "";}echo "";die;} //Kill it when there is no item in the cart
		$cartprodids = $this->getCartProductIds();
		$Products = $this->getProductsData($cartprodids);
		$cartProducts = array();
		$totalprice = 0;	
		$discount = 0;	
		foreach($this->Cart_Model->getCartContents($this->cart_id) as $key=>$cartitems)	{
			$val = array();			
			foreach($Products as $product){
				if($cartitems["id"] == $product["product_id"]){
					$val['product_id'] 		= $product["product_id"];
					$val['product_name'] 	= $product["product_name"];	
					$val['product_image'] 	= $product["product_image"];
					$val['product_price'] 	= number_format($product["product_price"]);
					$val['quantity'] 		= $cartitems["qty"];
					$val['row_id'] 			= $key;					
					$val['totalprice'] 		= number_format($product["product_price"] * $cartitems["qty"]);
					$totalprice 			+= doubleval($product['product_price'] * $cartitems["qty"]);					
				}				
			}
			$cartProducts[] = $val;
		}
		$retArr = array
		(
			"status"=>"success",
			"productData"	=>$cartProducts,
			"grossprice"	=>"$".number_format(doubleval($totalprice)-doubleval($discount)),
			"totalprice"	=>"$".number_format($totalprice),
			"totaldiscount"	=>"$".number_format($discount),
		);
		if($getValues == TRUE)		{
			return $retArr;
		}else{
			echo json_encode($retArr);
		}
	}

	public function delete($row_id,$action="cart"){
		if($action == "cart"){
			$this->cart->remove($row_id);
			if($this->isloggedin){ 
				if(isset($this->userData["userid"])){
					$user_id = $this->userData["userid"];
				}else{
					$user_id = $this->userData["admin_id"];
				}
				$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
			}
			$total_items = $this->cart->total_items();
		}else{
			$this->saveforlater->remove($row_id);
			if($this->isloggedin){ 
				if(isset($this->userData["userid"])){
					$user_id = $this->userData["userid"];
				}else{
					$user_id = $this->userData["admin_id"];
				}
				$this->Cart_Model->updateSaveForLater($user_id,$this->session->userdata('save_contents'));
			}
			$total_items = $this->saveforlater->total_items();
		}
		$response = array('status'=>1, 'message'=>'Success','total_items'=>$total_items);
		echo json_encode($response);
	}

	public function update()
	{
		$data = $this->input->post();
		$this->cart->update($data);
		if($this->isloggedin){ 
			if(isset($this->userData["userid"])){
				$user_id = $this->userData["userid"];
			}else{
				$user_id = $this->userData["admin_id"];
			}
			$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
		}
			redirect(base_url('cart'), 'refresh');
	}

	public function cartAjaxupdate($row_id="",$qty="",$shipping_id="",$is_ajax=""){
		//echo "<pre>";print_r($this->cart->contents());die();
		$data = array();
		$sub = 0;
		// echo "given => ".$this->input->post('qty')."<br>";
		if($this->input->post('rowid')){
			$data["rowid"] = $this->input->post('rowid');
		}else{
			$data["rowid"] = $row_id;
		}

		$item = $this->cart->get_item($data["rowid"]);
		//echo "<pre>";print_r($item);die();
		$max_qty = $this->Product_Model->getData(DBPREFIX."_product_inventory", "(quantity - sell_quantity) AS quantity", array("product_variant_id"=>($item['id'])));
		if($qty == ""){
			$data["qty"] = ($max_qty[0]->quantity >= $this->input->post('qty')) ? $this->input->post('qty') : $max_qty[0]->quantity;
		}else{
			$data["qty"] = $qty;
		}
		// echo "max => ".$max_qty[0]->quantity."<br>";
		// echo "final => ".$data["qty"]; die();
		if($this->input->post('shipping_id')){
			$data["shipping_id"] = $this->input->post('shipping_id');
		}else{
			$data["shipping_id"] = $shipping_id;
		}
		// echo"<pre>"; print_r($max_qty[0]->quantity); die();
		$row_ids = array();
		$subtotal = 0;
		$grand_total = 0;
		$shipping_item_price = 0;
		$shipping_total = 0;
		$free_after_seller = array();
		$subtotal_seller = array();
		$rowdata = array(
			'rowid'  => $data["rowid"],
			'qty'  => $data["qty"]
		);
		if($data["shipping_id"]){
			$rowdata['shipping_id'] = $data['shipping_id'];
			$shippingData = $this->Utilz_Model->getShippingDataByid($rowdata['shipping_id']);
			$rowdata['shippingData'] = $shippingData;
		//	echo "<pre>";
			
			//print_r($fas);die();
			/*$shipping_item = $this->Cart_Model->calculateItemShipping($data["shipping_id"], $data["qty"], $item);
			if($shipping_item['status'] == 1){
				$shipping_item_price = $shipping_item['shipping']['shipping_amount'];
				$rowdata = array(
					'rowid'  => $data["rowid"],
					'qty'  => $data["qty"],
					'shipping_id'  => $shipping_item['shipping']['shipping_id'],
					'shipping_title' => $shipping_item['shipping']['title'],
					'shipping_price' => $shipping_item_price,
				);
				// echo"row: "; print_r($rowdata);
				$data["shipping_title"] = $shipping_item['shipping']['title'];
				$data["shipping_price"] = $shipping_item_price;
				$row_ids[$data["rowid"]]['shipping_price'] = $shipping_item_price;
				$this->cart->update($rowdata);
			}*/
		}
		$this->cart->update($rowdata);
		$cart = $this->cart->contents();
		//echo "<pre>";
		if($cart){
			foreach($cart as $items){
				$shippingCal = $this->Cart_Model->shippingCal($items,$free_after_seller,$subtotal_seller);
				$free_after_seller = $shippingCal["free_after_seller"];
				$subtotal_seller = $shippingCal["subtotal_seller"];
			}
			//print_r($subtotal_seller);
			//print_r($free_after_seller);
			$fas = $this->Cart_Model->cartShipping($free_after_seller);
		}
		$cart = $this->cart->contents();//$this->session->userdata('cart_contents');
		//echo "<pre>";print_r($cart);die();
		$subtotal = $this->cart->total();
		$shipping_total = $this->Cart_Model->calculateShipping($cart);
		$tax = 0;
		if($this->session->userdata('checkout_ship')){
			$location = $this->session->userdata('checkout_ship');
			// i changed cart subtotal to item subtotal
			if($item['is_tax']){
				$tax = $this->getTax($location['zipcode'], $item['subtotal']);
			}else{
				$tax = 0;
			}
		}
		$grand_total = $shipping_total + $subtotal + $tax;
		$shipping_total = $this->cart->format_number($shipping_total);
		
		$grand_total = $this->cart->format_number($grand_total);//$this->cart->format_number($grand_total+$shipping_total);
		//$this->cart->update($data);
		//--------Update Cart---------//
		if($this->isloggedin){ 
			if(isset($this->userData["userid"])){
				$user_id = $this->userData["userid"];
			}else{
				$user_id = $this->userData["admin_id"];
			}
			$this->Cart_Model->updateCart($user_id,$this->session->userdata('cart_contents'));
			// echo '<pre>';print_r($cart);echo '</pre>';die();
			$row_ids[$data["rowid"]]['subtotal'] = $cart[$data["rowid"]]['subtotal'];
			if($this->session->userdata('checkout_ship')){
				$row_ids[$data["rowid"]]['tax'] = $tax;//$this->getTax($location['zipcode'], $cart[$data["rowid"]]['subtotal']);
			
				foreach($this->cart->contents() as $cart){
					if($cart['seller_id'] == $item['seller_id']){
						$sub += ($cart['subtotal'] + $tax); //($this->getTax($location['zipcode'], $cart['subtotal'])));
					}
				}
			}
			$row_ids[$data["rowid"]]['overall_sub'] = $this->cart->format_number($sub);
		}
		//-------Return Data--------//
		$return = array(
			"tax"=>$this->cart->format_number($tax),
			"subtotal" => $this->cart->format_number($subtotal),
			"grand_total"=>$grand_total,
			"shipping_total"=>$shipping_total,
			"shipping_price"=>$shipping_item_price,
			"row_ids"=>$row_ids,
			"max_qty"=>$max_qty[0]->quantity,
			"cart_row"=>$data["rowid"],
		);
		if($is_ajax){
			//echo "<pre>";print_r($this->session->userdata('cart_contents'));die();
			return true;
		}else{
			echo json_encode($return);
		}
	}

	public function updateQuantity($row_id,$qty = "1",$getResp = FALSE)
	{
		$data = array(
               'rowid' => $row_id,
               'qty'   => $qty,
            );
		$this->cart->update($data); 
		if($this->cart_id){
			$this->updatecarttodb($this->cart->contents()); 
		}
		if($getResp){
			$this->getcurrentcart();
		}
	}
	
	public function removeCart($redirect_url = "")
	{
		$this->cart->destroy();
		if($this->cart_id){
			$this->updatecarttodb($this->cart_id,"");
		}
		if($redirect_url)
			redirect(base_url(),"refresh");
	}
	
	private function getProductsData($prod_ids)
	{
		$where = " AND product_id IN ('".implode("','",$prod_ids)."')";
		$products = $this->Product_Model->frontProductDetails($prod_ids);
		return $products;
	}
	
	private function getCartProductIds()
	{
		$id_array = array();
		foreach($this->cart->contents() as $cartitems){
			$id_array[] = $cartitems["id"];
		}
		return $id_array;
	}
	
	private function addcarttodb($cartdata)
	{
		$cart_id = $this->Cart_Model->addtoCart($cartdata);
		$cookie = array(
						'name'   => 'cart_id',
						'value'  => $cart_id,
						'expire' => (365*60*60*24),
						'domain' => '',
						'path'   => '/',
						'prefix' => '',
						'secure' => FALSE
						);
		$this->input->set_cookie($cookie);
	}

	public function add_to_cart()
	{
		$this->data['page_name'] = 'add_cart';
		$this->data['hasScript'] = false;
		$this->data['title'] 	 = "Add to Cart";
		$this->load->view('front/template', $this->data);
	}

	private function updatecarttodb($cartdata)
	{
		$this->Cart_Model->updateCart($this->cart_id,$cartdata);		
	}

	public function buynow($id, $ship_id = "")
	{
		$_POST['is_ajax'] 	= true;
		$_POST['is_buynow'] = true;
		$ref = parse_url($_SERVER['HTTP_REFERER']);
		if(isset($ref['query'])){
			$ref = $ref['query'];
			$ref = (strstr($ref,"ref="))?explode("=", $ref):"";
			$ref = (isset($ref[1]) && $ref[1] != "")?$ref[1]:"";
		}else{
			$ref = "";
		}

		$_POST['qty'] = (!empty($this->input->get('qty')))?$this->input->get('qty'):'1';
		// $_SESSION["buy_qty"] = $_SERVER['REQUEST_URI'];
		// echo $qty; die();
		if($ship_id != "" && !empty($ship_id)){
			$shippingData 		=  $this->Product_Model->getData(DBPREFIX."_product_shipping","*",$ship_id,"shipping_id",'','price',"","",1);
			$_POST['shipping'] 	= (array) $shippingData[0];
		}
		$dt 				= $this->addtocart($id, $ref);
		$dt1 				= json_decode($dt);
		if($dt1->status == 1){
			redirect('checkout/signin');
		}
	}
	public function getShippingData(){
		$pv_id = $this->input->post("pv_id");
		$single = $this->input->post("single");
		$shippingData = $this->Utilz_Model->getLowestShippingDataByPvid($pv_id,$single);
		echo json_encode($shippingData);
	}
}
?>