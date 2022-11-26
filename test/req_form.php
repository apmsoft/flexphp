<?php
use Flex\App\App;
use Flex\R\R;

$path = dirname(__DIR__);
require $path. '/config/config.inc.php';

# 기본 선언
try{
    $form = \Flex\Req\ReqForm();
}catch(\Exception $e){
    echo $e->getMessage();
}

# 널값만 체크
try{
    $form = \Flex\Req\ReqForm();
    # 퀄럼명 , 퀄럼타이틀 , Request 값 , [true : 필수 입력, false : 옵션]
    $form->chkNull('id','식별번호',$_GET['id'],true);
    $form->chkName('userid','아이디',$_GET['userid'],true);
}catch(\Exception $e){
    echo $e->getMessage().PHP_EOL;
    print_r(json_encode($e->getMessage(),true));
}

echo "PASS";
echo PHP_EOL;
?>
