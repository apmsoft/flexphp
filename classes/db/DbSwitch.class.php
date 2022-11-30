<?php
namespace Flex\Db;

# purpose : 각종 SQL 관련 디비를 통일성있게  작성할 수 있도록 틀을 제공
interface DbSwitch
{
    public function query(string $query, int $result_mode = MYSQLI_STORE_RESULT) : mixed;			# 쿼리
    public function insert(string $table) : bool;			# 저장
    public function update(string $table,string $where) : bool;	# 수정
    public function delete(string $table,string $where) : bool;	# 삭제
}
?>
