<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: http://apmsoft.tistory.com
| @Editor	: Sublime Text 3
| @UPDATE	: 0.1
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 변수
$req = new Req;
$req->useGET();

# manifest
$Collection = new UtilConfig('document', 'list', $req->doc_id, $req->fetch());

# Validation Check
$form = new ReqForm();
$Collection->checkValidation($form);

# 권한체크
$Collection->chkAuthority($auth->id, $auth->level);

# model
$Collection->bindingModel();
$model = new UtilModel(R::$config['model']);
$model->todate = ($req->todate) ? $req->todate : __date('now','Y-m-d');

# db
$db = new DbMySqli();

# query
$loop = array();
if(isset(R::$config['query']['list']) && isset(R::$config['query']['list']['request']))
{
	# query
	$request_qry = R::$config['query']['list']['request'];
}

# where 문만들기
$model->where = $Collection->getQueryWhereString($model->field, $model->q);

# 카렌다
$calendar = new Calendars($req->todate);
$calendar->set_days_of_month();

$cal_model  = new UtilModel();
$cal_model->month_days =$calendar->days_of_month;
$cal_model->srh_date   =$calendar->year.'-'.$calendar->month;
$cal_model->end_date   =$calendar->year.$calendar->month.$calendar->lastday;

# 주별 쿼리
$cal_model->week_count = count($cal_model->month_days);
for($wx=0; $wx<$cal_model->week_count; $wx++)
{
	$week =&$cal_model->month_days[$wx];

	$week_model  = new UtilModel();
	$week_model->depth      =0;
	$week_model->w0date     =explode('-',$week[0]['date']);
	$week_model->w6date     =explode('-',$week[6]['date']);
	$week_model->stimestamp =mktime(0,0,0,$week_model->w0date[1],$week_model->w0date[2],$week_model->w0date[0]);
	$week_model->etimestamp =mktime(23,59,59,$week_model->w6date[1],$week_model->w6date[2],$week_model->w6date[0]);	

	# where
	$_wh0=sprintf("`start_date`<='%s' AND `end_date`>='%s'", $week[6]['date'],$week[0]['date']);
	$_wh1 = ($model->where) ? '('.$model->where.') AND ('.$_wh0.')' : $_wh0;

	# 일정 조회
	$qry=$db->query_binding($request_qry['query'], array(
		'tables'  =>$model->table,
		'columns' =>$Collection->getQueryColumnsString(),
		'where'   =>$_wh1,
		'limit'   =>array(),
		'req'     =>$req->fetch()
	));

	$rlt=$db->query($qry);
	$week_data_depth =array();
	$week_data       =array();
	// out_ln("---------------------------");
	while(R::$rows=$rlt->fetch_assoc())
	{
		$start_dow =(R::$rows['start_date']>$week[0]['date'])? __date(strtotime(R::$rows['start_date']),'w') : 0;
		$end_dow   =(R::$rows['end_date']>$week[6]['date']) ? 6 : __date(strtotime(R::$rows['end_date']),'w');
		$colspan   =($end_dow-$start_dow)+1;
		
		# 출력 모델
		$plan = new UtilModel(R::$rows);
		foreach(R::$columns as $fieldname => $field_option){
			if(!is_null($field_option['value'])){
				if($fieldname == 'fid'){
				}else{
					$plan->{$fieldname} = $Collection->columnsBinding(R::$rows[$fieldname], $field_option['value']);
				}
				
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
		

		# result query
		$request_reuslt_qry = R::$config['query']['list']['request_result'];
		if(!is_null($request_reuslt_qry))
		{
			// out_r($request_reuslt_qry);
			if(is_array($request_reuslt_qry)){
				foreach($request_reuslt_qry as $rrqk => $rrqv)
				{
					$rrqv_var   = $rrqv['var'];
					$rrqv_rows	= array();
					if(!is_null($rrqv_var)){
						R::$rows[$rrqv_var] = array();
					}

					// query
					if(!is_null($rrqv['query']))
					{
						$rrqv_tmp_qry = $db->query_binding($rrqv['query'],array(
							'tables'  =>$model->table,
							'req' =>$req->fetch()
						));
						if($rrqv_tmp_rlt =$db->query($rrqv_tmp_qry))
						{
							while($rrqv_tmp_row = $rrqv_tmp_rlt->fetch_assoc())
							{
								// output msg
								if(!is_null($rrqv['outmsg'])){
									if(!$rrqv['outmsg']['mode'] && !$rrqv_tmp_row){
										out_json(array('result'=>'false','msg_code'=>$rrqv['outmsg']['msg_code'],'msg'=>$Collection->run_binding($rrqv['outmsg']['msg'])));
									}
									else if($rrqv['outmsg']['mode'] && $rrqv_tmp_row){
										out_json(array('result'=>'false','msg_code'=>$rrqv['outmsg']['msg_code'],'msg'=>$Collection->run_binding($rrqv['outmsg']['msg'])));
									}
								}

								$rrqv_rows[] = $rrqv_tmp_row;
							}
							if(!is_null($rrqv_var)){
								R::$rows[$rrqv_var] = $rrqv_rows;
							}
							unset($rrqv_rows);
						}
					}
				}
			}
		}
	}
	// out_r($week_data_depth);
	if(isset($week_data[0])){
		$loop[$wx] = $week_data;
	}
	// out_ln("---------------------------");
}

# output
# execute_update_qry
$execute_update_qry = R::$config['query']['list']['request_execute_update_query'];
if(!is_null($execute_update_qry))
{
	// out_r($request_reuslt_qry);
	if(is_array($execute_update_qry)){
		foreach($execute_update_qry as $euqk => $euqv)
		{
			// query
			if(!is_null($euqv['query']))
			{
				$euq_qry = $db->query_binding($euqv['query'], array(
					'tables'  =>$model->table,
					'req'     =>$req->fetch()
				));
				$db->query($euq_qry);
			}
		}
	}
}
 
# output
$Collection->bindingOutJson();
$Collection->setOutJson('result', 'true');
$Collection->setOutJson('msg', $loop);
out_json($Collection->outjson);
?>
