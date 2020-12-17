<?php
namespace PushSDK\Push;

# 펜씨업소프트 기본 셋팅
class Push{
    protected $project_key;
    protected $push_id;
    protected $push_passwd;
    public function __construct($project_key, $push_id, $push_passwd)
	{
        if (!$project_key || $project_key =='') {
            $errmsg = sprintf("%s :: %d PUSH PROJECT KEY %s", __CLASS__,__LINE__,R::$sysmsg['e_null']);
            throw new ErrorException($errmsg);
        }
        if (!$push_id || $push_id =='') {
            $errmsg = sprintf("%s :: %d PUSH ID %s", __CLASS__,__LINE__,R::$sysmsg['e_null']);
            throw new ErrorException($errmsg);
        }
        if (!$push_passwd || $push_passwd =='') {
            $errmsg = sprintf("%s :: %d PUSH PASSWD %s", __CLASS__,__LINE__,R::$sysmsg['e_null']);
            throw new ErrorException($errmsg);
        }

        // Open connection
        if (!function_exists('curl_init')) {
            throw new ErrorException(R::$sysmsg['e_function_exists']);
        }

        $this->project_key = $project_key;
        $this->push_id = $push_id;
        $this->push_passwd = $push_passwd;
    }
}