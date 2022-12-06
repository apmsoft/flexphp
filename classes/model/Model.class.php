<?php
namespace Flex\Model;

# purpose : php 코딩시 환경설정에 대한 값들을 관리하기 위함
class Model{
    private $args = [];

    public function __construct(array $args=[]){
        if(count($args)){
            $this->args = $args;
        }
    }

    public function fetch() : array{
        return $this->args;
    }

    public function length() : int{
        return (is_array($this->args)) ? count($this->args) : 0;
    }

    public function __set($propertyName, $value){
        # 키가 없을 경우
        if(!array_key_exists($propertyName, $this->args)){
            $this->args[$propertyName] = $value;
        }else{
            # 기존 값이 배열 일때
            if(is_array($this->args[$propertyName]))
            {
                if(is_array($value))
                {
                    if(count($value)){
                        $pre_array = $this->args[$propertyName];
                        $this->args[$propertyName] = [];
                        $this->args[$propertyName] = $pre_array;
                        $this->args[$propertyName][] = $value;
                    }
                }else{
                    $this->args[$propertyName] = array_merge($this->args[$propertyName], (array)$value);
                }
            }else{
                $this->args[$propertyName] = $value;
            }
        }
    }

    public function __get($propertyName){
        $result = null;
        if(array_key_exists($propertyName, $this->args)){
            $result = $this->args[$propertyName];
        }
    return $result;
    }

    public function __isset($name){
        return isset($this->args[$name]);
    }

    public function __unset($name){
        unset($this->args[$name]);
    }

    public function __destruct(){
        unset($this->args);
    }
}
?>