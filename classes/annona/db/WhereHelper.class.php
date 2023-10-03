<?php
namespace Flex\Annona\Db;

# 데이터베이스 QUERY구문에 사용되는 WHERE문 만드는데 도움을 주는 클래스
class WhereHelper
{
	private $version = '1.7';
	private $where = '';
	private $where_group = [];
	private $current_group = '';
	private $current_coord = '';
	private $where_groups_data = [];

	# void
	# @fields : name+category+area 복수필드
	# @coord : [AND | OR]
	public function __construct()
	{
		$this->where = '';
		self::init();
	}

	# void
	# 구문어를 만든다.
	# @where_str : name='홍길동'
	# @condition : [=,!=,<,>,<=,>=,IN,LIKE-R=dd%,LIKE-L=%dd,LIKE=%dd%]
	# @value : NULL | VALUE | % | Array
	public function case(string $field_name, string $condition ,mixed $value, bool $is_qutawrap=true, bool $join_detection=true) : WhereHelper
	{
		$is_append = false;
		if($value == "0") $is_append = true;
		else if($value && $value !=''){
			$is_append = true;
		}

		# where 문을 그룹별로 묶기
		if($is_append)
		{
			$in_value = [];
			if (is_array($value)){ // array
				$in_value = $value;
			} else if (strpos($value, ",") !==false){
				$in_value = explode(',', $value);
			} else{
				$in_value[] = $value;
			}

			$_uppper_condition = strtoupper($condition);
			if($_uppper_condition == 'LIKE' || $_uppper_condition == 'LIKE-R' || $_uppper_condition == 'LIKE-L'){
				foreach($in_value as $n => $word)
				{
					// $_word = preg_replace("/[#\&\+\-%@=\/\\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/i",' ',$word);
					$_word = preg_replace("/[#\&\+\-%@=\/\\\:;,\.'\"\^`~|\!\?\*$#<>()\[\]\{\}]/i",' ',$word);

					// append
					$this->where_group[$this->current_group][] = match($_uppper_condition) {
						'LIKE' => sprintf("%s LIKE '%%%s%%'", $field_name, $_word),
						'LIKE-R' => sprintf("%s LIKE '%s%%'", $field_name, $_word),
						'LIKE-L' => sprintf("%s LIKE '%%%s'", $field_name, $_word),
					};
				}
			}
			else if($_uppper_condition == 'IN'){
				if(strpos($in_value[0],'.') !==false){
					$in_value_str = implode ( ",", $in_value );
				}else{
					$in_value_str = ($is_qutawrap) ? "'" . implode ( "', '", $in_value ) . "'" : implode ( ",", $in_value );
				}

				// append
				$this->where_group[$this->current_group][] = sprintf("%s IN (%s)", $field_name, $in_value_str);
			}
			else if($_uppper_condition == 'JSON_CONTAINS'){
				$in_value_str = json_encode($in_value, JSON_UNESCAPED_UNICODE);

				// append
				$this->where_group[$this->current_group][] = sprintf("JSON_CONTAINS(%s, '%s')", $field_name, $in_value_str);
			}
			else if($value == 'NULL'){
				// append
				$this->where_group[$this->current_group][] = sprintf("%s %s %s", $field_name, $condition, $value);
			}
			else{
				// set "a.name 형태인지 체크"
				$__value__ = ($is_qutawrap) ? sprintf("'%s'",$in_value[0]) : $in_value[0];
				$d_value = sprintf("%s %s %s", $field_name, $condition, $__value__);
				if($join_detection)
				{
					$pattern = "/^([a-zA-Z0-9]|_)+(\.)([a-zA-Z0-9]|_)/i";
					if(preg_match($pattern, $in_value[0])){
						$d_value = sprintf("%s %s %s", $field_name, $condition, $in_value[0]);
					}
				}

				$this->where_group[$this->current_group][] = $d_value;
			}
		}
	return $this;
	}

	# 상속한 부모 프라퍼티 값 포함한 가져오기
	public function __get($propertyName) : string{
		if(property_exists(__CLASS__,$propertyName))
		{
			if($propertyName == 'where'){
				$this->where = (count($this->where_groups_data)) ? "(" . implode ( ") AND (", $this->where_groups_data ) . ")" : '';
				self::init();
			}

			return $this->{$propertyName};
		}
	}

	private function init() : void {
		// reset
		$this->current_group = '';
		$this->current_coord = '';
		$this->where_group   = [];
		$this->where_groups_data = [];
	}

	public function fetch() : array
	{
		$result = $this->where_group;
		self::init();
	return $result;
	}

	# where 그룹묶기 시작
	public function begin(string $coord) : WhereHelper
	{
		$groupname = strtr(microtime(),[' '=>'','0.'=>'w']);
		$this->where_group[$groupname] = [];

		# 현재그룹 시작
		$this->current_group = $groupname;
		$this->current_coord = $coord;
	return $this;
	}

	# where 그룹묶기 종료
	public function end() : WhereHelper{
		if(count($this->where_group[$this->current_group])){
			$wher_str = implode(sprintf(" %s ", $this->current_coord), $this->where_group[$this->current_group]);
			$this->where_groups_data[] = $wher_str;
		}

		# 현재그룹 시작
		$this->current_group = '';
		$this->current_coord = '';
	return $this;
	}

	public function __destruct(){
		$this->where         = '';
		$this->current_group = '';
		$this->current_coord = '';
		$this->where_group   = [];
		$this->where_groups_data = [];
	}
}
?>
