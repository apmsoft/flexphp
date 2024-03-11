<?php
namespace Flex\Components\Data\Mgmt;

use Flex\Annona\Db\DbMySqli;
use Flex\Annona\Log;

# 스트링 다단 처리
class Fid
{
    public const __version = "1.3";
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
    # 다단 fid > "9999999997.01" AND fid < "9999999997.0199";
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

    # depth 깊이 만큼 가계 뽑아주기
    public function getFidGenealogy(string $fid) : array
    {
        # result
        $result = [];

        # depth
        $depth = $this->getDepthCount($fid);

        # category argv
        $root_pos = strpos($fid,".");
        if($root_pos !==false){
            $start_pos = $root_pos + 1;
            for($i=0; $i<=$depth; $i++){
                $end_pos = $start_pos + ($i*2);
                $result[] = substr($fid, 0, $end_pos);
            }
        }

    return $result;
    }

    # list query
    # FID depth 정렬
    public function orderBy(?string $asc = 'ASC') : string
    {
        return sprintf("%s+0 %s", $this->column_name, $asc);
    }

    # sort down (화살표 위)
    public function getSortDown (string $fid) : array
    {
        # current fid array
        $cur_fids = [];
        $cur_rlt = $this->db->table($this->T)
            ->where($this->column_name, 'LIKE-R', $fid)
            ->orderBy( $this->orderBy() )
            ->query();
        while($cur_row = $cur_rlt->fetch_assoc()){
            $cur_fids[] = $cur_row;
        }

        # < fid array
        $pre_fids = [];
        $depth = $this->getDepthCount($fid);
        $fids = explode('.',$fid);

        $pre_fid = '';
        if($depth < 1){
            $pre_fid = ($fids[0]-1).".";
        }else {
            $end_2str = substr($fid,-2);
            if($end_2str != '01'){
                $end_fid = ((int)$end_2str - 1);
                $pre_fid = sprintf("%s%02d",substr($fid,0,-2),$end_fid);
            }
        }

        if($pre_fid)
        {
            // Log::d('pre_fid ->',$pre_fid);
            $pre_row = $this->db->table($this->T)
                ->select($this->column_name)
                ->where($this->column_name, '>=', $pre_fid)
                ->limit(1)
                ->orderBy( $this->orderBy() )
                ->query()->fetch_assoc();
            if(isset($pre_row[$this->column_name]))
            {
                # pre_fid
                $pre_fid = $pre_row[$this->column_name];
                $pre_depth = $this->getDepthCount($pre_fid);
                // Log::d('pre_fid', $pre_fid,'pre_depth',$pre_depth);

                if($depth == $pre_depth)
                {
                    # query
                    $pre_rlt = $this->db->table($this->T)
                        ->where($this->column_name, 'LIKE-R', $pre_fid)
                        ->orderBy( $this->orderBy() )
                        ->query();
                    while($pre_row = $pre_rlt->fetch_assoc()){
                        $pre_fids[] = $pre_row;
                    }
                    // Log::d('pre_fids',$pre_fids);
                }
            }
        }

        return [
            'cur_fids' => $cur_fids,
            'ano_fids' => $pre_fids
        ];
    }

