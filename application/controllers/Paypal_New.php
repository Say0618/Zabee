<?php
class MusicPillars extends Securearea 
{ 
	function __construct(){		
		parent::__construct();
        $this->load->library('paypal_new');
	}

	function buy($id) {
        // Set variables for paypal form
        $returnURL = base_url().'paypal_new/success'; //payment success url
        $cancelURL = base_url().'paypal_new/cancel'; //payment cancel url
        $notifyURL = base_url().'paypal_new/ipn'; //ipn url

        // Get product data from the database
        $product = $this->product->getRows($id);

        // Get current user ID from the session (optional)
        $userID = !empty($_SESSION['userID'])?$_SESSION['userID']:1;

        // Add fields to paypal form
        $this->paypal_lib->add_field('return', $returnURL);
        $this->paypal_lib->add_field('cancel_return', $cancelURL);
        $this->paypal_lib->add_field('notify_url', $notifyURL);
        $this->paypal_lib->add_field('item_name', $product['name']);
        $this->paypal_lib->add_field('custom', $userID);
        $this->paypal_lib->add_field('item_number',  $product['id']);
        $this->paypal_lib->add_field('amount',  $product['price']);

        // Render paypal form
        $this->paypal_lib->paypal_auto_form();
    }

    // function success(){
    //     // Get the transaction data
    //     $paypalInfo = $this->input->get();

    //     $productData = $paymentData = array();
    //     if(!empty($paypalInfo['item_number']) && !empty($paypalInfo['tx']) && !empty($paypalInfo['amt']) && !empty($paypalInfo['cc']) && !empty($paypalInfo['st'])){
    //         $item_name = $paypalInfo['item_name'];
    //         $item_number = $paypalInfo['item_number'];
    //         $txn_id = $paypalInfo["tx"];
    //         $payment_amt = $paypalInfo["amt"];
    //         $currency_code = $paypalInfo["cc"];
    //         $status = $paypalInfo["st"];

    //         // Get product info from the database
    //         $productData = $this->product->getRows($item_number);

    //         // Check if transaction data exists with the same TXN ID
    //         $paymentData = $this->payment->getPayment(array('txn_id' => $txn_id));
    //     }

    //     // Pass the transaction data to view
    //     $data['product'] = $productData;
    //     $data['payment'] = $paymentData;
    //     $this->load->view('paypal/success', $data);
    // }

    //  function cancel(){
    //     // Load payment failed view
    //     $this->load->view('paypal/cancel');
    //  }

    //  function ipn(){
    //     // Retrieve transaction data from PayPal IPN POST
    //     $paypalInfo = $this->input->post();

    //     if(!empty($paypalInfo)){
    //         // Validate and get the ipn response
    //         $ipnCheck = $this->paypal_lib->validate_ipn($paypalInfo);

    //         // Check whether the transaction is valid
    //         if($ipnCheck){
    //             // Check whether the transaction data is exists
    //             $prevPayment = $this->payment->getPayment(array('txn_id' => $paypalInfo["txn_id"]));
    //             if(!$prevPayment){
    //                 // Insert the transaction data in the database
    //                 $data['user_id']    = $paypalInfo["custom"];
    //                 $data['product_id']    = $paypalInfo["item_number"];
    //                 $data['txn_id']    = $paypalInfo["txn_id"];
    //                 $data['payment_gross']    = $paypalInfo["mc_gross"];
    //                 $data['currency_code']    = $paypalInfo["mc_currency"];
    //                 $data['payer_name']    = trim($paypalInfo["first_name"].' '.$paypalInfo["last_name"], ' ');
    //                 $data['payer_email']    = $paypalInfo["payer_email"];
    //                 $data['status'] = $paypalInfo["payment_status"];

    //                 $this->payment->insertTransaction($data);
    //             }
    //         }
    //     }
    // }

}
?>