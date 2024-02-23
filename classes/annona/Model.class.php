<?php
namespace Flex\Annona;

# purpose : php 코딩시 환경설정에 대한 값들을 관리하기 위함
class Model{
    private $version = '1.0.3';
    private $args = [];

    public function __construct(?array $args=[]){
        if(is_array($args) && count($args)){
            $this->args = $args;
        }
    }

    public function fetch() : array{
        return $this->args;
    }

    public function length() : int{
        return (is_array($this->args)) ? count($this->args) : 0;
    }

    public function __set(string $propertyName, $value)
    {
        # 키가 있을 경우
        if(array_key_exists($propertyName, $this->args)){
            $this->args[$propertyName] = $value;
        }else{
            # + : 배열 index 하나씩 증가
            if(substr($propertyName,-1) == '+')
            {
                $re_propertyname = strtr($propertyName,['+'=>'']);

                # 값 선언이 배열이고 입력값이 string 일때
                if(is_array($this->args[$re_propertyname])){
                    $pre_array = $this->args[$re_propertyname];
                    $this->args[$re_propertyname] = [];
                    $this->args[$re_propertyname] = $pre_array;
                }else{
                    $this->args[$re_propertyname] = [];
                }
                $this->args[$re_propertyname][] = $value;
            }
            # - : 배열 뒤 하나씩 빼기
            else if(substr($propertyName,-1) == '-')
            {
                $re_propertyname = strtr($propertyName,['-'=>'']);

                # 값 선언이 배열이고 입력값이 string 일때
                if(is_array($this->args[$re_propertyname])){
                    array_pop($this->args[$re_propertyname]);
                }
            }
            # -{}{} : 배열 자르기
            else if(strpos($propertyName,'-{') !==false){
                preg_match_all("/({+)(.*?)(})/", $propertyName, $matches);
                $sno = strpos($propertyName,'-');
                $re_propertyname = substr($propertyName,0,$sno);

                # 값 선언이 배열이고 입력값이 string 일때
                if(is_array($this->args[$re_propertyname])){
                    $pre_array = $this->args[$re_propertyname];
                    if(count($matches[2]) == 2){
                        $this->args[$re_propertyname] = array_slice($pre_array, $matches[2][0],$matches[2][1]);
                    }else{
                        $this->args[$re_propertyname] = array_slice($pre_array, $matches[2][0]);
                    }
                }
            }
            # {} 배열 값 업데이트
            else if(strpos($propertyName,'{') !==false){
                preg_match_all("/({+)(.*?)(})/", $propertyName, $matches);
                $sno = strpos($propertyName,'{');
                $re_propertyname = substr($propertyName,0,$sno);
                if(count($matches[2]) ==3) $this->args[$re_propertyname][$matches[2][0]][$matches[2][1]][$matches[2][2]] = $value;
                if(count($matches[2]) ==2) $this->args[$re_propertyname][$matches[2][0]][$matches[2][1]] = $value;
                else $this->args[$re_propertyname][$matches[2][0]] = $value;
            }
            # 일반 키 => 값
            else{
                $this->args[$propertyName] = $value;
            }
        }
    }

    public function __get(string $propertyName){
        $result = null;
        if(array_key_exists($propertyName, $this->args)){
            $result = $this->args[$propertyName];
        }
    return $result;
    }

    public function __isset(string $name){
        return isset($this->args[$name]);
    }

    public function __unset(string $name){
        unset($this->args[$name]);
    }

    public function __destruct(){
        unset($this->args);
    }
}
?>