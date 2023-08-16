<?php
namespace Flex\Annona\Strings;

class StringTools
{
    public function __construct(private string $data=''){}

    # convert ascii to string
    public function convertAscii2String (string $ascii_sentence) : StringTools
    {
        if(trim($ascii_sentence))
        {
            $len = strlen($ascii_sentence);
            for($i=0; $i<=$len ; $i++){
                $charno = substr($ascii_sentence,$i,2);
                if($charno>=33 && $charno <= 99){ $this->data .= chr($charno); $i++;}
                else {
                    $charno = substr($ascii_sentence,$i,3);
                    if($charno >=100 && $charno <= 127){ $this->data .= chr($charno); $i+=2;}
                }
            }
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