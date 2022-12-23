<?php
namespace Flex\Annona\Db;

use \ArrayAccess;
use \ErrorException;

# parnet : DBSqliteResult
# purpose : sqlite 함수를 활용해 확장한다
class DbSqlite extends DbSqliteResult implements DbInterface,ArrayAccess
{
	const CHARSET = 'utf-8';
	private $host,$dbname;
	private $handle;
	private $affected_rows;	# 저장 레코드 갯수
	private $changes_rows;	# 수정된 레코드 갯수
	private $params = array();

	# dsn : 파일경로:파일명
    public function __construct($dsn){
        $dsn_args = explode(':',$dsn);

        $this->handle = sqlite_open($dsn_args[0].'/'.$dsn_args[1]);
        if($errno_num = sqlite_last_error($this->handle)){
			throw new ErrorException(sqlite_error_string($errno_num),$errno_num);
        }

		$this->host		= $dsn_args[0];
		$this->dbname	= $dsn_args[1];
    }

	#@ interface : ArrayAccess
	# 사용법 : $obj["two"] = "A value";
    public function offsetSet($offset, $value) {
        $this->params[$offset] = sqlite_escape_string($value);
    }

    #@ interface : ArrayAccess
    # 사용법 : isset($obj["two"]); -> bool(true)
	public function offsetExists($offset) {
        return isset($this->params[$offset]);
    }

    #@ interface : ArrayAccess
    # 사용법 : unset($obj["two"]); -> bool(false)
	public function offsetUnset($offset) {
        unset($this->params[$offset]);
    }

    #@ interface : ArrayAccess
    # 사용법 : $obj["two"]; -> string(7) "A value"
	public function offsetGet($offset) {
        return isset($this->params[$offset]) ? $this->params[$offset] : null;
    }

    # @ interface : DBSwitch
	public function query($query){
		$result = sqlite_query($this->handle,$query);
        if( !$result ){
			$errno_num = sqlite_last_error($this->handle);
			throw new ErrorException(sqlite_error_string($errno_num),$errno_num);
        }
        $this->num_rows = sqlite_num_rows($result);
        $this->resultHandle = $result;
    return $this;
    }

	# @ interface : DBSwitch
	# args = array(key => value)
	# args['name'] = 1, args['age'] = 2;
	public function insert($table){
		$fieldk = '';
		$datav	= '';

		if(count($this->params)<1) return false;
		foreach($this->params as $k => $v){
			$fieldk .= sprintf("%s,",$k);
			$datav	.= sprintf("'%s',",$v);
		}
		$fieldk	= substr($fieldk,0,-1);
		$datav	= substr($datav,0,-1);
		$this->params = array(); #변수값 초기화

		$query	= sprintf("INSERT INTO %s (%s) VALUES (%s)",$table,$fieldk,$datav);
		$this->exec($query);
	}

	# @ interface : DBSwitch
	public function update($table,$where){
		$fieldkv = '';

		if(count($this->params)<1) return false;
		foreach($this->params as $k => $v){
			$fieldkv .= sprintf("%s='%s',",$k,$v);
		}
		$fieldkv = substr($fieldkv,0,-1);
		$this->params = array(); #변수값 초기화

		$query	= sprintf("UPDATE %s SET %s WHERE %s",$table,$fieldkv,$where);
		$this->exec($query);
	}

	# @ interface : DBSwitch
    public function delete($table,$where){
		$query = sprintf("DELETE FROM %s WHERE %s",$table,$where);
		$this->exec($query);
    }

	# 디비 선택
    public function select_db($dbname){
		$this->handle = sqlite_factory($this->host.'/'.$dbname);

		if($errno_num = sqlite_last_error($this->handle)){
			throw new ErrorException(sqlite_error_string($errno_num),$errno_num);
        }

        $this->dbname= $dbname;
    }

	# 프라퍼티 값 가져오기
	public function __get($property){
		return $this->{$property};
	}

    # insert,update,delete 에 사용
	public function exec($query){
		$result = sqlite_exec($this->handle,$query,$error);
		if(!$result){
			throw new ErrorException("Error in query: '.$error.'");
		}else{
			$this->changes_rows = sqlite_changes($this->handle);
		}
    }

    # 저장한 마지막 primary_key 값
    public function insert_id(){
		return sqlite_last_insert_rowid($this->handle);
    }

    # 버전정보
    public function server_info(){
		return sqlite_libversion();
    }

    # 문자셋 정보
	public function character_set_name(){
		return sqlite_libencoding();
	}

    # db close
    public function __destruct(){
		sqlite_close($this->handle);
    }
}
?>
