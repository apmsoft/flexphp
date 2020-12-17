<?php
/** ======================================================
| @Author : 김종관
| @Email  : apmsoft@gmail.com
| @HomePage : http://apmsoft.tistory.com
| @Editor : sublime text 3
| @UPDATE : 0.6
----------------------------------------------------------*/
namespace Fus3\Util;

# 디비 스키마 및 insert 데이터 값 배열로 추출
class UtilSchemaManager extends SplFileObject
{
    private $file_name;

    #@ void
    public function __construct($file_name)
    {
        $this->file_name = $file_name;
        parent::__construct($this->file_name, 'r');
        if (!parent::isFile()) {
            Out::prints_ln('Error : ('.$file_name.') file not found');
        }
    }

    #@ return array
    ## SQL 파일에서 테이블명및 필드구문 추출
    ###### code /===========
    # $schemaMng = new UtilSchemaManager(_ROOT_PATH_.'/install/schema.sql');
    # $table_schema_arg = $schemaMng->fgetsTable();
    
    # example /===============================
    // --
    // -- 관리자 멤버 테이블
    // --
    // CREATE TABLE `fu_administrator` (
    //   `uid` int(10) unsigned NOT NULL COMMENT '아이디',
    //   PRIMARY KEY (`uid`)
    // ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    // 
    // --
    // -- 게시판 상세 테이블
    // --
    // CREATE TABLE `fu_bbs_description` (
    //   `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    //   PRIMARY KEY (`uid`)
    // ) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='게시판내용';
    public function fgetsTable()
    {
        $tables = array();
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
                        $tables[$x] = array(
                            'table' => $table,
                            'qry'=> ''
                        );

                        $tables[$x]['qry'].= $line."\n";
                    break;
                    case ')':
                    case ')E':
                        $tables[$x]['qry'].= $line;
                        $start = 0;
                        $x++;
                    break;
                    default :
                        if($start==1 && $line){
                            $tables[$x]['qry'].= $line."\n";
                        }
                }
            }
            @fclose($fp);
        }

    return $tables;
    }

    #@ return array
    ## SQL 파일에서 INSERT 완전 구문내용을 추출한다
    ####### code /==============
    # $schemaMng = new UtilSchemaManager(_ROOT_PATH_.'/install/schema_value.sql');
    # $insert_syntax_arg = $schemaMng->fgetsTableInsert();
    
    # example /=================================
    // --
    // -- 관리자 [시작점에 꼭 있어야 합니다]
    // --
    // INSERT INTO `fu_administrator` (`uid`,`signdate`,`lastsigndate`,`name`,`userid`,`passwd`,`level`)
    // VALUES ('1', '1415248190', 0, '관리자', 'creativeplatform@c1p.kr', 'qnHdlP/3Vx8Upy3qVMB3cQ', 100);

    // --
    // -- 일반회원
    // --
    // INSERT INTO `fu_member` (`uid`, `signdate`, `update`, `lastdate`, `p_cell`, `userid`, `passwd`)
    // VALUES (1, 1415248190, 0, 0, '01000000000', 'creativeplatform@c1p.kr', 'qnHdlP/3Vx8Upy3qVMB3cQ');
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