<?php
namespace Flex\Push;

use Flex\Http\HttpRequest;
use Flex\Log\Log;

class PushFCMMessage extends HttpRequest
{
	private string $url            = '';
	private string $serverApiKey   = '';
	private array $devices         = [];
	private $googleAccessToken     = []; // [access_token] => [expires_in] => 만료여부 3599[token_type] => Bearer[created] => 1656300909
	private $sendGoogleAccessToken = '';

	public function __construct(string $project_id){
		if(!$project_id){
			throw new \Exception('project_id is empty!!');
		}

		$this->url = sprintf("https://fcm.googleapis.com/v1/projects/%s/messages:send", $project_id);
	}

	#@void accesstoken
	public function getGoogleAccessToken( string $serviceAccountKey) : bool
	{
		$is_request = true;
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
			$token_argv   = explode('.', $this->googleAccessToken['access_token']);
			$access_token = sprintf("%s.%s.%s", $token_argv[0],$token_argv[1],$token_argv[2]);
			$this->sendGoogleAccessToken = sprintf("%s %s", $this->googleAccessToken['token_type'],$access_token );
			Log::d('NEW --> GoogleAccessToken ::: => ',$this->sendGoogleAccessToken);
			$is_request = true;
		}

	return $is_request;
	}

	# 하나씩 넣기
	public function setDeivce(string $deviceId, array $argv) : void
	{
		if(!$deviceId){
			throw new \Exception('deviceId is empty!!');
		}

		$fields = [
			"message" => [
				"token"        => $deviceId,
				"notification" => $argv
			]
		];

		$headers = [
			'Authorization: ' . $this->sendGoogleAccessToken,
			'Content-Type: application/json'
		];
		
		$this->devices[] = [
			'url'    => $this->url,
			'params' => $fields,
			'headers'=> $headers
		];
	}

	#전송
	public function send() : void
	{
		if(!is_array($this->devices) || count($this->devices) == 0){
			throw new \Exception('deviceId is empty!!');
		}

		#Log::d($this->devices);

		// 전송
		(new HttpRequest( $this->devices ))->post(function($data){
			if(is_array($data)){
				foreach($data as $idx => $contents){
					Log::d( $this->devices[$idx]['params']['message'], $contents);
				}
			}
		});
	}
}

?>