<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.8.2
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# use
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\Template\Template;
use Fus3\R\R;
use Fus3\Cipher\CipherEncrypt;
use Fus3\Date\DateTimes;

# 차단
if($app->lang !='ko' && $app->lang !='en'){
	die(R::$sysmsg['w_not_allowed_nation']);
	exit;
}

// exec("arp -H ether -n -a $REMOTE_ADDR",$values);
// $parts = explode(' ',$values[0]);
// print_r($parts);

// exec("ls -al",$v);
// print_r($v);
// exec("whoami",$u);
// print_r($u);
// echo system("ls -al");
# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 변수
$req = new Req;
$req->useGET();

# 로그인 상태 체크
if($auth->id && _is_null($_SESSION['aduuid'])){
	history_go(R::$sysmsg['w_aduuid']);
}
else if(!$auth->id){
	window_location('.'.DIRECTORY_SEPARATOR.'login.php');
}

# [세션보안]로그인 성공시 등록한 ip와 접속자 ip가 동일한지 체크한다.
if($auth->id){
	if(strcmp($_SESSION['auth_ip'],$_SERVER['REMOTE_ADDR'])){
		window_location('.'.DIRECTORY_SEPARATOR.'logout.php',R::$sysmsg['w_authority_ip']);
	}

	if($auth->level<_AUTH_SUPERADMIN_LEVEL){
		window_location('.'.DIRECTORY_SEPARATOR.'logout.php',R::$sysmsg['w_not_have_permission']);
	}
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
	window_location('.'.DIRECTORY_SEPARATOR.'logout.php',R::$sysmsg['w_aduuid']);
}

# resources
R::parserResourceDefinedID('manifest');
R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._LAYOUT_.DIRECTORY_SEPARATOR.'layout_adm.json', 'layout');
R::parserResourceDefinedID('tables');

# 환경설정 파일 체크
$activity = $req->act;
if(!$req->act){
    $activity = 'index';
}

# menu
R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._MENU_.DIRECTORY_SEPARATOR.'adm_drawer_menu.json', 'drawer_menu');
$drawer_menu = R::$r->drawer_menu;

# 관리자 메뉴 레이아웃 메뉴 위치 설정과 통합
if(is_array(R::$layout))
{
	$sitemenu =&$drawer_menu[0]['menu'];
	$sitemenu_ids = array_column($sitemenu, 'id');
	$sitemenu_ids[] = 'company';

	$develop_menu =&$drawer_menu[1]['menu'];
	$develmenu_ids= array_column($develop_menu, 'id');
	$develmenu_ids[] = 'vintegers';
	$develmenu_ids[] = 'varray';
	$develmenu_ids[] = 'vsysmsg';
	$develmenu_ids[] = 'qqueries';
	$develmenu_ids[] = 'layoutadm';	
	$develmenu_ids[] = 'manifestadm';

	foreach(R::$layout as $id => $id_data)
	{
		# 관리자 사이트 메뉴 설정
		if(array_search($id, $sitemenu_ids) > -1){
		}else{
			if($id_data['category'] == 'drawermenu'){
				$sitemenu[] = array(
					'id' => $id,
					'title' => $id_data['title'],
					'icon' => '<i class="fa fa-window-restore"></i>',
					'lv' => 100,
					'url' => './?act='.$id					
				);
			}
		}

		# 개발자 사이트 메뉴 설정
		if(array_search($id, $develmenu_ids) > -1){
		}else{
			if($id_data['category'] == 'develop'){
				$develop_menu[] = array(
					'id' => $id,
					'title' => $id_data['title'],
					'icon' => '<i class="fa fa-window-restore"></i>',
					'lv' => 999,
					'url' => './?act='.$id					
				);
			}
		}
	}	
}

# db
$db = new DbMySqli();

# 프로필 정보
$profile_row = array();
if($auth->id){
	$profile_qry = sprintf("SELECT m.id,m.name,m.userid,m.signdate,m.recently_connect_date,u.directory,u.sfilename FROM `%s` AS m LEFT OUTER JOIN `%s` AS u ON u.extract_id=m.extract_id AND u.is_regi='1' WHERE m.id='%s'", R::$tables['member'], R::$tables['member_upfiles'], $auth->id);
	$profile_rlt = $db->query($profile_qry);
	$profile_row = $profile_rlt->fetch_assoc();
}

