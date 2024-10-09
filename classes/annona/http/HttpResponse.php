<?php
namespace Flex\Annona\Http;

final class HttpResponse {
    public const __version = '0.5';

    public function __construct(
        private int $code,
        private array $headers,
        private mixed $message
    ){}

    public function __invoke(): mixed
    {
        // HEADERS
        foreach ($this->headers as $header_key => $header_value) {
            header(sprintf("%s:%s",$header_key,$header_value));
        }

        // HTTP 상태 코드 설정
        http_response_code($this->code);

        return $this->message;
    }

    public function __toString(): string
    {
        return (string)$this();
    }
}

?>