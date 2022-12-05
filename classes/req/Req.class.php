<?php
namespace Flex\Req;

# _POST, _GET, _REQUEST 값들을 제어 및 기본작업 수행
class Req
{
	private $params = [];

	#@ void
	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function usePOST($is_trim = true){
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
	}

	#@ void
	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function useGET(bool $is_trim = true) : void{
		self::trimParams($_GET,$is_trim);
	}

	#@ void
	#@param boolean $is_trim [trim 앞뒤공백 비우기 함수 활성화]
	public function useREQUEST(bool $is_trim = true) : void{
		self::trimParams($_REQUEST,$is_trim);
	}

	#@ void
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

	#@ return 
	#내장 함수 들을 사용할 수 있는 매직함수
	public function __call($func, $arguments){
        if(function_exists($func)){
            return call_user_func_array($func,$arguments);
        }
    }

    #@ array
    public function fetch() : array{
		return $this->params;
    }

	#@ String
	public function __get($key){
		if(isset($this->params[$key])){
			return $this->params[$key];
		}
	}

	#@ void
	public function __set($key, $value){
		$this->params[$key] = $value;
	}

	#@ boolean
	public function __isset($name){
		return isset($this->params[$name]);
	}
}
?>
