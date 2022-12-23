<?php
namespace Flex\Annona\Text;

# purpose : 문자을 변경하거나 더하거나 등 가공하는 역할을 한다.
class TextKeyword
{
	const CHARSET = 'utf-8';
	private $value;
	private array $allow_tags = [];

	# 키워드 중 지우고 싶은 글자 및 단어
	protected array $filter_words = [];

	# 키워드 중 끝 -1 글자 지우기
	protected array $filter_end_words = [];

	public function __construct(string $keyword, array $allow_tags=[]){
		if($keyword && $keyword !=''){
			# 문자 특수 문자
			if(is_array($allow_tags) && count($allow_tags)){
				$this->allow_tags = $allow_tags;
			}
			$this->value = $keyword;
			self::cleanWord();
		}
	return $this;
	}

	# 특수문자 제거 및 단어별 배열로 리턴
	private function cleanWord() : TextKeyword
	{
		# 한글 영어 숫자만 추출
		$pattern = '/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9]';
		if(count($this->allow_tags)){
			# 허용된 특수 문자
			$pattern .= '|[';
			foreach($this->allow_tags as $etcstr){
				$pattern .= '\\'.$etcstr;
			}
			$pattern .= ']';
		}
		$pattern .= ')+/';

		# 클린
		$keywords = $this->value;
		preg_match_all($pattern, $keywords, $match);
		$keywords = $match[0];
		if(is_array($keywords)){
			foreach($keywords as $n => $w)
			{
				# 영어만 추출
				preg_match_all("|(?<eng>[A-Za-z]+)|su", $w, $out);
				$eng = (isset($out['eng'][0])) ? $out['eng'][0] : '';
				if($eng){
					// echo $eng;
					# 발견된 단어에서 영어만 삭제
					$keywords[$n] = strtr($w, [$eng=>'']);
					# 발견된 영단어 추가
					$keywords[] = $eng;
				}
			}
		}

		$this->value = array_filter(array_unique($keywords));
	return $this;
	}

	# 분리된 단어중에서 필터 단어로 등록된 단어 지운 후 리턴
	/**
	 * filter_words : 특정 문자를 특정문자로 변환
	 * filter_end_words : 마지막 끝 글자를 특정문자로 변환
	 */
	public function filterCleanWord(array $filter_words =[], array $filter_end_words =[]) : TextKeyword
	{
		# init
		if(is_array($filter_words) && count($filter_words)){
			$this->filter_words = $filter_words;
		}

		if(is_array($filter_end_words) && count($filter_end_words)){
			$this->filter_end_words = $filter_end_words;
		}

		# 필터
		$argv = $this->value;
		$data = array();
		foreach($argv as $w)
		{
			$s = strtr($w, $this->filter_words);
			if($s && $s !='')
			{
				$es = mb_substr($s, -1, NULL, self::CHARSET);
				if (in_array($es, $this->filter_end_words)) {
					$elen = mb_strlen($s, self::CHARSET) - 1;
					$data[] = mb_substr($w, 0, $elen, self::CHARSET);
				}else{
					$data[] = $s;
				}
			}			
		}
		if(count($data)>0){
			$this->value = array_unique($data);
		}
	return $this;
	}

	public function __get(string $propertyName){
        $result = [];
        if(property_exists($this,$propertyName)){
            $result = $this->{$propertyName};
        }
    return $result;
    }
}
?>