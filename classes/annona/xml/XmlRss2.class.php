<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @UPDATE	: 2010-02-16
----------------------------------------------------------*/
namespace Flex\Annona\Xml;

# RSS 2.0 리더기
class XmlRss2
{
	private $parser;
	private $encoding = '';

	private $sTag	= '';
	private $eTag	= '';
	private $channel=array();

	private $items	= array();
	private $item	= array();

	private $images	= array();
	private $image	= array();
	
	#@ url : 주소
	public function __construct(string $url="", string $data="")
	{
		if(!empty($url))
			$this->getUrlData($url);
		else if(!empty($data))
			$this->getData($data);
		else
			throw new ErrorException(__CLASS__.' 주소를 입력해 주세요', __LINE__);
    }

	// 데이타로 추출
	private function getData(string $data) : void{
		# encoding /--
		if (!$this->encoding)
		{
			$tmp_encoding = preg_replace('/<\?xml version=(.*) encoding=(.*)\?>/i', '\\2', $data);
			$tmp_encoding = trim($tmp_encoding);
			$tmp_encoding = str_replace('"', '', $tmp_encoding);
			$tmp_encoding = str_replace("'", '', $tmp_encoding);
			$this->encoding = strtoupper($tmp_encoding);
		}

		$this->_xml_create();
		$this->_xml_parse($data);
	}

	// 파일 주소로 데이타 추출
	private function getUrlData(string $url) : void{
		$fp = @fopen($url,'r');
		if (!$fp) throw new ErrorException(__CLASS__.' fopen error ', __LINE__);

		$data = '';
		while (!@feof ($fp))
		{
			$data .= @fgets($fp, 4096);
			
			# encoding /--
			if (!$this->encoding)
			{
				$tmp_encoding = preg_replace('/<\?xml version=(.*) encoding=(.*)\?>/i', '\\2', $data);
				$tmp_encoding = trim($tmp_encoding);
				$tmp_encoding = str_replace('"', '', $tmp_encoding);
				$tmp_encoding = str_replace("'", '', $tmp_encoding);
				$this->encoding = strtoupper($tmp_encoding);
			}
		}
		@fclose ($fp);

		$this->_xml_create();
		$this->_xml_parse($data);
	}

	private function _xml_create() : void
	{
		$this->parser = @xml_parser_create();
		// if (is_resource($this->parser))
		// {
			xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
			xml_set_object($this->parser, $this);
			xml_set_element_handler($this->parser, 'startElement', 'endElement');
			xml_set_character_data_handler($this->parser, 'characterData');
        // }
    }

	private function _xml_parse($data) : void{
		@xml_parse($this->parser,$data);
	}

	// 메모리 비우기
	private function _xml_free() : void
	{
		if (is_resource($this->parser)){
			xml_parser_free($this->parser);
			unset( $this->parser );
		}
	}

	private function startElement($parser, $name, $attr)
	{
		switch ($name)
		{
			case 'channel':
			case 'item':
			case 'image':
			case 'textinput':
				$this->sTag = $name;
				break;
			default:
				$this->eTag = $name;
		}
	}

    private function endElement($parser, $name) : void
	{		
		if ($name == $this->sTag){
            $this->sTag = '';
        }

        if ($name == 'item'){
            $this->items[] = $this->item;
            $this->item = '';
        }

        if ($name == 'image'){
            $this->images[] = $this->image;
			$this->image = '';
        }

        $this->eTag = '';
    }


    private function characterData($parser, $data) : void
	{
		$tagName= $this->sTag;
		$field	= $this->eTag;
		
		if (trim($data)){
			if(!empty($tagName)){
				if(property_exists(__CLASS__,$tagName)){
					// if(!isset($this->{$tagName}[$field])){
					// 	$this->{$tagName}[$field] = '';
					// }
					if($field != 'description'){
						//out_ln($tagName.' > '.$field. ' == '.$data);
					}
					if($tagName == 'item'){
						// out_r($this->item);
					}
					switch($tagName){
						case 'channel' :
							if(!isset($this->channel[$field])){
								$this->channel[$field] = '';
							}
							$this->channel[$field] = $data;
						break;
						case 'item' :
							if(is_array($this->item)){
								$this->item = array();
							}
							out_r($this->item);
							if(!isset($this->item[$field])){
								$this->item[$field] = '';
							}
							$this->item[$field] = $data;
						break;
					}
					// $this->{$tagName}[$field].= $data;
					// $this->point = $this->{$tagName};
				}
			}
		}
	}

	public function __get($propertyname) : mixed {
		return $this->{$propertyname};
	}

    public function getChannel() : Array{
        return $this->channel;
    }

    public function getItems(): Array{
        return $this->items;
    }

    public function getImages() : Array{
        return $this->images;
    }

	// 비우기
	public function __destruct(){
    	$this->channel=array();
		$this->items= array();
		$this->item= array();
		$this->images= array();
		$this->image= array();
    }
}
?>
