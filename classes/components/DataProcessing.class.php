<?php 
namespace Flex\Components;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Annona\Model;
use Flex\Components\Data\Processing\Fid;
use Flex\Components\Data\Processing\Description;
use Flex\Components\Data\Processing\TITLE;
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
            "TITLE" => call_user_func_array( [new Title($value),$command[0]], $command[1] )->value,
            "DESCRIPTION" => call_user_func_array( [new Description($value),$command[0]], $command[1] ),
            "PASSWD"=> (new Encrypt($value))->_md5(),
            "FID"   => call_user_func_array( [new Fid($command[0]) , $command[1]], $command[2] ),
            default => $value
        };
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