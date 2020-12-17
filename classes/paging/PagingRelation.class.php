<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 1.2.1
----------------------------------------------------------*/
namespace Flex\Paging;

class PagingRelation
{
	private $url;						# 이동주소
	private $urlQuery;					# 주소쿼리
	private $urlQueryArray;				# 배열
	private $page           = 1;		# 현제 페이지
	private $totalPage      = 0;		# 총페이지
	private $qLimitStart    = 0;		# query LIMIT [0],[]
	private $qLimitEnd      = 0;		# query LIMIT [],[0]
	private $totalBlock     = 0;
	private $blockCount     = 0;
	private $blockLimit		= 10;
	private $blockStartPage = 0;		# 블록 시작페이지
	private $blockEndPage   = 0;		# 블록 끝페이지
	private $pageLimit      = 0;
	private $totalRecord    = 0;		# 총레코드 수
	
	private $relation = array(
		'link'    =>'',
		'first'   =>0,
		'pre'     =>0,
		'next'    =>0,
		'last'    =>0
	);
	private $relation_current = array();

	/**1
	 * 필요한 기본값 등록
	 * @param $url			: 기본경로(./list.php || list.php?a=1&b=2)
	 * @param $totalRecord	: 총 레코드 갯수
	 * @param $page			: 요청 페이지
	 */
	public function __construct($url, $totalRecord, $page){
		$this->url = $url;
		$this->totalRecord	= $totalRecord;
		$this->page			= (!empty($page)) ? $page : 1;
	}

	# 2 한페이지에 출력할 레코드 갯수
	public function setQueryCount($pagecount=10, $blockLimit=10){
		$this->blockLimit =$blockLimit;
		$this->totalPage  =@ceil($this->totalRecord/$pagecount);

		if($this->totalRecord ==0){
			$this->qLimitStart =0;
			$this->qLimitEnd   =0;
		}else{
			$this->qLimitStart =$pagecount * ($this->page-1);
			$this->qLimitEnd   =$pagecount;
		}

		$this->totalBlock     =ceil($this->totalPage/$this->blockLimit);
		$this->blockCount     =ceil($this->page/$this->blockLimit); // 현재속해 있는 block count
		$this->blockStartPage =($this->blockCount-1) * $this->blockLimit;
		$this->blockEndPage   =$this->blockCount*$this->blockLimit;

		if($this->totalBlock <=$this->blockCount) {
			$this->blockEndPage = $this->totalPage;
		}
// Out::prints_ln('totalBlock : '.$this->totalBlock);
// Out::prints_ln('blockCount : '.$this->blockCount);
// Out::prints_ln('blockStartPage : '.$this->blockStartPage);
// Out::prints_ln('blockEndPage : '.$this->blockEndPage);
		$this->pageLimit = $pagecount;
	}

	/** 3
	 * @void
	 * url 뒤에 붙일 http query 값
	 * @param $params
	 * @param $numeric_prefix
	 */
	public function setBuildQuery($params='',$numeric_prefix='')
	{
		# 배열
		if(is_array($params) && count($params)>0){
			foreach($params as $pk=>$pv){
				if(!$pv){
					unset($params[$pk]);
				}else{
					$this->urlQueryArray[$pk]=$pv;
				}
			}

			$this->urlQuery = (count($this->urlQueryArray)>0) ? http_build_query($this->urlQueryArray, $numeric_prefix) : '';
		}

		if(strpos($this->url,'?') !==false) $this->url.= $this->urlQuery;
		else $this->url.= '?'.$this->urlQuery;
	}

	#@ 4 void
	# 출력
	public function buildPageRelation()
	{
		$this->relation['link'] = str_replace('page='.$this->page,'',$this->url);
		self::rewindPage();
		self::prevPage();
		self::currentPage();
		self::nextPage();
		self::lastPage();
	}

	#@void 현재페이지 출력
	public function currentPage()
	{
		$s_page =$this->blockStartPage + 1;
		for($i = $s_page; $i<=$this->blockEndPage; $i++)
		{
			$this->relation_current[] = array(
				'link'=>$this->relation['link'].'&page='.$i,
				'num'=>$i
			);
		}
	}

	#@void  이전페이지
	public function prevPage(){
		if($this->blockCount > 1){
			$this->relation['pre'] = $this->blockStartPage;
		}
	}

	#@void  다음페이지
	public function nextPage(){
		if($this->blockCount< $this->totalBlock){
			$this->relation['next'] = $this->blockEndPage + 1;
		}
	}

	#@void  처음페이지
	public function rewindPage(){
		if($this->page > 1 && $this->blockCount>1){
			$this->relation['first'] = 1;
		}
	}

	#@void 마지막페이지
	public function lastPage(){
		if($this->totalBlock == $this->blockCount){
			$this->relation['last'] = 0;
		}else{
			$this->relation['last'] = $this->totalPage;
		}
	}

	# 프라퍼티 값 포함한 가져오기
	public function __get($propertyName){
		if(property_exists(__CLASS__,$propertyName)){
			$result = $this->{$propertyName};
			if($propertyName=='totalPage'){
				if($result==0){
					$result = 1;
				}
			}
		return $result;
		}
	}

	# 프라퍼티 값 변경하기
	public function __set($propertyName, $valuez){
		if(property_exists(__CLASS__,$propertyName)){
			return $this->{$propertyName} = $valuez;
		}
	}

	#@ array
	#[link] => ?
    #[first] => 0
    #[pre] => 0
    #[next] => 3
    #[last] => 5
    #[chanel] => Array(
    #    [0] => Array(
    #            [link] => ?&page=1
    #            [num] => 1
    #    )
    #)
    #페이징 채널 배열 출력
	public function printRelation(){
		$result = array_merge($this->relation,array('chanel'=>$this->relation_current));
		// Out::prints_r($result);
	return $result;
	}
}
?>
