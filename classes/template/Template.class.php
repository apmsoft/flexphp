<?php
#include_once 'TemplateCompiler.class.php';
/** ======================================================
| @Author	: 김종관 | 010-4023-7046
| @Email	: apmsoft@gmail.com
| @HomePage	: http://fancy-up.tistory.com
| @Editor	: Sublime Text 3
| @version  : 1.3.2
----------------------------------------------------------*/
namespace Fus3\Template;

use \ArrayAccess;
use \ErrorException;
use Fus3\Dir\DirInfo;
use Fus3\R\R;

# purpose : MVC 패턴목적, 디자인과 프로그램의 분리
class Template extends TemplateCompiler implements ArrayAccess
{
	/**
	* var filename;	# 파일명
	* var filemtime;# 파일수정시간
	*/
	private $filename;
	private $filemtime= 0;

	/**
	* @var compile_ext	: 파일 저장 확장자명
	* @var permission	: 폴더 권한
	*/
	const compile_ext	= 'php';
	const permission	= 0644;

	/**
	 * @var compile 	: true 강제실행, false 자동
	 * @var compile_dir	: 컴파일 경로
	 * @var safemode	: true php태그코딩지우기, false 유지
	 * @var chgimgsrc	: true 경로변경, false 사용자코딩 유지
     * @var compression : 소스코드 압축, false 소스코드 상태 유지
	 */
	private $compile           = false;
	private $compile_dir       = '';
	protected $safemode        = true;
	protected $chgimgpath      = false;
	protected $compression     = true;
	protected $compression_tag = '';

	# 처음 실행
	public function __construct($filename)
	{
		# 파일 체크
		parent::__construct($filename);
		$this->filename=parent::getRealPath();
		if(!self::isExists($this->filename)) throw new ErrorException(R::$sysmsg['e_filenotfound']);
        $this->file_path_arg  = explode(DIRECTORY_SEPARATOR,parent::getPath());
		$this->filemtime=parent::getMTime();

		# 기본경로 설정
		$this->compile_dir=$_SERVER['DOCUMENT_ROOT'];

		# abstract
		self::setDefinedConstants();
		self::setDefinedFunctions();
	}

	#@ void abstract
	# user가 선언한 define 변수
	protected function setDefinedConstants(){
		$const = get_defined_constants(true);
		if(is_array($const['user'])){
			$this->defines = array_keys($const['user']);
		}
	}

	#@ void abstract
	# 사용가능 함수 : 사용자함수포함 사용하고자 하는 함수 목록을 등록하면 됨
	protected function setDefinedFunctions(){
		if(function_exists('get_defined_functions')){
			$splfunargs=get_defined_functions();
			$this->funs=array_merge($splfunargs['user'],$this->funs);
			$this->funs=array_unique($this->funs);
		}
	}

	#@ void abstract
	#내장함 수 활성화(사용자 정의 함수는 자동 등록됨)
	public function enableBuiltInFunction($funtion_name){
		#함수명 문자로 입력했는지 체크
        if(!is_string($funtion_name)){
            $funtion_name = strval($funtion_name);
        }

        # 함수 목록에 있는지 체크
        if(!in_array($funtion_name, $this->funs))
        {
            # 함수 인지 체크
            if (function_exists($funtion_name)) {
                parent::$this->funs[] = $funtion_name;
            } else {
                throw new ErrorException(__CLASS__.' :: '.__LINE__.' '.strval($funtion_name).' functions are not available');
            }
        }
	}

	#@ interface : ArrayAccess
	public function offsetSet($offset, $value){
		if(is_array($value)){
			if(isset($this->variables[$offset])) $this->var_[$offset] = array_merge($this->var_[$offset],$value);
			else $this->var_[$offset] = $value;
		}
		else{ $this->var_[$offset] = $value; }
	}

	#@ interface : ArrayAccess
	public function offsetExists($offset){
		if(isset($this->var_[$offset])) return isset($this->var_[$offset]);
		else return isset($this->var_[$offset]);
	}

	#@ interface : ArrayAccess
	public function offsetUnset($offset){
		if(self::offsetExist($offset)) unset($this->var_[$offset]);
		else unset($this->var_[$offset]);
	}

	#@ interface : ArrayAccess
	public function offsetGet($offset) {
		return isset($this->var_[$offset]) ? $this->var_[$offset] : $this->var_[$offset];
	}

	# 컴파일된 파일 만들기
	private function makeCompileFile($filename,$source){
		if(!is_resource($fp=fopen($filename,"w"))) return false;
		if(!fwrite($fp,$source)) return false;
		if(!fclose($fp)) return false;

		try{
			#if(!chmod($filename,self::permission)) return false;
			#if(!@chown($filename,getmyuid())) return false;
		}catch(Exception $e){

		}

	return true;
	}

	# 폴더 만들기
	private function makeDirs($newdirname){
		$result = true;

		$dirObj = new DirInfo($newdirname);
		$dirObj->makesDir();
	return $result;
	}

	# 로컬 파일인지 체크
	public function isExists($filename){
		if(!file_exists($filename)) return false;
	return true;
	}

