<?php
namespace Flex\Annona\Arrays;

use \ErrorException;

# 배열 사용에 도움을 주는 클래스
class ArraysHelper
{
    public function __construct(
        private array $value
    ){return $this;}

    # 멀티배열 키의 값으로 소팅 [{},{}]
    # sort : asc | desc
    # key : 소팅 비교할 키네임
    public function multiSort(string $key, string $sort = 'asc') : ArraysHelper 
    {
        $result = [];
        usort($this->value, function($a, $b) use ($key,$sort) {
            if(strtolower($sort) == 'desc'){
                return $a[$key] <= $b[$key];
            }else {
                return $a[$key] <=> $b[$key];
            }
        });
    return $this;
    }

    public function __get(string $propertyName){
        $result = [];
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}