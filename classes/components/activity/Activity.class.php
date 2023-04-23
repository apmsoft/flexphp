<?php 
namespace Flex\Components\Activity;

use Flex\Annona\Adapter\BaseAdapter;

class Activity extends BaseAdapter
{
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