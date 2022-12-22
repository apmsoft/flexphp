<?php
namespace Flex\Http;

use Flex\Log\Log;

class HttpRequest {
    private $urls = [];
    private $mch;

    # 생성자
    public function __construct($argv){
        if(!is_array($argv)){
            throw new ErrorException(__CLASS__.' :: '.__LINE__.' is not array');
        }
        $this->urls = $argv;
        $this->mch = curl_multi_init();
    }

    /**
     * callback : 콜백함수
     */
    public function get(callable $callback)
    {
        // echo $method.PHP_EOL;
        $response = [];
        foreach($this->urls as $idx => $url)
        {
            $connet_url = $url["url"];
            if( is_array($url['params']) && count($url['params']) ){
                $connet_url = sprintf("%s/%s", $url['url'], http_build_query( $url['params']) );
            }
            Log::d($connet_url);

            $ch[$idx] = curl_init($connet_url);
            if(isset($url['headers']) && count($url['headers'])){
                curl_setopt($ch[$idx], CURLOPT_HTTPHEADER, $url['headers']);
            }
            curl_setopt($ch[$idx], CURLOPT_POST, false );
            curl_setopt($ch[$idx], CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch[$idx], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch[$idx], CURLOPT_RETURNTRANSFER, true );
            curl_multi_add_handle($this->mch,$ch[$idx]);
        }
        
        do {
            curl_multi_exec($this->mch, $running);
            curl_multi_select($this->mch);
        } while ($running > 0);

        foreach(array_keys($ch) as $index){
            Log::d(curl_getinfo($ch[$index], CURLINFO_HTTP_CODE), curl_getinfo($ch[$index], CURLINFO_EFFECTIVE_URL));
            $response[$index] = curl_multi_getcontent($ch[$index]);
            curl_multi_remove_handle($this->mch, $ch[$index]);
        }

        if(is_callable($callback)){
            $callback($response);
        }
    }

    public function post(callable $callback)
    {
        // echo $method.PHP_EOL;
        $response = [];
        foreach($this->urls as $idx => $url)
        {
            $ch[$idx] = curl_init($url['url']);
            if(isset($url['headers']) && count($url['headers'])){
                curl_setopt($ch[$idx], CURLOPT_HTTPHEADER, $url['headers']);
            }
            curl_setopt($ch[$idx], CURLOPT_POST, true );
            curl_setopt($ch[$idx], CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch[$idx], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch[$idx], CURLOPT_RETURNTRANSFER, true );
            if(isset($url['params'])){
                curl_setopt($ch[$idx], CURLOPT_POSTFIELDS, http_build_query($url['params']) );
            }
            curl_multi_add_handle($this->mch,$ch[$idx]);
        }
        
        do {
            curl_multi_exec($this->mch, $running);
            curl_multi_select($this->mch);
        } while ($running > 0);

        foreach(array_keys($ch) as $index){
            Log::d(curl_getinfo($ch[$index], CURLINFO_HTTP_CODE), curl_getinfo($ch[$index], CURLINFO_EFFECTIVE_URL));
            $response[$index] = curl_multi_getcontent($ch[$index]);
            curl_multi_remove_handle($this->mch, $ch[$index]);
        }

        if(is_callable($callback)){
            $callback($response);
        }
    }

    # 소멸
    public function __deconstruct(){
        curl_multi_close($this->mch);
    }
}
?>