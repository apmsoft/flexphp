<?php
namespace Flex\Files;

use Flex\R\R;
use Flex\Req\Req;
use Flex\Log\Log;

# parnet : 
# purpose : 파일삭제
final class FilesRemove
{
    private $request;
	private $upload_dir;

	final function __construct(Req $request){
        $this->request = $request;
		$this->upload_dir = _ROOT_PATH_.'/'._UPLOAD_;
	}

	public function doRemove () : array
	{
        # convert object
        $this->request->usePOST();
        $req = (object) $this->request->fetch();

        # Validation Check 예제
        try{
            $form = new \Flex\Req\ReqForm();
            $form->chkNull('extract_id', 'extract_id',$req->extract_id, true);
            $form->chkNull('sfilename', 'sfilename',$req->sfilename, true);
        }catch(\Exception $e){
            return json_decode($e->getMessage(),true);
        }

        # path
        $_dir = (isset($req->extract_id)) ? $this->upload_dir.'/'.$req->extract_id  :'';
		
        # 디렉토리인지 체크
        if($this->isDir($_dir))
        {
            if(isset($req->sfilename) && $req->sfilename!='')
            {
                $dirObject = new \Flex\Dir\DirObject($_dir);
                $files = $dirObject->findFiles('*',['html']);
                #print_r($files);
                if(is_array($files) && count($files))
                {
                    foreach($files as $fname){
                        $dfname = $fname;
                        if(strpos($fname,'_') !==false){
                            $dfname = explode('_',$fname)[1];
                        }
                        #echo $dfname.PHP_EOL;
                        if($dfname == $req->sfilename){
                            #echo 'deletefilename : '.$_dir.'/'.$fname.PHP_EOL;
                            if(file_exists($_dir.'/'.$fname)){
                                unlink($_dir.'/'.$fname);
                            }
                        }
                    }
                }
            }
        }

		// model
		return [
            'result' => 'true',
			'msg' => $req->sfilename ?? ''
		];
    }

    # 디렉토리인지 확인
	private function isDir(string $dir) : bool{
		if(!is_dir($dir)) return false;
	return true;
	}
}
?>