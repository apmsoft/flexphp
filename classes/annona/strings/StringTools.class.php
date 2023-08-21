<?php
namespace Flex\Annona\Strings;

class StringTools
{
    private $version = '0.2';
    public function __construct(private string $data=''){}

    # convert 10진 to string
    public function ascii2String () : StringTools
    {
        if(trim($this->data))
        {
            $cdata = '';
            $len = strlen($this->data);
            for($i=0; $i<=$len ; $i++){
                $charno = substr($this->data,$i,2);
                if($charno>=33 && $charno <= 99){ $cdata .= chr($charno); $i++;}
                else {
                    $charno = substr($this->data,$i,3);
                    if($charno >=100 && $charno <= 127){ $cdata .= chr($charno); $i+=2;}
                }
            }

            $this->data = $cdata;
        }

        return $this;
    }

    # convert 16진 to 10진
    public function hex2Ascii () : StringTools
    {
        if(trim($this->data))
        {
            $cdata = '';
            $len = strlen($this->data)-1;
            for ($i=0; $i < $len; $i+=2){
                $v = base_convert($this->data[$i].$this->data[$i+1], 16, 10);
                if($v != '0'){
                    $cdata .= $v;
                }
            }

            $this->data = $cdata;
        }

    return $this;
    }

    public function __get(string $propertyName) : mixed{
        $result = [];
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}
?>