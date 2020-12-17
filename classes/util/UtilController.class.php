<?php
/* ======================================================
| @Author   : 김종관 | 010-4023-7046
| @Email    : apmsoft@gmail.com
| @HomePage : https://www.fancyupsoft.com
| @Editor   : VSCode
| @version : 10
----------------------------------------------------------*/
namespace Fus3\Util;

use Fus3\R\R;
use Fus3\Req\ReqForm;
use \ErrorException;

class UtilController extends UtilConfig
{
	private $extends_mode = '';

    #@ void
	public function __construct(array &$request)
	{
		# req object
		if(!is_array($request)) {
			throw new ErrorException(' is not array',0,0,'request');
		}
		$this->request = $request;

		# check validation
		if(!isset($this->request['doc_id']) || !$this->request['doc_id']){
			throw new ErrorException('DOC_ID ' .R::$sysmsg['e_null'],0,0,'e_null');
		}
	}

	public function on($extends_mode){
		switch($extends_mode){
			case 'config':
			case 'uploadable':
				$this->extends_mode = $extends_mode;
			break;
		}
	}

	public function run($manifest_mode){
		# manifest /------------------------
		// echo $manifest_mode.' +++++ ';
		if($manifest_mode == 'admin'){
			R::parserResourceDefinedID('manifest_adm');
		}else{
			R::parserResourceDefinedID('manifest');
		}

		# doc_id argv
		$req_doc_id = trim(preg_replace('/[^\w\d \/\_]/u', '', $this->request['doc_id']));
		$doc_id = '';
		if($this->extends_mode == 'config')
		{
			# 형식에 맞는지 체크
			if(strpos($this->request['doc_id'], '/') ===false){
				throw new ErrorException('DOC_ID ' .R::$sysmsg['e_formality'],0,0,'e_formality');
			}

			$doc_id_argv   = explode('/', $req_doc_id);
			$doc_id        = $doc_id_argv[0];
			$doc_config_id = $doc_id_argv[1];
		}
		else if($this->extends_mode == 'uploadable'){
			$doc_id = $req_doc_id;
		}

		# check document id
		if(!isset(R::$manifest[$doc_id])){
			throw new ErrorException(R::$sysmsg['e_doc_id'],0,0,'e_doc_id',__LINE__);
		}

		# set manifest data
		$this->manifest = R::$manifest[$doc_id];

		# uploadable
		$this->uploadable = array();
		if(isset($this->manifest['uploadable']) && is_array($this->manifest['uploadable'])){
			foreach($this->manifest['uploadable'] as $uk => $uv){
				// print_r($uv);
				if(!is_array($uv)){
					$this->uploadable[$uk] = $this->compile($uv);
				}else{
					$this->uploadable[$uk] = $uv;
				}
			}
		}

		# config
		if($this->extends_mode == 'config')
		{
			# get config
			parent::__construct($doc_id, $doc_config_id);

			# check validation
			parent::doValidation();

			# model
			parent::doModel();

			# queries
			parent::doQueryies();

			# alarm
			parent::doAlarm();

			# output
			parent::doOutput();
		}
	}
}
?>