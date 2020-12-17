<?php
use SMS\R\R;

/** ======================================================
| @Author   : 펜씨업소프트
| @WebSite  : https://fancyupsoft.com
| @Editor   : Sublime Text3
| @UPDATE   : v0.5
----------------------------------------------------------*/
define('_SMS_ROOT_PATH_',$sms_sdk_root);
define('_RES_','res');
define('_VALUES_',_RES_.'/values');     #데이터 타입이 확실한

/**
 * _SMS_PROJECTKEY_ : 프로젝트키
 * _SMS_ID_ : SMS 아이디
 * _SMS_PASSWD_ : SMS 비밀번호
 */
define('_SMS_PROJECTKEY_','');
define('_SMS_ID_','');
define('_SMS_PASSWD_','');

# 클래스 로딩
// include_once _ROOT_PATH_.'/classes/r/R.class.php';
// include_once _ROOT_PATH_.'/classes/sms/Sms.class.php';
// include_once _ROOT_PATH_.'/classes/cipher/CipherEncrypt.class.php';

# 클래스 자동 인클루드 /--------------
spl_autoload_register(function($class_name){
    $tmp_args=explode('\\',preg_replace('/([a-z0-9])([A-Z.])/',"$1$2",$class_name));
    #print_r($tmp_args);
    $class_path_name= (count($tmp_args)>1) ? 
        strtolower($tmp_args[1]). DIRECTORY_SEPARATOR. $tmp_args[2] :
            strtolower($tmp_args[0]). DIRECTORY_SEPARATOR. $tmp_args[0];
    
    if(!class_exists($class_path_name,false))
    {
        # classes 폴더
        if(file_exists(_SMS_ROOT_PATH_.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$class_path_name.'.class.php')!==false){
            #echo _SMS_ROOT_PATH_.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$class_path_name.'.class.php'."\r\n";
            include_once _SMS_ROOT_PATH_.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$class_path_name.'.class.php';
        }
    }
});

# 리소스
R::parserResourceDefinedID('sysmsg');
?>
