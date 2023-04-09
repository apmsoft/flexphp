<?php 
namespace Flex\Components\Data\Processing;

use Flex\Components\Columns\ColumnsEnum;
use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Db\WhereHelper;

final class Fid extends Activity
{
    public function __construct(
        private string $T
    ){
        parent::__construct($this->T);
    }

    # reple 하부메뉴 및 답글에 사용
    # 다단 fid > "9999999997.01%" AND fid < "9999999997.0199";
    public function createDepth(string $fid) : string 
    {
        $fid_max = $this->table($this->T)->select(sprintf("max(%s)", ColumnsEnum::FID->value))->where(
            (new WhereHelper())->begin('AND')
                ->case(ColumnsEnum::FID->value,'>',$fid)
                ->case(ColumnsEnum::FID->value,'<',$fid.'99')
                ->end()->where
        )->query()->fetch_row();
        $depth  = ((int)substr($fid_max[0],-2) + 1);

        return sprintf("%s%02d",$fid, $depth);
    }

    # Insert 에 사용
    public function createFid() : string 
    {
        # 다단
        $fid_row = $this->table( $this->T )->select(sprintf("min(%s)", ColumnsEnum::FID->value))->query()->fetch_row();
        $_fid = (isset($fid_row[0])) ? explode('.',$fid_row[0])[0] -1 : '9999999999';
        return sprintf("%s.",$_fid);
    }

    # list
    public function countDepth(string $fid) : int 
    {
        # fid
        $fids = explode('.',$fid)[1];
        return (int) (strlen($fids) / 2);
    }

    # query
    # FID depth 정렬
    public function orderBy() : string 
    {
        return sprintf("%s+0 ASC", ColumnsEnum::FID->value);
    }
}
?>