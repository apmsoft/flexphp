<?php
namespace Flex\Components\Adapter;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;
use Flex\Annona\Db\WhereHelperInterface;

class DbMySqlAdapter extends BaseAdapter{
    public WhereHelperInterface $whereHelper;
    public function __construct(
        public DbMySqli $db,
        ?WhereHelperInterface $whereHelper = null
    ){
        # WhereHelper 를 상속은 커스텀 클래스 등록 가능
        $this->whereHelper = $whereHelper ?? new WhereHelper();
    }
}

?>