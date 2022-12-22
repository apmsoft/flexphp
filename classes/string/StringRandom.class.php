<?php
namespace Flex\String;

# purpose : 랜덤문자 만들기
class StringRandom {
	protected array $regs = [
		'A',1,'B',2,'C',3,'D',4,'E',5,'F',6,'G',7,'H',8,'A',9,'J',9,
		'K',1,'L',2,'M',3,'N',4,'A',5,'P',6,'Q',7,'R',8,'S',9,'T',7,
		'U',1,'V',2,'X',3,'Y',4,'Z'
	];

	protected array $int_regs=array(0,1,2,3,4,5,6,7,8,9);

	public function __construct(array $regs){
		if(is_array($regs) && count($regs)){
			$this->regs = $regs;
		}
	return $this;
	}

	# 숫자로 정해진 범위의 숫자로 난수를 만드어 낸다
	# min : 시작범위, max : 끝범위
	# 리턴 길이 1
	public function numberRand(int $min=0,int $max=9) : int
	{
		mt_srand((double) microtime()*1000000);
		$result = mt_rand($min,$max);
	return $result;
	}

	# 배열중에서 갯수 만큼 추출해 내기
	public function arrayRand(int $length=1) : string
	{
		$result = '';
		srand((double) microtime()*1000000);
		$array_keys = array_rand($this->regs,$length);

		$cnt = count($array_keys);
		for($i=0; $i<$cnt; $i++){
			$result .= $this->regs[$array_keys[$i]];
		}
	return $result;
	}

	# 숫자 배열중에서 갯수 만큼 추출해 내기
	public function arrayIntRand($length=1) : int
	{
		$result = '';
		srand((double) microtime()*1000000);
		$array_keys = array_rand($this->int_regs,$length);

		$cnt = count($array_keys);
		for($i=0; $i<$cnt; $i++){
			$result .= $this->int_regs[$array_keys[$i]];
		}
	return $result;
	}
}
?>
