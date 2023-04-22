<?php 
namespace Flex\Components\Activity;

use Flex\Components\Validation;

class Activity
{
    public function __construct(){
    }

    # 밸리데이션 체크
    public function validation (array $params) : void 
    {
        $validation = new Validation();
        foreach($params as $key => $v){
            $validation->is($key, $v);
        }
    }
}
?>