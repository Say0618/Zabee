<?php
set_include_path(APPPATH . 'third_party/' . PATH_SEPARATOR . get_include_path());
require_once APPPATH . 'third_party\mailgun\vendor\autoload.php';

use Mailgun\Mailgun;


class MailGunClient {
    function __construct($params = array()) {
        //parent::__construct();
    }
	function sendMail($params){
		$ci = get_instance();
		$mailgun = new Mailgun($ci->config->item('mailgun_key'), new \Http\Adapter\Guzzle6\Client());
		$mailgun->messages()->send('zab.ee', [
			'from'    => $params['from'],
			'to'      => $params['to'],
			'subject' => $params['subject'],
			'html'    => $params['message'],
		]);
		return $mailgun;
	}
}
?>