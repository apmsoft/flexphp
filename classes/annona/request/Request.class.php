<?php
namespace Flex\Annona\Request;

use Flex\Annona\Log;

# _POST, _GET, 값들을 제어 및 기본작업 수행
class Request
{
	private $version = '1.0.3';
	private array $params   = [];
	private array $headers  = [];
	public string $ip       = '';
	public string $uri_path = '';
	public string $method   = '';
	public string $port     = '';

	public function __construct(){
		$this->ip       = $this->get_client_ip();
		$this->uri_path = (isset($_SERVER['REQUEST_URI'])) ? (parse_url($_SERVER['REQUEST_URI']))['path'] : '';
		$this->method   = (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : '';
		$this->port     = (isset($_SERVER['SERVER_PORT'])) ? $_SERVER['SERVER_PORT'] : '';
    }

	#@ void
	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function post(bool $is_trim = true) : Request{
		if(count($_POST)){
			$this->trimParams($_POST,$is_trim);
		}else{
			$this->getInputContents($is_trim);
		}
    return $this;
	}

	#@ void
	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function input(bool $is_trim = true) : Request{
		$this->getInputContents($is_trim);
    return $this;
	}

	public function patch(bool $is_trim = true) : Request{
		$this->getInputContents($is_trim);
    return $this;
	}

	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function get(bool $is_trim = true) : Request{
		$this->trimParams($_GET,$is_trim);
    return $this;
	}

	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function delete(bool $is_trim = true) : Request{
		$this->trimParams($_GET,$is_trim);
    return $this;
	}

	# trim 함수로 앞뒤공백 비우기
	public function trimParams(array $arg, bool $is_trim) : void{
		if(is_array($arg)){
			foreach($arg as $k=>$v){
				if($is_trim){
					if(!is_array($v)){
						$v= trim($v);
					}
				}
				$this->params[$k]=$v;
			}
		}
	}

	private function getInputContents(bool $is_trim) : void 
	{
		if ($post_data = file_get_contents('php://input')) {
			if ($post_json = json_decode($post_data, TRUE)) {
				$this->trimParams($post_json,$is_trim);
			}else{
				parse_str($post_data, $post_variables);
				if (count($post_variables)>0){
					$this->trimParams($post_variables,$is_trim);
				}
			}
		}
	}

	public function getHeaders() : array
	{
		if (function_exists('getallheaders')) {
			$this->headers = getallheaders();
		} else if (function_exists('apache_request_headers')){
			$this->headers = apache_request_headers();
		} else {
			$headers = [];
			foreach ($_SERVER as $name => $value) {
				if (strtolower(substr($name, 0, 5)) == 'http_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}

			$this->headers = $headers;
		}

	return $this->headers;
	}

	public function getHeaderLine(string $name) : string 
	{
		$header_val = '';
		if(!count($this->headers)){
			$this->getHeaders();
		}

		foreach($this->headers as $k =>$v){
			if($name == $k){
				$header_val = strtr($v,["\""=>'','\\'=>'']);
				break;
			}
		}
	return $header_val;
	}

    public function fetch() : array{
		return $this->params;
    }

	private static function get_client_ip() : string
    {
        $result = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) $result = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $result = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED'])) $result = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) $result = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED'])) $result = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR'])) $result = $_SERVER['REMOTE_ADDR'];

    return $result;
    }

	public function __get($propertyName){
		if(isset($this->params[$propertyName])){
			return $this->params[$propertyName];
		}
	}

	public function __set($key, $value){
		$this->params[$key] = $value;
	}

	public function __isset($name){
		return isset($this->params[$name]);
	}
}
?>
