<?php 
namespace Flex\Component;

use Flex\Component\ColumnsInterface;
use Flex\Component\EntryArrayTrait;
use Flex\Annona\R;
use Flex\Annona\Log;

enum ColumnsEnum implements ColumnsInterface
{
    use EntryArrayTrait;

    case ID;
    case NAME;
    case USERID;
    case PASSWD;
    case EMAIL;
    case BIRTHDAY;
    case START_DATE;
    case END_DATE;
    case LINKURL;
    case VIEW_COUNT;
    case TITLE;
    case EXTRACT_ID;
    case EXTRACT_DATA;

    #@ interface
    public function name() : string 
    {
        return match($this){
            static::ID => 'id',
            static::NAME => 'name',
            static::USERID => 'userid',
            static::PASSWD => 'passwd',
            static::EMAIL => 'email',
            static::BIRTHDAY => 'birthday',
            static::START_DATE => 'start_date',
            static::END_DATE => 'end_date',
            static::LINKURL => 'linkurl',
            static::VIEW_COUNT => 'view_count',
            static::TITLE => 'title',
            static::EXTRACT_ID => 'extract_id',
            static::EXTRACT_DATA => 'extract_data'
        };
    }

    #@ interface
    public function label() : string 
    {
        return match($this){
            static::ID => R::columns('id'),
            static::NAME => R::columns('name'),
            static::USERID => R::columns('userid'),
            static::PASSWD => R::columns('passwd'),
            static::EMAIL => R::columns('email'),
            static::BIRTHDAY => R::columns('birthday'),
            static::START_DATE => R::columns('start_date'),
            static::END_DATE => R::columns('end_date'),
            static::LINKURL => R::columns('linkurl'),
            static::VIEW_COUNT => R::columns('view_count'),
            static::TITLE => R::columns('title'),
            static::EXTRACT_ID => R::columns('extract_id'),
            static::EXTRACT_DATA => R::columns('extract_data')
        };
    }

    #@ interface
    public function valueType() : string
    {
        return match($this){
            static::ID => 'int',
            static::NAME => 'string',
            static::USERID => 'string',
            static::PASSWD => 'password',
            static::EMAIL => 'email',
            static::BIRTHDAY => 'date',
            static::START_DATE => 'date',
            static::END_DATE => 'date',
            static::LINKURL => 'url',
            static::VIEW_COUNT => 'int',
            static::TITLE => 'string',
            static::EXTRACT_ID => 'string',
            static::EXTRACT_DATA => 'json'
        };
    }

    #@ interface
    public function valueDefault() : mixed
    {
        return match($this){
            static::ID => NULL,
            static::NAME => NULL,
            static::USERID => NULL,
            static::PASSWD => NULL,
            static::EMAIL => NULL,
            static::BIRTHDAY => NULL,
            static::START_DATE => NULL,
            static::END_DATE => NULL,
            static::LINKURL => NULL,
            static::VIEW_COUNT => 0,
            static::TITLE => NULL,
            static::EXTRACT_ID => NULL,
            static::EXTRACT_DATA => NULL
        };
    }

    #@ interface
    public function type() : string
    {
        return match($this){
            static::ID => 'int',
            static::NAME => 'varchar',
            static::USERID => 'varchar',
            static::PASSWD => 'varchar',
            static::EMAIL => 'varchar',
            static::BIRTHDAY => 'date',
            static::START_DATE => 'date',
            static::END_DATE => 'date',
            static::LINKURL => 'varchar',
            static::VIEW_COUNT => 'int',
            static::TITLE => 'varchar',
            static::EXTRACT_ID => 'varchar',
            static::EXTRACT_DATA => 'varchar'
        };
    }

    #@ interface
    public function length() : mixed
    {
        return match($this){
            static::ID => 10,
            static::NAME => 14,
            static::USERID => 14,
            static::PASSWD => 60,
            static::EMAIL => 40,
            static::BIRTHDAY => 'date',
            static::START_DATE => 'date',
            static::END_DATE => 'date',
            static::LINKURL => 160,
            static::VIEW_COUNT => 10,
            static::TITLE => 60,
            static::EXTRACT_ID => 60,
            static::EXTRACT_DATA => 'json'
        };
    }

    #@ interface
    # `view_count` int(10) unsigned NOT NULL DEFAULT '0',
    public function typeNull() : string
    {
        return match($this){
            static::ID => 'unsigned NOT NULL AUTO_INCREMENT',
            static::NAME => 'NOT NULL',
            static::USERID => 'NOT NULL',
            static::PASSWD => 'NOT NULL',
            static::EMAIL => 'NOT NULL',
            static::BIRTHDAY => 'NOT NULL',
            static::START_DATE => 'NOT NULL',
            static::END_DATE => 'NOT NULL',
            static::LINKURL => 'NULL',
            static::VIEW_COUNT => 'unsigned NOT NULL',
            static::TITLE => 'NOT NULL',
            static::EXTRACT_ID => 'NOT NULL',
            static::EXTRACT_DATA => 'NULL'
        };
    }

    static public function fetchByName(string $name) : array 
    {
        $NAME = strtoupper($name);
        $enum = constant("self::{$NAME}");
        $result = [
            'name'         => $enum->name(),
            'label'        => $enum->label(),
            'valueType'    => $enum->valueType(),
            'valueDefault' => $enum->valueDefault(),
            'type'         => $enum->type(),
            'length'       => $enum->length(),
            'typeNull'     => $enum->typeNull()
        ];
    return $result;
    }
}
?>