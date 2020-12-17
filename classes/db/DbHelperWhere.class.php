<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: https://www.fancyupsoft.com
| @Editor	: VSCode
| @UPDATE	: 1.4
----------------------------------------------------------*/
namespace Flex\Db;

use Flex\String\StringKeyword;

# 데이터베이스 QUERY구문에 사용되는 WHERE문 만드는데 도움을 주는 클래스
class DbHelperWhere extends StringKeyword
{
	private $where = '';
	private $where_or = array();
	private $where_and= array();

	# void
	# @fields : name+category+area 복수필드
	# @coord : [AND | OR]
	public function __construct($fields='', $keyword='', $coord='OR')
	{
		if($fields && $fields !='')
		{
			parent::__construct($keyword);
			$filterd_keyword = parent::get_keywords();
			if($filterd_keyword && $filterd_keyword !=''){
				self::setSentencesWhereByKeyword($fields, $filterd_keyword, $coord);
			}
		}
	}

	# void
	# 키워드를 가지고 선택된 필드로 디비 검색 문장을 만든다
	private function setSentencesWhereByKeyword($fields, $keywords, $coord)
	{
		// where문장 만들 배열값
		// $where_argv = array();

		// field 필터링
		$field_args = array();
		$fields = (strpos($fields, '+') !==false) ? str_replace('+', ' ', $fields) : $fields;
		if(strpos($fields, ' ') !== false) $field_args = explode(' ',$fields);
		else $field_args[] = trim($fields);

		// 키워드
		if(is_array($keywords))
		{
			$cntKeyword=count($keywords);	# 키워드 갯수
			$cntField = count($field_args);	# 필드 갯수

			for($i=0; $i<$cntField; $i++)
			{
				for($j=0; $j<$cntKeyword; $j++){
					if($keywords[$j]){
						$field_name=''; 
						$field_name = $field_args[$i];
						if(strpos($field_name,'.')!==false){ // join 문
							if($coord == 'OR'){
								$this->where_or[] = " ".$field_name." LIKE '%".$keywords[$j]."%' ";
							}else if($coord == 'AND'){
								$this->where_and[] = " ".$field_name." LIKE '%".$keywords[$j]."%' ";
							}
						}
						else{
							if($coord == 'OR'){
								$this->where_or[] = " `".$field_name."` LIKE '%".$keywords[$j]."%' ";
							}else if($coord == 'AND'){
								$this->where_and[] = " `".$field_name."` LIKE '%".$keywords[$j]."%' ";
							}
						}
					}
				}
			}
		}
	}

	# void
	# 구문어를 만든다.
	# @where_str : name='홍길동'
	# @condition : [=,!=,<,>,<=,>=]
	# @coord : [AND | OR]
	# @value : NULL | VALUE | %
	# @is_append : 필수 적용
	public function setBuildWhere($field_name, $condition ,$value, $coord='AND',$is_append=false)
	{
		if(!$is_append){
			if($value && $value !=''){
				$is_append = true;
			}
		}
		
		if($is_append)
		{
			$_field_name=''; 
			$_field_name = (strpos($field_name,'.')!==false) ? $field_name : "`".$field_name."`";
			if(strcmp($value, strtoupper('NULL'))){
				if(strtoupper($condition) == 'LIKE'){
					$where_str = sprintf("%s LIKE '%s'", $_field_name, $value);
				}else{
					$where_str = sprintf("%s %s '%s'", $_field_name, trim($condition), $value);
				}
			}

			$coord = strtoupper($coord);
			if($coord == 'OR'){
				$this->where_or[] = $where_str;
			}else if($coord == 'AND'){
				$this->where_and[] = $where_str;
			}
		}
	}

	# 상속한 부모 프라퍼티 값 포함한 가져오기
	public function __get($propertyName){
		if(property_exists(__CLASS__,$propertyName))
		{
			if($propertyName=='where'){
				$where_and = '';	
				if(count($this->where_and)){
					$where_and = implode(' AND ', $this->where_and);
				}

				$where_or = '';
				if(count($this->where_or)){
					$where_or = implode(' OR ', $this->where_or);
				}

				if($where_and && $where_or){
					$this->where = '('.$where_and.') AND ('.$where_or.')';
				}else if($where_and && !$where_or){
					$this->where = $where_and;
				}else if($where_or && !$where_and){
					$this->where = '('.$where_or.')';
				}
			}
			
			return $this->{$propertyName};
		}
	}
}
?>
