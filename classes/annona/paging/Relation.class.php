<?php
namespace Flex\Annona\Paging;

use Flex\Annona;

class Relation
{
	public $version         = '2.0';
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
	
	private $relation = ['first'=> 0,'pre'=> 0,'next'=> 0,'last'=> 0];
	private $relation_current = [];

	/**1
	 * 필요한 기본값 등록
	 * @param $totalRecord	: 총 레코드 갯수
	 * @param $page			: 요청 페이지
	 */
	public function __construct(int $totalRecord, int $page){
		$this->totalRecord	= $totalRecord;
		$this->page			= (!empty($page)) ? $page : 1;
	}

	# 2 한페이지에 출력할 레코드 갯수
	public function setQueryCount(int $pagecount=10, int $blockLimit=10)
	{
		$this->blockLimit =$blockLimit;
		$this->totalPage  =@ceil($this->totalRecord/$pagecount);

		if($this->totalRecord ==0){
			$this->qLimitStart =0;
			$this->qLimitEnd   =0;
		}else{
			$this->qLimitStart =$pagecount * ($this->page-1);
			$this->qLimitEnd   =$pagecount;
		}

		$this->totalBlock     = ceil($this->totalPage/$this->blockLimit);
		$this->blockCount     = ceil($this->page/$this->blockLimit); // 현재속해 있는 block count
		$this->blockStartPage = ($this->blockCount-1) * $this->blockLimit;
		$this->blockEndPage   = $this->blockCount*$this->blockLimit;

		if($this->totalBlock <=$this->blockCount) {
			$this->blockEndPage = $this->totalPage;
		}
		
		$this->pageLimit = $pagecount;
	}

	#@ 3
	# 출력
	public function buildPageRelation() : void
	{
		$this->rewindPage();
		$this->prevPage();
		$this->currentPage();
		$this->nextPage();
		$this->lastPage();
	}

	#@void 현재페이지 출력
	public function currentPage()
	{
		$s_page =$this->blockStartPage + 1;
		for($i = $s_page; $i<=$this->blockEndPage; $i++)
		{
			$this->relation_current[] = $i;
		}
	}

	#이전페이지
	public function prevPage() : void{
		if($this->page > 1 && $this->page <= $this->totalPage){
			$this->relation['pre'] = $this->page -1;
		}
	}

	#다음페이지
	public function nextPage() : void{
		if($this->page >0 && $this->page < $this->totalPage){
			$this->relation['next'] = $this->page + 1;
		}
	}

	#처음페이지
	public function rewindPage() : void{
		if($this->page > 1 && $this->page <= $this->totalPage){
			$this->relation['first'] = 1;
		}
	}

	#마지막페이지
	public function lastPage() : void{
		if($this->page > 0 && $this->page <= ($this->totalPage-1)){
			$this->relation['last'] = $this->totalPage;
		}else{
			$this->relation['last'] = 0;
		}
	}

	# 프라퍼티 값 포함한 가져오기
	public function __get($propertyName){
		if(property_exists(__CLASS__,$propertyName)){
			$result = $this->{$propertyName};
			if($propertyName =='totalPage'){
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
    #first : 0
    #pre : 0
    #next : 3
    #last : 5
    #chanel : [1,2,3]
    #페이징 채널 배열 출력
	public function printRelation() : array{
		$result = array_merge($this->relation,array('chanel'=>$this->relation_current));
	return $result;
	}
}
?>
