<?php 
namespace Flex\Components\Schema;

use Flex\Components\Columns\ColumnsEnum;

class SchemaType
{
    public function __construct () {}
    private function type(string $NAME) : string
    {
        return match($NAME){
            'ID'     => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'NAME'   => "varchar(60) NOT NULL",
            'USERID' => "varchar(50) DEFAULT NULL",
            'PASSWD' => "varchar(100) NOT NULL",
            'EMAIL'  => "varchar(50) DEFAULT NULL",
            'BIRTHDAY','START_DATE','END_DATE' => "date NOT NULL",
            'LINKURL'      => "varchar(200) NULL",
            'TITLE'        => "varchar(160) NOT NULL",
            'EXTRACT_ID'   => "varchar(40) NOT NULL",
            'EXTRACT_DATA' => "json DEFAULT NULL",
            'DESCRIPTION'  => "longtext DEFAULT NULL",
            'SIGNDATE','REGIDATE' => "datetime NOT NULL",
            'SIGNTIMESTAMP','VIEW_COUNT' => "int(10) unsigned NOT NULL DEFAULT '0'",
            'POINT'     => "int(11) NOT NULL DEFAULT '0'",
            'RECOMMAND' => "varchar(50) DEFAULT NULL",
            'IS_PUSH'   => "enum('n','y') NOT NULL DEFAULT 'y'",
            'LEVEL'     => "smallint(5) unsigned NOT NULL DEFAULT '1'",
            'CELLPHONE' => "varchar(20) NOT NULL",
            'MUID'      => "int(11) unsigned NOT NULL DEFAULT '0'",
            'TOTAL'     => "int(10) unsigned NOT NULL DEFAULT '0'",
            'CARTID'    => "varchar(45) NOT NULL",
            'DVMAC'     => "varchar(12) NOT NULL",
            'UP_DATE','RECENTLY_CONNECT_DATE','LOGOUT_TIME' => "datetime NULL",
            'ALARM_READDATE' => "int(10) unsigned NOT NULL DEFAULT '0'",
            'INTRODUCE'      => "varchar(250) DEFAULT NULL",
            'AUTHEMAILKEY'   => "varchar(100) DEFAULT NULL",
            'HEADLINE'       => "enum('n','y') DEFAULT 'n'",
            'CATEGORY'       => "varchar(12) NOT NULL DEFAULT 'Nan'",
            'IP'             => "varchar(150) DEFAULT NULL",
            'USAGE_INT'      => "int(10) unsigned NOT NULL DEFAULT '0'",
            'ACCESS_TOKEN'   => "varchar(100) NOT NULL",
            'TOKEN'   => "varchar(40) NOT NULL",
            'SECRET_KEY'     => "varchar(60) NOT NULL",
            'MODULE_ID'      => "varchar(30) NOT NULL",
            'ITEM_ID'        => "int(11) unsigned NOT NULL",
            'ITEMS'          => "json NOT NULL",
            'GID'            => "varchar(52) NOT NULL",
            'LIST_PRICE','PRICE' => "int(10) unsigned NOT NULL",
            'SALE_PRICE'   => "json DEFAULT NULL",
            'SOLD_OUT'     => "enum('n','y') NOT NULL DEFAULT 'n'",
            'OPTION1'      => "json NOT NULL",
            'OPTION2'      => "json DEFAULT NULL",
            'ORIGIN'       => "varchar(60) NOT NULL",
            'DELIVERY_FEE' => "int(10) unsigned NOT NULL DEFAULT '0'",
            'IS_AFTER_DELIVERY','INDIVIDUAL_DELIVERY' => "enum('n','y') NOT NULL DEFAULT 'n'",
            'HASHTAGS'    => "varchar(200) DEFAULT NULL",
            "FID"         => "varchar(21) NOT NULL",
            "WID"         => "varchar(14) DEFAULT NULL",
            "MESSAGE"     => "varchar(250) NOT NULL",
            "IS_PRINT"    => "enum('n','y') NOT NULL DEFAULT 'y'",
            "REPLY_COUNT" => "int(10) unsigned NOT NULL DEFAULT '0'",
            "ITEM_COUNT"  => "int(10) unsigned NOT NULL DEFAULT '0'",
            "SALE_STATE"  => "char(2) NOT NULL DEFAULT '00'",
            "NUMBER"  => "varchar(16) NOT NULL",
            "ORDERER","SHIPPING","PROOF","SALEPOINT","PAYMENT" => "json DEFAULT NULL",
            "TERM" => "tinyint(2) unsigned NOT NULL DEFAULT '0'",
            "MEMO" => "text",
            "ORDERCODE" => "varchar(12) NOT NULL"
        };
    }

    public function fetchByName(string $name) : array 
    {
        $NAME   = strtoupper($name);
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
        $result  = [];
        $columns = ColumnsEnum::values();
        foreach($columns as $column){
            $result[] = $this->fetchByName($column);
        }

    return $result;
    }
}