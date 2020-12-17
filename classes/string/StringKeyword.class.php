<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @UPDATE	: 1.1.1
----------------------------------------------------------*/
namespace Flex\String;

# purpose : 문자을 변경하거나 더하거나 등 가공하는 역할을 한다.
class StringKeyword
{
	private $keywords;
	protected $filter_words = array
	(
		'할수'=>'','있습'=>'','니다'=>'','있나'=>'','있나요'=>'','하세요'=>'','되나'=>'','하는데'=>'','정해져'=>'','이루어'=>'','집니다'=>'',
		'이제'=>'','만들'=>'','시켜야'=>'','언제나'=>'','그렇듯'=>'','그래'=>'','그리고'=>'','그러나'=>'','하지만'=>'','시키면'=>'','있는'=>'','처럼'=>'','시킬때'=>'','있다'=>'','정하다'=>'','정해진'=>'','습니다'=>'','보세요'=>''
	);
	protected $filter_end_words = array(
		'가','이','은','는'
	);

	public function __construct($keyword){
		if($keyword && $keyword !=''){
			$this->keywords=self::cleanWord($keyword);
		}
	}

	# 특수문자 제거 및 단어별 배열로 리턴
	# return array()
	private function cleanWord($keywords)
	{
		// $keywords = preg_replace("/[ #\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i",' ',$keywords);
		// $keywords = preg_replace('/\s\s+/', ' ', $match[0]); // 연속된 공백을 하나로 제거
		# 한글 영어 숫자만 추출
		preg_match_all('/([\xEA-\xED][\x80-\xBF]{2}|[a-zA-Z0-9])+/', $keywords, $match);
		$keywords = $match[0];
		if(is_array($keywords)){
			foreach($keywords as $n => $w){
				# 영어만 추출
				preg_match_all("|(?<eng>[A-Za-z]+)|su", $w, $out);
				$eng = $out['eng'][0];
				if($eng){
					// echo $eng;
					# 발견된 단어에서 영어만 삭제
					$keywords[$n] = strtr($w, array($eng=>''));
					# 발견된 영단어 추가
					$keywords[] = $eng;
				}
			}
		}		

		// $keywords = implode(' ',$argv);
		// $keywords = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/",$argv, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		return array_unique($keywords);
	}

	# 분리된 단어중에서 필터 단어로 등록된 단어 지운 후 리턴
	public function filterCleanWord(){
		$argv = $this->keywords;
		$data = array();
		foreach($argv as $w){
			$s = strtr($w, $this->filter_words);
			if($s && $s !=''){
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