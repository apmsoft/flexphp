<?php
namespace Flex\Annona\Db;

use Flex\Annona\R;
use Flex\Annona\Request\Validation;
use Flex\Annona\Db\QueryBuilderAbstract;
use Flex\Annona\Db\WhereHelper;
use \ArrayAccess;
use \ErrorException;

# Parent : MySqli
# Parent : DbInterface
# purpose : mysqli을 활용해 확장한다
class DbMySqli extends QueryBuilderAbstract implements DbInterface,ArrayAccess
{
	# 암호화 / 복호화
	const BLOCK_ENCRYPTION_MODE = "aes-256-cbc";	#AES
	const RANDOM_BYTES          = 16;
	private $encryption_enable = false;	# 암호화/복호화 활성화

	private $params = [];

	# dsn : host:dbname = localhost:dbname
	public function __construct(string $dsn='',string $user='',string $passwd='', int $port=3306, string $chrset='utf8')
	{
		# 데이타베이스 접속
		// if(!empty($dsn)){
		// 	$dsn_args = explode(':',$dsn);
		// 	parent::__construct($dsn_args[0],$user,$passwd,$dsn_args[1],$port);
		// }else{//config.inc.php > config.db.php
		// 	parent::__construct(_DB_HOST_,_DB_USER_,_DB_PASSWD_,_DB_NAME_, _DB_PORT_);
		// }

		// if (mysqli_connect_error()){
		// 	throw new ErrorException(mysqli_connect_error(),mysqli_connect_errno());
		// }

		// # 문자셋
		// $chrset_is = parent::character_set_name();
		// if(strcmp($chrset_is,$chrset)) parent::set_charset($chrset);
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

	# 암호화/복호화 쿼리 활성화
	public function begin_encrypt() : void{
		$this->encryption_enable = true;
	}

	# 암호화/복호화 쿼리 비활성
	public function end_encrypt() : void{
		$this->encryption_enable = false;
	}

	#@ string
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

	private function aes_decrypt($column_name, $is_as=true) : string
	{
		$result = '';
		if($column_name && $column_name !='')
		{
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

	# @ abstract : QueryBuilderAbstract
	public function table(...$table) : DbMySqli{
		parent::init();
		$this->query_params['table'] = implode(',', $table);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function select(...$columns) : DbMySqli{
		$this->query_params['columns'] = implode(',', $columns);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function selectGroupBy(...$columns) : DbMySqli{
		$argv = [];
		foreach($columns as $name){
			if(strpos($name,'(') !==false){
				$argv[] = $name;
			}else{ $argv[] = sprintf("ANY_VALUE(%s)",$name); }
		}
		$this->query_params['columns'] = implode(',', $argv);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function where(...$where) : DbMySqli
	{
		$length = count($where);
		if($length > 0)
		{
			if(isset($where[0]) && trim($where[0]))
			{
				$this->query_params['where'] = 'WHERE '.$where[0];
				if($length > 1)
				{
					$whereHelper = new \Flex\Annona\Db\WhereHelper();
					$whereHelper->beginWhereGroup(time(), 'AND');
					if($length ==2){
						$whereHelper->setBuildWhere($where[0], '=' , $where[1], true);
					}else if($length ==3){
						$whereHelper->setBuildWhere($where[0], $where[1] , $where[2], true);
					}
					$whereHelper->endWhereGroup();
					$this->query_params['where'] = 'WHERE '.$whereHelper->where;
				}
			}
		}
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function orderBy(...$orderby) : DbMySqli
	{
		$_by = [];
		if(count($orderby) > 2){
			foreach($orderby as $idx => $name){
				if($idx>0 && ($idx % 2) ==1){
					$pre_idx = $idx -1;
					$pre_name = $orderby[$pre_idx];
					$_by[] = implode(' ',[$pre_name,$name]);
				}
			}
		}else{
			$_by = $orderby;
		}
		$this->query_params['orderby'] = 'ORDER BY '.implode(',',$_by);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function on(...$on) : DbMySqli{
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function limit(...$limit) : DbMySqli{
		$this->query_params['limit'] = 'LIMIT '.implode(',',$limit);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function distinct(string $column_name) : DbMySqli{
		$this->query_params['columns'] = sprintf("DISTINCT %s", $column_name);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function groupBy(...$columns) : DbMySqli{
		$this->query_params['groupby'] = 'GROUP BY '.implode(',',$columns);
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function having(...$having) : DbMySqli{
		$length = count($having);
		if($length > 0)
		{
			if(isset($having[0]) && trim($having[0]))
			{
				$this->query_params['having'] = 'HAVING '.$having[0];
				if($length > 1)
				{
					$whereHelper = new \Flex\Annona\Db\WhereHelper();
					$whereHelper->beginWhereGroup(time(), 'AND');
					if($length ==2){
						$whereHelper->setBuildWhere($having[0], '=' , $having[1], true);
					}else if($length ==3){
						$whereHelper->setBuildWhere($having[0], $having[1] , $having[2], true);
					}
					$whereHelper->endWhereGroup();
					$this->query_params['having'] = 'HAVING '.$whereHelper->where;
				}
			}
		}
	return $this;
	}

	# @ abstract : QueryBuilderAbstract
    public function total(string $column_name = '*') : int
	{
		$total = 0;
		$this->query_params['columns'] = sprintf("count(%s)", $column_name);
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
			if($this->encryption_enable)
			{
				$validation = new Validation($v);
				if($validation->isNumber()){ // 숫자만 있으면 인코딩 안함
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
			if($this->encryption_enable){
				$validation = new Validation($v);
				if($validation->isNumber()){ // 숫자만 있으면 인코딩 안함
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

		$query= sprintf("UPDATE `%s` SET %s WHERE %s",$this->query_params['table'],$fieldkv,$this->query_params['table']);
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	# @ interface : DBSwitch
	public function delete() : bool
	{
		$result = false;
		$query = sprintf("DELETE FROM `%s` WHERE %s",$this->query_params['table'],$this->query_params['table']);
		if($this->query($query)){
			$result = true;
		}
	return $result;
	}

	public function __call($method, $args) : mixed{
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
		#parent::close();
	}
}
?>
