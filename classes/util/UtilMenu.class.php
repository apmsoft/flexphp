<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : apmsoft.tistory.com
| @Editor   : Sublime Text 3
| @UPDATE   : 1.0.1 
----------------------------------------------------------*/
namespace Fus3\Util;

class UtilMenu
{
    public $menu = array();
    public $current_access_menu = array();

    public function __construct($mid='', $filename='menu'){
        $file = _ROOT_PATH_.DIRECTORY_SEPARATOR._MENU_.DIRECTORY_SEPARATOR.$filename.'.xml';

        # xml 파일
        $resource_obj=new XmlSimpleXMLElementPlus(self::findLanguageFile($file), null, true);
        if($resource_obj->isNullChild('menu')) return false;

        $resource_obj= simplexml_load_string((string)$resource_obj->asXML(), null, LIBXML_NOCDATA);
        $result = $resource_obj->xpath('menu');
        if(is_array($result)){
            $this->menu =self::xml2Array($result[0],false);
            if($mid){
                self::currentChildMenu($mid);
            }
        }
    }

    #@ return String
    # XML 파일이 해당언어에 해당하는 파일이 있는지 체크
    private function findLanguageFile($filename){
        $real_filename = $filename;

        $path_parts = pathinfo($real_filename);
        $nation_filename = $path_parts['dirname'].DIRECTORY_SEPARATOR.$path_parts['filename'].'_'._LANG_.'.'.$path_parts['extension'];
        if(file_exists($nation_filename)){
            $real_filename = $nation_filename;
        }
    return $real_filename;
    }

    #@ return array
    private function xml2Array($xml, $root = true)
    {
        if (!$xml->children()) {
            return (string)$xml;
        }

        $array = array();
        foreach ($xml->children() as $element => $node)
        {
            $totalElement = count($xml->{$element});

            if (!isset($array[$element])) {
                $array[$element] = "";
            }

            // attributes
            if ($attributes = $node->attributes())
            {
                if (!count($node->children())){
                    $data['value'] = (string)$node;
                } else {
                    $data = self::xml2Array($node, false);
                }
                foreach ($attributes as $attr => $value) {
                    $data[$attr] = (string)$value;
                }

                $array[$element][] = $data;
            // only a value
            } else {
                $array[$element][] = self::xml2Array($node, false);
            }
        }

        if ($root) {
            return array($xml->getName() => $array);
        } else {
            return $array;
        }
    }

    #@ void
    # mid 값에 따른 메뉴 정보와 하위 메뉴 값 추출
    private function currentChildMenu($mid){
        $result = array();

        $mid_str = str_replace('00','', $mid);
        $len     = intval(strlen($mid_str)/2);

        # 1차, 2차 비교 mid 값 설정
        $mid_1 = '';
        $mid_2 = '';
        $mid_3 = '';
        if($len ==1){
            $mid_1 = substr($mid,0,2);
            $mid_2 = '';
            $mid_3 = '';
        }else if($len ==2){
            $mid_1 = substr($mid,0,2);
            $mid_2 = substr($mid,0,4);
            $mid_3 = '';
        }else if($len ==3){
            $mid_1 = substr($mid,0,2);
            $mid_2 = substr($mid,0,4);
            $mid_3 = $mid;
        }

        foreach($this->menu['menu1'] as $anum =>$arr)
        {
            # 1차 비교
            if(substr($arr['mid'],0,2) == $mid_1)
            {
                # 현재 1차 메뉴정보
                $this->current_menu_info[]=array(
                    'mid'   => $arr['mid'],
                    'title' => $arr['title'],
                    'href'  => $arr['href']
                );

                if(isset($arr['menu2']))
                {
                    # 2차 비교
                    if($mid_2)
                    {
                        foreach($arr['menu2'] as $bnum => $arr2)
                        {
                            if(substr($arr2['mid'],0,4) == $mid_2)
                            {
                                # 현재 2차 메뉴정보
                                $this->current_menu_info[]=array(
                                    'mid'   => $arr2['mid'],
                                    'title' => $arr2['title'],
                                    'href'  => $arr2['href']
                                );

                                # 3차 비교
                                if($mid_3)
                                {
                                    if(isset($arr2['menu3'])){
                                        foreach($arr2['menu3'] as $cnum => $arr3)
                                        {
                                            if($arr3['mid'] == $mid_3){
                                                # 현재 3차 메뉴정보
                                                $this->current_menu_info[]=array(
                                                    'mid'   => $arr3['mid'],
                                                    'title' => $arr3['title'],
                                                    'href'  => $arr3['href']
                                                );
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            }
        }
    }

    #@ void
    public function __destruct(){
        unset($this->menu);
        unset($this->current_access_menu);
    }
}
?>
