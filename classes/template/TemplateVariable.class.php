<?php
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://www.apmsoftax.com
| @Editor	: Eclipse(default)
| @UPDATE	: 1.0
----------------------------------------------------------*/
namespace Flex\Template;

use \SplFileInfo;

# purpose : 템블릿 컴파일 및 템플릿 클래스에서 사용할 변수
class TemplateVariable extends SplFileInfo{
	/**
	 * @var var_ : template에서 사용할 데이타
	 * @var includes :인클루드 선언된 파일 목록
	 */
	public $var_=array();
	protected $includes=array();
	protected $file_path_arg=array();
	public $import_js=array();
	public $import_css=array();
}
?>