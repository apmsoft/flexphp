<?php
namespace Flex\Date;

use Flex\Date\DateTimez;
use \ErrorException;
use \DateTimeImmutable;
use Flex\Log\Log;

# purpose : 날짜/시간에 필요한 것들을 다룬다
class DateTimezPeriod
{
    # Asia/Seoul
    public string $timezone = '';

    private $format_styles = [
        'days' => "%a days",
        'day'  => "%a days",
        'h:i:s'=> '%H:%I:%S',
        'hour'=> '%H Hours',
        'm:s'=> '%i Minute %s Seconds',
        's'=> '%s Seconds'
    ];

	public function __construct(string $timezone='Asia/Seoul')
	{
		$this->timezone = $timezone;
	}

    /**
     * 
     */
    public function getDatePeriod(string|int $start_date, string|int $end_date, array $style = ["format"=>'days']) : mixed 
    {
        $s = new DateTimeImmutable($start_date);
        $e = new DateTimeImmutable($end_date);
        $interval = $s->diff($e);
        $format = $style['format'];
        

    return $interval->format($format);
    }

	# 프라퍼티 값 가져오기
	public function __get($propertyname) : mixed{
		return $this->{$propertyname};
	}
}
?>
