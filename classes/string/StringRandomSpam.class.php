<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://apmsoft.tistory.com
| @Editor   : Eclipse(default)
| @vsesion  : 1.0
----------------------------------------------------------*/
namespace Flex\String;

class StringRandomSpam extends StringRandom
{
    /**
     * @var filter_spam_str     : 사용할 랜덤 영문숫자 조합 문자
     * @var input_mix_str       : 사용자에게 보이는 출판전 문자
     * @var input_spam_str      : 사용자가 입력해야할 문자
     * @var spam_str_cnt        : 스팸문자에 사용할 문자최대길이
     */
    private $filter_spam_str = '';
    private $input_mix_str   = '';
    private $input_spam_str  = '';
    private $spam_str_cnt    =10;

    # html span style
    private $html_spantag_cssstyle = '';

    #@ void
    public function __construct(){
        $this->filter_spam_str = parent::arrayRand($this->spam_str_cnt);
        self::makeInputSpamMixString();
        self::setCssStyle(array('font-size:16pt', 'font-weight:bold', 'color:red'));
    }

    # void
    #출판전 스팸문자 조합만들기
    private function makeInputSpamMixString()
    {
        # 실입력 문자 위치 만들기
        $spam_argv = array();
        $length = parent::numberRand(3,6);
        $spam_pos_str = parent::arrayIntRand($length);
        $spam_pos_str_cnt = strlen($spam_pos_str);
        for($pos=0; $pos<$spam_pos_str_cnt; $pos++){
            $pos_num = substr($spam_pos_str,$pos,1);
            $spam_argv[$pos_num] = 1;
        }

        # 출력용 스팸 문자 만들기
        for($i=0; $i<$this->spam_str_cnt; $i++)
        {
            $spam_str = substr($this->filter_spam_str,$i,1);
            if(isset($spam_argv[$i])){
                $this->input_mix_str.= '<span style="{{css_style}}">'.$spam_str.'</span>';
                $this->input_spam_str.=$spam_str;
            }else{
                $this->input_mix_str.= $spam_str;
            }
        }
    }

    # void
    # tags  : array ('font-size:16pt', 'font-weight:bold', 'color:red');
    # 사용자가 입력을 해야할 문자에 하이라이트 및 눈에 띄도록 하기 윈한 HTML 태그삽입
    public function setCssStyle($css_style){
        if(is_array($css_style)){
            $this->html_spantag_cssstyle = implode(';', $css_style);
        }
    }

    # String
    # 사용자가 입력해야할 문자
    public function getInputSpamStr(){
        return $this->input_spam_str;
    }

    # String
    # 최종문자 만들기
    public function doPrint(){
        return str_replace('{{css_style}}', $this->html_spantag_cssstyle, $this->input_mix_str);
    }
}
?>