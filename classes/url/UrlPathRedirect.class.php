<?php
/** ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Eclipse(default)
| @UPDATE	: 1.0.1
----------------------------------------------------------*/
namespace Flex\Url;

# 특정 URL 주소 길이를 줄이고 풀경로도 다시 이동 시키는 목적으로 사용한다.
# 모든 호스팅에 redirect 설정이 되어있는것이 아니라 기본 index.php나 index.html을 기준으로 설정한다.
# http://sensetouch.kr/?shop/200/q=1&sm=s23
# 홈페이지 주소           /?구분아이디/파라메터값
class UrlPathRedirect
{
	private $query_id     = '';				# 줄임 주소에서 프로그램실행 구분키
	private $query_params = array();		# query_id 별 파라메터
	private $base_url     = '';				# 기본 접속 경로
	private $basename     = 'index.php';	# 기본 접속 페이지
	private $pathinfo     = array();		# 사용자에 의해 설정된 query_id별 설정경로
	private $query_string = '';				# 주소 $_SERVER['QUERY_STRING']값

	# void
	# 추가 기본 경로 지정
	# $urlPathRedirect = new UrlPathRedirect(_SRC_);
	public function __construct($base_directory)
	{
		# http://sensetouch.kr/?shop/200/q=1&sm=s23
		$this->query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING']: '';
		if(!empty($this->query_string))
		{
			$this->query_params = preg_split('/\//', $this->query_string, -1, PREG_SPLIT_NO_EMPTY);

			# 첫번째 문자는 query_id 를 의미한다.
			$this->query_id = $this->query_params[0];
			unset($this->query_params[0]);

			# 쿼리 파라메터용 인수를 재 배열한다.
			# [0]=200, [1]=]q=1&sm=s23
			if(is_array($this->query_params)){
				$qp_argv=array();
				$qp_argv =&$this->query_params;
				$this->query_params = array();
				foreach($qp_argv as $qp_value)
					$this->query_params[] = $qp_value;
			}
		}

		# 기본 경로 만들기
		if(isset($_SERVER['DOCUMENT_ROOT'])){
			$this->base_url = str_replace($_SERVER['DOCUMENT_ROOT'],'',_ROOT_PATH_);
		}
		
		if(!empty($base_directory))
			$this->base_url.= $base_directory;
	}

	#@ void
	# 기본페이지 파일명 설정 확장자 포함
	# ex) index.php, index.ax, mail.html
	public function setBasename($basename){
		if(!empty($basename)){
			$this->basename = $basename;
		}
	}

	#@ void
	# member = 'member.php';
	# $urlPathRedirect->setPathInfo('shop','index_shop.php');
	public function setPathInfo($k,$path){
		if(!isset($this->pathinfo[$k]) || !$this->pathinfo[$k])
			$this->pathinfo[$k] = $path;
	}

	# return array
	# [0]=200, [1]=]q=1&sm=s23
	public function getQueryParams(){
		$argv = array();
		if(is_array($this->query_params)){
			if(count($this->query_params)>0)
				$argv = $this->query_params;
		}
	return $argv;
	}

	#@ return string
	private function getPathInfoByKey($k){
		$result = false;
		if(isset($this->pathinfo[$k]))
			$result = $this->pathinfo[$k];

	return $result;
	}

	#@ return string|array
	public function __get($propertyName){
		$result = '';
		if(property_exists(__CLASS__,$propertyName)){
			switch($propertyName){
				case 'query_string':
				case 'query_id' :
				$result = $this->{$propertyName};
				break;
			}
		}
	return $result;
	}

	#@ return String
	#src/shop/index_shop.php
	public function getRedirectUrl()
	{
		$redirect_url = '';
		$redirect_url = $this->base_url.'/'.$this->basename;
		if($this->query_id){
			$id_pathinfo = self::getPathInfoByKey($this->query_id);
			if($id_pathinfo)
				$redirect_url = $this->base_url.'/'.$id_pathinfo;
		}
	return $redirect_url;
	}
}
?>