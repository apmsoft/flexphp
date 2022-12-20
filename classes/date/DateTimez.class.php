<?php
namespace Flex\Date;

use \DateTime;
use \DateTimeZone;
use \DateInterval;
use \ErrorException;
use Flex\Log\Log;

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

	public function chkTimestamp(string|int $times) : string 
	{
		# datetime
		$_times = $times;
		if(is_int($times)){
			$_times = '@'.$times;
		}
	return $_times;
	}

	public function formatter(string $formatter) : DateTimez
    {
		if(strpos($formatter,'-P') !==false){
			parent::sub(new DateInterval( str_replace('-','',$formatter) ));
		}else if(substr($formatter,0,1) == 'P'){
			parent::add(new DateInterval($formatter));
		}else{
			parent::modify($formatter);
		}
	return $this;
	}
}
?>
