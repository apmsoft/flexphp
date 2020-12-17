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
$form->chkEngNumUnderline('feature_column','Model 키', $req->feature_column, false);


# model
$model = new UtilModel();
$model->find_dir = _ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_;

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest');
// R::parserResourceDefinedID('tables');

# db
$db = new DbMySqli();

# config 있는지 체크
$is_flag = (R::$manifest[$req->manifid]['config'][$req->cfid]) ? true : false;
if(!$is_flag){
    out_json(array('result'=>'false', 'msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

$config_filename = R::$manifest[$req->manifid]['config'][$req->cfid];

# read
R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/'.$req->manifid.'/'.$config_filename.'.json', 'config');

# 컴파일러
$utilConfigCompiler = new UtilConfigCompiler();
$column_value = '';
if($req->feature_column && $req->feature_column !=''){
    $column_value = R::$config['model'][$req->feature_column];
}

# 함수에 설정된 파라메터 갯수 가져오기
$myfuns = array();
if(is_array($utilConfigCompiler->funs)){
    foreach($utilConfigCompiler->funs as $fno => $func_name){
        if(array_search($func_name, $config_funs_print_ignore) > -1){}else
        {
            $myfuns[$fno] = array(
                'func_name' => $func_name,
                'func_parameter' => ''
            );

            $func_parameter = array();
            $ref = new ReflectionFunction($func_name);
            foreach( $ref->getParameters() as $param) {
                $func_parameter[] = $param->name;
            }

            if(count($func_parameter)){
                $myfuns[$fno]['func_parameter'] = implode(',',$func_parameter);
            }
        }
    }
}

# output
out_json(array(
    'result'     => 'true',
    'defines'    => $utilConfigCompiler->defines,
    'funs'       => $myfuns,
    'magic_funs' => array('__REQ__'),
    'resources'  => $select_resources,
    'globals'    => $utilConfigCompiler->globals,
	'msg'        => array(
        'column_name' => $req->feature_column,
        'column_value' => $column_value
    )
));
?>