	# 프라퍼티 값 리턴받기
	public function __get($propertyName){
		if(property_exists(__CLASS__,$propertyName)){
			return $this->{$propertyName};
		}
	}

	# compile,cache,chgimgpath 설정변경
	public function __set($name,$value){
		switch($name){
			case 'compile':
			case 'compile_dir':
			case 'chgimgpath':
			case 'safemode':
				$this->{$name} = $value;
				break;
            case 'compression':
                $this->compression = $value;
                if(!$value) $this->compression_tag = '';
                else $this->compression_tag = "\n";
                break;
		}
	}

	#@ void
	# import 파일들 환경설정용 inc.php 파일 만들기
	private function make_import_resource_incfile($files)
	{
		if(is_array($files) && count($files)>0)
		{
			// $_inc_name = $this->compile_dir.DIRECTORY_SEPARATOR.str_replace(array('.html','.htm','.tpl'),'.include.inc.'.self::compile_ext,basename($this->filename));
			$_inc_name = $this->compile_dir.DIRECTORY_SEPARATOR.strtr(basename($this->filename), array(
				'.html' =>'.include.inc.'.self::compile_ext,
				'.htm'  =>'.include.inc.'.self::compile_ext,
				'.tpl'  =>'.include.inc.'.self::compile_ext
			));
			$source = '<?php'."\n";
			$source.= '$include_args = array();'."\n";
			foreach($files as $inv){
				// $source.= '$include_args[] = "'.str_replace(';','',$inv).'";'."\n";
				$source.= '$include_args[] = "'.strtr($inv, array(';'=>'')).'";'."\n";
			}
			$source.= '?>';
			self::makeCompileFile($_inc_name,$source);
		}else{# 인클루드 파일 삭제
			// $_inc_name=$this->compile_dir.DIRECTORY_SEPARATOR.str_replace(array('.html','.htm','.tpl'),'.include.inc.'.self::compile_ext,basename($this->filename));
			$_inc_name = $this->compile_dir.DIRECTORY_SEPARATOR.strtr(basename($this->filename), array(
				'.html' =>'.include.inc.'.self::compile_ext,
				'.htm'  =>'.include.inc.'.self::compile_ext,
				'.tpl'  =>'.include.inc.'.self::compile_ext
			));
			if(self::isExists($_inc_name)){
				try{
					unlink($_inc_name);
				}catch(Exception $e){

				}
			}
		}
	}

	#@ void
	# import js, css 파일
	private function make_import_resource_jscss($type, $files)
	{
		if(is_array($files) && count($files)>0)
		{
			foreach($files as $basename => $import_files)
			{
				$_inc_name = $this->compile_dir.DIRECTORY_SEPARATOR.$basename.'.'.$type.'.'.self::compile_ext;
				if(is_array($import_files))
				{
					$source = '<?php'."\n";
					$source.= '$'.$type.'_args = array();'."\n";
					foreach($import_files as $jscss){
						// $source.= '$'.$type.'_args[] = "'.str_replace(';','',$jscss).'";'."\n";
						$source.= '$'.$type.'_args[] = "'.strtr($jscss, array(';'=>'')).'";'."\n";
					}
					$source.= '?>';
					self::makeCompileFile($_inc_name,$source);
				}
			}
		}
	}

	#@ return array
	# 인클루드/import/link 관련 처리
	private function _compile_import_resource($files)
	{
		$result = '';
		if(is_array($files) && count($files)>0)
		{
		    $root_dir_depth = count($this->file_path_arg);
			$result = array();
			foreach($files as $k => $filename)
			{
				// 파일확장자확인에 따른 작업
				$snum = strrpos($filename,'.');
				$ext = substr($filename,$snum+1);
				// $basename = str_replace(array('.html','.htm','.tpl'), '',basename($filename));
				$basename = strtr(basename($filename), array('.html'=>'','.htm'=>'', '.tpl'=>''));
				switch($ext){
					case 'html':
					case 'htm':
					case 'tpl':
						# 기존파일 디렉토리 경로 찾기
						$dir_parent_depth = 0;
						$real_parent_dir = _ROOT_PATH_;
						if(strpos($filename,DIRECTORY_SEPARATOR)!==false)
						{
						    $dir_parent_arg = explode(DIRECTORY_SEPARATOR, $filename);
						    if($dir_parent_arg[0]=='.'){
						        $real_parent_dir = implode(DIRECTORY_SEPARATOR, $this->file_path_arg).'/';
						        // $filename = str_replace('./','', $filename);
						        $filename = strtr($filename, array('.'.DIRECTORY_SEPARATOR=>''));
						        //echo $real_parent_dir.$filename;
						    }else{
						        foreach($dir_parent_arg as $dir_parent_name){
						            if($dir_parent_name=='..') $dir_parent_depth++;
						        }
						        if($dir_parent_depth>0){
						            $real_parent_dir_arg =array_slice($this->file_path_arg, 0, ($root_dir_depth-$dir_parent_depth));
						            $real_parent_dir = implode(DIRECTORY_SEPARATOR, $real_parent_dir_arg).DIRECTORY_SEPARATOR;
						            // $filename = str_replace('../','', $filename);
						            $filename = strtr($filename, array('..'.DIRECTORY_SEPARATOR=>''));
						            // echo $real_parent_dir.$filename;
						        }
						    }
						}

						# 기존파일정보
						parent::__construct($real_parent_dir.$filename);
						$filename = strtr(parent::getRealPath(), array("\\"=>DIRECTORY_SEPARATOR));
						$filemtime = parent::getMTime();

						# 컴파일정보
						$current_compile_dir =$this->compile_dir;
						$compile_name = $current_compile_dir.DIRECTORY_SEPARATOR.self::setCompileName($filename);
						parent::__construct($compile_name);
						$compile_mtime = 1;
						if(!$this->compile){
							if(self::isExists($compile_name)){
								$compile_mtime = parent::getMTime();
							}
						}

						$result[$k] = $compile_name;

						# include 컴파일 하기 결정
						if($filemtime>$compile_mtime){
							if(self::makeDirs($current_compile_dir)){
								$source= parent::compile($filename);
								if(self::makeCompileFile($compile_name,$source)) $result[$k] = $compile_name;
							}
						}

						# 해당 파일의 자바 스크립트 /css
						// self::_compile_import_resource_js($basename);
						// self::_compile_import_resource_css($basename);
					break;
					case 'php':
						$result[$k] = $filename;
						break;
				}
			}
		}
	return $result;
	}

