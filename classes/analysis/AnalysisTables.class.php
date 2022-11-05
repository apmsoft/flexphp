<?php
namespace Flex\Analysis;

use Flex\R\R;
use Flex\Db\DbMySqli;

class AnalysisTables
{
    private $db;
    public function __construct(){
        $this->db = new \Flex\Db\DbMySqli();
    }

    public function analysis ()
    {
        # query
        $total_count = 0;
        $total_records = [];

        $qry = sprintf("SELECT TABLE_NAME,TABLE_ROWS,ENGINE,UPDATE_TIME,TABLE_COMMENT FROM INFORMATION_SCHEMA.TABLES WHERE table_schema ='%s'",_DB_NAME_);
        $rlt = $this->db->query($qry);
        while($row = $rlt->fetch_assoc())
        {
            $_tname = $row['TABLE_NAME'];
            $table_info[$_tname] = [
                'tname' 		=> $_tname,
                'rows'  		=> (int)$row['TABLE_ROWS'],
                'engine'        => $row['ENGINE'],
                'update_time'   => $row['UPDATE_TIME'],
                'table_comment' => $row['TABLE_COMMENT']
            ];

            $total_count+= (int)$row['TABLE_ROWS'];
        }
        ksort($table_info);

        return [
            'result'          => 'true',
            'total_record'    => (int)$total_count,
            'msg'             => $table_info
        ];
    }
}
?>
