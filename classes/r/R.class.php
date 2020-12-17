<?php
/** ================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : https://www.fancyupsoft.com
| @Editor   : VSCode
| @UPDATE   : 1.3.4
----------------------------------------------------------*/
namespace Flex\R;

use \ArrayObject;
use \ErrorException;

final class R
{
    public static $nation = _LANG_; // 국가코드

    # resource 값
    public static $sysmsg   =array();
    public static $strings  =array();
    public static $integers =array();
    public static $floats   =array();
    public static $doubles  =array();
    
    public static $columns  =array();
    public static $layout   =array();

    public static $tables   =array();
    public static $queries  =array();
    public static $manifest =array();
    public static $config   =array();

    public static $r;

    # 배열값 추가 등록
    public static function init($nation=''){
        if($nation){
            self::$nation = $nation;
        }

        # resource 객체화 시키기
        self::$r = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
    }

    #@ void
    #res/[strings | integers ]
    # values  = array('strings', 'integers');
    public static function __autoload_resource($resources){
        if(is_array($resources)){
            foreach($resources as $resource_path => $resouces_args){
                foreach($resouces_args as $resource_name){
                    if(property_exists(__CLASS__,$resource_name)){
                        self::parserResource(self::findLanguageFile(_ROOT_PATH_.DIRECTORY_SEPARATOR.$resource_path.DIRECTORY_SEPARATOR.$resource_name.'.json'), $resource_name);
                    }
                }
            }
        }
    }

    public static function parserResourceArray($resource_name, $res_array){
        switch($resource_name){
            case 'sysmsg':  self::$sysmsg   =$res_array; break;
            case 'strings': self::$strings  =$res_array; break;
            case 'integers':self::$integers =$res_array; break;
            case 'floats':  self::$floats   =$res_array; break;
            case 'doubles': self::$doubles  =$res_array; break;
            case 'columns': self::$columns  =$res_array; break;
            case 'layout':  self::$layout   =$res_array; break;
            case 'tables':  self::$tables   =$res_array; break;
            case 'queries': self::$queries  =$res_array; break;
            case 'manifest':
                case 'manifest_adm':
                self::$manifest =$res_array; break;
            case 'config':  self::$config   =$res_array; break;
        }
    }

    #@ return boolen | void
    ## XML 데이터를 ID로 빠르게 호출하여 사용
    public static function parserResourceDefinedID($query){

        if($query == 'manifest_adm'){
        }else if(!property_exists(__CLASS__,$query)){
            throw new ErrorException(__LINE__.' : '.R::$sysmsg['e_filenotfound']);
        }

        $resources = array();

        switch($query){
            case 'sysmsg':
            case 'strings':
            case 'integers':
            case 'floats':
            case 'doubles': 
                self::parserResource(self::findLanguageFile(_ROOT_PATH_.DIRECTORY_SEPARATOR._VALUES_.DIRECTORY_SEPARATOR.$query.'.json'), $query);
            break;
            case 'tables':
            case 'queries':
                self::parserResource(self::findLanguageFile(_ROOT_PATH_.DIRECTORY_SEPARATOR._QUERY_.DIRECTORY_SEPARATOR.$query.'.json'), $query);
            break;
            case 'layout':
                self::parserResource(self::findLanguageFile(_ROOT_PATH_.DIRECTORY_SEPARATOR._LAYOUT_.DIRECTORY_SEPARATOR.$query.'.json'), $query);
            break;
            case 'manifest':
            case 'manifest_adm':
                self::parserResource(self::findLanguageFile(_ROOT_PATH_.DIRECTORY_SEPARATOR._RES_.DIRECTORY_SEPARATOR.$query.'.json'), $query);
            break;
            case 'config':
                self::parserResource(self::findLanguageFile(_ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_.DIRECTORY_SEPARATOR.$query.'.json'), $query);
            break;
        }
    }

    #@ void
    # R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._QUERY_.'/queries.json', 'queries');
    # out_r(R::$queries);
    public static function parserResource($filename, $query)
    {
        if(!$query) throw new ErrorException(__CLASS__.' :: '.__LINE__.' '.$query.' is null',0,0,'e_null');
        $real_filename = self::findLanguageFile($filename);
        $storage_data = '';
        $storage_data = file_get_contents($real_filename);
        if($storage_data){
            $data = self::cleanJSON($storage_data,true);
            if(!is_array($data)){
                $e_msg = '';
                switch($data){
                    case JSON_ERROR_DEPTH: $e_msg = 'Maximum stack depth exceeded';break;
                    case JSON_ERROR_CTRL_CHAR: $e_msg = 'Unexpected control character found';break;
                    case JSON_ERROR_SYNTAX: $e_msg = 'Syntax error, malformed JSON';break;
                }
                throw new ErrorException(__CLASS__.' :: '.__LINE__.' '.$real_filename.' / '.$e_msg);
            }

            if($query == 'manifest_adm'){
                self::parserResourceArray('manifest', $data);
            }else if(property_exists(__CLASS__,$query)){
                self::parserResourceArray($query, $data);
            }else{
                self::$r->{$query} =&$data;
            }
        }
    }

    # 버전별 AND CLEAN
    public static function cleanJSON($json, $assoc = false, $depth = 512, $options = 0){
        # // 주석제거
        $json=preg_replace('/(?<!\S)\/\/\s*[^\r\n]*/', '', $json);
        $json = strtr($json,array("\n"=>'',"\t"=>'',"\r"=>'')); 
        $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
        if(version_compare(phpversion(), '5.4.0', '>=')) {
            $json = json_decode($json, $assoc, $depth, $options);
        } 
        else if(version_compare(phpversion(), '5.3.0', '>=')) {
            $json = json_decode($json, $assoc, $depth);
        } 
        else {
            $json = json_decode($json, $assoc);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_last_error();
        }
    return $json;
    }

    #@ return String
    # XML 파일이 해당언어에 해당하는 파일이 있는지 체크
    public static function findLanguageFile($filename){
        $real_filename = $filename;

        $path_parts = pathinfo($real_filename);
        $nation_filename = $path_parts['dirname'].DIRECTORY_SEPARATOR.$path_parts['filename'].'_'.self::$nation.'.'.$path_parts['extension'];
        if(file_exists($nation_filename)){
            $real_filename = $nation_filename;
        }
        
    return $real_filename;
    }

    #@ void
    public function __destruct(){
        unset(self::$sysmsg);
        unset(self::$strings);
        unset(self::$integers);
        unset(self::$floats);
        unset(self::$doubles);

        unset(self::$columns);
        unset(self::$layout);

        unset(self::$tables);
        unset(self::$queries);
        unset(self::$manifest);
        unset(self::$config);
        unset(self::$r);
    }
}
?>