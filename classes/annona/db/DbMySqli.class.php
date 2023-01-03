<?php
namespace Flex\Annona\Db;

use Flex\Annona\Db\QueryBuilderAbstract;
use Flex\Annona\Request\Validation;
use \ArrayAccess;
use \ErrorException;

class DbMySqli extends QueryBuilderAbstract implements DbInterface,ArrayAccess
{
	private $version = '2.0.2';

	# 암호화 / 복호화
	const BLOCK_ENCRYPTION_MODE = "aes-256-cbc";	#AES
	const RANDOM_BYTES          = 16;

	private $params = [];

	# dsn : host:dbname = localhost:dbname
	public function __construct(string $dsn='',string $user='',string $passwd='', int $port=3306, string $chrset='utf8')
	{
		# 데이타베이스 접속
		if(!empty($dsn)){
			$dsn_args = explode(':',$dsn);
			parent::__construct($dsn_args[0],$user,$passwd,$dsn_args[1],$port);
		}else{//config.inc.php > config.db.php
			parent::__construct(_DB_HOST_,_DB_USER_,_DB_PASSWD_,_DB_NAME_, _DB_PORT_);
		}

		if (mysqli_connect_error()){
			throw new ErrorException(mysqli_connect_error(),mysqli_connect_errno());
		}

		# 문자셋
		$chrset_is = parent::character_set_name();
		if(strcmp($chrset_is,$chrset)) parent::set_charset($chrset);
	
	return $this;
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

	# db 암호화 초기 셋팅 설정
	public function set_encryption_mode() : void
	{
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

	#@ 암호화
	private function aes_encrypt(mixed $v) : string
	{
		$result = '';
		if($v && $v !=''){
			$result = sprintf("(HEX( AES_ENCRYPT( '%s',  SHA2('%s',512), RANDOM_BYTES(%d)) ))",
				parent::real_escape_string($v), _DB_SHA2_ENCRYPT_KEY_, self::RANDOM_BYTES
			);
		}
		return $result;
	}

	# 복호화
	private function aes_decrypt(string $column_name,bool $is_as =true) : string
	{
		$result = '';
		if($column_name && $column_name !='')
		{
			if(!$is_as){
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

	# @ abstract : QueryBuilderAbstract
	public function table(...$tables) : DbMySqli{
		parent::init('MAIN');
		$length = count($tables);
		$value = ($length ==2) ? implode(',',$tables) : implode(' ',$tables);
		parent::set('table', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
	public function tableSub(...$tables) : DbMySqli{
		parent::init('SUB');
		$length = count($tables);
		$value = ($length ==2) ? implode(',',$tables) : implode(' ',$tables);
		parent::set('table', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
	public function tableJoin(string $join, ...$tables) : DbMySqli{
		parent::init('JOIN');

		$upcase = strtoupper($join);
		$implode_join = sprintf(" %s JOIN ",$upcase);
		switch($upcase){
			case 'UNION': # 중복제거
			case 'UNION ALL': # 중복포함
				parent::setQueryTpl('UNINON');
				$implode_join = sprintf(" %s ",$upcase);
				break;
			default : 
				parent::setQueryTpl('default');
		}

		$value = implode($implode_join, $tables);
		parent::set('table', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function select(...$columns) : DbMySqli{
		$value = implode(',', $columns);
		parent::set('columns', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function selectGroupBy(...$columns) : DbMySqli{
		$argv = [];
		foreach($columns as $name){
			$argv[] = (strpos($name,'(') !==false) ? $name : sprintf("ANY_VALUE(%s) as %s",$name,$name);
		}
		$value = implode(',', $argv);
		parent::set('columns', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
	# select 암호화 -> 복호화 
	public function selectCrypt(...$columns) : DbMySqli{
		$argv = [];
		foreach($columns as $name){
			$validation = new Validation($name);
			if($validation->isNumber()){ 
				$argv[] = $name;
			}else{
				$argv[] = self::aes_decrypt($name,false);
			}
		}
		$value = implode(',', $argv);
		parent::set('columns', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function where(...$where) : DbMySqli
	{
		$result = parent::buildWhere($where);
		if($result){
			$value = 'WHERE '.$result;
			parent::set('where', $value);
		}
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function orderBy(...$orderby) : DbMySqli
	{
		$value = 'ORDER BY '.implode(',',$orderby);
		parent::set('orderby', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function on(...$on) : DbMySqli
	{
		$result = parent::buildWhere($on);
		if($result){
			$value = 'ON '.$result;
			parent::set('on', $value);
		}
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function limit(...$limit) : DbMySqli{
		$value = 'LIMIT '.implode(',',$limit);
		parent::set('limit', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function distinct(string $column_name) : DbMySqli{
		$value = sprintf("DISTINCT %s", $column_name);
		parent::set('columns', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function groupBy(...$columns) : DbMySqli{
		$value = 'GROUP BY '.implode(',',$columns);
		parent::set('groupby', $value);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function having(...$having) : DbMySqli{
		$result = parent::buildWhere($having);
		if($result){
			$value = 'HAVING '.$result;
			parent::set('having', $value);
		}
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function total(string $column_name = '*') : int
	{
		$total = 0;
		$value = sprintf("count(%s)", $column_name);
		parent::set('columns', $value);
		$query = parent::get();
		if($result = parent::query($query)){
			$row   = $result->fetch_row();
			$total = $row[0];
		}
	return $total;
	}

	# @ interface : DBSwitch
	public function query(string $query='', mixed $result_mode = MYSQLI_STORE_RESULT) : mixed{
		if(!$query) $query = $this->query = parent::get();

		$result = parent::query($query, $result_mode);
		if(!$result){
			throw new ErrorException(mysqli_error($this).' '.$query,mysqli_errno($this));
		}
	return $result;
	}

	# @ interface : DBSwitch
	# $db['name'] = 1, $db['age'] = 2;
	public function insert() : bool
	{
		$result = false;
		$fieldk = '';
		$datav	= '';
		if(count($this->params)<1) return $result;

		foreach($this->params as $k => $v)
		{
			$fieldk .= sprintf("`%s`,",$k);
			$datav .= sprintf("'%s',", parent::real_escape_string($v));
		}

		$fieldk	= substr($fieldk,0,-1);
		$datav	= substr($datav,0,-1);
		$this->params = array(); #변수값 초기화

		$query= sprintf("INSERT INTO `%s` (%s) VALUES (%s)",$this->query_params['table'],$fieldk,$datav);
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	# 암호화 저장
	public function insertEncrypt() : bool
	{
		$result = false;
		$fieldk = '';
		$datav	= '';
		if(count($this->params)<1) return $result;

		foreach($this->params as $k => $v)
		{
			$fieldk .= sprintf("`%s`,",$k);
			$validation = new Validation($v);
			if($validation->isNumber()){ // 숫자만 있으면 인코딩 안함
				$datav .= sprintf("'%s',", parent::real_escape_string($v));
			}else{
				$datav .= sprintf("%s,", $this->aes_encrypt(parent::real_escape_string($v)) );
			}
		}

		$fieldk	= substr($fieldk,0,-1);
		$datav	= substr($datav,0,-1);
		$this->params = array(); #변수값 초기화

		$query= sprintf("INSERT INTO `%s` (%s) VALUES (%s)",$this->query_params['table'],$fieldk,$datav);
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	# @ interface : DBSwitch
	public function update() : bool
	{
		$result = false;
		$fieldkv = '';

		if(count($this->params)<1) return $result;		
		foreach($this->params as $k => $v)
		{
			$datav = '';
			$pattern = sprintf("/(%s)(\+|\-|\*|\/)([0-9])/i", $k);
			if(preg_match($pattern, $v)){
				$datav = sprintf("%s", parent::real_escape_string($v));
			}else{
				$datav = sprintf("'%s'", parent::real_escape_string($v));
			}
			$fieldkv .= sprintf("`%s`=%s,",$k, $datav);
		}
		$fieldkv = substr($fieldkv,0,-1);
		$this->params = array(); #변수값 초기화

		$query= sprintf("UPDATE `%s` SET %s %s",$this->query_params['table'],$fieldkv,$this->query_params['where']);s
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	# 암호화 업데이트
	public function updateEncrypt() : bool
	{
		$result = false;
		$fieldkv = '';

		if(count($this->params)<1) return $result;		
		foreach($this->params as $k => $v)
		{
			$datav = '';
			$validation = new Validation($v);
			if($validation->isNumber()){ // 숫자만 있으면 인코딩 안함
				$datav = sprintf("'%s'", parent::real_escape_string($v));
			}else{
				$datav = sprintf("%s", $this->aes_encrypt(parent::real_escape_string($v)) );
			}
			$fieldkv .= sprintf("`%s`=%s,",$k, $datav);
		}
		$fieldkv = substr($fieldkv,0,-1);
		$this->params = array(); #변수값 초기화

		$query= sprintf("UPDATE `%s` SET %s %s",$this->query_params['table'],$fieldkv,$this->query_params['where']);
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	# @ interface : DBSwitch
	public function delete() : bool
	{
		$result = false;
		$query = sprintf("DELETE FROM `%s` %s",$this->query_params['table'],$this->query_params['where']);
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	public function __call($method, $args){
		if(method_exists($this, $method)){
			if($method == 'aes_decrypt' || $method == 'aes_encrypt'){
				return call_user_func_array(array($this, $method),$args);
			}
		}
	}

	# 상속한 부모 프라퍼티 값 포함한 가져오기
	public function __get(string $propertyName) : mixed{
		if(property_exists(__CLASS__,$propertyName)){
			if($propertyName == 'query'){
				return parent::get();
			}else{
				return $this->{$propertyName};
			}
		}
	}

	# db close
	public function __destruct(){
		parent::close();
	}
}
?>
