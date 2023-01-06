<?php
namespace Flex\Annona\Request;

use Flex\Log\Log;

# _POST, _GET, 값들을 제어 및 기본작업 수행
class Request
{
	private array $params = [];
	private array $headers = [];

	public function __construct(){ 
    return $this; 
    }

	#@ void
	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function post(bool $is_trim = true) : Request{
		if (count($_POST)>0) { 
			self::trimParams($_POST,$is_trim);
		}
		else if ($post_data = file_get_contents('php://input')) {
			if ($post_json = json_decode($post_data, TRUE)) {
				self::trimParams($post_json,$is_trim);
			}else{
				parse_str($post_data, $post_variables);
				if (count($post_variables)>0){
					self::trimParams($post_variables,$is_trim);
				}
			}
		}
    return $this;
	}

	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function get(bool $is_trim = true) : Request{
		self::trimParams($_GET,$is_trim);
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

	public function getHeaders() : array
	{
		if (function_exists('getallheaders')) {
			$this->headers = getallheaders();
		} else if (!function_exists('apache_request_headers')){
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
			self::getHeaders();
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

	public function __get($key){
		if(isset($this->params[$key])){
			return $this->params[$key];
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
