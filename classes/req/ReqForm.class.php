<?php
namespace Flex\Req;

use Flex\R\R;
use Flex\Req\ReqStrChecker;

# 폼체크
class ReqForm extends ReqStrChecker
{
	const VERSEION = '2.0';

	public function __construct(){
	}

	# 널값만 체크
	public function chkNull(string $field,string $title, mixed $value, bool $required, array $filters=[]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

    # 아이디체크
    public function chkUserid(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'        => [],
		'isStringLength' => [4,16],
		'isKorean'       => []
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

        if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
        }
    }

	# 비밀번호
	public function chkPasswd(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'        => [],
		'isStringLength' => [4,160],
		'isKorean'       => []
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 비밀번호 보안강화(최소8자 및 특수문자 한글자 포함)
	public function chkPasswdSecure(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'        => [],
		'isStringLength' => [8,160],
		'isKorean'       => [],
		'isEtcString'    => ['_','$','#','!','^','*','-','@','&','(',')','+']
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 이름
	public function chkName(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'        => [],
		'isEtcString'    => []
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 전화번호
	public function chkPhone(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'  	  => [],
		'isNumber'    => [],
		'isEtcString' => ['-']
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			# 허용된 특수문자를 제거 한다.
			if(isset($filters['isEtcString'])){
				foreach($filters['isEtcString'] as $etcstr){
					$value = str_replace($etcstr,'', $value);
				}
			}

			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 숫자만 int
	public function chkNumber(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'  => [],
		'isNumber' => []
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 더블 double
	public function chkFloat(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'     => [],
		'isNumber'    => [],
		'isEtcString' => ['.']
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value)
		{
			# 허용된 특수문자를 제거 한다.
			if(isset($filters['isEtcString'])){
				foreach($filters['isEtcString'] as $etcstr){
					$value = str_replace($etcstr,'', $value);
				}
			}

			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}

			if(!is_float(floatval($value))){
				$this->error_report($field, 'e_float', sprintf("%s %s", $title,R::$sysmsg['e_float']));
			}
		}
	}

	# 영문만 english
	public function chkAlphabet(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'    => [],
		'isAlphabet' => []
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 이메일 sed_-23@apmsoftax.com
	public function chkEmail(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'        => [],
		'isKorean'       => [],
		'isEtcString'    => ['@','-','_']
	]) : void 
	{
		$value = filter_var($value,FILTER_SANITIZE_EMAIL);

		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}

			if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
				$this->error_report($field, 'e_formality', sprintf("%s %s", $title,R::$sysmsg['e_formality']));
			}
		}
	}

