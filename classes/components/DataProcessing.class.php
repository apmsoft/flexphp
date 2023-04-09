<?php 
namespace Flex\Components;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Annona\Model;
use Flex\Components\Data\Processing\Fid;
use Flex\Components\Data\Processing\Description;
use Flex\Annona\Cipher\Encrypt;
use Flex\Annona\Log;

class DataProcessing extends Model
{
    public function __construct(){
        parent::__construct();
    }

    private function setValue(string $name, mixed $value, array $command) : mixed
    {
        return match($name){
            "description" => call_user_func_array( [new Description($value),$command[0]], $command[1] ),
            "passwd"=> (new Encrypt($value))->_md5(),
            "fid"   => call_user_func_array( [new Fid($command[0]) , $command[1]], $command[2] ),
            default => $value
        };
    }

    public function setByName(string $name, mixed $value, ...$command) : DataProcessing 
    {
        try{
            $NAME   = strtoupper($name);
            $column = ColumnsEnum::fetchByName($NAME);
            parent::__set($name, $this->setValue($name, $value, $command));
        }catch (\UnexpectedValueException $e) {
            Log::e( $e->getMessage() );
        }catch (\Exception $e) {
            Log::e( $e->getMessage() );
        }
    return $this;
    }

    public function fetchByName(string $name) : array 
    {
        $NAME   = strtoupper($name);
        $column = ColumnsEnum::fetchByName($NAME);
        $result = [
            'name'  => $column['value'],
            'label' => $column['label'],
            'value' => parent::__get($name)
        ];
    return $result;
    }

    public function fetchAll() : array 
    {
        $result  = parent::fetch();

    return $result;
    }
}
?>