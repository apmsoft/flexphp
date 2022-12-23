<?php
namespace Flex\Annona\Util;

use \SplFileObject;
class UtilSchemaMng extends SplFileObject
{
    private $file_name;
    public function __construct($file_name){
        $this->file_name = $file_name;
        parent::__construct($this->file_name, 'r');
        if (!parent::isFile()) {
            Out::prints_ln('파일이 존재하지 않거나 파일이 아닙니다');
        }
    }

    #@ return array
    ## SQL 파일에서 테이블명및 필드구문 추출
    public function fgetsTable()
    {
        $arr = array();
        $start = 0;

        $fp = @fopen($this->file_name, 'r');
        if($fp)
        {
            $x=0;
            while(!feof($fp)){
                $line = fgets($fp, 4096);
                $line = trim($line);

                # 주석인지 아닌지 체크
                $str = substr($line,0,2);
                switch(trim($str)){
                    case '--':
                    break;
                    case 'CR':
                        $start = 1;
                        $table = preg_replace('/(.*)\`(.*)\`(.*)/i', '\\2', $line);
                        $arr[$x] = array(
                            'table' => $table,
                            'qry'=> ''
                        );

                        $arr[$x]['qry'].= $line."\n";
                    break;
                    case ')':
                    case ')E':
                        $arr[$x]['qry'].= $line;
                        $start = 0;
                        $x++;
                    break;
                    default :
                        if($start==1 && $line){
                            $arr[$x]['qry'].= $line."\n";
                        }
                }
            }
            @fclose($fp);
        }

    return $arr;
    }

    #@ return array
    ## SQL 파일에서 INSERT 완전 구문내용을 추출한다
    public function fgetsTableInsert()
    {
        $inserts = array();

        $fp = @fopen($this->file_name, 'r');
        if($fp)
        {
            $x=-1;
            $insert_table_name;
            while(!feof($fp)){
                $line = fgets($fp, 4096);
                $line = trim($line);

                # 주석인지 아닌지 체크
                $str = substr($line,0,2);

                switch(trim($str)){
                    case '--': $insert_table_name=''; break;
                    default :
                        if($line){
                            if(strpos(strtolower($line), 'insert into') !==false){
                                $x++;
                                preg_match_all("|`[^`](.*)[^`]+`|U",$line,$match,PREG_PATTERN_ORDER);
                                $table_info_arg = str_replace(array('`'),'',$match[0]);
                                if(is_array($table_info_arg)){
                                    foreach($table_info_arg as $table_name){
                                        if(strcmp(_DB_NAME_, $table_name)){
                                            $insert_table_name = $table_name;
                                        }
                                    }
                                }
                            }
                            if($insert_table_name){
                                $inserts[$insert_table_name][$x].= $line;
                            }
                        }
                }
            }
            @fclose($fp);
        }
    return $inserts;
    }
}
?>