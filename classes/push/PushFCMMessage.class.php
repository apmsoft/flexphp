<?php
namespace Flex\Push;

use Flex\Log\Log;

class PushFCMMessage
{
	private string $url   = '';
	private $serverApiKey = '';
	private $devices      = [];
	public $log           = false;	// true : echo message, false: none;
	private $googleAccessToken = []; // [access_token] => [expires_in] => 만료여부 3599[token_type] => Bearer[created] => 1656300909
	private $sendGoogleAccessToken = '';

	public function __construct(string $project_id){
		$this->url = sprintf("https://fcm.googleapis.com/v1/projects/%s/messages:send", $project_id);
	}

	#@void accesstoken
	public function getGoogleAccessToken( string $serviceAccountKey) : bool
	{
		$is_request = false;
		if(count($this->googleAccessToken))
		{
			$is_request = true;
			$now = time();
			Log::d('생성시간 ', date('Y-m-d H:i:s', $this->googleAccessToken['created']));
			$pre_sstime = strtotime(sprintf("-%d seconds",$this->googleAccessToken['expires_in']), $now);
			Log::d('1시간전 : ',date('Y-m-d H:i:s', $pre_sstime));
			if($this->googleAccessToken['created']<= $pre_sstime){
				$is_request = false;
			}

			Log::d('OLD --> GoogleAccessToken ::: => '.$this->sendGoogleAccessToken);
		}
		
		if(!$is_request)
		{
			$client = new \Google_Client();
			$client->setAuthConfig($serviceAccountKey);
			$client->addScope('https://www.googleapis.com/auth/firebase.messaging');
			$client->refreshTokenWithAssertion();
			$this->googleAccessToken = $client->getAccessToken();
			
			Log::d($this->googleAccessToken);
			$token_argv = explode('.', $this->googleAccessToken['access_token']);
			$access_token = sprintf("%s.%s.%s", $token_argv[0],$token_argv[1],$token_argv[2]);
			$this->sendGoogleAccessToken = sprintf("%s %s", $this->googleAccessToken['token_type'],$access_token );
			Log::d('NEW --> GoogleAccessToken ::: => ',$this->sendGoogleAccessToken);
		}

		return $this->sendGoogleAccessToken;
	}

	#@ void
	#배열값 한번에 넣기
	public function setDevices($deviceIds){
		if(is_array($deviceIds)){
			$this->devices = $deviceIds;
		}
	}

	#@ void
	# 하나씩 넣기
	public function setDeivce($deviceId){
		// if(is_array($deviceId)){
			$this->devices[] = $deviceId;
		// }
	}

	#@ void
	#전송
	public function send($argv)
	{
		if(!is_array($this->devices) || count($this->devices) == 0){
			$this->error("No devices set");
		}

		if(strlen($this->sendGoogleAccessToken) < 8){
			$this->error("Server API Key not set");
		}

		$fields = [
			"message" => [
				"token" => $this->devices[0],
				"notification" => $argv
			]
		];
		Log::d($fields);
		Log::d(json_encode($fields));

		$headers = array(
			'Authorization: ' . $this->sendGoogleAccessToken,
			'Content-Type: application/json'
		);

		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_URL, $this->url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

		// Execute post
		$result = curl_exec($ch);
		Log::d($result);

		// Close connection
		curl_close($ch);
	}
}

?>