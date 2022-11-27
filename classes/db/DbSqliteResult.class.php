<?php
namespace Flex\Db;

# purpose : sqlite 함수를 활용해 확장한다
class DbSqliteResult
{
	protected $resultHandle;
	protected $num_rows;		# 쿼리 레코드 갯수
    
	# fetch : assoc
	public function fetch_assoc(){
		$args = array();
		$args = sqlite_fetch_array($this->resultHandle,SQLITE_ASSOC);
	return $args; 
	}
	
	# fetch : row
	public function fetch_row(){
		$args = array();
		$args = sqlite_fetch_array($this->resultHandle,SQLITE_NUM);
	return $args;
	}
	
	# fetch : array
	public function fetch_array(){
		$args = array();
		$args = sqlite_fetch_array($this->resultHandle,SQLITE_BOTH);
	return $args;
	}
    
	# 필드이름 가져오기
	public function columnName(){
		$args = array();
		$field_num = sqlite_num_fields($this->resultHandle);
		for($i=0; $i<$field_num; $i++){
			$args[] = sqlite_field_name($this->resultHandle,$i);
		}
	return $args;
	}
	
	#칼럼과 타입
	public function columnTypes($table){
		$args = array();
		$args = sqlite_fetch_column_types($table,$this->handle,SQLITE_ASSOC);
	return $args;
	}
}
?>
