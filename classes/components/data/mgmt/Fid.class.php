<?php
namespace Flex\Components\Data\Mgmt;

use Flex\Annona\Db\DbMySqli;

# 스트링 다단 처리
class Fid
{
    const __VERSION = "1.0";
    /**
     * @ T : table
     * @ db : 디비 클래스 인스턴스
     * @ column_name : 퀄명명
     */
    public function __construct(
        private string $T,
        private DbMySqli $db,
        private string $column_name
    ){}

    # reple 하부메뉴 및 답글에 사용
    # 다단 fid > "9999999997.01%" AND fid < "9999999997.0199";
    public function createChildFid(string $fid) : string
    {
        # 해당 fid 중 가장 큰값 찾기
        $fid_max = $this->db->table($this->T)
            ->select(sprintf("max(%s)", $this->column_name))
            ->where([$this->column_name,'>',$fid],[$this->column_name,'<',$fid.'99'])
            ->query()->fetch_row();

        # depth
        $depth  = ((int)substr($fid_max[0],-2) + 1);

        # result
        return sprintf("%s%02d",$fid, $depth);
    }

    # Insert 에 fid 키 만들기
    public function createParentFid() : string
    {
        # fid min 값 가져오기
        $fid_row = $this->db->table( $this->T )
            ->select(sprintf("min(%s)", $this->column_name))->query()->fetch_row();

        # make fid
        $_fid = (isset($fid_row[0])) ? explode('.',$fid_row[0])[0] -1 : '9999999999';
        return sprintf("%s.",$_fid);
    }

    # 현재 뎁스 길이
    public function getDepthCount(string $fid) : int
    {
        # fid
        $fids = (strpos($fid,".") !==false) ? explode('.',$fid)[1] : '';
        return (int) (strlen($fids) / 2);
    }

    # list query
    # FID depth 정렬
    public function orderBy(?string $asc = 'ASC') : string
    {
        return sprintf("%s+0 %s", $this->column_name, $asc);
    }
}
?>