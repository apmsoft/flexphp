<?php
/** ======================================================
| @Author	: 펜씨업소프트
| @Email	: apmsoft@gmail.com
| @HomePage	: https://fancyupsoft.com
| @VERSION	: 0.6
----------------------------------------------------------*/
namespace Fus3\Sms;

# 펜씨업소프트 문자 호스팅 전송 API
class SmsSend extends Sms{

    private $host = 'http://115.68.182.167/sms/sms_send/';
    private $to = array();
    private $cu = null;
    public $echo_result_print = false; # 결과값 강제 출력 확인

    # 
	public function __construct($project_key, $sms_id, $sms_passwd)
	{
		parent::__construct($project_key, $sms_id, $sms_passwd);

		$this->cu = curl_init();
	}

    # set array phone numbers
    public function push(array $request_to){
        # 전송할 폰배열
        if(!is_array($request_to)){
            $errmsg = sprintf("%s :: %d SMS TO IS NOT ARRAY", __CLASS__,__LINE__);
            throw new ErrorException($errmsg);
        }

        #set data
        $this->to = $request_to;
    }

    # 전송
    #@ return array
    # * msg : *메세지 내용
    # * title : LMS 사용시 제목(꼭 입력할 필요는 없음)
    # * type : A : SMS만 허용(80바이트 넘으면 수신 불가) , C : LMS 허용, D : MMS 허용
    # * reservation : 1 (즉시발송), yyyy-MM-dd H:i:s (예약발송)
    # * mms_filename : MMS 첨부이미지(jpg), 1MByte 이하, 첨부이미지 풀경로
    public function send(array $fields_data)
    {
        # 내용 체크
        if(!$fields_data['msg'] || $fields_data['msg']==''){
            throw new ErrorException('SMS MESSAGE :: '.R::$sysmsg['e_null']);
        }

        # 예약발송 및 즉시발송 체크
        if(!isset($fields_data['reservation'])){
            $fields_data['reservation'] = 1;
        }

        # mms 첨부파일
        if(!isset($fields_data['mms_filename'])){
            $fields_data['mms_filename'] = '';
        }else if(trim($fields_data['mms_filename']) && trim($fields_data['mms_filename'])!=''){
            if(file_exists($fields_data['mms_filename']))
            {
                # SMS type 확인
                if($fields_data['type'] !='D'){
                    throw new ErrorException(R::$sysmsg['w_miss_sms_type']);
                }

                $handle   = @fopen($fields_data['mms_filename'], "r");
                $fileData = @fread( $handle, filesize($fields_data['mms_filename']) );
                @fclose( $handle );

                $fields_data['mms_filename'] = base64_encode($fileData);
            }else{
                $fields_data['mms_filename'] = '';
            }
        }

        # LMS 타이틀
        if(!isset($fields_data['title'])){
            $fields_data['title'] = '';
        }

        # TYPE
        if(!isset($fields_data['type'])){
            $fields_data['type'] = 'A';
        }
        
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