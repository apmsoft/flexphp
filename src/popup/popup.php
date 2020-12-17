<?php
use Fus3\Req\Req;
use Fus3\Req\ReqForm;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\R\R;

$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# 변수
$req = new Req;
$req->useGET();

# model
$model = new UtilModel();
$model->timestamp = date('Y-m-d');
$data = array();

# db
$db = new DbMySqli();

#resource
R::parserResourceDefinedID('tables');

# 아이디중복체크
$qry = sprintf("SELECT id,title,extract_id,start_date,end_date,link FROM `%s` WHERE `start_date` <= '%s' AND `end_date` >= '%s' ORDER  BY Rand() LIMIT  1",
	R::$tables['popup'], 
		$model->timestamp,
			$model->timestamp);
$rlt = $db->query($qry);
while($row = $rlt->fetch_assoc())
{
	if(isset($row['id']))
	{
		$listUtil = new UtilModel($row);
		$listUtil->image = '';

		# 이미지
		$image_row = $db->get_record("id,directory,sfilename", R::$tables['popupfiles'], sprintf("extract_id='%s' AND `is_regi`='1'", $listUtil->extract_id));
		if(isset($image_row['id'])){
			$listUtil->image = _SITE_HOST_.$image_row['directory'].'/'.$image_row['sfilename'];
		}

		# RESULT
		unset($listUtil->extract_id);
		$data[] = $listUtil->fetch();

		# view count
		$db->query(sprintf("UPDATE %s SET view_count=view_count+1 WHERE `id`='%s'", R::$tables['popup'], $listUtil->id));
	}
}

# output
out_json(array(
	'result' =>'true',
	'msg'    =>$data
));
?>