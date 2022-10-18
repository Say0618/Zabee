<?php

/*
 *  ==============================================================================
 *  Author	: StripeLibrary
 *  Email	: info@kaygees.com
 *  For		: PaymentGateway
 *  Web		: http://www..com
 *  ==============================================================================
 */

class Stripe
{
	public function createCustomer($email, $key)
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000', 'customer'=>array(), 'customer_id'=>'');
		$customer = array("email" => $email, 'description'=>'Creating custumer for email:'.$email);
		try{
			$result = \Stripe\Customer::create($customer);
			$retArr['customer_id'] = $result['id'];
			$retArr['status'] = 1;
			$retArr['customer'] = $result;
		}catch (Stripe\Error\InvalidRequest $e) {
			$error3 = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error3['error']['code'];
			$retArr['error_data'] = $error['error'];
			// Since it's a decline, Stripe_CardError will be caught
			$retArr['code'] = '002';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '001';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		return $this->errorMessages($retArr);
	}

	public function saveCard($cc,$card_name,$customer_id,$key, $address = array())
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error', 'code'=> '000', 'card'=> array(), 'card_id'=>'');
		$getToken = $this->getToken($cc,$customer_id,$key,$card_name, $address);
		if($getToken['status'] == 1){
			$token = $getToken['token'];
			try{
				$result = \Stripe\Customer::createSource(
					$customer_id,
					array("source"=>$token));
				$retArr['card_id'] = $result['id'];
				$retArr['status'] = 1;
				$retArr['message'] = 'Card created';
				$retArr['card'] = $result;
			}catch (Stripe\Error\InvalidRequest $e) {
				$error = $e->getMessage()->getJsonBody();
				$retArr['error'] = $error['error']['code'];
				$retArr['error_data'] = $error['error'];
				$retArr['code'] = '002';
			}catch(\Stripe\Error\Card $e) {
				$error = $e->getMessage()->getJsonBody();
				$retArr['error'] = $error['error']['code'];
				$retArr['error_data'] = $error['error'];
				$retArr['code'] = '001';
			} catch (\Stripe\Error\Authentication $e) {
				$error = $e->getMessage()->getJsonBody();
				$retArr['error'] = $error['error']['code'];
				$retArr['error_data'] = $error['error'];
				$retArr['code'] = '004';
			$error3 = $e->getMessage();
			} catch (Stripe\Error\ApiConnection $e) {
				$error = $e->getMessage()->getJsonBody();
				$retArr['error'] = $error['error']['code'];
				$retArr['error_data'] = $error['error'];
				$retArr['code'] = '005';
			$error4 = $e->getMessage();
			} catch (Stripe\Error $e) {
				$error = $e->getMessage()->getJsonBody();
				$retArr['error'] = $error['error']['code'];
				$retArr['error_data'] = $error['error'];
				$retArr['code'] = '006';
			} catch (Exception $e) {
				$error = $e->getJsonBody();
				$retArr['error'] = $error['error']['code'];
				$retArr['error_data'] = $error['error'];
				$retArr['code'] = '007';
			}
		} else {
			$retArr = $getToken;
		}
		return $this->errorMessages($retArr);
	}
	
	public function updateCard($data,$card_id,$customer_id,$key)
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error', 'code'=> '000', 'card'=> array(), 'card_id'=>'');
		try{
			$result = \Stripe\Customer::updateSource(
				$customer_id,
				$card_id,
				$data);
			$retArr['card_id'] = $result['id'];
			$retArr['status'] = 1;
			$retArr['message'] = 'Card updated';
			$retArr['card'] = $result;
		}catch (Stripe\Error\InvalidRequest $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '002';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '001';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		$error3 = $e->getMessage();
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		$error4 = $e->getMessage();
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		return $this->errorMessages($retArr);
	}
	
	public function getToken($cc, $customer_id, $key, $card_name = '', $address = array())
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000', 'token'=> '', 'tokenObject' => array());
		$card_details = array("card" => $cc);
		if($card_name != ''){
			$card_details['card']['metadata'] = array('card_name'=>$card_name);
		}
		if($customer_id != ''){
			$card_details['card']['customer'] = $customer_id;
		}
		$card_details['card'] = array_merge($card_details['card'], $address);
		//echo '<pre>';print_r($card_details);die();
		try {
			$result = \Stripe\Token::create($card_details);
			$retArr['token'] = $result['id'];
			$retArr['card_id'] = $result['card']['id'];
			$retArr['tokenObject'] = $result;
			$retArr['status'] = 1;
			$retArr['message'] = 'token created';
		}catch (Stripe\Error\InvalidRequest $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '002';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '001';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$retArr['error'] = 'parameter_missing';
			$retArr['code'] = '007';
		}
		return $this->errorMessages($retArr);
	}
	public function getCards(){
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000');
		return $retArr;
	}
	public function doTransaction($token, $package, $user_email, $key, $customer = '')
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000');
		//$card_details = array("card" => $cc);
		$charge_details = array(
			"source"=>$token,
			"amount" => $package->package_price,
			"currency"=>'usd',
			"description"=> 'Payment on behalf of '.$user_email.' for '.$package->package_name,
			"capture"=>false
		);
		if($customer != ''){
			$charge_details['customer'] = $customer;
		}
		try {
			$Charge = \Stripe\Charge::create($charge_details);
			if($Charge->status == 'succeeded'){
				$retArr['status'] = 1;
				$retArr['message'] = $Charge->status;
				$retArr['transaction_id'] = $Charge->id;
				$retArr['TIMESTAMP'] = $Charge->created;
				$retArr['payment'] = $Charge->amount;
			}
		}catch (Stripe\Error\InvalidRequest $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '003';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		return $this->errorMessages($retArr);
	}

	
	public function captureCharge($chargeToken, $key)
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000', 'stripe_code' => '');
		try {
			$charge = \Stripe\Charge::retrieve($chargeToken);
			$charge->capture();
			
			if($charge->status == 'succeeded'){
				$retArr['status'] = 1;
				$retArr['message'] = $charge->status;
				$retArr['transaction_id'] = $charge->id;
				$retArr['TIMESTAMP'] = $charge->created;
				$retArr['payment'] = $charge->amount;
			}
		}catch (Stripe\Error\InvalidRequest $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '003';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';	
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		return $this->errorMessages($retArr);
	}

	public function getRefund($charge, $amount, $key)
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000', 'customer'=>array(), 'charge_id'=>'');
		$customer = array("charge" => $charge, "amount" => $amount);
		try{
			$result = \Stripe\Refund::create($customer);
			$retArr['charge_id'] = $result['id'];
			$retArr['status'] = 1;
			$retArr['message'] = 'Refund completed';
			$retArr['customer'] = $result;
		}catch (Stripe\Error\InvalidRequest $e) {
			// Since it's a decline, Stripe_CardError will be caught
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '002';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '001';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		$error3 = $e->getMessage();
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		$error4 = $e->getMessage();
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		
		return $this->errorMessages($retArr);
	}

	public function paymentRetrieve($charge, $key)
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000', 'intent'=>array(), 'charge_id'=>'');
		try{
			$result = \Stripe\PaymentIntent::retrieve($charge);
			$result->cancel();
			$retArr['status'] = 1;
			$retArr['message'] = 'Refund completed';
			$retArr['intent'] = $result;
		}catch (Stripe\Error\InvalidRequest $e) {
			// Since it's a decline, Stripe_CardError will be caught
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '002';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '001';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		$error3 = $e->getMessage();
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		$error4 = $e->getMessage();
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		
		return $this->errorMessages($retArr);
	}
	
	private function errorMessages($data)
	{
		if($data['status'] == 1)
			return $data;
		
		$error = $data['error'];
		$error_message = '';
		$err = array(
			'charge_already_refunded' => 'Refund was is declined, plaease review the card information',
			'card_declined' => 'Your card was declined, please review the card information',
			'expired_card' => 'Your card has expired, please review the card information',
			'incorrect_cvc' => 'Your card was declined, please review the card information',
			'resource_missing' => 'Your card was declined, please review the card information',
			'parameter_invalid_integer' => 'Payment can proceed, please contact admin',
			'parameter_missing' => 'Invalid payment request, please contact admin',
		);
		//echo '<pre>';print_r($error);echo '</pre>';
		$code = (isset($error['error']['code']))?$error['error']['code']:'';
		$error_message = (isset($err[$code]))?$err[$code]:'Error in transaction, please try again.';
		$data['message'] = $error_message;
		$data['error_data'] = (isset($data['error_data']))?$data['error_data']:"";
		//echo '<pre>';print_r($data);echo '</pre>';die();
		return $data;
	}
	public function decline($transaction_id,$key)
	{
		include APPPATH . 'third_party/autoload.php';
		\Stripe\Stripe::setApiKey($key);
		$retArr = array('status'=>0, 'message' => 'Error in Submitting', 'code'=> '000');
		//$card_details = array("card" => $cc);
		$charge_details = array(
			"source"=>$token,
		);
		try {
			$Charge = \Stripe\Issuing\Authorizations::decline($transaction_id);
			if($Charge->status == 'closed'){
				$retArr['status'] = 1;
				$retArr['message'] = $Charge->status;
				$retArr['transaction_id'] = $Charge->id;
				$retArr['TIMESTAMP'] = $Charge->created;
				$retArr['payment'] = $Charge->amount;
			}
		}catch (Stripe\Error\InvalidRequest $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		}catch(\Stripe\Error\Card $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '003';
		} catch (\Stripe\Error\Authentication $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '004';
		} catch (Stripe\Error\ApiConnection $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '005';
		} catch (Stripe\Error $e) {
			$error = $e->getMessage()->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '006';
		} catch (Exception $e) {
			$error = $e->getJsonBody();
			$retArr['error'] = $error['error']['code'];
			$retArr['error_data'] = $error['error'];
			$retArr['code'] = '007';
		}
		return $this->errorMessages($retArr);
	}
}