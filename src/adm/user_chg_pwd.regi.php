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
$req->usePOST();

# manifest
$Collection = new UtilConfig('document', 'modify_regi', $req->doc_id, $req->fetch());

# Validation Check
$form = new ReqForm();
$Collection->checkValidation($form);

# 권한체크
$Collection->chkAuthority($auth->id, $auth->level);

# model
$Collection->bindingModel();
$model = new UtilModel(R::$config['model']);

# db
$db=new DbMySqli();

# where 문만들기
$model->where = $Collection->getQueryWhereString($model->field, $model->q);

#query
if(isset(R::$config['query']['modify']) && isset(R::$config['query']['modify']['regi']))
{
	# query
	$request_qry = R::$config['query']['modify']['regi'];
	if(!is_null($request_qry) && !is_null($request_qry['query']))
	{
		# query
		$model->qry = $db->query_binding($request_qry['query'], array(
			'tables'  =>$model->table,
			'columns' =>$Collection->getQueryColumnsString(), 
			'where'   =>$model->where,
			'req'     =>$req->fetch()
		));
		$rlt = $db->query($model->qry);
		R::$rows =$rlt->fetch_assoc();

		// output msg
		if(!is_null($request_qry['outmsg']))
		{
			if(!$request_qry['outmsg']['mode'] && !$rlt){
				out_json(array(
					'result'=>'false',
					'msg_code'=>$request_qry['outmsg']['mode_code'],
					'msg'=>$Collection->run_binding($request_qry['outmsg']['msg'])
				));
			}else if($request_qry['outmsg']['mode'] && count(R::$rows)){
				out_json(array(
					'result'=>'false',
					'msg_code'=>$request_qry['outmsg']['mode_code'],
					'msg'=>$Collection->run_binding($request_qry['outmsg']['msg'])
				));
			}
		}
	}
}

# 비번비교
if(strcmp(R::$rows['passwd'],$Collection->password($req->passwd))){
	out_json(array('result'=>'false', 'msg_code'=>'w_password_not_match','msg'=>R::$sysmsg['w_password_not_match']));
}

# autocommit 설정
$db->autocommit(false);

# 데이터수정
$db['passwd'] = $Collection->password($req->new_passwd);
$db->update($model->table, sprintf("`id`='%s'", $auth->id));

# result query
$request_reuslt_qry = R::$config['query']['modify']['regi_result'];
if(!is_null($request_reuslt_qry))
{
	// out_r($request_reuslt_qry);
	if(is_array($request_reuslt_qry)){
		foreach($request_reuslt_qry as $euqk => $rrqv)
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
				// out_ln($rrqv_tmp_qry);
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

# 첨부파일 처리
if(isset(R::$config['query']['modify']) && isset(R::$config['query']['modify']['regi_isupfiles']))
{
	# query
	$is_upfiles_qry = $db->query_binding(R::$config['query']['modify']['regi_isupfiles'], array(
		'tables'  =>$model->table,
		'req' =>$req->fetch()
	));

	# 썸네일 만들기
	if($file_rlt = $db->query($is_upfiles_qry)){
		$utilMakeThumbnail = new UtilMakeThumbnail;
		while($file_row = $file_rlt->fetch_assoc()){
			$utilMakeThumbnail->makeThumbnail($file_row, $model->image_size['thumbnail'],$model->image_size['middle']);
		}
	}

	#files update
	$regi_upfiles_qry = $db->query_binding(R::$config['query']['modify']['regi_upfiles'], array(
		'tables'  =>$model->table,
		'req' =>$req->fetch()
	));
	$db->query($regi_upfiles_qry);
}

# execute_update_qry
$execute_update_qry = R::$config['query']['modify']['regi_execute_update_query'];
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

# commit
$db->commit();

# output
$Collection->setOutJson('result', 'true');
$Collection->bindingOutJson();
$Collection->setOutJson('msg', R::$sysmsg['v_modify']);
out_json($Collection->outjson);
?>
