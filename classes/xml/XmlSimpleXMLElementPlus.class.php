<?php
/** ======================================================
| @Author   : 김종관 | 010-4023-7046
| @Email    : apmsoft@gmail.com
| @HomePage : http://www.apmsoftax.com
| @Editor   : SUBLIME
| @UPDATE   : 0.1
----------------------------------------------------------*/
namespace Flex\Xml;

use \SimpleXMLElement;

class XmlSimpleXMLElementPlus extends SimpleXMLElement
{
    /**
     * @void
     * CData 추가
     * @param [type] $cdata_text [description]
     */
    public function addCData($elementName, $contents) : void
    {
        if(self::isNullChild($elementName)){
            $this->addChild($elementName);
        }

        $this->{$elementName} = '';
        $node = dom_import_simplexml($this->{$elementName});
        $no   = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($contents));
    }

    /**
     * @void
     * addChild
     * @param [type] $elementName [description]
     * @param [type] $contents    [description]
     */
    public function addChildPlus($elementName, $contents) : string{
        if(self::isNullChild($elementName)) {
            $this->addChild($elementName);
        }

        $this->{$elementName} = $contents;

        return $this->{$elementName};
    }

    /**
     * @return boolean
     * child Element Node 가 있는지 체크
     * @param  [type]  $elementName [description]
     * @return boolean              [description]
     */
    public function isNullChild($elementName) : bool{
        $result = false;
        if(is_null($this->{$elementName}[0]))
            $result = true;

        return $result;
    }
}
?>