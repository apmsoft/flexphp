<?php
namespace Flex\Annona\Db;

use \MySQLi;

# purpose : 각종 SQL 관련 디비를 통일성있게  작성할 수 있도록 틀을 제공
abstract class QueryBuilderAbstract extends mysqli
{
    protected array $query_params;
    protected string $query_tpl = 'SELECT {columns}FROM {table}{on}{where}{groupby}{having}{orderby}{limit}';
    protected string $query = '';

    const QUERY_GROUPBY = 2;
    const QUERY_DEFAULT = 1;

    abstract public function table(...$table) : mixed;
    abstract public function select(...$columns) : mixed;
    abstract public function selectGroupBy(...$columns) : mixed;
    abstract public function where(...$where) : mixed;
    abstract public function orderBy(...$orderby) : mixed;
    abstract public function on(...$on) : mixed;
    abstract public function limit(...$limit) : mixed;
    abstract public function distinct(string $column_name) : mixed;
    abstract public function groupBy(...$columns) : mixed;
    abstract public function having(...$columns) : mixed;
    abstract public function total(string $column_name) : int;

    public function init() : void {
        $this->query_params = [
            'columns' => '*',
            'table'   => '',
            'where'   => '',
            'orderby' => '',
            'on'      => '',
            'limit'   => '',
            'groupby' => '',
            'having'  => ''
        ];
    }

    public function get() : string
    {
        $result = '';
        preg_match_all("/({+)(.*?)(})/", $this->query_tpl, $matches);
        $patterns = $matches[0];
        $columns  = $matches[2];
        $render_args = [];
        foreach($patterns as $idx=>$text){
            $column_name = $columns[$idx];
            $render_args[$text] = (trim($this->query_params[$column_name])) ? $this->query_params[$column_name].' ':'';
        }
        // print_r($render_args);
        $this->query = trim(strtr($this->query_tpl, $render_args));
    return $this->query;
    }
}
?>