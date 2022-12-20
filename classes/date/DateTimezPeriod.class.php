<?php
namespace Flex\Date;

use Flex\Date\DateTimez;
use Flex\Log\Log;
use \DateTimeImmutable;
use \ErrorException;

class DateTimezPeriod
{
    # Asia/Seoul
    public string $timezone = '';

	public function __construct(string $timezone='Asia/Seoul')
	{
		$this->timezone = $timezone;
	}

    /**
     * 특정 날짜와 타켓 날짜사이 시간차
     */
    public function diff(string|int $start_date, string|int $end_date, array $formatter = ["format"=>'days','nf'=>'2']) : mixed 
    {
        $s = new DateTimeImmutable($start_date);
        $e = new DateTimeImmutable($end_date);
        $interval = $s->diff($e);

        # 소수점 자리수
        $nf = (isset($formatter['nf'])) ? sprintf("%%0.%df", $formatter['nf']) : "%0.2f";
        
        # 월하는 데이터 형
        $result = match($formatter['format']) {
            'days','day' => sprintf($nf, $interval->days),
            'seconds'    => $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s,
            'minutes'    => sprintf($nf, ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s) / 60),
            'hours'      => sprintf($nf, ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s) / 3600),
            'minutes:seconds','i:s' => sprintf("%02d:%02d",( ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60) / 60),$interval->s),
            'hours:minutes:seconds','h:i:s' => sprintf("%02d:%02d:%02d",( ($interval->days * 86400 + $interval->h * 3600) / 3600), ($interval->i * 60 / 60),$interval->s),
            'months'      => sprintf($nf,($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s) / 86400),
            'months:days:hours:minutes:seconds','m-d h:i:s' => sprintf("%02d-%02d %02d:%02d:%02d",$interval->m,$interval->d,$interval->h,$interval->i,$interval->s)
        };

    return $result;
    }
}
?>
