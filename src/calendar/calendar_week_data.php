<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.9
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# [세션보안]로그인 성공시 등록한 ip와 접속자 ip가 동일한지 체크한다.
if($auth->id){
	if(strcmp($_SESSION['auth_ip'],$_SERVER['REMOTE_ADDR'])){
		out_json(array('result'=>'false', 'msg_code'=>'w_authority_ip', 'msg'=>R::$sysmsg['w_authority_ip']));
	}
}

# 변수
$req = new Req;
$req->useGET();

# manifest /------------------------
# 관리자
if($req->admin && !strcmp($req->admin,'y')){
	# 로그인 상태 체크
	if(!$auth->id && _is_null($_SESSION['aduuid'])) 
		out_json(array('result'=>'false', 'msg_code'=>'w_aduuid',  'msg'=>R::$sysmsg['w_aduuid']));

	R::parserResource(_ROOT_PATH_.'/'._RES_.'/manifest_admin.json', 'manifest');
}else{
	R::parserResourceDefinedID('manifest');
}

# check document id
if(!isset(R::$manifest['feature']['calendar'][$req->doc_id])) 
	out_json(array('result'=>'false', 'msg_code'=>'e_doc_id',  'msg'=>R::$sysmsg['e_doc_id']));

# manifest
$manifest = new UtilModel(R::$manifest['feature']['calendar'][$req->doc_id]);

# config
if(!is_null($manifest->config)){
	R::parserResource(_ROOT_PATH_.'/'._CONFIG_.'/'.$manifest->config.'.json', 'config');

	# request 값 체크
	new UtilReqFormOutJson(R::$config['request']['week_data'], $req->fetch());
}
#out_r($model->fetch());

# feature
if(!isset(R::$config['feature']['week_data'])){
	out_json(array('result'=>'false', 'msg_code'=>'e_doc_id',  'msg'=>R::$sysmsg['e_doc_id'].'[week_data]'));
}

# model
$model = new UtilModel();
$model->feature  =R::$config['feature']['week_data'];
$model->config   =$manifest->config;
$model->table_id =$manifest->table;
$model->columns  ='';
$model->queries  ='';
$model->table    ='';
$model->category =array();
$model->where    ='';

$loop =array();

# column
$columns = array('id','fid');
if(!is_null($model->feature['columns']))
{
	$tmp_columns_name= $model->feature['columns'];
	R::parserResourceArray('columns', R::$config['columns'][$tmp_columns_name]);

	if(is_array(R::$columns))
	{
		foreach(R::$columns as $fieldname => $field_option){
			if($field_option['prints']){
				$columns[] = $fieldname;
			}
		}
	}
}
$model->columns = implode(',',$columns);
#out_r($model->fetch());

# queries
if(!is_null($model->feature['queries'])){
	R::parserResourceDefinedID('queries');
	$queries_str = $model->feature['queries'];
	$model->queries = R::$queries[$queries_str];
}
#out_r($model->fetch());

# tables
R::parserResourceDefinedID('tables');
if(!is_null($model->table_id)){
	if(!isset(R::$tables[$model->table_id])){
		out_json(array('result'=>'false', 'msg_code'=>'w_authority_ip', 'msg'=>R::$sysmsg['e_table_name']));
	}
	$model->table = R::$tables[$model->table_id];
}
#out_r($model->fetch());

# category
if(isset(R::$config['category'])){
	if(isset(R::$config['category']['prints']) && R::$config['category']['prints']){
	$req->category = (isset(R::$config['category'][$req->category])) ? 
		R::$config['category'][$req->category]['value'] : '';
	}
}
#out_r($model->fetch());

# 카렌다
$calendar = new Calendars($req->todate);
$calendar->set_days_of_month();

$cal_model  = new UtilModel();
$cal_model->month_days =$calendar->days_of_month;
$cal_model->srh_date   =$calendar->year.'-'.$calendar->month;
$cal_model->end_date   =$calendar->year.$calendar->month.$calendar->lastday;