	# 일반 영어/숫자/언더라인 만 허용
	public function chkEngNumUnderline(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace'     => [],
		'isKorean'    => [],
		'isEtcString' => ['_']
	]) : void 
	{
		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
	}

	# 링크주소
	public function chkLinkurl(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace' => [],
	]) : void 
	{
		$value =filter_var($value,FILTER_SANITIZE_URL);

		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			if(!filter_var($value, FILTER_VALIDATE_URL)){
				$this->error_report($field, 'e_link_url', sprintf("%s %s", $title,R::$sysmsg['e_link_url']));
			}
		}
	}

    # 날짜
    public function chkDateFormat(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace' => [],
		'chkDate' => []
	]) : void 
	{
        if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

        if($value){
            foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
        }
	}
	
	# 시간
	public function chkTimeFormat(string $field, string $title, mixed $value, bool $required, array $filters=[
		'isSpace' => [],
		'chkTime' => []
	]) : void 
	{
        if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

        if($value){
            foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
        }
	}

    #@ void
    # 기간체크
    # $field_args = array('sdate','edate')
    # $value_args= array($_REQUEST['sdate'],$_REQUEST'edate'])
    # $required = true | false
    public function chkDatePeriod(string $field,string $title,array $value_args, bool $required, array $filters=[]) : void 
	{
		$value = implode('', $value_args);

		if($required){
			self::chkValidation($field, $title, $value,'isNull');
        }

        if($value)
        {
            // 기간체크
			self::chkValidation($field, $title, implode(',', $value_args), 'chkDatePeriod');

			// 추가 필터
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, title, $value, $methodName, $arguments);
			}
		}
    }

    #@void
    #$argsv = array($req->v, 비교값);
	# $value_args = ['문자1','문자2'];
    public function chkEquals(string $field,string $title,array $value_args,bool $required, array $filters=[]) : void 
	{
		$value = implode('', $value_args);

		if($required){
			self::chkValidation($field, $title, $value, 'isNull');
        }

		if($value){
			self::chkValidation($field, $title, implode(',', $value_args), 'equals');
        }

		if($value){
			foreach ($filters as $methodName => $arguments) {
				self::chkValidation($field, $title, $value, $methodName, $arguments);
			}
		}
    }

	# 메소드에 따른 체크 기능
	private function chkValidation(string $field, string $title, mixed $value, string $methodName, array $arguments=[]) : void
	{
		parent::__construct($value);
		switch($methodName){
			case 'isNull' :
				if(parent::isNull()) {
					$this->error_report($field, 'e_null', sprintf("%s %s", $title,R::$sysmsg['e_null']));
				}
				break;
			case 'isSpace':
				if(!parent::isSpace()){
					$this->error_report($field, 'e_spaces', sprintf("%s %s", $title,R::$sysmsg['e_spaces']));
				}
				break;
			case 'isStringLength':
				if(!parent::isStringLength($arguments)){
					$this->error_report($field, 'e_string_length', sprintf("%s %s", $title,R::$sysmsg['e_string_length']));
				}
				break;
			case 'isKorean':
				if(parent::isKorean()){
					$this->error_report($field, 'e_korean', sprintf("%s %s", $title,R::$sysmsg['e_korean']));
				}
				break;
			case 'isEtcString':
				if(!parent::isEtcString(implode(',',$arguments))){
					$etc_msg = (count($arguments)) ? '['.implode(',',$arguments).']' : '';
					$err_msg = sprintf(R::$sysmsg['e_etc_string'],$etc_msg);
					$this->error_report($field, 'e_etc_string', sprintf("%s %s", $title,$err_msg));
				}
				break;
			case 'isSameRepeatString':
				if(!parent::isSameRepeatString($arguments)){
					$err_msg = sprintf(R::$sysmsg['e_same_repeat_string'], implode(',',$arguments));
					$this->error_report($field, 'e_same_repeat_string', sprintf("%s %s", $title,$err_msg));
				}
				break;
			case 'isNumber':
				if(!parent::isNumber()){
					$this->error_report($field, 'e_number', sprintf("%s %s", $title,R::$sysmsg['e_number']));
				}
				break;
			case 'isAlphabet':
				if(!parent::isAlphabet()){
					$this->error_report($field, 'e_alphabet', sprintf("%s %s", $title,R::$sysmsg['e_alphabet']));
				}
				break;
			case 'isUpAlphabet':
				if(!parent::isUpAlphabet()){
					$this->error_report($field, 'e_up_alphabet', sprintf("%s %s", $title,R::$sysmsg['e_up_alphabet']));
				}
				break;
			case 'isLowAlphabet':
				if(!parent::isLowAlphabet()){
					$this->error_report($field, 'e_low_alphabet', sprintf("%s %s", $title,R::$sysmsg['e_low_alphabet']));
				}
				break;
			case 'isFirstAlphabet':
				if(!parent::isFirstAlphabet()){
					$this->error_report($field, 'e_first_alphabet', sprintf("%s %s", $title,R::$sysmsg['e_first_alphabet']));
				}
				break;
			case 'isJSON':
				if(!parent::isJSON()){
					$this->error_report($field, 'e_json', sprintf("%s %s", $title,R::$sysmsg['e_json']));
				}
				break;
			case 'chkDate':
				if(!parent::chkDate()){
					$this->error_report($field,'e_date', sprintf("%s %s", $title,R::$sysmsg['e_date']));
				}
				break;
			case 'chkTime':
				if(!parent::chkTime()){
					$this->error_report($field,'e_time', sprintf("%s %s", $title,R::$sysmsg['e_time']));
				}
				break;
			case 'chkDatePeriod':
				if(!parent::chkDatePeriod_()){
					$this->error_report($field, 'e_date_period',sprintf("%s %s", $title,R::$sysmsg['e_date_period']));
				}
				break;
			case 'equals':
				if(!parent::equals($value)){
					$this->error_report($field, 'e_isnot_match', sprintf("%s %s", $title,R::$sysmsg['e_isnot_match']));
				}
				break;
		}
	}

	public function error_report(string $field, string $msg_code, string $msg)
	{
		throw new \Exception(strval(
			json_encode(
				['result'=>'false','fieldname'=>$field,'msg_code'=>$msg_code,'msg'=>$msg], 
				JSON_UNESCAPED_UNICODE
			)
		));
	}
}
?>
