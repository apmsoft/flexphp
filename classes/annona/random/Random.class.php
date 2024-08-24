<?php
namespace Flex\Annona\Random;

# purpose : 랜덤문자 만들기
class Random {
	public const __version = '0.7';
	protected array $specialChars = ['!', '@', '#', '$', '*', '_', '-'];
	protected array $characters = [];

	public function __construct(array $characters = []){
		if(is_array($characters) && count($characters)){
			$this->characters = $characters;
		}else{
			if(!count($this->characters)){
				$this->characters = array_merge(range('A', 'Z'), range(0, 9));
			}
		}
	}

	# 숫자로 정해진 범위의 숫자로 난수를 만드어 낸다
	# min : 시작범위, max : 끝범위
	# 리턴 길이 
	public function number(int $min=0,int $max=9, int $length=1) : int
	{
		$result = '';
		// 첫 번째 숫자는 0이 아닌 숫자로 시작
		$result = (string)random_int(max(1, $min), $max);

		for($i=1; $i<$length; $i++){
			$result .= random_int($min,$max);
		}

	return (int) $result;
	}

	# 특수문자 랜덤
	public function createSpecialChars(int $length = 1): string {
        $result = '';
        $charCount = count($this->specialChars);

        for ($i = 0; $i < $length; $i++) {
            $result .= $this->specialChars[random_int(0, $charCount - 1)];
        }
        return $result;
    }

	# 배열중에서 갯수 만큼 추출해 내기
	public function array(int $length=1, bool $includeSpecialChars = false) : string
	{
		$result = '';

		# 특수문자 포함 여부
		if($includeSpecialChars){
			$specialCharsCount = count($this->specialChars);
			$this->characters = array_merge($this->characters, $this->createSpecialChars( $specialCharsCount ));
		}

		# 갯수만큼 추출
		$array_keys = ($length==1) ? [array_rand($this->characters,$length)] : array_rand($this->characters,$length);

		# 결과
		$cnt = count($array_keys);
		for($i=0; $i<$cnt; $i++){
			$result .= $this->characters[$array_keys[$i]];
		}
	return $result;
	}
}
?>
