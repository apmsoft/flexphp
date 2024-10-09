<?php
namespace Flex\Annona\Http;

class HttpRequest {
    public const __version = '1.1.0';
    private $urls = [];
    private $mch;

    # 생성자
    public function __construct(array $argv = []){
        if(!is_array($argv)){
            throw new \Exception(__CLASS__.' :: '.__LINE__.' is not array');
        }
        $this->urls = $argv;
        $this->mch = curl_multi_init();
    }

    public function set(string $url, string $params, array $headers = []) : HttpRequest
    {
        if(trim($url)){
            $this->urls[] = [
                "url"     => $url,
                "params"  => $params,
                "headers" => $headers
            ];
        }
    return $this;
    }

    /**
     * callback : 콜백함수
     */
    public function get(callable $callback)
    {
        $response = [];
        foreach($this->urls as $idx => $url)
        {
            $connet_url = $url["url"];
            if( isset($url['params']) ){
                $connet_url = sprintf("%s?%s", $url['url'], $url['params'] );
            }

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

        foreach(array_keys($ch) as $index)
        {
            if(curl_getinfo($ch[$index], CURLINFO_HTTP_CODE) != 200){
                throw new \Exception(
                    'HTTP status code : '.curl_getinfo($ch[$index], CURLINFO_HTTP_CODE).
                    " | URL : ".curl_getinfo($ch[$index], CURLINFO_EFFECTIVE_URL)
                );
            }

            $response[$index] = curl_multi_getcontent($ch[$index]);
            curl_multi_remove_handle($this->mch, $ch[$index]);
        }

        if(is_callable($callback)){
            $callback($response);
        }
    }

    public function post(callable $callback)
    {
        $response = [];
        foreach($this->urls as $idx => $url)
        {
            $ch[$idx] = curl_init($url['url']);

            $headers = $url['headers'] ?? [];

            $params = $url['params'];
            $postFields = [];

            // Content-Type 확인
            $contentType = $this->getContentType($headers);

            // 파라미터 처리 (항상 문자열)
            switch ($contentType) {
                case 'application/json':
                    $postFields = $params; // JSON 문자열 그대로 사용
                    break;
                case 'application/x-www-form-urlencoded':
                    $postFields = $params; // URL 인코딩된 문자열 그대로 사용
                    break;
                case 'multipart/form-data':
                    // multipart/form-data의 경우 파싱 후 파일 처리
                    parse_str($params, $parsedParams);
                    foreach ($parsedParams as $key => $value) 
                    {
                        if (is_string($value) && strpos($value, '@') === 0 && file_exists(substr($value, 1))) {
                            $filePath = substr($value, 1);

                            // MIME 타입 얻기
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $filePath);
                            finfo_close($finfo);

                            // 파일 이름 얻기
                            $fileName = basename($filePath);

                            $postFields[$key] = new \CURLFile($filePath, $mimeType, $fileName);
                        } else {
                            $postFields[$key] = $value;
                        }
                    }
                    break;
                default:
                    $postFields = $params;
            }

            curl_setopt($ch[$idx], CURLOPT_POST, true);
            curl_setopt($ch[$idx], CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch[$idx], CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch[$idx], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch[$idx], CURLOPT_RETURNTRANSFER, true);
            $headers = array_filter($headers, function($header) {
                return stripos($header, 'Content-Type:') !== 0;
            });
            curl_setopt($ch[$idx], CURLOPT_HTTPHEADER, $headers);

            curl_multi_add_handle($this->mch, $ch[$idx]);
        }

        do {
            curl_multi_exec($this->mch, $running);
            curl_multi_select($this->mch);
        } while ($running > 0);

        foreach(array_keys($ch) as $index)
        {
            $httpCode = curl_getinfo($ch[$index], CURLINFO_HTTP_CODE);
            if($httpCode != 200){
                throw new \Exception(
                    'HTTP status code : ' . $httpCode .
                    " | URL : " . curl_getinfo($ch[$index], CURLINFO_EFFECTIVE_URL)
                );
            }

            $response[$index] = curl_multi_getcontent($ch[$index]);
            curl_multi_remove_handle($this->mch, $ch[$index]);
        }

        if(is_callable($callback)){
            $callback($response);
        }
    }

    private function getContentType($headers): string|null
    {
        foreach ($headers as $header) {
            if (stripos($header, 'Content-Type:') === 0) {
                list(, $contentType) = explode(':', $header, 2);
                return trim($contentType);
            }
        }
        return null;
    }

    # 소멸
    public function __deconstruct(){
        curl_multi_close($this->mch);
    }
}
?>