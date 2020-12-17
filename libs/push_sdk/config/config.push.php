<?php
use PushSDK\R\R;

/** ======================================================
| @Author   : 펜씨업소프트
| @WebSite  : https://fancyupsoft.com
| @Editor   : VSCode
| @UPDATE   : v0.5
----------------------------------------------------------*/
define('_PUSH_SDK_PATH_',$push_sdk_root);
define('_RES_','res');
define('_VALUES_',_RES_.'/values');     #데이터 타입이 확실한

/**
 * _PUSH_PROJECTKEY_ : 프로젝트키
 * _PUSH_ID_ : PUSH 아이디
 * _PUSH_PASSWD_ : PUSH 비밀번호
 */
define('_PUSH_PROJECTKEY_','');
define('_PUSH_ID_','');
define('_PUSH_PASSWD_','');

# 클래스 로딩
include_once _PUSH_SDK_PATH_.'/classes/r/R.class.php';
include_once _PUSH_SDK_PATH_.'/classes/push/Push.class.php';
include_once _PUSH_SDK_PATH_.'/classes/cipher/CipherEncrypt.class.php';

# 리소스
R::parserResourceDefinedID('sysmsg');
?>
