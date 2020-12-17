<?php 
namespace Flex\Util;

/** ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://developer.fancyupsoft.com
| @Editor   : Sublime Text 3
| @version  : 0.3
# 푸싱 전송 유틸 클래스
----------------------------------------------------------*/ 
class UtilSendPush {

	private $push_type         = null;
	private $part_recive_users = array();
	static $db;

	public $send_users    = array();
	private $push_ios     = array();
	private $push_android = array();

	public function __construct($type, $part_recive=array())
	{
		$this->push_type = $type;
		$this->part_recive_users = $part_recive;
		self::$db = new DbMySqli();
	}

	// 회원정보 추출
	public function findPushReciveUsers(){
		switch($this->push_type){
			case 'part':
				foreach($this->part_recive_users as $recive_id){
					$user_row = self::$db->get_record('id,userid,name,is_push', R::$tables['member'], 
						sprintf("`userid`='%s'", $recive_id));
					if($user_row['id'])
					{
						if($user_row['is_push'] == 'y'){
							$this->send_users[] = $user_row;

							// 푸싱키 추출
							self::getPushToken($user_row['id']);
						}
					}
				}
			break;

			case 'all':
				$all_user_qry = sprintf("SELECT id,userid,name,is_push FROM `%s` WHERE `level` > '0'", R::$tables['member']);
				$all_user_rlt = self::$db->query($all_user_qry);
				while($all_user_row = $all_user_rlt->fetch_assoc()){
					if($all_user_row['id'])
					{
						if($all_user_row['is_push'] == 'y'){
							$this->send_users[] = $all_user_row;

							// 푸싱키 추출
							self::getPushToken($all_user_row['id']);
						}
					}
				}
			break;
		}
	}

	// 등록된 푸싱키 추출
	public function getPushToken($int_id){
		// 푸싱키 추출
		$push_row = self::$db->get_record('pushtoken,os_type', R::$tables['pushtoken'], sprintf("`id`='%s'", $int_id));
		if($push_row['pushtoken'] && $push_row['pushtoken'] !=''){
			if($push_row['os_type']=='i'){
				$this->push_ios[] = $push_row['pushtoken'];
			}else{
				$this->push_android[] = $push_row['pushtoken'];
			}
		}
	}

	// 메세지
	public function getBindPushMsg($msg, $args){
		$bind_msg = $msg;

		preg_match_all("|\[[^\]](.*)[^\]]+\]|U",$msg,$matches1,PREG_PATTERN_ORDER);

		#Out::prints_r($matches1);
		if(is_array($matches1) && isset($matches1[0]))
		{
			foreach($matches1[0] as $m1var){
				#Out::prints_ln($m1var);
				$_m1var = strtr($m1var, array('['=>'',']'=>''));
				preg_match_all("|{[^}](.*)[^}]+}|U",$_m1var,$_m1var_matches,PREG_PATTERN_ORDER);
				
				#Out::prints_r($_m1var_matches);
				if(is_array($_m1var_matches) && isset($_m1var_matches[0]))
				{
					foreach($_m1var_matches[0] as $_m1var_matches_var){
						#Out::prints_ln($_m1var_matches_var);
						$_m1var_matches_var2 = strtr($_m1var_matches_var, array('{'=>'','}'=>''));
						if(isset($args[$_m1var_matches_var2]) && $args[$_m1var_matches_var2]){
							$re_m1var = strtr($m1var, array(','=>'', '['=>'',']'=>'',$_m1var_matches_var=>$args[$_m1var_matches_var2]));
							#Out::prints_ln($re_m1var);
							$bind_msg = str_replace($m1var, $re_m1var, $bind_msg);
						}else{
							$bind_msg = strtr($bind_msg, array($m1var=>''));
						}
					}
				}
			}
		}

		preg_match_all("|{[^}](.*)[^}]+}|U",$bind_msg,$matches2,PREG_PATTERN_ORDER);
		// Out::prints_r($matches2);
		if(is_array($matches2) && isset($matches2[0]))
		{
			foreach($matches2[0] as $var){
				$_var = strtr($var, array('{'=>'','}'=>''));
				if(strpos($_var,'.') !==false)
				{
					$_var_argv = explode('.', $_var);
					$bind_name = $_var_argv[0];
					$bind_val  = $_var_argv[1];
					switch($bind_name){
						case 'req':
							if(isset($args[$bind_name]) && $args[$bind_name][$bind_val]){
								$bind_msg = strtr($bind_msg, array($var=>$args[$bind_name][$bind_val]));
							}
						break;
						case '_SESSION':
							if(isset($_SESSION[$bind_val]) && $_SESSION[$bind_val]){
								$bind_msg = strtr($bind_msg, array($var=>$_SESSION[$bind_val]));
							}
						break;
					}
				}else{
					if(isset($args[$_var])){
						$bind_msg = strtr($bind_msg, array($var=>$args[$_var]));
					}
				}
			}
		}
	return $bind_msg;
	}

	// 알람 테이블 저장
	public function sendAlarm($msg){
		foreach($this->send_users as $share_user){
			# alarm
			self::$db['muid']      =$share_user['id'];
			self::$db['friend_id'] =$msg['sender'];
			self::$db['mode']      =$msg['mode'];
			self::$db['msg']       =$msg['msg'];
			self::$db['parent_id'] =$msg['parent_id'];
			self::$db['param']     =$msg['param'];
			self::$db['signdate']  =time();
			self::$db['isread']    =time();
			self::$db->insert(R::$tables['alarm']);
		}
	}

	// 전송::안드로이드
	public function sendPushAndroid($args=array()){
		# 푸슁 전송하기 /======================
		if(count($this->push_android)){
			if(_GCM_API_KEY_ !=''){
				$pushFCM = new PushFCMMessage(_GCM_API_KEY_);
				$pushFCM->setDevices($this->push_android);
				$response = $pushFCM->send($args);
			}
		}
	}

	// 전송::아이폰
	public function sendPushIOS($send_msg, $args=array()){
		# 푸슁 전송하기 /======================
		if(count($this->push_ios)){
			if(_IOS_APNS_PEM_ !=''){
				$pushIOS = new PushIOSMessage(_IOS_APNS_PEM_);
				// $pushIOS->service = 'dev';
				$pushIOS->setDevices($this->push_ios);
				$pushIOS->send(
					$send_msg,
					$args
				);
			}
		}
	}
}
?>