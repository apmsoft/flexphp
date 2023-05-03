<?php 
namespace Flex\Components\Schema;

use Flex\Components\Schema\SchemaType;

class SchemaGenerator extends SchemaType
{
    private string $schema_tpl = "
    CREATE TABLE `{table}` (
        {columns}{primary_key}{index_key}
    ) ENGINE={engine}{auto_increment}DEFAULT CHARSET={charset}COMMENT='{comment}';
    ";

    private array $schema_params = [
        'table'          => '',
        'primary_key'    => '',
        'columns'        => '',
        'engine'         => 'InnoDB',
        'charset'        => 'utf8',
        'auto_increment' => '',
        'index_key'      => '',
        'comment'        => ''
    ];

    private array $labels = [];

    public function __construct(string $name,string $comment, array $labels)
    {
        $this->schema_params['table'] = $name;
        $this->schema_params['comment'] = $comment;
        $this->labels = $labels;
    }

    # PRIMARY KEY
    public function primaryKey(string $name) : SchemaGenerator
    {
        $this->schema_params['primary_key'] = sprintf("PRIMARY KEY (`%s`)",$name);
    return $this;
    }

    # INDEX KEY
    # ['name'=>'name','muidsigndate'=>'muid,signdate']
    public function indexKey(array $indexkeys) : SchemaGenerator
    {
        $keys = [];
        foreach($indexkeys as $keyname => $keycolumns){
            $keys[] = sprintf("KEY `%s` (`%s`)", $keyname, implode("`,`", explode(",",$keycolumns)));
        }
        $this->schema_params['index_key'] = implode(",\n", $keys);
    return $this;
    }

    # COLUMNS
    public function columns (...$params) : SchemaGenerator 
    {
        $columns = [];
        foreach($params as $name){
            $column = parent::fetchByName($name);
            $columns[] = sprintf("`%s` %s COMMENT '%s'", $column['name'], $column['type'], $this->labels[$column['name']]);
        }
        $this->schema_params['columns'] = implode(",\n", $columns);

    return $this;
    }

    # ENGINE
    public function engine (string $engine) : SchemaGenerator
    {
        $this->schema_params['engine'] = sprintf("ENGINE=%s",$engine);
    return $this;
    }

    # CHARSET
    public function charset (string $charset) : SchemaGenerator
    {
        $this->schema_params['charset'] = sprintf("CHARSET=%s",$charset);
    return $this;
    }

    # AUTO_INCREMENT
    public function auto_increment(int $number) : SchemaGenerator 
    {
        $this->schema_params['auto_increment'] = sprintf("AUTO_INCREMENT=%u",$number);
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
                'table'       => $this->schema_params['table'],
                'columns'     => (($this->schema_params['columns']) && ($this->schema_params['primary_key'] || $this->schema_params['index_key'])) ? $this->schema_params['columns'].",\n":$this->schema_params['columns'],
                'primary_key' => ($this->schema_params['primary_key'] && $this->schema_params['index_key']) ? $this->schema_params['primary_key'].",\n":$this->schema_params['primary_key'],
                'comment' => $this->schema_params['comment'],
                default => (trim($this->schema_params[$column_name])) ? $this->schema_params[$column_name].' ':''
            };
        }
        $schema = trim(strtr($this->schema_tpl, $render_args));
        
    return $schema;
    }
}
?>