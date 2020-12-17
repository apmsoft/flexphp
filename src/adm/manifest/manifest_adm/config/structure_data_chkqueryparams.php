<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Dir\DirObject;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilConfigCompiler;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

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
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('manifid','메니페스트 ID', $req->manifid, true);
$form->chkEngNumUnderline('cfid','실행프로그램 ID', $req->cfid, true);
$form->chkEngNumUnderline('query','실행프로그램 ID', $req->query, true);


# model
$model = new UtilModel();
$model->find_dir = _ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_;

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');
R::parserResourceDefinedID('queries');

# db
$db = new DbMySqli();

# config 있는지 체크
$is_flag = (R::$manifest[$req->manifid]['config'][$req->cfid]) ? true : false;
if(!$is_flag){
    out_json(array('result'=>'false', 'msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

# 분석데이터
$query_params = array();
preg_match_all("|{[^}](.*)[^}]+}|U",R::$queries[$req->query],$matches,PREG_PATTERN_ORDER);
if(is_array($matches) && isset($matches[0]))
{
    foreach($matches[0] as $qpk => $qpv){
        $clean_column = strtr($qpv, array('{'=>'','}'=>''));
        $query_params[$qpk] =$clean_column;
    }
}

# output
out_json(array(
    'result' => 'true',
    'query_params_type' =>$query_params_type,
	'msg'    => $query_params
));
?>
