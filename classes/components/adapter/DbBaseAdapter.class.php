<?php
namespace Flex\Components\Adapter;

use Flex\Annona\Db\DbMySqli;
class DbBaseAdapter extends BaseAdapter{
    public function __construct(
        public DbMySqli $db
    ){
        $this->db = new DbMySqli();
    }
}

?>