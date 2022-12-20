<?php
namespace Flex\Date;

use \DateTime;
use \DateTimeZone;
use \ErrorException;
use Flex\Log\Log;

# Parent Class : DateTime::';
# purpose : 날짜/시간에 필요한 것들을 다룬다
class DateTimez extends DateTime
{
	public DateTimeZone $dateTimeZone;
	public $timezone;
	public $location;

	# time() || now || today || yesterday , Asia/Seoul
	public function __construct(string|int $times="now", string $timezone='Asia/Seoul')
	{
		# timezone
		$this->dateTimeZone = new \DateTimeZone($timezone);
		$this->timezone = $this->dateTimeZone->getName();
		$this->location = $this->dateTimeZone->getLocation();

		# datetime
		parent::__construct(self::chkTimestamp($times), $this->dateTimeZone);
	}

	// public function 

	public function chkTimestamp(string|int $times) : string 
	{
		# datetime
		$_times = $times;
		if(is_int($times)){
			$_times = '@'.$times;
		}
	return $_times;
	}
}
?>
