<?php
use Fus3\Auth\AuthSession;
use Fus3\Cipher\CipherEncrypt;
use Fus3\R\R;
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Dir\DirObject;
use Fus3\Db\DbMySqli;
use Fus3\Util\UtilConfigCompiler;

# config
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

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
if($auth->level <_AUTH_SUPERDEVEL_LEVEL){
	out_json(array('result'=>'false', 'msg_code'=>'w_not_have_permission','msg'=>R::$sysmsg['w_not_have_permission']));
}

# REQUEST (POST|GET|REQUEST)
$req = new Req;
$req->useGET();

# 폼및 request값 체크
$form = new ReqForm();
$form->chkEngNumUnderline('manifid','메니페스트 ID', $req->manifid, true);
$form->chkEngNumUnderline('cfid','실행프로그램 ID', $req->cfid, true);
$form->chkEngNumUnderline('feature_column','data 키', $req->feature_column, true);
$form->chkEngNumUnderline('data id','data ID', $req->data_id, true);
$form->chkEngNumUnderline('data then id','data then ID', $req->data_thenid, true);

# model
$model = new UtilModel();
$model->find_dir = _ROOT_PATH_.DIRECTORY_SEPARATOR._CONFIG_;

# config
include_once $path.DIRECTORY_SEPARATOR._SRC_.DIRECTORY_SEPARATOR.'adm'.DIRECTORY_SEPARATOR.'manifest'.DIRECTORY_SEPARATOR.'manifest_adm'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

# resource
R::init(_LANG_);
R::parserResourceDefinedID('manifest_adm');
R::parserResourceDefinedID('tables');
R::parserResourceDefinedID('queries');
R::parserResource(_ROOT_PATH_.'/'._VALUES_.'/array.json', 'array');

# db
$db = new DbMySqli();

# config 있는지 체크
$is_flag = (R::$manifest[$req->manifid]['config'][$req->cfid]) ? true : false;
if(!$is_flag){
    out_json(array('result'=>'false', 'msg_code'=>'e_db_unenabled','msg'=>R::$sysmsg['e_db_unenabled']));
}

$config_filename = R::$manifest[$req->manifid]['config'][$req->cfid];

# read
R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/'.$req->manifid.'/'.$config_filename.'.json', 'config');

# 컴파일러
$utilConfigCompiler = new UtilConfigCompiler();
$column_args = '';
if($req->feature_column && $req->feature_column !=''){
    $column_args = R::$config['data'][$req->feature_column]['then'][$req->data_thenid];
}

# 함수에 설정된 파라메터 갯수 가져오기
// $myfuns = array();
// if(is_array($utilConfigCompiler->funs)){
//     foreach($utilConfigCompiler->funs as $fno => $func_name){
//         if(array_search($func_name, $config_funs_print_ignore) > -1){}else
//         {
//             $myfuns[$fno] = array(
//                 'func_name' => $func_name,
//                 'func_parameter' => ''
//             );

//             $func_parameter = array();
//             $ref = new ReflectionFunction($func_name);
//             foreach( $ref->getParameters() as $param) {
//                 $func_parameter[] = $param->name;
//             }

//             if(count($func_parameter)){
//                 $myfuns[$fno]['func_parameter'] = implode(',',$func_parameter);
//             }
//         }
//     }
// }

# 테이블 목록
// $tables = R::$tables;
// sort(array_keys($tables));
// $tables_args = array();
// foreach($tables as $tk => $tv){
//     $tables_args[$tk] = $tk;
// }

// $column_schemes = array();
// if(isset($column_args['scheme']) && is_array($column_args['scheme'])){
//     foreach($column_args['scheme'] as $cask => $cask_arg){
//         $column_schemes[$cask] = array();
//         foreach($cask_arg as $)
//     }
// }

# array
$r_array = array();
if(is_array(R::$r->array)){
	foreach(R::$r->array as $n => $argv){
		$jsondata = strval(json_encode($argv, JSON_UNESCAPED_UNICODE));

		$r_array[$n] = array(
			'key' => '{__ARRAY__.'.$n.'}',
			'value'=> $jsondata
		);
	}
}

# params
$r_params = array();
if(is_array($column_args['params'])){
    foreach($column_args['params'] as $pn => $pnv){
        $p_val = '';
        $p_type = 'text';
        if(is_array($pnv)){
            $p_type = 'list';
            $p_val = $pnv;
        }else {
            $p_val = trim($pnv);
        }

		$r_params[$pn] = array(
            'type' => $p_type,
            'value' => $p_val
        );
	}
}

$scheme_column_args =array(
    'query' => array(
        'type'     => 'select',
        'title'    => '쿼리구문',
        'default' => R::$queries,
        'value'    => (isset($column_args['query']) && $column_args['query']) ? strtr($column_args['query'],array('{' => '','}' => '','__QUERIES__.' => '')): ''
    ),
    'query_type' => array(
        'type'    => 'select',
        'title'   => '쿼리타입',
        'default' => $query_type_category,
        'value'   => (isset($column_args['query_type']) && $column_args['query_type']) ? $column_args['query_type']: ''
    ),
    'scheme' => array(
        'title'   => '테이터 출력퀄럼 및 데이터 값',
        'type'    => 'scheme_list',
        'default' => $column_args['scheme']
    ),
    'params' => array(
        'type' => 'params_list',
        'title' => '쿼리문 대입 인수',
        'default' => $r_params
    ),
    'outmsg' => array(
        'type'  => 'outmsg',
        'title' => '에러 메세지 설정',
        'default' => array(
            'mode' => array(
                'type'    => 'select',
                'title'   => '에러메세지 출력 조건',
                'default' => array('true' => '데이터가 있을때', 'false' => '데이터가 없을때'),
                'value' => (isset($column_args['outmsg']['mode'])) ? $column_args['outmsg']['mode'] : ''
            ),
            'msg_code' => array(
                'type'    => 'select',
                'title'   => '에러메세지',
                'default' => R::$sysmsg,
                'value'=>(isset($column_args['outmsg']['msg_code'])) ? $column_args['outmsg']['msg_code'] : ''
            )
        )
    )
);

# output
out_json(array(
    'result'           => 'true',
    'config_coord'     => $config_coord,
    'config_condition' => $config_condition,
    'input_types'      => $input_types,
    'feature_column'   => $req->feature_column,
    'arrays'           => $r_array,
	'msg'              => $scheme_column_args
));
?>
