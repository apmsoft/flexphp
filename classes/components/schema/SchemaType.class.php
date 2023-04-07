<?php 
namespace Flex\Components\Schema;

use Flex\Components\Columns\ColumnsEnum;

class SchemaType
{
    public function __construct (
    ) {}

    private function type(string $NAME) : string
    {
        return match($NAME){
            'ID'           => 'int(11) unsigned NOT NULL AUTO_INCREMENT',
            'NAME'         => 'varchar(60) NOT NULL',
            'USERID'       => 'varchar(50) DEFAULT NULL',
            'PASSWD'       => 'varchar(100) NOT NULL',
            'EMAIL'        => 'varchar(50) DEFAULT NULL',
            'BIRTHDAY'     => 'date NOT NULL',
            'START_DATE'   => 'date NOT NULL',
            'END_DATE'     => 'date NOT NULL',
            'LINKURL'      => 'varchar(200) NULL ',
            'VIEW_COUNT'   => 'int',
            'TITLE'        => 'varchar(80) NOT NULL',
            'EXTRACT_ID'   => 'varchar(40) NOT NULL',
            'EXTRACT_DATA' => 'json DEFAULT NULL',
            'DESCRIPTION'  => 'longtext DEFAULT NULL',
            'SIGNDATE'     => "datetime NOT NULL",
            'POINT'        => "int(11) NOT NULL DEFAULT '0'",
            'RECOMMAND'    => "varchar(50) DEFAULT NULL",
            'IS_PUSH'      => "enum('n','y') NOT NULL DEFAULT 'y'",
            'LEVEL'        => "smallint(5) unsigned NOT NULL DEFAULT '1'",
            'CELLPHONE'    => "varchar(20) NOT NULL",
            'MUID'         => "int(11) unsigned NOT NULL DEFAULT '0'"
        };
    }

    public function fetchByName(string $name) : array 
    {
        $NAME = strtoupper($name);
        $column = ColumnsEnum::fetchByName($NAME);
        $result = [
            'name'  => $column['value'],
            'label' => $column['label'],
            'type'  => $this->type($NAME)
        ];
    return $result;
    }

    public function fetchAll() : array 
    {
        $result = [];
        $columns = ColumnsEnum::values();
        foreach($columns as $column){
            $result[] = $this->fetchByName($column);
        }

    return $result;
    }
}