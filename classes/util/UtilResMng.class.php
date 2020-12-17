<?php
/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://apmsoft.tistory.com
| @Editor   : Sublime Text 3
| @version  : 0.4.1
----------------------------------------------------------*/ 
namespace Flex\Util;

#config.json 컨트롤
class UtilResMng extends PreferenceInternalStorage
{
	const w_permission	= 0707;
	const o_permission	= 0755;
	private $old_umask;

	#@ void
	public function __construct( $file_name, $mode )
	{
		// $this->old_umask = umask(000);
		parent::__construct ( $file_name, $mode );
	}

	#@ boolean
	public function flushResource($context){
		
		// if(!@chmod($this->file_name, self::w_permission)) return false;

		if( parent::writeInternalStorage ( $context ) ==0){
			return false;
		}
		
		// @chmod($this->file_name, self::o_permission);
		// @umask($this->old_umask);

		// if ($this->old_umask != umask()) {
		//     return false;
		// }
	return true;
	}
}
?>
