<?php

/*
 *  ==============================================================================
 *  Author	: GoogleShoppingIntegeration
 *  Email	: info@kaygees.com
 *  For		: Shopping
 *  Web		: https://zab.ee
 *  ==============================================================================
 */

class Shopping{
	private $client;
	private $service;
	//
	const MERCHANT_ID = '127172679';
	// The products will be sold online
	const CHANNEL = 'online';
	// The product details are provided in English
	const CONTENT_LANGUAGE = 'en';
	// The products are sold in the United States
	const TARGET_COUNTRY = 'US';
    //
	const CURRENCY = "USD";
	function __construct(){
		include APPPATH.'third_party/vendor/autoload.php';
		$this->client = new Google_Client();
		$this->client->useApplicationDefaultCredentials();
		$this->client->addScope(Google_Service_ShoppingContent::CONTENT);
		//$jsonpath = APPPATH.'third_party/vendor/live-merchant-api-key.json';
		$jsonpath = APPPATH.'third_party/vendor/merchant-center-1541160781237-0c5faac89b3c.json';
		putenv($jsonpath);
		$this->client->setApplicationName('Zabee Google Integration');
		$this->client->setAuthConfig($jsonpath);
		$this->client->setAccessType("offline");        // offline access
		$this->client->setIncludeGrantedScopes(true);   // incremental auth
		$this->service = new Google_Service_ShoppingContent($this->client);
		//$httpClient = $this->client->authorize();
		//$httpClient->get('https://www.googleapis.com/plus/v1/people/me');
	}
	public function insertProduct($data=""){
			//echo "<pre>";print_r($data);die();
			foreach($data as $d){
				$product = new Google_Service_ShoppingContent_Product();
				//print_r($d);die();
				$product->setOfferId($d['offer_id']);
				$product->setTitle($d['title']);
				$product->setDescription($d['description']);
				$product->setLink($d['link']);
				$product->setImageLink($d['image_link']);
				$product->setContentLanguage(self::CONTENT_LANGUAGE);
				$product->setTargetCountry(self::TARGET_COUNTRY);
				$product->setChannel(self::CHANNEL);
				$product->setAvailability($d['availablity']);
				$product->setCondition($d['condition']);
				//$product->setGoogleProductCategory('Computer > Cooler Master');
				$product->setGtin($d['gtin']);
				
				//echo "<pre>";print_r($product);die("here");
				$price = new Google_Service_ShoppingContent_Price();
				$price->setValue($d['price']);
				$price->setCurrency(self::CURRENCY);
				
				$shipping_price = new Google_Service_ShoppingContent_Price();
				$shipping_price->setValue('0.1');
				$shipping_price->setCurrency('USD');
				
				$shipping = new Google_Service_ShoppingContent_ProductShipping();
				$shipping->setPrice($shipping_price);
				$shipping->setCountry('US');
				$shipping->setService('Free Shipping');
				
				$shipping_weight = new Google_Service_ShoppingContent_ProductShippingWeight();
				$shipping_weight->setValue(176);
				$shipping_weight->setUnit('grams');
				
				$product->setPrice($price);
				$product->setShipping(array($shipping));
				$product->setShippingWeight($shipping_weight);
				
				$result = $this->service->products->insert(self::MERCHANT_ID, $product);
			}
			echo "<pre>";print_r($result);
	}
	public function getProductList(){
		//$this->client->setDeveloperKey("jtyLoodKIpqhFuCe-seK-0F8");
		//echo "<pre>";var_dump($service->products);
		$result = $this->service->products->listProducts(self::MERCHANT_ID);
		echo "<pre>";print_r($result);
	}
	public function deletetProduct(){
		$productId = $this->buildProductId($offerId);
		$result = $this->service->products->delete(self::MERCHANT_ID,$productId);
		echo "<pre>";print_r($result);
	}
	private function buildProductId($offerId) {
		return sprintf('%s:%s:%s:%s', self::CHANNEL, self::CONTENT_LANGUAGE,
		self::TARGET_COUNTRY, $offerId);
	}
	public function getProduct($offerId) {
		$productId = $this->buildProductId($offerId);
		$product = $this->service->products->get(self::MERCHANT_ID, $productId);
		printf("Retrieved product -  %s: %s \n", $product->getId(),$product->getTitle());
	}
}