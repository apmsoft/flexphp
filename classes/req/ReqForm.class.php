<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://apmsoft.tistory.com
| @Editor   : VSCode
| @UPDATE   : 1.3
----------------------------------------------------------*/
namespace Fus3\Req;

use Fus3\R\R;
use Fus3\Out\Out;

# 폼체크
class ReqForm
{
	public function __construct(){
	}

	# 널값만 체크
	public function chkNull($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()) {
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		    }
        }
	}

    # 아이디체크
    public function chkUserid($field,$title,$value,$required){
        $isChceker = new ReqStrChecker($value);
        if($required){
            if($isChceker->isNull())
                $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
        }

        if($value){
            if(!$isChceker->isSpace())
                $this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
            if(!$isChceker->isStringLength(4,16))
                $this->error_report($field, 'e_userid_length', $title.' '.R::$sysmsg['e_userid_length']);
            if($isChceker->isKorean())
                $this->error_report($field, 'e_korean', $title.' '.R::$sysmsg['e_korean']);
            if(!$isChceker->isEtcString(''))
                $this->error_report($field, 'e_symbol', $title.' '.R::$sysmsg['e_symbol']);
        }
    }

	# 비밀번호
	public function chkPasswd($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull())
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if(!$isChceker->isStringLength(4,160))
				$this->error_report($field, 'e_password_length', $title.' '.R::$sysmsg['e_password_length']);
			if($isChceker->isKorean())
				$this->error_report($field, 'e_korean', $title.' '.R::$sysmsg['e_korean']);
			// if(!$isChceker->isEtcString(''))
			// 	$this->error_report($field, 'e_symbol', $title.' '.R::$sysmsg['e_symbol']);
		}
	}

	# 비밀번호 보안강화(최소8자 및 특수문자 한글자 포함)
	public function chkPasswdSecure($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull())
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if(!$isChceker->isStringLength(8,160))
				$this->error_report($field, 'e_password_secure_length', $title.' '.R::$sysmsg['e_password_secure_length']);
			if($isChceker->isKorean())
				$this->error_report($field, 'e_korean', $title.' '.R::$sysmsg['e_korean']);
			if($isChceker->isEtcString('>,<,?,/,|,`,\',"'))
				$this->error_report($field, 'e_not_found_symbol', $title.' '.R::$sysmsg['e_not_found_symbol']);
		}
	}

	# 이름
	public function chkName($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if(!$isChceker->isEtcString(''))
				$this->error_report($field, 'e_symbol', $title.' '.R::$sysmsg['e_symbol']);
		}
	}

	# 전화번호
	public function chkPhone($field,$title,$value,$required){
		$isChceker = new ReqStrChecker(str_replace('-','',$value));
		if($required){
			if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field,'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if(!$isChceker->isNumber())
				$this->error_report($field, 'e_phone_symbol', $title.' '.R::$sysmsg['e_phone_symbol']);

			//if(!$isChceker->isSameRepeatString(5))
			//	$this->error_report($field, 'e_number_repeat', $title.' '.R::$sysmsg['e_number_repeat']);
			//if(!$isChceker->isEtcString('-'))
				//$this->error_report($field, 'e_phone_symbol', $title.' '.R::$sysmsg['e_phone_symbol']);
		}
	}

	# 숫자만
	public function chkNumber($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if(!$isChceker->isNumber())
				$this->error_report($field, 'e_number', $title.' '.R::$sysmsg['e_number']);
		}
	}

	# 더블
	public function chkFloat($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if(!is_float(floatval($value)))
				$this->error_report($field, 'e_float', $title.' '.R::$sysmsg['e_float']);
		}
	}

	# 영문만
	public function chkAlphabet($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!$isChceker->isAlphabet())
				$this->error_report($field, 'e_alphabet', $title.' '.R::$sysmsg['e_alphabet']);
		}
	}

	# 이메일 sed_-23@apmsoftax.com
	public function chkEmail($field,$title,$value,$required){
		$value =filter_var($value,FILTER_SANITIZE_EMAIL);
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()){
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
			}
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if($isChceker->isKorean())
				$this->error_report($field, 'e_korean', $title.' '.R::$sysmsg['e_korean']);
			if(!filter_var($value, FILTER_VALIDATE_EMAIL))
				$this->error_report($field, 'e_formality', $title.' '.R::$sysmsg['e_formality']);
			if(!$isChceker->isEtcString('@,-,_'))
				$this->error_report($field, 'e_email_symbol', $title.' '.R::$sysmsg['e_email_symbol']);
		}
	}

	# 일반 영어/숫자/언더라인 만 허용
	public function chkEngNumUnderline($field,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull()){
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
			}
		}

		if($value){
			if(!$isChceker->isSpace())
				$this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
			if($isChceker->isKorean())
				$this->error_report($field, 'e_korean', $title.' '.R::$sysmsg['e_korean']);
			if(!$isChceker->isEtcString('_'))
				$this->error_report($field, 'e_email_symbol', $title.' '.R::$sysmsg['e_email_symbol']);
		}
	}

	# 링크주소
	public function chkLinkurl($field,$title,$value,$required){
		$value =filter_var($value,FILTER_SANITIZE_URL);
		$isChceker = new ReqStrChecker($value);
		if($required){
			if($isChceker->isNull())
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($value){
			if(!filter_var($value, FILTER_VALIDATE_URL))
				$this->error_report($field, 'e_formality', $title.' '.R::$sysmsg['e_formality']);
		}
	}

    # 날짜
    public function chkDateFormat($field,$title,$value,$required){
        $isChceker = new ReqStrChecker($value);
        if($required){
            if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
        }

        if($value){
            if(!$isChceker->isSpace())
                $this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
            // if($isChceker->isKorean())
            //     $this->error_report($field, 'e_korean', $title.' '.R::$sysmsg['e_korean']);
            // if(!$isChceker->isEtcString('-,:'))
            //     $this->error_report($field, 'e_date_symbol', $title.' '.R::$sysmsg['e_date_symbol']);
            if(!$isChceker->chkDate())
                $this->error_report($field,'e_date_symbol', $title.' '.R::$sysmsg['e_date_symbol']);
        }
	}
	
	# 시간
	public function chkTimeFormat($filed,$title,$value,$required){
		$isChceker = new ReqStrChecker($value);
        if($required){
            if($isChceker->isNull()) $this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
        }

        if($value){
            if(!$isChceker->isSpace())
                $this->error_report($field, 'e_spaces', $title.' '.R::$sysmsg['e_spaces']);
            if(!$isChceker->chkTime())
                $this->error_report($field,'e_time_symbol', $title.' '.R::$sysmsg['e_time_symbol']);
        }
	}

    #@ void
    # 기간체크
    # $field_args = array('sdate','edate')
    # $value_args= array($_REQUEST['sdate'],$_REQUEST'edate'])
    # $required = true | false
    public function chkDatePeriod($field_args,$title_args,$value_args,$required){
        // 배열인지 체크
        if(!is_array($field_args) || !is_array($value_args)){
            $this->error_report($field, 'e_date_period_array',R::$sysmsg['e_date_period_array']);
        }

        if($required)
        {
            // 데이터 형식 체크
            foreach($field_args as $index => $field){
                self::chkDateFormat($field_args[$index],$title_args[$index],$value_args[$index],$required);
            }

            // 기간체크
            $isChceker = new ReqStrChecker(implode('/', $value_args));
            if(!$isChceker->chkDatePeriod()){
                $this->error_report($field_args[0], 'e_date_period',$title_args[0].' '.R::$sysmsg['e_date_period']);
            }
		}
    }

    #@void
    #$argsv = array($req->v, 비교값);
    public function chkEquals($field,$title,$argsv,$required){
		$isChceker = new ReqStrChecker($argsv[0]);
		if($required){
			if($isChceker->isNull())
				$this->error_report($field, 'e_null', $title.' '.R::$sysmsg['e_null']);
		}

		if($argsv[0]){
            if(!$isChceker->equals($argsv[1]))
                $this->error_report($field, 'e_isnot_match', $title.' '.R::$sysmsg['e_isnot_match']);
        }
    }

	public function error_report($field, $msg_code, $msg){
		Out::prints_json(array('result'	=>'false','fieldname'=>$field,'msg_code'=>$msg_code,'msg'=>$msg));
	}
}
?>
