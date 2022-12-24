<?php
namespace Flex\Annona\Array;

# 배열 사용에 도움을 주는 클래스
class ArrayHelper
{
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
    public function findAll(string $key, mixed $val) : ArrayHelper
    {
        $result = [];
        foreach($this->value as $a){
            if($a[$key] == $val){
                $result[] = $a;
            }
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