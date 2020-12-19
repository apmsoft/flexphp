<?php
namespace Flex\Db;

# 데이터베이스 QUERY구문에 사용되는 WHERE문 만드는데 도움을 주는 클래스
class DbHelperWhere
{
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
	}

	# void
	# 구문어를 만든다.
	# @where_str : name='홍길동'
	# @condition : [=,!=,<,>,<=,>=,IN]
	# @value : NULL | VALUE | % | Array
	# @is_append : 필수 적용
	public function setBuildWhere(string $field_name, string $condition ,int|string|Array|NULL $value ,bool $is_append=false) : void
	{
		if(!$is_append){
			if($value && $value !=''){
				$is_append = true;
			}
		}

		# where 문을 그룹별로 묶기		
		if($is_append)
		{
			$_field_name=''; 
			$_field_name = (strpos($field_name,'.')!==false) ? $field_name : "`".$field_name."`";
			if(is_array($value) || strcmp($value, strtoupper('NULL')))
			{
				if(strtoupper($condition) == 'LIKE'){
					$where_str = sprintf("%s LIKE '%s'", $_field_name, $value);
				} 
				else if(strtoupper($condition) == 'IN'){
					$in_value =[];
					if (is_array($value)){
						$in_value = $value;
					} else if (strpos($value, ",") !==false){
						$in_value = explode(',', $value);
					} else{
						$in_value[] = $value;
					}
					$in_value_str = "'" . implode ( "', '", $in_value ) . "'";
					$where_str = sprintf("%s IN (%s)", $_field_name, $in_value_str);
				} 
				else{
					$where_str = sprintf("%s %s '%s'", $_field_name, $condition, $value);
				}
			}

			$this->where_group[$this->current_group][] = $where_str;
		}
	}

	# 상속한 부모 프라퍼티 값 포함한 가져오기
	public function __get($propertyName) : string{
		if(property_exists(__CLASS__,$propertyName))
		{
			if($propertyName == 'where')
			{
				$this->where = (count($this->where_groups_data)) ? "(" . implode ( ") AND (", $this->where_groups_data ) . ")" : '';
			}
			
			return $this->{$propertyName};
		}
	}

	# where 그룹묶기 시작
	public function beginWhereGroup(string $groupname, string $coord) : void{
		if ( !isset( $this->where_group[$groupname] ) ){
			$this->where_group[$groupname] = [];
		}

		# 현재그룹 시작
		$this->current_group = $groupname;
		$this->current_coord = $coord;
	}

	# where 그룹묶기 종료
	public function endWhereGroup() : void{
		// out_ln ( $this->current_group );
		// out_ln ( $this->current_coord );
		if(count($this->where_group[$this->current_group])){
			// out_r ($this->where_group[$this->current_group] );
			$wher_str = implode(sprintf(" %s ", $this->current_coord), $this->where_group[$this->current_group]);
			// out_ln($wher_str);
			$this->where_groups_data[] = $wher_str;
		}

		# 현재그룹 시작
		$this->current_group = '';
		$this->current_coord = '';
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
