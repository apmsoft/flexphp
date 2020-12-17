<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Ftp\Ftp;
use Fus3\Preference\PreferenceInternalStorage;
use Fus3\Util\UtilResFtpHelper;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ftp.php';

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 레벨체크
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('manifid','메니페스트 ID', $req->manifid, true);
$form->chkEngNumUnderline('cfid','실행프로그램 ID', $req->cfid, true);

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');

# 등록된것인지 체크
if(!isset(R::$manifest[$req->manifid])){
    out_json(array('result'=>'false', 'msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 실 실행 config 파일이 생성되었는지 체크
$is_flag = (R::$manifest[$req->manifid]['config'][$req->cfid]) ? true : false;
if($is_flag){
    out_json(array('result'=>'false', 'msg_code'=>'w_duplicate_id','msg'=>R::$sysmsg['w_duplicate_id']));
}

# 기존 템플릿 가져다 복사해서 만들어 주기
if(array_search($req->cfid, $config_list) > -1){

}

# Model
$model = new UtilModel();
$docs_data = array();
$is_docs_data = true;

# docs 문서가 있는지 체크
if(!file_exists(_ROOT_PATH_.'/'._LAYOUT_.'/docs/'.$req->manifid.'.json')){
    # docs admin template
    R::parserResource(_ROOT_PATH_.'/'._LAYOUT_.'/docs/template/docs_adm.json', 'docs_tpl');
    $docs_data[$req->manifid] = R::$r->docs_tpl['docs'];
    $is_docs_data = false;
}

# ftp 클랙스
$ftp = new Ftp();

#### config /=======================
# resource ftp helper
$utilResFtpHelper = new UtilResFtpHelper(_CONFIG_.DIRECTORY_SEPARATOR.'template', $req->cfid);
$utilResFtpHelper->set_save_server_filename(_CONFIG_.DIRECTORY_SEPARATOR.$req->manifid);

# 파일 저장
if(!$file = $ftp->open_file_read($utilResFtpHelper->local_filename, $utilResFtpHelper->server_filename)){
    out_json(array('result'=>'false', 'msg'=>'config :: open_file_write() failed!'));
}

# 파일 데이터 읽어 들이기
$pref = new PreferenceInternalStorage($utilResFtpHelper->local_real_filename,'r');
$context = $pref->readInternalStorage();

# dir 이 있는지 체크 없으면 생성
if(!is_dir(_ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_.DIRECTORY_SEPARATOR.$req->manifid)){
    if(!$ftp->ftp_mkdir(_FTP_DIR_._CONFIG_.DIRECTORY_SEPARATOR.$req->manifid)){
        out_json(array('result'=>'false', 'msg'=>'config :: ftp mkdir failed!'));
    }
}

# config 파일 생성
if(!$ftp->open_file_write($utilResFtpHelper->local_filename, $utilResFtpHelper->save_server_filename, $context)) {
    out_json(array('result'=>'false', 'msg'=>'config :: open_file_write() failed!'));
}

#### manifest /================
# resource ftp helper
$utilResFtpHelper2 = new UtilResFtpHelper(_RES_, 'manifest_adm');
$utilResFtpHelper2->set_save_server_filename(_RES_);

# 파일 저장
if(!$file2 = $ftp->open_file_read($utilResFtpHelper2->local_filename, $utilResFtpHelper2->server_filename)){
    out_json(array('result'=>'false', 'msg'=>'manifest :: open_file_write() failed!'));
}

# 메니페스트 데이터 
R::$manifest[$req->manifid]['config'][$req->cfid] = $req->cfid;
$context_manifest = json_encode(R::$manifest,JSON_UNESCAPED_UNICODE);
if(!$ftp->open_file_write($utilResFtpHelper2->local_filename, $utilResFtpHelper2->save_server_filename, $context_manifest)) {
    out_json(array('result'=>'false', 'msg'=>'manifest :: open_file_write() failed!'));
}

if(!$is_docs_data)
{
    #### layout docs 문서 /===================
    $utilResFtpHelper3 = new UtilResFtpHelper(_LAYOUT_.'/docs/template', 'docs_adm');

    # 파일 저장
    if(!$file3 = $ftp->open_file_read($utilResFtpHelper3->local_filename, $utilResFtpHelper3->server_filename)){
        out_json(array('result'=>'false', 'msg'=>'layout_docs :: open_file_write() failed!'));
    }

    $context_layout_docs = json_encode($docs_data,JSON_UNESCAPED_UNICODE);    
    $utilResFtpHelper3->filename = $req->manifid.'.json';
    $utilResFtpHelper3->set_save_server_filename(_LAYOUT_.'/docs');
    if(!$ftp->open_file_write($utilResFtpHelper3->local_filename, $utilResFtpHelper3->save_server_filename, $context_layout_docs)) {
        out_json(array('result'=>'false', 'msg'=>'layout_docs :: open_file_write() failed!'));
    }
}

# output
out_json(array(
    'result' =>'true',
    'msg'    =>R::$sysmsg['v_update']
));
?>
