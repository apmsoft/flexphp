<?php
/* ======================================================
| @Author   : 김종관
| @Email    : apmsoft@gmail.com
| @HomePage : http://fancy-up.tistory.com
| @Editor   : Sublime Text 3 (기본설정)
| @UPDATE   : 0.5
| @TITLE    : php 개발 가이드 (종합)
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.ftp.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 로그인 상태 체크
if(!$auth->id || _is_null($_SESSION['aduuid'])){
    out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 관리자인지 체크
$cipherEncrypt = new CipherEncrypt($auth->id.$_SESSION['auth_ip']);
if(strcmp($cipherEncrypt->_md5_utf8encode(),$_SESSION['aduuid'])){
    out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# 레벨체크
if($auth->level <_AUTH_SUPERADMIN_LEVEL){
    out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->usePOST();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkNull('title', '일정[캘린더]명', $req->title, true);
$form->chkEngNumUnderline('id', '일정[캘린더]ID', $req->id, true);

# Model
$model = new UtilModel();
$model->category = 'calendar';
$model->manifest_document = array();
$model->new_table = 'fu2_cal_'.$req->id;
$model->config_temp_path   = _ROOT_PATH_.'/'._DATA_.'/'.$model->category.'_'.$req->id.'.json';
$model->config_real_path   = _FTP_DIR_.'/'._CONFIG_.'/'.$model->category.'_'.$req->id.'.json';
$model->table_temp_path    = _ROOT_PATH_.'/'._DATA_.'/tables.json';
$model->table_real_path    = _FTP_DIR_.'/'._QUERY_.'/tables.json';
$model->manifest_temp_path = _ROOT_PATH_.'/'._DATA_.'/manifest.json';
$model->manifest_real_path = _FTP_DIR_.'/'._RES_.'/manifest.json';

# db 선언 및 접속
$db = new DbMySqli();

# resource
R::parserResourceDefinedID('tables');
R::parserResourceDefinedID('manifest');

# 테이블이 있는지 체크
$tables = array();
if($rlt = $db->query(sprintf("SHOW TABLES FROM `%s`", _DB_NAME_))){
    while($row = $rlt->fetch_row()){
        $tables[] = $row[0];
    }
}

# 없으면 생성 : 테이블 생성
if(!in_array($model->new_table, $tables)){
# 캘린더 스키마
$schema = <<<CAL_SCHEMA
CREATE TABLE `{$model->new_table}` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `muid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) DEFAULT NULL,
  `category` varchar(10) NOT NULL DEFAULT '',
  `view_count` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_count` int(10) unsigned NOT NULL DEFAULT '0',
  `signdate` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `extract_id` varchar(60) DEFAULT NULL COMMENT '첨부파일id',
  PRIMARY KEY (`id`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CAL_SCHEMA;
    
$db->query($schema);
}

# ftp 클랙스
$ftp = new Ftp();

# tables.json 추가
if(!isset(R::$tables[$req->id]) || !R::$tables[$req->id]){    
    if($file_table = $ftp->open_file_read($model->table_temp_path, $model->table_real_path))
    {
        # 쓰기
        R::$tables[$req->id] = $model->new_table;
        $context_tables = json_encode(R::$tables);
        if(!$ftp->open_file_write($model->table_temp_path, $model->table_real_path, $context_tables)) {
            out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!11'));
        }
    }else{
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!12'));
    }
}

# manifest.json 추가
if(!isset(R::$manifest['feature']['document'][$req->id]) || !R::$manifest['feature']['document'][$req->id])
{    
    # document
    R::$manifest['feature']['document'][$req->id] = array(
        "category" => $model->category,
        'title'    => $req->title,
        "table"    => $req->id, 
        "config"   => $model->category.'_'.$req->id
    );

    # uploadfiles
    R::$manifest['feature']['uploadfiles'][$req->id] = array(
        'title'    => $req->title,
        "table"    => 'uploadfiles', 
        "config"   => 'uploadfiles'
    );
    
    if($file_manifest = $ftp->open_file_read($model->manifest_temp_path, $model->manifest_real_path))
    {
        # 쓰기
        $context_manifest = json_encode(R::$manifest);
        if(!$ftp->open_file_write($model->manifest_temp_path, $model->manifest_real_path, $context_manifest)) {
            out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!21'));
        }
    }else{
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!22'));
    }
}

# config, new create josn file
if (!file_exists($model->config_real_path)) 
{

$context_config = <<< CONFIG
{    
    // Validation Check
    "validation":{
        "list":{
          "todate" :{"title":"날짜", "required":true, "filter":"chkDateFormat"}
        },
        "write_regi" :{
            "category"   :{"title":"카테고리", "required":true, "filter":"chkNull"},
            "title"      :{"title":"제목", "required":true, "filter":"chkNull"},
            "start_date" :{"title":"시작일", "required":true, "filter":"chkDateFormat"},
            "end_date"   :{"title":"종료일", "required":true, "filter":"chkDateFormat"},
            "description":{"title":"내용", "required":true, "filter":"chkNull"}
        },
        "view" :{
            "id" :{"title":"데이터식별번호", "required":true, "filter":"chkNumber"}
        },
        "modify" :{
            "id" :{"title":"데이터식별번호", "required":true, "filter":"chkNumber"}
        },
        "modify_regi" :{
            "category"   :{"title":"카테고리", "required":true, "filter":"chkNull"},
            "title"      :{"title":"제목", "required":true, "filter":"chkNull"},
            "start_date" :{"title":"시작일", "required":true, "filter":"chkDateFormat"},
            "end_date"   :{"title":"종료일", "required":true, "filter":"chkDateFormat"},
            "description":{"title":"내용", "required":true, "filter":"chkNull"}
        },
        "delete_regi" :{
            "id"    :{"title":"데이터식별번호", "required":true, "filter":"chkNumber"}
        }
    },

    // model
    "model" : {
        "authority" : {                 // 권한등급
            "list"      : 0,
            "view"      : 0,
            "modify"    : 100,
            "write"     : 100,
            "delete"    : 100
        },
        
        "image_size" :{
            "thumbnail":{"width":110, "height":110},
            "middle"   :{"width":616, "height":616}
        },
        "categoryg" :{
            "#0288D1": "#0288D1",
            "#5BC0DE": "#5BC0DE",
            "#D9534F": "#D9534F",
            "#FA573C": "#FA573C",
            "#7BD148": "#7BD148",
            "#92e1c0": "#92e1c0",
            "#9FE1E7": "#9FE1E7",
            "#A47AE2": "#A47AE2",
            "#FFAD46": "#FFAD46",
            "#42D692": "#42D692",
            "#C2C2C2": "#C2C2C2",
            "#CABDBF": "#CABDBF",
            "#D06B64": "#D06B64"
        }
    },

    // where 조건문 만들기
    "where" : {
        "list" :{
          "category" :{"title":"카테고리", "condition":"=","value":"{req.category}", "coord":"AND"}
        }
    },

    // query문
    "query" :{
        "list":{
            "total"  : "SELECT count(id) FROM `{tables}` [WHERE, {where}]",
            "request":{
                "query":"SELECT {columns} FROM `{tables}` [WHERE, {where}] ORDER BY `start_date` ASC",
                "outmsg":null
            },
            "request_result":null,
            "request_execute_update_query":null
        },

        "view" :{
            "request":{
                "query":"SELECT {columns} FROM `{tables}` WHERE `id`={req.id}",
                "outmsg":{"mode":false,"msg_code":"e_db_unenabled","msg":"{sysmsg.e_db_unenabled}"}
            },
            "request_result":[
                {
                    "query":"SELECT id,file_type,directory,sfilename,ofilename FROM `{tables.uploadfiles}` WHERE extract_id='{columns.extract_id}' AND `is_regi`='1' ORDER BY id ASC",
                    "outmsg":null,
                    "var"   :"upfiles",
                    "title" :"첨부파일"
                }
            ],
            
            "pre_view"  : null,
            "next_view" : null,

            "request_execute_update_query":[
                {
                    "query":"UPDATE `{tables}` SET view_count=view_count+1 WHERE `id`='{req.id}'",
                    "title" :"뷰카운트증가"
                }
            ]
        },

        "write":{
            "request":null,
            "request_result":null,
            "request_execute_update_query":null,

            "regi":null,
            "regi_result":null,
            "regi_isupfiles": "SELECT id,file_type,directory,sfilename FROM `{tables.uploadfiles}` WHERE extract_id='{req.extract_id}' AND `is_regi`<'1'",
            "regi_upfiles"  : "UPDATE `{tables.uploadfiles}` SET `is_regi`='1' WHERE extract_id='{req.extract_id}' AND `is_regi`<'1'",
            "regi_execute_update_query":null
        },

        "modify":{
            "request":{
                "query":"SELECT {columns} FROM `{tables}` WHERE `id`='{req.id}'",
                "outmsg":{"mode":false,"msg_code":"e_db_unenabled","msg":"{sysmsg.e_db_unenabled}"}
            },
            "request_result":[
                {
                    "query":"SELECT id,file_type,directory,sfilename FROM `{tables.uploadfiles}` WHERE extract_id='{columns.extract_id}'",
                    "outmsg":null,
                    "var"   :"upfiles",
                    "title" :"첨부파일"
                }
            ],
            "request_execute_update_query":null,

            "regi" :  {
                "query":"SELECT {columns} FROM `{tables}` WHERE `id`='{req.id}'",
                "outmsg":{"mode":false,"msg_code":"e_db_unenabled","msg":"{sysmsg.e_db_unenabled}"}
            },
            "regi_result":null,

            // 첨부파일
            "regi_isupfiles": "SELECT id,file_type,directory,sfilename FROM `{tables.uploadfiles}` WHERE extract_id='{columns.extract_id}' AND `is_regi`<'1'",
            "regi_upfiles"  : "UPDATE `{tables.uploadfiles}` SET `is_regi`='1' WHERE extract_id='{columns.extract_id}' AND `is_regi`<'1'",
            "regi_execute_update_query":null
        },

        "delete":{
            "request" : {
                "query":"SELECT {columns} FROM `{tables}` WHERE `id`='{req.id}'",
                "outmsg":{"mode":false,"msg_code":"e_db_unenabled","msg":"{sysmsg.e_db_unenabled}"}
            },
            "request_result":null,
            "request_execute_update_query":null,

            "regi" : {
                "query":"SELECT {columns} FROM `{tables}` WHERE `id`='{req.id}'",
                "outmsg":{"mode":false,"msg_code":"e_db_unenabled","msg":"{sysmsg.e_db_unenabled}"}
            },
            "regi_result":null,

            // 첨부파일
            "regi_isupfiles"  : "SELECT id,file_type,directory,sfilename FROM `{tables.uploadfiles}` WHERE extract_id='{columns.extract_id}'",
            "regi_upfiles"    : "DELETE FROM `{tables.uploadfiles}` WHERE extract_id='{columns.extract_id}'",
            "regi_execute_update_query":null
        }
    },

    // 퀄럼
    "columns":{
        "list" :{
            "id"           :{"title":"고유번호", "value":"integer()"},
            "muid"         :{"title":"회원번호", "value":"integer()"},
            "title"        :{"title":"제목", "value":"str_cut(70)"},
            "category"     :{"title":"카테고리", "value":"text()"},
            "start_date"   :{"title":"시작일", "value":"text()"},
            "end_date"     :{"title":"종료일", "value":"text()"},
            "signdate"     :{"title":"등록일", "value":"dateformat(Y.m.d)"},
            "extract_id"   :{"title":"파일키", "value":"text()"}
        },
        "view" :{
            "id"           :{"title":"고유번호", "value":"integer()"},
            "muid"         :{"title":"회원번호", "value":"text()"},
            "category"        :{"title":"카테고리", "value":"text()"},
            "start_date"   :{"title":"시작일", "value":"text()"},
            "end_date"     :{"title":"종료일", "value":"text()"},
            "title"        :{"title":"제목", "value":"text()"},
            "description"  :{"title":"내용", "value":"contextType(XSS)"},
            "view_count"   :{"title":"조회수", "value":"number_format()"},
            "signdate"     :{"title":"등록일", "value":"dateformat(Y/m/d H:i)"},
            "extract_id"   :{"title":"파일키", "value":"text()"}
        },
        "modify" :{
            "id"         :{"title":"고유번호", "type":"hidden","value":"integer()"},
            "category"   :{"title":"카테고리", "type":"select","value":"text()"},
            "title"      :{"title":"제목", "type":"text","value":"text()"},
            "start_date" :{"title":"시작일", "type":"date","value":"text()"},
            "end_date"   :{"title":"종료일", "type":"date","value":"text()"},            
            "description":{"title":"내용", "type":"textarea","value":"text()"},
            "extract_id" :{"title":"파일키", "type":"hidden","value":"text()"}
        },
        "modify_regi" :{
            "id"         :{"title":"고유번호", "value":"integer()"},
            "category"   :{"title":"카테고리", "value":"text()"},
            "start_date" :{"title":"시작일", "value":"text()"},
            "end_date"   :{"title":"종료일", "value":"text()"},
            "title"      :{"title":"제목", "value":"text()"},
            "description":{"title":"내용", "value":"urldecode()"},
            "extract_id" :{"title":"파일키", "value":"text()"}
        },
        "write" :{
            "category"   :{"title":"카테고리", "type":"select","value":"text()"},
            "title"      :{"title":"제목", "type":"text","value":"text()"},            
            "start_date" :{"title":"시작일", "type":"date","value":"text()"},
            "end_date"   :{"title":"종료일", "type":"date","value":"text()"},
            "description":{"title":"내용", "type":"textarea","value":"text()"},
            "extract_id" :{"title":"파일키", "type":"hidden","value":"create_upload_token()"}
        },
        "write_regi" :{
            "muid"       :{"title":"회원번호", "value":"integer({_SESSION.auth_id})"},
            "category"   :{"title":"카테고리", "value":"text()"},
            "start_date" :{"title":"시작일", "value":"text()"},
            "end_date"   :{"title":"종료일", "value":"text()"},
            "title"      :{"title":"제목", "value":"text()"},
            "description":{"title":"내용", "value" :"urldecode()"},
            "signdate"   :{"title":"등록일", "value":"timestamp()"},
            "extract_id" :{"title":"파일키", "value":"create_upload_token()"}
        },
        "delete_regi" :{
            "id"         :{"title":"고유번호", "value":"integer()"},
            "muid"       :{"title":"회원번호", "value":"integer({_SESSION.auth_id})"},
            "extract_id" :{"title":"파일키", "value":"text()"}
        }
    },

    // 리턴 json 추가 파라메터
    "outjson" : {
        "list" :{
            "category_navi":{"title":"카테고리배열", "value":"{model.categoryg}"},
            "category"     :{"title":"카테고리", "value" : "{req.category}"},
            "title"        :{"title":"타이틀", "value":"{model.title}"},
            "authority"    :{"title":"모드별권한", "value":"{model.authority}"},
            "authlevel"    :{"title":"등급", "value":"integer({_SESSION.auth_level})"},
            "admlevel"     :{"title":"ADM", "value":"integer({_DEFINE._AUTH_SUPERADMIN_LEVEL})"}
        },
        "write" : {
            "category_navi" :{"title":"카토고리배열", "value":"{model.categoryg}"},
            "title"         :{"title":"타이틀", "value":"[{model.title}, 쓰기]"}
        },
        "modify" : {
            "category_navi" :{"title":"카토고리배열", "value":"{model.categoryg}"},
            "title"         :{"title":"타이틀", "value":"[{model.title}, 수정]"}
        },
        "view" :{
            "category_navi":{"title":"카테고리배열", "value":"{model.categoryg}"},
            "title"        :{"title":"타이틀", "value":"[{model.title}, 글뷰]"},
            "authority"    :{"title":"모드별권한", "value":"{model.authority}"},
            "auth_id"      :{"title":"auth_id", "value":"integer({_SESSION.auth_id})"},
            "authlevel"    :{"title":"등급", "value":"integer({_SESSION.auth_level})"},
            "admlevel"     :{"title":"ADM", "value":"integer({_DEFINE._AUTH_SUPERADMIN_LEVEL})"}
        }
    }
}
CONFIG;

    $piStorage = new PreferenceInternalStorage($model->config_temp_path,'w');
    if($piStorage->writeInternalStorage($context_config)){
        # 쓰기
        if(!$ftp->open_file_write($model->config_temp_path, $model->config_real_path, $context_config)) {
            out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!31'));
        }
    }else{
        out_json(array('result'=>'false', 'msg'=>'open_file_write() failed!33'));
    }
}

# adm log
Log::init(R::$tables['adm_log']);
Log::d( sprintf("cal CREATE ID : %s, TITLE : %s ", $req->id, $req->title) );

# output
out_json(array(
    'result' =>'true',
    'msg'    =>R::$sysmsg['v_write']
));
?>