# 통계 대쉬보드
if($activity =='index'){
	# 게시판 통계
	// $comment_total  = 0;
	// $bbs_recent_qry = '';
	// $bbs_total      = 0;
	// $bbs_statis     = '';
	// $document_arg   = &R::$manifest['feature']['document'];
	// if(is_array($document_arg)){
	// 	foreach($document_arg as $doc_id => $doc_attr){
	// 		if(is_array($doc_attr) && isset($doc_attr['category']) && $doc_attr['category']=='bbs')
	// 		{
	// 			# 게시판 총 갯수
	// 			$this_table_id = $doc_attr['table'];
	// 			if(R::$tables[$this_table_id])
	// 			{
	// 				$total_record  = $db->get_total_record(R::$tables[$this_table_id], '');
	// 				$bbs_total+= $total_record;
					
	// 				# set item array
	// 				$bbs_statis.= ($bbs_statis) ? ',["'.$doc_id.'",'.$total_record.']' : '["'.$doc_id.'",'.$total_record.']';

	// 				# bbs union all
	// 				$bbs_recent_qry.= ($bbs_recent_qry) 
	// 					? sprintf(" UNION ALL (SELECT id,signdate,title FROM `%s` ORDER BY id DESC LIMIT 5)", R::$tables[$this_table_id]) 
	// 					: sprintf("(SELECT id,signdate,title FROM `%s` ORDER BY id DESC LIMIT 5)", R::$tables[$this_table_id]);
	// 			}
	// 		}

	// 		# 코멘트 통계
	// 		if(is_array($doc_attr) && isset($doc_attr['category']) && $doc_attr['category']=='comment')
	// 		{
	// 			# 코멘트 총 갯수
	// 			$this_table_cid = $doc_attr['table'];
	// 			$total_crecord  = $db->get_total_record(R::$tables[$this_table_cid], '');
	// 			$comment_total+= $total_crecord;
	// 		}
	// 	}
	// }

	# 회원통계 /======================
	# 회원 시작-종료
	$member_statis_groups = array();
	$member_statis_groups_str = '';
	$mem_start_end_y = $db->get_record("min(signdate) as sd, max(signdate) as ed", R::$tables['member'], "");
	#out_r($mem_start_end_y);
	$mem_start_y = date('Y',$mem_start_end_y['sd']);
	$mem_end_y = date('Y',$mem_start_end_y['ed']);
	#out_ln(sprintf("sd : %s - ed : %s", $mem_start_y, $mem_end_y));
	for($gi=$mem_start_y; $gi<=$mem_end_y; $gi++){
		$member_statis_groups[] = $gi;
		$member_statis_groups_str .= ($member_statis_groups_str) ? ',"'.$gi.'"' : '"'.$gi.'"';
	}
	#out_r($member_statis_groups);

	# 날짜별
	$member_total = 0;
	$member_statics_str = '';
	$member_statics_args = array();
	$mem_sta_qry = "SELECT FROM_UNIXTIME(signdate, '%Y-%m') sd,count(*) as c FROM `".R::$tables['member']."` GROUP BY sd";
	$mem_sta_rlt = $db->query($mem_sta_qry);
	while($mem_sta_row= $mem_sta_rlt->fetch_assoc()){
		$sta_arg = explode('-',$mem_sta_row['sd']);
		$sta_y = $sta_arg[0];
		$sta_m = (int)$sta_arg[1];
		$member_statics_args[$sta_y][$sta_m] = $mem_sta_row['c'];
		$member_total+= $mem_sta_row['c'];
	}
	#out_r($member_statics_args);
	//['data1', 13,1,3,3,2,2,14,12,20,13,16,7,9,3],
	foreach($member_statis_groups as $idx => $idy){
		$member_statics_str .= "['".$idy."',";
		$temp_cnt = '';
		for($mi=1; $mi<=12; $mi++){
			$temp_cnt .= (isset($member_statics_args[$idy][$mi])) ? $member_statics_args[$idy][$mi].',' : '0,';
		}
		$member_statics_str .= substr($temp_cnt,0,-1).'],';
	}
	#out_ln($member_statics_str);

	# 신규가입자 /=====================
	$member_new = array();
	$mem_new_qry = sprintf("SELECT id,signdate,name,extract_id FROM `%s` ORDER BY id DESC LIMIT 6", R::$tables['member']);
	$mem_new_rlt = $db->query($mem_new_qry);
	while($mem_new_row= $mem_new_rlt->fetch_assoc()){
		// photo
		$mem_new_photo = '';
		$mem_new_file_row = $db->get_record('directory,sfilename', R::$tables['member_upfiles'], sprintf("extract_id='%s' AND is_regi='1' ORDER BY id ASC LIMIT 1", $mem_new_row['extract_id']));
		if($mem_new_file_row['sfilename']){
			$mem_new_photo = $mem_new_file_row['directory'].'/s/'.$mem_new_file_row['sfilename'];
		}

		// set array
		$member_new[] = array(
			'id'       => $mem_new_row['id'],
			'name'     => $mem_new_row['name'],
			'signdate' => date('Y/m/d',$mem_new_row['signdate']),
			'photo'    => $mem_new_photo
		);
	}

	# 최근접속자 /=====================
	$member_recent = array();
	$mem_recent_qry = sprintf("SELECT id,recently_connect_date,name,extract_id FROM `%s` WHERE recently_connect_date>0 ORDER BY recently_connect_date DESC LIMIT 6", R::$tables['member']);
	$mem_recent_rlt = $db->query($mem_recent_qry);
	while($mem_recent_row= $mem_recent_rlt->fetch_assoc()){
		// photo
		$mem_recent_photo = '';
		$mem_recent_file_row = $db->get_record('directory,sfilename', R::$tables['member_upfiles'], sprintf("extract_id='%s' AND is_regi='1' ORDER BY id ASC LIMIT 1", $mem_recent_row['extract_id']));
		if($mem_recent_file_row['sfilename']){
			$mem_recent_photo = $mem_recent_file_row['directory'].'/s/'.$mem_recent_file_row['sfilename'];
		}

		// set array
		$member_recent[] = array(
			'id'       => $mem_recent_row['id'],
			'name'     => $mem_recent_row['name'],
			'signdate' => date('Y/m/d',$mem_recent_row['recently_connect_date']),
			'photo'    => $mem_recent_photo
		);
	}

	# 최근게시물
	// $bbs_recent = array();
	// if($bbs_recent_qry !=''){
	// 	$bbs_recent_rlt = $db->query($bbs_recent_qry." ORDER BY signdate DESC LIMIT 7");
	// 	while($bbs_recent_row = $bbs_recent_rlt->fetch_assoc()){
	// 		$bbs_recent[] = $bbs_recent_row;
	// 	}
	// }
}

