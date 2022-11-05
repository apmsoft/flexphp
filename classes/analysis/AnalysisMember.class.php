<?php
namespace Flex\Analysis;

use Flex\R\R;
use Flex\Db\DbMySqli;

class AnalysisMember
{
    private $db;
    public function __construct(){
        $this->db = new \Flex\Db\DbMySqli();
    }

    public function analysis ()
    {
        #chartjs 셋팅 
        $background_color = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 234, 200, 0.2)',
            'rgba(255, 102, 23, 0.2)'
        ];

        $border_color = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(255, 102, 23, 1)'
        ];
        $labels1 = [];
        $ch1_data = [];

        # 회원통계 /======================
        # 회원가입 시작[년] - 종료[년] 구하기
        $member_statis_groups = [];
        $mem_start_end_y = $this->db->get_record("min(signdate) as sd, max(signdate) as ed", R::$tables['member'], "");
        #out_r($mem_start_end_y);
        $mem_start_y = date('Y',$mem_start_end_y['sd']);
        $mem_end_y = date('Y',$mem_start_end_y['ed']);
        #out_ln(sprintf("sd : %s - ed : %s", $mem_start_y, $mem_end_y));
        $nn=0;
        for($gi=$mem_start_y; $gi<=$mem_end_y; $gi++)
        {
            # set chart
            $ch1_data[] = [
                'label' => $gi,
                'fill'  => false,
                'borderColor'  => $border_color[$nn], // The main line color
                'backgroundColor'  => $background_color[$nn],
                'data'  => [0,0,0,0,0,0,0,0,0,0,0,0]
            ];
            $nn++;
        }
        #out_r($member_statis_groups);

        # 날짜 [년][월] 별 총 가입자 수 구하기/======================
        $nx = 0;
        $member_total = 0;
        $member_statics_str = '';
        $member_statics_args = [];
        $mem_sta_qry = sprintf("SELECT FROM_UNIXTIME(signdate, '%%Y-%%m') sd,count(*) as c FROM `%s` GROUP BY sd", R::$tables['member']);
        $mem_sta_rlt = $this->db->query($mem_sta_qry);
        $pre_year = $ch1_data[0]['label'];
        while($mem_sta_row= $mem_sta_rlt->fetch_assoc())
        {
            $sta_arg = explode('-',$mem_sta_row['sd']);
            $sta_y = $sta_arg[0];
            $sta_m = (int)$sta_arg[1] - 1;
            
            # 년-월
            if(!isset($member_statics_args[$sta_y])){
                $member_statics_args[$sta_y] = [0,0,0,0,0,0,0,0,0,0,0,0,0];
                
                // set chart
                $ch1_data[$nx]['data'] = [0,0,0,0,0,0,0,0,0,0,0,0];
            }
            $member_statics_args[$sta_y][$sta_m] = (int)$mem_sta_row['c'];
            $member_statics_args[$sta_y][12] += (int)$mem_sta_row['c'];
            
            # set chart
            $ch1_data[$nx]['data'][$sta_m] = (int)$mem_sta_row['c'];

            # total member
            $member_total+= $mem_sta_row['c'];

            if($sta_y != $pre_year){
                $pre_year = $sta_y;
                $nx++;
            }
        }

        # 신규가입자 /=====================
        $member_new = [];
        $mem_new_qry = sprintf("SELECT id,signdate,`name`,userid,extract_id FROM `%s` ORDER BY id DESC LIMIT 6", R::$tables['member']);
        $mem_new_rlt = $this->db->query($mem_new_qry);
        while($mem_new_row= $mem_new_rlt->fetch_assoc())
        {
            # image
            $mem_new_photo = '';
            $files = [];
            // $utilFileQuery = new \Flex\Util\UtilFileQuery($mem_new_row['extract_id']);
            // $files = $utilFileQuery->fetch(R::$tables['member_upfiles'], '`id` ASC', 'LIMIT 1');
            // if(isset($files[0])){
            //     $file_info =& $files[0];
            //     $mem_new_photo = $file_info['imagedata'];
            // }

            // set array
            $member_new[] = [
                'id'       => (int)$mem_new_row['id'],
                'name'     => $mem_new_row['name'],
                'userid'   => $mem_new_row['userid'],
                'signdate' => date('Y/m/d',$mem_new_row['signdate']),
                'photo'    => $mem_new_photo
            ];
        }

        # 최근접속자 /=====================
        $member_recent = [];
        $mem_recent_qry = sprintf("SELECT id,recently_connect_date,`name`,userid,extract_id FROM `%s` WHERE recently_connect_date>0 ORDER BY recently_connect_date DESC LIMIT 6", R::$tables['member']);
        $mem_recent_rlt = $this->db->query($mem_recent_qry);
        while($mem_recent_row = $mem_recent_rlt->fetch_assoc())
        {
            # image
            $mem_recent_photo = '';
            $files = [];
            // $utilFileQuery = new \Flex\Util\UtilFileQuery($mem_recent_row['extract_id']);
            // $files = $utilFileQuery->fetch(R::$tables['member_upfiles'], '`id` ASC', 'LIMIT 1');
            // if(isset($files[0])){
            //     $file_info =& $files[0];
            //     $mem_recent_photo = $file_info['imagedata'];
            // }

            // set array
            $member_recent[] = [
                'id'       => (int)$mem_recent_row['id'],
                'name'     => $mem_recent_row['name'],
                'userid'   => $mem_recent_row['userid'],
                'signdate' => date('Y/m/d',$mem_recent_row['recently_connect_date']),
                'photo'    => $mem_recent_photo
            ];
        }

        return [
            'result'       => 'true',
            'total_record' => $member_total,
            'member_recent'=> $member_recent,
            'member_new'   => $member_new,
            'ch1'          => [
                'labels' => [1,2,3,4,5,6,7,8,9,10,11,12],
                'datasets' => $ch1_data
            ],
            'msg'          => $member_statics_args
        ];
    }
}
?>
