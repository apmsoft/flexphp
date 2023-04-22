<?php 
namespace Flex\Components;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Annona\Model;
use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Log;

class DataProcessing extends Model
{
    public function __construct(array $params = []){
        parent::__construct($params);
    }

    private function setValue(string $name, mixed $value, array $command) : mixed
    {
        return match($name){
            "TITLE" => self::oop( new \Flex\Components\Data\Processing\Title($value), $command[0], $command[1])->value,
            "DESCRIPTION" => self::oop( new \Flex\Components\Data\Processing\Description($value), $command[0], $command[1]),
            "PASSWD"=> (new Encrypt($value))->_md5_base64(),
            "EXTRACT_DATA" => self::fun( $command[0], [$value] ),
            "FID","GID" => self::oop( new \Flex\Components\Data\Processing\Fid($command[0]), $command[1], $command[2]),
            default => $value
        };
    }

    # 클래스 실행
    private function oop(mixed $class, string $method, array $params) : mixed 
    {
        if(is_object($class)){
            return call_user_func_array( [$class, $method] , $params );
        }
    }

    # 함수 실행
    private function fun(string $func,array $params) : mixed{
        if(function_exists($func)){
            return call_user_func_array($func,$params);
        }
    }

    public function put(string $name, mixed $value, ...$command) : DataProcessing 
    {
        $NAME   = strtoupper($name);
        $column = ColumnsEnum::fetchByName($NAME);
        parent::__set($column['value'], $this->setValue($NAME, $value, $command));
    return $this;
    }

    public function fetchByName(string $name) : array 
    {
        $NAME   = strtoupper($name);
        $column = ColumnsEnum::fetchByName($NAME);
        $result = [
            'name'  => $column['value'],
            'label' => $column['label'],
            'value' => parent::__get($column['value'])
        ];
    return $result;
    }

    public function fetchAll() : array 
    {
        $result = parent::fetch();

    return $result;
    }
}
?>