# docs json 불러오기
$layout_appdocs = '{}';
if(isset(R::$layout[$activity]['manifid'])){
	$layout_docs_id = R::$layout[$activity]['manifid'];
	if($layout_docs_id && $layout_docs_id !='')
	{
		$app_docs_filename = $layout_docs_id.'.json';
		$nation_filename = _ROOT_PATH_.'/'._LAYOUT_.'/docs/'.$layout_docs_id.'_'._LANG_.'.json';
		if(file_exists($nation_filename)){
            $app_docs_filename = $layout_docs_id.'_'._LANG_.'.json';
		}
		if(file_exists(_ROOT_PATH_.'/'._LAYOUT_.'/docs/'.$app_docs_filename)){
			R::parserResource(_ROOT_PATH_.'/'._LAYOUT_.'/docs/'.$app_docs_filename, 'app_docs');
			$layout_appdocs = R::$r->app_docs;
		}
	}
}

# template 선언
try{
	$tpl = new Template(getcwd().DIRECTORY_SEPARATOR.R::$layout[$activity]['filename']);
}catch(Exception $e){
	throw new ErrorException($e->getMessage(),__LINE__);
}

# tpl 변수
$tpl['strings']  = R::$strings;
$tpl['profile']  = $profile_row;
if($activity =='index'){
	$tpl['member_statis_groups'] = $member_statis_groups_str;
	$tpl['member_statis_cnt'] = ($member_statics_str) ? substr($member_statics_str,0,-1) : '[]';
	$tpl['member_new']        = $member_new;
	$tpl['member_recent']     = $member_recent;
	$tpl['member_total']      = number_format($member_total);
	$tpl['bbs_statis']        = $bbs_statis;
	$tpl['bbs_recent']        = $bbs_recent;
	$tpl['bbs_total']         = number_format($bbs_total);
	$tpl['comment_total']     = number_format($comment_total);
}
$tpl['drawer_menu']     = $drawer_menu;
$tpl['app_docs']        = json_encode($layout_appdocs);
$tpl['layout'] 			= R::$layout[$activity];
$tpl['layout_docs'] 	= json_encode(R::$layout[$activity]);
$tpl['menu']            = $menu;
$tpl['is_apple_device'] = $app->is_apple_device();
// print_r($tpl);
# prints
$tpl->compile_dir =_ROOT_PATH_.DIRECTORY_SEPARATOR._TPL_.DIRECTORY_SEPARATOR.'_adm';
$tpl->compile     = true;
// $tpl->compression = false;
out_html($tpl->display());
?>
