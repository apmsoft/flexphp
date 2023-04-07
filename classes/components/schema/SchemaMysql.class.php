<?php 
namespace Flex\Components\Schema;

use Flex\Components\Schema\SchemaType;

class SchemaMysql extends SchemaType
{
    private string $schema_tpl = "CREATE TABLE `{table}` (
        {columns}{primary_key}{index_key}
    ) ENGINE={engine}{auto_increment}DEFAULT CHARSET={charset}COMMENT='{comment}';";

    private array $model = [
        'table'          => '',
        'primary_key'    => '',
        'columns'        => '',
        'engine'         => 'InnoDB',
        'charset'        => 'utf8',
        'auto_increment' => '',
        'index_key'      => '',
        'comment'        => ''
    ];

    public function __construct(string $name,string $comment){
        $this->model['table'] = $name;
        $this->model['comment'] = $comment;
    }

    # PRIMARY KEY
    public function primaryKey(string $name) : SchemaMySql
    {
        $this->model['primary_key'] = sprintf("PRIMARY KEY (`%s`)",$name);
    return $this;
    }

    # INDEX KEY
    # ['name'=>'name','muidsigndate'=>'muid,signdate']
    public function indexKey(array $indexkeys) : SchemaMySql
    {
        $keys = [];
        foreach($indexkeys as $keyname => $keycolumns){
            $keys[] = sprintf("KEY `%s` (`%s`)", $keyname, implode("`,`", explode(",",$keycolumns)));
        }
        $this->model['index_key'] = implode(",\n", $keys);
    return $this;
    }

    # COLUMNS
    public function columns (...$params) : SchemaMySql 
    {
        $columns = [];
        foreach($params as $name){
            $column = parent::fetchByName($name);
            $columns[] = sprintf("`%s` %s COMMENT '%s'", $column['name'], $column['type'], $column['label']);
        }
        $this->model['columns'] = implode(",\n", $columns);

    return $this;
    }

    # ENGINE
    public function engine (string $engine) : SchemaMySql
    {
        $this->model['engine'] = sprintf("ENGINE=%s",$engine);
    return $this;
    }

    # CHARSET
    public function charset (string $charset) : SchemaMySql
    {
        $this->model['charset'] = sprintf("CHARSET=%s",$charset);
    return $this;
    }

    # AUTO_INCREMENT
    public function auto_increment(int $number) : SchemaMySql 
    {
        $this->model['auto_increment'] = sprintf("AUTO_INCREMENT=%u",$number);
    return $this;
    }

    public function create() : string
    {
        preg_match_all("/({+)(.*?)(})/", $this->schema_tpl, $matches);
        $patterns = $matches[0];
        $columns  = $matches[2];

        $render_args  = [];

        # binding
        foreach($patterns as $idx=>$text)
        {
            $column_name = $columns[$idx];
            $render_args[$text] = match($column_name){
                'table'       => $this->model['table'],
                'columns'     => (($this->model['columns']) && ($this->model['primary_key'] || $this->model['index_key'])) ? $this->model['columns'].",\n":$this->model['columns'],
                'primary_key' => ($this->model['primary_key'] && $this->model['index_key']) ? $this->model['primary_key'].",\n":$this->model['primary_key'],
                default => (trim($this->model[$column_name])) ? $this->model[$column_name].' ':''
            };
        }
        $schema = trim(strtr($this->schema_tpl, $render_args));
        
    return $schema;
    }
}
?>