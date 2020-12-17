<?php
/* ======================================================
| @Author	: 김종관
| @Email	: apmsoft@gmail.com
| @HomePage	: fancyupsoft.com
| @Editor	: VSCode
| @UPDATE	: 0.8.3
|||||||||||||||||||||||||||||||
|||||     |||||  ||||||||||  ||
||||   ||||||||  ||||||||||  ||
|          ||||  ||||||||||  ||
||||| |||||||||  ||||||||||  ||
||||| ||||||||||  ||||||||  |||
||||| ||||||||||||        |||||
|||||||||||||||||||||||||||||||
-------------------------------
| copyright@ fancyupsoft.com
----------------------------------------------------------*/
$path = str_replace($_SERVER['PHP_SELF'],'',__FILE__);
include_once $path.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.inc.php';

# use
use Fus3\Auth\AuthSession;
use Fus3\Req\Req;
use Fus3\Util\UtilModel;
use Fus3\Db\DbMySqli;
use Fus3\Template\Template;
use Fus3\Template\TemplateVariable;
use Fus3\R\R;

# 세션
$auth=new AuthSession($app['auth']);
$auth->sessionStart();

# 변수
$req = new Req;
$req->useGET();

# model
$model = new UtilModel();
$model->tpl_dir = '';
$model->layout  = array();
$og = array();

# db
$db = new DbMySqli();

# resources
R::parserResourceDefinedID('manifest');
R::parserResourceDefinedID('tables');
R::parserResource(_ROOT_PATH_.DIRECTORY_SEPARATOR._LAYOUT_.DIRECTORY_SEPARATOR.'layout.json', 'layout');

#환경설정 파일 체크
$activity = $req->act;
if(!$req->act){
    $activity = 'index';
}

# layout info
$model->layout = R::$layout[$activity];

# set og default info
$og['title']        = ($model->layout['title']) ? $model->layout['title'] : R::$strings['app_name'];
$og['url']          = ($model->layout['url']) ? $model->layout['url'] : _SITE_HOST_;
$og['description']  = ($model->layout['description']) ? $model->layout['description'] : '';
$og['image']        = ($model->layout['image']) ? $model->layout['image'] : R::$strings['app_name'];
$og['image_width']  = ($model->layout['image_width']) ? $model->layout['image_width'] : 200;
$og['image_height'] = ($model->layout['image_height']) ? $model->layout['image_height'] : 200;

# template 디렉토리 자동 설정
if(strpos($model->layout['filename'], DIRECTORY_SEPARATOR) !==false){
	$tmp_path = explode(DIRECTORY_SEPARATOR, $model->layout['filename']);
	$model->tpl_dir = DIRECTORY_SEPARATOR._LANG_.DIRECTORY_SEPARATOR.$tmp_path[0];
}

# template 선언 /----------------------
try{
	$tpl = new Template(_ROOT_PATH_.DIRECTORY_SEPARATOR._ASSETS_.DIRECTORY_SEPARATOR.$model->layout['filename']);
}catch(Exception $e){
	throw new ErrorException($e->getMessage(),__LINE__);
}

# tpl 변수
$tpl['strings']      = R::$strings;
$tpl['og']           = $og;
$tpl['http_referer'] = (!_is_null($app->http_referer))? $app->http_referer : _SITE_HOST_;
$tpl['auth']		 = ($auth->id) ? $auth->id : '';

# prints
$tpl->compile_dir =_ROOT_PATH_.DIRECTORY_SEPARATOR._TPL_.$model->tpl_dir;
#$tpl->compile     = true;
#$tpl->compression = false;
out_html($tpl->display());
?>
