<?php
namespace Flex\My\BBS;

use Flex\Annona\App;
use Flex\Annona\Log;
use Flex\Annona\R;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;
use Flex\Annona\Request\Request;
use Flex\Annona\Request\FormValidation;
use Flex\Annona\Model;
use Flex\Annona\Paging\Relation;
use Flex\Annona\Date\DateTimez;
use Flex\Annona\Date\DateTimezPeriod;

class Notice 
{
    DbMySqli $db;

    public function __construct()
	{
        $db = new DbMySqli();
    }

    public function doList(){

    }

    public function doPost(){

    }

    public function doInsert(){

    }

    public function doUpdate(){

    }

    public function doDelete(){

    }
}