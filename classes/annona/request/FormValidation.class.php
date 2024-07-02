<?php
namespace Flex\Annona\Request;

use Flex\Annona\R;
use Flex\Annona\Request\Validation;

# 폼체크
class FormValidation extends Validation
{
	public const __version = '2.1';
    private string $fieldName;
    private string $title;
    private bool $required = false;

	public function __construct(string $fieldName, string $title,mixed $value){
        $this->fieldName = $fieldName;
        $this->title = $title;
        parent::__construct($value);
    }

    # 필수 옵션
    public function null () : FormValidation
    {
        $this->required = true;
        if(parent::isNull()) {
            self::error_report($this->fieldName, 'e_null', sprintf("%s %s", $this->title, R::sysmsg('e_null')));
        }
    return $this;
    }

    # 길이
    public function length (int $min, int $max) : FormValidation
    {
        if($this->str && !parent::isStringLength([$min, $max])){
            $err_msg =sprintf( R::sysmsg('e_string_length'), $min, $max );
            self::error_report($this->fieldName, 'e_string_length', sprintf("%s %s", $this->title, $err_msg));
        }
    return $this;
    }

    # 특수 문자 있으면 reject
    public function disliking (array $arguments=[]) : FormValidation
    {
        if($this->str){
            # 허용된 특수문자를 제거 한다.
            if(is_array($arguments)){
                foreach($arguments as $etcstr){
                    $this->str = str_replace($etcstr,'', $this->str);
                }
            }

            if(!parent::isEtcString()){
                $etc_msg = (count($arguments)) ? '['.implode(',',$arguments).']' : '';
                $err_msg = sprintf(R::sysmsg('e_etc_string'),$etc_msg);
                self::error_report($this->fieldName, 'e_etc_string', sprintf("%s %s", $this->title, $err_msg));
            }
        }
    return $this;
    }

    # 특수 문자 없으면 에러 (최소 1개이상 입력)
    public function liking (array $arguments=[]) : FormValidation
    {
        if($this->str && parent::isEtcString()){
            self::error_report($this->fieldName, 'e_chk_etc_string', sprintf("%s %s", $this->title, R::sysmsg('e_chk_etc_string')));
        }
    return $this;
    }

    # 공백체크
    public function space () : FormValidation
    {
        if($this->str && !parent::isSpace()){
            self::error_report($this->fieldName, 'e_spaces', sprintf("%s %s", $this->title,R::sysmsg('e_spaces')));
        }
    return $this;
    }

    # enum
    public function enum (array $arguments=[]) : FormValidation
    {
        if($this->str){
            if(array_search($this->str, $arguments) === false)
            self::error_report($this->fieldName, 'e_enum', sprintf("%s %s", $this->title,R::sysmsg('e_enum')));
        }
    return $this;
    }

    # 영문또는 숫자 만
    public function alnum () : FormValidation
    {
        if($this->str && !ctype_alnum($this->str)){
            self::error_report($this->fieldName, 'e_ctype_alnum', sprintf("%s %s", $this->title,R::sysmsg('e_ctype_alnum')));
        }
    return $this;
    }

    # 연속반복문자 체크
    public function repeat(int $max) : FormValidation
    {
        if($this->str && !parent::isSameRepeatString($max)){
            $err_msg = sprintf(R::sysmsg('e_same_repeat_string'), $max);
            self::error_report($this->fieldName, 'e_same_repeat_string', sprintf("%s %s", $this->title,$err_msg));
        }
    }

    # 숫자인지 체크
    public function number() : FormValidation
    {
        if($this->str && !parent::isNumber()){
            self::error_report($this->fieldName, 'e_number', sprintf("%s %s", $this->title,R::sysmsg('e_number')));
        }
    return $this;
    }

    # 영어만 체크
    public function alphabet () : FormValidation
    {
        if($this->str && !parent::isAlphabet()){
            self::error_report($this->fieldName, 'e_alphabet', sprintf("%s %s", $this->title,R::sysmsg('e_alphabet')));
        }
    return $this;
    }

    # 알파벳인지 대문자 인지 체크
    public function upal () : FormValidation
    {
        if($this->str && !parent::isUpAlphabet()){
            self::error_report($this->fieldName, 'e_up_alphabet', sprintf("%s %s", $this->title,R::sysmsg('e_up_alphabet')));
        }
    return $this;
    }

    # 알파벳인지 소문자 인지 체크
    public function lowal () : FormValidation
    {
        if($this->str && !parent::isLowAlphabet()){
            self::error_report($this->fieldName, 'e_low_alphabet', sprintf("%s %s", $this->title,R::sysmsg('e_low_alphabet')));
        }
    return $this;
    }

    # 첫글자가 알파벳인지 체크
    public function firstal () : FormValidation
    {
        if($this->str && !parent::isFirstAlphabet()){
            self::error_report($this->fieldName, 'e_first_alphabet', sprintf("%s %s", $this->title,R::sysmsg('e_first_alphabet')));
        }
    return $this;
    }

    # json 타입의 데이터인지 체크
    public function jsonf() :FormValidation
    {
        if($this->str && !parent::isJSON()){
            self::error_report($this->fieldName, 'e_json', sprintf("%s %s", $this->title,R::sysmsg('e_json')));
        }
    return $this;
    }

    # 날짜데이터인지 체크
    public function datef() :FormValidation
    {
        if($this->str && !parent::chkDate()){
            self::error_report($this->fieldName,'e_date', sprintf("%s %s", $this->title,R::sysmsg('e_date')));
        }
    return $this;
    }

    # 시간 데이터인지 체크
    public function timef() :FormValidation
    {
        if($this->str && !parent::chkTime()){
            self::error_report($this->fieldName,'e_time', sprintf("%s %s", $this->title,R::sysmsg('e_time')));
        }
    return $this;
    }

    # 시작날짜와 종료날짜 이 올바른지 체크
    public function dateperiod (string $end_date) : FormValidation
    {
        if($this->str){
            $this->str = $this->str.','.$end_date;
            if(!parent::chkDatePeriod_()){
                self::error_report($this->fieldName, 'e_date_period',sprintf("%s %s", $this->title,R::sysmsg('e_date_period')));
            }
        }
    return $this;
    }

    # 두 문자가 일치하는지 체크
    public function equal (mixed $value) : FormValidation
    {
        if($this->str){
            $this->str = $this->str.','.$value;
            if(!parent::equals()){
                self::error_report($this->fieldName, 'e_equals', sprintf("%s %s", $this->title,R::sysmsg('e_equals')));
            }
        }
    return $this;
    }

    # 이메일 데이터인지 체크
    public function email () : FormValidation
    {
        if($this->str && !filter_var($this->str, FILTER_VALIDATE_EMAIL)){
            self::error_report($this->fieldName, 'e_formality', sprintf("%s %s", $this->title,R::sysmsg('e_formality')));
        }
    return $this;
    }

    # http:: url 데이터인지 체크
    public function url () : FormValidation
    {
        if($this->str && !filter_var($this->str, FILTER_VALIDATE_URL)){
            self::error_report($this->fieldName, 'e_link_url', sprintf("%s %s", $this->title,R::sysmsg('e_link_url')));
        }
    return $this;
    }

    # 소수형 데이터 인지 체크
    public function floatf () : FormValidation
    {
        if($this->str && !is_float(floatval($this->str))){
            self::error_report($this->fieldName, 'e_float', sprintf("%s %s", $this->title,R::sysmsg('e_float')));
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
