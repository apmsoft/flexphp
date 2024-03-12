<?php
namespace Flex\Annona;

use \ArrayObject;
use \Exception;

final class R
{
    public const __version = '2.2.7';
    public static $language = ''; // 국가코드

    # resource 값
    public static $sysmsg   = [];
    public static $strings  = [];
    public static $integers = [];
    public static $floats   = [];
    public static $doubles  = [];
    public static $arrays    = [];
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

    # 특정 리소스 키에 해당하는 값 리턴
    private static function get(string $query, string $fieldname){
        $r_data = match((string)$query){
            'sysmsg','strings','integers','floats','doubles','arrays','tables' => self::${$query}[self::$language][$fieldname],
            default => self::$r->{$query}[self::$language][$fieldname]
        };

        return $r_data;
    }

    # 특정 리소스에 전체 값 바꾸기
    public static function set(string $query, array $data) : void{
        $r_data = match((string)$query){
            'sysmsg','strings','integers','floats','doubles','arrays','tables' => self::${$query}[self::$language] = $data,
            default => self::$r->{$query}[self::$language] = $data
        };
    }

    private static function fetch(string $query): array{
        $r_data = match((string)$query){
            'sysmsg','strings','integers','floats','doubles','arrays','tables' => self::${$query}[self::$language],
            default => self::$r->{$query}[self::$language]
        };

        return $r_data;
    }

    # 특정리소스의 키에 해당하는 값들을 배열로 돌려받기
    private static function selectR(array $params) : array
    {
        $argv = [];
        foreach($params as $query => $fieldname){
            $columns = [ $fieldname ];
            if(strpos($fieldname,",") !==false){
                $columns = explode(",", $fieldname);
            }

            foreach($columns as $columname){
                $argv[$columname] = self::get($query,$columname);
            }
        }
        return $argv;
    }

    public static function __callStatic(string $query, array $args=[])
    {
        # 배열을 dictionary Object
        if(strtolower($query) == 'dic' && count($args)){
            return (object)$args[0];
        }else if(($query == 'fetch') && (isset($args[0]) && is_string($args[0])) ){
            return self::fetch($args[0]);
        }else if($query == 'select' && count($args)){
            return self::selectR($args[0]);
        }else if(!self::is($query)){ #이미 로드된데이터인지체크
            self::id($query);
        }else if(isset($args[0]) && is_string($args[0])){ # 해당하는 리소스 키값 리턴
            return self::get($query, $args[0]);
        }else if(isset($args[0]) && is_array($args[0])){ # 해당하는 리소스 데이터 병합
            self::mergeData($query, $args[0]);
        }
    }

    # 배열값 추가 머지
    private static function mergeData(string $query, array $args) : void
    {
        $r_array = match((string)$query){
            'sysmsg','strings','integers','floats','doubles','arrays','tables' => self::${$query}[self::$language],
            default => self::$r->{$query}[self::$language]
        };

        if(is_array($r_array)){
            if(property_exists(__CLASS__,$query)){
                self::${$query}[self::$language] = array_merge(self::${$query}[self::$language], $args);
            }else{
                self::$r->{$query}[self::$language] = array_merge(self::$r->{$query}[self::$language], $args);
            }
        }
    }

    ## JSON 데이터를 ID로 빠르게 호출하여 사용
    private static function id(string $query) : void
    {
        $define_dir = match((string)$query){
            'sysmsg','strings','integers','floats','doubles','arrays' => _VALUES_,
            'tables' => _QUERY_,
            default => ''
        };
        if($define_dir){
            $filename = self::findLanguageFile(_ROOT_PATH_.'/'.$define_dir.'/'.$query.'.json');
            self::parser($filename, $query);
        }
    }

    # 데이터 로딩된 상태인지 체크
    private static function is(string $query) : bool{
        $result = match((string)$query){
            'sysmsg','strings','integers','floats','doubles','arrays','tables' => (isset(self::${$query}[self::$language])) ?? false,
            default => (isset(self::$r->{$query}[self::$language])) ?? false
        };

    return $result;
    }

    #@ void
    # R::parser(_ROOT_PATH_.'/'._QUERY_.'/tables.json', 'tables');
    public static function parser(string $filename, string $query) : void
    {
        if(!$query) throw new \Exception(__CLASS__.' :: '.__LINE__.' '.$query.' is null',0,0,'e_null');

        if(!self::is($query))
        {
            $real_filename = self::findLanguageFile($filename);
            $storage_data  = '';
            $storage_data  = file_get_contents($real_filename);
            if($storage_data)
            {
                $data = self::filterJSON($storage_data,true);
                if(!is_array($data))
                {
                    $e_msg = '';
                    switch($data){
                        case JSON_ERROR_DEPTH: $e_msg = 'Maximum stack depth exceeded';break;
                        case JSON_ERROR_CTRL_CHAR: $e_msg = 'Unexpected control character found';break;
                        case JSON_ERROR_SYNTAX: $e_msg = 'Syntax error, malformed JSON';break;
                    }
                    throw new \Exception(__CLASS__.' :: '.__LINE__.' '.$real_filename.' / '.$e_msg);
                }

                if(property_exists(__CLASS__,$query)){
                    self::${$query}[self::$language] = $data;
                }else{
                    self::$r->{$query}[self::$language] =&$data;
                }
            }
        }
    }

    # 버전별 AND CLEAN
    public static function filterJSON($json, $assoc = false, $depth = 512, $options = 0) : mixed {
        # // 주석제거
        $json=preg_replace('/(?<!\S)\/\/\s*[^\r\n]*/', '', $json);
        $json = strtr($json,array("\n"=>'',"\t"=>'',"\r"=>''));
        $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);

        if (version_compare(phpversion(), '8.0.0', '>='))
        {
            $options |= JSON_THROW_ON_ERROR;
            try {
                $json = json_decode($json, $assoc, $depth, $options);
            } catch (\JsonException $e) {
                return $e->getMessage();
            }
        } else if (version_compare(phpversion(), '7.0.0', '>=')) {
            $options |= JSON_ERROR_EXCEPTION;
            try {
                $json = json_decode($json, $assoc, $depth, $options);
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            $json = json_decode($json, $assoc, $depth);
        }

        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_last_error();
        }
    return $json;
    }

    #@ return String
    # 파일이 해당언어에 해당하는 파일이 있는지 체크
    public static function findLanguageFile(string $filename) : String{
        $real_filename   = $filename;
        $path_parts      = pathinfo($real_filename);
        $nation_filename = $path_parts['dirname'].'/'.$path_parts['filename'].'_'.self::$language.'.'.$path_parts['extension'];
        if(file_exists($nation_filename)){
            $real_filename = $nation_filename;
        }
    return $real_filename;
    }

    #@ __destruct
    public function __destruct(){
        unset(self::$sysmsg);
        unset(self::$strings);
        unset(self::$integers);
        unset(self::$floats);
        unset(self::$doubles);
        unset(self::$tables);
        unset(self::$arrays);
        unset(self::$r);
    }
}
?>
