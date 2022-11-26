<?php
namespace Flex\Util;

# purpose : php 코딩시 환경설정에 대한 값들을 관리하기 위함
class UtilModel{
  private $arg;

  #@ void
  public function __construct($properties=array()){
    $this->arg = (is_array($properties)) ? $properties : array();
  }
  
  #@ return array
  public function fetch(){
    return $this->arg;
  }

  #@ int
  # 배열갯수
  public function length(){
    return (is_array($this->arg)) ? count($this->arg) : 0;
  }

  #@ void
  public function __set($propertyName, $value){
    # 키가 없을 경우
    if(!array_key_exists($propertyName, $this->arg)){
      $this->arg[$propertyName] = $value;
    }else{
      # 기존 값이 배열 일때
      if(is_array($this->arg[$propertyName])){
        if(is_array($value)){
          if(count($this->arg[$propertyName]) && !isset($this->arg[$propertyName][0])){
            $pre_array = $this->arg[$propertyName];
            $this->arg[$propertyName] = array();
            $this->arg[$propertyName][] = $pre_array;
            $this->arg[$propertyName][] = $value;
          }
          else if(count($this->arg[$propertyName])>1){
            $this->arg[$propertyName][] = $value;
          }else{
            $this->arg[$propertyName] = $value;
          }
        }
        else{
          $this->arg[$propertyName] = array_merge($this->arg[$propertyName], (array)$value);
        }
      }else{
        $this->arg[$propertyName] = $value;
      }
    }
  }

  #@ return string || array
  public function __get($propertyName){
    $result = null;
    if(array_key_exists($propertyName, $this->arg)){
      $result = $this->arg[$propertyName];
    }
  return $result;
  }

  #@ boolean
  public function __isset($name){
    return isset($this->arg[$name]);
  }

  #@ void
  public function __unset($name){
    unset($this->arg[$name]);
  }

  #@ void
  public function __destruct(){
    unset($this->arg);
  }
}
?>
