<?php
function sns_time_format(array $snsf, string $time){
	return match($snsf[1]) {
		'second','seconds' => sprintf("%d초전",$snsf[0]),
		'minute','minutes' => sprintf("약%d분전",$snsf[0]),
		'hour','hours'     => sprintf("약%d시간전",$snsf[0]),
		'day','days'       => sprintf("약%d일전",$snsf[0]),
		// 'month','months'   => sprintf("약%d 개월전",$snsf[0]),
		default            => $time ?? ''
	};
}
?>