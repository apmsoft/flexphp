<?php
namespace Flex\Annona\Cipher;

use Exception;

class Base64UrlEncoder
{
    public const __version = '1.0';
    protected string $data;

    public function __construct(string $data = '')
    {
        $this->data = $data;
    }

    /**
     * 데이터를 Base64Url로 인코딩
     *
     * @param string|null $data 인코딩할 데이터 (null이면 내부 데이터 사용)
     * @return string Base64Url 인코딩된 문자열
     * @throws Exception 인코딩 실패 시
     */
    public function encode(?string $data = null): string
    {
        $input = $data ?? $this->data;
        $base64 = base64_encode($input);
        if ($base64 === false) {
            throw new Exception("Base64 encoding failed", __LINE__);
        }
        return $this->urlEncode($base64);
    }

    /**
     * Base64Url 인코딩된 문자열을 디코딩
     *
     * @param string|null $data 디코딩할 Base64Url 문자열 (null이면 내부 데이터 사용)
     * @return string 디코딩된 원본 데이터
     * @throws Exception 디코딩 실패 시
     */
    public function decode(?string $data = null): string
    {
        $input = $data ?? $this->data;
        $base64 = $this->urlDecode($input);
        $decoded = base64_decode($base64, true);
        if ($decoded === false) {
            throw new Exception("Base64 decoding failed", __LINE__);
        }
        return $decoded;
    }

    /**
     * Base64 문자열을 URL 안전 형식으로 변환
     *
     * @param string $input Base64 문자열
     * @return string URL 안전 Base64 문자열
     */
    protected function urlEncode(string $input): string
    {
        return rtrim(strtr($input, '+/', '-_'), '=');
    }

    /**
     * URL 안전 Base64 문자열을 표준 Base64 형식으로 변환
     *
     * @param string $input URL 안전 Base64 문자열
     * @return string 표준 Base64 문자열
     */
    protected function urlDecode(string $input): string
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        return strtr($input, '-_', '+/');
    }

    /**
     * 내부 데이터를 설정
     *
     * @param string $data 설정할 데이터
     */
    public function setData(string $data): void
    {
        $this->data = $data;
    }

    /**
     * 내부 데이터를 반환
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }
}
?>