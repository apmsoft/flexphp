<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : apmsoft.tistory.com
| @Editor   : Sublime Text 3
| @UPDATE   : 1.2.1
----------------------------------------------------------*/
namespace Flex\Auth;

# _AUTH_MODE_
class AuthSession
{
    private $authType = ''; # 관리자, 웹 세션 활용 모드

    # 웹용 세션 항목
    private $auth_args = array();

    # 로그인 체크 값
    private $authinfo = array();

    # run
    public function __construct($args=array()){
        if(is_array($args) && count($args)>0){
            $this->auth_args = $args;
        }
    }

    # void
    public function __set($k, $v){
        $this->authinfo[$k] = $v;
    }

    # return data
    public function __get($k){
        if(array_key_exists($k, $this->authinfo))
            return $this->authinfo[$k];
    }

    #@ void
    # 세션스타트 및 배열에 담기
    public function sessionStart()
    {
        if(is_array($_SESSION)){
            foreach($this->auth_args as $k=>$v){
                //echo $k.' '.$v."\n";
                if(isset($_SESSION[$v])){
                    $this->authinfo[$k]=$_SESSION[$v];
                }
            }
        }
    }

    #@ void
    # 세션등록
    public function regiAuth($data_args)
    {
        if(is_array($data_args)){
            @session_start();
            foreach($this->auth_args as $k=>$v){
                if(isset($data_args[$v]) && $data_args[$v]!=''){
                    //session_register($v);
                    // echo '-----'.$v.' '.$data_args[$v];
                    $_SESSION[$v]=$data_args[$v];
                }
            }
        }
    }

    #void
    # 세션비우기
    public function unregiAuth(){
        foreach($this->auth_args as $k=>$v){
            if(isset($_SESSION[$v])){
                unset($_SESSION[$v]);
            }
        }
    }
}
?>
