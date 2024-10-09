<?php
namespace Flex\Annona\Request;

class Request
{
    public const __version = '1.0.4';
    private array $params = [];
    private array $headers = [];
    public string $ip;
    public string $uri_path;
    public string $method;
    public string $port;

    public function __construct()
    {
        $this->ip = $this->getClientIp();
        $this->uri_path = $_SERVER['REQUEST_URI'] ?? '';
        $this->method = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->port = $_SERVER['SERVER_PORT'] ?? '';
    }

    public function post(bool $is_trim = true): self
    {
        if (!empty($_POST)) {
            $this->trimParams($_POST, $is_trim);
        } else {
            $this->getInputContents($is_trim);
        }
        return $this;
    }

    public function input(bool $is_trim = true): self
    {
        $this->getInputContents($is_trim);
        return $this;
    }

    public function patch(bool $is_trim = true): self
    {
        $this->getInputContents($is_trim);
        return $this;
    }

    public function get(bool $is_trim = true): self
    {
        $this->trimParams($_GET, $is_trim);
        return $this;
    }

    public function delete(bool $is_trim = true): self
    {
        $this->trimParams($_GET, $is_trim);
        return $this;
    }

    public function trimParams(array $arg, bool $is_trim): void
    {
        foreach ($arg as $k => $v) {
            if ($is_trim && !is_array($v)) {
                $v = trim($v);
            }
            $this->params[$k] = $v;
        }
    }

    private function getInputContents(bool $is_trim): void
    {
        $post_data = file_get_contents('php://input');
        if ($post_data) {
            $post_json = json_decode($post_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->trimParams($post_json, $is_trim);
            } else {
                parse_str($post_data, $post_variables);
                if (!empty($post_variables)) {
                    $this->trimParams($post_variables, $is_trim);
                }
            }
        }
    }

    public function getHeaders(): array
    {
        if (empty($this->headers)) {
            if (function_exists('getallheaders')) {
                $this->headers = getallheaders();
            } elseif (function_exists('apache_request_headers')) {
                $this->headers = apache_request_headers();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (str_starts_with($name, 'HTTP_')) {
                        $header_name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->headers[$header_name] = $value;
                    }
                }
            }
        }
        return $this->headers;
    }

    public function getHeaderLine(string $name): string
    {
        return $this->headers[$name] ?? '';
    }

    public function fetch(): array
    {
        return $this->params;
    }

    private static function getClientIp(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? 
			$_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
			$_SERVER['HTTP_X_FORWARDED'] ?? 
			$_SERVER['HTTP_FORWARDED_FOR'] ?? 
			$_SERVER['HTTP_FORWARDED'] ?? 
			$_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function __get(string $propertyName): mixed
    {
        return $this->params[$propertyName] ?? null;
    }

    public function __set(string $key, mixed $value): void
    {
        $this->params[$key] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->params[$name]);
    }
}
?>
