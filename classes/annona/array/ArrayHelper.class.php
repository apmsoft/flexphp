<?php
namespace Flex\Annona\Array;

use \Flex\Annona\Log;

# 배열 사용에 도움을 주는 클래스
class ArrayHelper
{
    private $version = '0.9.9';
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
        if($index > -1){
            $this->value = $this->value[$index];
        }else $this->value = [];
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
    public function findWhere (array $params, string $operator='AND') : ArrayHelper 
    {
        $result = [];
        $find_mcnt   = count($params);
        $up_operator = strtoupper($operator);
        foreach ($this->value as $key => $value)
        {
            if($up_operator == 'AND')
            {
                $find_cnt = 0;
                foreach ($params as $fk => $fv) {
                    if (isset($value[$fk])){
                        if(is_array($fv)){
                            $condition = $fv[0];
                            $fvalue = $fv[1];
                            switch($condition){
                                case '>': if($value[$fk] > $fvalue) $find_cnt++;break;
                                case '>=': if($value[$fk] >= $fvalue) $find_cnt++;break;
                                case '<': if($value[$fk] < $fvalue) $find_cnt++; break;
                                case '<=': if($value[$fk] <= $fvalue) $find_cnt++; break;
                                case '=': if($value[$fk] = $fvalue) $find_cnt++;break;
                                case '!=': if($value[$fk] != $fvalue) $find_cnt++;break;
                            }
                        }else if($value[$fk] == $fv){
                            $find_cnt++;
                        }
                    }

                    if($find_cnt == $find_mcnt){
                        $result[] = $value;
                    }
                }
            }else{
                foreach ($params as $fk => $fv) {
                    if (isset($value[$fk]) && $value[$fk] == $fv){
                        $result[] = $value;
                    }
                }
            }
        }

        $this->value = $result;
    return $this;
    }

    # 멀티 키 => 밸류 값 찾기
    public function findWhereIndex (array $params) : int 
    {
        $result = -1;
        $find_mcnt = count($params);
        foreach ($this->value as $key => $value)
        {
            $find_cnt = 0;
            foreach ($params as $fk => $fv) {
                if (isset($value[$fk]) && $value[$fk] == $fv){
                    $find_cnt++;
                }

                if($find_cnt == $find_mcnt){
                    $result = $key;
                    break;
                }
            }
        
            if($result>-1){
                break;
            }
        }
    return $result;
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

    # 빈데이터가 있는 배열 찾기
    public function isnull() : ArrayHelper
    {
        $result = [];
        foreach($this->value as $idx => $arg){
            if(array_search(null, $arg)){
                $result[$idx] = $arg;
            }
        }
        $this->value = $result;
    return $this;
    }

    # 빈데이터가 있는 배열 제거
    public function dropnull() : ArrayHelper
    {
        $result = [];
        foreach($this->value as $idx => $arg){
            if(!in_array(null, $arg)){
                $result[] = $arg;
            }
        }
        $this->value = $result;
    return $this;
    }

    # 빈데이터 있는 배열에 데이터 채우기
    public function fillnull(mixed $filldata) : ArrayHelper
    {
        $is_arr = (is_array($filldata)) ? true : false;
        foreach($this->value as $idx => $arg){
            $cur_keys = array_keys($arg,null);
            foreach($cur_keys as $nkey){
                if($is_arr){
                    if(isset($filldata[$nkey])){
                        $this->value[$idx][$nkey] = $filldata[$nkey];
                    }
                }else{
                    $this->value[$idx][$nkey] = $filldata;
                }
            }
        }
    return $this;
    }

    # 배열 끝에 추가
    public function append(array $args) : ArrayHelper
    {
        $this->value[] = $args;
    return $this;
    }

    # 특정 배열의 int 값 sum 하기
    public function sum(string $key = '') : int
    {
        $result = ($key) ? self::find_numeric($key) : $this->value;
        $sum = ($key) ? array_sum($result) : count($result);
    return $sum;
    }

    # 특정 배열의 int 값 min 값
    public function min(string $key = '') : int
    {
        $result = ($key) ? self::find_numeric($key) : array_keys($this->value);
        $min = 0;
        if(count($result)){
            $min = min($result);
        }
    return $min;
    }

    # 특정 배열의 int 값 min 값
    public function max(string $key = '') : int
    {
        $result = ($key) ? self::find_numeric($key) : array_keys($this->value);
        $max = 0;
        if(count($result)){
            $max = max($result);
        }
    return $max;
    }

    # 특정 배열의 int 값의 평균 값
    public function avg(string $key) : int|float
    {
        $result = self::find_numeric($key);
        $avg = 0;
        $cnt = count($result);
        if($cnt>0){
            $avg = array_sum($result) / $cnt;
        }
    return $avg;
    }

    # union
    public function union (array $params) : ArrayHelper
    {
        $temp = [];

        # params columns
        $columns = [];
        foreach($params as $uikey => $pvalue){
            $columns[$uikey] = explode(',', $pvalue);
        }

        $arr = [];
        foreach($this->value as $uikey => $args)
        {
            $index = 0;
            foreach($args as $cidx => $cargs)
            {
                foreach($columns[$uikey] as $column_name){
                    $arr[$index][$column_name] = $cargs[$column_name];
                }
            $index++;
            }
        }
        $this->value = $arr;
    return $this;
    }

    # index key number
    public function findIndex(string $key, mixed $val) : int
    {
        $result = -1;
        $index = array_search($val, array_column($this->value, $key));
        if($index !== false){
            $result = $index;
        }
    return $result;
    }

    # slice 배열 자르기
    public function slice(...$params) : ArrayHelper {
        $result = [];
        if(count($params) > 1){
            $result = array_slice($this->value, $params[0],$params[1]);
        }else {
            $result = array_slice($this->value, $params[0]);
        }

        $this->value = $result;

    return $this;
    }

    private function find_numeric (string $key) : array 
    {
        $result = [];
        foreach($this->value as $a){
            if(is_numeric($a[$key])){
                $result[] = $a[$key];
            }
        }
    return $result;
    }

    private function asc ($a, $b ): mixed {
        return ($a <=> $b) ? -1 : 1;
    }

    private function desc ($a, $b ): mixed {
        return ($a <= $b) ? 1 : -1;
    }

    public function __get(string $propertyName){
        $result = [];
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}
?>