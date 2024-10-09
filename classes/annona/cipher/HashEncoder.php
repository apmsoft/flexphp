<?php
namespace Flex\Annona\Cipher;

use Exception;

class HashEncoder
{
    public const __version = '1.0';
    private string $encrypt_str;

    public function __construct(string $encrypt_str)
    {
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
}
?>