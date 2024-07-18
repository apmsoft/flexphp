<?php
namespace Flex\Annona\Array;

# 배열 사용에 도움을 주는 클래스
class ArrayHelper
{
    public const __version = '1.3';
    public function __construct(
        private array $value
    ){}

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

    # select 여러키 중에서 원하는 키만 뽑아서 배열에 담기
    public function select(...$keys) : ArrayHelper 
    {
        $this->value = array_map(function($item) use ($keys) {
            return array_intersect_key($item, array_flip($keys));
        }, $this->value);

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
                                case 'LIKE': 
                                    if(strpos($value[$fk],$fvalue) !==false) $find_cnt++;
                                    break;
                                case 'LIKE-R':
                                    if (preg_match('/^' . preg_quote($fvalue, '/') . '/', $value[$fk])) { $find_cnt++; }
                                    break;
                                case 'LIKE-L':
                                    if (preg_match('/^.*' . preg_quote($fvalue, '/') . '$/', $value[$fk])) { $find_cnt++; }
                                    break;
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
    public function isnull(...$params) : ArrayHelper
    {
        $result = [];

        # 지정된 키들이 있는지 체크
        if(count($params) > 0){
            foreach($this->value as $idx => $arg){
                foreach ($params as $key) {
                    if (isset($arg[$key]) && ($arg[$key] === '' || $arg[$key] === null)) {
                        $result[$idx] = $arg;
                        break;
                    }
                }
            }
        }else{
            foreach($this->value as $idx => $arg){
                if (in_array('', $arg, true) || in_array(null, $arg, true)) {
                    $result[$idx] = $arg;
                }
            }
        }
        $this->value = $result;
    return $this;
    }

    # 빈데이터가 있는 배열 제거
    public function dropnull(...$params) : ArrayHelper
    {
        $result = [];
        # 지정된 키들이 있는지 체크
        if(count($params) > 0){
            foreach($this->value as $idx => $arg){
                foreach ($params as $key) {
                    if (isset($arg[$key]) && ($arg[$key] !== '' || $arg[$key] !== null)) {
                        $result[] = $arg;
                        break;
                    }
                }
            }
        }else{
            foreach($this->value as $idx => $arg){
                if (!in_array('', $arg, true) || !in_array(null, $arg, true)) {
                    $result[] = $arg;
                }
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

    # fill
    public function fill(int $start=0, ?int $length=null, mixed $value=null) : ArrayHelper
    {
        if ($length === null || $length < $start) {
            $length = $start;
        }

        // 원래의 배열 값을 유지하면서 새로운 범위만 변경
        $args = array_fill($start, $length, $value);
        $this->value = $this->value + $args;
    return $this;
    }

    # 배열 끝에 추가
    public function append(array $args) : ArrayHelper
    {
        $this->value[] = $args;
    return $this;
    }

    # 특정 배열의 int 값 sum 하기
    public function sum(string $key = '') : int|float
    {
        $result = ($key) ? self::find_numeric($key) : $this->value;
        $sum = ($key) ? array_sum($result) : count($result);
    return $sum;
    }

    # 특정 배열의 int 값 min 값
    public function min(string $key = '') : int|float
    {
        $result = ($key) ? self::find_numeric($key) : array_keys($this->value);
        $min = 0;
        if(count($result)){
            $min = min($result);
        }
    return $min;
    }

    # 특정 배열의 int 값 min 값
    public function max(string $key = '') : int|float
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

    # union All
    public function unionAll(...$params) : ArrayHelper
    {
        $this->value = call_user_func_array('array_merge', $params);
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

    # split 배열 여러개씩 잘라서 묶음으로 배열화 하기
    public function split(int $length = 2) : ArrayHelper {
        $this->value = array_chunk($this->value, $length );
    return $this;
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

    # 모든배열의 키를 새롭게 바꿈
    public function changeKeys (...$keys) : ArrayHelper | null
    {
        $result = [];

        # key 만 뽑기
        $arrKeys = (isset($keys[0]) && is_array($keys[0])) ? array_values($keys[0]) : array_values($keys);

        # 키배열크기와 값크기가 일치하는지 체크 및 부족한 키 밸류키에서 넣기
        $kCnt = count($arrKeys);
        $vCnt = count($this->value[0]);
        $valueKeys = array_keys($this->value[0]);
        if($kCnt < $vCnt){
            for($i=$kCnt; $i < $vCnt; $i++){
                $arrKeys[] = $valueKeys[$i];
            }
        }else if($kCnt > $vCnt){
            $arrKeys = array_slice($arrKeys,0,$vCnt);
        }

        # change keys
        foreach($this->value as $index => $args){
            $result[] = array_combine($arrKeys, array_values($args));
        }
        $this->value = $result;

    return $this;
    }

    # 원하는 키만 뽑아서 1차원 배열로 출력하기
    public function extractValues(string $key) : ArrayHelper
    {
        $this->value = array_column($this->value, $key);
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

    // private function asc ($a, $b ): mixed {
    //     return ($a <=> $b) ? -1 : 1;
    // }

    // private function desc ($a, $b ): mixed {
    //     return ($a <= $b) ? 1 : -1;
    // }

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
?>