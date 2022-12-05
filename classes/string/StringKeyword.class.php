<?php
namespace Flex\String;

# define : _CHRSET_
# purpose : 문자을 변경하거나 더하거나 등 가공하는 역할을 한다.
class StringKeyword
{
	private $keywords;
	private $allow_tags = [];

	# 키워드 중 지우고 싶은 글자 및 단어
	protected $filter_words = [];

	# 키워드 중 끝 -1 글자 지우기
	protected $filter_end_words = [];

	public function __construct(string $keyword, array $allow_tags=[]){
		if($keyword && $keyword !=''){
			# 문자 특수 문자
			if(is_array($allow_tags) && count($allow_tags)){
				$this->allow_tags = $allow_tags;
			}

			# 특수 문자 제거
			$this->keywords = self::cleanWord($keyword);
		}
	}

	# 특수문자 제거 및 단어별 배열로 리턴
	# return array()
	private function cleanWord($keywords)
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

		return array_filter(array_unique($keywords));
	}

	# 분리된 단어중에서 필터 단어로 등록된 단어 지운 후 리턴
	/**
	 * filter_words : 틀정 문자를 특정문자로 변환
	 * filter_end_words : 마지막 끝 글자를 특정문자로 변환
	 */
	public function filterCleanWord(array $filter_words =[], array $filter_end_words =[])
	{
		# init
		if(is_array($filter_words) && count($filter_words)){
			$this->filter_words = $filter_words;
		}

		if(is_array($filter_end_words) && count($filter_end_words)){
			$this->filter_end_words = $filter_end_words;
		}

		# 필터
		$argv = $this->keywords;
		$data = array();
		foreach($argv as $w)
		{
			$s = strtr($w, $this->filter_words);
			if($s && $s !='')
			{
				$es = mb_substr($s, -1, NULL, _CHRSET_);
				if (in_array($es, $this->filter_end_words)) {
					$elen = mb_strlen($s, _CHRSET_) - 1;
					$data[] = mb_substr($w, 0, $elen, _CHRSET_);
				}else{
					$data[] = $s;
				}
			}			
		}
		if(count($data)>0){
			$this->keywords = array_unique($data);
		}
	}

	#@ return
	# keywords 값 배열 리턴
	public function get_keywords(){
		return $this->keywords;
	}
}
?>