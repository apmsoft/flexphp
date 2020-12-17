<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: apmsoft.tistory.com
| @Editor	: Sublime Text3
| @UPDATE	: 1.4.3
----------------------------------------------------------*/
namespace Flex\Db;

use Flex\R\R;
use Flex\Req\ReqStrChecker;
use \MySQLi;
use \ArrayAccess;
use \ErrorException;

# Parent : MySqli
# Parent : DBSwitch
# purpose : mysqli을 활용해 확장한다
class DbMySqli extends mysqli implements DbSwitch,ArrayAccess
{
	# 암호화 / 복호화
	const BLOCK_ENCRYPTION_MODE = "aes-256-cbc";	#AES
	const RANDOM_BYTES          = 16;
	private $encryption_enable = false;	# 암호화/복호화 활성화

	private $params = array();

	# dsn : host:dbname = localhost:dbname
	public function __construct($dsn='',$user='',$passwd='',$chrset='utf8')
	{
		# 데이타베이스 접속
		if(!empty($dsn)){
			$dsn_args = explode(':',$dsn);
			parent::__construct($dsn_args[0],$user,$passwd,$dsn_args[1]);
		}else{//config.inc.php > config.db.php
			parent::__construct(_DB_HOST_,_DB_USER_,_DB_PASSWD_,_DB_NAME_);
		}

		if (mysqli_connect_error()){
			throw new ErrorException(mysqli_connect_error(),mysqli_connect_errno());
		}

		# 문자셋
		$chrset_is = parent::character_set_name();
		if(strcmp($chrset_is,$chrset)) parent::set_charset($chrset);
	}

