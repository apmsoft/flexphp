<?php
use Flex\String\StringUtil;

# 문자 자르기(UTF-8)
# 슬래쉬, HTML 태그 제거
function str_cut(string $str, int $lenth, bool $is_apeend_cutstr = true, string $strip_tags = '<font><strong><b><strike>'){
	$stringUtil = new StringUtil($str);
	$stringUtil->cut($lenth, $is_apeend_cutstr, $strip_tags);
	return $stringUtil->str;
}

# 숫자 포멧
# 1588-1234 | 010-1234-5678 | 1234-5678-1234-5678 |
function formatNumberPrintf(string $str ,string $glue='-'){
	$stringUtil6 = new \Flex\String\StringUtil( $str );
	$stringUtil6->formatNumberPrintf( $glue );
	return $stringUtil6->str;
}
?>