<?php
require_once "vendor/autoload.php";

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\ApaiIO;

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

/*$search = new Search();
$search->setCategory('DVD');
$search->setActor('Bruce Willis');
$search->setKeywords('Die Hard');*/

$lookup = new Lookup();
$lookup->setItemId('B0040PBK32');



$apaiIo = new ApaiIO($conf);
$response = $apaiIo->runOperation($lookup);

echo '<pre>';print_r($response);
?>