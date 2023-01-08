<?php
namespace Flex\Annona\Array;

use \Flex\Annona\Log;

# 배열 사용에 도움을 주는 클래스
class ArrayHelper
{
    private $version = '0.6';
    public function __construct(
        private array $value
    ){return $this;}

    # 멀티배열 키의 값으로 소팅 [{},{}]
    # sort : asc | desc
    # key : 소팅 비교할 키네임
    public function sorting(string $key, string $sorting = 'ASC') : ArrayHelper 
    {
        $sorting = strtoupper($sorting);
        usort($this->value, function($a, $b) use ($key,$sorting) {
            return match($sorting){
                'DESC' => self::desc($a[$key],$b[$key]),
                'ASC'  => self::asc($a[$key],$b[$key])
            };
        });
    return $this;
    }

    # 멀티배열 중 원하는 값의 첫번째 키를 찾아낸다
    public function find(string $key, mixed $val) : ArrayHelper
    {
        $index = self::findIndex($key, $val);
        $this->value = $this->value[$index];
    return $this;
    }

    # 멀티배열 중 원하는 값의 전체를 찾아 낸다
    public function findAll(string $key,...$params) : ArrayHelper
    {
        $values = $params;
        
        # 배열로 들어왔는지 체크
        if(is_array($params[0])){
            $values = $params[0];
        }

        $result = [];
        $argv = array_column($this->value, $key);
        foreach($argv as $idx => $val){
            foreach($values as $fval){
                if($val == $fval){
                    $result[] = $this->value[$idx];
                }
            }
        }
        $this->value = $result;
    return $this;
    }

    # 멀티 키 => 밸류 값 찾기 OR
    public function findWhere (array $params) : ArrayHelper 
    {
        $result = [];
        foreach ($this->value as $key => $value)
        {
            foreach ($params as $fk => $fv) {
                if (isset($value[$fk]) && $value[$fk] == $fv){
                    $result[] = $value;
                }
            }
        }

        $this->value = $result;
    return $this;
    }

    # 중복 데이터 제거
    public function unique(string $column_name) : ArrayHelper
    {
        $result = [];
        $fd_args = array_unique(array_column($this->value, $column_name));
        foreach($fd_args as $idx => $val){
            $result[] = $this->value[$idx];
        }
        $this->value = $result;
    return $this;
    }

    # 배열 끝에 추가
    public function append(array $args) : ArrayHelper
    {
        $this->value[] = $args;
    return $this;
    }

    # 특정 배열의 int 값 sum 하기
    public function sum(string $key) : int
    {
        $result = [];
        foreach($this->value as $a){
            if(is_numeric($a[$key])){
                $result[] = $a[$key];
            }
        }
        $sum = 0;
        if(count($result)){
            $sum = array_sum($result);
        }
    return $sum;
    }

    # 특정 배열의 int 값의 평균 값
    public function avg(string $key) : int
    {
        $result = [];
        foreach($this->value as $a){
            if(is_numeric($a[$key])){
                $result[] = $a[$key];
            }
        }
        $avg = 0;
        $cnt = count($result);
        if($cnt>0){
            $avg = array_sum($result) / $cnt;
        }
    return $avg;
    }

    # index key number
    public function findIndex(string $key, mixed $val) : int
    {
        $index = array_search($val, array_column($this->value, $key));
    return $index;
    }

    private function asc ($a, $b ): mixed {
        return $a <=> $b;
    }

    private function desc ($a, $b ): mixed {
        return $a <= $b;
    }

    public function __get(string $propertyName){
        $result = [];
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}