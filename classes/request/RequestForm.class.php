<?php
namespace Flex\Request;

use Flex\R\R;
use Flex\Request\RequestValidation;

# 폼체크
class RequestForm extends RequestValidation
{
	const VERSEION = '2.0.1';
    private string $fieldName;
    private string $title;

	public function __construct(string $fieldName, string $title,mixed $value){ 
        $this->fieldName = $fieldName;
        $this->title = $title;
        parent::__construct($value);
    return $this; 
    }

    # 필수 옵션
    public function null () : RequestForm 
    {
        if(parent::isNull()) {
            self::error_report($this->fieldName, 'e_null', sprintf("%s %s", $this->title, R::$sysmsg[R::$language]['e_null']));
        }
    return $this;
    }

    # 길이
    public function length (int $min, int $max) : RequestForm 
    {
        if(!parent::isStringLength([$min, $max])){
            $err_msg =sprintf( R::$sysmsg[R::$language]['e_string_length'], $min, $max );
            self::error_report($this->fieldName, 'e_string_length', sprintf("%s %s", $this->title, $err_msg));
        }
    return $this;
    }

    # 특수 문자 있으면 reject
    public function disliking (array $arguments=[]) : RequestForm
    {
        # 허용된 특수문자를 제거 한다.
        if(is_array($arguments)){
            foreach($arguments as $etcstr){
                $this->str = str_replace($etcstr,'', $this->str);
            }
        }
        
        if(!parent::isEtcString()){
            $etc_msg = (count($arguments)) ? '['.implode(',',$arguments).']' : '';
            $err_msg = sprintf(R::$sysmsg[R::$language]['e_etc_string'],$etc_msg);
            self::error_report($this->fieldName, 'e_etc_string', sprintf("%s %s", $this->title, $err_msg));
        }
    return $this;
    }

    # 특수 문자 없으면 에러 (최소 1개이상 입력)
    public function liking (array $arguments=[]) : RequestForm
    {        
        if(parent::isEtcString()){
            self::error_report($this->fieldName, 'e_chk_etc_string', sprintf("%s %s", $this->title, R::$sysmsg[R::$language]['e_chk_etc_string']));
        }
    return $this;
    }

    # 공백체크
    public function space () : RequestForm
    {
        if(!parent::isSpace()){
            self::error_report($this->fieldName, 'e_spaces', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_spaces']));
        }
    return $this;
    }

    # 영문또는 숫자 만
    public function alnum () : RequestForm 
    {
        if(!ctype_alnum($this->str)){
            self::error_report($this->fieldName, 'e_ctype_alnum', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_ctype_alnum']));
        }
    return $this;
    }

    # 연속반복문자 체크
    public function repeat(int $max) : RequestForm 
    {
        if(!parent::isSameRepeatString($max)){
            $err_msg = sprintf(R::$sysmsg[R::$language]['e_same_repeat_string'], $max);
            self::error_report($this->fieldName, 'e_same_repeat_string', sprintf("%s %s", $this->title,$err_msg));
        }
    }

    # 숫자인지 체크
    public function number() : RequestForm 
    {
        if(!parent::isNumber()){
            self::error_report($this->fieldName, 'e_number', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_number']));
        }
    return $this;
    }

    # 영어만 체크
    public function alphabet () : RequestForm 
    {
        if(!parent::isAlphabet()){
            self::error_report($this->fieldName, 'e_alphabet', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_alphabet']));
        }
    return $this;
    }

    # 알파벳인지 대문자 인지 체크
    public function upal () : RequestForm
    {
        if(!parent::isUpAlphabet()){
            self::error_report($this->fieldName, 'e_up_alphabet', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_up_alphabet']));
        }
    return $this;
    }

    # 알파벳인지 소문자 인지 체크
    public function lowal () : RequestForm
    {
        if(!parent::isLowAlphabet()){
            self::error_report($this->fieldName, 'e_low_alphabet', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_low_alphabet']));
        }
    return $this;
    }

    # 첫글자가 알파벳인지 체크
    public function firstal () : RequestForm
    {
        if(!parent::isFirstAlphabet()){
            self::error_report($this->fieldName, 'e_first_alphabet', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_first_alphabet']));
        }
    return $this;
    }

    # json 타입의 데이터인지 체크
    public function jsonf() :RequestForm 
    {
        if(!parent::isJSON()){
            self::error_report($this->fieldName, 'e_json', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_json']));
        }
    return $this;
    }

    # 날짜데이터인지 체크
    public function datef() :RequestForm 
    {
        if(!parent::chkDate()){
            self::error_report($this->fieldName,'e_date', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_date']));
        }
    return $this;
    }

    # 시간 데이터인지 체크
    public function timef() :RequestForm 
    {
        if(!parent::chkTime()){
            self::error_report($this->fieldName,'e_time', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_time']));
        }
    return $this;
    }

    # 시작날짜와 종료날짜 이 올바른지 체크
    public function dateperiod (string $end_date) : RequestForm
    {
        $this->str = $this->str.','.$end_date;
        if(!parent::chkDatePeriod_()){
            self::error_report($this->fieldName, 'e_date_period',sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_date_period']));
        }
    return $this;
    }

    # 두 문자가 일치하는지 체크
    public function equal (mixed $value) : RequestForm
    {
        $this->str = $this->str.','.$value;
        if(!parent::equals()){
            self::error_report($this->fieldName, 'e_equals', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_equals']));
        }
    return $this;
    }

    # 이메일 데이터인지 체크
    public function email () : RequestForm 
    {
        if(!filter_var($this->str, FILTER_VALIDATE_EMAIL)){
            self::error_report($this->fieldName, 'e_formality', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_formality']));
        }
    return $this;
    }

    # http:: url 데이터인지 체크
    public function url () : RequestForm
    {
        if(!filter_var($this->str, FILTER_VALIDATE_URL)){
            self::error_report($this->fieldName, 'e_link_url', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_link_url']));
        }
    return $this;
    }

    # 소수형 데이터 인지 체크
    public function floatf () : RequestForm
    {
        if(!is_float(floatval($this->str))){
            self::error_report($this->fieldName, 'e_float', sprintf("%s %s", $this->title,R::$sysmsg[R::$language]['e_float']));
        }
    return $this;
    }

	private function error_report(string $field, string $msg_code, string $msg)
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
