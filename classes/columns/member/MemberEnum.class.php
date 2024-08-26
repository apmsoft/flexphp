<?php
namespace Flex\Columns\Member;

use Flex\Columns\EntryArrayTrait;

class MemberEnum
{
    use EntryArrayTrait;

    const ID                    = 'id';
    const SIGNDATE              = 'signdate';
    const UP_DATE               = 'up_date';
    const RECENTLY_CONNECT_DATE = 'recently_connect_date';
    const LOGOUT_TIME           = 'logout_time';
    const AUTHTOKEN             = 'authtoken';
    const ALARM_READDATE        = 'alarm_readdate';
    const LICENSE_PERMIT        = 'license_permit';
    const USERID                = 'userid';
    const PASSWD                = 'passwd';
    const PASSWD2                = 'passwd2';
    const RE_PASSWD             = 're_passwd';
    const NEW_PASSWD            = 'new_passwd';
    const NEW_RE_PASSWD         = 'new_re_passwd';
    const LEVEL                 = 'level';
    const CELLPHONE             = 'cellphone';
    const SMS_AUTHNO            = 'sms_authno';
    const SSID_AUTHNO           = 'ssid_authno';
    const NAME                  = 'name';
    const EXTRACT_ID            = 'extract_id';
    const INTRODUCE             = 'introduce';
    const AUTHEMAILKEY          = 'authemailkey';
    const IS_PUSH               = 'is_push';
    const COMPANY_ID            = 'company_id';
    const DEPARTMENT            = 'department';
    const IS_MARKETING          = 'is_marketing';
    const WITHDRAWAL_DATE       = 'withdrawal_date';
}
?>