<?php
namespace Flex\Auth;

use Flex\Log\Log;

# _AUTH_MODE_
final class AuthSession
{
    # 웹용 세션 항목
    private $auth_args = [];

    # 로그인 체크 값
    private $authinfo = [];

    # run
    public function __construct(?Array $args){
        if (!is_null($args)){
            if(is_array($args) && count($args)>0){
                $this->auth_args = $args;
            }
        }
    }

    # 세션 키와 값 생성
    public function __set(string $k, mixed $v) : void{
        $this->authinfo[$k] = $v;
        if(!isset($_SESSION[$k]) && $v){
            $_SESSION[$k] = $v;
        }
    }

    # 세션 키값
    public function __get(string $k){
        if(array_key_exists($k, $this->authinfo))
            return $this->authinfo[$k];
    }

    # 세션생성된 전체 값
    public function fetch(): array 
    {
        return $this->authinfo;
    }

    # 세션등록
    public function regiAuth(array $data_args) : void
    {
        if(is_array($data_args))
        {
            @session_start();
            foreach($this->auth_args as $k => $session_key){
                if(isset($data_args[$k]) && $data_args[$k]!=''){
                    #Log::d($session_key, $data_args[$k]);
                    $_SESSION[$session_key] = $data_args[$k];
                }
            }
        }
    }

    # 세션스타트 및 배열에 담기
    public function sessionStart() : void
    {
        if(is_array($_SESSION)){
            foreach($this->auth_args as $k => $session_key){
                if(isset($_SESSION[$session_key])){
                    $this->authinfo[$session_key] = $_SESSION[$session_key];
                }
            }
        }
    }

    #void
    # 세션비우기
    public function unregiAuth() : void{
        foreach($this->auth_args as $k=>$v)
        {
            $this->authinfo = [];
            if(isset($_SESSION[$v])){
                unset($_SESSION[$v]);
            }
        }
    }
}
?>
