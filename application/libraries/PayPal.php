<?php

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Payments\AuthorizationsCaptureRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;

use Sample\PayPalClient;

ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

class PayPal{

    public $clientId;
    public $clientSecret;
    public $CI;

    function __construct(){
        include APPPATH . 'third_party/vendor/autoload.php';
        $CI =& get_instance();
        $this->clientId = PayPalClientId;
        $this->clientSecret = PayPalSecret;
    }

    public static function client($clientId, $clientSecret){
        return new PayPalHttpClient(self::environment($clientId, $clientSecret));
    }

    public static function environment($clientId, $clientSecret){
        return new SandboxEnvironment($clientId, $clientSecret);
    }

    public static function buildRequestBody($type = "", $amount = "", $currency){
        if($amount == ""){
            return "{}";
        }else{
            return array(
                'amount' =>
                  array(
                    'value' => $amount,
                    'currency_code' => $currency
                  )
            );
        }
    }

    public function captureOrder($authorizationId, $amount, $currency, $debug=false){
        $request = new AuthorizationsCaptureRequest($authorizationId);
        $request->body = self::buildRequestBody($amount, $currency);
        $client = self::client($this->clientId, $this->clientSecret);
        $response = $client->execute($request);

        if ($debug){
            print "Status Code: {$response->statusCode}\n";
            print "Status: {$response->result->status}\n";
            print "Capture ID: {$response->result->id}\n";
            print "Links:\n";
            foreach($response->result->links as $link){
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            // To toggle printing the whole response body comment/uncomment below line
            echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
        }
        return $response;
    }

    public function refundOrder($captureId, $amount, $currency, $debug=false){
        $request = new CapturesRefundRequest($captureId);
        $request->body = self::buildRequestBody("refund", $amount, $currency);
        $client = self::client($this->clientId, $this->clientSecret);
        $response = $client->execute($request);

        if ($debug)
        {
            print "Status Code: {$response->statusCode}\n";
            print "Status: {$response->result->status}\n";
            print "Order ID: {$response->result->id}\n";
            print "Links:\n";
            foreach($response->result->links as $link){
                print "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
            }
            // To toggle printing the whole response body comment/uncomment
            // the following line
            echo json_encode($response->result, JSON_PRETTY_PRINT), "\n";
        }
        return $response;
    }
}
?>