	#@void
	# js
	private function _compile_import_resource_js($basename){
		$import_js_name = $this->compile_dir.DIRECTORY_SEPARATOR.$basename.'.js.'.self::compile_ext;
		if(self::isExists($import_js_name)){
			include_once $import_js_name;
			if(is_array($js_args)){
				foreach ($js_args as $jsfilename) {
					$this->compressJSMinify($jsfilename);
				}
			}
		}
	}

	#@void
	# css
	private function _compile_import_resource_css($basename){
		$import_css_name = $this->compile_dir.DIRECTORY_SEPARATOR.$basename.'.css.'.self::compile_ext;
		if(self::isExists($import_css_name)){
			include_once $import_css_name;
			if(is_array($css_args)){
				foreach ($css_args as $cssfilename) {
					$this->compressCSSMinify($cssfilename);
				}
			}
		}
	}

    #@ return String
	# 컴파일 작명
	private function setCompileName($filename){
		$compile_name = basename($filename);

		// return str_replace(array('.html','.htm','.tpl'),'.'.self::compile_ext,$compile_name);
		return strtr($compile_name, array(
			'.html' =>'.'.self::compile_ext,
			'.htm'  =>'.'.self::compile_ext,
			'.tpl'  =>'.'.self::compile_ext
		));
	}

	# 출력 return
	public function display()
	{
		$source = '';

		# 컴파일 된 파일과 시간비교
		$current_compile_dir =$this->compile_dir;
		$compile_name = $current_compile_dir.DIRECTORY_SEPARATOR.self::setCompileName($this->filename);
		parent::__construct($compile_name);
		$compile_mtime = 1;
		if(!$this->compile){
			if(self::isExists($compile_name)){
				$compile_mtime = parent::getMTime();
			}
		}

		// 컴파일 실행
		if($this->filemtime>$compile_mtime)
		{
			if(!self::makeDirs($current_compile_dir)) return false;

			//echo '<!--compile...-->';
			$source.= parent::compile($this->filename);

			# includes 컴파일 및 이름 바꾸기
			self::make_import_resource_incfile($this->includes);
			$infiles=self::_compile_import_resource($this->includes);
			if(is_array($infiles) && count($infiles)>0){
				$source = str_replace($this->includes,$infiles,$source);
			}

			# root tpl 파일만들기
			if(!self::makeCompileFile($compile_name,$source)) return false;

			# js/css
			// self::make_import_resource_jscss('js', $this->import_js);
			// self::make_import_resource_jscss('css', $this->import_css);
		}
		# 인클루드되는 파일들의 수정 사항을 체크 및 컴파일링 하기
		else{
			# include.inc.php
			// $basename = str_replace(array('.html','.htm','.tpl'), '', basename($this->filename));
			$basename = strtr(basename($this->filename), array('.html'=>'','.htm'=>'', '.tpl'=>''));
			$include_inc_name = $this->compile_dir.DIRECTORY_SEPARATOR.$basename.'.include.inc.'.self::compile_ext;
			if(self::isExists($include_inc_name)){
				include_once $include_inc_name;
				if(is_array($include_args)){
					self::_compile_import_resource($include_args);
				}
			}

			# root 파일용 js, css
			// self::_compile_import_resource_js($basename);
			// self::_compile_import_resource_css($basename);
		}

        ob_start();
		include_once $compile_name;
		$source=ob_get_contents();
		ob_end_clean();
		return $source;
	}

	# 출력 echo
	public function __toString(){
		return self::display();
	}
}
?>