	#@ interface : ArrayAccess
	# 사용법 : $obj["two"] = "A value";
	public function offsetSet($offset, $value) {
		$this->params[$offset] = $value;
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

	#@ void
	public function set_encryption_mode(){
			# 서버 버전
		$mysql_version = $this->server_version;
		if($mysql_version < 50700){
			$err_msg = sprintf("Setting(%s) is not possible.",self::BLOCK_ENCRYPTION_MODE);
			throw new ErrorException($err_msg);
		}

		# encryption_mode 확인
		$encryption_mode_qry = sprintf("SELECT @@session.block_encryption_mode as em");
		$encryption_row = self::get_record_assoc($encryption_mode_qry);
		if(isset($encryption_row['em'])){
			if($encryption_row['em'] != $encryption_mode){
				$set_encrypt_qry = sprintf("SET @@session.block_encryption_mode = '%s'", self::BLOCK_ENCRYPTION_MODE);
				self::query($set_encrypt_qry);
			}
		}
	}

	#@ void
	# 암호화/복호화 쿼리 활성화
	public function begin_encrypt(){
		$this->encryption_enable = true;
	}

	#@ void
	# 암호화/복호화 쿼리 비활성
	public function end_encrypt(){
		$this->encryption_enable = false;
	}

	#@ string
	private function aes_encrypt($v){
		$result = '';
		if($v && $v !=''){
			$result = sprintf("(HEX( AES_ENCRYPT( '%s',  SHA2('%s',512), RANDOM_BYTES(%d)) ))",
				parent::real_escape_string($v), _DB_SHA2_ENCRYPT_KEY_, self::RANDOM_BYTES
			);
		}
		return $result;
	}

	private function aes_decrypt($column_name, $is_as=true){
		$result = '';
		if($column_name && $column_name !=''){
			if($is_as){
				$result = sprintf("(CONVERT( AES_DECRYPT(UNHEX(%s), SHA2('%s',512), RANDOM_BYTES(%d)) USING utf8)) as %s", 
					$column_name, _DB_SHA2_ENCRYPT_KEY_, self::RANDOM_BYTES, $column_name
				);
			}else{
				$result = sprintf("(CONVERT( AES_DECRYPT(UNHEX(%s), SHA2('%s',512), RANDOM_BYTES(%d)) USING utf8))", 
					$column_name, _DB_SHA2_ENCRYPT_KEY_, self::RANDOM_BYTES
				);
			}
		}
		return $result;
	}

	#@ return int
	# 총게시물 갯수 추출
	public function get_total_record($table, $where=""){
		$wh = ($where) ? " WHERE ".$where : '';
		if($result = parent::query("SELECT count(*) FROM `".$table."`".$wh)){
			$row = $result->fetch_row();
			return $row[0];
		}
	return 0;
	}

	#@ return int
	# 총게시물 쿼리문에 의한 갯수 추출
	public function get_total_query($qry){
		if($result = parent::query($qry)){
			$row = $result->fetch_row();
			return $row[0];
		}
	return 0;
	}

	# return boolean | array
	# 하나의 레코드 값을 완성된 쿼리문을 받아 가져오기
	public function get_record_assoc($qry){
		if($result = $this->query($qry)){
			$row = $result->fetch_assoc();
			return $row;
		}
	return false;
	}

	# return boolean | array
	# 하나의 레코드 값을 가져오기
	public function get_record($field, $table, $where){
		$where = ($where) ? " WHERE ".$where : '';
		$qry = "SELECT ".$field." FROM `".$table."` ".$where;
		if($result = $this->query($qry)){
			$row = $result->fetch_assoc();
			return $row;
		}
	return false;
	}

	# @ interface : DBSwitch
	public function query($query){
		$result = parent::query($query);
		if(!$result){
			if(PHP_VERSION_ID>50300){
				throw new ErrorException(mysqli_error($this).' '.$query,mysqli_errno($this));
			}else{
				#throw new ErrorException(mysqli_error(&$this).' '.$query,mysqli_errno(&$this));
			}
		}
	return $result;
	}

	# @ interface : DBSwitch
	# args = array(key => value)
	# args['name'] = 1, args['age'] = 2;
	public function insert($table){
		$fieldk = '';
		$datav	= '';
		if(count($this->params)<1) return false;
		foreach($this->params as $k => $v){
			$fieldk .= sprintf("`%s`,",$k);			
			if($this->encryption_enable){
				$isChceker = new ReqStrChecker($v);
				if($isChceker->isNumber()){ // 숫자만 있으면 인코딩 안함
					$datav .= sprintf("'%s',", parent::real_escape_string($v));
				}else{
					$datav .= sprintf("%s,", $this->aes_encrypt(parent::real_escape_string($v)) );
				}
			}else{
				$datav .= sprintf("'%s',", parent::real_escape_string($v));
			}
		}
		$fieldk	= substr($fieldk,0,-1);
		$datav	= substr($datav,0,-1);
		$this->params = array(); #변수값 초기화

		$query= sprintf("INSERT INTO `%s` (%s) VALUES (%s)",$table,$fieldk,$datav);
		$this->query($query);
	}

	# @ interface : DBSwitch
	public function update($table,$where)
	{
		$fieldkv = '';

		if(count($this->params)<1) return false;
		
		foreach($this->params as $k => $v){
			$datav = '';
			if($this->encryption_enable){
				$isChceker = new ReqStrChecker($v);
				if($isChceker->isNumber()){ // 숫자만 있으면 인코딩 안함
					$datav = sprintf("'%s'", parent::real_escape_string($v));
				}else{
					$datav = sprintf("%s", $this->aes_encrypt(parent::real_escape_string($v)) );
				}
			}else{
				$datav = sprintf("'%s'", parent::real_escape_string($v));
			}
			$fieldkv .= sprintf("`%s`=%s,",$k, $datav);
		}
		$fieldkv = substr($fieldkv,0,-1);
		$this->params = array(); #변수값 초기화

		$query= sprintf("UPDATE `%s` SET %s WHERE %s",$table,$fieldkv,$where);
		$this->query($query);
	}

	# @ interface : DBSwitch
	public function delete($table,$where){
		$query = sprintf("DELETE FROM `%s` WHERE %s",$table,$where);
		$this->query($query);
	}

	#@ array
	#테이블에 속한 필드 명=>필드 type
	#http://dev.mysql.com/doc/refman/5.0/en/columns-table.html
	public function show_columns($table){
		$columns = array();
		$rlt = $this->query(sprintf("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name ='%s'", $table));
		while($row = $rlt->fetch_row()){
			$field_name =$row[0];
			$field_type =$row[1];
			$columns[$field_name] =$field_type;
		}
	return $columns;
	}

	public function __call($method, $args){
		if(method_exists($this, $method)){
			if($method == 'aes_decrypt' || $method == 'aes_encrypt'){
            	return call_user_func_array(array($this, $method),$args);
			}
		}
	}

	# 상속한 부모 프라퍼티 값 포함한 가져오기
	public function __get($propertyName){
		if(property_exists(__CLASS__,$propertyName)){
			return $this->{$propertyName};
		}
	}

	# db close
	public function __destruct(){
		parent::close();
	}
}
?>
