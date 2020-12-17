<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://www.apmsoftax.com
| @version : 1.1
----------------------------------------------------------*/
#example
# if(count($this->ios_tokens)>0){
# 	$iosPush = new PushIOSMessage($apn_path);
# 	$iosPush->service = 'dev';
# 	$iosPush->setDevices($this->ios_tokens);
# 	$iosPush->send(
# 		$title,
# 		array(
# 	        'type' => '1',
# 	        'msg' => $message,
# 	        'mode' => $mode,
# 	        'uid'=>$tuid
#     	)
# 	);
# }
namespace Fus3\Push;

use Fus3\Out\Out;

class PushIOSMessage
{
	private $apn_url = '';
	private $devices = array();
	
	public $service  = 'dev';
	private $ssl_url = array(
		'service' => 'ssl://gateway.push.apple.com:2195',
		'dev'     =>'ssl://gateway.sandbox.push.apple.com:2195'
	);
	public $log = false;	// true : echo message, false: none;
	private $fp;
	
	public function __construct($apn_path){
		if($apn_path){
			$this->apn_url = $apn_path;
			
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', $this->apn_url);

			$this->fp = stream_socket_client($this->ssl_url[$this->service], $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			if (!$this->fp) {
				$this->error("Failed to connect $err $errstr\n");
				return;
			}
		}
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
	public function send($title,$argv)
	{		
		if(!is_array($this->devices) || count($this->devices) == 0){
			$this->error("No devices set");
		}

		$count = count($this->devices);
		for ($i=0; $i<$count; $i++)	
		{
			$fields = array(
				'aps' => array(
					'alert' => $title, 
					'badge' => 1, 
					'sound' => 'default'
				)
			);
			$fields = array_merge($fields,$argv);

			$payload = json_encode($fields);
			$msg = chr(0).pack("n",32).pack('H*',$this->devices[$i]).pack("n",strlen($payload)).$payload;
			
			# log print
			if($this->log){
				Out::prints_ln("log : ". $payload);
			}
			fwrite($this->fp, $msg);
		}
		fclose($this->fp);
	}
	
	#@ void
	private function error($msg){
		echo "IOS send notification failed with error:";
		echo "\t" . $msg;
		exit(1);
	}
}
?>