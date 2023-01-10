<?php
namespace Flex\Annona\Db;

# purpose : 각종 SQL 관련 디비를 통일성있게  작성할 수 있도록 틀을 제공
interface DbInterface
{
    public function query(string $query, mixed $result_mode) : mixed;			# 쿼리
    public function insert() : bool;			# 저장
    public function update() : bool;	# 수정
    public function delete() : bool;	# 삭제
}
?>
