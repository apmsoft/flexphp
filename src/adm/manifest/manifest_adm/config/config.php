<?php
$config_list = array("list","view","post","insert","edit","update","password","delete");

$config_validation = array(
    'chkNull'         => '단순히 데이터가 전송 되었는지 체크 ',
    'chkUserid'       => '아이디체크 (공백체크,문자길이체크[4~14],한국어체크,특수문자인지체크)',
    'chkPasswd'       => '비밀번호체크 (공백체크,문자길이체크[4~160],한국어체크)',
    'chkPasswdSecure' => '비밀번호 보안강화(최소8자 및 특수문자 최소1개 포함)',
    'chkName'         => '이름체크 (공백체크,특수문자)',
    'chkPhone'        => '전화번호형식 (공백체크,허용특수문자(-))',
    'chkNumber'       => '숫자인지확인 (공백체크,넘버체크)',
    'chkFloat'        => '실수인지체크',
    'chkAlphabet'     => '영단어인지체크 (알파벳체크)',
    'chkEmail'        => '이메일체크 (ex : sed_-23@apmsoftax.com)',
    'chkLinkurl'      => 'url 체크 (http://)',
    'chkDateFormat'   => '날짜체크 (정확한 날짜인지 체크 (yyyy-MM-dd)',
    'chkTimeFormat'   => '시간체크 (정확한 시간인지 체크 (HH:ii:ss, HH:ii)',
    'chkDatePeriod'   => '시작날짜(yyyy-MM-dd)와 종료날짜(yyyy-MM-dd) 체크 및 유효성 체크',
    'chkEquals'       => '두개의 값이 같은지 체크',
    'chkEngNumUnderline' => '영어,숫자, underline 만 입력되었는지 체크 (ex : a_23)'
);

$config_funs_print_ignore = array(
    '_is_null','_is_up_alphabet',
    '_is_space','_is_same_repeat_string','_is_number','_is_alphabet','_is_low_alphabet',
    '_is_korean','_is_etc_string','_is_first_alphabet','_is_string_length','_chk_date',
    '_chk_date_period','_equals','out_','out_ln','out_array','out_r','out_json','out_jsonobj',
    'out_compress','out_xml','out_html','window_location','history_go','window_close','opener_location',
    'input_prompt','window_confirm'
);

$select_resources = array('__ARRAY__','__TABLES__','__SYSMSG__','__STRINGS__','__DOUBLUES__','__INTEGERS__','__QUERIES__');

$query_type_category = array(
    'single' => '한개의 데이터용',
    'multiple' => '여러개의 데이터용',
    'update' => 'DB UPDATE',
    'insert' => 'DB INSERT',
    'post' => '글쓰기 폼',
    'delete' => 'DB DELETE'
);

$input_types = array(
    'text' => 'TEXT 일반 텍스트 입력형',
    'hidden' => 'HIDDEN 항목 숨김형',
    'integer' => 'INTEGER 숫자 입력형',
    'select' => 'SELECT 메뉴 선택형',
    'radio' => 'RADIO 여러 메뉴 중 단일 선택형',
    'checkbox' => 'CHECKBOX 여러 메뉴 중 다중 선택형',
    'textarea' => 'TEXTAREA 여러 줄 텍스트 입력형',
    'wysiwyg' => 'TEXTAREA wysiwyg 편집 툴 여러 줄 입력형',
    'datepicker' => 'DATE PICKER 월 달력 날짜(년-월-일)선택형',
    'rolldate' => 'SCROLL DATE PICKER iPHONE 스타일 달력 날짜(년-월-일)선택형',
    'timepicker' => 'TIME PICKER 시계형 시간 선택(시:분)형',
    'file' => 'FILE 첨부 파일(드래그앤드롭가능)',
    'daum_postcodemap' => 'DAUM 우편번호 찾기형 좌표와 주소찾기'
);

$config_coord = array('AND','OR');
$config_condition = array('=','>=','<=','!=');
$query_params_type = array('text'=>'단순입력','keyval'=>'키와 데이터', 'wherehelper'=>'DB WHERE문 도움');
?>