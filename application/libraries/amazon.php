<?php
set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());
require_once APPPATH . 'third_party\amazon\vendor\autoload.php';

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\ApaiIO;

class Amazon {
    function __construct($params = array()) {
        //parent::__construct();
    }
	function getItems($itemId, $responseGroup = array('Reviews', 'SalesRank', 'Images', 'VariationImages', 'ItemAttributes')){
		$conf = new GenericConfiguration();
		$client = new \GuzzleHttp\Client();
		$request = new \ApaiIO\Request\GuzzleRequest($client);

		$conf
			->setCountry('com')
			->setAccessKey('AKIAIGNESFCMUSV543QA')
			->setSecretKey('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')
			->setAssociateTag('AKIAJCKEWUQYE47IIBLQ')
			->setRequest($request)
			->setResponseTransformer(new \ApaiIO\ResponseTransformer\XmlToArray());


		$lookup = new Lookup();
		$lookup->setIdType('UPC');
		$lookup->setItemId($itemId);
		$lookup->setResponseGroup($responseGroup);



		$apaiIo = new ApaiIO($conf);
		$response = $apaiIo->runOperation($lookup);
		return $response['Items'];
	}
}
?>