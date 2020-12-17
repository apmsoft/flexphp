<?php
namespace PushSDK\Push;

use PushSDK\Cipher\CipherEncrypt;
use \ErrorException;
/** ======================================================
| @Author	: 펜씨업소프트
| @Email	: apmsoft@gmail.com
| @HomePage	: https://www.fancyupsoft.com
| @VERSION	: 0.6
----------------------------------------------------------*/

# 펜씨업소프트 푸시 호스팅 전송 API
class PushSend extends Push{

    private $host = 'http://115.68.182.167/push/push_send/';
    private $to = array();
    private $cu = null;
    public $echo_result_print = true; # 결과값 강제 출력 확인

    # 
	public function __construct($project_key, $push_id, $push_passwd)
	{
		parent::__construct($project_key, $push_id, $push_passwd);

		$this->cu = curl_init();
	}

    # set array push token
    public function push(array $request_to){
        # 전송할 푸시 토큰 배열
        if(!is_array($request_to)){
            $errmsg = sprintf("%s :: %d PUSH TO IS NOT ARRAY", __CLASS__,__LINE__);
            throw new ErrorException($errmsg);
        }

        #set data
        $this->to = $request_to;
    }

    # 전송
    #@ return array
    public function send(array $fields_data)
    {
        # 제목체크
        if(!$fields_data['title'] || $fields_data['title']==''){
            throw new ErrorException('PUSH TITLE :: '.R::$sysmsg['e_null']);
        }

        # 내용 체크
        if(!$fields_data['msg'] || $fields_data['msg']==''){
            throw new ErrorException('PUSH MESSAGE :: '.R::$sysmsg['e_null']);
        }

        # 예약발송 및 즉시발송 체크
        // if(!isset($fields_data['reservation'])){
            $fields_data['reservation'] = 1;
        // }
        
        # set header
        $cipherEncrypt = new CipherEncrypt($this->project_key);
        $project_key = $cipherEncrypt->_base64_urlencode();
        $cipherEncrypt = new CipherEncrypt($this->push_id);
        $push_id = $cipherEncrypt->_md5_utf8encode();
        $cipherEncrypt = new CipherEncrypt($this->push_passwd);
        $push_passwd = $cipherEncrypt->_md5_utf8encode();
        $authorization = sprintf("project_key=%s", $project_key);
        $authorization.= sprintf("&push_id=%s", $push_id);
        $authorization.= sprintf("&push_passwd=%s", $push_passwd);        
        $headers = array( 
            'Authorization: '.$authorization,
            'Content-Type: application/json'
        );

        # set fields
        $fields = array(
			'registration_ids' => $this->to,
			'data' => $fields_data
		);

        // Set the url, number of POST vars, POST data
		curl_setopt( $this->cu, CURLOPT_URL, $this->host);
		curl_setopt( $this->cu, CURLOPT_POST, true);
		curl_setopt( $this->cu, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $this->cu, CURLOPT_RETURNTRANSFER, true);		
		curl_setopt( $this->cu, CURLOPT_POSTFIELDS, json_encode($fields));

		// Execute post
		$result = curl_exec($this->cu);
        if($this->echo_result_print){
            if (curl_error($this->cu)) {
                $error_msg = curl_error($this->cu);
                throw new ErrorException($error_msg.' '.$result);
            }
        }
		
		// log print
		$resp = json_decode($result);
        if($resp->result == 'false'){
            throw new ErrorException($resp->msg);
        }

        return array(
            'remaining_count'=>$resp->remaining_count, 
            'msg'=>$resp->msg
        );
    }

    # db close
	public function __destruct(){
		curl_close($this->cu);
	}
}
?>