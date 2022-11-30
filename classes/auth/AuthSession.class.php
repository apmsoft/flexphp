<?php
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
    public function __construct(?Array $args){
        if (!is_null($args)){
            if(is_array($args) && count($args)>0){
                $this->auth_args = $args;
            }
        }
    }

    public function __set($k, $v) : void{
        $this->authinfo[$k] = $v;
    }

    # return data
    public function __get($k){
        if(array_key_exists($k, $this->authinfo))
            return $this->authinfo[$k];
    }

    #@ void
    # 세션스타트 및 배열에 담기
    public function sessionStart() : void
    {
        if(is_array($_SESSION)){
            foreach($this->auth_args as $k=>$v){
                //echo $k.' '.$v."\n";
                if(isset($_SESSION[$v])){
                    $this->authinfo[$k] = $_SESSION[$v];
                }
            }
        }
    }

    #@ void
    # 세션등록
    public function regiAuth($data_args) : void
    {
        if(is_array($data_args))
        {
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
    public function unregiAuth() : void{
        foreach($this->auth_args as $k=>$v){
            if(isset($_SESSION[$v])){
                unset($_SESSION[$v]);
            }
        }
    }
}
?>
