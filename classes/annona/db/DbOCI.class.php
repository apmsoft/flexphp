<?php
namespace Flex\Annona\Db;

use \ArrayAccess;
use \ErrorException;
use Flex\Annona\Db\DbOCIResult;
use Flex\Annona\Db\DbInterface;

# parnet : DbOCIResult
# purpose : oci 함수를 활용해 확장한다
class DbOCI extends OCIResult implements DbInterface,ArrayAccess
{
	const CHARSET = 'utf-8';
	private $host,$dbname;
	private $handle;
	private $affected_rows;	# 저장 레코드 갯수
	private $changes_rows;	# 수정된 레코드 갯수
	private $params = array();
	private $oci_autocommit = OCI_COMMIT_ON_SUCCESS;

	# dsn : host:dbname = localhost:dbname
    public function __construct($dsn='',$user='',$passwd='',$chrset='utf8'){
        if(!empty($dsn)){
			$dsn_args = explode(':',$dsn);
			$this->handle = oci_connect($user,$passwd,$dsn);
        }else{//config.inc.php > config.db.php
			$dsn_args[] = _DB_HOST_;
			$dsn_args[] = _DB_NAME_;
			$dsn = (_DB_HOST_) ? _DB_HOST_._DB_NAME_ : _DB_NAME_;
			$this->handle = oci_connect(_DB_USER_,_DB_PASSWD_,$dsn);
		}

        if (!$this->handle) {
			$e = oci_error();
			throw new ErrorException(htmlentities($e['message'].' '.$e['sqltext']),$e['message']);
		}

		$this->host		= $dsn_args[0];
		$this->dbname	= $dsn_args[1];
    }

	#@ interface : ArrayAccess
	# 사용법 : $obj["two"] = "A value";
    public function offsetSet($offset, $value) {
        $this->params[$offset] = $this->oci_escape_string($value);
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

    #@ return int
	# 총게시물 갯수 추출
	public function get_total_record(string $table, string $where="") : int{
		$wh = ($where) ? " WHERE ".$where : '';
		if($result = $this->query("SELECT count(*) FROM ".$table." ".$wh)){
			$row = $result->fetch_row();
			return $row[0];
		}
	return 0;
	}

	#@ return int
	# 총게시물 쿼리문에 의한 갯수 추출
	public function get_total_query(string $qry) : int{
		if($result =$this->query($qry)){
			$row = $result->fetch_row();
			return $row[0];
		}
	return 0;
	}

	# return boolean | array
	# 하나의 레코드 값을 가져오기
	public function get_record(string $field, string $table, string $where) : bool|Array{
		$where = ($where) ? " WHERE ".$where : '';
		$qry = "SELECT ".$field." FROM ".$table." ".$where;
		if($result = $this->query($qry)){
			$row = $result->fetch_assoc();
			return $row;
		}
	return false;
	}

    # @ interface : DBSwitch
	public function query(string $query, int $result_mode = NULL) : mixed{
		$result = oci_parse($this->handle,$query);
        if( !$result ){
			$e = oci_error();
			throw new ErrorException(htmlentities($e['message'].' '.$e['sqltext']),$e['message']);
        }

        if(!oci_execute($result)){  
			$e = oci_error($result);  
			throw new ErrorException(htmlentities($e['message'].' '.$e['sqltext']),$e['message']);
		}

        $this->num_rows = oci_num_rows($result);
        $this->resultHandle = $result;
    return $this;
    }

	# @ interface : DBSwitch
	# args = array(key => value)
	# args['name'] = 1, args['age'] = 2;
	public function insert($table) : bool{
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

	# 프라퍼티 값 가져오기
	public function __get($propertyName){
		if(property_exists(__CLASS__,$propertyName)){
			return $this->{$propertyName};
		}
	}

    # insert,update,delete 에 사용
	public function exec($query){
		$result = oci_parse($this->handle,$query);
		if( !$result ){
			$e = oci_error();
			throw new ErrorException(htmlentities($e['message'].' '.$e['sqltext']),$e['message']);
        }

        if(!oci_execute($result, $this->oci_autocommit)){  
			$e = oci_error($result);

			// rollback
			if($this->oci_autocommit == OCI_NO_AUTO_COMMIT){
				oci_rollback($this->handle);
			}
			throw new ErrorException(htmlentities($e['message'].' '.$e['sqltext']),$e['message']);
		}
		oci_free_statement($result);
    }

    # set autocommit
    # @flag : true | false
    function autocommit($flag){
		$this->oci_autocommit = ($flag) ? OCI_COMMIT_ON_SUCCESS : OCI_NO_AUTO_COMMIT;
    }

    # commit
    function commit(){
		$result = oci_commit($this->handle);
		if (!$result) {
			$e = oci_error($this->handle);
			throw new ErrorException(htmlentities($e['message'].' '.$e['sqltext']),$e['message']);
		}
    }

    function oci_escape_string($string) {
		return str_replace(array('"', "'", '\\'), array('\\"', '\\\'', '\\\\'), $string);
	}

    # 버전정보
    public function server_info(){
		return oci_server_version($this->handle);
    }

    # db close
    public function __destruct(){
		oci_close($this->handle);
    }
}
?>
