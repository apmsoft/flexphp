<?php
namespace Flex\Util;

use Psr\Http\Message\ServerRequestInterface;

# reactphp ServerRequestInterface 용 확장 클래스
class Requested
{
    public const __version = '1.0';
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

	public function __call($name, $arguments)
    {
        return call_user_func_array([$this->request, $name], $arguments);
    }

    public function fetch() : array{
		return $this->params;
    }

	public function __get($propertyName) : mixed
	{
		echo '>>>'.$propertyName.PHP_EOL;
		if(isset($this->params[$propertyName])){
			return $this->params[$propertyName];
		}
		return null;
	}

	public function __set($key, $value){
		$this->params[$key] = $value;
	}

	public function __isset($name) {
		return isset($this->params[$name]);
	}
}
?>
