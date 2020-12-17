<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;
use Fus3\Files\FilesSizeConvert;
use Fus3\Util\UtilFileUpload;
// use \ErrorException;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.'/config/config.inc.php';

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# Validation Check 예제
$form = new ReqForm();
$form->chkNull('usertoken', 'user token',$req->usertoken, true);
$form->chkEmail('userid', 'id', $req->userid, true);
$form->chkNumber('evtid', '게임', $req->evtid, true);
$form->chkNumber('missonid', '미션', $req->missonid, true);
$form->chkNull('misson_token', '미션토큰', $req->misson_token, true);

# resources
R::parserResourceDefinedID('tables');

# model
$model = new UtilModel();
$model->is_game_end = 'F';
$model->where = sprintf("`userid`='%s'",$req->userid);
// print_r($req->fetch());
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

# 등록된 미션이 있는지 체크
$is_misson = $db->get_record('id,misson_way,misson_token', R::$tables['misson'], 
    sprintf("`evtid`='%s' AND `id`='%u'", $req->evtid, $req->missonid)
);
if(!isset($is_misson['id'])){
    out_json(array('result'=>'false','msg_code'=>'e_db_unenabled', 'msg'=>R::$sysmsg['e_db_unenabled']));
}

# 파일 처리
$filesSizeConvert = new FilesSizeConvert();

# 파일업로드
if(!isset($_FILES['upfilename'])){
    out_json(array('result'=>'false','msg_code'=>'file error', 'msg'=>'dddd'));
}

# 필터 및 복사
$fileupObj = new UtilFileUpload(array(
    'file_extension' => 'jpg,jpeg',
    'file_maxsize'   => 8
));
$file_args=$fileupObj->fileUpload(
    $_FILES['upfilename']['tmp_name'],
    $_FILES['upfilename']['name'],
    $_FILES['upfilename']['size'],
    $_FILES['upfilename']['type'],
    $_FILES['upfilename']['error']
);

#디비 저장
$image_fullpath = '';
if(is_array($file_args) && count($file_args)>0)
{
    # 저장된 이미지 경로
    $image_fullpath = $file_args['directory'].'/'.$file_args['sfilename'];

    # mymisson insert
    $db['evtid']        = $req->evtid;
    $db['missonid']     = $req->missonid;
    $db['muid']         = $userinfo['id'];
    $db['misson_token'] = $req->misson_token;
    $db['answer']       = $image_fullpath;
    $db['signdate']     = time();
    $db->insert(R::$tables['mymisson']);

    # chat insert
    $db['evtid']          = $req->evtid;
    $db['missonid']       = $req->missonid;
    $db['muid']           = $userinfo['id'];
    $db['mymisson_token'] = $req->misson_token;
    $db['signdate']       = time();
    $db->insert(R::$tables['misson_chat']);

    # 전체 미션 완료인지 체크
    $misson_total = $db->get_total_record(R::$tables['misson'], sprintf("`evtid`='%u'", $req->evtid) );
    $mymisson_total = $db->get_total_record(R::$tables['mymisson'], sprintf("`evtid`='%u' AND `muid`='%u'", $req->evtid, $userinfo['id']) );
    if($misson_total == $mymisson_total){
        # 이벤트 종료
        $db['end_date'] = date('Y-m-d');
        $db['end_time'] = date('H:i');
        $db->update(R::$tables['myevents'], sprintf("`evt_id`='%u' AND `muid`='%u'", $req->evtid, $userinfo['id']) );

        # 게임완료
        $model->is_game_end = 'T';
    }

    # output
    out_json( array(
        'result' => 'true',
        'is_game_end' => $model->is_game_end,
        'msg'    => R::$sysmsg['v_camera_insert']
    ));
}else{
    # output
    out_json( array(
        'result' => 'false',
        'is_game_end' => $model->is_game_end,
        'msg'    => R::$sysmsg['e_failed_write_disk']
    ));
}
?>