<?php
namespace Flex\Token;

use Flex\R\R;
use \ErrorException;
use Flex\Cipher\CipherEncrypt;
use Flex\Cipher\CipherDecrypt;
use Flex\Log\Log;
use Flex\Req\Req;

final class TokenCheckAtype
{
    const TAG = 'TokenCheckAtype::';
    private $header_access_token = 'Nan';
    private $token_generate_key = 'Nan';

    public function __construct(
        private Req $request
    ){}

    # 토큰 찾기 및 토큰생성키 찾기
    public function __invoke(string $token_key)
    {
        # 접속토큰 체크
        if($this->request->getHeaderLine($token_key)) {
            $this->header_access_token = $this->request->getHeaderLine($token_key);
        }

        # reject
        if( $this->header_access_token == 'Nan' ){
            Log::e('connect access_token : Nan');
            throw new \ErrorException( R::$sysmsg['e_match_token'] );
        }

        # 접속토큰 디코딩
        $cipherDecrypt        = new \Flex\Cipher\CipherDecrypt($this->header_access_token);
        $decrypt_access_token = $cipherDecrypt->_base64_urldecode();
        $access_token_argv    = (strpos($decrypt_access_token,__TOKEN_CHARACTER__) !==false) ? explode(__TOKEN_CHARACTER__, $decrypt_access_token) : [];

        # 모듈ID 또는 아이디등:토큰 분리
        if(isset($access_token_argv[0])){
            $this->token_generate_key = $access_token_argv[0];
        }

        # reject
        if($this->token_generate_key == 'Nan'){
            Log::e('connect token_generate_key : None');
            throw new \ErrorException( R::$sysmsg['e_match_token'] );
        }

        return $this;
    }

    # 토큰비교
    public function chkAccessToken () : string 
    {
        # 토큰생성
        $generate_string     = implode('',[$this->token_generate_key,_SECRET_KEY_]);
        $tokenGenerateAtype  = new \Flex\Token\TokenGenerateAtype($generate_string);
        $hsahkey             = $tokenGenerateAtype->generateHashKey($tokenGenerateAtype->generate_string);
        $hash_token          = implode(__TOKEN_CHARACTER__,[$this->token_generate_key,$hashkey]);
        $generate_token      = $tokenGenerateAtype->generateToken($hash_token);
        
        // reject :: token
        if($this->header_access_token != $generate_token){
            Log::e('<<< Token No match >>>');
            Log::e('header_token : '.$this->header_access_token);
            Log::e('access_token : '.$generate_token);
            throw new \ErrorException( R::$sysmsg['e_match_token'] );
        }

        return $this->token_generate_key;
    }
}
?>