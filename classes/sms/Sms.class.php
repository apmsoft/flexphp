<?php
/** ======================================================
| @Author	: 펜씨업소프트
| @Email	: apmsoft@gmail.com
| @HomePage	: https://fancyupsoft.com
| @VERSION	: 0.5
----------------------------------------------------------*/
namespace Fus3\Sms;

# 펜씨업소프트 기본 셋팅
class Sms{
    protected $project_key;
    protected $sms_id;
    protected $sms_passwd;
    public function __construct($project_key, $sms_id, $sms_passwd)
	{
        if (!$project_key || $project_key =='') {
            $errmsg = sprintf("%s :: %d SMS PROJECT KEY %s", __CLASS__,__LINE__,R::$sysmsg['e_null']);
            throw new ErrorException($errmsg);
        }
        if (!$sms_id || $sms_id =='') {
            $errmsg = sprintf("%s :: %d SMS ID %s", __CLASS__,__LINE__,R::$sysmsg['e_null']);
            throw new ErrorException($errmsg);
        }
        if (!$sms_passwd || $sms_passwd =='') {
            $errmsg = sprintf("%s :: %d SMS PASSWD %s", __CLASS__,__LINE__,R::$sysmsg['e_null']);
            throw new ErrorException($errmsg);
        }

        // Open connection
        if (!function_exists('curl_init')) {
            throw new ErrorException(R::$sysmsg['e_function_exists']);
        }

        $this->project_key = $project_key;
        $this->sms_id = $sms_id;
        $this->sms_passwd = $sms_passwd;
    }
}