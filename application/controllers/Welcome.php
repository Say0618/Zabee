<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function sign_in_with_apple()
	{
		echo '<br /><a href="intent://callback?access_token=a215e0422969d44ef8b465993b9f2866a.0.mrytw.CIvjn9S5SLaC_U5I6_EKFA&token_type=Bearer&expires_in=3600&refresh_token=rf955a3b0359b45349af86fc2c575248e.0.mrytw.vKdT_feNLSzSMLpWj-eMJQ&id_token=eyJraWQiOiI4NkQ4OEtmIiwiYWxnIjoiUlMyNTYifQ.eyJpc3MiOiJodHRwczovL2FwcGxlaWQuYXBwbGUuY29tIiwiYXVkIjoiY29tLnNtYXJ0bWljcm9zLnNlcnZpY2VpZCIsImV4cCI6MTU5ODg2NTA0OCwiaWF0IjoxNTk4ODY0NDQ4LCJzdWIiOiIwMDE4MzYuNzU2YTUwMWEzMzI1NDlhZGI0MWMxNzBkNjQwYThlMWIuMDkwMCIsImF0X2hhc2giOiJFZkpVUjZVeHVqMUpURW5Hclkxd3hnIiwiZW1haWwiOiI0YnN0NHhwM2k2QHByaXZhdGVyZWxheS5hcHBsZWlkLmNvbSIsImVtYWlsX3ZlcmlmaWVkIjoidHJ1ZSIsImlzX3ByaXZhdGVfZW1haWwiOiJ0cnVlIiwiYXV0aF90aW1lIjoxNTk4ODY0NDQ2LCJub25jZV9zdXBwb3J0ZWQiOnRydWV9.hcsT-zDBg743qOlm--r75E2YHmc1R02eqgf8ImAXGLP9fF8hNE5i4d0_WKigkvF9PprXdTnbssZJleAKXP38zQ0Od-0ce2jTz2xxKTvkhTfYkNkQg66qwDgigcvC31AJdCkc4KDIDEDpr2WqkBnkChxM8jfBSqOF4hY1z7GYXGyiMAVGNdbap1HGY3meGa4NmxEWR8B1RS-k4v4u-UP3uzRXlifYit14Upbd2qeY9WXf5BREC5aVswwsRAKpvWyY8iosjQ_vaKx11xEXo_zxNHQB9azAOkmgHcu9N97S9DlSIoe0JB_bwerkwGRdxqdCmeIu2TJJSs1kfM5qv9byYg&code=c50d5b474f2294060b27f1df9d88fef4f.0.nrytw.wrMIG7p9sRdIz4HUqdK9mw&state=c800cd1b68&user=%7B%22name%22%3A%7B%22firstName%22%3A%22Mobeen%22%2C%22lastName%22%3A%22Shakil%22%7D%2C%22email%22%3A%224bst4xp3i6%40privaterelay.appleid.com%22%7D#Intent;package=com.smartmicros.zabee;scheme=signinwithapple;end">TEST</a><br />';
		$client_id = 'com.smartmicros.serviceid';
		$client_secret = 'eyJraWQiOiIyNjM1UTg0Tkc3IiwiYWxnIjoiRVMyNTYifQ.eyJpc3MiOiI5WTI2OUdYQ1UzIiwiaWF0IjoxNjEwNTQ0MDU0LCJleHAiOjE2MjYwOTYwNTQsImF1ZCI6Imh0dHBzOi8vYXBwbGVpZC5hcHBsZS5jb20iLCJzdWIiOiJjb20uc21hcnRtaWNyb3Muc2VydmljZWlkIn0.EOe296XPNI5OQYATx3HPX7MTQLx6zYNoc4fj1sXQT3WBOUUsGKw-coLekAtf6MiVEDfezjsBUGKouRa3q8SXaw';
		//$redirect_uri = 'intent://callback?${}#Intent;package=com.smartmicros.zabee;scheme=signinwithapple;end';
		$redirect_uri = 'intent://callback?name=abc#Intent;package=com.smartmicros.zabee;scheme=signinwithapple;end';
		if(isset($_POST['code'])) {
			echo '<pre>';print_r($_POST);echo '</pre>';
			//$res = array('code'=>$_POST['authorizationCode']);
			//$redirect_uri = 'intent://callback?{'.urlencode(json_encode($res)).'}#Intent;package=com.smartmicros.zabee;scheme=signinwithapple;end';
			//if($_SESSION['state'] != $_POST['state']) {
				//die('Authorization server returned an invalid state parameter');
			//}
			//    Token endpoint docs:
			//    https://developer.apple.com/documentation/signinwithapplerestapi/generate_and_validate_tokens

			$response = $this->http('https://appleid.apple.com/auth/token', [
				'grant_type' => 'authorization_code',
				'code' => $_POST['code'],
				'redirect_uri' => base_url('callbacks/sign_in_with_apple'),
				//'redirect_uri' => $redirect_uri,
				'client_id' => $client_id,
				'client_secret' => $client_secret,
			]);

			if(!isset($response->access_token)) {
				echo '<p>Error getting an access token:</p>';
				echo '<pre>'; print_r($response); echo '</pre>';
				echo '<p><a href="/">Start Over</a></p>';
				die();
			}
			$response->code = $_POST['code'];
			$response->state = $_POST['state'];
			$response->user = (isset($_POST['user']))?$_POST['user']:array();
			$redirect_uri = 'intent://callback?{'.http_build_query($response).'}#Intent;package=com.smartmicros.zabee;scheme=signinwithapple;end';
			redirect($redirect_uri);exit();
			echo '<a href="'.$redirect_uri.'">Back to app</a>';
			echo '<h3>Access Token Response</h3>';
			echo '<pre>'; print_r($response); echo '</pre>';

			$claims = explode('.', $response->id_token)[1];
			$claims = json_decode(base64_decode($claims));

			echo '<h3>Parsed ID Token</h3>';
			echo '<pre>';print_r($claims);echo '</pre>';
			die();
		}
		/// Client request
      	$_SESSION['state'] = $this->getStateKey();
		if(isset($_SESSION['state'])){
			$authorize_url = 'https://appleid.apple.com/auth/authorize'.'?'.http_build_query([
				'response_type' => 'code',
				'response_mode' => 'form_post',
				'client_id' => $client_id,
				'redirect_uri' => base_url('callbacks/sign_in_with_apple'), //$redirect_uri,
				'state' => $_SESSION['state'],
				'scope' => 'name email',
			]);
			echo '<a href="'.$authorize_url.'">Sign In with Apple</a>';
		} else {
			$this->load->helper('url'); 
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	private function http($url, $params=false) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($params)
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'User-Agent: curl', # Apple requires a user agent header at the token endpoint
		]);
		$response = curl_exec($ch);
		return json_decode($response);
	}
	private function getStateKey(){
		return bin2hex(random_bytes(5));
	}
	public function getState(){
		$_SESSION['state'] = $this->getStateKey();
		echo $_SESSION['state'];
	}
}