# DB
$db=new DbMySqli();
$utilDataConversion = new UtilDataConversion;

# where /================================
$dbHelperWhere = new DbHelperWhere($req->field, $req->q);
$dbHelperWhere->setBuildWhere('category', '=', R::$config['category'][$req->category]['value']);
# where config
if(isset(R::$config['where']) && isset(R::$config['where']['week_data'])){
	$tmp_where = R::$config['where']['week_data'];
	if(is_array($tmp_where)){
		foreach($tmp_where as $wh_field => $wh_option){
			$wh_call_method = '';
			$wh_option_argv = (strpos($wh_option['value'],':')!==false) ? explode(':', $wh_option['value']) : array($wh_option['value'],'');
			$wh_call_method = $wh_option_argv[0];
			$wh_val =data_conversion($utilDataConversion,$wh_call_method, '', $wh_option_argv[1]);
			$dbHelperWhere->setBuildWhere($wh_field, $wh_option['symbol'], $wh_val);
		}
	}
}
if($dbHelperWhere->where) $model->where = $dbHelperWhere->where;

# 주별 쿼리
$week_model  = new UtilModel();
$week_model->depth      =0;
$week_model->w0date     =explode('-',$calendar->get_pre_week_last_date());
$week_model->w6date     =explode('-',$calendar->get_next_week_first_date());
$week_model->stimestamp =mktime(0,0,0,$week_model->w0date[1],$week_model->w0date[2],$week_model->w0date[0]);
$week_model->etimestamp =mktime(23,59,59,$week_model->w6date[1],$week_model->w6date[2],$week_model->w6date[0]);

# 일정 조회
$week_data_depth =array();
$week_data       =array();

#query
$qry=$db->query_binding($model->queries, 
	array(
		'columns' =>$model->columns,
		'tables'  =>$model->table, 
		'where'   =>sprintf("`start_date`<='%d' AND `end_date`>='%d", $week_model->etimestamp,$week_model->stimestamp),
		'req'     =>$req->fetch()
));
$rlt=$db->query($qry);
while($row=$rlt->fetch_assoc())
{
	$start_dow =($row['start_date']>$week_model->stimestamp)? __date($row['start_date'],'w') : 0;
	$end_dow   =($row['end_date']>$week_model->etimestamp) ? 6 : __date($row['end_date'],'w');
	$colspan   =($end_dow-$start_dow)+1;
	
	# 출력 모델
	$plan = new UtilModel($row);
	foreach(R::$columns as $fieldname => $field_option){
		if($field_option['prints']=='true'){
			$call_method = '';
			$field_option_argv = (strpos($field_option['value'],':')!==false) ? explode(':', $field_option['value']) : array($field_option['value'],'');
			$call_method = $field_option_argv[0];
			$plan->{$fieldname} =data_conversion($utilDataConversion,$call_method, $row[$fieldname], $field_option_argv[1]);
		}
	}
	$plan->start_pos =$start_dow;
	$plan->end_pos   =$end_dow;
	$plan->colspan   =$colspan;
	// out_ln('...........'.$start_dow.'/'.$row['title']);
	// out_r($week_data_depth);
	
	# week_data_depth 위치에 값이 없는지 먼저 체크 후 있으면 깊이 증가
	$this_use_depth = 0;
	for($wdp=0; $wdp<=$week_model->depth; $wdp++){
		if(!$week_data_depth[$wdp][$start_dow]){
			break;
		}
		else if($week_data_depth[$wdp][$start_dow]){
			$week_model->depth++;
			$this_use_depth++;
		}
		// out_ln('$this_use_depth='.$this_use_depth);
	}
	
	# week_data_depth 에 데이터 기록
	for($dow=$start_dow; $dow<=$end_dow; $dow++){
		// out_ln('..........dow='.$dow);
		$week_data_depth[$this_use_depth][$dow] = 1;
	}

	$week_data[$this_use_depth][] = $plan->fetch();
	// out_ln("<br />");
}

# output
out_json(array('result'=>'true', 'msg'=>$week_data));
?>
