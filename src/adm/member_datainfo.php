<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilModel;
use Fus3\R\R;

/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://fancy-up.tistory.com
| @Editor	: Sublime Text 3 (기본설정)
| @UPDATE	: 0.5
| @TITLE 	: php 개발 가이드 (종합)
----------------------------------------------------------*/
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
if($auth->level <_AUTH_SUPERADMIN_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();
// $form->chkNull('doc_id', 'doc_id', $req->doc_id, true);
$form->chkNumber('id', '회원식별번호', $req->id, true);

# Model
$model = new UtilModel();
// $model->info   = array();
$model->table  = '';
$alarm = array();

# resource
// R::parserResourceDefinedID('manifest');
R::parserResourceDefinedID('tables');

# manifest document
// $document_arg = &R::$manifest['feature']['document'];
// if(is_array($document_arg)){
// 	foreach($document_arg as $doc_id => $doc_attr){
// 		if(is_array($doc_attr) && isset($doc_attr['category']) && $doc_attr['category']=='member'){
// 			if( $doc_id == $req->doc_id){
// 				$doc_attr['doc_id'] = $doc_id;
// 				$model->info = $doc_attr;
// 			}
// 		}
// 	}
// }

# check config
// if(!isset($model->info['config']) || !$model->info['config']){
// 	out_json(array('result'=>'false','msg'=>'It is not config filename'));
// }

# db
$db = new DbMySqli();

# config json
// R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/'.$model->info['config'].'.json', 'config');

# table
$model->table = $model->info['table'];

# 최근 알림 메세지
$alarm_qry = sprintf("SELECT id,msg,friend_id,signdate FROM `%s` WHERE `muid`='%u' ORDER BY `id` DESC LIMIT 5",R::$tables['alarm'],$req->id);
$alarm_rlt = $db->query($alarm_qry);
while($alarm_row = $alarm_rlt->fetch_assoc()){
	$alarm_row['signdate'] = __date($alarm_row['signdate'],'Y-m-d');
	$alarm_row['fprofile'] = array();

	# to
	$alarm_fquery = sprintf("SELECT m.id,m.name,u.directory,u.sfilename FROM `%s` AS m LEFT OUTER JOIN `%s` AS u ON u.extract_id=m.extract_id WHERE m.id='%u'",R::$tables['member'],R::$tables['uploadfiles_mem'],$alarm_row['friend_id']);
	if($alarm_frlt = $db->query($alarm_fquery)){
		$alarm_frow = $alarm_frlt->fetch_assoc();
		if(isset($alarm_frow['id'])){
			$alarm_row['fprofile'] = $alarm_frow;
		}
	}

	# loop
	$alarm[] = $alarm_row;
}

# output
out_json(array(
	'result' =>'true',
	'alarm'=>$alarm,
	'msg'    =>''
));
?>
