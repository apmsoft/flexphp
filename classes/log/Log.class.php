<?php
/** ======================================================
| @Author   : 김종관 | 010-4023-7046
| @Email    : apmsoft@gmail.com
| @HomePage : http://www.apmsoftax.com
| @Editor   : Eclipse(default)
| @version : 1.0.140926
 * DB 데어터 및 동적 데이터를 JSON 및 파일데이터 형태로 저장하고 일정 시간동안 유지 하는 목적을 갖는다
----------------------------------------------------------*/
namespace Fus3\Log;

use Fus3\Db\DbMySqli;

final class Log
{
    public static $tname = '';

    # void
    public static function init($table)
    {
        self::$tname = $table;
    }

    #@ void : inset
    public static function d($description){
        # db
        $db = new DbMySqli();

        $db['muid']        = $_SESSION['auth_id'];
        $db['description'] = $description;
        $db['signdate']    = time();
        $db['ip']          = $_SERVER['REMOTE_ADDR'];        
        $db->insert(self::$tname);
    }
}
?>