    # sort up (화살표 다운)
    public function getSortUp (string $fid) : array
    {
        # current fid array
        $cur_fids = [];
        $cur_rlt = $this->db->table($this->T)
            ->where($this->column_name, 'LIKE-R', $fid)
            ->orderBy( $this->orderBy() )
            ->query();
        while($cur_row = $cur_rlt->fetch_assoc()){
            $cur_fids[] = $cur_row;
        }
        // Log::d('cur_fids',$cur_fids);

        # > fid array
        $nxt_fids = [];
        $depth = $this->getDepthCount($fid);
        $fids = explode('.',$fid);
        $nxt_fid = ($depth < 1) ? ($fids[0]+1)."." : sprintf("%s%02d",substr($fid,0,-2),((int)substr($fid,-2) + 1));
        // Log::d('nxt_fid ->',$nxt_fid);
        $nxt_row = $this->db->table($this->T)
            ->select($this->column_name)
            ->where($this->column_name, '>=', $nxt_fid)
            ->limit(1)
            ->orderBy( $this->orderBy() )
            ->query()->fetch_assoc();
        if(isset($nxt_row[$this->column_name]))
        {
            # nxt_fid
            $nxt_fid = $nxt_row[$this->column_name];
            $nxt_depth = $this->getDepthCount($nxt_fid);
            // Log::d('nxt_fid', $nxt_fid,'nxt_depth',$nxt_depth);

            if($depth == $nxt_depth)
            {
                # query
                $nxt_rlt = $this->db->table($this->T)
                    ->where($this->column_name, 'LIKE-R', $nxt_fid)
                    ->orderBy( $this->orderBy() )
                    ->query();
                while($nxt_row = $nxt_rlt->fetch_assoc()){
                    $nxt_fids[] = $nxt_row;
                }
                // Log::d('nxt_fids',$nxt_fids);
            }
        }

        return [
            'cur_fids' => $cur_fids,
            'ano_fids' => $nxt_fids
        ];
    }

    # 데이터베이스의 fid 값 변경 하기
    public function changeSortFid (array $cur_fids, array $ano_fids, string $using_where_key='id') : array
    {
        $result = [];
        # fid change
        if(count($cur_fids) && count($ano_fids))
        {
            # change cur -> ano
            $ano_root_fid = $ano_fids[0][$this->column_name];
            $ano_depth = $this->getDepthCount($ano_root_fid);
            // Log::d('ano', $ano_root_fid, $ano_depth);
            $ano_parent_fid = ($ano_depth<1) ? (explode('.',$ano_root_fid))[0]."." : $ano_root_fid;

            # db update
            $this->db->autocommit(FALSE);
            foreach($cur_fids as $cur_fid)
            {
                $this_fid = $cur_fid[$this->column_name];
                $cur_depth = $this->getDepthCount($this_fid);
                $cur_update_fid = ($ano_depth ==$cur_depth) ? $ano_parent_fid: sprintf("%s%s",$ano_parent_fid,substr($this_fid,($cur_depth-$ano_depth)*-2));
                // Log::d('cur -> ano', $this_fid,'->',$cur_update_fid);
                $result[] = sprintf("cur => ano : %s -> %s", $this_fid,$cur_update_fid);
                try{
                    $this->db[$this->column_name] = $cur_update_fid;
                    $this->db->table($this->T)->where("`{$using_where_key}`",$cur_fid[$using_where_key])->update();
                }catch(\Exception $e){
                    Log::e($e->getMessage());
                }
            }
            $this->db->commit();

            # change ano -> cur
            $cur_root_fid = $cur_fids[0][$this->column_name];
            $cur_depth = $this->getDepthCount($cur_root_fid);
            // Log::d('cur', $cur_root_fid, $cur_depth);
            $cur_parent_fid = ($cur_depth<1) ? (explode('.',$cur_root_fid))[0]."." : $cur_root_fid;

            # db update
            $this->db->autocommit(FALSE);
            foreach($ano_fids as $ano_fid)
            {
                $this_fid = $ano_fid[$this->column_name];
                $ano_depth = $this->getDepthCount($this_fid);
                $ano_update_fid = ($cur_depth ==$ano_depth) ? $cur_parent_fid: sprintf("%s%s",$cur_parent_fid,substr($this_fid,($ano_depth-$cur_depth)*-2));
                // Log::d('ano -> cur', $this_fid,'->',$ano_update_fid);
                $result[] = sprintf("ano => cur : %s -> %s", $this_fid,$ano_update_fid);
                try{
                    $this->db[$this->column_name] = $ano_update_fid;
                    $this->db->table($this->T)->where("`{$using_where_key}`",$ano_fid[$using_where_key])->update();
                }catch(\Exception $e){
                    Log::e($e->getMessage());
                }
            }
            $this->db->commit();
        }

    return $result;
    }
}
?>