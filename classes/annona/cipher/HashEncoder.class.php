<?php
namespace Flex\Annona\Cipher;

use Flex\Annona\Cipher\Base64UrlEncoder;
use Exception;

class HashEncoder extends Base64UrlEncoder
{
    public const __version = '1.1';
    private string $encrypt_str;

    public function __construct(string $encrypt_str)
    {
        parent::__construct($encrypt_str);
        $this->encrypt_str = $encrypt_str;
    }

    public function hash(string $algorithm = 'sha256'): string
    {
        if (!in_array($algorithm, ['sha256', 'sha512'])) {
            throw new Exception("Unsupported hash algorithm", __LINE__);
        }
        $result = hash($algorithm, $this->encrypt_str);
        if ($result === false) {
            throw new Exception("Hash generation failed", __LINE__);
        }
        return $result;
    }

    public function hashBase64(string $algorithm = 'sha256'): string
    {
        $hashed = $this->hash($algorithm);
        return base64_encode($hashed);
    }

    public function hashBase64Url(string $algorithm = 'sha256'): string
    {
        $hashed = $this->hash($algorithm);
        return $this->encode($hashed);
    }

    public function base64UrlEncode(): string
    {
        return $this->encode($this->encrypt_str);
    }

    public function base64UrlDecode(string $encodedString): string
    {
        return $this->decode($encodedString);
    }
}
?>