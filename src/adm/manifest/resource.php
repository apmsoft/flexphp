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
$form->chkEngNumUnderline('resid','리소스ID', $req->resid, true);

$res_id = strtolower(str_replace('_','', $req->resid));
$data = array();

# resource
R::init(_LANG_);
switch($res_id){
    case 'array':
        R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');
        if(is_array(R::$r->array)){
            foreach(R::$r->array as $n => $argv){
                $jsondata = strval(json_encode($argv, JSON_UNESCAPED_UNICODE));        
                $data[$n] = $jsondata;
            }
        }
    break;
    case 'tables':
        case 'strings':
            case 'integers':
                case 'doubles':
                    case 'floats':
                        case 'sysmsg':
                            case 'queries':
        R::parserResourceDefinedID($res_id);
        $data = R::${$res_id};
    break;
}

# output
out_json(array(
    'result' => 'true',
    'title'  => strtoupper($res_id),
	'msg'    => $data
));
?>
