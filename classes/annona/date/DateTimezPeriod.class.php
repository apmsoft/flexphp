<?php
namespace Flex\Annona\Date;

use Flex\Annona\Date\DateTimez;
use \DateTimeImmutable;
use \DateInterval;
use \DatePeriod;
use \ErrorException;

class DateTimezPeriod
{
    const VERSEION = '1.2';
    # Asia/Seoul
    public string $timezone = '';
    private array $relative_pos = [
        'year','month','day','hour','minute','second'
    ];

	public function __construct(string $timezone='')
	{
        # timezone
		if(!$timezone && function_exists('date_default_timezone_get')){
			$timezone = date_default_timezone_get();
		}
		$this->timezone = $timezone ?? 'Asia/Seoul';
	}

    /**
     * 특정 날짜와 타켓 날짜사이 시간차
     * format : 시간차 포멧
     * demical : 소수점 자리
     */
    public function diff(string $start_date, string $end_date, array $formatter = ["format"=>'default','demical'=>'2']) : mixed 
    {
        $s = new DateTimeImmutable($start_date);
        $e = new DateTimeImmutable($end_date);
        $interval = $s->diff($e);

        # 월하는 데이터 형
        $result = match($formatter['format']) {
            'days','day' => $interval->days,
            'seconds'    => $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s,
            'minutes'    => ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s) / 60,
            'hours'      => ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s) / 3600,
            'minutes:seconds','i:s' => sprintf("%02d:%02d",( ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60) / 60),$interval->s),
            'hours:minutes:seconds','h:i:s' => sprintf("%02d:%02d:%02d",( ($interval->days * 86400 + $interval->h * 3600) / 3600), ($interval->i * 60 / 60),$interval->s),
            'months'      => (($interval->m /12) + ($interval->days / 30)),
            'months:days:hours:minutes:seconds','m-d h:i:s' => sprintf("%02d-%02d %02d:%02d:%02d",$interval->m,$interval->d,$interval->h,$interval->i,$interval->s),
            'top' => $interval->format("%y-%m-%d %h:%i:%s"),
            default => $interval->format("%Y-%M-%D %H:%I:%S")
        };

        # 소수점 자리 및 버리기
        if(isset($formatter['demical'])){
            if( in_array($formatter['format'], ['minutes','hours','months'])){
                if((int)$formatter['demical'] > 0){
                    $_f = (int)"1".str_repeat("0", $formatter['demical']);
                    $result = floor($result * $_f)/$_f;
                }else {
                    $result = floor($result);
                }
            }
        }
        

        # 시간이 큰것만 우선 순위 출력 약 시간 표시용
        # 약 1분전, 약 1시간전, 약10일전
        if($formatter['format'] == 'top')
        {
            $relative_timef = self::aboutTopTime ($result);
            if($relative_timef){
                $result = $relative_timef;
            }
        }

    return $result;
    }

    # 날짜와 날짜 사이 날짜
    /**
     * interval : 1  // 날짜(1일, 3일)간격
     * days :  30 // 며칠(30일/개)
     */
    public function period(string $start_date, int $interval, int $days) : array 
    {
        $result = [];

        $format = sprintf("P%dD", $interval);
        $startDateTimez = new DateTimez($start_date, $this->timezone);
        $interval  = new DateInterval($format);
        $period    = new DatePeriod($startDateTimez, $interval, $days);
        foreach($period as $dateTimez){
            $result[] = $dateTimez->format('Y-m-d');
        }

    return $result;
    }

    # 최상위 순서대로만 표시 y > m > d > h > i > s
    private function aboutTopTime (string $relative) : string 
    {
        $result = '';
        $argv = explode('-',strtr($relative,[':'=>'-',' '=>'-']));
        foreach($argv as $idx => $v){
            if($v > 0){
                $formatter = ($v>1) ? $this->relative_pos[$idx].'s' : $this->relative_pos[$idx];
                $result = sprintf("%d %s",$v,$formatter);
                break;
            }
        }

    return $result;
    }
}
?>
