<?php
namespace Flex\Annona\Db;

# purpose : oci 함수를 활용해 확장한다
class DbOCIResult
{
	public const __version = '0.5';
	protected $resultHandle;
	protected $num_rows;		# 쿼리 레코드 갯수
    
	# fetch : assoc
	public function fetch_assoc(){
		$args = array();
		$args = oci_fetch_assoc($this->resultHandle);
	return $args;
	}
	
	# fetch : row
	public function fetch_row(){
		$args = array();
		$args = oci_fetch_row($this->resultHandle);
	return $args;
	}
	
	# fetch : array
	public function fetch_array(){
		$args = array();
		$args = oci_fetch_array($this->resultHandle,OCI_BOTH);
	return $args;
	}
    
	# 필드이름 가져오기
	public function columnName(){
		$args = array();
		$field_num = oci_num_fields($this->resultHandle);
		for($i=0; $i<$field_num; $i++){
			$args[] = oci_field_name($this->resultHandle,$i);
		}
	return $args;
	}

	# 리소스 반납
	public function free(){
		oci_free_statement($this->resultHandle);
	}
	
	#칼럼과 타입
	public function columnTypes(){
		$args = array();
		$field_num = oci_num_fields($this->resultHandle);
		for($i=0; $i<$field_num; $i++){
			$args[] = oci_field_type($this->resultHandle,$i);
		}
	return $args;
	}
}
?>
