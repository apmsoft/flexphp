<?php
namespace Flex\Util;

use Psr\Http\Message\ServerRequestInterface;

# reactphp ServerRequestInterface 용 확장 클래스
class Requested
{
    public const __version = '0.7';
	private array $params   = [];

	public function __construct(
        private ServerRequestInterface $request
	){
		$this->params = [];
	}

	public function post() : Requested
	{
		if ($this->request->getHeaderLine('Content-Type') == 'application/json') {
            $this->params = array_merge($this->params,json_decode($this->request->getBody()->getContents(),true) ?? []);
        }else {
            $this->params = array_merge($this->params,$this->request->getParsedBody());
        }
    return $this;
	}

	public function get() : Requested
	{
		$this->params = $this->request->getQueryParams();
    return $this;
	}

	public function getHeaderLine( string $headline_key) : string
	{
		$result = '';

		if($this->request->getHeaderLine($headline_key)) {
            $result = $this->request->getHeaderLine($headline_key);
        }

		return $result;
	}

	public function getServerParams() : array{
		return $this->request->getServerParams();
	}

	public function getUriPath() : string
	{
		return $this->request->getUri()->getPath();
	}

	public function getMethod(): string
	{
		return $this->request->getMethod();
	}

    public function fetch() : array{
		return $this->params;
    }

	public function getUploadedFiles() : array{
		return $this->request->getUploadedFiles();
	}

	public function __get($propertyName) : mixed
	{
		echo '>>>'.$propertyName.PHP_EOL;
		if(isset($this->params[$propertyName])){
			return $this->params[$propertyName];
		}
	}

	public function __set($key, $value){
		$this->params[$key] = $value;
	}

	public function __isset($name) {
		return isset($this->params[$name]);
	}
}
?>
