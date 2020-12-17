<?php
/** ======================================================
| @Author	: 펜씨업소프트
| @Email	: apmsoft@gmail.com
| @HomePage	: https://fancyupsoft.com
| @VERSION	: 0.6
----------------------------------------------------------*/
namespace Fus3\Sms;

# 펜씨업소프트 문자 호스팅 잔여량 확인
class SmsAmount extends Sms{

    private $host = 'http://115.68.182.167/sms/sms_amount/';
    private $cu = null;
    public $echo_result_print = false; # 결과값 강제 출력 확인

    # 
	public function __construct($project_key, $sms_id, $sms_passwd)
	{
        parent::__construct($project_key, $sms_id, $sms_passwd);

		$this->cu = curl_init();
	}

    # 잔여량 확인
    #@ return array
    public function getRemainingAmount()
    {
        # set header
        $cipherEncrypt = new CipherEncrypt($this->project_key);
        $project_key = $cipherEncrypt->_base64_urlencode();
        $cipherEncrypt = new CipherEncrypt($this->sms_id);
        $sms_id = $cipherEncrypt->_md5_utf8encode();
        $cipherEncrypt = new CipherEncrypt($this->sms_passwd);
        $sms_passwd = $cipherEncrypt->_md5_utf8encode();
        $authorization = sprintf("project_key=%s", $project_key);
        $authorization.= sprintf("&sms_id=%s", $sms_id);
        $authorization.= sprintf("&sms_passwd=%s", $sms_passwd);        
        $headers = array( 
            'Authorization: '.$authorization,
            'Content-Type: application/json'
        );

        // Set the url, number of POST vars, POST data
		curl_setopt( $this->cu, CURLOPT_URL, $this->host);		
		curl_setopt( $this->cu, CURLOPT_POST, true);
		curl_setopt( $this->cu, CURLOPT_HTTPHEADER, $headers);
		curl_setopt( $this->cu, CURLOPT_RETURNTRANSFER, true);
		
		// Execute post
		$result = curl_exec($this->cu);
        if($this->echo_result_print){
            if (curl_error($this->cu)) {
                $error_msg = curl_error($this->cu);
                throw new ErrorException($error_msg);
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