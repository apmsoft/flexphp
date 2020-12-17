<?php
namespace Fus3\Util;

/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://apmsoft.tistory.com
| @Editor   : Sublime Text 3
| @UPDATE   : 0.5
----------------------------------------------------------*/
# create
# $uuid = UtilUUID::create();

# 사용자별 uuid 만들기
# $uuid = UtilUUID::make('fancy-up-php');

# 사용자 uuid 확인
# $uuid = UtilUUID::make('fancy-up-php','a246dc5f-5923-5eb7-a40e-b10a6c648f10');
class UtilUUID extends UtilUUIDGenerator
{
	#@ String
	public static function create(){
		return parent::v4();
	}

	#@ String | boolean
	public static function make($str=null, $uuid=null, $v='v5')
	{
		if(is_null($str) || !$str)
			return false;

		if(is_null($uuid) || !$uuid)
			$uuid = self::create();

		switch ($v) {
			case 'v3':
				return parent::v3($uuid, $str);
				break;

			case 'v5':
				return parent::v5($uuid, $str);
				break;
		}
	}

	#@ String
	# uniqid
	public static function create_uniqid(){
		return microtime(true).'.'.uniqid('', true);
	}
}
?>