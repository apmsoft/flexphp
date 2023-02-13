<?php
function sns_time_format(array $snsf, string $time) : string{
	return match($snsf[1]) {
		'second','seconds' => sprintf("%d초전",$snsf[0]),
		'minute','minutes' => sprintf("약%d분전",$snsf[0]),
		'hour','hours'     => sprintf("약%d시간전",$snsf[0]),
		'day','days'       => sprintf("약%d일전",$snsf[0]),
		// 'month','months'   => sprintf("약%d 개월전",$snsf[0]),
		default            => $time ?? ''
	};
}

# microtime 2 datetime
function convert2MTDT(int|float $duration) : string{
	$result = '';
	$hours = (int)($duration/60/60);
	$minutes = (int)($duration/60)-$hours*60;
	$seconds = (int)$duration-$hours*60*60-$minutes*60;

return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
?>