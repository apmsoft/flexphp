<?php
namespace Fus3\Push;

use Fus3\Out\Out;

class PushFCMiOSMessage
{
	private $url          = 'https://fcm.googleapis.com/fcm/send';
	private $serverApiKey = '';
	private $devices      = array();
	public $log           = false;	// true : echo message, false: none;

	public function __construct($apiKeyIn){
		$this->serverApiKey = $apiKeyIn;
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
		if(is_array($deviceId)){
			$this->devices[] = $deviceId;
		}
	}

	#@ void
    #전송
    /*
    array(
        "id"    => strtotime("now"),
        'title' => '앱타이틀명',
        'body'  => '푸싱테스트 내용',
        'sound' => "default"
    )
    */
	public function send($argv)
	{
		if(!is_array($this->devices) || count($this->devices) == 0){
			$this->error("No devices set");
		}

		if(strlen($this->serverApiKey) < 8){
			$this->error("Server API Key not set");
		}

		$fields = array(
            'registration_ids' => $this->devices,
            'priority' => 'high',
            'notification' => $argv,
			'time_to_live' => 600
		);

		$headers = array(
			'Authorization:key=' . $this->serverApiKey,
			'Content-Type: application/json'
		);

		// Open connection
		$ch = curl_init();

		// Set the url, number of POST vars, POST data
		curl_setopt( $ch, CURLOPT_URL, $this->url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

		// Execute post
		$result = curl_exec($ch);
		if($this->log)
		{
			if($result){
				$prlt = json_decode($result, true);
				if($prlt['failure'] == 1){
					Out::prints_r($prlt);
				}
			}
			// log print
			else if ($result === FALSE) {
				echo curl_error($ch);
			}
		}

		// Close connection
		curl_close($ch);
	}

	#@ void
	private function error($msg){
		echo "iOS send notification failed with error:";
		echo "\t" . $msg;
		exit(1);
	}
}

?>
