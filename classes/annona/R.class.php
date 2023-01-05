<?php
namespace Flex\Annona;

use \ArrayObject;
use \ErrorException;
use Flex\Annona\Log;

final class R
{
    public static $language = ''; // 국가코드

    # resource 값
    public static $sysmsg   = [];
    public static $strings  = [];
    public static $integers = [];
    public static $floats   = [];
    public static $doubles  = [];
    public static $array    = [];
    public static $tables   = [];

    public static $r = [];

    # 배열값 추가 등록
    public static function init(string $lang='', array $support_langs=[])
    {
        $language = (trim($lang)) ? $lang : '';
        if(!$language){
            $language = (defined('_LANG_')) ? _LANG_ : 'ko';
        }

        self::$language = $language;

        # resource 객체화 시키기
        self::$r = new ArrayObject(array(), ArrayObject::STD_PROP_LIST);
    }

    #@ void
    #res/[strings | integers ]
    # values  = array('strings', 'integers');
    public static function __autoload_resource(array $resources) : void
    {
        if(is_array($resources)){
            foreach($resources as $resource_path => $resouces_args){
                foreach($resouces_args as $resource_name){
                    if(property_exists(__CLASS__,$resource_name)){
                        self::parser(self::findLanguageFile(_ROOT_PATH_.'/'.$resource_path.'/'.$resource_name.'.json'), $resource_name);
                    }
                }
            }
        }
    }

    public static function parserArray(string $resource_name, array $res_array) : void{
        switch($resource_name){
            case 'sysmsg'  : self::$sysmsg[self::$language]   = $res_array; break;
            case 'strings' : self::$strings[self::$language]  = $res_array; break;
            case 'integers': self::$integers[self::$language] = $res_array; break;
            case 'floats'  : self::$floats[self::$language]   = $res_array; break;
            case 'doubles' : self::$doubles[self::$language]  = $res_array; break;
            case 'tables'  : self::$tables[self::$language]   = $res_array; break;
            case 'array'   : self::$array[self::$language]    = $res_array; break;
        }
    }

    public static function __callStatic(string $query, array $args=[]) 
    {
        # 배열을 dictionary Object 
        if(strtolower($query) == 'dic' && count($args)){
            return (object)$args[0];
        }else if(!self::isUpdated($query)){
            self::id($query);
        }else if(isset($args[0])){
            self::setIdValues($query, $args[0]);
        }
    }

    # 배열값 추가 머지
    private static function setIdValues(string $query, array $args) : void
    {
        switch($query){
            case 'sysmsg': self::$sysmsg[self::$language] = array_merge(self::$sysmsg[self::$language], $args); break;
            case 'strings': self::$strings[self::$language] = array_merge(self::$strings[self::$language], $args); break;
            case 'integers': self::$integers[self::$language] = array_merge(self::$integers[self::$language], $args); break;
            case 'floats': self::$floats[self::$language] = array_merge(self::$floats[self::$language], $args); break;
            case 'doubles': self::$doubles[self::$language] = array_merge(self::$doubles[self::$language], $args); break;
            case 'array': self::$array[self::$language] = array_merge(self::$array[self::$language], $args); break;
            case 'tables': self::$tables[self::$language] = array_merge(self::$tables[self::$language], $args); break;
        }
    }

    ## JSON 데이터를 ID로 빠르게 호출하여 사용
    private static function id(string $query) : void
    {
        switch($query){
            case 'sysmsg':
            case 'strings':
            case 'integers':
            case 'floats':
            case 'doubles': 
            case 'array': 
                self::parser(self::findLanguageFile(_ROOT_PATH_.'/'._VALUES_.'/'.$query.'.json'), $query);
            break;
            case 'tables':
                self::parser(self::findLanguageFile(_ROOT_PATH_.'/'._QUERY_.'/'.$query.'.json'), $query);
            break;
        }
    }

    private static function isUpdated(string $query) : bool{
        $result = false;
        
        if($query=='sysmsg' && isset( self::$sysmsg[self::$language] )){ 
            $result =true;
        }else if($query=='strings' && isset( self::$strings[self::$language] )){ 
            $result =true;
        }else if($query=='integers' && isset( self::$integers[self::$language] )){ 
            $result =true;
        }else if($query=='floats' && isset( self::$floats[self::$language] )){ 
            $result =true;
        }else if($query=='doubles' && isset( self::$doubles[self::$language] )){ 
            $result =true;
        }else if($query=='array' && isset( self::$array[self::$language] )){ 
            $result =true;
        }else if($query=='tables' && isset( self::$tables[self::$language] )){ 
            $result =true;
        }else{
            if(isset( self::$r->{$query}[self::$language] )){ 
                $result =true;
            }
        }

    return $result;
    }

    #@ void
    # R::parser(_ROOT_PATH_.'/'._QUERY_.'/tables.json', 'tables');
    public static function parser(string $filename, string $query) : void
    {
        if(!$query) throw new ErrorException(__CLASS__.' :: '.__LINE__.' '.$query.' is null',0,0,'e_null');

        if(!self::isUpdated($query))
        {
            $real_filename = self::findLanguageFile($filename);
            $storage_data  = '';
            $storage_data  = file_get_contents($real_filename);
            if($storage_data)
            {
                $data = self::cleanJSON($storage_data,true);
                if(!is_array($data))
                {
                    $e_msg = '';
                    switch($data){
                        case JSON_ERROR_DEPTH: $e_msg = 'Maximum stack depth exceeded';break;
                        case JSON_ERROR_CTRL_CHAR: $e_msg = 'Unexpected control character found';break;
                        case JSON_ERROR_SYNTAX: $e_msg = 'Syntax error, malformed JSON';break;
                    }
                    throw new ErrorException(__CLASS__.' :: '.__LINE__.' '.$real_filename.' / '.$e_msg);
                }

                if(property_exists(__CLASS__,$query)){
                    self::parserArray($query, $data);
                }else{
                    self::$r->{$query}[self::$language] =&$data;
                }
            }
        }
    }

    # 버전별 AND CLEAN
    public static function cleanJSON($json, $assoc = false, $depth = 512, $options = 0) : Array {
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
    public static function findLanguageFile(string $filename) : String{
        $real_filename   = $filename;
        $path_parts      = pathinfo($real_filename);
        $nation_filename = $path_parts['dirname'].'/'.$path_parts['filename'].'_'.self::$language.'.'.$path_parts['extension'];
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
        unset(self::$tables);
        unset(self::$array);
        unset(self::$r);
    }
}
?>
