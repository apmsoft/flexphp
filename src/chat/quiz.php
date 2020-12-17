<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
// use \ErrorException;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.'/config/config.inc.php';

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# Validation Check 예제
$form = new ReqForm();
$form->chkNull('usertoken', 'user token',$req->usertoken, true);
$form->chkNull('userid', 'id', $req->userid, true);
$form->chkNumber('evtid', '게임', $req->evtid, true);
$form->chkNumber('missonid', '미션', $req->missonid, true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->where = sprintf("`userid`='%s'",$req->userid);

# db
$db = new DbMySqli();

# 회원가입자 인지 체크
$userinfo = $db->get_record('id,authtoken', R::$tables['member'], $model->where);
if(!isset($userinfo['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled', 'msg'=>R::$sysmsg['e_db_unenabled']));
}

# 토큰비교
if($userinfo['authtoken'] != $req->usertoken){
    out_json(array('result'=>'false','msg_code'=>'w_token_isnot_match', 'msg'=>R::$sysmsg['w_token_isnot_match']));
}

# 이미 진행중인 게임인지 확인하기
$is_myevent = $db->get_record('id', R::$tables['myevents'], 
    sprintf("`evt_id`='%s' AND `muid`='%u' AND end_date is NULL", $req->evtid, $userinfo['id'])
);
if(!isset($is_myevent['id'])){
    out_json(array('result'=>'false','msg_code'=>'w_offgoing_game', 'msg'=>R::$sysmsg['w_offgoing_game']));
}

# 미션 정보
$loop = array();

# 미션 정보
$misson_info = $db->get_record('id,msg,misson_way,linkurl,misson_token,extract_id,dvmac', R::$tables['misson'], sprintf("`evtid`='%u' AND `id`='%u'", $req->evtid, $req->missonid));
if(!isset($misson_info['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled', 'msg'=>R::$sysmsg['e_db_unenabled']));
}

if($misson_info['misson_way'] == 'quiz')
{
    $loopModel = new UtilModel($misson_info);
    $loopModel->msg        = $misson_info['msg'];
    $loopModel->linkurl    = $misson_info['linkurl'];
    $loopModel->misson_way = $misson_info['misson_way'];
    $loopModel->images     = array();
    $loopModel->questions  = array();

    # 사진
    $images = array();
    $upfiles_qry =sprintf("SELECT id,file_type,directory,sfilename,ofilename,extract_id FROM `%s` WHERE %s ORDER BY id ASC LIMIT 1", R::$tables['misson_upfiles'], sprintf("`extract_id`='%s' AND `is_regi`='1'", $misson_info['extract_id']));
    $upfiles_rlt = $db->query($upfiles_qry);
    while($upfiles_row = $upfiles_rlt->fetch_assoc()){
        $file_types = explode('/',$upfiles_row['file_type']);
        if($file_types[0] == 'image'){
            $images = array(
                'id' => $upfiles_row['id'],
                'extract_id' => $upfiles_row['extract_id'],
                'filename' => sprintf("%s/%s/m/%s",_SITE_HOST_, $upfiles_row['directory'],$upfiles_row['sfilename'])
            );
        break;
        }
    }

    $loopModel->images = $images;

    # 퀴즈 항목 가져오기
    $quiz_info = $db->get_record('id,question', R::$tables['misson_quiz'], sprintf("`misson_token`='%s'", $loopModel->misson_token));
    if(isset($quiz_info['id'])){
        $question = str_replace("\r\n","\n",trim($quiz_info['question']));
		$question = str_replace("\n",',',$question);
        $loopModel->questions = explode(',',$question);
    }

    # fetch
    $loop = $loopModel->fetch();
}

# output
out_json( array(
    'result' => 'true',
    'msg'    => $loop
));
?>