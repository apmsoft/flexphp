<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 1.3.1
----------------------------------------------------------*/
namespace Fus3\Db;

# purpose : 각종 SQL 관련 디비를 통일성있게  작성할 수 있도록 틀을 제공
interface DbSwitch
{
    public function query($query);			# 쿼리
    public function insert($table);			# 저장
    public function update($table,$where);	# 수정
    public function delete($table,$where);	# 삭제
    #public function query_binding($query, $args=array());
}
?>
